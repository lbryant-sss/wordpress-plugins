<?php
/**
 * 'Form' class file.
 *
 * @package hcaptcha-wp
 */

// phpcs:ignore Generic.Commenting.DocComment.MissingShort
/** @noinspection PhpUndefinedClassInspection */

namespace HCaptcha\CoBlocks;

use CoBlocks_Form;
use HCaptcha\Helpers\API;
use HCaptcha\Helpers\HCaptcha;
use WP_Block;
use WP_Error;

/**
 * Class Form.
 */
class Form {

	/**
	 * Nonce action.
	 */
	private const ACTION = 'hcaptcha_coblocks';

	/**
	 * Nonce name.
	 */
	private const NONCE = 'hcaptcha_coblocks_nonce';

	/**
	 * Fake hCaptcha token.
	 */
	private const HCAPTCHA_DUMMY_TOKEN = 'hcaptcha_token';

	/**
	 * Error message.
	 *
	 * @var string|null
	 */
	private $error_message;

	/**
	 * Form constructor.
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Init hooks.
	 *
	 * @return void
	 */
	protected function init_hooks(): void {
		add_filter( 'render_block', [ $this, 'add_hcaptcha' ], 10, 3 );
		add_filter( 'render_block_data', [ $this, 'render_block_data' ], 10, 3 );
		add_action( 'wp_head', [ $this, 'print_inline_styles' ] );
	}

	/**
	 * Add hcaptcha to CoBlocks form.
	 *
	 * @param string|mixed $block_content The block content.
	 * @param array        $block         The full block, including name and attributes.
	 * @param WP_Block     $instance      The block instance.
	 *
	 * @return string
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function add_hcaptcha( $block_content, array $block, WP_Block $instance ): string {
		$block_content = (string) $block_content;

		if ( 'coblocks/form' !== $block['blockName'] ) {
			return $block_content;
		}

		$form_id = 0;

		if ( preg_match( '/<div class="coblocks-form" id="(.+)">/', $block_content, $m ) ) {
			$form_id = $m[1];
		}

		$search  = '<button type="submit" ';
		$args    = [
			'action' => self::ACTION,
			'name'   => self::NONCE,
			'id'     => [
				'source'  => HCaptcha::get_class_source( __CLASS__ ),
				'form_id' => $form_id,
			],
		];
		$message = $this->error_message ? '<div class="h-captcha-error">' . $this->error_message . '</div>' . "\n" : '';
		$replace = $message . HCaptcha::form( $args ) . "\n";

		return str_replace( $search, $replace . $search, $block_content );
	}

	/**
	 * Render block context filter.
	 * CoBlocks have no filters in form processing.
	 * So, we need to do some tricks.
	 *
	 * @since WP 5.1.0
	 *
	 * @param array|mixed $parsed_block The block being rendered.
	 * @param array       $source_block An unmodified copy of $parsed_block, as it appeared in the source content.
	 *
	 * @return array
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function render_block_data( $parsed_block, array $source_block ): array {
		static $filters_added;

		if ( $filters_added ) {
			return $parsed_block;
		}

		$parsed_block = (array) $parsed_block;
		$block_name   = $parsed_block['blockName'] ?? '';

		if ( 'coblocks/form' !== $block_name ) {
			return $parsed_block;
		}

		// Nonce is checked by CoBlocks.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$form_submission = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';

		if ( 'coblocks-form-submit' !== $form_submission ) {
			return $parsed_block;
		}

		// We cannot add filters right here.
		// In this case, the calculation of form hash in the coblocks_render_coblocks_form_block() will fail.
		add_action( 'coblocks_before_form_submit', [ $this, 'before_form_submit' ], 10, 2 );

		$filters_added = true;

		return $parsed_block;
	}

	/**
	 * Before form submitting action.
	 *
	 * @param array $post User submitted form data.
	 * @param array $atts Form block attributes.
	 *
	 * @return void
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function before_form_submit( array $post, array $atts ): void {
		add_filter( 'pre_option_coblocks_google_recaptcha_site_key', '__return_true' );
		add_filter( 'pre_option_coblocks_google_recaptcha_secret_key', '__return_true' );

		$_POST['g-recaptcha-token'] = self::HCAPTCHA_DUMMY_TOKEN;

		add_filter( 'pre_http_request', [ $this, 'verify' ], 10, 3 );
	}

	/**
	 * Verify the hCaptcha.
	 *
	 * @param false|array|WP_Error $response    A preemptive return value of an HTTP request. Default false.
	 * @param array                $parsed_args HTTP request arguments.
	 * @param string               $url         The request URL.
	 *
	 * @return array|WP_Error
	 */
	public function verify( $response, array $parsed_args, string $url ) {
		if (
			CoBlocks_Form::GCAPTCHA_VERIFY_URL !== $url ||
			self::HCAPTCHA_DUMMY_TOKEN !== $parsed_args['body']['response']
		) {
			return $response;
		}

		remove_filter( 'pre_http_request', [ $this, 'verify' ] );

		// Nonce is checked in API::verify().
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$this->error_message = API::verify( $this->get_entry( $_POST ) );

		if ( null === $this->error_message ) {
			return [
				'body'     => '{"success":true}',
				'response' =>
					[
						'code'    => 200,
						'message' => 'OK',
					],
			];
		}

		return [
			'body'     => '{"success":false}',
			'response' =>
				[
					'code'    => 200,
					'message' => 'OK',
				],
		];
	}

	/**
	 * Print inline styles.
	 *
	 * @return void
	 * @noinspection CssUnusedSymbol
	 */
	public function print_inline_styles(): void {
		/* language=CSS */
		$css = '
	.wp-block-coblocks-form .h-captcha-error {
		color: red;
		margin-bottom: 25px;
	}

	.wp-block-coblocks-form .h-captcha {
		margin-bottom: 25px;
	}
';

		HCaptcha::css_display( $css );
	}

	/**
	 * Get entry.
	 *
	 * @param array $form_data Form data.
	 *
	 * @return array
	 */
	private function get_entry( array $form_data ): array {
		global $post;

		$entry = [
			'nonce_name'         => self::NONCE,
			'nonce_action'       => self::ACTION,
			'h-captcha-response' => $form_data['h-captcha-response'] ?? '',
			'form_date_gmt'      => $post->post_modified_gmt ?? null,
			'data'               => [],
		];
		$name  = [];

		foreach ( $form_data as $key => $field ) {
			if ( ! preg_match( '/field-(.+)/', $key, $m ) ) {
				continue;
			}

			$type = $m[1];

			if ( ! in_array( $type, [ 'name', 'email', 'message' ], true ) ) {
				continue;
			}

			$field = wp_parse_args(
				$field,
				[
					'label' => '',
					'value' => '',
				]
			);

			$label = $field['label'];
			$value = $field['value'];

			if ( 'name' === $type ) {
				$name[] = $value;
			}

			if ( 'email' === $type ) {
				$entry['data']['email'] = $value;
			}

			$entry['data'][ $label ] = $value;
		}

		$entry['data']['name'] = implode( ' ', $name ) ?: null;

		return $entry;
	}
}
