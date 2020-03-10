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

    echo '<div style="width: 98%">';


    echo '<select name="ukm_post_layout_style" id="ukm_post_layout_style">'.
            '<option value="delete">Ingen mal</option>'.
            '<option '. ($meta == 'sidemedmeny' ? 'selected' : '').' value="sidemedmeny">Side med meny</option>'.
            '<option '. ($meta == 'image_left' ? 'selected' : '').' value="image_left">Bilde til venstre</option>'.
            '<option '. ($meta == 'image_right' ? 'selected' : '').' value="image_right">Bilde til høyre</option>'.
            '<option '. ($meta == 'lead' ? 'selected' : '').' value="lead">Tekst til venstre</option>'.
            '<option '. ($meta == 'lead_center' ? 'selected' : '').' value="lead_center">Tekst sentrert</option>'.
            '<option '. ($meta == 'liste' ? 'selected' : '').' value="liste">Side med liste</option>'.
            '<option '. ($meta == 'list' ? 'selected' : '').' value="list">Liste-element</option>'.
        '</select>';

    echo '<span style="font-style: italic;">'.
    'Lagre innlegget for å oppdatere stilen.'.
    '</span>';

	// Hvis vi har et bilde, vis det her
	if( in_array($meta, array("image_left", "image_right", "lead", "lead_center")) ) {
		echo '<image src="'.$ukm_image_xs.'" id="ukm_post_layout_image" style="width: 100%"/><br />';
	}

	$key = get_post_meta($post->ID, "UKM_nav_menu", true);
	ukm_post_layout_selectMenu($key, $meta == "sidemedmeny");
	ukm_post_layout_imageButton();
    ukm_post_layout_icon($meta == 'list');
    ukm_post_layout_liste($meta=='liste');
    echo '</div>'.
        '<script>jQuery( window ).on( "load", function() { jQuery("#ukm_post_layout_style").change() });</script>'
        ;

	return true;
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
        echo '<div id="menuSelect" class="'.($visible ? '' : 'hidden').'">'.
            'Det finnes ingen menyer du kan velge.'.
            '</div>';
		return;
	}
    echo '<div id="menuSelect" class="'.($visible ? '' : 'hidden').'">';
	echo "<br />";
    echo '<select name="menuSelect">'.
        '<option value="blank">Ingen meny valgt</option>';
	foreach($menyer as $meny) {
		echo '<option '.($currentKey == $meny->term_id ? 'selected' : '').' value="'.$meny->term_id.'">'.$meny->name.'</option>';	
	}
    echo '</select>'.
        '<p style="margin-top:1em; float:right;"><a href="nav-menus.php" target="_blank" class="button">Rediger menyer</a></p>'.
        '<div class="clearfix"></div>'.
    '</div>';	
}

function ukm_post_layout_icon($visible) {
    global $post;

    $ikon = get_post_meta($post->ID, 'ikon');
    if( is_array($ikon) ) {
        $ikon = $ikon[0];
    }
    echo '<div id="ukm_post_layout_ikon" class="mt-3 '.($visible ? '' : 'hidden').'">'.
        'Skal liste-elementet ha ett ikon? I tilfelle kan du sette inn en emoji her: <br /> '.
        '<input type="text" style="width: 3em;" name="ukm_post_layout_ikon" value="'. $ikon .'" />'.
        '</div>';
}

function ukm_post_layout_liste($visible) {
    echo '<div id="ukm_post_layout_liste_helper" class="mt-3 '.($visible ? '' : 'hidden').'">'.
        'For å legge til elementer i listen, må du legge til en side med designmal: liste-element '.
        'som en underside til denne siden.'.
        '</div>';
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


    switch( $style ) {
        case 'list':
            if( get_post_meta($post->ID, 'ikon') ) {
                update_post_meta($post->ID, 'ikon', $_POST['ukm_post_layout_ikon']);
            } else {
                add_post_meta($post->ID, 'ikon', $_POST['ukm_post_layout_ikon']);
            }
        break;
    }
    
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