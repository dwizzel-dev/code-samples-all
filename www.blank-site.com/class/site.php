<?php
class Site {
	
	private $reg;
	
	public function __construct($reg){
		$this->reg = $reg;
		}
	
	//------------------------------------------------------------------------------------------------	
	
	public function getLinks(){
		$query = 'SELECT '.DB_PREFIX.'links.id AS "id", '.DB_PREFIX.'links.keyindex AS "keyindex", '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'languages.code AS "code", '.DB_PREFIX.'languages.prefix AS "prefix", '.DB_PREFIX.'links.name AS "name", '.DB_PREFIX.'links.extern AS "extern" FROM '.DB_PREFIX.'links INNER JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'links.language_id WHERE '.DB_PREFIX.'links.status = "1" ORDER BY '.DB_PREFIX.'links.language_id;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;	
			}
		return false;
		}

	//------------------------------------------------------------------------------------------------	
	
	public function getRedirectLinks(){
		$query = 'SELECT '.DB_PREFIX.'links.ref_id AS "ref_id", '.DB_PREFIX.'links.path AS "path" FROM '.DB_PREFIX.'links WHERE '.DB_PREFIX.'links.has_moved = "1" AND '.DB_PREFIX.'links.ref_id IN (SELECT '.DB_PREFIX.'links.id FROM '.DB_PREFIX.'links WHERE '.DB_PREFIX.'links.status = "1" AND '.DB_PREFIX.'links.has_moved = "0");';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;	
			}
		return false;
		}

	
	//------------------------------------------------------------------------------------------------	
	
	public function getRoute(){	
		$query = 'SELECT '.DB_PREFIX.'content.access AS "access", '.DB_PREFIX.'content.id AS "id", '.DB_PREFIX.'content.controller AS "controller", '.DB_PREFIX.'content.link_id AS "link_id", '.DB_PREFIX.'links.path AS "path" FROM '.DB_PREFIX.'content INNER JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'content.link_id = '.DB_PREFIX.'links.id WHERE '.DB_PREFIX.'content.status = "1" AND '.DB_PREFIX.'content.is_catalogue_content = "0" ;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;
			}
		return false;
		}	
		
	//------------------------------------------------------------------------------------------------	
	
	public function getNewsRoute(){	
		$query = 'SELECT "news" AS "controller", "0" AS "id", '.DB_PREFIX.'news_category.link_id AS "link_id", '.DB_PREFIX.'links.path AS "path" FROM '.DB_PREFIX.'news_category INNER JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'news_category.link_id = '.DB_PREFIX.'links.id WHERE '.DB_PREFIX.'news_category.status = "1";';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;
			}
		return false;
		}	
	
	//------------------------------------------------------------------------------------------------	
	
	public function getCatalogueCategoriesRoute(){
		require_once(DIR_CLASS.'category.php');
		$oCategory = new Category($this->reg);
		$arrCatalogueCatPath = $oCategory->getFirstLevelCategoryPathForRouter();
		//
		if(is_array($arrCatalogueCatPath)){
			$arr = array();
			//on va chercher tout les content qui recoivent des parametres de produit du catalogue EX: /catalogue/nfl/mens/shirt/
			$query = 'SELECT '.DB_PREFIX.'content.controller AS "controller", '.DB_PREFIX.'links.path AS "path" FROM '.DB_PREFIX.'content INNER JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'content.link_id = '.DB_PREFIX.'links.id WHERE '.DB_PREFIX.'content.status = "1" AND '.DB_PREFIX.'content.is_catalogue_content = "1" ;';
			$rs = $this->reg->get('db')->query($query);
			if($rs->num_rows){
				//pour chacun on rajoute le path des categories du catalogue
				foreach($rs->rows as $k=>$v){
					foreach($arrCatalogueCatPath as $k2=>$v2){
						array_push($arr, array(
							'id'=>$v2['id'],
							'controller'=>$v['controller'],
							'path'=>$v['path'].'/'.$v2['alias'],
							));
						}
					}
				return $arr;
				}
			}	
		return false;
		}
		
	//------------------------------------------------------------------------------------------------	
	
	public function getContent($id){	
		$query = 'SELECT '.DB_PREFIX.'languages.code AS "code", '.DB_PREFIX.'content.bgimage AS "bgimage", '.DB_PREFIX.'content.content AS "content", '.DB_PREFIX.'content.css_class AS "css_class", '.DB_PREFIX.'content.title AS "title", '.DB_PREFIX.'content.meta_title AS "meta_title", '.DB_PREFIX.'content.meta_description AS "meta_description", '.DB_PREFIX.'content.meta_keywords AS "meta_keywords", '.DB_PREFIX.'content.view AS "view" FROM '.DB_PREFIX.'content INNER JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'content.language_id WHERE '.DB_PREFIX.'content.status = "1" AND '.DB_PREFIX.'content.id = "'.$id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0];
			}
		return false;
		}	
		
	
	//------------------------------------------------------------------------------------------------	
	
	public function setContentHits($id){	
		$query = 'UPDATE '.DB_PREFIX.'content SET '.DB_PREFIX.'content.hits = '.DB_PREFIX.'content.hits + 1 WHERE '.DB_PREFIX.'content.id = "'.$id.'";';
		$this->reg->get('db')->query($query);
		return true;
		}	


	//------------------------------------------------------------------------------------------------	

	public function getBreadcrumbHome(&$oGlob){
		//on invert les links
		$arrLinks = array_flip($oGlob->get('links'));
		//on push le home au debut
		//le link
		$strHomeLink = $oGlob->getArray('links-by-key', 'home-'.$oGlob->get('lang_prefix'));
		//le title via le arr qui a ete flip
		$strHomeTitle = $oGlob->getArray('links-name-by-id',$arrLinks[$strHomeLink]);
		//
		return array(
			'text' => $strHomeTitle,
			'link' => $strHomeLink,
			);
		}


	//------------------------------------------------------------------------------------------------	

	public function getBreadcrumbFromPath($strContentPath, &$oGlob){
		//check si vide
		if($strContentPath != ''){
			//on va splitter le path et chercher dans le router
			$arrPath = explode('/', $strContentPath);
			//on clean les vides
			foreach($arrPath as $k=>$v){
				if($v == ''){
					unset($arrPath[$k]);
					}
				}
			$arrPath = $arrPath;
			//le container des subdivision du path
			$arrSubPath = array();
			//on va looper et chercher si le path existe si oui on le mais en lien si non il sera vide et non cliquable
			if(count($arrPath)){	
				//on commence a partir du home
				for($i=0; $i<count($arrPath); $i++){
					if(SIMPLIFIED_URL){
						$strSubPath = '/';
					}else{
						$strSubPath = '';
						}
					for($j=0; $j < ($i + 1); $j++){
						$strSubPath .= $arrPath[$j].'/';
						}
					$arrSubPath[$i] = $strSubPath;
					}
				}
			//check si vide
			if(count($arrSubPath)){
				//on invert les links
				$arrLinks = array_flip($oGlob->get('links'));
				//print_r($arrLinks);

				//on va chercher dans les links si ca existe
				//si le url simplifie alors on rajoute la langue prefix
				foreach($arrSubPath as $k=>$v){
					if(SIMPLIFIED_URL){
						//la langue est au debut
						//on cherche si existe
						$strSearchWithPrefix = PATH_HOME.$oGlob->get('lang_prefix').$v;
					}else{
						$strSearchWithPrefix = PATH_HOME.'?&lang='.$oGlob->get('lang').'&path='.$v;
						}
					//echo $strSearchWithPrefix.EOL;
					//check si existe
					if(isset($arrLinks[$strSearchWithPrefix])){
						$arrSubPath[$k] = array(
							'text' => $oGlob->getArray('links-name-by-id',$arrLinks[$strSearchWithPrefix]),
							'link' => $strSearchWithPrefix,
							);
					}else{
						$arrSubPath[$k] = array(
							'text' => $arrPath[$k],
							'link' => false,
							);
						}
					}
				//on push le home au debut
				//le link
				$strHomeLink = $oGlob->getArray('links-by-key', 'home-'.$oGlob->get('lang_prefix'));
				//le title via le arr qui a ete flip
				$strHomeTitle = $oGlob->getArray('links-name-by-id',$arrLinks[$strHomeLink]);
				//
				array_unshift($arrSubPath, array(
					'text' => $strHomeTitle,
					'link' => $strHomeLink,
					));
				//out
				return $arrSubPath;
				}
			//out	
			return false;
			}
		//out	
		return false;
		}
		
	
	}



	
	
//END	