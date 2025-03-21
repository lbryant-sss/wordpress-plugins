<?php
/**
 * Package Manager
 *
 * @package  VK All in One Expansion Unit
 */

// パッケージリストの取得.
require VEU_DIRECTORY_PATH . '/veu-packages.php';

veu_package_initilate();

/**
 * パッケージの初期化
 */
function veu_package_initilate() {
	global $vkExUnit_packages;
	if ( ! is_array( $vkExUnit_packages ) ) {
		$vkExUnit_packages = array();
	}
}

/**
 * パッケージの初期化
 */
function veu_package_init() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	do_action( 'veu_package_init' );
}
add_action( 'init', 'veu_package_init' );

/**
 * パッケージが有効か否か
 *
 * @param string $package_name パッケージ名.
 */
function veu_package_is_enable( $package_name ) {
	// パッケージ情報を取得.
	global $vkExUnit_packages;

	// パッケージ情報に パッケージ名 が存在しなかった場合はnullを返す.
	if ( ! isset( $vkExUnit_packages[ $package_name ] ) ) {
		return null; }

	// 共通設定（有効化情報）を読み込む.
	$options = veu_get_common_options();

	// 保存されている共通設定データにパッケージ名が存在しない場合.
	if ( ! isset( $options[ 'active_' . $package_name ] ) ) {
		// 初期情報のデータを返す.
		return $vkExUnit_packages[ $package_name ]['default'];
	}
	return $options[ 'active_' . $package_name ];
}

/**
 * パッケージの登録
 *
 * @param array $args パッケージ情報.
 */
function veu_package_register( $args ) {
	$defaults = veu_package_default();
	$args     = wp_parse_args( $args, $defaults );

	global $vkExUnit_packages;
	$vkExUnit_packages[ $args['name'] ] = $args;
}

/**
 * パッケージの有効化
 */
function veu_package_include() {
	global $vkExUnit_packages;
	if ( ! count( $vkExUnit_packages ) || ! is_array( $vkExUnit_packages ) ) {
		return; }
	$options      = veu_get_common_options();
	$include_base = VEU_DIRECTORY_PATH . '/inc/';

	$use_ex_blocks = false;

	foreach ( $vkExUnit_packages as $package ) {
		if (
			$package['include'] &&
			(
				( isset( $options[ 'active_' . $package['name'] ] ) && $options[ 'active_' . $package['name'] ] ) ||
				( ! isset( $options[ 'active_' . $package['name'] ] ) && $package['default'] )
			)
		) {
			require_once $include_base . $package['include'];

			if ( $package['use_ex_blocks'] ) {
				$use_ex_blocks = true;
			}
		}
	}

	if ( $use_ex_blocks ) {
		add_action(
			'init',
			function () {
				// WordPress 6.5 以下の対策.
				if ( ! wp_script_is( 'react-jsx-runtime', 'registered' ) ) {
					wp_register_script(
						'react-jsx-runtime',
						plugins_url( 'assets/js/react-jsx-runtime.js', __FILE__ ),
						array( 'react' ),
						'18.3.1',
						true
					);
				}
			}
		);
		// ver5.8.0 block_categories_all.
		if ( function_exists( 'get_default_block_categories' ) && function_exists( 'get_block_editor_settings' ) ) {
			add_filter( 'block_categories_all', 'veu_add_block_category', 10, 2 );
		} else {
			add_filter( 'block_categories', 'veu_add_block_category', 10, 2 );
		}
	}
}

/**
 * ブロックカテゴリーの追加
 *
 * @param array $categories カテゴリー.
 * @param array $post       投稿.
 */
function veu_add_block_category( $categories, $post ) {
	$categories = array_merge(
		$categories,
		array(
			array(
				'slug'  => 'veu-block',
				'title' => veu_get_prefix() . __( 'ExUnit Blocks', 'vk-all-in-one-expansion-unit' ),
				// 'icon'  => 'layout',
			),
		)
	);
	return $categories;
}

/**
 * パッケージのデフォルト値
 */
function veu_package_default() {
	return array(
		'name'          => null,
		'title'         => 'noting',
		'description'   => 'noting',
		'attr'          => array(),
		'default'       => null,
		'include'       => false,
		'use_ex_blocks' => false,
		'hidden'        => false,
	);
}

/**
 * パッケージの有効化情報を保存
 *
 * @param array $output 保存するデータ.
 * @param array $input  入力データ.
 */
function veu_common_package_options_validate( $output, $input ) {
	global $vkExUnit_packages;
	if ( ! count( $vkExUnit_packages ) || ! is_array( $vkExUnit_packages ) ) {
		return $output; }
	foreach ( $vkExUnit_packages as $package ) {
		if (
			( isset( $output[ 'active_' . $package['name'] ] ) && isset( $input[ 'active_' . $package['name'] ] ) &&
			$output[ 'active_' . $package['name'] ] === $input[ 'active_' . $package['name'] ] ) ? true : false
		) {
			continue; }
		$output[ 'active_' . $package['name'] ] = ( isset( $input[ 'active_' . $package['name'] ] ) ) ? true : false;
	}
	return $output;
}
add_filter( 'vkExUnit_common_options_validate', 'veu_common_package_options_validate', 10, 2 );

foreach ( $required_packages as $package ) {
	veu_package_register( $package );
}
