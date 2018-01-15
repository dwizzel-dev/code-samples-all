<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default home view

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
		<!-- start section-home view -->	
		<?php 
		//si section afficher
		if(isset($arrOutput['section-home']) && $arrOutput['section-home']['html'] != ''){
			echo '<div class="border-rad hideoverflow bottom-pad">';
			echo '<div class="container">';
			echo safePhpCode($arrOutput['section-home']['html']); 
			echo '</div>';
			echo '</div>';
			}
		?>
		<!-- end section-home view -->
		<!-- start module-home view -->	
		<?php	
		//si module a afficher
		if(isset($arrOutput['module-home']) && $arrOutput['module-home']['html'] != ''){
			echo '<div class="border-rad hideoverflow bottom-pad">';
			echo '<div class="container">';
			echo safePhpCode($arrOutput['module-home']['html']); 
			echo '</div>';
			echo '</div>';
			}
		?>	
		<!-- end module-home view -->
	<?php require_once(DIR_VIEWS.'bottom-frontpage.php'); ?>	
	<?php require_once(DIR_VIEWS.'footer.php'); ?>
	</div>	
	<!-- end container -->
	<?php require_once(DIR_VIEWS.'structure-append.php'); ?>
	<!-- end structured data -->
</body>
<?php require_once(DIR_VIEWS.'append.php'); ?>