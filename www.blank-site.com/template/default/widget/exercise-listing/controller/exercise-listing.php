<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	ExerciseBox controller for listing exercises

*/

class widgetExerciseListingController{
	
	//vars
	private $wname;
	private $data;
	private $reg;
	private $glob;
	
	//construct
	public function __construct(&$reg, &$glob, &$data, $wname = ''){
		$this->reg = $reg;
		$this->glob = $glob;
		$this->data = $data;
		$this->wname = $wname;
		}

	//check si empty
	public function getWidget(){
		if($this->data === false || !is_array($this->data) || count($this->data) == 0){
			return false;
			}
		return true;
		}
	
	//get the code of the vue	
	public function getHtml($css_class = '', $title = '', $infos = ''){
		require_once(DIR_WIDGET.'exercise-listing/views/exercise-listing.php');
		$oView = new widgetExerciseListingView($this->wname, $this->glob);
		//return the code
		return $oView->getListingView($this->data, $css_class, $title, $infos);
		}
		
	
		
	}

	
	
	
//END	
	
	
	
	
	



