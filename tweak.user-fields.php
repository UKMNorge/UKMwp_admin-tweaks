<?php
// REMOVE CONTACT FIELDS
function UKMwpat_user_remove_controls( $contactmethods ) {
  unset($contactmethods['aim']);
  unset($contactmethods['jabber']);
  unset($contactmethods['yim']);
  return $contactmethods;
}



function UKMwpat_users_form($hook) {
    if( 'profile.php' != $hook )
        return;
    wp_enqueue_script( 'UKMwpat_users_form_js',  plugin_dir_url( __FILE__ ). 'js/tweak.user-remove-inputs.js');
}
