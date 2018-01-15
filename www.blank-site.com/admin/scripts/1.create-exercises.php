<?php
/**
@auth: Dwizzel
@date: 00-00-0000
@info: creer les exercises dans le repertoire temporaire, en format short et format complet

@towatch:

	http://www.blank-site.com/fr/exercices/details/anal-contract-release-push-mp4.10545/


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

//limit pour tester les script ou des ids predefini uniquement
$selectThoseExerciseOnly = '';
if(isset($argv[1]) && $argv[1] != ''){
	$selectThoseExerciseOnly = ' AND exercise_id IN ('.$argv[1].') ';	
	}
//loop dans les lang
foreach($arrLangByKey as $k=>$v){
	//vars
	$localeId = intVal($k);	
	//on commence par creer les repertoire de langue
	$renderPath = DIR_RENDER_EXERCISES.$v.'/';
	if(!is_dir($renderPath)){
		//existe pas alors on le creer
		mkdir($renderPath, null, true);	
		}
	//select from DB des exercises
	$query = 'SELECT exercise_id, short_title, url_title, title, description, thumb_0, thumb_1, video_youtube, ref_id  FROM exercises WHERE locale = "'.$localeId.'" '.$selectThoseExerciseOnly.' ORDER BY exercise_id ASC;';
	//show		
	echo $query.EOL.EOL;
	//result set
	$rs = $oReg->get('db-site')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k2=>$v2){
			$exerciseId = intVal($v2['exercise_id']);
			//minor check
			if($exerciseId){
				//show
				echo 'EX['.$exerciseId.']'.EOL;
				//exercice infos holder
				$arrExerciseInfos =array(
					'id' => $exerciseId,
					'name' => html_entity_decode($v2['short_title'], ENT_QUOTES|ENT_HTML5, 'UTF-8'),
					'html' => $v2['short_title'], //html encode
					'href' => $v2['url_title'],
					'title' => $v2['title'],
					'description' => html_entity_decode($v2['description'], ENT_QUOTES|ENT_HTML5, 'UTF-8'),
					'thumb0' => $exerciseId.'-t0.jpg',
					'thumb1' => $exerciseId.'-t1.jpg',
					'pic0' => $exerciseId.'-p0.jpg',
					'pic1' => $exerciseId.'-p1.jpg',
					'datetime' => time(),
					'video' => $v2['video_youtube'],
					'locale' => $v,
					'ref_id' => $v2['ref_id'],
					'categories' => array(),
					'keywords' => array(),
					'languages' => array(),
					);
				//on va chercher toutes les categories dans leqeul se retouve l'exercise
				$query = 'SELECT categories.category_id AS "category_id", categories.name AS "name", categories.title AS "title" FROM categories_filters_exercises LEFT JOIN categories ON categories.category_id = categories_filters_exercises.category_id WHERE categories_filters_exercises.exercise_id = "'.$exerciseId.'" AND categories_filters_exercises.locale = "'.$localeId.'" ORDER BY categories.name ASC;';
				//show		
				//echo $query.EOL.EOL;
				//result set
				$rs2 = $oReg->get('db-site')->query($query);
				//minor check
				if($rs2 && $rs2->num_rows){
					foreach($rs2->rows as $k3=>$v3){
						//ids
						$categoryId = intVal($v3['category_id']);
						//minor check
						if($categoryId){
							//push
							$arrExerciseInfos['categories'][$categoryId] = array(
								'id' => $categoryId,
								'name' => html_entity_decode($v3['name'], ENT_QUOTES|ENT_HTML5, 'UTF-8'),
								'html' => $v3['name'],
								'href' => $v3['title'],
								'filters' => array(),
								);
							//on va chercher les filters relatif a la categorie et exercises
							$query = 'SELECT filters.filter_id AS "filter_id", filters.name AS "name", filters.title AS "title" FROM categories_filters_exercises LEFT JOIN filters ON filters.filter_id = categories_filters_exercises.filter_id WHERE categories_filters_exercises.exercise_id = "'.$exerciseId.'" AND categories_filters_exercises.category_id = "'.$categoryId.'" AND categories_filters_exercises.locale = "'.$localeId.'" ORDER BY filters.name ASC;';
							//show		
							//echo $query.EOL.EOL;
							//result set
							$rs3 = $oReg->get('db-site')->query($query);
							//minor check
							if($rs3 && $rs3->num_rows){
								foreach($rs3->rows as $k4=>$v4){
									//ids
									$filterId = intVal($v4['filter_id']);
									//garde juste un distinct filter peu importe la categorie
									if($filterId && !isset($arrExerciseInfos['categories'][$categoryId]['filters'][$filterId])){
										$arrExerciseInfos['categories'][$categoryId]['filters'][$filterId] = array(
											'id' => $filterId,
											'name' => html_entity_decode($v4['name'], ENT_QUOTES|ENT_HTML5, 'UTF-8'),
											'html' => $v4['name'],
											'href' => $v4['title'],
											);
										}
									}
								}
							//clean
							unset($rs3, $k4, $v4);
							}
						}
					}
				//clean
				unset($rs2, $k3, $v3);
				//vars
				//on va chercher tout les keywords surtout pour bing qui en tient compte google ne l'utilise pas
				$query = 'SELECT keywords.keyword_id AS "keyword_id", keywords.name AS "name", keywords.title AS "title" FROM exercises_keywords LEFT JOIN keywords ON keywords.keyword_id = exercises_keywords.keyword_id WHERE exercises_keywords.exercise_id = "'.$exerciseId.'" AND keywords.locale = "'.$localeId.'" ORDER BY keywords.name ASC;';
				//result set
				$rs2 = $oReg->get('db-site')->query($query);
				if($rs2 && $rs2->num_rows){
					foreach($rs2->rows as $k3=>$v3){
						//ids
						$keywordId = intVal($v3['keyword_id']);
						if($keywordId && !isset($arrExerciseInfos['keywords'][$keywordId])){
							//put
							$arrExerciseInfos['keywords'][$keywordId] = array(
								'id' => $keywordId,
								'name' => html_entity_decode($v3['name'], ENT_QUOTES|ENT_HTML5, 'UTF-8'),	
								'html' => $v3['name'], //html encode
								'href' => $v3['title'],
								'linked' => 0,
								);
							//on cherche dans la description si est la les 2 sont url decode en UTF-8
							if(preg_match('/[\s]{1}('.preg_quote($arrExerciseInfos['keywords'][$keywordId]['name'], '/').')[\s\.]{1}/Ui', $arrExerciseInfos['description'])){
								$arrExerciseInfos['keywords'][$keywordId]['linked'] = 1;
								}
							
							}
						}
					}
				//clean
				unset($rs2, $k3, $v3);
				//on va chercher si il y a autres exercice selon le ref_if
				$queryAltLang = 'SELECT * FROM exercises WHERE ref_id = "'.$arrExerciseInfos['ref_id'].'" AND locale <> "'.$k.'";';
				$rs2 = $oReg->get('db-site')->query($queryAltLang);
				//minor check
				if($rs2 && $rs2->num_rows){
					foreach($rs2->rows as $k3=>$v3){
						if(isset($arrLangByKey[$v3['locale']])){
							$arrExerciseInfos['languages'][$arrLangByKey[$v3['locale']]] = array(
								'id' => $v3['exercise_id'],
								'name' => html_entity_decode($v3['short_title'], ENT_QUOTES|ENT_HTML5, 'UTF-8'),
								'html' => $v3['short_title'], //html encode
								'href' => $v3['url_title'],
								'title' => $v3['title'],	
								'locale' => $arrLangByKey[$v3['locale']],
								);
							}
						}
					}
				//on serialize
				$strOutput = serialize($arrExerciseInfos);
				//minor check
				if($strOutput != '' && $arrExerciseInfos['id'] !== 0){
					//show 
					$filePath = $renderPath.$arrExerciseInfos['id'].'.details.data';
					echo $filePath.EOL;
					//file infos
					$fp = fopen($filePath, 'w');
					if($fp){
						fwrite($fp, $strOutput);
						}
					fclose($fp);
					}
				//on fait une coppie pour les resulat de listing avec infos minimum
				$arrExerciseInfosForListing = array(
					'id' => $arrExerciseInfos['id'],
					'html' => $arrExerciseInfos['html'],
					'href' => $arrExerciseInfos['href'],
					'title' => $arrExerciseInfos['title'],
					'thumb0' => $arrExerciseInfos['thumb0'],
					'thumb1' => $arrExerciseInfos['thumb1'],
					'pic0' => $arrExerciseInfos['pic1'],
					'pic1' => $arrExerciseInfos['pic1'],
					'categories' => $arrExerciseInfos['categories']
					);
				//on serialize
				$strOutput = serialize($arrExerciseInfosForListing);
				//minor check
				if($strOutput != '' && $arrExerciseInfosForListing['id'] !== 0){
					//show 
					$filePath = $renderPath.$arrExerciseInfosForListing['id'].'.listing.data';
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