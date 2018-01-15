<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	Service API

*/


class Service {

	private $reg;
	private $pid;
	private $timestamp;
	private $sessionId;
	private $section;
	private $service;
	private $lang;
	private $data;
	private $errNum;
	private $iJsonErrorOnDataParse = 0;	
	private $className = 'Service';

	//-------------------------------------------------------------------------------------------------------------
	public function __construct(&$reg) {
		//vars
		$this->reg = $reg;
		$this->errNum = '';
		$this->pid = intVal($this->reg->get('req')->get('pid'));
		$this->timestamp = intVal($this->reg->get('req')->get('time'));
		$this->section = $this->reg->get('req')->get('section');
		$this->service = $this->reg->get('req')->get('service');
		$this->data = false;

		//si data then json decode the string
		if($this->reg->get('req')->get('data').'' != ''){
			$this->data = json_decode($this->reg->get('req')->get('data'), true);
			//check si erreur json
			$this->iJsonErrorOnDataParse = json_last_error();
			if($this->iJsonErrorOnDataParse != JSON_ERROR_NONE){
				$this->data = false;
				}
		}else{
			//on existe pas alors on le rajoute au request object
			$this->reg->get('req')->set('data', '');
			}
		
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
	public function check() {
		
		//check le parse de this.data de json fait dans le construct
		if($this->iJsonErrorOnDataParse != 0){
			$this->setError($this->reg->get('err')->getJsonError($this->iJsonErrorOnDataParse));
			return false;
			}

		//we need them all except $data, but return different errors on missing parts of the call
		if(!isTrue($this->pid)){
			$this->setError(101);
			return false;
			}
		
		if(!isTrue($this->timestamp)){
			$this->setError(102);
			return false;
			}
		if(!isTrue($this->section)){
			$this->setError(103);
			return false;
			}
		if(!isTrue($this->service)){
			$this->setError(104);
			return false;
			}
		
		//check if we need a session id, only the login dont need some
		/*
		if(!($this->section == 'user' && $this->service == 'do-login')){
			if(!$this->reg->get('sess')->validate($this->sessionId)){
				$this->setError(107);
				return false;
				}
			}
		*/
		//custom php errors
		
		//
		return true;	
		}

	//-------------------------------------------------------------------------------------------------------------
	private function setError($num) {
		$this->errNum = intVal($num);
		}

	//-------------------------------------------------------------------------------------------------------------
	public function getErrorNum() {
		return $this->errNum;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function getError() {
		return $this->reg->get('err')->get($this->errNum);
		}
	
	//-------------------------------------------------------------------------------------------------------------
	public function process() {
		//check the section first
		switch($this->section){
			case 'user':
				return $this->processUser();
				break;
				
			default:
				$this->setError(106);
				return false;
				break;
			}

		}

	//-------------------------------------------------------------------------------------------------------------
	private function processUser() {
		require_once(DIR_CLASS.'user.php');
		$oUser = new User($this->reg);
		//case services
		switch($this->service){
			case 'do-login':
				return $oUser->doLogin($this->data);
				break;	
			
			case 'do-logout':
				return $oUser->doLogout($this->data);
				break;	
			
			default:
				$this->setError(105);
				return false;
				break;
			}
		}

	
	}


//END