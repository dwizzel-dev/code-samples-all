<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	script de cleanup de des table de la DB avec les duplicate keyword, 
@inst:	
		kwtype = 1  = keywords
		kwtype = 2  = short title
		kwtype = 3  = code exercice


	1. 	On va chercher les keyword un a un
	2. 	On utilise le idKeyword, idLicence, locale et (kwtype)
	3. 	On va chercher tout les keyword avec le meme 
		keyword, idLicense, locale et (kwtype = 1 = keyword ou kwtype = 3 = code exercise)
	4. 	Si dans les resultat on a le kwtype = 3 
		on va prendre le id de celui-ci, car c'est un "code exercise" nouvellement insere
	5. 	Ensuite on va chercher les idExercise dans la table keyword_exercise relie a chacun 
		des retours excepte celui du id que l'on a garde
	6. 	On supprime les idKeyword que l'on ne veut plus de la table keyword_exercise et on les relit
		avec le nouveau idKeyword<=>idExercise
	7. 	Ensuite on va chercher les rank et locale dans la table keyword_rank relie a chacun 
		des retours excepte celui du id que l'on a garde
	8.	
		


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
$bExecute = false;
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
//CONATINERS
$arrDuplicateCount = array(
	'KW1' => 0,
	'KW2' => 0,
	'KW3' => 0,
	);
$arrDuplicateKeywords = array();
$arrDuplicateTitles = array();
$arrDuplicateKeywordsIdsFound = array(0);
// LIMITER
$gContinue = true;
$gLimitMin = 0;
$gLimitMax = 0; //0 = no limit
$gLimitStart = $gLimitMin;
$gChunkLimit = 2000;
//LOOP DES CHUNKS
while($gContinue){
	//LE SELECT DE BASE
	$gBaseQuery = 'SELECT idKeyword AS "id", idLicence AS "idLicence", keyword AS "keyword", locale AS "locale", kwtype AS "kwtype" FROM keyword WHERE idKeyword NOT IN ('.implode(',',array_values($arrDuplicateKeywordsIdsFound)).') ORDER BY idKeyword ASC LIMIT ';	
	//select from DB
	$query = $gBaseQuery.$gLimitStart.','.$gChunkLimit.';';	
	//show
	if($bShow){			
		echo $query.EOL.EOL;	
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
				//la requete pour trouver les doublons
				//vu que c'est en ordre de id on cherche toujours plus haut que id qu'il est lui 
				//meme car il aura fait la recherche precedement
				$querySearch = 'SELECT idKeyword AS "id", kwtype AS "kwtype" FROM keyword WHERE keyword COLLATE utf8_bin LIKE "'.$oReg->get('db')->escape($v['word']).'" AND locale LIKE "'.$v['locale'].'" AND idLicence = "'.$v['licence'].'" AND idKeyword > "'.$v['id'].'" AND idKeyword NOT IN ('.implode(',',array_values($arrDuplicateKeywordsIdsFound)).')';
				//dans le cas d'un kwtype = (1 ou 3) ou (2)
				if($v['kwtype'] == 1 || $v['kwtype'] == 3){
					$querySearch .= ' AND (kwtype = 1 OR kwtype = 3) ';
				}else{
					$querySearch .= ' AND kwtype = 2 ';
					}
				$querySearch .= 'ORDER BY idKeyword ASC;';
				if($bShow){
					//echo $querySearch.EOL.EOL; 
					}
				//result set
				$rsSearch = $oReg->get('db')->query($querySearch);
				//minor check
				if($rsSearch && $rsSearch->num_rows){
					//set
					if($v['kwtype'] == 1 || $v['kwtype'] == 3){
						//set
						$arrDuplicateKeywords[$v['id']] = array(
							'KW1' => array(),
							'KW3' => array(),
							);
						//on push le principal
						array_push($arrDuplicateKeywords[$v['id']]['KW'.$v['kwtype']], $v['id']);
						//push le deja found
						array_push($arrDuplicateKeywordsIdsFound, $v['id']);
						//loop
						foreach($rsSearch->rows as $k2=>$v2){	
							//le count total de chaque
							$arrDuplicateCount['KW'.$v2['kwtype']]++;
							//push
							array_push($arrDuplicateKeywords[$v['id']]['KW'.$v2['kwtype']], $v2['id']);
							//push le deja found
							array_push($arrDuplicateKeywordsIdsFound, $v2['id']);
							}
					}else{
						//loop
						foreach($rsSearch->rows as $k2=>$v2){	
							//le count total de chaque
							$arrDuplicateCount['KW'.$v2['kwtype']]++;
							//push
							array_push($arrDuplicateTitles[$v['id']], $v2['id']);
							//push le deja found
							array_push($arrDuplicateKeywordsIdsFound, $v2['id']);
							}
						}
					//clean
					unset($k2, $v2);
					}
				//clean
				unset($rsSearch, $querySearch);
				}
			//clean
			unset($arrTmpData, $k, $v);	
			}
		//increment	
		$gLimitStart += $gChunkLimit;
		//pour ne pas tout faire	
		if($gLimitStart > $gLimitMax && $gLimitMax > 0){
			$gContinue = false;
			}
	}else{
		$gContinue = false;
		}
	}
//final count
echo 'FINAL COUNT:';
print_r($arrDuplicateCount);

//show
echo 'END SCRIPT'.EOL.EOL;









//END



