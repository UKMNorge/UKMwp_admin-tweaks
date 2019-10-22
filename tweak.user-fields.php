<?php

use UKMNorge\Wordpress\User;

require_once('UKM/Autoloader.php');

// REMOVE CONTACT FIELDS
function UKMwpat_user_remove_controls( $contactmethods ) {
  unset($contactmethods['aim']);
  unset($contactmethods['jabber']);
  unset($contactmethods['yim']);
  return $contactmethods;
}


function UKMwpat_profile_deactivated_warning( $user ) {
    if( !User::erAktiv($user->ID) ) {
        echo '<div class="alert alert-danger notice notice-danger"><b>OBS:</b> Brukeren er deaktivert</div>';
    }
}

function UKMwpat_users_form($hook) {
    wp_enqueue_script( 'UKMwpat_users_form_js',  plugin_dir_url( __FILE__ ). 'js/tweak.user-remove-inputs.js');

    if( 'profile.php' != $hook )
        return;
}