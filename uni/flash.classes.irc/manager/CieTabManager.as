/*

multiple tab

NOTES:

Il faut absolum,ent avertir le ContentManager que le Tab est detruiot pour qu'il reset ses references

*/

//import control.CieTab;
import control.CiePanelTab;
import control.CiePanel;
//import graphic.CieLine;
//import graphic.CieSquareTabPanel;

dynamic class manager.CieTabManager{
	
	static private var __className:String = "CieTabManager";
	private var __offSet:Number = 1;
	
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
	
	private var __tabCounter:Number;
	private var __tabDragArrow:MovieClip;
	private var __arrTabPosX:Array;
	private var __lastArrowTabNum:Number;
	private var __lastArrowTabNumSelected:Number;
	private var __mouseListener:Object;
	
	//focus event
	private var __registeredObject:Array;

	
	public function CieTabManager(mv:MovieClip, w:Number, h:Number, x:Number, y:Number){
		//trace("new instance of :" + __className);
		this.__mv = mv;
		this.__barPosY = y;
		this.__offSetX = x;
		this.__offSetY = y + (CieStyle.__tab.__bgTabHeight - CieStyle.__tab.__titleTabHeight);
		this.__lastW = w - (this.__offSetX * 2);
		this.__lastH = h - this.__offSetX;
		this.__tabCounter = 0;
		this.__lastRemovedTab = '';
		this.__tabs = new Array();
		this.__tabsDepth = new Array();
		this.__tabProp = new Array();
		this.__nextX = CieStyle.__tab.__tabOffSetX;
		
		this.__arrTabPosX = new Array();
		this.__lastArrowTabNum = -1;
		this.__lastArrowTabNumSelected = -1;
		
		this.__registeredObject = new Array();
		
		};
	
	public function createPanelTab(tabName:String, tabTitle:String, bCloseButt:Boolean):Boolean{
		//trace("CieTabManager.createPanelTab: " + tabName);
		//trace("CREATE_PANEL_TAB: " + tabName); 
		if(this.__tabs[tabName] == undefined){
			this.__tabCounter++;
			
			this.__tabs[tabName] = new CiePanelTab(this.__mv, this.__offSetX, this.__offSetY, this.__lastW, this.__lastH, tabName, tabTitle);
	
			this.__tabs[tabName].watch('__focus', this.watchFocus, {__tabName:tabName, __super:this});
			this.__tabs[tabName].watch('__destroyed', this.watchDestroyed, {__tabName:tabName, __super:this});
			this.__tabs[tabName].watch('__drag', this.watchTabDrag, {__tabName:tabName, __super:this});
			
			this.__tabsDepth[tabName] = new Object();
			this.__tabsDepth[tabName].__t = this.__tabs[tabName].getTab();
			this.__tabsDepth[tabName].__d = this.__tabs[tabName].getTab().getDepth();
		
			this.repositionTabResize();
			
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
				this.__tabs[o].resize(w, h);
				}
			}
		this.__lastW = w;
		this.__lastH = h; 
		this.repositionTabResize();
		};
		
	//change the color of the tab without giving focus
	public function giveTabAttention(tabName:String, b:Boolean){
		this.__tabs[tabName].giveTabAttention(b);
		};	
		
		

	/*	
	public function changeY(newY:Number):Void{
		this.__offSetY += newY; 
		for(var o in this.__tabs){
			if(this.__tabs[o] != undefined){
				this.__tabs[o].changeY(this.__offSetY);
				}
			}
		};
	*/	
	public function changeNextX(newVal:Number):Void{
		this.__nextX +=  newVal + CieStyle.__tabPanel.__tabSpacer;
		};
	
	//when call outside the tabManager
	public function removeTabByName(tabName:String):Void{
		this.__tabs[tabName].Destroy();
		};
	
	private function removeTab(tabName:String):Void{
		this.__tabCounter--;
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
		this.repositionTabResize();
		};
		
		
	public function traceTabs(Void):Void{
		var str = '';
		for(var o in this.__tabs){
			str += o + ',';
			}
		trace(str);	
		};	
		
	public function removeTabs(Void):Void{
		for(var o in this.__tabs){
			this.__tabs[o].Destroy();
			this.__tabProp[o].Destroy();
			}
		};
	
	//go to the next tab when one is deleted
	public function gotoNextTab(tabName:String):Void{
		//always reverse it
		var oTmp:Array = new Array();
		for(var o in this.__tabs){
			oTmp.push(o);
			}
		//from the first to last array,   <- right to left
		for(var o = 0; o < oTmp.length ; o++){
			if(tabName == oTmp[o]){ //the one we want to destroyed
				if(o != 0){//the last one on the row
					if(oTmp[o - 1] != undefined){
						this.setTabFocus(oTmp[o - 1]);
						}	
				}else{
					if(oTmp[o + 1] != undefined){
						this.setTabFocus(oTmp[o + 1]);	
						}
					}
				break;
				}
			}
		};
	
	public function repositionTabResize(Void):Void{
		tmpX = ((this.__lastW - (CieStyle.__tab.__tabOffSetX * 2) - (CieStyle.__tabPanel.__tabSpacer*this.__tabCounter))/this.__tabCounter)/CieStyle.__tabPanel.__minTitleTabWidth;		
		if(tmpX > 1){
			tmpX = 1;
			}
		var oTmp:Array = new Array();
		for(var o in this.__tabs){
			oTmp.push(o);
			}	
		this.__nextX = CieStyle.__tab.__tabOffSetX;	
		//
		delete this.__arrTabPosX;
		this.__arrTabPosX = new Array();
		//
		for(var o in oTmp){
			this.__arrTabPosX.push(this.__nextX);
			this.__tabs[oTmp[o]].resizeAllElements(tmpX);
			this.__tabs[oTmp[o]].moveTitle(this.__nextX);
			this.__tabProp[oTmp[o]].__x = this.__nextX;
			this.__nextX += this.__tabs[oTmp[o]].getMaxTitleSize() + CieStyle.__tabPanel.__tabSpacer;
			}
		this.__arrTabPosX.push(this.__nextX);	
		};
	
	public function watchDestroyed(prop, oldVal:Boolean, newVal:Boolean, obj:Object):Boolean{
		if(newVal){
			obj.__super.removeTab(obj.__tabName);
			}
		return newVal;
		};
		
	public function watchTabDrag(prop, oldVal:Boolean, newVal:Boolean, obj:Object):Boolean{
		if(newVal){
			obj.__super.__firstArrowTabName = obj.__tabName;
			obj.__super.startTabDrag(true);
		}else if(oldVal && !newVal){
			obj.__super.startTabDrag(false);
			}
		return newVal;
		};	

	private function startTabDrag(b:Boolean):Void{
		if(b){
			this.__tabDragArrow = this.__mv.attachMovie('mvTabArrow', 'mvTabArrow', this.__mv.getNextHighestDepth());
			this.__tabDragArrow._y = this.__offSetY - this.__tabDragArrow._height;
			this.__tabDragArrow._x = - 3000;
			this.__mouseListener = new Object();
			this.__mouseListener.__super = this;
			this.__mouseListener.onMouseMove = function() {
				this.__super.positionArrow();
				};
			Mouse.addListener(this.__mouseListener);
		}else{
			this.__tabDragArrow.removeMovieClip();
			delete this.__tabDragArrow;
			Mouse.removeListener(this.__mouseListener);
			delete this.__mouseListener;
			//check if the tab wants to be moved
			if(this.__lastArrowTabNum != -1){
				var oTmp:Array = new Array();
				//order it
				for(var o in this.__tabs){
					oTmp.push(o);
					}
				//swap then push away
				var arrPanelTab:Array = new Array();
				var tmpPanelTab = this.__tabs[this.__firstArrowTabName];
				delete this.__tabs[this.__firstArrowTabName];
				var tmpPanelTab2;
				for(var o=0; o<=oTmp.length;o++){
					//check if we put it there
					if(this.__lastArrowTabNum - (oTmp.length - o) == 0){
						arrPanelTab[this.__firstArrowTabName] = tmpPanelTab;
						}
					//push it in the array
					if(oTmp[o] != this.__firstArrowTabName){
						if(this.__tabs[oTmp[o]] != undefined){
							tmpPanelTab2 = this.__tabs[oTmp[o]];
							delete this.__tabs[oTmp[o]];
							arrPanelTab[oTmp[o]] = tmpPanelTab2;
							}
						}
					}
				//we have it sort so push it back into the tabs array
				this.__tabs = new Array();
				for(var o in arrPanelTab){
					this.__tabs[o] = arrPanelTab[o];
					}
				this.repositionTabResize();
				}
			}
		};
		
	private function positionArrow():Void{	
		var oPoint:Object = {x:_xmouse,y:_ymouse};
		this.__mv.globalToLocal(oPoint);
		oPoint.x -= this.__offSetX;
		oPoint.y -= this.__offSetY;
		if(oPoint.y < 0 || oPoint.y > CieStyle.__tabPanel.__titleTabHeight){
			this.__tabDragArrow._visible = false;
			this.__lastArrowTabNum = -1;
		}else{
			for(var i=0; i< (this.__arrTabPosX.length - 1); i++){
				//
				var xStart = this.__arrTabPosX[i];
				var xMiddle = this.__arrTabPosX[i] + (this.__arrTabPosX[i+1] - this.__arrTabPosX[i])/2;
				var xEnd = this.__arrTabPosX[i+1];
				//check the mouse pointer
				if(oPoint.x > xStart && oPoint.x < xMiddle){
					this.__tabDragArrow._x = this.__arrTabPosX[i] - CieStyle.__tabPanel.__tabSpacer + CieStyle.__tabPanel.__tabBorderOffSet +  - (this.__tabDragArrow._width/2);
					this.__lastArrowTabNum = i;
					this.__tabDragArrow._visible = true;
					//trace("OVER: " + this.__lastArrowTabNum);
					break;
				}else if(oPoint.x > xMiddle && oPoint.x < xEnd){
					this.__tabDragArrow._x = this.__arrTabPosX[i+1] - CieStyle.__tabPanel.__tabSpacer + CieStyle.__tabPanel.__tabBorderOffSet - (this.__tabDragArrow._width/2);
					this.__lastArrowTabNum = i + 1;
					this.__tabDragArrow._visible = true;
					//trace("OVER: " + this.__lastArrowTabNum);
					break;
					}
				}	
			}
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
		
	public function setPanelTabAction(tabName:String, strAction:String):Void{
		//trace("setPanelTabAction: " + tabName);
		this.__tabs[tabName].setPanelTabAction(strAction);
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
		//trace("WATCH_FOCUS: " + newVal);
		if(newVal == true && oldVal == false){
			//trace("TAB.onFocus: " + obj.__tabName);
			obj.__super.notifyRegisteredObjectOnFocusEvent(obj.__tabName);
			obj.__super.swapTabFocus(obj.__tabName);
			}
		return newVal;
		};
	
	/*** FOR FOCUS ON TAB EVNT *****************************************************/
	
	public function registerForOnFocusEvent(tabName:String, obj:Object):Void{
		if(obj != undefined && tabName != undefined){
			this.__registeredObject[tabName] = obj;
			}
		};	
		
	public function unregisterForOnFocusEvent(tabName:String):Void{
		//trace("unregisterForOnFocusEvent: " + tabName);
		if(tabName != undefined){
			delete this.__registeredObject[tabName];
			}
		};		
		
	private function notifyRegisteredObjectOnFocusEvent(tabName:String):Void{
		if(!this.__registeredObject[tabName].notifyOnFocusEvent()){
			delete this.__registeredObject[tabName];
			}
		};	
	
	/**********************************************************************************/
		
	public function setTabFocus(tabName:String, bNoFocus:Boolean):Void{
		//bNoFocus  = whene the tab is created we dont want the appz ficusong on it but stay on the tab taht it was before
		//trace("TABFOCUS: " + tabName + " | " + bNoFocus); 
		if(this.__tabs[tabName] != undefined){
			if(bNoFocus){
				this.__tabs[tabName].setTabFocus(false);
			}else{
				//trace("CieTabManager.FOCUS: " + tabName);
				this.swapTabFocus(tabName);
				if(!this.__tabs[tabName].getTabFocus()){
					this.__tabs[tabName].setTabFocus(true);
					}
				}
			}
		};
		
	public function getTabFocus(Void):String{
		for(var o in this.__tabs){
			if(this.__tabs[o].getTabFocus()){
				return o;
				}
			}
		return null;	
		};	
		
	public function getTabFocusByName(tabName:String):String{
		return this.__tabs[tabName].getTabFocus();	
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