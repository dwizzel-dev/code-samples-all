<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default users-items view

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
	<div class="row-fluid ">
		<div class="span12">
			<ul class="nav nav-pills">
				<li><?php echo '<a href="#details" onclick="scrollToElement(\'#details\')">'._T('details').'</a>'; ?></li>
				<li><?php echo '<a href="#iplisting" onclick="scrollToElement(\'#iplisting\')">'._T('ip listing').'</a>'; ?></li>
			</ul>
			<form class="form-vertical" id="form-item">
				<input type="hidden" name="id" value="<?php echo $arrOutput['content']['item-id'];?>">
				<div class="control-group" style="margin-bottom:20px;">
					<fieldset><legend><?php echo _T('informations');?></legend>
						<div><?php echo _T('#id')?>: <?php echo $arrOutput['content']['item']['details']['id']; ?></div>
						<div><?php echo _T('date added')?>: <?php echo $arrOutput['content']['item']['details']['date_added']; ?></div>
						<div><?php echo _T('date modified')?>: <?php echo $arrOutput['content']['item']['details']['date_modified']; ?></div>
					</fieldset>
				</div>
				<div class="control-group" >
					<a name="details" id="details"></a>
					<h3><?php echo _T('details'); ?><a onclick="scrollToElement('#page-top');" style="margin-left:20px;opacity:0.3;" href="#page-top"><img src="<?php echo PATH_IMAGE; ?>glyphicons/glyphicons_218_circle_arrow_top.png"></a></h3>
				</div>
				<div class="control-group">
					<label class="control-label"><?php echo _T('email')?> :</label>
					<div class="controls"><input type="text" id="modify-input-username" name="username" class="input-large" value="<?php echo strtolower($arrOutput['content']['item']['details']['username']); ?>"></div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php echo _T('firstname')?> :</label>
					<div class="controls"><input type="text" id="modify-input-firstname" name="firstname" class="input-large" value="<?php echo mb_strtolower($arrOutput['content']['item']['details']['firstname'], 'UTF-8'); ?>"></div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php echo _T('lastname')?> :</label>
					<div class="controls"><input type="text" id="modify-input-lastname" name="lastname" class="input-large" value="<?php echo mb_strtolower($arrOutput['content']['item']['details']['lastname'], 'UTF-8'); ?>"></div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php echo _T('password')?> :</label>
					<div class="controls"><input type="text" id="modify-input-password" name="password" class="input-large" value="<?php echo $arrOutput['content']['item']['details']['password']; ?>"></div>
				</div>
				<div class="control-group" >
					<a name="iplisting" id="iplisting"></a>
					<h3><?php echo _T('IPs listing'); ?><a onclick="scrollToElement('#page-top');" style="margin-left:20px;opacity:0.3;" href="#page-top"><img src="<?php echo PATH_IMAGE; ?>glyphicons/glyphicons_218_circle_arrow_top.png"></a></h3>
				</div>
				<?php
				//les ips
				if(isset($arrOutput['content']['item']['details']['ips']) && is_array($arrOutput['content']['item']['details']['ips'])){
					$strIps = '';
					foreach($arrOutput['content']['item']['details']['ips'] as $k=>$v){
						$strIps .= $v['date_added'].'   /   '.$v['ip'].EOL;
						}
					?>		
					<div class="control-group">
					<div class="controls"><textarea rows="10" id="modify-input-ips" name="ips" class="span6"><?php echo $strIps; ?></textarea></div>
					</div>
					<?php
				}else{
					echo '<div>no listing</div>';
					}
				?>
				<!--<div class="control-group">
					<div class="controls">
						<br><button class="btn btn-primary" id="buttsave-details"><?php echo _T('save changes'); ?></button>
					</div>
				</div>-->
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
//
jQuery(document).ready(function($){
	//delete items
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
	});
	
//---------------------------------

function disableItem(){
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'users',service:'disable-users-infos',data:JSON.stringify($('#form-process').serializeArray())},
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
		data: {section:'users',service:'enable-users-infos',data:JSON.stringify($('#form-process').serializeArray())},
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

function showButtLoadingTxt(strButt, strText){
	if(strText == ''){
		$(strButt).text('<?php echo formatJavascript(_T('wait!...')); ?>');
	}else{
		$(strButt).text(strText);
		}
	}	
	

//---------------------------------	
function scrollToElement(id){
	$(window).scrollTo(id, 500); 
	};		
	
	
</script>	
</body>
<?php require_once(DIR_VIEWS.'append.php'); ?>