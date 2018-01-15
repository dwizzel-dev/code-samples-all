<?php
/**
@auth: Dwizzel
@date: 00-00-0000
@info: le sitemap generator
@description:
	
	1. toutes les pages par langues
	2. toutes les nouvelles par langues
	3. tout les exercices par langue
	4. le sitemap.xml
	
	
	
*/

//EXIT('COMMENT LINE '.__LINE__.' TO RUN'."\n"); //remove to insert data

//-------------------------------------------------------------------------------------------------------------

//CONTENTTYPE
header('Content-Type: text/plain; charset=utf-8', true);

// ERROR REPORTING
error_reporting(E_ALL);

// base required
if(!defined('IS_DEFINED')){
	require_once('../define.php');
	}

// BASE REQUIRED
require_once(DIR_INC.'required.php');

//required
require_once(DIR_INC.'helpers.php');
require_once(DIR_CLASS.'registry.php');
require_once(DIR_CLASS.'database.php');
require_once(DIR_CLASS.'log.php');

// register new class too the registry to simplify arguments passing to other classes
$oReg = new Registry();
$oReg->set('log', new Log($oReg));
$oReg->set('db-site', new Database(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, $oReg));
$oReg->set('db-exercise', new Database(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE_VISOU, $oReg));

//holder des links 
$arrXmlLinks = array();

$arrPageContentExceptionIds = array(1,2,3);



//-------------------------------------------------------------------------------------------------------------
//	1.	les liens du site static par langue et la langue alternative

$query = 'SELECT '.DB_PREFIX.'links.id AS "link_id", '.DB_PREFIX.'content.date_modified AS "date", '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'languages.prefix AS "prefix" FROM '.DB_PREFIX.'content LEFT JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'content.link_id = '.DB_PREFIX.'links.id LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'content.language_id WHERE '.DB_PREFIX.'content.status = "1" AND '.DB_PREFIX.'links.extern = "0" AND '.DB_PREFIX.'content.id NOT IN ('.implode(',',array_values($arrPageContentExceptionIds)).') ORDER BY '.DB_PREFIX.'content.id ASC;';
//fetch
$rs = $oReg->get('db-site')->query($query);
//minor check
if($rs && $rs->num_rows){
	//fill the array
	foreach($rs->rows as $k=>$v){
		array_push($arrXmlLinks, array(
			'link_id' => $v['link_id'],
			'loc' => PATH_WEB_SITE.$v['prefix'].'/'.$v['path'].'/',
			'changefreq' => 'monthly',
			'lastmod' => $v['date'],
			'alternate' => array(),
			));
		}
	}
//clean
unset($rs, $k, $v);	
//pour tout les link on va chercher le lien alternatif dans une autre langue si il y a 
foreach($arrXmlLinks as $k=>$v){
	$query = 'SELECT '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'languages.prefix AS "prefix" FROM '.DB_PREFIX.'links_alternate LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'links_alternate.language_id LEFT JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'links.id = '.DB_PREFIX.'links_alternate.alternate_id WHERE '.DB_PREFIX.'links_alternate.link_id = "'.$v['link_id'].'" ORDER BY '.DB_PREFIX.'links_alternate.id ASC;';
	//fetch
	$rs = $oReg->get('db-site')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		//fill the array
		foreach($rs->rows as $k2=>$v2){
			array_push($arrXmlLinks[$k]['alternate'], array(
				'hreflang' => $v2['prefix'],
				'href' => PATH_WEB_SITE.$v2['prefix'].'/'.$v2['path'].'/',
				));
			}
		}	
	}
//clean
unset($rs, $k, $v, $k2, $v2);	





//-------------------------------------------------------------------------------------------------------------
//	2.	on va chercher les nouvelles et les categories


//tmp holder
$arrTmpNews = array();

//a. le premier niveau
$query = 'SELECT '.DB_PREFIX.'links.id AS "link_id", '.DB_PREFIX.'news_category.id AS "id", '.DB_PREFIX.'news_category.parent_id AS "parent_id", '.DB_PREFIX.'news_category.date_modified AS "date", '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'languages.prefix AS "prefix" FROM '.DB_PREFIX.'news_category LEFT JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'news_category.link_id = '.DB_PREFIX.'links.id LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'news_category.language_id WHERE '.DB_PREFIX.'news_category.status = "1" AND '.DB_PREFIX.'links.extern = "0" ORDER BY '.DB_PREFIX.'news_category.id ASC;';
//fetch
$rs = $oReg->get('db-site')->query($query);
//minor check
if($rs && $rs->num_rows){
	//fill the array
	foreach($rs->rows as $k=>$v){
		array_push($arrTmpNews, array(
			'id' => $v['id'],
			'parent_id' => $v['parent_id'],
			'link_id' => $v['link_id'],
			'loc' => PATH_WEB_SITE.$v['prefix'].'/'.$v['path'].'/',
			'changefreq' => 'weekly',
			'lastmod' => $v['date'],
			'alternate' => array(),
			));
		}
	}
//clean
unset($rs, $k, $v);	
//pour tout les link on va chercher le lien alternatif dans une autre langue si il y a 
foreach($arrTmpNews as $k=>$v){
	$query = 'SELECT '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'languages.prefix AS "prefix" FROM '.DB_PREFIX.'links_alternate LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'links_alternate.language_id LEFT JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'links.id = '.DB_PREFIX.'links_alternate.alternate_id WHERE '.DB_PREFIX.'links_alternate.link_id = "'.$v['link_id'].'" ORDER BY '.DB_PREFIX.'links_alternate.id ASC;';
	//fetch
	$rs = $oReg->get('db-site')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		//fill the array
		foreach($rs->rows as $k2=>$v2){
			array_push($arrTmpNews[$k]['alternate'], array(
				'hreflang' => $v2['prefix'],
				'href' => PATH_WEB_SITE.$v2['prefix'].'/'.$v2['path'].'/',
				));
			}
		}	
	}
//clean
unset($rs, $k, $v, $k2, $v2);		
//on va chercher les nouvelles pour chaque sous categories
foreach($arrTmpNews as $k=>$v){
	if(intVal($v['parent_id']) != 0){
		$query = 'SELECT '.DB_PREFIX.'news.id AS "id", '.DB_PREFIX.'news.alias AS "alias", '.DB_PREFIX.'news.date_modified AS "date" FROM '.DB_PREFIX.'news WHERE '.DB_PREFIX.'news.category_id = "'.$v['id'].'" ORDER BY '.DB_PREFIX.'news.id ASC;';
		//fetch
		$rs = $oReg->get('db-site')->query($query);
		//minor check
		if($rs && $rs->num_rows){
			//fill the array
			foreach($rs->rows as $k2=>$v2){
				array_push($arrTmpNews, array(
					'loc' => $v['loc'].$v2['alias'].'/'.$v2['id'].'/',
					'changefreq' => 'never',
					'lastmod' => $v2['date'],
					'alternate' => array(),
					));
				}	
			}
		}
	}
//clean
unset($rs, $k, $v, $k2, $v2);
//on va les rajouter a la liste actuelle
foreach($arrTmpNews as $k=>$v){
	array_push($arrXmlLinks, array(
		'loc' => $v['loc'],
		'changefreq' => $v['changefreq'],
		'lastmod' => $v['lastmod'],
		'alternate' => array(),
		));
	}
//clean
unset($k, $v, $arrTmpNews);



	
	
//-------------------------------------------------------------------------------------------------------------
//	3.	on va chercher la liste des exercices et les hreflang avec
//		on va se baser sur les array pregenere pour le javascript et la DB php
			

//tmp holder
$arrTmpExercises = array();	
			
//on va chercher les links by key pour la page details
$arrPathByLangCode = array();
//query
$query = 'SELECT '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'languages.prefix AS "prefix" FROM '.DB_PREFIX.'links LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'links.language_id WHERE '.DB_PREFIX.'links.keyindex LIKE "exercises-details-%";';	
//fetch
$rs = $oReg->get('db-site')->query($query);
//minor check
if($rs && $rs->num_rows){
	//fill the array
	foreach($rs->rows as $k=>$v){
		$arrPathByLangCode[$v['prefix']] = $v['path'];
		}
	}
//clean
unset($rs, $k, $v);

// LANG BY key=>value, 
// IMPORTANT: not the same as the web site 
$arrExerciseLangCodeByKey = array(
	1 => 'en',
	2 => 'fr',
	3 => 'es',	
	);

//select from DB des exercises
foreach($arrExerciseLangCodeByKey as $k=>$v){
	//query
	$query = 'SELECT exercise_id, url_title, ref_id, pict_0, title, date_modified FROM exercises WHERE locale = "'.$k.'" ORDER BY exercise_id ASC LIMIT 0, 1000000;';
	//fetch
	$rs = $oReg->get('db-exercise')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		//fill the array
		foreach($rs->rows as $k2=>$v2){
			if(isset($arrPathByLangCode[$v])){
				$strImage = PATH_WEB_MEDIA.'physiotec-logo.jpg';
				if($v2['pict_0'] != ''){
					$strImage = PATH_IMAGE_EXERCISE.$v2['exercise_id'].'-p0.jpg';
					}
				$arrTmpExercises[$v2['exercise_id']] = array(
					'id' => $v2['exercise_id'],
					'ref_id' => $v2['ref_id'],
					'loc' => PATH_WEB_SITE.$v.'/'.$arrPathByLangCode[$v].'/'.$v2['url_title'].'-'.$v2['exercise_id'].'/',
					'locale' => $k,
					'lastmod' => $v2['date_modified'],
					'image' => array(
						'loc' => $strImage,
						'caption' => htmlentities(html_entity_decode($v2['title'], ENT_QUOTES|ENT_HTML5, 'UTF-8'), ENT_QUOTES|ENT_XML1, 'UTF-8'),
						),
					'alternate' => array(),
					);
				}
			}
		}
	}
//clean
unset($rs, $k, $v, $k2, $v2);
		
//pour chacun on va checker si une langue alternative
foreach($arrTmpExercises as $k=>$v){
	//on va chercher si il y a autres exercice selon le ref_if
	$query = 'SELECT exercise_id, url_title, locale FROM exercises WHERE ref_id = "'.$v['ref_id'].'" AND locale <> "'.$v['locale'].'";';
	//fetch
	$rs = $oReg->get('db-exercise')->query($query);
	//minor check
	if($rs && $rs->num_rows){
		//fill the array
		foreach($rs->rows as $k2=>$v2){
			if(isset($arrExerciseLangCodeByKey[$v2['locale']]) && isset($arrPathByLangCode[$arrExerciseLangCodeByKey[$v2['locale']]])){
				array_push($arrTmpExercises[$k]['alternate'], array(
					'hreflang' => $arrExerciseLangCodeByKey[$v2['locale']],
					'href' => PATH_WEB_SITE.$arrExerciseLangCodeByKey[$v2['locale']].'/'.$arrPathByLangCode[$arrExerciseLangCodeByKey[$v2['locale']]].'/'.$v2['url_title'].'-'.$v2['exercise_id'].'/',
					));
				}
			}
		}
	}
//clean
unset($rs, $k, $v, $k2, $v2);	
//on va les rajouter a la liste actuelle
foreach($arrTmpExercises as $k=>$v){
	array_push($arrXmlLinks, array(
		'loc' => $v['loc'],
		'changefreq' => 'never',
		'image' => $v['image'],
		'lastmod' => $v['lastmod'],
		'alternate' => $v['alternate'],
		));
	}	
//clean
unset($k, $v, $arrTmpExercises);		
	
	


//-------------------------------------------------------------------------------------------------------------
//	4.	on va builder le xml sitemap
/*
	
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
	<url> 
		<loc>http://www.example.com/foo.html</loc> 
		 <lastmod>2017-02-15T19:28:19+00:00</lastmod>
		<changefreq>weekly</changefreq>
		<priority>1.00</priority>
		<image:image>
			<image:loc>http://example.com/image.jpg</image:loc>
			<image:caption>Dogs playing poker</image:caption>
		</image:image>
	</url>
	<url>
		<loc>http://www.example.com/english/</loc>
		<xhtml:link 
			rel="alternate"
			hreflang="es"
			href="http://www.example.com/deutsch/"
			/>
		<xhtml:link 
			rel="alternate"
			hreflang="fr"
			href="http://www.example.com/schweiz-deutsch/"
			/>
		<xhtml:link 
			rel="alternate"
			hreflang="en"
			href="http://www.example.com/english/"
			/>
	</url>
</urlset>	

*/

$strOutput = '<?xml version="1.0" encoding="UTF-8"?>'.EOL;
$strOutput .= '<urlset 
	xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
	xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" 
	xmlns:xhtml="http://www.w3.org/1999/xhtml" 
	>'.EOL;
foreach($arrXmlLinks as $k=>$v){
	$strOutput .= TAB.'<url>'.EOL;
	//location
	$strOutput .= TAB.TAB.'<loc>'.$v['loc'].'</loc>'.EOL;
	//last modification
	$strOutput .= TAB.TAB.'<lastmod>'.date('c', strtotime($v['lastmod'])).'</lastmod>'.EOL;
	//change frequence
	$strOutput .= TAB.TAB.'<changefreq>'.$v['changefreq'].'</changefreq>'.EOL;
	//image
	if(isset($v['image']) && is_array($v['image'])){
		$strOutput .= TAB.TAB.'<image:image>'.EOL;	
		$strOutput .= TAB.TAB.TAB.'<image:loc>'.$v['image']['loc'].'</image:loc>'.EOL;
		$strOutput .= TAB.TAB.TAB.'<image:caption>'.$v['image']['caption'].'</image:caption>'.EOL;	
		$strOutput .= TAB.TAB.TAB.'<image:title>'.$v['image']['caption'].'</image:title>'.EOL;	
		$strOutput .= TAB.TAB.'</image:image>'.EOL;		
		}
	//lien alternatif	
	if(isset($v['alternate']) && count($v['alternate']) > 0){
		foreach($v['alternate'] as $k2 => $v2){
			$strOutput .= TAB.TAB.'<xhtml:link'.EOL;
			$strOutput .= TAB.TAB.TAB.'rel="alternate"'.EOL;
			$strOutput .= TAB.TAB.TAB.'hreflang="'.$v2['hreflang'].'"'.EOL;
			$strOutput .= TAB.TAB.TAB.'href="'.$v2['href'].'"'.EOL;
			$strOutput .= TAB.TAB.TAB.'/>'.EOL;
			}
		}
	//
	$strOutput .= TAB.'</url>'.EOL;
	}
$strOutput .= '</urlset>';

$strOutput = str_replace(
	'http://www.blank-site.com/',
	'https://www....@physiotec.ca/',
	$strOutput);

//minor check
if($strOutput != ''){
	//show 
	$filePath = DIR.'sitemap.test.xml';
	echo $filePath.EOL;
	//file infos
	$fp = fopen($filePath, 'w');
	if($fp){
		fwrite($fp, $strOutput);
		}
	fclose($fp);
	}	

	
	





	
	
	


//SCRIPT END