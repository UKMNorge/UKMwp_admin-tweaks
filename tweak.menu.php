<?php
function UKMwpat_tweak_menu_filter( $current_parent_file ) {
	if( isset( $_GET['debug'] ) ) {
		echo 'CURRENT_PARENT_FILE: '. $current_parent_file;
	}
	if( $current_parent_file == 'upload.php' ) {
		return 'edit.php';
	}
	if( in_array($current_parent_file, ['site-new.php', 'user-new.php', 'sites.php'])) {
		return 'index.php';
	}
	return $current_parent_file;
}

function UKMwpat_tweak_menu_remove() {
	global $current_user, $menu, $submenu;

	// RENAME
	if( get_option('pl_id') ) {
        $menu[2][0] = 'Arrangement';
        $menu[2][6] = 'dashicons-buddicons-groups';
    } else {
        $menu[2][0] = 'Startside';
        $menu[2][6] = 'dashicons-smiley';
    }


	## ENDRE HOVEDMENY FRA WP
	# POSTS = Nettside
	$menu[5][0] = 'Nettside';
	$menu[5][6] = 'dashicons-desktop';
	$submenu['edit.php'][5][0] = 'Nyheter';
	remove_submenu_page('edit.php', 'post-new.php');

	# PAGES = Nettside: sider
    unset( $menu[20] ); // Fjernet side-redigering
    // Kommune- og fylkessider skal ikke ha sider (enda)
    if( is_super_admin() || !in_array(get_option('site_type'), ['kommune','fylke'])) {
        add_submenu_page('edit.php', 'Sider', 'Sider', 'edit_pages', 'edit.php?post_type=page');
    }
	
	# MEDIA = Nettside: mediebibliotek
	unset( $menu[10] ); // Fjernet side-redigering
	add_submenu_page('edit.php', 'Mediebibliotek', 'Mediebibliotek', 'edit_media', 'upload.php');

	unset( $menu[65] ); // plugins
	unset( $menu[80] ); // settings
    
	// REMOVE
	$remove = [
        15	=> 'link-manager.php',
        25	=> 'edit-comments.php',
        60 => 'themes.php',
        75 => 'tools.php',
        70 => 'users.php',
        70 => 'profile.php'
    ];

    if( !is_super_admin() ){
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


	if( !in_array(get_option('site_type'), ['arrangor','norge','ungdom','om']) ) {
		remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );
	}
	remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );
	remove_submenu_page( 'index.php', 'my-sites.php' );
	remove_submenu_page( 'upload.php', 'media-new.php' );
}

function UKMwpat_tweak_network_menu() {
	global $menu, $submenu;

	$_title = 0;
	$_icon = 6;

	$menu[2][$_title] = 'Nettverket';
	$menu[2][$_icon] = 'dashicons-rest-api';#https://ico.ukm.no/map-menu.png';

	$menu[25][$_title] = 'Wordpress';
	$menu[25][$_icon] = 'dashicons-wordpress';

	// Flytt "nettsteder"
	unset( $menu[5] ); 
	add_submenu_page('settings.php', 'Nettsteder', 'Nettsteder', 'manage_sites', 'sites.php');

	// Flytt "brukere"
    unset( $menu[10] );
    if ( is_super_admin() ) {
        add_submenu_page('settings.php', 'Brukere','Brukere','manage_network_users','users.php');
    }
	
	// Flytt "utvidelser"
	unset( $menu[20] );
	add_submenu_page('settings.php', 'Utvidelser', 'Utvidelser', 'manage_network_plugins', 'plugins.php');

	// Fjernet "tema"
	unset( $menu[15]);


	// Flyttet "oppdateringer"
	if( isset( $submenu['index.php'][10] ) ) {
		$submenu['settings.php'][] = $submenu['index.php'][10];
		unset( $submenu['index.php'][10] );
	}
	// Flyttet "oppgrader nettverk"
	if( isset( $submenu['index.php'][15] ) ) {
		$submenu['settings.php'][] = $submenu['index.php'][15];
		unset( $submenu['index.php'][15] );
	}

	/*
	echo "\n\r\n\r  -- MENU -- \n\r\n\r ";
	var_dump( $menu );
	echo "\n\r\n\r  -- SUBMENU -- \n\r\n\r ";
	var_dump( $submenu );
	*/
}
?>
