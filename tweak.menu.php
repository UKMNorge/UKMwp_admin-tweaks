<?php
function UKMwpat_tweak_menu_remove() {
	global $current_user, $menu, $submenu;

	$menu[5][6] = 'http://ico.ukm.no/news-20.png';
	$menu[2][0] = 'Startside';
	$submenu['index.php'][1] = $menu[70];
	$submenu['index.php'][1][0] = 'Brukere';
	unset($menu[70]);
	
	if($menu[15][2] == 'link-manager.php')
		unset($menu[15]);
	
	if($menu[10][2] == 'upload.php' && get_option('ukm_top_site')!='true')
		unset($menu[10]);
	
	$move = array(59 => 'separator2',
				  60 => 'themes.php',
				  65 => 'plugins.php',
				  70 => 'users.php',
				  75 => 'tools.php',
				  80 => 'options-general.php');

	foreach( $move as $key => $file ) {
		if($menu[$key][2] == $file) {
			$menu[(1000+$key)] = $menu[$key];
			unset($menu[$key]);
		}
	}
	## IF USER IS A KOMMUNE-ADMIN
	if(get_blog_option($current_user->primary_blog, 'site_type')=='kommune')
		remove_submenu_page( 'index.php', 'my-sites.php' );
}

function UKMwpat_tweak_menu_separators() {
	global $blog_id;
	add_admin_menu_separator(399);

	if($blog_id == 1) {
		add_admin_menu_separator(349);
		add_admin_menu_separator(399);
		add_admin_menu_separator(499);
		add_admin_menu_separator(749);
		add_admin_menu_separator(899);
	} else {
		add_admin_menu_separator(199);
		add_admin_menu_separator(299);
	}
}

?>