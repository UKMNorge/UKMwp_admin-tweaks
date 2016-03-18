<?php
# the call looks like this: 
# return apply_filters( 'get_avatar', $avatar, $id_or_email, $size, $default, $alt );

function ukm_avatar($avatar, $id_or_email, $size, $default, $alt) {
/*	var_dump($avatar);
	var_dump($id_or_email);
	var_dump($size);
	var_dump($default);
	var_dump($alt);*/


	if(is_numeric($id_or_email)) {
		$user = new WP_user($id_or_email);
	} else {
		$user = get_user_by('email', $id_or_email);
	}
	
	if (!$user) {
		return $avatar;
	}

	// Sjekk om brukeren har lagret en avatar.
	$avatar_url = get_user_meta($user->ID, 'user_avatar', true);
	if(!$avatar_url) {
		return $avatar;
	}
	#var_dump($avatar_url);
	// Bygg egen avatar
	$avatar = '<img alt src="'.$avatar_url.'" class="avatar avatar-'.$size.' photo" height="'.$size.'" width="'.$size.'" />';
	return $avatar;
}