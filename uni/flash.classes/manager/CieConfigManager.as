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
	private var __cXmlParser:CieXmlParser;
	
	private function CieConfigManager(callBackFunction:Function, callBackClass:Object){
		this.__cbFunction = callBackFunction;
		this.__cbClass = callBackClass;
		
		//file containing the basic configuration
		this.__configFileName = 'config.xml';
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
	
	public function reload(Void):Void{
		//deletes
		delete this.__cbFunction;
		delete this.__cbClass;
		delete this.__cXmlParser;
		//reset
		CieStyle = new Object();
		//loading sequence
		this.getCStyle();
		};
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieConfigManager{
		return this;
		};
	*/	
		
	private function initBasicStyle(Void):Void{
		_global.style.setStyle('color', CieStyle.__basic.__fontColor);
		_global.style.setStyle('themeColor', 'haloBlue');
		_global.style.setStyle('fontSize', 11);
		_global.style.setStyle('embedFonts' , false);
		_global.style.setStyle('fontFamily' , CieStyle.__basic.__fontFamily);
		};
	
	/***************************************************************/
	
	public function getConfig(Void):Void{
		this.__cXmlParser = new CieXmlParser(this.__configFileName, BC, this.cbConfig, this);
		};
		
	public function cbConfig(cbClass:Object):Void{
		Debug(cbClass.__configFileName + ' loaded succesfully');
		delete cbClass.__cXmlParser;
		setLoadingBar(20);
		
		//system configuration
		BC.__system.__resolutionX = System.capabilities.screenResolutionX;
		BC.__system.__resolutionY = System.capabilities.screenResolutionY;
		
		//get some var from the registry if none are there register them
		//nopub
		var nopub = cRegistry.getKey('nopub');
		if(nopub != undefined && nopub != ''){
			BC.__user.__nopub = nopub;
			}
		//pseudo
		var pseudo = cRegistry.getKey('pseudo');
		if(pseudo != undefined && pseudo != ''){
			BC.__user.__pseudo = pseudo;
			}	
		//password
		var psw = cRegistry.getKey('password');
		if(psw != undefined && psw != ''){
			BC.__user.__psw = psw;
			}
		//langue
		var lang = cRegistry.getKey('language');
		if(lang != undefined && lang != ''){
			BC.__user.__lang = lang;
		}else{
			cRegistry.setKey('language', BC.__user.__lang);
			}
		//version
		var vers = cRegistry.getKey('version');
		if(vers != undefined && vers != ''){
			BC.__user.__version = vers;
		}else{
			cRegistry.setKey('version', BC.__user.__version);
			}
		//retenir le mot de passe	
		var memorizedpsw = cRegistry.getKey('memorizedpsw');
		if(memorizedpsw != undefined && memorizedpsw != ''){
			if(memorizedpsw == 'true'){
				BC.__user.__memorizedpsw = true;
			}else{
				BC.__user.__memorizedpsw = false;
				}
		}else{
			cRegistry.setKey('memorizedpsw', BC.__user.__memorizedpsw);
			}
		//showbubble
		var showbubble = cRegistry.getKey('showbubble');
		if(showbubble != undefined && showbubble != ''){
			if(showbubble == 'true'){
				BC.__user.__showbubble = true;
			}else{
				BC.__user.__showbubble = false;
				}
		}else{
			cRegistry.setKey('showbubble', BC.__user.__showbubble);
			}		
		//autologin
		var autologin = cRegistry.getKey('autologin');
		if(autologin != undefined && autologin != ''){
			if(autologin == 'true'){
				BC.__user.__autologin = true;
			}else{
				BC.__user.__autologin = false;
				}
		}else{
			cRegistry.setKey('autologin', BC.__user.__autologin);
			}		
		//autoupdate
		var autoupdate = cRegistry.getKey('autoupdate');
		if(autoupdate != undefined && autoupdate != ''){
			if(autoupdate == 'true'){
				BC.__user.__autoupdate = true;
			}else{
				BC.__user.__autoupdate = false;
				}
		}else{
			cRegistry.setKey('autoupdate', BC.__user.__autoupdate);
			}	
		//at startup
		var startup = cRegistry.getKey('startup');
		if(startup != undefined && startup != ''){
			if(startup == 'true'){
				BC.__user.__startup = true;
			}else{
				BC.__user.__startup = false;
				}
		}else{
			cRegistry.setKey('startup', BC.__user.__startup);
			if(BC.__user.__startup){
				cRegistry.runOnStart(mdm.Application.path + 'UNI2.exe');
				}
			}
		//les alertes
		//critere
		var newcrit = cRegistry.getKey('alert_newcrit');
		if(newcrit != undefined && newcrit != ''){
			if(newcrit == 'true'){
				BC.__alert.__newcrit = true;
			}else{
				BC.__alert.__newcrit = false;
				}
		}else{
			cRegistry.setKey('alert_newcrit', BC.__alert.__newcrit);
			}	
		
		//msg
		var newmsg = cRegistry.getKey('alert_newmsg');
		if(newmsg != undefined && newmsg != ''){
			if(newmsg == 'true'){
				BC.__alert.__newmsg = true;
			}else{
				BC.__alert.__newmsg = false;
				}
		}else{
			cRegistry.setKey('alert_newmsg', BC.__alert.__newmsg);
			}	
		//carnet
		var newconn = cRegistry.getKey('alert_newconn');
		if(newconn != undefined && newconn != ''){
			if(newconn == 'true'){
				BC.__alert.__newconn = true;
			}else{
				BC.__alert.__newconn = false;
				}
		}else{
			cRegistry.setKey('alert_newconn', BC.__alert.__newconn);
			}	
		//chat
		var newchat = cRegistry.getKey('alert_newchat');
		if(newchat != undefined && newchat != ''){
			if(newchat == 'true'){
				BC.__alert.__newchat = true;
			}else{
				BC.__alert.__newchat = false;
				}
		}else{
			cRegistry.setKey('alert_newchat', BC.__alert.__newchat);
			}
		//qui a consute
		var newprofil = cRegistry.getKey('alert_newprofil');
		if(newprofil != undefined && newprofil != ''){
			if(newprofil == 'true'){
				BC.__alert.__newprofil = true;
			}else{
				BC.__alert.__newprofil = false;
				}
		}else{
			cRegistry.setKey('alert_newprofil', BC.__alert.__newprofil);
			}
		
		//les couleurs
		var colorTemplate = cRegistry.getKey('color_template');
		if(colorTemplate != undefined && colorTemplate != ''){
			BC.__user.__styleColor = colorTemplate;
		}else{
			cRegistry.setKey('color_template', BC.__user.__styleColor);
			}
			
		//loading sequence
		cbClass.getCStyle();
		};
	
	
	/***************************************************************/	
	
	public function getCStyle(Void):Void{
		this.__cXmlParser = new CieXmlParser((BC.__user.__styleColor + BC.__user.__style), CieStyle, this.cbStyle, this);
		};
		
	public function cbStyle(cbClass:Object):Void{
		Debug(BC.__user.__style + ' loaded succesfully');
		delete cbClass.__cXmlParser;
		setLoadingBar(30);
		//loading sequence
		cbClass.initBasicStyle();
		//NEW
		setLoadingBar(50);
		cbClass.__cbFunction(cbClass.__cbClass);
		};

	};
	
	