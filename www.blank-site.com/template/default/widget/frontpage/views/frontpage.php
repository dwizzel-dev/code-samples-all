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
			$strOutput .= '<div id="'.$this->wname.'" class="rows noprint frontpage">';
			$strOutput .= '<div class="cols">';
			//items
			$cmpt = 0;
			foreach($arr as $k=>$v){
				if($v['active']){	
					//inner carousel
					$cmpt++;
					$strOutput .= '<div class="rows '.$v['name'].'">';
					$strOutput .= '<div class="cols frontpage-inner '.$v['css_class'].' ';
					//si img on veut un min height
					if($v['img'] != ''){
						$strOutput .= ' withimg ';	
						}
					//spacers et border	
					if($cmpt < count($arr) && count($arr) != 1){
						$strOutput .= ' middle ';
						}
					//img background
					if($v['img'] != ''){
						$strOutput .= '" style="background-image: url(\''.PATH_IMAGE.$v['img'].'\');background-color:'.$v['bgcolor'];
					}else{
						$strOutput .= '" style="background:'.$v['bgcolor'].';';
						}
					$strOutput .= '">';	
					//link
					if($v['link'] != ''){
						$strOutput .= '<a href="'.$v['link'].'">';
						}
					if($v['text'] != ''){
						$strOutput .= '<div class="rows frontpage-caption">';
						$strOutput .= '<div class="cols frontpage-block">';
						if($v['text'] != ''){
							$strOutput .= safeReverse($v['text']);
							}
						$strOutput .= '</div>';
						$strOutput .= '</div>';
						}
					if($v['link'] != ''){
						$strOutput .= '</a>';
						}
					//close section-inner		
					$strOutput .= '</div>';
					$strOutput .= '</div>';
					}			
				}
			//close section class
			$strOutput .= '</div>';
			$strOutput .= '</div>';
			}
		//return outpur of the view
		return $strOutput;
		}

	
	}
	
//END	
	
	
	
	
	