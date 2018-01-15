/*

a tab window gerer/creted by the TabManager 

*/
//import mx.controls.Label;
//import effect.CieGlow;
import effect.CieDropShadow;

//import graphic.CieOnglet;
//import graphic.CieSquareTabPanel;
//import graphic.CieLine;
import manager.CiePanelManager;
import control.CiePanel;
import control.CieTextLine;
import utils.CieUserEventDispatcher;

//dynamic class control.CiePanelTab extends control.CieTab{
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
	
	private var __drag:Boolean;
	
	private var __baseTitle:String;
	private var __fixedTitle:String;
	private var __titleTabWidth:Number;	
	private var __fixedName:String;
	
	private var __maxTitleTabWidth:Number;
	private var __minTitleTabWidth:Number;
	
	private var __bgColorStatic:Number;
	private var __bgColor:Number;
	private var __bgColorAttention:Number;
	private var __bgColorOver:Number;
	private var __titleColor:Number;
	private var __titleColorOff:Number;
	
	public function CiePanelTab(mv:MovieClip, x:Number, y:Number, w:Number, h:Number, cname:String, cTitle:String){
		if(cname.charAt(0) != CieStyle.__tabPanel.__bUseSpecialTag){
			this.__bgColorStatic  = CieStyle.__tabPanel.__bgColor;
			this.__bgColorOver = CieStyle.__tabPanel.__bgColorOver;
			this.__titleColor = CieStyle.__tabPanel.__titleColor;
			this.__titleColorOff = CieStyle.__tabPanel.__titleColorOff;
		}else{
			this.__bgColorStatic  = CieStyle.__tabPanel.__bgColorSpecial;
			this.__bgColorOver = CieStyle.__tabPanel.__bgColorSpecialOver;
			this.__titleColor = CieStyle.__tabPanel.__titleColorSpecial;
			this.__titleColorOff = CieStyle.__tabPanel.__titleColorSpecialOff;
			}
		this.__bgColorAttention = CieStyle.__tabPanel.__bgColorAttention;	
		this.__bgColor = this.__bgColorStatic;	
			
		this.__destroyed = false;
		this.__drag = false;
		//this.__labeledW = 0;
		this.__mv = mv;
		this.__x = x;
		this.__y = y;
		this.__lastW = w;
		this.__lastH = h;
		//this.__bCloseButt = bCloseButt;
		this.__fixedName = cname;
		this.__baseTitle = cTitle;
		//
		this.init();
		//
		this.resizeAllElements(1);
		//this.setButt();
		this.setAction();
		
		/*************
		IMPORTANT
		NOT SURE ANYMORE ABOUT THIS ONE
		
		this.setTabFocus(true);
			
		**************/
		};
		
	public function resizeAllElements(iPrct:Number):Void{
		if(this.__maxTitleTabWidth != CieStyle.__tabPanel.__maxTitleTabWidth * iPrct){
			this.__maxTitleTabWidth = CieStyle.__tabPanel.__maxTitleTabWidth * iPrct;
			this.__minTitleTabWidth = CieStyle.__tabPanel.__minTitleTabWidth * iPrct;
			this.truncateChars();
			this.setTitle();
			}
		};	
		
	public function getMaxTitleSize(Void):Number{
		return this.__titleTabWidth;
		};

	//reduce num of chars	
	private function truncateChars(Void):Void{		
		var iSteps:Number = 0.1; 
		var bContinue:Boolean = true;
		//
		__stageWinLayer.tmpTextBox.autoSize = 'left';
		//
		this.__fixedTitle = this.__baseTitle;
		var iNumChars:Number = this.__fixedTitle.length;
		while(bContinue){
			__stageWinLayer.tmpTextBox.htmlText = '<b>' + this.__fixedTitle + '</b>';
			this.__titleTabWidth = __stageWinLayer.tmpTextBox._width + (CieStyle.__tabPanel.__titleTextOffSet * 2);
			if(this.__titleTabWidth < this.__maxTitleTabWidth){
				bContinue = false;
				if(this.__titleTabWidth < this.__minTitleTabWidth){
					if(this.__maxTitleTabWidth < this.__minTitleTabWidth){
						this.__titleTabWidth = this.__maxTitleTabWidth;
					}else{
						this.__titleTabWidth = this.__minTitleTabWidth;
						}
					}
			}else{
				iNumChars = Math.ceil(this.__fixedTitle.length - (iNumChars * iSteps) - 3);
				if(iNumChars > 0){
					this.__fixedTitle = this.__baseTitle.substr(0, (iNumChars - 3)) + '...';
				}else{
					bContinue = false;
					}
				}
			}
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
	
	
	public function giveTabAttention(b:Boolean){
		if(b){
			this.__bgColor  = this.__bgColorAttention;
		}else{
			this.__bgColor  = this.__bgColorStatic;
			}
		this.__title.clear();
		this.drawOnglet(this.__title, this.__titleTabWidth, CieStyle.__tabPanel.__titleTabHeight, false);
		};
	
	private function drawOnglet(mv:MovieClip, boxWidth:Number, boxHeight:Number, bMouseIsOver:Boolean):Void {
		if(bMouseIsOver){
			mv.beginFill(this.__bgColorOver, 100);
		}else{
			mv.beginFill(this.__bgColor, 100);	
			}
		
		//box
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
		
	public function setTitle(Void):Void{
		
		this.__title.clear();
		this.drawOnglet(this.__title, this.__titleTabWidth, CieStyle.__tabPanel.__titleTabHeight, false);
		//text Box
		if(this.__cTextLine == undefined){
			this.__cTextLine = new CieTextLine(this.__title, 0, 0, 50, 20, this.__fixedName, this.__fixedTitle, 'dynamic', [false,false,false], false, false, false, false, [0x000000, CieStyle.__tabPanel.__titleSize]);
			this.__cTextLine.getSelectionMovie()._x = CieStyle.__tabPanel.__titleTextOffSet;
			this.__cTextLine.getSelectionMovie()._y = CieStyle.__tabPanel.__titleTextOffSetY;
		}else{
			this.__cTextLine.setNewText(this.__fixedTitle);
			}
		};
		
	private function setPanelTabAction(strAction:String):Void{	
		this.__title.__action = strAction;
		this.__title.onRelease = function(Void):Void{
			gEventManager.addEvent('', this.__action , []);
			if(!this.__super.getTabFocus()){
				this.__super.setTabFocus(true);
				}
			};
		};	
			
	private function setAction(Void):Void{
		this.__title.__super = this;

		this.__title.useHandCursor = false;
		this.__title.onRelease = function(Void):Void{
			if(!this.__super.getTabFocus()){
				this.__super.setTabFocus(true);
				}
			//this.stopDrag();
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
			
		/*
		this.__title.onPress = function(Void):Void{
			//this.startDrag(false, this._x, this._y, this._x, this._y);
			this.__super.setTabDrag(true);
			};
		*/
		this.__title.onDragOut = function(Void):Void{
			//this.startDrag(false, this._x, this._y, this._x, this._y);
			this.__super.setTabDrag(true);
			};
			
		this.__title.onDragOver = function(Void):Void{
			//this.startDrag(false, this._x, this._y, this._x, this._y);
			this.__super.setTabDrag(false);
			};	
	
		this.__title.onReleaseOutside = function(Void):Void{
			//this.stopDrag();
			this.__super.setTabDrag(false);
			if(!this.__super.getTabFocus()){
				this.__super.mouseOver(false);
				}
			//test
			//this.__super.Destroy(true);
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
			
			this.__content.beginFill(this.__bgColor, 100);
								
			this.__content.moveTo(0, CieStyle.__tabPanel.__titleTabHeight);
			this.__content.lineTo(w, CieStyle.__tabPanel.__titleTabHeight);
			this.__content.lineTo(w, (h - this.__y) - CieStyle.__panel.__borderRadius);
			this.__content.curveTo(w, (h - this.__y), w - CieStyle.__panel.__borderRadius, (h - this.__y));
			this.__content.lineTo(CieStyle.__panel.__borderRadius, (h - this.__y));
			this.__content.curveTo(0, (h - this.__y), 0, (h - this.__y) - CieStyle.__panel.__borderRadius);
			this.__content.lineTo(0, CieStyle.__tabPanel.__titleTabHeight);
			this.__content.endFill();
			
			//bottom border of the onglet draw in 2 lines left and right
			//draw directly
			
			//first
			this.__content.lineStyle(CieStyle.__tabPanel.__borderWidth, CieStyle.__tabPanel.__borderColor, 100);
			this.__content.moveTo(0, (CieStyle.__tabPanel.__borderWidth/2) + CieStyle.__tabPanel.__titleTabHeight);
			this.__content.lineTo(this.__x, (CieStyle.__tabPanel.__borderWidth/2) + CieStyle.__tabPanel.__titleTabHeight);
			//second
			this.__content.lineStyle(CieStyle.__tabPanel.__borderWidth, CieStyle.__tabPanel.__borderColor, 100);
			this.__content.moveTo((this.__titleTabWidth + this.__x), (CieStyle.__tabPanel.__borderWidth/2) + CieStyle.__tabPanel.__titleTabHeight);
			this.__content.lineTo(w, (CieStyle.__tabPanel.__borderWidth/2) + CieStyle.__tabPanel.__titleTabHeight);
			
			//pos
			if(this.__panelManager != undefined){
				this.__panelManager.resize(w, (this.__content._height + CieStyle.__tabPanel.__titleTabHeight));
				}
			}
		this.__title._x = this.__x;
		this.__lastW = w;
		this.__lastH = h;
		};	
	
		
	public function createPanels(model:String):Void{
		this.__panelManager = new CiePanelManager(this.__panel, model, this.__lastW, (this.__content._height + CieStyle.__tabPanel.__titleTabHeight), 0, this.__title._height);
		};

	private function Destroy(Void):Void{
		//destroyObject('label');
		
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
		if(b){
			this.giveTabAttention(false);
			}
		if(b != this.__focus){
			if(b){
				//trace("CiePanelTab.FOCUS: " + this.__fixedName);
				}
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
	/*	
	public function changeY(newY:Number):Void{
		this.__newY = newY;
		this.__cthread = cThreadManager.newThread(33, this, 'moveTabEffect', {__newY:newY});
		};
	*/
	/*
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
		//trace("showDropShadow: " + b);
		if(b){
			new CieDropShadow(this.__tab, 1, .15, 1, 3);
			this.__tab._alpha = 100;
			this.__title.clear();
			this.drawOnglet(this.__title, this.__titleTabWidth, CieStyle.__tabPanel.__titleTabHeight, false);
			
			this.__cTextLine.changeTextStyle([this.__titleColor, CieStyle.__tabPanel.__titleSize, true]);
			
			
				
		}else{
			this.__tab.filters = null;
			this.__tab._alpha = CieStyle.__tabPanel.__alphaAmountWhenDisabled;
			this.__title.clear();
			this.drawOnglet(this.__title, this.__titleTabWidth, CieStyle.__tabPanel.__titleTabHeight, false);
			
			this.__cTextLine.changeTextStyle([this.__titleColorOff, CieStyle.__tabPanel.__titleSize, false]);
						
				
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