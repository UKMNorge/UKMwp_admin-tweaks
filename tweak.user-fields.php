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


function UKMwpat_set_password() {
	global $wpdb;

	if($_POST['pass1']==$_POST['pass2']&&!empty($_POST['pass1'])) {
		$wpdb->update('ukm_brukere',
					array('b_password'=>$_POST['pass1']),
					array('wp_bid'=>$_POST['user_id']));
	}
}