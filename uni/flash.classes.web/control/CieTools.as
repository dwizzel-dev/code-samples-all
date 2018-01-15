/*

outils qui cont dans les barre d'outils ToolManager

*/

import flash.filters.GlowFilter;
import graphic.CieSquare;
import control.CieBubble;

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
	private var __iconOffSet:Number;
	private var __toolColor:Number;
	private var __toolColorBeforeBlink:Number;
	private var __toolBorderWidth:Number;
	private var __borderToolColor:Number;
	private var __colorEffect:Number;
	private var __colorEffectOff:Number;
	
	public function CieTools(mv:MovieClip, toolName:String, type:String, icon:String){
		this.__mv = mv;
		this.__icon = icon;
		this.__type = type;
		this.__name = toolName;
		this.__loaderOn = false;
		this.__iconOffSet = CieStyle.__basic.__percentIconTool;
		this.__toolColor = CieStyle.__basic.__toolColor;
		this.__toolColorBeforeBlink = this.__toolColor;
		this.__toolBorderWidth = CieStyle.__basic.__toolBorderWidth;
		this.__borderToolColor = CieStyle.__basic.__borderToolColor;
		this.__colorEffect = CieStyle.__basic.__toolEffectColor;
		this.__colorEffectOff = CieStyle.__basic.__toolEffectColorOff;
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
			this.__webIcon._width = this.__w * this.__iconOffSet;
			this.__webIcon._height = this.__h * this.__iconOffSet;
			this.__webIcon._alpha = CieStyle.__basic.__alphaIconTool;
			}
		new CieSquare(this.__tool, 0, 0, this.__w, this.__h, CieStyle.__basic.__toolRadius, [this.__toolColor, 100], ['int', this.__toolBorderWidth, this.__borderToolColor, 100]);
		this.showDropShadow(true);
		};
		
	public function removeTool(Void):Void{
		this.__tool.removeMovieClip();
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
				}else{
					this.__bubble.destroy();
					}
				}
			this.__tool.onRollOut = this.__tool.onDragOut = this.__tool.onReleaseOutside = function(){
				if(!this.__super.__loaderOn){
					this.__super.showDropShadow(true);
				}else{
					this.__bubble.destroy();
					}
				}
			this.__tool.onRollOver = function(){
				if(!this.__super.__loaderOn){
					this.__super.showDropShadow(false);
				}else{
					this.__bubble = new CieBubble(__stage, 100, 150, 5, '', gLang[2]);
					}
				}
		}else{
			this.__tool.onRollOut = this.__tool.onDragOut = this.__tool.onReleaseOutside = function(){
				this.__super.showDropShadow(true);
				}
			this.__tool.onRollOver = function(){
				this.__super.showDropShadow(false);
				}
			}
		};
		
	public function redraw(x:Number, y:Number):Void{
		this.__tool._x = x;
		this.__tool._y = y;
		if(this.__icon != undefined && this.__icon != ''){
			this.__webIcon._x = x + (this.__w - (this.__w * this.__iconOffSet))/2;
			this.__webIcon._y = y + (this.__h - (this.__h * this.__iconOffSet))/2;
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
			var color:Number = this.__colorEffect;
			var alpha:Number = CieStyle.__basic.__toolEffectAlpha;
			var blurX:Number = CieStyle.__basic.__toolEffectBlur;
			var blurY:Number = CieStyle.__basic.__toolEffectBlur;
			var strength:Number = 3;
			var quality:Number = 3;
			var inner:Boolean = false;
			var knockout:Boolean = false;
			var filter:GlowFilter = new GlowFilter(color, alpha, blurX, blurY, strength, quality, inner, knockout);
			var filterArray:Array = new Array();
			filterArray.push(filter);
			this.__tool.filters = filterArray;
		}else{
			var color:Number = this.__colorEffectOff;
			var alpha:Number = CieStyle.__basic.__toolEffectAlphaOff;
			var blurX:Number = CieStyle.__basic.__toolEffectBlurOff;
			var blurY:Number = CieStyle.__basic.__toolEffectBlurOff;
			var strength:Number = 3;
			var quality:Number = 3;
			var inner:Boolean = true;
			var knockout:Boolean = false;
			var filter:GlowFilter = new GlowFilter(color, alpha, blurX, blurY, strength, quality, inner, knockout);
			var filterArray:Array = new Array();
			filterArray.push(filter);
			this.__tool.filters = filterArray;
			}	
		
		};
		
	public function changeIcon(strIcon):Void{
		if(strIcon != undefined){
			this.__icon = strIcon;
			}
		var x = this.__webIcon._x;
		var y = this.__webIcon._y;
		this.__webIcon.removeMovieClip();
		this.__webIcon = this.__mv.attachMovie(this.__icon, 'mvIcon_' + this.__name, this.__mv.getNextHighestDepth());
		this.__webIcon._width = this.__w * this.__iconOffSet;
		this.__webIcon._height = this.__h * this.__iconOffSet;
		this.__webIcon._alpha = CieStyle.__basic.__alphaIconTool;
		this.__webIcon._x = x;
		this.__webIcon._y = y;
		};
		
	public function setLoaderIcon(bOn:Boolean):Void{
		if(bOn){
			this.__oldIcon = this.__icon;
			this.changeIcon('mvIconLoader');
			this.showDropShadow(false);
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
		this.__loaderIcon.clear();
		if(CieStyle.__basic.__menuLoaderHorizontal){
			new CieSquare(this.__loaderIcon, 0, 0, (this.__w * this.__loaderProgress), this.__h, 0, [CieStyle.__basic.__menuLoaderColor, CieStyle.__basic.__menuLoaderAlpha], ['int', this.__toolBorderWidth, this.__borderToolColor, 0]);
		}else{
			new CieSquare(this.__loaderIcon, 0, (this.__h * (1 - iProgress)), this.__w, (this.__h * iProgress), 0, [CieStyle.__basic.__menuLoaderColor, CieStyle.__basic.__menuLoaderAlpha], ['int', this.__toolBorderWidth, this.__borderToolColor, 0]);
			}
		};	
		
	public function changeSize(prct:Number):Void{
		if(prct != 0){
			this.__oldW = this.__w;
			this.__oldH = this.__h;
			this.__w *= prct;
			this.__h *= prct;
		}else{
			this.__w = this.__oldW;
			this.__h = this.__oldH;
			}
		this.__type = this.__w + 'X' + this.__h;
		this.__tool.clear();
		if(this.__icon != undefined && this.__icon != ''){
			this.__webIcon._width = this.__w * this.__iconOffSet;
			this.__webIcon._height = this.__h * this.__iconOffSet;
			}
		new CieSquare(this.__tool, 0, 0, this.__w, this.__h, CieStyle.__basic.__toolRadius, [this.__toolColor, 100], ['int', this.__toolBorderWidth, this.__borderToolColor, 100]);
		this.showDropShadow(true);
		if(this.__loaderOn){
			this.setLoaderProgress(this.__loaderProgress);
			}
		}

	//used by CieButton	to differences between button and tools
	public function changeColor(toolColor:Number, toolBorderWidth:Number, borderToolColor:Number, colorEffect:Number, colorEffectOff:Number):Void{
		this.__toolColor = toolColor;
		this.__toolBorderWidth = toolBorderWidth;
		this.__borderToolColor = borderToolColor;
		this.__colorEffect = colorEffect;
		this.__colorEffectOff = colorEffectOff;
		//redraw the tool
		this.__tool.clear();
		new CieSquare(this.__tool, 0, 0, this.__w, this.__h, CieStyle.__basic.__toolRadius, [this.__toolColor, 100], ['int', this.__toolBorderWidth, this.__borderToolColor, 100]);
		this.showDropShadow(true);
		};
		
	//blink the tool, used for notification on new messages	
	public function blinkEffect(bState:Boolean):Void{
		if(bState){
			//start the thread
			this.__toolColorBeforeBlink = this.__toolColor;
			this.changeColor(CieStyle.__basic.__toolColorBlink, this.__toolBorderWidth, this.__borderToolColor, this.__colorEffect, this.__colorEffectOff);
		}else{
			this.changeColor(this.__toolColorBeforeBlink, this.__toolBorderWidth, this.__borderToolColor, this.__colorEffect, this.__colorEffectOff);
			}
		};
		
	}	