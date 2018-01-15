<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	script de cleanup de des table de la DB avec les duplicate keyword, 
@inst:	
		kwtype = 1  = keywords
		kwtype = 3  = code exercice


	


*/
//-----------------------------------------------------------------------------------------------

header('Content-Type: text/plain; charset=utf-8', true);

//EXIT('COMMENT LINE '.__LINE__.' TO RUN'."\n"); //remove to insert data

// ERROR REPORTING
error_reporting(E_ALL);
// BASE DEFINE
require_once('/var/www/mobile....@physiotec.ca/dev/keywords.com/scripts/define.php');
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
//register new class too the registry to simplify arguments passing to other classes
$oReg = new Registry();
$oReg->set('glob', new Globals());	
$oReg->set('utils', new Utility($oReg));		
$oReg->set('log', new Log($oReg));
$oReg->set('err', new Errors($oReg));
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
$bExecute = true;
$bShow = true;
$gLogName = 'err-find-duplicate';
$gArrLocaleEnabled = array(
	'en_US',
	'fr_CA',
	'de_DE',
	'es_MX',
	'fr_FR',
	'nl_NL',
	'pt_PT',
	);
$iLastSearchedKeywordId = 654789; //0; 
// LIMITER
$gLimitMax = 1000000;
$gCounter = 0;
$gContinue = true;
//le script roule uniquement pour ce kw-type 1 = keyword, 3 = code exercice
$strScriptKwType = '1,3';	
//LOOP DES CHUNKS 
while($gContinue){
	if($bShow){			
		echo '--['.$iLastSearchedKeywordId.']---------------------------------------------------'.EOL.EOL;	
		}	
	//LE SELECT DE BASE
	$query = 'SELECT idKeyword AS "id", idLicence AS "idLicence", keyword AS "keyword", locale AS "locale", kwtype AS "kwtype" FROM keyword WHERE kwtype IN ('.$strScriptKwType.') AND idKeyword > "'.$iLastSearchedKeywordId.'" ORDER BY idKeyword ASC LIMIT 0,1;';	
	//show
	if($bShow){			
		//echo $query.EOL.EOL;	
		}
	//temp data
	$arrTmpData = array();		
	//result set
	$rs = $oReg->get('db')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k=>$v){
			//fill data
			$arrTmpData[intVal($v['id'])] = array(
				'id' => intVal($v['id']),
				'licence' => intVal($v['idLicence']),
				'locale' => $v['locale'],
				'word' => $v['keyword'],
				'kwtype' => intVal($v['kwtype']),
				);
			}
		//clean
		unset($rs, $k, $v);
		//minor check
		if(count($arrTmpData)){
			foreach($arrTmpData as $k=>$v){
				//last one 
				$iLastSearchedKeywordId = $v['id'];
				//la requete pour trouver les doublons
				//vu que c'est en ordre de id on cherche toujours plus haut que id qu'il est lui 
				//meme car il aura fait la recherche precedement
				$querySearch = 'SELECT idKeyword AS "id", kwtype AS "kwtype" FROM keyword WHERE keyword COLLATE utf8_bin LIKE "'.$oReg->get('db')->escape($v['word']).'" AND locale LIKE "'.$v['locale'].'" AND idLicence = "'.$v['licence'].'" AND kwtype IN ('.$strScriptKwType.') AND idKeyword > "'.$iLastSearchedKeywordId.'" ORDER BY idKeyword ASC;';
				if($bShow){
					echo $querySearch.EOL.EOL; 
					}
				//result set
				$rsSearch = $oReg->get('db')->query($querySearch);
				//minor check
				if($rsSearch && $rsSearch->num_rows){
					if($bShow){
						echo '['.$v['id'].']="'.$v['word'].'"'.EOL.EOL;
						}
					//set
					$iMainKwId = 0;
					$arrDuplicateKeywords = array();
					$strDuplicateKeywords = '';	
					//loop
					foreach($rsSearch->rows as $k2=>$v2){	
						//si c'est un kw = 3 alors on garde celui-la, 
						if($v2['kwtype'] == 3 && $iMainKwId === 0){
							$iMainKwId = $v2['id'];
							//push celui de base
							array_push($arrDuplicateKeywords, $iLastSearchedKeywordId);
						}else{
							//push les autres duplicate
							array_push($arrDuplicateKeywords, $v2['id']);
							}
						}
					//clean
					unset($k2, $v2);
					//si on en avait pas trouve de KW = 3 alors on prend celui de base
					if($iMainKwId == 0){
						$iMainKwId = $iLastSearchedKeywordId;
						}
					//le IN()
					$strDuplicateKeywords = implode(',', array_values($arrDuplicateKeywords));
					//clean
					unset($arrDuplicateKeywords);
					//on va chercher les keyword_exercise relie au autres			
					$queryExercise = 'SELECT idExercise AS "idExercise" FROM keyword_exercise WHERE idKeyword IN ('.$strDuplicateKeywords.');';
					//show
					if($bShow){			
						echo $queryExercise.EOL.EOL;	
						}
					//result set
					$rsExercise = $oReg->get('db')->query($queryExercise);
					//clea
					unset($queryExercise);
					//minor check
					if($rsExercise && $rsExercise->num_rows){
						//les exercises id
						$arrExerciseIds = array();
						//loop
						foreach($rsExercise->rows as $k2=>$v2){
							array_push($arrExerciseIds, $v2['idExercise']);
							}
						//clean
						unset($k2, $v2);
						//on supprime les autres exercise
						$queryDeleteExercise = 'DELETE FROM keyword_exercise WHERE idKeyword IN ('.$strDuplicateKeywords.');';
						//show
						if($bShow){			
							echo $queryDeleteExercise.EOL.EOL;	
							}
						if($bExecute){
							//delete
							$oReg->get('db')->query($queryDeleteExercise);
							}
						//clean
						unset($queryDeleteExercise);
						//on update avec un nouveau ou ancien si deja la
						foreach($arrExerciseIds as $k2=>$v2){
							$queryUpdateExercise = 'REPLACE INTO keyword_exercise (idKeyword, idExercise) VALUES ("'.$iMainKwId.'", "'.$v2.'");';
							//show
							if($bShow){			
								echo $queryUpdateExercise.EOL.EOL;	
								}
							if($bExecute){
								//replace or insert
								$oReg->get('db')->query($queryUpdateExercise);
								}
							//clean
							unset($queryUpdateExercise);
							}
						//clean
						unset($k2, $v2);
						}
					//clean
					unset($rsExercise);
					//on enleve les autres keywords	
					$queryDeleteKeywords = 'DELETE FROM keyword WHERE idKeyword IN ('.$strDuplicateKeywords.');';
					//show
					if($bShow){			
						echo $queryDeleteKeywords.EOL.EOL;	
						}
					if($bExecute){
						//replace or insert
						$oReg->get('db')->query($queryDeleteKeywords);
						}
					//clean
					unset($queryDeleteKeywords);	
					//maintenant on va chercher les keyword_rank
					//cas special si est en en_US alors il se peut que les 
					//mots cle soit aussi dans les autres langues
					//car la recherche EX: en fr_CA se fera aussi en en_US
					//mais pas avec le meme ranking 
					//alors il faut essayer avec les autres langue aussi
					//dans ce cas si on va rajouter le iMainKwId a la string pour l'adition des rank	
					$queryRank = 'SELECT idKeyword AS "id", locale AS "locale", rank AS "rank" FROM keyword_rank WHERE idKeyword IN ('.$iMainKwId.','.$strDuplicateKeywords.') ORDER BY idKeyword ASC;';
					//show
					if($bShow){			
						echo $queryRank.EOL.EOL;	
						}
					//result set
					$rsRank = $oReg->get('db')->query($queryRank);
					//minor check
					if($rsRank && $rsRank->num_rows){
						//les keywords id
						$arrKeywordRankIds = array();
						//loop
						foreach($rsRank->rows as $k2=>$v2){
							//check si c'est une autre langue
							if(!isset($arrKeywordRankIds[$v2['id']])){
								//init
								$arrKeywordRankIds[$v2['id']] = array(
									'id' => $v2['id'],
									'locale-rank' => array(),
									);
								}
							//on rajoute la locale rank
							$arrKeywordRankIds[$v2['id']]['locale-rank'][$v2['locale']] = intVal($v2['rank']);
							}
						//clean
						unset($k2, $v2);
						//on delete maintenant les rank on rajoute aussi le idMainKwId a la string
						$queryDeleteKeywordsFromRank = 'DELETE FROM keyword_rank WHERE idKeyword IN ('.$iMainKwId.','.$strDuplicateKeywords.');';
						//show
						if($bShow){			
							echo $queryDeleteKeywordsFromRank.EOL.EOL;	
							}
						if($bExecute){
							//replace or insert
							$oReg->get('db')->query($queryDeleteKeywordsFromRank);
							}
						//clean	
						unset($queryDeleteKeywordsFromRank);
						//on additionne tout les rank dune locale et on insere
						$arrRankByLocale = array();
						//loop
						foreach($arrKeywordRankIds as $k2=>$v2){
							foreach($v2['locale-rank'] as $k3=>$v3){
								if(!isset($arrRankByLocale[$k3])){
									$arrRankByLocale[$k3] = 0;	
									}
								//addition
								$arrRankByLocale[$k3] += intVal($v3);	
								}
							unset($k3, $v3);
							}
						//clean
						unset($k2, $v2);
						//on insere dans keyword_rank
						foreach($arrRankByLocale as $k2=>$v2){
							$queryUpdateKeyrank = 'REPLACE INTO keyword_rank (idKeyword, locale, rank) VALUES ("'.$iMainKwId.'", "'.$k2.'", "'.$v2.'");';
							//show
							if($bShow){			
								echo $queryUpdateKeyrank.EOL.EOL;	
								}
							if($bExecute){
								//replace or insert
								$oReg->get('db')->query($queryUpdateKeyrank);
								}
							//clean
							unset($queryUpdateKeyrank);
							}
						//clean
						unset($k2, $v2, $arrKeywordRankIds, $arrRankByLocale);
						}
					//clean	
					unset($rsRank, $queryRank, $iMainKwId, $strDuplicateKeywords);
						
					}
				//clean
				unset($rsSearch, $querySearch);
				}
			//clean
			unset($arrTmpData, $k, $v);	
			}
	}else{
		$gContinue = false;
		}
	//limit
	$gCounter++;	
	if($gCounter > $gLimitMax){
		$gContinue = false;	
		}
	}
//show
echo 'END SCRIPT'.EOL.EOL;









//END



