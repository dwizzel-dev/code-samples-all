/*

multiple tab

NOTES:

Il faut absolum,ent avertir le ContentManager que le Tab est detruiot pour qu'il reset ses references

*/

import control.CiePanelTab;
import control.CiePanel;

dynamic class manager.CieTabManager{
	
	static private var __className:String = "CieTabManager";
	//private var __offSet:Number = -2;
	
	private var __tabProp:Array;
	private var __tabs:Array;
	private var __tabsDepth:Array;
	private var __mv:MovieClip;
	private var __offSetX:Number;
	private var __offSetY:Number;
	private var __lastW:Number;
	private var __lastH:Number;
	private var __nextX:Number;
	private var __bgTab:MovieClip;
	private var __barPosY:Number;
	private var __lastRemovedTab:String; //for the ContentManager watch
			
	public function CieTabManager(mv:MovieClip, w:Number, h:Number, x:Number, y:Number){
		//trace("new instance of :" + __className);
		
		
		this.__mv = mv;
		this.__barPosY = y;
		
		this.__offSetX = x;
		this.__offSetY = y + (CieStyle.__tab.__bgTabHeight - CieStyle.__tab.__titleTabHeight);
		this.__lastW = w - (this.__offSetX * 2);
		this.__lastH = h - this.__offSetX;
		
		
		//Debug("OFFSETY_0: " + this.__offSetY);
		
		/*
		this.__offSetX = x;
		this.__offSetY = y + (CieStyle.__tab.__bgTabHeight - CieStyle.__tab.__titleTabHeight);
		this.__lastW = w;
		this.__lastH = h;
		*/
		
		this.init();
		};
	
	private function init(Void):Void{
		this.__lastRemovedTab = '';
		this.__tabs = new Array();
		this.__tabsDepth = new Array();
		this.__tabProp = new Array();
		this.__nextX = CieStyle.__tab.__tabOffSetX;
		};
	
	public function wacthNextPositionX(prop, oldVal:Number, newVal:Number, obj:Object):Number{	
		if(newVal){
			obj.__super.changeNextX(newVal);
			}
		return newVal;
		};

	public function createPanelTab(tabName:String, tabTitle:String, bCloseButt:Boolean):Boolean{
		if(this.__tabs[tabName] == undefined){
			/*
			offX = this.__offSetX + 20;
			offY = this.__offSetY + 20;
			wi  = this.__lastW - 40;
			he = this.__lastH - 40;
			this.__tabs[tabName] = new CiePanelTab(this.__mv, offX, offY, wi, he, tabName, tabTitle);
			*/
			/*
			offX = this.__offSetX + 20;
			offY = this.__offSetY + 20;
			wi  = this.__lastW - (offX * 2);
			he = this.__lastH;
			this.__tabs[tabName] = new CiePanelTab(this.__mv, offX, offY, wi, he, tabName, tabTitle);
			*/
			
			this.__tabs[tabName] = new CiePanelTab(this.__mv, this.__offSetX, this.__offSetY, this.__lastW, this.__lastH, tabName, tabTitle);
						
			this.__tabs[tabName].watch('__focus', this.watchFocus, {__tabName:tabName, __super:this});
			this.__tabs[tabName].watch('__destroyed', this.watchDestroyed, {__tabName:tabName, __super:this});
			
			this.__tabs[tabName].moveTitle(this.__nextX);
			
			this.__tabsDepth[tabName] = new Object();
			this.__tabsDepth[tabName].__t = this.__tabs[tabName].getTab();
			this.__tabsDepth[tabName].__d = this.__tabs[tabName].getTab().getDepth();
		
			this.__nextX += CieStyle.__tabPanel.__minTitleTabWidth  + CieStyle.__tabPanel.__tabSpacer;
			
			return true;
		}else{
			return false;
			}
		};	
		
	public function resize(w:Number, h:Number){
		
		w -= (this.__offSetX * 2);
		h -= this.__offSetX;
		
		for(var o in this.__tabs){
			if(this.__tabs[o] != undefined){
				//Debug("*******" + this.getClassName() + " W: " + w + " H: " + h);
				//this.__tabs[o].moveTitlePrct(w/this.__nextX);
				//this.__tabs[o].resize(w, h);
				this.__tabs[o].resize(w, h);
				}
			}
		this.__lastW = w;
		this.__lastH = h; 
		/*
		if(this.__bgTab != undefined){
			this.drawBgTabBar();
			}
		*/	
		};	
		
	public function changeY(newY:Number):Void{
		this.__offSetY += newY; 
		for(var o in this.__tabs){
			if(this.__tabs[o] != undefined){
				this.__tabs[o].changeY(this.__offSetY);
				}
			}
		};
		
	public function changeNextX(newVal:Number):Void{
		this.__nextX +=  newVal + __offSet;
		};
	
	public function removeTabByName(tabName:String):Void{
		this.__tabs[tabName].Destroy();
		};
	
	public function removeTab(tabName:String):Void{
		this.__tabsDepth[tabName] = null;
		delete this.__tabsDepth[tabName];
		
		if(this.__tabs[tabName].getTabFocus()){
			this.gotoNextTab(tabName);
			}
		this.__tabs[tabName] = null;
		this.__tabProp[tabName] = null;
		delete this.__tabs[tabName];
		delete this.__tabProp[tabName];
		this.__lastRemovedTab = tabName;
		this.repositionTab();
		};
		
	public function removeTabs(Void):Void{
		for(var o in this.__tabs){
			this.__tabs[o].Destroy();
			this.__tabProp[o].Destroy();
			}
		};
		
	public function gotoNextTab(tabName:String):Void{
		for(var o in this.__tabs){
			if(tabName != o){
				this.setTabFocus(o);
				break;
				}
			}
		};
		
	public function repositionTab(Void):Void{
		this.__nextX = CieStyle.__tab.__tabOffSetX;
		var oTmp:Array = new Array();
		var oTmpPos:Array = new Array();
		for(var o in this.__tabs){
			oTmp.push({cname: o, cobj: this.__tabs[o]});
			oTmpPos[o] = this.__tabs[o].getTitleWidth();
			}
		for(var o in oTmp){	
			oTmp[o].cobj.moveTitle(this.__nextX);
			this.__tabProp[oTmp[o].cname].__x = this.__nextX;
			this.__nextX += oTmp[o].cobj.getTitleWidth() - CieStyle.__tab.__borderWidth + __offSet;
			}
		delete oTmp;
		};

	public function watchDestroyed(prop, oldVal:Boolean, newVal:Boolean, obj:Object):Boolean{
		if(newVal){
			obj.__super.removeTab(obj.__tabName);
			}
		return newVal;
		};	

	public function setPanelContent(tabName:String, panelName:String, strToLoad:String):Void{
		if(this.__tabs[tabName] != undefined){
			this.__tabs[tabName].setPanelContent(panelName, strToLoad);
			}
		};
		
	public function setPanelBgColor(tabName:String, panelName:String, cColor:Number):Void{
		if(this.__tabs[tabName] != undefined){
			this.__tabs[tabName].setPanelBgColor(panelName, cColor);
			}
		};	
		
	public function setPanelGlow(tabName:String, panelName:String, b:Boolean):Void{
		if(this.__tabs[tabName] != undefined){
			this.__tabs[tabName].setPanelGlow(panelName, b);
			}
		};		

	public function getPanelContent(tabName:String, panelName:String):MovieClip{
		if(this.__tabs[tabName] != undefined){
			return this.__tabs[tabName].getPanelContent(panelName);
			}
		};
		
	public function disablePanelScroll(tabName:String, panelName:String):Void{
		if(this.__tabs[tabName] != undefined){
			this.__tabs[tabName].disablePanelScroll(panelName);
			}
		};	

	public function getPanelSize(tabName:String, panelName:String):Object{
		//trace(__className + ".getPanelSize(" + tabName + ", " + panelName + ")");
		if(this.__tabs[tabName] != undefined){
			return this.__tabs[tabName].getPanelSize(panelName);
			}
		};	
		
	public function getPanelClass(tabName:String, panelName:String):CiePanel{
		if(this.__tabs[tabName] != undefined){
			return this.__tabs[tabName].getPanelClass(panelName);
			}
		};	

	public function createPanels(tabName:String, model:String):Void{
		if(this.__tabs[tabName] != undefined){
			this.__tabs[tabName].createPanels(model);
			}
		};
		
	public function watchFocus(prop, oldVal:Boolean, newVal:Boolean, obj:Object):Boolean{
		if(newVal == true && oldVal == false){
			obj.__super.swapTabFocus(obj.__tabName);
			}
		return newVal;
		};
		
		
	public function setTabFocus(tabName:String):Void{
		if(this.__tabs[tabName] != undefined){
			this.swapTabFocus(tabName);
			if(!this.__tabs[tabName].getTabFocus()){
				this.__tabs[tabName].setTabFocus(true);
				}
			}
		};
		
	private function swapTabFocus(tabName:String):Void{
		var tmpDepth = this.__tabsDepth[tabName].__d;
		var tmpMvClip = this.__tabsDepth[tabName].__t;
		var topWinDepth = 0;
		for(var o in this.__tabsDepth){
			//check the max depth
			if(this.__tabsDepth[o].__d > topWinDepth){
				topWinDepth = this.__tabsDepth[o].__d;
				}
			//skip the one selected               
			if(o != tabName){
				if(this.__tabs[o].getTabFocus()){
					this.__tabs[o].setTabFocus(false); //set the focus to false; if it was true
					}
				//if above send one level back
				if(this.__tabsDepth[o].__d > tmpDepth){
					this.__tabsDepth[o].__d -= 1;
					this.__tabsDepth[o].__t.swapDepths(this.__tabsDepth[o].__d);
					}
				}
			}
		//put the selected window in the front
		this.__tabsDepth[tabName].__t.swapDepths(topWinDepth);
		this.__tabsDepth[tabName].__d = topWinDepth;	
		};
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieTabManager{
		return this;
		};	
	
	}	