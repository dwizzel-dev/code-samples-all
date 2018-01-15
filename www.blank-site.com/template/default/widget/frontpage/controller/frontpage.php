<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	Frontpage controller

*/

class widgetFrontpageController{
	
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
		require_once(DIR_WIDGET.'frontpage/model/frontpage.php');
		$oModel = new widgetFrontpageModel($this->reg, $this->glob, $this->wname);
		//get the data
		$data = $oModel->getData();
		//si pas bon ou pas la	
		if(!$data){
			return false;
			}
		//on va chercher voir si doit etre affiche ou pas
		$linkId = $this->glob->get('link_id');
		$this->data = array();
		foreach($data as $k=>$v){
			$arrLinks = explode(',', $v['links']);
			if(in_array($linkId, $arrLinks)){
				$this->data[$k] = $v;
				}
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
		require_once(DIR_WIDGET.'frontpage/views/frontpage.php');
		$oView = new widgetFrontpageView($this->wname);
		//return the code
		return $oView->getView($this->data);	
		}
		
	
		
	}

	
	
	
//END	
	
	
	
	
	



