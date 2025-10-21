<?php
defined('ABSPATH') or die('No script kiddies please!');
if (!isset($trustindexShortCodeText)) {
$trustindexShortCodeText = $pluginManagerInstance->get_shortcode_name().' no-registration='.$pluginManagerInstance->getShortName();
}
$trustindexShortCodeId = "ti-shortcode-id-".uniqid();
?>
<div class="ti-form-group" style="margin-bottom: 2px">
<label>Shortcode</label>
<code class="ti-shortcode" id="<?php echo esc_attr($trustindexShortCodeId); ?>">[<?php echo esc_html($trustindexShortCodeText); ?>]</code>
<a href="#<?php echo esc_attr($trustindexShortCodeId); ?>" class="ti-btn ti-tooltip ti-toggle-tooltip btn-copy2clipboard">
<?php echo esc_html(__('Copy to clipboard', 'wp-reviews-plugin-for-google')); ?>
<span class="ti-tooltip-message">
<span style="color: #00ff00; margin-right: 2px">✓</span>
<?php echo esc_html(__('Copied', 'wp-reviews-plugin-for-google')); ?>
</span>
</a>
</div>
<div class="ti-info-text"><?php echo esc_html(__('Copy and paste this shortcode into post, page or widget.', 'wp-reviews-plugin-for-google')); ?></div>