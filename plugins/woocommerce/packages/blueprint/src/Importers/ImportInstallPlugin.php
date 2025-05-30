<?php

namespace Automattic\WooCommerce\Blueprint\Importers;

use Automattic\WooCommerce\Blueprint\ResourceStorages;
use Automattic\WooCommerce\Blueprint\StepProcessor;
use Automattic\WooCommerce\Blueprint\StepProcessorResult;
use Automattic\WooCommerce\Blueprint\Steps\InstallPlugin;
use Automattic\WooCommerce\Blueprint\UseWPFunctions;
use Plugin_Upgrader;

/**
 * Class ImportInstallPlugin
 *
 * Handles the installation and activation of plugins based on a schema.
 */
class ImportInstallPlugin implements StepProcessor {
	use UseWPFunctions;

	/**
	 * Resource storage instance for handling plugin files.
	 *
	 * @var ResourceStorages
	 */
	private ResourceStorages $storage;

	/**
	 * Array of paths to installed plugins.
	 *
	 * @var array
	 */
	private array $installed_plugin_paths = array();

	/**
	 * Constructor.
	 *
	 * @param ResourceStorages $storage Resource storage instance.
	 */
	public function __construct( ResourceStorages $storage ) {
		$this->storage = $storage;
	}

	/**
	 * Processes the schema to install and optionally activate a plugin.
	 *
	 * @param object $schema Schema object containing plugin information.
	 * @return StepProcessorResult Result of the processing.
	 */
	public function process( $schema ): StepProcessorResult {
		$result = StepProcessorResult::success( InstallPlugin::get_step_name() );

		$installed_plugins = $this->get_installed_plugins_paths();

		// phpcs:ignore
		$plugin = $schema->pluginData;

		// We only support CorePluginReference at the moment.
		if ( 'wordpress.org/plugins' !== $plugin->resource ) {
			$result->add_info( "Skipped installing a plugin. Unsupported resource type. Only 'wordpress.org/plugins' is supported at the moment." );
			return $result;
		}

		// If the plugin is already installed, skip the installation.
		if ( isset( $installed_plugins[ $plugin->slug ] ) ) {
			$result->add_info( "Skipped installing {$plugin->slug}. It is already installed." );
			return $result;
		}

		// If the resource type is not supported, return an error.
		if ( $this->storage->is_supported_resource( $plugin->resource ) === false ) {
			$result->add_error( "Invalid resource type for {$plugin->slug}." );
			return $result;
		}

		// Download the plugin.
		$downloaded_path = $this->storage->download( $plugin->slug, $plugin->resource );
		if ( ! $downloaded_path ) {
			$result->add_error( "Unable to download {$plugin->slug} with {$plugin->resource} resource type." );
			return $result;
		}

		// Install the plugin.
		$install = $this->install( $downloaded_path );

		if ( is_wp_error( $install ) ) {
			$result->add_error( "Failed to install {$plugin->slug}." );
			return $result;
		}

		$result->add_info( "Installed {$plugin->slug}." );

		// If the plugin should be activated, activate it.
		$should_activate = isset( $schema->options, $schema->options->activate ) && true === $schema->options->activate;
		if ( $should_activate ) {
			$activate = $this->activate( $plugin->slug );

			if ( $activate instanceof \WP_Error ) {
				$result->add_error( "Failed to activate {$plugin->slug}." );
				return $result;
			}

			$result->add_info( "Activated {$plugin->slug}." );
		}

		return $result;
	}

	/**
	 * Installs a plugin from the given local path.
	 *
	 * @param string $local_plugin_path Path to the local plugin file.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	protected function install( $local_plugin_path ) {
		if ( ! class_exists( 'Plugin_Upgrader' ) ) {
			include_once ABSPATH . '/wp-admin/includes/class-wp-upgrader.php';
			include_once ABSPATH . '/wp-admin/includes/class-plugin-upgrader.php';
		}

		$upgrader = new \Plugin_Upgrader( new \Automatic_Upgrader_Skin() );
		return $upgrader->install( $local_plugin_path );
	}

	/**
	 * Activates an installed plugin by its slug.
	 *
	 * @param string $slug Plugin slug.
	 * @return \WP_Error|null WP_Error on failure, null on success.
	 */
	protected function activate( $slug ) {
		if ( empty( $this->installed_plugin_paths ) ) {
			$this->installed_plugin_paths = $this->get_installed_plugins_paths();
		}

		$path = $this->installed_plugin_paths[ $slug ] ?? false;

		if ( ! $path ) {
			return new \WP_Error( 'plugin_not_installed', "Plugin {$slug} is not installed." );
		}

		return $this->wp_activate_plugin( $path );
	}

	/**
	 * Retrieves an array of installed plugins and their paths.
	 *
	 * @return array Array of installed plugins and their paths.
	 */
	protected function get_installed_plugins_paths() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins           = get_plugins();
		$installed_plugins = array();

		foreach ( $plugins as $path => $plugin ) {
			$path_parts                 = explode( '/', $path );
			$slug                       = $path_parts[0];
			$installed_plugins[ $slug ] = $path;
		}

		return $installed_plugins;
	}

	/**
	 * Returns the class name of the step being processed.
	 *
	 * @return string Class name of the step.
	 */
	public function get_step_class(): string {
		return InstallPlugin::class;
	}

	/**
	 * Check if the current user has the required capabilities for this step.
	 *
	 * @param object $schema The schema to process.
	 *
	 * @return bool True if the user has the required capabilities. False otherwise.
	 */
	public function check_step_capabilities( $schema ): bool {
		return current_user_can( 'install_plugins' );
	}
}
