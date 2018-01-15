<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	search utilities and manipulation for exercise serach
@inst:	
		
		

*/
//------------------------------------------------------------------------

class ExSearch {
		
	//values	
	private $className = 'ExSearch';
	private $bTrace = false;
	private $bExecute = true;	
	private $bTryHarderInTable = true;
	private $bTryHarderInUserExerciseTable = true;
	private $bStripAllAccent = true;
	private $idUser = 0;
	private $idLicence = 0;
	private $isAdmin = false;
	private $strDefaultLocale = 'en_US';
	private $strUserLocale = '';
	private $iCountKeyword = 0;	
	private $strKeyword = '';	
	private $bChangeRank = true;	
	private $bSubmitKwForRevision = true;
	private $iMinLenghtKw = 2;		

	//arrays
	private $arrKeywords = array();		
	private $arrUser = array();
	private $arrModules = array();
	private $arrTryWithAccentLocale = array(
		'fr_CA',
		'fr_FR',
		);
			
	//by params object
	private $reg = false;	
	
	//by params values
	private $idModule = 0;		
	private $bFavOnly = false;

	//------------------------------------------------------------------------
	public function __construct(&$reg, $data){
		//registry	
		$this->reg = $reg;
		//la locale
		$this->strUserLocale = $this->reg->get('sess')->get('locale');
		//la license
		$arrLicenses = $this->reg->get('sess')->get('licence');
		if(!count($arrLicenses)){
			$this->idLicence = 0;	
		}else{
			$this->idLicence = $arrLicenses['idLicence'];	
			}
		//les modules
		if(!isset($data['module']) || (isset($data['module']) && intVal($data['module']) == '-1')){
			$this->arrModules = $this->reg->get('utils')->getClinicModule();
		}else{
			//on set celui qui est passe en params
			$this->arrModules = array(
				intVal($data['module']) => 'none'
				);
			}
		//les string et autres
		$this->idUser = intVal($this->reg->get('sess')->get('idUser'));
		$this->isAdmin = intVal($this->reg->get('sess')->get('isAdmin'));
		//les arrayrs avec acces au methode du caller class
		$this->arrUser = $this->reg->get('utils')->getClinicUsers();
		//
		return true;
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
	private function trace($lineNumber, $funcName, $args = false){
		if($this->bTrace){
			$cpu = sys_getloadavg();
			echo '----------------------------------------------------------------------'.EOL;
			echo 'FILE: '.TAB.__FILE__.EOL;
			echo 'LINE: '.TAB.$lineNumber.EOL;
			echo 'CPU: '.TAB.$cpu[0].EOL;
			echo 'MEM:'.TAB.number_format(((memory_get_peak_usage()/1024)/1000), 2).' Mo'.EOL.EOL;
			//method
			echo $this->className.'::'.$funcName.'(';
			//string or object
			if(is_string($args)){
				echo $args;
			}else{
				print_r($args).EOL;
				}
			echo ')'.EOL.EOL;
			}
		}

	//------------------------------------------------------------------------
	private function modifyAccentQueryLike($strKwLike){
		//debug
		$this->trace(__LINE__, __FUNCTION__, array(
			$strKwLike
			));	
		//copy
		$strRtn = $strKwLike;
		$strNoAccent = '';
		if($this->bStripAllAccent){
			//pour touts les charactere a la fois
			//arrChars 
			$arrChars = array(
				'à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï',
				'ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','þ','ÿ');
			$arrCharsReplace = array(
				'a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i',
				'o','n','o','o','o','o','o','o','u','u','u','u','y','p','y');
			//si jamais avait des caractere accentue on va faire la meme requete en plus avec ceux la
			$strNoAccent = str_replace($arrChars, $arrCharsReplace, $strKwLike);	
			if($strNoAccent == $strKwLike){
				$strNoAccent = '';
				}
			}
		//uniquement les 3 premiers comencement de e opn essaye avec des accent aigu
		while(preg_match('/^(.*"[%\s]{0,1}.{0,3})(e{1})(.*)$/U', $strKwLike, $arrMatch)){
			$strKwLike = $arrMatch[1].'é'.$arrMatch[3];
			}
		//check si on rajoute ou pas
		if($strRtn != $strKwLike){
			$strRtn = ' ( '.$strRtn.' ) OR ( '.$strKwLike.' ) ';
			}
		//check si avait des accent a enlever
		if($strNoAccent != ''){
			$strRtn .= ' OR '.$strNoAccent;
			}
		//retour
		return $strRtn;
		}

	//-------------------------------------------------------------------------
	private function wordPermutationForSQL($fieldName, $items, $strType = 'utf-8', $strPermuted = '', $perms = array()){
		//debug
		//$this->trace(__LINE__, __FUNCTION__,func_get_args());
		//
		if(empty($items)){
			//juste un seul
			if(count($perms) === 1){
				//le like
				if($strType == 'utf-8'){
					//word
					$strWord = $perms[0];
					$strLike = $fieldName.' COLLATE utf8_bin LIKE ';	
				}else{
					//word
					$strWord = htmlentities($perms[0], ENT_NOQUOTES|ENT_HTML5, 'UTF-8');
					$strLike = $fieldName.' LIKE ';	
					}
				//posssibilities
				
				//IMPORTANT: a averifier si on doit enlever
				//quand c'est le mot tout seul
				//EX: 'rotation' donne "rotation%" OR "% rotation%"
				if($strType == 'utf-8'){
					$strPermuted .= $strLike.'"'.$strWord.'%"'.' OR '.$strLike.'"% '.$strWord.'%"'; 
				}else{
					$strPermuted .= $strLike.'LCASE("'.$strWord.'%")'.' OR '.$strLike.'LCASE("% '.$strWord.'%")'; 
					}
				
			}else{
				//le like
				if($strType == 'utf-8'){
					$strLike = $fieldName.' COLLATE utf8_bin LIKE "';	
				}else{
					$strLike = $fieldName.' LIKE LCASE("';	
					}
				//si premier pas de or
				if($strPermuted != ''){
					$strLike = ' OR '.$strLike;
					}
				//str
				$strPermuted .= $strLike;
				//text utf-8 sans espace avant
				for($i=0; $i<count($perms); $i++){
					if($strType == 'utf-8'){
						$strWord = $perms[$i];
					}else{
						$strWord = htmlentities($perms[$i], ENT_NOQUOTES|ENT_HTML5, 'UTF-8');
						}
					//sans espace avant
					if($i == 0){ 
						//le premier du array
						$strPermuted .= ''.$strWord.'%'; 
					}else{
						//ceux dans le milieu
						$strPermuted .= ' '.$strWord.'%';
						}
					}
				//le like
				if($strType == 'utf-8'){
					$strPermuted .= '"';
				}else{
					$strPermuted .= '")';
					}
				//le like
				if($strType == 'utf-8'){
					$strLike = ' OR '.$fieldName.' COLLATE utf8_bin LIKE "';	
				}else{
					$strLike = ' OR '.$fieldName.' LIKE LCASE("';	
					}
				//str
				$strPermuted .= $strLike;
				//text utf-8	avec espace avant
				for($i=0; $i<count($perms); $i++){
					if($strType == 'utf-8'){
						$strWord = $perms[$i];
					}else{
						$strWord = htmlentities($perms[$i], ENT_NOQUOTES|ENT_HTML5, 'UTF-8');
						}
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
				//le like
				if($strType == 'utf-8'){
					$strPermuted .= '"';
				}else{
					$strPermuted .= '")';
					}
				//
				}
		}else{
			for($i=count($items)-1; $i>=0; --$i){
				$newitems = $items;
				$newperms = $perms;
				list($foo) = array_splice($newitems, $i, 1);
				array_unshift($newperms, $foo);
				$strPermuted = $this->wordPermutationForSQL($fieldName, $newitems, $strType, $strPermuted, $newperms);
				}
			}
		return $strPermuted;
		}

	//------------------------------------------------------------------------
	private function trimKeyword($str){
		//debug
		$this->trace(__LINE__, __FUNCTION__,func_get_args());
		//clean up des mots
		//clean up des mots
		$str = mb_strtolower($str, 'UTF-8');
		$str = preg_replace('/[^a-zA-Z0-9\sÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ+-]/', ' ', $str);	
		$str = preg_replace('/[\s]+/', ' ', $str);	
		$str = trim($str);	
		
					
		return $str;
		}

	//------------------------------------------------------------------------
	static function uSortKeywordByLen($str0, $str1){
		//compare
		if($str0 == $str1){
			return 0;
		}else if(strlen($str0) > strlen($str1)){
			return -1;
			}
		//
		return 1;	
		}

	//------------------------------------------------------------------------
	private function prepareKeywords($str){
		//debug
		$this->trace(__LINE__, __FUNCTION__,func_get_args());
		//on trim
		$this->strKeyword = $this->trimKeyword($str);		
		//clean up des mots
		if($this->strKeyword != ''){
			$keywordArr = explode(' ', $this->strKeyword);
			foreach($keywordArr as $k1=>$v1){
				if(strlen($v1) < $this->iMinLenghtKw){
					unset($keywordArr[$k1]);
				}else{
					//on check si c'est pas un mot qui est deja la
					//pour eviter les repetition
					foreach($keywordArr as $k2=>$v2){
						if($v1 == $v2 && $k1 != $k2){
							unset($keywordArr[$k1]);
							}
						}
					}
				}
			//si plus de un mot orderer par le mot le plus long 
			//car souvent moins de resultat en sql 
			//ce qui va accelerer les recherches avec IN(exIds)
			if(count($keywordArr) > 1){
				usort($keywordArr, array('ExSearch', 'uSortKeywordByLen'));
				}
			//on garde le array
			$this->arrKeywords = array_values($keywordArr);
			$this->iCountKeyword = count($this->arrKeywords);	
			//debug
			$this->trace(__LINE__, __FUNCTION__.' | debug',array(
				'$this->iCountKeyword' => $this->iCountKeyword,
				'$this->arrKeywords' => $this->arrKeywords,
				));
			}
		}

	//------------------------------------------------------------------------
	private function setFavoriteForExercises(&$arrExercises){
		//debug
		$this->trace(__LINE__, __FUNCTION__);
		//vars
		$arrExerciseIds = array();
		//query
		$query = 'SELECT DISTINCT(idExercise) FROM exercise_favorite WHERE idUser = '. $this->idUser.' AND idExercise IN ('.implode(',', array_keys($arrExercises)).');';
		//debug
		$this->trace(__LINE__,__FUNCTION__,array(
			'$query' => $query
			));
		//prepare db
		$rs = $this->reg->get('db')->query($query);
		if($rs && $rs->num_rows){
			foreach($rs->rows as $k=>$v){
				$arrExercises[$v['idExercise']]['fav'] = 1;
				}
			//clean
			unset($k,$v);
			}
		//clean
		unset($query, $rs);
		}

	//------------------------------------------------------------------------
	private function getExerciseIds($arrFilteredExIds = false){
		//debug
		$this->trace(__LINE__, __FUNCTION__);
		//la clause pour les different type
		$arrWhereClause = array();
		$arrWhereClause['keywords'] = array(); 
		//pour les keywords separe
		foreach($this->arrKeywords as $k=>$v){
			$arrWhereClause['keywords'][$v] = $this->wordPermutationForSQL('keyword.keyword', array($v));
			//modified accent
			if(in_array($this->strUserLocale, $this->arrTryWithAccentLocale)){
				$arrWhereClause['keywords'][$v] = $this->modifyAccentQueryLike($arrWhereClause['keywords'][$v]);
				}
			}
		//clean
		unset($k, $v);
		//debug
		$this->trace(__LINE__, __FUNCTION__.' | $arrWhereClause:', $arrWhereClause);
		//la query de base pour la table keyword
		$strBaseQuery = 'SELECT DISTINCT(keyword_exercise.idExercise), exercise.rank FROM keyword, keyword_exercise, exercise, mod_exercise WHERE keyword.idKeyword = keyword_exercise.idKeyword AND keyword.idLicence IN (0,'.$this->idLicence.') AND mod_exercise.idModule IN ('.implode(',', array_keys($this->arrModules)).') AND keyword_exercise.idExercise = exercise.idExercise AND keyword_exercise.idExercise = mod_exercise.idExercise AND exercise.ready = 1 AND ((exercise.idUser = '.$this->idUser.') OR (exercise.idUser = 0) OR (exercise.idUser IN ('.implode(',', array_keys($this->arrUser)).') AND exercise.shared = 1)) ';
		//la locale
		if($this->strUserLocale == 'en_US'){
			$strBaseQuery .= ' AND keyword.locale = "en_US" ';
		}else{
			$strBaseQuery .= ' AND keyword.locale IN ("en_US","'.$this->strUserLocale.'") ';
			}
		//si on a des exercises filtre
		if(is_array($arrFilteredExIds) && count($arrFilteredExIds)){
			$strBaseQuery .= ' AND keyword_exercise.idExercise IN ('.implode(',',array_keys($arrFilteredExIds)).')';
			}
		//on va chercher la liste une par une des exercise 
		//et on rajoute a la liste des IN() pour en avoir moins
		//a chaque mot car au final on veut tous les mots
		$bFirstRun = true;	
		$arrExerciseIds = array();
		foreach($arrWhereClause['keywords'] as $k=>$v){
			//query
			//pour trouver le mot peu importe ou dans: keyword (1), code exercise(3), et short title(2)
			$query = $strBaseQuery.' AND ('.$v.')';
			//si est pas vide il faut que ce soit une intersection avec ce que l'on a deja
			if(count($arrExerciseIds) > 0){
				$query .= ' AND keyword_exercise.idExercise IN('.implode(',',array_keys($arrExerciseIds)).')';
				//on clean a haque tour 
				//au final va sortir avant que celui-ci soit cleane
				$arrExerciseIds = array();
			}else if(!$bFirstRun){
				//ce n'est pas le premier tour et est vide donc on a rien a chercher
				break;
				}
			$query .= ';';
			//debug
			$this->trace(__LINE__,__FUNCTION__,array(
				'$query' => $query
				));
			//prepare db
			$rs2 = $this->reg->get('db')->query($query);
			if($rs2 && $rs2->num_rows){
				foreach($rs2->rows as $k2=>$v2){
					//on push les ids
					$arrExerciseIds[$v2['idExercise']] = $v2['rank'];
					}
				}
			//clean
			unset($query, $rs2, $k2, $v2);
			//premier tour fini
			$bFirstRun= false;
			}
		
		//debug
		$this->trace(__LINE__,__FUNCTION__,array(
			'count($arrExerciseIds)' => count($arrExerciseIds),
			));
		//clean
		unset($k, $v);
		//on va sorter par rank
		arsort($arrExerciseIds, SORT_NUMERIC);
		//on slice pour garder selon le max
		$arrExerciseIds = array_slice($arrExerciseIds, 0, MAX_SEARCH_NUM_ROWS, true);
		//return 
		return $arrExerciseIds;	
		}

	//------------------------------------------------------------------------
	//mem chose que 'getExerciseIds' mais en lusieurs requete tres differente
	private function getExerciseIdsMultiplePass($arrFilteredExIds = false){
		//debug
		$this->trace(__LINE__, __FUNCTION__);
		//la clause pour les different type
		$arrExerciseIds = array();
		//la query de base pour la table keyword
		$strBaseQuery = 'SELECT idKeyword FROM keyword WHERE idLicence IN (0,'.$this->idLicence.') ';
		//la locale
		if($this->strUserLocale == 'en_US'){
			$strBaseQuery .= ' AND locale = "en_US" ';
		}else{
			$strBaseQuery .= ' AND locale IN ("en_US","'.$this->strUserLocale.'") ';
			}
		//
		$bFirstRun = true;	
		foreach($this->arrKeywords as $k=>$v){
			//where
			$strWhereClause = $this->wordPermutationForSQL('keyword', array($v));
			//modified accent
			if(in_array($this->strUserLocale, $this->arrTryWithAccentLocale)){
				$strWhereClause = $this->modifyAccentQueryLike($strWhereClause);
				}
			//query
			$query = $strBaseQuery.' AND ('.$strWhereClause.');';
			//debug
			$this->trace(__LINE__,__FUNCTION__,array(
				'$query' => $query
				));
			//
			$arrKeywordIds = array();
			//prepare db
			$rs = $this->reg->get('db')->query($query);
			if($rs && $rs->num_rows){
				foreach($rs->rows as $k2=>$v2){
					//on push les ids
					array_push($arrKeywordIds, $v2['idKeyword']);
					}	
				}
			//clean
			unset($query, $rs, $k2, $v2);
			//minor check
			if(count($arrKeywordIds)){
				$query = 'SELECT DISTINCT(idExercise) FROM keyword_exercise WHERE idKeyword IN ('.implode(',', array_values($arrKeywordIds)).');';
				//debug
				$this->trace(__LINE__,__FUNCTION__,array(
					'$query' => $query
					));	
				//
				$arrTmpExerciseIds = array();
				//prepare db
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){
					foreach($rs->rows as $k2=>$v2){
						//on push les ids
						array_push($arrTmpExerciseIds, $v2['idExercise']);
						}	
					}	
				//clean
				unset($query, $rs, $k2, $v2);
				//on intersect
				//si vide alors on arrete et on reset
				if(count($arrTmpExerciseIds)){
					if($bFirstRun){
						$arrExerciseIds = $arrTmpExerciseIds;
						$bFirstRun = false;
					}else{
						$arrExerciseIds = array_intersect($arrExerciseIds, $arrTmpExerciseIds);
						if(!count($arrExerciseIds)){
							break;
							}
						}
				}else{
					$arrExerciseIds = array();
					break;
					}
			}else{
				$arrExerciseIds = array();
				break;
				}
			}
		//minor check
		if(count($arrExerciseIds)){
			//on flip pour ramener uniquement les cle
			$arrExerciseIds = array_flip($arrExerciseIds);
			}
		//debug
		$this->trace(__LINE__,__FUNCTION__,array(
			'count($arrExerciseIds)' => count($arrExerciseIds),
			//'$arrExerciseIds' => $arrExerciseIds,
			));
		//return 
		return $arrExerciseIds;	
		}

	//------------------------------------------------------------------------
	//mem chose que 'getExerciseIds' mais avec une seule requete
	private function getExerciseIdsMerged($arrFilteredExIds = false){
		//debug
		$this->trace(__LINE__, __FUNCTION__);
		//holder
		$arrExerciseIds = array();
		//la clause pour les different type
		$arrWhereClause = $this->wordPermutationForSQL('keyword.keyword', $this->arrKeywords);
		if(in_array($this->strUserLocale, $this->arrTryWithAccentLocale)){
			$arrWhereClause = $this->modifyAccentQueryLike($arrWhereClause);
			}
		//clean
		unset($k, $v);
		//debug
		$this->trace(__LINE__, __FUNCTION__.' | $arrWhereClause:', $arrWhereClause);
		//la query de base pour la table keyword
		$query = 'SELECT keyword_exercise.idExercise, exercise.rank FROM keyword, keyword_exercise, exercise, mod_exercise WHERE keyword.idKeyword = keyword_exercise.idKeyword AND keyword.idLicence IN (0,'.$this->idLicence.') AND mod_exercise.idModule IN ('.implode(',', array_keys($this->arrModules)).') AND keyword_exercise.idExercise = exercise.idExercise AND keyword_exercise.idExercise = mod_exercise.idExercise AND exercise.ready = 1 AND ((exercise.idUser = '.$this->idUser.') OR (exercise.idUser = 0) OR (exercise.idUser IN ('.implode(',', array_keys($this->arrUser)).') AND exercise.shared = 1)) ';
		//la locale
		if($this->strUserLocale == 'en_US'){
			$query .= ' AND keyword.locale = "en_US" ';
		}else{
			$query .= ' AND keyword.locale IN ("en_US","'.$this->strUserLocale.'") ';
			}
		//si on a des exercises filtre
		if(is_array($arrFilteredExIds) && count($arrFilteredExIds)){
			$query .= ' AND keyword_exercise.idExercise IN ('.implode(',',array_keys($arrFilteredExIds)).')';
			}
		//les keywords
		$query .= ' AND ('.$arrWhereClause.');';
		//debug
		$this->trace(__LINE__,__FUNCTION__,array(
			'$query' => $query
			));
		//prepare db
		$rs = $this->reg->get('db')->query($query);
		if($rs && $rs->num_rows){
			foreach($rs->rows as $k=>$v){
				//on push les ids
				$arrExerciseIds[$v['idExercise']] = $v['rank'];
				}
			}
		//clean
		unset($query, $rs, $k, $v);
		//debug
		$this->trace(__LINE__,__FUNCTION__,array(
			'count($arrExerciseIds)' => count($arrExerciseIds),
			//'$arrExerciseIds' => $arrExerciseIds,
			));
		//clean
		unset($k, $v);
		//on va sorter par rank et garder ceux en slice
		arsort($arrExerciseIds, SORT_NUMERIC);
		//on slice pour garder selon le max
		$arrExerciseIds = array_slice($arrExerciseIds, 0, MAX_SEARCH_NUM_ROWS, true);
		//return 
		return $arrExerciseIds;	
		}

	//------------------------------------------------------------------------
	private function wordPermutationForRegex($items, $strPermuted = '', $perms = array()){
		//debug
		//$this->trace(__LINE__, __FUNCTION__);	
		//
		if(empty($items)){ 
			$strPermuted .= '';
			for($i=0; $i<count($perms); $i++){
				$strPermuted .= $perms[$i].'.*';
				//le entre les mots
				if(($i + 1) < count($perms)){
					$strPermuted .= '[\s]{1}';
					}
				}
			//close
			$strPermuted .= '|';
		}else{
			for($i=count($items)-1; $i>=0; --$i){
				$newitems = $items;
				$newperms = $perms;
				list($foo) = array_splice($newitems, $i, 1);
				array_unshift($newperms, $foo);
				$strPermuted = $this->wordPermutationForRegex($newitems, $strPermuted,  $newperms);
				}
			}
		//alors on efface le dernier stripe
		if(!count($perms) && $strPermuted != ''){
			$strPermuted = substr($strPermuted, 0, strlen($strPermuted) - 1);
			}
		//return la string
		return $strPermuted;
		}

	//------------------------------------------------------------------------
	private function getUserExerciseIds($arrFilteredExIds = false){
		//debug
		$this->trace(__LINE__, __FUNCTION__);
		//la clause pour les different type
		$arrWhereClause = array(
			'code' => $this->wordPermutationForSQL('exercise_user.codeExercise', $this->arrKeywords),
			'keywords' => $this->wordPermutationForSQL('exercise_user.keywords', $this->arrKeywords), 
			'regex.data' => $this->wordPermutationForRegex($this->arrKeywords), 	
			);
		//debug
		$this->trace(__LINE__,__FUNCTION__,array(
			'$arrWhereClause' => $arrWhereClause
			));	
		//on va chercher les exercise des users
		//le holder des exercise
		$arrExerciseIds = array();	
		//query abse
		$query = 'SELECT DISTINCT(exercise_user.idExercise) FROM exercise_user WHERE exercise_user.idUser = '.$this->idUser.'';
		//si on a des exercises filtre
		if(is_array($arrFilteredExIds) && count($arrFilteredExIds)){
			$query .= ' AND exercise_user.idExercise IN ('.implode(',',array_keys($arrFilteredExIds)).')';
			}
		$query .= ' AND ('.$arrWhereClause['code'].' OR '.$arrWhereClause['keywords'].');';	
		//debug
		$this->trace(__LINE__,__FUNCTION__,array(
			'$query' => $query
			));	
		//prepare db
		$rs = $this->reg->get('db')->query($query);
		if($rs && $rs->num_rows){
			foreach($rs->rows as $k=>$v){
				//on push les ids
				$arrExerciseIds[$v['idExercise']] = 1;
				}
			}
		//clean
		unset($query, $rs);
		//
		//	
		//rajoute pour essai seulement
		//
		//
		if($this->bTryHarderInUserExerciseTable){
			//array
			$arrExerciseData = array();
			//recherche dans les short_title des exercise_user
			$query = 'SELECT DISTINCT(idExercise), data FROM exercise_user WHERE idUser = '.$this->idUser.'';
			//si on a des exercises filtre
			if(is_array($arrFilteredExIds) && count($arrFilteredExIds)){
				$query .= ' AND idExercise IN ('.implode(',',array_keys($arrFilteredExIds)).')';
				}
			//debug
			$this->trace(__LINE__,__FUNCTION__,array(
				'$query' => $query
				));
			//prepare db
			$rs = $this->reg->get('db')->query($query);
			if($rs && $rs->num_rows){
				foreach($rs->rows as $k=>$v){
					//on push les ids
					$arrExerciseData[$v['idExercise']] = $v['data'];
					}
				}
			//clean
			unset($query, $rs, $k, $v);
			//faire un preg match sur le result
			//pattern /.*"en_US":\{"short_title":"(.*)","title":/iU
			//pattern /intern.*[\s]{1}dwizz.*|dwizz.*[\s]{1}intern.*/iU
			foreach($arrExerciseData as $k=>$v){
				//echo '['.$k.']:"'.$v.'"'.EOL.EOL;	
				if(preg_match('/"'.$this->strUserLocale.'":\{"short_title":"(.*)","title":/Ui', $v, $arrMatch)){
					if(isset($arrMatch[1])){
						if(preg_match('/'.$arrWhereClause['regex.data'].'/Ui', $arrMatch[1])){
							$arrExerciseIds[$k] = 1;
							}
						}
					}
				}
			}
		//debug
		$this->trace(__LINE__,__FUNCTION__,array(
			'count($arrExerciseIds)' => count($arrExerciseIds),
			//'$arrExerciseIds' => $arrExerciseIds,
			));
		//return 
		return $arrExerciseIds;	
		}	

	//------------------------------------------------------------------------
	private function getExerciseData(&$arrExIds){
		//debug
		$this->trace(__LINE__, __FUNCTION__);
		//
		//holder 
		$arrExercises = array();
		//le data et video
		$query = 'SELECT DISTINCT(exercise.idExercise), exercise.codeExercise, exercise.data, exercise.idUser, video.host, video.embed_code FROM mod_exercise, exercise LEFT JOIN video ON exercise.idExercise = video.idExercise WHERE exercise.idExercise = mod_exercise.idExercise AND exercise.ready = 1 AND mod_exercise.idModule IN ('.implode(',', array_keys($this->arrModules)).') AND ((exercise.idUser = '.$this->idUser.') OR (exercise.idUser = 0) OR (exercise.idUser IN ('.implode(',', array_keys($this->arrUser)).') AND exercise.shared = 1)) AND exercise.idExercise IN ('.implode(',', array_keys($arrExIds)).') ORDER BY exercise.rank DESC LIMIT 0,'.MAX_SEARCH_NUM_ROWS.';';
		//$query = 'SELECT exercise.idExercise, exercise.codeExercise, exercise.data, exercise.idUser, video.host, video.embed_code FROM exercise, mod_exercise LEFT JOIN video ON exercise.idExercise = video.idExercise WHERE exercise.idExercise IN ('.implode(',', array_keys($arrExIds)).') ORDER BY exercise.rank DESC;';
		//debug
		$this->trace(__LINE__,__FUNCTION__, array(
			'$query' => $query,
			));
		$rs = $this->reg->get('db')->query($query);
		//debug
		/*
		$this->trace(__LINE__,__FUNCTION__,array(
			'$rs->num_rows' => $rs->num_rows,
			));	
		*/
		if($rs && $rs->num_rows){
			foreach($rs->rows as $k=>$v){
				$video = $v['embed_code'];
				if($v['host'] == 'sprout'){
					$video = PATH_VIDEO_SPROUT.$video;
					}
				$arrExercises[$v['idExercise']] = array(
					'codeExercise' => $v['codeExercise'],
					'data' => decodeString($v['data']),
					'video' => $video,
					//vide pour linstant ca etre rempli plus loin avec une autre requete
					'filter' => '', 
					);
				//check pour les "mine exercices"
				if(intVal($v['idUser']) == intVal($this->idUser)){
					$arrExercises[$v['idExercise']]['mine'] = 1;
					}
				}
			}
		unset($rs, $k, $v);	
		//debug
		$this->trace(__LINE__,__FUNCTION__,array(
			'count($arrExercises)' => count($arrExercises),
			));
		//return
		return $arrExercises;	
		}
	
	//------------------------------------------------------------------------
	private function getUserExerciseData(&$arrExIds){
		//debug
		$this->trace(__LINE__, __FUNCTION__);	
		//on va chercher les infos des Exercises de tous
		$query = 'SELECT idExercise, data FROM exercise_user WHERE idUser = '.$this->idUser.' AND idExercise IN ('.implode(',', array_keys($arrExIds)).');';
		//debug
		$this->trace(__LINE__,__FUNCTION__, array(
			'$query' => $query,
			));
		//
		$arrUserExercisesData = array();
		//prepare db
		$rs = $this->reg->get('db')->query($query);
		if($rs && $rs->num_rows){
			foreach($rs->rows as $k=>$v){
				//on push les ids
				$arrUserExercisesData[$v['idExercise']] = $v['data'];
				}
			}
		//clean
		unset($query, $rs);
		//debug
		$this->trace(__LINE__,__FUNCTION__, array(
			'count($arrUserExercisesData)' => count($arrUserExercisesData),
			));	
		//return
		return $arrUserExercisesData;
		}

	//------------------------------------------------------------------------
	private function changeKeywordRank(){
		//debug
		$this->trace(__LINE__, __FUNCTION__);
		//EX: 
		//pour 'internal rotation'
		//on va checker pour 'internal', 'rotation' et 'internal rotation'
		$arrKeywords = array(
			$this->strKeyword => array()
			);	
		//les separation si plus de 1
		if(count($this->arrKeywords) > 1){
			foreach($this->arrKeywords as $k=>$v){
				$arrKeywords[$v] = array();
				}
			//clean
			unset($k, $v);
			}
		//on va chercher les ids des keywords 
		//pour les incremente dans la table keyword_rank
		$strWhereClause = '';
		foreach($arrKeywords as $k=>$v){
			if($strWhereClause != ''){
				$strWhereClause .= ' OR ';
				}
			$strWhereClause .= ' keyword COLLATE utf8_bin LIKE "'.$k.'" ';
			}
		//clean
		unset($k, $v);
		//base query
		$query = 'SELECT keyword, idKeyword FROM keyword WHERE autogenerated = 0 AND kwtype IN (1,3) AND idLicence IN (0,'.$this->idLicence.') AND ('.$strWhereClause.') AND';
		//selon la locale
		if($this->strUserLocale == 'en_US'){
			$query .= ' locale = "en_US"';
		}else{
			$query .= ' locale IN ("en_US","'.$this->strUserLocale.'")';
			}
		$query .= ';';
		//debug
		$this->trace(__LINE__,__FUNCTION__,array(
			'$query' => $query,
			));
		//prepare db
		$rs = $this->reg->get('db')->query($query);
		if($rs && $rs->num_rows){
			foreach($rs->rows as $k=>$v){
				//on push les ids
				$arrKeywords[$v['keyword']][$v['idKeyword']] = 1;
				}
			}
		//clean
		unset($query, $rs, $k, $v);	
		//
		$arrNewKeywords = array();	
		//on garde ceux qui n'existe pas pour les soummettre a revision
		foreach($arrKeywords as $k=>$v){
			if(count($v) === 0){
				array_push($arrNewKeywords, $k);
				}
			}
		//si on a des mots pas trouve on les 
		//soumet pour etre revise ulterieurement
		if(count($arrNewKeywords)){
			$this->submitKeywordForRevision($arrNewKeywords);
			}
		//clean
		unset($arrNewKeywords, $k, $v);
		//on check ceux que l'on va updater
		$arrUpdateRank = array();	
		//debug
		$this->trace(__LINE__,__FUNCTION__,array(
			'$arrKeywords' => $arrKeywords,
			));
		foreach($arrKeywords as $k=>$v){
			if(count($v)){
				$query = 'SELECT idKeyword FROM keyword_rank WHERE locale = "'.$this->strUserLocale.'" AND idKeyword IN ('.implode(',', array_keys($v)).');';
				//debug
				$this->trace(__LINE__,__FUNCTION__,array(
					'$query' => $query,
					));
				//prepare db
				$rs = $this->reg->get('db')->query($query);
				if($rs && $rs->num_rows){
					foreach($rs->rows as $k2=>$v2){
						array_push($arrUpdateRank, $v2['idKeyword']);
						//on l'enleve du array
						unset($arrKeywords[$k2][$v2['idKeyword']]);
						}
					}
				unset($query, $rs, $k2, $v2);
				}
			}
		//ceux que l'on va inserer
		$arrInsertRank = array();	
		foreach($arrKeywords as $k=>$v){
			foreach($v as $k2=>$v2){
				array_push($arrUpdateRank, $k2);
				}
			//clean
			unset($k2, $v2);
			}
		//clean
		unset($arrKeywords, $k, $v);
		//on fait les update
		if(count($arrUpdateRank)){
			$query = 'UPDATE keyword_rank SET rank = rank + 1 WHERE locale = "'.$this->strUserLocale.'" AND idKeyword IN('.implode(',', $arrUpdateRank).');';
			//debug
			$this->trace(__LINE__,__FUNCTION__,array(
				'$query' => $query,
				));
			//execute	
			if($this->bExecute){
				$this->reg->get('db')->query($query);
				}
			}
		unset($arrUpdateRank);
		//on fait les insert
		if(count($arrInsertRank)){
			foreach($arrInsertRank as $k=>$v){
				$query = 'INSERT INTO keyword_rank (idKeyword, locale, rank) VALUES("'.$v.'", "'.$this->strUserLocale.'", "1");';
				//debug
				$this->trace(__LINE__,__FUNCTION__,array(
					'$query' => $query,
					));
				//execute	
				if($this->bExecute){
					$this->reg->get('db')->query($query);
					}
				}
			}
		unset($arrInsertRank);
		
		}

	//------------------------------------------------------------------------
	private function submitKeywordForRevision(&$arrKw){
		//debug
		$this->trace(__LINE__, __FUNCTION__,func_get_args());
		//va servir a faire valider des mots cle qui sont recherche
		if($this->bSubmitKwForRevision){
			if(count($arrKw)){
				foreach($arrKw as $k=>$v){
					//on check si est la et deja ete verifie
					$query = 'SELECT id, counter FROM keyword_revision WHERE keyword = "'.$v.'" AND locale = "'.$this->strUserLocale.'" LIMIT 0,1;';
					//debug
					$this->trace(__LINE__,__FUNCTION__,array(
						'$query' => $query,
						));
					//prepare db
					$rs = $this->reg->get('db')->query($query);
					//prepare
					//si est pas deja la
					if(!$rs->num_rows){
						//clean
						unset($query, $rs);
						//query	
						$query = 'REPLACE INTO keyword_revision (keyword, locale) VALUES ("'.$v.'", "'.$this->strUserLocale.'");';
						//debug
						$this->trace(__LINE__,__FUNCTION__,array(
							'$query' => $query,
							));
						//execute	
						if($this->bExecute){
							$this->reg->get('db')->query($query);
							}
					}else{
						$kwId = 0;
						//on push les ids de keyword
						foreach($rs->rows as $k=>$v){
							$kwId = intval($v['id']);
							$kwCount = intval($v['counter']) + 1;
							}
						//on incremente le compteur
						//clean
						unset($query, $sth);
						//query	
						$query = 'UPDATE keyword_revision SET counter = '.$kwCount.' WHERE id = '.$kwId.';';
						//debug
						$this->trace(__LINE__,__FUNCTION__,array(
							'$query' => $query,
							));
						//execute	
						if($this->bExecute){
							$this->reg->get('db')->query($query);
							}
						}
					}
				}
			}
		}

	//------------------------------------------------------------------------
	private function setFiltersForExercises(&$arrExercises){
		//debug
		$this->trace(__LINE__, __FUNCTION__);
		//recherche des filtres pour chaque exercice
		$query = 'SELECT idExercise, idMod_category, idMod_search_filter FROM mod_exercise WHERE idExercise IN ('.implode(',', array_keys($arrExercises)).') AND idModule IN ('.implode(',', array_keys($this->arrModules)).') ORDER BY idMod_category ASC, idExercise ASC, idMod_search_filter ASC;';
		$rs = $this->reg->get('db')->query($query);
		if($rs && $rs->num_rows){	
			//conteneur des exercice->cat->filter
			$arrFilterResult = array();
			//ordre de priorite des category, etrange mais c'est ca qui est ca...
			$arrCatPriotity = array(3,4); 
			//les autres suivant on pas d'importance
			for($i=1; $i<50; $i++){
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
			//clean
			unset($k, $v);	
			//on a un listing par exercice->category avec tous les filtres associes
			//loop dans les result key = idExercice
			foreach($arrFilterResult as $k=>$v){
				//on va chercher le cat selon la priorite
				foreach($arrCatPriotity as $k2=>$v2){
					//check si existe
					if(isset($v[$v2])){
						//on push le data filter dans l'exercice
						$arrExercises[$k]['filter'] = $v[$v2];
						break;
						}
					}
				//clean
				unset($k2, $v2);	
				}
			//test
			unset($arrFilterResult, $arrCatPriotity);
			}
		//clean	
		unset($rs);
		}

	//------------------------------------------------------------------------
	private function getExerciseFinalResult(&$arrExIds){	
		//debug
		$this->trace(__LINE__, __FUNCTION__);
		//on va chercher le data des exercises
		$arrExercises = $this->getExerciseData($arrExIds);
		if(count($arrExercises)){
			//on va chercher le data de user exercise ids
			$arrUserExercisesData = $this->getUserExerciseData($arrExercises);	
			//on rajoute le exercise data du user exercise data
			if(count($arrUserExercisesData)){
				foreach($arrUserExercisesData as $k=>$v){
					$arrExercises[$k]['userdata'] = json_endecodeArr(array('locale' => json_decodeStr($v)));
					}
				}
			}	
		//on va sorter les exercises sur le rank des ex ids
		//et on garde selon le max que l'on veut defini
		//return
		return $arrExercises;
		}

	//------------------------------------------------------------------------
	private function searchNormal(){
		//debug
		$this->trace(__LINE__, __FUNCTION__);
		//la liste des exercise regulier par keywords
		//$arrExerciseIds = $this->getExerciseIdsMerged();
		//$arrExerciseIds = $this->getExerciseIds();
		$arrExerciseIds = $this->getExerciseIdsMultiplePass();
		//minor check
		//la liste des exercise des users	
		$arrUserExerciseIds = $this->getUserExerciseIds();
		//si on en a
		if(count($arrUserExerciseIds)){	
			//on rajoute les array des user exercises ids a celui des exercise ids normaux
			foreach($arrUserExerciseIds as $k=>$v){
				$arrExerciseIds[$k] = 1;
				}
			}
		//return
		return $arrExerciseIds;
		}	

	//------------------------------------------------------------------------
	public function search($str){
		//debug
		$this->trace(__LINE__, __FUNCTION__,func_get_args());
		//init le container des exercises ids 
		//qui seront ramene par les methodes
		$arrExercises = array();
		//on prepare les keywords pour une recherce complete
		$this->prepareKeywords($str);
		//check si une recherche par keyword ou juste avec filtre sans mots cle
		if($this->iCountKeyword){
			//si doit etre ranker
			if($this->bChangeRank){
				//change le rank
				$this->changeKeywordRank();
				}
			//recherche sans filtre
			$arrExerciseIds = $this->searchNormal();
			//peut importe la recherche on doit avoir des Ex Ids
			if(count($arrExerciseIds)){
				//on va aller chercher le data pour le final
				$arrExercises = $this->getExerciseFinalResult($arrExerciseIds);
				//si on a des exercises on set les filters
				if(count($arrExercises)){
					//set les filter pour chaque exercises
					$this->setFiltersForExercises($arrExercises);
					//set les favorites
					$this->setFavoriteForExercises($arrExercises); 
					}
				}
			}
		//debug
		$this->trace(__LINE__,__FUNCTION__,array(
			'count($arrExercises)' => count($arrExercises),
			//'$arrExercises' => $arrExercises
			));
		//just show	
		if($this->bTrace){
			exit();
			}
		//retourne un array	
		return $arrExercises;
		}

	}


//END CLASS
