<?php
/**
@auth:	"Dwizzel"
@date:	00-00-0000
@info:	get images from the server for download for the visou.com site
@example: https://mobile....@physiotec.ca/dev/utilities/getimages.php?&img=/var/www/gallery/generic/images/REN19992_A.jpg
*/

//------------------------------------------------------------------------



if(!isset($_GET['img'])){
	exit();
	}

if($_GET['img'].'' == ''){
	exit();
	}

$file = $_GET['img'];

if(file_exists($file) && filesize($file) > 0){
	$angle = 0;
	$arrExif = exif_read_data($file);
	if(isset($arrExif["Orientation"])){
		switch($arrExif["Orientation"]){
			case 3: $angle = 180; break;
			case 6: $angle = 90; break;
			case 8: $angle = 270; break;
			}
		}
	header( "Content-Type: image/jpeg" );
	$im = new imagick($file);
	if($angle > 0){
		$im->rotateImage(new ImagickPixel('#00000000'), $angle);
		}
	echo $im;
	}

//END SCRIPT