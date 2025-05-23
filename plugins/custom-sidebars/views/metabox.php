<?php
/**
 * Metabox inside posts/pages where user can define custom sidebars for an
 * individual post.
 *
 * Uses:
 *   $selected
 *   $wp_registered_sidebars
 *   $post_id
 */

$sidebars = CustomSidebars::get_options( 'modifiable' );

$is_front = get_option( 'page_on_front' ) == $post_id;
$is_blog = get_option( 'page_for_posts' ) == $post_id;
/**
 * check is WooCommerce shop
 */
$is_woo_shop = intval( $post_id ) === ( function_exists( 'wc_get_page_id' )? intval( wc_get_page_id( 'shop' ) ) : 0 );
/**
 * local display helper
 *
 * @since 3.2.0
 *
 * @param string $page_name Page Name to display.
 * @param string $img Image to display.
 * @param string $archive Archive name to display.
 */
if ( ! function_exists( 'custom_sidebars_replace_not_allowed' ) ) {
	function custom_sidebars_replace_not_allowed( $page_name, $img, $archive = null ) {
		echo '<p>';
		printf(
			'<strong>%s</strong>',
			sprintf(
                /* translators: %s is replaced with the Page Name */
				esc_html__( 'To change the sidebar for %s', 'custom-sidebars' ),
				esc_html( $page_name )
			)
		);
		echo '<ul>';
		CustomSidebars::wp_kses_wf(sprintf(
			'<li>%s</li>',
            /* translators: %1$s is replaced with the link to edit the Widgets page */
			sprintf(__( 'Go to the <a href="%1$s">Widgets page</a>', 'custom-sidebars' ), admin_url( 'widgets.php' ))));
		printf(
			'<li>%s</li>',
			esc_html__( 'Click on "Sidebar Location"', 'custom-sidebars' )
		);
		printf(
			'<li>%s</li>',
			esc_html__( 'Open the "Archive-Types" tab', 'custom-sidebars' )
		);
		printf(
			'<li>%s</li>',
            /* translators: %s is replaced with the Page Name or Archive Name */
			sprintf(esc_html__( 'Choose "%s"', 'custom-sidebars' ),	esc_html( empty( $archive )? $page_name : $archive ))
		);
		echo '</ul>';
		echo '</p>';
		$url = esc_url( CSB_IMG_URL . 'metabox/' . $img . '?version=3.2.4' );
		printf(
			'<a href="%s" target="_blank"><img src="%s" style="width:100%%" /></a>',
			esc_url( $url ),
			esc_url( $url )
		);
	}
}
/**
 * show
 */
if ( $is_front  ) {
	$page_name = esc_html__( 'Front Page', 'custom-sidebars' );
	custom_sidebars_replace_not_allowed( $page_name, 'frontpage-info.png' );
} elseif ( $is_blog ) {
	$page_name = esc_html__( 'Blog Page', 'custom-sidebars' );
	$archive = esc_html__( 'Post Index', 'custom-sidebars' );
	custom_sidebars_replace_not_allowed( $page_name, 'blogpage-info.png', $archive );
} elseif ( $is_woo_shop ) {
	$page_name = esc_html__( 'WooCommerce Shop', 'custom-sidebars' );
	$post_type_object = get_post_type_object( 'product' );
    /* translators: %s is replaced with the Archive name */
	$archive = sprintf( esc_html__( '%s Archives', 'custom-sidebars' ), $post_type_object->label );
	custom_sidebars_replace_not_allowed( $page_name, 'wooshop-info.png', $archive );
} else {
	echo '<p>';
	esc_html_e( 'Here you can replace the default sidebars. Simply select what sidebar you want to show for this post!', 'custom-sidebars' );
	echo '</p>';
	if ( ! empty( $sidebars ) ) {
		global $wp_registered_sidebars;
		$available = CustomSidebars::sort_sidebars_by_name( $wp_registered_sidebars );
		foreach ( $sidebars as $s ) { ?>
            <?php $sb_name = $available[ $s ]['name']; ?>
            <p>
                <label for="cs_replacement_<?php echo esc_attr( $s ); ?>">
                    <b><?php echo esc_html( $sb_name ); ?></b>:
                </label>
                <select name="cs_replacement_<?php echo esc_attr( $s ); ?>"
                    id="cs_replacement_<?php echo esc_attr( $s ); ?>"
                    class="cs-replacement-field <?php echo esc_attr( $s ); ?>">
                    <option value=""></option>
                    <?php foreach ( $available as $a ) { ?>
                    <option value="<?php echo esc_attr( $a['id'] ); ?>" <?php selected( $selected[ $s ], $a['id'] ); ?>>
                        <?php echo esc_html( $a['name'] ); ?>
                    </option>
                    <?php } ?>
                </select>
            </p>
<?php
		}
	} else {
		echo '<p id="message" class="updated">';
        /* translators: %s is replaced with the URL of the Widgets page */
		CustomSidebars::wp_kses_wf(sprintf(__( 'All sidebars have been locked, you cannot replace them. Go to <a href="%s">the widgets page</a> to unlock a sidebar.', 'custom-sidebars' ), admin_url( 'widgets.php' )));
		echo '</p>';
	}
}
