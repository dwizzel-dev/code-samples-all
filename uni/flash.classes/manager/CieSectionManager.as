/*

multiple tab

NOTES:

Il faut absolum,ent avertir le ContentManager que le Tab est detruiot pour qu'il reset ses references

*/

import control.CieSection;
import manager.CiePanelManager;
//import control.CiePanelTab;
import control.CiePanel;
//import graphic.CieLine;
//import graphic.CieSquareTabPanel;

dynamic class manager.CieSectionManager{
	
	static private var __className:String = 'CieSectionManager';
	private var __offSet:Number = -2;
	
	private var __tabs:Array;
	private var __mv:MovieClip;
	private var __offSetX:Number;
	private var __offSetY:Number;
	private var __lastW:Number;
	private var __lastH:Number;

	private var __lastRemovedTab:String; //for the ContentManager watch
			
	public function CieSectionManager(mv:MovieClip, w:Number, h:Number, x:Number, y:Number){
		this.__offSetX = x;
		this.__offSetY = y;
		this.__mv = mv;
		this.__lastW = w;
		this.__lastH = h;
		this.__lastRemovedTab = '';
		this.__tabs = new Array();
		};

	public function createSection(tabName:String):Boolean{
		//Debug("createSection: " + tabName)
		if(this.__tabs[tabName] == undefined){
			this.__tabs[tabName] = new CieSection(this.__mv, this.__offSetX, this.__offSetY, this.__lastW, this.__lastH, tabName);
			this.__tabs[tabName].watch('__focus', this.watchFocus, {__tabName:tabName, __super:this});
			this.__tabs[tabName].watch('__destroyed', this.watchDestroyed, {__tabName:tabName, __super:this});
			//Debug('CieSectionManager.createSection(' + tabName + ')');
			return true;
		}else{
			return false;
			}
		};

	public function removeTabByName(tabName:String):Void{
		this.__tabs[tabName].Destroy();
		};
	
	public function removeTab(tabName:String):Void{
		this.__tabs[tabName] = null;
		delete this.__tabs[tabName];
		this.__lastRemovedTab = tabName;
		};
		
	public function removeTabs(Void):Void{
		for(var o in this.__tabs){
			this.__tabs[o].Destroy();
			}
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
		if(this.__tabs[tabName] != undefined){
			return this.__tabs[tabName].getPanelSize(panelName);
			}
		};	
		
	public function getPanelClass(tabName:String, panelName:String):CiePanel{
		if(this.__tabs[tabName] != undefined){
			return this.__tabs[tabName].getPanelClass(panelName);
			}
		};	
		
	public function getPanelManager(tabName:String):CiePanelManager{
		if(this.__tabs[tabName] != undefined){
			return this.__tabs[tabName].getPanelManager();
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
			//Debug('CieSectionManager.setTabFocus(' + tabName + ')');
			if(!this.__tabs[tabName].getTabFocus()){
				this.__tabs[tabName].setTabFocus(true);
				}
			}
		};
		
	private function swapTabFocus(tabName:String):Void{
		for(var o in this.__tabs){
			if(o != tabName){
				if(this.__tabs[o].getTabFocus()){
					this.__tabs[o].setTabFocus(false); //set the focus to false; if it was true
					}
				}
			}		
		};
	
	public function resize(w:Number, h:Number):Void{
		for(var o in this.__tabs){
			if(this.__tabs[o] != undefined){
				this.__tabs[o].resize(w, h);
				}
			}	
		this.__lastW = w;
		this.__lastH = h; 
		};
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieSectionManager{
		return this;
		};	
	
	}	