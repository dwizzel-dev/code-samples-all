<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default page view

*/
?>
<?php require_once(DIR_VIEWS.'prepend.php'); ?>
<head>
<?php require_once(DIR_VIEWS.'meta.php'); ?>
<?php require_once(DIR_VIEWS.'css.php'); ?>
<?php require_once(DIR_VIEWS.'script.php'); ?>
</head>
<body class="<?php if(isset($arrOutput['content']['class'])){echo $arrOutput['content']['class'];} ?>">
	<!-- start structured data -->
	<?php require_once(DIR_VIEWS.'structure-prepend.php'); ?>	
	<!-- start container -->
	<div class="main-container">
		<?php require_once(DIR_VIEWS.'header.php'); ?>
		<?php require_once(DIR_VIEWS.'top-frontpage.php'); ?>
		<!-- start content view -->
		<div class="rows content" id="content" <?php if(isset($arrOutput['content']['bgimage'])){echo ' style="background-image:url(\''.PATH_IMAGE.$arrOutput['content']['bgimage'].'\');" ';}?>>
			<div class="cols">
				<?php
				if(isset($arrOutput['breadcrumbs']['html'])){
					echo $arrOutput['breadcrumbs']['html'];
					}
				//le content a gauche
				echo '<div class="rows pad pbt plr content-item">';
				echo '<div class="cols c9 content-left">';
				echo '<section itemscope itemprop="mainContentOfPage" itemtype="http://health-lifesci.schema.org/WebPageElement">';
				//le contenu de la categorie, listing ou news
				if($arrOutput['content']['text'] != ''){
					echo '<span itemprop="description">'.safeReverse($arrOutput['content']['text']).'</span>';
					}
				//title
				echo '<div class="news-stats">';
				echo '<div><small>'.$arrOutput['content']['date_added'].'</small></div>';
				echo '<div><small>'._T('category:').' '.'<span itemscope="" itemtype="http://schema.org/SiteNavigationElement"><a itemprop="url" href="'.$oGlob->getArray('links',$arrOutput['content']['link_id']).'"><span itemprop="name">'.$arrOutput['content']['category_title'].'</span></a></span></small></div>';
				echo '<div><small>'._T('viewed:').' '.$arrOutput['content']['hits'].'</small></div>';
				echo '</div>';	
				//pager
				if(isset($arrOutput['pager'])){
					echo $arrOutput['pager']['html'];
					}
				//close le div content a gauche	
				echo '</div>';	
				echo '</section>';	
				//le sidebar
				//ouvre le sidebar
				echo '<div class="cols c3 sidebar">';
				if(isset($arrOutput['sidebar'])){
					//sidebar categorie
					if(isset($arrOutput['sidebar']['category'])){
						echo $arrOutput['sidebar']['category']['html'];
						}
					//sidebar latest news	
					if(isset($arrOutput['sidebar']['latest_news'])){
						echo $arrOutput['sidebar']['latest_news']['html'];
						}	
					}
				//ferme le sidebar
				echo '</div>';
						
				?>
			</div>
		</div>	
		<!-- end content view -->
		<?php require_once(DIR_VIEWS.'bottom-frontpage.php'); ?>		
		<?php require_once(DIR_VIEWS.'footer.php'); ?>
	</div>
	<!-- end container -->
	<?php require_once(DIR_VIEWS.'structure-append.php'); ?>
	<!-- end structured data -->	
</body>
<?php require_once(DIR_VIEWS.'append.php'); ?>