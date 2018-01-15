<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	functions used everywhere by one site

*/

//------------------------------------------------------------------------------------------	
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

















	
	
	
//END