import flash.filters.BlurFilter;

dynamic class effect.CieBlur{
	
	static private var __className = 'CieBlur';
			
	public function CieBlur(mv:MovieClip, iBlurX:Number, iBlurY:Number){
		if(mv != undefined){
			var filter:BlurFilter = new BlurFilter(iBlurX, iBlurY, 3);
			var filterArray:Array = new Array();
			filterArray.push(filter);
			mv.filters = filterArray;
			}
		};
		
	};
 

 












 
