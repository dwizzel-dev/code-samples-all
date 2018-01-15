<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	script pour faire les rank des keyword exercises
@inst:

	
*/
//-----------------------------------------------------------------------------------------------

header('Content-Type: text/plain; charset=utf-8', true);

EXIT('COMMENT LINE '.__LINE__.' TO RUN'."\n"); //remove to insert data

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
$bExecute = false;
$bShow = true;
$iSleep = 0;
// LIMITER
$gContinue = true;
$gLimitMin = 0;
$gLimitMax = 0; //0 = no limit
$gLimitStart = $gLimitMin;
$gChunkLimit = 10;
//LE SELECT DE BASE
$gBaseQuery = 'SELECT idKeyword AS "id", locale as "locale" FROM keyword WHERE kwtype = "2" ORDER BY idKeyword ASC LIMIT ';
//LOOP DES CHUNKS
while($gContinue){
	//select from DB
	$query = $gBaseQuery.$gLimitStart.','.$gChunkLimit.';';	
	//show		
	if($bShow){	
		//echo $query.EOL.EOL;	
		}
	//result set
	$rs = $oReg->get('db')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k=>$v){
			$idKw = intVal($v['id']);
			$strLocale = $v['locale'];
			//ca passe
			if($strLocale != '' && $idKw !== 0){
				//on check si existe
				$queryExercise = 'SELECT idExercise AS "id" FROM keyword_exercise WHERE idKeyword = "'.$idKw.'";';
				//show
				if($bShow){
					echo $queryExercise.EOL.EOL;
					}	
				//result set
				$rsExercise = $oReg->get('db')->query($queryExercise);
				//clean
				unset($queryExercise);
				//check
				if($rsExercise && $rsExercise->num_rows){
					$arrExerciseIds = array();
					//check si a des results
					foreach($rsExercise->row as $k2=>$v2){
						array_push($arrExerciseIds, $v2['id']);
						}
					//clean
					unset($k2, $v2);	
					//on va aller chercher le plus gros rank de la gang
					$strExerciseIds	= implode(',', array_values($arrExerciseIds));
					//rank 
					$queryRank = 'SELECT MAX(rank) AS "rank" FROM exercise WHERE idExercise IN ('.$strExerciseIds.');';
					//show
					if($bShow){
						echo $queryRank.EOL.EOL;
						}	
					//result set
					$rsRank = $oReg->get('db')->query($queryRank);
					//check
					$iMaxRank = 0;
					if($rsRank && $rsRank->num_rows){
						$iMaxRank = intVal($rsRank->row['rank']);
						}
					//si different de zero alors on rajoute a keyword_rank
					if($iMaxRank > 0){
						/*

	
						TODO, mais on ne sait pas encore
						

						*/
						}
					//clean
					unset($rsRank);	
					//clean
					unset($queryExercise);
					}
				//clean
				unset($rsExercise);
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
	}
//show
echo 'END SCRIPT'.EOL.EOL;



//END