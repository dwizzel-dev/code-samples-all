<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default widget-item content view

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
					$cmptCarouselItems = 0;
					if(is_array($arrOutput['content']['item']['details']['data-unserialize'])){
						foreach($arrOutput['content']['item']['details']['data-unserialize'] as $k=>$v){
							$strItemsValues .= $cmptCarouselItems.',';
							
							echo '<div id="div-main-items-'.$cmptCarouselItems.'" class="widget-block">';
													
							echo '<input type="hidden" name="content-'.$cmptCarouselItems.'" id="content-'.$cmptCarouselItems.'" value="'.$v['text'].'">';
							
							//ouvrir le contenu
							echo '<div class="opener-butt"><div onclick="expandWidgetRow('.$cmptCarouselItems.');"><input type="text" value="'.$v['name'].'" placeholder="default name" id="modify-input-name-'.$cmptCarouselItems.'">';
							//supprimer et activer
							echo '<div class="btn-group pull-right">';
							if(!intVal($v['active'])){
								echo '<button class="btn btn-danger" id="butt-activation-'.$cmptCarouselItems.'" activated="0">'._T('enable').'</button>';
							}else{
								echo '<button class="btn btn-success" id="butt-activation-'.$cmptCarouselItems.'" activated="1">'._T('disable').'</button>';
								}
							echo '<button class="btn" onclick="removeItems('.$cmptCarouselItems.')"><i class="icon-remove"></i></button>';
							echo '</div>';
							//
							echo '</div></div>';//close the ouvrir le contenu
							
							
							$strImage = $v['img']; 												
							if($strImage == ''){
								$strImage = DEFAULT_NO_IMAGE;
								}
							echo '<div class="widget-block-div-image"><img class="img-polaroid" src="'.PATH_WEB_MEDIA.$strImage.'"></div>';

							echo '<div class="control-group">';
							echo '<div class="controls input-append"><input id="modify-input-image-'.$cmptCarouselItems.'" type="text" value="'.$v['img'].'" class="input-xlarge"><button type="button" class="btn" onclick="ImageSelector.select(\'modify-input-image-'.$cmptCarouselItems.'\');">'._T('select').'</button></div>';
							echo '</div>';
							
							//css class
							echo '<div class="control-group">';
							echo '<label class="control-label">'._T('extra css class').'</label>';
							echo '<input type="text" value="'.$v['css_class'].'" class="form-control" placeholder="" id="modify-input-css_class-'.$cmptCarouselItems.'">';
							echo '</div>';
							
							//background-color
							echo '<div class="control-group">';
							echo '<label class="control-label">'._T('background color').'</label>';
							echo '<select id="modify-select-bgcolor-'.$cmptCarouselItems.'" class="input-xlarge">';
							echo '<option value="">--</option>';
							foreach($arrOutput['content']['colors-dropdown'] as $k2=>$v2){
								echo '<option value="'.$v2.'"';
								if($v2 == $v['bgcolor']){
									echo ' selected ';
									}
								echo '>';	
								echo $k2;
								echo '</option>';
								}
							echo '</select>';
							echo '</div>';
							
							//links button
							echo '<div class="control-group">';
							echo '<label class="control-label">'._T('link').'</label>';
							echo '<select id="modify-select-link-'.$cmptCarouselItems.'" class="input-xlarge">';
							echo '<option value="0">--</option>';
							foreach($arrOutput['content']['link-dropdown'] as $k2=>$v2){
								echo '<option value="'.$v2['id'].'"';
								if(intVal($v2['id']) == intVal($v['link'])){
									echo ' selected ';
									}
								echo '>';	
								if($v2['extern'] == '1'){	
									echo $v2['path'].' [extern]';
								}else{
									echo '/'.$v2['language'].'/'.$v2['path'].'/';
									}
								echo '</option>';
								}
							echo '</select>';
							echo '</div>';
							
							echo '<div class="control-group"><label class="control-label">'._T('content').'</label><div class="controls"><textarea rows="3" uid="'.$cmptCarouselItems.'" id="content-'.$cmptCarouselItems.'-html" class="w96 editable">'.$v['text'].'</textarea></div></div>';

							//la liste des pages sur lesquelles montre le widget
							echo '<div class="control-group">';
							echo '<label class="control-label">'._T('links where showed').'</label>';
							echo '<div class="controls">';
							//on va calculer combien on en a diviser en 3 colonnes
							$iNumCols = 4;
							$iMaxPerCols = intVal(count($arrOutput['content']['links-for-checkbox'])/$iNumCols);
							$iCountElPerCols = 0;
							$iCmptLinksItems = 0;
							$arrExplodedLinksString = explode(',', $v['links']);
							echo '<div class="row-fluid">';
							//la liste de checkbox qui sera coche ou pas
							foreach($arrOutput['content']['links-for-checkbox'] as $k2=>$v2){
								if($iCountElPerCols === 0){
									echo '<div class="span'.(12/$iNumCols).'">';
									echo '<table class="table table-hover">';
									echo '<tbody>';
									}	
								echo '<tr>';
								echo '<td class="small"><input type="checkbox" id="cb-links-'.$cmptCarouselItems.'-'.$iCmptLinksItems.'" value="'.$v2['id'].'" ';
								if(in_array($v2['id'], $arrExplodedLinksString)){
									echo ' checked ';
									}
								echo '></td>';
								echo '<td><small>'.$v2['path'].'</small></td>';
								echo '</tr>';
								//saut de table
								$iCmptLinksItems++;	
								$iCountElPerCols++;	
								if(($iCountElPerCols >= $iMaxPerCols) || (count($arrOutput['content']['links-for-checkbox']) == $iCmptLinksItems)){				
									echo '</tbody>';	
									echo '</table>';
									echo '</div>';
									$iCountElPerCols = 0;
									}
								
								}
							echo '</div>'; //close rows fluid de tables
							echo '</div>'; //close controls
							echo '</div>'; //close control-group
							
							//
							echo '</div>'; //close le div-main-items
							
							$cmptCarouselItems++;
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
var gCmptItemBlock = <?php echo --$cmptCarouselItems; ?>;
var gArrLinksCount = <?php echo count($arrOutput['content']['links-for-checkbox']);?>;
var gArrLinks = [];
<?php 
foreach($arrOutput['content']['links-for-checkbox'] as $k=>$v){
	echo 'gArrLinks['.$v['id'].'] = "'.$v['path'].'";'.EOL;
	}
?>

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
		saveCarousel();
		});	
	//promotion form butt
	$('#butt-createitems').click(function(e){
		e.preventDefault();
		insertItems();
		});	
	//les boutons de status de chacun des items
	for(var i=0;i<=gCmptItemBlock;i++){	
		$('#butt-activation-' + i).click(function(e){
			e.preventDefault();
			activateSingleItem(this.id);
			});
		}	
	//editor
	addEditor('');
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
	code += '<div id="div-main-items-' + gCmptItemBlock + '" class="widget-block  opened">';
	code += '<input type="hidden" name="content-' + gCmptItemBlock + '" id="content-' + gCmptItemBlock + '">';
	//open
	code += '<div class="opener-butt"><div onclick="expandWidgetRow(' + gCmptItemBlock + ');"><input type="text" value="" placeholder="default name" id="modify-input-name-' + gCmptItemBlock + '"></div></div>';
	//
	code += '<div class="btn-group pull-right"><button class="btn btn-danger" id="butt-activation-' + gCmptItemBlock + '" activated="0"><?php echo _T('enable'); ?></button><button class="btn" onclick="removeItems(' + gCmptItemBlock + ')"><i class="icon-remove"></i></button></div>';	
	//images	
	code += '<div class="control-group">';
	code += '<div class="controls input-append"><input id="modify-input-image-' + gCmptItemBlock + '" type="text" class="input-xlarge"><button type="button" class="btn" onclick="ImageSelector.select(\'modify-input-image-' + gCmptItemBlock + '\');"><?php echo _T('select'); ?></button></div>';
	code += '</div>';
	//css class
	code += '<div class="control-group"><label class="control-label"><?php echo _T('extra css class'); ?></label>';
	code += '<input type="text" value="" class="form-control" placeholder="" id="modify-input-css_class-' + gCmptItemBlock + '">';
	code += '</div>';	
	//background colors
	code += '<div class="control-group"><label class="control-label"><?php echo _T('background color'); ?></label><select id="modify-select-bgcolor-' + gCmptItemBlock + '" class="input-xlarge">';
	code += '<option value="0">--</option>';
	<?php	
	if(count($arrOutput['content']['colors-dropdown'])){	
		echo 'code += \'';	
		foreach($arrOutput['content']['colors-dropdown'] as $k=>$v){
			echo '<option value="'.$v.'">';
			echo $k;
			echo '</option>';
			}
		}
		echo '\';'.EOL;
	?>	
	code += '</select></div>';	
	//le link		
	code += '<div class="control-group"><label class="control-label"><?php echo _T('link'); ?></label><select id="modify-select-link-' + gCmptItemBlock + '" class="input-xlarge">';
	code += '<option value="">--</option>';
	<?php	
	if(count($arrOutput['content']['link-dropdown'])){	
		echo 'code += \'';	
		foreach($arrOutput['content']['link-dropdown'] as $k=>$v){
			echo '<option value="'.$v['id'].'">';
			if($v['extern'] == '1'){	
				echo $v['path'].' [extern]';
			}else{
				echo '/'.$v['language'].'/'.$v['path'].'/';
				}
			echo '</option>';
			}
		}
		echo '\';'.EOL;
	?>	
	code += '</select></div>';
	//le contenu	
	code += '<div class="control-group"><label class="control-label"><?php echo _T('content'); ?></label><div class="controls"><textarea rows="3" id="content-' + gCmptItemBlock + '-html" class="w96 editable"></textarea></div></div>';
	//le select des links sur lequel il apparait
	//la liste des pages sur lesquelles montre le widget
	code += '<div class="control-group"><label class="control-label"><?php echo _T('links where showed'); ?></label><div class="controls"><table class="table table-hover"><tbody>';
	var iCmptLinksItems = 0;
	for(var o in gArrLinks){
		code += '<tr>';
		code += '<td class="small"><input type="checkbox" id="cb-links-' + gCmptItemBlock + '-' + iCmptLinksItems + '" value="' + o + '"></td>';
		code += '<td><small>' + gArrLinks[o] + '</small></td>';
		code += '</tr>';
		iCmptLinksItems++;
		}	
	code += '</tbody></table></div></div>';
	
	code += '</div>';	
	$('#div-items-main').append(code);
	scrollToElement('#div-main-items-' + gCmptItemBlock);
	//
	addEditor('content-' + gCmptItemBlock + '-html');	
	}


//---------------------------------
function addEditor(strId){
	var obj = {
		relative_urls: false,
		document_base_url : '<?php echo PATH_WEB; ?>',
		plugins: 'image table preview media code anchor link lists fullscreen moxiecut visualblocks visualchars',
		image_advtab: true,
		toolbar1: 'insertfile undo redo preview | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor | code',
		selector: 'textarea.editable',
		content_css : [
			'<?php echo PATH_WEB_CSS; ?>global.css',
			'<?php echo PATH_WEB_CSS; ?>responsive.css',
			'<?php echo PATH_CSS; ?>editor/widget-content-item.css'
			],
		inline: false,
		height : 250,
		//call back de l'instance pour avoir le id et le textarea id de l'editeur de base
		init_instance_callback : function(editor){
			//on rajoute l'image
			this.settings.plugin_preview_image_path = '<?php echo PATH_WEB_MEDIA; ?>';
			this.settings.plugin_preview_image_input = 'modify-input-image-' + $('#' + editor.id).attr('uid');
			this.settings.plugin_preview_bgcolor_select = 'modify-select-bgcolor-' + $('#' + editor.id).attr('uid');
			this.settings.plugin_preview_cssclass_input = 'modify-input-name-' + $('#' + editor.id).attr('uid');
			},
		//preview plugin
		plugin_preview_type: 'widget-content-item', //le type de widget pour le preview
		plugin_preview_background_image: '', //dwizzel to show bg image	
		};	
	//editor
	//with MOXIECUT image editor
	if(strId != ''){	
		//le new editor pour celui-ci
		var nEditor = new tinymce.Editor(strId, obj, tinymce.EditorManager);
		//render	
		nEditor.render();
		
	}else{
		tinymce.PluginManager.load('moxiecut', '<?php echo PATH_WEB; ?>js/editor.with-moxiecut/plugins/moxiecut/plugin.min.js');
		tinymce.init(obj);
		}
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
	
function saveCarousel(){	
	if($('#modify-input-items_values')){
		//active ids
		var arrIds = JSON.parse($('#modify-input-items_values').val());
		var arrValues = [];
		for(var o in arrIds){
			setHtmlContent('content-' + arrIds[o]);
			arrValues.push({
				active:parseInt($('#butt-activation-' + arrIds[o]).attr('activated')),
				img:$('#modify-input-image-' + arrIds[o]).val(),
				name:cleanUrlText($('#modify-input-name-' + arrIds[o]).val()),
				link:$('#modify-select-link-' + arrIds[o]).val(),
				text:$('#content-' + arrIds[o]).val(),
				css_class:$('#modify-input-css_class-' + arrIds[o]).val(),
				bgcolor:$('#modify-select-bgcolor-' + arrIds[o]).val(),
				links:getCheckedLinksById(arrIds[o])
				});
			}
		$.ajax({
			type: 'POST',
			url: '<?php echo PATH_SERVICE; ?>',
			data: {section:'widget',service:'modify-widget-infos',data:JSON.stringify(arrValues),id:'<?php echo $arrOutput['content']['item-id'];?>'},
			success:function(data){
				//alert(data);
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

function getCheckedLinksById(id){
	var s = '';
	for(var i=0;i<gArrLinksCount;i++){	
		if($('#cb-links-' + id + '-'+ i) && $('#cb-links-' + id + '-'+ i).is(':checked')){
			s += $('#cb-links-' + id + '-'+ i).val() + ',';
			}
		}
	if(s != ''){
		s = s.substring(0,(s.length - 1));
		return s;
		}
	return '';
	}
	
//---------------------------------

function activateSingleItem(id){
	console.log(id);	
	if(parseInt($('#' + id).attr('activated'))){	
		$('#' + id).text('<?php echo formatJavascript(_T('enable')); ?>');
		$('#' + id).removeClass('btn-success');
		$('#' + id).addClass('btn-danger');
		$('#' + id).attr('activated', 0);
	}else{
		$('#' + id).text('<?php echo formatJavascript(_T('disable')); ?>');
		$('#' + id).removeClass('btn-danger');
		$('#' + id).addClass('btn-success');
		$('#' + id).attr('activated', 1);
		}
	};	

	
//---------------------------------

function setHtmlContent(id){
	var tBox = tinymce.get(id + '-html');
	var tInput = $('#' + id);
	if(tInput && tBox){
		tInput.val(tBox.getContent());
		}
	}	
	
//---------------------------------	
//MODIFIED DWIZZEL IMAGE MANAGER
var gListePopUp;
var gImageManager = new ImageManager('media-manager','en');
ImageSelector = {
	update : function(params){
		if(this.field && this.field.value != null){
			this.field.value = params.f_file; //params.f_url
			}
		},
	select: function(textfieldID){
		this.field = document.getElementById(textfieldID);
		gImageManager.popManager(this);	
		}
	};	
	
	
</script>
</body>
<?php require_once(DIR_VIEWS.'append.php'); ?>