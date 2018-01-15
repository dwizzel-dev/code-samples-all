<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	define for all globals

		
*/
//-----------------------------------------------------------------------------------------------

// DEFINE
define('VERSION', '3.0');
define('BUILD', '121'); 
define('IS_DEFINED', 1);
define('EOL', "\n");
define('TAB', "\t");

// ABSOLUTE PATH
define('DIR', '/var/www/mobile....@physiotec.ca/dev/');
define('TRANSLATION_BASE_DIR', '/var/www/include/locale');
define('DIR_CLASS', DIR.'class/');
define('DIR_INC', DIR.'inc/');
define('DIR_LOGS', DIR.'logs/');

//USER PASSED
//define('PATH_USER', 'pierre:jake26@');

//SI BROWSER VA LE DEMANDER, SI APPZ VA ETRE INCLUT DANS LE PREMIER CALL DU IFRAME DE l'APPLICATION
define('PATH_USER', '');

// RELATIVE PATH
define('PATH_WEB', 'https://'.PATH_USER.'mobile....@physiotec.ca/dev/');
define('PATH_EXERCICE_IMAGE', 'https://'.PATH_USER.'dev....@physiotec.ca/img/exercise.php?param=');
//define('PATH_PRINT', 'https://'.PATH_USER.'dev....@physiotec.ca/pdf_templates/');
define('PATH_PRINT', 'https://'.PATH_USER.'dev....@physiotec.ca/pdf/program.pdf');
define('PATH_PDF_VIEWER', 'https://'.PATH_USER.'dev....@physiotec.ca/js/pdfjs.stable/web/viewer.php?file=');
define('PATH_PROGRAM_CLIENT_EMAIL', 'https://'.PATH_USER.'dev....@physiotec.ca/');

define('PATH_CSS', PATH_WEB.'css/');
define('PATH_JS', PATH_WEB.'js/');
define('PATH_IMAGE', PATH_WEB.'images/');
define('PATH_FILE_DEFAULT', PATH_WEB.'index.php');
define('PATH_OFFLINE', PATH_WEB.'offline.php');
define('PATH_SERVICE', PATH_WEB.'service.php');

// VIDEO
define('PATH_VIDEO_SPROUT', 'embed/');
define('PATH_VIDEO_FLIQZ', '//services.fliqz.com/smart/20100401/applications/e9be3ff0699547dc825bd262261fbf91/assets/[{VIDEOCODE}]/containers/videodiv/smarttag.js?width=100%25&amp;height=100%25');

// DATABASE TYPES
define('DB_DRIVER', 'mysql');/*the db drivers*/
define('DB_TYPE', 'mysql');/*the type of database*/

// DATABASE CONN
//define('DB_DATABASE', 'physiotec_dev');/*the database name*/
define('DB_DATABASE', 'physiotec_devel');/*the database name*/
define('DB_HOSTNAME', 'localhost');/*the hostname*/
define('DB_PORT', '3306');
define('DB_USERNAME', '');/*the user connecting to databasse*/
define('DB_PASSWORD', '');/*the psw of the user*/
define('DB_PREFIX', '');/*the table prefix*/

// DATABASE CONN
//define('DB_US_DATABASE', 'physiotec_dev_US');/*the database name*/
define('DB_US_DATABASE', 'physiotec_devel_US');/*the database name*/
define('DB_US_HOSTNAME', 'localhost');/*the hostname*/
define('DB_US_PORT', '3306');
define('DB_US_USERNAME', '');/*the user connecting to databasse*/
define('DB_US_PASSWORD', '');/*the psw of the user*/
define('DB_US_PREFIX', '');/*the table prefix*/

// DATABASE SESSION CONN
define('DB_SESS_DATABASE', 'session');/*the database name*/
define('DB_SESS_HOSTNAME', 'localhost');
define('DB_SESS_PORT', '3306');
define('DB_SESS_USERNAME', '');
define('DB_SESS_PASSWORD', '');
//define('DB_SESS_TABLE', 'mobile_sessions');
define('DB_SESS_TABLE', 'ws_sessions');
define('DB_SESS_PREFIX', '');/*the table prefix*/

//SHOW DEBUG
define('SHOW_DEBUG', false);
define('ENABLE_LOG', true);
define('ERROR_REPORT_LEVEL', E_ERROR);

//OTHER
define('SITE_IS_DOWN', false);
define('REMOTE_ADDR_ACCEPTED', '127.0.0.1');

//CACHE
define('ENABLE_CACHING', false);

//path rewrite ou pas
define('SIMPLIFIED_SITE_PATH', false);

//LIMITS AND NUMS
define('MAX_ROWS_AUTOCOMPLETE_RETURNED', 25); //retour du autocomplete max
define('MAX_LOG_ERROR', 5); 
define('MAX_SEARCH_NUM_ROWS', 800); //le max de retour des recherches exercies
define('MAX_CLIENT_NUM_ROWS', 200); //le max de retour des recherches client
define('MIN_SESSION_STRLEN', 32); //la longueur en char du session id

//USER AND CLIENT SALT - CIPHER
define('PASS_CYPHER_SALT', 'Z/F-b@>V2w.RvaJ');

//LANG
define('DEFAULT_LOCALE_LANG', 'en_US');

//FOR INDEX.PHP
define('DEFAULT_STYLE', 'oxygen');
define('DEFAULT_BRAND', '000');
define('DEFAULT_STATE', 'DEV');
define('DEFAULT_TITLE', 'Physiotec Server '.DEFAULT_STATE);
define('SITE_NAME', DEFAULT_TITLE.' V.'.VERSION.' B.'.BUILD);




//END


