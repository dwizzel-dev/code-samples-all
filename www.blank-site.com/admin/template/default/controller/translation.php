<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default controller for translation editing


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
$arrOutput['content']['title'] = _T('translations');
$arrOutput['content']['text'] = '';
$arrOutput['content']['class'] = '';

//les metas
$arrOutput['meta']['title'] = $arrOutput['content']['title'];
$arrOutput['meta']['description'] = META_DESCRIPTION;
$arrOutput['meta']['keywords'] =  META_KEYWORDS;
$arrOutput['meta']['lang'] = $oGlob->get('lang');	

require_once(DIR_CLASS.'translation.php');
$oTranslation = new Translation($oReg);

//le dropdown
$arrOutput['content']['page-dropdown'] = $oTranslation->getDataFields();

//la view
$oGlob->set('page_view', 'translation');

//on set toutes les vars de la view dans le array avec les differents header, meta et du footer
require_once(DIR_MODEL.MODEL_DEFAULT_CSS);

//les cripts
require_once(DIR_MODEL.MODEL_DEFAULT_SCRIPT);

//ce qi vient avant tout
require_once(DIR_MODEL.MODEL_DEFAULT_PREPEND);

//les header
require_once(DIR_MODEL.MODEL_DEFAULT_HEADER);

//le footer
require_once(DIR_MODEL.MODEL_DEFAULT_FOOTER);

//ce qi vient apres tout
require_once(DIR_MODEL.MODEL_DEFAULT_APPEND);	

//on load la view
require_once(DIR_VIEWS.$oGlob->get('page_view').'.php');






//END





