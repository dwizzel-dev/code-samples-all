<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	ERROR STRINGS AND JSON ERRORS

*/


class Errors {
	
	private $strGeneric; 
	private $arr;
	private $reg;
	private $className = 'Errors';

	//-------------------------------------------------------------------------------------------------------------	
	public function __construct(&$reg){
		$this->reg = $reg;
		$this->strGeneric = $this->gettext('sorry! a generic error has occured');
		$this->arr = array(
			
			//[100-200] erreur generic
			103 => $this->gettext('section is missing'),
			104 => $this->gettext('service is missing'),
			105 => $this->gettext('service not available'),
			106 => $this->gettext('section not available'),

			//[200-300] translation errors
			201 => $this->gettext('no items found'),
			202 => $this->gettext('an error occured, no modifications were made'),
						
			//[700-750] json errors
			700 => $this->gettext('No errors'),
			701 => $this->gettext('Maximum stack depth exceeded'),
			702 => $this->gettext('Underflow or the modes mismatch'),
			703 => $this->gettext('Unexpected control character found'),
			704 => $this->gettext('Syntax error, malformed JSON'),
			705 => $this->gettext('Malformed UTF-8 characters, possibly incorrectly encoded'),
			706 => $this->gettext('Unknown error'),
						
			);
		
		}
	
	//-------------------------------------------------------------------------------------------------------------	
	public function get($num){
		//minor check
		if(isset($this->arr[$num])){
			return $this->arr[$num];
			}
		//return generic error if none found before
		return $this->strGeneric;
		}

	//-------------------------------------------------------------------------------------------------------------	
	private function gettext($str){
		if(function_exists('gettext')){
			return gettext($str);
			}
		return $str;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function getJsonError($num){
		$rtn = 0;
		switch($num){
			case JSON_ERROR_NONE:
				$rtn = 700;
				break;
			case JSON_ERROR_DEPTH:
				$rtn = 701;
				break;
			case JSON_ERROR_STATE_MISMATCH:
				$rtn = 702;
				break;
			case JSON_ERROR_CTRL_CHAR:
				$rtn = 703;
				break;
			case JSON_ERROR_SYNTAX:
				$rtn = 704;
				break;
			case JSON_ERROR_UTF8:
				$rtn = 705;
				break;
			default:
				$rtn = 706;
				break;
			}
		return $rtn;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function getClassName(){
		return $this->className;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function getClassObject(){
		return $this;
		}

	
	}


//END