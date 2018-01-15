dynamic class graphic.CieSquare{
	
	static private var __className:String = 'CieSquare';
	
	private var __main:MovieClip;
	private var __styleSquare:Array;
	private var __styleBorder:Array;
	private var __x:Number;
	private var __y:Number;
	private var __w:Number;
	private var __h:Number;
	private var __c:Number;
	private var __xBorder:Number;
	private var __yBorder:Number;
	private var __wBorder:Number;
	private var __hBorder:Number;
	private var __cBorder:Number;
	
	//default style
	static var STYLESQUARE:Array = new Array(0xdddddd, 100); //[color, alpha, border(width, color, alpha)]
	static var STYLEBORDER:Array = new Array('int', 2, 0xcccccc, 100); //[color, alpha, border(width, color, alpha)]
	static var CELLPADDING:Number = 5; 
	/***************************************************************************************************************************************/

	public function CieSquare(mvMain:MovieClip, x:Number, y:Number, w:Number, h:Number, iCurve:Number, arrSquareStyle:Array, arrBorderStyle:Array){
		
		if(mvMain != undefined && typeof(mvMain) == 'movieclip'){
			this.__main = mvMain;
		}else{
			this.__main = _level0;
			}
		// POSITION
		this.__x = x;
		this.__y = y;
		this.__w = w;
		this.__h = h;
		this.__c = iCurve;
		// STYLE SQUARE
		if(arrSquareStyle != undefined && arrSquareStyle.length == 2){
			this.__styleSquare = arrSquareStyle;
		}else{
			this.__styleSquare = STYLESQUARE;
			}
		// STYLE BORDER	
		if(arrBorderStyle != undefined && arrBorderStyle.length == 4){
			this.__styleBorder = arrBorderStyle;
		}else{
			this.__styleBorder = STYLEBORDER;
			}
			
		if((this.__c + this.__c) > this.__h){
			this.__c = this.__c - this.__h;
			}
			
		// DRAW SQUARE
		this.drawSquare();
	}
	
	public function changeColor(arrSquareStyle:Array, arrBorderStyle:Array):Void{
		if(arrSquareStyle != undefined && arrSquareStyle.length == 2){
			this.__styleSquare = arrSquareStyle;
		}else{
			this.__styleSquare = STYLESQUARE;
			}
			
		if(arrBorderStyle != undefined && arrBorderStyle.length == 4){
			this.__styleBorder = arrBorderStyle;
		}else{
			this.__styleBorder = STYLEBORDER;
			}
		//trace("main: " + this.__main);	
		this.__main.clear();
		
		this.drawSquare();
		}
	
	private function drawSquare():Void{	
		if (this.__styleBorder[0] == 'int'){
			this.__xBorder = this.__x + this.__styleBorder[1];
			this.__yBorder = this.__y + this.__styleBorder[1];
			this.__wBorder = this.__w - this.__styleBorder[1]*2;
			this.__hBorder = this.__h - this.__styleBorder[1]*2;
			this.__cBorder = this.__c - this.__styleBorder[1]; 
			
			this.__main.beginFill(this.__styleBorder[2], this.__styleBorder[3]);
			this.drawBorder(this.__c);
			this.__main.beginFill(this.__styleSquare[0], this.__styleSquare[1]);
			this.drawInt(this.__cBorder);
			
		}else if (this.__styleBorder[0] == 'ext'){
			this.__xBorder = this.__x - this.__styleBorder[1];
			this.__yBorder = this.__y - this.__styleBorder[1];
			this.__wBorder = this.__w + this.__styleBorder[1]*2;
			this.__hBorder = this.__h + this.__styleBorder[1]*2;
			this.__cBorder = this.__c + this.__styleBorder[1]; 
			
			this.__main.beginFill(this.__styleBorder[2], this.__styleBorder[3]);
			this.drawInt(this.__cBorder);
			this.__main.beginFill(this.__styleSquare[0], this.__styleSquare[1]);
			this.drawBorder(this.__c);		
			}		
		}	
		
	private function drawBorder(c):Void{	
		this.__main.moveTo(this.__x, (this.__y+c));
this.__main.curveTo(this.__x, this.__y, (this.__x+c), this.__y);
		this.__main.lineTo((this.__x+this.__w)-c, this.__y);
		this.__main.curveTo((this.__x+this.__w), this.__y, (this.__x+this.__w), (this.__y+c));
		this.__main.lineTo((this.__x+this.__w), (this.__y+this.__h)-c);
		this.__main.curveTo((this.__x+this.__w), (this.__y+this.__h), (this.__x+this.__w)-c, (this.__y+this.__h));
		this.__main.lineTo((this.__x+c), (this.__y+this.__h));
		this.__main.curveTo(this.__x, (this.__y+this.__h), this.__x, (this.__y+this.__h)-c);
		this.__main.lineTo(this.__x, (this.__y+c));
		this.__main.endFill();
		}
	
	private function drawInt(iCurve):Void{		
		this.__main.moveTo(this.__xBorder, (this.__yBorder+iCurve));
		this.__main.curveTo(this.__xBorder, this.__yBorder, (this.__xBorder+iCurve), this.__yBorder);
		this.__main.lineTo((this.__xBorder+this.__wBorder)-iCurve, this.__yBorder);
		this.__main.curveTo((this.__xBorder+this.__wBorder), this.__yBorder, (this.__xBorder+this.__wBorder), (this.__yBorder+iCurve));
		this.__main.lineTo((this.__xBorder+this.__wBorder), (this.__yBorder+this.__hBorder)-iCurve);
		this.__main.curveTo((this.__xBorder+this.__wBorder), (this.__yBorder+this.__hBorder), (this.__xBorder+this.__wBorder)-iCurve, (this.__yBorder+this.__hBorder));
		this.__main.lineTo((this.__xBorder+iCurve), (this.__yBorder+this.__hBorder));
		this.__main.curveTo(this.__xBorder, (this.__yBorder+this.__hBorder), this.__xBorder, (this.__yBorder+this.__hBorder)-iCurve);
		this.__main.lineTo(this.__xBorder, (this.__yBorder+iCurve));
		this.__main.endFill();
		};
	
	private function getHeight(Void):Number{
		return this.__h;
		};
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieSquare{
		return this;
		};
	*/	
	}