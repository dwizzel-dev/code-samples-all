/*

rounded square

*/

//import flash.filters.GlowFilter;

dynamic class graphic.CieRoundedSquare{
	
	static private var __className:String = 'CieRoundedSquare';
	
	function CieRoundedSquare(ctype:String, mv:MovieClip, width:Number, height:Number, radius:Number, fillColor:Number, fillAlpha:Number){
		if(mv != undefined)
			mv.lineStyle(CieStyle.__panel.__borderWidth, CieStyle.__panel.__borderColor, 100);
			mv.beginFill(fillColor, fillAlpha);
			mv.moveTo(radius, 0);
			mv.lineTo(width - radius, 0);
			mv.curveTo(width, 0, width, radius);
			mv.lineTo(width, radius);
			mv.lineTo(width, height - radius);
			mv.curveTo(width, height, width - radius, height);
			mv.lineTo(width - radius, height);
			mv.lineTo(radius, height);
			mv.curveTo(0, height, 0, height - radius);
			mv.lineTo(0, height - radius);
			mv.lineTo(0, radius);
			mv.curveTo(0, 0, radius, 0);
			mv.lineTo(radius, 0);
			mv.endFill();
			}
		};
	}