import control.CieTools;
import control.CieTextLine;

dynamic class control.CieButton{

	static private var __className = 'CieButton';
	private var __mv:MovieClip;
	private var __w:Number;
	private var __h:Number;
	private var __x:Number;
	private var __y:Number;
	private var __label:String;
	private var __cButton:CieTools;
	private var __mvText:MovieClip;
	private var __cTextLine:CieTextLine;
		
	public function CieButton(mv:MovieClip, label:String, w:Number, h:Number, x:Number, y:Number){
		this.__mv = mv;
		this.__label = label;
		this.__w = w;
		this.__h = h;
		this.__x = x;
		this.__y = y;
		this.createButton();
		};
	
	private function createButton(Void):Void{
		//instance of tools
		this.__cButton = new CieTools(this.__mv, 'butt', this.__w + 'X' + this.__h, '');
		//color
		this.__cButton.changeColor(CieStyle.__basic.__buttonColor, CieStyle.__basic.__buttonBorderWidth, CieStyle.__basic.__buttonBorderColor, CieStyle.__basic.__buttonEffectColor, CieStyle.__basic.__buttonEffectColorOff);
		//textLine
		this.__mvText = this.__mv.createEmptyMovieClip('mvText', this.__mv.getNextHighestDepth());
		this.__cTextLine = new CieTextLine(this.__mvText, 0, 0, 0, 0, 'label', this.__label, 'dynamic', [true, false, false], false, false, false, false,[CieStyle.__basic.__buttonFontColor, 11]);
		this.__mvText._x = this.__x + (this.__w/2) - (this.__mvText._width/2);
		this.__mvText._y = this.__y + (this.__h/2) - (this.__mvText._height/2);
		//for the mouse over effects
		this.__cButton.setAction('','',[]);
		//position
		this.__cButton.redraw(this.__x, this.__y);
		};
		
	public function changeColor(buttonColor:Number, buttonBorderWidth:Number, buttonBorderColor:Number, buttonEffectColor:Number, buttonEffectColorOff:Number):Void{
		this.__cButton.changeColor(buttonColor, buttonBorderWidth, buttonBorderColor, buttonEffectColor, buttonEffectColorOff);
		};
		
	public function changeTextColor(fontColor:Number):Void{
		this.__cTextLine.changeTextStyle([fontColor, 11, true]);
		};
		
	public function redraw(x:Number, y:Number):Void{
		this.__x = x;
		this.__y = y;
		//the button
		this.__cButton.redraw(this.__x, this.__y);
		//the text
		this.__mvText._x = this.__x + (this.__w/2) - (this.__mvText._width/2);
		this.__mvText._y = this.__y + (this.__h/2) - (this.__mvText._height/2);
		};
		
	public function setAction(ctype:String, funcName:String, arrParams:Array):Void{
		this.__cButton.setAction(ctype, funcName, arrParams);
		};
		
	public function changeSize(prct:Number):Void{
		this.__cButton.changeSize(prct);
		}	
		
	public function removeButton(Void):Void{
		this.__mvText.removeMovieClip();
		this.__cButton.removeTool();
		};
		
	public function getIconWidth(Void):Number{
		return this.__cButton.getIconWidth();
		};	
		
	public function getIconHeight(Void):Number{
		return this.__cButton.getIconHeight();
		};	
		
	public function getMovie(Void):MovieClip{
		return this.__cButton.getIcon();
		};
		
	public function getIcon(Void):MovieClip{
		return this.__cButton.getIcon();
		};	
		
	public function getTool(Void):CieTools{
		return this.__cButton;
		};	
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieButton{
		return this;
		};
	};