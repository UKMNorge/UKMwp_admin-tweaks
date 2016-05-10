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

	// Hvis dette er en slett-request
	if(isset($_GET['ukm_ma_fjern'])) {
		UKMwpat_ma_fjern($_GET['ukm_ma_fjern']);
	}

	# Liste over alle bidragsytere som er med i artikkelen.
	$old = get_post_meta($post->ID, 'ukm_ma', true);
	#var_dump($old);
	if($old) {
		$old = json_decode($old, true);

		#var_dump($old);
		foreach($old as $login => $role) {
			// Get user
			$user = get_user_by('login', $login);
			#var_dump($user);
			if(!$user) {
				// Hvis vi ikke fant en bruker med denne innloggings-IDen.
				#continue;
			}

			$args = '?';
			foreach($_GET as $key => $val) {
				$args .=  $key . '='.$val.'&';
			}
			$args = rtrim($args, '&');
			// List ut info:
			echo '<div class="col-xs-8">';
			echo '<span>'.$user->data->display_name.' ('.$role.')</span>';
			echo '</div>';
			echo '<div class="col-xs-1"></div>';
			echo '<div class="col-xs-2">';
			echo '<small><a href="'.$args.'&ukm_ma_fjern='.$user->data->user_login.'" class="btn btn-xs btn-danger">(fjern)</a></small>';
			echo '</div>';
			echo '<br><br>';
			echo '<div class="clearfix"></div>';
		}
	}		#echo '</div>';	


	# Dropdown over alle med skriverettigheter til bloggen
	#$authors = wp_list_authors(array('echo' => false, 'html' => false)); # Henter kun authors, tror jeg?

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

	echo '<label for="ukm_ma_author">Legg til / oppdater bidragsyter:</label>';
	#echo '<div class="col-xs-12">';
	echo '<select class="form-control" id="ukm_ma_author" name="ukm_ma_author">';
	echo '<option selected disabled value="">Velg bidragsyter</option>';
	foreach($list as $object) {
		echo '<option value="'.$object->data->user_login.'">'.$object->data->display_name.'</option>';
	}
	echo '</select>';

	#echo '<div class="">';

	echo '<label for="ukm_ma_role">Rolle:</label>';
	echo '<input type="text" class="form-control" name="ukm_ma_role" id="ukm_ma_role" placeholder="Tekst og foto...">';
	#echo '</div>';
	#echo '<br>';
	#echo '<button name="save" type="submit" class="btn btn-success pull-right">Legg til</button>';
	#echo '</div>';
	echo '<div class="clearfix"></div>';
	echo '<small style="font-style: italic;">Lagre artikkelen for å legge til ny bidragsyter</small>';
	#var_dump($authors);
	#var_dump($editors);
	#var_dump($contributors);
}

function UKMwpat_ma_save($post_id) {
	// Verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
  	// to do anything
  	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
    	return $post_id;

	global $post;

	$author = $_POST['ukm_ma_author'];
	$role = $_POST['ukm_ma_role'];

	#var_dump($author);
	#var_dump($role);

	// Liste over nåværende bidragsytere
	$list = get_post_meta($post->ID, 'ukm_ma', true);
	$list = json_decode($list, true);

	// Sjekk om denne authoren finnes i oppsettet allerede
	/*if (in_array($author, $list)) {
		unset($list[$author]);
		$save = json_encode($list);
		update_post_meta($post->ID, 'ukm_ma', $save);
		return true;
	}*/
	$list[$author] = $role;

	$save = json_encode($list);
	update_post_meta($post->ID, 'ukm_ma', $save);

	#var_dump($_POST);
	#var_dump($video_on_top);
	/*if ($author != 'delete') {
		// Do save
		update_post_meta($post->ID, 'video_on_top', $video_on_top);
	}
	else
		delete_post_meta($post->ID, 'video_on_top');
*/
	#var_dump(get_post_meta($post->ID, 'video_on_top'));
	#var_dump($video_on_top);
	#throw new Exception ('Staaahp', 20007);
	return true;
}

function UKMwpat_ma_fjern($author) {
	global $post;

	// Liste over nåværende bidragsytere
	$list = get_post_meta($post->ID, 'ukm_ma', true);
	$list = json_decode($list, true);

	unset($list[$author]);

	$save = json_encode($list);
	update_post_meta($post->ID, 'ukm_ma', $save);
	return true;
}