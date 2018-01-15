<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	sidebar controller

*/

class widgetSidebarController{
	
	//vars
	private $wname;
	private $data;
	private $type;
	
	//construct
	public function __construct($wname, $type, $data){
		$this->wname = $wname;
		$this->data = $data;
		$this->type = $type;
		}
	
	//get the code of the vue	
	public function getHtml($css_class = '', $title = ''){
		require_once(DIR_WIDGET.'sidebar/views/sidebar.php');
		$oView = new widgetSidebarView($this->wname, $css_class);
		//return the code
		if($this->type == 'listing'){ //un array de lien en li
			return $oView->getListingView($this->data, $title);
			}
		}
		
	
		
	}

	
	
	
//END	
	
	
	
	
	



