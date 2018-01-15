<?php
/**
@auth:	Dwizzel
@date:	06-07-2012
@info:	header model

*/

//init obj menu
require_once(DIR_CLASS.'menu.php');
$oMenu = new Menu($oReg);

//get the tree

//top menu mobile
$arrOutput['header']['mobile'] = array(
	'img-menu' => PATH_IMAGE.'menu-mobile.png',
	'img-logo' => PATH_IMAGE.'logo-mobile.png',
	'top-menu' => $oMenu->getMenuTree('header-top-menu-mobile-1', $oGlob->get('lang'))
	);


//top menu des langues
$arrOutput['header']['menu-contact'] = $oMenu->getMenuTree('header-menu-contact', $oGlob->get('lang'));

//top menu des langues
$arrOutput['header']['menu-lang'] = $oMenu->getMenuTree('header-menu-lang', $oGlob->get('lang'));

//menu du login
$arrOutput['header']['menu-login'] = $oMenu->getMenuTree('header-menu-login', $oGlob->get('lang'));

//top menu des choix
$arrOutput['header']['top-menu-1'] = $oMenu->getMenuTree('header-top-menu-1', $oGlob->get('lang'));

//top menu slogan
$arrOutput['header']['slogan'] = _T('company slogan text');
if(isset($gSlogan[$oGlob->get('lang')])){
	$arrTmpSlogan = $gSlogan[$oGlob->get('lang')];
	//un random 
	$arrOutput['header']['slogan'] = $arrTmpSlogan[rand(0,(count($arrTmpSlogan) - 1))];
	}


//top logo
$arrOutput['header']['img-logo'] = PATH_IMAGE.'logo-physiotec-header.png';
$arrOutput['header']['alt-logo'] = SITE_NAME;
$arrOutput['header']['href-logo'] = $oGlob->getArray('links-by-key', 'home-'.$oGlob->get('lang_prefix'));


//clean
unset($oMenu);




//END




