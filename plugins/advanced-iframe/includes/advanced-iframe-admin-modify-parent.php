<?php
defined('_VALID_AI') or die('Direct Access to this location is not allowed.');

aiPostboxOpen("id-advanced-modify-parent", "Modify the parent page", $closedArray);
?>
<p>
  <?php _e('With the following options you can modify your template on the fly to give the iframe more space! At most templates you would have to create a page template with a special css and this is quite complicated. By using the options below your template is modified on the fly by jQuery. With onload_resize_delay this modifications can also be delayed if the elements are loaded dynamically. Please also look at "Add css styles to parent" because there the css is directly written to the page. For more details read the documentation there.', 'advanced-iframe'); ?>
</p>
<table class="form-table">
  <?php
  printTextInput(false, $devOptions, __('Hide elements', 'advanced-iframe'), 'hide_elements', __('This setting allows you to hide elements when the iframe is shown. This can be used to hide the sidebar or the heading. Please read the section "<a class="howto-id-link" href="#">How to find the id and the attributes</a>" to find the right id or class. Usage: If you want to hide a div you have to enter a hash (#) followed by the id e.g. #sidebar. If you want to hide a heading which is a &lt;h2&gt; you have to enter h2. You can define several elements separated by , e.g. #sidebar,h2. This gives you a lot more space to show the content of the iframe. To get the id of the sidebar go to Appearance -> Editor -> Click on \'Sidebar\' on the right side. Then look for the first \'div\' you find. The id of this div is the one you need. For some common templates the id is e.g. #menu, #sidebar, or #primary. For Twenty Ten and iNove you can remove the sidebar directly: Page attributes -> Template -> no sidebar. Wordpress default: \'#sidebar\'. I recommend using firebug (see below) to find the elements and the ids. You can use any valid <a class="jquery-help-link" href="#">jQuery selector pattern</a> here! Shortcode attribute: hide_elements=""', 'advanced-iframe'));
  echo '</table><p>';
  _e('With the next 2 options you can modify the css of your parent page. The first option defines the id/class/element you want to modify and at the 2nd option you define the styles you want to change.', 'advanced-iframe');
  echo '</p><table class="form-table">';
  printTextInput(false, $devOptions, __('Content id', 'advanced-iframe'), 'content_id', __('Some templates do not use the full width for their content and even most \'One column, no sidebar Page Template\' templates only remove the sidebar but do not change the content width. Set the e.g. id of the div starting with a hash (#) that defines the content.  You can use any valid <a class="jquery-help-link" href="#">jQuery selector pattern</a> here! Please read the section "<a class="howto-id-link" href="#">How to find the id and the attributes</a>" to find the right id or class. In the field below you then define the style you want to overwrite. For Twenty Ten and WordPress Default the id is #content, for iNove it is #main. You can also define more than one element. Please separate them with | and provide the styles below. Please read the note below how to find this id for other templates. #content|h2 means that you want to set a new style for the div content and the heading h2 below. Shortcode attribute: content_id=""', 'advanced-iframe'));
  printTextInput(false, $devOptions, __('Content styles', 'advanced-iframe'), 'content_styles', __('Define the styles that have to be overwritten to enable the full width. Most of the time you have to modify some of the following attributes: width, margin-left, margin-right, padding-left. Please use ; as separator between styles. If you have defined more than one element above (Content id) please separate the different style sets with |. The default values are: Wordpress default: \'width:450px;padding-left:45px;\'. Twenty Ten: \'margin-left:20px;margin-right:240px\'. iNove: \'width:605px\'. Read the note below how to find these styles for other templates. If you have defined #content|h2 at the Content id you can e.g. set \'width:650px;padding-left:25px;|padding-left:15px;\'. Shortcode attribute: content_styles=""', 'advanced-iframe'));

  printTextInput(false, $devOptions, __('Add css styles to parent', 'advanced-iframe'), 'parent_content_css', __('This setting does add the css you enter here directly where the plugin is written to the page. The difference to the settings before is, that the modification is not done by jQuery but the css is directly written to the parent. The advantage is that also !important can be used to overwrite such styles and that the modifications is not done after the whole page is loaded. You can also use this setting to configure "Hide elements" directly. The disadvantage is that the styles added where the plugin is written and do not overwrite the style rendered later and that writing valid css is a little bit more complicated. Enter the styles without &lt;style&gt;. The value is sanitized at the output! Therefore not all styles do work! e.g. body &gt; p cannot be used. Use external files if you need this. Shortcode attribute: parent_content_css=""', 'iframe_advanced-iframe'), 'text', '', false);
  if ($evanto || $isDemo) {
    printTrueFalse(true, $devOptions, __('Add css class to parent elements', 'advanced-iframe'), 'add_css_class_parent', __('Sometimes it is not possible to modify existing css classes of the parent because they are also used somewhere else or there is no unique selector for this element. Setting this attribute to true causes that a new class is added at each parent of the iframe up to the body! If the element has an id the class is named "ai-class-(id)". Otherwise "ai-class-(number)" is added. Then it is easy to identify all parent elements of the iframe and modify them. If you have several iframes on one page the classes could not be unique anymore. You need to set "Include ai.js in the footer" to false if you want to use this! Shortcode attribute: add_css_class_parent="true" or add_css_class_parent="false" ', 'advanced-iframe'));
  }
  echo '</table>';

  aiPostboxClose();

  if ($evanto || $isDemo) {

    aiPostboxOpen("id-advanced-layer", "Open iframe as layer/popup - Change parent links target", $closedArray);
    ?>
    <p>
      <?php _e('With the following options you can modify the links on your page that they are opened in a new tab or in a popup. So users will not leave your page when they click on them. Additionally you can add a header/footer to the page. It is also possible that you add this functionality to all pages.  ', 'advanced-iframe'); ?>
    </p>
    <table class="form-table">
      <?php
      printTextInput(true, $devOptions, __('Change parent links target', 'advanced-iframe'), 'change_parent_links_target', __('Change links of the parent page to open the url inside the iframe. This option does add the attribute target="your id" to the links you define. You can use any valid <a class="jquery-help-link" href="#">jQuery selector pattern</a> here! Please read the section "<a class="howto-id-link" href="#">How to find the id and the attributes</a>" to find the right id or class. So if your link e.g. has an id="link1" you have to use "a#link1". If you want to change all links e.g. in the div with the id="menu-div" you have to use "#menu-div a". You can also define more than one element. Please separate them with ,. Because brackets [ ... ] are replaced in the short code by Wordpress it has to be replaced with {{ ... }}.<br>If you like that links you have changed the target get bold when you click on it add |bold at the end. If you like to apply your own css class add |bold:your-css-class. e.g. "a#link1|bold:ai-bold". Shortcode attribute: change_parent_links_target=""', 'advanced-iframe'), 'text', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/change-links-targets#e10');
      printTrueExternalFalse($devOptions, __('i-20-Show iframe as layer/popup - supports auto open', 'advanced-iframe'), 'show_iframe_as_layer', __('If you enable this, the iframe is initially hidden. If you click on a link defined at "Change parent links target" the iframe is shown as a lightbox on the page. If you use this for external links the user does not leave your page! "External" does simply open all links that are not on the same domain in a layer/popup. The setting at "Change parent links target" is ignored than.<br>You can also open the iframe automatically. You have 2 ways to do this. <span>Add |autoclick to the "Change parent links target" setting after the selector you like to open. If your selector is very general like "a" then the first one is used. In this case add a 2nd selector which is more specific. Like a,a#id-of-the-link|autoclick.</span><span>The second option is to specify the id of the link you like to open as hash. So if the id of your link is linkToOpen then add #linkToOpen to the URL of the parent. Make sure you add this link also to "Change parent links target". Only links that have the iframe as target are opened!</span><br>This setting does overwrite some iframe settings like height, width and border! Show part of iframe, lazy load, hide part of iframe and iframe loader are disabled as they do not work with this feature.  Shortcode attribute: show_iframe_as_layer="true", show_iframe_as_layer="external", show_iframe_as_layer="false" ', 'advanced-iframe'), '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/show-the-iframe-as-layer#e34');
      printTrueOriginalFalse($devOptions, __('i-40-Show layer 100% or original', 'advanced-iframe'), 'show_iframe_as_layer_full', __('Show the layer with 100% ("Yes") or 96% ("No"). Original does mean that the size you set for the iframe is set as max width/height of the layer and 96% if the parent is smaller than your height/width Shortcode attribute: show_iframe_as_layer_full="true", show_iframe_as_layer_full="false", show_iframe_as_layer_full="original" ', 'advanced-iframe'));

      printNumberInput(true, $devOptions, __('i-40-Auto click delay', 'advanced-iframe'), 'show_iframe_as_layer_autoclick_delay', __('The delay in ms the iframe is opened when auto click is enabled. Read "Show iframe as layer" how to enable auto open. Shortcode attribute: show_iframe_as_layer_autoclick_delay=""', 'advanced-iframe'), 'text', '0', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/auto-open-layer-popup');
      printNumberInput(true, $devOptions, __('i-40-Auto click hide time', 'advanced-iframe'), 'show_iframe_as_layer_autoclick_hide_time', __('Defines the number of days auto click is disabled after opened once. If you set the value to 0 then the iframe is opened every time. You can define the time in days the iframe is not opened. 1 = one day. 365 = one year. This info is stored in a cookie starting with ai_disable_autoclick_iframe with the expiration time you define. Shortcode attribute: show_iframe_as_layer_autoclick_hide_time=""', 'advanced-iframe'), 'text', '0', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/auto-open-layer-popup');

      printTextInput(true, $devOptions, __('i-40-Layer file id', 'advanced-iframe'), 'show_iframe_as_layer_header_file', __('You can add an additional header/footer with custom html above or below the iframe in the layer. Header/Footer files need to be in the folder plugins/advanced-iframe-custom with the following naming convention: layer_{id}.html. The id has to be saved in this text field. Below you see the existing header/footer files and also you can create/edit/delete them. The content of this file is included into a div at the given position. You need to provide the height of your additional content in the next setting. Shortcodes in your custom file are supported! The placeholder {id} is replaced by the id of your iframe. This can be used to reuse a layer file where e.g.  different images depending on the iframe should be shown. The id can only contain alphanumeric characters, - and _ . The placeholder {src} is replaced by the src of your iframe. This can be used to create a link like: "Go to this page".  Shortcode attribute: show_iframe_as_layer_header_file=""', 'advanced-iframe'), 'text', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/show-the-iframe-as-layer#e37');
      printNumberInput(true, $devOptions, __('i-40-Layer header/footer height', 'advanced-iframe'), 'show_iframe_as_layer_header_height', __('The height of the additional layer. The height is needed to calculate the height of the iframe properly. Shortcode attribute: show_iframe_as_layer_header_height=""', 'advanced-iframe'));
      printTopBottom($devOptions, __('i-40-Layer header position', 'advanced-iframe'), 'show_iframe_as_layer_header_position', __('Show the additional area above or below the iframe. Shortcode attribute: show_iframe_as_layer_header_position="top" or show_iframe_as_layer_header_position="bottom" ', 'advanced-iframe'), 'top', '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/show-the-iframe-as-layer#e38');
      printTrueFalse(true, $devOptions, __('i-40-Keep the content after closing', 'advanced-iframe'), 'show_iframe_as_layer_keep_content', __('To improve performance the content of an iframe is not loaded again if the same opening link is clicked again. It is only hidden and shown then. But sometimes it makes sense to unload the content if e.g. sound should be stopped or it is mandatory that the iframe shows the first page again. Shortcode attribute: show_iframe_as_layer_keep_content="true" or show_iframe_as_layer_keep_content="false" ', 'advanced-iframe'));

      printTrueFalse(true, $devOptions, __('Insert an iframe on all pages', 'advanced-iframe'), 'add_ai_to_all_pages', __('Enabling this includes an iframe to all pages of your site before the end of the body. The config from the administration is used. This makes especially sense with the "Show iframe as layer" with the setting "External". Then all external links of your page are opened in an iframe you define above. It can also be used e.g. for a global chat bot, basket, overlay.... This setting cannot be set by a shortcode.', 'advanced-iframe'));
      ?>
    </table>
    <?php if ($evanto || $isDemo) { ?>
      <div class="hide-print"><h4>
          <?php _e('Existing additional layer files', 'advanced-iframe') ?></h4>
        <p>
          <?php _e('The following additional layer files in the folder "advanced-iframe-custom" currently exist. Please note that you can view/edit this files with the plugin editor of Wordpress by clicking on the "Edit/View" link.', 'advanced-iframe') ?>
        </p>
        <?php
        $config_files = array();
        foreach (glob(dirname(__FILE__) . '/../../advanced-iframe-custom/layer_*.html') as $filename) {
          $base = basename($filename);
          $base_url1 = site_url() . '/wp-admin/plugin-editor.php?file=advanced-iframe-custom%2F';
          $base_url2 = '';
          $config_files[] = $base;
        }
        echo "<hr height=1>";
        if (empty($config_files)) {
          echo "<ul><li>";
          _e('No custom additional header files found.', 'advanced-iframe');
          echo "</li></ul>";
        } else {
          foreach ($config_files as $file) {
            echo '<div class="config-file-block"><div class="ai-external-config-label"><span class="config-list">' . $file . '</span> &nbsp; <a href="' . $base_url1 . $file . $base_url2 . '">';
            _e('Edit/View', 'advanced-iframe');
            echo '</a>';
            $rid = substr(basename($file, '.html'), 6);
            echo ' &nbsp; <a class="confirmation-file post" href="admin.php?page=advanced-iframe&remove-custom-header-id=' . $rid . '">';
            _e('Delete', 'advanced-iframe');
            echo '</a></div>';
            echo '<br /></div>';
          }
        }
        echo "<hr height=1>";
        ?>
        <p>
          <?php _e('Create a custom layer file. Only specify the id. All files are named "layer_{id}.html":', 'advanced-iframe') ?>
          <br/>
          <input name="ai_custom_header_id" id="ai_custom_header_id" type="text" size="20" maxlength="20"/>
          <input id="chf" class="button-primary" type="submit" name="create-custom-header-id"
                 value="<?php _e('Create custom layer file', 'advanced-iframe') ?>"/>
        </p>
      </div>
    <?php }
    aiPostboxClose();
  }
  ?>