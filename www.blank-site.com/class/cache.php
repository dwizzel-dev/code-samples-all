<?php
class Cache {
	
	private $reg;
	private $ext = '.data';
	
	public function __construct($reg){
		$this->reg = $reg;
		}
		
	public function cacheRead($name){
		if(ENABLE_CACHING){
			$file = DIR_CACHE.$name.$this->ext;
			if(file_exists($file)){	
				$fh = @fopen($file, 'r');
				if($fh){
					$content = @fread($fh, filesize($file));
					$arrTmp = unserialize($content);
					@fclose($fh);
					return $arrTmp;
					}
				}
			}
		return false;
		}

	public function cacheWrite($name, $arr){	
		if(ENABLE_CACHING){
			$file = DIR_CACHE.$name.$this->ext;
			$fh = @fopen($file, 'a');
			if($fh){
				@fwrite($fh, serialize($arr));
				@fclose($fh);
				return true;
				}
			}
		return false;
		}		

		
	}

	
	
	
	
//END



	
	