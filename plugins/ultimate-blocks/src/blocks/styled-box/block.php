<?php

function ub_render_styled_box_bordered_content($attributes, $content){
    return $content;
}

function ub_register_styled_box_bordered_box_block() {
	if( function_exists( 'register_block_type_from_metadata' ) ) {
		register_block_type_from_metadata( dirname(dirname(dirname(__DIR__))) . '/dist/blocks/styled-box/styled-box-border/block.json', array(
            'attributes' => array(),
            'render_callback' => 'ub_render_styled_box_bordered_content')
        );
    }
}

add_action('init', 'ub_register_styled_box_bordered_box_block');

function ub_render_styled_box_numbered_box_column($attributes, $content){
    extract($attributes);

    return '<div class="ub-number-panel">
        <div class="ub-number-container">
            <p class="ub-number-display">' . wp_kses_post($number) . '</p>
        </div>
        <p class="ub-number-box-title">' . wp_kses_post($title) . '</p>
        <div class="ub-number-box-body">' . Ultimate_Blocks\includes\strip_xss($content) . '</div>
    </div>';
}

function ub_register_styled_box_numbered_box_column_block() {
	if( function_exists( 'register_block_type_from_metadata' ) ) {
		register_block_type_from_metadata( dirname(dirname(dirname(__DIR__))) . '/dist/blocks/styled-box/styled-box-numbered-box-column/block.json', array(
            'attributes' => array(
                'number' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'title' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'titleAlign' => array(
                    'type' => 'string',
                    'default' => 'center'
                ),
                'numberColor' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'backColor' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'borderColor' => array(
                    'type' => 'string',
                    'default' => ''
                )
            ),
            'render_callback' => 'ub_render_styled_box_numbered_box_column')
        );
    }
}

add_action('init', 'ub_register_styled_box_numbered_box_column_block');

function ub_render_styled_box_block($attributes, $content){
    extract($attributes);
    $renderedBlock = '';
    if($mode === 'notification' && $text[0] != ''){
        $renderedBlock = '<div class="ub-notification-text">'. wp_kses_post($text[0]) .'</div>';
    }
    else if($mode === 'feature'){
        foreach(range(0, count($text)-1) as $i){
            $renderedBlock .= '<div class="ub-feature">'.
                ($image[$i]['url'] === '' ? '' :
                    '<img class="ub-feature-img" src="'. esc_url($image[$i]['url']) .'"/>').
                    '<p class="ub-feature-title">'. wp_kses_post($title[$i]) .'</p>
                    <p class="ub-feature-body">'. wp_kses_post($text[$i]) .'</p>
            </div>';
        }
    }
    else if($mode === 'number'){
        if(count( array_filter($text, function($item){return $item !== '';}) ) > 0 ||
                count( array_filter($title, function($item){return $item !== '';}) ) > 0){
            foreach(range(0, count($text)-1) as $i){
                $renderedBlock .= '<div class="ub-number-panel">
                    <div class="ub-number-container">
                        <p class="ub-number-display">'. wp_kses_post($number[$i]) .'</p>
                    </div>
                    <p class="ub-number-box-title">'. wp_kses_post($title[$i]) .'</p>
                    <p class="ub-number-box-body">'. wp_kses_post($text[$i]) .'</p>
                </div>';
            }
        }
        else {
            $renderedBlock = $content;
        }
    }
    else if(in_array($mode, array('bordered', 'notification'))){
        $renderedBlock = $content;
    }

    return '<div class="wp-block-ub-styled-box ub-styled-box ub-'. esc_attr($mode) .'-box'.(isset($className) ? ' ' . esc_attr($className) : '')
            .'" id="ub-styled-box-'. esc_attr($blockID) .'">'.
                $renderedBlock.'</div>';
}

function ub_register_styled_box_block() {
	if( function_exists( 'register_block_type_from_metadata' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
		register_block_type_from_metadata( dirname(dirname(dirname(__DIR__))) . '/dist/blocks/styled-box/block.json', array(
            'attributes' => $defaultValues['ub/styled-box']['attributes'],
            'render_callback' => 'ub_render_styled_box_block'));
    }
}

add_action('init', 'ub_register_styled_box_block');
