<?php
/**
@auth:	Dwizzel
@date:	07-06-2012
@info:	validation de session direct


*/

if(isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID'].'' != ''){
	$sess_id_cook = $oReg->get('sess')->get('sess_id');
	$user_id = $oReg->get('sess')->get('user_id');
	if(!$sess_id_cook || !$user_id){
		Header('Location: '.PATH_FORM_PROCESS.'?&send_logout=1&err=103');
		exit();
		}
	if($sess_id_cook != $oReg->get('sess')->getSessionID() || $user_id == ''){
		Header('Location: '.PATH_FORM_PROCESS.'?&send_logout=1&err=103');
		exit();
		}
	unset($sess);
	unset($user_id);
	unset($sess_id_cook);
}else{
	Header('Location: '.PATH_FORM_PROCESS.'?&send_logout=1&err=103');
	exit();
	}

//END