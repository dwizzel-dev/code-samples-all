<?php
class Reponse {
	
	private $reg;
	private $arrFormErrors;
	private $strMsg;
	
	//------------------------------------------------------------------------------------------------	
	
	public function __construct($reg) {
		$this->reg = $reg;

		}
		
		
	//------------------------------------------------------------------------------------------------		
	
	public function getReponseInfos($id){
		$query = 'SELECT '.DB_PREFIX.'reponse.id as "id", '.DB_PREFIX.'reponse.name as "name", '.DB_PREFIX.'reponse.date_modified as "date_modified", '.DB_PREFIX.'reponse.text as "text" FROM '.DB_PREFIX.'reponse WHERE '.DB_PREFIX.'reponse.id = "'.$id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0];	
			}
		return false;
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