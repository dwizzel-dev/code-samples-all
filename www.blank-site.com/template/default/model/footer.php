<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	footer model

*/

//--------------------------------------------------------

//copyright
$arrOutput['footer']['copyright'] = _T('copyright');

//--------------------------------------------------------

//init obj menu
require_once(DIR_CLASS.'menu.php');
$oMenu = new Menu($oReg);

//get the tree
$arrOutput['footer']['footer-menu'] = $oMenu->getMenuTree('footer-menu-1', $oGlob->get('lang'));
unset($oMenu);

//logo
$arrOutput['footer']['logo'] = array(
	'img' => PATH_IMAGE.'logo-physiotec-footer.png',
	'alt' => _T('company slogan from'),
	'href' => $oGlob->getArray('links-by-key', 'home-'.$oGlob->get('lang_prefix')),
	);
	
//icone pour le scrooll up
$arrOutput['footer']['img-scroll-up'] = PATH_IMAGE.'arrow-up-glow.png';	
$arrOutput['footer']['img-menu-up'] = PATH_IMAGE.'menu-mobile-up.png';

//le widget du text
require_once(DIR_WIDGET.'footer/controller/footer.php');
$oFooterInfos = new widgetFooterController($oReg, $oGlob, 'footer-infos');

//check si il existe sinon on passe
if($oFooterInfos->getWidget()){
	$arrOutput['footer']['infos'] = array(
		'html' => $oFooterInfos->getHtml()
		);
	}

unset($oFooterInfos);

//le widget socialmedia
require_once(DIR_WIDGET.'socialmedia/controller/socialmedia.php');
$oSocialMedia = new widgetSocialMediaController($oReg, 'socialmedia-footer', $oGlob->get('lang'));

//check si il existe sinon on passe
if($oSocialMedia->getWidget()){
	$arrOutput['footer']['socialmedia'] = array(
		'html' => $oSocialMedia->getHtml()
		);
	}

unset($oSocialMedia);



//END

