<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	basic menu for admin not from DB but coded directly here
		1 = super admin
		2 = admin
		3 = report
		4 = translator
		5 = blogger
		6 = designer



*/

class Menu {
	
	private $arr = array();
	private $glob;	
	private $reg;		
	
	public function __construct(&$reg, &$glob) {
		$this->reg = $reg;	
		$this->glob = $glob;	
		
		//english admin menu
		$this->arr['fr_CA'] = array(
			array(
				'name' => _T('system'),
				'link_id' => 0,
				'group_ids' => array(1,2,4),		
				'child' => array(
					array(
						'name' => _T('translation'),
						'description' => _T('simple text used all across the website that are not in the database'),
						'menu_type' => 0,
						'link_id' => 'translation',
						'group_ids' => array(1,2,4),				
						'child' => false, 
						),
					array(
						'name' => _T('datafields'),
						'description' => _T('database fields used all across the website: conseil, reponse, etc...'),
						'menu_type' => 0,
						'link_id' => 'datafields',
						'group_ids' => array(1,2,4),						
						'child' => false, 
						),
					array(
						'name' => _T('config'),
						'description' => _T('basic site configuration: offline, path, debug, etc...'),
						'menu_type' => 0,
						'link_id' => 'config',
						'group_ids' => array(1,2),						
						'child' => false, 
						),	
					array(
						'name' => _T('users'),
						'description' => _T('users administration'),
						'menu_type' => 0,
						'link_id' => 'users-listing',
						'group_ids' => array(1,2),						
						'child' => false,			
						),	
					),			
				),
				
			
				
			array(
				'name' => _T('content'),
				'link_id' => 0,
				'group_ids' => array(1,2,6),					
				'child' => array(
					array(
						'name' => _T('links'),
						'description' => _T('links used by the website'),
						'menu_type' => 0,
						'link_id' => 'links',
						'group_ids' => array(1,2),							
						'child' => false, 
						),
					array(
						'name' => _T('menu'),
						'description' => _T('menu used by the website'),
						'menu_type' => 0,
						'link_id' => 'menu',
						'group_ids' => array(1,2),
						'child' => false,
						),
					array(
						'name' => _T('widgets'),
						'description' => _T('little gadgets used by the website: carousel, socialmedia, etc...'),
						'menu_type' => 0,
						'link_id' => 'widget',
						'group_ids' => array(1,2,6),
						'child' => false,
						),	
					array(
						'name' => _T('categories'),
						'description' => _T('content category listing'),
						'menu_type' => 0,
						'link_id' => 'content-category',
						'group_ids' => array(1,2),	
						'child' => false,
						),	
					array(
						'name' => _T('items'),
						'description' => _T('page content listing'),
						'menu_type' => 0,
						'link_id' => 'content-listing',
						'group_ids' => array(1,2,6),		
						'child' => false,
						),	
					),			
				),
			array(
				'name' => _T('news'),
				'link_id' => 0,
				'group_ids' => array(1,2,5),	
				'child' => array(
					array(
						'name' => _T('categories'),
						'description' => _T('news categories listing'),
						'menu_type' => 0,
						'link_id' => 'news-category',
						'group_ids' => array(1,2),	
						'child' => false, 
						),
					array(
						'name' => _T('items'),
						'description' => _T('news items listing'),
						'menu_type' => 0,
						'link_id' => 'news-listing',
						'group_ids' => array(1,2,5),	
						'child' => false, 
						),	
					),			
				),
				
			array(
				'name' => _T('logout'),
				'link_id' => 'logout',
				'group_ids' => false,	
				'child' => ''	
				),	
						
			);
		
		
		
		//english admin menu
		$this->arr['en_US'] = array();		
		}
		
	public function getMenuTree(){
		//parse selon le group dans lequel il est
		
		//check la sess	
		if(!$this->reg->get('sess')){
			return false;
			}
		//check si un group id
		if(!$this->reg->get('sess')->get('login')){
			return false;
			}
		//on garde juste les menu auquel il a droit
		$userLogin = $this->reg->get('sess')->get('login');
		//minor check
		if($userLogin['user_group'] == '' || $userLogin['user_group'] ===  false || $userLogin['user_group'] === 0 || $userLogin['user_group'] == '0'){
			return false;
			}
		//peu secure pour l'instant doit le faire dans chaque controller	
		return $this->parseMenuTree(
			$this->arr[$this->glob->get('lang')],
			intVal($userLogin['user_group'])	
			);
		}
		
	private function parseMenuTree($arrMenu, $group, $arr = array()){
		
		foreach($arrMenu as $k=>$v){
			//check si dans le group id = 0 on ne fait pas de check et on le rajoute
			//si autres on verifie si est dedans
			if(!is_array($v['group_ids'])){
				$arr[$k] = $v;
				//check si sous menu		
				if(is_array($v['child'])){
					//alors opn efface le child que lon ramene et on repart dans la recursive
					$arr[$k]['child'] = $this->parseMenuTree($v['child'], $group, $arr[$k]['child']);		
					}
			}else{
				//si fait parti du group	
				if(in_array($group, $v['group_ids'])){	
					$arr[$k] = $v;
					//check si sous menu		
					if(is_array($v['child'])){
						//alors opn efface le child que lon ramene et on repart dans la recursive
						$arr[$k]['child'] = $this->parseMenuTree($v['child'], $group, $arr[$k]['child']);		
						}
				}else{
					unset($arr[$k]);	
					}
				}
				
			}
			
		return $arr;		
		}
		


	}
	
	
	
	
//END


