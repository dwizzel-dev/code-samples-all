<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	database object

*/


class Database{
	
	private $trace = false;
	private $connection;
	private $reg;
	private $type;
	private $bStatus = false;
	private $database;
	private $arrQueries = array();
	private $className = 'Database';	
	
	//-------------------------------------------------------------------------------------------------------------	
	public function __construct($type, $hostname, $username, $password, $database, &$reg) {

		if($this->trace){
			echo $this->className.'.__construct('.$database.')'.EOL;	
			}
		
		$this->database = $database;	

		$this->reg = $reg;
		$this->type = $type;
		
		$this->connection = new mysqli($hostname, $username, $password, $database);
		
		if($this->connection->connect_errno != 0) {
			$this->reg->get('log')->log('connection-error', $this->database.'[0]Error: '.$this->connection->connect_error.' Error No: '.$this->connection->connect_errno);
			return;
		}else{
			if(!$this->connection->select_db($database)) {
				$this->reg->get('log')->log('connection-error', $this->database.'[1]Error: '.$this->connection->error.' Error No: '.$this->connection->errno);
				return;
			}else{
				# olivier i comment this code since the files will handle the encoding from and to utf8 and the db is using the default encoding 
				# which is utf8_general_ci
				
				$this->connection->set_charset("utf8");
				mysqli_set_charset($this->connection,"utf8");
				$this->connection->query("SET NAMES 'utf8'");
				$this->connection->query("SET CHARACTER SET utf8");
				$this->connection->query("SET CHARACTER_SET_CONNECTION=utf8");
				$this->connection->query("SET SQL_MODE = ''");
				}
			}
		
		$this->bStatus = true;	
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
  	public function query($sql){
		
		if($this->trace){
			echo $this->className.'.query()'.EOL;	
			echo $sql.EOL;
			}
		//
		$resource = false;
		//
		if(ENABLE_LOG){
			//$this->reg->get('log')->log('sql-'.$this->database, $sql);
			}
		//debug
		array_push($this->arrQueries, $sql);
		//
		if(!$this->bStatus || !$this->connection){
			$this->reg->get('log')->log('connection-error', $this->database.'[2]Error: [' . $sql. ']');
			return false;	
			}
		//
		if($this->connection->connect_errno != 0){
			$this->reg->get('log')->log('connection-error', $this->database.'[3]Error: '.$this->connection->connect_error.' Error No: '.$this->connection->connect_errno.' [' . $sql. ']');
			return false;	
			}
		//
		if($this->connection->errno != 0){
			$this->reg->get('log')->log('connection-error', $this->database.'[4]Error: '.$this->connection->error.' Error No: '.$this->connection->errno.' [' . $sql. ']');
			return false;	
			}
		//
		$resource = $this->connection->prepare($sql);
		//
		if(!$resource){
			$this->reg->get('log')->log('queries-error', $this->database.'[0]Error: '.$this->connection->error.' Error No: '.$this->connection->errno.' [' . $sql. ']');
			return false;
			}
		//
		if($resource->errno == 0){
			if(!$resource->execute()){
				$this->reg->get('log')->log('queries-error', $this->database.'[1]Error: '.$resource->error.' Error No: '.$resource->errno.' [' . $sql. ']');
				$resource->reset();
				$resource->close();
				unset($resource);
				return false;
			}else{
				$query = new stdClass();
				if($resource->field_count != 0){
					$results = $this->fetch_assoc_stmt($resource);
					$query->num_rows = count($results);
					$query->row = isset($results[0]) ? $results[0] : array();
					$query->fields = $this->getFields($query->row);
					$query->rows = $results;
					$query->affected_rows = $resource->affected_rows;
					$resource->reset();
					$resource->close();
					unset($resource);
					unset($results);
					return $query;	
				
				}else{
					$query->affected_rows = $resource->affected_rows;
					$query->insert_id = $resource->insert_id;
					$resource->reset();
					$resource->close();
					unset($resource);
					return $query;	
					}
				}		
		}else{
			$this->reg->get('log')->log('queries-error',  $this->database.'[2]Error: '.$resource->error.' Error No: '.$resource->errno.' [' . $sql. ']');
			$resource->reset();
			$resource->close();
			unset($resource);	
			return false;
			}
		//
		unset($resource);
		return false;
		}

	//-------------------------------------------------------------------------------------------------------------	
	private function getFields($arr){
		$arrFields = array();	
		if(count($arr)){
			$arrFields = array();
			foreach($arr as $k=>$v){
				array_push($arrFields, $k);
				}
			return $arrFields;
			}
		return false;	
		}
		
	//-------------------------------------------------------------------------------------------------------------	
	private function addSlashes($result){
		if(is_array($result)){
			foreach($result as $k=>$v){
				$result[$k] = $this->addSlashes($result[$k]);
				}
		}else{
			$result = stripslashes($result);
			$result = htmlspecialchars($result, ENT_COMPAT, 'UTF-8');
			}
		return $result;
		}
	
	//-------------------------------------------------------------------------------------------------------------	
	public function escape($value){
		if($this->connection){
			return $this->connection->real_escape_string($value);
			}
		return false;
		}
	
  	//-------------------------------------------------------------------------------------------------------------	
	public function __destruct() {
		
		if($this->trace){
			echo $this->className.'.__destruct('.$this->database.')'.EOL;	
			}

		if($this->bStatus === true){
			$this->connection->close();
			}	
		}
	
	//-------------------------------------------------------------------------------------------------------------	
	public function showRows($rs){
		$bTitle = 0;
		$table = '<table border="1" cellpadding="2" cellspacing="2">';
		foreach($rs->rows as $k=>$v){
			if(!$bTitle){
				$table .= $this->showTitle($v);
				$bTitle = 1;
				}
			$table .= $this->showRow($v);
			}
		$table .= '</table>';
		echo $table;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function showTitle($row){
		$data = '<tr>';
		foreach($row as $k=>$v){
			$data .='<td><font size="-1"><b>'.$k.'</b></font></td>';
			}
		$data .= '</tr>';
		return $data;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function showRow($row){
		$data = '<tr>';
		foreach($row as $k=>$v){
			$data .='<td><font size="-1">'.$v.'</font></td>';
			}
		$data .= '</tr>';
		return $data;
		}	
		
	//-------------------------------------------------------------------------------------------------------------	
	public function getStatus(){
		return $this->bStatus;
		}	
	
	//-------------------------------------------------------------------------------------------------------------		
	public function getQueries(){
		return $this->arrQueries;
		}

	//-------------------------------------------------------------------------------------------------------------		
	public function getQueriesNum(){
		return count($this->arrQueries);
		}	
		
	//-------------------------------------------------------------------------------------------------------------	
	public function fetch_assoc_stmt(&$stmt, $buffer = true){
		if($buffer){
			$stmt->store_result();
			}
		$fields = $stmt->result_metadata()->fetch_fields();
		$args = array();
		foreach($fields AS $field) {
			$key = str_replace(' ', '_', $field->name); 
			$args[$key] = &$field->name; 
			}
		call_user_func_array(array($stmt, 'bind_result'), $args);
		$results = array();
		$i = 0;
		while($stmt->fetch()){
			$results[] = array_map("self::copyValue", $args);
			}
		if ($buffer) {
			$stmt->free_result();
			}
		return $results;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function copyValue($v){
		return $v;
		}
			
		
	}



//END
