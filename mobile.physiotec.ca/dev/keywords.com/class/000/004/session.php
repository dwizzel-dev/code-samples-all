<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	sessions utilities
@notes:
	- to log file just use: $this->reg->get('log')->log('sessions', 'data to write');


*/

class Session {

	//registry
	private $reg;

	//vars
	private $db;
	private $trace = false;
	private $lifeTime;
	private $user_ip;
	private $lastpage = '';
	private $className = 'Session';
	private $sessIsDestroyed = false;	
	
	//-------------------------------------------------------------------------------------------------------------	
	public function __construct(&$reg){
		
		if($this->trace){	
			echo $this->className.'.__construct()'.EOL;
			echo 'LIFETIME: '.get_cfg_var('session.gc_maxlifetime').EOL;
			}
		//registry access
		$this->reg = $reg;
		//
		$this->lifeTime = get_cfg_var('session.gc_maxlifetime');
		$this->user_ip = $_SERVER['REMOTE_ADDR'];

		//access a la db, elle n'est pas enregistre dans le $reg, car est unqieuement accessible via cette classe
		$this->db =  new Database(DB_TYPE, DB_SESS_HOSTNAME, DB_SESS_USERNAME, DB_SESS_PASSWORD, DB_SESS_DATABASE, $this->reg);
		}
	
	//-------------------------------------------------------------------------------------------------------------
	//problems cause will call it with close and destruct at the end of the page so no need for it now
	/*		
	public function __destruct(){
		
		if($this->trace){	
			echo $this->className.'.__destruct()'.EOL;
			}
		
		$this->close();	
		}
	*/	

	//-------------------------------------------------------------------------------------------------------------
	public function getSessionDataFromUsername($str){
		
		if($this->trace){	
			echo $this->className.'.getSessionDataFromUsername('.$str.')'.EOL;
			}
		
		$query = 'SELECT session_data FROM '.DB_SESS_TABLE.' WHERE session_data LIKE "'.$this->db->escape($str).'";';
		$rs = $this->db->query($query);
		if($rs){
			return $rs;
			}	
		return false;		
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function getStatus(){
		if(!$this->db->getStatus()){
			return false;
			}
		return true;
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
	public function put($key, $data){
	
		if($this->trace){	
			echo $this->className.'.put('.$key.','.EOL;
			print_r($data).EOL;
			echo EOL.')'.EOL;
			}
		
		$_SESSION[$key] = $data;
		}
		
	//-------------------------------------------------------------------------------------------------------------	
	public function get($key){
		
		if($this->trace){	
			echo $this->className.'.get('.$key.')'.EOL;
			}
		
		if(isset($_SESSION[$key])){
			return $_SESSION[$key];
			}
		return false;	
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function remove($key){
		
		if($this->trace){	
			echo $this->className.'.remove('.$key.')'.EOL;
			}
		
		if(isset($_SESSION[$key])){
			unset($_SESSION[$key]);
			}
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function validate($sessID){
		
		if($this->trace){	
			echo $this->className.'.validate('.$sessID.')'.EOL;
			}
	
		//minor check
		if($sessID != $this->getSessionID() || $sessID == '0' || $sessID == '' || $sessID == '1' || strlen($sessID) != MIN_SESSION_STRLEN){
			return 0;
			}
		//minor check on DB and check if we have data if not then kick out
		$query = 'SELECT session_id AS "session_id" FROM '.DB_SESS_TABLE.' WHERE session_id = "'.$this->db->escape($sessID).'" LIMIT 0,1;';
		$rs = $this->db->query($query);
		if(!$rs || !$rs->num_rows){
			unset($rs);
			return 0;
		}else{
			//check if we have needed data in the session
			if(!isTrue($this->get('idUser'))){
				unset($rs);	
				return 0;
				}
			}
		return 1;
		}	
		
	//-------------------------------------------------------------------------------------------------------------	
	public function clear(){
		
		if($this->trace){	
			echo $this->className.'.clear()'.EOL;
			}
		
		if(isset($_SESSION)){
			foreach($_SESSION as $k=>$v){	
				unset($_SESSION[$k]);
				}
			}	
		//session_unset(); 
		}
		
	//-------------------------------------------------------------------------------------------------------------	
	public function getSessionID(){
		
		if($this->trace){	
			echo $this->className.'.getSessionID()'.EOL;
			}
		
		return session_id();
		}
		
	//-------------------------------------------------------------------------------------------------------------	
	public function showSession(){
		if(isset($_SESSION)){
			$this->recursiveShow($_SESSION,EOL);
			}
		}
	
	//-------------------------------------------------------------------------------------------------------------	
	private function recursiveShow($arr, $spacer){
		foreach($arr as $k=>$v){
			if(is_array($v)){
				echo $spacer.'['.$k.']'.EOL;
				$this->recursiveShow($v, $spacer.TAB);
			}else{
				echo $spacer.'['.$k.'] = "'.htmlentities($v).'"'.EOL;
				}
			}
		}	
		
	//-------------------------------------------------------------------------------------------------------------		
	public function start(){
		
		if($this->trace){	
			echo $this->className.'.start()'.EOL;
			}
		
		session_set_save_handler(
                	array(&$this,'open'),
                	array(&$this,'close'),
	                array(&$this,'read'),
        	        array(&$this,'write'),
                	array(&$this,'destroy'),
	                array(&$this,'gc')
			);

		// the following prevents unexpected effects when using objects as save handlers
		register_shutdown_function('session_write_close');		

		session_start();
		}
	
	//-------------------------------------------------------------------------------------------------------------	
	public function showPath(){
		echo 'PATH: '.session_save_path().'<br>';
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function getVars(){
		if(isset($_SESSION)){
			return $_SESSION;
			}
		return false;
		}

	//-------------------------------------------------------------------------------------------------------------		
	public function open($savePath, $sessName){
		
		if($this->trace){	
			echo $this->className.'.open('.$savePath.', '.$sessName.')'.EOL;
			}
		
		return true;
		}

	//-------------------------------------------------------------------------------------------------------------		
	public function close(){

		if($this->trace){	
			echo $this->className.'.close()'.EOL;
			}

		session_write_close();
		return true;
		}
		
	//-------------------------------------------------------------------------------------------------------------	
	public function destroy($sessID){
		
		if($this->trace){	
			echo $this->className.'.destroy('.$sessID.')'.EOL;
			}
		
		/*
		$this->clear();
		session_destroy();
		*/
		$query = 'DELETE FROM '.DB_SESS_TABLE.' WHERE session_id = "'.$this->db->escape($sessID).'";';
		if(!$this->db->query($query)){
			return false;
			}
		//si on ne veut pas qu'il fasse un write après le destroy
		$this->sessIsDestroyed = true;
		return true;
		}
		
	//-------------------------------------------------------------------------------------------------------------
	public function read($sessID){
		
		if($this->trace){	
			echo $this->className.'.read('.$sessID.')'.EOL;
			}
		
		$query = 'SELECT session_data AS "session_data", session_ip AS "session_ip" FROM '.DB_SESS_TABLE.' WHERE session_id = "'.$this->db->escape($sessID).'" LIMIT 0,1;';
		$rs = $this->db->query($query);
		if($rs && $rs->num_rows){
			$session_data = $rs->row['session_data'];
			$session_ip = $rs->row['session_ip'];
			if($session_ip != $this->user_ip){
				$logout = true;
				$array1 = explode('.', $session_ip);
				$array2 = explode('.', $this->user_ip);
				if(count($array1) == count($array2)){
					if($array1[0] == $array2[0] && $array1[1] == $array2[1] && $array1[2] == $array2[2]){
						$logout = false;
						}
					}
				if($logout){
					//dwizzel:: need a way to force logout of the user trought the service call or something else
					}
				}
			unset($rs);
			return $session_data;
			}
		return '';
		}

	//-------------------------------------------------------------------------------------------------------------
	public function write($sessID, $sessData){
		
		if($this->trace){	
			echo $this->className.'.write('.$sessID.', '.$sessData.')'.EOL;
			}
		
		if(!$this->sessIsDestroyed){	
			$newExp = time() + $this->lifeTime;
			$now = date('Y-m-d H:i:s');
			$session_page = $_SERVER['PHP_SELF'];
			$query = 'SELECT session_datetime AS "session_datetime", session_page AS "session_page" FROM '.DB_SESS_TABLE.' WHERE session_id = "'.$this->db->escape($sessID).'" LIMIT 0,1;';
			$rs = $this->db->query($query);
			if($rs && $rs->num_rows){
				$session_datetime = $rs->row['session_datetime'];
				$this->lastpage = $rs->row['session_page'];
				if($session_datetime < date('Y-m-d H:i:s',(time()-$this->lifeTime))){
					$query = 'DELETE FROM '.DB_SESS_TABLE.' WHERE session_id = "'.$this->db->escape($sessID).'";';
					if($this->db->query($query)){
						return true;
						}
				}else{
					$sessData = str_replace('"', '\"', stripslashes($sessData));
					$query  = 'UPDATE '.DB_SESS_TABLE.' SET session_datetime = "'.$now.'", session_data = "'.$sessData.'", session_page = "'.$session_page.'" WHERE session_id = "'.$this->db->escape($sessID).'";';
					if($this->db->query($query)){
						return true;
						}	
					}
				unset($rs);
			}else{
				$sessData = str_replace('"', '\"', stripslashes($sessData));
				$query = 'INSERT INTO '.DB_SESS_TABLE.' (session_id, session_datetime, session_data, session_ip, session_page) VALUES("'.$this->db->escape($sessID).'", "'.$now.'", "'.$sessData.'", "'.$this->user_ip.'", "'.$session_page.'");';
				if($this->db->query($query)){
					return true;
					}	
				}
		}else{
			return true;
			}
		return false;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function gc($sessMaxLifeTime){
		
		if($this->trace){	
			echo $this->className.'.gc('.$sessMaxLifeTime.')'.EOL;
			}
		
		$query = 'UPDATE '.DB_SESS_TABLE.' SET session_data = "" WHERE session_datetime < "'.date('Y-m-d H:i:s',(time()-$this->lifeTime)).'";';
		$rs = $this->db->query($query);
		if($rs){
			return $rs->affected_rows;
			}
		unset($rs);	
		return false;		
		}
		
	
	}


//END