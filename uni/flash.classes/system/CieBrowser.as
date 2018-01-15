/*

cllas for broser application IExplorer

*/

import utils.CieThread;
import utils.CieXmlBuilder;

dynamic class system.CieBrowser{

	private var __className = 'CieBrowser';
	static private var __instance:CieBrowser;
	
	private var __browser:Object;
	private var __positionning:String;
	private var __lastBannerUpdate:Date;
	private var __cThreadFetchNewBanner:CieThread;
	
	private function CieBrowser(w:Number, h:Number){
		this.__positionning = 'horizontal';
		this.__lastBannerUpdate = new Date();
		this.__browser = new mdm.Browser(0, (h - BC.__banner.__bottomHeight), w, BC.__banner.__bottomHeight, '', false);
		this.__browser.__super = this;
		this.__browser.onDocumentComplete = function(obj):Void{
			this.__super.showBrowser(true);
			var dDate = new Date();
			Debug('LAST_BANNER_UPDATE: ' + ((dDate.getTime() - this.__super.__lastBannerUpdate.getTime())/1000) + ' sec');
			this.__super.__lastBannerUpdate = new Date();
			};
		//this.fetchBanner();
		};
		
	static public function getInstance(w:Number, h:Number):CieBrowser{
		if(__instance == undefined) {
			__instance = new CieBrowser(w, h);
			}
		return __instance;
		};

	public function startBannerRotation(Void):Void{
		this.__cThreadFetchNewBanner = cThreadManager.newThread(BC.__banner.__pubTimer, this, 'tFetchNewBanner', {__superclass:this});	
		};
		
	public function tFetchNewBanner(obj:Object):Boolean{
		if(gAppzRestored){ //if the form is showed
			obj.__superclass.fetchBanner();
			}
		return true;
		};	
	
	public function repositionBanner(positionning:String):Void{
		this.__positionning = positionning;
		};
	
	public function fetchBanner(Void):Void{
		Debug("CirBrowser.fetchBanner()");
		this.showBrowser(false);
		var randNumID:String = '&rand=' + ((Math.round(Math.random() * (900)) + 100));
		var strUrlRequest:String = '&__cdata=' + this.buildURL();
		this.__browser.goto(BC.__server.__banner + '?' + randNumID + strUrlRequest);
		};
		
	public function buildURL(Void):String{
		var cXml = new CieXmlBuilder();
		cXml.openNode();
		cXml.addHeader();
		if(this.__positionning == 'horizontal'){
			cXml.createNode('arguments', (Stage.width + ',' + BC.__banner.__bottomHeight + ',' + this.__positionning));
		}else{
			cXml.createNode('arguments', (BC.__banner.__rightWidth + ',' + (Stage.height - CieStyle.__basic.__toolHeight) + ',' + this.__positionning));
			}
        cXml.closeNode();
		var strData = cXml.getXml();
		cXml.Destroy();
		delete cXml;
		return strData;
		};
		
	public function fetchBannerOnResize(Void):Void{
		//Debug("CirBrowser.fetchBanner()");
		if(this.__cThreadFetchNewBanner != undefined){
			this.__cThreadFetchNewBanner.destroy();
			}	
		this.fetchBanner();
		this.startBannerRotation();
		};	
		
	public function showBrowser(bShow:Boolean):Void{
		this.__browser.visible = bShow;
		};	
		
	public function resize(w:Number, h:Number):Void{
		if(this.__browser != undefined){
			if(this.__positionning == 'horizontal'){
				this.__browser.x = 0;
				this.__browser.y = h - BC.__banner.__bottomHeight;
				this.__browser.width = w;
				this.__browser.height = BC.__banner.__bottomHeight;
			}else{
				this.__browser.x = Stage.width - BC.__banner.__rightWidth;
				this.__browser.y = CieStyle.__basic.__toolHeight;
				this.__browser.width = BC.__banner.__rightWidth;
				this.__browser.height = Stage.height - CieStyle.__basic.__toolHeight;
				}
			}
		};
	
	public function reset(Void):Void{
		//ckear al threads
		this.__cThreadFetchNewBanner.destroy();
		this.__cThreadFetchNewBanner = null;
		//this.fetchBanner();
		};
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieBrowser{
		return this;
		};
	};