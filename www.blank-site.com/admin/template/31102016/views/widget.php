<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default widget view

*/
?>
<?php require_once(DIR_VIEWS.'prepend.php'); ?>
<head>
<?php require_once(DIR_VIEWS.'meta.php'); ?>
<?php require_once(DIR_VIEWS.'css.php'); ?>
<?php require_once(DIR_VIEWS.'script.php'); ?>
</head>
<body class="<?php if(isset($arrOutput['content']['class'])){echo $arrOutput['content']['class'];} ?>">
<div class="row-fluid top-color"></div>
<div id="container" >
	<?php require_once(DIR_VIEWS.'header.php'); ?>
	<div class="row-fluid thick-border-t thick-border-b">
		<div class="span12">
			<h1><?php echo $arrOutput['content']['title'];?></h1>
			<?php echo $arrOutput['content']['text'];?>
		</div>
	</div>
	<div class="row-fluid" style="margin-bottom:20px;">
		<div class="span12">
			<div class="btn-group pull-left">
			  <button class="btn btn-small" id="buttdisable"><?php echo _T('disable'); ?></button>
			   <button class="btn btn-small" id="buttenable"><?php echo _T('enable'); ?></button>
			</div>
		</div>
	</div>	
	<div class="row-fluid">
		<div class="span12">
			<table class="table table-hover ">
				<thead>
					<tr>
					<?php
					//table head
					foreach($arrOutput['content']['widget']['columns'] as $k=>$v){
						echo '<th>'.ucfirst($v).'</th>';
						}
					?>
					</tr>
				</thead>
				<tbody>
					<?php
					$cbCmpt = -1;
					if($arrOutput['content']['widget']['rows']){
						foreach($arrOutput['content']['widget']['rows'] as $k=>$v){
							$cbCmpt++;
							if($v['status'] == '1'){
								echo '<tr id="tr'.$cbCmpt.'" class="">';
							}else{
								echo '<tr id="tr'.$cbCmpt.'" class="disable">';
								}
							echo '<td class="small"><input type="checkbox" id="cb'.$cbCmpt.'" value="'.$v['id'].'"></td>';
							echo '<td style="white-space:nowrap;">'.$v['alias'].'</td>';
							echo '<td>'.$v['category'].'</td>';
							echo '<td>'.$v['language'].'</td>';
							echo '<td>'.$v['id'].'</td>';
							echo '<td><a id="buttmodify'.$v['id'].'" href="#"><img src="'.PATH_IMAGE.'glyphicons/glyphicons_030_pencil.png" class="img-pencil"></a><img id="imgloading'.$v['id'].'" src="'.PATH_IMAGE.'loading.gif" class="img-loading" style="display:none;"></td>';
							echo '</tr>'.EOL;
							//script action on butt
							echo '<script type="text/javascript">'.EOL;
							echo 'jQuery(document).ready(function($){'.EOL;
							echo '	$("#buttmodify'.$v['id'].'").click(function(e){'.EOL;
							echo '		e.preventDefault();'.EOL;	
							echo '		getWidgetInfos('.$v['id'].');'.EOL;
							echo '		});'.EOL;
							echo '	});'.EOL;
							echo '</script>'.EOL;
							}
						}	
					?>
				</tbody>
			</table>
		</div>
	</div>
	<?php require_once(DIR_VIEWS.'footer.php'); ?>
</div>
<form id="form-process">
	<input type="hidden" name="cbchecked" id="cbchecked" value="">
</form>	
<!-- modal alertpopup for messages/warning -->
<div id="modal-alert" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="modal-alert-title"></h3>
	</div>
	<div class="modal-body">
		<div id="modal-alert-content"></div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true" id="modal-alert-close"></button>
	</div>
</div>
<!-- modal win for messages/warning with save options-->
<div id="modal-win" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="modal-win-title"></h3>
	</div>
	<div class="modal-body">
		<div id="modal-win-content"></div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true" id="modal-win-close"></button>
		<button class="btn btn-primary" id="modal-win-save"></button>
	</div>
</div>
<script type="text/javascript">
//
jQuery(document).ready(function($){
	//changes staus of items checked
	$('#buttdisable').click(function(e){
		e.preventDefault();
		if(getCheckedItems()){
			showButtLoadingTxt('#buttdisable', '');
			disableItem();
			}
		});	
	//changes staus of items checked
	$('#buttenable').click(function(e){
		e.preventDefault();
		if(getCheckedItems()){
			showButtLoadingTxt('#buttenable', '');
			enableItem();
			}
		});	
	});


//---------------------------------

function showButtLoadingTxt(strButt, strText){
	if(strText == ''){
		$(strButt).text('<?php echo formatJavascript(_T('wait!...')); ?>');
	}else{
		$(strButt).text(strText);
		}
	}

	
//---------------------------------

function disableItem(){
	$('#cbchecked').val(getCheckedItems());
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'widget',service:'disable-widget-infos',data:JSON.stringify($('#form-process').serializeArray())},
		success:function(data){
			//parse data
			eval('var obj = ' + data + ';');
			if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
				showButtLoadingTxt('#buttdisable', '<?php echo _T('disable'); ?>');
				$('#modal-alert-title').text('<?php echo formatJavascript(_T('error')); ?>');
				$('#modal-alert-content').html(obj.msgerrors);
				$('#modal-alert-close').text('<?php echo formatJavascript(_T('close')); ?>');
				$('#modal-alert').modal('show');
			}else{
				//pas erreur reload page
				//location.reload();
				//on hange le status directement du TR
				changeStatusTR(true);
				showButtLoadingTxt('#buttdisable', '<?php echo _T('disable'); ?>');
				}
			},
		error:function(){
			showButtLoadingTxt('#buttdisable', '<?php echo _T('disable'); ?>');
			}	
		});
	}	
	
//---------------------------------

function enableItem(){
	$('#cbchecked').val(getCheckedItems());
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'widget',service:'enable-widget-infos',data:JSON.stringify($('#form-process').serializeArray())},
		success:function(data){
			//parse data
			eval('var obj = ' + data + ';');
			if(typeof(obj.msgerrors) !== 'undefined' && obj.msgerrors){
				showButtLoadingTxt('#buttenable', '<?php echo _T('enable'); ?>');
				$('#modal-alert-title').text('<?php echo formatJavascript(_T('error')); ?>');
				$('#modal-alert-content').html(obj.msgerrors);
				$('#modal-alert-close').text('<?php echo formatJavascript(_T('close')); ?>');
				$('#modal-alert').modal('show');
			}else{
				//pas erreur reload page
				//location.reload();
				//on hange le status directement du TR
				changeStatusTR(false);
				showButtLoadingTxt('#buttenable', '<?php echo _T('enable'); ?>');
				}
			},
		error:function(){
			showButtLoadingTxt('#buttenable', '<?php echo _T('enable'); ?>');
			}	
		});
	}

	
//---------------------------------	

function changeStatusTR(bOn){
	eval('var arrTr = [' + getCheckedTR() + '];');
	if(typeof(arrTr) != 'undefined'){
		for(var o in arrTr){
			if(bOn){
				//tr
				$('#tr' + arrTr[o]).addClass('disable');
			}else{
				//tr
				$('#tr' + arrTr[o]).removeClass('disable');
				}
			//check box
			$('#cb' + arrTr[o]).attr('checked', false);
			}
		}
	}	

//---------------------------------	

function getCheckedTR(){
	var s = '';
	for(var i=0;i<=<?php echo $cbCmpt; ?>;i++){
		if($('#cb'+ i) && $('#cb' + i).is(':checked')){
			s += i + ',';
			}
		}
	if(s != ''){
		s = s.substring(0,(s.length - 1));
		return s;
		}
	return false;
	}	
	
//---------------------------------	

function getCheckedItems(){
	var s = '';
	for(var i=0;i<=<?php echo $cbCmpt; ?>;i++){
		if($('#cb'+ i) && $('#cb' + i).is(':checked')){
			s += $('#cb' + i).val() + ',';
			}
		}
	if(s != ''){
		s = s.substring(0,(s.length - 1));
		return s;
		}
	return false;
	}

	
//---------------------------------

function getWidgetInfos(strid){
	showPencilLoadingImg(strid, true);
	window.location.href = '<?php echo $oGlob->getArray('links','widget-items')?>item-' + strid + '/';
	
	}	

//---------------------------------

function showPencilLoadingImg(strid, status){
	if(status){
		$('#buttmodify' + strid).hide();
		$('#imgloading' + strid).show();
	}else{
		$('#buttmodify' + strid).show();
		$('#imgloading' + strid).hide();
		}
	}	


</script>
</body>
<?php require_once(DIR_VIEWS.'append.php'); ?>