<?php

class Brizy_Editor_Editor_ModuleGroups_ContentProvider implements Brizy_Editor_Editor_ModuleGroups_ProviderInterface {

	use Brizy_Editor_Editor_ModuleGroups_ContextUtils;

	public function supportContext( $context ) {
		return !$this->isStory( $context );
	}

	public function collect( $context ) {
		return [
			new Brizy_Editor_Editor_ModuleGroups_ModuleGroup( __( 'content', 'brizy' ), [
				"IconText",
				"AnimatedHeadline",
				"Lottie",
				"Embed",
				"StarRating",
				"Alert",
				"Counter",
				"Countdown2",
				"ProgressBar",
				"Calendly",
				"Carousel",
				"Tabs",
				"Accordion",
				"Switcher",
				"Table",
				"Timeline",
				"Login",
				"Flipbox",
				"Paypal",
				"Chart"
			], 400 ),
		];
	}
}


