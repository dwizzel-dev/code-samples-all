/*

Stage Manager dispath the onResise Event
is a Singleton, 
instanciate like new CieStage.getInstance();
and register object who need stage resize event

*/

import utils.CieThread;

dynamic class chat.CieStageManager{

	static private var __className = 'CieStageManager';
	static private var __instance:CieStageManager;
	
	private var __stageListener:Object;
	private var __registeredObject:Array;
	
	//resize
	//private var __w:Number;
	//private var __h:Number;
	//private var __minWidth:Number;
	//private var __minHeight:Number;
	//public var __arrThreadFetchNewBanner:Array;
	
	//private function CieStageManager(minWidth:Number, minHeight:Number){
	private function CieStageManager(){
		/*
		this.__arrThreadFetchNewBanner = new Array();
		this.__arrThreadFetchNewBanner['thread'] = null;
		this.__arrThreadFetchNewBanner['run'] = 0;
		*/
		//this.__minWidth = minWidth;
		//this.__minHeight = minHeight;
	
		this.initStage();
		this.clearRegisteredObject();
		this.initListener();
		};
		
	//static public function getInstance(minWidth:Number, minHeight:Number):CieStageManager{
	static public function getInstance(Void):CieStageManager{
		if(__instance == undefined){
			//__instance = new CieStageManager(minWidth, minHeight);
			__instance = new CieStageManager();
			}
		return __instance;
		};
		
	public function registerObject(obj:Object):Void{
		if(typeof(obj) == 'object'){
			this.__registeredObject.push(obj);
			}
		};
		
	public function registerMultipleObject(arrObj:Array):Void{
		for(var o in arrObj){
			this.registerObject(arrObj[o]);
			}
		};	
		
	public function clearRegisteredObject(Void):Void{
		delete this.__registeredObject;
		this.__registeredObject = new Array();
		};
		
	public function removeRegisteredObject(obj:Object):Void{
		for(var o in this.__registeredObject){
			if(this.__registeredObject[o] == obj){
				delete this.__registeredObject[o];
				break;
				}
			}
		};
	
	private function initStage(Void):Void{
		Stage.align = 'TL';
		Stage.scaleMode = 'noScale';
		Stage.showMenu = false;
		};
	
	private function initListener(Void):Void{
		this.__stageListener = new Object();
		this.__stageListener.__super = this;
		this.__stageListener.onResize = function(Void):Void{
			/*
			if( this.__super.__arrThreadFetchNewBanner['run'] == 0){
				cBrowser.showBrowser(false);
				this.__super.__arrThreadFetchNewBanner['run'] = 1;
				this.__super.__arrThreadFetchNewBanner['thread'] = cThreadManager.newThread(250, this.__super, 'tCheckOnStopRedraw', {__superclass:this.__super, __lastW:Stage.width, __lastH:Stage.height});	
				}
			
			//for banner positionning
			if((Stage.width > (BC.__banner.__staticRightWidth + BC.__system.__resMax)) && ((Stage.height - CieStyle.__basic.__toolHeight) > BC.__banner.__staticMinHeight)){	
				BC.__banner.__bottomHeight = 0;
				BC.__banner.__rightWidth = BC.__banner.__staticRightWidth;
				cBrowser.repositionBanner('vertical');
			}else{
				BC.__banner.__bottomHeight = BC.__banner.__staticBottomHeight;
				BC.__banner.__rightWidth = 0;
				cBrowser.repositionBanner('horizontal');
				}
			*/			
			for(var o in this.__super.__registeredObject){
				this.__super.__registeredObject[o].resize(Stage.width, Stage.height);
				}
						
			};
		Stage.addListener(this.__stageListener);
		};
		
	/*
	public function tCheckOnStopRedraw(obj:Object):Boolean{
		if((obj.__lastW == Stage.width) && (obj.__lastH == Stage.height)){
			cBrowser.fetchBannerOnResize();	
			obj.__superclass.__arrThreadFetchNewBanner['run'] = 0;
			return false;
			}
		obj.__lastW = Stage.width;
		obj.__lastH = Stage.height;
		return true;
		};	
	*/
	
	public function redraw(Void):Void{
		/*
		this.__w = Stage.width;
		this.__h = Stage.height;
		//width
		if(this.__w < this.__minWidth){
			this.__w = this.__minWidth;
			}
		//height	
		if(this.__h < this.__minHeight){
			this.__h = this.__minHeight;
			}	
		//send event
		for(var o in this.__registeredObject){
			this.__registeredObject[o].resize(this.__w, this.__h);
			}
		*/
		for(var o in this.__registeredObject){
			this.__registeredObject[o].resize(Stage.width, Stage.height);
			}
		
		};	
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieStageManager{
		return this;
		};
	
	}
