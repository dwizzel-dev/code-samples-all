<?php
/**
@auth: Dwizzel
@date: 04-05-2016
@info: va chercher les images sur le serveur, juste les thumbs pour l'instant car on ne veut pas se faire aspirer le site

@execution:

	B.13.  (fetch-images.php) va chercher les images des exercices dans la db et physiquement
	

*/

//EXIT('COMMENT LINE '.__LINE__.' TO RUN'."\n"); //remove to insert data


//-------------------------------------------------------------------------------------------------------------

// FUNCTIONS

function getAndSetContent($file, $id, $title, $suffix, $type){
	//show
	echo 'IMAGE:'.$file.EOL; 
	//link			
	$strLink = PATH_GET_IMAGES.$file;
	//on va chercher
	$fp = fopen($strLink, 'rb');
	if($fp !== false){
		$contents = stream_get_contents($fp);
		fclose($fp);	
		//minor check
		if($contents !== false){
			//on creer le fichier et on ecrit le contenu en binaire dedans
			if($type == 'thumbs'){
				$fp2 = fopen(DIR_IMAGE_THUMBS.$id.'-'.$title.'-'.$suffix.'.jpg', 'a');
			}else if($type == 'pictures'){
				$fp2 = fopen(DIR_IMAGE_PICTURES.$id.'-'.$title.'-'.$suffix.'.jpg', 'a');	
				}
			if($fp2){
				fwrite($fp2, $contents);
				fclose($fp2);
				}
		}else{
			echo 'FAILED: '.$strLink.EOL.EOL;
			}
		}
	//
	return;
	}


//-------------------------------------------------------------------------------------------------------------

//CONTENTTYPE
header('Content-Type: text/plain; charset=utf-8', true);

// ERROR REPORTING
error_reporting(E_ALL);

// BASE DEFINE
require_once('define.php');

// BASE REQUIRED
require_once(DIR_INC.'required.php');

//STEP B.13 ----------------------------------------------------------------------------------------------------

//counter au cas ou plante avant et reprendre avec un LIMIT 0,XXX
$iCmpt = 0;
//on va chercher toutes les keywords que l'on utilise dans la table physiotec_referencement
$query = 'SELECT exercise_id, url_title, thumb_0, thumb_1, pict_0, pict_1 FROM exercises ORDER BY exercise_id ASC;';	
//result set
$rs = $oReg->get('db-site')->query($query);
//minor check
if($rs && $rs->num_rows){
	foreach($rs->rows as $k=>$v){
		//show
		echo EOL.'EXERCISE('.$iCmpt.'): '.$v['exercise_id'].EOL.EOL;
		//show
		if($v['thumb_0'] != ''){
			getAndSetContent($v['thumb_0'], $v['exercise_id'], $v['url_title'], 't0', 'thumbs');
			}
		//show
		if($v['thumb_1'] != ''){
			getAndSetContent($v['thumb_1'], $v['exercise_id'], $v['url_title'], 't1', 'thumbs');
			}
		if($v['pict_0'] != ''){
			getAndSetContent($v['pict_0'], $v['exercise_id'], $v['url_title'], 'p0', 'pictures');
			}
		if($v['pict_1'] != ''){	
			getAndSetContent($v['pict_1'], $v['exercise_id'], $v['url_title'], 'p1', 'pictures');
			}
		
		$iCmpt++;
		}
	}
//clean
unset($rs, $k, $v);
//show
echo 'END STEP #B.13'.EOL.EOL;	



//SCRIPT END