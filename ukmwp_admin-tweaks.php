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
	require_once('tweak.admin_bar.php');
	require_once('tweak.capabilities.php');
	require_once('tweak.logon_redir.php');
	require_once('tweak.mediaform.php');
	require_once('tweak.menu.php');
	require_once('tweak.posts.php');

	## CHANGE ROLE NAMES
	add_action('init', 'UKMwpat_change_role_name');
	add_action('wp_before_admin_bar_render','UKMwpat_modify_toolbar', 1000);


	## HOOK MENU
	add_action('admin_menu', 'UKMwpat_tweak_menu_separators');
	add_action('admin_menu', 'UKMwpat_tweak_menu_remove', 300);

	## REDIRECT USER TO HIS/HERS ONE SITE/BLOG
	add_action('_admin_menu', 'UKMwpat_redirect_admin');

	## CHANGE POSTS GUI
	add_action( 'add_meta_boxes', 'ukmn_meta_box' );
	add_action( 'save_post', 'ukmn_meta_box_save' );
	add_action('init', 'UKMwpat_change_allowed_tags');

	add_filter('manage_posts_columns', 'UKMwpat_custom_post_columns');
	wp_enqueue_style('tablefooter_hide', dirname(__FILE__).'/css/tweak.tablefooter_hide.css');

	## CHANGE UPLOAD / MEDIA GUI	
	add_filter('attachment_fields_to_edit', 'UKMwpat_mediaform', 20);
	add_filter('media_meta', 'UKMwpat_editmedia');

#	add_action('delete_post', 'UKMN_related_sync', 10);
}