<?php
/**
 * Multi-Author plugin tweak for UKM Norge
 * By Asgeir Stavik Hustad,
 * asgeirsh@ukmmedia.no
 * 04.05.2016
 */

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

	# Liste over alle bidragsytere som er med i artikkelen.
	#echo '<div>';
	echo '<div class="col-xs-8">';
	echo '<span>Bidragsyter 1 (rolle)</span>';
	echo '</div>';
	echo '<div class="col-xs-1"></div>';
	echo '<div class="col-xs-2">';
	echo '<small><a href="" class="btn btn-xs btn-danger">(fjern)</a></small>';
	echo '</div>';
	echo '<br><br>';
	echo '<div class="clearfix"></div>';
	#echo '</div>';	


	# Dropdown over alle med skriverettigheter til bloggen
	#$authors = wp_list_authors(array('echo' => false, 'html' => false)); # Henter kun authors, tror jeg?

	$authors = get_users(array('role' => 'author'));
	$editors = get_users(array('role' => 'editor'));
	$contributors = get_users(array('role' => 'contributor'));
	$producers = get_users(array('role' => 'ukm_produsent'));

	foreach($authors as $author) {
		$list[] = $author;
		var_dump($author);
		#echo $author->data->display_name;
	}
	foreach($editors as $editor) {
		$list[] = $editor;
		#echo $editor->data->display_name;
	}
	foreach($contributors as $contributor) {
		$list[] = $contributor;
		#echo $contributor->data->display_name;
	}
	foreach($producers as $producer) {
		$list[] = $producer;
	}

	echo '<label for="ukm_ma_author">Legg til ny bidragsyter:</label>';
	#echo '<div class="col-xs-12">';
	echo '<select class="form-control" id="ukm_ma_author" name="ukm_ma_author">';
	echo '<option selected disabled value="">Velg bidragsyter</option>';
	foreach($list as $object) {
		echo '<option value="'.$object->data->display_name.'">'.$object->data->display_name.'</option>';
	}
	echo '</select>';

	#echo '<div class="">';

	echo '<label for="ukm_ma_role">Rolle:</label>';
	echo '<input type="text" class="form-control" name="ukm_ma_role" id="ukm_ma_role">';
	#echo '</div>';
	echo '<br>';
	echo '<button type="submit" class="btn btn-success pull-right">Legg til</button>';
	#echo '</div>';
	echo '<div class="clearfix"></div>';
	#var_dump($authors);
	#var_dump($editors);
	#var_dump($contributors);
}

function UKMwpat_ma_save() {
	global $post;

	$author = $_POST['ukm_ma_author'];
	$role = $_POST['ukm_ma_role'];

	var_dump($author);
	var_dump($role);

	// Liste over nåværende bidragsytere
	$list = get_post_meta($post->ID, 'ukm_ma');
	$list = unserialize($list);

	// Sjekk om denne authoren finnes i oppsettet allerede
	if (in_array($author, $list)) {
		unset($list[$author]);
		$save = serialize($list);
		update_post_meta($post->ID, 'ukm_ma', $save);
		return true;
	}
	$list[$author] = $role;

	$save = serialize($list);
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