<?php
/**
 * Greetings call to action - style - 1
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$opt_in = 'Privacy Policy';

if (isset( $ht_ctc_greetings) && isset( $ht_ctc_greetings['opt_in'])) {
  $opt_in = $ht_ctc_greetings['opt_in'];
}

$opt_id = (isset($opt_in_id)) ? $opt_in_id : 'ctc_opt';

?>
<div class="ctc_opt_in" style="display:none; text-align:center;">
    <div class="<?php echo $opt_id ?>" style="display:inline-flex;justify-content:center;align-items:center;padding:0 4px;">
        <input type="checkbox" name="" id="<?php echo $opt_id ?>" style="margin: 0 5px;">
        <label for="<?php echo $opt_id ?>"><?php echo $opt_in ?></label>
    </div>
</div>