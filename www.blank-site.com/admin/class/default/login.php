<?php
class Login {
	
	private $reg;
	
	public function __construct($reg) {
		$this->reg = $reg;
		}
		
		//------------------------------------------------------------------------------------------------		
	
	public function doLogin($arr){
		$this->strMsg = '';
		$arrValues = array();
		$this->arrFormErrors = array();
		if(isset($arr) && is_array($arr) && count($arr)){
			foreach($arr as $k=>$v){
				if($v['name'] == 'user'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('missing user').'</li>';
						}
					$arrValues['user'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'psw'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('missing psw').'</li>';
						}
					$arrValues['psw'] = sqlSafe($v['value']);	
				}else if($v['name'] == 'captcha'){
					if($v['value'] == ''){
						array_push($this->arrFormErrors, $v['name']);
						$this->strMsg .= '<li>'._T('missing captcha').'</li>';
						}
					$arrValues['captcha'] = sqlSafe($v['value']);	
					}
				}	
			//
			if(count($this->arrFormErrors) != 0){
				return false;
			}else{
				if(!isset($arrValues['user']) || !isset($arrValues['psw']) || !isset($arrValues['captcha'])){
					$this->strMsg .= '<li>'._T('all fields must be filled.').'</li>';
					return false;
					}
				//on a tout alors on commence par le captcha avant les infos de DB
				$arrSessLogin = $this->reg->get('sess')->get('login');
				if(is_array($arrSessLogin) && isset($arrSessLogin['captcha'])){
					if($arrSessLogin['captcha'] != $arrValues['captcha']){
						array_push($this->arrFormErrors, 'captcha');
						$this->strMsg .= '<li>'._T('captcha is invalid.').'</li>';
						return false;
						}
					}
				//on check le user/psw
				$arrUserInfos = $this->checkUser($arrValues['user'], $arrValues['psw']);
				if(!$arrUserInfos){
					$this->strMsg .= '<li>'._T('invalid login.').'</li>';
					return false;
					}
				//si on est la c'est que tout le reste est valide, alors on set la session ID et le reste des infos
				$this->reg->get('sess')->put('sess_id', $this->reg->get('sess')->getSessionID());	
				$this->reg->get('sess')->put('login', array(
					'captcha' => $arrSessLogin['captcha'],
					'user_name' => $arrValues['user'],
					'user_id' => $arrUserInfos['id'],
					'user_group' => $arrUserInfos['group_id'],
					'user_email' => $arrUserInfos['email'],
					'lastdate' => time(),
					));
				//on set les display select and the page numbers for visual interface
				$this->reg->get('sess')->put('content-listing', array(
					'filter-display-id' => '1',
					'filter-categorie-id' => '0',
					'filter-language-id' => '0',
					'filter-sortby-id' => '0',
					'filter-searchitems-id' => '',
					'last-page-number' => '0',
					));
				$this->reg->get('sess')->put('menu-listing', array(
					'filter-display-id' => '1',
					'filter-language-id' => '0',
					'filter-menu-group-id' => '0',
					));
				$this->reg->get('sess')->put('users-listing', array(
					'filter-display-id' => '1',
					'filter-sortby-id' => '0',
					'filter-searchitems-id' => '',
					'last-page-number' => '0',
					));
				$this->reg->get('sess')->put('news-listing', array(
					'filter-display-id' => '1',
					'filter-categorie-id' => '0',
					'filter-language-id' => '0',
					'filter-sortby-id' => '0',
					'filter-searchitems-id' => '',
					'last-page-number' => '0',
					));
				return true;
				}
			}
		return false;	
		}
		
		
	//------------------------------------------------------------------------------------------------				
		
	public function doLogout() {
		$this->reg->get('sess')->clear();
		$this->reg->get('sess')->close();
		//si on a une session
		if(isset($_COOKIE['PHPSESSID'])){
			$this->reg->get('sess')->destroy($_COOKIE['PHPSESSID']);	
			setcookie('PHPSESSID', '');
			}
		
		return true;
		}	
		
	//------------------------------------------------------------------------------------------------			
	
	public function checkUser($user, $psw){
		//query
		$query = 'SELECT '.DB_PREFIX.'admin.id AS "id", '.DB_PREFIX.'admin.email AS "email", '.DB_PREFIX.'admin.group_id AS "group_id" FROM '.DB_PREFIX.'admin WHERE '.DB_PREFIX.'admin.username = "'.$user.'" AND '.DB_PREFIX.'admin.password = "'.$psw.'" LIMIT 0,1;';
		$rs = $this->reg->get('db')->query($query);
		if($rs->num_rows){
			return $rs->rows[0];
			}
		return false;
		}
		
	//------------------------------------------------------------------------------------------------		
		
	public function isLogued(){
		if(isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID'].'' != ''){
			if(!$this->reg->get('sess')->validate($_COOKIE['PHPSESSID'])){
				return false;
				}
			if(!$this->reg->get('sess')->get('sess_id')){
				return false;
				}
			if($this->reg->get('sess')->get('sess_id') != $this->reg->get('sess')->getSessionID()){
				return false;
				}
			$arrUserInfos = $this->reg->get('sess')->get('login');
			//la abse des droits
			if(is_array($arrUserInfos)){
				if(!isset($arrUserInfos['user_id']) || !isset($arrUserInfos['user_group']) || !isset($arrUserInfos['user_name']) || $arrUserInfos['user_name'] == '' || intVal($arrUserInfos['user_id']) <= 0 || intVal($arrUserInfos['user_group']) <= 0){
					return false;
					}
			}else{
				return false;
				}
		}else{
			return false;
			}
		return true;
		}	

	//------------------------------------------------------------------------------------------------		
		
	public function createCaptcha($str){		
		//ob_start();
		$im = imagecreatetruecolor(45, 27);
		$text_color = imagecolorallocate($im, 255, 255, 255);
		imagestring($im, 4, 5, 5,  $str, $text_color);
		//header('Content-Type: image/jpeg');
		imagejpeg($im);
		//imagedestroy($im);
		//$out = ob_get_clean();
		//return "<img src='data:image/jpeg;base64," . base64_encode($im)."'>";
		return base64_encode($im);
		}
		
		
	//------------------------------------------------------------------------------------------------		
		
	public function getFormErrors(){
		return $this->arrFormErrors;
		}
		
	//------------------------------------------------------------------------------------------------		
		
	public function getMsgErrors(){
		return $this->strMsg;
		}	
		
		
		
		
	
	}
?>