<?php
function UKMwpat_tweak_menu_remove() {
	global $current_user, $menu, $submenu;

	#var_dump( $menu );

	// RENAME
	$menu[2][0] = 'Startside';

	// MOVE POSTS WITHIN ARRAY, NOT MENU
		$menu[9] = $menu[5];
		unset( $menu[5] );
	
	$move = array();
	$move[] = (object) array('position'=> 9, 'id' => 'edit.php', 'offset' => 101);
	$move[] = (object) array('position'=> 10, 'id' => 'upload.php', 'offset' => 110);
	$move[] = (object) array('position'=> 20, 'id' => 'edit.php?post_type=page', 'offset' => 110);
	
	$move[] = (object) array('position'=> 65, 'id' => 'plugins.php', 'offset' => 1000);
	$move[] = (object) array('position'=> 80, 'id' => 'options-general.php', 'offset' => 1000);
	
	foreach( $move as $data ) {
		if( $menu[ $data->position ][2] == $data->id ) {
			$menu[ ($data->offset+$data->position) ] = $menu[ $data->position ];
			unset( $menu[ $data->position ] );
		}
	}

	
	// REMOVE
	$remove = array(
					15	=> 'edit-tags.php?taxonomy=link_category',
					25	=> 'edit-comments.php',
					60 => 'themes.php',
					75 => 'tools.php',
					70 => 'users.php',

					);
	if( !(get_option('site_type') == 'land' && current_user_can('author') ) && !is_super_admin() ) {
		$remove[120]	= 'upload.php';
		$remove[130]	= 'edit.php?post_type=page';
	}

	if( !is_super_admin() ) {
		$remove[1075] = 'tools.php';
	}
	foreach( $remove as $id => $file) {
		if( $menu[ $id ][2] == $file )
			unset( $menu[ $id ] );	
	}
	
	unset( $menu[59] );
	
	if( get_option('site_type') == 'kommune' || get_option('site_type') == 'fylke' ) {
		remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );
	}
	remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );

	// Hide profile menu (use the right top corner instead)
	if( $menu[70][2] == 'profile.php') {
		unset($menu[70]);
	}
	## IF USER IS A KOMMUNE-ADMIN
	#if(get_blog_option($current_user->primary_blog, 'site_type')=='kommune')
	remove_submenu_page( 'index.php', 'my-sites.php' );

	remove_submenu_page( 'upload.php', 'media-new.php' );	
}

function UKMwpat_tweak_network_menu() {
	global $menu;
	$menu[2][0] = 'Startside';
}
?>
