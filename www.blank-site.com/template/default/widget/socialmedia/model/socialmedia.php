<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	socialmedia model

*/



class widgetSocialMediaModel{
	
	//vars
	private $reg;
	private $wname;
	private $lang;
	
	//construct
	public function __construct(&$reg, $wname, $lang){
		$this->reg = $reg;
		$this->wname = $wname;
		$this->lang = $lang;
		}
	
	//get the carousel data	
	public function getData(){
		//test data
		//cache
		$widgetFileName = 'widget.'.$this->wname.'.'.$this->lang;
		$bFileExist = false;
		$arrData = $this->reg->get('cache')->cacheRead($widgetFileName);	
		if(is_array($arrData)){
			return $arrData;
		}else{
			//on va chercher dans la DB
			$query = 'SELECT '.DB_PREFIX.'widget.data AS "data" FROM '.DB_PREFIX.'widget INNER JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'widget.language_id WHERE '.DB_PREFIX.'widget.status = "1" AND '.DB_PREFIX.'widget.alias = "'.$this->wname.'"  AND '.DB_PREFIX.'languages.code = "'.$this->lang.'" LIMIT 0,1;';
			$rs = $this->reg->get('db')->query($query);
			if($rs->num_rows){
				$arrData = unserialize(str_replace('&quot;','"',$rs->rows[0]['data']));
				if($arrData){
					//cache
					$this->reg->get('cache')->cacheWrite($widgetFileName, $arrData);
					return $arrData;
					}
				}
			}
		return false;
		}
		
	}
	
	
	
	
	
	
//END	
	
	
	