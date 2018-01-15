/*

The starter of all appz

*/

//in order of use
import system.CieRegistry;
import system.CieSysTray;
import manager.CieConfigManager;
import database.CieDbManager;
import manager.CieRequestManager;
import manager.CieSocketManager;
import core.CieFunctions;
import manager.CieStageManager;
import manager.CieContentManager;
import manager.CieEventManager;
import utils.CieFunctionTable;
import utils.CieConversion;
import manager.CieToolsManager;
import manager.CieSectionManager;
import manager.CieFormManager;
import manager.CieDataManager;
import manager.CieThreadManager;
import manager.CieBannerManager;
import comm.CieLocalSocket;

import messages.CieTextMessages;

//import system.CieBrowser;

dynamic class core.CieUni{

	static private var __className = 'CieUni';
	static private var __instance:CieUni;
	private var __intervalTmp:Number;
	private var __cThreadRepositioning:Object;
	
	private function CieUni(Void){
		Debug("WIN_VERSION: " + System.capabilities.os);
		Debug("APPZ PATH: " + mdm.Application.path);
		//this function is on the stageLoader
		setLoading(true); 
		//this function is on the stageLoader
		setLoadingBar(0);
		
		
		//var 
		_global.gAppzRestored = true;
		//handler
		this.setEventHandler();
		//setExitHandler
		this.setExitHandler();
		//minimize to tray at the start
		mdm.Application.minimizeToTray(true);
		//hide
		mdm.Application.minimize();
		
				
		//CONTINUE
		//global var that will hols all no_publique in the DB
		//so instaed of checking on a dbKeyExist
		//we will verify if the key is in the array
		_global.gDbKey = new Array();
		//keep nopublique of the carnet instead of checking in the DB each time
		_global.gDbCarnet = new Array();
		//to know if the mouse is over a the left side on a miniprofil or a form
		_global.gMouseIsOverLeftSection = false;	
		//instance of a registry manipulator
		_global.cRegistry = CieRegistry.getInstance();
		//set the last run date
		cRegistry.setKey('lastrun', Date());
		//utility conversion
		_global.cConversion = new CieConversion();
		setLoadingBar(10);
		//thread manager
		_global.cThreadManager = CieThreadManager.getInstance();
		//loads the config/style/lang/err in this order
		_global.cConfigManager = CieConfigManager.getInstance(this.cbConfigManager, this);
		//instance of the DB manager
		_global.cDbManager = CieDbManager.getInstance();
		//instance of request manager
		_global.cReqManager = CieRequestManager.getInstance();
		//instance of the socket manager
		_global.cSockManager = CieSocketManager.getInstance();
		//global functions of the applications can be accessed by any manager, user actions will passed trought CieFunctionTable before
		_global.cFunc = CieFunctions.getInstance();
		//stage listener and resize event dispatcher  //must be global
		//_global.cStage = CieStageManager.getInstance(366, 523);
		_global.cStage = CieStageManager.getInstance();
		//content manager
		_global.cContent = CieContentManager.getInstance();
		//global functions regrouping byname functions in ech of classes Tools And Tabs ans other global manager
		_global.gEventManager = new CieEventManager(new CieFunctionTable());
		//datamanager for all listing of carnet, instant,c ourrier, etc...
		_global.cDataManager = CieDataManager.getInstance();
		//local connection
		_global.cLocalConn = new CieLocalSocket('localconnection.txt', 'WIN_MASTER');
		//banner manager
		_global.cBannerManager = CieBannerManager.getInstance();
		};
		
	static public function getInstance(Void):CieUni{
		if(__instance == undefined) {
			__instance = new CieUni();
			}
		return __instance;
		};
	/*	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieUni{
		return this;
		};
	*/
	
	/**************************************************************************************************************/	
	
	public function setEventHandler(Void):Void{
		mdm.Application.onAppMinimize = function(Void):Void{
			//Debug("-------------------------APPZ MINIMIZE");
			gAppzRestored = false;
			__stage._visible = false;
			};
			
		mdm.Application.onAppRestore = function(Void):Void{
			//Debug("-------------------------APPZ RESTORE");
			gAppzRestored = true;
			__stage._visible = true;
			//to systray
			mdm.Application.minimizeToTray(false);
			mdm.Application.bringToFront();
			//cBrowser.fetchBannerOnResize();	
			};
			
		mdm.Application.onFormMinimize = function(Void):Void{
			//Debug("-------------------------FORM MINIMIZE");
			};
			
		mdm.Application.onFormRestore = function(Void):Void{
			//Debug("-------------------------FORM RESTORE");
			cUni.resetSize(true);
			};
			
		};
	
	/**************************************************************************************************************/	
	
	public function setExitHandler(Void):Void{
		//disble
		mdm.Exception.trapErrors();
		//enable the exit handler
		mdm.Application.enableExitHandler()
		//close all before quitting
		mdm.Application.onAppExit = function(Void):Void{
			//Debug("-------------------------APPZ EXIT");
			//to systray
			mdm.Application.minimizeToTray(true);
			//hide
			mdm.Application.minimize();
			};
		};	
	
	/**************************************************************************************************************/	
	/*	
	public function resetStyle(bReconnect:Boolean):Void{
		cContent.createFromXmlFile('content.' + BC.__user.__lang + '.xml');
		//reload config files
		cConfigManager.reload();
		};
	*/	
		
	/**************************************************************************************************************/	
	public function resetSize(bMin:Boolean):Void{
		if(bMin){
			mdm.Forms[BC.__system.__formName].width = BC.__system.__resMin;
			mdm.Forms[BC.__system.__formName].height = BC.__system.__resMinH;
			if(mdm.Forms[BC.__system.__formName].windowState == 'max'){
				mdm.Forms[BC.__system.__formName].restore();
				}
		}else{
			mdm.Forms[BC.__system.__formName].width = BC.__system.__resMax;
			mdm.Forms[BC.__system.__formName].height = BC.__system.__resMinH;
			this.checkWindowsX();
			}
		};	
		
		
	/**************************************************************************************************************/
	public function checkWindowsX(Void):Void{
		//check si depasse de l'ecran sinon on le replace:
		if((mdm.Forms[BC.__system.__formName].width + mdm.Forms[BC.__system.__formName].x) > BC.__system.__resolutionX){
			var offSetPosX:Number = (mdm.Forms[BC.__system.__formName].width + mdm.Forms[BC.__system.__formName].x) - BC.__system.__resolutionX;
			//si pas deja entrain de se replacer on le kill
			if(this.__cThreadRepositioning != undefined){
				this.__cThreadRepositioning.destroy();
				}
			this.__cThreadRepositioning = cThreadManager.newThread(50, this, 'animWindowPositionX', {__offsetposx: offSetPosX, __win:mdm.Forms[BC.__system.__formName]});
			}
		};	
		
	/**************************************************************************************************************/
	public function animWindowPositionX(obj:Object):Boolean{
		var w = obj.__win.x + obj.__win.width;
		if(w > BC.__system.__resolutionX){
			var moveX = Math.ceil((w - BC.__system.__resolutionX)/BC.__system.__formSpeedDivider);
			if(moveX < 3){
				moveX = 3;
				}
			obj.__win.x -= moveX;	
			return true;
			}
		return false;
		};	
		
		
	/**************************************************************************************************************/	
		
	public function constraintForm(Void):Void{
		mdm.Forms[BC.__system.__formName].constraints(5000, 5000, BC.__system.__resMin, BC.__system.__resMinH);
		};	

	/**************************************************************************************************************/	
		
	public function reset(bReconnect:Boolean):Void{
		clearInterval(this.__intervalTmp);
		//this function is on the stageLoader
		setLoadingBar(0);
		//setLoadingBarData(0);
		//this function is on the stageLoader
		setLoading(true); 
		//broswer instance
		//cBrowser.reset();
		//reset the width of the form
		this.resetSize(true);
		//close all opened alert windows
		cFunc.closeAllAlertWindow();
		//change systray
		cSysTray.enableAllSysTrayItems(false);
		//cancelled all pending request
		cReqManager.reset();
		//close all chat windows because we will renew the session
		cFunc.closeAllChatWindow();
		//close the socket remote to UNI server
		cSockManager.closeConnection();
		//disconnect the DB
		cDbManager.disconnectDB();
		//removes all opened tabs
		cSectionManager.removeTabs();
		//remove the toolbar		
		cToolManager.removeAllTools();
		//reset the DataManager
		cDataManager.reset();
		//reset the form manager
		cFormManager.reset();
		//reset all content globals section
		cFunc.reset();
		//banner manager
		cBannerManager.stopBannerManager();
		//title
		cFunc.setTitleForm('');
		//reset the global db key holder and carnet holder
		gDbKey = new Array();
		gDbCarnet = new Array();
		//reload the DB
		this.initDB();
		//reload content tree
		cContent.createFromXmlFile('content.' + BC.__user.__lang + '.xml');
		if(!bReconnect){
			//init the login pahge
			this.__intervalTmp = setInterval(this, 'initLogin', 1000);
		}else{
			cFunc.openLogin('', true);
			}
		};
	
	/**************************************************************************************************************/
	
	public function cbConfigManager(cbClass:Object):Void{
		Debug('config files loaded');
		
		//try a new loading BG since we have the appropirate color now
		setNewLoadingBG();
		
		
		//now that we have all the config load the form data
		//at the same we'll knoew if we have a connection to the internet
		/*
		will be in resetSize for now
		mdm.Forms[BC.__system.__formName].width = BC.__system.__resMin;
		*/
		
		/*
		patch for vista because bars are wider then on other OS 
		*/
		var strOS:String = System.capabilities.os;
		if(strOS.indexOf('Vista') != -1){
			BC.__system.__resMin += 8;
			BC.__system.__resMax += 8;
			};
				
		//add a remote socket to the alert windows
		cLocalConn.addRemoteSocket('ALERT');
		//title
		cFunc.setTitleForm('');
		//continue
		cbClass.resetSize(true);
		cbClass.constraintForm();
		cbClass.initLang();
		cbClass.initGeo();
		cbClass.initSysTray();
		cbClass.initForm();
		};
	
	/**************************************************************************************************************/	
	
	public function initSysTray(Void):Void{
		//systray
		_global.cSysTray = CieSysTray.getInstance('systray.' + BC.__user.__lang + '.xml');
		};
	
	public function initForm(Void):Void{
		_global.cFormManager = CieFormManager.getInstance(this.cbFormManager, this);
		};
	
	public function cbFormManager(cbClass:Object):Void{
		//we have the config, the internet and the form data so let's init the content and the DB
		//Debug('FormData loaded successfully');
		setLoadingBar(60);
		cbClass.initDB();
		cbClass.initContent();
		};
	
	
	/**************************************************************************************************************/	
	
	//load le fichier swf de geo avec les array en actionscript pregenere
	private function initGeo(Void):Void{
		//will load like a movie clip in the mvGeoLoader movieclip sirectly on the stage
		//gGeo
		Debug('loading mvGeoLoader with geo.' + BC.__user.__lang + '.swf');
		__stageWinLayer.mvGeoLoader.loadMovie('geo.' + BC.__user.__lang + '.swf');
		};
		
	/**************************************************************************************************************/	
	
	//load le fichier swf de geo avec les array en actionscript pregenere
	private function initLang(Void):Void{
		//will load like a movie clip in the mvGeoLoader movieclip sirectly on the stage
		//gLang
		Debug('loading mvLangLoader with lang.' + BC.__user.__lang + '.swf');
		__stageWinLayer.mvLangLoader.loadMovie('lang.' + BC.__user.__lang + '.swf');
		};	
	
	/**************************************************************************************************************/	
		
	private function initDB(Void):Void{
		//compact the DB
		if(cDbManager.compactDB(BC.__dbconf.__name, BC.__dbconf.__password, BC.__dbconf.__version)){
			Debug('DB compacted succesfully');
		}else{
			Debug('***ERROR compacting DB');
			cFunc.openLogout(false);
			new CieTextMessages('MB_OK', gLang[277], gLang[16]);
			}
		//try a connection to the DB
		if(cDbManager.connectDB(BC.__dbconf.__name, BC.__dbconf.__password, BC.__dbconf.__version)){
			Debug('DB connected succesfully');
			//clear the DB
			cDbManager.clearDB(true);
			Debug('DB cleared succesfully');
			//disconnect the DB
			cDbManager.disconnectDB();
			Debug('DB disconnected succesfully');
			setLoadingBar(70);
		}else{
			Debug('***ERROR NO DB connection');
			cFunc.openLogout(false);
			new CieTextMessages('MB_OK', gLang[277], gLang[16]);
			}
		};
	
	
	/**************************************************************************************************************/
	
	private function initContent(Void):Void{
		//top toolbar manager
		_global.cToolManager = new CieToolsManager(__stage, Stage.width, CieStyle.__basic.__toolHeight, 0, 0);
		cToolManager.drawBgToolBar();
		//tab manager of the stage
		_global.cSectionManager = new CieSectionManager(__stage, Stage.width, Stage.height, 0, CieStyle.__basic.__toolHeight);
		//register to content manager
		cContent.registerTabManager(cSectionManager);
		//load the content tree from xml
		cContent.createFromXmlFile('content.' + BC.__user.__lang + '.xml');
		//register to tthe stage to receive resize event
		cStage.registerObject(cSectionManager);
		cStage.registerObject(cToolManager);
		//init the login pahge
		//have to set a f*** interval
		this.__intervalTmp = setInterval(this, 'initLogin', 1000);
		setLoadingBar(80);
		};
	
	private function initLogin(Void):Void{
		if(cContent.isLoaded()){
			setLoadingBar(90);
			clearInterval(this.__intervalTmp);
			cFunc.openLogin('', false);
			
			//browser instance
			//_global.cBrowser = CieBrowser.getInstance(Stage.width, Stage.height);
			//register to tthe stage to receive resize event
			//cStage.registerObject(cBrowser);
			}	
		};
	
	}	