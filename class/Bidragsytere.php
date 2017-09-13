<?php

class Bidragsytere {

	// TODO: Legg til blogg-id som parameter, så vi kan jobbe på andre enn innlogget blogg.
	public function __construct($postID) {
		$this->postID = $postID;
		$this->bidragsyterListe = array();
		// Hent alle som hører til oss.
		$this->alle();
	}

	// Bidragsyterlisten er et array der nøkkelen er loginnavnet, og rollen i denne artikkelen er value.
	public $postID;
	private $bidragsyterListe = array();

	public function leggTil($loginName, $role, $append = true) {
		// Sjekk for tomme inputs
		if ( null == $loginName || null == $role || "" == $loginName || "" == $role)
			return false;

		// Dersom keyen finnes slår vi sammen teksten
		if ( null != $this->bidragsyterListe[$loginName] && $append) {
			$role = $this->bidragsyterListe[$loginName].', '.$role;
		}
		$this->bidragsyterListe[$loginName] = $role;
	}

	public function fjern($loginName) {
		unset($this->bidragsyterListe[$loginName]);

		// Dersom dette var den siste, slett også selve meta-feltet.
		if ( empty($this->bidragsyterListe) ) {
			delete_post_meta($this->postID, 'ukm_ma');
		}
	}

	public function lagre() {
		if ( empty( $this->bidragsyterListe ) ) {
			return;
		}

		$encodedList = json_encode($this->bidragsyterListe);
		// Hvis vi prøver å lagre akkurat det som er lagret vil update_post_meta returnere false, derfor slutter vi tidlig hvis det er ingen endringer.
		if ( get_post_meta($this->postID, 'ukm_ma', true) == $encodedList )
			return true;
		$saved = update_post_meta($this->postID, 'ukm_ma', $encodedList);
		return (bool)$saved;
	}

	/**
	 * Sjekker om denne bloggen skal ha spørsmål om bidragsytere.
	 */
	public function burdeHa() {
		$user = wp_get_current_user();
		// Fylkessider og kommunesider skal kun ha bidragsyter dersom brukernavnet har et punktum i seg, altså en redaksjonsbruker.
		if ( 'fylke' == get_option('site_type') || 'kommune' == get_option('site_type') ) {
			if ( strpos($user->user_login, '.') === FALSE ) {
				return false;
			}
		}
		// Alle andre skal ha bidragsytere pdd.
		return true;
	}

	public function har() {
		if ( empty( $this->alle() ) ) {
			return false;
		}
		return true;
	}

	public function harIkke() {
		return !$this->har();
	}

	public function alle() {
		if ( empty($this->bidragsyterListe) ) {
			$list = get_post_meta($this->postID, 'ukm_ma', true);
			$this->bidragsyterListe = json_decode($list, true);
		}
		return $this->bidragsyterListe;
	}

	/**
	 * Henter ut en liste over alle brukere som skal vises i 
	 * nedtrekkslistene.
	 */
	function alleMulige() {
		$admins = get_users(array('role' => 'administrator'));
		$authors = get_users(array('role' => 'author'));
		$editors = get_users(array('role' => 'editor'));
		$contributors = get_users(array('role' => 'contributor'));
		$producers = get_users(array('role' => 'ukm_produsent'));

		foreach($authors as $author) {
			$list[] = $author;
		}
		foreach($editors as $editor) {
			$list[] = $editor;
		}
		foreach($contributors as $contributor) {
			$list[] = $contributor;
		}
		foreach($producers as $producer) {
			$list[] = $producer;
		}
		foreach($admins as $admin) {
			$list[] = $admin;
		}

		return $list;
	}

}