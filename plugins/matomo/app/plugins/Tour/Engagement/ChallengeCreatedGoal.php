<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Tour\Engagement;

use Piwik\Piwik;
use Piwik\Url;
class ChallengeCreatedGoal extends \Piwik\Plugins\Tour\Engagement\Challenge
{
    public function getName()
    {
        return Piwik::translate('Tour_DefineGoal');
    }
    public function getDescription()
    {
        return Piwik::translate('Tour_DefineGoalDescription');
    }
    public function getId()
    {
        return 'define_goal';
    }
    public function getUrl()
    {
        return 'index.php' . Url::getCurrentQueryStringWithParametersModified(array('module' => 'Goals', 'action' => 'manage', 'widget' => \false));
    }
}
