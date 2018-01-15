<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	news model

*/



class widgetNewsModel{
	
	//vars
	private $reg;
	private $lang;

	//construct
	public function __construct(&$reg, $lang){
		$this->reg = $reg;
		$this->lang = $lang;
		}
	
	//get the carousel data	
	public function getLatestNews($limit = 3, $start = 0){
		//on va chercher dans la DB
		$query = 'SELECT '.DB_PREFIX.'news_category.title AS "category_title", '.DB_PREFIX.'news_category.link_id AS "link_id", '.DB_PREFIX.'news.alias AS "alias", '.DB_PREFIX.'news.id AS "id", '.DB_PREFIX.'news.date_modified AS "date_modified", '.DB_PREFIX.'news.date_added AS "date_added", '.DB_PREFIX.'news.title AS "title", '.DB_PREFIX.'languages.code AS "code", '.DB_PREFIX.'news.preview AS "preview" FROM '.DB_PREFIX.'news INNER JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'news.language_id INNER JOIN '.DB_PREFIX.'news_category ON '.DB_PREFIX.'news_category.id = '.DB_PREFIX.'news.category_id WHERE '.DB_PREFIX.'news.status = "1" AND '.DB_PREFIX.'languages.code = "'.$this->lang.'" ORDER BY '.DB_PREFIX.'news.date_added DESC, '.DB_PREFIX.'news.id DESC ';
		if(!$limit){
			$query .= ';';
		}else{
			$query .= ' LIMIT '.$start.','.$limit.';';
			}
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;
			}
		return false;
		
		}
		
	}
	
	
	
	
	
	
//END	
	
	
	