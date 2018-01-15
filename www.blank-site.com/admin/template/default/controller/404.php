<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	404 controller


*/

//on set la variable de base de output utilise par la view
$arrOutput = array(
	'css' => array(),
	'script' => array(),
	'prepend' => array(),
	'header' => array(),
	'meta' => array(),
	'content' => array(),
	'footer' => array(),
	'append' => array(),
	);

//on set toutes les vars de la view dans le array 
$arrOutput['content']['title'] = _T('404 title');
$arrOutput['content']['text'] = _T('404 content');

//les metas
$arrOutput['meta']['title'] = _T('404 title');
$arrOutput['meta']['description'] = META_DESCRIPTION;
$arrOutput['meta']['keywords'] =  META_KEYWORDS;
$arrOutput['meta']['lang'] = $oGlob->get('lang');	

//la view
$oGlob->set('page_view', VIEW_404);
	
//on set toutes les vars de la view dans le array avec les differents header, meta et du footer
require_once(DIR_MODEL.MODEL_DEFAULT_CSS);

//on set les cripts
require_once(DIR_MODEL.MODEL_DEFAULT_SCRIPT);

//ce qui veient avant tout
require_once(DIR_MODEL.MODEL_DEFAULT_PREPEND);

//on set les header
require_once(DIR_MODEL.MODEL_DEFAULT_HEADER);

//on load le footer
require_once(DIR_MODEL.MODEL_DEFAULT_FOOTER);

//ce qui veient apres
require_once(DIR_MODEL.MODEL_DEFAULT_APPEND);
	
//on load la view
require_once(DIR_VIEWS.$oGlob->get('page_view').'.php');



//END



