<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	pager controller

*/

class widgetPagerController{
	
	//vars
	private $wname;
	private $data;
	
	//construct
	public function __construct($wname, $data){
		$this->wname = $wname;
		$this->data = $data;
		}
	
	//get the code of the vue	
	public function getHtml(){
		require_once(DIR_WIDGET.'pager/views/pager.php');
		$oView = new widgetPagerView($this->wname);
		//return the code
		return $oView->getView($this->data);
		}
		
	
		
	}

	
	
	
//END	
	
	
	
	
	



