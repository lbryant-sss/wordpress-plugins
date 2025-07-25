jQuery(document).ready(function ($) {
	$('.wpcp-carousel-section').each(function () {
		var carousel_id = jQuery(this).attr('id');
		var carousel_type = jQuery(this).data('carousel_type');

		if ($('#' + carousel_id + '.wpcp-standard').length) {
			var selector = '#' + carousel_id + '.wpcp-carousel-section .swiper-slide:not(.swiper-slide-duplicate) [data-fancybox="wpcp_view"]';
		} else {
			var selector = '#' + carousel_id + '.wpcp-carousel-section .wpcp-single-item [data-fancybox="wpcp_view"]';
		}
		$().fancybox({
			selector: selector,
			backFocus: false,
			margin: [44, 0],
			baseClass: carousel_id + ' wpcf-fancybox-wrapper',
			animationDuration: 366,
			transitionDuration: 366,
			infobar: false,
			hash: false,
			caption: function (instance, item) {
				return '';
			},
			beforeShow: function (instance, slide) {
				$(".wpcf-fancybox-wrapper ~ .elementor-lightbox").css('display', 'none');
			},
			afterShow: function () {
				$(".wpcf-fancybox-wrapper ~ .elementor-lightbox").css('display', 'none');
			},
			btnTpl: {
				arrowLeft:
					'<button data-fancybox-prev class="fancybox-button fancybox-button--arrow_left" title="{{PREV}}">' +
					'<div class="wpcp-fancybox-nav-arrow"><i class="fa fa-chevron-left"></i></div>' +
					"</button>",
				arrowRight:
					'<button data-fancybox-next class="fancybox-button fancybox-button--arrow_right" title="{{NEXT}}">' +
					'<div class="wpcp-fancybox-nav-arrow"><i class="fa fa-chevron-right"></i></div>' +
					"</button>",
			},
		});
	})
});
