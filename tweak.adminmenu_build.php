<?php
require_once('UKM/monstring.class.php');

require_once( dirname( __FILE__ ) .'/tweak.adminmenu_conditions.php');

$_UKM_menu = array();
$_UKM_submenu = array();
$_UKM_scripts = array();
$_UKM_blocks = array( 'content' 	=> 100,
					  'resources' 	=> 200,
					  'monstring'	=> 300,
					  'kommunikasjon'=>400,
					  'norge'		=> 600,
					  'festivalen'	=> 500,
					  'intranett'	=> 700
					);
$_UKM_separators = array( 99, 199, 299, 399, 499, 599, 699);						
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

	if( is_array( $function ) ) {
		$function = implode('_', $function);
	}

	$_UKM_scripts[ $function ][] = $sns_function;
}

function UKMwpat_addSnS($function, $page) {
	global $_UKM_scripts;
	
	if( is_array( $function ) ) {
		$function = implode('_', $function);
	}

	if( isset( $_UKM_scripts[ $function ] ) ) {
		if( is_array( $_UKM_scripts[ $function ] ) ) {
			foreach( $_UKM_scripts[ $function ] as $sns_function ) {
				#echo 'add_action( \'admin_print_styles-\' . '. $page.', '. $sns_function .' ); <br />';
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
	if( isset( $menu[103] ) && $menu[103][4] == 'wp-menu-separator' ) {
		unset( $menu[103] );
	}
}

function UKMwpat_admin_menu_build() {
	global $_UKM_menu, $_UKM_scripts, $_UKM_blocks, $_UKM_submenu;
		
	do_action('UKM_admin_menu');
	UKMmenu_conditions::setConditions( apply_filters('UKM_admin_menu_conditions', []) );
		
	foreach( $_UKM_menu as $block => $menu_items ) {
		if( !in_array( get_option('site_type'), array('kommune','fylke','land')) && $block != 'content') {
			continue;
		}
		foreach( $menu_items as $position => $menu ) {
			
			if( !UKMmenu_conditions::assert( $menu['menu_slug'] ) ) {
				continue;
			}
			
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