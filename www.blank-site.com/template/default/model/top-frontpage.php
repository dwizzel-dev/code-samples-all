<?php
/**
@auth:	Dwizzel
@date:	06-07-2012
@info:	top frontpage model

*/

//--------------------------------------------------------

//on va chercher le links id de la page ou on est


//le widget carousel
require_once(DIR_WIDGET.'frontpage/controller/frontpage.php');

//the top one
$oFrontpage = new widgetFrontpageController($oReg, $oGlob, 'frontpage-top');

//check si il existe sinon on passe
if($oFrontpage->getWidget()){
	$arrOutput['top-frontpage'] = array();
	$arrOutput['top-frontpage']['html'] = $oFrontpage->getHtml();
	}

//clean
unset($oFrontpage);
	
//the bottom one	
$oFrontpage = new widgetFrontpageController($oReg, $oGlob, 'frontpage-bottom');
//check si il existe sinon on passe
if($oFrontpage->getWidget()){
	$arrOutput['bottom-frontpage'] = array();
	$arrOutput['bottom-frontpage']['html'] = $oFrontpage->getHtml();
	}	

//clean
unset($oFrontpage);

//END