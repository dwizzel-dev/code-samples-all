<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	news controller

*/

class widgetNewsController{
	
	//vars
	private $reg;
	private $lang;
	private $wname;
	private $data;
	
	//construct
	public function __construct($reg, $wname, $lang){
		$this->reg = $reg;
		$this->lang = $lang;
		$this->wname = $wname;
		}
	
	//get the code of the vue	
	public function getWidget(){
		//require
		require_once(DIR_WIDGET.'news/model/news.php');
		$oModel = new widgetNewsModel($this->reg, $this->lang);
		//get the data
		$this->data = $oModel->getLatestNews(3);
		if(!$this->data){
			return false;
			}
		return true; 
		}
		
		
	//get the code of the vue	
	public function getHtml($arrLinks){
		require_once(DIR_WIDGET.'news/views/news.php');
		$oView = new widgetNewsView($this->wname);
		//return the code
		return $oView->getView($this->data, $arrLinks);	
		}
		
	
		
	}

	
	
	
//END	
	
	
	
	
	



