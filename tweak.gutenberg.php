<?php

function my_plugin_allowed_block_types($allowed_block_types, $post)
{
    return [
        'core/heading',
        'core/paragraph',
        'core/image',
        'core/html',
        'core/block',
        'core/separator',
        'core/preformatted',
        'core/gallery',
        'core/quote',
        'core/embed',
        'core/list'
        #'core/text-columns',
        #'core/video', Tas bort, da vi bruker embed i stedet for
        /*    'core/table',*/
        #'core/spacer',
        #'core/more',
        #'core/missing',
        #'core/button',
        #'core/columns',
        #'core/column',
    ];
}


/*
function my_plugin_block_categories($categories, $post)
{
    var_dump($categories);
    return $categories;
    return array_merge(
        $categories,
        array(
            array(
                'slug' => 'my-category',
                'title' => __('My category', 'my-plugin'),
                'icon'  => 'wordpress',
            ),
        )
    );
}
add_filter('block_categories', 'my_plugin_block_categories', 10, 2);
*/
add_filter('allowed_block_types', 'my_plugin_allowed_block_types', 10, 2);


function UKMwpat_gutenberg_mod() {
    wp_enqueue_script(
        'my-plugin-blacklist-blocks',
        plugins_url( 'js/gutenberg-lite.js', __FILE__ ),
        array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' )
    );
}
add_action( 'enqueue_block_editor_assets', 'UKMwpat_gutenberg_mod' );