/*

NOTES: ne pas oublier de faire des rajouts des observer quand un nouveau s'ajoute a la liste () si il est active, c<est a dire pas undefined


*/

import utils.CieThread;

dynamic class manager.CieThreadManager{
	
	static private var __className = 'CieThreadManager';
	private var tObjects:Array;
	private var tNumber:Number;
	//private var tObserver:Object;
	
	static private var __instance:CieThreadManager;
	
	//public var bShowList:Boolean;
	
	private function CieThreadManager(Void){
		this.tObjects = new Array();
		this.tNumber = 0;
		//this.init();
		};
		
	static public function getInstance(Void):CieThreadManager{
		if(__instance == undefined) {
			__instance = new CieThreadManager();
			}
		return __instance;
		};		
		
	/*
	private function init(Void):Void{
		//this.tObserver = undefined;
		//this.bShowList = false;
		this.tObjects = new Array();
		this.tNumber = 0;
		};
	*/
	
	public function newThread(priority:Number, mvLevel:Object, funcName:String, param:Object):CieThread{
		this.tNumber++;
		this.tObjects[this.tNumber] = new Object();
		//this.tObjects[this.tNumber].__priority = priority;
		this.tObjects[this.tNumber].thread = new CieThread(funcName, mvLevel, priority, param);
		this.tObjects[this.tNumber].thread.__MT = this;
		this.tObjects[this.tNumber].thread.__id = this.tNumber;
		this.tObjects[this.tNumber].thread.watchdog = function(property, oldvalue, newvalue):Void{
			if(property == '__active' && !newvalue){
				this.unwatch('__active');
				this.__MT.destroy(this.__id);
				}
			};
		this.tObjects[this.tNumber].thread.watch('__active', this.tObjects[this.tNumber].thread.watchdog);	
		this.tObjects[this.tNumber].thread.start();
		/*
		if(this.bShowList){
			this.list();
			}
		*/
		return this.tObjects[this.tNumber].thread;
		};
		
	/*
	public function list(Void):Void{
		var ind = null;
		Debug('---------------------- THREAD LISTING -----------------------------------------------------------------');
		Debug("ID\t\tFUNC\t\tACTIVE\t\tPRIORITY\t\tSTATE");
		for(ind in this.tObjects){
			Debug(ind + '\t\t' + this.tObjects[ind].thread.__humanname + '\t\t' + this.tObjects[ind].thread.__active + '\t\t' + this.tObjects[ind].__priority + '\t\t' + this.tObjects[ind].thread.__state);
			}
		Debug('');	
		};
	*/	
		
	public function destroy(id:Number):Void{
		/*
		if(this.tObserver != undefined){
				this.notifies(id);
				}
		*/		
		this.tObjects[id].thread = null;
		delete this.tObjects[id].thread;
		this.tObjects[id] = null;
		delete this.tObjects[id];
		/*
		if(this.bShowList){
			this.list();
			}
		*/	
		};
		
	public function kill(tObj:CieThread):Void{
		tObj.destroy();
		//tObj = null;
		delete tObj;
		};
		
	/*
	public function addObservers(funcname:Function, param:Object):Void{
		this.tObserver = new Object();
		this.tObserver.__funcname = funcname;
		this.tObserver.__param = param;
		};
	*/	
	/*
	public function notifies(id:Number):Void{
		this[this.tObserver.__funcname](this.tObjects[id].thread, this.tObserver.__param);
		};
	*/
		
	public function killAll(Void):Void{
		for(var ind in this.tObjects){
			this.tObjects[ind].thread.destroy();
			}
		};
	
	/*	
	public function traceObj(obj):Void{
		var ind = null;
		for(ind in obj){
			if(typeof(obj[ind]) == 'object'){
				this.traceObj(obj[ind]);
			}else{		
				Debug(obj[ind]);
				}
			}	
		};
	*/	
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieThreadManager{
		return this;
		};		
	}