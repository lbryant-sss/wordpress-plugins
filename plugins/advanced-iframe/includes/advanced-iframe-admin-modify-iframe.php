<?php
defined('_VALID_AI') or die('Direct Access to this location is not allowed.');

aiPostboxOpen("id-advanced-part", "Show only a part of the iframe", $closedArray);

if ($evanto || $isDemo) { ?>
  <p>

  <?php _e('You can only show a part of the iframe. This solution DOES WORK across domains without any hacks! This is a solution that works only with css by placing a window over the iframe which does a clipping. All areas of the iframe that are not inside the window cannot be seen. Please specify the upper left corner coordinates x and y and the height and width that should be shown. Specify a fixed height and width in the iframe options at the top for optimal results! I recommend to make the iframe itself that big that no scrollbars do exist anymore. Otherwise scrolling e.g. with the mouse wheel on some browsers is possible. Simply select the area you want to show with the graphical area selector! You can even zoom the selected area that it fits properly e.g. on a mobile phone. Please go to the <a target="_blank" href="//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/show-only-a-part-of-the-iframe">pro demo</a> for some working examples. Please also check the additional 5 options. These are the advanced features to handle changes in the iframe.<p>Also media queries are supported! This enables you to show different areas depending on the browser width. Please see <a  target="_blank" href="//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/show-only-a-part-of-the-iframe#e55">example 55</a> for a working demo.</p>', 'advanced-iframe');

  echo '<p><input id="s" class="button-primary" type="button" name="update_iframe-loader" onclick="aiOpenSelectorWindow(\'' . plugins_url() . '/'.$aiSlug.'/includes/advanced-iframe-area-selector.html\');" value="';
  _e('Open the area selector', 'advanced-iframe');
  echo '" /><a href="#" id="ai-selector-help-link">Show me an image how the settings are used.</a></p>';

  echo '<div id="ai-selector-help"><img alt="" src="' . plugins_url() . '/'.$aiSlug.'/img/help-area-selector.gif"></div>';


  echo '<table class="form-table">';
  printTrueFalse(true, $devOptions, __('Show only part of the iframe', 'advanced-iframe'), 'show_part_of_iframe', __('Show only part of the iframe. You have to enable this to use all the options below. Please read the text above. Shortcode attribute: show_part_of_iframe="true" or show_part_of_iframe="false" ', 'advanced-iframe'), 'false', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/show-only-a-part-of-the-iframe', false);
  printNumberInput(true, $devOptions, __('i-20-Upper left corner x', 'advanced-iframe'), 'show_part_of_iframe_x', __('Specifies the x coordinate of the upper left corner of the view window. Enter the x-offset from the left border of your external iframe page you want to show. Shortcode attribute: show_part_of_iframe_x="". <a href="#" class="ai-selector-help-link-move">Show me an image how this settings is used.</a>', 'advanced-iframe'));
  printNumberInput(true, $devOptions, __('i-20-Upper left corner y (top distance)', 'advanced-iframe'), 'show_part_of_iframe_y', __('Specifies the y coordinate of the upper left corner.  Enter the y-offset from the top border of your external iframe page you want to show. Shortcode attribute: show_part_of_iframe_y="". <a href="#" class="ai-selector-help-link-move">Show me an image how this settings is used.</a>', 'advanced-iframe'));
  printNumberInput(true, $devOptions, __('i-20-Width of the visible content', 'advanced-iframe'), 'show_part_of_iframe_width', __('Specifies the width of the content in pixel that should be shown. Shortcode attribute: show_part_of_iframe_width="". <a href="#" class="ai-selector-help-link-move">Show me an image how this settings is used.</a>', 'advanced-iframe'));
  printNumberInput(true, $devOptions, __('i-20-Height of the visible content', 'advanced-iframe'), 'show_part_of_iframe_height', __('Specifies the height of the content in pixel that should be shown. Shortcode attribute: show_part_of_iframe_height="". <a href="#" class="ai-selector-help-link-move">Show me an image how this settings is used.</a>', 'advanced-iframe'));

  printtMediaQuery(true, $devOptions, __('i-20-Media queries', 'advanced-iframe'), 'show_part_of_iframe_media_query', __('If you don\'t have the rights to use auto height you can use <a href="https://www.w3schools.com/css/css_rwd_mediaqueries.asp" target="_blank">media queries</a> to show a different viewport depending on the width of the browser. This is pretty cool and can be configured by pressing the "Add breakpoint" link. There you can define the x, y, width, height and iframe width for a certain browser width (breakpoint). See the descriptions before for a description of this values. You always define the upper width of a breakpoint. So if you want to optimize your iframe for mobile, table, desktop you can define 2 view ports. You can also only enter one value you want to change. Now the interesting part is coming. You cannot simply set the width of the iframe itself to 100% to have it responsive because it will than have the same size as the viewport. The plugin will set the width to the breakpoint width when you reach this value or the iframe width if you define one. You can use the area selector by entering the breakpoint or iframe with for this breakpoint as width and select the new area. Please check the example for a full explanation. If you want to define this in a shortcode you have to define x,y,width,height,iframe witdh and a breakpoint separated by |. e.g. show_part_of_iframe_media_query="80|100|800|600|1024,100|40|600|400|800". If you don\'t want to change a setting simply leave this part empty. Please note that the breakpoints will be ordered automatically from big to small to work properly! Zoom does also work! So together with zoom this solution is the best one possible for mobile devices if you don\'t have access to the other page.', 'advanced-iframe'), '', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/show-only-a-part-of-the-iframe#e55');

  printTrueFalse(true, $devOptions, __('i-20-Enable horizontal scrollbar', 'advanced-iframe'), 'show_part_of_iframe_allow_scrollbar_horizontal', __('By default you specify a fixed area you want to show from the external page. Settings this to "true" will show a horizontal scrollbar if needed. Shortcode attribute: show_part_of_iframe_allow_scrollbar_horizontal="true" or show_part_of_iframe_allow_scrollbar_horizontal="false". <a href="#" class="ai-selector-help-link-move">Show me an image how this settings is used.</a>', 'advanced-iframe'), 'false');
  printTrueFalse(true, $devOptions, __('i-20-Enable vertical scrollbar', 'advanced-iframe'), 'show_part_of_iframe_allow_scrollbar_vertical', __('By default you specify a fixed area you want to show from the external page. Settings this to "true" will show a vertical scrollbar if needed. Shortcode attribute: show_part_of_iframe_allow_scrollbar_vertical="true" or show_part_of_iframe_allow_scrollbar_vertical="false". <a href="#" class="ai-selector-help-link-move">Show me an image how this settings is used.</a>', 'advanced-iframe'), 'false');
  printTextInput(true, $devOptions, __('i-20-View port style', 'advanced-iframe'), 'show_part_of_iframe_style', __('Show part of an iframe does create an additional div which is the element you can style here. If you e.g. want to add a border you can add css here directly. e.g. use "border: 2px solid #ff0000;". Using the style, border or class in the default settings do not work as they are all related to the iframe directly! If you also using zoom or features like "Hide a part of the iframe" or the iframe loader AND you want to center the iframe you need the "old" &lt;center&gt;[advanced-iframe ....]&lt;/center&gt; If you want to apply styles to other elements that are added dynamically please use the options on "Modify the parent page". Shortcode attribute:  show_part_of_iframe_style=""', 'advanced-iframe'));

  printTrueFalseFull($devOptions, __('i-20-Enable auto zoom', 'advanced-iframe'), 'show_part_of_iframe_zoom', __('This zoom setting enables you to zoom the view port automatically to the available space. The difference to the normal zoom options is that the whole selected area is zoomed and not the content of the iframe only. This zoom works like the "Auto zoom by ratio" but you don\'t have to specify a ratio as the height and the width is already known from the settings above. This feature does check the size of the div around the view port and calculates the needed zoom factor and offsets. Therefore you have to select a fixed view port (e.g. width:500) because otherwise the calculated zoom would always be 1. If you select "Yes" the zoom does only shrink the view port which is normally the best choice because looks good on desktop and is shown smaller on mobile devices. If you select "Full" the view port is also enlarged. Also the feature "Hide/cover parts of the iframe" is supported. So if you place e.g. a colored div over a certain area to hide it it is also zoomed Shortcode attribute: show_part_of_iframe_zoom="true", show_part_of_iframe_zoom="false" or show_part_of_iframe_zoom="full"', 'advanced-iframe'), '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/show-only-a-part-of-the-iframe/show-only-a-part-of-an-iframe-zoom');
  echo '</table>';


  echo '<p>';
  _e('With the following 5 options you can do something when the page in the iframe does change. The parent page does only know the url of the iframe that is loaded initially. This is a browser restriction when the pages are not on the same domain. The parent only can find out when the page inside does change. But it does not know to which url. So the options below rely on a counting of the onload event. But for certain solutions (e.g. show only the login part of a page and then open the result page as parent) this will work.', 'advanced-iframe');
  echo '</p><table class="form-table">';

  printTextInput(true, $devOptions, __('i-20-Change the view port when iframe changes to the next step', 'advanced-iframe'), 'show_part_of_iframe_next_viewports', __('You can define different view ports when the page inside the iframe does change and a onload event is fired. Each time this event is fired a different view port is shown. A view port is defined the following way: left,top,width,height e.g. 50,100,500,600. You can define several view ports (if you e.g. have a straight  work flow) by separating the view ports by ; e.g. 50,100,500,600;10,40,200,400. Each view port has its own class: ai-viewport-X. X is the number of the view port starting with 0! You can e.g. enable scroll for specific view ports with this setting. Shortcode attribute:  show_part_of_iframe_next_viewports=""', 'advanced-iframe'));
  printTrueFalse(true, $devOptions, __('i-20-Restart the view ports from the beginning after the last step.', 'advanced-iframe'), 'show_part_of_iframe_next_viewports_loop', __('If you define different view ports it could make sense always to use them in a loop. E.g. if you have an image gallery where you have an overview with view port 1 and a detail page with view port 2. And you can only can come from the overview to the detail page and back. Shortcode attribute: show_part_of_iframe_next_viewports_loop="true" or show_part_of_iframe_next_viewports_loop="false" ', 'advanced-iframe'));
  printTextInput(true, $devOptions, __('i-20-Open iFrame in new window after the last step', 'advanced-iframe'), 'show_part_of_iframe_new_window', __('You can define if the iframe is opened in a new tab/window or as full window. the options you can use are "_top" = as full window, "_blank" = new tab/window or you leave it blank to stay in the iframe. Because of the browser restriction not the current url of the iframe can be loaded. It is either the initial one or the one you specify in the next setting. Shortcode attribute: show_part_of_iframe_new_window="", show_part_of_iframe_new_window="_top" or show_part_of_iframe_new_window="_blank" ', 'advanced-iframe'));
  printTextInput(true, $devOptions, __('i-20-Url that is opened after the last step', 'advanced-iframe'), 'show_part_of_iframe_new_url', __('You can define the url that is loaded after the last step. This enables you to jump to a certain page after your work flow. This is useful with the above. Shortcode attribute: show_part_of_iframe_new_url="" ', 'advanced-iframe'));
  printTrueFalse(true, $devOptions, __('i-20-Hide the iframe after the last step', 'advanced-iframe'), 'show_part_of_iframe_next_viewports_hide', __('Hides the iframe after the last step completely. Shortcode attribute: show_part_of_iframe_next_viewports_hide="true" or show_part_of_iframe_next_viewports_hide="false" ', 'advanced-iframe'));

  echo '</table>'; ?>

<?php } else { ?>
  <p>
    <?php _e('This feature is only available in the Pro version where you have the option to show only a part of the iframe even when the content you want to include is on a different domain. Please note that there is still no way to modify anything on the remote site.', 'advanced-iframe') ?>
  </p>
<?php }

aiPostboxClose();

if ($evanto || $isDemo) {
  aiPostboxOpen("id-advanced-hide", "Hide/cover parts of the iframe / Fullscreen button.", $closedArray);
  ?>
  <p>
    <?php _e('Please note: This is an advanced setting! You need to know basic html/css to use all possibilities of this feature! You can define an area which will be hidden by a rectangle you define. This can e.g. be used to hide a logo.', 'advanced-iframe'); ?>
  </p>
  <?php
  echo '<p><input id="s" class="button-primary" type="button" name="update_iframe-loader" onclick="aiOpenSelectorWindow(\'' . plugins_url() . '/'.$aiSlug.'/includes/advanced-iframe-area-selector.html?hide_feature=true\');" value="';
  _e('Open the area selector in the hide parts mode', 'advanced-iframe');
  echo '" /></p>';
  ?>

  <table class="form-table">
    <?php
    if ($evanto || $isDemo) {
      printTextInput(true, $devOptions, __('Hide/cover parts of the iframe. Make an iframe read only', 'advanced-iframe'), 'hide_part_of_iframe', __('A rectangle over the iframe is defined the following way: left,top,width,height,color,z-index e.g. 10,20,200,50,#ffffff,10. This defines a rectangle in white with the z-index of 10. z-index means the layer the rectangle is placed. If you don\'t see your rectangle please use a higher z-index. You can define a background image! use e.g. 10,20,200,50,#ffffff;background-image:url(your-logo.gif);background-repeat:no-repeat;,10 for a white rectangle with the given background image.</p><p class="description">Use the area selector above to get the coordinates. You can specify several rectangles by separating them by |. Please see the <a target="_blank" href="//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/hide-a-part-of-the-iframe#e8">pro demo</a> for a cool example where a logo is exchanged.<p class="description"><strong>Read only iframes</strong> can be created by using hide_part_of_iframe="0,0,100%,100%,transparent,10". For a working example please see example 21 of the pro demo.</p><p class="description">A <strong>link and a target</strong> can be defined. Parameter 7 is the URL and parameter 8 the target. So a working example would be: hide_part_of_iframe="0,0,100%,100%,transparent,10,https://www.advanced-iframe.com,_blank". Instead of the URL you can also use "changeViewport". Together with "Show only a part of the iframe" and $hide (see below) this makes it possible to generate a read only iframe which does change the viewport after a click and enable the iframe again.</p><p class="description">The divs can be <strong>right and bottom aligned</strong> by specifying the prefix r for right and b for bottom. An example would look like this: r10,b20,200,50,#ffffff,10.</p><p class="description"><strong>Media queries</strong> can be used! This enables to hides areas depending on the browser width. Please see <a  target="_blank" href="//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/hide-a-part-of-the-iframe#e50">example 50</a> for a working demo.</p><p class="description">The usage of an <strong>external html files</strong> even with shortcodes are supported. Below you see the existing external files, how to use them and also create, edit and delete them.</p><p class="description">You can <strong>hide the divs by click or after a given time</strong>. Add $hide or $hideXXXX after the color where XXXX is the time in ms. So $hide3000 hides the div after 3 seconds. This setting can be added together with a file. E.g. #ffffff$hide or #ffffff$file$hide3000.</p><p class="description"><strong>Semi transparent areas</strong> can be defined with a rgba color. You need to replace , with &sect;. E.g. rgba(1&sect;1&sect;1&sect;0.5).</p><p class="description">If this feature is used together with a <strong>fullscreen iframe</strong> you need to add f in front of the z-index. e.g. f9001. Then position:fixed is used instead of position:absolute for the divs.</p><p class="description">Shortcode attribute: hide_part_of_iframe=""</p>', 'advanced-iframe'), 'text', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/hide-a-part-of-the-iframe', false);

      printFullscreenButton($devOptions, __('Add a full screen button over the iframe', 'advanced-iframe'), 'fullscreen_button', __('This feature uses the "Hide/cover parts of the iframe" above. It shows a div with a fullscreen button like youtube videos.  The "+ scrollbar" setting does add 30px on the right instead of 10px so the button is not over the scrollbar iframe. The file hide_fullscreen.html from the custom folder "advanced-iframe-custom" is loaded. Please note that there was a change of a parameter in 2025.1. Please delete the file once. It is regenerated with the new parameter plugin_url automatically, when you enter the administration. The file includes the fullscreen buttons and the styles for it. If you like to use different additional logic: Modify the hide_fullscreen.html or copy to e.g. hide_fullscreen2.html, add your custom logic and enter the settings directly at "Hide/cover parts of the iframe" with e.g. r10,10,32,32,transparent$fullscreen2,auto above. In the file are placeholders like "x_style", which are replaced on the fly and also the images of the buttons. Below at "Full screen button style" you can set different styles for the buttons. This feature does not work together with "Show only a part of an iframe". Shortcode attribute: fullscreen_button="top", fullscreen_button="top_scroll", fullscreen_button="bottom", fullscreen_button="bottom_scroll", fullscreen_button="top_left", fullscreen_button="bottom_left" fullscreen_button="false"', 'advanced-iframe'), $url = '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/full-screen-demo', $default = 'false');
      printTrueFalse(true, $devOptions, __('i-20-Full screen without any browser elements', 'advanced-iframe'), 'fullscreen_button_full', __('Setting this to "Yes" will make the iframe full screen without any address bar, menu, tabs and window elements. This setting is global for all iframes of one page! The last shortcode does set this setting. Shortcode attribute: fullscreen_button_full="true", fullscreen_button_full="false" ', 'advanced-iframe'), 'false', 'https://www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/full-screen-demo/full-screen-demo-no-browser-elements', false);

      printFullscreenButtonStyle($devOptions, __('i-20-Full screen button style', 'advanced-iframe'), 'fullscreen_button_style', __('Selects the style for the fullscreen button. If you select "custom" you need to copy your buttons with a size of 32x32 for the open and the close button to "advanced-iframe-custom/fullscreen_open.png" and "advanced-iframe-custom/fullscreen_close.png". For different sizes please see "Add a full screen button over the iframe". Shortcode attribute: fullscreen_button_style="black", fullscreen_button_style="black2", fullscreen_button_style="white", fullscreen_button_style="custom"', 'advanced-iframe'), $url = 'https://www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/full-screen-demo/full-screen-demo-no-browser-elements');

      printTextInput(true, $devOptions, __('i-20-Hide additional elements at full screen', 'advanced-iframe'), 'fullscreen_button_hide_elements', __('The full screen button adds a very high z-index to the iframe to shown it over all elements. Depending on the HTML/css of your template, it is possible that some elements, e.g. a sticky header, are still shown. You can use any valid <a class="jquery-help-link" href="#">jQuery selector pattern</a> to hide this elements! A css class is added, that will be used when the fullscreen button is pressed to hide this elements and also to shown them on close again. You can define several elements separated by , e.g. header,#admin-bar. Shortcode attribute: fullscreen_button_hide_elements=""', 'advanced-iframe'), 'text', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/full-screen-demo', false);
    }
    ?>
  </table>

  <?php if ($evanto || $isDemo) { ?>
    <div class="hide-print"><h4>
        <?php _e('Existing external div files', 'advanced-iframe') ?></h4>
      <p>
        <?php _e('You can show custom html inside the div you create. This makes it possible to show whatever you like over the other iframe. Also shortcodes are supported in the external file. You can use this file by attaching the id of the file after the color settings separated by a $. So 10,20,200,50,#ffffff$123,10 has to be used if your id is 123 (file name = hide_123.html)', 'advanced-iframe') ?>
      </p>
      <p>
        <?php _e('The following files in the folder "advanced-iframe-custom" with the pattern hide_*.html exist. You can view/edit this files with the plugin editor of Wordpress by clicking on the "Edit/View" link.', 'advanced-iframe') ?>
      </p>
      <?php
      $config_files = array();
      foreach (glob(dirname(__FILE__) . '/../../advanced-iframe-custom/hide_*.html') as $filename) {
        $base = basename($filename);
        $base_url1 = site_url() . '/wp-admin/plugin-editor.php?file=advanced-iframe-custom%2F';
        $base_url2 = '';
        $config_files[] = $base;
      }
      echo "<hr height=1>";
      if (empty($config_files)) {
        echo "<ul><li>";
        _e('No custom external div files found.', 'advanced-iframe');
        echo "</li></ul>";
      } else {
        foreach ($config_files as $file) {
          echo '<div class="config-file-block"><div class="ai-external-config-label"><span class="config-list">' . $file . '</span> &nbsp; <a href="' . $base_url1 . $file . $base_url2 . '">';
          _e('Edit/View', 'advanced-iframe');
          echo '</a>';
          $rid = substr(basename($file, '.html'), 5);
          echo ' &nbsp; <a class="confirmation-file post" href="admin.php?page=advanced-iframe&remove-custom-hide-id=' . $rid . '">';
          _e('Delete', 'advanced-iframe');
          echo '</a></div>';
          echo '<br /></div>';
        }
      }
      echo "<hr height=1>";
      ?>
      <p>
        <?php _e('Create a custom external div file. Only specify the id. All files are named "hide_{id}.html":', 'advanced-iframe') ?>
        <br/>
        <input name="ai_custom_hide_id" id="ai_custom_hide_id" type="text" size="20" maxlength="20"/>
        <input id="chf" class="button-primary" type="submit" name="create-custom-hide-id"
               value="<?php _e('Create external div file', 'advanced-iframe') ?>"/>
      </p>
    </div>
  <?php }

  aiPostboxClose();
}
aiPostboxOpen("id-advanced-modify-iframe", "Modify the iframe", $closedArray);
?>

  <h3 id="modifycontent"><?php _e('Modify the content of the iframe if the iframe page is on the same domain', 'advanced-iframe') ?><?php
    if ($evanto || $isDemo) {
      _e(' or if you can use the external workaround. ', 'advanced-iframe');
    } else {
      echo '.';
    }
    ?>

  </h3>
<?php
if ($evanto || $isDemo) {
  echo '<p>';
  _e('<strong>If you use the external wokaround add enable_external_height_workaround="true" to your shortcode!</strong> This is needed to disable the settings with the ', 'advanced-iframe');
  echo renderExternalWorkaroundIcon(true);
  _e(' for the same domain.', 'advanced-iframe');
  echo '</p>';
}
?>
  <p>
    <?php _e('With the following options you can modify the content of the iframe. <strong>IMPORTANT</strong>: This is only possible if the iframe comes from the <strong>same domain</strong> because of the <a href="https://en.wikipedia.org/wiki/Same_origin_policy" target="_blank">same origin policy</a> of Javascript.<p>If you can use the "<a id="external-workaround-link" href="#xss">External workaround</a>", you can also use this setting in the pro version.</p><p>Please read the section "<a class="howto-id-link" href="#">How to find the id and the attributes</a>" above how to find the right styles. If the content comes from a different domain you have to modify the iframe page by e.g. adding a Javascript function that is then called by the onload function you can set above or you add a parameter in the url that you can read in the iframe and display the page differently then. You should also use the external workaround to modify the iframe if your page loads quite slow and you see the modifications on subsequent pages. The reason is that the direct modification can only be done after the page is loaded and the "Hide until loaded" is only working for the 1st page. The external workaround is able to hide the iframe until it is modified always and also css can be added to the header directly.', 'advanced-iframe'); ?>
  </p>
  <table class="form-table">
    <?php
    if ($evanto || $isDemo) {
      printTrueFalse(true, $devOptions, __('Add css class to iframe elements', 'advanced-iframe'), 'add_css_class_iframe', __('Sometimes it is not possible to modify existing css classes in the iframe because they are also used somewhere else or there is no unique selector for this element. Also it is sometimes needed that each iframe page do need a different unique selector. Setting this attribute to true causes that in the iframe an unique is created from the iframe url and is added as class to the body and his children. Then you are also able to e.g. hide a element on one page and show it on another page. Shortcode attribute: add_css_class_iframe="true" or add_css_class_iframe="false" ', 'advanced-iframe'), 'false', '', true);
    }


    printTextInput(false, $devOptions, __('Hide elements in iframe', 'advanced-iframe'), 'iframe_hide_elements', __('This setting allows you to hide elements inside the iframe. This can be used to hide e.g. a div or a heading. Please read the section "<a class="howto-id-link" href="#">How to find the id and the attributes</a>" to find the right id or class. Usage: If you want to hide a div you have to enter a hash (#) followed by the id e.g. #header. If you want to hide a heading which is a &lt;h2&gt; you have to enter h2. You can define several elements separated by , e.g. #header,h2. I recommend using firebug to find the elements and the ids. You can use any valid <a class="jquery-help-link" href="#">jQuery selector pattern</a> here! Also the width and height of the elements are set to 0 because e.g. auto height or auto zoom could have problems measuring! Shortcode attribute: iframe_hide_elements=""', 'advanced-iframe'), 'text', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/external-workaround-auto-height-and-css-modifications', $evanto || $isDemo);
    printTextInput(false, $devOptions, __('Show only one element', 'advanced-iframe'), 'onload_show_element_only', __('You can define which part of the page should be shown in the iframe. You can define the id (e.g. #id) or the class (.class) which should be shown. <strong>Be aware that all other elements below the body are removed!</strong>. Many webpages do NOT work fully anymore (sometimes do not show anymore) if you change the page structure! So if your Javascript/css relies on a certain structure you e.g. have to add additional css by "Content id in iframe" below.<br>I recommend to use the "Hide elements in iframe" if you have problems here. There the structure stays like before. Only elements are hidden! Often a background is defined for the header which you can remove below e.g. by setting background-image: none; in the body. This can be done at "Content id in iframe" and "Content styles in iframe" below. Shortcode attribute: onload_show_element_only=""', 'advanced-iframe'), 'text', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/external-workaround-auto-height-and-css-modifications#e7', $evanto || $isDemo);
    echo '</table>';
    echo '<p id="id-modify-css-iframe">';
    _e('With the next 3 options you can modify the css of your iframe if <strong>it is on the same domain</strong> or if you can use the external workaround and have the pro version. The first 2 settings are saved to the ai_external.js. The first option defines the id/class/element you want to modify and at the 2nd option you define the styles you want to change. If you use "Add css styles to iframe" the css is written directly to the iframe. <strong>Please use either the first 2 options OR the 3rd one!</strong> Depending on your website solution 1 or 2 can be the better choice.', 'advanced-iframe');
    echo '</p><table class="form-table">';

    printTextInput(false, $devOptions, __('Content id in iframe', 'advanced-iframe'), 'iframe_content_id', __('Set the id of the element starting with a hash (#) that defines element you want to modify the css.  You can use any valid <a class="jquery-help-link" href="#">jQuery selector pattern</a> here! Please read the section "<a class="howto-id-link" href="#">How to find the id and the attributes</a>" to find the right id or class. In the field below you then define the style you want to overwrite. You can also define more than one element. Please separate them by | and provide the styles below. Please read the note below how to find this id for other templates. #content|h2 means that you want to set a new style for the div content and the heading h2 below. Shortcode attribute: iframe_content_id=""', 'advanced-iframe'), 'text', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/external-workaround-auto-height-and-css-modifications', $evanto || $isDemo);
    printTextInput(false, $devOptions, __('Content styles in iframe', 'advanced-iframe'), 'iframe_content_styles', __('Define the styles that have to be overwritten to enable the full width. Most of the time you have to modify some of the following attributes: width, margin-left, margin-right, padding-left. Please use ; as separator between styles. If you have defined more than one element above (Content id in iframe) please separate the different style sets with |. The default values are: Wordpress default: \'width:450px;padding-left:45px;\'. Twenty Ten: \'margin-left:20px;margin-right:240px\'. iNove: \'width:605px\'. Please read the note below how to find these styles for other templates. If you have defined #content|h2 at the Content id you can e.g. set \'width:650px;padding-left:25px;|padding-left:15px;\'. Shortcode attribute: iframe_content_styles=""', 'advanced-iframe'), 'text', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/external-workaround-auto-height-and-css-modifications', $evanto || $isDemo);

    printTextInput(true, $devOptions, __('Add css styles to iframe', 'advanced-iframe'), 'iframe_content_css', __('This setting does add the css you enter here directly as last element to the body of the iframe page. The big difference to the two settings before is, that not the css styles are modified by Javascript but a style element is added directly to the iframe. The advantage is that also !important can be used to overwrite such styles. This setting is only supported for the <strong>same domain</strong>. The disadvantage is that adding the style element is still done after the iframe is fully loaded and that writing valid css is a little bit more complicated. Use "Write css directly" for the external workaround. Enter the styles without &lt;style&gt;. E.g. iframe_content_css="body { margin: 0 !important; }". The value is sanitized at the output! Therefore not all styles do work! e.g. body &gt; p cannot be used. Use external files if you need this. Shortcode attribute: iframe_content_css=""', 'advanced-iframe'), 'text', '', false);
    ?>
  </table>
<?php
if ($evanto || $isDemo) {
  echo '<p id="id-modify-target">';
  _e('With the next 3 options you can modify the target and hrefs of links in your iframe if <strong>it is on the same domain or if you can use the external workaround and have the pro version. This settings are save to the ai_external.js.</strong>.', 'advanced-iframe');
  echo '</p><table class="form-table">';

  printTextInput(false, $devOptions, __('Change iframe links/forms selector', 'advanced-iframe'), 'change_iframe_links', __('Change links of the iframe page to open the url at a different target or a different href. This option does add the attribute target="your target" to the links you define or replaces parts of the url. The targets and urls are defined in the next settings. You can use any valid <a class="jquery-help-link" href="#">jQuery selector pattern</a> here! Please read the section "<a class="howto-id-link" href="#">How to find the id and the attributes</a>" to find the right id or class. So if your link e.g. has an id="link1" you have to use "a#link1". If you want to change all links e.g. in the div with the id="menu-div" you have to use "#menu-div a". The <a class="jquery-help-link" href="#">jQuery selector pattern</a> help also shows how to identify all external links! Because brackets [ ... ] are replaced in the short code by Wordpress it has to be replaced with {{ ... }}. Also the target of a form can be changed. So using "form" will change the target of all forms. You can also define more than one element. Please separate them with |. Shortcode attribute: change_iframe_links=""', 'advanced-iframe'), 'text', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/change-links-targets', true);
  printTextInput(true, $devOptions, __('Change iframe links/forms target value', 'advanced-iframe'), 'change_iframe_links_target', __('Here you define the targets for the links you define in the setting before. If you have defined more than one element above (Change iframe links) please separate the different targets with |. E.g. "_blank|_top". If you enter "_blank" then also rel="noopener" is added for security reasons.  Shortcode attribute: change_iframe_links_target=""', 'advanced-iframe'), 'text', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/change-links-targets', true);
  printTextInput(true, $devOptions, __('Change iframe links href value', 'advanced-iframe'), 'change_iframe_links_href', __('Here you can change the href or part of the href for links you define in the setting for the selector. You have to separate the before and after string with ~. If you have defined more than one element above (Change iframe links) please separate the different href patterns with |. E.g. "www.test.com~www.test2.com|prefix.~" will change www.test.com to www.test2.com and for the other link prefix. will be replaced by a empty string and therefore removed. If you want to append a string to existing links you can use append as the before part. e.g. append~?conf=1 will append ?conf=1 to all links defined in the selector. This setting does also work for elements which are later added to the website by ajax! Shortcode attribute: change_iframe_links_href=""', 'advanced-iframe'), 'text', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/change-links-targets', true);

  echo '</table>';
}
aiPostboxClose();
?>