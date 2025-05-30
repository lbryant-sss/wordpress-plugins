<?php

/**
 * Manage any frontend related tasks here.
 */

namespace Extendify\Library;

defined('ABSPATH') || die('No direct access.');

/**
 * This class handles any file loading for the frontend of the site.
 */

class Frontend
{
    /**
     * Adds various actions to set up the page
     *
     * @return void
     */
    public function __construct()
    {
        \add_action('wp_enqueue_scripts', [$this, 'enqueueUtilityStyles']);
        \add_action('admin_init', [$this, 'enqueueUtilityStyles']);
    }

    /**
     * Enqueue utility styles if the extendify pattern was imported
     *
     * @return void
     */
    public function enqueueUtilityStyles()
    {
        // This only is in the database in older versions of the plugin that require these styles.
        if (\get_option('extendify_pattern_was_imported', false)) {
            $this->themeCompatInlineStyles();
            // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
            \wp_enqueue_style(
                'extendify-utility-styles',
                EXTENDIFY_BASE_URL . 'public/build/utility-minimum.css'
            );
        }
    }

    /**
     * Inline styles to be applied for compatible themes
     *
     * @return void
     */
    // phpcs:ignore
    public function themeCompatInlineStyles()
    {
        $css = '';
        $theme = get_option('template');

        if ($theme === 'kadence') {
            $css = 'body, .editor-styles-wrapper {
                --wp--preset--color--background: var(--global-palette8);
                --wp--preset--color--foreground: var(--global-palette4);
                --wp--preset--color--primary: var(--global-palette1);
                --wp--preset--color--secondary: var(--global-palette2);
                --wp--preset--color--tertiary: var(--global-palette7);
                --wp--custom--spacing--large: clamp(var(--global-sm-spacing), 5vw, var(--global-xxl-spacing));
                --wp--preset--font-size--large: var(--h2FontSize);
                --wp--preset--font-size--huge: var(--h1FontSize);
            }';
        }

        if ($theme === 'neve') {
            $css = 'body, .editor-styles-wrapper {
                --wp--preset--color--background: var(--nv-site-bg);
                --wp--preset--color--foreground: var(--nv-text-color);
                --wp--preset--color--primary: var(--nv-primary-accent);
                --wp--preset--color--secondary: var(--nv-secondary-accent);
                --wp--preset--color--tertiary: var(--nv-light-bg);
                --wp--custom--spacing--large: clamp(15px, 5vw, 80px);
                --wp--preset--font-size--large: var(--h2FontSize);
                --wp--preset--font-size--huge: var(--h1FontSize);
            }';
        }

        if ($theme === 'blocksy') {
            $css = 'body, .editor-styles-wrapper {
                --wp--preset--color--background: var(--paletteColor7);
                --wp--preset--color--foreground: var(--color);
                --wp--preset--color--primary: var(--paletteColor1);
                --wp--preset--color--secondary: var(--paletteColor4);
            }';
        }

        if ($theme === 'go') {
            $css = 'body, .editor-styles-wrapper {
                --wp--preset--color--background: var(--go--color--background);
                --wp--preset--color--foreground: var(--go--color--text);
            }';
        }

        if ($theme === 'astra') {
            $css = 'body, .editor-styles-wrapper {
                --wp--preset--color--background: #ffffff;
                --wp--preset--color--foreground: var(--ast-global-color-2);
                --wp--preset--color--primary: var(--ast-global-color-0);
                --wp--preset--color--secondary: var(--ast-global-color-2);
            }';
        }

        if ($theme === 'oceanwp') {
            $background = get_theme_mod('ocean_background_color', '#ffffff');
            $primary    = get_theme_mod('ocean_primary_color', '#13aff0');
            $secondary  = get_theme_mod('ocean_hover_primary_color', '#0b7cac');
            $gap        = get_theme_mod('ocean_separate_content_padding', '30px');

            $css = 'body, .editor-styles-wrapper {
                --wp--preset--color--background: ' . $background . ';
                --wp--preset--color--foreground: #1B1B1B;
                --wp--preset--color--primary: ' . $primary . ';
                --wp--preset--color--secondary: ' . $secondary . ';
                --wp--style--block-gap: ' . $gap . ';
                --wp--custom--spacing--large: clamp(2rem, 7vw, 8rem);
            }';
        }

        if ($theme === 'generatepress') {
            $settings = (array) get_option('generate_settings', []);

            if (! array_key_exists('background_color', $settings)) {
                $background = '#f7f8f9';
            } else {
                $background = $settings['background_color'];
            }

            if (! array_key_exists('text_color', $settings)) {
                $foreground = '#222222';
            } else {
                $foreground = $settings['text_color'];
            }

            if (! array_key_exists('link_color', $settings)) {
                $primary = '#1e73be';
            } else {
                $primary = $settings['link_color'];
            }

            if (! array_key_exists('link_color', $settings)) {
                $primary = '#1e73be';
            } else {
                $primary = $settings['link_color'];
            }

            $css = 'body, .editor-styles-wrapper {
                --wp--preset--color--background: ' . $background . ';
                --wp--preset--color--foreground: ' . $foreground . ';
                --wp--preset--color--primary: ' . $primary . ';
                --wp--preset--color--secondary: #636363;
                --wp--style--block-gap: 3rem;
                --wp--custom--spacing--large: clamp(2rem, 7vw, 8rem);
                --responsive--alignwide-width: 1120px;
            }';
        }//end if

        if ($theme === 'twentytwentytwo') {
            $css = 'body, .editor-styles-wrapper {
                --extendify--spacing--large: clamp(2rem,8vw,8rem);
            }';
        }

        if ($theme === 'twentytwentyone') {
            $css = 'body, .editor-styles-wrapper {
                --wp--preset--color--background: var(--global--color-background);
                --wp--preset--color--foreground: var(--global--color-primary);
                --wp--preset--color--primary: var(--global--color-gray);
                --wp--preset--color--secondary: #464b56;
                --wp--preset--color--tertiary: var(--global--color-light-gray);
                --wp--style--block-gap: var(--global--spacing-unit);
                --wp--preset--font-size--large: 2.5rem;
                --wp--preset--font-size--huge: var(--global--font-size-xxl);
            }
            .has-foreground-background-color,
            .has-primary-background-color,
            .has-secondary-background-color {
                --local--color-primary: var(--wp--preset--color--background);
                --local--color-background: var(--wp--preset--color--primary);
            }';
        }

        if ($theme === 'twentytwenty') {
            $background = sanitize_hex_color_no_hash(get_theme_mod('background_color', 'f5efe0'));
            $primary = get_theme_mod(
                'accent_accessible_colors',
                [
                    'content' => [ 'accent' => '#cd2653' ],
                ]
            );
            $primary = $primary['content']['accent'];
            $css = 'body, .editor-styles-wrapper {
                --wp--preset--color--background: #' . $background . ';
                --wp--preset--color--foreground: #000;
                --wp--preset--color--primary: ' . $primary . ';
                --wp--preset--color--secondary: #69603e;
                --wp--style--block-gap: 3rem;
                --wp--custom--spacing--large: clamp(2rem, 7vw, 8rem);
                --responsive--alignwide-width: 120rem;
            }';
        }//end if

        if ($theme === 'twentynineteen') {
            /**
             * Use the color from Twenty Nineteen's customizer value.
             */
            $primary = 199;
            if (get_theme_mod('primary_color', 'default') !== 'default') {
                $primary = absint(get_theme_mod('primary_color_hue', 199));
            }

            /**
             * Filters Twenty Nineteen default saturation level.
             *
             * @since Twenty Nineteen 1.0
             *
             * @param int $saturation Color saturation level.
             */
            // phpcs:ignore
            $saturation = apply_filters('twentynineteen_custom_colors_saturation', 100);
            $saturation = absint($saturation) . '%';

            /**
             * Filters Twenty Nineteen default lightness level.
             *
             * @since Twenty Nineteen 1.0
             *
             * @param int $lightness Color lightness level.
             */
            // phpcs:ignore
            $lightness = apply_filters('twentynineteen_custom_colors_lightness', 33);
            $lightness = absint($lightness) . '%';

            $css = 'body, .editor-styles-wrapper {
                --wp--preset--color--foreground: #111;
                --wp--preset--color--primary: hsl( ' . $primary . ', ' . $saturation . ', ' . $lightness . ' );
                --wp--preset--color--secondary: #767676;
                --wp--preset--color--tertiary: #f7f7f7;
            }';
        }//end if

        if (!$css) {
            return;
        }

        // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
        \wp_register_style('extendify-utility-extras', false);
        \wp_enqueue_style('extendify-utility-extras');
        \wp_add_inline_style('extendify-utility-extras', $css);
        // Adds inline to the live preview.
        \wp_add_inline_style('wp-components', $css);
    }
}
