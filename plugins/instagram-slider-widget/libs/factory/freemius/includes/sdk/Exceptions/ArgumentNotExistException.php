<?php

namespace WBCR\Factory_Freemius_171\Sdk;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( !class_exists('WBCR\Factory_Freemius_171\Sdk\Freemius_InvalidArgumentException') ) {
	exit;
}

if( !class_exists('WBCR\Factory_Freemius_171\Sdk\Freemius_ArgumentNotExistException') ) {
	class Freemius_ArgumentNotExistException extends Freemius_InvalidArgumentException {

	}
}