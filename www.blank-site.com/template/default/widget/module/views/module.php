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
			$strOutput .= '<div id="'.$this->wname.'" class="rows module">';
			$strOutput .= '<div class="cols">';
			//items
			$cmpt = 0;
			$cmptRows = 0;
			foreach($arr as $k=>$v){
				//inner carousel
				$iStyleType = $cmpt%2;
				//ouvre la row
				if(!$iStyleType){
					$strOutput .= '<div class="rows r-'.($cmptRows%2).'">';
					}
				$strOutput .= '<div class="cols c6 module-inner '.'c-'.$iStyleType.'">';
				if($v['text'] != ''){
					$strOutput .= '<div class="rows module-item">';
					//si une image
					if($v['img'] != ''){
						$strOutput .= '<div class="cols c6 img">';
						if($v['link'] != ''){
							$strOutput .= '<a href="'.$v['link'].'"><img class="module-img" src="'.PATH_IMAGE.$v['img'].'" alt=""></a>';
						}else{
							$strOutput .= '<img class="module-img" src="'.PATH_IMAGE.$v['img'].'" alt="">';
							}
						$strOutput .= '</div>';
						}
					//title	
					if($v['img'] != ''){	
						$strOutput .= '<div class="cols c6 title">';
					}else{
						$strOutput .= '<div class="cols c9 title">';
						}
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
				//close la row
				if($iStyleType){
					$strOutput .= '</div>';
					$cmptRows++;
					}
				// 
				$cmpt++;		
				}			
			//close module class
			$strOutput .= '</div>';
			$strOutput .= '</div>';
				
			}
		//return outpur of the view
		return $strOutput;
		}

	
	}
	
//END	
	
	
	
	
	