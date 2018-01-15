/*

The starter of all appz

*/

//in order of use
import chat.CieFunctions;

import manager.CieConfigManager;
import manager.CieStageManager;
import manager.CieContentManager;
import manager.CieSectionManager;
import manager.CieThreadManager;

import comm.CieLocalSocket;

dynamic class chat.CieChat{

	static private var __className = "CieChat";
	static private var __instance:CieChat;
	private var __intervalTmp:Number;
	
	private function CieChat(Void){
		Debug('CieChat()');
		//this function is on the stageLoader
		setLoadingBar(10);
		setLoadingText(__gMsg[__gArgs.__ln]['chat']);
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
		};
		
	static public function getInstance(Void):CieChat{
		Debug('getInstance()');
		if(__instance == undefined) {
			__instance = new CieChat();
			}
		return __instance;
		};
		
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieChat{
		return this;
		};

	
	/**************************************************************************************************************/
	
	public function cbConfigManager(cbClass:Object):Void{
		Debug('cbConfigManager()');
		//now that we have all the config load the form data
		//at the same we'll knoew if we have a connection to the internet
		cbClass.initLocalConn();
		};
	
	/**************************************************************************************************************/	
	
	public function initLocalConn(Void):Void{
		Debug('initLocalConn()');
		//local connection
		_global.cLocalConn = new CieLocalSocket(BC.__user.__localconn);
		cLocalConn.addRemoteSocket(BC.__user.__remoteconn);
		this.initLang();
		this.initContent();
		//this function is on the stageLoader
		setLoadingBar(60);
		};
	
	/**************************************************************************************************************/
	//load le fichier swf de geo avec les array en actionscript pregenere
	private function initLang(Void):Void{
		//will load like a movie clip in the mvGeoLoader movieclip sirectly on the stage
		//gLang
		Debug('loading mvLangLoader with chat.' + BC.__user.__lang + '.swf');
		//__stageWinLayer.mvLangLoader.loadMovie('chat.' + BC.__user.__lang + '.swf');
		_level0.mvLangLoader.loadMovie('chat.' + BC.__user.__lang + '.swf');
		//this function is on the stageLoader
		setLoadingBar(70);
		};	
	
	/**************************************************************************************************************/
	
	private function initContent(Void):Void{
		Debug('initContent()');
		//tab manager of the stage
		_global.cSectionManager = new CieSectionManager(__stage, Stage.width, Stage.height, 0, 0);
		//register to content manager
		cContent.registerTabManager(cSectionManager);
		//load the content tree from xml
		cContent.createFromXmlFile('chat.content.xml?&__type=' + BC.__user.__type);
		//register to tthe stage to receive resize event
		cStage.registerObject(cSectionManager);
		//init the login pahge
		this.__intervalTmp = setInterval(this, 'initChat', 1000);
		//this function is on the stageLoader
		setLoadingBar(80);
		};
	
	private function initChat(Void):Void{
		Debug('initChat()');
		if(cContent.isLoaded()){
			clearInterval(this.__intervalTmp);
			setLoadingBar(90);
			setLoadingText(gLang[26]);
			cFunc.openChat();
			}
		};
	
	}	