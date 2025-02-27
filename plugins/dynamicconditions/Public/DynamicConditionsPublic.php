<?php

namespace DynamicConditions\Pub;

use Elementor\Core\Base\Document;
use Elementor\Element_Base;
use Elementor\Plugin;
use ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager;
use ElementorPro\Modules\ThemeBuilder\Module;
use DynamicConditions\Lib\Date;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.rto.de
 * @since      1.0.0
 *
 * @package    DynamicConditions
 * @subpackage DynamicConditions/public
 */

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    die;
}

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    DynamicConditions
 * @subpackage DynamicConditions/public
 * @author     RTO GmbH <kundenhomepage@rto.de>
 */
class DynamicConditionsPublic {

    private string $pluginName;

    private string $version;

    private array $elementSettings = [];

    /**
     * @access   private
     * @var      array $isSectionHidden For storing hidden-status
     */
    private array $isSectionHidden = [];

    private Date $dateInstance;

    private static bool $debugCssRendered = false;

    private array $shortcodeTags = [];

    /**
     * Initialize the class and set its properties.
     */
    public function __construct( string $pluginName, string $version ) {

        $this->pluginName = $pluginName;
        $this->version = $version;
        $this->dateInstance = new Date();
    }

    /**
     * Gets settings with english locale (needed for date)
     * @param Element_Base|Document $element
     */
    private function getElementSettings( $element ): array {
        $id =  get_the_id(). '-'. $element->get_id();

        if ( !empty( $this->elementSettings[$id] ) ) {
            return $this->elementSettings[$id];
        }
        $clonedElement = clone $element;

        $fields = '__dynamic__
            dynamicconditions_dynamic
            dynamicconditions_condition
            dynamicconditions_type
            dynamicconditions_resizeOtherColumns
            dynamicconditions_hideContentOnly
            dynamicconditions_visibility
            dynamicconditions_day_value
            dynamicconditions_day_value2
            dynamicconditions_day_array_value
            dynamicconditions_month_value
            dynamicconditions_month_value2
            dynamicconditions_month_array_value
            dynamicconditions_date_value
            dynamicconditions_date_value2
            dynamicconditions_value
            dynamicconditions_value2
            dynamicconditions_parse_shortcodes
            dynamicconditions_debug
            dynamicconditions_hideOthers
            dynamicconditions_hideWrapper
            dynamicconditions_removeStyles
            _column_size
            _inline_size';

        $fieldArray = explode( "\n", $fields );

        $this->elementSettings[$id]['dynamicconditions_dynamic_raw'] = $element->get_settings_for_display( 'dynamicconditions_dynamic' );

        $preventDateParsing = $element->get_settings_for_display( 'dynamicconditions_prevent_date_parsing' );
        $this->elementSettings[$id]['preventDateParsing'] = $preventDateParsing;

        if ( empty( $preventDateParsing ) ) {
            // set locale to english, for better parsing
            $currentLocale = setlocale( LC_ALL, 0 );
            setlocale( LC_ALL, 'en_GB' );
            add_filter( 'date_i18n', [ $this->dateInstance, 'filterDateI18n' ], 10, 4 );
            add_filter( 'get_the_date', [ $this->dateInstance, 'filterPostDate' ], 10, 3 );
            add_filter( 'get_the_modified_date', [ $this->dateInstance, 'filterPostDate' ], 10, 3 );
        }

        foreach ( $fieldArray as $field ) {
            $field = trim( $field );
            $this->elementSettings[$id][$field] = $clonedElement->get_settings_for_display( $field );
        }
        unset( $clonedElement );

        if ( empty( $preventDateParsing ) ) {
            remove_filter( 'date_i18n', [ $this->dateInstance, 'filterDateI18n' ], 10 );
            remove_filter( 'get_the_date', [ $this->dateInstance, 'filterPostDate' ], 10 );
            remove_filter( 'get_the_modified_date', [ $this->dateInstance, 'filterPostDate' ], 10 );

            // reset locale
            Date::setLocale( $currentLocale );
        }

        $tagData = $this->getDynamicTagData( $id );
        $this->convertAcfDate( $id, $tagData );

        $this->elementSettings[$id]['dynamicConditionsData'] = [
            'id' => $id,
            'type' => $element->get_type(),
            'name' => $element->get_name(),
            'selectedTag' => $tagData['selectedTag'],
            'tagData' => $tagData['tagData'],
            'tagKey' => $tagData['tagKey'],
        ];

        return $this->elementSettings[$id];
    }

    /**
     * Returns data of dynamic tag
     */
    private function getDynamicTagData( string $id ): array {
        $dynamicEmpty = empty( $this->elementSettings[$id]['__dynamic__'] )
            || empty( $this->elementSettings[$id]['__dynamic__']['dynamicconditions_dynamic'] );
        $staticEmpty = empty( $this->elementSettings[$id]['dynamicconditions_dynamic'] )
            || empty( $this->elementSettings[$id]['dynamicconditions_dynamic']['url'] );

        if ( $dynamicEmpty && $staticEmpty ) {
            // no dynamic tag or static value set
            return [
                'selectedTag' => null,
                'tagData' => null,
                'tagKey' => null,
            ];
        }

        $selectedTag = null;
        $tagSettings = null;
        $tagData = [];
        $tagKey = null;

        if ( $dynamicEmpty ) {
            // no dynamic tag set, but static value
            $this->elementSettings[$id]['__dynamic__'] = [
                'dynamicconditions_dynamic' => $this->elementSettings[$id]['dynamicconditions_dynamic'],
            ];
            $selectedTag = 'static';
        }


        $tag = $this->elementSettings[$id]['__dynamic__']['dynamicconditions_dynamic'];
        if ( is_array( $tag ) ) {
            return [
                'selectedTag' => null,
                'tagData' => null,
                'tagKey' => null,
            ];
        }
        $splitTag = explode( ' name="', $tag );

        // get selected tag
        if ( !empty( $splitTag[1] ) ) {
            $splitTag2 = explode( '"', $splitTag[1] );
            $selectedTag = $splitTag2[0];
        }

        // get tag settings
        if ( strpos( $selectedTag, 'acf-' ) === 0 ) {
            $splitTag = explode( ' settings="', $tag );
            if ( !empty( $splitTag[1] ) ) {
                $splitTag2 = explode( '"', $splitTag[1] );
                $tagSettings = json_decode( urldecode( $splitTag2[0] ), true );
                if ( !empty( $tagSettings['key'] ) ) {
                    $tagKey = $tagSettings['key'];
                    $tagData = get_field_object( explode( ':', $tagSettings['key'] )[0] );
                }
            }
        }
        return [
            'selectedTag' => $selectedTag,
            'tagData' => $tagData,
            'tagKey' => $tagKey,
        ];

    }

    /**
     * Convert acf date to timestamp
     */
    private function convertAcfDate( string $id, ?array $data ): void {
        if ( empty( $data ) ) {
            return;
        }

        if ( !empty( $this->elementSettings[$id]['preventDateParsing'] ) ) {
            return;
        }

        $allowedTypes = [
            'date_time_picker',
            'date_picker',
        ];

        $tagData = $data['tagData'];

        if ( empty( $data['tagKey'] ) || strpos( $data['selectedTag'], 'acf-' ) !== 0 ) {
            return;
        }

        if ( empty( $tagData['type'] ) || !in_array( trim( $tagData['type'] ), $allowedTypes, true ) ) {
            return;
        }

        if ( empty( $tagData['value'] ) || empty( $tagData['return_format'] ) ) {
            return;
        }

        $time = \DateTime::createFromFormat( $tagData['return_format'], Date::unTranslateDate( $tagData['value'] ) );

        if ( empty( $time ) ) {
            return;
        }

        if ( $tagData['type'] === 'date_picker' ) {
            $time->setTime( 0, 0, 0 );
        }

        $timestamp = $time->getTimestamp();

        // override value with timestamp
        $this->elementSettings[$id]['dynamicconditions_dynamic'] = $timestamp;
    }


    /**
     * Removes popup from location, if it is hidden by condition
     */
    public function checkPopupsCondition( Locations_Manager $locationManager ): void {
        if ( $this->getMode() !== 'website' ) {
            return;
        }

        $conditionManager = Module::instance()->get_conditions_manager();
        $module = $conditionManager->get_documents_for_location( 'popup' );

        foreach ( $module as $documentId => $document ) {
            $settings = $this->getElementSettings( $document );
            $hide = $this->checkCondition( $settings );

            if ( $hide ) {
                $locationManager->remove_doc_from_location( 'popup', $documentId );
            }
        }
    }

    /**
     * Check if section is hidden, before rendering
     * @param Element_Base|Document $section
     */
    public function filterSectionContentBefore( $section ): void {
        if ( $this->getMode() === 'edit' ) {
            return;
        }

        $settings = $this->getElementSettings( $section );
        $hide = $this->checkCondition( $settings );

        if ( !$hide ) {
            return;
        }

        $id = get_the_id() .'-'. $section->get_id();
        $this->isSectionHidden[$id] = true;

        //prevent shortcodes from execution
        $this->shortcodeTags += $GLOBALS['shortcode_tags'];
        $GLOBALS['shortcode_tags'] = [];

        ob_start();
    }

    /**
     * Clean output of section if it is hidden
     *
     * @param Element_Base|Document $section
     */
    public function filterSectionContentAfter( $section ): void {
        // reset shortcode tags
        $GLOBALS['shortcode_tags'] += $this->shortcodeTags;
        if ( empty( $section ) ||
            empty( $this->isSectionHidden[get_the_id() .'-'. $section->get_id()] )
        ) {
            return;
        }
        $id = get_the_id() .'-'. $section->get_id();

        /*while ( ob_get_level() > $this->widgetCache[$section->get_id()]['ob_level'] ) {
            ob_end_flush();
        }*/

        $content = ob_get_clean();
        $matchesLinkTags = [];
        $matchesStyleTags = [];

        $type = $section->get_type();
        $settings = $this->elementSettings[$id];

        if ( empty( $settings['dynamicconditions_removeStyles'] ) ) {
            preg_match_all( '/<link.*?\/?>/', $content, $matchesLinkTags );
            preg_match_all( '/<style(.*?)<\/style>/s', $content, $matchesStyleTags );
            echo implode( '', $matchesLinkTags[0] );
            echo implode( '', $matchesStyleTags[0] );
        }

        if ( !empty( $settings['dynamicconditions_hideContentOnly'] ) ) {
            // render wrapper
            $section->before_render();
            $section->after_render();
        } else if ( $type == 'column' && $settings['dynamicconditions_resizeOtherColumns'] ) {
            echo '<div class="dc-hidden-column" data-size="' . floatval( $settings['_column_size'] ) . '"></div>';
        }

        if ( !empty( $settings['dynamicconditions_hideWrapper'] ) ) {
            echo '<div class="dc-hide-wrapper" data-selector="' . esc_attr($settings['dynamicconditions_hideWrapper']) . '"></div>';
        }

        if ( !empty( $settings['dynamicconditions_hideOthers'] ) ) {
            echo '<div class="dc-hide-others" data-selector="' . esc_attr($settings['dynamicconditions_hideOthers']) . '"></div>';
        }

        echo "<!-- hidden $type $id -->";
    }

    /**
     * Checks condition, return if element is hidden
     */
    public function checkCondition( array $settings ): bool {
        if ( !$this->hasCondition( $settings ) ) {
            return false;
        }

        if ( $this->getMode() === 'edit' ) {
            return false;
        }
        /*if ( filter_input( INPUT_SERVER, 'REQUEST_METHOD' ) === 'POST' ) {
            return false;
        }*/

        // loop values
        $condition = $this->loopValues( $settings );

        $hide = false;

        $visibility = self::checkEmpty( $settings, 'dynamicconditions_visibility', 'hide' );
        switch ( $visibility ) {
            case 'show':
                if ( !$condition ) {
                    $hide = true;
                }
                break;
            case 'hide':
            default:
                if ( $condition ) {
                    $hide = true;
                }
                break;
        }

        return $hide;
    }

    /**
     * Loop widget-values and check the condition
     */
    private function loopValues( array $settings ): bool {
        $condition = false;
        $dynamicTagValueArray = self::checkEmpty( $settings, 'dynamicconditions_dynamic' );

        if ( !is_array( $dynamicTagValueArray ) ) {
            $dynamicTagValueArray = [ $dynamicTagValueArray ];
        }

        // get value form conditions
        $compareType = self::checkEmpty( $settings, 'dynamicconditions_type', 'default' );
        $checkValues = $this->getCheckValue( $compareType, $settings );
        $checkValue = $checkValues[0];
        $checkValue2 = $checkValues[1];

        $debugValue = '';

        foreach ( $dynamicTagValueArray as $dynamicTagValue ) {
            if ( is_array( $dynamicTagValue ) ) {
                if ( !empty( $dynamicTagValue['id'] ) ) {
                    $dynamicTagValue = wp_get_attachment_url( $dynamicTagValue['id'] );
                } else {
                    continue;
                }
            }

            if ( !empty( $settings['dynamicconditions_parse_shortcodes'] ) ) {
                $dynamicTagValue = do_shortcode( $dynamicTagValue );
            }

            // parse value based on compare-type
            $this->parseDynamicTagValue( $dynamicTagValue, $compareType );

            $debugValue .= $dynamicTagValue . '~~*#~~';

            // compare widget-value with check-values
            $compareValues = $this->compareValues( $settings['dynamicconditions_condition'], $dynamicTagValue, $checkValue, $checkValue2 );
            $condition = $compareValues[0];
            $break = $compareValues[1];
            $breakFalse = $compareValues[2];

            if ( $break && $condition ) {
                // break if condition is true
                break;
            }

            if ( $breakFalse && !$condition ) {
                // break if condition is false
                break;
            }
        }

        // debug output
        $this->renderDebugInfo( $settings, $debugValue, $checkValue, $checkValue2, $condition );

        return $condition;
    }

    /**
     * Compare values
     *
     * @param $compare
     * @param $dynamicTagValue
     * @param $checkValue
     * @param $checkValue2
     * @return array
     */
    private function compareValues( $compare, $dynamicTagValue, $checkValue, $checkValue2 ): array {
        $break = false;
        $breakFalse = false;
        $condition = false;
        if ( is_null( $dynamicTagValue ) ) {
            $dynamicTagValue = '';
        }

        switch ( $compare ) {
            case 'equal':
                $condition = $checkValue == $dynamicTagValue;
                $break = true;
                break;

            case 'not_equal':
                $condition = $checkValue != $dynamicTagValue;
                $breakFalse = true;
                break;

            case 'contains':
                if ( empty( $checkValue ) ) {
                    break;
                }
                $condition = strpos( $dynamicTagValue, $checkValue ) !== false;
                $break = true;
                break;

            case 'not_contains':
                if ( empty( $checkValue ) ) {
                    break;
                }
                $condition = strpos( $dynamicTagValue, $checkValue ) === false;
                $breakFalse = true;
                break;

            case 'empty':
                $condition = empty( $dynamicTagValue );
                $breakFalse = true;
                break;

            case 'not_empty':
                $condition = !empty( $dynamicTagValue );
                $break = true;
                break;

            case 'less':
                if ( is_numeric( $dynamicTagValue ) ) {
                    $condition = $dynamicTagValue < $checkValue;
                } else {
                    $condition = strlen( $dynamicTagValue ) < strlen( $checkValue );
                }
                $break = true;
                break;

            case 'greater':
                if ( is_numeric( $dynamicTagValue ) ) {
                    $condition = $dynamicTagValue > $checkValue;
                } else {
                    $condition = strlen( $dynamicTagValue ) > strlen( $checkValue );
                }
                $break = true;
                break;

            case 'between':
                $condition = $dynamicTagValue >= $checkValue && $dynamicTagValue <= $checkValue2;
                $break = true;
                break;

            case 'in_array':
                $condition = in_array( $dynamicTagValue, explode( ',', $checkValue ) ) !== false;
                $break = true;
                break;

            case 'in_array_contains':
                foreach ( explode( ',', $checkValue ) as $toCheck ) {
                    $condition = strpos( $dynamicTagValue, $toCheck ) !== false;
                    if ( $condition ) {
                        break;
                    }
                }
                $break = true;
                break;
        }

        return [
            $condition,
            $break,
            $breakFalse,
        ];
    }

    /**
     * Parse value of widget to timestamp, day or month
     */
    private function parseDynamicTagValue( ?string &$dynamicTagValue, string $compareType ): void {
        switch ( $compareType ) {
            case 'days':
                $dynamicTagValue = date( 'N', Date::stringToTime( $dynamicTagValue ) );
                break;

            case 'months':
                $dynamicTagValue = date( 'n', Date::stringToTime( $dynamicTagValue ) );
                break;

            case 'int':
                $dynamicTagValue = (int)filter_var( $dynamicTagValue, FILTER_SANITIZE_NUMBER_INT );
                break;

            case 'strtotime':
                // nobreak
            case 'date':
                $dynamicTagValue = Date::stringToTime( $dynamicTagValue );
                break;
        }
    }

    /**
     * Get value to compare
     */
    private function getCheckValue( string $compareType, array $settings ): array {

        switch ( $compareType ) {
            case 'days':
                if ( $settings['dynamicconditions_condition'] === 'in_array' ) {
                    $checkValue = self::checkEmpty( $settings, 'dynamicconditions_day_array_value' );
                    $checkValue = $this->parseShortcode( $checkValue, $settings );
                    $checkValue = implode( ',', $checkValue );
                } else {
                    $checkValue = self::checkEmpty( $settings, 'dynamicconditions_day_value' );
                    $checkValue = $this->parseShortcode( $checkValue );
                }
                $checkValue2 = self::checkEmpty( $settings, 'dynamicconditions_day_value2' );
                $checkValue2 = $this->parseShortcode( $checkValue2, $settings );
                $checkValue = Date::unTranslateDate( $checkValue );
                $checkValue2 = Date::unTranslateDate( $checkValue2 );
                break;

            case 'months':
                if ( $settings['dynamicconditions_condition'] === 'in_array' ) {
                    $checkValue = self::checkEmpty( $settings, 'dynamicconditions_month_array_value' );
                    $checkValue = $this->parseShortcode( $checkValue, $settings );
                    $checkValue = implode( ',', $checkValue );
                } else {
                    $checkValue = self::checkEmpty( $settings, 'dynamicconditions_month_value' );
                    $checkValue = $this->parseShortcode( $checkValue, $settings );
                }
                $checkValue2 = self::checkEmpty( $settings, 'dynamicconditions_month_value2' );
                $checkValue2 = $this->parseShortcode( $checkValue2, $settings );
                $checkValue = Date::unTranslateDate( $checkValue );
                $checkValue2 = Date::unTranslateDate( $checkValue2 );
                break;

            case 'date':
                $checkValue = self::checkEmpty( $settings, 'dynamicconditions_date_value' );
                $checkValue2 = self::checkEmpty( $settings, 'dynamicconditions_date_value2' );
                $checkValue = $this->parseShortcode( $checkValue, $settings );
                $checkValue2 = $this->parseShortcode( $checkValue2, $settings );
                $checkValue = Date::stringToTime( $checkValue );
                $checkValue2 = Date::stringToTime( $checkValue2 );
                break;

            case 'strtotime':
                $checkValue = self::checkEmpty( $settings, 'dynamicconditions_value' );
                $checkValue2 = self::checkEmpty( $settings, 'dynamicconditions_value2' );
                $checkValue = $this->parseShortcode( $checkValue, $settings );
                $checkValue2 = $this->parseShortcode( $checkValue2, $settings );
                $checkValue = Date::stringToTime( $checkValue );
                $checkValue2 = Date::stringToTime( $checkValue2 );
                break;

            case 'int':
                $checkValue = (int)filter_var( self::checkEmpty( $settings, 'dynamicconditions_value' ), FILTER_SANITIZE_NUMBER_INT );
                $checkValue2 = (int)filter_var( self::checkEmpty( $settings, 'dynamicconditions_value2' ), FILTER_SANITIZE_NUMBER_INT );
                break;

            case 'default':
            default:
                $checkValue = self::checkEmpty( $settings, 'dynamicconditions_value' );
                $checkValue2 = self::checkEmpty( $settings, 'dynamicconditions_value2' );
                $checkValue = $this->parseShortcode( $checkValue, $settings );
                $checkValue2 = $this->parseShortcode( $checkValue2, $settings );
                break;
        }

        return [
            $checkValue,
            $checkValue2,
        ];
    }

    /**
     * Parse shortcode if active
     * @return mixed
     */
    private function parseShortcode( $value, array $settings = [] ) {
        if ( !is_string( $value ) ) {
            return $value;
        }
        if ( empty( $settings['dynamicconditions_parse_shortcodes'] ) ) {
            return $value;
        }
        return do_shortcode( $value );
    }

    /**
     * Checks if an array or entry in array is empty and return its value
     * @return mixed
     */
    public static function checkEmpty( array $array = [], ?string $key = null, ?string $fallback = null ) {
        if ( empty( $key ) ) {
            return !empty( $array ) ? $array : $fallback;
        }

        return !empty( $array[$key] ) ? $array[$key] : $fallback;
    }

    /**
     * Checks if element has a condition
     */
    public function hasCondition( array $settings ): bool {
        if ( empty( $settings['dynamicconditions_condition'] ) || empty( $settings['dynamicConditionsData']['selectedTag'] )
        ) {
            // no condition or no tag selected - disable conditions
            return false;
        }

        return true;
    }

    /**
     * Renders debug info
     *
     * @param $settings
     * @param $dynamicTagValue
     * @param $checkValue
     * @param $checkValue2
     * @param $conditionMets
     */
    private function renderDebugInfo( $settings, $dynamicTagValue, $checkValue, $checkValue2, $conditionMets ): void {
        if ( !$settings['dynamicconditions_debug'] ) {
            return;
        }

        if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
            return;
        }

        $visibility = self::checkEmpty( $settings, 'dynamicconditions_visibility', 'hide' );

        $dynamicTagValue = str_replace( '[', '&#91;', htmlentities( $dynamicTagValue ?? '' ) );
        $dynamicTagValue = str_replace( '~~*#~~', '<br />', $dynamicTagValue );
        $checkValue = str_replace( '[', '&#91;', htmlentities( $checkValue ?? '' ) );
        $checkValue2 = str_replace( '[', '&#91;', htmlentities( $checkValue2 ?? '' ) );
        $dynamicTagValueRaw = self::checkEmpty( $settings, 'dynamicconditions_dynamic_raw', '' );

        if ( is_array( $dynamicTagValueRaw ) ) {
            $dynamicTagValueRaw = json_encode( $dynamicTagValueRaw );
        }

        include( 'partials/debug.php' );

        $this->renderDebugCss();
    }

    /**
     * Renders css for debug-output
     */
    private function renderDebugCss(): void {
        if ( self::$debugCssRendered ) {
            return;
        }
        self::$debugCssRendered = true;

        echo '<style>';
        include( 'css/debug.css' );
        echo '</style>';
    }

    /**
     * Returns elementor-mode (edit, preview or website)
     */
    private function getMode(): string {
        if ( !class_exists( 'Elementor\Plugin' ) ) {
            return '';
        }

        if ( !empty( Plugin::$instance->editor ) && Plugin::$instance->editor->is_edit_mode() ) {
            return 'edit';
        }

        if ( !empty( Plugin::$instance->preview ) && Plugin::$instance->preview->is_preview_mode() ) {
            return 'preview';
        }

        return 'website';
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueueScripts(): void {
        if ( $this->getMode() === 'edit' ) {
            return;
        }
        wp_enqueue_script( $this->pluginName, DynamicConditions_URL . '/Public/js/dynamic-conditions-public.js', [ 'jquery' ], $this->version, true );
    }

}