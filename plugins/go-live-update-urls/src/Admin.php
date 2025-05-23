<?php

namespace Go_Live_Update_Urls;

use Go_Live_Update_Urls\Traits\Singleton;

/**
 * Tools page in WordPress admin.
 *
 * @author OnPoint Plugins
 * @since  6.0.0
 */
class Admin {
	use Singleton;

	public const NAME = 'go-live-update-urls-settings';

	public const PARENT_MENU      = 'tools.php';
	public const OLD_URL          = 'old_url';
	public const NEW_URL          = 'new_url';
	public const NONCE            = 'go-live-update-urls/nonce/update-tables';
	public const TABLE_INPUT_NAME = 'go-live-update-urls/input/database-table';
	public const SUBMIT           = 'go-live-update-urls/input/submit';

	protected const CAPABILITY = 'manage_options';


	/**
	 * Add actions.
	 */
	protected function hook(): void {
		if ( isset( $_POST[ static::SUBMIT ] ) ) { //phpcs:ignore WordPress.Security.NonceVerification
			add_action( 'init', [ $this, 'validate_update_submission' ] );
		}

		add_action( 'admin_menu', [ $this, 'register_admin_page' ] );
	}


	/**
	 * Validate and trigger an update submission
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function validate_update_submission(): void {
		if ( ! isset( $_POST[ static::NONCE ] ) || false === wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ static::NONCE ] ) ), static::NONCE ) ) {
			wp_die( esc_html__( 'Ouch! That hurt! You should not be here!', 'go-live-update-urls' ) );
		}

		if ( ! isset( $_POST[ static::OLD_URL ], $_POST[ static::NEW_URL ] ) || '' === $_POST[ static::OLD_URL ] || '' === $_POST[ static::NEW_URL ] ) {
			$this->failure_message();
			return;
		}

		$old_url = go_live_update_urls_sanitize_field( (string) $_POST[ static::OLD_URL ] );
		$new_url = go_live_update_urls_sanitize_field( (string) $_POST[ static::NEW_URL ] );
		if ( '' === $old_url || '' === $new_url || ! isset( $_POST[ static::TABLE_INPUT_NAME ] ) ) {
			$this->failure_message();
			return;
		}

		$tables = \array_filter( \array_map( 'go_live_update_urls_sanitize_field', (array) $_POST[ static::TABLE_INPUT_NAME ] ), fn( $value ) => '' !== $value );

		do_action( 'go-live-update-urls/admin-page/before-update', $old_url, $new_url, $tables );

		if ( \count( Database::instance()->update_the_database( $old_url, $new_url, $tables ) ) > 0 ) {
			add_action( 'admin_notices', [ $this, 'success' ] );
			add_filter( 'go-live-update-urls/views/admin-tools-page/disable-description', '__return_true' );
		}
	}


	/**
	 * Render a success message as admin banner.
	 */
	public function success(): void {
		?>
		<div id="message" class="updated fade">
			<p>
				<strong>
					<?php echo esc_html( apply_filters( 'go-live-update-urls/admin/success', __( 'The urls in the checked tables have been updated.', 'go-live-update-urls' ) ) ); ?>
				</strong>
			</p>
		</div>
		<?php
	}


	/**
	 * Display a message if any fields were not filed out.
	 *
	 * @return void
	 */
	public function failure_message(): void {
		add_action( 'admin_notices', function() {
			?>
			<div id="message" class="error fade">
				<p>
					<strong>
						<?php esc_html_e( 'You must select tables and fill out both the Old URL and New URL to update urls!', 'go-live-update-urls' ); ?>
					</strong>
				</p>
			</div>
			<?php
		} );
	}


	/**
	 * Menu Under Tools Menu
	 *
	 * @since 5.0.0
	 */
	public function register_admin_page(): void {
		add_submenu_page( self::PARENT_MENU, 'Go Live Update Urls', 'Go Live', $this->get_admin_capability(), self::NAME, [ $this, 'admin_page' ] );
	}


	/**
	 * Get the filtered capability required to use the tools page.
	 *
	 * @since 6.9.0
	 *
	 * @return string
	 */
	public function get_admin_capability(): string {
		return (string) apply_filters( 'go-live-update-urls/admin/admin-capability', self::CAPABILITY, $this );
	}


	/**
	 * Render the tools page header.
	 *
	 * This is the same header used by the PRO version.
	 *
	 * @param string $url - URL to link the plugin name to.
	 *
	 * @return void
	 */
	public function render_admin_header( string $url ): void {
		?>
		<div class="go-live-header-wrap">
			<div>
				<h1 class="dashicons-before dashicons-update">
					<a
						href="<?php echo esc_url( $url ); ?>"
						target="_blank"
						rel="noopener noreferrer">
						<?php esc_html_e( 'Go Live Update Urls', 'go-live-update-urls' ); ?>
					</a>
				</h1>
			</div>
			<div class="go-live-header-message">
				<?php esc_html_e( 'Replaces all occurrences in the entire database of the Old URL with a New URL.', 'go-live-update-urls' ); ?>
			</div>
		</div>
		<?php
	}


	/**
	 * Output the Admin Page for using this plugin
	 *
	 * @since 5.0.0
	 */
	public function admin_page(): void {
		wp_enqueue_script( 'go-live-update-urls/admin/admin-page/js', GO_LIVE_UPDATE_URLS_URL . 'resources/go-live-update-urls.js', [ 'jquery' ], GO_LIVE_UPDATE_URLS_VERSION, true );
		wp_enqueue_style( 'go-live-update-urls/admin/admin-page/css', GO_LIVE_UPDATE_URLS_URL . 'resources/go-live-update-urls.css', [], GO_LIVE_UPDATE_URLS_VERSION );

		?>
		<div id="go-live-update-urls/admin-page">
			<?php
			$this->render_admin_header( 'https://wordpress.org/plugins/go-live-update-urls/' );
			?>
			<form
				method="post"
				class="go-live-checkbox-form">
				<?php
				wp_nonce_field( static::NONCE, static::NONCE );

				do_action( 'go-live-update-urls-pro/admin/before-checkboxes', Database::instance() );

				if ( apply_filters( 'go-live-update-urls-pro/admin/use-default-checkboxes', true ) ) {
					?>
					<h3>
						<?php esc_html_e( 'WordPress Core Tables', 'go-live-update-urls' ); ?>
					</h3>
					<div class="go-live-section">
						<p class="description" style="color:green;">
							<strong>
								<?php esc_attr_e( 'These tables are safe to update.', 'go-live-update-urls' ); ?>
							</strong>
						</p>
						<p>
							<input
								type="checkbox"
								data-list="wp-core"
								data-js="go-live-update-urls/checkboxes/check-all"
								checked
							/>
							<span class="go-live-only-checked"><?php esc_html_e( 'Only the checked tables will be updated.', 'go-live-update-urls' ); ?></span>
						</p>
						<hr />

						<?php
						$this->render_check_boxes( Database::instance()->get_core_tables(), 'wp-core' );
						?>
					</div>
					<?php

					$custom_tables = Database::instance()->get_custom_plugin_tables();
					if ( \count( $custom_tables ) > 0 ) {
						?>
						<h3>
							<?php esc_html_e( 'Tables Created By Plugins', 'go-live-update-urls' ); ?>
						</h3>
						<div class="go-live-section">
							<p class="description" style="color:red;">
								<strong>
									<?php
									/* translators: <br /> <a> </a> */
									printf( esc_html_x( 'These tables are not safe to update with the basic version of this plugin! %1$sTo update tables created by plugins, use the %2$sPRO version.%3$s', '{<br />}{<a>}{</a>}', 'go-live-update-urls' ), '<br />', '<a href="https://onpointplugins.com/product/go-live-update-urls-pro/?utm_source=plugin-tables&utm_campaign=gopro&utm_medium=wp-dash" target="_blank">', '</a>' );
									?>
								</strong>
							</p>
							<p>
								<input
									type="checkbox"
									data-list="custom-plugins"
									data-js="go-live-update-urls/checkboxes/check-all" />
								<span class="go-live-only-checked"><?php esc_html_e( 'Only the checked tables will be updated.', 'go-live-update-urls' ); ?></span>
							</p>
							<hr />

							<?php
							$this->render_check_boxes( $custom_tables, 'custom-plugins', false );
							?>
						</div>
						<?php
					}
				}

				do_action( 'go-live-update-urls-pro/admin/after-checkboxes', Database::instance() );

				if ( apply_filters( 'go-live-update-urls-pro/admin/use-default-inputs', true ) ) {
					?>
					<table class="form-table go-live-inputs">
						<tr class="go-live-inputs-old-url">
							<th scope="row">
								<label for="old_url">
									<?php esc_html_e( 'Old URL', 'go-live-update-urls' ); ?>
								</label>
							</th>
							<td>
								<input
									name="<?php echo esc_attr( static::OLD_URL ); ?>"
									type="text"
									id="old_url"
									value=""
									class="regular-text"
									title="<?php esc_attr_e( 'Old URL', 'go-live-update-urls' ); ?>" />
							</td>
						</tr>
						<tr class="go-live-inputs-new-url">
							<th scope="row">
								<label for="new_url">
									<?php esc_attr_e( 'New URL', 'go-live-update-urls' ); ?>
								</label>
							</th>
							<td>
								<input
									name="<?php echo esc_attr( static::NEW_URL ); ?>"
									type="text"
									id="new_url"
									value=""
									class="regular-text"
									title="<?php esc_attr_e( 'New URL', 'go-live-update-urls' ); ?>" />
							</td>
						</tr>
					</table>


					<?php
				}
				if ( apply_filters( 'go-live-update-urls-pro/admin/use-default-checkboxes', true ) ) {
					?>
					<p class="description">
						<strong>

							<?php
							/* translators: <a></a> */
							printf( esc_html_x( 'Use the %1$sPRO version%2$s to test URL updates before making them.', '{<a>}{</a>}', 'go-live-update-urls' ), '<a href="https://onpointplugins.com/go-live-update-urls/go-live-update-urls-pro-usage/go-live-update-urls-pro-url-testing/?utm_source=url-test&utm_campaign=gopro&utm_medium=wp-dash" target="_blank">', '</a>' );
							?>

						</strong>
					</p>
					<?php
				}
				?>
				<?php submit_button( __( 'Update Urls', 'go-live-update-urls' ), 'primary', static::SUBMIT ); ?>
			</form>
		</div>
		<?php
	}


	/**
	 * Creates a list of checkboxes for each table
	 *
	 * @since  5.0.0
	 *
	 * @param string[] $tables  - List of all tables.
	 * @param string   $list_id - Used by JS to separate lists.
	 * @param bool     $checked - Should all checkboxes be checked.
	 *
	 * @return void
	 */
	public function render_check_boxes( array $tables, string $list_id, bool $checked = true ): void {
		?>
		<ul data-list="<?php echo esc_attr( $list_id ); ?>">
			<?php
			foreach ( $tables as $_table ) {
				?>
				<li>
					<?php
					\printf( '<input name="%s[]" type="checkbox" value="%s" %s/> %s', esc_attr( static::TABLE_INPUT_NAME ), esc_attr( $_table ), checked( $checked, true, false ), esc_html( $_table ) );
					?>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
	}


	/**
	 * Get the URL of the tools page.
	 *
	 * @return string
	 */
	public function get_url(): string {
		return admin_url( 'tools.php?page=' . static::NAME );
	}
}
