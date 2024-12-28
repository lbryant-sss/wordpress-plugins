<?php

/**
 * $attributes (array): The block attributes.
 * $content (string): The block default content.
 * $block (WP_Block): The block instance.
 */


echo wp_bannerize_pro([
    'numbers' => $attributes['numbers'],
    'orderby' => $attributes['orderby'],
    'id' => $attributes['banners'],
]);
