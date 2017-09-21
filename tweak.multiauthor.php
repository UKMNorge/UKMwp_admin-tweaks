<?php
/**
 * Multi-Author plugin tweak for UKM Norge
 * By Asgeir Stavik Hustad,
 * asgeirsh@ukmmedia.no
 * 04.05.2016
 */

// Lagre når posten lagres
add_action('save_post', 'UKMwpat_ma_save');

function UKMwpat_add_ma_box() {
	add_meta_box('ukm_ma', 'Bidragsytere', 'ukm_multiauthor', 'post', 'side', 'high');
}

function UKMwpat_add_ma_styles($hook) {
    if ( !in_array($hook, array('post.php', 'post-new.php')) ) {
        return;
    }
    wp_enqueue_style( 'WPbootstrap3_css' );
}

function ukm_multiauthor() {
	global $post;
	require_once('class/Bidragsytere.php');
	$bidragsytere = new Bidragsytere($post->ID);

	// Hvis dette er en slett-request
	if(isset($_GET['ukm_ma_fjern'])) {
		$bidragsytere->fjern($_GET['ukm_ma_fjern']);
	}

	if($bidragsytere->har()) {
		foreach($bidragsytere->alle() as $login => $role) {
			// Get user
			$user = get_user_by('login', $login);
			
			// Bygg URL-struktur
			$args = '?';
			foreach($_GET as $key => $val) {
				$args .=  $key . '='.$val.'&';
			}
			$args = rtrim($args, '&');
			// List ut info:
			echo '<div class="col-xs-8 col-sm-7">';
			echo '<span>'.$user->data->display_name.' ('.$role.')</span>';
			echo '</div>';
			echo '<div class="col-xs-1"></div>';
			echo '<div class="col-xs-2">';
			echo '<small><a href="'.$args.'&ukm_ma_fjern='.$user->data->user_login.'" class="btn btn-xs btn-danger">(fjern)</a></small>';
			echo '</div>';
			echo '<br><br>';
			echo '<div class="clearfix"></div>';
		}
	}


	# Dropdown over alle med skriverettigheter til bloggen
	echo '<label for="ukm_ma_author">Legg til / oppdater bidragsyter:</label>';
	#echo '<div class="col-xs-12">';
	echo '<select class="form-control" id="ukm_ma_author" name="ukm_ma_author">';
	echo '<option selected disabled value="">Velg bidragsyter</option>';
	foreach($bidragsytere->alleMulige() as $object) {
		echo '<option value="'.$object->data->user_login.'">'.$object->data->display_name.'</option>';
	}
	echo '</select>';

	echo '<label for="ukm_ma_role">Rolle:</label>';
	echo '<input type="text" class="form-control" name="ukm_ma_role" id="ukm_ma_role" placeholder="Tekst og foto...">';
	
	echo '<div class="clearfix"></div>';
	echo '<small style="font-style: italic;">Lagre artikkelen for å legge til ny bidragsyter</small>';
}

function UKMwpat_ma_save($post_id) {
	// Verify if this is an auto save routine. If it is our form has not been submitted, 
	// so we dont want to do anything
  	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
    	return $post_id;

	global $post;

	require_once('class/Bidragsytere.php');
	$bidragsytere = new Bidragsytere($post->ID);

	$bidragsytere->leggTil($_POST['ukm_ma_author'], $_POST['ukm_ma_role']);
	$bidragsytere->lagre();
	return true;
}

function UKMwpat_ma_fjern($author) {
	global $post;

	$bidragsytere = new Bidragsytere($post->ID);
	$bidragsytere->fjern($author);

	return true;
}