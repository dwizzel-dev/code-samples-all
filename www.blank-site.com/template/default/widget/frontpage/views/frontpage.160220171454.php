<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	Frontpage view

*/

class widgetFrontpageView{

	//vars
	private $wname;

	//construct
	public function __construct( $wname){
		//
		$this->wname = $wname;
		}
		
	public function getView($arr){
		//si ds images vide alors on veut juste du text 
		//pas la meme chose que si les images sont disparus ou corrompue
		foreach($arr as $k=>$v){
			if($v['img'] != ''){
				if(!is_file(DIR_MEDIA.$v['img'])){
					unset($arr[$k]);
					}
				}
			}
		$strOutput = '';
		$arr = $arr;
		if(count($arr)){
			//carousel
			$strOutput .= '<div id="'.$this->wname.'" class="frontpage">';
			//items
			$cmpt = 0;
			foreach($arr as $k=>$v){
				//inner carousel
				$cmpt++;
				if($cmpt < count($arr)){
					$strOutput .= '<div class="frontpage-inner middle">';
				}else{
					$strOutput .= '<div class="frontpage-inner">';	
					}
				if($v['link'] != ''){
					$strOutput .= '<a href="'.$v['link'].'">';
					}
				if($v['text'] != ''){
					if($v['img'] != ''){
						$strOutput .= '<div class="frontpage-caption">';
						$strOutput .= '<div class="frontpage-block">';
					}else{
						$strOutput .= '<div class="frontpage-caption noimg">';
						$strOutput .= '<div class="frontpage-block noimg">';
						}	
					if($v['text'] != ''){
						$strOutput .= safeReverse($v['text']);
						}
					$strOutput .= '</div>'; //close frontpage-block
					$strOutput .= '</div>'; //close frontpage-caption
					}
				if($v['img'] != ''){
					$strOutput .= '<div class="frontpage-item">';
					$strOutput .= '<img class="frontpage-img" src="'.PATH_IMAGE.$v['img'].'" alt="'.PATH_IMAGE.$v['img'].'">';
					$strOutput .= '</div>';	
				}else{
					// si on enleve image
					$strOutput .= '<div class="frontpage-item noimg"></div>';
					}
				if($v['link'] != ''){
					$strOutput .= '</a>';
					}
				//close frontpage-inner		
				$strOutput .= '</div>';
				}			
			//close frontpage class
			$strOutput .= '</div>';
			}
		//return outpur of the view
		return $strOutput;
		}

	
	}
	
//END	
	
	
	
	
	