"use strict";

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/*! js-cookie v3.0.0-rc.0 | MIT */
!function (e, t) {
  "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) && "undefined" != typeof module ? module.exports = t() : "function" == typeof define && define.amd ? define(t) : (e = e || self, function () {
    var r = e.Cookies,
        n = e.Cookies = t();

    n.noConflict = function () {
      return e.Cookies = r, n;
    };
  }());
}(void 0, function () {
  "use strict";

  function e(e) {
    for (var t = 1; t < arguments.length; t++) {
      var r = arguments[t];

      for (var n in r) {
        e[n] = r[n];
      }
    }

    return e;
  }

  var t = {
    read: function read(e) {
      return e.replace(/%3B/g, ";");
    },
    write: function write(e) {
      return e.replace(/;/g, "%3B");
    }
  };
  return function r(n, i) {
    function o(r, o, u) {
      if ("undefined" != typeof document) {
        "number" == typeof (u = e({}, i, u)).expires && (u.expires = new Date(Date.now() + 864e5 * u.expires)), u.expires && (u.expires = u.expires.toUTCString()), r = t.write(r).replace(/=/g, "%3D"), o = n.write(String(o), r);
        var c = "";

        for (var f in u) {
          u[f] && (c += "; " + f, !0 !== u[f] && (c += "=" + u[f].split(";")[0]));
        }

        return document.cookie = r + "=" + o + c;
      }
    }

    return Object.create({
      set: o,
      get: function get(e) {
        if ("undefined" != typeof document && (!arguments.length || e)) {
          for (var r = document.cookie ? document.cookie.split("; ") : [], i = {}, o = 0; o < r.length; o++) {
            var u = r[o].split("="),
                c = u.slice(1).join("="),
                f = t.read(u[0]).replace(/%3D/g, "=");
            if (i[f] = n.read(c, f), e === f) break;
          }

          return e ? i[e] : i;
        }
      },
      remove: function remove(t, r) {
        o(t, "", e({}, r, {
          expires: -1
        }));
      },
      withAttributes: function withAttributes(t) {
        return r(this.converter, e({}, this.attributes, t));
      },
      withConverter: function withConverter(t) {
        return r(e({}, this.converter, t), this.attributes);
      }
    }, {
      attributes: {
        value: Object.freeze(i)
      },
      converter: {
        value: Object.freeze(n)
      }
    });
  }(t, {
    path: "/"
  });
});
var seedprodCookies = Cookies.noConflict(); // optin form

var sp_emplacementRecaptcha = [];
var sp_option_id = "";
jQuery("form[id^=sp-optin-form]").submit(function (e) {
  e.preventDefault();
  var form_id = jQuery(this).attr("id");
  var id = form_id.replace("sp-optin-form-", "");

  if (seeprod_enable_recaptcha === 1) {
    grecaptcha.execute(sp_emplacementRecaptcha[id]);
  } else {
    var token = "";
    sp_send_request(token, id);
  }
});

var sp_CaptchaCallback = function sp_CaptchaCallback() {
  jQuery("div[id^=recaptcha-]").each(function (index, el) {
    sp_option_id = el.id.replace("recaptcha-", "");
    sp_emplacementRecaptcha[sp_option_id] = grecaptcha.render(el, {
      sitekey: "6LdfOukUAAAAAMCOEFEZ9WOSKyoYrxJcgXsf66Xr",
      badge: "bottomright",
      type: "image",
      size: "invisible",
      callback: function callback(token) {
        sp_send_request(token, sp_option_id);
      }
    });
  });
};

function sp_send_request(token, id) {
  var data = jQuery("#sp-optin-form-" + id).serialize();
  var j1 = jQuery.ajax({
    url: seedprod_api_url + "subscribers",
    type: "post",
    dataType: "json",
    timeout: 5000,
    data: data
  }); // add ajax class

  jQuery("#sp-optin-form-" + id + ' .sp-optin-submit').addClass('sp-ajax-striped sp-ajax-animated'); //var j2 = jQuery.ajax( "/" );

  var j2 = jQuery.ajax({
    url: sp_subscriber_callback_url,
    type: 'post',
    timeout: 30000,
    data: data
  });
  jQuery.when(j1, j2).done(function (a1, a2) {
    // take next action
    var action = jQuery("#sp-optin-form-" + id + " input[name^='seedprod_action']").val(); // show success message

    if (action == "1") {
      jQuery("#sp-optin-form-" + id).hide();
      jQuery("#sp-optin-success-" + id).show();
    } // redirect


    if (action === "2") {
      var redirect = jQuery("#sp-optin-form-" + id + " input[name^='redirect_url']").val();
      window.location.href = redirect;
    }

    jQuery("#sp-optin-form-" + id + ' .sp-optin-submit').removeClass('sp-ajax-striped sp-ajax-animated'); // alert( "We got what we came for!" );
  }).fail(function (jqXHR, textStatus, errorThrown) {
    jQuery("#sp-optin-form-" + id + ' .sp-optin-submit').removeClass('sp-ajax-striped sp-ajax-animated');

    if (seeprod_enable_recaptcha === 1) {
      grecaptcha.reset(sp_emplacementRecaptcha[id]);
    } // var response = JSON.parse(j1.responseText);
    // var errorString  = '';
    // jQuery.each( response.errors, function( key, value) {
    //     errorString +=  value ;
    // });
    // alert(errorString);
    // console.log(j1);
    // console.log(j2);

  });
  return;
} // countdown


var x = [];

function countdown(type, ts, id, action, redirect) {
  var now = new Date().getTime();

  if (type == 'vt') {
    ts = ts + now;
    var seedprod_enddate = seedprodCookies.get('seedprod_enddate_' + id);

    if (seedprod_enddate != undefined) {
      ts = seedprod_enddate;
    } else {
      seedprodCookies.set('seedprod_enddate_' + id, ts, {
        expires: 360
      });
    }
  } // Update the count down every 1 second


  x[id] = setInterval(function () {
    var now = new Date().getTime();
    var distance = ts - now;
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor(distance % (1000 * 60 * 60 * 24) / (1000 * 60 * 60));
    var minutes = Math.floor(distance % (1000 * 60 * 60) / (1000 * 60));
    var seconds = Math.floor(distance % (1000 * 60) / 1000);

    if (seconds == -1) {
      seconds = 0;
      minutes = 0;
      hours = 0;
      days = 0;
    }

    if (days == 0) {
      jQuery("#sp-cd-days-" + id).hide();
    } else {
      jQuery("#sp-cd-days-" + id + " .sp-cd-amount").html(pad(days, 2));
    }

    jQuery("#sp-cd-hours-" + id + " .sp-cd-amount").html(pad(hours, 2));
    jQuery("#sp-cd-minutes-" + id + " .sp-cd-amount").html(pad(minutes, 2));
    jQuery("#sp-cd-seconds-" + id + " .sp-cd-amount").html(pad(seconds, 2)); //   document.getElementById(id).innerHTML = days + "d " + pad(hours,2) + "h "
    //   + pad(minutes,2) + "m " + pad(seconds,2) + "s ";
    // If the count down is finished, write some text

    if (distance < 0) {
      clearInterval(x[id]); // show success message

      if (action == "1") {
        jQuery("#sp-countdown-" + id + " .sp-countdown-group").hide();
        jQuery("#sp-countdown-expired-" + id).show();
      } // redirect


      if (action == "2") {
        jQuery("#sp-countdown-" + id + " .sp-countdown-group").hide();
        window.location.href = redirect;
      } // restart


      if (action == "3") {
        //console.log('remove' + id);
        seedprodCookies.remove('seedprod_enddate_' + id); //location.reload();
      }
    }
  }, 1000);
}

function seedprod_animatedheadline(blockId, infiniteLoop, animationDuration, animationDelay) {
  //let animatewrapper = jQuery("#sp-animated-head-"+blockId+" .sp-title-highlight .sp-highlighted-text-wrapper");
  if (infiniteLoop == "true") {
    window.setInterval(function () {
      jQuery('#sp-animated-head-' + blockId + ' .sp-title-highlight .sp-title--headline.sp-animated').addClass('sp-hide-highlight'); //jQuery("#sp-animated-head-"+blockId+" .sp-title-highlight .sp-highlighted-text-wrapper").addClass("sp-highlighted-hide-text-wrapper");

      setTimeout(function () {
        jQuery('#sp-animated-head-' + blockId + ' .sp-title-highlight .sp-title--headline.sp-animated').removeClass('sp-hide-highlight'); //jQuery("#sp-animated-head-"+blockId+" .sp-title-highlight .sp-highlighted-text-wrapper").removeClass("sp-highlighted-hide-text-wrapper");
      }, 200);
    }, animationDelay);
  }
}

function seedprod_rotateheadline(blockId, continueLoop, animationDuration) {
  var $animatedHead = jQuery("#sp-animated-head-" + blockId + ' .preview-sp-title');
  var currentWidth = window.innerWidth;
  var view; // Determine the current view category
  // Determine the current view based on width

  if (currentWidth <= 480) {
    view = "mobile";
  } else if (currentWidth > 480 && currentWidth <= 1024) {
    view = "tablet";
  } else {
    view = "desktop";
  } // Initialize only if not already initialized


  if (!$animatedHead.data('initialized')) {
    // Save the original HTML of the block
    $animatedHead.data('original-html', $animatedHead.html());
    $animatedHead.data('last-view', view); // Save the initial view

    $animatedHead.data('initialized', true); // Run the shortcode for the first time

    $animatedHead.seedprod_responsive_title_shortcode();
  } else {
    // Check if the view has changed
    var lastView = $animatedHead.data('last-view');

    if (lastView !== view) {
      // Update the last view
      $animatedHead.data('last-view', view); // Restore the original HTML to the block

      $animatedHead.html($animatedHead.data('original-html')); // Re-run the shortcode with the original HTML

      $animatedHead.seedprod_responsive_title_shortcode();
    }
  }
}
/* end of rotate js code */


function pad(n, width, z) {
  z = z || "0";
  n = n + "";
  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
} // remove any theme css


jQuery(document).ready(function ($) {
  $('link[href*="/wp-content/themes/"]').remove();
  $(".sp-imagecarousels-wrapper").each(function () {
    var $carousel = $(this);
    var carouselId = '#' + $carousel.attr('id'); // Get the ID of the current carousel

    var slides = $(carouselId + ' .sp-imagecarousel-wrapper'); // Get all slides within this carousel

    var navDots = $(carouselId + ' .sp-imagecarousel-nav button[data-index]'); // Get navigation dots

    var slideshowmaxAttr = $carousel.attr('data-slidetoshow');
    var slideshowmax = slideshowmaxAttr ? parseInt(slideshowmaxAttr) : 1;

    if (isNaN(slideshowmax) || slideshowmax < 1) {
      slideshowmax = 1; // Default to 1 if attribute is missing or invalid
    } // Reset all slides to an inactive state first


    slides.css({
      'opacity': 0,
      'height': '0',
      'position': 'absolute'
    }); // Activate the first slide (or first set of slides if slideshowmax > 1)

    if (slides.length > 0) {
      for (var i = 0; i < slideshowmax && i < slides.length; i++) {
        slides.eq(i).css({
          'opacity': 1,
          'height': 'auto',
          'position': 'initial'
        });
      }
    } // Reset all navigation dots to an inactive state


    navDots.css({
      'opacity': 0.25
    }); // Activate the first navigation dot
    // This assumes that the first navDot corresponds to the first slide/group of slides.

    if (navDots.length > 0) {
      navDots.first().css({
        'opacity': 1
      });
    }
  });
}); // Dynamic Text

jQuery(document).ready(function ($) {
  var default_format = "{MM}/{dd}/{yyyy}";
  var html = $("body").html();
  var newTxt = html.split("[#");

  for (var i = 1; i < newTxt.length; i++) {
    var format = default_format;
    var tag = newTxt[i].split("]")[0];
    var parts = tag.split(":");

    if (parts.length > 1) {
      format = parts[1];
    } else {
      format = default_format;
    }

    var d = Date.create(parts[0]);
    var regex = "\\[#" + tag + "]";
    var re = new RegExp(regex, "g");
    $("body *").replaceText(re, d.format(format));
  }

  $(".sp-dynamic-text").contents().unwrap();
}); // Dynamic Query Parameter

jQuery(document).ready(function ($) {
  var default_value = "";
  var html = $("body").html();
  var newTxt = html.split("[q:");

  for (var i = 1; i < newTxt.length; i++) {
    var def_val = default_value;
    var tag = newTxt[i].split("]")[0];
    var parts = tag.split("=");

    if (parts.length > 1) {
      def_val = parts[1];
    } else {
      def_val = default_value;
    }

    var d = parts[0]; //Date.create(parts[0]);

    var regex = "\\[q:" + tag + "]";
    var re = new RegExp(regex, "g");
    var searchParams = new URLSearchParams(window.location.search);
    var paramdata = searchParams.get(d);

    if (paramdata != null) {
      def_val = paramdata;
    } // console.log(re);
    // console.log(def_val);
    //  console.log(d);
    //  console.log(def_val);
    //  console.log(paramdata);
    //$("body *").replaceText(re,seedprod_escapeHtml(def_val));


    var replaced = $("body").html().replace(re, seedprod_escapeHtml(def_val));
    $("body").html(replaced);
  }

  $(".sp-dynamic-text").contents().unwrap();
});

function seedprod_escapeHtml(unsafe) {
  return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
/** 
 * SeedProd Tab Block javascript
*/


function seedprod_tabbedlayout(blockId) {
  jQuery("#sp-" + blockId + ' ul.sp-tabbedlayout-wrapper li a').click(function () {
    jQuery("#sp-" + blockId + ' ul.sp-tabbedlayout-wrapper li a').removeClass('sp-active-tab');
    var sp_tab = jQuery(this).attr('data-tab');
    jQuery("#sp-" + blockId + ' ul.sp-tabbedlayout-wrapper li a.sp-tab-section-' + sp_tab).addClass('sp-active-tab');
    jQuery("#sp-" + blockId + ' div.tab-content-box').addClass('sp-hidden');
    jQuery("#sp-" + blockId + ' div.sp-tab-content-section-' + sp_tab).removeClass('sp-hidden');
  });
}
/*!-----------------------------------------------------------------------------
 * seedprod_bg_slideshow()
 * ----------------------------------------------------------------------------
 * Example:
 * seedprod_bg_slideshow('body', ['IMG_URL', 'IMG_URL', 'IMG_URL'], 3000);
 * --------------------------------------------------------------------------*/


function seedprod_bg_slideshow(selector, slides) {
  var delay = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 5000;
  var transition_timing = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'ease-in';
  var transition_duration = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : 500;
  document.querySelector(selector).style.backgroundSize = "cover";
  document.querySelector(selector).style.backgroundRepeat = "no-repeat";
  document.querySelector(selector).style.backgroundPosition = "center center"; // Set transitions

  var transition = "all " + transition_duration + 'ms ' + transition_timing;
  document.querySelector(selector).style.WebkitTransition = transition;
  document.querySelector(selector).style.MozTransition = transition;
  document.querySelector(selector).style.MsTransition = transition;
  document.querySelector(selector).style.OTransition = transition;
  document.querySelector(selector).style.transition = transition;
  var currentSlideIndex = 0; // Load first slide

  document.querySelector(selector).style.backgroundImage = "url('" + slides[currentSlideIndex] + "')";
  currentSlideIndex++; // Load next slide every interval

  setInterval(function () {
    document.querySelector(selector).style.backgroundImage = "url('" + slides[currentSlideIndex] + "')";
    currentSlideIndex++; // Reset counter

    if (currentSlideIndex >= slides.length) {
      currentSlideIndex = 0;
    }
  }, delay); // Preload slideshow images

  var preloadImages = new Array();
  slides.forEach(function (val, i) {
    preloadImages[i] = new Image();
    preloadImages[i].src = val;
  });
}

jQuery('.sp-testimonial-nav button').click(function () {
  var currentId = '#' + jQuery(this).parents('.sp-testimonials-wrapper').attr('id');
  var currentButtonIndex = jQuery(currentId + ' .sp-testimonial-nav button').index(this);
  var currentIndex = 0;
  var testimonials = jQuery('.sp-testimonial-wrapper', jQuery(this).parents(currentId));
  var slideshowmax = jQuery(this).parents('.sp-testimonials-wrapper').attr('data-slidetoshow');

  if (slideshowmax == undefined) {
    slideshowmax = 1;
  }
  /*
  jQuery(testimonials).each(function (index) {
  	var o = jQuery(this).css('opacity');
  	if (o == 1) {
  		currentIndex = index;
  	}
  })
  */


  var slider_length = Math.ceil(testimonials.length / parseInt(slideshowmax));

  for (var customindexdata = 0; customindexdata < slider_length; customindexdata++) {
    var opa = jQuery(currentId + ' .sp-testimonial-nav button[data-index="' + customindexdata + '"]').css('opacity');

    if (opa >= 0.5) {
      //console.log("customindexdata is =" + customindexdata);
      currentIndex = customindexdata;
    }
  }

  var buttonsLength = jQuery(currentId + ' .sp-testimonial-nav button').length - 1;
  var currentButtonIndexData = jQuery(currentId + ' .sp-testimonial-nav button').eq(currentButtonIndex).attr('data-index'); // check for previous button click

  if (currentButtonIndex == 0) {
    if (0 == currentIndex) {
      currentIndex = Math.ceil(testimonials.length / parseInt(slideshowmax)) - 1;
    } else {
      currentIndex--;
    }
  } // check for next button click


  if (currentButtonIndex == buttonsLength) {
    if (Math.ceil(testimonials.length / parseInt(slideshowmax)) - 1 == currentIndex) {
      currentIndex = 0;
    } else {
      currentIndex++;
    }
  } // reset states


  testimonials.css({
    'opacity': 0,
    'height': '0',
    'position': 'absolute'
  });
  jQuery(currentId + ' .sp-testimonial-nav button[data-index]').css({
    'opacity': 0.25
  });
  var startindex = parseInt(currentIndex * parseInt(slideshowmax));
  var endindex = parseInt(startindex + parseInt(slideshowmax)); // select testimonial and button

  if (currentButtonIndexData !== undefined) {
    currentIndex = currentButtonIndexData;
    startindex = parseInt(currentIndex * parseInt(slideshowmax));
    endindex = parseInt(startindex + parseInt(slideshowmax));

    for (var i = startindex; i < endindex; i++) {
      jQuery(testimonials).eq(i).css({
        'opacity': 1,
        'height': 'auto',
        'position': 'initial'
      });
    } //jQuery(testimonials).eq(currentIndex).css({ 'opacity': 1, 'height': 'auto', 'position': 'initial' });


    jQuery(currentId + ' .sp-testimonial-nav button').eq(currentButtonIndex).css({
      'opacity': 1
    });
  } else {
    startindex = parseInt(currentIndex * parseInt(slideshowmax));
    endindex = parseInt(startindex + parseInt(slideshowmax));

    for (var _i = startindex; _i < endindex; _i++) {
      jQuery(testimonials).eq(_i).css({
        'opacity': 1,
        'height': 'auto',
        'position': 'initial'
      }); //jQuery(currentId + ' .sp-imagecarousel-nav button').eq(currentButtonIndex).css({ 'opacity': 1 })	
    } //jQuery(testimonials).eq(currentIndex).css({ 'opacity': 1, 'height': 'auto', 'position': 'initial' });


    jQuery(currentId + ' .sp-testimonial-nav button').eq(currentIndex + 1).css({
      'opacity': 1
    });
  }
});
var testimonial_timers = {};
jQuery(".sp-testimonials-wrapper").each(function () {
  var currentId = '#' + jQuery(this).attr('id');
  var autoPlay = jQuery(this).attr('data-autoplay');
  var speed = jQuery(this).attr('data-speed');

  if (speed === '') {
    speed = 5000;
  } else {
    speed = parseInt(speed) * 1000;
  }

  if (autoPlay !== undefined) {
    testimonial_timers[currentId] = setInterval(function () {
      var clickEvent = jQuery.Event('click');
      clickEvent.preventDefault();
      jQuery(currentId + ' .sp-testimonial-nav button:last-child').triggerHandler(clickEvent);
    }, speed);
  }
});
jQuery(".sp-testimonials-wrapper").hover(function () {
  var id = '#' + jQuery(this).attr('id');
  clearInterval(testimonial_timers[id]);
});
jQuery(".sp-testimonials-wrapper").mouseleave(function () {
  var currentId = '#' + jQuery(this).attr('id');
  var autoPlay = jQuery(this).attr('data-autoplay');
  var speed = jQuery(this).attr('data-speed');

  if (speed === '') {
    speed = 5000;
  } else {
    speed = parseInt(speed) * 1000;
  }

  if (autoPlay !== undefined) {
    testimonial_timers[currentId] = setInterval(function () {
      var clickEvent = jQuery.Event('click');
      clickEvent.preventDefault();
      jQuery(currentId + ' .sp-testimonial-nav button:last-child').triggerHandler(clickEvent);
    }, speed);
  }
});
/* start  of twitter timline js code */

function seedprod_twitterembedtimeline(blockId, timelineid, showReplies, width, height, chrome, align, borderColors, colorScheme, lang) {
  //jQuery("#sp-animated-head-"+blockId+' .preview-sp-title' ).seedprod_responsive_title_shortcode();
  twttr.ready(function (twttr) {
    window.twttr.widgets.createTimeline({
      sourceType: "profile",
      screenName: timelineid
    }, document.getElementById('sp-twitterembedtimeline-preview-' + blockId), {
      showReplies: showReplies,
      width: width,
      height: height,
      chrome: chrome,
      align: align,
      borderColor: borderColors,
      theme: colorScheme,
      lang: lang
    }).then(function (el) {//console.log('Tweet added.'); 
    });
  });
}

function seedprod_twittertweetbutton(blockId, tweetUrl, buttonSize, tweetText, tweetHashTag, viaHandle, relatedTweet, lang) {
  twttr.ready(function (twttr) {
    window.twttr.widgets.createShareButton(tweetUrl, document.getElementById('sp-twittertweetbutton-preview-' + blockId), {
      size: buttonSize,
      text: tweetText,
      hashtags: tweetHashTag,
      via: viaHandle,
      related: relatedTweet,
      lang: lang
    });
  });
}
/* end of twitter timline js code */

/* this is image carousel block code */


jQuery('.sp-imagecarousel-nav button').click(function () {
  var currentId = '#' + jQuery(this).parents('.sp-imagecarousels-wrapper').attr('id');
  var currentButtonIndex = jQuery(currentId + ' .sp-imagecarousel-nav button').index(this);
  var currentIndex = 0;
  var currentIndexOfNav = 0;
  var imagecarousels = jQuery('.sp-imagecarousel-wrapper', jQuery(this).parents(currentId));
  var slideshowmax = jQuery(this).parents('.sp-imagecarousels-wrapper').attr('data-slidetoshow');

  if (slideshowmax == undefined) {
    slideshowmax = 1;
  } //console.log("new slidershow value = " + slideshowmax);

  /*
  jQuery(imagecarousels).each(function (index) {
  	var o = jQuery(this).css('opacity');
  	if (o == 1) {
  		currentIndex = index;
  	}
  })
  */


  var slider_length = Math.ceil(imagecarousels.length / parseInt(slideshowmax));

  for (var customindexdata = 0; customindexdata < slider_length; customindexdata++) {
    var opa = jQuery(currentId + ' .sp-imagecarousel-nav button[data-index="' + customindexdata + '"]').css('opacity');

    if (opa >= 0.5) {
      //console.log("customindexdata is =" + customindexdata);
      currentIndex = customindexdata;
    }
  }

  var buttonsLength = jQuery(currentId + ' .sp-imagecarousel-nav button').length - 1;
  var currentButtonIndexData = jQuery(currentId + ' .sp-imagecarousel-nav button').eq(currentButtonIndex).attr('data-index'); // check for previous button click

  if (currentButtonIndex == 0) {
    if (0 == currentIndex) {
      currentIndex = Math.ceil(imagecarousels.length / parseInt(slideshowmax)) - 1;
    } else {
      currentIndex--;
    }
  } // check for next button click


  if (currentButtonIndex == buttonsLength) {
    if (Math.ceil(imagecarousels.length / parseInt(slideshowmax)) - 1 == currentIndex) {
      currentIndex = 0;
    } else {
      currentIndex++;
    }
  }

  var startindex = parseInt(currentIndex * parseInt(slideshowmax));
  var endindex = parseInt(startindex + parseInt(slideshowmax)); // reset states

  imagecarousels.css({
    'opacity': 0,
    'height': '0',
    'position': 'absolute'
  });
  jQuery(currentId + ' .sp-imagecarousel-nav button[data-index]').css({
    'opacity': 0.25
  }); // select imagecarousel and button

  if (currentButtonIndexData !== undefined) {
    currentIndex = currentButtonIndexData;
    startindex = parseInt(currentIndex * parseInt(slideshowmax));
    endindex = parseInt(startindex + parseInt(slideshowmax));

    for (var i = startindex; i < endindex; i++) {
      jQuery(imagecarousels).eq(i).css({
        'opacity': 1,
        'height': 'auto',
        'position': 'initial'
      });
    } //jQuery(imagecarousels).eq(currentIndex).css({ 'opacity': 1, 'height': 'auto', 'position': 'initial' });


    jQuery(currentId + ' .sp-imagecarousel-nav button').eq(currentButtonIndex).css({
      'opacity': 1
    });
  } else {
    startindex = parseInt(currentIndex * parseInt(slideshowmax));
    endindex = parseInt(startindex + parseInt(slideshowmax));

    for (var _i2 = startindex; _i2 < endindex; _i2++) {
      jQuery(imagecarousels).eq(_i2).css({
        'opacity': 1,
        'height': 'auto',
        'position': 'initial'
      }); //jQuery(currentId + ' .sp-imagecarousel-nav button').eq(currentButtonIndex).css({ 'opacity': 1 })	
    } //jQuery(imagecarousels).eq(currentIndex).css({ 'opacity': 1, 'height': 'auto', 'position': 'initial' });


    jQuery(currentId + ' .sp-imagecarousel-nav button').eq(currentIndex + 1).css({
      'opacity': 1
    });
  }
});
var imagecarousel_timers = {};
jQuery(".sp-imagecarousels-wrapper").each(function (index) {
  var currentId = '#' + jQuery(this).attr('id');
  var autoPlay = jQuery(this).attr('data-autoplay');
  var speed = jQuery(this).attr('data-speed');

  if (speed === '') {
    speed = 5000;
  } else {
    speed = parseInt(speed) * 1000;
  }

  if (autoPlay !== undefined) {
    imagecarousel_timers[currentId] = setInterval(function () {
      var clickEvent = jQuery.Event('click');
      clickEvent.preventDefault();
      jQuery(currentId + ' .sp-imagecarousel-nav button:last-child').triggerHandler(clickEvent);
    }, speed);
  }
});
jQuery(".sp-imagecarousels-wrapper").hover(function () {
  var id = '#' + jQuery(this).attr('id');
  clearInterval(imagecarousel_timers[id]);
});
jQuery(".sp-imagecarousels-wrapper").mouseleave(function () {
  var currentId = '#' + jQuery(this).attr('id');
  var autoPlay = jQuery(this).attr('data-autoplay');
  var speed = jQuery(this).attr('data-speed');

  if (speed === '') {
    speed = 5000;
  } else {
    speed = parseInt(speed) * 1000;
  }

  if (autoPlay !== undefined) {
    imagecarousel_timers[currentId] = setInterval(function () {
      var clickEvent = jQuery.Event('click');
      clickEvent.preventDefault();
      jQuery(currentId + ' .sp-imagecarousel-nav button:last-child').triggerHandler(clickEvent);
    }, speed);
  }
});

function PureDropdown(dropdownParent) {
  var PREFIX = 'seedprod-',
      ACTIVE_CLASS_NAME = PREFIX + 'menu-active',
      ARIA_ROLE = 'role',
      ARIA_HIDDEN = 'aria-hidden',
      MENU_OPEN = 0,
      MENU_CLOSED = 1,
      MENU_ACTIVE_SELECTOR = '.menu-item-active',
      MENU_LINK_SELECTOR = '.menu-item a',
      MENU_SELECTOR = '.sub-menu',
      DISMISS_EVENT = window.hasOwnProperty && window.hasOwnProperty('ontouchstart') ? 'touchstart' : 'mousedown',
      ARROW_KEYS_ENABLED = true,
      ddm = this; // drop down menu

  this._state = MENU_CLOSED;

  this.show = function () {
    if (this._state !== MENU_OPEN) {
      this._dropdownParent.classList.add(ACTIVE_CLASS_NAME);

      this._menu.setAttribute(ARIA_HIDDEN, false);

      this._state = MENU_OPEN;
    }
  };

  this.hide = function () {
    if (this._state !== MENU_CLOSED) {
      this._dropdownParent.classList.remove(ACTIVE_CLASS_NAME);

      this._menu.setAttribute(ARIA_HIDDEN, true);

      this._link.focus();

      this._state = MENU_CLOSED;
    }
  };

  this.toggle = function () {
    this[this._state === MENU_CLOSED ? 'show' : 'hide']();
  };

  this.halt = function (e) {
    e.stopPropagation();
    e.preventDefault();
  };

  this._dropdownParent = dropdownParent;
  this._link = this._dropdownParent.querySelector(MENU_LINK_SELECTOR);
  this._menu = this._dropdownParent.querySelector(MENU_SELECTOR);
  this._firstMenuLink = this._menu.querySelector(MENU_LINK_SELECTOR); // Set ARIA attributes

  this._link.setAttribute('aria-haspopup', 'true');

  this._menu.setAttribute(ARIA_ROLE, 'menu');

  this._menu.setAttribute('aria-labelledby', this._link.getAttribute('id'));

  this._menu.setAttribute('aria-hidden', 'true');

  [].forEach.call(this._menu.querySelectorAll('li'), function (el) {
    el.setAttribute(ARIA_ROLE, 'presentation');
  });
  [].forEach.call(this._menu.querySelectorAll('a'), function (el) {
    el.setAttribute(ARIA_ROLE, 'menuitem');
  }); // Toggle on click

  this._link.addEventListener('click', function (e) {
    // e.stopPropagation();
    // e.preventDefault();
    // ddm.toggle();
    if (ddm._state !== MENU_OPEN) {
      e.stopPropagation();
      e.preventDefault();
      ddm.show();
    }
  }); // Toggle on hover


  this._link.addEventListener('mouseover', function (e) {
    e.stopPropagation();
    e.preventDefault();
    var isDesktop = window.matchMedia('only screen and (min-width: 768px)').matches;

    if (isDesktop) {
      // Only run this for desktop only. Submenu is shown on hover using CSS but we have no way to track it in JS. This does that.
      ddm.toggle();
    }
  }); // Close menu when hovered out of - Desktop.


  this._link.addEventListener('mouseout', function (e) {
    e.stopPropagation();
    e.preventDefault();
    var isDesktop = window.matchMedia('only screen and (min-width: 768px)').matches;

    if (isDesktop) {
      ddm.hide();

      ddm._link.blur();
    }
  }); // Keyboard navigation


  document.addEventListener('keydown', function (e) {
    var currentLink, previousSibling, nextSibling, previousLink, nextLink; // if the menu isn't active, ignore

    if (ddm._state !== MENU_OPEN) {
      return;
    } // if the menu is the parent of an open, active submenu, ignore


    if (ddm._menu.querySelector(MENU_ACTIVE_SELECTOR)) {
      return;
    }

    currentLink = ddm._menu.querySelector(':focus'); // Dismiss an open menu on ESC

    if (e.keyCode === 27) {
      /* Esc */
      ddm.halt(e);
      ddm.hide();
    } // Go to the next link on down arrow
    else if (ARROW_KEYS_ENABLED && e.keyCode === 40) {
        /* Down arrow */
        ddm.halt(e); // get the nextSibling (an LI) of the current link's LI

        nextSibling = currentLink ? currentLink.parentNode.nextSibling : null; // if the nextSibling is a text node (not an element), go to the next one

        while (nextSibling && nextSibling.nodeType !== 1) {
          nextSibling = nextSibling.nextSibling;
        }

        nextLink = nextSibling ? nextSibling.querySelector('.menu-item a') : null; // if there is no currently focused link, focus the first one

        if (!currentLink) {
          ddm._menu.querySelector('.menu-item a').focus();
        } else if (nextLink) {
          nextLink.focus();
        }
      } // Go to the previous link on up arrow
      else if (ARROW_KEYS_ENABLED && e.keyCode === 38) {
          /* Up arrow */
          ddm.halt(e); // get the currently focused link

          previousSibling = currentLink ? currentLink.parentNode.previousSibling : null;

          while (previousSibling && previousSibling.nodeType !== 1) {
            previousSibling = previousSibling.previousSibling;
          }

          previousLink = previousSibling ? previousSibling.querySelector('.menu-item a') : null; // if there is no currently focused link, focus the last link

          if (!currentLink) {
            ddm._menu.querySelector('.menu-item:last-child .menu-item a').focus();
          } // else if there is a previous item, go to the previous item
          else if (previousLink) {
              previousLink.focus();
            }
        }
  }); // Dismiss an open menu on outside event

  document.addEventListener(DISMISS_EVENT, function (e) {
    var target = e.target;

    if (target !== ddm._link && !ddm._menu.contains(target)) {
      ddm.hide();

      ddm._link.blur();
    }
  });
}

function initDropdowns() {
  var dropdownParents = document.querySelectorAll('.menu-item-has-children');

  for (var i = 0; i < dropdownParents.length; i++) {
    var ddm = new PureDropdown(dropdownParents[i]);
  }
}

jQuery('.hamburger').click(function () {
  jQuery(this).toggleClass("active");
  jQuery(this).next('.nav-menu').toggleClass("active");
});

function seedprod_add_basic_lightbox(blockId) {
  jQuery("#sp-" + blockId + " a").click(function () {
    return false;
  });
  var imgbasicpreview = new ImgPreviewer('#sp-' + blockId, {
    scrollbar: true,
    dataUrlKey: 'href'
  });
  imgbasicpreview.update();
}

function seedprod_add_gallery_lightbox(blockId) {
  jQuery("#sp-" + blockId + " a.sp-gallery-items").click(function () {
    return false;
  });
  var imgpreview = new ImgPreviewer('#sp-' + blockId + " .sp-gallery-block", {
    scrollbar: true,
    dataUrlKey: 'href'
  });
  imgpreview.update();
  jQuery("#sp-" + blockId + " .sp-gallery-tabs a.sp-gallery-tab-title").click(function () {
    var activeTabIndex = jQuery(this).attr("data-gallery-index");
    jQuery('#sp-' + blockId + ' .sp-gallery-tab-title').removeClass('sp-tab-active');
    jQuery(this).addClass('sp-tab-active');
    jQuery.each(jQuery('#sp-' + blockId + ' .sp-gallery-items'), function (i, v) {
      jQuery(this).removeClass('sp-hidden-items');
      jQuery(this).removeClass('zoom-in'); // Hide images that do not match the active tab index.

      if (activeTabIndex !== 'all') {
        var currentTabIndex = jQuery(v).data('tags');
        var cleanCurrentTabIndex = currentTabIndex.split(',');

        if (!cleanCurrentTabIndex.includes(activeTabIndex)) {
          jQuery(this).addClass('sp-hidden-items');
        }
      }
    });
    imgpreview.update();
  });
}

function seedprod_add_gallery_js(blockId) {
  jQuery("#sp-" + blockId + " .sp-gallery-tabs a.sp-gallery-tab-title").click(function () {
    var activeTabIndex = jQuery(this).attr("data-gallery-index");
    jQuery('#sp-' + blockId + ' .sp-gallery-tab-title').removeClass('sp-tab-active');
    jQuery(this).addClass('sp-tab-active');
    jQuery.each(jQuery('#sp-' + blockId + ' .sp-gallery-items'), function (i, v) {
      jQuery(this).removeClass('sp-hidden-items');
      jQuery(this).removeClass('zoom-in'); // Hide images that do not match the active tab index.

      if (activeTabIndex !== 'all') {
        var currentTabIndex = jQuery(v).data('tags');
        var cleanCurrentTabIndex = currentTabIndex.split(',');

        if (!cleanCurrentTabIndex.includes(activeTabIndex)) {
          jQuery(this).addClass('sp-hidden-items');
        }
      }
    });
  });
}

jQuery.fn.isInViewport = function () {
  var elementTop = jQuery(this).offset().top;
  var elementBottom = elementTop + jQuery(this).outerHeight();
  var viewportTop = jQuery(window).scrollTop();
  var viewportBottom = viewportTop + jQuery(window).height();
  return elementBottom > viewportTop && elementTop < viewportBottom;
}; // Check if an element is in the viewport with a threshold percentage.


jQuery.fn.isInViewportWithThreshold = function () {
  var threshold = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 50;
  var elementTop = this.offset().top;
  var elementBottom = elementTop + this.outerHeight();
  var viewportTop = jQuery(window).scrollTop();
  var viewportBottom = viewportTop + jQuery(window).height(); // Only consider it "in viewport" if a threshold percentage is visible

  var elementVisible = Math.min(elementBottom, viewportBottom) - Math.max(elementTop, viewportTop);
  return elementVisible / this.outerHeight() * 100 >= threshold;
}; // Trigger counter block.


function counter(blockId) {
  var duration = jQuery("#sp-counter-".concat(blockId, " .sp-counter-text-wrapper .sp-counter-number")).attr('data-duration');
  var startNumber = jQuery("#sp-counter-".concat(blockId, " .sp-counter-text-wrapper .sp-counter-number")).attr('data-start-number');
  var endNumber = jQuery("#sp-counter-".concat(blockId, " .sp-counter-text-wrapper .sp-counter-number")).attr('data-end-number');
  var thousandsSeparator = jQuery("#sp-counter-".concat(blockId, " .sp-counter-text-wrapper .sp-counter-number")).attr('data-thousands-separator');
  var separator = jQuery("#sp-counter-".concat(blockId, " .sp-counter-text-wrapper .sp-counter-number")).attr('data-separator');
  var options = {};
  var delimeter = {
    'default': ',',
    'space': ' ',
    'dot': '.'
  };
  options.duration = duration;
  options.delimiter = thousandsSeparator ? delimeter[separator] : '';
  options.toValue = endNumber;
  jQuery("#sp-counter-number-".concat(blockId)).html(startNumber);
  jQuery("#sp-counter-number-".concat(blockId)).numerator(options);
} // Render dynamic tags.


function image_dynamic_tags(blockId) {
  // Replace image src if source is dynamic tags
  jQuery(".sp-image-block-".concat(blockId)).each(function () {
    var _this = this;

    // If data-image-src === dynamictags
    var imageSrc = jQuery(this).attr('data-image-src');

    if ('dynamictags' === imageSrc) {
      var imageLink = jQuery(this).attr('data-dynamic-tag');

      if (imageLink) {
        // Pre-load the image
        var img = new Image();

        img.onload = function () {
          // Set new src when the image has loaded
          jQuery(_this).attr('src', imageLink);
        };

        img.src = imageLink;
      }
    }
  });
}

function postcomments(blockId) {
  // Get comment policy content.
  var commentPolicyElement = "#sp-".concat(blockId, " .sp-postcomments-content-policy-").concat(blockId);
  var commentPolicyContent = jQuery(commentPolicyElement).html(); // Create comment policy element, add class & append content.

  var commentPolicyHtml = jQuery('<p class="commentpolicy"></p>').html(commentPolicyContent); // Find commentform on current block & append comment policy

  var currentBlock = "#sp-".concat(blockId, " #commentform");
  var currentBlockHtml = document.querySelector(currentBlock);

  if (currentBlockHtml) {
    jQuery(currentBlock).prepend(commentPolicyHtml);
  }
}

function beforeafterslider(blockId, options) {
  /*
  let options1 = {
  	default_offset_pct: 0.5,
  	orientation: "horizontal",
  	before_label: "Before",
  	after_label: "After",
  	no_overlay: false,//self.block.settings.overlayColor,
  	move_slider_on_hover: true,
  	move_with_handle_only: true,
  	click_to_move: true
  };
  */
  jQuery("#sp-toggle-".concat(blockId, " .twentytwenty-container")).twentytwenty(options);
}

function hotspotTooltips(blockId, items) {
  var trigger = jQuery("#sp-".concat(blockId, " .sp-hotspot-image")).attr('data-tooltip-trigger');
  var animation = jQuery("#sp-".concat(blockId, " .sp-hotspot-image")).attr('data-tooltip-animation');
  var duration = jQuery("#sp-".concat(blockId, " .sp-hotspot-image")).attr('data-tooltip-duration');
  var position = jQuery("#sp-".concat(blockId, " .sp-hotspot-image")).attr('data-tooltip-position');
  var showArrow = jQuery("#sp-".concat(blockId, " .sp-hotspot-image")).attr('data-tooltip-show-arrow');
  var maxWidth = jQuery("#sp-".concat(blockId, " .sp-hotspot-image")).attr('data-tooltip-max-width');
  items = JSON.parse(items);
  items.map(function (item, index) {
    var $myElement = "#sp-".concat(blockId, " #hotspot-").concat(blockId, "-").concat(index);
    jQuery($myElement).tooltipster({
      animation: animation,
      delay: duration,
      trigger: trigger,
      side: position,
      arrow: 'true' === showArrow ? true : false,
      maxWidth: maxWidth,
      content: item.tooltipContent,
      contentCloning: true,
      contentAsHTML: true
    });
  });
}

function seedprod_add_content_toggle_js(blockId) {
  if (jQuery(this).is(":checked") == false) {
    jQuery("#sp-contenttoggle-" + blockId + " .sp-toggle-sections .sp-toggle-sections1").removeClass("sp-hidden");
    jQuery("#sp-contenttoggle-" + blockId + " .sp-toggle-sections .sp-toggle-sections2").addClass("sp-hidden");
  } else {
    jQuery("#sp-contenttoggle-" + blockId + " .sp-toggle-sections .sp-toggle-sections1").addClass("sp-hidden");
    jQuery("#sp-contenttoggle-" + blockId + " .sp-toggle-sections .sp-toggle-sections2").removeClass("sp-hidden");
  }

  jQuery("#sp-contenttoggle-" + blockId + " .sp-content-toggle-area .sp-toggle-switch").change(function () {
    if (jQuery(this).is(":checked") == false) {
      jQuery("#sp-contenttoggle-" + blockId + " .sp-toggle-sections .sp-toggle-sections1").removeClass("sp-hidden");
      jQuery("#sp-contenttoggle-" + blockId + " .sp-toggle-sections .sp-toggle-sections2").addClass("sp-hidden");
    } else {
      jQuery("#sp-contenttoggle-" + blockId + " .sp-toggle-sections .sp-toggle-sections1").addClass("sp-hidden");
      jQuery("#sp-contenttoggle-" + blockId + " .sp-toggle-sections .sp-toggle-sections2").removeClass("sp-hidden");
    }
  });
}

jQuery('.sp-type-alert button.sp-alert-close').click(function () {
  jQuery(this).parents('.sp-type-alert').hide();
});
/**
 * businessreview javascript
 */

jQuery('.sp-businessreview-nav button').click(function () {
  var currentId = '#' + jQuery(this).parents('.sp-businessreview-wrapper').attr('id');
  var currentButtonIndex = jQuery(currentId + ' .sp-businessreview-nav button').index(this);
  var currentIndex = 0;
  var businessreviews = jQuery('.seedprod-business-review-wrapper', jQuery(this).parents(currentId));
  var slideshowmax = jQuery(this).parents('.sp-businessreview-wrapper').attr('data-slidetoshow');

  if (slideshowmax == undefined) {
    slideshowmax = 1;
  }

  var slider_length = Math.ceil(businessreviews.length / parseInt(slideshowmax));

  for (var customindexdata = 0; customindexdata < slider_length; customindexdata++) {
    var opa = jQuery(currentId + ' .sp-businessreview-nav button[data-index="' + customindexdata + '"]').css('opacity');

    if (opa >= 0.5) {
      //console.log("customindexdata is =" + customindexdata);
      currentIndex = customindexdata;
    }
  }

  var buttonsLength = jQuery(currentId + ' .sp-businessreview-nav button').length - 1;
  var currentButtonIndexData = jQuery(currentId + ' .sp-businessreview-nav button').eq(currentButtonIndex).attr('data-index'); // check for previous button click

  if (currentButtonIndex == 0) {
    if (0 == currentIndex) {
      currentIndex = Math.ceil(businessreviews.length / parseInt(slideshowmax)) - 1;
    } else {
      currentIndex--;
    }
  } // check for next button click


  if (currentButtonIndex == buttonsLength) {
    if (Math.ceil(businessreviews.length / parseInt(slideshowmax)) - 1 == currentIndex) {
      currentIndex = 0;
    } else {
      currentIndex++;
    }
  } // reset states


  businessreviews.css({
    'z-index': 999,
    'opacity': 0,
    'height': '0',
    'position': 'absolute'
  });
  jQuery(currentId + ' .sp-businessreview-nav button[data-index]').css({
    'opacity': 0.25
  });
  var startindex = parseInt(currentIndex * parseInt(slideshowmax));
  var endindex = parseInt(startindex + parseInt(slideshowmax)); // select businessreviews and button

  if (currentButtonIndexData !== undefined) {
    currentIndex = currentButtonIndexData;
    startindex = parseInt(currentIndex * parseInt(slideshowmax));
    endindex = parseInt(startindex + parseInt(slideshowmax));

    for (var i = startindex; i < endindex; i++) {
      jQuery(businessreviews).eq(i).css({
        'opacity': 1,
        'height': 'auto',
        'position': 'initial'
      });
    } //jQuery(businessreviews).eq(currentIndex).css({ 'opacity': 1, 'height': 'auto', 'position': 'initial' });


    jQuery(currentId + ' .sp-businessreview-nav button').eq(currentButtonIndex).css({
      'opacity': 1
    });
  } else {
    startindex = parseInt(currentIndex * parseInt(slideshowmax));
    endindex = parseInt(startindex + parseInt(slideshowmax));

    for (var _i3 = startindex; _i3 < endindex; _i3++) {
      jQuery(businessreviews).eq(_i3).css({
        'opacity': 1,
        'height': 'auto',
        'position': 'initial'
      }); //jQuery(currentId + ' .sp-imagecarousel-nav button').eq(currentButtonIndex).css({ 'opacity': 1 })	
    } //jQuery(businessreviews).eq(currentIndex).css({ 'opacity': 1, 'height': 'auto', 'position': 'initial' });


    jQuery(currentId + ' .sp-businessreview-nav button').eq(currentIndex + 1).css({
      'opacity': 1
    });
  }
});
var businessreview_timers = {};
jQuery(".sp-businessreview-wrapper").each(function (index) {
  var currentId = '#' + jQuery(this).attr('id');
  var autoPlay = jQuery(this).attr('data-autoplay');
  var speed = jQuery(this).attr('data-speed');

  if (speed === '') {
    speed = 5000;
  } else {
    speed = parseInt(speed) * 1000;
  }

  if (autoPlay !== undefined) {
    businessreview_timers[currentId] = setInterval(function () {
      var clickEvent = jQuery.Event('click');
      clickEvent.preventDefault();
      jQuery(currentId + ' .sp-businessreview-nav button:last-child').triggerHandler(clickEvent);
    }, speed);
  }
});
jQuery(".sp-businessreview-wrapper").hover(function () {
  var id = '#' + jQuery(this).attr('id');
  clearInterval(businessreview_timers[id]);
});
jQuery(".sp-businessreview-wrapper").mouseleave(function () {
  var currentId = '#' + jQuery(this).attr('id');
  var autoPlay = jQuery(this).attr('data-autoplay');
  var speed = jQuery(this).attr('data-speed');

  if (speed === '') {
    speed = 5000;
  } else {
    speed = parseInt(speed) * 1000;
  }

  if (autoPlay !== undefined) {
    businessreview_timers[currentId] = setInterval(function () {
      var clickEvent = jQuery.Event('click');
      clickEvent.preventDefault();
      jQuery(currentId + ' .sp-businessreview-nav button:last-child').triggerHandler(clickEvent);
    }, speed);
  }
});

function seedprod_particlessectionjs(blockId, particlesconfig) {
  var particlesJSON = particlesconfig;
  particlesJS("tsparticles-preview-sp-" + blockId, particlesJSON);
}

function seedprod_pro_video_pop_up_trigger_video(blockId, videoHtml, blockOptions) {
  var options = JSON.parse(blockOptions);
  var responsiveClass = options.source === 'custom' ? 'sp-video-responsive-video' : 'sp-video-responsive';
  var videoWrapper = jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId));
  var videoResponsive = jQuery("#sp-".concat(blockId, " #sp-video-responsive-").concat(blockId));
  var banner = jQuery("#sp-".concat(blockId, " #sp-video-pop-up-banner-").concat(blockId)); // Only enable if image overlay is enabled.

  if (options.enable_image_overlay) {
    if (options.enable_lightbox) {
      // Open lightbox modal onclick
      jQuery("#sp-".concat(blockId, " .sp-video-pop-up-image-overlay-container")).click(function () {
        // Set modal content html
        jQuery("#sp-".concat(blockId, " #video-pop-up-lightbox-modal-").concat(blockId, " .modal-content")).html("<div id=\"sp-video-responsive-".concat(blockId, "\" class=\"").concat(responsiveClass, " sp-video-pop-up-video\">").concat(videoHtml, "</div>"));
        jQuery("#sp-".concat(blockId, " #video-pop-up-lightbox-modal-").concat(blockId)).css('display', 'block');
      }); // Close lightbox

      jQuery("#sp-".concat(blockId, " #video-pop-up-lightbox-modal-").concat(blockId, " span.close")).click(function () {
        jQuery("#sp-".concat(blockId, " #video-pop-up-lightbox-modal-").concat(blockId)).css('display', 'none');
      });
    } else {
      // When image overlay is clicked, display video.
      jQuery("#sp-".concat(blockId, " .sp-video-pop-up-image-overlay-container")).click(function () {
        jQuery("#sp-".concat(blockId, " .sp-video-pop-up-image-overlay-container")).remove(); // Create video element.

        jQuery("#sp-".concat(blockId, " .sp-video-wrapper")).append("<div id=\"sp-video-responsive-".concat(blockId, "\" class=\"").concat(responsiveClass, " sp-video-pop-up-video\">").concat(videoHtml, "</div>"));
        jQuery("#sp-".concat(blockId, " #sp-video-responsive-").concat(blockId)).css('aspect-ratio', options.aspect_ratio);
      });
    }
  }

  if (options.enable_sticky_video && !options.enable_lightbox) {
    // On scroll/resize
    jQuery(window).on('resize scroll', throttle(function () {
      // Disable for mobile.
      if (window.matchMedia('only screen and (min-width: 960px)').matches) {
        // Check if video is in viewport
        if (jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).isInViewportWithThreshold()) {
          // Return original class
          videoWrapper.removeClass('sp-video-wrapper-sticky').addClass('sp-video-wrapper');
          videoResponsive.removeClass('sp-video-responsive-sticky').addClass(responsiveClass);

          if (options.enable_banner) {
            // Check if video is custom
            if (options.source === 'custom') {
              banner.removeClass('sp-video-pop-up-banner-custom-sticky').addClass('sp-video-pop-up-banner');
            } else if (options.source === 'vimeo') {
              banner.removeClass('sp-video-pop-up-banner-vimeo-sticky').addClass('sp-video-pop-up-banner');
            } else {
              banner.removeClass('sp-video-pop-up-banner-sticky').addClass('sp-video-pop-up-banner');
            }
          } // Remove isolation & reset z-index
          // Section


          if (jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-section').length > 0) {
            if (seedprod_pro_check_for_entrance_animation(jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-section'))) {
              videoWrapper.parents('.sp-el-section').css('isolation', 'initial');
              videoWrapper.parents('.sp-el-section').css('z-index', 'auto');
            }
          } // Col


          if (jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-col').length > 0) {
            if (seedprod_pro_check_for_entrance_animation(jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-col'))) {
              jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-col').css('isolation', 'initial');
              jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-col').css('z-index', 'auto');
            }
          } // Row


          if (jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-row').length > 0) {
            if (seedprod_pro_check_for_entrance_animation(jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-row'))) {
              jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-row').css('isolation', 'initial');
              jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-row').css('z-index', 'auto');
            }
          }
        } else {
          // Add sticky class
          videoWrapper.removeClass('sp-video-wrapper').addClass('sp-video-wrapper-sticky');
          videoResponsive.removeClass(responsiveClass).addClass('sp-video-responsive-sticky');

          if (options.enable_banner) {
            // Check if video is custom or vimeo
            if (options.source === 'custom') {
              banner.removeClass('sp-video-pop-up-banner').addClass('sp-video-pop-up-banner-custom-sticky');
            } else if (options.source === 'vimeo') {
              banner.removeClass('sp-video-pop-up-banner').addClass('sp-video-pop-up-banner-vimeo-sticky');
            } else {
              banner.removeClass('sp-video-pop-up-banner').addClass('sp-video-pop-up-banner-sticky');
            }
          } // Check if parent row/col/section has an entrance animation.
          // Section


          if (jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-section').length > 0) {
            if (seedprod_pro_check_for_entrance_animation(jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-section'))) {
              // If entrance animation is set, check if z-index is 0 or empty
              if (videoWrapper.parents('.sp-el-section').css('z-index') === '0' || videoWrapper.parents('.sp-el-section').css('z-index') === '' || videoWrapper.parents('.sp-el-section').css('z-index') === 'auto') {
                videoWrapper.parents('.sp-el-section').css('z-index', 1);
                videoWrapper.parents('.sp-el-section').css('isolation', 'isolate');
              }
            }
          } // Col


          if (jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-col').length > 0) {
            if (seedprod_pro_check_for_entrance_animation(jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-col'))) {
              // If entrance animation is set, check if z-index is 0 or empty
              if (jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-col').css('z-index') === '0' || jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-col').css('z-index') === '' || jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-col').css('z-index') === 'auto') {
                videoWrapper.parents('.sp-el-col').css('z-index', 1);
                videoWrapper.parents('.sp-el-col').css('isolation', 'isolate');
              }
            }
          } // Row


          if (jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-row').length > 0) {
            if (seedprod_pro_check_for_entrance_animation(jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).parents('.sp-el-row'))) {
              // If entrance animation is set, check if z-index is 0 or empty
              if (videoWrapper.parents('.sp-el-row').css('z-index') === '0' || videoWrapper.parents('.sp-el-row').css('z-index') === '' || videoWrapper.parents('.sp-el-row').css('z-index') === 'auto') {
                videoWrapper.parents('.sp-el-row').css('z-index', 1);
                videoWrapper.parents('.sp-el-row').css('isolation', 'isolate');
              }
            }
          }
        }
      }
    }, 2000));
  } // Teaser Video


  if (options.enable_teaser_video) {
    // When teaser video icon is clicked, play unmuted video.
    jQuery("#sp-".concat(blockId, " .sp-video-pop-up-teaser-video-play-icon-display, #sp-").concat(blockId, " .sp-video-pop-up-teaser-video-play-icon-display i")).click(function () {
      // Sticky video.
      var stickyVideoClass = '';

      if (options.enable_sticky_video) {
        // Disable for mobile.
        if (window.matchMedia('only screen and (min-width: 960px)').matches) {
          if (jQuery("#sp-".concat(blockId, " #sp-video-wrapper-").concat(blockId)).isInViewportWithThreshold()) {
            stickyVideoClass = "sp-video-wrapper ".concat(responsiveClass);
          } else {
            stickyVideoClass = 'sp-video-wrapper-sticky sp-video-responsive-sticky';
          }
        }
      } else {
        stickyVideoClass = "sp-video-wrapper ".concat(responsiveClass);
      } // Set mute to false & show controls.


      if (options.source === 'custom') {
        videoHtml = videoHtml.replace('muted', 'controls');
      }

      if (options.source === 'youtube') {
        videoHtml = videoHtml.replace('mute=1', 'mute=0');
        videoHtml = videoHtml.replace('controls=0', 'controls=1');
      }

      if (options.source === 'vimeo') {
        videoHtml = videoHtml.replace('muted=1', 'muted=0');
        videoHtml = videoHtml.replace('controls=0', 'controls=1');
      }

      jQuery("#sp-".concat(blockId, " .sp-video-pop-up-teaser-video-play-icon-display")).remove();
      jQuery("#sp-".concat(blockId, " .sp-video-pop-up-teaser-video")).remove(); // Create video element.

      videoWrapper.append("<div id=\"sp-video-responsive-".concat(blockId, "\" class=\"").concat(stickyVideoClass, " sp-video-pop-up-video\">").concat(videoHtml, "</div>"));
      videoResponsive.css('aspect-ratio', options.aspect_ratio);
    });
  }
}
/** Throttle function */


function throttle(callback, limit) {
  var waiting = false;
  return function () {
    if (!waiting) {
      callback.apply(this, arguments);
      waiting = true;
      setTimeout(function () {
        waiting = false;
      }, limit);
    }
  };
}
/** Check if a section/col/row has an entrance animation and if it has a z-index of 0 or empty */


function seedprod_pro_check_for_entrance_animation(element) {
  // Check if element contains an entrance animation of class 'sp-entrance-animation'
  if (element.hasClass('sp_animated_start')) {
    return true;
  }

  return false;
}
/**
 * post carousel javascript
 */


jQuery('.sp-postblock-nav button').click(function () {
  var currentId = '#' + jQuery(this).parents('.sp-posts-block-wrapper').attr('id');
  var currentButtonIndex = jQuery(currentId + ' .sp-postblock-nav button').index(this);
  var currentIndex = 0;
  var postblock_data = jQuery('.sp-posts-single-block', jQuery(this).parents(currentId));
  var slideshowmax = jQuery(this).parents('.sp-posts-block-wrapper').attr('data-slidetoshow');

  if (slideshowmax == undefined) {
    slideshowmax = 1;
  }

  var slider_length = Math.ceil(postblock_data.length / parseInt(slideshowmax));

  for (var customindexdata = 0; customindexdata < slider_length; customindexdata++) {
    var opa = jQuery(currentId + ' .sp-postblock-nav button[data-index="' + customindexdata + '"]').css('opacity');

    if (opa >= 0.5) {
      currentIndex = customindexdata;
    }
  }

  var buttonsLength = jQuery(currentId + ' .sp-postblock-nav button').length - 1;
  var currentButtonIndexData = jQuery(currentId + ' .sp-postblock-nav button').eq(currentButtonIndex).attr('data-index'); // check for previous button click

  if (currentButtonIndex == 0) {
    if (0 == currentIndex) {
      currentIndex = Math.ceil(postblock_data.length / parseInt(slideshowmax)) - 1;
    } else {
      currentIndex--;
    }
  } // check for next button click


  if (currentButtonIndex == buttonsLength) {
    if (Math.ceil(postblock_data.length / parseInt(slideshowmax)) - 1 == currentIndex) {
      currentIndex = 0;
    } else {
      currentIndex++;
    }
  } // reset states


  postblock_data.css({
    'z-index': 999,
    'opacity': 0,
    'height': '0',
    'position': 'absolute'
  });
  jQuery(currentId + ' .sp-postblock-nav button[data-index]').css({
    'opacity': 0.25
  });
  var startindex = parseInt(currentIndex * parseInt(slideshowmax));
  var endindex = parseInt(startindex + parseInt(slideshowmax)); // select postblock_data and button

  if (currentButtonIndexData !== undefined) {
    currentIndex = currentButtonIndexData;
    startindex = parseInt(currentIndex * parseInt(slideshowmax));
    endindex = parseInt(startindex + parseInt(slideshowmax));

    for (var i = startindex; i < endindex; i++) {
      jQuery(postblock_data).eq(i).css({
        'opacity': 1,
        'height': 'auto',
        'position': ''
      });
    }

    jQuery(currentId + ' .sp-postblock-nav button').eq(currentButtonIndex).css({
      'opacity': 1
    });
  } else {
    startindex = parseInt(currentIndex * parseInt(slideshowmax));
    endindex = parseInt(startindex + parseInt(slideshowmax));

    for (var _i4 = startindex; _i4 < endindex; _i4++) {
      jQuery(postblock_data).eq(_i4).css({
        'opacity': 1,
        'height': 'auto',
        'position': ''
      });
    }

    jQuery(currentId + ' .sp-postblock-nav button').eq(currentIndex + 1).css({
      'opacity': 1
    });
  }
});
var postblock_timers = {};
jQuery(".sp-posts-block-wrapper.sp-posts-skinlayout-carousel").each(function (index) {
  var currentId = '#' + jQuery(this).attr('id');
  var autoPlay = jQuery(this).attr('data-autoplay');
  var speed = jQuery(this).attr('data-speed');

  if (speed === '') {
    speed = 5000;
  } else {
    speed = parseInt(speed) * 1000;
  }

  if (autoPlay !== undefined) {
    postblock_timers[currentId] = setInterval(function () {
      var clickEvent = jQuery.Event('click');
      clickEvent.preventDefault();
      jQuery(currentId + ' .sp-postblock-nav button:last-child').triggerHandler(clickEvent);
    }, speed);
  }
});
jQuery(".sp-posts-block-wrapper.sp-posts-skinlayout-carousel").hover(function () {
  var id = '#' + jQuery(this).attr('id');
  clearInterval(postblock_timers[id]);
});
jQuery(".sp-posts-block-wrapper.sp-posts-skinlayout-carousel").mouseleave(function () {
  var currentId = '#' + jQuery(this).attr('id');
  var autoPlay = jQuery(this).attr('data-autoplay');
  var speed = jQuery(this).attr('data-speed');

  if (speed === '') {
    speed = 5000;
  } else {
    speed = parseInt(speed) * 1000;
  }

  if (autoPlay !== undefined) {
    postblock_timers[currentId] = setInterval(function () {
      var clickEvent = jQuery.Event('click');
      clickEvent.preventDefault();
      jQuery(currentId + ' .sp-postblock-nav button:last-child').triggerHandler(clickEvent);
    }, speed);
  }
});
/**
 * Masonary Layout
 */

if (jQuery(".sp-skin-block.sp-layout-masonary .seedprod-masonary-post-block").length > 0) {
  jQuery(".sp-skin-block.sp-layout-masonary .seedprod-masonary-post-block").imagesLoaded(function (e) {
    jQuery(" .sp-skin-block.sp-layout-masonary .seedprod-masonary-post-block").isotope({
      layoutMode: "masonry",
      itemSelector: '.sp-posts-single-block'
    });
  });
}