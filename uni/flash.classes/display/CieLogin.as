/*

section login

cette section est le starter de tout une fois le PHPSESSION UNI::PHP recupere, il fait sa demande au serveur UNI:JAVA:
ouvre les fsection set le loading bar et reset les tray menu

*/

import control.CieTextLine;
import control.CieButton;
import messages.CieLostPsw;

import messages.CieTextMessages;

import graphic.CieGradientRoundedSquare;
import flash.filters.GlowFilter;


dynamic class display.CieLogin{

	static private var __className = 'CieLogin';
	static private var __instance:CieLogin;
	private var __type:String;
	private var __hvSpacer:Number;
	private var __hBox:Number;
	private var __h:Number;
	private var __keyListener:Object;
	private var __selectBoxWidth:Number;
	private var __arrBoxes:Array;
	
	//thread
	private var __cThreadConnect:Object;
	private var __cThreadUserInterface:Object;
	private var __cThreadUserBasic:Object;
	private var __cThreadUserNewMsg:Object;
		
	private function CieLogin(Void){
		this.__hvSpacer = 10;
		this.__hBox = 20;
		this.__hButt = 30;
		this.__selectBoxWidth = 185;
		this.__h = 0;
		this.__arrBoxes = new Array();
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
		this.__cThreadUserNewMsg.destroy();
		//this.__cThreadPub.destroy();

		this.__cThreadConnect = null;;
		this.__cThreadUserInterface = null;
		this.__cThreadUserBasic = null;
		this.__cThreadUserNewMsg = null;
		//this.__cThreadPub = null;
		
		this.__h = 0;
		};
		
	/*************************************************************************************************************************************************/	
	
	public function openLogin(strError:String):Void{
		//open the tab to write on it
		cContent.openTab(['login']);
		//filters effect 
		var filterArray:Array = new Array(new GlowFilter(CieStyle.__welcome.__effGlowColor, 0.3, 4, 4, 2, 3, false, false));
		//class ref
		var panelClass = cContent.getPanelClass(['login','_tl']);
		//BG
		panelClass.setContent('mvLogin');
		//the size
		var oSize:Object = panelClass.getPanelSize();
		//panel content
		var mvPanel:MovieClip = panelClass.getPanelContent();
				
		//----LOGIN BOX
		this.__arrBoxes['enter'] = mvPanel.createEmptyMovieClip('BOX_enter', mvPanel.getNextHighestDepth());
		//logo icon
		var mvLogo = this.__arrBoxes['enter'].attachMovie('mvIconImage_32', 'I_0', this.__arrBoxes['enter'].getNextHighestDepth());
		mvLogo._x = 2;
		mvLogo._y = -45;
		//text next to the logo
		var mvTitle = this.__arrBoxes['enter'].attachMovie('mvAide', 'T_0', this.__arrBoxes['enter'].getNextHighestDepth());
		mvTitle.txtInfos.autoSize = 'left';	
		mvTitle.txtInfos._width = 300;	
		mvTitle._x = 67;
		mvTitle._y = -39;
		mvTitle.txtInfos.htmlText = '<font color="' + CieStyle.__welcome.__htmlTxTitleColor + '" size="+4"><b>' + gLang[600] + '</b></font>\n<font color="' + CieStyle.__welcome.__htmlTxTitleColor + '"><b>' + gLang[601] + '</b></font>';
		//corners graditne tbox around the login
		new CieGradientRoundedSquare(this.__arrBoxes['enter'], (oSize.__width - (this.__hvSpacer * 2)), 200);
		this.__arrBoxes['enter']._x = this.__hvSpacer;
		this.__arrBoxes['enter']._y = 70;
		this.__arrBoxes['enter'].filters = filterArray;
		//pseudo box and input pseudo box
		new CieTextLine(this.__arrBoxes['enter'], this.__hvSpacer, 19, 0, this.__hBox, 'tPseudo', gLang[195] + ':', 'dynamic',[], false, false, false, false, [CieStyle.__welcome.__hexTxColor, 11]);
		var pseudoBox = new CieTextLine(this.__arrBoxes['enter'], this.__hvSpacer, 36, this.__selectBoxWidth, this.__hBox, 'pseudo', BC.__user.__pseudo, 'input',[], false, true, false, true, [CieStyle.__welcome.__hexTxColor, 11]);
		Selection.setFocus(pseudoBox.getTextField());
		//psw box and input psw box
		new CieTextLine(this.__arrBoxes['enter'], this.__hvSpacer, 58, 0, this.__hBox, 'tPsw', gLang[196] + ':', 'dynamic',[], false, false, false, false, [CieStyle.__welcome.__hexTxColor, 11]);
		var pswBox = new CieTextLine(this.__arrBoxes['enter'], this.__hvSpacer, 75, this.__selectBoxWidth, this.__hBox, 'psw', BC.__user.__psw, 'input',[], false, true, true, true, [CieStyle.__welcome.__hexTxColor, 11]);
		//instance of the send button
		var button = new CieButton(this.__arrBoxes['enter'], gLang[197], this.__selectBoxWidth, this.__hButt, this.__hvSpacer, 112);
		button.getMovie().__sup = this;
		button.getMovie().__pseudo = pseudoBox;
		button.getMovie().__psw = pswBox;
		button.getMovie().onRelease = function(Void):Void{
			BC.__user.__pseudo = this.__pseudo.getSelectionText();
			BC.__user.__psw = this.__psw.getSelectionText();
			this.__sup.getLogin();
			};
		//le lien vers inscrivez-vous
		var mvTexte = this.__arrBoxes['enter'].attachMovie('mvAide', 'T_1', this.__arrBoxes['enter'].getNextHighestDepth());
		mvTexte.txtInfos.autoSize = 'left';	
		mvTexte.txtInfos._width = oSize.__width - (this.__hvSpacer * 2);	
		mvTexte._x = this.__hvSpacer;
		mvTexte._y = 171;
		mvTexte.txtInfos.htmlText = '<font color="' + CieStyle.__welcome.__htmlTxColor + '"><b>' + gLang[602] + '</b>  <a href="asfunction:cFunc.gotoSiteRedirection,register"><u>' + gLang[603] + '</u></a></font>';

		//---HELP LINK 
		//title
		new CieTextLine(mvPanel, (this.__hvSpacer * 1.5), 293, 0, 20, 'tf', gLang[604], 'dynamic',[true,false,false], false, false, false, false, [CieStyle.__welcome.__hexSubTxColor, 12]);
		//text
		var mvTexte = mvPanel.attachMovie('mvAide', 'T_2', mvPanel.getNextHighestDepth());
		mvTexte.txtInfos.autoSize = 'left';	
		mvTexte.txtInfos._width = oSize.__width - (this.__hvSpacer * 2);	
		mvTexte._x = this.__hvSpacer * 1.5;
		mvTexte._y = 316;
		mvTexte.txtInfos.htmlText = '<font color="' + CieStyle.__welcome.__htmlTxColor + '">- <a href="asfunction:secLogin.openLostPasswordPopUp"><u>' + gLang[605] + '</u></a>\n- <a href="asfunction:cFunc.gotoSiteRedirection,faq"><u>' + gLang[606] + '</u></a>\n- <a href="asfunction:cFunc.gotoSiteRedirection,faq"><u>' + gLang[607] + '</u></a>\n- <a href="asfunction:cFunc.gotoSiteRedirection,terms"><u>' + gLang[608] + '</u></a>\n- <a href="asfunction:cFunc.gotoSiteRedirection,contact"><u>' +gLang[609] + '</u></a></font>';
		
		
		panelClass.registerObject(this);
				
		
		/*
		// TEXTE
		mvPanel.txtTitre.htmlText = gLang[193];
		mvPanel.txtMsg.htmlText = gLang[194];
		this.__h = 100;
		// PSEUDO BOXES
		new CieTextLine(mvPanel, this.__hvSpacer, this.__h, 0, this.__hBox, 'tPseudo', gLang[195], 'dynamic',[], false, false, false, false);
		this.__h += this.__hBox;
		var pseudoBox = new CieTextLine(mvPanel, this.__hvSpacer, this.__h, (this.__selectBoxWidth), this.__hBox, 'pseudo', BC.__user.__pseudo, 'input',[], false, true, false, true, [0x000000, 12]);
		Selection.setFocus(pseudoBox.getTextField());
		this.__h += this.__hBox + this.__hvSpacer;
		// PASSWORD BOXES
		new CieTextLine(mvPanel, this.__hvSpacer, this.__h, 0, this.__hBox, 'tPsw', gLang[196], 'dynamic',[], false, false, false, false);
		this.__h += this.__hBox;
		var pswBox = new CieTextLine(mvPanel, this.__hvSpacer, this.__h, (this.__selectBoxWidth), this.__hBox, 'psw', BC.__user.__psw, 'input',[], false, true, true, true, [0x000000, 12]);
		this.__h += this.__hBox + this.__hvSpacer;
		// SEND BUTTON
		//instance of the the button
		var button:CieButton = new CieButton(mvPanel, gLang[197], (this.__selectBoxWidth), this.__hButt, this.__hvSpacer, this.__h);
		button.getMovie().__sup = this;
		button.getMovie().__panel = cP;
		button.getMovie().__pseudo = pseudoBox;
		button.getMovie().__psw = pswBox;
		button.getMovie().onRelease = function(Void):Void{
			BC.__user.__pseudo = this.__pseudo.getSelectionText();
			BC.__user.__psw = this.__psw.getSelectionText();
			this.__sup.getLogin();
			};
		this.__h += this.__hButt + this.__hvSpacer;
		
		// LOST PASSWORD BUTTON
		//instance of the the button
		var lButton:CieButton = new CieButton(mvPanel, gLang[198], (this.__selectBoxWidth), this.__hButt, this.__hvSpacer, this.__h);
		lButton.getMovie().__sup = this;
		lButton.getMovie().__panel = cP;
		lButton.getMovie().onRelease = function(Void):Void{
			new CieLostPsw(gLang[199]);
			};
		this.__h += this.__hButt + (this.__hvSpacer * 3);	
			
		// REGISTER BUTTON
		//instance of the the button
		var regButton:CieButton = new CieButton(mvPanel, gLang[332], this.__selectBoxWidth, (this.__hButt * 2), this.__hvSpacer, this.__h + 137);
		regButton.getMovie().__sup = this;
		regButton.getMovie().__panel = cP;
		regButton.getMovie().onRelease = function(Void):Void{
			var arrD = Array();
			arrD['pseudo'] = 'pseudo';
			arrD['no_publique'] = '0';
			cFunc.openSiteRedirectionBox('register', arrD);
			};	
		*/
		
		
		//remove the loading mask
		setLoading(false); //this function is on the stageLoader
		
		//prompt comme quoi la demande est refuse
		if(strError != '' && strError != undefined){
			if(strError == 'ERROR_INVALID_USER'){
				new CieTextMessages('MB_OK', gLang[275], gLang[16]);
			}else{
				new CieTextMessages('MB_OK', gLang[276], gLang[16]);
				}
		}else{
			//set the key listener
			this.loginKeyListener(true, pseudoBox, pswBox);
			}
		};
		
	/*************************************************************************************************************************************************/
	public function openLostPasswordPopUp(Void):Void{
		new CieLostPsw(gLang[199]);
		}
	
	/*************************************************************************************************************************************************/	

	public function loginKeyListener(bEnable:Boolean, pseudoBox:Object, pswBox:Object):Void{
		if(bEnable){
			this.__keyListener = new Object();
			this.__keyListener.__sup = this;	
			this.__keyListener.__pseudo = pseudoBox;
			this.__keyListener.__psw = pswBox;
			this.__keyListener.onKeyUp = function(){
					if(Key.getCode() == Key.ENTER){
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
		setLoadingBar(0);
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
		setLoadingBar(10);
		obj.__super.parseLogin(obj.__req.getXml().firstChild);
		cReqManager.removeRequest(obj.__req.getID());
		};	
		
	/*************************************************************************************************************************************************/	
		
	public function parseLogin(xmlNode:XMLNode):Void{
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
			}else if(currNode.attributes.n == 'visitors'){
				BC.__user.__visitors = currNode.firstChild.nodeValue;
				//Debug('VISITEUR: ' + BC.__user.__visitors);
			}else if(currNode.attributes.n == 'onlineusers'){
				BC.__user.__onlineusers = currNode.firstChild.nodeValue;
				//Debug('ONLINE: ' + BC.__user.__onlineusers);
			}else if(currNode.attributes.n == 'messages_concat'){
				BC.__user.__msgcount = new Array();
				var arrSplit = currNode.firstChild.nodeValue.toString().split(',');
				BC.__user.__msgcount['instant'] = arrSplit[0];
				BC.__user.__msgcount['express'] = arrSplit[1];
				BC.__user.__msgcount['courriel'] = arrSplit[2];
				BC.__user.__msgcount['vocal'] = arrSplit[3];
				/*
				for(var o in BC.__user.__msgcount){
					Debug('MESSAGES[' + o + ']: ' + BC.__user.__msgcount[o]);
					}
				*/	
				}
			}
		//si erreur
		if(strError != ''){	
			this.reset();
			this.openLogin(strError);
			//brgin back the winfdow application
			cFunc.appzGetFocus();
		}else{
			//connect to the DB
			if(cDbManager.connectDB(BC.__dbconf.__name, BC.__dbconf.__password, BC.__dbconf.__version)){
				//main loader bar
				setLoadingBar(20);
				Debug('DB connected succesfully');
				//connect to the socket
				this.__cThreadConnect = cThreadManager.newThread(2000, this, 'tWaitBeforeConnect', {});	
				//set ta thread to init user basics
				this.__cThreadUserBasic = cThreadManager.newThread(2500, this, 'tInitUserBasic_BYPASSED', {});
				//set ta thread to init user interface
				this.__cThreadUserInterface = cThreadManager.newThread(3000, this, 'tInitUserInterface_BYPASSED', {});
				//check for new messages
				this.__cThreadUserNewMsg = cThreadManager.newThread(3500, this, 'tInitUserNewMsg_BYPASSED', {});
				//start the keepalive
				cFunc.startKeepAlive(true);
				//main loader bar
				setLoadingBar(30);
			}else{
				Debug('***ERROR NO DB connection');
				}
			}
		};	
		
	/*************************************************************************************************************************************************/	
	
	//WITH THREAD
	
	//have to use this to let the database do his replication
	public function tWaitBeforeConnect(obj:Object):Boolean{
		//set the connection to the java server
		setLoadingBar(40);
		cSockManager.setConnection();
		return false;
		};
		

	//TO BYPASS ALL AND INIT INTERFACE DIRECTKY (TEST DWIZZEL)
	
	public function tInitUserBasic_BYPASSED(obj:Object):Boolean{
		if(cSockManager.isConnected()){
			//save the user to the registry
			//set the tool bar
			setLoadingBar(50);
			cRegistry.setKey('pseudo', BC.__user.__pseudo);
			cRegistry.setKey('password', BC.__user.__psw);
			//title of the form
			cFunc.setTitleForm(BC.__user.__pseudo);
			//get the data critere
			setLoadingBar(60);
			cToolManager.removeAllTools();
			cToolManager.createFromXmlFile('toolbar.' + BC.__user.__lang + '.xml');
			cDataManager.getCritere();
			return false;
			}
		return true;
		};	
		
	public function tInitUserInterface_BYPASSED(obj:Object):Boolean{
		if(cDataManager.isCritLoaded()){
			//remove the login
			setLoadingBar(70);
			cSectionManager.removeTabByName('login');
			//open the welcome page
			//cFunc.openSalon();
			setLoadingBar(80);
			cFunc.openWelcome(); //will probably be welcome page
			//set the online state to default value 0=online
			//status will be set after stackTreated in CieSocketManager class
			//cFunc.changeStatus(BC.__user.__status);
			//set the tray menu
			setLoadingBar(90);
			cSysTray.enableAllSysTrayItems(true);
			//set the toolbar icon to loading state
			cToolManager.getTool('messages', 'bottin').setLoaderIcon(true);
			cToolManager.getTool('messages', 'salon').setLoaderIcon(true);
			cToolManager.getTool('messages', 'message').setLoaderIcon(true);
			//load a banner
			//cBrowser.fetchBanner();
			//remove the loadgin bar
			setLoading(false); //this function is on the stageLoader
			//put a new banner and that will start the banner rotation
			//cBrowser.fetchBannerOnResize();
			//set the key listener to catch all <ENTER> event
			//cFunc.addKeyListener(true);
			//set the application to loaded state
			BC.__user.__loaded = true;
			//starts the job of carnet, listenoire, etc...
			cDataManager.startJobs();
			//stop the thread
			return false;
			}
		return true;	
		};	
		
	public function tInitUserNewMsg_BYPASSED(obj:Object):Boolean{
		if(cDataManager.isJobsFinished()){
			//get if have new messages
			cFunc.getNewMessagesFlag();
			//check for updates
			cFunc.checkForUpdates();
			return false;
			}
		return true;
		};	
		
	/*************************************************************************************************************************************************/
	public function resize(w:Number, h:Number):Void{
		//clear drawing
		this.__arrBoxes['enter'].clear();
		//redraw boxes
		new CieGradientRoundedSquare(this.__arrBoxes['enter'], (w - (this.__hvSpacer * 2)), 200);
		};
	
	/*************************************************************************************************************************************************/
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieLogin{
		return this;
		};
	*/	
	}	