<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	globals var for faster render instead of taking it from the database

*/



$oGlob->set('display_filter_for_dropbox', 
	array(
		array('id'=>'2','text'=>'--'),
		array('id'=>'1','text'=>_T('active')),
		array('id'=>'0','text'=>_T('inactive')),
		)
	);	

$oGlob->set('languages_for_dropbox', 
	array(
		array('id'=>'0','text'=>'--'),
		array('id'=>'1','text'=>'fr'),
		array('id'=>'2','text'=>'en'),
		array('id'=>'3','text'=>'es'),
		)
	);	
	
$oGlob->set('sortdirection_for_dropbox', 
	array(
		array('id'=>'ASC','text'=>_T('ascendant')),
		array('id'=>'DESC','text'=>_T('descendant')),
		)
	);	

$oGlob->set('languages_code_by_prefix', 
	array(
		'fr' => 'fr_CA',
		'en' => 'en_US',
		'es' => 'es_MX',	
		)
	);	
	
$oGlob->set('languages_id_by_code', 
	array(
		'fr_CA' => 1,
		'en_US' => 2,
		'es_MX' => 2,
		)
	);
$oGlob->set('languages_prefix_by_id', 
	array(
		'1' => 'fr',
		'2' => 'en',
		'3' => 'es',
		)
	);	
$oGlob->set('languages_code_by_id', 
	array(
		'1' => 'fr_CA',
		'2' => 'en_US',
		'3' => 'es_MX',
		)
	);
$oGlob->set('colors_for_dropbox', 	
	array(
		'black'=>'#000000',
		'white'=>'#ffffff',
		'red'=>'#ff0000',
		'bleu'=>'#1076BB',
		'grey'=>'#333',
		'green'=>'#00ff00',
		'orange'=>'#FF9300',
		'rose'=>'#FF00FF',
		'bleu-fluo'=>'#00FFFF',
		'bleu-darker'=>'#2668A9',
		'bleu-light'=>'#F1F8FC',
		'grey-light'=>'#eee',
		'blue-links'=>'#0099ff',
		)
	);	
	
	
		
	
	
//END
	
	