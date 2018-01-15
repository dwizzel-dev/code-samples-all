<?php
/**
@auth: Dwizzel
@date: 04-05-2016
@info: script pour l'insertion ds keywords

@execution:

	3. (keywords.php) on va inserer toutes les "keywords"

		
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

//STEP B.4 ----------------------------------------------------------------------------------------------------

//on va chercher toutes les keywords
$query = 'SELECT idKeyword, keyword, locale FROM keyword WHERE idLicence = 0 AND kwtype = 1 ORDER BY idKeyword;';	
//result set
$rs = $oReg->get('db')->query($query);
//minor check
if($rs && $rs->num_rows){
	foreach($rs->rows as $k=>$v){
		//on garde juste ceux plus grand que 2 cars
		$strName = trim($v['keyword']);
		//minor check
		if($strName != '' && strlen($strName) >= 2 && isset($arrLangByValue[$v['locale']])){
			//on ne va pas garder ceux qui sont comme app125980
			if(!preg_match('/^[a-z]{2,4}[0-9]{4,}$/', $strName) && !preg_match('/^[0-9]{1,}$/', $strName)){ 
				//insert
				$insert = 'INSERT INTO keywords (locale, name, ref_id) VALUES("'.intVal($arrLangByValue[$v['locale']]).'","'.$oReg->get('db-site')->escape($strName).'","'.intVal($v['idKeyword']).'");';		
				//echo
				echo $insert.EOL.EOL;
				//insert
				$oReg->get('db-site')->query($insert);
				}
			}
		}
	}
//clean
unset($rs, $k, $v);
//show
echo 'END STEP #B.4'.EOL.EOL;	



//SCRIPT END