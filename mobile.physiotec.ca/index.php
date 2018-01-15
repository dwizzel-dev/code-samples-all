<?php
/**
@auth: Dwizzel
@date: 22-04-2016
@info: fichier de base
@version: V.3.0 B.110
 
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
$gPrintServer = PATH_PRINT;
$gPdfViewer = PATH_PDF_VIEWER;
$gExerciceImagePath = PATH_EXERCICE_IMAGE;
$gStyle	= DEFAULT_STYLE;
$gBrand = DEFAULT_BRAND;
$gVersion = SITE_NAME;
$gTitle = DEFAULT_TITLE;
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
	'raleway' => array(
		'font-bold' => '600',
		'font-family' => 'Raleway',
		'font-load' => '400,500,600',
		'font-weight' => '400',
		'font-size' => '13px',
		),
	'dosis' => array(
		'font-bold' => '700',
		'font-family' => 'Dosis',
		'font-load' => '400,500,700',
		'font-weight' => '400',
		'font-size' => '14px',
		),
	'josephin' => array(
		'font-bold' => '700',
		'font-family' => 'Josefin Sans',
		'font-load' => '300,400,700',
		'font-weight' => '400',
		'font-size' => '14px',
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

//pour le debug
if(isset($_GET['dwizzel'])){	
	$gDebug = intVal($_GET['dwizzel']);
	}

//pour le mobile
if(isset($_GET['is_appz'])){	
	$gIsAppz = intVal($_GET['is_appz']);
	}

//pour le ipad/iphone
if(isset($_GET['os_type'])){	
	$gOsType = $_GET['os_type'];
	}

//pour windows on ne peut pas passer de user:psw dans le url
if(strtolower($gOsType) == 'windows'){
	$gServerPath = str_replace(PATH_USER, '', $gServerPath);
	$gPrintServer = str_replace(PATH_USER, '', $gPrintServer);
	$gPdfViewer = str_replace(PATH_USER, '', $gPdfViewer);
	}

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
<link href="<?php echo $gServerPath; ?>css/<?php echo $gBrand; ?>/global.css<?php echo $noCache; ?>" rel="stylesheet">
<link href="<?php echo $gServerPath; ?>css/<?php echo $gBrand; ?>/jsetting.css<?php echo $noCache; ?>" rel="stylesheet">
<!-- EXTERN SCRIPT -->
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jquery-2.1.4.min.js"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/e-smart-hittest-jquery.min.js"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/hammer.min.js"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/json2.js"></script>
<!-- LOCAL SCRIPT -->
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jdebug.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jclient.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jsearch.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jprogram.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jslider.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jutils.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jcomm.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jclient.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jexercise.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jsetting.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jappz.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jtouchspin.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jthread.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/juser.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jtemplate.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/jautocomplete.js<?php echo $noCache; ?>"></script>
<script charset="utf-8" src="<?php echo $gServerPath; ?>js/joptions.js<?php echo $noCache; ?>"></script>
<style>

*{ 
	-moz-user-select:none; 
	-webkit-user-select:none; 
	-webkit-tap-highlight-color:rgba(255, 255, 255, 0);  
	/* -webkit-user-select:none;*/ /* safari apple bug on text input */
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
	font-family:"<?php echo $arrStyle['font-family']; ?>";
	font-size:<?php echo $arrStyle['font-size']; ?>;
	font-weight:<?php echo $arrStyle['font-weight']; ?>;
	background-color: #000;
	color:#fff;
	overflow:hidden;
	zoom: 1;
	min-height: 100%;
	height: 100%;
	}

</style>
</head>
<body>
<!--main menu top:  -->
<div id="main-menu-top">

	<!--mask the top nanem and image:  -->
	<div id="main-client-logo">
		<img draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/logo-white.png">
	</div>

	<!--info client et prog:  -->
	<div id="main-client-name-text">
		<img id="top-user-image" draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/menu-user.png">
		<div id="top-client-name" >No client selected</div>
		<div id="top-program-name">No program saved</div>
	</div>

	<!--mobile menu:  -->
	<div id="main-site-menu">
		<a href="#"><img  draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/icone-main-menu.png"></a>
	</div>

</div>

<!--setting top title  -->
<div id="main-settings-top">
	<div class="settings-title-text">
		<span class="div-sty-20">Settings:</span> 
		<span id="main-settings-program-name">No Name...</span>
	</div>
	<div class="div-sty-1"><img  draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/glyphicons_chevron-up.png"></div>
</div>

<!--main container:  -->
<div id="main-container">
	<div class="main-client">

		<!--layer programs: main appz-->
		<div id="layer-programs" class="layer-slide-main gradient-background-color-2">
			<div class="div-sty-2">
				<h2 id="butt-modify-program" class="layer-top-title">
					<a href="#" id="butt-modify-program-icon"><img  draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/icone-program-edit-2.png"></a>
					<div id="main-program-name-text">New program</div>
				</h2>
				
				<!-- listing programs -->
				<ul id="listing-programs" class="div-sty-3"></ul>
			</div>
			
			<!--toolbar programs-->
			<div id="programs-tools">
				<div class="programs-submenu-layer">
					<a href="#" id="butt-programs-settings" class="butt-tools butt-slide-inner"><img draggable="false" class="img-sty-1" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/icone-settings.png"></a>
					<a href="#" id="butt-programs-send" class="butt-tools butt-slide-inner"><img draggable="false" class="img-sty-2" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/icone-send.png"></a>
					<a href="#" id="butt-programs-print" class="butt-tools butt-slide-inner"><img draggable="false" class="img-sty-3" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/icone-print.png"></a>
					<a href="#" id="butt-zoom-in" class="butt-tools" zoom-id="1" ><img draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/glyphicons_236_zoom_in.png"></a>
					<a href="#" id="butt-save" class="butt-tools"><img draggable="false" class="img-sty-3" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/icone-disk-1.png"></a>
					<a href="#" id="butt-check-invert" class="butt-tools" checkstate="0"><img draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/icone-select.png"></a>
					<a href="#" id="butt-delete-check-items" class="butt-tools"><img draggable="false" class="img-sty-4" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/icone-trash-red.png"></a>
					<a href="#" id="butt-delete-uncheck-items" class="butt-tools"><img draggable="false" class="img-sty-4" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/icone-trash-black.png"></a>
				</div>	
			</div>
			
			<!--layer settings-->
			<div id="layer-settings" class="layer-slide-inner" showed="0" showing="0" fix-bottom="1">
				<div class="layer-content">
					
					<!-- setting panel with touchspin componenet -->
					<div class="settingpanel" id="setting-panel-container"></div>
				</div>
			</div>
		</div>

		<!--layer client:  -->
		<div id="layer-client" class="layer-slide gradient-background-color" showed="0" showing="0">
			<div class="div-sty-5">
				<div class="layer-top-title">
					<a href="#" id="butt-new-client"><img draggable="false" class="add-client" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/add-user.png"></a>
					<div>
						<input type="text" placeholder="client search" value="" class="input-1 search-client" id="input-main-client-search-autocomplete" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false">
						<a href="#" class="butt-clear-search" attached-input-id="input-main-client-search-autocomplete"><img draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/glyphicons_197_remove.png"></a>
						<a href="#" id="butt-refresh-client" attached-input-id="input-main-client-search-autocomplete"><img draggable="false" class="client-search-icon" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/60577-w.png"></a>
					</div>
				</div>
				
				<!--layer client: listing search return -->
				<div id="listing-client-search"></div>
			</div>
		</div>

		<!--layer search:  -->
		<div id="layer-search" class="layer-slide gradient-background-color" showed="0" showing="0">
			<div class="div-sty-9">
				<div class="layer-top-title">
					<div>
						<select class="select-1 modules" id="search-select-module"></select>
						<select class="select-1 filter" id="search-select-filter"></select>
					</div>
					<div>
						<input type="text" placeholder="exercice search" value="" class="input-1 search-exercice" id="input-main-exercice-search-autocomplete" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false">
						<a href="#" class="butt-clear-search" attached-input-id="input-main-exercice-search-autocomplete"><img draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/glyphicons_197_remove.png"></a>
						<a href="#" id="butt-new-search-exercise" attached-input-id="input-main-exercice-search-autocomplete" ><img draggable="false" class="client-search-icon" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/60577-w.png"></a>	
					</div>
				</div>
				<div class="show" id="search-exercise-form">
					<p>By Template:</p>
					<p><select class="select-1 search" id="search-select-template-mine"></select></p>
					<p><select class="select-1 search" id="search-select-template-all"></select></p>
					<p><select class="select-1 search" id="search-select-template-license"></select></p>
					<p><select class="select-1 search" id="search-select-template-brand"></select></p>
				</div>
				
				<!--layer search: listing serach return -->
				<ul id="listing-search"></ul>
			</div>
			
			<!--layer search: le counter de resultat -->	
			<div id="search-exercice-counter">
				<div class="msg"></div>
			</div>	
		</div>
	</div>
</div>

<!--layer settings bottom:  -->
<div id="main-settings-bottom">
	<div class="div-sty-14">apply to:</div>
	<div class="div-sty-12">
		<div class="div-sty-13">
			<div class="div-sty-15">
				<a href="#" id="butt-settings-selected" class="a-icon-setting" setting-type="selected"><img draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/icon-selected.png"></a>
				<a href="#" id="butt-settings-unselected" class="a-icon-setting" setting-type="unselected"><img draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/icon-unselected.png"></a>
				<a href="#" id="butt-settings-all" class="butt settings" setting-type="all">all</a>
			</div>
		</div>
		<div class="div-sty-16">
			<a href="#" id="butt-zoom-in-2" class="butt-settings" zoom-id="-1"><img draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/glyphicons_236_zoom_in_invert.png"></a>
		</div>
		<div class="div-sty-17">
			<a href="#" class="butt settings fright" id="butt-settings-close">Close</a>
		</div>
	</div>
</div>

<!--layer menu bottom:  -->
<div id="main-menu-bottom">
	<div class="div-sty-18">
		<div class="div-sty-19">
			<a href="#" id="butt-client" class="butt-slide"><img draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/menu-user.png"></a>
			<a href="#" id="butt-search" class="butt-slide"><img draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/menu-exercice.png"></a>
			<a href="#" id="butt-programs" class="butt-slide">
				<div id="div-program-count"><span id="program-count-text"></span></div>
				<img  draggable="false"  src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/menu-program.png">
			</a>
			<a href="#" id="butt-exit" class="butt-slide exit"><img draggable="false" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/menu-exit.png"></a>
		</div>
	</div>
</div>

<!--main carouseel:  -->
<div id="main-popup-caroussel"></div>

<!--main popup: full screen  -->
<div id="main-popup-window" showing="0"></div>

<!--mobile sub menu: action with the main-site-menu -->
<div id="main-site-sub-menu" showed="0"></div>

<!--main loader -->
<div id="main-loader">
	<div style="padding-top:20px;font-family:Arial;">
		<b id="appz-version"><?php echo $gVersion; ?></b>
		<br />
		<img draggable="false" style="margin-top:0px;width:100%;max-width:134px;height:auto;" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/logo.png">
		<br />
		<img style="width:22px;height:auto;margin:0px 0px" src="<?php echo $gServerPath; ?>images/<?php echo $gBrand; ?>/mobile-loading-w-3.png" class="loading">	
	</div>
	<div id="main-login"></div>
</div>

<!--main popup alert: error, message, warning, comfirm, etc...  -->
<div id="main-popup-alert">
	<!--fond noir: -->
	<div class="window-box"></div>
	<!--contenu avec fond blanc et bouton close -->
	<div class="content-box">
		<!--contenu: -->
		<div class="content">	
			<h1></h1>
			<div class="text"></div>
		</div>
		<!--bouton close: -->
		<div class="butts">	
			<div class="actions"></div>
		</div>
	</div>
</div>
<!-- INTERN SCRIPT -->
<script charset="utf-8">

<?php
//---------------------------------------------------------------------
//if application mobile doit parler avec la fenetre principal qui call le iframe
if($gIsAppz === 1){
?>	

//---------------------------------------------------------------------
//call main window for mobile appz	
function gCallWindow(msgObj){
	if(typeof(window.parent) == 'object'){
		window.parent.postMessage(msgObj, '*');	
		}
	};

<?php 
	}
//---------------------------------------------------------------------
?>

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
	}


//---------------------------------------------------------------------
//prevent back button on mobile phone
window.location.hash = '#loading';
window.history.pushState(null, '', '#started');
window.onhashchange = function(){
	if(location.hash == '#loading'){
		window.history.pushState(null, '', '#started');
		}
	}

//---------------------------------------------------------------------
//global vars
var gLocaleLangDefault = '<?php echo $gLangDefault; ?>';
var gLocaleLang = '<?php echo $gLang; ?>';
var gServerPath = '<?php echo $gServerPath; ?>';
var gExerciceImagePath = '<?php echo $gExerciceImagePath; ?>';
var gPrintServer = '<?php echo $gPrintServer; ?>';
var gPdfViewer = '<?php echo $gPdfViewer; ?>';
var gSessionId = '<?php echo $gSessionId; ?>';
var gDebug = <?php echo $gDebug; ?>;
var gBrand = '<?php echo $gBrand; ?>';
var gStyle = '<?php echo $gStyle; ?>';
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
	$.getScript(gServerPath + 'js/lang/' + gBrand + '/jlang.' + strLang + '.js<?php echo $noCache; ?>')
		//success
		.done(function(){
			jDebug = new JDebug();
			//global language base on the loaded file from gLocaleLang
			jLang = new JLang();
			//main application 		
			jAppz = new JAppz();
			//init main appz
			jAppz.init();
			})
		//fail
		.fail(function(){
			gLangAttempt++;
			//set to default
			gLocaleLang = gLocaleLangDefault;
			//if fail then maybe we dont have this language use the basic one
			if(gLangAttempt < 5){
				getLangScript(gLocaleLang);
				}

			});
	};

</script>
</body>
</html>
