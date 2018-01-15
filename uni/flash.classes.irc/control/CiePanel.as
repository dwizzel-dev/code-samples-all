/*

make a panel with background resizable and receive event from the CiePanelManager to resize his panel and BG
can also be resize dispatcher for contains who registered to it

*/
import mx.containers.ScrollPane;

import effect.CieGlow;
//import effect.CieDropShadow;
//import graphic.CieRoundedSquare;

dynamic class control.CiePanel{

	static private var __className = 'CiePanel';
	private var __mv:MovieClip;
	private var __bg:MovieClip;
	private var __bScrollBar:Boolean;
	private var __panelKey:Number;
	//private var __panel:ScrollPane;
	private var __panel;
	private var __mask;
	private var __attach;
	private var __front:MovieClip;
	private var __registeredObject:Array;
	private var __bgColor:Number;
	private var __focus:Boolean;	
	private var __destroyed:Boolean;	
	private var __lastW:Number;
	private var __lastH:Number;
	//private var __arrPos:Array;
	
	private var __x:Number;
	private var __y:Number;
	
		
	public function CiePanel(mv:MovieClip, w:Number, h:Number, x:Number, y:Number){
		this.__bgColor = CieStyle.__panel.__bgColor;
		this.__mv = mv;
		//this.__arrPos = new Array(x, y);
		
		this.__x = x;
		this.__y = y;
		
		
		this.__lastW = w;
		this.__lastH = h;
		this.__destroyed = false;
		this.__focus = true;
		this.__registeredObject = new Array();
		this.__bg = this.__mv.createEmptyMovieClip('BG', this.__mv.getNextHighestDepth());
		};
		
	public function createScrollPane(bScroll:Boolean):Void{
		this.__panelKey = Math.round(Math.random() * (1000000-0)) + 1;
		this.__bScrollBar = bScroll;
		if(this.__bScrollBar){
			var depth = this.__mv.getNextHighestDepth(); //patch for F** macromedia shit
			this.__panel = this.__mv.createClassObject(ScrollPane, 'PANEL_' + this.__panelKey, depth + 1);		
			this.__panel.setStyle('borderStyle', 'none');
			this.__panel.hScrollPolicy = false;
			this.__panel.vLineScrollSize = CieStyle.__panel.__scrollSize;
			this.__panel.tabEnabled = false;
			this.__panel.vScrollPolicy = 'auto';
		}else{
			this.__panel = this.__mv.createEmptyMovieClip('PANEL_' + this.__panelKey, this.__mv.getNextHighestDepth());	
			this.__mask = this.__mv.createEmptyMovieClip('MASK_' + this.__panelKey, this.__mv.getNextHighestDepth());			
			this.__panel.setMask(this.__mask);
			}
		this.showGlow(CieStyle.__panel.__effectGlow);
		this.resize(this.__x, this.__y, this.__lastW, this.__lastH);	
		};	
		
	public function resize(x:Number, y: Number, w:Number, h:Number):Void{
		//patch with 1 pixels because F*** flash claculate border even of there is none
		if(this.__bScrollBar){
			//scrollpane
			this.__panel.setSize(w, h);
			this.__panel.move(x, y);
		}else{
			//panel
			this.__panel._x = x;
			this.__panel._y = y;
			//the mask
			this.__mask.clear();
			this.drawRoundedRectangle(this.__mask, w, h);
			this.__mask._x = x;
			this.__mask._y = y;
			}
	
		//the BG
		this.__bg.clear();
		this.drawRoundedRectangle(this.__bg, w, h);
		this.__bg._x = x;
		this.__bg._y = y;
		
		 //for the panel registrered object EX: tabsWindow
		this.redraw(this.__bg._width, this.__bg._height);	
		
		//keep for later use
		this.__lastW = w;
		this.__lastH = h;
		};
		
	public function setBgColor(cColor:Number):Void{
		this.__bgColor = cColor;
		this.__bg.clear();
		this.drawRoundedRectangle(this.__bg, this.__lastW, this.__lastH);
		};
		
	public function drawRoundedRectangle(mv:MovieClip, boxWidth:Number, boxHeight:Number):Void {
		if(CieStyle.__panel.__borderWidth){
			mv.lineStyle(CieStyle.__panel.__borderWidth, CieStyle.__panel.__borderColor, 100);
			}
		mv.beginFill(this.__bgColor, 100);
		mv.moveTo(CieStyle.__panel.__borderRadius, 0);
		mv.lineTo(boxWidth - CieStyle.__panel.__borderRadius, 0);
		mv.curveTo(boxWidth, 0, boxWidth, CieStyle.__panel.__borderRadius);
		mv.lineTo(boxWidth, CieStyle.__panel.__borderRadius);
		mv.lineTo(boxWidth, boxHeight - CieStyle.__panel.__borderRadius);
		mv.curveTo(boxWidth, boxHeight, boxWidth - CieStyle.__panel.__borderRadius, boxHeight);
		mv.lineTo(boxWidth - CieStyle.__panel.__borderRadius, boxHeight);
		mv.lineTo(CieStyle.__panel.__borderRadius, boxHeight);
		mv.curveTo(0, boxHeight, 0, boxHeight - CieStyle.__panel.__borderRadius);
		mv.lineTo(0, boxHeight - CieStyle.__panel.__borderRadius);
		mv.lineTo(0, CieStyle.__panel.__borderRadius);
		mv.curveTo(0, 0, CieStyle.__panel.__borderRadius, 0);
		mv.lineTo(CieStyle.__panel.__borderRadius, 0);
		mv.endFill();
		};	
		
	public function setGlow(b:Boolean):Void{
		this.showGlow(b);
		};	
		
	public function showGlow(b:Boolean){
		if(b){
			new CieGlow(this.__bg, true);
		}else{
			this.__bg.filters = null;
			}
		};
		
	public function registerObject(obj:Object):Void{
		if(typeof(obj) == "object" || typeof(obj) == "movieclip"){
			this.__registeredObject.push(obj);
			}
		};	
		
	public function getPanelSize(Void):Object{
		return {__width: this.__lastW, __height: this.__lastH};
		};

	public function registerMultipleObject(arrObj:Array):Void{
		for(var o in arrObj){
			this.registerObject(arrObj[o]);
			}
		};	
		
	public function clearRegisteredObjects(Void):Void{
		for(var o in this.__registeredObject){
			this.__registeredObject[o] = null;
			delete this.__registeredObject[o];
			}
		this.__registeredObject = null;	
		delete this.__registeredObject;
		};
		
	public function removeRegisteredObject(obj:Object):Void{
		for(var o in this.__registeredObject){
			if(this.__registeredObject[o] == obj){
				this.__registeredObject[o] = null;
				delete this.__registeredObject[o];
				break;
				}
			}
		};
		
	public function hasRegisteredObject(Void):Boolean{
		var bHasObject:Boolean = false;
		for(var o in this.__registeredObject){
			bHasObject = true;
			break;
			}
		return bHasObject; 	
		};	
		
	public function redraw( w:Number, h:Number):Void{
		for(var o in this.__registeredObject){
			if(this.__registeredObject[o] != undefined){
				this.__registeredObject[o].resize(w, h);
			}else{
				delete this.__registeredObject[o];
				}
			}
		};	
		
	public function redrawForScrollBar(Void):Void{
		//Debug("-------------------------redrawForScrollBar");
		if(this.__bScrollBar){
			this.__panel.setSize(this.__lastW, this.__lastH);
			}
		};		

	public function disableScroll(Void):Void{
		this.__panel.vScrollPolicy = 'off';
		};
	
	public function getPanelContent(Void):MovieClip{
		if(this.__panel == undefined){
			this.createScrollPane(false);
			}
		if(this.__bScrollBar){
			return this.__panel.content; 		
			}
		if(this.__attach != undefined){
			return this.__attach;
		}else{
			return this.__panel;
			}
		};
		
	public function placeScrollBar(iPos:Number):Void{
		this.__panel.vPosition = iPos;
		};
			
	public function setPanelFocus(b:Boolean):Void{
		this.__focus = b;
		};

	public function getPanelFocus(Void):Boolean{
		return this.__focus;
		};		

	public function removePanel(Void):Void{
		if(this.__bScrollBar){
			destroyObject('PANEL_' + this.__panelKey);
		}else{
			this.__attach.removeMovieClip();
			this.__panel.setMask(null);
			this.__mask.removeMovieClip();
			this.__panel.removeMovieClip();
			delete this.__panel;
			}
		this.__bg.removeMovieClip();
		this.__destroyed = true;
		};	
		
	public function setContent(mvPath:String):Void{
		if(this.__bScrollBar){
			this.__panel.contentPath = mvPath;
		}else{
			this.__attach.removeMovieClip();
			this.__attach = this.__panel.attachMovie(mvPath, 'ATTACHED', this.__panel.getNextHighestDepth());
			}
		};
		
	public function getPanelMovie(Void):MovieClip{
		//for pub only
		return this.__panel;
		};	

	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CiePanel{
		return this;
		};
	}