<?php
/**
@auth: Dwizzel
@date: 03-05-2016
@info: script pour prendre le data de la db et le mettre dans une table
@version: 1.0 BUILD 001
@execution:
	
	A. utiliser la DB "db" "physiotec_dev" sur le serveur de physiotec
		0. (0.start.php) on fait une copie du data essentiel pour le reste des scripts qui feront le clean up et normalization
		
			liste des tables:
				- exercise
				- keyword
				- keyword_exercise
				- keyword_rank
				- mod_category
				- mod_exercise
				- mod_search_filter
				- video
		
			a) on doit importer les donnes de physiotec dans la db de physiotec
				mysql -udwizzel -p physiotec < C:\wamp\www\visou.com\install\db\physiotec.sql
			b) on doit creer les table de la DB de visou
				mysql -udwizzel -p visou < C:\wamp\www\visou.com\install\db\visou.sql
			c) on doit retirer les modules canins 19 DOG Canine, 50 pelvicology	
			
	
	B. utiliser la DB "db" et "db-site" "physiotec_site"
		1. (exercises.php) on va inserer tout les exercices dans la table "exercises" avec un en fr_CA et un en en_US dans la langue correspondante
		2. (categories.php)on va inserer toutes les "categories" (fr_CA et en_US) on prend le en_US de la DB et le le convertit avec un fichier PO
		3. (filters.php) on va inserer toutes les filters (fr_CA et en_US)
		4. (keywords.php) on va inserer toutes les keywords (fr_CA et en_US)
		5. (exercises-categories.php) faire les liens entre exercices et toutes les categories (fr_CA et en_US)
		6. (exercises-filters.php) faire les liens entre exercices et toutes les filters (fr_CA et en_US)
		7. (exercises-keywords.php) faire les liens entre exercices et toutes les keywords (fr_CA et en_US)
		8.1. (data-uniformize.php) standardiser tous les textes en html entities name ou entities numeric, 
			pour ne plus avoir aucun caracteres accentues ou autres
		8.2. (keywords-uniformization.php) trimmer les keywords pour garder ceux valide et assez long
		9. (duplicate-finder.php) trouver les duplicate en garder un et changer les cles correspondantes et vider les exercice qui ne sont plus dans les liens
		10. (categories-filters-exercises.php) faire une table relationnelles entre les categories->filters et exercises pour les paths (fr_CA et en_US)	
		11. (categories-filters.php) faire une table relationnelles entre les categories->filters pour les paths (fr_CA et en_US)	
		12. (create-title.php) creer les titre trimmer et SEO oriented et les meta description ainsi que les description raccouci pour ne pas que l'on vole notre contenu (fr_CA et en_US)
		13. (fetch-images.php) va chercher les images des exercices dans la db et physiquement
		14. (create-exercises.php) creer tout les fichiers des exercises de facon separes dans un repertoire que l'on redistribuera dans les bons repertoires
		15. (create-directories.php) creation desw repertoire /category/filter/
		
		
	C. il faut aussi importe les feuille csv des description de filter et categories avec ces requetes 
		
		UPDATE categories AS T1, (SELECT description, locale, ref_id FROM xls_categories) AS T2, (SELECT locale_id, name FROM locales) AS T3 SET T1.description = T2.description WHERE T3.name = T2.locale AND T2.ref_id = T1.ref_id AND T3.locale_id = T1.locale;

		UPDATE filters AS T1, (SELECT description, locale, ref_id FROM xls_filters) AS T2, (SELECT locale_id, name FROM locales) AS T3 SET T1.description = T2.description WHERE T3.name = T2.locale AND T2.ref_id = T1.ref_id AND T3.locale_id = T1.locale;
		

@command:
	
	.\php C:\wamp\www\visou.com\script\0.start.php

*/

//EXIT('COMMENT LINE '.__LINE__.' TO RUN'."\n");

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
$gChunkLimit = 100;
//excluded modules: 19 Canine, 50 pelvicology
//$arrExcludedModules = array(19,50);
/*
on garde juste ceux-la:

	- amputé fémoral
	- amputé tibial
	- aquatherapy
	- éducation adl
	- bariatrics
	- cardio
	- warmup
	- flexibility
	- orthopédie
	- gériatrie
	- yogafit
	- hands therapy
	- neurologie
	- physiobreathe
	- pediatrie
	- plyometrie
	- pregnancy
	- pilates
	- Strengthening
	- rocktape
	- speeche therapy
	- vestibulaire
	- appi

*/

$arrInclidedModules = array(81,66,11,5,6,74,8,9,14,15,16,17,18,12,84,27,30,33,44,29,54,48,55,51,52);

//$arrExcludedModules = array(83,2,4,13,19,7,68,1,21,23,24,25,26,28,31,32,34,35,36,37,76,75,38,39,42,40,41,87,49,79,73,77,78,10,88,45,46,22,80,50,47,86,53,56,57,59,61,85,60,43,63,64,89,69);

//STEP A.1 ----------------------------------------------------------------------------------------------------

$gBaseQuery = 'SELECT exercise.idExercise AS "exercise_id", exercise.codeExercise AS "exercise_code", exercise.data AS "exercise_data", exercise.rank AS "rank" FROM exercise WHERE exercise.idUser = "0" AND exercise.shared = "1" AND exercise.ready = "1" ORDER BY exercise.idExercise ASC LIMIT ';
while($gContinue){
	//select from DB
	$query = $gBaseQuery.$gLimitStart.','.$gChunkLimit.';';	
	//show		
	echo $query.EOL.EOL;	
	//result set
	$rs = $oReg->get('db')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		foreach($rs->rows as $k=>$v){
			$iExId = intVal($v['exercise_id']);
			$strExerciseCode = $v['exercise_code'];
			$bExcluded = true;
			//on va checker si c'est des modules que l'on doit enlever
			$query = 'SELECT idModule FROM mod_exercise WHERE idModule IN ('.implode(',',array_values($arrInclidedModules)).') AND idExercise = "'.$iExId.'" LIMIT 0,1;';
			//result set
			$rs2 = $oReg->get('db')->query($query);
			if($rs2 && $rs2->num_rows){
				$bExcluded = false;
				}
			//clean	
			unset($rs2);	
			//les filtres que l'on ne veut pas	
			if(!$bExcluded){
				$strVideoCode = '';
				$strVideoName = '';
				$iVideoId = 0;
				//on va aller chercher un viso si il y en a un
				$query = 'SELECT video.embed_code AS "video_code", video.idVideo AS "video_id", video.filename AS "video_name" FROM video WHERE video.idExercise = "'.$iExId.'" LIMIT 0,1;';
				//result set
				$rs2 = $oReg->get('db')->query($query);
				if($rs2 && $rs2->num_rows){
					$strVideoCode = $rs2->row['video_code'];
					$strVideoName = $rs2->row['video_name'];
					$iVideoId = intVal($rs2->row['video_id']);
					}
				//clean	
				unset($rs2);	
				//le ranking
				$iRank = intVal($v['rank']);
				$arrDataByLang = array();	
				$arrPictures = array(
					'0' => array(
						'thumb' => '',
						'pic' => '',
						),
					'1' => array(
						'thumb' => '',
						'pic' => '',
						),
					);
				//dejsonize et on remplace les sauts de ligne par [EOL] que l'on remplacera lus tard en HTML5 par &NewLine;
				$data = $oReg->get('json')->decode(str_replace("\n", "[EOL]", $v['exercise_data']));
				//clean
				unset($v['exercise_data']);
				//check json error if its numeric then it's an error
				if(!is_numeric($data) && is_array($data)){
					//minor check
					if(isset($data['locale'])){
						//on decompose le data et on garde uniquement les langues qui sont valide avec des titre et description valide et pas vide
						foreach($data['locale'] AS $k2=>$v2){
							//minor check
							if(isset($v2['short_title']) && isset($v2['title']) && isset($v2['description'])){
								//minor check
								if(($v2['short_title'].'' != '' || $v2['title'].'' != '') && $v2['description'].'' != ''){
									//lequel des 2 on prend
									if($v2['short_title'].'' != '' && $v2['title'].'' != ''){
										$arrDataByLang[$k2] = array(
											'short_title' => $v2['short_title'],
											'title' => $v2['title'],
											'description' => $v2['description'],
											);
									}else if($v2['short_title'].'' == ''){
										$arrDataByLang[$k2] = array(
											'short_title' => $v2['title'],
											'title' => $v2['title'],
											'description' => $v2['description'],
											);
									}else if($v2['title'].'' == ''){
										$arrDataByLang[$k2] = array(
											'short_title' => $v2['short_title'],
											'title' => $v2['short_title'],
											'description' => $v2['description'],
											);
										}
									}
								}
							}
						}
					//pictures
					$iNumImages = 0;
					foreach($arrPictures as $k2=>$v2){
						foreach($v2 as $k3=>$v3){
							if(isset($data['picture'][$k2][$k3])){
								if($data['picture'][$k2][$k3] != ''){
									$iNumImages++;
									$arrPictures[$k2][$k3] = $data['picture'][$k2][$k3];
									}
								}
							}
						}
					//si le bon nombre d'image et rien de vide
					if($iNumImages >= 4){
						//pictures encoded json
						$strPictures = $oReg->get('json')->encode($arrPictures);
						//check si pas plante lors du encode en json	
						if(!is_numeric($strPictures)){
							//pour chaque langue on fait une insertion differente vu que le data ne sera plus en json data
							foreach($arrDataByLang as $k2=>$v2){
								//creer la requete sql
								$insert = 'INSERT INTO basic_infos (locale, exercise_id, exercise_code, short_title, title, description, pictures, video_code, video_id, video_name, ranking) VALUES("'.$k2.'","'.$iExId.'","'.$strExerciseCode.'","'.$oReg->get('db-site')->escape($v2['short_title']).'","'.$oReg->get('db-site')->escape($v2['title']).'","'.$oReg->get('db-site')->escape($v2['description']).'","'.$oReg->get('db-site')->escape($strPictures).'","'.$oReg->get('db-site')->escape($strVideoCode).'","'.intVal($iVideoId).'","'.$oReg->get('db-site')->escape($strVideoName).'","'.$iRank.'");';
								//show
								echo $insert.EOL.EOL;
								//insere
								$oReg->get('db-site')->query($insert);
								}
						}else{
							//show 
							echo 'JSON ENCODE ERROR ['.$strPictures.'] '.json_last_error_msg().': '.$iExId.EOL;
							}
					}else{
						//show 
						echo 'NO ENOUGHT IMAGES ['.$iNumImages.']: '.$iExId.EOL;
						}
				}else{
					//show 
					echo 'JSON DECODE ERROR ['.$data.'] '.json_last_error_msg().': '.$iExId.EOL;
					}
			}else{
				//show 
				echo 'EXCLUDED: '.$iExId.EOL;
				}
			//clean
			unset($rs2);
			}
	}else{
		$gContinue = false;
		}
	//clean
	unset($rs, $k, $v, $k2, $v2, $k3, $v3);
	//increment	
	$gLimitStart += $gChunkLimit;
	//pour ne pas tout faire	
	if($gLimitStart > $gLimitMax && $gLimitMax > 0){
		$gContinue = false;
		}
	//show
	echo '['.format2Dec(((memory_get_peak_usage()/1024)/1000)).' Mo] LIMIT-START['.$gLimitStart.']'.EOL.EOL;
	//
	}
//show
echo 'END STEP #A.1'.EOL.EOL;	



//SCRIPT END