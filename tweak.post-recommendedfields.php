<?php

function UKMwpat_req_menu_hook() {
	add_submenu_page( 
		null,            			// -> Set to null - will hide menu link 
		'Nesten ferdig',    		// -> Page Title
		'Publiseringsinformasjon', 	// -> Title that would otherwise appear in the menu
		'editor', 					// -> Capability level
		'recommendedfields',   		// -> Still accessible via admin.php?page=menu_handle
		'UKMwpat_req_render' 		// -> To render the page
	);	
}

function UKMwpat_req_script() {
	wp_enqueue_script('bootstrap_js');
	wp_enqueue_style('bootstrap_css');
}

// Denne funksjonen trigges første gang posten publiseres.
// Skal sjekke om all informasjon vi vil ha er på plass, 
// og hvis ikke redirecte oss til en ny side der vi kan fylle inn den manglende informasjonen.
function UKMwpat_req_hook( $ID, $post) {
	$user = wp_get_current_user();

	$shouldRedirect = false;
	if ( shouldHaveContributors() && missingContributors() ) {
		$shouldRedirect = true;
	}

 	if ( !has_post_thumbnail($post) ) {
 		$shouldRedirect = true;
 	}

	if( !hasPostType($post->ID) ) {
		$shouldRedirect = true;
	}

	if ( $shouldRedirect ) {
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
		if ($saved) {
			// Publish the post and redirect to editor.
			var_dump("Publish and redir.");
			die();
		}
		else {
			// Output an error and show the page again.
			var_dump("error.");
		}
	}

	$post = get_post($_GET['id']);

	// Hvilken type sak er dette?
	$TWIGdata['missingPostType'] = !hasPostType($post->ID);

	// Har du husket å legge inn forsidebilde?
	$TWIGdata['missingThumbnail'] = !has_post_thumbnail($post);

	// Sjekk om vi mangler bidragsytere:
	$TWIGdata['shouldHaveContributors'] = shouldHaveContributors();
	$TWIGdata['missingContributors'] = missingContributors($post->ID);
	// Ta med oss de vi har av contributors dersom det er noen.
	if ( $TWIGdata['shouldHaveContributors'] && !$TWIGdata['missingContributors'] ) {
		$TWIGdata['contributors'] = $getContributors();
	}

	echo TWIG('recommendedfields.html.twig', $TWIGdata, dirname(__FILE__) );
}

function UKMwpat_req_save() {
	$postID = $_GET['id'];
	return false;
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
	} elseif ( NULL == $ukm_ma[0] ) {
		return true;
	} else {
		return false;
	}
}

function getContributors($postID) {
	$list = get_post_meta($post->ID, 'ukm_ma', true);
	$ukm_ma = json_decode($list, true);
	return $ukm_ma;
}

function hasPostType($postID) {
	$ukm_post_type = get_post_meta($post->ID, 'ukm_post_type');
	$TWIGdata['postType'] = $ukm_post_type;
}