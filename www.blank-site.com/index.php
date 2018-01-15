<?php
/**
@auth:	Monsieur Dwizzel
@date:	00-00-0000
@info:	starthome page for all treatment

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
	//on set les stats de page, pour les news ca se fait dans les controller/news.php puisque le content_id = 0 par defaut avec inc/router.php
	if($oGlob->get('content_id') != 0 && CONTROLLER_DEFAULT_404 != $oGlob->get('router')){
		$oReg->get('site')->setContentHits($oGlob->get('content_id'));	
		}
	require_once(DIR_CONTROLLER.$oGlob->get('router').'.php');
	}





//END