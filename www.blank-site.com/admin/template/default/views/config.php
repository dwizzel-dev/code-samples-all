<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default config view

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
	<a name="page-top" id="page-top"></a>
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
	<div class="row-fluid ">
		<div class="span12">
			<form class="form-vertical" id="form-config">
				<?php
				if(isset($arrOutput['content']['config']['defined'][1]) && isset($arrOutput['content']['config']['defined'][2])){
					for($i=0; $i<count($arrOutput['content']['config']['defined'][1]);$i++){
						echo '<div class="control-group inline">';
						echo '<label class="control-label">'.$arrOutput['content']['config']['defined'][1][$i].': <br><small class="muted">'.$arrOutput['content']['config']['defined'][3][$i].'</small></label>';
						echo '<div class="controls"><input type="text" name="'.$arrOutput['content']['config']['defined'][1][$i].'" class="span6" value="'.$arrOutput['content']['config']['defined'][2][$i].'"><input type="hidden" name="'.$arrOutput['content']['config']['defined'][1][$i].'" value="'. str_replace('"','\"',$arrOutput['content']['config']['defined'][3][$i]).'"></div>';
						echo '</div>';
						}
					}
				?>
				<div class="control-group">
					<div class="controls">
						<br><button class="btn btn-primary" id="buttsave-details"><?php echo _T('save changes'); ?></button>
					</div>
				</div>
			</form>	
		</div>
	</div>
	<?php require_once(DIR_VIEWS.'footer.php'); ?>
</div>
<script type="text/javascript">
//
jQuery(document).ready(function($){
	//details form butt
	$('#buttsave-details').click(function(e){
		e.preventDefault();
		showButtLoadingTxt('#buttsave-details', '');
		saveConfig();
		});	
	});
	
//---------------------------------

function saveConfig(){
	//hide les erreurs precedente
	resetForm();
	//on send
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'config',service:'set-config-site',data:JSON.stringify($('#form-config').serializeArray())},
		success:function(data){
			//parse data
			eval('var obj = ' + data + ';');
			if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
				showButtLoadingTxt('#buttsave-details', '<?php echo _T('save changes'); ?>');
				$('#box-alert-msg').html(obj.msgerrors);
				$('#box-alert').show();
				scrollToElement('#page-top');
			}else{
				//pas erreur reload page
				successAlert(1);
				showButtLoadingTxt('#buttsave-details', '<?php echo _T('save changes'); ?>');
				}
			},
		error:function(){
			showButtLoadingTxt('#buttsave-details', '<?php echo _T('save changes'); ?>');
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

function resetForm(){
	successAlert(0);
	}	

//---------------------------------

function successAlert(bOn){
	if(bOn){
		$('#box-success-msg').html('<?php echo _T('modifications are succesfull');?>');
		$('#box-success').show();
		//$(window).scrollTo(150, 500);
		//$(window).scrollTo('#box-success', 500);
		$(window).scrollTo('#page-top', 500);
	}else{
		$('#box-success').hide();
		$('#box-sucess-msg').text();
		}
	}	

//---------------------------------	
function scrollToElement(id){
	$(window).scrollTo(id, 500); 
	};		
	
	
</script>	
</body>
<?php require_once(DIR_VIEWS.'append.php'); ?>