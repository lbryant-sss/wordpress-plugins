<?php

namespace Wpmet\Libs;

use MetForm\Plugin as Plugin;

defined('ABSPATH') || exit;

if(!class_exists('\Wpmet\Libs\Pro_Awareness')) :

	class Pro_Awareness
    {

		private static $instance;

		private $text_domain;
		private $plugin_file;
		private $parent_menu_slug;
		private $menu_slug = '_get_help';
		private $default_grid_link  = 'https://wpmet.com/support-ticket';
		private $default_grid_title = 'Support Center';
		private $default_grid_thumbnail = '';
		private $default_grid_desc  = 'Our experienced support team is ready to resolve your issues any time.';
		private $pro_link_conf      = [];

		private $grids = [];
		private $action_links = [];
		private $row_meta_links = [];
		private $parent_menu_text = 'Get Help';
		private $products = [];


		protected $script_version = '1.2.0';

		/**
		 * Get version of this script
		 *
		 * @return string Version name
		 */
		public function get_version() {
			return $this->script_version;
		}

		/**
		 * Get current directory path
		 *
		 * @return string
		 */
		public function get_script_location() {
			return __FILE__;
		}


		public static function instance($text_domain) {

			self::$instance = new self();

			return self::$instance->set_text_domain($text_domain);
		}

		protected function set_text_domain($val) {

			$this->text_domain = $val;

			return $this;
		}

		private function default_grid() {

			return [
				'url'         => $this->default_grid_link,
				'title'       => $this->default_grid_title,
				'thumbnail'   => $this->default_grid_thumbnail,
				'description' => $this->default_grid_desc,
			];
		}

		public function set_parent_menu_text($text) {

		    $this->parent_menu_text = $text;

		    return $this;
        }

		public function set_default_grid_link($url) {

			$this->default_grid_link = $url;

			return $this;
		}

		public function set_default_grid_title($title) {

			$this->default_grid_title = $title;

			return $this;
		}

		public function set_default_grid_desc($title) {

			$this->default_grid_desc = $title;

			return $this;
		}

		public function set_default_grid_thumbnail($thumbnail) {

			$this->default_grid_thumbnail = $thumbnail;

			return $this;
		}

		public function set_parent_menu_slug($slug) {

			$this->parent_menu_slug = $slug;

			return $this;
		}


		public function set_menu_slug($slug) {

			$this->menu_slug = $slug;

			return $this;
		}

		public function set_plugin_file($plugin_file) {

			$this->plugin_file = $plugin_file;

			return $this;
		}

		public function set_pro_link($url, $conf = []) {

			if($url == '') {
				return $this;
			}

			$this->pro_link_conf[] = [
				'url'        => $url,
				'anchor'     => empty($conf['anchor']) ? '<span style="color: #FCB214;" class="pro_aware pro">Upgrade To Premium</span>' : $conf['anchor'],
				'permission' => empty($conf['permission']) ? 'manage_options' : $conf['permission'],
			];

			return $this;
		}

		/**
		 * Set page grid
		 */
		public function set_page_grid($conf = []) {

			if(!empty($conf['url'])) {

				$this->grids[] = [
					'url'         => $conf['url'],
					'title'       => empty($conf['title']) ? esc_html__('Default Title', 'metform') : $conf['title'],
					'thumbnail'   => empty($conf['thumbnail']) ? '' : esc_url($conf['thumbnail']),
					'description' => empty($conf['description']) ? '' : $conf['description'],
				];
			}

			return $this;
		}

		/**
		 *  Set wpmet products
		 */
		public function set_products( $product = [] ) {			
			$this->products[] = [
				'url' => empty( $product['url'] ) ? '' : esc_url( $product['url'] ),
				'title'       => empty( $product['title'] ) ? esc_html__( 'Default Title', 'metform' ) : $product['title'],
				'thumbnail'   => empty( $product['thumbnail'] ) ? '' : esc_url( $product['thumbnail'] ),
				'description' => empty( $product['description'] ) ? '' : $product['description'],
			];

			return $this;
		}

		protected function prepare_pro_links() {

			if(!empty($this->pro_link_conf)) {

				foreach($this->pro_link_conf as $conf) {

					add_submenu_page($this->parent_menu_slug, $conf['anchor'], $conf['anchor'], $conf['permission'], $conf['url'], '');
				}
			}
		}

		protected function prepare_grid_links() {

			if(!empty($this->grids)) {

				add_submenu_page($this->parent_menu_slug, $this->parent_menu_text, $this->parent_menu_text, 'manage_options', $this->text_domain . $this->menu_slug, [$this, 'generate_grids']);
			}
		}


		public function generate_grids() {

			/**
			 * Adding default grid at first position
			 */
			array_unshift($this->grids, $this->default_grid());
			?>

            <div class="pro_aware grid_container wpmet_pro_a-grid-container">

	            <?php do_action($this->text_domain.'/pro_awareness/before_grid_contents'); ?>

                <div class="wpmet_pro_a-row">
					<?php
					foreach($this->grids as $grid) {
						?>
                        <div class="grid wpmet_pro_a-grid">
                            <div class="wpmet_pro_a-grid-inner">
                                <a target="_blank" href="<?php echo esc_url($grid['url']); ?>"
                                   class="wpmet_pro_a_wrapper" title="<?php echo esc_attr($grid['title']); ?>"
                                   title="<?php echo esc_attr($grid['title']); ?>">
                                    <div class="wpmet_pro_a_thumb">
                                        <img src="<?php echo esc_attr($grid['thumbnail']); ?>" alt="Thumbnail">
                                    </div>
                                    <!-- // thumbnail -->

                                    <h4 class="wpmet_pro_a_grid_title"><?php echo esc_attr($grid['title']); ?></h4>
									<?php if(!empty($grid['description'])) { ?>
                                        <p class="wpmet_pro_a_description"><?php echo esc_html($grid['description']); ?></p>
                                        <!-- // description -->
									<?php } ?>
                                    <!-- // title -->
                                </a>
                            </div>
                        </div>
						<?php
					} ?>
                </div>
				<div class="metform-video-section">
					<div class="metform-video-separator"></div>
						<h2 class="metform-video-title">MetForm Overview ⤵️</h2>
						<div class="metform-video-embed-container">
							<iframe
								width="560"
								height="315"
								src="https://www.youtube.com/embed/zg1QIouKO_Q?si=CS33ga5SEFe2--_n"
								title="YouTube video player"
								frameborder="0"
								allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
								referrerpolicy="strict-origin-when-cross-origin"
								allowfullscreen
							></iframe>
						</div>
					</div>

				<style>
					.metform-video-section {
						margin-top: 60px;
						max-width: 100%;
						padding: 0 15px;
					}
					
					.metform-video-separator {
						width: 100px;
						height: 3px;
						background-color: #aeaeae;
						margin: 0 auto 30px;
						border-radius: 3px;
					}
					
					.metform-video-title {
						font-size: 28px;
						color: #021343;
						margin: 20px 0;
						font-weight: 700;
						display: block;
						text-align: left;
					}
					
					.metform-video-embed-container {
						margin: 30px auto 0;
						width: 100%;
						position: relative;
					}
					
					.metform-video-embed-container iframe {
						width: 100%;
						height: 500px;
						border: none;
					}

					
					@media (max-width: 768px) {
						.metform-video-separator {
							margin-bottom: 20px;
						}
						.metform-video-title {
							font-size: 24px;
							text-align: center;
						}
						.metform-video-embed-container iframe {
							height: 400px;
						}
					}

				</style>

	            <?php do_action($this->text_domain.'/pro_awareness/after_grid_contents'); ?>

            </div>

			<?php
		}

		public static function enqueue_scripts() {
			echo "
			<script>
			jQuery(document).ready( function($) {   
				$('.pro_aware').parent().attr('target','_blank');  
			});
            </script>
            <style>
            .wpmet_pro_a-grid-container {
                max-width: 1350px;
                width: 100%;
                padding-right: 15px;
                padding-left: 15px;
                box-sizing: border-box;
                margin-top: 50px;
            }
        
            .wpmet_pro_a-grid-inner .wpmet_pro_a_wrapper {
                padding: 35px 50px;
                display: block;
            }
    
            .wpmet_pro_a-row {
                display: grid;
				grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
				grid-gap: 30px;
            }
        
            .wpmet_pro_a-grid {
                background-color: #fff;
                border-radius: 4px;
                box-shadow: 0px 2px 5px 10px rgba(0,0,0,.01);
                transition: all .4s ease;
            }

			.wpmet_pro_a-grid:hover {
                transform: translateY(-3px);
                box-shadow: 0px 10px 15px 15px rgba(0,0,0,.05);
            }
        
            .wpmet_pro_a_thumb {
                min-height: 76px;
                margin-bottom: 10px;
                display: block;
                border-radius: inherit;
            }
        
            .wpmet_pro_a_grid_title {
                font-size: 1.6rem;
				display: inline-block;
				line-height: normal;
				text-decoration: none;
				margin: 0px;
				font-weight: 600;
				color: #021343;
            }
        
            .wpmet_pro_a_description {
                margin-bottom: 0;
				text-decoration: none;
				display: inline-block;
				margin-top: 10px;
				font-size: 15px;
				line-height: 22px;
				color: #5D5E65;
            }
            .wp-submenu > li > a{
                position: relative;
            }

			.wpmet_pro_a-grid-container .wpmet-products {
				margin-top: 80px;
			}

			.wpmet_pro_a-grid-container .wpmet-products h1 {
				font-size: 40px;
				color: #021343;
				font-weight: 700;
				margin-bottom: 0;
				line-height: 44px;
			}

			.wpmet_pro_a-grid-container .wpmet-products p {
				color: #5D5E65;
    			font-size: 16px;
			}

			.wpmet_pro_a-grid-container .wpmet-products__content {
				margin-top: 40px;
				display: grid;
				grid-gap: 20px;
				grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
			}

			.wpmet_pro_a-grid-container .wpmet-products__content .help-card {
				background-color: #fff;
				border-radius: 4px;
				-webkit-box-shadow: 0px 2px 5px 10px #00000003;
				box-shadow: 0px 2px 5px 10px #00000003;
				-webkit-transition: all .4s ease;
				transition: all .4s ease;
				padding: 30px;
			}

			.wpmet_pro_a-grid-container .wpmet-products__content .help-card:hover {
				-webkit-transform: translateY(-3px);
				transform: translateY(-3px);
				-webkit-box-shadow: 0px 10px 15px 15px #0000000d;
				box-shadow: 0px 10px 15px 15px #0000000d;
			}

			.wpmet_pro_a-grid-container .wpmet-products__content label {
				color: #021343;
				font-size: 16px;
				font-weight: 700;
				display: -webkit-box;
				display: -ms-flexbox;
				display: flex;
				-webkit-column-gap: 10px;
				-moz-column-gap: 10px;
				column-gap: 10px;
				-webkit-box-align: center;
				-ms-flex-align: center;
				align-items: center;
				margin-bottom: 15px;
			}

			.wpmet_pro_a-grid-container .wpmet-products__content span {
				display: inline-block;
				color: #5D5E65;
    			font-size: 16px;
			}
        
            @media (max-width: 767px) {
                .wpmet_pro_a_grid_title {
                    font-size: 1.2rem;
                }
            }
        </style>
		";
		}

		public function insert_plugin_links($links) {

			foreach($this->action_links as $action_link) {

				if(!empty($action_link['link']) && !empty($action_link['text'])) {

					$attributes = '';

					if(!empty($action_link['attr'])) {

						foreach($action_link['attr'] as $key => $val) {

							$attributes .= $key.'="'.esc_attr($val).'" ';
					    }
					}

					$links[] = sprintf('<a href="%s" ' . $attributes . ' > %s </a>', $action_link['link'], esc_html($action_link['text']));
				}
			}


			return $links;
		}

		public function insert_plugin_row_meta($links, $file) {
			if($file == $this->plugin_file) {

				foreach($this->row_meta_links as $meta) {

					if(!empty($meta['link']) && !empty($meta['text'])) {

						$attributes = '';

						if(!empty($meta['attr'])) {

							foreach($meta['attr'] as $key => $val) {

								$attributes .= $key.'="'.esc_attr($val).'" ';
							}
						}

						$links[] = sprintf('<a href="%s" %s > %s </a>', $meta['link'], $attributes, esc_html($meta['text']));
					}
				}

			}

			return $links;
		}

		public function set_plugin_action_link($text, $link, $attr = []) {

			$this->action_links[] = [
				'text' => $text,
				'link' => $link,
				'attr'  => $attr,
			];

			return $this;
		}

		public function set_plugin_row_meta($text, $link, $attr = []) {

			$this->row_meta_links[] = [
				'text' => $text,
				'link' => $link,
				'attr'  => $attr,
			];

			return $this;
		}

		public function generate_menus() {
			add_filter('plugin_action_links_' . $this->plugin_file, [$this, 'insert_plugin_links']);
			add_filter('plugin_row_meta', [$this, 'insert_plugin_row_meta'], 10, 2);

			if(!empty($this->parent_menu_slug)) {
				$this->prepare_grid_links();
				$this->prepare_pro_links();
			}
		}

		public static function init() {
			add_action('admin_head', [__CLASS__, 'enqueue_scripts']);
		}

		public function call() {
			add_action('admin_menu', [$this, 'generate_menus'], 99999);
		}
	}

endif;
