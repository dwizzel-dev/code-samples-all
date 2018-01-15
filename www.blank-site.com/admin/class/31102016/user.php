<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	...

*/



class User {
	
	private $reg;
		
	public function __construct($reg) {
		$this->reg = $reg;
		}
		
	public function getUserInfos($user_id){
		//get the data we need from the DB
		$query = 'SELECT '.DB_PREFIX.'user.username AS "username", '.DB_PREFIX.'user.firstname AS "firstname", '.DB_PREFIX.'user.lastname AS "lastname", '.DB_PREFIX.'user.email AS "email", '.DB_PREFIX.'user.tel_1 AS "tel_1", '.DB_PREFIX.'user.tel_2 AS "tel_2" FROM '.DB_PREFIX.'user WHERE '.DB_PREFIX.'user.id = "'.$user_id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0];	
			}
		return $false;		
		}
		
	public function getUserInfolettre($id){
		//check si on le user
		$query = 'SELECT '.DB_PREFIX.'user.infolettre AS "infolettre", '.DB_PREFIX.'user.email AS "email" FROM '.DB_PREFIX.'user WHERE '.DB_PREFIX.'user.id = "'.$id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		return $rs->rows[0];	
		}	
		
	public function getUserWithCourriel($courriel){
		//check si on le user
		$query = 'SELECT '.DB_PREFIX.'user.id AS "id", '.DB_PREFIX.'user.group_id AS "group_id", '.DB_PREFIX.'user.password AS "password" FROM '.DB_PREFIX.'user WHERE '.DB_PREFIX.'user.username = "'.$courriel.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0];
			}
		return false;	
		}
		
	
	}


//END