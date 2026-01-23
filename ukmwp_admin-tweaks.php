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

// Endrer URL til UKM-logoen
add_filter( 'login_headerurl', 'UKMwpat_login_logo_url' );
add_filter( 'login_headertext', 'UKMwpat_login_logo_url_title' );

// Bytt lost-password-lenken mot mailto:support@ukm.no
add_action('lostpassword_url', 'UKMWpat_lostpassword' );
add_filter( 'gettext', 'UKMWpat_change_lost_your_password' );

add_action('wp_login', 'UKMwpat_login_redirect', 10, 2);
add_filter('login_message', 'UKMwpat_login_message');
add_filter('login_redirect', 'UKMwpat_login_rfid', 10, 3);
add_action('show_user_profile', 'UKMwpat_profile_deactivated_warning');
add_action('edit_user_profile', 'UKMwpat_profile_deactivated_warning');
// add_filter( 'user_contactmethods', 'UKMwpat_profile_fields' );
add_filter( "user_contactmethods", 'display_user_phone_number' );
add_filter( "user_user_phone_label", 'UKMwpat_profile_field_phone_description' );
add_filter('wp_is_application_passwords_available', '__return_false');


// Display user phone number in user profiles
function display_user_phone_number($user_contactmethods) {
	$user_contactmethods['user_phone'] = 'Mobilnummer';
    return $user_contactmethods;
}

function UKMwpat_profile_field_phone_description( $user ){
    echo 'Mobilnummer <br />'.
    '<small>'.
    'Kontaktpunkt for UKM Norge';
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
	wp_enqueue_style('tablefooter_hide', PLUGIN_PATH . 'UKMwp_admin-tweaks/css/tweak.tablefooter_hide.css');

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
	// add_filter('allow_password_reset', 'tr_restrict_password_reset');
	add_action('login_head', 'tr_remove_reset_link_init');
	add_filter('login_errors', 'tr_remove_reset_link');
    
    wp_enqueue_style('tweak_wp_admin', PLUGIN_PATH . 'UKMwp_admin-tweaks/css/wp-admin.css', 100000);
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
    wp_enqueue_script( 'ukmwpat_adminmenu_js', PLUGIN_PATH . 'UKMwp_admin-tweaks/js/tweak.adminmenu.js');
    wp_enqueue_script( 'ukmwpat_deltakerinfo_js', PLUGIN_PATH . 'UKMwp_admin-tweaks/js/tweak.deltakerinfo.js');
    wp_enqueue_style('tweak_adminmenu', PLUGIN_PATH . 'UKMwp_admin-tweaks/css/tweak.adminmenu.css');
    wp_enqueue_style('tweak_posteditor', PLUGIN_PATH . 'UKMwp_admin-tweaks/css/tweak.posteditor.css');
	wp_enqueue_style('UKMArrSysStyle');
	wp_enqueue_style('WPbootstrap3_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
	wp_enqueue_script('WPbootstrap3_js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
}




// Prevent WordPress from saving the biography data because biography
function prevent_biography_save($userId) {
    if (isset($_POST['description'])) {
        unset($_POST['description']);
    }
}
add_action('personal_options_update', 'prevent_biography_save');
add_action('edit_user_profile_update', 'prevent_biography_save');


// Add page edit capability to editor role
function add_page_edit_capability_to_editor_role() {
    $editor_role = get_role('editor');
    $editor_role->add_cap('edit_pages');
    $editor_role->add_cap('edit_others_pages');
    $editor_role->add_cap('publish_pages');
}
add_action('init', 'add_page_edit_capability_to_editor_role');

// Prevent access to WordPress pages created by UKM by other users
function prevent_access_to_wordpress_pages_created_by_ukm() {
	if(is_super_admin()) {
		return;
	}

	// Sjekk hvis det er admin og at det er på redigering av en side
    if (is_admin() && isset($_GET['post']) && isset($_GET['action']) && $_GET['action'] === 'edit') {
		$post_id = $_GET['post'];
		$current_post = get_post($post_id);
		
		// Liste me sider som opprettes av systemet og kan ikke redigeres fra brukere
		$excluded_page_slugs = array('bilder', 'kontaktpersoner', 'forside', 'nyheter', 'pameldte', 'program', 'lokalmonstringer', 'deltakerprogram', 'testside');

		// Hvis brukeren prøver å redigere en system side
		if ($current_post && in_array(get_post_field('post_name', $current_post), $excluded_page_slugs)) {
			throw new Exception("Du har ikke tilgang til denne siden. '". get_post_field('post_name', $current_post) ."' brukes i systemet og kan ikke redigeres. Kontakt support@ukm.no for mer informasjon!");
            exit;
        }
    }
}
add_action('admin_init', 'prevent_access_to_wordpress_pages_created_by_ukm');


add_action('admin_init', function () {
    if (!is_admin()) return;

    // Works for site admin, network admin, user admin
    if (isset($_GET['page']) && $_GET['page'] === 'ukm_deltakerinfo') {
        wp_safe_redirect( self_admin_url('edit.php?post_type=post') );
        exit;
    }
});

add_filter('parent_file', function ($parent_file) {
    // When viewing Posts list, highlight our custom menu instead of Media
    if ($GLOBALS['pagenow'] === 'edit.php' && ($_GET['post_type'] ?? 'post') === 'post') {
        return 'ukm_deltakerinfo';
    }
    return $parent_file;
});

add_filter('submenu_file', function ($submenu_file) {
    if ($GLOBALS['pagenow'] === 'edit.php' && ($_GET['post_type'] ?? 'post') === 'post') {
        return 'ukm_deltakerinfo';
    }
    return $submenu_file;
});

// Adding somthing to the top of the Posts admin page only
add_action('admin_notices', function() {
    if ($GLOBALS['pagenow'] === 'edit.php' && ($_GET['post_type'] ?? 'post') === 'post') {
	    echo '
		<div style="width: fit-content; background: var(--as-color-primary-warning-light); border: 2px solid var(--as-color-primary-warning-medium);" class="nosh-impt as-card-2 as-margin-top-space-2 as-margin-space-bottom-4 as-padding-space-2">
			<h2 class="nom as-margin-bottom-space-2">Deltakerinfo</h2>
			<section class="deltakerinfo-innlegg">
			<h4>I denne seksjonen kan du publisere innlegg direkte til deltakerne.</h4>

			<h5>Innleggene:</h5>
			<ul style="list-style-type: disc; margin-left: 20px;">
				<li>Vises kun på «Min side» hos deltakeren</li>
				<li>Er ikke synlige på den offentlige nettsiden</li>
				<li>Brukes til intern informasjon, beskjeder eller oppdateringer knyttet til arrangementet</li>
			</ul>

			<h5><strong>⚠️ Viktig:</strong></h5>
			<p>
				Arrangørene som publiserer innleggene er selv ansvarlige for å informere deltakerne om at det finnes nye innlegg.
				Klikk på <b>Send varsling</b> under hvert innlegg for å sende en SMS til alle deltakere med informasjon om det nye innlegget.
			</p>
			</section>
		</div>
		';
	}
});

add_filter('post_row_actions', 'add_custom_row_action', 10, 2);
function add_custom_row_action($actions, $post) {

    // Only for posts (change to your CPT slug if needed)
    if ($post->post_type !== 'post') {
        return $actions;
    }

    // Add your custom link
    $actions['my_action'] = sprintf(
        '<a class="send-varsling-fra-innlegg" post-id="%d">Send varsling</a>',
        $post->ID
    );

    return $actions;
}