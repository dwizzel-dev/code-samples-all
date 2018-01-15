<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	bottom frontpage view


*/
?>
<?php 

//si carousel afficher
if(isset($arrOutput['bottom-frontpage']) && $arrOutput['bottom-frontpage']['html'] != ''){
?>
<!-- start bottom-frontpage view -->
<div class="rows" id="bottom-frontpage">
	<div class="cols">
	<?php 
	//si carousel afficher
	echo safePhpCode($arrOutput['bottom-frontpage']['html']); 
	?>
	</div>
</div>
<!-- end bottom-frontpage view -->
<?php 
	}
?>