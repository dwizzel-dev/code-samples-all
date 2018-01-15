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
				//content
				echo '<div class="rows pad pbt plr content-item">';
				echo '<div class="cols c9" itemprop="description">';
				//title
				echo '<section itemscope itemprop="mainContentOfPage" itemtype="http://health-lifesci.schema.org/WebPageElement">';	
				echo '<h1 itemprop="name">'.ucfirst($arrOutput['content']['title']).'</h1>';
				//le contenu de la categorie
				echo '<p itemprop="description">'.safeReverse($arrOutput['content']['text']).'</p>';
				echo '</section>';
				//les categorie de news			
				if(is_array($arrOutput['content']['category'])){
					echo '<div class="news-categories-result" id="news-categories">';
					echo '<ul itemscope="" itemtype="http://schema.org/ItemList">';
					echo '<meta itemprop="name" content="'.htmlSafeTag(ucfirst($arrOutput['content']['title'])).'">';
					$iListPosition = 0;
					foreach($arrOutput['content']['category'] as $k=>$v){
						echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">';
						echo '<meta itemprop="position" content="'.($iListPosition++).'">';
						echo '<h2><a itemprop="url" href="'.$oGlob->getArray('links',$v['link_id']).'"><span itemprop="name">'.$v['title'].'</span></a></h2>';
						echo '<p itemprop="description">'.safeReverse($v['content']).'</p>';
						echo '<div class="readmore"><a href="'.$oGlob->getArray('links',$v['link_id']).'">'.sprintf(_T('go to the %s category'), $v['title']).'</a></div>';
						echo '</li>';
						}
					echo '</ul>';
					echo '</div>';
					}
				//close content-full
				echo '</div>'; 
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