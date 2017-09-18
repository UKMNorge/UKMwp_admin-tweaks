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
	wp_enqueue_script( 'UKMwpat_fastlivefilterjs', plugin_dir_url( __FILE__ ) . "js/fastlivefilter.jquery.js");
}

// Denne funksjonen trigges første gang posten publiseres.
// Skal sjekke om all informasjon vi vil ha er på plass, 
// og hvis ikke redirecte oss til en ny side der vi kan fylle inn den manglende informasjonen.
function UKMwpat_req_hook( $ID, $post ) {
	require_once('class/Bidragsytere.php');
	$user = wp_get_current_user();
	$bidragsytere = new Bidragsytere($post->ID);
	if ( $bidragsytere->burdeHa() && $bidragsytere->harIkke() 
		|| !has_post_thumbnail($post->ID)
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
	$post = get_post($_GET['id']);
	if("POST" == $_SERVER['REQUEST_METHOD']) {
		$saved = UKMwpat_req_save();
		// Redirect to editor.
		if ( empty($saved['errors']) && false != $saved ) {
			echo ("<script>location.href='post.php?post=".$post->ID."&action=edit'</script>");
			die();
			$TWIGdata = $saved;
		}
		else {
			echo ("<script>location.href='post.php?post=".$post->ID."&action=edit'</script>");
			die();
			$TWIGdata = $saved;
		}
	}

	require_once('class/Bidragsytere.php');
	$bidragsytere = new Bidragsytere($post->ID);

	// Hvilken type sak er dette?
	$TWIGdata['missingPostType'] = !hasPostType($post->ID);
	$monstring = new monstring_v2(get_option('pl_id'));
	$TWIGdata['deltakerliste'] = $monstring->getInnslag()->getAll();

	// Har du husket å legge inn forsidebilde?
	$TWIGdata['missingThumbnail'] = !has_post_thumbnail($post->ID);
	$TWIGdata['bildePreview'] = '';

	// Sjekk om vi mangler bidragsytere:

	$TWIGdata['shouldHaveContributors'] = $bidragsytere->burdeHa();
	$TWIGdata['missingContributors'] = $bidragsytere->harIkke();
	$TWIGdata['contributorList'] = $bidragsytere->alleMulige();

	// Ta med oss de vi har av contributors dersom det er noen.
	if ( $TWIGdata['shouldHaveContributors'] && !$TWIGdata['missingContributors'] ) {
		$TWIGdata['contributors'] = $bidragsytere->alle();
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
	if ( in_array("bidragsytere", $fields) && !empty($_POST['rolle']) ) {
		require_once('class/Bidragsytere.php');
		$bidragsytere = new Bidragsytere($postID);

		$roller = $_POST['rolle'];
		$loginNames = $_POST['loginName'];

		for($i = 0; $i < sizeof($roller); $i++) {
			$bidragsytere->leggTil($loginNames[$i], $roller[$i]);
		}
		$saveContributors = $bidragsytere->lagre();

		#$ukm_ma = array_fill_keys($loginNames, $roller);
		#$savedContributors = saveContributors($postID, $ukm_ma);
		if ( false == $savedContributors ) {
			$output['errors'][] = "Klarte ikke å lagre bidragsytere!";
		} else {
			$output['success'][] = "Lagret bidragsytere.";
		}
	}

	// Save thumbnail:
	if ( in_array("forsidebilde", $fields) && null != $_POST['upload_id'] ) {
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

function getMentions($postID) {
	require_once('UKM/related.class.php');
	throw new Exception("Not implemented");
}

function saveMentions($postID, $mentions) {
	require_once('UKM/related.class.php');

	$post = get_post($postID);
	foreach($mentions as $bid) {
		$relate = new related($bid);
		$relate->set($postID, 'post', array('title'=>base64_encode($post->post_title),'link'=>$post->guid) );
	}
	
	return true;
}
