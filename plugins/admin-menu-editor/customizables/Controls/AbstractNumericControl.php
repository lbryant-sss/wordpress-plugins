<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Controls;

use YahnisElsts\AdminMenuEditor\Customizable\HtmlHelper;
use YahnisElsts\AdminMenuEditor\Customizable\Settings;
use YahnisElsts\AdminMenuEditor\Customizable\Schemas;

abstract class AbstractNumericControl extends ClassicControl {
	const NUMBER_VALIDATION_PATTERN = '\\s*-?[0-9]+(?:[.,]\\d*)?\s*';

	protected $min = null;
	protected $max = null;
	protected $step = null;

	protected $rangeByUnit = [];

	protected $spinButtonsAllowed = false;

	public function __construct($settings = [], $params = []) {
		parent::__construct($settings, $params);

		//Range.
		list($defaultMin, $defaultMax) = static::getSettingMinMax($this->mainSetting);
		if ( array_key_exists('min', $params) ) {
			$this->min = $params['min'];
		} else {
			$this->min = $defaultMin;
		}
		if ( array_key_exists('max', $params) ) {
			$this->max = $params['max'];
		} else {
			$this->max = $defaultMax;
		}

		//Step.
		if ( array_key_exists('step', $params) ) {
			//Step must be a positive number, null, or the special value "any".
			if ( is_numeric($params['step']) ) {
				$this->step = abs($params['step']);
			} else if ( ($params['step'] === null) || ($params['step'] === 'any') ) {
				$this->step = $params['step'];
			} else {
				throw new \InvalidArgumentException("Invalid step value: {$params['step']}");
			}
		}

		//Each unit can have a different range.
		if ( array_key_exists('rangeByUnit', $params) ) {
			$this->rangeByUnit = $params['rangeByUnit'];
		}
	}

	protected static function getSettingMinMax($setting) {
		if ( !$setting ) {
			return [null, null];
		}

		if ( $setting instanceof Settings\NumericSetting ) {
			return [$setting->getMinValue(), $setting->getMaxValue()];
		} else if ( $setting instanceof Settings\WithSchema\SingularSetting ) {
			$schema = $setting->getSchema();
			if ( $schema instanceof Schemas\Number ) {
				return [$schema->getMin(), $schema->getMax()];
			}
		}
		return [null, null];
	}

	protected static function settingMayContainFloat($setting) {
		if ( $setting instanceof Settings\WithSchema\SingularSetting ) {
			$schema = $setting->getSchema();
			if ( $schema instanceof Schemas\Number ) {
				return !$schema->isInt();
			}
		} else if ( $setting instanceof Settings\FloatSetting ) {
			return true;
		}
		return false;
	}

	protected function getSliderRanges() {
		$sliderRanges = [];
		if ( ($this->min !== null) && ($this->max !== null) ) {
			$sliderRanges['_default'] = [
				'min'  => $this->min,
				'max'  => $this->max,
				'step' => $this->getDefaultStep(),
			];
		}
		return array_merge($sliderRanges, $this->rangeByUnit);
	}

	/**
	 * @return float|int
	 */
	protected function getDefaultStep() {
		if ( is_numeric($this->step) ) {
			$step = (float)$this->step;
		} else {
			if ( self::settingMayContainFloat($this->mainSetting) ) {
				$step = ($this->max - $this->min) / 100;
			} else {
				$step = 1;
			}
		}
		return $step;
	}

	protected function getBasicInputAttributes() {
		$attributes = [
			'min'  => $this->min,
			'max'  => $this->max,
			'step' => $this->step,
		];

		if ( $this->spinButtonsAllowed ) {
			$attributes['type'] = 'number';
		} else {
			$attributes['type'] = 'text';
			$attributes['inputmode'] = 'numeric';
			$attributes['pattern'] = self::NUMBER_VALIDATION_PATTERN;
			$attributes['maxlength'] = 20;
		}
		return $attributes;
	}

	protected function renderUnitDropdown(
		Settings\AbstractSetting $unitSetting,
		                         $elementAttributes = [],
		                         $includeKoBindings = true
	) {
		//Display a dropdown list of units.
		$units = ChoiceControlOption::tryGenerateFromSetting($unitSetting);
		if ( empty($units) ) {
			return false; //This setting isn't an enum or doesn't have any values.
		}

		$selectedUnit = $unitSetting->getValue();

		list($optionHtml, $optionBindings) = ChoiceControlOption::generateSelectOptions(
			$units,
			$selectedUnit,
			$unitSetting
		);

		if ( $includeKoBindings ) {
			$elementAttributes['data-bind'] = $this->makeKoDataBind(array_merge(
				$optionBindings,
				['value' => $this->getKoObservableExpression($selectedUnit, $unitSetting)],
				$this->getKoEnableBinding()
			));
		}

		echo HtmlHelper::tag('select', $elementAttributes);
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $optionHtml;
		echo '</select>';

		return true;
	}

	protected function getKoComponentParams() {
		$params = parent::getKoComponentParams();
		$params['min'] = $this->min;
		$params['max'] = $this->max;
		$params['step'] = $this->getDefaultStep();

		$sliderRanges = $this->getSliderRanges();
		if ( !empty($sliderRanges) ) {
			$params['sliderRanges'] = $sliderRanges;
		}

		return $params;
	}
}