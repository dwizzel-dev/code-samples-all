import flash.filters.DropShadowFilter;
//import manager.CieStageManager;

dynamic class effect.CieDropShadow{
	
	static private var __className = 'CieDropShadow';
		
	public function CieDropShadow(mv:MovieClip, distance:Number, alpha:Number, strength:Number, quality:Number){
		if(mv != undefined){
			var filter:DropShadowFilter = new DropShadowFilter(
				distance, 
				270, 
				CieStyle.__tab.__effectShadowColor, 
				alpha, 
				6, 
				6, 
				strength, 
				quality, 
				false, 
				false, 
				false);
		
			var filterArray:Array = new Array();
			filterArray.push(filter);
			mv._alpha = 100;
			mv.filters = filterArray;
			}
		};
	};