<?php
/**
@auth: Dwizzel
@date: 04-05-2016
@info: script pour l'insertion des keywords/exercise ranking

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

//STEP B.22 ----------------------------------------------------------------------------------------------------

//on va chercher toutes les keywords
$query = 'SELECT keyword_id, locale, ref_id FROM keywords ORDER BY keyword_id ASC;';	
//result set
$rs = $oReg->get('db-site')->query($query);
//minor check
if($rs && $rs->num_rows){
	foreach($rs->rows as $k=>$v){
		//on garde juste ceux plus grand que 2 cars
		$kwId = intVal($v['keyword_id']);
		$localeId = intVal($v['locale']);
		$kwRefId = intVal($v['ref_id']);
		//minor check
		if($kwId != 0 && $localeId != 0 && $kwRefId){
			if(isset($arrLangByKey[$localeId])){
				//on va chercher toutes le rank
				$query = 'SELECT rank FROM keyword_rank WHERE idKeyword = "'.$kwRefId.'" AND locale = "'.$arrLangByKey[$localeId].'" LIMIT 0,1;';	
				//result set
				$rs2 = $oReg->get('db')->query($query);
				//minor check
				if($rs2 && $rs2->num_rows){
					$iRank = intVal($rs2->row['rank']);
					//on insere		
					$query = 'UPDATE keywords SET ranking = "'.$iRank.'" WHERE keyword_id = "'.$kwId.'";';
					//show
					echo $query.EOL.EOL;
					//result set
					$rs3 = $oReg->get('db-site')->query($query);
					//clean
					unset($rs3);
					}
				//clean
				unset($rs2);	
				}
			}
		}
	//clean
	unset($k, $v);	
	}
//clean
unset($rs);
//show
echo 'END STEP #B.22'.EOL.EOL;	



//SCRIPT END