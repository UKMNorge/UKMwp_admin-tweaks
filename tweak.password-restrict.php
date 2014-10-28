<?php
/*
Author: Ryan Willis
Author URI: http://www.totallyryan.com
*/

/*
/--------------------------------------------------------------------\
|                                                                    |
| License: GPL                                                       |
|                                                                    |
| Restrict Password Changes (MultiSite) - Restricts password         |
| changes or resets to super administrators                          |
| Copyright (C) 2010, Ryan Willis,                                   |
| http://www.totallyryan.com                                         |
| All rights reserved.                                               |
|                                                                    |
| This program is free software; you can redistribute it and/or      |
| modify it under the terms of the GNU General Public License        |
| as published by the Free Software Foundation; either version 2     |
| of the License, or (at your option) any later version.             |
|                                                                    |
| This program is distributed in the hope that it will be useful,    |
| but WITHOUT ANY WARRANTY; without even the implied warranty of     |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the      |
| GNU General Public License for more details.                       |
|                                                                    |
| You should have received a copy of the GNU General Public License  |
| along with this program; if not, write to the                      |
| Free Software Foundation, Inc.                                     |
| 51 Franklin Street, Fifth Floor                                    |
| Boston, MA  02110-1301, USA                                        |
|                                                                    |
\--------------------------------------------------------------------/
*/

function tr_restrict_password_changes_prevent() {

		if(!is_super_admin()) {
			$_POST['pass1'] = '';
			$_POST['pass2'] = '';
		}

}

function tr_restrict_password_changes($val = null) {

	if(is_multisite()) {
		
		if(is_super_admin()) return true;
		else return false;

	} else {

		return true;

	}

}

function tr_restrict_password_reset() {
	return false;
}

function tr_remove_reset_link_init() {
	add_filter('gettext', 'tr_remove_reset_link');
}

function tr_remove_reset_link($text) {
	if(strpos($text, 'Lost your password') !== false) $text = str_replace('Lost your password', '', str_replace('Lost your password?', '', str_replace('Lost your password</a>?', '</a>', $text)));
	return $text;
}

?>