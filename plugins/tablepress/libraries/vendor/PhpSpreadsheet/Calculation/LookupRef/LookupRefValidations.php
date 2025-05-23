<?php

namespace TablePress\PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use TablePress\PhpOffice\PhpSpreadsheet\Calculation\Exception;
use TablePress\PhpOffice\PhpSpreadsheet\Calculation\Information\ErrorValue;
use TablePress\PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class LookupRefValidations
{
	/**
				 * @param mixed $value
				 */
				public static function validateInt($value): int
	{
		if (!is_numeric($value)) {
			if (is_string($value) && ErrorValue::isError($value)) {
				throw new Exception($value);
			}

			throw new Exception(ExcelError::VALUE());
		}

		return (int) floor((float) $value);
	}

	/**
				 * @param mixed $value
				 */
				public static function validatePositiveInt($value, bool $allowZero = true): int
	{
		$value = self::validateInt($value);

		if (($allowZero === false && $value <= 0) || $value < 0) {
			throw new Exception(ExcelError::VALUE());
		}

		return $value;
	}
}
