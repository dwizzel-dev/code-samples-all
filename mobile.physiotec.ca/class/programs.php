<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	PROGRAMS
@necessary to use htmlentities for the new changed data - because of json_encode will fail if there is wierd characters.
*/


class Programs {
	
	private $reg;
	private $className = 'Programs';		

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
	public function saveProgramModification($data){
		//save program modifications
		/*
		RECEIVER:
			data:{
				"clientid": 1350301,
				"order": "117697",
				"name": "test",
				"notes": "test test test test 123",
				"id": 1780410,
				"exercices": [{
					"id": "117697",
					"settings": {
						"sets": "1",
						"repetition": "8",
						"hold": "10",
						"weight": "",
						"tempo": "",
						"rest": "",
						"frequency": "2x/day",
						"duration": ""
					},
					"settings_lang": {
						"fr_CA": {
							"sets": "1",
							"repetition": "8",
							"hold": "10",
							"weight": "",
							"tempo": "",
							"rest": "",
							"frequency": "2x/day",
							"duration": ""
						},
						"en_US": {
							"sets": "1",
							"repetition": "8",
							"hold": "10",
							"weight": "",
							"tempo": "",
							"rest": "",
							"frequency": "2x/day",
							"duration": ""
						}
					},
					"flip": 0,
					"mirror": 0,
					"programdata": {
						"locale": {
							"fr_CA": {
								"short_title": "Key Element 5: Head and neck",
								"title": "Key Element 5: Head and neck placement",
								"description": "If you have neck pain",
								"level": ""
							},
							"en_US": {
								"short_title": "Key Element 5: Head and neck",
								"title": "Key Element 5: Head and neck placement",
								"description": "If you have neck pain or headaches",
								"level": ""
							}
						}
					},
					"code": "GEN13029"
				}]
			} 
		SENDER:
			data:"1"
		*//*
		return 1;
		*/
		$arr = array();
		if(!isset($data['clientid']) || !is_numeric($data['clientid']) || !($data['clientid'] > 0) ){
			$arr['error']        = 1;
			$arr['errormessage'] = $this->reg->get('err')->get(762);
			return $arr;
		}
		if(count($data) > 0 && isset($data['clientid']) && is_numeric($data['clientid']) && $data['clientid'] > 0 && isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0 && isset($data['name']) && !empty(trim($data['name']))){
			// collecting necessary info
			$idUser		 = $this->reg->get('sess')->get('idUser');
			//$user_locale	 = $this->reg->get('sess')->get('locale');
			$licence_data	 = $this->reg->get('sess')->get('licence');
			$available_locale= $licence_data['available_locale'];
			$clinic_data	 = $this->reg->get('sess')->get('clinic');
			$idClinic 	 = $clinic_data['idClinic'];
			$program_data	 = '{}';
			$last_update	 = buildDateTime($this->reg->get('req')->get('time'));
			$idProgram	= $data['id'];
			$ex_order	= isset($data['order']) ? $data['order'] : '';
			$exercises	= isset($data['exercices']) ? $data['exercices']: array();
			$title		= encodeString($data['name']);
			$note		= isset($data['notes']) ? encodeString($data['notes']) : '';
			$titleArr	= array();
			$noteArr	= array();
			$exercisesArr	= array();
			$exercise_ids	= array();
			$arr		= array();
			$videos		= array();
			$idClient	= $data['clientid'];
			$query_external = 'SELECT locale FROM client WHERE idClient="'.intval($idClient).'";';
			$rs_external = $this->reg->get('db-ext')->query($query_external);
			if ($rs_external && $rs_external->num_rows) {
				$client_locale = $rs_external->row['locale'];
			}
			unset($rs_external);
			$client_locale	= !empty($client_locale) ? $client_locale: 'en_US';
			
			// build data
			foreach($available_locale AS $locale_licence){
				$titleArr[$locale_licence] = $title;
				$noteArr[$locale_licence]  = $note;
			}
			$title = json_endecodeArr($titleArr);
			$note  = json_endecodeArr($noteArr);
			
			if(count($exercises) > 0){
				foreach($exercises AS $ex_data){
					$id_ex		= $ex_data['id'];
					$code_ex	= $ex_data['code'];
					$settings_ex	= $ex_data['settings'];
					if(isset($settings_ex['sets'])){
						$settings_ex['sets'] = encodeString($settings_ex['sets']);
					}
					if(isset($settings_ex['repetition'])){
						$settings_ex['repetition'] = encodeString($settings_ex['repetition']);
					}
					if(isset($settings_ex['hold'])){
						$settings_ex['hold'] = encodeString($settings_ex['hold']);
					}
					if(isset($settings_ex['weight'])){
						$settings_ex['weight'] = encodeString($settings_ex['weight']);
					}
					if(isset($settings_ex['tempo'])){
						$settings_ex['tempo'] = encodeString($settings_ex['tempo']);
					}
					if(isset($settings_ex['rest'])){
						$settings_ex['rest'] = encodeString($settings_ex['rest']);
					}
					if(isset($settings_ex['frequency'])){
						$settings_ex['frequency'] = encodeString($settings_ex['frequency']);
					}
					if(isset($settings_ex['duration'])){
						$settings_ex['duration'] = encodeString($settings_ex['duration']);
					}
					$flip_ex	= isset($ex_data['flip']) ? $ex_data['flip'] : 0;
					$mirror_ex	= isset($ex_data['mirror']) ? $ex_data['mirror'] : 0;
					$locale_ex	= $ex_data['programdata']['locale'];
					//handle data
					$exercise_ids[]	= $id_ex;
					$exercisesArr[$id_ex]['codeExercise']	= $code_ex;
					$exercisesArr[$id_ex]['picture']	= array();
					$exercisesArr[$id_ex]['drawing']	= array();
					$exercisesArr[$id_ex]['video']		= array();
					$exercisesArr[$id_ex]['mirror']		= $mirror_ex;
					$exercisesArr[$id_ex]['flip']		= $flip_ex;
					
					$default_locale_array = isset($locale_ex[$client_locale]) ? $locale_ex[$client_locale] : (isset($locale_ex['en_US']) ? $locale_ex['en_US'] : $locale_ex[array_keys($locale_ex)[0]]);
					
					foreach($available_locale AS $locale_licence){
						if(isset($locale_ex[$locale_licence])){
							if(!(isset($locale_ex[$locale_licence]['short_title']) && !empty($locale_ex[$locale_licence]['short_title']) && isset($locale_ex[$locale_licence]['description']) && !empty($locale_ex[$locale_licence]['description']))){
								$locale_ex[$locale_licence] = $default_locale_array;
							}
						} else {
							$locale_ex[$locale_licence] = $default_locale_array;
						}
						if(isset($locale_ex[$locale_licence]['short_title'])){
							$locale_ex[$locale_licence]['short_title'] = encodeString($locale_ex[$locale_licence]['short_title']);
						}
						if(isset($locale_ex[$locale_licence]['description'])){
							$locale_ex[$locale_licence]['description'] = encodeString($locale_ex[$locale_licence]['description']);
						}
						$locale_ex[$locale_licence] = array_merge($locale_ex[$locale_licence], $settings_ex);
					}
					$exercisesArr[$id_ex]['locale'] = $locale_ex;
				}
				#
				# $query for all exercise ids
				#
				$query = 'SELECT idExercise, data FROM exercise WHERE idExercise IN ('.implode(',', $exercise_ids).');';
				$rs = $this->reg->get('db')->query($query);
				if ($rs && $rs->num_rows) {
					foreach ($rs->rows AS $idRow => $row){
						$idExercise	= $row['idExercise'];
						if(isset($exercisesArr[$idExercise])){
							$exercises_data = json_decodeStr($row['data']);
							foreach($available_locale AS $locale_licence){
								$exercisesArr[$idExercise]['locale'][$locale_licence]['title'] = isset($exercises_data['locale'][$locale_licence]['title']) ? $exercises_data['locale'][$locale_licence]['title']:'';
							}
							if($exercisesArr[$idExercise]['flip']){
								$exercisesArr[$idExercise]['picture'] = array_reverse($exercises_data['picture']);
								$exercisesArr[$idExercise]['drawing'] = array_reverse($exercises_data['drawing']);
							} else {
								$exercisesArr[$idExercise]['picture'] = $exercises_data['picture'];
								$exercisesArr[$idExercise]['drawing'] = $exercises_data['drawing'];
							}
							//UNSET FLIP
							unset($exercisesArr[$idExercise]['flip']);
							$exercisesArr[$idExercise]['video'] = '';
						}
						// reverse exercise array content
						$exercisesArr[$idExercise] = array_reverse($exercisesArr[$idExercise]);
					}
					unset($rs);
				}
				#
				# reorder exercises
				#
				$exercises_order = explode(',', $ex_order);
				$ordered_exercises = array();
				if(count($exercises_order) > 0 ){
					foreach($exercises_order As $idExercise){
						if(isset($exercisesArr[$idExercise])){
							$ordered_exercises[$idExercise] = $exercisesArr[$idExercise];
						}
					}
				}
				// for preventing array_ddf and array_flip of failing 
				if(!is_array($exercises_order)){
					$ordered_exercises = array();
				}
				$ordered_exercises += array_diff_key($ordered_exercises, array_flip($exercises_order));
				
				$program_data = json_endecodeArr($ordered_exercises);
			}
			//update program
			$query = 'UPDATE program SET '.
				 'idUser="'.intval($idUser).'", '.
				 'idClinic="'.intval($idClinic).'", '.
				 'idClient="'.intval($idClient).'", '.
				 'title="'.$this->reg->get('db')->escape($title).'", '.
				 'note="'.$this->reg->get('db')->escape($note).'", '.
				 'timestamp="'.$this->reg->get('db')->escape($last_update).'", '.
				 'data="'.$this->reg->get('db')->escape($program_data).'" '.
				 'WHERE idProgram="'.intval($idProgram).'";';
			$rs = $this->reg->get('db')->query($query);
			if(isset($rs->affected_rows)){
				return 1;
			}
			$arr['error']        = 1;
			$arr['errormessage'] = $this->reg->get('err')->get(763);
			return $arr;
		}
		$arr['error']        = 1;
                $arr['errormessage'] = $this->reg->get('err')->get(108);
                return $arr;
	}

	//-------------------------------------------------------------------------------------------------------------
	public function createNewProgram($data){
		//create a new program
		/*
		RECEIVER:
			data:{
				"clientid":1352541,
				"programname":"TEMPLATE 1003",
				"programid":-1
				}
		SENDER:
			data:"1455044713"
		*/
		/*
		RECEIVER:
			data:{
				"clientid":1352541,
				"programname":"TEMPLATE 1003",
				"programid":0000666
				}
		SENDER:
			data:"0000666"
		*/
		$arr = array();
		if(isset($data['clientid']) && isset($data['programname'])){
			$result 	= $this->isProgramNameExist($data, false);
			switch($result){
				case 'fail_parameter':
				case 'fail_finding_locale':
				case 'fail_to_access_client':
					$arr['error']        = 1;
					$arr['errormessage'] = $this->reg->get('err')->get(750);
					return $arr;
					break;
				default:
					break;
			}
			if(!is_numeric($data['clientid']) || !($data['clientid'] > 0)){
				$arr['error']        = 1;
				$arr['errormessage'] = $this->reg->get('err')->get(752);
				return $arr;
			}
			if(empty(trim($data['programname']))){
				$arr['error']        = 1;
				$arr['errormessage'] = $this->reg->get('err')->get(760);
				return $arr;
			}
			if(!empty($result) && $result['exist'] == 0){
				#
				# for when we create a program and we overright an existing one
				#
				if(isset($data['programid']) && is_numeric($data['programid']) && $data['programid'] > 0 ){
					$data['programidfrom'] = -1;
					$data['programnotes'] = '';
					$result = $this->modifyProgramName($data);
					if(is_array($result) && isset($result['programid'])){
						return $result['programid'];
					}
					return $result;
				}
				$idProgram	 = 0;
				$idUser		 = $this->reg->get('sess')->get('idUser');
				$brand_data	 = $this->reg->get('sess')->get('brand');
				$licence_data	 = $this->reg->get('sess')->get('licence');
				$idLicence	 = $licence_data['idLicence'];
				$available_locale= $licence_data['available_locale'];
				$clinic_data	 = $this->reg->get('sess')->get('clinic');
				$idClinic	 = $clinic_data['idClinic'];
				$idPrint_size	 = $this->reg->get('sess')->get('print_size');
				$idClient	 = $data['clientid'];
				$image_type	 = 'picture';
				$title		 = '{}';
				$titleArr	 = array();
				$end_date	 = '0000-00-00';
				$modification_date	 = buildDateTime($this->reg->get('req')->get('time'));
				$start_date 		= buildDate($this->reg->get('req')->get('time'));
				$new_title 	= encodeString($data["programname"]);
				
				//build the title
				foreach ($available_locale As $locale){
					$titleArr[$locale] = $new_title;
					}
				$title = json_endecodeArr($titleArr);
				// insert new program base data
				$query = 'INSERT INTO program ( idUser, idClinic, idClient, title, idPrint_size, creation_date, img_type, start_date, end_date, timestamp, note, data) VALUES ('.
					 '"'.intval($idUser).'", '.
					 '"'.intval($idClinic).'", '.
					 '"'.intval($idClient).'", '.
					 '"'.$this->reg->get('db')->escape($title).'", '.
					 '"'.intval($idPrint_size).'", '.
					 '"'.$this->reg->get('db')->escape($modification_date).'", '.
					 '"'.$this->reg->get('db')->escape($image_type).'", '.
					 '"'.$this->reg->get('db')->escape($start_date).'", '.
					 '"'.$this->reg->get('db')->escape($end_date).'", '.
					 '"'.$this->reg->get('db')->escape($modification_date).'", '.
					 '"{}", '.
					 '"{}");';
				$rs = $this->reg->get('db')->query($query);
				// idProgram
				$idProgram = $rs->insert_id;
				if(!is_numeric($idProgram) || $idProgram == 0 ){
					$arr['error']        = 1;
					$arr['errormessage'] = $this->reg->get('err')->get(754);
					return $arr;
				}
				return $idProgram;
			} else {
				// error program title already exist
				$arr['error']        = 1;
				$arr['errormessage'] = $this->reg->get('err')->get(751);
				return $arr;
			}
		}
		$arr['error']        = 1;
		$arr['errormessage'] = $this->reg->get('err')->get(108);
		return $arr;	
	}

	//-------------------------------------------------------------------------------------------------------------
	public function modifyProgramName($data){
		/*
		RECEIVER:
			data:{
				//over write an existing program 
				"clientid":888888,
				"programidfrom":1455044907,
				"programid":1455044907,
				"programname":"FFFF TMLT 02",
				"programnotes":""
				}
		SENDER:
			data:{
				programid:"1455044907"
				}
		//------------------
		
		RECEIVER:
			data:{
				"clientid":888888,
				"programidfrom":-1,
				"programid":-1,
				"programname":"FFF",
				"programnotes":""
				}
		SENDER:
			data:{
				programid:"1455045448"
				}	
		
		*/
		/*
		$arr = array(	
			'programid' => $data['programid'],	
			);
		if($arr['programid'] == '-1'){
			$arr['programid'] = time();
			}
		return $arr;*/
		$arr = array();
		if(isset($data['programid']) && is_numeric($data['programid']) && isset($data['programidfrom']) && is_numeric($data['programidfrom']) && isset($data['clientid']) && is_numeric($data['clientid']) && isset($data['programname'])){
			if(empty(trim($data['programname']))){
				$arr['error']        = 1;
				$arr['errormessage'] = $this->reg->get('err')->get(764);
				return $arr;
			}
			$idUser			= $this->reg->get('sess')->get('idUser');
			$idClient		= $data['clientid'];
			$idProgram		= $data['programid'];
			$idProgram_from		= $data['programidfrom'];
			$title			= encodeString($data['programname']);
			$orig_title		= $data['programname'];
			$note			= isset($data['programnotes']) ? encodeString($data['programnotes']) : '';
			$data			= '{}';
			$titleArr		= array();
			$notesArr		= array();
			$licence_data		= $this->reg->get('sess')->get('licence');
			$available_locale	= $licence_data['available_locale'];
			$clinic_data		= $this->reg->get('sess')->get('clinic');
			$idClinic		= $clinic_data['idClinic'];
			$image_type		= 'picture';
			$idPrint_size		= $this->reg->get('sess')->get('print_size');
			$end_date		= '0000-00-00';
			$modification_date	= buildDateTime($this->reg->get('req')->get('time'));
			$start_date	 = buildDate($this->reg->get('req')->get('time'));
			// build data title and note
			foreach($available_locale AS $locale_licence){
				$titleArr[$locale_licence] = $title;
				$noteArr[$locale_licence]  = $note;
			}
			$title = json_endecodeArr($titleArr);
			$note  = json_endecodeArr($noteArr);
			// modifying the current program
			if($idProgram == $idProgram_from && $idProgram > 0){
				$testArr = array('clientid'=>$idClient, 'programid'=>$idProgram, 'programname'=>$orig_title);
				$result = $this->isProgramNameExist($testArr, false);
				if( isset($result['exist']) && $result['exist'] == 1){
					$arr['error']        = 1;
					$arr['errormessage'] = $this->reg->get('err')->get(765);
					return $arr;
				} else if($result == 'fail_finding_locale' || $result == 'fail_parameter'){
					$arr['error']        = 1;
					$arr['errormessage'] = $this->reg->get('err')->get(767);
					return $arr;
				}
				
				$query = 'UPDATE program SET '.
					 'idUser="'.intval($idUser).'", '.
					 'idClinic="'.intval($idClinic).'", '.
					 'idClient="'.intval($idClient).'", '.
					 'title="'.$this->reg->get('db')->escape($title).'", '.
					 'note="'.$this->reg->get('db')->escape($note).'", '.
					 'timestamp="'.$this->reg->get('db')->escape($modification_date).'" '.
					 'WHERE idProgram="'.intval($idProgram).'";';
				$rs = $this->reg->get('db')->query($query);
				if(isset($rs->affected_rows)){
					return array('programid'=>$idProgram);
				}
				$arr['error']        = 1;
				$arr['errormessage'] = $this->reg->get('err')->get(768);
				return $arr;
			} else if($idProgram != $idProgram_from && $idProgram_from != -1 && $idProgram > 0){
				// no title duplication check - since the check has been done from before
				// note: we select the data from the source program and we save it to the source without any maniplulation
				$select_query = 'SELECT data FROM program WHERE idProgram="'.intval($idProgram_from).'" LIMIT 0,1;';
				$rs = $this->reg->get('db')->query($select_query);
				if ($rs && $rs->num_rows) {
					$data = $rs->row['data'];
				}
				$query = 'UPDATE program SET '.
					 'idUser="'.intval($idUser).'", '.
					 'idClinic="'.intval($idClinic).'", '.
					 'idClient="'.intval($idClient).'", '.
					 'title="'.$this->reg->get('db')->escape($title).'", '.
					 'note="'.$this->reg->get('db')->escape($note).'", '.
					 'creation_date="'.$this->reg->get('db')->escape($modification_date).'", '.
					 'idPrint_size="'.intval($idPrint_size).'", '.
					 'img_type="'.$this->reg->get('db')->escape($image_type).'", '.
					 'timestamp="'.$this->reg->get('db')->escape($modification_date).'", '.
					 'data="'.$this->reg->get('db')->escape($data).'", '.
					 'start_date="'.$this->reg->get('db')->escape($start_date).'", '.
					 'end_date="'.$this->reg->get('db')->escape($end_date).'" '.
					 'WHERE idProgram="'.intval($idProgram).'";';
				$rs = $this->reg->get('db')->query($query);
				if(isset($rs->affected_rows)){
					$delete_query = 'DELETE FROM program WHERE idProgram="'.intval($idProgram_from).'";';
					$rs = $this->reg->get('db')->query($delete_query);
					return array('programid'=>$idProgram);
				}
				$arr['error']        = 1;
				$arr['errormessage'] = $this->reg->get('err')->get(769);
				return $arr;
			} else if($idProgram != $idProgram_from && $idProgram_from == -1 && $idProgram > 0){
				// no title duplication check - since the check has been done from before
				$query = 'UPDATE program SET '.
					 'idUser="'.intval($idUser).'", '.
					 'idClinic="'.intval($idClinic).'", '.
					 'idClient="'.intval($idClient).'", '.
					 'title="'.$this->reg->get('db')->escape($title).'", '.
					 'note="'.$this->reg->get('db')->escape($note).'", '.
					 'creation_date="'.$this->reg->get('db')->escape($modification_date).'", '.
					 'idPrint_size="'.intval($idPrint_size).'", '.
					 'img_type="'.$this->reg->get('db')->escape($image_type).'", '.
					 'timestamp="'.$this->reg->get('db')->escape($modification_date).'", '.
					 'data="'.$this->reg->get('db')->escape($data).'", '.
					 'start_date="'.$this->reg->get('db')->escape($start_date).'", '.
					 'end_date="'.$this->reg->get('db')->escape($end_date).'" '.
					 'WHERE idProgram="'.intval($idProgram).'";';
				$rs = $this->reg->get('db')->query($query);
				if(isset($rs->affected_rows)){
					return array('programid'=>$idProgram);
				}
				$arr['error']        = 1;
				$arr['errormessage'] = $this->reg->get('err')->get(770);
				return $arr;
			} else if($idProgram == -1 && $idProgram_from == -1){
				return 1;
			}
		}
		$arr['error']        = 1;
                $arr['errormessage'] = $this->reg->get('err')->get(108);
                return $arr;
	}
		
	//-------------------------------------------------------------------------------------------------------------
	public function isNewProgramNameExist($data){
		//verify if a new program name already exist
		/*
		RECEIVER:
			data:{
				"clientid":888888,
				"programname":"AAAA"
				}
		SENDER:
			data:{
				exist:"0"
				}
		*//*
		$arr = array(
			'exist' => 0,
			);
		//test exception	
		if($data['programname'] == 'XXX'){ //test with XXX
			$arr['exist'] = 1;
			}
		return $arr;*/
		$result = $this->isProgramNameExist($data);
		switch($result){
			case 'fail_parameter':
			case 'fail_finding_locale':
			case 'fail_to_access_client':
				$result = false;
			break;
		}
		return $result;
	}

	//-------------------------------------------------------------------------------------------------------------
	public function isModifiedProgramNameExist($data){
		//verify if the modified program name of an existing program already exist
		/*
		RECEIVER:
			data:{
				"clientid":888888,
				"programid": 1820274, 
				"programname":"CCCCCCCAAAA",
				"programnotes":""
				}
		SENDER:
			data:{
				exist:"0"
				}
		*//*
		$arr = array(
			'exist' => 0,
			);	
		//test exception	
		if($data['programname'] == 'XXX'){ //test with XXX
			$arr['exist'] = 1;
			}
		return $arr;*/
		$result = $this->isProgramNameExist($data);
		switch($result){
			case 'fail_parameter':
			case 'fail_finding_locale':
			case 'fail_to_access_client':
				$result = false;
			break;
		}
		return $result;
	}

	//-------------------------------------------------------------------------------------------------------------
	public function isClientProgramNameExist($data){
		//when we give construct a program and select a client after, does a check if the name exist
		/*
		RECEIVER:
			data:{
				"clientid":123456,
				"programname":"TEMPLATE 1003"
				}
		SENDER:
			data:{
				exist:"0"
				}
		*//*
		$arr = array(
			'exist' => 0,
			);	
		//test exception	
		if($data['programname'] == 'XXX'){ //test with XXX
			$arr['exist'] = 1;
			}
		
		return $arr;
		}
		*/
		$result = $this->isProgramNameExist($data);
		switch($result){
			case 'fail_parameter':
			case 'fail_finding_locale':
			case 'fail_to_access_client':
				$result = false;
			break;
		}
		return $result;
	}
	
	//-------------------------------------------------------------------------------------------------------------
	private function isProgramNameExist($data, $check_client_acccess = true){
		if(isset($data["programname"]) && !empty(trim($data["programname"])) && isset($data['clientid']) && is_numeric($data['clientid']) && $data['clientid'] > 0){			
			$idClient	= $data['clientid'];
			$new_title	= decodeString($data["programname"]);
			$new_title	= mb_strtolower($new_title, 'UTF-8');
			$client_locale  = 'en_US';
			if($check_client_acccess){
				$clientArr 	= $this->reg->get('utils')->getUserClients();
				// check user->clients access
				if(!isset($clientArr[$idClient])){
					// user does not have access for this client
					return 'fail_to_access_client';
				}
				#
				# get client locale and check access for client
				#
				$client_locale = $clientArr[$idClient]['locale'];
				if(!isset($client_locale) || empty($client_locale)){
					//error unable to find client
					return 'fail_finding_locale';
				}
			} else {
				$query_external = 'SELECT locale FROM client WHERE idClient="'.intval($idClient).'";';
				$rs_external = $this->reg->get('db-ext')->query($query_external);
				if ($rs_external && $rs_external->num_rows) {
					$client_locale = $rs_external->row['locale'];
				}
				unset($rs_external);
				$client_locale	= !empty($client_locale) ? $client_locale: 'en_US';
			}
			$query = 'SELECT title FROM program WHERE idClient="'.intval($idClient).'" ';
			if(isset($data["programid"]) && is_numeric($data["programid"]) && $data["programid"] > 0){
				$query .= 'AND idProgram NOT IN ("'.intval($data["programid"]).'")';
			}
			$query .= ';';
			$rs = $this->reg->get('db')->query($query);
			if ($rs && $rs->num_rows) {
				foreach ($rs->rows AS $idRow => $row){
					$title_data = $row['title'];
					$old_title_array = json_decodeStr($title_data);
					if (isset($old_title_array[$client_locale])) {
						$old_title = decodeString($old_title_array[$client_locale]);
						if (mb_strtolower($old_title, 'UTF-8') == $new_title) {
							return array('exist' => 1);
						}
					}
				}
				unset($rs);
			}
			return array('exist' => 0);
		}
		//error invalid receiver data
		return 'fail_parameter';
	}
}
//END