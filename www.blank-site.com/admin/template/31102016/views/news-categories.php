<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default news/categories view

*/

function recursiveBuildTable($v, $level, &$cbCmpt){
	global $oGlob;
	$cbCmpt++;
	if($v['status'] == '1'){
		echo '<tr id="tr'.$cbCmpt.'" class="">';
	}else{
		echo '<tr id="tr'.$cbCmpt.'" class="disable">';
		}
	if($v['access'] == '1'){
		echo '<td class="small"><input type="checkbox" id="cb'.$cbCmpt.'" value="'.$v['id'].'"></td>';
	}else{
		echo '<td class="small">&nbsp;</td>';
		}
	echo '<td><span style="white-space:nowrap;margin-right:20px;">';
	if($level != 0){
		echo '<img src="'.PATH_IMAGE.'menu_tree_level.png" class="menu-level-'.$level.'">';
		}
	echo '<a href="'.$oGlob->getArray('links','news-listing').'page-0/category-'.$v['id'].'/">'.$v['name'].'</a>';
	//echo $v['name'];
	echo '</span></td>';
	echo '<td>'.$v['title'].'</td>';
	
	if(SIMPLIFIED_SITE_PATH){
		echo '<td><a href="/'.$v['language'].'/'.$v['path'].'/" target="_new">/'.$v['language'].'/'.$v['path'].'/</a></td>';
	}else{
		$arrLangCodeByPrefix = $oGlob->get('languages_code_by_prefix');
		echo '<td><a href="'.PATH_WEB_SITE.'index.php?&lang='.$arrLangCodeByPrefix[$v['language']].'&path='.$v['path'].'/" target="_new">'.PATH_WEB_SITE.'index.php?&lang='.$arrLangCodeByPrefix[$v['language']].'&path='.$v['path'].'/</a></td>';
		}
	
	
	echo '<td>'.$v['position'].'</td>';
	echo '<td>'.$v['id'].'</td>';
	if($v['access'] == '1'){
		echo '<td><a id="buttmodify'.$v['id'].'" href="#"><img src="'.PATH_IMAGE.'glyphicons/glyphicons_030_pencil.png" class="img-pencil"></a><img id="imgloading'.$v['id'].'" src="'.PATH_IMAGE.'loading.gif" style="display:none;" class="img-loading"></td>';
		echo '</tr>'.EOL;
		//script action on butt
		echo '<script type="text/javascript">'.EOL;
		echo 'jQuery(document).ready(function($){'.EOL;
		echo '	$("#buttmodify'.$v['id'].'").click(function(e){'.EOL;
		echo '		e.preventDefault();'.EOL;	
		echo '		getContentCategoryInfos('.$v['id'].');'.EOL;
		echo '		});'.EOL;
		echo '	});'.EOL;
		echo '</script>'.EOL;
	}else{
		echo '<td>&nbsp;</td>';
		echo '</tr>'.EOL;
		}
	if(is_array($v['child'])){
		$level++;
		foreach($v['child'] as $k2=>$v2){
			recursiveBuildTable($v2, $level, $cbCmpt);
			}
		}
	}



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
			  <button class="btn btn-small" id="buttdelete"><?php echo _T('delete'); ?></button>
			  <button class="btn btn-small" id="buttdisable"><?php echo _T('disable'); ?></button>
			   <button class="btn btn-small" id="buttenable"><?php echo _T('enable'); ?></button>
			</div>
			<div class="top-butt-create">
			  <button class="btn btn-primary" id="buttcreate"><?php echo _T('create new'); ?></button>
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
					foreach($arrOutput['content']['category']['columns'] as $k=>$v){
						echo '<th>'.ucfirst($v).'</th>';
						}
					?>
					</tr>
				</thead>
				<tbody>
					<?php
					if($arrOutput['content']['category']['rows']){
						foreach($arrOutput['content']['category']['rows'] as $k=>$v){
							recursiveBuildTable($v, 0, $arrOutput['content']['category']['cmpt-row']);
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
<!-- modal win for modifying form -->
<div id="modal-modify-items" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="modal-modify-items-title"></h3>
	</div>
	<div class="modal-body" id="modal-modify-items-body">
		<div id="modal-modify-items-content">
			<div class="alert alert-error hide" id="modal-modify-items-alert">
				<h4><?php echo _T('errors'); ?></h4>
				<p id="modal-modify-items-alert-msg"></p>
			</div>
			<form id="form-modify">
				<input type="hidden" name="id" id="modal-modify-input-id" value="">
				<input type="hidden" name="link_id" id="modal-modify-input-link_id" value="">
				<label class="checkbox"  style="margin-bottom:20px;">
				<input type="checkbox" id="modal-modify-input-status" name="status"> <?php echo _T('active'); ?>
				</label>
				<label><?php echo _T('name'); ?> :</label>
				<input type="text" name="name" id="modal-modify-input-name" class="w96">
				<label><?php echo _T('position in list'); ?> :</label>
				<input type="text" name="position" id="modal-modify-input-position" class="w20">
				<label><?php echo _T('meta title and page title'); ?> :</label>
				<textarea rows="3" name="meta_title" id="modal-modify-input-meta_title" class="w96"></textarea>
				<label><?php echo _T('page content'); ?> :</label>
				<textarea rows="3" name="content" id="modal-modify-input-content" class="w96"></textarea>
				<label><?php echo _T('meta description'); ?> :</label>
				<textarea rows="3" name="meta_description" id="modal-modify-input-meta_description" class="w96"></textarea>
				<label><?php echo _T('meta keywords'); ?> :</label>
				<textarea rows="3" name="meta_keywords" id="modal-modify-input-meta_keywords" class="w96"></textarea>
				<label><?php echo _T('parent category'); ?> :</label>
				<select name="parent_id" id="modal-modify-input-parent_id" class="input-xlarge">
				<?php
				foreach($arrOutput['content']['news-category-dropdown'] as $k=>$v){
					echo '<option value="'.$v['id'].'">'.$v['text'].'</option>'.EOL;
					}
				?>
				</select>
			</form>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"  id="modal-modify-items-close"></button>
		<button class="btn btn-primary" id="modal-modify-items-save"></button>
	</div>
</div>
<script type="text/javascript">
//
jQuery(document).ready(function($){
	//create new items
	$('#buttcreate').click(function(e){
		e.preventDefault();
		$('#modal-modify-items-title').text('<?php echo formatJavascript(_T('new news category')); ?>');
		$('#modal-modify-items-close').text('<?php echo formatJavascript(_T('close')); ?>');
		$('#modal-modify-items-save').text('<?php echo formatJavascript(_T('save changes')); ?>');
		$('#modal-modify-items-save').unbind('click');	
		$('#modal-modify-items-save').click(function(e){
			e.preventDefault();
			setContentCategoryInfos(true);
			});
		$('#modal-modify-items').modal('show');
		resetForm(true);
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
	//delete items
	$('#buttdelete').click(function(e){
		e.preventDefault();
		if(getCheckedItems()){
			$('#modal-win-title').text('<?php echo formatJavascript(_T('warning')); ?>');
			$('#modal-win-content').html('<?php echo formatJavascript(_T('all selected <b>items and subtree items</b> will be <b>deleted</b>, are you sure?')); ?>');
			$('#modal-win-close').text('<?php echo formatJavascript(_T('close')); ?>');
			$('#modal-win-save').text('<?php echo formatJavascript(_T('save changes')); ?>');
			$('#modal-win').modal('show');
			$('#modal-win-save').unbind('click');	
			$('#modal-win-save').click(function(e){
				e.preventDefault();
				showButtLoadingTxt('#buttdelete', '');
				$('#modal-win').modal('hide');
				deleteItem();
				});
			}
		});	
	});
	
//---------------------------------

function resetForm(bText){
	$('#modal-modify-items-alert').hide();
	$('#modal-modify-items-alert-msg').text();
	var arr = [['status', 'checkbox'], ['content', 'text'], ['name', 'val'], ['parent_id', 'val'], ['alias', 'val'], ['meta_title', 'text'], ['meta_description', 'text'], ['meta_keywords', 'text']];
	for(var o in arr){
		if(bText){
			if(arr[o][1] == 'text'){
				$('#modal-modify-input-' + arr[o][0]).val('');
			}else if(arr[o][1] == 'val'){
				$('#modal-modify-input-' + arr[o][0]).val('');
			}else if(arr[o][1] == 'checkbox'){
				$('#modal-modify-input-' + arr[o][0]).attr('checked', false);
				}
			}
		//alert('reset: ' + arr[o][0]);	
		$('#modal-modify-input-' + arr[o][0]).css('border-color', '');
		}
	$('#modal-modify-items-body').scrollTo(0, 500);
	return true;	
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

function deleteItem(){
	$('#cbchecked').val(getCheckedItems());
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'news',service:'delete-category-infos',data:JSON.stringify($('#form-process').serializeArray())},
		success:function(data){
			//parse data
			eval('var obj = ' + data + ';');
			if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
				showButtLoadingTxt('#buttdelete', '<?php echo _T('delete'); ?>');
				$('#modal-alert-title').text('<?php echo formatJavascript(_T('error')); ?>');
				$('#modal-alert-content').html(obj.msgerrors);
				$('#modal-alert-close').text('<?php echo formatJavascript(_T('close')); ?>');
				$('#modal-alert').modal('show');
			}else{
				//pas erreur reload page
				//location.reload();
				removeTR();
				showButtLoadingTxt('#buttdelete', '<?php echo _T('delete'); ?>');
				}
			},
		error:function(){
			showButtLoadingTxt('#buttdelete', '<?php echo _T('delete'); ?>');
			}		
		});
	}		
	
//---------------------------------

function disableItem(){
	$('#cbchecked').val(getCheckedItems());
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'news',service:'disable-category-infos',data:JSON.stringify($('#form-process').serializeArray())},
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
		data: {section:'news',service:'enable-category-infos',data:JSON.stringify($('#form-process').serializeArray())},
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

function getCheckedItems(){
	var s = '';
	for(var i=0;i<=<?php echo $arrOutput['content']['category']['cmpt-row']; ?>;i++){
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

function setContentCategoryInfos(bNew){
	resetForm(false);
	var strService = 'set-category-infos';
	if(bNew){
		strService = 'new-category-infos';
		}
	showButtLoadingTxt('#modal-modify-items-save', '');
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'news',service:strService,data:JSON.stringify($('#form-modify').serializeArray())},
		success:function(data){
			//alert(data);
			//parse data
			eval('var obj = ' + data + ';');
			if(typeof(obj.formerrors) !== "undefined" && obj.formerrors){
				for(var o in obj.formerrors){
					if($('#modal-modify-input-' + obj.formerrors[o])){
						$('#modal-modify-input-' + obj.formerrors[o]).css('border-color', '#b94a48');
						}
					}
				}
			if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
				$('#modal-modify-items-alert-msg').html(obj.msgerrors);
				$('#modal-modify-items-alert').show();
				$('#modal-modify-items-body').scrollTo(0, 500);
				showButtLoadingTxt('#modal-modify-items-save', '<?php echo formatJavascript(_T('save changes')); ?>');
			}else{
				//pas erreur reload page
				location.reload();
				}
			},
		error:function(){
			showButtLoadingTxt('#modal-modify-items-save', '<?php echo formatJavascript(_T('save changes')); ?>');
			}	
		});
	}		
	
//---------------------------------

function getContentCategoryInfos(strid){
	resetForm(true);
	showPencilLoadingImg(strid, true);
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'news',service:'get-category-infos',id:strid},
		beforeSend:function(){
			//
			},
		success:function(data){
			//parse data
			eval('var obj = ' + data + ';');
			//var obj = $.parseJSON(data);
			//check si erreur from serveur
			if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
				$('#modal-alert-title').text('<?php echo formatJavascript(_T('error')); ?>');
				$('#modal-alert-content').text(obj.msgerrors);
				$('#modal-alert-close').text('<?php echo formatJavascript(_T('close')); ?>');
				$('#modal-alert').modal('show');
			}else{
				//the infos
				$('#modal-modify-input-id').val(obj.id);
				$('#modal-modify-input-link_id').val(obj.link_id);
				$('#modal-modify-input-name').val(htmlspecialchars_decode(obj.name));
				$('#modal-modify-input-position').val(htmlspecialchars_decode(obj.position));
				$('#modal-modify-input-content').val(htmlspecialchars_decode(obj.content));
				$('#modal-modify-input-meta_title').val(htmlspecialchars_decode(obj.meta_title));
				$('#modal-modify-input-meta_description').val(htmlspecialchars_decode(obj.meta_description));
				$('#modal-modify-input-meta_keywords').val(htmlspecialchars_decode(obj.meta_keywords));
				if(obj.status == "1"){
					$('#modal-modify-input-status').prop('checked',true);
				}else{
					$('#modal-modify-input-status').prop('checked',false);
					}
				$('#modal-modify-input-parent_id option[value="' + obj.parent_id + '"]').prop('selected',true);
				//the window
				$('#modal-modify-items-title').text('<?php echo formatJavascript(_T('modifying content category')); ?>');
				$('#modal-modify-items-close').text('<?php echo formatJavascript(_T('close')); ?>');
				$('#modal-modify-items-save').text('<?php echo formatJavascript(_T('save changes')); ?>');
				$('#modal-modify-items-save').unbind('click');	
				$('#modal-modify-items-save').click(function(e){
					e.preventDefault();
					setContentCategoryInfos(false);
					});
				$('#modal-modify-items').modal('show');
				$('#modal-modify-items-body').scrollTo(0,500);
				}
			showPencilLoadingImg(strid, false);	
				
			},
		error:function(){
			showPencilLoadingImg(strid, false);
			//
			}
		});
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
	
//---------------------------------	

function removeTR(){
	eval('var arrTr = [' + getCheckedTR() + '];');
	if(typeof(arrTr) != 'undefined'){
		for(var o in arrTr){
			//check box
			$('#cb' + arrTr[o]).attr('checked', false);
			//remove row
			$('#tr' + arrTr[o]).remove();
			}
		}
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
	for(var i=0;i<=<?php echo $arrOutput['content']['category']['cmpt-row']; ?>;i++){
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
</script>
</body>
<?php 


require_once(DIR_VIEWS.'append.php'); 




//END