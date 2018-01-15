<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	function used by this site

*/


//---------------------------------------------------------------------------------------------------------------

	
function phpErrorHandler($errno, $errstr, $errfile, $errline){
	if(!(error_reporting() && $errno)){
		return;
		}
	//text error container
	$str = '';
	//		
	switch($errno){
		case E_USER_ERROR:
			echo 'E_USER_ERROR['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']'.EOL;
			exit(1);
			break;

		case E_USER_WARNING:
			$str = 'E_USER_WARNING['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']';
			break;

		case E_USER_NOTICE:
			$str = 'E_USER_NOTICE['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']';
			break;

		case E_ERROR:
			echo 'E_ERROR['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']'.EOL;
			exit(1);
			break;

		case E_WARNING:
			$str = 'E_WARNING['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']';
			break;

		case E_NOTICE:
			$str = 'E_NOTICE['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']';
			break;

		case E_CORE_ERROR:
			echo 'E_CORE_ERROR['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']'.EOL;
			exit(1);
			break;	

		case E_PARSE:
			$str = 'E_PARSE['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']';
			break;

		case E_COMPILE_ERROR:
			echo 'E_COMPILE_ERROR['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']'.EOL;
			exit(1);
			break;

		default:
			$str = 'UNDEFINED_ERROR['.EOL.TAB.'errstr: '.$errstr.EOL.TAB.'errno: '.$errno.EOL.TAB.'line: '.$errline.EOL.TAB.'file: '.$errfile.EOL.TAB.']';
			break;
		}

	//check si on a un phperrors object dans le $oReg qui est global 
	//et acces a tout ce qui est enregistre dedans
	global $oReg;
	//minor check
	if(isset($oReg)){
		if(method_exists($oReg->get('phperr'), 'add')){	
			$oReg->get('phperr')->add($str);
			//skip php internal errors
			return true;
			}
		}
	
	//si on est pas sorti avant alors on sort maintenant
	//show error	
	echo $str.EOL.EOL;
	//skip php internal errors
	return true;
	}	
	
	
//END