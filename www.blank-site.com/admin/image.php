<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	image generator
@note:	doit etre complexifie avec les params passes

*/

// ERROR REPORTING
error_reporting(E_ALL);

// BASE DEFINE
require_once('define.php');

// ERROR REPORTING
error_reporting(ERROR_LEVEL);

// BASE REQUIRED
require_once(DIR_INC.'required.php');

//IMAGE CLASS
require_once(DIR_CLASS.'image.php');
$oImage = new Image($oReg);
	
//IMAGE INFOS
$imType = $oReg->get('req')->get('type');
$imData = $oReg->get('req')->get('data');

if($imType == 'jpeg' || $imType == 'jpg' ){
	if($imData){
		echo $oImage->createCaptcha();
		}
	}



//END




