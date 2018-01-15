<?php
/**
@auth: Dwizzel
@date: 22-04-2016
@info: fichier de base pour un autologin via un url en POST ou GET
@version: V.1.0 B.001

*/
//-------------------------------------------------------------------------------------------------------------

// ERROR REPORTING
error_reporting(0);

// BASE DEFINE
require_once('define.php');

// ERROR REPORTING
if(defined('ERROR_REPORT_LEVEL')){
	error_reporting(ERROR_REPORT_LEVEL);
	}

// CHECK ACCESS
if(SITE_IS_DOWN){
	if(isset($_SERVER['REMOTE_ADDR'])){
		if(!in_array($_SERVER['REMOTE_ADDR'],explode(',',REMOTE_ADDR_ACCEPTED))){
			exit('SORRY! OFFLINE FOR MAINTENANCE');
			}
	}else{
		exit('SORRY! OFFLINE FOR MAINTENANCE');
		}
	}	

// BASE REQUIRED
require_once(DIR_INC.'required.php');

// IP OF THE CALL
if(isset($_SERVER['REMOTE_ADDR'])){
	$oReg->get('req')->set('ip', $_SERVER['REMOTE_ADDR']);
}else{
	$oReg->get('req')->set('ip', '0.0.0.0');	
	}

//LOG REQUEST
if(ENABLE_LOG){
	$oReg->get('log')->log(
		'autologin', 
		$oReg->get('req')->showRequestAllText()
		);
	}

//REGISTER A JSON FOR DATA
$oReg->set('json', new Json());

//ON VA SETTER DES VARS DU REQUEST QUI NE SONT PAS ENCORE LA
//le pid
$oReg->get('req')->set('pid', 1);
//le timestamp
$oReg->get('req')->set('time', time());
//la section	
$oReg->get('req')->set('section', 'user');
//le service
$oReg->get('req')->set('service', 'auto-login');
//le data de base du login soit le user et psw
$oReg->get('req')->set('data', $oReg->get('json')->encode(array(
	'username' => $oReg->get('req')->get('username'),
	'password' => $oReg->get('req')->get('password')
	)));

// BASE CLASSES
require_once(DIR_CLASS.'service.php');

//INSTANCE OF SERVICE
$oService = new Service($oReg);

//MINOR CHECK ON THE ARGS WE NEED TO DO THE CALL
if($oService->check()){
	//session and args are valid	
	$rtn = $oService->process();
	if(!isTrue($rtn)){
		//kick out au login
		$oReg->get('resp')->redirect(PATH_WEB);
		}
}else{
	//kick out au login
	$oReg->get('resp')->redirect(PATH_WEB);
	}


//END