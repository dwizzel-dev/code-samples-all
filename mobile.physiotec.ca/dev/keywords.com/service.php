<?php
/**
@auth: Dwizzel
@date: 15-07-2016
@info: fichier de base pour le call des services ajax
@version: V.1.0 B.001

*/
//-------------------------------------------------------------------------------------------------------------

// ERROR REPORTING
error_reporting(0);

// BASE DEFINE
require_once('define.php');

// ERROR REPORTING
if(defined('ERROR_REPORT_LEVEL')){
	error_reporting(ERROR_REPORT_LEVEL);
	}

// CHECK ACCESS
if(SITE_IS_DOWN){
	if(isset($_SERVER['REMOTE_ADDR'])){
		if(!in_array($_SERVER['REMOTE_ADDR'],explode(',',REMOTE_ADDR_ACCEPTED))){
			exit('SORRY! OFFLINE FOR MAINTENANCE');
			}
	}else{
		exit('SORRY! OFFLINE FOR MAINTENANCE');
		}
	}	

// ON VA VOIR SI DIFFERENT BRANDING
$gBrand = DEFAULT_BRAND;
//on get la langue dans le url
if(isset($_GET['brand']) && strlen($_GET['brand']) == 3){
	$gBrand = $_GET['brand'].'';
	}

// ON VA VOIR SI DIFFERENT VERSIONING
$gVersioning = DEFAULT_VERSIONING;
if(isset($_GET['versioning']) && strlen($_GET['versioning']) == 3){
	$gVersioning = $_GET['versioning'].'';	
	}

// ON DEFINI LES PATH DE BASE DES CLASSES, LOGS et INC
// AVEC LE BRANDING ET VERSIONING QUI SONT UTILISE PARTOUT AILLEURS
define('DIR_CLASS', DIR_BASE_CLASS.$gBrand.'/'.$gVersioning.'/');
define('DIR_INC', DIR_BASE_INC.$gBrand.'/'.$gVersioning.'/');
define('DIR_LOGS', DIR_BASE_LOGS.$gBrand.'/'.$gVersioning.'/');

// ON SUPPRIME LES VARS PLUS UTILISEES
unset($gBrand, $gVersioning);

// BASE REQUIRED
require_once(DIR_INC.'required.php');

// IP OF THE CALL
if(isset($_SERVER['REMOTE_ADDR'])){
	$oReg->get('req')->set('ip', $_SERVER['REMOTE_ADDR']);
}else{
	$oReg->get('req')->set('ip', '0.0.0.0');	
	}

//LOG REQUEST
if(ENABLE_LOG){
	$iUserId = intVal($oReg->get('req')->get('uid'));
	$strFileName = 'service';
	if($iUserId){
		$strFileName = '_'.$strFileName.'_'.$iUserId;
		}
	$oReg->get('log')->log(
		$strFileName, 
		$oReg->get('req')->showRequestAllText()
		);
	}

// BASE CLASSES
require_once(DIR_CLASS.'service.php');

//INSTANCE OF SERVICE
$oService = new Service($oReg);	

//MINOR CHECK ON THE ARGS WE NEED TO DO THE CALL
if($oService->check()){
	//session and args are valid	
	$rtn = $oService->process();
	if(!isTrue($rtn)){
		$oReg->get('resp')->puts(
			buildAjaxMessage(
				$oReg, 
				'', 
				$oService->getError()
				)
			);
	}else{
		$oReg->get('resp')->puts(
			buildAjaxMessage(
				$oReg, 
				$rtn
				)
			);	
		}
}else{
	//session is invalid so we do a kick out command to the javascript.jComm that catched every call of the service 		
	$oReg->get('resp')->puts(
		buildAjaxMessage(
			$oReg, 
			array(
				'command' => 'logout',
				'message' => $oService->getError(),
				), 
			$oService->getError()
			)
		);
	}
	
//LOG RESPONSE
if(ENABLE_LOG){
	$iUserId = intVal($oReg->get('req')->get('uid'));
	$strFileName = 'response';
	if($iUserId){
		$strFileName = '_'.$strFileName.'_'.$iUserId;
		}
	$oReg->get('log')->log(
		$strFileName, 
		$oReg->get('resp')->outputLog()
		);
	}

//OUTPUT HEADER FOR AJAX
$oReg->get('resp')->addHeader('Content-Type: text/plain; charset=utf-8');

//LE OUTPUT STRING
$gOutput = $oReg->get('resp')->output();	 
if(is_numeric($gOutput)){
	//un probleme dencodage avec json on renvoie un message erreur
	//car devrait etre une string ou object mais pas seulement un numeric
	$oReg->get('resp')->clear();
	$oReg->get('resp')->puts(
		buildAjaxMessage(
			$oReg, 
			array(
				'message' => $oReg->get('err')->get($oReg->get('err')->getJsonError($gOutput))
				) 
			)
		);
	$gOutput = $oReg->get('resp')->output();	
	}

//OUTPUT TO CLIENT
echo $gOutput;



//END