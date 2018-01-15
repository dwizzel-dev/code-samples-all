<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default datafields view

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
			<div class="input-append pull-left">
				<select id="select-input-table-id">
				<?php
				foreach($arrOutput['content']['field-dropdown'] as $k=>$v){
					echo '<option value="'.$v['id'].'">'.$v['text'].'</option>'.EOL;
					}
				?>
				</select>
				<button class="btn" id="buttshow-items"><?php echo _T('show items listing'); ?></button>
			</div>
			<div class="btn-group pull-right">
				<button class="btn btn-warning" id="buttgenerate"><?php echo _T('generate datafields files'); ?></button>
			</div>
		</div>
	</div>
	<div class="row-fluid ">
		<div class="span12">
			<p><small class="muted"><?php echo _T('select a datafiled from the dropdown above to start editing it'); ?></small></p>
				
			
			<form class="form-vertical" id="form-datafields">
				<h3 id="div-items-main-title"></h3>
				<div class="btn-group pull-left" style="margin-bottom:20px;">
					<button class="btn btn-small hide" id="buttcreate"><?php echo _T('insert new row'); ?></button>
				</div>
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
var gArrNewData = [];
var gDataTableName = '';


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
	$('#' + gDataTableName + '_' + row + '_' + col).css('border', 'solid 1px #bce8f1');;
	//$('#' + gDataTableName + '_' + row + '_' + col).css('background-color', '#d9edf7');
	$('#' + gDataTableName + '_' + row + '_' + col).css('background-color', '#EEF8FD');
	$('#' + gDataTableName + '_' + row + '_' + col).css('color', '#3a87ad');
	}
	
//---------------------------------

function getData(){	
	resetForm();
	var id = $('#select-input-table-id').val();
	if(id != ''){
		$('#buttcreate').hide();
		gArrChangedData = [];
		gArrNewData = [];
		gDataTableName = '';
		$('#div-items-main-title').text('<?php echo formatJavascript(_T('result for: ')); ?>' + id);
		$('#div-items-main').html('<span class="muted"><?php echo formatJavascript(_T('wait!...')); ?></span>');
		$.ajax({
			type: 'POST',
			url: '<?php echo PATH_SERVICE; ?>',
			data: {section:'datafields',service:'get-items-from-table',data:id},
			success:function(data){
				//parse data
				//console.log(data);
				eval('var obj = ' + data + ';');
				if(typeof(obj.msgerrors) !== "undefined" && obj.msgerrors){
					$('#box-alert-msg').html(obj.msgerrors);
					$('#box-alert').show();
					scrollToElement('#page-top');
				}else if(typeof(obj.msgdata) !== "undefined" && obj.msgdata){
					var oData = JSON.parse(obj.msgdata);
					if(typeof(oData.table) !== "undefined" && typeof(oData.columns) !== "undefined" && typeof(oData.rows) !== "undefined"){
						gDataTableName = oData.table;
						var code = '';
						code += '<table class="table table-hover">';
						code += '<thead>';
						code += '<tr>';
						for(var o in oData.columns){
							code += '<th>' + oData.columns[o] + '</th>';
							}
						code += '</tr>';
						code += '</thead>';
						code += '<tbody style="font-size:95%" id="tbody-' + gDataTableName + '">';
						var cmptRows = 0;
						for(var o in oData.rows){
							code += '<tr>';
							var cmptCols = 0;
							for(var p in oData.rows[o]){
								if(p == 'id'){
									code += '<td style="line-height:12px;" id="' + gDataTableName + '_' + cmptRows + '_' + p + '" class="small">' + oData.rows[o][p] + '</td>';
								}else{
									code += '<td style="line-height:12px;" onFocus="tdDataOnFocus(' + cmptRows + ', \'' + p + '\')" id="' + gDataTableName + '_' + cmptRows + '_' + p + '" class="editable" contenteditable="true" spellcheck="false">' + oData.rows[o][p] + '</td>';
									}
								cmptCols++;	
								}
							code += '</tr>';
							cmptRows++;
							}
						code += '</tbody>';
						code += '</table>';
						$('#div-items-main').html(code);
						//create row button
						$('#buttcreate').attr('cmpt', cmptRows);
						$('#buttcreate').attr('cols', oData.columns);
						$('#buttcreate').unbind('click');
						$('#buttcreate').click(function(e){
							e.preventDefault();
							insertNewRow();
							});
						$('#buttcreate').show();
						}
					}
				showButtLoadingTxt('#buttshow-items', '<?php echo _T('show items listing'); ?>');
				},
			error:function(){
				showButtLoadingTxt('#buttshow-items', '<?php echo _T('show items listing'); ?>');
				}	
			});
	}else{
		$('#box-alert-msg').html('select a datafields from the dropdown below');
		$('#box-alert').show();
		scrollToElement('#page-top');
		showButtLoadingTxt('#buttshow-items', '<?php echo _T('show items listing'); ?>');
		}
	
	
	}
	
//---------------------------------

function insertNewRow(){
	var cmpt = $('#buttcreate').attr('cmpt');
	var cols = $('#buttcreate').attr('cols').split(',');
	var code = '<tr id="table-bottom">';
	var arrTmp = [];
	for(var o in cols){
		if(cols[o] == 'id'){
			code += '<td style="line-height:12px;" id="' + gDataTableName + '_' + cmpt + '_' + cols[o] + '">new</td>';
		}else{
			code += '<td style="line-height:12px;" onFocus="tdDataOnFocus(' + cmpt + ', \'' + cols[o] + '\')" id="' + gDataTableName + '_' + cmpt + '_' + cols[o] + '" class="editable" contenteditable="true" spellcheck="false"></td>';
			}
		arrTmp.push(cols[o]);	
		}
	code += '</tr>';	
	$('#tbody-' + gDataTableName).append(code);
	gArrNewData.push([cmpt, arrTmp]);	
	cmpt++;		
	$('#buttcreate').attr('cmpt', cmpt);	
	scrollToElement('#table-bottom');
	}
	
	
//---------------------------------

function saveData(){
	//we build the data depending on the one that has changed form: [[2,"name"],[3,"name"],[4,"name"],[4,"name"]] where [[row,col]] 
	var arr = [];
	var arrNew = [];
	for(var o in gArrNewData){
		var arrKeys = [];
		var arrValues = [];
		for(var p in gArrNewData[o][1]){
			if(gArrNewData[o][1][p] != 'id'){
				arrValues.push($('#' +  gDataTableName + '_' + gArrNewData[o][0] + '_' + gArrNewData[o][1][p]).text());
				arrKeys.push(gArrNewData[o][1][p]);
				}
			}
		arrNew.push({row:gArrNewData[o][0],keys:arrKeys,values:arrValues});	
		}
	for(var o in gArrChangedData){
		arr.push({
			id: $('#' +  gDataTableName + '_' + gArrChangedData[o][0] + '_id').text(),
			field: gArrChangedData[o][1],
			data: $('#' + gDataTableName + '_' + gArrChangedData[o][0] + '_' + gArrChangedData[o][1]).text()
			});
		}
	//hide les erreurs precedente
	resetForm();
	//on send
	$.ajax({
		type: 'POST',
		url: '<?php echo PATH_SERVICE; ?>',
		data: {section:'datafields',service:'set-datafields-infos',data:JSON.stringify(arr),insert:JSON.stringify(arrNew),table:gDataTableName},
		success:function(data){
			//alert(data);
			//reset le arr des new 
			gArrNewData = [];
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
			//reception des nouveaux ID des fields nouvellement cree	
			if(typeof(obj.msgdata) !== "undefined" && obj.msgdata){
				for(var o in obj.msgdata){
					$('#' +  gDataTableName + '_' + obj.msgdata[o].row + '_id').text(obj.msgdata[o].id);
					}
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
		data: {section:'datafields',service:'generate-datafields-infos'},
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
			showButtLoadingTxt('#buttgenerate', '<?php echo _T('generate datafields files'); ?>');	
			},
		error:function(){
			showButtLoadingTxt('#buttgenerate', '<?php echo _T('generate datafields files'); ?>');	
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