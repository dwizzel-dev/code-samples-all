<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	REQUIRED FILES AND PROCEDURE BEFORE THE SITE TO CONTINUE

*/

//-----------------------------------------------------------------------------------------------
// BASE REQUIRED FUNC AND CLASSES	
	
//helpers function for all sites
require_once(DIR_INC.'helpers.php');

//functions for this specific sites
require_once(DIR_INC.'functions.php');

//change the error handling if it is defined in the function.php or helpers.php file
if(function_exists('phpErrorHandler')){
	set_error_handler('phpErrorHandler');
	}

//required 
require_once(DIR_CLASS.'globals.php');
require_once(DIR_CLASS.'utility.php');	
require_once(DIR_CLASS.'registry.php');
require_once(DIR_CLASS.'request.php');
require_once(DIR_CLASS.'log.php');
require_once(DIR_CLASS.'json.php');
require_once(DIR_CLASS.'response.php');
require_once(DIR_CLASS.'database.php');
require_once(DIR_CLASS.'session.php');
require_once(DIR_CLASS.'cipher.php');
require_once(DIR_CLASS.'errors.php');

//register new class too the registry to simplify arguments passing to other classes
$oReg = new Registry();
$oReg->set('glob', new Globals());	
$oReg->set('utils', new Utility($oReg));		
$oReg->set('log', new Log($oReg));
$oReg->set('err', new Errors($oReg));
$oReg->set('req', new Request($_GET, $_POST));
$oReg->set('resp', new Response(new Json()));

//static variables will be set in the $oReg->Global object
require_once(DIR_INC.'hash.php');

//minor check on sess db connection that will be made in session class directly
$oReg->set('sess', new Session($oReg));
if(!$oReg->get('sess')->getStatus()){
	exit('ERR: NO DATABASE CONNECTION[db-sess]');
	}

//minor check on main db connection
$oReg->set('db', new Database(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, $oReg));
if(!$oReg->get('db')->getStatus()){
	exit('ERR: NO DATABASE CONNECTION[db]');
	}

//minor check on main US db connection
$oReg->set('db-ext', new Database(DB_TYPE, DB_US_HOSTNAME, DB_US_USERNAME, DB_US_PASSWORD, DB_US_DATABASE, $oReg));
if(!$oReg->get('db-ext')->getStatus()){
	exit('ERR: NO DATABASE CONNECTION[db-ext]');
	}

//start session
$oReg->get('sess')->start();



//END