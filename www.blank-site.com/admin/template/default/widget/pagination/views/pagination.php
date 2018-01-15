<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	pagination view

*/

class widgetPaginationView{

	//
	private $wname;

	//construct
	public function __construct($wname){
		$this->wname = $wname;
		}
		
	public function getView($arr, $arrLinks){
		$strOutput = '';
		//pagination
		$iMod = $arr['total_count']%$arr['max_per_page'];
		$iMaxPage = intval($arr['total_count']/$arr['max_per_page']);
		$iMaxPage = ($iMod)?$iMaxPage+1:$iMaxPage;
		//page for loop
		$iMaxPageLoop = 0;
		if($iMaxPage > ($arr['max_per_page'] * $arr['max_pagination'])){
			$iMaxPageLoop = $arr['max_pagination'];
		}else{
			if($iMaxPage < $arr['max_pagination']){
				$iMaxPageLoop = $iMaxPage;
			}else{
				$iMaxPageLoop = $arr['max_pagination'];
				}
			}
		//start from
		$iStartPageNumber = 0;
		if(!$arr['start_page'] || $iMaxPageLoop < $arr['max_pagination']){
			$iStartPageNumber = 1;
		}else if(($arr['start_page'] + $arr['page_offset'])  < $iMaxPage){
			$iStartPageNumber = $arr['start_page'];
		}else{
			$iStartPageNumber = $iMaxPage - $arr['max_pagination'] + 1;
			}
		$strOutput .= '<div id="'.$this->wname.'" class="pagination pagination-centered">';
		$strOutput .= '<ul>';
		//if first page
		if(!$arr['start_page']){
			$strOutput .= '<li class="disabled"><a href="#">&lt;</a></li>';
		}else{
			$strOutput .= '<li><a href="'.$arrLinks[$arr['link_id']].'page-'.($arr['start_page'] - 1).'/">&lt;</a></li>';
			}
		//
		for($i=0;$i<$iMaxPageLoop;$i++){
			$strOutput .= '<li';
			if($arr['start_page'] != 0){
				if($arr['start_page']+1 == ($i+$iStartPageNumber)){
					$strOutput .= ' class="disabled" ';
					}
			}else if($i == 0){
				$strOutput .= ' class="disabled" ';
				}
			$strOutput .= '><a href="'.$arrLinks[$arr['link_id']].'page-'.(($i+$iStartPageNumber) - 1).'/">'.($i+$iStartPageNumber).'</a></li>';
			}
		if(($arr['start_page'] + 1) >= (($iStartPageNumber - 1)+ $iMaxPageLoop)){
			$strOutput .= '<li class="disabled"><a href="#">&gt;</a></li>';
		}else{
			$strOutput .= '<li><a href="'.$arrLinks[$arr['link_id']].'page-'.($arr['start_page'] + 1).'/">&gt;</a></li>';
			}
		$strOutput .= '</ul>';
		$strOutput .= '</div>';	
		//end pagination
		return $strOutput;
		
		}

	
	}
	
//END	
	
	
	
	
	