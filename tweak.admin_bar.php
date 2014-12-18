<?php
function UKMwpat_modify_toolbar() {
	/* @var $wp_admin_bar WP_Admin_Bar */
	global $wp_admin_bar;

	$wp_admin_bar->remove_menu('wp-logo');
	
	$remove = array(# Everthing child of WP logo
					'about','wporg','documentation','support-forums','feedback',
					# My sites
					'my-sites',
					# Comments
					'comments',
					# Add something
					'new-media','new-page','new-user',
					);
	# Hide "edit this page"
	if( !is_super_admin() ) {
		$remove[] = 'edit';

	}
	
	foreach($remove as $i => $id)
		$wp_admin_bar->remove_node($id);
		
	$wp_admin_bar->add_node(array(
								'id'=>'wp-logo',
								'href'=>admin_url()
							) );
	if(is_super_admin())
		$wp_admin_bar->add_menu( array(
									'parent' => 'site-name',
									'id'     => 'network-admin',
									'title'  => __('Network Admin'),
									'href'   => network_admin_url(),
								) );
								

	$wp_admin_bar->add_menu( array('parent' => 'new-content',
								   'id'		=> 'new-image',
								   'title'	=> 'Bilder',
								   'href' => admin_url().'admin.php?page=UKM_images'
								  )
						   );
	$wp_admin_bar->add_menu( array('parent' => 'new-content',
								   'id'		=> 'new-video',
								   'title'	=> 'Video',
								   'href' => admin_url().'admin.php?page=UKM_videorep'
								  )
						   );
	$wp_admin_bar->add_node( array(
									'id'    => 'ukm_support',
									'title' => '<img src="http://ico.ukm.no/support-16.png" id="UKMhelpicon" style=" margin-top: -4px;" /> Brukerstøtte',
									'href'  => admin_url().'admin.php?page=UKMwpd_support',
									'parent'=>'top-secondary'
								)
							);



	// TODO: should be moved to a class
	function set_node_title($id, $new_title) {
		global $wp_admin_bar;
		$new_network_admin = $wp_admin_bar->get_node($id);
		$new_network_admin->title = $new_title;
		$wp_admin_bar->remove_node($id);
		$wp_admin_bar->add_node($new_network_admin);
	}
	function reset_node_position($id) {
		global $wp_admin_bar;
		$new_network_admin = $wp_admin_bar->get_node($id);
		$wp_admin_bar->remove_node($id);
		$wp_admin_bar->add_node($new_network_admin);
	}

	set_node_title("network-admin", __("UKM Norge-admin", "UKM"));
	if (is_network_admin())
		set_node_title("site-name", sprintf( __("UKM Norge-admin: %s", "UKM"), get_current_site()->site_name ));
	reset_node_position("updates");
	reset_node_position("new-content");

	$wp_admin_bar->remove_node("wp-logo");
	$wp_admin_bar->remove_node("network-admin-s");
	$wp_admin_bar->remove_node("network-admin-u");
	$wp_admin_bar->remove_node("network-admin-p");
	$wp_admin_bar->remove_node("network-admin-d");
	$wp_admin_bar->remove_node("network-admin-t");

//	set_node_title("network-admin-d", __("Startside", "UKM"));
//	var_dump($wp_admin_bar);
}