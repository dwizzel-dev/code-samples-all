<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	script des transferts des keywords
@inst:
		kwtype == 1 // est un keyword 
		kwtype == 2 // est un short_title d'un exercice ou plusieur si ils sont pareils
		kwtype == 3 // est un code exercice
				
*/
//-----------------------------------------------------------------------------------------------

header('Content-Type: text/plain; charset=utf-8', true);

//EXIT('COMMENT LINE '.__LINE__.' TO RUN'."\n"); //remove to insert data

// ERROR REPORTING
error_reporting(E_ALL);
// BASE DEFINE
require_once('/var/www/mobile....@physiotec.ca/dev/keywords.com/scripts/kw-define.php');
//check if it was defined
if(!defined('DIR_CLASS')){
	define('DIR_CLASS', DIR_BASE_CLASS.'SCRIPT/');
	}
if(!defined('DIR_INC')){
	define('DIR_INC', DIR_BASE_INC.'SCRIPT/');
	}
if(!defined('DIR_LOGS')){
	define('DIR_LOGS', DIR_BASE_LOGS.'SCRIPT/');
	}
//helpers function for all sites
require_once(DIR_INC.'helpers.php');
//functions for this specific sites
require_once(DIR_INC.'functions.php');
//change the error handling if it is defined in the function.php or helpers.php file
if(function_exists('phpErrorHandler')){
	set_error_handler('phpErrorHandler');
	}
//required 
require_once(DIR_CLASS.'globals.php');
require_once(DIR_CLASS.'utility.php');
require_once(DIR_CLASS.'registry.php');
require_once(DIR_CLASS.'log.php');
require_once(DIR_CLASS.'database.php');
require_once(DIR_CLASS.'errors.php');
require_once(DIR_CLASS.'json.php');
//register new class too the registry to simplify arguments passing to other classes
$oReg = new Registry();
$oReg->set('glob', new Globals());	
$oReg->set('utils', new Utility($oReg));		
$oReg->set('log', new Log($oReg));
$oReg->set('err', new Errors($oReg));
$oReg->set('json', new Json());
//minor check on main db connection
$oReg->set('db', new Database(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, $oReg));
if(!$oReg->get('db')->getStatus()){
	exit('ERR: NO DATABASE CONNECTION[db]');
	}

//-----------------------------------------------------------------------------------------------

//show
echo 'START SCRIPT'.EOL.EOL;
//VARS
$iSleep = 0;
$bExecute = false;
$bBypassEncoding = true;
$gLogName = 'err-exercise-transfert';
// LIMITER
$gMinIdExexercise = 0; //655
$gContinue = true;
$gLimitMax = 0; //0 = no limit
$gLimitStart = 0;
$gChunkLimit = 1000;
$gArrLocaleEnabled = array(
	'en_US',
	'fr_CA',
	'de_DE',
	'es_MX',
	'fr_FR',
	'nl_NL',
	'pt_PT',
	);
//LE SELECT DE BASE
$gBaseQuery = 'SELECT idExercise AS "id", data AS "data", rank AS "rank", idUser AS "idUser" FROM exercise WHERE ready = "1" AND oldIdExercise <> 0 AND idExercise >= '.intVal($gMinIdExexercise).' ORDER BY idExercise ASC LIMIT ';
//LOOP DES CHUNKS
while($gContinue){
	//select from DB
	$query = $gBaseQuery.$gLimitStart.','.$gChunkLimit.';';	
	//show		
	echo $query.EOL.EOL;	
	//result set
	$rs = $oReg->get('db')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k=>$v){
			//$strRankEx = $v['rank'];
			//pour l'instant on met le rank a 0
			$strRankEx = '666';
			$idEx = intVal($v['id']);
			$idUser = intVal($v['idUser']);
			$idLicence = 0;
			//si le idUser est different de 0, alors on va chercher le idLicence du idUser
			if($idUser !== 0){
				//select from DB
				$query = 'SELECT DISTINCT(idLicence) AS "idLicence" FROM licence_admin WHERE idUser = "'.$idUser.'" LIMIT 0,1;';
				//show		
				//echo $query.EOL.EOL;
				//result set
				$rsUser = $oReg->get('db')->query($query);
				//minor check
				if($rsUser && $rsUser->num_rows){
					$idLicence = intVal($rsUser->row['idLicence']);
					}
				//clean
				unset($rsUser);
				//si encore a 0 alors on cherche ailleurs
				if($idLicence === 0){
					//select from DB
					$query = 'SELECT DISTINCT(idLicence) AS "idLicence" FROM licence_clinic, clinic_user WHERE licence_clinic.idClinic = clinic_user.idClinic AND clinic_user.idUser = "'.$idUser.'" LIMIT 0,1;';	
					//show		
					//echo $query.EOL.EOL;
					//result set
					$rsLicence = $oReg->get('db')->query($query);
					//minor check
					if($rsLicence && $rsLicence->num_rows){
						$idLicence = intVal($rsLicence->row['idLicence']);
						}
					//clean
					unset($rsLicence);
					}
				}
			//ca vaeux x dire que ca ne fonctionne pas alors on ne fait rien
			if($idUser !== 0 && $idLicence !== 0 || $idUser === 0 && $idLicence === 0){
				//on va remplacer les saut de ligne qui fuck le decodage
				$v['data'] = str_replace("\n", '', $v['data']);
				//on decode
				$arrData = $oReg->get('json')->decode($v['data']);
				//minor check sur ce queon a besoin
				if(isset($arrData['locale'])){
					//par lang locale
					foreach($arrData['locale'] AS $k2=>$v2){
						//lang
						$strLangLocale = $k2;
						//minor check
						if(isset($v2['short_title'])){
							$strShortTitle = $v2['short_title'];
							//minor check sur tout
							if($strRankEx != '' && $idEx !== false && $idEx !== 0 && $strShortTitle != '' && in_array($strLangLocale, $gArrLocaleEnabled)){
								//flag savoir si on fait update
								$bUpdateData = true;
								//on bypass le encoding on le trimera avec un script separe ainsi que les doublons
								if($bBypassEncoding){
									$data = $strShortTitle;
								}else{
									//on va cleaner la string de title et la minimizer
									//on detect encodage de la string
									$encoding = mb_detect_encoding($strShortTitle, mb_detect_order(), true);
									//minor check sur l'encodage de base
									if($encoding !== false){
										//on convertit le encodingde *** a utf-8 pour catcher les erreurs 
										$dataConvertEncoding = mb_convert_encoding($strShortTitle, 'UTF-8', $encoding);
										//on change le encoding et check si valide
										$data = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $dataConvertEncoding);
										//si pas valide utf-8
										if($data === false){
											$bUpdateData = false;
											$strErr = 'Error[0] on iconv(\'UTF-8\', \'ISO-8859-1//TRANSLIT\', "'.$dataConvertEncoding.'") idEx=['.$idEx.']';
											trigger_error($strErr , E_USER_NOTICE);
											$oReg->get('log')->log($gLogName.'-0', $strErr);
											sleep($iSleep);
										}else{
											//
											//si c'est du UTF-8 ca se peut qu'il soit doublement encode comme exemple dans @important alors on verifie
											if($encoding == 'UTF-8'){
												//on detect le double encodage de la string
												$doubleEncoding = mb_detect_encoding($data, mb_detect_order(), true);
												if($doubleEncoding == 'UTF-8'){
													$strErr = 'Found Double Encoding Issue:"'.$data.'" idEx=['.$idEx.']';
													trigger_error($strErr , E_USER_NOTICE);
													$oReg->get('log')->log($gLogName.'-1', $strErr);
													sleep($iSleep);
													//on convertit le encodingde *** a utf-8 pour catcher les erreurs 
													$dataConvertEncoding = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
													//on change le encoding et check si valide
													$data = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $dataConvertEncoding);
													//si pas valide utf-8
													if($data === false){
														$bUpdateData = false;
														$strErr = 'Error[1] on iconv(\'UTF-8\', \'ISO-8859-1//TRANSLIT\', "'.$dataConvertEncoding.'") idEx=['.$idEx.']';
														trigger_error($strErr , E_USER_NOTICE);
														$oReg->get('log')->log($gLogName.'-2', $strErr);
														sleep($iSleep);
														}
													}
												}	
											}
									}else{
										//ca passe pas on trig une erreur perso
										$bUpdateData = false;
										$strErr = 'Error on mb_detect_encoding("'.$v2['data'].'", '. mb_detect_order().', true) idEx=['.$idEx.']';
										trigger_error($strErr, E_USER_NOTICE);
										$oReg->get('log')->log($gLogName, $strErr);
										sleep($iSleep);
										}
									}
									
								//ca passe
								if($bUpdateData === true && $data != ''){
									//on bypass le encoding on le trimera avec un script separe ainsi que les doublons
									if($bBypassEncoding){
										$dataUTF8 = stripslashes($data);
									}else{
										//on decode en char normale html 4.01
										$data = html_entity_decode($data, ENT_NOQUOTES | ENT_HTML5 | ENT_HTML401, 'ISO-8859-1');
										//on clean la string de toute les cocheneries
										$data = cleanString($data);
										//on strip
										$data = stripslashes($data);
										//on reconvertit en utf-8
										$dataUTF8 = mb_convert_encoding($data, 'UTF-8', 'ISO-8859-1');
										}
									if($dataUTF8 != ''){
										//on bypass le encoding on le trimera avec un script separe ainsi que les doublons
										if(!$bBypassEncoding){	
											//to lower case
											$dataUTF8 = mb_strtolower($dataUTF8, 'UTF-8');
											}
										//show
										echo '['.$strLangLocale.':'.$idEx.']:"'.$dataUTF8.'"'.EOL.EOL;
										//query insert
										//le kwtype = 2 = un short_title 
										$insert = 'INSERT INTO keyword (idLicence, keyword, locale, autogenerated, kwtype) VALUES("'.$idLicence.'", "'.$oReg->get('db')->escape($dataUTF8).'", "'.$oReg->get('db')->escape($strLangLocale).'", "0", "2");';
										//show
										//echo $insert.EOL.EOL;
										//exec or not
										if($bExecute){	
											//on fait le update
											$rs2 = $oReg->get('db')->query($insert);
											//on va chercher le dernier id car on va l'associer avec les exercice relatif a celui-ci
											if($rs2){
												$newKwId = intVal($rs2->insert_id);
												//minor check
												if($newKwId !== false && $newKwId !== 0){
													//query
													$insertEx = 'INSERT INTO keyword_exercise (idKeyword, idExercise) VALUES("'.$newKwId.'", "'.$idEx.'");';
													//show
													//echo $insertEx.EOL.EOL;
													//on va inserer les keyword_exercices
													$rs3 = $oReg->get('db')->query($insertEx);
													//clean
													unset($rs3);
													//on va inserer le rank maintenant
													//query
													$insertRank = 'INSERT INTO keyword_rank (idKeyword, locale, rank) VALUES("'.$newKwId.'", "'.$oReg->get('db')->escape($strLangLocale).'", "'.$oReg->get('db')->escape($strRankEx).'");';
													//show
													//echo $insertRank.EOL.EOL;
													//on va inserer les keyword_exercices
													$rs3 = $oReg->get('db')->query($insertRank);
													//clean
													unset($rs3);
												}else{
													$strErr = 'Error[0] newKwId['.$newKwId.'] idEx=['.$idEx.']';
													trigger_error($strErr, E_USER_NOTICE);
													$oReg->get('log')->log($gLogName.'-3', $strErr);
													sleep($iSleep);
													}
											}else{
												$strErr = 'Error[1] RS idEx=['.$idEx.']';
												trigger_error($strErr, E_USER_NOTICE);
												$oReg->get('log')->log($gLogName.'-4', $strErr);
												sleep($iSleep);
												}
											}
										//clean
										unset($rs2);
									}else{
										$strErr = 'Error on mb_convert_encoding("'.$data.'", \'UTF-8\', \'ISO-8859-1\') idEx=['.$idEx.']';
										trigger_error($strErr, E_USER_NOTICE);
										$oReg->get('log')->log($gLogName.'-5', $strErr);
										sleep($iSleep);
										}
									}
								}
						}else{
							$strErr = 'Error on v2[short_title] is empty idEx=['.$idEx.']';
							trigger_error($strErr, E_USER_NOTICE);
							$oReg->get('log')->log($gLogName.'-6', $strErr);
							sleep($iSleep);
							}
						}
					//clean
					unset($k2, $v2);
				}else{
					$strErr = 'Error on arrData[locale] "'.$v['data'].'" idEx=['.$idEx.']';
					trigger_error($strErr, E_USER_NOTICE);
					$oReg->get('log')->log($gLogName.'-7', $strErr);
					sleep($iSleep);
					}
			}else{
				$strErr = 'Error on idEx|idLicence|idUser=['.$idEx.'|'.$idLicence.'|'.$idUser.']';
				trigger_error($strErr, E_USER_NOTICE);
				$oReg->get('log')->log($gLogName.'-8', $strErr);
				sleep($iSleep);
				}
			}//end for
		//clean
		unset($k, $v);	
	}else{
		$gContinue = false;
		}
	//clean
	unset($rs);
	//increment	
	$gLimitStart += $gChunkLimit;
	//pour ne pas tout faire	
	if($gLimitStart > $gLimitMax && $gLimitMax > 0){
		$gContinue = false;
		}
	}
//show
echo 'END SCRIPT'.EOL.EOL;






//END
