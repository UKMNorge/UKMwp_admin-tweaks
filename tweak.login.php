<?php

use UKMNorge\Wordpress\User;

require_once('UKMconfig.inc.php');
require_once('UKM/Autoloader.php');

/**
 * Bytt wordpress-logo med UKM-logo ved innlogging
 * 
 * @print <style>
 */
function UKMwpat_login() {
	echo "<style type=\"text/css\">
		h1 a {
            background-image:url('https://grafikk.ukm.no/profil/logoer/UKM_logo_sort_0300.png') !important; 
            background-size: 140px 80px !important;
            min-height: 80px;
            min-width: 160px;
            margin:0 auto;
        }
		</style>";
}

/**
 * Endrer url for videresending tilbake til forsiden
 * 
 * @return String ukm.no-url
 */
function UKMwpat_login_logo_url() {
    return home_url();
}

/**
 * Endrer teksten til UKM-logo
 * 
 * @return String ukm.no-url
 */
function UKMwpat_login_logo_url_title() {
    return UKM_HOSTNAME;
}

/**
 * Overskriv lenken for glemt passord
 * 
 * @return String glemt passord-url
 */
function UKMwpat_lostpassword() {
	return '//'.UKM_HOSTNAME_SUBDOMAIN.'/glemt-passord/';
}

/**
 * Overskriv teksten for glemt passord
 *
 * @param [type] $text
 * @return String glemt passord-streng
 */
function UKMwpat_change_lost_your_password ($text) {
    if ($text == 'Mistet passordet ditt?'){
        $text = 'Glemt passordet?';
    }
    return $text;
 }

/**
 * Send brukeren til riktig sted etter login (user-center)
 *
 * @param [type] $user_login
 * @param [type] $user
 * @return Redirect
 */
function UKMwpat_login_redirect( $user_login, $user=null ) {

    if ( !$user ) {
        $user = get_user_by('login', $user_login);
    }
    // Hvis brukeren ikke er logget inn, return false
    if ( !$user ) {
        return;
    }
    
    // Hvis brukeren er deaktivert, kast ut
    if( !User::erAktiv( $user->ID) ) {
        wp_clear_auth_cookie();
        wp_redirect( site_url( 'wp-login.php', 'login' ) .'?deaktivert=true' );
        exit();
    }
    
    // Sjekk $_POST['redirect_to'] med regexp for å redirecte
    $regexRedirect = UKM_HOSTNAME == 'ukm.no' ? "/^https:\/\/ukm\.no\/.*$/" : "/^https:\/\/ukm\.dev\/.*$/";
    if(isset($_POST['redirect_to']) && preg_match($regexRedirect, $_POST['redirect_to']) && $_POST['redirect_to'] != 'https://'. UKM_HOSTNAME .'/wp-admin/') {
        header("Location: ". $_POST['redirect_to']);
        exit();
    }

    // Super admins skal til nettverksadmin
    if( is_super_admin($user->ID) ) {
        header("Location: ". get_blogaddress_by_id(1) . 'wp-admin/network/');
        exit();
    }

    // Alle andre skal til bruker-admin
    header("Location: ". get_blogaddress_by_id(1) . 'wp-admin/user/');
    exit();
}

/**
 * Filtrer login-meldinger
 *
 * @param [type] $message
 * @return void
 */
function UKMwpat_login_message( $message ) {
    if( isset( $_GET['deaktivert'] ) ) {
        $message = '<div id="login_error">'.
            'Brukeren din er deaktivert. Kontakt UKM Norge support'.
            '</div>';
    }
    return $message;
}

/**
 * Send RFID-brukere til riktig sted
 *
 * @param [type] $redirect_to
 * @param [type] $request
 * @param [type] $user
 * @return void
 */
function UKMwpat_login_rfid( $redirect_to, $request, $user ) {
	if( is_array( $user ) && is_array( $user->roles ) && sizeof( $user->roles ) == 1 && $user->roles[0] == 'ukm_rfid' ) {
		$redirect_to = 'https://ukm.no/wp-admin/admin.php?page=RFIDreports';
	}
	return $redirect_to;
}
?>