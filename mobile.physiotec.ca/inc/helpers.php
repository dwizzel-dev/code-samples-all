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
		'sessid' => $oReg->get('req')->get('PHPSESSID'),	
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

//------------------------------------------------------------------------------------------	
function format2Dec($num){
	return number_format($num, 2);
	}

//------------------------------------------------------------------------------------------	
function pagePerfo(){
	return 0;
	}

//------------------------------------------------------------------------------------------	
function translate($str){
	if(function_exists('gettext')){
		return gettext($str);
		}
	return $str;
	}

//------------------------------------------------------------------------------------------	
function encodeString($str){
	if(is_string($str)){
		$str = htmlentities(stripslashes(cleanString(trim($str))), ENT_COMPAT | ENT_IGNORE, "UTF-8", false);
		}
	// in case the decoding fail or fail parameter
	if(is_string($str) && !empty($str)){
		return $str;
		}
	return '';
	}

//------------------------------------------------------------------------------------------
function decodeString($str){
	if(is_string($str) && !empty(trim($str))){
		if(!mb_detect_encoding($str, 'UTF-8', true)){
			return '';
			}
		$str = html_entity_decode(cleanString(trim($str)), ENT_NOQUOTES | ENT_HTML401, "UTF-8");
		}
	// in case the decoding fail or fail parameter
	if(is_string($str) && !empty($str)){
		return $str;
		}
	return '';
	}

//------------------------------------------------------------------------------------------
function json_decodeStr($str){
	$return_result = json_decode(cleanString($str), true);
	//
	if(is_array($return_result)){
		return $return_result;
		}
	return array();
	}

//------------------------------------------------------------------------------------------
function json_endecodeArr($arr){
	$str = '{}';
	if(is_array($arr) && !empty($arr)){
		$str = json_encode($arr, JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
		}

	//check si erreur json
	/*	
	if(json_last_error() != JSON_ERROR_NONE){
		return 'JSONERROR:'.json_last_error_msg();
		}	
	*/
	// false or ture or null when fail decoding
	if(is_string($str) && !empty($str)){
		return $str;
		}
	//
	return '{}';	
	}

//------------------------------------------------------------------------------------------	
function testArgs($str){
	return html_entity_decode($str);
	}

//------------------------------------------------------------------------------------------
function frenchUcfirst($v) { 
	$lowCase  = "\\xE0\\xE1\\xE2\\xE3\\xE4\\xE5\\xE7\\xE8\\xE9\\xEA\\xEB\\xEC\\xED\\xEE\\xEF"; 
	$lowCase .= "\\xF1\\xF2\\xF3\\xF4\\xF5\\xF6\\xF8\\xF9\\xFA\\xFB\\xFC\\xFD\\xFF\\u0161"; 
	$upperCase = "AAAAAA\\xC7EEEEIIIINOOOOOOUUUUYYS"; 
	return strtoupper(strtr(substr($v, 0, 1), $lowCase, $upperCase)) . substr($v, 1); 
	} 

//---------------------------------------------------------------------------------------------------------------	
function hexstr($hexstr) {
	$hexstr = str_replace(' ', '', $hexstr);
	$hexstr = str_replace('\x', '', $hexstr);
	$retstr = pack('H*', $hexstr);
	return $retstr;
	}

//---------------------------------------------------------------------------------------------------------------
function addHoursToDate($date, $hours){
	return date("Y-m-d H:i:s", strtotime($date) + ((60*60) * $hours));
	}

//---------------------------------------------------------------------------------------------------------------	
function addDate($givendate, $day=0, $mth=0, $yr=0){
	$cd = strtotime($givendate);
	$newdate = date('d-m-Y', mktime(date('h', $cd), date('i',$cd), date('s',$cd), date('m',$cd)+$mth, date('d',$cd)+$day, date('Y',$cd)+$yr));
	return $newdate;
	}

//---------------------------------------------------------------------------------------------------------------	
function numberPad($number, $n){
	return str_pad((int)$number, $n, '0', STR_PAD_LEFT);
	}	

//---------------------------------------------------------------------------------------------------------------	
function replaceNewLineByBR($str){
	return str_replace("\n", '<br />', $str);
	}
	
//---------------------------------------------------------------------------------------------------------------	
function replaceSpaceByNBSP($str){
	return str_replace(' ', '&nbsp;', $str);
	}

//------------------------------------------------------------------------------------------	
function rteSafeReverse($strText) {
	$tmpString = $strText; 
	$tmpString = str_replace('&amp;','&', $tmpString); 
	$tmpString = str_replace('&lt;','<', $tmpString); 
	$tmpString = str_replace('&gt;','>',  $tmpString); 
	$tmpString = str_replace('&quot;', '"', $tmpString); 
	$tmpString = str_replace("\r\n", '<br />', $tmpString); //etait en dernier 01-07-2013
	$tmpString = str_replace(chr(10), '<br />', $tmpString); 
	$tmpString = str_replace(chr(13), '<br />', $tmpString); 
	//
	return $tmpString;
	}
	
//------------------------------------------------------------------------------------------	
function safeReverse($strText) {
	return str_replace(array('&amp;','&lt;','&gt;','&quot;'),array('&','<','>','"'), $strText); 
	}	

//---------------------------------------------------------------------------------------------------------------
function formatJavascript($result){
	$result = stripslashes($result);
	$result = addcslashes($result, "'");
	//mais on reconvertit kesa <br /> en \n qui sont maintenant des "&lt;br /&gt;"
	$result =  str_replace('&lt;br /&gt;', "\\n", $result);
	$result =  str_replace('&lt;br&gt;', "\\n", $result);
	$result =  str_replace("\r", '', $result);//les carriage return des formulaire et windows \r\f
	
	return $result;
	}

//---------------------------------------------------------------------------------------------------------------
function orderBy($data, $field){
    $code = "return strnatcmp(\$a['$field'], \$b['$field']);";
    uasort($data, create_function('$a,$b', $code));
    return $data;
	}   

//---------------------------------------------------------------------------------------------------------------
function double_quote_decode($strText){
	return str_replace('"', "&quot;", $strText);
	}

//---------------------------------------------------------------------------------------------------------------
function safeRegExStr($str){
	return str_replace('/','\/', preg_quote(mb_strtolower($str, 'UTF-8')));
	}

//---------------------------------------------------------------------------------------------------------------
function buildDate($time){
	$date = '';	
	try{
		$oDate = new DateTime($time);
		$date = $oDate->format('Y-m-d');
	}catch (Exception $e){
		$date = date('Y-m-d');
		}	
	return $date;
	}

//---------------------------------------------------------------------------------------------------------------
function buildDateTime($time){
	$date = '';
	try{
		$oDate = new DateTime($time);
		$date = $oDate->format('Y-m-d H:i:s');
	}catch (Exception $e){
		$date = date('Y-m-d H:i:s');
		}	
	return $date;
	}

//---------------------------------------------------------------------------------------------------------------
//for debug with special file path in require or include
//format: search-040320161057:client-070320161038	
function checkForDebugFile($filename, $str){
	if($str.'' != '' && $filename.'' != ''){
		$arr = explode(':', $str);
		if(count($arr) > 0){
			foreach($arr as $k=>$v){
				$arr2 = explode('-', $v);
				if(count($arr2) == 2){
					if($arr2[0] == $filename){
						return $filename.'.'.$arr2[1];
						}
					}
				}
			}
		}
	return $filename;
	}

//---------------------------------------------------------------------------------------------------------------
//to check if the file exist on the server eg image and check the size of it remotely
function checkRemoteFile($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_NOBODY, 1);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if(curl_exec($ch)!==FALSE) {
		return true;
	} else {
		return false;
	}
}

//------------------------------------------------------------------------------------------	
//strange caracters we dont want, complete list here: 
//http://www.aivosto.com/vbtips/control-characters.html
//and here
//http://unicodelookup.com/
//http://www.roubaixinteractive.com/PlayGround/Binary_Conversion/The_Characters.asp
//PROB:  found in APP114255
function cleanString($str){
	if(is_string($str)){

		//trim
		$str = trim($str);	
		
		//special char and/or with the controller
		$str = str_replace(
			array(
				'&#145;', 
				'&#146;',
				'&#176;', 
				'&#186;', 
				'&#174;', 
				'&#188;', 
				'&#173;', 
				'&#171;', 
				'&#177;', 
				'&#163;', 
				'&#190;', 
				'&#187;', 
				'&#189;', 
				chr(194).chr(145),
				chr(194).chr(146),
				chr(194).chr(176),
				chr(194).chr(186),	
				chr(194).chr(174),	
				chr(194).chr(188),	
				chr(194).chr(173),	
				chr(194).chr(171),	
				chr(194).chr(177),	
				chr(194).chr(163),	
				chr(194).chr(190),	
				chr(194).chr(187),	
				chr(194).chr(189),	
				),
			array(
				'&lsquo;', 
				'&rsquo;', 
				'&deg;',
				'&ordm;', 
				'&reg;', 
				'&frac14;', 
				'&shy;', 
				'&laquo;', 
				'&plusmn;', 
				'&pound;', 
				'&frac34;', 
				'&raquo;', 
				'&frac12;', 
				'&lsquo;', 
				'&rsquo;', 
				'&deg;', 
				'&ordm;', 
				'&reg;', 
				'&frac14;', 
				'&shy;', 
				'&laquo;', 
				'&plusmn;', 
				'&pound;', 
				'&frac34;', 
				'&raquo;', 
				'&frac12;', 
				),
			$str);
		
		//replace with nothing
		$str = str_replace(
			array(
				'&#140;',
				'&#147;',
				'&#148;',
				'&#149;',
				'&#150;',
				'&#151;',
				'&#156;',
				'&#160;',
				chr(194).chr(140),
				chr(194).chr(147),
				chr(194).chr(148),
				chr(194).chr(149),
				chr(194).chr(150),
				chr(194).chr(151),
				chr(194).chr(156),
				chr(194).chr(160),
				chr(0),
				), 
			'', $str);
		
		//cariage return
		$str = str_replace(
			array(
				'&#130;', 
				'&#131;', 
				'&#133;', 
				'<br >', 
				'<br>', 
				'<br />', 
				'<br/>',
				"\n\r",
				"\r\n",
				"\r",
				"\f",
				"\r\f",	
				"\f\r",		
				chr(130), 
				chr(131), 
				chr(133),
				chr(10).chr(13), 
				chr(13).chr(10), 
				chr(9), 
				chr(10), 
				chr(11),
				chr(12),
				chr(13),
				chr(14),
				chr(15),
				), 
			"\n", $str);
		
		//special chars	
		/*
		$str = str_replace(
			array(
				'®',
				'ô',
				'é',
				'á',
				'à',
				'À',
				'â',
				'É',
				'è',
				'ï',
				'ê',
				'î',
				'¼',
				'ó',
				'ñ',
				'ú',
				'ç',
				'°',
				'í',
				'ã',
				'ü',
				'û',
				'­',
				'º',
				'ä',
				'½',
				'>',
				'Ì',
				'ë',
				'<',
				'«',
				'»',
				'Ó',
				'ì',
				'·',
				'Ê',
				'©',
				'£',
				'“',
				'”',
				'¨',
				'Ñ',
				'×',
				'±',
				'¸',
				'ø',
				'å',
				'ö',
				'•',
				'’',
				'–',
				'—',
				'œ',
				'Æ',
				'Œ',
				'Š',
				'ª',
				),
			array(
				'&reg;',
				'&ocirc;',
				'&eacute;',
				'&aacute;',
				'&agrave;',
				'&Agrave;',
				'&acirc;',
				'&Eacute;',
				'&egrave;',
				'&iuml;',
				'&ecirc;',
				'&icirc;',
				'&frac14;',
				'&oacute;',
				'&ntilde;',
				'&uacute;',
				'&ccedil;',
				'&deg;',
				'&iacute;',
				'&atilde;',
				'&uuml;',
				'&ucirc;',
				'&shy;',
				'&ordm;',
				'&auml;',
				'&frac12;',
				'&gt;',
				'&Igrave;',
				'&euml;',
				'&lt;',	
				'&laquo;',
				'&raquo;',
				'&Oacute;',
				'&igrave;',
				'&middot;',	
				'&Ecirc;',
				'&copy;',
				'&pound;',
				'&ldquo;',
				'&rdquo;',
				'&uml;',
				'&Ntilde;',	
				'&times;',
				'&plusmn;',	
				'&cedil;',
				'&oslash;',
				'&aring;',
				'&ouml;',
				'&bull;',
				'&rsquo;',
				'&ndash;',
				'&mdash;',
				'&oelig;',
				'&AElig;',
				'&OElig;',
				'&Scaron;',
				'&ordf;',
				),
			$str);
		*/	
		}
	// in case the decoding fail or fail parameter
	if(is_string($str)){
		return $str;
		}
	return '';
	}


//------------------------------------------------------------------------------------------
function in_array_r($needle, $haystack, $strict = false) {
	foreach($haystack as $item){
		if(($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
			return true;
			}
		}
	return false;
	}




	
//END
