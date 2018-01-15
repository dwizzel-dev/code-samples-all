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
				echo '<div class="cols">';
				echo '<div class="rows">';
				echo '<div class="cols">';
				//text check si php code inside
				echo safePhpCode($arrOutput['content']['text']);
				echo '</div>'; 
				echo '</div>';
				//le container des exercise qui sera builder en javascript
				echo '<div class="rows pad pt">';
				echo '<div class="cols c9">';
				echo '<h3>'.$arrOutput['content']['listing']['title'].'</h3>';
				echo '</div>'; 
				echo '</div>';
				echo '<div class="rows">';
				echo '<div class="cols c9">';
				echo '<p>'.$arrOutput['content']['listing']['message'].'</p>';
				echo '</div>'; 
				echo '</div>';
				echo '<div class="rows">';
				echo '<div class="cols c9">';
				echo '<div class="saved-exercises-listing"></div>';
				echo '</div>'; 
				echo '</div>';
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
<script>
jQuery(document).ready(function($){
	checkSavedExercises();
	});
</script>
<?php require_once(DIR_VIEWS.'append.php'); ?>