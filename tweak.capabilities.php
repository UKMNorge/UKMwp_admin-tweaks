<?php
/*
// KOMMENTERES UT FOR Å ENDRE ROLES. 
// FØRST MODIFISERT ER DET LAGRET, INGEN GRUNN TIL Å GJØRE DETTE HELE TIDEN
add_action( 'admin_init', 'UKMwpat_modify_roles');
function UKMwpat_modify_roles() {
	$author = get_role( 'author' );
	$author->add_cap('edit_pages');
	$author->add_cap('edit_published_pages');
}
*/
function UKMwpat_change_role_name() {
    global $wp_roles;

	UKMwpat_add_roles();

    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();

	UKMwpat_add_capabilities($wp_roles);
	
	UKMwpat_change_role_name_raw( $wp_roles );
}

function UKMwpat_change_role_name_raw($wp_roles) {
    $wp_roles->roles['editor']['name'] = 'UKM-arrangør';
    $wp_roles->role_names['editor'] = 'UKM-arrangør';
    $wp_roles->roles['author']['name'] = 'UKM Nettredaktør';
    $wp_roles->role_names['author'] = 'UKM Nettredaktør';
    $wp_roles->roles['contributor']['name'] = 'UKM-journalist';
    $wp_roles->role_names['contributor'] = 'UKM-journalist';
	return $wp_roles;
}


function UKMwpat_add_roles() {
	$capabilities = array('read','ukm_rapporter','ukm_playback', 'ukm_sms');
	add_role( 'ukm_produsent', 'UKM Produsent', $capabilities );

	$capabilities = array('read','ukm_nominasjon', 'ukm_sms');
	add_role( 'ukm_nominasjon', 'UKM Nominasjon', $capabilities );	
	
	$capabilities = array('read', 'ukmrfid_reports', 'ukm_sms');
	add_role( 'ukm_rfid', 'UKM RFID Rapport', $capabilities);
}

function UKMwpat_add_capabilities($wp_roles) {
	
	# RFID rapport-bruker
	$ukm_rfid = get_role('ukm_rfid');
	$ukm_rfid->add_cap('read');
	$ukm_rfid->add_cap('ukmrfid_reports');
	$ukm_rfid->add_cap('ukm_sms');
	
	# Nominasjon-bruker
	$ukm_nominasjon = get_role('ukm_nominasjon');
	$ukm_nominasjon->add_cap('read');
#	$ukm_nominasjon->add_cap('upload_files');
	$ukm_nominasjon->add_cap('ukm_sms');
	
	# Rapport-bruker / Produsent
	$ukm_produsent = get_role('ukm_produsent');
	$ukm_produsent->add_cap('read');
	$ukm_produsent->add_cap('upload_files');
	$ukm_produsent->add_cap('ukm_rapporter');	
	$ukm_produsent->add_cap('ukm_playback');
	$ukm_produsent->add_cap('ukm_materiell');
	$ukm_produsent->add_cap('ukm_idebank');
	$ukm_produsent->add_cap('ukm_sms');
	
	# UKM-journalist
	$contributor = get_role('contributor');
	$contributor->remove_cap('edit_others_posts');
	$contributor->add_cap('upload_files');
	$contributor->add_cap('ukm_materiell');
	$contributor->add_cap('ukm_idebank');
	$contributor->remove_cap('ukm_sms');

	# UKM-nettredaktør
	$author = get_role('author');
	$author->add_cap('edit_others_posts');
	$author->add_cap('ukm_rapporter');
	$author->add_cap('ukm_materiell');
	$author->add_cap('ukm_idebank');
	$author->add_cap('ukm_sms');
    $author->add_cap('publish_posts');
    $author->add_cap('ukm_nettside');
	
	# UKM-arrangør
	$editor = get_role('editor');
	$editor->add_cap('ukm_rapporter');
	$editor->add_cap('ukm_playback');
	$editor->add_cap('ukm_materiell');
	$editor->add_cap('ukm_idebank');
    $editor->remove_cap('ukm_sms');
	$editor->add_cap('ukm_nominasjon');
    $editor->add_cap('ukm_nettside');

	# Admin
	$administrator = get_role('administrator');
	$administrator->add_cap('ukm_rapporter');
	$administrator->add_cap('ukm_playback');
	$administrator->add_cap('ukm_materiell');
	$administrator->add_cap('ukm_idebank');
	$administrator->add_cap('ukm_sms');
    $administrator->add_cap('ukm_nominasjon');
    $administrator->add_cap('ukm_nettside');

}

//// !!! !!! OBS: HACK !!! !!!
/* For at det skal virke i network-admin, bytt ut følgende linje (omtrent 55):
	
	$editblog_roles = $wp_roles->roles;
	
	med:
	
	$editblog_roles = apply_filters('UKM_filter_roles', $wp_roles);
	$editblog_roles = $wp_roles->roles;
*/
