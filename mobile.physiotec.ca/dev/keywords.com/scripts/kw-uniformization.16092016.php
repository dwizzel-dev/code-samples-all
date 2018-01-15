<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	script pour uniformise en utf-8
@inst:

	kwtype == 1 // est un keyword 
	kwtype == 2 // est un short_title d'un exercice ou plusieur si ils sont pareils
	kwtype == 3 // est un code exercice

	search pattern in VI: &[a-zA-Z0-9]\+
	
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

// certain cahractere cause des problemes ou ne sont pas complet
// les saut de ligne 		= [{EOL}]
// les points interogation 	= [{QUEST}]	


//$gArrReverseTranslationTable = array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES|ENT_HTML5));


function stringConverter($str){

	//on decode en char normale html
	$str = htmlspecialchars_decode($str, ENT_QUOTES);	
	$str = html_entity_decode($str, ENT_QUOTES|ENT_HTML401, 'ISO-8859-1');
	$str = html_entity_decode($str, ENT_QUOTES|ENT_HTML5, 'ISO-8859-1');
	$str = html_entity_decode($str, ENT_QUOTES|ENT_XHTML, 'ISO-8859-1');
		
	//trim
	$str = trim($str);	

	//special chars numeric
	$str = str_replace(
		array(
			'&#145;', 
			'&#146;',
			'&#176;', 
			'&#186;', 
			'&#174;', 
			'&#188;', 
			'&#173;', 
			'&#171;', 
			'&#177;', 
			'&#163;', 
			'&#190;', 
			'&#187;', 
			'&#189;', 
			'&#140;',
			'&#147;',
			'&#148;',
			'&#149;',
			'&#150;',
			'&#151;',
			'&#156;',
			'&#160;',
			'&#130;',
			'&#131;',
			'&#133;',
			),
		array(
			'&lsquo;', 
			'&rsquo;', 
			'&deg;',
			'&ordm;', 
			'&reg;', 
			'&frac14;', 
			'&shy;', 
			'&laquo;', 
			'&plusmn;', 
			'&pound;', 
			'&frac34;', 
			'&raquo;', 
			'&frac12;', 
			'&OElig;', 
			'&ldquo;', 
			'&rdquo;', 
			'&bull;', 
			'&ndash;', 
			'&mdash;', 
			'&oelig;', 
			'&nbsp;', 
			'&sbquo;', 
			'&fnof;',
			'&mldr;',
			),
		$str);
		
	//cariage return
	$str = str_replace(
		array(
			'<br >', 
			'<br>', 
			'<br />', 
			'<br/>',
			"\n\r",
			"\r\n",
			"\r\f",	
			"\f\r",		
			"\n",
			"\r",
			"\f",
			chr(130), 
			chr(131), 
			chr(133),
			chr(10).chr(13), 
			chr(13).chr(10), 
			chr(9), 
			chr(10), 
			chr(11),
			chr(12),
			chr(13),
			chr(14),
			chr(15),
			), 
		"[{eol}]", $str);

	//idealement on devrait remplacer les titrait par des tirait normal
	//liste des chars a remplacer par d'autre
	/*

	'_'	= '&ndash' = '-'
	'“' = '&ldquo;' = '"'
	'”' = '&rdquo;' = '"'	

	*/

	//des trucs qui ne passe pas ou qui sont double htmlise
	/*	
	$str = str_replace(
		array(
			'&semi;', 
			'&comma;',
			'&colon;',
			'&sol;',
			'&lpar;',
			'&rpar;',
			'&period;',
			'&oelig;',
			'&excl;',
			'&lowbar;',
			'&ast;',
			'&plus;',
			'&commat;',
			'&fjlig;',
			'&lbrace;',
			'&rcub;',
			'&percnt;',
			'&num;',
			'&lbrack;',
			'&lsqb;',
			'&rbrack;',
			'&rsqb;',
			'&OpenCurlyDoubleQuote;',
			'&rdquo;',
			),
		array(
			';', 
			',',
			':',
			'/',
			'(',
			')',
			'.',
			'œ',
			'!',
			'_',
			'*',
			'+',
			'@',
			'fj',
			'{',
			'}',
			'%',
			'#',
			'[',
			'[',	
			']',
			']',
			'“',
			'”',
			),
		$str);
	*/

	//des trucs qui n'on pas de bon sens car ils sont brise ou pas complet
	//selon l'analyse les É/é ne passe jamais la function precedente les a remplacer par [{quest}]	
	$str = str_replace(
		array(
			'&quest;', 
			'&tab;',
			'&Tab;',	
			'&bsol;\'',
			'&bsol;"',
			'[{eol}]',
			"\'",
			"\t",
			),
		array(
			'&eacute;',	
			' ',	
			' ',
			"'",	
			'"',
			' ',
			"'",
			' ',
			),
		$str);

	//strip multiple espace pour un seul
	$str = str_replace('&nbsp;', ' ', $str);
	$str = preg_replace('/[\s]+/', ' ', $str);

	//les fins de ligne bizarre ou breake genre &comma
	$str = preg_replace('/(.*)(&{1}[\d\w]*)$/', '${1}', $str);

	//un dernier trim pour le fun
	$str = trim($str);

	//si les fins sont une virgule
	$str = preg_replace('/(.*),$/', '${1}', $str);

	return $str;
	}


//-----------------------------------------------------------------------------------------------

//show
echo 'START SCRIPT'.EOL.EOL;
//VARS
$bExecute = false;
$bShow = true;
$iSleep = 0;
$gLogName = 'err-uniformization';
$gLogPreview = 'preview-uniformization';
$gLogDataStep = array();
// LIMITER
$gContinue = true;
$gLimitMin = 91000;
$gLimitMax = 10000;//0; //0 = no limit
$gLimitStart = $gLimitMin;
$gChunkLimit = 10000;
//LE SELECT DE BASE
//$gBaseQuery = 'SELECT idKeyword AS "id", keyword AS "data" FROM keyword ORDER BY idKeyword ASC LIMIT ';
$gBaseQuery = 'SELECT idKeyword AS "id", keyword AS "data" FROM keyword WHERE kwtype <> 3 AND keyword NOT REGEXP "^([[:alnum:]]{3,}[[:digit:]]+)$" ORDER BY idKeyword ASC LIMIT ';
//LOOP DES CHUNKS
while($gContinue){
	//select from DB
	$query = $gBaseQuery.$gLimitStart.','.$gChunkLimit.';';	
	//show		
	if($bShow){	
		echo $query.EOL.EOL;	
		}
	//result set
	$rs = $oReg->get('db')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k=>$v){
			//le holder du data qui passe dans les different step
			$gLogDataStep = array();	
			//id kwyword
			$idKw = intVal($v['id']);
			//on va remplacer les saut de ligne qui fuck le decodage
			$strData = $v['data'];
			//log step
			array_push($gLogDataStep, '[0A]:"'.$strData.'"');
			//minor check sur ce queon a besoin
			if(isset($strData) && $strData != ''){
				//flag savoir si on fait update
				$bUpdateData = true;
				//on detect encodage de la string
				$encoding = mb_detect_encoding($strData, mb_detect_order(), true);
				//minor check sur l'encodage de base
				if($encoding !== false){
					//on convertit le encoding de *** a utf-8 pour catcher les erreurs 
					$dataConvertEncoding = mb_convert_encoding($strData, 'UTF-8', $encoding);
					//log step
					array_push($gLogDataStep, '"'.$encoding.'"'.EOL.'[1A]:"'.$dataConvertEncoding.'"');
					//on change le encoding et check si valide
					$data = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $dataConvertEncoding);
					//log step
					array_push($gLogDataStep, '[1B]:"'.$data.'"');
					if($data === false){
						$bUpdateData = false;
						$strErr = '[IDKW:'.$idKw.'] "'.$dataConvertEncoding.'"';
						trigger_error($strErr , E_USER_NOTICE);
						$oReg->get('log')->log($gLogName.'-0', $strErr);
						sleep($iSleep);
					}else{
						//
						//si c'est du UTF-8 ca se peut qu'il soit doublement encode alors on verifie
						if($encoding == 'UTF-8'){
							//on detect le double encodage de la string
							$doubleEncoding = mb_detect_encoding($data, mb_detect_order(), true);
							if($doubleEncoding == 'UTF-8'){
								//on convertit le encodingde *** a utf-8 pour catcher les erreurs 
								$dataConvertEncoding = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
								//log step
								array_push($gLogDataStep, '[2A]:"'.$dataConvertEncoding.'"');
								//on change le encoding et check si valide
								$data = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $dataConvertEncoding);
								//log step
								array_push($gLogDataStep, '[2B]:"'.$data.'"');	
								//si pas valide utf-8
								if($data === false){
									$bUpdateData = false;
									$strErr = '[IDKW:'.$idKw.'] "'.$dataConvertEncoding.'"';
									trigger_error($strErr , E_USER_NOTICE);
									$oReg->get('log')->log($gLogName.'-1', $strErr);
									sleep($iSleep);
									}
								}
							}	
						}
				}else{
					//ca passe pas on trig une erreur perso
					$bUpdateData = false;
					$strErr = '[IDKW:'.$idKw.'] "'.$strData.'"';
					trigger_error($strErr , E_USER_NOTICE);
					$oReg->get('log')->log($gLogName.'-2', $strErr);
					sleep($iSleep);
					}
			}else{
				$strErr = '[IDKW:'.$idKw.'] "'.$strData.'"';
				trigger_error($strErr , E_USER_NOTICE);
				$oReg->get('log')->log($gLogName.'-3', $strErr);	
				sleep($iSleep);
				}
			//ca passe
			if($bUpdateData === true && $data != '' && isset($idKw) && $idKw != '' && $idKw !== 0){
				//on strip
				$data = stringConverter($data);
				//log step
				array_push($gLogDataStep, '[3A]:"'.$data.'"');
				//on reconvertit en utf-8
				$dataUTF8 = mb_convert_encoding($data, 'UTF-8', 'ISO-8859-1');
				//log step
				array_push($gLogDataStep, '[3C]:"'.$dataUTF8.'"');
				//minor check
				if($dataUTF8 != ''){
					//to lower case
					$dataUTF8 = mb_strtolower($dataUTF8, 'UTF-8');
					//log step
					array_push($gLogDataStep, '[4A]:"'.$dataUTF8.'"');
					//show
					if($bShow){	
						echo $idKw.EOL;
						$strShow = '['.$idKw.']'.EOL;
						foreach($gLogDataStep AS $kL=>$vL){
							$strShow .= $vL.EOL;
							}
						$oReg->get('log')->log($gLogPreview, $strShow);
						unset($kL,$vL);
						}
					//si execute	
					if($bExecute){
						//query update
						$update = 'UPDATE keyword SET keyword = "'.$oReg->get('db')->escape($dataUTF8).'" WHERE idKeyword = "'.$idKw.'";';
						//show
						if($bShow){
							echo $update.EOL.EOL;
							}
						//on fait le update
						$rs2 = $oReg->get('db')->query($update);
						}
					//clean
					unset($rs2);
				}else{
					//ca passe pas on trig une erreur perso
					$strErr = '[IDKW:'.$idKw.'] "'.$strData.'" = "'.$data.'"';
					trigger_error($strErr , E_USER_NOTICE);
					$oReg->get('log')->log($gLogName.'-4', $strErr);
					sleep($iSleep);
					}
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
