<?php
/**
@auth:	"Dwizzel"
@date:	00-00-0000
@info:	SEARCH
		
*/


class Search {
	
	private $reg;
	private $className = 'Search';
	private $bTrace = false;	
	
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
	//test line with the encoder.php
	public function getExerciceListingByKeywordIdsForPreview($data){
		//sera une liste de ids de keyword separe selon le type
		//12,15,49|26,18,70 = keyword_ids,keyword_ids,etc...
		$arr = array();	
		if($data != '' && isset($data['ids']) && $data['ids'] != ''){
			//on va chercher les exercices selon les ids des keywords	
			$arrData = explode(',', $data['ids']); //keyword_ids
			//error dans le explode car doit en avoir 2 absolument
			if(!isset($arrData[0])){
				return 0;	
				}
			//ca nous prend du data sinon on ramene rien
			if($arrData[0] == '' || $arrData[0] === 0 || $arrData[0] == false){
				return 0;	
				}
			//on continu
			$idUser	= $this->reg->get('sess')->get('idUser');
			$licence_data = $this->reg->get('sess')->get('licence');
			$available_locale = $licence_data['available_locale'];
			$idLicence = $licence_data['idLicence'];
			$clinic_users = $this->reg->get('utils')->getClinicUsers();
			$locale = $this->reg->get('sess')->get('locale');
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
			$strImplodedKeywordIds = implode(',', $arrData);
			//
			$arrKeyExercise = array();
			//on va chercher les exercises selon la liste des keywords
			$query = 'SELECT keyword_exercise.idExercise, exercise.rank FROM keyword_exercise, exercise WHERE keyword_exercise.idKeyword IN ('.$strImplodedKeywordIds.') AND keyword_exercise.idExercise = exercise.idExercise ORDER BY exercise.rank ASC;';
			$rs = $this->reg->get('db')->query($query);
			if($rs && $rs->num_rows){		
				//will get DISTINCT key with that
				foreach($rs->rows AS $k=>$v){
					$arrKeyExercise[$v['idExercise']] = intVal($v['rank']);
					}
				}
			unset($rs, $k, $v);
			//savoir combien d<exercices il restte a montrer
			$iTotalExerciseCount = count($arrKeyExercise);	
			//on classe selon le rank
			asort($arrKeyExercise);
			//on va juste garder une partie des premier exercices surtout pour le test
			$arrKeyExercise = array_slice($arrKeyExercise, 0, MAX_PREVIEW_SEARCH_NUM_ROWS, true);
			//le datat de base
			if(is_array($arrKeyExercise) && count($arrKeyExercise) > 0){
				//imploded arrayKeys, since we use it 4 times
				$strImplodedArrKeys = implode(", ", array_keys($arrKeyExercise));
				//le data et video
				$query = 'SELECT exercise.idExercise, exercise.codeExercise, exercise.data, exercise.idUser, video.host, video.embed_code FROM exercise LEFT JOIN video ON exercise.idExercise = video.idExercise WHERE exercise.idExercise IN ('.$strImplodedArrKeys.');';
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){
					foreach($rs->rows AS $k=>$v){
						$arrKeyExercise[$v['idExercise']] = array(
							'codeExercise'	=> $v['codeExercise'],
							'data'			=> decodeString($v['data']),
							);
						}
					}
				unset($rs, $k, $v);
				//send	
				return array(
					'data' => $this->reorderExercise($arrKeyExercise),
					'maxcount' => intVal($iTotalExerciseCount)
					);		
				}
			}
		//no result
		return 0;
		}

	//------------------------------------------------------------------------
	//test line with the encoder.php
	public function getExerciceListingByKeywordIds($data){
		//sera une liste de ids de keyword separe selon le type
		//12,15,49|26,18,70 = keyword_ids,keyword_ids|keyword_user_ids,keyword_user_ids
		$arr = array();	
		if($data != '' && isset($data['ids']) && $data['ids'] != ''){
			//on va chercher les exercices selon les ids des keywords	
			$arrData = explode(',', $data['ids']); //keyword_ids
			//error dans le explode car doit en avoir 2 absolument
			if(!isset($arrData[0])){
				return 0;	
				}
			//ca nous prend du data sinon on ramene rien
			if($arrData[0] == '' || $arrData[0] === 0 || $arrData[0] == false){
				return 0;	
				}
			//on continu
			$idUser	= $this->reg->get('sess')->get('idUser');
			$licence_data = $this->reg->get('sess')->get('licence');
			$available_locale = $licence_data['available_locale'];
			$idLicence = $licence_data['idLicence'];
			$clinic_users = $this->reg->get('utils')->getClinicUsers();
			$locale = $this->reg->get('sess')->get('locale');
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
			$strImplodedKeywordIds = implode(',', $arrData);
			//
			$arrKeyExercise = array();
			//on va chercher les exercises selon la liste des keywords
			$query = 'SELECT keyword_exercise.idExercise, exercise.rank FROM keyword_exercise, exercise WHERE keyword_exercise.idKeyword IN ('.$strImplodedKeywordIds.') AND keyword_exercise.idExercise = exercise.idExercise ORDER BY exercise.rank ASC;';
			$rs = $this->reg->get('db')->query($query);
			if($rs && $rs->num_rows){		
				//will get DISTINCT key with that
				foreach($rs->rows AS $k=>$v){
					$arrKeyExercise[$v['idExercise']] = intVal($v['rank']);
					}
				}
			unset($rs, $k, $v);
			//on classe selon le rank
			asort($arrKeyExercise);
			//on va juste garder une partie des premier exercices surtout pour le test
			$arrKeyExercise = array_slice($arrKeyExercise, 0, MAX_SEARCH_NUM_ROWS, true);
			//le datat de base
			if(is_array($arrKeyExercise) && count($arrKeyExercise) > 0){
				//imploded arrayKeys, since we use it 4 times
				$strImplodedArrKeys = implode(", ", array_keys($arrKeyExercise));
				//le data et video
				$query = 'SELECT exercise.idExercise, exercise.codeExercise, exercise.data, exercise.idUser, video.host, video.embed_code FROM exercise LEFT JOIN video ON exercise.idExercise = video.idExercise WHERE exercise.idExercise IN ('.$strImplodedArrKeys.');';
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){
					foreach($rs->rows AS $k=>$v){
						$arrKeyExercise[$v['idExercise']] = array(
							'codeExercise'	=> $v['codeExercise'],
							'data'			=> decodeString($v['data']),
							);
						}
					}
				unset($rs, $k, $v);
				//send	
				return $this->reorderExercise($arrKeyExercise);
				}
			}
		//no result
		}


	//------------------------------------------------------------------------
	private function reorderExercise($arrExercise = array()){
		$cpt = 0;
		$array = array();
		$oCipher = new Cipher(PASS_CYPHER_SALT);
		$licence_data = $this->reg->get('sess')->get('licence');
		$available_locale = array('en_US' => 'en_US');
		$idLicence = $licence_data['idLicence'];
		reset($arrExercise);
		while(list($idExercise, $val_exercise) = each($arrExercise)){
			$dataArr = json_decodeStr($val_exercise['data']);
			$array[$cpt] = array();
			$array[$cpt]['id'] = $idExercise;
			$array[$cpt]['codeExercise'] = $val_exercise['codeExercise'];
			//
			foreach($available_locale AS $key){
				//Reconstruction du data de l'exercice.
				if(isset($dataArr['locale'][$key]['short_title'])){
					$array[$cpt]['shortTitle'] = decodeString($dataArr['locale'][$key]['short_title']);
				}else if(isset($dataArr['locale'][$key]['title'])){
					$array[$cpt]['shortTitle'] = decodeString($dataArr['locale'][$key]['title']);
				}else{
					$array[$cpt]['shortTitle'] = '';
					}
				if(isset($dataArr['locale'][$key]['title'])){
					$array[$cpt]['title'] = decodeString($dataArr['locale'][$key]['title']);
				}else if(isset($dataArr['locale'][$key]['short_title'])){
					$array[$cpt]['title'] = decodeString($dataArr['locale'][$key]['short_title']);
				}else{
					$array[$cpt]['title'] = '';
					}
				}
			//Manipulation des images de l'exercice
			if(isset($dataArr['picture'][0]['thumb'])){
				$array[$cpt]['thumb'] = base64_encode($oCipher->encrypt($idLicence.';'.$dataArr['picture'][0]['thumb']));
			}else{
				$array[$cpt]['thumb'] = '';
				}
			//
			$cpt++;
			}
		return $array;
	}


	//------------------------------------------------------------------------------------------
	private function wordPermutationForRegex($items, $strPermuted = '', $perms = array()){
		//EX: 
		//avec [g,a] et "getting in and out of bed" 
		//et aussi avec [g,a] et "in and out of bed i am getting"
		//pour la deuxieme phrase  je ne crois pas qu'il faut que ca fonctionne 
		//sinon beaucoup trop de possibilite de retour alors il faut
		//avoir au minimum la premiere lettre de bonne peut importe la permutation	
		// 	"/(^ge[\w]{1,}.*[\s]{1}a[\w]{2,}.*$)|(^a[\w]{2,}.*[\s]{1}ge[\w]{1,}.*$)/"
		//	OU
		//		
		//
		//on a au moins besoin d'un mot de 3 chars pour eviter les 
		// "and, et, ou, or, de, le, la, etc..."
		$iMinChars = 3;
		if(empty($items)){ 
			$strPermuted .= '(';
			for($i=0; $i<count($perms); $i++){
				$strWord = $perms[$i];
				$iRightStringCompletion = 0;
				if(strlen($strWord) < $iMinChars){
					$iRightStringCompletion = $iMinChars - strlen($strWord);
					}
				//si pas de completion alors la consition n'est pas la meme
				if($iRightStringCompletion === 0){
					if($i == 0){ 
						//le premier du array
						$strPermuted .= '^'.$strWord.'.*'; 
					}else if($i == (count($perms)-1)){ 
						//le dernier du array
						$strPermuted .= '[\s]{1}'.$strWord.'.*$';
					}else{
						//ceux dans le milieu
						$strPermuted .= '[\s]{1}'.$strWord.'.*';
						}
				}else{
					if($i == 0){ 
						//le premier du array
						$strPermuted .= '^'.$strWord.'[\w]{'.$iRightStringCompletion.',}.*'; 
					}else if($i == (count($perms)-1)){ 
						//le dernier du array
						$strPermuted .= '[\s]{1}'.$strWord.'[\w]{'.$iRightStringCompletion.',}.*$';
					}else{
						//ceux dans le milieu
						$strPermuted .= '[\s]{1}'.$strWord.'[\w]{'.$iRightStringCompletion.'}.*';
						}
					}
				}
			$strPermuted .= ')|';
		}else{
			for($i=count($items)-1; $i>=0; --$i){
				$newitems = $items;
				$newperms = $perms;
				list($foo) = array_splice($newitems, $i, 1);
				array_unshift($newperms, $foo);
				$strPermuted = $this->wordPermutationForRegex($newitems, $strPermuted,  $newperms);
				}
			}
		return $strPermuted;
		}


	//------------------------------------------------------------------------------------------
	private function wordPermutationForSQL($items, $strPermuted = '', $perms = array()){
		//EX: simple chars
		// REGEXP "^ab.*$"
		// REGEXP "^a[[:alnum:]]{2,}.*$"
		// REGEXP "^a[[:alnum:]]{2,}.*[[:space:]]{1}p[[:alnum:]]{2,}.*$|^p[[:alnum:]]{2,}.*[[:space:]]{1}a[[:alnum:]]{2,}.*$"
		$iMinChars = 3;
		if(empty($items)){ 
			$strPermuted .= '(';
			for($i=0; $i<count($perms); $i++){
				$strWord = $perms[$i];
				$iRightStringCompletion = 0;
				if(strlen($strWord) < $iMinChars){
					$iRightStringCompletion = $iMinChars - strlen($strWord);
					}
				//si pas de completion alors la consition n'est pas la meme
				if($iRightStringCompletion === 0){
					if($i == 0){ 
						//le premier du array
						$strPermuted .= '^'.$strWord.'.*'; 
					}else if($i == (count($perms)-1)){ 
						//le dernier du array
						$strPermuted .= '[[:space:]]{1}'.$strWord.'.*$';
					}else{
						//ceux dans le milieu
						$strPermuted .= '[[:space:]]{1}'.$strWord.'.*';
						}
				}else{
					if($i == 0){ 
						//le premier du array
						$strPermuted .= '^'.$strWord.'[[:alnum:]]{'.$iRightStringCompletion.',}.*'; 
					}else if($i == (count($perms)-1)){ 
						//le dernier du array
						$strPermuted .= '[[:space:]]{1}'.$strWord.'[[:alnum:]]{'.$iRightStringCompletion.',}.*$';
					}else{
						//ceux dans le milieu
						$strPermuted .= '[[:space:]]{1}'.$strWord.'[[:alnum:]]{'.$iRightStringCompletion.'}.*';
						}
					}
				}
			$strPermuted .= ')|';
		}else{
			for($i=count($items)-1; $i>=0; --$i){
				$newitems = $items;
				$newperms = $perms;
				list($foo) = array_splice($newitems, $i, 1);
				array_unshift($newperms, $foo);
				$strPermuted = $this->wordPermutationForSQL($newitems, $strPermuted,  $newperms);
				}
			}
		return $strPermuted;
		}

	
	//------------------------------------------------------------------------
	public function fetchAutoCompleteData($data){
		//get the data from word search in the input box::input-main-exercice-search-autocomplete
		/*
		POUR PROTO: 001 juste le mot cle
			RECEIVER:
				data:{
						word:"hip",
						proto:"001"
						}

		POUR PROTO: 002, 003 mots cles et on va mettre un flag 0 = keywords, 1 = title, 2=code exercice
					pour limiter les recherches
			RECEIVER:
				data:{
						word:"hip",
						proto:"002"
						kwtype:"0,1,2"
						}	

		SENDER:
			data:{
				0:{
					id:"12121,666",
					name:"EXR-200003"
					},
				1:{
					id:"200030",
					name:"EXR-200030"
					},
				}
		*/
		$arr = array();
		if(isset($data['word']) && !empty($data['word'])){
			//pour PROTO:001 on a pas encore le kwtype, celui par defaut est donc kwtype = 0
			$strProto = '001';	
			if(isset($data['proto']) && $data['proto'] != ''){	
				$strProto = $data['proto'];	
				}
			$strKwType = '1';	
			if(isset($data['kwtype']) && $data['kwtype'] != ''){	
				$strKwType = $data['kwtype'];	
				}
			$locale	= 'en_US';
			$brand_data = $this->reg->get('sess')->get('brand');
			$licence_data = $this->reg->get('sess')->get('licence');
			$idLicence = $licence_data['idLicence'];
			if(!isset($data['module']) || (isset($data['module']) && intVal($data['module']) == '-1') ){
				$moduleArr = $this->reg->get('utils')->getClinicModule();
			}else{
				$moduleArr = array(
					intVal($data['module']) => ''
					);
				}
			//show
			//with base test word : "   gA.   E$    "	
			//doit donner a la fin: "ga e"
			if($this->bTrace){
				echo ('WORD[0]:"'.$data['word'].'"').EOL;
				}
			//on va decompser les mots si il va y en avoir plusieurs
			$data['word'] = trim($data['word']);
			if($this->bTrace){
				echo ('WORD[1]:"'.$data['word'].'"').EOL;
				}
			//clean les suite de space par un seul
			$data['word'] = preg_replace('/[\s]+/', ' ', $data['word']);
			if($this->bTrace){
				echo ('WORD[2]:"'.$data['word'].'"').EOL;
				}
			//et on garde seulement les caracteres valide pour le reg ex c'est a dire du [a-z0-9] et space
			$data['word'] = preg_replace('/[^a-zA-Z0-9\s]/', '', $data['word']);
			if($this->bTrace){	
				echo ('WORD[3]:"'.$data['word'].'"').EOL;
				}
			//on lowercase
			$data['word'] = mb_strtolower($data['word'], 'UTF-8');
			if($this->bTrace){
				echo ('WORD[4]:"'.$data['word'].'"').EOL;
				}
			//pour le word encode et decode
			$arrWordData = array();
			$arrWordDataTmp =  explode(' ', $data['word']);
			foreach($arrWordDataTmp AS $k=>$v){
				//check si vide ou ' '
				if($arrWordDataTmp[$k] != '' && $arrWordDataTmp[$k] != ' '){
					array_push($arrWordData, $this->reg->get('db')->escape($arrWordDataTmp[$k]));
					}
				}
			//
			$strWhereClause = '';
			//check si plusieurs mots
			if(count($arrWordData) == 1){ //un
				$strWhereClause = 'AND ( LCASE(keyword.keyword) LIKE "'.$arrWordData[0].'%" )';
			}else if(count($arrWordData) == 2){ //deux
				$strWhereClause = 'AND ( LCASE(keyword.keyword) LIKE "'.$arrWordData[0].'%" OR LCASE(keyword.keyword) LIKE "% '.$arrWordData[0].'%" OR LCASE(keyword.keyword) LIKE "'.$arrWordData[1].'%" OR LCASE(keyword.keyword) LIKE "% '.$arrWordData[1].'%")';
			}else if(count($arrWordData) == 3){ //deux
				$strWhereClause = 'AND ( LCASE(keyword.keyword) LIKE "'.$arrWordData[0].'%" OR LCASE(keyword.keyword) LIKE "% '.$arrWordData[0].'%" OR LCASE(keyword.keyword) LIKE "'.$arrWordData[1].'%" OR LCASE(keyword.keyword) LIKE "% '.$arrWordData[1].'%" OR LCASE(keyword.keyword) LIKE "'.$arrWordData[2].'%" OR LCASE(keyword.keyword) LIKE "% '.$arrWordData[2].'%")';
				}
			//
			//pour le champs de plus pour le proto 002
			$strQueryProto = '';
			if($strProto != '001'){
				$strQueryProto = ' keyword.kwtype, ';
				}
			//la query on order by the rank et par les plus vieux keyword soit le plus petit id
			$query = 'SELECT '.$strQueryProto.'keyword.keyword, keyword.idKeyword, keyword_rank.rank FROM keyword_exercise, mod_exercise, keyword, keyword_rank WHERE keyword.idKeyword = keyword_rank.idKeyword AND mod_exercise.idModule IN ('.implode(",", array_keys($moduleArr)).') AND keyword.idLicence IN (0,'.intval($idLicence).') '.$strWhereClause.' AND keyword.idKeyword = keyword_exercise.idKeyword AND keyword_exercise.idExercise = mod_exercise.idExercise AND keyword.locale = "'.$locale.'" AND keyword_rank.locale = "'.$locale.'" AND keyword.kwtype IN ('.$strKwType.') GROUP BY keyword.idKeyword ORDER BY keyword_rank.rank DESC, keyword.idKeyword ASC;';
			//
			$rs = $this->reg->get('db')->query($query);
			//si il y a
			if($rs && $rs->num_rows){
				$arrResult = array();
				//loop
				//si un ou plus
				if(count($arrWordData) == 1){ // 1 keyword
					//	exemple: "abdominal plank" et "abdominal
					// (^ab[\w]{1,}.*$)
					//pour un cas csimple on utilisera pas de permutation inutile
					//on va pusher seulement ceux qui corresponde au nombre de mot
					$strPregMatch = '/(^'.$arrWordData[0].'.*$)/';
				}else if(count($arrWordData) > 1){ 
					//	on va essayer toutes les permutatio spossible
					//	exemple: "abdominal plank" et "plank abdominal" et "plank with abdominal"
					$strPregMatch = $this->wordPermutationForRegex($arrWordData);
					//on strip le last pipe
					$strPregMatch = '/'.substr($strPregMatch, 0, strlen($strPregMatch)-1).'/';
					}
				//show for the fun of it
				if($this->bTrace){
					echo 'REGEX:"'.$strPregMatch.'"'.EOL;
					}
				//
				foreach($rs->rows as $k=>$v){
					//le keyword encode et minuscule
					$keyword = encodeString(mb_strtolower($v['keyword'], 'UTF-8'));
					//si le keyword match
					if(preg_match($strPregMatch, $keyword)){
						if(isset($arrResult[$keyword])){
							//si existe deja
							$arrResult[$keyword]['id'] .= ','.$v['idKeyword'];
						}else{
							if($strProto != '001'){ //PROTO 002, 003
								//si existe deja
								$arrResult[$keyword] = array(
									'id' => $v['idKeyword'].'',
									'name' => $keyword,
									'kwtype' => $v['kwtype'],
									);
							}else{ //PROTO 001
								//si existe deja
								$arrResult[$keyword] = array(
									'id' => $v['idKeyword'].'',
									'name' => $keyword,
									);
								}
							}
						}
					}
				unset($rs, $k, $v);
				//on met ca par numeric et garde seulement les premiers
				if(count($arrResult)){
					$iCmpt = 0;
					if($strProto != '001'){ //PROTO 002, 003
						if($strProto == '002'){ //PROTO 002)
							foreach($arrResult as $k=>$v){
								$iCmpt++;
								array_push($arr, array(
									'id' => $v['id'],
									'name' => $v['name'],
									'kwtype' => $v['kwtype'],	
									));
								if($iCmpt >= MAX_ROWS_AUTOCOMPLETE_RETURNED){
									break;
									}
								}
						}else{ //PROTO 003 ui classe par groupe de proto
							//conteneur temporaire des keywords de retour par kwtype
							$arrTmpKw = array();
							//check combien de kwtype
							$arrKwTypeMax = explode(',', $strKwType);
							//loop pour les compterur de chanque kwtype
							foreach($arrKwTypeMax as $k2=>$v2){
								$arrTmpKw[$v2] = array();
								}
							//clean
							unset($k2, $v2);
							//le max pour chaque type de kwtype
							//important trouver une maniere de faire plus intelligente
							//car si atteint le max d'un kwtype ca ne veut pas diref
							//que les autre seront plein
							//EX: 	sur 12 retour au max avec 2 kwtype
							//		keyword = 6, title = 4
							//		donc il a saute des keyword se rend jusqu'a la fin de la loop
							//		mais ne trouve pas assez de title pour le compte au total de 12
							//		il faudrait donc se garder des keyword et title pour chaque kwtype
							//		en reserve au cas ou il faudrait en rajouter a la fin de la loop
							//		si le compte total n'est pas atteint
							$iMaxEachKwType = MAX_ROWS_AUTOCOMPLETE_RETURNED/count($arrTmpKw);
							//loop et classe par kwtype max pour chaque
							foreach($arrResult as $k=>$v){
								//si on a un max pour un alors on 
								//le laisse tomber et on passe au suivant
								if(count($arrTmpKw[$v['kwtype']]) < $iMaxEachKwType){
									//incremente le counter principal
									$iCmpt++;
									//on push
									array_push($arrTmpKw[$v['kwtype']], array(
										'id' => $v['id'],
										'name' => $v['name'],
										));
									}
								//si tout les kwtype sont remplis alors on sort car pas besoin de plus
								if($iCmpt >= MAX_ROWS_AUTOCOMPLETE_RETURNED){
									break;
									}
								}
							//clean
							unset($k, $v);
							//on classe par groupe de kwtype si on a du data dans au moins un 
							//sinon on supprime
							foreach($arrTmpKw as $k =>$v){
								if(count($arrTmpKw[$k]) == 0){
									unset($arrTmpKw[$k]);
									}
								}
							//si on a quelque chose
							if(count($arrTmpKw) > 0){
								$arr = $arrTmpKw;
								}
							}
					}else{ //PROTO 001
						foreach($arrResult as $k=>$v){
							$iCmpt++;
							array_push($arr, array(
								'id' => $v['id'],
								'name' => $v['name'],
								));
							if($iCmpt >= MAX_ROWS_AUTOCOMPLETE_RETURNED){
								break;
								}
							}
						}
					unset($k, $v);
					}
				}
			}
		//print_r($arr);
		//exit();
		return $arr;
		}
	};


//END
