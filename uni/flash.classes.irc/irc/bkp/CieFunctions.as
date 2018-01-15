/*

functins for the core of the application
caller of the managers functions
can be accessed by anything

this.__arrRooms['testroom']
this.__arrRoomsDB['#testroom']
this.__arrUsersDB[99778]

*/

import control.CiePanel;
import manager.CieTabManager;

import messages.CieTextMessages;
import messages.CiePromptMessages;
import messages.CieActionMessages;

//les chats
import irc.CieRoomIrc;
import irc.CieUserIrc;
import irc.CieSpecialIrc;

//les tabs
import irc.CieOptions;
import irc.CieAdmin;
import irc.CieLogin;

//threads
import utils.CieThread;

//date
import system.CieDate;

dynamic class irc.CieFunctions{

	static private var __className = "CieFunctions";
	static private var __instance:CieFunctions;
	
	private var __arrRooms:Array;
	private var __tabManager:CieTabManager;
	
	private var __arrRoomsDB:Array;
	private var __arrUsersDB:Array;
	private var __arrPrivateDB:Array;
	private var __arrBannedDB:Array;	
	
	private var __cMessageBox:Object;
	private var __cActionMessages:Object;
	private var __roomPrefix:String;
	
	//for coloring
	private var __iColorCounter:Number;
	
	private var __cThreadGCUserDB:CieThread;
	private var __cThreadLastUserAction:CieThread;
	
	//for idle client c'est a dire quand un usager n'envoi rien en ligne depuis X minutes
	public var __lastUserActionDate:Number;
	public var __bClientInIdleState:Boolean = false;
	
	//les rooms pour lequel 'usasager actuel est moderateur
	private var __arrModeratorOfRoom:Array;
		
				
	/*************************************************************************************************************************************************/			
			
	private function CieFunctions(Void){
		this.__iColorCounter = 0;
		this.__arrRooms = new Array();
		this.__arrUsersDB = new Array();
		this.__arrRoomsDB = new Array();
		this.__arrPrivateDB = new Array();
		this.__arrBannedDB = new Array();
		this.__arrModeratorOfRoom = new Array();
		this.__roomPrefix = "#";
		};
		
	/*************************************************************************************************************************************************/	
		
	static public function getInstance(Void):CieFunctions{
		if(__instance == undefined) {
			__instance = new CieFunctions();
			}
		return __instance;
		};
	
	/*************************************************************************************************************************************************/
	//when a user is unpromote or protmote to moderator for a room
	public function promoteUserToModeratorForRoom(arrInfos:Array):Void{
		/*arrparams
		0 = nom de la room
		1 = flag 0=unpromote, 1=promote
		*/
		//put the roomPrefix because the server dont send one
		//Debug("promoteUserToModeratorForRoom(): " + arrInfos);
		if(arrInfos[1] == '1'){ //promote
			this.__arrModeratorOfRoom[this.__roomPrefix + arrInfos[0]] = true;
		}else{// UnPromote
			delete this.__arrModeratorOfRoom[this.__roomPrefix + arrInfos[0]];
			}
		//on change le welcome page pour lui dire ce qu'il est maintenant
		if(cOptions != undefined){
			cOptions.changeWelcomeText();
			}
		};
		
	/*************************************************************************************************************************************************/
	//check si est moderateur d'une room en particulier
	public function checkIfModeratorOfThisRoom(roomName:String):Boolean{	
		//check si on a un prefix sinon le rajuter
		if(roomName.substr(0,1) != '#'){
			roomName = '#' + roomName;
			}
		//si est moderateur
		if(this.__arrModeratorOfRoom[roomName]){
			return true;
			}
		return false;
		};
			
	/*************************************************************************************************************************************************/
	//load from CieIrcthis is the first step once connect to the server and received "ROOMEND:true"
	public function openIrc(Void):Void{
		//load this one if the first tab is not created from the xml file
		cContent.openTab(['irc']);
		//the xml 
		var strXml = '<P n="_tl" content="mvBgPanel" bgcolor="' + CieStyle.__basic.__ircBgColor + '" scroll="false">';
		strXml += '<PT n="~options" model="un" ystart="' + CieStyle.__tabPanel.__tabOffSetTopForFirst + '" title="Options" closebutt="false" action="refreshList">';
		strXml += '<P n="_tl" content="mvContent" bgcolor="' + CieStyle.__tabPanel.__bgColorSpecial + '" scroll="false" effect="false"></P>';
		strXml += '</PT>';
		strXml += '</P>';
		//change the node
		cFunc.changeNodeValue(new XML(strXml),['irc','_tl']);
		//load at lest the firt tab
		cContent.openTab(['irc','~options']);
		//start the GC of usersDB when is offline
		this.__cThreadGCUserDB = cThreadManager.newThread(10000, this, 'GCUserDb', {__supclass:this});
		//start the thread for idle client
		this.updateLastUserAction();
		this.__cThreadLastUserAction = cThreadManager.newThread(10000, this, 'checkLastUserAction', {__supclass:this});
		//on se rajoute a la bd
		this.addUserToDB([BC.__user.__nopub, BC.__user.__pseudo, BC.__user.__state, BC.__user.__admin]);
		//the panel object
		if(this.__tabManager == undefined){
			this.__tabManager = this.getPanelTabManager(['irc','_tl']);
			}
		
		//build the options	
		_global.cOptions = CieOptions.getInstance();	
		//show the interface
		setLoading(false);
		//if admin then open the admin panel
		if(BC.__user.__admin){
			openAdmin();
			}
		};
		
	/*************************************************************************************************************************************************/
	//load from CieAdmin
	public function openAdmin(Void):Void{	
		var tabName = '~admin';
		//si est ouverte on ouvre le tab seulement sinon on fait la requete au seveur java comme quoi nous entrons dans la room
		if(_global.cAdmin == undefined){
			//ref to the class
			_global.cAdmin = new CieAdmin(tabName, this.__tabManager);
		}else{
			//set the tab focus
			cContent.openTab(['irc',tabName]);
			}
		}
	
	/*************************************************************************************************************************************************/
	//load the login page
	public function openLogin(Void):Void{	
		//on check le session ID si il y en a un alors on passe directement a la connection
		if(__gArgs.__ss.length > 10){ //le min de charactere
			//fill in missing infos
			BC.__user.__sessionID = __gArgs.__ss;
			//reset des global passed by ref
			__gArgs.__ss = '';
			//connect directly
			setLoadingText(gLang[3]);
			setLoadingBar(70);
			cSockManager.setConnection();
		}else{
			//instance
			_global.cLogin = CieLogin.getInstance();
			//open the login window, will set the loader a false et affiche le formulaire
			cLogin.openLogin();
			}
		};
		
	/*************************************************************************************************************************************************/
	//logout generally end the connection and clean all
	public function openLogout(bReconnect:Boolean):Void{	
		cIrc.reset(bReconnect);
		};	
	
	/*****STATUS CHECK THREAD*********************************************************************************************************************/
	//THREAD verifie la derniere action de l'usager si aucune pendant 2 minutes met son staut a onPause
	public function checkLastUserAction(obj:Object):Boolean{
		//Debug("CieFunctions.checkLastUserAction()");
		var newTimestamp = new Date().getTime();
		var currTimestamp = obj.__supclass.__lastUserActionDate;
		//check la diff entre les 2 date
		if((newTimestamp - currTimestamp) > BC.__system.__idle){
			//check si n'est pas deja idle
			if(!obj.__supclass.__bClientInIdleState){
				//on envoi au serveur la modif de statut
				obj.__supclass.__bClientInIdleState = true;
				//Debug('__IDLE');
				cSockManager.socketSend("USERSTATUS:2");
				}
			}
		return true;
		};
	
	//update la date de la derniere action de l'usager
	public function updateLastUserAction(Void):Void{
		this.__lastUserActionDate = new Date().getTime();
		//check si etaity idle sinon on envoi au serveur que l'on est revenu alors on change de staus a online
		if(this.__bClientInIdleState){
			this.__bClientInIdleState = false;
			cSockManager.socketSend("USERSTATUS:1");
			//Debug('__UN__IDLE');
			}
		};	
		
			
	/*************************************************************************************************************************************************/
	//Gatbage Collector des usager offline THREAD
	public function GCUserDb(obj:Object):Boolean{
		//Debug("CRONJOB-->GCUserDb()");
		var bRefresh = false;
		var arrListRemoved = new Array();
		var arrTmpUserInfos:Array;
		for(var o in obj.__supclass.__arrUsersDB){ //o = is the nopub
			//check if it is offline
			if(obj.__supclass.__arrUsersDB[o]['state'] == '0'){
				//keep a tmp before deleting it
				arrTmpUserInfos = obj.__supclass.__arrUsersDB[o];
				//check if there is a private discussion going on or if blocked keep it in DB
				if(obj.__supclass.__arrRooms[o] == undefined && obj.__supclass.__arrUsersDB[o]['blocked'] == 0){
					//remove the userDB
					bRefresh = true;
					arrListRemoved.push(obj.__supclass.__arrUsersDB[o]['pseudo']);
					delete obj.__supclass.__arrUsersDB[o];
					}
				//we can remove it from the rooms db
				for(var k in obj.__supclass.__arrRoomsDB){
					obj.__supclass.removeUserFromRoomDB([k, o]);
					}
				}
			}
		//refresh list in admin panelTab	
		if(bRefresh){
			if(cAdmin != undefined){
				//cAdmin.drawUserList();
				}
			}
			
		//ok now cxheck for opened special provate
		bRefresh = false;
		for(var k in obj.__supclass.__arrPrivateDB){
			//si est dans la BD des special private
			for(var o in arrListRemoved){
				//Debug('IND: ' + k.indexOf(arrListRemoved[o]));
				if(k.indexOf(arrListRemoved[o]) > 0){
					//check si n'a pas deja un tab d'ouvert
					if(obj.__supclass.__arrRooms[k] == undefined){
						//alors on peu l'enlever de la bd
						bRefresh = true;
						delete obj.__supclass.__arrPrivateDB[k];
						}
					}
				}	
			}	
		//refresh du private list in option panelTab	
		if(bRefresh){
			if(cAdmin != undefined){
				cAdmin.drawPrivateList();
				}
			}	

		return true;
		};
		
		
	/*************************************************************************************************************************************************/	
	//reset all vats and others	
	public function reset(Void):Void{
		//stop he thread
		this.__cThreadGCUserDB.destroy();
		this.__cThreadGCUserDB = null;
		//stop he thread
		this.__cThreadLastUserAction.destroy();
		this.__cThreadLastUserAction = null;
		
		//popup if there was some
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;	
		this.__cMessageBox.closeWindow();
		this.__cMessageBox = null;
		//reset the options panel
		cOptions.reset();
		delete cOptions; 
		//reset the admin pannel
		if(cAdmin != undefined){
			cAdmin.reset();
			}
		delete cAdmin; 
		//array of opened tabs private and public
		delete this.__arrRooms;
		this.__arrRooms = new Array();
		//
		delete this.__arrUsersDB;
		this.__arrUsersDB = new Array();
		//
		delete this.__arrRoomsDB;
		this.__arrRoomsDB = new Array();
		//
		delete this.__arrPrivateDB;
		this.__arrPrivateDB = new Array();
		//
		delete this.__arrBannedDB;
		this.__arrBannedDB = new Array();
		//
		delete this.__arrModeratorOfRoom;
		this.__arrModeratorOfRoom = new Array();
		//color count
		this.__iColorCounter = 0;
		//the tabManager
		delete this.__tabManager;
		};
	
	/*************************************************************************************************************************************************/	
		
	public function onEnterPress(Void):Void{
		var tabName = this.__tabManager.getTabFocus();
		if(this.__arrRooms[tabName] != undefined){
			//Debug('TAB: ' + tabName + " EXIST");
			this.__arrRooms[tabName].sendMessage();
		}else{
			//Debug('TAB: ' + tabName + " NOT EXIST");
			}
		};
	
	/** KICK ********************************************************************************************************************************************/	
	//for kicking a member from a specific room
	public function kickUser(arrInfos:Array, roomNameWithoutPrefix:String):Void{	
		//on check si a le droit
		if(cFunc.checkIfModeratorOfThisRoom(roomNameWithoutPrefix) || (BC.__user.__admin)){
			this.__cActionMessages = new CieActionMessages(gLang[113], gLang[114] + '<b>' + arrInfos['pseudo'] + '</b>' + gLang[115] + '<b>' + roomNameWithoutPrefix + '</b>');
			this.__cActionMessages.setCallBackFunction(this.cbPromptKickUser, {__class: this, __arrInfos:arrInfos, __roomName:roomNameWithoutPrefix});
		}else{
			new CieTextMessages('MB_OK', gLang[116] + '<b>' + arrInfos['pseudo'] + '</b>' + gLang[117] + '<b>' + roomNameWithoutPrefix + '</b>', gLang[109]);
			}
		};
		
	//callback when user press button
	public function cbPromptKickUser(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptKickUser(bResult, cbObject.__arrInfos, cbObject.__roomName);
		};
		
	//rajout au kicked
	public function treatUserActionOnPromptKickUser(bResult:Boolean, arrInfos:Array, strRoomName:String):Void{
		//depending on the user selection
		if(bResult){
			//on envoi au serveur
			cSockManager.socketSend("BLOCKUSERFROMROOM:" + strRoomName + ',' + arrInfos['nopub'] + ',1');
			//Debug("SEND_BLOCK_FROM_ROOM: " +  strRoomName + '->' + arrInfos['nopub']);
			}
		//user have pressed cancel thrn close the window
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;	
		};	
		
	/** PROMOTE ********************************************************************************************************************************************/	
	//for promotion a member 
	public function promoteUser(arrNoPub:Array, arrPseudo:Array, strRoomName:String, bFromModerator:Boolean):Void{	
		//check si promote or unpromote et si ca vienyt d'un moderator qui a seulement le droit de promouvoir contrairement a l'admin
		if(this.__arrRoomsDB['#' + strRoomName]['moderator'][arrNoPub[0]] != undefined){
			//si c'est un admin il peut unpromote conrtrtairement au moderator qui peut seuelemtn promote
			if(!bFromModerator){
				this.__cActionMessages = new CieActionMessages(gLang[118],  gLang[119] + '<b>' + arrPseudo + '</b>' + gLang[120] + '<b>' + strRoomName + '</b>"?');
				this.__cActionMessages.setCallBackFunction(this.cbPromptPromoteUser, {__class: this, __arrNoPub:arrNoPub, __roomName:strRoomName, __bPromote:false});
			}else{
				new CieTextMessages('MB_OK', gLang[182] + '<b>' + strRoomName + '</b>', gLang[109]);
				}
		}else{
			this.__cActionMessages = new CieActionMessages(gLang[121], gLang[122] + '<b>' + arrPseudo + '</b>' + gLang[123] + '<b>' + strRoomName + '</b>?');
			this.__cActionMessages.setCallBackFunction(this.cbPromptPromoteUser, {__class: this, __arrNoPub:arrNoPub, __roomName:strRoomName, __bPromote:true});
			}
		};
		
	//callback when user press button
	public function cbPromptPromoteUser(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptPromoteUser(bResult, cbObject.__arrNoPub, cbObject.__roomName, cbObject.__bPromote);
		};		
		
	//rajout au promote
	public function treatUserActionOnPromptPromoteUser(bResult:Boolean, arrNoPub:Array, strRoomName:String, bPromote:Boolean):Void{
	//depending on the user selection
		if(bResult){
			//on envoi au serveur
			for(var o in arrNoPub){
				if(bPromote){
					cSockManager.socketSend("PROMOTEUSER:" + strRoomName + ',' + arrNoPub[o] + ',1');
					//Debug("PROMOTEUSER:" + strRoomName + ',' + arrNoPub[o] + ',1');
				}else{
					cSockManager.socketSend("PROMOTEUSER:" + strRoomName + ',' + arrNoPub[o] + ',0');
					//Debug("PROMOTEUSER:" + strRoomName + ',' + arrNoPub[o] + ',0');
					}
				}
			if(cAdmin != undefined){
				//cAdmin.drawUserList();
				}
			}
		//user have pressed cancel thrn close the window
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;	
		};
	
	/** KILL ********************************************************************************************************************************************/	
	//for killing a member 
	public function killUser(arrNoPub:Array, arrPseudo:Array):Void{	
		this.__cActionMessages = new CieActionMessages(gLang[125], gLang[126] + '<b>' + arrPseudo + '</b>');
		this.__cActionMessages.setCallBackFunction(this.cbPromptKillUser, {__class: this, __arrNoPub:arrNoPub});
		};
		
	//callback when user press button
	public function cbPromptKillUser(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptKillUser(bResult, cbObject.__arrNoPub);
		};		
		
	//rajout au killed
	public function treatUserActionOnPromptKillUser(bResult:Boolean, arrNoPub:Array):Void{
	//depending on the user selection
		if(bResult){
			//on envoi au serveur
			for(var o in arrNoPub){
				cSockManager.socketSend("KILLUSER:" + arrNoPub[o]);
				//Debug("SEND_KILL: " + arrNoPub[o]);
				}
			if(cAdmin != undefined){
				//cAdmin.drawUserList();
				}
			}
		//user have pressed cancel thrn close the window
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;	
		};	

	/** BANNED ********************************************************************************************************************************************/	
	//for baning a member 
	public function bannUser(arrNoPub:Array, arrPseudo:Array):Void{	
		this.__cActionMessages = new CieActionMessages(gLang[127], gLang[128] + '<b>' + arrPseudo + '</b>');
		this.__cActionMessages.setCallBackFunction(this.cbPromptBannUser, {__class: this, __arrNoPub:arrNoPub});
		};
		
	//callback when user press button
	public function cbPromptBannUser(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptBannUser(bResult, cbObject.__arrNoPub);
		};		
		
	//rajout au banned
	public function treatUserActionOnPromptBannUser(bResult:Boolean, arrNoPub:Array):Void{
	//depending on the user selection
		if(bResult){
			//on envoi au serveur
			for(var o in arrNoPub){
				cSockManager.socketSend("BANNUSER:" + arrNoPub[o]);
				//Debug("SEND_BANN: " + arrNoPub[o]);
				}
			if(cAdmin != undefined){
				//cAdmin.drawUserList();
				}
			}
		//user have pressed cancel thrn close the window
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		};	
		
	/**UN  BANNED ********************************************************************************************************************************************/	
	//for baning a member 
	public function unBannUser(arrNoPub:Array):Void{	
		this.__cActionMessages = new CieActionMessages(gLang[129], gLang[130] + '<b>' + arrNoPub + '</b>');
		this.__cActionMessages.setCallBackFunction(this.cbPromptUnBannUser, {__class: this, __arrNoPub:arrNoPub});
		};
		
	//callback when user press button
	public function cbPromptUnBannUser(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptUnBannUser(bResult, cbObject.__arrNoPub);
		};		
		
	//rajout au banned
	public function treatUserActionOnPromptUnBannUser(bResult:Boolean, arrNoPub:Array):Void{
	//depending on the user selection
		if(bResult){
			//on envoi au serveur
			for(var o in arrNoPub){
				cSockManager.socketSend("UNBANNUSER:" + arrNoPub[o]);
				//Debug("SEND_BANN: " + arrNoPub[o]);
				}
			if(cAdmin != undefined){
				//cAdmin.drawUserList();
				}
			}
		//user have pressed cancel thrn close the window
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		};		
		
	
	/*****ADD TO BVLOCKED ********************************************************************************************************************************************/	
	//for blockin a member from receiving message from him	
	public function blockUser(arrUserInfos:Array):Void{	
		if(!this.__arrUsersDB[arrUserInfos['nopub']]['blocked']){
			this.__cActionMessages = new CieActionMessages(gLang[131], gLang[132] + '<b>' + arrUserInfos['pseudo'] + '</b>' + gLang[133]);
		}else{
			this.__cActionMessages = new CieActionMessages(gLang[134], gLang[135] + '<b>' + arrUserInfos['pseudo'] + '</b>' + gLang[136]);
			}
		this.__cActionMessages.setCallBackFunction(this.cbPromptBlockUser, {__class: this, __arrinfos:arrUserInfos});
		};
		
	//callback when user press button
	public function cbPromptBlockUser(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptBlockUser(bResult, cbObject.__arrinfos);
		};	
	
	//rajout au blocked
	public function treatUserActionOnPromptBlockUser(bResult:Boolean, arrUserInfos:Array):Void{
	//depending on the user selection
		if(bResult){
			//do a request to add to the carnet
			if(!this.__arrUsersDB[arrUserInfos['nopub']]['blocked']){
				//on envoi au serveur
				cSockManager.socketSend("BLOCKUSER:" + arrUserInfos['nopub'] + ",1");
				//set les vars
				//Debug("BLOCKING: " + arrUserInfos['pseudo']);
				this.__arrUsersDB[arrUserInfos['nopub']]['blocked'] = 1;
				//on notifie pour cvhanger licone
				cSockManager.notifyRegisterObject([arrUserInfos['nopub'],1], 'blocked');
				/*
				a la place on va fermer la fenetre quand on bloque, mais quand on debloque comme plus bas dans le code de cette function
				*/
				this.__arrRooms[arrUserInfos['nopub']].Destroy();
				
				//le texte des boutons
				//this.__arrRooms[arrUserInfos['nopub']].changeBlockedButtText('unblocked');
				//la petite pharese en haut de la case input
				//this.__arrRooms[arrUserInfos['nopub']].updateTyping(gLang[137] + '<b>' + arrUserInfos['pseudo'] + '</b>' + gLang[138]);
			}else{
				//on envoi au serveur
				cSockManager.socketSend("BLOCKUSER:" + arrUserInfos['nopub'] + ",0");
				//set les vars
				//Debug("UN_BLOCKING: " + arrUserInfos['pseudo']);
				this.__arrUsersDB[arrUserInfos['nopub']]['blocked'] = 0;
				//on notifie pour cvhanger licone
				cSockManager.notifyRegisterObject([arrUserInfos['nopub'],0], 'blocked');
				//le texte des boutons
				this.__arrRooms[arrUserInfos['nopub']].changeBlockedButtText(gLang[175]);
				//la petite pharese en haut de la case input
				this.__arrRooms[arrUserInfos['nopub']].updateTyping(gLang[139] + '<b>' + arrUserInfos['pseudo'] + '</b>' + gLang[140]);
				}
			}
		//user have pressed cancel thrn close the window
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;	
		};	
	

	/*************************************************************************************************************************************************/	
	public function addUsersToBannedDB(arrInfos:Array):Void{
		//clear the array
		delete this.__arrBannedDB;
		this.__arrBannedDB = new Array();
		//split nopubs
		for(var o in arrInfos){
			this.__arrBannedDB[arrInfos[o]] = arrInfos[o];
			Debug("ADD_BANNED: " + arrInfos[o]);
			}
		//refresh user list in admin tab
		if(cAdmin != undefined){
			cAdmin.drawBannedList();
			}	
		};
	
	/*************************************************************************************************************************************************/	
	
	public function addUserToDB(arrInfos:Array):Void{
		//0=nopub
		//1 =pseudo
		//2= status
		//3 = isAdmin
		var key = arrInfos[0];
		if(this.__arrUsersDB[key] == undefined){
			this.__arrUsersDB[key] = new Array();
			this.__arrUsersDB[key]['nopub'] = arrInfos[0];
			this.__arrUsersDB[key]['pseudo'] = arrInfos[1];
			this.__arrUsersDB[key]['state'] = arrInfos[2];
			this.__arrUsersDB[key]['blocked'] = 0;
			this.__arrUsersDB[key]['killed'] = 0;
			this.__arrUsersDB[key]['admin'] = Number(arrInfos[3]);
			//color text
			var arrColorChoice:Array = CieStyle.__basic.__colorChoices.split(",");
			if(this.__iColorCounter >= arrColorChoice.length){
				this.__iColorCounter = 0;
				}
			this.__arrUsersDB[key]['textcolor'] = arrColorChoice[this.__iColorCounter];
			this.__iColorCounter++;
		}else{
			//maybe only the state has change
			this.__arrUsersDB[key]['state'] = arrInfos[2];
			}
			
		if(cAdmin != undefined){
			//pour changer le staut d'une user d'une liste de l'admin
			cSockManager.notifyRegisterObject([this.__arrUsersDB[key]['nopub'], this.__arrUsersDB[key]['state']], 'state');
			}
			
		};
	
	/*************************************************************************************************************************************************/
	public function addUserToRoomDB(arrInfos:Array):Void{
		//0= roomName
		//1=nopub
		//2 =pseudo
		//3= state
		//4= isAdmin
		//insert user in arrOf Users if doest exits yet
		this.addUserToDB([arrInfos[1], arrInfos[2], arrInfos[3], arrInfos[4]]);
		//push the ref in the room DB
		var roomName = this.__roomPrefix + arrInfos[0];
		var key = arrInfos[1];
		//so now we are sure we have the room DB created
		if(this.__arrRoomsDB[roomName]['users'][key] == undefined){
			this.__arrRoomsDB[roomName]['users'][key] = key;
			//check if the room is opened if yes then redraw the list
			if(this.__arrRooms[roomName] != undefined){
				this.__arrRooms[roomName].drawUserList();
				this.__arrRooms[roomName].showUserInRoom(true, this.__arrUsersDB[key]);
				}
			}
		};	

	/*************************************************************************************************************************************************/
	public function addModeratorToRoomDB(arrInfos:Array):Void{
		//Debug("addModeratorToRoomDB(): " + arrInfos);
		//0= roomName
		//1= nopub|nopub|nopub|etc... of the moderator
		//push the ref in the room DB
		var roomName = this.__roomPrefix + arrInfos[0];
		var arrModerator = arrInfos[1].split('|');
		//so now we are sure we have the room DB created
		for(var o in arrModerator){
			if(this.__arrRoomsDB[roomName]['moderator'][arrModerator[o]] == undefined){
				this.__arrRoomsDB[roomName]['moderator'][arrModerator[o]] = arrModerator[o];
				Debug("ADD_MODERATOR[" + roomName + "]: " + arrModerator[o]);
				}
			}
		//check if the room is opened if yes then redraw the list
		if(this.__arrRooms[roomName] != undefined){
			this.__arrRooms[roomName].drawUserList();
			}	
		};
		
	/*************************************************************************************************************************************************/
	public function removeModeratorFromRoomDB(arrInfos:Array):Void{
		//Debug("removeModeratorFromRoomDB(): " + arrInfos);
		//0= roomName
		//1= nopub|nopub|nopub|etc... of the moderator
		//push the ref in the room DB
		var roomName = this.__roomPrefix + arrInfos[0];
		var arrModerator = arrInfos[1].split('|');
		//so now we are sure we have the room DB created
		for(var o in arrModerator){
			if(this.__arrRoomsDB[roomName]['moderator'][arrModerator[o]] != undefined){
				delete this.__arrRoomsDB[roomName]['moderator'][arrModerator[o]];
				Debug("REMOVE_MODERATOR[" + roomName + "]: " + arrModerator[o]);
				}
			}
		//check if the room is opened if yes then redraw the list
		if(this.__arrRooms[roomName] != undefined){
			this.__arrRooms[roomName].drawUserList();
			}	
		};	
					
	/*************************************************************************************************************************************************/
	public function addRoomToDB(arrRoomInfos:Array):Void{
		if(this.__arrRoomsDB[this.__roomPrefix + arrRoomInfos[0]] == undefined){
			/*arr param
			0 = name
			1 = count
			2 = image
			3 = slogan de la room
			4 = le owner de la room
			*/
			//DB of the listing of room -> listing of the user n the room
			Debug("ADD_ROOM: " + arrRoomInfos[0]);
			this.__arrRoomsDB[this.__roomPrefix + arrRoomInfos[0]] = new Array();
			this.__arrRoomsDB[this.__roomPrefix + arrRoomInfos[0]]['name'] = arrRoomInfos[0];
			this.__arrRoomsDB[this.__roomPrefix + arrRoomInfos[0]]['usercount'] = Number(arrRoomInfos[1]);
			this.__arrRoomsDB[this.__roomPrefix + arrRoomInfos[0]]['owner'] = arrRoomInfos[4];	
			if(arrRoomInfos[2] == '' || arrRoomInfos[2] == 0){
				this.__arrRoomsDB[this.__roomPrefix + arrRoomInfos[0]]['image'] = '';	
			}else{
				//Debug("IMG: " + arrRoomInfos[2]);
				this.__arrRoomsDB[this.__roomPrefix + arrRoomInfos[0]]['image'] = unescape(arrRoomInfos[2]);	
				}
			if(arrRoomInfos[3] == ''){
				this.__arrRoomsDB[this.__roomPrefix + arrRoomInfos[0]]['title'] = '';	
			}else{
				this.__arrRoomsDB[this.__roomPrefix + arrRoomInfos[0]]['title'] = unescape(arrRoomInfos[3]);	
				}
			//usres in the room
			this.__arrRoomsDB[this.__roomPrefix + arrRoomInfos[0]]['users'] = new Array();
			//moderator for the room
			this.__arrRoomsDB[this.__roomPrefix + arrRoomInfos[0]]['moderator'] = new Array();
			
			//refresh user list in options tab
			cOptions.drawRoomList();
			if(cAdmin != undefined){
				cAdmin.drawRoomList();
				}
			}
		};
		
	/*************************************************************************************************************************************************/	
	public function addPrivateToDB(nopub_1:String, nopub_2:String):String{
		var key:String = this.__arrUsersDB[nopub_1]['pseudo'] + '~' + this.__arrUsersDB[nopub_2]['pseudo'];
		if(this.__arrPrivateDB[key] == undefined){
			//inverse au cas ou le message viendrait dans le sens contraire
			key = this.__arrUsersDB[nopub_2]['pseudo'] + '~' + this.__arrUsersDB[nopub_1]['pseudo'];
			if(this.__arrPrivateDB[key] == undefined){
				//DB of the listing of PRIVATE CHAT -> listing of the user n the room
				//Debug("ADD_PRIVATE: " + key);
				this.__arrPrivateDB[key] = new Array();
				this.__arrPrivateDB[key]['name'] = key;
				this.__arrPrivateDB[key]['nopub_1'] = nopub_1;
				this.__arrPrivateDB[key]['nopub_2'] = nopub_2;
				this.__arrPrivateDB[key]['pseudo_1'] = this.__arrUsersDB[nopub_1]['pseudo'];
				this.__arrPrivateDB[key]['pseudo_2'] = this.__arrUsersDB[nopub_2]['pseudo'];
				//refresh PRIVATE list in options tab
				if(cAdmin != undefined){
					cAdmin.drawPrivateList();
					}
				}
			}
		//update du timstamp
		var dDate:Date = new Date();
		this.__arrPrivateDB[key]['lastmsgtimestamp'] = dDate.getTime();
		//Debug("TIME: " + this.__arrPrivateDB[key]['lastmsgtimestamp']);
		//dans tout les cas on retourne la cle
		return key;	
		};	

		
	/*************************************************************************************************************************************************/
	//get the user db
	public function getUserDB(Void):Array{
		return this.__arrUsersDB;
		};
	
	public function getRoomDB(Void):Array{
		return this.__arrRoomsDB;
		};
		
	public function getRooms(Void):Array{
		return this.__arrRooms;
		};	
		
	public function getModeratorDB(Void):Array{
		return this.__arrModeratorOfRoom;
		};	
		
	public function getPrivateDB(Void):Array{
		return this.__arrPrivateDB;
		};	
		
	public function getUserDBByRoomName(roomName:String):Array{
		return this.__arrRoomsDB[roomName]['users'];
		};	
		
	public function getModeratorByRoomName(roomName:String):Array{
		return this.__arrRoomsDB[roomName]['moderator'];
		};	
		
	public function getBannedDB(Void):Array{
		return this.__arrBannedDB;
		};	
			
	/*************************************************************************************************************************************************/	
	public function removeUserFromRoomDB(arrInfos:Array):Void{
		/*array params
		0= roomName (with or without prefix)
		1=nopub
		*/
		var roomNameWithPrefix = arrInfos[0];
		if(roomNameWithPrefix.substr(0,1) != '#'){
			roomNameWithPrefix = '#' + arrInfos[0];
			}
		var key = arrInfos[1];
		//from room DB
		if(this.__arrRoomsDB[roomNameWithPrefix]['users'][key] != undefined){
			delete this.__arrRoomsDB[roomNameWithPrefix]['users'][key];
			//if was in the DB maybe a room opened in a panelTab then refresh the list of this room
			if(this.__arrRooms[roomNameWithPrefix] != undefined){
				this.__arrRooms[roomNameWithPrefix].drawUserList();
				this.__arrRooms[roomNameWithPrefix].showUserInRoom(false, this.__arrUsersDB[key]);
				}
			}
		};	

	/*************************************************************************************************************************************************/	
	public function updateUserInfoFromDB(arrInfos:Array):Void{
		/*array params
		0= nopub
		1=state
		*/
		if(this.__arrUsersDB[arrInfos[0]] != undefined){
			this.__arrUsersDB[arrInfos[0]]['state'] = Number(arrInfos[1]);
			if(cAdmin != undefined){ //adminpanel
				//cAdmin.drawUserList();
				}
			}
		};
	

	/*************************************************************************************************************************************************/		
	
	//update les messages d'une fenetre
	public function updateMsg(bPublic:Boolean, arrInfos:Array):Void{
		/* arrpartams
		0 = tabName sans prefix
		1 = nopub de celui qui envoit le message
		2 = le msg 
		*/
		if(bPublic){ //pour les rooms
			if(this.__arrRooms[this.__roomPrefix + arrInfos[0]] != undefined){
				//check if the user was defined sinon will show undefined as username
				if(this.__arrUsersDB[arrInfos[1]] != undefined){
					this.__arrRooms[this.__roomPrefix + arrInfos[0]].updateMessage(unescape(arrInfos[2]), arrInfos[1]);
					}
				}
		}else{ //pour les conversation private (one<=>one)
			if(this.__arrRooms[arrInfos[0]] != undefined){
				this.__arrRooms[arrInfos[0]].updateMessage(unescape(arrInfos[2]), arrInfos[1]);
				}
			}
		};
		
	/*************************************************************************************************************************************************/		
	
	//update les messages d'une fenetre
	public function updatePrivateMsg(arrInfos:Array):Void{
		/* arrpartams
		0 = nopub_1 = celui qui envoit
		1 = nopub_2 = celui qui recoit
		2 = msg = celui qui recoit
		*/
		//rajoute a la DB si jamais n'existe pas encore et on recupere la cle et update le lastmsgtimestamp
		var tabName = this.addPrivateToDB(arrInfos[0], arrInfos[1]);
		if(this.__arrRooms[tabName] != undefined){
			this.__arrRooms[tabName].updateMessage(unescape(arrInfos[2]), arrInfos[0]);
			}
		};


	/*************************************************************************************************************************************************/		
	
	//creeer une room public 
	private function createSpecialRoom(tabName:String):Void{
		if(this.__arrRooms[tabName] == undefined){
			//ref to the class
			this.__arrRooms[tabName] = new CieSpecialIrc(tabName, this.__tabManager, this.__arrPrivateDB[tabName]);
		}else{
			//set the tab focus
			cContent.openTab(['irc',tabName]);
			}
		};	

	/*************************************************************************************************************************************************/		
	
	//destroy une room public c'est a dire le Tab pas le data
	private function closeSpecialRoom(roomName:String):Void{
		//dete the ref opf class
		delete this.__arrRooms[roomName];
		};	
		
	/*************************************************************************************************************************************************/		
	//creeer une room public 
	private function createPublicRoom(tabName:String):Void{
		//si est ouverte on ouvre le tab seulement sinon on fait la requete au seveur java comme quoi nous entrons dans la room
		if(this.__arrRooms[tabName] == undefined){
			//Debug("createPublicRoom(): " + tabName);
			//ref to the class
			this.__arrRooms[tabName] = new CieRoomIrc(tabName, this.__tabManager, this.__arrRoomsDB[tabName]);
		}else{
			//set the tab focus
			cContent.openTab(['irc',tabName]);
			}
		};	
		
	/*************************************************************************************************************************************************/		
	//destroy une room public c'est a dire le Tab pas le data
	private function closePublicRoom(roomName:String):Void{
		//dete the ref opf class
		//Debug("closePublicRoom(): " + roomName);
		delete this.__arrRooms[roomName];
		};
		
	/*************************************************************************************************************************************************/		
	//destroy une room et son data, vient de la command serveur quan d un admin close une room pour toujours
	private function closeRoomFromServerCommand(roomName:String):Void{
		//Debug("closeRoomFromServerCommand(): " + roomName);
		//on rajoute le prfix devant car le serveur ne le fait pas
		var roomNameWithPrefix = this.__roomPrefix + roomName;
		//check si un tab est ouvert
		if(this.__arrRooms[roomNameWithPrefix] != undefined){
			//destroy du tab qui va lui meme appele closePublicRoom() une fois cleaner
			this.__arrRooms[roomNameWithPrefix].Destroy();
			//avertir l'usager qu'il est kicker de la room
			//new CieTextMessages('MB_OK', gLang[142] + '<b>' + roomName + '</b>', gLang[141]);
			new CieTextMessages('MB_OK', 'Désolée, mais la room ' + '<b>' + roomName + '</b>' + ' est maintenant fermée.', gLang[141]);
			}
		//on l'enleve de la liste des rooms
		if(this.__arrRoomsDB[roomNameWithPrefix] != undefined){
			delete this.__arrRoomsDB[roomNameWithPrefix];
			}

		if(cOptions != undefined){
			//refresh user list in options tab
			cOptions.drawRoomList();
			if(roomName == BC.__user.__pseudo){	
				//s'enleve lui-meme de la liste des mods, car la room n'existe plus anyway
				this.promoteUserToModeratorForRoom([roomName, '0']);
				//si jamais la room a le meme nom que sdont pseudo c'est qu'il en rest le createur alors reafraichir la seftion options
				cOptions.changeWelcomeText();
				}
			}	
		
		//si admin
		if(cAdmin != undefined){
			cAdmin.drawRoomList();
			}
		};	
		
	/*************************************************************************************************************************************************/		
	//message admin quand qulequ'un est killer ou banni et tente de ce reloguer
	private function showAdminMessage(strType:String, strDateKilled:String):Void{	
		if(strType == 'killed'){
			//conversion de la date
			//on enleve les 3 dernier charatere car en milliseconde
			//Debug("KILLEWD THE_1: " + strDateKilled);
			strDateKilled = strDateKilled.substr(0, (strDateKilled.length - 3));
			//Debug("KILLEWD THE_2: " + strDateKilled);
			var dDate = new CieDate(strDateKilled, "Y/m/d H:i:s");
			new CieTextMessages('MB_OK', gLang[143] + dDate.printDate(), gLang[141]);
		}else if(strType == 'banned'){
			new CieTextMessages('MB_OK', gLang[144], gLang[141]);
			}
		};
		
	/** DELETE SPECIAL********************************************************************************************************************************************/	
	//detruit un special "nopub_1~nopub_2" de la DB private
	public function deleteSpecial(tabName:String):Void{	
		//destroy du tab
		this.__arrRooms[tabName].Destroy();
		delete this.__arrRooms[tabName];		
		//remove des infos sur la room
		delete this.__arrPrivateDB[tabName];
		//refresh de la liste des rooms
		if(cAdmin != undefined){
			cAdmin.drawPrivateList();
			}
		};
	
	/** DELETE AL PRIVATE ROOM DATA********************************************************************************************************************************************/
	//detruit toute conversation prive les tab ouvert comme les finos DB
	public function deleteAllPrivateData(Void):Void{	
		for(var o in this.__arrPrivateDB){
			this.__arrRooms[o].Destroy();
			delete this.__arrRooms[o];
			delete this.__arrPrivateDB[o];
			}
		if(cAdmin != undefined){
			cAdmin.drawPrivateList();
			}
		};	
			
	/** DELETE ROOM ********************************************************************************************************************************************/	
	//destroy une room public c'est a dire le Tab pas le data
	public function deleteRoom(arrRooms:Array):Void{	
		this.__cActionMessages = new CieActionMessages(gLang[145], gLang[146] + '<b>' + arrRooms + '</b>' +  gLang[147]);
		this.__cActionMessages.setCallBackFunction(this.cbPromptDeleteRoom, {__class: this, __arrRooms:arrRooms});
		};
		
	//callback when user press button
	public function cbPromptDeleteRoom(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptDeleteRoom(bResult, cbObject.__arrRooms);
		};		
		
	//treat
	public function treatUserActionOnPromptDeleteRoom(bResult:Boolean, arrRooms:Array):Void{
	//depending on the user selection
		if(bResult){
			//on envoi au serveur
			for(var o in arrRooms){
				cSockManager.socketSend("DELETEROOM:" + arrRooms[o]);
				//Debug("SEND_DELETE_ROOM: " +  arrRooms[o]);
				}
			if(cAdmin != undefined){
				//cAdmin.drawUserList();
				}
			}
		//user have pressed cancel thrn close the window
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;	
		};			
		
	/*************************************************************************************************************************************************/		
	
	//update le counter de user in a room
 	public function updateCountRoom(arrInfo:Array):Void{
		/* args array
		0 = roomName
		1 = nouveau count un -1 ou +1
		*/
		//change the count
		this.__arrRoomsDB[this.__roomPrefix + arrInfo[0]]['usercount'] = Number(arrInfo[1]);
		//refresh de la liste des rooms
		cOptions.drawRoomList();
		if(cAdmin != undefined){
			//cAdmin.drawRoomList();
			}
		};	
		
	
	/*************************************************************************************************************************************************/		
	
	//creeer une room private
	private function createPrivateRoom(tabName:String, arrInfos:Array, bDontFocus:Boolean, fromRoomName:String):Void{
		//tabName is the npub of the person we chat with
		if(this.__arrRooms[tabName] == undefined){
			this.__arrRooms[tabName] = new CieUserIrc(tabName, this.__tabManager, arrInfos, bDontFocus, fromRoomName);
		}else{
			//set the tab focus
			cContent.openTab(['irc', tabName]);
			}
		};
		
	/*************************************************************************************************************************************************/		
	
	//destroy a private room	le Tab pas le data
	private function closePrivateRoom(tabName:String):Void{
		//delete the ref class
		delete this.__arrRooms[tabName];
		};
		
	/*************************************************************************************************************************************************/

	//get du tab manager general
	public function getTabManager(Void):CieTabManager{
		return this.__tabManager;
		};
	
	
	/***CREATE ROOM PRIVATE OURMEMBRE PRIVILEGE UNIQUEMENT ********************************************************************************/
	
	//set a room
	public function createDirectRoomPrivate(Void):Void{
		this.addRoomSloganPrivate();
		};
			
	//set a room slogan
	public function addRoomSloganPrivate(Void):Void{
		this.__cMessageBox = null;
		this.__cMessageBox = new CiePromptMessages(gLang[152], gLang[153] + '<b>' + BC.__user.__pseudo + '</b>');
		this.__cMessageBox.setCallBackFunction(this.cbAddRoomSloganPrivate, {__class: this});
		};	
	
	//callback when user make a choice of selection	
	public function cbAddRoomSloganPrivate(cbObject:Object):Void{
		if(cbObject.__ok == true){
			//send to server
			cbObject.__class.sendNewRoomToServerPrivate([cbObject.__class.__cMessageBox.getInputText()]);
			}
		};
	
	//send room to server
	public function sendNewRoomToServerPrivate(arrInfos:Array):Void{
		Debug("SEND_NEW_ROOM_PRIVATE: " +  arrInfos);
		cSockManager.socketSend("CREATEROOMPRIVATE:" + escape(arrInfos[0]));
		};
		
	//socket retunr confirmation on ROOMPRIVATECREATED
	public function confirmationCreationRoomPrivate(arrInfos:Array):Void{
		//0 = room name
		//2 = message
		Debug("CONFIRMATION_NEW_ROOM_PRIVATE_CREATED: " +  arrInfos);
		if(arrInfos[1] == 'OK'){
			new CieTextMessages('MB_OK', 'La room ' + '<b>' + arrInfos[0] + '</b>' + ' a bien été crée et devrait paraitre pour vous et les autres dans la liste de droite, vous en etes l\'administrateur, si vous n\'êtes plus dans cette room depuis plus de 10 minutes, celle-ci s\'auto-detruira.', 'Confirmation');
			//on ouvre la room qui vient d'etre cree
			//cFunc.createPublicRoom(arrInfos[0]);
		}else if(arrInfos[1] == 'ERR_EXIST'){	
			new CieTextMessages('MB_OK', 'La room ' + '<b>' + arrInfos[0] + '</b>' + ' est deja existante et devrait deja se trouver dans la liste des rooms.', 'Error');	
			}
		};
		
		
	
	/***CREATE ROOM *********************************************************************************************************************************/
	
	//set a room
	public function createDirectRoom(Void):Void{
		this.__cMessageBox = new CiePromptMessages(gLang[148], gLang[149]);
		this.__cMessageBox.setCallBackFunction(this.cbCreateDirectRoom, {__class: this});
		};
			
	//callback when user make a choice of selection
	public function cbCreateDirectRoom(cbObject:Object):Void{
		if(cbObject.__ok == true){
			var input = cbObject.__class.__cMessageBox.getInputText();
			if(cbObject.__class.checkIfRoomExistInDB(cbObject.__class.__roomPrefix + input)){
				new CieTextMessages('MB_OK', gLang[150] + '<b>' + input + '</b>' + gLang[151], gLang[109]);
				cbObject.__class.__cMessageBox = null;	
			}else{
				cbObject.__class.addRoomSlogan(input);
				}
			}
		};
		
	//set a room slogan
	public function addRoomSlogan(roomName:String):Void{
		this.__cMessageBox = null;
		this.__cMessageBox = new CiePromptMessages(gLang[152], gLang[153] + '<b>' + roomName + '</b>');
		this.__cMessageBox.setCallBackFunction(this.cbAddRoomSlogan, {__class: this, __roomName:roomName});
		};	
	
	//callback when user make a choice of selection	
	public function cbAddRoomSlogan(cbObject:Object):Void{
		if(cbObject.__ok == true){
			var input = cbObject.__class.__cMessageBox.getInputText();
			cbObject.__class.addRoomLanguage(cbObject.__roomName, input);
			}
		};
	
	//set a room language
	public function addRoomLanguage(roomName:String, roomSlogan:String):Void{
		this.__cMessageBox = null;
		this.__cMessageBox = new CiePromptMessages(gLang[154], gLang[155] + '<b>' + roomName + '</b>' + gLang[156]);
		this.__cMessageBox.setCallBackFunction(this.cbAddRoomLanguage, {__class: this, __roomName:roomName, __roomSlogan:roomSlogan});
		};	
		
	//callback when user make a choice of selection	
	public function cbAddRoomLanguage(cbObject:Object):Void{
		if(cbObject.__ok == true){
			var roomLanguage = cbObject.__class.__cMessageBox.getInputText();
			//minor check
			if(roomLanguage == '' || roomLanguage == undefined){
				roomLanguage = 'fr_CA';
				}
			//send to server
			cbObject.__class.sendNewRoomToServer([cbObject.__roomName, cbObject.__roomSlogan, roomLanguage]);
			}
		cbObject.__class.__cMessageBox = null;	
		};	
		
	//send room to server
	public function sendNewRoomToServer(arrInfos:Array):Void{
		/*arrparmas
		0 = roomname
		1 =roomslogan
		2 = roomlanguage
		
		//image are not there yet TODO::Later
		
		*/
		//Debug("SEND_NEW_ROOM: " +  arrInfos);
		cSockManager.socketSend("CREATEROOM:" + arrInfos[0] + ',' + 't3.jpg' + ',' + escape(arrInfos[1]) + ',' + arrInfos[2]);
		};
		
	/*************************************************************************************************************************************************/	
	//check if room tab is already opened
	public function checkIfRoomTabExist(roomName:String):Boolean{
		if(this.__arrRooms[roomName] != undefined){ //public room
			return true;
			}
		return false;	
		};
		
	/*************************************************************************************************************************************************/	
	//check if room exist ino the DB of room doesnt meen that its opened in a tab
	public function checkIfRoomExistInDB(roomName:String):Boolean{
		if(this.__arrRoomsDB[roomName] != undefined){
			return true;
			}
		return false;	
		};	
	
	/*************************************************************************************************************************************************/	
	//refresh la liste du panel options (cOptisn)
	public function refreshList(Void):Void{
		//Debug("refreshList()");
		if(cOptions != undefined){
			cOptions.drawRoomList();
			}
		if(cAdmin != undefined){
			//cAdmin.drawRoomList();
			cAdmin.drawPrivateList();
			//cAdmin.drawUserList();
			}	
		};	
	
	/*************************************************************************************************************************************************/	
	//set the title of the form when ots an apppkiication
	public function setTitleForm(strTitle:String):Void{
		if(strTitle == undefined || strTitle == ''){
			mdm.Forms[BC.__system.__formName].title = 'IRC (not connected)';
		}else{
			mdm.Forms[BC.__system.__formName].title = 'IRC (' + strTitle + ')';
			}
		};	
		
	/*************************************************************************************************************************************************/	
	//open a tab 	
	public function openTab(arrParam:Array):Void{
		cContent.openTab(arrParam);
		};
		
	/*************************************************************************************************************************************************/	
	//change the xmlNode value	
	public  function changeNodeValue(xmlNode:XMLNode, arrParam:Array):Void{
		cContent.changeNodeValue(xmlNode, arrParam);
		};
		
	/*************************************************************************************************************************************************/	
	//check if a panel is already exist	
	public function checkIfPanelIDExist(arrPanel:Array, id:Number):Boolean{
		if(cContent.getPanelID(arrPanel) == id){
			return true;
			}
		return false;	
		};
	
	
	/*************************************************************************************************************************************************/
	//get the tabmanager for a certain path tab
	public function getPanelTabManager(arrPanel:Array):CieTabManager{
		return cContent.getPanelTabManager(arrPanel);
		};
	
	
	/*************************************************************************************************************************************************/
	//get the panel content for a certain path tab
	public function getPanelContent(arrPanel:Array):CiePanel{
		return cContent.getPanelClass(arrPanel).getPanelContent();
		};
	
	/*************************************************************************************************************************************************/
	//get the panel objcet for a certain path tab
	public function getPanelObject(arrPanel:Array):Object{
		return cContent.getPanelObject(arrPanel);
		};	
		
	/*************************************************************************************************************************************************/	
	//set le contenu d'un panel	
	public function setPanelContent(arrPanel:Array, strContent:String):CiePanel{
		return cContent.getPanelClass(arrPanel).setContent(strContent);
		};
		
	/*************************************************************************************************************************************************/		
	//notify user when no connection to the net is possible	
	public function notifyUserOnHttpRequestError(Void):Void{
		//prompt the question 
		setLoading(false); 
		new CieTextMessages('MB_OK', gLang[180], gLang[179]);
		};
		
	/*************************************************************************************************************************************************/		
	//send message to all user of a certain language
	public function sendMessageToAll(strToSend:String, strLang:String):Void{
		this.__cActionMessages = new CieActionMessages(gLang[141] + " (" + strLang + ")", strToSend);
		this.__cActionMessages.setCallBackFunction(this.cbPromptSendMessageToAll, {__class: this, __message:strToSend, __lang:strLang});
		};	
		
	//callback when user press button
	public function cbPromptSendMessageToAll(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptSendMessageToAll(bResult, cbObject.__message, cbObject.__lang);
		};
		
	//rajout au kicked
	public function treatUserActionOnPromptSendMessageToAll(bResult:Boolean, strToSend:String, strLang:String):Void{
		//depending on the user selection
		if(bResult){
			//on envoi au serveur
			cSockManager.socketSend("ADMINMESSAGETOALL:" + escape(strToSend) + ',' + strLang);
			//Debug("SENDING: " + strToSend);
			}
		//user have pressed cancel thrn close the window
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;	
		};		
		
	/*************************************************************************************************************************************************/		
	//reception d'un message de l'admin
	public function messageFromYourAdmin(strToShow:String):Void{	
		new CieTextMessages('MB_OK', unescape(strToShow), gLang[141]);
		};
		
	/*************************************************************************************************************************************************/			
	//rajout d'un moderator via le pseudo
	public function addModeratorByPseudo(strRoomNameNoPrefix:String):Void{
		this.__cMessageBox = new CiePromptMessages(gLang[4], gLang[5] +  "<b>" + strRoomNameNoPrefix + "</b>:");
		this.__cMessageBox.setCallBackFunction(this.cbAddModeratorByPseudo, {__class: this, __roomname:strRoomNameNoPrefix});
		};
		
	//callback when user make a choice of selection
	public function cbAddModeratorByPseudo(cbObject:Object):Void{
		if(cbObject.__ok == true){
			var input = cbObject.__class.__cMessageBox.getInputText();
			Debug("INPUT: " + input);
			}
		};	
		
		
	/*****LOCAL SOCKET COMMAND TREATMENT CALLED BY CieLocalSocket*******************************************************************************/
	
	public function localSocketCommand(param:Array):Void{
		//Debug('LC_' + BC.__user.__localconn + ': ' + param);
		//param[0] est le nom de la fenetre appelante
		//param[1] est un array d'argument
		//Debug("LC_COMMAND_NOT_FOUND: " + param);
		
		};
	
	
	/***STRING FORMATAGE*********************************************************************************************************************************/
	
	//format the string for html format web pages /////////////////////////////////////////////////////////////////////////////
	public function FormatString(htmlStr:String):String{
		var mainStr:String = new String();
		htmlStr = this.FormatBR(htmlStr);
		htmlStr = this.ReplaceSpecialChar(htmlStr, "=", "&#61;");
		var aStr:Array = htmlStr.split("<");
		for(i=0;i<aStr.length;i++){
			if(aStr[i].indexOf("B>",0) == -1){
				if(aStr[i].indexOf("I>",0) == -1){
					if(aStr[i].indexOf("U>",0) == -1){	
						mainStr += aStr[i].substring(aStr[i].indexOf(">",0) + 1 , aStr[i].length );	
					}else{
						mainStr += "<" + aStr[i];
						}	
				}else{
					mainStr += "<" + aStr[i];	
					}
			}else{
				mainStr += "<" + aStr[i];	
				}
			}
		return mainStr;
		}

	//replace end of ligne with <BR> /////////////////////////////////////////////////////////////////////////////
	private function FormatBR(htmlStr:String):String{
		var mainStr = new String();	
		var aStr:Array = htmlStr.split('<P ALIGN="LEFT">');
		for(i=0;i<aStr.length;i++){
			mainStr += aStr[i].split("</P>")[0];
			}
		return (mainStr);
		}	

	//replace quote and double quote etc .../////////////////////////////////////////////////////////////////////////////
	private function FormatSpecialChar(htmlStr:String):String{
		var mainStr = new String();
		//array of special char
		var aSpecialChar = new Array(4);
		aSpecialChar[0] = new Array(2);
		aSpecialChar[1] = new Array(2);
		aSpecialChar[2] = new Array(2);
		aSpecialChar[3] = new Array(2);
		
		aSpecialChar[0][0] = "&quot;";
		aSpecialChar[0][1] = '"';
		aSpecialChar[1][0] = "&apos;";
		aSpecialChar[1][1] = "'";
		
		htmlStr = this.ReplaceSpecialChar(htmlStr, aSpecialChar[0][0], aSpecialChar[0][1]);
		htmlStr = this.ReplaceSpecialChar(htmlStr, aSpecialChar[1][0], aSpecialChar[1][1]);
		return htmlStr;	
		}	

	//goes with the FormatSpecialChar function	/////////////////////////////////////////////////////////////////////////////
	private function ReplaceSpecialChar(htmlStr:String, SpecialChar:String, ReplaceChar:String):String{
		var mainStr = new String();
		var aStr:Array = htmlStr.split(SpecialChar);
		for(i=0;i<aStr.length;i++)
			{
			mainStr += aStr[i];
			if((aStr.length - (i+1)) > 0  )
				mainStr += ReplaceChar;
			}
		return mainStr;	
		}
	
	
	
	/*************************************************************************************************************************************************/
	
	public function getClassName(Void):String{
		return __className;
		};
	
	/*************************************************************************************************************************************************/
	
	public function getClass(Void):CieFunctions{
		return this;
		};
	}	