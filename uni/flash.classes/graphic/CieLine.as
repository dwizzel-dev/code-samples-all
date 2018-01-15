/*

line

*/

dynamic class graphic.CieLine{
	
	static private var __className:String = 'CieLine';
	
	function CieLine(mv:MovieClip, pos:Number, length:Number, height:Number, fillColor:Number, fillAlpha:Number, ctype:String){
		if(mv != undefined){
			if(ctype == 'H'){
				mv.lineStyle(height, fillColor, fillAlpha);
				mv.moveTo(pos, 0);
				mv.lineTo(length, 0);
			}else{
				mv.lineStyle(height, fillColor, fillAlpha);
				mv.moveTo(0, pos);
				mv.lineTo(0, length);
				}
			}	
		};
	}