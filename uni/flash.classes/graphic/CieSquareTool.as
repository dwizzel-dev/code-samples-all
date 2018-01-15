/*

square for tools

*/

dynamic class graphic.CieSquareTool{
	
	static private var __className:String = 'CieSquareTool';
	
	function CieSquareTool(mv:MovieClip, w:Number, h:Number, fillColor:Number, fillAlpha:Number){
		if(mv != undefined){
			mv.beginFill(fillColor, fillAlpha);
			mv.moveTo(0, 0);
			mv.lineTo(w, 0);
			mv.lineTo(w, h);
			mv.lineTo(0, h);
			mv.lineTo(0, 0);
			mv.endFill();
			}
		};
	}