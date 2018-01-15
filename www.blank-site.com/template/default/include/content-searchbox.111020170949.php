<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	le search box a l'interieur des contents


*/

//doit etre caller de facon global 
//car a perdu son scope dans le call de class/directphp.php

global $oGlob, $oReg;

?>
<div class="content-searchbox-exercises resizable">
	<input type="hidden" value="<?php echo $oGlob->get('lang'); ?>" name="lang">
	<input type="hidden" value="search-exercises" name="type">
	<div id="main-input" class="kw-searchbox"></div>
</div>
<!-- on va loader la librairie utiliser pour le autocomplete -->
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jdebug.js" type="text/javascript"></script>
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jutils.js" type="text/javascript"></script>
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jserver.js" type="text/javascript"></script>
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jcomm.js" type="text/javascript"></script>
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jsearch.js" type="text/javascript"></script>
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jappz.js" type="text/javascript"></script>
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jautocomplete.js" type="text/javascript"></script>
<script>

//---------------------------------------------------------------------
//global vars
var gKwDb = '';
var gDebug = 1;
var gIsAppz = 0;
var gLocaleLang = '<?php echo $oGlob->get('lang'); ?>';
var gLocaleLangDefault = '<?php echo LANG_DEFAULT; ?>';
var gServerPath = '';
var gServerPathJs = '<?php echo PATH_JS; ?>';
var gServerCachePath = '<?php echo PATH_CACHE_JS; ?>';
var gLangAttempt = 0;
var gBrand = '<?php echo DEFAULT_BRAND; ?>';
var gVersioning = '<?php echo DEFAULT_VERSIONING; ?>';
var gSessionId = '0';
var gPathFormProcess = '<?php echo PATH_FORM_PROCESS; ?>';

//---------------------------------------------------------------------
//global object
var jDebug, jAppz, jLang;

//---------------------------------------------------------------------
//main loader, need change for intel XDK
jQuery(document).ready(function($){
	//load the lang file
	getLangScript(gLocaleLang);
	});	

//---------------------------------------------------------------------
//load a script if fail try another language
function getLangScript(strLang){
	//load the lang file
	$.getScript(gServerPathJs + gBrand + '/' + gVersioning + '/lang/jlang.' + strLang + '.js')
		//success
		.done(function(){
			//message
			console.log('success load: ' + this.url);	
			//debugger	
			jDebug = new JDebug();
			//global language base on the loaded file from gLocaleLang
			jLang = new JLang();
			//main application 		
			jAppz = new JAppz({
				'basediv':'kw-content-result',
				'maincontainer': '.content-full',
				'word': '<?php echo $oGlob->get('current-search-word'); ?>',
				'focusoninput': true,	
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
			if(gLangAttempt < 5){
				getLangScript(gLocaleLang);
				}
			});
	};

	 
</script>