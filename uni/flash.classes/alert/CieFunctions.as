/*

functins for the core of the application
caller of the managers functions
can be accessed by anything

*/

import alert.CieAlert;
import alert.CieAlertWindow;
import utils.CieThread;




dynamic class alert.CieFunctions{

	static private var __className = "CieFunctions";
	static private var __instance:CieFunctions;
			
	private var __arrActiveAlertWindow:Array;		
			
	private function CieFunctions(Void){
		this.__arrActiveAlertWindow = new Array();
		};
		
	/*************************************************************************************************************************************************/	
		
	static public function getInstance(Void):CieFunctions{
		if(__instance == undefined) {
			__instance = new CieFunctions();
			}
		return __instance;
		};
		
	/*************************************************************************************************************************************************/	
		
	public function reset(Void):Void{
		//
		};
	
	/*****LOCAL SOCKET COMMAND TREATMENT CALLED BY CieLocalSocket*******************************************************************************/
	
	public function localSocketCommand(param:Array):Void{
		//Debug('localSocketCommand(' + param.toString() + ')');
		if(param[1][0] == 'ALERT'){
			//it's an alert so we're gooing to stack so it can be treatred later
			//keep a max of 20 alerts
			if(BC.__stack.__messages.length > BC.__stack.__max){
				//shift the first one and put the one just received
				BC.__stack.__messages.shift();
				}
			BC.__stack.__messages.push(param[1][1]);
		}else if(param[1][0] == 'CLOSE_ALL'){
			//command to close all opened alert
			this.closeAllAlertWindow();	
		}else if(param[1][0] == 'STOP'){
			//command to close all opened alert
			this.stopAlertWindow();		
		}else{
			Debug("LC_COMMAND_NOT_FOUND: " + param);
			}
		};
	

	/****STOP ALL***************************************************************************************************************/	
	//tell to stop alerting user
	public function stopAlertWindow(Void):Void{
		delete BC.__stack.__messages;
		BC.__stack.__messages = new Array();
		};
	
	/****CLOSE ALL***************************************************************************************************************/	
	//tell to close all opened alert boxes
	public function closeAllAlertWindow(Void):Void{
		//Debug('CLOSE ALL ALERT WINDOWS');
		//reset le stack
		for(var o in BC.__stack.__messages){
			BC.__stack.__messages[o] = null;
			delete BC.__stack.__messages[o];
			BC.__stack.__messages = new Array();
			}
		//close all opended windows
		for(var o in BC.__user.__windows){
			BC.__user.__windows[o].__cAlertWindow.closeWindowEvent();
			}
		//dock place
		for(var o in BC.__win.__dockplace){
			BC.__win.__dockplace[o].__empty = true;
			BC.__win.__dockplace[o].__winkey = null;
			}
		};	
		
	
	/****MESSAGES STACK TREATMENT***************************************************************************************************************/	
		
	function treatStackMessages(obj:Object):Boolean{
		//Debug("-----------STACK MSG: " + BC.__stack.__messages.length);
		if(BC.__stack.__messages.length){
			//check to see if there is some places left for docking the new window
			for(var i=0; i<BC.__win.__dockplace.length; i++){
				if(BC.__win.__dockplace[i].__empty){
					//change dock place
					BC.__win.__dockplace[i].__empty = false;
					BC.__win.__dockplace[i].__winkey = BC.__user.__nextwin;
					//create the object window
					BC.__user.__windows[BC.__user.__nextwin] = new Object();	
					BC.__user.__windows[BC.__user.__nextwin].__cAlertWindow = new CieAlertWindow(__stage, BC.__user.__nextwin, i, BC.__user.__nextdepth, BC.__win.__glowsize, BC.__win.__dockplace[i].__y, BC.__stack.__messages.shift(), this, this.closeWindow);
					BC.__user.__windows[BC.__user.__nextwin].__id = BC.__user.__nextwin;
					BC.__user.__windows[BC.__user.__nextwin].__depth = BC.__user.__nextdepth;
					BC.__user.__windows[BC.__user.__nextwin].__focus = false;
					//increment keys and movie depth for the next window
					BC.__user.__nextwin++;
					BC.__user.__nextdepth++;
					//put the focus on the window
					mdm.Application.bringToFront();
					//we found a place so lets get out
					return true;
					}
				}			
			}
		return true;
		};	

	/*************************************************************************************************************************************************/
	//called by a window when it autodestruct
	public function closeWindow(cbObject:Object, winID:Number, dockID:Number):Void{
		//the window reference
		if(BC.__user.__windows[winID].__cAlertWindow != undefined){
			BC.__user.__windows[winID].__cAlertWindow.removeWindow();
			delete BC.__user.__windows[winID].__cAlertWindow;
			delete BC.__user.__windows[winID];
			//the dock reference
			BC.__win.__dockplace[dockID].__empty = true;
			BC.__win.__dockplace[dockID].__winkey = null;
			//repostionning
			cbObject.replaceWinPosition();
			}
		};
		
	/*************************************************************************************************************************************************/	
	//repostioning of the windows when one is going away
	public function replaceWinPosition(Void):Void{
		for(var i=0; i<BC.__win.__dockplace.length; i++){
			if(!BC.__win.__dockplace[i].__empty){
				for(var j=0; j<BC.__win.__dockplace.length; j++){
					if((BC.__win.__dockplace[j].__empty) && (BC.__user.__windows[BC.__win.__dockplace[i].__winkey].__cAlertWindow.getDockID() > j) && (!BC.__user.__windows[BC.__win.__dockplace[i].__winkey].__cAlertWindow.getWindowFocus())){
						//positionning with function declre in mvWin
						BC.__user.__windows[BC.__win.__dockplace[i].__winkey].__cAlertWindow.changeWindowPosition(BC.__win.__dockplace[j].__y);
						//dockindex
						BC.__user.__windows[BC.__win.__dockplace[i].__winkey].__cAlertWindow.changeDockID(j);
						//prop of new dockplace
						BC.__win.__dockplace[j].__empty = false;
						BC.__win.__dockplace[j].__winkey = BC.__win.__dockplace[i].__winkey;
						//prop of old dockplace
						BC.__win.__dockplace[i].__empty = true;
						BC.__win.__dockplace[i].__winkey = null;
						break;
						}
					}
				}
			}
		};	
		
		
	/*************************************************************************************************************************************************/
	
	public function getClassName(Void):String{
		return __className;
		};
	
	/*************************************************************************************************************************************************/
	
	public function getClass(Void):CieFunctions{
		return this;
		};
	}	