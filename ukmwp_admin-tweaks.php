<?php  
/* 
Plugin Name: UKM WP Admin Tweaks
Plugin URI: http://www.ukm-norge.no
Description: Tweaker WP-admin litt her og der for å få det til å se bra ut. (tidligere del av UKMNorge plugin)
Author: UKM Norge / M Mandal 
Version: 1.0 
Author URI: http://www.ukm-norge.no
*/

require_once('UKMconfig.inc.php');
require_once('tweak.logon.php');

#require_once('tweak.mail.php');

require_once('tweak.login.php');
require_once('tweak.video_on_top.php');

// Bytt ut WP-logoen med UKM-logoen på innloggingsskjermen
add_filter('login_head', 'UKMwpat_login');
// Bytt lost-password-lenken mot mailto:support@ukm.no
add_action('lostpassword_url', 'UKMWpat_lostpassword' );


add_action('login_redirect', 'UKMwpat_redirect_superadmin', 10, 3);
function UKMwpat_redirect_superadmin( $url, $request, $user ) {
	if( $user->ID == 1 )
		return 'https://'. UKM_HOSTNAME .'/wp-admin/network/';
	return $url;
}

// Bytt ut avatarer
require_once('tweak.avatars.php');
add_filter( 'get_avatar' , 'ukm_avatar' , 1 , 5 );

// Legg til video som toppbilde-boks
add_action('add_meta_boxes', 'UKMwpat_add_video_box');
add_action('save_post', 'ukm_top_video_save');

if(is_admin()){
	require_once('tweak.logon_redir.php');
	require_once('tweak.mediaform.php');
	require_once('tweak.menu.php');
	require_once('tweak.adminmenu_build.php');
	require_once('tweak.posts.php');
	require_once('tweak.post-meta.php');
	require_once('tweak.post-layout.php');
	require_once('tweak.update-services.php');
	require_once('tweak.password-restrict.php');
	require_once('tweak.user-fields.php');
	require_once('tweak.multiauthor.php');
	require_once('tweak.set-option.php');
	require_once('tweak.post-recommendedfields.php');

	
	add_action( 'admin_init', 'UKMwpat_logon_check' );

	## ADD NETWORK UPDATE MENU
	add_action('init', 'ActivateUpdateServices_init');

	## HOOK MENU
//	add_action('admin_menu', 'UKMwpat_tweak_menu_separators', 15000);
	add_action('network_admin_menu', 'UKMwpat_tweak_network_menu', 300);
	add_action( 'network_admin_menu', 'UKMwpat_set_option' );
	
	add_action('admin_menu', 'UKMwpat_tweak_menu_remove', 300);
	add_action('admin_menu', 'UKMwpat_admin_menu_build');
	add_action('admin_menu', 'UKMwpat_addSeparators',10000);
	wp_enqueue_style('tweak_adminmenu', plugin_dir_url( __FILE__ ).'css/tweak.adminmenu.css');

	## REDIRECT USER TO HIS/HERS ONE SITE/BLOG
	add_action('_admin_menu', 'UKMwpat_redirect_admin');


	## CHANGE POSTS GUI
	add_action( 'admin_menu', 'UKMwpat_remove_posts_meta_boxes' );
	add_action('init', 'UKMwpat_change_allowed_tags');

	add_action( 'add_meta_boxes', 'UKMwpat_add_tag_meta_box' );
	add_action( 'save_post', 'ukmn_meta_box_save' );
	add_action('delete_post', 'UKMwpat_related_delete', 10);

	add_action('add_meta_boxes', 'UKMwpat_add_ma_box');
	add_action( 'admin_enqueue_scripts', 'UKMwpat_add_ma_styles', 10000 );

	
	// Stopp publisering og vis spørsmål om vi mangler info
	add_action('admin_menu', 'UKMwpat_req_menu_hook');
	add_action('save_post', 'UKMwpat_req_hook', 10002, 2);
	add_action('admin_enqueue_scripts', 'UKMwpat_req_script', 10000 );

	// Layout for festival-lignende sider
	add_action( 'add_meta_boxes', 'UKMwpat_add_layout_meta_box' );
	add_action( 'save_post', 'ukm_post_layout_save' );


	add_filter('manage_posts_columns', 'UKMwpat_custom_post_columns');
	wp_enqueue_style('tablefooter_hide', plugin_dir_url( __FILE__ ).'css/tweak.tablefooter_hide.css');

	## CHANGE UPLOAD / MEDIA GUI	
	add_filter('attachment_fields_to_edit', 'UKMwpat_mediaform', 20);
	add_filter('media_meta', 'UKMwpat_editmedia');
	
	add_filter('upload_mimes', 'UKMwpat_upload_mimes');
	
	## USERS (EDIT FORM)
	add_filter('user_contactmethods','UKMwpat_user_remove_controls',10,1);
	add_action( 'admin_enqueue_scripts', 'UKMwpat_users_form' );
	add_action('profile_update', 'UKMwpat_set_password');

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
add_action('current_screen', 'UKMwpat_change_role_name');
add_action('UKM_filter_roles', 'UKMwpat_change_role_name_raw');
add_action('wp_before_admin_bar_render','UKMwpat_modify_toolbar', 10000);