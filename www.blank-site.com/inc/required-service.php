<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	REQUIRED FILES AND PROCEDURE BEFORE THE SITE TO CONTINUE

*/

//-----------------------------------------------------------------------------------------------
// BASE REQUIRED FUNC AND CLASSES	

//functions for this specific sites
require_once(DIR_INC.'functions.php');

//change the error handling if it is defined in the function.php or helpers.php file
if(function_exists('phpErrorHandler')){
	set_error_handler('phpErrorHandler');
	}
	
//required 
require_once(DIR_CLASS.'globals.php');
require_once(DIR_CLASS.'registry.php');
require_once(DIR_CLASS.'phperrors.php');
require_once(DIR_CLASS.'errors.php');
require_once(DIR_CLASS.'request.php');
require_once(DIR_CLASS.'response.php');
require_once(DIR_CLASS.'json.php');
require_once(DIR_CLASS.'database.php');
require_once(DIR_CLASS.'session.php');
require_once(DIR_CLASS.'log.php');
require_once(DIR_CLASS.'cache.php');
require_once(DIR_CLASS.'site.php');

//globals registed vars
$oGlob = new Globals();
$oGlob->set('lang', LANG_DEFAULT); //la langue
$oGlob->set('content_id', '0'); //le id du content de la DB pour avoir les Metas, Title, Content, etc...
$oGlob->set('links', ''); //les liens

//register new class too the registry to simplify arguments passing to other classes
$oReg = new Registry();
$oReg->set('phperr', new PhpErrors());
$oReg->set('err', new Errors());
$oReg->set('req', new Request($_GET, $_POST));
$oReg->set('resp', new Response(new Json()));
$oReg->set('log', new Log($oReg));	
$oReg->set('cache', new Cache($oReg));	
$oReg->set('db', new Database(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, $oReg));
$oReg->set('site', new Site($oReg));

//session
$oReg->set('sess', new Session($oReg));
$oReg->get('sess')->start();



//-----------------------------------------------------------------------------------------------
// CHECK LA DB CONNECTION

if(!$oReg->get('db')->getStatus()){
	exit('ERR: NO DATABASE CONNECTION');
	}

//-----------------------------------------------------------------------------------------------
// GLOBAL VARS LANG

$arrLang = explode(",",LANG_ENABLED);  
$oGlob->set('lang', LANG_DEFAULT);
if(in_array($oReg->get('req')->get('lang'), $arrLang)){ //via url
	$oGlob->set('lang', $oReg->get('req')->get('lang'));
}else if(isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $arrLang)){ //via cookie
	$oGlob->set('lang', $_COOKIE['lang']);
}else if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){ ///via browser language default
	/*
	foreach($arrLang as $k=>$v){
		if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == substr($v, 0, 2)){
			$oGlob->set('lang', $v);
			break;
			}
		}
	*/	
	}

setcookie('lang', $oGlob->get('lang'));

//prefix
$oGlob->set('lang_prefix', substr($oGlob->get('lang'), 0, 2));
unset($arrLang);
	
//-----------------------------------------------------------------------------------------------	
// ****NOTES: ORDER IS VERY IMPORTANT, EX: function.php use lang.php 


// LINKS, ROUTES
require_once(DIR_INC.'links.php');
require_once(DIR_INC.'router.php');

// GLOBAL LANG
require_once(DIR_INC.'lang.php');

// GLOBAL HELPERS
require_once(DIR_INC.'helpers.php');

// ARRAYS FOR FASTER RENDERS
require_once(DIR_INC.'hash.php');

// GLOBAL ERRORS
require_once(DIR_INC.'errors.php');

// GLOBAL FUNCTIONS
require_once(DIR_INC.'functions-site.php');










//END





