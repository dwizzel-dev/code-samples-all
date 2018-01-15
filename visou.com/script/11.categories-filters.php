<?php
/**
@auth: Dwizzel
@date: 09-05-2016
@info: script pour l'insertion ds categories-filters

@execution:

	11. (categories-filters.php) faire une table relationnelles entre les categories et filters pour les paths (fr_CA et en_US)
		
			

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

//STEP B.11 ----------------------------------------------------------------------------------------------------

//minor check
foreach($arrLangByKey as $k=>$v){
	//vars
	$localeId = intVal($k);
	//select from DB
	$query = 'SELECT category_id AS "category_id", filter_id AS "filter_id" FROM categories_filters_exercises WHERE locale = "'.$localeId.'" ORDER BY category_id ASC;';
	//show		
	echo $query.EOL.EOL;
	//result set
	$rs = $oReg->get('db-site')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		//vars
		$arrCategoriesFilters = array();
		//loop
		foreach($rs->rows as $k2=>$v2){
			//vars
			$categoryId = intVal($v2['category_id']);
			$filterId = intVal($v2['filter_id']);
			//minor check
			if($categoryId && $filterId){
				//create array
				if(!isset($arrCategoriesFilters[$categoryId])){
					$arrCategoriesFilters[$categoryId] = array();
					}
				//minor check
				if(!in_array($filterId, $arrCategoriesFilters[$categoryId])){
					array_push($arrCategoriesFilters[$categoryId], $filterId);
					}
				}

			}
		//clean
		unset($k2, $v2);
		//on fait l'insertion dans la table
		if(count($arrCategoriesFilters)){
			foreach($arrCategoriesFilters as $k2=>$v2){
				//minor check
				if(count($v2)){
					//loop des filters
					foreach($v2 as $k3=>$v3){
						$categoryId = intVal($k2);
						$filterId = intVal($v3);
						//minor check
						if($categoryId && $filterId){
							$insert = 'REPLACE INTO categories_filters (category_id, filter_id, locale) VALUES("'.$categoryId.'","'.$filterId.'","'.$localeId.'");';
							//show		
							echo $insert.EOL.EOL;
							//result set
							$oReg->get('db-site')->query($insert);
							}	
						}
					//clean
					unset($k3, $v3);
					}
				}
			//clean
			unset($k2, $v2);		
			}
		}
	//clean
	unset($rs);	
	}
//clean
unset($k, $v);
//show
echo 'END STEP #B.11'.EOL.EOL;



//SCRIPT END