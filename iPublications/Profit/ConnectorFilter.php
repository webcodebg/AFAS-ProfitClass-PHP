<?php

namespace iPublications\Profit;
use \Exception;
use \SimpleXMLElement;

class ConnectorFilter {
	private $M_a_filterArray;
	private $M_i_or;
	private $M_i_fieldcount;

	const EQ 		= 1;
	const GT_OR_EQ 	= 2;
	const LT_OR_EQ 	= 3;
	const GT 		= 4;
	const LT 		= 5;
	const LIKE 		= 6;
	const NOTEQ 	= 7;
	const ISEMPTY 	= 8;
	const NOTEMPTY 	= 9;
	const START 	= 10;
	const NOTLIKE 	= 11;
	const NOTSTART 	= 12;
	const END 		= 13;
	const NOTEND 	= 14;
	const QUICK 	= 15;

	final public function __construct(){
		$this->M_i_or         = 0;
		$this->M_i_fieldcount = 0;
	}

	final public function add($P_s_field = '', $P_i_filtertype = 1, $P_s_value = null){
		$this->M_i_fieldcount++;
		$this->M_a_filterArray[$this->M_i_or][ trim($P_s_field) . "|" . $P_i_filtertype . "|" . $this->M_i_fieldcount] = ($P_s_value === null ? '' : $P_s_value);
	}

	final public function add_or(){
		$this->M_i_or++;
	}

	final public function get(){
		return $this->M_a_filterArray;
	}
	
	/*
		1 = 	Gelijk aan
		2 = 	Groter dan of gelijk aan
		3 = 	Kleiner dan of gelijk aan
		4 = 	Groter dan
		5 = 	Kleiner dan
		6 = 	Bevat
		7 = 	Ongelijk aan
		8 = 	Moet leeg zijn
		9 = 	Mag niet leeg zijn
		10 = 	Begint met
		11 = 	Bevat niet
		12 =	Begint niet met
		13 = 	Eindigt met tekst
		14 = 	Eindigt niet met tekst
		15 = 	Gebruik filter-criteria (zie de voorbeelden).
	*/

}