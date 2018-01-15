/*

The starter of all appz

*/

//in order of use
import irc.CieFunctions;
import comm.CieLocalSocket;
import utils.CieFunctionTable;
import manager.CieConfigManager;
import manager.CieStageManager;
import manager.CieContentManager;
import manager.CieSectionManager;
import manager.CieEventManager;
import manager.CieThreadManager;
import manager.CieRequestManager;
import manager.CieSocketManager;

dynamic class irc.CieIrc{

	static private var __className = "CieIrc";
	static private var __instance:CieIrc;
	private var __intervalTmp:Number;
	
	private function CieIrc(Void){
		setLoadingBar(10);
		//loads the config/style/lang/err in this order
		_global.cConfigManager = CieConfigManager.getInstance(this.cbConfigManager, this);
		//instance of request manager
		_global.cReqManager = CieRequestManager.getInstance();
		//instance of the socket manager
		_global.cSockManager = CieSocketManager.getInstance();
		//functions for all
		_global.cFunc = CieFunctions.getInstance();
		//stage listener and resize event dispatcher  //must be global
		_global.cStage = CieStageManager.getInstance();
		//content manager
		_global.cContent = CieContentManager.getInstance();
		//global functions regrouping byname functions in ech of classes Tools And Tabs ans other global manager
		_global.gEventManager = new CieEventManager(new CieFunctionTable());
		//thread manager
		_global.cThreadManager = CieThreadManager.getInstance();
		//loader
		setLoadingBar(30);
		setLoadingText('loading IRC...');
		};
		
	static public function getInstance(Void):CieIrc{
		if(__instance == undefined) {
			__instance = new CieIrc();
			}
		return __instance;
		};
		
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieIrc{
		return this;
		};

	
	/**************************************************************************************************************/	
		
	public function reset(bReconnect:Boolean):Void{
		clearInterval(this.__intervalTmp);
		//this function is on the stageLoader
		setLoadingBar(0);
		//this function is on the stageLoader
		setLoading(true); 
		//cancelled all pending request
		cReqManager.reset();
		//close the socket remote to UNI server
		cSockManager.closeConnection();
		//removes all opened tabs
		cSectionManager.removeTabs();
		//reset all content globals section
		cFunc.reset();
		//reload content tree
		cContent.createFromXmlFile(__gArgs.__ws + 'xml/irc.content.xml');
		//init the login pahge
		this.__intervalTmp = setInterval(this, 'initIrc', 250);
		};
	
	/**************************************************************************************************************/
	//ccall back du config manager on en a bersoin surtout pour la langue 
	public function cbConfigManager(cbClass:Object):Void{
		cbClass.initGeo();
		cbClass.initLang();
		cbClass.initContent();
		setLoadingBar(50);
		};
		
	/**************************************************************************************************************/	
	
	//load le fichier swf de geo avec les array en actionscript pregenere
	private function initGeo(Void):Void{
		//will load like a movie clip in the mvGeoLoader movieclip sirectly on the stage
		//gGeo
		setLoadingText('loading geo...');
		Debug('loading mvGeoLoader with ' + __gArgs.__ws + 'swf/geo.' + BC.__user.__lang + '.swf');
		__stageWinLayer.mvGeoLoader.loadMovie(__gArgs.__ws + 'swf/geo.' + BC.__user.__lang + '.swf');
		};	
	
	/**************************************************************************************************************/	
	
	//load le fichier swf de lang avec les array en actionscript pregenere
	private function initLang(Void):Void{
		//will load like a movie clip in the mvLangLoader movieclip sirectly on the stage
		//gLang
		setLoadingText('loading lang...');
		Debug('loading mvLangLoader with ' + __gArgs.__ws + 'swf/irc.' + BC.__user.__lang + '.swf');
		__stageWinLayer.mvLangLoader.loadMovie(__gArgs.__ws + 'swf/irc.' + BC.__user.__lang + '.swf');
		setLoadingBar(70);
		};	
	
	/**************************************************************************************************************/
	
	private function initContent(Void):Void{
		setLoadingText('loading content...');
		//tab manager of the stage
		_global.cSectionManager = new CieSectionManager(__stage, Stage.width, Stage.height, 0, 0);
		//register to content manager
		cContent.registerTabManager(cSectionManager);
		//load the content tree from xml
		cContent.createFromXmlFile(__gArgs.__ws + 'xml/irc.content.xml');
		//register to tthe stage to receive resize event
		cStage.registerObject(cSectionManager);
		//init the login pahge
		this.__intervalTmp = setInterval(this, 'initIrc', 500);
		setLoadingBar(90);
		};
	
	/**************************************************************************************************************/
	
	private function initIrc(Void):Void{
		if(cContent.isLoaded() && gLang != undefined && gGeo != undefined){
			clearInterval(this.__intervalTmp);
			setLoadingBar(100);
			setLoadingText('Init IRC interface...');
			cFunc.openLogin();
			}	
		};
	
	}	