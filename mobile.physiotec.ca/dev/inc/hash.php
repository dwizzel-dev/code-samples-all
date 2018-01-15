<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	static variables availabal globaly (that I hate)

*/
//------------------------------------------------------------------


//global available locales languages key -> value translated
$oReg->get('glob')->set('system-locales', array(
	'en_US' => translate('english'),	
	'fr_CA' => translate('french - canada'),
	'fr_FR' => translate('french - france'),
	'es_MX' => translate('spanish'),
	'nl_NL' => translate('dutch'),
	'pt_PT' => translate('portuguese')
	));

	
	
//END