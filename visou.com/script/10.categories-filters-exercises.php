<?php
/**
@auth: Dwizzel
@date: 09-05-2016
@info: script pour l'insertion ds categories-filters-exercises

@execution:

	10. (categories-filters-exercises.php) faire une table relationnelles entre les categories et filters pour les paths (fr_CA et en_US)
		
		a) pour cahque exercices aller chercher sa categorie et ses filtres dans la DB "physiotec" dans la table "mod_exercise"
	
*/

//EXIT('COMMENT LINE '.__LINE__.' TO RUN'."\n"); //remove to insert data

//-------------------------------------------------------------------------------------------------------------

//CONTENTTYPE
header('Content-Type: text/plain; charset=utf-8', true);

// ERROR REPORTING
error_reporting(E_ALL);

// BASE DEFINE
require_once('define.php');

// BASE REQUIRED
require_once(DIR_INC.'required.php');

//STEP B.10 ----------------------------------------------------------------------------------------------------

//minor check
foreach($arrLangByKey as $k=>$v){
	//vars
	$localeId = intVal($k);
	//select from DB
	$query = 'SELECT exercise_id AS "id", ref_id AS "ref_id" FROM exercises WHERE locale = "'.$localeId.'" ORDER BY exercise_id ASC;';	
	//show		
	echo $query.EOL.EOL;
	//result set
	$rs = $oReg->get('db-site')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		//vars
		$arrExercisesCategoriesFilters = array();
		$arrCategories = array();
		$arrFilters = array();			
		//la pair ref_id=>id pour categories et filters	
		$select = 'SELECT category_id AS "category_id", ref_id AS "category_ref_id" FROM categories WHERE locale = "'.$localeId.'" ORDER BY category_id ASC;';
		//show		
		echo $select.EOL.EOL;
		//result set
		$rs2 = $oReg->get('db-site')->query($select);
		if($rs2 && $rs2->num_rows){
			foreach($rs2->rows as $k2=>$v2){
				$arrCategories[intVal($v2['category_ref_id'])] = intVal($v2['category_id']);
				}
			}
		//clean
		unset($rs2, $k2, $v2);
		//la pair ref_id=>id pour categories et filters	
		$select = 'SELECT filter_id AS "filter_id", ref_id AS "filter_ref_id" FROM filters WHERE locale = "'.$localeId.'" ORDER BY filter_id ASC;';
		//show		
		echo $select.EOL.EOL;
		//result set
		$rs2 = $oReg->get('db-site')->query($select);
		if($rs2 && $rs2->num_rows){
			foreach($rs2->rows as $k2=>$v2){
				$arrFilters[intVal($v2['filter_ref_id'])] = intVal($v2['filter_id']);
				}
			}
		//clean
		unset($rs2, $k2, $v2);
		//loop
		foreach($rs->rows as $k2=>$v2){
			//TODO
			$exerciseId = intVal($v2['id']);
			$exerciseRefId = intVal($v2['ref_id']);
			//minor check
			if($exerciseId && $exerciseRefId){
				//on va chercher les filter_id de la DB "physiotec"
				$select = 'SELECT idMod_category, idMod_search_filter FROM mod_exercise WHERE idExercise = "'.$exerciseRefId.'" ORDER BY idMod_category, idMod_search_filter ASC;';
				//show		
				echo $select.EOL.EOL;
				//result set
				$rs2 = $oReg->get('db')->query($select);
				//minor check
				if($rs2 && $rs2->num_rows){
					foreach($rs2->rows as $k3=>$v3){
						//categories
						$categoryId = 0;
						if(isset($arrCategories[$v3['idMod_category']])){
							$categoryId = intVal($arrCategories[$v3['idMod_category']]);
							}
						//filters
						$filterId = 0;
						if(isset($arrFilters[$v3['idMod_search_filter']])){
							$filterId = intVal($arrFilters[$v3['idMod_search_filter']]);
							}
						//minor check
						if($categoryId && $filterId){
							//minor check
							if(!isset($arrExercisesCategoriesFilters[$exerciseId])){
								$arrExercisesCategoriesFilters[$exerciseId] = array();
								}
							//minor check
							if(!isset($arrExercisesCategoriesFilters[$exerciseId][$categoryId])){
								$arrExercisesCategoriesFilters[$exerciseId][$categoryId] = array();
								}	
							//exercises->categories->filters
							if(!in_array($filterId, $arrExercisesCategoriesFilters[$exerciseId][$categoryId])){
								array_push($arrExercisesCategoriesFilters[$exerciseId][$categoryId], $filterId);
								}
							}
						}
					}
				unset($rs2, $k3, $v3);
				}
			}
		//clean
		unset($k2, $v2);
		//on fait l'insertion dans la table
		if(count($arrExercisesCategoriesFilters)){
			//loop des exercises
			foreach($arrExercisesCategoriesFilters as $k2=>$v2){
				//minor check
				if(count($v2)){
					//loop des categories
					foreach($v2 as $k3=>$v3){
						//minor check
						if(count($v3)){
							//loop des categories
							foreach($v3 as $k4=>$v4){
								$exerciseId = intVal($k2);
								$categoryId = intVal($k3);
								$filterId = intVal($v4);
								//minor check
								if($exerciseId && $categoryId && $filterId){
									$insert = 'REPLACE INTO categories_filters_exercises (category_id, filter_id, exercise_id, locale) VALUES("'.$categoryId.'","'.$filterId.'","'.$exerciseId.'","'.$localeId.'");';
									//show		
									echo $insert.EOL.EOL;
									//result set
									$oReg->get('db-site')->query($insert);
									}
								}
							}	
						}
					}
				}
			}			
		}
	//clean
	unset($rs);	
	}
//clean
unset($k, $v);
//show
echo 'END STEP #B.10'.EOL.EOL;		



//SCRIPT END