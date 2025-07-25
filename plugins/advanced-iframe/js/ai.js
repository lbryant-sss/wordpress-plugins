/**
 *  Advanced iframe functions v2025.6
 */
/* jslint devel: true, unused: false */
/* globals ai_show_id_only:false, aiChangeUrl: false, aiResizeIframeHeightId: false, aiShowIframeId: false, findAndReplaceDOMText: false, aiShowDebug: false */

var aiEnableCookie = (typeof x === 'undefined') ? false : aiEnableCookie;
var aiId = '';
var aiExtraSpace = (typeof aiExtraSpace === 'undefined') ? 0 : aiExtraSpace;
var aiAccTime = 0;
var aiRealFullscreen = (typeof aiRealFullscreen === 'undefined') ? false : aiRealFullscreen;
var aiInFullscreen = false;
var aiOnloadEventsCounter = 0;
var aiOverflowHtml = jQuery('html').css('overflow') ?? 'visible';
var aiOverflowBody = jQuery('body').css('overflow') ?? 'visible';

var aiCallbackExists = typeof aiReadyCallbacks !== 'undefined' && aiReadyCallbacks instanceof Array;
var aiReadyCallbacks = aiCallbackExists ? aiReadyCallbacks : [];

/**
 * The debug messages are enabled if you enable the debug console.
 */
function aiDebugExtended(message) {
  if (typeof aiShowDebug !== 'undefined' && aiShowDebug) {
    if (console && console.log) {
      console.log("Advanced iframe: " + message);
    }
  }
}

/**
 *  This function resizes the iframe after loading to the height
 *  of then content of the iframe.
 *
 *  The extra space is not stored in the cookie! The height would
 *  be added every time otherwise and the iframe would grow,
 */
function aiResizeIframe(obj, resizeWidth, resizeMinHeight) {
  aiDebugExtended("aiResizeIframe");
  try {
    if (obj.contentWindow.location.href === 'about:blank') {
      return;
    }
    if (obj.contentWindow.document.body != null) {
      var oldScrollPosition = jQuery(window).scrollTop();
      obj.style.marginTop = '0';
      obj.style.marginBottom = '0';
      obj.height = Number(resizeMinHeight); // set to 1 because otherwise the iframe does never get smaller.
      obj.style.height = Number(resizeMinHeight) + 'px';
      var newHeight = aiGetIframeHeight(obj);
      aiDebugExtended("aiResizeIframe - newHeight: " + newHeight);
      obj.height = newHeight;
      obj.style.height = newHeight + 'px';

      // set the height of the zoom div
      const zoomDivjQuery = jQuery('#ai-zoom-div-' + obj.id);
      if (zoomDivjQuery.length !== 0) {
        var zoom = window['zoom_' + obj.id];
        zoomDivjQuery.css('height', newHeight * zoom);
      }

      if (aiEnableCookie && aiExtraSpace === 0) {
        aiWriteCookie(newHeight);
      }
      var hash = aiGetIframeHash(obj.contentWindow.location.href);
      if (hash !== '-1') {
        var iframeId = '#' + obj.id;
        try {
          var hashPosition = jQuery(iframeId).contents().find('#' + hash);
          if (hashPosition.length !== 0) {
            var hashPositionTop = hashPosition.offset().top;
            oldScrollPosition = Math.round(jQuery(iframeId).offset().top + hashPositionTop);
          }
        } catch (e) {
          // in case of an invalid hash it is ignored.
        }
      }
      setTimeout(function () {
        jQuery("html,body").scrollTop(oldScrollPosition);
      }, 50);

      if (resizeWidth === 'true') {
        var newWidth = aiGetIframeWidth(obj);
        obj.width = newWidth;
        obj.style.width = newWidth + 'px';
      }
      var fCallback = window['resizeCallback' + obj.id];
      fCallback();
      // fires the onload event again if iframes are wrapped
      if (window.frameElement != null) {
        parent.jQuery('iframe').trigger('onload');
      }
      aiHandleAnchorLinkScrolling(obj.id);
    } else {
      // body is not loaded yet - we wait 100 ms.
      setTimeout(function () {
        aiResizeIframe(obj, resizeWidth);
      }, 100);
    }
  } catch (e) {
    if (console && console.error) {
      console.error(
        'Advanced iframe configuration error: You have enabled the resize of the iframe for pages on the same domain. But you use an iframe page on a different domain. You need to use the external workaround like described in the settings. Also check the next log. There the browser message for this error is displayed.');
      console.log(e);
    }
  }
}

function aiHandleAnchorLinkScrolling(iframeId) {
  var iframejQuery = jQuery('#' + iframeId);
  var posIframeTop = iframejQuery.offset().top;

  iframejQuery.contents().find('body').on('click', "a[href^='#']", function () {
    var anchorId = jQuery(this).attr('href');
    var anchorElem = jQuery('#' + iframeId).contents().find(anchorId);

    if (anchorElem.length !== 0) {
      var anchorPositionTop = anchorElem.offset().top;
      var totalPosition = Math.round(posIframeTop + anchorPositionTop + 2);
      jQuery("html,body").scrollTop(totalPosition);
    }
  });
}

/**
 * returns the position of a #hash in the iframe or -1 if none was found.
 */

function aiGetIframeHash(url) {
  var hash = url.split('#')[1];
  return (hash) ? hash : '-1';
}

/**
 *  Get the iframe height
 */
function aiGetIframeHeight(obj) {
  var bodyHeight = Math.max(obj.contentWindow.document.body.scrollHeight,
    obj.contentWindow.document.body.offsetHeight,
    obj.contentWindow.document.documentElement.scrollHeight,
    obj.contentWindow.document.documentElement.offsetHeight);
  return bodyHeight + aiExtraSpace;
}

/**
 *  Get the iframe width
 */
function aiGetIframeWidth(obj) {
  var oldWidth = obj.width;
  obj.width = 1; // set to 1 because otherwise I don't get the minimum width
  obj.style.width = '1px';
  var bodyWidth = Math.max(obj.contentWindow.document.body.scrollWidth,
    obj.contentWindow.document.body.offsetWidth,
    obj.contentWindow.document.documentElement.scrollWidth,
    obj.contentWindow.document.documentElement.offsetWidth);
  if (bodyWidth !== 1) { // avoid that fully responsive sites do not get displayed at all.
    obj.width = bodyWidth;
    obj.style.width = bodyWidth + 'px';
  } else {
    obj.width = oldWidth;
    obj.style.width = oldWidth + 'px';
  }
  return bodyWidth;
}

/**
 *  Get the current width of the iframe inside the parent.
 */
function aiGetParentIframeWidth(obj) {
  if (obj != null) {
    const iframejQuery = jQuery('#' + obj.id);
    if (iframejQuery.length !== 0) {
      return iframejQuery.width();
    }
    return -1;
  }
}

/**
 *  Resizes an iframe to a given height.
 *  this is used for xss enabled iframes.
 *  Please read the documentation!
 */
function aiResizeIframeHeightById(id, nHeight) {
  aiDebugExtended("aiResizeIframeHeightById - id: " + id + ", nHeight: " + nHeight);
  try {
    var fCallback = window['resizeCallback' + id];
    fCallback();
    var height = parseInt(nHeight, 10) + aiExtraSpace;
    var iframe = document.getElementById(id);
    if (iframe === null) {
      if (console && console.error) {
        console.error('Advanced iframe configuration error: The iframe to resize could not be found. The id of the iframe and the one defined for ai_external.js (' + id
          + ') are most likely different! Check your settings.');
        return;
      }
    }
    var oldScrollposition = jQuery(document).scrollTop();
    iframe.height = height;
    iframe.style.height = height + 'px';
    jQuery("html,body").scrollTop(oldScrollposition);
    if (aiEnableCookie && aiExtraSpace === 0) {
      aiWriteCookie(height);
    }
    // send the new height to the parent if it is a wrapped call
    var parentResizeCall = window['aiExecuteWorkaround_' + id];
    if (parentResizeCall != null) {
      parentResizeCall();
    }
  } catch (e) {
    if (console && console.error) {
      console.error('Advanced iframe configuration error: The id of the parent and the external workaround are different! Check your settings.');
      console.log(e);
    }
  }
}

/**
 * Scrolls the parent window to the top.
 * This is e.g. wanted when you have a link in the iframe and you want that the
 * page starts at the top and not that only the iframe changes.
 *
 * Sometimes are 2 onload events after another. To prevent that this causes
 * a jump to the top a delay of 1 sec is used for the increase of the counter.
 */
function aiScrollToTop(id, position) {
  aiDebugExtended("aiScrollToTop - id: " + id + ", position: " + position);

  if (aiOnloadEventsCounter > 0) {
    var posTop = 0;
    if (position === 'iframe') {
      posTop = jQuery('#' + id).offset().top;
    }
    setTimeout(function () {
      aiDebugExtended("aiScrollToTop - posTop: " + posTop);
      window.scrollTo(0, posTop);
    }, 100);
  }
  setTimeout(function () {
    aiOnloadEventsCounter++;
  }, 1000);
}

/**
 * Writes the last height to the cookie.
 */
function aiWriteCookie(height) {
  var cookieName = 'ai-last-height';
  if (aiId !== '') {
    cookieName = cookieName + '-' + aiId;
  }
  document.cookie = cookieName + '=' + height;
}

/**
 * Reads the cookie and preset the height of the iframe
 */
function aiUseCookie() {
  var cookieName = 'ai-last-height';
  if (aiId !== '') {
    cookieName = cookieName + '-' + aiId;
  }
  var allcookies = document.cookie;
  // Get all the cookies pairs in an array
  var cookiearray = allcookies.split(';');
  // Now take key value pair out of this array
  for (var i = 0; i < cookiearray.length; i++) {
    var name = cookiearray[i].split('=')[0];
    var value = cookiearray[i].split('=')[1];
    // cookie does exist and has a numeric value
    if (name === cookieName && value !== null && aiIsNumeric(value)) {
      var iframe = document.getElementById(aiId);
      iframe.height = parseInt(value, 10);
      iframe.style.height = value + 'px';
    }
  }
}

/**
 *  check if we have a numeric input
 */
function aiIsNumeric(input) {
  return !isNaN(input);
}

/**
 * Disable the additional_height input field
 */
function aiDisableHeight() {
  jQuery('#additional_height').attr('readonly', 'readonly')
  .val('0');
}

/**
 * Enable the additional_height input field
 */
function aiEnableHeight() {
  jQuery('#additional_height').removeAttr('readonly');
}

/**
 * Removes all elements from an iframe except the given one
 *
 * @param iframeId id of the iframe
 * @param showElement the id, class (jQuery syntax) of the element that should be displayed.
 */
function aiShowElementOnly(iframeId, showElement) {
  aiDebugExtended("aiShowElementOnly");
  try {
    var iframe = jQuery(iframeId).contents().find('body');
    var selectedBox = iframe.find(showElement).clone(true, true);
    iframe.find('*').not(jQuery('script')).remove();
    iframe.prepend(selectedBox);
  } catch (e) {
    if (console && console.error) {
      console.error(
        'Advanced iframe configuration error: You have enabled to show only one element of the iframe for pages on the same domain. But you use an iframe page on a different domain. You need to use the pro version of the external workaround like described in the settings. Also check the next log. There the browser message for this error is displayed.');
      console.log(e);
    }
  }
}

function aiCheckIfValidTarget(evt, elements) {
  var targ;
  if (!evt) {
    evt = window.event;
  }
  if (evt.target) {
    targ = evt.target;
  } else if (evt.srcElement) {
    targ = evt.srcElement;
  }
  if (targ.nodeType === 3) {
    targ = targ.parentNode;
  }

  var parts = elements.split(',');
  // check each part if we have a match...
  for (var i = 0; i < parts.length; ++i) {
    var selectorArray = parts[i].split(':');
    if (selectorArray[0].toLowerCase() === targ.nodeName.toLowerCase()) {
      if (selectorArray.length > 1) {
        if (targ.id.toLowerCase().indexOf(selectorArray[1].toLowerCase()) !== -1) {
          return true;
        }
      } else {
        return true;
      }
    }
  }
  return false;
}

function aiOpenSelectorWindow(url) {
  aiDebugExtended("aiOpenSelectorWindow");
  var localWidth = jQuery('#width').val();
  var localHeight = jQuery('#ai-height-0').val();

  if (localWidth.indexOf('%') >= 0 || Number(localWidth) < 900) {
    localWidth = 900;
  }
  localWidth = Number(localWidth) + 40;
  if (localWidth > (screen.width)) {
    localWidth = screen.width;
  }
  if (localHeight.indexOf('%') >= 0) {
    localHeight = screen.height;
  } else {
    localHeight = Number(localHeight) + 480;
  }
  if (localHeight > (screen.height - 50)) {
    localHeight = screen.height - 50;
  }
  var options = 'width=' + localWidth + ',height=' + localHeight + ',left=0,top=0,resizable=1,scrollbars=1';
  var popup_window = window.open(url, '', options);
  popup_window.focus();
}

function aiDisableAiResizeOptions(value) {
  jQuery('#onload_resize_delay').prop('readonly', value);
  jQuery('input[id=store_height_in_cookie1]:radio, input[id=store_height_in_cookie2]:radio').attr('disabled', value);
  jQuery('#additional_height').prop('readonly', value);
  jQuery('input[id=onload_resize_width1]:radio, input[id=onload_resize_width2]:radio').attr('disabled', value);
  jQuery('#resize_on_click').prop('readonly', value);
  jQuery('#resize_on_click_elements').prop('readonly', value);
  jQuery('#resize_on_ajax').prop('readonly', value);
  jQuery('input[id=resize_on_ajax_jquery1]:radio, input[id=resize_on_ajax_jquery2]:radio').attr('disabled', value);

  var selector = '#onload_resize_delay, #store_height_in_cookie1, #additional_height, #onload_resize_width1, ';
  selector += '#resize_on_click, #resize_on_click_elements, #resize_on_ajax, #resize_on_ajax_jquery1';
  aiDisableTextSection(value, selector);
}

function aiDisablePartOfIframeOptions(value) {
  jQuery('#show_part_of_iframe_x').prop('readonly', value);
  jQuery('#show_part_of_iframe_y').prop('readonly', value);
  jQuery('#show_part_of_iframe_height').prop('readonly', value);
  jQuery('#show_part_of_iframe_width').prop('readonly', value);
  jQuery('input[id=show_part_of_iframe_allow_scrollbar_horizontal1]:radio, input[id=show_part_of_iframe_allow_scrollbar_horizontal2]:radio').attr('disabled', value);
  jQuery('input[id=show_part_of_iframe_allow_scrollbar_vertical1]:radio, input[id=show_part_of_iframe_allow_scrollbar_vertical2]:radio').attr('disabled', value);
  jQuery('#show_part_of_iframe_next_viewports').prop('readonly', value);
  jQuery('input[id=show_part_of_iframe_next_viewports_loop1]:radio, input[id=show_part_of_iframe_next_viewports_loop2]:radio').attr('disabled', value);
  jQuery('#show_part_of_iframe_new_window').prop('readonly', value);
  jQuery('#show_part_of_iframe_new_url').prop('readonly', value);
  jQuery('input[id=show_part_of_iframe_next_viewports_hide1]:radio, input[id=show_part_of_iframe_next_viewports_hide2]:radio').attr('disabled', value);
  jQuery('#show_part_of_iframe_style').prop('readonly', value);
  jQuery('input[id=show_part_of_iframe_zoom1]:radio, input[id=show_part_of_iframe_zoom2]:radio, input[id=show_part_of_iframe_zoom3]:radio').attr('disabled', value);
  jQuery('.media-query-input').prop('readonly', value);

  var selector = '#show_part_of_iframe_x, #show_part_of_iframe_y, #show_part_of_iframe_height, #show_part_of_iframe_width, ';
  selector += '#show_part_of_iframe_allow_scrollbar_horizontal1, #show_part_of_iframe_next_viewports, #show_part_of_iframe_next_viewports_loop1, ';
  selector += '#show_part_of_iframe_new_window, #show_part_of_iframe_new_url, #show_part_of_iframe_next_viewports_hide1, #show_part_of_iframe_style, ';
  selector += '#show_part_of_iframe_zoom1, #show_part_of_iframe_allow_scrollbar_vertical1, #add-media-query-show_part_of_iframe_media_query';
  aiDisableTextSection(value, selector);

  if (value) {
    jQuery('#add-media-query-show_part_of_iframe_media_query').hide();
    jQuery('.ai-delete').hide();
  } else {
    jQuery('#add-media-query-show_part_of_iframe_media_query').show();
    jQuery('.ai-delete').show();
  }

}

function aiDisableLazyLoadOptions(value) {
  jQuery('#enable_lazy_load_threshold').prop('readonly', value);
  jQuery('#enable_lazy_load_fadetime').prop('readonly', value);
  jQuery('input[id=enable_lazy_load_reserve_space1]:radio, input[id=enable_lazy_load_reserve_space2]:radio').attr('disabled', value);
  jQuery('input[id=enable_lazy_load_manual1]:radio, input[id=enable_lazy_load_manual2]:radio, input[id=enable_lazy_load_manual3]:radio').attr('disabled', value);

  var selector = '#enable_lazy_load_threshold, #enable_lazy_load_fadetime, #enable_lazy_load_reserve_space1, #enable_lazy_load_manual1';
  aiDisableTextSection(value, selector);
}

function aiDisableIframeAsLayerOptions(value) {
  jQuery('input[id=show_iframe_as_layer_full]:radio').attr('disabled', value);
  jQuery('#show_iframe_as_layer_header_file').prop('readonly', value);
  jQuery('#show_iframe_as_layer_header_height').prop('readonly', value);
  jQuery('#show_iframe_as_layer_autoclick_delay').prop('readonly', value);
  jQuery('#show_iframe_as_layer_autoclick_hide_time').prop('readonly', value);
  jQuery('input[id=show_iframe_as_layer_header_position1]:radio, input[id=show_iframe_as_layer_header_position2]:radio').attr('disabled', value);
  jQuery('input[id=show_iframe_as_layer_full1]:radio, input[id=show_iframe_as_layer_full2]:radio, input[id=show_iframe_as_layer_full3]:radio').attr('disabled', value);
  jQuery('input[id=show_iframe_as_layer_keep_content1]:radio, input[id=show_iframe_as_layer_keep_content2]:radio').attr('disabled', value);

  var selector = '#show_iframe_as_layer_full, #show_iframe_as_layer_header_file, #show_iframe_as_layer_header_height, ';
  selector += '#show_iframe_as_layer_header_position1, #show_iframe_as_layer_full1, #show_iframe_as_layer_keep_content1, ';
  selector += '#show_iframe_as_layer_autoclick_delay, #show_iframe_as_layer_autoclick_hide_time';

  aiDisableTextSection(value, selector);

}

function aiDisableAddParamOptions(value) {
  jQuery('input[id=add_iframe_url_as_param_direct1]:radio, input[id=add_iframe_url_as_param_direct2]:radio').attr('disabled', value);
  jQuery('#add_iframe_url_as_param_prefix').prop('readonly', value);
  var selector = '#add_iframe_url_as_param_prefix, #add_iframe_url_as_param_direct1';
  aiDisableTextSection(value, selector);
}

function aiDisableTextSection(value, selector) {
  if (value) {
    jQuery(selector).closest('tr').addClass('disabled');
  } else {
    jQuery(selector).closest('tr').removeClass('disabled');
  }
}

var aiInstance;

/**
 *  This function initializes all checks that are done by Javascript
 *  on the admin page like enabling disabling fields...
 */
function aiInitAdminConfiguration(isPro, acc_type) {
  // enable checkbox of onload_resize_delay and if resize is set to true external workaround is set to false
  if (jQuery('input[type=radio][name=onload_resize]:checked').val() === 'false') {
    aiDisableAiResizeOptions(true);
  }
  jQuery('input[type=radio][name=onload_resize]').click(function () {
    if (jQuery(this).val() === 'true') {
      jQuery('input:radio[name=enable_external_height_workaround]')[1].checked = true; // set to external!
      aiDisableAiResizeOptions(false);
    } else {
      jQuery('#onload_resize_delay').val('');
      aiDisableAiResizeOptions(true);

    }
  });

  // if external workaround is set to to true resize on load is set to false and the
  // onload_resize_delay is made readonly
  jQuery('input[type=radio][name=enable_external_height_workaround]').click(function () {
    if (jQuery(this).val() === 'true') {
      jQuery('input:radio[name=onload_resize]')[1].checked = true;
      jQuery('#onload_resize_delay').val('');
      aiDisableAiResizeOptions(true);
    }
  });

  // Show only a part of the iframe enable/disable
  if (jQuery('input[type=radio][name=show_part_of_iframe]:checked').val() === 'false') {
    aiDisablePartOfIframeOptions(true);
  }
  jQuery('input[type=radio][name=show_part_of_iframe]').click(function () {
    if (jQuery(this).val() === 'false') {
      aiDisablePartOfIframeOptions(true);
    } else {
      aiDisablePartOfIframeOptions(false);
    }
  });

  // show_iframe_as_layer enable/disable
  if (jQuery('input[type=radio][name=show_iframe_as_layer]:checked').val() === 'false') {
    aiDisableIframeAsLayerOptions(true);
  }
  jQuery('input[type=radio][name=show_iframe_as_layer]').click(function () {
    if (jQuery(this).val() === 'false') {
      aiDisableIframeAsLayerOptions(true);
    } else {
      aiDisableIframeAsLayerOptions(false);
    }
  });

  // if expert mode
  if (jQuery('input[type=radio][name=expert_mode]:checked').val() === 'true') {
    jQuery('.description').css('display', 'none');
    jQuery('table.form-table th').css('cursor', 'pointer')
    .css('padding-top', '8px')
    .css('padding-bottom', '2px')
    .click(function () {
      jQuery('.description').css('display', 'none');
      jQuery('.description', jQuery(this).parent()).css('display', 'block');
    });
    jQuery('table.form-table td').css('padding-top', '5px').css('padding-bottom', '5px');
  }
  jQuery('input[type=radio][name=expert_mode]').click(function () {
    if (jQuery(this).val() === 'false') {
      jQuery('.description').css('display', 'block');
      jQuery('table.form-table th').css('cursor', 'auto')
      .css('padding-top', '20px')
      .css('padding-bottom', '20px')
      .off('click');
      jQuery('table.form-table td').css('padding-top', '15px').css('padding-bottom', '15px');
    } else {
      jQuery('.description').css('display', 'none');
      jQuery('table.form-table th').css('cursor', 'pointer')
      .css('padding-top', '8px')
      .css('padding-bottom', '2px')
      .click(function () {
        jQuery('.description').css('display', 'none');
        jQuery('.description', jQuery(this).parent()).css('display', 'block');
      });
      jQuery('table.form-table td').css('padding-top', '5px').css('padding-bottom', '5px');
    }
  });

  const accordionjQuery = jQuery('#accordion');
  accordionjQuery.find('h1').click(function () {
    jQuery(this).next().slideToggle(aiAccTime);
  }).next().hide();

  accordionjQuery.find('a').click(function () {
    var hash = jQuery(this).prop('hash');
    var hash_only = '#h1-' + hash.substring(1);
    jQuery(hash_only).next().show();
    location.hash = hash_only;
  });

  // lazy load
  if (jQuery('input[type=radio][name=enable_lazy_load_manual]:checked').val() === 'false') {
    jQuery('#enable_lazy_load_manual_element').prop('readonly', true);
  }

  jQuery('input[type=radio][name=enable_lazy_load_manual]').click(function () {
    if (jQuery(this).val() === 'false' || jQuery(this).val() === 'auto') {
      jQuery('#enable_lazy_load_manual_element').prop('readonly', true);
    } else {
      jQuery('#enable_lazy_load_manual_element').prop('readonly', false);
    }
  });

  // add_iframe_url_as_param
  if (jQuery('input[type=radio][name=add_iframe_url_as_param]:checked').val() === 'false') {
    aiDisableAddParamOptions(true);
  }
  jQuery('input[type=radio][name=add_iframe_url_as_param]').click(function () {
    aiDisableAddParamOptions(jQuery(this).val() === 'false');
  });

  if (jQuery('input[type=radio][name=enable_lazy_load]:checked').val() === 'false') {
    aiDisableLazyLoadOptions(true);
    jQuery('#enable_lazy_load_manual_element').prop('readonly', true);
  }

  jQuery('input[type=radio][name=enable_lazy_load]').click(function () {
    if (jQuery(this).val() === 'false') {
      aiDisableLazyLoadOptions(true);
      jQuery('#enable_lazy_load_manual_element').prop('readonly', true);
    } else {
      aiDisableLazyLoadOptions(false);
      const valueLazyLoad = jQuery('input[type=radio][name=enable_lazy_load_manual]:checked').val();
      if (valueLazyLoad === 'false' || valueLazyLoad === 'auto') {
        jQuery('#enable_lazy_load_manual_element').prop('readonly', true);
      } else {
        jQuery('#enable_lazy_load_manual_element').prop('readonly', false);
      }
    }
  });

  jQuery('.confirmation').on('click', function () {
    return confirm('Are you sure? Selecting OK will set all settings to the default.');
  });

  jQuery('.confirmation-file').on('click', function () {
    return confirm('Do you really want to delete the file?');
  });

  jQuery('.confirmation-hash').on('click', function () {
    return confirm('Do you really want to delete the hash/URL cache?');
  });

  jQuery('a.post').click(function (e) {
    e.stopPropagation();
    e.preventDefault();
    var href = this.href;
    var parts = href.split('?');
    var url = parts[0];
    var params = parts[1].split('&');
    var pp, inputs = '';
    url += '?' + params[0];
    for (var i = 1, n = params.length; i < n; i++) {
      pp = params[i].split('=');
      inputs += '<input type="hidden" name="' + pp[0] + '" value="' + pp[1] + '" />';
    }
    // Add twg-options nonce
    var nonceValue = jQuery('#twg-options').val();
    inputs += '<input type="hidden" name="twg-options" value="' + nonceValue + '" />';

    jQuery('body').append('<form action="' + url + '" method="post" id="poster">' + inputs + '</form>');
    jQuery('#poster').submit();
  });

  jQuery('.ai-input-search').keyup(function () {
    var searchTerm = jQuery('input.ai-input-search').val().toLowerCase();
    aiSettingsSearch(searchTerm, acc_type);
  })
  .on('click', function () {
    setTimeout(function () {
      var searchTerm = jQuery('input.ai-input-search').val().toLowerCase();
      aiSettingsSearch(searchTerm, acc_type);
    }, 100);
  });

  jQuery(document).on('click', '.nav-tab-wrapper a', function () {
    var current_tab = jQuery(this).attr('id');
    jQuery('section').hide();
    jQuery('section.' + current_tab).show();
    jQuery('#current_tab').val(current_tab.substr(4, 1));
    jQuery('.nav-tab').removeClass('nav-tab-active');
    jQuery(this).addClass('nav-tab-active');
    jQuery(this).blur();

    return false;
  });

  // set the links between tabs and open the right one at the right section.
  jQuery(document).on('click', 'a#external-workaround-link', function () {
    jQuery('.external-workaround').click();
    location.hash = 'tab_3';
    // no flash
    aiShowHeader('tab_3');
    return false;
  });
  jQuery(document).on('click', 'a#resize-same-link', function () {
    jQuery('.advanced-settings-tab').click();
    jQuery('#id-advanced-resize').removeClass('closed');
    location.hash = 'id-advanced-resize';
    // no flash
    aiShowHeader('id-advanced-resize');
    return false;
  });
  jQuery(document).on('click', 'a.jquery-help-link', function () {
    jQuery('.help-tab').click();
    jQuery('#id-help-jquery').removeClass('closed');
    jQuery('#jquery-help').show();
    location.hash = 'id-help-jquery';
    // no flash
    aiShowHeader('id-help-jquery');
    return false;
  });
  jQuery(document).on('click', 'a#browser-detection-link', function () {
    jQuery('.help-tab').click();
    jQuery('#id-help-browser').removeClass('closed');
    jQuery('#browser-help').show();
    location.hash = 'id-help-browser';
    // no flash
    aiShowHeader('id-help-browser');
    return false;
  });
  jQuery(document).on('click', 'a.howto-id-link', function () {
    jQuery('.help-tab').click();
    jQuery('#id-help-id').removeClass('closed');
    location.hash = 'id-help-id';
    // no flash
    aiShowHeader('id-help-id');
    return false;
  });
  jQuery(document).on('click', '.modifycontent-link', function () {
    jQuery('.advanced-settings-tab').click();
    jQuery('#id-advanced-modify-iframe').removeClass('closed');
    location.hash = 'id-advanced-modify-iframe';
    aiShowHeader('id-advanced-modify-iframe', 'tr-' + jQuery(this).data('detail'));
    return false;
  });

  jQuery(document).on('click', '.id-modify-css-iframe-link', function () {
    jQuery('.advanced-settings-tab').click();
    jQuery('#id-advanced-modify-iframe').removeClass('closed');
    location.hash = 'id-modify-css-iframe';
    aiShowHeader('id-advanced-modify-iframe', 'tr-' + jQuery(this).data('detail'));
    return false;
  });

  jQuery(document).on('click', '.modify-target', function () {
    jQuery('.advanced-settings-tab').click();
    jQuery('#id-advanced-modify-iframe').removeClass('closed');
    location.hash = 'id-modify-target';
    aiShowHeader('id-advanced-modify-iframe', 'tr-' + jQuery(this).data('detail'));
    return false;
  });

  jQuery(document).on('click', 'a.link-external-domain', function () {
    jQuery('#id-external-different').removeClass('closed');
    location.hash = '#id-external-different';
    // no flash - 'id-external-different'
    aiShowHeader('id-external-different');
    return false;
  });
  jQuery(document).on('click', 'a.link-id-external-ai-config-post', function () {
    jQuery('#id-external-ai-config-post').removeClass('closed');
    location.hash = '#id-external-ai-config-post';
    aiShowHeader('id-external-ai-config-post', 'tr-use_post_message');
    return false;
  });
  jQuery(document).on('click', 'a.link-id-external-ai-overview', function () {
    jQuery('#id-external-ai-overview').removeClass('closed');
    location.hash = '#id-external-ai-overview';
    aiShowHeader('id-external-ai-overview', 'id-external-ai-overview');
    return false;
  });
  jQuery(document).on('click', 'a.post-message-help-link', function () {
    jQuery('.help-tab').click();
    jQuery('#id-help-communication').removeClass('closed');
    location.hash = '#id-help-communication';
    aiShowHeader('id-help-communication', 'id-help-communication');
    return false;
  });

  jQuery(document).on('click', 'a.enable-admin', function () {
    jQuery('.options-tab').click();
    jQuery('#id-options-display').removeClass('closed');
    location.hash = '#id-options-display';
    aiShowHeader('id-options-display', 'tr-demo');
    return false;
  });

  jQuery(document).on('click', 'a.enter-registration', function () {
    jQuery('.options-tab').click();
    jQuery('#id-options-registration').removeClass('closed');
    location.hash = '#id-options-registration';
    aiShowHeader('id-options-registration', 'tr-demo');
    return false;
  });
  
  jQuery(document).on('click', 'a.enter-pro', function () {
    jQuery('.options-tab').click();
    jQuery('#id-options-pro').removeClass('closed');
    location.hash = '#id-options-pro';
    aiShowHeader('id-options-pro', 'first');
    return false;
  });

  jQuery(document).on('click', 'a#user-help-link', function () {
    jQuery('#user-help').css('display', 'block');
    return false;
  });
  jQuery(document).on('click', 'a#user-meta-link', function () {
    jQuery('#meta-help').css('display', 'block');
    return false;
  });

  jQuery(document).on('click', '#ai-selector-help-link', function () {
    jQuery('#ai-selector-help').slideDown(1000);
    return false;
  });

  jQuery(document).on('click', '.ai-selector-help-link-move', function () {
    jQuery('#ai-selector-help').show('slow');
    location.hash = '#ai-selector-help-link';
    // no flash
    aiShowHeader('ai-selector-help-link');
    return false;
  });

  jQuery('#ai_form').submit(function () {
    aiSetScrollposition();
  });

  // Close postboxes that should be closed.
  jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
  // Postboxes setup.
  if (typeof postboxes !== 'undefined') {
    postboxes.add_postbox_toggles('toplevel_page_advanced-iframe');
  }

  jQuery('.ai-spinner').css('display', 'none');
  jQuery("#" + acc_type).next().show();

  jQuery(document).on('click', '#test-pro-admin.is-permanent-closable button', function () {
    closeInfoPermanent('test-pro-admin');
  });

  jQuery(document).on('click', '#show-registration-message.is-permanent-closable button', function () {
    closeInfoPermanent('show-registration-message');
  });

  jQuery(document).on('click', '#show-version-message.is-permanent-closable button', function () {
    closeInfoPermanent('show-version-message');
  });

  jQuery(document).on('click', '.mq-breakpoint-height a', function (evt) {
    jQuery(this).parent().remove();
    aiUpdateHeightHiddenField('height');
    evt.preventDefault();
    return false;
  });

  jQuery(document).on('click', 'a#add-media-query-height', function (evt) {
    // count existing elements
    var nextNr = jQuery(".mq-breakpoint-height").length + 1;
    jQuery(this).parent().append('<div id="breakpoint-row-height-' + nextNr + '" class="mq-breakpoint-height">' +
      '<input type="text" id="ai-height-' + nextNr
      + '" style="width:150px;margin-top:5px;"  onblur="aiCheckHeightNumber(this, \'height\');" placeholder="Insert height"/> &nbsp;Breakpoint: '
      +
      '<input type="text" id="ai-breakpoint-height-' + nextNr
      + '" style="width:130px;" onblur="aiCheckHeightNumber(this, \'height\');" placeholder="Insert breakpoint"/>' +
      '<a id="delete-media-query-' + nextNr + '" href="#" class="delete ai-delete">Delete</a>');
    evt.preventDefault();
    return false;
  });

  jQuery(document).on('click', '.mq-breakpoint-show_part_of_iframe_media_query a', function (evt) {
    jQuery(this).parent().remove();
    aiUpdateHeightHiddenFieldMediaQuery('show_part_of_iframe_media_query');
    evt.preventDefault();
    return false;
  });

  jQuery(document).on('click', 'a#add-media-query-show_part_of_iframe_media_query', function (evt) {
    // count existing elements
    var nextNr = jQuery(".mq-breakpoint-show_part_of_iframe_media_query").length + 1;
    jQuery(this).parent().append('<div id="breakpoint-row-show_part_of_iframe_media_query-' + nextNr
      + '" class="mq-breakpoint-show_part_of_iframe_media_query">' +
      'x: <input type="text" id="ai-x-show_part_of_iframe_media_query-' + nextNr + '" class="media-query-input" ' +
      ' onblur="aiCheckHeightNumberMediaQuery(this, \'show_part_of_iframe_media_query\');" placeholder="x"/>' +
      ' &nbsp;y: <input type="text" id="ai-y-show_part_of_iframe_media_query-' + nextNr + '" class="media-query-input" '
      +
      ' onblur="aiCheckHeightNumberMediaQuery(this, \'show_part_of_iframe_media_query\');" placeholder="y"/>' +
      ' &nbsp;w: <input type="text" id="ai-w-show_part_of_iframe_media_query-' + nextNr + '" class="media-query-input" '
      +
      ' onblur="aiCheckHeightNumberMediaQuery(this, \'show_part_of_iframe_media_query\');" placeholder="width"/>' +
      ' &nbsp;h: <input type="text" id="ai-h-show_part_of_iframe_media_query-' + nextNr + '" class="media-query-input" '
      +
      ' onblur="aiCheckHeightNumberMediaQuery(this, \'show_part_of_iframe_media_query\');" placeholder="height"/>' +
      ' &nbsp;iframe width: <input type="text" id="ai-i-show_part_of_iframe_media_query-' + nextNr
      + '" class="media-query-input" style="width:100px;" ' +
      ' onblur="aiCheckHeightNumberMediaQuery(this, \'show_part_of_iframe_media_query\');" placeholder="iframe width"/>'
      +
      ' &nbsp;Breakpoint: <input type="text" id="ai-breakpoint-show_part_of_iframe_media_query-' + nextNr
      + '" class="media-query-input" style="width:130px;" ' +
      ' onblur="aiCheckHeightNumberMediaQuery(this, \'show_part_of_iframe_media_query\');" placeholder="Insert breakpoint"/>'
      +
      '<a id="delete-media-query-show_part_of_iframe_media_query-' + nextNr
      + '" href="#" class="delete ai-delete">Delete</a>');
    evt.preventDefault();
    return false;
  });
}

function aiCheckHeightNumber(element, id) {
  aiCheckInputNumber(element);
  aiUpdateHeightHiddenField(id);
}

function aiCheckHeightNumberMediaQuery(element, id) {
  aiCheckInputNumber(element);
  aiUpdateHeightHiddenFieldMediaQuery(id);
}

function aiUpdateHeightHiddenField(id) {
  var heightDefault = jQuery('#ai-' + id + '-0').val();
  var breakpoints = [];
  jQuery('.mq-breakpoint-' + id).each(function () {

    var heightChild = jQuery(this).children().eq(0).val();
    var breakpointChild = jQuery(this).children().eq(1).val();

    if (heightChild !== '' && breakpointChild !== '') {
      breakpoints.push({
        heightChild: heightChild,
        breakpointChild: breakpointChild
      });
    }
  });

  // we sort to have higher breakpoints first. Because of css cascading styles order is important
  breakpoints.sort(function (a, b) {
    return b.breakpointChild - a.breakpointChild;
  });

  let output = heightDefault;
  breakpoints.forEach(function (element) {
    output += ',' + element.heightChild + '|' + element.breakpointChild;
  });
  jQuery('#' + id).val(output);
  const descriptionjQuery = jQuery('#description-' + id);
  const newVal = descriptionjQuery.html().split('Shortcode attribute: ')[0];
  descriptionjQuery.html(newVal + 'Shortcode attribute: ' + id + '="' + output + '"');
}

function aiUpdateHeightHiddenFieldMediaQuery(id) {
  var breakpoints = [];
  jQuery('.mq-breakpoint-' + id).each(function () {

    var mediaX = jQuery(this).children().eq(0).val();
    var mediaY = jQuery(this).children().eq(1).val();
    var mediaW = jQuery(this).children().eq(2).val();
    var mediaH = jQuery(this).children().eq(3).val();
    var mediaIW = jQuery(this).children().eq(4).val();
    var breakpointChild = jQuery(this).children().eq(5).val();

    if ((mediaX !== '' || mediaY !== '' || mediaW !== '' || mediaH !== '' || mediaIW !== '') &&
      breakpointChild !== '') {
      breakpoints.push({
        mediaX: mediaX,
        mediaY: mediaY,
        mediaW: mediaW,
        mediaH: mediaH,
        mediaIW: mediaIW,
        breakpointChild: breakpointChild
      });
    }
  });

  // we sort to have higher breakpoints first. Because of css cascading styles order is important
  breakpoints.sort(function (a, b) {
    return b.breakpointChild - a.breakpointChild;
  });

  let output = '';
  breakpoints.forEach(function (element) {
    output += ',' + element.mediaX + '|' + element.mediaY + '|' +
      element.mediaW + '|' + element.mediaH + '|' + element.mediaIW + '|' + element.breakpointChild
  });
  output = output.replace(/(^,)|(,$)/g, "");
  jQuery('#' + id).val(output);
  // update description
  const descriptionjQuery = jQuery('#description-' + id);
  const newVal = descriptionjQuery.html().split('Shortcode attribute: ')[0];
  descriptionjQuery.html(newVal + 'Shortcode attribute: ' + id + '="' + output + '"');
}

function aiSettingsSearch(searchTerm, accType) {
  var found = 0;

  if (searchTerm !== '') {
    jQuery('#ai p').not('.form-table p').hide();
    jQuery('#ai ul').not('.form-table ul').hide();
    jQuery('#ai ol').not('.form-table ol').hide();
    if (accType !== 'false') {
      jQuery('#ai h1').not('.show-always').hide();
      jQuery('#ai #accordion').attr('id', 'acc');
      jQuery('#ai #acc > div').show();
      jQuery('#ai #spacer-div').show();
    }
    jQuery('#ai h2,#ai .icon_ai,#ai h3,#ai h4').not('.show-always').hide();
    jQuery('#ai .form-table').addClass('ai-remove-margin');
    jQuery('#ai hr, .signup_account_container, .config-file-block').hide();
    jQuery('#ai .hide-always').hide();
    jQuery('#ai .hide-search').hide();
    jQuery('#ai .postbox-container').not('.show-always').hide();
    jQuery('#ai .show-always p').show();
    jQuery('#ai .show-always ul').show();
    jQuery('#ai .show-always ol').show();
    jQuery('#ai .show-always h2,#ai .show-always .icon_ai,#ai .show-always h3,#ai .show-always h4').show();
  } else {
    jQuery('#ai p').not('.form-table p').show();
    jQuery('#ai section .ai-anchor').show();
    jQuery('#ai ul').not('.form-table ul').show();
    jQuery('#ai ol').not('.form-table ol').show();
    if (accType !== 'false') {
      jQuery('#ai h1').not('.show-always').show();
      jQuery('#ai #acc').attr('id', 'accordion');
      jQuery('#ai #accordion > div').hide();
      jQuery('#ai #spacer-div').hide();
    }
    jQuery('#ai h2,#ai .icon_ai,#ai h3,#ai h4').not('.show-always').show();
    jQuery('#ai .form-table').removeClass('ai-remove-margin');
    jQuery('#ai hr, .signup_account_container, .config-file-block').show();
    jQuery('#ai .sub-domain-container').show();
    jQuery('#ai .hide-search').show();
    jQuery('#ai .hide-always').hide();
    jQuery('#ai .postbox-container').show();

    setTimeout(function () {
      jQuery('#ai .postbox-container .closed .inside').css('display', '');
    }, 5);

  }

  const markTabHeaderjQuery = jQuery('#ai .mark-tab-header');
  markTabHeaderjQuery.removeClass('mark-tab-header');

  var firstHit = '';

  // check the search.
  jQuery('#ai tr').each(function () {
    var $this = jQuery(this);
    var valueLabel = $this.find('th').text();
    var valueDescription = $this.find('p.description').text();

    valueLabel = (valueLabel !== undefined) ? valueLabel.toLowerCase() : 'XXXXXXX';
    valueDescription = (valueDescription !== undefined) ? valueDescription.toLowerCase() : 'XXXXXXX';

    if (valueLabel.indexOf(searchTerm) === -1 && valueDescription.indexOf(searchTerm) === -1) {
      if ($this.parents('.show-always').length === 0) {
        $this.addClass('hide-setting');
      }
    } else {
      $this.closest('table').prevAll('h2:first').show();
      $this.closest('.postbox-container').show();
      $this.closest('.postbox-container').find('h2, .inside').show();
      $this.closest('table').prevAll('#ai .icon_ai:first').show();
      $this.closest('table').nextAll('p.button-submit:first').show();
      $this.removeClass('hide-setting');
      $this.closest('.hide-search').show();

      if (searchTerm.length > 2) {
        var headerId = $this.closest('section').attr('class');
        if (headerId !== undefined) {
          jQuery('#' + headerId).addClass('mark-tab-header');
          if (firstHit === '') {
            firstHit = headerId;
          }
        }
      }
      found++;
    }
  });
  if (found === 0) {
    jQuery('#ai-input-search-result').show();
    markTabHeaderjQuery.removeClass('mark-tab-header');
  } else {
    jQuery('#ai-input-search-result').hide();
    // https://github.com/padolsey/findAndReplaceDOMText
    aiInstance && aiInstance.revert();
    if (searchTerm !== '' && searchTerm.length > 2) {
      var regex = RegExp(searchTerm, 'gi');
      aiInstance = findAndReplaceDOMText(document.getElementById('tab_wrapper'), {
        find: regex,
        wrap: 'em'
      });
    }
    jQuery('#' + firstHit).click();

  }
}

/**
 *  Resizes the iframe with a certain ratio.
 *  Width is read and the height is than calculated.
 */
function aiResizeIframeRatio(obj, ratio) {
  aiDebugExtended("aiResizeIframeRatio");
  var width = jQuery('#' + obj.id).width();
  var valueRatio = parseFloat(ratio.replace(',', '.'));
  var newHeight = Math.ceil(width * valueRatio);
  obj.height = newHeight;
  obj.style.height = newHeight + 'px';
}

/**
 * Generate a shortcode string from the current settings.
 */
function aiGenerateShortcode(isPro) {
  var output = '[advanced_iframe ';

  // default section
  const securityKey = jQuery('#securitykey').val();
  if (securityKey !== '') {
    output += 'securitykey="' + securityKey + '" ';
  }
  output += 'use_shortcode_attributes_only="true" ';

  var include_html_val = jQuery('#include_html').val();
  var include_url_val = jQuery('#include_url').val();
  var document_domain_add = jQuery('#document_domain_add').val();

  if (include_html_val === undefined || (include_html_val === '' && include_url_val === '')) {
    var src = jQuery('#src').val();
    if (src === '') {
      alert('Required url is missing.');
    } else {
      output += 'src="' + src + '" ';
    }

    output += aiGenerateTextShortcode('src_hide');
    output += aiGenerateTextShortcode('width');
    output += aiGenerateTextShortcode('height');
    output += aiGenerateRadioShortcode('scrolling', 'none');
    output += aiGenerateRadioShortcode('add_surrounding_p', 'false');
    output += aiGenerateRadioShortcode('enable_ios_mobile_scolling', 'false');
    output += aiGenerateTextShortcode('marginwidth');
    output += aiGenerateTextShortcode('marginheight');
    output += aiGenerateTextShortcode('frameborder');
    output += aiGenerateRadioShortcode('transparency', 'true');
    output += aiGenerateTextShortcode('class');
    output += aiGenerateTextShortcode('style');
    output += aiGenerateTextShortcodeWithDefault('id', 'advanced_iframe');
    output += aiGenerateTextShortcode('name');
    output += aiGenerateRadioShortcode('allowfullscreen', 'false');
    output += aiGenerateTextShortcode('safari_fix_url');
    output += aiGenerateTextShortcode('sandbox');
    output += aiGenerateTextShortcode('title');
    output += aiGenerateTextShortcode('allow');
    output += aiGenerateRadioShortcode('loading', 'lazy');
    output += aiGenerateTextShortcode('referrerpolicy');
    output += aiGenerateTextShortcode('custom');

    // advanced settings
    output += aiGenerateTextShortcode('url_forward_parameter');
    output += aiGenerateTextShortcode('map_parameter_to_url');
    output += aiGenerateRadioShortcode('add_iframe_url_as_param', 'false');
    output += aiGenerateTextShortcode('add_iframe_url_as_param_prefix');
    output += aiGenerateRadioShortcode('add_iframe_url_as_param_direct', 'false');
    output += aiGenerateRadioShortcode('use_iframe_title_for_parent', 'false');
    output += aiGenerateRadioShortcode('onload_scroll_top', 'false');
    output += aiGenerateRadioShortcode('hide_page_until_loaded', 'false');
    output += aiGenerateRadioShortcode('show_iframe_loader', 'false');
    output += aiGenerateTextShortcode('hide_content_until_iframe_color');
    output += aiGenerateTextShortcode('iframe_zoom');
    output += aiGenerateRadioShortcode('use_zoom_absolute_fix', 'false');
    output += aiGenerateRadioShortcode('auto_zoom', 'false');
    output += aiGenerateTextShortcode('auto_zoom_by_ratio');

    output += aiGenerateRadioShortcode('enable_responsive_iframe', 'false');
    output += aiGenerateTextShortcode('iframe_height_ratio');

    output += aiGenerateRadioShortcode('enable_lazy_load', 'false');
    output += aiGenerateTextShortcodeWithDefault('enable_lazy_load_threshold', '3000');
    output += aiGenerateRadioShortcode('enable_lazy_load_reserve_space', 'true');

    output += aiGenerateTextShortcode('enable_lazy_load_fadetime');
    output += aiGenerateRadioShortcode('enable_lazy_load_manual', 'false');
    output += aiGenerateRadioShortcode('enable_lazy_load_manual_element', 'false');
    output += aiGenerateTextShortcode('reload_interval');

    // modify the parent page
    output += aiGenerateTextShortcode('hide_elements');
    output += aiGenerateTextShortcode('content_id');
    output += aiGenerateTextShortcode('content_styles');
    output += aiGenerateTextShortcode('parent_content_css');
    output += aiGenerateRadioShortcode('add_css_class_parent', 'false');

    output += aiGenerateTextShortcode('change_parent_links_target');
    output += aiGenerateRadioShortcode('show_iframe_as_layer', 'false');
    output += aiGenerateRadioShortcode('show_iframe_as_layer_full', 'false');

    output += aiGenerateTextShortcode('show_iframe_as_layer_autoclick_delay');
    output += aiGenerateTextShortcode('show_iframe_as_layer_autoclick_hide_time');
    output += aiGenerateTextShortcode('show_iframe_as_layer_header_file');
    output += aiGenerateTextShortcodeWithDefault('show_iframe_as_layer_header_height', '100');
    output += aiGenerateRadioShortcode('show_iframe_as_layer_header_position', 'top');
    output += aiGenerateRadioShortcode('show_iframe_as_layer_keep_content', 'true');

    // show only a part of the iframe
    var showPartOfIframe = aiGenerateRadioShortcode('show_part_of_iframe', 'false');
    output += showPartOfIframe;

    if (showPartOfIframe !== '') {
      output += aiGenerateTextShortcodeWithDefault('show_part_of_iframe_x', -1);
      output += aiGenerateTextShortcodeWithDefault('show_part_of_iframe_y', -1);
      output += aiGenerateTextShortcode('show_part_of_iframe_width');
      output += aiGenerateTextShortcode('show_part_of_iframe_height');
      output += aiGenerateTextShortcode('show_part_of_iframe_media_query');
      output += aiGenerateRadioShortcode('show_part_of_iframe_allow_scrollbar_horizontal', 'false');
      output += aiGenerateRadioShortcode('show_part_of_iframe_allow_scrollbar_vertical', 'false');
      output += aiGenerateTextShortcode('show_part_of_iframe_style');
      output += aiGenerateRadioShortcode('show_part_of_iframe_zoom', 'false');

      output += aiGenerateTextShortcode('show_part_of_iframe_next_viewports');
      output += aiGenerateRadioShortcode('show_part_of_iframe_next_viewports_loop', 'false');
      output += aiGenerateTextShortcode('show_part_of_iframe_new_window');
      output += aiGenerateTextShortcode('show_part_of_iframe_new_url');
      output += aiGenerateRadioShortcode('show_part_of_iframe_next_viewports_hide', 'false');
    }

    // hide cover parts on an iframe
    output += aiGenerateTextShortcode('hide_part_of_iframe');
    output += aiGenerateRadioShortcode('fullscreen_button', 'false');
    output += aiGenerateTextShortcode('fullscreen_button_hide_elements');
    output += aiGenerateRadioShortcode('fullscreen_button_full', 'false');
    output += aiGenerateRadioShortcode('fullscreen_button_style', 'black');

    // same domain
    output += aiGenerateRadioShortcode('add_css_class_iframe', 'false');
    output += aiGenerateTextShortcode('iframe_hide_elements');
    output += aiGenerateTextShortcode('onload_show_element_only');
    output += aiGenerateTextShortcode('iframe_content_id');
    output += aiGenerateTextShortcode('iframe_content_styles');
    output += aiGenerateTextShortcode('iframe_content_css');
    output += aiGenerateTextShortcode('change_iframe_links');
    output += aiGenerateTextShortcode('change_iframe_links_target');
    output += aiGenerateTextShortcode('change_iframe_links_href');

    // resize content height
    output += aiGenerateTextShortcode('onload');
    output += aiGenerateRadioShortcode('onload_resize', 'false');
    output += aiGenerateTextShortcode('onload_resize_delay');
    output += aiGenerateRadioShortcode('store_height_in_cookie', 'false');
    output += aiGenerateTextShortcode('additional_height');
    output += aiGenerateRadioShortcode('onload_resize_width', 'false');
    output += aiGenerateTextShortcode('resize_on_ajax');
    output += aiGenerateRadioShortcode('resize_on_ajax_jquery', 'true');
    output += aiGenerateTextShortcode('resize_on_click');
    output += aiGenerateTextShortcodeWithDefault('resize_on_click_elements', 'a');

    output += aiGenerateTextShortcode('resize_on_element_resize');
    output += aiGenerateTextShortcodeWithDefault('resize_on_element_resize_delay', '250');

    // tabs
    output += aiGenerateTextShortcode('tab_hidden');
    output += aiGenerateTextShortcode('tab_visible');
    // cross domain ....
    output += aiGenerateRadioShortcode('add_document_domain', 'false');
    //
    if (document_domain_add === 'true') {
      output += aiGenerateTextShortcode('document_domain');
    }
    output += aiGenerateRadioShortcode('enable_external_height_workaround', 'external');
    output += aiGenerateRadioShortcode('hide_page_until_loaded_external', 'false');
    output += aiGenerateTextShortcode('pass_id_by_url');
    output += aiGenerateRadioShortcode('multi_domain_enabled', 'true');
    if (isPro === 'true') {
      output += aiGenerateRadioShortcode('use_post_message', 'true');
    } else {
      output += aiGenerateRadioShortcode('use_post_message', 'false');
    }
    // additional files
    output += aiGenerateTextShortcode('additional_css');
    output += aiGenerateTextShortcode('additional_js');
    output += aiGenerateTextShortcode('additional_js_file_iframe');
    output += aiGenerateTextShortcode('additional_css_file_iframe');
  } else { // include content directly
    if (include_html_val === '') {
      output += aiGenerateTextShortcode('include_url');
      output += aiGenerateTextShortcode('include_content');
      output += aiGenerateTextShortcode('include_height');
      output += aiGenerateTextShortcode('include_fade');
      output += aiGenerateRadioShortcode('include_hide_page_until_loaded', 'false');
    } else {
      output += aiGenerateTextShortcode('include_html');
    }
  }
  // options
  output += aiGenerateRadioShortcode('debug_js', 'false');
  output = output.slice(0, -1);
  output += ']';
  jQuery('#gen-shortcode').html(output);
}

/**
 * Generate a text shortcode with default
 */
function aiGenerateTextShortcodeWithDefault(field, defaultValue) {
  var output = '';
  var value = jQuery('#' + field);
  var val = value.val();
  if (value.length > 0 && val !== '' && val !== defaultValue) {
    output = field + '="' + val + '" ';
  }
  return output;
}

/**
 * Generate a text shortcode if the value is not empty or != 0
 */
function aiGenerateTextShortcode(field) {
  var output = '';
  var value = jQuery('#' + field);
  var val = value.val();
  if (value.length > 0 && val !== '' && val !== '0') {
    output = field + '="' + val + '" ';
  }
  return output;
}

/**
 * Generate a radio shortcode with default
 */
function aiGenerateRadioShortcode(field, defaultValue) {
  var output = '';
  var value = jQuery('input:radio[name=' + field + ']:checked');
  var val = value.val();

  if (field === 'enable_ios_mobile_scolling') {
    field = 'enable_ios_mobile_scrolling';
  }

  if (value.length > 0 && val !== defaultValue) {
    output += field + '="' + val + '" ';
  }
  return output;
}

/**
 * Add a css class to the parents to enable that the iframe parents
 * can be identified very easy. Is an is ist set ai-class-<id> is used.
 * Otherwise a-class-<number> with an increasing number is used.
 */
function aiAddCssClassAllParents(element) {
  var parents = jQuery(element).parentsUntil('html');
  var ai_class = 'ai-class-';
  for (var i = 0; i < parents.length; i++) {
    var id = jQuery(parents[i]).attr('id');
    if (typeof id !== 'undefined') {
      if (id.indexOf('ai-') !== 0) {
        jQuery(parents[i]).addClass(ai_class + id);
      }
    } else {
      jQuery(parents[i]).addClass(ai_class + i);
    }
  }
}

function aiAutoZoomExternalHeight(id, width, height, responsive) {
  aiDebugExtended("aiAutoZoomExternalHeight");
  var parentWidth = aiAutoZoomExternal(id, width, responsive);
  var zoomRatio = window['zoom_' + id];
  var oldScrollposition = jQuery(document).scrollTop();
  var newHeight = Math.ceil(height * zoomRatio);
  jQuery('#ai-zoom-div-' + id).css('height', newHeight);
  jQuery("html,body").scrollTop(oldScrollposition);
  return parentWidth;
}

function aiAutoZoomExternal(id, width, responsive) {
  aiDebugExtended("aiAutoZoomExternal");
  var obj = document.getElementById(id);
  var objAround = document.getElementById('ai-zoom-div-' + id);
  var jObj = jQuery('#' + id);

  if (responsive === 'true') {
    jObj.css('max-width', '100%');
  }
  var iframeWidth = width;
  var parentWidth = aiGetParentIframeWidth(obj);
  if (parentWidth === iframeWidth) {
    // we check again the surrounding div as some browser do return the zoomes value!
    parentWidth = aiGetParentIframeWidth(objAround);
  }
  var zoomRatio = parentWidth / iframeWidth;
  var zoomRatioRounded = Math.floor(zoomRatio * 100) / 100;
  if (zoomRatioRounded > 1) {
    zoomRatioRounded = 1;
  }

  aiSetZoom(id, zoomRatioRounded);
  window['zoom_' + id] = zoomRatioRounded;
  jObj.width(iframeWidth).css('max-width', 'none');
  return parentWidth;
}

function aiAutoZoom(id, responsive, ratio) {
  aiDebugExtended("aiAutoZoom");
  var parts = ratio.split('|');
  ratio = parts[0];
  var width = -1;
  if (parts.length !== 1) {
    width = parts[1];
  }

  var obj = document.getElementById(id);
  var iframeWidth;
  if (width === -1) {
    obj.width = 1; // set to 1 because otherwise the iframe does never get smaller.
    obj.style.width = '1px';
    iframeWidth = aiGetIframeWidth(obj);
    obj.width = iframeWidth;
    obj.style.width = iframeWidth + 'px';
  } else {
    iframeWidth = width;
  }

  var parentWidth = aiAutoZoomExternal(id, iframeWidth, responsive);
  if (ratio === '') {
    aiResizeIframe(obj, false);
  } else {
    var newheight = Math.ceil(iframeWidth * ratio);
    obj.height = newheight;
    obj.style.height = newheight + 'px';
    // set the height of the zoom div
    const zoomDivjQuery = jQuery('#ai-zoom-div-' + obj.id);
    if (zoomDivjQuery.length !== 0) {
      var zoom = window['zoom_' + obj.id];
      zoomDivjQuery.css('height', Math.ceil(newheight * zoom));
    }
  }
  return parentWidth;
}

/**
 * Set the zoom div settings dynamically.
 */
function aiSetZoom(id, zoom) {

  var obj = jQuery('#' + id);

  obj.css({
    '-ms-transform': 'scale(' + zoom + ')',
    '-moz-transform': 'scale(' + zoom + ')',
    '-o-transform': 'scale(' + zoom + ')',
    '-webkit-transform': 'scale(' + zoom + ')',
    'transform': 'scale(' + zoom + ')'
  });
}

function aiAutoZoomViewport(id, full) {

  var viewportDiv = jQuery(id);
  var outerDiv = viewportDiv.parent();
  var counter = 0;

  // We only go up and look for divs which are not from ai or p elements which are rendered by mistake.
  while (outerDiv.is('p') || (outerDiv.attr('id') !== undefined && outerDiv.attr('id').indexOf('ai-') === 0)) {
    outerDiv = outerDiv.parent();
    if (counter++ > 10) {
      alert('Unexpected div structure. Please disable the zoom.');
      break;
    }
  }

  var viewportDivWidth = viewportDiv.width();
  var outer_div_width = outerDiv.width();
  var viewportDivHeight = viewportDiv.height();
  var zoom = outer_div_width / viewportDivWidth;

  if (full === 'true' && zoom > 1) {
    zoom = 1;
  }

  aiSetZoom(viewportDiv.attr('id'), zoom);
  // set the margin because otherwise it is normally "centered" in the old area
  var marginLeft = -Math.round((viewportDivWidth - viewportDivWidth * zoom) / 2);
  var marginTop = -Math.round((viewportDivHeight - viewportDivHeight * zoom) / 2);
  viewportDiv.css({
    'margin-left': marginLeft + 'px',
    'margin-right': marginLeft + 'px',
    'margin-top': marginTop + 'px',
    'margin-bottom': marginTop + 'px'
  });

}

function aiResetAiSettings() {
  jQuery('#action').val('reset');
}

function aiCheckInputNumber(inputField) {
  inputField.value = inputField.value.split(' ').join('');
  var f = inputField.value;
  if (inputField.value === '') {
    return;
  }
  var match = f.match(
    /^(-)?([\d.])+(px|%|em|pt|vh|vw|rem|ch)?([-+])?([\d.]){0,7}(px|%|em|pt|vh|vw|rem|ch)?$/);

  if (!match) {
    alert(
      'Please check the value you have entered. Only numbers with a dot or with an optional px, %, em or pt are allowed.');
    setTimeout(function () {
      inputField.focus();
    }, 10);
  }
}

function aiCheckInputPurchaseCode(inputField) {
  inputField.value = inputField.value.split(' ').join('');
  var f = inputField.value;
  if (inputField.value === '') {
    return;
  }

  if (!f.match(/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i)) {
    alert('Please check the value you have entered. Your input seems not to be a valid purchase code.');
    inputField.value = '';
    setTimeout(function () {
      inputField.focus();
    }, 10);
  }
}

function aiCheckInputNumberOnly(inputField) {
  inputField.value = inputField.value.split(' ').join('');
  var f = inputField.value;
  if (inputField.value === '') {
    inputField.value = '0';
    return;
  }
  var match = f.match(/^(-)?([\d.])+$/);

  if (!match) {
    alert(
      'Please check the value you have entered. Only numbers without a dot or optional px, %, em or pt are allowed.');
    setTimeout(function () {
      inputField.focus();
    }, 10);
  }
}

// https://codepen.io/anon/pen/baaLYB/
function aiShowHeader(id, element) {
  var y = jQuery(window).scrollTop();
  jQuery(window).scrollTop(y - 40);
  if (element !== undefined) {
    aiFlashElement(element);
  }
}

function aiFlashElement(element) {
  setTimeout(function () {
    jQuery('#' + element).css('background-color', '#eee');
  }, 500);
  setTimeout(function () {
    jQuery('#' + element).css('background-color', '#fff');
  }, 900);
  setTimeout(function () {
    jQuery('#' + element).css('background-color', '#eee');
  }, 1300);
  setTimeout(function () {
    jQuery('#' + element).css('background-color', '#fff');
  }, 1700);
}

function aiSetScrollposition() {
  var scrollposition = jQuery(document).scrollTop();
  jQuery('#scrollposition').val(scrollposition); // +32
}

function aiResetShowPartOfAnIframe(id) {
  jQuery('#' + id).css('top', '0px').css('left', '0px').css('position', 'static');
  jQuery('#ai-div-' + id).css('width', 'auto').css('height', 'auto').css('overflow', 'auto').css('position', 'static');
}

function aiShowLayerIframe(event, id, path, showLoadingIcon, keep, reload) {
  aiDebugExtended("aiShowLayerIframe");
  keep = (keep === undefined) ? false : keep;
  reload = (reload === undefined) ? true : reload;

  var layerId = '#' + id;
  jQuery('#ai-zoom-div-' + id).show()
  .css('visibility', 'visible');
  jQuery(layerId).show();
  jQuery(layerId).css('visibility', 'visible');

  if (jQuery('#ai-layer-div-' + id).length) {
    layerId = '#ai-layer-div-' + id;
    jQuery(layerId).show()
    .css('visibility', 'visible');
  }

  jQuery('html').css('overflow-y', 'visible');
  jQuery('body').css('overflow', 'hidden')
  .append('<img alt="" id="ai_backlink" src="' + path + 'close.png" style="z-index:100005;position:fixed;top:0;right:0;cursor:pointer" />');

  var icon = '<!-- -->';
  if (reload && showLoadingIcon === 'true') {
    icon = '<div id="ai-div-loader-global" style="position: fixed;z-index:100004;margin-left:-33px;left: 50%;top:50%;margin-top:-33px"><img src="' + path
      + 'loader.gif" width="66" height="66" title="Loading" alt="Loading"></div>';
  }

  if (jQuery('#ai_backlayer').length === 0) { // we do not have a layer yet...
    jQuery(layerId).parent().append(
      '<div id="ai_backlayer" style="z-index:100001;position:fixed;top:0;left:0;width:100%;height:100%;background-color: rgba(50,50,50,0.5);overflow:hidden;cursor:pointer"><!-- --></div>' + icon);
  }

  jQuery('#ai_backlink, #ai_backlayer').click(function () {
    aiHideLayerIframe(id, keep);
  });
  if (!reload) {
    event.preventDefault();
    event.stopPropagation();
  }
}

function aiHideLayerIframe(id, keep) {
  aiDebugExtended("aiHideLayerIframe");
  const idjQuery = jQuery('#' + id);
  idjQuery.css('visibility', 'hidden');
  if (!keep) {
    idjQuery.attr('src', 'about:blank');
    aiLayerIframeHrefs[id] = 'about:blank';
  }
  jQuery('#ai-zoom-div-' + id).css('visibility', 'hidden');
  jQuery('#ai-layer-div-' + id).css('visibility', 'hidden');
  jQuery('#ai_backlink').remove();
  jQuery('#ai_backlayer').remove();
  jQuery('#ai-div-loader-global').remove();
  jQuery('body').css('overflow', 'auto');
  jQuery('html').css('overflow-y', 'scroll');
}

/**
 * As the src of an iframe cannot be read from a remote domain we remember
 * the urls from the links here for each opened iframe.
 */
var aiLayerIframeHrefs = [];

/**
 * Check if the location of the iframe is already the one of the link.
 * The iframe is only loaded if it is was not loaded already.
 *
 * true - if src and url of the iframe is different and need to be loaded
 * false - if it is already the same;
 */
function aiCheckReload(link, id) {
  var iframeSrc;
  if (typeof aiLayerIframeHrefs[id] === 'undefined') {
    iframeSrc = jQuery('#' + id).attr('src');
  } else {
    iframeSrc = aiLayerIframeHrefs[id];
  }
  var linkHref = jQuery(link).attr('href');
  // alert(linkHref + ": iframe:" + iframeSrc);
  aiLayerIframeHrefs[id] = linkHref;
  return (iframeSrc !== linkHref);
}

function aiChangeTitle(id) {
  aiDebugExtended("aiChangeTitle");
  try {
    var title = document.getElementById(id).contentDocument.title;
    if (title !== null && title !== 'undefined' && title !== '') {
      document.title = title;
    }
  } catch (e) {
    if (console && console.error) {
      console.error(
        'Advanced iframe configuration error: You have enabled to add the title if the iframe to the parent on the same domain. But you use an iframe page on a different domain. You need to use the pro version of the external workaround like described in the settings. Also check the next log. There the browser message for this error is displayed.');
      console.log(e);
    }
  }
}

/**
 * This changes the current url and adds or updates the
 * existing parameter with the given url
 */
function aiChangeUrlParam(loc, param, orig, prefix, isDirect) {
  aiDebugExtended("aiChangeUrlParam");
  var newUrl;
  var keepSlash = false;

  // add protocol if // is used in the shortcode
  if (orig.lastIndexOf("//", 0) !== -1) {
    orig = location.protocol + orig;
  }

  if (loc !== encodeURIComponent(orig)) {
    newUrl = aiSetGetParameter(param, loc);
    var removeProtocol = true;
    if (prefix.startsWith('hash')) {
      return aiGetUrlMapping(loc, param, prefix);
    } else if (prefix) {
      var repUrl = newUrl.replace(prefix, '');
      if (repUrl === newUrl) {
        removeProtocol = false;
      }
      newUrl = repUrl;
    }

    // remove protocol
    if (removeProtocol) {
      newUrl = newUrl.replace('http%3A%2F%2F', '');
      if (window.location.href.toLowerCase().lastIndexOf("http:", 0) !== -1) {
        newUrl = newUrl.replace('https%3A%2F%2F', 's|');
      } else {
        newUrl = newUrl.replace('https%3A%2F%2F', '');
      }
    }

    if (isDirect) {
      newUrl = aiRemoveQueryString(window.location.href);
      var locDecoded = decodeURIComponent(loc);
      var queryStart = locDecoded.indexOf('?');
      var hashStart = locDecoded.indexOf('#');
      if (queryStart !== -1) {
        var queryPart = locDecoded.slice(queryStart + 1);
        newUrl += '?' + queryPart;
        keepSlash = true;
      } else if (hashStart !== -1) {
        var hashPart = locDecoded.slice(hashStart + 1);
        newUrl += '?hash=' + hashPart;
        keepSlash = true;
      }
    }
    if (aiEndsWidth(newUrl, param + "=")) {
      newUrl = aiRemoveURLParameter(newUrl, param);
    }
  } else {
    var fullUrl = window.location.href;
    // remove param/* first
    fullUrl = fullUrl.split("/" + param + "/", 1)[0];
    newUrl = aiRemoveURLParameter(fullUrl, param);
  }
  var seperator = (newUrl.indexOf('?') >= 0) ? '&' : '?';
  newUrl = newUrl.replace("#", seperator + "hash=");
  aiSetBrowserUrl(newUrl, keepSlash);
}

function aiGetUrlMappingUrl(param, prefix, id) {
  var fullUrl = window.location.href;

  param = param.replace(":short", "");
  var newUrl = aiRemoveURLParameter(fullUrl, param);
  if (prefix.startsWith('hashrewrite')) {
    // remove param/* first
    var queryString = "";
    if (newUrl.indexOf('?') >= 0) {
      var newUrlArray = newUrl.split('?');
      newUrl = newUrlArray[0];
      queryString = '?' + newUrlArray[1];
    }
    var baseUrl = newUrl.split("/" + param + "/", 1)[0];
    // add the path before the query string
    if (!aiEndsWidth(baseUrl, "/")) {
      baseUrl += '/';
    }
    var path = param + "/";
    newUrl = baseUrl + path + id + queryString;
  } else {
    var seperator = (newUrl.indexOf('?') >= 0) ? '&' : '?';
    newUrl += seperator + param + "=" + id;
  }
  return newUrl;
}

function aiSetBrowserUrl(newUrl, keepSlash) {
  if (aiSupportsHistoryApi()) {
    if (!keepSlash) {
      newUrl = newUrl.replace(/%2F/g, '/');
    }
    window.history.pushState({}, '', newUrl);
    // I asume the back button is clicked.
    window.onpopstate = function (event) {
      if (event && event.state) {
        window.history.back();
      }
    };
  }
}

function aiRemoveQueryString(loc) {
  if (loc.indexOf('%3F') >= 0) {
    return loc.split('%3F')[0];
  } else if (loc.indexOf('?') >= 0) {
    return loc.split('?')[0];
  } else {
    return loc;
  }
}

function aiGetUrlMapping(url, param, prefix) {
  var data = {
    action: 'aip_map_url_action',
    security: MyAjax.security,
    url: url
  };

  // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
  jQuery.post(MyAjax.ajaxurl, data, function (id) {
    var newUrl = aiGetUrlMappingUrl(param, prefix, id);
    aiSetBrowserUrl(newUrl, false);
  });
}

function closeInfoPermanent(id) {
  var data = {
    action: 'aip_close_message_permanent',
    security: MyAjax.security,
    id: id
  };
  // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
  var message = 'The message before will only appear again when you reset the advanced iframe settings.';
  if (id === 'show-discount-message') {
    message = 'The message of advanced iframe shown before will only appear again when you reset the advanced iframe settings or a new discount is available.';
  } else if (id === 'show-registration-message') {
    message = 'The message will appear again after a page reload.';
  }

  jQuery.post(MyAjax.ajaxurl, data, function () {
    jQuery('h1').after('<div class="message-notice notice notice-success"><p>' + message + '</p></div>');
  });
  setTimeout(function () {
    jQuery(".message-notice").fadeOut()
  }, 4000);
}

function aiSupportsHistoryApi() {
  return !!(window.history && history.pushState);
}

function aigetIframeLocation(id) {
  try {
    var location = document.getElementById(id).contentWindow.location;
    return encodeURIComponent(location);
  } catch (e) {
    if (console && console.error) {
      console.error(
        'Advanced iframe configuration error: You have enabled to add the url to the url on the same domain. But you use an iframe page on a different domain. You need to use the pro version of the external workaround like described in the settings. Also check the next log. There the browser message for this error is displayed.');
      console.log(e);
    }
  }
}

/**
 * Replaces a url parameter with a given value.
 */
function aiSetGetParameter(paramName, paramValue) {
  var url = window.location.href;
  var splitAtAnchor = url.split('#');
  url = splitAtAnchor[0];
  var anchor = typeof splitAtAnchor[1] === 'undefined' ? '' : '#' + splitAtAnchor[1];
  if (url.indexOf(paramName + '=') >= 0) {
    var prefix = url.substring(0, url.indexOf(paramName + '='));
    var suffix = url.substring(url.indexOf(paramName + '='));
    suffix = suffix.substring(suffix.indexOf('=') + 1);
    suffix = (suffix.indexOf('&') >= 0) ? suffix.substring(suffix.indexOf('&')) : '';
    url = prefix + paramName + '=' + paramValue + suffix;
  } else {
    if (url.indexOf('?') < 0) {
      url += '?' + paramName + '=' + paramValue;
    } else {
      url += '&' + paramName + '=' + paramValue;
    }
  }
  return url + anchor;
}

/**
 * Remove a given parameter from the url
 */
function aiRemoveURLParameter(url, parameter) {
  //prefer to use l.search if you have a location/link object
  var urlparts = url.split('?');
  if (urlparts.length >= 2) {

    var prefix = encodeURIComponent(parameter) + '=';
    var pars = urlparts[1].split(/[&;]/g);

    //reverse iteration as may be destructive
    for (var i = pars.length; i-- > 0;) {
      //idiom for string.startsWith
      if (pars[i].lastIndexOf(prefix, 0) !== -1) {
        pars.splice(i, 1);
      }
    }
    if (pars.length !== 0) {
      url = urlparts[0] + '?' + pars.join('&');
    } else {
      url = urlparts[0];
    }
    return url;
  } else {
    return url;
  }
}

function aiEndsWidth(string, target) {
  return string.substr(-target.length) === target;
}

/**
 * Adds css to the end of the body to make sure that styles are
 * not overwritten by inline css of any kind.
 */
function aiAddCss(id, css) {
  css = decodeURIComponent(css.replace(/\+/g, '%20'));

  var body = jQuery(id).contents().find('body');
  var s = document.createElement('style');
  s.setAttribute('type', 'text/css');
  if (s.styleSheet) {   // IE
    s.styleSheet.cssText = css;
  } else {                // the world
    s.appendChild(document.createTextNode(css));
  }
  body.append(s);
}

/**
 * add a css file to the end of the body to make sure that styles
 *  are not overwritten by inline css of any kind.
 */
function aiAddCssFile(id, filename) {
  var body = jQuery(id).contents().find('body');
  var link = document.createElement('link');
  link.rel = 'stylesheet';
  link.type = 'text/css';
  link.href = filename;
  body.append(link);
}

/**
 * Add a Javascript file to the end of the body
 */
function aiAddJsFile(id, filename) {
  jQuery.ajaxSetup({cache: true});

  var body = jQuery(id).contents().find('body');
  var script = document.createElement('script');
  script.type = 'text/javascript';
  script.src = filename;
  body.append(script);
}

function aiPresetFullscreen() {
  jQuery('#style').val('position:fixed;z-index:9000;top:0px;left:0px;margin:0px');
  jQuery('#width').val('100%');
  jQuery('#ai-height-0').val('100%');
  jQuery('#content_id').val('html,body');
  jQuery('#content_styles').val('overflow:hidden');
  jQuery('#hide_content_until_iframe_color').val('#ffffff');
}

/**
 * main ready function that calls all generated callbacks.
 * Add dynamically created functions that should be loaded
 * when the page is read to aiReadyCallbacks
 */
jQuery(document).ready(function () {
  aiDebugExtended("document.ready called");

  // wordpress adds often p elements that have margins we remove here.
  jQuery('iframe').parent('p').css('margin', '0');

  aiWindowWidth = jQuery(window).width();
  jQuery.each(aiReadyCallbacks, function (index, callback) {
    callback();
  });

  jQuery('.ai-fullscreen-open').on('click', function () {
    jQuery(this).closest('.ai-wrapper-div').addClass("ai-fullscreen-wrapper");
    jQuery(this).closest('.ai-wrapper-div').find('iframe').addClass("ai-fullscreen");
    jQuery('.ai-fullscreen-open').hide();
    jQuery('.ai-fullscreen-hide').addClass("ai-fullscreen-display-none")
    .hide();
    jQuery('html,body').css('overflow', 'hidden');

    var id = jQuery(this).data("id");
    // show/render the close button
    jQuery('.ai-fullscreen-close-' + id).show();
    jQuery(document).on('keydown', function (evt) {
      if (evt.key === "Escape") {
        jQuery('.ai-fullscreen-close').trigger("click");
      }
    });
    aiOpenFullscreen();
    aiInFullscreen = true;
  });

  jQuery('.ai-fullscreen-close').on('click', function () {
    // remove css for fullscreen-open
    jQuery('div.ai-wrapper-div').removeClass("ai-fullscreen-wrapper")
    jQuery('iframe.ai-fullscreen').removeClass("ai-fullscreen");
    jQuery('html').css('overflow', aiOverflowHtml);
    jQuery('body').css('overflow', aiOverflowBody);

    // hide/remove the close button
    jQuery('.ai-fullscreen-close').hide();
    // show the fullscreen icon again
    jQuery('.ai-fullscreen-open').show();
    jQuery('.ai-fullscreen-display-none').removeClass("ai-fullscreen-display-none");
    jQuery('.ai-fullscreen-hide').show();

    jQuery(document).off("keydown");
    if (aiInFullscreen) {
      aiCloseFullscreen();
    }

  });

  setTimeout(function () {
    jQuery("#ai #ai-updated-text").css("visibility", "hidden")
  }, 4000);

  jQuery('#ai #checkIframes').on('click', function () {
    jQuery('.ai-spinner').css('display', 'inline-table');
    jQuery(this).addClass('disabled');
    setTimeout(aiDisableCheckIframes, 200);
  });

  var moved = false;
  jQuery('#aiDebugDivTotal').mousedown(function () {
    moved = false;
  }).mousemove(function () {
    moved = true;
  }).mouseup(function () {
    if (!moved) {
      var elem = jQuery('#aiDebugDiv');
      if (Math.floor(elem.height()) > '300') {
        elem.height('0px');
      } else {
        elem.height('400px');
      }
    }
  });

  if (typeof ai_show_id_only !== 'undefined') {
    const showIdOnlyjQuery = jQuery('#' + ai_show_id_only);
    if (showIdOnlyjQuery.length === 0) {
      alert('The element with the id "' + ai_show_id_only + '" cannot be found. Please check your configuration.');
    } else {
      showIdOnlyjQuery.siblings().hide();
      var parents = showIdOnlyjQuery.parents();
      parents.siblings().hide();
      parents.css('padding', '0px').css('margin', '0px').css('overflow', 'hidden');

      // we send the size of the element as post message if we are in an iframe
      if (parent === top) {
        var elementRaw = showIdOnlyjQuery[0];
        elementRaw.style.marginTop = elementRaw.style.marginBottom = '0';
        elementRaw.style.overflow = "hidden";
        var newHeightRaw = Math.max(elementRaw.scrollHeight, elementRaw.offsetHeight) + '';
        var newHeight = parseInt(newHeightRaw, 10);
        var data = {'aitype': 'height', 'height': newHeight, 'id': ai_show_id_only};
        var json_data = JSON.stringify(data);
        window.parent.postMessage(json_data, '*');
      }
    }
  }
});

function aiDisableCheckIframes() {
  var input = jQuery("<input>").attr("type", "hidden").attr("name", "checkIframes").val("true");
  jQuery("#ai_form").append(input)
  .submit();
  jQuery('#checkIframes').prop('disabled', 'disabled');
}

function aiProcessMessage(event, id, debug) {
  var jsObject;
  try {
    jsObject = JSON.parse(event.data);
  } catch (e) {
    // Result is not expected so we try if data is an object already
    // because a converter was not implemented properly.
    if (debug === 'debug' && console && console.log) {
      console.log(
        'Advanced iframe: The received message cannot be parsed and seems not to belong to advanced iframe pro. Please disable the postMessage debug mode if this o.k. and that this message is not shown anymore.');
      console.log("Advanced iframe: Unknown event: ", event);
    }
    jsObject = event.data;
  }
  try {
    // we only process objects from advanced iframe
    if (jsObject.hasOwnProperty('aitype')) {
      // we only process the ones for the same id here.
      if (id === jsObject.id) {
        var type = jsObject.aitype;
        if (type === 'debug') {
          aiProcessDebug(jsObject);
        } else if (type === 'scrollToTop') {
          aiProcessScrollToTop(jsObject);
        } else if (type === 'anchor') {
          aiProcessAnchor(jsObject);
        } else {
          // check if the data is of the expected
          if (type === 'height') {
            aiProcessHeight(jsObject);
          } else if (type === 'show') {
            aiProcessShow(jsObject);
          }
          for (var key in jsObject.data) {
            if (jsObject.data.hasOwnProperty(key)) {
              jQuery(key).html(jsObject.data[key]);
            }
          }
        }
      }
    }
  } catch (e) {
    if (debug === 'debug' && console && console.log) {
      console.log('Advanced iframe: The received message do not belong to advanced iframe pro. Please disable the postMessage debug mode if this o.k. and that this message is not shown anymore.');
      console.log(e);
    }
  }
}

function aiProcessDebug(jsObject) {
  var debugData = jsObject.data;
  const debugDivjQuery = jQuery('#aiDebugDiv');
  if (debugDivjQuery.length !== 0) {
    debugData = debugData.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    debugData = debugData.replace('\"', '"').replace(/\\/g, "");
    debugDivjQuery.append('<p class="ai-debug-remote"> r: ' + debugData + '</p>');
  }
}

function aiProcessScrollToTop(jsObject) {
  var id = jsObject.id;
  aiScrollToTop(id, aiOnloadScrollTop);
}

function aiProcessAnchor(jsObject) {
  var id = jsObject.id;
  var position = parseInt(jsObject.position, 10);
  var iframeTop = jQuery("#" + id).offset().top;
  setTimeout(function () {
    jQuery("html,body").scrollTop(Math.round(iframeTop + position));
  }, 100);
}

function aiProcessHeight(jsObject) {
  var nHeight = jsObject.height;
  var nWidth = jsObject.width;
  var iAnchor = parseInt(jsObject.anchor, 10);
  var id = jsObject.id;

  if (nHeight != null) {
    try {
      var loc = jsObject.loc;
      if (loc != null && !loc.includes("send_console_log")) {
        if (typeof aiChangeUrl === "function") {
          aiChangeUrl(loc);
        }
      }
      var title = jsObject.title;
      if (title != null && title !== "undefined" && title !== "") {
        document.title = decodeURIComponent(title);
      }
      if (id != null) {
        var iHeight = parseInt(nHeight, 10);
        var iWidth = parseInt(nWidth, 10);
        aiResizeIframeHeightId(iHeight, iWidth, id);
        if (!isNaN(iAnchor) && iAnchor > -1) {
          //
          var iframeTop = jQuery("#" + id).offset().top;
          setTimeout(function () {
            jQuery("html,body").scrollTop(Math.round(iframeTop + iAnchor));
            aiShowIframeId(id);
          }, 100);
        } else {
          aiShowIframeId(id);
        }
      } else {
        alert('Please update the ai_external.js to the current version.');
      }
    } catch (e) {
      if (console && console.log) {
        console.log(e);
      }
    }
  }
}

function aiProcessShow(jsObject) {
  var id = jsObject.id;

  try {
    aiShowIframeId(id);
  } catch (e) {
    if (console && console.log) {
      console.log(e);
    }
  }
}

function aiDisableRightClick(id) {
  try {
    window.frames[id].document.oncontextmenu = function () {
      return false;
    };
  } catch (e) {
    // we ignore the error as it only works on the same domain.
  }
}

function aiRemoveElementsFromHeight(id, height, removeElements) {
  const iframejQuery = jQuery('#' + id);
  var elementArray = removeElements.split(',');
  var totalHeight = 0;
  for (var i = 0; i < elementArray.length; i++) {
    try {
      var el = elementArray[i];
      if (el.includes("|")) {
        var rangeArray = el.split('|');
        var bottomElement = jQuery(rangeArray[0]);
        var beforeBottom = Math.round(bottomElement.offset().top + bottomElement.outerHeight(true));
        var nextTop = Math.round(jQuery(rangeArray[1]).offset().top);
        totalHeight += nextTop - beforeBottom;
      } else if (el === 'top') {
        totalHeight += Math.round(iframejQuery.offset().top);
      } else if (isNaN(el)) {
        totalHeight += jQuery(el).outerHeight(true);
      } else {
        totalHeight += parseInt(el);
      }
    } catch (e) {
      if (console && console.error) {
        console.error('Advanced iframe configuration error: The configuration of remove_elements_from_height "' + removeElements
          + '" is invalid. Please check if the elements you defined do exist and ids/classes are defined properly.');
        console.log(e);
      }
    }
  }
  var calc = 'calc(' + height + ' - ' + totalHeight + 'px)';
  iframejQuery.css('height', calc);
}

function aiTriggerAutoOpen(id, selector, autoclickDelay, hideTime) {
  aiDebugExtended("aiTriggerAutoOpen");

  if (autoclickDelay === 0) {
    aiOpenIframeOnClick(id, selector);
  } else {
    setTimeout(function () {
      aiOpenIframeOnClick(id, selector);
    }, autoclickDelay);
  }

  var now = new Date();
  var time = now.getTime();
  var expireTime = time + (hideTime * 86400000);
  now.setTime(expireTime);
  var trimmedSelector = selector.replace(/[^A-Za-z0-9\-]/g, '');
  document.cookie = 'ai_disable_autoclick_iframe_' + trimmedSelector + '=Auto open is disabled until this cookie expires;expires=' + now.toUTCString() + ';path=/';
}

function aiCheckAutoOpenHash(id, autoclickDelay, hideTime) {
  if (window.location.hash) {
    var hash = window.location.hash;
    var trimmedHash = hash.replace(/[^A-Za-z0-9\-]/g, '');
    trimmedHash = "#" + trimmedHash;
    if (jQuery(trimmedHash).length !== 0) { // id does exist
      if (jQuery(trimmedHash).first().attr('target') === id) { // target is iframe
        aiTriggerAutoOpen(id, trimmedHash, autoclickDelay, hideTime);
      }
    }
  }
}

function aiOpenIframeOnClick(id, selector) {
  var myelement = jQuery(selector).first().attr("href");
  jQuery("#" + id).attr("src", myelement);
  jQuery(selector).first().click();
}

// IE11 does not support includes
if (!String.prototype.includes) {
  String.prototype.includes = function (search, start) {
    if (typeof start !== 'number') {
      start = 0;
    }
    if (start + search.length > this.length) {
      return false;
    } else {
      return this.indexOf(search, start) !== -1;
    }
  };
}

if (!String.prototype.startsWith) {
  String.prototype.startsWith = function (searchString, position) {
    position = position || 0;
    return this.indexOf(searchString, position) === position;
  };
}

// aiRealFullscreen
/* Get the documentElement (<html>) to display the page in fullscreen */
var elem = document.documentElement;

/* View in fullscreen */
function aiOpenFullscreen() {
  if (aiRealFullscreen) {
    if (elem.requestFullscreen) {
      elem.requestFullscreen();
    } else if (elem.webkitRequestFullscreen) { /* Safari */
      elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) { /* IE11 */
      elem.msRequestFullscreen();
    }
  }
}

/* Close fullscreen */
function aiCloseFullscreen() {
  if (aiRealFullscreen) {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.webkitExitFullscreen) { /* Safari */
      document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) { /* IE11 */
      document.msExitFullscreen();
    }
  }
}

document.addEventListener('fullscreenchange', aiExitHandler);
document.addEventListener('webkitfullscreenchange', aiExitHandler);
document.addEventListener('mozfullscreenchange', aiExitHandler);
document.addEventListener('MSFullscreenChange', aiExitHandler);

function aiExitHandler() {
  if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
    aiInFullscreen = false;
    jQuery('.ai-fullscreen-close').trigger("click");
  }
}




