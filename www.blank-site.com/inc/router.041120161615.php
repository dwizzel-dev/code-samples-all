<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	basic routing vers le controlleur
@notes:	route table are cache pour eviter les requete sql repetitive

*/

//fill the array with route from cc_content/cc_links
$bFileExist = false;
$routerFile = 'router';
//cache
$arrTmpRouter = $oReg->get('cache')->cacheRead($routerFile);
//check
if(is_array($arrTmpRouter)){
	$bFileExist = true;

}else{
	$arrTmpRouter = array();
	
	foreach($oReg->get('site')->getRoute() as $k=>$v){
		$arrTmpRouter[$v['path']] = array(
			'controller' => $v['controller'],
			'content_id' => $v['id'],
			'link_id' => $v['link_id'],
			);		
		}

	//fill the route with the news_category/links
	foreach($oReg->get('site')->getNewsRoute() as $k=>$v){
		$arrTmpRouter[$v['path']] = array(
			'controller' => $v['controller'],
			'content_id' => $v['id'],
			'link_id' => $v['link_id'],	
			);		
		}	
		
	}

//cache	
if(!$bFileExist){
	$oReg->get('cache')->cacheWrite($routerFile, $arrTmpRouter);
	}
		
//set it to global
if($oGlob->get('page') == ''){
	//default to home
	$oGlob->set('router', CONTROLLER_DEFAULT_HOME);
	eval('$oGlob->set(\'content_id\', $arrTmpRouter[ROUTER_KEY_DEFAULT_HOME_'.strtoupper($oGlob->get('lang_prefix')).'][\'content_id\']);');
	eval('$oGlob->set(\'link_id\', $arrTmpRouter[ROUTER_KEY_DEFAULT_HOME_'.strtoupper($oGlob->get('lang_prefix')).'][\'link_id\']);');	
}else if(isset($arrTmpRouter[$oGlob->get('page').'/'.$oGlob->get('sub_page')])){
	//set the controller si la bonne page et sub_page
	$oGlob->set('link_id', $arrTmpRouter[$oGlob->get('page').'/'.$oGlob->get('sub_page')]['link_id']);	
	$oGlob->set('router', $arrTmpRouter[$oGlob->get('page').'/'.$oGlob->get('sub_page')]['controller']);
	$oGlob->set('content_id', $arrTmpRouter[$oGlob->get('page').'/'.$oGlob->get('sub_page')]['content_id']);
}else if(isset($arrTmpRouter[$oGlob->get('page')]) && $oGlob->get('sub_page').'' == ''){
	//set the controller si la bonne page et pas de sub_page dans le path
	$oGlob->set('link_id', $arrTmpRouter[$oGlob->get('page')]['link_id']);	
	$oGlob->set('router', $arrTmpRouter[$oGlob->get('page')]['controller']);
	$oGlob->set('content_id', $arrTmpRouter[$oGlob->get('page')]['content_id']);
}else if(isset($arrTmpRouter[$oGlob->get('page')]) && $oGlob->get('sub_page').'' != ''){
	//dwizzel 18-09-2013
	//set the controller si la bonne page et pas de sub_page dans le path
	$oGlob->set('link_id', $arrTmpRouter[$oGlob->get('page')]['link_id']);
	$oGlob->set('router', $arrTmpRouter[$oGlob->get('page')]['controller']);
	$oGlob->set('content_id', $arrTmpRouter[$oGlob->get('page')]['content_id']);	
}else{
	//error 404
	$oGlob->set('router', CONTROLLER_DEFAULT_404);
	//Header('Location:'.PATH_404);
	//exit();
	}
	
	
//print_r($arrTmpRouter);exit();	

//fo debug only remove for prod
$oGlob->set('controller', $arrTmpRouter);


//clean
unset($arrTmpRouter);


//END



