<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	top frontpage view


*/
?>
<?php 
//si carousel afficher
if(isset($arrOutput['top-frontpage']) && $arrOutput['top-frontpage']['html'] != ''){
?>
<!-- start top-frontpage view -->
<div class="rows" id="top-frontpage">
	<div class="cols">
	<?php 
	//si carousel afficher
	echo safePhpCode($arrOutput['top-frontpage']['html']); 
	?>
	</div>
</div>
<!-- end top-frontpage view -->
<?php 
	}
?>