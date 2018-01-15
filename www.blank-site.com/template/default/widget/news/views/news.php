<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	news view

*/

class widgetNewsView{

	//vars
	private $wname;

	//construct
	public function __construct( $wname){
		$this->wname = $wname;
		}
		
	public function getView($arr, $arrLinks){
		$strOutput = '';
		//items
		$bActive = true;
		$strOutput .= '<div class="rows" id="'.$this->wname.'">';
		$strOutput .= '<div class="cols">';
		foreach($arr as $k=>$v){
			$strOutput .= '<div class="rows item"><div class="cols">';
			$strOutput .= '<a href="'.$arrLinks[$v['link_id']].$v['id'].'/'.$v['alias'].'/"><h3>'.$v['title'].'</h3></a>';
			$strOutput .= '<p class="date">'.$v['date_added'].'</p>';
			$strOutput .= '<p class="category">'._T('category:').'<a href="'.$arrLinks[$v['link_id']].'">'.$v['category_title'].'</a></p>';
			$strOutput .= '<p class="preview">'.$v['preview'].'</p>';
			$strOutput .= '</div></div>';
			}	
		$strOutput .= '</div>';
		$strOutput .= '</div>';
		//return outpur of the view
		return $strOutput;
		}

	
	}
	
//END	
	
	
	
	
	