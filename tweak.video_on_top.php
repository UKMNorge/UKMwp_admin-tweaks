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

    echo 'Bruk denne videoen i stedet for topp-bilde:';

    if( get_option('pl_id') ) {
        require_once('UKM/sql.class.php');
        require_once('UKM/tv.class.php');
        require_once('UKM/tv_files.class.php');

        $sql = new SQL("SELECT * FROM `ukm_standalone_video` 
                    WHERE `pl_id` = '#plid'
                    ORDER BY `video_name` ASC",
                    array('plid' => get_option('pl_id')));
        $res = $sql->run();
        echo '<select name="video_on_top" class="form-control" style="width: 100%;" onChange="check(this);">';
        echo '<option '.(!$selected ? 'selected' : '') .' value="delete">Ingen video</option>';
        while( $film = SQL::fetch( $res )) {
            $TV = new tv(false, $film['cron_id']);
            if($TV->id) {
                echo '<option '. ($selected == $TV->id ? 'selected' : '').' value="'.$TV->id.'">'.$TV->title.'</option>';
            } else {
                echo '<option disabled="disabled" value="'.$TV->id.'">'.$TV->title.' (Må konverteres ferdig først)</option>';
            }
        }
        echo '<option value="egendefinert" '.($selected == 'egendefinert' ? 'selected' : '').'>Annen film</option>';
        echo '</select><br />';
    } else {
        echo '<input name="video_on_top" type="hidden" value="egendefinert" />';
    }

	if($selected == 'egendefinert') {
		$url = get_post_meta($post->ID, 'video_on_top_URL', true);
		echo '<div id="egendefinertURLBox" style="">URL til UKM-TV-film:';
		echo '<input type="text" class="form-control" name="egendefinertURL" id="egendefinertURL" value="'.$url.'">';	
		echo '</div>';
	} 
	else {
		echo '<div id="egendefinertURLBox" '. (get_option('pl_id') ? 'style="display:none;"' : '') .'>URL til UKM-TV-film:';
		echo '<input type="text" class="form-control" name="egendefinertURL" id="egendefinertURL">';
		echo '</div>'; 
	}
	echo '<span style="font-style: italic;">OBS: Du må fortsatt velge et bilde i boksen over - dette vises på forsiden.</span>';

    if( get_option('pl_id') ) {
        echo '<script>function check(elem) {if(elem.value == "egendefinert") { jQuery("#egendefinertURLBox").slideDown(); } else { jQuery("#egendefinertURLBox").slideUp();}}</script>';
    }
}

function ukm_top_video_save() {
	global $post;
	if( isset( $_POST['video_on_top'] ) ) {
		$video_on_top = $_POST['video_on_top'];
		#var_dump($_POST);
		#var_dump($video_on_top);
		if ($video_on_top != 'delete') {
			if($video_on_top == 'egendefinert') {
				if(!empty($_POST['egendefinertURL']))
					update_post_meta($post->ID, 'video_on_top_URL', $_POST['egendefinertURL']);
			}
			else {
				delete_post_meta($post->ID, 'video_on_top_URL');
			}
            // Do save
            if( !empty( $video_on_top ) ) {
                update_post_meta($post->ID, 'video_on_top', $video_on_top);
            }
		}
		else {
			delete_post_meta($post->ID, 'video_on_top');
			delete_post_meta($post->ID, 'video_on_top_URL');
		}
	
		#var_dump(get_post_meta($post->ID, 'video_on_top'));
		#var_dump($video_on_top);
		#throw new Exception ('Staaahp', 20007);
	}
	return true;
}