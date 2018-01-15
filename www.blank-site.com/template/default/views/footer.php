<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	footer views

*/
?>
<!-- start footer view -->
<footer>
	<div class="rows pad plr noprint footer" id="footer">
		<div class="rows">
			<div class="cols">
				<?php
				echo '<div class="rows">';
				//compagnie infos
				echo '<div class="cols">';
				//le logo
				echo '<div class="cell infos-inner"><a href="'.$arrOutput['footer']['logo']['href'].'"><img src="'.$arrOutput['footer']['logo']['img'].'" alt="'.htmlSafeTag($arrOutput['footer']['logo']['alt']).'"></a></div>';
				//social media footer
				echo '<div class="cell infos-socialmedia">';		
				if(isset($arrOutput['footer']['socialmedia']['html'])){
					echo $arrOutput['footer']['socialmedia']['html']; 
					}
				echo '</div>';	//close cell media
				//company infos
				if(isset($arrOutput['footer']['infos']) && $arrOutput['footer']['infos']['html'] != ''){
					echo $arrOutput['footer']['infos']['html'];
					}
				//menu footer
				echo '<div class="rows pad pbt">';
				echo '<div class="cols">';
				echo '<ul class="footer-menu">';
				if(isset($arrOutput['footer']['footer-menu']) && is_array($arrOutput['footer']['footer-menu'])){
					foreach($arrOutput['footer']['footer-menu'] as $k=>$v){
						echo '<li class="column">';
						echo '<ul class="sub-footer-menu" itemscope="" itemtype="http://schema.org/SiteNavigationElement">';
						echo '<li class="title">';
						if($v['link_id'] !== 0){
							$strMenuClass = ' class="" ';
							/*
							$strMenuClass = ' class="';
							if($v['link_id'] == $oGlob->get('link_id')){
								$strMenuClass .= ' selected';
								}
							$strMenuClass .= '" ';	
							*/
							echo '<h5><a '.$strMenuClass.' href="'.$oGlob->getArray('links',$v['link_id']).'" title="'.htmlSafeTag($v['name']).'">'.$v['name'].'</a></h5>';
						}else{
							echo '<h5>'.$v['name'].'</h5>';	
							}
						echo '</li>'; //close class cols
						if(is_array($v['child'])){
							foreach($v['child'] as $k2=>$v2){
								$strMenuClass = ' class="';
								if($v2['link_id'] == $oGlob->get('link_id')){
									$strMenuClass .= ' selected';
									}
								$strMenuClass .= '" ';	
								echo '<li class="item"><a '.$strMenuClass.' itemprop="url" href="'.$oGlob->getArray('links',$v2['link_id']).'" title="'.htmlSafeTag($v2['name']).'"><span itemprop="name">'.$v2['name'].'</span></a></li>';
								}
							}
						echo '</ul>';//close class sub-footer-menu	
						echo '</li>';
						}
					}
				echo '</ul>';//close class footer-menu
				echo '</div>';//close cols
				echo '</div>';//close rows
				//second row
				echo '<div class="rows pad pt">';
				//copyright
				//echo '<div class="cols c6">';		
				echo '<div class="cols">';		
				//copyright
				if($arrOutput['footer']['copyright'] != ''){
					echo '<div class="copyright">'.$arrOutput['footer']['copyright'].'</div>';
					}
				echo '</div>';	//close cols c6
				/*
				//social media
				echo '<div class="cols c6">';		
				if(isset($arrOutput['footer']['socialmedia']['html'])){
					echo $arrOutput['footer']['socialmedia']['html']; 
					}
				echo '</div>';	//close cols c6
				*/
				echo '</div>';//close rows	
				?>
				</div>
			</div>
		</div>
	</div>
</footer>
<!-- end footer view -->
<!-- bottom scroller -->
<div id="scroller-up" class="noprint"><img src="<?php echo $arrOutput['footer']['img-scroll-up']; ?>"></div>	
<div id="menu-up" class="noprint show"><img src="<?php echo $arrOutput['footer']['img-menu-up']; ?>"></div>	
	
