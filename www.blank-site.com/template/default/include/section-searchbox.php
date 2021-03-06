<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	le search box a l'interieur des widgets


*/
//doit etre caller de facon global 
//car a perdu son scope dans le call de class/directphp.php

global $oGlob, $oReg;

$tmpStrWidgetNameUID = 'widget-searchbox-exercises-'.rand();

?>
<!-- loaded css -->
<link rel="stylesheet" href="<?php echo PATH_CSS.'searchbox-exercises.'.DEFAULT_VERSIONING.'.css'; ?>" type="text/css">
<!-- search box layer -->
<div class="section-searchbox-exercises noprint <?php echo $tmpStrWidgetNameUID; ?> "></div>
<!-- loaded script -->
<!--<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jphysiotec.min.js" type="text/javascript"></script>-->
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jdebug.js" type="text/javascript"></script>
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jlang.js" type="text/javascript"></script>
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jutils.js" type="text/javascript"></script>
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jserver.js" type="text/javascript"></script>
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jcomm.js" type="text/javascript"></script>
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jsearch.js" type="text/javascript"></script>
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jappz.js" type="text/javascript"></script>
<script src="<?php echo PATH_JS.DEFAULT_BRAND.'/'.DEFAULT_VERSIONING; ?>/jautocomplete.js" type="text/javascript"></script>
<!-- locale script -->
<script>
jQuery(document).ready(function($){
	//main application 		
	new JAppz({
		jdebug: new JDebug({
			bAppz: false,
			bDebug: true,
			}).init(),
		sessionId: 0,
		localeLang: '<?php echo $oGlob->get('lang'); ?>',	
		serverFormProcess: '<?php echo PATH_FORM_PROCESS; ?>',
		serverCashPath: '<?php echo PATH_CACHE_JS; ?>',
		serverImagePath: '<?php echo PATH_IMAGE; ?>',
		mainContainer: '.section-block',
		currentSearchedWord: '',
		focusOnInput: false,	
		searchContainer: '.<?php echo $tmpStrWidgetNameUID; ?>',
		}).init();
	});	
</script>
<?php

unset($tmpStrWidgetNameUID);


