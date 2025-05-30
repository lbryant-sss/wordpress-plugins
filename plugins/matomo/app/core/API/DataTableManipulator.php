<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\API;

use Exception;
use Piwik\Archive\DataTableFactory;
use Piwik\Container\StaticContainer;
use Piwik\DataTable\Row;
use Piwik\DataTable;
use Piwik\Period\Range;
use Piwik\Plugins\API\API;
use Piwik\Url;
/**
 * Base class for manipulating data tables.
 * It provides generic mechanisms like iteration and loading subtables.
 *
 * The manipulators are used in ResponseBuilder and are triggered by
 * API parameters. They are not filters because they don't work on the pre-
 * fetched nested data tables. Instead, they load subtables using this base
 * class. This way, they can only load the tables they really need instead
 * of using expanded=1. Another difference between manipulators and filters
 * is that filters keep the overall structure of the table intact while
 * manipulators can change the entire thing.
 */
abstract class DataTableManipulator
{
    protected $apiModule;
    protected $apiMethod;
    protected $request;
    protected $apiMethodForSubtable;
    /**
     * Constructor
     *
     * @param bool $apiModule
     * @param bool $apiMethod
     * @param array $request
     */
    public function __construct($apiModule = \false, $apiMethod = \false, $request = array())
    {
        $this->apiModule = $apiModule;
        $this->apiMethod = $apiMethod;
        $this->request = $request;
    }
    /**
     * This method can be used by subclasses to iterate over data tables that might be
     * data table maps. It calls back the template method self::doManipulate for each table.
     * This way, data table arrays can be handled in a transparent fashion.
     *
     * @param DataTable\Map|DataTable $dataTable
     * @throws Exception
     * @return DataTable\Map|DataTable
     */
    protected function manipulate($dataTable)
    {
        if ($dataTable instanceof DataTable\Map) {
            return $this->manipulateDataTableMap($dataTable);
        } elseif ($dataTable instanceof DataTable) {
            return $this->manipulateDataTable($dataTable);
        } else {
            return $dataTable;
        }
    }
    /**
     * Manipulates child DataTables of a DataTable\Map. See @manipulate for more info.
     *
     * @param DataTable\Map $dataTable
     * @return DataTable\Map
     */
    protected function manipulateDataTableMap($dataTable)
    {
        $result = $dataTable->getEmptyClone();
        foreach ($dataTable->getDataTables() as $tableLabel => $childTable) {
            $newTable = $this->manipulate($childTable);
            $result->addTable($newTable, $tableLabel);
        }
        return $result;
    }
    /**
     * Manipulates a single DataTable instance. Derived classes must define
     * this function.
     */
    protected abstract function manipulateDataTable($dataTable);
    /**
     * Load the subtable for a row.
     * Returns null if none is found.
     *
     * @param DataTable $dataTable
     * @param Row $row
     *
     * @return DataTable
     */
    protected function loadSubtable($dataTable, $row)
    {
        if (!($this->apiModule && $this->apiMethod && count($this->request))) {
            return null;
        }
        $request = $this->request;
        $idSubTable = $row->getIdSubDataTable();
        if ($idSubTable === null) {
            return null;
        }
        $request['idSubtable'] = $idSubTable;
        if ($dataTable) {
            $period = $dataTable->getMetadata(DataTableFactory::TABLE_METADATA_PERIOD_INDEX);
            if ($period instanceof Range) {
                $request['date'] = $period->getDateStart() . ',' . $period->getDateEnd();
            } else {
                $request['date'] = $period->getDateStart()->toString();
            }
        }
        $method = $this->getApiMethodForSubtable($request);
        return $this->callApiAndReturnDataTable($this->apiModule, $method, $request);
    }
    /**
     * In this method, subclasses can clean up the request array for loading subtables
     * in order to make ResponseBuilder behave correctly (e.g. not trigger the
     * manipulator again).
     *
     * @param $request
     * @return
     */
    protected abstract function manipulateSubtableRequest($request);
    /**
     * Extract the API method for loading subtables from the meta data
     *
     * @throws Exception
     * @return string
     */
    protected function getApiMethodForSubtable($request)
    {
        if (!$this->apiMethodForSubtable) {
            if (!empty($request['idSite'])) {
                $idSite = $request['idSite'];
            } else {
                $idSite = 'all';
            }
            $apiParameters = array();
            $entityNames = StaticContainer::get('entities.idNames');
            foreach ($entityNames as $idName) {
                if (!empty($request[$idName])) {
                    $apiParameters[$idName] = $request[$idName];
                }
            }
            $meta = API::getInstance()->getMetadata($idSite, $this->apiModule, $this->apiMethod, $apiParameters);
            if (empty($meta) && array_key_exists('idGoal', $apiParameters)) {
                unset($apiParameters['idGoal']);
                $meta = API::getInstance()->getMetadata($idSite, $this->apiModule, $this->apiMethod, $apiParameters);
            }
            if (empty($meta)) {
                throw new Exception(sprintf("The DataTable cannot be manipulated: Metadata for report %s.%s could not be found. You can define the metadata in a hook, see example at: %s", $this->apiModule, $this->apiMethod, Url::addCampaignParametersToMatomoLink('https://developer.matomo.org/api-reference/events#apigetreportmetadata')));
            }
            if (isset($meta[0]['actionToLoadSubTables'])) {
                $this->apiMethodForSubtable = $meta[0]['actionToLoadSubTables'];
            } else {
                $this->apiMethodForSubtable = $this->apiMethod;
            }
        }
        return $this->apiMethodForSubtable;
    }
    protected function callApiAndReturnDataTable($apiModule, $method, $request)
    {
        $class = \Piwik\API\Request::getClassNameAPI($apiModule);
        $request = $this->manipulateSubtableRequest($request);
        $request['serialize'] = 0;
        $request['expanded'] = 0;
        $request['format'] = 'original';
        $request['format_metrics'] = 0;
        $request['compare'] = 0;
        // don't want to run recursive filters on the subtables as they are loaded,
        // otherwise the result will be empty in places (or everywhere). instead we
        // run it on the flattened table.
        unset($request['filter_pattern_recursive']);
        $dataTable = \Piwik\API\Proxy::getInstance()->call($class, $method, $request);
        $response = new \Piwik\API\ResponseBuilder($format = 'original', $request);
        $response->disableSendHeader();
        $dataTable = $response->getResponse($dataTable, $apiModule, $method);
        // save API method name so it can be used by filters
        if ($dataTable instanceof DataTable\DataTableInterface) {
            $dataTable->filter(function (DataTable $table) use($apiModule, $method) {
                $table->setMetadata('apiModule', $apiModule);
                $table->setMetadata('apiMethod', $method);
            });
        }
        return $dataTable;
    }
}
