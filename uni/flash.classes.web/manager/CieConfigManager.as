/*

sert a la loade tout les fichier de config et les transformer en object

*/

import utils.CieXmlParser;

dynamic class manager.CieConfigManager{	
	
	static private var __className:String = 'CieConfigManager';
	static private var __instance:CieConfigManager;
	
	private var __configFileName:String;
	private var __cbFunction:Function;
	private var __cbClass:Object;
	
	private var __bFromWeb:Boolean;
	
	private function CieConfigManager(callBackFunction:Function, callBackClass:Object){
		this.__bFromWeb = true;
		this.__cbFunction = callBackFunction;
		this.__cbClass = callBackClass;
		
		//file containing the basic configuration
		if(!this.__bFromWeb){
			this.__configFileName = 'chat.config.xml';
		}else{
			var urlrand:String = '?&rand=' + ((Math.round(Math.random() * (900)) + 100));
			var urlxml:String = '&__cdata=';
			urlxml += '<UNIREQUEST>';
			urlxml += '<C n="language">' + __gArgs.__ln + '</C>';
			urlxml += '<C n="arguments">' + __gArgs.__ln + '|' + __gArgs.__lp + '|' + __gArgs.__ps + '|' + __gArgs.__ap + '|' + __gArgs.__ac + '|' + __gArgs.__rc + '|' + __gArgs.__lc + '|' + __gArgs.__tp + '</C>';
			urlxml += '<C n="action">chatconfig</C>';
			urlxml += '<C n="methode">web</C>';
			urlxml += '</UNIREQUEST>';
					
			//the complete path builded to get the good config
			this.__configFileName = __gArgs.__ss + urlrand + urlxml;
			
			Debug(this.__configFileName);
			
			}
		//declare new global obecjt for config
		_global.BC = new Object();
		//global for the styles
		_global.CieStyle = new Object();
		//loading sequence
		this.getConfig();
		};
		
	static public function getInstance(callBackFunction:Function, callBackClass:Object):CieConfigManager{
		if(__instance == undefined) {
			__instance = new CieConfigManager(callBackFunction, callBackClass);
			}
		return __instance;
		};	
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieConfigManager{
		return this;
		};
		
	private function initBasicStyle(Void):Void{
		_global.style.setStyle('color', CieStyle.__basic.__fontColor);
		_global.style.setStyle('themeColor', 'haloBlue');
		_global.style.setStyle('fontSize', 11);
		_global.style.setStyle('embedFonts' , false);
		_global.style.setStyle('fontFamily' , CieStyle.__basic.__fontFamily);
		};
	
	/***************************************************************/
	
	public function getConfig(Void):Void{
		new CieXmlParser(this.__configFileName, BC, this.cbConfig, this);
		};
		
	public function cbConfig(cbClass:Object):Void{
		Debug(cbClass.__configFileName + ' loaded succesfully');
		
		//system configuration
		BC.__system.__resolutionX = System.capabilities.screenResolutionX;
		BC.__system.__resolutionY = System.capabilities.screenResolutionY;
					
		//loading sequence
		cbClass.getCStyle();
		};
	
	
	/***************************************************************/	
	
	public function getCStyle(Void):Void{
		new CieXmlParser(BC.__user.__style, CieStyle, this.cbStyle, this);
		};
		
	public function cbStyle(cbClass:Object):Void{
		Debug(BC.__user.__style + ' loaded succesfully');
		//loading sequence
		cbClass.initBasicStyle();
		cbClass.__cbFunction(cbClass.__cbClass);
		};
		
	/***************************************************************/
		
	};
	
	