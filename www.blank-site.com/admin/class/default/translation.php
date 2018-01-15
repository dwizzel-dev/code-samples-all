<?php
class Translation {
	
	private $reg;
	private $date;
	private $time;
	
	//------------------------------------------------------------------------------------------------	
	
	public function __construct($reg) {
		$this->reg = $reg;
		date_default_timezone_set('America/New_York'); 
		$this->date = date('dmY');
		$this->time = date('H:i:s');
		}
	
	//------------------------------------------------------------------------------------------------		
	
	public function getDataFields(){
		$query = 'SELECT DISTINCT('.DB_PREFIX.'site_langue.page) FROM '.DB_PREFIX.'site_langue ORDER BY '.DB_PREFIX.'site_langue.page;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;	
			}
		return false;
		}
	
	//------------------------------------------------------------------------------------------------		
	
	public function getData($data){
		if(isset($data)){
			if($data != ''){
				$query = 'SELECT '.DB_PREFIX.'site_langue.id as "id", '.DB_PREFIX.'site_langue.name as "key", '.DB_PREFIX.'site_langue.name_fr as "name_fr", '.DB_PREFIX.'site_langue.name_en as "name_en", '.DB_PREFIX.'site_langue.name_es as "name_es" FROM '.DB_PREFIX.'site_langue WHERE '.DB_PREFIX.'site_langue.page = "'.$data.'" ORDER BY '.DB_PREFIX.'site_langue.name;';
			}else{
				$query = 'SELECT '.DB_PREFIX.'site_langue.id as "id", '.DB_PREFIX.'site_langue.name as "key", '.DB_PREFIX.'site_langue.name_fr as "name_fr", '.DB_PREFIX.'site_langue.name_en as "name_en", '.DB_PREFIX.'site_langue.name_es as "name_es" FROM '.DB_PREFIX.'site_langue ORDER BY '.DB_PREFIX.'site_langue.name;';
				}
			$rs = $this->reg->get('db')->query($query);
			if($rs->num_rows){
				$arrSend = array(
					'columns' => array('id','key','name_fr','name_en','name_es'),
					'rows' => $rs->rows,
					);
				
				return $arrSend;
				}
			}
		return false;
		}	
		
	//------------------------------------------------------------------------------------------------		
	
	public function getDataFromSearch($data){
		if(isset($data) && $data != ''){
			$query = 'SELECT '.DB_PREFIX.'site_langue.id as "id", '.DB_PREFIX.'site_langue.name as "key", '.DB_PREFIX.'site_langue.name_fr as "name_fr", '.DB_PREFIX.'site_langue.name_en as "name_en", '.DB_PREFIX.'site_langue.name_es as "name_es" FROM '.DB_PREFIX.'site_langue WHERE LOWER('.DB_PREFIX.'site_langue.name) LIKE LOWER("%'.sqlSafe($data).'%") OR LOWER('.DB_PREFIX.'site_langue.name_en) LIKE LOWER("%'.sqlSafe($data).'%") OR LOWER('.DB_PREFIX.'site_langue.name_fr) LIKE LOWER("%'.sqlSafe($data).'%") OR LOWER('.DB_PREFIX.'site_langue.name_es) LIKE LOWER("%'.sqlSafe($data).'%") ORDER BY '.DB_PREFIX.'site_langue.name;';
			$rs = $this->reg->get('db')->query($query);
			if($rs->num_rows){
				$arrSend = array(
					'columns' => array('id','key','name_fr','name_en','name_es'),
					'rows' => $rs->rows,
					);
				return $arrSend;
				}
			}
		return false;
		}	
		
	//------------------------------------------------------------------------------------------------	
		
	public function generateData(){
		if(file_exists(DIR_SCRIPTS.'populate_hash_lang.php')){
			require_once(DIR_SCRIPTS.'populate_hash_lang.php');
		}else{
			return false;
			}
		return true;
		}
		
	//------------------------------------------------------------------------------------------------	
		
	public function setData($arr){
		$bError = false;
		if(isset($arr) && is_array($arr) && count($arr)){
			foreach($arr as $k=>$v){
				if(isset($v['id']) && isset($v['field']) && isset($v['data'])){ //check si exist
					$field = $v['field'];
					$data = $v['data'];
					$id = intVal($v['id']);
					if($id != 0 && $field != ''){ //check si valide
						$query = 'UPDATE '.DB_PREFIX.'site_langue SET date_modified = NOW(), '.$field.' = "'.sqlSafe($data).'" WHERE id = "'.$id.'";';
						$this->reg->get('db')->query($query);
						}
					}
				}
		}else{
			$bError = true;
			}
		if(!$bError){
			return true;
			}
		return false;
		}		
		
	}
?>