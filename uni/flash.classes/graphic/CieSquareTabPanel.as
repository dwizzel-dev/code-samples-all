/*

square

*/

dynamic class graphic.CieSquareTabPanel{
	
	static private var __className:String = 'CieSquareTabPanel';
	
	function CieSquareTabPanel(mv:MovieClip, width:Number, height:Number, fillColor:Number, fillAlpha:Number){
		
		if(mv != undefined){
			mv.beginFill(fillColor, fillAlpha);
			mv.moveTo(0, 0);
			mv.lineTo(width, 0);
			mv.lineTo(width, height);
			mv.lineTo(0, height);
			mv.lineTo(0, 0);
			mv.endFill();
			}
		};
	}