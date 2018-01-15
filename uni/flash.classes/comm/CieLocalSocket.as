/*

class des sockets local/remote via localConnection de Flash

*/


dynamic class comm.CieLocalSocket{

	static private var __className:String = 'CieLocalSocket';
	
	private	var __randomNum:Number;
	private	var __fileName:String;
	private	var __localSocket:LocalConnection;
	private	var __remoteSocket:Array;
	private	var __localConnectionName:String;
	private var __formName:String;
	private var __renewMaxAttempt:Number;
	private var __renewAttempt:Array;
	
	public function CieLocalSocket(fileName:String, formName:String){
		this.__renewMaxAttempt = 60;
		this.__renewAttempt = new Array();
		this.__fileName = fileName;
		this.__formName = formName;
		this.__remoteSocket = new Array();
		this.__localSocket = new LocalConnection();
		this.__randomNum = 0;
		this.__localConnectionName = '';
		this.initLocalSocket();
		};
		
	public function addRemoteSocket(sockName:String):Void{
		//Debug('REMOTE_SOCKNAME: ' + sockName);
		if(this.__renewAttempt[sockName] == undefined){
			this.__renewAttempt[sockName] = 0;
			}
		this.__remoteSocket[sockName] = new LocalConnection();
		this.__remoteSocket[sockName].__super = this;
		this.__remoteSocket[sockName].__name = sockName;
		this.__remoteSocket[sockName].onStatus = function(obj:Object):Void{
			if(obj.level != 'status'){
				Debug('*** ERROR REMOTE_SOCKET[' + this.__name + ']: ' + obj.level);    
				if(obj.level == 'error'){
					this.__super.renewRemoteSocket(this.__name);
					}
				}
			};
		};
		
	public function renewRemoteSocket(sockName:String):Void{
		this.__remoteSocket[sockName].close();
		delete this.__remoteSocket[sockName];
		if(this.__renewAttempt[sockName]++ < this.__renewMaxAttempt){
			this.addRemoteSocket(sockName);
		}else{
			Debug('*** RENEW_REMOTE_SOCKET[' + sockName + '] MAX ATTEMPT REACH QUITTING RENEWAL');
			}
		};	
		
	public function callRemoteSocket(sockName:String, formArgs:Array):Void{
		if(this.__remoteSocket[sockName] != undefined){
			this.__remoteSocket[sockName].send(sockName + '_' + this.__randomNum, 'winCommand', [this.__formName, formArgs]);
			}
		};	
		
	private function setLocalSocketListener(Void):Void{
		//check if a connection is still opened
		this.__localConnectionName = this.__formName + '_' + this.__randomNum;
		Debug("LOCAL_SOCKET_NAME: " + this.__localConnectionName);
		if(!this.__localSocket.connect(this.__localConnectionName)){ 
			Debug("*** ERROR LOCAL_SOCKET NOT CONNECTED ON " + this.__localConnectionName);
		}else{
			this.__localSocket.winCommand = function(param:Array):Void{
				//dispatch to the global core.CieFunctions 
				cFunc.localSocketCommand(param);				
				};		
			}
		};
		
	private function initLocalSocket(Void):Void{
		//get the local connection file rand num genrated by WinLoader.exe
		var lvRandNum:LoadVars = new LoadVars();
		lvRandNum.__super = this;
		lvRandNum.onLoad = function(ok:Boolean){
			if(ok){
				this.__super.__randomNum = this.lc;
			}else{
				Debug('***ERROR LOADING CONNECTION FILE');
				}
			this.__super.setLocalSocketListener();	
			};
		lvRandNum.load(this.__fileName);
		};	
		
	public function clearLocalSocketListener(Void):Void{
		this.__localSocket.close();
		this.__localSocket = null;
		};	
		
	public function clearRemoteSocket(Void):Void{
		for(var o in this.__remoteSocket){
			this.callRemoteSocket(o,['WM_EXIT']);
			this.__remoteSocket[o].close();
			delete this.__remoteSocket[o];
			delete this.__renewAttempt[o];
			}
		};

	public function getLocalSocketFullName(Void):String{
		return this.__localConnectionName;
		};
	/*	
	public function getClassName(Void):String{
		return __className;
		};
	*/
	/*
	public function getClass(Void):CieLocalSocket{
		return this;
		};
	*/	
	}	
