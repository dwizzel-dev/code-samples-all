<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	breadcrumbs view

*/

class widgetBreadcrumbsView{

	//
	private $wname;

	//construct
	public function __construct($wname){
		$this->wname = $wname;
		}
		
	public function getView($arr){
		$strDivider = '/';
		$strOutput = '';
		$strOutput .= '<div id="'.$this->wname.'" class="rows pad plr noprint breadcrumb">';
		$strOutput .= '<div class="cols">';
		$strOutput .= '<nav>';	
		$strOutput .= '<ul class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList" itemprop="breadcrumb">';
		//print_r($arr);
		//exit();
		if(is_array($arr) && count($arr)){
			$iMaxCount = count($arr);
			$iCmpt = 0;	
			foreach($arr as $k=>$v){
				$iCmpt++;
				$strOutput .= '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">';
				if(!$v['link']){
					$strOutput .= '<span itemprop="item"><span itemprop="name">'.$v['text'].'</span></span>';
				}else{
					$strOutput .= '<a itemprop="item" href="'.$v['link'].'" title="'.htmlSafeTag($v['text']).'"><span itemprop="name">'.$v['text'].'</span></a>';
					}
				if($iCmpt < $iMaxCount){
					$strOutput .= '<span class="divider">'.$strDivider.'</span>';
					}
				$strOutput .= '</li>';
				}
			}
		$strOutput .= '</ul>';
		$strOutput .= '</nav>';	
		$strOutput .= '</div>';	
		$strOutput .= '</div>';	
		return $strOutput;
		}

	
	}
	
//END	
	
	
	
	
	