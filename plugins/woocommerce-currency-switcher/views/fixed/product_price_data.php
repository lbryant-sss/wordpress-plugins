<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

if (!function_exists('woocs_price_options')) {

    function woocs_price_options($post_id, $curr_code, $hash, $value_regular = '', $value_sale = '') {
        ?>
        <li id="woocs_li_<?php echo esc_attr($hash) ?>_<?php echo esc_attr($curr_code) ?>">
            <div class="woocs_price_col">
                <p class="form-field form-row _regular_price_field">
                    <label for="woocs_regular_<?php echo esc_attr($hash) ?>_<?php echo esc_attr($curr_code) ?>"><?php esc_html_e('Regular price', 'woocommerce-currency-switcher') ?>&nbsp;(<b><?php echo esc_html($curr_code) ?></b>):</label>
                    <input type="text" class="short wc_input_price" name="woocs_regular_price[<?php echo esc_attr($post_id) ?>][<?php echo esc_attr($curr_code) ?>]" id="woocs_regular_<?php echo esc_attr($hash) ?>_<?php echo esc_attr($curr_code) ?>" value="<?php echo esc_html($value_regular > 0 ? $value_regular : '') ?>" placeholder="<?php esc_html_e('auto', 'woocommerce-currency-switcher') ?>">
                </p>
            </div>
            <div class="woocs_price_col">
                <p class="form-field form-row _sale_price_field">
                    <label for="woocs_sale_<?php echo esc_attr($hash) ?>_<?php echo esc_attr($curr_code) ?>"><?php esc_html_e('Sale price', 'woocommerce-currency-switcher') ?>&nbsp;(<b><?php echo esc_html($curr_code) ?></b>):</label>
                    <input type="text" class="short wc_input_price" name="woocs_sale_price[<?php echo esc_attr($post_id) ?>][<?php echo esc_attr($curr_code) ?>]" id="woocs_sale_<?php echo esc_attr($hash) ?>_<?php echo esc_attr($curr_code) ?>" value="<?php echo esc_html($value_sale > 0 ? $value_sale : '') ?>" placeholder="<?php esc_html_e('auto', 'woocommerce-currency-switcher') ?>">
                </p>
            </div>
            <div class="woocs_price_col">
                <p class="form-row">
                    <a href="javascript:woocs_remove_li_product_price('<?php echo esc_attr($hash) ?>','<?php echo esc_attr($curr_code) ?>',false);void(0);" class="button"><?php esc_html_e('Remove', 'woocommerce-currency-switcher') ?></a>
                </p>
            </div>
        </li>
        <?php
    }

}

//***

if (!function_exists('woocs_price_options_geo')) {

    function woocs_price_options_geo($post_id, $index, $hash, $countries_selected, $value_regular = '', $value_sale = '') {
        ?>
        <li id="woocs_li_geo_<?php echo esc_attr($hash) ?>_<?php echo esc_attr($index) ?>">
            <div class="woocs_price_col">
                <p class="form-field form-row _regular_price_field">
                    <label for="woocs_regular_geo_<?php echo esc_attr($hash) ?>_<?php echo esc_attr($index) ?>"><?php esc_html_e('Regular price', 'woocommerce-currency-switcher') ?>&nbsp;(<b><?php echo esc_html(get_woocommerce_currency_symbol()); ?></b>):</label>
                    <input type="text" class="short wc_input_price" name="woocs_regular_price_geo[<?php echo esc_attr($post_id) ?>][<?php echo esc_attr($index) ?>]" id="woocs_regular_geo_<?php echo esc_attr($hash) ?>_<?php echo esc_attr($index) ?>" value="<?php echo esc_html($value_regular > 0 ? $value_regular : '') ?>" placeholder="<?php esc_html_e('auto', 'woocommerce-currency-switcher') ?>">
                </p>
            </div>
            <div class="woocs_price_col">
                <p class="form-field form-row _sale_price_field">
                    <label for="woocs_sale_geo_<?php echo esc_attr($hash) ?>_<?php echo esc_attr($index) ?>"><?php esc_html_e('Sale price', 'woocommerce-currency-switcher') ?>&nbsp;(<b><?php echo esc_html(get_woocommerce_currency_symbol()); ?></b>):</label>
                    <input type="text" class="short wc_input_price" name="woocs_sale_price_geo[<?php echo esc_attr($post_id) ?>][<?php echo esc_attr($index) ?>]" id="woocs_sale_geo_<?php echo esc_attr($hash) ?>_<?php echo esc_attr($index) ?>" value="<?php echo esc_html($value_sale > 0 ? $value_sale : '') ?>" placeholder="<?php esc_html_e('auto', 'woocommerce-currency-switcher') ?>">
                </p>
            </div>
            <div class="woocs_price_col">
                <p class="form-row">
                    <a href="javascript:woocs_remove_li_product_price('<?php echo esc_attr($hash) ?>','<?php echo esc_attr($index) ?>', true);void(0);" class="button"><?php esc_html_e('Remove', 'woocommerce-currency-switcher') ?></a>
                </p>
            </div>
            <div class="variations-defaults woocs-variations-defaults">

                <p class="form-row">
                    <?php $c = new WC_Countries(); ?>
                    <select name="woocs_price_geo_countries[<?php echo esc_attr($post_id) ?>][<?php echo esc_attr($index) ?>][]" multiple="" size="1" <?php if ($index !== '__INDEX__'): ?>class="woocs_price_geo_countries chosen_select woocs_country_select" <?php else: ?>class="woocs_country_select" <?php endif; ?> data-placeholder="<?php esc_html_e('select some countries', 'woocommerce-currency-switcher') ?>">
                        <option value="0"></option>
                        <?php foreach ($c->get_countries() as $key => $value): ?>
                            <option <?php echo(in_array($key, $countries_selected) ? 'selected=""' : '') ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_html($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <h4><?php esc_html_e("GeoIP profiles", 'woocommerce-currency-switcher'); ?>:</h4>
                <p class="form-row woocs-padd-0">
                    <?php
                    global $WOOCS;
                    $geoIP_profiles = $WOOCS->geoip_profiles->get_data();
                    ?>
                    <select class="woocs_geoip_profile">
                        <?php
                        foreach ($geoIP_profiles as $key => $item) {
                            ?>
                            <option value='<?php echo json_encode($item['data']) ?>'><?php echo esc_html($item['name']) ?></option>
                            <?php
                        }
                        ?>
                    </select><br />
                    <a href="javascript:woocs_set_geoip_profile('woocs_li_geo_<?php echo esc_attr($hash) ?>_<?php echo esc_attr($index) ?>')" class="button woocs_set_geoip_profile"><?php esc_html_e('Load countries from profile', 'woocommerce-currency-switcher') ?></a>
                </p>
            </div>
        </li>
        <?php
    }

}
?>

<div class="woocs_multiple_simple_panel options_group pricing woocommerce_variation_fixed" style="<?php if ($type == 'simple'): ?>display: none;<?php endif; ?>">

    <ul class="woocs_tab_navbar">
        <?php if ($is_fixed_enabled): ?>
            <li><a href="javascript:woocs_open_tab('woocs_tab_fixed','<?php echo esc_attr(woocs_short_id($post_id)) ?>');void(0)" id="woocs_tab_fixed_btn_<?php echo esc_attr(woocs_short_id($post_id)) ?>" class="woocs_tab_button button"><?php esc_html_e('The product fixed prices rules', 'woocommerce-currency-switcher') ?></a></li>
        <?php endif; ?>

        <?php if ($is_geoip_manipulation): ?>
            <li><a href="javascript:woocs_open_tab('woocs_tab_geo','<?php echo esc_attr(woocs_short_id($post_id)) ?>');void(0)" id="woocs_tab_geo_btn_<?php echo esc_attr(woocs_short_id($post_id)) ?>" class="woocs_tab_button button"><?php esc_html_e('The product custom GeoIP rules', 'woocommerce-currency-switcher') ?></a></li>
        <?php endif; ?>
    </ul>

    <input type="hidden" name="woocs_regular_price[<?php echo esc_attr($post_id) ?>]" value="" />
    <input type="hidden" name="woocs_sale_price[<?php echo esc_attr($post_id) ?>]" value="" />
    <input type="hidden" name="woocs_regular_price_geo[<?php echo esc_attr($post_id) ?>]" value="" />
    <input type="hidden" name="woocs_sale_price_geo[<?php echo esc_attr($post_id) ?>]" value="" />
    <input type="hidden" name="woocs_price_geo_countries[<?php echo esc_attr($post_id) ?>]" value="" />

    <!---------------------------------------------------------------->

    <?php if ($is_fixed_enabled): ?>
        <div id="woocs_tab_fixed_<?php echo esc_attr(woocs_short_id($post_id)) ?>" class="woocs_tab">
            <h4><?php esc_html_e('FOX - the product fixed prices', 'woocommerce-currency-switcher') ?><img class="help_tip" data-tip="<?php esc_html_e('Here you can set FIXED price for the product for any the currency you want. In the case of empty amount field - will work recounting by rate!', 'woocommerce-currency-switcher') ?>" src="<?php echo esc_attr(WOOCS_LINK) ?>/img/help.png" height="16" width="16" /></h4>
            <select class="select short woocs-width-200" id="woocs_mselect_<?php echo esc_attr(woocs_short_id($post_id)) ?>">
                <?php foreach ($currencies as $code => $curr): ?>
                    <?php
                    if ($code === $default_currency OR $this->is_exists($post_id, $code, 'regular')) {
                        continue;
                    }
                    ?>
                    <option value="<?php echo esc_attr($code) ?>"><?php echo esc_html($code) ?></option>
                <?php endforeach; ?>
            </select>
            &nbsp;<a href="javascript:woocs_add_product_price('<?php echo esc_attr($post_id) ?>', '<?php echo esc_attr(woocs_short_id($post_id)) ?>');void(0);" class="button"><?php esc_html_e('Add', 'woocommerce-currency-switcher') ?></a>
            &nbsp;<a href="javascript:woocs_add_all_product_price('<?php echo esc_attr($post_id) ?>', '<?php echo esc_attr(woocs_short_id($post_id)) ?>');void(0);" class="button woocs-mt-3"><?php esc_html_e('Add all', 'woocommerce-currency-switcher') ?></a>
            <br />
            <br />
            <hr class="woocs-hr-1" />
            <ul id="woocs_mlist_<?php echo esc_attr(woocs_short_id($post_id)) ?>">
                <?php
                foreach ($currencies as $code => $curr) {
                    if ($this->is_exists($post_id, $code, 'regular')) {
                        woocs_price_options($post_id, $code, woocs_short_id($post_id), $this->prepare_float_to_show($this->get_value($post_id, $code, 'regular'), $curr['decimals']), $this->prepare_float_to_show($this->get_value($post_id, $code, 'sale'), $curr['decimals']));
                    }
                }
                ?>
            </ul>
            <div id="woocs_multiple_simple_tpl">
                <?php woocs_price_options('__POST_ID__', '__CURR_CODE__', '__HASH__') ?>
            </div>
        </div>
    <?php endif; ?>

    <!---------------------------------------------------------------->

    <?php if (true OR $is_geoip_manipulation): ?>

        <div id="woocs_tab_geo_<?php echo esc_attr(woocs_short_id($post_id)) ?>" class="woocs_tab">
            <h4><?php esc_html_e('FOX - the product custom GeoIP rules', 'woocommerce-currency-switcher') ?><img class="help_tip" data-tip="<?php esc_html_e('Here you can set prices in the basic currency for different countries, and recount will be done relatively of this values. ATTENTION: fixed price has higher priority!', 'woocommerce-currency-switcher') ?>" src="<?php echo esc_attr(WOOCS_LINK) ?>/img/help.png" height="16" width="16" /></h4>

            <a href="javascript: woocs_add_group_geo('<?php echo esc_attr($post_id) ?>', '<?php echo esc_attr(woocs_short_id($post_id)) ?>');void(0);" class="button"><?php esc_html_e('Add group', 'woocommerce-currency-switcher') ?></a>

            <ul id="woocs_mlist_geo_<?php echo esc_attr(woocs_short_id($post_id)) ?>">
                <?php
                $curr = $currencies[$default_currency];
                if (!empty($product_geo_data) AND!empty($product_geo_data['price_geo_countries'])) {
                    foreach ($product_geo_data['price_geo_countries'] as $index => $countries_selected) {
                        if ($index == 0) {
                            continue;
                        }
                        woocs_price_options_geo($post_id, $index, woocs_short_id($post_id), (array) $countries_selected, $this->prepare_float_to_show($product_geo_data['regular_price_geo'][$index], $curr['decimals']), $this->prepare_float_to_show($product_geo_data['sale_price_geo'][$index], $curr['decimals']));
                    }
                }
                ?>
            </ul>

            <div id="woocs_multiple_simple_tpl_geo">
                <?php woocs_price_options_geo('__POST_ID__', '__INDEX__', '__HASH__', array()) ?>
            </div>


        </div>
    <?php endif; ?>


</div>