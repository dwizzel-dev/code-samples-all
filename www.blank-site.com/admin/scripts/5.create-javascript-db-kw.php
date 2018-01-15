<?php
/**
@auth: Dwizzel
@date: 00-00-0000
@info: creer les keywords pour la recherche par keywrd du site en javascript

@queries:

	on doit faire une exportation de la DB physiotec avant pour avoir le ranking

	SELECT idKeyword, locale, rank 
	FROM keyword_rank 
	WHERE rank > 0 AND locale IN ('fr_CA', 'en_US', 'es_MX') 
	ORDER BY keyword_rank.rank DESC;


*/

//EXIT('COMMENT LINE '.__LINE__.' TO RUN'."\n"); //remove to insert data

//pour aller chercher les keywords pour le javscript

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

$bWithShortTitle = true;

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
	$renderPath = DIR_RENDER_KW_JS;	
	if(!is_dir($renderPath)){
		//existe pas alors on le creer
		mkdir($renderPath, null, true);	
		}
	//select from DB des exercises
	//par ranking base sur le site de physiotec	
	$query = 'SELECT name AS "name" FROM keywords WHERE locale = '.$localeId.' ORDER BY ranking DESC;';
	//show		
	//echo $query.EOL.EOL;
	//result set
	$rs = $oReg->get('db-site')->query($query);
	//le holder array
	$arrKeywordInfos = array();
	$arrKeywordInfosBigData = array();
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k2=>$v2){
			$keyName = html_entity_decode($v2['name'], ENT_QUOTES|ENT_HTML5, 'UTF-8');
			//on strip
			$keyName = preg_replace('/[^a-zA-Z0-9\s\'Þßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ+-]/', ' ', $keyName);
			//on supprime les double space
			$keyName = preg_replace('/[\s]+/', ' ', $keyName);
			//trim		
			$keyName = trim($keyName);
			//on met tout en minuscule
			$keyName = mb_strtolower($keyName, 'UTF-8');
			//minor check
			if($keyName != ''){
				//show
				echo 'KW['.$keyName.']'.EOL;
				//exercise infos holder
				if(strlen($keyName) < 25){
					if(!isset($arrKeywordInfos[$keyName])){
						$arrKeywordInfos[$keyName] = 1;
						}
					}
				//exercise infos holder
				if(!isset($arrKeywordInfosBigData[$keyName])){
					$arrKeywordInfosBigData[$keyName] = 1;
					}	
				}
			}
		unset($k2, $v2);	
		}
	//clean
	unset($rs);
	//imaplode
	$strOutput = '|'.implode('|', array_keys($arrKeywordInfos)).'|';
	//minor check
	if($strOutput != ''){
		//la db pour le javascript 
		$filePath = $renderPath.'db-kw.'.$v.'.data';
		echo $filePath.EOL;
		//file infos
		$fp = fopen($filePath, 'w');
		if($fp){
			fwrite($fp, $strOutput);
			}
		fclose($fp);
		}
	//clean
	unset($arrKeywordInfos);
	//la db pour le php avec les short title en plus	
	if($bWithShortTitle){
		//maintenant on va chercher les title des exercices
		//par ranking base sur le site de physiotec	
		$query = 'SELECT short_title AS "name" FROM exercises WHERE locale = '.$localeId.' ORDER BY ranking DESC;';
		//show		
		//echo $query.EOL.EOL;
		//result set
		$rs = $oReg->get('db-site')->query($query);
		//minor check
		if($rs && $rs->num_rows){
			foreach($rs->rows as $k2=>$v2){
				$keyName = html_entity_decode($v2['name'], ENT_QUOTES|ENT_HTML5, 'UTF-8');
				//on strip
				$keyName = preg_replace('/[^a-zA-Z0-9\s\'Þßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ]/', ' ', $keyName);
				//on supprime les double space
				$keyName = preg_replace('/[\s]+/', ' ', $keyName);	
				//trim		
				$keyName = trim($keyName);	
				//on met tout en minuscule
				$keyName = mb_strtolower($keyName, 'UTF-8');
				//minor check
				if($keyName != ''){
					//show
					echo 'KW-ST['.$keyName.']'.EOL;
					//exercise infos holder
					if(!isset($arrKeywordInfosBigData[$keyName])){
						$arrKeywordInfosBigData[$keyName] = 1;
						}
					}
				}
			unset($k2, $v2);	
			}
		//clean
		unset($rs);		
		}
	//imaplode
	$strOutput = '|'.implode('|', array_keys($arrKeywordInfosBigData)).'|';
	//minor check
	if($strOutput != ''){
		//show 
		$filePath = $renderPath.'complete-db-kw.'.$v.'.data';
		echo $filePath.EOL;
		//file infos
		$fp = fopen($filePath, 'w');
		if($fp){
			fwrite($fp, $strOutput);
			}
		fclose($fp);
		}	
	}
//clean
unset($k, $v);




//SCRIPT END