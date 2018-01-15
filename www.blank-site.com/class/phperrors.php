<?php
class PhpErrors {

	private $data;
	
	public function __construct(){
		$this->data = array();
		}
	
	public function add($value){
		array_push($this->data, $value);
		}
		
	public function getAll(){
		return $this->data;
		}	
		
	}


//END