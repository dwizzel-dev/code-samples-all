<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default controller for exercises keywords


*/
//-------------------------------------------------------------------------------
//ca nous prend un id de categorie sinon on affiche le listing des categories
$iKwId = 0;
//on check si c'est une cat-xx/filter-xx/
if(preg_match('/[a-z0-9\-]{0,}'.preg_quote(DEFAULT_ID_SEPARATOR).'([0-9]+)$/', $oGlob->get('args_page'), $arrPregRes)){
	//on garde le id
	$iKwId = intVal($arrPregRes[1]);
	}

//si pas de id alors on retourne au search
if(!$iKwId){
	//on redirect vers la recherche
	$strRedirect = $oGlob->getArray('links-by-key', 'exercises-search-'.$oGlob->get('lang_prefix'));
	Header('HTTP/1.1 301 Moved Permanently');
	Header('Location: '.$strRedirect);
	exit();	
	}

//-------------------------------------------------------------------------------
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

//-------------------------------------------------------------------------------
//on cherche le content
$arrControllerPageContent = $oReg->get('site')->getContent($oGlob->get('content_id'));
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


//-------------------------------------------------------------------------------

//on a un kw id alors on ca chercher les exercices specifique a celui ci
//init the object	
require_once(DIR_MODEL.'keywords-exercises.php');
$oKw = new KeywordsExercises(
	$oReg, 
	$oGlob
	);
//do the search	
$arrKeywords = $oKw->getExercises($iKwId);
//clean
unset($oKw);
//init widget listing des categories
require_once(DIR_WIDGET.'exercise-categories/controller/exercise-categories.php');
$oExKw = new widgetExerciseCategoriesController(
	$oReg, 
	$oGlob,
	$arrKeywords, 
	'exercise-exercises'
	);		
//check si il existe sinon on passe
if($oExKw->getWidget()){
	$arrOutput['content']['listing'] = $oExKw->getHtml(
		'keywords',
		'exercise-exercises-result',
		$arrKeywords['html'],
		''
		);	
	}
//clean
unset($oExKw);
//on change le titre de la page
$arrOutput['meta']['title'] = ucfirst($arrKeywords['html']).' - '.ucfirst($arrOutput['meta']['title']);	
$arrOutput['meta']['description'] = _T('exercises containing the tag:').' '.$arrKeywords['html'];	
$arrOutput['meta']['image'] = PATH_IMAGE_DEFAULT;	
//canonique
$arrOutput['meta']['canonical'] = $oGlob->getArray('links-by-key', 'exercises-kw-'.$oGlob->get('lang_prefix')).$arrKeywords['href'].DEFAULT_ID_SEPARATOR.$arrKeywords['id'].'/';
//init widget le breadcrumb
require_once(DIR_WIDGET.'breadcrumbs/controller/breadcrumbs.php');
//le path du bread crumb	
$arrBreadcrumb = $oReg->get('site')->getBreadcrumbFromPath(
	$oGlob->get('page').'/'.$oGlob->get('sub_page').'/',
	$oGlob
	);
//on rajoute au array le path du keywords
array_push($arrBreadcrumb, array(
	'text' => $arrKeywords['html'],
	'link' => $oGlob->getArray('links-by-key', 'exercises-kw-'.$oGlob->get('lang_prefix')).$arrKeywords['href'].DEFAULT_ID_SEPARATOR.$arrKeywords['id'].'/'
	));
$oBreadcrumbs = new widgetBreadcrumbsController(
	'top-breadcrumbs', 
	$arrBreadcrumb
	);
$arrOutput['breadcrumbs'] = array(
	'html' => $oBreadcrumbs->getHtml()
	);
//clean
unset($oBreadcrumbs, $arrBreadcrumb, $arrKeywords);
	
	




//-------------------------------------------------------------------------------

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





