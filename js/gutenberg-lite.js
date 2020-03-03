const { addFilter } = wp.hooks;

addFilter(
    "blocks.registerBlockType",
    "ukmnorge/hideblocks",
    extendParagraphBlock
);

function extendParagraphBlock(settings, name) {
    switch (name) {
        case 'core/embed':
            settings.category = 'common';
            break;
        case 'core/separator':
            settings.category = 'formatting';
            break;
    }
    return settings;
}

wp.domReady(function() {
    wp.data.dispatch('core/edit-post').removeEditorPanel('taxonomy-panel-category');
    wp.data.dispatch('core/edit-post').removeEditorPanel('taxonomy-panel-post_tag');
    wp.data.dispatch('core/edit-post').removeEditorPanel('post-link');
});

/* FLYTTET TIL PHP
var allowedBlocks = [
    'core/paragraph',
    'core/image',
    'core/html',
    'core/video',
    'core/text-columns',
    'core/spacer',
    'core/block',
    'core/separator',
    'core/preformatted',
    'core/more',
    'core/missing',
    'core/heading',
    'core/gallery',
    'core/button',
    'core/columns',
    'core/column',
    'core/embed',
    'core/list'
];

wp.domReady(function() {
    console.log(wp.blocks.getBlockTypes());
    wp.blocks.getBlockTypes().forEach(function(blockType) {
        if (allowedBlocks.indexOf(blockType.name) === -1) {
            wp.blocks.unregisterBlockType(blockType.name);
        }
    });
});
*/