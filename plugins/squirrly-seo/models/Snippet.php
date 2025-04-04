<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class SQ_Models_Snippet {

	public $post;

	public function getPages( $search ) {
		global $wp_query;
		$pages = array();

		// Sanitize the search
		$search = SQ_Classes_Helpers_Sanitize::sanitizeSearch( $search );

		//remove other plugins filters
		wp_reset_query();
		remove_all_filters( 'pre_get_posts' );

		$labels        = SQ_Classes_Helpers_Tools::getValue( 'slabel', array() );
		$paged         = SQ_Classes_Helpers_Tools::getValue( 'spage', 1 );
		$post_id       = SQ_Classes_Helpers_Tools::getValue( 'sid', false );
		$post_type     = SQ_Classes_Helpers_Tools::getValue( 'stype', 'post' );
		$post_per_page = SQ_Classes_Helpers_Tools::getValue( 'cnt', 10 );
		$post_status   = SQ_Classes_Helpers_Tools::getValue( 'sstatus', 'all' );

		//Set publish post status for Focus Pages and Audit Pages
		$page = apply_filters( 'sq_page', SQ_Classes_Helpers_Tools::getValue( 'page', false ) );
		if ( in_array( $page, array( 'sq_focuspages', 'sq_audits' ) ) ) {
			$post_status = SQ_Classes_Helpers_Tools::getValue( 'sstatus', 'publish' );
		}

		if ( $post_status == 'all' ) { //to show all statuses
			$post_status = '';
		}

		$patterns = SQ_Classes_Helpers_Tools::getOption( 'patterns' );
		if ( ! isset( $patterns[ $post_type ] ) ) {
			$patterns[ $post_type ] = $patterns['custom'];
		}

		//Set the Labels and Categories
		SQ_Classes_ObjController::getClass( 'SQ_Models_BulkSeo' )->init();

		//If home then show the home url
		if (  $post_type == 'home' && ( $post_status == '' || $post_status == 'publish' ) ) {
			if ( $post = $this->setHomePage() ) {
				$page = SQ_Classes_ObjController::getClass( 'SQ_Models_BulkSeo' )->parsePage( $post, $labels )->getPage();

				if ( $search <> '' && SQ_Classes_Helpers_Tools::findStr( $page->sq->title, $search ) === false && SQ_Classes_Helpers_Tools::findStr( $page->sq->description, $search ) === false && strpos( $page->url, trim( $search, '/' ) ) === false ) {
				} else {
					$pages[] = $page;
				}
			}
		}

		//get all the public post types
		$types = get_post_types( array( 'public' => true ) );

		$statuses = array( 'draft', 'publish', 'pending', 'future', 'private' );
		//push the shop page into post types to pass the filter
		if ( $post_type == 'shop' ) {
			$types[] = 'shop';
		}

		if ( ! empty( $types ) && in_array( $post_type, $types ) ) {

			//get all the post types from database
			//filter by all in case of #all search
			//filter by page in case of shop post type
			$query = array(
				'post_type'      => ( $post_id ? array_keys( $types ) : ( $post_type <> 'shop' ? $post_type : 'page' ) ),
				'post_status'    => $statuses,
				'posts_per_page' => $post_per_page,
				'paged'          => $paged,
				'orderby'        => 'date',
				'order'          => 'DESC',
			);

			//if there is a limit given
			if ( $search <> '' && strpos( $search, '/' ) === false ) {
				$query['posts_per_page'] = 1000;
			}

			//if there is a search in posts
			if ( strpos( $search, '/' ) !== false ) {

				//set the order to the exact match
				add_filter( 'posts_orderby', function ( $orderby ) use ( $search ) {
					global $wpdb;

					return "({$wpdb->posts}.post_name = '$search') desc, length({$wpdb->posts}.post_name)" . ( $orderby ? ',' . $orderby : '' );
				}, 10, 1 );

				//add data in where
				add_filter( 'posts_where', function ( $where ) use ( $search ) {
					global $wpdb;

					//remove the domain
					if ( strpos( $search, '//' ) !== false && parse_url( $search, PHP_URL_PATH ) ) {
						$search = parse_url( $search, PHP_URL_PATH );
					}

					//remove the trail slash
					$search = untrailingslashit( $search );

					//get only the name of the post
					if ( strrpos( $search, '/' ) !== false ) {
						$search = substr( $search, strrpos( $search, '/' ) + 1 );
					}

					//if search is valid
					if ( $search <> '' ) {
						$where .= ' AND ' . $wpdb->posts . '.post_name LIKE \'%' . $wpdb->esc_like( $search ) . '%\'';
					}

					return $where;
				}, 10, 1 );

			} elseif ( $search <> '' ) {
				$query['s'] = $search;
			}


			//If post id is set in URL
			if ( $post_id ) {
				$query['post__in'] = explode( ',', $post_id );
			}

			//show the draft and publish posts
			if ( ! $post_id && ! $search && $post_type <> 'attachment' ) {
				$query['post_status'] = ( $post_status <> '' ? $post_status : $statuses );
			}

			//add filter for the current search query
			$query = apply_filters( 'sq_get_pages_before', $query );

			$wp_query           = new WP_Query( $query );
			$posts              = $wp_query->get_posts();
			$wp_query->is_paged = false; //remove pagination

			if ( ! empty( $posts ) ) {
				foreach ( $posts as $post ) {

					if ( $post = $this->setPostByID( $post ) ) {
						if ( $page = SQ_Classes_ObjController::getClass( 'SQ_Models_BulkSeo' )->parsePage( $post, $labels )->getPage() ) {
							if ( $page->url <> '' ) {

								//Search the Squirrly Title, Description and URL if search is set
								if ( $search <> '' ) {
									if ( SQ_Classes_Helpers_Tools::findStr( $page->post_title, $search ) === false && SQ_Classes_Helpers_Tools::findStr( $page->sq->title, $search ) === false && SQ_Classes_Helpers_Tools::findStr( $page->sq->description, $search ) === false && strpos( $page->url, trim( $search, '/' ) ) === false ) {
										continue;
									}
								}

								//Don't let other post types to pass
								if ( ! $post_id && isset( $page->post_type ) && ! in_array( $page->post_type, (array) $query['post_type'] ) ) {
									continue;
								}

								$pages[] = $page;
							}

							unset( $page );
						}
					}
				}
			}
		}

		//
		//Get all taxonomies like category, tag, custom post types
		$taxonomies = get_taxonomies( array( 'public' => true ) );
		if ( $post_type == 'tag' ) {
			$post_type = 'post_tag';
		}
		if ( strpos( $post_type, 'tax-' ) !== false ) {
			$post_type = str_replace( 'tax-', '', $post_type );
		}

		if ( in_array( $post_type, $taxonomies ) ) {
			$pages = array();

			$query = array(
				'public'     => true,
				'taxonomy'   => $post_type,
				'hide_empty' => false,
			);

			//If post id is set in URL
			//Same filter for taxonomy id
			if ( $post_id ) {
				$query['include'] = explode( ',', $post_id );
			}

			//if there is a search in posts
			if ( strpos( $search, '/' ) !== false ) {
				add_filter( 'terms_clauses', function ( $clauses ) use ( $search ) {
					global $wpdb;

					//remove the domain
					if ( strpos( $search, '//' ) !== false && parse_url( $search, PHP_URL_PATH ) ) {
						$search = parse_url( $search, PHP_URL_PATH );
					}

					//remove the trail slash
					$search = untrailingslashit( $search );

					//get only the name of the post
					if ( strrpos( $search, '/' ) !== false ) {
						$search = substr( $search, strrpos( $search, '/' ) + 1 );
					}

					//if search is valid
					if ( $search <> '' ) {
						$search = $wpdb->esc_like( $$search );

						if ( ! isset( $clauses['where'] ) ) {
							$clauses['where'] = '1=1';
						}

						$clauses['where'] .= $wpdb->prepare( " AND t.name LIKE %s", "%$search%" );
					}

					return $clauses;
				}, 10, 1 );
			}

			$categories = get_terms( $query );
			if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
				foreach ( $categories as $category ) {

					if ( $post = $this->setPostByTaxID( $category->term_id, $category->taxonomy ) ) {

						if ( $page = SQ_Classes_ObjController::getClass( 'SQ_Models_BulkSeo' )->parsePage( $post, $labels )->getPage() ) {
							if ( $page->url <> '' ) {
								if ( $search <> '' ) {
									if ( SQ_Classes_Helpers_Tools::findStr( $category->name, $search ) === false && SQ_Classes_Helpers_Tools::findStr( $category->slug, $search ) === false && SQ_Classes_Helpers_Tools::findStr( $page->sq->title, $search ) === false && strpos( $page->url, trim( $search, '/' ) ) === false ) {
										continue;
									}
								}

								$pages[] = $page;
							}
							unset( $page );

						}
					}
				}
			}


			//////////////////////////////
			//Set the correct number of pages
			$wp_query->max_num_pages = 0;
			if ( count( $pages ) > 0 ) {
				$wp_query->max_num_pages = ceil( count( $pages ) / $post_per_page );
				$pages                   = array_slice( $pages, ( ( $paged - 1 ) * $post_per_page ), $post_per_page );
			}
			//////////////////////////////
		}

		//Get the user profile from database
		//search in user profile
		if ( $post_type == "profile" ) {
			$blog_id = get_current_blog_id();
			$args    = array(
				'blog_id'      => $blog_id,
				'role__not_in' => array( 'subscriber', 'contributor', 'customer' ),
				'orderby'      => 'login',
				'order'        => 'ASC',
				'search'       => $search,
				'count_total'  => false,
				'fields'       => array( 'ID' ),
			);

			$users = get_users( $args );

			foreach ( $users as $user ) {
				if ( $post = $this->setAuthorPage( $user->ID ) ) {
					if ( $page = SQ_Classes_ObjController::getClass( 'SQ_Models_BulkSeo' )->parsePage( $post, $labels )->getPage() ) {
						if ( $page->url <> '' ) {
							$pages[] = $page;
							unset( $page );
						}
					}
				}
			}

			//////////////////////////////
			//Set the correct number of pages
			$wp_query->max_num_pages = 0;
			if ( count( $pages ) > 0 ) {
				$wp_query->max_num_pages = ceil( count( $pages ) / $post_per_page );
				$pages                   = array_slice( $pages, ( ( $paged - 1 ) * $post_per_page ), $post_per_page );
			}
			//////////////////////////////
		}


		return apply_filters( 'sq_wpposts', $pages, $search );

	}

	/**
	 * Save the Post data into DB
	 * used for Focus Pages, Audit Pages and more
	 *
	 * @param SQ_Models_Domain_Post $post
	 */
	public function savePost( $post ) {
		$sq = SQ_Classes_ObjController::getClass( 'SQ_Models_Qss' )->getSqSeo( $post->hash );

		//Save the post data in DB with the hash
		SQ_Classes_ObjController::getClass( 'SQ_Models_Qss' )->saveSqSEO( $post->url, $post->hash, maybe_serialize( array(
					'ID'        => (int) $post->ID,
					'post_type' => $post->post_type,
					'term_id'   => (int) $post->term_id,
					'taxonomy'  => $post->taxonomy,
				) ), maybe_serialize( $sq->toArray() ), gmdate( 'Y-m-d H:i:s' ) );
	}

	/**
	 * Save the SEO in DB for the current post
	 *
	 * @param int $post_id
	 * @param int $term_id
	 * @param string $taxonomy
	 * @param string $post_type
	 *
	 * @return array|bool
	 */
	public function saveSEO( $post_id = 0, $term_id = 0, $taxonomy = '', $post_type = '' ) {
		$json = array();
		if ( SQ_Classes_Helpers_Tools::getIsset( 'sq_hash' ) ) {
			$sq_hash = SQ_Classes_Helpers_Tools::getValue( 'sq_hash', '' );

			$post_id   = (int) SQ_Classes_Helpers_Tools::getValue( 'post_id', $post_id );
			$term_id   = (int) SQ_Classes_Helpers_Tools::getValue( 'term_id', $term_id );
			$taxonomy  = SQ_Classes_Helpers_Tools::getValue( 'taxonomy', $taxonomy );
			$post_type = SQ_Classes_Helpers_Tools::getValue( 'post_type', $post_type );

			if ( ! SQ_Classes_Helpers_Tools::userCan( 'sq_manage_snippets' ) ) {
				if ( ! SQ_Classes_Helpers_Tools::userCan( 'edit_post', $post_id ) ) {
					$json['error']         = 1;
					$json['error_message'] = esc_html__( "You don't have enough pemission to edit this article", 'squirrly-seo' );

					return $json;
				}
			}

			$url = SQ_Classes_Helpers_Tools::getValue( 'sq_url', '' );

			$sq = SQ_Classes_ObjController::getClass( 'SQ_Models_Qss' )->getSqSeo( $sq_hash );

			$sq->doseo = SQ_Classes_Helpers_Tools::getValue( 'sq_doseo', 0 );

			$sq->title       = SQ_Classes_Helpers_Sanitize::clearTitle(SQ_Classes_Helpers_Tools::getValue( 'sq_title', '' ));
			$sq->description = SQ_Classes_Helpers_Sanitize::clearDescription(SQ_Classes_Helpers_Tools::getValue( 'sq_description', '' ));
			$sq->keywords    = SQ_Classes_Helpers_Sanitize::clearKeywords(SQ_Classes_Helpers_Tools::getValue( 'sq_keywords', '' ));
			$sq->canonical   = SQ_Classes_Helpers_Tools::getValue( 'sq_canonical', '' );
			$sq->redirect    = SQ_Classes_Helpers_Tools::getValue( 'sq_redirect', '' );
			if ( SQ_Classes_Helpers_Tools::getIsset( 'sq_noindex' ) ) {
				$sq->noindex = SQ_Classes_Helpers_Tools::getValue( 'sq_noindex', 0 );
			}
			if ( SQ_Classes_Helpers_Tools::getIsset( 'sq_nofollow' ) ) {
				$sq->nofollow = SQ_Classes_Helpers_Tools::getValue( 'sq_nofollow', 0 );
			}
			if ( SQ_Classes_Helpers_Tools::getIsset( 'sq_nositemap' ) ) {
				$sq->nositemap = SQ_Classes_Helpers_Tools::getValue( 'sq_nositemap', 0 );
			}

			$sq->og_title       = SQ_Classes_Helpers_Sanitize::clearTitle(SQ_Classes_Helpers_Tools::getValue( 'sq_og_title', '' ));
			$sq->og_description = SQ_Classes_Helpers_Sanitize::clearDescription(SQ_Classes_Helpers_Tools::getValue( 'sq_og_description', '' ));
			$sq->og_author      = SQ_Classes_Helpers_Tools::getValue( 'sq_og_author', '' );
			$sq->og_type        = SQ_Classes_Helpers_Tools::getValue( 'sq_og_type', '' );
			$sq->og_media       = SQ_Classes_Helpers_Tools::getValue( 'sq_og_media', '' );

			$sq->tw_title       = SQ_Classes_Helpers_Sanitize::clearTitle(SQ_Classes_Helpers_Tools::getValue( 'sq_tw_title', '' ));
			$sq->tw_description = SQ_Classes_Helpers_Sanitize::clearDescription(SQ_Classes_Helpers_Tools::getValue( 'sq_tw_description', '' ));
			$sq->tw_media       = SQ_Classes_Helpers_Tools::getValue( 'sq_tw_media', '' );
			$sq->tw_type        = SQ_Classes_Helpers_Tools::getValue( 'sq_tw_type', '' );

			//Sanitize Emoticons
			$sq->title          = wp_encode_emoji( $sq->title );
			$sq->description    = wp_encode_emoji( $sq->description );
			$sq->og_title       = wp_encode_emoji( $sq->og_title );
			$sq->og_description = wp_encode_emoji( $sq->og_description );
			$sq->tw_title       = wp_encode_emoji( $sq->tw_title );
			$sq->tw_description = wp_encode_emoji( $sq->tw_description );

			if ( SQ_Classes_Helpers_Tools::getValue( 'sq_jsonld_code_type', 'auto' ) == 'custom' ) {
				if ( isset( $_POST['sq_jsonld'] ) ) {
					$allowed_html = array(
						'script' => array( 'type' => array() ),
					);
					$sq->jsonld   = strip_tags( wp_unslash( trim( wp_kses( $_POST['sq_jsonld'], $allowed_html ) ) ) );
				}
			} else {
				$sq->jsonld = '';
			}
			$sq->jsonld_types     = array_filter( SQ_Classes_Helpers_Tools::getValue( 'sq_jsonld_types', array() ) );
			$sq->primary_category = SQ_Classes_Helpers_Tools::getValue( 'sq_primary_category', '' );

			if ( SQ_Classes_Helpers_Tools::getValue( 'sq_fpixel_code_type', 'auto' ) == 'custom' ) {
				if ( isset( $_POST['sq_fpixel'] ) ) {
					$allowed_html = array(
						'script'   => array(),
						'noscript' => array(),
					);
					$sq->fpixel   = wp_unslash( trim( wp_kses( $_POST['sq_fpixel'], $allowed_html ) ) );
				}
			} else {
				$sq->fpixel = '';
			}

			//Filter the SQ before save
			// Send SQ_Models_Domain_Sq object
			$sq = apply_filters( 'sq_seo_before_save', $sq, (int) $post_id, $post_type, (int) $term_id, $taxonomy, $sq_hash );

			//Filter the URL before save
			$url = apply_filters( 'sq_url_before_save', $url, $sq_hash );

			//Prevent broken url in canonical link
			if ( strpos( $sq->canonical, '//' ) === false ) {
				$sq->canonical = '';
			}

			if ( strpos( $sq->redirect, '//' ) === false || $sq->redirect === $url ) {
				$sq->redirect = '';
			}

			try {

				if ( SQ_Classes_ObjController::getClass( 'SQ_Models_Qss' )->saveSqSEO( $url, $sq_hash, maybe_serialize( array(
							'ID'        => (int) $post_id,
							'post_type' => $post_type,
							'term_id'   => (int) $term_id,
							'taxonomy'  => $taxonomy,
						) ), maybe_serialize( $sq->toArray() ), gmdate( 'Y-m-d H:i:s' ) ) ) {

					//trigger action after SEO is saved in Squirrly DB
					do_action( 'sq_save_seo_after' );

					return true;
				} else {
					/** @var SQ_Models_Qss $qssModel Create Qss table if not exists */
					if ( $qssModel = SQ_Classes_ObjController::getClass( 'SQ_Models_Qss' ) ) {
						$qssModel->checkTableExists();
						$qssModel->alterTable();
					}
				}

			} catch ( Exception $e ) {
				$json['error']         = 1;
				$json['error_message'] = esc_html__( "Error! Could not save the data.", 'squirrly-seo' );
			}

		} else {
			$json['error']         = 1;
			$json['error_message'] = esc_html__( "Error! Invalid request.", 'squirrly-seo' );
		}

		return $json;
	}

	public function getCurrentSnippet( $post_id, $term_id = 0, $taxonomy = '', $post_type = '' ) {
		$post = false;

		if ( $post_type == 'home' ) {

			//if this is the home page
			$post = $this->setHomePage();

		} elseif ( $post_type == 'profile' ) {

			//if this is the profile page
			$post = $this->setAuthorPage( $post_id );

		} elseif ( $post_id > 0 ) {

			//if this is a post type
			$post = $this->setPostByID( $post_id );
			//check for multilingual plugins
			$this->getMultilangPage( $post );

		} elseif ( $term_id > 0 && $taxonomy <> '' ) {

			//If there is a category ot taxonomy
			if ( $post = $this->setPostByTaxID( $term_id, $taxonomy ) ) {
				if ( get_term_link( $term_id, $taxonomy ) == $post->url ) {
					$this->getMultilangPage( $post );
				}
			}

		} elseif ( $post_type <> '' ) {

			//for other post types
			/** @var SQ_Models_Domain_Post $post */
			$post = SQ_Classes_ObjController::getDomain( 'SQ_Models_Domain_Post' );

			/** @var SQ_Models_Frontend $frontend */
			$frontend = SQ_Classes_ObjController::getClass( 'SQ_Models_Frontend' );

			if ( $archive = $frontend->getArchiveDetails() ) {
				//add the archive details for this post
				$post = $frontend->addArchive( $post, $archive );
			} elseif ( $frontend->checkCutomPostType( $post->post_type ) ) {
				//add custom post type details for this post
				$post = $frontend->addCustomPostType( $post );
			} else {
				//for other post types
				$post->post_type = $post_type;
				$post->hash      = md5( $post_type );

				//check if archive
				if ( $url = SQ_Classes_Helpers_Tools::getValue( 'sq_url' ) ) {
					if ( $path = parse_url( $url, PHP_URL_PATH ) ) {
						preg_match( '/([0-9]{4})\/([0-9]{1,2})/', $path, $dates );

						if ( ! empty( $dates ) && isset( $dates[1] ) && isset( $dates[2] ) ) {
							$post->hash      = md5( $post_type . (int) $dates[1] . '-' . (int) $dates[2] );
							$post->post_date = wp_date( get_option( 'date_format' ), strtotime( $dates[1] . '-' . $dates[2] ) );
						}
					}

					$post->url = $url;
				}
			}

			//get the current post from frontend
			$post = $frontend->setPost( $post )->getPost();

		} else {
			SQ_Classes_Error::setError( esc_html__( "Couldn't find the page", 'squirrly-seo' ) );
		}

		return apply_filters( 'sq_wppost', $post, $post_id, $term_id, $taxonomy, $post_type );

	}

	public function getMultilangPage( &$post ) {
		global $polylang, $wp_query;

		if ( function_exists( 'pll_default_language' ) ) {
			$language = pll_default_language();
			if ( isset( $polylang ) && function_exists( 'pll_get_term' ) ) {
				if ( ( $post->post_type == 'category' || $post->post_type == 'tag' ) && $post->term_id > 0 ) {

					if ( ! pll_get_term( $post->term_id, $language ) ) {
						SQ_Classes_Error::setError( esc_html__( "No Polylang translation for this post.", 'squirrly-seo' ) );

						$wp_query->is_404 = true;
						$post->post_type  = '404';
						$post->hash       = md5( $post->post_type );
						$post->sq         = $post->sq_adm = null;
						$post             = SQ_Classes_ObjController::getClass( 'SQ_Models_Frontend' )->setPost( $post )->getPost();
					}
				} elseif ( $post->ID > 0 ) {
					//SQ_Debug::dump(pll_get_post($post->ID, $language));
					if ( function_exists( 'pll_get_post' ) ) {
						if ( ! pll_get_post( $post->ID, $language ) ) {
							SQ_Classes_Error::setError( esc_html__( "No Polylang translation for this post.", 'squirrly-seo' ) );
						}
					}
				}

			}
		}

		return true;
	}

	public function setPostByURL( $url ) {
		$post_id = url_to_postid( $url );

		if ( $post_id > 0 ) {
			$post = get_post( $post_id );
			$post = SQ_Classes_ObjController::getClass( 'SQ_Models_Frontend' )->setPost( $post )->getPost();

			return $post;
		}

		return false;
	}

	/**
	 * Set the home page or blog page in Shippet
	 *
	 * @return array|null|stdClass|WP_Post
	 */
	public function setHomePage() {
		global $wp_query;

		//If  post id set in General Readings for Home Page
		if ( $post_id = get_option( 'page_on_front' ) ) {
			//Get the post for this post ID
			$post = get_post( (int) $post_id );

		} else {
			//create the home page domain if no post ID set in Settings > Readings
			$post = SQ_Classes_ObjController::getDomain( 'SQ_Models_Domain_Post' );

			$wp_query->is_home  = true;
			$post->post_type    = 'home';
			$post->hash         = md5( 'wp_homepage' );
			$post->post_title   = get_bloginfo( 'name' );
			$post->post_excerpt = get_bloginfo( 'description' );
			$post->url          = home_url();
		}

		$post = SQ_Classes_ObjController::getClass( 'SQ_Models_Frontend' )->setPost( $post )->getPost();

		return $post;
	}

	/**
	 * Get post by hash
	 *
	 * @param  $sq_hash
	 *
	 * @return array|bool|int|stdClass|WP_Post|null
	 */
	public function setPostByHash( $sq_hash ) {
		$post = SQ_Classes_ObjController::getClass( 'SQ_Models_Qss' )->getSqPost( $sq_hash );

		return $this->getCurrentSnippet( $post->ID, $post->term_id, $post->taxonomy, $post->post_type );
	}

	public function setPostByID( $post = 0 ) {

		if ( ! $post instanceof WP_Post && ! $post instanceof SQ_Models_Domain_Post ) {
			$post_id = (int) $post;
			if ( $post_id > 0 ) {
				$post = get_post( $post_id );
			}
		}

		if ( $post ) {
			if ( isset( $post->post_type ) ) {
				set_query_var( 'post_type', $post->post_type );
			}
			$post = SQ_Classes_ObjController::getClass( 'SQ_Models_Frontend' )->setPost( $post )->getPost();

			return $post;
		}

		return false;
	}

	public function setPostByTaxID( $term_id = 0, $taxonomy = 'category' ) {
		if ( $term_id > 0 ) {
			global $wp_query;

			if ( ! method_exists( $wp_query, 'query' ) ) {
				return false;
			}

			$term = get_term_by( 'term_id', $term_id, $taxonomy );

			if ( ! is_wp_error( $term ) ) {
				$args = array( 'posts_per_page' => 1, $taxonomy => $taxonomy, 'term_id' => $term_id );

				if ( isset( $term->slug ) ) {
					$tax_query         = array(
						array(
							'taxonomy'         => $taxonomy,
							'terms'            => $term->slug,
							'field'            => 'slug',
							'include_children' => true,
							'operator'         => 'IN'
						),
						array(
							'taxonomy'         => $taxonomy,
							'terms'            => $term->slug,
							'field'            => 'slug',
							'include_children' => false,
						)
					);
					$args['tax_query'] = $tax_query;

				}

				$wp_query->query( $args );
				set_query_var( 'post_type', $taxonomy );
				//SQ_Debug::dump($term, $args);

				if ( $post = SQ_Classes_ObjController::getClass( 'SQ_Models_Frontend' )->setPost( $term )->getPost() ) {
					return $post;
				}
			}
		}

		return false;
	}

	public function setAuthorPage( $user_id ) {

		if ( $author = get_userdata( $user_id ) ) {
			$post = SQ_Classes_ObjController::getDomain( 'SQ_Models_Domain_Post' );

			$post->post_type = 'profile';
			if ( isset( $author->ID ) ) {
				$post->ID              = $author->ID;
				$post->hash            = md5( $post->post_type . $author->ID );
				$post->post_author     = $author->display_name;
				$post->post_title      = $author->display_name;
				$post->post_excerpt    = get_the_author_meta( 'description', $author->ID );
				$post->post_attachment = false;

				//If buddypress installed
				if ( function_exists( 'bp_core_get_user_domain' ) ) {
					$post->url = bp_core_get_user_domain( $author->ID );
				} else {
					$post->url = get_author_posts_url( $author->ID );
				}

			}

			if ( $post = SQ_Classes_ObjController::getClass( 'SQ_Models_Frontend' )->setPost( $post )->getPost() ) {
				return $post;
			}
		}

		return false;
	}

	/**
	 * Is the user on page name? Default name = post edit page
	 * name = 'quirrly'
	 *
	 * @param string $name
	 *
	 * @return boolean
	 * @global array $pagenow
	 */
	public function is_page( $name = '' ) {
		global $pagenow;
		$page = array();
		//make sure we are on the backend
		if ( is_admin() && $name <> '' ) {
			if ( $name == 'edit' ) {
				$page = array( 'post.php', 'post-new.php' );
			} else {
				$page[] = $name . '.php';
			}

			return in_array( $pagenow, $page );
		}

		return false;
	}

}
