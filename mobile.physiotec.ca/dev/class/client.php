<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@modif:	02-03-2016
@info:	CLIENT
@todo:
	1. crash server line: \(|\)|\[|\]|\^|\$|\{|\}|\?|\||\.|\=|\+|\/|\-|\* in preg_replace
	2. decodestring for each word you are putting in the email_template.php, be sure to test send html characters in the received emails in the clients side
	3. get rid og the "level" property and "drawing"

*/


class Client {
	
	private $reg;
	private $className = 'Client';
	
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
	public function getSingleClientInfosById($data, $clientArr = false){
		//gets the info of a specific client by id when the client click directly on the link from the autocomplete data of input-main-clent-search-autocomplete
		/*
		RECEIVER:
			data:"888888"
		SENDER:
			data:{
				0:{ 
					id: 888888,
					username:"gutathib2",
					active:"1",
					qty_try:"0",
					name:"",
					file:"",
					department:"",
					firstname:"Nadja",
					lastname:"Mbarki",
					locale:"fr_CA",
					email:"dwizzel@...@physiotec.ca",
					creation_date:"2015-03-16",
					idUser:"888888",
					user_firstname:"Pierre",
					user_lastname:"Leblanc",
					password:"XXX",
					email2:"",
					programs:{
						222:{
							name:"N-222",
							notes:"this is a test note for N-222",
							exercices:{
								200008:{
									id:"200005",
									idUser:"0",
									idModule:"29",
									category:"3",
									filter:"404",
									data:"{"locale":{"en_US":{"short_title":"EXR-200005","title":"10-H. LATERAL ASSIST - OPPOSITE SIDE","description":"Attach cord to your side and get into a &quot;spread leg&quot; position with tension on cord. Step forward with back leg to cross over other leg, take one more step, walking toward anchor. Reverse motion under controlled conditions and return to starting position. Repeat for required sets and reps. Then repeat bilaterally.","level":""},"fr_CA":{"short_title":"EXR-200005","title":"Assistance lat&eacute;rale, c&ocirc;t&eacute; oppos&eacute;","description":"Attachez un &eacute;lastique sur le cot&eacute; du corps et &eacute;cartez les jambes en gardant une tension sur l'&eacute;lastique. Faites un pas vers le point d'attache de l'&eacute;lastique avec la jambe &eacute;loign&eacute;e pour croiser devant l'autre jambe puis un pas de plus. Revenez en contr&ocirc;le &agrave; la position de d&eacute;part. R&eacute;p&eacute;tez pour le nombre de","level":""}},"picture":[{"pic":"/images/exercices/GEN101442_A.jpg","thumb":"/images/exercices/thumbs/GEN101442_A.jpg"},{"pic":"/images/exercices/GEN101442_B.jpg","thumb":"/images/exercices/thumbs/GEN101442_B.jpg"}],"drawing":[{"pic":"/images/exercices/GEN101442_A.jpg","thumb":"/images/exercices/GEN101442_A.jpg"},{"pic":"/images/exercices/GEN101442_B.jpg","thumb":"/images/exercices/GEN101442_B.jpg"}]}",
									userdata:"{"locale":{"en_US":{"short_title":"EXR-200005","title":"10-H. LATERAL ASSIST - OPPOSITE SIDE","description":"Attach cord to your side and get into a &quot;spread leg&quot; position with tension on cord. Step forward with back leg to cross over other leg, take one more step, walking toward anchor. Reverse motion under controlled conditions and return to starting position. Repeat for required sets and reps. Then repeat bilaterally.","level":""},"fr_CA":{"short_title":"EXR-200005","title":"Assistance lat&eacute;rale, c&ocirc;t&eacute; oppos&eacute;","description":"Attachez un &eacute;lastique sur le cot&eacute; du corps et &eacute;cartez les jambes en gardant une tension sur l'&eacute;lastique. Faites un pas vers le point d'attache de l'&eacute;lastique avec la jambe &eacute;loign&eacute;e pour croiser devant l'autre jambe puis un pas de plus. Revenez en contr&ocirc;le &agrave; la position de d&eacute;part. R&eacute;p&eacute;tez pour le nombre de","level":""}}}",
									programdata:"{"locale":{"en_US":{"short_title":"EXR-200005","title":"10-H. LATERAL ASSIST - OPPOSITE SIDE","description":"Attach cord to your side and get into a &quot;spread leg&quot; position with tension on cord. Step forward with back leg to cross over other leg, take one more step, walking toward anchor. Reverse motion under controlled conditions and return to starting position. Repeat for required sets and reps. Then repeat bilaterally.","level":""},"fr_CA":{"short_title":"EXR-200005","title":"Assistance lat&eacute;rale, c&ocirc;t&eacute; oppos&eacute;","description":"Attachez un &eacute;lastique sur le cot&eacute; du corps et &eacute;cartez les jambes en gardant une tension sur l'&eacute;lastique. Faites un pas vers le point d'attache de l'&eacute;lastique avec la jambe &eacute;loign&eacute;e pour croiser devant l'autre jambe puis un pas de plus. Revenez en contr&ocirc;le &agrave; la position de d&eacute;part. R&eacute;p&eacute;tez pour le nombre de","level":""}}}",
									codeExercise:"EXR-200005",
									video:"videos.sproutvideo.com/embed/e89bddb11315e1ce60/678473c7e2714564",
									flip:"1",
									mirror:"1",
									settings:{
										sets:"0",
										repetition:"0",
										hold:"0",
										weight:"0",
										tempo:"0",
										rest:"0",
										frequency:"0",
										duration:"0"
										},
									settings-lang:{
										fr_CA:{
											sets:"",
											repetition:"",
											hold:"",
											weight:"",
											tempo:"",
											rest:"",
											frequency:"",
											duration:""
											},
										en_US:{
											sets:"",
											repetition:"",
											hold:"",
											weight:"",
											tempo:"",
											rest:"",
											frequency:"",
											duration:""
											}
										}
									},
								},
							order:"200009" //comma separeated 11111,222,333,444, etc....
							}
						}
					}
				}
		*/
		
		if(!empty($data) && is_numeric($data) && $data > 0){
			if(!isTrue($clientArr)){
				$clientArr = $this->reg->get('utils')->getUserClients();
				}
			if(count($clientArr) > 0){
				$idUser = $this->reg->get('sess')->get('idUser');
				$idClient = $data;
				$access_all_clients	= $this->reg->get('sess')->get('access_all_clients');
				$licence_admin = $this->reg->get('sess')->get('licence_admin');
				$locale = $this->reg->get('sess')->get('locale');
				$licence_data  = $this->reg->get('sess')->get('licence');
				$available_locale = array_flip($licence_data['available_locale']);
				$idLicence = $licence_data['idLicence'];
				$oCipher = new Cipher(PASS_CYPHER_SALT);
				//check if the user have access for this client
				if(isset($clientArr[$idClient])){
					//get client locale
					$Client_locale = $clientArr[$idClient]['locale'];
					$clientArr[$idClient]['programs'] = array();
					//
					if(!isset($available_locale[$Client_locale])){
						$Client_locale = isset($available_locale['en_US']) ? $available_locale['en_US'] : array_keys($available_locale)[0];
						}
					//fetching client programs
					$usersArr = array();
					if($licence_admin == 1 ){
						//access for all programs of users within this licence
						$usersArr = $this->reg->get('utils')->getLicenceUsers();
					}else if($access_all_clients == 1){
						//access for all programs of users within this clinic
						$usersArr = $this->reg->get('utils')->getClinicUsers();
					}else{
						//access for the user only programs
						$usersArr = array(0 => $idUser);
						}
					//get programs based on the user access level
					$program_query = 'SELECT idProgram, idUser, title, note, data, img_type, idPrint_size FROM program WHERE idUser IN ('.implode(', ', $usersArr).') AND idClient = "'.intVal($idClient).'";';
					$rs = $this->reg->get('db')->query($program_query);
					if($rs && $rs->num_rows){
						foreach ($rs->rows AS $idRow =>$row){
							$program = array();
							$idProgram = $row['idProgram'];
							$program_name = json_decodeStr($row['title']);
							$program_notes = json_decodeStr($row['note']);
							//basic infos
							$program['name'] = isset($program_name[$Client_locale])? decodeString($program_name[$Client_locale]):decodeString(translate('untitled'));
							$program['notes'] = isset($program_notes[$Client_locale])? decodeString($program_notes[$Client_locale]):'';
							//exercvices data from program
							$exercices = json_decodeStr($row['data']);

							//print_r($exercices);
							//echo '---------------'.EOL.EOL;
							//echo $exercices;
							//exit('---------------'.EOL.EOL);

							//
							$exercises_tmp = array();
							if(is_array($exercices) && count($exercices) > 0){
								$exercises_ids = array();
								foreach($exercices AS $idExercise => $exercise_data){
									// build exercise data
									$settings_lang = array();
									$settings = array();
									$data_locale = array();
									$exercises_ids[] = $idExercise;
									//data locale de l'exercice
									foreach($available_locale AS $av_locale => $key){
										// @to check if we have to encode the settings since it will be json_encode
										$settings_lang[$av_locale]['sets'] = isset($exercise_data['locale'][$av_locale]['sets']) ? decodeString($exercise_data['locale'][$av_locale]['sets']):'';
										//
										$settings_lang[$av_locale]['repetition'] = isset($exercise_data['locale'][$av_locale]['repetition']) ? decodeString($exercise_data['locale'][$av_locale]['repetition']):'';
										//
										$settings_lang[$av_locale]['hold'] = isset($exercise_data['locale'][$av_locale]['hold']) ? decodeString($exercise_data['locale'][$av_locale]['hold']):'';
										//
										$settings_lang[$av_locale]['weight'] = isset($exercise_data['locale'][$av_locale]['weight']) ? decodeString($exercise_data['locale'][$av_locale]['weight']):'';
										//
										$settings_lang[$av_locale]['tempo']	= isset($exercise_data['locale'][$av_locale]['tempo']) ? decodeString($exercise_data['locale'][$av_locale]['tempo']):'';
										//
										$settings_lang[$av_locale]['rest'] = isset($exercise_data['locale'][$av_locale]['rest']) ? decodeString($exercise_data['locale'][$av_locale]['rest']):'';
										//
										$settings_lang[$av_locale]['frequency'] = isset($exercise_data['locale'][$av_locale]['frequency']) ? decodeString($exercise_data['locale'][$av_locale]['frequency']):'';
										//
										$settings_lang[$av_locale]['duration'] = isset($exercise_data['locale'][$av_locale]['duration']) ? decodeString($exercise_data['locale'][$av_locale]['duration']):'';
										//
										//@to check encodestrong(decodestring()) have to remove #135; characters
										$data_locale['locale'][$av_locale]['short_title'] = isset($exercise_data['locale'][$av_locale]['short_title']) ? decodeString($exercise_data['locale'][$av_locale]['short_title']) : '';
										$data_locale['locale'][$av_locale]['title'] = isset($exercise_data['locale'][$av_locale]['title']) ? decodeString($exercise_data['locale'][$av_locale]['title']) : '';
										$data_locale['locale'][$av_locale]['description'] = isset($exercise_data['locale'][$av_locale]['description']) ? decodeString($exercise_data['locale'][$av_locale]['description']) : '';
										//
										if($av_locale == $Client_locale){
											$settings = $settings_lang[$av_locale];
											}
										}
									//
									$exercises_tmp[$idExercise]['id'] = $idExercise;
									$exercises_tmp[$idExercise]['codeExercise'] = $exercise_data['codeExercise'];
									$exercises_tmp[$idExercise]['mirror'] = isset($exercise_data['mirror']) ? $exercise_data['mirror'] : 0;
									//settings
									$exercises_tmp[$idExercise]['settings'] = $settings;
									//PUT BACK THE SETTING LANG IF NEEDED DWIZZEL 30-03-2016
									//other infos	
									$exercises_tmp[$idExercise]['flip'] = 0;
									//origina data
									$exercises_tmp[$idExercise]['programdata'] = json_endecodeArr($data_locale);
									// default video
									$exercises_tmp[$idExercise]['video'] = '';
									}
									//get videos
									$query = 'SELECT idExercise, host, embed_code FROM video WHERE idExercise IN ('.implode(', ', $exercises_ids) .');';
									$rs = $this->reg->get("db")->query($query);
									if($rs && $rs->num_rows){
										foreach ($rs->rows AS $idRow => $row){
											if($row['host'] == 'sprout'){
												$exercises_tmp[$row['idExercise']]['video'] = PATH_VIDEO_SPROUT.$row['embed_code'];
											}
										}
										unset($rs);
									}
									//select original exercise data
									$query = 'SELECT idExercise, data FROM exercise WHERE idExercise IN ('.implode(', ', $exercises_ids) .');';

									//echo $query.EOL;
									//echo '- 1 ----------------------------------------------------------'.EOL.EOL;	

									$rs = $this->reg->get("db")->query($query);
									if($rs && $rs->num_rows){
										foreach ($rs->rows AS $idRow => $row){
											//
											$exercise_data = json_decodeStr($row['data']);

											//echo $row['data'].EOL;
											//echo '- 2 ----------------------------------------------------------'.EOL.EOL;	
											//print_r($exercise_data);
											//echo '- 3 ----------------------------------------------------------'.EOL.EOL;	
	
											if(!is_array($exercise_data)){
												$exercise_data = array();
											}
											//pics-1
											if(isset($exercise_data['picture'][0]['pic']) && file_exists($exercise_data['picture'][0]['pic']) && filesize($exercise_data['picture'][0]['pic']) > 0) {
												$exercise_data['picture'][0]['pic'] = base64_encode($oCipher->encrypt($idLicence . ';' . $exercise_data['picture'][0]['pic']));
											}else{
												$exercise_data['picture'][0]['pic'] = '';
											}
											//thumbs-1
											if(isset($exercise_data['picture'][0]['thumb']) && file_exists($exercise_data['picture'][0]['thumb']) && filesize($exercise_data['picture'][0]['thumb']) > 0) {
												$exercise_data['picture'][0]['thumb'] = base64_encode($oCipher->encrypt($idLicence . ';' . $exercise_data['picture'][0]['thumb']));
											}else{
												$exercise_data['picture'][0]['thumb'] = '';
											}
											//pics-2
											if(isset($exercise_data['picture'][1]['pic']) && file_exists($exercise_data['picture'][1]['pic']) && filesize($exercise_data['picture'][1]['pic']) > 0) {
												$exercise_data['picture'][1]['pic'] = base64_encode($oCipher->encrypt($idLicence . ';' . $exercise_data['picture'][1]['pic']));
											}else{
												$exercise_data['picture'][1]['pic'] = '';
											}
											//thumbs-2
											if(isset($exercise_data['picture'][1]['thumb']) && file_exists($exercise_data['picture'][1]['thumb']) && filesize($exercise_data['picture'][1]['thumb']) > 0) {
												$exercise_data['picture'][1]['thumb'] = base64_encode($oCipher->encrypt($idLicence . ';' . $exercise_data['picture'][1]['thumb']));
											}else{
												$exercise_data['picture'][1]['thumb'] = '';
											}
											//unset les drawings
											unset($exercise_data['drawing']);


											//TO REMOVE
											//unset($exercise_data['locale']);

											//echo '- 0 ----------------------------------------------------------'.EOL.EOL;	
											//print_r($exercise_data).EOL.EOL;
											
											//jsonize
											$exercises_tmp[$row['idExercise']]['data'] = decodeString(json_endecodeArr($exercise_data));

											//echo '- 1 ----------------------------------------------------------'.EOL.EOL;	
											//print_r($exercises_tmp[$row['idExercise']]['data']).EOL.EOL;
											//echo '- 2 ----------------------------------------------------------'.EOL.EOL;	
											


										}
										unset($rs);
									}
									//select user modified data
									$user_data = array_fill_keys(array_flip($available_locale), array('short_title'=>'', 'title'=>'', 'description'=>''));
									$query = 'SELECT idExercise, data FROM exercise_user WHERE idExercise IN ('.implode(', ', $exercises_ids).') AND idUser = "'.intVal($idUser).'";';
									$rs = $this->reg->get("db")->query($query);
									if($rs && $rs->num_rows){
										foreach ($rs->rows AS $idRow => $row){
											//adding locale level
											$exercises_data = json_decodeStr($row['data']);
											if(!is_array($exercises_data)){
												$exercises_data = array();
											}
											foreach($available_locale as $locale){
												if(isset($exercises_data[$locale]['title'])){
													$exercises_data[$locale]['title'] = decodeString($exercises_data[$locale]['title']);
												}
												if(isset($exercises_data[$locale]['short_title'])){
													$exercises_data[$locale]['short_title'] = decodeString($exercises_data[$locale]['short_title']);
												}
												if(isset($exercises_data[$locale]['description'])){
													$exercises_data[$locale]['description'] = decodeString($exercises_data[$locale]['description']);
												}
											}
											$exercise_user['locale'] = array_merge($user_data, $exercises_data);
											$exercises_tmp[$row['idExercise']]['userdata'] = json_endecodeArr($exercise_user);
											}
										unset($rs);
										}
								$program['order'] = implode(',', array_keys($exercices));
							}else{
								$program['order'] = '';
								}
							$clientArr[$idClient]['programs'][$idProgram] = $program;
							$clientArr[$idClient]['programs'][$idProgram]['exercices'] = $exercises_tmp;	
							}
						}
					unset($rs);
					//
					//print_r($clientArr[$idClient]);
					//
					return array($clientArr[$idClient]);

					}
				//error message user does not have access to this client 
				$arr = array(
					'error' => 1,
					'errormessage' => $this->reg->get('err')->get(600)
					);
				return $arr;
				}
			//return empty array in case of no client
			return array();
			}
		$arr = array(
			'error' => 1,
			'errormessage' => $this->reg->get('err')->get(108)
			);
		return $arr;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function getClientListingByWord($data){
		//gets the listing of client that contains words enter in the input box::input-main-client-search-autocomplete
		/*
		RECEIVER:
			data:"nal"
		SENDER:
			data:{
				0:{
					username:"gutathib2",
					id:888888,
					active:"1",
					qty_try:"0",
					name:"",
					file:"",
					department:"",
					firstname:"Olivier",
					lastname:"Renaldin",
					locale:"fr_CA",
					email:"dwizzel@...@physiotec.ca",
					creation_date:"2015-03-16",
					idUser:"123456",
					user_firstname:"Pierre",
					user_lastname:"Leblanc",
					password:"XXX",
					email2:"",
					programs:{
						779:{
							name:"DC-779",
							notes:"this is a test note for DC-COMIC-779",
							exercices:{
								390:{
									id:"390",
									idUser:"0",
									idModule:"29",
									category:"3",
									filter:"404",
									data:"{"locale":{"en_US":{"short_title":"Shoulder abduction with a long title instead","title":"Supine lying active shoulder overhead press (no weight)","description":"Lie on your back with both legs straight. Place your arms out to the side with the shoulders and elbows bent to 90 degrees. Tighten your abdominal muscles by pulling your belly button towards the floor and hold this contraction. Slide your arms up towards your ears.","level":""},"fr_CA":{"short_title":"Abduction &eacute;paules","title":"D&eacute;cubitus dorsal, abduction des &eacute;paules au-dessus de la t&ecirc;te (sans poids)","description":"Couchez-vous sur le dos avec les jambes fl&eacute;chies. Placez vos mains de c&ocirc;t&eacute; avec les &eacute;paules et coudes pli&eacute;s &agrave; 90 degr&eacute;s. Glissez vos bras vers votre t&ecirc;te en redressant vos coudes. Durcissez vos muscles abdominaux de fa&ccedil;on &agrave; ce que votre dos soit aplati contre le lit.","level":""}},"picture":[{"pic":"/images/exercices/XGEN1418_A.jpg","thumb":"/images/exercices/thumbs/XGEN1418_A.jpg"},{"pic":"/images/exercices/XGEN1418_B.jpg","thumb":"/images/exercices/thumbs/XGEN1418_B.jpg"}],"drawing":[{"pic":"/images/exercices/XGEN1418_A.jpg","thumb":"/images/exercices/XGEN1418_A.jpg"},{"pic":"/images/exercices/XGEN1418_B.jpg","thumb":"/images/exercices/XGEN1418_B.jpg"}]}",
									codeExercise:"XGEN1418",
									video:"videos.sproutvideo.com/embed/e89bddb11315e1ce60/678473c7e2714564",
									flip:"0",
									mirror:"0",
									settings:{
										sets:"Tsets",
										repetition:"Treps",
										hold:"Thold",
										weight:"Tweight",
										tempo:"Ttempo",
										rest:"Trest",
										frequency:"Tfreq",
										duration:"Tdur"
										},
									settings-lang:{
										fr_CA:{
											sets:"Tsets - FR",
											repetition:"Treps - FR",
											hold:"Thold - FR",
											weight:"Tweight - FR",
											tempo:"Ttempo - FR",
											rest:"Trest - FR",
											frequency:"Tfreq - FR",
											duration:"Tduration - FR"
											},
										en_US:{
											sets:"Tsets - EN",
											repetition:"Treps - EN",
											hold:"Thold - EN",
											weight:"Tweight - EN",
											tempo:"Ttempo - EN",
											rest:"Trest - EN",
											frequency:"Tfreq - EN",
											duration:"Tduration - EN"
											}
										}
									}
								},
							order:"390"
							}
						}
					}
				}
		*/	
		
		//this one only check for the first characters
		if(!empty(trim($data)) && is_string($data)){
			$data = preg_split("/\\s/", $data,-1, PREG_SPLIT_NO_EMPTY);
			$clientArr = $this->reg->get('utils')->getUserClients();
			$clientArr_return = array();
			$data1 = isset($data[0]) ? $data[0] : false;
			$data2 = isset($data[1]) ? $data[1] : false;			
			$part1 = safeRegExStr(mb_strtolower($data1, 'UTF-8'));
			$part2 = safeRegExStr(mb_strtolower($data2, 'UTF-8'));
			$name1 = (!empty($data1) && !empty($data2)) ? $data1.' '.$data2 : 
					((!empty($data1) && empty($data2)) ? $data1 : 
						((empty($data1) && !empty($data2)) ? $data2 : false));
			
			$name2 = (!empty($data2) && !empty($data1)) ? $data2.' '.$data1 : 
					((!empty($data2) && empty($data1)) ? $data2 : 
						((empty($data2) && !empty($data1)) ? $data1 : false));
			$case1Arr = array();
			$case2Arr = array(); 
			$case3Arr = array();
			$case4Arr = array();
			$case5Arr = array();
			$case6Arr = array();
			if(count($clientArr) > 0){
				foreach($clientArr AS $idClient => $client_data){
					if(isset($client_data['firstname']) && isset($client_data['lastname']) && isset($client_data['name'])){
						$client_name	  = mb_strtolower(decodeString($client_data['name']), 'UTF-8');
						$orig_client_firstname = decodeString($client_data['firstname']);
						$orig_client_lastname  = decodeString($client_data['lastname']);
						$client_firstname = mb_strtolower($orig_client_firstname, 'UTF-8');
						$client_lastname  = mb_strtolower($orig_client_lastname, 'UTF-8');
						$case1 = !empty($name1) && ($name1 == $client_name) ? true : false;
						$case2 = !empty($name2) && ($name2 == $client_name) ? true : false;
						$case3 = !empty($part1) && preg_match('/^'.$part1.'/', $client_firstname) ? true : false;
						$case4 = !empty($part1) && preg_match('/^'.$part1.'/', $client_lastname) ? true : false;
						$case5 = !empty($part2) && preg_match('/^'.$part2.'/', $client_firstname) ? true : false;
						$case6 = !empty($part2) && preg_match('/^'.$part2.'/', $client_lastname) ? true : false;
						$client_firstname = mb_strtolower($orig_client_firstname, 'UTF-8');
						$client_lastname  = mb_strtolower($orig_client_lastname, 'UTF-8');
						if($case1){
							$case1Arr[$idClient] = array(
										'id' => $idClient,
										'firstname' => $client_firstname,
										'lastname' => $client_lastname
										);
						} else if($case2) {
							$case2Arr[$idClient] = array(
										'id' => $idClient,
										'firstname' => $client_firstname,
										'lastname' => $client_lastname
										);
						} else if($case3) {
							$case3Arr[$idClient] = array(
										'id' => $idClient,
										'firstname' => $client_firstname,
										'lastname' => $client_lastname
										);
						} else if($case4) {
							$case4Arr[$idClient] = array(
										'id' => $idClient,
										'firstname' => $client_firstname,
										'lastname' => $client_lastname
										);
						} else if($case5) {
							$case5Arr[$idClient] = array(
										'id' => $idClient,
										'firstname' => $client_firstname,
										'lastname' => $client_lastname
										);
						} else if($case6) {
							$case6Arr[$idClient] = array(
										'id' => $idClient,
										'firstname' => $client_firstname,
										'lastname' => $client_lastname
										);
						}
					}
				}
				$result = $case1Arr + $case2Arr + $case3Arr + $case4Arr + $case5Arr + $case6Arr;
				
				if(count($result) > 0){
					$result = array_slice($result, 0, MAX_CLIENT_NUM_ROWS, false);
					foreach($result AS $data){
						$idClient = $data['id'];
						$client_data = $this->getSingleClientInfosById($idClient, $clientArr);
						if(isset($client_data[0])){
							array_push($clientArr_return, $client_data[0]);
						}
					}
				}
			}
			//return the search array
			return $clientArr_return;
		}

		$arr = array(
			'error' => 1,
			'errormessage' => $this->reg->get('err')->get(108)
			);
		return $arr;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function addNewClient($data){
		//insert a newly created client
		/*
		RECEIVER:
			data:{
				"firstname":"amjad",
				"lastname":"jitan",
				"email":"",
				"locale":"fr_CA"
				}
		SENDER:
			data:{
				0:{
					username:"gutathib",
					id:123456789,
					active:"1",
					qty_try:"0",
					name:"",
					file:"",
					department:"",
					firstname:"amjad",
					lastname:"jitan",
					locale:"fr_CA",
					email:"",
					creation_date:"2011-03-16",
					idUser:"000",
					user_firstname:"",
					user_lastname:"",
					password:"",
					email2:"",
					programs:
					}
				}
		*/
		if(isset($data['firstname']) && isset($data['lastname']) && isset($data['email']) && isset($data['locale'])){
			// validate email
			$email = trim($data['email']);
			$email_empty = empty($email);
			//email empty
			/*if($email_empty){
				$arr = array(
					'error' => 1,
					'errormessage' => $this->reg->get('err')->get(601)
					);
				return $arr;
				}*/
			//email validation
			if(!$email_empty){
				if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
					$arr = array(
						'error' => 1,
						'errormessage' => $this->reg->get('err')->get(602)
						);
					return $arr;
				}
			}
			//validate first name
			if(empty(trim($data['firstname']))){
				$arr = array(
					'error' => 1,
					'errormessage' => $this->reg->get('err')->get(607)
					);
				return $arr;
				}
			// validate last name
			if(empty(trim($data['lastname']))){
				$arr = array(
					'error' => 1,
					'errormessage' => $this->reg->get('err')->get(608)
					);
				return $arr;
				}
			// generate user name
			$client_username = $this->reg->get('utils')->generateRandomStr();
			//loop check for username generation
			while($this->reg->get('utils')->checkUserName($client_username) != 'unique'){
				$client_username = $this->reg->get('utils')->generateRandomStr();
				}
			// collecting necessary info
			$passwordArr = $this->reg->get('utils')->generatePassword();
			$original_password = $passwordArr['original'];
			$encoded_password  = $passwordArr['encoded']; 
			$idUser = $this->reg->get('sess')->get('idUser');
			$licence_data = $this->reg->get('sess')->get('licence');
			$idLicence = $licence_data['idLicence'];
			$clinic_data = $this->reg->get('sess')->get('clinic');
			$idClinic = $clinic_data['idClinic'];
			$creation_date = buildDateTime($this->reg->get('req')->get('time'));
			//
			$user_firstname = $this->reg->get('sess')->get('firstname');
			$user_lastname = $this->reg->get('sess')->get('lastname');
			//
			$query = 'INSERT INTO licence_client (idUser, idLicence, username, password, active, qty_try, idClinic) VALUES ("'.intVal($idUser).'", "'.intVal($idLicence).'", "'.$this->reg->get('db')->escape($client_username).'", "'.$this->reg->get('db')->escape($encoded_password).'", 1, 0,"'.intval($idClinic).'");';
			$rs = $this->reg->get('db')->query($query);
			if($rs){
				// idclient
				$idClient = $rs->insert_id;
				//
				if(is_numeric($idClient)){
					$query = 'INSERT INTO client (idClient, idLicence, idUser, firstname, lastname, locale, email, email_valid, creation_date, active) VALUES ("'.intVal($idClient).'", "'.intVal($idLicence).'", "'.intVal($idUser).'", "'.$this->reg->get('db-ext')->escape($data['firstname']).'", "'.$this->reg->get('db-ext')->escape($data['lastname']).'", "'.$this->reg->get('db-ext')->escape($data['locale']). '", "'.$this->reg->get('db-ext')->escape($email).'", "1", NOW(), "1");';
					$rs = $this->reg->get('db-ext')->query($query);
					if($rs){
						//return client data
						$clientArr = array(
							//'username' => $client_username,
							'id' => $idClient,
							//'active' => 1,
							//'qty_try' => 0,
							//'name' => trim($data['firstname']).' '.trim($data['lastname']),
							//'file' => '',
							//'department' => '',
							'firstname' => trim($data['firstname']),
							'lastname' => trim($data['lastname']),
							'locale' => $data['locale'],
							'email' => $email,
							//'creation_date' => $creation_date,
							//'idUser' => $idUser,
							//'user_firstname' => $user_firstname,
							//'user_lastname' => $user_lastname,
							//'password' => '',
							//'email2' => '',
							'programs' => ''
							);
						//
						//send email is not necessary when creating client profile since it will be send when assigning a program and sending
						// less client confusion
						unset($rs);
						return array($clientArr);
						}
					}
				}
			//eerur de creation du id
			$arr = array(
				'error' => 1,
				'errormessage' => $this->reg->get('err')->get(603)
				);
			return $arr;	
			}
	//error generic	
	$arr = array(
		'error' => 1,
		'errormessage' => $this->reg->get('err')->get(108)
		);
	return $arr;
	}

	//-------------------------------------------------------------------------------------------------------------
	public function modifyClientInfos($data){
		//modify the information of a specific client
		/*
		RECEIVER:
			data:{
				"id":"1454449383",
				"firstname":"amjad",
				"lastname":"jitan",
				"email":"",
				"locale":"fr_CA",
				"rmprograms":"" 
				}
		SENDER:
			data:"1454449383"
		*/
		if(isset($data['id']) && is_numeric($data['id']) && isset($data['firstname']) && isset($data['lastname']) && isset($data['email']) && isset($data['locale'])){
			//check if email is valid 
			$email = trim($data['email']);
			$email_empty = empty($email);
			/*if($email_empty){
				$arr = array(
					'error' => 1,
					'errormessage' => $this->reg->get('err')->get(604)
					);
				return $arr;
				}*/
			if(!$email_empty){
				if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
					$arr = array(
						'error' => 1,
						'errormessage' => $this->reg->get('err')->get(605)
						);
					return $arr;
					}
				}
			//validate first name
			if(empty(trim($data['firstname']))){
				$arr = array(
					'error' => 1,
					'errormessage' => $this->reg->get('err')->get(609)
					);
				return $arr;
				}
			// validate last name
			if(empty(trim($data['lastname']))){
				$arr = array(
					'error' => 1,
					'errormessage' => $this->reg->get('err')->get(610)
					);
				return $arr;
				}
			//
			$idClient  = $data['id'];
			$firstname = trim($data['firstname']);
			$lastname  = trim($data['lastname']);
			$locale    = $data['locale'];

			//removed programs id are split on ","
			$idUser = $this->reg->get('sess')->get('idUser');
			if(isset($data['rmprograms']) && $idUser !== false){
				if($data['rmprograms'].'' != ''){
					$arrProgToRemove = explode(',', $data['rmprograms']);
					if(count($arrProgToRemove)){
						//on supprime les programmes et ce qui vient avec
						foreach($arrProgToRemove as $k=>$v){
							if($v.'' != ''){
								//la table program
								$query = 'DELETE FROM program WHERE idProgram = "'.intVal($v).'" AND idUser = "'.intVal($idUser).'";';
								$this->reg->get('db')->query($query);
								//la table programTmp
								$query = 'DELETE FROM programTmp WHERE idProgram = "'.intVal($v).'" AND idUser = "'.intVal($idUser).'";';
								$this->reg->get('db')->query($query);
								//la table licno pour v2=>v3
								$query = 'DELETE FROM program_idProgramme_licno WHERE idProgram = "'.intVal($v).'";';
								$this->reg->get('db')->query($query);
								}
							}
						}
					}
				}
			//update infos client
			$query = 'UPDATE client SET firstname = "'.$this->reg->get('db-ext')->escape($firstname).'", lastname = "'.$this->reg->get('db-ext')->escape($lastname).'", email = "'.$this->reg->get('db-ext')->escape($email).'", email_valid = "1", locale = "'.$this->reg->get('db-ext')->escape($locale).'" WHERE idClient = "'.intVal($idClient).'";';
			$rs = $this->reg->get('db-ext')->query($query);
			if(isset($rs->affected_rows)){
				return $idClient;
				}
			//
			$arr = array(
				'error' => 1,
				'errormessage' => $this->reg->get('err')->get(606)
				);
			return $arr;
			}
		//
		$arr = array(
			'error' => 1,
			'errormessage' => $this->reg->get('err')->get(606)
			);
		return $arr;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function sendProgramEmail($data){
		//send the program by email, if "saveemail" is true then change the client email in the db
		/*
		RECEIVER:
			data:{
				"clientid":1454449383,
				"programid":1454509303,
				"sendemailto":"",
				"saveemail":true
				}
		SENDER:
			data:{
				ok:"1",
				msg:""
				}
		*/
		//be sure to send the correct reply-to -  amjad
		/*
		$bSaveEmail = false;
		$bError = false;
		//save or not
		if(isTrue($data['saveemail'])){
			$bSaveEmail = true;
			}
		//check email validity
		if(isset($data['sendemailto'])){
			if($data['sendemailto'].'' != ''){
				if(!filter_var($data['sendemailto'], FILTER_VALIDATE_EMAIL)){
					$bError = true;
					}
			}else{
				$bError = true;
				}
		}else{
			$bError = true;
			}
		//check errors
		if($bError){
			$arr = array(
				'ok' => 0,	
				'msg' => $this->reg->get('err')->get(300),
				);
		}else{
			//update the db if needed
			if($bSaveEmail){
				$query = 'UPDATE client SET email = "'.$this->reg->get('db')->escape($data['sendemailto']).'" WHERE idClient = "'.intVal($data['clientid']).'";';
				$this->reg->get('db-ext')->query($query);
				}
			//send OK
			$arr = array(
				'ok' => 1,	
				'msg' => '',
				);	
			}
		//
		return $arr;
		*/
		$arr = array();
		if(isset($data['clientid']) && is_numeric($data['clientid']) && $data['clientid'] > 0 && isset($data['programid']) && is_numeric($data['programid']) && $data['programid'] > 0  && isset($data['sendemailto']) && !empty($data['sendemailto'])){
			// being process 
			$fp = fopen("/var/log/php/smtp.log", "a");
			fwrite($fp, date("Y-m-d H:i:s") . "\tProcess started\n");
			// validate email
			$client_email = trim($data['sendemailto']);
			if(!filter_var($client_email, FILTER_VALIDATE_EMAIL)){
				$arr = array(
				'ok' => 0,	
				'msg' => $this->reg->get('err')->get(300),
				);
				fwrite($fp, date("Y-m-d H:i:s") . "\tInvalid email address: $client_email\n");
				return $arr;
			}
			$idUser		 = $this->reg->get('sess')->get('idUser');
			$user_fullname	 = $this->reg->get('sess')->get('full_name');
			$user_locale	 = $this->reg->get('sess')->get('locale');
			$brand_data	 = $this->reg->get('sess')->get('brand');
			$licence_data	 = $this->reg->get('sess')->get('licence');
			$clinic_data	 = $this->reg->get('sess')->get('clinic');
			$idBrand	 = $brand_data['idBrand'];
			$brand_title	 = $brand_data['brand_title'];
			$brand_email	 = $brand_data['brand_email'];
			$idLicence	 = $licence_data['idLicence'];
			$available_locale= $licence_data['available_locale'];
			$idClinic 	 = $clinic_data['idClinic'];
			$clinic_title	 = $clinic_data['clinic_title'];
			$clinic_email	 = $clinic_data['clinic_email'];
			$clinic_address	 = '';
			$prepend_text    = '';
			$prepend_html    = '';
			$logo            = '';
			$reply_email     = '';
			$email_user1     = '';
			$email_user2     = '';
			$origIdUser	 = 0;
			$program_name	 = decodeString(translate("Untitled"));
			$prepared_name	 = '';
			$prepared_id	 = $idUser;
			$idClient	 =  $data['clientid'];
			$idProgram	 =  $data['programid'];
			$save_email 	 = (isset($data['saveemail']) && ($data['saveemail'] == true|| $data['saveemail'] == 1)) ? true : false; 
			$client_locale	 = 'en_US';
			$exception_idLicence = 5609;
			$oCipher	 = new Cipher(PASS_CYPHER_SALT);
			$licence_logo	 = '';
			$brand_logo	 = '';
			$iframe_url	 = '';
			
			//update email
			if($save_email == true){
				$query = 'UPDATE client SET email = "'.$this->reg->get('db-ext')->escape($client_email).'" WHERE idClient = "'.intVal($idClient).'";';
				$this->reg->get('db-ext')->query($query);
			}
			// collecting client info
			$query_external = 'SELECT locale FROM client WHERE idClient="'.intval($idClient).'";';
			$rs_external = $this->reg->get('db-ext')->query($query_external);
			if ($rs_external && $rs_external->num_rows) {
				$client_locale = $rs_external->row['locale'];
			}
			unset($rs_external);
			
			$query = 'SELECT username, password FROM licence_client WHERE idClient="'.intval($idClient).'";';
			$rs = $this->reg->get('db')->query($query);
			if ($rs && $rs->num_rows) {
				$client_username = $rs->row['username'];
				$client_password = $oCipher->decrypt($rs->row['password']);
			}
			unset($rs);
			#
			# error if fail retrive client data
			#
			// get program data
			$query = 'SELECT idUser, title FROM program WHERE idprogram="'.intval($idProgram).'";';
			$rs = $this->reg->get('db')->query($query);
			if ($rs && $rs->num_rows) {
				$origIdUser = $rs->row['idUser'];
				$tmpProgramArr	 = json_decodeStr($rs->row['title'], true);
				if(!empty($tmpProgramArr)){
					$program_name = isset($tmpProgramArr[$client_locale]) ? decodeString($tmpProgramArr[$client_locale]) : $program_name;
				}
			}
			unset($rs);
			// get clinic data 
			$query     = 'SELECT clinic.signature ' .
				     'FROM clinic ' .
				     'WHERE clinic.idClinic = "' . intval($idClinic) . '";';
			$rs = $this->reg->get('db')->query($query);
			if ($rs && $rs->num_rows) {
				$signature = trim($rs->row['signature']);
				if(!empty($signature)){
					// special case
					$clinic_address  = htmlspecialchars_decode(htmlentities($signature, ENT_NOQUOTES, 'UTF-8', false), ENT_NOQUOTES);
				}
				
			}
			unset($rs);
			if ($idUser !=  $origIdUser) {
				$query = 'SELECT user.firstname, user.lastname ' .
					 'FROM user ' .
					 'WHERE user.idUser = "'. intval($origIdUser) . '";';
				$rs = $this->reg->get('db')->query($query);
				if ($rs && $rs->num_rows) {
					$prepared_id   = $origIdUser;
					$firstname     = decodeString($rs->row['firstname']);
					$lastname      = decodeString($rs->row['lastname']);
					if (!empty($firstname)) {
						$prepared_name = $firstname;
					}
					if (!empty($lastname)) {
						if (!empty($prepared_name)) {
							$prepared_name .= ' ';
						}
						$prepared_name .= $lastname;
					}
				}
			} else {
				$prepared_name = $user_fullname;
			}
			#
			# L'adresse email du client est valide.
			# Recherche du logo à afficher dans la partie HTML du email
			#
			$query = 'SELECT logo, email FROM clinic WHERE idClinic = "' . intval($idClinic) . '";';
			$rs = $this->reg->get('db')->query($query);
			if ($rs && $rs->num_rows) {
				$logo           = $rs->row['logo'];
				$clinic_email   = $rs->row['email'];
			}
			unset($rs);
			if (!empty(trim($logo)) && checkRemoteFile('https://hep....@physiotec.ca/img/logo/' . $logo)) {
				#
				# Le logo existe
				#
				$logo = 'https://hep....@physiotec.ca/img/logo/' . $logo;
			} else {
				#
				# Le logo de la clinique n'existe pas.
				# Vérification du logo de la licence et celui du brand
				#
				$query = 'SELECT licence.logo ' .
					 'FROM licence ' .
					 'WHERE licence.idLicence = "' . intval($idLicence) . '";';
				$rs = $this->reg->get('db')->query($query);
				if ($rs && $rs->num_rows) {
					$licence_logo = $rs->row['logo'];
				}
				unset($rs);
				
				if (!empty(trim($licence_logo)) && checkRemoteFile('https://hep....@physiotec.ca/img/logo/' . $licence_logo)) {
					$logo = 'https://hep....@physiotec.ca/img/logo/' . $licence_logo;
				} else {
					$query = 'SELECT brand.favicon ' .
						 'FROM brand, brand_licence ' .
						 'WHERE brand.idBrand = brand_licence.idBrand ' .
						 'AND brand_licence.idLicence = "' . intval($idLicence) . '";';
					$rs = $this->reg->get('db')->query($query);
					if ($rs && $rs->num_rows) {
						$brand_logo = $rs->row['favicon'];
					}
					unset($rs);
					if (!empty(trim($brand_logo)) && checkRemoteFile('https://hep....@physiotec.ca/img/logo/' . $brand_logo)) {
						$logo = 'https://hep....@physiotec.ca/img/logo/' . $brand_logo;
					} else {
						$logo = '';
					}
				}
			}
			$email_template         = file_get_contents("/var/www/email_template/email.html");
			fwrite($fp, date("Y-m-d H:i:s") . "\tPreparing to send to: $client_email\n");
			
			#
			# Remplacement temporaire de la locale par celle du client
			#
			putenv("LC_ALL=" . $client_locale);
			setlocale(LC_ALL, $client_locale . '.UTF8');
			bindtextdomain('messages', TRANSLATION_BASE_DIR);
			textdomain('messages');
			require_once DIR_INC . 'email_templates.php';
			
			if ($idLicence == $exception_idLicence) {
				$email_template		= $email_template_special_5609;
				$send_program_subject	= $send_program_subject_special_5609;
				$send_program_text	= $send_program_text_special_5609;
				$send_program_html	= $send_program_html_special_5609;
			}
			$send_program_subject = sprintf($send_program_subject, $clinic_title);
			$query = 'SELECT clinic.iframe_url FROM clinic WHERE clinic.idClinic = "' . intval($idClinic) . '";';
			$rs = $this->reg->get('db')->query($query);
			if ($rs && $rs->num_rows) {
				$iframe_url = trim($rs->row['iframe_url']);
			}
			unset($rs);
			if (empty($iframe_url)) {
				$query = 'SELECT licence.iframe_url FROM licence WHERE licence.idLicence = "' . intval($idLicence) . '";';
				$rs = $this->reg->get('db')->query($query);
				if ($rs && $rs->num_rows) {
					$iframe_url = trim($rs->row['iframe_url']);
				}
			}
			unset($rs);
			$query = 'SELECT licence_text.idClinic, licence_text.email_text ' .
				 'FROM licence_text ' .
				 'WHERE licence_text.idLicence = "' . intval($idLicence) . '";';
			$rs = $this->reg->get('db')->query($query);
			if ($rs && $rs->num_rows) {
				$email_text = '';
				foreach ($rs->rows AS $idRow => $row){
					if ($row['idClinic'] == $idClinic) {
						$email_text = $row['email_text'];
					} else if ($row['idClinic'] == 0 && $email_text == '') {
						$email_text = $row['email_text'];
					}
				}
				$jsonArr    = json_decodeStr($email_text, true);
				if (isset($jsonArr[$client_locale])) {
					$prepend_text = decodeString($jsonArr[$client_locale]) . "\n\n";
					$prepend_html = decodeString($jsonArr[$client_locale]);
					$prepend_html = nl2br(encodeString($prepend_html)) . '<br><br>';
					$send_program_text = str_replace("%%prepend%%", $prepend_text, $send_program_text);
					$send_program_html = str_replace("%%prepend%%", $prepend_html, $send_program_html);
				} else {
					#
					# Aucun texte de disponible.  Nous devons faire disparaître le tag "prepend"
					#
					$send_program_text = str_replace("%%prepend%%", '', $send_program_text);
					$send_program_html = str_replace("%%prepend%%", '', $send_program_html);
				}
			} else {
				$send_program_text = str_replace("%%prepend%%", '', $send_program_text);
				$send_program_html = str_replace("%%prepend%%", '', $send_program_html);
			}
			unset($rs);
			$query = 'SELECT user.email_client, user.email1, user.email2 ' .
				 'FROM user ' .
				 'WHERE user.idUser = "' . intval($idUser) . '";';
			$rs = $this->reg->get('db')->query($query);
			if ($rs && $rs->num_rows) {			
				$email_client = $rs->row['email_client'];
				$email_user1  = $rs->row['email1'];
				$email_user2  = $rs->row['email2'];
			}
			unset($rs);
			// verify Reply to email
			if (!empty($email_user1) && filter_var($email_user1, FILTER_VALIDATE_EMAIL)) {
				$reply_email = $email_user1;
			} else if (!empty($email_user2) && filter_var($email_user2, FILTER_VALIDATE_EMAIL)) {
				$reply_email = $email_user2;
			} else if (!empty($clinic_email) && filter_var($clinic_email, FILTER_VALIDATE_EMAIL)) {
				$reply_email = $clinic_email;
			} else {
				if (!empty($brand_email) && filter_var($brand_email, FILTER_VALIDATE_EMAIL)) {
					$reply_email = $brand_email;
				}
			}
			
			if ($iframe_url != "") {
				if (strpos($iframe_url, "?") !== false) {
					$site_url = $iframe_url . '&';
				} else {
					$site_url = $iframe_url . '?';
				}
				$site_url .= 'remove_header=y&first=' . $idClient . '&username=' . urlencode($client_username) . 
					'&password=' . urlencode($client_password) . '&idProgram=' . urlencode($idProgram);
			} else {
				$site_url = PATH_PROGRAM_CLIENT_EMAIL .'?do=patient&action=new_load&directAccess=yes&l=' . 
					$idLicence . '&username=' . urlencode($client_username) . '&password=' . urlencode($client_password) .
					'&idProgram=' . urlencode($idProgram);
			}
			$mc = "MC" . $idLicence . str_pad($idClinic, 6, '0', STR_PAD_LEFT);
			if ($idLicence != $exception_idLicence) {
				$send_program_text = sprintf($send_program_text, $site_url, $program_name,  $client_username, $client_password, 
					translate("Prepared By:") . "\n\n" . $prepared_name, "U$prepared_id  " . $mc);
				$send_program_html = sprintf($send_program_html, $site_url, htmlentities($program_name), htmlentities($client_username), 
					htmlentities($client_password), translate("Prepared By:") . "<br><br>" . $prepared_name, "U$prepared_id", $mc);
			} else {
				$site_url = 'http://www.dptsport.com/Prescription-Exercises/a~7740/article.html?username=' . urlencode($client_username) .
					 '&password=' . urlencode($client_password);
				$mobile_url = 'https://' . $_SERVER["HTTP_HOST"] . '/?do=patient&action=new_load&directAccess=yes&l=' .
					$idLicence . '&username=' . urlencode($client_username) . '&password=' . urlencode($client_password) .
					'&idProgram=' . urlencode($idProgram);
				$send_program_text = sprintf($send_program_text, $site_url, $mobile_url, $program_name,  $client_username, $client_password, 
					$user_fullname, "U$idUser  " . $mc);
				$send_program_html = sprintf($send_program_html, $site_url, $site_url, $mobile_url, $mobile_url, htmlentities($program_name), htmlentities($client_username),
					htmlentities($client_password), $user_fullname, "U$idUser", $mc);
			}
			$send_program_text = str_replace('%clinic_address%', strip_tags($clinic_address), $send_program_text);
			$email_template    = str_replace('%%email_logo%%', $logo, $email_template);
			$email_template    = str_replace('%%email_content%%', $send_program_html, $email_template);
			$email_template    = str_replace('%%clinic_address%%', nl2br($clinic_address), $email_template);
			$send_program_text = html_entity_decode( $send_program_text);
			if($email_client == 'OUTLOOK' ) {
				$send_program_text = (html_entity_decode(str_replace(array("\r\n","\r","\n"),"<br>", $send_program_text)));
				$send_program_text = rawurlencode($send_program_text);
				$send_program_text = str_replace("%3Cbr%3E" , "%0D%0A", $send_program_text);
				$send_program_subject = rawurlencode((html_entity_decode($send_program_subject)));
				
				echo 'var clientEmail ="' . $client_email . '";';
				echo 'var subject = "' . $send_program_subject . '";';
				echo 'var body= "'.$send_program_text.'";';
			
				echo 'window.location.href = "mailto:"+clientEmail+"?subject="+subject+"&body="+body;';
				exit();
			}
			$Message        = new Mail_mime();
			$Message->setTXTBody(utf8_decode($send_program_text));
			$Message->setHTMLBody(utf8_decode($email_template));
			$body = $Message->get();
			$bad_character = array('!', '#', '$', '%', '*', '+', '/', '\\', '=', '?', '^', '`', '{', '|', '}', '~', '`', '(', ')', ',');// keeping double quote and single quote
			$clinic_title = preg_replace("/[^a-z0-9\x{c0}-\x{ff}ÆÇËÀÈÉÍÏÒÓÚÜÁÝÆØÅÄÖÕÐÂÊÎÔÙÛßÃÞÌÑ·ªº¡¿ -._]+/ui", " ", $clinic_title);
			$clinic_title = utf8_decode(str_replace($bad_character,' ', $clinic_title));
			$extraheaders = array(
				'From'    => $clinic_title ." <...@...>",
				'Subject' => utf8_decode($send_program_subject),
				'Reply-To' => $reply_email
			);
			$headers = $Message->headers($extraheaders);
			$smtp    = @Mail::factory('smtp', array(
					'host' => 'smtp.mandrillapp.com',
					'port' => '587',
					'auth' => true,
					'username' => '',
					'password' => ''
					));
			$mail = @$smtp->send($client_email, $headers, $body);
			#
			# Remise en place de la locale de l'utilisateur
			#
			putenv("LC_ALL=" . $user_locale);
			setlocale(LC_ALL, $user_locale . '.UTF8');
			bindtextdomain('messages', TRANSLATION_BASE_DIR);
			textdomain('messages');
			if (@PEAR::isError($mail)) {
				fwrite($fp, date("Y-m-d H:i:s") . "\t" . $client_email . "\t" . $mail->getMessage() . "\n");
				$arr['error']        = 1;
				$arr['errormessage'] = $this->reg->get('err')->get(611);
				return $arr;
			} else {
				fwrite($fp, date("Y-m-d H:i:s") . "\t" . $client_email . "\tMessage successfully sent\n");
				$arr['ok']  = 1;
				$arr['msg'] = '';
				return $arr;
			}
			fwrite($fp, date("Y-m-d H:i:s") . "\tProcess ended\n");
			fclose($fp);
		}
		$arr['error']        = 1;
                $arr['errormessage'] = $this->reg->get('err')->get(108);
                return $arr;
	}

	//-------------------------------------------------------------------------------------------------------------
	public function fetchClientSearchAutoCompleteData($data){
		//listing of the client containing words enter in the input box::input-main-client-search-autocomplete for the autocomplete search
		/*
		RECEIVER:
			data:"na"
		SENDER:
			data:{
				0:{
					id:"888888",
					name:"nadja"
					},
				1:{
					id:"123456",
					firstname:"olivier",
					lastname:"renaldin"
					}
				}
		*/

		
		//this one only check for the first characters
		if(!empty(trim($data)) && is_string($data)){
			$data = preg_split("/\\s/", $data,-1, PREG_SPLIT_NO_EMPTY);
			$clientArr = $this->reg->get('utils')->getUserClients();
			$clientArr_return = array();
			$data1 = isset($data[0]) ? $data[0] : false;
			$data2 = isset($data[1]) ? $data[1] : false;			
			$part1 = safeRegExStr(mb_strtolower($data1, 'UTF-8'));
			$part2 = safeRegExStr(mb_strtolower($data2, 'UTF-8'));
			$name1 = (!empty($data1) && !empty($data2)) ? $data1.' '.$data2 : 
					((!empty($data1) && empty($data2)) ? $data1 : 
						((empty($data1) && !empty($data2)) ? $data2 : false));
			
			$name2 = (!empty($data2) && !empty($data1)) ? $data2.' '.$data1 : 
					((!empty($data2) && empty($data1)) ? $data2 : 
						((empty($data2) && !empty($data1)) ? $data1 : false));
			$case1Arr = array();
			$case2Arr = array(); 
			$case3Arr = array();
			$case4Arr = array();
			$case5Arr = array();
			$case6Arr = array();
			if(count($clientArr) > 0){
				foreach($clientArr AS $idClient => $client_data){
					if(isset($client_data['firstname']) && isset($client_data['lastname']) && isset($client_data['name'])){
						$client_name	  = mb_strtolower(decodeString($client_data['name']), 'UTF-8');
						$orig_client_firstname = decodeString($client_data['firstname']);
						$orig_client_lastname  = decodeString($client_data['lastname']);
						$client_firstname = mb_strtolower($orig_client_firstname, 'UTF-8');
						$client_lastname  = mb_strtolower($orig_client_lastname, 'UTF-8');
						$case1 = !empty($name1) && ($name1 == $client_name) ? true : false;
						$case2 = !empty($name2) && ($name2 == $client_name) ? true : false;
						$case3 = !empty($part1) && preg_match('/^'.$part1.'/', $client_firstname) ? true : false;
						$case4 = !empty($part1) && preg_match('/^'.$part1.'/', $client_lastname) ? true : false;
						$case5 = !empty($part2) && preg_match('/^'.$part2.'/', $client_firstname) ? true : false;
						$case6 = !empty($part2) && preg_match('/^'.$part2.'/', $client_lastname) ? true : false;
						$client_firstname = mb_strtolower($orig_client_firstname, 'UTF-8');
						$client_lastname  = mb_strtolower($orig_client_lastname, 'UTF-8');
						if($case1){
							$case1Arr[$idClient] = array(
										'id' => $idClient,
										'firstname' => $client_firstname,
										'lastname' => $client_lastname
										);
						} else if($case2) {
							$case2Arr[$idClient] = array(
										'id' => $idClient,
										'firstname' => $client_firstname,
										'lastname' => $client_lastname
										);
						} else if($case3) {
							$case3Arr[$idClient] = array(
										'id' => $idClient,
										'firstname' => $client_firstname,
										'lastname' => $client_lastname
										);
						} else if($case4) {
							$case4Arr[$idClient] = array(
										'id' => $idClient,
										'firstname' => $client_firstname,
										'lastname' => $client_lastname
										);
						} else if($case5) {
							$case5Arr[$idClient] = array(
										'id' => $idClient,
										'firstname' => $client_firstname,
										'lastname' => $client_lastname
										);
						} else if($case6) {
							$case6Arr[$idClient] = array(
										'id' => $idClient,
										'firstname' => $client_firstname,
										'lastname' => $client_lastname
										);
						}
					}
				}
				$result = $case1Arr + $case2Arr + $case3Arr + $case4Arr + $case5Arr + $case6Arr;
				if(count($result) > 0){
					$clientArr_return = array_values($result);
					$clientArr_return = array_slice($clientArr_return, 0, MAX_ROWS_AUTOCOMPLETE_RETURNED, false);
				}
			}
			//return the search array
			return $clientArr_return;
		}
		
		$arr = array(
			'error' => 1,
			'errormessage' => $this->reg->get('err')->get(108)
			);	
		return $arr;
	}

	}


//END