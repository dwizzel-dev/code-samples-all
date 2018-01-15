/*

class des sockets local/remote via localConnection de Flash

*/


dynamic class comm.CieLocalSocket{

	static private var __className:String = 'CieLocalSocket';
	
	private	var __localSocket:LocalConnection;
	private	var __remoteSocket:Array;
	private	var __localConnectionName:String;
	private var __formName:String;
	
	public function CieLocalSocket(formName:String){
		this.__formName = formName;
		this.__remoteSocket = new Array();
		this.__localSocket = new LocalConnection();
		this.__localConnectionName = '';
		this.setLocalSocketListener();
		};
		
	public function addRemoteSocket(sockName:String):Void{
		Debug('REMOTE_SOCKNAME: ' + sockName);
		this.__remoteSocket[sockName] = new LocalConnection();
		this.__remoteSocket[sockName].__name = sockName;
		this.__remoteSocket[sockName].onStatus = function(obj:Object):Void{
			if(obj.level != 'status'){
				Debug('SOCK[' + this.__name + ']: ' + obj.level);    
				}
			};
		};
		
	public function callRemoteSocket(sockName:String, formArgs:Array):Void{
		this.__remoteSocket[sockName].send(sockName, 'winCommand', [this.__formName, formArgs]);
		};	
		
	private function setLocalSocketListener(Void):Void{
		//check if a connection is still opened
		this.__localConnectionName = this.__formName;
		Debug("LOCAL_SOCKNAME: " + this.__localConnectionName);
		if(!this.__localSocket.connect(this.__localConnectionName)){ 
			Debug("***ERROR LOCAL_SOCKET NOT CONNECTED ON " + this.__localConnectionName);
		}else{
			this.__localSocket.winCommand = function(param:Array):Void{
				//dispatch to the global core.CieFunctions 
				cFunc.localSocketCommand(param);				
				};		
			}
		};
		
	public function clearLocalSocketListener(Void):Void{
		this.__localSocket.close();
		this.__localSocket = null;
		};	
		
	public function clearRemoteSocket(Void):Void{
		for(var o in this.__remoteSocket){
			this.callRemoteSocket(o,['WM_EXIT']);
			this.__remoteSocket[o].close();
			this.__remoteSocket[o] = null;
			}
		};

	public function getLocalSocketFullName(Void):String{
		return this.__localConnectionName;
		};
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieLocalSocket{
		return this;
		};
	}	
