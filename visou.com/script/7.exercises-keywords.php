<?php
/**
@auth: Dwizzel
@date: 04-05-2016
@info: script pour le lien entre exercice et keywords

@execution:

	B.7.  (exercises-keywords.php) faire les liens entre exercices et toutes les keywords

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

//STEP B.7 ----------------------------------------------------------------------------------------------------

//vars
$rsLastResult = false;
$startExerciseId = 0;
$arrKwEx = array();
$arrExKw = array();
//on va chercher la table de keyword_id => exerceise_id
$query = 'SELECT idKeyword AS "idKeyword", idExercise AS "idExercise" FROM keyword_exercise ORDER BY idKeyword ASC;';
//result set
$rs = $oReg->get('db')->query($query);
//minor check
if($rs && $rs->num_rows){
	foreach($rs->rows as $k=>$v){
		if(!isset($arrExKw[$v['idExercise']])){
			$arrExKw[$v['idExercise']] = array();
			}
		array_push($arrExKw[$v['idExercise']], $v['idKeyword']);	
		//reverse
		if(!isset($arrKwEx[$v['idKeyword']])){
			$arrKwEx[$v['idKeyword']] = array();
			}
		array_push($arrKwEx[$v['idKeyword']], $v['idExercise']);	
		}
	//clean
	unset($k, $v);	
	}
//clean
unset($rs);	
//on va chercher toutes les exercises
$query = 'SELECT exercise_id, ref_id, locale FROM exercises WHERE exercise_id > '.$startExerciseId.' ORDER BY exercise_id ASC;';		
//result set
$rs = $oReg->get('db-site')->query($query);
//minor check
if($rs && $rs->num_rows){
	foreach($rs->rows as $k=>$v){
		//
		$exerciseId = intVal($v['exercise_id']);
		$exerciseRefId = intVal($v['ref_id']);
		if($exerciseId !== 0 && $exerciseRefId != 0 && isset($arrExKw[$exerciseRefId])){
			$query = 'SELECT keyword_id AS "keyword_id" FROM keywords WHERE ref_id IN ('.implode(',',array_values($arrExKw[$exerciseRefId])).')';
			//show
			echo $query.EOL.EOL;
			//result set
			$rs2 = $oReg->get('db-site')->query($query);
			//minor check
			if($rs2 && $rs2->num_rows){
				//loop
				foreach($rs2->rows as $k2=>$v2){
					$keywordId = intVal($v2['keyword_id']);
					//minor check
					if($keywordId !== 0){
						//insert
						$insert = 'REPLACE INTO exercises_keywords (exercise_id, keyword_id) VALUES("'.$exerciseId.'","'.$keywordId.'");';
						//show
						echo $insert.EOL.EOL;
						//result set
						$oReg->get('db-site')->query($insert);
						}
					}
				//clean
				unset($k2, $v2);	
				}
			//clean
			unset($rs2);	
			}
		}
	//clean
	unset($k, $v);	
	}
//clean
unset($rs);
//show
echo 'END STEP #B.7'.EOL.EOL;	



//SCRIPT END