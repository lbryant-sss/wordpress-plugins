<?php
defined('_VALID_AI') or die('Direct Access to this location is not allowed.');
/**
 *  In this file the iframe itself and the surrounding divs are created
 */

$filenamedir = dirname(__FILE__) . '/../../advanced-iframe-custom';

if ($debug_js === 'bottom' && !isset($_REQUEST['debugRendered'])) {
  $html .= '<div id="aiDebugDivTotal"><div id="aiDebugDivHeader">Advanced iframe debug console - l: local messages, r: remote messages</div><div id="aiDebugDiv">';
  include_once dirname(__FILE__) . '/advanced-iframe-admin-functions.php';
  $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
  $src_trim = trim($src);
  $result_check_all = ai_checkUrlStatus(array($src_trim), $agent);
  $result_check = $result_check_all[$src_trim];
  $html .= 'User agent: ' . esc_html($agent);
  $html .= '<p><strong>Headers of ' . $src . '</strong>:<br>';
  if (isset($result_check['header']) && is_array($result_check['header'])) {
    foreach ($result_check['header'] as $line) {
      if (!empty($line)) {
        $replace = array("\n", "\r");
        $html .= str_replace($replace, '', esc_html($line)) . '<br>';
      }
    }
  }
  $html .= ai_print_result($result_check);
  $html .= '</p></div></div>';

  $html .= '<script>
     console.defaultLog = console.log.bind(console);
     console.logs = [];
     console.log = function(){
		 console.defaultLog.apply(console, arguments);
		 console.logs.push(Array.from(arguments));
		 var consoleData = [].map.call(arguments, JSON.stringify);
		 consoleData += "";
		 consoleData = consoleData.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
		 consoleData = consoleData.replace(\'\"\', \'"\').replace(/\\\\/g,"");
		 var content = "<" + "p class=\'ai-debug-local\'> l: LOG: " + consoleData + "<" + "/" + "p>";
         jQuery("#aiDebugDiv").append(content);
	 }
	console.defaultWarn = console.warn.bind(console);
	console.warns = [];
	console.warn = function(){
		console.defaultWarn.apply(console, arguments);
		console.warns.push(Array.from(arguments));
        var consoleData = [].map.call(arguments, JSON.stringify);
		consoleData += "";
		consoleData = consoleData.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
		consoleData = consoleData.replace(\'\"\', \'"\').replace(/\\\\/g,"");
		var content = "<" + "p class=\'ai-debug-local\'> l: WARN: " + consoleData + "<" + "/" + "p>";
        jQuery("#aiDebugDiv").append(content);
	}
	console.defaultError = console.error.bind(console);
	console.errors = [];
	console.error = function(){
		console.defaultError.apply(console, arguments);
		console.errors.push(Array.from(arguments));
		var consoleData = [].map.call(arguments, JSON.stringify);
		consoleData += "";
		consoleData = consoleData.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
		consoleData = consoleData.replace(\'\"\', \'"\').replace(/\\\\/g,"");
		var content = "<" + "p class=\'ai-debug-error\'> l: ERROR: " + consoleData + "<" + "/" + "p>";
        jQuery("#aiDebugDiv").append(content);
	}
    window.onerror = function (msg, url, lineNo, columnNo, error) {
      var content = "<" + "p class=\'ai-debug-error\'> ERROR: " + msg + " - " + lineNo + ":" + columnNo  + "<" + "/" + "p>";
      jQuery("#aiDebugDiv").append(content);
      return false;
    };
    </script>
 ';
  $_REQUEST['debugRendered'] = true;
}


if (!empty($hide_content_until_iframe_color)) {
  // we print the div to hide everything directly when the plugin is evaluated.
  $hide_content_until_iframe_color_print = trim(str_replace('|keep', '', strtolower($hide_content_until_iframe_color)));
  $hide_content_until_content = '<div style="width:100%;height:100%;position:fixed;z-index:999999999;top:0;left:0;background-color:' . esc_html($hide_content_until_iframe_color_print) . ';" id="ai-div-hide-content-' . $id . '"><!-- hides the content ' . current_action() . ' --></div>';

  global $isAiGutenberg;
  if ($isAiGutenberg || is_admin() || current_action() == "parse_request") {
    // we do not render this in the editor mode as it can make the editor unusable.
  } else {
    echo $hide_content_until_content;
  }
}

if ($add_surrounding_p === 'true') {
  $html .= '<p class="ai-surrounding-p">';
}

if ($show_iframe_loader === 'true') {
  // div around
  $html .= '<div id="ai-div-container-' . $id . '">';
  $filenameloader = $filenamedir . '/loader.gif';
  if (file_exists($filenameloader)) {
    $loader_url = AIP_URL_CUSTOM . '/loader.gif';
    list($widthLoader, $heightLoader) = @getimagesize($loader_url);
    // get the size
    $html .= '<style>#ai img.img-loader-icon {';
    $html .= 'width: ' . $widthLoader . 'px;';
    $html .= 'height: ' . $heightLoader . 'px;';
    $html .= '}</style>';
  } else {
    $loader_url = AIP_IMGURL . '/loader.gif';
  }
  // div for the loader
  $html .= '<div id="ai-div-loader-' . $id . '"><img class="img-loader-icon" src="' . $loader_url . '" title="Loading" alt="Loading" /></div>';
}

if (!empty($hide_part_of_iframe)) {
  $html_hide = '';
  $rectangles = explode('|', $hide_part_of_iframe);

  $div_width = $this->addPx($width);
  if ($show_part_of_iframe === 'true') {
    $div_width = $this->addPx($show_part_of_iframe_width);
  }
  $html .= '<div id="wrapper-div-' . $id . '" class="ai-wrapper-div" style="position:relative;width:' . $div_width . '">';

  for ($hi = 0; $hi < count($rectangles); ++$hi) {
    $values = explode(',', $rectangles[$hi]);

    $num_values = count($values);
    if ($num_values === 6 || $num_values === 7 || $num_values === 8) {
      // add px or %
      $r_x = $this->addPx($values[0]);
      $r_y = $this->addPx($values[1]);
      $r_width = $this->addPx($values[2]);
      $r_height = $this->addPx($values[3]);
      $display_type = 'div';
      $hide_href = '';
      if ($num_values === 7 || $num_values === 8) {
        $display_type = 'a';
        $hrefValue = esc_html(trim($values[6]));
        if ($hrefValue === 'changeViewport') {
          $hide_href = ' href="javascript:setNewViewPort' . $id . '(0); "';
        } else {
          $hide_href = ' href="' . $hrefValue . '"';
        }
      }
      if ($num_values === 8) {
        $hide_href .= ' target="' . esc_html(trim($values[7])) . '"';
      }

      // bottom and right are extracted
      $x_style = 'left';
      $y_style = 'top';
      if ($r_x[0] === 'r') {
        $x_style = 'right';
        $r_x = substr($r_x, 1);
      }
      if ($r_y[0] === 'b') {
        $y_style = 'bottom';
        $r_y = substr($r_y, 1);
      }

      // $values[4] does also support a html file which also includes a shortcode
      // It is added with a $ after the color. html files needs to be in the custom folder.

      // hide and hide1000 creade click events on close + automatic hide at a specific time.
      $values_color = explode('$', $values[4]);
      $bg_color = $values_color[0];
      // file or shortcode
      $div_content = '<!-- -->';
      if (count($values_color) >= 2) {
        if (count($values_color) === 3) {
          $element = $values_color[1];
          $hide = $values_color[2];
        } else {
          if ($this->ai_startsWith($values_color[1], 'hide')) {
            $hide = $values_color[1];
            $element = "not_used";
          } else {
            $element = $values_color[1];
            $hide = "not_used";
          }
        }
        if ($element != 'not_used') {
          $filename = $filenamedir . '/hide_' . esc_html(trim($element)) . '.html';
          // load file and show a message if it does not exist
          if (file_exists($filename)) {
            $div_content = trim(file_get_contents($filename));
            // evaluate shortcodes
            $div_content = do_shortcode($div_content);
            $div_content = str_replace('{plugin_url}', AIP_URL , $div_content);
            $div_content = str_replace('{x_style}', $x_style, $div_content);
            $div_content = str_replace('{x_distance}', $r_x, $div_content);
            $div_content = str_replace('{y_style}', $y_style, $div_content);
            $div_content = str_replace('{y_distance}', $r_y, $div_content);
            $div_content = str_replace('{width}', $r_width, $div_content);
            $div_content = str_replace('{height}', $r_height, $div_content);
            $div_content = str_replace('{id}', $id, $div_content);
			 if ($fullscreen_button_style !== 'black') {
              switch ($fullscreen_button_style) {
                case 'black2':
                  $button_open = "fullscreen2_open.png";
                  $button_close = "fullscreen2_close.png";
                  break;
                case 'white':
                  $button_open = "fullscreen_open-white.png";
                  $button_close = "fullscreen_close-white.png";
                  break;
                case 'custom':
                  $button_open = "../../advanced-iframe-custom/fullscreen_open.png";
                  $button_close = "../../advanced-iframe-custom/fullscreen_close.png";
                  break;
                default:
                  // we keep the existing settings
              }
              $div_content = str_replace('fullscreen_open.png', $button_open, $div_content);
              $div_content = str_replace('fullscreen_close.png', $button_close, $div_content);
            }
          } else {
            $content = 'The file "' . $filename . '" cannot be found.';
          }
        }
        if ($hide != 'not_used') {
          $error = false;
          if ($this->ai_startsWith($hide, 'hide')) {
            $html_hide = '<script>';
            if ($hide === 'hide') {
              $html_hide .= 'jQuery("#wrapper-div-element-' . $id . '-' . $hi . '").on( "click" , function() { jQuery( this ).remove(); }); ';
            } else {
              $time_hide = substr($hide, 4);
              if (is_numeric($time_hide)) {
                $html_hide .= 'setTimeout(function() { jQuery( "#wrapper-div-element-' . $id . '-' . $hi . '").remove(); }, ' . $time_hide . ');';
              } else {
                $error = true;
              }
            }
            $html_hide .= '</script>';
            if ($hide === 'hide') {
              $html_hide .= '<style>';
              $html_hide .= '#wrapper-div-element-' . $id . '-' . $hi . ', #wrapper-div-element-' . $id . '-' . $hi . ' *:hover { cursor:pointer } ';
              $html_hide .= '</style>';
            }
          } else {
            $error = true;
          }
          if ($error) {
            $html = $error_css . '<div class="errordiv">' . __('ERROR: hide part of iframe only supports $hide or $hideXXX as last parameter.', 'advanced-iframe') . '</div>';
            return $html;
          }
        }
      }

      $zIndex = $values[5];
      $position = 'position:absolute';
      if ($this->ai_startsWith($zIndex, 'f')) {
        $position = 'position:fixed';
        $zIndex = substr($zIndex, 1);
      }

      // replace § with , for rbga !
      $bg_color = str_replace('§', ',', $bg_color);
      $html .= '<style>';
      $html .= '#wrapper-div-element-' . $id . '-' . $hi . ' {';
      $html .= $position . ';z-index:' . esc_html(trim($zIndex)) . ';' . $x_style . ':' . esc_html(trim($r_x)) . ';' . $y_style . ':' . esc_html(trim($r_y)) . ';width:' . $r_width . ';height:' . $r_height . ';background-color:' . esc_html(trim($bg_color)) . ';';
      $html .= '}</style>';
      $html .= '<' . $display_type . $hide_href . ' id="wrapper-div-element-' . $id . '-' . $hi . '">' . $div_content . '</' . $display_type . '>';
      $html .= $html_hide;
    } else {
      if ($hide_part_of_iframe === 'false') {
        // The plugin ignores false now. Too many users do not understand the error message and it only creates effortto explain every time. 
      } else {
        $html = $error_css . '<div class="errordiv">' . __('ERROR: hide part of iframe does not have the required parameters', 'advanced-iframe') . '</div>';
      }
      return $html;
    }
  }
}

if ($show_part_of_iframe === 'true') {
  $html .= '<div id="ai-div-' . $id . '">';
  if ($fixChrome65) {
    $html .= '<div id="ai-div-inner-' . $id . '">';
  }
}
if (!empty($iframe_zoom)) {
  $html .= '<div id="ai-zoom-div-' . $id . '">';
}
if ($enable_lazy_load === 'true') {
  $html .= '<div id="ai-lazy-load-' . $id . '" class="ai-lazy-load-' . $id . '"><script type="text/lazyload">';
}

if ($show_iframe_as_layer_div) {
  $html .= '<div id="ai-layer-div-' . $id . '"';
  $html .= ' style="' . $layer_div_style . '">';

  $layer_header_html = '';
  if (!empty($show_iframe_as_layer_header_file)) {
    $filename = $filenamedir . '/layer_' . esc_html(trim($show_iframe_as_layer_header_file)) . '.html';

    // load file and show a message if it does not exist
    if (file_exists($filename)) {
      $content = trim(file_get_contents($filename));
      // evaluate shortcodes
      $content = str_replace('{id}', $id, $content);
      $content = str_replace('{src}', trim($src), $content);
      $content = do_shortcode($content);
    } else {
      $content = 'The header file "' . $filename . '" cannot be found.';
    }
    $layer_header_html .= '<div class="header-div" style="height:' . esc_html($this->addPx($show_iframe_as_layer_header_height)) . ';margin:0px !important;padding:0px !important;width:100%;">';
    $layer_header_html .= $content;
    $layer_header_html .= '</div>';

    if ($show_iframe_as_layer_header_position === 'top') {
      $html .= $layer_header_html;
    }
    if ($show_iframe_as_layer_div_header) {
      $html .= '<div class="header-div-scroll" style="' . $layer_div_header_style . '" >';
    }

  }
}

if ($this->ai_startsWith(strtolower($src), "http://") && AdvancedIframeHelper::isSecure()) {
  // show a warning if https pages are shown in http pages.
  $html .= 'Http iframes are not shown in https pages in many major browsers. Please read <a href="//www.advanced-iframe.com/iframe-do-not-mix-http-and-https" target="_blank">this post</a> for details.';
} elseif ($this->ai_startsWith(strtolower($src), "https:") && !AdvancedIframeHelper::isSecure() &&
  $enable_external_height_workaround === "true" && $use_post_message === 'false') {
  $html .= 'You use a https iframe in a http page with the external workaround. To enable the external workaround you NEED to enable "Use postMessage for communication" on the "external workaround" tab.';
}

if ($debug_js === 'bottom') {
  $sep = (strpos($src, '?') === false) ? '?' : "&amp;";
  $src .= $sep . 'send_console_log=true';
} elseif (isset($_GET['aiEnableDebugConsole']) && $_GET['aiEnableDebugConsole'] === 'false') {
  $sep = (strpos($src, '?') === false) ? '?' : "&amp;";
  $src .= $sep . 'send_console_log=false';
}

if ($src_hide != '') {
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  $hash_url = str_replace(array('+', '/', '='), array('', '', ''), base64_encode(md5(($src . $id . microtime() . $ip . mt_rand()), true)));
  $srcHideArray = explode('|', $src_hide);
  $srcHide = $srcHideArray[0];

  if (count($srcHideArray) === 2 && $srcHideArray[1] === 'db') {
    $time_trans = ($srcHide === '0') ? 3600 : intval($srcHide);
    set_transient('aip_' . $hash_url, $src . '|' . $srcHide, $time_trans);
    $src = get_site_url() . "/?aiUrl=" . $hash_url;
  } else {
    AdvancedIframeHelper::aiUpdateOption('aip_' . $hash_url, base64_encode($src), intval($srcHide));
     $src = AIP_URL . "includes/iframe.php?aiUrl=" . $hash_url;
  }
}

$html .= '<iframe id="' . $id . '" ';
if (!empty ($name)) {
  $html .= ' name="' . esc_html(trim($name)) . '" ';
}
$html .= ' src="' . esc_url(trim($src)) . '" ';
if ($width != 'not set' && $width != '') {
  $html .= ' width="' . esc_html(trim($width)) . '" ';
  // html5 style to support vw and vh and we only add it if not present.
  if (strpos($style, 'width:') === false) {
    $style .= ';width:' . esc_html(trim($this->addPx($width))) . ';';
  }
}
if ($height != 'not set' && $height != '') {
  $html .= ' height="' . esc_html(trim($height)) . '" ';
  // html5 style to support vw and vh and we only add it if not present.
  if (strpos($style, 'height:') === false) {
    $style .= 'height:' . esc_html(trim($this->addPx($height))) . ';';
  }
}

// default is auto - enables to add scrolling with css!
if ($scrolling != 'none') {
  $html .= ' scrolling="' . esc_html(trim($scrolling)) . '" ';
}
if (!empty ($marginwidth)) {
  $html .= ' marginwidth="' . esc_html(trim($marginwidth)) . '" ';
}
if (!empty ($marginheight)) {
  $html .= ' marginheight="' . esc_html(trim($marginheight)) . '" ';
}
if ($frameborder != '') {
  $html .= ' frameborder="' . esc_html(trim($frameborder)) . '" ';
  if ($frameborder === "0") {
    $html .= ' border="0" ';
  }
}
if (!empty ($transparency)) {
  $html .= ' allowtransparency="' . esc_html(trim($transparency)) . '" ';
}
if (!empty($loading) && $loading !== "false" && $enable_lazy_load === "false") {
  $html .= ' loading="' . esc_html(trim($loading)) . '" ';
}

if (!empty ($class)) {
  $html .= ' class="' . esc_html(trim($class)) . '" ';
}
if (!empty ($sandbox)) {
  if (trim($sandbox) != '') {
    if (trim($sandbox) === 'sandbox') {
      $html .= ' sandbox ';
    } else {
      $html .= ' sandbox="' . esc_html(trim($sandbox)) . '" ';
    }
  }
}

if (!empty ($referrerpolicy)) {
  $html .= ' referrerpolicy="' . esc_html(trim($referrerpolicy)) . '" ';
}

if (!empty ($custom)) {
  $html .= ' ' . str_replace("'", '"', $custom) . ' ';
}

if (!empty ($title)) {
  $html .= ' title="' . esc_html(trim($title)) . '" ';
}

if (!empty ($allow)) {
  $html .= ' allow="' . esc_html(trim($allow)) . '" ';
}

$addIosFixInCss = false;
if (strpos($style, 'max-width') === false) {
  if ($show_part_of_iframe === 'true') {
    $style .= ';max-width:none;';
  } elseif ($enable_responsive_iframe === 'true') {
    // width:1px;min-width:100%; fix for IOS
    $style .= ';width:1px;min-width:100%;max-width:100%;';
    if ($auto_zoom === 'false') {
      $addIosFixInCss = true;
    }
  }
}
$html .= ' style="' . esc_html(trim($style)) . '" ';


if ($allowfullscreen != 'false') {
  $html .= ' allowfullscreen ';
}

// create onload string
$onload_str = '';
if (!empty ($onload)) {
  $onload = str_replace("'", '"', $onload);
  $onload_str .= esc_html($onload);
}


if (!empty ($tab_hidden)) {
  $split_hidden_array = explode(',', $tab_hidden);
  $hidden_counter = 0;
  foreach ($split_hidden_array as $split_hidden) {
    if ($hidden_counter++ === 0) {
      // measure the width of the surrounding element
      if (!empty ($tab_visible)) {
        $onload_str .= ';jQuery("' . $split_hidden . '").css("width",jQuery("' . $tab_visible . '").width());';
      }
      $onload_str .= ';jQuery("' . $split_hidden . '").css("position", "absolute").css("top", "-20000px").css("visibility", "hidden").show();';
    } else {
      $onload_str .= ';jQuery("' . $split_hidden . '").show();';
    }
  }
}

if ($show_iframe_loader === 'true') {
  $onload_str .= ';jQuery("#ai-div-loader-' . $id . '").hide();';
}
if ($show_iframe_loader_layer === 'true') {
  $onload_str .= ';jQuery("#ai-div-loader-global").hide();';
}

if (!empty($hide_content_until_iframe_color)) {
  if (stripos($hide_content_until_iframe_color, '|keep') === false) {
    $onload_str .= ';jQuery("#ai-div-hide-content-' . $id . '").hide();';
  }
}

if ($show_part_of_iframe === 'true' && (!empty ($show_part_of_iframe_new_window) ||
    !empty ($show_part_of_iframe_new_url) || !empty ($show_part_of_iframe_next_viewports) ||
    ($show_part_of_iframe_next_viewports_hide === 'true'))) {
  $onload_str .= ';modifyOnLoad' . $id . '();';
}

if (!empty($onload_resize_delay)) {
  $onload_str .= ';setTimeout(function() { ';
}

if ($auto_zoom === 'same') {
  $onload_str .= ';zoomOnLoad' . $id . '();';
}

if ($hideiframehtml != '') {
  $onload_str .= ';aiModifyIframe_' . $id . '();';
}

if (!empty($onload_show_element_only)) {
  $onload_str .= ';aiShowElementOnly("#' . $id . '","' . $onload_show_element_only . '");';
}
if ($onload_resize === 'true') {
  $iframeObject = 'this';
  if (!empty($onload_resize_delay)) {
    $iframeObject = 'ifrm_' . $id;
  }
  $onload_str .= ';aiResizeIframe(' . $iframeObject . ', "' . $onload_resize_width . '","' . $resize_min_height . '");';
}

if (!empty($onload_resize_delay)) {
  $onload_str .= '},' . $onload_resize_delay . ');';
}

if (!empty($iframe_height_ratio)) {
  $onload_str .= ';aiResizeIframeRatio(this, "' . $iframe_height_ratio . '");';
}

if ($onload_scroll_top === 'true' || $onload_scroll_top === 'iframe') {
  $onload_str .= ';aiScrollToTop("' . $id . '","' . $onload_scroll_top . '");';
}
// hide_page_until_loaded
if ($hide_page_until_loaded === 'true' && $show_iframe_as_layer === 'false') {
  $onload_str .= 'jQuery("#' . $id . '").css("visibility", "visible");';
  if (!empty($hide_part_of_iframe)) {
    $onload_str .= 'jQuery("#wrapper-div-' . $id . '").css("visibility", "visible");';
  }
  $onload_str .= 'ai_hide_iframe_loading_' . $id . '(this);';
}

if (!empty($resize_on_element_resize)) {
  $onload_str .= 'initResizeIframe' . $id . '();';
}

$htmlJs = '';
if ($add_iframe_url_as_param === 'same') {
  $changeUrlParam = 'aiChangeUrlParam(aigetIframeLocation("' . $id . '"), "' . $map_parameter_to_url . '","' . $src_orig . '","' . $add_iframe_url_as_param_prefix . '",' . $add_iframe_url_as_param_direct . ');';
  $onload_str .= $changeUrlParam;
  $htmlJs = AdvancediFramePrepareJs::aiConfigureHistroyChange($changeUrlParam, $id);
}

if ($use_iframe_title_for_parent === 'same') {
  $onload_str .= 'aiChangeTitle("' . $id . '");';
}

if ($src_hide !== '') {
  $onload_str .= 'aiDisableRightClick("' . $id . '");';
}

if ($onload_str != '') {
  $html .= " onload='" . esc_js(trim($onload_str, ";")) . "' ";
}

$html .= '></iframe>';

$html .= $htmlJs;
if ($show_iframe_as_layer_div) {
  if ($show_iframe_as_layer_div_header) {
    $html .= '</div>';
  }
  if ($show_iframe_as_layer_header_position === 'bottom') {
    $html .= $layer_header_html;
  }
  $html .= '</div>';
}
if ($enable_lazy_load === 'true') {
  $html .= '</script></div>';
}
if (!empty($iframe_zoom)) {
  $html .= '</div>';
}

if ($show_part_of_iframe === 'true') {
  if ($fixChrome65) {
    $html .= '</div>';
  }
  $html .= '</div>';
}
if (!empty($hide_part_of_iframe)) {
  $html .= '</div>';
}
if ($show_iframe_loader === 'true') {
  $html .= '</div>';
}
if ($add_surrounding_p === 'true') {
  $html .= '</p>';
}

return "";
?>