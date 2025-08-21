<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    $title       = esc_html__( 'Introducing EB Mega Menu', 'essential-blocks' );
    $description = esc_html__( 'Create stunning, multi-column mega menus directly in the block editor. Easily organize navigation with sections, images, icons, and dynamic content for a responsive and accessible user experience across devices.', 'essential-blocks' );
    $learn_more  = esc_html__( 'See Demos', 'essential-blocks' );
    $dismiss     = esc_html__( 'Dismiss', 'essential-blocks' );
    $doc_url     = esc_url( 'https://essential-blocks.com/demo/mega-menu' );
?>

<script type="text/javascript">
jQuery(document).ready(function ($) {
	const promoHtml = `
		<div class="eb-whats-new">
			<div class="eb-hn-title">
				<span class="dashicons dashicons-megaphone"></span>
				<span><?php echo $title; ?></span>
			</div>
			<div class="eb-hn-content">
				<p><?php echo $description; ?></p>
                <div class="eb-hn-buttons">
                    <button class="button button-primary">
                        <a href="<?php echo $doc_url; ?>" target="_blank" rel="noopener noreferrer"><?php echo $learn_more; ?></a>
                    </button>
                    <button class="button button-dismiss">
                        <span class="dashicons dashicons-dismiss"></span>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              					                                                 					                                                 					                                                 					                                                 					                                                 					                                                  <?php echo $dismiss; ?>
                    </button>
                </div>
			</div>
		</div>
	`;

	$('#toplevel_page_essential-blocks').append(promoHtml);

	$(document).on('click', '.eb-whats-new .button-dismiss', function () {
		$('.eb-whats-new').remove();
	});
});
</script>
