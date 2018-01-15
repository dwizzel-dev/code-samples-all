<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	file to process all sent form or action on posted data

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

//--------------------------------------------------------------------------------------			
// DEBUGGER
//--------------------------------------------------------------------------------------
//on set la variable de base de output utilise par la view
$arrOutput = array(
	'css' => array(),
	'script' => array(),
	'append' => array(),
	);

//on set toutes les vars de la view dans le array avec les differents header, meta et du footer
require_once(DIR_MODEL.MODEL_DEFAULT_CSS);
require_once(DIR_MODEL.MODEL_DEFAULT_SCRIPT);
require_once(DIR_MODEL.MODEL_DEFAULT_APPEND);

//debug views
/*
require_once(DIR_VIEWS.'css.php');
require_once(DIR_VIEWS.'script.php');
require_once(DIR_VIEWS.'append.php');
*/

//FORM RETURN
$sendType = $oReg->get('req')->get('type');

//IF NO ACTIONS	
if($sendType == '' || $sendType === false){
	Header('Location: '.$oGlob->getArray('links-by-key', 'home-'.$oGlob->get('lang_prefix')));
	exit();
	}

//--------------------------------------------------------------------------------------			
// PROCESSING
//--------------------------------------------------------------------------------------			
switch($sendType){
	
	//------------------------------------------
	CASE 'logout':
		//on clair le reste
		$oReg->get('sess')->clear();
		$oReg->get('sess')->close();
		//redirect
		Header('Location: '.$oGlob->getArray('links-by-key', 'home-'.$oGlob->get('lang_prefix')));
		exit();
		break;

	//------------------------------------------
	
	// IMPOSTANT se fait maintenant directement avec "http://www.blank-site.com/?q=kw-serach-word"
	/*
	CASE 'search-exercises':
		//on va rediriger vers la bonne page dana la bonne langue
		$strRequest = $oGlob->getArray('links-by-key', 'exercises-result-'.$oGlob->get('lang_prefix'));
		$strRequest .= urlencode(trimKeyword(urldecode($oReg->get('req')->get('keyword')))).'/';	
		Header('Location: '.$strRequest);
		exit();
		break;
	*/
	//------------------------------------------
	DEFAULT:
		Header('Location: '.$oGlob->getArray('links-by-key', 'home-'.$oGlob->get('lang_prefix')));
		exit();
		break;		

	}












//END




	
