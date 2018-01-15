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
			//content
			echo '<div class="rows pad pbt plr content-item">';
			echo '<div class="cols c9 content-left">';
			//title
			echo '<section itemscope itemprop="mainContentOfPage" itemtype="http://health-lifesci.schema.org/WebPageElement">';
			echo '<h1 itemprop="name">'.ucfirst($arrOutput['content']['title']).'</h1>';
			//le contenu de la categorie, listing ou news
			if($arrOutput['content']['text'] != ''){
				echo '<p itemprop="description">'.safeReverse($arrOutput['content']['text']).'</p>';
				}
			echo '</section>';
			//le listing des news de la categorie	
			if(is_array($arrOutput['content']['listing'])){
				echo '<div class="news-listing-result" id="news-listing">';
				echo '<ul itemscope="" itemtype="http://schema.org/ItemList">';
				echo '<meta itemprop="name" content="'.htmlSafeTag(ucfirst($arrOutput['content']['title'])).'">';
				$iListPosition = 0;
				foreach($arrOutput['content']['listing'] as $k=>$v){
					echo '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">';
					echo '<meta itemprop="position" content="'.($iListPosition++).'">';
					echo '<h2><a itemprop="url" href="'.$oGlob->getArray('links',$v['link_id']).$v['alias'].'/'.$v['id'].'/"><span itemprop="name">'.$v['title'].'</span></a></h2>';
					echo '<small><p>'.$v['date_added'].'<br>'._T('viewed:').$v['hits'].'</p></small>';
					echo '<p itemprop="description">'.safeReverse($v['preview']).'</p>';
					echo '<div class="readmore"><a href="'.$oGlob->getArray('links',$v['link_id']).$v['alias'].'/'.$v['id'].'/">'._T('read more').'</div></a>';
					echo '</li>';
					}
				echo '</ul>';
				echo '</div>';
				}
			//pagination
			if(isset($arrOutput['pagination'])){
				echo $arrOutput['pagination']['html'];
				}
			//end pagination
			//close le div content a gauche	
			echo '</div>';	
			//le sidebar a droite
			echo '<div class="cols c3 sidebar">';
			if(isset($arrOutput['sidebar']['category'])){
				echo $arrOutput['sidebar']['category']['html'];
				}
			echo '</div>';	
			//end le side bar
			echo '</div>';	//end rows
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