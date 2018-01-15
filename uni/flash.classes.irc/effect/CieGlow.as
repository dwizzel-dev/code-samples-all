import flash.filters.GlowFilter;
//import manager.CieStageManager;

dynamic class effect.CieGlow{
	
	static private var __className = 'CieGlow';
				
	public function CieGlow(mv:MovieClip, bInner:Boolean){
		if(mv != undefined){
			var filter:GlowFilter = new GlowFilter(
				CieStyle.__panel.__effectGlowColor, 
				CieStyle.__panel.__effectGlowAlpha, 
				CieStyle.__panel.__effectGlowSize, 
				CieStyle.__panel.__effectGlowSize, 
				2, 
				3, 
				bInner, 
				false
				);
			var filterArray:Array = new Array();
			filterArray.push(filter);
			mv.filters = filterArray;
			}
		};
	};
 












 
