<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\ProfessionalServices\Widgets;

use Piwik\Container\StaticContainer;
use Piwik\Piwik;
use Piwik\View;
use Piwik\Widget\WidgetConfig;
class PromoCustomReports extends \Piwik\Plugins\ProfessionalServices\Widgets\DismissibleWidget
{
    private const PROMO_PLUGIN_NAME = 'CustomReports';
    public static function configure(WidgetConfig $config)
    {
        $config->setCategoryId('ProfessionalServices_PromoCustomReports');
        $config->setSubcategoryId('ProfessionalServices_PromoManage');
        $config->setIsNotWidgetizable();
        $promoWidgetApplicable = StaticContainer::get('Piwik\\Plugins\\ProfessionalServices\\PromoWidgetApplicable');
        $isEnabled = $promoWidgetApplicable->check(self::PROMO_PLUGIN_NAME, self::getDismissibleWidgetName());
        $config->setIsEnabled($isEnabled);
    }
    public function render()
    {
        $marketplacePlugins = StaticContainer::get('Piwik\\Plugins\\Marketplace\\Plugins');
        $pluginInfo = $marketplacePlugins->getPluginInfo(self::PROMO_PLUGIN_NAME);
        $view = new View('@ProfessionalServices/pluginAdvertising');
        $view->plugin = $pluginInfo;
        $view->widgetName = self::getDismissibleWidgetName();
        $view->userCanDismiss = Piwik::isUserIsAnonymous() === \false;
        $view->title = Piwik::translate('ProfessionalServices_PromoUnlockPowerOf', $pluginInfo['displayName']);
        $view->listOfFeatures = [Piwik::translate('ProfessionalServices_CustomReportsFeature01'), Piwik::translate('ProfessionalServices_CustomReportsFeature02'), Piwik::translate('ProfessionalServices_CustomReportsFeature03')];
        return $view->render();
    }
}
