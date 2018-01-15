<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default controller for menu editing


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
$arrOutput['content']['title'] = _T('menu listing');
$arrOutput['content']['text'] = '';
$arrOutput['content']['class'] = '';

//les metas
$arrOutput['meta']['title'] = $arrOutput['content']['title'];
$arrOutput['meta']['description'] = META_DESCRIPTION;
$arrOutput['meta']['keywords'] =  META_KEYWORDS;
$arrOutput['meta']['lang'] = $oGlob->get('lang');	

//les filtres
$arrOutput['filter'] = array(
	'display-id' => 2,
	'language-id' => 0,
	'menu-group-id' => 0,	
	);

//les ssion menu values pour interface select
$arrSessMenu = $oReg->get('sess')->get('menu-listing');
//	
if(preg_match('/language-([0-9]+)\//',$oGlob->get('args_page'),$arrPregRes)){
	$arrOutput['filter']['language-id'] = intVal($arrPregRes[1]);
	$arrSessMenu['filter-language-id'] = $arrOutput['filter']['language-id'];		
}else if($arrSessMenu['filter-language-id']){
	$arrOutput['filter']['language-id'] = $arrSessMenu['filter-language-id'];
	}	
if(preg_match('/display-([0-9]+)\//',$oGlob->get('args_page'),$arrPregRes)){
	$arrOutput['filter']['display-id'] = intVal($arrPregRes[1]);
	$arrSessMenu['filter-display-id'] = $arrOutput['filter']['display-id'];			
}else if($arrSessMenu['filter-display-id'] || $arrSessMenu['filter-display-id'] === 0){
	$arrOutput['filter']['display-id'] = $arrSessMenu['filter-display-id'];
	}
if(preg_match('/menu-group-([0-9]+)\//',$oGlob->get('args_page'),$arrPregRes)){
	$arrOutput['filter']['menu-group-id'] = intVal($arrPregRes[1]);
	$arrSessMenu['filter-menu-group-id'] = $arrOutput['filter']['menu-group-id'];			
}else if($arrSessMenu['filter-menu-group-id']){
	$arrOutput['filter']['menu-group-id'] = $arrSessMenu['filter-menu-group-id'];
	}
//on remet ca dans la session
$oReg->get('sess')->put('menu-listing', $arrSessMenu);
unset($arrSessMenu);


//
$arrOutput['content']['display-dropdown-selected'] = $arrOutput['filter']['display-id'];
$arrOutput['content']['language-dropdown-selected'] = $arrOutput['filter']['language-id'];
$arrOutput['content']['menu-group-dropdown-selected'] = $arrOutput['filter']['menu-group-id'];

//la view
$oGlob->set('page_view', 'menu');

//on va chercher les links
require_once(DIR_CLASS.'menu-client.php');
$oMenuClient = new MenuClient($oReg);

$arrOutput['content']['menu'] = array();
$arrOutput['content']['menu']['cmpt-row'] = -1;
$arrOutput['content']['menu']['columns'] = array('',_T('name'),_T('path'),_T('group'),_T('order'),_T('#'),_T('[nbsp]'));
$arrOutput['content']['menu']['rows'] = $oMenuClient->getMenuClientArrayForAdmin($arrOutput['filter']['language-id'], $arrOutput['filter']['display-id'], $arrOutput['filter']['menu-group-id']);
//dropdown
$arrOutput['content']['language-dropdown'] = $oGlob->get('languages_for_dropbox');
$arrOutput['content']['display-dropdown'] = $oGlob->get('display_filter_for_dropbox');
$arrOutput['content']['menu-type-dropdown'] = array(
	array('id'=>'','text'=>'--'),
	array('id'=>'0','text'=>'link'),
	array('id'=>'1','text'=>'title'),
	);	
$arrOutput['content']['menu-group-dropdown'] = 	$oMenuClient->getMenuGroupArray();
array_unshift($arrOutput['content']['menu-group-dropdown'], array('id' => '0', 'name' => '--', 'language' => ''));


//$arrOutput['content']['menu-dropdown'] = $oMenuClient->getMenuClientListing();
$arrOutput['content']['menu-dropdown'] = $oMenuClient->getMenuClientTreeListing($arrOutput['content']['menu']['rows']);//filter auto le array selon menu-group
//dropdoewn
require_once(DIR_CLASS.'links.php');
$oLinks = new Links($oReg);
$arrOutput['content']['link-dropdown'] = $oLinks->getLinksForDropDown();


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





