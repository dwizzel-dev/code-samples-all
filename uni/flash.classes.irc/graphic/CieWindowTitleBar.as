/*

draw a title bar

*/
import flash.geom.Matrix;
import control.CieTextLine;

dynamic class graphic.CieWindowTitleBar{
	
	static private var __className:String = 'CieWindowTitleBar';
	private var __boxWidth:Number;
	private var __iMarge:Number;
	private var __iCloseButtWidth:Number;
	private var __minimumTitleBarWidth:Number;
	private var __title:CieTextLine;
	private var __mv:MovieClip;
	
	function CieWindowTitleBar(mv:MovieClip, texte:String, w:Number){
		var hBox = 17;
		this.__mv = mv;
		this.__iMarge = 10;
		this.__minimumTitleBarWidth = 200;
		this.__iCloseButtWidth = 14;
		this.__title = new CieTextLine(this.__mv, 0, 0, 0, hBox, 'title', texte, 'dynamic', [true, false, false], false, false, false, false, [CieStyle.__window.__titleFontColor, 11]);
		if (w != 0){
			this.__boxWidth = w;
		}else{
			if(this.__title.getWidth() < this.__minimumTitleBarWidth){			
				//12 pour le width du bouton
				this.__boxWidth = Math.floor(this.__iMarge + this.__minimumTitleBarWidth + this.__iMarge + this.__iCloseButtWidth + this.__iMarge);
			}else{
				this.__boxWidth = Math.floor(this.__iMarge + this.__title.getWidth() + this.__iMarge + this.__iCloseButtWidth + this.__iMarge);
				}
			}
		this.drawBG();
		};
		
	public function drawBG(Void):Void{
		//box
		var boxHeight = 36; //a pixel more because the line still appears when effect glow is on in CieWindows.__titleBarHeight
		var myMatrix:Matrix = new Matrix();
		myMatrix.createGradientBox(this.__boxWidth, boxHeight, 11, 0, 0);
		var colors:Array = [CieStyle.__window.__bottomGradienColor, CieStyle.__window.__topGradienColor]; 
		var alphas:Array = [100, 100];
		var ratios:Array = [0, 0xFF];
		//draw
		this.__mv.beginGradientFill('linear', colors, alphas, ratios, myMatrix);
		this.__mv.moveTo(0, boxHeight);
		this.__mv.lineTo(0, CieStyle.__panel.__borderRadius);
		this.__mv.curveTo(0, 0, CieStyle.__panel.__borderRadius, 0);
		this.__mv.lineTo(this.__boxWidth - CieStyle.__panel.__borderRadius , 0);
		this.__mv.curveTo(this.__boxWidth, 0, this.__boxWidth, CieStyle.__panel.__borderRadius);
		this.__mv.lineTo(this.__boxWidth, boxHeight);
		this.__mv.endFill();
		this.__title.getSelectionMovie()._x = this.__iMarge; 
		this.__title.getSelectionMovie()._y = (boxHeight/2) - (this.__title.getSelectionMovie()._height/2); 
		};
		
	public function redrawBG(newW:Number):Void{	
		this.__boxWidth = newW;
		this.__mv.clear();
		this.drawBG();
		};
		
	public function getWidth(Void):Number{
		return this.__boxWidth;
		};	
	}