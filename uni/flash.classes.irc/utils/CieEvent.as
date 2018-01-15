//utils/CieEvent.as

/*

user event catcher and function dispatcher

*/

//import utils.CiePublicFunctions;

dynamic class utils.CieEvent{

	static private var __className = 'CieEvent';
	private var __action:String;
	private var __funcName:String;
	private var __param:Array;
	
	public function CieEvent(eventAction:String, functionName:String, functionParam:Array){
		this.__action = eventAction;
		this.__funcName = functionName;
		this.__param = functionParam;
		};
		
	public function getAction(Void):String{
		return this.__action;
		};
		
	public function getFuncName(Void):String{
		return this.__funcName;
		};

	public function getParams(Void):Array{
		return this.__param;
		};		
	/*	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieEvent{
		return this;
		};
	*/	
	}	