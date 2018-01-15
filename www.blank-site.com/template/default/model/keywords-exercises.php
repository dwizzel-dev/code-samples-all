<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	keywords model

@desc:	
	
	
*/


class KeywordsExercises{
	
	//vars
	private $reg;
	private $id;
	private $glob;
	private $strPathCat = '';		
	
	//------------------------------------------------------------------------
	public function __construct(&$reg, &$glob){
		$this->reg = $reg;
		$this->glob = $glob;
		$this->strPathCat = DIR_RENDER_KEYWORD_PHP.$this->glob->get('lang').'/';			
		}
	
	//------------------------------------------------------------------------
	public function getExercises($kwId){
		//return the array
		return $this->getSingleKeywordsDB($kwId);
		}


	//------------------------------------------------------------------------
	private function getSingleKeywordsDB($id){
		$path = $this->strPathCat.$id.'.data';
		if(is_readable($path)){
			$fh = @fopen($path, 'r');
			$str = @fread($fh, filesize($path));
			@fclose($fh);
			return unserialize($str);
			}
		return false;
		}	

	

	}



//END