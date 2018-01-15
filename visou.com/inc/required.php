<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	requires and procedures and hash and lots of things to be able to satsrt a service or site

*/

//-----------------------------------------------------------------------------------------------
	
// DATE DEFAULT ZONE
date_default_timezone_set('America/New_York'); 

//helpers function for all sites
require_once(DIR_INC.'helpers.php');

//functions for this specific sites
require_once(DIR_INC.'functions.php');

//change the error handling if it is defined in the function.php or helpers.php file
if(function_exists('phpErrorHandler')){
	set_error_handler('phpErrorHandler');
	}

//required 
require_once(DIR_CLASS.'registry.php');
require_once(DIR_CLASS.'globals.php');
require_once(DIR_CLASS.'log.php');
require_once(DIR_CLASS.'errors.php');
require_once(DIR_CLASS.'json.php');
require_once(DIR_CLASS.'database.php');
require_once(DIR_CLASS.'cipher.php');

//register new class too the registry to simplify arguments passing to other classes
$oReg = new Registry();
$oReg->set('glob', new Globals());	
$oReg->set('log', new Log($oReg));
$oReg->set('err', new Errors($oReg));
$oReg->set('json', new Json());

//static variables will be set in the $oReg->Global object
require_once(DIR_INC.'hash.php');

//minor check on main db connection
$oReg->set('db', new Database(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, $oReg));
if(!$oReg->get('db')->getStatus()){
	exit('ERR: NO DATABASE CONNECTION[db]');
	}
//minor check on main db connection
$oReg->set('db-site', new Database(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE_SITE, $oReg));
if(!$oReg->get('db-site')->getStatus()){
	exit('ERR: NO DATABASE CONNECTION[db-site]');
	}




//END