<div id="modal-plugin-deactivate" data-modal-backdrop="" tabindex="-1" class="hidden modal-wrapper popup-modal"
	data-plugin="<?php echo esc_attr( $clashingPlugin['plugin'] ); ?>"
	data-plugin-name="<?php echo esc_attr( $clashingPlugin['name'] ); ?>">
	<div class="popup-container">
		<div class="popup-inner">
			<!-- Modal header -->
			<div class="popup-header">
				<button type="button" class="close-modal" data-modal-hide="modal-plugin-deactivate">
					<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd"
							d="M0.293031 1.29308C0.480558 1.10561 0.734866 1.00029 1.00003 1.00029C1.26519 1.00029 1.5195 1.10561 1.70703 1.29308L6.00003 5.58608L10.293 1.29308C10.3853 1.19757 10.4956 1.12139 10.6176 1.06898C10.7396 1.01657 10.8709 0.988985 11.0036 0.987831C11.1364 0.986677 11.2681 1.01198 11.391 1.06226C11.5139 1.11254 11.6255 1.18679 11.7194 1.28069C11.8133 1.37458 11.8876 1.48623 11.9379 1.60913C11.9881 1.73202 12.0134 1.8637 12.0123 1.99648C12.0111 2.12926 11.9835 2.26048 11.9311 2.38249C11.8787 2.50449 11.8025 2.61483 11.707 2.70708L7.41403 7.00008L11.707 11.2931C11.8892 11.4817 11.99 11.7343 11.9877 11.9965C11.9854 12.2587 11.8803 12.5095 11.6948 12.6949C11.5094 12.8803 11.2586 12.9855 10.9964 12.9878C10.7342 12.99 10.4816 12.8892 10.293 12.7071L6.00003 8.41408L1.70703 12.7071C1.51843 12.8892 1.26583 12.99 1.00363 12.9878C0.741432 12.9855 0.49062 12.8803 0.305212 12.6949C0.119804 12.5095 0.0146347 12.2587 0.0123563 11.9965C0.0100779 11.7343 0.110873 11.4817 0.293031 11.2931L4.58603 7.00008L0.293031 2.70708C0.10556 2.51955 0.000244141 2.26525 0.000244141 2.00008C0.000244141 1.73492 0.10556 1.48061 0.293031 1.29308Z"
							fill="#1B004E" />
					</svg>
					<span class="sr-only"><?php esc_html_e( 'Close modal', 'nitropack' ); ?></span>
				</button>
				<img src="<?php echo plugin_dir_url( __FILE__ ) . '../images/info.svg'; ?>" width="46" height="46"
					class="icon rotate-180">
				<h3 class="text-center"><?php printf( 'Deactivate %s?', $clashingPlugin['name'] ); ?></h3>
			</div>
			<!-- Modal body -->
			<div class="popup-body text-center">
				<p><?php printf( esc_html__( 'This will turn off %s to help avoid conflicts with NitroPack. You can turn it back on anytime from the Plugins page.', 'nitropack' ), $clashingPlugin['name'] ); ?>
				</p>
			</div>

			<div class="popup-footer">
				<button type="button" class="btn btn-secondary popup-close"
					data-modal-hide="modal-plugin-deactivate"><?php esc_html_e( 'Cancel', 'nitropack' ); ?></button>
				<button type="button"
					class="btn btn-primary popup-action"><?php esc_html_e( 'Deactivate', 'nitropack' ); ?></button>
			</div>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function ($) {
		const $targetEl = document.getElementById('modal-plugin-deactivate'),
			modal = new Modal($targetEl),
			modal_wrapper = $('#modal-plugin-deactivate'),
			modal_text = modal_wrapper.find('.popup-body p'),
			modal_title = modal_wrapper.find('.popup-header h3'),
			modal_icon = modal_wrapper.find('.icon'),
			modal_footer = modal_wrapper.find('.popup-footer'),
			close_btn = modal_footer.find('.popup-close'),
			action_btn = modal_footer.find('.popup-action'),
			plugin = modal_wrapper.data('plugin'),
			plugin_name = modal_wrapper.data('plugin-name');

		action_btn.click(function (e) {
			$.ajax({
				url: ajaxurl,
				type: "POST",
				data: {
					action: "nitropack_conflict_plugin_deactivate",
					plugin: plugin,
					plugin_name: plugin_name,
					nonce: nitroNonce
				},
				beforeSend: function () {
					modal_icon.attr('src', '<?php echo plugin_dir_url( __FILE__ ) . '../images/loading.svg'; ?>');
					modal_title.html('<?php esc_html_e( 'Deactivating ' . $clashingPlugin['name'] . '...', 'nitropack' ); ?>');
					modal_footer.addClass('hidden');
					modal_text.text('');
				},
				success: function (resp) {
					if (resp.success) {
						NitropackUI.triggerToast('success', resp.data.message);
					} else {
						NitropackUI.triggerToast('error', resp.data.message);
					}
					setTimeout(function () {
						location.reload();
					}, 500);
				},
				error: function (xhr) {
					NitropackUI.triggerToast('error', "Something went wrong");
				}
			});
		});

	});
</script>