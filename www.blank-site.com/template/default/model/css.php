<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	css model

*/


//la font doit aussi etre dans le fichier de l'editeur : .\template\default\model\content-item.php
//et aussi le style sheet qui overwrote dans l'editeur: .\admin\js\editor.with-moxiecut\skins\lightgray\content.min.css

//push le global
$arrOutput['css']['stylesheet'] = array(
	'https://fonts.googleapis.com/css?family=Raleway:300,400,500,600,700,800,900',
	PATH_CSS.'global.css',
	PATH_CSS.'responsive.css',
	PATH_CSS.'print.css',
	);

//END
$arrOutput['css']['style'] = array();

