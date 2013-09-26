<?php
function UKMwpat_custom_post_columns($defaults) {
  unset($defaults['categories']);
  unset($defaults['tags']);
  return $defaults;
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
