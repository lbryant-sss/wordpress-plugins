<?php

namespace WBCR\Factory_Freemius_171\Sdk;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( !class_exists('WBCR\Factory_Freemius_171\Sdk\Freemius_Exception') ) {
	exit;
}

if( !class_exists('WBCR\Factory_Freemius_171\Sdk\Freemius_InvalidArgumentException') ) {
	class Freemius_InvalidArgumentException extends Freemius_Exception { }
}