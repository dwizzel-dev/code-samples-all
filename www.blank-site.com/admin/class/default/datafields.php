<?php
class Datafields {
	
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
		
	public function generateData(){
		require_once(DIR_SCRIPTS.'populate_hash_from_table.php');
		return true;
		}	
	
	//------------------------------------------------------------------------------------------------		
	
	public function getDataFields(){
		$arr =array(
			array(
				'id'=>'', 
				'text'=>_T('--')
				),
			array(
				'id'=>'colors|id,name,hex', 
				'text'=>_T('colors')
				),
			array(
				'id'=>'bodyparts|id,name', 
				'text'=>_T('bodyparts')
				),	
			);
		return $arr;	
		}
	
	//------------------------------------------------------------------------------------------------		
	/*
	 some of tables are specials, like colors we will put a filter because the listing is too long
	
	*/
	
	
	
	public function getData($data){
		//we need the table_name
		//we send the table_name, field_name, field_values
		if(isset($data) && $data != ''){
			//on slipt  pour savoir la table
			$arr = explode('|', $data);
			if(isset($arr[0]) && isset($arr[1])){
				$strTable = $arr[0];
				//le nom des champs
				$strChamps = $arr[1];
				//query
				$query = 'SELECT '.$strChamps.' FROM '.DB_PREFIX.$strTable.' ORDER BY '.DB_PREFIX.$strTable.'.id;';
				$rs = $this->reg->get('db')->query($query);
				if($rs->num_rows){
					$arrSend = array(
						'table' => $strTable,
						'columns' => $arrFields = explode(',', $strChamps),
						'rows' => $rs->rows,
						);
					return $arrSend;	
					}
				return false;
				}
			}
		return false;
		}	
		
	//------------------------------------------------------------------------------------------------	
		
	public function setData($tableName, $arr, $arrNew){
		/*
		
		IMPORTANT FAIRE UN CHECK SUR LES TABLES QUI ONT LE DROIT D"ETRE EDITE
		SOIT CELLE QUI SE TROUVE DANS getDataFields()	
		
		
		
		FORMAT:
			["section"] = "datafields"
			["service"] = "set-datafields-infos"
			["data"] = "[
				{"id":"74","field":"name","data":"Chicago Bearsdfsdfsdfsdfs"},
				{"id":"76","field":"name","data":"Green Bay Pacsdfsdfsdfkers"},
				{"id":"75","field":"name","data":"Detrosdfsdfsdfit Lions"}
				]"
			["table"] = "team"
			
			-----------------
			
			["data"] = "[{"id":"new","field":"name","data":"cgvbcvb"},{"id":"new","field":"status","data":"cvbcvbcvb"},{"id":"new","field":"name","data":"cvbcvb"},{"id":"new","field":"status","data":"cbvcvbcbv"}]"
			["insert"] = "[
				{"row":"7","keys":["name","status"],"values":["cgvbcvb","cvbcvbcvb"]},
				{"row":"8","keys":["name","status"],"values":["cvbcvb","cbvcvbcbv"]}
				]"
			["table"] = "product_coupe"
			
			
		*/
		//print_r($arr);
		$bError = false;
		if(isset($tableName) && $tableName != '' && ((isset($arr) && is_array($arr) && count($arr)) || (isset($arrNew) && is_array($arrNew) && count($arrNew)))){
			//old ones
			if(isset($arr) && is_array($arr) && count($arr)){
				foreach($arr as $k=>$v){
					if(isset($v['id']) && isset($v['field']) && isset($v['data'])){ //check si exist
						$field = $v['field'];
						$data = $v['data'];
						$id = intVal($v['id']);
						if($id != 0 && $field != ''){ //check si valide
							$query = 'UPDATE '.DB_PREFIX.$tableName.' SET date_modified = NOW(), '.$field.' = "'.sqlSafe($data).'" WHERE id = "'.$id.'";';
							$this->reg->get('db')->query($query);
							}
						}
					}
				}
			//new one
			if(isset($arrNew) && is_array($arrNew) && count($arrNew)){
				$arrSend = array();
				foreach($arrNew as $k=>$v){
					$strKeys = '';
					$strValues = '';
					foreach($v['keys'] as $k2=>$v2){
						$strKeys .= $v2.',';
						}
					foreach($v['values'] as $k2=>$v2){
						$strValues .= '"'.sqlSafe($v2).'",';
						}	
					if($strKeys != ''){
						$strKeys = substr($strKeys, 0, (strlen($strKeys) - 1));
						}
					if($strValues != ''){
						$strValues = substr($strValues, 0, (strlen($strValues) - 1));
						}
					//on va chercher le dernier ID max
					$newId = 0;
					$query = 'SELECT MAX('.DB_PREFIX.$tableName.'.id) as "max" FROM '.DB_PREFIX.$tableName.';';		
					$rs = $this->reg->get('db')->query($query);
					if($rs->num_rows){
						$newId = intVal($rs->rows[0]['max']) + 1;
						}
					$query = 'INSERT INTO '.DB_PREFIX.$tableName.' ('.$strKeys.', date_modified, id) VALUES('.$strValues.',NOW(), "'.$newId.'");';
					$this->reg->get('db')->query($query);
					//le send back
					array_push($arrSend, array('row'=>$v['row'], 'id'=>$newId));
					}
				return $arrSend;	
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