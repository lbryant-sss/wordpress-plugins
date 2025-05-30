<?php

ob_start();

if ( has_filter( 'fl_builder_module_frontend_custom_' . $module->slug ) ) {
	echo apply_filters( 'fl_builder_module_frontend_custom_' . $module->slug, (array) $module->settings, $module );
} else {
	include apply_filters( 'fl_builder_module_frontend_file', $module->path( 'includes/frontend.php' ), $module );
}

$out = ob_get_clean();

echo apply_filters( 'fl_builder_render_module_content', $out, $module );
