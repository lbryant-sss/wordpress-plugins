<?php
/*
	Plugin Name: MouseWheel Smooth Scroll
	Plugin URI: https://kubiq.sk
	Description: MouseWheel smooth scrolling for your WordPress website
	Version: 6.6
	Author: KubiQ
	Author URI: https://kubiq.sk
	Text Domain: wpmss
	Domain Path: /languages
*/

class wpmss{

	var $settings;
	var $uploads;

	function __construct(){
		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );
		add_action( 'admin_menu', [ $this, 'plugin_menu_link' ] );
		add_action( 'init', [ $this, 'plugin_init' ] );
	}

	function plugins_loaded(){
		load_plugin_textdomain( 'wpmss', FALSE, basename( __DIR__ ) . '/languages/' );
	}

	function filter_plugin_actions( $links, $file ){
		$settings_link = '<a href="options-general.php?page=' . basename( __FILE__ ) . '">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	function plugin_menu_link(){
		add_submenu_page(
			'options-general.php',
			__( 'Smooth Scroll', 'wpmss' ),
			__( 'Smooth Scroll', 'wpmss' ),
			'manage_options',
			basename( __FILE__ ),
			[ $this, 'admin_options_page' ]
		);
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'filter_plugin_actions' ], 10, 2 );
	}

	function plugin_init(){
		$this->settings = get_option( 'wpmss_settings', [] );
		if( ! isset( $this->settings['general']['timestamp'] ) ){
			$this->settings['general']['timestamp'] = time();
			$this->settings['general']['pulseAlgorithm'] = 1;
			$this->settings['general']['keyboardSupport'] = 1;
			update_option( 'wpmss_settings', $this->settings );
		}

		$this->uploads = wp_get_upload_dir();

		$this->process_settings();

		add_action( 'wp_enqueue_scripts', [ $this, 'plugin_scripts_load' ] );
	}

	function process_settings(){
		$unsanitized_settings = $this->settings;
		$this->settings = [ 'general' => [] ];
		$this->settings['general']['timestamp'] = empty( $unsanitized_settings['general']['timestamp'] ) ? time() : intval( $unsanitized_settings['general']['timestamp'] );
		$this->settings['general']['frameRate'] = empty( $unsanitized_settings['general']['frameRate'] ) ? 150 : intval( $unsanitized_settings['general']['frameRate'] );
		$this->settings['general']['animationTime'] = empty( $unsanitized_settings['general']['animationTime'] ) ? 1000 : intval( $unsanitized_settings['general']['animationTime'] );
		$this->settings['general']['stepSize'] = empty( $unsanitized_settings['general']['stepSize'] ) ? 100 : intval( $unsanitized_settings['general']['stepSize'] );
		$this->settings['general']['pulseAlgorithm'] = empty( $unsanitized_settings['general']['pulseAlgorithm'] ) ? 0 : intval( $unsanitized_settings['general']['pulseAlgorithm'] );
		$this->settings['general']['pulseScale'] = empty( $unsanitized_settings['general']['pulseScale'] ) ? 4 : intval( $unsanitized_settings['general']['pulseScale'] );
		$this->settings['general']['pulseNormalize'] = empty( $unsanitized_settings['general']['pulseNormalize'] ) ? 1 : intval( $unsanitized_settings['general']['pulseNormalize'] );
		$this->settings['general']['accelerationDelta'] = empty( $unsanitized_settings['general']['accelerationDelta'] ) ? 50 : intval( $unsanitized_settings['general']['accelerationDelta'] );
		$this->settings['general']['accelerationMax'] = empty( $unsanitized_settings['general']['accelerationMax'] ) ? 3 : intval( $unsanitized_settings['general']['accelerationMax'] );
		$this->settings['general']['keyboardSupport'] = empty( $unsanitized_settings['general']['keyboardSupport'] ) ? 0 : intval( $unsanitized_settings['general']['keyboardSupport'] );
		$this->settings['general']['arrowScroll'] = empty( $unsanitized_settings['general']['arrowScroll'] ) ? 50 : intval( $unsanitized_settings['general']['arrowScroll'] );
		$this->settings['general']['allowedBrowsers'] = empty( $unsanitized_settings['general']['allowedBrowsers'] ) ? [ 'IEWin7', 'Chrome', 'Safari' ] : array_intersect( [ 'Mobile', 'IEWin7', 'Edge', 'Chrome', 'Safari', 'Firefox', 'other' ], $unsanitized_settings['general']['allowedBrowsers'] );

		if( ! file_exists( $this->uploads['basedir'] . '/wpmss/wpmss.min.js' ) ){
			$this->save_js_config();
		}
	}

	function plugin_scripts_load(){
		wp_enqueue_script( 'wpmssab', $this->uploads['baseurl'] . '/wpmss/wpmssab.min.js', [], $this->settings['general']['timestamp'], 1 );
		wp_enqueue_script( 'SmoothScroll', plugins_url( 'js/SmoothScroll.min.js', __FILE__ ), ['wpmssab'], '1.5.1', 1 );
		wp_enqueue_script( 'wpmss', $this->uploads['baseurl'] . '/wpmss/wpmss.min.js', ['SmoothScroll'], $this->settings['general']['timestamp'], 1 );
	}

	function plugin_admin_tabs( $current = 'general' ){
		$tabs = [ 'general' => __('General'), 'info' => __('Help') ]; ?>
		<h2 class="nav-tab-wrapper">
		<?php foreach( $tabs as $tab => $name ){ ?>
			<a class="nav-tab <?php echo ( $tab == $current ) ? "nav-tab-active" : "" ?>" href="?page=<?php echo basename( __FILE__ ) ?>&amp;tab=<?php echo $tab ?>"><?php echo $name ?></a>
		<?php } ?>
		</h2><br><?php
	}

	function save_js_config(){
		if( ! file_exists( $this->uploads['basedir'] . '/wpmss' ) ){
			mkdir( $this->uploads['basedir'] . '/wpmss', 0777, true );
		}
		$allowedBrowsers = sprintf(
			'var allowedBrowsers=["%s"];',
			implode( '","', $this->settings['general']['allowedBrowsers'] )
		);
		file_put_contents( $this->uploads['basedir'] . '/wpmss/wpmssab.min.js', $allowedBrowsers );

		$content = sprintf(
			'SmoothScroll({'.
				'frameRate:%d,'.
				'animationTime:%d,'.
				'stepSize:%d,'.
				'pulseAlgorithm:%d,'.
				'pulseScale:%d,'.
				'pulseNormalize:%d,'.
				'accelerationDelta:%d,'.
				'accelerationMax:%d,'.
				'keyboardSupport:%d,'.
				'arrowScroll:%d,'.
			'})',
			intval( $this->settings['general']['frameRate'] ),
			intval( $this->settings['general']['animationTime'] ),
			intval( $this->settings['general']['stepSize'] ),
			intval( $this->settings['general']['pulseAlgorithm'] ),
			intval( $this->settings['general']['pulseScale'] ),
			intval( $this->settings['general']['pulseNormalize'] ),
			intval( $this->settings['general']['accelerationDelta'] ),
			intval( $this->settings['general']['accelerationMax'] ),
			intval( $this->settings['general']['keyboardSupport'] ),
			intval( $this->settings['general']['arrowScroll'] )
		);
		file_put_contents( $this->uploads['basedir'] . '/wpmss/wpmss.min.js', $content );
	}

	function admin_options_page(){
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
		if( ! empty( $_POST['wpmss_nonce'] ) && check_admin_referer( 'wpmss_data', 'wpmss_nonce' ) ){
			$this->settings['general'] = $_POST;
			$this->process_settings();
			update_option( 'wpmss_settings', $this->settings );
			$this->save_js_config();
		} ?>
		<div class="wrap">
			<h2><?php _e( 'MouseWheel Smooth Scroll', 'wpmss' ); ?></h2>
			<?php if( isset( $_POST['wpmss_nonce'] ) ) echo '<div id="message" class="below-h2 updated"><p>' . __('Settings saved.') . '</p></div>' ?>
			<form method="post" action="<?php admin_url( 'options-general.php?page=' . basename( __FILE__ ) ) ?>"><?php
				wp_nonce_field( 'wpmss_data', 'wpmss_nonce' );
				$this->plugin_admin_tabs( $tab );
				switch( $tab ):
					case 'general':
						$this->plugin_general_options();
						break;
					case 'info':
						$this->plugin_info_options();
						break;
				endswitch; ?>
			</form>
		</div><?php
	}

	function plugin_general_options(){ ?>
		<style>.default{color:#a0a5aa}</style>
		<input type="hidden" name="timestamp" value="<?php echo time() ?>">
		<table class="form-table">
			<tr>
				<th colspan="2">
					<h3><?php _e( 'Scrolling Core', 'wpmss' ) ?></h3>
				</th>
			</tr>
			<tr>
				<th>
					<label for="q_field_1"><?php _e( 'frameRate', 'wpmss' ) ?>:</label> 
				</th>
				<td>
					<input type="number" name="frameRate" placeholder="150" value="<?php echo $this->settings['general']['frameRate'] ?>" id="q_field_1">
					[Hz]&emsp;<small class="default"><?php _e( 'default:', 'wpmss' ) ?> 150</small>
				</td>
			</tr>
			<tr>
				<th>
					<label for="q_field_2"><?php _e( 'animationTime', 'wpmss' ) ?>:</label> 
				</th>
				<td>
					<input type="number" name="animationTime" placeholder="1000" value="<?php echo $this->settings['general']['animationTime'] ?>" id="q_field_2">
					[ms]&emsp;<small class="default"><?php _e( 'default:', 'wpmss' ) ?> 1000</small>
				</td>
			</tr>
			<tr>
				<th>
					<label for="q_field_3"><?php _e( 'stepSize', 'wpmss' ) ?>:</label> 
				</th>
				<td>
					<input type="number" name="stepSize" placeholder="100" value="<?php echo $this->settings['general']['stepSize'] ?>" id="q_field_3">
					[px]&emsp;<small class="default"><?php _e( 'default:', 'wpmss' ) ?> 100</small>
				</td>
			</tr>

			<tr>
				<th colspan="2">
					<h3><?php _e( 'Pulse (less tweakable)<br>ratio of "tail" to "acceleration"', 'wpmss' ) ?></h3>
				</th>
			</tr>
			<tr>
				<th>
					<label for="q_field_35"><?php _e( 'pulseAlgorithm', 'wpmss' ) ?>:</label> 
				</th>
				<td>
					<input type="hidden" name="pulseAlgorithm" value="0">
					<input type="checkbox" name="pulseAlgorithm" value="1" <?php echo $this->settings['general']['pulseAlgorithm'] ? 'checked="checked"' : '' ?> id="q_field_35">
					&emsp;<small class="default"><?php _e( 'default:', 'wpmss' ) ?> on</small>
				</td>
			</tr>
			<tr>
				<th>
					<label for="q_field_4"><?php _e( 'pulseScale', 'wpmss' ) ?>:</label> 
				</th>
				<td>
					<input type="number" name="pulseScale" placeholder="4" value="<?php echo $this->settings['general']['pulseScale'] ?>" id="q_field_4">
					&emsp;<small class="default"><?php _e( 'default:', 'wpmss' ) ?> 4</small>
				</td>
			</tr>
			<tr>
				<th>
					<label for="q_field_5"><?php _e( 'pulseNormalize', 'wpmss' ) ?>:</label> 
				</th>
				<td>
					<input type="number" name="pulseNormalize" placeholder="1" value="<?php echo $this->settings['general']['pulseNormalize'] ?>" id="q_field_5">
					&emsp;<small class="default"><?php _e( 'default:', 'wpmss' ) ?> 1</small>
				</td>
			</tr>

			<tr>
				<th colspan="2">
					<h3><?php _e( 'Acceleration', 'wpmss' ) ?></h3>
				</th>
			</tr>
			<tr>
				<th>
					<label for="q_field_6"><?php _e( 'accelerationDelta', 'wpmss' ) ?>:</label> 
				</th>
				<td>
					<input type="number" name="accelerationDelta" placeholder="50" value="<?php echo $this->settings['general']['accelerationDelta'] ?>" id="q_field_6">
					&emsp;<small class="default"><?php _e( 'default:', 'wpmss' ) ?> 50</small>
				</td>
			</tr>
			<tr>
				<th>
					<label for="q_field_7"><?php _e( 'accelerationMax', 'wpmss' ) ?>:</label> 
				</th>
				<td>
					<input type="number" name="accelerationMax" placeholder="3" value="<?php echo $this->settings['general']['accelerationMax'] ?>" id="q_field_7">
					&emsp;<small class="default"><?php _e( 'default:', 'wpmss' ) ?> 3</small>
				</td>
			</tr>

			<tr>
				<th colspan="2">
					<h3><?php _e( 'Keyboard Settings', 'wpmss' ) ?></h3>
				</th>
			</tr>
			<tr>
				<th>
					<label for="q_field_75"><?php _e( 'keyboardSupport', 'wpmss' ) ?>:</label> 
				</th>
				<td>
					<input type="hidden" name="keyboardSupport" value="0">
					<input type="checkbox" name="keyboardSupport" value="1" <?php echo $this->settings['general']['keyboardSupport'] ? 'checked="checked"' : '' ?> id="q_field_75">
					&emsp;<small class="default"><?php _e( 'default:', 'wpmss' ) ?> on</small>
				</td>
			</tr>
			<tr>
				<th>
					<label for="q_field_8"><?php _e( 'arrowScroll', 'wpmss' ) ?>:</label> 
				</th>
				<td>
					<input type="number" name="arrowScroll" placeholder="50" value="<?php echo $this->settings['general']['arrowScroll'] ?>" id="q_field_8">
					[px]&emsp;<small class="default"><?php _e( 'default:', 'wpmss' ) ?> 50</small>
				</td>
			</tr>

			<tr>
				<th colspan="2">
					<h3><?php _e( 'Other', 'wpmss' ) ?></h3>
				</th>
			</tr>
			<tr>
				<th>
					<label for="q_field_11"><?php _e( 'allowedBrowsers', 'wpmss' ) ?>:</label> 
				</th>
				<td>
					<select name="allowedBrowsers[]" id="q_field_11" multiple="multiple" style="height:150px">
						<?php foreach([
							'Mobile' => 'mobile browsers',
							'IEWin7' => 'IEWin7',
							'Edge' => 'Edge',
							'Chrome' => 'Chrome',
							'Safari' => 'Safari',
							'Firefox' => 'Firefox',
							'other' => 'all other browsers',
						] as $key => $value ){
							echo '<option value="' . $key . '"' . ( in_array( $key, $this->settings['general']['allowedBrowsers'] ) ? ' selected="selected"' : '' ) . '>' . $value . '</option>';
						} ?>
					</select>
					&emsp;<small class="default"><?php _e( 'default:', 'wpmss' ) ?> IEWin7, Chrome, Safari</small>
				</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" class="button button-primary button-large" value="<?php _e('Save') ?>"></p><?php
	}

	function plugin_info_options(){ ?>
		<p>This plugin is only WordPress implementation of JS script from <strong title="Blaze (Balázs Galambosi)">gblazex</strong>.</p>
		<p>Find more <a href="https://github.com/gblazex/smoothscroll-for-websites" target="_blank">on Github</a></p><?php
	}
}

$wpmss = new wpmss();