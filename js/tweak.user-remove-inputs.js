jQuery(document).ready(function() {
    jQuery('.user-user-login-wrap').hide();
    jQuery('.user-rich-editing-wrap').hide();
    jQuery('.user-admin-color-wrap').hide();
    jQuery('.user-comment-shortcuts-wrap').hide();
    jQuery('.user-admin-bar-front-wrap').hide();
    jQuery('.user-language-wrap').hide();
    jQuery('.user-url-wrap').hide();
    jQuery('.user-nickname-wrap').hide();
    jQuery('.user-comment-shortcuts-wrap').hide();
    jQuery('.user-syntax-highlighting-wrap').hide();
    jQuery('.wp-heading-inline').hide();


    jQuery('h2').each((index, element) => {
        switch (jQuery(element).html()) {
            case 'Kontaktinfo':
            case 'Om deg':
            case 'Navn':
                jQuery(element).remove();
                break;
        }
    });
    jQuery('#pw-weak-text-label').html('Bruk av usikre passord er ikke tillatt');
    jQuery('.pw-checkbox').attr('disabled', true).prop('checked', false).removeProp('checked');
});