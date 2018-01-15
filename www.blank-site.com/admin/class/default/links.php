<?php
class Links {

	private $reg;
	private $arrFormErrors;
	private $strMsg;
	
	public function __construct($reg) {
		$this->reg = $reg;
		}
		
	//------------------------------------------------------------------------------------------------	
	
	public function getLinksListing(){
		$query = 'SELECT '.DB_PREFIX.'links.has_moved AS "has_moved", '.DB_PREFIX.'links.access AS "access", '.DB_PREFIX.'links.hits AS "hits", '.DB_PREFIX.'links.id AS "id", '.DB_PREFIX.'links.keyindex AS "keyindex", '.DB_PREFIX.'links.name AS "name", '.DB_PREFIX.'links.language_id AS "language_id", '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'links.status AS "status", '.DB_PREFIX.'languages.prefix AS "language", '.DB_PREFIX.'links.extern AS "extern" FROM '.DB_PREFIX.'links LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'links.language_id ORDER BY '.DB_PREFIX.'links.language_id, '.DB_PREFIX.'links.name;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;	
			}
		return false;
		}	
		
	//------------------------------------------------------------------------------------------------	
	
	public function getLinksForDropDown(){
		$query = 'SELECT '.DB_PREFIX.'links.access AS "access", '.DB_PREFIX.'links.hits AS "hits", '.DB_PREFIX.'links.id AS "id", '.DB_PREFIX.'links.keyindex AS "keyindex", '.DB_PREFIX.'links.name AS "name", '.DB_PREFIX.'links.language_id AS "language_id", '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'links.extern AS "extern", '.DB_PREFIX.'links.status AS "status", '.DB_PREFIX.'languages.prefix AS "language" FROM '.DB_PREFIX.'links LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'links.language_id WHERE '.DB_PREFIX.'links.has_moved = "0" ORDER BY '.DB_PREFIX.'links.language_id, '.DB_PREFIX.'links.name;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;	
			}
		return false;
		}	
		
	//------------------------------------------------------------------------------------------------		
	
	public function setLinksInfos($arr){
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
				}else if($v['name'] == 'extern'){
					if($v['value'] == 'on'){
						$arrValues['extern'] = '1';
					}else{
						$arrValues['extern'] = '0';
						}
				}else if($v['name'] == 'name'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Name is required.').'</li>';
						}
					$arrValues['name'] = sqlSafe($v['value']);
				}else if($v['name'] == 'keyindex'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Key is required.').'</li>';
						}
					$arrValues['keyindex'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'path'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Path is required.').'</li>';
						}
					$arrValues['path'] = sqlSafe($v['value']);		
				}else if($v['name'] == 'language_id'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Language is required.').'</li>';
						}
					$arrValues['language_id'] = intVal($v['value']);		
					}
				}	
			//
			if(count($this->arrFormErrors) != 0){
				return false;
			}else{
				if(!isset($arrValues['status'])){
					$arrValues['status'] = '0';
					}	
				if(!isset($arrValues['extern'])){
					$arrValues['extern'] = '0';
					}	
				//on strip le path et la key pour enlever les blank space et charactere speciaux
				//on strip le path et la key pour enlever les blank space et charactere speciaux
				if($arrValues['extern'] != '1'){
					$arrValues['keyindex'] = cleanKeyIndex($arrValues['keyindex']);
					$arrValues['path'] = cleanPath($arrValues['path']);
					}
				//on check si un key qui existe deja, pas de duplicate
				$query = 'SELECT '.DB_PREFIX.'links.id FROM '.DB_PREFIX.'links WHERE '.DB_PREFIX.'links.keyindex LIKE "'.$arrValues['keyindex'].'" AND '.DB_PREFIX.'links.id <> "'.$arrValues['id'].'" LIMIT 0,1;';
				$rs = $this->reg->get('db')->query($query);
				if($rs->num_rows){
					array_push($this->arrFormErrors, 'key');
					$this->strMsg .= '<li>'._T('field Key already exist.').'</li>';
					return false;	
					}
				//on check si un path qui existe deja, pas de duplicate	
				$query = 'SELECT '.DB_PREFIX.'links.id FROM '.DB_PREFIX.'links WHERE '.DB_PREFIX.'links.path LIKE "'.$arrValues['path'].'" AND '.DB_PREFIX.'links.id <> "'.$arrValues['id'].'" LIMIT 0,1;';
				$rs = $this->reg->get('db')->query($query);
				if($rs->num_rows){
					array_push($this->arrFormErrors, 'path');
					$this->strMsg .= '<li>'._T('field Path already exist.').'</li>';
					return false;	
					}
				//si on est la c'est que l'on peut insert
				$query = 'UPDATE '.DB_PREFIX.'links SET status = "'.$arrValues['status'].'", extern = "'.$arrValues['extern'].'", name = "'.$arrValues['name'].'", keyindex = "'.$arrValues['keyindex'].'", path = "'.$arrValues['path'].'", language_id = "'.$arrValues['language_id'].'", date_modified = NOW() WHERE '.DB_PREFIX.'links.id = "'.$arrValues['id'].'";';
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
	public function isDuplicateLinksKeyIndex($str, $id = ''){
		$strWhere = '';
		if($id != ''){
			$strWhere = ' AND '.DB_PREFIX.'links.id != "'.$id.'" ';
			}
		$query = 'SELECT '.DB_PREFIX.'links.id FROM '.DB_PREFIX.'links WHERE '.DB_PREFIX.'links.keyindex LIKE "'.$str.'" '.$strWhere.' LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return true;	
			}
		return false;
		}
		
	//------------------------------------------------------------------------------------------------	
	public function isDuplicateLinksPath($str, $id = ''){
		$strWhere = '';
		if($id != ''){
			$strWhere = ' AND '.DB_PREFIX.'links.id != "'.$id.'" ';
			}
		$query = 'SELECT '.DB_PREFIX.'links.id FROM '.DB_PREFIX.'links WHERE '.DB_PREFIX.'links.path LIKE "'.$str.'" '.$strWhere.' LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return true;	
			}
		return false;
		}	
		
	//------------------------------------------------------------------------------------------------		
	
	public function newLinksInfos($arr){
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
				}else if($v['name'] == 'extern'){
					if($v['value'] == 'on'){
						$arrValues['extern'] = '1';
					}else{
						$arrValues['extern'] = '0';
						}
				}else if($v['name'] == 'name'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Name is required.').'</li>';
						}
					$arrValues['name'] = sqlSafe($v['value']);
				}else if($v['name'] == 'keyindex'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Key is required.').'</li>';
						}
					$arrValues['keyindex'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'path'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Path is required.').'</li>';
						}
					$arrValues['path'] = sqlSafe($v['value']);		
				}else if($v['name'] == 'language_id'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('field Language is required.').'</li>';
						}
					$arrValues['language_id'] = intVal($v['value']);		
					}
				}	
			//
			if(count($this->arrFormErrors) != 0){
				return false;
			}else{
				if(!isset($arrValues['status'])){
					$arrValues['status'] = '0';
					}	
				if(!isset($arrValues['extern'])){
					$arrValues['extern'] = '0';
					}	
				//on strip le path et la key pour enlever les blank space et charactere speciaux
				if($arrValues['extern'] != '1'){
					$arrValues['keyindex'] = cleanKeyIndex($arrValues['keyindex']);
					$arrValues['path'] = cleanPath($arrValues['path']);
					}
				//on check si un key qui existe deja, pas de duplicate
				$query = 'SELECT '.DB_PREFIX.'links.id FROM '.DB_PREFIX.'links WHERE '.DB_PREFIX.'links.keyindex LIKE "'.$arrValues['keyindex'].'" LIMIT 0,1;';
				$rs = $this->reg->get('db')->query($query);
				if($rs->num_rows){
					array_push($this->arrFormErrors, 'key');
					$this->strMsg .= '<li>'._T('field Key already exist.').'</li>';
					return false;	
					}
				//on check si un path qui existe deja, pas de duplicate	
				$query = 'SELECT '.DB_PREFIX.'links.id FROM '.DB_PREFIX.'links WHERE '.DB_PREFIX.'links.path LIKE "'.$arrValues['path'].'" LIMIT 0,1;';
				$rs = $this->reg->get('db')->query($query);
				if($rs->num_rows){
					array_push($this->arrFormErrors, 'path');
					$this->strMsg .= '<li>'._T('field Path already exist.').'</li>';
					return false;	
					}
				//si on est la c'est que l'on peut insert
				$query = 'INSERT INTO '.DB_PREFIX.'links (status, name, keyindex, path, language_id, extern, date_modified) VALUES("'.$arrValues['status'].'", "'.$arrValues['name'].'", "'.$arrValues['keyindex'].'", "'.$arrValues['path'].'", "'.$arrValues['language_id'].'", "'.$arrValues['extern'].'", NOW());';
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
		
	public function disableLinksInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'links SET status = "0" WHERE '.DB_PREFIX.'links.id IN ('.$arr[0]['value'].') AND '.DB_PREFIX.'links.access = "1";';
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
		
	public function enableLinksInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'links SET status = "1" WHERE '.DB_PREFIX.'links.id IN ('.$arr[0]['value'].') AND '.DB_PREFIX.'links.access = "1";';
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
	
	public function deleteLinksInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'DELETE FROM '.DB_PREFIX.'links WHERE '.DB_PREFIX.'links.id IN ('.$arr[0]['value'].') AND '.DB_PREFIX.'links.access = "1";';
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
	
	public function getLinksInfos($id){	
		$query = 'SELECT '.DB_PREFIX.'links.status AS "status", '.DB_PREFIX.'links.id AS "id", '.DB_PREFIX.'links.name AS "name", '.DB_PREFIX.'links.keyindex AS "keyindex", '.DB_PREFIX.'links.path AS "path", '.DB_PREFIX.'links.language_id AS "language_id", '.DB_PREFIX.'links.extern AS "extern" FROM '.DB_PREFIX.'links WHERE '.DB_PREFIX.'links.id = "'.$id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0];	
			}
		return false;		
		}	

	//------------------------------------------------------------------------------------------------		
	
	public function newDirectLinksInfos($arrValues, $bDisableAccess = false){
		if($bDisableAccess){
			$arrValues['access'] = 0;
		}else{
			$arrValues['access'] = 1;
			}
		$query = 'INSERT INTO '.DB_PREFIX.'links (status, name, keyindex, path, language_id, access, date_modified) VALUES("'.$arrValues['status'].'", "'.$arrValues['name'].'", "'.$arrValues['keyindex'].'", "'.$arrValues['path'].'", "'.$arrValues['language_id'].'", "'.$arrValues['access'].'", NOW());';
		$this->reg->get('db')->query($query);
		//clean la cache
		$this->deleteCache();
		//on va chercher le ID du link
		$query = 'SELECT '.DB_PREFIX.'links.id AS "id" FROM '.DB_PREFIX.'links WHERE '.DB_PREFIX.'links.keyindex = "'.$arrValues['keyindex'].'" AND '.DB_PREFIX.'links.path = "'.$arrValues['path'].'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0]['id'];	
			}
		return false;	
		}

	//------------------------------------------------------------------------------------------------		
	
	public function setDirectLinksInfos($arrValues, $bRedirect = false){
		if($bRedirect){
			$arrLink = $this->getLinksInfos($arrValues['id']);
			if(isset($arrLink['path']) && ($arrLink['path'] != $arrValues['path'])){
				//on check si c'est le meme path si non alors ont creer un redirect avec l'ancien path
				$query = 'INSERT INTO '.DB_PREFIX.'links (status, path, language_id, date_modified, has_moved) VALUES("0", "'.$arrLink['path'].'", "'.$arrLink['language_id'].'", NOW(), "1");';
				$this->reg->get('db')->query($query);
				}
			}
		$query = 'UPDATE '.DB_PREFIX.'links SET status = "'.$arrValues['status'].'", name = "'.$arrValues['name'].'", keyindex = "'.$arrValues['keyindex'].'", path = "'.$arrValues['path'].'", language_id = "'.$arrValues['language_id'].'", date_modified = NOW() WHERE '.DB_PREFIX.'links.id = "'.$arrValues['id'].'";';
		$this->reg->get('db')->query($query);
		//clean la cache
		$this->deleteCache();
		//retour
		//backup 502 reditect
		return true;	
		}	

	//------------------------------------------------------------------------------------------------		
	
	public function deleteCache(){
		require_once(DIR_CLASS.'cache.php');
		$oCache = new Cache($this->reg);
		$oCache->delete('links.*');		
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