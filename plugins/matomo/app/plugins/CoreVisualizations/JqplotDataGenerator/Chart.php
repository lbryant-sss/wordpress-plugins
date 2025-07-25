<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\CoreVisualizations\JqplotDataGenerator;

use Exception;
use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Log\LoggerInterface;
use Piwik\NumberFormatter;
use Piwik\ProxyHttp;
class Chart
{
    // temporary
    public $properties;
    // the data kept here conforms to the jqplot data layout
    // @see https://www.jqplot.com/docs/files/jqPlotOptions-txt.html
    protected $series = [];
    protected $data = [];
    protected $axes = [];
    /**
     * @var array<string>
     */
    protected $dataStates = [];
    /**
     * @var LoggerInterface
     */
    protected $logger;
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? StaticContainer::get(LoggerInterface::class);
    }
    public function setAxisXLabels($xLabels, $xTicks = null, $index = 0)
    {
        $axisName = $this->getXAxis($index);
        $xSteps = $this->properties['x_axis_step_size'];
        $showAllTicks = $this->properties['show_all_ticks'];
        $this->axes[$axisName]['labels'] = array_values($xLabels);
        $ticks = array_values($xTicks ?: $xLabels);
        if (!$showAllTicks) {
            // unset labels so there are $xSteps number of blank ticks between labels
            foreach ($ticks as $i => &$label) {
                if ($i % $xSteps != 0) {
                    $label = ' ';
                }
            }
        }
        $this->axes[$axisName]['ticks'] = $ticks;
    }
    public function setAxisXOnClick(&$onClick)
    {
        $this->axes['xaxis']['onclick'] =& $onClick;
    }
    /**
     * Set the series values
     *
     * @param            $values
     * @param null       $seriesMetadata
     * @param array|null $seriesUnits     If the series units array is passed then the values will be formatted
     */
    public function setAxisYValues(&$values, $seriesMetadata = null, ?array $seriesUnits = null)
    {
        foreach ($values as $label => &$data) {
            $seriesInfo = array('label' => $label, 'internalLabel' => $label);
            if (isset($seriesMetadata[$label])) {
                $seriesInfo = array_merge($seriesInfo, $seriesMetadata[$label]);
            }
            $this->series[] = $seriesInfo;
            $unit = isset($seriesUnits[$label]) ? $seriesUnits[$label] : null;
            array_walk($data, function (&$v) use($unit) {
                $v = (float) Common::forceDotAsSeparatorForDecimalPoint($v);
                if ($unit === '%') {
                    $v = $v * 100;
                }
            });
            $this->data[] =& $data;
        }
    }
    public function setAxisYUnits($yUnits)
    {
        $yUnits = array_values(array_map('strval', $yUnits));
        // generate axis IDs for each unique y unit
        $axesIds = array();
        foreach ($yUnits as $idx => $unit) {
            if (!isset($axesIds[$unit])) {
                // handle axes ids: first y[]axis, then y[2]axis, y[3]axis...
                $nextAxisId = empty($axesIds) ? '' : count($axesIds) + 1;
                $axesIds[$unit] = 'y' . $nextAxisId . 'axis';
            }
        }
        // generate jqplot axes config
        foreach ($axesIds as $unit => $axisId) {
            if ($unit === '$' || $unit === '£') {
                $this->axes[$axisId]['tickOptions']['formatString'] = $unit . '%s';
            } else {
                $this->axes[$axisId]['tickOptions']['formatString'] = '%s' . $unit;
            }
        }
        $currencies = StaticContainer::get('Piwik\\Intl\\Data\\Provider\\CurrencyDataProvider')->getCurrencyList();
        $currencies = array_column($currencies, 0);
        // generate jqplot axes config
        foreach ($axesIds as $unit => $axisId) {
            if ($unit === '%') {
                $this->axes[$axisId]['tickOptions']['formatString'] = str_replace('0', '%s', NumberFormatter::getInstance()->formatPercent(0, 0, 0));
            } elseif (in_array($unit, $currencies)) {
                $this->axes[$axisId]['tickOptions']['formatString'] = str_replace('0', '%s', NumberFormatter::getInstance()->formatCurrency(0, $unit, 0));
            } else {
                $this->axes[$axisId]['tickOptions']['formatString'] = '%s' . $unit;
            }
        }
        // map each series to appropriate yaxis
        foreach ($yUnits as $idx => $unit) {
            $this->series[$idx]['yaxis'] = $axesIds[$unit];
        }
    }
    public function setAxisYLabels($labels)
    {
        foreach ($this->series as &$series) {
            $label = $series['internalLabel'];
            if (isset($labels[$label])) {
                $series['label'] = $labels[$label];
            }
        }
    }
    public function render()
    {
        ProxyHttp::overrideCacheControlHeaders();
        $this->checkDataStateAvailableForAllTicks();
        // See https://www.jqplot.com/docs/files/jqPlotOptions-txt.html
        $data = ['params' => ['axes' => &$this->axes, 'series' => &$this->series], 'data' => &$this->data, 'dataStates' => &$this->dataStates];
        return $data;
    }
    public function setAxisXLabelsMultiple($xLabels, $seriesToXAxis, $ticks = null)
    {
        foreach ($xLabels as $index => $labels) {
            $this->setAxisXLabels($labels, $ticks === null ? null : $ticks[$index], $index);
        }
        foreach ($seriesToXAxis as $seriesIndex => $xAxisIndex) {
            $axisName = $this->getXAxis($xAxisIndex);
            // don't actually set xaxis otherwise jqplot will show too many axes. however, we need the xaxis labels, so we add them
            // to the jqplot config
            $this->series[$seriesIndex]['_xaxis'] = $axisName;
        }
    }
    /**
     * Set state information ("complete", "incomplete", ...) for the data points.
     *
     * Each entry is associated with all values of all series having the same index (stored in $data).
     *
     * @param array<string> $dataStates
     */
    public function setDataStates(array $dataStates) : void
    {
        $this->dataStates = $dataStates;
    }
    private function getXAxis($index)
    {
        $axisName = 'xaxis';
        if ($index != 0) {
            $axisName = 'x' . ($index + 1) . 'axis';
        }
        return $axisName;
    }
    private function checkDataStateAvailableForAllTicks() : void
    {
        if ([] === $this->dataStates) {
            return;
        }
        $maxTickCount = 0;
        $stateCount = count($this->dataStates);
        if ([] !== $this->data) {
            $dataCounts = array_map('count', $this->data);
            $uniqueCounts = array_unique($dataCounts);
            $maxTickCount = max($uniqueCounts);
        }
        if ($stateCount === $maxTickCount) {
            return;
        }
        $ex = new Exception(sprintf('Data state information does not span graph length (%u ticks, %u states)', $maxTickCount, $stateCount));
        $this->logger->info('{exception}', ['exception' => $ex]);
    }
}
