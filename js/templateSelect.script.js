jQuery(document).ready(function() { 
	jQuery("#ukm_post_layout_style").change(function(clicked) { 
		console.log(jQuery("#ukm_post_layout_style").val());
		if (jQuery("#ukm_post_layout_style").val() == "sidemedmeny" || jQuery('#ukm_post_layout_style').val() == 'list') { 
			jQuery("#imageStuff").addClass("hidden");
			jQuery("#menuSelect").removeClass("hidden");
		} else if (jQuery("#ukm_post_layout_style").val() == "delete") {
			jQuery("#menuSelect").addClass("hidden");
			jQuery("#imageStuff").addClass("hidden");
		} 
		else {
			jQuery("#imageStuff").removeClass("hidden");
			jQuery("#menuSelect").addClass("hidden");
		}
	}); 
});