<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	include in scripts javascript

*/

foreach($arrOutput['script'] as $k=>$v){
	echo '<script src="'.$v.'" type="text/javascript"></script>'.EOL;
	}


if(isset($arrOutput['script-load']) && is_array($arrOutput['script-load'])){	
	echo '<script type="text/javascript">'.EOL;
	echo 'jQuery(document).ready(function($){'.EOL;	
	foreach($arrOutput['script-load'] as $k=>$v){
		echo '$.getScript("'.$v.'");'.EOL;	
		}
	echo '});'.EOL;
	echo '</script>'.EOL;
	}
	
//END
