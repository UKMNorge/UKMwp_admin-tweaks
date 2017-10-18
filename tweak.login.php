<?php

function UKMwpat_login() {
	echo '<style type="text/css">
		h1 a {background-image:url(//grafikk.ukm.no/profil/logo/UKM_logo.png) !important; background-size: 140px 80px !important; min-height: 80px;
    min-width: 160px; margin:0 auto;}
		</style>';

}

function UKMwpat_lostpassword() {
	// Echo noe
	$user_login = $_POST['log'];
	#var_dump($_POST);

	// TODO: Denne dumpes ut hvis det er feil passord, det skal den ikke gjøre (d'oh)
	$string = 'mailto:support@ukm.no';
	if ($user_login) 
		$string .= '?subject=Glemt arrangørpassord&body=Auto-epost fra systemet: Jeg har glemt passordet mitt! Brukernavn: '.$user_login.'. Kan dere hjelpe meg?';

	return htmlentities($string);
}