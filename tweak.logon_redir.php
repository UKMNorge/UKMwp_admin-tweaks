<?php
require_once('UKMconfig.inc.php');

function UKMwpat_login_redirect( $user_login, $user ) {
    if( is_super_admin($user->ID) ) {
        header("Location: ". get_blogaddress_by_id(1) . 'wp-admin/network/');
        exit();
    }
    header("Location: ". get_blogaddress_by_id(1) . 'wp-admin/user/');
    exit();
}

function UKMwpat_redirect_admin() {
	global $current_user, $blog_id;
	
	if ($current_user->primary_blog =='1') return;

		$primary_url = get_blogaddress_by_id($current_user->primary_blog) . 'wp-admin/';

	if( strpos($_SERVER['REQUEST_URI'], 'wp-admin' ) && ( $blog_id != $current_user->primary_blog) && strpos($primary_url,'/pl/')!==false) {
		wp_redirect($primary_url);
	}
}

function UKMwpat_login_rfid( $redirect_to, $request, $user ) {
	if( is_array( $user ) && is_array( $user->roles ) && sizeof( $user->roles ) == 1 && $user->roles[0] == 'ukm_rfid' ) {
		$redirect_to = 'https://ukm.no/wp-admin/admin.php?page=RFIDreports';
	}
	return $redirect_to;
}
?>