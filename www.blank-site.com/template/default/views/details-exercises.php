<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default exercises details view

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
				//text check si php code inside
				//echo safePhpCode($arrOutput['content']['text']);
				//la recherche si il y a 
				if(isset($arrOutput['content']['details'])){
					//affichage du details
					echo $arrOutput['content']['details'];
					}
				//
				echo '</div>'; //close content-full
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
<script type="text/javascript">
jQuery(document).ready(function($){
	//action butt	
	$('#butt-print-exercise, #butt-share-exercise, #butt-email-exercise, #butt-save-exercise').click(function(e){
		e.preventDefault();
		DispatchButtAction($(this), 'details-exercises');
		});
	//check si avait deja un saved
	checkSavedExercise();	
	});
</script>
<?php require_once(DIR_VIEWS.'append.php'); ?>
