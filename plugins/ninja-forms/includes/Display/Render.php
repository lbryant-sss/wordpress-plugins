<?php if ( ! defined( 'ABSPATH' ) ) exit;
use NinjaForms\Includes\Handlers\Sanitizer;

class NF_Display_Render
{
    protected static $render_instance_count = array();

    protected static $loaded_templates = array(
        'app-layout',
        'app-before-form',
        'app-after-form',
        'app-before-fields',
        'app-after-fields',
        'app-before-field',
        'app-after-field',
        'form-layout',
        'form-hp',
        'field-layout',
        'field-before',
        'field-after',
        'fields-wrap',
        'fields-wrap-no-label',
        'fields-wrap-no-container',
        'fields-label',
        'fields-error',
        'form-error',
        'field-input-limit',
        'field-null'
    );

    public static $use_test_values = FALSE;

    protected static $form_uses_recaptcha      = array();
    protected static $form_uses_datepicker     = array();
    protected static $form_uses_inputmask      = array();
    protected static $form_uses_currencymask   = array();
    protected static $form_uses_rte            = array();
    protected static $form_uses_textarea_media = array();
    protected static $form_uses_helptext       = array();
    protected static $form_uses_starrating     = array();

    protected static $thread_id      = 1;
    protected static $recorded_forms       = [];
    protected static $inline_vars_recorded  = [];

    public static function localize( $form_id )
    {
        global $wp_locale;
        $form_id = absint( $form_id );

        /**
         * Action that passes the form ID as a parameter.
         * @since 3.2.2
         */
        do_action( 'nf_get_form_id', $form_id );

        $capability = apply_filters( 'ninja_forms_display_test_values_capabilities', 'read' );
        if( isset( $_GET[ 'ninja_forms_test_values' ] ) && current_user_can( $capability ) ){
            self::$use_test_values = TRUE;
        }

        if( ! has_action( 'wp_footer', 'NF_Display_Render::output_templates', 9999 ) ){
            add_action( 'wp_footer', 'NF_Display_Render::output_templates', 9999 );
        }
        $form = Ninja_Forms()->form( $form_id )->get();

        $settings = $form->get_settings();

        foreach( $settings as $name => &$value ){
            if( ! in_array(
                $name,
                array(
                    'changeEmailErrorMsg',
                    'changeDateErrorMsg',
                    'confirmFieldErrorMsg',
                    'fieldNumberNumMinError',
                    'fieldNumberNumMaxError',
                    'fieldNumberIncrementBy',
                    'formErrorsCorrectErrors',
                    'validateRequiredField',
                    'honeypotHoneypotError',
                    'fieldsMarkedRequired',
                )
            ) ) continue;

            if( $value ) {
                $value = esc_html($value);
                continue;
            }

            unset( $settings[ $name ] );
        }

        // Remove the embed_form setting to avoid pagebuilder conflicts.
        $settings[ 'embed_form' ] = '';

        $settings = array_merge( Ninja_Forms::config( 'i18nFrontEnd' ), $settings );
        $settings = apply_filters( 'ninja_forms_display_form_settings', $settings, $form_id );

        $form->update_settings( $settings );

        if( $form->get_setting( 'logged_in' ) && ! is_user_logged_in() ){
            echo do_shortcode( $form->get_setting( 'not_logged_in_msg' ));
            return;
        }

        if( $form->get_setting( 'sub_limit_number' ) && ! empty($form->get_setting( 'sub_limit_number' )) ){
            global $wpdb;
            $result = $wpdb->get_row( "SELECT COUNT(DISTINCT(p.ID)) AS count FROM `$wpdb->posts` AS p
            LEFT JOIN `$wpdb->postmeta` AS m
            ON p.ID = m.post_id
            WHERE m.meta_key = '_form_id'
            AND m.meta_value = $form_id
            AND p.post_status = 'publish'");

            if( intval( $result->count ) >= $form->get_setting( 'sub_limit_number' ) ) {
                echo do_shortcode( apply_filters( 'nf_sub_limit_reached_msg', $form->get_setting( 'sub_limit_msg' ), $form_id ));
                return;
            }
        }

        // Get our maintenance value out of the DB.
        $maintenance = WPN_Helper::form_in_maintenance( $form_id );

        // If maintenance isn't empty and the bool is set to 1 then..
        if( true == $maintenance ) {
            // Set a filterable maintenance message and echo it out.
            $maintenance_msg = apply_filters( 'nf_maintenance_message', esc_html__( 'This form is currently undergoing maintenance. Please try again later.', 'ninja-forms' ) );
            echo $maintenance_msg;

            // bail.
            return false;
        }


        if( ! apply_filters( 'ninja_forms_display_show_form', true, $form_id, $form ) ) return;

        $currency = $form->get_setting( 'currency', Ninja_Forms()->get_setting( 'currency' ) );
        $currencySymbolLookup = Ninja_Forms::config( 'CurrencySymbol' );

        $currencySymbol = self::getCurrencySymbol($currencySymbolLookup,$currency) ;
        $form->update_setting( 'currency_symbol', $currencySymbol );

        $title = apply_filters( 'ninja_forms_form_title', $form->get_setting( 'title' ), $form_id );
        $form->update_setting( 'title', esc_html( $title ) );

        $before_form = apply_filters( 'ninja_forms_display_before_form', '', $form_id );
        $form->update_setting( 'beforeForm', $before_form );

        $before_fields = apply_filters( 'ninja_forms_display_before_fields', '', $form_id );
        $form->update_setting( 'beforeFields', $before_fields );

        $after_fields = apply_filters( 'ninja_forms_display_after_fields', '', $form_id );
        $form->update_setting( 'afterFields', $after_fields );

        $after_form = apply_filters( 'ninja_forms_display_after_form', '', $form_id );
        $form->update_setting( 'afterForm', $after_form );

        $form_fields = Ninja_Forms()->form( $form_id )->get_fields();
        $fields = array();

        if( empty( $form_fields ) ){
            echo esc_html__( 'No Fields Found.', 'ninja-forms' );
        } else {

            // TODO: Replace unique field key checks with a refactored model/factory.
            $unique_field_keys = array();

            foreach ($form_fields as $field) {

                if( is_object( $field ) ) {
                    $field = array(
                        'id' => $field->get_id(),
                        'settings' => $field->get_settings()
                    );
                }

                $field_id = $field[ 'id' ];


                /*
                 * Duplicate field check.
                 * TODO: Replace unique field key checks with a refactored model/factory.
                 */
                $field_key = $field[ 'settings' ][ 'key' ];

                if( in_array( $field_key, $unique_field_keys ) || '' == $field_key ){
                    continue; // Skip the duplicate field.
                }
                array_push( $unique_field_keys, $field_key ); // Log unique key.
                /* END Duplicate field check. */

                $field_type = $field[ 'settings' ][ 'type' ];

                if( ! is_string( $field_type ) ) continue;

                if( ! isset( Ninja_Forms()->fields[ $field_type ] ) ) {
                    $field =  self::constructUnknownField($field);
                    $field_type = $field[ 'settings' ][ 'type' ];
                }

                $fieldBeforeFilters = $field;

                $field = self::applyLocalizeFilters($field, $field_type);

                $field_class = Ninja_Forms()->fields[$field_type];

                if (self::$use_test_values) {
                    $field[ 'value' ] = $field_class->get_test_value();
                }

                $field= self::ensureFieldArrayStructureValidity($field,$fieldBeforeFilters);
                
                // Copy field ID into the field settings array for use in localized data.
                $field[ 'settings' ][ 'id' ] = $field[ 'id' ];


                /*
                 * TODO: For backwards compatibility, run the original action, get contents from the output buffer, and return the contents through the filter. Also display a PHP Notice for a deprecate filter.
                 */

                $display_before = apply_filters( 'ninja_forms_display_before_field_type_' . $field[ 'settings' ][ 'type' ], '' );
                $display_before = apply_filters( 'ninja_forms_display_before_field_key_' . $field[ 'settings' ][ 'key' ], $display_before );
                $field[ 'settings' ][ 'beforeField' ] = $display_before;

                $display_after = apply_filters( 'ninja_forms_display_after_field_type_' . $field[ 'settings' ][ 'type' ], '' );
                $display_after = apply_filters( 'ninja_forms_display_after_field_key_' . $field[ 'settings' ][ 'key' ], $display_after );
                $field[ 'settings' ][ 'afterField' ] = $display_after;

                $templates = $field_class->get_templates();

                if (!array($templates)) {
                    $templates = array($templates);
                }

                foreach ($templates as $template) {
                    self::load_template('fields-' . $template);
                }

                $settings = $field[ 'settings' ];
                // Scrub any values that might be stored in data. Defaults will set these later.
                $settings['value'] = '';
                foreach ($settings as $key => $setting) {
                    if (is_numeric($setting) && 'custom_mask' != $key )
                    	$settings[$key] =
	                    floatval($setting);
                }

                if( ! isset( $settings[ 'label_pos' ] ) || 'default' == $settings[ 'label_pos' ] ){
                    $settings[ 'label_pos' ] = $form->get_setting( 'default_label_pos' );
                }

                $settings[ 'parentType' ] = $field_class->get_parent_type();

                if( 'list' == $settings[ 'parentType' ] && isset( $settings[ 'options' ] ) && is_array( $settings[ 'options' ] ) ){
                    $settings[ 'options' ] = apply_filters( 'ninja_forms_render_options', $settings[ 'options' ], $settings );
                    $settings[ 'options' ] = apply_filters( 'ninja_forms_render_options_' . $field_type, $settings[ 'options' ], $settings );
                }

                $default_value = ( isset( $settings[ 'default' ] ) ) ? $settings[ 'default' ] : null;
                $default_value = apply_filters('ninja_forms_render_default_value', $default_value, $field_type, $settings);
                if ( $default_value ) {

                    $default_value = preg_replace( '/{[^}]}/', '', $default_value );

                    if ($default_value) {
                        $settings['value'] = $default_value;

                        if( ! is_array( $default_value ) ) {
                            ob_start();
                            do_shortcode( $settings['value'] );
                            $ob = ob_get_clean();

                            if( ! $ob ) {
                                $settings['value'] = do_shortcode( $settings['value'] );
                            }
                        }
                    }
                }

                $thousands_sep = $wp_locale->number_format[ 'thousands_sep'];
                $decimal_point = $wp_locale->number_format[ 'decimal_point' ];
                $currencySymbol = Ninja_Forms()->get_setting('currency_symbol');

                $settings = static::ensureProductRelatedCostLocalizeSettings($settings,$decimal_point,$thousands_sep,$currencySymbol);
                
                $settings['element_templates'] = $templates;
                $settings['old_classname'] = $field_class->get_old_classname();
                $settings['wrap_template'] = $field_class->get_wrap_template();

                $settings['label']=\wp_kses_post(Sanitizer::preventScriptTriggerInHtmlOutput($settings['label']));
                
                $fields[] = apply_filters( 'ninja_forms_localize_field_settings_' . $field_type, $settings, $form );

                if( 'recaptcha' == $field[ 'settings' ][ 'type' ] ){
                    array_push( self::$form_uses_recaptcha, $form_id );
                }
                if( 'date' == $field[ 'settings' ][ 'type' ] || self::checkRepeaterChildType($field, 'date') ){
                    array_push( self::$form_uses_datepicker, $form_id );
                }
                if( 'starrating' == $field[ 'settings' ][ 'type' ] || self::checkRepeaterChildType($field, "starrating")){
                    array_push( self::$form_uses_starrating, $form_id );
                }
                if( isset( $field[ 'settings' ][ 'mask' ] ) && $field[ 'settings' ][ 'mask' ] || self::checkRepeaterChildSetting($field, "mask", null) ){
                    array_push( self::$form_uses_inputmask, $form_id );
                }
                if( isset( $field[ 'settings' ][ 'mask' ] ) && 'currency' == $field[ 'settings' ][ 'mask' ] || self::checkRepeaterChildSetting($field, "mask", "currency") ){
                    array_push( self::$form_uses_currencymask, $form_id );
                }
                if( isset( $field[ 'settings' ][ 'textarea_rte' ] ) && $field[ 'settings' ][ 'textarea_rte' ] || self::checkRepeaterChildSetting($field, "textarea_rte", null) ){
                    array_push( self::$form_uses_rte, $form_id );
                }
                if( isset( $field[ 'settings' ][ 'textarea_media' ] ) && $field[ 'settings' ][ 'textarea_media' ] || self::checkRepeaterChildSetting($field, "textarea_media", null) ){
                    array_push( self::$form_uses_textarea_media, $form_id );
                }
                // Check if field contains help text, this helps prevent jBox to be enqueued if there isn't a field using it.
                // strip all tags except image tags
                if( self::checkRepeaterChildSetting($field, "help_text", null) ||
                    isset( $field[ 'settings' ][ 'help_text' ] ) &&
                    strip_tags( $field[ 'settings' ][ 'help_text' ], '<img>'
                    ) ){
                    array_push( self::$form_uses_helptext, $form_id );
                }
            }
        }

        $fields = apply_filters( 'ninja_forms_display_fields', $fields, $form_id );

        if(!isset($_GET['nf_preview_form'])){
            /* Render Instance Fix */
            $instance_id = $form_id;
            if( ! isset(self::$render_instance_count[$form_id]) ) self::$render_instance_count[$form_id] = 0;
            if(self::$render_instance_count[$form_id]) {
                $instance_id .= '_' . self::$render_instance_count[$form_id];
                foreach( $fields as $id => $field ) {
                    $fields[$id]['id'] .= '_' . self::$render_instance_count[$form_id];
                }
            }
            self::$render_instance_count[$form_id]++;
            $form_id = $instance_id;
            /* END Render Instance Fix */
        }

        // Output Form Container
        do_action( 'ninja_forms_before_container', $form_id, $form->get_settings(), $form_fields );
        Ninja_Forms::template( 'display-form-container.html.php', compact( 'form_id' ) );

        $form_id = "$form_id";

        ?>
        <!-- That data is being printed as a workaround to page builders reordering the order of the scripts loaded-->
        <script>var formDisplay=1;var nfForms=nfForms||[];var form=[];form.id='<?php echo $form_id; ?>';form.settings=<?php echo wp_json_encode( $form->get_settings() ); ?>;form.fields=<?php echo wp_json_encode( $fields ); ?>;nfForms.push(form);</script>
        <?php

        self::enqueue_scripts( $form_id );
    }

    /**
     * Transform the inline JS into a variable passed to wp_localize_script
     *
     * @param array $fields
     * @param string $form_id
     * @param object $form
     * @return void
     * */
    protected static function transformInlineVars($fields, $form_id, $form_settings)
    {
        $thread_id = sprintf("%08x", abs(crc32($_SERVER['REMOTE_ADDR'] . $_SERVER['REQUEST_TIME'] . $_SERVER['REMOTE_PORT'])));
        if($thread_id !== self::$thread_id || isset( $_GET['nf_preview_form'] ) ){
            self::$recorded_forms = [];
        }
        self::$thread_id = $thread_id;
        $set_form = [];
        $set_form['id'] = $form_id;
        $set_form['settings'] = $form_settings;
        $set_form['fields'] = $fields;
        array_push(self::$recorded_forms, $set_form);
        self::$inline_vars_recorded = [
            "formDisplay"   =>  1,
            "form"          => $set_form,
            "nfForms"       => self::$recorded_forms,
        ];
    }
    /**
     * Ensure that product related costs on `localize` method have intended number format
     *
     * @param array $settings
     * @param string $decimal_point
     * @param string $thousands_sep
     * @return array
     */
    protected static function ensureProductRelatedCostLocalizeSettings(array $settings, $decimal_point,$thousands_sep, $currencySymbol): array
    {
        if ('shipping' == $settings['type']) {
            $settings['shipping_cost'] = preg_replace('/[^\d,\.]/', '', $settings['shipping_cost']);
            $settings['shipping_cost'] = str_replace($currencySymbol, '', $settings['shipping_cost']);

            $settings['shipping_cost'] = str_replace($decimal_point, '||', $settings['shipping_cost']);
            $settings['shipping_cost'] = str_replace($thousands_sep, '', $settings['shipping_cost']);
            $settings['shipping_cost'] = str_replace('||', '.', $settings['shipping_cost']);
        } elseif ('product' == $settings['type']) {
            $settings['product_price'] = preg_replace('/[^\d,\.]/', '', $settings['product_price']);
            $settings['product_price'] = str_replace($currencySymbol, '', $settings['product_price']);

            $settings['product_price'] = str_replace($decimal_point, '||', $settings['product_price']);
            $settings['product_price'] = str_replace($thousands_sep, '', $settings['product_price']);
            $settings['product_price'] = str_replace('||', '.', $settings['product_price']);
        } elseif ('total' == $settings['type'] && isset($settings['value'])) {
            if (empty($settings['value'])) $settings['value'] = 0;
            $settings['value'] = number_format((float)$settings['value'], 2);
        }
        return $settings;
    }

    /**
     * Construct field array for an unknown field type
     *
     * @param array $field
     * @return array
     */
    protected static function constructUnknownField( $field): array
    {
        $unknown_field = NF_Fields_Unknown::create( $field );

        $return = array(
            'settings' => $unknown_field->get_settings(),
            'id' => $unknown_field->get_id()
        );

        return $return;
    }

    /**
     * Apply localize filters to field
     *
     * Property types are not declared because we cannot guarantee what is
     * returned from apply_filters.
     * 
     * @param array $field
     * @param string $field_type
     * @return array
     */
    protected static function applyLocalizeFilters($field, $field_type)
    {
        $wip = apply_filters('ninja_forms_localize_fields', $field);

        $return = apply_filters('ninja_forms_localize_field_' . $field_type, $wip);

        return $return;
    }

    /**
     * Ensure that field array has proper construction after localize filters
     *
     * After any WP filter, we cannot assume that all properties are intact, so
     * ensure that our structure is valid.  Checks field type and calls method
     * that ensures validity of that field typ.
     *
     * @param array $field
     * @param array $fieldBeforeFilters
     * @return array
     */
    protected static function ensureFieldArrayStructureValidity($field, array $fieldBeforeFilters): array
    {
        // filter altered field beyond repair, fallback to before
        if(!is_array($field)){
            return $fieldBeforeFilters;
        }

        // initialize return value to incoming value
        $return = $field;

        if ('recaptcha' === $field['settings']['type']) {
            $return = self::ensureRecaptchaFieldStructureValidity($return);
        }

        return $return;
    }

    /**
     * Ensure that Recaptcha field array structure is correct
     *
     * @param array $field
     * @param array $fieldBeforeFilters
     * @return void
     */
    protected static function ensureRecaptchaFieldStructureValidity(array $field): array
    {
        // initialize return value to incoming value
        $return = $field;

        // Hide the label on invisible reCAPTCHA fields
        if (
            'recaptcha' === $field['settings']['type'] 
            && isset($field['settings']['size'])
            && 'invisible' === $field['settings']['size']) {

            $return['settings']['label_pos'] = 'hidden';
        }

        return $return;
    }

    /**
     * Determine currency symbol
     *
     * @param array $currencySymbolLookup Currency symbol lookups
     * @param string $currency
     * @return string
     */
    protected static function getCurrencySymbol($currencySymbolLookup, $currency): string
    {
        if(!is_string($currency)){
            return '';
        }
        
        $return = isset( $currencySymbolLookup[ $currency ] ) ? $currencySymbolLookup[ $currency ] : '';

        return $return;
    }

    public static function checkRepeaterChildType($field, $type)
    {
        $return = [];
        if($field["settings"]["type"] === "repeater" && !empty($field["settings"]["fields"])){
            foreach($field["settings"]["fields"] as $child){
                array_push( $return, isset( $child[ 'type' ] ) &&  $type === $child[ 'type' ] );
            }
        }
        return in_array(true, $return, true);
    }

    public static function checkRepeaterChildSetting($field, $setting, $value)
    {
        $return = [];
        if($field["settings"]["type"] === "repeater" && !empty($field["settings"]["fields"])){
            foreach($field["settings"]["fields"] as $child){
                if( $value !== null ){
                    array_push( $return, isset( $child[ $setting ] ) && $value === $child[ $setting ] );
                } else {
                    array_push( $return, isset( $child[ $setting ] ) && $child[ $setting ] );
                }
                
            }
        }
        return in_array(true, $return, true);
    }

    public static function localize_preview( $form_id )
    {
        $capability = apply_filters( 'ninja_forms_display_test_values_capabilities', 'read' );
        if( isset( $_GET[ 'ninja_forms_test_values' ] ) && current_user_can( $capability ) ){
            self::$use_test_values = TRUE;
        }

        add_action( 'wp_footer', 'NF_Display_Render::output_templates', 9999 );

        $form = get_user_option( 'nf_form_preview_' . $form_id );

        if( ! $form ){
            self::localize( $form_id );
            return;
        }

        if( isset( $form[ 'settings' ][ 'logged_in' ] ) && $form[ 'settings' ][ 'logged_in' ] && ! is_user_logged_in() ){
            echo do_shortcode( $form[ 'settings' ][ 'not_logged_in_msg' ]);
            return;
        }

        $form[ 'settings' ] = array_merge( Ninja_Forms::config( 'i18nFrontEnd' ), $form[ 'settings' ] );
        $form[ 'settings' ] = apply_filters( 'ninja_forms_display_form_settings', $form[ 'settings' ], $form_id );

        // Remove the embed_form setting to avoid pagebuilder conflicts.
        $form[ 'settings' ][ 'embed_form' ] = '';

        $form[ 'settings' ][ 'is_preview' ] = TRUE;

        $currency = ( isset( $form[ 'settings' ][ 'currency' ] ) && $form[ 'settings' ][ 'currency' ] ) ? $form[ 'settings' ][ 'currency' ] : Ninja_Forms()->get_setting( 'currency' ) ;
        $currencySymbolLookup = Ninja_Forms::config( 'CurrencySymbol' );
        $currencySymbol =  ( isset( $currencySymbolLookup[ $currency ] ) ) ? $currencySymbolLookup[ $currency ] : '';

        $form[ 'settings' ][ 'currency_symbol' ] =$currencySymbol;

        $before_form = apply_filters( 'ninja_forms_display_before_form', '', $form_id, TRUE );
        $form[ 'settings' ][ 'beforeForm'] = $before_form;

        $before_fields = apply_filters( 'ninja_forms_display_before_fields', '', $form_id, TRUE );
        $form[ 'settings' ][ 'beforeFields'] = $before_fields;

        $after_fields = apply_filters( 'ninja_forms_display_after_fields', '', $form_id, TRUE );
        $form[ 'settings' ][ 'afterFields'] = $after_fields;

        $after_form = apply_filters( 'ninja_forms_display_after_form', '', $form_id, TRUE );
        $form[ 'settings' ][ 'afterForm'] = $after_form;

        $fields = array();

        if( empty( $form['fields'] ) ){
            echo esc_html__( 'No Fields Found.', 'ninja-forms' );
        } else {
            foreach ($form['fields'] as $field_id => $field) {

                $field_type = $field['settings']['type'];
                // Scrub any values that might be stored in data. Defaults will set these later.
                $field['settings']['value'] = '';

                if( ! isset( Ninja_Forms()->fields[ $field_type ] ) ) continue;
                if( ! apply_filters( 'ninja_forms_preview_display_type_' . $field_type, TRUE ) ) continue;
                if( ! apply_filters( 'ninja_forms_preview_display_field', $field ) ) continue;

                $field['settings']['id'] = $field_id;

                $field = apply_filters('ninja_forms_localize_fields_preview', $field);
                $field = apply_filters('ninja_forms_localize_field_' . $field_type . '_preview', $field);

                $display_before = apply_filters( 'ninja_forms_display_before_field_type_' . $field['settings'][ 'type' ], '' );
                $display_before = apply_filters( 'ninja_forms_display_before_field_key_' . $field['settings'][ 'key' ], $display_before );
                $field['settings'][ 'beforeField' ] = $display_before;

                $display_after = apply_filters( 'ninja_forms_display_after_field_type_' . $field['settings'][ 'type' ], '' );
                $display_after = apply_filters( 'ninja_forms_display_after_field_key_' . $field['settings'][ 'key' ], $display_after );
                $field['settings'][ 'afterField' ] = $display_after;

                foreach ($field['settings'] as $key => $setting) {
                    if (is_numeric($setting)) $field['settings'][$key] = floatval($setting);
                }

                if( ! isset( $field['settings'][ 'label_pos' ] ) || 'default' == $field['settings'][ 'label_pos' ] ){
                    if( isset( $form[ 'settings' ][ 'default_label_pos' ] ) ) {
                        $field['settings'][ 'label_pos' ] = $form[ 'settings' ][ 'default_label_pos' ];
                    }
                }

                $field_class = Ninja_Forms()->fields[$field_type];

                $templates = $field_class->get_templates();

                if (!array($templates)) {
                    $templates = array($templates);
                }

                foreach ($templates as $template) {
                    self::load_template('fields-' . $template);
                }

                if (self::$use_test_values) {
                    $field['settings']['value'] = $field_class->get_test_value();
                }

                $field[ 'settings' ][ 'parentType' ] = $field_class->get_parent_type();

                if( 'list' == $field[ 'settings' ][ 'parentType' ] && isset( $field['settings'][ 'options' ] ) && is_array( $field['settings'][ 'options' ] ) ){
                    $field['settings'][ 'options' ] = apply_filters( 'ninja_forms_render_options', $field['settings'][ 'options' ], $field['settings'] );
                    $field['settings'][ 'options' ] = apply_filters( 'ninja_forms_render_options_' . $field['settings'][ 'type' ], $field['settings'][ 'options' ], $field['settings'] );
                }

                $default_value = ( isset( $field[ 'settings' ][ 'default' ] ) ) ? $field[ 'settings' ][ 'default' ] : null;
                $default_value = apply_filters( 'ninja_forms_render_default_value', $default_value, $field_type, $field[ 'settings' ]);
                if( $default_value ){

                    $default_value = preg_replace( '/{.*}/', '', $default_value );

                    if ($default_value) {
                        $field['settings']['value'] = $default_value;

                        if( ! is_array( $default_value ) ) {
                            ob_start();
                            do_shortcode( $field['settings']['value'] );
                            $ob = ob_get_clean();

                            if( ! $ob ) {
                                $field['settings']['value'] = do_shortcode( $field['settings']['value'] );
                            }
                        }
                    }
                }

                $fieldType = $field['settings']['type'];

                if(in_array($fieldType,['shipping','product','total'])){
                    $field = self::ensureProductRelatedCostPreviewFormats($field, $currencySymbol);
                }

                $field['settings']['element_templates'] = $templates;
                $field['settings']['old_classname'] = $field_class->get_old_classname();
                $field['settings']['wrap_template'] = $field_class->get_wrap_template();

                $fields[] = apply_filters( 'ninja_forms_localize_field_settings_' . $field_type, $field['settings'], $form );
            }
        }

        // Output Form Container
        do_action( 'ninja_forms_before_container_preview', $form_id, $form[ 'settings' ], $fields );
        Ninja_Forms::template( 'display-form-container.html.php', compact( 'form_id' ) );

        self::transformInlineVars($fields, $form_id, $form[ 'settings' ]);

        self::enqueue_scripts( $form_id, true );
    }

    /**
     * Set root element that will insert the WP element
     * 
     * @since 3.7.4
     * 
     * @param string Form ID
     * 
     * @return void
     */
    public static function localize_iframe( $form_id )
    {
        //Render root div
        echo "<div id='nf_form_iframe_" . (int)$form_id . "'></div>";
        //Enqueue WP element
       static::enqueue_iframe_scripts( $form_id );

    }

    /**
     * Enqueue scripts and localize data needed to insert the iFrame
     * 
     * @since 3.7.4
     * 
     * @param string Form ID
     * 
     * @return void
     */
    public static function enqueue_iframe_scripts( $form_id ) {
         //Get Dependencies and Version from build asset.php generated by wp-scripts
         $dashboard_asset_php = [
            "dependencies" => [],
            "version"   => false
        ];
        if( file_exists( Ninja_Forms::$dir . "build/displayFrame.asset.php" ) ){
            $asset_php = include( Ninja_Forms::$dir . "build/displayFrame.asset.php" );
            $dashboard_asset_php["dependencies"] = array_merge( $dashboard_asset_php["dependencies"], $asset_php["dependencies"]);
            $dashboard_asset_php["version"] = $asset_php["version"];
        }
         //Register displayFrame script
         wp_register_script( 'ninja_forms_form_iframe', Ninja_Forms::$url . 'build/displayFrame.js',  $dashboard_asset_php["dependencies"], $dashboard_asset_php["version"], false );
         wp_enqueue_script( 'ninja_forms_form_iframe' );

         //Set parameters needed in the script
         wp_localize_script('ninja_forms_form_iframe', 'ninja_forms_form_iframe_data', [
            'formID'        =>  $form_id,
            'homeUrl'       => esc_url_raw( home_url() ),
            'previewToken'  => wp_create_nonce('nf_iframe' ),
            'isBlock'       => false
         ]);
    }

    protected static function ensureProductRelatedCostPreviewFormats(array $field, string $currencySymbol): array
    {
        // TODO: Find a better way to do this.
        if ('shipping' == $field['settings']['type']) {
            $field['settings']['shipping_cost'] = static::decodeNumberByLocale($field['settings']['shipping_cost']);
            $field['settings']['shipping_cost'] = str_replace($currencySymbol, '', $field['settings']['shipping_cost']);
            $field['settings']['shipping_cost'] = number_format((float)$field['settings']['shipping_cost'], 2);
        } elseif ('product' == $field['settings']['type']) {
            // TODO: Does the currency marker need to stripped here?
            $field['settings']['product_price'] =  static::decodeNumberByLocale($field['settings']['product_price']);
            $field['settings']['product_price'] = (float)str_replace($currencySymbol, '', $field['settings']['product_price']);
            $field['settings']['product_price'] = number_format((float)$field['settings']['product_price'], 2);
        } elseif ('total' == $field['settings']['type']) {
            
            if (!isset($field['settings']['value'])) $field['settings']['value'] = 0;
            $field['settings']['value'] = number_format((float)$field['settings']['value'], 2);
        }

        return $field;
    }

    /**
     * Decode a number by locale into string
     *
     * @return array
     */
    protected static function decodeNumberByLocale( $incoming ): string
    {
        $localeNumberFormatting= NF_Handlers_LocaleNumberFormatting::create();
        $return = $localeNumberFormatting->locale_decode_number($incoming);
        return $return;
    }


    public static function enqueue_scripts( $form_id, $is_preview = false )
    {
        global $wp_locale;

        $ver     = Ninja_Forms::VERSION;
        $js_dir  = Ninja_Forms::$url . 'assets/js/min/';
        $css_dir = Ninja_Forms::$url . 'assets/css/';

        self::enqueue_styles_display( $css_dir );

        if( $is_preview || in_array( $form_id, self::$form_uses_recaptcha ) ) {
            $recaptcha_lang = Ninja_Forms()->get_setting('recaptcha_lang');
            wp_enqueue_script('nf-google-recaptcha', 'https://www.google.com/recaptcha/api.js?hl=' . $recaptcha_lang . '&onload=nfRenderRecaptcha&render=explicit', array( 'jquery', 'nf-front-end-deps' ), $ver, TRUE );
        }

        if( $is_preview || in_array( $form_id, self::$form_uses_datepicker ) ) {
            wp_enqueue_style( 'nf-flatpickr', $css_dir . 'flatpickr.css', $ver );
            wp_enqueue_script('nf-datepicker', $js_dir . 'datepicker.min.js', array( 'jquery', 'nf-front-end' ), $ver );
        }

        if( $is_preview || in_array( $form_id, self::$form_uses_inputmask ) ) {
            wp_enqueue_script('nf-front-end--inputmask', $js_dir . 'front-end--inputmask.min.js', array( 'jquery' ), $ver );
        }

        if( $is_preview || in_array( $form_id, self::$form_uses_currencymask ) ) {
            wp_enqueue_script('nf-front-end--currencymask', $js_dir . 'autonumeric.min.js', array( 'jquery' ), $ver );
        }

        if( $is_preview || in_array( $form_id, self::$form_uses_rte ) ) {
            if( $is_preview || in_array( $form_id, self::$form_uses_textarea_media ) ) {
                wp_enqueue_media();
            }

            wp_enqueue_style( 'summernote',         $css_dir . 'summernote.css'   , $ver );
            wp_enqueue_style( 'codemirror',         $css_dir . 'codemirror.css'   , $ver );
            wp_enqueue_style( 'codemirror-monokai', $css_dir . 'monokai-theme.css', $ver );
            wp_enqueue_script('nf-front-end--rte', $js_dir . 'front-end--rte.min.js', array( 'jquery' ), $ver );
        }

        if( $is_preview || in_array( $form_id, self::$form_uses_helptext ) ) {
            wp_enqueue_style( 'jBox', $css_dir . 'jBox.css', $ver );
            wp_enqueue_script('nf-jBox', $js_dir . 'jBox.min.js', array( 'jquery' ), $ver );
        }

        if( $is_preview || in_array( $form_id, self::$form_uses_starrating ) ) {
            wp_enqueue_style( 'rating', $css_dir . 'rating.css', Ninja_Forms::VERSION );
            wp_enqueue_script('nf-front-end--starrating', $js_dir . 'front-end--starrating.min.js', array( 'jquery' ), $ver );
        }

        wp_enqueue_script( 'nf-front-end-deps', $js_dir . 'front-end-deps.js', array( 'jquery', 'backbone' ), $ver );
        wp_enqueue_script( 'nf-front-end',      $js_dir . 'front-end.js',      array( 'nf-front-end-deps'  ), $ver );

        wp_localize_script( 'nf-front-end', 'nfi18n', Ninja_Forms::config( 'i18nFrontEnd' ) );

        $data = apply_filters( 'ninja_forms_render_localize_script_data', array(
            'adminAjax' => admin_url( 'admin-ajax.php' ),
            'ajaxNonce' => wp_create_nonce( 'ninja_forms_display_nonce' ),
            'requireBaseUrl' => Ninja_Forms::$url . 'assets/js/',
            'use_merge_tags' => array(),
            'opinionated_styles' => Ninja_Forms()->get_setting( 'opinionated_styles' ),
            'filter_esc_status'  =>    json_encode( WPN_Helper::maybe_disallow_unfiltered_html_for_escaping() ),
            'nf_consent_status_response'    => []
        ));

        foreach( Ninja_Forms()->fields as $field ){
            foreach( $field->use_merge_tags() as $merge_tag ){
                $data[ 'use_merge_tags' ][ $merge_tag ][ $field->get_type() ] = $field->get_type();
            }
        }

        wp_localize_script( 'nf-front-end', 'nfFrontEnd', $data );

        // !!Todoed!! moved inline JS to data
        wp_localize_script( 'nf-front-end', 'nfInlineVars', self::$inline_vars_recorded );

        do_action( 'ninja_forms_enqueue_scripts', array( 'form_id' => $form_id ) );

        do_action( 'nf_display_enqueue_scripts' );
    }

    /**
     * Enqueue NF frontend basic display styles.
     *
     * @param string $css_dir
     */
    public static function enqueue_styles_display( $css_dir ) {
        switch( Ninja_Forms()->get_setting( 'opinionated_styles' ) ) {
            case 'light':
                wp_enqueue_style( 'nf-display',      $css_dir . 'display-opinions-light.css', array( 'dashicons' ) );
                wp_enqueue_style( 'nf-font-awesome', $css_dir . 'font-awesome.min.css'       );
                break;
            case 'dark':
                wp_enqueue_style( 'nf-display',      $css_dir . 'display-opinions-dark.css', array( 'dashicons' )  );
                wp_enqueue_style( 'nf-font-awesome', $css_dir . 'font-awesome.min.css'      );
                break;
            default:
                wp_enqueue_style( 'nf-display',      $css_dir . 'display-structure.css', array( 'dashicons' ) );
        }
    }

    public static function load_template( $file_name = '' )
    {
        if( ! $file_name ) return;

        if( self::is_template_loaded( $file_name ) ) return;

        self::$loaded_templates[] = $file_name;
    }

    public static function output_templates()
    {
        // Build File Path Hierarchy
        $file_paths = apply_filters( 'ninja_forms_field_template_file_paths', array(
            get_stylesheet_directory() . '/ninja-forms/templates/',
        ));

        $file_paths[] = Ninja_Forms::$dir . 'includes/Templates/';

        // Search for and Output File Templates
        foreach( self::$loaded_templates as $file_name ) {

            foreach( $file_paths as $path ){

                if( file_exists( $path . "$file_name.html" ) ){
                    echo file_get_contents( $path . "$file_name.html" );
                    break;
                }
            }
        }

        // Action to Output Custom Templates
        do_action( 'ninja_forms_output_templates' );
    }

    /*
     * UTILITY
     */

    protected static function is_template_loaded( $template_name )
    {
        return ( in_array( $template_name, self::$loaded_templates ) ) ? TRUE : FALSE ;
    }

} // End Class NF_Display_Render
