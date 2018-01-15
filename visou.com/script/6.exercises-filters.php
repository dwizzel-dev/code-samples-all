<?php
/**
@auth: Dwizzel
@date: 04-05-2016
@info: script pour le lien entre exercice et filter

@execution:

	B.6. (exercises-filters.php) faire les liens entre exercices et toutes les filters
		
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

//STEP B.6 ----------------------------------------------------------------------------------------------------

//on va chercher toutes les exercise
$query = 'SELECT exercise_id, ref_id, locale FROM exercises ORDER BY exercise_id ASC;';	
//result set
$rs = $oReg->get('db-site')->query($query);
//minor check
if($rs && $rs->num_rows){
	//loop	
	foreach($rs->rows as $k=>$v){
		//on va chercher dans la table
		$query = 'SELECT DISTINCT(idMod_search_filter) AS "idMod_search_filter" FROM mod_exercise WHERE idExercise = "'.intVal($v['ref_id']).'" ORDER BY idMod_search_filter ASC;';
		//result set
		$rs2 = $oReg->get('db')->query($query);
		//minor check
		if($rs2 && $rs2->num_rows){
			//loop
			foreach($rs2->rows as $k2=>$v2){
				//on va chercher le id de la filter seon la locale aussi
				$query = 'SELECT filter_id FROM filters WHERE ref_id = "'.intVal($v2['idMod_search_filter']).'" AND locale = "'.intVal($v['locale']).'" LIMIT 0,1;';
				//result set
				$rs3 = $oReg->get('db-site')->query($query);
				//minor check
				if($rs3 && $rs3->num_rows){
					//on fait l'insertion
					$insert = 'REPLACE INTO exercises_filters (exercise_id, filter_id) VALUES("'.intVal($v['exercise_id']).'","'.intVal($rs3->row['filter_id']).'");';
					//show
					echo $insert.EOL.EOL;
					//result set
					$oReg->get('db-site')->query($insert);
					}
				//clean
				unset($rs3);
				}
			}
		//clean
		unset($rs2, $k2, $v2);
		}
	}
//clean
unset($rs, $k, $v);
//show
echo 'END STEP #B.6'.EOL.EOL;	



//SCRIPT END