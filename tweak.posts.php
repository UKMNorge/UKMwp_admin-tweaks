<?php
function UKMwpat_custom_post_columns($defaults) {
#	var_dump($defaults);
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

    if( !is_super_admin( )) {
        remove_meta_box('trackbacksdiv', 'post', 'normal');
    }
    
	remove_meta_box( 'trackbacksdiv', 'post', 'normal' ); // Trackbacks meta box
	remove_meta_box( 'postcustom', 'post', 'normal' ); // Custom fields meta box
	remove_meta_box( 'commentsdiv', 'post', 'normal' ); // Comments meta box
	remove_meta_box( 'slugdiv', 'post', 'normal' );	// Slug meta box
	remove_meta_box( 'authordiv', 'post', 'normal' ); // Author meta box
	remove_meta_box( 'revisionsdiv', 'post', 'normal' ); // Revisions meta box
	remove_meta_box( 'formatdiv', 'post', 'normal' ); // Post format meta box
	remove_meta_box( 'commentstatusdiv', 'post', 'normal' ); // Comment status meta box
	remove_meta_box( 'categorydiv', 'post', 'side' ); // Category meta box
	remove_meta_box( 'tagsdiv-post_tag', 'post', 'normal' ); // Post tags meta box
	remove_meta_box( 'pageparentdiv', 'post', 'side' ); // Page attributes meta box
    

    remove_meta_box('tagsdiv-post_tag', 'post', 'side');
}
function UKMwpat_remove_post_type_support() {
    if( true ){#!is_super_admin( )) {
        remove_post_type_support( 'post', 'excerpt' );
    }

    unregister_taxonomy_for_object_type('post_tag', 'post');

    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'post', 'trackbacks' );    
    remove_post_type_support( 'post', 'custom-fields' );
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
