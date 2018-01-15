<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	Module controller

*/

class widgetModuleController{
	
	//vars
	private $reg;
	private $glob;	
	private $wname;
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
		require_once(DIR_WIDGET.'module/model/module.php');
		$oModel = new widgetModuleModel($this->reg, $this->glob, $this->wname);
		//get the data
		$this->data = $oModel->getData();
		//si pas bon ou pas la	
		if(!count($this->data)){
			return false;
			}
		//ok
		return true; 
		}
		
		
	//get the code of the vue	
	public function getHtml(){
		require_once(DIR_WIDGET.'module/views/module.php');
		$oView = new widgetModuleView($this->wname);
		//return the code
		return $oView->getView($this->data);	
		}
		
	
		
	}

	
	
	
//END	
	
	
	
	
	



