<?php
/**
@auth:	Dwizzel
@date:	06-07-2012
@info:	top content model

*/

//--------------------------------------------------------

//on va chercher le links id de la page ou on est


//le widget carousel
require_once(DIR_WIDGET.'content/controller/content.php');

//the bottom one	
$oContent = new widgetContentController($oReg, $oGlob, 'content-bottom');
//check si il existe sinon on passe
if($oContent->getWidget()){
	$arrOutput['bottom-content'] = array();
	$arrOutput['bottom-content']['html'] = $oContent->getHtml();
	}	

//clean
unset($oContent);

//END