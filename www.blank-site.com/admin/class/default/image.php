<?php
class Image {
	private $reg;

	public function __construct($reg) {
		$this->reg = $reg;
		}
	
	//------------------------------------------------------------------------------------------------		
		
	public function createCaptcha($str){		
		ob_start();
		$im = imagecreatetruecolor(45, 27);
		imagestring(imagecolorallocate($im, 255, 255, 255), 4, 5, 5,  $str, $text_color);
		header('Content-Type: image/jpeg');
		imagejpeg($im);
		imagedestroy($im);
		$out = ob_get_clean();
		//return "<img src='data:image/jpeg;base64," . base64_encode($out)."'>";
		return base64_encode($out);
		}
	
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>