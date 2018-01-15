<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	Utilities class used by all other classes 
@modif:	
	
	19-08-2016 16:14
		- add the method getClinicOptionTitle to retrieve the title of the clinic title
		- add the method getLicenceOptionTitle to retrieve the title of the licence	


*/


class Utility {

	private $reg;
	private $bTrace = false;
	private $className = 'Utility';

	//-------------------------------------------------------------------------------------------------------------
	public function __construct(&$reg) {
		$this->reg = $reg;
		
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function getClassName(){
		return $this->className;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function getClassObject(){
		return $this;
		}
	
	//-------------------------------------------------------------------------------------------------------------	
	//Fn:return the clients of this user based on the access level
	public function getUserClients(){
		$idUser = $this->reg->get('sess')->get('idUser');
		$access_all_clients	= $this->reg->get('sess')->get('access_all_clients');
		$licence_data = $this->reg->get('sess')->get('licence');
		$idLicence = $licence_data['idLicence'];
		$clinic_data = $this->reg->get('sess')->get('clinic');
		$idClinic = $clinic_data['idClinic'];
		$licence_admin = $this->reg->get('sess')->get('licence_admin');
		//
		$clientsArr  = array();
		$user_ids = array();
		$users_array = array();
		//
		if($licence_admin == 1){
			$user_ids = $this->getLicenceUsers();
		}else if($access_all_clients == 1){
			$user_ids = $this->getClinicUsers();
		}else{
			$user_ids = array(
				0 => $idUser
				);
			}
		//
		foreach($user_ids AS $k=>$v){
			$query = 'SELECT firstname, lastname FROM user WHERE idUser="'.intVal($v).'";';
			$rs = $this->reg->get('db')->query($query);
			if($rs && $rs->num_rows){
				foreach($rs->rows AS $idRow =>$row){
					$users_array[$v]['firstname'] = decodeString($row['firstname']);
					$users_array[$v]['lastname']  = decodeString($row['lastname']);
					}
				}
			unset($rs);
			}
		
		//
		$query = 'SELECT idClient, idUser, username, active, qty_try FROM licence_client WHERE idUser IN ('.implode(', ', $user_ids).') AND idLicence = "'.intVal($idLicence).'" AND active = "1";';
		$rs = $this->reg->get('db')->query($query);
		if($rs && $rs->num_rows){
			foreach($rs->rows AS $idRow =>$row){
				$idClient = $row['idClient'];
				$User_id = $row['idUser'];
				$username = $row['username'];
				$active = $row['active'];
				$qty_try = $row['qty_try'];
				//
				$query_external = 'SELECT department, file, firstname, lastname, locale, email, email2, creation_date FROM client WHERE idClient = "'.intVal($idClient).'" LIMIT 0,1;';
				$rs_external = $this->reg->get('db-ext')->query($query_external);
				if($rs_external && $rs_external->num_rows){
					$clientsArr[$idClient]['id'] = $idClient;
					$clientsArr[$idClient]['firstname']	= decodeString($rs_external->row['firstname']);
					$clientsArr[$idClient]['lastname'] = decodeString($rs_external->row['lastname']);
					$clientsArr[$idClient]['locale'] = $rs_external->row['locale'];
					$clientsArr[$idClient]['email']	= $rs_external->row['email'];
					$clientsArr[$idClient]['name']= $clientsArr[$idClient]['firstname'].' '.$clientsArr[$idClient]['lastname'];
					
				}else{
					//just to maintain json consistancy
					$clientsArr[$idClient]['id'] = $idClient;
					$clientsArr[$idClient]['firstname']	= '';
					$clientsArr[$idClient]['lastname'] = '';
					$clientsArr[$idClient]['locale'] = 'en_US';
					$clientsArr[$idClient]['email']	= '';
					$clientsArr[$idClient]['name'] = '';
					}
				unset($rs_external);
				}
			}
		unset($rs);
		$clientsArr = orderBy($clientsArr, 'name');
		//	
		return $clientsArr;
		}

	//-------------------------------------------------------------------------------------------------------------
	//Fn:get all users of the licence
	public function getLicenceUsers(){
		$user_ids = array();
		$licence_data = $this->reg->get('sess')->get('licence');
		$idLicence = $licence_data['idLicence'];
		$query = 'SELECT user.idUser FROM user, licence_admin WHERE user.idUser = licence_admin.idUser AND licence_admin.idLicence = "'.intVal($idLicence).'";';
		$rs = $this->reg->get('db')->query($query);
		if($rs && $rs->num_rows){
			foreach($rs->rows AS $idRow =>$row){
				$user_ids[] = $row['idUser'];
				}
			}
			unset($rs);
		$query = 'SELECT clinic_user.idUser FROM clinic_user, licence_clinic WHERE clinic_user.idClinic = licence_clinic.idClinic AND licence_clinic.idLicence = "'.intVal($idLicence).'";';
		$rs = $this->reg->get('db')->query($query);
		if($rs && $rs->num_rows){
			foreach ($rs->rows AS $idRow =>$row){
				$user_ids[] = $row['idUser'];
				}
			}
		unset($rs);
		$user_ids = array_unique($user_ids);
		return $user_ids; 
		}

	//-------------------------------------------------------------------------------------------------------------
	//Fn:returning the clients of all users within this clinic including the licence admin users
	public function getClinicUsers(){
		$user_ids = array();
		$licence_data = $this->reg->get('sess')->get('licence');
		$idLicence = $licence_data['idLicence'];
		$clinic_data = $this->reg->get('sess')->get('clinic');
		$idClinic = $clinic_data['idClinic'];
		//
		$query = 'SELECT user.idUser FROM user, licence_admin WHERE user.idUser = licence_admin.idUser AND licence_admin.idLicence = "'.intVal($idLicence).'";';
		$rs = $this->reg->get('db')->query($query);
		if($rs && $rs->num_rows){
			foreach($rs->rows AS $idRow =>$row){
				$user_ids[] = $row['idUser'];
				}
			}
		$query = 'SELECT user.idUser FROM user, clinic_user WHERE user.idUser = clinic_user.idUser AND clinic_user.idClinic = "'.intVal($idClinic).'";';
		unset($rs);
		$rs = $this->reg->get('db')->query($query); 
		if($rs && $rs->num_rows){
			foreach($rs->rows AS $idRow =>$row){
				$user_ids[] = $row['idUser'];
				}
			}
		unset($rs);
		$user_ids = array_unique($user_ids);		
		//
		return $user_ids;
		}
	
	//-------------------------------------------------------------------------------------------------------------
	public function checkUserName($username){
		$username = mb_strtolower($username, 'UTF-8');
		$search_result = false;
		$query = 'SELECT idUser FROM user WHERE LCASE(username) LIKE "'.$this->reg->get('db')->escape($username).'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs && $rs->num_rows){
			//username is used by other user 
			unset($rs);
			return $search_result = 1;
			}
		$query = 'SELECT idClient FROM licence_client WHERE LCASE(username) LIKE "'.$this->reg->get('db')->escape($username).'" LIMIT 0,1;';
		unset($rs);
		$rs = $this->reg->get('db')->query($query);
		if($rs && $rs->num_rows){
			//username is used by other client 
			unset($rs);
			return $search_result = 2;
			}
		unset($rs);
		return 'unique';
		}
	
	//-------------------------------------------------------------------------------------------------------------
	public function generateRandomStr($length = 8){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for($i=0; $i<$length; $i++){
			$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
		return $randomString;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function generatePassword($length = 10){
		$oCipher = new Cipher(PASS_CYPHER_SALT);
		$Original = $this->generateRandomStr($length);
		$Password = $oCipher->encrypt($Original);
		return array(
			'original' => $Original, 
			'encoded'=> $Password
			);
		}
	
	//-------------------------------------------------------------------------------------------------------------
	public function getClinicModule(){
		$clinic_data = $this->reg->get('sess')->get('clinic');
		$idClinic = $clinic_data['idClinic'];
		$returnArr = array();
		$query = 'SELECT clinic_module.idModule, module.title FROM module, clinic_module WHERE module.idModule = clinic_module.idModule AND clinic_module.idClinic = "'.intval($idClinic).'";';
		$rs = $this->reg->get("db")->query($query);
		if($rs && $rs->num_rows){
			foreach($rs->rows AS $k=>$v){
				if(!isset($returnArr[$v['idModule']])){
					$returnArr[$v['idModule']] = decodeString(translate($v['title']));
					}
				}
			}
		return $returnArr;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function getClinicOptionTitle(){
		//vars
		$arrClinicData = $this->reg->get('sess')->get('clinic');
		//trace
		if($this->bTrace){
			echo '$arrClinicData:'.print_r($arrClinicData).EOL.EOL;
			}
		//minor check
		if(isset($arrClinicData['idClinic'])){
			//query
			$query = 'SELECT clinic.protocol_menu AS "title" FROM clinic WHERE clinic.idClinic = "'. intVal($arrClinicData['idClinic']).'" LIMIT 0,1;';
			//trace
			if($this->bTrace){
				echo $query.EOL.EOL;
				}
			//resultset
			$rs = $this->reg->get('db')->query($query);
			//minor check
			if($rs && $rs->num_rows){
				if($rs->row['title'] != ''){
					return trim($rs->row['title']);
					}
				}
			}
		return translate('All');
		}

	//-------------------------------------------------------------------------------------------------------------
	public function getLicenceOptionTitle(){
		//vars
		$arrLicenceData = $this->reg->get('sess')->get('licence');
		//trace
		if($this->bTrace){
			echo '$arrLicenceData:'.print_r($arrLicenceData).EOL.EOL;
			}	
		//minor check
		if(isset($arrLicenceData['idLicence'])){
			//query
			$query = 'SELECT licence.protocol_menu AS "title" FROM licence WHERE licence.idLicence = "'.intVal($arrLicenceData['idLicence']).'" LIMIT 0,1;';
			//trace
			if($this->bTrace){
				echo $query.EOL.EOL;
				}
			//resultset
			$rs = $this->reg->get('db')->query($query);
			//minor check
			if($rs && $rs->num_rows){
				return trim($rs->row['title']);
				}
			}
		return translate('corporate');
		}


	//-------------------------------------------------------------------------------------------------------------
	public function getBrandOptionTitle() {
		
		return '';
		}

	
	}
	


//END