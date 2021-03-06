<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Database\SQL\Delete;
use UKMNorge\Database\SQL\Query;

require_once('UKM/Autoloader.php');

function UKMwpat_add_tag_meta_box() {
	if( get_option('pl_id') ) {
		#add_meta_box('ukm_post', 'Innlegget handler om', 'ukm_post_info', 'post', 'side', 'low');
	}
}

## HANDLE SAVE
function ukmn_meta_box_save( $post_id ) {
	global $post;
	// verify if this is an auto save routine.  // If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if( !isset( $_POST['post_type'] ) ) return; // programmatisk oppretting av innhold har ikke denne
	// Check permissions
	if ( 'page' == $_POST['post_type'] ){ if ( !current_user_can( 'edit_page', $post_id ) ) return;  } 
	else { if ( !current_user_can( 'edit_post', $post_id ) ) return; }

	## IKKE LAGRE HVIS REVISJON, SJEKK OM DET FUNKER!!
	if( $post->post_type == 'revision' ) return; // Don't store custom data twice
	
 	// OK, we're authenticated: we need to find and save the data
	global $blog_id;
	
	if(empty($post->ID)||(int)$post->ID == 0)
		return false;
	if((int) $blog_id == 0)
		return false;
	
	require_once('UKM/related.class.php');		
	$del = new Delete('ukmno_wp_related',
					  array('blog_id'=>$blog_id,
					  		'post_id'=>$post->ID)
					  );
	$del->run();
	
	if(isset($_POST['ukmn_tagMe'])) {
		foreach($_POST['ukmn_tagMe'] as $i => $bid) {
			$relate = new related($bid);
			$relate->set($post->ID, 'post',array('title'=>base64_encode($post->post_title),'link'=>$post->guid));
		}
	}
}

## LIST ALL BANDS
function ukm_post_info() {
	global $post;

	$monstring = new Arrangement(intval(get_option('pl_id')));
	$bands = $monstring->innslag_alpha();
	
	echo '<label for="ukmn_b_id">'
		.'Hvis innlegget handler om ett eller flere innslag i mønstringen kan du krysse av for dette her.'
		.'<br /><br />'
		.'<strong>OBS: om innlegget er informasjon til deltakerne skal du IKKE krysse av her!</strong>'
		.'</label>'
		.'<br /><br />';

	$those_tagged=array();
	$tagged = new Query("SELECT `b_id` FROM `ukmno_wp_related`
						WHERE `post_type` = 'post'
						AND `post_id` = '#id'",
						array('id'=>$post->ID));
	$tagged = $tagged->run();

	while($t = Query::fetch($tagged))
		$those_tagged[] = $t['b_id'];

	if( is_array( $bands ) ) {
		foreach($bands as $i => $band) {
			echo '<label>'
				.'<input type="checkbox" name="ukmn_tagMe[]" '.(in_array($band['b_id'],$those_tagged)?'checked="checked"':'').' value="'.$band['b_id'].'" />'
				. ucwords(shortString($band['b_name'], 30))
				.'</label>'
				.'<br />';
		}
	}
}

function shortString( $str, $length = 14 ) {
	if( strlen( $str ) > $length ) {
		$separator = '...';
		$separatorlength = strlen($separator) ;
		$maxlength = $length-3;
		$start = $maxlength / 2 ;
		$trunc =  strlen($str) - $maxlength;
		return substr_replace($str, $separator, $start, $trunc);
	}
	return $str;
}

// DELETE FROM RELATED-TABLE
function UKMwpat_related_delete($pid) {
	global $blog_id;
	
	if((int)$pid == 0)
		return false; 
	
	$sql = new Delete('ukmno_wp_related', array('post_id'=>$pid, 'blog_id'=>$blog_id, 'post_type'=>'post'));
	$sql->run();
}
?>
