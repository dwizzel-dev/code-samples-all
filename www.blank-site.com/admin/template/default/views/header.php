<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	header view


*/

?>
<div class="row-fluid" id="header">
	<div class="span12 navbar">
		<div class="navbar-inner">
			<div class="container">
				<button class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
				<?php
				//logo image or text site name
				echo '<a href="'.$arrOutput['header']['logo']['link'].'" alt="'.quoteHtml($arrOutput['header']['logo']['alt']).'">';
				if($arrOutput['header']['logo']['image'] != ''){
					echo '<div id="logo"><img src="'.$arrOutput['header']['logo']['image'].'" alt="'.quoteHtml($arrOutput['header']['logo']['alt']).'"></div>';
				}else{
					echo '<div id="logo"><h1>'.$arrOutput['header']['logo']['alt'].'</h1></div>';
					}
				echo '</a>';
				?>
				<div class="nav-collapse collapse">
					<ul class="nav pull-right">
						<?php
						//oothers from class/menu.php
						$iCmptCols = 0;
						foreach($arrOutput['header']['menu'] as $k=>$v){
							$iCmptCols++;
							if(!$v['child']){
								echo '<li><a href="'.$oGlob->getArray('links',$v['link_id']).'">'.ucfirst($v['name']).'</a></li>'.EOL;
								if(count($arrOutput['header']['menu']) != $iCmptCols){
									echo ' <li class="divider-vertical"></li>'.EOL;
									}
							}else{
								echo '<li class="dropdown">'.EOL;
								echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.ucfirst($v['name']).' <b class="caret"></b></a>'.EOL;
								echo '<ul class="dropdown-menu">'.EOL;
								$iCmptRows = 0;
								foreach($v['child'] as $k2=>$v2){
									$iCmptRows++;
									if($v2['menu_type'] == '1'){
										echo '<li class="nav-header">'.ucfirst($v2['name']);
										if($v2['description'] != ''){
											echo '<br><small>'.ucfirst($v2['description']).'</small>';
											}
										echo '</li>';
										
									}else{
										echo '<li><a href="'.$oGlob->getArray('links',$v2['link_id']).'">'.ucfirst($v2['name']);
										if($v2['description'] != ''){
											echo '<br><small>'.ucfirst($v2['description']).'</small>';
											}
										echo '</a></li>';
										if($iCmptRows < count($v['child'])){
											echo '<li class="divider"></li>';
											}
										}
									}
								echo '</ul>'.EOL;
								echo '</li>'.EOL;
								//echo ' <li class="divider-vertical"></li>'.EOL;
								}
							}
						
												
						//lang choices
						if(isset($arrOutput['header']['languages']) && is_array($arrOutput['header']['languages'])){
							echo ' <li class="divider-vertical"></li>'.EOL;
							echo '<li class="dropdown">'.EOL;
							echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.ucfirst(_T('menu lang choice')).' <b class="caret"></b></a>'.EOL;
							echo '<ul class="dropdown-menu">'.EOL;
							$iCmptRows = 0;
							foreach($arrOutput['header']['languages'] as $k=>$v){
								$iCmptRows++;
								echo '<li><a href="'.$v['link'].'">'.ucfirst($v['name']).EOL;
								if($v['description'] != ''){
									echo '<br><small>'.ucfirst($v['description']).'</small>';
									}
								echo '</a></li>';
								if($iCmptRows < count($arrOutput['header']['languages'])){
									echo '<li class="divider"></li>';
									}
								}
							echo '</ul>'.EOL;
							echo '</li>'.EOL;
							}	
						?>
					</ul>
				</div><!-- /.nav-collapse -->
			</div>
		</div><!-- /navbar-inner -->
	</div><!-- /navbar -->
</div>