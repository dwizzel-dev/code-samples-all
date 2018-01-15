<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	pagination model

*/



class widgetPaginationModel{
	
	//vars
	private $linkId;
	private $total;
	private $start;
	private $max;
	
	//construct
	public function __construct($total, $start, $max, $linkId){
		$this->total = $total;
		$this->start = $start;
		$this->max = $max;
		$this->linkId = $linkId;
		}
	
	//get the carousel data	
	public function getData(){
		$iMaxPaginationBox = 10;
		//on va chercher dans la DB
		$arr = array(
			'total_count' => $this->total,
			'link_id' => $this->linkId,
			'max_pagination' => $iMaxPaginationBox,
			'page_offset' => $iMaxPaginationBox - 1,
			'max_per_page' => $this->max,
			'start_page' => $this->start,
			);
		return $arr;
		}
		
	}
	
	
	
	
	
	
//END	
	
	
	