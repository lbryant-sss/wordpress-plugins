<?php
/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once(SG_POPUP_CLASSES_PATH.'/dataTable/Subscribers.php');
require_once(SG_POPUP_CLASSES_POPUPS_PATH.'SubscriptionPopup.php');
require_once(SG_POPUP_HELPERS_PATH.'AdminHelper.php');
use sgpb\AdminHelper;
use sgpb\SubscriptionPopup;

$subscribers = SubscriptionPopup::getSubscribersCount();
$allData = SubscriptionPopup::getAllSubscriptionForms();

$fistElement = array_values($allData);
if (isset($fistElement[0])) {
	$fistElement = $fistElement[0];
}
$subscribersSelectbox = AdminHelper::createSelectBox(
	$allData,
	__('Select subscription(s):', 'popup-builder'),
	array(
		'name' => 'sgpb-add-subscriber-input',
		'class' => 'js-sg-select2 js-sg-newsletter-forms sgpb-add-subscriber-input js-select-basic',
		'multiple' => 'multiple',
		'autocomplete' => 'off'
	)
);

$importSubscribersSelectbox = AdminHelper::createSelectBox(
	$allData,
	$fistElement,
	array(
		'name' => 'sgpb-import-subscriber-input',
		'class' => 'js-sg-select2 js-sg-import-list sgpb-add-subscriber-input js-select-basic',
		'autocomplete' => 'off'
	)
);
$isEmpty = empty($allData);
$disable = '';
if ($isEmpty) {
	$disable = 'disabled';
}
?>
<div class="sgpb sgpb-wrapper ">
	<div class="sgpb-subscription sgpb-padding-20">
		<h2 class="sgpb-header-h1 sgpb-margin-top-10 sgpb-margin-bottom-40"><?php esc_html_e('Subscribers', 'popup-builder')?></h2>
		<?php 
		if ( !empty( get_transient('sgpbImportSubscribersMessaage') ) )
		{
			?>
			<div class="sgpb-alert sgpb-newsletter-notice sgpb-alert-info fade in">	
				<span class="sgpb-newsletter-test-success-message"><?php echo esc_html( get_transient('sgpbImportSubscribersMessaage') );?></span>
			</div>
			<?php
			delete_transient('sgpbImportSubscribersMessaage'); 
		}
		?>
		
		<div class="sgpb-margin-bottom-20 sgpb-display-flex sgpb-justify-content-between">

			<div>
				<a href="javascript:void(0)"
				   data-target="addSubscriber"
				   class="sgpb-display-inline-block sgpb-btn sgpb-btn-blue--outline sgpb-btn--rounded sgpb-padding-x-30 sgpb-modal-btn">
					<?php esc_html_e('Add new', 'popup-builder'); ?>
				</a>
				<a href="javascript:void(0)"
				   class="sgpb-display-inline-block sgpb-btn sgpb-btn-blue--outline sgpb-btn--rounded sgpb-padding-x-30 sgpb-export-subscriber">
					<?php esc_html_e('Export', 'popup-builder'); ?>
				</a>
				<a href="javascript:void(0)"
				   data-target="importSubscriber"
				   class="sgpb-display-inline-block sgpb-btn sgpb-btn-blue--outline sgpb-btn--rounded sgpb-padding-x-30 sgpb-modal-btn">
					<?php esc_html_e('Import', 'popup-builder'); ?>
				</a>
			</div>


			<div style="text-align: right" id="sgpbPostSearch">
				<div class="sgpb--group">
					<input type="text" id="sgpbSearchInAllSubscribers" placeholder="Search Subscriber" class="sgpb-input">
					<input type="submit" value="GO!" id="sgpbSearchInAllSubscribersSubmit" class="sgpb-btn sgpb-btn-blue">
				</div>
			</div>
		</div>
		<?php
		$table = new SGPBSubscribers();
		echo wp_kses($table, AdminHelper::allowed_html_tags());
		?>
	</div>
</div>

<div id="addSubscriber" class="sgpb-display-none">
	<span id="addSubscriberHeader"><?php esc_html_e('Add New Subscribers', 'popup-builder'); ?></span>
	<div id="addSubscriberBody">
		<div class="formItem sgpb-subscriber-adding-error sg-hide-element">
			<div class="alert alert-danger fade in alert-dismissable">
				<?php esc_html_e('Error occurred: could not add subscriber.', 'popup-builder')?>
			</div>
		</div>
		<div class="sgpb-add-subscriber-header-spinner-column">
			<img src="<?php echo esc_url(SG_POPUP_IMG_URL.'ajaxSpinner.gif'); ?>" alt="gif" class="sgpb-subscribers-add-spinner js-sg-spinner js-sgpb-add-spinner sg-hide-element js-sg-import-gif" width="20px">
		</div>

		<div class="formItem">
			<?php echo wp_kses($subscribersSelectbox, AdminHelper::allowed_html_tags()); ?>
		</div>
		<div class="sg-hide-element sgpb-subscription-error formItem"><?php esc_html_e('Subscription is not selected', 'popup-builder')?>.</div>
		<div class="formItem">
			<input type="email" autocomplete="off" name="subs-email" class="sgpb-add-subscribers-email sgpb-add-subscriber-input sgpb-formItem-input" placeholder="<?php esc_html_e('Email', 'popup-builder')?>">
		</div>
		<div class="sg-hide-element sgpb-email-error formItem"><?php esc_html_e('Invalid email address', 'popup-builder')?>.</div>
		<div class="formItem">
			<input type="text" autocomplete="off" name="subs-firstName" class="sgpb-add-subscribers-first-name sgpb-add-subscriber-input sgpb-formItem-input" placeholder="<?php esc_html_e('First name', 'popup-builder')?>">
		</div>
		<div class="formItem">
			<input type="text" autocomplete="off" name="subs-firstName" class="sgpb-add-subscribers-last-name sgpb-add-subscriber-input sgpb-formItem-input" placeholder="<?php esc_html_e('Last name', 'popup-builder')?>">
		</div>
	</div>
	<div id="addSubscriberFooter">
		<input type="button" value="<?php esc_html_e('Add to list', 'popup-builder')?>"
		       class="sgpb-btn sgpb-btn-blue sgpb-add-to-list-js" data-ajaxNonce="<?php echo esc_attr(SG_AJAX_NONCE);?>">
	</div>
</div>
<div id="importSubscriber" class="sgpb-display-none">
	<span id="importSubscriberHeader"><?php esc_html_e('Import Subscribers', 'popup-builder'); ?></span>
	<div id="importSubscriberBody">
		<div class="formItem">
			<div class="formItem__title sgpb-margin-bottom-10">
				<?php esc_html_e('Select subscription(s):', 'popup-builder'); ?>
			</div>
			<?php echo wp_kses($importSubscribersSelectbox, AdminHelper::allowed_html_tags()); ?>
		</div>
		<div class="formItem">
			<div class="formItem__title">
				<?php esc_html_e('Import Subscribers from csv file:', 'popup-builder'); ?>
			</div>
		</div>
		<div class="formItem">
			<input class="formItem__input formItem__input_sgpb-popup-overlay" id="js-import-subscriber-file-url" type="text" size="36" name="js-import-subscriber-file-url" value="" readonly>
			<input class="formItem__input formItem__input_sgpb-popup-overlay" id="js-import-subscriber-file-id" type="hidden" name="js-import-subscriber-file-id" value="" readonly>
			<div class="easy-icons-wrapper">
				<div class="icons__item">
					<img id="js-import-subscriber-button" class="sgpb-cursor-pointer" src="<?php echo esc_url(SG_POPUP_PUBLIC_URL.'icons/cloud.svg'); ?>" title="<?php esc_html_e('Select file', 'popup-builder')?>" alt="<?php esc_html_e('Select file', 'popup-builder')?>">
				</div>
			</div>
		</div>
	</div>
	<div id="importSubscriberFooter">
		<input type="button" value="<?php esc_html_e('Import', 'popup-builder'); ?>"
		       class="sgpb-btn sgpb-btn-blue sgpb-import-subscriber-to-list" data-ajaxnonce="popupBuilderAjaxNonce" <?php echo esc_attr($disable); ?>>
	</div>
</div>
<?php
	wp_register_script( 'sgpb-subscribers-js-footer', '', array("jquery"), '1.0', true );
	wp_enqueue_script( 'sgpb-subscribers-js-footer'  );
	wp_add_inline_script( 'sgpb-subscribers-js-footer', "jQuery(document).ready(function($) {
		const myForm = $('#posts-filter-sgpbAllSubscribers');
		const searchValue = $('#sgpbAllSubscribers-search-input').val();
		$('#posts-filter-sgpbAllSubscribers .tablenav.top .tablenav-pages').append($('.subsubsub').addClass('show'));
		myForm.append($('#posts-filter-sgpbAllSubscribers .tablenav.bottom .tablenav-pages:not(.no-pages, .one-page) .pagination-links'));
		$('#sgpbSearchInAllSubscribers').val(searchValue);
		$('#sgpbSearchInAllSubscribers').keyup('enter', function (e) {
			if (e.key === 'Enter') {
				$('#sgpbAllSubscribers-search-input').val(this.value);
				$(myForm).submit();
			}
		});
		$('#sgpbSearchInAllSubscribersSubmit').on('click', function () {
			$('#sgpbAllSubscribers-search-input').val($('#sgpbSearchInAllSubscribers').val());
			$(myForm).submit();
		})
	});");
?>