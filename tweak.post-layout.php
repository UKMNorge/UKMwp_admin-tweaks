<?php
/* 
Denne boksen lar brukeren velge layout for enkeltsider med samme layout som Festivalsidene.
Vises for alle sider, siden kun superadmin har tilgang til sider ("pages").
*/

function UKMwpat_add_layout_meta_box() {
	add_meta_box('ukm_layout', 'Designmal', 'ukm_post_layout', 'page', 'side', 'low');
}

function ukm_post_layout() {
	global $post;

	// Finn current meta-tag
    $meta = get_post_meta($post->ID, 'UKM_block');
    if( is_array($meta)) {
        $meta = $meta[0];
    }

    // Hvis det ikke er satt en UKMblock, prøv og se om viseng er støttet her
    if(!$meta) {
        $meta = get_post_meta($post->ID, 'UKMviseng');
    }

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

	echo '<div class="form-group">';
	$select .= '<select name="ukm_post_layout_style" id="ukm_post_layout_style" class="form-control">';
	$select .= '<option value="delete">Ingen mal</option>';
	$select .= '<option '. ($meta == 'sidemedmeny' ? 'selected' : '').' value="sidemedmeny">Side med meny</option>';
	$select .= '<option '. ($meta == 'image_left' ? 'selected' : '').' value="image_left">Bilde til venstre</option>';
	$select .= '<option '. ($meta == 'image_right' ? 'selected' : '').' value="image_right">Bilde til høyre</option>';
	$select .= '<option '. ($meta == 'lead' ? 'selected' : '').' value="lead">Tekst til venstre</option>';
	$select .= '<option '. ($meta == 'lead_center' ? 'selected' : '').' value="lead_center">Tekst sentrert</option>';
	$select .= '<option '. ($meta == 'liste' ? 'selected' : '').' value="list">Liste (foreldresiden til liste-elementer)</option>';
	$select .= '<option '. ($meta == 'list' ? 'selected' : '').' value="list">Liste-element</option>';
	$select .= '';
	$select .= '</select>';

	echo $select;	

	// Hvis vi har et bilde, vis det her
	if( in_array($meta, array("image_left", "image_right", "lead", "lead_center")) ) {
		echo '<image src="'.$ukm_image_xs.'" id="ukm_post_layout_image" style="width: 100%"/>';
	}

	$key = get_post_meta($post->ID, "UKM_nav_menu", true);
	ukm_post_layout_selectMenu($key, $meta == "sidemedmeny");
	ukm_post_layout_imageButton();
	
	$text = '<br><span style="font-style: italic;">';
	$text .= 'Lagre innlegget for å oppdatere stilen.';
	$text .= '</span>';
	echo $text;
	echo '<p style="margin-top:1em; float:right;"><a href="nav-menus.php" target="_blank" class="button">Rediger menyer</a></p>';
	echo '<div class="clearfix"></div>';
	echo '</div>';

	echo '<script>jQuery( window ).on( "load", function() { jQuery("#ukm_post_layout_style").change() });</script>';

	return $select;
}

function ukm_post_layout_imageButton() {
	echo '<div id="imageStuff" class="hidden">';
	$img = '<img style="width: 100%;" id="ukm_post_layout_image" src="'. ( $ukm_image_lg ? $ukm_image_lg : '').'">';
	$att = '<input type="hidden" name="ukm_post_layout_attachment" id="ukm_post_layout_attachment" value="'. ($att ? $att : '').'">';
	echo $img;
	echo $att;
	
	wp_enqueue_script('UKMMonstring_script',  plugin_dir_url( __FILE__ )  . 'js/monstring.script.js' );
	wp_enqueue_script('UKMtemplate_script',  plugin_dir_url( __FILE__ )  . 'js/templateSelect.script.js' );

	$btn = '<input class="button button-primary button-large" style="margin-top: 8px;" id="imageedit" type="button" value="Velg bilde">';	
	echo $btn;

	echo '</div>';
}

function ukm_post_layout_selectMenu($currentKey, $visible) {
	$menyer = wp_get_nav_menus();
	if (empty($menyer)) {
		echo '<span id="menuSelect" class="'.($visible ? '' : 'hidden').'">Det finnes ingen menyer du kan velge.</span>';
		return;
	}
	echo "<br />";
	$select = '<select name="menuSelect" id="menuSelect" class="'.($visible ? '' : 'hidden').' form-control">';
	$select .= '<option value="blank">Ingen meny valgt</option>';

	foreach($menyer as $index => $meny) {
		$select .= '<option '.($currentKey == $meny->term_id ? 'selected' : '').' value="'.$meny->term_id.'">'.$meny->name.'</option>';	
	}
	$select .= '</select>';
	echo $select;	
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

	## SKAL VI SLETTE?
	if ($style == 'delete') {
		// Fjern UKM_block-meta-tagger
		if (get_post_meta($post->ID, 'UKM_block')) {
			delete_post_meta($post->ID, 'UKM_block');
			delete_post_meta($post->ID, "UKM_nav_menu");
		}

		return false;
    }

    $viseng = ['liste'];
    
    // VISENG-STYLES
    if( in_array( $style, $viseng) ) {
        delete_post_meta($post->ID, 'UKM_block');
        if( get_post_meta($post->ID, 'UKMviseng')) {
            update_post_meta($post->ID, 'UKMviseng', $style);
        } else {
            add_post_meta($post->ID, 'UKMviseng', $style);
        }
    // BLOCK-STYLES
    } else {
        ### LAGRE TEMPLATE
        if (get_post_meta($post->ID, 'UKM_block')) {
            update_post_meta($post->ID, 'UKM_block', $style);
        }
        else {
            add_post_meta($post->ID, 'UKM_block', $style);
        }

        ### LAGRE ENTEN MENYVALG ELLER BILDEDETALJER
        if( "sidemedmeny" == $style) {
            update_post_meta($post->ID, "UKM_nav_menu", $_POST['menuSelect']);

            // Slett bildedetaljer?
            delete_post_meta($post->ID, "UKM_att");
            delete_post_meta($post->ID, "image_xs");
            delete_post_meta($post->ID, "image_sm");
            delete_post_meta($post->ID, "image_md");
            delete_post_meta($post->ID, "image_lg");
        }
        else {

            $att = $_POST['ukm_post_layout_attachment'];

            if (!is_numeric($att)) {
                return false;
            }

            delete_post_meta($post->ID, "UKM_nav_menu");

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
        }
    }
	
	
	return true;
}