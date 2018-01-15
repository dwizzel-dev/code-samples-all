/*

window popup

*/

dynamic class control.CieGradientWindows extends control.CieWindows{

	static private var __className = 'CieGradientWindows';
		
	public function CieGradientWindows(strTitle:String, cbFunc:Function, cbClass:Object){
		super(strTitle, cbFunc, cbClass);
		};	
		
	public function drawGradient(tmpWidth:Number):Void{	
		
		var tmpHeight:Number = (this.__mvContent._height + (this.__iMarge * 4));
		
		var matrix = {matrixType:"box", x:0, y:0, w:tmpWidth, h:tmpHeight, r:(0 * Math.PI)};
		if(CieStyle.__window.__reverseHRGradient){
			var colors:Array = [CieStyle.__window.__topGradienColor, CieStyle.__window.__bottomGradienColor]; 
		}else{
			var colors:Array = [CieStyle.__window.__bottomGradienColor, CieStyle.__window.__topGradienColor]; 
			}
		var alphas:Array = [100, 100];
		var ratios:Array = [0, 0xFF];
		//draw
		this.__mvWindow.beginGradientFill('linear', colors, alphas, ratios, matrix);
		this.__mvWindow.moveTo(0, 0);
		this.__mvWindow.lineTo(tmpWidth, 0);
		this.__mvWindow.lineTo(tmpWidth, tmpHeight);
		this.__mvWindow.lineTo(0, tmpHeight);
		this.__mvWindow.lineTo(0, 0);
		this.__mvWindow.endFill();
		};	
		
	};