/*	

banner roller

les banniere sont starte dans 

avec cBannerManager.startBannerManager(); dans ClieSocketManager

*/


dynamic class manager.CieBannerManager{

	static private var __className:String = 'CieBannerManager';
	static private var __instance:CieBannerManager;
	
	private var __bStarted:Boolean;
	
	private var __cThreadPub:Object;
	private var __arrSection:Array;

	private function CieBannerManager(Void){
		this.__arrSection = ['salon', 'message', 'bottin', 'recherche']; //ne pas oublier de remettre le fichier XML a model trois pour banner et deux quand il n'y en a pas
		//this.__arrSection = ['salon'];
		this.__bStarted = false;
		};
		
	static public function getInstance(Void):CieBannerManager{
		if(__instance == undefined) {
			__instance = new CieBannerManager();
			}
		return __instance;
		};	
		
		
	//called by SocketManager on IM for membership
	public function startBannerManager(Void):Void{
		Debug('startBannerManager()');
		if(!this.__bStarted){
			this.__cThreadPub = cThreadManager.newThread(BC.__user.__pubTimer, this, 'startPubTimer', {__supclass:this});
			this.__bStarted = true;
			}
		};

		
	public function startPubTimer(obj:Object):Boolean{
		//Debug('startPubTimer()');
		//go get the banner
		if(gAppzRestored){
			var arrD = new Array();
			arrD['methode'] = 'banner';
			arrD['action'] = 'getsingle';
			//add the request
			cReqManager.addRequest(arrD, obj.__supclass.cbStartPubTimer, {__supclass:obj.__supclass});
			}
		return true;	
		};
	
	//callback func	
	public function cbStartPubTimer(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//Debug('cbStartPubTimer()');
		//parse the return
		obj.__super.__supclass.parseXmlBanner(obj.__req.getXml().firstChild);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	//pub parser
	public function parseXmlBanner(xmlNode:XMLNode):Void{
		//Debug('parseXmlBanner()');
		var strPath:String = xmlNode.childNodes[0].firstChild.nodeValue;
		//Debug('banner_path:' + strPath);
		if(strPath != undefined && strPath != '' && gAppzRestored){
			var tmpPanelMv:MovieClip;
			//salon/recherche/bottin/message 
			//check si instancie if not then no need to display because it dont exist yet
			for(var o in this.__arrSection){
				tmpPanelMv = cSectionManager.getPanelManager(this.__arrSection[o]).getPubPanelMovie();
				if(tmpPanelMv != undefined){
					Debug('bannerPath: ' + strPath);
					//Debug('bannerMv:' + tmpPanelMv.mvBanner);
					//Debug('bannerMv_Width:' + tmpPanelMv.mvBanner._width);
					//Debug('bannerMv_X:' + tmpPanelMv.mvBanner._x);
					//Debug('bannerMv_Y:' + tmpPanelMv.mvBanner._y);
					//unload previous for gb collector
					//tmpPanelMv.unloadMovie();
					//load
					tmpPanelMv.mvBanner.loadMovie(strPath);
					}
				}	
			}
		};		
	
	//stop the banner rotation timer	
	public function stopBannerManager(Void):Void{
		Debug('stopBannerManager()');
		this.__bStarted = false;
		if(this.__cThreadPub != undefined){
			this.__cThreadPub.destroy();
			delete this.__cThreadPub;
			}
		};		
		
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieBannerManager{
		return this;
		};
	}	
