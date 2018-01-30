<?php

function UKMwpat_login() {
	echo '<style type="text/css">
		h1 a {background-image:url(//grafikk.ukm.no/profil/logo/UKM_logo.png) !important; background-size: 140px 80px !important; min-height: 80px;
    min-width: 160px; margin:0 auto;}
		</style>';

}

function UKMwpat_lostpassword() {
	return '//'.UKM_HOSTNAME.'/glemt-passord/';
}