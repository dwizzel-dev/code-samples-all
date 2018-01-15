<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	helpers functions used everywhere by the site
@notes:

*/
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

//---------------------------------------------------------------------------------------------------------------
function formatHtmlTags($str){
	$str = stripslashes($str);
	$str = addcslashes($str, "\"");
	$str =  str_replace("\n", '', $str);
	$str =  str_replace("\r\f", '', $str);	
	$str =  str_replace("<br>", '', $str);
	$str =  str_replace("<br />", '', $str);
	
	return $str;
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

//-------------------------------------------------------
function cleanUrl($str, $locale = 'en_US', $htmlFormat = ENT_HTML401, $maxLenght = 3){
	//convertir les html chars
	$str = html_entity_decode(trim($str), ENT_NOQUOTES | $htmlFormat, 'UTF-8');	
	//tout en minuscule
	$str = mb_strtolower($str, 'UTF-8');
	//on convertit
	$str = convertChar($str);
	//les and
	$str = preg_replace('/&amp;/', '-', $str);
	//les chars delimiter
	$str = preg_replace("/&quot;/", "-", $str);
	$str = preg_replace("/'/", "-", $str); 
	$str = preg_replace('/"/', "-", $str);
	//last trim of invalid chars au cas ou
	$str = preg_replace("/[^a-z0-9-]/", "-", $str);	
	//clean les suite pour un seul
	$str = preg_replace("/[\s-]+/", "-", $str); 
	//on split et on garde uniquement les mots plus grand que 3 carateres, sinon le mot sera invalide pour google crawlers
	$arrStr = explode('-', $str);
	$strTmp = '';
	foreach($arrStr as $k=>$v){
		//si plus long on le garde
		if(strlen($v) >= $maxLenght){
			$strTmp .= $v.'-';
			}
		}
	//strip le dernier dash
	$strTmp = substr($strTmp, 0, strlen($strTmp) - 1);	
	//return 
	return $strTmp;
	}


//-------------------------------------------------------
//pour les description des meta
function metaTextReducer($str, $htmlFormat = ENT_HTML401, $maxLenght = 150){
	//convertir les html chars
	$strNoHtml = html_entity_decode($str, ENT_NOQUOTES | $htmlFormat, 'UTF-8');	
	//on change le encoding et check si valide
	$str = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $strNoHtml);
	if($str === false){
		trigger_error('Error[0] on iconv(\'UTF-8\', \'ISO-8859-1//TRANSLIT\', '.$strNoHtml.');', E_USER_NOTICE);		
		}
	//garde le nombre de char needed	
	$str = substr($str, 0, $maxLenght);
	//on split et on garde uniquement un mot complet a la fin du texte pour pas donner des "il etait une f" on garde "il etait une"
	$arrStr = explode(' ', $str);	
	$strTmp = '';
	if(count($arrStr) > 2){
		for($i=0; $i<(count($arrStr) - 1); $i++){
			$strTmp	.= $arrStr[$i].' ';
			}
	}else{
		$strTmp = $str;	
		}
	//on remplace les doubles espace
	$strTmp = str_replace("\n", ' ', $strTmp); 	
	$strTmp = str_replace('[EOL]', ' ', $strTmp); 			
	$strTmp = preg_replace("/[\s]+/", ' ', $strTmp);
	//trim les last spaces
	$strTmp = trim($strTmp);
	//on reconvertit en UTF-8
	$strTmp = mb_convert_encoding($strTmp, 'UTF-8', 'ISO-8859-1');
	//return 
	return $strTmp;
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
function search_replace($s,$r,$sql){ 
	$e = '/('.implode('|',array_map('preg_quote', $s)).')/'; 
	$r = array_combine($s,$r);
	return preg_replace_callback($e, function($v) use ($s,$r){ 
		return $r[$v[1]]; 
		},$sql);
	}
	
//-----------------------------------------------------------------------------------------------
function uniformize($strData){
	$bModified = true;	
	$data = '';
	//minor check sur ce queon a besoin
	if($strData != ''){
		//on detect encodage de la string
		$encoding = mb_detect_encoding($strData, mb_detect_order(), true);
		//minor check sur l'encodage de base
		if($encoding !== false){
			//on convertit le encoding de *** a utf-8 pour catcher les erreurs 
			$dataConvertEncoding = mb_convert_encoding($strData, 'UTF-8', $encoding);
			//on change le encoding et check si valide
			$data = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $dataConvertEncoding);
			if($data !== false){
				//si c'est du UTF-8 ca se peut qu'il soit doublement encode alors on verifie
				if($encoding == 'UTF-8'){
					//on detect le double encodage de la string
					$doubleEncoding = mb_detect_encoding($data, mb_detect_order(), true);
					if($doubleEncoding == 'UTF-8'){
						//on convertit le encodingde *** a utf-8 pour catcher les erreurs 
						$dataConvertEncoding = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
						//on change le encoding et check si valide
						$data = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $dataConvertEncoding);
						//si pas valide utf-8
						if($data === false){
							$bModified = false;
							trigger_error($data ,E_USER_WARNING);	
							}
						}
					}	
			}else{
				$bModified = false;
				trigger_error($data ,E_USER_WARNING);	
				}
		}else{
			$bModified = false;
			trigger_error($data ,E_USER_WARNING);		
			}
	}else{
		$bModified = false;
		trigger_error($data ,E_USER_WARNING);		
		}
	//ca passe
	if($bModified === true && $data != ''){
		//on strip
		$data = stringConverter($data);
		//une deuxxime fois
		$data = stringConverter($data);
		//on reconvertit en utf-8
		$dataUTF8 = mb_convert_encoding($data, 'UTF-8', 'ISO-8859-1');
		//try to fix UTF-8 chars
		$dataUTF8 = fixString($dataUTF8);
		//minor check
		if($dataUTF8.'' != ''){
			//si execute	
			return $dataUTF8;
			}
		}
	return false;	
	}

//-----------------------------------------------------------------------------------------------
function stringConverter($str){

	//on va essayer de changer ceux qui sont des accent aigus mal convertit et qui donne des '&quest;'
	// le pattern '&quest;lastique'
	// le pattern '-&quest;tirement'	
	// le pattern ' &quest;levation'
	$str = preg_replace('/(\s{0,}|-{0,})(&quest;)(.*)$/', '${1}&eacute;${3}', $str);

	//on decode en char normale html
	$str = htmlspecialchars_decode($str, ENT_QUOTES);	
	$str = html_entity_decode($str, ENT_QUOTES|ENT_HTML401, 'ISO-8859-1');
	$str = html_entity_decode($str, ENT_QUOTES|ENT_HTML5, 'ISO-8859-1');
	$str = html_entity_decode($str, ENT_QUOTES|ENT_XHTML, 'ISO-8859-1');

	//trim
	$str = trim($str);	

	//special chars numeric
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
			'&#140;',
			'&#147;',
			'&#148;',
			'&#149;',
			'&#150;',
			'&#151;',
			'&#156;',
			'&#160;',
			'&#130;',
			'&#131;',
			'&#133;',
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
			'&OElig;', 
			'&ldquo;', 
			'&rdquo;', 
			'&bull;', 
			'&ndash;', 
			'&mdash;', 
			'&oelig;', 
			'&nbsp;', 
			'&sbquo;', 
			'&fnof;',
			'&mldr;',
			),
		$str);

	//les chars qui ne pase toujours pas
	$str = str_replace(
		array(
			'&OpenCurlyDoubleQuote;',
			'&rdquo;',
			'&oelig;',
			'&OElig;',
			'&sol;',
			'&fjlig;',
			),
		array(
			'"',
			'"',
			'oe',
			'OE',
			'/',
			'fj',
			),
		$str);
		
	//cariage return
	$str = str_replace(
		array(
			'<br >', 
			'<br>', 
			'<br />', 
			'<br/>',
			"\n\r",
			"\r\n",
			"\r\f",	
			"\f\r",		
			"\n",
			"\r",
			"\f",
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
		"\n", $str); //[{eol}]

	//des trucs qui n'on pas de bon sens car ils sont brise ou pas complet
	//selon l'analyse les É/é ne passe jamais la function precedente les a remplacer par [{quest}]	
	$str = str_replace(
		array(
			'&tab;',
			'&Tab;',	
			'&bsol;\'',
			'&bsol;"',
			"\'",
			'\"',
			"\t",
			),
		array(
			' ',	
			' ',
			"'",	
			'"',
			"'",
			'"',
			' ',
			),
		$str);

	//strip multiple espace pour un seul
	$str = str_replace('&nbsp;', ' ', $str);
	$str = preg_replace('/[\s]+/', ' ', $str);

	//les fins de ligne bizarre ou breake genre &comma
	$str = preg_replace('/(.*)(&{1}[\d\w]*)$/', '${1}', $str);

	//un dernier trim pour le fun
	$str = trim($str);

	//si les fins sont une virgule
	$str = preg_replace('/(.*),$/', '${1}', $str);

	return $str;
	}

//-----------------------------------------------------------------------------------------------
function fixString($str){
	
	$chr_map = array(
		//windows
		"\xC2\x82" => "'",
		"\xC2\x8B" => "'",
		"\xC2\x91" => "'",
		"\xC2\x92" => "'",
		"\xC2\x9B" => "'",
		"\xC2\x84" => '"',
		"\xC2\x93" => '"',
		"\xC2\x94" => '"',
		"\xC2\xAB" => '"',
		"\xC2\xBB" => '"',
		//uncode
		"\xE2\x80\x98" => "'",
		"\xE2\x80\x99" => "'",
		"\xE2\x80\x9A" => "'",
		"\xE2\x80\x9B" => "'",
		"\xE2\x80\xB9" => "'",
		"\xE2\x80\xBA" => "'",
		"\xE2\x80\x9C" => '"',
		"\xE2\x80\x9D" => '"',
		"\xE2\x80\x9E" => '"',
		"\xE2\x80\x9F" => '"',
		//les barres windows	
		"\xC2\x96" => "-",
		"\xC2\x97" => "-",
		//les barres unicode	
		"\xE2\x80\x93" => "-",
		"\xE2\x80\x94" => "-",
		"\xE2\x80\x92" => "-",	
		);

	$chr = array_keys($chr_map);
	$rpl = array_values($chr_map);
	$str = str_replace($chr, $rpl, $str);
	
	return $str;
	}	

	
//END
