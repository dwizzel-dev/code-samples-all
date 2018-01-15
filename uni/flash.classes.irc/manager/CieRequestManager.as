/*	

a request manager for the CieRequest class

TODO make a request queue

*/

import comm.CieRequest;

dynamic class manager.CieRequestManager{

	static private var __className:String = 'CieRequestManager';
	static private var __instance:CieRequestManager;
	
	private var __requestId:Number;
	private var __requestMaxBuffer:Number;
	private var __arrRequest:Array;
		
	private function CieRequestManager(Void){
		this.__requestId = 0;
		this.__requestMaxBuffer = 40;
		this.__arrRequest = new Array();
		};
		
	static public function getInstance(Void):CieRequestManager{
		if(__instance == undefined) {
			__instance = new CieRequestManager();
			}
		return __instance;
		};	
		
	public function addRequest(arrData:Array, callBackFunction:Function, extraObj:Object, bNotifyUserOnHttpError:Boolean):Void{
		this.__requestId++;
		if(this.__requestId > this.__requestMaxBuffer){
			this.__requestId = 0;
			}
		this.__arrRequest[this.__requestId] = new Array();
		this.__arrRequest[this.__requestId]['__cReq'] = new CieRequest(this.__requestId, arrData);
		//call the function set by the class calling the request
		if(extraObj != undefined && extraObj != null){
			this.__arrRequest[this.__requestId]['__cReq'].watch('__finish', callBackFunction, {__req:this.__arrRequest[this.__requestId]['__cReq'], __super:extraObj});
		}else{
			this.__arrRequest[this.__requestId]['__cReq'].watch('__finish', callBackFunction, {__req:this.__arrRequest[this.__requestId]['__cReq']});
			}
		
		//if user need notification on http error
		if(bNotifyUserOnHttpError){
			Debug('NOTIF_ON_HTTP_ERROR: ON');
			this.__arrRequest[this.__requestId]['__cReq'].addNotificationOnHttpError();
			}
		
		//send the request
		this.__arrRequest[this.__requestId]['__cReq'].sendRequest();
		};
		
		
	public function reset(Void):Void{
		this.__requestId = 0;
		//cancelled any previous request
		for(var o in this.__arrRequest){
			this.removeRequest(o);
			}
		this.__arrRequest = new Array();
		};
	
	public function removeRequest(id:Number):Void{
		this.__arrRequest[id]['__cReq'].cancelRequest();
		delete this.__arrRequest[id];
		};

	/*	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieRequestManager{
		return this;
		};
	*/
	}	
