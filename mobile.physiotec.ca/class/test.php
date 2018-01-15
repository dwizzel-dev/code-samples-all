<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	test all kind of things

*/


class Test {

	private $reg;
	private $className = 'Test';	
	
	//-------------------------------------------------------------------------------------------------------------	
	public function __construct(&$reg){
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
	public function sess(){
		//getting a session id
		$arr = array(
			'sessid' => $this->reg->get('sess')->getSessionID(),	
			);
		return $arr;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function read($data){
		//test read all session infos
		return $this->reg->get('sess')->getVars();
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function write($data){
		//test write session infos
		$this->reg->get('sess')->put('test', $data);
		return 1;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function select($data){
		//select from a database
		//array to return
		$arr = array(
			'num_rows' => 0,
			'fields' => array(),
			'rows' => array(),
			);
		//select string
		$query = 'SELECT '.DB_PREFIX.'user.idUser AS "id", '.DB_PREFIX.'user.firstname AS "firstname", '.DB_PREFIX.'user.lastname AS "lastname", '.DB_PREFIX.'user.username AS "username" FROM '.DB_PREFIX.'user ORDER BY '.DB_PREFIX.'user.idUser ASC LIMIT 0,10;';
		//result set
		$rs = $this->reg->get('db')->query($query);
		//minor check
		if($rs && $rs->num_rows){
			$arr['num_rows'] = $rs->num_rows;
			$arr['fields'] = $rs->fields;
			foreach($rs->rows as $k=>$v){
				array_push($arr['rows'], $v);
				}
			}	
		return $arr;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function insert($data){
		if(isset($data['firstname']) && isset($data['lastname']) && isset($data['age'])){
			if($data['firstname'] != '' && $data['lastname'] != '' && $data['age'] != ''){
				$query = 'INSERT INTO '.DB_PREFIX.'test (firstname, lastname, age) VALUES("'.$data['firstname'].'","'.$data['lastname'].'","'.$data['age'].'");';
				if(!$this->reg->get('db')->query($query)){
					return 0;
				}else{
					return 1;
					}
				}
			}
		return 0;
		}


	}


//END
