<?php
/**
@auth: Dwizzel
@date: 00-00-0000
@info: creer les keywords dans le repertoire temporaire

*/

//EXIT('COMMENT LINE '.__LINE__.' TO RUN'."\n"); //remove to insert data

//-------------------------------------------------------------------------------------------------------------

//CONTENTTYPE
header('Content-Type: text/plain; charset=utf-8', true);

// ERROR REPORTING
error_reporting(E_ALL);

// base required
if(!defined('IS_DEFINED')){
	require_once('../define.php');
	}

// BASE REQUIRED
require_once(DIR_INC.'required.php');

//required
require_once(DIR_INC.'helpers.php');
require_once(DIR_CLASS.'registry.php');
require_once(DIR_CLASS.'database.php');
require_once(DIR_CLASS.'log.php');

// register new class too the registry to simplify arguments passing to other classes
$oReg = new Registry();
$oReg->set('log', new Log($oReg));
$oReg->set('db-site', new Database(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE_VISOU, $oReg));

// LANG BY key=>value
$arrLangByKey = array(
	1 => 'en_US',
	2 => 'fr_CA',
	3 => 'es_MX',	
	);

//STEP ----------------------------------------------------------------------------------------------------

//loop dans les lang
foreach($arrLangByKey as $k=>$v){
	//vars
	$localeId = intVal($k);	
	//on commence par creer les repertoire de langue
	$renderPath = DIR_RENDER_KEYWORDS.$v.'/';	
	if(!is_dir($renderPath)){
		//existe pas alors on le creer
		mkdir($renderPath, null, true);	
		}
	//select from DB des exercises
	$query = 'SELECT keyword_id, name, title FROM keywords WHERE locale = "'.$localeId.'" ORDER BY keyword_id ASC;';
	//show		
	echo $query.EOL.EOL;
	//result set
	$rs = $oReg->get('db-site')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k2=>$v2){
			$keywordId = intVal($v2['keyword_id']);
			//minor check
			if($keywordId){
				//show
				echo 'KW['.$keywordId.']'.EOL;
				//exercie infos holder
				$arrKeywordInfos =array(
					'id' => $keywordId,
					'name' => html_entity_decode($v2['name'], ENT_QUOTES|ENT_HTML5, 'UTF-8'),
					'html' => $v2['name'],
					'href' => $v2['title'],	
					'datetime' => time(),
					'locale' => $v,
					'exercises' => array(),
					);
				//on va chercher tout les exercises
				$query = 'SELECT exercises.exercise_id AS "exercise_id", exercises.title AS "title", exercises.short_title AS "short_title", exercises.url_title AS "url_title" FROM exercises LEFT JOIN exercises_keywords ON exercises.exercise_id = exercises_keywords.exercise_id WHERE exercises_keywords.keyword_id = "'.$keywordId.'" AND exercises.locale = "'.$localeId.'" ORDER BY exercises.short_title ASC;';
				//show		
				//echo $query.EOL.EOL;
				//result set
				$rs2 = $oReg->get('db-site')->query($query);
				if($rs2 && $rs2->num_rows){
					foreach($rs2->rows as $k3=>$v3){
						$exerciseId = intVal($v3['exercise_id']);
						$strConv = mb_convert_encoding($v3['short_title'], 'ISO-8859-1', 'UTF-8');
						$strConv = html_entity_decode($strConv, ENT_QUOTES|ENT_HTML5, 'ISO-8859-1');
						$strFirstLetter = substr($strConv, 0, 1);
						$strFirstLetter = mb_convert_encoding(strtolower($strFirstLetter), 'UTF-8', 'ISO-8859-1');
						$arrKeywordInfos['exercises'][$exerciseId] = array(
							'id' => $exerciseId,
							'letter' => $strFirstLetter,
							'html' => $v3['short_title'],
							'title' => $v3['title'],
							'href' => $v3['url_title'],
							'locale' => $v,
							);	
						}
					}
				unset($rs2, $k3, $v3);
				//on serialize
				$strOutput = serialize($arrKeywordInfos);
				//minor check
				if($strOutput != '' && $arrKeywordInfos['id'] !== 0){
					//show 
					$filePath = $renderPath.$arrKeywordInfos['id'].'.data';
					echo $filePath.EOL;
					//file infos
					$fp = fopen($filePath, 'w');
					if($fp){
						fwrite($fp, $strOutput);
						}
					fclose($fp);
					}
				}
			}
		}
	//clean
	unset($rs, $k2, $v2);
	}
//clean
unset($k, $v);




//SCRIPT END