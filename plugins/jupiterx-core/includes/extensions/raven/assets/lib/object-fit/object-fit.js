/*----------------------------------------
 * objectFitPolyfill 2.1.1
 *
 * Made by Constance Chen
 * Released under the MIT license
 *
 * https://github.com/constancecchen/object-fit-polyfill
 *--------------------------------------*/

(function () {
	"use strict";

	// if the page is being rendered on the server, don't continue
	if (typeof window === "undefined") {
		return;
	}

	// Workaround for Edge 16+, which only implemented object-fit for <img> tags
	// TODO: Keep an eye on Edge to determine which version has full final support
	var edgeVersion = window.navigator.userAgent.match(/Edge\/(\d{2})\./);
	var edgePartialSupport = (edgeVersion) ? (parseInt(edgeVersion[1], 10) >= 16) : false;

	// If the browser does support object-fit, we don't need to continue
	if ("objectFit" in document.documentElement.style !== false && !edgePartialSupport) {
		window.objectFitPolyfill = function () { return false };
		return;
	}

  /**
   * Check the container's parent element to make sure it will
   * correctly handle and clip absolutely positioned children
   *
   * @param {node} $container - parent element
   */
	var checkParentContainer = function ($container) {
		var styles = window.getComputedStyle($container, null);
		var position = styles.getPropertyValue("position");
		var overflow = styles.getPropertyValue("overflow");
		var display = styles.getPropertyValue("display");

		if (!position || position === "static") {
			$container.style.position = "relative";
		}
		if (overflow !== "hidden") {
			$container.style.overflow = "hidden";
		}
		// Guesstimating that people want the parent to act like full width/height wrapper here.
		// Mostly attempts to target <picture> elements, which default to inline.
		if (!display || display === "inline") {
			$container.style.display = "block";
		}
		if ($container.clientHeight === 0) {
			$container.style.height = "100%";
		}

		// Add a CSS class hook, in case people need to override styles for any reason.
		if ($container.className.indexOf("object-fit-polyfill") === -1) {
			$container.className = $container.className + " object-fit-polyfill";
		}
	};

  /**
   * Check for pre-set max-width/height, min-width/height,
   * positioning, or margins, which can mess up image calculations
   *
   * @param {node} $media - img/video element
   */
	var checkMediaProperties = function ($media) {
		var styles = window.getComputedStyle($media, null);
		var constraints = {
			"max-width": "none",
			"max-height": "none",
			"min-width": "0px",
			"min-height": "0px",
			"top": "auto",
			"right": "auto",
			"bottom": "auto",
			"left": "auto",
			"margin-top": "0px",
			"margin-right": "0px",
			"margin-bottom": "0px",
			"margin-left": "0px",
		};

		for (var property in constraints) {
			var constraint = styles.getPropertyValue(property);

			if (constraint !== constraints[property]) {
				$media.style[property] = constraints[property];
			}
		}
	};

  /**
   * Calculate & set object-position
   *
   * @param {string} axis - either "x" or "y"
   * @param {node} $media - img or video element
   * @param {string} objectPosition - e.g. "50% 50%", "top bottom"
   */
	var setPosition = function (axis, $media, objectPosition) {
		var position, other, start, end, side;
		objectPosition = objectPosition.split(" ");

		if (objectPosition.length < 2) {
			objectPosition[1] = objectPosition[0];
		}

		if (axis === "x") {
			position = objectPosition[0];
			other = objectPosition[1];
			start = "left";
			end = "right";
			side = $media.clientWidth;
		}
		else if (axis === "y") {
			position = objectPosition[1];
			other = objectPosition[0];
			start = "top";
			end = "bottom";
			side = $media.clientHeight;
		}
		else {
			return; // Neither x or y axis specified
		}

		if (position === start || other === start) {
			$media.style[start] = "0";
			return;
		}

		if (position === end || other === end) {
			$media.style[end] = "0";
			return;
		}

		if (position === "center" || position === "50%") {
			$media.style[start] = "50%";
			$media.style["margin-" + start] = (side / -2) + "px";
			return;
		}

		// Percentage values (e.g., 30% 10%)
		if (position.indexOf("%") >= 0) {
			position = parseInt(position);

			if (position < 50) {
				$media.style[start] = position + "%";
				$media.style["margin-" + start] = side * (position / -100) + "px";
			}
			else {
				position = 100 - position;
				$media.style[end] = position + "%";
				$media.style["margin-" + end] = side * (position / -100) + "px";
			}

			return;
		}
		// Length-based values (e.g. 10px / 10em)
		else {
			$media.style[start] = position;
		}

	};

  /**
   * Calculate & set object-fit
   *
   * @param {node} $media - img/video/picture element
   */
	var objectFit = function ($media) {
		// Fallbacks, IE 10- data
		var fit = ($media.dataset) ? $media.dataset.objectFit : $media.getAttribute("data-object-fit");
		var position = ($media.dataset) ? $media.dataset.objectPosition : $media.getAttribute("data-object-position");
		fit = fit || "cover";
		position = position || "50% 50%";

		// If necessary, make the parent container work with absolutely positioned elements
		var $container = $media.parentNode;
		checkParentContainer($container);

		// Check for any pre-set CSS which could mess up image calculations
		checkMediaProperties($media);

		// Mathematically figure out which side needs covering, and add CSS positioning & centering
		$media.style.position = "absolute";
		$media.style.height = "100%";
		$media.style.width = "auto";

		if (fit === "scale-down") {
			$media.style.height = "auto";

			if (
				$media.clientWidth < $container.clientWidth &&
				$media.clientHeight < $container.clientHeight
			) {
				setPosition("x", $media, position);
				setPosition("y", $media, position);
			}
			else {
				fit = "contain";
				$media.style.height = "100%";
			}
		}

		if (fit === "none") {
			$media.style.width = "auto";
			$media.style.height = "auto";
			setPosition("x", $media, position);
			setPosition("y", $media, position);
		}
		else if (
			fit === "cover" && $media.clientWidth > $container.clientWidth ||
			fit === "contain" && $media.clientWidth < $container.clientWidth
		) {
			$media.style.top = "0";
			$media.style.marginTop = "0";
			setPosition("x", $media, position);
		}
		else if (fit !== "scale-down") {
			$media.style.width = "100%";
			$media.style.height = "auto";
			$media.style.left = "0";
			$media.style.marginLeft = "0";
			setPosition("y", $media, position);
		}
	};

  /**
   * Initialize plugin
   *
   * @param {node} media - Optional specific DOM node(s) to be polyfilled
   */
	var objectFitPolyfill = function (media) {
		if (typeof media === "undefined") {
			// If left blank, all media on the page will be polyfilled.
			media = document.querySelectorAll("[data-object-fit]");
		} else if (media && media.nodeName) {
			// If it's a single node, wrap it in an array so it works.
			media = [media];
		} else if (typeof media === "object" && media.length && media[0].nodeName) {
			// If it's an array of DOM nodes (e.g. a jQuery selector), it's fine as-is.
			media = media;
		} else {
			// Otherwise, if it's invalid or an incorrect type, return false to let people know.
			return false;
		}

		for (var i = 0; i < media.length; i++) {
			if (!media[i].nodeName) { continue; }

			var mediaType = media[i].nodeName.toLowerCase();

			if (mediaType === "img" && !edgePartialSupport) {
				if (media[i].complete) {
					objectFit(media[i]);
				}
				else {
					media[i].addEventListener("load", function () {
						objectFit(this);
					});
				}
			}
			else if (mediaType === "video") {
				if (media[i].readyState > 0) {
					objectFit(media[i]);
				}
				else {
					media[i].addEventListener("loadedmetadata", function () {
						objectFit(this);
					});
				}
			}
		}

		return true;
	};

	document.addEventListener("DOMContentLoaded", function () {
		objectFitPolyfill();
	});
	window.addEventListener("resize", function () {
		objectFitPolyfill();
	});

	window.objectFitPolyfill = objectFitPolyfill;

})();
