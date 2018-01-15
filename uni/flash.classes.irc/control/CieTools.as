/*

outils qui cont dans les barre d'outils ToolManager

*/

import control.CieBubble;
import flash.geom.ColorTransform;

dynamic class control.CieTools{
	
	static private var __className:String = 'CieTools';
	private var __mv:MovieClip;
	private var __tool:MovieClip;
	
	//the loader for salon, bottin, message or other
	private var __loaderIcon:MovieClip;
	private var __loaderOn:Boolean;
	private var __loaderProgress:Number;
	private var __oldIcon:String;
	
	private var __icon:String;
	private var __type:String;
	private var __name:String;
	private var __w:Number;
	private var __h:Number;
	private var __oldW:Number;
	private var __oldH:Number;
	private var __webIcon:MovieClip;	
	private var __bIsBlinking:Boolean;
	private var __bIsBigSize:Boolean;
	private var __button:Object;
	
	public function CieTools(mv:MovieClip, toolName:String, type:String, icon:String){
		this.__bIsBlinking = false;
		this.__mv = mv;
		this.__icon = icon;
		this.__type = type;
		this.__name = toolName;
		this.__loaderOn = false;
		this.getToolSize();
		this.init();
		};
		
	private function getToolSize(Void):Void{
		var arrSize = new Array();
		arrSize = this.__type.split('X');
		this.__w = Number(arrSize[0]);
		this.__h = Number(arrSize[1]);
		this.__oldW = this.__w;
		this.__oldH = this.__h;
		};
		
	private function init(Void):Void{
		this.__tool = this.__mv.attachMovie('mvContent',  'TICON_' + this.__name, this.__mv.getNextHighestDepth());
		if(this.__icon != undefined && this.__icon != ''){
			this.__webIcon = this.__mv.attachMovie(this.__icon, 'mvIcon_' + this.__name, this.__mv.getNextHighestDepth());
			this.__webIcon._width = this.__w * CieStyle.__basic.__percentIconTool;
			this.__webIcon._height = this.__h * CieStyle.__basic.__percentIconTool;
			this.__webIcon._alpha = CieStyle.__basic.__alphaIconTool;
			}
		this.showDropShadow(true);
		};
		
	public function removeTool(Void):Void{
		this.__tool.removeMovieClip();
		};
		
	public function setBubble(strBubbleText:String):Void{	
		this.__tool.__bubbletext = unescape(strBubbleText);
		};
		
	public function setAction(ctype:String, funcName:String, arrParams:Array):Void{
		this.__tool.tabEnabled = false;
		this.__tool.__super = this;
		if(ctype == 'onclick'){
			if(this.__tool.__actions == undefined){
				this.__tool.__actions = new Array();
				this.__tool.__funcs = new Array();
				this.__tool.__params = new Array();
				}
			this.__tool.__actions.push(ctype);
			this.__tool.__bubble = null;
			this.__tool.__funcs.push(funcName);
			this.__tool.__params.push(arrParams);
			this.__tool.onRelease = function(){
				//checkif it's not in loding state
				if(!this.__super.__loaderOn){
					//put a loader before any other action
					//use the global CieEventManager
					for(var o in this.__actions){
						gEventManager.addEvent(this.__actions[o], this.__funcs[o], this.__params[o]);
						}
					if(this.__bubbletext != undefined){
						this.__bubble.destroy();
						}
					if(cBrowser != undefined){
						cBrowser.fetchBanner();
						}	
				}else{
					this.__bubble.destroy();
					}
				};
			this.__tool.onRollOut = this.__tool.onDragOut = this.__tool.onReleaseOutside = function(){
				if(!this.__super.__loaderOn){
					this.__super.showDropShadow(true);
					//for bubble text usage
					if(this.__bubbletext != undefined){
						this.__bubble.destroy();
						}
				}else{
					this.__bubble.destroy();
					}
				};
			this.__tool.onRollOver = function(){
				if(!this.__super.__loaderOn){
					this.__super.showDropShadow(false);
					//for bubble text usage
					if(this.__bubbletext != undefined && BC.__user.__showbubble){
						this.__bubble = new CieBubble(__stage, 100, 150, 5, '', this.__bubbletext);
						}	
				}else{
					this.__bubble = new CieBubble(__stage, 100, 150, 5, '', gLang[2]);
					}
				};
		}else{
			this.__tool.onRollOut = this.__tool.onDragOut = this.__tool.onReleaseOutside = function(){
				this.__super.showDropShadow(true);
				//for bubble text usage
				if(this.__bubbletext != undefined){
					this.__bubble.destroy();
					}
				};
			this.__tool.onRollOver = function(){
				this.__super.showDropShadow(false);
				//for bubble text usage
				if(this.__bubbletext != undefined && BC.__user.__showbubble){
					this.__bubble = new CieBubble(__stage, 100, 150, 5, '', this.__bubbletext);
					}	
				};
			}
		};
		
	public function setButton(cButton:Object):Void{
		this.__button = cButton;
		};
		
	public function redraw(x:Number, y:Number):Void{
		this.__tool._x = x;
		this.__tool._y = y;
		if(this.__icon != undefined && this.__icon != ''){
			this.__webIcon._x = x + (this.__w - (this.__w * CieStyle.__basic.__percentIconTool))/2;
			this.__webIcon._y = y + (this.__h - (this.__h * CieStyle.__basic.__percentIconTool))/2;
			}
		};
	
	public function getIcon(Void):MovieClip{
		return this.__tool;
		};
	
	public function getClassName(Void):String{
		return __className;
		};
		
	public function getIconWidth(Void):Number{
		return this.__tool._width;
		};	
		
	public function getIconHeight(Void):Number{
		return this.__tool._height;
		};		
	
	public function getClass(Void):CieTools{
		return this;
		};
		
	public function showDropShadow(b:Boolean):Void{
		if(b){
			if(this.__bIsBlinking){
				this.changeIconColor(CieStyle.__basic.__iconToolColorOver);
				this.drawSquare(2, 0, 0, this.__w, this.__h, this.__tool);
			}else if(this.__bIsBigSize){
				this.changeIconColor(CieStyle.__basic.__iconToolColorBig);
				this.drawSquare(3, 0, 0, this.__w, this.__h, this.__tool);
			}else{
				this.changeIconColor(-1);
				this.drawSquare(0, 0, 0, this.__w, this.__h, this.__tool);
				if(this.__button != undefined){
					this.__button.changeTextColorEffect(false);
					}
				}
		}else{
			this.changeIconColor(CieStyle.__basic.__iconToolColorOver);
			this.drawSquare(1, 0, 0, this.__w, this.__h, this.__tool);
			if(this.__button != undefined){
				this.__button.changeTextColorEffect(true);
				}
			}	
		};
		
	public function changeIcon(strIcon:String):Void{
		//Debug("CHANGING ICON: " + strIcon );
		if(strIcon != undefined){
			this.__icon = strIcon;
			}
		var x = this.__webIcon._x;
		var y = this.__webIcon._y;
		this.__webIcon.removeMovieClip();
		this.__webIcon = this.__mv.attachMovie(this.__icon, 'mvIcon_' + this.__name, this.__mv.getNextHighestDepth());
		this.__webIcon._width = this.__w * CieStyle.__basic.__percentIconTool;
		this.__webIcon._height = this.__h * CieStyle.__basic.__percentIconTool;
		this.__webIcon._alpha = CieStyle.__basic.__alphaIconTool;
		this.__webIcon._x = x;
		this.__webIcon._y = y;
		};
		
	public function setLoaderIcon(bOn:Boolean):Void{
		if(bOn){
			this.__oldIcon = this.__icon;
			this.changeIcon('mvIconLoader');
			this.showDropShadow(true);
			this.__loaderOn = true;
			//put an empry layer in front
			this.__loaderIcon = this.__tool.createEmptyMovieClip('mvIconLoader',  this.__tool.getNextHighestDepth());
		}else{
			//remove buuble if some
			this.__tool.__bubble.destroy();
			this.changeIcon(this.__oldIcon);
			this.showDropShadow(true);
			this.__loaderIcon.removeMovieClip();
			this.__loaderOn = false;
			}
		};
		
	public function setLoaderProgress(iProgress:Number):Void{
		//iProgress is based on 100% total
		this.__loaderProgress = iProgress;
		if(CieStyle.__basic.__menuLoaderHorizontal && ((this.__w * this.__loaderProgress) > (CieStyle.__basic.__toolRadius * 2))){
			this.drawSquare(1, 0, 0, (this.__w * this.__loaderProgress), this.__h, this.__loaderIcon);
		}else if((this.__h * this.__loaderProgress) > (CieStyle.__basic.__toolRadius * 2)){
			this.drawSquare(1, 0, (this.__h * (1 - iProgress)), this.__w, (this.__h * iProgress), this.__loaderIcon);
			}
		};
		
	public function changeSize(prct:Number):Void{
		if(prct != 0){
			this.__oldW = this.__w;
			this.__oldH = this.__h;
			this.__w *= prct;
			this.__h *= prct;
			this.__bIsBigSize = true;
		}else{
			this.__w = this.__oldW;
			this.__h = this.__oldH;
			this.__bIsBigSize = false;	
			}
		this.__type = this.__w + 'X' + this.__h;
		if(this.__icon != undefined && this.__icon != ''){
			this.__webIcon._width = this.__w * CieStyle.__basic.__percentIconTool;
			this.__webIcon._height = this.__h * CieStyle.__basic.__percentIconTool;
			}
		this.showDropShadow(true);
		if(this.__loaderOn){
			this.setLoaderProgress(this.__loaderProgress);
			}
		}
	
	//blink the tool, used for notification on new messages	
	public function blinkEffect(bState:Boolean):Void{
		if(bState && !this.__bIsBlinking){
			this.__bIsBlinking = true;	
			this.showDropShadow(true);
		}else if(!bState){
			this.__bIsBlinking = false;	
			this.showDropShadow(true);
			}
		};
		
	private function drawSquare(ctype:Number, x:Number, y:Number, w:Number, h:Number, mv:MovieClip):Void{
		//coolors matrix
		var __colors:Array;
		if(ctype == 0){ //normal
			__colors = [CieStyle.__basic.__toolGradientStart, CieStyle.__basic.__toolGradientEnd]; 
		}else if(ctype == 1){ //over
			__colors = [CieStyle.__basic.__toolOverGradientStart, CieStyle.__basic.__toolOverGradientEnd]; 
		}else if(ctype == 2){ //blink
			__colors = [CieStyle.__basic.__toolBlinkGradientStart, CieStyle.__basic.__toolBlinkGradientEnd]; 
		}else if(ctype == 3){ //focus big
			__colors = [CieStyle.__basic.__toolBigGradientStart, CieStyle.__basic.__toolBigGradientEnd]; 
			}
		var __alphas:Array = [100, 100];
		var __ratios:Array = [0, 0xFF];
		
		//init os and 
		var __xBorder = x + CieStyle.__basic.__toolBorderWidth;
		var __yBorder = y + CieStyle.__basic.__toolBorderWidth;
		var __wBorder = w - (CieStyle.__basic.__toolBorderWidth * 2);
		var __hBorder = h - (CieStyle.__basic.__toolBorderWidth * 2);
		var __cBorder = CieStyle.__basic.__toolRadius - CieStyle.__basic.__toolBorderWidth;
		
		//clear
		mv.clear();
		
		//border	
		mv.beginFill(CieStyle.__basic.__borderToolColor, 100);
		mv.moveTo(x, (y+CieStyle.__basic.__toolRadius));
		mv.curveTo(x, y, (x+CieStyle.__basic.__toolRadius), y);
		mv.lineTo((x+w)-CieStyle.__basic.__toolRadius, y);
		mv.curveTo((x+w), y, (x+w), (y+CieStyle.__basic.__toolRadius));
		mv.lineTo((x+w), (y+h)-CieStyle.__basic.__toolRadius);
		mv.curveTo((x+w), (y+h), (x+w)-CieStyle.__basic.__toolRadius, (y+h));
		mv.lineTo((x+CieStyle.__basic.__toolRadius), (y+h));
		mv.curveTo(x, (y+h), x, (y+h)-CieStyle.__basic.__toolRadius);
		mv.lineTo(x, (y+CieStyle.__basic.__toolRadius));
		mv.endFill();
		
		//interior
		var __matrix = {matrixType:"box", x:0, y:0, w:__wBorder, h:__hBorder, r:(0.5 * Math.PI)};
		mv.beginGradientFill('linear', __colors, __alphas, __ratios, __matrix);
		
		mv.moveTo(__xBorder, (__yBorder+__cBorder));
		mv.curveTo(__xBorder, __yBorder, (__xBorder+__cBorder), __yBorder);
		mv.lineTo((__xBorder+__wBorder)-__cBorder, __yBorder);
		mv.curveTo((__xBorder+__wBorder), __yBorder, (__xBorder+__wBorder), (__yBorder+__cBorder));
		mv.lineTo((__xBorder+__wBorder), (__yBorder+__hBorder)-__cBorder);
		mv.curveTo((__xBorder+__wBorder), (__yBorder+__hBorder), (__xBorder+__wBorder)-__cBorder, (__yBorder+__hBorder));
		mv.lineTo((__xBorder+__cBorder), (__yBorder+__hBorder));
		mv.curveTo(__xBorder, (__yBorder+__hBorder), __xBorder, (__yBorder+__hBorder)-__cBorder);
		mv.lineTo(__xBorder, (__yBorder+__cBorder));
		mv.endFill();
		};
		
	private function changeIconColor(iColor:Number):Void{	
		var colorTrans:ColorTransform = new ColorTransform();
		if(iColor != -1){
			colorTrans.rgb = iColor;
			}
		this.__webIcon.transform.colorTransform = colorTrans;
		};
	}	