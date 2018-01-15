<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	script model

*/


//push le global
array_push($arrOutput['script'], PATH_JS.'respond.min.js');
array_push($arrOutput['script'], PATH_JS.'json2.js');
array_push($arrOutput['script'], PATH_JS.'jquery-1.7.2.js');
array_push($arrOutput['script'], PATH_JS.'bootstrap.js');
array_push($arrOutput['script'], PATH_JS.'global.js');
array_push($arrOutput['script'], PATH_JS.'bootstrap-datepicker.js');
array_push($arrOutput['script'], PATH_JS.'jquery.scrollTo-1.4.3.1-min.js');

array_push($arrOutput['script'], PATH_WEB.'media-manager/assets/dialog.js');
array_push($arrOutput['script'], PATH_WEB.'media-manager/IMEStandalone.js');

array_push($arrOutput['script'], PATH_BASIC_JS.'editor.with-moxiecut/tinymce.js'); //moxie-cut russian hack




$arrOutput['script-load'] = array();



//END