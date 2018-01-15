<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	Sidebar view

*/

class widgetSidebarView{

	//
	private $wname;
	private $cssClass;

	//construct
	public function __construct($wname, $css_class = ''){
		$this->wname = $wname;
		$this->cssClass = $css_class;
		}
		
	public function getListingView($arr, $title){
		$strOutput = '';
		$strOutput .= '<div class="'.$this->cssClass.'" id="'.$this->wname.'">';	
		$strOutput .= '<h3>'.$title.'</h3>';	
		$strOutput .= '<ul itemscope="" itemtype="http://schema.org/SiteNavigationElement">';
		foreach($arr as $k=>$v){
			$strOutput .= '<li>';
			$strOutput .= '<a itemprop="url" href="'.$v['link'].'"><span itemprop="name">'.ucfirst($v['text']).'</span></a>';
			$strOutput .= '</li>';
			}
		$strOutput .= '</ul>';
		$strOutput .= '</div>';
		//send out
		return $strOutput;
		
		

		
		
		}

	
	}
	
//END	
	
	
	
	
	