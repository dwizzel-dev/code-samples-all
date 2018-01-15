<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default controller for login


*/

//on set la variable de base de output utilise par la view
$arrOutput = array(
	'css' => array(),
	'script' => array(),
	'prepend' => array(),
	'header' => array(),
	'meta' => array(),
	'content' => array(),
	'footer' => array(),
	'append' => array(),
	);

//on set toutes les vars de la view dans le array
$arrOutput['content']['title'] = _T('login');
$arrOutput['content']['text'] = '<p>'._T('please enter your information below to login').'</p>';
$arrOutput['content']['class'] = '';

//les metas
$arrOutput['meta']['title'] = $arrOutput['content']['title'];
$arrOutput['meta']['description'] = META_DESCRIPTION;
$arrOutput['meta']['keywords'] =  META_KEYWORDS;
$arrOutput['meta']['lang'] = $oGlob->get('lang');	

//la view
$oGlob->set('page_view', 'login');

//les erreurs
if($oReg->get('req')->get('err')){
	if(isset($gErrors[$oReg->get('req')->get('err')])){
		$arrOutput['content']['error'] = $gErrors[$oReg->get('req')->get('err')];
	}else{
		$arrOutput['content']['error'] = $gErrors['100'];
		}
}else{
	$arrOutput['content']['error'] = '';	
	}

//le captcha
$strTime = time().'';
$arrOutput['content']['captcha'] = substr($strTime, strlen($strTime)-intVal(CAPTCHA_MULTIPLIER), strlen($strTime)); 
$arrOutput['content']['captcha'] = intVal($arrOutput['content']['captcha'] * CAPTCHA_MULTIPLIER);
$arrOutput['content']['captcha'] = str_shuffle($arrOutput['content']['captcha']);
//$arrOutput['content']['captcha-image'] = $oReg->get('login')->createCaptcha($arrOutput['content']['captcha']);
//set le captcha dans la session pour revalidation
$oReg->get('sess')->put('login', array(
	'captcha' => $arrOutput['content']['captcha'],
	'user_name' => '',
	'user_id' => '',
	'user_group' => '',
	'user_email' => '',
	'lastdate' => '',
	'lastpath' => $_SERVER['REQUEST_URI'],
	));
unset($strTime);	
	
	
//on set toutes les vars de la view dans le array avec les differents header, meta et du footer
require_once(DIR_MODEL.MODEL_DEFAULT_CSS);

//les cripts
require_once(DIR_MODEL.MODEL_DEFAULT_SCRIPT);

//ce qi vient avant tout
require_once(DIR_MODEL.MODEL_DEFAULT_PREPEND);

//les header
require_once(DIR_MODEL.MODEL_DEFAULT_HEADER);

//le footer
require_once(DIR_MODEL.MODEL_DEFAULT_FOOTER);

//ce qi vient apres tout
require_once(DIR_MODEL.MODEL_DEFAULT_APPEND);	

//on load la view
require_once(DIR_VIEWS.$oGlob->get('page_view').'.php');






//END





