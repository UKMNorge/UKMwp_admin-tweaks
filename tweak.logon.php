<?php
function UKMwpat_logon_check() {
	global $wp_version;
	$wpat_version = get_site_option('ukmwpat_logon_version');
	// If WP is updated, rewrite wp-admin/index.php
	if($wp_version != $wpat_version)
		UKMWP_logon_update();
}

// Will re-write wp-admin/index.php to use custom dashboard
function UKMWP_logon_update() {
	global $wp_version;

	$logon_path = ABSPATH.'wp-login.php';
	
	$logon_content	= file_get_contents($logon_path);
	$logon_content 	= substr($logon_content, 5);
	
	if( strpos($logon_content, 'UKMsierJegHarLov') == false) {
		
		$replace_form_target= '<?php echo esc_url( site_url( \'wp-login.php\', \'login_post\' ) ); ?>';
		$insert_form_target	= '<?php echo esc_url( site_url( \'wp-login.php\', \'login_post\' ) ); ?>?UKMsierJegHarLov=sant';
		
		$logon_content = str_replace($replace_form_target, $insert_form_target, $logon_content);
		
		$logon_addon	= '<?php'."\r\n".'if(isset($_POST[\'log\'])&&isset($_POST[\'pwd\'])&&!isset($_GET[\'UKMsierJegHarLov\'])){die(\'Blokker adgang\');}';
		$logon_content	= $logon_addon . $logon_content;

		$fp = fopen($logon_path, 'w');
		fwrite($fp, $logon_content);
		fclose($fp);
	}
	update_site_option('ukmwpat_logon_version', $wp_version);
}
