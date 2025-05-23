<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\CoreVisualizations\Visualizations\JqplotGraph;

use Piwik\Plugins\CoreVisualizations\JqplotDataGenerator;
use Piwik\Plugins\CoreVisualizations\Visualizations\JqplotGraph;
/**
 * Visualization that renders HTML for a Bar graph using jqPlot.
 */
class Bar extends JqplotGraph
{
    public const ID = 'graphVerticalBar';
    public const FOOTER_ICON = 'icon-chart-bar';
    public const FOOTER_ICON_TITLE = 'General_VBarGraph';
    public function beforeLoadDataTable()
    {
        parent::beforeLoadDataTable();
        $this->checkRequestIsNotForMultiplePeriods();
        $this->config->datatable_js_type = 'JqplotBarGraphDataTable';
    }
    public static function getDefaultConfig()
    {
        $config = new \Piwik\Plugins\CoreVisualizations\Visualizations\JqplotGraph\Config();
        $config->max_graph_elements = 6;
        return $config;
    }
    protected function ensureValidColumnsToDisplay()
    {
        parent::ensureValidColumnsToDisplay();
        $columnsToDisplay = $this->config->columns_to_display;
        // Use a sensible default if the columns_to_display is empty
        $this->config->columns_to_display = $columnsToDisplay ?: array('nb_visits');
    }
    protected function makeDataGenerator($properties)
    {
        return JqplotDataGenerator::factory('bar', $properties, $this);
    }
    public function supportsComparison()
    {
        return \true;
    }
}
