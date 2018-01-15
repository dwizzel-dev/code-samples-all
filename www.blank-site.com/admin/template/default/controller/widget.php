<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default controller for widgets editing


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

//les metas
$arrOutput['meta']['title'] = META_TITLE;
$arrOutput['meta']['description'] = META_DESCRIPTION;
$arrOutput['meta']['keywords'] =  META_KEYWORDS;
$arrOutput['meta']['lang'] = $oGlob->get('lang');	

//le la body class si besoin
$arrOutput['content']['class'] = '';
$arrOutput['content']['text'] = '';
$arrOutput['content']['title'] = '';

//on check les args for section 'category', 'listing', 'items'
$section = 'listing'; //default if none
if($oGlob->get('args_page').'' != ''){
	$arrArgs = explode('/', $oGlob->get('args_page'));
	if(isset($arrArgs[0])){
		$section = $arrArgs[0];
		}
	}

if($section == 'listing'){
	//title
	$arrOutput['content']['title'] = _T('widgets listing');
	//la view
	$oGlob->set('page_view', 'widget');
	//on va chercher les widget
	require_once(DIR_CLASS.'widget.php');
	$oWidget = new Widget($oReg);
	$arrOutput['content']['widget'] = array();
	$arrOutput['content']['widget']['columns'] = array('',_T('name'),_T('category'),_T('lang'),_T('#'),_T('[nbsp]'));
	$arrOutput['content']['widget']['rows'] = $oWidget->getWidgetListing();
	//
	//$arrOutput['content']['language-dropdown'] = $oGlob->get('languages_for_dropbox');
	//cleaan
	unset($oWidget);
		
		
}else if($section == 'items'){		
	//get du ID
	$arrOutput['content']['item-id'] = 0;
	if(preg_match('/item-([0-9]+)\//',$oGlob->get('args_page'),$arrPregRes)){
		$arrOutput['content']['item-id'] = intVal($arrPregRes[1]);
		}
	//check si valide
	if(!$arrOutput['content']['item-id']){
		Header('Location: '.$oGlob->getArray('links', '404'));
		exit();
		}
	//title
	$arrOutput['content']['title'] = _T('widget details');
	$arrOutput['meta']['title'] = $arrOutput['content']['title'];
	//le data
	require_once(DIR_CLASS.'widget.php');
	$oWidget = new Widget($oReg);
	//dropdown
	$arrOutput['content']['category-dropdown'] = $oWidget->getWidgetCategoryDropBox();
	$arrOutput['content']['language-dropdown'] = $oGlob->get('languages_for_dropbox');
	//data	
	$arrOutput['content']['item'] = array();
	$arrOutput['content']['item']['details'] = $oWidget->getWidgetInfos($arrOutput['content']['item-id']);
	//pour les checkbox de ou on veut que ca affiche le top-frontpage
	$arrOutput['content']['links-for-checkbox'] = $oWidget->getLinksForCheckboxByLang($arrOutput['content']['item']['details']['language_id']);	
	//on unserialize l'object ramenr par la DB
	$arrOutput['content']['item']['details']['data-unserialize'] = unserializeFromDbData($arrOutput['content']['item']['details']['data']);
	array_walk_recursive($arrOutput['content']['item']['details']['data-unserialize'],'formatSerializeRev');	
	//les liste d'icone
	$arrOutput['content']['item']['icons-list'] = $oWidget->getIconList(DIR_SOCIALMEDIA_ICONS);
	//la liste de controller selon 
	$arrOutput['content']['item']['icons-list'] = $oWidget->getIconList(DIR_SOCIALMEDIA_ICONS);	
	//la iew selon le wodget category
	$oGlob->set('page_view', 'widget-item-'.$arrOutput['content']['item']['details']['category']);
	//le name du widget
	$arrOutput['content']['item']['h-title'] = $arrOutput['content']['item']['details']['name'];
	//clean
	unset($oWidget);
	
	

}else{
	Header('Location: '.$oGlob->getArray('links', '404'));
	exit();
	}

unset($section);



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





