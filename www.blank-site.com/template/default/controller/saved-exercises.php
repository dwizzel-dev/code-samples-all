<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default controller for saved programs


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

//utilisons la cache
$bFileExist = false;
$strCacheFile = 'page.'.$oGlob->get('content_id').'.'.$oGlob->get('lang');
//cache
$arrControllerPageContent = $oReg->get('cache')->cacheRead($strCacheFile);

if(is_array($arrControllerPageContent)){
	$bFileExist = true;
}else{	
	//on cherche le content
	$arrControllerPageContent = $oReg->get('site')->getContent($oGlob->get('content_id'));
	}
//cache	
if(!$bFileExist){
	$oReg->get('cache')->cacheWrite($strCacheFile, $arrControllerPageContent);
	}

//on set toutes les vars de la view dans le array
$arrOutput['content']['title'] = $arrControllerPageContent['title'];
$arrOutput['content']['text'] = $arrControllerPageContent['content'];
$arrOutput['content']['class'] = $arrControllerPageContent['css_class'];
$arrOutput['content']['bgimage'] = $arrControllerPageContent['bgimage'];
//les metas
$arrOutput['meta']['title'] =  $arrControllerPageContent['meta_title'];
$arrOutput['meta']['description'] =  $arrControllerPageContent['meta_description'];
$arrOutput['meta']['keywords'] =  $arrControllerPageContent['meta_keywords'];
$arrOutput['meta']['lang'] = $arrControllerPageContent['code'];
$arrOutput['meta']['lang_prefix'] = $oGlob->get('lang_prefix');

//init widget le breadcrumb
require_once(DIR_WIDGET.'breadcrumbs/controller/breadcrumbs.php');
$oBreadcrumbs = new widgetBreadcrumbsController('top-breadcrumbs', $oReg->get('site')->getBreadcrumbFromPath($oReg->get('req')->get('path'), $oGlob));
$arrOutput['breadcrumbs'] = array();
$arrOutput['breadcrumbs']['html'] = $oBreadcrumbs->getHtml();
//clean
unset($oBreadcrumbs);

//le listing eds exercises
$arrOutput['content']['listing'] = array(
	'title' => _T('your saved exercises'),
	'message' => _T('here is a list of your previously saved exrcises, notes that they are saved in cookie.')
	);


//structure data
$arrOutput['structure'] = 'webpage';

//la view
$oGlob->set('page_view', $arrControllerPageContent['view']);

//clean
unset($arrControllerPageContent);	
	
//on set toutes les vars de la view dans le array avec les differents header, meta et du footer
require_once(DIR_MODEL.MODEL_DEFAULT_CSS);

//structure data
require_once(DIR_MODEL.MODEL_DEFAULT_STRUCTURE);

//les cripts
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





