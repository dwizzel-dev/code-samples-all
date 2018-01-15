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
			100 => $this->gettext('generic errors'),
			101 => $this->gettext('pid is missing'),
			102 => $this->gettext('timestamp is missing'),
			103 => $this->gettext('section is missing'),
			104 => $this->gettext('service is missing'),
			105 => $this->gettext('service not available'),
			106 => $this->gettext('section not available'),
			107 => $this->gettext('invalid session id'),
			108 => $this->gettext('invalid call - missing parameter(s)'),
			109 => $this->gettext('sorry! your session has been lost, the application will automatically restart in 3 seconds.'),
			
			//[200-300] erreur login...
			200 => $this->gettext('usager non valide'),
			201 => $this->gettext('user account is not active'),
			202 => $this->gettext('too many login tries, user account has been deactivated'),
			203 => $this->gettext('user account has been blocked'),
			204 => $this->gettext('unknown username'),
			205 => $this->gettext('invalid credentials, please check your user name and/or password'),
			206 => $this->gettext('inactive clinic or Licence, please contact your administrator'),

			//[300-400] erreur format...
			300 => $this->gettext('email format is not valid'),
			301 => $this->gettext('new password must contain ***'),
			302 => $this->gettext('email adress already in use'),

			//[500-600] user and options class save /retreive user data #2...
			500 => $this->gettext('fail to save selected print size'),
			501 => $this->gettext('your clinic does not have any module, please contact your administrator, error code:501'),
			502 => $this->gettext('user preferences save fail'),
			503 => $this->gettext('username should be at least 8 alphanumeric characters'),
			504 => $this->gettext('Invalid UserName, Your username includes an invalid/bad character(s). Please remove it, Ex: %, +, /, \\, =, ^, `, {, |, }, ~, (, ), <, >, &'),
			505 => $this->gettext('username is already used by another user'),
			506 => $this->gettext('username is already used by another client'),
			507 => $this->gettext('you should enter at least one valid email'),
			508 => $this->gettext('the primary email should be a valid email'),
			509 => $this->gettext('the secondary email should be a valid email'),
			510 => $this->gettext('the entered password values mismatch'),
			511 => $this->gettext('the new password must be over 8 characters long'),
			512 => $this->gettext('Invalid Password, Your password includes an invalid/bad character(s). Please remove it, Ex: %, +, /, \\, =, ^, `, {, |, }, ~, (, ), ,, <, >, & and white space'),
			513 => $this->gettext('unable to save account options, please contact your administrator, error code:513'),
			514 => $this->gettext('invalid current password value'),
			515 => $this->gettext('locale language(s) not available at your licence, please contact your administrator, error code:515'),

			//[600-650] client section 
			600 => $this->gettext('user does not have access to this client'),
			601 => $this->gettext('Please enter an email address to complete client registration'),
			602 => $this->gettext('Please enter a valid email address to complete client registration'),
			603 => $this->gettext('client save fail, please contact your administrator, error code:603'),
			604 => $this->gettext('Please enter an email address to be able to update this client'),
			605 => $this->gettext('Please enter a valid email address to be able to update this client'),
			606 => $this->gettext('client update fail, please contact your administrator, error code:606'),
			607 => $this->gettext('client save fail, please enter first name'),
			608 => $this->gettext('client save fail, please enter last name'),
			609 => $this->gettext('client save fail, please enter first name'),
			610 => $this->gettext('client save fail, please enter last name'),
			611 => $this->gettext('program send fail, please contact your administrator, error code:611'),
			
			//[650-700] template section 
			650 => $this->gettext('template save fail, please enter a template name'),
			651 => $this->gettext('template save fail, template name already exist'),
			652 => $this->gettext('template save fail, please contact your administrator, error code:613'),
			653 => $this->gettext('template save fail, template name already exist'),
			654 => $this->gettext('template save fail, please contact your administrator, error code:615'),
			655 => $this->gettext('template save fail, please select a module'),
			656 => $this->gettext('you don\'t have the rights to modify this template, you can do a copy of it instead'),
			657 => $this->gettext('template save fail, please contact your administrator, error code:657'),
			658 => $this->gettext('template save fail, please contact your administrator, error code:658'),

			//[700-750] json errors
			700 => $this->gettext('No errors'),
			701 => $this->gettext('Maximum stack depth exceeded'),
			702 => $this->gettext('Underflow or the modes mismatch'),
			703 => $this->gettext('Unexpected control character found'),
			704 => $this->gettext('Syntax error, malformed JSON'),
			705 => $this->gettext('Malformed UTF-8 characters, possibly incorrectly encoded'),
			706 => $this->gettext('Unknown error'),
			
			//[750-800] program section 
			750 => $this->gettext('program save fail, please verify your entries'),
			751 => $this->gettext('program save fail, program name already exist'),
			752 => $this->gettext('program save fail, client must be selected befor you save a program'),
			753 => $this->gettext('program save fail, you are no longer have access to this client'),
			754 => $this->gettext('program save fail, please contact your administrator, error code:754'),
			755 => $this->gettext('program save fail, please verify your entries'),
			756 => $this->gettext('program save fail, program name already exist'),
			757 => $this->gettext('program save fail, client must be selected befor you save a program'),
			758 => $this->gettext('program save fail, you are no longer have access to this client'),
			759 => $this->gettext('program save fail, please contact your administrator, error code:759'),
			760 => $this->gettext('program save fail, please enter a program title'),
			761 => $this->gettext('program save fail, please enter a program title'),
			762 => $this->gettext('program save fail, unable to identify client'),
			763 => $this->gettext('program save fail, please contact your administrator, error code:763'),
			764 => $this->gettext('program save fail, please enter a program title'),
			765 => $this->gettext('program save fail, program name already exist'),
			766 => $this->gettext('program save fail, you are no longer have access to this client'),
			767 => $this->gettext('program save fail, please contact your administrator, error code:767'),
			768 => $this->gettext('program save fail, please contact your administrator, error code:768'),
			769 => $this->gettext('program save fail, please contact your administrator, error code:769'),
			770 => $this->gettext('program save fail, please contact your administrator, error code:770'),
			
			//[800-850] search section
			800 => $this->gettext('user instruction save fail, unable to retreive exercise data'),
			801 => $this->gettext('user instruction save fail, please contact your administrator, error code:801'),
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