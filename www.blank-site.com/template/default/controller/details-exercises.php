<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default controller for exercises details


*/
//-------------------------------------------------------------------------------
//ca nous prend un id d'exercise sinon on retourne au search
$iExerciseId = 0;
//si on a un id et reset le args_page
if(preg_match('/'.preg_quote(DEFAULT_ID_SEPARATOR).'([0-9]+)$/',$oGlob->get('args_page'), $arrPregRes)){
	//on garde la id	
	$iExerciseId = intVal($arrPregRes[1]);
	}
//si pas de id alors on retourne au search
if(!$iExerciseId){
	//on redirect vers la recherche
	$strRedirect = $oGlob->getArray('links-by-key', 'exercises-result-'.$oGlob->get('lang_prefix'));
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
//init the object	
require_once(DIR_MODEL.'details-exercises.php');
$oDetails = new DetailsExercises(
	$oReg, 
	$oGlob
	);
//do the search	
$arrExercise = $oDetails->getExerciseDetails($iExerciseId);
//si false alors on retourne a la recherche
if(!$arrExercise){
	//on redirect vers la recherche
	$strRedirect = $oGlob->getArray('links-by-key', 'exercises-result-'.$oGlob->get('lang_prefix'));
	Header('HTTP/1.1 301 Moved Permanently');
	Header('Location: '.$strRedirect);
	exit();	
	}
//clean
unset($oDetails, $iExerciseId);	
//print_r($arrExercise);
//on set le title
$arrOutput['meta']['title'] = ucfirst($arrExercise['html']).' - '.ucfirst($arrOutput['meta']['title']);
$arrOutput['meta']['description'] = $arrExercise['description'];
//canonique
$arrOutput['meta']['canonical'] = $oGlob->getArray('links-by-key', 'exercises-details-'.$oGlob->get('lang_prefix')).$arrExercise['href'].DEFAULT_ID_SEPARATOR.$arrExercise['id'].'/';
//image de base
if($arrExercise['thumb0'] != ''){
	$arrOutput['meta']['image'] = PATH_IMAGE_EXERCISE.$arrExercise['thumb0'];
}else{
	$arrOutput['meta']['image'] = PATH_IMAGE_DEFAULT;
	}
//les liens alternate
if(count($arrExercise['languages'])){
	//on met les liens alternatif des autres langues
	$arrOutput['meta']['alternate'] = array();
	foreach($arrExercise['languages'] as $k=>$v){
		$strLangPrefixAlt = substr($k,0,2);
		$strBaseLangExDetailPage = $oGlob->getArray('links-by-key', 'exercises-details-'.$strLangPrefixAlt);
		if($strBaseLangExDetailPage !== false){
			$arrOutput['meta']['alternate'][$strLangPrefixAlt] = $strBaseLangExDetailPage.$v['href'].DEFAULT_ID_SEPARATOR.$v['id'].'/';
			}
		}
	}


//-------------------------------------------------------------------------------
//init widget le breadcrumb
require_once(DIR_WIDGET.'breadcrumbs/controller/breadcrumbs.php');
//le path du bread crumb	
$arrBreadcrumb = $oReg->get('site')->getBreadcrumbFromPath(
	$oGlob->get('page').'/'.$oGlob->get('sub_page').'/',
	$oGlob
	);
//on rajoute au array le path du categorie
array_push($arrBreadcrumb, array(
	'text' => $arrExercise['html'],
	'link' => $oGlob->getArray('links-by-key', 'exercises-details-'.$oGlob->get('lang_prefix')).$arrExercise['href'].DEFAULT_ID_SEPARATOR.$arrExercise['id'].'/'
	));
$oBreadcrumbs = new widgetBreadcrumbsController(
	'top-breadcrumbs', 
	$arrBreadcrumb
	);
$arrOutput['breadcrumbs'] = array();
$arrOutput['breadcrumbs']['html'] = $oBreadcrumbs->getHtml();
//clean
unset($oBreadcrumbs, $arrBreadcrumb);

//-------------------------------------------------------------------------------
//widget details des exercises
require_once(DIR_WIDGET.'exercise-details/controller/exercise-details.php');
$oExDetails = new widgetExerciseDetailsController(
	$oReg, 
	$oGlob, 
	$arrExercise, 
	'exercise-details'
	);	
//check si il existe sinon on passe
if($oExDetails->getWidget()){
	$arrOutput['content']['details'] = $oExDetails->getHtml(
		'exercise-details-result' 
		);	
	}
//clean
unset($oExDetails);

//-------------------------------------------------------------------------------

//structure data
$arrOutput['structure'] = 'medicalepage';

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





