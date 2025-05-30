<?php

namespace wpautoterms\frontend\notice;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cookies_Notice extends Base_Notice {
	const CLASS_COOKIES_NOTICE = 'wpautoterms-cookies-notice';
	const CLASS_CLOSE_BUTTON = 'wpautoterms-notice-close';
	const COOKIE_NAME = 'wpautoterms-cookies-notice';


	public static function create(  ) {
		$a = new Cookies_Notice( 'cookies_notice', 'wpautoterms-cookies-notice-container', self::CLASS_COOKIES_NOTICE );


		return $a;
	}


	protected function _is_enabled() {
		return parent::_is_enabled();
	}

	protected function _print_box() {
		$class_escaped = esc_attr( Cookies_Notice::CLASS_COOKIES_NOTICE );
		\wpautoterms\print_template( 'cookies-notice', array(
			'cookie_name'   => static::COOKIE_NAME,
			'cookie_value'  => 1,
			'class_escaped' => $class_escaped,
			'message'       => do_shortcode( $this->_message ),
			'close'         => $this->_get_close_message(),
		) );
	}

	protected function _localize_args() {
		$result = parent::_localize_args();

		$result['cookie_name'] = static::COOKIE_NAME;
		$result['class']       = esc_attr( Cookies_Notice::CLASS_COOKIES_NOTICE );

		return $result;
	}
}
