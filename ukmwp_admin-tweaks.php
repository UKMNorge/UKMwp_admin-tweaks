<?php  
/* 
Plugin Name: UKM WP Admin Tweaks
Plugin URI: http://www.ukm-norge.no
Description: Tweaker WP-admin litt her og der for å få det til å se bra ut. (tidligere del av UKMNorge plugin)
Author: UKM Norge / M Mandal 
Version: 1.0 
Author URI: http://www.ukm-norge.no
*/


if(is_admin()){
	require_once('tweak.logon_redir.php');
	require_once('tweak.mediaform.php');
	require_once('tweak.menu.php');
	require_once('tweak.posts.php');
	require_once('tweak.post-meta.php');
	require_once('tweak.update-services.php');
	require_once('tweak.password-restrict.php');
	require_once('tweak.user-fields.php');

	## ADD NETWORK UPDATE MENU
	add_action('init', 'ActivateUpdateServices_init');


	## HOOK MENU
	add_action('admin_menu', 'UKMwpat_tweak_menu_separators', 1500);
	add_action('admin_menu', 'UKMwpat_tweak_menu_remove', 300);

	## REDIRECT USER TO HIS/HERS ONE SITE/BLOG
	add_action('_admin_menu', 'UKMwpat_redirect_admin');

	## CHANGE POSTS GUI
	add_action( 'admin_menu', 'UKMwpat_remove_posts_meta_boxes' );
	add_action('init', 'UKMwpat_change_allowed_tags');

	add_action( 'add_meta_boxes', 'UKMwpat_add_tag_meta_box' );
	add_action( 'save_post', 'ukmn_meta_box_save' );
	add_action('delete_post', 'UKMwpat_related_delete', 10);


	add_filter('manage_posts_columns', 'UKMwpat_custom_post_columns');
	wp_enqueue_style('tablefooter_hide', plugin_dir_url( __FILE__ ).'/css/tweak.tablefooter_hide.css');

	## CHANGE UPLOAD / MEDIA GUI	
	add_filter('attachment_fields_to_edit', 'UKMwpat_mediaform', 20);
	add_filter('media_meta', 'UKMwpat_editmedia');
	
	## USERS (EDIT FORM)
	add_filter('user_contactmethods','UKMwpat_user_remove_controls',10,1);
	add_action( 'admin_enqueue_scripts', 'UKMwpat_users_form' );

	
	## PASSWORDS
	add_filter('show_password_fields', 'tr_restrict_password_changes');
	add_action('edit_user_profile_update', 'tr_restrict_password_changes_prevent');
	add_action('personal_options_update', 'tr_restrict_password_changes_prevent');
	add_filter('allow_password_reset', 'tr_restrict_password_reset');
	add_action('login_head', 'tr_remove_reset_link_init');
	add_filter('login_errors', 'tr_remove_reset_link');

}

require_once('tweak.admin_bar.php');
require_once('tweak.capabilities.php');

## CHANGE ROLE NAMES
add_action('init', 'UKMwpat_change_role_name');
add_action('wp_before_admin_bar_render','UKMwpat_modify_toolbar', 10000);