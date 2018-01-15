<?php
class News {

	private $reg;
	private $arrFormErrors;
	private $strMsg;
	
	public function __construct($reg) {
		$this->reg = $reg;
		}
		
	//------------------------------------------------------------------------------------------------		
		
	public function disableNewsInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'news SET status = "0" WHERE '.DB_PREFIX.'news.id IN ('.$arr[0]['value'].');';
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
		
	public function enableNewsInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'news SET status = "1" WHERE '.DB_PREFIX.'news.id IN ('.$arr[0]['value'].');';
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
	
	public function deleteNewsInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				//on delete les cc_contents
				$query = 'DELETE FROM '.DB_PREFIX.'news WHERE '.DB_PREFIX.'news.id IN ('.$arr[0]['value'].');';
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
		
	public function disableNewsCategoryInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'news_category SET status = "0" WHERE '.DB_PREFIX.'news_category.id IN ('.$arr[0]['value'].') AND '.DB_PREFIX.'news_category.access = "1";';
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
		
	public function enableNewsCategoryInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'news_category SET status = "1" WHERE '.DB_PREFIX.'news_category.id IN ('.$arr[0]['value'].') AND '.DB_PREFIX.'news_category.access = "1";';
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
	
	public function deleteNewsCategoryInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				//on redirect les cc_links qui sontr relie au cc_news_category
				$query = 'SELECT '.DB_PREFIX.'news_category.link_id AS "link_id" FROM '.DB_PREFIX.'news_category WHERE '.DB_PREFIX.'news_category.id IN ('.$arr[0]['value'].');';
				$rs = $this->reg->get('db')->query($query);
				if($rs->num_rows){
					require_once(DIR_CLASS.'links.php');
					$oLinks = new Links($this->reg);
					foreach($rs->rows as $k=>$v){
						//on va chercher les anciennes valeurs
						$arrInfos = $oLinks->getLinksInfos($v['link_id']);
						$arrRedirectLink = array(
							'id' =>  $v['link_id'],
							'status' => '0',
							'name' => '',
							'keyindex' => '',
							'path' => $arrInfos['path'],
							'language_id' => $arrInfos['language_id'],
							);
						$oLinks->setDirectLinksInfos($arrRedirectLink);	
						}
					}
				$query = 'DELETE FROM '.DB_PREFIX.'news_category WHERE '.DB_PREFIX.'news_category.id IN ('.$arr[0]['value'].');';
				$this->reg->get('db')->query($query);
				$query = 'UPDATE '.DB_PREFIX.'news_category SET parent_id = "0", status = "0" WHERE '.DB_PREFIX.'news_category.parent_id IN ('.$arr[0]['value'].');';
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
	public function getNewsArrayForAdmin(){
		$query = 'SELECT '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'news_category.position AS "position", '.DB_PREFIX.'news_category.id AS "id", '.DB_PREFIX.'news_category.name AS "name", '.DB_PREFIX.'news_category.parent_id AS "parent_id", '.DB_PREFIX.'news_category.access AS "access", '.DB_PREFIX.'news_category.alias AS "alias", '.DB_PREFIX.'news_category.status AS "status", '.DB_PREFIX.'news_category.title AS "title", '.DB_PREFIX.'languages.prefix AS "language" FROM '.DB_PREFIX.'news_category LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'news_category.language_id LEFT JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'links.id = '.DB_PREFIX.'news_category.link_id  WHERE '.DB_PREFIX.'news_category.parent_id = "0" ORDER BY '.DB_PREFIX.'languages.prefix, '.DB_PREFIX.'news_category.position ASC;';
		$rs = $this->reg->get('db')->query($query);
		//
		$arrTmp = array();
		foreach($rs->rows as $k=>$v){
			array_push($arrTmp, 
				array(
					'id' => $v['id'], 
					'name' => $v['name'], 
					'parent_id' => $v['parent_id'], 
					'access' => $v['access'], 
					'alias' => $v['alias'], 
					'path' => $v['path'], 
					'status' => $v['status'], 
					'title' => $v['title'], 
					'language' => $v['language'], 
					'position' => $v['position'], 
					'level' => 0,
					'child' => false,
					)
				);
			}
		//
		$this->recursiveNewsChildFromParentId($arrTmp, 0);
		//
		return $arrTmp;
		}
		
		
			//------------------------------------------------------------------------------------------------		
		
	private function recursiveNewsChildFromParentId(&$arr, $level){ //arr by reference	
		//
		$level += 1;
		foreach($arr as $k=>$v){
			$query = 'SELECT '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'news_category.position AS "position", '.DB_PREFIX.'news_category.id AS "id", '.DB_PREFIX.'news_category.name AS "name", '.DB_PREFIX.'news_category.parent_id AS "parent_id", '.DB_PREFIX.'news_category.access AS "access", '.DB_PREFIX.'news_category.alias AS "alias", '.DB_PREFIX.'news_category.status AS "status", '.DB_PREFIX.'news_category.title AS "title", '.DB_PREFIX.'languages.prefix AS "language" FROM '.DB_PREFIX.'news_category LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'news_category.language_id LEFT JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'links.id = '.DB_PREFIX.'news_category.link_id  WHERE '.DB_PREFIX.'news_category.parent_id = "'.$v['id'].'" ORDER BY '.DB_PREFIX.'languages.prefix, '.DB_PREFIX.'news_category.position ASC;';
			$rs = $this->reg->get('db')->query($query);
			//
			$arrTmp = array();
			foreach($rs->rows as $k2=>$v2){
				array_push($arrTmp, 
					array(
						'id' => $v2['id'],
						'name' => $v2['name'], 
						'parent_id' => $v2['parent_id'], 
						'access' => $v2['access'], 
						'alias' => $v2['alias'], 
						'path' => $v2['path'],
						'status' => $v2['status'], 
						'title' => $v2['title'], 
						'language' => $v2['language'], 
						'position' => $v2['position'], 
						'level' => 0,
						'child' => false,
						)
					);
				}
			if(count($arrTmp)){
				$arr[$k]['child'] = $arrTmp;	
				//check les child pour descendre dans l'arbo
				$this->recursiveNewsChildFromParentId($arr[$k]['child'], $level);
				}
			}
		}	
		
	//------------------------------------------------------------------------------------------------			
	public function getNewsDropBox($arr, $bRecursive = true){
		//faire un dropbox avec le arrCat  
		$arrDropBox = array();
		//rajoute le default drop box level 0
		array_unshift($arr, array('id' => '0', 'name' => '--', 'child' => false));
		$this->recursiveNewsDropBox($str = '', $arrDropBox, $arr, $bRecursive);
		return $arrDropBox;
		}
		
	//------------------------------------------------------------------------------------------------		
	private function recursiveNewsDropBox($str, &$arrDropBox, &$arrTmp, $bRecursive = true){
		//
		foreach($arrTmp as $k=>$v){
			$strTmp = $str;
			if($strTmp != ''){
				$strTmp .= ' > ';
				}
			$strTmp .= $v['name'];
			array_push($arrDropBox, array('id'=>$v['id'], 'text'=>$strTmp));
			if($bRecursive && is_array($v['child'])){
				$this->recursiveNewsDropBox($strTmp, $arrDropBox, $v['child']);
				}
			}
		}	
		
	//------------------------------------------------------------------------------------------------		
	
	public function getNewsCategoryLanguageId($id){
		$query = 'SELECT '.DB_PREFIX.'news_category.language_id AS "id" FROM '.DB_PREFIX.'news_category WHERE '.DB_PREFIX.'news_category.id = "'.$id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0]['id'];
			}
		return 0;	
		}
		
	//------------------------------------------------------------------------------------------------		
	
	public function newNewsCategoryInfos($arr){
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
				}else if($v['name'] == 'position'){
					$arrValues['position'] = sqlSafe($v['value']);		
				}else if($v['name'] == 'parent_id'){
					if($v['value'] == '' || $v['value'] == '0'){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Parent Category is required.').'</li>';
						}
					$arrValues['parent_id'] = intVal($v['value']);		
					}
				}	
			if(count($this->arrFormErrors) != 0){
				return false;
			}else{
				if(!isset($arrValues['status'])){
					$arrValues['status'] = '0';
					}
				//on va chercher le language selon la categorie parente
				$arrValues['language_id'] = $this->getNewsCategoryLanguageId($arrValues['parent_id']);	
				//alias	
				$arrValues['alias'] = cleanAlias($arrValues['meta_title']);
				//build le link_id
				$arrValues['link_id'] = $this->buildLinkForNewsCategory(true, $arrValues['parent_id'], $arrValues['alias'], $arrValues['name'], $arrValues['language_id']);
				if(!$arrValues['link_id']){
					array_push($this->arrFormErrors, 'meta_title');
					$this->strMsg .= '<li>'._T('the path created with the Category News Title is already in use, please modify the Category News Title to create a new path.').'</li>';
					return false;
					}				
				//sql
				$query = 'INSERT INTO '.DB_PREFIX.'news_category (status, alias, name, title, meta_title, meta_description, meta_keywords, parent_id, content, language_id, link_id, position, date_modified) VALUES("'.$arrValues['status'].'", "'.$arrValues['alias'].'", "'.$arrValues['name'].'", "'.$arrValues['meta_title'].'", "'.$arrValues['meta_title'].'", "'.$arrValues['meta_description'].'", "'.$arrValues['meta_keywords'].'", "'.$arrValues['parent_id'].'", "'.$arrValues['content'].'", "'.$arrValues['language_id'].'", "'.$arrValues['link_id'].'", "'.$arrValues['position'].'", NOW());';
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
	
	public function setNewsCategoryInfos($arr){
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
				}else if($v['name'] == 'link_id'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('missing link id').'</li>';
						}
					$arrValues['link_id'] = intVal($v['value']);
				
				}else if($v['name'] == 'status'){
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
				}else if($v['name'] == 'position'){
					$arrValues['position'] = intVal($v['value']);		
				}else if($v['name'] == 'parent_id'){
					if($v['value'] == '' || $v['value'] == '0'){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Parent Category is required.').'</li>';
						}
					$arrValues['parent_id'] = intVal($v['value']);		
					}
				}
			if(!isset($arrValues['link_id'])){
				$this->strMsg .= '<li>'._T('the link id is missing.').'</li>';
				return false;	
				}
			if(count($this->arrFormErrors) != 0){
				return false;
			}else{
				if(!isset($arrValues['status'])){
					$arrValues['status'] = '0';
					}
				//on va chercher le language selon la categorie parente
				$arrValues['language_id'] = $this->getNewsCategoryLanguageId($arrValues['parent_id']);	
				//alias		
				$arrValues['alias'] = cleanAlias($arrValues['meta_title']);	
				//build le link_id
				//on  a besoin de l'ancien link id
				if(!$this->buildLinkForNewsCategory(false, $arrValues['parent_id'], $arrValues['alias'], $arrValues['name'], $arrValues['language_id'], $arrValues['link_id'])){
					array_push($this->arrFormErrors, 'meta_title');
					$this->strMsg .= '<li>'._T('the path created with the News Category Title is already in use, please modify the News Category Title to create a new path.').'</li>';
					return false;
					}
				
				//sql
				$query = 'UPDATE '.DB_PREFIX.'news_category SET position = "'.$arrValues['position'].'", status = "'.$arrValues['status'].'", alias = "'.$arrValues['alias'].'", name = "'.$arrValues['name'].'", title = "'.$arrValues['meta_title'].'", meta_title = "'.$arrValues['meta_title'].'", meta_description = "'.$arrValues['meta_description'].'", meta_keywords = "'.$arrValues['meta_keywords'].'", parent_id = "'.$arrValues['parent_id'].'", content = "'.$arrValues['content'].'", language_id = "'.$arrValues['language_id'].'", date_modified = NOW() WHERE '.DB_PREFIX.'news_category.id = "'.$arrValues['id'].'";';
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
		
	private function buildLinkForNewsCategory($bCreate, $catId, $strTitleAlias, $strName, $langId, $linkId = ''){
		$arrCatInfos = $this->getNewsCategoryInfos($catId);
		if(isset($arrCatInfos['alias'])){
			$strLinkName = $arrCatInfos['name'].' '.$strName;
			$strLinkPath = $arrCatInfos['alias'].'/'.$strTitleAlias;
			$strLinkKeyIndex = $arrCatInfos['alias'].'-'.$strTitleAlias;
			require_once(DIR_CLASS.'links.php');
			$oLinks = new Links($this->reg);
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
					return $oLinks->setDirectLinksInfos($arr, true);
					}
				return true;	
				}
			}
		return false;	
		}	

	//------------------------------------------------------------------------------------------------		
		
	public function getNewsCategoryInfos($id){
		$query = 'SELECT '.DB_PREFIX.'news_category.position AS "position", '.DB_PREFIX.'news_category.link_id AS "link_id", '.DB_PREFIX.'news_category.id AS "id", '.DB_PREFIX.'news_category.alias AS "alias", '.DB_PREFIX.'news_category.name AS "name", '.DB_PREFIX.'news_category.content AS "content", '.DB_PREFIX.'news_category.meta_title AS "meta_title", '.DB_PREFIX.'news_category.meta_keywords AS "meta_keywords",'.DB_PREFIX.'news_category.meta_description AS "meta_description", '.DB_PREFIX.'news_category.status AS "status", '.DB_PREFIX.'news_category.parent_id AS "parent_id", '.DB_PREFIX.'news_category.language_id AS "language_id" FROM '.DB_PREFIX.'news_category WHERE '.DB_PREFIX.'news_category.id = "'.$id.'" LIMIT 0,1;';
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
	
	public function setNewsInfos($arr){	
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
				}else if($v['name'] == 'name'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Name is required.').'</li>';
						}
					$arrValues['name'] = sqlSafe($v['value']);
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
				}else if($v['name'] == 'preview'){
					$arrValues['preview'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'category_id'){
					if($v['value'] == '' || $v['value'] == '0'){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field News Category is required.').'</li>';
						}
					$arrValues['category_id'] = intVal($v['value']);		
					}
				}	
			if(count($this->arrFormErrors) != 0){
				return false;
			}else{
				if(!isset($arrValues['status'])){
					$arrValues['status'] = '0';
					}
				//alias
				$arrValues['alias'] = cleanAlias($arrValues['meta_title']);
				//sql
				$query = 'UPDATE '.DB_PREFIX.'news SET alias = "'.$arrValues['alias'].'", name = "'.$arrValues['name'].'", title = "'.$arrValues['meta_title'].'", meta_title = "'.$arrValues['meta_title'].'", meta_description = "'.$arrValues['meta_description'].'", meta_keywords = "'.$arrValues['meta_keywords'].'", category_id = "'.$arrValues['category_id'].'", content = "'.$arrValues['content'].'", preview = "'.$arrValues['preview'].'", date_modified = NOW() WHERE '.DB_PREFIX.'news.id = "'.$arrValues['id'].'";';
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
		
	private function buildLinkForContent($bCreate, $catId, $strTitleAlias, $strName, $langId, $linkId = ''){
		if($catId != '1'){ //qui est le ID du generic (sans langue) dans la DB des cc_content_category
			$arrCatInfos = $this->getContentCategoryInfos($catId);
		}else{
			$arrCatInfos = array(
				'alias' => '',
				'name' => '',
				);
			}
		if(isset($arrCatInfos['alias'])){
			if($arrCatInfos['alias'] == ''){ //sans la categorie precedente
				$strLinkName = $strName;
				$strLinkPath = $strTitleAlias;
				$strLinkKeyIndex = $strTitleAlias;
			}else{
				$strLinkName = $arrCatInfos['name'].' '.$strName;
				$strLinkPath = $arrCatInfos['alias'].'/'.$strTitleAlias;
				$strLinkKeyIndex = $arrCatInfos['alias'].'-'.$strTitleAlias;
				}
			require_once(DIR_CLASS.'links.php');
			$oLinks = new Links($this->reg);
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
					return $oLinks->setDirectLinksInfos($arr);
					}
				return true;	
				}
			}
		return false;	
		}

	//------------------------------------------------------------------------------------------------		
		
	public function getNewsInfos($id){
		$query = 'SELECT '.DB_PREFIX.'languages.prefix AS "language", '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'news.date_added AS "date_added", '.DB_PREFIX.'news.date_modified AS "date_modified", '.DB_PREFIX.'news.preview AS "preview", '.DB_PREFIX.'news.content AS "content", '.DB_PREFIX.'news.id AS "id", '.DB_PREFIX.'news.name AS "name", '.DB_PREFIX.'news.category_id AS "category_id", '.DB_PREFIX.'news.meta_title AS "meta_title", '.DB_PREFIX.'news.meta_keywords AS "meta_keywords",'.DB_PREFIX.'news.meta_description AS "meta_description", '.DB_PREFIX.'news.status AS "status", '.DB_PREFIX.'news.hits AS "hits", '.DB_PREFIX.'news.alias AS "alias" FROM '.DB_PREFIX.'news LEFT JOIN '.DB_PREFIX.'news_category ON '.DB_PREFIX.'news.category_id = '.DB_PREFIX.'news_category.id LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'news_category.language_id LEFT JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'links.id = '.DB_PREFIX.'news_category.link_id WHERE '.DB_PREFIX.'news.id = "'.$id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0];	
			}
		return false;	
		}	
		
		
	//------------------------------------------------------------------------------------------------		
	public function getNewsFromCategorie($id, $iStart, $iLimit, $strSortBy = '', $strSearchItems = '', $strLanguage = '', $strDisplay = ''){
		$strWhere = '';
		if($id != 0 && $id != ''){
			$strWhere = ' WHERE '.DB_PREFIX.'news.category_id = "'.$id.'" '; 
			}
		if($strSortBy != ''){
			$strSortBy = ' ORDER BY '.DB_PREFIX.'news.'.$strSortBy.' ASC ';
		}else{
			$strSortBy = ' ORDER BY '.DB_PREFIX.'news.date_modified ASC ';
			}
		if($strSearchItems != ''){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= ' ('.DB_PREFIX.'news.name LIKE "%'.$strSearchItems.'%" || '.DB_PREFIX.'news.title LIKE "%'.$strSearchItems.'%"'.') ';
			}
		if($strLanguage != '' && $strLanguage != 0){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= DB_PREFIX.'news.language_id = "'.$strLanguage.'" ';
			}
		if($strDisplay != '' && $strDisplay != '2' || $strDisplay == '0'){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= DB_PREFIX.'news.status = "'.$strDisplay.'" ';
			}	
		$query = 'SELECT '.DB_PREFIX.'languages.prefix AS "language", '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'news.alias AS "alias", '.DB_PREFIX.'news.id AS "id", '.DB_PREFIX.'news.status AS "status", '.DB_PREFIX.'news.name AS "name", '.DB_PREFIX.'news.hits AS "hits", '.DB_PREFIX.'news.title AS "title", '.DB_PREFIX.'news.date_modified AS "date_modified" FROM '.DB_PREFIX.'news LEFT JOIN '.DB_PREFIX.'news_category ON '.DB_PREFIX.'news_category.id = '.DB_PREFIX.'news.category_id LEFT JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'links.id = '.DB_PREFIX.'news_category.link_id LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'news.language_id '.$strWhere.' '.$strSortBy.' LIMIT '.$iStart.','.$iLimit.';';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;
			}
		return false;	
		}	

	//------------------------------------------------------------------------------------------------			
	public function getNewsCountFromCategory($id, $strSearchItems = '', $strLanguage = '', $strDisplay = ''){
		$strWhere = '';
		if($id != 0 && $id != ''){
			$strWhere = ' WHERE '.DB_PREFIX.'news.category_id = "'.$id.'" '; 
			}
		if($strSearchItems != ''){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= ' ('.DB_PREFIX.'news.name LIKE "%'.$strSearchItems.'%" || '.DB_PREFIX.'news.title LIKE "%'.$strSearchItems.'%"'.') ';
			}
		if($strLanguage != '' && $strLanguage != 0){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= DB_PREFIX.'news.language_id = "'.$strLanguage.'" ';
			}
		if($strDisplay != '' && $strDisplay != '2' || $strDisplay == '0'){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= DB_PREFIX.'news.status = "'.$strDisplay.'" ';
			}	
		$query = 'SELECT COUNT('.DB_PREFIX.'news.id) AS "count" FROM '.DB_PREFIX.'news '.$strWhere.' ;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0]['count'];
			}
		return false;	
		}		
		
	//------------------------------------------------------------------------------------------------	
	public function newNewsInfos($arr){	
		/*
		
		NB: on va chercher le language_id selon le language_id de la categori parent
		
		- preview
		- language selon categorie parent
		- date added
		- title et meta_title sont la meme chose
		
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
				}else if($v['name'] == 'preview'){
					$arrValues['preview'] = sqlSafe($v['value']);		
				}else if($v['name'] == 'category_id'){
					if($v['value'] == '' || $v['value'] == '0'){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field News Category is required.').'</li>';
						}
					$arrValues['category_id'] = intVal($v['value']);		
					}
				}	
			if(count($this->arrFormErrors) != 0){
				return false;
			}else{
				if(!isset($arrValues['status'])){
					$arrValues['status'] = '0';
					}
				//alias
				$arrValues['alias'] = cleanAlias($arrValues['meta_title']);
				//on va chercher le language selon la categorie parente
				$arrValues['language_id'] = $this->getNewsCategoryLanguageId($arrValues['category_id']);	
				//insert
				$query = 'INSERT INTO '.DB_PREFIX.'news (status, alias, name, title, meta_title, meta_description, meta_keywords, category_id, content, preview, language_id, date_added, date_modified) VALUES ("'.$arrValues['status'].'", "'.$arrValues['alias'].'", "'.$arrValues['name'].'", "'.$arrValues['meta_title'].'", "'.$arrValues['meta_title'].'", "'.$arrValues['meta_description'].'", "'.$arrValues['meta_keywords'].'", "'.$arrValues['category_id'].'", "'.$arrValues['content'].'", "'.$arrValues['preview'].'", "'.$arrValues['language_id'].'", NOW(), NOW());';
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
	
	public function deleteCache(){
		require_once(DIR_CLASS.'cache.php');
		$oCache = new Cache($this->reg);
		$oCache->delete('news.*');
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