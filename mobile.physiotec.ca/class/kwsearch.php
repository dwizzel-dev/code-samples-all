<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	search utilities and manipulation from the search box with autocomplete
@inst:	ceci est une forme de extend de la classe search base sur la meme recherche 
		que sur le site standard pour eviter de refaire toujours le code.
		la valeur de reception est la meme, mais le retour differe car nous avons
		du data de format different que sur le site, sianon que les logs et trace.
			

*/
//------------------------------------------------------------------------

class KwSearch {

	//values
	private $className = 'KwSearch';
	private $bTrace = false; 
	private $iMaxCharPermution = 4;	
	private $iMaxWordPermution = 4;
	private $bStripAllAccent = true;
	private $bKwIdsForQuery = true;	
	private $strDefaultLocale = 'en_US';	
	private $arrModules = false;	
	private $arrTryWithAccentLocale = array(
		'fr_CA',
		'fr_FR',
		);			
	
	//by params object
	private $reg;
	
	//------------------------------------------------------------------------
	public function __construct(&$reg, $data){
		//registry	
		$this->reg = $reg;
		//la locale
		$this->strLocale = $this->reg->get('sess')->get('locale');
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
		}

	//------------------------------------------------------------------------
	public function __destruct(){
		
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
	private function wordForSQLV3($str){
		//debug
		$this->trace(__LINE__, __FUNCTION__, array(
			$str
			));
		//
		$strLike = 'keyword.keyword COLLATE utf8_bin LIKE "'.$str.'%"';
		//
		return $strLike;
		}

	//------------------------------------------------------------------------
	private function wordPermutationForSQLV3($items, $strPermuted = '', $perms = array()){
		//debug
		$this->trace(__LINE__, __FUNCTION__, array(
			$items,
			$strPermuted,
			$perms
			));
		//	
		if(empty($items)){
			//juste un seul
			if(count($perms) === 1){
				//word
				$strWord = $perms[0];
				//le like
				$strLike = 'keyword.keyword COLLATE utf8_bin LIKE ';	
				//posssibilities
				$strPermuted .= $strLike.'"'.$strWord.'"'; 
				//on rajoute le or
				$strLike = ' OR '.$strLike;
				//on continue
				$strPermuted .= $strLike.'"'.$strWord.'%"'; 
				$strPermuted .= $strLike.'"% '.$strWord.'%"'; 
			}else{
				//le like
				$strLike = 'keyword.keyword COLLATE utf8_bin LIKE "';	
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
				$strLike = ' OR keyword.keyword COLLATE utf8_bin LIKE "';	
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
				$strPermuted = $this->wordPermutationForSQLV3($newitems, $strPermuted,  $newperms);
				}
			}
		return $strPermuted;
		}

	//------------------------------------------------------------------------
	private function createSqlWordsPermutationForDummiesV3($word, $bFromStart = false){
		//debug
		$this->trace(__LINE__, __FUNCTION__, array(
			$word,
			$bFromStart
			));	
		//arr des mots a retenir
		$strRegex = '';
		//on va le mettre en ISO avant
		$word = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $word);	
		//on va permuter seulement les iMaxCharsPermutation premier chars au dela 
		//on a pas les serverus pour on est pas google quand meme
		$firstChar = '';		
		$arrChars = str_split($word);
		//car doit au moins commenc er apr ca sinon c'est un peu n'importquoi qu'il tape
		//on garde le premier charatere si jamais on veut commencer par ca
		if(!$bFromStart){
			$firstChar = mb_convert_encoding($arrChars[0], 'UTF-8', 'ISO-8859-1');
			//le reste des lettres
			$arrChars = array_slice($arrChars, 1, $this->iMaxCharPermution);	
		}else{
			$firstChar = '';
			//le reste des lettres
			$arrChars = array_slice($arrChars, 0, $this->iMaxCharPermution);	
			}
		//on va remettre ca en utf-8	
		foreach($arrChars as $k=>$v){
			$arrChars[$k] = mb_convert_encoding($arrChars[$k], 'UTF-8', 'ISO-8859-1');
			}
		//
		$strRegex = $this->charsPermutationForSqlV3($firstChar, $arrChars);	
		//strip le last " OR" soit 3 espaces
		if($strRegex != ''){
			$strRegex = substr($strRegex, 0, (strlen($strRegex) - 3));
			}
		//le retour
		return $strRegex;	
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

	//------------------------------------------------------------------------
	private function queryBuilderV3($strKwLike, $bKwIdsForQuery = false){
		//debug
		$this->trace(__LINE__, __FUNCTION__, array(
			$strKwLike
			));
		//simple query avec juste les ids qui se trouve dans $strKwLike
		if($bKwIdsForQuery){
			$strQuery = 'SELECT DISTINCT(keyword.idKeyword), keyword.keyword FROM keyword_exercise, mod_exercise, keyword, keyword_rank WHERE keyword.idKeyword = keyword_rank.idKeyword AND ('.$strKwLike.')'.' AND keyword.idKeyword = keyword_exercise.idKeyword AND keyword_exercise.idExercise = mod_exercise.idExercise ';
			//loop de locale
			if($this->strLocale != $this->strDefaultLocale){
				$strQuery .= 'AND keyword_rank.locale IN ("'.$this->reg->get('db')->escape($this->strLocale).'","en_US") ';
			}else{
				$strQuery .= 'AND keyword_rank.locale = "en_US" '; 
				}
			if(count($this->arrModules)){
				$strQuery .= 'AND mod_exercise.idModule IN ('.implode(',', array_keys($this->arrModules)).') ';
				}
			$strQuery .= 'ORDER BY keyword_rank.rank DESC, keyword.keyword LIMIT 0,'.MAX_ROWS_AUTOCOMPLETE_RETURNED.';';
			
		}else{
			//modified accent
			if(in_array($this->strLocale, $this->arrTryWithAccentLocale)){
				$strKwLike = $this->modifyAccentQueryLike($strKwLike);
				}
			//
			$strQuery = 'SELECT DISTINCT(keyword.idKeyword), keyword.keyword FROM keyword_exercise, mod_exercise, keyword, keyword_rank WHERE keyword.idKeyword = keyword_rank.idKeyword AND keyword.kwtype IN (1,3) AND keyword.idLicence IN (0, '.$this->idLicence.') AND ('.$strKwLike.')'.' AND keyword.idKeyword = keyword_exercise.idKeyword AND keyword_exercise.idExercise = mod_exercise.idExercise ';
			//loop de locale
			if($this->strLocale != $this->strDefaultLocale){
				$strQuery .= 'AND keyword.locale IN ("'.$this->reg->get('db')->escape($this->strLocale).'", "en_US") ';
				$strQuery .= 'AND keyword_rank.locale IN ("'.$this->reg->get('db')->escape($this->strLocale).'", "en_US") ';
			}else{
				$strQuery .= 'AND keyword.locale = "en_US" '; 
				$strQuery .= 'AND keyword_rank.locale = "en_US" '; 
				}
			if(count($this->arrModules)){
				$strQuery .= 'AND mod_exercise.idModule IN ('.implode(',', array_keys($this->arrModules)).') ';
				}
			$strQuery .= 'ORDER BY keyword_rank.rank DESC, keyword.keyword LIMIT 0,'.MAX_ROWS_AUTOCOMPLETE_RETURNED.';';
			}
		//
		return $strQuery;
		}

	//------------------------------------------------------------------------
	private function trimKeyword($str){
		//debug
		$this->trace(__LINE__, __FUNCTION__, array(
			$str
			));
		//clean up des mots
		$str = mb_strtolower($str, 'UTF-8');
		$str = preg_replace('/[^a-zA-Z0-9\sÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ+-]/', ' ', $str);	
		$str = preg_replace('/[\s]+/', ' ', $str);	
		$str = trim($str);
		
		return $str;
		}

	//------------------------------------------------------------------------
	private function charsPermutationForSqlV3($firstChar, $items, $strPermuted = '', $perms = array()){
		//debug
		$this->trace(__LINE__, __FUNCTION__, array(
			$firstChar,
			$items,
			$strPermuted,
			$perms
			));
		//
		if(empty($items)){ 
			$strPermuted .= 'keyword.keyword COLLATE utf8_bin LIKE "'.$firstChar;
			for($i=0; $i<count($perms); $i++){
				$strPermuted .= $perms[$i];
				}
			//close
			$strPermuted .= '%" OR ';
		}else{
			for($i=count($items)-1; $i>=0; --$i){
				$newitems = $items;
				$newperms = $perms;
				list($foo) = array_splice($newitems, $i, 1);
				array_unshift($newperms, $foo);
				$strPermuted = $this->charsPermutationForSqlV3($firstChar, $newitems, $strPermuted,  $newperms);
				}
			}
		return $strPermuted;
		}

	//------------------------------------------------------------------------
	private function fetchUniqueDataRowsV3($query){
		//debug
		$this->trace(__LINE__, __FUNCTION__, array(
			$query
			));	
		//Retrait des keywords identiques
		$arrRtn = array();
		//execute
		$rs = $this->reg->get('db')->query($query);
		//minor check
		if($rs && $rs->num_rows){
			$i = 0;
			$arrUnique = array();
			foreach($rs->rows as $k=>$v){
				if($i < MAX_ROWS_AUTOCOMPLETE_RETURNED){
					if($v['keyword'].'' != '' && !isset($arrUnique[$v['keyword']])){
						//le par cle pour pas de repetition
						$arrUnique[$v['keyword']] = 1;
						//ce que l'on va envoyer
						array_push($arrRtn, array(
							'id'   => $v['idKeyword'],
							'name' => $v['keyword'],
							));
						$i++;
						}
					}
				}
			//debug
			$this->trace(__LINE__, __FUNCTION__, array(
				'$arrUnique' => $arrUnique,
				));
			}
		//debug
		$this->trace(__LINE__, __FUNCTION__, array(
			'$arrRtn' => $arrRtn,
			));
		//return
		return $arrRtn;		
		}

	//------------------------------------------------------------------------
	private function createWordsWithSpaceForDummiesV3($word, $bFromStart = false){
		//debug
		$this->trace(__LINE__, __FUNCTION__, array(
			$word,
			$bFromStart
			));	
		//arr des mots a retenir
		$strRegex = '';
		//on va le mettre en ISO avant
		$word = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $word);	
		//on creer un couple de mot de remplacement
		for($i=0;$i<strlen($word);$i++){
			$strLeft = '';
			for($j=0;$j<strlen($word)-(strlen($word)-($i+1));$j++){
				$strLeft .= mb_convert_encoding($word{$j}, 'UTF-8', 'ISO-8859-1');
				}
			$strRight = '';
			for($j=($i + 1);$j<strlen($word);$j++){
				//$strRight .= $word{$j};
				$strRight .= mb_convert_encoding($word{$j}, 'UTF-8', 'ISO-8859-1');
				}
			if($bFromStart){
				$strRegex .= '(^[[:alnum:]]{0,2}'.$strLeft.'[[:alnum:]]{0,2}'.$strRight.'[[:alnum:]]*)|';
			}else{
				$strRegex .= '(^'.$strLeft.'[[:alnum:]]{1,2}'.$strRight.'[[:alnum:]]*)|';
				}
			}
		//strip
		if($strRegex != ''){
			$strRegex = substr($strRegex, 0, (strlen($strRegex) - 1));
			}
		//le retour
		return ' keyword.keyword COLLATE utf8_bin REGEXP "'.$strRegex.'" ';	
		}

	//------------------------------------------------------------------------
	private function getKwIdsForQuery($arrKw){
		//debug
		$this->trace(__LINE__, __FUNCTION__);
		//retour
		$arrFinalKwIds = array();
		//la query de base pour la table keyword
		$strBaseQuery = 'SELECT idKeyword FROM keyword WHERE idLicence IN (0,'.$this->idLicence.') AND kwtype IN(1,3) ';
		//la locale
		if($this->strLocale != $this->strDefaultLocale){
			$strBaseQuery .= ' AND locale IN ("en_US","'.$this->strLocale.'") ';
		}else{
			$strBaseQuery .= ' AND locale = "en_US" ';	
			}
		//
		$bFirstRun = true;
		foreach($arrKw as $k=>$v){
			//where
			$strWhereClause = $this->wordPermutationForSQLV3(array($v));
			//modified accent
			if(in_array($this->strLocale, $this->arrTryWithAccentLocale)){
				$strWhereClause = $this->modifyAccentQueryLike($strWhereClause);
				}
			//on rajoute les autres id trouve precedement
			if($bFirstRun){
				$bFirstRun = false;
				//query
				$query = $strBaseQuery.' AND ('.$strWhereClause.');';
			}else{
				//query
				$query = $strBaseQuery.' AND idKeyword IN ('.implode(',', array_values($arrFinalKwIds)).') AND ('.$strWhereClause.');';	
				}
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
			//
			if(count($arrKeywordIds)){
				//minor check
				$arrFinalKwIds = $arrKeywordIds;
			}else{
				return false;
				}
			}
		//minor check
		if(count($arrFinalKwIds)){
			//return the string to find
			return ' keyword.idKeyword IN ('.implode(',', array_values($arrFinalKwIds)).') ';	
			}
		//
		return false;
		}

	//------------------------------------------------------------------------
	private function searchHard($str){
		//debug
		$this->trace(__LINE__, __FUNCTION__, array(
			$str
			));
		//
		$foundArr = array();	
		//on va faire les permutations de mots jusqu'a 4 maximum
		$arrKw = explode(' ', $str);
		//premier essai de query
		if($this->bKwIdsForQuery){
			//va chercher une query de IN (kwids) 
			$queryKwIds = $this->getKwIdsForQuery(array_slice($arrKw, 0, $this->iMaxWordPermution));
			if($queryKwIds){
				$query = $this->queryBuilderV3($queryKwIds, true);
				//on fetch le data
				$foundArr = $this->fetchUniqueDataRowsV3($query);
				}
		}else{
			//va chercher une query de strings pour le LIKE
			$query = $this->queryBuilderV3($this->wordPermutationForSQLV3(array_slice($arrKw, 0, $this->iMaxWordPermution)));
			//on fetch le data
			$foundArr = $this->fetchUniqueDataRowsV3($query);
			}
		//check si on a un resultat
		if(count($foundArr) === 0){
			//on essai autrement avec le premier mot seulement
			$query = $this->queryBuilderV3($this->createSqlWordsPermutationForDummiesV3($arrKw[0]));
			//on fetch le data
			$foundArr = $this->fetchUniqueDataRowsV3($query);
			//check si on a un resultat
			if(count($foundArr) === 0){
				//on essai autrement avec le premier mot seulement
				$query = $this->queryBuilderV3($this->createWordsWithSpaceForDummiesV3($arrKw[0]));
				//on fetch le data
				$foundArr = $this->fetchUniqueDataRowsV3($query);
				//check si on a un resultat
				if(count($foundArr) === 0){
					//on essai autrement avec une permutation depuis le debut
					$query = $this->queryBuilderV3($this->createWordsWithSpaceForDummiesV3($arrKw[0], true));
					//on fetch le data
					$foundArr = $this->fetchUniqueDataRowsV3($query);
					//check si on a un resultat
					if(count($foundArr) === 0){
						//on essai autrement avec une permutation depuis le debut
						$query = $this->queryBuilderV3($this->createSqlWordsPermutationForDummiesV3($arrKw[0], true));
						//on fetch le data
						$foundArr = $this->fetchUniqueDataRowsV3($query);
						}
					}	
				}
			}
		//return
		return $foundArr;
		}

	//------------------------------------------------------------------------
	private function searchMedium($str){
		//debug
		$this->trace(__LINE__, __FUNCTION__, array(
			$str
			));	
		//on va faire les permutations de mots jusqu'a 3 maximum
		$arrKw = explode(' ', $str); 	
		//premier essai de query	
		$query = $this->queryBuilderV3($this->wordPermutationForSQLV3(array_slice($arrKw, 0, $this->iMaxWordPermution)));
		//on fetch le data
		$foundArr = $this->fetchUniqueDataRowsV3($query);
		//check si on a un resultat
		if(count($foundArr) === 0){
			//on essai autrement avec le premier mot seulement
			$query = $this->queryBuilderV3($this->createSqlWordsPermutationForDummiesV3($arrKw[0]));
			//on fetch le data
			$foundArr = $this->fetchUniqueDataRowsV3($query);
			}
		//return
		return $foundArr;
		}

	//------------------------------------------------------------------------
	private function searchNormal($str){
		//debug
		$this->trace(__LINE__, __FUNCTION__, array(
			$str
			));	
		//premier essai de query	
		$query = $this->queryBuilderV3($this->wordForSQLV3($str));
		//on fetch le data
		$foundArr = $this->fetchUniqueDataRowsV3($query);
		//return
		return $foundArr;
		}

	//------------------------------------------------------------------------
	public function search($str, $type = ''){
		//debug
		$this->trace(__LINE__, __FUNCTION__, array(
			$str,
			$type
			));
		//timer
		$fStartTime = microtime(true);
		//vars
		$arrRtn = array();
		//clean up des mots
		$str = $this->trimKeyword($str);
		//
		if($str.'' != ''){
			//check
			switch($type){
				case 'hard':
					$arrRtn = $this->searchHard($str);
					break;
				case 'medium':
					$arrRtn = $this->searchMedium($str);
					break;
				default:
					$arrRtn = $this->searchNormal($str);
					break;	
				}
			}
		
		//debug
		$this->trace(__LINE__, __FUNCTION__, 'TIME:'.(microtime(true) - $fStartTime));
		//return ajax type
		return $arrRtn;
		}

	}


//END CLASS