<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	basic lang loader files

*/

// GLOBAL LANG
if($oGlob->get('lang').'' != '' && file_exists(DIR_LANG.'lang.'.$oGlob->get('lang').'.php')){
	require_once(DIR_LANG.'lang.'.$oGlob->get('lang').'.php');
	}

//END