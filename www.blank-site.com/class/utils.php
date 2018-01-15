<?php
class Utils {
	
	private $reg;
		
	public function __construct($reg) {
		$this->reg = $reg;
		}
		
	public function getTypeById($type_id){
		$query = 'SELECT '.DB_PREFIX.'categorie.id AS "id", '.DB_PREFIX.'categorie.name AS "name", '.DB_PREFIX.'categorie.name_en AS "name_en", '.DB_PREFIX.'categorie.slogan AS "slogan", '.DB_PREFIX.'categorie.slogan_en AS "slogan_en", '.DB_PREFIX.'categorie.image AS "image" FROM '.DB_PREFIX.'categorie WHERE '.DB_PREFIX.'categorie.id = "'.$type_id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0];
			}
		return false;
		}	
	
	public function getProvince(){
		$query = 'SELECT '.DB_PREFIX.'province.id AS "id", '.DB_PREFIX.'province.name AS "name", '.DB_PREFIX.'province.name_en AS "name_en", '.DB_PREFIX.'province.code AS "code" FROM '.DB_PREFIX.'province  ORDER BY '.DB_PREFIX.'province.id ASC;';
		$rs = $this->reg->get('db')->query($query);
		return $rs->rows;
		}	
		
	public function getSlogan(){
		$query = 'SELECT '.DB_PREFIX.'slogan.id AS "id", '.DB_PREFIX.'slogan.name AS "name", '.DB_PREFIX.'slogan.locale AS "locale" FROM '.DB_PREFIX.'slogan ORDER BY '.DB_PREFIX.'slogan.locale ASC;';
		$rs = $this->reg->get('db')->query($query);
		return $rs->rows;
		}	

	public function writeNewFile($filename, $content){
		$fh = fopen($filename, 'w');
		if($fh){
			fwrite($fh, $content);
			fclose($fh);
			}
		}	

	public function addHoursToDate($date, $hours){
		return date("Y-m-d H:i:s", strtotime($date) + ((60*60) * $hours));
		}	
		
	}
?>