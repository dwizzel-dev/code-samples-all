<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default news items listing view

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
	<div class="row-fluid thick-border-b" style="margin-bottom:20px;">
		<div class="span12 inline">
			<div class="pull-left">
				<select id="filter-categorie">
				<?php
				echo '<option value="">'._T('category filter').'</option>'.EOL;	
				foreach($arrOutput['content']['category-dropdown'] as $k=>$v){
					echo '<option value="'.$v['id'].'"';
					if(isset($arrOutput['content']['category-dropdown-selected']) && $arrOutput['content']['category-dropdown-selected'] == $v['id']){
						echo ' selected ';
						}
					echo '>'.$v['text'].'</option>'.EOL;
					}
				?>
				</select>
				<select id="filter-language">
				<?php
				echo '<option value="">'._T('language filter').'</option>'.EOL;	
				foreach($arrOutput['content']['language-dropdown'] as $k=>$v){
					echo '<option value="'.$v['id'].'" ';
					if(isset($arrOutput['content']['language-dropdown-selected']) && $arrOutput['content']['language-dropdown-selected'] == $v['id']){
						echo ' selected ';
						}
					echo '>'.$v['text'].'</option>'.EOL;
					}
				?>
				</select>
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
		</div>
	</div>		
	<div class="row-fluid" style="margin-bottom:20px;">
		<div class="span12 inline">
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
					foreach($arrOutput['content']['items']['columns'] as $k=>$v){
						echo '<th>'.ucfirst($v).'</th>';
						}
					?>
					</tr>
				</thead>
				<tbody>
					<?php
					$cbCmpt = -1;
					//level 0
					if(is_array($arrOutput['content']['items']['rows'])){
						foreach($arrOutput['content']['items']['rows'] as $k=>$v){
							$cbCmpt++;
							if($v['status'] == '1'){
								echo '<tr id="tr'.$cbCmpt.'" class="">';
							}else{
								echo '<tr id="tr'.$cbCmpt.'" class="disable">';
								}
							echo '<td><input type="checkbox" id="cb'.$cbCmpt.'" value="'.$v['id'].'"></td>';
							echo '<td>'.$v['name'].'</td>';
							if($v['path'] != ''){
								if(SIMPLIFIED_SITE_PATH){
									echo '<td><a href="/'.$v['language'].'/'.$v['path'].'/'.$v['id'].'/'.$v['alias'].'/" target="_new">/'.$v['language'].'/'.$v['path'].'/'.$v['id'].'/'.$v['alias'].'/</a></td>';
								}else{
									$arrLangCodeByPrefix = $oGlob->get('languages_code_by_prefix');
									echo '<td><a href="'.PATH_WEB_SITE.'index.php?&lang='.$arrLangCodeByPrefix[$v['language']].'&path='.$v['path'].'/'.$v['id'].'/'.$v['alias'].'/" target="_new">'.PATH_WEB_SITE.'index.php?&lang='.$arrLangCodeByPrefix[$v['language']].'&path='.$v['path'].'/'.$v['id'].'/'.$v['alias'].'/</a></td>';
									}
							}else{
								echo '<td>--</td>';
								}
							echo '<td>'.$v['hits'].'</td>';
							echo '<td>'.$v['date_modified'].'</td>';
							echo '<td>'.$v['title'].'</td>';
							echo '<td>'.$v['id'].'</td>';
							echo '<td><a id="buttmodify'.$v['id'].'" href="#"><img src="'.PATH_IMAGE.'glyphicons/glyphicons_030_pencil.png" class="img-pencil"></a><img id="imgloading'.$v['id'].'" src="'.PATH_IMAGE.'loading.gif" style="display:none;" class="img-loading"></td>';
							echo '</tr>'.EOL;
							//script action on butt
							echo '<script type="text/javascript">'.EOL;
							echo 'jQuery(document).ready(function($){'.EOL;
							echo '	$("#buttmodify'.$v['id'].'").click(function(e){'.EOL;
							echo '		e.preventDefault();'.EOL;
							echo '		getItemInfos('.$v['id'].');'.EOL;
							echo '		});'.EOL;
							echo '	});'.EOL;
							echo '</script>'.EOL;
							}
						}	
					?>
				</tbody>
			</table>
			<form id="form-process" method="post">
				<input type="hidden" name="cbchecked" id="cbchecked" value="">
			</form>	
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
<!-- modal win for modifying category form -->
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
		<form id="form-modify" method="post">
			<input type="hidden" name="id" id="modal-modify-input-id" value="">
			<label class="checkbox"  style="margin-bottom:20px;">
			<input type="checkbox" id="modal-modify-input-status" name="status"> <?php echo _T('active'); ?>
			</label>
			<label><?php echo _T('name'); ?> :</label>
			<input type="text" name="name" id="modal-modify-input-name" class="w96">
			<label><?php echo _T('news category'); ?> :</label>
			<select name="category_id" class="input-xlarge" id="modal-modify-input-category_id">
			<?php
			foreach($arrOutput['content']['category-dropdown'] as $k=>$v){
				echo '<option value="'.$v['id'].'">'.$v['text'].'</option>'.EOL;
				}
			?>
			</select>
			<label><?php echo _T('meta title and page title'); ?> :</label>
			<textarea rows="3" name="meta_title" id="modal-modify-input-meta_title" class="w96"></textarea>
			<label><?php echo _T('meta description'); ?> :</label>
			<textarea rows="3" name="meta_description" id="modal-modify-input-meta_description" class="w96"></textarea>
			<label><?php echo _T('meta keywords'); ?> :</label>
			<textarea rows="3" name="meta_keywords" id="modal-modify-input-meta_keywords" class="w96"></textarea>
			<label><?php echo _T('news preview'); ?> :</label>
			<textarea rows="3" name="preview" id="modal-modify-input-preview" class="w96"></textarea>
			<label><?php echo _T('page content'); ?> :</label>
			<textarea rows="3" name="content" id="modal-modify-input-content" class="w96"></textarea>
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
	//event on filter
	$('#filter-categorie').change(function(){
		window.location.href = '<?php echo $oGlob->getArray('links','news-listing')?>page-0/category-' + $(this).attr('value') + '/';
		});
	$('#filter-language').change(function(){
		window.location.href = '<?php echo $oGlob->getArray('links','news-listing')?>page-0/language-' + $(this).attr('value') + '/';
		});	
	$('#filter-display').change(function(){
		window.location.href = '<?php echo $oGlob->getArray('links','news-listing')?>page-0/display-' + $(this).attr('value') + '/';
		});	
	$('#filter-sortby').change(function(){
		window.location.href = '<?php echo $oGlob->getArray('links','news-listing')?>page-0/sortby-' + $(this).attr('value') + '/';
		});	
	//delete items
	$('#buttdelete').click(function(e){
		e.preventDefault();
		if(getCheckedItems()){
			$('#modal-win-title').text('<?php echo formatJavascript(_T('warning')); ?>');
			$('#modal-win-content').html('<?php echo formatJavascript(_T('all selected items will be <b>deleted</b>, are you sure?')); ?>');
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
	//create new items
	$('#buttcreate').click(function(e){
		e.preventDefault();
		$('#modal-modify-items-title').text('<?php echo formatJavascript(_T('new news item')); ?>');
		$('#modal-modify-items-close').text('<?php echo formatJavascript(_T('close')); ?>');
		$('#modal-modify-items-save').text('<?php echo formatJavascript(_T('save changes')); ?>');
		$('#modal-modify-items-save').unbind('click');	
		$('#modal-modify-items-save').click(function(e){
			e.preventDefault();
			setItemsInfos();
			});
		$('#modal-modify-items').modal('show');
		resetForm(true);
		});	
	//search box
	$('#buttsearchitems').click(function(e){
		e.preventDefault();
		if($('#filter-searchitems').attr('value') != ''){
			window.location.href = '<?php echo $oGlob->getArray('links','news-listing')?>page-0/searchitems-' + $('#filter-searchitems').attr('value') + '/';
		}else{
			window.location.href = '<?php echo $oGlob->getArray('links','news-listing')?>page-0/searchitems-0/';
			}
		});	
	//clear serach
	$('#buttsearchclear').click(function(e){
		e.preventDefault();
		window.location.href = '<?php echo $oGlob->getArray('links','news-listing')?>page-0/searchitems-0/';
		});	
	});
	
//---------------------------------

function setItemsInfos(){
	showButtLoadingTxt('#modal-modify-items-save', '');
	resetForm(false);
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'news',service:'new-item-infos',data:JSON.stringify($('#form-modify').serializeArray())},
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
				//pas erreur alors on va a la page des produits
				}
			},
		error:function(){
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

function showButtLoadingTxt(strButt, strText){
	if(strText == ''){
		$(strButt).text('<?php echo formatJavascript(_T('wait!...')); ?>');
	}else{
		$(strButt).text(strText);
		}
	}		

//---------------------------------

function resetForm(bText){
	$('#modal-modify-items-alert').hide();
	$('#modal-modify-items-alert-msg').text();
	$('#modal-modify-items-body').scrollTo(0, 500);	
	var arr = [['status', 'checkbox'], ['name', 'val'], ['category_id', 'val'], ['meta_title', 'val'], ['meta_description', 'val'], ['meta_keywords', 'val'], ['content', 'val'],  ['preview', 'val'],];
	for(var o in arr){
		if(bText){
			if(arr[o][1] == 'text'){
				//$('#modal-modify-input-' + arr[o][0]).text('');
				$('#modal-modify-input-' + arr[o][0]).val('');
			}else if(arr[o][1] == 'val'){
				$('#modal-modify-input-' + arr[o][0]).val('');
			}else if(arr[o][1] == 'checkbox'){
				$('#modal-modify-input-' + arr[o][0]).attr('checked', false);
				}
			}	
		$('#modal-modify-input-' + arr[o][0]).css('border-color', '');
		}
	}	


//---------------------------------

function getItemInfos(strid){
	window.location.href = '<?php echo $oGlob->getArray('links','news-items')?>item-' + strid + '/';
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

function deleteItem(){
	$('#cbchecked').val(getCheckedItems());
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'news',service:'delete-item-infos',data:JSON.stringify($('#form-process').serializeArray())},
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
		data: {section:'news',service:'disable-item-infos',data:JSON.stringify($('#form-process').serializeArray())},
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
		data: {section:'news',service:'enable-item-infos',data:JSON.stringify($('#form-process').serializeArray())},
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
	
	
</script>	
</body>
<?php 


require_once(DIR_VIEWS.'append.php'); 





//END



