/*

functins for the core of the application
caller of the managers functions
can be accessed by anything


modification: dwizzel 24-07-2008
modifi de la methode sendCourriel pour afficher le progress de l'envoi d'un fichier

*/

//import manager.CieContentManager;
//import manager.CieRequestManager;
//import manager.CieStageManager;
//import manager.CieTabManager;
//import manager.CieToolsManager;

//import display.CieListing;
import display.CieBottin;
import display.CieMessage;
import display.CieLogin;
import display.CieRecherche;
import display.CieWelcome;
import display.CieAide;
import display.CieOptions;
import display.CieSalon;
import display.CieDetailedProfil;

import messages.CieActionMessages;
import messages.CieTextMessages;
import messages.CieOptionMessages;
import messages.CieChatMessages;
import messages.CieVideoUploadMessages;

import flash.net.FileReference;

//import control.CieTools;
import control.CiePanel;

//import comm.CieLocalSocket;
import comm.CieUpdate;

//for video display
import messages.CieVideoMessages;
import messages.CieAudioMessages;

//record
import messages.CieRecordMessages;

dynamic class core.CieFunctions{

	static private var __className = "CieFunctions";
	static private var __instance:CieFunctions;
	
	private var __arrSections:Array;
	private var __lastSection:String;
	private var __lastPubSection:String;
	
	//for subSection for KEY listener on form send
	//private var __lastSubSection:String;
	
	//opened details profil
	//private var __arrDetailedProfilOpened:Array;
	
	//updates
	private var __cUpdate:CieUpdate;
	
	//for the chat win
	private var __chatWinCounter:Number;
	private var __chatWindow:Array;
	
	//popup window holder
	private var __cActionMessages:Object; //for all popup window, because there will be only one at once, because user cannot select any other thing
	private var __cChatMessages:Object; //for all CHAT popup window, because there can be other popup opened at the same time, so there is a problem with callBack action when user pressed OK or CANCEL or CLOSE
	private var __cNotifyMessages:Object; //for popup window on notifyUserOnHttpError, same as chatWindow
	private var __cRecordMessages:Object; //for popup window for record window
	private var __cVideoMessages:Object; //for the upload of a video description
	
	//new object
	private var __serveurUpload:String;
	private var __arrAttachFiles:Array;
	private var __isAttachFinished:Boolean;
	
	private var __cFileReferenceForAttach:FileReference;
	private var __cFileReferenceForAttachListener:Object;  
	
	private var __cThreadKeepAlive:Object;
	
	//private var __cThreadPub:Object;
	
	//private var __directXEnabled:Boolean;
	
	private var __bRechercheDetailleeIsLoaded:Boolean;
	
	//for banner
	//private var __mclBanner:MovieClipLoader;
	//private var __objBannerListener:Object;
	
	private function CieFunctions(Void){
		//this.__lastSubSection = '';
		this.__lastSection = '';
		//this.__arrDetailedProfilOpened = new Array();
		this.__arrSections = new Array();
		this.__chatWinCounter = 0;
		this.__chatWindow = new Array();
		this.__bRechercheDetailleeIsLoaded = false;
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
		//key listener
		//this.addKeyListener(false);
		//this.__lastSubSection = '';
		
		//close any messages
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		
		this.__cChatMessages.closeWindow();
		this.__cChatMessages = null;
		
		this.__cNotifyMessages.closeWindow();
		this.__cNotifyMessages = null;
		
		this.__cRecordMessages.closeWindow();
		this.__cRecordMessages = null;
		
		this.startKeepAlive(false);
		//this.startPubTimer(false);
		this.__lastSection = '';
		//this.__arrDetailedProfilOpened = new Array();
		this.__arrSections = new Array();
		this.__chatWindow = new Array();
		this.__bRechercheDetailleeIsLoaded = false;
		//delete this.__objBannerListener;
		//delete this.__mclBanner;
		
		secLogin.reset();
		delete secLogin;
		secSalon.reset();
		delete secSalon;
		secAide.reset();
		delete secAide;
		secOptions.reset();
		delete secOptions;
		secWelcome.reset();
		delete secWelcome;
		secRecherche.reset();
		delete secRecherche;
		secBottin.reset();
		delete secBottin;
		secMessage.reset();
		delete secMessage;

		};
	
	/*************************************************************************************************************************************************/		
	//notify user when no connection to the net is possible	
	public function notifyUserOnHttpRequestError(Void):Void{
		Debug('+++notifyUserOnHttpRequestError()');
		//prompt the question 
		setLoading(false); 
		this.__cNotifyMessages = new CieActionMessages(gLang[10], gLang[328]);
		this.__cNotifyMessages.setCallBackFunction(this.cbPromptNotifyUserOnHttpRequestError, {__class: this});
		};
		
	public function cbPromptNotifyUserOnHttpRequestError(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptNotifyUserOnHttpRequestError(bResult);
		};
		
	//keep the scope so have to pass trought another function to 	
	public function treatUserActionOnPromptNotifyUserOnHttpRequestError(bResult:Boolean):Void{
		//depending on the user selection
		this.__cNotifyMessages.closeWindow();
		this.__cNotifyMessages = null;	
		if(bResult){
			//retry to fetch the data
			cFormManager.fetchData();
		}else{
			//else exit application since we dont have any connection			
			this.exitApplication();
			}
		setLoading(true);	
		};	
	
	/*************************************************************************************************************************************************/		
	
	/*
	public function resetStyle(Void):Void{
		cUni.resetStyle();
		};
	*/	
		
	/*************************************************************************************************************************************************/		
		
	public function resizeForm(Void):Boolean{
		//if to small to show it all
		if(mdm.Forms[BC.__system.__formName].width < BC.__system.__resMax){
			cUni.resetSize(false);
			return false;
			}
		cUni.resetSize(true);
		return true;
		};	
	
	/*************************************************************************************************************************************************/	
		
	public function openDescriptionVocal(arrParam:Array):Void{
		this.__cActionMessages = new CieAudioMessages(arrParam);
		};
		
	/*************************************************************************************************************************************************/	
		
	public function openDescriptionVideo(arrParam:Array):Void{
		//params
		/*
		0 = nopub
		1 = pseudo
		2 = strFlv|strJpg|flagRights
		3 = strTab to the panel instant
		
		so for flash UNI2 client will be 000:
                        first : private
                        second : porn
                        third: abol
		*/

		//get the rihts
		var arrRights = arrParam[2].split('|');
				
		var	isPrivate = arrRights[2].substr(0,1);
		var	isPorn = arrRights[2].substr(1,1);	
		var	isAbo = arrRights[2].substr(2,1);
		
		//check the ritghs for private and porn
		if(isAbo == '1'){ //membership
			this.chatAbonnement();
		}else if(isPrivate == '1'){ //porn
			this.__cActionMessages = new CieActionMessages(gLang[433], gLang[434] + arrParam[1] + gLang[435]);
			this.__cActionMessages.setCallBackFunction(this.cbPromptPrivateVideo, {__class: this, __params:arrParam});
		}else if(isPorn == '1'){ //porn
			this.__cActionMessages = new CieActionMessages(gLang[436], gLang[437]);
			this.__cActionMessages.setCallBackFunction(this.cbPromptPornVideo, {__class: this, __params:arrParam});	
		}else{
			this.__cActionMessages = new CieVideoMessages(arrParam);
			}
		};

		
	/*************************************************************************************************************************************************/	
	//callback when user press button
	public function cbPromptPrivateVideo(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptPrivateVideo(bResult, cbObject.__params);
		};	

	public function treatUserActionOnPromptPrivateVideo(bResult:Boolean, arrParam:Array):Void{
		//depending on the user selection
		//onferme
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;	
		if(bResult){
			//on redirige vers les messages
			this.openTab(arrParam[3].split(','));
			}
		};
		
	/*************************************************************************************************************************************************/	
	//callback when user press button
	public function cbPromptPornVideo(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptPornVideo(bResult, cbObject.__params);
		};	

	public function treatUserActionOnPromptPornVideo(bResult:Boolean, arrParam:Array):Void{
		//depending on the user selection
		//onferme
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		if(bResult){
			//on ouvre
			this.__cActionMessages = new CieVideoMessages(arrParam);
			}
		};
		
		
	/*************************************************************************************************************************************************/	
		
	public function openRecord(Void):Void{
		cUni.resetSize(false);
		this.__cRecordMessages = new CieRecordMessages();
		};		
	
	/*************************************************************************************************************************************************/	
		
	public function openTab(arrParam:Array):Void{
		cContent.openTab(arrParam);
		};
		
	/*************************************************************************************************************************************************/	
		
	public  function changeNodeValue(xmlNode:XMLNode, arrParam:Array):Void{
		cContent.changeNodeValue(xmlNode, arrParam);
		};
		
	/*************************************************************************************************************************************************/	
	/*	
	public function checkIfPanelIDExist(arrPanel:Array, id:Number):Boolean{
		if(cContent.getPanelID(arrPanel) == id){
			return true;
			}
		return false;	
		};
	*/
	/*************************************************************************************************************************************************/
	
	public function getPanelContent(arrPanel:Array):CiePanel{
		return cContent.getPanelClass(arrPanel).getPanelContent();
		};
	
	/*************************************************************************************************************************************************/
	
	public function getPanelObject(arrPanel:Array):Object{
		return cContent.getPanelObject(arrPanel);
		};	
		
	/*************************************************************************************************************************************************/	
		
	public function setPanelContent(arrPanel:Array, strContent:String):CiePanel{
		return cContent.getPanelClass(arrPanel).setContent(strContent);
		};
		
	/*************************************************************************************************************************************************/	
	
	public function setTitleForm(strTitle:String):Void{
		if(strTitle == undefined || strTitle == ''){
			mdm.Forms[BC.__system.__formName].title = "UNI V.2 BETA";
		}else{
			mdm.Forms[BC.__system.__formName].title = gLang[294] + BC.__user.__pseudo + gLang[295];
			}
		};

	/*************************************************************************************************************************************************/	
	
	public function openStatus(Void):Void{
		//TODO
		var arrStatus = [[0, gLang[3]],[1, gLang[4]],[2, gLang[5]],[3, gLang[6]]];
		this.__cActionMessages = new CieOptionMessages(gLang[7], arrStatus, gLang[8]);
		this.__cActionMessages.setSelectionValue(BC.__user.__status);
		this.__cActionMessages.setCallBackFunction(this.cbChangeStatus, {__class: this, __arrState: arrStatus});
		};
	
	public function cbChangeStatus(cbObject:Object):Void{
		if(cbObject.__ok == true){
			cFunc.changeStatus(Number(cbObject.__arrState[cbObject.__class.__cActionMessages.getSelectedChoice()][0]));
			}
		//clear the holder var
		cbObject.__class.__cActionMessages = null;
		};
		
	public function changeStatus(state:Number):Void{
		//if(BC.__user.__status != state){
			//change remote state
			cSockManager.socketSend('STATUS:' + BC.__user.__nopub + ',' + state);
			//change local state
			BC.__user.__status = state;
			cToolManager.getTool('usersections', 'status').changeIcon('mvIconImage_7_' + state);
			//change the checked tray
			for(var i=0; i<4; i++){
				if(i == state){
					cSysTray.checkSysTrayItem('__state_' + i, true);
					cSysTray.changeSysTrayIcon('cie_' + i + '.ico');	
				}else{
					cSysTray.checkSysTrayItem('__state_' + i, false);
					}
				}
			//}
		};
		
	/*************************************************************************************************************************************************/	
	
	public function openLogout(bReconnect:Boolean):Void{
		//set le flag a false car c'est une deconnexion demande par l'usager
		if(!bReconnect){
			BC.__user.__autologin = false;
			}	
		//flag pour eviter les alertes pendant qu'on load le data
		BC.__user.__loaded = false;
		// RESET ALL
		cUni.reset(bReconnect);
		};
	
	/*************************************************************************************************************************************************/
	
	public function openLogin(strError:String, bReconnect:Boolean):Void{	
		//instance
		_global.secLogin = CieLogin.getInstance();
		secLogin.reset();
		//check for autoreconnection when was kicked or relogin
		if(!bReconnect){
			//check if it was memorized
			if(BC.__user.__memorizedpsw || BC.__user.__autologin){
				BC.__user.__pseudo = cRegistry.getKey('pseudo');
				BC.__user.__psw = cRegistry.getKey('password');
				BC.__user.__sessionID = '0';
			}else{
				BC.__user.__pseudo = '';
				BC.__user.__psw = '';
				BC.__user.__sessionID = '0';
				}
			//check f autologin	
			if(BC.__user.__autologin && BC.__user.__pseudo != '' && BC.__user.__psw != ''){
				secLogin.getLogin();
			}else{
				secLogin.openLogin('');
				//cFunc.setSubTabSection('');
				//since there are no way to connect auto then show the login form so bring it back from the systray
				this.appzGetFocus();
				//help button
				cToolManager.createFromXmlFile('login.' + BC.__user.__lang + '.xml');
				}
			//remettre le flag a la valeur du registre car a pu etre modifier avec openLogout
			if(cRegistry.getKey('autologin') == 'true'){
				BC.__user.__autologin = true;
			}else{
				BC.__user.__autologin = false;
				}
			//status
			BC.__user.__status = 0;			
		}else{
			//fetch the session automatically
			secLogin.getLogin();
			}
		};
	
	/*************************************************************************************************************************************************/	

	public function openSalonFromOutside(Void):Void{
		if(!cSockManager.isStackListingTreated()){
			new CieTextMessages('MB_OK', gLang[502], gLang[503]);
			}
		this.openSalon();	
		};
		
	/*************************************************************************************************************************************************/		
	
	public function openSalon(Void):Void{
		if(_global.secSalon == undefined){
			//instance
			_global.secSalon = CieSalon.getInstance();
			//refresh of the section
			secSalon.changeNode();
			secSalon.refreshSection('salon');
			secSalon.refreshCritere('critere');
			//default tab
			secSalon.openSection('salon');
			this.__arrSections['salon'] = cToolManager.getTool('messages', 'salon');
			//pub only for regular
			if(BC.__user.__membership != '2'){	
				//cSectionManager.getPanelManager('salon').removePubPanel();	
				}	
		}else{
			cSectionManager.setTabFocus('salon');
			}
		this.changeToolSize('salon');		
		};
	
	/*************************************************************************************************************************************************/	

	public function openAide(Void):Void{
		if(_global.secAide == undefined){
			//instance
			_global.secAide = CieAide.getInstance();
			//refresh of the section
			secAide.changeNode();
			secAide.refreshSection('apropos');
			secAide.refreshSection('faq');
			secAide.refreshSection('termes_conditions');
			//default tab
			secAide.openSection('apropos');
			this.__arrSections['aide'] = cToolManager.getTool('messages', 'aide');
		}else{
			cSectionManager.setTabFocus('aide');
			}
		this.changeToolSize('aide');	
		};
	
	/*************************************************************************************************************************************************/	
	
	public function openOptions(strSub:String):Void{
	
		if(_global.secOptions == undefined){
			//instance
			_global.secOptions = CieOptions.getInstance();
			//refresh of the section
			secOptions.changeNode();
			secOptions.refreshSection('mes_alertes');
			secOptions.refreshSection('mon_profil');
			secOptions.refreshSection('preferences');
			secOptions.refreshSection('abonnement');
			//default tab
			if(strSub == undefined || strSub == ''){
				strSub = 'mon_profil'; //sub section par defaut
				}
			secOptions.openSection(strSub);
			this.__arrSections['options'] = cToolManager.getTool('messages', 'options');
		}else{
			cSectionManager.setTabFocus('options');
			if(strSub != undefined && strSub != ''){
				secOptions.openSection(strSub);
				}
			}
		this.changeToolSize('options');	
		};	
		
	/*************************************************************************************************************************************************/	
	
	public function openWelcome(Void):Void{
		if(_global.secWelcome == undefined){
			//instance
			_global.secWelcome = CieWelcome.getInstance();
			//secWelcome.reset();
			secWelcome.openWelcome();
			this.__arrSections['welcome'] = cToolManager.getTool('usersections', 'welcome');
		}else{
			cSectionManager.setTabFocus('welcome');
			secWelcome.refreshWelcome();
			}
		this.changeToolSize('welcome');
		};
	
	/*************************************************************************************************************************************************/	

	public function openDirectPseudoRecherche(strPseudo:String):Void{
		this.openRecherche('pseudo', strPseudo);
		};
	
	/*************************************************************************************************************************************************/	

	public function openRecherche(strSub:String, strDirectPseudoSearch:String):Void{
		if(_global.secRecherche == undefined){
			//instance
			_global.secRecherche = CieRecherche.getInstance();
			//sequence
			secRecherche.removeRegisteredObject();
			secRecherche.changeNode();
			//refresh of the section
			if(strDirectPseudoSearch != undefined && strDirectPseudoSearch != ''){	//specifique a la recherche direct sans passe par le formulaire et le send
				secRecherche.refreshDirectPseudoSection();
			}else{	
				secRecherche.refreshSection('pseudo');
				}
			secRecherche.refreshSection('rapide');
			//secRecherche.refreshSection('detaillees'); //will load when tab has the focus
			//default tab
			if(strSub == undefined || strSub == ''){
				strSub = 'pseudo'; //sub section par defaut
				}
			secRecherche.openSection(strSub);
			this.__arrSections['recherche'] = cToolManager.getTool('messages', 'recherche');
			//pub only for regular
			/*
			if(BC.__user.__membership != '2'){	
				cSectionManager.getPanelManager('recherche').removePubPanel();	
				}
			*/	
		}else{
			cSectionManager.setTabFocus('recherche');
			if(strSub != undefined && strSub != ''){
				secRecherche.openSection(strSub);
				}
			}
			
		//direct serach
		if(strDirectPseudoSearch != undefined && strDirectPseudoSearch != '' && strSub == 'pseudo'){	
			secRecherche.directPseudoSearch(strDirectPseudoSearch);
			}
		
		this.changeToolSize('recherche');	
		};	
	
	/*************************************************************************************************************************************************/	
		
	public function openBottin(Void):Void{
		if(_global.secBottin == undefined){
			//instance
			_global.secBottin = CieBottin.getInstance();
			//sequence
			secBottin.removeRegisteredObject();
			secBottin.changeNode();
			//refresh of the section
			secBottin.refreshSection('carnet');
			secBottin.refreshSection('listenoire');
			//default tab
			secBottin.openSection('carnet');
			this.__arrSections['bottin'] = cToolManager.getTool('messages', 'bottin');
			//pub only for regular
			if(BC.__user.__membership != '2'){	
				//cSectionManager.getPanelManager('bottin').removePubPanel();	
				}		
		}else{
			cSectionManager.setTabFocus('bottin');
			}
		this.changeToolSize('bottin');	
		};
		
	/*************************************************************************************************************************************************/	
		
	public function openMessage(strSub):Void{
		if(_global.secMessage == undefined){
			//instance
			_global.secMessage = CieMessage.getInstance();
			//sequence
			secMessage.removeRegisteredObject();
			secMessage.changeNode();
			//refresh of the section
			secMessage.refreshSection('quiaconsulte');
			secMessage.refreshSection('communications');
			//default tab
			if(strSub == undefined || strSub == ''){
				strSub = 'communications'; //sub section par defaut
				}
			secMessage.openSection(strSub);	
			this.__arrSections['message'] = cToolManager.getTool('messages', 'message');
			//pub only for regular
			if(BC.__user.__membership != '2'){	
				//cSectionManager.getPanelManager('message').removePubPanel();	
				}
		}else{
			cSectionManager.setTabFocus('message');
			if(strSub != undefined && strSub != ''){
				secMessage.openSection(strSub);
				}
			}
		//onremet l'icone a son etat precedent
		cSysTray.changeSysTrayIcon('cie_' + BC.__user.__status + '.ico');	
		this.changeToolSize('message');	
		};
		
	/*************************************************************************************************************************************************/	

	private function changeToolSize(strSection:String):Void{
		for(var o in this.__arrSections){
			if(o == strSection){
				if(this.__lastSection != o){
					this.__lastSection = o;
					//if(this.__lastSection != 'welcome'){ //pas besoin de focus pour lui
						this.__arrSections[o].changeSize(CieStyle.__toolbar.__toolMax);
					//	}
					}
			}else{
				this.__arrSections[o].changeSize(CieStyle.__toolbar.__toolMin);
				}
			}
		cToolManager.redraw('messages');
		cToolManager.redraw('usersections');
		};
		
	/*************************************************************************************************************************************************/	
	
	//reception de la room dans laquelle ont peut aller se connecter
	public function openVideoChatRoom(pseudo:String, room:String, code:String):Void{
		
		//prepare a local connection with the chat win
		this.__chatWinCounter++;
		var strConn:String = 'CHATWIN_SLAVE_' + this.__chatWinCounter;
		
		//add a remote socket opened
		cLocalConn.addRemoteSocket(strConn);
		//put the chatWin in a stack to distribute msg on receive later
		this.__chatWindow[pseudo] = new Object();
		this.__chatWindow[pseudo].__lcName = strConn;
		this.__chatWindow[pseudo].__active = true;	
	
		var arrRows:Array = cDbManager.selectDB('SELECT members.no_publique, members.age, members.code_pays, members.region_id, members.ville_id, members.titre, members.sexe, members.orientation, members.etat_civil, members.photo FROM members WHERE members.pseudo="' + pseudo + '";');
				
		var xmlInfos:String = '';
		xmlInfos += '<CHATCONFIG>\n';
		xmlInfos += '\t<C n="__user">\n';
		if(arrRows[0][9] == '2'){
			xmlInfos += '\t\t<C n="__photo" type="String">' + escape(BC.__server.__thumbs + arrRows[0][0].substr(0,2) + "/" + pseudo + ".jpg") + '</C>\n';
			}
		xmlInfos += '\t\t<C n="__lang" type="String">' + BC.__user.__lang + '</C>\n';
		xmlInfos += '\t\t<C n="__debug" type="Boolean">' + BC.__user.__debug + '</C>\n';
		//file debugger
		if(BC.__user.__debugfile != undefined && BC.__user.__debugfile != ''){
			xmlInfos += '\t\t<C n="__debugfile" type="String">' + BC.__user.__debugfile + '</C>\n';
			}
		xmlInfos += '\t\t<C n="__style" type="String">' + BC.__user.__styleColor + 'chat.style.xml</C>\n';
		xmlInfos += '\t\t<C n="__pseudo">' + pseudo + '</C>\n';
		xmlInfos += '\t\t<C n="__localpseudo">' + BC.__user.__pseudo + '</C>\n';
		xmlInfos += '\t\t<C n="__photosexe">' + arrRows[0][6] + '</C>\n';
		xmlInfos += '\t\t<C n="__sexe">' + escape(cFormManager.__obj['sexe'][arrRows[0][6]][1]) + '</C>\n';
		xmlInfos += '\t\t<C n="__orientation">' + escape(cFormManager.__obj['orientation'][arrRows[0][7]][1]) + '</C>\n';
		xmlInfos += '\t\t<C n="__etatcivil">' + escape(cFormManager.__obj['etatcivil'][arrRows[0][8]][1]) + '</C>\n';
				
		
		xmlInfos += '\t\t<C n="__no_publique">' + arrRows[0][0] + '</C>\n';
		xmlInfos += '\t\t<C n="__age">' + arrRows[0][1] + '</C>\n';
		
		if(gGeo[arrRows[0][2] + '_' + arrRows[0][3] + '_' + arrRows[0][4]] != undefined){
			xmlInfos += '\t\t<C n="__location">' + escape(gGeo[arrRows[0][2] + '_' + arrRows[0][3] + '_' + arrRows[0][4]]) + '</C>\n';
		}else{
			xmlInfos += '\t\t<C n="__location">...</C>\n';
			}

		xmlInfos += '\t\t<C n="__titre">' + arrRows[0][5] + '</C>\n';
		xmlInfos += '\t\t<C n="__language">' + BC.__user.__lang + '</C>\n';
		xmlInfos += '\t\t<C n="__application">' + room + '</C>\n';
		xmlInfos += '\t\t<C n="__accesscode">' + code + '</C>\n';
		xmlInfos += '\t\t<C n="__remoteconn">WIN_MASTER</C>\n';
		xmlInfos += '\t\t<C n="__localconn">' + strConn + '</C>\n';
		if(room == '0'){
			xmlInfos += '\t\t<C n="__type">UNI</C>\n';
		}else{
			xmlInfos += '\t\t<C n="__type">FMS</C>\n';
			}
		xmlInfos += '\t\t<C n="__ip">' + BC.__server.__ipChat + '</C>\n';
		xmlInfos += '\t\t<C n="__port">' + BC.__server.__portChat + '</C>\n';
		xmlInfos += '\t</C>\n';
		xmlInfos += '\t<C n="__system">\n';
		xmlInfos += '\t\t<C n="__resolutionX" type="Number">0</C>\n';
		xmlInfos += '\t\t<C n="__resolutionY" type="Number">0</C>\n';
		xmlInfos += '\t\t<C n="__resMin" type="Number">' + BC.__system.__resChatMin + '</C>\n';
		xmlInfos += '\t\t<C n="__resMinH" type="Number">' + BC.__system.__resChatMinH + '</C>\n';
		xmlInfos += '\t\t<C n="__formName" type="String">' + BC.__system.__formChatName + '</C>\n';
		xmlInfos += '\t</C>\n';
		xmlInfos += '</CHATCONFIG>';
		
		mdm.FileSystem.saveFile(mdm.Application.path + 'chat.config.xml', xmlInfos); //the config file
		
		//Debug('-----------------XML_INFOS-------------------\n\n' + xmlInfos + '\n\n');
				
		//the content file tell wheter have 2 or 1 frame
		xmlInfos = '';
		xmlInfos += '<CHATCONTENT>\n';
		xmlInfos += '\t<S n="fms" model="un">\n';
		xmlInfos += '\t\t<P n="_tl" content="mvProfilDetails" bgcolor="' + CieStyle.__profil.__bgTopColor + '" scroll="false">\n';
		if(room != '0'){
			xmlInfos += '\t\t\t<PT n="chat" model="trois" ystart="90" title="' + 'Chat' + '" closebutt="false">\n';
		}else{
			xmlInfos += '\t\t\t<PT n="chat" model="deux_V" ystart="90" title="' + 'Chat' + '" closebutt="false">\n';
			}
		xmlInfos += '\t\t\t\t<P n="_tl" content="mvContent" bgcolor="0xffffff" scroll="false"></P>\n';
		xmlInfos += '\t\t\t\t<P n="_bl" content="mvContent" bgcolor="0xffffff" scroll="false"></P>\n';
		if(room != '0'){
			xmlInfos += '\t\t\t\t<P n="_tr" content="mvContent" bgcolor="0x000000" scroll="false" effect="false"></P>\n';
			}
		xmlInfos += '\t\t\t</PT>\n';
		xmlInfos += '\t\t</P>\n';
		xmlInfos += '\t</S>\n';
		xmlInfos += '</CHATCONTENT>';
				
		mdm.FileSystem.saveFile(mdm.Application.path + 'chat.content.xml', xmlInfos); //the config file
		
		//Debug('-----------------XML_CONTENT-------------------\n\n' + xmlInfos + '\n\n');
		
		//mdm.System.exec(mdm.Application.path + 'Chat.exe');
		mdm.System.exec(mdm.Application.path + 'cuni.exe');
		
		//Debug('-----------------START CHAT (supposition)-------------------\n\n');
		
		};
	
	/*************************************************************************************************************************************************/
	
	//la de mande de chat a ete refuse
	public function refuseVideoChat(pseudo:String):Void{
		//prompt comme quoi la demande est refuse
		new CieTextMessages('MB_OK', pseudo + gLang[9], gLang[10]);
		};
		
	//la de mande de chat a ete refuse
	public function timeoutVideoChat(pseudo:String):Void{
		//prompt comme quoi la demande est refuse car l'usager n'y a pas repondu
		new CieTextMessages('MB_OK', pseudo + gLang[700], gLang[10]);
		};	
	
	/*************************************************************************************************************************************************/
	
	//prompt que quelque'un nous fait une demande de chat
	public function askingForVideoChat(pseudo:String, nopub:String):Void{
		//check if we have a chat opened already
		if(!this.checkIfChatIsOpened(pseudo)){
			//ok we need the info of this user so do a request first
			var arrRows:Array = cDbManager.selectDB('SELECT members.no_publique, members.age, members.code_pays, members.region_id, members.ville_id, members.photo, members.membership, members.orientation, members.sexe, members.titre, members.relation, members.etat_civil FROM members WHERE members.pseudo = "' + pseudo + '";');
			if(arrRows.length > 0){
				//set le systray comme ayant une demande de chat
				cSysTray.changeSysTrayIcon('cie_chat.ico');
				//location
				var strLocation:String = '...';
				if(gGeo[arrRows[0][2] + '_' + arrRows[0][3] + '_' + arrRows[0][4]] != undefined){
					strLocation = escape(gGeo[arrRows[0][2] + '_' + arrRows[0][3] + '_' + arrRows[0][4]]);
					}
				var strProfil:String = arrRows[0][0] + '|' + pseudo + '|' + arrRows[0][1] + '|' + strLocation + '|' + arrRows[0][5] + '|' + arrRows[0][6] + '|' + arrRows[0][7] + '|' + arrRows[0][8] + '|' + arrRows[0][9] + '|' + arrRows[0][10] + '|' + arrRows[0][11];
								
				this.__cChatMessages = new CieChatMessages(gLang[11], gLang[12], strProfil);
				this.__cChatMessages.setCallBackFunction(this.cbPromptVideoChatIn, {__class:this, __pseudo:pseudo});
				//send an window alert
				if(BC.__user.__showAlert){
					cFunc.updateAlertWindow(nopub, 'newchat');
					}
			}else{//we didn't find anyone so refuse it
				//onremet l'icone a son etat precedent
				cSysTray.changeSysTrayIcon('cie_' + BC.__user.__status + '.ico');
				//refused automatic
				//cSockManager.socketSend('AUTHN:' + pseudo);
				Debug('NEWCHAT: NOPUB NOT THERE');
				//call the server to get it
				cSockManager.registerForUserInfosCallBack(this.cbUserInfos, {__class:this,__args:{ __cmd:'CHAT', __pseudo:pseudo, __nopub:nopub}}, nopub);
				}
		}else{
			Debug('CHAT ALREADY OPENDED WITH ' + pseudo);
			//onremet l'icone a son etat precedent
			cSysTray.changeSysTrayIcon('cie_' + BC.__user.__status + '.ico');
			//refused automatic
			cSockManager.socketSend('AUTHN:' + pseudo);
			}
		};
		
	//callback when user press button	
	public function cbPromptVideoChatIn(cbObject:Object, bResult:Boolean, bAutoDecline:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptVideoChatIn(bResult, cbObject.__pseudo, bAutoDecline);
		};	
		
	//keep the scope so have to pass trought another function to 	
	public function treatUserActionOnPromptVideoChatIn(bResult:Boolean, pseudo:String, bAutoDecline:Boolean):Void{
		//depending on the user selection
		if(bResult){
			//close the prompt
			this.__cChatMessages.closeWindow();
			//the user said ok to the chat
			cSockManager.socketSend('AUTHY:' + pseudo);
		}else{
			//user have pressed cancel thrn close the window
			this.__cChatMessages.closeWindow();
			//the user refuse the chat (auto or not)
			if(bAutoDecline == true){
				cSockManager.socketSend('AUTHA:' + pseudo);
			}else{
				cSockManager.socketSend('AUTHN:' + pseudo);
				}
			}
		//onremet l'icone a son etat precedent
		cSysTray.changeSysTrayIcon('cie_' + BC.__user.__status + '.ico');	
		//reset la var
		this.__cChatMessages = null;
		};		
	
	/*************************************************************************************************************************************************/	
	
	//demande de chat video, oUser contient le pseudo et le no_publique a qui ont fait la demande
	public function askForVideoChat(oUser:Object):Void{
		//check si on a deja un chat d'ouvert avec cette personne
		if(!this.checkIfChatIsOpened(oUser.__pseudo)){
			//prompt the question 
			this.__cActionMessages = new CieActionMessages(gLang[13], gLang[14] + oUser.__pseudo);
			this.__cActionMessages.setCallBackFunction(this.cbPromptVideoChatOut, {__class: this, __objUser:oUser});
		}else{
			//avertir l'usager
			new CieTextMessages('MB_OK', gLang[15] + oUser.__pseudo, gLang[16]);
			}
		};
	
	/*************************************************************************************************************************************************/
	
	//callback when user press button
	public function cbPromptVideoChatOut(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptVideoChatOut(bResult, cbObject.__objUser);
		};
		
	/*************************************************************************************************************************************************/	
	
	//keep the scope so have to pass trought another function to 	
	public function treatUserActionOnPromptVideoChatOut(bResult:Boolean, oUser:Object):Void{
		//depending on the user selection
		if(bResult){
			//close the prompt
			this.__cActionMessages.closeWindow();
			//ask the ither user via the socket manager
			cSockManager.socketSend('AUTH:' + oUser.__pseudo);
		}else{
			//user have pressed cancel thrn close the window
			this.__cActionMessages.closeWindow();
			}
		this.__cActionMessages = null;	
		};
	
	/*************************************************************************************************************************************************/	
	
	public function chatRequestSentOk(pseudo:String):Void{
		//prompt the user that his chat request was sent OK to strData
		new CieTextMessages('MB_OK', gLang[17] + pseudo + gLang[18], gLang[19]);
		};
	
	/*****ADD TO CARNET********************************************************************************************************************************************/	
	
	//rajout au carnet
	public function addToCarnet(arrUser:Array):Void{
		this.__cActionMessages = new CieActionMessages(gLang[20], gLang[21] + arrUser['pseudo'] + gLang[22]);
		this.__cActionMessages.setCallBackFunction(this.cbPromptAddToCarnet, {__class: this, __user:arrUser});
		};
	
	//callback when user press button pour ajour acrnet
	public function cbPromptAddToCarnet(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptAddToCarnet(bResult, cbObject.__user);
		};	
	
	//rajout au carnet
	public function treatUserActionOnPromptAddToCarnet(bResult:Boolean, arrUser:Array):Void{
	//depending on the user selection
		if(bResult){
			//do a request to add to the carnet
			var arrD = new Array();
			arrD['methode'] = 'carnet';
			arrD['action'] = 'add';
			arrD['arguments'] = arrUser['no_publique'];
			//add the request
			cReqManager.addRequest(arrD, this.cbAddToCarnet, {__class:this, __user:arrUser});	
			//put a loader
			this.__cActionMessages.setLoading();
		}else{
			//user have pressed cancel thrn close the window
			this.__cActionMessages.closeWindow();
			this.__cActionMessages = null;	
			}
		};
		
	public function cbAddToCarnet(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlAddToCarnet(obj.__req.getXml().firstChild, obj.__super.__user);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	public function parseXmlAddToCarnet(xmlNode:XMLNode, arrUser:Array):Void{
		var str:String = xmlNode.childNodes[0].firstChild.nodeValue;
		var strErrAttr:String = xmlNode.childNodes[0].attributes.n;
		var strError:String = '';
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		if(str != undefined){
			if(strErrAttr == 'error'){
				if(str == 'ALREADY_IN_CARNET'){
					strError = arrUser['pseudo'] + gLang[23];
				}else if(str == 'CARNET_LIMIT_REACH'){
					strError = gLang[24];
				}else{
					Debug('***ERR_ADD_CARNET: ' + str);	
					strError = gLang[25] + arrUser['pseudo'] + gLang[26];
					}
				}	
		}else{
			strError = gLang[25] + arrUser['pseudo'] + gLang[26];
			}
		
		if(strError != ''){
			new CieTextMessages('MB_OK', strError, gLang[27]);
		}else{
			//add to the global var holding the no_pub of the carnet
			gDbCarnet[arrUser['no_publique']] = 1;
			//if no error add to the local DB
			//if(cDbManager.dbKeyExist('no_publique', arrUser['no_publique'])){
			if(gDbKey[arrUser['no_publique']] != undefined){
				cDbManager.queryDB("UPDATE members SET msg_carnet = '1', msg_listenoire = '0' WHERE no_publique = " + arrUser['no_publique'] + ";");
			}else{
				cDbManager.queryDB("INSERT INTO members (no_publique, pseudo, age, ville_id, region_id, code_pays, album, photo, vocal, membership, orientation, sexe, titre, relation, etat_civil, msg_carnet) VALUES ('" + arrUser['no_publique'] + "','" + arrUser['pseudo'] + "','" + arrUser['age'] + "','" + arrUser['ville_id'] + "','" + arrUser['region_id'] + "','" + arrUser['code_pays'] + "','" + arrUser['album'] + "','" + arrUser['photo'] + "','" + arrUser['vocal'] + "','" + arrUser['membership'] + "','" + arrUser['orientation'] + "','" + arrUser['sexe'] + "','" + arrUser['titre'] + "','" + arrUser['relation'] + "','" + arrUser['etat_civil'] + "', '1');");
				}
			//and refresh the carnet if it's alredy loaded in a section secBottin
			if(secBottin != undefined){
				secBottin.refreshSection('listenoire');
				secBottin.refreshSection('carnet');
				}
			}
		};
	

	/*********ADD TO LISTENOIRE*****************************************************************************************************************/	
	
	//rajout au carnet
	public function addToListeNoire(arrUser:Array):Void{
		this.__cActionMessages = new CieActionMessages(gLang[28], gLang[29] + arrUser['pseudo'] + gLang[30]);
		this.__cActionMessages.setCallBackFunction(this.cbPromptAddToListeNoire, {__class: this, __user:arrUser});
		};
	
	//callback when user press button pour ajour acrnet
	public function cbPromptAddToListeNoire(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptAddToListeNoire(bResult, cbObject.__user);
		};	
	
	//rajout au carnet
	public function treatUserActionOnPromptAddToListeNoire(bResult:Boolean, arrUser:Array):Void{
	//depending on the user selection
		if(bResult){
			//do a request to add to the carnet
			var arrD = new Array();
			arrD['methode'] = 'listenoire';
			arrD['action'] = 'add';
			arrD['arguments'] = arrUser['no_publique'];
			//add the request
			cReqManager.addRequest(arrD, this.cbAddToListeNoire, {__class:this, __user:arrUser});	
			//put a loader
			this.__cActionMessages.setLoading();
		}else{
			//user have pressed cancel thrn close the window
			this.__cActionMessages.closeWindow();
			this.__cActionMessages = null;	
			}
		};
		
	public function cbAddToListeNoire(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlAddToListeNoire(obj.__req.getXml().firstChild, obj.__super.__user);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	public function parseXmlAddToListeNoire(xmlNode:XMLNode, arrUser:Array):Void{
		var str:String = xmlNode.childNodes[0].firstChild.nodeValue;
		var strErrAttr:String = xmlNode.childNodes[0].attributes.n;
		var strError:String = '';
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		if(str != undefined){
			if(strErrAttr == 'error'){
				if(str == 'ALREADY_IN_CARNET'){
					strError = arrUser['pseudo'] + gLang[31];
				}else if(str == 'CARNET_LIMIT_REACH'){
					strError = gLang[32];
				}else{
					Debug('***ERR_SEND_EXPRESS: ' + str);
					strError = gLang[33] + arrUser['pseudo'] + gLang[34];
					}
				}	
		}else{
			strError = gLang[33] + arrUser['pseudo'] + gLang[34];
			}
		
		if(strError != ''){
			new CieTextMessages('MB_OK', strError, gLang[35]);
		}else{
			//remove from global var if it was there
			delete gDbCarnet[arrUser['no_publique']];
			//if no error add to the local DB
			if(gDbKey[arrUser['no_publique']] != undefined){
			//if(cDbManager.dbKeyExist('no_publique', arrUser['no_publique'])){
				cDbManager.queryDB("UPDATE members SET msg_listenoire = '1', msg_carnet = '0' WHERE no_publique = " + arrUser['no_publique'] + ";");
			}else{
				cDbManager.queryDB("INSERT INTO members (no_publique, pseudo, age, ville_id, region_id, code_pays, album, photo, vocal, membership, orientation, sexe, titre, relation, etat_civil, msg_listenoire) VALUES ('" + arrUser['no_publique'] + "','" + arrUser['pseudo'] + "','" + arrUser['age'] + "','" + arrUser['ville_id'] + "','" + arrUser['region_id'] + "','" + arrUser['code_pays'] + "','" + arrUser['album'] + "','" + arrUser['photo'] + "','" + arrUser['vocal'] + "','" + arrUser['membership'] + "','" + arrUser['orientation'] + "','" + arrUser['sexe'] + "','" + arrUser['titre'] + "','" + arrUser['relation'] + "','" + arrUser['etat_civil'] + "', '1');");
				}
			//and refresh the liste noire if it's alredy loaded in a section secBottin
			if(secBottin != undefined){
				secBottin.refreshSection('listenoire');
				secBottin.refreshSection('carnet');
				}
			}
		};
	
	
	/*********REMOVE FROM CARNET****************************************************************************************************************************************/	
	
	//remove from carnet
	public function removeFromCarnet(arrUsers:Array):Void{
		if(arrUsers.length > 0){
			//this.__cActionMessages = new CieActionMessages(gLang[36], gLang[37] + arrUsers.length + gLang[38]);
			this.__cActionMessages = new CieActionMessages(gLang[36], gLang[37] + ' ' + gLang[38]);
			this.__cActionMessages.setCallBackFunction(this.cbPromptRemoveFromCarnet, {__class: this, __users:arrUsers});
		}else{
			new CieTextMessages('MB_OK', gLang[39], gLang[40]);
			}
		};
	
	//callback when user press button pour remove acrnet
	public function cbPromptRemoveFromCarnet(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptRemoveFromCarnet(bResult, cbObject.__users);
		};	
	
	//remove from carnet
	public function treatUserActionOnPromptRemoveFromCarnet(bResult:Boolean, arrUser:Array):Void{
	//depending on the user selection
		if(bResult){
			//do a request to remove from the carnet
			var arrD = new Array();
			arrD['methode'] = 'carnet';
			arrD['action'] = 'remove';
			arrD['arguments'] = arrUser.toString();
			//add the request
			cReqManager.addRequest(arrD, this.cbRemoveFromCarnet, {__class:this, __users:arrUser});	
			//put a loader
			this.__cActionMessages.setLoading();
		}else{
			//user have pressed cancel thrn close the window
			this.__cActionMessages.closeWindow();
			this.__cActionMessages = null;	
			}
		};
		
	public function cbRemoveFromCarnet(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlRemoveFromCarnet(obj.__req.getXml().firstChild, obj.__super.__users);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	public function parseXmlRemoveFromCarnet(xmlNode:XMLNode, arrUsers:Array):Void{
		var str:String = xmlNode.childNodes[0].firstChild.nodeValue;
		var strErrAttr:String = xmlNode.childNodes[0].attributes.n;
		var strError:String = '';
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		if(str == undefined){
			strError = gLang[41];
		}else{
			if(strErrAttr == 'error'){
				Debug("***ERR_REMOVE_FROM_CARNET");
				strError = gLang[41];
				}
			}
		if(strError != ''){
			new CieTextMessages('MB_OK', strError, gLang[42]);
		}else{
			//if no error change flag from BD
			var strWhere:String = '';
			for(var o in arrUsers){
				//remove from global var if it was there
				delete gDbCarnet[arrUsers[o]];
				strWhere += " no_publique = " + arrUsers[o] + " OR";
				}
			strWhere = "UPDATE members SET msg_carnet = '0' WHERE " + strWhere.substring(0, (strWhere.length - 2)) + ";";	
			cDbManager.queryDB(strWhere);	
			//and refresh the carnet if it's alredy loaded in a section secBottin
			if(secBottin != undefined){
				secBottin.refreshSection('carnet');
				}
			}
		};
	
	
	/*********REMOVE FROM LISTE NOIRE*************************************************************************************************/	
	
	//remove from carnet
	public function removeFromListeNoire(arrUsers:Array):Void{
		if(arrUsers.length > 0){
			//this.__cActionMessages = new CieActionMessages(gLang[43], gLang[44] + arrUsers.length + gLang[45]);
			this.__cActionMessages = new CieActionMessages(gLang[43], gLang[44] + gLang[45]);
			this.__cActionMessages.setCallBackFunction(this.cbPromptRemoveFromListeNoire, {__class: this, __users:arrUsers});
		}else{
			new CieTextMessages('MB_OK', gLang[46], gLang[47]);
			}
		};
	
	//callback when user press button pour remove acrnet
	public function cbPromptRemoveFromListeNoire(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptRemoveFromListeNoire(bResult, cbObject.__users);
		};	
	
	//remove from carnet
	public function treatUserActionOnPromptRemoveFromListeNoire(bResult:Boolean, arrUser:Array):Void{
	//depending on the user selection
		if(bResult){
			//do a request to remove from the carnet
			var arrD = new Array();
			arrD['methode'] = 'listenoire';
			arrD['action'] = 'remove';
			arrD['arguments'] = arrUser.toString();
			//add the request
			cReqManager.addRequest(arrD, this.cbRemoveFromListeNoire, {__class:this, __users:arrUser});	
			//put a loader
			this.__cActionMessages.setLoading();
		}else{
			//user have pressed cancel thrn close the window
			this.__cActionMessages.closeWindow();
			this.__cActionMessages = null;	
			}
		};
		
	public function cbRemoveFromListeNoire(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlRemoveFromListeNoire(obj.__req.getXml().firstChild, obj.__super.__users);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	public function parseXmlRemoveFromListeNoire(xmlNode:XMLNode, arrUsers:Array):Void{
		var str:String = xmlNode.childNodes[0].firstChild.nodeValue;
		var strErrAttr:String = xmlNode.childNodes[0].attributes.n;
		var strError:String = '';
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		if(str == undefined){
			strError = gLang[48];
		}else{
			if(strErrAttr == 'error'){
				Debug("***ERR_REMOVE_FROM_BLOCKED");
				strError = gLang[48];
				}
			}
		if(strError != ''){
			new CieTextMessages('MB_OK', strError, gLang[49]);
		}else{
			//if no error change flag from BD
			var strWhere:String = '';
			for(var o in arrUsers){
				strWhere += " no_publique = " + arrUsers[o] + " OR";
				}
			strWhere = "UPDATE members SET msg_listenoire = '0' WHERE " + strWhere.substring(0, (strWhere.length - 2)) + ";";	
			cDbManager.queryDB(strWhere);	
			//and refresh the carnet if it's alredy loaded in a section secBottin
			if(secBottin != undefined){
				secBottin.refreshSection('listenoire');
				}
			}
		};
	
	/*********REMOVE ALL USER MESSAGES***************************************************************************************************************/	
	
	//remove from carnet
	public function removeAllUserMessages(arrUsers:Array):Void{
		if(arrUsers.length > 0){
			//this.__cActionMessages = new CieActionMessages(gLang[50], gLang[51] + arrUsers.length + gLang[52]);
			this.__cActionMessages = new CieActionMessages(gLang[50], gLang[51] + ' ' + gLang[52]);
			this.__cActionMessages.setCallBackFunction(this.cbPromptRemoveAllUserMessages, {__class: this, __users:arrUsers});
		}else{
			new CieTextMessages('MB_OK', gLang[53], gLang[54]);
			}
		};
	
	//callback when user press button pour remove acrnet
	public function cbPromptRemoveAllUserMessages(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptAllUserMessages(bResult, cbObject.__users);
		};	
	
	//remove from carnet
	public function treatUserActionOnPromptAllUserMessages(bResult:Boolean, arrUser:Array):Void{
	//depending on the user selection
		if(bResult){
			//do a request to remove from the carnet
			var arrD = new Array();
			arrD['methode'] = 'messages';
			arrD['action'] = 'remove';
			arrD['arguments'] = arrUser.toString();
			//add the request
			cReqManager.addRequest(arrD, this.cbRemoveAllUserMessages, {__class:this, __users:arrUser});	
			//put a loader
			this.__cActionMessages.setLoading();
		}else{
			//user have pressed cancel thrn close the window
			this.__cActionMessages.closeWindow();
			this.__cActionMessages = null;	
			}
		};
		
	public function cbRemoveAllUserMessages(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlRemoveAllUserMessages(obj.__req.getXml().firstChild, obj.__super.__users);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	public function parseXmlRemoveAllUserMessages(xmlNode:XMLNode, arrUsers:Array):Void{
		var str:String = xmlNode.childNodes[0].firstChild.nodeValue;
		var strErrAttr:String = xmlNode.childNodes[0].attributes.n;
		var strError:String = '';
		//Debug("REMOVE_FROM_MESSAGES: [" + arrUsers.toString() + "] " + str);
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		if(str == undefined){
			strError = gLang[55];
		}else{
			if(strErrAttr == 'error'){
				Debug("***ERR_REMOVE_ALL_USER_MSG");
				strError = gLang[48];
				}
			}
		if(strError != ''){
			new CieTextMessages('MB_OK', strError, gLang[56]);
		}else{
			//if no error change flag from BD
			var strWhere:String = '';
			for(var o in arrUsers){
				//minor check
				if(arrUsers[o] != undefined){
					strWhere += " no_publique = " + arrUsers[o] + " OR";
					}
				}
			//minor check	
			if(strWhere.length > 0){	
				strWhere = strWhere.substring(0, (strWhere.length - 2)) + ";";	
				//change flag from all type of messages
				cDbManager.queryDB("UPDATE members SET msg_express = '0', msg_instant = '0', msg_courrier = '0', msg_vocal = '0', msg_express_date = '0' WHERE " + strWhere);	
				//delete all instant from those users
				cDbManager.queryDB("DELETE * FROM instant WHERE " + strWhere);	
				//delete all courrier from those users
				cDbManager.queryDB("DELETE * FROM courrier WHERE " + strWhere);	
				//delete all vocal from those users
				cDbManager.queryDB("DELETE * FROM vocal WHERE " + strWhere);				
				//and refresh thecommunicationsif it's alredy loaded in a section secMessage
				if(secMessage != undefined){
					secMessage.refreshSection('communications');
					}
				}	
			}
		};
	

	/*********QUI A CONSULTE********************************************************************************************************/
	//user message
	public function removeAllUserFromQuiAConsulte(Void):Void{
		this.__cActionMessages = new CieActionMessages(gLang[57], gLang[58]);
		this.__cActionMessages.setCallBackFunction(this.cbPromptRemoveAllUserFromQuiAConsulte, {__class: this});
		};
		
	//callback when user press button pour remove acrnet
	public function cbPromptRemoveAllUserFromQuiAConsulte(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptAllUserFromQuiAConsulte(bResult);
		};	

	//remove from quiaconsulte
	public function treatUserActionOnPromptAllUserFromQuiAConsulte(bResult:Boolean):Void{
	//depending on the user selection
		if(bResult){
			//do a request to remove from the carnet
			var arrD = new Array();
			arrD['methode'] = 'quiaconsulte';
			arrD['action'] = 'remove';
			arrD['arguments'] = '';
			//add the request
			cReqManager.addRequest(arrD, this.cbRemoveAllUserFromQuiAConsulte, {__class:this});	
			//put a loader
			this.__cActionMessages.setLoading();
		}else{
			//user have pressed cancel thrn close the window
			this.__cActionMessages.closeWindow();
			this.__cActionMessages = null;	
			}
		};	
	
	public function cbRemoveAllUserFromQuiAConsulte(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlRemoveAllUserFromQuiAConsulte(obj.__req.getXml().firstChild);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	public function parseXmlRemoveAllUserFromQuiAConsulte(xmlNode:XMLNode):Void{
		var str:String = xmlNode.childNodes[0].firstChild.nodeValue;
		var strErrAttr:String = xmlNode.childNodes[0].attributes.n;
		var strError:String = '';
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		if(str == undefined){
			strError = gLang[59];
		}else{
			if(strErrAttr == 'error'){
				Debug("***ERR_REMOVE_ALL_QUIACONSULTE");
				strError = gLang[59];
				}
			}
		if(strError != ''){
			new CieTextMessages('MB_OK', strError, gLang[60]);
		}else{
			//if no error change flag from BD
			cDbManager.queryDB("UPDATE members SET msg_quiaconsulte = '0', msg_quiaconsulte_date = '0';");	
			//and refresh the listeif it's alredy loaded in a section secBottin
			if(secMessage != undefined){
				secMessage.refreshSection('quiaconsulte');
				}
			}
		};	
	
	
	/*********ENVOI EXPRESS MESSAGE******************************************************************************************************************/
	
	//user message
	public function sendExpressMessage(arrUser:Array):Void{
		this.__cActionMessages = new CieActionMessages(gLang[61], gLang[62] + arrUser['pseudo'] + gLang[63]);
		this.__cActionMessages.setCallBackFunction(this.cbPromptSendExpressMessage, {__class: this, __user:arrUser});
		};
		
	//callback when user press button pour envoi
	public function cbPromptSendExpressMessage(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptSendExpressMessage(bResult, cbObject.__user);
		};	
		
	//envoi
	public function treatUserActionOnPromptSendExpressMessage(bResult:Boolean, arrUser:Array):Void{
	//depending on the user selection
		if(bResult){
			//do a request
			var arrD = new Array();
			arrD['methode'] = 'express';
			arrD['action'] = 'add';
			arrD['arguments'] = arrUser['no_publique'];
			//add the request
			cReqManager.addRequest(arrD, this.cbSendExpressMessage, {__class:this, __user:arrUser});	
			//put a loader
			this.__cActionMessages.setLoading();
		}else{
			//user have pressed cancel thrn close the window
			this.__cActionMessages.closeWindow();
			this.__cActionMessages = null;	
			}
		};	
	
	public function cbSendExpressMessage(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlSendExpressMessage(obj.__req.getXml().firstChild, obj.__super.__user);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
	
	public function parseXmlSendExpressMessage(xmlNode:XMLNode, arrUser:Array):Void{
		var str:String = xmlNode.childNodes[0].firstChild.nodeValue;
		var strErrAttr:String = xmlNode.childNodes[0].attributes.n;
		var strError:String = '';
		if(str != undefined){
			if(strErrAttr == 'error'){
				if(str == 'ERROR_EXPRESS_MSG_ALREADY_SENT_TO_USER_1'){
					strError = gLang[64] + arrUser['pseudo'] + gLang[65];
				}else if(str == 'ERROR_EXPRESS_MSG_ALREADY_SENT_TO_USER_2'){
					strError = gLang[66] + arrUser['pseudo'] + gLang[67];
				}else if(str == 'ERROR_EXPRESS_SENDING_RIGHTS'){
					strError = gLang[68];
				}else if(str == 'ERROR_MEMBER_DESACTIVE'){
					strError = gLang[333];	
				}else{
					Debug('***ERR_SEND_EXPRESS: ' + str);
					strError = gLang[84];
					}
				}
		}else{
			strError = gLang[84];
			}
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		if(strError != ''){
			new CieTextMessages('MB_OK', strError, gLang[71]);
			}
		};
	
		
	/*********OPEN SITE******************************************************************************************************************************/

	public function gotoSiteRedirection(strSection:String):Void{
		var arrUser = new Array();
		arrUser['pseudo'] = BC.__user.__pseudo;
		arrUser['no_publique'] = BC.__user.__nopub;
		this.openSiteRedirectionBox(strSection, arrUser);
		};
	
	public function openSiteRedirectionBox(section:String, arrUser:Array):Void{
		this.__cActionMessages = new CieActionMessages(gLang[72], gLang[73]);
		this.__cActionMessages.setCallBackFunction(this.cbPromptOpenSiteRedirection, {__class: this, __user:arrUser, __section:section});
		};
		
	//callback when user press button 
	public function cbPromptOpenSiteRedirection(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptOpenSiteRedirection(bResult, cbObject.__section, cbObject.__user);
		};		
	
	public function treatUserActionOnPromptOpenSiteRedirection(bResult:Boolean, section:String, arrUser:Array):Void{
		if(bResult){
			//do a request
			var arrD = new Array();
			arrD['methode'] = 'site';
			arrD['action'] = 'gotosite';
			arrD['arguments'] = section + ',' + arrUser['pseudo'] + ',' + arrUser['no_publique'];
			//add the request
			cReqManager.addRequest(arrD, this.cbOpenSite, {__class:this});
			}
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;	
		};
		
	public function cbOpenSite(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlOpenSite(obj.__req.getXml().firstChild);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};

	public function parseXmlOpenSite(xmlNode:XMLNode):Void{
		var str:String = xmlNode.childNodes[0].firstChild.nodeValue;
		var strErrAttr:String = xmlNode.childNodes[0].attributes.n;
		var strError:String = '';
		if(str == undefined || str == ''){
			strError = gLang[74];
		}else{
			if(strErrAttr == 'error'){
				Debug("***ERR_OPEN_SITE");
				strError = gLang[74];
				}
			}
		if(strError != ''){
			new CieTextMessages('MB_OK', strError, gLang[75]);
		}else{
			getURL(unescape(str), '_blank');
			}
		};	
		
	/*****POPUP pour l'abonnement CHAT pour les membre regulier*******************************************************************************/	
		
	public function chatAbonnement(Void):Void{
		this.__cActionMessages = new CieActionMessages(gLang[76], gLang[77]);
		this.__cActionMessages.setCallBackFunction(this.cbPromptChatAbonnement, {__class:this});
		};
		
	//callback when user press button 
	public function cbPromptChatAbonnement(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptChatAbonnement(bResult);
		};	
		
	public function treatUserActionOnPromptChatAbonnement(bResult:Boolean):Void{
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;	
		if(bResult){
			var arrUser = new Array();
			arrUser['pseudo'] = BC.__user.__pseudo;
			arrUser['no_publique'] = BC.__user.__nopub;
			cFunc.openSiteRedirectionBox('subscription', arrUser);
			}
		};	
	

	/*****RECEVING CHAT MSG ANBD DISTRIBUTE IT TO THE GOOD WINDOW CHAT*******************************************************************/	
	
	//update msg	
	public function updateChatWindowMsg(strPseudo:String, strMsg:String):Void{
		//regarde si la fenetre de chat existe et si elle est active
		if(this.__chatWindow[strPseudo] != undefined){
			if(this.__chatWindow[strPseudo].__active){
				arrCmd = new Array();
				//check si c'est une commande d'update
				if(strMsg == 'IS_READY' || strMsg == 'IS_TYPING' || strMsg == 'IS_NOT_TYPING' || strMsg == 'CAM_ON' || strMsg == 'CAM_OFF'){ 
					arrCmd = [strMsg]; //envoi la commande directement
				}else{ 
					//alors c'est un message de l'autre usager
					arrCmd = ['MSG', strMsg];
					}
				//send msg to the good chat window via the local_conn
				cLocalConn.callRemoteSocket(this.__chatWindow[strPseudo].__lcName, arrCmd);	
				}
		}else{
			delete this.__chatWindow[strPseudo];
			}
		};	
	
	//someone closed his chat	
	public function closeChatWindow(strPseudo:String):Void{
		if(this.__chatWindow[strPseudo] != undefined){
			//SEND A CLOSE CHAT STATE
			cLocalConn.callRemoteSocket(this.__chatWindow[strPseudo].__lcName, ['CLOSE']);
			//put flag to false
			this.__chatWindow[strPseudo].__active = false;
		}else{
			delete this.__chatWindow[strPseudo];
			}
		};
		
	//close all the chat window opened	
	public function closeAllChatWindow(Void):Void{
		for(var o in this.__chatWindow){
			if(this.__chatWindow[o] != undefined){
				//SEND A EXIT CHAT STATE
				cLocalConn.callRemoteSocket(this.__chatWindow[o].__lcName, ['EXIT']);
				//no need to clean wee are going away anyway
				//send message to other users
				cSockManager.socketSend('CLOSED:' + o);
				//put the flag
				this.__chatWindow[o].__active = false;
			}else{
				delete this.__chatWindow[o];
				}
			}
		};	
		
	//check si un chat est deja ouvert avec cette personne
	public function checkIfChatIsOpened(strPseudo:String):Boolean{
		if(this.__chatWindow[strPseudo].__active){
			return true;
			}
		return false;	
		};	
		
		
	/*****LOCAL SOCKET COMMAND TREATMENT CALLED BY CieLocalSocket*******************************************************************************/
	
	public function localSocketCommand(param:Array):Void{
		//param[0] est le nom de la fenetre appelante
		//param[1] est un array d'argument
		//si c'est une fenetre de chat qui nous qu'elle est prete
		if(param[1][0] == 'IS_READY'){
			//on envoi la conmmad au serveur pour dire que tout est beau et que l'on peut commencer a chatter
			cSockManager.socketSend(param[1][1] + '=' + param[1][0]);
		}else if(param[1][0] == 'MSG'){
			//redistribuer le message
			cSockManager.socketSend(param[1][1] + '=' + param[1][2]);
		}else if(param[1][0] == 'CAM_ON'){
			//redistribuer le message
			cSockManager.socketSend(param[1][1] + '=' + param[1][0]);	
		}else if(param[1][0] == 'CAM_OFF'){
			//redistribuer le message
			cSockManager.socketSend(param[1][1] + '=' + param[1][0]);		
		}else if(param[1][0] == 'CLOSE'){
			//cleaner le array de connection de chat avec ce pseudo
			if(this.__chatWindow[param[1][1]] != undefined){
				this.__chatWindow[param[1][1]].__active = false;
			}else{
				delete this.__chatWindow[param[1][1]];
				}
			//redistribuer le message de fermeture au client via le serveur UNI
			cSockManager.socketSend('CLOSED:' + param[1][1]);
		}else if(param[1][0] == 'WM_FOCUS'){
			//la fenetre reprend son etat standard
			//generalement qunad on clcique sur les alert
			this.appzGetFocus(param[1][1], param[1][2], param[1][3], param[1][4]);
		}else{
			//Debug("LC_COMMAND_NOT_FOUND: " + param);
			}
		};
	

	/**** FOCUS *******************************************************************************************************************************************/
	
	public function appzGetFocus(ctype:String, nopub:String, strXml:String, msgType:String):Void{
		//strXml is the node to pass for a detailed profil
		//same has when we click on a miniProfil
		
		//not to systray
		mdm.Application.minimizeToTray(false);
		//focus restore
		mdm.Application.restore();
	
		//if we got a section then go to it
		if(ctype == 'newconn'){
			//allezx au carnet
			this.openBottin();
			//rafraichir la section
			secBottin.refreshSection('carnet');
			//ouvrir l'onglet carnet
			secBottin.openSection('carnet');
			//afficher le profil a droite en convertissant le strXml en XML puis send du child XMLNode, car detailed s'attend a recevoir ca
			var newXml:XML = new XML(strXml);
			new CieDetailedProfil(['bottin','_tr'], ['bottin','SKIP','profil','SKIP','description'], newXml.firstChild, ['bottin','SKIP']);
		}else if(ctype == 'newchat'){
			//ouvrir selement la fenetre pour afficher la fenetre pour accepter ou refuser le chat
			//NOTHING TO DO
		}else if(ctype == 'newcrit'){
			//,match les critetes alors on ouvre le salon
			//ouvrurir la section des messages
			this.openSalon();
			//afficher le profil a droite en convertissant le strXml en XML puis send du child XMLNode, car detailed s'attend a recevoir ca
			var newXml:XML = new XML(strXml);
			new CieDetailedProfil(['salon','_tr'], ['salon','SKIP','profil','SKIP','description'], newXml.firstChild, ['salon','SKIP']);
		}else if(ctype == 'newmsg'){
			//ouvrurir la section des messages
			this.openMessage();
			//refresh de la sectrion	
			secMessage.refreshSection('communications');
			//ouvrir l'onglet communications
			secMessage.openSection('communications');
			//afficher le profil a droite en convertissant le strXml en XML puis send du child XMLNode, car detailed s'attend a recevoir ca
			var newXml:XML = new XML(strXml);
			new CieDetailedProfil(['message','_tr'], ['message','SKIP','messages',msgType], newXml.firstChild, ['message','SKIP']);
		}else if(ctype == 'newprofil'){
			//ouvrior ls section des qui a consulte
			this.openMessage();
			//refresh de la section
			secMessage.refreshSection('quiaconsulte');
			//ouvrir le bon onglet
			secMessage.openSection('quiaconsulte');
			//afficher le profil a droite en convertissant le strXml en XML puis send du child XMLNode, car detailed s'attend a recevoir ca
			var newXml:XML = new XML(strXml);
			new CieDetailedProfil(['message','_tr'], ['message','SKIP','profil','SKIP','description'], newXml.firstChild, ['message','SKIP']);
			}
		
		};
	
	
	
	/**** ALERT *******************************************************************************************************************************************/
	
	//tell to stop the alerts
	public function stopAlertWindow(Void):Void{
		cLocalConn.callRemoteSocket('ALERT',['STOP']);
		};
	
	
	//tell to close all opened alert boxes
	public function closeAllAlertWindow(Void):Void{
		cLocalConn.callRemoteSocket('ALERT',['CLOSE_ALL']);
		};
		
	//remote call to alert windowa
	public function updateAlertWindowSalon(arrInfos:Array):Void{
		
		//msgtype est passe seulement lors de la reception d'un message I,A,E,V
		//pour renvoyer a l'Alert pour que le click sur celle ci puisse ouvrir le bon onglet de messages de l'aaplication principale
		var xmlInfos:String = '';
		//xmlInfos += '<ALERTMSG>';
		//ids when click on the alert to pass the infos of the member
		//same format has a miniProfil
		xmlInfos += '<R>';
		xmlInfos += '<C n="age">' + arrInfos[2] + '</C>';
		xmlInfos += '<C n="no_publique">' + arrInfos[0] + '</C>';
		xmlInfos += '<C n="pseudo">' + arrInfos[1] + '</C>';
		xmlInfos += '<C n="ville_id">' + arrInfos[12] + '</C>';
		xmlInfos += '<C n="region_id">' + arrInfos[11] + '</C>';
		xmlInfos += '<C n="code_pays">' + arrInfos[10] + '</C>';
		xmlInfos += '<C n="album">' + arrInfos[3] + '</C>';
		xmlInfos += '<C n="photo">' + arrInfos[4] + '</C>';
		xmlInfos += '<C n="vocal">' + arrInfos[5] + '</C>';
		xmlInfos += '<C n="membership">' + arrInfos[6] + '</C>';
		xmlInfos += '<C n="orientation">' + arrInfos[7] + '</C>';
		xmlInfos += '<C n="sexe">' + arrInfos[8] + '</C>';
		xmlInfos += '<C n="relation">' + arrInfos[9] + '</C>';
		xmlInfos += '<C n="etat_civil">' + arrInfos[13] + '</C>';
		xmlInfos += '<C n="titre">' + arrInfos[14] + '</C>';
				
		//specail to text
		xmlInfos += '<C n="t_sexe">' + escape(cFormManager.__obj['sexe'][arrInfos[8]][1]) + '</C>';
		xmlInfos += '<C n="t_orientation">' + escape(cFormManager.__obj['orientation'][arrInfos[7]][1]) + '</C>';
		xmlInfos += '<C n="t_etatcivil">' + escape(cFormManager.__obj['etatcivil'][arrInfos[13]][1]) + '</C>';
		
		//aller chercher les texte pour ville, region, pays
		if(gGeo[arrInfos[10] + '_' + arrInfos[11] + '_' + arrInfos[12]] != undefined){
			xmlInfos += '<C n="t_location">' + escape(gGeo[arrInfos[10] + '_' + arrInfos[11] + '_' + arrInfos[12]]) + '</C>';
		}else{
			xmlInfos += '<C n="t_location">...</C>';
			}
		
		//photo
		if(arrInfos[4] == '2'){
			xmlInfos += '<C n="t_photo">' + escape(BC.__server.__thumbs + arrInfos[0].substr(0,2) + "/" + arrInfos[1] + ".jpg") + '</C>';
			}
		
		//type d'alerte
		xmlInfos += '<C n="ctype">newcrit</C>';
		
		//type de msg 
		if(msgType != undefined && msgType != ''){
			xmlInfos += '<C n="msgtype">newcrit</C>';
			}
		
		xmlInfos += '</R>';
		//xmlInfos += '</ALERTMSG>';
		
		//send to the alert window via localConnection socket
		cLocalConn.callRemoteSocket('ALERT', ['ALERT', xmlInfos]);	
		}
	
	
	public function updateAlertWindow(noPub:String, ctype:String, msgType:String):Void{
	
		//check if everything is loaded before senbding alert to user
		if(BC.__user.__loaded && BC.__user.__showAlert){
			//aller chercher les infos dans la BD
			if((ctype == 'newconn' && BC.__alert.__newconn) || (ctype == 'newchat' && BC.__alert.__newchat) || (ctype == 'newmsg' && BC.__alert.__newmsg) || (ctype == 'newprofil' && BC.__alert.__newprofil)){
				var arrRows:Array = cDbManager.selectDB('SELECT members.pseudo, members.age, members.titre, members.sexe, members.orientation, members.etat_civil, members.photo, members.membership, members.album, members.vocal, members.relation, members.code_pays, members.region_id, members.ville_id FROM members WHERE members.no_publique = ' + noPub + ';');
				if(arrRows.length > 0){
					var xmlInfos:String = '';
					//xmlInfos += '<ALERTMSG>';
					//ids when click on the alert to pass the infos of the member
					//same format has a miniProfil
					xmlInfos += '<R>';
					xmlInfos += '<C n="age">' + arrRows[0][1] + '</C>';
					xmlInfos += '<C n="no_publique">' + noPub + '</C>';
					xmlInfos += '<C n="pseudo">' + arrRows[0][0] + '</C>';
					xmlInfos += '<C n="ville_id">' + arrRows[0][13] + '</C>';
					xmlInfos += '<C n="region_id">' + arrRows[0][12] + '</C>';
					xmlInfos += '<C n="code_pays">' + arrRows[0][11] + '</C>';
					xmlInfos += '<C n="album">' + arrRows[0][8] + '</C>';
					xmlInfos += '<C n="photo">' + arrRows[0][6] + '</C>';
					xmlInfos += '<C n="vocal">' + arrRows[0][9] + '</C>';
					xmlInfos += '<C n="membership">' + arrRows[0][7] + '</C>';
					xmlInfos += '<C n="orientation">' + arrRows[0][4] + '</C>';
					xmlInfos += '<C n="sexe">' + arrRows[0][3] + '</C>';
					xmlInfos += '<C n="relation">' + arrRows[0][10] + '</C>';
					xmlInfos += '<C n="etat_civil">' + arrRows[0][5] + '</C>';
					xmlInfos += '<C n="titre">' + arrRows[0][2] + '</C>';
					
					//specail to text
					xmlInfos += '<C n="t_sexe">' + escape(cFormManager.__obj['sexe'][arrRows[0][3]][1]) + '</C>';
					xmlInfos += '<C n="t_orientation">' + escape(cFormManager.__obj['orientation'][arrRows[0][4]][1]) + '</C>';
					xmlInfos += '<C n="t_etatcivil">' + escape(cFormManager.__obj['etatcivil'][arrRows[0][5]][1]) + '</C>';
					
					//aller chercher les texte pour ville, region, pays
					if(gGeo[arrRows[0][11] + '_' + arrRows[0][12] + '_' + arrRows[0][13]] != undefined){
						xmlInfos += '<C n="t_location">' + escape(gGeo[arrRows[0][11] + '_' + arrRows[0][12] + '_' + arrRows[0][13]]) + '</C>';
					}else{
						xmlInfos += '<C n="t_location">...</C>';
						}
				
					//type d'alerte
					xmlInfos += '<C n="ctype">' + ctype + '</C>';
					
					//type de msg 
					if(msgType != undefined && msgType != ''){
						xmlInfos += '<C n="msgtype">' + msgType + '</C>';
						}
					
					//photo
					if(arrRows[0][6] == '2'){
						xmlInfos += '<C n="t_photo">' + escape(BC.__server.__thumbs + noPub.substr(0,2) + "/" + arrRows[0][0] + ".jpg") + '</C>';
						}
					
					xmlInfos += '</R>';
					//xmlInfos += '</ALERTMSG>';
					
					//send to the alert window via localConnection socket
					cLocalConn.callRemoteSocket('ALERT', ['ALERT', xmlInfos]);	
					}
				}	
			}	
		};
		
	/***EXIT ALL ************************************************************************************************************************/	
	
	public function exitApplication(Void):Void{
		//disconnect from the BD
		cDbManager.disconnectDB();
		//close all the chat window
		cFunc.closeAllChatWindow(); 
		if(cSockManager != undefined){
			cSockManager.closeConnection();
			}
		//exit
		mdm.Application.exit();
		};
	
	
	/***RECEPTION DE PROFIL CONSULTE************************************************************************************************************************/
	
	//traitement de la reception de nouveau consulte
	public function receiveNewConsulte(strData:String):Void{
		Debug("receiveNewConsulte(" + strData + ")");
		var noPub:String = strData;
		if(gDbKey[noPub] != undefined){
			var timeStamp:Number = Math.round(new Date().getTime() / 1000);
			cDbManager.queryDB("UPDATE members SET msg_quiaconsulte = '1', msg_quiaconsulte_date = '" + timeStamp + "' WHERE no_publique = " + noPub + ";");
			if(BC.__user.__debugquery != false){
				Debug("QUERY :: UPDATE members SET msg_quiaconsulte = '1', msg_quiaconsulte_date = '" + timeStamp + "' WHERE no_publique = " + noPub + ";");
				}
			//Good so now we can show the alert to the user
			this.updateAlertWindow(noPub, 'newprofil');
		}else{
			Debug('NEWCONSULTE: NOPUB NOT THERE');
			//call the server to get it
			cSockManager.registerForUserInfosCallBack(this.cbUserInfos, {__class:this,__args:{ __cmd:'NC'}}, strData);
			}
		};
			
	/***RECEPTION d'un usager qui manquanit dans la DB***************************************************************************************************/		
	//la mem info qu'un P: ou PLIST:
	public function cbUserInfos(cbObject:Object, strData:String):Void{
		cbObject.__class.insertMissingUser(strData.split(','), cbObject.__args);
		};
		
	public function insertMissingUser(arrData:Array, objArgs:Object):Void{
		Debug("insertMissingUser(" + arrData + ", " + objArgs + ")");
		if(gDbKey[arrData[0]] == undefined){
			//insert into DB
			var strQuery = "INSERT INTO members (no_publique, pseudo, age, album, photo, vocal, membership, orientation, sexe, relation, code_pays, region_id, ville_id, etat_civil, titre, active, msg_salon) VALUES(" + arrData[0] + ",'" + arrData[1] + "','" + arrData[2] + "','" + arrData[3] + "','" + arrData[4] + "','" + arrData[5] + "','" + arrData[6] + "','" + arrData[7] + "','" + arrData[8] + "','" + arrData[9] + "','" + arrData[10] + "','" + arrData[11] + "','" + arrData[12] + "','" + arrData[13] + "','" + arrData[14] + "','" + arrData[15] + "','1');";
			cDbManager.queryDB(strQuery);
			//DBKEY
			gDbKey[arrData[0]] = true;
			//update le stackOnline
			cSockManager.setOnlineStatus(arrData[0], arrData[15]);
			//check whoa was calling it
			if(objArgs.__cmd == 'NC'){
				Debug("RECALL: NC");
				this.receiveNewConsulte(arrData[0]);
			}else if(objArgs.__cmd == 'NM'){
				Debug("RECALL: NM");
				this.receiveNewMessage(objArgs.__data);
			}else if(objArgs.__cmd == 'CHAT'){
				this.askingForVideoChat(objArgs.__pseudo, objArgs.__nopub);
				}
			}
		};
	
	/***RECEPTION DE MESSAGE************************************************************************************************************************/	
	
	//traitement de la reception de nouveau message du web de la v1 ou de la v2
	public function receiveNewMessage(strData:String):Void{
		/* 
		EXEMPLE DE RETOUR (NM est stripper avant d'arriver la)
		20:53:51: UniAppz-->IN: NM:1348483,I,22444745
		20:55:10: UniAppz-->IN: NM:1348483,E,1175820917
		*/
		//split les args en ordre donne (nopublique), (typedemessage), (noinstant ou nocourrier ou novocal ou expressdate)
		var arrData:Array = strData.split(',');
		var noPub = arrData[0];
		var msgType = arrData[1];
		var extraInfos = arrData[2]; //depends on de msgType
		delete arrData; //no need for it anymore
		var bMissingUser:Boolean = false;
		
		//since the message came from somebody that is online we assume it is in the members database table
		if(msgType == 'E'){ //si c'est un message express extraInfos will be the timestamp
			//check si il est dans la db
			if(gDbKey[noPub] != undefined){
			//if(cDbManager.dbKeyExist('no_publique', noPub)){
				cDbManager.queryDB("UPDATE members SET msg_express = '1', msg_express_date = '" + extraInfos + "' WHERE no_publique = " + noPub + ";");
				//Good so now we can show the alert to the user
				this.updateAlertWindow(noPub, 'newmsg', 'express');
				//set the icon to tell we have new messages
				cSysTray.changeSysTrayIcon('cie_msg.ico');
				//make the message icon blink
				cToolManager.getTool('messages', 'message').blinkEffect(true);	
				//make the refresh icon of the section messages/communication to blink
				if(secMessage != undefined){
					secMessage.makeRefreshButtonBlink(true);
					}
			}else{
				Debug('NEWMSG(E): NOPUB NOT THERE');
				bMissingUser = true;
				}
			//	
		}else if(msgType == 'I'){ //si c'est un message instant extraInfos will be the no_instant
			if(gDbKey[noPub] != undefined){
				cDbManager.queryDB("UPDATE members SET msg_instant = '1' WHERE no_publique = " + noPub + ";");
				//add a request to get all infos of this messages
				var arrD = new Array();
				arrD['methode'] = 'instant';
				arrD['action'] = 'single';
				arrD['arguments'] = extraInfos;
				//add the request
				cReqManager.addRequest(arrD, this.cbGetSingleInstant, {__class:this});
			}else{
				Debug('NEWMSG(I): NOPUB NOT THERE');
				bMissingUser = true;
				}
			//
		}else if(msgType == 'A'){
			if(gDbKey[noPub] != undefined){
				cDbManager.queryDB("UPDATE members SET msg_courrier = '1' WHERE no_publique = " + noPub + ";");
				//add a request to get all infos of this messages
				var arrD = new Array();
				arrD['methode'] = 'courrier';
				arrD['action'] = 'single';
				arrD['arguments'] = extraInfos;
				//add the request
				cReqManager.addRequest(arrD, this.cbGetSingleCourrier, {__class:this});	
			}else{
				Debug('NEWMSG(A): NOPUB NOT THERE');
				bMissingUser = true;
				}
			//
		}else if(msgType == 'V'){
			if(gDbKey[noPub] != undefined){
				cDbManager.queryDB("UPDATE members SET msg_vocal = '1' WHERE no_publique = " + noPub + ";");
				//add a request to get all infos of this messages
				var arrD = new Array();
				arrD['methode'] = 'vocal';
				arrD['action'] = 'single';
				arrD['arguments'] = extraInfos;
				//add the request
				cReqManager.addRequest(arrD, this.cbGetSingleVocal, {__class:this});			
			}else{
				Debug('NEWMSG(V): NOPUB NOT THERE');
				bMissingUser = true;
				}
			}
			
		//if we have a missing user get it
		if(bMissingUser){
			//call the server to get it
			cSockManager.registerForUserInfosCallBack(this.cbUserInfos, {__class:this,__args:{ __cmd:'NM',__data:strData}}, noPub);
			}
			
		};
	
	
	//reception du single vocal
	public function cbGetSingleVocal(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlSingleVocal(obj.__req.getXml().firstChild);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};	
		
	//parse the vocal messages content messages
	private function parseXmlSingleVocal(xmlNode:XMLNode):Void{	
		var strVocal:String = xmlNode.childNodes[0].firstChild.nodeValue;
		//Debug('NEWMSG_2_V: ' + strVocal);
		if(strVocal != undefined || strVocal != null || strVocal != '' || strVocal != 'EMPTYDATA'){
			//split the values
			var arrInfos:Array = strVocal.split('|');
			//minor check
			if(arrInfos.length > 0){
				//insert du message vocal
				cDbManager.queryDB("INSERT INTO vocal (no_vocal, no_publique, cdate, lu, repondu, message_id ) VALUES ('" + arrInfos[0] + "','" + arrInfos[1] + "','" + arrInfos[2] + "','" + arrInfos[3] + "','" + arrInfos[4] + "','" + arrInfos[5] + "');");
				//Good so now we can show the alert to the user
				this.updateAlertWindow(arrInfos[1], 'newmsg', 'vocal');
				//set the icon to tell we have new messages
				cSysTray.changeSysTrayIcon('cie_msg.ico');
				//make the message icon blink
				cToolManager.getTool('messages', 'message').blinkEffect(true);
				//make the refresh icon of the section messages/communication to blink
				secMessage.makeRefreshButtonBlink(true);
				}
			}
		};	
	
	//reception du single instant
	public function cbGetSingleInstant(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlSingleInstant(obj.__req.getXml().firstChild);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};	
		
		
	//parse le single instant et fait l'insertion si les donnees sont valides
	public function parseXmlSingleInstant(xmlNode:XMLNode):Void{
		var strInstant:String = xmlNode.childNodes[0].firstChild.nodeValue;
		//Debug("SINGLE_INSTANT: " + strInstant);
		if(strInstant != undefined || strInstant != null || strInstant != '' || strInstant != 'EMPTYDATA'){
			//split the values
			var arrInfos:Array = strInstant.split('|');
			//minor check
			if(arrInfos.length > 0){
				//insert du message instant
				cDbManager.queryDB("INSERT INTO instant (no_instant, type, cdate, msg, no_publique, lu) VALUES ('" + arrInfos[0] + "','0','" + arrInfos[2] + "','" + arrInfos[3] + "','" + arrInfos[1] + "','" + arrInfos[4] + "');");
				//Good so now we can show the alert to the user
				this.updateAlertWindow(arrInfos[1], 'newmsg', 'instant');
				//set the icon to tell we have new messages
				cSysTray.changeSysTrayIcon('cie_msg.ico');
				//make the message icon blink
				cToolManager.getTool('messages', 'message').blinkEffect(true);
				//make the refresh icon of the section messages/communication to blink
				secMessage.makeRefreshButtonBlink(true);
				}
			}
		};	
	
	//reception du single courrier
	public function cbGetSingleCourrier(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlSingleCourrier(obj.__req.getXml().firstChild);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};	
		
		
	//parse des courrier
	public function parseXmlSingleCourrier(xmlNode:XMLNode):Void{
		//Debug("SINGLE_COURRIER_XMLNODE: " + xmlNode);
		for(var iIndex=0; iIndex<xmlNode.childNodes.length; iIndex++){
			//check if we have an error must ne the first node with tag n='error'
			if(xmlNode.childNodes[iIndex].attributes.n == 'error'){
				Debug('***ERR (courrier): ' + xmlNode.childNodes[iIndex].firstChild.nodeValue);
				xmlNode = null;
				delete xmlNode; 
				//Debug("SINGLE_COURRIER_XML_FINISH");
			}else{
				//user infos at childNode[0]
				var arrInfos:Array = xmlNode.childNodes[iIndex].childNodes[0].firstChild.nodeValue.split('|');
				if(gDbKey[arrInfos[0]] != undefined){
				//if(cDbManager.dbKeyExist('no_publique', arrInfos[0])){
					cDbManager.queryDB("UPDATE members SET msg_courrier = '1' WHERE no_publique = " + arrInfos[0] + ";");
				}else{
					cDbManager.queryDB("INSERT INTO members (no_publique, pseudo, age, ville_id, region_id, code_pays, album, photo, vocal, membership, orientation, sexe, titre, relation, etat_civil, msg_courrier) VALUES ('" + arrInfos[0] + "','" + arrInfos[1] + "','" + arrInfos[2] + "','" + arrInfos[3] + "','" + arrInfos[4] + "','" + arrInfos[5] + "','" + arrInfos[6] + "','" + arrInfos[7] + "','" + arrInfos[8] + "','" + arrInfos[9] + "','" + arrInfos[10] + "','" + arrInfos[11] + "','" + arrInfos[12] + "','" + arrInfos[13] + "','" + arrInfos[14] + "', '1');");
					}
				//instant messages content at childNode[1]
				this.parseXmlSingleCourrierMessages(xmlNode.childNodes[iIndex].childNodes[1], arrInfos[0]);
				}	
			}
		//Good so now we can show the alert to the user
		this.updateAlertWindow(arrInfos[0], 'newmsg', 'courriel');
		//set the icon to tell we have new messages
		cSysTray.changeSysTrayIcon('cie_msg.ico');
		//make the message icon blink
		cToolManager.getTool('messages', 'message').blinkEffect(true);
		//make the refresh icon of the section messages/communication to blink
		secMessage.makeRefreshButtonBlink(true);
		};	
	
	//parse the courrier	messages
	private function parseXmlSingleCourrierMessages(xmlNode:XMLNode, iNoPub:Number):Void{	
		//Debug("SINGLE_COURRIER_MSG_XMLNODE: " + xmlNode);
		var strChamps:String = 'no_publique,';
		var strValues:String = iNoPub + ',';
		var iNoCourrier:Number = 0;
		var bQuery:Boolean = true;
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.firstChild.nodeType == 1){
				//found a ROW
				if(currNode.attributes.n == 'attachement'){
					this.parseXmlSingleCourrierAttachement(currNode, iNoCourrier);
				}else{
					bQuery = false;
					this.parseXmlSingleCourrierMessages(currNode, iNoPub);
					}
			}else{
				//found an ITEM
				strChamps += currNode.attributes.n + ',';
				if(currNode.attributes.n == 'no_courrier'){
					strValues += Number(currNode.firstChild.nodeValue) + ",";
					iNoCourrier = Number(currNode.firstChild.nodeValue);
				}else{
					strValues += "'" + String(currNode.firstChild.nodeValue) + "',";
					}
				}
			}
		//finish the query
		if(bQuery){
			cDbManager.queryDB("INSERT INTO courrier (" + strChamps.substring(0, strChamps.length - 1) + ") VALUES (" + strValues.substring(0, strValues.length - 1) + ");");
			}		
		};		
		
	//parse the attachement des courriers
	private function parseXmlSingleCourrierAttachement(xmlNode:XMLNode, iNoCourrier:Number):Void{
		//Debug("SINGLE_COURRIER_ATTACHEMENT_XMLNODE: " + xmlNode);
		var strChamps:String = 'no_courrier,';
		var strValues:String = iNoCourrier + ',';
		var bQuery:Boolean = true;
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.firstChild.nodeType == 1){
				//found a ROW
				bQuery = false;
				this.parseXmlSingleCourrierAttachement(currNode, iNoCourrier);
			}else{
				//found an ITEM
				strChamps += currNode.attributes.n + ',';
				strValues += "'" + String(currNode.firstChild.nodeValue) + "',";
				}
			}
		//finish the query
		if(bQuery){
			cDbManager.queryDB("INSERT INTO attachement (" + strChamps.substring(0, strChamps.length - 1) + ") VALUES (" + strValues.substring(0, strValues.length - 1) + ");");
			}		
		};			
		
		
	/***DB SYNCHRONISATION**********************************************************************************************************************/
	
	//synchronisation between local and remote DB
	//called by the treadManager in socketManager because fucking replication of mySql take to long
	//so we have to wait a bit like a 2 seconds
	//public function dbSync(strTypeOfMsg:String, strMethod:String, arrArgs:Array):Void{
	public function dbSync(obj:Object):Boolean{
		//replcae bacause I'm to lazy to change the code
		var strTypeOfMsg:String = obj.__type; 
		var strMethod:String = obj.__method;
		var arrArgs:Array = obj.__args;
		//check the type of msg to update
		if(strTypeOfMsg == 'instant'){
			if(strMethod == 'delete'){
				//build the condition
				var strWhere:String = '';
				for(var o in arrArgs){
					strWhere += ' no_instant = ' + arrArgs[o] + ' OR';
					}
				//strip de last 'OR'
				strWhere = strWhere.substring(0, (strWhere.length - 2));		
				//delete from local DB
				Debug('SYNC: DELETE * FROM instant WHERE ' + strWhere + ';');		
				cDbManager.queryDB('DELETE * FROM instant WHERE ' + strWhere + ';');
			}else if(strMethod == 'insert'){
				//add a request to get all infos of this messages because we need the nopub or pseudo to retrieve the profil for updating members table
				var arrD = new Array();
				arrD['methode'] = 'instant';
				arrD['action'] = 'singlesync';
				arrD['arguments'] = arrArgs[0];
				//add the request
				cReqManager.addRequest(arrD, this.cbDbSyncSingleInstant, {__class:this});
				}
		}else if(strTypeOfMsg == 'courrier'){
			if(strMethod == 'delete'){
				//build the condition
				var strWhere:String = '';
				for(var o in arrArgs){
					strWhere += ' no_courrier = ' + arrArgs[o] + ' OR';
					}
				//strip de last 'OR'
				strWhere = strWhere.substring(0, (strWhere.length - 2));		
				//delete from local DB
				Debug('SYNC: DELETE * FROM courrier WHERE ' + strWhere + ';');		
				cDbManager.queryDB('DELETE * FROM courrier WHERE ' + strWhere + ';');
				//delete the attach
				Debug('SYNC: DELETE * FROM attachement WHERE ' + strWhere + ';');		
				cDbManager.queryDB('DELETE * FROM attachement WHERE ' + strWhere + ';');
			}else if(strMethod == 'insert'){
				//add a request to get all infos of this messages because we need the nopub or pseudo to retrieve the profil for updating members table
				var arrD = new Array();
				arrD['methode'] = 'courrier';
				arrD['action'] = 'singlesync';
				arrD['arguments'] = arrArgs;
				//add the request
				cReqManager.addRequest(arrD, this.cbDbSyncSingleCourrier, {__class:this});
				}
		}else if(strTypeOfMsg == 'express'){
			if(strMethod == 'delete'){
				if(arrArgs[0] == 'ALL'){
					//delete from local DB
					//Debug('SYNC: UPDATE members SET msg_express = "0", msg_express_date = "";');		
					cDbManager.queryDB('UPDATE members SET msg_express = "0", msg_express_date = "";');
				}else{
					//build the condition
					var strWhere:String = '';
					for(var o in arrArgs){
						strWhere += ' no_publique = ' + arrArgs[o] + ' OR';
						}
					//strip de last 'OR'
					strWhere = strWhere.substring(0, (strWhere.length - 2));
					//delete from local DB
					Debug('SYNC: UPDATE members SET  msg_express = "0" WHERE ' + strWhere + ';');		
					cDbManager.queryDB('UPDATE members SET  msg_express = "0" WHERE ' + strWhere + ';');
					}
				}
			}
		return false;
		};
		
		
	//reception du single instant
	public function cbDbSyncSingleInstant(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlDbSyncSingleInstant(obj.__req.getXml().firstChild);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};	
		
		
	//parse le single instant et fait l'insertion si les donnees sont valides
	public function parseXmlDbSyncSingleInstant(xmlNode:XMLNode):Void{
		var strInstant:String = xmlNode.childNodes[0].firstChild.nodeValue;
		var strQuery:String = '';
		//Debug("SINGLE_INSTANT: " + strInstant);
		if(strInstant != undefined || strInstant != null || strInstant != '' || strInstant != 'EMPTYDATA'){
			//split the values
			var arrInfos:Array = strInstant.split('|');
			//minor check
			if(arrInfos.length > 0){
				//query du message instant
				strQuery = "INSERT INTO instant (no_instant, type, cdate, msg, no_publique, lu) VALUES ('" + arrInfos[0] + "','1','" + arrInfos[2] + "','" + arrInfos[3] + "','" + arrInfos[1] + "','" + arrInfos[4] + "');";
				//ok check if the user is alredy in db
				if(gDbKey[arrInfos[1]] != undefined){
				//if(cDbManager.dbKeyExist('no_publique', arrInfos[1])){
					//it there so chamge the flag for instant
					//Debug('SYNC: UPDATE members SET msg_instant = "1" WHERE no_publique = ' + arrInfos[1] + ';');	
					cDbManager.queryDB('UPDATE members SET msg_instant = "1" WHERE no_publique = ' + arrInfos[1] + ';');
					//insert the message instant
					cDbManager.queryDB(strQuery);
				}else{
					//ok user dont exist in DB so go get himm with a request
					//we will insert the msg on recetion of the miniprofil
					var arrD = new Array();
					arrD['methode'] = 'details';
					arrD['action'] = 'miniprofilconcatbynopub';
					arrD['arguments'] = arrInfos[1];
					//add the request
					cReqManager.addRequest(arrD, this.cbDbSyncMiniProfil, {__class:this, __query:strQuery});
					}
				}	
			}	
		};	

	//reception du miniprofil
	public function cbDbSyncMiniProfil(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlMiniProfilForDbSync(obj.__req.getXml().firstChild, obj.__super.__query);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};

	//parse le single miniprofil et fait l'insertion si les donnees sont valides
	public function parseXmlMiniProfilForDbSync(xmlNode:XMLNode, strQuery:String):Void{
		var strProfil:String = xmlNode.childNodes[0].firstChild.nodeValue;
		//Debug("MINI_PROFIL: " + strProfil);
		if(strProfil != undefined || strProfil != null || strProfil != '' || strProfil != 'EMPTYDATA'){
			//split the values
			var arrData:Array = strProfil.split('|');
			//minor check
			if(arrData.length > 0){
				//insert du membre
				//Debug("SYNC: INSERT INTO members (no_publique, pseudo, age, album, photo, vocal, membership, orientation, sexe, relation, code_pays, region_id, ville_id, etat_civil, titre, msg_instant) VALUES(" + arrData[0] + ",'" + arrData[1] + "','" + arrData[2] + "','" + arrData[3] + "','" + arrData[4] + "','" + arrData[5] + "','" + arrData[6] + "','" + arrData[7] + "','" + arrData[8] + "','" + arrData[9] + "','" + arrData[10] + "','" + arrData[11] + "','" + arrData[12] + "','" + arrData[13] + "','" + arrData[14] + "','1');");	
				cDbManager.queryDB("INSERT INTO members (no_publique, pseudo, age, album, photo, vocal, membership, orientation, sexe, relation, code_pays, region_id, ville_id, etat_civil, titre, msg_instant) VALUES(" + arrData[0] + ",'" + arrData[1] + "','" + arrData[2] + "','" + arrData[3] + "','" + arrData[4] + "','" + arrData[5] + "','" + arrData[6] + "','" + arrData[7] + "','" + arrData[8] + "','" + arrData[9] + "','" + arrData[10] + "','" + arrData[11] + "','" + arrData[12] + "','" + arrData[13] + "','" + arrData[14] + "','1');");
				//inserrt du message
				cDbManager.queryDB( strQuery);
				}
			}	
		};	
		
		
	//reception du single courrier
	public function cbDbSyncSingleCourrier(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlDbSyncSingleCourrier(obj.__req.getXml().firstChild);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};		
		
	
	//parse des courrier
	public function parseXmlDbSyncSingleCourrier(xmlNode:XMLNode):Void{
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var arrInfos:Array = xmlNode.childNodes[i].childNodes[0].firstChild.nodeValue.split('|');
			if(gDbKey[arrInfos[0]] != undefined){
			//if(cDbManager.dbKeyExist('no_publique', arrInfos[0])){
				cDbManager.queryDB("UPDATE members SET msg_courrier = '1' WHERE no_publique = " + arrInfos[0] + ";");
			}else{
				cDbManager.queryDB("INSERT INTO members (no_publique, pseudo, age, ville_id, region_id, code_pays, album, photo, vocal, membership, orientation, sexe, titre, relation, etat_civil, msg_courrier) VALUES ('" + arrInfos[0] + "','" + arrInfos[1] + "','" + arrInfos[2] + "','" + arrInfos[3] + "','" + arrInfos[4] + "','" + arrInfos[5] + "','" + arrInfos[6] + "','" + arrInfos[7] + "','" + arrInfos[8] + "','" + arrInfos[9] + "','" + arrInfos[10] + "','" + arrInfos[11] + "','" + arrInfos[12] + "','" + arrInfos[13] + "','" + arrInfos[14] + "', '1');");
				}
			//courrier messages content at childNode[1]
			this.parseXmlDbSyncCourrierMessages(xmlNode.childNodes[i].childNodes[1], arrInfos[0]);	
			}

		};	
		
	//parse the courrier	messages
	private function parseXmlDbSyncCourrierMessages(xmlNode:XMLNode, iNoPub:Number):Void{	
		var strChamps:String = 'no_publique,';
		var strValues:String = iNoPub + ',';
		var iNoCourrier:Number = 0;
		var bQuery:Boolean = true;
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.firstChild.nodeType == 1){
				//found a ROW
				if(currNode.attributes.n == 'attachement'){
					this.parseXmlDbSyncCourrierAttachement(currNode, iNoCourrier);
				}else{
					bQuery = false;
					this.parseXmlDbSyncCourrierMessages(currNode, iNoPub);
					}
			}else{
				//found an ITEM
				if(currNode.attributes.n != 'attachement'){
					strChamps += currNode.attributes.n + ',';
					}
				if(currNode.attributes.n == 'no_courrier'){
					strValues += Number(currNode.firstChild.nodeValue) + ",";
					iNoCourrier = Number(currNode.firstChild.nodeValue);
				}else if(currNode.attributes.n == 'attachement'){	
					//do nothing it'a an error of attachement flag without any attachement
				}else{
					strValues += "'" + String(currNode.firstChild.nodeValue) + "',";
					}
				}
			}
		//finish the query
		if(bQuery){
			cDbManager.queryDB("INSERT INTO courrier (" + strChamps.substring(0, strChamps.length - 1) + ") VALUES (" + strValues.substring(0, strValues.length - 1) + ");");
			}		
		};			
		
		
	//parse the attachement des courriers
	private function parseXmlDbSyncCourrierAttachement(xmlNode:XMLNode, iNoCourrier:Number):Void{
		var strChamps:String = 'no_courrier,';
		var strValues:String = iNoCourrier + ',';
		var bQuery:Boolean = true;
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.firstChild.nodeType == 1){
				//found a ROW
				bQuery = false;
				this.parseXmlCourrierAttachement(currNode, iNoCourrier);
			}else{
				//found an ITEM
				strChamps += currNode.attributes.n + ',';
				strValues += "'" + String(currNode.firstChild.nodeValue) + "',";
				}
			}
		//finish the query
		if(bQuery){
			cDbManager.queryDB("INSERT INTO attachement (" + strChamps.substring(0, strChamps.length - 1) + ") VALUES (" + strValues.substring(0, strValues.length - 1) + ");");
			}		
		};	

	/**START NEW OBJECt FOR MESSAGES**************************************************************************************/	
	
	public function sendInstantMsg(cbClass:Object, arrMiniProfil:Array, msg:String):Void{
		//check if the instant msg is not  empty
		if (msg != ''){
			this.__cActionMessages = new CieActionMessages(gLang[78], '');
			this.__cActionMessages.setLoading();
			this.treatSendInstantMsg(cbClass, arrMiniProfil, msg);
		}else{
			//if it's empty show error
			this.__cActionMessages.closeWindow();
			new CieTextMessages('MB_OK', gLang[79], gLang[80]);       
			}
			
		};
		
		
	/*************************************************************************************************************************************************/	
	
	public function treatSendInstantMsg(cbClass:Object, arrUser:Array, msg:String):Void{
		//do a request to send an instant
		if (arrUser['no_publique'] != undefined && arrUser['no_publique'] != '' && arrUser['no_publique'] != 0 && arrUser['no_publique'] != null){
			var arrD = new Array();
			arrD['methode'] = 'instant';
			arrD['action'] = 'add';
			arrD['arguments'] = arrUser['no_publique'] + ',' + escape(msg);
			//add the request		
			cReqManager.addRequest(arrD, this.cbSendInstantMsg, {__class:this, __cbClass:cbClass, __user:arrUser, __msg:msg});  
		}else{
			this.__cActionMessages.closeWindow();
			new CieTextMessages('MB_OK', gLang[81], gLang[82]);       
			}			
		};
	
	/*************************************************************************************************************************************************/	
	
	public function cbSendInstantMsg(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlSendInstantMsg(obj.__req.getXml().firstChild, obj.__super.__cbClass, obj.__super.__user, obj.__super.__msg);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
	
	/*************************************************************************************************************************************************/	
	
	public function parseXmlSendInstantMsg(xmlNode:XMLNode, cbClass:Object, arrUser:Array, msg:String):Void{
		var str:String = xmlNode.childNodes[0].firstChild.nodeValue;
		var strErrAttr:String = xmlNode.childNodes[0].attributes.n;
		
		var strError:String = '';
		var bAboRedirect:Boolean = false;	

		if(str != undefined){
			if(strErrAttr == 'error'){
				if(str == 'ERROR_INSTANT_SENDING_RIGHTS'){
					bAboRedirect = true;
				}else if(str == 'ERROR_INSTANT_LIMIT_FOUND'){
					strError = gLang[331];
				}else if(str == 'ERROR_MEMBER_DESACTIVE'){
					strError = gLang[333];	
				}else{
					Debug('***ERR_SEND_INSTANT: ' + str);
					strError = gLang[84];
					}
			}else{
				//if no error add to the local DB
				//get n0_instant and date
				var arrStr = str.split('|');
				//insert the instant message into instant table
				//lu est a 1 par defaut car vient de nous
				cDbManager.queryDB("INSERT INTO instant (no_instant, type, cdate, msg, no_publique, lu) VALUES ('" + arrStr[0] + "','1','" + arrStr[1] + "','" + escape(msg) + "','" + arrUser['no_publique'] + "','1');");
				// update the members table
				if(gDbKey[arrUser['no_publique']] != undefined){
				//if(cDbManager.dbKeyExist('no_publique', arrUser['no_publique'])){ 
					cDbManager.queryDB("UPDATE members SET msg_instant = '1' WHERE no_publique = " + arrUser['no_publique'] + ";");
				// insert the member's profil into members table	
				}else{ 
					cDbManager.queryDB("INSERT INTO members (no_publique, pseudo, age, ville_id, region_id, code_pays, album, photo, vocal, membership, orientation, sexe, titre, relation, etat_civil, msg_instant) VALUES ('" + arrUser['no_publique'] + "','" + arrUser['pseudo'] + "','" + arrUser['age'] + "','" + arrUser['ville_id'] + "','" + arrUser['region_id'] + "','" + arrUser['code_pays'] + "','" + arrUser['album'] + "','" + arrUser['photo'] + "','" + arrUser['vocal'] + "','" + arrUser['membership'] + "','" + arrUser['orientation'] + "','" + arrUser['sexe'] + "','" + arrUser['titre'] + "','" + arrUser['relation'] + "','" + arrUser['etat_civil'] + "', '1');");
					}
				//show messages	
				cbClass.showInstantMsg();
				//refresh the communication section
				if(secMessage != undefined){
					secMessage.refreshSection('communications');	
					}
				}
		}else{
			strError = gLang[84];
			}
		//remove the loading window	
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		//show error
		if(strError != ''){
			new CieTextMessages('MB_OK', strError, gLang[85]);       
			}
		//abo redirection	
		if(bAboRedirect){
			this.chatAbonnement();
			}
		};
	
	
	/*************************************************************************************************************************************************/	
	
	public function removeInstantMsg(cbClass:Object, arrUser:Array, no_instant:Number):Void{
		//remove the loading window
		this.__cActionMessages = new CieActionMessages(gLang[86], '');
		this.__cActionMessages.setLoading();
		//call the function to do a request
		this.treatRemoveInstantMsg(cbClass, arrUser, no_instant);			
		};
	
	/*************************************************************************************************************************************************/
	
	public function treatRemoveInstantMsg(cbClass:Object, arrUser:Array, no_instant:Number):Void{
		//do a request to remove an instant
		var arrD = new Array();
		arrD['methode'] = 'instant';
		arrD['action'] = 'remove';
		arrD['arguments'] = no_instant;
		//add the request		
		cReqManager.addRequest(arrD, this.cbRemoveInstantMsg, {__class:this, __cbClass:cbClass, __arrUser: arrUser, __noInstant:no_instant});               	   
		};
	
	/*************************************************************************************************************************************************/
	
	public function cbRemoveInstantMsg(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlRemoveInstantMsg(obj.__req.getXml().firstChild, obj.__super.__cbClass, obj.__super.__arrUser, obj.__super.__noInstant);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
	
	/*************************************************************************************************************************************************/	
	
	public function parseXmlRemoveInstantMsg(xmlNode:XMLNode, cbClass:Object, arrUser:Array, no_instant:Number):Void{
		var str:String = xmlNode.childNodes[0].firstChild.nodeValue;
		var strErrAttr:String = xmlNode.childNodes[0].attributes.n;
		var strError:String = '';
		//remove the loading
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		if(str != undefined){
			if(strErrAttr == 'error' && str != 'ERROR_INSTANT_NOT_DELETED'){
				if(str == 'ERROR_INSTANT_NOT_DELETED'){
					strError = '';
				}else{
					Debug('***ERR_REMOVE_INSTANT: ' + str);
					strError = gLang[84];
					}
			}else{
				//if no error delete from local DB
				// delete the msg from the local DB
				cDbManager.queryDB("DELETE * FROM instant WHERE instant.no_instant = " + no_instant + ";"); 
				//check if we still got messages from this member if not update the members table
				var strSql:String = "SELECT no_instant FROM instant WHERE no_publique = " + arrUser['no_publique'] + ";";	
				var arrRows:Array = cDbManager.selectDB(strSql);				
				if (arrRows.length <= 0){				
					cDbManager.queryDB("UPDATE members SET msg_instant = '0' WHERE no_publique = " + arrUser['no_publique'] + ";");
					}
				//show messages
				cbClass.showInstantMsg();	
				//refresh the communication section
				if(secMessage != undefined){
					secMessage.refreshSection('communications');	
					}
				}
		}else{
			strError = gLang[87];
			}
		//show error
		if(strError != ''){
			new CieTextMessages('MB_OK', strError, gLang[88]);       
			}
			
		};
	
	/*************************************************************************************************************************************************/	
	
	public function sendCourriel(cbClass:Object, arrFileBrowse:Array, arrUser:Array, sujet:String, msg:String, bCopy:Boolean, bReply:Boolean, bAttach:Boolean):Void{
		// if the email is empty we don't send the email
		if (sujet != '' || msg != '' || bAttach == 1){
			if (sujet == ''){
				sujet = '...';
				}
				
			var UTCdate = new Date();
			UTCdate = UTCdate.valueOf();
			
			var obj:Object = new Object();
			obj.__cbClass = cbClass;
			obj.__arrFile = arrFileBrowse;
			obj.__date = UTCdate;
			obj.__arrUser = arrUser;
			obj.__sujet = sujet;
			obj.__msg = msg;
			obj.__copy = bCopy;
			obj.__reply = bReply;
			obj.__attach = bAttach;			
			
			//set loading
			this.__cActionMessages = new CieActionMessages(gLang[89], '');
			if(bAttach){
				this.__cActionMessages.setProgress();
			}else{	
				this.__cActionMessages.setLoading();
				}
			// the server for the upload
			this.__serveurUpload = BC.__server.__attachement + "?&p=" + obj.__arrUser['pseudo'] + "&t=" + obj.__date;
			this.__arrAttachFiles = obj.__arrFile;
			//send the  attachements
			if(bAttach){
				this.treatSendAttach(cbClass, obj);	
			}else{
				this.treatSendCourriel(cbClass, obj);	
				}
		}else{
			this.__cActionMessages.closeWindow();
			new CieTextMessages('MB_OK', gLang[90], gLang[91])
			}			
		};
	
	/*************************************************************************************************************************************************/
	
	public function treatSendCourriel(cbClass:Object, obj:Object):Void{
		//if the pub number exists we do a request
		if (obj.__arrUser['no_publique'] != undefined && obj.__arrUser['no_publique'] != '' && obj.__arrUser['no_publique'] != 0 && obj.__arrUser['no_publique'] != null){
						
			var arrD = new Array();
			arrD['methode'] = 'courrier';
			arrD['action'] = 'sendemail';
			arrD['arguments'] = obj.__arrUser['pseudo'] + ',' + escape(obj.__sujet) + ',' + escape(obj.__msg) + ',' + obj.__copy + ',' + obj.__reply + ',' + obj.__attach + ',' + obj.__date;
			
			//add the request		
			cReqManager.addRequest(arrD, this.cbSendCourriel, {__class:this, __cbClass:cbClass, __obj:obj});  
		}else{
			//show the error
			this.__cActionMessages.closeWindow();
			new CieTextMessages('MB_OK', gLang[92], gLang[93]);	
			}		
		};
	
	/*************************************************************************************************************************************************/
	
	public function cbSendCourriel(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlSendCourriel(obj.__req.getXml().firstChild, obj.__super.__cbClass, obj.__super.__obj);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
	
	/*************************************************************************************************************************************************/	
	
	public function parseXmlSendCourriel(xmlNode:XMLNode, cbClass:Object, obj:Object):Void{
		var strError:String = '';
		var bAboRedirect:Boolean = false;		
		
		if(xmlNode != undefined){
			for (var iIndex=0; iIndex<xmlNode.childNodes.length; iIndex++){
				if (xmlNode.childNodes[iIndex].attributes.n == 'error'){
					var str = xmlNode.childNodes[iIndex].firstChild.nodeValue;
					xmlNode = null;
					delete xmlNode;
					if(str == 'EMAIL_ERR_NOT_MEMBER'){
						strError = gLang[94];
					}else if(str == 'EMAIL_ERR_MEMBER_DESACTIVE'){
						strError = gLang[95];
					}else if(str == 'EMAIL_ERR_DES_BOX_FULL'){
						strError = gLang[96];
					}else if(str == 'EMAIL_ERR_ORI_BOX_FULL'){
						strError = gLang[97];
					}else if(str == 'MAIL_ERROR'){
						strError = gLang[98];					
					}else if(str == 'ERROR_EMAIL_SENDING_RIGHTS'){
						bAboRedirect = true;		
					}else if(str == 'ERROR_MEMBER_DESACTIVE'){
						strError = gLang[333];	
					}else{
						strError = gLang[84];
						}
				}else{
					//if no error add to local DB
					//user infos at childNode[0]
					var arrInfos:Array = xmlNode.childNodes[iIndex].childNodes[0].firstChild.nodeValue.split('|');
					//if the user doesn't want to save a copy of the email we don't add the member in the members table
					//if the user wants to save a copy 
					if (obj.__copy != 0){
						//then we check if the member is already in the members table, then we update the table, 
						if(gDbKey[arrInfos[0]] != undefined){
						//if(cDbManager.dbKeyExist('no_publique', arrInfos[0])){
							cDbManager.queryDB("UPDATE members SET msg_courrier = '1' WHERE no_publique = " + arrInfos[0] + ";");
						//if the member is not in the table, we add him/her.	
						}else{
							cDbManager.queryDB("INSERT INTO members (no_publique, pseudo, age, ville_id, region_id, code_pays, album, photo, vocal, membership, orientation, sexe, titre, relation, etat_civil, msg_courrier) VALUES ('" + arrInfos[0] + "','" + arrInfos[1] + "','" + arrInfos[2] + "','" + arrInfos[3] + "','" + arrInfos[4] + "','" + arrInfos[5] + "','" + arrInfos[6] + "','" + arrInfos[7] + "','" + arrInfos[8] + "','" + arrInfos[9] + "','" + arrInfos[10] + "','" + arrInfos[11] + "','" + arrInfos[12] + "','" + arrInfos[13] + "','" + arrInfos[14] + "', '1');");
							}						
						this.parseXmlSendCourrielMessage(xmlNode.childNodes[iIndex].childNodes[1], arrInfos[0]);						
						}					
					}               
				}
			
			//show messages			
			cbClass.showCourrielMsg();				
			//refresh the communication section 
			if(secMessage != undefined){
				secMessage.refreshSection('communications');	
				}				
		}else{
			strError = gLang[99];
			}
		//close the loading window
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		//show error
		if(strError != ''){
			new CieTextMessages('MB_OK', strError, gLang[100]);       
			}
		//abo redirect
		if(bAboRedirect){
			this.chatAbonnement();       
			}	
			
		};	
	
	/*************************************************************************************************************************************************/	
	
	private function parseXmlSendCourrielMessage(xmlNode:XMLNode, iNoPub:Number):Void{           
		var strChamps:String = 'no_publique,';
		var strValues:String = iNoPub + ',';
		var iNoCourrier:Number = 0;
		var bQuery:Boolean = true;
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.firstChild.nodeType == 1){
				//found a ROW
				if(currNode.attributes.n == 'attachement'){
					this.parseXmlSendCourrielAttachement(currNode, iNoCourrier);
				}else{
					bQuery = false;
					this.parseXmlSendCourrielMessage(currNode, iNoPub);
					}
			}else{
				if(currNode.attributes.n == 'no_courrier'){
					strChamps += currNode.attributes.n + ',';
					strValues += Number(currNode.firstChild.nodeValue) + ",";
					iNoCourrier = Number(currNode.firstChild.nodeValue);
				}else if(currNode.attributes.n != 'attachement'){
					strChamps += currNode.attributes.n + ',';
					strValues += "'" + String(currNode.firstChild.nodeValue) + "',";
					}
				}
			}
			
		//finish the query
		if(bQuery){
			cDbManager.queryDB("INSERT INTO courrier (" + strChamps.substring(0, strChamps.length - 1) + ") VALUES (" + strValues.substring(0, strValues.length - 1) + ");");			
			}               
		};     
	
	
	/*************************************************************************************************************************************************/
	
	//parse the attachement of courriers
	private function parseXmlSendCourrielAttachement(xmlNode:XMLNode, iNoCourrier:Number):Void{
		var strChamps:String = 'no_courrier,';
		var strValues:String = iNoCourrier + ',';
		var bQuery:Boolean = true;
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.firstChild.nodeType == 1){
				//found a ROW
				bQuery = false;
				this.parseXmlSendCourrielAttachement(currNode, iNoCourrier);
			}else{
				//found an ITEM
				strChamps += currNode.attributes.n + ',';
				strValues += "'" + String(currNode.firstChild.nodeValue) + "',";
				}
			}
		//insert into attachement table
		if(bQuery){
			cDbManager.queryDB("INSERT INTO attachement (" + strChamps.substring(0, strChamps.length - 1) + ") VALUES (" + strValues.substring(0, strValues.length - 1) + ");");			
			}              
		};  


	/*************************************************************************************************************************************************/

	public function treatSendAttach(cbClass:Object, obj:Object):Void{
		
		this.__isAttachFinished = true;
		//send the attaches
		for(var o in this.__arrAttachFiles){
			this.__isAttachFinished = false;
			
			var listener:Object = new Object();								
			listener.__arrAttachFiles = this.__arrAttachFiles;	
			listener.__obj = obj;	
			listener.__cbClass = cbClass; 	
			
			listener.onComplete = function(file:FileReference):Void{			
				//once an attachment is sent we send the next one
				this.__arrAttachFiles[file.name] = null;
				delete this.__arrAttachFiles[file.name];
				file.removeListener(this);
				cFunc.treatSendAttach(this.__cbClass, this.__obj);
				};
			
			/*	
			listener.onOpen = function(file:FileReference):Void{
				//Debug("onOpen -- : " + file.name);
				};
			*/
			/*		
			listener.onCancel = function(file:FileReference):Void {
				//Debug("onCancel -- : " + file.name);
				};
			*/	
			listener.__actionMessage = this.__cActionMessages;
			listener.onProgress = function(file:FileReference, bytesLoaded:Number, bytesTotal:Number):Void{
				//Debug("PROGRESS: " + file.name + " " + bytesLoaded + "/" + bytesTotal);
				//this.__actionMessage.setLoadingProgress("PROGRESS: " + file.name + " " + bytesLoaded + "/" + bytesTotal);
				this.__actionMessage.setLoadingProgress(file.name, bytesLoaded, bytesTotal);
				};
			//upload the file
			this.__arrAttachFiles[o].__file.addListener(listener);
			this.__arrAttachFiles[o].__file.upload(this.__serveurUpload);
			break;
			}
		//when all the attachs are sent we send the email
		if(this.__isAttachFinished){
			this.treatSendCourriel(cbClass, obj);
			}
		};
	
	/*************************************************************************************************************************************************/
	
	public function removeCourriel(cbClass:Object, arrUser:Array, no_courriel:Number):Void{
		//set loadind
		this.__cActionMessages = new CieActionMessages(gLang[101], '');
		this.__cActionMessages.setLoading();
		//call the function to do a request
		this.treatRemoveCourriel(cbClass, arrUser, no_courriel);			
		}; 
	
	/*************************************************************************************************************************************************/
	
	public function treatRemoveCourriel(cbClass:Object, arrUser:Array, no_courriel:Number):Void{
		//do a request to remove an email
		var arrD = new Array();
		arrD['methode'] = 'courrier';
		arrD['action'] = 'deleteemail';
		arrD['arguments'] = no_courriel;
		//add the request		
		cReqManager.addRequest(arrD, this.cbRemoveCourriel, {__class:this, __cbClass:cbClass, __arrUser: arrUser, __noCourriel:no_courriel});     	   
		};
	
	/*************************************************************************************************************************************************/
	
	public function cbRemoveCourriel(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlRemoveCourriel(obj.__req.getXml().firstChild, obj.__super.__cbClass, obj.__super.__arrUser, obj.__super.__noCourriel);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
	
	/*************************************************************************************************************************************************/
	
	public function parseXmlRemoveCourriel(xmlNode:XMLNode, cbClass:Object, arrUser:Array, no_courriel:Number):Void{
		var str:String = xmlNode.childNodes[0].firstChild.nodeValue;
		var strErrAttr:String = xmlNode.childNodes[0].attributes.n;
		var strError:String = '';		
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		if(str != undefined){
			if(strErrAttr == 'error' && str != 'ERROR_NO_EMAIL_TO_DELETE'){
				if(str == 'ERROR_NO_EMAIL_TO_DELETE'){
					strError = '';
				}else{
					Debug('***ERR_REMOVE_MAIL: ' + str);
					strError = gLang[84];
					}
			}else{
				//if no error we delete email from the local DB
				// delete emails from the local DB
				cDbManager.queryDB("DELETE * FROM courrier WHERE courrier.no_courrier = " + no_courriel + ";"); 
				// delete the attachments from the local DB
				cDbManager.queryDB("DELETE * FROM attachement WHERE attachement.no_courrier = " + no_courriel + ";");
				//check if we still got messages from this user
				var strSql:String = "SELECT no_courrier FROM courrier WHERE no_publique = " + arrUser['no_publique'] + ";";	
				var arrRows:Array = cDbManager.selectDB(strSql);
				//if we don't have any message from this member, we update the members table
				if (arrRows.length <= 0){				
					cDbManager.queryDB("UPDATE members SET msg_courrier = '0' WHERE no_publique = " + arrUser['no_publique'] + ";");
					}
				//display emails
				cbClass.showCourrielMsg();	
				//refresh the communication section
				if(secMessage != undefined){
					secMessage.refreshSection('communications');	
					}
				}
		}else{
			strError = gLang[102];
			}
		
		if(strError != ''){			
			new CieTextMessages('MB_OK', strError, gLang[103]);       			
			}			
		};
	
	/*************************************************************************************************************************************************/
	
	public function getAttach(fileName:String, no_courrier:Number):Void{
		//the link for the download
		var serverDownload = BC.__server.__dattachement + '?n=' + BC.__user.__nopub + '&p=' + BC.__user.__pseudo + '&e=' + no_courrier + '&f=' + fileName;		
		getURL(serverDownload);	
		};
	
	/**END NEW OBJECt FOR MESSAGES********************************************************************************************************************/	
	
	
	/***SESSION ID**********************************************************************************************************************************************/
	
	public function startKeepAlive(bState:Boolean):Void{
		//set the thread for keeping session alive
		if(bState){
			if(this.__cThreadKeepAlive == undefined){
				this.__cThreadKeepAlive = cThreadManager.newThread(1800000, this, 'keepAlive', {});
				}
		}else{
			this.__cThreadKeepAlive.destroy();
			this.__cThreadKeepAlive = undefined;
			}
		};
	
	public function keepAlive(obj:Object):Boolean{
		var arrD = new Array();
		arrD['methode'] = 'keepalive';
		arrD['action'] = '';
		arrD['arguments'] = '';
		cReqManager.addRequest(arrD, cFunc.cbKeepAlive, null);
		return true;
		};
		
	public function cbKeepAlive(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		// remove request
		cFunc.parseKeepAlive(obj.__req.getXml().firstChild);
		cReqManager.removeRequest(obj.__req.getID());
		};
	
	public function parseKeepAlive(xmlNode:XMLNode):Void{
		var strError:String = '';
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.attributes.n == 'error'){
				if(currNode.firstChild.nodeValue == 'ERROR_SESSIONID'){
					cFunc.startKeepAlive(false);
					cFunc.getNewSessionID();					
					}
				}
			}
		};
		
	public function getNewSessionID(Void):Void{
		var arrD = new Array();
		arrD['methode'] = 'login';
		arrD['action'] = '';
		arrD['arguments'] = '';
		cReqManager.addRequest(arrD, cFunc.cbGetNewSessionID, null);
		};
	
	public function cbGetNewSessionID(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		// remove request
		cFunc.parseNewSessionID(obj.__req.getXml().firstChild);
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	public function parseNewSessionID(xmlNode:XMLNode):Void{
		/*
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.attributes.n == 'nopub'){
				BC.__user.__nopub = currNode.firstChild.nodeValue;
				Debug('NOPUB: ' + BC.__user.__nopub);
			}else if(currNode.attributes.n == 'session'){
				BC.__user.__sessionID = currNode.firstChild.nodeValue;
				Debug('SESSIONID: ' + BC.__user.__sessionID);
				}
			}
		*/
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.attributes.n != 'error'){
				if(currNode.attributes.n == 'nopub'){
					BC.__user.__nopub = currNode.firstChild.nodeValue;
					Debug('NOPUB: ' + BC.__user.__nopub);
				}else if(currNode.attributes.n == 'session'){
					BC.__user.__sessionID = currNode.firstChild.nodeValue;
					Debug('SESSIONID: ' + BC.__user.__sessionID);
				}else if(currNode.attributes.n == 'photoflag'){
					BC.__user.__photo = currNode.firstChild.nodeValue;
					Debug('PHOTOFLAG: ' + BC.__user.__photo);
				}else if(currNode.attributes.n == 'visitors'){
					BC.__user.__visitors = currNode.firstChild.nodeValue;
					Debug('VISITEUR: ' + BC.__user.__visitors);
				}else if(currNode.attributes.n == 'onlineusers'){
					BC.__user.__onlineusers = currNode.firstChild.nodeValue;
					Debug('ONLINE: ' + BC.__user.__onlineusers);
				}else if(currNode.attributes.n == 'messages_concat'){
					BC.__user.__msgcount = new Array();
					var arrSplit = currNode.firstChild.nodeValue.toString().split(',');
					BC.__user.__msgcount['instant'] = arrSplit[0];
					BC.__user.__msgcount['express'] = arrSplit[1];
					BC.__user.__msgcount['courriel'] = arrSplit[2];
					BC.__user.__msgcount['vocal'] = arrSplit[3];
					for(var o in BC.__user.__msgcount){
						Debug('MESSAGES[' + o + ']: ' + BC.__user.__msgcount[o]);
						}
					}
				cFunc.startKeepAlive(true);	
			}else{
				cFunc.startKeepAlive(false);	
				cFunc.getNewSessionID();
				}
			}		
		};

		
	/***ADDALBUM RIGHT FRO CARNET*****************************************************************************************************************/	

	public function giveAlbumRights(arrUsers:Array, strSection:String):Void{
		if(arrUsers.length > 0){
			//this.__cActionMessages = new CieActionMessages(gLang[104], gLang[105] + arrUsers.length + gLang[106]);
			this.__cActionMessages = new CieActionMessages(gLang[104], gLang[105] + ' ' + gLang[106]);
			this.__cActionMessages.setCallBackFunction(this.cbPromptGiveAlbumRights, {__class: this, __users:arrUsers, __section:strSection});
		}else{
			new CieTextMessages('MB_OK', gLang[107], gLang[108]);
			}
		};
		
	//callback when user press button pour album rights
	public function cbPromptGiveAlbumRights(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptGiveAlbumRights(bResult, cbObject.__users, cbObject.__section);
		};	
		
	//give the album roghts
	public function treatUserActionOnPromptGiveAlbumRights(bResult:Boolean, arrUser:Array, strSection:String):Void{
	//depending on the user selection
		if(bResult){
			//do a request to remove from the carnet
			var arrD = new Array();
			arrD['methode'] = 'carnet';
			arrD['action'] = 'albumrights';
			arrD['arguments'] = arrUser.toString();
			//add the request
			//Debug("REQUEST[carnet::remove]: " + arrD['arguments']);
			cReqManager.addRequest(arrD, this.cbGiveAlbumRights, {__class:this, __users:arrUser, __section:strSection});	
			//put a loader
			this.__cActionMessages.setLoading();
		}else{
			//user have pressed cancel thrn close the window
			this.__cActionMessages.closeWindow();
			this.__cActionMessages = null;	
			}
		};	
	
	public function cbGiveAlbumRights(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlGiveAlbumRights(obj.__req.getXml().firstChild, obj.__super.__users, obj.__super.__section);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};	
		
	public function parseXmlGiveAlbumRights(xmlNode:XMLNode, arrUsers:Array, strSection:String):Void{
		str = xmlNode.childNodes[0].firstChild.nodeValue;
		strError = '';
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		if(str == undefined){
			strError = gLang[109];
			}
		if(strError != ''){
			new CieTextMessages('MB_OK', strError, gLang[110]);
		}else{
			if(strSection == 'message' && secMessage != undefined){
				secMessage.refreshSection('communications');
			}else if(strSection == 'bottin' && secBottin != undefined){
				secBottin.refreshSection('carnet');
				}
			}
		};	
		
	/******GET NEW MESSAGE FLAG************************************************************************************************************************/
	
	public function getNewMessagesFlag(Void):Void{
		var bMsg:Boolean = false;
		for(var o in BC.__user.__msgcount){
			if(BC.__user.__msgcount[o] > 0){
				bMsg = true;
				break;
				}
			}
		if(bMsg){
			cToolManager.getTool('messages', 'message').blinkEffect(true);
			}
		};
		
	/*
	public function getNewMessagesFlag(Void):Void{
		var arrD = new Array();
		arrD['methode'] = 'messages';
		arrD['action'] = 'all';
		arrD['arguments'] = '';
		//add the request
		cReqManager.addRequest(arrD, this.cbGetNewMessagesFlag, {__class:this});
		};
	
	public function cbGetNewMessagesFlag(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlGetNewMessagesFlag(obj.__req.getXml().firstChild);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	public function parseXmlGetNewMessagesFlag(xmlNode:XMLNode):Void{
		str = xmlNode.childNodes[0].firstChild.nodeValue;
		//Debug('MSG_FLAG:' + str);
		if(str != undefined){
			if(str == 'true'){
				//set the icon to tell we have new messages
				cSysTray.changeSysTrayIcon('cie_msg.ico');
				//make the message icon blink
				cToolManager.getTool('messages', 'message').blinkEffect(true);
				}
			}
		};	
	*/	
	
	/***UPDATES**********************************************************************************************************************************/
	
	public function checkForUpdates(Void):Void{
		var __path = mdm.String.replace(mdm.Application.path, "\\", "/");
		this.__cUpdate = new CieUpdate(BC.__server.__updates, 'version.xml', __path, 'updates.zip', cRegistry.getKey('version'), this.gotNewUpdates, this);
		};
		
	public function gotNewUpdates(cbClass:Object, objMsg:Object):Void{
		if(!BC.__user.__autoupdate){
			cbClass.__cActionMessages = new CieActionMessages(gLang[111], objMsg[BC.__user.__lang]);
			cbClass.__cActionMessages.setCallBackFunction(cbClass.cbPromptNewUpdates, {__class: cbClass});
		}else{
			cbClass.__cUpdate.loadUpdates();
			}
		};
		
	//callback when user press button
	public function cbPromptNewUpdates(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptNewUpdates(bResult);
		};
		
	//keep the scope so have to pass trought another function to 	
	public function treatUserActionOnPromptNewUpdates(bResult:Boolean):Void{
		//depending on the user selection
		if(bResult){
			//close the prompt
			this.__cActionMessages.closeWindow();
			//get the updates
			this.__cUpdate.loadUpdates();
		}else{
			//user have pressed cancel thrn close the window
			this.__cActionMessages.closeWindow();
			}
		this.__cActionMessages = null;	
		};	
	
	//called by CieUpdate
	public function updatesAreInstalled(Void):Void{
		//tell the user we need to restart the application
		this.__cActionMessages = new CieTextMessages('MB_OK', gLang[112], gLang[113]);
		this.__cActionMessages.setCallBackFunction(this.restartApplication, this);
		};
		
	
	/**APPLICATION********************************************************************************************************************************************/
	
	public function restartApplication(Void):Void{
		//Debug('RESTARTING UNI');
		//start a new one
		//mdm.System.exec(mdm.Application.path + 'WinLoader.exe');
		mdm.System.exec(mdm.Application.path + 'UNI2.exe');
		//close this one
		cFunc.exitApplication();
		};
		
	/**DIRECTX********************************************************************************************************************************************/
	/*
	public function enableDirectX(Void):Void{
		if(!this.__directXEnabled){
			this.__directXEnabled = true;
			mdm.System.DirectX.enable(1024, 768, 24);
		}else{
			this.__directXEnabled = false;
			mdm.System.DirectX.disable();
			}
		}
	*/	
	
	/**recherche detaillees******************************************************************************************************************/
	public function loadRechercheDetaillee(Void):Void{
		//Debug('loadRechercheDetaillee');
		if(!this.__bRechercheDetailleeIsLoaded){
			secRecherche.refreshSection('detaillees');
			this.__bRechercheDetailleeIsLoaded = true;
			}
		};
		
		
	/**VIDEO ASK********************************************************************************************************************************************/	
	public function askForVideoDescriptionMethod(Void):Void{
		var arrMethod = [['upload', gLang[450]],['webcam', gLang[451]]];
		this.__cActionMessages = new CieOptionMessages(gLang[452], arrMethod, gLang[438]);
		this.__cActionMessages.setSelectionValue(0);
		this.__cActionMessages.setCallBackFunction(this.cbAskForVideoDescriptionMethod, {__class: this, __arrMethod:arrMethod});
		};
		
	public function cbAskForVideoDescriptionMethod(cbObject:Object):Void{
		if(cbObject.__ok == true){
			cbObject.__class.popVideoWizard(cbObject.__arrMethod[cbObject.__class.__cActionMessages.getSelectedChoice()][0]);
			}
		//clear the holder var
		cbObject.__class.__cActionMessages = null;
		};
		
	public function popVideoWizard(strMethod:String):Void{
		this.__cActionMessages = null;
		if(strMethod == 'upload'){
			this.__cVideoMessages = new CieVideoUploadMessages();
		}else{
			cFunc.openRecord();
			}
		};
		
	public function clearVideoUploadMessagesClass(Void):Void{
		Debug("clearVideoUploadMessagesClass()");
		delete this.__cVideoMessages;
		this.__cVideoMessages = null;
		};
		
			
	/* KEY LISTENER ***********************************************************************************************************************/
	/*
	public function setSubTabSection(ctype:String):Void{
		this.__lastSubSection = ctype;
		};
	*/
	/*
	public function addKeyListener(bEnable:Boolean):Void{
		if(bEnable){
			this.__keyListener = new Object();
			this.__keyListener.__sup = this;
			this.__keyListener.onKeyUp = function(){
					if(Key.getCode() == Key.ENTER){
						Debug("SECTION: " + this.__sup.__lastSection + '[' + this.__sup.__lastSubSection + ']');
						if(this.__sup.__strTabFocusOn != '' && this.__sup.__strTabFocusOn != undefined){
							Debug('K_LIST : ' + this.__sup.__strTabFocusOn);
							//cFormManager.sendForm(this.__sup.__strTabFocusOn);
							}
						}
					};
			Key.addListener(this.__keyListener);
		}else{
			Key.removeListener(this.__keyListener);
			delete this.__keyListener;
			}
		};		
	*/
	/*************************************************************************************************************************************************/
	
	/*
	public function getClassName(Void):String{
		return __className;
		};
	*/
	/*************************************************************************************************************************************************/
	/*
	public function getClass(Void):CieFunctions{
		return this;
		};
	*/	
	}	