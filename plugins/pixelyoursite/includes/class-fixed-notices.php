<?php
namespace PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}


class NoticesFixed {

    private static $_instance;
    private $dismissedKey = "pys_free_fixed_dismissed_notices";

    public static function instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;

    }

    public function __construct() {
        add_action( 'init', [$this,'init'] );
    }

    function init() {
        global $pagenow;
        if ( ! current_user_can( 'manage_pys' ) ) {
            return;
        }
        // Exclude display of notifications on the update.php screen
        if ( $pagenow === 'update.php' ) {
            return;
        }

        $this->checkEnqueueStyles();

        add_action( 'admin_notices', [$this,'showNotices'], 2 );
        add_action( 'wp_ajax_pys_fixed_notice_dismiss', [$this,'catchOnCloseNotice'] );
        add_action('wp_ajax_pys_fixed_notice_opt_dismiss', [$this,'allCloseNotice']);

    }

    function showNotices() {

        require_once PYS_FREE_PATH . '/notices/fixed.php';
        $user_id = get_current_user_id();

        $this->isNeedToShow(adminGetFixedNotices(),(array)get_user_meta( $user_id, $this->dismissedKey,true ));
    }

    function allCloseNotice(){
        require_once PYS_FREE_PATH . '/notices/fixed.php';
        $notices = adminGetFixedNotices();
        $user_id = get_current_user_id();


        if ( empty( $user_id ) ) {
            return;
        }
        if ( empty( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'pys_fixed_notice_opt_dismiss') ) {
            return;
        }
        $dismissedSlugs = (array) get_option($this->dismissedKey, array());
        foreach ($notices as $noticesGroup)
        {
            foreach ($noticesGroup['multiMessage'] as $noticesMessage) {
                $dismissedSlugs[] = sanitize_text_field( $noticesMessage['slug'] );
            }

        }
        $dismissedSlugs = array_unique($dismissedSlugs);
        update_option( $this->dismissedKey, $dismissedSlugs );
        echo json_encode($dismissedSlugs);
        die();
    }

    function  catchOnCloseNotice() {
        require_once PYS_FREE_PATH . '/notices/fixed.php';
        $notices = adminGetFixedNotices();
        $user_id = get_current_user_id();


        if ( empty( $user_id ) || empty( $_POST['addon_slug'] ) || empty( $_POST['meta_key'] ) ) {
            return;
        }
        if ( empty( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'pys_fixed_notice_dismiss' ) ) {
            return;
        }

        $dismissedSlugs = (array) get_option($this->dismissedKey, array());
        foreach ($_POST['meta_key'] as $meta_key)
        {
            $dismissedSlugs[] = sanitize_text_field( $meta_key );
        }


        // save dismissed notice
        update_option( $this->dismissedKey, $dismissedSlugs );
        echo json_encode($this->whoIsNext($notices));
        die();
    }

    private function renderNotice($notice) {

        if ( ! current_user_can( 'manage_pys' ) ) {
            return;
        }

        if ( ! $notice ) {
            return;
        }

        ?>
        <div class="notice notice-info is-dismissible pys-promo-fixed-notice pys-fixed-notice <?php echo (isset($notice['enabelDismiss']) && $notice['enabelDismiss']==false)? 'notice-disable-dismiss' : ''; ?>" data-slug="<?=$notice['slug'] ?>">
            <div class="pys-fixed-notice-container">
                <div class="notice-content-top single-notice">
                    <div class="logo-notice">
                        <img src="<?php echo PYS_FREE_URL; ?>/dist/images/pys-logo.svg" alt="plugin logo"/>
                    </div>

                    <div class="notice-content">
                        <div class="notice-item">
                            <?php if($notice['title']) : ?>
                                <div class="notice-title">
                                    <?php echo $notice['title']; ?>
                                </div>
                            <?php endif; ?>

                            <?php if($notice['message']) : ?>
                                <div class="notice-message">
                                    <p><?php echo $notice['message']; ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn notice-dismiss custom-dismiss-button"><span
                            class="screen-reader-text"><i class="icon-delete"></i></span></button>

            </div>

        </div>
        <script type='application/javascript'>
            jQuery(document).on('click', '.pys-promo-fixed-notice .notice-dismiss', function () {
                _this = jQuery(this);
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'pys_fixed_notice_dismiss',
                        nonce: '<?php echo esc_attr( wp_create_nonce( 'pys_fixed_notice_dismiss'))?>',
                        addon_slug: 'free',
                        meta_key: [jQuery(this).parents('.pys-promo-fixed-notice').data('slug')]
                    },
                    success: function (response)
                    {
                        _this.closest('.pys-promo-fixed-notice').slideUp();
                    }
                });
            });
        </script>
        <?php
    }

	private function renderNoticeGroped( $group ) {
		if ( !current_user_can( 'manage_pys' ) ) {
			return;
		}

		if ( isset( $group[ 'multiMessage' ] ) ):
			?>
            <div class="notice notice-info is-dismissible pys-chain-fixed-notice pys-fixed-notice <?php echo isset( $group[ 'color' ] ) ? 'notice-color-' . $group[ 'color' ] : ''; ?> <?php echo ( isset( $group[ 'enabelDismiss' ] ) && $group[ 'enabelDismiss' ] == false ) ? 'notice-disable-dismiss' : ''; ?>">
                <div class="pys-fixed-notice-container">
                    <div class="notice-content-top">
						<?php if ( isset( $group[ 'enabelLogo' ] ) && $group[ 'enabelLogo' ] != false ) : ?>
                            <div class="logo-notice">
                                <img src="<?php echo PYS_FREE_URL; ?>/dist/images/pys-logo.svg" alt="plugin logo"/>
                            </div>
						<?php endif; ?>
                        <div class="notice-content">
							<?php foreach ( $group[ 'multiMessage' ] as $key => $notice ) :
								if ( !$notice ) {
									return;
								}
								?>

                                <div class="notice-item" data-slug="<?= $notice[ 'slug' ] ?>">
									<?php if ( isset( $notice[ 'title' ] ) && $notice[ 'title' ] != '' ) : ?>
                                        <div class="notice-title">
											<?php echo $notice[ 'title' ]; ?>
                                        </div>
									<?php endif; ?>

									<?php if ( isset( $notice[ 'message' ] ) && $notice[ 'message' ] != '' ) : ?>
                                        <div class="notice-message">
                                            <p><?php echo $notice[ 'message' ]; ?></p>
											<?php if ( ( isset( $notice[ 'button_text' ] ) && isset( $notice[ 'button_url' ] ) ) && ( $notice[ 'button_text' ] != '' && $notice[ 'button_url' ] != '' ) ) : ?>
                                                <a class="notice_watch_button notice-watch-link link" href="<?= $notice[ 'button_url' ] ?>"
                                                   target="_blank"><?= $notice[ 'button_text' ] ?></a>
											<?php endif; ?>
                                        </div>
									<?php endif; ?>

									<?php if ( $key !== array_key_last( $group[ 'multiMessage' ] ) ) : ?>
                                        <div class="line"></div>
									<?php endif; ?>
                                </div>

							<?php endforeach; ?>
                        </div>
                    </div>
					<?php if ( isset( $group[ 'optoutEnabel' ] ) && $group[ 'optoutEnabel' ] != false ) : ?>
                        <div class="notice_opt_out_block">
                            <div>
								<?php if ( isset( $group[ 'enabelYoutubeLink' ] ) && $group[ 'enabelYoutubeLink' ] != false ) : ?>
                                    <div class="bottom-chanel-link">
                                        <p>Improve your tracking with our video tips: <a
                                                    href="https://www.youtube.com/channel/UCnie2zvwAjTLz9B4rqvAlFQ?sub_confirmation=1"
                                                    target="_blank">Subscribe to our YouTube channel</a></p>
                                    </div>
								<?php endif; ?>
                                <div class="opt_out_message"><?php echo $group[ 'optoutMessage' ]; ?></div>
                            </div>

                            <div>
                                <button class="opt_out_dismiss_button"><?php echo $group[ 'optoutButtonText' ]; ?></button>
                            </div>
                        </div>
					<?php endif; ?>

                </div>

                <button type="button" class="btn notice-dismiss custom-dismiss-button"><span
                            class="screen-reader-text"><i class="icon-delete"></i></span></button>
            </div>

		<?php endif; ?>
        <script type='application/javascript'>
            jQuery( document ).on( 'click', '.opt_out_dismiss_button', function ( e ) {
                e.preventDefault();
                let _this = jQuery( this );
                jQuery.ajax( {
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'pys_fixed_notice_opt_dismiss',
                        nonce: '<?php echo esc_attr( wp_create_nonce( 'pys_fixed_notice_opt_dismiss'))?>',
                        addon_slug: 'free'
                    },
                    success: function (response)
                    {
                        _this.closest('.pys-chain-fixed-notice').slideUp();
                    }
                });
            });
            jQuery(document).on('click', '.pys-chain-fixed-notice .notice-watch-link', function (e) {
                _this = jQuery(this);
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                            action: 'pys_fixed_notice_dismiss',
                            nonce: '<?php echo esc_attr( wp_create_nonce( 'pys_fixed_notice_dismiss'))?>',
                            addon_slug: 'free',
                            meta_key: [_this.closest('.notice-item').data('slug')]
                        },
                        success: function (response)
                        {
                            _this.closest('.notice-item').addClass('closed-notice');
                            _this.closest('.notice-item').slideUp();
                            if(_this.closest('.pys-chain-fixed-notice').find('.notice-item').length == _this.closest('.pys-chain-fixed-notice').find('.notice-item.closed-notice').length)
                            {
                                _this.closest('.pys-chain-fixed-notice').slideUp();
                            }
                        }
                });
            });
        </script>
        <script type='application/javascript'>
            jQuery(document).on('click', '.pys-chain-fixed-notice .notice-dismiss', function () {
                var array_notice = [];
                jQuery(this).closest('.pys-chain-fixed-notice').find('.notice-item').each(function (){
                    array_notice.push(jQuery(this).data('slug'));
                })
                _this = jQuery(this);
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'pys_fixed_notice_dismiss',
                        nonce: '<?php echo esc_attr( wp_create_nonce( 'pys_fixed_notice_dismiss'))?>',
                        addon_slug: 'free',
                        meta_key: array_notice
                    },
                    success: function (response)
                    {
                        _this.closest('.pys-chain-fixed-notice').slideUp();
                    },
                });
            });
        </script>
    <?php
    }
    private function isNeedToShow($noticeGroups,$showedNoticesSlug) {
        $activePlugins = [];
        $grouped_notice_by_multimessage = array();
        if(isWooCommerceActive()) {
            $activePlugins[]='woo';
        }
        if(isWcfActive()) {
            $activePlugins[]='wcf';
        }
        if(isEddActive()) {
            $activePlugins[]='edd';
        }

        foreach ($noticeGroups as $keyGroup => $noticeGroup) {

            // check is notice has some plugin dependencies
            if( isset($noticeGroup['plugins']) && (count($noticeGroup['plugins']) == 0
                || (count(array_intersect($noticeGroup['plugins'], $activePlugins)) == count($activePlugins)
                    && count($noticeGroup['plugins']) == count($activePlugins)))
            ) {
				if ( isset( $noticeGroup[ 'type' ] ) && $noticeGroup[ 'type' ] == 'promo' ) {
					$screen = get_current_screen();
					$showing = ( $noticeGroup[ 'location' ] === 'plugin' && $screen && $screen->parent_base === 'pixelyoursite' ) || $noticeGroup[ 'location' ] === 'backend';
					if ( $showing && !in_array( $noticeGroup[ 'slug' ], $showedNoticesSlug ) ) {

						$this->renderNotice( $noticeGroup );
					}
				}
            }
        }

        $this->renderNoticeGroped($this->whoIsNext($noticeGroups));


    }
    private function whoIsNext($noticeGroups) {
        $minOrderBlock = 999999;
        $user_id = get_current_user_id();
        $noticeBlock = array();
		$screen = get_current_screen();
        foreach ($noticeGroups as $noticeGroup) {
			$showing = ( $noticeGroup[ 'location' ] === 'plugin' && $screen && $screen->parent_base === 'pixelyoursite' ) || $noticeGroup[ 'location' ] === 'backend';
			if($showing && isset($noticeGroup['type']) && $noticeGroup['type'] == 'event chain') {
                $paramGroup = $noticeGroup;
                unset($paramGroup['multiMessage']);
                $noticeBlock[$noticeGroup['order']] = $paramGroup;
                foreach ($noticeGroup['multiMessage'] as $notice){
                    if(!in_array($notice['slug'], (array) get_option( $this->dismissedKey, array())) && !in_array($notice['slug'], (array) get_user_meta($user_id, $this->dismissedKey, true))) {
                        $noticeBlock[$noticeGroup['order']]['multiMessage'][] = $notice;
                    }
                }
            }
        }

        foreach ($noticeBlock as $block)
        {
            if(($block['order'] <= $minOrderBlock) && isset($block['multiMessage'])){
                $minOrderBlock = $block['order'];
            }
        }
        if(get_option('free_next_chain_notice') != $minOrderBlock)
        {
            if((get_option('free_next_chain_notice') < $minOrderBlock && get_user_meta($user_id, 'free_next_chain_notice', true) < $minOrderBlock) && $minOrderBlock != 999999)
            {
                update_option('free_next_chain_notice', $minOrderBlock);
                update_option('free_expiration_chain_notice_dismissed_at', time() + $this->convertTimeToSeconds(isset($noticeBlock[$minOrderBlock]['wait']) ? $noticeBlock[$minOrderBlock]['wait'] : 24, 'hours'));
            }
            else if((get_option('free_next_chain_notice') > $minOrderBlock || get_user_meta($user_id, 'free_next_chain_notice', true) > $minOrderBlock) && $minOrderBlock != 999999)
            {
                update_option('free_expiration_chain_notice_dismissed_at', time() + $this->convertTimeToSeconds(0, 'seconds'));
            }
            else{
                update_option('free_next_chain_notice', $minOrderBlock);
            }
        }

        if(isset($noticeBlock[$minOrderBlock]) && (get_option('free_next_chain_notice') >= $minOrderBlock && time() >= get_option('free_expiration_chain_notice_dismissed_at')))
        {

            return $noticeBlock[$minOrderBlock];
        }

    }
    private function convertTimeToSeconds($timeValue = 24, $type = 'hours')
    {
        switch ($type){
            case 'hours':
                $time = $timeValue * 60 * 60;
                break;
            case 'minute':
                $time = $timeValue * 60;
                break;
            case 'seconds':
                $time = $timeValue;
                break;
        }
        return $time;
    }
    private function checkEnqueueStyles() {
        if ( ! wp_style_is( 'pys_notice' ) ) {
            wp_enqueue_style( 'pys_notice', PYS_FREE_URL . '/dist/styles/notice.min.css', array(), PYS_FREE_VERSION );
        }
    }
}

/**
 * @return NoticesFixed
 */
function NoticesFixed() {
    return NoticesFixed::instance();
}

NoticesFixed();