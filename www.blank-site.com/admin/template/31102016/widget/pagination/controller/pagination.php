<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	pagination controller

*/

class widgetPaginationController{
	
	//vars
	private $wname;
	private $linkId;
	private $total;
	private $start;
	private $max;
	
	//construct
	public function __construct($wname, $total, $start, $max, $link_id){
		$this->wname = $wname;
		$this->linkId = $link_id;
		$this->total = $total;
		$this->start = $start;
		$this->max = $max;
		}
	
	//get the code of the vue	
	public function getWidget(){
		//require
		require_once(DIR_WIDGET.'pagination/model/pagination.php');
		$oModel = new widgetPaginationModel($this->total, $this->start, $this->max, $this->linkId);
		//get the data
		$this->data = $oModel->getData();
		if(!$this->data){
			return false;
			}
		return true; 
		}
		
		
	//get the code of the vue	
	public function getHtml($arrLinks){
		require_once(DIR_WIDGET.'pagination/views/pagination.php');
		$oView = new widgetPaginationView($this->wname);
		//return the code
		return $oView->getView($this->data, $arrLinks);
		}
		
	
		
	}

	
	
	
//END	
	
	
	
	
	



