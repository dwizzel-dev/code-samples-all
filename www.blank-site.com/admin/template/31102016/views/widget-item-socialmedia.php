<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default widget-item socialmedia view

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
	<div class="row-fluid">
		<div class="span12 inline">
			<a name="page-top" id="page-top"></a>
			<div class="pull-left">
				<?php echo '<h2>'.ucfirst($arrOutput['content']['item']['h-title']).'</h2>'; ?>
			</div>
			<div class="btn-group pull-right">
				<button class="btn" id="butt-createitems"><?php echo _T('add a new item'); ?></button>
				<?php
				if($arrOutput['content']['item']['details']['status'] == '1'){
					echo '<button class="btn btn-success" id="buttchangestatus">'._T('disable').'</button>';
				}else{
					echo '<button class="btn btn-danger" id="buttchangestatus">'._T('enable').'</button>';
					}
				?>
			</div>
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
	<div class="row-fluid">
		<div class="span12">
			<form class="form-vertical" id="form-widget">
				<div id="div-items-main" class="row-fluid">
					<?php
					$strItemsValues = '';
					$cmptSocialMediaItems = 0;
					if(is_array($arrOutput['content']['item']['details']['data-unserialize'])){
						foreach($arrOutput['content']['item']['details']['data-unserialize'] as $k=>$v){
							$strItemsValues .= $cmptSocialMediaItems.',';
							
							echo '<div id="div-main-items-'.$cmptSocialMediaItems.'" class="widget-block opened">';
													
							echo '<div class="row-fluid"><div class="span12"><h4 class="pull-left">'._T('social media').'</h4><button class="btn pull-right" id="buttremove-items" onclick="removeItems('.$cmptSocialMediaItems.')"><i class="icon-remove"></i></button></div></div>';
																			
							if($v['icon'] == ''){
								$v['icon'] = DEFAULT_NO_ICON;
								}
							
							echo '<div class="control-group">';
							echo '<div class="controls input-append">';
							echo '<div style="margin-right:20px;display:inline;"><img id="modal-modify-input-icon-'.$cmptSocialMediaItems.'" class="img-polaroid" src="'.PATH_SOCIALMEDIA_ICONS.$v['icon'].'"></div>';
							
							echo '<input id="modal-modify-input-image-'.$cmptSocialMediaItems.'" type="text" value="'.$v['icon'].'" class="input-xlarge"><button type="button" class="btn" onclick="iconSelect('.$cmptSocialMediaItems.');">'._T('select icon').'</button></div>';
							echo '</div>';
							echo '<div class="control-group"><label class="control-label">'._T('text').'</label><div class="controls"><input type="text" id="modify-input-text-'.$cmptSocialMediaItems.'" class="input-xlarge" value="'.$v['text'].'"></div></div>';
							echo '<div class="control-group"><label class="control-label">'._T('alternative text').'</label><div class="controls"><input type="text" id="modify-input-alt-'.$cmptSocialMediaItems.'" class="input-xlarge" value="'.$v['alt'].'"></div></div>';
							if(!isset($v['url'])){
								$v['url'] = '';
								}
							echo '<div class="control-group"><label class="control-label">'._T('url').'</label><div class="controls"><input type="text" id="modify-input-url-'.$cmptSocialMediaItems.'" class="input-xlarge" value="'.$v['url'].'"></div></div>';
							
							echo '</div>';
							
							$cmptSocialMediaItems++;
							}
						if($strItemsValues != ''){
							$strItemsValues = substr($strItemsValues, 0, (strlen($strItemsValues) - 1));
							}	
						}
					?>
				</div>
				<input type="hidden" id="modify-input-items_values" value="<?php echo '['.$strItemsValues.']'; ?>">
				<div class="control-group">
					<div class="controls">
						<br><button class="btn btn-primary" id="buttsave-items"><?php echo _T('save changes'); ?></button>
					</div>
				</div>
			</form>
		</div>
	</div>	
	<?php require_once(DIR_VIEWS.'footer.php'); ?>
</div>
<form id="form-process" method="post">
	<input type="hidden" name="cbchecked" id="cbchecked" value="<?php echo $arrOutput['content']['item-id'];?>">
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
		<button class="btn" data-dismiss="modal" aria-hidden="true"  id="modal-win-close"></button>
		<button class="btn btn-primary" id="modal-win-save"></button>
	</div>
</div>
<script type="text/javascript">

//---------------------------------
//global counter
var gCmptItemBlock = <?php echo --$cmptSocialMediaItems; ?>;


//---------------------------------

jQuery(document).ready(function($){
	//changes status
	$('#buttchangestatus').click(function(e){
		e.preventDefault();
		showButtLoadingTxt('#buttchangestatus', '');
		<?php
		if($arrOutput['content']['item']['details']['status'] == '1'){
			echo 'disableItem();'.EOL;
		}else{
			echo 'enableItem();'.EOL;
			}
		?>
		});
	//promotion form butt
	$('#buttsave-items').click(function(e){
		e.preventDefault();
		showButtLoadingTxt('#buttsave-items', '');
		saveSocialMedia();
		});	
	//promotion form butt
	$('#butt-createitems').click(function(e){
		e.preventDefault();
		insertItems();
		});		
	
	});
	
//---------------------------------	
function scrollToElement(id){
	$(window).scrollTo(id, 500); 
	};	
	
//---------------------------------

function insertItems(){
	gCmptItemBlock++;
	var arrIds = JSON.parse($('#modify-input-items_values').val());
	arrIds.push(gCmptItemBlock);
	$('#modify-input-items_values').val(JSON.stringify(arrIds));
	var code = '';
	code += '<div id="div-main-items-' + gCmptItemBlock + '" class="widget-block">';
	code += '<div class="row-fluid"><div class="span12"><h4 class="pull-left"><?php echo _T('social media'); ?></h4><button class="btn pull-right" id="buttremove-items" onclick="removeItems(' + gCmptItemBlock + ')"><i class="icon-remove"></i></button></div></div>';
	code += '<div class="control-group">';
	code += '<div class="controls input-append">';
	code += '<div style="margin-right:20px;display:inline;"><img id="modal-modify-input-icon-' + gCmptItemBlock + '" class="img-polaroid" src="<?php echo PATH_SOCIALMEDIA_ICONS.DEFAULT_NO_ICON; ?>"></div>';
	code += '<input id="modal-modify-input-image-' + gCmptItemBlock + '" type="text" class="input-xlarge"><button type="button" class="btn" onclick="iconSelect(' + gCmptItemBlock + ');"><?php echo _T('select icon'); ?></button></div>';
	code += '</div>';
	code += '<div class="control-group"><label class="control-label"><?php echo _T('text'); ?></label><div class="controls"><input type="text" id="modify-input-text-' + gCmptItemBlock + '" class="input-xlarge"></div></div>';
	code += '<div class="control-group"><label class="control-label"><?php echo _T('alternative text'); ?></label><div class="controls"><input type="text" id="modify-input-alt-' + gCmptItemBlock + '" class="input-xlarge" ></div></div>';
	code += '<div class="control-group"><label class="control-label"><?php echo _T('url'); ?></label><div class="controls"><input type="text" id="modify-input-url-' + gCmptItemBlock + '" class="input-xlarge"></div></div>';
	code += '</div>';
	
	$('#div-items-main').append(code);
	scrollToElement('#div-main-items-' + gCmptItemBlock);
	}
	
//---------------------------------

function removeItems(id){
	if($('#div-main-items-' + id)){
		$('#div-main-items-' + id).remove();
		var arrIds = JSON.parse($('#modify-input-items_values').val());
		arrIds = $.grep(arrIds, function(a){return a != id;});
		$('#modify-input-items_values').val(JSON.stringify(arrIds));
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
	

//---------------------------------

function disableItem(){
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'widget',service:'disable-widget-infos',data:JSON.stringify($('#form-process').serializeArray())},
		success:function(data){
			//parse data
			eval('var obj = ' + data + ';');
			if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
				showButtLoadingTxt('#buttchangestatus', '<?php echo _T('disable'); ?>');
				$('#modal-alert-title').text('<?php echo formatJavascript(_T('error')); ?>');
				$('#modal-alert-content').html(obj.msgerrors);
				$('#modal-alert-close').text('<?php echo formatJavascript(_T('close')); ?>');
				$('#modal-alert').modal('show');
			}else{
				$('#buttchangestatus').unbind('click');
				$('#buttchangestatus').removeClass('btn-success');
				$('#buttchangestatus').addClass('btn-danger');
				$('#buttchangestatus').click(function(e){
					e.preventDefault();
					showButtLoadingTxt('#buttchangestatus', '');
					enableItem();
					});
				showButtLoadingTxt('#buttchangestatus', '<?php echo _T('enable'); ?>');	
				}	
			},
		error:function(){
			showButtLoadingTxt('#buttchangestatus', '<?php echo _T('disable'); ?>');
			}	
		});
	}		
	
//---------------------------------

function enableItem(){
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'widget',service:'enable-widget-infos',data:JSON.stringify($('#form-process').serializeArray())},
		success:function(data){
			//parse data
			eval('var obj = ' + data + ';');
			if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
				showButtLoadingTxt('#buttchangestatus', '<?php echo _T('enable'); ?>');
				$('#modal-alert-title').text('<?php echo formatJavascript(_T('error')); ?>');
				$('#modal-alert-content').html(obj.msgerrors);
				$('#modal-alert-close').text('<?php echo formatJavascript(_T('close')); ?>');
				$('#modal-alert').modal('show');
			}else{
				$('#buttchangestatus').unbind('click');
				$('#buttchangestatus').removeClass('btn-danger');
				$('#buttchangestatus').addClass('btn-success');
				$('#buttchangestatus').click(function(e){
					e.preventDefault();
					showButtLoadingTxt('#buttchangestatus', '');
					disableItem();
					});
				showButtLoadingTxt('#buttchangestatus', '<?php echo _T('disable'); ?>');
				}
			},
		error:function(){
			showButtLoadingTxt('#buttchangestatus', '<?php echo _T('enable'); ?>');
			}	
		});
	}	
	
//---------------------------------
	
function saveSocialMedia(){	
	if($('#modify-input-items_values')){
		//active ids
		var arrIds = JSON.parse($('#modify-input-items_values').val());
		var arrValues = [];
		for(var o in arrIds){
			arrValues.push({
				icon:$('#modal-modify-input-image-' + arrIds[o]).val(),
				alt:$('#modify-input-alt-' + arrIds[o]).val(),
				url:$('#modify-input-url-' + arrIds[o]).val(),
				text:$('#modify-input-text-' + arrIds[o]).val(),
				size:'3'
				});
			}
		$.ajax({
			type: 'POST',
			url: '<?php echo PATH_SERVICE; ?>',
			data: {section:'widget',service:'modify-widget-infos',data:JSON.stringify(arrValues),id:'<?php echo $arrOutput['content']['item-id'];?>'},
			success:function(data){
				//parse data
				eval('var obj = ' + data + ';');
				if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
					showButtLoadingTxt('#buttsave-items', '<?php echo _T('save changes'); ?>');
					$('#box-alert-msg').html(obj.msgerrors);
					$('#box-alert').show();
					scrollToElement('#page-top');
				}else{
					//pas erreur reload page
					//location.reload();
					//pas besoin de reload sur ce tab
					successAlert(1);
					showButtLoadingTxt('#buttsave-items', '<?php echo _T('save changes'); ?>');
					location.reload();
					
					}
				},
			error:function(){
				//
				}	
			});	
		
		}
	}

	
//---------------------------------

function selectItems(id, icon){
	$('#modal-alert').modal('hide');
	$('#modal-modify-input-image-' + id).val(icon);
	$('#modal-modify-input-icon-' + id).attr('src', '<?php echo PATH_SOCIALMEDIA_ICONS; ?>' + icon);
	}

//---------------------------------

function iconSelect(id){
	<?php
	//le array de tout les icones genere en php
	echo 'var arrData = '.json_encode($arrOutput['content']['item']['icons-list']).';'.EOL;
	?>
	var code = '<div style="width:100%;margin-bottom:20px;"><?php echo _T('click on the icon you want to insert'); ?></div>';
	for(var i=0; i<arrData.length;i++){
		code += '<div id="div-list-items-' + i + '" item-id="' + i + '" item-selected="0" item-image="' + arrData[i] + '" item-name="' + arrData[i] + '" onclick="selectItems(' + id + ',\'' + arrData[i] + '\')" class="pull-left img-polaroid" style="margin:5px;">';
		code += '<img src="<?php echo PATH_SOCIALMEDIA_ICONS; ?>' + arrData[i] + '">';
		code += '</div>';
		}
	//on va chercher tous les icones du repertoire
	$('#modal-alert-title').text('<?php echo formatJavascript(_T('icon list')); ?>');
	$('#modal-alert-content').html(code);
	$('#modal-alert-close').text('<?php echo formatJavascript(_T('close')); ?>');
	$('#modal-alert').modal('show');
	}
	

	
	
	
	
</script>
</body>
<?php require_once(DIR_VIEWS.'append.php'); ?>