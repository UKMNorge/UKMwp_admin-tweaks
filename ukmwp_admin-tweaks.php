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

add_action('wp_login', 'UKMwpat_login_redirect', 10, 2);
add_filter('login_message', 'UKMwpat_login_message');
add_filter('login_redirect', 'UKMwpat_login_rfid', 10, 3);
add_action('show_user_profile', 'UKMwpat_profile_deactivated_warning');
add_action('edit_user_profile', 'UKMwpat_profile_deactivated_warning');
add_filter( 'user_contactmethods', 'UKMwpat_profile_fields' );
add_filter( "user_user_phone_label", 'UKMwpat_profile_field_phone_description' );

function UKMwpat_profile_field_phone_description( $user ){
    echo 'Mobilnummer <br />'.
    '<small>'.
    'Kontaktpunkt for UKM Norge.<br />'.
    'OBS: Før du har opprettet arrangement vises dette på UKM.no. '.
    'Når du har ett eller flere arrangement, vises kontaktpersoner for disse på UKM.no'.
    '</small>';
}

add_filter('screen_options_show_screen', 'UKMdeactivate_screen_options');
function UKMdeactivate_screen_options() { 
	return false;
}

// Bytt ut avatarer
require_once('tweak.avatars.php');
add_filter( 'get_avatar' , 'ukm_avatar' , 1 , 5 );

// Legg til video som toppbilde-boks
add_action('add_meta_boxes', 'UKMwpat_add_video_box');
add_action('save_post', 'ukm_top_video_save');

require_once('tweak.user-fields.php'); # UTENFOR if(s_admin()) da den brukes av glemt passord!

if(is_admin()){
	require_once('tweak.mediaform.php');
	require_once('tweak.menu.php');
	#require_once('tweak.adminmenu_build.php');
	require_once('tweak.posts.php');
	require_once('tweak.post-meta.php');
	require_once('tweak.post-layout.php');
	require_once('tweak.update-services.php');
	require_once('tweak.multiauthor.php');
	require_once('tweak.set-option.php');
	require_once('tweak.post-recommendedfields.php');
    require_once('tweak.gutenberg.php');
    require_once('tweak.users.php');
    
	add_action( 'admin_init', 'UKMwpat_logon_check' );

	## ADD NETWORK UPDATE MENU
	add_action('init', 'ActivateUpdateServices_init');

	## HOOK MENU
//	add_action('admin_menu', 'UKMwpat_tweak_menu_separators', 15000);
	add_filter('parent_file', 'UKMwpat_tweak_menu_filter',-15000);
	add_action('network_admin_menu', 'UKMwpat_tweak_network_menu', 300);
	add_action( 'network_admin_menu', 'UKMwpat_set_option' );
	
	add_action('admin_menu', 'UKMwpat_tweak_menu_remove', 300);

	## CHANGE POSTS GUI
	add_action( 'admin_menu', 'UKMwpat_remove_posts_meta_boxes',999 );
    add_action( 'init', 'UKMwpat_remove_post_type_support',100 );
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
    
    ## USERS (TABELL)
    add_filter( 'manage_users_columns', 'UKMwpat_modify_user_table', 100 );
    add_filter('manage_users_custom_column', 'UKMwpat_modify_user_column', 100, 3);

	## PASSWORDS
	add_filter('allow_password_reset', 'tr_restrict_password_reset');
	add_action('login_head', 'tr_remove_reset_link_init');
	add_filter('login_errors', 'tr_remove_reset_link');
    
    wp_enqueue_style('tweak_wp_admin', plugin_dir_url( __FILE__ ).'css/wp-admin.css', 100000);
}

require_once('tweak.admin_bar.php');
require_once('tweak.capabilities.php');

## CHANGE ROLE NAMES
add_action('init', 'UKMwpat_change_role_name');
add_action('current_screen', 'UKMwpat_change_role_name');
add_action('UKM_filter_roles', 'UKMwpat_change_role_name_raw');
add_action('wp_before_admin_bar_render','UKMwpat_modify_toolbar', 10000);

## Admin favicon
add_action( 'admin_head', 'UKMwpat_favicon' );

add_action('admin_enqueue_scripts', 'UKMwpat_load_scripts_and_styles');



function UKMwpat_favicon() {
	echo '<link rel="shortcut icon" href="//ico.ukm.no/wp-admin_favicon.ico" />';
}

function UKMwpat_load_scripts_and_styles() {
    wp_enqueue_script( 'ukmwpat_adminmenu_js', plugin_dir_url(__FILE__). 'js/tweak.adminmenu.js');
    wp_enqueue_style('tweak_adminmenu', plugin_dir_url(__FILE__). 'css/tweak.adminmenu.css');
}