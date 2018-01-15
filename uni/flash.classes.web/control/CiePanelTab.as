/*

a tab window gerer/creted by the TabManager 

*/
import mx.controls.Label;
import effect.CieDropShadow;
import manager.CiePanelManager;
import control.CiePanel;
import control.CieTextLine;

dynamic class control.CiePanelTab{
	
	
	static private var __className = "CiePanelTab";
	private var __mv:MovieClip;
	private var __x:Number;
	private var __y:Number;
	private var __cTextLine:CieTextLine;
	private var __origX:Number;
	private var __newY:Number;
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
	private var __titleName:String;
	private var __focus:Boolean;
	private var __destroyed:Boolean;
	private var __cthread:Object;
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
		this.__lastW = w;
		this.__lastH = h;
		//this.__bCloseButt = bCloseButt;
		this.init();
		//this.showDropShadow(CieStyle.__tab.__effectShadow);
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
	private function drawOnglet(mv:MovieClip, boxWidth:Number, boxHeight:Number):Void {
		with (mv) {
			//box
			beginFill(CieStyle.__tabPanel.__bgColor, 100);
			moveTo(0, boxHeight);
			lineTo(0, CieStyle.__tabPanel.__borderRadius);
			curveTo(0, 0, CieStyle.__tabPanel.__borderRadius, 0);
			lineTo(boxWidth - CieStyle.__tabPanel.__borderRadius , 0);
			curveTo(boxWidth, 0, boxWidth, CieStyle.__tabPanel.__borderRadius);
			lineTo(boxWidth, boxHeight);
			endFill();
			
			//line
			lineStyle(CieStyle.__tabPanel.__borderWidth, CieStyle.__tabPanel.__borderColor, 100);
			moveTo(0, boxHeight);
			lineTo(0, CieStyle.__tabPanel.__borderRadius);
			curveTo(0, 0, CieStyle.__tabPanel.__borderRadius, 0);
			lineTo(boxWidth - CieStyle.__tabPanel.__borderRadius , 0);
			curveTo(boxWidth, 0, boxWidth, CieStyle.__tabPanel.__borderRadius);
			lineTo(boxWidth, boxHeight);
			}
		};
		
	public function setTitle(newName:String, newTitle:String):Void{
		
		this.__title.clear();
		this.drawOnglet(this.__title, CieStyle.__tabPanel.__minTitleTabWidth, CieStyle.__tabPanel.__titleTabHeight);
		//text Box
		this.__cTextLine = new CieTextLine(this.__title, 0, 0, 50, 20, newName, newTitle, 'dynamic', [false,false,false], false, false, false, false, [0x000000, CieStyle.__tabPanel.__titleSize]);
		
		this.__cTextLine.getSelectionMovie()._x = CieStyle.__tabPanel.__titleTextOffSet;
		this.__cTextLine.getSelectionMovie()._y = CieStyle.__tabPanel.__titleTextOffSetY;
		};
		
	private function setAction(Void):Void{
		this.__title.__super = this;

		//this.__title.useHandCursor = false;
		this.__title.onRelease = function(Void):Void{
			if(!this.__super.getTabFocus()){
				this.__super.setTabFocus(true);
				}
			this.stopDrag();
			this.__super.setTabDrag(false);
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
			this.startDrag(false, this._x, this._y, this._x, this._y);
			this.__super.setTabDrag(true);
			};
	
		this.__title.onReleaseOutside = function(Void):Void{
			this.stopDrag();
			this.__super.setTabDrag(false);
			if(!this.__super.getTabFocus()){
				this.__super.mouseOver(false);
				}
			};
			
		//this.__closeButt.__super = this;
		/*
		this.__closeButt.onRelease = function(Void):Void{
			this.__super.Destroy(true);
			}
		*/	
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
			
			
			/*
			this.__content.clear();
			this.__content.beginFill(CieStyle.__tabPanel.__bgColor, 100);
			this.__content.moveTo(0, CieStyle.__tabPanel.__titleTabHeight);
			this.__content.lineTo(w, CieStyle.__tabPanel.__titleTabHeight);
			this.__content.lineTo(w, (h - this.__y));
			this.__content.lineTo(0, (h - this.__y));
			this.__content.lineTo(0, CieStyle.__tabPanel.__titleTabHeight);
			this.__content.endFill();
			*/
			
			//bottom border of the onglet draw in 2 lines left and right
			//draw directly
			
			//first
			this.__content.lineStyle(CieStyle.__tabPanel.__borderWidth, CieStyle.__tabPanel.__borderColor, 100);
			this.__content.moveTo(0, CieStyle.__tabPanel.__borderWidth + CieStyle.__tabPanel.__titleTabHeight);
			this.__content.lineTo(this.__x, CieStyle.__tabPanel.__borderWidth + CieStyle.__tabPanel.__titleTabHeight);
			//second
			
			this.__content.lineStyle(CieStyle.__tabPanel.__borderWidth, CieStyle.__tabPanel.__borderColor, 100);
			this.__content.moveTo((CieStyle.__tabPanel.__minTitleTabWidth + this.__x), CieStyle.__tabPanel.__borderWidth + CieStyle.__tabPanel.__titleTabHeight);
			this.__content.lineTo(w, CieStyle.__tabPanel.__borderWidth + CieStyle.__tabPanel.__titleTabHeight);
			
			/*
			this.__separator.clear();
			new CieLine(this.__separator, 0, this.__x, CieStyle.__tabPanel.__borderWidth, CieStyle.__tabPanel.__borderColor, 100, 'H');
			new CieLine(this.__separator, (CieStyle.__tabPanel.__minTitleTabWidth + this.__x), w, CieStyle.__tabPanel.__borderWidth, CieStyle.__tabPanel.__borderColor, 100, 'H');
			*/
			
			//pos
			//this.__separator._y = CieStyle.__tabPanel.__titleTabHeight;
					
			if(this.__panelManager != undefined){
				this.__panelManager.resize(w, (this.__content._height + CieStyle.__tabPanel.__titleTabHeight));
				}
			}
		this.__title._x = this.__x;
		this.__lastW = w;
		this.__lastH = h;
			
		}	
	
		
	public function createPanels(model:String):Void{
		/*
		var newW = this.__content._width - (CieStyle.__panel.__borderpadding + CieStyle.__tabPanel.__borderWidth);
		var newH = this.__content._height - (CieStyle.__panel.__borderpadding + CieStyle.__tabPanel.__borderWidth);
		this.__panelManager = new CiePanelManager(this.__panel, model, newW, newH, 0, this.__title._height);
		*/
		
		//this.__panelManager = new CiePanelManager(this.__panel, model, this.__content._width, (this.__content._height + CieStyle.__tabPanel.__titleTabHeight), 0, this.__title._height);
		
		this.__panelManager = new CiePanelManager(this.__panel, model, this.__lastW, (this.__content._height + CieStyle.__tabPanel.__titleTabHeight), 0, this.__title._height);
		
		};

	private function Destroy(Void):Void{
		destroyObject('label');
		
		this.__content.removeMovieClip();
		this.__content = null;
		delete this.__content;
		
		//this.__separator.removeMovieClip();
		//this.__separator = null;
		//delete this.__separator
		
		this.__title.removeMovieClip();
		this.__title = null;
		delete this.__title;
		
		this.__panel.removeMovieClip();
		this.__panel = null;
		delete this.__panel;
		
		this.__tab.removeMovieClip();
		this.__tab = null;
		delete this.__tab;
	
		/*
		this.__closeButt.removeMovieClip();
		this.__closeButt = null;
		delete this.__closeButt;
		*/
		this.__panelManager.removePanels();
		this.__panelManager = null;
		delete this.__panelManager;
		
		this.__destroyed = true;
		};

	public function setTabDrag(b:Boolean):Void{
		this.__drag = b;
		};

	public function setTabFocus(b:Boolean):Void{
		if(b != this.__focus){
			this.__focus = b;
			this.showContent();
			this.resize(this.__lastW, this.__lastH);
			this.showDropShadow(b);
			}
		//tell the PanelManager taht we don't have focus anymore and can disable some movies
		//this.setPanelManagerFocus(b);
		}

	public function setPanelManagerFocus(b:Boolean){
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
		
	public function changeY(newY:Number):Void{
		this.__newY = newY;
		this.__cthread = cThreadManager.newThread(33, this, 'moveTabEffect', {__newY:newY});
		};
		
	public function moveTabEffect(obj:Object):Boolean{
		var newY = Math.floor((obj.__newY - this.__y)/4);
		this.__y += newY;
		this.__tab._y = this.__y;
		this.resize(this.__lastW, this.__lastH);
		if(!newY){
			return false;
			}
		return true;	
		};	
		
	public function disablePanelScroll(panelName:String):Void{
		this.__panelManager.getPanel(panelName).disableScroll();
		};		
		
	private function mouseOver(state:Boolean):Void{
		this.__title.clear();
		if(state){
			this.drawOnglet(this.__title, CieStyle.__tabPanel.__minTitleTabWidth, CieStyle.__tabPanel.__titleTabHeight);
		}else{
			this.drawOnglet(this.__title, CieStyle.__tabPanel.__minTitleTabWidth, CieStyle.__tabPanel.__titleTabHeight);
			}
		};	
		
	public function showDropShadow(b:Boolean):Void{
		if(b){
			new CieDropShadow(this.__tab, 1, .25, 2, 3);
			this.__title.clear();
			this.drawOnglet(this.__title, CieStyle.__tabPanel.__minTitleTabWidth, CieStyle.__tabPanel.__titleTabHeight);
			this.__cTextLine.changeTextStyle([CieStyle.__tabPanel.__titleColor, CieStyle.__tabPanel.__titleSize, true]);
		}else{
			this.__tab.filters = null;
			this.__tab._alpha = CieStyle.__tabPanel.__alphaAmountWhenDisabled;
			this.__title.clear();
			this.drawOnglet(this.__title, CieStyle.__tabPanel.__minTitleTabWidth, CieStyle.__tabPanel.__titleTabHeight);
			this.__cTextLine.changeTextStyle([CieStyle.__tabPanel.__titleColorOff, CieStyle.__tabPanel.__titleSize, false]);
			}
		};	
		
	public function moveTitle(x:Number):Void{
		this.__origX = x;
		this.__x = x;
		this.resize(this.__lastW, this.__lastH);
		};
	
	/*
	public function moveTitlePrct(percent:Number):Void{
		this.__x = this.__origX * percent;
		if(this.__x > this.__origX){
			this.__x = this.__origX;
			this.__title._xscale = 100;
		}else{
			this.__title._xscale = percent * 100;
			}
		};
	*/
		
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
		//trace(__className + ".getPanelSize(" + panelName + ")");
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
		
	public function getTabName(Void):String{
		return this.__titleName;
		};	
		
	public function getClassName(Void):String{
		return __className;
		};
		
	public function getClass(Void):CiePanelTab{
		return __className;
		};	
	
	}	