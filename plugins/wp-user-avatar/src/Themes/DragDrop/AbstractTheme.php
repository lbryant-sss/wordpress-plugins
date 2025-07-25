<?php

namespace ProfilePress\Core\Themes\DragDrop;

use ProfilePress\Core\Base;
use ProfilePress\Core\Classes\ExtensionManager as EM;
use ProfilePress\Core\Classes\FormRepository as FR;

abstract class AbstractTheme implements ThemeInterface
{
    public int $form_id;

    public string $form_type;

    public string $tag_name;

    public string $asset_image_url;

    public function __construct($form_id, $form_type)
    {
        $this->form_id   = absint($form_id);
        $this->form_type = $form_type;

        $this->asset_image_url = PPRESS_ASSETS_URL . '/images';

        $this->tag_name = 'login';

        switch ($this->form_type) {
            case FR::REGISTRATION_TYPE:
                $this->tag_name = 'reg';
                break;
            case FR::EDIT_PROFILE_TYPE:
                $this->tag_name = 'edit-profile';
                break;
            case FR::PASSWORD_RESET_TYPE:
                $this->tag_name = 'reset';
                break;
        }

        add_shortcode('pp-form-wrapper', [$this, 'form_wrapper_shortcode']);

        add_filter('ppress_form_builder_meta_box_settings', [$this, 'metabox_settings'], 10, 3);

        add_filter('ppress_form_builder_meta_box_submit_button_settings', [$this, 'submit_button_settings']);

        add_filter('ppress_form_builder_meta_box_appearance_settings', [$this, 'appearance_settings']);

        add_filter('ppress_form_builder_meta_box_colors_settings', [$this, 'color_settings']);

        // add div wrapper to remember me checkbox
        add_filter('ppress_form_field_listing_login-remember', [$this, 'remember_me_checkbox_wrapper'], 10, 4);
        // when label is found in field settings, it is automatically added before the field so we are removing it.
        add_filter('ppress_form_field_listing_setting_login-remember', [$this, 'remember_me_checkbox_remove_label']);
    }

    public function is_show_social_login()
    {
        return in_array($this->form_type, [FR::LOGIN_TYPE, FR::REGISTRATION_TYPE]) && EM::is_enabled(EM::SOCIAL_LOGIN);
    }

    /**
     * Array of fields whose settings in form builder should not be modified. Should be as is.
     */
    public function disallowed_settings_fields()
    {
        return ['pp-custom-html', 'pp-recaptcha', 'pp-turnstile', 'pp-user-avatar', 'pp-user-cover-image', $this->tag_name . '-cpf-agreeable'];
    }

    public function minified_form_css()
    {
        return ppress_minify_css($this->form_css());
    }

    public function get_meta($key)
    {
        $metabox_settings = FR::get_form_meta($this->form_id, $this->form_type, FR::METABOX_FORM_BUILDER_SETTINGS);

        if (empty($metabox_settings)) $metabox_settings = [];

        $default_metabox_settings = $this->default_metabox_settings();

        $val = $metabox_settings[$key] ?? ($default_metabox_settings[$key] ?? '');

        if (is_array($val) && ! empty($val)) {
            $val = array_filter($val);
        }

        return $val;
    }

    public function remember_me_checkbox_remove_label($field_setting)
    {
        unset($field_setting['label']);

        return $field_setting;
    }

    public function remember_me_checkbox_wrapper($tag, $field_setting, $form_id, $form_type)
    {
        if ($form_type == FR::LOGIN_TYPE) {

            static $flag = [];

            if ( ! isset($flag[$form_id . '_' . $form_type])) {

                $flag[$form_id . '_' . $form_type] = true;

                return sprintf(
                    '<div class="ppform-remember-me"><label class="ppform-remember-checkbox">%s <span class="ppform-remember-label">%s</span></label></div>',
                    $tag, esc_html($field_setting['label'])
                );
            }
        }

        return $tag;
    }

    public function default_metabox_settings()
    {
        $button_text     = esc_html__('Log In', 'wp-user-avatar');
        $success_message = '';

        switch ($this->form_type) {
            case FR::REGISTRATION_TYPE:
                $button_text     = esc_html__('Register', 'wp-user-avatar');
                $success_message = esc_html__('Registration successful.', 'wp-user-avatar');
                break;
            case FR::EDIT_PROFILE_TYPE:
                $button_text     = esc_html__('Update Profile', 'wp-user-avatar');
                $success_message = esc_html__('Changes saved.', 'wp-user-avatar');
                break;
            case FR::PASSWORD_RESET_TYPE:
                $button_text     = esc_html__('Reset Password', 'wp-user-avatar');
                $success_message = esc_html__('Check your email for further instructions.', 'wp-user-avatar');
                break;
        }

        return [
            'submit_button_text'             => $button_text,
            'submit_button_processing_label' => esc_html__('Processing', 'wp-user-avatar'),
            FR::SUCCESS_MESSAGE              => $success_message,
            FR::REGISTRATION_USER_ROLE       => 'subscriber'
        ];
    }

    public function metabox_settings($settings, $form_type, $DragDropBuilderInstance)
    {
        return $settings;
    }

    public function submit_button_settings($settings)
    {
        return $settings;
    }

    public function appearance_settings($settings)
    {
        return $settings;
    }

    public function color_settings($settings)
    {
        return $settings;
    }

    /**
     * Each fields default values.
     *
     * @return array
     */
    public function default_fields_settings()
    {
        $defaults = [
            $this->tag_name . '-username'         => [
                'placeholder' => esc_html__('Username', 'wp-user-avatar')
            ],
            $this->tag_name . '-email'            => [
                'placeholder' => esc_html__('Email Address', 'wp-user-avatar')
            ],
            $this->tag_name . '-password'         => [
                'placeholder' => esc_html__('Password', 'wp-user-avatar')
            ],
            $this->tag_name . '-confirm-password' => [
                'placeholder' => esc_html__('Confirm Password', 'wp-user-avatar')
            ],
            $this->tag_name . '-confirm-email'    => [
                'placeholder' => esc_html__('Confirm Email', 'wp-user-avatar')
            ],
            $this->tag_name . '-website'          => [
                'placeholder' => esc_html__('Website', 'wp-user-avatar')
            ],
            $this->tag_name . '-nickname'         => [
                'placeholder' => esc_html__('Nickname', 'wp-user-avatar')
            ],
            $this->tag_name . '-display-name'     => [
                'placeholder' => esc_html__('Display Name', 'wp-user-avatar')
            ],
            $this->tag_name . '-first-name'       => [
                'placeholder' => esc_html__('First Name', 'wp-user-avatar')
            ],
            $this->tag_name . '-last-name'        => [
                'placeholder' => esc_html__('Last Name', 'wp-user-avatar')
            ],
            $this->tag_name . '-bio'              => [
                'placeholder' => esc_html__('Biography', 'wp-user-avatar')
            ],
            $this->tag_name . '-avatar'           => [],
            $this->tag_name . '-password-meter'   => [
                'enforce' => true
            ],
            $this->tag_name . '-select-role'      => ['options' => ''],

            // edit profile only
            'pp-user-avatar'                      => ['size' => 300],

            // login form
            'login-username'                      => [
                'placeholder' => esc_html__('Username or Email', 'wp-user-avatar')
            ],
            'login-password'                      => [
                'placeholder' => esc_html__('Password', 'wp-user-avatar')
            ],
            'login-remember'                      => [
                'label' => esc_html__('Remember Me', 'wp-user-avatar')
            ],

            // password reset
            'user-login'                          => [
                'placeholder' => esc_html__('Username or Email', 'wp-user-avatar')
            ],

            // user profile
            'profile-username'                    => [
                'label' => esc_html__('Username', 'wp-user-avatar')
            ],
            'profile-email'                       => [
                'label' => esc_html__('Email Address', 'wp-user-avatar')
            ],
            'profile-first-name'                  => [
                'label' => esc_html__('First Name', 'wp-user-avatar')
            ],
            'profile-last-name'                   => [
                'label' => esc_html__('Last Name', 'wp-user-avatar')
            ],
            'profile-website'                     => [
                'label' => esc_html__('Website', 'wp-user-avatar')
            ],
            'profile-bio'                         => [
                'label' => esc_html__('Bio', 'wp-user-avatar')
            ],
        ];

        if ($this->form_type == FR::MEMBERS_DIRECTORY_TYPE) {
            // user profile
            $defaults['profile-username']   = [];
            $defaults['profile-email']      = [];
            $defaults['profile-first-name'] = [];
            $defaults['profile-last-name']  = [];
            $defaults['profile-website']    = [];
            $defaults['profile-bio']        = [];
        }

        return apply_filters('ppress_form_default_fields_settings', $defaults, $this);
    }

    public function form_wrapper_shortcode($atts, $content)
    {
        $form_id   = $this->form_id;
        $form_type = $this->form_type;

        $atts = shortcode_atts(['class' => '', 'style' => ''], $atts);

        $classes = ['pp-form-wrapper', "pp-$form_type", "pp-$form_type-$form_id"];
        if (isset($atts['class']) && ! empty($atts['class'])) {
            $classes[] = esc_attr($atts['class']);
        }

        return sprintf(
            '<div id="%s" class="%s"%s>%s</div>',
            "pp-$form_type-$form_id",
            implode(' ', $classes),
            ! empty($atts['style']) ? ' style="' . esc_attr($atts['style']) . '"' : '',
            do_shortcode($content)
        );
    }

    public function field_listing()
    {
        return (new FieldListing($this->form_id, $this->form_type))->defaults($this->default_fields_settings())->forge();
    }

    /**
     * @return ProfileFieldListing
     */
    public function profile_listing()
    {
        return (new ProfileFieldListing($this->form_id))->defaults($this->default_fields_settings());
    }

    public function get_profile_field($field_key, $parse_shortcode = false)
    {
        if (empty($field_key)) return '';

        $return = sprintf('[profile-cpf key=%s]', $field_key);

        $standard_fields = array_keys(ppress_standard_fields_key_value_pair(true));

        if (in_array($field_key, $standard_fields)) {

            if ($field_key == 'first_last_names') {
                $return = '[profile-first-name] [profile-last-name]';
            } elseif ($field_key == 'last_first_names') {
                $return = '[profile-last-name] [profile-first-name]';
            } elseif ($field_key == 'registration_date') {
                $return = '[profile-date-registered]';
            } else {
                $return = sprintf('[profile-%s]', $field_key);
            }
        }

        return $parse_shortcode === true ? do_shortcode($return, true) : $return;
    }

    public function form_submit_button()
    {
        $submit_button_text = $this->get_meta('submit_button_text');
        $processing_label   = $this->get_meta('submit_button_processing_label');

        return sprintf(
            '[%s-submit class="ppform-submit-button" value="%s" processing_label="%s"]',
            $this->tag_name, $submit_button_text, $processing_label
        );
    }

    public function social_profile_icons()
    {
        if ( ! EM::is_enabled(EM::CUSTOM_FIELDS)) return false;

        $facebook_url  = $this->get_profile_field(Base::cif_facebook, true);
        $twitter_url   = $this->get_profile_field(Base::cif_twitter, true);
        $linkedin_url  = $this->get_profile_field(Base::cif_linkedin, true);
        $github_url    = $this->get_profile_field(Base::cif_github, true);
        $instagram_url = $this->get_profile_field(Base::cif_instagram, true);
        $youtube_url   = $this->get_profile_field(Base::cif_youtube, true);
        $vk_url        = $this->get_profile_field(Base::cif_vk, true);
        $pinterest_url = $this->get_profile_field(Base::cif_pinterest, true);
        $bluesky_url   = $this->get_profile_field(Base::cif_bluesky, true);
        $threads_url   = $this->get_profile_field(Base::cif_threads, true);

        if (
            empty($facebook_url) &&
            empty($twitter_url) &&
            empty($linkedin_url) &&
            empty($github_url) &&
            empty($instagram_url) &&
            empty($youtube_url) &&
            empty($pinterest_url) &&
            empty($bluesky_url) &&
            empty($threads_url) &&
            empty($vk_url)) {
            return false;
        }

        ob_start();
        ?>
        <div class="ppress-pf-profile-connect">

            <?php if ( ! empty($facebook_url)) :  // ignore_html set to true to quicken the parsing ?>
                <a href="<?= $facebook_url ?>" target="_blank" class="ppress-pf-social-icon dpf-facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="48" height="48" viewBox="0 0 48 48" style=" fill:#000000;">
                        <path fill="#039be5" d="M24 5A19 19 0 1 0 24 43A19 19 0 1 0 24 5Z"></path>
                        <path fill="#fff" d="M26.572,29.036h4.917l0.772-4.995h-5.69v-2.73c0-2.075,0.678-3.915,2.619-3.915h3.119v-4.359c-0.548-0.074-1.707-0.236-3.897-0.236c-4.573,0-7.254,2.415-7.254,7.917v3.323h-4.701v4.995h4.701v13.729C22.089,42.905,23.032,43,24,43c0.875,0,1.729-0.08,2.572-0.194V29.036z"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ( ! empty($twitter_url)) : ?>
                <a href="<?= $twitter_url ?>" target="_blank" class="ppress-pf-social-icon dpf-twitter">
                    <svg style="width: 34px;height: 34px;" fill-rule="evenodd" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                        <path d="m256 0c141.385 0 256 114.615 256 256s-114.615 256-256 256-256-114.615-256-256 114.615-256 256-256z"/>
                        <path d="m318.64 157.549h33.401l-72.973 83.407 85.85 113.495h-67.222l-52.647-68.836-60.242 68.836h-33.423l78.052-89.212-82.354-107.69h68.924l47.59 62.917zm-11.724 176.908h18.51l-119.476-157.964h-19.86z" fill="#fff" fill-rule="nonzero"/>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ( ! empty($linkedin_url)) : ?>
                <a href="<?= $linkedin_url ?>" target="_blank" class="ppress-pf-social-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                         width="48" height="48"
                         viewBox="0 0 48 48"
                         style="fill:#000000;">
                        <path fill="#0288d1" d="M24 4A20 20 0 1 0 24 44A20 20 0 1 0 24 4Z"></path>
                        <path fill="#fff" d="M14 19H18V34H14zM15.988 17h-.022C14.772 17 14 16.11 14 14.999 14 13.864 14.796 13 16.011 13c1.217 0 1.966.864 1.989 1.999C18 16.11 17.228 17 15.988 17zM35 24.5c0-3.038-2.462-5.5-5.5-5.5-1.862 0-3.505.928-4.5 2.344V19h-4v15h4v-8c0-1.657 1.343-3 3-3s3 1.343 3 3v8h4C35 34 35 24.921 35 24.5z"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ( ! empty($instagram_url)) : ?>
                <a href="<?= $instagram_url ?>" target="_blank" class="ppress-pf-social-icon dpf-instagram">
                    <svg viewBox="0 0 128 128" width="35px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g>
                            <linearGradient gradientTransform="matrix(1 0 0 -1 594 633)" gradientUnits="userSpaceOnUse" id="SVGID_1_" x1="-566.7114" x2="-493.2875" y1="516.5693" y2="621.4296">
                                <stop offset="0" style="stop-color:#FFB900"/>
                                <stop offset="1" style="stop-color:#9100EB"/>
                            </linearGradient>
                            <circle cx="64" cy="64" fill="url(#SVGID_1_)" r="64"/>
                        </g>
                        <g>
                            <g>
                                <path d="M82.333,104H45.667C33.72,104,24,94.281,24,82.333V45.667C24,33.719,33.72,24,45.667,24h36.666    C94.281,24,104,33.719,104,45.667v36.667C104,94.281,94.281,104,82.333,104z M45.667,30.667c-8.271,0-15,6.729-15,15v36.667    c0,8.271,6.729,15,15,15h36.666c8.271,0,15-6.729,15-15V45.667c0-8.271-6.729-15-15-15H45.667z" fill="#FFFFFF"/>
                            </g>
                            <g>
                                <path d="M64,84c-11.028,0-20-8.973-20-20c0-11.029,8.972-20,20-20s20,8.971,20,20C84,75.027,75.028,84,64,84z     M64,50.667c-7.352,0-13.333,5.981-13.333,13.333c0,7.353,5.981,13.333,13.333,13.333S77.333,71.353,77.333,64    C77.333,56.648,71.353,50.667,64,50.667z" fill="#FFFFFF"/>
                            </g>
                            <g>
                                <circle cx="85.25" cy="42.75" fill="#FFFFFF" r="4.583"/>
                            </g>
                        </g>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ( ! empty($pinterest_url)) : ?>
                <a href="<?= $pinterest_url ?>" target="_blank" class="ppress-pf-social-icon dpf-pinterest">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="35px" xml:space="preserve">
<circle cx="24" cy="24" r="20" fill="#E60023"/>
                        <path fill="#FFF" d="M24.4439087,11.4161377c-8.6323242,0-13.2153931,5.7946167-13.2153931,12.1030884	c0,2.9338379,1.5615234,6.5853882,4.0599976,7.7484131c0.378418,0.1762085,0.581543,0.1000366,0.668457-0.2669067	c0.0668945-0.2784424,0.4038086-1.6369019,0.5553589-2.2684326c0.0484619-0.2015381,0.0246582-0.3746338-0.1384277-0.5731201	c-0.8269653-1.0030518-1.4884644-2.8461304-1.4884644-4.5645752c0-4.4115601,3.3399658-8.6799927,9.0299683-8.6799927	c4.9130859,0,8.3530884,3.3484497,8.3530884,8.1369019c0,5.4099731-2.7322998,9.1584473-6.2869263,9.1584473	c-1.9630737,0-3.4330444-1.6238403-2.9615479-3.6153564c0.5654297-2.3769531,1.6569214-4.9415283,1.6569214-6.6584473	c0-1.5354004-0.8230591-2.8169556-2.5299683-2.8169556c-2.006958,0-3.6184692,2.0753784-3.6184692,4.8569336	c0,1.7700195,0.5984497,2.9684448,0.5984497,2.9684448s-1.9822998,8.3815308-2.3453979,9.9415283	c-0.4019775,1.72229-0.2453003,4.1416016-0.0713501,5.7233887l0,0c0.4511108,0.1768799,0.9024048,0.3537598,1.3687744,0.4981079l0,0	c0.8168945-1.3278198,2.0349731-3.5056763,2.4864502-5.2422485c0.2438354-0.9361572,1.2468872-4.7546387,1.2468872-4.7546387	c0.6515503,1.2438965,2.5561523,2.296936,4.5831299,2.296936c6.0314941,0,10.378418-5.546936,10.378418-12.4400024	C36.7738647,16.3591919,31.3823242,11.4161377,24.4439087,11.4161377z"/></svg>
                </a>
            <?php endif; ?>

            <?php if ( ! empty($bluesky_url)) : ?>
                <a href="<?= $bluesky_url ?>" target="_blank" class="ppress-pf-social-icon dpf-bluesky">
                    <svg style="width: 34px;height: 34px;" fill-rule="evenodd" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="256" cy="256" r="256" fill="#2690FE"/>
                        <path d="M193.688 172.036C218.909 190.972 246.042 229.367 256 249.969C265.963 229.367 293.091 190.972 318.312 172.036C336.515 158.375 366 147.805 366 181.444C366 188.164 362.15 237.879 359.887 245.951C352.035 274.012 323.42 281.172 297.968 276.84C342.46 284.409 353.78 309.494 329.333 334.58C282.908 382.215 262.61 322.626 257.404 307.357C256.45 304.56 256.005 303.251 256 304.361C255.995 303.245 255.55 304.555 254.596 307.357C249.39 322.626 229.092 382.215 182.667 334.58C158.22 309.494 169.54 284.414 214.032 276.84C188.575 281.172 159.96 274.017 152.113 245.951C149.85 237.879 146 188.159 146 181.444C146 147.805 175.49 158.375 193.688 172.036Z" fill="url(#paint0_linear_581_21)"/>
                        <defs>
                        <linearGradient id="paint0_linear_581_21" x1="256" y1="159.287" x2="256" y2="344.731" gradientUnits="userSpaceOnUse">
                        <stop stop-color="white"/>
                        <stop offset="1" stop-color="white"/>
                        </linearGradient>
                        </defs>
                    </svg>



            <?php endif; ?>

            <?php if ( ! empty($threads_url)) : ?>
                <a href="<?= $threads_url ?>" target="_blank" class="ppress-pf-social-icon dpf-threads">
                    <svg style="width: 34px;height: 34px;" fill-rule="evenodd" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="256" cy="256" r="256" fill="black"/>
                        <path d="M317.192 246.651C316.09 246.123 314.972 245.614 313.838 245.127C311.864 208.718 291.992 187.873 258.624 187.66C258.472 187.659 258.322 187.659 258.171 187.659C238.212 187.659 221.613 196.188 211.396 211.708L229.748 224.31C237.38 212.718 249.358 210.246 258.18 210.246C258.282 210.246 258.384 210.246 258.485 210.247C269.472 210.317 277.762 213.515 283.128 219.752C287.033 224.292 289.645 230.567 290.938 238.485C281.197 236.828 270.662 236.318 259.4 236.964C227.675 238.794 207.28 257.317 208.65 283.056C209.345 296.112 215.842 307.344 226.943 314.681C236.329 320.884 248.418 323.917 260.982 323.231C277.573 322.32 290.589 315.983 299.67 304.395C306.566 295.595 310.928 284.191 312.854 269.821C320.761 274.599 326.621 280.885 329.858 288.443C335.361 301.289 335.682 322.4 318.476 339.611C303.4 354.688 285.279 361.211 257.893 361.412C227.515 361.187 204.54 351.433 189.602 332.423C175.614 314.621 168.385 288.909 168.116 256C168.385 223.09 175.614 197.378 189.602 179.577C204.54 160.567 227.514 150.813 257.893 150.587C288.492 150.815 311.867 160.615 327.376 179.717C334.981 189.085 340.715 200.865 344.495 214.6L366 208.856C361.418 191.95 354.209 177.381 344.399 165.299C324.516 140.809 295.436 128.26 257.968 128H257.818C220.426 128.259 191.672 140.856 172.355 165.439C155.166 187.315 146.299 217.754 146.001 255.91L146 256L146.001 256.09C146.299 294.245 155.166 324.685 172.355 346.561C191.672 371.144 220.426 383.741 257.818 384H257.968C291.211 383.769 314.644 375.056 333.948 355.748C359.204 330.488 358.443 298.825 350.119 279.388C344.147 265.449 332.761 254.128 317.192 246.651ZM259.794 300.676C245.889 301.46 231.444 295.212 230.732 281.829C230.204 271.907 237.785 260.835 260.647 259.516C263.265 259.365 265.834 259.291 268.358 259.291C276.662 259.291 284.431 260.098 291.494 261.644C288.859 294.58 273.407 299.928 259.794 300.676Z" fill="white"/>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ( ! empty($youtube_url)) : ?>
                <a href="<?= $youtube_url ?>" target="_blank" class="ppress-pf-social-icon dpf-youtube">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                         width="48" height="48"
                         viewBox="0 0 48 48"
                         style=" fill:#000000;">
                        <path fill="#f44336" d="M24 4A20 20 0 1 0 24 44A20 20 0 1 0 24 4Z"></path>
                        <path fill="#fff" d="M17,34l18-10L17,14V34z"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ( ! empty($vk_url)) : ?>
                <a href="<?= $vk_url ?>" target="_blank" class="ppress-pf-social-icon dpf-vk">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                         width="48" height="48"
                         viewBox="0 0 48 48"
                         style=" fill:#000000;">
                        <path fill="#1976d2" d="M24 4A20 20 0 1 0 24 44A20 20 0 1 0 24 4Z"></path>
                        <path fill="#fff" d="M35.937,18.041c0.046-0.151,0.068-0.291,0.062-0.416C35.984,17.263,35.735,17,35.149,17h-2.618 c-0.661,0-0.966,0.4-1.144,0.801c0,0-1.632,3.359-3.513,5.574c-0.61,0.641-0.92,0.625-1.25,0.625C26.447,24,26,23.786,26,23.199 v-5.185C26,17.32,25.827,17,25.268,17h-4.649C20.212,17,20,17.32,20,17.641c0,0.667,0.898,0.827,1,2.696v3.623 C21,24.84,20.847,25,20.517,25c-0.89,0-2.642-3-3.815-6.932C16.448,17.294,16.194,17,15.533,17h-2.643 C12.127,17,12,17.374,12,17.774c0,0.721,0.6,4.619,3.875,9.101C18.25,30.125,21.379,32,24.149,32c1.678,0,1.85-0.427,1.85-1.094 v-2.972C26,27.133,26.183,27,26.717,27c0.381,0,1.158,0.25,2.658,2c1.73,2.018,2.044,3,3.036,3h2.618 c0.608,0,0.957-0.255,0.971-0.75c0.003-0.126-0.015-0.267-0.056-0.424c-0.194-0.576-1.084-1.984-2.194-3.326 c-0.615-0.743-1.222-1.479-1.501-1.879C32.062,25.36,31.991,25.176,32,25c0.009-0.185,0.105-0.361,0.249-0.607 C32.223,24.393,35.607,19.642,35.937,18.041z"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ( ! empty($github_url)) : ?>
                <a href="<?= $github_url ?>" target="_blank" class="ppress-pf-social-icon dpf-github">
                    <svg height="35" viewBox="0 0 16 16" version="1.1" width="35">
                        <path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"></path>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
        <?php

        return ob_get_clean();
    }

    public static function get_instance($form_id, $form_type)
    {
        static $instance = [];

        $cache_key = $form_id . '_' . $form_type;

        if ( ! isset($instance[$cache_key])) {
            $class                = get_called_class();
            $instance[$cache_key] = new $class($form_id, $form_type);
        }

        return $instance[$cache_key];
    }
}
