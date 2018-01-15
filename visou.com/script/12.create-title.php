<?php
/**
@auth: Dwizzel
@date: 04-05-2016
@info: creer les titre trimmer et SEO oriented

@execution:

	12. (create-title.php) creer les titre trimmer et SEO oriented (fr_CA et en_US), ainsi que la description à 150 characteres
	
	
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

//STEP B.12 ----------------------------------------------------------------------------------------------------

//le nombre de chars de la description, pas trop pour etre certain de ne pas se faire aspirer le site et tenir compte de google
$iMaxDescriptionCharsForPage = 200;
$iMaxDescriptionCharsForMeta = 150;

//minor check
foreach($arrLangByKey as $k=>$v){
	//vars
	$localeId = intVal($k);	
	$arrCategories = array();
	$arrFilters = array();
	$arrExercises = array();
	$arrKeywords = array();
	//	
	//select from DB des categories
	$query = 'SELECT category_id AS "id", name AS "name" FROM categories WHERE locale = "'.$localeId.'" ORDER BY category_id ASC;';
	//show		
	echo $query.EOL.EOL;
	//result set
	$rs = $oReg->get('db-site')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k2=>$v2){
			//associate
			$arrCategories[intVal($v2['id'])] = $v2['name'];
			}
		}
	//clean
	unset($rs, $k2, $v2);
	//maintenant on modifie les noms pour des url title et on les insere dans la db qui serviront a creer nos repertoires de referencement
	//categories
	foreach($arrCategories as $k2=>$v2){
		//convertir les name en clean URL
		$strCleanUrl = cleanUrl($v2, $arrLangByKey[$localeId], ENT_HTML5); //func dans inc/helpers.php
		//double check
		if(strlen($strCleanUrl) <= 2){
			//on reessaye
			$strCleanUrl = 'category-'.$k2; //func dans inc/helpers.php
			}
		//on insere dans title
		$insert = 'UPDATE categories SET title = "'.$oReg->get('db-site')->escape($strCleanUrl).'" WHERE category_id = "'.intVal($k2).'";';	
		//show
		echo $insert.EOL.EOL;
		//insert
		$oReg->get('db-site')->query($insert);
		}
	//clean
	unset($arrCategories, $k2, $v2);
	//
	//select from DB des categories
	$query = 'SELECT filter_id AS "id", name AS "name" FROM filters WHERE locale = "'.$localeId.'" ORDER BY filter_id ASC;';
	//show		
	echo $query.EOL.EOL;
	//result set
	$rs = $oReg->get('db-site')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k2=>$v2){
			//associate
			$arrFilters[intVal($v2['id'])] = $v2['name'];
			}
		}
	//clean
	unset($rs, $k2, $v2);	
	//filters
	foreach($arrFilters as $k2=>$v2){
		//convertir les name en clean URL
		$strCleanUrl = cleanUrl($v2, $arrLangByKey[$localeId], ENT_HTML5); //func dans inc/helpers.php
		//double check
		if(strlen($strCleanUrl) <= 2){
			//on reessaye
			$strCleanUrl = 'filter-'.$k2; //func dans inc/helpers.php
			}
		//on insere dans title
		$insert = 'UPDATE filters SET title = "'.$oReg->get('db-site')->escape($strCleanUrl).'" WHERE filter_id = "'.intVal($k2).'";';	
		//show
		echo $insert.EOL.EOL;
		//insert
		$oReg->get('db-site')->query($insert);
		}
	//clean
	unset($arrFilters, $k2, $v2);
	//
	//select from DB des categories
	$query = 'SELECT exercise_id AS "id", short_title AS "short_title", title AS "title" FROM exercises WHERE locale = "'.$localeId.'" ORDER BY exercise_id ASC;';
	//show		
	echo $query.EOL.EOL;
	//result set
	$rs = $oReg->get('db-site')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k2=>$v2){
			//associate
			$arrExercises[intVal($v2['id'])] = array(
				'title' => $v2['title'],
				'short_title' => $v2['short_title'],
				);
			}
		}
	//clean
	unset($rs, $k2, $v2);	
	//filters
	foreach($arrExercises as $k2=>$v2){
		//convertir les name en clean URL pour le SEO
		$strCleanUrl = cleanUrl($v2['short_title'], $arrLangByKey[$localeId], ENT_HTML5); //func dans inc/helpers.php
		//certain peuvent etre vide alors on reprend avec le titre qui est plus long
		if(strlen($strCleanUrl) <= 2){
			//on reessaye
			$strCleanUrl = cleanUrl($v2['title'], $arrLangByKey[$localeId], ENT_HTML5); //func dans inc/helpers.php
			}
		//double check
		if(strlen($strCleanUrl) <= 2){
			//on change manuellement
			$strCleanUrl = 'exercise-'.$k2; //func dans inc/helpers.php
			}
		//on insere dans la DB
		$insert = 'UPDATE exercises SET url_title = "'.$oReg->get('db-site')->escape($strCleanUrl).'" WHERE exercise_id = "'.intVal($k2).'";';
		//show
		echo $insert.EOL.EOL;
		//insert
		$oReg->get('db-site')->query($insert);
		}
	//clean
	unset($arrExercises, $k2, $v2);
	//	
	//select from DB des categories
	$query = 'SELECT keyword_id AS "id", name AS "name" FROM keywords WHERE locale = "'.$localeId.'" ORDER BY keyword_id ASC;';
	//show		
	echo $query.EOL.EOL;
	//result set
	$rs = $oReg->get('db-site')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k2=>$v2){
			//associate
			$arrKeywords[intVal($v2['id'])] = $v2['name'];
			}
		}
	//clean
	unset($rs, $k2, $v2);
	//maintenant on modifie les noms pour des url title et on les insere dans la db qui serviront a creer nos repertoires de referencement
	//categories
	foreach($arrKeywords as $k2=>$v2){
		//convertir les name en clean URL
		$strCleanUrl = cleanUrl($v2, $arrLangByKey[$localeId], ENT_HTML5); //func dans inc/helpers.php
		//double check
		if(strlen($strCleanUrl) <= 2){
			//on reessaye
			$strCleanUrl = 'keyword-'.$k2; //func dans inc/helpers.php
			}
		//on insere dans title
		$insert = 'UPDATE keywords SET title = "'.$oReg->get('db-site')->escape($strCleanUrl).'" WHERE keyword_id = "'.intVal($k2).'";';	
		//show
		echo $insert.EOL.EOL;
		//insert
		$oReg->get('db-site')->query($insert);
		}
	//clean
	unset($arrKeywords, $k2, $v2);	
	}
//show
echo 'END STEP #B.12'.EOL.EOL;	



//SCRIPT END