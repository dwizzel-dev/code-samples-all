<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	define for all globals 


	IMPORTANT: VERSION BETWEEN PROD AND DEV OF DATABASES CONNECTIONS

		
*/
//-----------------------------------------------------------------------------------------------

// DEFINE
define('VERSION', '1.0');
define('BUILD', '004'); 
define('IS_DEFINED', 1);
define('EOL', "\n");
define('TAB', "\t");

// DEFAULT BRANDING AND VERSIONING
define('DEFAULT_BRAND', '000');
define('DEFAULT_VERSIONING', '004');

// ABSOLUTE PATH
define('DIR', '/var/www/mobile....@physiotec.ca/dev/keywords.com/');
define('DIR_BASE_CLASS', DIR.'class/');
define('DIR_BASE_INC', DIR.'inc/');
define('DIR_BASE_LOGS', DIR.'logs/');

// DATABASE TYPES
define('DB_DRIVER', 'mysql');/*the db drivers*/
define('DB_TYPE', 'mysql');/*the type of database*/

// DATABASE CONN DEV
/*
define('DB_DATABASE', 'physiotec_dev');
define('DB_HOSTNAME', 'localhost');
define('DB_PORT', '3306');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
define('DB_PREFIX', '');
*/

// DATABASE CONN FAST SERVER DEV
define('DB_DATABASE', 'physiotec');
define('DB_HOSTNAME', '66.135.33.221');
define('DB_PORT', '3306');
define('DB_USERNAME', '');
define('c', '');
define('DB_PREFIX', '');

//SHOW DEBUG
define('SHOW_DEBUG', false);
define('ENABLE_LOG', false);
define('ERROR_REPORT_LEVEL', 0);

//USER AND CLIENT SALT - CIPHER
define('PASS_CYPHER_SALT', 'Z/F-b@>V2w.RvaJ');

//LANG
define('DEFAULT_LOCALE_LANG', 'en_US');



//END