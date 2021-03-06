<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	ExerciseBox view for details exercises

@exemple:
		http://www.blank-site.com/en/exercises/details/seated-hip-extension-29761/	
		http://www.blank-site.com/en/exercises/details/sitting-differential-exam-28858/	
*/

class widgetExerciseDetailsView{

	//
	private $wname;
	private $glob;
	
	//--------------------------------------------------------------------------------------------------
	//construct
	public function __construct($wname, &$glob){
		$this->wname = $wname;
		$this->bBottomSettings = true;
		$this->glob = $glob;
		}
		
	//--------------------------------------------------------------------------------------------------
	public function getDetailsView(&$arr, $css_class = ''){

		//print_r($arr);
		//le title
		$strOutput = '<div class="rows"><div class="cols"><h1 itemprop="name">'.$arr['html'].'</h1></div></div>';
		//main div
		$strOutput .= '<div class="'.$css_class.'" id="'.$this->wname.'">';
		//les details de l'exercice	
		$strOutput .= '<div class="rows"><div class="cols">';
		$strOutput .= $this->getDetails($arr, $css_class);
		$strOutput .= '</div></div>';
		//settings de exercice dans cookie si il y a
		if(!$this->bBottomSettings){
			$strOutput .= '<div class="rows"><div class="cols">';
			$strOutput .= $this->getSettings($arr);
			$strOutput .= '</div></div>';
			}
		//le video
		$strOutput .= '<div class="rows pad pt noprint"><div class="cols">';
		$strOutput .= $this->getVideo($arr);
		$strOutput .= '</div></div>';	
		//les languages
		$strOutput .= '<div class="rows pad pt noprint"><div class="cols">';
		$strOutput .= $this->getInOtherLanguageView($arr['languages']);
		$strOutput .= '</div></div>';
		//les categories
		$strOutput .= '<div class="rows pad pt noprint"><div class="cols">';
		$strOutput .= $this->getCategoriesWithLi($arr['categories']);
		$strOutput .= '</div></div>';
		//les keywords
		$strOutput .= '<div class="rows pad pt noprint"><div class="cols">';
		$strOutput .= $this->getKeywords($arr['keywords']);
		$strOutput .= '</div></div>';
		//settings de exercice bottoms
		if($this->bBottomSettings){
			$strOutput .= '<div class="rows pad pt"><div class="cols">';
			$strOutput .= $this->getSettingsBottom($arr);
			$strOutput .= '</div></div>';
			}
		//close le main div
		$strOutput .= '</div>';
		//send out
		return $strOutput;
		}

	//--------------------------------------------------------------------------------------------------
	private function getDetails(&$arr, $css_class){	
		//les avrs neccessaire
		$strDate = date('Y-m-d H:i:s', $arr['datetime']);
		
		//div les photos
		$strOutput = '<div class="rows ex-images" itemscope="" itemprop="primaryImageOfPage" itemtype="http://schema.org/ImageObject">';
		//thumb0
		if($arr['pic0'] != ''){
			$strOutput .= '<div class="cols c4">';
			$strOutput .= '<div class="img">';
			$strOutput .= '<img itemprop="contentUrl" onerror="this.src=\''.PATH_IMAGE_DEFAULT.'\'" src="'.PATH_IMAGE_EXERCISE.$arr['pic0'].'" alt="'.htmlSafeTag(ucfirst($arr['html'])).'" title="'.htmlSafeTag(ucfirst($arr['html'])).'">';
			//close tags
			$strOutput .= '</div>';
			$strOutput .= '</div>';
			}
		//thumb1
		if($arr['pic1'] != ''){
			$strOutput .= '<div class="cols c4"><div class="img"><img onerror="this.src=\''.PATH_IMAGE_DEFAULT.'\'" src="'.PATH_IMAGE_EXERCISE.$arr['pic1'].'" alt="'.htmlSafeTag(ucfirst($arr['title'])).'" title="'.htmlSafeTag(ucfirst($arr['title'])).'"></div></div>';
			}
		//close le photos
		$strOutput .= '</div>';
		//title sous les images
		$strOutput .= '<div class="rows"><div class="cols c8"><p class="images-title"><span itemprop="name">'.$arr['title'].'</span> '.sprintf('(%s<span itemprop="author">%s</span>)', _T('picture from').' ', _T('physiotec hep software')).'</p></div></div>';
		
		//la description
		$strOutput .= '<div class="rows ex-description" itemscope="" itemprop="mainContentOfPage" itemtype="https://health-lifesci.schema.org/WebPageElement"><div class="cols">';
		$strOutput .= '<div class="rows"><div class="cols"><h2 itemprop="name">'._T('home exercise description').'</h2></div></div>';
		$strOutput .= '<div class="rows"><div class="cols c9"><p itemprop="description">'.$this->getTaggedDescription($arr['keywords'], $arr['description']).'</p></div></div>';
		//modification date	
		$strOutput .= '<div class="rows ex-date-modified"><div class="cols">'._T('last modification:').' <time itemprop="dateModified" datetime="'.$strDate.'">'.$strDate.'</time></div></div>';
		$strOutput .= '</div></div>';
		
		//
		return $strOutput;
		}
		
		
	//--------------------------------------------------------------------------------------------------
	private function getVideo(&$arr){
		$strOutput = '';	
		//le video si il y a 
		if($arr['pic0'] != '' && $arr['video'] != ''){
			$strOutput .= '<div class="ex-video"><h3>'._T('practice with the video').'</h3>';
			$strOutput .= '<div class="rows pad pt ex-images" itemscope="" itemprop="primaryImageOfPage" itemtype="http://schema.org/ImageObject">';	
			$strOutput .= '<div class="cols c6">';
			$strOutput .= '<div class="img">';
			$strOutput .= '<img itemprop="contentUrl" onerror="this.src=\''.PATH_IMAGE_DEFAULT.'\'" src="'.PATH_IMAGE_EXERCISE.$arr['pic0'].'" alt="'.htmlSafeTag(ucfirst($arr['html'])).'" title="'.htmlSafeTag(ucfirst($arr['html'])).'">';
			//the clickable video
			//$strOutput .= '<div id="'.$arr['video'].'" class="video-mask">&nbsp;</div>';
			//direct iframe pur le referencement par google
			$strOutput .= '<div class="video-mask"><iframe width="100%" height="100%" src="https://www.youtube.com/embed/'.$arr['video'].'?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe></div>';
			//close tags
			$strOutput .= '</div>';
			$strOutput .= '</div>';
			$strOutput .= '</div>';
			$strOutput .= '<div class="rows"><div class="cols c6"><p class="images-title">'.ucfirst($arr['html']).' ('._T('video from').' '._T('physiotec hep software').')</p></div></div>';
			}
		//
		return $strOutput;	
			
		}	

	//--------------------------------------------------------------------------------------------------
	private function getInOtherLanguageView(&$arr){	

		global $gLanguagesCode, $gLanguagesPrefix; //in the inc/hash.php file generated by admin script
		//
		$str = '';
		//	
		if(is_array($gLanguagesCode) && is_array($gLanguagesPrefix)){
			if(count($arr)){
				$str .= '<div class="ex-other-language">';
				$str .= '<h3>'._T('in other languages').'</h3>';
				$str .= '<p class="small">'._T('exercise are also offered on those languages').'</p>';
				$str .= '<ul class="languages" itemscope="" itemtype="http://schema.org/SiteNavigationElement">';
				foreach($arr as $k=>$v){
					$strLinkSearch = $this->glob->getArray('links-by-key', 'exercises-search-'.substr($k,0,2));
					$strLink = $this->glob->getArray('links-by-key', 'exercises-details-'.substr($k,0,2));
					$strLink .= $v['href'].DEFAULT_ID_SEPARATOR.$v['id'].'/';	
					//
					$str .= '<li>';
					$str .= '<h4>'.$gLanguagesCode[$k].'</h4>';
					$str .= '<ul class="exercises">';
					$str .= '<li><a itemprop="url" href="'.$strLink.'" title="'.htmlSafeTag(ucfirst($v['html'])).'"><span itemprop="name">'.$v['html'].'</span></a></li>';
					$str .= '</ul>';
					$str .= '</li>';
						
					}
				$str .= '</ul>';
				$str .= '</div>';
				}
			}
		//
		return $str;
		}

	//--------------------------------------------------------------------------------------------------
	private function getSettings(&$arr){	

		//la page de save
		$strSavedLink = $this->glob->getArray('links-by-key', 'exercises-saved-'.$this->glob->get('lang_prefix'));
		//la page de l'exercise		
		$strExLink = $this->glob->getArray('links-by-key', 'exercises-details-'.$this->glob->get('lang_prefix'));
		$strExLink .= $arr['href'].DEFAULT_ID_SEPARATOR.$arr['id'].'/';
		//on va strippper le nom de domaine pour prendre moins d'espace
		$strExLink = str_replace(PATH_WEB, '/', $strExLink);

		$str = '<form id="form-exercise-settings">';
		
		$str .= '<input type="hidden" name="id" value="'.htmlSafeTag($arr['id']).'">';
		$str .= '<input type="hidden" name="na" value="'.htmlSafeTag($arr['html']).'">';
		$str .= '<input type="hidden" name="li" value="'.htmlSafeTag($strExLink).'">';
		$str .= '<input type="hidden" name="ti" value="'.htmlSafeTag(time()).'">';
		/*
		if($arr['thumb0'] != ''){
			$str .= '<input type="hidden" name="im" value="'.htmlSafeTag(PATH_IMAGE_EXERCISE.$arr['thumb0']).'">';
		}else{
			$str .= '<input type="hidden" name="im" value="">';
			}
		*/
		
		$str .= '<div class="rows pad pbt ex-settings">';
		$str .= '<div class="cols c9">';
		$str .= '<h4>'._T('free home exercise program settings').'</h4>';
		$str .= '<p>'.sprintf(_T('you can edit this form to enter exercises settings for you or your physician, Or you can go see your existing %ssaved home exercise program%s.'), '<a href="'.$strSavedLink.'">', '</a>').'</p>';
		//$str .= '<div class="table settings">';
		$str .= '<div class="rows">';
		$str .= '<div class="cols c4">';
		$str .= '<p>'._T('repetition:').'</p>';
		$str .= '<p><input type="text" name="re" value=""></p>';
		$str .= '</div>';
		$str .= '<div class="cols c4">';
		$str .= '<p>'._T('hold:').'</p>';
		$str .= '<p><input type="text" name="ho" value=""></p>';
		$str .= '</div>';
		$str .= '<div class="cols c4">';
		$str .= '<p>'._T('weight:').'</p>';
		$str .= '<p><input type="text" name="we" value=""></p>';
		$str .= '</div>';
		$str .= '</div>';
		$str .= '<div class="rows">';
		$str .= '<div class="cols c4">';
		$str .= '<p>'._T('tempo:').'</p>';
		$str .= '<p><input type="text" name="te" value=""></p>';
		$str .= '</div>';
		$str .= '<div class="cols c4">';
		$str .= '<p>'._T('rest:').'</p>';
		$str .= '<p><input type="text" name="rt" value=""></p>';
		$str .= '</div>';
		$str .= '<div class="cols c4">';
		$str .= '<p>'._T('frequency:').'</p>';
		$str .= '<p><input type="text" name="fq" value=""></p>';
		$str .= '</div>';
		$str .= '</div>';
		//$str .= '</div>';
		$str .= '<div class="rows pad pt">';
		$str .= '<div class="cols">';
		$str .= '<span class="btn-group">';
		$str .= '<p><button id="butt-save-exercise" class="btn blue">'._T('save').'</button>&nbsp;&nbsp;';
		$str .= '<button id="butt-print-exercise" class="btn blue">'._T('print').'</button>&nbsp;&nbsp;';
		$str .= '<button id="butt-email-exercise" class="btn blue">'._T('email').'</button></p>';
		$str .= '<p class="small"><a href="'.$strSavedLink.'">'._T('view my saved home exercise program').'</a></p>';
		$str .= '</span>';
		$str .= '</div>';
		$str .= '</div>';
		//close settings
		$str .= '</div>';
		$str .= '</div>';
		$str .= '</form>';

		return $str;

		}

	//--------------------------------------------------------------------------------------------------
	private function getSettingsBottom(&$arr){	

		//la page de save
		$strSavedLink = $this->glob->getArray('links-by-key', 'exercises-saved-'.$this->glob->get('lang_prefix'));
		//la page de l'exercise		
		$strExLink = $this->glob->getArray('links-by-key', 'exercises-details-'.$this->glob->get('lang_prefix'));
		$strExLink .= $arr['href'].DEFAULT_ID_SEPARATOR.$arr['id'].'/';
		//on va strippper le nom de domaine pour prendre moins d'espace
		$strExLink = str_replace(PATH_WEB, '/', $strExLink);

		$str = '<form id="form-exercise-settings">';
		
		$str .= '<input type="hidden" name="id" value="'.htmlSafeTag($arr['id']).'">';
		$str .= '<input type="hidden" name="na" value="'.htmlSafeTag($arr['html']).'">';
		$str .= '<input type="hidden" name="li" value="'.htmlSafeTag($strExLink).'">';
		$str .= '<input type="hidden" name="ti" value="'.htmlSafeTag(time()).'">';
		$str .= '<div class="rows ex-settings">';
		$str .= '<div class="cols">';
		$str .= '<h3>'._T('free home exercise program settings').'</h3>';
		$str .= '<p class="small">'.sprintf(_T('you can edit this form to enter exercises settings for you or your physician, Or you can go see your existing %ssaved home exercise program%s.'), '<a href="'.$strSavedLink.'">', '</a>').'</p>';
		//$str .= '<div class="table settings">';
		$str .= '<div class="rows">';
		$str .= '<div class="cols c4">';
		$str .= '<p>'._T('repetition:').'</p>';
		$str .= '<p><input type="text" name="re" value=""></p>';
		$str .= '</div>';
		$str .= '<div class="cols c4">';
		$str .= '<p>'._T('hold:').'</p>';
		$str .= '<p><input type="text" name="ho" value=""></p>';
		$str .= '</div>';
		$str .= '<div class="cols c4">';
		$str .= '<p>'._T('weight:').'</p>';
		$str .= '<p><input type="text" name="we" value=""></p>';
		$str .= '</div>';
		$str .= '</div>';
		$str .= '<div class="rows">';
		$str .= '<div class="cols c4">';
		$str .= '<p>'._T('tempo:').'</p>';
		$str .= '<p><input type="text" name="te" value=""></p>';
		$str .= '</div>';
		$str .= '<div class="cols c4">';
		$str .= '<p>'._T('rest:').'</p>';
		$str .= '<p><input type="text" name="rt" value=""></p>';
		$str .= '</div>';
		$str .= '<div class="cols c4">';
		$str .= '<p>'._T('frequency:').'</p>';
		$str .= '<p><input type="text" name="fq" value=""></p>';
		$str .= '</div>';
		$str .= '</div>';
		//$str .= '</div>';
		$str .= '<div class="rows pad pt noprint">';
		$str .= '<div class="cols">';
		$str .= '<span class="btn-group">';
		$str .= '<p><button id="butt-save-exercise" class="btn blue">'._T('save').'</button>&nbsp;&nbsp;';
		$str .= '<button id="butt-print-exercise" class="btn blue">'._T('print').'</button>&nbsp;&nbsp;';
		$str .= '<button id="butt-email-exercise" class="btn blue">'._T('email').'</button></p>';
		$str .= '</span>';
		$str .= '</div>'; //close c6
		$str .= '<div class="cols">';
		$str .= '<p><a href="'.$strSavedLink.'">'._T('view my saved home exercise program').'</a></p>';
		$str .= '</div>'; //close c6
		$str .= '</div>';
		//close settings
		$str .= '</div>';
		$str .= '</div>';
		$str .= '</form>';

		return $str;

		}

	//--------------------------------------------------------------------------------------------------
	private function getCategories(&$arr){	

		$str = '';
	
		if(count($arr)){
			$str .= '<div class="ex-categories">';
			$str .= '<h3>'._T('found in those categories').'</h3>';
			$str .= '<p class="small">'._T('this exercise can be found in those different categories and filters').'</p>';
			$str .= '<ul class="categories" itemscope="" itemtype="http://schema.org/SiteNavigationElement">';
			foreach($arr as $k=>$v){
				$strLink = $this->glob->getArray('links-by-key', 'exercises-categories-'.$this->glob->get('lang_prefix'));
				$strLink .= $v['href'].DEFAULT_ID_SEPARATOR.$v['id'].'/';	
				//le title
				$str .= '<li>';
				$str .= '<h4><a href="'.$strLink.'">'.$v['html'].'</a></h4>';
				//les filtres
				$strFilters = '';
				foreach($v['filters'] as $k2=>$v2){
					$strLinkFilter = $strLink.$v2['href'].DEFAULT_ID_SEPARATOR.$v2['id'].'/';
					$strFilters .= '<a itemprop="url" href="'.$strLinkFilter.'" title="'.htmlSafeTag(ucfirst($v2['html'])).'"><span itemprop="name">'.$v2['html'].'</span></a>, ';
					}
				if($strFilters != ''){
					$strFilters = substr($strFilters, 0, strlen($strFilters) - 2);
					$str .= _T('filters:').'&nbsp;'.$strFilters;
					}
				$str .= '</li>';
				}
			$str .= '</ul>';
			$str .= '</div>';
			}
			
		//
		return $str;
		}

	//--------------------------------------------------------------------------------------------------
	private function getCategoriesWithLi(&$arr){	

		$str = '';
	
		if(count($arr)){
			$str .= '<div class="ex-categories">';
			$str .= '<h3>'._T('found in those categories').'</h3>';
			$str .= '<p class="small">'._T('this exercise can be found in those different categories and filters').'</p>';
			$str .= '<ul class="categories2" itemscope="" itemtype="http://schema.org/SiteNavigationElement">';
			foreach($arr as $k=>$v){
				$strLink = $this->glob->getArray('links-by-key', 'exercises-categories-'.$this->glob->get('lang_prefix'));
				$strLink .= $v['href'].DEFAULT_ID_SEPARATOR.$v['id'].'/';	
				//le title
				$str .= '<li>';
				$str .= '<h4><a href="'.$strLink.'">'.$v['html'].'</a></h4>';
				$str .= '<ul class="filters">';
				//les filtres
				$strFilters = '';
				foreach($v['filters'] as $k2=>$v2){
					$strLinkFilter = $strLink.$v2['href'].DEFAULT_ID_SEPARATOR.$v2['id'].'/';
					$strFilters .= '<li><a itemprop="url" href="'.$strLinkFilter.'" title="'.htmlSafeTag(ucfirst($v2['html'])).'"><span itemprop="name">'.$v2['html'].'</span></a></li>';
					}
				$str .= $strFilters;
				$str .= '</ul>';
				$str .= '</li>';
				}
			$str .= '</ul>';
			$str .= '</div>';
			}
			
		//
		return $str;
		}
	

	//--------------------------------------------------------------------------------------------------
	private function getKeywords(&$arr){
		$str = '';
	
		if(count($arr)){
			$strLink = $this->glob->getArray('links-by-key', 'exercises-kw-'.$this->glob->get('lang_prefix'));
			$str .= '<div class="ex-keywords">';
			$str .= '<h3>'._T('tags').'</h3>';
			$str .= '<p class="small">'._T('this exercise was tag with those keywords').'</p>';
			$str .= '<span itemscope="" itemtype="http://schema.org/SiteNavigationElement">';
			$strKw = '';
			foreach($arr as $k=>$v){
				$strLinkKw = $strLink.$v['href'].DEFAULT_ID_SEPARATOR.$v['id'].'/';	
				$strKw .= '<a itemprop="url" href="'.$strLinkKw.'" title="'.htmlSafeTag(ucfirst($v['html'])).'"><span itemprop="name">'.ucfirst($v['html']).'</span></a>, ';
				}
			if($strKw != ''){
				$strKw = substr($strKw, 0, strlen($strKw) - 2);
				$str .= $strKw;
				}
			$str .= '</span>';
			$str .= '</div>';
			}
			
		//
		return $str;
		
		}

	//--------------------------------------------------------------------------------------------------
	private function getTaggedDescription(&$arr, $str){
		if(count($arr)){
			$strLink = $this->glob->getArray('links-by-key', 'exercises-kw-'.$this->glob->get('lang_prefix'));
			foreach($arr as $k=>$v){
				if($v['linked'] == '1'){
					$strRegex = '/[\s]{1}('.preg_quote($v['name']).')[\s]{1}|^('.preg_quote($v['name']).')[\s]{1}/iU';
					$strLinkKw = $strLink.$v['href'].DEFAULT_ID_SEPARATOR.$v['id'].'/';
					$strKw = '&nbsp;<a title="'.htmlSafeTag(_T('go to the keyword page with exercises containing the tag').' '.$v['name']).'" href="'.$strLinkKw.'">'.$v['name'].'</a>&nbsp;';
					$str = preg_replace($strRegex, $strKw, $str);
					}
				}
			}
			
		//
		return $str;
		
		}

	
	}
	
//END	
	
	
	
	
	