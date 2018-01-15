<?php
/**
@auth: Dwizzel
@date: 00-00-0000
@info: 	creer les keywords pour la recherche par keywrd du site en php
		c'est a dire un gros array qui pointe vers les exercises ids

@queries:

	on doit faire une exportation de la DB physiotec avant pour avoir le ranking des keywords

	SELECT idKeyword, locale, rank 
	FROM keyword_rank 
	WHERE rank > 0 AND locale IN ('fr_CA', 'en_US', 'es_MX') 
	ORDER BY keyword_rank.rank DESC;

	on doit faire une exportation de la DB physiotec avant pour avoir le ranking des exercises

	SELECT idExercise, rank 
	FROM exercise 
	WHERE idUser = 0 
	ORDER exercise.rank ASC;



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
	$renderPath = DIR_RENDER_KW_PHP;	
	if(!is_dir($renderPath)){
		//existe pas alors on le creer
		mkdir($renderPath, null, true);	
		}
	//select from DB des keywords
	//par ranking base sur le site de physiotec	
	$query = 'SELECT keyword_id AS keyword_id, name AS "name" FROM keywords WHERE locale = '.$localeId.';';
	//show		
	//echo $query.EOL.EOL;
	//result set
	$rs = $oReg->get('db-site')->query($query);
	//holder array
	$arrKeywordInfos = array();
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k2=>$v2){
			$keyId = intVal($v2['keyword_id']);
			$keyName = html_entity_decode($v2['name'], ENT_QUOTES|ENT_HTML5, 'UTF-8');
			//on strip
			$keyName = preg_replace('/[^a-zA-Z0-9\'\sÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ+-]/', ' ', $keyName);
			//on supprime les double space
			$keyName = preg_replace('/[\s]+/', ' ', $keyName);
			//trim		
			$keyName = trim($keyName);
			//on met tout en minuscule
			$keyName = mb_strtolower($keyName, 'UTF-8');
			//minor check
			if($keyName != '' && $keyId){
				//show
				echo 'KW['.$keyName.']'.EOL;
				//exercise infos holder
				if(!isset($arrKeywordInfos[$keyName])){
					$arrKeywordInfos[$keyName] = array();
					}
				//on va chercher les lkeyword ids
				$query = 'SELECT exercises_keywords.exercise_id AS "exercise_id", exercises.ranking AS "ranking" FROM exercises_keywords LEFT JOIN exercises ON exercises.exercise_id = exercises_keywords.exercise_id WHERE exercises_keywords.keyword_id = "'.$keyId.'" AND exercises.locale = "'.$localeId.'";';
				//show		
				//echo $query.EOL.EOL;
				//result set
				$rs2 = $oReg->get('db-site')->query($query);
				//minor check
				if($rs2 && $rs2->num_rows){
					foreach($rs2->rows as $k3=>$v3){
						$exId = intVal($v3['exercise_id']);
						$exRank = intVal($v3['ranking']);	
						if($exId){
							array_push($arrKeywordInfos[$keyName], array(
								$exId,
								$exRank
								));
							}
						}
					//clean	
					unset($k3, $v3);		
					}
				//clean	
				unset($rs2);
				}
			}
		//clean
		unset($k2, $v2);		
		}
	//clean
	unset($rs);
	//on va chercher les short title comme kw aussi
	if($bWithShortTitle){
		//par ranking base sur le site de physiotec	
		$query = 'SELECT exercises.exercise_id AS "exercise_id", exercises.short_title AS "name", exercises.ranking AS "ranking" FROM exercises WHERE exercises.locale = '.$localeId.';';
		//result set
		$rs = $oReg->get('db-site')->query($query);
		if($rs && $rs->num_rows){
			foreach($rs->rows as $k2=>$v2){
				$keyName = html_entity_decode($v2['name'], ENT_QUOTES|ENT_HTML5, 'UTF-8');	
				//on strip
				$keyName = preg_replace('/[^a-zA-Z0-9\'\sÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ+-]/', ' ', $keyName);
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
					if(!isset($arrKeywordInfos[$keyName])){
						$arrKeywordInfos[$keyName] = array();
						}
					//les ids
					$exId = intVal($v2['exercise_id']);
					$exRank = intVal($v2['ranking']);
					//on push
					if($exId){
						array_push($arrKeywordInfos[$keyName], array(
							$exId,
							$exRank
							));
						}
					}
				}
			//clean
			unset($k2, $v2);	
			}
		//clean
		unset($rs);
		}
	//on serialize
	$strOutput = serialize($arrKeywordInfos);
	//minor check
	if($strOutput != ''){
		//show 
		$filePath = $renderPath.'array-kw.'.$v.'.data';
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