/**
 * FancyBox - jQuery Plugin
 * Simple and fancy lightbox alternative
 *
 * Examples and documentation at: http://fancybox.net
 *
 * Copyright (c) 2008 - 2010 Janis Skarnelis
 * That said, it is hardly a one-person project. Many people have submitted bugs, code, and offered their advice freely. Their support is greatly appreciated.
 *
 * Version: 1.3.28 (2019/04/07)
 * Requires: jQuery v1.7+
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */

(function($) {
	var tmp, loading, overlay, wrap, outer, content, close, title, nav_left, nav_right, resize_timeout,
		selectedIndex = 0, selectedOpts = {}, selectedArray = [], currentIndex = 0, currentOpts = {}, currentArray = [],
		ajaxLoader = null, imgPreloader = new Image(),
		imgRegExp = /\.(jpg|gif|png|bmp|jpeg|webp)(.*)?$/i, swfRegExp = /[^\.]\.(swf)\s*$/i, svgRegExp = /[^\.]\.(svg)\s*$/i, pdfRegExp = /[^\.]\.(pdf)\s*$/i,
		loadingTimer, loadingFrame = 1,
		titleHeight = 0, titleStr = '', start_pos, final_pos, busy = false, fx = $.extend($('<div/>')[0], { prop: 0 }),
		isIE = /MSIE|Trident/.test(window.navigator.userAgent),	isIE6 = isIE && !window.XMLHttpRequest,
		isTouch = document.createTouch !== undefined;

	/*
		* Private methods
		*/

	_abort = function() {
		$.fancybox.hideActivity();

		imgPreloader.onerror = imgPreloader.onload = null;

		if (ajaxLoader) {
			ajaxLoader.abort();
		}

		tmp.empty();
	},

	_error = function(msg) {
		if (false === selectedOpts.onError(selectedArray, selectedIndex, selectedOpts)) {
			$.fancybox.hideActivity();
			busy = false;
			return;
		}

		if ( typeof msg === 'undefined' ) {
			msg = 'Please try again later.';
		}

		selectedOpts.titleShow = false;

		selectedOpts.width = 'auto';
		selectedOpts.height = 'auto';

		tmp.html( '<p id="fancybox-error">The requested content cannot be loaded.<br />' + msg + '</p>' );

		_process_inline();
	},

	_start = function() {
		var obj = selectedArray[ selectedIndex ],
			href,
			type,
			title,
			str,
			emb,
			ret;

		_abort();

		selectedOpts = $.extend({}, $.fn.fancybox.defaults, (typeof $(obj).data('fancybox') == 'undefined' ? selectedOpts : $(obj).data('fancybox')));

		if ( document.documentElement.clientWidth < selectedOpts.minViewportWidth ) {
			busy = false;
			return;
		}

		$(document).trigger('fancybox-start', [ selectedArray, selectedIndex, selectedOpts ] );

		ret = selectedOpts.onStart(selectedArray, selectedIndex, selectedOpts);

		if (ret === false) {
			busy = false;
			return;
		} else if (typeof ret == 'object') {
			selectedOpts = $.extend(selectedOpts, ret);
		}

		title = DOMPurify.sanitize(selectedOpts.title || (obj.nodeName ? $(obj).attr('title') : obj.title) || '');

		if (obj.nodeName && !selectedOpts.orig) {
			selectedOpts.orig = $(obj).find("img:first").length ? $(obj).find("img:first") : $(obj);
		}

		if (title === '' && selectedOpts.orig) {
			title = DOMPurify.sanitize(selectedOpts.orig.attr('title')) || (selectedOpts.titleFromAlt ? DOMPurify.sanitize(selectedOpts.orig.attr('alt')) : '');
		}

		href = selectedOpts.href || (obj.nodeName ? $(obj).attr('href') : obj.href) || null;

		if ((/^(?:javascript)/i).test(href) || href == '#') {
			href = null;
		}

		if (selectedOpts.type) {
			type = selectedOpts.type;

			if (!href) {
				href = selectedOpts.content;
			}

		} else if (selectedOpts.content) {
			type = 'html';

		} else if ( $(obj).hasClass('iframe') ) {
			type = 'iframe';

		} else if (href) {
			if (href.match(imgRegExp) || $(obj).hasClass("image")) {
				type = 'image';

			} else if (href.match(swfRegExp)) {
				type = 'swf';

			} else if (href.match(svgRegExp)) {
				type = 'svg';

			} else if (href.match(pdfRegExp)) {
				type = 'pdf';

			} else if (href.indexOf("#") === 0) {
				type = 'inline';

			} else {
				type = 'ajax';
			}
		}

		if (!type) {
			_error('No content type found.');
			return;
		}

		if ($(obj).hasClass('modal')) {
			selectedOpts.modal = true;
		}

		if (type == 'inline') {
			obj	= href.substr(href.indexOf("#"));
			type = $(obj).length > 0 ? 'inline' : 'ajax';
		}

		selectedOpts.type = type;
		selectedOpts.href = href;
		selectedOpts.title = title;

		if (selectedOpts.autoDimensions) {
			if (selectedOpts.type == 'html' || selectedOpts.type == 'inline' || selectedOpts.type == 'ajax') {
				selectedOpts.width = 'auto';
				selectedOpts.height = 'auto';
			} else {
				selectedOpts.autoDimensions = false;
			}
		}

		if (selectedOpts.modal) {
			selectedOpts.overlayShow = true;
			selectedOpts.hideOnOverlayClick = false;
			selectedOpts.hideOnContentClick = false;
			selectedOpts.enableEscapeButton = false;
			selectedOpts.showCloseButton = false;
		}

		selectedOpts.padding = parseInt(selectedOpts.padding, 10);
		selectedOpts.margin = parseInt(selectedOpts.margin, 10);

		tmp.css('padding', (selectedOpts.padding + selectedOpts.margin));

		$('.fancybox-inline-tmp').off('fancybox-cancel').on('fancybox-change', function() {
			$(this).replaceWith(content.children());
		});

		switch (type) {
			case 'html' :
				tmp.html( selectedOpts.content );
				_process_inline();
			break;

			case 'inline' :
				if ( $(obj).parent().is('#fancybox-content') === true) {
					busy = false;
					return;
				}

				$('<div class="fancybox-inline-tmp" />')
					.hide()
					.insertBefore( $(obj) )
					.on('fancybox-cleanup', function() {
						$(this).replaceWith(content.find(obj));
					}).on('fancybox-cancel', function() {
						$(this).replaceWith(tmp.find(obj));
					});

				$(obj).appendTo(tmp);

				_process_inline();
			break;

			case 'image':
				selectedOpts.keepRatio = true;

				busy = false;

				$.fancybox.showActivity();

				imgPreloader = new Image();

				imgPreloader.onerror = function() {
					_error('No image found.');
				};

				imgPreloader.onload = function() {
					busy = true;

					imgPreloader.onerror = imgPreloader.onload = null;

					_process_image();
				};

				imgPreloader.src = href;
			break;

			case 'swf':
				selectedOpts.scrolling = 'no';
				selectedOpts.keepRatio = true;

				str = '<object type="application/x-shockwave-flash" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="' + selectedOpts.width + '" height="' + selectedOpts.height + '"><param name="movie" value="' + href + '"></param>';
				emb = '';

				$.each(selectedOpts.swf, function(name, val) {
					str += '<param name="' + name + '" value="' + val + '"></param>';
					emb += ' ' + name + '="' + val + '"';
				});

				str += '<embed src="' + href + '" type="application/x-shockwave-flash" width="' + selectedOpts.width + '" height="' + selectedOpts.height + '"' + emb + '></embed></object>';

				tmp.html(str);

				_process_inline();
			break;

			case 'svg':
				selectedOpts.scrolling = 'no';
				selectedOpts.keepRatio = true;

				str = '<object type="image/svg+xml" width="' + selectedOpts.width + '" height="' + selectedOpts.height + '" data="' + href + '"></object>';

				tmp.html(str);

				_process_inline();
			break;

			case 'pdf':
				selectedOpts.scrolling = 'no';
				selectedOpts.enableKeyboardNav = false;
				selectedOpts.showNavArrows = false;

				str = '<object type="application/pdf" width="100%" height="100%" data="' + href + '"><a href="' + href + '" style="display:block;position:absolute;top:48%;width:100%;text-align:center">' + $(obj).html() + '</a></object>';

				tmp.html(str);

				_process_inline();
			break;

			case 'ajax':
				busy = false;

				$.fancybox.showActivity();

				selectedOpts.ajax.win = selectedOpts.ajax.success;

				ajaxLoader = $.ajax($.extend({}, selectedOpts.ajax, {
					url	: href,
					data : selectedOpts.ajax.data || {},
					error : function(XMLHttpRequest, textStatus, errorThrown) {
						if ( XMLHttpRequest.status > 0 ) {
							_error(errorThrown);
						}
					},
					success : function(data, textStatus, XMLHttpRequest) {
						var o = typeof XMLHttpRequest == 'object' ? XMLHttpRequest : ajaxLoader;
						if (o.status == 200) {
							if ( typeof selectedOpts.ajax.win == 'function' ) {
								ret = selectedOpts.ajax.win(href, data, textStatus, XMLHttpRequest);

								if (ret === false) {
									$.fancybox.hideActivity();
									return;
								} else if (typeof ret == 'string' || typeof ret == 'object') {
									data = ret;
								}
							}

							if ( data.indexOf("<!DOCTYPE") > -1 || data.indexOf("<html") > -1 || data.indexOf("<body") > -1 ) {
								_error('Unexpected response.');
							} else {
								tmp.html( data );
								_process_inline();
							}
						}
					}
				}));
			break;

			case 'iframe':
				selectedOpts.enableKeyboardNav = false;
				selectedOpts.showNavArrows = false;

				$.fancybox.showActivity();

				_show();
			break;
		}
	},

	_process_inline = function() {
		var w = selectedOpts.width,
			h = selectedOpts.height,
			ww = $(window).width() == 0 ? window.innerWidth : $(window).width(),
			wh = $(window).height() == 0 ? window.innerHeight : $(window).height();

		if (w.toString().indexOf('%') > -1) {
			w = parseInt( (ww - (selectedOpts.margin * 2)) * parseFloat(w) / 100, 10) + 'px';
		} else {
			w = w == 'auto' ? 'auto' : w + 'px';
		}

		if (h.toString().indexOf('%') > -1) {
			h = parseInt( (wh - (selectedOpts.margin * 2)) * parseFloat(h) / 100, 10) + 'px';
		} else {
			h = h == 'auto' ? 'auto' : h + 'px';
		}

		tmp.wrapInner('<div style="width:' + w + ';height:' + h + ';overflow:' + (selectedOpts.scrolling == 'auto' ? 'auto' : (selectedOpts.scrolling == 'yes' ? 'scroll' : 'hidden')) + ';position:relative;"></div>');

		selectedOpts.width = Math.ceil( tmp.width() );
		selectedOpts.height = Math.ceil( tmp.height() );

		_show();
	},

	_process_image = function() {
		selectedOpts.width = imgPreloader.width;
		selectedOpts.height = imgPreloader.height;

		$("<img />").attr({
			'id' : 'fancybox-img',
			'src' : imgPreloader.src,
			'alt' : selectedOpts.title
		}).appendTo( tmp );

		_show();
	},

	_show = function() {
		var pos, equal;

		if (selectedOpts.type !== 'iframe') {
			$.fancybox.hideActivity();
		}

		if (wrap.is(":visible") && false === currentOpts.onCleanup(currentArray, currentIndex, currentOpts)) {
			$('.fancybox-inline-tmp').trigger('fancybox-cancel');

			busy = false;
			return;
		}

		busy = true;

		$(content.add( overlay )).off();

		$(window).off("orientationchange.fb resize.fb scroll.fb");
		$(document).off('keydown.fb');

		if (wrap.is(":visible") && currentOpts.titlePosition !== 'outside') {
			wrap.css('height', wrap.height());
		}

		currentArray = selectedArray;
		currentIndex = selectedIndex;
		currentOpts = selectedOpts;

		if (currentOpts.overlayShow) {
			overlay.css({
				'background-color' : currentOpts.overlayColor,
				'opacity' : currentOpts.overlayOpacity,
				'cursor' : currentOpts.hideOnOverlayClick ? 'pointer' : 'auto',
				'height' : $(document).height()
			});

			if (!overlay.is(':visible')) {
				if (isIE6) {
					$('select:not(#fancybox-tmp select)').filter(function() {
						return this.style.visibility !== 'hidden';
					}).css({'visibility' : 'hidden'}).one('fancybox-cleanup', function() {
						this.style.visibility = 'inherit';
					});
				}
				overlay.show();
			}
		} else {
			overlay.hide();
		}

		final_pos = _get_zoom_to();

		_process_title();

		if (wrap.is(":visible")) {
			$( close.add( nav_left ).add( nav_right ) ).hide();

			pos = wrap.position(),

			start_pos = {
				top	 : pos.top,
				left : pos.left,
				width : wrap.width(),
				height : wrap.height()
			};

			equal = (start_pos.width == final_pos.width && start_pos.height == final_pos.height);

			content.fadeTo(currentOpts.changeFade, 0.3, function() {
				var finish_resizing = function() {
					content.html( tmp.contents() ).fadeTo(currentOpts.changeFade, 1, _finish);
				};

				$('.fancybox-inline-tmp').trigger('fancybox-change');

				content
					.empty()
					.removeAttr('filter')
					.css({
						'border-width' : currentOpts.padding,
						'width'	: final_pos.width - currentOpts.padding * 2,
						'height' : currentOpts.autoDimensions ? 'auto' : final_pos.height - titleHeight - currentOpts.padding * 2
					});

				if (equal) {
					finish_resizing();
				} else {
					fx.prop = 0;

					$(fx).animate({prop: 1}, {
							duration : currentOpts.changeSpeed,
							easing : currentOpts.easingChange,
							step : _draw,
							complete : finish_resizing
					});
				}
			});

			return;
		}

		wrap.removeAttr("style");

		content.css('border-width', currentOpts.padding);

		if (currentOpts.transitionIn == 'elastic') {
			start_pos = _get_zoom_from();

			content.html( tmp.contents() );

			wrap.show();

			if (currentOpts.opacity) {
				final_pos.opacity = 0;
			}

			fx.prop = 0;

			$(fx).animate({prop: 1}, {
					duration : currentOpts.speedIn,
					easing : currentOpts.easingIn,
					step : _draw,
					complete : _finish
			});

			return;
		}

		if (currentOpts.titlePosition == 'inside' && titleHeight > 0) {
			title.show();
		}

		content
			.css({
				'width' : final_pos.width - currentOpts.padding * 2,
				'height' : currentOpts.autoDimensions ? 'auto' : final_pos.height - titleHeight - currentOpts.padding * 2
			})
			.html( tmp.contents() );

		wrap
			.css(final_pos)
			.fadeIn( currentOpts.transitionIn == 'none' ? 0 : currentOpts.speedIn, _finish );
	},

	_format_title = function(title) {
		if (title && title.length) {
			if (currentOpts.titlePosition == 'float') {
				return '<table id="fancybox-title-float-wrap" style="border-spacing:0;border-collapse:collapse"><tr><td id="fancybox-title-float-left"></td><td id="fancybox-title-float-main">' + title + '</td><td id="fancybox-title-float-right"></td></tr></table>';
			}

			return '<div id="fancybox-title-' + currentOpts.titlePosition + '">' + title + '</div>';
		}

		return false;
	},

	_process_title = function() {
		titleStr = currentOpts.title || '';
		titleHeight = 0;

		title
			.empty()
			.removeAttr('style')
			.removeClass();

		if (currentOpts.titleShow === false) {
			title.hide();
			return;
		}

		titleStr = $.isFunction(currentOpts.titleFormat) ? currentOpts.titleFormat(titleStr, currentArray, currentIndex, currentOpts) : _format_title(titleStr);

		if (!titleStr || titleStr === '') {
			title.hide();
			return;
		}

		title
			.addClass('fancybox-title-' + currentOpts.titlePosition)
			.html( titleStr )
			.appendTo( 'body' )
			.show();

		switch (currentOpts.titlePosition) {
			case 'inside':
				title
					.css({
						'width' : final_pos.width - (currentOpts.padding * 2),
						'marginLeft' : currentOpts.padding,
						'marginRight' : currentOpts.padding
					})
					.appendTo( outer );

				titleHeight = title.outerHeight(true);

				final_pos.height += titleHeight;
			break;

			case 'over':
				title
					.css({
						'marginLeft' : currentOpts.padding,
						'width'	: final_pos.width - (currentOpts.padding * 2),
						'bottom' : currentOpts.padding
					})
					.appendTo( outer );
			break;

			case 'float':
				title
					.css('left', parseInt( ( title.width() - final_pos.width ) / 2, 10 ) * -1 )
					.appendTo( wrap );
			break;

			default:
				title
					.css({
						'width' : final_pos.width - (currentOpts.padding * 2),
						'paddingLeft' : currentOpts.padding,
						'paddingRight' : currentOpts.padding
					})
					.appendTo( wrap );
			break;
		}

		title.hide();
	},

	_set_navigation = function() {
		if (currentOpts.enableEscapeButton || currentOpts.enableKeyboardNav) {
			$(document).on('keydown.fb', function(e) {
				if (e.keyCode == 27 && currentOpts.enableEscapeButton) {
					e.preventDefault();
					$.fancybox.close();
				} else if ((e.keyCode == 37 || e.keyCode == 39) && currentOpts.enableKeyboardNav && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA' && e.target.tagName !== 'SELECT') {
					e.preventDefault();
					$.fancybox[ e.keyCode == 37 ? 'prev' : 'next']();
				} else if ((e.keyCode == 9) && currentOpts.enableKeyboardNav && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA' && e.target.tagName !== 'SELECT') {
					e.preventDefault();
					$.fancybox[ e.shiftKey ? 'prev' : 'next']();
				}
			});
		}

		if (!currentOpts.showNavArrows) {
			nav_left.hide();
			nav_right.hide();
			return;
		}

		if ((currentOpts.cyclic && currentArray.length > 1) || currentIndex !== 0) {
			nav_left.show();
		}

		if ((currentOpts.cyclic && currentArray.length > 1) || currentIndex != (currentArray.length -1)) {
			nav_right.show();
		}
	},

	_finish = function () {
		if (isIE) {
			content.css('filter', 0);
			wrap.css('filter', 0);
		}

		if (currentOpts.autoDimensions) {
			content.css('height','auto');
		}

		wrap.css('height', 'auto');

		if (titleStr && titleStr.length) {
			title.show();
		}

		if (currentOpts.showCloseButton) {
			close.show();
		}

		_set_navigation();

		if (currentOpts.hideOnContentClick)	{
			content.on('click', $.fancybox.close);
		}

		if (currentOpts.hideOnOverlayClick)	{
			overlay.on('click', $.fancybox.close);
		}

		if (currentOpts.autoResize) {
			$(window).on("resize.fb", $.fancybox.resize);
		}

		if (currentOpts.centerOnScroll && !isTouch) {
			$(window).on("scroll.fb", $.fancybox.center);
		}

		if ($.fn.mousewheel) {
			wrap.on('mousewheel.fb', function(e, delta) {
				if (busy) {
					e.preventDefault();
				} else if ( currentOpts.type == 'image' && ( $(e.target).outerHeight() == 0 || $(e.target).prop('scrollHeight') === $(e.target).outerHeight() ) ) {
					e.preventDefault();
					$.fancybox[ delta > 0 ? 'prev' : 'next' ]();
				}
			});
		}

		if (currentOpts.type == 'iframe') {
			$('<iframe id="fancybox-frame" name="fancybox-frame' + new Date().getTime() + '"' + (navigator.userAgent.match(/msie [6]/i) ? ' allowtransparency="true""' : '')
				+ ' style="border:0;margin:0;overflow:' + (currentOpts.scrolling == 'auto' ? 'auto' : (currentOpts.scrolling == 'yes' ? 'scroll' : 'hidden')) + '" src="'
				+ currentOpts.href + '"' + (false === currentOpts.allowfullscreen ? '' : ' allowfullscreen') + ' allow="autoplay; encrypted-media" tabindex="999"></iframe>')
				.appendTo(content).on('load',function() {
				$.fancybox.hideActivity();
			}).focus();
		}

		wrap.show();

		busy = false;

		$.fancybox.center();

		$(document).trigger('fancybox-complete', [ currentArray, currentIndex, currentOpts ] );

		currentOpts.onComplete(currentArray, currentIndex, currentOpts);

		if (currentArray.length > 1) {
			_preload_next();
			_preload_prev();
		}
	},

	_preload_next = function() {
		var pos = typeof arguments[0] == 'number' ? arguments[0] : currentIndex + 1;

		if ( pos >= currentArray.length ) {
			if (currentOpts.cyclic) {
				pos = 0;
			} else {
				return;
			}
		}

		if ( pos == currentIndex ) {
			currentOpts.enableKeyboardNav = false;
			wrap.off('mousewheel.fb');
			nav_right.hide();
			return;
		}

		if ( _preload_image( pos ) ) {
			return;
		} else {
			_preload_next( pos + 1 );
		}
	},

	_preload_prev = function() {
		var pos = typeof arguments[0] == 'number' ? arguments[0] : currentIndex - 1;

		if ( pos < 0 ) {
			if (currentOpts.cyclic) {
				pos = currentArray.length - 1;
			} else {
				return;
			}
		}

		if ( pos == currentIndex ) {
			currentOpts.enableKeyboardNav = false;
			wrap.off('mousewheel.fb');
			nav_left.hide();
			return;
		}

		if ( _preload_image( pos ) ) {
			return;
		} else {
			_preload_prev( pos - 1 );
		}
	},

	_preload_image = function(pos) {
		var objNext, obj = currentArray[ pos ];

		if ( typeof obj !== 'undefined' && typeof obj.href !== 'undefined' &&  obj.href !== currentOpts.href && (obj.href.match(imgRegExp) || $(obj).hasClass("image")) ) {
			objNext = new Image();
			objNext.src = obj.href;
			return true;
		} else {
			return false;
		}
	},

	_draw = function(pos) {
		var dim = {
			width : parseInt(start_pos.width + (final_pos.width - start_pos.width) * pos, 10),
			height : parseInt(start_pos.height + (final_pos.height - start_pos.height) * pos, 10),

			top : parseInt(start_pos.top + (final_pos.top - start_pos.top) * pos, 10),
			left : parseInt(start_pos.left + (final_pos.left - start_pos.left) * pos, 10)
		};

		if (typeof final_pos.opacity !== 'undefined') {
			dim.opacity = pos < 0.5 ? 0.5 : pos;
		}

		wrap.css(dim);

		content.css({
			'width' : dim.width - currentOpts.padding * 2,
			'height' : dim.height - (titleHeight * pos) - currentOpts.padding * 2
		});
	},

	_get_viewport = function() {
		var w = !isTouch && window.innerWidth && document.documentElement.clientWidth ?
				Math.min(window.innerWidth, document.documentElement.clientWidth) :
				window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth,
			h = !isTouch && window.innerHeight && document.documentElement.clientHeight ?
				Math.min(window.innerHeight, document.documentElement.clientHeight) :
				window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight,
			margin;

		margin = arguments[0] === true ? 0 : currentOpts.margin;

		return [
			w - (margin * 2),
			h - (margin * 2),
			$(document).scrollLeft() + margin,
			$(document).scrollTop() + margin
		];
	},

	_get_zoom_to = function () {
		var view = _get_viewport(),
			to = {},
			double_padding = currentOpts.padding * 2,
			ratio;

		if (currentOpts.width.toString().indexOf('%') > -1) {
			to.width = parseInt((view[0] * parseFloat(currentOpts.width)) / 100, 10);
		} else {
			to.width = currentOpts.width + double_padding;
		}

		if (currentOpts.height.toString().indexOf('%') > -1) {
			to.height = parseInt((view[1] * parseFloat(currentOpts.height)) / 100, 10);
		} else {
			to.height = currentOpts.height + double_padding;
		}

		if (currentOpts.autoScale && (to.width > view[0] || to.height > view[1])) {
			if (currentOpts.keepRatio) {
				ratio = currentOpts.width / currentOpts.height;

				if ((to.width ) > view[0]) {
					to.width = view[0];
					to.height = parseInt(((to.width - double_padding) / ratio) + double_padding, 10);
				}

				if ((to.height) > view[1]) {
					to.height = view[1];
					to.width = parseInt(((to.height - double_padding) * ratio) + double_padding, 10);
				}
			} else {
				to.width = Math.min(to.width, view[0]);
				to.height = Math.min(to.height, view[1]);
			}
		}

		to.top = parseInt(Math.max(view[3] - 20, view[3] + ((view[1] - to.height - 40) * 0.5)), 10);
		to.left = parseInt(Math.max(view[2] - 20, view[2] + ((view[0] - to.width - 40) * 0.5)), 10);

		return to;
	},

	_get_obj_pos = function(obj) {
		var pos = obj.offset();

		pos.top += parseInt( obj.css('paddingTop'), 10 ) || 0;
		pos.left += parseInt( obj.css('paddingLeft'), 10 ) || 0;

		pos.top += parseInt( obj.css('border-top-width'), 10 ) || 0;
		pos.left += parseInt( obj.css('border-left-width'), 10 ) || 0;

		pos.width = obj.width();
		pos.height = obj.height();

		return pos;
	},

	_get_zoom_from = function() {
		var orig = selectedOpts.orig ? $(selectedOpts.orig) : false,
			from = {},
			pos,
			view;

		if (orig && orig.length) {
			pos = _get_obj_pos(orig);

			from = {
				width : pos.width + (currentOpts.padding * 2),
				height : pos.height + (currentOpts.padding * 2),
				top : pos.top - currentOpts.padding - 20,
				left : pos.left - currentOpts.padding - 20
			};

		} else {
			view = _get_viewport();

			from = {
				width : currentOpts.padding * 2,
				height : currentOpts.padding * 2,
				top : parseInt((view[3] + view[1]) * 0.5, 10),
				left : parseInt((view[2] + view[0]) * 0.5, 10)
			};
		}

		return from;
	},

	_animate_loading = function() {
		if (!loading.is(':visible')){
			clearInterval(loadingTimer);
			return;
		}

		$('div', loading).css('top', (loadingFrame * - 40 ) + 'px');

		loadingFrame = (loadingFrame + 1) % 12;
	};

	/*
	 * Public methods
	 */

	$.fn.fancybox = function(options) {
		if (!$(this).length) {
			return this;
		}

		$(this)
			.data('fancybox', $.extend({}, options, ($.metadata ? $(this).metadata() : {})))
			.off('click.fb')
			.on('click.fb', function(e) {
				e.preventDefault();

				if (busy) {
					return;
				}

				busy = true;

				$(this).blur();

				selectedArray = [];
				selectedIndex = 0;

				var rel = $(this).attr('rel') || '';

				if (rel == '' || rel.replace(/alternate|external|help|license|nofollow|noreferrer|noopener|\s+/gi,'') == '') {
					selectedArray.push(this);
				} else {
					selectedArray = $('a[rel="' + rel + '"], area[rel="' + rel + '"]');
					selectedIndex = selectedArray.index( this );
				}

				_start();

				return false;
			});

		return this;
	};

	$.fancybox = function(obj) {
		var opts;

		if (busy) {
			return;
		}

		busy = true;
		opts = typeof arguments[1] !== 'undefined' ? arguments[1] : {};

		selectedArray = [];
		selectedIndex = parseInt(opts.index, 10) || 0;

		if ( $.isArray(obj) ) {
			for (var i = 0, j = obj.length; i < j; i++) {
				if (typeof obj[i] == 'object') {
					$(obj[i]).data('fancybox', $.extend({}, opts, obj[i]));
				} else {
					obj[i] = $({}).data('fancybox', $.extend({content : obj[i]}, opts));
				}
			}

			selectedArray = jQuery.merge(selectedArray, obj);

		} else {
			if ( typeof obj == 'object' ) {
				$(obj).data('fancybox', $.extend({}, opts, obj));
			} else {
				obj = $({}).data('fancybox', $.extend({content : obj}, opts));
			}

			selectedArray.push(obj);
		}

		if (selectedIndex > selectedArray.length || selectedIndex < 0) {
			selectedIndex = 0;
		}

		_start();
	};

	$.fancybox.showActivity = function() {
		clearInterval(loadingTimer);

		loading.show();
		loadingTimer = setInterval( _animate_loading, 66 );
	};

	$.fancybox.hideActivity = function() {
		loading.hide();
	};

	$.fancybox.next = function() {
		var obj, pos = typeof arguments[0] == 'number' ? arguments[0] : currentIndex + 1;

		if (pos >= currentArray.length) {
			if (currentOpts.cyclic) {
				pos = 0;
			} else {
				return;
			}
		}

		obj = currentArray[pos];

		if ( pos != currentIndex && typeof obj !== 'undefined' && typeof obj.href !== 'undefined' && obj.href === currentOpts.href ) {
			$.fancybox.next( pos + 1 );
		} else {
			$.fancybox.pos( pos );
		}

		return;
	};

	$.fancybox.prev = function() {
		var obj, pos = typeof arguments[0] == 'number' ? arguments[0] : currentIndex - 1;

		if (pos < 0) {
			if (currentOpts.cyclic) {
				pos = currentArray.length - 1;
			} else {
				return;
			}
		}

		obj = currentArray[pos];

		if ( pos != currentIndex && typeof obj !== 'undefined' && typeof obj.href !== 'undefined' && obj.href === currentOpts.href ) {
			$.fancybox.prev( pos - 1 );
		} else {
			$.fancybox.pos( pos );
		}

		return;
	};

	$.fancybox.pos = function(pos) {
		if (busy) {
			return;
		}

		pos = parseInt(pos);

		selectedArray = currentArray;

		if (pos > -1 && pos < currentArray.length) {
			selectedIndex = pos;
			_start();
		}

		return;
	};

	$.fancybox.cancel = function() {
		if (busy) {
			return;
		}

		busy = true;

		$('.fancybox-inline-tmp').trigger('fancybox-cancel');

		_abort();

		selectedOpts.onCancel(selectedArray, selectedIndex, selectedOpts);

		busy = false;
	};

	// Note: within an iframe use - parent.$.fancybox.close();
	$.fancybox.close = function() {
		if (busy || wrap.is(':hidden')) {
			return;
		}

		busy = true;

		if (currentOpts && false === currentOpts.onCleanup(currentArray, currentIndex, currentOpts)) {
			busy = false;
			return;
		}

		_abort();

		$(close.add( nav_left ).add( nav_right )).hide();

		$(content.add( overlay )).off();

		$(window).off("orientationchange.fb resize.fb scroll.fb mousewheel.fb");
		$(document).off('keydown.fb');

		// This helps IE
		if (isIE) {
			content.find('iframe#fancybox-frame').attr('src', isIE6 && /^https/i.test(window.location.href || '') ? 'javascript:void(false)' : '//about:blank');
		}

		if (currentOpts.titlePosition !== 'inside') {
			title.empty();
		}

		wrap.stop();

		function _cleanup() {
			overlay.fadeOut('fast');

			title.empty().hide();
			wrap.hide();

			$('.fancybox-inline-tmp').trigger('fancybox-cleanup');

			content.empty();

			$(document).trigger('fancybox-closed', [ currentArray, currentIndex, currentOpts ] );

			currentOpts.onClosed(currentArray, currentIndex, currentOpts);

			currentArray = selectedOpts	= [];
			currentIndex = selectedIndex = 0;
			currentOpts = selectedOpts	= {};

			busy = false;
		}

		if (currentOpts.transitionOut == 'elastic') {
			start_pos = _get_zoom_from();

			var pos = wrap.position();

			final_pos = {
				top : pos.top ,
				left : pos.left,
				width :	wrap.width(),
				height : wrap.height()
			};

			if (currentOpts.opacity) {
				final_pos.opacity = 1;
			}

			title.empty().hide();

			fx.prop = 1;

			$(fx).animate({ prop: 0 }, {
				 duration : currentOpts.speedOut,
				 easing : currentOpts.easingOut,
				 step : _draw,
				 complete : _cleanup
			});

		} else {
			wrap.fadeOut( currentOpts.transitionOut == 'none' ? 0 : currentOpts.speedOut, _cleanup);
		}
	};

	$.fancybox.resize = function() {
		var pos;

		clearTimeout( resize_timeout );

		resize_timeout = setTimeout( function() {
			var finish_resizing = function() {
				if (selectedOpts.autoDimensions) {
					content.css('height','auto');
				}

				wrap.css('height', 'auto');

				if (titleStr && titleStr.length) {
					title.show();
				}

				busy = false;

				$.fancybox.center(true);
			};

			if (overlay.is(':visible')) {
				overlay.css('height', $(document).height());
			}

			pos = wrap.position(),

			start_pos = {
				top	 : pos.top,
				left : pos.left,
				width : wrap.width(),
				height : wrap.height()
			};

			final_pos = _get_zoom_to();

			busy = true;

			_process_title();

			fx.prop = 0;

			$(fx).animate({prop: 1}, {
				duration : currentOpts.changeSpeed,
				easing : currentOpts.easingChange,
				step : _draw,
				complete : finish_resizing
			});
		}, 500 );
	};

	$.fancybox.center = function() {
		var view, align;

		if (busy) {
			return;
		}

		align = arguments[0] === true ? 1 : 0;
		view = _get_viewport(true);

		if (!align && ((wrap.width() + 40) > view[0] || (wrap.height() + 40) > view[1])) {
			return;
		}

		wrap
			.stop()
			.animate({
				'top' : parseInt(Math.max(view[3] - 20, view[3] + ((view[1] - content.height() - 40) * 0.5) - currentOpts.padding)),
				'left' : parseInt(Math.max(view[2] - 20, view[2] + ((view[0] - content.width() - 40) * 0.5) - currentOpts.padding))
			}, typeof arguments[0] == 'number' ? arguments[0] : 300);
	};

	$.fancybox.init = function() {
		if ($("#fancybox-wrap").length) {
			return;
		}

		$('body').append(
			tmp	= $('<div id="fancybox-tmp"></div>'),
			loading	= $('<div id="fancybox-loading"><div></div></div>'),
			overlay	= $('<div id="fancybox-overlay"></div>'),
			wrap = $('<div id="fancybox-wrap"></div>')
		);

		outer = $('<div id="fancybox-outer"></div>')
			.append('<div class="fancybox-bg" id="fancybox-bg-n"></div><div class="fancybox-bg" id="fancybox-bg-ne"></div><div class="fancybox-bg" id="fancybox-bg-e"></div><div class="fancybox-bg" id="fancybox-bg-se"></div><div class="fancybox-bg" id="fancybox-bg-s"></div><div class="fancybox-bg" id="fancybox-bg-sw"></div><div class="fancybox-bg" id="fancybox-bg-w"></div><div class="fancybox-bg" id="fancybox-bg-nw"></div>')
			.appendTo( wrap );

		outer.append(
			content = $('<div id="fancybox-content"></div>'),
			close = $(`<a id="fancybox-close" title="${(window.efb_i18n && window.efb_i18n.close) || 'Close'}" class="fancy-ico" href="javascript:;"></a>`),
			title = $('<div id="fancybox-title"></div>'),

			nav_left = $(`<a id="fancybox-left" title="${(window.efb_i18n && window.efb_i18n.prev) || 'Previous'}" href="javascript:;"><span class="fancy-ico" id="fancybox-left-ico"></span></a>`),
			nav_right = $(`<a id="fancybox-right" title="${(window.efb_i18n && window.efb_i18n.next) || 'Next'}" href="javascript:;"><span class="fancy-ico" id="fancybox-right-ico"></span></a>`)
		);

		close.on('click',$.fancybox.close);
		loading.on('click',$.fancybox.cancel);

		nav_left.on('click',function(e) {
			e.preventDefault();
			$.fancybox.prev();
		});

		nav_right.on('click',function(e) {
			e.preventDefault();
			$.fancybox.next();
		});

		if (isIE) {
			wrap.addClass('fancybox-ie');
		}

		if (isIE6) {
			loading.addClass('fancybox-ie6');
			wrap.addClass('fancybox-ie6');

			$('<iframe id="fancybox-hide-sel-frame" src="' + (/^https/i.test(window.location.href || '') ? 'javascript:void(false)' : 'about:blank' ) + '" style="overflow:hidden;border:0" tabindex="-1"></iframe>').prependTo(outer);
		}
	};

	$.fn.fancybox.defaults = {
		padding : 10,
		margin : 40,
		opacity : false,
		modal : false,
		cyclic : false,
		allowfullscreen : false,
		scrolling : 'auto',	// 'auto', 'yes' or 'no'

		width : 560,
		height : 340,

		autoScale : true,
		autoDimensions : true,
		centerOnScroll : false,
		autoResize : true,
		keepRatio : false,
		minViewportWidth : 0,

		ajax : {},
		swf : { wmode: 'opaque' },
		svg : { wmode: 'opaque' },

		hideOnOverlayClick : true,
		hideOnContentClick : false,

		overlayShow : true,
		overlayOpacity : 0.7,
		overlayColor : '#777',

		titleShow : true,
		titlePosition : 'float', // 'float', 'outside', 'inside' or 'over'
		titleFormat : null,
		titleFromAlt : true,

		transitionIn : 'fade', // 'elastic', 'fade' or 'none'
		transitionOut : 'fade', // 'elastic', 'fade' or 'none'

		speedIn : 300,
		speedOut : 300,

		changeSpeed : 300,
		changeFade : 'fast',

		easingIn : 'swing',
		easingOut : 'swing',

		showCloseButton	 : true,
		showNavArrows : true,
		enableEscapeButton : true,
		enableKeyboardNav : true,

		onStart : function(){},
		onCancel : function(){},
		onComplete : function(){},
		onCleanup : function(){},
		onClosed : function(){},
		onError : function(){}
	};

	$(document).ready(function() {
		$.fancybox.init();
	});

})(jQuery);
