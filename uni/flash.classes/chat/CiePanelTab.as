/*

a tab window gerer/creted by the TabManager 

*/
//import mx.controls.Label;
//import effect.CieGlow;
import effect.CieDropShadow;

//import graphic.CieOnglet;
//import graphic.CieSquareTabPanel;
//import graphic.CieLine;
import chat.CiePanelManager;
import control.CiePanel;
import control.CieTextLine;
//import utils.CieUserEventDispatcher;

dynamic class chat.CiePanelTab{
	
	
	static private var __className = 'CiePanelTab';
	private var __mv:MovieClip;
	private var __x:Number;
	private var __y:Number;
	private var __origineY:Number;
	private var __cTextLine:CieTextLine;
	private var __origX:Number;
	//private var __newY:Number;
	private var __lastW:Number;
	private var __lastH:Number;
	private var __tab:MovieClip;
	private var __title:MovieClip;
	//private var __titleW:Number;
	private var __content:MovieClip;
	private var __panel:MovieClip;
	//private var __separator:MovieClip;
	private var __tabKey:Number;
	private var __panelManager:CiePanelManager;
	//private var __titleName:String;
	private var __focus:Boolean;
	private var __destroyed:Boolean;
	//private var __cthread:Object;
	
	//sub section
	private var __strTabName:String;
		
	private var __titleTabWidth:Number;	
		
	
	//private var __labeledW:Number;
	//private var __intervalTitleEffect:Number;
	//private var __closeButt:MovieClip;
	//private var __bCloseButt:Boolean;
	
	public function CiePanelTab(mv:MovieClip, x:Number, y:Number, w:Number, h:Number, cname:String, cTitle:String){
		this.__destroyed = false;
		//this.__labeledW = 0;
		this.__mv = mv;
		this.__x = x;
		this.__y = y;
		this.__origineY = y;
		this.__lastW = w;
		this.__lastH = h;
		//this.__bCloseButt = bCloseButt;
		this.setMaxTitleSize(cTitle);
		this.init();
		//this.showDropShadow(CieStyle.__tab.__effectShadow);
		this.__strTabName = cname;
		this.setTitle(cname, cTitle);
		//this.setButt();
		this.setAction();
		this.setTabFocus(true);
		};
		
	private function init(Void):Void{
		//create a atab layer
		//this.__focus = true;
		this.__tabKey = Math.round(Math.random() * (1000000-0)) + 1;
		this.__tab = this.__mv.createEmptyMovieClip('TAB_' + this.__tabKey, this.__mv.getNextHighestDepth());
		this.__content = this.__tab.createEmptyMovieClip('Content', this.__tab.getNextHighestDepth());	
		//this.__separator = this.__tab.createEmptyMovieClip('Separator', this.__tab.getNextHighestDepth());	
		this.__title = this.__tab.createEmptyMovieClip('Title', this.__tab.getNextHighestDepth());	
		this.__panel = this.__tab.createEmptyMovieClip('Panel', this.__tab.getNextHighestDepth());
		//this.__closeButt = this.__tab.createEmptyMovieClip('CloseButt', this.__tab.getNextHighestDepth());
		this.__tab._x = this.__x;
		this.__tab._y = this.__y;
		};
	
	/*
	private function setButt(Void):Void{
		if (this.__bCloseButt == 'true'){
			this.__closeButt.attachMovie('mvTabCloseButt','mvTabCloseButt',1);
			}
		};	
	*/	
	
	public function getMaxTitleSize(Void):Number{
		return this.__titleTabWidth;
		};
	
	private function setMaxTitleSize(newTitle:String):Void{
		//we are going to use a tmp textBox to calculate the size of the text
		var tmpBox = __stageWinLayer.tmpTextBox;
		tmpBox.htmlText = '<b>' + newTitle + '</b>';
		tmpBox.autoSize = 'left';
		this.__titleTabWidth = tmpBox._width + (CieStyle.__tabPanel.__titleTextOffSet * 2);
		if(this.__titleTabWidth < CieStyle.__tabPanel.__minTitleTabWidth){
			this.__titleTabWidth = CieStyle.__tabPanel.__minTitleTabWidth;
			}
		};
	
	private function drawOnglet(mv:MovieClip, boxWidth:Number, boxHeight:Number, bMouseIsOver:Boolean):Void {
		if(bMouseIsOver){
			mv.beginFill(CieStyle.__tabPanel.__bgColorOver, 100);
		}else{
			mv.beginFill(CieStyle.__tabPanel.__bgColor, 100);	
			}
		mv.moveTo(0, boxHeight);
		mv.lineTo(0, CieStyle.__tabPanel.__borderRadius);
		mv.curveTo(0, 0, CieStyle.__tabPanel.__borderRadius, 0);
		mv.lineTo(boxWidth - CieStyle.__tabPanel.__borderRadius , 0);
		mv.curveTo(boxWidth, 0, boxWidth, CieStyle.__tabPanel.__borderRadius);
		mv.lineTo(boxWidth, boxHeight);
		mv.endFill();
		//line
		mv.lineStyle(CieStyle.__tabPanel.__borderWidth, CieStyle.__tabPanel.__borderColor, 100);
		mv.moveTo(0, boxHeight);
		mv.lineTo(0, CieStyle.__tabPanel.__borderRadius);
		mv.curveTo(0, 0, CieStyle.__tabPanel.__borderRadius, 0);
		mv.lineTo(boxWidth - CieStyle.__tabPanel.__borderRadius , 0);
		mv.curveTo(boxWidth, 0, boxWidth, CieStyle.__tabPanel.__borderRadius);
		mv.lineTo(boxWidth, boxHeight);
		};
		
	public function setTitle(newName:String, newTitle:String):Void{
		this.__title.clear();
		this.drawOnglet(this.__title, this.__titleTabWidth, CieStyle.__tabPanel.__titleTabHeight, false);
		//text Box
		this.__cTextLine = new CieTextLine(this.__title, 0, 0, 50, 20, newName, newTitle, 'dynamic', [false,false,false], false, false, false, false, [0x000000, CieStyle.__tabPanel.__titleSize]);
		
		this.__cTextLine.getSelectionMovie()._x = CieStyle.__tabPanel.__titleTextOffSet;
		this.__cTextLine.getSelectionMovie()._y = CieStyle.__tabPanel.__titleTextOffSetY;
		};
		
	public function setPanelTabAction(strAction:String):Void{	
		this.__title.__action = strAction;
		this.__title.onRelease = function(Void):Void{
			gEventManager.addEvent('', this.__action , []);
			if(!this.__super.getTabFocus()){
				this.__super.setTabFocus(true);
				}
			//this.stopDrag();
			//this.__super.setTabDrag(false);
			};
		};
		
	private function setAction(Void):Void{
		this.__title.__super = this;
		this.__title.onRelease = function(Void):Void{
			if(!this.__super.getTabFocus()){
				this.__super.setTabFocus(true);
				}
			//this.stopDrag();
			//this.__super.setTabDrag(false);
			};
			
		this.__title.onRollOver = function(Void):Void{
			if(!this.__super.getTabFocus()){
				this.__super.mouseOver(true);
				}
			};
			
		this.__title.onRollOut = function(Void):Void{
			if(!this.__super.getTabFocus()){
				this.__super.mouseOver(false);
				}
			};	
			
		this.__title.onPress = function(Void):Void{
			//this.startDrag(false, this._x, this._y, this._x, this._y);
			//this.__super.setTabDrag(true);
			};
	
		this.__title.onReleaseOutside = function(Void):Void{
			//this.stopDrag();
			//this.__super.setTabDrag(false);
			if(!this.__super.getTabFocus()){
				this.__super.mouseOver(false);
				}
			};
		};	

	private function resize(w:Number, h:Number):Void{
		//draw
		if(this.__focus){
			//draw directly
			this.__content.clear();
						
			this.__content.beginFill(CieStyle.__tabPanel.__bgColor, 100);
			this.__content.moveTo(0, CieStyle.__tabPanel.__titleTabHeight);
			this.__content.lineTo(w, CieStyle.__tabPanel.__titleTabHeight);
			this.__content.lineTo(w, (h - this.__y) - CieStyle.__panel.__borderRadius);
			this.__content.curveTo(w, (h - this.__y), w - CieStyle.__panel.__borderRadius, (h - this.__y));
			this.__content.lineTo(CieStyle.__panel.__borderRadius, (h - this.__y));
			this.__content.curveTo(0, (h - this.__y), 0, (h - this.__y) - CieStyle.__panel.__borderRadius);
			this.__content.lineTo(0, CieStyle.__tabPanel.__titleTabHeight);
			this.__content.endFill();
			
			//first
			this.__content.lineStyle(CieStyle.__tabPanel.__borderWidth, CieStyle.__tabPanel.__borderColor, 100);
			this.__content.moveTo(0, CieStyle.__tabPanel.__titleTabHeight);
			this.__content.lineTo(this.__x, CieStyle.__tabPanel.__titleTabHeight);
			//second
			this.__content.lineStyle(CieStyle.__tabPanel.__borderWidth, CieStyle.__tabPanel.__borderColor, 100);
			this.__content.moveTo((this.__titleTabWidth + this.__x), CieStyle.__tabPanel.__titleTabHeight);
			this.__content.lineTo(w, CieStyle.__tabPanel.__titleTabHeight);
					
			if(this.__panelManager != undefined){
				this.__panelManager.resize(w, (this.__content._height + CieStyle.__tabPanel.__titleTabHeight));
				}
			}
		this.__title._x = this.__x;
		this.__lastW = w;
		this.__lastH = h;
			
		}	
	
		
	public function createPanels(model:String):Void{
		this.__panelManager = new CiePanelManager(this.__panel, model, this.__lastW, (this.__content._height + CieStyle.__tabPanel.__titleTabHeight), 0, this.__title._height);
		};

	private function Destroy(Void):Void{
		//destroyObject('label');
		//kill the thread
		//this.__cthread.destroy();
		
		this.__content.removeMovieClip();
		this.__content = null;
		delete this.__content;
		
		this.__title.removeMovieClip();
		this.__title = null;
		delete this.__title;
		
		this.__panel.removeMovieClip();
		this.__panel = null;
		delete this.__panel;
		
		this.__tab.removeMovieClip();
		this.__tab = null;
		delete this.__tab;
	
		this.__panelManager.removePanels();
		this.__panelManager = null;
		delete this.__panelManager;
		
		this.__destroyed = true;
		};

	/*
	public function setTabDrag(b:Boolean):Void{
		this.__drag = b;
		};
	*/
		
	public function setTabFocus(b:Boolean):Void{
		if(b != this.__focus){
			this.__focus = b;
			this.showContent();
			this.resize(this.__lastW, this.__lastH);
			this.showDropShadow(b);
			//Debug('CiePanelTab.setTabFocus(' + this.__strTabName + ')');
			}
		/*
		if(b){
			Debug('CiePanelTab.setTabFocus(' + this.__strTabName + ')');
			}
		*/	
		};

	public function setPanelManagerFocus(b:Boolean):Void{
		if(this.__panelManager != undefined){
			this.__panelManager.setPanelFocus(b);
			}
		};

	public function getTabFocus(Void):Boolean{
		return this.__focus;
		};
		
	public function showContent(Void):Void{
		this.__panel._visible = this.__focus;
		this.__content._visible = this.__focus;
		};	
	
	/*
	public function changeY(newY:Number, mvBanner:MovieClip):Void{
		//Debug('BAN_Y: ' + newY);
		this.__cthread = cThreadManager.newThread(50, this, 'moveTabEffect', {__newY:newY, __supclass:this, __mvbanner:mvBanner});
		};
	*/	
		
	/*
	public function moveTabEffect(obj:Object):Boolean{
		//var newY = Math.floor((obj.__newY - obj.__supclass.__y)/2);
		var newY = (obj.__newY - obj.__supclass.__y)/2;
		obj.__supclass.__y += newY;
		obj.__supclass.__tab._y = obj.__supclass.__y;
		obj.__supclass.resize(obj.__supclass.__lastW, obj.__supclass.__lastH);
		if(!newY){
			if(obj.__mvbanner != undefined){
				obj.__mvbanner.play();
				}
			return false;
			}
		return true;	
		};	
	*/
	
	public function disablePanelScroll(panelName:String):Void{
		this.__panelManager.getPanel(panelName).disableScroll();
		};		
		
	private function mouseOver(state:Boolean):Void{
		this.__title.clear();
		if(state){
			this.drawOnglet(this.__title, this.__titleTabWidth, CieStyle.__tabPanel.__titleTabHeight, true);
		}else{
			this.drawOnglet(this.__title, this.__titleTabWidth, CieStyle.__tabPanel.__titleTabHeight, false);
			}
		};	
		
	public function showDropShadow(b:Boolean):Void{
		if(b){
			new CieDropShadow(this.__tab, 1, .25, 2, 2);
			this.__title.clear();
			this.drawOnglet(this.__title, this.__titleTabWidth, CieStyle.__tabPanel.__titleTabHeight, false);
			this.__cTextLine.changeTextStyle([CieStyle.__tabPanel.__titleColor, CieStyle.__tabPanel.__titleSize, true]);
		}else{
			this.__tab.filters = null;
			this.__tab._alpha = CieStyle.__tabPanel.__alphaAmountWhenDisabled;
			this.__title.clear();
			this.drawOnglet(this.__title, this.__titleTabWidth, CieStyle.__tabPanel.__titleTabHeight, false);
			this.__cTextLine.changeTextStyle([CieStyle.__tabPanel.__titleColorOff, CieStyle.__tabPanel.__titleSize, false]);
			}
		};	
		
	public function moveTitle(x:Number):Void{
		this.__origX = x;
		this.__x = x;
		this.resize(this.__lastW, this.__lastH);
		};
		
	public function setPanelBgColor(panelName:String, cColor:Number):Void{
		this.__panelManager.setPanelBgColor(panelName, cColor);
		};	
		
	public function setPanelGlow(panelName:String, b:Boolean):Void{
		this.__panelManager.setPanelGlow(panelName, b);
		};	
		
	public function setPanelContent(panelName:String, strToLoad:String):Void{
		this.__panelManager.setPanelContent(panelName, strToLoad);
		};	
		
	public function getPanelContent(panelName:String):MovieClip{
		return this.__panelManager.getPanel(panelName).getPanelContent();
		};
		
	public function getPanelClass(panelName:String):CiePanel{
		return this.__panelManager.getPanel(panelName).getClass();
		};	
	
	public function getPanelSize(panelName:String):Object{
		return this.__panelManager.getPanel(panelName).getPanelSize();
		};		

	public function getTitleWidth(Void):Number{
		return this.__title._width;
		};
		
	public function getTitleX(Void):Number{
		return this.__title._x;
		};
	
	public function getTab(Void):MovieClip{
		return this.__tab;
		};
		
	/*
	public function getTabName(Void):String{
		return this.__titleName;
		};
	*/	
		
	public function getClassName(Void):String{
		return __className;
		};
		
	public function getClass(Void):CiePanelTab{
		return __className;
		};	
	}	