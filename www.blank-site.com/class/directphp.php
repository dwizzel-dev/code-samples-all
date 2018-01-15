<?php

class DirectPHP {
		
	private $block_list = 'system,exec,mysql_execute';
	
	function __construct(){
		}

	function onContent($text){
		$this->block_list = preg_replace('/\s*/s', '', $this->block_list);
		$this->block_list = explode(',', $this->block_list);

		$php_start = "<?php";
		$php_end = "?>";
		$contents = $text;
		$contents = $this->fix_str($contents);
		$output = "";
		$regexp = '/(.*?)'.$this->fix_reg($php_start).'\s+(.*?)'.$this->fix_reg($php_end).'(.*)/s';
		$found = preg_match($regexp, $contents, $matches);
		while($found){
			$output .= $matches[1];
			$phpcode = $matches[2];
			if($this->check_php($phpcode)){
				ob_start();
				eval($this->fix_str2($phpcode));
				$output .= ob_get_contents();
				ob_end_clean();
			}else{
				$output .= "The following command is not allowed: <b>$errmsg</b>";
				}
			$contents = $matches[3];
			$found = preg_match($regexp, $contents, $matches);
			}
		$output .= $contents;
		return $output;
		}

	function fix_str($str){
		$str = str_replace('{?php', '<?php', $str);
		$str = str_replace('?}', '?>', $str);
		$str = preg_replace(array('%&lt;\?php(\s|&nbsp;|<br\s/>|<br>|<p>|</p>)%s', '/\?&gt;/s', '/-&gt;/'), array('<?php ', '?>', '->'), $str);
		return $str;
		}

	function fix_str2($str){
		$str = str_replace('<br>', "\n", $str);
		$str = str_replace('<br />', "\n", $str);
		$str = str_replace('<p>', "\n", $str);
		$str = str_replace('</p>', "\n", $str);
		$str = str_replace('&#39;', "'", $str);
		$str = str_replace('&quot;', '"', $str);
		$str = str_replace('&lt;', '<', $str);
		$str = str_replace('&gt;', '>', $str);
		$str = str_replace('&amp;', '&', $str);
		$str = str_replace('&nbsp;', ' ', $str);
		$str = str_replace('&#160;', "\t", $str);
		$str = str_replace(chr(hexdec('C2')).chr(hexdec('A0')), '', $str);
		$str = str_replace(html_entity_decode("&Acirc;&nbsp;"), '', $str);
		return $str;
		}

	function fix_reg($str){
		$str = str_replace('?', '\?', $str);
		$str = str_replace('{', '\{', $str);
		$str = str_replace('}', '\}', $str);
		return $str;
		}

	function check_php($code){
		$status = 1;
		$function_list = array();
		if(preg_match_all('/([a-zA-Z0-9_]+)\s*[(|"|\']/s', $code, $matches)){
			$function_list = $matches[1];
			}

		if(preg_match('/`(.*?)`/s', $code)){
			$status = 0;
			return $status;
			}
		if(preg_match('/\$database\s*->\s*([a-zA-Z0-9_]+)\s*[(|"|\']/s', $code, $matches)){
			$status = 0;
			return $status;
			}
		foreach($function_list as $command){
			if (in_array($command, $this->block_list)){
				$status = 0;
				break;
				}
			}
		return $status;
		}
	}


//END CLASS
