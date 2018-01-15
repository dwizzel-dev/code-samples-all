<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	function used by this site

*/


//---------------------------------------------------------------------------------------------------------------

function cleanLogout(&$reg, &$glob){
	//logout clean 	
	if(isset($reg)){	
		$reg->get('login')->doLogout();
		}	
	//and redirect
	Header('Location: '.$glob->getArray('links','home'));
	exit();			
	}
	
//END