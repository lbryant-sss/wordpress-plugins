<?php
defined('_VALID_AI') or die('Direct Access to this location is not allowed.');

aiPostboxOpen("id-help-communication", "Communication with window.postMessage or iframe", $closedArray, "100%", " show-always");
?>
  <p>
    <?php _e('There are two ways how the page in the iframe can communicate with the parent to send e.g. the height:
      
      <ol>
        <li><a href="https://developer.mozilla.org/en-US/docs/Web/API/Window/postMessage" target="_blank">window.postMessage</a> - Pro version only. Now the default for new installations.</li>
        <li>hidden iframe - <a href="#" onclick="jQuery(\'#details-workaround\').show(); return false;" >Show me more infos how the iframe communication way works.</a></li>
      </ol>
      ', 'advanced-iframe'); ?>

    <?php _e('
      <div id="details-workaround" >If the parent page (the page where the iframe is) and the iframe page (the page which is inside the iframe) are NOT on the same domain it is only possible to do the above stuff by including an additional iframe to the remote page which than can call a script on the parent domain that can then access the functions there. The following steps are needed:
      <ol>
        <li>The parent page has a Javascript function that resizes the iframe</li>
        <li>The external iframe page has an additional hidden iframe, an onload attribute at the body and a javascript function</li>
        <li>A page on the parent domain does exist that is included by the hidden iframe that calls the function on the parent page</li>
      </ol></div>', 'advanced-iframe');

    _e('<p>Using window.postMessage has the following advantages/disadvantages</p>
    <ul>
     <li> + The external workaround does also work when https pages are included to http pages! (See example 53)</li>
     <li> + You can include the same iframe page into several parents much easier!</li>
     <li> + Additional data can be transferred as no browser url restrictions do apply (See example 52)</li>
     <li> + No additional hidden iframe has to be added to the page.</li>
     <li> - Cannot be seen in the network tab</li>
    </ul>
    
    <p>
     One advantage of the iframe communication is that by default you see the callbacks to height.html in the network. So is was always easy to debug out of the box. window.postMessage by default is not visible like that. If you have any problems with window.postMessage select "Debug" at "Use postMessage communication" and log information about the transferred data is printed to the browser console. Use F12 at your browser to open the developer tools.
    </p>
    
    <p>When the first version plugin was planned the percentage of browsers that not supported window.postMessage was ~20%. But now this has changed. The latest browser statistics show that IE <= 8 browsers have dropped to zero now. The new default is now window.postMessage for new pro installations.
    </p><p>Existing installations can switch to window.postMessage by changing this in the administration.
    ', 'advanced-iframe'); ?>

  </p>
<?php
aiPostboxClose();
?>