<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	ExerciseBox controller for listing categories

*/


class widgetExerciseCategoriesController{
	
	//vars
	private $wname;
	private $data;
	private $reg;
	private $glob;
	
	//------------------------------------------------------------------------
	//construct
	public function __construct(&$reg, &$glob, &$data, $wname = ''){
		$this->reg = $reg;
		$this->glob = $glob;
		$this->data = $data;
		$this->wname = $wname;
		}

	//------------------------------------------------------------------------
	//check si empty
	public function getWidget(){
		if($this->data === false || !is_array($this->data) || count($this->data) === 0){
			return false;
			}
		return true;
		}
	
	//------------------------------------------------------------------------
	//get the code of the vue	
	public function getHtml($type = 'all', $css_class = '', $title = '', $infos = ''){
		require_once(DIR_WIDGET.'exercise-categories/views/exercise-categories.php');
		$oView = new widgetExerciseCategoriesView($this->wname, $this->glob);
		//return the code
		if($type == 'categories'){
			return $oView->getListingViewCategories($this->data, $css_class, $title, $infos);
		}else if($type == 'filters'){
			return $oView->getListingViewFilters($this->data, $css_class, $title, $infos);
		}else if($type == 'exercises'){
			return $oView->getListingViewExercises($this->data, $css_class, $title, $infos);
		}else if($type == 'keywords'){
			return $oView->getListingViewExercises($this->data, $css_class, $title, $infos);
			}
		}
		
	
		
	}

	
	
	
//END	
	
	
	
	
	



