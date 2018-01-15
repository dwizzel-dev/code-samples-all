<?php
/**
@auth: Dwizzel
@date: 04-05-2016
@info: script pour l'insertion ds categories en fr_CA et en_US 

@execution:

	2. (categories.php) on va inserer toutes les "categories" (fr_CA et en_US) on prend le en_US de la DB et le le convertit avec un fichier PO

			
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


//STEP B.2 ----------------------------------------------------------------------------------------------------

// hoslder des categories
$gArrCategories = array();

//on va chercher toutes les categories que l'on utilise dans la table physiotec_referencement
$query = 'SELECT idMod_category, title FROM mod_category ORDER BY idMod_category ASC;';	
//result set
$rs = $oReg->get('db')->query($query);
//minor check
if($rs && $rs->num_rows){
	foreach($rs->rows as $k=>$v){
		$gArrCategories[$v['idMod_category']] = $v['title'];	
		}
	}
//clean
unset($rs, $k, $v);
//minor check
if(count($gArrCategories) > 0){
	//loop throught all data categories
	foreach($gArrCategories as $k=>$v){
		//les langues
		foreach($arrLangByKey as $k2=>$v2){	
			//si english
			if($v2 == 'en_US'){
				//query	en_US
				$insert = 'INSERT INTO categories (locale, name, ref_id) VALUES("'.intVal($k2).'","'.$oReg->get('db-site')->escape($v).'","'.intVal($k).'");';	
			}else{
				//ca nous prend les traduction du messages.po convertit en array php 
				$gArrTranslation = poFileToPhpArray(DIR_LANG.'messages.'.$v2.'.po');
				//minor check
				$strName = '';
				if(isset($gArrTranslation[$v])){
					$strName = $gArrTranslation[$v];
					}
				//minor check
				if($strName == ''){
					//on prend en_US par defaut alors
					$strName = $v;
					}
				//minor check
				$insert = 'INSERT INTO categories (locale, name, ref_id) VALUES("'.intVal($k2).'","'.$oReg->get('db-site')->escape($strName).'","'.intVal($k).'");';		
				}
			//echo
			echo $insert.EOL.EOL;
			//insert
			$oReg->get('db-site')->query($insert);
			}
		}
	}
//show
echo 'END STEP #B.2'.EOL.EOL;	



//SCRIPT END