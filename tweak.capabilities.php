<?php
function UKMwpat_change_role_name() {
    global $wp_roles;

    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();

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


//// !!! !!! OBS: HACK !!! !!!
/* For at det skal virke i network-admin, bytt ut følgende linje (omtrent 55):
	
	$editblog_roles = $wp_roles->roles;
	
	med:
	
	$editblog_roles = apply_filters('UKM_filter_roles', $wp_roles);
	$editblog_roles = $wp_roles->roles;
*/