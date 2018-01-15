<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	script model

*/


//push le global
array_push($arrOutput['script'], PATH_JS.'respond.min.js');
array_push($arrOutput['script'], PATH_JS.'json2.js');
//array_push($arrOutput['script'], PATH_JS.'jquery-1.7.2.js');
array_push($arrOutput['script'], PATH_JS.'jquery-3.1.1.min.js');
array_push($arrOutput['script'], PATH_JS.'modernizr.js');
array_push($arrOutput['script'], PATH_JS.'global.js');

//javascript load
$arrOutput['script-load'] = array(
	//PATH_JS.'bootstrap.js',
	);




//END