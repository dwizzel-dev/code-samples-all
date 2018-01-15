<?php
class Content {

	private $reg;
	private $arrFormErrors;
	private $strMsg;
	
	public function __construct($reg) {
		$this->reg = $reg;
		}
		
	//------------------------------------------------------------------------------------------------		
		
	public function disableContentInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'content SET status = "0" WHERE '.DB_PREFIX.'content.id IN ('.$arr[0]['value'].') AND '.DB_PREFIX.'content.access = "1";';
				$this->reg->get('db')->query($query);
				//clean la cache
				$this->deleteCache();
				//retour
				return true;
				}
			}
		return false;	
		}

	//------------------------------------------------------------------------------------------------		
		
	public function enableContentInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'content SET status = "1" WHERE '.DB_PREFIX.'content.id IN ('.$arr[0]['value'].') AND '.DB_PREFIX.'content.access = "1";';
				$this->reg->get('db')->query($query);
				//clean la cache
				$this->deleteCache();
				//retour
				return true;
				}
			}
		return false;	
		}
	//------------------------------------------------------------------------------------------------		
	
	public function deleteContentInfos($arr){	
		
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				//on delete les cc_links qui sontr relie au cc_content
				$query = 'SELECT '.DB_PREFIX.'content.link_id AS "id" FROM '.DB_PREFIX.'content WHERE '.DB_PREFIX.'content.id IN ('.$arr[0]['value'].');';
				$rs = $this->reg->get('db')->query($query);
				if($rs->num_rows){
					$strLinksDelete = '';
					foreach($rs->rows as $k=>$v){
						$strLinksDelete .= $v['id'].','; 
						}
					if($strLinksDelete != ''){
						$strLinksDelete = substr($strLinksDelete, 0, (strlen($strLinksDelete) - 1));
						//les principaux
						$query = 'DELETE FROM '.DB_PREFIX.'links WHERE '.DB_PREFIX.'links.id IN ('.$strLinksDelete.') AND '.DB_PREFIX.'links.access = "1";';
						$this->reg->get('db')->query($query);
						//les redirect
						$query = 'DELETE FROM '.DB_PREFIX.'links WHERE '.DB_PREFIX.'links.ref_id IN ('.$strLinksDelete.') AND '.DB_PREFIX.'links.has_moved = "1";';
						$this->reg->get('db')->query($query);
						}
					}
				
				//on delete les cc_contents
				$query = 'DELETE FROM '.DB_PREFIX.'content WHERE '.DB_PREFIX.'content.id IN ('.$arr[0]['value'].') AND '.DB_PREFIX.'content.access = "1";';
				$this->reg->get('db')->query($query);
				//clean la cache
				$this->deleteCache();
				//retour
				return true;
				}
			}
		return false;	
		}	
		
	//------------------------------------------------------------------------------------------------		
		
	public function disableContentCategoryInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'content_category SET status = "0" WHERE '.DB_PREFIX.'content_category.id IN ('.$arr[0]['value'].') AND '.DB_PREFIX.'content_category.access = "1";';
				$this->reg->get('db')->query($query);
				//clean la cache
				$this->deleteCache();
				//retour
				return true;
				}
			}
		return false;	
		}
		
	//------------------------------------------------------------------------------------------------		
		
	public function enableContentCategoryInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'content_category SET status = "1" WHERE '.DB_PREFIX.'content_category.id IN ('.$arr[0]['value'].') AND '.DB_PREFIX.'content_category.access = "1";';
				$this->reg->get('db')->query($query);
				//clean la cache
				$this->deleteCache();
				//retour
				return true;
				}
			}
		return false;	
		}
		
	//------------------------------------------------------------------------------------------------		
	
	public function deleteContentCategoryInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'DELETE FROM '.DB_PREFIX.'content_category WHERE '.DB_PREFIX.'content_category.id IN ('.$arr[0]['value'].');';
				$this->reg->get('db')->query($query);
				$query = 'UPDATE '.DB_PREFIX.'content_category SET parent_id = "0", status = "0" WHERE '.DB_PREFIX.'content_category.parent_id IN ('.$arr[0]['value'].');';
				$this->reg->get('db')->query($query);
				//clean la cache
				$this->deleteCache();
				//retour
				return true;
				}
			}
		return false;	
		}	

		
	//------------------------------------------------------------------------------------------------			
	public function getContentArrayForAdmin(){
		$query = 'SELECT '.DB_PREFIX.'content_category.id AS "id", '.DB_PREFIX.'content_category.name AS "name", '.DB_PREFIX.'content_category.parent_id AS "parent_id", '.DB_PREFIX.'content_category.position AS "position", '.DB_PREFIX.'content_category.access AS "access", '.DB_PREFIX.'content_category.alias AS "alias", '.DB_PREFIX.'content_category.status AS "status", '.DB_PREFIX.'content_category.title AS "title", '.DB_PREFIX.'languages.prefix AS "language" FROM '.DB_PREFIX.'content_category LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'content_category.language_id  WHERE '.DB_PREFIX.'content_category.parent_id = "0" ORDER BY '.DB_PREFIX.'languages.prefix, '.DB_PREFIX.'content_category.position ASC;';
		$rs = $this->reg->get('db')->query($query);
		//
		$arrTmp = array();
		foreach($rs->rows as $k=>$v){
			array_push($arrTmp, 
				array(
					'id' => $v['id'], 
					'name' => $v['name'], 
					'parent_id' => $v['parent_id'], 
					'position' => $v['position'], 
					'access' => $v['access'], 
					'alias' => $v['alias'], 
					'status' => $v['status'], 
					'title' => $v['title'], 
					'language' => $v['language'], 
					'level' => 0,
					'child' => false,
					)
				);
			}
		//
		$this->recursiveContentChildFromParentId($arrTmp, 0);
		//
		return $arrTmp;
		}
		
		
			//------------------------------------------------------------------------------------------------		
		
	private function recursiveContentChildFromParentId(&$arr, $level){ //arr by reference	
		//
		$level += 1;
		foreach($arr as $k=>$v){
			$query = 'SELECT '.DB_PREFIX.'content_category.id AS "id", '.DB_PREFIX.'content_category.name AS "name", '.DB_PREFIX.'content_category.parent_id AS "parent_id", '.DB_PREFIX.'content_category.position AS "position", '.DB_PREFIX.'content_category.access AS "access", '.DB_PREFIX.'content_category.alias AS "alias", '.DB_PREFIX.'content_category.status AS "status", '.DB_PREFIX.'content_category.title AS "title", '.DB_PREFIX.'languages.prefix AS "language" FROM '.DB_PREFIX.'content_category LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'content_category.language_id  WHERE '.DB_PREFIX.'content_category.parent_id = "'.$v['id'].'" ORDER BY '.DB_PREFIX.'languages.prefix, '.DB_PREFIX.'content_category.position ASC;';
			$rs = $this->reg->get('db')->query($query);
			//
			$arrTmp = array();
			foreach($rs->rows as $k2=>$v2){
				array_push($arrTmp, 
					array(
						'id' => $v2['id'],
						'name' => $v2['name'], 
						'parent_id' => $v2['parent_id'], 
						'position' => $v2['position'], 
						'access' => $v2['access'], 
						'alias' => $v2['alias'], 
						'status' => $v2['status'], 
						'title' => $v2['title'], 
						'language' => $v2['language'], 
						'level' => 0,
						'child' => false,
						)
					);
				}
			if(count($arrTmp)){
				$arr[$k]['child'] = $arrTmp;	
				//check les child pour descendre dans l'arbo
				$this->recursiveContentChildFromParentId($arr[$k]['child'], $level);
				}
			}
		}	
		
	//------------------------------------------------------------------------------------------------			
	public function getContentDropBox($arr){
		//faire un dropbox avec le arrCat  
		$arrDropBox = array();
		//rajoute le default drop box level 0
		array_unshift($arr, array('id' => '0', 'name' => '--', 'child' => false));
		$this->recursiveContentDropBox($str = '', $arrDropBox, $arr);
		return $arrDropBox;
		}
		
	//------------------------------------------------------------------------------------------------		
	private function recursiveContentDropBox($str, &$arrDropBox, &$arrTmp){
		//
		foreach($arrTmp as $k=>$v){
			$strTmp = $str;
			if($strTmp != ''){
				$strTmp .= ' > ';
				}
			$strTmp .= $v['name'];
			array_push($arrDropBox, array('id'=>$v['id'], 'text'=>$strTmp));
			if(is_array($v['child'])){
				$this->recursiveContentDropBox($strTmp, $arrDropBox, $v['child']);
				}
			}
		}	
		
	//------------------------------------------------------------------------------------------------		
	
	public function newContentCategoryInfos($arr){
		$this->strMsg = '';
		$arrValues = array();
		if(isset($arr) && is_array($arr) && count($arr)){
			$this->arrFormErrors = array();
			foreach($arr as $k=>$v){
				if($v['name'] == 'status'){
					if($v['value'] == 'on'){
						$arrValues['status'] = '1';
					}else{
						$arrValues['status'] = '0';
						}
				}else if($v['name'] == 'position'){
					$arrValues['position'] = intVal($v['value']);		
				}else if($v['name'] == 'name'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Name is required.').'</li>';
						}
					$arrValues['name'] = sqlSafe($v['value']);
				}else if($v['name'] == 'alias'){
					$arrValues['alias'] = sqlSafe($v['value']);		
				}else if($v['name'] == 'meta_title'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Meta Title is required.').'</li>';
						}
					$arrValues['meta_title'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'meta_description'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Meta Description is required.').'</li>';
						}
					$arrValues['meta_description'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'meta_keywords'){
					$arrValues['meta_keywords'] = sqlSafe($v['value']);		
				}else if($v['name'] == 'parent_id'){
					$arrValues['parent_id'] = intVal($v['value']);		
				}else if($v['name'] == 'language_id'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Language is required.').'</li>';
						}
					$arrValues['language_id'] = sqlSafe($v['value']);		
					}
				}	
			if(count($this->arrFormErrors) != 0){
				return false;
			}else{
				if(!isset($arrValues['status'])){
					$arrValues['status'] = '0';
					}
				if($arrValues['alias'] == ''){
					//si vide on le base sur le titre
					$arrValues['alias'] = cleanAlias($arrValues['meta_title']);	
				}else{
					$arrValues['alias'] = cleanAlias($arrValues['alias']);	
					}
				$query = 'INSERT INTO '.DB_PREFIX.'content_category (status, alias, name, title, meta_title, meta_description, meta_keywords, parent_id, position, language_id, date_modified) VALUES("'.$arrValues['status'].'", "'.$arrValues['alias'].'", "'.$arrValues['name'].'", "'.$arrValues['meta_title'].'", "'.$arrValues['meta_title'].'", "'.$arrValues['meta_description'].'", "'.$arrValues['meta_keywords'].'", "'.$arrValues['parent_id'].'", "'.$arrValues['position'].'", "'.$arrValues['language_id'].'", NOW());';
				$this->reg->get('db')->query($query);
				//clean la cache
				$this->deleteCache();
				//retour
				return true;	
				}
			}
		return false;	
		}	
		
	//------------------------------------------------------------------------------------------------		
	
	public function setContentCategoryInfos($arr){
		$this->strMsg = '';
		$arrValues = array();
		if(isset($arr) && is_array($arr) && count($arr)){
			$this->arrFormErrors = array();
			foreach($arr as $k=>$v){
				if($v['name'] == 'id'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('missing category id').'</li>';
						}
					$arrValues['id'] = intVal($v['value']);
				}else if($v['name'] == 'status'){
					if($v['value'] == 'on'){
						$arrValues['status'] = '1';
					}else{
						$arrValues['status'] = '0';
						}
				}else if($v['name'] == 'position'){
					$arrValues['position'] = intVal($v['value']);		
				}else if($v['name'] == 'name'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Name is required.').'</li>';
						}
					$arrValues['name'] = sqlSafe($v['value']);
				}else if($v['name'] == 'alias'){
					$arrValues['alias'] = sqlSafe($v['value']);		
				}else if($v['name'] == 'meta_title'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Meta Title is required.').'</li>';
						}
					$arrValues['meta_title'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'meta_description'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Meta Description is required.').'</li>';
						}
					$arrValues['meta_description'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'meta_keywords'){
					$arrValues['meta_keywords'] = sqlSafe($v['value']);		
				}else if($v['name'] == 'parent_id'){
					$arrValues['parent_id'] = intVal($v['value']);		
				}else if($v['name'] == 'language_id'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Language is required.').'</li>';
						}
					$arrValues['language_id'] = sqlSafe($v['value']);		
					}
				}	
			if(count($this->arrFormErrors) != 0){
				return false;
			}else{
				if(!isset($arrValues['status'])){
					$arrValues['status'] = '0';
					}
				if($arrValues['alias'] == ''){
					//pas d'alias on le contruit avec le titre
					$arrValues['alias'] = cleanAlias($arrValues['meta_title']);	
				}else{
					$arrValues['alias'] = cleanAlias($arrValues['alias']);	
					}
				$query = 'UPDATE '.DB_PREFIX.'content_category SET status = "'.$arrValues['status'].'", alias = "'.$arrValues['alias'].'", name = "'.$arrValues['name'].'", title = "'.$arrValues['meta_title'].'", meta_title = "'.$arrValues['meta_title'].'", meta_description = "'.$arrValues['meta_description'].'", meta_keywords = "'.$arrValues['meta_keywords'].'", parent_id = "'.$arrValues['parent_id'].'", position = "'.$arrValues['position'].'", language_id = "'.$arrValues['language_id'].'", date_modified = NOW() WHERE '.DB_PREFIX.'content_category.id = "'.$arrValues['id'].'";';
				$this->reg->get('db')->query($query);
				//clean la cache
				$this->deleteCache();
				//retour
				return true;	
				}
			}
		return false;	
		}		

	//------------------------------------------------------------------------------------------------		
		
	public function getContentCategoryInfos($id){
		$query = 'SELECT '.DB_PREFIX.'content_category.id AS "id", '.DB_PREFIX.'content_category.alias AS "alias", '.DB_PREFIX.'content_category.name AS "name", '.DB_PREFIX.'content_category.position AS "position", '.DB_PREFIX.'content_category.meta_title AS "meta_title", '.DB_PREFIX.'content_category.meta_keywords AS "meta_keywords",'.DB_PREFIX.'content_category.meta_description AS "meta_description", '.DB_PREFIX.'content_category.status AS "status", '.DB_PREFIX.'content_category.parent_id AS "parent_id", '.DB_PREFIX.'content_category.language_id AS "language_id" FROM '.DB_PREFIX.'content_category WHERE '.DB_PREFIX.'content_category.id = "'.$id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0];	
			}
		return false;	
		}	
		
	//------------------------------------------------------------------------------------------------		
	public function getContentFromCategorie($id, $iStart, $iLimit, $strSortBy = '', $strSearchItems = '', $strLanguage = '', $strDisplay = ''){
		$strWhere = '';
		if($id != 0 && $id != ''){
			$strWhere = ' WHERE '.DB_PREFIX.'content.category_id = "'.$id.'" '; 
			}
		if($strSortBy != ''){
			$strSortBy = ' ORDER BY '.DB_PREFIX.'content.'.$strSortBy.' ASC ';
		}else{
			$strSortBy = ' ORDER BY '.DB_PREFIX.'content.date_modified ASC ';
			}
		if($strSearchItems != ''){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= ' ('.DB_PREFIX.'content.name LIKE "%'.$strSearchItems.'%" || '.DB_PREFIX.'content.title LIKE "%'.$strSearchItems.'%"'.') ';
			}
		if($strLanguage != '' && $strLanguage != 0){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= DB_PREFIX.'content.language_id = "'.$strLanguage.'" ';
			}
		if($strDisplay != '' && $strDisplay != '2' || $strDisplay == '0'){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= DB_PREFIX.'content.status = "'.$strDisplay.'" ';
			}	
		$query = 'SELECT '.DB_PREFIX.'languages.prefix AS "language", '.DB_PREFIX.'content.access AS "access", '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'content.id AS "id", '.DB_PREFIX.'content.status AS "status", '.DB_PREFIX.'content.name AS "name", '.DB_PREFIX.'content.hits AS "hits", '.DB_PREFIX.'content.title AS "title", '.DB_PREFIX.'content.date_modified AS "date_modified" FROM '.DB_PREFIX.'content LEFT JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'links.id = '.DB_PREFIX.'content.link_id LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'content.language_id '.$strWhere.' '.$strSortBy.' LIMIT '.$iStart.','.$iLimit.';';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;
			}
		return false;	
		}	
		
		
	//------------------------------------------------------------------------------------------------			
	public function getContentCountFromCategory($id, $strSearchItems = '', $strLanguage = '', $strDisplay = ''){
		$strWhere = '';
		if($id != 0 && $id != ''){
			$strWhere = ' WHERE '.DB_PREFIX.'content.category_id = "'.$id.'" '; 
			}
		if($strSearchItems != ''){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= ' ('.DB_PREFIX.'content.name LIKE "%'.$strSearchItems.'%" || '.DB_PREFIX.'content.title LIKE "%'.$strSearchItems.'%"'.') ';
			}
		if($strLanguage != '' && $strLanguage != 0){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= DB_PREFIX.'content.language_id = "'.$strLanguage.'" ';
			}
		if($strDisplay != '' && $strDisplay != '2' || $strDisplay == '0'){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= DB_PREFIX.'content.status = "'.$strDisplay.'" ';
			}	
		$query = 'SELECT COUNT('.DB_PREFIX.'content.id) AS "count" FROM '.DB_PREFIX.'content '.$strWhere.' ;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0]['count'];
			}
		return false;	
		}	
		
	//------------------------------------------------------------------------------------------------	
	
	public function newContentInfos($arr){	
		/*
		
		IMPORTANT:
			Il faut construire le link_id et l'alias selon le content_category et le meta_title
			la view et le conrtoller a "page" automatique
		*/
		
		$this->strMsg = '';
		$arrValues = array();
		if(isset($arr) && is_array($arr) && count($arr)){
			$this->arrFormErrors = array();
			foreach($arr as $k=>$v){
				if($v['name'] == 'status'){
					if($v['value'] == 'on'){
						$arrValues['status'] = '1';
					}else{
						$arrValues['status'] = '0';
						}
				}else if($v['name'] == 'name'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Name is required.').'</li>';
						}
					$arrValues['name'] = sqlSafe($v['value']);
				}else if($v['name'] == 'alias'){
					$arrValues['alias'] = sqlSafe($v['value']);
				}else if($v['name'] == 'meta_title'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Meta Title is required.').'</li>';
						}
					$arrValues['meta_title'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'meta_description'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Meta Description is required.').'</li>';
						}
					$arrValues['meta_description'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'meta_keywords'){
					$arrValues['meta_keywords'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'content'){
					$arrValues['content'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'css_class'){
					$arrValues['css_class'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'category_id'){
					if($v['value'] == '' || $v['value'] == '0'){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Category Content is required.').'</li>';
						}
					$arrValues['category_id'] = intVal($v['value']);		
				}else if($v['name'] == 'language_id'){
					if($v['value'] == '' || $v['value'] == '0'){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Language is required.').'</li>';
						}
					$arrValues['language_id'] = intVal($v['value']);		
					}
				}	
			if(count($this->arrFormErrors) != 0){
				return false;
			}else{
				if(!isset($arrValues['status'])){
					$arrValues['status'] = '0';
					}
				//access
				$arrValues['access'] = '1';				
				//page view	
				$arrValues['view'] = 'page';
				//page controller
				$arrValues['controller'] = 'page';
				//alias
				//check si un alias ou pas
				if($arrValues['alias'] == ''){
					//on va en genere d'apres le titre de la page
					$arrValues['alias'] = cleanAlias($arrValues['meta_title']);
				}else{
					//on va le cleaner
					$arrValues['alias'] = cleanAlias($arrValues['alias']);
					}
				//build le link_id
				$arrLinkForContent = $this->buildLinkForContent(true, $arrValues['category_id'], $arrValues['alias'], $arrValues['name'], $arrValues['language_id'], $arrValues['meta_title']);
				//$arrValues['link_id'] = $this->buildLinkForContent(true, $arrValues['category_id'], $arrValues['alias'], $arrValues['name'], $arrValues['language_id'], $arrValues['meta_title']);
				if(!$arrLinkForContent || !isset($arrLinkForContent['link_id']) || $arrLinkForContent['link_id'] === false){
					array_push($this->arrFormErrors, 'meta_title');
					$this->strMsg .= '<li>'._T('the path created with the Page Title is already in use, please modify the Page Title to create a new path.').'</li>';
					return false;
					}
				//on continue
				$arrValues['link_id'] = $arrLinkForContent['link_id'];
				$arrValues['alias'] = $arrLinkForContent['alias'];
				//
				$query = 'INSERT INTO '.DB_PREFIX.'content (status, alias, name, title, meta_title, meta_description, meta_keywords, category_id, content, css_class, language_id, view, controller, access, link_id, date_modified) VALUES ("'.$arrValues['status'].'", "'.$arrValues['alias'].'", "'.$arrValues['name'].'", "'.$arrValues['meta_title'].'", "'.$arrValues['meta_title'].'", "'.$arrValues['meta_description'].'", "'.$arrValues['meta_keywords'].'", "'.$arrValues['category_id'].'", "'.$arrValues['content'].'", "'.$arrValues['css_class'].'", "'.$arrValues['language_id'].'", "'.$arrValues['view'].'", "'.$arrValues['controller'].'", "'.$arrValues['access'].'", "'.$arrValues['link_id'].'", NOW());';
				$this->reg->get('db')->query($query);
				//clean la cache
				$this->deleteCache();
				//retour
				return true;	
				}
			}
		return false;	
		}
		
	//------------------------------------------------------------------------------------------------	
	
	public function setContentInfos($arr){	
		/*
		
		IMPORTANT:
			***les pages "name": 
			"accueil", "home", "404" sont fixe et reseve dans la DB et les alias, name, categorie_id, language_id  ne doient pas etre change
		
		IMPORTANT:
			Il faut construire le link_id et l'alias selon le content_category et le meta_title
			la view et le conrtoller a "page" automatique
			
		*/
		
		//
		$this->strMsg = '';
		$arrValues = array();
		if(isset($arr) && is_array($arr) && count($arr)){
			$this->arrFormErrors = array();
			foreach($arr as $k=>$v){
				if($v['name'] == 'id'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('missing item id').'</li>';
						}
					$arrValues['id'] = intVal($v['value']);
				}else if($v['name'] == 'link_id'){
					if($v['value'] == '' || $v['value'] == '0'){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('missing link id.').'</li>';
						}
					$arrValues['link_id'] = intVal($v['value']);
				}else if($v['name'] == 'name'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Name is required.').'</li>';
						}
					$arrValues['name'] = sqlSafe($v['value']);
				}else if($v['name'] == 'alias'){
					$arrValues['alias'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'meta_title'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Meta Title is required.').'</li>';
						}
					$arrValues['meta_title'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'meta_description'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Meta Description is required.').'</li>';
						}
					$arrValues['meta_description'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'meta_keywords'){
					$arrValues['meta_keywords'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'content'){
					$arrValues['content'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'css_class'){
					$arrValues['css_class'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'view'){
					$arrValues['view'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'controller'){
					$arrValues['controller'] = sqlSafe($v['value']);		
				}else if($v['name'] == 'category_id'){
					if($v['value'] == '' || $v['value'] == '0'){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Category Content is required.').'</li>';
						}
					$arrValues['category_id'] = intVal($v['value']);		
				}else if($v['name'] == 'language_id'){
					if($v['value'] == '' || $v['value'] == '0'){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Language is required.').'</li>';
						}
					$arrValues['language_id'] = intVal($v['value']);		
					}
				}	
			if(count($this->arrFormErrors) != 0){
				return false;
			}else{
				if(!isset($arrValues['status'])){
					$arrValues['status'] = '0';
					}
				if(!isset($arrValues['view']) || $arrValues['view'] == '' ){
					$arrValues['view'] = VIEW_DEFAULT;
					}
				if(!isset($arrValues['controller']) || $arrValues['controller'] == '' ){
					$arrValues['controller'] = CONTROLLER_DEFAULT_PAGE;
					}
				if(!$this->isFixedContent($arrValues['id'])){
					//alias
					//check si un alias ou pas
					if($arrValues['alias'] == ''){
						//on va en genere d'apres le titre de la page
						$arrValues['alias'] = cleanAlias($arrValues['meta_title']);
					}else{
						//on va le cleaner
						$arrValues['alias'] = cleanAlias($arrValues['alias']);
						}
					//build le link_id
					$arrLinkForContent = $this->buildLinkForContent(false, $arrValues['category_id'], $arrValues['alias'], $arrValues['name'], $arrValues['language_id'], $arrValues['meta_title'], $arrValues['link_id']);
					if(!$arrLinkForContent || !isset($arrLinkForContent['updated']) || $arrLinkForContent['updated'] === false){
						array_push($this->arrFormErrors, 'meta_title');
						$this->strMsg .= '<li>'._T('the path created with the Page Title is already in use, please modify the Page Title to create a new path.').'</li>';
						return false;
						}
					//on continue
					$arrValues['alias'] = $arrLinkForContent['alias'];
					//update DB	
					$query = 'UPDATE '.DB_PREFIX.'content SET controller = "'.$arrValues['controller'].'", view = "'.$arrValues['view'].'", alias = "'.$arrValues['alias'].'", name = "'.$arrValues['name'].'", title = "'.$arrValues['meta_title'].'", meta_title = "'.$arrValues['meta_title'].'", meta_description = "'.$arrValues['meta_description'].'", meta_keywords = "'.$arrValues['meta_keywords'].'", category_id = "'.$arrValues['category_id'].'", content = "'.$arrValues['content'].'", css_class = "'.$arrValues['css_class'].'", language_id = "'.$arrValues['language_id'].'", date_modified = NOW() WHERE '.DB_PREFIX.'content.id = "'.$arrValues['id'].'";';
				
				}else{
					//pour les page fixe on ne modifie pas les alias, name, categorie_id, language_id 
					$query = 'UPDATE '.DB_PREFIX.'content SET controller = "'.$arrValues['controller'].'", view = "'.$arrValues['view'].'", title = "'.$arrValues['meta_title'].'", meta_title = "'.$arrValues['meta_title'].'", meta_description = "'.$arrValues['meta_description'].'", meta_keywords = "'.$arrValues['meta_keywords'].'", content = "'.$arrValues['content'].'", css_class = "'.$arrValues['css_class'].'", date_modified = NOW() WHERE '.DB_PREFIX.'content.id = "'.$arrValues['id'].'";';
					}
				//commit to DB	
				$this->reg->get('db')->query($query);
				//clean la cache
				$this->deleteCache();
				//retour
				return true;	
				}
			}
		return false;	
		}	
		
	//------------------------------------------------------------------------------------------------		
	
	private function buildLinkForContent($bCreate, $catId, $strTitleAlias, $strName, $langId, $strTitle, $linkId = ''){
		//la base
		$strLinkName = $strTitle;
		$strLinkPath = $strTitleAlias;
		$strLinkKeyIndex = $strTitleAlias;
		//qui est le ID = 1 du generic (sans langue) dans la DB des cc_content_category
		if(intVal($catId) != 1){ 
			$bEndPath = false;
			while(!$bEndPath){	
				//sinon on va chercher le path tree
				$arrCatInfos = $this->getContentCategoryInfos($catId);	
				//si un alias est setter
				if(isset($arrCatInfos['alias'])){
					$strLinkPath = $arrCatInfos['alias'].'/'.$strLinkPath;
					$strLinkKeyIndex = $arrCatInfos['alias'].'-'.$strLinkKeyIndex;
					}
				//si a une categorie parent
				if(intVal($arrCatInfos['parent_id'])){
					$catId = $arrCatInfos['parent_id'];
				}else{
					$bEndPath = true;
					}
				}
			}
		/*
		echo $strLinkName.EOL;
		echo $strLinkPath.EOL;	
		echo $strLinkKeyIndex.EOL;	
		exit();	
		*/
		require_once(DIR_CLASS.'links.php');
		$oLinks = new Links($this->reg);
		//on va creer le links du kwyindex et du path qui doivent etre unique
		//on va essayer tant que l'on n'en trouve pas un
		$iIncrementLinkSuffixe = 0;
		$strTmpLinkKeyIndex = $strLinkKeyIndex;	
		while($oLinks->isDuplicateLinksKeyIndex($strTmpLinkKeyIndex, $linkId)){
			$iIncrementLinkSuffixe++;	
			$strTmpLinkKeyIndex = $strLinkKeyIndex.'-'.$iIncrementLinkSuffixe;
			}
		//on le garde
		$strLinkKeyIndex = $strTmpLinkKeyIndex;
		//meme chose avec les path
		$iIncrementLinkSuffixe = 0;
		$strTmpLinkPath = $strLinkPath;	
		while($oLinks->isDuplicateLinksPath($strTmpLinkPath, $linkId)){
			$iIncrementLinkSuffixe++;	
			$strTmpLinkPath = $strLinkPath.'-'.$iIncrementLinkSuffixe;
			}
		//on le garde
		$strLinkPath = $strTmpLinkPath;
		//on creer le lien 
		$arr = array(
			'id' =>  $linkId,
			'status' => '1',
			'name' => $strLinkName,
			'keyindex' => $strLinkKeyIndex,
			'path' => $strLinkPath,
			'language_id' => $langId,
			);
		//the alias
		if($iIncrementLinkSuffixe !== 0){
			$strTitleAlias .= '-'.$iIncrementLinkSuffixe;
			}
		if($bCreate){	
			//create new link avec le alias retourne
			return array(
				'link_id' => $oLinks->newDirectLinksInfos($arr),
				'alias' => $strTitleAlias
				);
		}else{
			return array(
				'updated' => $oLinks->setDirectLinksInfos($arr, true),
				'alias' => $strTitleAlias
				);
			}
		//	
		return false;
		}

	/*	
	private function buildLinkForContent($bCreate, $catId, $strTitleAlias, $strName, $langId, $strTitle, $linkId = ''){
		//qui est le ID du generic (sans langue) dans la DB des cc_content_category
		if($catId != '1'){ 
			$arrCatInfos = $this->getContentCategoryInfos($catId);
		}else{
			$arrCatInfos = array(
				'alias' => '',
				'name' => '',
				);
			}
		print_r($arrCatInfos);
		exit();
		if(isset($arrCatInfos['alias'])){
			if($arrCatInfos['alias'] == ''){ //sans la categorie precedente
				//$strLinkName = $strName;
				$strLinkName = $strTitle;
				$strLinkPath = $strTitleAlias;
				$strLinkKeyIndex = $strTitleAlias;
			}else{
				//$strLinkName = $arrCatInfos['name'].' '.$strName;
				$strLinkName = $strTitle;
				$strLinkPath = $arrCatInfos['alias'].'/'.$strTitleAlias;
				$strLinkKeyIndex = $arrCatInfos['alias'].'-'.$strTitleAlias;
				}
			require_once(DIR_CLASS.'links.php');
			$oLinks = new Links($this->reg);
			//on va creer le links du kwyindex et du path qui doivent etre unique
			//check for duplicate keyindex and path not allowed
			if(!$oLinks->isDuplicateLinksKeyIndex($strLinkKeyIndex, $linkId) && !$oLinks->isDuplicateLinksPath($strLinkPath, $linkId)){
				//on creer le lien 
				$arr = array(
					'id' =>  $linkId,
					'status' => '1',
					'name' => $strLinkName,
					'keyindex' => $strLinkKeyIndex,
					'path' => $strLinkPath,
					'language_id' => $langId,
					);
				if($bCreate){	
					//create new link	
					return $oLinks->newDirectLinksInfos($arr);
				}else{
					//modify
					//return $oLinks->setDirectLinksInfos($arr, true);
					return $oLinks->setDirectLinksInfos($arr, false); //false pour ne pas faire une copie de has_moved = 1
					}
				return true;	
				}
			}
		return false;	
		}
	*/
		
	//------------------------------------------------------------------------------------------------		
		
	private function isFixedContent($id){
		$query = 'SELECT 1 FROM '.DB_PREFIX.'content WHERE '.DB_PREFIX.'content.id = "'.$id.'" AND '.DB_PREFIX.'content.fixed = "1" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return true;	
			}
		return false;	
		}	
		
	//------------------------------------------------------------------------------------------------		
		
	public function getContentInfos($id){
		$query = 'SELECT '.DB_PREFIX.'content.alias AS "alias", '.DB_PREFIX.'content.access AS "access", '.DB_PREFIX.'content.fixed AS "fixed", '.DB_PREFIX.'languages.prefix AS "language", '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'content.date_modified AS "date_modified", '.DB_PREFIX.'content.css_class AS "css_class", '.DB_PREFIX.'content.content AS "content", '.DB_PREFIX.'content.link_id AS "link_id", '.DB_PREFIX.'content.id AS "id", '.DB_PREFIX.'content.name AS "name", '.DB_PREFIX.'content.category_id AS "category_id", '.DB_PREFIX.'content.meta_title AS "meta_title", '.DB_PREFIX.'content.meta_keywords AS "meta_keywords",'.DB_PREFIX.'content.meta_description AS "meta_description", '.DB_PREFIX.'content.status AS "status", '.DB_PREFIX.'content.hits AS "hits", '.DB_PREFIX.'content.view AS "view", '.DB_PREFIX.'content.controller AS "controller", '.DB_PREFIX.'content.language_id AS "language_id" FROM '.DB_PREFIX.'content LEFT JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'links.id = '.DB_PREFIX.'content.link_id LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'content.language_id  WHERE '.DB_PREFIX.'content.id = "'.$id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0];	
			}
		return false;	
		}	

	//------------------------------------------------------------------------------------------------		
	
	public function deleteCache(){
		require_once(DIR_CLASS.'cache.php');
		$oCache = new Cache($this->reg);
		$oCache->delete('home.*');		
		$oCache->delete('page.*');		
		$oCache->delete('router');
		}
		
	//------------------------------------------------------------------------------------------------		
		
	public function getFormErrors(){
		return $this->arrFormErrors;
		}
		
	//------------------------------------------------------------------------------------------------		
		
	public function getMsgErrors(){
		return $this->strMsg;
		}	
	
	
	}
?>