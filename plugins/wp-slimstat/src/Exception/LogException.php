<?php

namespace SlimStat\Exception;

use Exception;
use wp_slimstat as SlimStat;

class LogException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        SlimStat::log($this->generateLogMessage($message, $code), 'error');
    }

    private function generateLogMessage($message, $code)
    {
        return sprintf(
            __('Exception occurred: [Code %d] %s at %s:%d', 'wp-slimstat'),
            $code,
            $message,
            $this->getFile(),
            $this->getLine()
        );
    }
}
