#!/usr/bin/php
<?php
/** 
@auth:	Dwizzel
@date:	20-04-2016
@note:	IMPORTANT: THIS IS A SCRIPT NOT A PAGE
@info:	a la recherche de &#666;

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



*/
//-----------------------------------------------------------------------------------------------
function utfCharToNumber($char){
	$i = 0;
	$number = '';
	while(isset($char{$i})) {
		$number.= 'chr('.ord($char{$i}).').';
		++$i;
		}
	if($number != ''){
		$number = substr($number, 0, strlen($number)-1);
		}
	return $number;
	}


//------------------------------------------------------------------------------------------
// Returns true if $string is valid UTF-8 and false otherwise.
function is_utf8($string){
	// From http://w3.org/International/questions/qa-forms-utf-8.html
	return preg_match('%^(?:[\x09\x0A\x0D\x20-\x7E]	| [\xC2-\xDF][\x80-\xBF] | \xE0[\xA0-\xBF][\x80-\xBF] | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} | \xED[\x80-\x9F][\x80-\xBF] | \xF0[\x90-\xBF][\x80-\xBF]{2} | [\xF1-\xF3][\x80-\xBF]{3} 
	| \xF4[\x80-\x8F][\x80-\xBF]{2} )*$%xs', $string);

	}

//------------------------------------------------------------------------------------------
function charset_decode_utf_8($string){ 
	/* Only do the slow convert if there are 8-bit characters */ 
	/* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */ 
	if(!preg_match("[\200-\237]", $string) && !preg_match("[\241-\377]", $string)){ 
		return $string; 
		}
	// decode three byte unicode characters 
	$string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e", "'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'", $string); 
	// decode two byte unicode characters 
	$string = preg_replace("/([\300-\337])([\200-\277])/e", "'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'", $string); 
	//
	return $string; 
	} 

//------------------------------------------------------------------------------------------

exit();

header('Content-Type: text/plain; charset=utf-8', true);


// ERROR REPORTING
error_reporting(E_ALL);
// BASE DEFINE
require_once('/var/www/mobile....@physiotec.ca/dev/define.php');
//require_once('/var/www/mobile....@physiotec.ca/define.php');
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
//les char numric c,est a dire: &#145; | &eacute; | &#x91; | 0x0091 | u0091
//$strRegex = "(&#[\d]{1,4};)|(&[a-zA-Z]{1,8};)|(&#x[\d]{1,4};)|(\u[\da-fA-F]{4})|(0x[\d]{2,4})";
//$strRegex = "(&#[\d]{1,4};)|(&[a-zA-Z]{1,8};)|(&#x[\d]{1,4};)|(0x[\d]{2,4})|(\p{M})|(\p{Zl})|(\p{Zp})|(\p{S})|(\p{C})";
//$strRegex = "(&#[\d]{1,4};)|(&#x[\d]{1,4};)|(0x[\d]{2,4})|(\p{M})|(\p{Zl})|(\p{Zp})|(\p{S})|(\p{C})";
//$strRegex = "(&#[\d]{1,4};)|(&#x[\d]{1,4};)|(\p{M})|(\p{Zl})|(\p{Zp})|(\p{S})|(\p{C})";
$strRegex = "(&#[\d]{1,4};)|(&#x[\d]{1,4};)";
//les queries
$arrQueries = array(
	'SELECT "exercice" AS "table", "data" AS "field", idExercise AS "id", data AS "words" FROM exercise ORDER BY idExercise ASC LIMIT ', 
	'SELECT "exercice" AS "table", "keywords" AS "field", idExercise AS "id", keywords AS "words" FROM exercise ORDER BY idExercise ASC LIMIT ',	
	'SELECT "exercice_user" AS "table", "data" AS "field", idExercise AS "id", data AS "words" FROM exercise_user ORDER BY idExercise ASC LIMIT ',
	'SELECT "exercice_user" AS "table", "keywords" AS "field", idExercise AS "id", keywords AS "words" FROM exercise_user ORDER BY idExercise ASC LIMIT ',
	'SELECT "protocol" AS "table", "title" AS "field", idProtocol AS "id", title AS "words" FROM protocol ORDER BY idProtocol ASC LIMIT ',
	'SELECT "protocol" AS "table", "note" AS "field", idProtocol AS "id", note AS "words" FROM protocol ORDER BY idProtocol ASC LIMIT ',
	'SELECT "protocol" AS "table", "data" AS "field",  idProtocol AS "id", data AS "words" FROM protocol ORDER BY idProtocol ASC LIMIT ',
	'SELECT "program" AS "table", "title" AS "field", idProgram AS "id", title AS "words" FROM program ORDER BY idProgram ASC LIMIT ',
	'SELECT "program" AS "table", "data" AS "field", idProgram AS "id", data AS "words" FROM program ORDER BY idProgram ASC LIMIT ',
	);
//les distinct que l'on garde
$arrKeep = array();	
$arrCrash = array();	
//vars
$iNextQuery = 0;
//loop a coup de chunk
while($iNextQuery < count($arrQueries)){
	$bContinue = true;
	$iLimitMin = 0;
	$iLimitMax = 0; //0 = no limit
	$iLimitStart = $iLimitMin;
	$iChunkLimit = 10000;	
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
				$arrMatches = array();
				//strip return EOL
				$data = '';	
				$v['words'] = str_replace("\n", '[EOL]', $v['words']);
				$data = iconv("UTF-8", "ISO-8859-1//IGNORE", $v['words']);
				if($data === false){
					array_push($arrCrash, array(
						'table'	=> $v['table'],
						'field' => $v['field'],
						'id' => $v['id'],
						'words' => $v['words'],
						));
					$data = $v['words'];
					}
				//check pour un match du pattern
				preg_match_all("/".$strRegex."/", $data, $arrMatches);
				//si resultat
				if(is_array($arrMatches)){
					//si a trouve quelque chose
					if(isset($arrMatches[0]) && count($arrMatches[0])){
						//on loop sur ceux qu'il a trouve
						foreach($arrMatches[0] as $k2=>$v2){
							//on garde juste un chars mais on check ou il est dans la DB pour le retrouver et faire des tests
							if(!isset($arrKeep[$v2])){
								$arrKeep[$v2] = array();
								}
							//on pousse le data avec les infos neccessaire
							$arrKeep[$v2] = array(
								'table'	=> $v['table'],
								'field' => $v['field'],
								'id' => $v['id'],
								'char' => $v2,
								'code' => utfCharToNumber($v2),	
								);
							}
						}	
					}
				unset($k2, $v2, $arrMatches);		
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
		unset($rs, $k, $v);
		usleep(100000);
		}
	$iNextQuery++;
	}

//log to a file
$str = EOL.'';
foreach($arrKeep as $k=>$v){
	//$str .= $v.'='.htmlentities(html_entity_decode($v, ENT_QUOTES),ENT_QUOTES).EOL;
	$str .= '==={'.EOL;
	foreach($v as $k3=>$v3){
		if($k3 == 'char'){
			$str .= '"'.$k3.'":['.$v3.']'.EOL;
		}else{
			$str .= '"'.$k3.'":'.$v3.EOL;
			}
		}
	$str .= '}==='.EOL.EOL;	
	}

//log result
$oReg->get('log')->log(
	'numericchars',
	$str
	);

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
	'crashchars',
	$str
	);



/*
foreach($arrKeep as $k=>$v){
	//$str .= $v.'='.htmlentities(html_entity_decode($v, ENT_QUOTES),ENT_QUOTES).EOL;
	foreach($v as $k2=>$v2){	
		$str .= '==={'.EOL;
		foreach($v2 as $k3=>$v3){
			if($k3 == 'char'){
				$str .= '"'.$k3.'":['.$v3.']'.EOL;
			}else{
				$str .= '"'.$k3.'":'.$v3.EOL;
				}
			}
		$str .= '}==='.EOL.EOL;	
		}	
	}
*/





//cleanup
unset($oReg);



//END SCRIPT
