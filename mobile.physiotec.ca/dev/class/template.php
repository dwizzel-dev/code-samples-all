<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	TEMPLATE
@todo: 

- test all methodes
- @missing: saveTemplateModification - default print size - should it be sent with the receiver or not??
- @necessary to use htmlentities for the new changed data - because of json_encode will fail if there is wierd characters.
- check for the owner of the protocol before doing a modification arround line 381
- all template will be saved as USER template (not brand, not license, not clinic)
- get rid of "level"


*/


class Template {
	
	private $reg;
	private $className = 'Template';
	
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
	public function isTemplateNameExist($data){
		//verify if the template name already exist
		/*
		RECEIVER:
			data:{
				"templatename":"aaaaaa demo"
				}
		SENDER:
			data:{
				exist:"1"
				}
		*/
		if(isset($data["templatename"]) && !empty(trim($data["templatename"]))){
			$idUser	   = $this->reg->get('sess')->get('idUser');
			$locale	   = $this->reg->get('sess')->get('locale');
			$new_title = decodeString($data["templatename"]);
			$new_title = mb_strtolower($new_title, 'UTF-8');
			$query    = 'SELECT title FROM protocol WHERE idUser = "'.intval($idUser).'";';
			$rs = $this->reg->get('db')->query($query);
			if ($rs && $rs->num_rows) {
				foreach ($rs->rows AS $idRow => $row){
					$old_title_array = json_decodeStr($row['title']);
					if (isset($old_title_array[$locale])) {
						$old_title = decodeString($old_title_array[$locale]);
						if (mb_strtolower($old_title, 'UTF-8') == $new_title) {
							unset($rs);
							return array('exist' => 1);
							}
						}
					}
				unset($rs);
				}
			return array('exist' => 0);
			}
		//error at first level
		return false;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function createNewTemplate($data){
		//create a new template
		/*
		RECEIVER:
			data:{
				"templatename":"AATEMPLATE 1003",
				"templatemodule":"3"
				}
		SENDER:
			data:"1454512757"
		*/
		$arr = array();
		// check template title & module isset
		if(!isset($data["templatename"]) || !isset($data["templatemodule"])){
			$arr['error']        = 1;
			$arr['errormessage'] = $this->reg->get('err')->get(108);
			return $arr;
		}
		if(empty(trim($data["templatename"]))){
			$arr['error']        = 1;
			$arr['errormessage'] = $this->reg->get('err')->get(650);
			return $arr;
		}
		if(!is_numeric($data["templatemodule"]) || !($data["templatemodule"] > 0)){
			$arr['error']        = 1;
			$arr['errormessage'] = $this->reg->get('err')->get(655);
			return $arr;
		}
		
		$idProtocol_orig = 0;
		$idUser		 = $this->reg->get('sess')->get('idUser');
		$brand_data	 = $this->reg->get('sess')->get('brand');
		$locale	   	 = $this->reg->get('sess')->get('locale');
		$idBrand	 = $brand_data['idBrand'];
		$licence_data	 = $this->reg->get('sess')->get('licence');
		$idLicence	 = $licence_data['idLicence'];
		$available_locale= $licence_data['available_locale'];
		$clinic_data	 = $this->reg->get('sess')->get('clinic');
		$idClinic	 = $clinic_data['idClinic'];
		$idPrint_size	 = $this->reg->get('sess')->get('print_size');
		$title		 = '{}';
		$note		 = '{}';
		$protocol_data	 = '{}';
		//$creation_date	 = buildDate($this->reg->get('req')->get('time'));
		
		$to_delete	= 0;
		$idModule	= $data['templatemodule'];
		$new_title 	= decodeString($data["templatename"]);
		$new_titletmp	= mb_strtolower($new_title, 'UTF-8');
		// update title and check if already exist for a previous template
		$query    = 'SELECT title FROM protocol WHERE idUser = "'.intval($idUser).'";';
		$rs = $this->reg->get('db')->query($query);
		if ($rs && $rs->num_rows) {
			foreach ($rs->rows AS $idRow => $row){
				$old_title_array = json_decodeStr($row['title']);
				if (isset($old_title_array[$locale])) {
					$old_title = decodeString($old_title_array[$locale]);
					if (mb_strtolower($old_title, 'UTF-8') == $new_titletmp) {
						unset($rs);
						$arr['error']        = 1;
						$arr['errormessage'] = $this->reg->get('err')->get(651);
						return $arr;
					}
				}
			}
			unset($rs);
		}
		$new_title = encodeString($new_title);
		//build the title
		$titleArr = array();
		foreach ($available_locale As $locale){
			$titleArr[$locale] = $new_title;
		}
		$title = json_endecodeArr($titleArr);
		// insert new protocol base data
		$query = 'INSERT INTO protocol (idProtocol_orig, idPrint_size, idUser, idBrand, idLicence, idClinic, title, note, creation_date, data, to_delete) VALUES ("'.intval($idProtocol_orig).'", "'.intval($idPrint_size).'", "'.intval($idUser).'", "'.intval($idBrand).'", "'.intval($idLicence).'", "'.intval($idClinic).'", "'.$this->reg->get('db')->escape($title).'", "'.$this->reg->get('db')->escape($note).'", NOW(), "'.$this->reg->get('db')->escape($protocol_data).'", "'.intval($to_delete).'");';
		$rs = $this->reg->get('db')->query($query);
		// idTemplate
		$idProtocol = $rs->insert_id;
		unset($rs);
		if(is_numeric($idProtocol) && $idProtocol > 0 ){
			$query = 'INSERT INTO protocol_module(idProtocol, idModule) VALUES("'.intval($idProtocol).'", "'.intval($idModule).'");';	
			$this->reg->get('db')->query($query);
		} else {
			$arr['error']        = 1;
			$arr['errormessage'] = $this->reg->get('err')->get(652);
			return $arr;
		}
		return $idProtocol;
	}

	//-------------------------------------------------------------------------------------------------------------
	public function saveTemplateModification($data){
		//save existing template modifications
		/*
		NOTES: 	
			if id = -1 AND overwritename = true
				then we have a new template wihout id but we want to overwrite an old one
				so we have to send him the id of the protocol we are overwriting
			if id != -1 AND keeporiginal = true
				so we have to keep the original one	
				

		RECEIVER:
			data:{
				"id":77760, 
				"name":"FFF",
				"notes":"abc",
				"order":"103542",
				"module":"1",
				"overwritename":false  //ou true
				"keeporiginal":false  //ou true
				"exercices":[{
					"id":103542,
					"settings":{
						"sets":"",
						"repetition":"",
						"hold":"",
						"weight":"",
						"tempo":"",
						"rest":"",
						"frequency":"",
						"duration":""
						},
					"flip":0,
					"mirror":0,
					"code":"xgen",
					"programdata":{
						"locale":{
							"fr_CA":{
								"description":"Débutez en position de squat avec un élastique autour des chevilles.\nEn gardantl'élastique tendu en tout temps, effectuez des pas latéraux.\nPoussez les genoux vers l'extérieur en vous déplaçant sur le côté de façon à ce qu'ils ne soient pas tirés vers l'intérieur.\nChaque pas doit couvrir environ 50% de la distance initiale entre les deux pieds.",
								"short_title":"aaaaaPas latéraux avec élastique",
								"level":""
								},
							"en_US":{
								"description":"Start in a squat position with a band around your ankles.\nKeeping the band taut at all times, step to the side.\nPush the knees out while taking the steps so they don't cave in.\nEach step is about 50% of the starting position stance.",
								"short_title":"bbbbbSidestep with band",
								"level":""
								}
							}
						}
					}]
				}
		SENDER:
			data:"1"
		*/
		
		if(count($data) > 0 && isset($data['id']) && is_numeric($data['id']) && isset($data['module']) && is_numeric($data['module']) && $data['module'] > 0 && isset($data['name']) && !empty(trim($data['name']))){
			// collecting necessary info
			$idUser		 = $this->reg->get('sess')->get('idUser');
			$user_locale	 = $this->reg->get('sess')->get('locale');
			$licence_data	 = $this->reg->get('sess')->get('licence');
			$available_locale= $licence_data['available_locale'];
			//handle basic data info
 			$idProtocol	= $data['id'];
			$ex_order	= isset($data['order']) ? $data['order']: '';
			$overwritename	= (isset($data['overwritename']) && !empty($data['overwritename'])) ? $data['overwritename'] : false;
			$bKeepOriginal	= (isset($data['keeporiginal']) && !empty($data['keeporiginal'])) ? $data['keeporiginal'] : false;
			$idModule	= $data['module'];
			$exercises	= isset($data['exercices']) ? $data['exercices'] : array();
			$title		= isset($data['name']) ? stripslashes(decodeString($data['name'])) : decodeString(translate('untitled'));
			$titleArr	= array();
			$note		= isset($data['notes']) ? stripslashes(decodeString($data['notes'])) : '{}';
			$noteArr	= array();
			$exercisesArr	= array();
			$exercise_ids	= array();
			$arr		= array();
			$videos		= array();
			$protocol_data	= '{}';
			/*
			if($overwritename == true && $idProtocol <= 0 ){
				$arr = array(
					'error' => 1,
					'errormessage' => $this->reg->get('err')->get(657)
					);
				return $arr;
			}
			*/
			//first check if he have the roghts to modify the template if it's not an overwrite
			//sinon on avertit qu'il n'est pas proprietaire			
 			$new_title = decodeString($data['name']);
			$new_titletmp = mb_strtolower($new_title, 'UTF-8');
			if(empty($overwritename)){
				$query = 'SELECT 1 FROM protocol WHERE idUser = "'.intval($idUser).'" AND idProtocol = "'.intval($idProtocol).'";';
				$rs = $this->reg->get('db')->query($query);
				if(!$rs || ($rs && !$rs->num_rows)){
					//donc pas les droits
					$arr = array(
						'error' => 1,
						'errormessage' => $this->reg->get('err')->get(656)
						);
					return $arr;
				}
				unset($rs);
				//check if title is already used by other template
				//$query = 'SELECT title FROM protocol WHERE idUser = "'.intval($idUser).'" AND idProtocol NOT IN ('.intval($idProtocol).');';
				$query = 'SELECT title FROM protocol WHERE idUser = "'.intval($idUser).'" AND idProtocol <> "'.intval($idProtocol).'";';
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){
					foreach ($rs->rows AS $idRow => $row){
						$old_title_array = json_decodeStr($row['title']);
						if(isset($old_title_array[$user_locale])) {
							$old_title = decodeString($old_title_array[$user_locale]);
							if(mb_strtolower($old_title, 'UTF-8') == $new_titletmp){
								unset($rs);
								//send exist to 1
								return array('exist' => 1);
								}
							}
						}
					unset($rs);
					}
				}
			$title = encodeString($new_title);
			// should be encode not decode becausse its already decoded
			$note = encodeString($data['notes']);
			// build data
			foreach($available_locale AS $locale_licence){
				$titleArr[$locale_licence] = $title;
				$noteArr[$locale_licence]  = $note;
				}
			//
			$title = json_endecodeArr($titleArr);
			$note = json_endecodeArr($noteArr);
			//
			if(count($exercises) > 0){
				foreach($exercises AS $ex_data){
					$id_ex = $ex_data['id'];
					$code_ex = $ex_data['code'];
					$settings_ex = $ex_data['settings'];
					$flip_ex = isset($ex_data['flip']) ? $ex_data['flip'] : 0;
					$mirror_ex = isset($ex_data['mirror'])?$ex_data['mirror'] : 0;
					$locale_ex = $ex_data['programdata']['locale'];
					//handle data
					$exercise_ids[]	= $id_ex;
					$exercisesArr[$id_ex]['codeExercise'] = $code_ex;
					$exercisesArr[$id_ex]['picture'] = array();
					$exercisesArr[$id_ex]['drawing'] = array();
					$exercisesArr[$id_ex]['video'] = array();
					$exercisesArr[$id_ex]['mirror'] = $mirror_ex;
					$exercisesArr[$id_ex]['flip'] = $flip_ex;
					$default_locale_array = isset($locale_ex[$user_locale]) ? $locale_ex[$user_locale]: (isset($locale_ex['en_US']) ? $locale_ex['en_US']:$locale_ex[array_keys($locale_ex)[0]]);
					//
					foreach($available_locale AS $locale_licence){
						if(isset($locale_ex[$locale_licence])){
							if(!(isset($locale_ex[$locale_licence]['short_title']) && !empty($locale_ex[$locale_licence]['short_title']) && isset($locale_ex[$locale_licence]['description']) && !empty($locale_ex[$locale_licence]['description']))){
								$locale_ex[$locale_licence] = $default_locale_array;
								}
						}else{
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
				//query for all exercise ids
				$query = 'SELECT idExercise, data FROM exercise WHERE idExercise IN ('.implode(',', $exercise_ids).');';
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){
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
							}else{
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
				//reorder exercises
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
				$protocol_data = json_endecodeArr($ordered_exercises);
				}
			//si overwrite = true, fetch the id of template with the same name we want to overwrite
			if($overwritename){
				$new_title = decodeString($data['name']);
				$new_titletmp  = mb_strtolower($new_title, 'UTF-8');
				$idProtocoleToOverwrite = 0;
				//$query = 'SELECT idProtocol, title FROM protocol WHERE idUser = "'.intval($idUser).'" AND idProtocol <> "'.intval($idProtocol).'";';
				$query = 'SELECT idProtocol, title FROM protocol WHERE idUser = "'.intval($idUser).'";';
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){
					foreach($rs->rows AS $idRow => $row){
						//si jamais ecrase celui qu'il edite, car peut changer seulement le module
						//ou faire un save as avec meme nom ou lieu d'un save car les usagers sont pas vite vite
						if($row['idProtocol'] == $idProtocol){
							$idProtocoleToOverwrite = intVal($row['idProtocol']);
							//break;
							}
						$old_title_array = json_decodeStr($row['title']);
						if(isset($old_title_array[$user_locale])) {
							$old_title = decodeString($old_title_array[$user_locale]);
							if (mb_strtolower($old_title, 'UTF-8') == $new_titletmp) {
								$idProtocoleToOverwrite = intVal($row['idProtocol']);
								break;
								}
							}
						}
					}
				unset($rs);
				//on a trouve un id alors on remplace
				//peut etre qu"il veut s'ecraser lui meme car il veut seulement changer le module
				if($idProtocoleToOverwrite != 0){
					//replace with the new protocole
					$query = 'UPDATE protocol SET title = "'.$this->reg->get('db')->escape($title).'", note = "'.$this->reg->get('db')->escape($note).'", last_update = NOW(), data = "'.$this->reg->get('db')->escape($protocol_data).'" WHERE idProtocol = "'.intval($idProtocoleToOverwrite).'" AND idUser = "'.intval($idUser).'";';
					$rs = $this->reg->get('db')->query($query);
					if(isset($rs->affected_rows)){
						unset($rs);
						//uniquement si un existant overwrite un vieux, 
						//car si -1 alors pas encore de id du nouveau qui ecrase
						//mais si on a un keeporiginal = true, alors on ne supprime pas
						//delete les protocol_module de clui que l'on ecrase
						if($idProtocol > 0 && !$bKeepOriginal && ($idProtocoleToOverwrite != $idProtocol)){
							//delete the old one protocol_module
							$query = 'DELETE FROM protocol_module WHERE idProtocol = "'.intval($idProtocol).'";';
							$this->reg->get('db')->query($query);
							//delete le old protocole
							$query = 'DELETE FROM protocol WHERE idProtocol = "'.intval($idProtocol).'";';
							$this->reg->get('db')->query($query);
						}
						if($idProtocoleToOverwrite == $idProtocol){
							$query = 'UPDATE protocol_module SET idModule = "'.intval($idModule).'" WHERE idProtocol = "'.intval($idProtocoleToOverwrite).'";';
							$this->reg->get('db')->query($query);
						}
						
						if($bKeepOriginal && ($idProtocoleToOverwrite != $idProtocol)){
							$query = 'SELECT idModule FROM protocol_module WHERE idProtocol = "'.intval($idProtocoleToOverwrite).'";';
							$rs = $this->reg->get('db')->query($query);
							if($rs && $rs->num_rows){
								$idModuletmp = $rs->row['idModule'];
								if($idModuletmp != $idModule){ //  update
									$query = 'UPDATE protocol_module SET idModule = "'.intval($idModule).'" WHERE idProtocol = "'.intval($idProtocoleToOverwrite).'";';
									$this->reg->get('db')->query($query);
								}
							} else {
								//insert in the protocol_module with the new id
								$query = 'INSERT INTO protocol_module (idProtocol, idModule) VALUES("'.intval($idProtocoleToOverwrite).'", "'.intval($idModule).'");';
								$this->reg->get('db')->query($query);
							}
						}
						return $idProtocoleToOverwrite; //instead of "1" we are sending the id of the protocol we overwrite
					}
					unset($rs);
					$arr = array(
						'error' => 1,
						'errormessage' => $this->reg->get('err')->get(654)
						);
					return $arr;	
					}
				$arr = array(
					'error' => 1,
					'errormessage' => $this->reg->get('err')->get(658)
					);
				return $arr;
			}else{
				// update protocol
				$query = 'UPDATE protocol SET idProtocol_orig = "'.intval($idProtocol).'", title = "'.$this->reg->get('db')->escape($title).'", note = "'.$this->reg->get('db')->escape($note).'", last_update = NOW(), data = "'.$this->reg->get('db')->escape($protocol_data).'" WHERE idProtocol = "'.intval($idProtocol).'" AND idUser = "'.intval($idUser).'";';
				$rs = $this->reg->get('db')->query($query);
				if(isset($rs->affected_rows)){
					$query = 'UPDATE protocol_module SET idModule = "'.intval($idModule).'" WHERE idProtocol = "'.intval($idProtocol).'";';
					$this->reg->get('db')->query($query);
					unset($rs);
					return 1;
					}
				unset($rs);
				$arr = array(
					'error' => 1,
					'errormessage' => $this->reg->get('err')->get(654)
					);
				return $arr;
				}

			}
		//
		$arr = array(
			'error' => 1,
			'errormessage' => $this->reg->get('err')->get(108)
			);
		return $arr;
	}

	}


//END