<?php
/*
 +---------------------------------------------------------------------+
 | NinjaFirewall (WP Edition)                                          |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +---------------------------------------------------------------------+
 | This program is free software: you can redistribute it and/or       |
 | modify it under the terms of the GNU General Public License as      |
 | published by the Free Software Foundation, either version 3 of      |
 | the License, or (at your option) any later version.                 |
 |                                                                     |
 | This program is distributed in the hope that it will be useful,     |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of      |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the       |
 | GNU General Public License for more details.                        |
 +---------------------------------------------------------------------+ i18n+ / sa / 2
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

nf_not_allowed( 'block', __LINE__ );

$nfw_options = nfw_get_option( 'nfw_options' );

$tz = get_option('timezone_string');
if (! empty( $tz ) ) {
	date_default_timezone_set( $tz );
}

$log_dir = NFW_LOG_DIR . '/nfwlog/';
$monthly_log = 'firewall_' . date( 'Y-m' ) . '.php';

if ( ! is_file( $log_dir . $monthly_log ) ) {
	nf_sub_log_create( $log_dir . $monthly_log );
}

if (! is_writable( $log_dir . $monthly_log ) ) {
	$write_err = sprintf( __('the current month log (%s) is not writable. Please chmod it and its parent directory to 0777', 'ninjafirewall'), htmlspecialchars( $log_dir . $monthly_log ) );
} elseif (! is_writable( $log_dir ) ) {
	$write_err = sprintf( __('the log directory (%s) is not writable. Please chmod it to 0777', 'ninjafirewall'), htmlspecialchars($log_dir ) );
}

global $available_logs;
$available_logs = nf_sub_log_find_local( $log_dir );

if (! empty( $_POST['nfw_act'] ) ) {
	// Save public key:
	if ( $_POST['nfw_act'] == 'pubkey' ) {
		if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'clogs_pubkey') ) {
			wp_nonce_ays('clogs_pubkey');
		}
		if (isset( $_POST['delete_pubkey'] ) ) {
			$_POST['nfw_options']['clogs_pubkey'] = '';
			$ok_msg = __('Your public key has been deleted', 'ninjafirewall');
		} else {
			$ok_msg = __('Your public key has been saved', 'ninjafirewall');
		}
		nf_sub_log_save_pubkey( $nfw_options );
	// Save log options:
	} elseif ( $_POST['nfw_act'] == 'save_options' ) {
		nf_sub_log_save_options( $nfw_options );
		$ok_msg = __('Your changes have been saved.', 'ninjafirewall');
	}
	// Update options:
	$nfw_options = nfw_get_option( 'nfw_options' );
}

$max_lines = 1500;

if ( isset( $_GET['nfw_logname'] ) ) {
	if ( empty( $_GET['nfwnonce'] ) || ! wp_verify_nonce($_GET['nfwnonce'], 'log_select') ) {
		wp_nonce_ays('log_select');
	}
	$data = nf_sub_log_read_local( $_GET['nfw_logname'], $log_dir, $max_lines-1 );
}

if ( isset( $_GET['nfw_logname'] ) && ! empty( $available_logs[$_GET['nfw_logname']] ) ) {
	$selected_log = $_GET['nfw_logname'];
} else {
	$selected_log = $monthly_log;
	$data = nf_sub_log_read_local( $monthly_log, $log_dir, $max_lines-1 );
}

// Display a one-time notice after two weeks of use:
nfw_rate_notice( $nfw_options );

if ( ! empty( $write_err ) ) {
	echo '<div class="error notice is-dismissible"><p>' . __('Error', 'ninjafirewall') . ': ' . $write_err . '</p></div>';
}

if ( ! empty( $ok_msg ) ) {
	echo '<div class="updated notice is-dismissible"><p>' . $ok_msg . '</p></div>';
}
if ( isset( $data['lines'] ) && $data['lines'] > $max_lines ) {
	echo '<div class="notice-info notice is-dismissible"><p>' . __('Note', 'ninjafirewall') . ': ' . sprintf( __('your log has %s lines. I will display the last %s lines only.', 'ninjafirewall'), $data['lines'], $max_lines ) . '</p></div>';
}


echo '<center>' . __('Viewing:', 'ninjafirewall') . ' <select onChange=\'window.location="?page=nfsublog&nfwnonce='. wp_create_nonce('log_select') .'&nfw_logname=" + this.value;\'>';
foreach ($available_logs as $log_name => $tmp) {
	echo '<option value="' . $log_name . '"';
	if ( $selected_log == $log_name ) {
		echo ' selected';
	}
	$log_stat = stat($log_dir . $log_name);
	echo '>' . str_replace('.php', '', $log_name) . ' (' . number_format_i18n($log_stat['size']) .' '. __('bytes', 'ninjafirewall') . ')</option>';
}
echo '</select></center>';

$levels = array( '', 'MEDIUM', 'HIGH', 'CRITICAL', 'ERROR', 'UPLOAD', 'INFO', 'DEBUG_ON' );

$logline = '';
if ( isset( $data['log'] ) && is_array( $data['log'] ) ) {
	foreach ( $data['log'] as $line ) {
		if ( preg_match( '/^\[(\d{10})\]\s+\[.+?\]\s+\[(.+?)\]\s+\[(#\d{7})\]\s+\[(\d+)\]\s+\[(\d)\]\s+\[([\d.:a-fA-Fx, ]+?)\]\s+\[.+?\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[(hex:|b64:)?(.+)\]$/', $line, $match ) ) {
			if ( empty( $match[4]) ) { $match[4] = '-'; }
			if ( $match[10] == 'hex:' ) { $match[11] = @pack('H*', $match[11]); }
			if ( $match[10] == 'b64:' ) { $match[11] = base64_decode( $match[11]); }
			$res = date( 'd/M/y H:i:s', $match[1] ) . '  ' . $match[3] . '  ' .
			str_pad( $levels[$match[5]], 8 , ' ', STR_PAD_RIGHT) .'  ' .
			str_pad( $match[4], 4 , ' ', STR_PAD_LEFT) . '  ' . str_pad( $match[6], 15, ' ', STR_PAD_RIGHT) . '  ' .
			$match[7] . ' ' . $match[8] . ' - ' .	$match[9] . ' - [' . $match[11] . '] - ' . $match[2];
			$logline .= htmlentities( $res ."\n" );
		}
	}
}
if ( defined('NFW_TEXTAREA_HEIGHT') ) {
	$th = (int) NFW_TEXTAREA_HEIGHT;
} else {
	$th = '450';
}
?>
<form name="frmlog">
	<table class="form-table">
		<tr>
			<td width="100%">
				<textarea name="txtlog" class="large-text code" style="height:<?php echo $th; ?>px;" wrap="off" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><?php
				if ( ! empty( $logline ) ) {
					echo '       DATE         INCIDENT  LEVEL     RULE     IP            REQUEST' . "\n";
					echo $logline;
				} else {
					if (! empty( $data['err_msg'] ) ) {
						echo "\n\n > {$data['err_msg']}";
					} else {
						echo "\n\n > " . __('The selected log is empty.', 'ninjafirewall');
					}
				}
				?></textarea>
				<center>
					<p class="description"><?php _e('The log shows all threats that were blocked by the firewall, unless stated otherwise. It is rotated monthly.', 'ninjafirewall') ?></p>
				</center>
			</td>
		</tr>
	</table>
</form>
<?php

if ( empty( $nfw_options['auto_del_log'] ) ) {
	$nfw_options['auto_del_log'] = 0;
}

?>
<h3><?php _e('Log Options', 'ninjafirewall') ?></h3>
<form method="post" action="?page=nfsublog"><?php wp_nonce_field('log_save', 'nfwnonce', 0); ?>
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med"><?php _e('Auto-delete log', 'ninjafirewall') ?></th>
			<td>
			<?php
				$input = '<input type="number" name="nfw_options[auto_del_log]" min="0" value="'. (int) $nfw_options['auto_del_log'] .'" class="small-text" />';
				printf( __('Automatically delete logs older than %s days', 'ninjafirewall' ), $input );
			?>
			<p class="description"><?php _e('Set this option to 0 to disable it.', 'ninjafirewall' ) ?></p>
			</td>
		</tr>
	</table>
	<br />
	<input type="hidden" name="nfw_act" value="save_options" />
	<input type="submit" class="button-primary" value="<?php _e('Save Log Options', 'ninjafirewall') ?>" name="savelog" />
	<input type="hidden" name="tab" value="firewalllog" />
</form>

<a name="clogs"></a>
<form name="frmlog2" method="post" action="?page=nfsublog" onsubmit="return nfwjs_check_key();">
	<?php

	wp_nonce_field('clogs_pubkey', 'nfwnonce', 0);
	if ( empty( $nfw_options['clogs_pubkey'] ) || ! preg_match( '/^[a-f0-9]{40}:(?:[a-f0-9:.]{3,39}|\*)$/', $nfw_options['clogs_pubkey'] ) ) {
		$nfw_options['clogs_pubkey'] = '';
	}

	?>
	<br />

	<a name="clogs"></a>
	<h3><?php _e('Centralized Logging', 'ninjafirewall') ?></h3>
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med"><?php _e('Enter your public key (optional)', 'ninjafirewall') ?></th>
			<td>
				<input id="clogs-pubkey" class="large-text" type="text" maxlength="80" name="nfw_options[clogs_pubkey]" value="<?php echo htmlspecialchars( $nfw_options['clogs_pubkey'] ) ?>" autocomplete="off" />
				<p class="description"><?php printf( __('<a href="%s">Consult our blog</a> if you want to enable centralized logging.', 'ninjafirewall'), 'https://blog.nintechnet.com/centralized-logging-with-ninjafirewall/' ) ?></p>
			</td>
		</tr>
	</table>

	<br />
	<input type="hidden" name="nfw_act" value="pubkey" />
	<input class="button-primary" name="save_pubkey" value="<?php _e('Save Public Key', 'ninjafirewall') ?>" type="submit" />
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input class="button-secondary" name="delete_pubkey" value="<?php _e('Delete Public Key', 'ninjafirewall') ?>" type="submit"<?php disabled($nfw_options['clogs_pubkey'], '' ) ?> />
	<input type="hidden" name="tab" value="firewalllog" />

</form>
<?php

// ---------------------------------------------------------------------

function nf_sub_log_save_options( $nfw_options ) {

	if ( empty( $_POST['nfw_options']['auto_del_log'] ) || ! preg_match( '/^\d+$/', $_POST['nfw_options']['auto_del_log'] ) ) {
		$nfw_options['auto_del_log'] = 0;
	} else {
		$nfw_options['auto_del_log'] = (int) $_POST['nfw_options']['auto_del_log'];
	}
	// We need to keep the log for more than 24 hours otherwise
	// the daily report will be empty
	if ( $nfw_options['auto_del_log'] == 1 ) {
		$nfw_options['auto_del_log'] = 2;
	}

	nfw_update_option( 'nfw_options', $nfw_options );

}

// ---------------------------------------------------------------------

function nf_sub_log_create( $log ) {

	file_put_contents( $log, "<?php exit; ?>\n" );

}

// ---------------------------------------------------------------------

function nf_sub_log_find_local( $log_dir ) {

	$available_logs = array();
	if ( is_dir( $log_dir ) ) {
		if ( $dh = opendir( $log_dir ) ) {
			while ( ($file = readdir($dh) ) !== false ) {
				if (preg_match( '/^(firewall_(\d{4})-(\d\d)(?:\.\d+)?\.php)$/', $file, $match ) ) {
					$available_logs[$match[1]] = 1;
				}
			}
			closedir($dh);
		}
	}
	krsort($available_logs);

	return $available_logs;
}

// ---------------------------------------------------------------------

function nf_sub_log_save_pubkey( $nfw_options ) {

	if ( empty( $_POST['nfw_options']['clogs_pubkey'] ) ||
		! preg_match( '/^[a-f0-9]{40}:(?:[a-f0-9:.]{3,39}|\*)$/', $_POST['nfw_options']['clogs_pubkey'] ) ) {
		$nfw_options['clogs_pubkey'] = '';
	} else {
		$nfw_options['clogs_pubkey'] = $_POST['nfw_options']['clogs_pubkey'];
	}

	nfw_update_option( 'nfw_options', $nfw_options);

}

// ---------------------------------------------------------------------

function nf_sub_log_read_local( $log, $log_dir, $max_lines ) {

	if (! preg_match( '/^(firewall_\d{4}-\d\d(?:\.\d+)?\.)php$/', trim( $log ) ) ) {
		wp_nonce_ays('log_select');
	}

	$data = array();
	$data['type'] = 'local';

	if (! is_file( $log_dir . $log ) ) {
		$data['err_msg'] = __('The requested log does not exist.', 'ninjafirewall');
		return $data;
	}

	$data['log'] = file( $log_dir . $log, FILE_SKIP_EMPTY_LINES );

	if ( $data['log'] === false ) {
		$data['err_msg'] = __('Unable to open the log for read operation.', 'ninjafirewall');
		return $data;
	}
	if ( strpos( $data['log'][0], '<?php' ) !== FALSE ) {
		unset( $data['log'][0] );
	}
	$data['lines'] = count( $data['log'] );
	if ( $max_lines < $data['lines'] ) {
		for ($i = 0; $i < ( $data['lines'] - $max_lines); ++$i ) {
			unset( $data['log'][$i] ) ;
		}
	}

	if ( $data['lines'] == 0 ) {
		$data['err_msg'] = __('The selected log is empty.', 'ninjafirewall');
	}

	return $data;

}

// ---------------------------------------------------------------------
// EOF
