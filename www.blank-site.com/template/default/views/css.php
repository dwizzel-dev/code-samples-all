<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	included css

*/

if(is_array($arrOutput['css']['stylesheet'])){
	foreach($arrOutput['css']['stylesheet'] as $k=>$v){
		echo '<link rel="stylesheet" href="'.$v.'" type="text/css">'.EOL;
		}
	}	
if(is_array($arrOutput['css']['style'])){
	foreach($arrOutput['css']['style'] as $k=>$v){
		echo $v.EOL;
		}
	}
	
	
//END