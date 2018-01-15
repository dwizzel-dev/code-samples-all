/*

The starter of all appz

*/

//in order of use
import chat.CieConfigManager;
import chat.CieFunctions;
import chat.CieStageManager;
import chat.CieContentManager;
import chat.CieSectionManager;

import manager.CieThreadManager;
import comm.CieLocalSocket;

dynamic class chat.CieChat{

	static private var __className = "CieChat";
	static private var __instance:CieChat;
	private var __intervalTmp:Number;
	
	private function CieChat(Void){
		setLoadingBar(10);
		//loads the config/style/lang/err in this order
		_global.cConfigManager = CieConfigManager.getInstance(this.cbConfigManager, this);
		//global functions of the applications can be accessed by any manager, user actions will passed trought CieFunctionTable before
		_global.cFunc = CieFunctions.getInstance();
		//stage listener and resize event dispatcher  //must be global
		_global.cStage = CieStageManager.getInstance();
		//content manager
		_global.cContent = CieContentManager.getInstance();
		//thread manager
		_global.cThreadManager = CieThreadManager.getInstance();
		//set the exit handler
		this.setExitHandler();
		};
		
	static public function getInstance(Void):CieChat{
		if(__instance == undefined) {
			__instance = new CieChat();
			}
		return __instance;
		};
		
	public function setExitHandler(Void):Void{
		//disble
		mdm.Exception.trapErrors();
		//enable the exit handler
		mdm.Application.enableExitHandler();
		//close all before quitting
		mdm.Application.onAppExit = function(Void):Void{
			cFunc.closeChat(true); //true for broadcast
			mdm.Application.exit();
			};
		};
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieChat{
		return this;
		};

	/**************************************************************************************************************/	
		
	public function constraintForm(Void):Void{
		mdm.Forms[BC.__system.__formName].constraints(5000, 5000, BC.__system.__resMin, BC.__system.__resMinH);
		};
	
	/**************************************************************************************************************/	
		
	public function reset(bReconnect:Boolean):Void{
		//
		};
	
	/**************************************************************************************************************/
	
	public function cbConfigManager(cbClass:Object):Void{
		//now that we have all the config load the form data
		//at the same we'll knoew if we have a connection to the internet
		
		setNewLoadingBG();
		
		cbClass.initLang();
		cbClass.initLocalConn();
		//
		cFunc.setTitleForm('');
		cbClass.constraintForm();
		//
		cbClass.initContent();
		};
	
	/**************************************************************************************************************/	
	
	public function initLocalConn(Void):Void{
		//local connection
		_global.cLocalConn = new CieLocalSocket('localconnection.txt', BC.__user.__localconn);
		cLocalConn.addRemoteSocket(BC.__user.__remoteconn);
		setLoadingBar(70);
		};
	
	/**************************************************************************************************************/
	//load le fichier swf de geo avec les array en actionscript pregenere
	private function initLang(Void):Void{
		//will load like a movie clip in the mvGeoLoader movieclip sirectly on the stage
		//gLang
		Debug('loading mvLangLoader with chat.' + BC.__user.__lang + '.swf');
		__stageWinLayer.mvLangLoader.loadMovie('chat.' + BC.__user.__lang + '.swf');
		setLoadingBar(60);
		};	
	
	/**************************************************************************************************************/
	
	private function initContent(Void):Void{
		//tab manager of the stage
		_global.cSectionManager = new CieSectionManager(__stage, Stage.width, Stage.height, 0, 0);
		//register to content manager
		cContent.registerTabManager(cSectionManager);
		//load the content tree from xml
		cContent.createFromXmlFile('chat.content.xml');
		//register to tthe stage to receive resize event
		cStage.registerObject(cSectionManager);
		//init the login pahge
		this.__intervalTmp = setInterval(this, 'initChat', 1000);
		setLoadingBar(80);
		};
	
	private function initChat(Void):Void{
		if(cContent.isLoaded()){
			clearInterval(this.__intervalTmp);
			setLoadingBar(90);
			setLoadingText(gLang[26]);
			cFunc.openChat();
			}	
		};
	
	}	