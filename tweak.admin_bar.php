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

        # Link til program
        'site-link',
    ];

    foreach ($nodes as $node) {
        $wp_admin_bar->remove_node($node);
    }
    if (is_user_admin()) {
        $wp_admin_bar->add_node(
            [
                'id' => 'wp-logo',
                'title' => '<img src="//grafikk.ukm.no/profil/logoer/UKM_logo_sort_0100.png" id="UKMlogo" />' .
                    '<span class="title-at-ukmlogo">Min side</span>',
                'href' => user_admin_url()
            ]
        );
    } elseif (is_network_admin()) {
        $wp_admin_bar->add_node(
            [
                'id' => 'wp-logo',
                'title' => '<img src="//grafikk.ukm.no/profil/logoer/UKM_logo_sort_0100.png" id="UKMlogo" />' .
                    '<span class="title-at-ukmlogo">UKM Norge-admin</span>',
                'href' => network_admin_url()
            ]
        );
    } elseif (is_admin()) {
        $wp_admin_bar->add_node(
            [
                'id' => 'wp-logo',
                'title' => '<div><img src="//grafikk.ukm.no/profil/logoer/UKM_logo_sort_0100.png" id="UKMlogo"  />' .
                    '<span class="title-at-ukmlogo">'. get_bloginfo('name') .'</span>',
                'href' => admin_url()
            ]
        );
    } else {
        $wp_admin_bar->add_node(
            [
                'id' => 'wp-logo',
                'title' => '<img src="//grafikk.ukm.no/profil/logoer/UKM_logo_hvit_0100.png" id="UKMlogo" style="width: 2.7em;margin-top: -.4em;margin-right: .5em;" />' .
                    '<span class="title-at-ukmlogo">'. get_bloginfo('name') .'</span>',
                'href' => admin_url()
            ]
        );
    }

    
    if (is_admin() && !is_user_admin() && !is_network_admin()) {
        // $wp_admin_bar->add_node(
        //     [
        //         'id'    => 'site-link',
        //         'title' => '<span class="ab-icon dashicons dashicons-admin-home" style="margin-top: .1em;"></span>Vis nettsiden',
        //         'href'  => admin_url() . '../'
        //     ]
        // );
    }   

    // $wp_admin_bar->add_node(
    //     [
    //         'id'    => 'user',
    //         'title' => '<span class="ab-icon dashicons dashicons-admin-users" style="margin-top: .1em;"></span>Min side',
    //         'href'  => admin_url() . 'user/',
    //         'parent' => 'top-secondary'
    //     ]
    // );



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

        $post_id = get_the_ID();
        $postPath = null;
        // Get the current post object
        if($post_id) {
            $current_post = get_post($post_id);
            $postPath = admin_url() . '/post.php/?post='. $current_post->ID .'&action=edit';
        }

        $wp_admin_bar->add_node(
            [
                'parent' => 'top-secondary',
                'id'     => 'edit-site',
                'title' => '<span class="ab-icon dashicons dashicons-edit" style="margin-top: .1em;"></span> Rediger siden',
                'href'   => $postPath ? $postPath : network_admin_url('site-info.php?id=' . get_current_blog_id()),
            ]
        );

        

        $args = array(
            'post_type'      => 'page', // Adjust to your custom post type if needed
            'post_parent'    => $post_id,
            'posts_per_page' => -1, // Retrieve all posts
        );
        
        // Hvis vi ikke er i nettverksadmin
        if (!is_network_admin()) {        
            // Henter subsider
            $child_posts_query = new WP_Query($args);

            if ($child_posts_query->have_posts()) {
                while ($child_posts_query->have_posts()) {
                    $child_posts_query->the_post();

                    $post_id = get_the_ID();
                    $postPathUnderside = null;

                    if($post_id) {
                        $current_post = get_post($post_id);
                        $postPathUnderside = admin_url() . '/post.php/?post='. $current_post->ID .'&action=edit';
                    }
                    
                    $wp_admin_bar->add_node(
                        [
                            'parent' => 'edit-site',
                            'id'     => 'edit-undersite' . get_the_ID(),
                            'title' => '<span>'.  get_the_title() .'</span>',
                            'href'   => $postPathUnderside ? $postPathUnderside : network_admin_url('site-info.php?id=' . get_current_blog_id()),
                        ]
                    );
                }

                wp_reset_postdata();
            }
        }

    } else {
        // $wp_admin_bar->add_node(
        //     array(
        //         'id'    => 'ukm_support',
        //         'title' => '<span class="ab-icon dashicons dashicons-sos" style="margin-top: .1em;"></span> Brukerstøtte',
        //         'href'  => '/wp-admin/user/admin.php?page=UKMwpd_support',
        //         'parent' => 'top-secondary'
        //     )
        // );
    }

    $logout_url = wp_nonce_url('/wp-login.php?action=logout', 'log-out');
    $wp_admin_bar->add_node(
        [
            'id'        => 'logout', // id of the existing child node
            'href' => '',
            'title'        => '
            <div id="buttonBrukerMenu" class="active">
               
              

                <button class="hamburger-button hamburger nav-item dropdown" style="background: transparent" id="login-meny" href="" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="hamburger-inner"></div>
                    <div class="hamburger-inner"></div>
                </button>
                <div id="arrangorBrukerMenu" class="hamburger-menu dropdown-menu dropdown-menu-right as-card-1" aria-labelledby="DropdownLoginLink">
                    <div class="mainpage">
                            <a href="//'. UKM_HOSTNAME .'/wp-admin/user/" class="dropdown-item drop-item">
                                Min side
                            </a>
                            <a href="//'. UKM_HOSTNAME .'/wp-admin/user/profile.php" class="dropdown-item drop-item">
                                Min profil
                            </a>
                            <a href="//'. UKM_HOSTNAME .'/wp-admin/user/admin.php?page=UKMwpd_support" class="dropdown-item drop-item">
                                Brukerstøtte
                            </a>
                            <a href="'. esc_url($logout_url) .'" class="dropdown-item drop-item">
                                Logg ut
                            </a>
                        </div>
                    </div>
            </div>
            ',
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