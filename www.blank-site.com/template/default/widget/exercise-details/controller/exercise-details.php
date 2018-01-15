<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	ExerciseBox controller for details exercises

*/

class widgetExerciseDetailsController{
	
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
	public function getHtml($css_class = ''){
		require_once(DIR_WIDGET.'exercise-details/views/exercise-details.php');
		$oView = new widgetExerciseDetailsView($this->wname, $this->glob);
		//return the code
		return $oView->getDetailsView($this->data, $css_class);
		}
		
	
		
	}

	
	
	
//END	
	
	
	
	
	



