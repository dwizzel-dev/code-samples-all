<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	basic links

*/

$arrTmpLinks = array( 
	
	//single
	'404' => '404',
	'home' => 'home',
	'links' => 'links',
	'menu' => 'menu',
	'login' => 'login',
	'logout' => 'logout',

	//widget
	'widget' => 'widget',
	'widget-items' => 'widget/items',	
	
	//content	
	'content-category' => 'content/category',
	'content-listing' => 'content/listing',
	'content-items' => 'content/items',

	//news
	'news-category' => 'news/category',
	'news-listing' => 'news/listing',
	'news-items' => 'news/items',	
	
	//users
	'users-listing' => 'users/listing',
	'users-items' => 'users/items',
	
	//globals
	'translation' => 'translation',
	'datafields' => 'datafields',
	'config' => 'config',
	
		
	);
	
foreach($arrTmpLinks as $k=>$v){
	if(SIMPLIFIED_ADMIN_SITE_PATH){
		$arrTmpLinks[$k] = '/'.$oGlob->get('lang_prefix').PATH_WEB.$v.'/';	
	}else{
		$arrTmpLinks[$k] = PATH_WEB.'index.php?&lang='.$oGlob->get('lang').'&path='.$v.'/';	
		}
	}	
		
//set it to global
$oGlob->set('links', $arrTmpLinks);

//clean
unset($arrTmpLinks);


//END