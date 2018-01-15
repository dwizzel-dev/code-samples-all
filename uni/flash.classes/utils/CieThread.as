/*

single Thread class

*/


dynamic class utils.CieThread{

	private static var __className = 'CieThread';
	private var __priority:Number;
	private var __param:Object;
	//private var __type:Boolean;
	private var __intervalid:Number;
	private var __levelid:Object;
	//private var __observer:Object;
	//private var __birth:Number;
	//private var __death:Number;
	
	public var __funcname:String;
	//public var __id:Number;
	//public var __humanname:String;
	
	public var __active:Boolean;
	public var __state:Boolean;
		
	public function CieThread(name:String,level:Object,priority:Number, param:Object){
		//this.__humanname = level + '.' + name;
		this.__funcname = name;
		this.__levelid = level;
		this.__priority = priority;
		this.__param = param;
		this.__active = true;
		//this.init();
		};
	
	/*	
	private function init(Void):Void{
		//this.__birth = new Date().getTime();
		//this.__death = 0;
		this.__active = true;
		//this.__observer = undefined;
		};
	*/
		
	private function run(Void):Void{
		clearInterval(this.__intervalid);
		if(this.__active){
			var bStillRun:Boolean = this.__levelid[this.__funcname](this.__param);
			if(bStillRun){
				this.__intervalid = setInterval(this, 'run', this.__priority);
			}else{
				//this.destroy();
				delete this.__param;
				this.__param = undefined;
				delete this.__levelid;
				this.__levelid = undefined;
				this.__state = false;
				this.__active = false;
				}
			}
		};
		
	public function start(Void):Void{
		if(this.__active){
			clearInterval(this.__intervalid);
			//this.run();
			this.__intervalid = setInterval(this, 'run', this.__priority);
			this.__state = true;
			}
		};
		
	public function stop(Void):Void{
		if(this.__active){
			clearInterval(this.__intervalid);
			this.__state = false;
			}
		};
		
	public function destroy(Void):Void{
		if(this.__active){
			//this.__death = new Date().getTime();
			clearInterval(this.__intervalid);
			/*
			if(this.__observer != undefined){
				this.notify();
				}
			*/	
			this.__state = false;
			this.__active = false;
			}
		};
	
	/*
	public function getState(Void):Boolean{
		return this.__state;
		};
	*/	
		
	/*
	public function getBirth(Void):Number{
		return this.__birth;
		};	
	*/
	/*
	public function getDeath(Void):Number{
		return this.__death;
		};	
	*/
	/*
	public function getName(Void):String{
		return this.__id + '::' + this.__humanname;
		};
	*/
	/*
	public function addObserver(funcname:String, param:Object):Void{
		this.__observer = new Object();
		this.__observer.__funcname = funcname;
		this.__observer.__param = param;
		};
	*/	
	
	/*
	public function notify(Void):Void{
		this[this.__observer.__funcname](this.__observer.__param);
		};
	*/	
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieThread{
		return this;
		};	
	}