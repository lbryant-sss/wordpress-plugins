<?php

class Brizy_Admin_Popups_Main {

	const CP_POPUP = 'editor-popup';

	/**
	 * @return Brizy_Admin_Popups_Main
	 */
	public static function _init() {
		static $instance;
		if ( ! $instance ) {
			$instance = new self();
			$instance->initialize();
		}

		return $instance;
	}

	public function initialize() {
		add_action( 'brizy_after_enabled_for_post', [ $this, 'afterBrizyEnabledForPopup' ] );
		add_action( 'brizy_preview_mode', array( $this, 'initializePreviewActions' ) );
		if ( Brizy_Editor::is_user_allowed() ) {
			add_action( 'admin_menu', [ $this, 'removePageAttributes' ] );
		}
	}

	public function initializePreviewActions( $post ) {
		add_action( 'brizy_preview_enqueue_post', [ $this, 'enqueuePopupScripts' ] );
		add_action( 'wp_footer', [ $this, 'wpFooterAppendPopupHtml' ] );
		add_filter( 'body_class', [ $this, 'bodyClassFrontend' ], 11 );
		$this->enqueuePopupScripts( $post->getWpPostId() );
		$this->enqueuePopupScripts( null );
	}

	public function enqueuePopupScripts( $postId ) {
		$wp_post = null;
		if ( $postId ) {
			$wp_post = get_post( $postId );
		}
		$matching_brizy_popups = $this->getMatchingBrizyPopups( $wp_post );
		foreach ( $matching_brizy_popups as $popup ) {

			try {
				$compiler = new Brizy_Editor_Compiler( Brizy_Editor_Project::get(), new Brizy_Admin_Blocks_Manager( Brizy_Admin_Blocks_Main::CP_GLOBAL ), new Brizy_Editor_UrlBuilder( Brizy_Editor_Project::get(), $popup ), Brizy_Config::getCompilerUrls(), Brizy_Config::getCompilerDownloadUrl() );
				if ( $compiler->needsCompile( $popup ) ) {
					$editgorConfig = Brizy_Editor_Editor_Editor::get( Brizy_Editor_Project::get(), $popup )->config( Brizy_Editor_Editor_Editor::COMPILE_CONTEXT );
					$compiler->compilePost( $popup, $editgorConfig );
				}

			} catch ( Exception $e ) {
				Brizy_Logger::instance()->exception( $e );
			}
			$manger = Brizy_Public_AssetEnqueueManager::_init();
			if ( ! $manger->isPostEnqueued( $popup ) ) {
				$manger->enqueuePost( $popup );
			}
		}
	}

	public function wpHeadAppendPopupHtml() {
		$headHtml = $this->getPopupsHtml( null, null, 'head' );
		if ( empty( $headHtml ) ) {
			return;
		}
		echo do_shortcode( $headHtml );
	}

	public function wpFooterAppendPopupHtml() {
		$bodyHtml = $this->getPopupsHtml( null, null, 'body' );
		if ( empty( $bodyHtml ) ) {
			return;
		}
		echo do_shortcode( $bodyHtml );
	}

	public function bodyClassFrontend( $classes ) {
		if ( ! $this->getMatchingBrizyPopups() || false !== array_search( 'brz', $classes ) ) {
			return $classes;
		}
		$classes[] = 'brz';

		return $classes;
	}

	public function removePageAttributes() {
		remove_meta_box( 'pageparentdiv', self::CP_POPUP, 'side' );
	}

	static public function registerCustomPosts() {

		$labels = array(
			'name'               => _x( 'Popups', 'post type general name', 'brizy' ),
			'singular_name'      => _x( 'Popup', 'post type singular name', 'brizy' ),
			'menu_name'          => _x( 'Popups', 'admin menu', 'brizy' ),
			'name_admin_bar'     => _x( 'Popup', 'add new on admin bar', 'brizy' ),
			'add_new'            => __( 'Add New', 'brizy' ),
			'add_new_item'       => __( 'Add New Popup', 'brizy' ),
			'new_item'           => __( 'New Popup', 'brizy' ),
			'edit_item'          => __( 'Edit Popup', 'brizy' ),
			'view_item'          => __( 'View Popup', 'brizy' ),
			'all_items'          => __( 'Popups', 'brizy' ),
			'search_items'       => __( 'Search Popups', 'brizy' ),
			'parent_item_colon'  => __( 'Parent Popups:', 'brizy' ),
			'not_found'          => __( 'No Popups found.', 'brizy' ),
			'not_found_in_trash' => __( 'No Popups found in Trash.', 'brizy' ),
			'attributes'         => __( 'Popup attributes:', 'brizy' ),
		);
		register_post_type( self::CP_POPUP, array(
			'labels'              => $labels,
			'public'              => false,
			'has_archive'         => false,
			'description'         => __( 'Popups', 'brizy' ),
			'publicly_queryable'  => Brizy_Editor_User::is_user_allowed(),
			'show_ui'             => defined( 'BRIZY_PRO_VERSION' ),
			'show_in_menu'        => Brizy_Admin_Settings::menu_slug(),
			'query_var'           => false,
			'rewrite'             => array( 'slug' => 'editor-popup' ),
			'capability_type'     => 'page',
			'hierarchical'        => false,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'can_export'          => true,
			'supports'            => array( 'title', 'post_content', 'revisions' ),
		) );
		remove_post_type_support( self::CP_POPUP, 'page-attributes' );
		add_filter( 'brizy_supported_post_types', function ( $posts ) {
			$posts[] = self::CP_POPUP;

			return $posts;
		} );
	}

	/**
	 * @param $post
	 *
	 * @throws Exception
	 */
	public function afterBrizyEnabledForPopup( $post ) {
		if ( $post->post_type === Brizy_Admin_Popups_Main::CP_POPUP ) {
			$manager = new Brizy_Admin_Rules_Manager();
			if ( count( $manager->getRules( $post->ID ) ) == 0 ) {
				$manager->saveRules( $post->ID, array(
					new Brizy_Admin_Rule( null, Brizy_Admin_Rule::TYPE_INCLUDE, '', '', array() ),
				) );
			}
		}
	}

	/**
	 * @param $content
	 * @param $project
	 * @param $wpPost
	 * @param string $context
	 *
	 * @return string|string[]|null
	 * @throws Brizy_Editor_Exceptions_NotFound
	 * @throws Brizy_Editor_Exceptions_ServiceUnavailable
	 */
	public function getPopupsHtml( $project, $wpPost, $context ) {
		$content = "";
		$popups  = $this->getMatchingBrizyPopups( $wpPost );
		foreach ( $popups as $brizyPopup ) {
			/**
			 * @var Brizy_Editor_Post $brizyPopup ;
			 */
			if ( empty($brizyPopup->getCompiledSections()) ) {
				continue;
			}

			$popupContent = apply_filters( 'brizy_content', $brizyPopup->getCompiledHtml(), Brizy_Editor_Project::get(), null, $context );
			$content .= "\n\n<!-- POPUP BODY -->\n{$popupContent}\n<!-- POPUP BODY END-->\n\n";
		}

		return $content;
	}

	/**
	 * @param null $wpPost
	 *
	 * @return array
	 */
	public function getMatchingBrizyPopups( $wpPost = null ) {
		$ruleMatches = [];
		if ( $wpPost ) {
			$ruleMatches[] = [
				'applyFor'     => Brizy_Admin_Rule::POSTS,
				'entityType'   => $wpPost->post_type,
				'entityValues' => [ $wpPost->ID ],
			];
		} else {
			$ruleMatches = Brizy_Admin_Rules_Manager::getCurrentPageGroupAndTypeForPopoup();
		}

		return $this->findMatchingPopups( $ruleMatches );
	}

	/**
	 * @param $applyFor
	 * @param $entityType
	 * @param $entityValues
	 *
	 * @return array
	 */
	private function findMatchingPopups( $ruleMatches ) {

		$resultPopups = array();
		$allPopups    = get_posts( array(
			'post_type'   => self::CP_POPUP,
			'numberposts' => - 1,
			'post_status' => 'publish',
		) );
		$ruleManager  = new Brizy_Admin_Rules_Manager();
		$ruleSets     = [];
		foreach ( $allPopups as $aPopup ) {
			$ruleSets[ $aPopup->ID ] = $ruleManager->getRuleSet( $aPopup->ID );
		}
		foreach ( $ruleMatches as $ruleMatch ) {
			$applyFor     = $ruleMatch['applyFor'];
			$entityType   = $ruleMatch['entityType'];
			$entityValues = $ruleMatch['entityValues'];
			$allPopups    = Brizy_Admin_Rules_Manager::sortEntitiesByRuleWeight( $allPopups, [
				'type'         => $applyFor,
				'entityType'   => $entityType,
				'entityValues' => $entityValues,
			] );
			foreach ( $allPopups as $aPopup ) {
				try {
					if ( $ruleSets[ $aPopup->ID ]->isMatching( $applyFor, $entityType, $entityValues ) ) {
						$resultPopups[ $aPopup->ID ] = Brizy_Editor_Popup::get( $aPopup );
					}
				} catch ( \Exception $e ) {
					continue; // we catch here  the  exclusions
				}
			}
		}

		return array_values( $resultPopups );
	}
}

