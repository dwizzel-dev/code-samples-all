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
function encodeStringForKeywordSearch($str){
	//la base
	$str = encodeString($str);
	//some specials one

	//special chars	
	$str = str_replace(
		array(
			"®",
			"ô",
			"é",
			"á",
			"à",
			"&",
			"À",
			"â",
			"É",
			"è",
			"ï",
			"ê",
			"î",
			"¼",
			"\"",
			"ó",
			"ñ",
			"ú",
			"ç",
			"°",
			"í",
			"ã",
			"ü",
			"û",
			"­",
			"º",
			"ä",
			"½",
			">",
			"Ì",
			"&",
			"ë",
			"<",
			"«",
			"»",
			"Ó",
			"ì",
			"·",
			"Ê",
			"©",
			"£",
			"“",
			"”",
			"¨",
			"Ñ",
			"×",
			"±",
			"¸",
			"ø",
			"å",
			"\\",
			"ö",
			"•",
			"’",
			"–",
			"—",
			"œ",
			"Æ",
			"Œ",
			"Š",
			"ª",
			),
		array(
			"&reg;",
			"&ocirc;",
			"&eacute;",
			"&aacute;",
			"&agrave;",
			"&amp;",
			"&Agrave;",
			"&acirc;",
			"&Eacute;",
			"&egrave;",
			"&iuml;",
			"&ecirc;",
			"&icirc;",
			"&frac14;",
			"&quot;",
			"&oacute;",
			"&ntilde;",
			"&uacute;",
			"&ccedil;",
			"&deg;",
			"&iacute;",
			"&atilde;",
			"&uuml;",
			"&ucirc;",
			"&shy;",
			"&ordm;",
			"&auml;",
			"&frac12;",
			"&gt;",
			"&Igrave;",
			"&amp;",
			"&euml;",
			"&lt;",	
			"&laquo;",
			"&raquo;",
			"&Oacute;",
			"&igrave;",
			"&middot;",	
			"&Ecirc;",
			"&copy;",
			"&pound;",
			"&ldquo;",
			"&rdquo;",
			"&uml;",
			"&Ntilde;",	
			"&times;",
			"&plusmn;",	
			"&cedil;",
			"&oslash;",
			"&aring;",
			"&#92;",
			"&ouml;",
			"&bull;",
			"&rsquo;",
			"&ndash;",
			"&mdash;",
			"&oelig;",
			"&AElig;",
			"&OElig;",
			"&Scaron;",
			"&ordf;",
			),
		$str);

	return $str;
	}

//------------------------------------------------------------------------------------------	
function encodeString($str){
	if(is_string($str) && !empty(trim($str))){
		
		//replace with nothing
		$str = trim(str_replace(array(
				"u0000", 
				"u0001", 
				"u0002", 
				"u0003", 
				"u0004", 
				"u0005", 
				"u0006", 
				"u0007", 
				"u0008", 
				"u0009", 
				"u000e", 
				"u000f", 
				"u0010", 
				"u0011", 
				"u0012", 
				"u0013", 
				"u0014", 
				"u0015", 
				"u0016", 
				"u0017", 
				"u0018", 
				"u0019", 
				"u001a", 
				"u001b", 
				"u001c", 
				"u001d", 
				"u001e", 
				"u001f", 
				"u008c", 
				"u0095", 
				"u007f", 
				"u0080", 
				"u0081", 
				"u0084", 
				"u0086", 
				"u0087", 
				"u0088", 
				"u0089", 
				"u008a", 
				"u008b", 
				"u008c", 
				"u008d", 
				"u008e", 
				"u008f", 
				"u0090", 
				"u0091", 
				"u0092", 
				"u0093", 
				"u0094", 
				"u0095", 
				"u0096", 
				"u0097", 
				"u0098", 
				"u0099", 
				"u009a", 
				"u009b", 
				"u009c", 
				"u009d", 
				"u009e", 
				"u009f", 
				"&#140;",
				"&#147;",
				"&#148;",
				"&#149;",
				"&#150;",	
				), 
			"", $str));

		//cariage return
		$str = str_replace(
			array(
				"<br >", 
				"<br>", 
				"<br />", 
				"<br/>", 
				"u000a",
				"u000b",
				"u000c",
				"u000d",
				"u0082", 
				"u0083",
				"u0085",
				"u2028",
				"u2029",
				"u0d0a",
				"&#130;", 
				"&#131;", 
				"&#133;", 
				"\n\r",
				"\r\n",
				"\r",
				"\f",
				chr(9), 
				chr(10), 
				chr(11),
				chr(12),
				chr(13),
				chr(14),
				chr(15),
				chr(10).chr(13), 
				chr(13).chr(10), 
				), 
			"\n", $str);
			
		//special chars	
		$str = str_replace(
			array(
				"u00e9",
				"é",
				"u0091", 
				"u0092", 
				"u0096", 
				"&#145;", 
				"&#146;", 
				"&#150;", 
				"u00ad",
				chr(176), 
				),
			array(
				"&eacute;",
				"&eacute;",	
				"&lsquo;", 
				"&rsquo;", 
				"&ndash;",
				"&lsquo;", 
				"&rsquo;", 
				"&ndash;", 
				"&ndash;", 
				"&deg;", 
				),
			$str);
			
		//spaces
		$str = str_replace(array(
			"u0020",
			"u00a0",
			), 
			" ", $str);
			
		$str = trim(stripslashes($str));
		// suppose we should convert single and double quotes
		$str = htmlentities($str, ENT_COMPAT | ENT_IGNORE, "UTF-8", false);
		}
	// in case the decoding fail or fail parameter
	if(!is_string($str) || empty($str)){
		$str = '';
		}
	return $str;
	}

//------------------------------------------------------------------------------------------
function decodeString($str){
	if(is_string($str) && !empty(trim($str))){
		if(!mb_detect_encoding($str, 'UTF-8', true)){
			$str = '';
		}
		//special codes found in protocols
		//u0082 &#130; Break Permitted Here
		//u0083 &#131; No Break Here
		//u008c &#140; Partial Line Backward
		//u0095 &#149; Message Waiting
		$str = str_replace(array("u0082", "u0083", "u008c", "u0095", "u0091", "u0092", "u0096"), array('', '', '', '', "&lsquo;", "&rsquo;", "&ndash;"), $str);
		//$str = html_entity_decode($str, ENT_COMPAT | ENT_HTML401, "UTF-8");
		$str = html_entity_decode($str, ENT_NOQUOTES | ENT_HTML401, "UTF-8");
		// suppose we should convert single and double quotes
		// ENT_QUOTES
		$str = str_replace(array("<br >", "<br>", "<br />", "<br/>", chr(10) . chr(13), chr(13) . chr(10), chr(13), chr(10), chr(9), "\r\n", "\r"), "\n", $str);
		$str = trim($str);
	}
	// in case the decoding fail or fail parameter
	if(!is_string($str) || empty($str)){
		$str = '';
	}
	return $str;
}

//------------------------------------------------------------------------------------------
function json_decodeStr($str){
	$retun_result = array();
	if(is_string($str) && !empty(trim($str))){
		if(!mb_detect_encoding($str, 'UTF-8', true)){
			$str = '';
		}
			
		//strange caracters we dont want, complete list here: 
		//http://www.aivosto.com/vbtips/control-characters.html
		//and here
		//http://unicodelookup.com/
		
		//replace with nothing
		$str = trim(str_replace(array(
			"u0000", 
			"u0001", 
			"u0002", 
			"u0003", 
			"u0004", 
			"u0005", 
			"u0006", 
			"u0007", 
			"u0008", 
			"u0009", 
			"u000e", 
			"u000f", 
			"u0010", 
			"u0011", 
			"u0012", 
			"u0013", 
			"u0014", 
			"u0015", 
			"u0016", 
			"u0017", 
			"u0018", 
			"u0019", 
			"u001a", 
			"u001b", 
			"u001c", 
			"u001d", 
			"u001e", 
			"u001f", 
			"u008c", 
			"u0095", 
			"u007f", 
			"u0080", 
			"u0081", 
			"u0084", 
			"u0086", 
			"u0087", 
			"u0088", 
			"u0089", 
			"u008a", 
			"u008b", 
			"u008c", 
			"u008d", 
			"u008e", 
			"u008f", 
			"u0090", 
			"u0091", 
			"u0092", 
			"u0093", 
			"u0094", 
			"u0095", 
			"u0096", 
			"u0097", 
			"u0098", 
			"u0099", 
			"u009a", 
			"u009b", 
			"u009c", 
			"u009d", 
			"u009e", 
			"u009f", 
			"&#140;",
			"&#149;",
			), 
			"", $str));	
		
		//special chars
		$str = str_replace(array(
			"u0091", 
			"u0092", 
			"u0096", 
			"&#145;", 
			"&#146;", 
			"&#150;", 
			"u00ad",
			chr(176), 
			), 
		array( 
			"&lsquo;", 
			"&rsquo;", 
			"&ndash;",
			"&lsquo;", 
			"&rsquo;", 
			"&ndash;", 
			"&ndash;", 
			"&deg;", 
			), $str);		
		
		//cariage return	
		$str = str_replace(array(
			"u000a",
			"u000b",
			"u000c",
			"u000d",
			"u0082", 
			"u0083",
			"u0085",
			"u2028",
			"u2029",
			"u0d0a",
			"&#130;", 
			"&#131;", 
			"\n\r",
			"\r\n",
			"\r",
			"\f",
			chr(9), 
			chr(10), 
			chr(11),
			chr(12),
			chr(13),
			chr(14),
			chr(15),
			), 
			"\n", $str);

		//spaces
		$str = str_replace(array(
			"u0020",
			"u00a0",
			), 
			" ", $str);

		$retun_result = json_decode($str, true);

		//echo json_last_error_msg()."\n\n";
		//exit($str);

	}
	// false or ture or null when fail decoding
	if(!is_array($retun_result) || empty($retun_result)){
		$retun_result = array();
	}
	return $retun_result;
}

//------------------------------------------------------------------------------------------
function json_endecodeArr($arr){
	$retun_result = '{}';
	if(is_array($arr) && !empty($arr)){
		$retun_result = json_encode($arr, JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
	}
	// false or ture or null when fail decoding
	if(!is_string($retun_result) || empty($retun_result)){
		$retun_result = '{}';
	}
	return $retun_result;
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
function cleanString($str){
	if(is_string($str)){
		$str = trim($str);	
		//replace with nothing
		$str = str_replace(
			array(
				"u0000", 
				"u0001", 
				"u0002", 
				"u0003", 
				"u0004", 
				"u0005", 
				"u0006", 
				"u0007", 
				"u0008", 
				"u0009", 
				"u000e", 
				"u000f", 
				"u0010", 
				"u0011", 
				"u0012", 
				"u0013", 
				"u0014", 
				"u0015", 
				"u0016", 
				"u0017", 
				"u0018", 
				"u0019", 
				"u001a", 
				"u001b", 
				"u001c", 
				"u001d", 
				"u001e", 
				"u001f", 
				"u008c", 
				"u0095", 
				"u007f", 
				"u0080", 
				"u0081", 
				"u0084", 
				"u0086", 
				"u0087", 
				"u0088", 
				"u0089", 
				"u008a", 
				"u008b", 
				"u008c", 
				"u008d", 
				"u008e", 
				"u008f", 
				"u0090", 
				"u0091", 
				"u0092", 
				"u0093", 
				"u0094", 
				"u0095", 
				"u0096", 
				"u0097", 
				"u0098", 
				"u0099", 
				"u009a", 
				"u009b", 
				"u009c", 
				"u009d", 
				"u009e", 
				"u009f", 
				"&#140;",
				"&#147;",
				"&#148;",
				"&#149;",
				"&#150;",	
				), 
			"", $str);

		//cariage return
		$str = str_replace(
			array(
				"<br >", 
				"<br>", 
				"<br />", 
				"<br/>", 
				"u000a",
				"u000b",
				"u000c",
				"u000d",
				"u0082", 
				"u0083",
				"u0085",
				"u2028",
				"u2029",
				"u0d0a",
				"&#130;", 
				"&#131;", 
				"&#133;", 
				"\n\r",
				"\r\n",
				"\r",
				"\f",
				chr(9), 
				chr(10), 
				chr(11),
				chr(12),
				chr(13),
				chr(14),
				chr(15),
				chr(10).chr(13), 
				chr(13).chr(10), 
				), 
			"\n", $str);
			
		//special chars	
		$str = str_replace(
			array(
				"u00e9",
				"é",
				"u0091", 
				"u0092", 
				"u0096", 
				"&#145;", 
				"&#146;", 
				"&#150;", 
				"u00ad",
				chr(176), 
				),
			array(
				"&eacute;",
				"&eacute;",	
				"&lsquo;", 
				"&rsquo;", 
				"&ndash;",
				"&lsquo;", 
				"&rsquo;", 
				"&ndash;", 
				"&ndash;", 
				"&deg;", 
				),
			$str);
			
		//spaces
		$str = str_replace(array(
			"u0020",
			"u00a0",
			), 
			" ", $str);
			
		}
	// in case the decoding fail or fail parameter
	if(!is_string($str)){
		$str = '';
		}
	return $str;
	}

	
//END
