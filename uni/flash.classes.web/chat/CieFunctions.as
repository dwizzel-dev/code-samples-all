/*

functins for the core of the application
caller of the managers functions
can be accessed by anything

*/

import chat.CieFms;
import utils.CieThread;
//import messages.CieTextMessages;

dynamic class chat.CieFunctions{

	static private var __className = "CieFunctions";
	static private var __instance:CieFunctions;
	
	private var __chatIN:Object;
	private var __chatOUT:Object;
	public var __intervalShakeHand:Number;
	private var __otherUserIsReady:Boolean;
	public var __isReadyLoopCounter:Number;
	public var __isReadyMaxLoopCounter:Number;
	private var __cThreadIsTyping:CieThread;
	
	private var __lastReceivedMsgDate:String;
		
	private function CieFunctions(Void){
		this.__otherUserIsReady = false;
		this.__lastReceivedMsgDate = '';
		this.__isReadyLoopCounter = 0;
		this.__isReadyMaxLoopCounter = 20;
		};
		
	/*************************************************************************************************************************************************/	
		
	static public function getInstance(Void):CieFunctions{
		if(__instance == undefined) {
			__instance = new CieFunctions();
			}
		return __instance;
		};
		
	/*************************************************************************************************************************************************/	
		
	public function reset(Void):Void{
		//
		};
	
	/*************************************************************************************************************************************************/	
		
	public function openChat(Void):Void{
		Debug('openChat()');
		if(_global.secFms == undefined){
			//instance
			_global.secFms = CieFms.getInstance();
			secFms.openChat(BC.__user.__type);
			//tell the server we are ready to go, kind of a ping untill we get one from the other
			this.__intervalShakeHand = setInterval(this, 'clientIsReady', 2000, this);
		}else{
			cSectionManager.setTabFocus('fms');
			}
		};
		
	/*************************************************************************************************************************************************/	
		
	public function disconnectCamera(Void):Void{
		if(_global.secFms != undefined){
			secFms.disconnectCamera(BC.__user.__type);
			}
		};	
		
	/***REGISTER THE CHAT IN/OUT Object**********************************************************************************************************************************************/		

	public function registerChatObject(In:Object, Out:Object):Void{
		//Debug('registerChatObject(' + In + ', ' + Out + ')');
		this.__chatIN = In;
		this.__chatOUT = Out;
		};
		
	/**SEND MESSAGE CHAT TEXTE IN***********************************************************************************************************************************************/
	
	public function sendMessage(Void):Void{
		//get the text standard html formated
		var str:String = this.FormatString(this.__chatIN.txtInfos.htmlText); 
		//clear the text
		this.__chatIN.txtInfos.text = '';
		//send the message to the other user
		cLocalConn.callRemoteSocket(BC.__user.__remoteconn, ['MSG', BC.__user.__pseudo, str]);
		//update OUT chat
		this.updateMessage(str, true);
		};
	
	/***has connected his camera*********************************************************************************************************************************/	
	
	public function cameraIsOn(Void):Void{
		//tell the other that we have connected our camera
		cLocalConn.callRemoteSocket(BC.__user.__remoteconn, ['CAM_ON', BC.__user.__pseudo]);
		};
	
	/**TELL TEHE SREVER WE ARE READY TO GO***********************************************************************************************************************************************/
	
	public function clientIsReady(cbClass:Object):Void{
		//send msg to the good chat window via the local_conn
		//Debug('LOOP_COUNTER_IS_READY: ' + cbClass.__isReadyLoopCounter);
		cbClass.__isReadyLoopCounter++;
		if(cbClass.__isReadyLoopCounter < cbClass.__isReadyMaxLoopCounter){
			cLocalConn.callRemoteSocket(BC.__user.__remoteconn, ['IS_READY', BC.__user.__pseudo]);
			if(cbClass.__isReadyLoopCounter == (cbClass.__isReadyMaxLoopCounter/2) ){
				setLoadingText(gLang[27]);
				}
		}else{
			clearInterval(cbClass.__intervalShakeHand);
			//close the chat
			cbClass.closeChat(true);
			//typing actions
			this.typingAction(2);
			//msg
			setLoadingText(gLang[24]);
			}
		};	
		
	/**TELL THE SERVER WE ARE CLOSING THE CONVERSATION***********************************************************************************************************************************************/
	
	public function closeChat(bBroadcast:Boolean):Void{
		//send msg to the good chat window via the local_conn
		if(bBroadcast){
			cLocalConn.callRemoteSocket(BC.__user.__remoteconn, ['CLOSE', BC.__user.__pseudo]);
			}
		//cleanup the chat via instantce of CieFms
		secFms.closeChat(BC.__user.__type);
		//kill the thread for typing msg
		this.__cThreadIsTyping.destroy();
		};

	/**UPDATE MESSAGE CHAT TEXTE OUT FOR ADMIN USE **************************************************************************************************************************************/
	
	public function updateAdminMessage(str:String):Void{
		//update text
		this.__chatOUT.txtInfos.htmlText += '<font color="#ff0000"><b>admin:</b> ' + str + '</font>\n\n' ; 
		this.__chatOUT.txtInfos.scroll += 10000;
		};		
		
	/**UPDATE MESSAGE CHAT TEXTE OUT***********************************************************************************************************************************************/
	
	public function updateMessage(str:String, bLocal:Boolean):Void{
		//update text
		if(bLocal){ //if its me who is writing
			this.__chatOUT.txtInfos.htmlText += '<font color="#999999"><b>' + BC.__user.__localpseudo + ':</b> ' + str + '</font>\n\n' ; 
		}else{ //its the other
			//show the msg
			this.__chatOUT.txtInfos.htmlText += '<b>' + BC.__user.__pseudo + ':</b> ' + str + '\n\n' ; 
			}
		//pos scroll
		this.__chatOUT.txtInfos.scroll += 10000;
		//TODO
		//SEND TO THE LOCAL SOCKET OF WIN_MASTER SO IT CAN RELAY MESSAGE TO UNI
		};	
		
	/*****LOCAL SOCKET COMMAND TREATMENT CALLED BY CieLocalSocket*******************************************************************************/
	
	public function localSocketCommand(param:Array):Void{
		//Debug('LC_' + BC.__user.__localconn + ': ' + param);
		Debug('RCV: ' + param);
		//param[0] est le nom de la fenetre appelante
		//param[1] est un array d'argument
		if(param[1][0] == 'MSG'){
			//c'est une commande message
			this.updateMessage(param[1][1], false);
			this.typingAction(3);
		}else if(param[1][0] == 'CAM_ON'){
			//c'est une commande update
			Debug('CAM_ON');
			//l'autre a connecte sa camera. alors on demande a l'usager si il veut faire de meme
			secFms.otherCameraIsOn();
		}else if(param[1][0] == 'IS_TYPING'){
			//c'est une commande update
			this.typingAction(1);
			if(!this.__otherUserIsReady){
				//and ssend a last one to be sure it didn't went in the air
				this.clientIsReady(this);
				//remove the loading screen
				setLoading(false);	
				}
			//fi IS_READY just passed by for whatever reason
			//clear le ping interval
			clearInterval(this.__intervalShakeHand);
			//change flag
			this.__otherUserIsReady = true;	
		}else if(param[1][0] == 'IS_NOT_TYPING'){
			//c'est une commande update
			this.typingAction(0);
		}else if(param[1][0] == 'IS_READY'){
			//c'est une commande update comme quoi l'autre est pret
			//Debug('RECEIVING ----------------------------------------------------------->(IS_READY)');
			if(!this.__otherUserIsReady){
				//and ssend a last one to be sure it didn't went in the air
				this.clientIsReady(this);
				//remove the loading screen
				setLoading(false);
				//startr du thread de verif si l'usager tape ou non sur on claivier
				this.__cThreadIsTyping = cThreadManager.newThread(2000, this, 'checkIfUserIsTyping', {__supclass:this, __newMsg:'', __oldMsg:this.__chatIN.txtInfos.text, __newCmd:'', __oldCmd:''});
				}
			//clear le ping interval
			clearInterval(this.__intervalShakeHand);
			//change flag
			this.__otherUserIsReady = true;	
			//connected
			this.typingAction(4);
		}else if(param[1][0] == 'CLOSE'){
			//si c'est une fermeture de chat
			this.typingAction(2);
			//close the chat virtually not EXIT
			this.closeChat(false);
		}else if(param[1][0] == 'EXIT'){
			//l'application principal c'est ferme, fermer cette fenetre aussi
			//have to call a javascript method so the window will close when user logout
		}else{
			//unknown command
			Debug("LC_COMMAND_NOT_FOUND: " + param);
			}
		};
		
	/***STRING FORMATAGE*********************************************************************************************************************************/
	
	//format the string for html format web pages /////////////////////////////////////////////////////////////////////////////
	private function FormatString(htmlStr:String):String{
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
	
	//function d'update du message de si l'autre usager tape sur son clvier ou non
	public function typingAction(iTyping:Number):Void{
		if(iTyping == 0){
			if(this.__lastReceivedMsgDate == ''){
				this.typingAction(4);
			}else{
				this.__chatOUT.txtTyping.htmlText = gLang[16] + BC.__user.__pseudo + gLang[17] + this.__lastReceivedMsgDate;
				}
		}else if(iTyping == 1){
			this.__chatOUT.txtTyping.htmlText = gLang[18] + BC.__user.__pseudo + gLang[19];
		}else if(iTyping == 2){
			this.__chatOUT.txtTyping.htmlText = gLang[20] + BC.__user.__pseudo + gLang[21];
		}else if(iTyping == 3){
			var dDate = new Date();
			var dMinute = dDate.getMinutes();
			if(dMinute < 10){
				dMinute = '0' + dMinute;
				}
			var dSecond = dDate.getSeconds();
			if(dSecond < 10){
				dSecond = '0' + dSecond;
				}	
			this.__lastReceivedMsgDate = dDate.getHours() + ':' + dMinute + ':' + dSecond;
			this.__chatOUT.txtTyping.htmlText = gLang[22] + BC.__user.__pseudo + gLang[23] + this.__lastReceivedMsgDate;
		}else if(iTyping == 4){
			this.__chatOUT.txtTyping.htmlText = gLang[13] + BC.__user.__pseudo + gLang[14];
			}
		}	
	
	/*************************************************************************************************************************************************/	
	//check si l'usager est en train de taper	
	public function checkIfUserIsTyping(obj:Object):Boolean{	
		obj.__newMsg = obj.__supclass.__chatIN.txtInfos.text;
		obj.__newCmd = '';
		if(obj.__newMsg != ''){
			if(obj.__oldMsg != obj.__newMsg){
				obj.__newCmd = 'IS_TYPING';
			}else{
				obj.__newCmd = 'IS_NOT_TYPING';
				}	
		}else{
			obj.__newCmd = 'IS_NOT_TYPING';
			}
		if(obj.__newCmd != obj.__oldCmd){	
			//send the message to the other user
			cLocalConn.callRemoteSocket(BC.__user.__remoteconn, ['MSG', BC.__user.__pseudo, obj.__newCmd]);
			}
		obj.__oldMsg = obj.__newMsg;
		obj.__oldCmd = obj.__newCmd;		
		return true;
		};
	
	/*************************************************************************************************************************************************/
	
	public function getClassName(Void):String{
		return __className;
		};
	
	/*************************************************************************************************************************************************/
	
	public function getClass(Void):CieFunctions{
		return this;
		};
	}	