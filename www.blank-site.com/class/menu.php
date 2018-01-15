<?php
class Menu {
	
	private $reg;
		
	public function __construct($reg) {
		$this->reg = $reg;
		}
		
	//------------------------------------------------------------------------------------------			
		
	public function getMenuTree($menu, $lang){
		$bFileExist = false;
		$menuFile = 'menu.'.$menu.'.'.$lang;
		//cache
		$arrTmp = $this->reg->get('cache')->cacheRead($menuFile);
		//check
		if(is_array($arrTmp)){
			$bFileExist = true;
		}else{
			$arrTmp = array();
			//premier niveau
			$query = 'SELECT '.DB_PREFIX.'menu.catalogue_cat_id AS "catalogue_cat_id", '.DB_PREFIX.'menu.description AS "description", '.DB_PREFIX.'menu.menu_type AS "menu_type", '.DB_PREFIX.'menu.id AS "id", '.DB_PREFIX.'menu.name AS "name", '.DB_PREFIX.'menu.link_id AS "link_id", '.DB_PREFIX.'menu.parent_id AS "parent_id" FROM '.DB_PREFIX.'menu INNER JOIN '.DB_PREFIX.'menu_group ON '.DB_PREFIX.'menu_group.id = '.DB_PREFIX.'menu.menu_group_id INNER JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'menu_group.language_id WHERE '.DB_PREFIX.'menu.status = "1" AND '.DB_PREFIX.'menu_group.name = "'.$menu.'" AND '.DB_PREFIX.'languages.code = "'.$lang.'"  AND '.DB_PREFIX.'menu.parent_id = "0" ORDER BY '.DB_PREFIX.'menu.position;';
			$rs = $this->reg->get('db')->query($query);
			if(!$rs->num_rows){
				if(!$bFileExist){
					$this->reg->get('cache')->cacheWrite($menuFile, $arrTmp);
					}
				return false;
				}
			foreach($rs->rows as $k=>$v){
				array_push($arrTmp, array(
					'id' => $v['id'], 
					'name' => $v['name'], 
					'link_id' => $v['link_id'], 
					'parent_id' => $v['parent_id'], 
					'menu_type' => $v['menu_type'], 
					'description' => $v['description'],
					'catalogue_cat_id' => $v['catalogue_cat_id'],
					'child' => false
					));
				}
			$this->recursiveMenuChildFromParentId($arrTmp, $menu, $lang);
			}
		if(!$bFileExist){
			$this->reg->get('cache')->cacheWrite($menuFile, $arrTmp);
			}
			
		return $arrTmp;
		}
		
	//------------------------------------------------------------------------------------------		
		
	private function recursiveMenuChildFromParentId(&$arr, $menu, $lang){ //arr by reference	
		foreach($arr as $k=>$v){
			$query = 'SELECT '.DB_PREFIX.'menu.catalogue_cat_id AS "catalogue_cat_id", '.DB_PREFIX.'menu.description AS "description", '.DB_PREFIX.'menu.menu_type AS "menu_type", '.DB_PREFIX.'menu.id AS "id", '.DB_PREFIX.'menu.name AS "name", '.DB_PREFIX.'menu.link_id AS "link_id", '.DB_PREFIX.'menu.parent_id AS "parent_id" FROM '.DB_PREFIX.'menu INNER JOIN '.DB_PREFIX.'menu_group ON '.DB_PREFIX.'menu_group.id = '.DB_PREFIX.'menu.menu_group_id INNER JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'menu_group.language_id WHERE '.DB_PREFIX.'menu.status = "1" AND '.DB_PREFIX.'menu_group.name = "'.$menu.'" AND '.DB_PREFIX.'languages.code = "'.$lang.'"  AND '.DB_PREFIX.'menu.parent_id = "'.$v['id'].'" ORDER BY '.DB_PREFIX.'menu.position;';
			$rs = $this->reg->get('db')->query($query);
			$arrTmp = array();
			foreach($rs->rows as $k2=>$v2){
				array_push($arrTmp, array(
					'id' => $v2['id'], 
					'name' => $v2['name'],
					'link_id' => $v2['link_id'], 		
					'parent_id' => $v2['parent_id'], 
					'menu_type' => $v2['menu_type'],
					'description' => $v2['description'],
					'catalogue_cat_id' => $v2['catalogue_cat_id'],
					'child' => false
					));
				}
			if(count($arrTmp)){
				$arr[$k]['child'] = $arrTmp;
				$this->recursiveMenuChildFromParentId($arr[$k]['child'], $menu, $lang);
				}
			}
		}	
		
			
	//------------------------------------------------------------------------------------------------		
	
	public function getMenuForCatalogueMainCategoryDisplay($id, $menu, $lang){
		$arrTmp = array();
		//premier niveau
		$query = 'SELECT '.DB_PREFIX.'menu.id AS "id" FROM '.DB_PREFIX.'menu WHERE '.DB_PREFIX.'menu.catalogue_cat_id = "'.$id.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if(!$rs->num_rows){
			return false;
			}
		array_push($arrTmp, array(
			'id' => $rs->rows[0]['id'], 
			'child' => false
			));
		$this->recursiveMenuChildFromParentId($arrTmp, $menu, $lang);
		if(isset($arrTmp[0]['child'])){
			$arrTmp = $arrTmp[0]['child'];
			}
		return $arrTmp;
		}
		
		
		
	}
?>