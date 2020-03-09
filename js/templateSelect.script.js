jQuery(document).ready(function() {
    jQuery("#ukm_post_layout_style").change(function(clicked) {
        var selected = jQuery('#ukm_post_layout_style').val();

        jQuery("#imageStuff").addClass("hidden");
        jQuery("#menuSelect").addClass("hidden");
        jQuery('#ukm_post_layout_ikon').addClass('hidden');
        jQuery('#ukm_post_layout_liste_helper').addClass('hidden');

        if (selected == "sidemedmeny") {
            jQuery("#menuSelect").removeClass("hidden");
        } else if (selected == 'image_left' || selected == 'image_right') {
            jQuery("#imageStuff").removeClass("hidden");
        } else if (selected == 'list') {
            jQuery('#ukm_post_layout_ikon').removeClass('hidden');
        } else if (selected == 'liste') {
            jQuery('#ukm_post_layout_liste_helper').removeClass('hidden');
        }
    });
});