<?php
class Utils {
	
	private $reg;
	private $arrFormErrors;
	private $strMsg;
		
	public function __construct($reg) {
		$this->reg = $reg;
		}
		
		
	//--------------------------------------------------------------------------------------------------		
	public function importUsersEmailCsvFile(){
		$this->strMsg = '';
		//
		$query = 'SELECT '.DB_PREFIX.'user.email as "email", '.DB_PREFIX.'user.firstname as "firstname", '.DB_PREFIX.'user.lastname as "lastname" FROM '.DB_PREFIX.'user;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			$strPrintFile = '';
			foreach($rs->rows as $k=>$v){
				$strPrintFile .= '"'.mb_strtolower($v['firstname'], 'UTF-8').'","'.mb_strtolower($v['lastname'], 'UTF-8').'","'.mb_strtolower($v['email'], 'UTF-8').'"'.EOL;
				}
			if($strPrintFile == ''){
				return false;
				}
			$csvname = 'useremails_'.time().'.csv';
			$this->writeNewFile(DIR_CSV.$csvname, $strPrintFile);	
			return PATH_CSV.$csvname;
                        }
		return false;
		}
		
		
	//--------------------------------------------------------------------------------------------------		
	public function getProvince(){
		$query = 'SELECT '.DB_PREFIX.'province.id AS "id", '.DB_PREFIX.'province.name AS "name", '.DB_PREFIX.'province.code AS "code" FROM '.DB_PREFIX.'province  ORDER BY '.DB_PREFIX.'province.id ASC;;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;
			}
		return false;	
		}	

	//--------------------------------------------------------------------------------------------------		
	public function getColors(){
		$query = 'SELECT '.DB_PREFIX.'colors.id AS "id", '.DB_PREFIX.'colors.name AS "name", '.DB_PREFIX.'colors.hex AS "hex" FROM '.DB_PREFIX.'colors  ORDER BY '.DB_PREFIX.'colors.id ASC;;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;
			}
		return false;	
		}
	
	//--------------------------------------------------------------------------------------------------		
	public function getBodyParts(){
		$query = 'SELECT '.DB_PREFIX.'bodyparts.id AS "id", '.DB_PREFIX.'bodyparts.name AS "name"  FROM '.DB_PREFIX.'bodyparts ORDER BY '.DB_PREFIX.'bodyparts.id ASC;;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;
			}
		return false;	
		}
	
	//--------------------------------------------------------------------------------------------------		
	public function getSiteAdminText($arrFilter = array()){
		$strWhere = '';
		if(isset($arrFilter['langue_page']) && $arrFilter['langue_page'] != '-1'){
			$strWhere = ' WHERE '.DB_PREFIX.'site_admin_langue.page = "'.$arrFilter['langue_page'].'" ';
			}
		$query = 'SELECT '.DB_PREFIX.'site_admin_langue.id AS "id", '.DB_PREFIX.'site_admin_langue.name AS "name", '.DB_PREFIX.'site_admin_langue.page AS "page", '.DB_PREFIX.'site_admin_langue.name_fr AS "name_fr" , '.DB_PREFIX.'site_admin_langue.name_en AS "name_en" FROM '.DB_PREFIX.'site_admin_langue '.$strWhere.' ORDER BY '.DB_PREFIX.'site_admin_langue.page ASC;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;
			}
		return false;	
		}		
	//--------------------------------------------------------------------------------------------------		
	public function getSiteText($arrFilter = array()){
		$strWhere = '';
		if(isset($arrFilter['langue_page']) && $arrFilter['langue_page'] != '-1'){
			$strWhere = ' WHERE '.DB_PREFIX.'site_langue.page = "'.$arrFilter['langue_page'].'" ';
			}
		$query = 'SELECT '.DB_PREFIX.'site_langue.id AS "id", '.DB_PREFIX.'site_langue.name AS "name", '.DB_PREFIX.'site_langue.page AS "page", '.DB_PREFIX.'site_langue.name_en AS "name_en", '.DB_PREFIX.'site_langue.name_fr AS "name_fr", '.DB_PREFIX.'site_langue.name_es AS "name_es" FROM '.DB_PREFIX.'site_langue '.$strWhere.' ORDER BY '.DB_PREFIX.'site_langue.page ASC;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;
			}
		return false;	
		}	
	//--------------------------------------------------------------------------------------------------		
	public function getSingleSiteText($id, $lang){
		$query = 'SELECT '.DB_PREFIX.'site_langue.name_'.$lang.' AS "txt" FROM '.DB_PREFIX.'site_langue WHERE '.DB_PREFIX.'site_langue.id = "'.$id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0]['txt'];
			}
		return false;
		}	
	//--------------------------------------------------------------------------------------------------		
	public function getSingleSiteAdminText($id, $lang){
		$query = 'SELECT '.DB_PREFIX.'site_admin_langue.name_'.$lang.' AS "txt" FROM '.DB_PREFIX.'site_admin_langue WHERE '.DB_PREFIX.'site_admin_langue.id = "'.$id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0]['txt'];
			}
		return false;
		}		
	//--------------------------------------------------------------------------------------------------	
	public function writeNewFile($filename, $content){
		$fh = fopen($filename, 'w');
		if($fh){
			fwrite($fh, $content);
			fclose($fh);
			}
		}	
	//--------------------------------------------------------------------------------------------------	
	public function addHoursToDate($date, $hours){
		return date("Y-m-d H:i:s", strtotime($date) + ((60*60) * $hours));
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