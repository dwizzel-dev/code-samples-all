/*

systray manipulation

*/

import utils.CieXmlParser;

dynamic class system.CieSysTray{

	static private var __className = 'CieSysTray';
	static private var __instance:CieSysTray;
	
	private var __fileName:String;

	private function CieSysTray(filename:String){
		this.__fileName = filename;
		_global.CieTrayMenu = new Object();
		this.createFromXmlFile();
		};
		
	static public function getInstance(filename:String):CieSysTray{
		if(__instance == undefined) {
			__instance = new CieSysTray(filename);
			}
		return __instance;
		};		
	/*	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieSysTray{
		return this;
		};
	*/
	public function createFromXmlFile(Void):Void{
		new CieXmlParser(this.__fileName, CieTrayMenu, this.cbTrayMenu, this);
		};
	
	public function cbTrayMenu(cbClass:Object):Void{
		Debug(cbClass.__fileName + ' loaded succesfully');
		cbClass.setTray();
		};

	public function enableSysTrayItem(strItem:String, bState:Boolean):Void{
		mdm.Menu.Tray.itemEnabled(CieTrayMenu.__traymenu[strItem], bState);
		};
		
	public function checkSysTrayItem(strItem:String, bState:Boolean):Void{
		mdm.Menu.Tray.itemChecked(CieTrayMenu.__traymenu[strItem], bState);
		};	
		
	public function changeSysTrayIcon(strIcon:String):Void{	
		mdm.Menu.Tray.setIcon(mdm.Application.path + strIcon);
		};	
		
	public function enableAllSysTrayItems(bState:Boolean):Void{
		//enable all
		if(bState){
			//the online state
			for(var i=0; i<4; i++){
				this.enableSysTrayItem('__state_' + i, true);
				if(BC.__user.__status == i){
					this.checkSysTrayItem('__state_' + i, true);
					this.changeSysTrayIcon('cie_' + i + '.ico');
				}else{
					this.checkSysTrayItem('__state_' + i, false);
					}
				}
			//the alert
			this.enableSysTrayItem('__showalert', true);
			this.checkSysTrayItem('__showalert', BC.__user.__showAlert);
			this.enableSysTrayItem('__logout', true);
		}else{
			this.changeSysTrayIcon('cie.ico');
			//the online state
			for(var i=0; i<4; i++){
				this.enableSysTrayItem('__state_' + i, false);
				this.checkSysTrayItem('__state_' + i, false);
				}
			//the alert
			this.enableSysTrayItem('__showalert', false);
			this.checkSysTrayItem('__showalert', false);	
			this.enableSysTrayItem('__logout', false);	
			}
		};
		
	public function setTray(Void):Void{
		//put the icon
		this.changeSysTrayIcon('cie.ico');
		//shoe it
		mdm.Menu.Tray.showIcon();
		//type of action when clicked
		//all function will call a cFunc.method
		mdm.Menu.Tray.menuType = 'function';
		//insert
		mdm.Menu.Tray.insertItem(CieTrayMenu.__traymenu.__showalert);
		mdm.Menu.Tray.itemEnabled(CieTrayMenu.__traymenu.__showalert, false);
		mdm.Menu.Tray['onTrayMenuClick_' + mdm.String.replace(CieTrayMenu.__traymenu.__showalert, ' ', '_')] = function(Void):Void{
			BC.__user.__showAlert = !BC.__user.__showAlert;
			if(!BC.__user.__showAlert){
				cFunc.stopAlertWindow();
				}
			mdm.Menu.Tray.itemChecked(CieTrayMenu.__traymenu.__showalert, BC.__user.__showAlert);
			};
		mdm.Menu.Tray.insertDivider();
		
		//insert
		mdm.Menu.Tray.insertItem(CieTrayMenu.__traymenu.__state_0);
		mdm.Menu.Tray.itemEnabled(CieTrayMenu.__traymenu.__state_0, false);
		mdm.Menu.Tray['onTrayMenuClick_' + mdm.String.replace(CieTrayMenu.__traymenu.__state_0, ' ', '_')] = function(Void):Void{
			cFunc.changeStatus(0);
			};
		//insert
		mdm.Menu.Tray.insertItem(CieTrayMenu.__traymenu.__state_1);
		mdm.Menu.Tray.itemEnabled(CieTrayMenu.__traymenu.__state_1, false);
		mdm.Menu.Tray['onTrayMenuClick_' + mdm.String.replace(CieTrayMenu.__traymenu.__state_1, ' ', '_')] = function(Void):Void{
			cFunc.changeStatus(1);
			};
		//insert
		mdm.Menu.Tray.insertItem(CieTrayMenu.__traymenu.__state_2);
		mdm.Menu.Tray.itemEnabled(CieTrayMenu.__traymenu.__state_2, false);
		mdm.Menu.Tray['onTrayMenuClick_' + mdm.String.replace(CieTrayMenu.__traymenu.__state_2, ' ', '_')] = function(Void):Void{
			cFunc.changeStatus(2);
			};
		//insert
		mdm.Menu.Tray.insertItem(CieTrayMenu.__traymenu.__state_3);
		mdm.Menu.Tray.itemEnabled(CieTrayMenu.__traymenu.__state_3, false);
		mdm.Menu.Tray['onTrayMenuClick_' + mdm.String.replace(CieTrayMenu.__traymenu.__state_3, ' ', '_')] = function(Void):Void{
			cFunc.changeStatus(3);
			};
		//insert
		mdm.Menu.Tray.insertDivider();
		mdm.Menu.Tray.insertItem(CieTrayMenu.__traymenu.__logout);
		mdm.Menu.Tray.itemEnabled(CieTrayMenu.__traymenu.__logout, false);
		mdm.Menu.Tray['onTrayMenuClick_' + mdm.String.replace(CieTrayMenu.__traymenu.__logout, ' ', '_')] = function(Void):Void{
			cFunc.openLogout();
			};	
		//insert
		mdm.Menu.Tray.insertDivider();
		mdm.Menu.Tray.insertItem(CieTrayMenu.__traymenu.__exit);
		mdm.Menu.Tray.itemEnabled(CieTrayMenu.__traymenu.__exit, true);
		mdm.Menu.Tray['onTrayMenuClick_' + mdm.String.replace(CieTrayMenu.__traymenu.__exit, ' ', '_')] = function(Void):Void{
			cFunc.exitApplication();
			};
		};
	
	};