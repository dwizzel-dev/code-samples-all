<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	LOGS

*/


class Log {
	
	private $reg;
	private $date;
	private $time;
	private $className = 'Log';

	//-------------------------------------------------------------------------------------------------------------		
	public function __construct(&$reg) {
		$this->reg = $reg;
		date_default_timezone_set('America/New_York'); 
		//$now = DateTime::createFromFormat('U.u', microtime(true)); //crash to often
		//$this->date = $now->format('dmY');
		//$this->time = $now->format('H:i:s.u'); //with millisecond
		$this->date = date('dmY');
		$this->time = date('H:i:s'); //no millisecond
		}

	//-------------------------------------------------------------------------------------------------------------		
	public function log($logName, $str) {
		$fp = fopen(DIR_LOGS.'/'.$logName.'_'.$this->date.'.txt', 'a');
		if($fp){
			fwrite($fp, $this->time."\t".$str."\n"."\n");
			fclose($fp);
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
	}


//END