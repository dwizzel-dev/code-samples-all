/*

create panels with pre established template 3 panel, 4 panel, 3 panel, 
will ghenerally be instanciate by the TabManager

*/

import control.CiePanel;

dynamic class manager.CiePanelManager{
	
	static private var __className:String = "CiePanelManager";
	private var __border:Number;
	private var __padding:Number;
	private var __model:String;
	private var __panels:Array;
	private var __mv:MovieClip;
	private var __offSetX:Number;
	private var __offSetY:Number;
	static private var __staticLeftSide:Number = 370;
	static private var __bannerBar:Object = {__w:450, __h:150};
	static private var __dockStation:Object = {__w:450, __h:100};
	static private var __navBar:Object = {__w:450, __h:90};
	//static private var __divLR:Object = {__l:0.44, __r:0.56};
	private var __divLR:Object;		
	
	private var __lastW:Number;
	private var __lastH:Number;
		
	public function CiePanelManager(mv:MovieClip, model:String, w:Number, h:Number, x:Number, y:Number){
		
		//Debug("MODEL: " + model)
		
		this.__border = CieStyle.__panel.__borderpadding;
		this.__padding = CieStyle.__panel.__cellpadding;
		
		this.__panels = new Array();
		this.__mv = mv;
		this.__model = model;
		
		this.__offSetX = x;
		this.__offSetY = y;
		
		var nw = 0;
		var nh = 0;
		var nx = 0;
		var ny = 0;
		
		this.__lastW = w;
		this.__lastH = h;
		
		w -= (this.__offSetX + (this.__border * 2));
		h -= (this.__offSetY + (this.__border * 2));
		
		//replace percent by hardcode for the left side 
		//this.__divLR = new Object();
		//this.__divLR.__l = __staticLeftSide/w;
		//this.__divLR.__r = 1 - this.__divLR.__l;
		
		this.__divLR = new Object();
		this.__divLR.__l = CieStyle.__panel.__prctLeft;
		this.__divLR.__r = 1 - CieStyle.__panel.__prctLeft;
		
		
		//trace("W: " + w + "\tL:" + this.__divLR.__l + "\R:" + this.__divLR.__r);
		
		if(model == "deux"){
			
			//TL panel
			nx = 0 + this.__offSetX + this.__border;
			ny = 0 + this.__offSetY + this.__border;
			nw = (w * __divLR.__l);
			nh = h;
			this.createPanel('_tl', nw, nh, nx, ny);
									
			//TR panel
			//if(w > BC.__user.__minStageW){
				nx = 0 + this.__offSetX + this.__border + (w * __divLR.__l) + this.__padding;
				nw = (w * __divLR.__r ) - this.__padding;
			//}else{
			//	nx = 0 + this.__offSetX + this.__border;
			//	nw = w;
			//	}
			ny = 0 + this.__offSetY + this.__border;
			nh = h;
			this.createPanel('_tr', nw, nh, nx, ny);
		
		}else if(model == "deux_H"){
			
			//TL panel
			nx = 0 + this.__offSetX + this.__border;
			ny = 0 + this.__offSetY + this.__border;
			nw = (w * 0.6);
			nh = h;
			this.createPanel('_tl', nw, nh, nx, ny);
									
			//TR panel
			nx = 0 + this.__offSetX + this.__border + (w * 0.6) + this.__padding;
			ny = 0 + this.__offSetY + this.__border;
			nw = (w * 0.4 ) - this.__padding;
			nh = h;
			this.createPanel('_tr', nw, nh, nx, ny);
		
		}else if(model == "deux_V"){
			
			//TL panel
			nx = 0 + this.__offSetX + this.__border;
			ny = 0 + this.__offSetY + this.__border;
			nw = w;
			nh = h - __navBar.__h - this.__padding;
			this.createPanel('_tl', nw, nh, nx, ny);
			
			//BL panel
			nx = 0 + this.__offSetX + this.__border;
			ny = (h - __navBar.__h) + (this.__offSetY + this.__border);
			nw = w;
			nh = __navBar.__h;
			this.createPanel('_bl', nw, nh, nx, ny);
						
		}else if(model == "trois"){	
		
			//TL panel
			nx = 0 + this.__offSetX + this.__border;
			ny = 0 + this.__offSetY + this.__border;
			nw = (w * __divLR.__l);
			nh = h - __navBar.__h - this.__padding;
			this.createPanel('_tl', nw, nh, nx, ny);
			
			//BL panel
			nx = 0 + this.__offSetX + this.__border;
			ny = (h - __navBar.__h) + (this.__offSetY + this.__border);
			nw = (w * __divLR.__l);
			nh = __navBar.__h;
			this.createPanel('_bl', nw, nh, nx, ny);
			
			//TR panel
			nx = 0 + this.__offSetX + this.__border + (w * __divLR.__l) + this.__padding;
			ny = 0 + this.__offSetY + this.__border;
			nw = (w * __divLR.__r ) - this.__padding;
			nh = h;
			this.createPanel('_tr', nw, nh, nx, ny);
		
		}else if(model == "quatre"){
			
			//TL panel
			nx = 0 + this.__offSetX + this.__border;
			ny = 0 + this.__offSetY + this.__border;
			nw = (w * __divLR.__l);
			nh = h - __navBar.__h - this.__padding;
			this.createPanel('_tl', nw, nh, nx, ny);
			
			//BL panel
			nx = 0 + this.__offSetX + this.__border;
			ny = (h - __navBar.__h) + (this.__offSetY + this.__border);
			nw = (w * __divLR.__l);
			nh = __navBar.__h;
			this.createPanel('_bl', nw, nh, nx, ny);
			
			//TR panel
			nx = 0 + this.__offSetX + this.__border + (w * __divLR.__l) + this.__padding;
			ny = 0 + this.__offSetY + this.__border;
			nw = (w * __divLR.__r ) - this.__padding;
			nh =  h - __dockStation.__h - this.__padding;
			this.createPanel('_tr', nw, nh, nx, ny);
			
			//BR panel
			nx = 0 + this.__offSetX + this.__border + (w * __divLR.__l) + this.__padding;
			ny = (h - __dockStation.__h) + (this.__offSetY + this.__border);
			nw = (w * __divLR.__r ) - this.__padding;
			nh = __dockStation.__h;
			this.createPanel('_br', nw, nh, nx, ny);
			
		}else if(model == "cinq"){
			
			//TL panel
			nx = 0 + this.__offSetX + this.__border;
			ny = 0 + this.__offSetY + this.__border;
			nw = (w * __divLR.__l);
			nh = h - __navBar.__h - this.__padding;
			this.createPanel('_tl', nw, nh, nx, ny);
			
			//BL panel
			nx = 0 + this.__offSetX + this.__border;
			ny = (h - __navBar.__h) + (this.__offSetY + this.__border);
			nw = (w * __divLR.__l);
			nh = __navBar.__h;
			this.createPanel('_bl', nw, nh, nx, ny);
			
			//TR panel
			nx = 0 + this.__offSetX + this.__border + (w * __divLR.__l) + this.__padding;
			ny = 0 + this.__offSetY + this.__border;
			nw = (w * __divLR.__r ) - this.__padding;
			nh =  h - __dockStation.__h - __bannerBar.__h - (this.__padding * 2);
			this.createPanel('_tr', nw, nh, nx, ny);
			
			//BMR panel
			nx = 0 + this.__offSetX + this.__border + (w * __divLR.__l) + this.__padding;
			ny = (h - __dockStation.__h - __bannerBar.__h - this.__padding) + (this.__offSetY + this.__border);
			nw = (w * __divLR.__r ) - this.__padding;
			nh = __bannerBar.__h;
			this.createPanel('_bmr', nw, nh, nx, ny);
			
			//BR panel
			nx = 0 + this.__offSetX + this.__border + (w * __divLR.__l) + this.__padding;
			ny = (h - __dockStation.__h) + (this.__offSetY + this.__border);
			nw = (w * __divLR.__r ) - this.__padding;
			nh = __dockStation.__h;
			this.createPanel('_br', nw, nh, nx, ny);
			
		}else{
			nx = 0 + this.__offSetX + this.__border;
			ny = 0 + this.__offSetY + this.__border;
			nw = w;
			nh = h;
			this.createPanel('_tl', nw, nh, nx, ny);
			}
		};
		
	public function setPanelFocus(b:Boolean):Void{
		for(var o in this.__panels){
			this.__panels[o].setPanelFocus(b);
			}
		};
		
	private function createPanel(cname:String, w:Number, h:Number, x:Number, y:Number):Void{
		this.__panels[cname] = new CiePanel(this.__mv, w, h, x, y);
		};
		
	private function movePanel(cname:String, x:Number, y:Number):Void{
		this.__panels[cname].movePanel(x, y);
		};	
		
	public function resize(w:Number, h:Number):Void{
		
		var nw:Number = 0;
		var nh:Number = 0;
		var nx:Number = 0;
		var ny:Number = 0;

		//var oldH:Number = h;
		//var oldW:Number = w;
		
		this.__lastW = w;
		this.__lastH = h;
		
		h -= (this.__offSetY + (this.__border * 2));
		w -= (this.__offSetX + (this.__border * 2));
		
		//replace percent by hardcode for the left side 
		//this.__divLR.__l = __staticLeftSide/w;
		//this.__divLR.__r = 1 - this.__divLR.__l;
		
		this.__divLR.__l = CieStyle.__panel.__prctLeft;
		this.__divLR.__r = 1 - CieStyle.__panel.__prctLeft;
			
		for(var o in this.__panels){
			if(this.__model == "deux"){
				if(o == "_tl"){
					nx = 0 + this.__offSetX + this.__border;
					ny = 0 + this.__offSetY + this.__border;
					nw = (w * __divLR.__l);
					nh = h;
				}else if(o == "_tr"){
					//if(w > BC.__user.__minStageW){
						nx = 0 + this.__offSetX + this.__border + (w * __divLR.__l) + this.__padding;
						nw = (w * __divLR.__r ) - this.__padding;
					//}else{
					//	nx = 0 + this.__offSetX + this.__border;
					//	nw = w;
					//	}
					ny = 0 + this.__offSetY + this.__border;
					nh = h;
					}
					
			}else if(this.__model == "deux_H"){		
				if(o == "_tl"){
					nx = 0 + this.__offSetX + this.__border;
					ny = 0 + this.__offSetY + this.__border;
					nw = (w * 0.6);
					nh = h;
				}else if(o == "_tr"){
					nx = 0 + this.__offSetX + this.__border + (w * 0.6) + this.__padding;
					ny = 0 + this.__offSetY + this.__border;
					nw = (w * 0.4 ) - this.__padding;
					nh = h;
					}	
					
			}else if(this.__model == "deux_V"){
				if(o == "_tl"){
					nx = 0 + this.__offSetX + this.__border;
					ny = 0 + this.__offSetY + this.__border;
					nw = w;
					nh = h - __navBar.__h - this.__padding;
				}else if(o == "_bl"){
					nx = 0 + this.__offSetX + this.__border;
					ny = (h - __navBar.__h) + (this.__offSetY + this.__border);
					nw = w;
					nh = __navBar.__h;
					}
					
			}else if(this.__model == "trois"){
				if(o == "_tl"){
					nx = 0 + this.__offSetX + this.__border;
					ny = 0 + this.__offSetY + this.__border;
					nw = (w * __divLR.__l);
					nh = h - __navBar.__h - this.__padding;
				}else if(o == "_bl"){
					nx = 0 + this.__offSetX + this.__border;
					ny = (h - __navBar.__h) + (this.__offSetY + this.__border);
					nw = (w * __divLR.__l);
					nh = __navBar.__h;
				}else if(o == "_tr"){
					nx = 0 + this.__offSetX + this.__border + (w * __divLR.__l) + this.__padding;
					ny = 0 + this.__offSetY + this.__border;
					nw = (w * __divLR.__r ) - this.__padding;
					nh = h;
					}		
					
			}else if(this.__model == "quatre"){
				if(o == "_tl"){
					nx = 0 + this.__offSetX + this.__border;
					ny = 0 + this.__offSetY + this.__border;
					nw = (w * __divLR.__l);
					nh = h - __navBar.__h - this.__padding;
				}else if(o == "_bl"){
					nx = 0 + this.__offSetX + this.__border;
					ny = (h - __navBar.__h) + (this.__offSetY + this.__border);
					nw = (w * __divLR.__l);
					nh = __navBar.__h;
				}else if(o == "_tr"){
					nx = 0 + this.__offSetX + this.__border + (w * __divLR.__l) + this.__padding;
					ny = 0 + this.__offSetY + this.__border;
					nw = (w * __divLR.__r ) - this.__padding;
					nh =  h - __dockStation.__h - this.__padding;
				}else if(o == "_br"){
					nx = 0 + this.__offSetX + this.__border + (w * __divLR.__l) + this.__padding;
					ny = (h - __dockStation.__h) + (this.__offSetY + this.__border);
					nw = (w * __divLR.__r ) - this.__padding;
					nh = __dockStation.__h;
					}
			}else if(this.__model == "cinq"){
				if(o == "_tl"){
					nx = 0 + this.__offSetX + this.__border;
					ny = 0 + this.__offSetY + this.__border;
					nw = (w * __divLR.__l);
					nh = h - __navBar.__h - this.__padding;
				}else if(o == "_bl"){
					nx = 0 + this.__offSetX + this.__border;
					ny = (h - __navBar.__h) + (this.__offSetY + this.__border);
					nw = (w * __divLR.__l);
					nh = __navBar.__h;
				}else if(o == "_tr"){
					nx = 0 + this.__offSetX + this.__border + (w * __divLR.__l) + this.__padding;
					ny = 0 + this.__offSetY + this.__border;
					nw = (w * __divLR.__r ) - this.__padding;
					nh =  h - __dockStation.__h - __bannerBar.__h - (this.__padding * 2);
				}else if(o == "_bmr"){
					nx = 0 + this.__offSetX + this.__border + (w * __divLR.__l) + this.__padding;
					ny = (h - __dockStation.__h - __bannerBar.__h - this.__padding) + (this.__offSetY + this.__border);
					nw = (w * __divLR.__r ) - this.__padding;
					nh = __bannerBar.__h;
				}else if(o == "_br"){	
					nx = 0 + this.__offSetX + this.__border + (w * __divLR.__l) + this.__padding;
					ny = (h - __dockStation.__h) + (this.__offSetY + this.__border);
					nw = (w * __divLR.__r ) - this.__padding;
					nh = __dockStation.__h;
					}
			}else{
				if(o == '_tl'){
					nx = 0 + this.__offSetX + this.__border;
					ny = 0 + this.__offSetY + this.__border;
					nw = w;
					nh = h;
					}
				}
			this.__panels[o].resize(nx, ny, nw, nh);
			}
		};
		
	public function setPanelContent(panelName:String, strToLoad:String):Void{
		if(this.__panels[panelName] != undefined){
			this.__panels[panelName].setContent(strToLoad); 
			}
		};
		
	public function setPanelBgColor(panelName:String, cColor:Number):Void{
		if(this.__panels[panelName] != undefined){
			this.__panels[panelName].setBgColor(cColor); 
			}
		};

	public function setPanelGlow(panelName:String, b:Boolean):Void{
		if(this.__panels[panelName] != undefined){
			this.__panels[panelName].setGlow(b); 
			}
		};		
		
	public function getPanels(Void):Array{
		var arr:Array = new Array();
		for(var o in this.__panels){
			arr.push(this.__panels[o]);
			}
		return arr;
		};
		
	public function removePanels(Void):Void{
		for(var o in this.__panels){
			this.__panels[o].clearRegisteredObjects();
			this.__panels[o].removePanel();
			this.__panels[o] = null;
			delete this.__panels[o];
			}
		};
		
	public function getPanel(panelName:String):CiePanel{
		if(this.__panels[panelName] == undefined){
			return null;
		}else{
			return this.__panels[panelName];
			}
		};	
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CiePanelManager{
		return this;
		};	
	
	}