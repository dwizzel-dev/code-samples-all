<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	socialmedia controller

*/

class widgetSocialMediaController{
	
	//vars
	private $reg;
	private $data;
	private $wname;
	private $lang;
	
	//construct
	public function __construct($reg, $wname, $lang){
		$this->reg = $reg;
		$this->wname = $wname;
		$this->lang = $lang;
		}
	
	//get the code of the vue	
	public function getWidget(){
		//require
		require_once(DIR_WIDGET.'socialmedia/model/socialmedia.php');
		$oModel = new widgetSocialMediaModel($this->reg, $this->wname, $this->lang);
		//get the data
		$this->data = $oModel->getData();
		if(!$this->data){
			return false;
			}
		return true; 
		}
		
		
	//get the code of the vue	
	public function getHtml(){
		require_once(DIR_WIDGET.'socialmedia/views/socialmedia.php');
		$oView = new widgetSocialMediaView($this->wname);
		//return the code
		return $oView->getView($this->data);	
		}
		
	
		
	}

	
	
	
//END	
	
	
	
	
	



