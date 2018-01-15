<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	Section controller

*/

class widgetSectionController{
	
	//vars
	private $reg;
	private $wname;
	private $glob;
	private $data;
		
	//construct
	public function __construct(&$reg, &$glob, $wname){
		$this->reg = $reg;
		$this->glob = $glob;
		$this->wname = $wname;
		}
	
	//get the code of the vue	
	public function getWidget(){
		//require
		require_once(DIR_WIDGET.'section/model/section.php');
		$oModel = new widgetSectionModel($this->reg, $this->glob, $this->wname);
		//get the data
		$this->data = $oModel->getData();
		//si pas bon ou pas la	
		if(!$this->data){
			return false;
			}
		//minor check
		if(!count($this->data)){
			return false;
			}
		//ok
		return true; 
		}
		
		
	//get the code of the vue	
	public function getHtml(){
		require_once(DIR_WIDGET.'section/views/section.php');
		$oView = new widgetSectionView($this->wname);
		//return the code
		return $oView->getView($this->data);	
		}
		
	
		
	}

	
	
	
//END	
	
	
	
	
	



