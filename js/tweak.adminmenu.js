jQuery(document).ready(function() {
    jQuery('.dashicons-wordpress-pink').addClass('dashicons-admin-tools');


    // Fikser click på meny på bruker panel når det er mobil
    jQuery('#wpadminbar.mobile .quicklinks #wp-admin-bar-root-default #wp-admin-bar-wp-logo').on('click', function(event) {
        // Redirect to the URL
        window.location.href = jQuery(event.currentTarget).find('a.ab-item').attr('href');

    });
    
});