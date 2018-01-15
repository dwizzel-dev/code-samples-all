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
<body <?php if(isset($arrOutput['content']['class'])){echo ' class="'.$arrOutput['content']['class'].'" ';} ?>>
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
				echo '<section itemscope itemprop="mainContentOfPage" itemtype="http://health-lifesci.schema.org/WebPageElement">';
				echo '<div class="rows pad pbt plr content-item">';
				echo '<div class="cols" itemprop="description">';
				//text check si php code inside
				echo '<h1 itemprop="name">'.ucfirst($arrOutput['content']['title']).'</h1>';
				echo '<p itemprop="description">'.$arrOutput['content']['text'].'</p>';
				//close content-full
				echo '</div>'; 
				echo '</div>'; 
				echo '</section>';
				?>
			</div>
		</div>	
		<!-- end content view -->
		<?php require_once(DIR_VIEWS.'bottom-content.php'); ?>
		<?php require_once(DIR_VIEWS.'bottom-frontpage.php'); ?>		
		<?php require_once(DIR_VIEWS.'footer.php'); ?>
	</div>
	<!-- end container -->
	<?php require_once(DIR_VIEWS.'structure-append.php'); ?>
	<!-- end structured data -->
</body>
<?php require_once(DIR_VIEWS.'append.php'); ?>