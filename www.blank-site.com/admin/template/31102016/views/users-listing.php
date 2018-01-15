<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default page view users

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
	<div class="row-fluid hide" id="box-alert">
		<div class="span12 inline">
			<div class="alert alert-error">
				<h4><?php echo _T('errors'); ?></h4>
				<p id="box-alert-msg"></p>
			</div>
		</div>
	</div>	
	<div class="row-fluid hide" id="box-success">
		<div class="span12 inline">
			<div class="alert alert-success">
				<h4><?php echo _T('success'); ?></h4>
				<p id="box-success-msg"></p>
			</div>
		</div>
	</div>	
	<div class="row-fluid thick-border-b" style="margin-bottom:20px;">
		<div class="span12 inline">
			<div class="pull-left">
				<select id="filter-display">
				<?php
				echo '<option value="">'._T('display filter').'</option>'.EOL;	
				foreach($arrOutput['content']['display-dropdown'] as $k=>$v){
					echo '<option value="'.$v['id'].'" ';
					if(isset($arrOutput['content']['display-dropdown-selected']) && $arrOutput['content']['display-dropdown-selected'] == $v['id']){
						echo ' selected ';
						}
					echo '>'.$v['text'].'</option>'.EOL;
					}
				?>
				</select>
				<select id="filter-sortby">
				<?php
				echo '<option value="">'._T('sort by').'</option>'.EOL;	
				foreach($arrOutput['content']['sortby-dropdown'] as $k=>$v){
					echo '<option value="'.$v['id'].'" ';
					if(isset($arrOutput['content']['sortby-dropdown-selected']) && $arrOutput['content']['sortby-dropdown-selected'] == $v['id']){
						echo ' selected ';
						}
					echo '>'.$v['text'].'</option>'.EOL;
					}
				?>
				</select>
				<div class="input-append">
					<input class="span6" id="filter-searchitems" type="text"
					<?php
					if(isset($arrOutput['filter']['searchitems-id']) && $arrOutput['filter']['searchitems-id'] != ''){
						echo ' value="'.$arrOutput['filter']['searchitems-id'].'" ';
						}
					?>
					>
					<button class="btn" type="button" id="buttsearchitems"><i class="icon-search"></i></button>
					<button class="btn" type="button" id="buttsearchclear"><?php echo _T('clear'); ?></button>
				</div>
			</div>
			<div class="btn-group pull-right">
				<button class="btn btn-warning" id="butt-import"><?php echo _T('export csv email'); ?></button>
			</div>
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
					foreach($arrOutput['content']['users']['columns'] as $k=>$v){
						echo '<th>'.ucfirst($v).'</th>';
						}
					?>
					</tr>
				</thead>
				<tbody>
					<?php
					$cbCmpt = -1;
					if(is_array($arrOutput['content']['users']['rows'])){
						foreach($arrOutput['content']['users']['rows'] as $k=>$v){
							$cbCmpt++;
							if($v['status'] == '1'){
								echo '<tr id="tr'.$cbCmpt.'" class="">';
							}else{
								echo '<tr id="tr'.$cbCmpt.'" class="disable">';
								}
							echo '<td class="small"><input type="checkbox" id="cb'.$cbCmpt.'" value="'.$v['id'].'"></td>';
							echo '<td style="white-space:nowrap;">'.strtolower($v['username']).'</td>';
							echo '<td>'.mb_strtolower($v['name'], 'UTF-8').'</td>';
							echo '<td>'.$v['date_added'].'</td>';
							echo '<td>'.$v['id'].'</td>';
							echo '<td><a id="buttmodify'.$v['id'].'" href="#"><img src="'.PATH_IMAGE.'glyphicons/glyphicons_030_pencil.png" class="img-pencil"></a><img id="imgloading'.$v['id'].'" src="'.PATH_IMAGE.'loading.gif" style="display:none;" class="img-loading"></td>';
							echo '</tr>'.EOL;
							//script action on butt
							echo '<script type="text/javascript">'.EOL;
							echo 'jQuery(document).ready(function($){'.EOL;
							echo '	$("#buttmodify'.$v['id'].'").click(function(e){'.EOL;
							echo '		e.preventDefault();'.EOL;	
							echo '		getUsersInfos('.$v['id'].');'.EOL;
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
	<?php
	//pagination
	if(isset($arrOutput['pagination'])){
		echo '<div class="row-fluid">';
		echo '<div class="span12">';
		echo $arrOutput['pagination']['html'];
		echo '</div>';
		echo '</div>';
		}
	?>
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
	$('#filter-display').change(function(){
		window.location.href = '<?php echo $oGlob->getArray('links','users-listing')?>page-0/display-' + $(this).attr('value') + '/';
		});	
	$('#filter-sortby').change(function(){
		window.location.href = '<?php echo $oGlob->getArray('links','users-listing')?>page-0/sortby-' + $(this).attr('value') + '/';
		});
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
	//search box
	$('#buttsearchitems').click(function(e){
		e.preventDefault();
		if($('#filter-searchitems').attr('value') != ''){
			window.location.href = '<?php echo $oGlob->getArray('links','users-listing')?>page-0/searchitems-' + $('#filter-searchitems').attr('value') + '/';
		}else{
			window.location.href = '<?php echo $oGlob->getArray('links','users-listing')?>page-0/searchitems-0/';
			}
		});	
	//clear serach
	$('#buttsearchclear').click(function(e){
		e.preventDefault();
		window.location.href = '<?php echo $oGlob->getArray('links','users-listing')?>page-0/searchitems-0/';
		});
	//import csv file	
	$('#butt-import').click(function(e){
		e.preventDefault();
		exportCsvFile();
		showButtLoadingTxt('#butt-import', '');
		});	
	});
	
//---------------------------------

function exportCsvFile(){
	//hide les erreurs precedente
	resetForm();
	//send
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'utils',service:'import-users-email-csv-file',data:1},
		success:function(data){
			//alert(data);
			//parse data
			eval('var obj = ' + data + ';');
			if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
				showButtLoadingTxt('#butt-import', '<?php echo _T('export csv email'); ?>');
				$('#box-alert-msg').html(obj.msgerrors);
				$('#box-alert').show();
				$(window).scrollTo(150, 500);
			}else{
				//pas erreur reload page
				//successAlert(1);
				showButtLoadingTxt('#butt-import', '<?php echo _T('export csv email'); ?>');
				//le lien
				if(typeof(obj.msgdata) !== "undefined" && obj.msgdata){
					$('#modal-alert-title').text('<?php echo formatJavascript(_T('csv file')); ?>');
					$('#modal-alert-content').html('<b>csv file: </b><a href="' + obj.msgdata + '">' + obj.msgdata + '</a>');
					$('#modal-alert-close').text('<?php echo formatJavascript(_T('close')); ?>');
					$('#modal-alert').modal('show');
					}
				}
			},
		error:function(){
			showButtLoadingTxt('#butt-import', '<?php echo _T('export csv email'); ?>');
			}		
		});
	}	

	
//---------------------------------

function showButtLoadingTxt(strButt, strText){
	if(strText == ''){
		$(strButt).text('<?php echo formatJavascript(_T('wait!...')); ?>');
	}else{
		$(strButt).text(strText);
		}
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

function disableItem(){
	$('#cbchecked').val(getCheckedItems());
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'users',service:'disable-users-infos',data:JSON.stringify($('#form-process').serializeArray())},
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
		data: {section:'users',service:'enable-users-infos',data:JSON.stringify($('#form-process').serializeArray())},
		success:function(data){
			//parse data
			eval('var obj = ' + data + ';');
			if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
				showButtLoadingTxt('#buttenable', '<?php echo _T('enable'); ?>');
				$('#modal-alert-title').text('<?php echo formatJavascript(_T('error')); ?>');
				$('#modal-alert-content').html(obj.msgerrors);
				$('#modal-alert-close').text('<?php echo formatJavascript(_T('close')); ?>');
				$('#modal-alert').modal('show');
			}else{
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
	
function resetForm(){
	$('#box-alert').hide();
	$('#box-alert-msg').text();
	}
	
	
//---------------------------------

function successAlert(bOn){
	if(bOn){
		$('#box-success-msg').html('<?php echo _T('modifications are succesfull');?>');
		$('#box-success').show();
		$(window).scrollTo('#page-top', 500);
	}else{
		$('#box-success').hide();
		$('#box-sucess-msg').text();
		}
	}	
	
//--------------------------------
	
function getUsersInfos(strid){
	window.location.href = '<?php echo $oGlob->getArray('links','users-items')?>item-' + strid + '/';
	}	
	
</script>
</body>
<?php require_once(DIR_VIEWS.'append.php'); ?>