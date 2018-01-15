<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	categories model

@desc:	
	
	
*/


class CategoriesExercises{
	
	//vars
	private $reg;
	private $id;
	private $glob;
	private $strPathCat = '';		
	
	//------------------------------------------------------------------------
	public function __construct(&$reg, &$glob){
		$this->reg = $reg;
		$this->glob = $glob;
		$this->strPathCat = DIR_RENDER_CAT_PHP.$this->glob->get('lang').'/';			
		}
	
	//------------------------------------------------------------------------
	public function getAllCategories(){
		//return the array
		return $this->getAllCategoriesDB();
		}

	//------------------------------------------------------------------------
	public function getFilters($catId){
		//return the array
		return $this->getSingleCategoryDB($catId);
		}

	//------------------------------------------------------------------------
	public function getExercises($catId, $filterId){
		//return the array
		return $this->getSingleCategoryFilterDB($catId.'.'.$filterId);
		}


	//------------------------------------------------------------------------
	private function getAllCategoriesDB(){
		$path = $this->strPathCat.'all.data';
		if(is_readable($path)){
			$fh = @fopen($path, 'r');
			$str = @fread($fh, filesize($path));
			@fclose($fh);
			return unserialize($str);
			}
		return false;
		}	

	//------------------------------------------------------------------------
	private function getSingleCategoryDB($id){
		$path = $this->strPathCat.$id.'.data';
		if(is_readable($path)){
			$fh = @fopen($path, 'r');
			$str = @fread($fh, filesize($path));
			@fclose($fh);
			return unserialize($str);
			}
		return false;
		}	

	//------------------------------------------------------------------------
	private function getSingleCategoryFilterDB($id){
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