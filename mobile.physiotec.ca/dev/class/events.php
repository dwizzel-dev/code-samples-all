<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	EVENTS log des users events

*/


class Events {

	private $reg;
	private $className = 'Events';

	//-------------------------------------------------------------------------------------------------------------	
	public function __construct(&$reg) {
		$this->reg = $reg;
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
	public function add($data){
		/*
		data:{
			event(string):"login",
			userid: ...
			clinicid: ...
			ip: ...
			browser(string):...
			detail(string):...
			}
		*/
		if(is_array($data)){
			if(!isset($data['event'])){
				//on retourne ccar c'est le minumum requis
				return false;
				}
			if(!isset($data['userid'])){
				$data['userid'] = intVal($this->reg->get('sess')->get('idUser'));
				}
			if(!isset($data['clinicid'])){
				$arrClinic = $this->reg->get('sess')->get('clinic');
				if(isset($arrClinic['idClinic'])){
					$data['clinicid'] = intVal($arrClinic['idClinic']);
				}else{
					$data['clinicid'] = 0;
					}
				}
			if(!isset($data['ip'])){
				$data['ip'] = $this->reg->get('req')->get('ip');
				}
			if(!isset($data['browser'])){
				$data['browser'] = 'mobile';
				}	
			if(!isset($data['detail'])){
				$data['detail'] = '';
				}
			//on a tout ce qu'il faut alors on fait l'insertion
			$query = 'INSERT INTO user_event(idUser, idClinic, event, ip, browser, detail, datetime) VALUES("'.$data['userid'].'", "'.$data['clinicid'].'", "'.$this->reg->get('db')->escape($data['event']).'", "'.$this->reg->get('db')->escape($data['ip']).'", "'.$this->reg->get('db')->escape($data['browser']).'", "'.$this->reg->get('db')->escape($data['detail']).'", NOW());';
			//insert
			$this->reg->get('db')->query($query);	
			//
			return true;
			}
		return false;
		}
	
	}


//END