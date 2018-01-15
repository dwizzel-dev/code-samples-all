<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	header view


*/
?>
<!-- start header view -->
<header itemscope itemtype="http://schema.org/WPHeader">
	<!-- meta header -->
	<meta itemprop="headline" content="<?php echo htmlSafeTag($arrOutput['meta']['title']); ?>">
	<meta itemprop="description" content="<?php echo htmlSafeTag($arrOutput['meta']['description']); ?>">
	<!-- header -->
	<div class="rows pad plr header" id="header">
		<div class="cols">
			<div class="rows">
				<?php
				//RESPONSIVE
				//menu responsive
				echo '<div class="cell top-menu-mobile noprint show" id="top-menu-mobile"><img src="'.$arrOutput['header']['mobile']['img-menu'].'" alt="" title=""></div>';
				echo '<div class="cell right logo show"><a href="'.$arrOutput['header']['href-logo'].'"><img src="'.$arrOutput['header']['mobile']['img-logo'].'" alt="'.htmlSafeTag($arrOutput['header']['alt-logo']).'" title="'.htmlSafeTag($arrOutput['header']['alt-logo']).'"></a></div>';
				//le menu par dessus pour le mobile
				echo '<div class="menu-mobile noprint" id="menu-mobile">';
				echo '<div class="rows inner-menu">';
				echo '<div class="cols">';
				//menu
				if(isset($arrOutput['header']['mobile']['top-menu']) && is_array($arrOutput['header']['mobile']['top-menu'])){
					foreach($arrOutput['header']['mobile']['top-menu'] as $k=>$v){
						echo '<ul>';
						//check si le title a un link
						if($v['link_id']){
							$strMenuClass = ' class="';
							if($v['link_id'] == $oGlob->get('link_id')){
								$strMenuClass .= ' selected';
								}
							$strMenuClass .= '" ';	
							echo '<p class="title"><a '.$strMenuClass.' href="'.$oGlob->getArray('links',$v['link_id']).'">'.$v['name'].'</a></p>';
						}else{
							echo '<p class="title">'.$v['name'].'</p>';
							}
						//sub menu	
						if(is_array($v['child'])){
							foreach($v['child'] as $k2=>$v2){
								//le lien
								$strHrefLink = $oGlob->getArray('links', $v2['link_id']);
								//si fait partidu lien alors c'est un parent
								$strMenuClass = ' class="';
								if(stripos($oGlob->get('current_url'), $strHrefLink) !== false){
									$strMenuClass .= ' selected';
									}
								$strMenuClass .= '" ';
								echo '<li class="links"><a '.$strMenuClass.' href="'.$oGlob->getArray('links',$v2['link_id']).'">'.$v2['name'].'</a></li>';
								}
							}
						echo '</ul>';//close class sub-footer-menu	
						}
					}
				echo '<div class="rows slogan-mobile"><p>'.$arrOutput['header']['slogan'].'<br />'._T('company slogan from').'</p></div>';	
				echo '</div>';
				echo '</div>';
				echo '</div>'; //END menu-mobile RESPONSIVE
								
				
				//affichage du logo
				echo '<div class="logo noprint hide"><a href="'.$arrOutput['header']['href-logo'].'"><img src="'.$arrOutput['header']['img-logo'].'" alt="'.htmlSafeTag($arrOutput['header']['alt-logo']).'" title="'.htmlSafeTag($arrOutput['header']['alt-logo']).'"></a></div>';
				//affichage du menu de login
				if(isset($arrOutput['header']['menu-login']) && $arrOutput['header']['menu-login'] != ''){
					echo '<div class="cell right noprint hide">';	
					echo '<ul class="menu-login">';
					foreach($arrOutput['header']['menu-login'] as $k=>$v){
						echo '<li><a href="'.$oGlob->getArray('links', $v['link_id']).'" title="'.htmlSafeTag($v['name']).'">'.$v['name'].'</a></li>';
						}
					echo '</ul>';
					echo '</div>';	
					}
				//affichage du menu de langue
				if(isset($arrOutput['header']['menu-lang']) && $arrOutput['header']['menu-lang'] != ''){
					echo '<div class="cell right noprint hide ">';	
					echo '<ul class="menu-lang" itemscope itemtype="http://schema.org/SiteNavigationElement">';
					foreach($arrOutput['header']['menu-lang'] as $k=>$v){
						echo '<li><a itemprop="url" href="'.$oGlob->getArray('links', $v['link_id']).'" title="'.htmlSafeTag($v['name']).'"><span itemprop="name">'.$v['name'].'</span></a></li>';
						}
					echo '</ul>';
					echo '</div>';
					}
				//affichage du menu contact
				if(isset($arrOutput['header']['menu-contact']) && $arrOutput['header']['menu-contact'] != ''){
					echo '<div class="cell right noprint hide">';	
					echo '<ul class="menu-contact" itemscope itemtype="http://schema.org/SiteNavigationElement">';
					foreach($arrOutput['header']['menu-contact'] as $k=>$v){
						echo '<li><a itemprop="url" href="'.$oGlob->getArray('links', $v['link_id']).'" title="'.htmlSafeTag($v['name']).'"><span itemprop="name">'.$v['name'].'</span></a></li>';
						}
					echo '</ul>';
					echo '</div>';
					}
				//slogan
				echo '<div class="cell right top-slogan hide"><p>'.$arrOutput['header']['slogan'].'</p><p>'._T('company slogan from').'</p></div>';		
				//affichage du menu top
				if(isset($arrOutput['header']['top-menu-1']) && $arrOutput['header']['top-menu-1'] != ''){
					$iTopMenuDivider = 100/count($arrOutput['header']['top-menu-1']);
					//standard
					echo '<nav>';
					echo '<div class="top-menu noprint hide">';
					echo '<ul class="menu" itemscope itemtype="http://schema.org/SiteNavigationElement">';
					foreach($arrOutput['header']['top-menu-1'] as $k=>$v){
						//le lien
						$strHrefLink = $oGlob->getArray('links', $v['link_id']);
						//si fait partidu lien alors c'est un parent
						$strMenuClass = ' class="';
						if(stripos($oGlob->get('current_url'), $strHrefLink) !== false){
							$strMenuClass .= ' selected';
							}
						$strMenuClass .= '" ';	
						
						//echo '<li style="width:'.$iTopMenuDivider.'%"><a '.$strMenuClass.' itemprop="url" href="'.$strHrefLink.'" title="'.htmlSafeTag($v['name']).'"><span itemprop="name">'.$v['name'].'</span></a></li>';
						echo '<li><a '.$strMenuClass.' itemprop="url" href="'.$strHrefLink.'" title="'.htmlSafeTag($v['name']).'"><span itemprop="name">'.$v['name'].'</span></a></li>';
						}
					echo '</ul>';
					echo '</div>';
					echo '</nav>';
					}
				?>
			</div>	
		</div>
	</div>
</header>
<!-- end header view -->
