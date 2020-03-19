<?php

use UKMNorge\Wordpress\User;

require_once('UKM/Autoloader.php');

function UKMwpat_modify_user_table($columns) {
    unset($columns['ure_roles']);
    if(is_super_admin()) {
        $columns['active'] = 'Aktiv';
    }
    return $columns;
}

function UKMwpat_modify_user_column($output, $column_name, $user_id) {
    if(!is_super_admin()) {
    return $output;
    }
    if( $column_name == 'active' && !User::erAktiv($user_id)) {
        return $output .
            '<div id="aktiver_'. $user_id .'">'.
                '<small class="text-danger">Brukeren er deaktivert</small>'.
                '<br />'.
                '<a href="#" class="btn btn-xs btn-ukm userActivate" data-user-id="'. $user_id .'">'.
                    'aktiver'.
                '</a>'.
            '</div>'
        ;
    }
    
    return $output;
}