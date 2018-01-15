<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	define for all globals

*/
//-----------------------------------------------------------------------------------------------

// DEFINE
define('VERSION', '1.0');
define('BUILD', '001'); 
define('IS_DEFINED', 1);
define('EOL', "\n");
define('TAB', "\t");

// ABSOLUTE PATH
define('DIR', 'C:/wamp/www/visou.com/');
define('DIR_CLASS', DIR.'class/');
define('DIR_INC', DIR.'inc/');
define('DIR_LANG', DIR_INC.'lang/');
define('DIR_LOGS', DIR.'logs/');
define('DIR_SCRIPT', DIR.'script/');
define('DIR_IMAGES', DIR.'images/');
define('DIR_IMAGE_THUMBS', DIR_IMAGES.'/thumbs/');
define('DIR_IMAGE_PICTURES', DIR_IMAGES.'/pictures/');

//GRAB IMAGES FROM SERVER
define('PATH_GET_IMAGES', 'https://pierre:jake26@mobile....@physiotec.ca/dev/utilities/getimages.php?&img=');

// DATABASE TYPES
define('DB_DRIVER', 'mysql');/*the db drivers*/
define('DB_TYPE', 'mysql');/*the type of database*/

// DATABASE CONN
define('DB_DATABASE', 'physiotec');/*the database name*/
define('DB_DATABASE_SITE', 'visou');/*the database name*/
define('DB_HOSTNAME', 'localhost');/*the hostname*/
define('DB_PORT', '3306');
define('DB_USERNAME', '');/*the user connecting to databasse*/
define('DB_PASSWORD', '');/*the psw of the user*/
define('DB_PREFIX', '');/*the table prefix*/

//SHOW DEBUG
define('SHOW_DEBUG', true);
define('ENABLE_LOG', true);

//LANG
define('DEFAULT_LOCALE_LANG', 'en_US');

//END