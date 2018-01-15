<?php
class Users {
	
	private $reg;
	private $arrFormErrors;
	private $strMsg;
	
	//------------------------------------------------------------------------------------------------	
	
	public function __construct($reg) {
		$this->reg = $reg;

		}
		
	//------------------------------------------------------------------------------------------------		
	
	public function getUsersListing($iStart, $iLimit, $arrFilters = array()){
		$strWhere = '';
		$strSortBy = '';
		//sort
		if(isset($arrFilters['sortby-id']) && $arrFilters['sortby-id'] != ''){
			if($arrFilters['sortby-id'] == 'id'){
				$strSortBy = ' ORDER BY '.DB_PREFIX.'user.id ASC ';
			}else if($arrFilters['sortby-id'] == 'name'){
				$strSortBy = ' ORDER BY name ASC ';
			}else if($arrFilters['sortby-id'] == 'username'){
				$strSortBy = ' ORDER BY '.DB_PREFIX.'user.username ASC ';
			}else{
				$strSortBy = ' ORDER BY '.DB_PREFIX.'user.date_added DESC ';
				}
		}else{
			$strSortBy = ' ORDER BY '.DB_PREFIX.'user.date_added DESC ';
			}
		//filter
		if(isset($arrFilters['searchitems-id']) && $arrFilters['searchitems-id'] != ''){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= ' ('.DB_PREFIX.'user.firstname LIKE "%'.$arrFilters['searchitems-id'].'%" || '.DB_PREFIX.'user.lastname LIKE "%'.$arrFilters['searchitems-id'].'%" || '.DB_PREFIX.'user.tel_1 LIKE "%'.$arrFilters['searchitems-id'].'%" || '.DB_PREFIX.'user.username LIKE "%'.$arrFilters['searchitems-id'].'%"'.') ';
			}
		if(isset($arrFilters['display-id']) && $arrFilters['display-id'] != '' && $arrFilters['display-id'] != '2' || $arrFilters['display-id'] == '0'){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= DB_PREFIX.'user.status = "'.$arrFilters['display-id'].'" ';
			}	
	
	
		$query = 'SELECT '.DB_PREFIX.'user.date_added as "date_added", '.DB_PREFIX.'user.status as "status", '.DB_PREFIX.'user.id as "id", '.DB_PREFIX.'user.username as "username", '.DB_PREFIX.'user.tel_1 as "tel_1", CONCAT('.DB_PREFIX.'user.firstname," ",'.DB_PREFIX.'user.lastname) as "name", '.DB_PREFIX.'user.age as "age" FROM '.DB_PREFIX.'user '.$strWhere.' '.$strSortBy.' LIMIT '.$iStart.','.$iLimit.';';
		//exit($query);
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;	
			}
		return false;
		}
	
	//------------------------------------------------------------------------------------------------		
	public function getUsersListingCount($arrFilters = array()){
		$strWhere = '';
		//filter
		if(isset($arrFilters['searchitems-id']) && $arrFilters['searchitems-id'] != ''){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= ' ('.DB_PREFIX.'user.firstname LIKE "%'.$arrFilters['searchitems-id'].'%" || '.DB_PREFIX.'user.lastname LIKE "%'.$arrFilters['searchitems-id'].'%" || '.DB_PREFIX.'user.tel_1 LIKE "%'.$arrFilters['searchitems-id'].'%" || '.DB_PREFIX.'user.username LIKE "%'.$arrFilters['searchitems-id'].'%"'.') ';
			}
		if(isset($arrFilters['display-id']) && $arrFilters['display-id'] != '' && $arrFilters['display-id'] != '2' || $arrFilters['display-id'] == '0'){
			if($strWhere != ''){
				$strWhere .= ' AND ';
			}else{
				$strWhere .= ' WHERE ';
				}
			$strWhere .= DB_PREFIX.'user.status = "'.$arrFilters['display-id'].'" ';
			}	
		$query = 'SELECT COUNT('.DB_PREFIX.'user.id) AS "count" FROM '.DB_PREFIX.'user '.$strWhere.' ;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0]['count'];
			}
		return false;	
		}
		
	//------------------------------------------------------------------------------------------------		
	
	public function getUsersInfos($id){
		$query = 'SELECT '.DB_PREFIX.'user.status as "status", '.DB_PREFIX.'user.id as "id", '.DB_PREFIX.'user.firstname as "firstname", '.DB_PREFIX.'user.lastname as "lastname", '.DB_PREFIX.'user.tel_1 as "tel_1", '.DB_PREFIX.'user.username as "username", '.DB_PREFIX.'user.password as "password", '.DB_PREFIX.'user.age as "age", '.DB_PREFIX.'user.date_added as "date_added", '.DB_PREFIX.'user.date_modified as "date_modified", '.DB_PREFIX.'user.infolettre as "infolettre", CONCAT('.DB_PREFIX.'user.firstname," ",'.DB_PREFIX.'user.lastname) as "name" FROM '.DB_PREFIX.'user WHERE '.DB_PREFIX.'user.id = "'.$id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			//on va chercher les adresses
			/*
			$query = 'SELECT '.DB_PREFIX.'adresse.adresse as "adresse", '.DB_PREFIX.'adresse.pays as "pays", '.DB_PREFIX.'adresse.ville as "ville", '.DB_PREFIX.'adresse.province as "province", '.DB_PREFIX.'adresse.code_postal as "code_postal", '.DB_PREFIX.'adresse.date_added as "date_added", '.DB_PREFIX.'adresse.date_modified as "date_modified" FROM '.DB_PREFIX.'adresse WHERE '.DB_PREFIX.'adresse.user_id = "'.$id.'";';
			$rs2 = $this->reg->get('db')->query($query);
			if($rs2->num_rows){
				$rs->rows[0]['adresses'] = $rs2->rows;
				}
			*/
			//on va chercher la liste de IP
			$query = 'SELECT '.DB_PREFIX.'user_ip.ip as "ip", '.DB_PREFIX.'user_ip.date_added as "date_added" FROM '.DB_PREFIX.'user_ip WHERE '.DB_PREFIX.'user_ip.user_id = "'.$id.'" ORDER BY '.DB_PREFIX.'user_ip.date_added DESC;';
			$rs3 = $this->reg->get('db')->query($query);
			if($rs3->num_rows){
				$rs->rows[0]['ips'] = $rs3->rows;
				}
			//on va chercher les transactions
			/*
			$query = 'SELECT '.DB_PREFIX.'transaction.facture_id as "facture_id", '.DB_PREFIX.'transaction.total as "total", '.DB_PREFIX.'transaction.transaction_date as "transaction_date", '.DB_PREFIX.'transaction.transaction_no as "transaction_no", '.DB_PREFIX.'transaction.adresse_tel_concat as "adresse_tel_concat" FROM '.DB_PREFIX.'transaction WHERE '.DB_PREFIX.'transaction.user_id = "'.$id.'" ORDER BY '.DB_PREFIX.'transaction.date_added DESC;';
			$rs4 = $this->reg->get('db')->query($query);
			if($rs4->num_rows){
				$rs->rows[0]['transactions'] = $rs4->rows;
				}	
			*/
			return $rs->rows[0];	
			}
		return false;
		}

	//------------------------------------------------------------------------------------------------		
		
	public function disableUsersInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'user SET status = "0" WHERE '.DB_PREFIX.'user.id IN ('.$arr[0]['value'].');';
				$this->reg->get('db')->query($query);
				return true;
				}
			}
		return false;	
		}
		
	//------------------------------------------------------------------------------------------------		
		
	public function enableUsersInfos($arr){	
		if(isset($arr) && is_array($arr) && count($arr)){
			if(isset($arr[0]['name']) && isset($arr[0]['value']) && $arr[0]['name'] == 'cbchecked' && $arr[0]['value'] != ''){
				$query = 'UPDATE '.DB_PREFIX.'user SET status = "1" WHERE '.DB_PREFIX.'user.id IN ('.$arr[0]['value'].');';
				$this->reg->get('db')->query($query);
				return true;
				}
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