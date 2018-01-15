<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	home controller, le home/accueil ne contient pas de content dans la DB, 
		car c'est une page particuliere, le texte sera parmi les textes pregeneres de la DB,
		dans /inc/lang/xx_XX


*/

//--------------------------------------------------------

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
	
//--------------------------------------------------------	

//utilisons la cache
$bFileExist = false;
$strCacheFile = 'home.'.$oGlob->get('lang');
//cache
$arrControllerHomeContent = $oReg->get('cache')->cacheRead($strCacheFile);

if(is_array($arrControllerHomeContent)){
	$bFileExist = true;
}else{	
	//on cherche le content
	$arrControllerHomeContent = $oReg->get('site')->getContent($oGlob->get('content_id'));
	}
//cache	
if(!$bFileExist){
	$oReg->get('cache')->cacheWrite($strCacheFile, $arrControllerHomeContent);
	}

//on set toutes les vars de la view dans le array 
$arrOutput['content']['title'] = $arrControllerHomeContent['title'];
$arrOutput['content']['text'] = $arrControllerHomeContent['content'];
$arrOutput['content']['class'] = $arrControllerHomeContent['css_class'];

//les metas
$arrOutput['meta']['title'] =  $arrControllerHomeContent['meta_title'];
$arrOutput['meta']['description'] =  $arrControllerHomeContent['meta_description'];
$arrOutput['meta']['keywords'] =  $arrControllerHomeContent['meta_keywords'];
$arrOutput['meta']['lang'] = $arrControllerHomeContent['code'];
$arrOutput['meta']['lang_prefix'] = $oGlob->get('lang_prefix');

//init widget le module
require_once(DIR_WIDGET.'module/controller/module.php');
$oModuleHome = new widgetModuleController($oReg, $oGlob, 'module-home');
if($oModuleHome->getWidget()){
	$arrOutput['module-home'] = array(
		'html' => $oModuleHome->getHtml(),
		);
	}
//clean
unset($oModuleHome);

//init widget le module
require_once(DIR_WIDGET.'section/controller/section.php');
$oSectionHome = new widgetSectionController($oReg, $oGlob, 'section-home');
if($oSectionHome->getWidget()){
	$arrOutput['section-home'] = array(
		'html' => $oSectionHome->getHtml(),
		);
	}
//clean
unset($oSectionHome);

//structure data
$arrOutput['structure'] = 'webpage';

//la view
$oGlob->set('page_view', VIEW_HOME);

//clean
unset($arrControllerHomeContent);

//on ckeck si il y a des erreus
if($oReg->get('req')->get('err')){
	$arrOutput['content']['error'] = $gErrors[intVal($oReg->get('req')->get('err'))];
	}
//on ckeck si il y a des confirm
if($oReg->get('req')->get('cfrm')){
	$arrOutput['content']['confirm'] = $gErrors[intVal($oReg->get('req')->get('cfrm'))];
	}

//--------------------------------------------------------

//on set toutes les vars de la view dans le array avec les differents header, meta et du footer
require_once(DIR_MODEL.MODEL_DEFAULT_CSS);

//structure data
require_once(DIR_MODEL.MODEL_DEFAULT_STRUCTURE);

//les script
require_once(DIR_MODEL.MODEL_DEFAULT_SCRIPT);

//ce qi vient avant tout
require_once(DIR_MODEL.MODEL_DEFAULT_PREPEND);

//les header
require_once(DIR_MODEL.MODEL_DEFAULT_HEADER);

//le top frontpage
require_once(DIR_MODEL.MODEL_DEFAULT_TOP_FRONTPAGE);

//le footer
require_once(DIR_MODEL.MODEL_DEFAULT_FOOTER);

//ce qi vient apres tout
require_once(DIR_MODEL.MODEL_DEFAULT_APPEND);

//on load la view
require_once(DIR_VIEWS.$oGlob->get('page_view').'.php');



//END






