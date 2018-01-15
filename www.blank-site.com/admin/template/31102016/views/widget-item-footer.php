<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default widget-item footer view

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
						//						
						foreach($arrOutput['content']['item']['details']['data-unserialize'] as $k=>$v){
							//la string 
							$strItemsValues .= $cmptCarouselItems.',';
							//le div de items
							echo '<div id="div-main-items-'.$cmptCarouselItems.'" class="widget-block">';
													
							echo '<input type="hidden" name="content-'.$cmptCarouselItems.'" id="content-'.$cmptCarouselItems.'" value="'.$v['text'].'">';
										
							//ouvrir le contenu
							echo '<div class="opener-butt"><div onclick="expandWidgetRow('.$cmptCarouselItems.');"><input type="text" value="'.$v['name'].'" placeholder="default name" id="modify-input-name-'.$cmptCarouselItems.'">';
							
							//supprimer et activer
							echo '<div class="input-append pull-right">';
							echo '<input type="text" value="'.$v['position'].'" class="form-control input-mini" placeholder="position" id="modify-input-position-'.$cmptCarouselItems.'">';
							if(!intVal($v['active'])){
								echo '<button class="btn btn-danger" id="butt-activation-'.$cmptCarouselItems.'" activated="0">'._T('enable').'</button>';
							}else{
								echo '<button class="btn btn-success" id="butt-activation-'.$cmptCarouselItems.'" activated="1">'._T('disable').'</button>';
								}
							echo '<button class="btn" onclick="removeItems('.$cmptCarouselItems.')"><i class="icon-remove"></i></button>';
							echo '</div>';
							//
							echo '</div></div>';//close the ouvrir le contenu
								
							//content
							echo '<div class="control-group"><label class="control-label"><h3>'._T('content').'</h3></label><div class="controls"><textarea rows="8" id="content-'.$cmptCarouselItems.'-html" class="w96 editable" uid="'.$cmptCarouselItems.'">'.$v['text'].'</textarea></div></div>';
							
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
function getHighestPosition(){
	var iPos = 0;	
	if($('#modify-input-items_values')){
		//active ids
		var arrIds = JSON.parse($('#modify-input-items_values').val());
		//on classe par position avant d'envoyer
		for(var o in arrIds){
			var iTmpPos = parseInt($('#modify-input-position-' + arrIds[o]).val());
			if(iTmpPos > iPos){
				iPos = iTmpPos;
				}
			}
		iPos++;
		}
	return iPos;
	}
	
//---------------------------------

function insertItems(){
	gCmptItemBlock++;
	var arrIds = JSON.parse($('#modify-input-items_values').val());
	arrIds.push(gCmptItemBlock);
	$('#modify-input-items_values').val(JSON.stringify(arrIds));
	var code = '';
	code += '<div id="div-main-items-' + gCmptItemBlock + '" class="widget-block opened">';
	code += '<input type="hidden" name="content-' + gCmptItemBlock + '" id="content-' + gCmptItemBlock + '">';
	//open
	code += '<div class="opener-butt"><div onclick="expandWidgetRow(' + gCmptItemBlock + ');"><input type="text" value="" placeholder="default name" id="modify-input-name-' + gCmptItemBlock + '"></div></div>';
	//
	code += '<div class="input-append pull-right"><input type="text" value="' + getHighestPosition() + '" class="form-control input-mini" placeholder="<?php echo _T('position'); ?>" id="modify-input-position-' + gCmptItemBlock + '"><button class="btn btn-danger" id="butt-activation-' + gCmptItemBlock + '" activated="0"><?php echo _T('enable'); ?></button><button class="btn" onclick="removeItems(' + gCmptItemBlock + ')"><i class="icon-remove"></i></button></div>';	
	//le contenu	
	code += '<div class="control-group"><label class="control-label"><h3><?php echo _T('content'); ?></h3></label><div class="controls"><textarea rows="8" id="content-' + gCmptItemBlock + '-html" class="w96 editable"></textarea></div></div>';
	code += '</div>';	
	$('#div-items-main').append(code);
	//on rajoute le handle su click	
	$('#butt-activation-' + gCmptItemBlock).click(function(e){
		e.preventDefault();
		activateSingleItem(this.id);
		});
	//	
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
			'<?php echo PATH_CSS; ?>editor/widget-footer-item.css'
			],
		inline: false,
		height : 250,
		//preview plugin
		plugin_preview_type: 'widget-footer-item', //le type de widget pour le preview
		};	
	//editor
	//with MOXIECUT image editor
	tinymce.PluginManager.load('moxiecut', '<?php echo PATH_WEB; ?>js/editor.with-moxiecut/plugins/moxiecut/plugin.min.js');
	tinymce.init(obj);
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
		var arrPosition = [];
		//on classe par position avant d'envoyer
		var bFoundDuplicatePosition	= false;
		for(var o in arrIds){
			//check si a la meme position sinon va overwrite 
			if(typeof(arrPosition[parseInt($('#modify-input-position-' + arrIds[o]).val())]) != 'undefined'){
				//raise alert
				bFoundDuplicatePosition = true;
				break;
				}
			arrPosition[parseInt($('#modify-input-position-' + arrIds[o]).val())] = arrIds[o];
			}
	
		//on break car il y a un duplicate
		if(bFoundDuplicatePosition){
			$('#modal-alert-title').text('<?php echo formatJavascript(_T('error')); ?>');
			$('#modal-alert-content').html('<?php echo formatJavascript(_T('2 or more items have the same position number')); ?>');
			$('#modal-alert-close').text('<?php echo formatJavascript(_T('close')); ?>');
			$('#modal-alert').modal('show');
			showButtLoadingTxt('#buttsave-items', '<?php echo _T('save changes'); ?>');
			return;
			}

		for(var o in arrPosition){
			//seulement si on utilise de html editor	
			setHtmlContent('content-' + arrPosition[o]);
			arrValues.push({
				name:cleanUrlText($('#modify-input-name-' + arrPosition[o]).val()),
				position:$('#modify-input-position-' + arrPosition[o]).val(),
				active:parseInt($('#butt-activation-' + arrPosition[o]).attr('activated')),
				//seulement si on utilise de html editor	
				text:$('#content-' + arrPosition[o]).val(),
				//text:$('#content-' + arrPosition[o] + '-html').val(),
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