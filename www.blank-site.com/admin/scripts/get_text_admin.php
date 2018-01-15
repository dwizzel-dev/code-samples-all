<?php
/**
@auth:	Dwizzel
@date:	xx-xx-xxxx
@info:	populate hash for _T('***') search base 

IMPORTANT: 	this is a script not a page

*/

// base required
if(!defined('IS_DEFINED')){
	require_once('../define.php');
	}

//required
require_once(DIR_INC.'helpers.php');
require_once(DIR_CLASS.'registry.php');
require_once(DIR_CLASS.'database.php');
require_once(DIR_CLASS.'log.php');

// register new class too the registry to simplify arguments passing to other classes
$reg = new Registry();
$reg->set('log', new Log($reg));
$reg->set('db', new Database(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, $reg));


$arrMatches = array();
$pattern = "/_T\('([a-z0-9A-Z-~.,=?#%^\"`*:<>!\{\}\|\/\[\]\s]*)'\)/";
$basepath = DIR_ADMIN;
$arrPath = array(
	"template\\default\\controller\\",
	"template\\default\\model\\",
	"template\\default\\views\\",
	"template\\default\\widget\\pager\\controller\\",
	"template\\default\\widget\\pager\\views\\",
	"template\\default\\widget\\pagination\\controller\\",
	"template\\default\\widget\\pagination\\model\\",
	"template\\default\\widget\\pagination\\views\\",
	"inc\\",
	"class\\",
	"js\\",
	"",
	);
foreach($arrPath as $k=>$v){
	$arrFiles = scandir($basepath.$v);
	foreach($arrFiles as $k2=>$v2){
		echo 'scaning: '.$basepath.$v.$v2.EOL;
		if($v2 != '.' && $v2 != '..' && is_file($basepath.$v.$v2)){
			$arrMatches[$v.$v2] = array();
			$content = file_get_contents($basepath.$v.$v2);
			preg_match_all($pattern, $content, $arrMatches[$v.$v2]);
			//on swicth le result en premierer place car pas besoin du reste
			$arrMatches[$v.$v2] = $arrMatches[$v.$v2][1];
			if(!count($arrMatches[$v.$v2])){
				unset($arrMatches[$v.$v2]);
				}
			}
		}
	}

foreach($arrMatches as $k=>$v){
	if(is_array($v)){
		foreach($v as $k2=>$v2){
			$str = str_replace('\\', '/', $k);
			$query = 'SELECT '.DB_PREFIX.'site_admin_langue.id FROM '.DB_PREFIX.'site_admin_langue WHERE '.DB_PREFIX.'site_admin_langue.name = "'.sqlSafe($v2).'" LIMIT 0,1;';
			$rs = $reg->get('db')->query($query);
			if(!$rs->num_rows){
				$query = 'INSERT INTO '.DB_PREFIX.'site_admin_langue SET name = "'.sqlSafe($v2).'", name_fr = "'.sqlSafe($v2).'", name_en = "'.sqlSafe($v2).'", page = "/'.sqlSafe($str).'";';
				$reg->get('db')->query($query);
				}
			}		
		}
	}

	
?>