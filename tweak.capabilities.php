<?php
// Do we now use plugin? (2013)
/*

function modify_capabilities(){
  // get the role you want to change: editor, author, contributor, subscriber
  $editor_role = get_role('editor');
  $editor_role->remove_cap('publish_pages');
  $editor_role->remove_cap('edit_pages');
  $editor_role->remove_cap('edit_others_pages');
  $editor_role->remove_cap('manage_links');
  $editor_role->remove_cap('moderate_comments');
}
*/

function UKMwpat_change_role_name() {
    global $wp_roles;

    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();

	UKMwpat_change_role_name_raw( $wp_roles );

        #remove_role( 'subscriber' );
    //You can list all currently available roles like this...
    //$roles = $wp_roles->get_names();
    //print_r($roles);
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