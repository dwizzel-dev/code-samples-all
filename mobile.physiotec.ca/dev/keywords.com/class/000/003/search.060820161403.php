<?php
/**
@auth:	"Dwizzel"
@date:	00-00-0000
@info:	SEARCH
	
	search
	fetch-autocomplete	
	{"word":"knee","kwtype":"1,2"}
	
	view-source:https://mobile....@physiotec.ca/dev/keywords.com/service.php?&PHPSESSID=j0t756la8jq1ctsihp3mml8ccdnoej8a&pid=1&time=1&section=search&service=fetch-autocomplete&data=%7B%22word%22%3A%22knee%22%2C%22kwtype%22%3A%221%2C2%22%7D

	ANALYSE:

		Affichage des lignes 0 - 29 (total de 1728, Traitement en 0.1252 sec)

		SELECT 
		keyword.idKeyword,
		keyword.keyword
		FROM 
		keyword 
		WHERE 
		keyword.idLicence IN ('1013', 0) AND 
		keyword.keyword REGEXP "^abdo.*$|[[:space:]]{1}abdo.*$"
		AND
		keyword.keyword REGEXP "^sup.*$|[[:space:]]{1}sup.*$" AND
		keyword.locale = "en_US";

		"^abdo.*|[[[:space:]]]{1,}abdo.*"
		"^.*[[[:space:]]]{1,}abdo.*$"

		SELECT 
		keyword.idKeyword,
		keyword.keyword
		FROM 
		keyword 
		WHERE 
		keyword.idLicence IN ('1013', 0) AND 
		keyword.keyword REGEXP "^abdo.*[[:space:]]{1}sup.*$|[[:space:]]abdo.*[[:space:]]{1}sup.*$" AND
		keyword.locale = "en_US";
		
		SELECT 
		keyword.idKeyword,
		keyword.keyword
		FROM 
		keyword 
		WHERE 
		keyword.idLicence IN ('1013', 0) AND (
			
			keyword.keyword = 'abdo' OR 
			keyword.keyword LIKE 'abdo%' OR 
			keyword.keyword LIKE '% abdo%' OR
			keyword.keyword = 'ABDO' OR 
			keyword.keyword LIKE 'ABDO%' OR 
			keyword.keyword LIKE '% ABDO%'
			
		) AND 
		keyword.locale = "en_US";


		SELECT 
		keyword.idKeyword,
		keyword.keyword
		FROM 
		keyword 
		WHERE 
		keyword.idLicence IN ('1013', 0) AND (
			
			keyword.keyword = 'sup' OR 
			keyword.keyword LIKE 'sup%' OR 
			keyword.keyword LIKE '% sup%' OR
			keyword.keyword = 'SUP' OR 
			keyword.keyword LIKE 'SUP%' OR 
			keyword.keyword LIKE '% SUP%'
			
		) AND 
		keyword.locale = "en_US";


		SELECT 
		keyword.idKeyword,
		keyword.keyword
		FROM 
		keyword 
		WHERE 
		keyword.idLicence IN ('1013', 0) AND (
			
			keyword.keyword = 'abdo' OR 
			keyword.keyword LIKE 'abdo%' OR 
			keyword.keyword LIKE '% abdo%' OR
			keyword.keyword = 'ABDO' OR 
			keyword.keyword LIKE 'ABDO%' OR 
			keyword.keyword LIKE '% ABDO%' OR 
			keyword.keyword = 'sup' OR 
			keyword.keyword LIKE 'sup%' OR 
			keyword.keyword LIKE '% sup%' OR
			keyword.keyword = 'SUP' OR 
			keyword.keyword LIKE 'SUP%' OR 
			keyword.keyword LIKE '% SUP%'
			
		) AND 
		keyword.locale = "en_US";

		SELECT 
		keyword.idKeyword,
		keyword.keyword
		FROM 
		keyword 
		WHERE 
		keyword.idLicence IN ('1013', 0) AND (
			
			LCASE(keyword.keyword) LIKE "abdo% sup%" OR 
			LCASE(keyword.keyword) LIKE "% abdo% sup%"
				
		) AND 
		keyword.locale = "en_US";

	
*/

class Search {
	
	private $reg;
	private $className = 'Search';
	private $bTrace = false; 
	private $bWordsForDummies = true;
	private $bWordsPermutationForDummies = true;
	private $bWeWillTryHarder = false;
	private $bWeWillTryEverything = false;
	//on a pas l'architecture serveur
	//pour supporter plus que ca
	private $iMaxPermutation = 4;
	private $iMaxCharsPermutation = 4;
	
	//------------------------------------------------------------------------
	public function __construct(&$reg){
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
			$query = 'SELECT keyword_exercise.idExercise, exercise.rank FROM keyword_exercise, exercise WHERE exercise.ready = "1" AND keyword_exercise.idKeyword IN ('.$strImplodedKeywordIds.') AND keyword_exercise.idExercise = exercise.idExercise ORDER BY exercise.rank ASC;';
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
							'codeExercise' => $v['codeExercise'],
							'data' => decodeString($v['data']),
							);
						}
					}
				//clean	
				unset($rs, $k, $v);
				//recherche des filtres pour chaque exercice
				$query = 'SELECT idExercise, idMod_category, idMod_search_filter FROM mod_exercise WHERE idExercise IN ('.$strImplodedArrKeys.') AND idModule IN ('.$strImplodedArrModules.') ORDER BY idMod_category ASC, idExercise ASC, idMod_search_filter ASC;';
				$rs = $this->reg->get('db')->query($query);
				//le arr conteneur des filter name by id
				$arrFiltersName = array();
				//minor check
				if($rs && $rs->num_rows){	
					//conteneur des exercice->cat->filter
					$strFilterForQuery = '';
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
							$arrFilterResult[$v['idExercise']][$v['idMod_category']] = array();
							}
						array_push($arrFilterResult[$v['idExercise']][$v['idMod_category']], $v['idMod_search_filter']);
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
								//la srtong apor aller chercher les names des filters
								foreach($arrKeyExercise[$k]['filter'] as $k3=>$v3){
									$strFilterForQuery .= $v3.',';
									}	
								break;
								}
							}
						}
					//clean	
					unset($rs, $k, $v, $k2, $v2, $k3, $v3);	
					//clean
					unset($arrFilterResult, $arrCatPriotity);
					//on va aller chercher les noms des filters
					if(strlen($strFilterForQuery) > 0){	
						//on strip la last virgule
						$strFilterForQuery = substr($strFilterForQuery, 0, strlen($strFilterForQuery) - 1);
						$query = 'SELECT idMod_search_filter AS "id", title AS "title" FROM mod_search_filter WHERE idMod_search_filter IN ('.$strFilterForQuery.') ORDER BY title ASC;';
						$rs = $this->reg->get('db')->query($query);	
						if($rs && $rs->num_rows){
							foreach($rs->rows as $k=>$v){
								$arrFiltersName[$v['id']] = $v['title'];
								}
							}
						//clean	
						unset($rs, $k, $v);		
						}
					}
				//clean	
				unset($rs, $k, $v);			
				//send	
				return array(
					'data' => $this->reorderExercise($arrKeyExercise),
					'maxcount' => intVal($iTotalExerciseCount),
					'filters' => $arrFiltersName,	
					);		
				}
			}
		//no result
		return 0;
		}

	//------------------------------------------------------------------------
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
							'codeExercise' => $v['codeExercise'],
							'data' => decodeString($v['data']),
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
		$available_locale = array(
			'en_US' => 'en_US'
			);
		$idLicence = $licence_data['idLicence'];
		reset($arrExercise);
		while(list($idExercise, $val_exercise) = each($arrExercise)){
			$dataArr = json_decodeStr($val_exercise['data']);
			$array[$cpt] = array();
			$array[$cpt]['id'] = $idExercise;
			$array[$cpt]['codeExercise'] = $val_exercise['codeExercise'];
			//les filters
			if(isset($val_exercise['filter'])){
				//on va stripper la last virgule
				$array[$cpt]['filter'] = $val_exercise['filter'];
				}
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

	//------------------------------------------------------------------------
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

	//------------------------------------------------------------------------
	private function wordPermutationForSQL($items, $strPermuted = '', $perms = array()){
		//EX: simple chars
		// REGEXP "^ab.*$"
		// REGEXP "^a[[:alnum:]]{2,}.*$"
		// REGEXP "^a[[:alnum:]]{2,}.*[[:space:]]{1}p[[:alnum:]]{2,}.*$|^p[[:alnum:]]{2,}.*[[:space:]]{1}a[[:alnum:]]{2,}.*$"
		$iMinChars = 2;
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
		//get the data from word search in the input 
		//on fait un test avec la deuxieme methode de query
		return $this->fetchAutoCompleteData3($data);
		}
			
	//------------------------------------------------------------------------
	private function wordPermutationForSQL2($items, $strPermuted = '', $perms = array()){
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
				//pour lesphrase commancant par et continuan t par	
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
			$strPermuted .= '(';
			//les mots compose
			for($i=0; $i<count($perms); $i++){
				$strWord = $perms[$i];
				$iRightStringCompletion = 0;
				if(strlen($strWord) < $iMinChars){
					$iRightStringCompletion = $iMinChars - strlen($strWord);
					}
				//pour lesphrase commancant par et continuan t par	
				//si pas de completion alors la consition n'est pas la meme
				if($iRightStringCompletion === 0){
					if($i == 0){ 
						//le premier du array
						$strPermuted .= '.*[[:space:]]{1}'.$strWord.'.*'; 
					}else if($i == (count($perms)-1)){ 
						//le dernier du array
						$strPermuted .= '.*[[:space:]]{1}'.$strWord.'.*$';
					}else{
						//ceux dans le milieu
						$strPermuted .= '.*[[:space:]]{1}'.$strWord.'.*';
						}
				}else{
					if($i == 0){ 
						//le premier du array
						$strPermuted .= '.*[[:space:]]{1}'.$strWord.'[[:alnum:]]{'.$iRightStringCompletion.',}.*'; 
					}else if($i == (count($perms)-1)){ 
						//le dernier du array
						$strPermuted .= '.*[[:space:]]{1}'.$strWord.'[[:alnum:]]{'.$iRightStringCompletion.',}.*$';
					}else{
						//ceux dans le milieu
						$strPermuted .= '.*[[:space:]]{1}'.$strWord.'[[:alnum:]]{'.$iRightStringCompletion.'}.*';
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
				$strPermuted = $this->wordPermutationForSQL2($newitems, $strPermuted,  $newperms);
				}
			}
		return $strPermuted;
		}	
		
	//------------------------------------------------------------------------
	private function fetchAutoCompleteData2($data){
		//get the data from word search in the input box::input-main-exercice-search-autocomplete
		/*
		POUR PROTO: 003 mots cles et on va mettre un flag 0 = keywords, 1 = title, 2=code exercice (2  va prendre le bord vu quil sont dans keyword)
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
				},
			kword: "hip" -> le kw stripper de tout truc pas bon	
		*/
		$arr = array();
		if(isset($data['word']) && !empty($data['word'])){
			$strQueryInKwType = '1';	
			if(isset($data['kwtype']) && $data['kwtype'] != ''){	
				$strQueryInKwType = $data['kwtype'];	
				}
			$arrLocale	= array('en_US','fr_CA');
			//$arrLocale	= array('en_US');
			$strQueryInLocale = '"'.implode('","', $arrLocale).'"';	
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
			//with base test word : "   gA.   E$(*&  à  "	
			//doit donner a la fin: "ga e à"
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
			//on lowercase
			$data['word'] = mb_strtolower($data['word'], 'UTF-8');
			if($this->bTrace){
				echo ('WORD[3]:"'.$data['word'].'"').EOL;
				}
			//et on garde seulement les caracteres valide pour 
			//le reg ex c'est a dire du [a-z 0-9] et space et 
			//extended ascii chars
			//$data['word'] = preg_replace('/[^a-zA-Z0-9\sÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ]/', '', $data['word']);
			//vu que l,on a minuscule pas besoin de trimmer les majuscule
			$data['word'] = preg_replace('/[^a-zA-Z0-9\sÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ]/', '', $data['word']);
			if($this->bTrace){
				echo ('WORD[4]:"'.$data['word'].'"').EOL;
				}
			//pour le word encode et decode
			$arrWordData = array();
			$arrWordDataTmp =  explode(' ', $data['word']);
			//just un check au cas ou mais devrait pas avoir besoin de ca
			foreach($arrWordDataTmp AS $k=>$v){
				//check si vide ou ' '
				if($arrWordDataTmp[$k] != '' && $arrWordDataTmp[$k] != ' '){
					array_push($arrWordData, $arrWordDataTmp[$k]);
					}
				}
			$strPregMatch = $this->reg->get('db')->escape($this->wordPermutationForSQL2(array_slice($arrWordData, 0, $this->iMaxPermutation)));
			//on strip le last pipe
			$strPregMatch = substr($strPregMatch, 0, strlen($strPregMatch)-1);
			//la query on order by the rank
			// le kwtype n'est pas encore dans la base de donnees et
			// sera la representation	
			$query = 'SELECT keyword.kwtype AS kwtype, keyword.keyword, keyword.idKeyword FROM keyword_exercise, mod_exercise, keyword, keyword_rank WHERE keyword.idKeyword = keyword_rank.idKeyword AND mod_exercise.idModule IN ('.implode(',', array_keys($moduleArr)).') AND keyword.idLicence IN (0,'.intval($idLicence).') AND keyword.idKeyword = keyword_exercise.idKeyword AND keyword_exercise.idExercise = mod_exercise.idExercise AND keyword.locale IN('.$strQueryInLocale.') AND keyword_rank.locale IN ('.$strQueryInLocale.') AND keyword.kwtype IN ('.$strQueryInKwType.') AND LCASE(keyword.keyword) REGEXP "'.$strPregMatch.'" GROUP BY keyword.idKeyword ORDER BY keyword_rank.rank DESC;';
			//for the fun of debuuugiing
			if($this->bTrace){
				echo $query.EOL.EOL;
				}
			//
			$rs = $this->reg->get('db')->query($query);
			//si il y a
			if($rs && $rs->num_rows){
				//on va maintenant mettre en group de kwtype, et de word
				//car il se peut que un word soit de type different 
				//comme "flexion"
				//qui est de type keyword et shorttitle
				//en meme temps on check si on a atteint notre max 
				//dans tout les kwtype que l'on a	
				$arrByKwType = array();
				//loop
				foreach($rs->rows as $k=>$v){
					//la string encode
					$strKeyword = html_entity_decode(mb_strtolower($v['keyword'], 'UTF-8'), ENT_NOQUOTES|ENT_HTML401, 'UTF-8');
					//check si existe
					//mais foudrait voir avec un flag si les a tous cree
					//alors on bypass cette condition genre ils sont tous //cree alors ne pas check le isset
					if(!isset($arrByKwType[$v['kwtype']])){
						$arrByKwType[$v['kwtype']] = array();
						}
					//on push le data dedans selon le word 
					//on va additionner les keywordId
					//pour ne finir qu'avec un word et plusieurs keywordId
					if(!isset($arrByKwType[$v['kwtype']][$strKeyword])){
						//on le creer
						$arrByKwType[$v['kwtype']][$strKeyword] = $v['idKeyword'];
					}else{
						//on rajoute le kw id
						$arrByKwType[$v['kwtype']][$strKeyword] .= ','.$v['idKeyword'];
						}
					}
				//clean
				unset($k, $v, $rs);
				//derniere passe on creer le array final 
				//sera raccorci plus tard pour avoir un
				//un total de MAX_ROWS_AUTOCOMPLETE_RETURNED 
				//peu mporte combien il y en a dans chaque
				//soit 4,4,4 ou 2,6,4 ou 8,3,1 etc...
				$arrTotalResultPerKwType = array();
				//loop
				foreach($arrByKwType as $k=>$v){
					//le total dans chaque pour le calcul plus loin
					$arrTotalResultPerKwType[$k] = count($arrByKwType[$k]);
					//le array principal
					$arr[$k] = array();			 
					//on loop dans les mots on les mets tous pour l'instant
					foreach($arrByKwType[$k] as $k2=>$v2){
						array_push($arr[$k], array(
							'id' => $v2,
							'name' => $k2,
							));
						}
					}
				//clean
				unset($arrByKwType, $k, $v, $k2, $v2);
				//maintenant on clacule combien on put en 
				//garder dans chaque array selon le define
				//si en manque dans un on le rajoute 
				//alors a celui en dessous
				//on commence par odre inportance 
				//keyword, short title, code exercice
				$iLeftToPushInOtherKwType = 0;
				$iMaxInEachKwType = MAX_ROWS_AUTOCOMPLETE_RETURNED/count($arr);
				$arrTotalToKeepPerKwType = array();
				//test array for calculation
				// 10,10,10
				// 2,2,10
				// 10,10,2
				// 10,2,10
				// 2,10,2
				/*
				$arrTotalResultPerKwType = array(
					'1' => 10,
					'2' => 10,
					'3' => 10,
					);
				*/
				//loop
				foreach($arrTotalResultPerKwType as $k=>$v){
					//combien dans chaque pour la fin 
					$iHowManyTooPushInKwType = 0;
					//si il nous en reste a pucher dans un autre
					if(($v - ($iMaxInEachKwType + $iLeftToPushInOtherKwType)) < 0){
						//ce que lon peut pusher vu que on les a
						$iHowManyTooPushInKwType = $v;
						//on keep combien n peut en agrder de plus	
						$iLeftToPushInOtherKwType = $iMaxInEachKwType - $v;
					}else{
						//ce que lon peut pusher vu que on les a
						$iHowManyTooPushInKwType = $iMaxInEachKwType + $iLeftToPushInOtherKwType;
						//sinon on en ajoute pas
						$iLeftToPushInOtherKwType = 0;
						}
					//on ajoute combien on en garde par kwtype 
					//pour raccouci 
					//le array de recult que l'on envoi
					$arrTotalToKeepPerKwType[$k] = $iHowManyTooPushInKwType;
					}
				//clean
				unset($k, $v);	
				//si il nous en reste a la fin alors on ressaye de 
				//les pusher de nouveau au debut si il ne sont pas vide
				if($iLeftToPushInOtherKwType > 0){
					foreach($arrTotalResultPerKwType as $k=>$v){
						//check si on en a de left over
						if(($v - ($arrTotalToKeepPerKwType[$k] + $iLeftToPushInOtherKwType)) > 0){
							//on rajoute a ce quil peut garder
							$arrTotalToKeepPerKwType[$k] += $iLeftToPushInOtherKwType;
							//on pourrait aller plus loin en vrifiant 
							//si il peut juste en mettre 1
							//mais on ne vira pas fou non plus
							break;
							}
						}
					}
				//clean
				unset($arrTotalResultPerKwType, $k, $v);
				//mainternant on sait combien dans chaque alors 
				//on raccourci chacun des array de retour
				//print_r($arr).EOL;
				foreach($arrTotalToKeepPerKwType as $k=>$v){
					$arr[$k] = array_slice($arr[$k], 0, $v);
					}
				//for fun 
				if($this->bTrace){
					print_r($arr).EOL.EOL;
					}
				//on va pitcher ca
				return array(
					'result' => $arr,
					'cword' => $data['word'],
					'reg' => $strPregMatch,		
					);
				}
			//for fun 
			if($this->bTrace){
				print_r($arr).EOL.EOL;
				}
			//on va pitcher ca		
			return array(
				'result' => $arr,
				'cword' => $data['word'],
				'reg' => '',		
				);
			}
		return array(
			'result' => $arr,
			'cword' => '',
			'reg' => '',
			);
		}	
		
	//------------------------------------------------------------------------
	private function wordPermutationForSQL3($items, $strPermuted = '', $perms = array()){
		
		/*
		//
		"prone"
		"prone%"
		" prone%"
		"% prone%"
		//
		"prone% plank%"
		"plank% prone%"
		"% prone% plank%"
		"% plank% prone%"	
		*/
		if(empty($items)){
			//juste un seul
			if(count($perms) === 1){
				//word
				$strWord = $perms[0];
				//le like
				$strLike = 'LCASE(keyword.keyword) LIKE ';	
				//posssibilities
				$strPermuted .= $strLike.'"'.$strWord.'"'; 
				//on rajoute le or
				$strLike = ' OR '.$strLike;
				//on continue
				$strPermuted .= $strLike.'"'.$strWord.'%"'; 
				$strPermuted .= $strLike.'"% '.$strWord.'%"'; 
			}else{
				//le like
				$strLike = 'LCASE(keyword.keyword) LIKE "';	
				//si premier pas de or
				if($strPermuted != ''){
					$strLike = ' OR '.$strLike;
					}
				//str
				$strPermuted .= $strLike;
				//text utf-8	sans espace avant
				for($i=0; $i<count($perms); $i++){
					$strWord = $perms[$i];
					//sans espace avant
					if($i == 0){ 
						//le premier du array
						$strPermuted .= ''.$strWord.'%'; 
					}else{
						//ceux dans le milieu
						$strPermuted .= ' '.$strWord.'%';
						}
					}
				$strPermuted .= '"';
				//le like
				$strLike = ' OR LCASE(keyword.keyword) LIKE "';	
				//str
				$strPermuted .= $strLike;
				//text utf-8	avec espace avant
				for($i=0; $i<count($perms); $i++){
					$strWord = $perms[$i];
					//sans espace avant
					if($i == 0){ 
						//le premier du array
						$strPermuted .= '% '.$strWord.'%'; 
					}else{
						//faut enlever le dernier chars rajouter 
						//par les autres condition precedente
						$strPermuted = 	substr($strPermuted, 0, (strlen($strPermuted) - 1));
						//ceux dans le milieu
						$strPermuted .= '% '.$strWord.'%';
						}
					}
				$strPermuted .= '"';
				//
				}
		}else{
			for($i=count($items)-1; $i>=0; --$i){
				$newitems = $items;
				$newperms = $perms;
				list($foo) = array_splice($newitems, $i, 1);
				array_unshift($newperms, $foo);
				$strPermuted = $this->wordPermutationForSQL3($newitems, $strPermuted,  $newperms);
				}
			}
		return $strPermuted;
		}	
			
	//------------------------------------------------------------------------
	private function fetchAutoCompleteData3($data){
		if($this->bTrace){
			echo $this->className.'->'.'fetchAutoCompleteData3('.$data.')'.EOL.EOL;
			}
		//get the data from word search in the input box::input-main-exercice-search-autocomplete
		/*
		POUR PROTO: 003 mots cles et on va mettre un flag 0 = keywords, 1 = title, 2=code exercice (2  va prendre le bord vu quil sont dans keyword)
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
				},
			kword: "hip" -> le kw stripper de tout truc pas bon	
		*/
		$arr = array();
		if(isset($data['word']) && !empty($data['word'])){
			$strQueryInKwType = '1';	
			if(isset($data['kwtype']) && $data['kwtype'] != ''){	
				$strQueryInKwType = $data['kwtype'];	
				}
			$arrLocale	= array('en_US','fr_CA');
			//$arrLocale	= array('en_US');
			$strQueryInLocale = '"'.implode('","', $arrLocale).'"';	
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
			//with base test word : "   gA.   E$(*&  à  "	
			//doit donner a la fin: "ga e à"
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
			//on lowercase
			$data['word'] = mb_strtolower($data['word'], 'UTF-8');
			if($this->bTrace){
				echo ('WORD[3]:"'.$data['word'].'"').EOL;
				}
			//et on garde seulement les caracteres valide pour 
			//le reg ex c'est a dire du [a-z 0-9] et space et 
			//extended ascii chars
			//$data['word'] = preg_replace('/[^a-zA-Z0-9\sÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ]/', '', $data['word']);
			//vu que l,on a minuscule pas besoin de trimmer les majuscule
			$data['word'] = preg_replace('/[^a-zA-Z0-9\sÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ]/', '', $data['word']);
			if($this->bTrace){
				echo ('WORD[4]:"'.$data['word'].'"').EOL;
				}
			//pour le word encode et decode
			$arrWordData = array();
			//ca cest pour la grosse marde dans la DB pour etre certain
			//que tout soit tres tres lent lent
			$arrWordDataHtmlEncoded = array();
			$arrWordDataTmp =  explode(' ', $data['word']);
			//just un check au cas ou mais devrait pas avoir besoin de ca
			foreach($arrWordDataTmp AS $k=>$v){
				//check si vide ou ' ' ou si on doit en plus avoir 
				//un charctere encode en html car le data est de la grosse marde
				if($arrWordDataTmp[$k] != '' && $arrWordDataTmp[$k] != ' '){
					array_push($arrWordData, $arrWordDataTmp[$k]);
					//on check si est html encode on doit le rajouter
					//a un autre array poiur etre certain que tout ca finira
					//par etre pas du tout performant et supre lent
					//pourquoi faire une condition quand on peut en faire 10
					array_push($arrWordDataHtmlEncoded, htmlentities($arrWordDataTmp[$k], ENT_COMPAT|ENT_IGNORE, 'UTF-8', false));
					}
				}
			//clean
			unset($k, $v);	
			//on compare les deux array pour voir sil sont pareils 
			//on en garde juste un, sinon on ralenti le tout en rajoutant
			//des conditions de marde inutile
			$bKeepCompareWithBothWordArrayData = false;
			foreach($arrWordData as $k=>$v){
				if($arrWordData[$k] != $arrWordDataHtmlEncoded[$k]){
					//ben tin on va garder les deux tant qua ralentir
					$bKeepCompareWithBothWordArrayData = true;
					break;
					}
				}
			//clean
			unset($k, $v);		
			//show for debug fun		
			if($this->bTrace){
				echo '$bKeepCompareWithBothWordArrayData:'.$bKeepCompareWithBothWordArrayData.EOL;;
				echo '$arrWordData:';
				print_r($arrWordData).EOL;
				echo '$arrWordDataHtmlEncoded:';	
				print_r($arrWordDataHtmlEncoded).EOL;
				}
			//straight chars not html encoded	
			$strPregMatch = $this->wordPermutationForSQL3(array_slice($arrWordData, 0, $this->iMaxPermutation));
			//html encoded word	
			if($bKeepCompareWithBothWordArrayData){
				$strPregMatchHtml = $this->wordPermutationForSQL3(array_slice($arrWordDataHtmlEncoded, 0, $this->iMaxPermutation));
				}
			//la query on order by the rank
			// le kwtype n'est pas encore dans la base de donnees et
			// sera la representation	
			$query = 'SELECT keyword.kwtype AS kwtype, keyword.keyword, keyword.idKeyword FROM keyword_exercise, mod_exercise, keyword, keyword_rank WHERE keyword.idKeyword = keyword_rank.idKeyword AND mod_exercise.idModule IN ('.implode(',', array_keys($moduleArr)).') AND keyword.idLicence IN (0,'.intval($idLicence).') AND keyword.idKeyword = keyword_exercise.idKeyword AND keyword_exercise.idExercise = mod_exercise.idExercise AND keyword.locale IN('.$strQueryInLocale.') AND keyword_rank.locale IN ('.$strQueryInLocale.') AND keyword.kwtype IN ('.$strQueryInKwType.') AND ';
			//on va ralentir le tout car on adore quand cest non performat
			if($bKeepCompareWithBothWordArrayData){
				$query .= '('.$strPregMatch.' OR '.$strPregMatchHtml.')';
			}else{
				//ouf ca va enfin aller vite hahaha!
				$query .= '('.$strPregMatch.')';
				}
			$query .= ' GROUP BY keyword.idKeyword ORDER BY keyword_rank.rank DESC;';
			//for the fun of debuuugiing
			if($this->bTrace){
				echo $query.EOL.EOL;
				}
			//
			$rs = $this->reg->get('db')->query($query);
			//si il y a
			if($rs && $rs->num_rows){
				//on va maintenant mettre en group de kwtype, et de word
				//car il se peut que un word soit de type different 
				//comme "flexion"
				//qui est de type keyword et shorttitle
				//en meme temps on check si on a atteint notre max 
				//dans tout les kwtype que l'on a	
				$arrByKwType = array();
				//loop
				foreach($rs->rows as $k=>$v){
					//la string encode
					$strKeyword = html_entity_decode(mb_strtolower($v['keyword'], 'UTF-8'), ENT_NOQUOTES|ENT_HTML401, 'UTF-8');
					//check si existe
					//mais foudrait voir avec un flag si les a tous cree
					//alors on bypass cette condition genre ils sont tous //cree alors ne pas check le isset
					if(!isset($arrByKwType[$v['kwtype']])){
						$arrByKwType[$v['kwtype']] = array();
						}
					//on push le data dedans selon le word 
					//on va additionner les keywordId
					//pour ne finir qu'avec un word et plusieurs keywordId
					if(!isset($arrByKwType[$v['kwtype']][$strKeyword])){
						//on le creer
						$arrByKwType[$v['kwtype']][$strKeyword] = $v['idKeyword'];
					}else{
						//on rajoute le kw id
						$arrByKwType[$v['kwtype']][$strKeyword] .= ','.$v['idKeyword'];
						}
					}
				//clean
				unset($k, $v, $rs);
				//on va pitcher ca
				return array(
					'result' => $this->buildArrayForFetchDataRtn($arrByKwType),
					'cword' => $data['word'],
					'reg' => $strPregMatch,		
					);
			}else{
				//NB: on se rend uniquement si on a un seul mot pour plusieur
				//il va falloir des serveurs de calcul, car une permutation
				//de plusieurs mot long on peut vite atteindre un regexp
				//de plusieurs milliers de ligne

				//lusager ne sait pas ecrire alors on 
				//tente de le corriger un peu
				if($this->bWordsForDummies && (count($arrWordData) === 1)){
					//juste le premier mot au cas ou
					if(isset($arrWordData[0])){
						//on va changer le type pour uniquement chercher dans le kwtype = 1
						//on reessaye avec un reagex du premier mot transforme
						return $this->fetchAutoCompleteDataWithRegexDummy($data);		
						}
					}
				}
			//for fun 
			if($this->bTrace){
				print_r($arr).EOL.EOL;
				}
			//on va pitcher ca		
			return array(
				'result' => $arr, //vide
				'cword' => $data['word'],
				'reg' => '',		
				);
			}
		return array(
			'result' => $arr, //vide
			'cword' => '',
			'reg' => '',
			);
		}	
	
	//------------------------------------------------------------------------
	private function fetchAutoCompleteDataWithRegexDummy($data){
		if($this->bTrace){
			echo $this->className.'->'.'fetchAutoCompleteDataWithRegexDummy('.$data.')'.EOL.EOL;
			}
		$arr = array();
		if(isset($data['word']) && !empty($data['word'])){
			//vu que l'usager ecrit n'iimporte quoi on va seulement chertcher dans 
			//les keyword sinon ca va etre trop long et gele le serveur
			$strQueryInKwType = '1';	
			$arrLocale	= array('en_US','fr_CA');
			$strQueryInLocale = '"'.implode('","', $arrLocale).'"';	
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
			//pour le word encode et decode
			$arrWordData = array();
			//ca cest pour la grosse marde dans la DB pour etre certain
			//que tout soit tres tres lent lent
			$arrWordDataTmp = explode(' ', $data['word']);
			//just un check au cas ou mais devrait pas avoir besoin de ca
			foreach($arrWordDataTmp AS $k=>$v){
				//check si vide ou ' ' ou si on doit en plus avoir 
				//un charctere encode en html car le data est de la grosse marde
				if($arrWordDataTmp[$k] != '' && $arrWordDataTmp[$k] != ' '){
					array_push($arrWordData, $arrWordDataTmp[$k]);
					}
				}
			//clean
			unset($k, $v);	
			//show for debug fun		
			if($this->bTrace){
				echo '$arrWordData:';
				print_r($arrWordData).EOL;
				}
			//on essye d'une facon et d'une autre au dela on a rien et on sen va	
			/*
			if($this->bWeWillTryHarder){
				//premier try
				if($this->bWeWillTryEverything){ 
					//on y va avec le premier mot qui sera manipule 
					//de toute les facons possibles
					$strPregMatch = $this->createMultipleWordsPermutationForDummies($arrWordData[0]);
				}else{
					//on y va seulement avec le premier mot
					$strPregMatch = $this->createWordsPermutationForDummies($arrWordData[0]);
					}
			}else{
				//on y va seulement avec le premier mot
				$strPregMatch = $this->createWordsForDummies($arrWordData[0]);
				}
			*/
			if($this->bWeWillTryHarder){
				//premier try
				if($this->bWeWillTryEverything){ 
					//on y va avec le premier mot qui sera manipule 
					//de toute les facons possibles
					$strPregMatch = $this->createMultipleWordsPermutationForDummies($arrWordData[0]);
				}else{
					//on y va seulement avec le premier mot
					$strPregMatch = $this->createWordsPermutationForDummies($arrWordData[0]);
					}
			}else{
				//on y va seulement avec le premier mot et les autres qui seront tranforme
				$strPregMatch = $this->createWordsArrForDummies($arrWordData);
				}
			
			//la query on order by the rank
			$query = 'SELECT keyword.kwtype AS kwtype, keyword.keyword, keyword.idKeyword FROM keyword_exercise, mod_exercise, keyword, keyword_rank WHERE keyword.idKeyword = keyword_rank.idKeyword AND mod_exercise.idModule IN ('.implode(',', array_keys($moduleArr)).') AND keyword.idLicence IN (0,'.intval($idLicence).') AND keyword.idKeyword = keyword_exercise.idKeyword AND keyword_exercise.idExercise = mod_exercise.idExercise AND keyword.locale IN('.$strQueryInLocale.') AND keyword_rank.locale IN ('.$strQueryInLocale.') AND keyword.kwtype IN ('.$strQueryInKwType.') AND keyword.keyword REGEXP "'.$strPregMatch.'" GROUP BY keyword.idKeyword ORDER BY keyword_rank.rank DESC;';
			//for the fun of debuuugiing
			if($this->bTrace){
				echo $query.EOL.EOL;
				}
			//
			$rs = $this->reg->get('db')->query($query);
			//si il y a
			if($rs && $rs->num_rows){
				//on va maintenant mettre en group de kwtype, et de word
				//car il se peut que un word soit de type different 
				//comme "flexion"
				//qui est de type keyword et shorttitle
				//en meme temps on check si on a atteint notre max 
				//dans tout les kwtype que l'on a	
				$arrByKwType = array();
				//loop
				foreach($rs->rows as $k=>$v){
					//la string encode
					$strKeyword = html_entity_decode(mb_strtolower($v['keyword'], 'UTF-8'), ENT_NOQUOTES|ENT_HTML401, 'UTF-8');
					//check si existe
					//mais foudrait voir avec un flag si les a tous cree
					//alors on bypass cette condition genre ils sont tous //cree alors ne pas check le isset
					if(!isset($arrByKwType[$v['kwtype']])){
						$arrByKwType[$v['kwtype']] = array();
						}
					//on push le data dedans selon le word 
					//on va additionner les keywordId
					//pour ne finir qu'avec un word et plusieurs keywordId
					if(!isset($arrByKwType[$v['kwtype']][$strKeyword])){
						//on le creer
						$arrByKwType[$v['kwtype']][$strKeyword] = $v['idKeyword'];
					}else{
						//on rajoute le kw id
						$arrByKwType[$v['kwtype']][$strKeyword] .= ','.$v['idKeyword'];
						}
					}
				//clean
				unset($k, $v, $rs);
				//on va pitcher ca
				//on va pitcher ca
				return array(
					'result' => $this->buildArrayForFetchDataRtn($arrByKwType),
					'cword' => $data['word'],
					'reg' => $strPregMatch,		
					);
			}else{
		
				//NB: on se rend uniquement si on a un seul mot pour plusieur
				//il va falloir des serveurs de calcul, car une permutation
				//de plusieurs mot long on peut vite atteindre un regexp
				//de plusieurs milliers de ligne
				
				

				//lusager ne sait toujours pas ecrire alors on 
				//tente de le corriger un peu en permutant son mot de base
				if($this->bWordsPermutationForDummies && !$this->bWeWillTryHarder && (count($arrWordData) === 1)){
					//comme quoi on essaye fort de lui plaire 
					$this->bWeWillTryHarder = true;
					//juste le premier mot au cas ou
					if(isset($arrWordData[0])){
						//on reessaye avec un reagex du premier mot transforme
						return $this->fetchAutoCompleteDataWithRegexDummy($data);		
						}
				}else if($this->bWordsPermutationForDummies && !$this->bWeWillTryEverything  && (count($arrWordData) === 1)){
					//comme quoi on aura tout esssayer 
					//mais il va falloir qu'il se corrige
					$this->bWeWillTryEverything = true;
					//juste le premier mot au cas ou
					if(isset($arrWordData[0])){
						//on reessaye avec un reagex du premier mot transforme
						return $this->fetchAutoCompleteDataWithRegexDummy($data);		
						}
				}else{

					//ou on a plusieurs mot ce qui est trop demandant ou

					//l usager ne sait tout simplement pas utiliser un clavier
					//et devrait utiliser un crayon et un papier et on lui 
					//donne un timbre pour qu'il envoi sa requete par la poste
					//qui sera lu et traite par un humain dote dun cerveau
					//en remplacement du sien
					}
				}
			//for fun 
			if($this->bTrace){
				print_r($arr).EOL.EOL;
				}
			//on va pitcher ca		
			return array(
				'result' => $arr, //vide
				'cword' => $data['word'],
				'reg' => '',		
				);
			}
		return array(
			'result' => $arr, //vide
			'cword' => '',
			'reg' => '',
			);
		}	
		
	//------------------------------------------------------------------------
	private function createWordsForDummies($word){
		if($this->bTrace){
			echo $this->className.'->'.'createWordsForDummies('.$word.')'.EOL.EOL;
			}
		/*
		EX:	1. "flxion"	= "flexion"
			2. "hnch" 	= "hanche"
			3. "kenn" 	= "knee"

		1. 	run regex on "f[\w]{1,2}lxion.*"
			run regex on "fl[\w]{1,2}xion.*" 
			run regex on "fl[\w]{1,2}ion.*" = "flexion" alors on retourne celui-ci 
		
		2. 	same as 1
		
		3. 	same as 1 mais ramene rien alors on permute les lettres
			excepte la premiere quand meme sinon faut vraiment etre epais
			et pas savoir ecrire
			
			run regex on "kenn"
			run regex on "knen"
			run regex on "knne"
			run regex on "k[\w]{1}nee.*"
			
		4. 	si a ecrit "kenn" et aucun retour souvent il corrige sont mot 
			et refait une recherche avec "knee" donc on pourrait garder comme
			mot "kenn" = "knee" et lui propose les "knee" la prochaine fois
			qu'il fait la meme erreur	
			
					
		*/
		//arr des mots a retenir
		$strRegex = '';
		//de un on va remplacer les chars accentue sait on jamais on
		//peut les avoir dans notre data de marde mais pas vec les accents
		//on double triple encode ou whatever
		$word = convertChar($word);
		//on creer un couple de mot de remplacement
		for($i=0;$i<strlen($word);$i++){
			$strLeft = '';
			for($j=0;$j<strlen($word)-(strlen($word)-($i+1));$j++){
				$strLeft .= $word{$j};
				}
			$strRight = '';
			for($j=($i + 1);$j<strlen($word);$j++){
				$strRight .= $word{$j};
				}
			$strRegex .= '(^'.$strLeft.'[[:alnum:]]{1,2}'.$strRight.'[[:alnum:]]*)|';
			}
		//strip
		if($strRegex != ''){
			$strRegex = substr($strRegex, 0, (strlen($strRegex) - 1));
			}
		//show
		if($this->bTrace){
			echo '$strRegex:"'.$strRegex.'"'.EOL.EOL;
			}
			
		//le retour
		return $strRegex;	
		}


	//------------------------------------------------------------------------
	private function createWordsArrForDummies($arr){
		if($this->bTrace){
			echo $this->className.'->'.'createWordsArrForDummies('.$arr.')'.EOL.EOL;
			}
		/*
		EX:	1. "flxion"	= "flexion"
			2. "hnch" 	= "hanche"
			3. "kenn" 	= "knee"

		1. 	run regex on "f[\w]{1,2}lxion.*"
			run regex on "fl[\w]{1,2}xion.*" 
			run regex on "fl[\w]{1,2}ion.*" = "flexion" alors on retourne celui-ci 
		
		2. 	same as 1
		
		3. 	same as 1 mais ramene rien alors on permute les lettres
			excepte la premiere quand meme sinon faut vraiment etre epais
			et pas savoir ecrire
			
			run regex on "kenn"
			run regex on "knen"
			run regex on "knne"
			run regex on "k[\w]{1}nee.*"
			
		4. 	si a ecrit "kenn" et aucun retour souvent il corrige sont mot 
			et refait une recherche avec "knee" donc on pourrait garder comme
			mot "kenn" = "knee" et lui propose les "knee" la prochaine fois
			qu'il fait la meme erreur	
			
					
		*/
		//le premier mots
		$word = $arr[0];
		//arr des mots a retenir
		$strRegex = '';
		//de un on va remplacer les chars accentue sait on jamais on
		//peut les avoir dans notre data de marde mais pas vec les accents
		//on double triple encode ou whatever
		$word = convertChar($word);
		//on creer un couple de mot de remplacement
		for($i=0;$i<strlen($word);$i++){
			$strLeft = '';
			for($j=0;$j<strlen($word)-(strlen($word)-($i+1));$j++){
				$strLeft .= $word{$j};
				}
			$strRight = '';
			for($j=($i + 1);$j<strlen($word);$j++){
				$strRight .= $word{$j};
				}
			$strRegex .= '(^'.$strLeft.'[[:alnum:]]{1,2}'.$strRight.'[[:alnum:]]*)|';
			}
		//strip
		if($strRegex != ''){
			$strRegex = substr($strRegex, 0, (strlen($strRegex) - 1));
			}
		//show
		if($this->bTrace){
			echo '$strRegex:"'.$strRegex.'"'.EOL.EOL;
			}
			
		//le retour
		return $strRegex;	
		}
	
	//------------------------------------------------------------------------
	private function createWordsPermutationForDummies($word){
		if($this->bTrace){
			echo $this->className.'->'.'createWordsPermutationForDummies('.$word.')'.EOL.EOL;
			}
		//arr des mots a retenir
		$strRegex = '';
		//de un on va remplacer les chars accentue sait on jamais on
		//peut les avoir dans notre data de marde mais pas vec les accents
		//on double triple encode ou whatever
		$word = convertChar($word);
		//on va permuter seulement les iMaxCharsPermutation premier chars au dela 
		//on a pas les serverus pour on est pas google quand meme
		$arrChars = str_split($word);
		//
		//car doit au moins commenc er apr ca sinon c'est un peu n'importquoi qu'il tape
		//on garde le premier charatere
		$firstChar = $arrChars[0];
		//le reste des lettres
		$arrChars = array_slice($arrChars, 1, $this->iMaxCharsPermutation);
		//
		$strRegex = $this->charsPermutationForRegex($firstChar, $arrChars);	
		//strip
		if($strRegex != ''){
			$strRegex = substr($strRegex, 0, (strlen($strRegex) - 1));
			}
		//show
		if($this->bTrace){
			echo '$strRegex:"'.$strRegex.'"'.EOL.EOL;
			}
		//le retour
		return $strRegex;	
		}
	
	//------------------------------------------------------------------------
	private function createMultipleWordsPermutationForDummies($word){
		if($this->bTrace){
			echo $this->className.'->'.'createMultipleWordsPermutationForDummies('.$word.')'.EOL.EOL;
			}

		//arr des mots a retenir
		$strRegex = '';
		//de un on va remplacer les chars accentue sait on jamais on
		//peut les avoir dans notre data de marde mais pas vec les accents
		//on double triple encode ou whatever
		$word = convertChar($word);
		//on va permuter ce mot de toute les facon possible
		//en prenant seulement les 4 premier characteres
		if($this->bTrace){
			echo '$word:"'.$word.'"'.EOL;
			}
		//on va chercher un array de mots permutte de toute les facons
		$arrChars = str_split($word);
		//car doit au moins commenc er apr ca sinon c'est un peu n'importquoi qu'il tape
		//on garde le premier charatere
		$firstChar = $arrChars[0];
		//le reste des lettres
		$arrChars = array_slice($arrChars, 1, $this->iMaxCharsPermutation);
		//les mots possible
		$arrWords = $this->charsPermutation($arrChars);
		//show
		if($this->bTrace){
			echo '$arrWords:"';
			print_r($arrWords).EOL.EOL;
			}
		//on va creer le regexp sql de toute possibilite
		foreach($arrWords as $k=>$v){
			$strRegex .= $this->createWordsForDummies($firstChar.$v);
			}
		//show
		if($this->bTrace){
			echo '$strRegex:"'.$strRegex.'"'.EOL.EOL;
			}
		//le retour
		return $strRegex;	
		}
	
	//------------------------------------------------------------------------
	private function buildArrayForFetchDataRtn($arrByKwType){
		//derniere passe on creer le array final 
		//sera raccorci plus tard pour avoir un
		//un total de MAX_ROWS_AUTOCOMPLETE_RETURNED 
		//peu mporte combien il y en a dans chaque
		//soit 4,4,4 ou 2,6,4 ou 8,3,1 etc...
		$arr = array();
		$arrTotalResultPerKwType = array();
		//loop
		foreach($arrByKwType as $k=>$v){
			//le total dans chaque pour le calcul plus loin
			$arrTotalResultPerKwType[$k] = count($arrByKwType[$k]);
			//le array principal
			$arr[$k] = array();			 
			//on loop dans les mots on les mets tous pour l'instant
			foreach($arrByKwType[$k] as $k2=>$v2){
				array_push($arr[$k], array(
					'id' => $v2,
					'name' => $k2,
					));
				}
			}
		//clean
		unset($arrByKwType, $k, $v, $k2, $v2);
		//maintenant on clacule combien on put en 
		//garder dans chaque array selon le define
		//si en manque dans un on le rajoute 
		//alors a celui en dessous
		//on commence par odre inportance 
		//keyword, short title, code exercice
		$iLeftToPushInOtherKwType = 0;
		$iMaxInEachKwType = MAX_ROWS_AUTOCOMPLETE_RETURNED/count($arr);
		$arrTotalToKeepPerKwType = array();
		//test array for calculation
		// 10,10,10
		// 2,2,10
		// 10,10,2
		// 10,2,10
		// 2,10,2
		/*
		$arrTotalResultPerKwType = array(
			'1' => 10,
			'2' => 10,
			'3' => 10,
			);
		*/
		//loop
		foreach($arrTotalResultPerKwType as $k=>$v){
			//combien dans chaque pour la fin 
			$iHowManyTooPushInKwType = 0;
			//si il nous en reste a pucher dans un autre
			if(($v - ($iMaxInEachKwType + $iLeftToPushInOtherKwType)) < 0){
				//ce que lon peut pusher vu que on les a
				$iHowManyTooPushInKwType = $v;
				//on keep combien n peut en agrder de plus	
				$iLeftToPushInOtherKwType = $iMaxInEachKwType - $v;
			}else{
				//ce que lon peut pusher vu que on les a
				$iHowManyTooPushInKwType = $iMaxInEachKwType + $iLeftToPushInOtherKwType;
				//sinon on en ajoute pas
				$iLeftToPushInOtherKwType = 0;
				}
			//on ajoute combien on en garde par kwtype 
			//pour raccouci 
			//le array de recult que l'on envoi
			$arrTotalToKeepPerKwType[$k] = $iHowManyTooPushInKwType;
			}
		//clean
		unset($k, $v);	
		//si il nous en reste a la fin alors on ressaye de 
		//les pusher de nouveau au debut si il ne sont pas vide
		if($iLeftToPushInOtherKwType > 0){
			foreach($arrTotalResultPerKwType as $k=>$v){
				//check si on en a de left over
				if(($v - ($arrTotalToKeepPerKwType[$k] + $iLeftToPushInOtherKwType)) > 0){
					//on rajoute a ce quil peut garder
					$arrTotalToKeepPerKwType[$k] += $iLeftToPushInOtherKwType;
					//on pourrait aller plus loin en vrifiant 
					//si il peut juste en mettre 1
					//mais on ne vira pas fou non plus
					break;
					}
				}
			}
		//clean
		unset($arrTotalResultPerKwType, $k, $v);
		//mainternant on sait combien dans chaque alors 
		//on raccourci chacun des array de retour
		//print_r($arr).EOL;
		foreach($arrTotalToKeepPerKwType as $k=>$v){
			$arr[$k] = array_slice($arr[$k], 0, $v);
			}
		//for fun 
		if($this->bTrace){
			print_r($arr).EOL.EOL;
			}
		return $arr;
		
		}
	
	//------------------------------------------------------------------------
	private function charsPermutationForRegex($firstChar, $items, $strPermuted = '', $perms = array()){
		//
		if(empty($items)){ 
			$strPermuted .= '(^'.$firstChar;
			for($i=0; $i<count($perms); $i++){
				$strPermuted .= $perms[$i];
				}
			//close
			$strPermuted .= '.*)|';
		}else{
			for($i=count($items)-1; $i>=0; --$i){
				$newitems = $items;
				$newperms = $perms;
				list($foo) = array_splice($newitems, $i, 1);
				array_unshift($newperms, $foo);
				$strPermuted = $this->charsPermutationForRegex($firstChar, $newitems, $strPermuted,  $newperms);
				}
			}
		return $strPermuted;
		}
	
	//------------------------------------------------------------------------
	private function charsPermutation($items, $arrPermuted = array(), $perms = array()){
		//
		$strPermuted = '';
		//
		if(empty($items)){ 
			for($i=0; $i<count($perms); $i++){
				$strPermuted .= $perms[$i];
				}
			array_push($arrPermuted, $strPermuted);
		}else{
			for($i=count($items)-1; $i>=0; --$i){
				$newitems = $items;
				$newperms = $perms;
				list($foo) = array_splice($newitems, $i, 1);
				array_unshift($newperms, $foo);
				$arrPermuted = $this->charsPermutation($newitems, $arrPermuted,  $newperms);
				}
			}
		return $arrPermuted;
		}

	};


//END