<?php
/**
@auth: Dwizzel
@date: 15-07-2016
@info: fichier de base pour le testing du autocomplete avec keywords
@version: V.1.0 B.048
 
*/
//----------------------------------------------------------------------------------------------------------------------

// ERROR REPORTING
error_reporting(0);

// BASE DEFINE
require_once('define.php');

// ERROR REPORTING
if(defined('ERROR_REPORT_LEVEL')){
	error_reporting(ERROR_REPORT_LEVEL);
	}

// CHECK ACCESS
if(SITE_IS_DOWN){
	if(isset($_SERVER['REMOTE_ADDR'])){
		if(!in_array($_SERVER['REMOTE_ADDR'],explode(',',REMOTE_ADDR_ACCEPTED))){
			exit('SORRY! OFFLINE FOR MAINTENANCE');
			}
	}else{
		exit('SORRY! OFFLINE FOR MAINTENANCE');
		}
	}

//default
$gLangDefault = DEFAULT_LOCALE_LANG;
$gLang = DEFAULT_LOCALE_LANG;
$gUserPath = PATH_USER;
$gServerPath = PATH_WEB;
$gStyle	= DEFAULT_STYLE;
$gBrand = DEFAULT_BRAND;
$gVersioning = DEFAULT_VERSIONING;
$gVersion = SITE_NAME;
$gTitle = DEFAULT_TITLE;
$gExerciceImagePath = PATH_EXERCICE_IMAGE;
$gIsAppz = 0;
$gOsType = '';

//default
$gSessionId = '0';
$gDebug = 0;
$noCache = '?&t='.time();

//style
$arrStyle = array(
	'oxygen' => array(
		'font-bold' => '700',
		'font-family' => 'Oxygen',
		'font-load' => '300,400,700',
		'font-weight' => '300',
		'font-size' => '13px',
		),
	);

//check les style
if(isset($_GET['style'])){
	if(isset($arrStyle[$_GET['style']])){
		$gStyle = $_GET['style'];
		}
	}
$arrStyle = $arrStyle[$gStyle];

//on get la langue dans le url
if(isset($_GET['lang'])){
	$gLang = $_GET['lang'];
	}

//on get la sessid dans le url
if(isset($_GET['PHPSESSID'])){
	$gSessionId = $_GET['PHPSESSID'];
	}

//pour le brand 000,001
if(isset($_GET['brand'])){	
	$gBrand = $_GET['brand'];
	}

//pour le versionning 003,004
if(isset($_GET['versioning'])){	
	$gVersioning = $_GET['versioning'];
	}

//pour le debug
if(isset($_GET['dwizzel'])){	
	$gDebug = intVal($_GET['dwizzel']);
	}

//pour le mobile
if(isset($_GET['is_appz'])){	
	$gIsAppz = intVal($_GET['is_appz']);
	}

//pour le prototype c'est a dire quel fichier utiliser 
//avec les deifferent type de call et autocomplete
if(isset($_GET['proto'])){	
	$gPrototypeVersion = $_GET['proto'];
	//minor check	
	if(strlen($gPrototypeVersion) != 3){
		$gPrototypeVersion = '003';	
		}
	}

//pour le ipad/iphone
if(isset($_GET['os_type'])){	
	$gOsType = $_GET['os_type'];
	}

//pour windows on ne peut pas passer de user:psw dans le url
if(strtolower($gOsType) == 'windows'){
	$gServerPath = str_replace(PATH_USER, '', $gServerPath);
	}

//le title avec le branding and versioning
$gTitle = DEFAULT_TITLE.'(BV.'.$gBrand.'.'.$gVersioning.')';


?><!DOCTYPE html>
<html>
<head>
<title><?php echo $gTitle; ?></title>
<!-- META -->
<meta charset="utf-8">
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Security-Policy" content="default-src * 'unsafe-inline' 'unsafe-eval';">
<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
<!-- FONT -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=<?php echo $arrStyle['font-family']; ?>:<?php echo $arrStyle['font-load']; ?>" type="text/css" />
<!-- CSS -->
<link href="<?php echo $gServerPath; ?>css/<?php echo $gBrand; ?>/<?php echo $gVersioning; ?>/global.css<?php echo $noCache; ?>" rel="stylesheet">
<!-- EXTERN SCRIPT -->
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jquery-2.1.4.min.js"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/hammer.min.js"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/json2.js"></script>
<!-- LOCAL SCRIPT -->
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/<?php echo $gBrand; ?>/<?php echo $gVersioning; ?>/jdebug.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/<?php echo $gBrand; ?>/<?php echo $gVersioning; ?>/jutils.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/<?php echo $gBrand; ?>/<?php echo $gVersioning; ?>/jthread.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/<?php echo $gBrand; ?>/<?php echo $gVersioning; ?>/jsearch.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/<?php echo $gBrand; ?>/<?php echo $gVersioning; ?>/jcomm.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/<?php echo $gBrand; ?>/<?php echo $gVersioning; ?>/jautocomplete.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/<?php echo $gBrand; ?>/<?php echo $gVersioning; ?>/jappz.js<?php echo $noCache; ?>"></script>

<style>
*{ 
	/*
	-moz-user-select:none; 
	-webkit-user-select:none; 
	-webkit-tap-highlight-color:rgba(255, 255, 255, 0);  
	*/	
	}
img{
	/*pointer-events: none;*/
	}
	
INPUT[type=TEXT],TEXTAREA,SELECT,INPUT[type=PASSWORD] {
	-webkit-user-select: text !important;
	font-family:"<?php echo $arrStyle['font-family']; ?>";
	}	
B{
	font-weight: <?php echo $arrStyle['font-bold']; ?>;
	}
html, body {
	padding: 0;
	margin: 0;
	position: relative;
	/*font-family:"<?php echo $arrStyle['font-family']; ?>";*/
	font-family:"Arial";
	font-size:<?php echo $arrStyle['font-size']; ?>;
	font-weight:<?php echo $arrStyle['font-weight']; ?>;
	background-color: #ddd;
	color:#000;
	/*overflow:hidden;*/
	zoom: 1;
	/*min-height: 100%;*/
	/*height: 100%;*/
	}
</style>
<!-- INTERN SCRIPT -->
<script charset="utf-8">
//---------------------------------------------------------------------
//apple detector for printing pdf problem wwith iOS higher than 8.0
function isAppleMobile(){
	var arr = navigator.userAgent.match(/ipad|iphone/gi);	
	if(typeof(arr) == 'object'){
		if(arr != null){
			if(arr.length){
				return 1;
				}
			}
		}
	return 0;	
	};

//---------------------------------------------------------------------
//prevent back button on mobile phone
window.location.hash = '#loading';
window.history.pushState(null, '', '#started');
window.onhashchange = function(){
	if(location.hash == '#loading'){
		window.history.pushState(null, '', '#started');
		}
	};

//---------------------------------------------------------------------
//global vars
var gLocaleLangDefault = '<?php echo $gLangDefault; ?>';
var gLocaleLang = '<?php echo $gLang; ?>';
var gServerPath = '<?php echo $gServerPath; ?>';
var gServerPathImages = '<?php echo $gServerPath; ?>/images/<?php echo $gBrand; ?>/<?php echo $gVersioning; ?>/';
var gExerciceImagePath = '<?php echo $gExerciceImagePath; ?>';
var gSessionId = '<?php echo $gSessionId; ?>';
var gDebug = <?php echo $gDebug; ?>;
var gBrand = '<?php echo $gBrand; ?>';
var gVersioning = '<?php echo $gVersioning; ?>';
var gStyle = '<?php echo $gStyle; ?>';
var gVersion = '<?php echo $gVersion; ?>';
var gLangAttempt = 0;
var gIsAppz = <?php echo intVal($gIsAppz); ?>;
var gOsType = '<?php echo $gOsType; ?>';
var gIsAppleMobile = isAppleMobile();


//---------------------------------------------------------------------
//global object, class, etc...
var jDebug;
var jLang;
var jAppz;

//---------------------------------------------------------------------
//main loader, need change for intel XDK
jQuery(document).ready(function($){
	//load the lang file
	getLangScript(gLocaleLang);
	//bug fix on apple disconnect error for the $.ajax call
	if(window.navigator.standalone){
		$.ajaxSetup({
			isLocal:false,
			});
		}	
	}); 

//---------------------------------------------------------------------
//load a script if fail try another language
function getLangScript(strLang){
	//load the lang file
	$.getScript(gServerPath + 'js/' + gBrand + '/' + gVersioning + '/lang/jlang.' + strLang + '.js<?php echo $noCache; ?>')
		//success
		.done(function(){
			jDebug = new JDebug();
			//global language base on the loaded file from gLocaleLang
			jLang = new JLang();
			//main application 		
			jAppz = new JAppz({
				basediv:'main-autocomplete-result',
				maincontainer: 'BODY',	
				});
			//init main appz
			jAppz.init();
			})
		//fail
		.fail(function(){
			gLangAttempt++;
			//set to default
			gLocaleLang = gLocaleLangDefault;
			//if fail then maybe we dont have this language use the basic one
			if(gLangAttempt < 3){
				getLangScript(gLocaleLang);
				}

			});
	};

</script>
</head>
<body>
<div id="main-container" class="resizable">
	<div class="content-searchbox-exercises resizable">
		<div id="main-kwtype" class="kw-kwtype">
			<div class="kwtype"><label class="type"><input id="kwtype0" name="kwtype[]" type="checkbox" checked value="1" ><div>Keywords</div></label></div>
			<div class="kwtype"><label class="type"><input id="kwtype1" name="kwtype[]" type="checkbox" value="2" ><div>Short title</div></label></div>
		</div>
		<div id="main-input" class="kw-searchbox"></div>	
		<div id="main-result" class="kw-result"></div>
	</div>
</div>	
</body>
</html>