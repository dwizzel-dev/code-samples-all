<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	script model

*/


//push le global

array_push($arrOutput['script'], 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js');
array_push($arrOutput['script'], 'https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js');
array_push($arrOutput['script'], 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js');

array_push($arrOutput['script'], PATH_JS.'jquery.scrollTo.min.js');

array_push($arrOutput['script'], PATH_WEB.'media-manager/assets/dialog.js');
array_push($arrOutput['script'], PATH_WEB.'media-manager/IMEStandalone.js');

array_push($arrOutput['script'], PATH_BASIC_JS.'editor.with-moxiecut/tinymce.js'); //moxie-cut russian hack




$arrOutput['script-load'] = array();



//END