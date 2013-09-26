<?php
function UKMwpat_modify_toolbar() {
	global $wp_admin_bar;
	
	$remove = array(# Everthing child of WP ølogo
					'about','wporg','documentation','support-forums','feedback',
					# My sites
					'my-sites',
					# Comments
					'comments',
					# Add something
					'new-media','new-page','new-user'
					);
	
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
}