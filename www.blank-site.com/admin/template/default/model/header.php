<?php
/**
@auth:	Dwizzel
@date:	06-07-2012
@info:	header model

*/

//init obj menu
require_once(DIR_CLASS.'menu.php');
$oMenu = new Menu($oReg);

//get the tree selon logue ou pas
if(ENABLE_LOGIN){
	if(!$oReg->get('login')->isLogued()){
		$arrOutput['header']['menu'] = array();
	}else{
		$arrOutput['header']['menu'] = $oMenu->getMenuTree($oGlob->get('lang'));
		}
}else{
	$arrOutput['header']['menu'] = $oMenu->getMenuTree($oGlob->get('lang'));
	}

	
//get the lang choices
if(SIMPLIFIED_ADMIN_SITE_PATH){
	$arrOutput['header']['languages'] = array(
		array(
			'name' => _T('francais'),
			'description' => _T('changer interface pour le francais'),
			'link' => '/fr'.PATH_WEB,
			),
		array(
			'name' => _T('english'),
			'description' => _T('switch to english interface'),
			'link' => '/en'.PATH_WEB,
			),
		);
}else{
	$arrOutput['header']['languages'] = array(
		array(
			'name' => _T('francais'),
			'description' => _T('changer interface pour le francais'),
			'link' => PATH_WEB.'index.php?&lang=fr_CA',
			),
		array(
			'name' => _T('english'),
			'description' => _T('switch to english interface'),
			'link' => PATH_WEB.'index.php?&lang=en_US',
			),
		);
	}
	
//top logo
$arrOutput['header']['logo'] = array();
$arrOutput['header']['logo']['image'] = PATH_IMAGE.'logo.png';
$arrOutput['header']['logo']['alt'] = SITE_NAME;
if(SIMPLIFIED_ADMIN_SITE_PATH){
	$arrOutput['header']['logo']['link'] = '/'.$oGlob->get('lang_prefix').PATH_WEB;
}else{
	$arrOutput['header']['logo']['link'] = PATH_WEB.'index.php?&lang='.$oGlob->get('lang').'&path='.CONTROLLER_DEFAULT_HOME.'/';
	}

//clean
unset($oMenu);




//END




