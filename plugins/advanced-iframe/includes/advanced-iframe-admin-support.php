<?php
defined('_VALID_AI') or die('Direct Access to this location is not allowed.');

aiPostboxOpen("id-help-support", "Get support", $closedArray, "100%", " show-always");
?>
  <p>
    <?php _e('The basic settings are the settings a normal iframe solution does offer. They don\'t require any specific html, css or programming know-how.', 'advanced-iframe'); ?>
  </p>
  <p>
    <?php _e('The advanced options do modify the styles of the parent page, the iframe, do some Javascript magic when the iframe is loaded or include content directly to your page. Understanding this is not basic Wordpress know-how and therefore you can get help here if you want. I do offer paid support for this plugin now.', 'advanced-iframe'); ?>
  </p>

<?php _e('<p>What do you get?</p><ul><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Free check if you can include the content the way YOU like.</li><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Fast and reliable setup of what you want.</li><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- You only pay if it works!</li></ul>', 'advanced-iframe'); ?>

  <p>
    <?php _e('This offer is only available for Advanced iFrame Pro users.<br/>If you are interested please visit <a id="as" target="_blank" href="//www.advanced-iframe.com/advanced-iframe-support/">www.advanced-iframe.com/advanced-iframe-support/</a> for more information.', 'advanced-iframe'); ?>
  </p>
<?php
aiPostboxClose();
?>