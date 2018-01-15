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
require_once(DIR_CLASS.'request.php');
require_once(DIR_CLASS.'database.php');
require_once(DIR_CLASS.'session.php');
require_once(DIR_CLASS.'log.php');
require_once(DIR_CLASS.'cache.php');
require_once(DIR_CLASS.'site.php');

//globals registed vars
$oGlob = new Globals();
$oGlob->set('lang', LANG_DEFAULT); //la langue
$oGlob->set('lang_prefix', ''); //la langue fr/en/es
$oGlob->set('content_id', '0'); //le id du content de la DB pour avoir les Metas, Title, Content, etc...
$oGlob->set('router', ''); //router vers le controleur
$oGlob->set('links', ''); //les liens
$oGlob->set('path', ''); //le path de URL
$oGlob->set('page', ''); //le controller
$oGlob->set('sub_page', ''); //le sub controller
$oGlob->set('args_page', ''); //tout le path apres le sub controller
$oGlob->set('page_view', ''); //la vue pour la page
$oGlob->set('current_url', ''); //le url de la page 

//register new class too the registry to simplify arguments passing to other classes
$oReg = new Registry();
$oReg->set('phperr', new PhpErrors());
$oReg->set('req', new Request($_GET, $_POST));
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

if($oReg->get('req')->get('path')){
	$oGlob->set('path', $oReg->get('req')->get('path'));
	//on cherche le controller
	$arrPath = explode('/', $oReg->get('req')->get('path'));
	if(is_array($arrPath)){
		$strPath = '';
		for($i=0;$i<count($arrPath);$i++){
			if($arrPath[$i] != ''){
				if($i == 0){
					$oGlob->set('page', $arrPath[$i]);
				}else if($i == 1){
					$oGlob->set('sub_page', $arrPath[$i]);
				}else{
					$strPath .= $arrPath[$i].'/';
					}
				}
			}
		if($strPath.'' != ''){
			$strPath = substr($strPath, 0, (strlen($strPath) - 1));
			}
		$oGlob->set('args_page', $strPath);	
		unset($strPath);
		}
	unset($arrPath);	
	}
	
//le current path url pour la comparaison des menu et leur /parent/contenu/etc	
if(SIMPLIFIED_URL){
	$oGlob->set('current_url', PATH_WEB.$oGlob->get('lang_prefix').'/'.$oGlob->get('path').'/');	
}else{
	$oGlob->set('current_url', PATH_HOME.'?&lang='.$oGlob->get('lang').'&path='.$oGlob->get('path').'/');		
	}
	
	
	
//-----------------------------------------------------------------------------------------------	
// ****NOTES: ORDER IS VERY IMPORTANT, EX: function.php use lang.php 


// LINKS 
require_once(DIR_INC.'links.php');

// ROUTES
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





