<?php

class Xoo_Wsc_Helper extends Xoo_Helper{

	protected static $_instance = null;

	public static function get_instance( $slug, $path, $helperArgs = array() ){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $slug, $path, $helperArgs );
		}
		return self::$_instance;
	}

	public function get_general_option( $subkey = '' ){
		return $this->get_option( 'xoo-wsc-gl-options', $subkey );
	}

	public function get_style_option( $subkey = '' ){
		return $this->get_option( 'xoo-wsc-sy-options', $subkey );
	}

	public function get_advanced_option( $subkey = '' ){
		return $this->get_option( 'xoo-wsc-av-options', $subkey );
	}

	public function box_shadow_desc($value){
		$html = '<a href="https://box-shadow.dev/" target="__blank">Preview & click on "Show code" -> copy value</a>';
		if( $value ){
			$html .= 'Default: '.$value;
		}
		return $html;
	}

	public function get_usage_data(){

		$settings = array(
			'gl' => $this->get_general_option(),
			'sy' => $this->get_style_option(),
			'av' => $this->get_advanced_option()
		);

		return array(
			'version' 	=> XOO_WSC_VERSION,
			'settings' 	=> json_encode($settings)
		);
	}

}

function xoo_wsc_helper(){
	return Xoo_Wsc_Helper::get_instance( 'side-cart-woocommerce', XOO_WSC_PATH, array(
		'pluginFile' => XOO_WSC_PLUGIN_FILE,
		'pluginName' =>	'Woocommerce Side Cart' 
	) );
}
xoo_wsc_helper();

?>