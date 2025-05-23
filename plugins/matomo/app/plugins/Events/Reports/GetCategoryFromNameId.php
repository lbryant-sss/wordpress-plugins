<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Events\Reports;

use Piwik\Piwik;
use Piwik\Plugins\Events\Columns\EventCategory;
/**
 * Report metadata class for the Events.getCategoryFromNameId class.
 */
class GetCategoryFromNameId extends \Piwik\Plugins\Events\Reports\Base
{
    protected function init()
    {
        parent::init();
        $this->dimension = new EventCategory();
        $this->name = Piwik::translate('Events_EventCategories');
        $this->isSubtableReport = \true;
    }
}
