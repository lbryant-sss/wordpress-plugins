<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Exception;

/**
 * ErrorException
 *
 */
class ErrorException extends \ErrorException
{
    public function isHtmlMessage()
    {
        return \true;
    }
}
