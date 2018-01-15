<?php
/**
@auth: Dwizzel
@date: 06-05-2016
@info: script pour trouver les duplicate name et changer les cle pour n'en garder qu'une seul
@version: 1.0 BUILD 001

@execution:

	9. (duplicate-finder.php) trouver les duplicates en garder un et changer les cles correspondantes et vider les exercice-xxx qui ne sont plus dans les liens, ainsi que les keywords qui ont en bas de 3 caracteres, beaucoup de repetition de keyword car la table de base fonctionnait aussi par license, donc le mot 'hip', 'knee' ou 'flexion' peut se retrouver 50 fois, car pour le site nous ne fonctionnont pas par license et nous ne voulons pas de repetition de mot pour ne pas etre barre par google et autres crawlers. Aussi apres l'uniformisation des donnees des mots peuvent se repeter 
	EX: "repetition", "répétition", "r&eacutep&eacutetition", qui sont tous maintenant sous la forme "r&eacutep&eacutetition". 


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

//STEP B.9 -------------------------------------------------------------------------------------------------

// QUERIES
$gArrQueries = array(
	//categories-name	
	array(
		'query' => 'SELECT category_id AS "id", name AS "data" FROM categories [{WHERE_CLAUSE}] ORDER BY category_id ASC;',	
		'table' => 	'categories',
		'field' => 	'name',
		'id' => 'category_id'
		),	
	//filters-name
	array(
		'query' => 'SELECT filter_id AS "id", name AS "data" FROM filters [{WHERE_CLAUSE}] ORDER BY filter_id ASC;',	
		'table' => 	'filters',
		'field' => 	'name',
		'id' => 'filter_id'
		),
	//keywords-name
	//on va se baser sur le titre pour ne pas avoir de repetiion de fichier "this-is-a-keyname.html"	
	array(
		'query' => 'SELECT keyword_id AS "id", name AS "data" FROM keywords [{WHERE_CLAUSE}] ORDER BY keyword_id ASC;',	
		'table' => 	'keywords',
		'field' => 	'name',
		'id' => 'keyword_id'
		)
	);

//on loop dans les arr queries
foreach($gArrQueries as $k=>$v){
	//la locale where
	foreach($arrLangByKey as $k2=>$v2){
		//vars
		$arrData = array();	
		//select from DB
		$query = $v['query'];
		//on modifie le "WHERE_CLAUSE" selon la locale
		$query = str_replace('[{WHERE_CLAUSE}]', ' WHERE locale = "'.$k2.'" ', $query);	
		//show		
		echo $query.EOL.EOL;
		//result set
		$rs = $oReg->get('db-site')->query($query);
		//minor check
		if($rs && $rs->num_rows){
			//on loop et met ca dans un array data=>(id, id ,id, etc..)	pour savoir les duplicate
			foreach($rs->rows as $k3=>$v3){	
				//show		
				echo 'DATA['.$v['id'].']('.$v3['id'].'):'.$v3['data'].EOL;
				//check si existe avant de rajouter pour le duplicate
				if(!isset($arrData[$v3['data']])){
					$arrData[$v3['data']] = array();	
					}
				//on push
				array_push($arrData[$v3['data']], $v3['id']);
				}
			//clean
			unset($k3, $v3);
			//minor check
			if(is_array($arrData) && count($arrData) > 0){
				//on traite les duplicate en gardant le premier id, les autres vont etre supprimes
				foreach($arrData as $k3=>$v3){
					//garde le first id seulement
					$firstId = array_shift($v3);
					//check si il y en a d'autres
					if(count($v3) > 0){
						//loop dans les ids qui reste
						$strIds = implode(',', $v3);
						//minor check
						if($strIds != ''){
							//on delete le data de la db
							$delete = 'DELETE FROM '.$v['table'].' WHERE '.$v['id'].' IN ('.$strIds.');';
							//show
							echo $delete.EOL.EOL;	
							//result set
							$rs2 = $oReg->get('db-site')->query($delete);
							//show
							echo '[DELETED-0:'.$rs2->affected_rows.']'.EOL.EOL;
							//clean
							unset($rs2, $delete);	
							//on retire les liens exercises-xxx avec les cles que nous avons supprime
							$delete = 'DELETE FROM exercises_'.$v['table'].' WHERE '.$v['id'].' IN ('.$strIds.');';
							//show
							echo $delete.EOL.EOL;	
							//result set
							$rs2 = $oReg->get('db-site')->query($delete);
							//show
							echo '[DELETED-1:'.$rs2->affected_rows.']'.EOL.EOL;
							//clean
							unset($rs2, $delete);
							//la query prend trop de temps alors il faut la faire en php
							$select = 'SELECT DISTINCT('.$v['id'].') AS "ids" FROM exercises_'.$v['table'].';';	
							//show
							echo $select.EOL.EOL;
							//result set
							$rs2 = $oReg->get('db-site')->query($select);
							//minor check
							if($rs2 && $rs2->num_rows){
								//holder des ids a ne pas supprimer
								$strSelectIn = '';
								//loop
								foreach($rs2->rows as $k4=>$v4){
									$strSelectIn .= $v4['ids'].',';
									}
								//check
								if($strSelectIn != ''){
									//show
									echo '[PREPARING-DELETED-2]'.EOL.EOL;
									//la last virgule
									$strSelectIn = substr($strSelectIn, 0, strlen($strSelectIn) - 1);
									//un dernier trim sur le data qui n'est plus relier, table xxx et exercises-xxx
									$triming = 'DELETE FROM '.$v['table'].' WHERE '.$v['id'].' NOT IN('.$strSelectIn.');';
									//show
									//echo $triming.EOL.EOL;
									//result set
									$rs3 = $oReg->get('db-site')->query($triming);
									//show
									echo '[DELETED-2:'.$rs3->affected_rows.']'.EOL.EOL;
									//clean
									unset($rs3, $triming);
									}
								//clean
								unset($k4, $v4, $strSelectIn);
								}
							//clean
							unset($rs2, $select);
							}
						}
					//clean
					unset($strIds, $firstId);
					}
				}
			//clean
			unset($k3, $v3);	
			}
		}
	//clean
	unset($k2, $v2);
	}
//clean
unset($rs, $k, $v);
//on va supprimer tout les exercices relie a rien
$arrIds = array();	
//rajoute les ids
foreach($gArrQueries as $k=>$v){
	//la query prend trop de temps alors il faut la faire en php
	$select = 'SELECT DISTINCT(exercise_id) AS "ids" FROM exercises_'.$v['table'].';';
	//show
	echo $select.EOL.EOL;
	//result set
	$rs = $oReg->get('db-site')->query($select);
	//minor check
	if($rs && $rs->num_rows){
		//loop
		foreach($rs->rows as $k2=>$v2){
			if(!in_array($v2['ids'], $arrIds)){
				array_push($arrIds, $v2['ids']);
				}
			}
		//clean
		unset($k2, $v2);
		}
	unset($rs);
	}
//clean
unset($k, $v);
//check
if(count($arrIds) > 0){
	//vars
	$strSelectIn = '';
	//loop
	foreach($arrIds as $k=>$v){
		$strSelectIn .= $v.',';
		}
	//clean
	unset($k, $v);
	//minor check strip last virgule
	if($strSelectIn != ''){
		//la last virgule
		$strSelectIn = substr($strSelectIn, 0, strlen($strSelectIn) - 1);
		}
	//minor check
	if($strSelectIn != ''){
		//la query
		$delete = 'DELETE FROM exercises WHERE exercise_id NOT IN ('.$strSelectIn.');';
		//show
		echo $delete.EOL.EOL;
		//result set
		$rs = $oReg->get('db-site')->query($delete);
		//show
		echo '[DELETED-3:'.$rs->affected_rows.']'.EOL.EOL;
		//clean
		unset($rs, $delete);
		}
	}
//show
echo 'END STEP #B.9'.EOL.EOL;


//SCRIPT END