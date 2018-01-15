#!/usr/bin/php
<?php
/** 
@auth:	Dwizzel
@date:	20-04-2016
@note:	IMPORTANT: THIS IS A SCRIPT NOT A PAGE
@info:	a la recherche des json errors sur les fields lors du encode

	
*/

//------------------------------------------------------------------------------------------

exit();

header('Content-Type: text/plain; charset=utf-8', true);

// ERROR REPORTING
error_reporting(E_ALL);
// BASE DEFINE
require_once('/var/www/mobile....@physiotec.ca/define.php');
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
//les queries
$arrQueries = array(
	//'SELECT "exercice" AS "table", "data" AS "field", idExercise AS "id", data AS "words" FROM exercise ORDER BY idExercise ASC LIMIT ', 
	//'SELECT "exercice_user" AS "table", "data" AS "field", idExercise AS "id", data AS "words" FROM exercise_user ORDER BY idExercise ASC LIMIT ',
	//'SELECT "protocol" AS "table", "title" AS "field", idProtocol AS "id", title AS "words" FROM protocol ORDER BY idProtocol ASC LIMIT ',
	//'SELECT "protocol" AS "table", "note" AS "field", idProtocol AS "id", note AS "words" FROM protocol ORDER BY idProtocol ASC LIMIT ',
	//'SELECT "protocol" AS "table", "data" AS "field",  idProtocol AS "id", data AS "words" FROM protocol ORDER BY idProtocol ASC LIMIT ',
	//'SELECT "program" AS "table", "title" AS "field", idProgram AS "id", title AS "words" FROM program ORDER BY idProgram ASC LIMIT ',
	'SELECT "program" AS "table", "data" AS "field", idProgram AS "id", data AS "words" FROM program ORDER BY idProgram ASC LIMIT ',
	);
//filename le nom du fichier qui garde les logs
$logName = 'jsoncrash_'.'program-data';
//les distinct que l'on garde
$arrCrash = array();	
//vars
$iNextQuery = 0;
//loop a coup de chunk
while($iNextQuery < count($arrQueries)){
	$bContinue = true;
	$iLimitMin = 0;
	$iLimitMax = 0; //0 = no limit
	$iLimitStart = $iLimitMin;
	$iChunkLimit = 20000;	
	while($bContinue){
		$query = $arrQueries[$iNextQuery].$iLimitStart.','.$iChunkLimit.';';
		//show
		echo $query.EOL;	
		//resultset
		$rs = $oReg->get('db')->query($query);
		//if some result
		if($rs && $rs->num_rows){
			//loop throught data
			foreach($rs->rows AS $k=>$v){
				//strip return EOL
				$v['words'] = cleanString($v['words']);
				$v['words'] = str_replace("\n", '[EOL]', $v['words']);
				$v['words'] = str_replace("\\", "&bsol;", $v['words']);
				//dejsonize
				$arr = json_decode($v['words'], true);
				if(json_last_error() != JSON_ERROR_NONE){
					array_push($arrCrash, array(
						'table'	=> $v['table'],
						'field' => $v['field'],
						'id' => $v['id'],
						'words' => $v['words'],
						));
					echo EOL.'JSON_ERROR:'.json_last_error().EOL.EOL;
					}
				}
		}else{
			//on a plus de resultat
			$bContinue = false;
			}
		//increment	
		$iLimitStart += $iChunkLimit;
		//pour ne pas tout faire	
		if($iLimitStart > $iLimitMax && $iLimitMax > 0){
			$bContinue = false;
			}
		//show
		echo '['.format2Dec(((memory_get_peak_usage()/1024)/1000)).' Mo]'.EOL;
		//cleanup
		unset($rs, $k, $v, $arr);
		usleep(100000);
		}
	$iNextQuery++;
	}

$str = EOL.'';
foreach($arrCrash as $k=>$v){
	$str .= '==={'.EOL;
	foreach($v as $k3=>$v3){
		$str .= '"'.$k3.'":'.$v3.EOL;
		}
	$str .= '}==='.EOL.EOL;	
	}

//log result
$oReg->get('log')->log(
	$logName,
	$str
	);

//cleanup
unset($oReg);



//END SCRIPT
