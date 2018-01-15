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
				/*extra but have to test it*/
				chr(0),
				chr(1),
				chr(2),
				chr(3),
				chr(4),
				chr(5),
				chr(6),
				chr(7),
				chr(8),
				chr(9),
				chr(10),
				chr(11),
				chr(12),
				chr(13),
				chr(14),
				chr(15),
				chr(16),
				chr(17),
				chr(18),
				chr(19),
				chr(20),
				chr(21),
				chr(22),
				chr(23),
				chr(24),
				chr(25),
				chr(26),
				chr(27),
				chr(28),
				chr(29),
				chr(30),
				chr(31),
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
		
		}
	// in case the decoding fail or fail parameter
	if(is_string($str) || is_numeric($str)){
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


//-------------------------------------------------------
function convertChar($str){
	//arr replace	
    $replace = array(
		'ț' => 't', 'Ț' => 'T', '@' => 'at',
		'©' => 'c', '®' => 'r', 'À' => 'a', 'Ã' => 'A', 'ã' => 'a', 'Þ' => 'B', 
		'Ê' => 'E', 'Ñ' => 'N', 'ð' => 'o', 'ñ' => 'n', 'ș' => 's', 'Ș' => 'S', 
		'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'Å' => 'a', 'Æ' => 'ae','Ç' => 'c',
		'È' => 'e', 'É' => 'e', 'Ë' => 'e', 'Ì' => 'i', 'Í' => 'i', 'Î' => 'i',
		'Ï' => 'i', 'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Õ' => 'o', 'Ö' => 'o',
		'Ø' => 'o', 'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'Ý' => 'y',
		'ß' => 'ss','à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'å' => 'a',
		'æ' => 'ae','ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
		'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ò' => 'o', 'ó' => 'o',
		'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u',
		'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'þ' => 'p', 'ÿ' => 'y', 'Ā' => 'a',
		'ā' => 'a', 'Ă' => 'a', 'ă' => 'a', 'Ą' => 'a', 'ą' => 'a', 'Ć' => 'c',
		'ć' => 'c', 'Ĉ' => 'c', 'ĉ' => 'c', 'Ċ' => 'c', 'ċ' => 'c', 'Č' => 'c',
		'č' => 'c', 'Ď' => 'd', 'ď' => 'd', 'Đ' => 'd', 'đ' => 'd', 'Ē' => 'e',
		'ē' => 'e', 'Ĕ' => 'e', 'ĕ' => 'e', 'Ė' => 'e', 'ė' => 'e', 'Ę' => 'e',
		'ę' => 'e', 'Ě' => 'e', 'ě' => 'e', 'Ĝ' => 'g', 'ĝ' => 'g', 'Ğ' => 'g',
		'ğ' => 'g', 'Ġ' => 'g', 'ġ' => 'g', 'Ģ' => 'g', 'ģ' => 'g', 'Ĥ' => 'h',
		'ĥ' => 'h', 'Ħ' => 'h', 'ħ' => 'h', 'Ĩ' => 'i', 'ĩ' => 'i', 'Ī' => 'i',
		'ī' => 'i', 'Ĭ' => 'i', 'ĭ' => 'i', 'Į' => 'i', 'į' => 'i', 'İ' => 'i',
		'ı' => 'i', 'Ĳ' => 'ij','ĳ' => 'ij','Ĵ' => 'j', 'ĵ' => 'j', 'Ķ' => 'k',
		'ķ' => 'k', 'ĸ' => 'k', 'Ĺ' => 'l', 'ĺ' => 'l', 'Ļ' => 'l', 'ļ' => 'l',
		'Ľ' => 'l', 'ľ' => 'l', 'Ŀ' => 'l', 'ŀ' => 'l', 'Ł' => 'l', 'ł' => 'l',
		'Ń' => 'n', 'ń' => 'n', 'Ņ' => 'n', 'ņ' => 'n', 'Ň' => 'n', 'ň' => 'n',
		'ŉ' => 'n', 'Ŋ' => 'n', 'ŋ' => 'n', 'Ō' => 'o', 'ō' => 'o', 'Ŏ' => 'o',
		'ŏ' => 'o', 'Ő' => 'o', 'ő' => 'o', 'Œ' => 'oe','œ' => 'oe','Ŕ' => 'r',
		'ŕ' => 'r', 'Ŗ' => 'r', 'ŗ' => 'r', 'Ř' => 'r', 'ř' => 'r', 'Ś' => 's',
		'ś' => 's', 'Ŝ' => 's', 'ŝ' => 's', 'Ş' => 's', 'ş' => 's', 'Š' => 's',
		'š' => 's', 'Ţ' => 't', 'ţ' => 't', 'Ť' => 't', 'ť' => 't', 'Ŧ' => 't',
		'ŧ' => 't', 'Ũ' => 'u', 'ũ' => 'u', 'Ū' => 'u', 'ū' => 'u', 'Ŭ' => 'u',
		'ŭ' => 'u', 'Ů' => 'u', 'ů' => 'u', 'Ű' => 'u', 'ű' => 'u', 'Ų' => 'u',
		'ų' => 'u', 'Ŵ' => 'w', 'ŵ' => 'w', 'Ŷ' => 'y', 'ŷ' => 'y', 'Ÿ' => 'y',
		'Ź' => 'z', 'ź' => 'z', 'Ż' => 'z', 'ż' => 'z', 'Ž' => 'z', 'ž' => 'z',
		'ſ' => 'z', 'Ə' => 'e', 'ƒ' => 'f', 'Ơ' => 'o', 'ơ' => 'o', 'Ư' => 'u',
		'ư' => 'u', 'Ǎ' => 'a', 'ǎ' => 'a', 'Ǐ' => 'i', 'ǐ' => 'i', 'Ǒ' => 'o',
		'ǒ' => 'o', 'Ǔ' => 'u', 'ǔ' => 'u', 'Ǖ' => 'u', 'ǖ' => 'u', 'Ǘ' => 'u',
		'ǘ' => 'u', 'Ǚ' => 'u', 'ǚ' => 'u', 'Ǜ' => 'u', 'ǜ' => 'u', 'Ǻ' => 'a',
		'ǻ' => 'a', 'Ǽ' => 'ae','ǽ' => 'ae','Ǿ' => 'o', 'ǿ' => 'o', 'ə' => 'e',
		'Ё' => 'jo','Є' => 'e', 'І' => 'i', 'Ї' => 'i', 'А' => 'a', 'Б' => 'b',
		'В' => 'v', 'Г' => 'g', 'Д' => 'd', 'Е' => 'e', 'Ж' => 'zh','З' => 'z',
		'И' => 'i', 'Й' => 'j', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n',
		'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't', 'У' => 'u',
		'Ф' => 'f', 'Х' => 'h', 'Ц' => 'c', 'Ч' => 'ch','Ш' => 'sh','Щ' => 'sch',
		'Ъ' => '-', 'Ы' => 'y', 'Ь' => '-', 'Э' => 'je','Ю' => 'ju','Я' => 'ja',
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
		'ж' => 'zh','з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l',
		'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
		'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
		'ш' => 'sh','щ' => 'sch','ъ' => '-','ы' => 'y', 'ь' => '-', 'э' => 'je',
		'ю' => 'ju','я' => 'ja','ё' => 'jo','є' => 'e', 'і' => 'i', 'ї' => 'i',
		'Ґ' => 'g', 'ґ' => 'g', 'א' => 'a', 'ב' => 'b', 'ג' => 'g', 'ד' => 'd',
		'ה' => 'h', 'ו' => 'v', 'ז' => 'z', 'ח' => 'h', 'ט' => 't', 'י' => 'i',
		'ך' => 'k', 'כ' => 'k', 'ל' => 'l', 'ם' => 'm', 'מ' => 'm', 'ן' => 'n',
		'נ' => 'n', 'ס' => 's', 'ע' => 'e', 'ף' => 'p', 'פ' => 'p', 'ץ' => 'C',
		'צ' => 'c', 'ק' => 'q', 'ר' => 'r', 'ש' => 'w', 'ת' => 't', '™' => 'tm',
		/*
		'ъ'=>'-', 'Ь'=>'-', 'Ъ'=>'-', 'ь'=>'-',
		'Ă'=>'A', 'Ą'=>'A', 'À'=>'A', 'Ã'=>'A', 'Á'=>'A', 'Æ'=>'A', 'Â'=>'A', 'Å'=>'A', 'Ä'=>'Ae',
		'Þ'=>'B',
		'Ć'=>'C', 'ץ'=>'C', 'Ç'=>'C',
		'È'=>'E', 'Ę'=>'E', 'É'=>'E', 'Ë'=>'E', 'Ê'=>'E',
		'Ğ'=>'G',
		'İ'=>'I', 'Ï'=>'I', 'Î'=>'I', 'Í'=>'I', 'Ì'=>'I',
		'Ł'=>'L',
		'Ñ'=>'N', 'Ń'=>'N',
		'Ø'=>'O', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'Oe',
		'Ş'=>'S', 'Ś'=>'S', 'Ș'=>'S', 'Š'=>'S',
		'Ț'=>'T',
		'Ù'=>'U', 'Û'=>'U', 'Ú'=>'U', 'Ü'=>'Ue',
		'Ý'=>'Y',
		'Ź'=>'Z', 'Ž'=>'Z', 'Ż'=>'Z',
		'â'=>'a', 'ǎ'=>'a', 'ą'=>'a', 'á'=>'a', 'ă'=>'a', 'ã'=>'a', 'Ǎ'=>'a', 'а'=>'a', 'А'=>'a', 'å'=>'a', 'à'=>'a', 'א'=>'a', 'Ǻ'=>'a', 'Ā'=>'a', 'ǻ'=>'a', 'ā'=>'a', 'ä'=>'ae', 'æ'=>'ae', 'Ǽ'=>'ae', 'ǽ'=>'ae',
		'б'=>'b', 'ב'=>'b', 'Б'=>'b', 'þ'=>'b',
		'ĉ'=>'c', 'Ĉ'=>'c', 'Ċ'=>'c', 'ć'=>'c', 'ç'=>'c', 'ц'=>'c', 'צ'=>'c', 'ċ'=>'c', 'Ц'=>'c', 'Č'=>'c', 'č'=>'c', 'Ч'=>'ch', 'ч'=>'ch',
		'ד'=>'d', 'ď'=>'d', 'Đ'=>'d', 'Ď'=>'d', 'đ'=>'d', 'д'=>'d', 'Д'=>'D', 'ð'=>'d',
		'є'=>'e', 'ע'=>'e', 'е'=>'e', 'Е'=>'e', 'Ə'=>'e', 'ę'=>'e', 'ĕ'=>'e', 'ē'=>'e', 'Ē'=>'e', 'Ė'=>'e', 'ė'=>'e', 'ě'=>'e', 'Ě'=>'e', 'Є'=>'e', 'Ĕ'=>'e', 'ê'=>'e', 'ə'=>'e', 'è'=>'e', 'ë'=>'e', 'é'=>'e',
		'ф'=>'f', 'ƒ'=>'f', 'Ф'=>'f',
		'ġ'=>'g', 'Ģ'=>'g', 'Ġ'=>'g', 'Ĝ'=>'g', 'Г'=>'g', 'г'=>'g', 'ĝ'=>'g', 'ğ'=>'g', 'ג'=>'g', 'Ґ'=>'g', 'ґ'=>'g', 'ģ'=>'g',
		'ח'=>'h', 'ħ'=>'h', 'Х'=>'h', 'Ħ'=>'h', 'Ĥ'=>'h', 'ĥ'=>'h', 'х'=>'h', 'ה'=>'h',
		'î'=>'i', 'ï'=>'i', 'í'=>'i', 'ì'=>'i', 'į'=>'i', 'ĭ'=>'i', 'ı'=>'i', 'Ĭ'=>'i', 'И'=>'i', 'ĩ'=>'i', 'ǐ'=>'i', 'Ĩ'=>'i', 'Ǐ'=>'i', 'и'=>'i', 'Į'=>'i', 'י'=>'i', 'Ї'=>'i', 'Ī'=>'i', 'І'=>'i', 'ї'=>'i', 'і'=>'i', 'ī'=>'i', 'ĳ'=>'ij', 'Ĳ'=>'ij',
		'й'=>'j', 'Й'=>'j', 'Ĵ'=>'j', 'ĵ'=>'j', 'я'=>'ja', 'Я'=>'ja', 'Э'=>'je', 'э'=>'je', 'ё'=>'jo', 'Ё'=>'jo', 'ю'=>'ju', 'Ю'=>'ju',
		'ĸ'=>'k', 'כ'=>'k', 'Ķ'=>'k', 'К'=>'k', 'к'=>'k', 'ķ'=>'k', 'ך'=>'k',
		'Ŀ'=>'l', 'ŀ'=>'l', 'Л'=>'l', 'ł'=>'l', 'ļ'=>'l', 'ĺ'=>'l', 'Ĺ'=>'l', 'Ļ'=>'l', 'л'=>'l', 'Ľ'=>'l', 'ľ'=>'l', 'ל'=>'l',
		'מ'=>'m', 'М'=>'m', 'ם'=>'m', 'м'=>'m',
		'ñ'=>'n', 'н'=>'n', 'Ņ'=>'n', 'ן'=>'n', 'ŋ'=>'n', 'נ'=>'n', 'Н'=>'n', 'ń'=>'n', 'Ŋ'=>'n', 'ņ'=>'n', 'ŉ'=>'n', 'Ň'=>'n', 'ň'=>'n',
		'о'=>'o', 'О'=>'o', 'ő'=>'o', 'õ'=>'o', 'ô'=>'o', 'Ő'=>'o', 'ŏ'=>'o', 'Ŏ'=>'o', 'Ō'=>'o', 'ō'=>'o', 'ø'=>'o', 'ǿ'=>'o', 'ǒ'=>'o', 'ò'=>'o', 'Ǿ'=>'o', 'Ǒ'=>'o', 'ơ'=>'o', 'ó'=>'o', 'Ơ'=>'o', 'œ'=>'oe', 'Œ'=>'oe', 'ö'=>'oe',
		'פ'=>'p', 'ף'=>'p', 'п'=>'p', 'П'=>'p',
		'ק'=>'q',
		'ŕ'=>'r', 'ř'=>'r', 'Ř'=>'r', 'ŗ'=>'r', 'Ŗ'=>'r', 'ר'=>'r', 'Ŕ'=>'r', 'Р'=>'r', 'р'=>'r',
		'ș'=>'s', 'с'=>'s', 'Ŝ'=>'s', 'š'=>'s', 'ś'=>'s', 'ס'=>'s', 'ş'=>'s', 'С'=>'s', 'ŝ'=>'s', 'Щ'=>'sch', 'щ'=>'sch', 'ш'=>'sh', 'Ш'=>'sh', 'ß'=>'ss',
		'т'=>'t', 'ט'=>'t', 'ŧ'=>'t', 'ת'=>'t', 'ť'=>'t', 'ţ'=>'t', 'Ţ'=>'t', 'Т'=>'t', 'ț'=>'t', 'Ŧ'=>'t', 'Ť'=>'t', '™'=>'tm',
		'ū'=>'u', 'у'=>'u', 'Ũ'=>'u', 'ũ'=>'u', 'Ư'=>'u', 'ư'=>'u', 'Ū'=>'u', 'Ǔ'=>'u', 'ų'=>'u', 'Ų'=>'u', 'ŭ'=>'u', 'Ŭ'=>'u', 'Ů'=>'u', 'ů'=>'u', 'ű'=>'u', 'Ű'=>'u', 'Ǖ'=>'u', 'ǔ'=>'u', 'Ǜ'=>'u', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'У'=>'u', 'ǚ'=>'u', 'ǜ'=>'u', 'Ǚ'=>'u', 'Ǘ'=>'u', 'ǖ'=>'u', 'ǘ'=>'u', 'ü'=>'ue',
		'в'=>'v', 'ו'=>'v', 'В'=>'v',
		'ש'=>'w', 'ŵ'=>'w', 'Ŵ'=>'w',
		'ы'=>'y', 'ŷ'=>'y', 'ý'=>'y', 'ÿ'=>'y', 'Ÿ'=>'y', 'Ŷ'=>'y',
		'Ы'=>'y', 'ž'=>'z', 'З'=>'z', 'з'=>'z', 'ź'=>'z', 'ז'=>'z', 'ż'=>'z', 'ſ'=>'z', 'Ж'=>'zh', 'ж'=>'zh'
		*/
		);
	//replace all special chars
    	return strtr($str, $replace);
	}



//-------------------------------------------------------
function htmlmissingDecode($str){
	//va remplacer les missing html decode de HTML4.0	
	}


	
//END
