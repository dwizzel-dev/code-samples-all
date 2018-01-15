<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	Module view

*/

class widgetModuleView{

	//vars
	private $wname;

	//construct
	public function __construct( $wname){
		//
		$this->wname = $wname;
		}
		
	public function getView($arr){
		$strOutput = '';
		if(is_array($arr) && count($arr)){
			//carousel
			$strOutput .= '<div id="'.$this->wname.'" class="module">';
			//items
			$cmpt = 0;
			foreach($arr as $k=>$v){
				//inner carousel
				$iStyleType = $cmpt%2;
				$strOutput .= '<div class="module-inner '.'bg-'.$iStyleType.' '.'color-'.$iStyleType.'">';
				if($v['text'] != ''){
					$strOutput .= '<div class="module-item">';
					$strOutput .= '<div class="img">';
					if($v['link'] != ''){
						$strOutput .= '<a href="'.$v['link'].'"><img class="module-img" src="'.PATH_IMAGE.$v['img'].'" alt=""></a>';
					}else{
						$strOutput .= '<img class="module-img" src="'.PATH_IMAGE.$v['img'].'" alt="">';
						}
					$strOutput .= '</div>';
					//title
					$strOutput .= '<div class="title">';
					$strOutput .= '<h3>'.$v['title'].'</h3>';
					$strOutput .= '</div>';
					$strOutput .= '</div>';		
					
					$strOutput .= '<div class="module-caption">';
					$strOutput .= '<div class="module-block">';
					if($v['text'] != ''){
						$strOutput .= safeReverse($v['text']);
						}
					$strOutput .= '</div>';
					$strOutput .= '</div>';
					}
				//close module-inner		
				$strOutput .= '</div>';
				// 
				$cmpt++;		
				}			
			//close module class
			$strOutput .= '</div>';
			}
		//return outpur of the view
		return $strOutput;
		}

	
	}
	
//END	
	
	
	
	
	