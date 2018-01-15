/*

draw an onglet base on global cieStyle

*/

dynamic class graphic.CieOnglet{
	
	static private var __className:String = 'CieOnglet';
	
	function CieOnglet(mv:MovieClip, width:Number, height:Number, radius:Number, fillColor:Number, fillAlpha:Number){
		
		if(mv != undefined){
			//box
			mv.beginFill(fillColor, fillAlpha);
			mv.moveTo(0, height);
			mv.lineTo(0, radius);
			mv.curveTo(0, 0, radius, 0);
			mv.lineTo(width - radius , 0);
			mv.curveTo(width, 0, width, radius);
			mv.lineTo(width, height);
			mv.endFill();
			
			//line
			mv.lineStyle(CieStyle.__tab.__borderWidth, CieStyle.__tab.__borderColor, 100);
			mv.moveTo(0, height);
			mv.lineTo(0, radius);
			mv.curveTo(0, 0, radius, 0);
			mv.lineTo(width - radius , 0);
			mv.curveTo(width, 0, width, radius);
			mv.lineTo(width, height);
			}
		};

	}