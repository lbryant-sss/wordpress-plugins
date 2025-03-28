<?php

namespace Templately\Core\Importer\Runners;

use Exception;
use Templately\Core\Importer\Form;
use Templately\Core\Importer\Runners\BaseRunner;

class ExtraContent extends BaseRunner {

	protected static $imported_form = [];

	public function get_name(): string {
		return 'extra-content';
	}

	public function get_label(): string {
		return __( 'Extra Contents', 'templately' );
	}

	public function should_log(): bool {
		return true;
	}

	public function get_action(): string {
		return 'eventLog';
	}

	public function log_message(): string {
		return __( 'Importing Forms and Others Extra Contents', 'templately' );
	}

	public function should_run( $data, $imported_data = [] ): bool {
		return ! empty( $this->manifest['extra-content'] ) && !empty($data['import_demo_content']);
	}

	public function import( $data, $imported_data ): array {
		$contents      = $this->manifest['extra-content'] ?? [];

		$extra_content = $this->loop( $contents, function($type, $content ) {
			$extra_content = [];
			switch ( $type ) {
				case 'form':
					$import                = $this->import_form( $content );
					$extra_content['form'] = $import;
					break;
				case 'data':
					break;
			}
			return $extra_content;
		});

		return  [ 'extra-content' => $extra_content ];
	}

	private function import_form( $contents ): array {
		if( ! is_array( $contents ) || empty( $contents ) ) {
			return [];
		}

		$result = [];

		$root_path = $this->dir_path . 'content' . DIRECTORY_SEPARATOR;

		foreach( $contents as $json_id => $plugin_list ) {
			if( empty( $plugin_list ) ) {
				continue;
			}

			foreach ( $plugin_list as $plugin_name => $form_list ) {
				if( empty( $form_list ) ) {
					continue;
				}

				foreach ( $form_list as $form ) {
					try {
						$form_id = isset( $form['identifier'] ) ? $form['settings'][$form['identifier']] : $form['settings']['form_list'];
						if(isset(self::$imported_form[ $plugin_name ][ $form_id ])){
							$result[$json_id][ $plugin_name ][ $form['id'] ] = self::$imported_form[ $plugin_name ][ $form_id ];
							continue;
						}

						$file_path = $root_path . wp_unslash( $form['file_path'] );
						$import = new Form( $plugin_name, $file_path, $form );
						$data = $import->run();

						self::$imported_form[ $plugin_name ][ $form_id ] = (string) $data;
						$result[$json_id][ $plugin_name ][ $form['id'] ] = (string) $data;
					} catch ( Exception $e ) {
						continue;
					}
				}
			}
		}

		return $result;
	}
}