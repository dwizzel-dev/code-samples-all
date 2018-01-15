/*

class des sockets pour connection au java

*/

import manager.CieSocketManager;

dynamic class comm.CieSocket extends XMLSocket{

	static private var __className:String = 'CieSocket';
	static private var TIMERINTERVAL:Number = 30000;
	private var __port:Number;
	private var __ip:String;
	private var __session:String;
	private var __isConnected:Boolean;
	private var __reconnInterval:Number;
	private var __cSocketManager:CieSocketManager;
			
	public function CieSocket(cSocketManager:CieSocketManager){
		super();
		this.__cSocketManager = cSocketManager;
		this.__isConnected = false;
		this.__ip = '';
		this.__port = 0;
		this.__session = '';
		};
		
	public function isConnected(Void):Boolean{
		return this.__isConnected;
		};
		
	public function reset(Void):Void{
		clearInterval(this.__reconnInterval);
		};
		
	public function onConnect(ok:Boolean):Void{
		if(ok){
			Debug('socket connected to ' + this.__ip + ':' + this.__port);
			this.__isConnected = true;
			this.sendToServer('V2:' + this.__session);
		}else{
			Debug('***ERR unable to connect socket to ' + this.__ip + ':' + this.__port);
			this.__reconnInterval = setInterval(this, 'openSocket', TIMERINTERVAL);
			}
		};
		
	public function onData(strData:String):Void{
		//dispatch to the callback function
		if(strData != '' && strData != null){
			var arrData = strData.split(':');
			//on rebuild le message car a peut-etre des  : ()deux points dedans
			for(var i=2; i<arrData.length; i++){
				arrData[1] += ':' + arrData[i];
				}
			this.__cSocketManager.socketCommand(arrData[0], arrData[1]);
			}
		};
		
	public function onClose(Void):Void{
		Debug('***ERR socket lost connection to server at ' + this.__ip + ':' + this.__port);
		this.__cSocketManager.socketClose();
		this.__isConnected = false;
		};	
		
	public function changeSessionID(session:String):Void{
		this.__session = session;
		};	
		
	public function sendToServer(str:String):Void{
		//Debug("OUT: " + str);
		if(this.__isConnected){
			this.send(str + "\n");
			}
		};
		
	public function closeSocket(Void):Void{
		if(this.__isConnected){
			this.close();
			this.__isConnected = false;
			this.reset();
			Debug('socket disconnected by client from ' + this.__ip + ':' + this.__port);
			}
		};		
		
	public function reconnectSocket(Void):Void{
		clearInterval(this.__reconnInterval);
		this.__reconnInterval = setInterval(this, 'openSocket', TIMERINTERVAL);
		};
		
	public function setConnection(ip:String, port:Number, session:String):Void{
		this.__ip = ip;
		this.__port = port;
		this.__session = session;
		this.openSocket();
		};	
		
	public function openSocket(Void):Void{
		clearInterval(this.__reconnInterval);
		if(!this.__isConnected){
			Debug('opening socket on ' + this.__ip + ':' + this.__port);
			this.connect(this.__ip, this.__port);
			}
		};		
	/*
	public function getClassName(Void):String{
		return __className;
		};
	*/
	/*
	public function getClass(Void):CieSocket{
		return this;
		};
	*/	
	}	
