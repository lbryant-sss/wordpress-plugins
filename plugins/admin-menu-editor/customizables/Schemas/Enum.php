<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Schemas;

/**
 * Enum schema
 *
 * Note that if you want to allow NULL, it must be explicitly included as one
 * of the possible values. Only setting the default value to NULL is not enough.
 */
class Enum extends Schema {
	protected $convertEmptyStringsToNull = true;
	protected $enumValues = [];
	protected $valueDetails = [];
	protected $cachedStringConversionSafe = null;
	protected $cachedAllValuesAreStrings = null;

	public function values($values) {
		$this->enumValues = array_values($values);
		$this->cachedStringConversionSafe = null;
		$this->cachedAllValuesAreStrings = null;

		if ( !empty($this->enumValues) && !in_array($this->getDefaultValue(), $this->enumValues) ) {
			$this->defaultValue(reset($this->enumValues));
		}
		return $this;
	}

	public function parse($value, $errors = null, $stopOnFirstError = false) {
		$convertedValue = $this->checkForNull($value, $errors);
		if ( ($convertedValue === null) || is_wp_error($convertedValue) ) {
			return $convertedValue;
		}

		if ( !in_array($value, $this->enumValues) ) {
			return self::addError(
				$errors,
				'invalid_value',
				'Value must be one of: ' . implode(', ', $this->enumValues)
				. '. Received: ' . wp_json_encode($value)
			);
		}

		if ( !$this->isValueEnabled($value) ) {
			return self::addError($errors, 'disabled_value', 'That option is currently not allowed');
		}

		return $value;
	}

	public function isStringConversionSafe() {
		if ( $this->cachedStringConversionSafe === null ) {
			$this->cachedStringConversionSafe = true;

			$lastType = null;

			foreach ($this->enumValues as $value) {
				if ( !is_string($value) && !is_numeric($value) ) {
					$this->cachedStringConversionSafe = false;
					break;
				}

				//All values must be of the same type. Otherwise, we can't distinguish between
				//numbers and strings that contain numbers.
				$thisType = gettype($value);
				if ( ($lastType !== null) && ($thisType !== $lastType) ) {
					$this->cachedStringConversionSafe = false;
					break;
				}
				$lastType = $thisType;
			}

		}

		return $this->cachedStringConversionSafe;
	}

	public function areAllValuesStrings(): bool {
		if ( $this->cachedAllValuesAreStrings !== null ) {
			return $this->cachedAllValuesAreStrings;
		}

		$this->cachedAllValuesAreStrings = true;
		foreach ($this->enumValues as $value) {
			if ( !is_string($value) ) {
				$this->cachedAllValuesAreStrings = false;
				break;
			}
		}
		return $this->cachedAllValuesAreStrings;
	}

	public function getEnumValues() {
		return $this->enumValues;
	}

	public function describeValue($value, $label, $description = '', $enabled = null, $icon = null) {
		$safeValue = wp_json_encode($value);
		$this->valueDetails[$safeValue] = [
			'label'       => $label,
			'description' => $description,
			'enabled'     => $enabled,
			'icon'        => $icon,
		];
		return $this;
	}

	public function getValueDetails($value) {
		$safeValue = wp_json_encode($value);
		return isset($this->valueDetails[$safeValue]) ? $this->valueDetails[$safeValue] : null;
	}

	public function isValueEnabled($value) {
		if ( !in_array($value, $this->enumValues, true) ) {
			return false;
		}

		$safeValue = wp_json_encode($value);
		if ( !isset($this->valueDetails[$safeValue]['enabled']) ) {
			return true; //All values are enabled by default.
		}

		$decider = $this->valueDetails[$safeValue]['enabled'];
		if ( is_scalar($decider) ) {
			return (bool)$decider;
		} elseif ( is_callable($decider) ) {
			return call_user_func($decider, $value);
		}

		return true;
	}
}