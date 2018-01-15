/*

abtarct function layer between user/event call and function class
action are called by string and convert to object here
security abtract layer between user command and CieFunctions

*/

import core.CieFunctions;

dynamic class utils.CieFunctionTable{

	static private var __className = 'CieFunctionTable';
	static private var __instance:CieFunctionTable;
		
	public function CieFunctionTable(Void){
		//method
		};
		
	public function callFunction(funcName:String, fParams:Array):Void{
		switch(funcName){
			case 'tracert'://
					break;
			
			case 'openTab'://
					var arrParam = fParams[0].split('/');
					cFunc.openTab(arrParam);
					break;
					
			case 'refreshList'://
					cFunc.refreshList();
					break;
						
			default://
					break;
								
			}
		};
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieFunctionTable{
		return this;
		};
		
	}	