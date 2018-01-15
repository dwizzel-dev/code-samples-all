<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	news model

*/


class News{
	
	//vars
	private $reg;
	private $lang;
	
	//construct
	public function __construct(&$reg, $lang){
		$this->reg = $reg;
		$this->lang = $lang;
		}
	
	public function getNewsBreadcrumbs($id, $arr = array()){
		//on va chercher dans la DB
		$query = 'SELECT '.DB_PREFIX.'news_category.id AS "id", '.DB_PREFIX.'news_category.link_id AS "link_id", '.DB_PREFIX.'news_category.parent_id AS "parent_id", '.DB_PREFIX.'news_category.title AS "title" FROM '.DB_PREFIX.'news_category WHERE '.DB_PREFIX.'news_category.status = "1" AND '.DB_PREFIX.'news_category.id = "'.$id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			array_push($arr, $rs->rows[0]);
			if($rs->rows[0]['parent_id'] != 0){
				$arr = $this->getNewsBreadcrumbs($rs->rows[0]['parent_id'], $arr);
				}
			}
		return $arr;
		}	
		
	public function getNewsCategory(){
		//on va chercher dans la DB
		$query = 'SELECT '.DB_PREFIX.'news_category.link_id AS "link_id", '.DB_PREFIX.'news_category.content AS "content", '.DB_PREFIX.'news_category.title AS "title", '.DB_PREFIX.'news_category.alias AS "alias" FROM '.DB_PREFIX.'news_category INNER JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'news_category.language_id = '.DB_PREFIX.'languages.id  WHERE '.DB_PREFIX.'news_category.status = "1" AND '.DB_PREFIX.'news_category.parent_id != "0" AND '.DB_PREFIX.'languages.code = "'.$this->lang.'" ORDER BY '.DB_PREFIX.'news_category.position;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows;
			}
		return false;
		
		}
		
	public function getCategoryInfos($alias){
		//on va chercher dans la DB
		$query = 'SELECT '.DB_PREFIX.'news_category.link_id AS "link_id", '.DB_PREFIX.'news_category.id AS "id", '.DB_PREFIX.'news_category.title AS "title", '.DB_PREFIX.'languages.code AS "code", '.DB_PREFIX.'news_category.css_class AS "css_class", '.DB_PREFIX.'news_category.meta_title AS "meta_title", '.DB_PREFIX.'news_category.meta_description AS "meta_description", '.DB_PREFIX.'news_category.meta_keywords AS "meta_keywords", '.DB_PREFIX.'news_category.content AS "content" FROM '.DB_PREFIX.'news_category INNER JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'news_category.language_id WHERE '.DB_PREFIX.'news_category.alias = "'.$alias.'" AND '.DB_PREFIX.'news_category.status = "1"  LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0];
			}
		return false;
		
		}	
		
	public function getNewsFromCategory($cat, $limit = 0, $start = 0){
		//on va chercher dans la DB
		$query = 'SELECT '.DB_PREFIX.'news_category.link_id AS "link_id", '.DB_PREFIX.'news.hits AS "hits", '.DB_PREFIX.'news.alias AS "alias", '.DB_PREFIX.'news.id AS "id", '.DB_PREFIX.'news.date_modified AS "date_modified", '.DB_PREFIX.'news.date_added AS "date_added", '.DB_PREFIX.'news.title AS "title", '.DB_PREFIX.'languages.code AS "code", '.DB_PREFIX.'news.preview AS "preview" FROM '.DB_PREFIX.'news INNER JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'news.language_id  INNER JOIN '.DB_PREFIX.'news_category ON '.DB_PREFIX.'news_category.id = '.DB_PREFIX.'news.category_id WHERE '.DB_PREFIX.'news.category_id = "'.$cat.'" AND '.DB_PREFIX.'news.status = "1" ORDER BY '.DB_PREFIX.'news.date_added DESC, '.DB_PREFIX.'news.id DESC ';
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
		
	public function getNewsCountFromCategory($cat){
		//on va chercher le count des news selon la cat
		$query = 'SELECT COUNT('.DB_PREFIX.'news.id) AS "count" FROM '.DB_PREFIX.'news WHERE '.DB_PREFIX.'news.category_id = "'.$cat.'" AND '.DB_PREFIX.'news.status = "1";';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0]['count'];
			}
		return 0;
		}			
			
	public function getNewerNews($id, $cat_id){
		//on va chercher dans la DB
		$query = 'SELECT '.DB_PREFIX.'news_category.link_id AS "link_id", '.DB_PREFIX.'news.id AS "id", '.DB_PREFIX.'news.alias AS "alias" FROM '.DB_PREFIX.'news INNER JOIN '.DB_PREFIX.'news_category ON '.DB_PREFIX.'news_category.id = '.DB_PREFIX.'news.category_id WHERE '.DB_PREFIX.'news.date_added >= (SELECT '.DB_PREFIX.'news.date_added FROM '.DB_PREFIX.'news WHERE '.DB_PREFIX.'news.id = "'.$id.'" LIMIT 0,1) AND '.DB_PREFIX.'news.id != "'.$id.'" AND '.DB_PREFIX.'news.category_id = "'.$cat_id.'" ORDER BY '.DB_PREFIX.'news.date_added ASC, '.DB_PREFIX.'news.id ASC LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0]; //
			}
		return false;
		}
		
	public function getNewsPager($id, $cat_id){
		return array(
			'older' => $this->getOlderNews($id, $cat_id),
			'newer' => $this->getNewerNews($id, $cat_id),
			);	
		}
		
	public function getOlderNews($id, $cat_id){
		//on va chercher dans la DB
		$query = 'SELECT '.DB_PREFIX.'news_category.link_id AS "link_id", '.DB_PREFIX.'news.id AS "id", '.DB_PREFIX.'news.alias AS "alias" FROM '.DB_PREFIX.'news INNER JOIN '.DB_PREFIX.'news_category ON '.DB_PREFIX.'news_category.id = '.DB_PREFIX.'news.category_id WHERE '.DB_PREFIX.'news.date_added <= (SELECT '.DB_PREFIX.'news.date_added FROM '.DB_PREFIX.'news WHERE '.DB_PREFIX.'news.id = "'.$id.'" LIMIT 0,1) AND '.DB_PREFIX.'news.id != "'.$id.'" AND '.DB_PREFIX.'news.category_id = "'.$cat_id.'" ORDER BY '.DB_PREFIX.'news.date_added DESC, '.DB_PREFIX.'news.id DESC LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0];
			}
		return false;
		}	
		
	public function getNewsInfos($id){
		//on va chercher dans la DB
		$query = 'SELECT '.DB_PREFIX.'news.hits AS "hits", '.DB_PREFIX.'news_category.link_id AS "link_id", '.DB_PREFIX.'news_category.title AS "category_title", '.DB_PREFIX.'news.category_id AS "category_id", '.DB_PREFIX.'news.date_added AS "date_added", '.DB_PREFIX.'news.id AS "id", '.DB_PREFIX.'news.title AS "title", '.DB_PREFIX.'languages.code AS "code", '.DB_PREFIX.'news.meta_title AS "meta_title", '.DB_PREFIX.'news.meta_description AS "meta_description", '.DB_PREFIX.'news.meta_keywords AS "meta_keywords", '.DB_PREFIX.'news.content AS "content" FROM '.DB_PREFIX.'news INNER JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'news.language_id INNER JOIN '.DB_PREFIX.'news_category ON '.DB_PREFIX.'news_category.id = '.DB_PREFIX.'news.category_id WHERE '.DB_PREFIX.'news.id = "'.$id.'" AND '.DB_PREFIX.'news.status = "1" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0];
			}
		return false;
		}	

	public function setContentHits($id){	
		$query = 'UPDATE '.DB_PREFIX.'news SET '.DB_PREFIX.'news.hits = '.DB_PREFIX.'news.hits + 1 WHERE '.DB_PREFIX.'news.id = "'.$id.'";';
		$this->reg->get('db')->query($query);
		return true;
		}	
	
	}



//END