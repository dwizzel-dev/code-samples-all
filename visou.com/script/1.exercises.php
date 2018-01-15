<?php
/**
@auth: Dwizzel
@date: 04-05-2016
@info: script pour selectionner les exercises distinct
@version: 1.0 BUILD 001

@execution:

	B.1. (exercises.php) on va inserer tout les exercices dans la table "exercises" avec un en fr_CA et un en en_US dans la langue correspondante
	


*/

//EXIT('COMMENT LINE '.__LINE__.' TO RUN'."\n"); //remove to insert data

//-------------------------------------------------------------------------------------------------------------

//CONTENTTYPE
header('Content-Type: text/plain; charset=utf-8', true);

// ERROR REPORTING
error_reporting(E_ALL);

// BASE DEFINE
require_once('define.php');

// BASE REQUIRED
require_once(DIR_INC.'required.php');

// LIMITER
$gContinue = true;
$gLimitMin = 0;
$gLimitMax = 0; //0 = no limit
$gLimitStart = $gLimitMin;
$gChunkLimit = 1000;

//STEP B.1 -------------------------------------------------------------------------------------------------

// select
$gBaseQuery = 'SELECT exercise_id, short_title, title, description, pictures, video_code, video_id, locale, ranking FROM basic_infos ORDER BY exercise_id ASC LIMIT ';

while($gContinue){
	//select from DB
	$query = $gBaseQuery.$gLimitStart.','.$gChunkLimit.';';	
	//show		
	echo $query.EOL.EOL;	
	//result set
	$rs = $oReg->get('db-site')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k=>$v){
			$iRank = intVal($v['ranking']);
			//dejsonize
			$arrPictures = $oReg->get('json')->decode($v['pictures']);
			//check json error
			if(!is_numeric($arrPictures) && is_array($arrPictures)){
				//les images 1 ou 2
				$strThumb0 = '';
				$strThumb1 = '';
				$strPict0 = '';
				$strPict1 = '';
				//minor check
				if(isset($arrPictures[0]['thumb'])){
					$strThumb0 = $arrPictures[0]['thumb'];
					}
				if(isset($arrPictures[1]['thumb'])){
					$strThumb1 = $arrPictures[1]['thumb'];
					}
				if(isset($arrPictures[0]['pic'])){
					$strPict0 = $arrPictures[0]['pic'];
					}
				if(isset($arrPictures[1]['pic'])){
					$strPict1 = $arrPictures[1]['pic'];
					}
				//id ref
				$iRefId = intVal($v['exercise_id']);
				//locale
				if(isset($arrLangByValue[$v['locale']])){
					$iLocale = intVal($arrLangByValue[$v['locale']]);
					//short title et on remplace les saut de ligne [EOL] par un vide
					$strShortTitle = str_replace('[EOL]', ' ', $v['short_title']);
					//title
					$strTitle = str_replace('[EOL]', ' ', $v['title']);
					//description et on remplace par un vrai saut d ligne \n
					$strDescription = str_replace("[EOL]", "\n", $v['description']);
					//video id
					$iVideoId = intVal($v['video_id']);
					//video code
					$strVideoCode = $v['video_code'];
					//minor check
					if($iRefId && $iLocale && $strShortTitle != '' && $strTitle != '' && $strDescription != '' && $iVideoId && $strVideoCode != ''){
						//do the insert in db-site
						$insert = 'INSERT INTO exercises (locale, short_title, title, description, thumb_0, thumb_1, pict_0, pict_1, video_id, video_code, ref_id, ranking) VALUES("'.$iLocale.'","'.$oReg->get('db-site')->escape($strShortTitle).'","'.$oReg->get('db-site')->escape($strTitle).'","'.$oReg->get('db-site')->escape($strDescription).'","'.$oReg->get('db-site')->escape($strThumb0).'","'.$oReg->get('db-site')->escape($strThumb1).'","'.$oReg->get('db-site')->escape($strPict0).'","'.$oReg->get('db-site')->escape($strPict1).'","'.$iVideoId.'","'.$oReg->get('db-site')->escape($strVideoCode).'","'.$iRefId.'", "'.$iRank.'");';
						//show
						//echo $insert.EOL.EOL;
						//insert
						$oReg->get('db-site')->query($insert);
					}else{
						echo 'REJECTED[1]:'.$iRefId.EOL.EOL;
						}
					}	
			}else{
				echo 'REJECTED[0]:'.$iRefId.EOL.EOL;
				}
			}
	}else{
		$gContinue = false;
		}
	//clean
	unset($rs, $k, $v);
	//increment	
	$gLimitStart += $gChunkLimit;
	//pour ne pas tout faire	
	if($gLimitStart > $gLimitMax && $gLimitMax > 0){
		$gContinue = false;
		}
	//show
	echo '['.format2Dec(((memory_get_peak_usage()/1024)/1000)).' Mo] LIMIT-START['.$gLimitStart.']'.EOL.EOL;
	}
//show
echo 'END STEP #B.1'.EOL.EOL;	



//SCRIPT END