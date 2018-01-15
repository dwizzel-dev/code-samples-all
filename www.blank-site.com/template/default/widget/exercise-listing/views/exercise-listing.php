<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	ExerciseBox view for listing exercises

*/

class widgetExerciseListingView{

	//
	private $wname;
	private $glob;
	
	//--------------------------------------------------------------------------------------------------
	//construct
	public function __construct($wname, &$glob){
		$this->wname = $wname;
		$this->glob = $glob;
		}
	
	//--------------------------------------------------------------------------------------------------	
	public function getListingView(&$arr, $css_class = '', $title = '', $infos = ''){
		$strOutput = '';
		//$strOutput .= '<div class="rows"><div class="cols">';
		$strOutput .= '<div class="rows '.$css_class.'" id="'.$this->wname.'">';
		$strOutput .= '<div class="rows"><div class="cols">';	
		$strOutput .= '<ul itemscope="" itemtype="http://schema.org/ItemList">';
		if($title != ''){
			$strOutput .= '<div class="rows"><div class="cols"><h2 itemprop="name">'.$title.'</h2></div></div>';
			}
		if($infos != ''){
			$strOutput .= '<div class="rows pad pbt"><div class="cols"><div class="infos">'.$infos.'</div></div></div>';
			}
		$iListPosition = 0;
		foreach($arr as $k=>$v){
			//	
			$strLink = $this->glob->getArray('links-by-key', 'exercises-details-'.$this->glob->get('lang_prefix'));
			$strLink .= $v['href'].DEFAULT_ID_SEPARATOR.$v['id'].'/';	
			//
			$strOutput .= '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">';
			$strOutput .= '<div class="rows pad pb">';
			$strOutput .= '<meta itemprop="position" content="'.($iListPosition++).'">';
			//image
			$strOutput .= '<div class="cols c2">';
			$strOutput .= '<div class="image">';
			$strOutput .= '<a href="'.$strLink.'" title="'.htmlSafeTag(ucfirst($v['html'])).'"><img onerror="this.src=\''.PATH_IMAGE_DEFAULT.'\'" src="'.PATH_IMAGE_EXERCISE.$v['thumb0'].'" alt="'.htmlSafeTag(ucfirst($v['html'])).'"></a>';
			$strOutput .= '</div>';
			$strOutput .= '</div>';
			$strOutput .= '<div class="cols c7 text">';
			//le title
			$strOutput .= '<h3><a itemprop="url" href="'.$strLink.'" class="link" title="'.htmlSafeTag(ucfirst($v['html'])).'"><span itemprop="name">'.$v['html'].'</span></a></h3>';
			//la description
			$strOutput .= '<p itemprop="description">'.$v['title'].'</p>';
			$strOutput .= $this->getCategories($v['categories']);
			$strOutput .= '</div>';
			$strOutput .= '</div>';
			$strOutput .= '</li>';
			}
		$strOutput .= '</ul>';
		$strOutput .= '</div></div>';
		$strOutput .= '</div>';
		//$strOutput .= '</div></div>';
		//send out
		return $strOutput;
		
		}
		
		
	//--------------------------------------------------------------------------------------------------
	private function getCategories(&$arr){
		$str = '';
		//les liens vers les categories
		if(count($arr)){
			$strCategories = '';
			$strBaseCategoriesLink = $this->glob->getArray('links-by-key', 'exercises-categories-'.$this->glob->get('lang_prefix'));
			foreach($arr as $k=>$v){
				$strCategoriesLink = $strBaseCategoriesLink.$v['href'].DEFAULT_ID_SEPARATOR.$v['id'].'/';
				$strCategories .= '<a href="'.$strCategoriesLink.'" title="'.htmlSafeTag($v['html']).'">'.$v['html'].'</a>, ';
				}
			if($strCategories != ''){
				$strCategories = substr($strCategories, 0, strlen($strCategories) - 2);	
				}
			$str .= '<p class="categories">'._T('categories:').' '.$strCategories.'</p>';	
			}	
		return $str;
		}

	
	}
	
//END	
	
	
	
	
	