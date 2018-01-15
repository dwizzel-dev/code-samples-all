/*



*/

//in order of use
import control.CieButton;
import graphic.CieCornerSquare;

import mx.containers.ScrollPane;

dynamic class irc.CieRoomIrc{

	static private var __className = "CieRoomIrc";
		
	private var __tabName:String;
	private var __arrRoomInfos:Array;
	private var __tabManager:Object;
	private var __oPanelLeft:Object;
	private var __panelLeft:MovieClip;
	private var __oPanelRight:Object;
	private var __panelRight:MovieClip;
	private var __closeButt:Object;
	private var __deleteButt:Object;
	private var __sendButt:Object;
	private var __panelLeftLastSize:Object;
	private var __hvSpacer:Number;
	private var __mvChatInput:MovieClip;
	private var __mvChatOutput:MovieClip;
	private var __userPane:Object;
	private var __arrList:Array;
	
	private var __msgCount:Number = 0;
	private var __maxMsgHolder:Number;
		
	public function CieRoomIrc(tabName:String, tabManager:Object, arrRoomInfos:Array){
		//
		this.__tabName = tabName;
		this.__tabManager = tabManager;
		this.__arrRoomInfos = arrRoomInfos;
		this.__hvSpacer = CieStyle.__profil.__hvSpacer;
		this.__yTopOffSet = 66 + this.__hvSpacer;
	
		//for msg
		this.__maxMsgHolder = 100000; //environ 500 par ligne de message avec le formatage html 100000/500 = 200 messages
		//
		this.Init();		
		//on va ensuite chercher les infos des usagers dans la room //on envoi la requete au serveur
		cSockManager.socketSend("ENTERROOM:" + this.__arrRoomInfos['name']);	
		//on update la derniere action de l'usager
		cFunc.updateLastUserAction();
		};
	
	
	/**************************************************************************************************************/
	
	private function Init(Void):Void{
		
		var buttHeight = 35;
		
		//xml to create the tab
		strXml = '<PT n="' + this.__tabName + '" model="deux" title="#' + this.__arrRoomInfos['name'] + '" closebutt="false">';
		strXml += '<P n="_tl" content="mvProfilRoom" bgcolor="' + CieStyle.__panel.__bgColor + '" scroll="false" effect="false"></P>';
		strXml += '<P n="_tr" content="mvContent" bgcolor="' + CieStyle.__panel.__bgColor + '" scroll="false" effect="false"></P>';
		strXml += '</PT>';
		
		//load the xml and build the treee
		cFunc.changeNodeValue(new XML(strXml),['irc','_tl','SKIP']);
		
		//get the panel class for drawing
		this.__oPanelLeft = cFunc.getPanelObject(['irc','_tl',this.__tabName,'_tl']);
		this.__oPanelRight = cFunc.getPanelObject(['irc','_tl',this.__tabName,'_tr']);
		
		//get the panel movieclip for drawing
		this.__panelLeft = this.__oPanelLeft.__class.getPanelContent();
		this.__panelRight = this.__oPanelRight.__class.getPanelContent();
		
		//get the W and H of top panel
		this.__panelLeftLastSize = this.__oPanelLeft.__class.getPanelSize();
		
		//draw the close butt in the top right corner of top right panel
		this.__closeButt = new CieButton(this.__panelRight, gLang[169], 75, buttHeight, (this.__oPanelRight.__class.getPanelSize().__width - this.__hvSpacer - 75), this.__hvSpacer);
		this.__closeButt.getMovie().__class = this;
		this.__closeButt.getMovie().onRelease = function(Void):Void{
			this.__class.Destroy();
			};
			
	
		new CieCornerSquare(this.__panelRight, 1, this.__yTopOffSet, (this.__oPanelRight.__class.getPanelSize().__width - (this.__hvSpacer)), (this.__panelLeftLastSize.__height - this.__yTopOffSet - this.__hvSpacer), [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		
		//the user list
		var depth =  this.__panelRight.getNextHighestDepth(); //patch for F** macromedia shit
		this.__userPane = this.__panelRight.createClassObject(ScrollPane, 'LIST_' + depth, depth + 1);		
		this.__userPane.setStyle('borderStyle', 'none');
		this.__userPane.hScrollPolicy = false;
		this.__userPane.vLineScrollSize = CieStyle.__panel.__scrollSize;
		this.__userPane.tabEnabled = false;
		this.__userPane.vScrollPolicy = 'auto';		
		this.__userPane.setSize(this.__oPanelRight.__class.getPanelSize().__width - (this.__hvSpacer), (this.__panelLeftLastSize.__height - this.__yTopOffSet - this.__hvSpacer));
		this.__userPane.move(1, this.__yTopOffSet);
		this.__userPane.contentPath = 'mvContent';
			
		//draw
		this.drawForOutput();			
		this.drawForInput();
		this.drawUserList();
		
		//register the class to the top panel so it can recevie a resize event
		this.__oPanelLeft.__class.registerObject(this);
		
		//register to receive the panelTab focus event on TRUE
		this.__tabManager.registerForOnFocusEvent(this.__tabName, this);

		};
	
	/**************************************************************************************************************/	
	//draw the user list
	public function drawUserList(Void):Void{
		//Debug("CieRooms.drawUserList()");
		//first clean up 
		if(this.__arrList != undefined){
			for(var o in this.__arrList){
				cSockManager.unregisterObjectForOnlineNotification(this.__arrList[o]);
				this.__arrList[o].removeMovieClip();
				delete this.__arrList[o];
				}
			}	
		this.__arrList = new Array();
		
		
		var arrUsersDB = cFunc.getUserDB(); //get users DB
		var arrRoomModerator = cFunc.getModeratorByRoomName(this.__tabName);
		
		//try a sort
		var arrUserSort = new Array();
		for(var o in this.__arrRoomInfos['users']){
			arrUserSort.push({__name: arrUsersDB[o]['pseudo'], __id:o});
			}
		arrUserSort.sortOn(['__name']);
		//		
		var mv = this.__userPane.content;
		//for(var o in arrNoPubs){
		//for(var o in this.__arrRoomInfos['users']){
		for(var i=0; i<arrUserSort.length; i++){
			var o = arrUserSort[i]['__id'];	
			var arrInfos = arrUsersDB[this.__arrRoomInfos['users'][o]];
			
			var strAdmin = '';
			if(arrInfos['admin'] == 1){
				strAdmin = '@';
				}
				
			if(strAdmin == ''){
				if(arrRoomModerator[arrInfos['nopub']] != undefined){
					strAdmin = '+';
					}
				}	
			
			var tmpHeight = mv._height;
			//create
			this.__arrList[arrInfos['nopub']] = mv.attachMovie('mvPlus','mvPlus_' + arrInfos['nopub'], mv.getNextHighestDepth());
			//title
			if(arrInfos['state'] == '0'){
				this.__arrList[arrInfos['nopub']].txtInfos.htmlText = '<font color="#cccccc">' + strAdmin + arrInfos['pseudo'] + '</font>'
			}else{
				this.__arrList[arrInfos['nopub']].txtInfos.htmlText = '<font color="#333333">' + strAdmin + arrInfos['pseudo'] + '</font>'
				}
			//state	
			this.__arrList[arrInfos['nopub']].mvPlusBg.gotoAndStop('_' + arrInfos['state']);
			//blocked
			if(arrInfos['blocked']){
				this.__arrList[arrInfos['nopub']].mvBlocked.gotoAndStop('_1');
			}else{
				this.__arrList[arrInfos['nopub']].mvBlocked.gotoAndStop('_0');
				}
			//position
			this.__arrList[arrInfos['nopub']]._y = tmpHeight;
			
			//pour les notification de state par le socketManager
			this.__arrList[arrInfos['nopub']].__roomNameWithPrefix = this.__tabName;
			this.__arrList[arrInfos['nopub']].__roomName = this.__arrRoomInfos['name'];
			this.__arrList[arrInfos['nopub']].__arrInfos = arrInfos;
			this.__arrList[arrInfos['nopub']].updateObject = function(nopub:String, state:String):Boolean{
				//Debug("updateObject: " + nopub + ', ' + state);
				if(nopub == this.__arrInfos['nopub']){
					
					var strAdmin = '';
					if(this.__arrInfos['admin'] == 1){
						strAdmin = '@';
						}
						
					if(strAdmin == ''){
						var arrRoomModerator = cFunc.getModeratorByRoomName(this.__roomNameWithPrefix);
						//Debug("MODERATORS[" + this.__roomNameWithPrefix + "]: " + arrRoomModerator);
						if(arrRoomModerator[this.__arrInfos['nopub']] != undefined){
							strAdmin = '+';
							}
						}	
					
					//trace("\t\t -FOUND:" + nopub + ':' + state);
					this.mvPlusBg.gotoAndStop('_' + state);
					//the title
					if(state == '0'){
						this.txtInfos.htmlText = '<font color="#cccccc">' + strAdmin + this.__arrInfos['pseudo'] + '</font>'
					}else{
						this.txtInfos.htmlText = '<font color="#333333">' + strAdmin + this.__arrInfos['pseudo'] + '</font>'
						}
					//the actions
					this.mvPlusBg.onRelease = function(Void):Void{
						this.mvBgOver.gotoAndStop('_off');
						cFunc.createPrivateRoom(this.__arrUser['nopub'], this.__arrUser, false, this.__roomName);
						};
					this.mvPlusBg.onRollOver = function(Void):Void{
						this.mvBgOver.gotoAndStop('_on');
						};
					this.mvPlusBg.onRollOut = 
					this.mvPlusBg.onDragOut = 
					this.mvPlusBg.onReleaseOutside = function(Void):Void{
						this.mvBgOver.gotoAndStop('_off');
						};	
					}
				return true;	
				};
			this.__arrList[arrInfos['nopub']].updateBlocked = function(nopub:String, blocked:String):Boolean{
				if(nopub == this.__arrInfos['nopub']){
					//trace("\t\t -FOUND:" + nopub + ':' + state);
					this.mvBlocked.gotoAndStop('_' + blocked);
					}
				return true;	
				};		
			cSockManager.registerObjectForOnlineNotification(this.__arrList[arrInfos['nopub']]);	
			
			//on click
			this.__arrList[arrInfos['nopub']].mvPlusBg.useHandCursor = false;
			this.__arrList[arrInfos['nopub']].mvPlusBg.__roomName = this.__arrRoomInfos['name']; //without the #
			this.__arrList[arrInfos['nopub']].mvPlusBg.__arrUser = arrInfos;
			this.__arrList[arrInfos['nopub']].mvPlusBg.__tabManager = this.__tabManager;
			this.__arrList[arrInfos['nopub']].mvPlusBg.onRelease = function(Void):Void{
				this.mvBgOver.gotoAndStop('_off');
				cFunc.createPrivateRoom(this.__arrUser['nopub'], this.__arrUser, false, this.__roomName);
				};
			this.__arrList[arrInfos['nopub']].mvPlusBg.onRollOver = function(Void):Void{
				this.mvBgOver.gotoAndStop('_on');
				};	
			this.__arrList[arrInfos['nopub']].mvPlusBg.onRollOut = 
			this.__arrList[arrInfos['nopub']].mvPlusBg.onDragOut = 
			this.__arrList[arrInfos['nopub']].mvPlusBg.onReleaseOutside = function(Void):Void{
				this.mvBgOver.gotoAndStop('_off');
				};
			}
		};
		
	/**************************************************************************************************************/	
	
	//fenetre du bas pour le input text and button
	private function drawForInput(Void):Void{	
		
		//draw the send butt in the bottom left pannel in the right corner
		this.__sendButt = new CieButton(this.__panelLeft, gLang[173], 75, (CieStyle.__profil.__fixedHeightBottom - this.__hvSpacer), (this.__panelLeftLastSize.__width - this.__hvSpacer - 75), (this.__panelLeftLastSize.__height - CieStyle.__profil.__fixedHeightBottom));
		this.__sendButt.getMovie().__class = this;
		this.__sendButt.getMovie().onRelease = function(Void):Void{
			this.__class.sendMessage();
			};	
		
		//draw the input box
		this.__mvChatInput = this.__panelLeft.attachMovie('mvChatTexteInput', 'CHAT_TEXTE_INPUT', this.__panelLeft.getNextHighestDepth());
		//size
		this.__mvChatInput.txtInfos._height = CieStyle.__profil.__fixedHeightBottom - this.__hvSpacer;
		this.__mvChatInput.txtInfos._width = this.__panelLeftLastSize.__width - this.__sendButt.getMovie()._width - (3 * this.__hvSpacer);
		this.__mvChatInput.txtInfos.maxChars = 256;
		this.__mvChatInput.txtInfos.text = gLang[163];
		//pos
		this.__mvChatInput._x = this.__hvSpacer;
		this.__mvChatInput._y = (this.__panelLeftLastSize.__height - CieStyle.__profil.__fixedHeightBottom);	
		//border suaer
		new CieCornerSquare(this.__panelLeft, this.__hvSpacer, this.__mvChatInput._y, (this.__mvChatInput.txtInfos._width), this.__mvChatInput.txtInfos._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xF7F7F7, 100], ['ext', 0.5, 0x999999, 100]);
		};
		
	/**************************************************************************************************************/	
	
	//fenetre du bas pour le input text and button
	private function drawForOutput(Void):Void{
		//infos
		this.__panelLeft.txtPseudo.htmlText = '<b>' + this.__arrRoomInfos['name'] + '</b>'; 
		this.__panelLeft.txtSlogan.text = "\"" + this.__arrRoomInfos['title'] + "\"";
			
		//photo
		//check si c'est un path complet (pur les privileges rooms) sinon c'est une image generique
		if(this.__arrRoomInfos['image'].indexOf("http") != -1){
			Debug("ROOM_IMAGE_PATH: " + this.__arrRoomInfos['image']);
			this.__panelLeft.mvPhoto.mvPicture.loadMovie(this.__arrRoomInfos['image']);	
		}else{
			Debug("ROOM_IMAGE_PATH: " + BC.__server.__roomthumbs + '/' + this.__arrRoomInfos['image']);
			this.__panelLeft.mvPhoto.mvPicture.loadMovie(BC.__server.__roomthumbs + '/' + this.__arrRoomInfos['image']);	
			}
		
		//the box output text
		this.__mvChatOutput = this.__panelLeft.attachMovie('mvChatTexte', 'CHAT_TEXTE_ALL', this.__panelLeft.getNextHighestDepth());
		//pos
		this.__mvChatOutput._x = this.__hvSpacer;
		this.__mvChatOutput._y = this.__yTopOffSet;
			
		//size
		this.__mvChatOutput.txtInfos._height = (this.__panelLeftLastSize.__height - this.__yTopOffSet ) - (this.__hvSpacer * 3) - CieStyle.__profil.__fixedHeightBottom;
		this.__mvChatOutput.txtInfos._width = this.__panelLeftLastSize.__width - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvChatOutput.txtInfos.text = gLang[164];
		//pos y the typing box
		this.__mvChatOutput.txtTyping._y = this.__mvChatOutput.txtInfos._height + this.__mvChatOutput.txtInfos._y + (this.__hvSpacer / 2); 
		this.__mvChatOutput.txtTyping._width = this.__panelLeftLastSize.__width - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvChatOutput.txtTyping.htmlText = gLang[165] + '<b>' + this.__arrRoomInfos['name'] + '</b>';
		//the scroll
		this.__mvChatOutput.mvDescriptionScroll._x = this.__mvChatOutput.txtInfos._width;
		//the square around
		new CieCornerSquare(this.__panelLeft, this.__hvSpacer, this.__yTopOffSet, (this.__mvChatOutput.txtInfos._width + CieStyle.__profil.__scrollWidth), this.__mvChatOutput.txtInfos._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		};	
		
	/**************************************************************************************************************/	
	
	//called when registered the the container panel (top because the bottom will have a fixed height but same width has toppanel)
	public function resize(w:Number, h:Number):Void{
		
		//replace the values
		this.__panelLeftLastSize.__width = w;
		this.__panelLeftLastSize.__height = h;
		
		//the sep bar and list
		this.__panelRight.clear();
		this.__userPane.setSize(this.__oPanelRight.__class.getPanelSize().__width - (this.__hvSpacer), (this.__panelLeftLastSize.__height - this.__yTopOffSet - this.__hvSpacer));
		this.__userPane.move(1, this.__yTopOffSet);
		new CieCornerSquare(this.__panelRight, 1, this.__yTopOffSet, (this.__oPanelRight.__class.getPanelSize().__width - (this.__hvSpacer)), (this.__panelLeftLastSize.__height - this.__yTopOffSet - this.__hvSpacer), [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		
		//reposition the sendButt
		this.__sendButt.redraw((this.__panelLeftLastSize.__width - this.__sendButt.getMovie()._width - this.__hvSpacer), (this.__panelLeftLastSize.__height - CieStyle.__profil.__fixedHeightBottom));
		//chat output text
		this.__mvChatOutput.txtInfos._width = this.__panelLeftLastSize.__width - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvChatOutput.txtInfos._height = (this.__panelLeftLastSize.__height - this.__yTopOffSet) - (this.__hvSpacer * 3) - CieStyle.__profil.__fixedHeightBottom;
		//chat typing text
		this.__mvChatOutput.txtTyping._width = this.__panelLeftLastSize.__width - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvChatOutput.txtTyping._y = this.__mvChatOutput.txtInfos._height + this.__mvChatOutput.txtInfos._y + (this.__hvSpacer / 2); 
		//chat input text
		this.__mvChatInput.txtInfos._width = this.__panelLeftLastSize.__width - this.__sendButt.getMovie()._width - (3 * this.__hvSpacer);
		this.__mvChatInput._y = (this.__panelLeftLastSize.__height - CieStyle.__profil.__fixedHeightBottom);
		//the scroll
		this.__mvChatOutput.mvDescriptionScroll._x = this.__mvChatOutput.txtInfos._width;
		this.__mvChatOutput.mvDescriptionScroll.setSize(CieStyle.__profil.__scrollWidth, this.__mvChatOutput.txtInfos._height);
		//suare
		this.__panelLeft.clear();
		new CieCornerSquare(this.__panelLeft, this.__hvSpacer, this.__yTopOffSet, (this.__mvChatOutput.txtInfos._width + CieStyle.__profil.__scrollWidth), this.__mvChatOutput.txtInfos._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		new CieCornerSquare(this.__panelLeft, this.__hvSpacer, this.__mvChatInput._y, (this.__mvChatInput.txtInfos._width), this.__mvChatInput.txtInfos._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xF7F7F7, 100], ['ext', 0.5, 0x999999, 100]);
		};
	
	/**************************************************************************************************************/	
	
	private function getInputText(Void):String{
		//text
		var tmp:String = this.__mvChatInput.txtInfos.htmlText;
		var strReturn:String = '';
		//check for "\n"
		for(var i=0; i<tmp.length; i++){
			if(tmp.charCodeAt(i) != 13){
				strReturn += tmp.charAt(i);
				}
			}
		this.__mvChatInput.txtInfos.text = '';
		return strReturn;
		};
	
	/**************************************************************************************************************/	
	
	private function sendMessage(Void):Void{
		//text
		var str = cFunc.FormatString(this.getInputText());
		if(str != ''){
			//on envoi au serveur
			cSockManager.socketSend("ROOMMSG:" + this.__arrRoomInfos['name'] + "," + escape(str));
			//on update la derniere action de l'usager
			cFunc.updateLastUserAction();
			//on update le message du client
			this.updateMessage(str, BC.__user.__nopub);
			}
		};
		
	/**************************************************************************************************************/	
	//quand un usager rentre ou sort dans la salle
	private function showUserInRoom(bIn:Boolean, arrUserInfos:Array):Void{
		//text 
		if(bIn){
			var str:String = '<b>' +  arrUserInfos['pseudo'] + '</b>' + gLang[166];
			this.__mvChatOutput.txtInfos.htmlText += '<font size="-5">\n</font>' + '<font color="' + CieStyle.__profil.__textColorIN + '">' + str + '</font>';
		}else{
			var str:String = '<b>' +  arrUserInfos['pseudo'] + '</b>' + gLang[167];
			this.__mvChatOutput.txtInfos.htmlText += '<font size="-5">\n</font>' + '<font color="' + CieStyle.__profil.__textColorOUT + '">' + str + '</font>';
			}
		//the scrooll
		this.__mvChatOutput.txtInfos.scroll += 50000;	
		};	
	
	/**************************************************************************************************************/	
	/*
	private function Blocked(Void):Void{
		//text
		cFunc.blockUser(this.__arrRoomInfos);
		};
	*/	
	/**************************************************************************************************************/	
	//pour recevoir une notification de focus sur le tab
	public function notifyOnFocusEvent(Void):Boolean{
		//trace("ROOM.notifyOnFocusEvent: " + this.__tabName);
		//give the input text the focus
		Selection.setFocus(this.__mvChatInput.txtInfos);
		var len = this.__mvChatInput.txtInfos.text.length;
		Selection.setSelection(len, len);
		return true;
		};	
		
	/**************************************************************************************************************/	
	
	//NEW 06-05-2010
	private function updateMessage(msg:String, noPub:String):Void{
		//text
		if(noPub != BC.__user.__nopub){
			this.__msgCount++;
			var arrUsers = cFunc.getUserDB(); //get users DB
			var userName = arrUsers[noPub]['pseudo'];
			var userTextColor;
			if(userName != undefined){
				userTextColor = arrUsers[noPub]['textcolor'];
			}else{
				userName = 'USR' + noPub;
				userTextColor = '#666666';
				}
			this.__mvChatOutput.txtInfos.htmlText += '<font size="-5">\n</font>' +  '<font color="' + userTextColor + '"><b>' + userName + '</b>' + ": "  + msg + '</font>';
			//typing
			var dDate = new Date();
			var dMinute = dDate.getMinutes();
			if(dMinute < 10){
				dMinute = '0' + dMinute;
				}
			var dSecond = dDate.getSeconds();
			if(dSecond < 10){
				dSecond = '0' + dSecond;
				}	
			var lastSentMsgDate = dDate.getHours() + ':' + dMinute + ':' + dSecond;
			this.__mvChatOutput.txtTyping.htmlText = gLang[168] + lastSentMsgDate;
			
			//if this tab dont have focus give it
			if(!this.__tabManager.getTabFocusByName(this.__tabName)){
				this.__tabManager.giveTabAttention(this.__tabName, true);
				};
			
		}else{
			this.__mvChatOutput.txtInfos.htmlText += '<font size="-5">\n</font>' + '<font color="#bbbbbb"><b>' + BC.__user.__pseudo + '</b>' + ": " + msg + '</font>';
			}
		
		//check for to many message
		var tmpStr = this.__mvChatOutput.txtInfos.htmlText;
		if(tmpStr.length > this.__maxMsgHolder){
			var strSearch = "</TEXTFORMAT>";
			//if too long then strip the last 20 messages
			for(var i=0; i<40; i++){
				var pos = tmpStr.indexOf(strSearch);
				if(pos){
					tmpStr = tmpStr.substring((pos + strSearch.length), tmpStr.length);
				}else{
					break;
					}
				}
			this.__mvChatOutput.txtInfos.htmlText = tmpStr;	
			}
		//the scrooll
		this.__mvChatOutput.txtInfos.scroll += 50000;	
		};
	
	/*
	private function updateMessage(msg:String, noPub:String):Void{
		
		//text
		if(noPub != BC.__user.__nopub){
			this.__msgCount++;
			var arrUsers = cFunc.getUserDB(); //get users DB
			var userName = arrUsers[noPub]['pseudo'];
			var userTextColor = arrUsers[noPub]['textcolor'];
			this.__mvChatOutput.txtInfos.htmlText += '<font size="-5">\n</font>' +  '<font color="' + userTextColor + '"><b>' + userName + '</b>' + ": "  + msg + '</font>';
			//typing
			var dDate = new Date();
			var dMinute = dDate.getMinutes();
			if(dMinute < 10){
				dMinute = '0' + dMinute;
				}
			var dSecond = dDate.getSeconds();
			if(dSecond < 10){
				dSecond = '0' + dSecond;
				}	
			var lastSentMsgDate = dDate.getHours() + ':' + dMinute + ':' + dSecond;
			this.__mvChatOutput.txtTyping.htmlText = gLang[168] + lastSentMsgDate;
			
			//if this tab dont have focus give it
			if(!this.__tabManager.getTabFocusByName(this.__tabName)){
				this.__tabManager.giveTabAttention(this.__tabName, true);
				};
			
		}else{
			this.__mvChatOutput.txtInfos.htmlText += '<font size="-5">\n</font>' + '<font color="#bbbbbb"><b>' + BC.__user.__pseudo + '</b>' + ": " + msg + '</font>';
			}
		
		//check for to many message
		var tmpStr = this.__mvChatOutput.txtInfos.htmlText;
		if(tmpStr.length > this.__maxMsgHolder){
			var strSearch = "</TEXTFORMAT>";
			//if too long then strip the last 20 messages
			for(var i=0; i<40; i++){
				var pos = tmpStr.indexOf(strSearch);
				if(pos){
					tmpStr = tmpStr.substring((pos + strSearch.length), tmpStr.length);
				}else{
					break;
					}
				}
			this.__mvChatOutput.txtInfos.htmlText = tmpStr;	
			}
		//the scrooll
		this.__mvChatOutput.txtInfos.scroll += 50000;	
		};
	*/
	/**************************************************************************************************************/	
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieRoomIrc{
		return this;
		};
		
	/**************************************************************************************************************/		

	public function Destroy(Void):Void{
		//on avertit le serveur que l'on est sortit de la room
		cSockManager.socketSend("LEAVEROOM:" + this.__arrRoomInfos['name']);
		//on update la derniere action de l'usager
		cFunc.updateLastUserAction();
		//unregistered resize event
		this.__oPanelLeft.__class.removeRegisteredObject(this);
		//the list movies user on state change
		for(var o in this.__arrList){
			cSockManager.unregisterObjectForOnlineNotification(this.__arrList[o]);	
			}
		delete this.__arrList;
		//notify du focus panelTab event
		this.__tabManager.unregisterForOnFocusEvent(this.__tabName);
		//the list panel
		destroyObject('LIST_' +  this.__tabName);
		//the button
		this.__closeButt.removeButton();

		this.__sendButt.removeButton();
		//remove inpouts and outputs
		this.__mvChatInput.removeMovieClip();
		this.__mvChatOutput.removeMovieClip();
		//elete ref and arrays
		delete this.__oPanelLeft;
		delete this.__panelLeft;
		delete this.__oPanelRight;
		delete this.__panelRight;
		delete this.__arrRoomInfos;
		//delete the tab by name
		this.__tabManager.removeTabByName(this.__tabName);
		//delete the tabManager ref
		delete this.__tabManager;
		//tell CIeFunc that the room is closed
		cFunc.closePublicRoom(this.__tabName);
		//delete the oboject
		delete this;
		};
	
	/**************************************************************************************************************/
	
	
	/**************************************************************************************************************/	
	
	
	/**************************************************************************************************************/
	
	
	/**************************************************************************************************************/
	
		
	}	