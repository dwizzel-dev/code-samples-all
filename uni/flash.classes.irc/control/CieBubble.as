//import graphic.CieSquare;
import control.CieTextLine;
//import effect.CieDropShadow;

import flash.filters.DropShadowFilter;

dynamic class control.CieBubble{

	static private var __className = 'CieBubble';
	static var CELLPADDING:Number = 6; 
	
	private var __mv:MovieClip;
	
	public function CieBubble(mv:MovieClip, h:Number, w:Number, curve:Number, strName:String, strText:String, arrBorderStyle:Array, arrSquareStyle:Array){
		this.__mv = mv.createEmptyMovieClip('MV_BUBBLE', mv.getNextHighestDepth());
		var mvTexte = this.__mv.attachMovie('mvTextBubbles', 'BUBBLE', this.__mv.getNextHighestDepth());
		mvTexte.txtInfos.htmlText = strText;
		mvTexte.txtInfos.autoSize = 'left';	
		if (this.getTextWidth(strText) < 120){
			mvTexte._width = this.getTextWidth(strText);
		}else{
			mvTexte._width = 120;
			}
		var xBorder:Number = (mvTexte._x - CELLPADDING); 
		var yBorder:Number = (mvTexte._y - CELLPADDING);
		var wBorder:Number = (mvTexte._width + (CELLPADDING * 2)); 
		var hBorder:Number = (mvTexte._height + (CELLPADDING * 2));
				
		//draw square
		this.__mv.lineStyle(1.5, 0xffffff, 100);
		this.__mv.beginFill(0xffffff, 80);
		this.__mv.moveTo(xBorder, (yBorder+curve));
		this.__mv.curveTo(xBorder, yBorder, (xBorder+curve), yBorder);
		this.__mv.lineTo((xBorder+wBorder)-curve, yBorder);
		this.__mv.curveTo((xBorder+wBorder), yBorder, (xBorder+wBorder), (yBorder+curve));
		this.__mv.lineTo((xBorder+wBorder), (yBorder+hBorder)-curve);
		this.__mv.curveTo((xBorder+wBorder), (yBorder+hBorder), (xBorder+wBorder)-curve, (yBorder+hBorder));
		this.__mv.lineTo((xBorder+curve), (yBorder+hBorder));
		this.__mv.curveTo(xBorder, (yBorder+hBorder), xBorder, (yBorder+hBorder)-curve);
		this.__mv.lineTo(xBorder, (yBorder+curve));
		this.__mv.endFill();
		
		//put a shadow
		//new CieDropShadow(this.__mv, 3, 0.5, 1, 3);
		var filter:DropShadowFilter = new DropShadowFilter(
				3, 
				60, 
				0x000000, 
				0.4, 
				6, 
				6, 
				2, 
				2, 
				false, 
				false, 
				false);
		
		var filterArray:Array = new Array();
		filterArray.push(filter);
		this.__mv.filters = filterArray;
		
		//p[lace it now
		//X
		if((_xmouse + this.__mv._width + CELLPADDING) > (Stage.width - BC.__banner.__rightWidth)){
			this.__mv._x = (Stage.width - BC.__banner.__rightWidth) - (this.__mv._width + CELLPADDING);
		}else{
			this.__mv._x = _xmouse;
			}
		//Y
		if(_ymouse < 0){
			this.__mv._y = CELLPADDING;
		}else if((_ymouse + this.__mv._height + CELLPADDING) > (Stage.height - BC.__banner.__bottomHeight)){
			this.__mv._y = (Stage.height - BC.__banner.__bottomHeight) - (this.__mv._height + CELLPADDING);
		}else{
			this.__mv._y = _ymouse;
			}
			
		
		/*
		//p[lace it now
		//X
		if((_xmouse + this.__mv._width + CELLPADDING) > Stage.width){
			this.__mv._x = Stage.width - (this.__mv._width + CELLPADDING);
		}else{
			this.__mv._x = _xmouse;
			}
		//Y
		if(_ymouse < 0){
			this.__mv._y = CELLPADDING;
		}else if((_ymouse + this.__mv._height + CELLPADDING) > Stage.height){
			this.__mv._y = Stage.height - (this.__mv._height + CELLPADDING);
		}else{
			this.__mv._y = _ymouse;
			}
		*/	
		};
	
	/*********************************************************************************************************************************************************/	
		
	public function getTextWidth(strText:String):Number{
		var mvTxt = this.__mv.createEmptyMovieClip('mvTxt', this.__mv.getNextHighestDepth());
		var txt = new CieTextLine(mvTxt, 0, 0, 50, 100, 'text', strText, "dynamic", [], false, false, false, false, []);
		var txtWidth = txt.getWidth();
		removeMovieClip(mvTxt);
		return txtWidth;
		};	
	
	
	/*********************************************************************************************************************************************************/
	public function destroy(Void):Void{
		this.__mv.removeMovieClip();
		delete this.__mv;
		};
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieBubble{
		return this;
		};
	*/	
	};