<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	starthome page for admin

*/

header('Content-Type: text/html; charset=utf-8');

// ERROR REPORTING
error_reporting(E_ALL);

// BASE DEFINE
require_once('define.php');

// ERROR REPORTING
error_reporting(ERROR_LEVEL);

// CHECK IF SITE IS DOWN AND DEV PERMISSION 
if(SITE_IS_DOWN){
	if(isset($_SERVER["REMOTE_ADDR"])){
		if(!in_array($_SERVER["REMOTE_ADDR"],explode(",",REMOTE_ADDR_ACCEPTED))){
			Header('Location: '.PATH_OFFLINE);
			}
	}else{
		Header('Location: '.PATH_OFFLINE);
		}
	}

// BASE REQUIRED
require_once(DIR_INC.'required.php');

//CHECK LA SESSION
if(ENABLE_LOGIN){
	if(!$oReg->get('login')->isLogued()){
		$oGlob->set('router', CONTROLLER_DEFAULT_LOGIN);
		}
	}	

// ON LOAD LE CONTROLLEUR
if(!file_exists(DIR_CONTROLLER.$oGlob->get('router').'.php')){
	if(!file_exists(DIR_CONTROLLER.CONTROLLER_DEFAULT_404.'.php')){
		header('HTTP/1.0 404 Not Found');
		exit('ERROR 404');
	}else{
		$oGlob->set('router', CONTROLLER_DEFAULT_404);
		require_once(DIR_CONTROLLER.CONTROLLER_DEFAULT_404.'.php');
		}
}else{
	require_once(DIR_CONTROLLER.$oGlob->get('router').'.php');
	}

//END


