<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	REQUEST
@todo:	
		1. check the $get and $post for hacking or script injection in sql, php, javascript, css




*/


class Request {
		
	private $arr;
	//private $obj;
	private $className = 'Request';
	private $exclusion;
	private $repression;	

	
	//-------------------------------------------------------------------------------------------------------------	
	public function __construct(&$get, &$post){

		/*
		//check for magic quotes and json_decode and json_encode
		if(get_magic_quotes_gpc()){
			$process = array(&$get, &$post);
			while(list($key, $val) = each($process)) {
				foreach ($val as $k => $v){
					unset($process[$key][$k]);
					if (is_array($v)){
						$process[$key][stripslashes($k)] = $v;
						$process[] = &$process[$key][stripslashes($k)];
					}else{
						$process[$key][stripslashes($k)] = stripslashes($v);
						}
					}
				}
			unset($process);
			}
		*/
		//ce que lon veut pas
		$this->exclusion = array(
			'/eval\(/',
			'/echo\(/',
			'/exit\(/',
			'/exec\(/',
			'/exit;/',
			'/system\(/',
			'/popen\(/',
			'/preg_replace\(/',
			'/create_function\(/',	
			'/require_once\(/',	
			'/include\(/',	
			'/include_once\(/',	
			'/require\(/',	
			'/pcntl_exec\(/',	
			'/mysql_execute\(/',	
			'/assert\(/',	
			'/phpinfo\(/',	
			'/getenv\(/',	
			'/passthru\(/',	
			'/\$GET\[/',	
			'/\$POST\[/',	
			'/register_tick_function\(/',	
			'/register_shutdown_function\(/',	
			'/unserialize\(/',	
			'/str_repeat\(/',
			'/print_r\(/'
			);
		//par ce quoi on remplace
		$this->repression = array(
			'_eval(_',
			'_echo(_',
			'_exit(_',
			'_exec(_',
			'_exit;_',
			'_system(_',
			'_popen(_',
			'_preg_replace(_',
			'_create_function(_',
			'_require_once(_',	
			'_include(_',	
			'_include_once(_',	
			'_require(_',	
			'_pcntl_exec(_',	
			'_mysql_execute(_',	
			'_assert(_',	
			'_phpinfo(_',	
			'_getenv(_',	
			'_passthru(_',	
			'_$GET[]_',	
			'_$POST[]_',
			'_register_tick_function(_',	
			'_register_shutdown_function(_',	
			'_unserialize(_',	
			'_str_repeat(_',
			'_print_r(_',	
			);

		//container of all request properties
		$this->arr = array();	
		$this->arr = $this->recursiveBuildArray($get, $this->arr);
		$this->arr = $this->recursiveBuildArray($post, $this->arr);

		//print_r($this->arr);

		}
		
	//-------------------------------------------------------------------------------------------------------------	
	public function clear(){
		$this->arr = array();
		}	

	//-------------------------------------------------------------------------------------------------------------
	private function recursiveBuildArray($postdata, $arr){
		foreach($postdata as $k=>$v){
			if(is_array($v)){
				$arr = recursiveBuildArray($v, $arr);
			}else{
				$v = preg_replace($this->exclusion, $this->repression, $v);
				$arr[$k] = $v;
				}
			}
		return $arr;
		}	

	//-------------------------------------------------------------------------------------------------------------	
	public function getClassName(){
		return $this->className;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function getClassObject(){
		return $this;
		}
	
	//-------------------------------------------------------------------------------------------------------------		
	/*
	public function multipart($file){
		if(count($file) > 0) {
			foreach($file as $k=>$v){
				if(is_array($v)){
					$this->arr[$k] = $v;
				}else{
					eval("\$this->arr['".$k."'] = \"".$v."\";");
					}
				}
			}
		}		
	*/
	//-------------------------------------------------------------------------------------------------------------		
	public function get($key){
		if(isset($this->arr[$key])){
			return $this->arr[$key];
			}
		return false;		
		}
	
	//-------------------------------------------------------------------------------------------------------------		
	public function set($key, $value){
		$this->arr[$key] = $value;
		}

	//-------------------------------------------------------------------------------------------------------------		
	/*
	public function getObject($key){
		if(isset($this->obj[$key])){
			return $this->obj[$key];
			}
		return false;		
		}	
	*/
	//-------------------------------------------------------------------------------------------------------------	
	/*
	public function setFormObject($str){
		$this->obj = json_decode($str, true);
		}	
	*/	
	//-------------------------------------------------------------------------------------------------------------	
	/*
	public function showFormObject(){
		$str = '<br>'.'<b>FORM OBJECT:</b>'.'<br>';
		$str = $this->recursiveShow($this->obj,'<br>', $str);
		$str .= '<br>&nbsp;';	
		return $str;
		}		
	*/
	//-------------------------------------------------------------------------------------------------------------	
	public function getVars(){
		return $this->arr;
		}
		
	//-------------------------------------------------------------------------------------------------------------	
	/*
	public function showRequest(){
		$str = 'REQUEST';
		foreach($this->arr as $k=>$v){
			$str .= '["'.$k.'"] = "'.$v.'"'.'<br>';
			}
		return $str;	
		}
	*/	
	//-------------------------------------------------------------------------------------------------------------	
	public function showRequestAllText(){
		return $this->recursiveShow($this->arr,"\n",'');
		}	
		
	//-------------------------------------------------------------------------------------------------------------	
	/*
	public function showRequestAll(){
		return $this->recursiveShow($this->arr,'<br>','');
		}
	*/		
		
	//-------------------------------------------------------------------------------------------------------------	
	private function recursiveShow($arr, $spacer, $str){
		foreach($arr as $k=>$v){
			if(is_array($v)){
				$str .= $spacer.'['.$k.']';
				$str = $this->recursiveShow($v, $spacer."\t", $str);
			}else{
				$str .= $spacer.'["'.$k.'"] = "'.str_replace(array('<','>'),array('&lt;','&gt;'),$v).'"';
				}
			}
		return $str;	
		}	
		
	}

//END