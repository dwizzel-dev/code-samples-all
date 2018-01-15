/*

the admin panels

*/

import graphic.CieCornerSquare;
import mx.containers.ScrollPane;
import control.CieButton;
import messages.CieTextMessages;
import control.CieOptionBox;

dynamic class irc.CieAdmin{

	static private var __className = "CieAdmin";
	
	private var __arrListMovie:Array;
	private var __arrPane:Array;
	private var __arrPanel:Array;//array of movie ref
	private var __arrPanelObject:Array;//array of panelcCLass ref
	
	private var __mvTrace:MovieClip;
	private var __mvMessage:MovieClip;
	private var __optionBoxLang:CieOptionBox;
	private var __arrLangChoices:Array;
	
	private var __subTabManager:Object;
	private var __tabManager:Object;
	
	private var __arrRoomUsersButtons:Array;
	
	private var __hvSpacer:Number;
	private var __panelLastSize:Object;
	private var __buttHeight:Number;
	private var __buttWidth:Number;
	private var __listWidth:Number;
	
	private var __registeredForResizeEvent:Array;
	
	/*************************************************************************************************************************************************/
	public function CieAdmin(tabName:String, tabManager:Object){
		this.__hvSpacer = CieStyle.__profil.__hvSpacer;
		this.__buttHeight = 35;
		this.__buttWidth = 65;
		this.__listWidth = 130;
		
		//lang for message
		this.__arrLangChoices = [['fr_CA', 'francais'], ['en_US', 'english'], ['de_DE', 'german']];
		
		//butt of rooms->users
		this.__arrRoomUsersButtons = new Array();
		
		//list movies each type->rows
		this.__arrListMovie = new Array();
		this.__arrListMovie['rooms'] = new Array();
		this.__arrListMovie['users'] = new Array();
		this.__arrListMovie['private'] = new Array();
		this.__arrListMovie['roomusers'] = new Array();
		this.__arrListMovie['banned'] = new Array();
		
		//scroll pane
		this.__arrPane = new Array();
		//pannel moveiClip ref
		this.__arrPanel = new Array();
		//pannel class ref
		this.__arrPanelObject = new Array();

		//the panel object
		this.__tabName = tabName;
		this.__tabManager = tabManager;
		
		//resize
		this.__registeredForResizeEvent = new Array();
		
		this.Init();
		};
		
	/*************************************************************************************************************************************************/
	private function Init(Void):Void{
		
		//xml to create the tab
		strXml = '<PT n="' + this.__tabName + '" model="un" title="Admin" closebutt="false">';
		strXml += '<P n="_tl" content="mvContent" bgcolor="' + CieStyle.__tabPanel.__bgColorSpecial + '" scroll="false" effect="false">';
		strXml += '<PT n="users" model="un" ystart="10" title="Clients" closebutt="false">';
		strXml += '<P n="_tl" content="mvContent" bgcolor="0xffffff" scroll="false" effect="false"></P>';
		strXml += '</PT>';
		strXml += '<PT n="rooms" model="un" title="Rooms" closebutt="false">';
		strXml += '<P n="_tl" content="mvContent" bgcolor="0xffffff" scroll="false" effect="false"></P>';
		strXml += '</PT>';
		strXml += '<PT n="private" model="un" title="Chats" closebutt="false">';
		strXml += '<P n="_tl" content="mvContent" bgcolor="0xffffff" scroll="false" effect="false"></P>';
		strXml += '</PT>';
		strXml += '<PT n="banned" model="un" title="Banned" closebutt="false">';
		strXml += '<P n="_tl" content="mvContent" bgcolor="0xffffff" scroll="false" effect="false"></P>';
		strXml += '</PT>';
		strXml += '<PT n="trace" model="un" title="Trace" closebutt="false">';
		strXml += '<P n="_tl" content="mvContent" bgcolor="0xffffff" scroll="false" effect="false"></P>';
		strXml += '</PT>';
		strXml += '<PT n="message" model="un" title="Message" closebutt="false">';
		strXml += '<P n="_tl" content="mvContent" bgcolor="0xffffff" scroll="false" effect="false"></P>';
		strXml += '</PT>';
		strXml += '</P>';
		strXml += '</PT>';
		//load the xml and build the treee
		cFunc.changeNodeValue(new XML(strXml),['irc','_tl','SKIP']);
		
		//object panel as reference
		this.__arrPanelObject['users'] = cFunc.getPanelObject(['irc','_tl',this.__tabName,'_tl', 'users', '_tl']);
		this.__arrPanelObject['rooms'] = cFunc.getPanelObject(['irc','_tl',this.__tabName,'_tl', 'rooms', '_tl']);
		this.__arrPanelObject['private'] = cFunc.getPanelObject(['irc','_tl',this.__tabName,'_tl', 'private', '_tl']);
		this.__arrPanelObject['trace'] = cFunc.getPanelObject(['irc','_tl',this.__tabName,'_tl', 'trace', '_tl']);
		this.__arrPanelObject['banned'] = cFunc.getPanelObject(['irc','_tl',this.__tabName,'_tl', 'banned', '_tl']);
		this.__arrPanelObject['message'] = cFunc.getPanelObject(['irc','_tl',this.__tabName,'_tl', 'message', '_tl']);
		
		//movieclip panel as reference
		this.__arrPanel['users'] = this.__arrPanelObject['users'].__class.getPanelContent();
		this.__arrPanel['rooms'] = this.__arrPanelObject['rooms'].__class.getPanelContent();
		this.__arrPanel['private'] = this.__arrPanelObject['private'].__class.getPanelContent();
		this.__arrPanel['trace'] = this.__arrPanelObject['trace'].__class.getPanelContent();
		this.__arrPanel['banned'] = this.__arrPanelObject['banned'].__class.getPanelContent();
		this.__arrPanel['message'] = this.__arrPanelObject['message'].__class.getPanelContent();
		
		//pour le resize de chaque panel
		this.__registeredForResizeEvent['users'] = new Object();
		this.__registeredForResizeEvent['users'].__super = this;
		this.__registeredForResizeEvent['users'].resize = function(w:Number, h:Number):Void{
			this.__super.resizeUsers(w, h);
			};
		this.__registeredForResizeEvent['rooms'] = new Object();
		this.__registeredForResizeEvent['rooms'].__super = this;
		this.__registeredForResizeEvent['rooms'].resize = function(w:Number, h:Number):Void{
			this.__super.resizeRooms(w, h);
			};
		this.__registeredForResizeEvent['private'] = new Object();
		this.__registeredForResizeEvent['private'].__super = this;
		this.__registeredForResizeEvent['private'].resize = function(w:Number, h:Number):Void{
			this.__super.resizePrivate(w, h);
			};
		this.__registeredForResizeEvent['trace'] = new Object();
		this.__registeredForResizeEvent['trace'].__super = this;
		this.__registeredForResizeEvent['trace'].resize = function(w:Number, h:Number):Void{
			this.__super.resizeTrace(w, h);
			};
		this.__registeredForResizeEvent['banned'] = new Object();
		this.__registeredForResizeEvent['banned'].__super = this;
		this.__registeredForResizeEvent['banned'].resize = function(w:Number, h:Number):Void{
			this.__super.resizeBanned(w, h);
			};
		this.__registeredForResizeEvent['message'] = new Object();
		this.__registeredForResizeEvent['message'].__super = this;
		this.__registeredForResizeEvent['message'].resize = function(w:Number, h:Number):Void{
			this.__super.resizeMessage(w, h);
			};	
		//the size since each panel his the same only take one
		this.__panelLastSize = this.__arrPanelObject['users'].__class.getPanelSize();
				
		//------ROOMS-------------------------------------------------------------------------------------
		//draw bordere arround the room list
		new CieCornerSquare(this.__arrPanel['rooms'], this.__hvSpacer, this.__hvSpacer, this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)), [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//create the scrool pane for the room list
		var depth =  this.__arrPanel['rooms'].getNextHighestDepth(); //patch for F** macromedia shit
		this.__arrPane['room'] = this.__arrPanel['rooms'].createClassObject(ScrollPane, 'ROOMLIST', depth + 1);		
		this.__arrPane['room'].setStyle('borderStyle', 'none');
		this.__arrPane['room'].hScrollPolicy = false;
		this.__arrPane['room'].vLineScrollSize = CieStyle.__panel.__scrollSize;
		this.__arrPane['room'].tabEnabled = false;
		this.__arrPane['room'].vScrollPolicy = 'auto';		
		this.__arrPane['room'].setSize(this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)));
		this.__arrPane['room'].move(this.__hvSpacer, this.__hvSpacer);
		this.__arrPane['room'].contentPath = 'mvContent';
		//set a butt to refresh
		var bTRoomsRefresh = new CieButton(this.__arrPanel['rooms'], gLang[100], this.__buttWidth, this.__buttHeight, (this.__listWidth + (this.__hvSpacer * 2)), this.__hvSpacer);	
		bTRoomsRefresh.getMovie().__class = this;
		bTRoomsRefresh.getMovie().onRelease = function(Void):Void{
			this.__class.drawRoomList();
			};
		//set a butt to create room for test	
		var bTCreate = new CieButton(this.__arrPanel['rooms'], gLang[101], this.__buttWidth, this.__buttHeight, (this.__listWidth + (this.__hvSpacer * 2)), (this.__buttHeight + (this.__hvSpacer * 2)));	
		bTCreate.getMovie().onRelease = function(Void):Void{
			cFunc.createDirectRoom();
			};
		//set a butt to delete room for test	
		var bTDelete = new CieButton(this.__arrPanel['rooms'], gLang[102], this.__buttWidth, this.__buttHeight, (this.__listWidth + (this.__hvSpacer * 2)), ((this.__buttHeight * 2) + (this.__hvSpacer * 3)));	
		bTDelete.getMovie().__class = this;
		bTDelete.getMovie().onRelease = function(Void):Void{
			this.__class.Deleted();
			};	
		//set a butt to getusers from room selected
		var bTUsers = new CieButton(this.__arrPanel['rooms'], gLang[103], this.__buttWidth, this.__buttHeight, (this.__listWidth + (this.__hvSpacer * 2)), ((this.__buttHeight * 3) + (this.__hvSpacer * 4)));	
		bTUsers.getMovie().__class = this;
		bTUsers.getMovie().onRelease = function(Void):Void{
			this.__class.getUsersFromSelectedRoom();
			};	
		//draw bordere arround the room user list 
		new CieCornerSquare(this.__arrPanel['rooms'], ((this.__hvSpacer * 3) + this.__listWidth + this.__buttWidth), this.__hvSpacer, this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)), [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//create the scrool pane for the room list
		var depth =  this.__arrPanel['rooms'].getNextHighestDepth(); //patch for F** macromedia shit
		this.__arrPane['roomuser'] = this.__arrPanel['rooms'].createClassObject(ScrollPane, 'ROOMUSERLIST', depth + 1);		
		this.__arrPane['roomuser'].setStyle('borderStyle', 'none');
		this.__arrPane['roomuser'].hScrollPolicy = false;
		this.__arrPane['roomuser'].vLineScrollSize = CieStyle.__panel.__scrollSize;
		this.__arrPane['roomuser'].tabEnabled = false;
		this.__arrPane['roomuser'].vScrollPolicy = 'auto';		
		this.__arrPane['roomuser'].setSize(this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)));
		this.__arrPane['roomuser'].move(((this.__hvSpacer * 3) + this.__listWidth + this.__buttWidth), this.__hvSpacer);
		this.__arrPane['roomuser'].contentPath = 'mvContent';
				
		//------USERS--------------------------------------------------------------------------------------
		//draw bordere arround the user list
		new CieCornerSquare(this.__arrPanel['users'], this.__hvSpacer, this.__hvSpacer, this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)), [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//create the scrool pane for the userlist
		var depth =  this.__arrPanel['users'].getNextHighestDepth(); //patch for F** macromedia shit
		this.__arrPane['user'] = this.__arrPanel['users'].createClassObject(ScrollPane, 'USERLIST', depth + 1);		
		this.__arrPane['user'].setStyle('borderStyle', 'none');
		this.__arrPane['user'].hScrollPolicy = false;
		this.__arrPane['user'].vLineScrollSize = CieStyle.__panel.__scrollSize;
		this.__arrPane['user'].tabEnabled = false;
		this.__arrPane['user'].vScrollPolicy = 'auto';		
		this.__arrPane['user'].setSize(this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)));
		this.__arrPane['user'].move(this.__hvSpacer, this.__hvSpacer);
		this.__arrPane['user'].contentPath = 'mvContent';
		//set a butt to refresh
		var bTUsersRefresh = new CieButton(this.__arrPanel['users'], gLang[100], this.__buttWidth, this.__buttHeight, (this.__listWidth + (this.__hvSpacer * 2)), this.__hvSpacer);	
		bTUsersRefresh.getMovie().__class = this;
		bTUsersRefresh.getMovie().onRelease = function(Void):Void{
			this.__class.drawUserList();
			};
		//set a butt kill
		var bTUsersKill = new CieButton(this.__arrPanel['users'], gLang[104], this.__buttWidth, this.__buttHeight, (this.__listWidth + (this.__hvSpacer * 2)), (this.__buttHeight + (this.__hvSpacer * 2)));	
		bTUsersKill.getMovie().__class = this;
		bTUsersKill.getMovie().onRelease = function(Void):Void{
			this.__class.Killed('users');
			};
		//set a butt banned
		var bTUsersBanned = new CieButton(this.__arrPanel['users'], gLang[105], this.__buttWidth, this.__buttHeight, (this.__listWidth + (this.__hvSpacer * 2)), ((this.__buttHeight * 2) + (this.__hvSpacer * 3)));	
		bTUsersBanned.getMovie().__class = this;
		bTUsersBanned.getMovie().onRelease = function(Void):Void{
			this.__class.Banned('users');
			};
				
		//------PRIVATE--------------------------------------------------------------------------------------------------
		//draw bordere arround the private list
		new CieCornerSquare(this.__arrPanel['private'], this.__hvSpacer, this.__hvSpacer, (this.__listWidth * 1.5), (this.__panelLastSize.__height - (this.__hvSpacer * 2)), [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//create the scrool pane for the private list
		var depth = this.__arrPanel['private'].getNextHighestDepth(); //patch for F** macromedia shit
		this.__arrPane['private'] = this.__arrPanel['private'].createClassObject(ScrollPane, 'PRIVLIST', depth + 1);		
		this.__arrPane['private'].setStyle('borderStyle', 'none');
		this.__arrPane['private'].hScrollPolicy = false;
		this.__arrPane['private'].vLineScrollSize = CieStyle.__panel.__scrollSize;
		this.__arrPane['private'].tabEnabled = false;
		this.__arrPane['private'].vScrollPolicy = 'auto';		
		this.__arrPane['private'].setSize((this.__listWidth * 1.5), (this.__panelLastSize.__height - (this.__hvSpacer * 2)));
		this.__arrPane['private'].move(this.__hvSpacer, this.__hvSpacer);
		this.__arrPane['private'].contentPath = 'mvContent';
		//set a butt to clear
		var bTPrivateClear = new CieButton(this.__arrPanel['private'], "clear", this.__buttWidth, this.__buttHeight, ((this.__listWidth * 1.5)+ (this.__hvSpacer * 2)), this.__hvSpacer);	
		bTPrivateClear.getMovie().__class = this;
		bTPrivateClear.getMovie().onRelease = function(Void):Void{
			cFunc.deleteAllPrivateData();
			};
				
		//------TRACE--------------------------------------------------------------------------------------------------
		//the box output text
		this.__mvTrace = this.__arrPanel['trace'].attachMovie('mvTraceTexte', 'TRACE_TEXTE', this.__arrPanel['trace'].getNextHighestDepth());
		//pos
		this.__mvTrace._x = this.__hvSpacer;
		this.__mvTrace._y = this.__hvSpacer;
		//size
		this.__mvTrace.txtInfos._height = this.__panelLastSize.__height - (this.__hvSpacer * 2);
		this.__mvTrace.txtInfos._width = this.__panelLastSize.__width - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvTrace.txtInfos.text = gLang[106];
		//the scroll
		this.__mvTrace.mvDescriptionScroll._x = this.__mvTrace.txtInfos._width;
		//the square around
		new CieCornerSquare(this.__arrPanel['trace'], this.__hvSpacer, this.__hvSpacer, (this.__mvTrace.txtInfos._width + CieStyle.__profil.__scrollWidth), this.__mvTrace.txtInfos._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//sub tabs for tabs attenotion on trace event
		if(this.__subTabManager == undefined){
			this.__subTabManager = cFunc.getPanelTabManager(['irc','_tl',this.__tabName,'_tl']);
			}
					
		//------BANNED--------------------------------------------------------------------------------------------------	
		//draw bordere arround the private list
		new CieCornerSquare(this.__arrPanel['banned'], this.__hvSpacer, this.__hvSpacer, this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)), [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//create the scrool pane for the private list
		var depth =  this.__arrPanel['banned'].getNextHighestDepth(); //patch for F** macromedia shit
		this.__arrPane['unbanned'] = this.__arrPanel['banned'].createClassObject(ScrollPane, 'BANNEDLIST', depth + 1);		
		this.__arrPane['unbanned'].setStyle('borderStyle', 'none');
		this.__arrPane['unbanned'].hScrollPolicy = false;
		this.__arrPane['unbanned'].vLineScrollSize = CieStyle.__panel.__scrollSize;
		this.__arrPane['unbanned'].tabEnabled = false;
		this.__arrPane['unbanned'].vScrollPolicy = 'auto';		
		this.__arrPane['unbanned'].setSize(this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)));
		this.__arrPane['unbanned'].move(this.__hvSpacer, this.__hvSpacer);
		this.__arrPane['unbanned'].contentPath = 'mvContent';
		//set a butt to refresh
		var bTBannedRefresh = new CieButton(this.__arrPanel['banned'], gLang[100], this.__buttWidth, this.__buttHeight, (this.__listWidth + (this.__hvSpacer * 2)), this.__hvSpacer);	
		bTBannedRefresh.getMovie().__class = this;
		bTBannedRefresh.getMovie().onRelease = function(Void):Void{
			this.__class.drawBannedList();
			};
		//set a butt for unbanned
		var bTUsersUnBanned = new CieButton(this.__arrPanel['banned'], "unbann", this.__buttWidth, this.__buttHeight, (this.__listWidth + (this.__hvSpacer * 2)), (this.__buttHeight + (this.__hvSpacer * 2)));	
		bTUsersUnBanned.getMovie().__class = this;
		bTUsersUnBanned.getMovie().onRelease = function(Void):Void{
			this.__class.UnBanned();
			};
			
		//------MESSAGE--------------------------------------------------------------------------------------------------	
		//the box output text
		this.__mvMessage = this.__arrPanel['message'].attachMovie('mvChatTexteInput', 'MESSAGE_TEXTE', this.__arrPanel['message'].getNextHighestDepth());
		//pos
		this.__mvMessage._x = this.__hvSpacer;
		this.__mvMessage._y = this.__hvSpacer;
		//size
		this.__mvMessage.txtInfos._height = this.__panelLastSize.__height - (this.__hvSpacer * 3) - this.__buttHeight - 10;
		this.__mvMessage.txtInfos._width = this.__panelLastSize.__width - (this.__hvSpacer * 2);
		this.__mvMessage.txtInfos.text = "...";
		//the square around
		new CieCornerSquare(this.__arrPanel['message'], this.__hvSpacer, this.__hvSpacer, (this.__mvMessage.txtInfos._width), this.__mvMessage.txtInfos._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//the send button
		this.__sendButt = new CieButton(this.__arrPanel['message'], gLang[173], this.__buttWidth, this.__buttHeight, (this.__panelLastSize.__width - this.__hvSpacer - this.__buttWidth), (this.__panelLastSize.__height - this.__hvSpacer - this.__buttHeight - 10));	
		this.__sendButt.getMovie().__class = this;
		this.__sendButt.getMovie().onRelease = function(Void):Void{
			this.__class.sendMessageToAll();
			};
		//le radio box des lang a choisir pour l'envoi
		this.__optionBoxLang = new CieOptionBox(this.__arrPanel['message'], this.__arrLangChoices, 'lang');
		//set the default values to french
		this.__optionBoxLang.setSelectionValue(0);	
		//place it
		this.__optionBoxLang.redraw(this.__hvSpacer, (this.__panelLastSize.__height - (this.__hvSpacer * 2)- this.__buttHeight - 10));
		
		//------RESIZE--------------------------------------------------------------------------------------------------			
		//register the class to the top panel so it can recevie a resize event
		this.__arrPanelObject['users'].__class.registerObject(this.__registeredForResizeEvent['users']);	
		this.__arrPanelObject['rooms'].__class.registerObject(this.__registeredForResizeEvent['rooms']);	
		this.__arrPanelObject['private'].__class.registerObject(this.__registeredForResizeEvent['private']);	
		this.__arrPanelObject['trace'].__class.registerObject(this.__registeredForResizeEvent['trace']);	
		this.__arrPanelObject['banned'].__class.registerObject(this.__registeredForResizeEvent['banned']);	
		this.__arrPanelObject['message'].__class.registerObject(this.__registeredForResizeEvent['message']);
		
		//------DRAW--------------------------------------------------------------------------------------------------	
		//drawRoomList
		this.drawRoomList();
		//draw user list
		this.drawUserList();	
		//draw private list if rights are good
		this.drawPrivateList();	
		//fill the list
		this.drawBannedList();
		};
		
	/*************************************************************************************************************************************************/
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieAdmin{
		return this;
		};
		
		
	/*************************************************************************************************************************************************/	
	//draw the banned list
	private function drawBannedList(Void):Void{	
		//first clean up 
		this.clearList('banned');	
		//dbs	
		var arrBannedDB = cFunc.getBannedDB();	
		var mv = this.__arrPane['unbanned'].content;
		for(var o in arrBannedDB){
			//Debug("BANNED_LIST: " + arrBannedDB[o]);
			var tmpHeight = mv._height;
			this.__arrListMovie['banned'][o] = mv.attachMovie('mvPlusAdmin','mvPlusAdmin_' + o, mv.getNextHighestDepth());
			this.__arrListMovie['banned'][o].txtInfos.htmlText = '<font color="#333333">'  + o + '</font>';
			this.__arrListMovie['banned'][o].mvPlusBg.gotoAndStop('_0');
			this.__arrListMovie['banned'][o]._y = tmpHeight;
			//on click on line
			this.__arrListMovie['banned'][o].mvPlusBg.useHandCursor = false;
			this.__arrListMovie['banned'][o].mvPlusBg.__selected = false;
			this.__arrListMovie['banned'][o].mvPlusBg.onRelease = function(Void):Void{
				this.mvBgOver.gotoAndStop('_off');
				if(this.__selected){
					this.gotoAndStop('_0');
					this.__selected = false;
				}else{
					this.gotoAndStop('_1');
					this.__selected = true;
					}
				};
			this.__arrListMovie['banned'][o].mvPlusBg.onRollOver = function(Void):Void{
				this.mvBgOver.gotoAndStop('_on');
				};	
			this.__arrListMovie['banned'][o].mvPlusBg.onRollOut = 
			this.__arrListMovie['banned'][o].mvPlusBg.onDragOut = 
			this.__arrListMovie['banned'][o].mvPlusBg.onReleaseOutside = function(Void):Void{
				this.mvBgOver.gotoAndStop('_off');
				};	
			}
		};
	
	/*************************************************************************************************************************************************/	
	//draw the room list choice
	private function drawPrivateList(Void):Void{
		//first clean up 
		this.clearList('private');
		var mv = this.__arrPane['private'].content;
		var arrPrivateDB = cFunc.getPrivateDB();
		
		//try a sort
		var arrRoomSort = new Array();
		for(var o in arrPrivateDB){
			arrRoomSort.push(o);
			}
		arrRoomSort.sort();
		//loop and show
		for(var i=0; i<arrRoomSort.length; i++){
			var o = arrRoomSort[i];	
			var tmpHeight = mv._height;
			//the line
			this.__arrListMovie['private'][o] = mv.attachMovie('mvPlus','mvPlus_' + arrPrivateDB[o]['name'], mv.getNextHighestDepth());
			this.__arrListMovie['private'][o].txtInfos.htmlText = '<font color="#333333">'  + arrPrivateDB[o]['name'] + '</font>';
			this.__arrListMovie['private'][o].mvPlusBg.gotoAndStop('_1');
			this.__arrListMovie['private'][o]._y = tmpHeight;
			//on click on line
			this.__arrListMovie['private'][o].mvPlusBg.useHandCursor = false;
			this.__arrListMovie['private'][o].mvPlusBg.__roomName = o;
			this.__arrListMovie['private'][o].mvPlusBg.onRelease = function(Void):Void{
				this.mvBgOver.gotoAndStop('_off');
				cFunc.createSpecialRoom(this.__roomName);
				};
			this.__arrListMovie['private'][o].mvPlusBg.onRollOver = function(Void):Void{
				this.mvBgOver.gotoAndStop('_on');
				};	
			this.__arrListMovie['private'][o].mvPlusBg.onRollOut = 
			this.__arrListMovie['private'][o].mvPlusBg.onDragOut = 
			this.__arrListMovie['private'][o].mvPlusBg.onReleaseOutside = function(Void):Void{
				this.mvBgOver.gotoAndStop('_off');
				};
			}
		};	

	/*************************************************************************************************************************************************/	
	//draw the room list choice
	private function drawRoomList(Void):Void{
		//clean de la liste de droite des users relative a la room
		this.clearRoomUserButton();
		this.clearRoomUserList();
		this.clearList('rooms');
		var mv = this.__arrPane['room'].content;
		var arrRoomDB = cFunc.getRoomDB();
		
		//try a sort
		var arrRoomSort = new Array();
		for(var o in arrRoomDB){
			arrRoomSort.push(o);
			}
		arrRoomSort.sort();	
		//loop and show
		for(var i=0; i<arrRoomSort.length; i++){
			var o = arrRoomSort[i];
			//the room name wihout tthe prefix
			var strRoomName = arrRoomDB[o]['name'];
			var tmpHeight = mv._height;
			//the line
			this.__arrListMovie['rooms'][o] = mv.attachMovie('mvPlusAdmin','mvPlusAdmin_' + strRoomName, mv.getNextHighestDepth());
			this.__arrListMovie['rooms'][o].txtInfos.htmlText = '<font color="#333333">'  + strRoomName + '</font> <font color="#bbbbbb">[' + arrRoomDB[o]['usercount'] + ']' + '</font>'
			this.__arrListMovie['rooms'][o].mvPlusBg.gotoAndStop('_0');
			this.__arrListMovie['rooms'][o]._y = tmpHeight;
			//on click on line
			this.__arrListMovie['rooms'][o].mvPlusBg.useHandCursor = false;
			this.__arrListMovie['rooms'][o].mvPlusBg.__selected = false;
			this.__arrListMovie['rooms'][o].mvPlusBg.__roomName = o;
			this.__arrListMovie['rooms'][o].mvPlusBg.onRelease = function(Void):Void{
				this.mvBgOver.gotoAndStop('_off');
				if(this.__selected){
					this.gotoAndStop('_0');
					this.__selected = false;
				}else{
					this.gotoAndStop('_1');
					this.__selected = true;
					}
				};
			this.__arrListMovie['rooms'][o].mvPlusBg.onRollOver = function(Void):Void{
				this.mvBgOver.gotoAndStop('_on');
				};	
			this.__arrListMovie['rooms'][o].mvPlusBg.onRollOut = 
			this.__arrListMovie['rooms'][o].mvPlusBg.onDragOut = 
			this.__arrListMovie['rooms'][o].mvPlusBg.onReleaseOutside = function(Void):Void{
				this.mvBgOver.gotoAndStop('_off');
				};
			}
		};

	/*************************************************************************************************************************************************/	
	//draw the room list choice
	private function drawUserList(Void):Void{
		//first clean up 
		this.clearList('users');
		var mv = this.__arrPane['user'].content;
		var arrUsersDB = cFunc.getUserDB();
		
		//try a sort
		var arrUserSort = new Array();
		for(var o in arrUsersDB){
			arrUserSort.push({__name: arrUsersDB[o]['pseudo'], __id:arrUsersDB[o]['nopub']});
			}
		arrUserSort.sortOn(['__name']);
				
		for(var i=0; i<arrUserSort.length; i++){
			var o = arrUserSort[i]['__id'];
			var arrInfos = arrUsersDB[o];
			//admin sign
			var strAdmin = '';
			if(arrInfos['admin'] == 1){
				strAdmin = '@';
				}
			//height increment
			var tmpHeight = mv._height;
			//the line
			this.__arrListMovie['users'][o] = mv.attachMovie('mvPlusAdmin','mvPlusAdmin_' + arrInfos['pseudo'], mv.getNextHighestDepth());
			if(arrUsersDB[o]['state'] == '0'){
				this.__arrListMovie['users'][o].txtInfos.htmlText = '<font color="#cccccc">' + strAdmin + arrInfos['pseudo'] + '</font>';
			}else{
				this.__arrListMovie['users'][o].txtInfos.htmlText = '<font color="#333333">' + strAdmin + arrInfos['pseudo'] + '</font>';
				}
			this.__arrListMovie['users'][o].mvPlusBg.gotoAndStop('_0');
			this.__arrListMovie['users'][o]._y = tmpHeight;
					
			//register for online notif
			this.__arrListMovie['users'][o].__arrInfos = arrInfos;
			this.__arrListMovie['users'][o].updateObject = function(nopub:String, state:String):Boolean{
				if(nopub == this.__arrInfos['nopub']){
					if(state == '0'){
						//the title
						this.txtInfos.htmlText = '<font color="#cccccc">'  + this.__arrInfos['pseudo'] + '</font>';
					}else{
						//the title
						this.txtInfos.htmlText = '<font color="#333333">'  + this.__arrInfos['pseudo'] + '</font>';
						}
					}
				return true;	
				};
			cSockManager.registerObjectForOnlineNotification(this.__arrListMovie['users'][o]);
			
			//on click on line
			this.__arrListMovie['users'][o].mvPlusBg.__selected = false;
			this.__arrListMovie['users'][o].mvPlusBg.__arrInfos = arrInfos;
			this.__arrListMovie['users'][o].mvPlusBg.useHandCursor = false;
			this.__arrListMovie['users'][o].mvPlusBg.onRelease = function(Void):Void{
				this.mvBgOver.gotoAndStop('_off');
				if(this.__selected){
					this.gotoAndStop('_0');
					this.__selected = false;
				}else{
					this.gotoAndStop('_1');
					this.__selected = true;
					}
				};
			this.__arrListMovie['users'][o].mvPlusBg.onRollOver = function(Void):Void{
				this.mvBgOver.gotoAndStop('_on');
				};	
			this.__arrListMovie['users'][o].mvPlusBg.onRollOut = 
			this.__arrListMovie['users'][o].mvPlusBg.onDragOut = 
			this.__arrListMovie['users'][o].mvPlusBg.onReleaseOutside = function(Void):Void{
				this.mvBgOver.gotoAndStop('_off');
				};
			}
		};	
		
	/*************************************************************************************************************************************************/	
	//draw the room user list when a single room is selected in the left side
	private function drawRoomUserList(strRoomName:String):Void{
		//first clean up 
		this.clearRoomUserList();
		//ref and DBs		
		var mv = this.__arrPane['roomuser'].content;
		var arrUsersDB = cFunc.getUserDB();
		var arrRoomModerator = cFunc.getModeratorByRoomName(strRoomName);
		var arrUsersNoPubs = cFunc.getUserDBByRoomName(strRoomName);
		//try a sort
		var arrUserSort = new Array();
		for(var o in arrUsersNoPubs){
			arrUserSort.push({__name: arrUsersDB[arrUsersNoPubs[o]]['pseudo'], __id:arrUsersDB[arrUsersNoPubs[o]]['nopub']});
			}
		arrUserSort.sortOn(['__name']);
		//loop and show
		for(var i=0; i<arrUserSort.length; i++){
			//ref
			var o = arrUserSort[i]['__id'];
			var arrInfos = arrUsersDB[o];
			//admin sign
			var strAdmin = '';
			if(arrInfos['admin'] == 1){
				strAdmin = '@';
				}
			//moderator sign	
			if(strAdmin == ''){
				if(arrRoomModerator[arrInfos['nopub']] != undefined){
					strAdmin = '+';
					}
				}	
			//tot hreight
			var tmpHeight = mv._height;
			//the line
			this.__arrListMovie['roomusers'][o] = mv.attachMovie('mvPlusAdmin','mvPlusAdmin_' + arrInfos['pseudo'], mv.getNextHighestDepth());
			if(arrUsersDB[o]['state'] == '0'){
				this.__arrListMovie['roomusers'][o].txtInfos.htmlText = '<font color="#cccccc">' + strAdmin + arrInfos['pseudo'] + '</font>';
			}else{
				this.__arrListMovie['roomusers'][o].txtInfos.htmlText = '<font color="#333333">' + strAdmin + arrInfos['pseudo'] + '</font>';
				}
			this.__arrListMovie['roomusers'][o].mvPlusBg.gotoAndStop('_0');
			this.__arrListMovie['roomusers'][o]._y = tmpHeight;
			//register for online notif
			this.__arrListMovie['roomusers'][o].__roomNameWithPrefix = strRoomName;
			this.__arrListMovie['roomusers'][o].__arrInfos = arrInfos;
			this.__arrListMovie['roomusers'][o].updateObject = function(nopub:String, state:String):Boolean{
				if(nopub == this.__arrInfos['nopub']){
					//admin sign
					var strAdmin = '';
					if(this.__arrInfos['admin'] == 1){
						strAdmin = '@';
						}
					//moderator sign	
					if(strAdmin == ''){
						var arrRoomModerator = cFunc.getModeratorByRoomName(this.__roomNameWithPrefix);
						if(arrRoomModerator[this.__arrInfos['nopub']] != undefined){
							strAdmin = '+';
							}
						}	
					if(state == '0'){
						//the title
						this.txtInfos.htmlText = '<font color="#cccccc">'  + strAdmin + this.__arrInfos['pseudo'] + '</font>';
					}else{
						//the title
						this.txtInfos.htmlText = '<font color="#333333">'  + strAdmin + this.__arrInfos['pseudo'] + '</font>';
						}
					}
				return true;	
				};
			//register for moderator notif
			this.__arrListMovie['roomusers'][o].updateModerator = function(roomNameNoPrefix:String, nopub:String):Boolean{
				if((nopub == this.__arrInfos['nopub']) && (roomNameNoPrefix == this.__roomNameWithPrefix.substr(1, this.__roomNameWithPrefix.length))){
					//admin sign
					var strAdmin = '';
					if(this.__arrInfos['admin'] == 1){
						strAdmin = '@';
						}
					//moderator sign	
					if(strAdmin == ''){
						var arrRoomModerator = cFunc.getModeratorByRoomName(this.__roomNameWithPrefix);
						if(arrRoomModerator[this.__arrInfos['nopub']] != undefined){
							strAdmin = '+';
							}
						}	
					if(this.__arrInfos['state'] == '0'){
						//the title
						this.txtInfos.htmlText = '<font color="#cccccc">'  + strAdmin + this.__arrInfos['pseudo'] + '</font>';
					}else{
						//the title
						this.txtInfos.htmlText = '<font color="#333333">'  + strAdmin + this.__arrInfos['pseudo'] + '</font>';
						}
					}
				return true;	
				};

			//register for moderator/state modification	
			cSockManager.registerObjectForOnlineNotification(this.__arrListMovie['roomusers'][o]);
			//on click on line
			this.__arrListMovie['roomusers'][o].mvPlusBg.__roomName = strRoomName;
			this.__arrListMovie['roomusers'][o].mvPlusBg.__selected = false;
			this.__arrListMovie['roomusers'][o].mvPlusBg.__arrInfos = arrInfos;
			this.__arrListMovie['roomusers'][o].mvPlusBg.useHandCursor = false;
			this.__arrListMovie['roomusers'][o].mvPlusBg.onRelease = function(Void):Void{
				this.mvBgOver.gotoAndStop('_off');
				if(this.__selected){
					this.gotoAndStop('_0');
					this.__selected = false;
				}else{
					this.gotoAndStop('_1');
					this.__selected = true;
					}
				};
			this.__arrListMovie['roomusers'][o].mvPlusBg.onRollOver = function(Void):Void{
				this.mvBgOver.gotoAndStop('_on');
				};	
			this.__arrListMovie['roomusers'][o].mvPlusBg.onRollOut = 
			this.__arrListMovie['roomusers'][o].mvPlusBg.onDragOut = 
			this.__arrListMovie['roomusers'][o].mvPlusBg.onReleaseOutside = function(Void):Void{
				this.mvBgOver.gotoAndStop('_off');
				};
			}
		//clear les buttons
		this.clearRoomUserButton();
		//draw the button for users actions
		if(arrUserSort.length > 0){
			this.drawRoomUserButton(strRoomName, false);	
		}else{
			this.drawRoomUserButton(strRoomName, true);	
			}
		};
		
	/*************************************************************************************************************************************************/	
	//draw the button for actions on selected users from room selected
	private function clearRoomUserButton(Void):Void{	
		//remove old ones
		if(this.__arrRoomUsersButtons != undefined){
			for(var o in this.__arrRoomUsersButtons){
				this.__arrRoomUsersButtons[o].removeButton();
				delete this.__arrRoomUsersButtons[o];
				}
			}
		};	
	
	/*************************************************************************************************************************************************/	
	//clear la liste des rooms_users
	private function clearRoomUserList(Void):Void{
		//first clean up 
		if(this.__arrListMovie['roomusers'] != undefined){
			for(var o in this.__arrListMovie['roomusers']){
				cSockManager.unregisterObjectForOnlineNotification(this.__arrListMovie['roomusers'][o]);
				this.__arrListMovie['roomusers'][o].removeMovieClip();
				delete this.__arrListMovie['roomusers'][o];
				}
			}
		this.__arrListMovie['roomusers'] = new Array();	
		};	
		
	/*************************************************************************************************************************************************/		
	//clear les movieClip d'une liste
	private function clearList(strListName:String):Void{
		if(this.__arrListMovie[strListName] != undefined){
			for(var o in this.__arrListMovie[strListName]){
				if(strListName == 'users'){
					cSockManager.unregisterObjectForOnlineNotification(this.__arrListMovie[strListName][o]);
					}
				this.__arrListMovie[strListName][o].removeMovieClip();
				delete this.__arrListMovie[strListName][o];
				}
			}
		this.__arrListMovie[strListName] = new Array();
		};	
		
	
	/*************************************************************************************************************************************************/
	//draw the button for actions on selected users from room selected
	private function drawRoomUserButton(strRoomName:String, bEmpty:Boolean):Void{	
		if(!bEmpty){	
			//set a butt kill
			this.__arrRoomUsersButtons['killuser'] = new CieButton(this.__arrPanel['rooms'], gLang[104], this.__buttWidth, this.__buttHeight, ((this.__listWidth * 2) + (this.__buttWidth) + (this.__hvSpacer * 4)), this.__hvSpacer);
			this.__arrRoomUsersButtons['killuser'].getMovie().__class = this;
			this.__arrRoomUsersButtons['killuser'].getMovie().onRelease = function(Void):Void{
				this.__class.Killed('roomusers');
				};
			//set a butt banned
			this.__arrRoomUsersButtons['bannuser'] = new CieButton(this.__arrPanel['rooms'], gLang[105], this.__buttWidth, this.__buttHeight, ((this.__listWidth * 2) + (this.__buttWidth) + (this.__hvSpacer * 4)), (this.__buttHeight + (this.__hvSpacer * 2)));	
			this.__arrRoomUsersButtons['bannuser'].getMovie().__class = this;
			this.__arrRoomUsersButtons['bannuser'].getMovie().onRelease = function(Void):Void{
				this.__class.Banned('roomusers');
				};
			//set a butt promote/unpromote to moderator
			this.__arrRoomUsersButtons['promoteuser'] = new CieButton(this.__arrPanel['rooms'], gLang[107], this.__buttWidth, this.__buttHeight, ((this.__listWidth * 2) + (this.__buttWidth) + (this.__hvSpacer * 4)), ((this.__buttHeight * 2)+ (this.__hvSpacer * 3)));	
			this.__arrRoomUsersButtons['promoteuser'].getMovie().__class = this;
			this.__arrRoomUsersButtons['promoteuser'].getMovie().onRelease = function(Void):Void{
				this.__class.Promoted();
				};
			//set a butt kicked out of a room
			this.__arrRoomUsersButtons['kickuser'] = new CieButton(this.__arrPanel['rooms'], gLang[108], this.__buttWidth, this.__buttHeight, ((this.__listWidth * 2) + (this.__buttWidth) + (this.__hvSpacer * 4)), ((this.__buttHeight * 3)+ (this.__hvSpacer * 4)));	
			this.__arrRoomUsersButtons['kickuser'].getMovie().__class = this;
			this.__arrRoomUsersButtons['kickuser'].getMovie().onRelease = function(Void):Void{
				this.__class.Kicked();
				};	
			
			//set a butt kicked out of a room
			this.__arrRoomUsersButtons['pseudo'] = new CieButton(this.__arrPanel['rooms'], "pseudo", this.__buttWidth, this.__buttHeight, ((this.__listWidth * 2) + (this.__buttWidth) + (this.__hvSpacer * 4)), ((this.__buttHeight * 4)+ (this.__hvSpacer * 5)));	
		}else{
			this.__arrRoomUsersButtons['pseudo'] = new CieButton(this.__arrPanel['rooms'], "pseudo", this.__buttWidth, this.__buttHeight, ((this.__listWidth * 2) + (this.__buttWidth) + (this.__hvSpacer * 4)), this.__hvSpacer);	
			}
		this.__arrRoomUsersButtons['pseudo'].getMovie().__roomname = strRoomName.substring(1, strRoomName.length);
		this.__arrRoomUsersButtons['pseudo'].getMovie().__class = this;
		this.__arrRoomUsersButtons['pseudo'].getMovie().onRelease = function(Void):Void{
			cFunc.addModeratorByPseudo(this.__roomname);
			};
			
		};
		
	/*************************************************************************************************************************************************/
	//get the selected room from the left list
	private function getUsersFromSelectedRoom(Void):Void{
		//check de ceux selectionne
		var cmptRoomsSelected = 0;
		var tmpRoomName:String = '';
		for(var o in this.__arrListMovie['rooms']){
			if(this.__arrListMovie['rooms'][o].mvPlusBg.__selected){
				tmpRoomName = o;
				cmptRoomsSelected++;
				}
			}
		if(cmptRoomsSelected > 1){	
			//avertit ccar un a la fois uniqueent pour cette commande
			new CieTextMessages('MB_OK', gLang[110], gLang[109]);
		}else{	
			//draw
			this.drawRoomUserList(tmpRoomName);
			}
		};	
	
	/*************************************************************************************************************************************************/	
	public function reset(Void):Void{
		//unregistered resize event
		for(var o in this.__registeredForResizeEvent){
			if(typeof(this.__registeredForResizeEvent[o]) == 'object'){
				this.__arrPanelObject[o].__class.removeRegisteredObject(this.__registeredForResizeEvent[o]);
				delete this.__registeredForResizeEvent[o];
				}
			}
		//scrool pane
		destroyObject('ROOMLIST');
		destroyObject('ROOMUSERLIST');
		destroyObject('USERLIST');
		destroyObject('PRIVLIST');
		destroyObject('BANNEDLIST');
		//arrays
		delete this.__arrListMovie;
		delete this.__arrPane;
		delete this.__arrPanel;
		delete this.__arrPanelObject;
		delete this.__tabManager;
		delete this.__subTabManager;
		delete this.__registeredForResizeEvent;
		};
	
	/*************************************************************************************************************************************************/
	//kill un ou des users
	private function Killed(strListName:String):Void{
		//check de ceux selectionne
		var arrTmpNoPub = new Array();
		var arrTmpPseudo = new Array();
		//si ne vient pas de la liste rooms->users
		for(var o in this.__arrListMovie[strListName]){
			if(this.__arrListMovie[strListName][o].mvPlusBg.__selected){
				arrTmpNoPub.push(o);
				arrTmpPseudo.push(this.__arrListMovie[strListName][o].mvPlusBg.__arrInfos['pseudo']);
				}
			}
		if(arrTmpNoPub.length > 0){
			cFunc.killUser(arrTmpNoPub, arrTmpPseudo);
			}
		};	

	/*************************************************************************************************************************************************/
	private function Banned(strListName:String):Void{
		//check de ceux selectionne
		var arrTmpNoPub = new Array();
		var arrTmpPseudo = new Array();
		//si ne vient pas de la liste rooms->users
		for(var o in this.__arrListMovie[strListName]){
			if(this.__arrListMovie[strListName][o].mvPlusBg.__selected){
				arrTmpNoPub.push(o);
				arrTmpPseudo.push(this.__arrListMovie[strListName][o].mvPlusBg.__arrInfos['pseudo']);
				}
			}
		if(arrTmpNoPub.length > 0){
			cFunc.bannUser(arrTmpNoPub, arrTmpPseudo);
			}
		};	
		
	/*************************************************************************************************************************************************/
	private function UnBanned(Void):Void{
		var arrTmpNoPub = new Array();
		for(var o in this.__arrListMovie['banned']){
			if(this.__arrListMovie['banned'][o].mvPlusBg.__selected){
				arrTmpNoPub.push(o);
				}
			}
		if(arrTmpNoPub.length > 0){
			cFunc.unBannUser(arrTmpNoPub);
			}
		};		
		
	/*************************************************************************************************************************************************/
	private function Promoted(Void):Void{
		//check de ceux selectionne
		var cmptUsersSelected = 0;
		var arrTmpNoPub = new Array();
		var arrTmpPseudo = new Array();
		var strRoomName:String = '';
		//si ne vient pas de la liste rooms->users
		for(var o in this.__arrListMovie['roomusers']){
			if(this.__arrListMovie['roomusers'][o].mvPlusBg.__selected){
				arrTmpNoPub.push(o);
				arrTmpPseudo.push(this.__arrListMovie['roomusers'][o].mvPlusBg.__arrInfos['pseudo']);
				strRoomName = this.__arrListMovie['roomusers'][o].mvPlusBg.__roomName;
				cmptUsersSelected++;
				}
			}
		
		if(cmptUsersSelected > 1){	
			//avertit ccar un a la fois uniqueent pour cette commande
			new CieTextMessages('MB_OK', gLang[111], gLang[109]);
		}else{
			if((arrTmpNoPub.length > 0) && (strRoomName != '')){
				//strip the # from the room name
				if(strRoomName.substr(0,1) == '#'){
					strRoomName = strRoomName.substr(1,strRoomName.length);
					}
				cFunc.promoteUser(arrTmpNoPub, arrTmpPseudo, strRoomName, false);
				}
			}	
		};	
		
		
	/*************************************************************************************************************************************************/
	private function Deleted(Void):Void{
		//check de ceux selectionne
		var arrTmpRoom = new Array();
		for(var o in this.__arrListMovie['rooms']){
			if(this.__arrListMovie['rooms'][o].mvPlusBg.__selected){
				var tmpRoomName = this.__arrListMovie['rooms'][o].mvPlusBg.__roomName;
				//strip the # from the room name
				if(tmpRoomName.substr(0,1) == '#'){
					tmpRoomName = tmpRoomName.substr(1,tmpRoomName.length);
					}
				arrTmpRoom.push(tmpRoomName);
				}
			}
		if(arrTmpRoom.length > 0){
			cFunc.deleteRoom(arrTmpRoom);
			}
		};		
		
	/*************************************************************************************************************************************************/
	private function Kicked(Void):Void{
		//check de ceux selectionne
		var cmptUsersSelected = 0;
		var strRoomName:String = '';
		//si ne vient pas de la liste rooms->users
		for(var o in this.__arrListMovie['roomusers']){
			if(this.__arrListMovie['roomusers'][o].mvPlusBg.__selected){
				var arrTmpInfos = this.__arrListMovie['roomusers'][o].mvPlusBg.__arrInfos;
				strRoomName = this.__arrListMovie['roomusers'][o].mvPlusBg.__roomName;
				cmptUsersSelected++;
				}
			}
		
		if(cmptUsersSelected > 1){	
			//avertit ccar un a la fois uniqueent pour cette commande
			new CieTextMessages('MB_OK', gLang[112], gLang[109]);
		}else{
			if((arrTmpInfos != undefined) && (strRoomName != '')){
				//strip the # from the room name
				if(strRoomName.substr(0,1) == '#'){
					strRoomName = strRoomName.substr(1,strRoomName.length);
					}
				cFunc.kickUser(arrTmpInfos, strRoomName);
				}
			}
		};		
	
	/*************************************************************************************************************************************************/
	private function sendMessageToAll(Void):Void{
		//le message
		var strToSend = this.__mvMessage.txtInfos.text;
		//la langue
		var strLang = this.__arrLangChoices[Number(this.__optionBoxLang.getSelectionValue())][0];
		if(strToSend != "" && strLang != undefined && strLang != ""){
			cFunc.sendMessageToAll(strToSend, strLang);
			}
		};
		
	/*************************************************************************************************************************************************/
	public function updateTrace(str:String):Void{
		this.__mvTrace.txtInfos.text += '\n> ' + str;
		this.__mvTrace.txtInfos.scroll += 50000;	
		//si un update est fait mettre le focus dessus pour attirer l'attention
		if(!this.__subTabManager.getTabFocusByName('trace')){
			this.__subTabManager.giveTabAttention('trace', true);
			};
		};
	
	/*************************************************************************************************************************************************/
	//called when registered the the container panel 
	public function resizeTrace(w:Number, h:Number):Void{
		//les rooms list
		this.__panelLastSize.__height = h;
		this.__panelLastSize.__width = w;
		//clear drawing
		this.__arrPanel['trace'].clear();
		//textBox
		this.__mvTrace.txtInfos._width = w - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvTrace.txtInfos._height = h - (this.__hvSpacer * 2);
		//the scroll
		this.__mvTrace.mvDescriptionScroll._x = this.__mvTrace.txtInfos._width;
		this.__mvTrace.mvDescriptionScroll.setSize(CieStyle.__profil.__scrollWidth, this.__mvTrace.txtInfos._height);
		//draw
		new CieCornerSquare(this.__arrPanel['trace'], this.__hvSpacer, this.__hvSpacer, (this.__mvTrace.txtInfos._width + CieStyle.__profil.__scrollWidth), this.__mvTrace.txtInfos._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		};
		
	/*************************************************************************************************************************************************/
	//called when registered the the container panel 
	public function resizeRooms(w:Number, h:Number):Void{	
		//les rooms list
		this.__panelLastSize.__height = h;
		this.__panelLastSize.__width = w;
		//clear
		this.__arrPanel['rooms'].clear();
		//draw bordere arround the room list
		new CieCornerSquare(this.__arrPanel['rooms'], this.__hvSpacer, this.__hvSpacer, this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)), [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//list
		this.__arrPane['room'].setSize(this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)));
		this.__arrPane['room'].move(this.__hvSpacer, this.__hvSpacer);
		//les rooms_>users list
		new CieCornerSquare(this.__arrPanel['rooms'], ((this.__hvSpacer * 3) + this.__listWidth + this.__buttWidth), this.__hvSpacer, this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)), [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//create the scrool pane for the room list
		this.__arrPane['roomuser'].setSize(this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)));
		this.__arrPane['roomuser'].move(((this.__hvSpacer * 3) + this.__listWidth + this.__buttWidth), this.__hvSpacer);
		};
		
	/*************************************************************************************************************************************************/
	//called when registered the the container panel 
	public function resizeUsers(w:Number, h:Number):Void{	
		//les rooms list
		this.__panelLastSize.__height = h;
		this.__panelLastSize.__width = w;
		//clear
		this.__arrPanel['users'].clear();
		//draw bordere arround the room list
		new CieCornerSquare(this.__arrPanel['users'], this.__hvSpacer, this.__hvSpacer, this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)), [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//list
		this.__arrPane['user'].setSize(this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)));
		this.__arrPane['user'].move(this.__hvSpacer, this.__hvSpacer);
		};	
		
	/*************************************************************************************************************************************************/
	//called when registered the the container panel 
	public function resizePrivate(w:Number, h:Number):Void{	
		//les rooms list
		this.__panelLastSize.__height = h;
		this.__panelLastSize.__width = w;
		//clear
		this.__arrPanel['private'].clear();
		//draw bordere arround the room list
		new CieCornerSquare(this.__arrPanel['private'], this.__hvSpacer, this.__hvSpacer, (this.__listWidth * 1.5), (this.__panelLastSize.__height - (this.__hvSpacer * 2)), [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//list
		this.__arrPane['private'].setSize((this.__listWidth * 1.5), (this.__panelLastSize.__height - (this.__hvSpacer * 2)));
		this.__arrPane['private'].move(this.__hvSpacer, this.__hvSpacer);
		};	
		
	/*************************************************************************************************************************************************/
	//called when registered the the container panel 
	public function resizeBanned(w:Number, h:Number):Void{	
		//les rooms list
		this.__panelLastSize.__height = h;
		this.__panelLastSize.__width = w;
		//clear
		this.__arrPanel['banned'].clear();
		//draw bordere arround the room list
		new CieCornerSquare(this.__arrPanel['banned'], this.__hvSpacer, this.__hvSpacer, this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)), [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//list
		this.__arrPane['banned'].setSize(this.__listWidth, (this.__panelLastSize.__height - (this.__hvSpacer * 2)));
		this.__arrPane['banned'].move(this.__hvSpacer, this.__hvSpacer);
		};	

	/*************************************************************************************************************************************************/
	//called when registered the the container panel 
	public function resizeMessage(w:Number, h:Number):Void{	
		//les rooms list
		this.__panelLastSize.__height = h;
		this.__panelLastSize.__width = w;
		//clear
		this.__arrPanel['message'].clear();
		//size
		this.__mvMessage.txtInfos._height = this.__panelLastSize.__height - (this.__hvSpacer * 3) - this.__buttHeight - 10;
		this.__mvMessage.txtInfos._width = this.__panelLastSize.__width - (this.__hvSpacer * 2);
		//the square around
		new CieCornerSquare(this.__arrPanel['message'], this.__hvSpacer, this.__hvSpacer, (this.__mvMessage.txtInfos._width), this.__mvMessage.txtInfos._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		//the send button
		this.__sendButt.redraw((this.__panelLastSize.__width - this.__hvSpacer - this.__buttWidth), (this.__panelLastSize.__height - this.__hvSpacer - this.__buttHeight - 10));
		//option lang
		this.__optionBoxLang.redraw(this.__hvSpacer, (this.__panelLastSize.__height - (this.__hvSpacer * 2) - this.__buttHeight - 10));
		};	
	
	}	