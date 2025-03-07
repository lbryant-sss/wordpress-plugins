<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Actions;

use Piwik\Metrics as PiwikMetrics;
use Piwik\Piwik;
use Piwik\Tracker\Action;
/**
 * Class encapsulating logic to process Day/Period Archiving for the Actions reports
 *
 */
class Metrics
{
    public static $actionTypes = array(Action::TYPE_PAGE_URL, Action::TYPE_OUTLINK, Action::TYPE_DOWNLOAD, Action::TYPE_PAGE_TITLE, Action::TYPE_SITE_SEARCH);
    public static $columnsToRenameAfterAggregation = array(PiwikMetrics::INDEX_NB_UNIQ_VISITORS => PiwikMetrics::INDEX_SUM_DAILY_NB_UNIQ_VISITORS, PiwikMetrics::INDEX_PAGE_ENTRY_NB_UNIQ_VISITORS => PiwikMetrics::INDEX_PAGE_ENTRY_SUM_DAILY_NB_UNIQ_VISITORS, PiwikMetrics::INDEX_PAGE_EXIT_NB_UNIQ_VISITORS => PiwikMetrics::INDEX_PAGE_EXIT_SUM_DAILY_NB_UNIQ_VISITORS);
    public static $columnsToDeleteAfterAggregation = array(PiwikMetrics::INDEX_NB_UNIQ_VISITORS, PiwikMetrics::INDEX_PAGE_ENTRY_NB_UNIQ_VISITORS, PiwikMetrics::INDEX_PAGE_EXIT_NB_UNIQ_VISITORS);
    public static function getColumnsAggregationOperation()
    {
        $operations = [];
        $actionMetrics = self::getActionMetrics();
        foreach ($actionMetrics as $actionMetric => $definition) {
            if (!empty($definition['aggregation']) && $definition['aggregation'] !== 'sum') {
                $operations[$actionMetric] = $definition['aggregation'];
            }
        }
        return $operations;
    }
    public static function getActionMetrics()
    {
        $metricsConfig = array(PiwikMetrics::INDEX_NB_VISITS => array('aggregation' => 'sum', 'query' => "count(distinct log_link_visit_action.idvisit)"), PiwikMetrics::INDEX_NB_UNIQ_VISITORS => array('aggregation' => \false, 'query' => "count(distinct log_link_visit_action.idvisitor)"), PiwikMetrics::INDEX_PAGE_NB_HITS => array('aggregation' => 'sum', 'query' => "count(*)"), PiwikMetrics::INDEX_PAGE_SUM_TIME_GENERATION => array('aggregation' => 'sum', 'query' => "sum(\n                        case when " . Action::DB_COLUMN_CUSTOM_FLOAT . " is null\n                            then 0\n                            else " . Action::DB_COLUMN_CUSTOM_FLOAT . "\n                        end\n                ) / 1000"), PiwikMetrics::INDEX_PAGE_NB_HITS_WITH_TIME_GENERATION => array('aggregation' => 'sum', 'query' => "sum(\n                    case when " . Action::DB_COLUMN_CUSTOM_FLOAT . " is null\n                        then 0\n                        else 1\n                    end\n                )"), PiwikMetrics::INDEX_PAGE_MIN_TIME_GENERATION => array('aggregation' => 'min', 'query' => "min(" . Action::DB_COLUMN_CUSTOM_FLOAT . ") / 1000"), PiwikMetrics::INDEX_PAGE_MAX_TIME_GENERATION => array('aggregation' => 'max', 'query' => "max(" . Action::DB_COLUMN_CUSTOM_FLOAT . ") / 1000"));
        Piwik::postEvent('Actions.Archiving.addActionMetrics', array(&$metricsConfig));
        return $metricsConfig;
    }
}
