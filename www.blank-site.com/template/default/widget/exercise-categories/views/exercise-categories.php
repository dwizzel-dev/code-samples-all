<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	ExerciseBox view for listing categories

*/

class widgetExerciseCategoriesView{

	//
	private $wname;
	private $glob;
	
	//------------------------------------------------------------------------
	//construct
	public function __construct($wname, &$glob){
		$this->wname = $wname;
		$this->glob = $glob;
		}
		
	//------------------------------------------------------------------------
	public function getListingViewCategories(&$arr, $css_class = '', $title = '', $infos = ''){
		//array par lettre		
		$strAnchorName = 'letter-'; 
		$arrByLetter = array();
		foreach($arr as $k=>$v){
			if(!isset($arrByLetter[$v['letter']])){
				$arrByLetter[$v['letter']] = array();
				}
			array_push($arrByLetter[$v['letter']], $v['id']);
			}
		ksort($arrByLetter);
		$strLink = $this->glob->getArray('links-by-key', 'exercises-categories-'.$this->glob->get('lang_prefix'));
		$strOutput = '';
		//
		$strOutput .= '<span itemscope="" itemtype="http://schema.org/ItemList">';
		if($title != ''){
			$strOutput .= '<div class="rows"><div class="cols"><h1 itemprop="name">'.$title.'</h1></div></div>';
			}
		//
		if($infos != ''){
			$strOutput .= '<div class="rows"><div class="cols c9"><p itemprop="description">'.$infos.'</p></div></div>';
			}
		//on met un listing des lettres a cliquer dessus 
		//on va utiliser les memes style que la pagination
		$strOutput .= '<div class="rows"><div class="cols">';
		$strOutput .= '<div class="pagination letter">';
		$strOutput .= '<div class="pagination-centered">';
		$strOutput .= '<ul>';
		foreach($arrByLetter as $k=>$v){
			$strOutput .= '<li class="letter"><a class="pagenum toupper" href="#'.$strAnchorName.$k.'">'.$k.'</a></li>';
			}
		$strOutput .= '</ul>';	
		$strOutput .= '</div>';	
		$strOutput .= '</div>';
		$strOutput .= '</div></div>';		
		//
		$strOutput .= '<div class="rows"><div class="cols">';	
		$strOutput .= '<div class="'.$css_class.'" id="'.$this->wname.'">';
		foreach($arrByLetter as $k=>$v){
			$strOutput .= '<div class="rows"><div class="cols">';
			$strOutput .= '<h3 id="'.$strAnchorName.$k.'">'.$k.'</h3>';
			$strOutput .= '<ol title="'.htmlSafeTag($k).'">';
			$iListPosition = 0;	
			//	
			foreach($v as $k2=>$v2){
				$strLinkLi = $strLink.$arr[$v2]['href'].DEFAULT_ID_SEPARATOR.$arr[$v2]['id'].'/';	
				//
				$strOutput .= '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">';
				$strOutput .= '<meta itemprop="position" content="'.($iListPosition++).'">';
				$strOutput .= '<a itemprop="url" href="'.$strLinkLi.'" class="link" title="'.htmlSafeTag($arr[$v2]['html']).'"><span itemprop="name">'.$arr[$v2]['html'].'</span></a>';
				$strOutput .= '<div class="rows"><div class="cols c9">';
				$strOutput .= '<p itemprop="description">'.$arr[$v2]['description'].'</p>';
				$strOutput .= '</div></div>';
				$strOutput .= '</li>';
				}
			$strOutput .= '</ol>';
			$strOutput .= '</div></div>';
			}
		$strOutput .= '</div>';
		$strOutput .= '</div></div>';
		$strOutput .= '</span>';
		//send out
		return $strOutput;
		
		}

	//------------------------------------------------------------------------
	public function getListingViewFilters(&$arr, $css_class = '', $title = '', $infos = ''){
		//array par lettre		
		$strAnchorName = 'letter-'; 
		$arrByLetter = array();
		foreach($arr['filters'] as $k=>$v){
			if(!isset($arrByLetter[$v['letter']])){
				$arrByLetter[$v['letter']] = array();
				}
			array_push($arrByLetter[$v['letter']], $v['id']);
			}
		ksort($arrByLetter);
		$strLink = $this->glob->getArray('links-by-key', 'exercises-categories-'.$this->glob->get('lang_prefix')).$arr['href'].DEFAULT_ID_SEPARATOR.$arr['id'].'/';
		//
		$strOutput = '';
		$strOutput .= '<span itemscope="" itemtype="http://schema.org/ItemList">';
		if($title != ''){
			$strOutput .= '<div class="rows"><div class="cols"><h1 itemprop="name">'.$title.'</h1></div></div>';
			}
		//
		if($infos != ''){
			$strOutput .= '<div class="rows"><div class="cols c9"><p itemprop="description">'.$infos.'</p></div></div>';
			}
		//on met un listing des lettres a cliquer dessus 
		//on va utiliser les memes style que la pagination
		$strOutput .= '<div class="rows"><div class="cols">';
		$strOutput .= '<div class="pagination letter">';
		$strOutput .= '<div class="pagination-centered">';
		$strOutput .= '<ul>';
		foreach($arrByLetter as $k=>$v){
			$strOutput .= '<li class="letter"><a class="pagenum toupper" href="#'.$strAnchorName.$k.'">'.$k.'</a></li>';
			}
		$strOutput .= '</ul>';	
		$strOutput .= '</div>';	
		$strOutput .= '</div>';
		$strOutput .= '</div></div>';
		$strOutput .= '<div class="rows"><div class="cols">';
		$strOutput .= '<div class="'.$css_class.'" id="'.$this->wname.'">';
		//
		foreach($arrByLetter as $k=>$v){
			$strOutput .= '<div class="rows"><div class="cols">';
			$strOutput .= '<h3 id="'.$strAnchorName.$k.'">'.$k.'</h3>';	
			$strOutput .= '<ol title="'.htmlSafeTag($k).'">';
			$iListPosition = 0;	
			//	
			foreach($v as $k2=>$v2){
				$strLinkLi = $strLink.$arr['filters'][$v2]['href'].DEFAULT_ID_SEPARATOR.$arr['filters'][$v2]['id'].'/';	
				//
				$strOutput .= '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">';
				$strOutput .= '<meta itemprop="position" content="'.($iListPosition++).'">';
				$strOutput .= '<a itemprop="url" href="'.$strLinkLi.'" class="link" title="'.htmlSafeTag($arr['filters'][$v2]['html']).'"><span itemprop="name">'.$arr['filters'][$v2]['html'].'</span></a>';
				$strOutput .= '<div class="rows"><div class="cols c9">';
				$strOutput .= '<p itemprop="description">'.$arr['filters'][$v2]['description'].'</p>';
				$strOutput .= '</div></div>';
				$strOutput .= '</li>';
				}
			$strOutput .= '</ol>';
			$strOutput .= '</div></div>';
			}
		$strOutput .= '</div></div>';	
		//
		$strOutput .= '</div>';
		$strOutput .= '</span>';
		//send out
		return $strOutput;
		
		}

	//------------------------------------------------------------------------
	public function getListingViewExercises(&$arr, $css_class = '', $title = '', $infos = ''){
		//array par lettre		
		$strAnchorName = 'letter-'; 
		$arrByLetter = array();
		foreach($arr['exercises'] as $k=>$v){
			if(!isset($arrByLetter[$v['letter']])){
				$arrByLetter[$v['letter']] = array();
				}
			array_push($arrByLetter[$v['letter']], $v['id']);
			}
		ksort($arrByLetter);
		$strLink = $this->glob->getArray('links-by-key', 'exercises-details-'.$this->glob->get('lang_prefix'));
		//
		$strOutput = '';
		$strOutput .= '<span itemscope="" itemtype="http://schema.org/ItemList">';
		if($title != ''){
			$strOutput .= '<div class="rows"><div class="cols"><h1 itemprop="name">'.$title.'</h1></div></div>';
			}
		//
		if($infos != ''){
			$strOutput .= '<div class="rows"><div class="cols c9"><p itemprop="description">'.$infos.'</p></div></div>';
			}
		//on met un listing des lettres a cliquer dessus 
		//on va utiliser les memes style que la pagination
		$strOutput .= '<div class="rows"><div class="cols">';
		$strOutput .= '<div class="pagination letter">';
		$strOutput .= '<div class="pagination-centered">';
		$strOutput .= '<ul>';
		foreach($arrByLetter as $k=>$v){
			$strOutput .= '<li class="letter"><a class="pagenum toupper" href="#'.$strAnchorName.$k.'">'.$k.'</a></li>';
			}
		$strOutput .= '</ul>';	
		$strOutput .= '</div>';	
		$strOutput .= '</div>';
		$strOutput .= '</div></div>';
		$strOutput .= '<div class="rows"><div class="cols">';
		$strOutput .= '<div class="'.$css_class.'" id="'.$this->wname.'">';
		//
		foreach($arrByLetter as $k=>$v){
			$strOutput .= '<div class="rows"><div class="cols">';
			$strOutput .= '<h3 id="'.$strAnchorName.$k.'">'.$k.'</h3>';
			$strOutput .= '<ol title="'.htmlSafeTag($k).'">';
			$iListPosition = 0;	
			//	
			foreach($v as $k2=>$v2){
				$strLinkLi = $strLink.$arr['exercises'][$v2]['href'].DEFAULT_ID_SEPARATOR.$arr['exercises'][$v2]['id'].'/';
				//
				$strOutput .= '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">';
				$strOutput .= '<meta itemprop="position" content="'.($iListPosition++).'">';
				$strOutput .= '<a itemprop="url" href="'.$strLinkLi.'" class="link" title="'.htmlSafeTag($arr['exercises'][$v2]['html']).'"><span itemprop="name">'.$arr['exercises'][$v2]['html'].'</span></a>';
				$strOutput .= '<div class="rows"><div class="cols c9">';	
				$strOutput .= '<p itemprop="description">'.$arr['exercises'][$v2]['title'].'</p>';
				$strOutput .= '</div></div>';
				$strOutput .= '</li>';
				}
			$strOutput .= '</ol>';
			$strOutput .= '</div></div>';
			}
		//
		$strOutput .= '</div>';
		$strOutput .= '</div></div>';
		$strOutput .= '</span>';
		//send out
		return $strOutput;
		
		}

	}
	
//END	
	
	
	
	
	