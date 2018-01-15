<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	RESPONSE

*/
class Response {
		
	private $arr;
	private $json;
	private $error;
	private $tab = "\t";
	private $eol = "\n";
	private $className = 'Response';	
	
	//-------------------------------------------------------------------------------------------------------------	
	public function __construct($json){
		$this->arr = array();
		$this->json = $json;
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
	public function puts($data){
		if(is_array($data)){
			$this->recursivePut($this->arr, $data);
			return 1;	
		}else{
			return -1;
			}
		}
	
	//-------------------------------------------------------------------------------------------------------------	
	public function put($key, $data){
		if($key != ''){
			$this->arr[$key] = $data;
			}
		}
	
	//-------------------------------------------------------------------------------------------------------------	
	private function recursivePut(&$arr, $data){
		if(is_array($data)){
			foreach($data as $k=>$v){	
				if(is_array($v)){
					$arr[$k] = array();
					$this->recursivePut($arr[$k], $v);
				}else{
					$arr[$k] = $v;
					}
				}
			}
		}
	
	//-------------------------------------------------------------------------------------------------------------			
	public function showResponse(){
		$this->recursiveShow($this->arr,'');
		}

	//-------------------------------------------------------------------------------------------------------------	
	private function recursiveShow($arr, $spacer){
		foreach($arr as $k=>$v){
			if(is_array($v)){
				echo $spacer.'['.$k.']'.'<br>';
				$this->recursiveShow($v, $spacer.$this->tab);
			}else{
				echo $spacer.'['.$k.'] = "'.$v.'"'.'<br>';
				}
			}
		}
		
	//-------------------------------------------------------------------------------------------------------------	
	private function recursiveBuild($arr, $str, $tab){
		$i = 0;
		foreach($arr as $k=>$v){
			if(!$i){
				$str .= '{'.$this->eol;
				}
			if(is_array($v)){
				$str .= $tab.''.$k.':';
				$str = $this->recursiveBuild($v, $str, $tab.$this->tab);
				$str .= ','.$this->eol;
			}else{
				$str .= $tab.''.$k.':"'.$v.'",'.$this->eol;
				}
			if($i+1 == count($arr)){
				$str = substr($str, 0, (strlen($str)-(1+strlen($this->eol)))).$this->eol;	
				$str .= $tab.'}';
				}
			$i++;	
			}
		return $str;
		}	
		
	//-------------------------------------------------------------------------------------------------------------	
	public function addHeader($header) {
		$this->headers[] = $header;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function redirect($url) {
		header('Location: ' . $url);
		exit;
		}
		
	//-------------------------------------------------------------------------------------------------------------	
	public function clear() {
		$this->arr = array();
		}
	
	//-------------------------------------------------------------------------------------------------------------		
	public function addError($err) {
		$this->error = $err;
		}	
	
	//-------------------------------------------------------------------------------------------------------------			
	public function output() {
		if(!headers_sent()) {
			foreach ($this->headers as $header) {
				header($header, true);
				}
			}
		if(count($this->error)){
			$this->arr = array('error'=>$this->error);
			}
			
		$rtn = $this->json->encode($this->arr);

		if($rtn === false){ //on a un json encoding issue
			return false;
			}
		return $rtn;
		}	

	//-------------------------------------------------------------------------------------------------------------	
	public function outputRaw() {
		if(!headers_sent()) {
			foreach ($this->headers as $header) {
				header($header, true);
				}
			}
		if(count($this->error)){	
			echo $this->recursiveBuild($this->error, '', $this->tab);
		}else{
			echo $this->recursiveBuild($this->arr, '', $this->tab);
			}
		}	
		
	//-------------------------------------------------------------------------------------------------------------	
	public function outputLog() {
		if(count($this->error)){	
			return $this->recursiveBuild($this->error, '', $this->tab);
		}else{
			return $this->recursiveBuild($this->arr, '', $this->tab);
			}
		}		
	
	}
	


//END