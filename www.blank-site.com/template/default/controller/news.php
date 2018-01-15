<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default controller for standard news from DB cc_news


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
//extra class par defaut
$arrOutput['content']['class'] = 'standard news-view';	
//	
require_once(DIR_MODEL.'news.php');
$oNews = new News($oReg, $oGlob->get('lang'));
//la page NEWS
if($oGlob->get('sub_page') == ''){
	//utilisons la cache
	$bFileExist = false;	
	//cache file	
	$strCacheFile = 'news.content.'.$oGlob->get('lang');	
	//cache
	$arrControllerNewsContent = $oReg->get('cache')->cacheRead($strCacheFile);	
	if(is_array($arrControllerNewsContent)){
		$bFileExist = true;
	}else{	
		//on cherche le content
		$arrControllerNewsContent = $oNews->getCategoryInfos($oGlob->get('page'));
		}
	//cache	write
	if(!$bFileExist){
		$oReg->get('cache')->cacheWrite($strCacheFile, $arrControllerNewsContent);
		}
	//on set toutes les vars de la view dans le array 
	$arrOutput['content']['title'] = $arrControllerNewsContent['title'];
	$arrOutput['content']['text'] = safeReverse($arrControllerNewsContent['content']);
	$arrOutput['content']['class'] .= ' '.$arrControllerNewsContent['css_class'];
	//les metas
	$arrOutput['meta']['title'] =  $arrControllerNewsContent['meta_title'];
	$arrOutput['meta']['description'] = $arrOutput['content']['text'];
	$arrOutput['meta']['keywords'] =  $arrControllerNewsContent['meta_keywords'];
	$arrOutput['meta']['lang'] = $arrControllerNewsContent['code'];
	$arrOutput['meta']['lang_prefix'] = $oGlob->get('lang_prefix');
	$arrOutput['meta']['image'] = PATH_IMAGE_DEFAULT;	
	//canonique
	$arrOutput['meta']['canonical'] = $oGlob->getArray('links-by-key', 'news-'.$oGlob->get('lang_prefix'));
	//init widget le breadcrumb
	require_once(DIR_WIDGET.'breadcrumbs/controller/breadcrumbs.php');
	$oBreadcrumbs = new widgetBreadcrumbsController('top-breadcrumbs', $oReg->get('site')->getBreadcrumbFromPath($oReg->get('req')->get('path'), $oGlob));
	$arrOutput['breadcrumbs'] = array();
	$arrOutput['breadcrumbs']['html'] = $oBreadcrumbs->getHtml();
	//clean
	unset($oBreadcrumbs);	
	//listing des categorie de news
	$arrOutput['content']['type'] = 'category';
	//utilisons la cache
	$bFileExist = false;	
	//cache file	
	$strCacheFile = 'news.'.$arrOutput['content']['type'].'.'.$oGlob->get('lang');	
	//cache
	$arrNewsCategory = $oReg->get('cache')->cacheRead($strCacheFile);	
	if(is_array($arrNewsCategory)){
		$bFileExist = true;
	}else{	
		//on cherche le content
		$arrNewsCategory = $oNews->getNewsCategory();
		}
	//cache	write
	if(!$bFileExist){
		$oReg->get('cache')->cacheWrite($strCacheFile, $arrNewsCategory);
		}	
	$arrOutput['content']['category'] = $arrNewsCategory;
	//structure data
	$arrOutput['structure'] = 'webpage';	
	//on change le page view
	$oGlob->set('page_view', VIEW_NEWS.'-'.$arrOutput['content']['type']);

}else{
	//les sidebar
	$arrOutput['sidebar'] = array();
	//pas de args ou /page/XX/	
	if($oGlob->get('args_page') == '' || preg_match('/^page\/[0-9]+/',$oGlob->get('args_page'))){ 
		//utilisons la cache
		$bFileExist = false;	
		//cache file	
		$strCacheFile = 'news.content.'.$oGlob->get('sub_page').'.'.$oGlob->get('lang');	
		//cache
		$arrControllerNewsContent = $oReg->get('cache')->cacheRead($strCacheFile);	
		if(is_array($arrControllerNewsContent)){
			$bFileExist = true;
		}else{	
			//listing des news selon la sub_page = categorie
			$arrControllerNewsContent = $oNews->getCategoryInfos($oGlob->get('sub_page'));
			}
		//cache	write
		if(!$bFileExist){
			$oReg->get('cache')->cacheWrite($strCacheFile, $arrControllerNewsContent);
			}
		//on set toutes les vars de la view dans le array 
		$arrOutput['content']['title'] = $arrControllerNewsContent['title'];
		$arrOutput['content']['text'] = safeReverse($arrControllerNewsContent['content']);
		$arrOutput['content']['class'] .= ' '.$arrControllerNewsContent['css_class'];
		$arrOutput['content']['link_id'] = $arrControllerNewsContent['link_id'];
		//les metas
		$arrOutput['meta']['title'] =  $arrControllerNewsContent['meta_title'];
		$arrOutput['meta']['description'] =  $arrControllerNewsContent['meta_description'];
		$arrOutput['meta']['keywords'] =  $arrControllerNewsContent['meta_keywords'];
		$arrOutput['meta']['lang'] = $arrControllerNewsContent['code'];
		$arrOutput['meta']['lang_prefix'] = $oGlob->get('lang_prefix');
		$arrOutput['meta']['image'] = PATH_IMAGE_DEFAULT;	
		//canonique
		$arrOutput['meta']['canonical'] = $oGlob->getArray('links', $arrControllerNewsContent['link_id']);
		if($oGlob->get('args_page') != ''){
			$arrOutput['meta']['canonical'] .= $oGlob->get('args_page').'/';
			}
		//total
		$iTotalCount = $oNews->getNewsCountFromCategory($arrControllerNewsContent['id']);
		$iMaxPerPage = LIMIT_NEWS_PER_PAGE;
		$iStartPage = 0;
		if($oReg->get('req')->get('page')){
			$iStartPage = intval($oReg->get('req')->get('page'));
		}else{
			//if(preg_match('/^page\/([0-9]+)\//',$oGlob->get('args_page'), $arrPregRes)){
			if(preg_match('/^page\/([0-9]+)/',$oGlob->get('args_page'), $arrPregRes)){
				$iStartPage = intval($arrPregRes[1]);
			}else{
				$iStartPage = 0;
				}
			}
		//widget pagination
		require_once(DIR_WIDGET.'pagination/controller/pagination.php');
		$oPagination = new widgetPaginationController('news-pagination', $iTotalCount, $iStartPage, $iMaxPerPage, $arrOutput['content']['link_id']);
		//check si il existe sinon on passe
		if($oPagination->getWidget()){
			$arrOutput['pagination'] = array();
			$arrOutput['pagination']['html'] = $oPagination->getHtml($oGlob->get('links'));
			}
		//clean
		unset($oPagination);
		//les news listing			
		$arrOutput['content']['type'] = 'listing';	
		//structure data
		$arrOutput['structure'] = 'webpage';
		//on change le page view
		$oGlob->set('page_view', VIEW_NEWS.'-'.$arrOutput['content']['type']);
		//utilisons la cache
		$bFileExist = false;	
		//cache file	
		$strCacheFile = 'news.listing.'.$oGlob->get('sub_page').'.'.$iMaxPerPage.'.'.($iStartPage * $iMaxPerPage).'.'.$oGlob->get('lang');	
		//cache
		$arrContentListing = $oReg->get('cache')->cacheRead($strCacheFile);	
		if(is_array($arrContentListing)){
			$bFileExist = true;
		}else{	
			//categories
			$arrContentListing = $oNews->getNewsFromCategory($arrControllerNewsContent['id'], $iMaxPerPage , ($iStartPage * $iMaxPerPage));
			}
		//cache	write
		if(!$bFileExist){
			$oReg->get('cache')->cacheWrite($strCacheFile, $arrContentListing);
			}
		//categories
		$arrOutput['content']['listing'] = $arrContentListing;
		//clean
		unset($arrContentListing);
		//utilisons la cache
		$bFileExist = false;	
		//cache file	
		$strCacheFile = 'news.breadcrumbs.'.$oGlob->get('sub_page').'.'.$oGlob->get('lang');	
		//cache
		$arrBreadcrumbs = $oReg->get('cache')->cacheRead($strCacheFile);	
		if(is_array($arrBreadcrumbs)){
			$bFileExist = true;
		}else{	
			//widget breadcrumb
			$arrBreadcrumbs = $oNews->getNewsBreadcrumbs($arrControllerNewsContent['id']);
			}
		//cache	write
		if(!$bFileExist){
			$oReg->get('cache')->cacheWrite($strCacheFile, $arrBreadcrumbs);
			}
		//minor check
		if($arrBreadcrumbs){
			$arrBreadcrumbs = array_reverse($arrBreadcrumbs);
			foreach($arrBreadcrumbs as $k=>$v){
				$arrBreadcrumbs[$k] = array(
					'text' => $v['title'],
					'link' => $oGlob->getArray('links',$v['link_id']),
					);
				}
			//on push le home par defaut au debut du path
			array_unshift($arrBreadcrumbs, $oReg->get('site')->getBreadcrumbHome($oGlob));
			//init widget
			require_once(DIR_WIDGET.'breadcrumbs/controller/breadcrumbs.php');
			$oBreadcrumbs = new widgetBreadcrumbsController('top-breadcrumbs', $arrBreadcrumbs);
			$arrOutput['breadcrumbs'] = array();
			$arrOutput['breadcrumbs']['html'] = $oBreadcrumbs->getHtml();
			//clean
			unset($oBreadcrumbs);					
			}
		//clean
		unset($arrBreadcrumbs);	
		//utilisons la cache
		$bFileExist = false;	
		//cache file	
		$strCacheFile = 'news.sidebar.'.$oGlob->get('sub_page').'.'.$oGlob->get('lang');	
		//cache
		$arrSidebarCategory = $oReg->get('cache')->cacheRead($strCacheFile);	
		if(is_array($arrSidebarCategory)){
			$bFileExist = true;
		}else{	
			//widget sidebar category
			$arrSidebarCategory = $oNews->getNewsCategory();
			}
		//cache	write
		if(!$bFileExist){
			$oReg->get('cache')->cacheWrite($strCacheFile, $arrSidebarCategory);
			}
		if($arrSidebarCategory){
			if(!isset($arrOutput['sidebar'])){//si pas initialise
				$arrOutput['sidebar'] = array();
				}
			foreach($arrSidebarCategory as $k=>$v){
				$arrSidebarCategory[$k] = array(
					'text' => $v['title'],
					'link' => $oGlob->getArray('links',$v['link_id']),
					);
				}
			//init widget
			require_once(DIR_WIDGET.'sidebar/controller/sidebar.php');
			//sidebar des category		
			$oSidebar = new widgetSidebarController('news-sidebar-category', 'listing', $arrSidebarCategory);
			$arrOutput['sidebar']['category'] = array();
			$arrOutput['sidebar']['category']['html'] = $oSidebar->getHtml('sidebar-left', _T('news categories'));
			//clean
			unset($oSidebar);
			}	
		//clean	
		unset($arrSidebarCategory);

	}else{
		//split les args sur le slash /
		$arrPageArgs = explode('/', $oGlob->get('args_page'));
		//print_r($arrPageArgs);	
		//show the news
		//if(is_array($arrPageArgs) && isset($arrPageArgs[0])){
		if(is_array($arrPageArgs)){
			//on cherche le id qui se trouve a la finde la string
			//$newsId = intVal($arrPageArgs[0]); //si format /XX/nom-de-la-news/
			$newsId = array_pop($arrPageArgs); //si format /nom-de-la-news/XX/
			if($newsId){
				//utilisons la cache
				$bFileExist = false;	
				//cache file	
				$strCacheFile = 'news.content.items.'.$newsId.'.'.$oGlob->get('lang');	
				//cache
				$arrControllerNewsContent = $oReg->get('cache')->cacheRead($strCacheFile);	
				if(is_array($arrControllerNewsContent)){
					$bFileExist = true;
				}else{	
					//on cherche les infos
					$arrControllerNewsContent = $oNews->getNewsInfos($newsId);
					}
				//cache	write
				if(!$bFileExist){
					$oReg->get('cache')->cacheWrite($strCacheFile, $arrControllerNewsContent);
					}	
				if(!$arrControllerNewsContent){
					//on ne trouve pas le ID de la news alors un mauvais path redirect donc
					Header('Location:'.PATH_404);
					exit();
					}
				//le type de content
				$arrOutput['content']['type'] = 'details';
				//structure data
				$arrOutput['structure'] = 'webpage';
				//on change le page view
				$oGlob->set('page_view', VIEW_NEWS.'-'.$arrOutput['content']['type']);
				//on set toutes les vars de la view dans le array 
				$arrOutput['content']['title'] = $arrControllerNewsContent['title'];
				$arrOutput['content']['text'] = safeReverse($arrControllerNewsContent['content']);
				$arrOutput['content']['date_added'] = safeReverse($arrControllerNewsContent['date_added']);
				$arrOutput['content']['category_id'] = $arrControllerNewsContent['category_id'];
				$arrOutput['content']['category_title'] = $arrControllerNewsContent['category_title'];
				$arrOutput['content']['link_id'] = $arrControllerNewsContent['link_id'];
				$arrOutput['content']['hits'] = $arrControllerNewsContent['hits'];
				$arrOutput['content']['id'] = $newsId;
				//les metas
				$arrOutput['meta']['title'] =  $arrControllerNewsContent['meta_title'];
				$arrOutput['meta']['description'] =  $arrControllerNewsContent['meta_description'];
				$arrOutput['meta']['keywords'] =  $arrControllerNewsContent['meta_keywords'];
				$arrOutput['meta']['lang'] = $arrControllerNewsContent['code'];
				$arrOutput['meta']['lang_prefix'] = $oGlob->get('lang_prefix');
				//canonique
				$arrOutput['meta']['canonical'] = $oGlob->getArray('links', $arrControllerNewsContent['link_id']);
				if($oGlob->get('args_page') != ''){
					$arrOutput['meta']['canonical'] .= $oGlob->get('args_page').'/';
					}
				//widget pager
				//utilisons la cache
				$bFileExist = false;	
				//cache file	
				$strCacheFile = 'news.pager.items.'.$newsId.'.'.$oGlob->get('lang');	
				//cache
				$arrPager = $oReg->get('cache')->cacheRead($strCacheFile);	
				if(is_array($arrPager)){
					$bFileExist = true;
				}else{	
					//on cherche les infos
					$arrPager = $oNews->getNewsPager($newsId, $arrControllerNewsContent['category_id']);
					}
				//cache	write
				if(!$bFileExist){
					$oReg->get('cache')->cacheWrite($strCacheFile, $arrPager);
					}	
				if($arrPager){
					//newest news
					if($arrPager['newer']){
						$arrPager['newer'] = array(
							'link' => $oGlob->getArray('links',$arrPager['newer']['link_id']).$arrPager['newer']['alias'].'/'.$arrPager['newer']['id'].'/',
							);
						}
					//older news
					if($arrPager['older']){
						$arrPager['older'] = array(
							'link' => $oGlob->getArray('links',$arrPager['older']['link_id']).$arrPager['older']['alias'].'/'.$arrPager['older']['id'].'/',
							);
						}
					//text button
					$arrPager['next-text'] = _T('next');
					$arrPager['previous-text'] = _T('previous');
					//inc
					require_once(DIR_WIDGET.'pager/controller/pager.php');
					$oPager = new widgetPagerController('news-pager', $arrPager);
					$arrOutput['pager'] = array();
					$arrOutput['pager']['html'] = $oPager->getHtml();
					//clean
					unset($oPager);					
					}
				//clean
				unset($arrPager);		
				//widget breadcrumb
				//utilisons la cache
				$bFileExist = false;	
				//cache file	
				$strCacheFile = 'news.breadcrumbs.items.'.$newsId.'.'.$oGlob->get('lang');	
				//cache
				$arrBreadcrumbs = $oReg->get('cache')->cacheRead($strCacheFile);	
				if(is_array($arrBreadcrumbs)){
					$bFileExist = true;
				}else{	
					//widget breadcrumb
					$arrBreadcrumbs = $oNews->getNewsBreadcrumbs($arrControllerNewsContent['category_id']);
					}
				//cache	write
				if(!$bFileExist){
					$oReg->get('cache')->cacheWrite($strCacheFile, $arrBreadcrumbs);
					}
				if($arrBreadcrumbs){
					$arrBreadcrumbs = array_reverse($arrBreadcrumbs);
					foreach($arrBreadcrumbs as $k=>$v){
						$arrBreadcrumbs[$k] = array(
							'text' => $v['title'],
							'link' => $oGlob->getArray('links', $v['link_id']),
							);
						}
					//on rajoute la news ou on est
					array_push($arrBreadcrumbs, array(
						'text' => $arrControllerNewsContent['title'],
						'link' => $oGlob->getArray('links', $arrControllerNewsContent['link_id']).$oGlob->get('args_page').'/'
						));
					//on push le home par defaut au debut du path
					array_unshift($arrBreadcrumbs, $oReg->get('site')->getBreadcrumbHome($oGlob));
					//init widget
					require_once(DIR_WIDGET.'breadcrumbs/controller/breadcrumbs.php');
					$oBreadcrumbs = new widgetBreadcrumbsController('top-breadcrumbs', $arrBreadcrumbs);
					$arrOutput['breadcrumbs'] = array();
					$arrOutput['breadcrumbs']['html'] = $oBreadcrumbs->getHtml();
					//clean
					unset($oBreadcrumbs);					
					}
				//clean
				unset($arrBreadcrumbs);				
				//widget sidebar category
				//utilisons la cache
				$bFileExist = false;	
				//cache file	
				$strCacheFile = 'news.sidebar-categories.items.'.$newsId.'.'.$oGlob->get('lang');	
				//cache
				$arrSidebarCategory = $oReg->get('cache')->cacheRead($strCacheFile);	
				if(is_array($arrSidebarCategory)){
					$bFileExist = true;
				}else{	
					//widget sidebar category
					$arrSidebarCategory = $oNews->getNewsCategory();
					}
				//cache	write
				if(!$bFileExist){
					$oReg->get('cache')->cacheWrite($strCacheFile, $arrSidebarCategory);
					}
				if($arrSidebarCategory){
					if(!isset($arrOutput['sidebar'])){//si pas initialise
						$arrOutput['sidebar'] = array();
						}
					foreach($arrSidebarCategory as $k=>$v){
						$arrSidebarCategory[$k] = array(
							'text' => $v['title'],
							'link' => $oGlob->getArray('links',$v['link_id']),
							);
						}
					//init widget
					require_once(DIR_WIDGET.'sidebar/controller/sidebar.php');
					//sidebar des category		
					$oSidebar = new widgetSidebarController('news-sidebar-category', 'listing', $arrSidebarCategory);
					$arrOutput['sidebar']['category'] = array();
					$arrOutput['sidebar']['category']['html'] = $oSidebar->getHtml('sidebar-left', _T('news categories'));
					//clean
					unset($oSidebar);
					}
				unset($arrSidebarCategory);	
				//widget sidebar latest news from same category
				//utilisons la cache
				$bFileExist = false;	
				//cache file	
				$strCacheFile = 'news.sidebar-latestnews.items.'.$newsId.'.'.$oGlob->get('lang');	
				//cache
				$arrSidebarLatestNews = $oReg->get('cache')->cacheRead($strCacheFile);	
				if(is_array($arrSidebarLatestNews)){
					$bFileExist = true;
				}else{	
					//widget sidebar category
					$arrSidebarLatestNews = $oNews->getNewsFromCategory($arrControllerNewsContent['category_id'], 5);
					}
				//cache	write
				if(!$bFileExist){
					$oReg->get('cache')->cacheWrite($strCacheFile, $arrSidebarLatestNews);
					}
				//
				if($arrSidebarLatestNews){
					if(!isset($arrOutput['sidebar'])){//si pas initialise
						$arrOutput['sidebar'] = array();
						}
					foreach($arrSidebarLatestNews as $k=>$v){
						$arrSidebarLatestNews[$k] = array(
							'text' => $v['title'],
							'link' => $oGlob->getArray('links',$v['link_id']).$v['alias'].'/'.$v['id'].'/',
							);
						}
					//init widget
					require_once(DIR_WIDGET.'sidebar/controller/sidebar.php');
					//sidebar des category	
					$oSidebar = new widgetSidebarController('news-sidebar-latest-news', 'listing', $arrSidebarLatestNews);
					$arrOutput['sidebar']['latest_news'] = array();
					$arrOutput['sidebar']['latest_news']['html'] = $oSidebar->getHtml('sidebar-left', _T('latest news'));
					//clean
					unset($oSidebar);	
					}
				unset($arrSidebarLatestNews);	
				//content hits stats for news only, the rest is only listing
				if($newsId != 0){
					$oNews->setContentHits($newsId);
					}
			}else{
				//on ne trouve pas le ID de la news alors un mauvais path redirect donc
				Header('Location:'.PATH_404);
				exit();
				}
			}
		unset($arrPageArgs);
		unset($newsId);
		}
	}

//clean
unset($arrControllerNewsContent);	
unset($oNews);	
	
//on set toutes les vars de la view dans le array avec les differents header, meta et du footer
require_once(DIR_MODEL.MODEL_DEFAULT_CSS);

//structure data
require_once(DIR_MODEL.MODEL_DEFAULT_STRUCTURE);

//les scripts
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