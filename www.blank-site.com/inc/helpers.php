<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	helpers functions used everywhere by the site

*/


//------------------------------------------------------------------------------------------
//thw way the message is build for the client javascript application	
function buildAjaxMessage(&$oReg, $data, $errors = ''){
	$arr = array(
		'section' => $oReg->get('req')->get('section'),
		'service' => $oReg->get('req')->get('service'),
		'pid' => $oReg->get('req')->get('pid'),
		'timestamp' => $oReg->get('req')->get('time'),
		'msgerrors' => $errors,
		'data' => $data,
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
	$arr['sql'] = $iNumQ;	
	//
	return $arr;
	}
	
//---------------------------------------------------------------------------------------------------------------
function sqlSafe($str){
	//return mysql_real_escape_string($str);	
	//return mysql_escape_string($str);	
	//$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    //$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
    return str_replace('"', '\"', $str);
	//return $str;
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

//---------------------------------------------------------------------------------------------------------------
function htmlSafeTag($str){
	return str_replace('"', '\"', $str);
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

function safePhpCode($strText){
	// IMPORTANT: 
	// remimber that it will loose the scope of the $oReg and $oGlob	
	//	
	//safe reverse au as ou pas deja fait	
	$strText = safeReverse($strText);	
	//maintenant si il y a du php
	//EX: {[INCLUDE(test)]}
	$strText = preg_replace("/\{\[INCLUDE\((.*)\)\]\}/Ui", EOL."<?php include(DIR_INCLUDE.'$1.php'); ?>".EOL, $strText);
	//la classe direct php
	require_once(DIR_CLASS.'directphp.php');
	$oDirectPhp = new DirectPHP();
	return $oDirectPhp->onContent($strText);	
	}

//------------------------------------------------------------------------------------------

function safeReverse($strText){
	return str_replace(array('&amp;','&lt;','&gt;','&quot;'),array('&','<','>','"'), $strText);
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

function safeInputString($str){
	//les ecriture des retours de formulaire pour eviter les script attack
	return  preg_replace('/[<>;:()"]+/i','', stripslashes($str));;
	}

function safeInputInt($str){
	//les ecriture des retours de formulaire pour eviter les script attack
	return preg_replace('/[^0-9]+/i','', $str);
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

	
//---------------------------------------------------------------------------------------------------------------	
function hexstr($hexstr) {
	$hexstr = str_replace(' ', '', $hexstr);
	$hexstr = str_replace('\x', '', $hexstr);
	$retstr = pack('H*', $hexstr);
	return $retstr;
	}
	
//---------------------------------------------------------------------------------------------	
function frenchUcfirst($v) { 
	$lowCase  = "\\xE0\\xE1\\xE2\\xE3\\xE4\\xE5\\xE7\\xE8\\xE9\\xEA\\xEB\\xEC\\xED\\xEE\\xEF"; 
	$lowCase .= "\\xF1\\xF2\\xF3\\xF4\\xF5\\xF6\\xF8\\xF9\\xFA\\xFB\\xFC\\xFD\\xFF\\u0161"; 
	$upperCase = "AAAAAA\\xC7EEEEIIIINOOOOOOUUUUYYS"; 
	return strtoupper(strtr(substr($v, 0, 1), $lowCase, $upperCase)) . substr($v, 1); 
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

// THIS SERIALIZE AND UNSERIALIZE HTML CONTENT GENERALLY MADE WITH TINYMCE AND PASSED BY JSON


function unserializeFromDbData($data){
	return unserialize(str_replace('&quot;','"',$data)); 
	}
	
function formatSerialize(&$str, $strKey){
    $str = str_replace(array('>','<','&',"'"), array('[gt;]','[lt;]','[amp;]',"[sinquote;]"), $str);
	$str = str_replace("\n","[eol]", $str);
	}
    
function formatSerializeRev(&$str, $strKey){
	$str = str_replace(array('[gt;]','[lt;]','[amp;]',"[sinquote;]"), array('&gt;','&lt;','&',"'"), $str);
	$str = str_replace("[eol]","\n", $str);	
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
function trimKeyword($str, $encoding = 'UTF-8'){
	if($str != ''){
		//on va garder juste ce que l'on a besoin
		$str = mb_strtolower($str, $encoding);
		$str = preg_replace('/[^a-zA-Z0-9\'\sÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ]/', ' ', $str);	
		$str = trim($str);
		$str = preg_replace('/[ ]+/', DEFAULT_ID_SEPARATOR, $str);
		$str = preg_replace('/[-]+/', DEFAULT_ID_SEPARATOR, $str);
		$arrChars = array(
			'à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','þ','ÿ','ß','Þ');
		$arrCharsReplace = array(
			'a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','n','o','o','o','o','o','o','u','u','u','u','y','p','y','b','p');
		//si jamais avait des caractere accentue on va faire la meme requete en plus avec ceux la
		//$str = str_replace($arrChars, $arrCharsReplace, $str);
		}
	return $str;
	}	


//---------------------------------------------------------------------------------------------	
function redirectKeywordUrl($str){
	
	//test string:
	//|!"/$*()_+¨^P:`>É.£¢¤¬¦²³¼½¾±\+.ÉsÞßàáâãäåæçèéêëìíîïð/ñ/ò/ó\\ôõöøùúûüýþÿ

	//un peu de manipulation du request
	$strTrimmed = trimKeyword($str);
	if($strTrimmed != $str){
		//on redirect 
		return $strTrimmed;
		}
	return false;	
	}	
	
	
	
//END





