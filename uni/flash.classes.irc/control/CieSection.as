/*

a tab window gerer/creted by the TabManager 

*/
//import mx.controls.Label;
//import effect.CieGlow;
//import effect.CieDropShadow;

//import graphic.CieSquareTabPanel;
//import graphic.CieLine;
import manager.CiePanelManager;
import control.CiePanel;
import utils.CieUserEventDispatcher;


dynamic class control.CieSection{

	static private var __className = "CieSection";
	private var __mv:MovieClip;
	private var __x:Number;
	private var __y:Number;
	private var __lastW:Number;
	private var __lastH:Number;
	private var __panel:MovieClip;
	private var __tabKey:Number;
	private var __panelManager:CiePanelManager;
	private var __focus:Boolean;
	public var __destroyed:Boolean;
	
	public function CieSection(mv:MovieClip, x:Number, y:Number, w:Number, h:Number, cname:String){
		
		this.__destroyed = false;
		
		this.__mv = mv;
		this.__x = x;
		this.__y = y;
		this.__lastW = w;
		this.__lastH = h;
		
		this.init();
		this.setTabFocus(true);
		};
	
	private function Destroy(Void):Void{
		
		this.__panel.removeMovieClip();
		this.__panel = null;
		delete this.__panel;
		
		this.__panelManager.removePanels();
		this.__panelManager = null;
		delete this.__panelManager;
		
		this.__destroyed = true;
		
		};
		
	public function setTabFocus(b:Boolean):Void{
		if(b != this.__focus){
			this.__focus = b;
			this.__panel._visible = this.__focus;
			this.resize(this.__lastW, this.__lastH);
			}
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
		};
		
	private function init(Void):Void{
		//create a atab layer
		this.__focus = true;
		this.__tabKey = Math.round(Math.random() * (1000000-0)) + 1;
		this.__panel = this.__mv.createEmptyMovieClip('Panel_' + this.__tabKey, this.__mv.getNextHighestDepth());
		this.__panel._y = this.__y;
		this.drawBgTabBar();
		};
		
	public function drawBgTabBar(Void):Void{
		/*
		if(this.__bgTab == undefined){
			this.__bgTab = this.__mv.createEmptyMovieClip('BGTAB', this.__mv.getNextHighestDepth());
			}
		*/
		//draw directly
		this.__panel.clear();
		//this.__panel.lineStyle(2, 0x000000, 100);
		this.__panel.beginFill(CieStyle.__tab.__bgTabColor, 100);
		this.__panel.moveTo(0, 0);
		this.__panel.lineTo(this.__lastW, 0);
		this.__panel.lineTo(this.__lastW, this.__lastH);
		this.__panel.lineTo(0, this.__lastH);
		this.__panel.lineTo(0, 0);
		this.__panel.endFill();
				
		};
		
	private function resize(w:Number, h:Number):Void{
		//draw
		if(this.__focus){
			if(this.__panelManager != undefined){
				this.__panelManager.resize(w, (h - this.__y));
				}
			}
		this.__lastW = w;
		this.__lastH = h;
		this.drawBgTabBar();
		}
	
	public function animPanelForPub(panelName:String, cColor:Number):Void{
		this.__panelManager.setPanelBgColor(panelName, cColor);
		};
	
	/*	
	public function changeY(newY:Number):Void{
		Debug("animPanel_2_C");
		this.__y = newY;
		this.__panel._y = this._y;
		this.resize(this.__lastW, this.__lastH);
		};
	*/
	
	public function disablePanelScroll(panelName:String):Void{
		this.__panelManager.getPanel(panelName).disableScroll();
		};		
		
	public function createPanels(model:String):Void{
		this.__panelManager = new CiePanelManager(this.__panel, model, this.__lastW, (this.__lastH - this.__y), 0, 0);
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
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieSection{
		return this;
		};
	}	