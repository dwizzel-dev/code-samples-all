<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	socialmedia view

*/

class widgetSocialMediaView{

	//vars
	private $wname;

	//construct
	public function __construct( $wname){
		//
		$this->wname = $wname;
		}
		
	public function getView($arr){
		$strOutput = '';
		//carousel
		$strOutput .= '<ul id="'.$this->wname.'" class="social-media">';
		foreach($arr as $k=>$v){
			$strOutput .= '<li class="footer-mediaicons"><a rel="nofollow" href="'.$v['url'].'" target="_blank"><img src="'.PATH_SOCIALMEDIA_ICONS.$v['icon'].'" alt="'.$v['alt'].'" title="'.$v['alt'].'"></a></li>';
			}	
		$strOutput .= '</ul>';
		
		//return outpur of the view
		return $strOutput;
		}

	
	}
	
//END	
	
	
	
	
	