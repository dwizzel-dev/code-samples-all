<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	Frontpage model

*/



class widgetFrontpageModel{
	
	//vars
	private $reg;
	private $glob;	
	private $wname;

	//construct
	public function __construct(&$reg, &$glob, $wname){
		$this->reg = $reg;
		$this->glob = $glob;	
		$this->wname = $wname;
		}
	
	//get the carousel data	
	public function getData(){
		//vars
		$lang = $this->glob->get('lang');
		$arrLinks = $this->glob->get('links');
		//cache
		$widgetFileName = 'widget.'.$this->wname.'.'.$lang;
		$bFileExist = false;
		$arrData = $this->reg->get('cache')->cacheRead($widgetFileName);	
		if(is_array($arrData)){
			return $arrData;
		}else{
			//on va chercher dans la DB
			$query = 'SELECT '.DB_PREFIX.'widget.data AS "data" FROM '.DB_PREFIX.'widget INNER JOIN '.DB_PREFIX.'languages ON '.DB_PREFIX.'languages.id = '.DB_PREFIX.'widget.language_id WHERE '.DB_PREFIX.'widget.status = "1" AND '.DB_PREFIX.'widget.alias = "'.$this->wname.'" AND '.DB_PREFIX.'languages.code = "'.$lang.'" LIMIT 0,1;';
			$rs = $this->reg->get('db')->query($query);
			if($rs->num_rows){
				$arrData = unserializeFromDbData($rs->rows[0]['data']);
				array_walk_recursive($arrData,'formatSerializeRev');
				//on garde jsute ceux qui sont actif
				foreach($arrData as $k=>$v){
					//on change le link id pour un lien reel
					if(intVal($v['link'])){
						if(isset($arrLinks[$v['link']])){
							$arrData[$k]['link'] = $arrLinks[$v['link']];
						}else{
							$arrData[$k]['link'] = '';
							}
					}else{
						$arrData[$k]['link'] = '';
						}
					}
				$arrData = $arrData;
				if(count($arrData)){
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
	
	
	