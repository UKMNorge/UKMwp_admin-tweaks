<?php
function UKMwpat_modify_toolbar()
{
    /* @var $wp_admin_bar WP_Admin_Bar */
    global $wp_admin_bar;

    $nodes = [
        # Everthing child of WP logo
        'about', 'wporg', 'documentation', 'support-forums', 'feedback',
        # My sites
        'my-sites',
        # Comments
        'comments',
        # Add something
        'new-media',
        'new-page',
        'new-user',
        'new-post',
        'new-content',
        'archive',

        # Customize
        'customize',
        # ?
        #'wp-logo',
        'wp-logo-external',
        'my-sites-list',

        # Vis min side
        'view-site',
        'site-info',
        'site-name',
        'edit-site',

        # Vis inlegget (i rediger post)
        'view',
        'preview',

        # Network admin
        'network-admin',
        'network-admin-s',
        'network-admin-u',
        'network-admin-p',
        'network-admin-d',
        'network-admin-t',
        'network-admin-o',

        # Super admin
        'my-sites-super-admin',

        # Min profil
        'edit-profile',
        'my-account',
        'user-actions',
        'user-info',

        # Div
        'themes',
        'menus',
        'search',
        'updates',

        # Edit this page
        'edit',
    ];

    foreach ($nodes as $node) {
        $wp_admin_bar->remove_node($node);
    }
    if (is_user_admin()) {
        $wp_admin_bar->add_node(
            [
                'id' => 'wp-logo',
                'title' => '<img src="//grafikk.ukm.no/profil/logoer/UKM_logo_sort_0100.png" id="UKMlogo" />' .
                    'Kontrollpanel',
                'href' => user_admin_url()
            ]
        );
    } elseif (is_network_admin()) {
        $wp_admin_bar->add_node(
            [
                'id' => 'wp-logo',
                'title' => '<img src="//grafikk.ukm.no/profil/logoer/UKM_logo_sort_0100.png" id="UKMlogo" />' .
                    'UKM Norge-admin',
                'href' => network_admin_url()
            ]
        );
    } elseif (is_admin()) {
        $wp_admin_bar->add_node(
            [
                'id' => 'wp-logo',
                'title' => '<img src="//grafikk.ukm.no/profil/logoer/UKM_logo_sort_0100.png" id="UKMlogo"  />' .
                    get_bloginfo('name'),
                'href' => admin_url()
            ]
        );
    } else {
        $wp_admin_bar->add_node(
            [
                'id' => 'wp-logo',
                'title' => '<img src="//grafikk.ukm.no/profil/logoer/UKM_logo_hvit_0100.png" id="UKMlogo" style="width: 2.7em;margin-top: -.4em;margin-right: .5em;" />' .
                    get_bloginfo('name'),
                'href' => admin_url()
            ]
        );
    }

    $wp_admin_bar->add_node(
        [
            'id'    => 'user',
            'title' => '<span class="ab-icon dashicons dashicons-admin-users" style="margin-top: .1em;"></span>Kontrollpanel',
            'href'  => admin_url() . 'user/',
            'parent' => 'top-secondary'
        ]
    );

    if (is_super_admin()) {
        $wp_admin_bar->add_node(
            [
                'id'    => 'ukmnorge',
                'title' => '<span class="ab-icon dashicons dashicons-rest-api" style="margin-top: .1em;"></span> UKM Norge-admin',
                'href'  => network_admin_url(),
                'parent' => 'top-secondary'
            ]
        );
    }

    if (is_super_admin()) {
        // Legg til menyvalg for å redigere side mens vi ser på siden
        // WP default er kun i admin
        $wp_admin_bar->add_node(
            [
                'parent' => 'top-secondary',
                'id'     => 'edit-site',
                'title' => '<span class="ab-icon dashicons dashicons-edit" style="margin-top: .1em;"></span> Rediger siden',
                'href'   => network_admin_url('site-info.php?id=' . get_current_blog_id()),
            ]
        );
    } else {
        $wp_admin_bar->add_node(
            array(
                'id'    => 'ukm_support',
                'title' => '<span class="ab-icon dashicons dashicons-sos" style="margin-top: .1em;"></span> Brukerstøtte',
                'href'  => '/wp-admin/user/admin.php?page=UKMwpd_support',
                'parent' => 'top-secondary'
            )
        );
    }
    $wp_admin_bar->add_node(
        [
            'id'        => 'logout', // id of the existing child node
            'title'        => '<span class="ab-icon dashicons dashicons-no-alt" style="margin-top: .13em;"></span> Logg ut &nbsp;',
            'parent'    => 'top-secondary',
        ]
    );


    // TODO: should be moved to a class
    function set_node_title($id, $new_title)
    {
        global $wp_admin_bar;
        $new_network_admin = $wp_admin_bar->get_node($id);
        if (is_object($new_network_admin)) {
            $new_network_admin->title = $new_title;
            $wp_admin_bar->remove_node($id);
            $wp_admin_bar->add_node($new_network_admin);
        }
    }
    function reset_node_position($id)
    {
        global $wp_admin_bar;
        $new_network_admin = $wp_admin_bar->get_node($id);
        $wp_admin_bar->remove_node($id);
        $wp_admin_bar->add_node($new_network_admin);
    }

    reset_node_position("updates");
    reset_node_position("new-content");
}
