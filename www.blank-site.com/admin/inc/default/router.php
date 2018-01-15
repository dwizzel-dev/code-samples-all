<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	basic routing vers le controlleur en lien avec admin/inc/links.php

*/


$arrTmpRouter = array(	
	'home' => array(
		'controller' => CONTROLLER_DEFAULT_HOME,
		),
	'links' => array(
		'controller' => 'links',
		),
	'content' => array(
		'controller' => 'content',
		),
	'news' => array(
		'controller' => 'news',
		),	
	'widget' => array(
		'controller' => 'widget',
		),	
	'menu' => array(
		'controller' => 'menu',
		),
	'users' => array(
		'controller' => 'users',
		),	
	'config' => array(
		'controller' => 'config',
		),	
	'datafields' => array(
		'controller' => 'datafields',
		),
	'translation' => array(
		'controller' => 'translation',
		),
	'login' => array(
		'controller' => 'login',
		),
	'logout' => array(
		'controller' => 'logout',
		),	
		
	);

	
//set it to global
if($oGlob->get('page') == ''){
	//default to home
	$oGlob->set('router', CONTROLLER_DEFAULT_HOME);
}else if(isset($arrTmpRouter[$oGlob->get('page')])){
	//set the controller si la bonne page et sub_page
	$oGlob->set('router', $arrTmpRouter[$oGlob->get('page')]['controller']);
}else{
	//error 404
	$oGlob->set('router', CONTROLLER_DEFAULT_404);
	}
	

	
//clean
unset($arrTmpRouter);


//END