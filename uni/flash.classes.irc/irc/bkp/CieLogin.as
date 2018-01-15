/*

section login

cette section est le starter de tout une fois le PHPSESSION UNI::PHP recupere, il fait sa demande au serveur UNI:JAVA:
ouvre les fsection set le loading bar et reset les tray menu

*/

import control.CiePanel;
import control.CieTextLine;
import control.CieButton;

import messages.CieTextMessages;

dynamic class irc.CieLogin{

	static private var __className = 'CieLogin';
	static private var __instance:CieLogin;
	private var __type:String;
	private var __hvSpacer:Number;
	private var __hBox:Number;
	private var __h:Number;
	private var __keyListener:Object;
	private var __selectBoxWidth:Number;
	
	//thread
	private var __cThreadConnect:Object;
	private var __cThreadUserInterface:Object;
	private var __cThreadUserBasic:Object;
		
	private function CieLogin(Void){
		this.__hvSpacer = 10;
		this.__hBox = 17;
		this.__hButt = 35;
		this.__selectBoxWidth = 150;
		this.__h = 0;
		};
		
	static public function getInstance(Void):CieLogin{
		if(__instance == undefined) {
			__instance = new CieLogin();
			}
		return __instance;
		};	
		
	public function reset(Void):Void{
		//ckear al threads
		this.__cThreadConnect.destroy();
		this.__cThreadUserInterface.destroy();
		this.__cThreadUserBasic.destroy();

		this.__cThreadConnect = null;;
		this.__cThreadUserInterface = null;
		this.__cThreadUserBasic = null;
		
		this.__h = 0;
		};
		
	/*************************************************************************************************************************************************/	
	
	public function openLogin(strError:String):Void{
		//
		cContent.openTab(['irc']);
		//class ref
		var cP:CiePanel = cContent.getPanelClass(['irc','_tl']);
		cP.setContent('mvBgPanel');
		//panel content
		var mvPanel:MovieClip = cP.getPanelContent();
		this.__h = 10;
		
		// PSEUDO BOXES
		new CieTextLine(mvPanel, this.__hvSpacer, this.__h, 0, this.__hBox, 'textfield3', gLang[157], 'dynamic',[], false, false, false, false);
		this.__h += this.__hBox;
		var pseudoBox = new CieTextLine(mvPanel, this.__hvSpacer, this.__h, this.__selectBoxWidth, this.__hBox, 'textfieldBox1', BC.__user.__pseudo, 'input',[], false, true, false, true);
		Selection.setFocus(pseudoBox.getTextField());
		this.__h += this.__hBox;
		
		// PASSWORD BOXES
		new CieTextLine(mvPanel, this.__hvSpacer, this.__h, 0, this.__hBox, 'textfield4', gLang[158], 'dynamic',[], false, false, false, false);
		this.__h += this.__hBox;
		var pswBox = new CieTextLine(mvPanel, this.__hvSpacer, this.__h, this.__selectBoxWidth, this.__hBox, 'textfieldBox2', BC.__user.__psw, 'input',[], false, true, true, true);
		this.__h += this.__hBox + this.__hvSpacer;
		
		// SEND BUTTON
		//instance of the the button
		var button:CieButton = new CieButton(mvPanel, gLang[159], this.__selectBoxWidth, this.__hButt, this.__hvSpacer, this.__h);
		button.getMovie().__sup = this;
		button.getMovie().__panel = cP;
		button.getMovie().__pseudo = pseudoBox;
		button.getMovie().__psw = pswBox;
		button.getMovie().onRelease = function(Void):Void{
			BC.__user.__sessionID = 0;
			BC.__user.__pseudo = this.__pseudo.getSelectionText();
			BC.__user.__psw = this.__psw.getSelectionText();
			this.__sup.getLogin();
			};
		this.__h += this.__hButt + this.__hvSpacer;
					
		//remove the loading mask
		setLoading(false); //this function is on the stageLoader
		
		//prompt comme quoi la demande est refuse
		if(strError != '' && strError != undefined){
			if(strError == 'ERROR_INVALID_USER'){
				new CieTextMessages('MB_OK', gLang[160] , gLang[161]);
			}else{
				new CieTextMessages('MB_OK', gLang[162], gLang[161]);
				}
		}else{
			//set the key listener
			this.loginKeyListener(true, pseudoBox, pswBox);
			}
		};
	
	/*************************************************************************************************************************************************/	
	public function loginKeyListener(bEnable:Boolean, pseudoBox:Object, pswBox:Object):Void{
		if(bEnable){
			this.__keyListener = new Object();
			this.__keyListener.__sup = this;	
			this.__keyListener.__pseudo = pseudoBox;
			this.__keyListener.__psw = pswBox;
			this.__keyListener.onKeyUp = function(){
					if(Key.getCode() == Key.ENTER){
						BC.__user.__sessionID = 0;
						BC.__user.__pseudo = this.__pseudo.getSelectionText();
						BC.__user.__psw = this.__psw.getSelectionText();
						this.__sup.getLogin();
						}
					};
			Key.addListener(this.__keyListener);
		}else{
			Key.removeListener(this.__keyListener);
			delete this.__keyListener;
			}
		};	
	
	/*************************************************************************************************************************************************/	
	public function getLogin(Void):Void{
		//remove key listener
		this.loginKeyListener(false);
		//when its a reconnect move the loader a bit
		setLoading(true);
		setLoadingBar(10);
		//build request
		var arrD = new Array();
		arrD['methode'] = 'login';
		arrD['action'] = '';
		arrD['arguments'] = '';
		//add the request
		cReqManager.addRequest(arrD, this.cbLogin, this, true);
		};
		
	/*************************************************************************************************************************************************/	
	public function cbLogin(prop, oldVal:Number, newVal:Number, obj:Object){
		//when its a reconnect move the loader a bit
		setLoadingBar(30);
		obj.__super.parseLogin(obj.__req.getXml().firstChild);
		cReqManager.removeRequest(obj.__req.getID());
		};	
		
	/*************************************************************************************************************************************************/	
	public function parseLogin(xmlNode:XMLNode):Void{
		//Debug("NODE: " + xmlNode);
		setLoadingBar(50);
		var strError:String = '';
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.attributes.n == 'error'){
				strError = currNode.firstChild.nodeValue;
			}else if(currNode.attributes.n == 'nopub'){
				BC.__user.__nopub = currNode.firstChild.nodeValue;
				Debug('NOPUB: ' + BC.__user.__nopub);
			}else if(currNode.attributes.n == 'session'){
				BC.__user.__sessionID = currNode.firstChild.nodeValue;
				Debug('SESSIONID: ' + BC.__user.__sessionID);
			}else if(currNode.attributes.n == 'photoflag'){
				BC.__user.__photo = currNode.firstChild.nodeValue;
				//Debug('PHOTOFLAG: ' + BC.__user.__photo);
				}
			}
		//si erreur
		if(strError != ''){	
			this.reset();
			this.openLogin(strError);
		}else{
			setLoadingText(gLang[3]);
			setLoadingBar(70);
			cSockManager.setConnection();
			}
		};	
		
	
	/*************************************************************************************************************************************************/
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieLogin{
		return this;
		};
	}	