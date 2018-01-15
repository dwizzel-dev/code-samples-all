<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	OPTIONS

*/


class Options {
	
	private $reg;
	private $className = 'Options';	
	
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
	public function getPreferences($data){
		//return the preferences of the user for the preferences popup
		/*
		RECEIVER:
			data:{}
		SENDER:
			data:{
				lang:{
					fr_CA:"francais",
					en_US:"english"
					},
				langselected:"fr_CA",
				print:{
					1:"oui",
					0:"non"
					},
				printselected:"1",
				email:{
					OUTLOOK:"outlook",
					OTHER:"other"
					},
				emailselected:"OTHER",
				module:{
					1:"APPI Education",
					2:"APPI Pilates",
					3:"APPI Knees",
					4:"APPI Hip",
					5:"APPI Elbow"
					},
				moduleselected:"2",
				search:{
					1:"oui",
					0:"non"
					},
				searchselected:"0",
				clinic:{
					1953:"Clinic #1",
					5589:"Clinic #2"
					},
				clinicselected:"1953",	
				}
		*/
		$referenceArr = array();
		// system locales
		$system_locales = $this->reg->get('glob')->get('system-locales');
		//
		$licence_data = $this->reg->get('sess')->get('licence');
		$licence_locale = false;
		if(isset($licence_data['available_locale'])){
			$licence_locale = array_flip($licence_data['available_locale']);
			}
		//
		foreach($system_locales AS $key => $lang_title){
			if(isset($licence_locale[$key])){
				$referenceArr['lang'][$key] = decodeString(translate($lang_title));
				} 
			}
		//
		$referenceArr['langselected'] = $this->reg->get('sess')->get('locale');
		// print
		$referenceArr['print'] = array(
			'1' => decodeString(translate('yes')),
			'0' => decodeString(translate('no'))
			);
		//
		$referenceArr['printselected'] = $this->reg->get('sess')->get('print_summary');
		// email
		$referenceArr['email'] = array(
			'OUTLOOK' => decodeString(translate('outlook')),
			'OTHER'   => decodeString(translate('other'))
			);
		$referenceArr['emailselected'] = $this->reg->get('sess')->get('email_client');
		// getting moduleselected
		$clinicArr = $this->reg->get('sess')->get('clinic');
		$moduleArr = array();
		$idClinic  = 0;
		//$first_module_option = 0;
		if(isset($clinicArr['idClinic'])){
			$idClinic =  $clinicArr['idClinic'];
			}
		$referenceArr['module'] = array();
		//
		if($idClinic != 0){
			$moduleArr 	= $this->reg->get('utils')->getClinicModule();
			natcasesort($moduleArr);
			if(count($moduleArr) > 0){
				foreach($moduleArr as $k=>$v){
					array_push($referenceArr['module'], array(
						'id' => $k,
						'name' => translate($v),
						));
					}
				//$referenceArr['module'] = $moduleArr;
				//$first_module_option =  array_keys($moduleArr)[0];
			}else{
				$arr['error']        = 1;
				$arr['errormessage'] = $this->reg->get('err')->get(501);
				return $arr;
				}
			}
		$referenceArr['moduleselected'] = $this->reg->get('sess')->get('default_module');
		/*
		if(!isset($referenceArr['module'][$referenceArr['moduleselected']])){
			$referenceArr['moduleselected'] = $first_module_option;
			}
		*/
		//serach by module
		$referenceArr['search'] = array(
			'1' => decodeString(translate('yes')),
			'0' => decodeString(translate('no'))
			);
		$referenceArr['searchselected'] = $this->reg->get('sess')->get('search_by_module');
		
		//clinics
		$referenceArr['clinicselected'] = $idClinic;	
		$referenceArr['clinic'] = array();
		$idUser = $this->reg->get('sess')->get('idUser');
		$query = 'SELECT clinic.idClinic AS "id", clinic.title AS "title", clinic.address AS "address" FROM clinic_user, clinic WHERE clinic.idClinic = clinic_user.idClinic AND clinic.active = 1 AND clinic_user.idUser = "'.intval($idUser).'";';
		$rs = $this->reg->get('db')->query($query);
		if($rs && $rs->num_rows){
			foreach($rs->rows AS $k=>$v){
				$referenceArr['clinic'][$v['id']] = '['.$v['id'].'] '.$v['title'].' ('.$v['address'].')';
				}
			}
		return $referenceArr;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function savePreferences($data){
		//save the user preferences
		/*
		RECEIVER:
			data:{
				"lang":"en_US",
				"print":"1",
				"email":"OTHER",
				"module":"2",
				"search":"0",
				"clinic":"1953"
				}
		SENDER:
			data:"1"
		*/
		if(isset($data['lang']) && isset($data['print']) && isset($data['email']) && isset($data['module']) && isset($data['search'])){
			$idUser = $this->reg->get('sess')->get('idUser');
			$query = 'UPDATE user SET locale = "'.$this->reg->get('db')->escape($data['lang']).'", print_summary = "'.intval($data['print']).'", email_client = "'.$this->reg->get('db')->escape($data['email']).'", default_module = "'.intval($data['module']).'", search_by_module = "'.intval($data['search']).'" WHERE idUser = "'.intval($idUser).'";';
			$rs = $this->reg->get('db')->query($query);
			if(isset($rs->affected_rows)){
				$this->reg->get('sess')->put('locale', $data['lang']);
				$this->reg->get('sess')->put('print_summary', $data['print']);
				$this->reg->get('sess')->put('email_client', $data['email']);
				$this->reg->get('sess')->put('default_module', $data['module']);
				$this->reg->get('sess')->put('search_by_module', $data['search']);
				unset($rs);
				//getting the clinic info 
				$query = 'SELECT clinic.title, clinic.address, clinic.email FROM clinic WHERE clinic.idClinic = "'.intval($data['clinic']).'" LIMIT 0,1;';
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){
					$clinic_title = decodeString($rs->row['title']);
					$clinic_address = decodeString($rs->row['address']);
					$clinic_email = $rs->row['email'];
					//clinic session
					$this->reg->get('sess')->put('clinic', array(
						'idClinic' => intval($data['clinic']),
						'clinic_title' => $clinic_title,
						'clinic_address' => $clinic_address,
						'clinic_email' => $clinic_email)
						);
					}
				$query = 'UPDATE user SET last_visited_clinic = "'.intval($data['clinic']).'" WHERE idUser = "'.intval($idUser).'";';	
				$this->reg->get('db')->query($query);
				return 1;
			}else{
				$arr['error']        = 1;
				$arr['errormessage'] = $this->reg->get('err')->get(502);
				unset($rs);
				return $arr;
				}
			}
		//
		$arr['error']        = 1;
		$arr['errormessage'] = $this->reg->get('err')->get(108);
		return $arr;
		}
		
	//-------------------------------------------------------------------------------------------------------------
	public function getAccountOptions($data){
		//return the user account options for the account options popup
		/*
		RECEIVER:
			data:{
				}
		SENDER:
			data:{
				username:"orenaldin",
				emailprimary:"",
				emailsecondary:""
				}
		*/
		$AccountArr = array();
		$AccountArr['emailprimary']   = '';
		$AccountArr['emailsecondary'] = '';
		
		$query = 'SELECT email1, email2 ' .
			 'FROM user ' .
			 'WHERE idUser="'.intval($this->reg->get('sess')->get('idUser')).'";';
		$rs = $this->reg->get('db')->query($query);
		if ($rs && $rs->num_rows) {
			$row = $rs->row;
			$AccountArr['emailprimary']   = $row['email1'];
			$AccountArr['emailsecondary'] = $row['email2'];
		}
		$AccountArr['username'] = $this->reg->get('sess')->get('username');
		return $AccountArr;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function saveAccountOptions($data){
		//save the user account options
		/*
		RECEIVER:
			data:{
				"username":"orenaldin",
				"oldpsw":"1111",
				"newpsw":"2222",
				"confirmpsw":"3333",
				"emailprimary":"",
				"emailsecondary":""
				}
		SENDER:
			data:{
				ok:"1",
				msg:""
				}
		*/

		//OK
		/*$arr = array(
			'ok' => 1,	
			'msg' => '',
			);
		*/	
		//BAD
		/*
		$arr = array(
			'ok' => 0,	
			'msg' => $this->reg->get('err')->get(302),
			);
		*/
                $arr = array();
                if(isset($data['username']) && isset($data['oldpsw']) && isset($data['newpsw']) && isset($data['confirmpsw']) && isset($data['emailprimary'])&& isset($data['emailsecondary'])){
                        $new_username    = trim($data['username']);
                        $email1          = trim($data['emailprimary']);
                        $email2          = trim($data['emailsecondary']);
			$old_password	 = trim($data['oldpsw']);
                        $new_password    = trim($data['newpsw']);
                        $new_Password_confirm = trim($data['confirmpsw']);
                        $username_changed   = false;
                        $email_changed      = false;
                        $password_changed   = false;
                        
                        $orig_username = '';
                        $orig_email1   = '';
                        $orig_email2   = '';
			$orig_password = '';
                        $idUser        = $this->reg->get('sess')->get('idUser');
                        
                        $query = 'SELECT username, email1, email2, password ' .
                                 'FROM user ' .
                                 'WHERE idUser="'.intval($idUser).'";';
                        $rs = $this->reg->get('db')->query($query);
                        if ($rs && $rs->num_rows) {
                                $row = $rs->row;
                                $orig_username = $row['username'];
                                $orig_email1   = $row['email1'];
                                $orig_email2   = $row['email2'];
				$orig_password = $row['password'];
			}
			unset($rs);
                        #
                        # check username
                        #
                        if($new_username != $orig_username){
                                $username_changed =  true;
                                if(strlen($new_username) < 8 ){
                                        $arr['ok']  = 0;	
                                        $arr['msg'] = $this->reg->get('err')->get(503);
                                        return $arr;
                                }        
                                if(!preg_match('/^[a-z0-9\x{c0}-\x{ff}ÆŒÇËĆČĐŠŽÀÈÉÍÏÒÓÚÜÁĎĚŇŘŤŮÝÆØÅĈĜĤĴŜŬÄÖÕÐÂÊÎÔŒÙÛŸßÃĨŨĸŐŰÞÌĀĒĢĪĶĻŅŖŪĄĘŁŃŚŹŻĂŞŢŊŦĹĽŔÑĞİ·ªº¡¿\-.@_ ]+$/ui',$new_username)) {
                                        $arr['ok']  = 0;	
                                        $arr['msg'] = $this->reg->get('err')->get(504);
                                        return $arr;
                                }
                                #
                                # check if username is used by other user or client
                                #
				$tmp_new_username = mb_strtolower($new_username, 'UTF-8');
				$username_result = $this->reg->get('utils')->checkUserName($tmp_new_username);
				if($username_result == 1){
					$arr['ok']  = 0;	
                                        $arr['msg'] = $this->reg->get('err')->get(505);
                                        return $arr;
				} else if($username_result == 2){
					$arr['ok']  = 0;	
                                        $arr['msg'] = $this->reg->get('err')->get(506);
                                        return $arr;
				}
                        }
                        #
                        # check emails 
                        #
                        if(!(($email1 == $orig_email1 && $email2 == $orig_email2) || ($email1 == $orig_email2 && $email2 == $orig_email1))){
                                $email_changed = true;
                                $email1_empty  = empty($email1);
                                $email2_empty  = empty($email2);
                                if($email1_empty && $email2_empty){
                                        $arr['ok']  = 0;	
                                        $arr['msg'] = $this->reg->get('err')->get(507);
                                        return $arr;
                                }
                                if(!$email1_empty) {
                                        if (!filter_var($email1, FILTER_VALIDATE_EMAIL)) {
                                                $arr['ok']  = 0;	
                                                $arr['msg'] = $this->reg->get('err')->get(508);
                                                return $arr;
                                        }
                                }
                                if(!$email2_empty)  {
                                        if (!filter_var($email2, FILTER_VALIDATE_EMAIL))  {
                                                $arr['ok']  = 0;	
                                                $arr['msg'] = $this->reg->get('err')->get(509);
                                                return $arr;
                                        }
                                }
                                if ($email1_empty) {
                                        $temp   = $email1;
                                        $email1 = $email2;
                                        $email2 = $temp;
                                }
                        }
                        #
                        # check password
                        #
                        if(!empty($new_password) || !empty($new_Password_confirm)){
                                $password_changed   = true;
                                if (strlen($new_password) <= 8) {
                                        $arr['ok']  = 0;	
                                        $arr['msg'] = $this->reg->get('err')->get(511);
                                        return $arr;
                                }
                                if(!preg_match('/^[a-z0-9\x{c0}-\x{ff}ÆŒÇËĆČĐŠŽÀÈÉÍÏÒÓÚÜÁĎĚŇŘŤŮÝÆØÅĈĜĤĴŜŬÄÖÕÐÂÊÎÔŒÙÛŸßÃĨŨĸŐŰÞÌĀĒĢĪĶĻŅŖŪĄĘŁŃŚŹŻĂŞŢŊŦĹĽŔÑĞİ·ªº¡¿!@#$%*\-.@_]+$/ui', $new_password)) {
					$arr['ok']  = 0;	
                                        $arr['msg'] = $this->reg->get('err')->get(512);
                                        return $arr;
				}
                                if($new_password != $new_Password_confirm){
                                        $arr['ok']  = 0;
                                        $arr['msg'] = $this->reg->get('err')->get(510);
                                        return $arr;
                                }
                                $oCipher  = new Cipher(PASS_CYPHER_SALT);
				// validate current password
				if($oCipher->decrypt($orig_password) != $old_password){
					$arr['ok']  = 0;
                                        $arr['msg'] = $this->reg->get('err')->get(514);
                                        return $arr;
				}
                                $new_password = $oCipher->encrypt($new_password);
                        }
                        $query_parts = array();
                        
                        if($username_changed){
                                $query_parts[] = 'username = "' . $this->reg->get('db')->escape($new_username) . '"';
				$this->reg->get('sess')->put('username',$new_username);
                        }
                        if($email_changed){
                                $query_parts[] = 'email1 = "' . $this->reg->get('db')->escape($email1) . '"';
                                $query_parts[] = 'email2 = "' . $this->reg->get('db')->escape($email2) . '"';
                        }
                        if($password_changed){
                                $query_parts[] = 'password = "' . $this->reg->get('db')->escape($new_password) . '"';
                        }
                        
                        if(count($query_parts) > 0){
                                $query = 'UPDATE user SET ';
                                $query.= implode(', ', $query_parts) . ' ';
                                $query.= 'WHERE idUser = "' . intval($idUser) . '";';
                                $rs = $this->reg->get('db')->query($query);
                                if(isset($rs->affected_rows)){
                                        $arr['ok']  = 1;
                                        $arr['msg'] = '';
                                        return $arr;
                                } else {
                                        $arr['error']        = 1;
                                        $arr['errormessage'] = $this->reg->get('err')->get(513);
                                        return $arr;
                                }
                        } else {
				$arr['ok']  = 1;
                                $arr['msg'] = '';
                                return $arr;
			}
                }
		$arr['error']        = 1;
                $arr['errormessage'] = $this->reg->get('err')->get(108);
                return $arr;
	}

	}


//END