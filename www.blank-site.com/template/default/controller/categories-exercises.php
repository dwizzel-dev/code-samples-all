<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default controller for exercises categories


*/
//-------------------------------------------------------------------------------
//ca nous prend un id de categorie sinon on affiche le listing des categories
$iCatId = 0;
$iFilterId = 0;
//on check si c'est une cat-xx/filter-xx/
if(preg_match('/[a-z0-9\-]{0,}'.preg_quote(DEFAULT_ID_SEPARATOR).'([0-9]+)\/[a-z0-9\-]{0,}'.preg_quote(DEFAULT_ID_SEPARATOR).'([0-9]+)$/', $oGlob->get('args_page'), $arrPregRes)){
	//on garde les ids
	$iCatId = intVal($arrPregRes[1]);
	$iFilterId = intVal($arrPregRes[2]);
}else if(preg_match('/[a-z0-9\-]{0,}'.preg_quote(DEFAULT_ID_SEPARATOR).'([0-9]+)$/', $oGlob->get('args_page'), $arrPregRes)){
	//on garde le id
	$iCatId = intVal($arrPregRes[1]);
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
if($iCatId !== 0 && $iFilterId !== 0){
	//on a un cat id et un filter_id alors on ca chercher les exercises specifique a celui ci
	//init the object	
	require_once(DIR_MODEL.'categories-exercises.php');
	$oCat = new CategoriesExercises(
		$oReg, 
		$oGlob
		);
	//do the search	
	$arrExercises = $oCat->getExercises($iCatId, $iFilterId);
	//	
	//print_r($arrExercises);
	//clean
	unset($oCat);
	//init widget listing des categories
	require_once(DIR_WIDGET.'exercise-categories/controller/exercise-categories.php');
	$oExCat = new widgetExerciseCategoriesController(
		$oReg, 
		$oGlob,
		$arrExercises, 
		'exercise-exercises'
		);		
	//check si il existe sinon on passe
	if($oExCat->getWidget()){
		$arrOutput['content']['listing'] = $oExCat->getHtml(
			'exercises',
			'exercise-exercises-result',
			$arrExercises['html'],
			$arrExercises['description']
			);	
		}
	//clean
	unset($oExCat);
	//on change le titre de la page
	$arrOutput['meta']['title'] = ucfirst($arrExercises['html']).' - '.ucfirst($arrExercises['category']['html']).' - '.ucfirst($arrOutput['meta']['title']);	
	$arrOutput['meta']['description'] = $arrExercises['description'];	
	$arrOutput['meta']['image'] = PATH_IMAGE_DEFAULT;	
	//canonique
	$arrOutput['meta']['canonical'] = $oGlob->getArray('links-by-key', 'exercises-categories-'.$oGlob->get('lang_prefix')).$arrExercises['category']['href'].DEFAULT_ID_SEPARATOR.$arrExercises['category']['id'].'/'.$arrExercises['href'].DEFAULT_ID_SEPARATOR.$arrExercises['id'].'/';
	//init widget le breadcrumb
	require_once(DIR_WIDGET.'breadcrumbs/controller/breadcrumbs.php');
	//le path du bread crumb	
	$arrBreadcrumb = $oReg->get('site')->getBreadcrumbFromPath(
		$oGlob->get('page').'/'.$oGlob->get('sub_page').'/',
		$oGlob
		);
	//on rajoute au array le path du categorie
	array_push($arrBreadcrumb, array(
		'text' => $arrExercises['category']['html'],
		'link' => $oGlob->getArray('links-by-key', 'exercises-categories-'.$oGlob->get('lang_prefix')).$arrExercises['category']['href'].DEFAULT_ID_SEPARATOR.$arrExercises['category']['id'].'/'
		));
	//on rajoute au array le path du filter
	array_push($arrBreadcrumb, array(
		'text' => $arrExercises['html'],
		'link' => $oGlob->getArray('links-by-key', 'exercises-categories-'.$oGlob->get('lang_prefix')).$arrExercises['category']['href'].DEFAULT_ID_SEPARATOR.$arrExercises['category']['id'].'/'.$arrExercises['href'].DEFAULT_ID_SEPARATOR.$arrExercises['id'].'/'
		));	
	$oBreadcrumbs = new widgetBreadcrumbsController(
		'top-breadcrumbs', 
		$arrBreadcrumb
		);
	$arrOutput['breadcrumbs'] = array(
		'html' => $oBreadcrumbs->getHtml()
		);
	//clean
	unset($oBreadcrumbs, $arrBreadcrumb, $arrExercises);



}else if($iCatId !== 0){
	//on a un cat id alors on ca chercher les filtre specifique a celui ci
	//init the object	
	require_once(DIR_MODEL.'categories-exercises.php');
	$oCat = new CategoriesExercises(
		$oReg, 
		$oGlob
		);
	//do the search	
	$arrFilters = $oCat->getFilters($iCatId);
	//clean
	unset($oCat);
	//init widget listing des categories
	require_once(DIR_WIDGET.'exercise-categories/controller/exercise-categories.php');
	$oExCat = new widgetExerciseCategoriesController(
		$oReg, 
		$oGlob,
		$arrFilters, 
		'exercise-filters'
		);		
	//check si il existe sinon on passe
	if($oExCat->getWidget()){
		$arrOutput['content']['listing'] = $oExCat->getHtml(
			'filters',
			'exercise-filters-result',
			$arrFilters['html'],
			$arrFilters['description']
			);	
		}
	//clean
	unset($oExCat);
	//on change le titre de la page
	$arrOutput['meta']['title'] = ucfirst($arrFilters['html']).' - '.ucfirst($arrOutput['meta']['title']);	
	$arrOutput['meta']['description'] = $arrFilters['description'];	
	$arrOutput['meta']['image'] = PATH_IMAGE_DEFAULT;	
	//canonique
	$arrOutput['meta']['canonical'] = $oGlob->getArray('links-by-key', 'exercises-categories-'.$oGlob->get('lang_prefix')).$arrFilters['href'].DEFAULT_ID_SEPARATOR.$arrFilters['id'].'/';
	//init widget le breadcrumb
	require_once(DIR_WIDGET.'breadcrumbs/controller/breadcrumbs.php');
	//le path du bread crumb	
	$arrBreadcrumb = $oReg->get('site')->getBreadcrumbFromPath(
		$oGlob->get('page').'/'.$oGlob->get('sub_page').'/',
		$oGlob
		);
	//on rajoute au array le path du categorie
	array_push($arrBreadcrumb, array(
		'text' => $arrFilters['html'],
		'link' => $oGlob->getArray('links-by-key', 'exercises-categories-'.$oGlob->get('lang_prefix')).$arrFilters['href'].DEFAULT_ID_SEPARATOR.$arrFilters['id'].'/'
		));
	$oBreadcrumbs = new widgetBreadcrumbsController(
		'top-breadcrumbs', 
		$arrBreadcrumb
		);
	$arrOutput['breadcrumbs'] = array(
		'html' => $oBreadcrumbs->getHtml()
		);
	//clean
	unset($oBreadcrumbs, $arrBreadcrumb, $arrFilters);
	
	
}else{
	//on change le titre de la page
	$arrOutput['meta']['title'] = ucfirst($arrOutput['meta']['title']);
	$arrOutput['meta']['image'] = PATH_IMAGE_DEFAULT;		
	//canonique
	$arrOutput['meta']['canonical'] = $oGlob->getArray('links-by-key', 'exercises-categories-'.$oGlob->get('lang_prefix'));
	//init the object	
	require_once(DIR_MODEL.'categories-exercises.php');
	$oCat = new CategoriesExercises(
		$oReg, 
		$oGlob
		);
	//do the search	
	$arrCat = $oCat->getAllCategories();
	//clean
	unset($oCat);
	//init widget listing des categories
	require_once(DIR_WIDGET.'exercise-categories/controller/exercise-categories.php');
	$oExCat = new widgetExerciseCategoriesController(
		$oReg, 
		$oGlob, 
		$arrCat, 
		'exercise-categories'
		);	
	//check si il existe sinon on passe
	if($oExCat->getWidget()){
		$arrOutput['content']['listing'] = $oExCat->getHtml(
			'categories',
			'exercise-categories-result',
			$arrOutput['content']['text'],
			$arrOutput['meta']['description']
			);	
		}
	//clean
	unset($oExCat);		
	//init widget le breadcrumb
	require_once(DIR_WIDGET.'breadcrumbs/controller/breadcrumbs.php');
	$oBreadcrumbs = new widgetBreadcrumbsController(
		'top-breadcrumbs', 
		$oReg->get('site')->getBreadcrumbFromPath(
			$oGlob->get('path'),
			$oGlob
			)
		);
	$arrOutput['breadcrumbs'] = array(
		'html' => $oBreadcrumbs->getHtml()
		);
	//clean
	unset($oBreadcrumbs, $oExCat, $arrCat);

	} 



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





