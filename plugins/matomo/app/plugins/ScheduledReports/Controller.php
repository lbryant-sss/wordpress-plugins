<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\ScheduledReports;

use Piwik\Access;
use Piwik\API\Request;
use Piwik\Common;
use Piwik\Nonce;
use Piwik\Period\PeriodValidator;
use Piwik\Piwik;
use Piwik\Plugins\ImageGraph\ImageGraph;
use Piwik\Plugins\LanguagesManager\LanguagesManager;
use Piwik\Plugins\SegmentEditor\SegmentEditor;
use Piwik\Plugins\SitesManager\API as APISitesManager;
use Piwik\View;
/**
 *
 */
class Controller extends \Piwik\Plugin\Controller
{
    public const DEFAULT_REPORT_TYPE = \Piwik\Plugins\ScheduledReports\ScheduledReports::EMAIL_TYPE;
    public function index()
    {
        $view = new View('@ScheduledReports/index');
        $this->setGeneralVariablesView($view);
        $view->countWebsites = count(APISitesManager::getInstance()->getSitesIdWithAtLeastViewAccess());
        // get report types
        $reportTypes = \Piwik\Plugins\ScheduledReports\API::getReportTypes();
        $reportTypeOptions = array();
        foreach ($reportTypes as $reportType => $icon) {
            $reportTypeOptions[$reportType] = mb_strtoupper($reportType);
        }
        $view->reportTypes = $reportTypes;
        $view->reportTypeOptions = $reportTypeOptions;
        $view->defaultReportType = self::DEFAULT_REPORT_TYPE;
        $view->defaultReportFormat = \Piwik\Plugins\ScheduledReports\ScheduledReports::DEFAULT_REPORT_FORMAT;
        $view->defaultEvolutionPeriodN = ImageGraph::getDefaultGraphEvolutionLastPeriods();
        $view->displayFormats = \Piwik\Plugins\ScheduledReports\ScheduledReports::getDisplayFormats();
        $view->paramPeriods = [];
        $periodValidator = new PeriodValidator();
        $allowedPeriods = $periodValidator->getPeriodsAllowedForAPI();
        foreach ($allowedPeriods as $label) {
            if ($label === 'range') {
                continue;
            }
            $view->paramPeriods[$label] = Piwik::translate('Intl_Period' . ucfirst($label));
        }
        $reportsByCategoryByType = array();
        $reportFormatsByReportTypeOptions = array();
        $reportFormatsByReportType = array();
        $allowMultipleReportsByReportType = array();
        foreach ($reportTypes as $reportType => $reportTypeIcon) {
            // get report formats
            $reportFormatsByReportType[$reportType] = \Piwik\Plugins\ScheduledReports\API::getReportFormats($reportType);
            $reportFormatsByReportTypeOptions[$reportType] = $reportFormatsByReportType[$reportType];
            foreach ($reportFormatsByReportTypeOptions[$reportType] as $type => $icon) {
                $reportFormatsByReportTypeOptions[$reportType][$type] = mb_strtoupper($type);
            }
            $allowMultipleReportsByReportType[$reportType] = \Piwik\Plugins\ScheduledReports\API::allowMultipleReports($reportType);
            // get report metadata
            $reportsByCategory = array();
            $availableReportMetadata = \Piwik\Plugins\ScheduledReports\API::getReportMetadata($this->idSite, $reportType);
            foreach ($availableReportMetadata as $reportMetadata) {
                $reportsByCategory[$reportMetadata['category']][] = $reportMetadata;
            }
            $reportsByCategoryByType[$reportType] = $reportsByCategory;
        }
        $view->reportsByCategoryByReportType = $reportsByCategoryByType;
        $view->reportFormatsByReportType = $reportFormatsByReportType;
        $view->reportFormatsByReportTypeOptions = $reportFormatsByReportTypeOptions;
        $view->allowMultipleReportsByReportType = $allowMultipleReportsByReportType;
        $reports = array();
        $reportsById = array();
        if (!Piwik::isUserIsAnonymous()) {
            $reports = Request::processRequest('ScheduledReports.getReports', array('idSite' => $this->idSite, 'ifSuperUserReturnOnlySuperUserReports' => \true, 'filter_limit' => -1), []);
            foreach ($reports as &$report) {
                $report['evolutionPeriodFor'] = $report['evolution_graph_within_period'] ? 'each' : 'prev';
                $report['evolutionPeriodN'] = (int) $report['evolution_graph_period_n'] ?: ImageGraph::getDefaultGraphEvolutionLastPeriods();
                $report['periodParam'] = $report['period_param'];
                $report['recipients'] = \Piwik\Plugins\ScheduledReports\API::getReportRecipients($report);
                $reportsById[$report['idreport']] = $report;
            }
        }
        $view->reports = $reports;
        $view->reportsJSON = json_encode($reportsById);
        $view->downloadOutputType = \Piwik\Plugins\ScheduledReports\API::OUTPUT_INLINE;
        $view->periods = \Piwik\Plugins\ScheduledReports\ScheduledReports::getPeriodToFrequency();
        $view->defaultPeriod = \Piwik\Plugins\ScheduledReports\ScheduledReports::DEFAULT_PERIOD;
        $view->defaultHour = \Piwik\Plugins\ScheduledReports\ScheduledReports::DEFAULT_HOUR;
        $view->periodTranslations = \Piwik\Plugins\ScheduledReports\ScheduledReports::getPeriodFrequencyTranslations();
        $view->language = LanguagesManager::getLanguageCodeForCurrentUser();
        $view->segmentEditorActivated = \false;
        if (\Piwik\Plugins\ScheduledReports\API::isSegmentEditorActivated()) {
            $savedSegmentsById = array('' => Piwik::translate('SegmentEditor_DefaultAllVisits'));
            $allSegments = SegmentEditor::getAllSegmentsForSite($this->idSite);
            foreach ($allSegments as $savedSegment) {
                $savedSegmentsById[$savedSegment['idsegment']] = Common::unsanitizeInputValue($savedSegment['name']);
            }
            $view->savedSegmentsById = $savedSegmentsById;
            $view->segmentEditorActivated = \true;
        }
        return $view->render();
    }
    public function unsubscribe()
    {
        $view = new View('@ScheduledReports/unsubscribe');
        $this->setBasicVariablesView($view);
        $view->linkTitle = Piwik::getRandomTitle();
        $token = Common::getRequestVar('token', '', 'string');
        if (empty($token)) {
            $view->error = Piwik::translate('ScheduledReports_NoTokenProvided');
            return $view->render();
        }
        $subscriptionModel = new \Piwik\Plugins\ScheduledReports\SubscriptionModel();
        $subscription = $subscriptionModel->getSubscription($token);
        if (empty($subscription)) {
            $view->error = Piwik::translate('ScheduledReports_NoSubscriptionFound');
            return $view->render();
        }
        /*
         * Executed as super user, as we need to fetch a scheduled report, without the current user being authenticated.
         */
        $report = Access::doAsSuperUser(function () use($subscription) {
            $reports = Request::processRequest('ScheduledReports.getReports', ['idReport' => $subscription['idreport']]);
            return reset($reports);
        });
        $confirm = Common::getRequestVar('confirm', '', 'string');
        $view->reportName = $report['description'];
        $nonce = Common::getRequestVar('nonce', '', 'string');
        if (!empty($confirm) && Nonce::verifyNonce('Report.Unsubscribe', $nonce)) {
            Nonce::discardNonce('Report.Unsubscribe');
            $subscriptionModel->unsubscribe($token);
            $view->success = \true;
        } else {
            $view->nonce = Nonce::getNonce('Report.Unsubscribe');
        }
        return $view->render();
    }
}
