<?php
/**
@auth: Dwizzel
@date: 00-00-0000
@info: creer les categories dans le repertoire temporaire

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
	$renderPath = DIR_RENDER_CATEGORIES.$v.'/';	
	if(!is_dir($renderPath)){
		//existe pas alors on le creer
		mkdir($renderPath, null, true);	
		}
	//le container de toute les categories
	$arrAllCategoriesInfos = array();
	//on va chercher toutes les categories dans leqeul se retouve l'exercise
	$query = 'SELECT category_id, title, name, description FROM categories WHERE categories.category_id IN (SELECT DISTINCT(categories_filters_exercises.category_id) FROM categories_filters_exercises WHERE categories_filters_exercises.locale = "'.$localeId.'") ORDER BY categories.name ASC;';
	//result set
	$rs = $oReg->get('db-site')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k2=>$v2){
			$categoryId = intVal($v2['category_id']);	
			//push
			$arrCategoryInfos = array(
				'id' => $categoryId,
				'name' => html_entity_decode($v2['name'], ENT_QUOTES|ENT_HTML5, 'UTF-8'),
				'html' => $v2['name'],
				'href' => $v2['title'],	
				'description' => $v2['description'],
				'datetime' => time(),
				'locale' => $v,
				'filters' => array(),
				);
			//on va chercher tout les filtres selon la category id
			$query = 'SELECT filters.filter_id AS "filter_id", filters.title AS "title", filters.name AS "name", filters.description AS "description" FROM categories_filters_exercises LEFT JOIN filters ON categories_filters_exercises.filter_id = filters.filter_id WHERE categories_filters_exercises.locale = "'.$localeId.'" AND categories_filters_exercises.category_id = "'.$categoryId.'" ORDER BY filters.name ASC;';	
			//result set
			$rs2 = $oReg->get('db-site')->query($query);
			if($rs2 && $rs2->num_rows){
				foreach($rs2->rows as $k3=>$v3){
					//ids
					$filterId = intVal($v3['filter_id']);
					if($filterId && !isset($arrCategoryInfos['filters'][$filterId])){
						$arrCategoryInfos['filters'][$filterId] = array(
							'id' => $filterId,
							'name' => html_entity_decode($v3['name'], ENT_QUOTES|ENT_HTML5, 'UTF-8'),
							'html' => $v3['name'],
							'description' => $v3['description'],
							'datetime' => time(),
							'href' => $v3['title'],
							'locale' => $v,
							);
						$strConv = mb_convert_encoding($v3['name'], 'ISO-8859-1', 'UTF-8');
						$strConv = html_entity_decode($strConv, ENT_QUOTES|ENT_HTML5, 'ISO-8859-1');
						$strFirstLetter = substr($strConv, 0, 1);
						$strFirstLetter = mb_convert_encoding(strtolower($strFirstLetter), 'UTF-8', 'ISO-8859-1');	
						$arrCategoryInfos['filters'][$filterId]['letter'] = $strFirstLetter;	
						}
					}
				}
			//clean
			unset($rs2, $k3, $v3);
			//on serialize
			$strOutput = serialize($arrCategoryInfos);
			//minor check
			if($strOutput != '' && $arrCategoryInfos['id'] !== 0){
				//show 
				$filePath = $renderPath.$arrCategoryInfos['id'].'.data';
				echo $filePath.EOL;
				//file infos
				$fp = fopen($filePath, 'w');
				if($fp){
					fwrite($fp, $strOutput);
					}
				fclose($fp);
				//le categorie/filters -> exercices
				foreach($arrCategoryInfos['filters'] as $k3=>$v3){
					$arrExercises = $v3;
					//on rajoute les infos de la categorie parents
					$arrExercises['category'] = array(
						'id' => $arrCategoryInfos['id'],
						'html' => $arrCategoryInfos['html'],
						'href' => $arrCategoryInfos['href'],	
						'locale' => $arrCategoryInfos['locale'],
						);
					//on rajoute exercises
					$arrExercises['exercises'] = array();
					$filterId = intVal($v3['id']);
					//on va chercher la liste des exercices pour chaque categorie-filters
					$query = 'SELECT exercises.exercise_id AS "exercise_id", exercises.title AS "title", exercises.url_title AS "url_title", exercises.short_title AS "short_title" FROM categories_filters_exercises LEFT JOIN exercises ON categories_filters_exercises.exercise_id = exercises.exercise_id WHERE categories_filters_exercises.locale = "'.$localeId.'" AND categories_filters_exercises.category_id = "'.$categoryId.'" AND categories_filters_exercises.filter_id = "'.$filterId.'" ORDER BY exercises.title ASC;';
					//result set
					$rs2 = $oReg->get('db-site')->query($query);
					if($rs2 && $rs2->num_rows){
						foreach($rs2->rows as $k4=>$v4){
							$exerciseId = intVal($v4['exercise_id']);
							$strConv = mb_convert_encoding($v4['short_title'], 'ISO-8859-1', 'UTF-8');
							$strConv = html_entity_decode($strConv, ENT_QUOTES|ENT_HTML5, 'ISO-8859-1');
							$strFirstLetter = substr($strConv, 0, 1);
							$strFirstLetter = mb_convert_encoding(strtolower($strFirstLetter), 'UTF-8', 'ISO-8859-1');
							$arrExercises['exercises'][$exerciseId] = array(
								'id' => $exerciseId,
								'letter' => $strFirstLetter,
								'html' => $v4['short_title'],
								'title' => $v4['title'],
								'href' => $v4['url_title'],
								'locale' => $v,
								);	
							}
						//on serialize et write
						//on serialize
						$strOutput = serialize($arrExercises);
						//minor check
						if($strOutput != '' && $categoryId !== 0 && $filterId !== 0){
							//show 
							$filePath = $renderPath.$categoryId.'.'.$filterId.'.data';
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
					unset($rs2, $k4, $v4);
					}
				//clean
				unset($k3, $v3);
				//le all categories
				$strConv = mb_convert_encoding($arrCategoryInfos['name'], 'ISO-8859-1', 'UTF-8');
				$strConv = html_entity_decode($strConv, ENT_QUOTES|ENT_HTML5, 'ISO-8859-1');
				$strFirstLetter = substr($strConv, 0, 1);
				$strFirstLetter = mb_convert_encoding(strtolower($strFirstLetter), 'UTF-8', 'ISO-8859-1');
				$arrAllCategoriesInfos[$arrCategoryInfos['id']] = array(
					'id' => $arrCategoryInfos['id'],
					'letter' => $strFirstLetter,
					'html' => $arrCategoryInfos['html'],
					'href' => $arrCategoryInfos['href'],
					'datetime' => $arrCategoryInfos['datetime'],
					'description' => $v2['description'],
					'locale' => $v,
					);
				}
			}
		//on serialize pour le all categories
		$strOutput = serialize($arrAllCategoriesInfos);
		//minor check
		if($strOutput != ''){
			//show 
			$filePath = $renderPath.'all.data';
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
	unset($rs, $k2, $v2);
	}
//clean
unset($k, $v);


//SCRIPT END