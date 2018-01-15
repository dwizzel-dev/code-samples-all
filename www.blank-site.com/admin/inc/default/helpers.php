<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	helpers functions used everywhere by the site

*/


//---------------------------------------------------------------------------------------------------------------

function _T($text, $display = false){
	global $gArrText;
		
	if($text == '' || !isset($gArrText)){
		if($display){
			echo '~'.$text.'~';
		}else{
			return '~'.$text.'~';
			}
	}else{
		if(!isset($gArrText[$text])){
			if($display){
				echo '~'.$text.'~';
			}else{
				return '~'.$text.'~';
				}
		}else{
			if($display){
				echo rteSafeReverse($gArrText[$text]);
			}else{
				return rteSafeReverse($gArrText[$text]);
				}
			}
		}
	}

//------------------------------------------------------------------------------------------
//thw way the message is build for the client javascript application	
function buildAjaxMessage(&$oReg, $data, $errors = '', $formerrors = ''){
	$arr = array(
		'section' => $oReg->get('req')->get('section'),
		'service' => $oReg->get('req')->get('service'),
		//'sessid' => $oReg->get('req')->get('PHPSESSID'),	
		'msgerrors' => $errors,
		'formerrors' => $formerrors,
		'msgdata' => $oReg->get('json')->encode($data),
		);
	//extra data for performance and sql load
	$arr['usage'] = format2Dec(((memory_get_peak_usage()/1024)/1000)).' Mo';	
	//sql num of queries
	$iNumQ = 0;
	//db general
	$db = $oReg->get('db');
	if(isTrue($db)){
		if($oReg->get('db')->getStatus()){
			$iNumQ += $oReg->get('db')->getQueriesNum();
			}
		}
	//db external	
	$dbExt = $oReg->get('db-ext');
	if(isTrue($dbExt)){
		if($oReg->get('db-ext')->getStatus()){
			$iNumQ += $oReg->get('db-ext')->getQueriesNum();
			}
		}	
	$arr['sql'] = $iNumQ;	
	//ip adress
	$arr['ip'] = $oReg->get('req')->get('ip');			
	//
	return $arr;
	}
	
//------------------------------------------------------------------------------------------	
function isTrue($var){
	if(!isset($var)){
		return false;	
		}
	if(is_bool($var) === true){
		return (bool)$var;
		}
	if(is_string($var) === true){
		return true;
		}	
	if($var === 0 || $var === -1){
		return false;	
		}
	if($var === false){
		return false;	
		}
	return true;
	}


//---------------------------------------------------------------------------------------------------------------
function quoteHtml($str){
	$str = stripslashes($str);	
	$str = addcslashes($str, '"');	
	return $str;	
	}

//---------------------------------------------------------------------------------------------------------------
function formatJavascript($result){
	$result = stripslashes($result);
	$result = addcslashes($result, "'");
	
	//IMPORATNT CHECK SI FONCTIONNEPARTOUT ENCORE
	//Dwizzel 03-10-2012 23:17
	//$result = htmlspecialchars($result, ENT_COMPAT, 'UTF-8');
	
	
	
	//mais on reconvertit kesa <br /> en \n qui sont maintenant des "&lt;br /&gt;"
	$result =  str_replace('&lt;br /&gt;', "\\n", $result);
	//$result =  str_replace("\r\n", '%', $result);
	$result =  str_replace("\r", "", $result);//les carriage return des formulaire et windows \r\f
	
	return $result;
	}	
	
//------------------------------------------------------------------------------------------	

function rteSafeReverse($strText) {
	$tmpString = $strText; 
	$tmpString = str_replace('&amp;','&', $tmpString); 
	$tmpString = str_replace('&lt;','<', $tmpString); 
	$tmpString = str_replace('&gt;','>',  $tmpString); 
	$tmpString = str_replace('&quot;', '"', $tmpString); 
	$tmpString = str_replace("\r\n", "<br />", $tmpString); //etait en dernier 01-07-2013
	$tmpString = str_replace(chr(10), "<br />", $tmpString); 
	$tmpString = str_replace(chr(13), "<br />", $tmpString); 
	//$tmpString = str_replace("\n", "<br />", $tmpString);
	//$tmpString = str_replace("\r", "<br />", $tmpString);
	//
	return $tmpString;
	}
	
//------------------------------------------------------------------------------------------	

function safeReverse($strText) {
	return str_replace(array('&amp;','&lt;','&gt;','&quot;'),array('&','<','>','"'), $strText); 
	}	

//---------------------------------------------------------------------------------------------------------------	

function number_pad($number, $n){
	return str_pad((int)$number, $n, "0", STR_PAD_LEFT);
	}	

//---------------------------------------------------------------------------------------------------------------	
	
function replaceNewLineByBR($str){
	return str_replace("\n", '<br />', $str);
	}
	
//---------------------------------------------------------------------------------------------------------------	
	
function replaceSpaceByNBSP($str){
	return str_replace(" ", '&nbsp;', $str);
	}	
	
//---------------------------------------------------------------------------------------------------------------	
	
function formatHrefLink($str){
	$str = str_replace("&lt;", '<', $str);
	$str = str_replace("&quot;", '"', $str);
	$str = str_replace("&gt;", '>', $str);
	return $str;
	}	

//---------------------------------------------------------------------------------------------------------------	
	
function add_date($givendate, $day=0, $mth=0, $yr=0){
	$cd = strtotime($givendate);
	$newdate = date('d-m-Y', mktime(date('h', $cd), date('i',$cd), date('s',$cd), date('m',$cd)+$mth, date('d',$cd)+$day, date('Y',$cd)+$yr));
	return $newdate;
	}
	
//---------------------------------------------------------------------------------------------------------------
	
function addHoursToDate($date, $hours){
		return date("Y-m-d H:i:s", strtotime($date) + ((60*60) * $hours));
		}		


//------------------------------------------------------------------------------------------------------------	
	
function format2dec($num){
		$num = round($num,2);
		return number_format($num, 2, '.', '');
		}

//------------------------------------------------------------------------------------------------------------
function getSubLink(){
	global $oGlob;		
	$link = $oGlob->getArray('links', 'collection-'.$oGlob->get('subpage'));
	$arrLink = explode('/', $link);
	$sublink = '';
	for($j=0;$j<(count($arrLink)-1);$j++){
		if($arrLink[$j] != ''){
			$sublink .= '/'.$arrLink[$j]; 
			}
		}
			
	return array($sublink, $arrLink[$j]);
	}
	
//---------------------------------------------------------------------------------------------------------------
function getImagePath($path, $type){
	$iLastPos = strrpos($path, '/');
	$strFirstPath = substr($path, 0, $iLastPos);
	$strLastPath = substr($path, $iLastPos, strlen($path));
	$strImagePath = $strFirstPath.'/'.$type.'/'.$strLastPath; 
	if(!is_file(DIR_MEDIA.$strImagePath)){
		$strImagePath = $type.'/'.DEFAULT_NO_IMAGE;
		}
	return $strImagePath;		
	}	
	
//-------------------------------------------------------	

//For english caharacters only
function cleanUrl($str, $separator = '-', $lowercase = true){
	if ($separator == 'dash'){
		$separator = '-';
	}else if ($separator == 'underscore'){
		$separator = '_';
		}
	$q_separator = preg_quote($separator);
	$trans = array(
		'&.+?;'                 => '',
		'[^a-z0-9 _-]'          => '',
		'\s+'                   => $separator,
		'('.$q_separator.')+'   => $separator
		);
	$str = strip_tags($str);
	foreach ($trans as $key => $val){
		$str = preg_replace("#".$key."#i", $val, $str);
		}
	if($lowercase === true){
		$str = strtolower($str);
		}
	return trim($str, $separator);
	}	
	
	
//-------------------------------------------------------

function cleanAlias($str){	
	$str = preg_replace("/'/", "-", $str); 
	$str = preg_replace('/"/', "-", $str);
	$str = preg_replace("/à/", "a", $str);
	$str = preg_replace("/ä/", "a", $str);
	$str = preg_replace("/â/", "a", $str);
	$str = preg_replace("/é/", "e", $str);
	$str = preg_replace("/ê/", "e", $str);
	$str = preg_replace("/è/", "e", $str);
	$str = preg_replace("/ë/", "e", $str);
	$str = preg_replace("/ï/", "i", $str);
	$str = preg_replace("/î/", "i", $str);
	$str = preg_replace("/ì/", "i", $str);
	$str = preg_replace("/ü/", "u", $str);
	$str = preg_replace("/ç/", "c", $str);
	$str = preg_replace("/ô/", "o", $str);
	$str = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
	$str = strtolower(trim($str, '-'));
	$str = preg_replace("/[_|+ -]+\//", '-', $str);
	$str = preg_replace("/[\s-]+/", "-", $str); 
	return $str;
	}	
	
//-------------------------------------------------------

function cleanPath($str){	
	$str = preg_replace("/'/", "-", $str); 
	$str = preg_replace('/"/', "-", $str);
	$str = preg_replace("/à/", "a", $str);
	$str = preg_replace("/ä/", "a", $str);
	$str = preg_replace("/â/", "a", $str);
	$str = preg_replace("/é/", "e", $str);
	$str = preg_replace("/ê/", "e", $str);
	$str = preg_replace("/è/", "e", $str);
	$str = preg_replace("/ë/", "e", $str);
	$str = preg_replace("/ï/", "i", $str);
	$str = preg_replace("/î/", "i", $str);
	$str = preg_replace("/ì/", "i", $str);
	$str = preg_replace("/ü/", "u", $str);
	$str = preg_replace("/ç/", "c", $str);
	$str = preg_replace("/ô/", "o", $str);
	$str = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
	$str = strtolower(trim($str, '-'));
	$str = preg_replace("/[_|+ -]+/", '-', $str);
	return $str;
	}
	
//-------------------------------------------------------

function cleanKeyIndex($str){	
	$str = preg_replace("/'/", "-", $str); 
	$str = preg_replace('/"/', "-", $str);
	$str = preg_replace("/à/", "a", $str);
	$str = preg_replace("/ä/", "a", $str);
	$str = preg_replace("/â/", "a", $str);
	$str = preg_replace("/é/", "e", $str);
	$str = preg_replace("/ê/", "e", $str);
	$str = preg_replace("/è/", "e", $str);
	$str = preg_replace("/ë/", "e", $str);
	$str = preg_replace("/ï/", "i", $str);
	$str = preg_replace("/î/", "i", $str);
	$str = preg_replace("/ì/", "i", $str);
	$str = preg_replace("/ü/", "u", $str);
	$str = preg_replace("/ç/", "c", $str);
	$str = preg_replace("/ô/", "o", $str);
	$str = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
	$str = strtolower(trim($str, '-'));
	$str = preg_replace("/[_|+ -]+/", '-', $str);
	return $str;
	}	
	
//-------------------------------------------------------

function cleanUrlTitle($str){
    //üéâàçêëèïîì
	// invalid chars, make into spaces
    $str = preg_replace("/^a-z0-9\s-/", "", $str);  
	// char french
	$str = preg_replace("/à/", "a", $str);
	$str = preg_replace("/ä/", "a", $str);
	$str = preg_replace("/â/", "a", $str);
	$str = preg_replace("/é/", "e", $str);
	$str = preg_replace("/ê/", "e", $str);
	$str = preg_replace("/è/", "e", $str);
	$str = preg_replace("/ë/", "e", $str);
	$str = preg_replace("/ï/", "i", $str);
	$str = preg_replace("/î/", "i", $str);
	$str = preg_replace("/ì/", "i", $str);
	$str = preg_replace("/ü/", "u", $str);
	$str = preg_replace("/ç/", "c", $str);
	$str = preg_replace("/ô/", "o", $str);
	/*
	DWIZZEL: 19-11-2012
	IMPORTANT A REMETTRE
	
	$str = preg_replace("/&amp;/", "-and-", $str);
	$str = preg_replace("/&/", "-and-", $str);
	
	*/
	if($oGlob->get('lang_prefix') == 'fr'){
		$str = preg_replace("/&amp;/", "-et-", $str);
		$str = preg_replace("/&/", "-et-", $str);
	}else{
		$str = preg_replace("/&amp;/", "-and-", $str);
		$str = preg_replace("/&/", "-and-", $str);
		}
	/*
	DWIZZEL END MODIFS 19-11-2012
	*/	
		
	$str = preg_replace("/&quot;/", "-", $str);
	$str = preg_replace("/'/", "-", $str); 
	$str = preg_replace('/"/', "-", $str);
	$str = preg_replace("/[\s-]+/", "-", $str); 
	$str = preg_replace(array('/ü/','/é/','/â/','/à/','/ç/','/ê/','/ë/','/è/','/ï/','/î/','/ì/'), array('u','e','a','a','c','e','e','e','i','i','i'), $str);
	$str = strtolower($str);
    return $str;
	}
	
//-------------------------------------------------------

function cleanUploadFileName($str){
    //
	//$str = strtolower($str);
    // invalid chars, make into spaces
    $str = preg_replace("/^a-z0-9\s-/", "", $str);  
	$str = preg_replace("/à/", "a", $str);
	$str = preg_replace("/â/", "a", $str);
	$str = preg_replace("/é/", "e", $str);
	$str = preg_replace("/ê/", "e", $str);
	$str = preg_replace("/è/", "e", $str);
	$str = preg_replace("/ë/", "e", $str);
	$str = preg_replace("/ï/", "i", $str);
	$str = preg_replace("/î/", "i", $str);
	$str = preg_replace("/ì/", "i", $str);
	$str = preg_replace("/ü/", "u", $str);
	$str = preg_replace("/ç/", "c", $str);
	$str = preg_replace("/&amp;/", "-and-", $str);
	$str = preg_replace("/&quot;/", "-", $str);
	$str = preg_replace("/&/", "-and-", $str);
	$str = preg_replace("/'/", "-", $str); 
	$str = preg_replace('/"/', "-", $str);
    // convert multiple spaces/hyphens into one space       
    $str = preg_replace("/\s-+/", "", $str); 
    return $str;
	}	
	
//---------------------------------------------------------------------------------------------------------------
function sqlSafe($str){
	global $oReg;
	if(isset($oReg)){
		return $oReg->get('db')->escape($str);
		}
	return $str;
	}	
	
//---------------------------------------------------------------------------------------------------------------	
function hexstr($hexstr) {
	$hexstr = str_replace(' ', '', $hexstr);
	$hexstr = str_replace('\x', '', $hexstr);
	$retstr = pack('H*', $hexstr);
	return $retstr;
	}
	
//---------------------------------------------------------------------------------------------------------------		
function formatCanPostMessage($str){
	$str = preg_replace(array("/&amp;#xE9;/","/&amp;#xE0;/","/&amp;#xE8;/","/&amp;#xE7;/","/&amp;#xE2;/"), array('é','à','è','ç','â'), $str); 
	return $str;
	}

//---------------------------------------------------------------------------------------------	
function frenchUcfirst($v) { 
	$lowCase  = "\\xE0\\xE1\\xE2\\xE3\\xE4\\xE5\\xE7\\xE8\\xE9\\xEA\\xEB\\xEC\\xED\\xEE\\xEF"; 
	$lowCase .= "\\xF1\\xF2\\xF3\\xF4\\xF5\\xF6\\xF8\\xF9\\xFA\\xFB\\xFC\\xFD\\xFF\\u0161"; 
	$upperCase = "AAAAAA\\xC7EEEEIIIINOOOOOOUUUUYYS"; 
	return strtoupper(strtr(substr($v, 0, 1), $lowCase, $upperCase)) . substr($v, 1); 
	} 	
	
//---------------------------------------------------------------------------------------------	
function calculateTps($total){
	$prctTps = TPS;
	$tps = $total * $prctTps;
	return $tps;
	}

//---------------------------------------------------------------------------------------------	
function calculateTvq($total){
	$prctTvq = TVQ;
	$tvq = $total * $prctTvq;
	return $tvq;
	}	
	
//---------------------------------------------------------------------------------------------
function CanToUsConvertion($total){
	//get the currency change for the day in the database
	$currency = 0.68;
	return $currency * $total;;
	}

//---------------------------------------------------------------------------------------------	
function UsToCanConvertion($total){
	//get the currency change for the day in the database
	$currency = 1.4705882352941176470588235294118;
	return $currency * $total;;
	}	

//---------------------------------------------------------------------------------------------	
function writeNewFile($filename, $content){
	$fh = fopen($filename, 'w');
	if($fh){
		fwrite($fh, $content);
		fclose($fh);
		}
	}		

//---------------------------------------------------------------------------------------------	
function recursiveShow($arr, $spacer, $str){
	foreach($arr as $k=>$v){
		if(is_array($v)){
			$str .= $spacer.'['.$k.']';
			$str = recursiveShow($v, $spacer.'&nbsp;&nbsp;&nbsp;&nbsp;', $str);
		}else{
			$str .= $spacer.'["'.$k.'"] = "'.encodeArrowTag($v).'"';
			}
		}
	return $str;	
	}	

//---------------------------------------------------------------------------------------------	
//remplace les code html par  < et >
function decodeArrowTag($str){	
	return str_replace(array('&gt;','&lt;'), array('>','<'), $str);
	}
	
//---------------------------------------------------------------------------------------------		
//remplace les < et > par des codes html	
function encodeArrowTag($str){	
	return str_replace(array('>','<'), array('&gt;','&lt;'), $str);
	}

	
//---------------------------------------------------------------------------------------------		
function strAfterNeedle($str, $needle) {
    $pos = strrpos($str, $needle);
    if($pos===false) {
        return false;
    }else{
        return substr($str, ($pos+1));
		}
	}

//---------------------------------------------------------------------------------------------

// THIS SERIALIZE AND UNSERIALIZE HTML CONTENT GENERALLY MADE WITH TINYMCE AND PASSED BY JSON


function unserializeFromDbData($data){
	return unserialize(str_replace('&quot;','"',$data)); 
	}
	
function formatSerialize(&$str, $strKey){
    $str = str_replace(array('>','<','&',"'"), array('[gt;]','[lt;]','[amp;]',"[sinquote;]"), $str);
	}
    
function formatSerializeRev(&$str, $strKey){
	$str = str_replace(array('[gt;]','[lt;]','[amp;]',"[sinquote;]"), array('&gt;','&lt;','&',"'"), $str);
	}	
	
	
//---------------------------------------------------------------------------------------------	
function setCookies($key, $value, $time = 0){
	if($time){
		$time = time() + $time;
		setcookie($key, $value, $time);
	}else{
		setcookie($key, $value);
		}
	}
	
//---------------------------------------------------------------------------------------------	
function getCookies($key){
	if(isset($key) && isset($_COOKIE[$key])){
		return $_COOKIE[$key];
		}
	return false;	
	}		
	
//---------------------------------------------------------------------------------------------		
function createZipFile($basepath, $files = array(), $destination = '', $overwrite = false) {
	if(file_exists($destination) && !$overwrite){
		return false; 
		}
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($basepath.$file)) {
				$valid_files[] = $file;
				}
			}
		}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
			}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($basepath.$file,$file);
			}
		//close the zip -- done!
		$zip->close();
		//check to make sure the file exists
		if(file_exists($destination)){
			return $destination;
			}
		return false;	
		}
	return false;
	}	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
//END





