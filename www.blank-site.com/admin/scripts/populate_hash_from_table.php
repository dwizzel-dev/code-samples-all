<?php
/**
@auth:	Dwizzel
@date:	xx-xx-xxxx
@info:	populate the hash for faster rendering 

IMPORTANT: 	this is a script not a page

*/

// base required
if(!defined('IS_DEFINED')){
	require_once('../define.php');
	}

//required
require_once(DIR_CLASS.'registry.php');
require_once(DIR_CLASS.'log.php');
require_once(DIR_CLASS.'database.php');
require_once(DIR_CLASS.'utils.php');

// register new class too the registry to simplify arguments passing to other classes
$reg = new Registry();
$reg->set('log', new Log($reg));
$reg->set('db', new Database(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, $reg));
$reg->set('utils', new Utils($reg));


//province--------------------------------------------------------------------------------------------
$strProvince = '$gProvince = array('.EOL;
$arrTmp = $reg->get('utils')->getProvince();
if(is_array($arrTmp)){
	foreach($arrTmp as $k=>$v){
		$strProvince .= TAB.'\''.$v['id'].'\'=>array('.EOL;
		$strProvince .= TAB.'\'name\'=>\''.$v['code'].'\','.EOL;
		$strProvince .= TAB.'\'complete_name\'=>\''.addcslashes($v['name'], "'").'\','.EOL;
		$strProvince .= TAB.'),'.EOL;
		}
	}
$strProvince .= ');'.EOL;	

//province--------------------------------------------------------------------------------------------
$strProvinceByCode = '$gProvinceByCode = array('.EOL;
$arrTmp = $reg->get('utils')->getProvince();
if(is_array($arrTmp)){
	foreach($arrTmp as $k=>$v){
		$strProvinceByCode .= TAB.'\''.$v['code'].'\'=>\''.$v['name'].'\','.EOL;
		}
	}	
$strProvinceByCode .= ');'.EOL;	

//colors--------------------------------------------------------------------------------------------
$strColorsByName = '$gColors = array('.EOL;
$arrTmp = $reg->get('utils')->getColors();
if(is_array($arrTmp)){
	foreach($arrTmp as $k=>$v){
		$strColorsByName .= TAB.'\''.$v['name'].'\'=>\'#'.$v['hex'].'\','.EOL;
		}
	}	
$strColorsByName .= ');'.EOL;	

//languages--------------------------------------------------------------------------------------------
$strLanguages = '$gLanguages = array('.EOL;
$arrTmp = $reg->get('utils')->getLanguages();
if(is_array($arrTmp)){
	foreach($arrTmp as $k=>$v){
		$strLanguages .= TAB.'\''.$v['id'].'\'=>\''.$v['name'].'\','.EOL;
		}
	}	
$strLanguages .= ');'.EOL;	

$strLanguagesCode = '$gLanguagesCode = array('.EOL;
if(is_array($arrTmp)){
	foreach($arrTmp as $k=>$v){
		$strLanguagesCode .= TAB.'\''.$v['code'].'\'=>\''.$v['name'].'\','.EOL;
		}
	}	
$strLanguagesCode .= ');'.EOL;

$strLanguagesPrefix = '$gLanguagesPrefix = array('.EOL;
if(is_array($arrTmp)){
	foreach($arrTmp as $k=>$v){
		$strLanguagesPrefix .= TAB.'\''.$v['prefix'].'\'=>\''.$v['code'].'\','.EOL;
		}
	}	
$strLanguagesPrefix .= ');'.EOL;


// slogan--------------------------------------------------------------------------------------------
$strLocaleLang = '';
$strSlogan = '$gSlogan = array('.EOL;
$arrTmp = $reg->get('utils')->getSlogan();
if(is_array($arrTmp)){
	foreach($arrTmp as $k=>$v){ //par locale
		$strSlogan .= TAB.'"'.quotePhp($k).'" => array('.EOL;
		foreach($v as $k2=>$v2){ //par locale
			$strSlogan .= TAB.TAB.'"'.quotePhp($v2).'",'.EOL;
			}
		$strSlogan .= TAB.'),'.EOL;	
		}
	}	
$strSlogan .= ');'.EOL;	


//write all to hash files-----------------------------------------------------------------------------
$fp = fopen(FILE_GENERATED_HASH, 'w');
if($fp){
	fwrite($fp, '<?php'.EOL);
	//fwrite($fp, $strProvince.EOL);
	//fwrite($fp, $strProvinceByCode.EOL);
	fwrite($fp, $strColorsByName.EOL);
	fwrite($fp, $strLanguages.EOL);
	fwrite($fp, $strLanguagesCode.EOL);
	fwrite($fp, $strLanguagesPrefix.EOL);
	fwrite($fp, $strSlogan.EOL);
	fwrite($fp, '?>');
	fclose($fp);
	}


	
//END script	

