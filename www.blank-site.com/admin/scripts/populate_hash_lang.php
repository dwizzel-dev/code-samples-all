<?php
/**
@auth:	Dwizzel
@date:	xx-xx-xxxx
@info:	populate the hash for faster rendering 



*/

// base required
if(!defined('IS_DEFINED')){
	require_once('../define.php');
	}

//required
require_once(DIR_CLASS.'registry.php');
require_once(DIR_CLASS.'database.php');
require_once(DIR_CLASS.'log.php');
require_once(DIR_CLASS.'utils.php');

// register new class too the registry to simplify arguments passing to other classes
$oReg = new Registry();
$oReg->set('log', new Log($oReg));
$oReg->set('db', new Database(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, $oReg));
$oReg->set('utils', new Utils($oReg));

//langue
$arrLangue = array(
	'en_US'=>'en', 
	'fr_CA' => 'fr',
	'es_MX' => 'es',
	);
//text
$arrText = $oReg->get('utils')->getSiteText();
//build
if(is_array($arrText)){
	foreach($arrLangue as $k=>$v){	
		$str = '';
		foreach($arrText as $k2=>$v2){
			$str .= '\''.$v2['name'].'\'=>\''.addcslashes($v2['name'.'_'.$v], "'").'\','.EOL;
			}
		if($str != ''){
			$str = substr($str, 0, strlen($str) - 1);
			}
		$str = '$gArrText = array('.EOL.$str.EOL.');'.EOL;	
		//write all to hash files
		$fp = fopen(DIR_GENERATE_LANG.'lang.'.$k.'.php', 'w');
		if($fp){
			fwrite($fp, '<?php'.EOL);
			fwrite($fp, $str.EOL);
			fwrite($fp, '?>');
			fclose($fp);
			}
		}	
	}	



	
	
	
	
	
//end script	


















?>