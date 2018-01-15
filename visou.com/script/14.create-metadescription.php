<?php
/**
@auth: Dwizzel
@date: 04-05-2016
@info: creer les description et meta description

@execution:

	B.21. (create-metadescription.php) creer les description et meta description
	
	
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

//STEP B.21 ----------------------------------------------------------------------------------------------------

//minor check
foreach($arrLangByKey as $k=>$v){
	//vars
	$localeId = intVal($k);	
	$arrExercises = array();
	//
	//select from DB des categories
	$query = 'SELECT exercise_id AS "id", description AS "description" FROM exercises WHERE locale = "'.$localeId.'" ORDER BY exercise_id ASC;';
	//show		
	echo $query.EOL.EOL;
	//result set
	$rs = $oReg->get('db-site')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k2=>$v2){
			//associate
			$arrExercises[intVal($v2['id'])] = array(
				'description' => $v2['description'],
				);
			}
		}
	//clean
	unset($rs, $k2, $v2);	
	//filters
	foreach($arrExercises as $k2=>$v2){
		//on va trimmer la description a 150 chars MAX pour le SEO et va etre en chars straight
		//convertir en non html
		$strReducedDescription = metaTextReducer($v2['description']);
		//on insere dans la DB
		$insert = 'UPDATE exercises SET meta_description = "'.$oReg->get('db-site')->escape($strReducedDescription).'" WHERE exercise_id = "'.intVal($k2).'";';
		//show
		echo $insert.EOL.EOL;
		//insert
		$oReg->get('db-site')->query($insert);
		}
	//clean
	unset($arrExercises, $k2, $v2);
	}
//show
echo 'END STEP #B.21'.EOL.EOL;	



//SCRIPT END