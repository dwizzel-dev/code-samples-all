<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	REGISTRY

*/


class Registry {
	private $data;
	private $className = 'Registry';

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
		return (isset($this->data[$key]) ? $this->data[$key] : FALSE);
		}

	//-------------------------------------------------------------------------------------------------------------
	public function set($key, &$value) {
		$this->data[$key] = &$value;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function has($key) {
    	return isset($this->data[$key]);
		}
	}


//END