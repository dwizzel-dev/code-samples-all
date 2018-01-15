<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default translation view

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
		<div class="span12 inline">
			<div class="input-append pull-left" style="margin-right:20px;">
				<select id="select-input-table-id">
				<?php
				echo '<option value="">'._T('--').'</option>'.EOL;
				foreach($arrOutput['content']['page-dropdown'] as $k=>$v){
					echo '<option value="'.$v['page'].'">'.$v['page'].'</option>'.EOL;
					}
				?>
				</select>
				<button class="btn" id="buttshow-items"><?php echo _T('show items listing'); ?></button>
			</div>
			<div class="input-append pull-left">
				<input id="filter-searchitems" type="text" value="">
				<button class="btn" type="button" id="buttsearchitems"><i class="icon-search"></i></button>
			</div>
			<div class="btn-group pull-right">
				<button class="btn btn-warning" id="buttgenerate"><?php echo _T('generate translation files'); ?></button>
			</div>
		</div>
	</div>
	<div class="row-fluid ">
		<div class="span12">
			<p><small class="muted"><?php echo _T('select a page from the dropdown above to start editing translation'); ?></small></p>
			<form class="form-vertical" id="form-translation">
				<h3 id="div-items-main-title"></h3>
				<div id="div-items-main" class="control-group"></div>
				<div class="control-group">
					<div class="controls">
						<br><button class="btn btn-primary" id="buttsave-details"><?php echo _T('save changes'); ?></button>
						<small class="muted" style="margin-left:10px;"><?php echo _T('hilighted field will be saved'); ?></small>
					</div>
				</div>
			</form>	
		</div>
	</div>
	<a name="page-bottom" id="page-bottom"></a>
	<?php require_once(DIR_VIEWS.'footer.php'); ?>
</div>
<script type="text/javascript">

//global holder for id that has changed
var gArrChangedData = [];

//
jQuery(document).ready(function($){
	//details form butt
	$('#buttsave-details').click(function(e){
		e.preventDefault();
		showButtLoadingTxt('#buttsave-details', '');
		saveData();
		});	
	$('#buttshow-items').click(function(e){
		e.preventDefault();
		showButtLoadingTxt('#buttshow-items', '');
		getData();
		});	
	$('#buttgenerate').click(function(e){
		e.preventDefault();
		showButtLoadingTxt('#buttgenerate', '');
		generateData();
		});	
	$('#buttsearchitems').click(function(e){
		e.preventDefault();
		showButtLoadingTxt('#buttsearchitems', '');
		searchData();
		});		
	tinymce.init({
		selector: "td.editable",
		inline: true,
		/*toolbar: "undo redo",*/
		menubar: false,
		});
	});
	
//---------------------------------

function tdDataOnFocus(row, col){
	gArrChangedData.push([row,col]);
	$('#' + row + '_' + col).css('border', 'solid 1px #bce8f1');;
	//$('#' + row + '_' + col).css('background-color', '#d9edf7');
	$('#' + row + '_' + col).css('background-color', '#EEF8FD');
	$('#' + row + '_' + col).css('color', '#3a87ad');
	}
	
//---------------------------------

function getData(){	
	resetForm();
	var id = $('#select-input-table-id').val();
	gArrChangedData = [];
	$('#div-items-main-title').text('<?php echo formatJavascript(_T('result for: ')); ?>' + id);
	$('#div-items-main').html('<span class="muted"><?php echo formatJavascript(_T('wait!...')); ?></span>');
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'translation',service:'get-items-from-table',data:id},
		success:function(data){
			//parse data
			eval('var obj = ' + data + ';');
			if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
				$('#box-alert-msg').html(obj.msgerrors);
				$('#box-alert').show();
				scrollToElement('#page-top');
			}else if(typeof(obj.msgdata) !== "undefined" && obj.msgdata){
				var oData = JSON.parse(obj.msgdata);
				if(typeof(oData.columns) !== "undefined" && typeof(oData.rows) !== "undefined"){
					showRows(oData);
					}
				}
			showButtLoadingTxt('#buttshow-items', '<?php echo _T('show items listing'); ?>');
			},
		error:function(){
			showButtLoadingTxt('#buttshow-items', '<?php echo _T('show items listing'); ?>');
			}	
		});
	}
	
	
//---------------------------------

function searchData(){	
	resetForm();
	$('#select-input-table-id').val('');
	var str = $('#filter-searchitems').val();
	gArrChangedData = [];
	//on reset les trucs
	$('#div-items-main-title').text('<?php echo formatJavascript(_T('result for: ')); ?>' + str);
	$('#div-items-main').html('<span class="muted"><?php echo formatJavascript(_T('wait!...')); ?></span>');
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'translation',service:'get-items-filtered-by-string',data:str},
		success:function(data){
			//parse data
			eval('var obj = ' + data + ';');
			if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
				$('#box-alert-msg').html(obj.msgerrors);
				$('#box-alert').show();
				$('#div-items-main').html('<span class="muted"><?php echo formatJavascript(_T('no result!...')); ?></span>');
				scrollToElement('#page-top');
			}else if(typeof(obj.msgdata) !== "undefined" && obj.msgdata){
				var oData = JSON.parse(obj.msgdata);
				if(typeof(oData.columns) !== "undefined" && typeof(oData.rows) !== "undefined"){
					showRows(oData);
					}
				}
			$('#buttsearchitems').html('<i class="icon-search"></i>');
			},
		error:function(){
			$('#buttsearchitems').html('<i class="icon-search"></i>');
			}	
		});
	}	
	
//---------------------------------

function showRows(oData){
	var code = '';
	console.log(JSON.stringify(oData));
	if(oData){
		code += '<table class="table table-hover">';
		code += '<thead>';
		code += '<tr>';
		for(var o in oData.columns){
			code += '<th>' + oData.columns[o] + '</th>';
			}
		code += '</tr>';
		code += '</thead>';
		code += '<tbody style="font-size:95%" id="tbody-translation">';
		var cmptRows = 0;
		for(var o in oData.rows){
			code += '<tr>';
			var cmptCols = 0;
			for(var p in oData.rows[o]){
				if(p == 'id' || p == 'key' ){
					code += '<td id="' + cmptRows + '_' + p + '"';
					if(p == 'id'){	
						code += ' class="small" ';
						}
					code += '>' + oData.rows[o][p] + '</td>';
				}else{
					code += '<td onFocus="tdDataOnFocus(' + cmptRows + ', \'' + p + '\')" id="' + cmptRows + '_' + p + '" class="editable" contenteditable="true" spellcheck="false">' + oData.rows[o][p] + '</td>';
					}
				cmptCols++;	
				}
			code += '</tr>';
			cmptRows++;
			}
		code += '</tbody>';
		code += '</table>';
	}else{
		code = '<span class="muted"><?php echo formatJavascript(_T('no result!...')); ?></span>';
		}
	$('#div-items-main').html(code);
	}

//---------------------------------

function saveData(){
	//hide les erreurs precedente
	resetForm();
	//we build the data depending on the one that has changed form: [[2,"name"],[3,"name"],[4,"name"],[4,"name"]] where [[row,col]] 
	var arr = [];
	for(var o in gArrChangedData){
		arr.push({
			id: $('#' + gArrChangedData[o][0] + '_id').text(),
			field: gArrChangedData[o][1],
			data: $('#' + gArrChangedData[o][0] + '_' + gArrChangedData[o][1]).text()
			});
		}
	//on send
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'translation',service:'set-translation-infos',data:JSON.stringify(arr)},
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

function generateData(){
	//hide les erreurs precedente
	resetForm();
	//on send
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'translation',service:'generate-translation-infos'},
		success:function(data){
			//parse data
			eval('var obj = ' + data + ';');
			if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
				$('#box-alert-msg').html(obj.msgerrors);
				$('#box-alert').show();
				scrollToElement('#page-top');
			}else{
				//pas erreur reload page
				successAlert(1);
				}
			showButtLoadingTxt('#buttgenerate', '<?php echo _T('generate translation files'); ?>');	
			},
		error:function(){
			showButtLoadingTxt('#buttgenerate', '<?php echo _T('generate translation files'); ?>');	
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
	$('#box-alert').hide();
	$('#box-alert-msg').text();
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