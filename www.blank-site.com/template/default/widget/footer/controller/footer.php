<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	Footer controller

*/

class widgetFooterController{
	
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
		require_once(DIR_WIDGET.'footer/model/footer.php');
		$oModel = new widgetFooterModel($this->reg, $this->glob, $this->wname);
		//get the data
		$this->data = $oModel->getData();
		//si pas bon ou pas la	
		if(!$this->data){
			return false;
			}
		//ok
		return true; 
		}
		
		
	//get the code of the vue	
	public function getHtml(){
		require_once(DIR_WIDGET.'footer/views/footer.php');
		$oView = new widgetFooterView($this->wname);
		//return the code
		return $oView->getView($this->data);	
		}
		
	
		
	}

	
	
	
//END	
	
	
	
	
	



