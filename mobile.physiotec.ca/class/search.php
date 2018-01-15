<?php
/**
@auth:	"Dwizzel"
@date:	00-00-0000
@info:	SEARCH

@todo:  Pls update getLicenceLocale function licence locales are available at the session

@notes: REGEX TO FIND SPECIAL CHAR IN DB
	SELECT DISTINCT(`keyword`) 
	FROM  `keyword` 
	WHERE  `keyword` REGEXP '^.*(&[#a-zA-Z\d]+;|\bu[\da-fA-F]{4}\b|\b0[xX][0-9a-fA-F]{1,4}\b|\\[a-zA-Z]{1}|\c[A-Z]).*$'
	ORDER BY  `keyword`.`keyword` 

@important:
	1. dwizzel modif, le "data" et "userdata" sont different, "data" est l'original et "userdata" est celui de l'usager
	2. gewt rid of the "level" 
	3. get rid of the image path that will be in the client side "PATH_EXERCICE_IMAGE"
	4. get rid of "drawing"
		
*/


class Search {
	
	private $reg;
	private $bTrace = false;	
	private $className = 'Search';
	
	//------------------------------------------------------------------------
	public function __construct(&$reg) {
		$this->reg = $reg;
	}
	
	//------------------------------------------------------------------------
	public function getClassName(){
		return $this->className;
		}

	//------------------------------------------------------------------------
	public function getClassObject(){
		return $this;
		}
	
	//------------------------------------------------------------------------
	/*
		RECEIVER:
			data:{
					word:"hip",
					module:13 ou -1 si aucune selection
					}
		SENDER:
			data:{
				0:{
					id:"200030",
					idUser:"0",
					idModule:"29",
					category:"3",
					filter:"404",
					data:"{"locale":{"en_US":{"short_title":"EXR-200030","title":"10-H. LATERAL ASSIST - OPPOSITE SIDE","description":"Attach cord to your side and get into a &quot;spread leg&quot; position with tension on cord. Step forward with back leg to cross over other leg, take one more step, walking toward anchor. Reverse motion under controlled conditions and return to starting position. Repeat for required sets and reps. Then repeat bilaterally.","level":""},"fr_CA":{"short_title":"EXR-200030","title":"Assistance lat&eacute;rale, c&ocirc;t&eacute; oppos&eacute;","description":"Attachez un &eacute;lastique sur le cot&eacute; du corps et &eacute;cartez les jambes en gardant une tension sur l'&eacute;lastique. Faites un pas vers le point d'attache de l'&eacute;lastique avec la jambe &eacute;loign&eacute;e pour croiser devant l'autre jambe puis un pas de plus. Revenez en contr&ocirc;le &agrave; la position de d&eacute;part. R&eacute;p&eacute;tez pour le nombre de r&eacute;p&eacute;titions et de s&eacute;ries requises puis effectuez de l'autre c&ocirc;t&eacute;.","level":""}},"picture":[{"pic":"/images/exercices/GEN101442_A.jpg","thumb":"/images/exercices/thumbs/GEN101442_A.jpg"},{"pic":"/images/exercices/GEN101442_B.jpg","thumb":"/images/exercices/thumbs/GEN101442_B.jpg"}],"drawing":[{"pic":"/images/exercices/GEN101442_A.jpg","thumb":"/images/exercices/GEN101442_A.jpg"},{"pic":"/images/exercices/GEN101442_B.jpg","thumb":"/images/exercices/GEN101442_B.jpg"}]}",
					userdata:"{"locale":{"en_US":{"short_title":"EXR-200030","title":"10-H. LATERAL ASSIST - OPPOSITE SIDE","description":"Attach cord to your side and get into a &quot;spread leg&quot; position with tension on cord. Step forward with back leg to cross over other leg, take one more step, walking toward anchor. Reverse motion under controlled conditions and return to starting position. Repeat for required sets and reps. Then repeat bilaterally.","level":""},"fr_CA":{"short_title":"EXR-200030","title":"Assistance lat&eacute;rale, c&ocirc;t&eacute; oppos&eacute;","description":"Attachez un &eacute;lastique sur le cot&eacute; du corps et &eacute;cartez les jambes en gardant une tension sur l'&eacute;lastique. Faites un pas vers le point d'attache de l'&eacute;lastique avec la jambe &eacute;loign&eacute;e pour croiser devant l'autre jambe puis un pas de plus. Revenez en contr&ocirc;le &agrave; la position de d&eacute;part. R&eacute;p&eacute;tez pour le nombre de r&eacute;p&eacute;titions et de s&eacute;ries requises puis effectuez de l'autre c&ocirc;t&eacute;.","level":""}}}",
					codeExercise:"EXR-200030",
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
					}
				}
	*/
	public function getExerciceListingByWord($data){
		//gets the listing of exercices that contains words enter in the input box::input-main-exercice-search-autocomplete
		
		//minor check
		if(isset($data['word']) && $data['word'] != ''){
			//on va passer par un semblant de la classe du site web
			//mais qui n'utilise pas les memes arguments ni 
			//tout a fait la meme maniere de faire les trucs
			require_once(DIR_CLASS.'exsearch.php');
			//instance de kwsearch
			$oExSearch = new ExSearch($this->reg, $data);
			//retour
			return $this->reorderExercise(
				$oExSearch->search($data['word'])
				);
			}
		//
		return array();

		/*		
		if(isset($data['word']) && !empty(trim($data['word']))){
			$arrKeyExercise		= array();
			$word 				= $this->trimKeyword($data['word']);
			$arrRank        	= array();
			$idUser				= $this->reg->get("sess")->get("idUser");
			$licence_data		= $this->reg->get('sess')->get('licence');
			$available_locale 	= $licence_data['available_locale'];
			$idLicence			= $licence_data['idLicence'];
			$clinic_users   	= $this->reg->get('utils')->getClinicUsers();
			$locale				= $this->reg->get("sess")->get("locale");

			//filtre par module	
			if(!isset($data['module']) || (isset($data['module']) && intVal($data['module']) == '-1') ){
				$moduleArr = $this->reg->get('utils')->getClinicModule();
			}else{
				$moduleArr = array(
					intVal($data['module']) => ''
					);
				}
			//imploded arrayKeys, since we use it 2 times
			$strImplodedArrModules = implode(',', array_keys($moduleArr));
			//
			//si on en a des modules on fait la requete
			if(count($moduleArr) > 0){
				//esacpe string for sql
				$word = $this->reg->get("db")->escape($word);

				//on va chercher dans la table des keywords
				$query = 'SELECT keyword_exercise.idExercise, exercise.rank FROM keyword, keyword_exercise, exercise, mod_exercise WHERE (keyword.keyword COLLATE utf8_bin LIKE "'.$word.'" OR keyword.keyword COLLATE utf8_bin LIKE "'.$word.'%" OR keyword.keyword COLLATE utf8_bin LIKE "% '.$word.'" OR keyword.keyword COLLATE utf8_bin LIKE "% '.$word.' %") AND mod_exercise.idModule IN ('.$strImplodedArrModules.') AND keyword.idKeyword = keyword_exercise.idKeyword AND keyword_exercise.idExercise = exercise.idExercise AND keyword_exercise.idExercise = mod_exercise.idExercise ';
				if($locale != "en_US"){
					$query .= 'AND keyword.locale IN ("'.$this->reg->get("db")->escape($locale).'","en_US") ';
				}else{
					$query .= 'AND keyword.locale = "en_US" ';
					}
				$query .= ' AND keyword.idLicence IN (0,'.intval($idLicence).') AND kwtype IN (1,3) ORDER BY exercise.rank ASC;';
				$rs = $this->reg->get("db")->query($query);
				if($rs && $rs->num_rows){		
					//will get DISTINCT key with that
					foreach($rs->rows AS $k=>$v){
						$arrKeyExercise[$v["idExercise"]] = intVal($v["rank"]);
						//si jamais on est rendu
						if(count($arrKeyExercise) >= MAX_SEARCH_NUM_ROWS){
							break;
							}
						}
					}
				unset($rs, $k, $v);
				//on va chercher dans la table des exercices si on en a pas assez
				if(count($arrKeyExercise) < MAX_SEARCH_NUM_ROWS){
					$query = 'SELECT exercise.idExercise, exercise.rank FROM exercise, mod_exercise WHERE ( LCASE(exercise.codeExercise) LIKE "'.$word.'%" OR LCASE(exercise.keywords) LIKE "'.$word.'" OR LCASE(exercise.keywords) LIKE "% '.$word.'" OR LCASE(exercise.keywords) LIKE "'.$word.' %" OR LCASE(exercise.keywords) LIKE "% '.$word.' %") AND exercise.idExercise = mod_exercise.idExercise AND mod_exercise.idModule IN ('.$strImplodedArrModules.') AND ((exercise.idUser = "'.intval($idUser).'") OR (exercise.idUser = 0 && exercise.ready = 1) OR (exercise.idUser IN ('.implode(", ",$clinic_users).') && exercise.ready = 1 && exercise.shared = 1)) ';
					if(count($arrKeyExercise) > 0){
						$query .= ' AND exercise.idExercise NOT IN ('.implode(', ', array_keys($arrKeyExercise)).')';
						}
					$query .=' ORDER BY exercise.rank ASC;';
					//
					//exit($query);
					$rs = $this->reg->get("db")->query($query);
					if($rs && $rs->num_rows){
						foreach($rs->rows AS $k=>$v){
							if(!isset($arrKeyExercise[$v["idExercise"]])){
								$arrKeyExercise[$v["idExercise"]] = intVal($v["rank"]);
								//si jamais on est rendu
								if(count($arrKeyExercise) >= MAX_SEARCH_NUM_ROWS){
									break;
									}
								}
							}
						}
					unset($rs, $k, $v);
					}
				}
			asort($arrKeyExercise);
			//le datat de base
			if(is_array($arrKeyExercise) && count($arrKeyExercise) > 0){
				//imploded arrayKeys, since we use it 4 times
				$strImplodedArrKeys = implode(", ", array_keys($arrKeyExercise));

				//le data et video
				$query = 'SELECT exercise.idExercise, exercise.codeExercise, exercise.data, exercise.idUser, video.host, video.embed_code FROM exercise LEFT JOIN video ON exercise.idExercise = video.idExercise WHERE exercise.idExercise IN ('.$strImplodedArrKeys.');';
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){
					foreach($rs->rows AS $k=>$v){
						if($v['host'] == 'sprout'){
							$video = PATH_VIDEO_SPROUT.$v['embed_code'];
						}else{
							$video = $v['embed_code'];
							}
						$arrKeyExercise[$v['idExercise']] = array(
							'codeExercise'	=> $v['codeExercise'],
							'data'			=> decodeString($v['data']),
							'video'			=> $video,
							'filter'		=> '', //vide pour linstant ca etre rempli plus loin avec une autre requete
							);
						//check pour les "mine exercices"
						if(intVal($v['idUser']) == intVal($idUser)){
							$arrKeyExercise[$v["idExercise"]]['mine'] = 1;
							}

						}
					}
				unset($rs, $k, $v);
				//
				//Ajout du userdata de chaque exercice
				$query = 'SELECT exercise_user.idExercise, exercise_user.data AS user_data FROM exercise_user WHERE exercise_user.idExercise IN ('.$strImplodedArrKeys.') AND exercise_user.idUser = "'.intval($idUser).'";';
				$rs = $this->reg->get("db")->query($query);
				if($rs && $rs->num_rows){
					foreach($rs->rows AS $k=>$v){
						$data = json_decodeStr($v["user_data"]);
						if(!is_array($data)){
							$data = array();
							}
						foreach($available_locale as $locale){
							if(isset($data[$locale]['title'])){
								$data[$locale]['title'] = decodeString($data[$locale]['title']);
								}
							if(isset($data[$locale]['short_title'])){
								$data[$locale]['short_title'] = decodeString($data[$locale]['short_title']);
								}
							if(isset($data[$locale]['description'])){
								$data[$locale]['description'] = decodeString($data[$locale]['description']);
								}
							}
						$arrKeyExercise[$v["idExercise"]]["userdata"] = json_endecodeArr(array('locale'=> $data));
						}
					}
				unset($rs, $k, $v);
				//
				//recherche des filtres pour chaque exercice
				$query = 'SELECT idExercise, idMod_category, idMod_search_filter FROM mod_exercise WHERE idExercise IN ('.$strImplodedArrKeys.') AND idModule IN ('.$strImplodedArrModules.') ORDER BY idMod_category ASC, idExercise ASC, idMod_search_filter ASC;';
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){	
					//conteneur des exercice->cat->filter
					$arrFilterResult = array();
					//ordre de priorite des category, etrange mais c'est ca qui est ca...
					$arrCatPriotity = array(3,4); 
					//les autres suivant on pas d'importance
					for($i=1;$i<50;$i++){
						//maximum category suivante
						array_push($arrCatPriotity, $i);
						}
					//set les filter en string
					foreach($rs->rows AS $k=>$v){
						if(!isset($arrFilterResult[$v['idExercise']])){
							$arrFilterResult[$v['idExercise']] = array();
							}
						if(!isset($arrFilterResult[$v['idExercise']][$v['idMod_category']])){
							$arrFilterResult[$v['idExercise']][$v['idMod_category']] = '';
							}
						$arrFilterResult[$v['idExercise']][$v['idMod_category']] .= $v['idMod_search_filter'].',';
						}
					//on a un listing par exercice->category avec tous les filtres associes
					//loop dans les result key = idExercice
					foreach($arrFilterResult as $k=>$v){
						//on va chercher le cat selon la priorite
						foreach($arrCatPriotity as $k2=>$v2){
							//check si existe
							if(isset($v[$v2])){
								//on push le data filter dans l'exercice
								$arrKeyExercise[$k]['filter'] = $v[$v2];
								break;
								}
							}
						}
					//test
					unset($arrFilterResult, $arrCatPriotity);
					}
				unset($rs, $k, $v);
				//
				//recherche des favoris
				$query = 'SELECT idExercise FROM exercise_favorite WHERE idUser = "'.intval($idUser).'" AND idExercise IN ('.$strImplodedArrKeys.');';
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){
					foreach($rs->rows AS $k=>$v){
						$arrKeyExercise[$v['idExercise']]['fav'] = 1;
						}
					}
				unset($rs, $k, $v);
				//send	
				return $this->reorderExercise($arrKeyExercise);
				}
			//no result
			return array();
			}
		//no result
		return array();
		*/
		}

	//------------------------------------------------------------------------
	public function getSearchTemplateExercicesById($data){
		//gets the exercices of the giving template id
		/*
		RECEIVER:
			data:"1002"
		SENDER:
			data:{
				id:"1002",
				name:"TEMPLATE 1002",
				module:"2",
				notes:"...",
				exercices:{
					200004:{
						id:"200001",
						idUser:"0",
						idModule:"29",
						category:"3",
						filter:"404",
						data:"{"locale":{"en_US":{"short_title":"EXR-200001","title":"10-H. LATERAL ASSIST - OPPOSITE SIDE","description":"Attach cord to your side and get into a &quot;spread leg&quot; position with tension on cord. Step forward with back leg to cross over other leg, take one more step, walking toward anchor. Reverse motion under controlled conditions and return to starting position. Repeat for required sets and reps. Then repeat bilaterally.","level":""},"fr_CA":{"short_title":"EXR-200001","title":"Assistance lat&eacute;rale, c&ocirc;t&eacute; oppos&eacute;","description":"Attachez un &eacute;lastique sur le cot&eacute; du corps et &eacute;cartez les jambes en gardant une tension sur l'&eacute;lastique. Faites un pas vers le point d'attache de l'&eacute;lastique avec la jambe &eacute;loign&eacute;e pour croiser devant l'autre jambe puis un pas de plus. Revenez en contr&ocirc;le &agrave; la position de d&eacute;part. R&eacute;p&eacute;tez pour le nombre de r&eacute;p&eacute;titions et de s&eacute;ries requises puis effectuez de l'autre c&ocirc;t&eacute;.","level":""}},"picture":[{"pic":"/images/exercices/GEN101442_A.jpg","thumb":"/images/exercices/thumbs/GEN101442_A.jpg"},{"pic":"/images/exercices/GEN101442_B.jpg","thumb":"/images/exercices/thumbs/GEN101442_B.jpg"}],"drawing":[{"pic":"/images/exercices/GEN101442_A.jpg","thumb":"/images/exercices/GEN101442_A.jpg"},{"pic":"/images/exercices/GEN101442_B.jpg","thumb":"/images/exercices/GEN101442_B.jpg"}]}",
						codeExercise:"EXR-200001",
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
					200005:{
						id:"200002",
						idUser:"0",
						idModule:"29",
						category:"3",
						filter:"404",
						data:"{"locale":{"en_US":{"short_title":"EXR-200002","title":"10-H. LATERAL ASSIST - OPPOSITE SIDE","description":"Attach cord to your side and get into a &quot;spread leg&quot; position with tension on cord. Step forward with back leg to cross over other leg, take one more step, walking toward anchor. Reverse motion under controlled conditions and return to starting position. Repeat for required sets and reps. Then repeat bilaterally.","level":""},"fr_CA":{"short_title":"EXR-200002","title":"Assistance lat&eacute;rale, c&ocirc;t&eacute; oppos&eacute;","description":"Attachez un &eacute;lastique sur le cot&eacute; du corps et &eacute;cartez les jambes en gardant une tension sur l'&eacute;lastique. Faites un pas vers le point d'attache de l'&eacute;lastique avec la jambe &eacute;loign&eacute;e pour croiser devant l'autre jambe puis un pas de plus. Revenez en contr&ocirc;le &agrave; la position de d&eacute;part. R&eacute;p&eacute;tez pour le nombre de r&eacute;p&eacute;titions et de s&eacute;ries requises puis effectuez de l'autre c&ocirc;t&eacute;.","level":""}},"picture":[{"pic":"/images/exercices/GEN101442_A.jpg","thumb":"/images/exercices/thumbs/GEN101442_A.jpg"},{"pic":"/images/exercices/GEN101442_B.jpg","thumb":"/images/exercices/thumbs/GEN101442_B.jpg"}],"drawing":[{"pic":"/images/exercices/GEN101442_A.jpg","thumb":"/images/exercices/GEN101442_A.jpg"},{"pic":"/images/exercices/GEN101442_B.jpg","thumb":"/images/exercices/GEN101442_B.jpg"}]}",
						codeExercise:"EXR-200002",
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
						}
					}
				}
		*/
		$arr = array();
		if(isset($data) && !empty(trim($data)) && is_numeric($data) && $data > 0 ){
			$idProtocol = $data;
			$idUser	= $this->reg->get("sess")->get("idUser");
			$locale	= $this->reg->get("sess")->get("locale");
			$brand_data = $this->reg->get("sess")->get("brand");
			$idBrand = $brand_data['idBrand'];
			$licence_data = $this->reg->get("sess")->get("licence");
			$idLicence = $licence_data['idLicence'];
			$available_locale = $licence_data['available_locale'];
			//
			$clinic_users = $this->reg->get('utils')->getClinicUsers();
			//
			$query = 'SELECT protocol.idPrint_size, protocol.idUser, protocol.idBrand, protocol.idLicence, protocol.idClinic, protocol.title, protocol.note, protocol.creation_date, protocol.last_update, protocol.data, protocol_module.idModule, protocol.idProtocol FROM protocol, protocol_module WHERE protocol.idProtocol = '.intval($idProtocol).' AND protocol.idProtocol = protocol_module.idProtocol  AND ((protocol.idUser = '.intval($idUser).') OR (protocol.idBrand = '.intval($idBrand).' AND protocol.idLicence = 0) OR (protocol.idBrand = '.intval($idBrand).' AND protocol.idLicence = '.intval($idLicence).' AND protocol.idUser IN ('.implode(", ", ($clinic_users)) . ')));';
			$rs = $this->reg->get("db")->query($query);
			if($rs && $rs->num_rows){
				$row = $rs->row;
				unset($rs);
				$idPrint_size     = $row["idPrint_size"];
				$idUsertmp        = $row["idUser"];
				$idBrand          = $row["idBrand"];
				$idLicence        = $row["idLicence"];
				$idClinic         = $row["idClinic"];
				$title            = $row["title"];
				$note             = $row["note"];
				$creation_date    = $row["creation_date"];
				$last_update      = $row["last_update"];
				$data             = $row["data"];
				$idModule         = $row["idModule"];
				$idProtocol       = $row["idProtocol"];
				$arrTitle         = json_decodeStr($title);
				$arrData          = json_decodeStr($data);
				$arrNote          = json_decodeStr($note);
				if(!is_array($arrData)){
					$arrData = array();
				}
				if(isset($arrTitle[$locale]) && !empty(trim($arrTitle[$locale]))){
					$title = $arrTitle[$locale];
				}else if (isset($arrTitle["en_US"]) && !empty(trim($arrTitle["en_US"]))){
					$title = $arrTitle["en_US"];
				}else{
					$title = translate('untitled');
				}
				
				if(isset($arrNote[$locale])){
					$note = $arrNote[$locale];
				}else if (isset($arrNote["en_US"])){
					$note = $arrNote["en_US"];
				}else{
					$note = "";
				}
				$arr["id"]        = $idProtocol;
				$arr["name"]      = decodeString($title);
				$arr["module"]    = $idModule;
				$arr["notes"]     = decodeString($note);
				$arr["exercices"] = array();
				$arrExercise      = array();
				reset($arrData);
				while (list($idExercise, $valExercise) = each($arrData)){
					/*
					dwizzel explanations :
						[userdata]	= les instructions de la table 'exercise_user'
						[programdata]	= les instructions du programme, soit celle de la table 'protocole'
						[data]		= les instruction original de la table 'exercice' et les photos de la table 'protocole'
					*/
					$arrThisData = $valExercise["locale"];
					$newArrThisData = array();
					$newArrThisData["locale"]  = $arrThisData;
					$newArrThisData["picture"] = $valExercise["picture"];
					//$newArrThisData["drawing"] = $valExercise["drawing"];
					$arrExercise[$idExercise] = array(
						"codeExercise"		=> $valExercise["codeExercise"],
						"data"				=> "", //we need it
						"programdata"		=> "", //we need it
						"video"				=> "",
						"settings"			=> "",
						);
					//PROGDATA
					//rajouter le programdata, juste les locales que l'on trouve dans la table 'protocole'
					$tmpProgramData = array(
						'locale'=>array_fill_keys(
							$available_locale, array(
								'title' => '', 
								'short_title' => '', 
								'description' => ''
								)
							)
						);
					//on va chercher les locales uniquement pour le programdata
					foreach($available_locale as $k=>$v){
						if(isset($newArrThisData['locale'][$v])){
							$tmpProgramData['locale'][$v] = array(
								'short_title' => decodeString($newArrThisData['locale'][$v]['short_title']),
								'title' => decodeString($newArrThisData['locale'][$v]['title']),	
								'description' => decodeString($newArrThisData['locale'][$v]['description']),
								);
							//mettre par defaut les settings in english pour le main settings pas par lang
							if($v == 'en_US'){
								if(	isset($newArrThisData['locale'][$v]['sets']) && 
									isset($newArrThisData['locale'][$v]['repetition']) &&	
									isset($newArrThisData['locale'][$v]['hold']) &&	
									isset($newArrThisData['locale'][$v]['weight']) &&	
									isset($newArrThisData['locale'][$v]['tempo']) &&	
									isset($newArrThisData['locale'][$v]['rest']) &&	
									isset($newArrThisData['locale'][$v]['frequency']) &&	
									isset($newArrThisData['locale'][$v]['duration'])){
									$arrExercise[$idExercise]["settings"] = array(
										"sets" => decodeString($newArrThisData['locale'][$v]['sets']),
										"repetition" => decodeString($newArrThisData['locale'][$v]['repetition']),
										"hold" => decodeString($newArrThisData['locale'][$v]['hold']),
										"weight" => decodeString($newArrThisData['locale'][$v]['weight']),
										"tempo" => decodeString($newArrThisData['locale'][$v]['tempo']),
										"rest" => decodeString($newArrThisData['locale'][$v]['rest']),
										"frequency" => decodeString($newArrThisData['locale'][$v]['frequency']),
										"duration" => decodeString($newArrThisData['locale'][$v]['duration']),
										);
								}else{
									$arrExercise[$idExercise]["settings"] = array(
										"sets" => 0,
										"repetition" => 0,
										"hold" => 0,
										"weight" => 0,
										"tempo" => 0,
										"rest" => 0,
										"frequency" => 0,
										"duration" => 0,
										);
									}
								}
							}
						}
					//on insere le programdata et encode en json
					$arrExercise[$idExercise]['programdata'] = json_endecodeArr($tmpProgramData);
					
					//DATA
					//rajouter les infos du data c'est à dire les images de la table 'protocole' et les infos de la table 'exercice'
					$tmpData = array(
						'locale' => array_fill_keys(
							$available_locale, array(
								'title' => '', 
								'short_title' => '', 
								'description' => ''
								)
							),
						'picture' => $newArrThisData['picture'],	
						);
					$query = 'SELECT exercise.data AS data FROM exercise WHERE exercise.idExercise = "'.intVal($idExercise).'" LIMIT 0,1;';
					$rs = $this->reg->get("db")->query($query);
					if($rs && $rs->num_rows){
						$arrDataOriginal = json_decodeStr($rs->row['data']);
						foreach($available_locale as $k=>$v){
							if(isset($arrDataOriginal['locale'][$v])){
								$tmpData['locale'][$v] = array(
									'short_title' => decodeString($arrDataOriginal['locale'][$v]['short_title']),
									'title' => decodeString($arrDataOriginal['locale'][$v]['title']),	
									'description' => decodeString($arrDataOriginal['locale'][$v]['description'])
									);
								}
							}
						}
					unset($rs);		
					//on insere le data et encode en json
					$arrExercise[$idExercise]['data'] = json_endecodeArr($tmpData);
				}
				if(count($arrExercise) > 0 ){
					//USERDATA
					//on va chercher les userdata juste les locales
					$query = 'SELECT exercise_user.idExercise, exercise_user.data AS user_data FROM exercise_user WHERE exercise_user.idExercise IN ('.implode(", ",array_keys($arrExercise)).') AND exercise_user.idUser = "'.intval($idUser).'";';
					$rs = $this->reg->get("db")->query($query);
					if($rs && $rs->num_rows){
						foreach($rs->rows AS $k=>$v){
							$user_data = array();
							$idExercise = $v["idExercise"];
							$data = json_decodeStr($v["user_data"]);
							foreach($available_locale as $locale){
								if(isset($data[$locale]['title'])){
									$user_data[$locale]['title'] = decodeString($data[$locale]['title']);
								}
								if(isset($data[$locale]['short_title'])){
									$user_data[$locale]['short_title'] = decodeString($data[$locale]['short_title']);
								}
								if(isset($data[$locale]['description'])){
									$user_data[$locale]['description'] = decodeString($data[$locale]['description']);
								}
							}
							$arrExercise[$idExercise]["userdata"] = json_endecodeArr(array('locale'=> $user_data));
						}
					}
					unset($rs);
					//		
					$newArrExercise = array();
					$arrExercise = $this->reorderExercise($arrExercise);
					for($i = 0; $i < count($arrExercise); $i ++){
						$newArrExercise[$arrExercise[$i]["id"]] = $arrExercise[$i];
					}
					$arrExercise = $newArrExercise;
					$query = 'SELECT video.idExercise, video.host, video.embed_code FROM video WHERE video.idExercise IN ('.implode(", ", array_keys($arrExercise)).');';
					$rs = $this->reg->get("db")->query($query);
					if($rs && $rs->num_rows){
						foreach($rs->rows AS $k=>$v){
							if($v["host"] == "sprout"){
								$arrExercise[$v["idExercise"]]["video"] = PATH_VIDEO_SPROUT.$v["embed_code"];
							}else{
								$arrExercise[$v["idExercise"]]["video"] = $v["embed_code"];
								}	
							}
					}
					unset($rs);	
					//	
					$arr["exercices"] = $arrExercise;
					//this is a patch so we can go by count instead of id, sorting problems in javascript side when showing exercice
					$arrExerciceCountSort = array();
					foreach($arr["exercices"] as $k=>$v){
						array_push($arrExerciceCountSort, $v);
						}
					$arr["exercices"] = $arrExerciceCountSort;
				}
			}
			//print_r($arr).EOL;
			//
			return $arr;
		}
		$arr = array(	
			'error' => 1,
			'errormessage' => $this->reg->get('err')->get(108)
			);
		return $arr;
	}

	//------------------------------------------------------------------------
	public function setHasMyInstruction($data){
		//set instructions of a specific exercice from the Programs layer of the Appz
		/*
		RECEIVER:
			data:{
				"exerciceid":"200000",
				"flip":1,
				"mirror":1,
				"locale":{
					"locale":{
						"en_US":{
							"description":"Attach cord to your side and get into a \"spread leg\" position with tension on cord.",
							"short_title":"EXR-200000",
							"level":""
							},
						"fr_CA":{
							"description":"Attachez un élastique sur le coté du corps et écartez les jambes en gardant une tension sur l'élastique.",
							"short_title":"EXR-200000",
							"level":""
							}
						}
					}
				}
		SENDER:
			data:"1"
		*/
		$arr	= array();
		
		if (count($data) && intVal($data["exerciceid"]) > 0) {
			$idUser	    = $this->reg->get("sess")->get("idUser");
			$idExercise = $data["exerciceid"];
			$query      = 'SELECT exercise.codeExercise, exercise.data FROM exercise WHERE exercise.idExercise = '.intval($idExercise).';';
			$rs    = $this->reg->get("db")->query($query);
			if ($rs && $rs->num_rows) {
				$row   = $rs->row;
				$code  = $row["codeExercise"];
				$json  = $row["data"];
				$array = array();	
				$array = json_decodeStr($json);
				if(isset($array["locale"])){
					$array = $array["locale"];
					}	
				$arrkw = array();
				/**********************************/
				#
				# Step 2: Remplacement des titres courts et descriptions
				#
				reset($data["locale"]["locale"]);
				while (list($key, $val) = each($data["locale"]["locale"])) {
					$title = '';
					$short_title = '';
					$description = '';
					if (isset($val["short_title"])) {
						$short_title = encodeString($val["short_title"]);
					} else if (isset($val["title"])) {
						$short_title = encodeString($val["title"]);
					}
					if (isset($val["description"])) {
						$description = encodeString($val["description"]);
					}
					if (isset($array[$key]["title"])) {
						unset($array[$key]["title"]);
					}
					if (isset($array[$key]["level"])) {
						unset($array[$key]["level"]);
					}
					$array[$key]["short_title"] = $short_title;
					$array[$key]["description"] = $description;
				}
				#
				# Step 3: Recherche des mots clés
				#
				reset($array);
				while (list($key, $val) = each($array)) {
					$short_title = $val["short_title"];
					$tmp = explode(" ", $short_title);
					for ($i = 0; $i < count($tmp); $i ++) {
						$tmp[$i] = decodeString($tmp[$i]);
						$kw = mb_strtolower(encodeString($tmp[$i]), 'UTF-8');
						if ($kw != "" && !isset($arrkw[$kw])) {
							$arrkw[$kw] = 1;
						}
					}
				}
				#
				# Step 4: Sauvegarde des infos de l'exercise modifiés par l'usager.
				#
				$json  = json_endecodeArr($array);
				$arrkw = array_keys($arrkw);
				$query = 'REPLACE INTO exercise_user(idExercise, codeExercise, idUser, data, keywords, datetime) ' .
				         'VALUES(' .
				         intval($idExercise) . ', ' .
				         '"' . $this->reg->get("db")->escape($code) . '", ' .
				         intval($idUser) . ', ' .
				         '"' . str_replace('"', '\"', $json) . '", ' .
				         '"' . $this->reg->get("db")->escape(implode(" ", $arrkw)) . '", ' .
				         'NOW()' .
				         ');';
				$rs = $this->reg->get("db")->query($query);
				if(isset($rs->affected_rows)){
					return 1;
				}
				$arr = array(
					   'error' => 1,
					   'errormessage' => $this->reg->get('err')->get(801)
					);
				return $arr;
			}
			$arr = array(
				'error' => 1,
				'errormessage' => $this->reg->get('err')->get(800)
				);
			return $arr;
		}
		$arr = array(
			'error' => 1,
			'errormessage' => $this->reg->get('err')->get(108)
			);
		return $arr;
	}

	//------------------------------------------------------------------------
	public function saveExerciceModifications($data){
		//save exercices modifications, not from Layer Programs but from Layer Search
		/*
		RECEIVER:
			data:{
				"exerciceid":"200000",
				"flip":1,
				"mirror":1,
				"locale":{
					"locale":{
						"en_US":{
							"description":"Attach cord to your side and get into a \"spread leg\" position with tension on cord.",
							"short_title":"EXR-200000",
							"level":""
							},
						"fr_CA":{
							"description":"Attachez un élastique sur le coté du corps et écartez les jambes en gardant une tension sur l'élastique.",
							"short_title":"EXR-200000",
							"level":""
							}
						}
					}
				}
		SENDER:
			data:"1"
		*/
		return $this->setHasMyInstruction($data);
		}

	//------------------------------------------------------------------------
	public function getSearchModules($data){
		//get the templates to fill in the template select box::search-select-template from the serach exercise page
		//for now we show them all but it will have to be classified by : 'mine','all','license','brand'	
		/*
		RECEIVER:
			data:''
		SENDER:
			data:{
				module:{
					0:{
						id: 1,
						name: 'airostie'
						}
					}
				}
		*/
		$arr = array();
		$arrModule = $this->reg->get('utils')->getClinicModule();
		if(count($arrModule) > 0){
			natcasesort($arrModule);
			foreach($arrModule as $k=>$v){
				array_push($arr, array(
					'id' => $k,
					'name' => translate($v),
					));
				}
		}else{
			$arr = array(
				'error' => 1,
				'errormessage' => '',
				);
			return $arr;
			}
		//
		return $arr;
		}

	//------------------------------------------------------------------------
	public function getSearchTemplates($data){
		//get the templates to fill in the template select box::search-select-template from the serach exercise page
		//for now we show them all but it will have to be classified by : 'mine','all','license','brand'	
		/*

		RECEIVER:
			data:{
				"userid":"0000012"
				}
		SENDER:

			[ANCIENNE VERSION]

				data:{
					mine:{
						1001:"TEMPLATE 1001 mine",
						},
					all:{
						1002:"TEMPLATE 1002 all",
						},
					license:{
						1003:"TEMPLATE 1003 license",
						},
					brand:{
						1004:"TEMPLATE 1004 brand"
						}
					keys:{
						mine:{
							0: 1001
							},
						all:{
							0: 1002
							},
						license:{
							0: 1003
							},
						brand:{
							0: 1004
							}
						}
					}

			[NOUVELLE VERSION 22-08-2016]
				
				data:{
					select:{
						mine:{
							1001:"TEMPLATE 1001 mine",
							},
						all:{
							1002:"TEMPLATE 1002 all",
							},
						license:{
							1003:"TEMPLATE 1003 license",
							},
						brand:{
							1004:"TEMPLATE 1004 brand",
							1005:"TEMPLATE 1005 brand"
							}
						},
					titles:{
						mine:"Les miens",
						all:"Tous",
						license:"Physiotec Entr.",
						brand:"Physiotec"
						},
					keys:{
						mine:{
							0: 1001
							},
						all:{
							0: 1002
							},
						license:{
							0: 1003
							},
						brand:{
							0: 1004,
							1: 1005
							}
						}
					}

		*/	
		$idUser	= $this->reg->get('sess')->get('idUser');
		$locale	= $this->reg->get('sess')->get('locale');
		$brand_data = $this->reg->get('sess')->get('brand');
		$idBrand = $brand_data['idBrand'];
		$licence_data = $this->reg->get('sess')->get('licence');
		$idLicence = $licence_data['idLicence'];
		$clinic_users = $this->reg->get('utils')->getClinicUsers();
		//title du brand
		$strBrandTitle = 'Physiotec';
		if(isset($brand_data['brand_title'])){
			$strBrandTitle = $brand_data['brand_title'];
			}
		// just retreive the protocols within the modules of the selected clinic
		$moduleArr = array_keys($this->reg->get('utils')->getClinicModule());
		//basic arrays key
		$arr = array(
			//les textes des select
			'select' => array(
				'mine' => array(),
				'brand' => array(),
				'all' => array(),
				'license' => array()
				),
			//les pos avec key ref des select
			'keys' => array(
				'mine' => array(),
				'brand' => array(),
				'all' => array(),
				'license' => array()
				),
			//les title de chaque select
			'titles' => array(
				'mine' => translate('Mine'),
				'brand' => $strBrandTitle,
				'all' => translate($this->reg->get('utils')->getClinicOptionTitle()),
				'license' => $this->reg->get('utils')->getLicenceOptionTitle()
				),
			);	
		$arrSelect = array(
			'mine' => array(),
			'all' => array(),
			'license' => array(),
			'brand' => array(),	
			);
		//
		$query = 'SELECT protocol.idProtocol, protocol.idUser, protocol.idBrand, protocol.idLicence, protocol.idClinic, protocol.title FROM protocol, protocol_module WHERE protocol.idProtocol = protocol_module.idProtocol AND protocol_module.idModule IN ('.implode(', ', $moduleArr).') AND ((protocol.idUser = '.intval($idUser).') OR (protocol.idBrand = '.intval($idBrand).' AND protocol.idLicence = 0) OR (protocol.idBrand = '.intval($idBrand).' AND protocol.idLicence = '.intval($idLicence).' AND  protocol.idUser IN ('.implode(', ', ($clinic_users)).')));';
		//flip ??
		$clinic_users = array_flip($clinic_users);
		//qeury
		$rs = $this->reg->get('db')->query($query);	
		//minor check
		if($rs && $rs->num_rows){
			foreach($rs->rows as $k=>$v){
				$idProtocol = $v['idProtocol'];
				$idUsertmp = $v['idUser'];
				$idBrandtmp = $v['idBrand'];
				$idLicencetmp = $v['idLicence'];
				$idClinictmp = $v['idClinic'];
				$title = $v['title'];
				$arrTitle = json_decodeStr($title);
				if(isset($arrTitle[$locale]) && trim($arrTitle[$locale]) != ''){
					$title = $arrTitle[$locale];
				}else if (isset($arrTitle['en_US']) && trim($arrTitle['en_US']) != ''){
					$title = $arrTitle['en_US'];
				}else{
					$title = translate('Untitled');
					}
				if($idUsertmp == $idUser){
					$arrSelect['mine'][$idProtocol] = decodeString($title).' ['.$idProtocol.']';
					}
				if($idBrandtmp ==  $idBrand && $idLicencetmp == 0){
					$arrSelect['brand'][$idProtocol] = decodeString($title).' ['.$idProtocol.']';
					}
				if($idBrandtmp ==  $idBrand && $idLicencetmp != 0 && $idClinictmp == 0){
					$arrSelect['license'][$idProtocol] = decodeString($title).' ['.$idProtocol.']';
					}
				// all users of the clinic but not the brand and users for other clinics are not included
				if(isset($clinic_users[$idUsertmp])){
					$arrSelect['all'][$idProtocol] = decodeString($title).' ['.$idProtocol.']';
					}
				}	
			//we are going to sort alphabetical order case insensitive
			natcasesort($arrSelect['mine']);
			natcasesort($arrSelect['brand']);
			natcasesort($arrSelect['license']);
			natcasesort($arrSelect['all']);
			//loop
			foreach($arr['keys'] as $k=>$v){
				foreach($arrSelect[$k] as $k2=>$v2){
					//les keys
					array_push($arr['keys'][$k], $k2);
					//les select
					array_push($arr['select'][$k], $v2);
					}
				}
			}
		if($this->bTrace){
			print_r($arr);
			}
		//send
		return $arr;
		}

	//------------------------------------------------------------------------
	/*
		RECEIVER:
			data:{
					word:"hip",
					module:13 ou -1 si aucune selection
					}
		SENDER:
			data:{
				0:{
					id:"200003",
					name:"EXR-200003"
					},
				1:{
					id:"200030",
					name:"EXR-200030"
					},
				2:{
					id:"200031",
					name:"EXR-200031"
					},
				3:{
					id:"200032",
					name:"EXR-200032"
					},
				4:{
					id:"200033",
					name:"EXR-200033"
					}
				}
	*/	
	public function fetchExerciceSearchAutoCompleteData($data){
		//
		$arr = array();
		//minor check
		if(isset($data['word']) && $data['word'] != ''){
			//on va passer par un semblant de la classe du site web
			//mais qui n'utilise pas les memes arguments ni 
			//tout a fait la meme maniere de faire les trucs
			require_once(DIR_CLASS.'kwsearch.php');
			//instance de kwsearch
			$oKwSearch = new KwSearch($this->reg, $data);
			//retour
			$arr = $oKwSearch->search($data['word'], 'hard');
			}
		//
		return $arr;
				
		

		/*
		//get the data from word search in the input box::input-main-exercice-search-autocomplete
		$arr    = array();
		if(isset($data['word']) && !empty(trim($data['word']))){
			$locale	= $this->reg->get('sess')->get('locale');
			$licence_data = $this->reg->get('sess')->get('licence');
			$idLicence = $licence_data['idLicence'];
			$word = $this->trimKeyword($data['word']);
			if(!isset($data['module']) || (isset($data['module']) && intVal($data['module']) == '-1') ){
				$moduleArr = $this->reg->get('utils')->getClinicModule();
			}else{
				$moduleArr = array(
					intVal($data['module']) => ''
					);
				}
			$query = 'SELECT keyword.keyword, keyword.idKeyword FROM keyword_exercise, mod_exercise, keyword, keyword_rank WHERE keyword.idKeyword = keyword_rank.idKeyword AND mod_exercise.idModule IN ('.implode(", ", array_keys($moduleArr)).') AND keyword.idLicence IN (0, '.intval($idLicence).') AND keyword.keyword COLLATE utf8_bin LIKE "'.$this->reg->get('db')->escape($word).'%" AND keyword.kwtype IN (1,3) AND keyword.idKeyword = keyword_exercise.idKeyword AND keyword_exercise.idExercise = mod_exercise.idExercise ';
			//
			if($locale != "en_US"){
				$query .= 'AND keyword.locale IN ("'.$this->reg->get("db")->escape($locale).'", "en_US") ';
			}else{
				$query .= 'AND keyword.locale = "en_US" ';
				}
			$query .= 'AND keyword_rank.locale = "'.$this->reg->get("db")->escape($locale).'" GROUP BY keyword.keyword ORDER BY keyword_rank.rank DESC, keyword.keyword ASC LIMIT 0,'.MAX_ROWS_AUTOCOMPLETE_RETURNED.';';
			$rs = $this->reg->get('db')->query($query);
			if($rs && $rs->num_rows){
				foreach($rs->rows as $k=>$v){
					array_push($arr, array(
						'id'   => $v['idKeyword'],
						'name' => encodeString(mb_strtolower($v['keyword'], 'UTF-8')),
						));
					}
				}
			unset($rs, $k, $v);
			}
		return $arr;
		*/
		}
		
	//------------------------------------------------------------------------
	public function getSearchFiltersName($data){
		/*
		RECEIVER:
			data:{
				"filter":"61,390,706,1100,4228,5045,5157,5450,5513,5635,5718,5777,5782,5798"
				}
		SENDER:
			data:{
				filter:{
					61:"filt 61",
					390:"filt 390",
					},
				
				}
		*/
	
		$arr = array(
			'filter' => array(),
			);

		
		//get the filter name
		if(isset($data['filter']) && strlen($data['filter']) > 0){	
			$query = 'SELECT idMod_search_filter AS "id", title AS "title" FROM mod_search_filter WHERE idMod_search_filter IN ('.$this->reg->get("db")->escape($data['filter']).') ORDER BY title ASC;';
			$rs = $this->reg->get('db')->query($query);	
			if($rs && $rs->num_rows){
				foreach($rs->rows as $k=>$v){
					array_push($arr['filter'], array(
						'id' => $v['id'],
						'name' => translate($v['title']),
						));	
					}
				}
			unset($rs);	
			}
		return $arr;
		}

	//------------------------------------------------------------------------
	private function reorderExercise($arrExercise = array()) {
		$cpt     			= 0;
		$array   			= array();
		$jsonArr 			= array();
		$oCipher 			= new Cipher(PASS_CYPHER_SALT);
		$licence_data	  	= $this->reg->get('sess')->get('licence');
		$available_locale 	= $licence_data['available_locale'];
		$idLicence	  		= $licence_data['idLicence'];
		reset($arrExercise);
		while (list($idExercise, $val_exercise) = each($arrExercise)) {
			$bSkipSettingLang = true; //pas besoin des par langue a part si ils sont setter
			unset($jsonArr);
			unset($dataArr);
			$dataArr = json_decodeStr($val_exercise["data"]);
			$array[$cpt] = array();
			$array[$cpt]["id"]            = $idExercise;
			//la string de filtre
			if(isset($val_exercise["filter"])){
				$array[$cpt]["filter"]        = $val_exercise["filter"];
				}
			//favorite ou pas
			if(isset($val_exercise["fav"])){
				$array[$cpt]["fav"]        = $val_exercise["fav"];
				}
			//mon exercise ou pas
			if(isset($val_exercise["mine"])){
				$array[$cpt]["mine"]        = $val_exercise["mine"];
				}
			//
			$array[$cpt]["data"]          = "";
			$array[$cpt]["codeExercise"]  = $val_exercise["codeExercise"];
			$array[$cpt]["video"]         = $val_exercise["video"];
			$array[$cpt]["flip"]          = 0;
			$array[$cpt]["mirror"]        = 0;

			//mains settings
			if(isset($val_exercise["settings"]) && is_array($val_exercise["settings"])){
				$array[$cpt]["settings"] = $val_exercise["settings"];
				}
			//settings by lang
			if(isset($val_exercise["settings-lang"]) && is_array($val_exercise["settings-lang"])){
				$array[$cpt]["settings-lang"] = $val_exercise["settings-lang"];
				$bSkipSettingLang = true;
			}
			//
			foreach($available_locale AS $key){
				#
				# Reconstruction du data de l'exercice.
				#
				if (isset($dataArr["locale"][$key]["short_title"])) {
					$jsonArr["locale"][$key]["short_title"] = (decodeString($dataArr["locale"][$key]["short_title"]));
				} else if (isset($dataArr["locale"][$key]["title"])) {
					$jsonArr["locale"][$key]["short_title"] = (decodeString($dataArr["locale"][$key]["title"]));
				} else {
					$jsonArr["locale"][$key]["short_title"] = "";
				}
				if (isset($dataArr["locale"][$key]["title"])) {
					$jsonArr["locale"][$key]["title"] = (decodeString($dataArr["locale"][$key]["title"]));
				} else if (isset($dataArr["locale"][$key]["short_title"])) {
					$jsonArr["locale"][$key]["title"] = (decodeString($dataArr["locale"][$key]["short_title"]));
				} else {
					$jsonArr["locale"][$key]["title"] = "";
				}
				if (isset($dataArr["locale"][$key]["description"])) {
					$jsonArr["locale"][$key]["description"] = (decodeString($dataArr["locale"][$key]["description"]));
				} else {
					$jsonArr["locale"][$key]["description"] = "";
				}
				#
				# Settings
				#
				if(!$bSkipSettingLang){
					$array[$cpt]["settings-lang"][$key] = array();
					if (isset($dataArr["locale"][$key]["sets"])) {
						$array[$cpt]["settings-lang"][$key]["sets"] = (decodeString($dataArr["locale"][$key]["sets"]));
					} else {
						$array[$cpt]["settings-lang"][$key]["sets"] = "";
					}
					if (isset($dataArr["locale"][$key]["repetition"])) {
						$array[$cpt]["settings-lang"][$key]["repetition"] = (decodeString($dataArr["locale"][$key]["repetition"]));
					} else {
						$array[$cpt]["settings-lang"][$key]["repetition"] = "";
					}
					if (isset($dataArr["locale"][$key]["hold"])) {
						$array[$cpt]["settings-lang"][$key]["hold"] = (decodeString($dataArr["locale"][$key]["hold"]));
					} else {
						$array[$cpt]["settings-lang"][$key]["hold"] = "";
					}
					if (isset($dataArr["locale"][$key]["weight"])) {
						$array[$cpt]["settings-lang"][$key]["weight"] = (decodeString($dataArr["locale"][$key]["weight"]));
					} else {
						$array[$cpt]["settings-lang"][$key]["weight"] = "";
					}
					if (isset($dataArr["locale"][$key]["tempo"])) {
						$array[$cpt]["settings-lang"][$key]["tempo"] = (decodeString($dataArr["locale"][$key]["tempo"]));
					} else {
						$array[$cpt]["settings-lang"][$key]["tempo"] = "";
					}
					if (isset($dataArr["locale"][$key]["rest"])) {
						$array[$cpt]["settings-lang"][$key]["rest"] = (decodeString($dataArr["locale"][$key]["rest"]));
					} else {
						$array[$cpt]["settings-lang"][$key]["rest"] = "";
					}
					if (isset($dataArr["locale"][$key]["frequency"])) {
						$array[$cpt]["settings-lang"][$key]["frequency"] = (decodeString($dataArr["locale"][$key]["frequency"]));
					} else {
						$array[$cpt]["settings-lang"][$key]["frequency"]  = "";
					}
					if (isset($dataArr["locale"][$key]["duration"])) {
						$array[$cpt]["settings-lang"][$key]["duration"] = (decodeString($dataArr["locale"][$key]["duration"]));
					} else {
						$array[$cpt]["settings-lang"][$key]["duration"] = "";
					}
				}
			}
			#
			# Manipulation des images de l'exercice
			#
			$jsonArr["picture"] = array();
			if (isset($dataArr["picture"][0]["pic"]) && file_exists($dataArr["picture"][0]["pic"]) && filesize($dataArr["picture"][0]["pic"]) > 0) {
				$jsonArr["picture"][0]["pic"] = base64_encode($oCipher->encrypt($idLicence . ";" . $dataArr["picture"][0]["pic"]));
				
			} else {
				$jsonArr["picture"][0]["pic"] = "";
			}
			if (isset($dataArr["picture"][1]["pic"]) && file_exists($dataArr["picture"][1]["pic"]) && filesize($dataArr["picture"][1]["pic"]) > 0) {
				$jsonArr["picture"][1]["pic"] = base64_encode($oCipher->encrypt($idLicence . ";" . $dataArr["picture"][1]["pic"]));
				
			} else {
				$jsonArr["picture"][1]["pic"] = "";
			}
			if (isset($dataArr["picture"][0]["thumb"]) && file_exists($dataArr["picture"][0]["thumb"]) && filesize($dataArr["picture"][0]["thumb"]) > 0) {
				$jsonArr["picture"][0]["thumb"] = base64_encode($oCipher->encrypt($idLicence . ";" . $dataArr["picture"][0]["thumb"]));
				
			} else {
				$jsonArr["picture"][0]["thumb"] = "";
			}
			if (isset($dataArr["picture"][1]["thumb"]) && file_exists($dataArr["picture"][1]["thumb"]) && filesize($dataArr["picture"][1]["thumb"]) > 0) {
				$jsonArr["picture"][1]["thumb"] = base64_encode($oCipher->encrypt($idLicence . ";" . $dataArr["picture"][1]["thumb"]));
				
			} else {
				$jsonArr["picture"][1]["thumb"] = "";
			}
			
			#
			# Encodage du data de l'exercice.
			#
			$json = json_endecodeArr($jsonArr);
			$array[$cpt]["data"] = $json;
			if(isset($val_exercise["userdata"])){
				$array[$cpt]["userdata"] = $val_exercise["userdata"];
			}
			//dwizzel patch because for template we need userdata, data and programdata, not like the result of a search
			if(isset($val_exercise["programdata"])){
				$array[$cpt]["programdata"] = $val_exercise["programdata"];
			}
			$cpt++;
		}
		unset($jsonArr);
		return $array;
	}
	
	//------------------------------------------------------------------------
	private function trimKeyword($str){
		//clean up des mots
		$str = mb_strtolower($str, 'UTF-8');
		$str = preg_replace('/[^a-zA-Z0-9\sÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ+-]/', ' ', $str);	
		$str = preg_replace('/[\s]+/', ' ', $str);	
		$str = trim($str);
			
		return $str;
		}

	}


//END