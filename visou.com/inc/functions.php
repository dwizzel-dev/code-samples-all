<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	functions used everywhere by one site

*/

//------------------------------------------------------------------------------------------	
//handling
function phpErrorHandler($errno, $errstr, $errfile, $errline){
	if(!(error_reporting() && $errno)){
		return;
		}
		
	switch($errno){
		case E_USER_ERROR:
			echo 'E_USER_ERROR['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']'.EOL.EOL;
			exit(1);
			break;

		case E_USER_WARNING:
			echo 'E_USER_WARNING['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']'.EOL.EOL;
			break;

		case E_USER_NOTICE:
			echo 'E_USER_NOTICE['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']'.EOL.EOL;
			break;

		case E_ERROR:
			echo 'E_ERROR['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']'.EOL.EOL;
			exit(1);
			break;

		case E_WARNING:
			echo 'E_WARNING['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']'.EOL.EOL;
			break;

		case E_NOTICE:
			echo 'E_NOTICE['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']'.EOL.EOL;
			break;

		case E_CORE_ERROR:
			echo 'E_CORE_ERROR['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']'.EOL.EOL;
			exit(1);
			break;	

		case E_PARSE:
			echo 'E_PARSE['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']'.EOL.EOL;
			break;

		case E_COMPILE_ERROR:
			echo 'E_COMPILE_ERROR['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']'.EOL.EOL;
			exit(1);
			break;

		default:
			echo 'UNDEFINED_ERROR['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']'.EOL.EOL;
			break;
		}
	//skip php internal errors
	return true;	
	}


//------------------------------------------------------------------------------------------
//.PO file to php array
function poFileToPhpArray($fileName){
	if(file_exists($fileName)){
		$arrTranslations = array();
		$po = file($fileName);
		$current = null;
		foreach($po as $line){
			if(substr($line,0,5) == 'msgid'){
				$current = trim(substr(trim(substr($line,5)),1,-1));
				}
			if(substr($line,0,6) == 'msgstr'){
				$arrTranslations[$current] = trim(substr(trim(substr($line,6)),1,-1));
				}
			}
		return $arrTranslations;
		}
	return false;
	}


//------------------------------------------------------------------------------------------
//get la country selon la langue
function getCountryByLang($strLang){
	//
	$strCountry = '';	
	//	
	switch($strLang){
		case 'fr': 
			$strCountry = 'CA';
			break;

		default: 
			$strCountry = 'US';
			break;
		}
	return $strCountry;
	}

	
	
	
//END