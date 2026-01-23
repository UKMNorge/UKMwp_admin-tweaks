jQuery(document).ready(function() {
    jQuery('.send-varsling-fra-innlegg').on('click', function(event) {
        event.preventDefault();
        let postId = jQuery(event.currentTarget)[0].getAttribute('post-id');
    
        // Create a form element
        const form = document.createElement('form');
        form.method = 'post';
        form.action = 'wp-admin/admin.php?page=UKMSMS_gui'; // Set the destination URL

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'UKMSMS_wp_post_id'; // The name of the input field
        input.value = postId; // The value of the input field
        form.appendChild(input);

        document.body.appendChild(form);

        form.submit();
    });
});