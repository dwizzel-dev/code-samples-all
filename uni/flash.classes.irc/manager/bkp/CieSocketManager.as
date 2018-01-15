/*

The socket manager 

*/
import comm.CieSocket;
import messages.CieTextMessages;

dynamic class manager.CieSocketManager{

	static private var __className = 'CieSocketManager';
	static private var __instance:CieSocketManager;
	
	private var __socket:CieSocket;
	private var __registeredObject:Array;
	
		
	private function CieSocketManager(Void){
		this.__registeredObject = new Array();	
		this.__socket = new CieSocket(this);
		};
		
	static public function getInstance(Void):CieSocketManager{
		if(__instance == undefined) {
			__instance = new CieSocketManager();
			}
		return __instance;
		};	
	
	/**************************************************************************************************************/
	
	public function isConnected(Void):Boolean{
		return this.__socket.isConnected();
		};
		
	public function isStackListingTreated(Void):Boolean{
		return this.__bStackListingTreated;
		};

	/**************************************************************************************************************/		
	
	public function reset(Void):Void{
		delete this.__registeredObject ;
		this.__registeredObject = new Array();
		};
	
	public function closeConnection(Void):Void{
		//close the conn
		this.__socket.closeSocket();
		this.reset();
		};	
	
	public function setConnection(Void):Void{
		setLoadingBar(90);
		this.__bStackListingTreated = false;
		this.__socket.setConnection(BC.__server.__ip, BC.__server.__port, BC.__user.__sessionID);
		};
		
	
	/**************************************************************************************************************/
	//enregistre un opbject opur recevoir une modification de statu 
	public function registerObjectForOnlineNotification(obj:Object):Void{
		if(obj != undefined){
			this.__registeredObject.push(obj);
			}
		};	
		
	/**************************************************************************************************************/
	//supprime un opbject de recevoir une modification de statu 
	public function unregisterObjectForOnlineNotification(obj:Object):Void{
		//Debug("unregisterObjectForOnlineNotification: " + obj);
		for(var o in this.__registeredObject){
			if(this.__registeredObject[o] == obj){
				delete this.__registeredObject[o];
				break;
				}
			}
		};	
		
	/**************************************************************************************************************/	
	//notifi que le status a change
	public function notifyRegisterObject(arrInfo:Array, ctype:String):Void{
		/*array params
		0 = nopub
		1 = value state or blocked
		*/
		if(ctype == 'state'){
			for(o in this.__registeredObject){
				if(!this.__registeredObject[o].updateObject(arrInfo[0], arrInfo[1])){
					//if it dont returned true then the method attach to the object doesnt exist anymore
					delete this.__registeredObject[o];
					}
				}
		}else if(ctype == 'blocked'){
			for(o in this.__registeredObject){
				if(!this.__registeredObject[o].updateBlocked(arrInfo[0], arrInfo[1])){
					//if it dont returned true then the method attach to the object doesnt exist anymore
					//delete this.__registeredObject[o];
					}
				}
		}else if(ctype == 'moderator'){
			for(o in this.__registeredObject){
				if(!this.__registeredObject[o].updateModerator(arrInfo[0], arrInfo[1])){
					//if it dont returned true then the method attach to the object doesnt exist anymore
					//delete this.__registeredObject[o];
					}
				}
			}
		};
		
	/**************************************************************************************************************/

	public function socketSend(str:String):Void{
		Debug("socketSend(): " + str);
		this.__socket.sendToServer(str);
		};
	
	public function socketClose(Void):Void{
		//tell to relogin without autoreconnect
		cFunc.openLogout(false);
		};
	
	public function socketCommand(strCommand:String, strData:String):Void{
		
		Debug(strCommand + ":" + strData);
			
		if(strCommand == 'USERINFOS'){ //USERINFOS:1737771,uniadmin,1,1,11111
			//info sur l'usager connecte
			var arrData = strData.split(',');
			BC.__user.__nopub = arrData[0];
			BC.__user.__pseudo = arrData[1];
			BC.__user.__state = Number(arrData[2]);
			BC.__user.__admin = Number(arrData[3]);
			//les droits admin
			BC.__user.__rights.__kill = Number(arrData[4].substr(0,1));
			BC.__user.__rights.__delete = Number(arrData[4].substr(1,1));
			BC.__user.__rights.__create = Number(arrData[4].substr(2,1));
			BC.__user.__rights.__promote = Number(arrData[4].substr(3,1));
			BC.__user.__rights.__watch = Number(arrData[4].substr(4,1));
			//trace
			if(BC.__user.__debug){
				Debug('[USER] NOPUB: ' + BC.__user.__nopub);
				Debug('[USER] PSEUDO: ' + BC.__user.__pseudo);
				Debug('[USER] STATE: ' + BC.__user.__state);
				Debug('[USER] ADMIN: ' + BC.__user.__admin);
				for(var o in BC.__user.__rights){
					Debug('[RIGHTS] ' + o + ': ' + BC.__user.__rights[o]);
					}
				}
		}else if(strCommand == 'ROOMNAME'){ //ROOMNAME:fakeroom,0,t3.jpg,this+is+a+super+room+man%21%21%21,1737771
			cFunc.addRoomToDB(strData.split(','));
			
		}else if(strCommand == 'ROOMEND'){ //ROOMEND:true
			cFunc.openIrc();
				
		}else if(strCommand == 'ROOMCOUNT'){ //ROOMCOUNT:testroom,1
			cFunc.updateCountRoom(strData.split(','));
			
		}else if(strCommand == 'ROOMLIST'){ //ROOMLIST:testroom,1737771,uniadmin,1	
			cFunc.addUserToRoomDB(strData.split(","));
			
		}else if(strCommand == 'ROOMADDMODERATOR'){ //ROOMADDMODERATOR:testroom,1737711|99778|etc...
			var arrTmp = strData.split(",");
			cFunc.addModeratorToRoomDB(arrTmp);	
			if(cAdmin != undefined){
				this.notifyRegisterObject(arrTmp, 'moderator');
				}
			
		}else if(strCommand == 'ROOMREMOVEMODERATOR'){ //ROOMREMOVEMODERATOR:testroom,1737711
			var arrTmp = strData.split(",");
			cFunc.removeModeratorFromRoomDB(arrTmp);		
			if(cAdmin != undefined){
				this.notifyRegisterObject(arrTmp, 'moderator');
				}
			
		}else if(strCommand == 'USERLEAVEROOM'){//USERLEAVEROOM:testroom,1737771
			cFunc.removeUserFromRoomDB(strData.split(","));
			
		}else if(strCommand == 'ROOMMSG'){ //ROOMMSG:testroom,99778,this+is+a+test
			cFunc.updateMsg(true, strData.split(","));
		
		}else if(strCommand == 'USERSTATUS'){ //USERSTATUS:99778,2
			var arrTmp = strData.split(",");
			//update DB
			cFunc.updateUserInfoFromDB(arrTmp);
			//notify
			this.notifyRegisterObject(arrTmp, 'state');
				
		}else if(strCommand == 'USERMSG'){	//USERMSG:ROOM,99778,this+is+a+test+private
			var arrTmp = strData.split(",");
			var arrUsers = cFunc.getUserDB(); //get users DB
			if(arrUsers[arrTmp[1]]['blocked'] == 0){ //check if it wasnt blocked
				//check if its opened
				if(!cFunc.checkIfRoomTabExist(arrTmp[1])){
					cFunc.createPrivateRoom(arrTmp[1], arrUsers[arrTmp[1]], true, arrTmp[0]);
					}
				cFunc.updateMsg(false, [arrTmp[1], arrTmp[1], arrTmp[2]]);	
				}
		
		}else if(strCommand == 'USERMSGPRIVATE'){	//USERMSGPRIVATE:763044,1526743,sdfsdfsdf
			cFunc.updatePrivateMsg(strData.split(","));
					
		}else if(strCommand == 'USERLIST'){	//USERLIST:testroom,1737771,uniadmin,1	
			cFunc.addUserToDB(strData.split(","));
			
		}else if(strCommand == 'BANNEDLIST'){	//BANNEDLIST:1737771,99778,etc...
			//Debug("BANNEDLIST: " + strData);
			cFunc.addUsersToBannedDB(strData.split(","));
			
		}else if(strCommand == 'ROOMCLOSED'){	//ROOMCLOSED:testroom
			cFunc.closeRoomFromServerCommand(strData);
			
		}else if(strCommand == 'YOUREBLOCKEDFROMROOM'){	//YOUREBLOCKEDFROMROOM:testroom    un usager est bloque d'une room
			cFunc.closeRoomFromServerCommand(strData);	
			
		}else if(strCommand == 'YOUAREDEAD'){	//YOUAREDEAD:OK
			cFunc.showAdminMessage('killed', strData);
		
		}else if(strCommand == 'YOUAREBANNED'){	//YOUAREBANNED:OK
			cFunc.showAdminMessage('banned', strData);
		
		}else if(strCommand == 'USERPROMOTION'){	//USERPROMOTION:testroom,1 ou USERPROMOTION:testroom,0 quand on l'enleve
			cFunc.promoteUserToModeratorForRoom(strData.split(","));
			
		}else if(strCommand == 'ADMINMESSAGE'){	//ADMINMESSAGE:message recu
			cFunc.messageFromYourAdmin(strData);	
		
		}else if(strCommand == 'USERBANNED' || strCommand == 'USERUNBANNED' || strCommand == 'ROOMCREATED' || strCommand == 'USERKILLED' || strCommand == 'USERPROMOTED' || strCommand == 'USERFROMROOMBLOCKED' || strCommand == 'ADMINMESSAGING' || strCommand == 'ROOMDELETED'){
			if(cAdmin != undefined){
				cAdmin.updateTrace(strCommand + ":" + strData);
				}
			}		
		};
	
	/**************************************************************************************************************/	
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieSocketManager{
		return this;
		};	
	
	}	