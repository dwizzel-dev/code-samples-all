<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	included css

*/


foreach($arrOutput['css'] as $k=>$v){
	echo '<link rel="stylesheet" href="'.$v.'" type="text/css">'.EOL;
	}


//END