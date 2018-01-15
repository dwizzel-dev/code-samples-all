<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	GLOBALS

*/


class Globals {
	private $data;
	private $className = 'Globals';

	//-------------------------------------------------------------------------------------------------------------	
	public function __construct() {
		$this->data = array();
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
	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : false);
		}
	
	//-------------------------------------------------------------------------------------------------------------		
	public function getArray($key, $arr) {
		if(isset($this->data[$key])){
			if(isset($this->data[$key][$arr])){
				return $this->data[$key][$arr];
				}
			}
		return false;
		}	

	//-------------------------------------------------------------------------------------------------------------	
	public function set($key, $value) {
		$this->data[$key] = $value;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function has($key) {
    	return isset($this->data[$key]);
		}
		
	//-------------------------------------------------------------------------------------------------------------	
	public function showGlobals(){
		return $this->recursiveShow($this->data,'<br />', '');
		}
	
	//-------------------------------------------------------------------------------------------------------------		
	public function getVars(){
		return $this->data;
		}	
	
	//-------------------------------------------------------------------------------------------------------------		
	private function recursiveShow($arr, $spacer, $str){
		foreach($arr as $k=>$v){
			if(is_array($v)){
				$str .= $spacer.'['.$k.']';
				$str = $this->recursiveShow($v, $spacer.'&nbsp;&nbsp;&nbsp;&nbsp;', $str);
			}else{
				$str .= $spacer.'["'.$k.'"] = "'.str_replace(array('<','>'),array('&lt;','&gt;'),$v).'"';
				}
			}
		return $str;	
		}	
		
	}


//END