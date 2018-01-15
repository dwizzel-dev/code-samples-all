/*

the options panels

*/

import graphic.CieCornerSquare;
import mx.containers.ScrollPane;
import control.CieButton;
import manager.CieTabManager;

dynamic class irc.CieOptions{

	static private var __className = "CieOptions";
	static private var __instance:CieOptions;
	
	private var __arrPane:Array;
	private var __arrRoomListMovie:Array;
	
	private var __tabManager:CieTabManager;
	
	private var __keyListener:Object; //for <ENTER> press
	
	private var __panelLastSize:Object;
	private var __hvSpacer:Number;
	private var __listWidth:Number;
	
	private var __oPanel:Object;
	private var __panel:MovieClip;
	private var __mvWelcomeText:MovieClip;
	
	
	private function CieOptions(Void){
		//
		this.__arrRoomListMovie = new Array();
		this.__arrPane = new Array();
		//
		this.__hvSpacer = CieStyle.__profil.__hvSpacer;
		this.__listWidth = 130;

		//the panel object
		this.__tabManager = cFunc.getTabManager();
		
		this.__oPanel = cFunc.getPanelObject(['irc','_tl','~options','_tl']);
		this.__panel = this.__oPanel.__class.getPanelContent();
		
		//get the W and H of top panel
		this.__panelLastSize = this.__oPanel.__class.getPanelSize();
			
		//draw bordere arround the room list
		new CieCornerSquare(this.__panel, (this.__panelLastSize.__width - this.__hvSpacer - this.__listWidth), this.__hvSpacer, this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)), [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//create the scrool pane for the room list
		var depth =  this.__panel.getNextHighestDepth(); //patch for F** macromedia shit
		this.__arrPane['room'] = this.__panel.createClassObject(ScrollPane, 'ROOMLIST', depth + 1);		
		this.__arrPane['room'].setStyle('borderStyle', 'none');
		this.__arrPane['room'].hScrollPolicy = false;
		this.__arrPane['room'].vLineScrollSize = CieStyle.__panel.__scrollSize;
		this.__arrPane['room'].tabEnabled = false;
		this.__arrPane['room'].vScrollPolicy = 'auto';		
		this.__arrPane['room'].setSize(this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)));
		this.__arrPane['room'].move((this.__panelLastSize.__width - this.__hvSpacer - this.__listWidth), this.__hvSpacer);
		this.__arrPane['room'].contentPath = 'mvContent';
		
		//draw the welcome text
		this.__mvWelcomeText = this.__panel.attachMovie('mvTextMessages', 'mvWelcomeText', this.__panel.getNextHighestDepth());
		//size
		this.__mvWelcomeText.txtInfos._height = (this.__panelLastSize.__height - (this.__hvSpacer * 2));
		this.__mvWelcomeText.txtInfos._width = (this.__panelLastSize.__width - (this.__hvSpacer * 3) - this.__listWidth);
		//pos
		this.__mvWelcomeText._x = this.__hvSpacer;
		this.__mvWelcomeText._y = this.__hvSpacer;
		
		this.changeWelcomeText();
		
		//key listener	for <ENTER>
		this.addKeyListener(true);
		//drawRoomList
		this.drawRoomList();
		
		//register the class to the top panel so it can recevie a resize event
		this.__oPanel.__class.registerObject(this);
		};
		
	/**************************************************************************************************************/	
			
	static public function getInstance(Void):CieOptions{
		if(__instance == undefined) {
			__instance = new CieOptions();
			}
		return __instance;
		};
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieOptions{
		return this;
		};

	/**************************************************************************************************************/		
	//change the welcome text...
	public function changeWelcomeText(Void):Void{
		//welcome
		var strText = '<font size="+3">' + gLang[6] + '<b>' + BC.__user.__pseudo.toUpperCase() + '</b>!</font><br><br>';
		//instructions
		strText += gLang[7]; 
		strText += gLang[8];
		//si moderateur de room
		var strRooms = "";
		var arrModerator = cFunc.getModeratorDB();
		for(var o in arrModerator){
			strRooms += o + ", ";
			}
		if(strRooms != ""){
			//strip last virgule
			strRooms = strRooms.substr(0, (strRooms.length - 2));
			strText += gLang[9] + '<br>';
			strText += '<b><i>' + strRooms + '</i></b>';
			strText += '<br><br>';
			}
		//si admin
		if(BC.__user.__admin == 1){
			strText += gLang[10] + '<br><br>';
			}
		//logout
		strText += gLang[11] + '<a href="asfunction:cFunc.openLogout, false"><u>' + gLang[12] + '</u></a>'  + gLang[13];
		//byebye
		strText += gLang[14];
		//show
		this.__mvWelcomeText.txtInfos.htmlText = '<font color="' + CieStyle.__welcome.__htmlTxColor + '">' + strText + '</font>';
		};
		
	/**************************************************************************************************************/	
	
	//called when registered the the container panel
	public function resize(w:Number, h:Number):Void{
		//replace the values
		this.__panelLastSize.__width = w;
		this.__panelLastSize.__height = h;
		//clear
		this.__panel.clear();
		//draw bordere arround the room list
		new CieCornerSquare(this.__panel, (this.__panelLastSize.__width - this.__hvSpacer - this.__listWidth), this.__hvSpacer, this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)), [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//redraw the list
		for(var o in this.__arrPane){
			this.__arrPane[o].setSize(this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)));
			this.__arrPane[o].move((this.__panelLastSize.__width - this.__hvSpacer - this.__listWidth), this.__hvSpacer);
			}
		//size
		this.__mvWelcomeText.txtInfos._height = (this.__panelLastSize.__height - (this.__hvSpacer * 2));
		this.__mvWelcomeText.txtInfos._width = (this.__panelLastSize.__width - (this.__hvSpacer * 3) - this.__listWidth);
		//pos
		this.__mvWelcomeText._x = this.__hvSpacer;
		this.__mvWelcomeText._y = this.__hvSpacer;
		}
		
	/*************************************************************************************************************************************************/	
	private function addKeyListener(bEnable:Boolean):Void{
		if(bEnable){
			if(this.__keyListener == undefined){
				this.__keyListener = new Object();
				this.__keyListener.onKeyUp = function(){
					if(Key.getCode() == Key.ENTER){
						cFunc.onEnterPress();
						}
					};
				Key.addListener(this.__keyListener);
				}
		}else{
			Key.removeListener(this.__keyListener);
			delete this.__keyListener;
			}
		};	
		
	/*************************************************************************************************************************************************/	
	//draw the room list choice
	private function drawRoomList(Void):Void{
		//first clean up 
		if(this.__arrRoomListMovie != undefined){
			for(var o in this.__arrRoomListMovie){
				this.__arrRoomListMovie[o].removeMovieClip();
				}
			}
		this.__arrRoomListMovie = new Array();
		var mv = this.__arrPane['room'].content;
		var arrRoomDB = cFunc.getRoomDB();
		
		//try a sort
		var arrRoomSort = new Array();
		for(var o in arrRoomDB){
			arrRoomSort.push(o);
			}
		arrRoomSort.sort();	
				
		//loop and show
		//for(var o in arrRoomDB){
		for(var i=0; i<arrRoomSort.length; i++){
			var o = arrRoomSort[i];
			//the room name wihout tthe prefix
			var strRoomName = arrRoomDB[o]['name'];
			//Debug("ROOMLIST: " + strRoomName + " \tCount: " + arrRoomsDB[o]['usercount']);
			var tmpHeight = mv._height;
			//the line
			this.__arrRoomListMovie[o] = mv.attachMovie('mvPlus','mvPlus_' + strRoomName, mv.getNextHighestDepth());
			this.__arrRoomListMovie[o].txtInfos.htmlText = '<font color="#333333">'  + strRoomName + '</font> <font color="#bbbbbb">[' + arrRoomDB[o]['usercount'] + ']' + '</font>'
			this.__arrRoomListMovie[o].mvPlusBg.gotoAndStop('_1');
			this.__arrRoomListMovie[o]._y = tmpHeight;
			//on click on line
			this.__arrRoomListMovie[o].mvPlusBg.useHandCursor = false;
			this.__arrRoomListMovie[o].mvPlusBg.__roomName = o;
			this.__arrRoomListMovie[o].mvPlusBg.onRelease = function(Void):Void{
				this.mvBgOver.gotoAndStop('_off');
				cFunc.createPublicRoom(this.__roomName);
				};
			this.__arrRoomListMovie[o].mvPlusBg.onRollOver = function(Void):Void{
				this.mvBgOver.gotoAndStop('_on');
				};	
			this.__arrRoomListMovie[o].mvPlusBg.onRollOut = 
			this.__arrRoomListMovie[o].mvPlusBg.onDragOut = 
			this.__arrRoomListMovie[o].mvPlusBg.onReleaseOutside = function(Void):Void{
				this.mvBgOver.gotoAndStop('_off');
				};
			}
		};

	/*************************************************************************************************************************************************/	
	public function reset(Void):Void{
		//unregistered resize event
		this.__oPanel.__class.removeRegisteredObject(this);
		//enter listener
		this.addKeyListener(false);
		//arras
		delete this.__arrRoomListMovie;
		__instance = undefined;
		//arrays
		delete this.__arrPane;
		//scrool pane
		destroyObject('ROOMLIST');
		//movs
		this.__mvWelcomeText.removeMovieClip();
		delete this.__mvWelcomeText;
		//ref
		delete this.__oPanel;
		delete this.__panel;
		delete this.__panelLastSize;
		delete this.__tabManager;
		delete this;
		};
	
			
	}	