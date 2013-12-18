<?php

$_UKM_menu = array();
$_UKM_submenu = array();
$_UKM_scripts = array();
$_UKM_blocks = array( 'content' 	=> 30,
					  'resources' 	=> 100,
					  'monstring'	=> 200,
					  'norge'		=> 300,
					  'kommunikasjon'=>400,
					  'intranett'	=> 500
					);
$_UKM_separators = array( 199, 299, 399, 499);						
function UKM_add_menu_page( $block, $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position=90) {
	global $_UKM_menu, $_UKM_blocks;
	
	$position = $position + $_UKM_blocks[ $block ];
	
	while( isset( $_UKM_menu[$block][$position] ) ) {
		$position++;
	}
	
	$_UKM_menu[ $block ][$position]	= array('page_title'	=> $page_title,
									'menu_title'	=> $menu_title,
									'capability'	=> $capability,
									'menu_slug'		=> $menu_slug,
									'function'		=> $function,
									'icon_url'		=> $icon_url
									);
}

function UKM_add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function, $position=10 ) {
	global $_UKM_submenu;
	
	while( isset( $_UKM_submenu[ $position ] ) ) {
		$position++;
	}
	
	$_UKM_submenu[ $parent_slug ][] = array( 'parent_slug'	=> $parent_slug,
											 'page_title'	=> $page_title,
											 'menu_title'	=> $menu_title,
											 'capability'	=> $capability,
											 'menu_slug'	=> $menu_slug,
											 'function'		=> $function
										   );
}

function UKM_add_scripts_and_styles($function, $sns_function) {
	global $_UKM_scripts;
	$_UKM_scripts[ $function ][] = $sns_function;
}

function UKMwpat_addSnS($function, $page) {
	global $_UKM_scripts;
	
	if( isset( $_UKM_scripts[ $function ] ) ) {
		if( is_array( $_UKM_scripts[ $function ] ) ) {
			foreach( $_UKM_scripts[ $function ] as $sns_function ) {
				add_action( 'admin_print_styles-' . $page, $sns_function );					
			}
		}
	}
}

function UKMwpat_addSeparators() {
	global $_UKM_separators, $menu;
	foreach( $_UKM_separators as $ID ) {
		
		while(isset( $menu[$ID] )) {
			$ID++;
		}
		$menu[$ID] = array('','read',"separator{$ID}",'','wp-menu-separator');
	}
	
	ksort($menu);
}

function UKMwpat_admin_menu_build() {
	global $_UKM_menu, $_UKM_scripts, $_UKM_blocks, $_UKM_submenu;
	
	do_action('UKM_admin_menu');
	
	foreach( $_UKM_menu as $block => $menu_items ) {
		foreach( $menu_items as $position => $menu ) {
			$page = add_menu_page( $menu['page_title'],
								   $menu['menu_title'],# .'('.$position.')',
								   $menu['capability'],
								   $menu['menu_slug'],
								   $menu['function'],
								   $menu['icon_url'],
								   $position
								 );
			UKMwpat_addSnS($menu['function'], $page);
			
			if( isset( $_UKM_submenu[ $menu['menu_slug'] ] ) ) {
				foreach( $_UKM_submenu[ $menu['menu_slug'] ] as $position => $submenu ) {
					$subpage = add_submenu_page( $submenu['parent_slug'],
											  $submenu['page_title'],
											  $submenu['menu_title'],
											  $submenu['capability'],
											  $submenu['menu_slug'],
											  $submenu['function'],
											  $position
											);
					UKMwpat_addSnS($submenu['function'] , $subpage );
				}
			}
		}
	}
#	UKMwpat_addSeparators();	
}