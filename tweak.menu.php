<?php
function UKMwpat_tweak_menu_filter( $current_parent_file ) {
	if( $current_parent_file == 'upload.php' ) {
		return 'edit.php';
	}
	return $current_parent_file;
}

function UKMwpat_tweak_menu_remove() {
	global $current_user, $menu, $submenu;

	// RENAME
	$menu[2][0] = 'Startsiden';
	$menu[2][6] = 'https://ico.ukm.no/avis-menu.png';


	## ENDRE HOVEDMENY FRA WP
	# POSTS = Nettside
	$menu[5][0] = 'Nettside';
	$menu[5][6] = 'https://ico.ukm.no/hus-menu.png';
	$submenu['edit.php'][5][0] = 'Nyheter';
	remove_submenu_page('edit.php', 'post-new.php');

	# PAGES = Nettside: sider
	unset( $menu[20] ); // Fjernet side-redigering
	add_submenu_page('edit.php', 'Sider', 'Sider', 'edit_pages', 'edit.php?post_type=page');
	
	# MEDIA = Nettside: mediebibliotek
	unset( $menu[10] ); // Fjernet side-redigering
	add_submenu_page('edit.php', 'Mediebibliotek', 'Mediebibliotek', 'edit_media', 'upload.php');

	unset( $menu[65] ); // plugins
	unset( $menu[80] ); // settings
	
	// REMOVE
	$remove = array(
					15	=> 'edit-tags.php?taxonomy=link_category',
					25	=> 'edit-comments.php',
					60 => 'themes.php',
					75 => 'tools.php',
					70 => 'users.php',
					//70 => 'profile.php',
					);
	if( !(get_option('site_type') == 'land' && current_user_can('author') ) && !is_super_admin() ) {
		$remove[120]	= 'upload.php';
	}
	
	## spesial_meny er en setting som gir utvalgte sites tilgang til sider-modulen
	# funksjonen er innført i 2018, med Sogn og Fjordane + Østfold som testfylker
	# Innstillingen brukes av 
	# - UKMresponsive (Wordpress/Controller/monstring/fylke.controller.php
	# - UKMnettside (controller/forside.controller.php og ukmnettside.php)
	if( is_super_admin() || get_option('spesial_meny') ) {
	} else {
		$remove[130]	= 'edit.php?post_type=page';
	}


	foreach( $remove as $id => $file) {
		if( isset( $menu[ $id ] ) && $menu[ $id ][2] == $file )
			unset( $menu[ $id ] );	
	}
	unset( $menu[59] ); // separator2


	if( get_option('site_type') == 'kommune' || get_option('site_type') == 'fylke' ) {
		remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );
	}
	remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );
	remove_submenu_page( 'index.php', 'my-sites.php' );
	remove_submenu_page( 'upload.php', 'media-new.php' );	


}

function UKMwpat_tweak_network_menu() {
	global $menu;
	$menu[2][0] = 'Nettverket';
}
?>
