<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Geografi\Kommune;
use UKMNorge\Nettverk\Omrade;


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
        $arrangement = new Arrangement(get_option('pl_id'));
        
        // Arrangement tilbake knapp
        if($arrangement->getEierType() != 'land') {
            $menu[-5] = $menu[2];
            $menu[-5][0] = 'Tilbake';
            $menu[-5][1] = 'read';
            $menu[-5][4] =  $menu[-5][4] . ' show-item-always';  
            
            if($arrangement->erSingelmonstring() || $arrangement->getEierType() == 'fylke') {
                $kommuneEllerFylke = $arrangement->getEierType() == 'kommune' ? $arrangement->getKommune() : $arrangement->getFylke();
                $link = '/wp-admin/user/admin.php?page=UKMnettverket_'. $arrangement->getEierType() .'&omrade='. $kommuneEllerFylke->getId() .'&type='. $arrangement->getEierType();
                $menu[-5][0] = 'Tilbake til ' . ($arrangement->getEierType() == 'kommune' ? 'kommune' : ($arrangement->getEierType() == 'fylke' ? 'fylke' : ''));
            } else { 
                // flere kommuner funnet! Kan ikke gå tilbake til single kommune
                $link = "javascript:history.go(-1)";
                if(isset($_SERVER['HTTP_REFERER'])) {
                    $link = $_SERVER['HTTP_REFERER'];
                }
            }

            $menu[-5][2] = $link;
            $menu[-5][6] = 'dashicons-arrow-left-alt';
        }

        $menu[2][0] = 'Arrangement';
        $menu[2][6] = 'dashicons-buddicons-groups';
        $menu[2][4] = $menu[2][4] . ' show-item-always';        
    } else {
        $kommuneEllerFylke = null;

        if(get_option('site_type') == 'kommune') {
            $kommuneEllerFylke = new Kommune(get_option('Kommune'));
        }
        elseif(get_option('site_type') == 'fylke') {
            $kommuneEllerFylke = Fylker::getById(get_option('Fylke'));
        }

        if($kommuneEllerFylke != null) {
            $menu[2][0] = 'Tilbake';
            $menu[2][6] = 'dashicons-arrow-left-alt';
            $menu[2][4] = $menu[2][4] . ' show-item-always';        
            $menu[2][2] = '/wp-admin/user/admin.php?page=UKMnettverket_'. get_option('site_type') .'&omrade='. $kommuneEllerFylke->getId() .'&type='. get_option('site_type');
        }

        
    }

    add_action('wp_before_admin_bar_render', 'changeAdminBarInfo', 10001);

    
    $nettsideNavn = get_option('pl_id') ? 'Arrangement ' : (get_option('site_type') == 'kommune' ? 'Kommune ' : (get_option('site_type') == 'fylke' ? 'Fylke ' : ''));
	## ENDRE HOVEDMENY FRA WP
	# POSTS = Nettside
	$menu[5][0] = $nettsideNavn.'nettside';
	$menu[5][6] = 'dashicons-desktop';
	$submenu['edit.php'][5][0] = 'Nyheter';
	remove_submenu_page('edit.php', 'post-new.php');

	# PAGES = Nettside: sider
    unset( $menu[20] ); // Fjernet side-redigering
    // Kommune- og fylkessider skal ikke ha sider (enda), med mindre spesial_meny er definert
    if( is_super_admin() || !(in_array(get_option('site_type'), ['kommune','fylke']) && !get_option('spesial_meny')) ) {
        add_submenu_page('edit.php', 'Sider', 'Sider', 'edit_pages', 'edit.php?post_type=page');
    }
    ## spesial_meny er en setting som gir utvalgte sites tilgang til sider-modulen
	# funksjonen er innført i 2018, med Sogn og Fjordane + Østfold som testfylker
	# Innstillingen brukes av 
	# - UKMresponsive (Wordpress/Controller/monstring/fylke.controller.php
	# - UKMnettside (controller/forside.controller.php og ukmnettside.php)

	
	# MEDIA = Nettside: mediebibliotek
	unset( $menu[10] ); // Fjernet side-redigering
	add_submenu_page('users.php', 'Mediebibliotek', 'Mediebibliotek', 'superadmin', 'upload.php');

	unset( $menu[65] ); // plugins
	unset( $menu[80] ); // settings
    
	// REMOVE
	$remove = [
        15	=> 'link-manager.php',
        25	=> 'edit-comments.php',
        60 => 'themes.php',
        75 => 'tools.php',
        70 => 'profile.php',
        130 => 'edit.php?post_type=page'
    ];
    
    $remove_round_two = [
        70 => 'users.php',
    ];

	foreach( $remove as $id => $file) {
		if( isset( $menu[ $id ] ) && $menu[ $id ][2] == $file ) {
            unset( $menu[ $id ] );	
        }
    }
    foreach($remove_round_two as $id => $file ) {
        if( isset( $menu[ $id ] ) && $menu[ $id ][2] == $file ) {
            unset( $menu[ $id ] );	
        }
    }
	unset( $menu[59] ); // separator2

	if( !in_array(get_option('site_type'), ['arrangor','norge','ungdom','om']) ) {
		remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );
	}
	remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );
	remove_submenu_page( 'index.php', 'my-sites.php' );
    remove_submenu_page( 'upload.php', 'media-new.php' );
    

    /**
     * Lag egen WP-meny for superadmins
     */
    $page = add_menu_page(
        'Wordpress',
        'Wordpress',
        'superadmin',
        'users.php',
        '',
        'dashicons-wordpress-pink',
        1000
    );
    remove_submenu_page( 'users.php', 'user-new.php' );
    remove_submenu_page( 'users.php', 'profile.php' );
    remove_submenu_page( 'users.php', 'users-user-role-editor.php' );
    // Import / export
    $page_export = add_submenu_page(
        'users.php',
        'Eksporter',
        'Nyheter: eksporter',
        'superadmin',
        'export.php'
    );
    $page_import = add_submenu_page(
        'users.php',
        'Importer',
        'Nyheter: importer',
        'superadmin',
        'import.php'
    );

    $submenu['edit.php'][16][2] = 'edit.php?page=UKMnettside';
    $tempEditSm = $submenu['edit.php'][5];
    $submenu['edit.php'][5] =  $submenu['edit.php'][16];
    $submenu['edit.php'][16] = $tempEditSm;
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
}

function changeAdminBarInfo($n=null) {
    global $wp_admin_bar;

    $navn = '';
    $domClass = '';

    if(isset($_GET['omrade']) && isset($_GET['type'])) {
        $kommuneEllerFylke = $_GET['type'] == 'kommune' ? Omrade::getByKommune($_GET['omrade'])->getKommune() : Omrade::getByFylke($_GET['omrade'])->getFylke();
        $navn = $kommuneEllerFylke->getNavn(). ' - admin side';;
    }
    elseif(get_option('pl_id')) {
        $arrangement = new Arrangement(get_option('pl_id'));
        $navn = get_bloginfo('name') . ' - arrangement side </a><a class="vis-nettsiden-link" href="/'. $arrangement->getPath() .'"><span class="ab-icon dashicons dashicons-admin-home" style="margin-top: .1em;"></span><span class="navn">Vis nettsiden</span></a>';
        $domClass = ' arrangement-side';
    }
    elseif(get_option('site_type') == 'kommune') {
        $kommune = new Kommune(get_option('Kommune'));
        $navn = $kommune->getNavn(). ' - nettside redigering';
    }
    elseif(get_option('site_type') == 'fylke') {
        $fylke = Fylker::getById(get_option('Fylke'));
        $navn = $fylke->getNavn(). ' - nettside redigering';
    }
    elseif(get_option('pl_id')){
        $navn = 'Arrangement admin side';
    }

    $args = array(
        'id'    => 'wp-logo',
        'title' => '<img src="//grafikk.ukm.no/profil/logoer/UKM_logo_sort_0100.png" id="UKMlogo" />' . (strlen($navn) > 0 ? $navn : $n),
        'href'  => user_admin_url(),
        'meta'  => array('class' => 'kommune-fylke' . $domClass)
    );
    $wp_admin_bar->add_node($args);
}

?>
