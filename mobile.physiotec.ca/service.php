<?php
/**
@auth: Dwizzel
@date: 22-04-2016
@info: fichier de base pour le call des services ajax
@version: V.3.0 B.110

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
		if(!in_array($_SERVER['REMOTE_ADDR'], explode(',',REMOTE_ADDR_ACCEPTED))){
			exit('SORRY! OFFLINE FOR MAINTENANCE');
			}
	}else{
		exit('SORRY! OFFLINE FOR MAINTENANCE');
		}
	}	

// BASE REQUIRED
require_once(DIR_INC.'required.php');

// IP OF THE CALL
if(isset($_SERVER['REMOTE_ADDR'])){
	$oReg->get('req')->set('ip', $_SERVER['REMOTE_ADDR']);
}else{
	$oReg->get('req')->set('ip', '0.0.0.0');	
	}

// LOG REQUEST
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

// INSTANCE OF SERVICE
$oService = new Service($oReg);	

// MINOR CHECK ON THE ARGS WE NEED TO DO THE CALL
if($oService->check()){
	//session and args are valid	
	//do check the translation
	$gLocaleLang = DEFAULT_LOCALE_LANG;	
	//on check si on une locale en param sinon on prend celle par defaut
	if($oReg->get('req')->get('lang') !== false){
		//minor check
		if(strlen($oReg->get('req')->get('lang')) == 5){
			$gLocaleLang = $oReg->get('req')->get('lang');
			}
		}
	//pour le gettext
	setlocale(LC_MESSAGES, $gLocaleLang.'.utf8');
	bindtextdomain('mobile-physiotec-ca', TRANSLATION_BASE_DIR);	
	textdomain('mobile-physiotec-ca');	
	bind_textdomain_codeset('mobile-physiotec-ca', 'UTF-8');
	//clean
	unset($gLocaleLang);	
	//process le section->method
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
	//session is invalid so we do a kick out command to the javascript.jComm 
	//that catched every call of the service 		
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
	
// LOG RESPONSE
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

// OUTPUT HEADER FOR AJAX
$oReg->get('resp')->addHeader('Content-Type: text/plain; charset=utf-8');
$oReg->get('resp')->addHeader('Access-Control-Allow-Origin: *');
$oReg->get('resp')->addHeader('Access-Control-Allow-Headers: *');

// LE OUTPUT STRING
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

// OUTPUT TO CLIENT
echo $gOutput;






//END
