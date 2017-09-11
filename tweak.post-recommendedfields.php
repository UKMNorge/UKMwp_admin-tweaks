<?php

function UKMwpat_req_menu_hook() {
	add_submenu_page( 
		null,            			// Satt til null vil dette skjule siden fra menyen
		'Nesten ferdig',
		'Publiseringsinformasjon',
		'editor', 					
		'recommendedfields',   		// Slug for å nå siden.
		'UKMwpat_req_render'
	);	
}

function UKMwpat_req_script() {
	wp_enqueue_script('bootstrap_js');
	wp_enqueue_style('bootstrap_css');
	wp_enqueue_media();
}

// Denne funksjonen trigges første gang posten publiseres.
// Skal sjekke om all informasjon vi vil ha er på plass, 
// og hvis ikke redirecte oss til en ny side der vi kan fylle inn den manglende informasjonen.
function UKMwpat_req_hook( $ID, $post) {
	$user = wp_get_current_user();

	if ( shouldHaveContributors() && missingContributors()
		|| !has_post_thumbnail($post)
		|| !hasPostType($post->ID) )
	{
		$newURL = "admin.php?page=recommendedfields&id=".$post->ID;
		header('Location: '. $newURL);
		die();
	}

	// Hvis alt er OK, ikke avbryt.
	return;
}

## Renders the actual input-page.
function UKMwpat_req_render() {
	if("POST" == $_SERVER['REQUEST_METHOD']) {
		$saved = UKMwpat_req_save();
		if ( empty($saved['errors']) && false != $saved ) {
			// Publish the post and redirect to editor.
			var_dump("Publish and redir.");
			$TWIGdata = $saved;
			#die();
		}
		else {
			$TWIGdata = $saved;
			// Output an error and show the page again.
			var_dump("error.");
		}
	}

	$post = get_post($_GET['id']);

	// Hvilken type sak er dette?
	$TWIGdata['missingPostType'] = !hasPostType($post->ID);
	$monstring = new monstring_v2(get_option('pl_id'));
	$TWIGdata['deltakerliste'] = $monstring->getInnslag()->getAll();

	// Har du husket å legge inn forsidebilde?
	$TWIGdata['missingThumbnail'] = !has_post_thumbnail($post->ID);
	$TWIGdata['bildePreview'] = '';

	// Sjekk om vi mangler bidragsytere:
	$TWIGdata['shouldHaveContributors'] = shouldHaveContributors();
	$TWIGdata['missingContributors'] = missingContributors($post->ID);
	$TWIGdata['contributorList'] = getPossibleContributors();
	// Ta med oss de vi har av contributors dersom det er noen.
	if ( $TWIGdata['shouldHaveContributors'] && !$TWIGdata['missingContributors'] ) {
		$TWIGdata['contributors'] = $getContributors();
	}

	echo TWIG('recommendedfields.html.twig', $TWIGdata, dirname(__FILE__) );
}

function UKMwpat_req_save() {
	$postID = $_GET['id'];
	$output = false;

	$fields = explode(' ', $_POST['fields']);

	// Save post-type:
	if ( in_array("postType", $fields) ) {
		$postType = $_POST['postType'];	
		// Array med alle omtalte innslags-IDer.
		$mentionsList = $_POST['mentions'];

		// Save post type:
		$savedPostType = savePostType($postID, $postType);
		if ( false == $savedPostType ) {
			$output['errors'][] = "Klarte ikke å lagre post-typen!";
		} else {
			$output['success'][] = "Lagret post-typen.";
		}

		if ($postType == "news") {
			$savedMentions = saveMentions($postID, $mentionsList);
			if ( false == $savedMentions ) {
				$output['errors'][] = "Klarte ikke å lagre omtale-informasjon!";
			} else {
				$output['success'][] = "Lagret omtale-informasjon.";
			}		
		}
	
	}

	// Contributors - ukm_ma-fancy stuff
	if ( in_array("bidragsytere", $fields) ) {
		$roller = $_POST['rolle'];
		$loginNames = $_POST['loginName'];
		$ukm_ma = array_fill_keys($loginNames, $roller);

		$savedContributors = saveContributors($postID, $ukm_ma);
		if ( false == $savedContributors ) {
			$output['errors'][] = "Klarte ikke å lagre bidragsytere!";
		} else {
			$output['success'][] = "Lagret bidragsytere.";
		}
	}

	// Save thumbnail:
	if ( in_array("bidragsytere", $fields) ) {
		$thumbnailID = $_POST['upload_id'];
		$savedThumbnail = set_post_thumbnail($postID, $thumbnailID);
		if ( false == $savedThumbnail ) {
			$output['errors'][] = "Klarte ikke å lagre framsidebildet!";
		} else {
			$output['success'][] = "Lagret forsidebildet.";
		}
	}

	return $output;
}

function shouldHaveContributors() {
	$user = wp_get_current_user();
	// Fylkessider og kommunesider skal kun ha bidragsyter dersom brukernavnet har et punktum i seg, altså en redaksjonsbruker.
	if ( 'fylke' == get_option('site_type') || 'kommune' == get_option('site_type') ) {
		if ( strpos($user->user_login, '.') === FALSE ) {
			return false;
		}
	}
	// Alle andre skal ha bidragsytere
	return true;
}

function missingContributors ($postID) {
	$ukm_ma = getContributors($postID);
	if (empty($ukm_ma) ) {
		return true;
	} elseif ( NULL == reset($ukm_ma) ) {
		// Workaround for bug med at tom bidragsyter-liste blir et array med et tomt element. Bør fikses.
		return true;
	} else {
		return false;
	}
}

function getContributors($postID) {
	$list = get_post_meta($postID, 'ukm_ma', true);
	$ukm_ma = json_decode($list, true);
	return $ukm_ma;
}

function getPossibleContributors() {
	$admins = get_users(array('role' => 'administrator'));
	$authors = get_users(array('role' => 'author'));
	$editors = get_users(array('role' => 'editor'));
	$contributors = get_users(array('role' => 'contributor'));
	$producers = get_users(array('role' => 'ukm_produsent'));

	foreach($authors as $author) {
		$list[] = $author;
		#var_dump($author);
		#echo $author->data->display_name;
	}
	foreach($editors as $editor) {
		$list[] = $editor;
		#var_dump($editor);
		#echo $editor->data->display_name;
	}
	foreach($contributors as $contributor) {
		$list[] = $contributor;
		#echo $contributor->data->display_name;
	}
	foreach($producers as $producer) {
		$list[] = $producer;
	}
	foreach($admins as $admin) {
		$list[] = $admin;
	}
	return $list;
}

function saveContributors($postID, $list) {
	// LIST = $list[$author] = $role. $author = String, brukernavn (innlogging). $role = String, rollebeskrivelse.
	if(!is_array($list))
		return false;

	foreach($list as $key => $val) {
		if ( is_array($val) )
			$list[$key] = implode(', ', $val);
	}

	$encodedList = json_encode($list);

	// Hvis vi prøver å lagre akkurat det som er lagret vil update_post_meta returnere false, derfor slutter vi tidlig hvis det er ingen endringer.
	if ( get_post_meta($postID, 'ukm_ma', true) == $encodedList )
		return true;
	$saved = update_post_meta($postID, 'ukm_ma', $encodedList);
	return (bool)$saved;

}

function hasPostType($postID) {
	$ukm_post_type = get_post_meta($postID, 'ukm_post_type');

	return (bool)$ukm_post_type;
}

function getPostType($postID) {
	return get_post_meta($postID, 'ukm_post_type');
}

function savePostType($postID, $postType) {
	if( null == $postType || "" == $postType )
		return false;
	if ( get_post_meta($postID, 'ukm_post_type', true) == $postType ) 
		return true;

	$saved = update_post_meta($postID, 'ukm_post_type', $postType);
	return (bool)$saved;
}

function saveMentions($postID, $mentions) {
	throw new Exception("Ikke implementert");
}
