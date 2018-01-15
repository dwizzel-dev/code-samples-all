/*

single Thread class

*/


dynamic class utils.CieThread{

	private static var __className = 'CieThread';
	private var __priority:Number;
	private var __param:Object;
	private var __intervalid:Number;
	private var __levelid:Object;
	
	public var __funcname:String;
	public var __id:Number;
	
	public var __active:Boolean;
	public var __state:Boolean;
		
	public function CieThread(name:String,level:Object,priority:Number, param:Object){
		this.__funcname = name;
		this.__levelid = level;
		this.__priority = priority;
		this.__param = param;
		this.__active = true;
		};
	
	private function run(Void):Void{
		clearInterval(this.__intervalid);
		if(this.__active){
			var bStillRun:Boolean = this.__levelid[this.__funcname](this.__param);
			if(bStillRun){
				this.__intervalid = setInterval(this, 'run', this.__priority);
			}else{
				//this.destroy();
				this.__state = false;
				this.__active = false;
				}
			}
		};
		
	public function start(Void):Void{
		if(this.__active){
			clearInterval(this.__intervalid);
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
			clearInterval(this.__intervalid);
			this.__state = false;
			this.__active = false;
			}
		};
	}