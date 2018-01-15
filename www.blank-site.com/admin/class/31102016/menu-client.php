<?php
class MenuClient {

	private $reg;
	private $arrFormErrors;
	private $strMsg;
	
	public function __construct($reg) {
		$this->reg = $reg;
		}
		
	//------------------------------------------------------------------------------------------------		
	public function getMenuClientArrayForAdmin($strLanguage = '', $strDisplay = '', $strMenuGroup = ''){
	
		
		$strWhere = '';
		if($strLanguage != '' && $strLanguage != 0){
			$strWhere .= ' AND '.DB_PREFIX.'menu_group.language_id = "'.$strLanguage.'" ';
			}
		if($strDisplay != '' && $strDisplay != '2' || $strDisplay == '0'){
			$strWhere .= ' AND '.DB_PREFIX.'menu.status = "'.$strDisplay.'" ';
			}
		if($strMenuGroup != '' && $strMenuGroup != 0){
			$strWhere .= ' AND '.DB_PREFIX.'menu.menu_group_id = "'.$strMenuGroup.'" ';
			}
			
	
		$query = 'SELECT '.DB_PREFIX.'menu.product_type_id AS "product_type_id", '.DB_PREFIX.'menu.catalogue_cat_id AS "catalogue_cat_id", '.DB_PREFIX.'links.extern AS "extern", '.DB_PREFIX.'links.language_id AS "links_language_id", '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'menu_group.name AS "menu_group", '.DB_PREFIX.'menu.parent_id AS "parent_id", '.DB_PREFIX.'menu.position AS "position", '.DB_PREFIX.'menu.access AS "access", '.DB_PREFIX.'menu.id AS "id", '.DB_PREFIX.'menu.name AS "name", '.DB_PREFIX.'menu.status AS "status", '.DB_PREFIX.'languages.prefix AS "language" FROM '.DB_PREFIX.'menu LEFT JOIN '.DB_PREFIX.'menu_group ON '.DB_PREFIX.'menu_group.id = '.DB_PREFIX.'menu.menu_group_id LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'menu_group.language_id LEFT JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'links.id = '.DB_PREFIX.'menu.link_id WHERE '.DB_PREFIX.'menu.parent_id = "0" '.$strWhere.' ORDER BY '.DB_PREFIX.'menu_group.id, '.DB_PREFIX.'languages.prefix, '.DB_PREFIX.'menu.position ASC;';
		//$query = 'SELECT '.DB_PREFIX.'menu.product_type_id AS "product_type_id", '.DB_PREFIX.'menu.catalogue_cat_id AS "catalogue_cat_id", '.DB_PREFIX.'links.extern AS "extern", '.DB_PREFIX.'links.language_id AS "links_language_id", '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'menu_group.name AS "menu_group", '.DB_PREFIX.'menu.parent_id AS "parent_id", '.DB_PREFIX.'menu.position AS "position", '.DB_PREFIX.'menu.access AS "access", '.DB_PREFIX.'menu.id AS "id", '.DB_PREFIX.'menu.name AS "name", '.DB_PREFIX.'menu.status AS "status", '.DB_PREFIX.'languages.prefix AS "language" FROM '.DB_PREFIX.'menu LEFT JOIN '.DB_PREFIX.'menu_group ON '.DB_PREFIX.'menu_group.id = '.DB_PREFIX.'menu.menu_group_id LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'menu_group.language_id LEFT JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'links.id = '.DB_PREFIX.'menu.link_id WHERE '.DB_PREFIX.'menu.parent_id = "0" '.$strWhere.' ORDER BY '.DB_PREFIX.'languages.prefix, '.DB_PREFIX.'menu.position ASC;';
		$rs = $this->reg->get('db')->query($query);
		//
		$arrTmp = array();
		foreach($rs->rows as $k=>$v){
			array_push($arrTmp, 
				array(
					'id' => $v['id'], 
					'name' => $v['name'], 
					'path' => $v['path'], 
					'extern' => $v['extern'], 
					'menu_group' => $v['menu_group'], 
					'language' => $v['language'], 
					'access' => $v['access'], 
					'position' => $v['position'], 
					'status' => $v['status'], 
					'parent_id' => $v['parent_id'], 
					'links_language_id' => $v['links_language_id'], 
					'catalogue_cat_id' => $v['catalogue_cat_id'], 
					'product_type_id' => $v['product_type_id'], 
					'level' => 0,
					'child' => false,
					)
				);
			}
		//
		$this->recursiveMenuClientChildFromParentId($arrTmp, 0, $strLanguage, $strDisplay, $strMenuGroup);
		//
		return $arrTmp;
		}	
		
	//------------------------------------------------------------------------------------------------		
		
	private function recursiveMenuClientChildFromParentId(&$arr, $level, $strLanguage = '', $strDisplay = '', $strMenuGroup = ''){ //arr by reference	
		
		$strWhere = '';
		if($strLanguage != '' && $strLanguage != 0){
			$strWhere .= ' AND '.DB_PREFIX.'menu_group.language_id = "'.$strLanguage.'" ';
			}
		if($strDisplay != '' && $strDisplay != '2'){
			$strWhere .= ' AND '.DB_PREFIX.'menu.status = "'.$strDisplay.'" ';
			}
		if($strMenuGroup != '' && $strMenuGroup != 0){
			$strWhere .= ' AND '.DB_PREFIX.'menu.menu_group_id = "'.$strMenuGroup.'" ';
			}	
		
		//
		$level += 1;
		foreach($arr as $k=>$v){
			$query = 'SELECT '.DB_PREFIX.'menu.product_type_id AS "product_type_id", '.DB_PREFIX.'menu.catalogue_cat_id AS "catalogue_cat_id", '.DB_PREFIX.'links.extern AS "extern", '.DB_PREFIX.'links.language_id AS "links_language_id", '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'menu_group.name AS "menu_group", '.DB_PREFIX.'menu.parent_id AS "parent_id", '.DB_PREFIX.'menu.position AS "position", '.DB_PREFIX.'menu.access AS "access", '.DB_PREFIX.'menu.id AS "id", '.DB_PREFIX.'menu.name AS "name", '.DB_PREFIX.'menu.status AS "status", '.DB_PREFIX.'languages.prefix AS "language" FROM '.DB_PREFIX.'menu LEFT JOIN '.DB_PREFIX.'menu_group ON '.DB_PREFIX.'menu_group.id = '.DB_PREFIX.'menu.menu_group_id LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'menu_group.language_id LEFT JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'links.id = '.DB_PREFIX.'menu.link_id WHERE '.DB_PREFIX.'menu.parent_id = "'.$v['id'].'" '.$strWhere.' ORDER BY '.DB_PREFIX.'menu.position ASC;';
			$rs = $this->reg->get('db')->query($query);
			//
			$arrTmp = array();
			foreach($rs->rows as $k2=>$v2){
				array_push($arrTmp, 
					array(
						'id' => $v2['id'], 
						'name' => $v2['name'], 
						'path' => $v2['path'], 
						'extern' => $v2['extern'], 
						'menu_group' => $v2['menu_group'], 
						'language' => $v2['language'], 
						'access' => $v2['access'], 
						'position' => $v2['position'], 
						'status' => $v2['status'], 
						'parent_id' => $v2['parent_id'], 
						'links_language_id' => $v2['links_language_id'], 
						'catalogue_cat_id' => $v2['catalogue_cat_id'], 
						'product_type_id' => $v2['product_type_id'], 
						'level' => $level,
						'child' => false,
						)
					);
				}
			if(count($arrTmp)){
				$arr[$k]['child'] = $arrTmp;	
				//check les child pour descendre dans l'arbo
				$this->recursiveMenuClientChildFromParentId($arr[$k]['child'], $level, $strLanguage, $strDisplay, $strMenuGroup);
				}
			}
		}	
		
	
	//------------------------------------------------------------------------------------------------	
	
	public function getMenuGroupArray(){
		$query = 'SELECT '.DB_PREFIX.'menu_group.id AS "id", '.DB_PREFIX.'menu_group.name AS "name", '.DB_PREFIX.'languages.prefix AS "language" FROM '.DB_PREFIX.'menu_group LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'menu_group.language_id ORDER BY '.DB_PREFIX.'languages.code, '.DB_PREFIX.'menu_group.name;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;	
			}
		return false;
		}	
	
	//------------------------------------------------------------------------------------------------	
	
	public function getMenuClientTreeListing($arr){
		//faire un dropbox avec le arrCat  
		$arrDropBox = array();
		//rajoute le default drop box level 0
		array_unshift($arr, array('id' => '0', 'name' => '--', 'child' => false));
		$this->recursiveMenuClientTreeDropBox($str = '', $arrDropBox, $arr);
		return $arrDropBox;
		}
		
	//------------------------------------------------------------------------------------------------		
	private function recursiveMenuClientTreeDropBox($str, &$arrDropBox, &$arrTmp){
		//
		foreach($arrTmp as $k=>$v){
			$strTmp = $str;
			if($strTmp != ''){
				$strTmp .= ' > ';
				}
			$strTmp .= $v['name'];
			array_push($arrDropBox, array('id'=>$v['id'], 'text'=>$strTmp));
			if(is_array($v['child'])){
				$this->recursiveMenuClientTreeDropBox($strTmp, $arrDropBox, $v['child']);
				}
			}
		}	
			
		
	//------------------------------------------------------------------------------------------------	
	
	public function getMenuClientListing(){
		$query = 'SELECT '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'menu_group.name AS "menu_group", '.DB_PREFIX.'menu.parent_id AS "parent_id", '.DB_PREFIX.'menu.position AS "position", '.DB_PREFIX.'menu.access AS "access", '.DB_PREFIX.'menu.id AS "id", '.DB_PREFIX.'menu.name AS "name", '.DB_PREFIX.'menu.status AS "status", '.DB_PREFIX.'languages.prefix AS "language" FROM '.DB_PREFIX.'menu LEFT JOIN '.DB_PREFIX.'menu_group ON '.DB_PREFIX.'menu_group.id = '.DB_PREFIX.'menu.menu_group_id LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'menu_group.language_id LEFT JOIN '.DB_PREFIX.'links ON '.DB_PREFIX.'links.id = '.DB_PREFIX.'menu.link_id WHERE '.DB_PREFIX.'menu.link_id = 0 AND '.DB_PREFIX.'menu.menu_type = "0" ORDER BY '.DB_PREFIX.'languages.prefix, '.DB_PREFIX.'menu.name;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;	
			}
		return false;
		}	
		
	//------------------------------------------------------------------------------------------------		
	
	public function setMenuClientInfos($arr){
		$this->strMsg = '';
		$arrValues = array();
		$this->arrFormErrors = array();
		if(isset($arr) && is_array($arr) && count($arr)){
			foreach($arr as $k=>$v){
				if($v['name'] == 'id'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('missing links id').'</li>';
						}
					$arrValues['id'] = intVal($v['value']);
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
				}else if($v['name'] == 'description'){
					$arrValues['description'] = sqlSafe($v['value']);
				}else if($v['name'] == 'link_id'){
					if($v['value'] == ''){
						$arrValues['link_id'] = '0';		
					}else{
						$arrValues['link_id'] = intVal($v['value']);	
						}
				}else if($v['name'] == 'parent_id'){
					if($v['value'] == ''){
						$arrValues['parent_id'] = '0';		
					}else{
						$arrValues['parent_id'] = intVal($v['value']);		
						}		
				}else if($v['name'] == 'position'){
					if($v['value'] == ''){
						$arrValues['position'] = '0';		
					}else{
						$arrValues['position'] = intVal($v['value']);		
						}
				}else if($v['name'] == 'menu_group_id'){
					if($v['value'] == '' || $v['value'] == '0'){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Menu Group is required.').'</li>';
						}
					$arrValues['menu_group_id'] = intVal($v['value']);		
				}else if($v['name'] == 'menu_type'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Menu Type is required.').'</li>';
						}
					$arrValues['menu_type'] = intVal($v['value']);		
					}
				}	
			//
			if(count($this->arrFormErrors) != 0){
				return false;
			}else{
				if(!isset($arrValues['status'])){
					$arrValues['status'] = '0';
					}	
				//si on est la c'est que l'on peut insert
				$query = 'UPDATE '.DB_PREFIX.'menu SET status = "'.$arrValues['status'].'", name = "'.$arrValues['name'].'", description = "'.$arrValues['description'].'", link_id = "'.$arrValues['link_id'].'", parent_id = "'.$arrValues['parent_id'].'", position = "'.$arrValues['position'].'", menu_group_id = "'.$arrValues['menu_group_id'].'", menu_type = "'.$arrValues['menu_type'].'", date_modified =  NOW() WHERE '.DB_PREFIX.'menu.id = "'.$arrValues['id'].'";';
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
	
	public function newMenuClientInfos($arr){
		$this->strMsg = '';
		$arrValues = array();
		$this->arrFormErrors = array();
		if(isset($arr) && is_array($arr) && count($arr)){
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
				}else if($v['name'] == 'description'){
					$arrValues['description'] = sqlSafe($v['value']);
				}else if($v['name'] == 'link_id'){
					if($v['value'] == ''){
						$arrValues['link_id'] = '0';		
					}else{
						$arrValues['link_id'] = intVal($v['value']);	
						}
				}else if($v['name'] == 'parent_id'){
					if($v['value'] == ''){
						$arrValues['parent_id'] = '0';		
					}else{
						$arrValues['parent_id'] = intVal($v['value']);		
						}		
				}else if($v['name'] == 'position'){
					if($v['value'] == ''){
						$arrValues['position'] = '0';		
					}else{
						$arrValues['position'] = intVal($v['value']);		
						}
				}else if($v['name'] == 'menu_group_id'){
					if($v['value'] == '' || $v['value'] == '0'){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Menu Group is required.').'</li>';
						}
					$arrValues['menu_group_id'] = intVal($v['value']);		
				}else if($v['name'] == 'menu_type'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Menu Type is required.').'</li>';
						}
					$arrValues['menu_type'] = intVal($v['value']);		
					}
				}	
			//
			if(count($this->arrFormErrors) != 0){
				return false;
			}else{
				if(!isset($arrValues['status'])){
					$arrValues['status'] = '0';
					}	
				//si on est la c'est que l'on peut insert
				$query = 'INSERT INTO '.DB_PREFIX.'menu (status, name, description, link_id, parent_id, position, menu_group_id, menu_type, date_modified) VALUES("'.$arrValues['status'].'", "'.$arrValues['name'].'", "'.$arrValues['description'].'", "'.$arrValues['link_id'].'", "'.$arrValues['parent_id'].'", "'.$arrValues['position'].'", "'.$arrValues['menu_group_id'].'", "'.$arrValues['menu_type'].'", NOW());';
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
		
	public function disableMenuClientInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'menu SET status = "0" WHERE '.DB_PREFIX.'menu.id IN ('.$arr[0]['value'].') AND '.DB_PREFIX.'menu.access = "1";';
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
		
	public function enableMenuClientInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'menu SET status = "1" WHERE '.DB_PREFIX.'menu.id IN ('.$arr[0]['value'].') AND '.DB_PREFIX.'menu.access = "1";';
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
	
	public function deleteMenuClientInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'DELETE FROM '.DB_PREFIX.'menu WHERE '.DB_PREFIX.'menu.id IN ('.$arr[0]['value'].') AND '.DB_PREFIX.'menu.access = "1";';
				$this->reg->get('db')->query($query);
				$query = 'UPDATE '.DB_PREFIX.'menu SET parent_id = "0", status = "0" WHERE '.DB_PREFIX.'menu.parent_id IN ('.$arr[0]['value'].');';
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
	
	public function getMenuClientInfos($id){	
		$query = 'SELECT '.DB_PREFIX.'menu.status AS "status", '.DB_PREFIX.'menu.id AS "id", '.DB_PREFIX.'menu.name AS "name", '.DB_PREFIX.'menu.position AS "position", '.DB_PREFIX.'menu.description AS "description", '.DB_PREFIX.'menu.menu_group_id AS "menu_group_id", '.DB_PREFIX.'menu.link_id AS "link_id", '.DB_PREFIX.'menu.parent_id AS "parent_id", '.DB_PREFIX.'menu.menu_type AS "menu_type" FROM '.DB_PREFIX.'menu WHERE '.DB_PREFIX.'menu.id = "'.$id.'" LIMIT 0,1;';
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
		$oCache->delete('menu.*');		
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