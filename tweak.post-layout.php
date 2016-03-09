<?php
/* 
Denne boksen lar brukeren velge layout for enkeltsider med samme layout som Festivalsidene.
Vises for alle sider, siden kun superadmin har tilgang til sider ("pages").
*/

function UKMwpat_add_layout_meta_box() {
	add_meta_box('ukm_layout', 'Designmal', 'ukm_post_layout', 'page', 'side', 'low');
}

function ukm_post_layout() {
	require_once('UKM/form.class.php');
	global $post;
	#var_dump($post);

	// Finn current meta-tag
	$meta = get_post_meta($post->ID, 'UKM_block');
	$meta = $meta[0];

	$att = get_post_meta($post->ID, 'UKM_att');
	$att = $att[0];
	$ukm_image_xs = get_post_meta($post->ID, 'image_xs');
	$ukm_image_xs = $ukm_image_xs[0];
	$ukm_image_sm = get_post_meta($post->ID, 'image_sm');
	$ukm_image_sm = $ukm_image_sm[0];
	$ukm_image_md = get_post_meta($post->ID, 'image_md');
	$ukm_image_md = $ukm_image_md[0];
	$ukm_image_lg = get_post_meta($post->ID, 'image_lg');
	$ukm_image_lg = $ukm_image_lg[0];
	#var_dump($meta);
		
	$select .= '<select name="ukm_post_layout_style" style="width: 100%;">';
	$select .= '<option value="delete">Ingen mal</option>';
	$select .= '<option '. ($meta == 'image_left' ? 'selected' : '').' value="image_left">Bilde til venstre</option>';
	$select .= '<option '. ($meta == 'image_right' ? 'selected' : '').' value="image_right">Bilde til høyre</option>';
	$select .= '<option '. ($meta == 'lead' ? 'selected' : '').' value="lead">Tekst til venstre</option>';
	$select .= '<option '. ($meta == 'lead_center' ? 'selected' : '').' value="lead_center">Tekst sentrert</option>';
	$select .= '';
	$select .= '</select>';


	echo $select;	

	$img = '<img style="width: 100%;" id="ukm_post_layout_image" src="'. ( $ukm_image_lg ? $ukm_image_lg : '').'">';
	#$imgURL = '<input type="hidden" id="ukm_post_layout_image_url" value="'. ( $ukm_image_lg ? $ukm_image_lg : '' ).'">';
	$att = '<input type="hidden" name="ukm_post_layout_attachment" id="ukm_post_layout_attachment" value="'. ($att ? $att : '').'">';
	echo $img;
	#echo $imgURL;
	echo $att;
	wp_enqueue_script('UKMMonstring_script',  plugin_dir_url( __FILE__ )  . 'js/monstring.script.js' );

	$btn = '<input class="button button-primary button-large" style="margin-top: 8px;" id="imageedit" type="button" value="Velg bilde">';	
	echo $btn;

	$text = '<br><span style="font-style: italic;">';
	$text .= 'Lagre innlegget for å oppdatere stilen.';
	$text .= '</span>';
	echo $text;

	return $select;
}

// Trigges når man lagrer posten
function ukm_post_layout_save() {
	global $post;
	#var_dump($_POST);

	#throw new Exception('Oooops. '.$_POST['ukm_post_layout_style']);
	// Sjekk om det er meningen å lagre
	if (!isset($_POST['ukm_post_layout_style']))
		return false;

	$style = $_POST['ukm_post_layout_style'];

	if ($style == 'delete') {
		// Fjern UKM_block-meta-tagger
		if (get_post_meta($post->ID, 'UKM_block'))
			delete_post_meta($post->ID, 'UKM_block');

		return false;
	}

	if (get_post_meta($post->ID, 'UKM_block'))
		update_post_meta($post->ID, 'UKM_block', $style);
	else 
		add_post_meta($post->ID, 'UKM_block', $style);

	$att = $_POST['ukm_post_layout_attachment'];

	if (!is_numeric($att)) {
		return false;
	}

	#var_dump($att);
	#throw new Exception("Staahp");
	$image_xs = wp_get_attachment_url($att, 'thumbnail');
	$image_sm = wp_get_attachment_url($att, 'medium');
	$image_md = wp_get_attachment_url($att, 'large');
	$image_lg = wp_get_attachment_url($att, 'full');

	## DO SAVE
	if (get_post_meta($post->ID, 'UKM_att'))
		update_post_meta($post->ID, 'UKM_att', $att);
	else
		add_post_meta($post->ID, 'UKM_att', $att);

	if (get_post_meta($post->ID, 'image_xs'))
		update_post_meta($post->ID, 'image_xs', $image_xs);
	else 
		add_post_meta($post->ID, 'image_xs', $image_xs);
	
	if (get_post_meta($post->ID, 'image_sm'))
		update_post_meta($post->ID, 'image_sm', $image_sm);
	else 
		add_post_meta($post->ID, 'image_sm', $image_sm);

	if (get_post_meta($post->ID, 'image_md'))
		update_post_meta($post->ID, 'image_md', $image_md);
	else 
		add_post_meta($post->ID, 'image_md', $image_md);
	
	if (get_post_meta($post->ID, 'image_lg'))
		update_post_meta($post->ID, 'image_lg', $image_lg);
	else 
		add_post_meta($post->ID, 'image_lg', $image_lg);
	
	return true;
}