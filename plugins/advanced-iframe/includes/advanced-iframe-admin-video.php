<?php
defined('_VALID_AI') or die('Direct Access to this location is not allowed.');
?>
  <br/>
  <div id="icon-options-general" class="icon_ai">
    <br/>
  </div> <h2 class="default-h2">
  <?php _e('Help/FAQ', 'advanced-iframe'); ?>       </h2>

<?php
aiPostboxOpen("id-help-video", "Video tutorials", $closedArray, "100%", " show-always");
echo '<p>';
_e('The videos are not included in the plugin directly as they will be extended soon on the website.', 'advanced-iframe');
echo '</p>';
?>
  <p>
    <a href="//www.advanced-iframe.com/advanced-iframe/advanced-iframe-video-tutorials" target="_blank" id="vid"
       class="button-primary"><?php _e('Go to the video tutorials', 'advanced-iframe'); ?></a>
  </p>
<?php
aiPostboxClose();
?>