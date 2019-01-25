<?php
class UKMmenu_conditions {
	
	static $conditions = null;
	static $monstring = null;
	
	static $test_har_deltakere = null;
	static $test_er_registrert = null;
	static $test_er_startet = null;
	
	public static function setConditions( $conditions ) {
		if( is_array( $conditions ) ) {
			self::$conditions = $conditions;
			return;
		}
		self::$conditions = [];
	}
	
	public static function assert( $slug ) {
		if( isset( $_GET['debug'] ) ) {
			return true;
		}
		// Hvis ingen conditions definert, legg til
		if( !isset( self::$conditions[ $slug ] ) ) {
			return true;
		}

		// Sjekk conditions og test
		switch( self::$conditions[ $slug ] ) {
			case 'monstring_har_deltakere':
				if( self::_harMonstring() ) {
					return self::_getMonstring()->erRegistrert();
				}
			case 'monstring_er_registrert':
				if( self::_harMonstring() ) {
					return self::_testErRegistrert();
				}
				
			case 'monstring_er_startet':
				if( self::_harMonstring() ) {
					return self::_testErStartet();
				}
		}
		
		// Ingen conditions har feilet, legg til
		return true;
	}
	
	private static function _harMonstring() {
		return is_numeric( get_option('pl_id') );
	}
	
	private static function _getMonstring() {
		if( self::$monstring == null ) {
			self::$monstring = new monstring_v2( get_option('pl_id') );
		}
		return self::$monstring;
	}

	private static function _testErStartet() {
		if( self::$test_er_startet !== null ) {
			return self::$test_er_startet;
		}
		
		if( !self::_testErRegistrert() ) {
			self::$test_er_startet = false;
			return self::$test_er_startet;
		}
		
		self::$test_er_startet = self::_getMonstring()->erStartet();
		return self::$test_er_startet;
	}

	private static function _testErRegistrert() {
		if( self::$test_er_registrert !== null ) {
			return self::$test_er_registrert;
		}
		
		self::$test_er_registrert = self::_getMonstring()->erRegistrert();
		return self::$test_er_registrert;
	}
	
	private static function _testHarDeltakere() {
		// Returner allerede beregnet svar
		if( self::$test_har_deltakere !== null ) {
			return self::$test_har_deltakere;
		}
		
		// Ikke registrert mønstring = 0 deltakere
		if( !self::testErRegistrert() ) {
			self::$test_har_deltakere = false;
			return false;
		}
		
		self::$test_har_deltakere = self::_getMonstring()->getInnslag()->harInnslag();

		return self::$test_har_deltakere;
	}
}