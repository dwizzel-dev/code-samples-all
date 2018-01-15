<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default widget-item carousel view

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
														
							echo '<div class="row-fluid"><div class="span12"><h4 class="pull-left">'._T('image').'</h4><button class="btn pull-right" id="buttremove-items" onclick="removeItems('.$cmptCarouselItems.')"><i class="icon-remove"></i></button></div></div>';
							$strImage = $v['img']; 												
							if($strImage == ''){
								$strImage = DEFAULT_NO_IMAGE;
								}
							echo '<div class="widget-block-div-image"><img class="img-polaroid" src="'.PATH_WEB_MEDIA.$strImage.'"></div>';

							echo '<div class="control-group">';
							echo '<div class="controls input-append"><input id="modal-modify-input-image-'.$cmptCarouselItems.'" type="text" value="'.$v['img'].'" class="input-xlarge"><button type="button" class="btn" onclick="ImageSelector.select(\'modal-modify-input-image-'.$cmptCarouselItems.'\');">'._T('select').'</button></div>';
							echo '</div>';

							//la liste des pages sur lesquelles montre le widget
							echo '<div class="control-group">';
							echo '<label class="control-label">'._T('links where showed').'</label>';
							echo '<div class="controls">';
							//la liste de checkbox qui sera coche ou pas
							echo '<table class="table table-hover">';
							echo '<tbody>';
							$iCmptLinksItems = 0;
							$arrExplodedLinksString = explode(',', $v['links']);
							foreach($arrOutput['content']['links-for-checkbox'] as $k2=>$v2){
								echo '<tr>';
								echo '<td class="small"><input type="checkbox" id="cb-links-'.$cmptCarouselItems.'-'.$iCmptLinksItems.'" value="'.$v2['id'].'" ';
								if(in_array($v2['id'], $arrExplodedLinksString)){
									echo ' checked ';
									}
								echo '></td>';
								echo '<td><small>'.$v2['path'].'</small></td>';
								echo '</tr>';
								$iCmptLinksItems++;
								}	
							echo '</tbody>';	
							echo '</table>';	
							echo '</div>';
							echo '</div>';
							
							echo '<div class="control-group"><label class="control-label">'._T('title').'</label><div class="controls"><input type="text" id="modify-input-title-'.$cmptCarouselItems.'" class="input-xlarge" value="'.$v['title'].'"></div></div>';
							
							if(!isset($v['url'])){
								$v['url'] = '';
								}
							echo '<div class="control-group"><label class="control-label">'._T('url').'</label><div class="controls"><input type="text" id="modify-input-url-'.$cmptCarouselItems.'" class="input-xlarge" value="'.$v['url'].'"></div></div>';
							
							echo '<div class="control-group"><label class="control-label">'._T('content').'</label><div class="controls"><textarea rows="3" id="content-'.$cmptCarouselItems.'-html" class="w96 editable">'.$v['text'].'</textarea></div></div>';
							
							echo '</div>';
							
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
	//editor
	tinymce.init({
		//plugins: 'image table preview media code anchor link lists fullscreen',
		plugins: 'code anchor link lists fullscreen',
		image_advtab: false,
		selector: 'textarea.editable',
		content_css : '<?php echo PATH_WEB_CSS; ?>bootstrap.min.css,<?php echo PATH_WEB_CSS; ?>global.css,http://fonts.googleapis.com/css?family=Maven+Pro:400',
		inline: false,
		height : 150,
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
	code += '<input type="hidden" name="content-' + gCmptItemBlock + '" id="content-' + gCmptItemBlock + '">';
	code += '<div class="row-fluid"><div class="span12"><h4 class="pull-left"><?php echo _T('image'); ?></h4><button class="btn pull-right" id="buttremove-items" onclick="removeItems('+ gCmptItemBlock + ')"><i class="icon-remove"></i></button></div></div>';
	//images	
	code += '<div class="control-group">';
	code += '<div class="controls input-append"><input id="modal-modify-input-image-' + gCmptItemBlock + '" type="text" class="input-xlarge"><button type="button" class="btn" onclick="ImageSelector.select(\'modal-modify-input-image-' + gCmptItemBlock + '\');"><?php echo _T('select'); ?></button></div>';
	code += '</div>';
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
	//le title		
	code += '<div class="control-group"><label class="control-label"><?php echo _T('title'); ?></label><div class="controls"><input type="text" id="modify-input-title-' + gCmptItemBlock + '" class="input-xlarge"></div></div>';
	//le url	
	code += '<div class="control-group"><label class="control-label"><?php echo _T('url'); ?></label><div class="controls"><input type="text" id="modify-input-url-' + gCmptItemBlock + '" class="input-xlarge"></div></div>';
	//le contenu	
	code += '<div class="control-group"><label class="control-label"><?php echo _T('content'); ?></label><div class="controls"><textarea rows="3" id="content-' + gCmptItemBlock + '-html" class="w96 editable"></textarea></div></div>';
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
	
function saveCarousel(){	
	if($('#modify-input-items_values')){
		//active ids
		var arrIds = JSON.parse($('#modify-input-items_values').val());
		var arrValues = [];
		for(var o in arrIds){
			setHtmlContent('content-' + arrIds[o]);
			arrValues.push({
				img:$('#modal-modify-input-image-' + arrIds[o]).val(),
				title:$('#modify-input-title-' + arrIds[o]).val(),
				url:$('#modify-input-url-' + arrIds[o]).val(),
				text:$('#content-' + arrIds[o]).val(),
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