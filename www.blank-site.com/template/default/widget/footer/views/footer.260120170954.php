<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	Frontpage view

*/

class widgetFooterView{

	//vars
	private $wname;

	//construct
	public function __construct( $wname){
		//
		$this->wname = $wname;
		}
		
	public function getView($arr){
		$strOutput = '';
		if(count($arr)){
			//carousel
			$strOutput .= '<div class="footer-infos">';
			foreach($arr as $k=>$v){
				if($v['active']){
					$strOutput .= '<div class="infos-inner">'.safeReverse($v['text']).'</div>';
					}
				}			
			//close section class
			$strOutput .= '</div>';
			}
		//return outpur of the view
		return $strOutput;
		}

	
	}
	
//END	
	
	
	
	
	