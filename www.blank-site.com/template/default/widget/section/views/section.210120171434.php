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
				$strOutput .= '<div class="section-inner ';
				//si img on veut un min height
				if($v['img'] != ''){
					$strOutput .= ' withimg ';	
					}
				//spacers et border	
				if($cmpt == 1){
					$strOutput .= ' radius-top ';
					if(count($arr) == 1){
						$strOutput .= ' radius-bottom ';	
					}else{
						$strOutput .= ' middle ';		
						}
				}else if($cmpt < count($arr)){
					$strOutput .= ' middle ';
				}else{
					$strOutput .= ' radius-bottom ';		
					}
				//img background
				if($v['img'] != ''){
					$strOutput .= '" style="background:'.$v['bgcolor'].' url(\''.PATH_IMAGE.$v['img'].'\');background-size:contain;background-repeat:no-repeat;';
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
					$strOutput .= '<div class="section-caption">';
					$strOutput .= '<div class="section-block">';
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
				}			
			//close section class
			$strOutput .= '</div>';
			}
		//return outpur of the view
		return $strOutput;
		}

	
	}
	
//END	
	
	
	
	
	