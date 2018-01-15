<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default page view

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
	<?php require_once(DIR_VIEWS.'header.php');?>
	<div class="row-fluid thick-border-t thick-border-b">
		<div class="span12">
			<h1><?php echo $arrOutput['content']['title'];?></h1>
			<?php echo $arrOutput['content']['text'];?>
		</div>
	</div>	
	<div class="row-fluid <?php echo ($arrOutput['content']['error'] != '')? 'show' : 'hide';  ?>" id="box-alert">
		<div class="span12 inline">
			<div class="alert alert-error">
				<h4><?php echo _T('errors'); ?></h4>
				<p id="box-alert-msg"><?php echo $arrOutput['content']['error']; ?></p>
			</div>
		</div>
	</div>	
	<div class="row-fluid ">
		<div class="span12">
			<form class="form-vertical" id="form-login">
				<div class="control-group">
					<label class="control-label"><?php echo _T('user')?></label>
					<div class="controls"><input type="text" id="input-user" name="user" class="span3" value=""></div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php echo _T('psw')?></label>
					<div class="controls"><input type="password" id="input-psw" name="psw"  class="span3" value=""></div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php echo $arrOutput['content']['captcha']; ?> : <?php echo sprintf(_T('%senter numeric code in input below%s'), '<small>', '</small>')?></label>
					<div class="controls"><input type="text" id="input-captcha" name="captcha"  class="span3" value=""></div>
				</div>
				<div class="control-group">
					<div class="controls">
						<br><button class="btn btn-primary" id="butt-login"><?php echo _T('login'); ?></button>
					</div>
				</div>
			</form>
		</div>
	</div>	
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

<script type="text/javascript">
//
jQuery(document).ready(function($){
	//details form butt
	$('#butt-login').click(function(e){
		e.preventDefault();
		showButtLoadingTxt('#butt-login', '');
		doLogin();
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

function resetForm(){
	successAlert(0);
	$('#box-alert').hide();
	$('#box-alert-msg').text();
	var arr = [['user', 'val'], ['psw', 'val'], ['captcha', 'val']];
	for(var o in arr){
		$('#input-' + arr[o][0]).css('border-color', '');
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


function doLogin(){
	//hide les erreurs precedente
	resetForm();
	//on send
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'login',service:'do-login',data:JSON.stringify($('#form-login').serializeArray())},
		success:function(data){
			//parse data
			//alert(data);
			eval('var obj = ' + data + ';');
			if(typeof(obj.formerrors) !== "undefined" && obj.formerrors){
				for(var o in obj.formerrors){
					if($('#input-' + obj.formerrors[o])){
						$('#input-' + obj.formerrors[o]).css('border-color', '#b94a48');
						}
					}
				}
			if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
				showButtLoadingTxt('#butt-login', '<?php echo _T('login'); ?>');
				$('#box-alert-msg').html(obj.msgerrors);
				$('#box-alert').show();
				$(window).scrollTo(150, 500);
			}else{
				//pas erreur reload page
				showButtLoadingTxt('#butt-login', '<?php echo _T('reloading page...'); ?>');
				location.reload();
				}
			},
		error:function(){
			showButtLoadingTxt('#butt-login', '<?php echo _T('login'); ?>');
			}	
		});
	}	



</script>


</body>
<?php require_once(DIR_VIEWS.'append.php'); ?>