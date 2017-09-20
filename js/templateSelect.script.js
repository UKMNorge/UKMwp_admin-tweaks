jQuery(document).ready(function() { 
	jQuery("#ukm_post_layout_style").change(function(clicked) { 
		console.log(jQuery("#ukm_post_layout_style").val());
		if (jQuery("#ukm_post_layout_style").val() != "sidemedmeny") { 
			jQuery("#imageStuff").removeClass("hidden");
			jQuery("#menuSelect").addClass("hidden");
		} else {
			jQuery("#imageStuff").addClass("hidden");
			jQuery("#menuSelect").removeClass("hidden");
		}
	}); 
});