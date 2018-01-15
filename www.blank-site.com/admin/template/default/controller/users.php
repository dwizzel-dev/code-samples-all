<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default controller for users editing


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

//on check les args for section 'listing', 'items'
$section = 'listing'; //default if none
if($oGlob->get('args_page').'' != ''){
	$arrArgs = explode('/', $oGlob->get('args_page'));
	if(isset($arrArgs[0])){
		$section = $arrArgs[0];
		}
	}

if($section == 'listing'){
	//basics 
	$arrOutput['page'] = array(
		'start' => 0,
		'limit' => LIMIT_PER_PAGE,
		);
	$arrOutput['filter'] = array(
		'display-id' => 2,
		'sortby-id' => 'date_modified',
		'searchitems-id' => '',
		);
	//les ssion content values pour interface select
	$arrSessContent = $oReg->get('sess')->get('users-listing');
	//
	if(preg_match('/page-([0-9]+)\//',$oGlob->get('args_page'),$arrPregRes)){
		$arrOutput['page']['start'] = intVal($arrPregRes[1]);
		$arrSessContent['last-page-number'] = $arrOutput['page']['start'];
		}
	//
	if(preg_match('/display-([0-9]+)\//',$oGlob->get('args_page'),$arrPregRes)){
		$arrOutput['filter']['display-id'] = intVal($arrPregRes[1]);
		$arrSessContent['filter-display-id'] = $arrOutput['filter']['display-id'];
	}else if($arrSessContent['filter-display-id'] || $arrSessContent['filter-display-id'] == 0){
		$arrOutput['filter']['display-id'] = $arrSessContent['filter-display-id'];
		}
	//
	if(preg_match('/sortby-([a-z_]+)\//',$oGlob->get('args_page'),$arrPregRes)){
		$arrOutput['filter']['sortby-id'] = $arrPregRes[1];
		$arrSessContent['filter-sortby-id'] = $arrOutput['filter']['sortby-id'];
	}else if($arrSessContent['filter-sortby-id']){
		$arrOutput['filter']['sortby-id'] = $arrSessContent['filter-sortby-id'];
		}	
	//
	if(preg_match('/searchitems-0\//',$oGlob->get('args_page'),$arrPregRes)){
		$arrOutput['filter']['searchitems-id'] = '';
		$arrSessContent['filter-searchitems-id'] = '';
	}else if(preg_match('/searchitems-(.+)\//',$oGlob->get('args_page'),$arrPregRes)){
		$arrOutput['filter']['searchitems-id'] = $arrPregRes[1];
		$arrSessContent['filter-searchitems-id'] = $arrOutput['filter']['searchitems-id'];	
	}else{
		$arrOutput['filter']['searchitems-id'] = '';
		}
	//on remet ca dans la session
	$oReg->get('sess')->put('users-listing', $arrSessContent);
	unset($arrSessContent);

	//title
	$arrOutput['content']['title'] = _T('users listing');
	//la view
	$oGlob->set('page_view', 'users-listing');
	//dropdown
	$arrOutput['content']['display-dropdown'] = $oGlob->get('display_filter_for_dropbox');
	$arrOutput['content']['display-dropdown-selected'] = $arrOutput['filter']['display-id'];
	$arrOutput['content']['sortby-dropdown'] = array(
		array('id'=>'username','text'=>_T('sort by').' '._T('username')),
		array('id'=>'name','text'=>_T('sort by').' '._T('name')),
		array('id'=>'id','text'=>_T('sort by').' '._T('id')),
		array('id'=>'date_added','text'=>_T('sort by').' '._T('date added')),
		);
	$arrOutput['content']['sortby-dropdown-selected'] = $arrOutput['filter']['sortby-id'];	
	//on va chercher les stores
	require_once(DIR_CLASS.'users.php');
	$oUsers = new Users($oReg);
	$arrOutput['content']['users'] = array();
	$arrOutput['content']['users']['columns'] = array('',_T('username'),_T('name'),_T('date'),_T('#'),_T('[nbsp]'));
	$arrOutput['content']['users']['rows'] = $oUsers->getUsersListing(($arrOutput['page']['start'] * $arrOutput['page']['limit']), $arrOutput['page']['limit'], $arrOutput['filter']);
	$arrOutput['page']['total'] = $oUsers->getUsersListingCount($arrOutput['filter']);
	//cleaan
	unset($oUsers);
	//widget pagination
	require_once(DIR_WIDGET.'pagination/controller/pagination.php');
	$oPagination = new widgetPaginationController('items-pagination', $arrOutput['page']['total'], $arrOutput['page']['start'], $arrOutput['page']['limit'], 'users-listing');
	//check si il existe sinon on passe
	if($oPagination->getWidget()){
		$arrOutput['pagination'] = array();
		$arrOutput['pagination']['html'] = $oPagination->getHtml($oGlob->get('links'));
		}	
		
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
	$arrOutput['content']['title'] = _T('users details');
	$arrOutput['meta']['title'] = $arrOutput['content']['title'];
	//la iew selon le wodget category
	$oGlob->set('page_view', 'users-item');
	//le data
	require_once(DIR_CLASS.'users.php');
	$oUsers = new Users($oReg);
	//data	
	$arrOutput['content']['item'] = array();
	$arrOutput['content']['item']['details'] = $oUsers->getUsersInfos($arrOutput['content']['item-id']);
	//le name du stores
	$arrOutput['content']['item']['h-title'] = mb_strtolower($arrOutput['content']['item']['details']['name'], 'UTF-8');
	//clean
	unset($oUsers);
	
	

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





