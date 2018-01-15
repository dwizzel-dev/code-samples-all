<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	basic links
@notes:	links are cache pour eviter les requete sql repetitive

*/

//vars
$bFileExist = false;
$linksFileById = 'links.by-id';
$linksFileByKey = 'links.by-key';
$linksFileNameById = 'links.name-by-id';
$linksFileRedirect = 'links.redirect';
//cache
$arrTmpLinksById = $oReg->get('cache')->cacheRead($linksFileById);
$arrTmpLinksByKey = $oReg->get('cache')->cacheRead($linksFileByKey);
$arrTmpLinksNameById = $oReg->get('cache')->cacheRead($linksFileNameById);
$arrTmpLinksRedirect = $oReg->get('cache')->cacheRead($linksFileRedirect);

//BY IDS -------------------------------------------

//check
if(is_array($arrTmpLinksById)){
	$bFileExist = true;
}else{	
	$arrTmpLinksById = array();
	$arrLinks = $oReg->get('site')->getLinks();
	//fill the array
	if(SIMPLIFIED_URL){
		foreach($arrLinks as $k=>$v){
			if($v['extern'] == '1'){
				$arrTmpLinksById[$v['id']] = $v['path'];	
			}else{
				$arrTmpLinksById[$v['id']] = PATH_HOME.$v['prefix'].'/'.$v['path'].'/';	
				}
			}
	}else{
		foreach($arrLinks as $k=>$v){
			if($v['extern'] == '1'){
				$arrTmpLinksById[$v['id']] = $v['path'];	
			}else{
				$arrTmpLinksById[$v['id']] = PATH_HOME.'?&lang='.$v['code'].'&path='.$v['path'].'/';	
				}
			}
		}
	}
//cache
if(!$bFileExist){
	$oReg->get('cache')->cacheWrite($linksFileById, $arrTmpLinksById);
	}

unset($arrLinks);
		
//BY NAME----------------------------------------

//reset
$bFileExist = false;

//check
if(is_array($arrTmpLinksByKey)){
	$bFileExist = true;
}else{	
	$arrTmpLinksByKey = array();
	$arrLinks = $oReg->get('site')->getLinks();
	//fill the array
	if(SIMPLIFIED_URL){
		foreach($arrLinks as $k=>$v){
			if($v['extern'] == '1'){
				$arrTmpLinksByKey[$v['keyindex']] = $v['path'];	
			}else{
				$arrTmpLinksByKey[$v['keyindex']] = PATH_HOME.$v['prefix'].'/'.$v['path'].'/';	
				}
			}
	}else{
		foreach($arrLinks as $k=>$v){
			if($v['extern'] == '1'){	
				$arrTmpLinksByKey[$v['keyindex']] = $v['path'];	
			}else{
				$arrTmpLinksByKey[$v['keyindex']] = PATH_HOME.'?&lang='.$v['code'].'&path='.$v['path'].'/';	
				}
			}
		}
	}
//cache	
if(!$bFileExist){
	$oReg->get('cache')->cacheWrite($linksFileByKey, $arrTmpLinksByKey);
	}

unset($arrLinks);


//NAME BY ID----------------------------------------

//reset
$bFileExist = false;

//check
if(is_array($arrTmpLinksNameById)){
	$bFileExist = true;
}else{	
	$arrTmpLinksNameById = array();
	$arrLinks = $oReg->get('site')->getLinks();
	//fill the array
	foreach($arrLinks as $k=>$v){
		$arrTmpLinksNameById[$v['id']] = $v['name'];	
		}
	}
//cache	
if(!$bFileExist){
	$oReg->get('cache')->cacheWrite($linksFileNameById, $arrTmpLinksNameById);
	}

unset($arrLinks);

//REDIRECT BY PATH ----------------------------------------

//reset
$bFileExist = false;

//check
if(is_array($arrTmpLinksRedirect)){
	$bFileExist = true;
}else{	
	$arrTmpLinksRedirect = array();
	$arrLinks = $oReg->get('site')->getRedirectLinks();
	//fill the array
	if($arrLinks){	
		foreach($arrLinks as $k=>$v){
			$arrTmpLinksRedirect[$v['path']] = $v['ref_id'];	
			}
		}
	}
//cache	
if(!$bFileExist){
	$oReg->get('cache')->cacheWrite($linksFileRedirect, $arrTmpLinksRedirect);
	}

unset($arrLinks);


//write and clean----------------------------------------

//set it to global
$oGlob->set('links', $arrTmpLinksById);
$oGlob->set('links-by-key', $arrTmpLinksByKey);
$oGlob->set('links-name-by-id', $arrTmpLinksNameById);
$oGlob->set('links-redirect', $arrTmpLinksRedirect);

//clean
unset($arrTmpLinksByKey, $arrTmpLinksById, $arrTmpLinksNameById, $arrTmpLinksRedirect, $linksFileById, $linksFileByKey, $linksFileNameById, $linksFileRedirect, $bFileExist);


//END