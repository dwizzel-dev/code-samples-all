//manager/CieEventManager.as

/*

all event must pass thrgought this so we'll have control on double/triple click of exactly the same action

*/

import utils.CieEvent;
import utils.CieFunctionTable;

dynamic class manager.CieEventManager{

	static private var __className = 'CieEventManager';
	private var __event:Array;
	private var __intervalProcessEvent:Number;
	private var __fTable:CieFunctionTable;
	
	public function CieEventManager(fTable:CieFunctionTable){
		this.__fTable = fTable;
		this.init();
		};
			
	private function init(Void):Void{	
		this.__event = new Array();
		};
		
	private function processEvent(Void):Void{
		clearInterval(this.__intevalProcessEvent);
		if(this.__event.length != 0){
			var nEvent = this.__event.shift();
			this.__fTable.callFunction(nEvent.getFuncName(), nEvent.getParams());
			delete nEvent;
			this.__intevalProcessEvent = setInterval(this, 'processEvent', 1);
			}
		};
		
	public function addEvent(action:String, functionName:String, functionParam:Array):Void{
		/*
		clearInterval(this.__intevalProcessEvent);
		var bFound:Boolean = false;
		for(var o in this.__event){
			if(this.__event[o].getAction() == action && this.__event[o].getFuncName() == functionName){
				if(this.__event[o].getParams() == functionParam){
					//trace("EVENT_FOUND");
					bFound = true;
					}
				}
			}	
		if(!bFound){
			//trace("EVENT_ADD");
			this.__event.push(new CieEvent(action ,functionName, functionParam));
			}
		this.__intevalProcessEvent = setInterval(this, 'processEvent', 1);	
		*/
		/*
		
		WILL PROCESSED IT RIGHT F* NOW
		
		*/
		this.__fTable.callFunction(functionName, functionParam);
		};
	
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieEventManager{
		return this;
		};
	*/	
	}	