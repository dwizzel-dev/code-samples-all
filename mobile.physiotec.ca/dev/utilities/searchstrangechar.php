#!/usr/bin/php
<?php
/** 
@auth:	Dwizzel
@date:	20-04-2016
@note:	IMPORTANT: THIS IS A SCRIPT NOT A PAGE
@info:	a la recherche de char special comme PROB:  found in APP114255

	http://www.fileformat.info/info/unicode/utf8.htm

	00 to 7F hex (0 to 127)		: first and only byte of a sequence.
	80 to BF hex (128 to 191)	: continuing byte in a multi-byte sequence.
	C2 to DF hex (194 to 223)	: first byte of a two-byte sequence.
	E0 to EF hex (224 to 239)	: first byte of a three-byte sequence.
	F0 to FF hex (240 to 255)	: first byte of a four-byte sequence.

	UTF-8 Sequence
	01
	7F
	C2 80
	DF BF
	E0 A0 80
	EF BF BF
	F0 90 80 80
	F4 8F BF BF

----------------------
 chr(194).chr(146)=
 chr(194).chr(150)=
 chr(194).chr(149)=
 chr(194).chr(160)= 
 chr(194).chr(156)=
 chr(194).chr(176)=°
 chr(194).chr(186)=º
 chr(194).chr(147)=
 chr(194).chr(148)=
 chr(194).chr(151)=
 chr(194).chr(174)=®
 chr(194).chr(145)=
 chr(194).chr(188)=¼
 chr(194).chr(173)=­
 chr(194).chr(171)=«
chr(194).chr(184)=¸
chr(194).chr(170)=ª
chr(194).chr(168)=¨
chr(194).chr(180)=´
chr(194).chr(183)=·
 chr(194).chr(177)=±
 chr(194).chr(163)=£
 chr(194).chr(190)=¾
 chr(194).chr(187)=»
 chr(194).chr(189)=½
----------------------





*/
//-----------------------------------------------------------------------------------------------

header('Content-Type: text/plain; charset=utf-8', true);

exit();

// ERROR REPORTING
error_reporting(E_ALL);
// BASE DEFINE
require_once('/var/www/mobile....@physiotec.ca/dev/define.php');
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
//les pre charcode et le nombre de char apres avoir trouve
$arrCharCode = array(
	//0 => 1,	   
	194 => 2,
	//224 => 3,	
	//240 => 4,	
	);
//les queries
$arrQueries = array(
	'SELECT data AS "words" FROM exercise ORDER BY idExercise ASC LIMIT ',
	'SELECT keywords AS "words" FROM exercise ORDER BY idExercise ASC LIMIT ',	
	'SELECT data AS "words" FROM exercise_user ORDER BY idExercise ASC LIMIT ',
	'SELECT keywords AS "words" FROM exercise_user ORDER BY idExercise ASC LIMIT ',
	'SELECT title AS "words" FROM protocol ORDER BY idProtocol ASC LIMIT ',
	'SELECT note AS "words" FROM protocol ORDER BY idProtocol ASC LIMIT ',
	'SELECT data AS "words" FROM protocol ORDER BY idProtocol ASC LIMIT ',
	'SELECT title AS "words" FROM program ORDER BY idProgram ASC LIMIT ',
	'SELECT data AS "words" FROM program ORDER BY idProgram ASC LIMIT ',
	);
//ceux que l'on garde
$arrFound = array();
//les distinct que l'on garde
$arrKeep = array();	
//vars
$iNextQuery = 0;
//loop a coup de chunk
while($iNextQuery < count($arrQueries)){
	$bContinue = true;
	$iLimitMin = 0;
	$iLimitMax = 0; //0 = no limit
	$iLimitStart = $iLimitMin;
	$iChunkLimit = 5000;	
	while($bContinue){
		$query = $arrQueries[$iNextQuery].$iLimitStart.','.$iChunkLimit.';';
		//resultset
		$rs = $oReg->get('db')->query($query);
		//if some result
		if($rs && $rs->num_rows){
			//loop throught data
			foreach($rs->rows AS $k=>$v){
				$v['words'] = str_replace("\n", '[EOL]', trim($v['words']));
				foreach($arrCharCode as $k2=>$v2){
					$iPos = strpos($v['words'], chr($k2));
					if($iPos !== false){
						$found = substr($v['words'], $iPos, $v2);
						if(!in_array($found, $arrKeep)){
							array_push($arrKeep, $found);
							}
						}
					}
				unset($k2, $v2);		
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
		echo '['.format2Dec(((memory_get_peak_usage()/1024)/1000)).' Mo]: '.$query.EOL;
		//cleanup
		unset($rs, $k, $v);
		usleep(100000);
		}
	$iNextQuery++;
	}
//log to a file
$str = EOL.'';
foreach($arrKeep as $k=>$v){
	$str .= 'chr('.ord(substr($v,0,1)).').chr('.ord(substr($v,1,1)).')='.$v.EOL; 
	}
//log result
$oReg->get('log')->log(
	'specialchars', 
	$str
	);
//cleanup
unset($oReg);



//END SCRIPT
