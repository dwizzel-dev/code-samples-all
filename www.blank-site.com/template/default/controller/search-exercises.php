<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default controller for search exercises page 

*/
//-------------------------------------------------------------------------------

//la premiere page par defaut
$iStartPage = 0;
$bDirectQuerySearch = false;
//on peut avoir une requete direct aussi avec le ?q=xxxx-xxxx
if($oReg->get('req')->get('q') !== false){	
	$bDirectQuerySearch = true;
	//on va setter le args_page avec ca si vient d'une demande google
	$oGlob->set('args_page', $oReg->get('req')->get('q'));
	}
//si on a une page on enleve et reset le args_page
if(preg_match('/\/page\/([0-9]+)$/',$oGlob->get('args_page'), $arrPregRes)){
	//on garde la page	
	$iStartPage = intVal($arrPregRes[1]);
	//on remove le numero de page /page/XX/ et on reset le args page
	$regPattern = '/\/page\/([0-9]+)$/';	
	//
	$oGlob->set('args_page', preg_replace($regPattern, '', $oGlob->get('args_page')));			
	}
//si il y a eu une manipulation de url on recall 
//la page avec le url modifie pour modifier les hack
$strRedirect = redirectKeywordUrl($oGlob->get('args_page'));
if($strRedirect && $bDirectQuerySearch === false){
	//on redirect 
	$strRequest = $oGlob->getArray('links-by-key', 'exercises-result-'.$oGlob->get('lang_prefix'));
	$strRequest .= urlencode($strRedirect).'/';
	//la page de base	
	//$strRequest .= 'page/'.$iStartPage.'/';
	Header('HTTP/1.1 302');
	Header('Location: '.$strRequest);
	exit();	
	}
//clean
unset($strRedirect);

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
//on set le keyword
$oGlob->set('current-search-word', str_replace(DEFAULT_ID_SEPARATOR, ' ', trimKeyword($oGlob->get('args_page'))));


//-------------------------------------------------------------------------------
//init widget le breadcrumb
require_once(DIR_WIDGET.'breadcrumbs/controller/breadcrumbs.php');
$oBreadcrumbs = new widgetBreadcrumbsController(
	'top-breadcrumbs', 
	$oReg->get('site')->getBreadcrumbFromPath(
		$oGlob->get('page').'/'.$oGlob->get('sub_page').'/'.$oGlob->get('current-search-word'),
		$oGlob
		)
	);
$arrOutput['breadcrumbs'] = array();
$arrOutput['breadcrumbs']['html'] = $oBreadcrumbs->getHtml();
//clean
unset($oBreadcrumbs);

//-------------------------------------------------------------------------------
//on va faire la recherche si il y a des keyword a chercher
if($oGlob->get('args_page') != '' && $oGlob->get('args_page') !== false){	

	$iMaxPerPage = LIMIT_RESULT_PER_PAGE;
	//init the object	
	require_once(DIR_MODEL.'search-exercises.php');
	$oSearch = new SearchExercises(
		$oReg, 
		$oGlob->get('lang'), 
		$iMaxPerPage, 
		$iStartPage
		);
	//on change le titre de la page
	$arrOutput['meta']['title'] = ucfirst($oGlob->get('current-search-word')).' - '.ucfirst(_T('page')).' '.($iStartPage + 1).' - '.ucfirst($arrOutput['meta']['title']);
	$arrOutput['meta']['image'] = PATH_IMAGE_DEFAULT;	
	//canonique
	$arrOutput['meta']['canonical'] = $oGlob->getArray('links-by-key', 'exercises-result-'.$oGlob->get('lang_prefix')).$oGlob->get('args_page').'/';
	//do the search	
	//$arrExercisesListing = $oSearch->getExerciseListing($oGlob->get('current-search-word'));	
	$arrExercisesListing = $oSearch->getExerciseListingFromSeparatedKw($oGlob->get('current-search-word'));	
	//clean	
	unset($oSearch);
	//print_r($arrExercisesListing);	
	//si des result on met la pagination
	if(isset($arrExercisesListing['total']) && $arrExercisesListing['total'] > 0){	
		//widget pagination
		require_once(DIR_WIDGET.'pagination/controller/pagination.php');
		$oPagination = new widgetPaginationController(
			'search-pagination', 
			$arrExercisesListing['total'], 
			$iStartPage, 
			$iMaxPerPage, 
			0 //on set le link id a 0 car plus loin on met un lien direct et non base sur un id existant
			);
		//check si il existe sinon on passe
		if($oPagination->getWidget()){
			//le link pour les autres pages
			$strPaginationLink = $oGlob->getArray(
				'links-by-key', 
				'exercises-result-'.$oGlob->get('lang_prefix')
				);
			$strPaginationLink .= $oGlob->get('args_page').'/';		
			//le path qui N,est pas un path de page standard comme news
			$arrOutput['pagination'] = array();
			$arrOutput['pagination']['html'] = $oPagination->getHtml(
				array(0 => $strPaginationLink) //on passe un array avec le ID passe plus haut
				);
			//clean
			unset($strPaginationLink);
			}
		//clean
		unset($oPagination);
		//widget listing des exercises
		require_once(DIR_WIDGET.'exercise-listing/controller/exercise-listing.php');
		$oExListing = new widgetExerciseListingController(
			$oReg, 
			$oGlob, 
			$arrExercisesListing['exercises'], 
			'exercise-listing'
			);	
		//check si il existe sinon on passe
		if($oExListing->getWidget()){
			$arrOutput['content']['listing'] = $oExListing->getHtml(
				'exercise-listing-result', 
				_T('exercise result').' "<span>'.$oGlob->get('current-search-word').'</span>"',
				sprintf(_T('showing %d to %d of %d'), $arrExercisesListing['start'], $arrExercisesListing['end'], $arrExercisesListing['total'])
				);	
			}
		//clean
		unset($oExListing);
		}
	//clean
	unset($arrExercisesListing);
	//structure data
	$arrOutput['structure'] = 'searchpage';		
}else{
	/*
	//structure data
	$arrOutput['structure'] = 'webpage';

	//sinon on quitte vers la reche de base
	//si pas de id alors on retourne au search
	//on redirect vers la recherche
	$strRedirect = $oGlob->getArray('links-by-key', 'exercises-search-'.$oGlob->get('lang_prefix'));
	Header('HTTP/1.1 301 Moved Permanently');
	Header('Location: '.$strRedirect);
	exit();	
	*/
	}

//-------------------------------------------------------------------------------

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





