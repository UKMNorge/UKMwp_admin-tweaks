<?php

// Tweak.video_on_top
// This plugin enables the use of a video as the top-picture in a wordpress news post.
// Adds a box to the left where UKMTV-link can be copied in, maybe?

function UKMwpat_add_video_box() {
	#if (in_array(get_option('site_type'), array('kommune', 'fylke', 'land') ) ) {
		add_meta_box('ukm_video', 'Video som topp-bilde', 'ukm_top_video', 'post', 'side', 'low');
	#}
}

function ukm_top_video() {
	#echo '<span>Video som topp-bilde</span>';
	global $post;
	$selected = get_post_meta($post->ID, 'video_on_top', true);


	require_once('UKM/sql.class.php');
	require_once('UKM/tv.class.php');
	require_once('UKM/tv_files.class.php');
	require_once('UKM/monstring.class.php');
	///////////////////////////////////////////////////////////////
	$sql = new SQL("SELECT `cron_id` FROM `ukm_standalone_video` 
				WHERE `pl_id` = '#plid'
				ORDER BY `video_name` ASC",
				array('plid' => get_option('pl_id')));
	$res = $sql->run();
	echo 'Bruk denne videoen i stedet for topp-bilde:';
	echo '<select name="video_on_top" class="form-control" style="width: 100%;">';
	echo '<option '.(!$selected ? 'selected' : '') .' value="delete">Ingen video</option>';
	while( $r = mysql_fetch_assoc( $res )) {
		$film = new SQL("SELECT *
						FROM `ukm_standalone_video` 
						WHERE `cron_id` = '#cron_id'",
					array('cron_id' => $r['cron_id']));
		$film = $film->run('array');
		
		$TV = new tv(false, $film['cron_id']);
		if($TV->id) {
			echo '<option '. ($selected == $TV->id ? 'selected' : '').' value="'.$TV->id.'">'.$TV->title.'</option>';
		} else {
			echo '<option disabled="disabled" value="'.$TV->id.'">'.$TV->title.' (Må konverteres ferdig først)</option>';
		}
	}
	echo '</select><br />';
	echo '<span style="font-style: italic;">OBS: Du må fortsatt velge et bilde i boksen over - dette vises på forsiden.</span>';
}

function ukm_top_video_save() {
	global $post;

	$video_on_top = $_POST['video_on_top'];
	#var_dump($_POST);
	#var_dump($video_on_top);
	if ($video_on_top != 'delete') {
		// Do save
		update_post_meta($post->ID, 'video_on_top', $video_on_top);
	}
	else
		delete_post_meta($post->ID, 'video_on_top');

	#var_dump(get_post_meta($post->ID, 'video_on_top'));
	#var_dump($video_on_top);
	#throw new Exception ('Staaahp', 20007);
	return true;
}