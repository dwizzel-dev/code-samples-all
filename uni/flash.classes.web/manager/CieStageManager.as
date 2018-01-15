/*

Stage Manager dispath the onResise Event
is a Singleton, 
instanciate like new CieStage.getInstance();
and register object who need stage resize event

*/

dynamic class manager.CieStageManager{

	static private var __className = "CieStageManager";
	static private var __instance:CieStageManager;
	private var __stageListener:Object;
	private var __registeredObject:Array;
	
	//resize
	static private var MINWIDTH:Number = 470;
	static private var MINHEIGHT:Number = 415;
	
	private function CieStageManager(Void){
		//trace("new instance of :" + __className);
		this.initStage();
		//this.initStyles();
		this.clearRegisteredObject();
		this.initListener();
		};
		
	static public function getInstance(Void):CieStageManager{
		if(__instance == undefined) {
			__instance = new CieStageManager();
			}
		return __instance;
		};
		
	public function registerObject(obj:Object):Void{
		if(typeof(obj) == "object"){
			this.__registeredObject.push(obj);
			//trace(__className + ": OBJECT REGISTRED " + obj.getClassName());
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
		Stage.align = "TL";
		Stage.scaleMode = "noScale";
		Stage.showMenu = false;
		};
	
	private function initListener(Void):Void{
		this.__stageListener = new Object();
		this.__stageListener.__super = this;
		this.__stageListener.onResize = function(){
			this.__super.redraw();	
			};
		Stage.addListener(this.__stageListener);
		};
		
	public function redraw(Void):Void{
		//constraint form
		var w:Number = Stage.width;
		var h:Number = Stage.height;
		//width
		if(w < MINWIDTH){
			w = MINWIDTH;
			}
		//height	
		if(h < MINHEIGHT){
			h = MINHEIGHT;
			}	
		for(var o in this.__registeredObject){
			 this.__registeredObject[o].resize(w, h);
			}
		};	
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieStageManager{
		return this;
		};
	
	}
