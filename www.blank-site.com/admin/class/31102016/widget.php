<?php
class Widget {

	private $className = 'Widget';
	private $reg;
	private $arrFormErrors;
	private $strMsg;
	
	public function __construct($reg) {
		$this->reg = $reg;
		}
		
	//------------------------------------------------------------------------------------------------	
	
	//on va chercher dans la DB
	public function getWidgetListing(){	
		$query = 'SELECT '.DB_PREFIX.'widget_category.name AS "category", '.DB_PREFIX.'languages.prefix AS "language", '.DB_PREFIX.'widget.alias AS "alias", '.DB_PREFIX.'widget.language_id AS "language_id", '.DB_PREFIX.'widget.id AS "id", '.DB_PREFIX.'widget.status AS "status", '.DB_PREFIX.'widget.category_id AS "category_id", '.DB_PREFIX.'widget.name AS "name", '.DB_PREFIX.'widget.data AS "data" FROM '.DB_PREFIX.'widget LEFT JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'widget.language_id LEFT JOIN '.DB_PREFIX.'widget_category ON '.DB_PREFIX.'widget.category_id = '.DB_PREFIX.'widget_category.id ORDER BY '.DB_PREFIX.'widget_category.name ASC, '.DB_PREFIX.'languages.prefix ASC;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;
			}
		return false;
		}
		
	//------------------------------------------------------------------------------------------------	
	
	//on va chercher dans la DB
	public function getWidgetInfos($id){	
		$query = 'SELECT '.DB_PREFIX.'widget_category.name AS "category", '.DB_PREFIX.'widget.alias AS "alias", '.DB_PREFIX.'widget.language_id AS "language_id", '.DB_PREFIX.'widget.id AS "id", '.DB_PREFIX.'widget.status AS "status", '.DB_PREFIX.'widget.category_id AS "category_id", '.DB_PREFIX.'widget.name AS "name", '.DB_PREFIX.'widget.data AS "data" FROM '.DB_PREFIX.'widget LEFT JOIN '.DB_PREFIX.'widget_category ON '.DB_PREFIX.'widget_category.id = '.DB_PREFIX.'widget.category_id WHERE '.DB_PREFIX.'widget.id = "'.$id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0];
			}
		return false;
		}	
		
	//------------------------------------------------------------------------------------------------			
	
	//on va chercher dans la DB
	public function getWidgetCategoryDropBox(){	
		$query = 'SELECT '.DB_PREFIX.'widget_category.name AS "name", '.DB_PREFIX.'widget_category.id AS "id" FROM '.DB_PREFIX.'widget_category;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;
			}
		return false;
		}

	//------------------------------------------------------------------------------------------------		
		
	public function disableWidgetInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'widget SET status = "0" WHERE '.DB_PREFIX.'widget.id IN ('.$arr[0]['value'].');';
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
		
	public function enableWidgetInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'widget SET status = "1" WHERE '.DB_PREFIX.'widget.id IN ('.$arr[0]['value'].');';
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
	
	public function modifyWidgetInfos($id = 0, $arr){
		//action: 0 = rien, 1 = new, 2 = to delete from db
		$widget_id = intVal($id);
		if(isset($arr) && is_array($arr) && $widget_id != 0){
			array_walk_recursive($arr, 'formatSerialize');	
			$query = 'UPDATE '.DB_PREFIX.'widget SET '.DB_PREFIX.'widget.data = \''.serialize($arr).'\' WHERE '.DB_PREFIX.'widget.id = "'.$widget_id.'";';
			$rs = $this->reg->get('db')->query($query);
			//clean la cache
			$this->deleteCache();
			//retour
			return true;
			}
		return false;
		}	
		
		
	//------------------------------------------------------------------------------------------------		
	
	public function getIconList($basepath){
		$arrValues = array();
		$arrFiles = scandir($basepath);
		foreach($arrFiles as $k=>$v){
			if($v != '.' && $v != '..' && is_file($basepath.$v)){
				array_push($arrValues, $v);
				}
			}
		return $arrValues;
		}

	//------------------------------------------------------------------------------------------------		

	public function getLinksForCheckboxByLang($langId){
		$query = 'SELECT '.DB_PREFIX.'links.id AS "id", '.DB_PREFIX.'links.path AS "path" FROM '.DB_PREFIX.'links WHERE '.DB_PREFIX.'links.extern = 0 AND '.DB_PREFIX.'links.language_id = '.intVal($langId).' AND '.DB_PREFIX.'links.status = 1 ORDER BY '.DB_PREFIX.'links.path ASC;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;
			}
		return false;
		}

	//------------------------------------------------------------------------------------------------		
	
	public function deleteCache(){
		require_once(DIR_CLASS.'cache.php');
		$oCache = new Cache($this->reg);
		$oCache->delete('widget.*');		
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