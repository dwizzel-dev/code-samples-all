<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	details model

@desc:	
	
	
*/


class DetailsExercises{
	
	//vars
	private $reg;
	private $id;
	private $glob;
	private $strPathEx = '';		
	
	//------------------------------------------------------------------------
	public function __construct(&$reg, &$glob){
		$this->reg = $reg;
		$this->glob = $glob;
		$this->strPathEx = DIR_RENDER_EX_PHP.$this->glob->get('lang').'/';			
		}
	
	//------------------------------------------------------------------------
	public function getExerciseDetails($id){
		//return the array
		return $this->getSingleExerciseDB($id);
		}

	//------------------------------------------------------------------------
	private function getSingleExerciseDB($id){
		$path = $this->strPathEx.$id.'.details.data';
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