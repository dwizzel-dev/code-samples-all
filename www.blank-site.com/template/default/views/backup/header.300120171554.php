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
	<div class="rows pad pt plr header" id="header">
		<div class="cols">
			<div class="rows">
				<div class="cols c3">
				<?php
				//affichage du logo
				echo '<div class="logo"><a href="'.$arrOutput['header']['href-logo'].'"><img src="'.$arrOutput['header']['img-logo'].'" alt="'.htmlSafeTag($arrOutput['header']['alt-logo']).'" title="'.htmlSafeTag($arrOutput['header']['alt-logo']).'"></a></div>';
				?>
				</div>	
				<div class="cols c9">
				<?php
				//affichage du menu de login
				if(isset($arrOutput['header']['menu-login']) && $arrOutput['header']['menu-login'] != ''){
					echo '<div class="cell right">';	
					echo '<ul class="menu-login">';
					foreach($arrOutput['header']['menu-login'] as $k=>$v){
						echo '<li><a href="'.$oGlob->getArray('links', $v['link_id']).'" title="'.htmlSafeTag($v['name']).'">'.$v['name'].'</a></li>';
						}
					echo '</ul>';
					echo '</div>';	
					}
				//affichage du menu de langue
				if(isset($arrOutput['header']['menu-lang']) && $arrOutput['header']['menu-lang'] != ''){
					echo '<div class="cell right">';	
					echo '<ul class="menu-lang" itemscope itemtype="http://schema.org/SiteNavigationElement">';
					foreach($arrOutput['header']['menu-lang'] as $k=>$v){
						echo '<li ';
						if($v['menu_type'] == '1'){
							echo ' class="title" '; 	
							}
						echo '><a itemprop="url" href="'.$oGlob->getArray('links', $v['link_id']).'" title="'.htmlSafeTag($v['name']).'"><span itemprop="name">'.$v['name'].'</span></a></li>';
						}
					echo '</ul>';
					echo '</div>';
					}
				//slogan
				echo '<div class="cell right top-slogan"><p>'._T('company slogan text').'</p><p>'._T('company slogan from').'</p></div>';	
				?>
				</div>
			</div>	
			<div class="rows pad pbt1">	
				<div class="cols c12">
				<?php
				//affichage du menu top
				if(isset($arrOutput['header']['top-menu-1']) && $arrOutput['header']['top-menu-1'] != ''){
					//responsive
					/*
					
					
					*/
					//standard
					echo '<div class="cell top-menu">';
					echo '<nav>';
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
						
						echo '<li><a '.$strMenuClass.' itemprop="url" href="'.$strHrefLink.'" title="'.htmlSafeTag($v['name']).'"><span itemprop="name">'.$v['name'].'</span></a></li>';
						}
					echo '</ul>';
					echo '</nav>';
					echo '</div>';
					}
				?>
				</div>
			</div>
		</div>
	</div>
</header>
<!-- end header view -->
