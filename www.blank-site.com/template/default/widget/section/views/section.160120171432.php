<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	Section view

*/

class widgetSectionView{

	//vars
	private $wname;

	//construct
	public function __construct( $wname){
		//
		$this->wname = $wname;
		}
		
	public function getView($arr){
		//on check si les images sont la
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
			$strOutput .= '<div id="'.$this->wname.'" class="section">';
			//items
			$cmpt = 0;
			foreach($arr as $k=>$v){
				//inner carousel
				$cmpt++;
				if($cmpt < count($arr)){
					$strOutput .= '<div class="section-inner middle" style="background:'.$v['bgcolor'].';">';
				}else{
					$strOutput .= '<div class="section-inner" style="background:'.$v['bgcolor'].';">';	
					}
				if($v['link'] != ''){
					$strOutput .= '<a href="'.$v['link'].'">';
					}
				if($v['text'] != ''){
					if($v['img'] != ''){
						$strOutput .= '<div class="section-caption">';
					}else{
						$strOutput .= '<div class="section-caption noimg">';
						}
					$strOutput .= '<div class="section-block">';
					if($v['text'] != ''){
						$strOutput .= safeReverse($v['text']);
						}
					$strOutput .= '</div>';
					$strOutput .= '</div>';
					}
				if($v['img'] != ''){
					$strOutput .= '<div class="section-item" style="opacity:'.$v['opacity'].';">';
					$strOutput .= '<img class="section-img" src="'.PATH_IMAGE.$v['img'].'" alt="">';
					$strOutput .= '</div>';		
					}
				if($v['link'] != ''){
					$strOutput .= '</a>';
					}
				//close section-inner		
				$strOutput .= '</div>';
				}			
			//close section class
			$strOutput .= '</div>';
			}
		//return outpur of the view
		return $strOutput;
		}

	
	}
	
//END	
	
	
	
	
	