/*

window popup

*/
import graphic.CieWindowTitleBar;
import effect.CieBlur;

import flash.filters.GlowFilter;

dynamic class control.CieWindows{

	static private var __className = 'CieWindows';
	private var __cbFunc:Function;
	private var __cbClass:Object;
	private var __mvEmpty:MovieClip;
	private var __mvLayer:MovieClip;
	private var __mvWindow:MovieClip;
	private var __mvContent:MovieClip;
	private var __mvTitleBar:MovieClip;
	private var __mvCloseButt:MovieClip;
	private var __mvTitle:MovieClip;
	private var __iMarge:Number;
	private var __titleBarHeight:Number;
	private var __margeButt:Number;
	
	
	public function CieWindows(strTitle:String, cbFunc:Function, cbClass:Object){
		this.__cbFunc = cbFunc;
		this.__cbClass = cbClass;
		this.__strTitle = strTitle;
		this.__iMarge = 10;
		this.__margeButt = 10;
		this.__titleBarHeight = 35;
		
		//emplty layer
		this.__mvEmpty = __stageWinLayer.createEmptyMovieClip('mvEmpty', __stageWinLayer.getNextHighestDepth());
		//bg layer for suaer dark
		this.__mvLayer = this.__mvEmpty.createEmptyMovieClip('mvLayer', this.__mvEmpty.getNextHighestDepth());
		//draw the square window
		this.__mvLayer.beginFill(CieStyle.__window.__bgLayerColor, CieStyle.__window.__bgLayerAlpha);
		this.__mvLayer.moveTo(0, 0);
		this.__mvLayer.lineTo(4000, 0);
		this.__mvLayer.lineTo(4000, 4000);
		this.__mvLayer.lineTo(0, 4000);
		this.__mvLayer.lineTo(0, 0);
		this.__mvLayer.endFill();
		//block 
		this.__mvLayer.useHandCursor = false;
		this.__mvLayer.onRelease = function(Void):Void{}; //block user action
		//the window
		this.__mvWindow = this.__mvEmpty.createEmptyMovieClip('mvWindow',this.__mvEmpty.getNextHighestDepth());
		//the window content
		this.__mvContent = this.__mvWindow.createEmptyMovieClip('mvContent',this.__mvWindow.getNextHighestDepth());
		//the eindow title bar
		this.__mvTitleBar = this.__mvWindow.createEmptyMovieClip('mvTitleBar', this.__mvWindow.getNextHighestDepth());
		//the close butt of the title bar
		this.__mvCloseButt = this.__mvTitleBar.createEmptyMovieClip('mvCloseButt', this.__mvTitleBar.getNextHighestDepth());
		//the title of the window
		this.__mvTitle = this.__mvTitleBar.createEmptyMovieClip('mvTitle', this.__mvTitleBar.getNextHighestDepth());
		
		//add a glow to the window
		var filter:GlowFilter = new GlowFilter(
				0x000000, 
				0.4, 
				8, 
				8, 
				2, 
				3, 
				false, 
				false
				);
		
		var filterArray:Array = new Array();
		filterArray.push(filter);
		this.__mvWindow.filters = filterArray;	
		};

		
	public function blurStage(bState:Boolean):Void{
		if(bState){
			new CieBlur(__stage, CieStyle.__window.__bgLayerEffectBlur, CieStyle.__window.__bgLayerEffectBlur);
		}else{
			__stage.filters = null;
			}
		};	
		
	public function getContent(Void):MovieClip{		
		return this.__mvContent;
		};
		
	public function getWindowWidth(Void):Number{		
		return this.__mvWindow._width;
		};	
		
	public function setWindow(Void):Void{
		var titleBar = new CieWindowTitleBar(this.__mvTitleBar, this.__strTitle, 0);
		var titleBarWidth = titleBar.getWidth();
		//check if content larger then the box
		if(titleBarWidth < (this.__mvContent._width + (this.__iMarge * 4))){
			titleBar.redrawBG((this.__mvContent._width + (this.__iMarge * 4)));
			titleBarWidth = titleBar.getWidth();
			}
		
		//draw the square window
		this.__mvWindow.beginFill(CieStyle.__window.__bgColor, 100);
		this.__mvWindow.moveTo(0, 0);
		this.__mvWindow.lineTo(titleBarWidth, 0);
		this.__mvWindow.lineTo(titleBarWidth, (this.__mvContent._height + (this.__iMarge * 4)));
		this.__mvWindow.lineTo(0, (this.__mvContent._height + (this.__iMarge * 4)));
		this.__mvWindow.lineTo(0, 0);
		this.__mvWindow.endFill();
		
		//positionne le content
		//this.__mvContent._x = (this.__mvWindow._width / 2) - (this.__mvContent._width / 2);
		this.__mvContent._x = 0;
		this.__mvContent._y = this.__iMarge * 2;
				
		//positionnement du TitleBar
		this.__mvTitleBar._y = -this.__titleBarHeight;
		
		//positionnement du Window
		this.resize(Stage.width, Stage.height);
		
		//bouton close et positionnement du bouton close
		this.__mvCloseButt.attachMovie('mvTabCloseButt','mvCloseButt',this.__mvCloseButt.getNextHighestDepth());
		this.__mvCloseButt._x = this.__mvTitleBar._width - this.__mvCloseButt._width - this.__margeButt;
		this.__mvCloseButt._y = (this.__mvTitleBar._height/2) - (this.__mvCloseButt._height/2)
	
		//Action
		this.__mvCloseButt.__super = this;
		this.__mvCloseButt.onRelease = function(Void):Void{
			if(this.__super.__cbFunc != undefined){
				this.__super.__cbFunc(this.__super.__cbClass);
				}
			this.__super.removeRegisteredObject();			
			this.__super.destroy();
			}
		//register to stage for eresize
		cStage.registerObject(this);
		//blur the bg
		this.blurStage(true);
		};
	
	public function resize(w:Number, h:Number):Void{
		this.__mvWindow._x = (w/2) - (this.__mvWindow._width/2);
		this.__mvWindow._y = (h/2) - (this.__mvWindow._height/2) + this.__titleBarHeight;
		};
		
	public function removeRegisteredObject(Void):Void{
		cStage.removeRegisteredObject(this);
		};
	
	public function destroy(Void):Void{
		this.removeRegisteredObject();	
		this.__mvWindow.removeMovieClip();
		this.__mvLayer.removeMovieClip();
		this.__mvEmpty.removeMovieClip();
		this.blurStage(false);
		delete this;
		};
	
	public function getWindow(Void):MovieClip{
		return this.__mvWindow;
		};
	
	public function getButtMovie(Void):MovieClip{
		return this.__mvCloseButt;
		};
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieWindows{
		return this;
		};
	*/	
	};