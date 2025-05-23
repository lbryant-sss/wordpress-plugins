/*!***********************************!*\
  !*** ./src/admin/js/ilj_promo.js ***!
  \***********************************/
(function ($) {
  /**
   * Config for tipso
   */
  var tipsoConfig_promo = {
    width: '',
    useTitle: true,
    delay: 100,
    speed: 500,
    background: '#32373c',
    color: '#eeeeee',
    size: 'small',
    position: 'left'
  };
  $(function () {
    if (jQuery('.promo').length) {
      jQuery('.promo').find('.close').iljtipso(tipsoConfig_promo);
      jQuery('.promo').find('.close').on('click', function () {
        jQuery(this).closest('.promo').slideUp();
        jQuery.ajax({
          url: ajaxurl,
          type: "POST",
          data: {
            'action': 'ilj_hide_promo',
            'nonce': ilj_ajax_object.nonce
          }
        });
      });
    }
  });
})(jQuery);
