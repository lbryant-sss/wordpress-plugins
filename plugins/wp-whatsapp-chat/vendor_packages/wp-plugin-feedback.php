<?php

if ( class_exists( 'QuadLayers\\PluginFeedback\\Load' ) ) {
	\QuadLayers\PluginFeedback\Load::instance()->add(
		QLWAPP_PLUGIN_FILE,
		array(
			'support_link' => 'https://quadlayers.com/account/support/?utm_source=qlwapp_plugin&utm_medium=plugin_feedback&utm_campaign=support&utm_content=feedback_form',
		)
	);
}