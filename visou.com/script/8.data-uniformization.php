<?php
/**
@auth: Dwizzel
@date: 05-05-2016
@info: script pour convertir tout les champs texte de data en format html 

@execution:

	B.8.1 (data-uniformization.php) standardiser tous les textes en html entities name ou entities numeric, pour ne plus avoir aucun caracteres accentues ou autres
	
@command:
	
	C:\wamp\bin\php\php5.5.12> .\php C:\wamp\www\visou.com\script\8.1.data-uniformization.php


@important:

	1. 	attention au double encodage de utf-8 comme dans ce cas ou dans la DB est comme ceci (phpMyAdmin):

			ORIGINAL:
			
			"Protraction scapulaire résistée en décubitus ventral"
		
			"Protraction scapulaire rÃ©sistÃ©e en dÃ©cubitus ventral"

		est en vrai comme cela:

			"Protraction scapulaire r├â┬®sist├â┬®e en d├â┬®cubitus ventral"	
		
		et une fois convertit donne ca
			
			"Protraction scapulaire r&Atilde;&copy;sist&Atilde;&copy;e en d&Atilde;&copy;cubitus ventral"


@paterns:

	a) on va retirer les descriptions vides ou qui contiennent ce pattern ci à la main

		DELETE FROM exercises WHERE 
		short_title LIKE "%Sans titre%" 
		OR 
		short_title LIKE "%Ins&eacute;rez votre texte ici%"
		OR 
		short_title LIKE "%Ins&eacute;rez le nom de l'exercice ici%"
		OR 
		short_title LIKE "%Empty text%"
		OR 
		short_title LIKE "%Insert your text here%"		
		OR 
		short_title LIKE "%Sem titulo%"		
		OR 
		short_title LIKE "%De texto vazio%"	
		OR 
		short_title LIKE "%Texto vac&iacute;o%"		
		OR 
		short_title LIKE "%Sin titulos%"
		OR
		title LIKE "%Sans titre%" 
		OR 
		title LIKE "%Ins&eacute;rez votre texte ici%"
		OR 
		title LIKE "%Ins&eacute;rez le nom de l'exercice ici%"
		OR 
		title LIKE "%Empty text%"
		OR 
		title LIKE "%Insert your text here%"		
		OR 
		title LIKE "%Sem titulo%"		
		OR 
		title LIKE "%De texto vazio%"	
		OR 
		title LIKE "%Texto vac&iacute;o%"		
		OR 
		title LIKE "%Sin titulos%"
		OR
		description LIKE "%Sans titre%" 
		OR 
		description LIKE "%Ins&eacute;rez votre texte ici%"
		OR 
		description LIKE "%Ins&eacute;rez le nom de l'exercice ici%"
		OR 
		description LIKE "%Empty text%"
		OR 
		description LIKE "%Insert your text here%"		
		OR 
		description LIKE "%Sem titulo%"		
		OR 
		description LIKE "%De texto vazio%"	
		OR 
		description LIKE "%Texto vac&iacute;o%"		
		OR 
		description LIKE "%Sin titulos%"
		OR
		CHAR_LENGTH(description) < 60



	b) Ceux avec un "short_title" de la form "PFL00002" "PFL 00002" "XAMT101" on prend le "title" ou ceux trop court

		UPDATE exercises SET short_title = title WHERE short_title REGEXP "^[A-Z]{3,4}[ 0-9]+$"

		UPDATE exercises SET short_title = title WHERE CHAR_LENGTH(short_title) < 5

		UPDATE exercises SET short_title = title WHERE short_title REGEXP "^[0-9]+$"

		- probleme avec les E accent aigu majuscule
		
			SELECT * FROM exercises WHERE title REGEXP "^.*(&Atilde;&copy;)+.*$"

			SELECT * FROM exercises WHERE title REGEXP "^.*(&Atilde;&laquo;)+.*$"
	
			EX: (nl_NL) &Atilde;o&sol;oo&Atilde;&copy;n = &Eacute;&eacute;n = Één

			&Atilde;&micro;	
			&Atilde;&ordm;	
			&Atilde;&shy;	
			&Atilde;&pound;
			&Atilde;o&sol;
			&Atilde;&sect;
			&Atilde;&iexcl;
			&atilde;&nbsp;

			SELECT * FROM exercises WHERE title REGEXP "^.*(&Atilde;&)+.*$" OR short_title REGEXP "^.*(&Atilde;&)+.*$" OR description REGEXP "^.*(&Atilde;&)+.*$"

			Ã©
			Ã§
			Ã£


			&Atilde;&laquo; = ë	= &euml;
	

	c) Delete ceux qui ne sont plus relie

		- faire le cleanup a la main car peu contenir des bons exercices

			SELECT * FROM keywords WHERE name REGEXP "^.*(mitzouko|mayo|webpt|frank|ptholland|bernard|germain).*$"

			SELECT * FROM keywords WHERE locale = "1" AND name REGEXP "^.*(exercici|exercice).*$"

			SELECT * FROM keywords WHERE locale = "2" AND name REGEXP "^.*(exercici|exercise).*$"

			SELECT * FROM keywords WHERE locale = "3" AND name REGEXP "^.*(exercice|exercise).*$"

			SELECT * FROM keywords WHERE name REGEXP "^[0-9]+.*$"
			
			SELECT * FROM keywords WHERE  name REGEXP "^(january|february|march|april|may|june|july|august|september|october|november|december|janvier|fevrier|mars|avril|mai|juin|juillet|aout|septembre|octobre|novembre|decembre)[0-9]{2,}$"

			SELECT * FROM keywords WHERE name REGEXP "^[0-9]+$"

			SELECT * FROM keywords WHERE locale = "1" AND name REGEXP "^.*(&ecirc;|&acirc;|&eacute;|&egrave;|&aacute;|&agrave;).*$"
			
			SELECT * FROM exercises WHERE short_title REGEXP "^.*(&acirc;-).*$"

			SELECT * FROM keywords WHERE name REGEXP "^.*(&Acirc;oe).*$"

			SELECT * FROM keywords WHERE name REGEXP "^.*[-]+.*[-]+.*$" ORDER BY name ASC;

			


		
		- delete direct


			DELETE FROM `keywords` WHERE `name` LIKE '%frank%'

			DELETE FROM `keywords` WHERE `name` LIKE '%webpt%'

			DELETE FROM exercises_keywords WHERE exercise_id NOT IN (SELECT exercise_id FROM exercises)

			DELETE FROM keywords WHERE keyword_id NOT IN (SELECT DISTINCT(keyword_id) FROM exercises_keywords)

			DELETE FROM exercises_keywords WHERE keyword_id NOT IN (SELECT DISTINCT(keyword_id) FROM keywords)
			
			DELETE FROM categories_filters_exercises WHERE exercise_id NOT IN (SELECT exercise_id FROM exercises)

			DELETE FROM exercises_categories WHERE exercise_id NOT IN (SELECT exercise_id FROM exercises)

			DELETE FROM exercises_filters WHERE exercise_id NOT IN (SELECT exercise_id FROM exercises)

			DELETE FROM filters WHERE filters.filter_id NOT IN (SELECT DISTINCT(categories_filters_exercises.filter_id) FROM categories_filters_exercises)

			DELETE FROM categories WHERE categories.category_id NOT IN (SELECT DISTINCT(categories_filters_exercises.category_id) FROM categories_filters_exercises)

			DELETE FROM categories_filters WHERE categories_filters.category_id NOT IN (SELECT DISTINCT(categories_filters_exercises.category_id) FROM categories_filters_exercises)

			DELETE FROM categories_filters WHERE categories_filters.filter_id NOT IN (SELECT DISTINCT(categories_filters_exercises.filter_id) FROM categories_filters_exercises)

			SELECT * FROM categories WHERE category_id NOT IN (SELECT DISTINCT(categories_filters_exercises.category_id) FROM categories_filters_exercises)


		- les mots de la mauvaise langue genre hips en fr_CA
			hip 		hanche
			arm 		bras
			leg 		jambe
			shoulder	epaule épaule	
			knee		genou	
			ankle		cheville
			hand		main	
			finger		doigt
			walk		marche
			feet		pied
			foot		pied
			elbow		coude
			wrist		poignet
			back		dos
			stomach		ventre
			head		t&eacirc;te	
			neck		cou
			nose		nez	
			mouth		bouche
			tongue		langue
			chest		poitrine
			trunk
			thumb

		- mettre en minuscule les keywords, filters et categories
	
			UPDATE keywords SET name = LOWER(name COLLATE UTF8_GENERAL_CI);
			UPDATE filters SET name = LOWER(name COLLATE UTF8_GENERAL_CI);
			UPDATE categories SET name = LOWER(name COLLATE UTF8_GENERAL_CI);	
			
			

		
		

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

// LIMITER
$gContinue = true;
$gLimitMin = 0;
$gLimitMax = 0; //0 = no limit
$gLimitStart = $gLimitMin;
$gChunkLimit = 5000;
$iSleep = 4;

//STEP B.8 ----------------------------------------------------------------------------------------------------

// pour substituer les characters invalid de la function mb_convert_encoding
ini_set('mbstring.substitute_character', 'none');

// QUERIES
$gArrQueries = array(
	//categories-name	
	array(
		'query' => 'SELECT category_id AS "id", name AS "data" FROM categories ORDER BY category_id ASC LIMIT ',	
		'table' => 	'categories',
		'field' => 	'name',
		'id' => 'category_id'
		),
	//categories-description	
	array(
		'query' => 'SELECT category_id AS "id", description AS "data" FROM categories ORDER BY category_id ASC LIMIT ',	
		'table' => 	'categories',
		'field' => 	'description',
		'id' => 'category_id'
		),	
	//filters-name
	array(
		'query' => 'SELECT filter_id AS "id", name AS "data" FROM filters ORDER BY filter_id ASC LIMIT ',	
		'table' => 	'filters',
		'field' => 	'name',
		'id' => 'filter_id'
		),
	//filters-description
	array(
		'query' => 'SELECT filter_id AS "id", description AS "data" FROM filters ORDER BY filter_id ASC LIMIT ',	
		'table' => 	'filters',
		'field' => 	'description',
		'id' => 'filter_id'
		),	
	//keywords
	array(
		'query' => 'SELECT keyword_id AS "id", name AS "data" FROM keywords ORDER BY keyword_id ASC LIMIT ',	
		'table' => 	'keywords',
		'field' => 	'name',
		'id' => 'keyword_id'
		),	
	//exercises-short_title
	array(
		'query' => 'SELECT exercise_id AS "id", short_title AS "data" FROM exercises ORDER BY exercise_id ASC LIMIT ',	
		'table' => 	'exercises',
		'field' => 	'short_title',
		'id' => 'exercise_id'
		),	
	//exercises-title
	array(
		'query' => 'SELECT exercise_id AS "id", title AS "data" FROM exercises ORDER BY exercise_id ASC LIMIT ',	
		'table' => 	'exercises',
		'field' => 	'title',
		'id' => 'exercise_id'
		),	
	//exercises-description
	array(
		'query' => 'SELECT exercise_id AS "id", description AS "data" FROM exercises ORDER BY exercise_id ASC LIMIT ',	
		'table' => 	'exercises',
		'field' => 	'description',
		'id' => 'exercise_id'
		),
	);
	
$arrTableToLower = array('keywords', 'categories', 'filters');	

//on loop dans les arr queries
foreach($gArrQueries as $k=>$v){
	//on reset les compteurs
	$gContinue = true;
	$gLimitStart = $gLimitMin;
	//on fait les query a coup de chunk
	while($gContinue){
		//select from DB
		$query = $v['query'].$gLimitStart.','.$gChunkLimit.';';	
		//show		
		echo $query.EOL.EOL;
		//result set
		$rs = $oReg->get('db-site')->query($query);
		//minor check
		if($rs && $rs->num_rows){
			foreach($rs->rows as $k2=>$v2){
				$dataUTF8 = uniformize($v2['data']);
				if(in_array($v['table'], $arrTableToLower)){
					$dataUTF8 = mb_strtolower($dataUTF8, 'UTF-8');		
					}
				$id = intVal($v2['id']);
				if($dataUTF8 !== false && $dataUTF8 != '' && $id != 0){
					//on remplace tout en html entities html 4.01 qui ne prend les sauts de ligne
					$data = htmlentities($dataUTF8, ENT_HTML401 | ENT_SUBSTITUTE, 'UTF-8', false);
					//les sauts de ligne
					echo $v2['data'].EOL;
					echo $dataUTF8.EOL;
					echo $data.EOL;
					echo EOL.EOL;
					$data = str_replace("\n", '<br />', $data);
					$data = str_replace("&NewLine;", '<br />', $data);
					$data = str_replace("&period;", '.', $data);
					//minor check
					if($data != ''){
						//et on update la DB
						$update = 'UPDATE '.$v['table'].' SET '.$v['field'].' = "'.$oReg->get('db-site')->escape($data).'" WHERE '.$v['id'].' = "'.$id.'";';
						//show
						echo $update.EOL.EOL;
						//on fait le update
						$rs2 = $oReg->get('db-site')->query($update);
						//clean
						unset($rs2);
						}
					}
				}
		}else{
			$gContinue = false;
			}
		//clean
		unset($rs, $k2, $v2);
		//increment	
		$gLimitStart += $gChunkLimit;
		//pour ne pas tout faire	
		if($gLimitStart > $gLimitMax && $gLimitMax > 0){
			$gContinue = false;
			}
		//show
		echo '['.format2Dec(((memory_get_peak_usage()/1024)/1000)).' Mo] LIMIT-START['.$gLimitStart.']'.EOL.EOL;
		}
	}
//clean
unset($k, $v);
//show
echo 'END STEP #B.8'.EOL.EOL;	



//SCRIPT END