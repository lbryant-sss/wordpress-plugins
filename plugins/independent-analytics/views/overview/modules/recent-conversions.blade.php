@php /** @var \IAWP\Env $env */ @endphp
@php /** @var \IAWP\Overview\Modules\Recent_Conversions_Module $module */ @endphp
@php /** @var ?array $dataset */ @endphp

<?php
if ($is_empty) : ?>
    <p class="no-data-message"><span class="dashicons dashicons-chart-bar"></span> <?php esc_html_e('No data found in this date range.', 'independent-analytics'); ?></p><?php
endif;

if (is_array($dataset)) : ?>
    <div><?php 
        $dataset = $module->add_icons_to_dataset($dataset);
        for ($i = 0; $i < count($dataset); $i++) : 
            if ($i % 10 == 0) : ?>
                <div class="module-page module-page-<?php echo $i/10 + 1; ?> <?php echo $i == 0 ? 'current' : '';?> conversions-grid"><?php
            endif; ?>
            <div>
                <span class="icon-container">
                    <span class="icon"><?php echo sanitize_text_field($dataset[$i]['viewed_at']); ?></span>
                    <span class="icon-label"><?php echo sanitize_text_field($dataset[$i]['viewed_at_the_long_way']); ?></span>
                </span>
            </div>
            <div>
                <span class="icon-container">
                    <span class="icon"><?php echo wp_kses($dataset[$i]['flag'], 'post'); ?></span>
                    <span class="icon-label"><?php echo sanitize_text_field($dataset[$i]['country']); ?></span>
                </span>
            </div>
            <div>
                <span class="icon-container">
                    <span class="icon"><?php echo wp_kses($dataset[$i]['device_type_icon'], 'post'); ?></span>
                    <span class="icon-label"><?php echo sanitize_text_field($dataset[$i]['device_type']); ?></span>
                </span>
            </div>
            <div>
                <span class="icon-container">
                    <span class="icon"><?php echo wp_kses($dataset[$i]['browser_icon'], 'post'); ?></span>
                    <span class="icon-label"><?php echo sanitize_text_field($dataset[$i]['browser']); ?></span>
                </span>
            </div>
            <div>
                <span class="conversion-type <?php echo esc_attr($dataset[$i]['conversion_type']); ?>"><?php echo sanitize_text_field($dataset[$i]['conversion_label']); ?></span>
            </div>
                    <div class="page-title">
                        <?php echo $dataset[$i]['name']; ?>
                    </div><?php
            if (($i + 1) % 10 == 0 || $i == count($dataset) - 1) : ?>
                </div><?php
            endif;
        endfor;
        if (count($dataset) > 10) : ?>
            <div class="module-pagination">
                <button class="pagination-button left" disabled><span class="dashicons dashicons-arrow-left-alt2"></span></button>
                <span class="page-count">
                    <span class="current-page">1</span>
                    <span>/</span>
                    <span class="full-width-count"><?php echo ceil(count($dataset) / 20 ); ?></span>
                    <span class="regular-count"><?php echo ceil(count($dataset) / 10 ); ?></span>
                </span>
                <button class="pagination-button right"><span class="dashicons dashicons-arrow-right-alt2"></span></button>
            </div><?php
        endif; ?>
    </div><?php
else : ?>
    <div class="loading-message">
        <img src="<?php echo esc_url(iawp_url_to('img/loading.svg')) ?>" />
        <p><?php esc_html_e('Loading data...', 'independent-analytics'); ?></p>
    </div><?php
endif;