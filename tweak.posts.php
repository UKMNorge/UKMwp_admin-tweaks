<?php
function UKMwpat_custom_post_columns($defaults) {
	var_dump($defaults);
  unset($defaults['categories']);
  unset($defaults['tags']);
  return $defaults;
}

function UKMwpat_remove_posts_meta_boxes() {
	
	// SHOW EXCERPT-BOX BY DEFAULT
	if( !is_super_admin() ) {
		$user = wp_get_current_user();
		update_user_option($user->ID, "metaboxhidden_post", array('commentstatusdiv','slugdiv','authordiv'), true);
	}

	remove_meta_box('tagsdiv-post_tag', 'post', 'normal');
}

function UKMwpat_change_allowed_tags() {
	global $allowedposttags;
	
	$allowedposttags['iframe'] = array (
	    'align'       => true,
	    'frameborder' => true,
	    'height'      => true,
	    'width'       => true,
	    'sandbox'     => true,
	    'src'         => true,
	    'class'       => true,
	    'id'          => true,
	    'style'       => true,
	    'border'      => true,
	    'webkitallowfullscreen'	=>true,
	    'mozallowfullscreen' 	=> true,
	    'allowfullscreen' 		=> true,
	);
}
