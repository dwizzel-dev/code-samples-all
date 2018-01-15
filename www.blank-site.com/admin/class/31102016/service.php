<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	Service API
@to check the include in mail class is done when just needed at the client process line 184, 197
*/


class Service {

	private $reg;
	private $sessionId;
	private $section;
	private $service;
	private $lang;
	private $data; //received JSON compressed
	private $rawData; //received NOT JSON compressed	 
	private $errNum; //ERR NUM
	private $errStr; //ERR STRING	 
	private $formErrStr; //FORM STRING	 
	private $iJsonErrorOnDataParse = 0;	
	private $className = 'Service';

	//-------------------------------------------------------------------------------------------------------------
	public function __construct(&$reg) {
		//vars
		$this->reg = $reg;
		$this->sessionId = $this->reg->get('req')->get('PHPSESSID');
		$this->section = $this->reg->get('req')->get('section');
		$this->service = $this->reg->get('req')->get('service');
		$this->lang = $this->reg->get('req')->get('lang');
		$this->data = false;
		$this->rawData = $this->reg->get('req')->get('data');
		
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
			
		//si pas de langue on en set une par defaut dans le request object et le service var
		if(!isTrue($this->lang)){
			$this->lang = LANG_DEFAULT;
			$this->reg->get('req')->set('lang', $this->lang);
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
		//mais on a a un prob car certain data ne vont pas etre json encode
		/*
		if($this->iJsonErrorOnDataParse != 0){
			$this->setError($this->reg->get('err')->getJsonError($this->iJsonErrorOnDataParse));
			return false;
			}
		*/
		if(!isTrue($this->section)){
			$this->setError($this->reg->get('err')->get(103));
			return false;
			}
		if(!isTrue($this->service)){
			$this->setError($this->reg->get('err')->get(104));
			return false;
			}
		
		//check if we need a session id, only the login dont need some
		/*
		if(!($this->section == 'user' && $this->service == 'do-login') && !($this->section == 'user' && $this->service == 'auto-login')){
			if(!$this->reg->get('sess')->validate($this->sessionId)){
				$this->setError($this->reg->get('err')->get(107));
				return false;
				}
			}
		*/
		
		//custom php errors
		//trigger_error($this->className.'.check:: "'.$this->section.'", "'.$this->service.'"', E_USER_NOTICE);
		
		//
		return true;	
		}

	//-------------------------------------------------------------------------------------------------------------
	private function setError($str) {
		$this->errStr = $str;
		}

	//-------------------------------------------------------------------------------------------------------------
	private function setFormError($str) {
		$this->formErrStr = $str;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function getErrorNum() {
		return $this->errNum;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function getError() {
		return $this->errStr;
		}
	
	//-------------------------------------------------------------------------------------------------------------
	public function getFormError() {
		return $this->formErrStr;
		}
	
	//-------------------------------------------------------------------------------------------------------------
	public function process() {
		//check the section first
		switch($this->section){
			case 'login':
				return $this->processLogin();
				break;

			case 'translation':
				return $this->processTranslation();
				break;

			default:
				$this->setError($this->reg->get('err')->get(106));
				return false;
				break;
			}

		}

	//-------------------------------------------------------------------------------------------------------------
	private function processLogin(){	
		//instanciate object
		require_once(DIR_CLASS.'login.php');
		$oLogin = new Login($this->reg);
		//case services
		switch($this->service){
			case 'do-login':
				$funcRtn = $oLogin->doLogin($this->data);
				if(!$funcRtn){
					$this->setError($oLogin->getMsgErrors());
					$this->setFormError($oLogin->getFormErrors());
					}
				return $funcRtn;
				break;

			case 'do-logout':
				return $oLogin->doLogout();
				break;

			default:
				$this->setError($this->reg->get('err')->get(105));
				return false;
				break;
			}
		return false;
		}

	//-------------------------------------------------------------------------------------------------------------
	private function processTranslation(){	
		//instanciate object
		require_once(DIR_CLASS.'translation.php');
		$oTranslation = new Translation($this->reg);
		//case services
		switch($this->service){
			case 'get-items-from-table':
				$funcRtn = $oTranslation->getData($this->rawData);
				if(!$funcRtn){
					$this->setError($this->reg->get('err')->get(201));
					}
				return $funcRtn;
				break;

			case 'set-translation-infos':
				$funcRtn = $oTranslation->setData($this->data);
				if(!$funcRtn){
					$this->setError($this->reg->get('err')->get(202));
					}
				return $funcRtn;
				break;

			default:
				$this->setError($this->reg->get('err')->get(105));
				return false;
				break;
			}
		return false;
		}

	}


//END