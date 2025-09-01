<?php

$totals = array();

if( $total_savings ){
	$totals['savings'] = array(
		'{{data.footer.savingLabel}}', wc_price( $total_savings ), 'less'
	);
}

$totals = array_merge( $totals, array(
	'subtotal' 		=> array(
		'{{data.footer.subtotalLabel}}', wc_price( 100 ), 'add'
	),
) );




?>

<div class="xoo-wsc-ft-totals">

	<?php foreach ($totals as $key => $data ): ?>
		<# if( data.footer.totals.<?php echo $key ?> ){ #>
			<div class="xoo-wsc-ft-amt xoo-wsc-ft-amt-<?php echo $key ?> xoo-wsc-<?php echo $data[2] ?>">
				<span class="xoo-wsc-ft-amt-label"><?php echo $data[0] ?></span>
				<span class="xoo-wsc-ft-amt-value"><?php echo $data[1] ?></span>
			</div>
		<# } #>
	<?php endforeach; ?>

</div>
