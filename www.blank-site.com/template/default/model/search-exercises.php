<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	search model

@desc:	
	
	- 	Il y a un fichier d'une longue string genere pour le javscript dans le repertoire
		DIR_RENDER_KW_JS = www.blank-site.com/temp/cache/js/db-kw.en_US.data
		avec les fichiers par langue.

	-	Avec la bonne string trouve on va pouvoir se referer a un array php qui aura la liste des
		/admin/script/6.create-php-db-kw.php
		exercises ids EX: ['abdominal plank'] = array(19585,162,16785);
	
	-	Avec les ids de la liste on pourra loader le data via les fichier genere par le script
		/admin/script/1.create-exercises.php
		qui serialise des array que l'on pourra loader pour l'affichage des donnes
		en format listing comme en format details

	

*/


class SearchExercises{
	
	//vars
	private $reg;
	private $lang;
	private $iMaxWordPermution = 4;
	private $strPathKw = '';		
	private $strPathEx = '';		
	private $strPathKwExerciseIds = '';	
	private $iPageStart;
	private $iMaxPerPage;
	
	//------------------------------------------------------------------------
	public function __construct(&$reg, $lang = 'en_US', $limit = 10, $start = 0){
		$this->reg = $reg;
		$this->lang = $lang;
		$this->iPageStart = $start;
		$this->iMaxPerPage = $limit;
		$this->strPathKw = DIR_RENDER_KW_JS.'complete-db-kw.'.$this->lang.'.data';		
		$this->strPathEx = DIR_RENDER_EX_PHP.$this->lang.'/';		
		$this->strPathKwExerciseIds = DIR_RENDER_KW_PHP.'array-kw.'.$this->lang.'.data';		
			
		}
		
	//------------------------------------------------------------------------
	public function getExerciseListingFromSeparatedKw($word){
		//le result array
		$arrResult = array();
		$arrExercisesIdsByKw = array();
		$arrExercisesIds = array();
		$iTotalEx = 0;	
		//on trim le keyword
		$word = $this->trimKeyword($word);
		//on va faire les permutations de mots jusqu'au max permis
		$arrKw = explode(' ', $word);
		if(count($arrKw)){
			//on va loader la string a chercher dedans selon la langue
			$strDb = $this->getKwDB();
			//le preg match
			if($strDb != ''){
				//le container de mot
				$arrMatch = array();
				//les keywords
				$arrKw = array_slice($arrKw, 0, $this->iMaxWordPermution);	
				foreach($arrKw as $key=>$val){
					//la string de regex contruite avec le array
					$strRegex = $this->regexWordPermutation(array($val));
					//prepare le array par mot
					$arrMatch[$val] = array();
					//on strip le last pipe
					$strRegex = substr($strRegex, 0, (strlen($strRegex) - 1));
					if(preg_match_all('/'.$strRegex.'/', $strDb, $arrMatch[$val])){
						//on garde le array de match
						$arrMatch[$val] = $arrMatch[$val][0];
						//si on a des resultats
						}
					}
				//clean
				unset($key, $val);
				//maintenant on va loader le array complet de keywords->exercise_ids qui est serialise
				$arrKwExerciseIds = $this->getKwExerciseDB();
				if($arrKwExerciseIds !== false){
					//on va devoir seulement garder ceux qui se retrouve dans tous les array de word
					$arrExercisesIdsByKw = array();
					//on garde juste ceux que l'on a besoin
					foreach($arrMatch as $key=>$val){
						$arrExercisesIdsByKw[$key] = array();
						foreach($arrMatch[$key] as $k=>$v){
							//on va cleaner pour retire le dernier pipe | 
							//et on prepare le array si trouve des resultats
							if(!is_array($v)){
								$v = substr($v, 1);
								if(isset($arrKwExerciseIds[$v])){
									$arrExercisesIdsByKw[$key][$v] = $arrKwExerciseIds[$v];
									}
								}
							}
						//clean
						unset($k, $v);	
						}
					//clean
					unset($key, $val);	
					}
				//on clean car plus besoin
				unset($arrKwExerciseIds, $arrMatch);
				//el holder des exids
				$arrExercisesIds = array();
				//si on a des exercises on va retirer les repetitions
				foreach($arrExercisesIdsByKw as $key=>$val){
					if(count($arrExercisesIdsByKw[$key])){	
						$arrExercisesIds[$key] = array();
						foreach($arrExercisesIdsByKw[$key] as $k=>$v){
							//on garde des cle uniquement
							//qui est [0] = exID et [1] = rank
							foreach($v as $k2=>$v2){
								$arrExercisesIds[$key][$v2[0]] = $v2[1];
								}
							//clean
							unset($k2, $v2);
							}
						//clean
						unset($k, $v);
					}else{
						$arrExercisesIds[$key] = array();	
						}
					//clean
					unset($key, $val);	
					}
				//clean
				unset($arrExercisesIdsByKw);
				//maintenant on fait une intersection des cle qui se retrouve dans tout les array by 'mots' uniquement
				$arrIntersect = false;
				foreach($arrExercisesIds as $k=>$v){
					if($arrIntersect === false){
						//soit le premier de la gang
						$arrIntersect = $arrExercisesIds[$k];
					}else{
						$arrIntersect = array_intersect_key($arrIntersect, $arrExercisesIds[$k]);
						//clean
						unset($arrExercisesIds[$k]);	
						}
					//si vide alors pas besoin de continuer
					if(!count($arrIntersect)){
						break;
						}
					}
				//le array final	
				$arrExercisesIds = $arrIntersect;
				//le total
				$iTotalEx = count($arrExercisesIds);
				//on fait le classement par rank
				asort($arrExercisesIds);
				//on garde selon la page ou on est
				$arrExercisesIds = array_slice(
					$arrExercisesIds,
					$this->iPageStart * $this->iMaxPerPage,
					$this->iMaxPerPage,
					true
					);
				//on va chercher le data de chacun de ceux qui nous reste
				foreach($arrExercisesIds as $k=>$v){
					$arrExercisesIds[$k] = $this->getSingleExerciseDB($k);
					if($arrExercisesIds[$k] === false){
						unset($arrExercisesIds[$k]);
						}
					}
				//cleaner les unset
				$arrExercisesIds = $arrExercisesIds;
				}
			}
		//start and end
		$startEx = ($this->iPageStart * $this->iMaxPerPage) + 1;
		$endEx = ($this->iPageStart * $this->iMaxPerPage) + $this->iMaxPerPage;
		if($endEx >= $iTotalEx){
			$endEx = $iTotalEx;
			}
		//retour
		return array(
			'total' => $iTotalEx,
			'start' => $startEx,
			'end' => $endEx,
			'exercises' => $arrExercisesIds,
			);
		}	
	
	//------------------------------------------------------------------------
	public function getExerciseListing($word){
		//le result array
		$arrResult = array();
		$arrExercisesIdsByKw = array();
		$arrExercisesIds = array();
		$iTotalEx = 0;	
		//on trim le keyword
		$word = $this->trimKeyword($word);
		//on va faire les permutations de mots jusqu'au max permis
		$arrKw = explode(' ', $word);
		//la string de regex contruite avec le array
		$strRegex = $this->regexWordPermutation(array_slice($arrKw, 0, $this->iMaxWordPermution));
		if($strRegex != ''){
			//on strip le last pipe
			$strRegex = substr($strRegex, 0, (strlen($strRegex) - 1));
			//on va loader la string a chercher dedans selon la langue
			$strDb = $this->getKwDB();
			//le preg match
			if($strDb != ''){
				if(preg_match_all('/'.$strRegex.'/', $strDb, $arrMatch)){
					//on garde le array de match
					$arrMatch = $arrMatch[0];
					//si on a des resultats
					if(count($arrMatch)){
						//maintenant on va loader le array complet de keywords->exercise_ids qui est serialise
						$arrKwExerciseIds = $this->getKwExerciseDB();
						if($arrKwExerciseIds !== false){
							$arrExercisesIdsByKw = array();
							$arrExercisesIds = array();
							//on garde juste ceux que l'on a besoin
							foreach($arrMatch as $k=>$v){
								//on va cleaner pour retire le dernier pipe | 
								//et on prepare le array si trouve des resultats
								$v = substr($v, 1);
								if(isset($arrKwExerciseIds[$v])){
									//on push
									$arrExercisesIdsByKw[$v] = $arrKwExerciseIds[$v];
									}
								}
							}
						//on clean car plus besoin
						unset($arrKwExerciseIds, $arrMatch);
						//si on a des exercises on va retirer les repetitions
						if(count($arrExercisesIdsByKw)){	
							foreach($arrExercisesIdsByKw as $k=>$v){
								//on garde des cle uniquement peut-etre avec le value 
								//qui sera le rank [0] = exID et [1] = rank
								foreach($v as $k2=>$v2){
									$arrExercisesIds[$v2[0]] = $v2[1];
									}
								//clean
								unset($k2, $v2);
								}
							//clean
							unset($k, $v);
							}
						//clean
						unset($arrExercisesIdsByKw);
						//le total
						$iTotalEx = count($arrExercisesIds);
						//on fait le classement par rank
						asort($arrExercisesIds);
						//on garde selon la page ou on est
						$arrExercisesIds = array_slice(
							$arrExercisesIds,
							$this->iPageStart * $this->iMaxPerPage,
							$this->iMaxPerPage,
							true
							);
						//on va chercher le data de chacun de ceux qui nous reste
						foreach($arrExercisesIds as $k=>$v){
							$arrExercisesIds[$k] = $this->getSingleExerciseDB($k);
							if($arrExercisesIds[$k] === false){
								unset($arrExercisesIds[$k]);
								}
							}
						//cleaner les unset
						$arrExercisesIds = $arrExercisesIds;
						}
					}
				}
			}
		//start and end
		$startEx = ($this->iPageStart * $this->iMaxPerPage) + 1;
		$endEx = ($this->iPageStart * $this->iMaxPerPage) + $this->iMaxPerPage;
		if($endEx >= $iTotalEx){
			$endEx = $iTotalEx;
			}
		//retour
		return array(
			'total' => $iTotalEx,
			'start' => $startEx,
			'end' => $endEx,
			'exercises' => $arrExercisesIds,
			);
		}

	//------------------------------------------------------------------------
	private function getKwDB(){
		$str = '';
		if(is_readable($this->strPathKw)){
			$fh = @fopen($this->strPathKw, 'r');
			$str = @fread($fh, filesize($this->strPathKw));
			@fclose($fh);
			}
		return $str;
		}

	//------------------------------------------------------------------------
	private function getKwExerciseDB(){
		if(is_readable($this->strPathKwExerciseIds)){
			$fh = @fopen($this->strPathKwExerciseIds, 'r');
			$str = @fread($fh, filesize($this->strPathKwExerciseIds));
			@fclose($fh);
			return unserialize($str);
			}
		return false;
		}

	//------------------------------------------------------------------------
	private function getSingleExerciseDB($exId){
		$path = $this->strPathEx.$exId.'.listing.data';	
		if(is_readable($path)){
			$fh = @fopen($path, 'r');
			$str = @fread($fh, filesize($path));
			@fclose($fh);
			return unserialize($str);
			}
		return false;
		}	

	//------------------------------------------------------------------------
	private function trimKeyword($str){
		//clean up des mots
		$str = mb_strtolower($str, 'UTF-8');
		$str = preg_replace('/[^a-zA-Z0-9\'\sÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ+-]/', ' ', $str);	
		$str = preg_replace('/[\s]+/', ' ', $str);	
		$str = trim($str);
		
		return $str;
		}

	//------------------------------------------------------------------------
	private function regexWordPermutation($items, $strPermuted = '', $perms = array()){
		//	
		if(empty($items)){
			$str1 = '';
			$str2 = '';
			for($i=0; $i<count($perms); $i++){
				//le premier et tout seul
				if(count($perms) == 1){
					$str1 = '\\|[a-z0-9\'\\s]{0,}[\\s]{1}'.$perms[$i].'[a-z0-9\'\\s]{0,}'; 					 
					$str2 = '\\|'.$perms[$i].'[a-z0-9\'\\s]{0,}';	
				}else if($i == 0){ 
					//le premier du array
					$str1 = '\\|[a-z0-9\'\\s]{0,}[\\s]{1}'.$perms[$i].'[a-z0-9\'\\s]{0,}[\\s]{1}'; 					 
					$str2 = '\\|'.$perms[$i].'[a-z0-9\'\\s]{0,}[\\s]{1}';	
				}else if(($i + 1) == count($perms)){
					//le dernier
					$str1 .= $perms[$i].'[a-z0-9\'\\s]{0,}';
					$str2 .= $perms[$i].'[a-z0-9\'\\s]{0,}';
				}else{
					//ceux dans le milieu
					$str1 .= $perms[$i].'[a-z0-9\'\\s]{0,}[\\s]{1}';
					$str2 .= $perms[$i].'[a-z0-9\'\\s]{0,}[\\s]{1}';	
					}
				}
			$strPermuted .= $str1.'|'.$str2.'|';
		}else{
			for($i=count($items)-1; $i>=0; --$i){
				$newitems = $items;
				$newperms = $perms;
				list($foo) = array_splice($newitems, $i, 1);
				array_unshift($newperms, $foo);
				$strPermuted = $this->regexWordPermutation($newitems, $strPermuted, $newperms);
				}
			}
		//on strip le last pipe |
		//retourne le tout
		return $strPermuted;
		}



	}



//END