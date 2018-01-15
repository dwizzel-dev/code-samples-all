/*

The starter of all appz

*/

//in order of use
import alert.CieConfigManager;
import alert.CieFunctions;
import manager.CieThreadManager;
import comm.CieLocalSocket;
import system.CieRegistry;

dynamic class alert.CieAlert{

	static private var __className = "CieAlert";
	static private var __instance:CieAlert;
	private var __intervalTmp:Number;
	
	private function CieAlert(Void){
		//instance of a registry manipulator
		_global.cRegistry = CieRegistry.getInstance();
		//loads the config/style/lang/err in this order
		_global.cConfigManager = CieConfigManager.getInstance(this.cbConfigManager, this);
		//global functions of the applications can be accessed by any manager, user actions will passed trought CieFunctionTable before
		_global.cFunc = CieFunctions.getInstance();
		//thread manager
		_global.cThreadManager = CieThreadManager.getInstance();
		//set the exit handler
		this.setExitHandler();
		};
		
	static public function getInstance(Void):CieAlert{
		if(__instance == undefined) {
			__instance = new CieAlert();
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
			//whatever cleanup we have to do before closing the application
			mdm.Application.exit();
			};
		};
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieAlert{
		return this;
		};

	/**************************************************************************************************************/	
		
	public function reset(Void):Void{
		//
		};
	
	/**************************************************************************************************************/
	
	public function cbConfigManager(cbClass:Object):Void{
		//now that we have all the config load the form data
		//at the same we'll knoew if we have a connection to the internet
		cbClass.initLocalConn();
		};
	
	/**************************************************************************************************************/	
	
	public function initLocalConn(Void):Void{
		//local connection
		_global.cLocalConn = new CieLocalSocket('localconnection.txt', BC.__user.__localconn);
		//connection to WIN_MASTER main application
		cLocalConn.addRemoteSocket(BC.__user.__remoteconn);
		this.initVars();
		this.initLang();
		this.initContent();
		};
	
	/**************************************************************************************************************/
		
	//load le fichier swf de geo avec les array en actionscript pregenere
	private function initLang(Void):Void{
		//will load like a movie clip in the mvGeoLoader movieclip sirectly on the stage
		//gLang
		Debug('loading mvLangLoader with alert.' + BC.__user.__lang + '.swf');
		__stage.mvLangLoader.loadMovie('alert.' + BC.__user.__lang + '.swf');
		};	
	
	/**************************************************************************************************************/
	
	//init all vars like width heiht, stage, dock etc...
	private function initVars(Void):Void{
		Debug('initVars()');
		//client interface
		BC.__system.__resolutionX = System.capabilities.screenResolutionX;
		BC.__system.__resolutionY = System.capabilities.screenResolutionY;
		//form reference MDM
		BC.__form.__ref = mdm.Forms[BC.__form.__name];
		BC.__form.__height = Stage.height;
		BC.__form.__width = Stage.width;
		//form position
		BC.__form.__ref.x = BC.__system.__resolutionX - BC.__form.__width;
		BC.__form.__ref.y = BC.__system.__resolutionY - BC.__system.__taskbar_h - BC.__form.__h;
		//calcule des places de window
		BC.__win.__maxDockPlace = Math.floor((BC.__system.__resolutionY - BC.__system.__taskbar_h)/(BC.__win.__staticHeight + (BC.__win.__glowsize * 2)));
		if(BC.__win.__maxDockPlace > BC.__win.__maxNumOfDockPlace){
			BC.__win.__maxDockPlace = BC.__win.__maxNumOfDockPlace;
			}
		//calculate number of docking places	
		BC.__win.__dockplace = new Array();
		for(i=0; i< BC.__win.__maxDockPlace; i++){
			BC.__win.__dockplace[i] = new Object();
			BC.__win.__dockplace[i].__empty = true;
			BC.__win.__dockplace[i].__winkey = null;
			BC.__win.__dockplace[i].__y = BC.__form.__h - (BC.__win.__staticHeight * (i + 1)) - (BC.__win.__glowsize * (i + 1));
			}
		//for the windows
		BC.__user.__nextwin = 0;
		BC.__user.__windows = new Array();
		//depth of the windows pops
		BC.__user.__nextdepth = __stage.getNextHighestDepth();
		//the stack
		BC.__stack.__messages = new Array();
		};
	
	/**************************************************************************************************************/
	
	private function initContent(Void):Void{
		//start the treatstack thread
		Debug('initContent()');
		_global.__cThreadStack = cThreadManager.newThread(2500, cFunc, 'treatStackMessages', {});
		};
	
		
	}