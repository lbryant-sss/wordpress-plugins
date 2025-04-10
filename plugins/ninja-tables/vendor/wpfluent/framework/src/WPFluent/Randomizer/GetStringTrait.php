<?php

namespace NinjaTables\Framework\Randomizer;

use InvalidArgumentException;

trait GetStringTrait
{
	public function getString(
        int $length = 16, 
        string $charlist = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_=+[]{}|;:,.<>?/~'
    ) {
        if ($length < 1) {
            throw new InvalidArgumentException(
                'Length must be greater than zero.'
            );
        } elseif (strlen($charlist) < 2) {
            throw new InvalidArgumentException(
                'Character list must contain at least two chars.'
            );
        }
        return $this->getBytesFromString($charlist, $length);
    }
}
