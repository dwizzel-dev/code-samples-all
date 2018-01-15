<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	define for all admin, globals

		
*/
//-----------------------------------------------------------------------------------------------

// DEFINE
define('VERSION', '1.0.0');
define('IS_DEFINED', 1);
define('EOL', "\n");
define('TAB', "\t");

//BASIC
date_default_timezone_set('America/New_York');

//EDIT SITE CONFIG DEFINE
define('SITE_DEFINE_EDITABLE','define-editable.php');

//LE TEMPLATE DE BASE DU SITE VISIBLE
define('TEMPLATE_NAME', 'default');
define('ADMIN_TEMPLATE_NAME', '31102016/');

// ABSOLUTE PATH
define('DIR', 'C:/wamp/www/www.blank-site.com/');/*compltete directory of the web site*/
define('DIR_ADMIN', DIR.'admin/');
define('DIR_CLASS', DIR_ADMIN.'class/'.ADMIN_TEMPLATE_NAME);
define('DIR_INC', DIR_ADMIN.'inc/'.ADMIN_TEMPLATE_NAME);
define('DIR_MEDIA', DIR.'images/'.TEMPLATE_NAME.'/');
define('DIR_MEDIA_WIDGET', DIR_MEDIA.'widget/');
define('DIR_LANG', DIR_INC.'lang/');
define('DIR_SCRIPTS', DIR_ADMIN.'scripts/');
define('DIR_COURRIEL_MESSAGE', DIR_INC.'email/');
define('DIR_GENERATE_LANG', DIR.'inc/lang/');
define('DIR_GENERATE_ADMIN_LANG', DIR_INC.'lang/');
define('FILE_GENERATED_HASH', DIR.'inc/hash.php');
define('DIR_LOGS', DIR_ADMIN.'temp/logs/');
//cache client
define('DIR_CACHE', DIR.'temp/cache/');
//render des array de exercises, keywords
define('DIR_RENDER_EXERCISES', DIR_CACHE.'exercises/');
define('DIR_RENDER_KEYWORDS', DIR_CACHE.'keywords/');
define('DIR_RENDER_CATEGORIES', DIR_CACHE.'categories/');
define('DIR_RENDER_KW_JS', DIR_CACHE.'js/');
define('DIR_RENDER_KW_PHP', DIR_CACHE.'search/');

//CSV EXPORT
define('DIR_CSV', DIR.'temp/csv/');

// RELATIVE PATH
define('PATH_WEB', '/admin/');
define('PATH_CSS', PATH_WEB.'css/'.ADMIN_TEMPLATE_NAME);
define('PATH_JS', PATH_WEB.'js/'.ADMIN_TEMPLATE_NAME);
define('PATH_BASIC_JS', PATH_WEB.'js/');
define('PATH_IMAGE', PATH_WEB.'images/');
define('PATH_FORM_PROCESS', PATH_WEB.'process-form.php');
define('PATH_FILE_DEFAULT', PATH_WEB.'index.php');
define('PATH_OFFLINE', PATH_WEB.'offline.php');
define('PATH_SERVICE', PATH_WEB.'service.php');
define('PATH_GENERATE_IMAGE', PATH_WEB.'image.php?');

define('PATH_WEB_SITE', 'http://www.blank-site.com/');
define('PATH_WEB_MEDIA', PATH_WEB_SITE.'images/'.TEMPLATE_NAME.'/');
define('PATH_WEB_MEDIA_WIDGET', PATH_WEB_MEDIA.'widget/');
define('PATH_WEB_CSS', PATH_WEB_SITE.'css/'.TEMPLATE_NAME.'/');
define('PATH_CSV', PATH_WEB_SITE.'temp/csv/');
define('PATH_IMAGE_EXERCISE', PATH_WEB_MEDIA.'exercises/');

// LANG
define('LANG_ENABLED', 'fr_CA,en_US');
define('LANG_DEFAULT', 'fr_CA');

// OTHER
define('LIMIT_PER_PAGE', 30);

// DATABASE
define('DB_DRIVER', 'mysql');
define('DB_TYPE', 'mysql');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
//regular site
define('DB_DATABASE', 'blanksite');
define('DB_PREFIX', 'blanksite_');
//exercises generation
define('DB_DATABASE_VISOU', 'visou');
define('DB_PREFIX_VISOU', '');

// DATABASE SESSION CONN
define('DB_SESS_DRIVER', 'mysql');/*the db drivers*/
define('DB_SESS_TYPE', 'mysql');/*the type of database*/
define('DB_SESS_DATABASE', 'session');/*the database name*/
define('DB_SESS_HOSTNAME', 'localhost');
define('DB_SESS_PORT', '3306');
define('DB_SESS_USERNAME', '');
define('DB_SESS_PASSWORD', '');
define('DB_SESS_TABLE', 'ws_sessions');
define('DB_SESS_PREFIX', '');/*the table prefix*/

//MAIL
define('__EMAIL_ORIGINE_COURRIEL__', '');
define('__EMAIL_ORIGINE_NOM__', 'BLANK-SITE');

// RANKING
define('SITE_NAME', 'BLANK-SITE');

//PATH DES REPERTORE D"IMAGE
define('PATH_IMAGE_ZOOM', '.zoom');
define('PATH_IMAGE_RESPONSIVE', '.responsive');

//SIZE
define('RESPONSIVE_SIZE_W', 768);
define('RESPONSIVE_SIZE_H', 0);

define('PHOTO_SIZE_W', 1280);
define('PHOTO_SIZE_H', 0);

//PATH DES REPERTOIRE IMAGES QUI DOIVENT AVOIR PLUSIEURS FORMAT
define('PATH_IMAGE_ACCEPT', 'resizable/;widget/section/;widget/frontpage/;');

//PATH DES REPERTORE D'IMAGE ICONS ET UATRES
define('DIR_SOCIALMEDIA_ICONS', DIR_MEDIA.'widget/socialmedia/icons/default/size2/');
define('PATH_SOCIALMEDIA_ICONS', PATH_WEB_MEDIA.'widget/socialmedia/icons/default/size2/');
define('DEFAULT_NO_ICON', 'no_image.png');
define('DEFAULT_NO_IMAGE', 'no_image.jpg');

//SHOW DEBUG
define('SHOW_DEBUG', true);
define('ENABLE_LOG', false);

//OTHER
define('SITE_IS_DOWN', false);
define('ENABLE_LOGIN', true);
define('ERROR_LEVEL', E_ERROR);
define('CAPTCHA_MULTIPLIER', 2);
define('REMOTE_ADDR_ACCEPTED', '127.0.0.1');
define('MIN_SESSION_STRLEN', 26);

// MODEL DEFAULT
define('MODEL_DEFAULT_HEADER','header.php');
define('MODEL_DEFAULT_FOOTER','footer.php');
define('MODEL_DEFAULT_META','meta.php');
define('MODEL_DEFAULT_PREPEND','prepend.php');
define('MODEL_DEFAULT_CSS','css.php');
define('MODEL_DEFAULT_SCRIPT','script.php');
define('MODEL_DEFAULT_APPEND','append.php');

//CONTROLLER DEFAULT
define('CONTROLLER_DEFAULT_HOME','home');
define('CONTROLLER_DEFAULT_PAGE','page');
define('CONTROLLER_DEFAULT_404','404');
define('CONTROLLER_DEFAULT_LOGIN','login');

//VIEWS
define('VIEW_HOME','home');
define('VIEW_DEFAULT','page');
define('VIEW_404','404');

//CONTROLELR AFFICHAGE ETC...
define('DIR_TEMPLATE', DIR_ADMIN.'template/'.ADMIN_TEMPLATE_NAME);
define('DIR_VIEWS', DIR_TEMPLATE.'views/');
define('DIR_CONTROLLER', DIR_TEMPLATE.'controller/');
define('DIR_MODEL', DIR_TEMPLATE.'model/');
define('DIR_WIDGET', DIR_TEMPLATE.'widget/');

//META
define('META_CREATOR', 'BLANK-SITE');
define('META_TITLE', 'BLANK-SITE Admin');
define('META_SEPARATOR', '|');
define('META_DESCRIPTION', '');
define('META_KEYWORDS', '');

//cache
define('ENABLE_CACHING', false);

//path rewrite ou pas de l'affichage des url
define('SIMPLIFIED_SITE_PATH', true);

//path rewrite ou pas de l'affichage des url de admin site
define('SIMPLIFIED_ADMIN_SITE_PATH', false);

//END


