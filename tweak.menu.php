<?php
function UKMwpat_tweak_menu_remove() {
	global $current_user, $menu, $submenu;

	// RENAME
	$menu[2][0] = 'Startside';
	
	// MOVE
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
	
	// REMOVE
	$remove = array(10	=> 'upload.php',
					15	=> 'edit-tags.php?taxonomy=link_category',
					20	=> 'edit.php?post_type=page',
					25	=> 'edit-comments.php',
					);	
	if( !is_super_admin() ) 
		$remove[1075] = 'tools.php';
	foreach( $remove as $id => $file) {
		if( $menu[ $id ][2] == $file )
			unset( $menu[ $id ] );	
	}
	
	remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );
	remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );

	// Hide profile menu (use the right top corner instead)
	if( $menu[70][2] == 'profile.php') {
		unset($menu[70]);
	}
	## IF USER IS A KOMMUNE-ADMIN
	if(get_blog_option($current_user->primary_blog, 'site_type')=='kommune')
		remove_submenu_page( 'index.php', 'my-sites.php' );
}
?>