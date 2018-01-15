dynamic class graphic.CieCornerSquare{
	
	static var __className:String = "CieCornerSquare";
	
	private var __main:MovieClip;
	private var __styleSquare:Array;
	private var __styleBorder:Array;
	private var __arrCurve:Array;
	private var __x:Number;
	private var __y:Number;
	private var __w:Number;
	private var __h:Number;
	private var __cTr:Number;
	private var __cTl:Number;
	private var __cBr:Number;
	private var __cBl:Number;
	
	private var __xBorder:Number;
	private var __yBorder:Number;
	private var __wBorder:Number;
	private var __hBorder:Number;
	private var __cTrBorder:Number;
	private var __cTlBorder:Number;
	private var __cBrBorder:Number;
	private var __cBlBorder:Number;

	//default style
	static var STYLESQUARE:Array = new Array(0xFFCC00, 100); //[color, alpha, border(width, color, alpha)]
	static var STYLEBORDER:Array = new Array('int', 2, 0x000000, 100); //[color, alpha, border(width, color, alpha)]
	static var CURVE:Array = new Array(0, 0, 0, 0); //[color, alpha, border(width, color, alpha)]
	static var CELLPADDING:Number = 5; 
	
	/***************************************************************************************************************************************/

	public function CieCornerSquare(mvMain:MovieClip, x:Number, y:Number, w:Number, h:Number, arrCurve:Array, arrSquareStyle:Array, arrBorderStyle:Array){
		
		if(mvMain != undefined && typeof(mvMain) == 'movieclip'){
			this.__main = mvMain;
		}else{
			this.__main = _level0;
			}

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
			
		if(arrCurve != undefined && arrCurve.length == 4){
			this.__arrCurve = arrCurve;
		}else{
			this.__arrCurve = CURVE;
			}

		this.__x = x;
		this.__y = y;
		this.__w = w;
		this.__h = h;
		this.__cTr = this.__arrCurve[0];
		this.__cTl = this.__arrCurve[1];
		this.__cBr = this.__arrCurve[2];
		this.__cBl = this.__arrCurve[3];
		
		if((this.__c + this.__c) > this.__h){
			this.__c = this.__c - this.__h;
			}
			
		this.drawSquare();
		}
	
	public function changeColor(arrSquareStyle:Array, arrBorderStyle:Array):Void{
		this.__styleSquare = arrSquareStyle;
		this.__styleBorder = arrBorderStyle;
		this.__main.clear();
		this.drawSquare();
		}
	
	private function drawSquare():Void{	
		if (this.__styleBorder[0] == 'int'){
			this.__xBorder = this.__x + this.__styleBorder[1];
			this.__yBorder = this.__y + this.__styleBorder[1];
			this.__wBorder = this.__w - this.__styleBorder[1]*2;
			this.__hBorder = this.__h - this.__styleBorder[1]*2;
			this.__cTrBorder = this.__cTr - this.__styleBorder[1]; 
			this.__cTlBorder = this.__cTl - this.__styleBorder[1]; 
			this.__cBrBorder = this.__cBr - this.__styleBorder[1]; 
			this.__cBlBorder = this.__cBl - this.__styleBorder[1]; 
			
			this.__main.beginFill(this.__styleBorder[2], this.__styleBorder[3]);
			this.drawBorder();
			this.__main.beginFill(this.__styleSquare[0], this.__styleSquare[1]);
			this.drawInt();
			
		}else if (this.__styleBorder[0] == 'ext'){
			this.__xBorder = this.__x - this.__styleBorder[1];
			this.__yBorder = this.__y - this.__styleBorder[1];
			this.__wBorder = this.__w + this.__styleBorder[1]*2;
			this.__hBorder = this.__h + this.__styleBorder[1]*2;
			this.__cTrBorder = this.__cTr + this.__styleBorder[1]; 
			this.__cTlBorder = this.__cTl + this.__styleBorder[1]; 
			this.__cBrBorder = this.__cBr + this.__styleBorder[1]; 
			this.__cBlBorder = this.__cBl + this.__styleBorder[1]; 
			
			this.__main.beginFill(this.__styleBorder[2], this.__styleBorder[3]);
			this.drawInt(this.__cBorder);
			this.__main.beginFill(this.__styleSquare[0], this.__styleSquare[1]);
			this.drawBorder(this.__c);		
			}		
		}	
		
	private function drawBorder():Void{	
		
		this.__main.moveTo(this.__x, (this.__y+this.__cTr));
		this.__main.curveTo(this.__x, this.__y, (this.__x+this.__cTr), this.__y);
		
		this.__main.lineTo((this.__x+this.__w)-this.__cBr, this.__y);
		this.__main.curveTo((this.__x+this.__w), this.__y, (this.__x+this.__w), (this.__y+this.__cBr));
		
		this.__main.lineTo((this.__x+this.__w), (this.__y+this.__h)-this.__cBl);
		this.__main.curveTo((this.__x+this.__w), (this.__y+this.__h), (this.__x+this.__w)-this.__cBl, (this.__y+this.__h));
		
		this.__main.lineTo((this.__x+this.__cTl), (this.__y+this.__h));
		this.__main.curveTo(this.__x, (this.__y+this.__h), this.__x, (this.__y+this.__h)-this.__cTl);
		
		this.__main.lineTo(this.__x, (this.__y+this.__cTr));
		
		}
	
	private function drawInt():Void{	
		
		this.__main.moveTo(this.__xBorder, (this.__yBorder+this.__cTrBorder));
		this.__main.curveTo(this.__xBorder, this.__yBorder, (this.__xBorder+this.__cTrBorder), this.__yBorder);
		
		this.__main.lineTo((this.__xBorder+this.__wBorder)-this.__cBrBorder, this.__yBorder);
		this.__main.curveTo((this.__xBorder+this.__wBorder), this.__yBorder, (this.__xBorder+this.__wBorder), (this.__yBorder+this.__cBrBorder));
		
		this.__main.lineTo((this.__xBorder+this.__wBorder), (this.__yBorder+this.__hBorder)-this.__cBlBorder);
		this.__main.curveTo((this.__xBorder+this.__wBorder), (this.__yBorder+this.__hBorder), (this.__xBorder+this.__wBorder)-this.__cBlBorder, (this.__yBorder+this.__hBorder));
		
		this.__main.lineTo((this.__xBorder+this.__cTlBorder), (this.__yBorder+this.__hBorder));
		this.__main.curveTo(this.__xBorder, (this.__yBorder+this.__hBorder), this.__xBorder, (this.__yBorder+this.__hBorder)-this.__cTlBorder);
		
		this.__main.lineTo(this.__xBorder, (this.__yBorder+this.__cTrBorder));

		};
	/*
	public function getClass(Void):CieCornerSquare{
		return this;
		};
		
	public function getClassName(Void):String{
		return __className;
		};
	*/	
	}