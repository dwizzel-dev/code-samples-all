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
			$strOutput .= '<div id="'.$this->wname.'" class="rows section">';
			$strOutput .= '<div class="cols">';
			//items
			$cmpt = 0;
			foreach($arr as $k=>$v){
				//inner carousel
				$cmpt++;
				$strOutput .= '<div class="rows '.$v['name'].'">';
				$strOutput .= '<div class="cols section-inner ';
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
				//le text	
				if($v['text'] != ''){
					$strOutput .= '<div class="rows section-caption">';
					$strOutput .= '<div class="cols section-block">';
					if($v['text'] != ''){
						$strOutput .= safeReverse($v['text']);
						}
					$strOutput .= '</div>';
					$strOutput .= '</div>';
					}
				//link	
				if($v['link'] != ''){
					$strOutput .= '</a>';
					}
				//close section-inner		
				$strOutput .= '</div>';
				//close rows	
				$strOutput .= '</div>';
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
	
	
	
	
	