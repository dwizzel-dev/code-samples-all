<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	footer views

*/
?>
<!-- start footer view -->
<footer>
	<div class="border-rad bottom-pad footer" id="footer">
		<div class="container">
			<div class="pad">
				<?php
				echo '<div class="rows">';
				//compagnie infos
				echo '<div class="cols">';
				/*
				echo '<div class="cell"><img src="'.$arrOutput['footer']['logo']['img'].'" alt="'.htmlSafeTag($arrOutput['footer']['logo']['alt']).'"></div>';
				*/
				//company infos
				if(isset($arrOutput['footer']['infos']) && $arrOutput['footer']['infos']['html'] != ''){
					echo $arrOutput['footer']['infos']['html'];
					}
				//menu footer
				echo '<div class="cols menu">';
				echo '<ul class="footer-menu">';
				if(isset($arrOutput['footer']['footer-menu']) && is_array($arrOutput['footer']['footer-menu'])){
					foreach($arrOutput['footer']['footer-menu'] as $k=>$v){
						echo '<li class="column">';
						echo '<ul class="sub-footer-menu" itemscope="" itemtype="http://schema.org/SiteNavigationElement">';
						echo '<li class="title">';
						if($v['link_id'] !== 0){
							$strMenuClass = ' class="';
							if($v['link_id'] == $oGlob->get('link_id')){
								$strMenuClass .= ' selected';
								}
							$strMenuClass .= '" ';	
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
				echo '<div class="rows">';
				//copyright
				echo '<div class="cols half">';		
				//copyright
				if($arrOutput['footer']['copyright'] != ''){
					echo '<div class="copyright">'.$arrOutput['footer']['copyright'].'</div>';
					}
				echo '</div>';	//close cols
				//social media
				echo '<div class="cols half">';		
				if(isset($arrOutput['footer']['socialmedia']['html'])){
					echo $arrOutput['footer']['socialmedia']['html']; 
					}
				echo '</div>';	//close cols
				echo '</div>';//close rows	
				?>
				</div>
			</div>
		</div>
	</div>
</footer>
<!-- end footer view -->
