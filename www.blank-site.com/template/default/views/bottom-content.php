<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	bottom content view


*/
?>
<?php 
//si carousel afficher
if(isset($arrOutput['bottom-content']) && $arrOutput['bottom-content']['html'] != ''){
?>
<!-- start bottom-content view -->
<div class="rows" id="bottom-content">
	<div class="cols">
	<?php 
	//si carousel afficher
	echo safePhpCode($arrOutput['bottom-content']['html']); 
	?>
	</div>
</div>
<!-- end bottom-content view -->
<?php 
	}
?>