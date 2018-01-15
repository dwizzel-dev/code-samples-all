<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	USER
@todo:	1. lost password

@notes:
	/var/www/include/include_dev/parameters.php
	/var/www/include/include_dev/include.pdf.php

@modifs:
	17-08-2016 12:53;
		- rajout de la methode autoLogin (service: "user", methode: "auto-login")
			


*/


class User {
	
	private $reg;
	private $className = 'User';
	
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
	public function pingService($data){
		//the service pass through the service->session->check() 
		//so if session is not good service will bring the error to the client and disconnect him, this will only return OK
		//GOOD
		$arr = array(
			'error' => 0,
			'errormessage' => '',
			);
		return $arr;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function ping($data){
		return '1';
		}

	//-------------------------------------------------------------------------------------------------------------
	public function getModulesForSelectOptions($data){
		//list of the modules for the template saving options select box::popup-input-2 of the "save template" popup
		/*
		RECEIVER:
			data:{
			}
		SENDER:
			data:{
				1:"APPI Education",
				2:"APPI Pilates",
				3:"APPI Knees",
				4:"APPI Hip",
				5:"APPI Elbow"
				}
		*/
		$clinic_data = $this->reg->get('sess')->get('clinic');
		$moduleArr = array();
		$arr	   = array();
		$idClinic  = 0; 
		if(isset($clinic_data['idClinic'])){
			$idClinic =  $clinic_data['idClinic'];
			}
		if($idClinic != 0){
			$query = 'SELECT clinic_module.idModule, module.title FROM module, clinic_module WHERE module.idModule = clinic_module.idModule AND clinic_module.idClinic="'.intval($idClinic).'";';
			$rs = $this->reg->get('db')->query($query);
			if($rs && $rs->num_rows){
				foreach($rs->rows AS $idRow =>$row){
					$moduleArr[$row['idModule']] = decodeString(translate($row['title']));
					}
				}
				unset($rs);
			if(count($moduleArr) > 0){
				return $moduleArr;
			}else{
				$arr = array(
					'error' => 1,
					'errormessage' => $this->reg->get('err')->get(501),
					);
				return $arr;
				}
			} 
		$arr = array(
			'error' => 1,
			'errormessage' => $this->reg->get('err')->get(108),
			);
		return $arr;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function getPrintParameters($data){
		//get the printing parameters
		/*
		RECEIVER:
			data:{
				"programid":1780195,
				"clientid":1352541
				}
		SENDER:
			data:{
				format:{
					pdf:translate("Pdf"),
					},
				type:{
					picture:"Photos",
					drawing:"Dessins"
					},
				size:{
					15:"1 per page",
					18:"2 per page",
					24:"8 per page"
					},
				template:{
					15:"1_per_page.pdf",
					18:"2_per_page.pdf",
					24:"3_per_page.pdf"
					},
				selected:{
					format:"pdf",
					type:"picture",
					size:"18"
					}
				}
		*/	

		if(isset($data['programid']) && is_numeric($data['programid']) && $data['programid'] > 0 && isset($data['clientid']) && is_numeric($data['clientid']) && $data['clientid'] > 0){
			$idUser = $this->reg->get('sess')->get('idUser');
			$idPrint_size = 15;
			$format = 'pdf';
			$type   = 'picture';
			$print_type = array(
				'picture'=>decodeString(translate('picture')),
				'drawing'=>decodeString(translate('drawing'))
				);
			//fetching the user print size
			$query = 'SELECT idPrint_size FROM user WHERE idUser="'.intval($idUser).'";';
			$rs = $this->reg->get('db')->query($query);
			if($rs && $rs->num_rows){
				$idPrint_size = $rs->row['idPrint_size'];
				}
				unset($rs);
			//find the print size & type of the program
			$program_print_size = 0;
			$query = 'SELECT idPrint_size, img_type FROM program WHERE idProgram="'.intval($data['programid']).'" AND idClient="'.intval($data['clientid']).'";';
			$rs = $this->reg->get('db')->query($query);
			if($rs && $rs->num_rows){
				$row = $rs->row;
				$program_print_size = $row['idPrint_size'];
				$type = $row['img_type'];
				}
			unset($rs);
			if($program_print_size >= 15){
				// consider the print size
				$idPrint_size = $program_print_size;
			}
			if($idPrint_size < 15){
				//if not in the menue range set default 15
				$idPrint_size = 15;
			}
			//fetching out the print templates
			$idPrint_title    = array();
			$idPrint_template    = array();
			$query = 'SELECT idPrint_size, title, template FROM print_size WHERE idPrint_size > 14;';
			$rs = $this->reg->get('db')->query($query);
			if($rs && $rs->num_rows){
				foreach($rs->rows AS $idRow => $row){
					$idPrint_template[$row['idPrint_size']]    = $row['template'];
					$idPrint_title[$row['idPrint_size']]    = decodeString(translate($row['title']));
					}
				}
				unset($rs);
			//output
			$arr = array(
				'format' => array(
					'pdf' => decodeString(translate('Pdf'))
					),
				'type' => $print_type,
				'size' => $idPrint_title,
				'template' => $idPrint_template,
				'selected' => array(
					'format' => 'pdf',
					'type' => $type,
					'size' => $idPrint_size
					)
				);
			return $arr;
			}
		//	
		$arr = array(
			'error' => 1,
			'errormessage' => $this->reg->get('err')->get(108)
			);
		return $arr;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function savePrintParameters($data){
		//save the printing parameters
		/*
		RECEIVER:
			data:{
				"clientid":"1352541",
				"formatid":"pdf",
				"typeid":"drawing",
				"sizeid":"18",
				"clientlang":"fr_CA",
				"progid":"1780195"
				}
		SENDER:
			//we are going to build the url for the pdf here and send it or send an error instead
			data:"https://dev....@physiotec.ca/pdf_templates/5_per_page.pdf?&PHPSESSID=1cg82061q30mg31ikr0ertng717s3v6u&idProgram=0&picture_type=picture&program_locale=fr_CA"
			
		*/
		/*
		FORMAT OF PRINT LINK:
			https://dev....@physiotec.ca/pdf_templates/
			5_per_page.pdf?
			&PHPSESSID=1cg82061q30mg31ikr0ertng717s3v6u
			&idProgram=0
			&picture_type=picture
			&program_locale=fr_CA
		*/
		$arr = array();
		if(isset($data['sizeid']) && is_numeric($data['sizeid']) && $data['sizeid'] > 0 && isset($data['typeid']) && !empty(trim($data['typeid'])) && isset($data['progid']) && is_numeric($data['progid']) && $data['progid'] > 0 && isset($data['clientlang']) && !empty(trim($data['clientlang']))){
			$idUser = $this->reg->get('sess')->get('idUser');
			$query = 'UPDATE user SET idPrint_size="'.intval($data['sizeid']).'" WHERE idUser="'.intval($idUser).'";';
			$rs = $this->reg->get('db')->query($query);
			if(isset($rs->affected_rows)){
				$query = 'UPDATE program SET idPrint_size="'.intval($data['sizeid']).'", img_type="'.$this->reg->get('db')->escape($data['typeid']).'" WHERE idProgram="'.intval($data['progid']).'";';
				$this->reg->get('db')->query($query);
				$this->reg->get('sess')->put('print_size', $data['sizeid']);
				//pour l'envoi de donnee construire le lien directe
				$strLink = PATH_PRINT;
				//va chercher le size du print selon le id
				$query = 'SELECT template FROM print_size WHERE idPrint_size="'.intval($data['sizeid']).'" LIMIT 0,1;';	
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){
					$strLink .= $rs->row['template'].'?';
				}else{
					//on y va avec le default one
					$strLink .= '1_per_page.pdf'.'?';
					}
					unset($rs);
				//la session
				$strLink .= '&PHPSESSID='.$this->reg->get('sess')->getSessionID();
				//le program id
				$strLink .= '&idProgram='.$data['progid'];
				//picture type
				$strLink .= '&picture_type='.$data['typeid'];
				//la langue
				$strLink .= '&program_locale='.$data['clientlang'];
				//mobile key
				$strLink .= '&mobile=1';
				return $strLink;
			}else{
				$arr = array(
					'error' => 1,
					'errormessage' => $this->reg->get('err')->get(500)
					);
				return $arr;
				}
			}
		$arr = array(
			'error' => 1,
			'errormessage' => $this->reg->get('err')->get(108)
			);
		return $arr;
		}
	
	//-------------------------------------------------------------------------------------------------------------
    public function autoLogin($data){
		if(isset($data['username']) && isset($data['password']) && $data['username'] != '' && $data['password'] != ''){
			$arrRtn = $this->doLogin($data);
			//minor check
			if(isset($arrRtn['sessid']) && $arrRtn['sessid'] !== 0){
				$this->reg->get('resp')->redirect(PATH_WEB.'?&PHPSESSID='.$arrRtn['sessid']);
				}
			}
		$this->reg->get('resp')->redirect(PATH_WEB.'?&PHPSESSID=0');
		return;
		}

	//-------------------------------------------------------------------------------------------------------------
    public function doLogin($data){
		//base client login
		/*
		RECEIVER:
			data:{
				"username":"orenaldin",
				"password":"Leschiens666"
				}
		SENDER:
			data:{
				error:"0",
				sessid:"36k9k9ng3s1157fth1bi3sisu2",
				id:"999666000"
				lang:{
					en_US:"english",
					es_MX:"spanish"
					}
				}
		*/
		    
		$arr = array();
		if(isset($data['username']) && isset($data['password']) && !empty(trim($data['username'])) && !empty(trim($data['password']))){
			$username = urldecode(urldecode($data['username']));
			$password = $data['password'];
			$password_decoded = urldecode(urldecode($password));
			$clientIP = $_SERVER["REMOTE_ADDR"]?:($_SERVER["HTTP_X_FORWARDED_FOR"]?:$_SERVER["HTTP_CLIENT_IP"]);
			$now = time();
			$oCipher = new Cipher(PASS_CYPHER_SALT);
			//user
			$idUser = 0;
			$user_type = '';
			$full_name = '';
			$first_name = '';
			$Last_name = '';
			$current_clinic = 0;
			$access_all_clients = 0; // access own clients
			$search_by_module = 0;
			$print_summary = 0;
			$licence_admin = 0;
			$clinic_admin = 0;
			$user_locale = 'en_US';
			$user_active = 0;
			$user_deleted = 0;
			$userInstance = 1;
			//brand
			$idBrand = 0;
			$brand_title = '';
			$brand_email = '';
			//licence
			$idLicence = 0;
			$country_code = 'US'; // to be implemented in the future
			$licence_active = 0;
			$available_locale = array();
			//clinic
			$idClinic = 0;
			$clinic_title = '';
			$clinic_address = '';
			$clinic_email = '';
			$clinic_active = 0;
			//query
			$query = 'SELECT idUser, password, firstname, lastname, locale, deleted, active, email_client, restricted, last_visited_clinic, qty_try, default_module, order_exercise, idPrint_size, access_all_clients, search_by_module, print_summary FROM user WHERE username="'.$this->reg->get('db')->escape($username).'" LIMIT 0,1;';
			$rs = $this->reg->get('db')->query($query);
			if($rs && $rs->num_rows){
				$idUser = $rs->row['idUser'];
				$userPassword = $rs->row['password'];
				$first_name = decodeString($rs->row['firstname']);
				$last_name = decodeString($rs->row['lastname']);
				$user_locale = $rs->row['locale'];
				$user_active = $rs->row['active'];
				$user_deleted = $rs->row['deleted'];
				//$userRestricted = $rs->row['restricted'];
				$last_visited_clinic = $rs->row['last_visited_clinic'];
				$qty_try = $rs->row['qty_try'];
				$default_module = $rs->row['default_module'];
				$exercise_order = $rs->row['order_exercise'];
				$print_size = $rs->row['idPrint_size'];
				$access_all_clients = $rs->row['access_all_clients'];
				$search_by_module = $rs->row['search_by_module'];
				$print_summary = $rs->row['print_summary'];
				$email_client = $rs->row['email_client'];
				unset($rs);
				if($print_size < 15){
					$print_size = 15;
				}
				//normalize user variables 
				if($default_module == 0){
					$default_module = 29; // orthopedic
					}
				if($access_all_clients == 2){
					$access_all_clients = 0;
					}
				//verify user access
				if($user_active == 0 || $user_deleted == 1){
					//inactive user
					$arr = array(
						'sessid' => 0, 
						'error' => 1,	
						'errormessage' => $this->reg->get('err')->get(201)
						);
					return $arr;
					}
				if($qty_try >= MAX_LOG_ERROR){
					//too many attempts';
					$arr = array(
						'sessid' => 0, 
						'error' => 1,	
						'errormessage' => $this->reg->get('err')->get(202)
						);
					return $arr;
					}
				//verifing the password
				$userPassword = $oCipher->decrypt($userPassword);
				if($userPassword != $password){
					//Invalid credentials';
					$query = 'UPDATE user SET qty_try="'. intval($qty_try + 1).'" WHERE idUser="'.intval($idUser).'";';
					$this->reg->get('db')->query($query);
                    //error
					$arr = array(
						'sessid' => 0, 
						'error' => 1,	
						'errormessage' => $this->reg->get('err')->get(205)
						);
					return  $arr;
					}
                //tests pass - login success, now collect info and do procedures
				if($userPassword == $password || $userPassword == $password_decoded){
					$userType = 'user';
					$query = 'DELETE FROM jail WHERE ip = "'.$this->reg->get('db')->escape($clientIP).'";';
					$this->reg->get('db')->query($query);
					$query = 'UPDATE user SET qty_try=0 WHERE idUser="'.intval($idUser).'";';
					$this->reg->get('db')->query($query);
					}
				//Nous allons permettre a un user de se connecter plusieurs fois
				$lastUserInstance = 0;
				//to be checked db-sess
				$session_username = '%username|s:'.strlen($username).':"'.$username.'";%';
				$rs = $this->reg->get('sess')->getSessionDataFromUsername($session_username);
				if($rs && $rs->num_rows){
					foreach($rs->rows as $idRow => $row){
						$session_data = $row['session_data'];
						$array = explode(';', $session_data);
						for($i=0; $i<count($array); $i++){
							if(substr($array[$i], 0, 13) == 'userInstance|'){
								$lastUserInstance = substr($array[$i], 15);
								if($lastUserInstance >= $userInstance){
									$userInstance = $lastUserInstance + 1;
									}
								}
							}
						}
					}
					unset($rs);
				//Suppression de l'entrée du programTmp correspondant à l'instance de l'usager
				$query = 'DELETE FROM programTmp WHERE programTmp.idUser="'.intval($idUser).'" AND programTmp.user_instance = "'.intval($userInstance).'" AND programTmp.is_client = 0;';
				$this->reg->get('db')->query($query);
				//detecting the user clinic
				if(is_numeric($last_visited_clinic) && $last_visited_clinic > 0 ){
					$query = 'SELECT licence.idLicence, licence.active, brand.idBrand, brand.title, brand.email FROM licence, brand_licence, brand, licence_clinic, clinic WHERE licence_clinic.idLicence = licence.idLicence AND brand_licence.idLicence = licence.idLicence AND brand_licence.idBrand = brand.idBrand AND licence_clinic.idClinic = clinic.idClinic AND clinic.active = 1 AND licence_clinic.idClinic = "'.intval($last_visited_clinic).'";';
					$rs = $this->reg->get('db')->query($query);
					if($rs && $rs->num_rows){
						$idBrand = $rs->row['idBrand'];
						$idLicence = $rs->row['idLicence'];
						$idClinic = $last_visited_clinic;
						$brand_title = decodeString($rs->row['title']);
						$brand_email = $rs->row['email'];
						$licence_active = $rs->row['active'];
						$idLicence_admin = 0;
						$query = 'SELECT idLicence FROM licence_admin WHERE idUser = "'. intval($idUser).'";';
						$rs = $this->reg->get('db')->query($query);
						if($rs && $rs->num_rows){
							$row = $rs->row;
							$licence_admin = 1;
							}
							unset($rs);
						//
						$query = 'SELECT admin FROM clinic_user WHERE idClinic = "'.intval($idClinic).'" AND idUser="'.intval($idUser).'";';
						$rs = $this->reg->get('db')->query($query);
						if($rs && $rs->num_rows){
							$clinic_admin = 1;
							}
							unset($rs);
						}
					} 
				//
				if($idClinic == 0){
					//idclinic 0 
					$idLicence_admin = 0;
					$query = 'SELECT idLicence FROM licence_admin WHERE idUser = "'.intval($idUser).'";';
					$rs = $this->reg->get('db')->query($query);
					if($rs && $rs->num_rows){
						$idLicence_admin = $rs->row['idLicence'];
						}
						unset($rs);
					//
					$registered_recored = false;
					$query = 'SELECT licence_clinic.idLicence, clinic_user.idClinic, clinic_user.admin FROM licence_clinic, clinic_user, clinic WHERE licence_clinic.idClinic = clinic_user.idClinic AND clinic.idClinic = clinic_user.idClinic AND clinic.active = 1 AND clinic_user.idUser = "'.intval($idUser).'";';
					$rs = $this->reg->get('db')->query($query);
					if($rs && $rs->num_rows){
						foreach($rs->rows AS $idRow => $row){
							if($row['admin'] == 1){
								$registered_recored = false;
								}
							if(!$registered_recored){
								if($idLicence_admin != 0 && $idLicence_admin == $row['idLicence']){
									$idLicence = $row['idLicence'];
									$idClinic = $row['idClinic'];
									$Licence_admin = 1;
									$clinic_admin = $row['admin'];
									$registered_recored = true;
								}else if($idLicence_admin == 0){
									$idLicence     = $row['idLicence'];
									$idClinic      = $row['idClinic'];
									$Licence_admin = 0;
									$clinic_admin  = $row['admin'];
									$registered_recored = true;
									}
								}
							}
						unset($rs);	
						$query = 'SELECT brand.idBrand, brand.title, brand.email, licence.active FROM brand, brand_licence, licence WHERE brand.idbrand = brand_licence.idBrand AND brand_licence.idLicence = licence.idLicence AND brand_licence.idLicence="'.intval($idLicence).'";';
						$rs2 = $this->reg->get('db')->query($query);
						if($rs2 && $rs2->num_rows){
							$idBrand = $rs2->row['idBrand'];
							$brand_title = decodeString($rs2->row['title']);
							$brand_email = $rs2->row['email'];
							$licence_active = $rs2->row['active'];
							}
						unset($rs2);
						//update last visited clinic
						$query = 'UPDATE user SET last_visited_clinic="'.$this->reg->get('db')->escape($idClinic).'" WHERE idUser="'.intval($idUser).'";';
						$this->reg->get('db')->query($query);
						}
					}
				//
				if($idClinic == 0 || $idLicence == 0 || $licence_active == 0){
					$arr = array(
						'sessid' => 0, 
						'error'=> 1,	
						'errormessage' => $this->reg->get('err')->get(206)
						);
					return $arr;
					}
				//get available locals for the licence
				$query = 'SELECT locale FROM licence_locale WHERE idLicence="'.intval($idLicence).'";';
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){
					foreach($rs->rows AS $idRow => $row){
						$available_locale[] = $row['locale'];
						}
					}
				unset($rs);
				//getting the clinic info 
				$query = 'SELECT clinic.title, clinic.address, clinic.email FROM clinic WHERE clinic.idClinic = "'.intval($idClinic).'";';
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){
					$clinic_title = decodeString($rs->row['title']);
					$clinic_address = decodeString($rs->row['address']);
					$clinic_email = $rs->row['email'];
					}
				unset($rs);
				$full_name = $first_name.' '.$last_name;
				//adding variables for session
				$this->reg->get('sess')->put('session_type', 'mobile');
				$this->reg->get('sess')->put('idUser', $idUser);
				$this->reg->get('sess')->put('username', $username);
				$this->reg->get('sess')->put('full_name', $full_name);
				$this->reg->get('sess')->put('firstname', $first_name);
				$this->reg->get('sess')->put('lastname', $last_name);
				$this->reg->get('sess')->put('user_type', 'user');
				$this->reg->get('sess')->put('user_instance', $userInstance);
				$this->reg->get('sess')->put('locale', $user_locale);
				$this->reg->get('sess')->put('licence_admin', $licence_admin);
				$this->reg->get('sess')->put('clinic_admin', $clinic_admin);
				$this->reg->get('sess')->put('access_all_clients', $access_all_clients);
				$this->reg->get('sess')->put('search_by_module', $search_by_module);
				$this->reg->get('sess')->put('print_summary', $print_summary);
				$this->reg->get('sess')->put('default_module', $default_module);
				$this->reg->get('sess')->put('exercise_order', $exercise_order);
				$this->reg->get('sess')->put('print_size', $print_size);
				$this->reg->get('sess')->put('ip_client', $clientIP);
				$this->reg->get('sess')->put('email_client', $email_client);
				//brand
				$this->reg->get('sess')->put('brand', array(
					'idBrand' => $idBrand,
					'brand_title' => $brand_title,
					'brand_email' => $brand_email)
					);
				//license
				$this->reg->get('sess')->put('licence', array(
					'idLicence' => $idLicence,
					'available_locale' => $available_locale)
					);
				//clinic
				$this->reg->get('sess')->put('clinic', array(
					'idClinic' => $idClinic,
					'clinic_title' => $clinic_title,
					'clinic_address' => $clinic_address,
					'clinic_email' => $clinic_email)
					);
				
				//les langues
				$arrLangTranslation = $this->reg->get('glob')->get('system-locales');
				$arrLangSent = array();
				foreach($available_locale as $k=>$v){
					$arrLangSent[$v] = decodeString(translate($arrLangTranslation[$v]));
					}
				//no error
				$arr = array(
					'error' => 0,
					'sessid' => $this->reg->get('sess')->getSessionID(),	
					'id' => $idUser,
					'lang' => $arrLangSent,
					'moduleid' => intVal($this->reg->get('sess')->get('default_module'))
					);
				return $arr;
			}else{
				//Unknown username
				$query = 'SELECT qty_try, timestamp FROM jail WHERE ip="'.$this->reg->get('db')->escape($clientIP).'";';
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){
					$qtyTry = $rs->row['qty_try'];
					$timestamp = $rs->row['timestamp'];
					//MAX_LOG_ERROR to be defined
					if($qtyTry >= MAX_LOG_ERROR){
						//user account has been blocked
						$arr = array(
							'sessid' => 0, 
							'error' => 1,	
							'errormessage' => $this->reg->get('err')->get(203)
							);
						return $arr;
						}
					$query = 'UPDATE jail SET qty_try = qty_try + 1, timestamp = "'.$now.'" WHERE ip="'.$this->reg->get('db')->escape($clientIP).'";';
					$this->reg->get('db')->query($query);
					unset($rs);
				}else{
					$query = 'INSERT INTO jail (ip, qty_try, timestamp) VALUES("'.$this->reg->get('db')->escape($clientIP).'", 1, "'.$now.'");';
					$this->reg->get('db')->query($query);
					}
				$arr = array(
					'sessid' => 0, 
					'error' => 1,	
					'errormessage' => $this->reg->get('err')->get(204)
					);
				return $arr;
				}
			}
		$arr = array(
			'sessid' => 0, 
			'error' => 1,	
			'errormessage' => $this->reg->get('err')->get(108)
			);
        //
		return $arr;
        }

	//-------------------------------------------------------------------------------------------------------------
	public function doLogout($data){
		//logout routine for the user
		/*
		RECEIVER:
			data:{}
		SENDER:
			data:{
				error:"0",
				sessid:"0"
				}
		*/
		//destroy session
		$this->reg->get('sess')->destroy($this->reg->get('req')->get('PHPSESSID'));
		//sans erreur
		$arr = array(
			'sessid' => 0, 
			'error' => 0,	
			);
		//	
		return $arr;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function getBasicsInfos($data){
		//get the basicsw infos of the user so the user_id for now	
		/*
		RECEIVER:
			data:{}
		SENDER:
			data:{
				userid:"00000225",
				username:"dwizzel",
				lang:{
					fr_CA:"francais",
					en_US:"english"
					}
				}
		*/
		$licence_data = $this->reg->get('sess')->get('licence');
		$licence_locale = $licence_data['available_locale'];
		if(!is_array($licence_locale) || count($licence_locale) == 0 ){
			$arr = array(
				'sessid' => 0, 
				'error' => 1,	
				'errormessage' => $this->reg->get('err')->get(515)
				);
			}
		//les langues
		$arrLangTranslation = $this->reg->get('glob')->get('system-locales');
		$arrLangSent = array();
		foreach($licence_locale as $k=>$v){
			$arrLangSent[$v] = decodeString(translate($arrLangTranslation[$v]));
			}


		$arr = array(
			'userid' => $this->reg->get('sess')->get('idUser'),
			'username' => $this->reg->get('sess')->get('username'),
			'lang' => $arrLangSent,
			'moduleid' => intVal($this->reg->get('sess')->get('default_module'))
			);
		//
		return $arr;
		}

	}


//END