jQuery(document).ready(function(){

	jQuery('#imageedit').click(function(e) {
	    e.preventDefault();
	
	    var custom_uploader = wp.media({
	        title: 'Velg nytt bilde',
	        button: {
	            text: 'Bruk dette bildet'
	        },
	        multiple: false  // Set this to true to allow multiple files to be selected
	    })
	    .on('select', function() {
	        var attachment = custom_uploader.state().get('selection').first().toJSON();
	        jQuery('#ukm_post_layout_image').attr('src', attachment.url);
	        jQuery('#ukm_post_layout_image_url').val(attachment.url);
	        //console.log(attachment);
	        jQuery('#ukm_post_layout_attachment').val(attachment.id);
	    })
	    .open();
	});


});





