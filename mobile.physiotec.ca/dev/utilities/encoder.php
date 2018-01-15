<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	url builder and encoder for &data that are sent to the service.php

*/

//errors ------------------------------------------------------------------

Header('Content-Type: text/html; charset=utf-8');


error_reporting(E_ERROR);


//vars   ------------------------------------------------------------------

$bBuildPath = false;
$baseWebPath = 'http://mobile....@physiotec.ca/dev/service.php?';
$buildWebPath = '';
$arrInput = array(
	array(
		'type' => 'text',
		'placeholder' => 'sessid',
		'name' => 'sessid',
		'url' => '&PHPSESSID',
		'encode' => false,
		'value' => '',
		'enabled' => true,
		),
	array(
		'type' => 'text',
		'placeholder' => 'pid',
		'name' => 'pid',
		'url' => '&pid',
		'encode' => false,
		'value' => '',
		'enabled' => true,
		),
	array(
		'type' => 'text',
		'placeholder' => 'timestamp',
		'name' => 'timestamp',
		'url' => '&time',
		'encode' => false,
		'value' => '',
		'enabled' => true,
		),	
	array(
		'type' => 'text',
		'placeholder' => 'section',
		'name' => 'section',
		'url' => '&section',	
		'encode' => false,
		'value' => '',
		'enabled' => true,
		),	
	array(
		'type' => 'text',
		'placeholder' => 'service',
		'name' => 'service',
		'url' => '&service',
		'encode' => false,
		'value' => '',
		'enabled' => true,
		),	
	array(
		'type' => 'textarea',
		'placeholder' => 'data',
		'name' => 'data',
		'url' => '&data',	
		'encode' => array(
			'type' => 'textarea',
			'placeholder' => 'encoded data',
			'name' => 'encoded-data',
			'url' => '&data',	
			'encode' => false,
			'value' => '',
			'enabled' => false,
			),
		'value' => '',
		'enabled' => true,
		),
	);


//functions ---------------------------------------------------------------

function getInput($name){
	if(isset($_GET[$name])){
		if($_GET[$name].'' != ''){
			return $_GET[$name];
			}
		}
	if(isset($_POST[$name])){
		if($_POST[$name].'' != ''){
			return $_POST[$name];
			}
		}	
	return '';
	}

function encode($str){
	return urlencode($str);	
	}


//process ----------------------------------------------------------------

//check if we ahave a return from a form
foreach($arrInput as $k=>$v){
	//val of input	
	$str = getInput($v['name']);
	if($str != ''){
		$bBuildPath = true;
		}
	//base values from form	
	$arrInput[$k]['value'] = $str;
	//check if need encoding
	if(is_array($v['encode'])){
		$arrInput[$k]['encode']['value'] = encode(getInput($v['name']));
		}		
	}

//check if we have info to build the web link
if($bBuildPath){
	$buildWebPath = $baseWebPath;
	foreach($arrInput as $k=>$v){
		if(is_array($v['encode'])){ //encoed data instead
			$buildWebPath .= $v['encode']['url'].'='.$v['encode']['value'];
		}else{
			if($v['name'] == 'sessid'){
				if(strlen($v['value']) == 32){
					$buildWebPath .= $v['url'].'='.$v['value'];
					}
			}else{	
				$buildWebPath .= $v['url'].'='.$v['value'];
				}
			}
		}
	}



?>
<html>    
<head>
<meta charset="utf-8">
<title>URL builder/encoder</title>
<style>
img{
	border:0;
	}
	
INPUT[type=TEXT],TEXTAREA,SELECT,INPUT[type=PASSWORD] {
	font-family:Arial;
	font-size:100%;
	border:1px solid black;
	padding: 10px;
	margin: 10px;
	width:640px;
	border-radius:5px;
	}	
TEXTAREA{
	height:150px;
	}

html, body {
	text-align:center;
	padding: 0;
	margin: 20px;
	position: relative;
	font-family:Arial;
	font-size:16px;
	font-weight:normal;
	background-color: #ccc;
	color:#000;
	zoom: 1;
	}
H1{
	font-size: 125%;
	}

BUTTON{
	font-family:Arial;
	font-size:100%;
	border:1px solid black;
	padding: 10px 50px;
	margin: 10px 5px;
	border-radius:5px;
	background-color:#333;
	color:#fff;
	}
#webpath{	
	border:1px solid black;
	padding: 10px 50px;
	margin: 10px 5px;
	border-radius:5px;
	background-color:#000;
	display:block;
	color:#fff;
	}
A{
	text-decoration:none;
	font-size:100%;
	color:#fff;
	}

</style>
<script>
function process(){
	var frm = document.getElementsByName('form1');
	if(typeof(frm[0]) == 'object'){
		frm[0].submit();
		}
	}
function clearForm(){
	var frm = document.getElementsByName('form1');
	if(typeof(frm[0]) == 'object'){
		for(var i=0;i<6;i++){
			frm[0][i].value = '';
			}
		}
	}
</script>
</head>
<body>
<h1>URL builder/encoder</h1>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form1">
<?php

//si un webpath
if($bBuildPath){
	echo '<div id="webpath"><a href="'.$buildWebPath.'" target="_new">'.htmlentities($buildWebPath).'</a></div>';
	}

$strOutput = '';
foreach($arrInput as $k=>$v){
	if($v['type'] == 'text'){ //text input
		$strOutput .= '<input type="'.$v['type'].'" value="'.$v['value'].'" name="'.$v['name'].'" placeholder="'.$v['placeholder'].'"><br>';
	}else if($v['type'] == 'textarea'){ //textarea input
		$strOutput .= '<textarea name="'.$v['name'].'" placeholder="'.$v['placeholder'].'"';
		if(!$v['enabled']){ //check if disabled
			$strOutput .= ' disabled ';
			}
		$strOutput .= '>'.$v['value'].'</textarea><br>';
		//check if encoded and show it in a new ref input
		if(is_array($v['encode'])){ 
			if($v['encode']['value'] != ''){
				$strOutput .= '<textarea name="'.$v['encode']['name'].'" placeholder="'.$v['encode']['placeholder'].'"';
				if(!$v['encode']['enabled']){ //check if disabled
					$strOutput .= ' disabled ';
					}
				$strOutput .= '>'.$v['encode']['value'].'</textarea><br>';
				}
			}
		}
	}
echo $strOutput;

?>
</form>
<button onclick="clearForm();">clear</button>
<button onclick="process();">build</button>

</body>
<html>
