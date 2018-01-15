/*



*/

//in order of use
import control.CieButton;
import graphic.CieCornerSquare;
import messages.CieTextMessages;

dynamic class irc.CieUserIrc{

	static private var __className = "CieUserIrc";
		
	private var __tabName:String;
	private var __arrUserInfos:Array;
	private var __tabManager:Object;
	private var __oPanelTop:Object;
	private var __panelTop:MovieClip;
	private var __closeButt:Object;
	private var __sendButt:Object;
	private var __blockButt:Object;
	private var __promoteButt:Object;
	private var __killButt:Object;
	private var __panelLastSize:Object;
	private var __hvSpacer:Number;
	private var __mvChatInput:MovieClip;
	private var __mvChatOutput:MovieClip;
	
	private var __msgCount:Number = 0;
	
	private var __maxMsgHolder:Number;
	
	private var __fromRoomNameWithoutPrefix:String;
	private var __fromRoomNameWithPrefix:String;
	
	public function CieUserIrc(tabName:String, tabManager:Object, arrUserInfos:Array, bNoFocus:Boolean, fromRoomName:String){
		//
		this.__tabName = tabName;
		this.__tabManager = tabManager;
		this.__arrUserInfos = arrUserInfos;
		this.__hvSpacer = CieStyle.__profil.__hvSpacer;
		this.__fromRoomNameWithoutPrefix = fromRoomName;
		this.__fromRoomNameWithPrefix = '#' + fromRoomName;
		
		//for msg
		this.__maxMsgHolder = 100000; //environ 500 par ligne de message avec le formatage html 100000/500 = 200 messages
		
		//xml to create the tab
		if(!bNoFocus){
			strXml = '<PT n="' + this.__tabName + '" model="un" title="' + this.__arrUserInfos['pseudo'] + '" closebutt="false">';
			strXml += '<P n="_tl" content="mvProfilDetails" bgcolor="' + CieStyle.__panel.__bgColor + '" scroll="false" effect="false"></P>';
			strXml += '</PT>';
		}else{
			strXml = '<PT n="' + this.__tabName + '" model="un" title="' + this.__arrUserInfos['pseudo'] + '" closebutt="false" nofocus="true">';
			strXml += '<P n="_tl" content="mvProfilDetails" bgcolor="' + CieStyle.__panel.__bgColor + '" scroll="false" effect="false"></P>';
			strXml += '</PT>';
			}
		
		//load the xml and build the treee
		cFunc.changeNodeValue(new XML(strXml),['irc','_tl','SKIP']);
				
		//get the panel class for drawing
		this.__oPanelTop = cFunc.getPanelObject(['irc','_tl',this.__tabName,'_tl']);
		
		//get the panel movieclip for drawing
		this.__panelTop = this.__oPanelTop.__class.getPanelContent();
		
		//get the W and H of top panel
		this.__panelLastSize = this.__oPanelTop.__class.getPanelSize();
				
		//draw
		this.drawForOutput();			
		this.drawForInput();		
		
		//register the class to the top panel so it can recevie a resize event
		this.__oPanelTop.__class.registerObject(this);
		
		//register to receive the panelTab focus event on TRUE
		this.__tabManager.registerForOnFocusEvent(this.__tabName, this);
		
		};
		
	/**************************************************************************************************************/	
	//pour recevoir une notification de focus sur le tab
	public function notifyOnFocusEvent(Void):Boolean{
		//trace("USER.notifyOnFocusEvent: " + this.__tabName);
		//give the input text the focus
		Selection.setFocus(this.__mvChatInput.txtInfos);
		var len = this.__mvChatInput.txtInfos.text.length;
		Selection.setSelection(len, len);
		return true;
		};		
		
	/**************************************************************************************************************/	
	
	//fenetre du bas pour le input text and button
	private function drawForInput(Void):Void{
		
		//draw the send butt in the bottom left pannel in the right corner
		this.__sendButt = new CieButton(this.__panelTop, gLang[173], 75, (CieStyle.__profil.__fixedHeightBottom - this.__hvSpacer), (this.__panelLastSize.__width - this.__hvSpacer - 75), (this.__panelLastSize.__height - CieStyle.__profil.__fixedHeightBottom));
		this.__sendButt.getMovie().__class = this;
		this.__sendButt.getMovie().onRelease = function(Void):Void{
			this.__class.sendMessage();
			};	
		
		//draw the input box
		this.__mvChatInput = this.__panelTop.attachMovie('mvChatTexteInput', 'CHAT_TEXTE_INPUT', this.__panelTop.getNextHighestDepth());
		//size
		this.__mvChatInput.txtInfos._height = CieStyle.__profil.__fixedHeightBottom - this.__hvSpacer;
		this.__mvChatInput.txtInfos._width = this.__panelLastSize.__width - this.__sendButt.getMovie()._width - (3 * this.__hvSpacer);
		this.__mvChatInput.txtInfos.maxChars = 256;
		this.__mvChatInput.txtInfos.text = gLang[163];
		//pos
		this.__mvChatInput._x = this.__hvSpacer;
		this.__mvChatInput._y = (this.__panelLastSize.__height - CieStyle.__profil.__fixedHeightBottom);	
		//border suaer
		new CieCornerSquare(this.__panelTop, this.__hvSpacer, this.__mvChatInput._y, (this.__mvChatInput.txtInfos._width), this.__mvChatInput.txtInfos._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xF7F7F7, 100], ['ext', 0.5, 0x999999, 100]);
		};
		
	/**************************************************************************************************************/	
	//callback du miniprofil request
	public function cbUserInfos(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//parse the return
		obj.__super.__class.parseXmlUserInfos(obj.__req.getXml().firstChild);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	public function parseXmlUserInfos(xmlNode:XMLNode):Void{
		var str:String = xmlNode.childNodes[0].firstChild.nodeValue;	
		var strErrAttr:String = xmlNode.childNodes[0].attributes.n;
		if(strErrAttr != 'error'){
			//split the data
			//no_publique, pseudo, age, album, photo, vocal, membership, orientation, sexe, relation, code_pays, region_id, ville_id, etat_civil, titre
			Debug("XML: " + str);
			var arrSplit = str.split('|');
			//goto
			this.__panelTop.gotoAndStop('_1');
			//photo
			if(arrSplit[4] == '2'){
				this.__panelTop.mvPhoto.mvPicture.loadMovie(BC.__server.__thumbs + arrSplit[0].substr(0,2) + '/' + arrSplit[1] + '.jpg');
			}else{
				this.__panelTop.mvPhoto.mvSexeLoader.gotoAndStop('_' + arrSplit[8]);
				}
			//link on the picture
			this.__panelTop.mvPhoto.__pseudo = arrSplit[1];
			this.__panelTop.mvPhoto.onRelease = function(){
				var path:String = "/profil/?ps=" + this.__pseudo + "&PHPSESSID=" + BC.__user.__sessionID;
				getURL(path, '_blank');
				};
				
			//infos pseudo
			this.__panelTop.txtPseudo.htmlText = '<b>' +  arrSplit[1] + ', ' + arrSplit[2] + ' ' + gLang[247] + '</b>'; 
			//perso
			this.__panelTop.txtInfos.htmlText = gLang['sexe_' + arrSplit[8]] + ", " + gLang['etatcivil_' + arrSplit[13]] + ", " + gLang['orientation_' + arrSplit[7]]; 
			//geo
			if(gGeo[arrSplit[10] + '_' + arrSplit[11] + '_' + arrSplit[12]] != undefined){
				this.__panelTop.txtInfos.htmlText += gGeo[arrSplit[10] + '_' + arrSplit[11] + '_' + arrSplit[12]];
				}
			//info reste
			this.__panelTop.txtInfos.htmlText += "<i>\"" + unescape(arrSplit[14].toLowerCase()) + "\"</i>";
			}
		}
		
	/**************************************************************************************************************/	
	
	//fenetre du bas pour le input text and button
	private function drawForOutput(Void):Void{	
		//
		var arrRoomModerator = cFunc.getModeratorByRoomName(this.__fromRoomNameWithPrefix);
		
		var buttHeight = 35;
		if(cFunc.checkIfModeratorOfThisRoom(this.__fromRoomNameWithoutPrefix) && (this.__arrUserInfos['admin'] != 1)){
			var buttHeight = 23;
			}
		
		//do a request to //fetch the user infos
		var arrD = new Array();
		arrD['methode'] = 'details';
		arrD['action'] = 'miniprofilconcatbynopub';
		arrD['arguments'] = this.__arrUserInfos['nopub'];
		//add the request
		cReqManager.addRequest(arrD, this.cbUserInfos, {__class:this}, false);
		
		//draw the close butt in the top right corner of top panel
		this.__closeButt = new CieButton(this.__panelTop, gLang[169], 75, buttHeight, (this.__panelLastSize.__width - this.__hvSpacer - 75), this.__hvSpacer);
		this.__closeButt.getMovie().__class = this;
		this.__closeButt.getMovie().onRelease = function(Void):Void{
			this.__class.Destroy();
			};
		
		//check if the profil is admin
		if(this.__arrUserInfos['admin'] != 1){	
			//draw the kill in the top right corner of top panel if he is moderator
			if(cFunc.checkIfModeratorOfThisRoom(this.__fromRoomNameWithoutPrefix)){
				//promote butt
				this.__promoteButt = new CieButton(this.__panelTop, gLang[107], 75, buttHeight, (this.__panelLastSize.__width - this.__hvSpacer - 75), (this.__closeButt.getMovie()._y + this.__closeButt.getMovie()._height + (this.__hvSpacer/2)));
				this.__promoteButt.getMovie().__class = this;
				this.__promoteButt.getMovie().onRelease = function(Void):Void{
					this.__class.Promoted();
					};	
				//check si lk'autre fait partis des des moderateurs de la room car un mods ne peut pas kicker un autre mods
				if(arrRoomModerator[this.__arrUserInfos['nopub']] == undefined){
					//kill butt juste si l'autre n'est pas moderator
					this.__killButt = new CieButton(this.__panelTop, gLang[108], 75, buttHeight, (this.__panelLastSize.__width - this.__hvSpacer - 75), (this.__promoteButt.getMovie()._y + this.__promoteButt.getMovie()._height + (this.__hvSpacer/2)));
					this.__killButt.getMovie().__class = this;
					this.__killButt.getMovie().onRelease = function(Void):Void{
						this.__class.Kicked();
						};
					}
			}else if(arrRoomModerator[this.__arrUserInfos['nopub']] == undefined){
				//draw the blocked/unbloked in the top right corner of top panel
				if(this.__arrUserInfos['blocked']){
					this.__blockButt = new CieButton(this.__panelTop, gLang[174], 75, buttHeight, (this.__panelLastSize.__width - this.__hvSpacer - 75), (this.__closeButt.getMovie()._y + this.__closeButt.getMovie()._height + (this.__hvSpacer/2)));
				}else{
					this.__blockButt = new CieButton(this.__panelTop, gLang[175], 75, buttHeight, (this.__panelLastSize.__width - this.__hvSpacer - 75), (this.__closeButt.getMovie()._y + this.__closeButt.getMovie()._height + (this.__hvSpacer/2)));
					}	
				this.__blockButt.getMovie().__class = this;
				this.__blockButt.getMovie().onRelease = function(Void):Void{
					this.__class.Blocked();
					};	
				}
			}	
				
		//the box output text
		this.__mvChatOutput = this.__panelTop.attachMovie('mvChatTexte', 'CHAT_TEXTE_ALL', this.__panelTop.getNextHighestDepth());
		//pos
		this.__mvChatOutput._x = this.__hvSpacer;
		this.__mvChatOutput._y = CieStyle.__profil.__yTopOffSet;
		//size
		this.__mvChatOutput.txtInfos._height = (this.__panelLastSize.__height - CieStyle.__profil.__yTopOffSet ) - (this.__hvSpacer * 3) - CieStyle.__profil.__fixedHeightBottom;
		this.__mvChatOutput.txtInfos._width = this.__panelLastSize.__width - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvChatOutput.txtInfos.text = gLang[177];
		//pos y the typing box
		this.__mvChatOutput.txtTyping._y = this.__mvChatOutput.txtInfos._height + this.__mvChatOutput.txtInfos._y + (this.__hvSpacer / 2); 
		this.__mvChatOutput.txtTyping._width = this.__panelLastSize.__width - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvChatOutput.txtTyping.htmlText = gLang[178];
		//the scroll
		this.__mvChatOutput.mvDescriptionScroll._x = this.__mvChatOutput.txtInfos._width;
		//the square around
		new CieCornerSquare(this.__panelTop, this.__hvSpacer, CieStyle.__profil.__yTopOffSet, (this.__mvChatOutput.txtInfos._width + CieStyle.__profil.__scrollWidth), this.__mvChatOutput.txtInfos._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		};	
		
	/**************************************************************************************************************/	
	
	//called when registered the the container panel (top because the bottom will have a fixed height but same width has toppanel)
	public function resize(w:Number, h:Number):Void{
		//replace the values
		this.__panelLastSize.__width = w;
		this.__panelLastSize.__height = h;
		//reposition the sendButt
		this.__sendButt.redraw((this.__panelLastSize.__width - this.__sendButt.getMovie()._width - this.__hvSpacer), (this.__panelLastSize.__height - CieStyle.__profil.__fixedHeightBottom));
		//reposition the closeButt
		this.__closeButt.redraw((this.__panelLastSize.__width - this.__closeButt.getMovie()._width - this.__hvSpacer), this.__hvSpacer);
		
		
		//if is moderator no block butt
		if(this.__promoteButt != undefined){
			//promote butt
			this.__promoteButt.redraw((this.__panelLastSize.__width - this.__closeButt.getMovie()._width - this.__hvSpacer), (this.__closeButt.getMovie()._y + this.__closeButt.getMovie()._height + (this.__hvSpacer/2)));
			//kicked butt
			this.__killButt.redraw((this.__panelLastSize.__width - this.__promoteButt.getMovie()._width - this.__hvSpacer), (this.__promoteButt.getMovie()._y + this.__promoteButt.getMovie()._height + (this.__hvSpacer/2)));
			
		}else{
			//reposition the blockButt
			this.__blockButt.redraw((this.__panelLastSize.__width - this.__closeButt.getMovie()._width - this.__hvSpacer), (this.__closeButt.getMovie()._y + this.__closeButt.getMovie()._height + (this.__hvSpacer/2)));
			}
		
		//chat output text
		this.__mvChatOutput.txtInfos._width = this.__panelLastSize.__width - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvChatOutput.txtInfos._height = (this.__panelLastSize.__height - CieStyle.__profil.__yTopOffSet) - (this.__hvSpacer * 3) - CieStyle.__profil.__fixedHeightBottom;
		//chat typing text
		this.__mvChatOutput.txtTyping._width = this.__panelLastSize.__width - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvChatOutput.txtTyping._y = this.__mvChatOutput.txtInfos._height + this.__mvChatOutput.txtInfos._y + (this.__hvSpacer / 2); 
		//chat input text
		this.__mvChatInput.txtInfos._width = this.__panelLastSize.__width - this.__sendButt.getMovie()._width - (3 * this.__hvSpacer);
		this.__mvChatInput._y = (this.__panelLastSize.__height - CieStyle.__profil.__fixedHeightBottom);
		//the scroll
		this.__mvChatOutput.mvDescriptionScroll._x = this.__mvChatOutput.txtInfos._width;
		this.__mvChatOutput.mvDescriptionScroll.setSize(CieStyle.__profil.__scrollWidth, this.__mvChatOutput.txtInfos._height);
		//suare
		this.__panelTop.clear();
		new CieCornerSquare(this.__panelTop, this.__hvSpacer, CieStyle.__profil.__yTopOffSet, (this.__mvChatOutput.txtInfos._width + CieStyle.__profil.__scrollWidth), this.__mvChatOutput.txtInfos._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		new CieCornerSquare(this.__panelTop, this.__hvSpacer, this.__mvChatInput._y, (this.__mvChatInput.txtInfos._width), this.__mvChatInput.txtInfos._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xF7F7F7, 100], ['ext', 0.5, 0x999999, 100]);
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
			cSockManager.socketSend("USERMSG:" + this.__fromRoomNameWithoutPrefix + "," + this.__arrUserInfos['nopub'] + "," + escape(str));
			//on update la derniere action de l'usager
			cFunc.updateLastUserAction();
			//on update le message du client
			this.updateMessage(str, BC.__user.__nopub);
			}
		};
		
	/**************************************************************************************************************/	
	
	private function Blocked(Void):Void{
		//text
		cFunc.blockUser(this.__arrUserInfos, this);
		};
		
	/**************************************************************************************************************/	
	
	private function Kicked(Void):Void{
		//text
		if(cFunc.checkIfModeratorOfThisRoom(this.__fromRoomNameWithoutPrefix)){
			cFunc.kickUser(this.__arrUserInfos, this.__fromRoomNameWithoutPrefix);
		}else{
			new CieTextMessages('MB_OK', gLang[181] + '<b>' + this.__fromRoomNameWithoutPrefix + '</b>', gLang[109]);
			}
		};	
		
	/**************************************************************************************************************/	
	
	private function changeBlockedButtText(str:String):Void{
		this.__blockButt.setNewText(str);
		};	
		
	/**************************************************************************************************************/	
	
	public function updateMessage(msg:String, noPub:String):Void{
		
		//text
		if(noPub != BC.__user.__nopub){
			var arrUsers = cFunc.getUserDB(); //get users DB
			var userName = arrUsers[noPub]['pseudo'];
			this.__msgCount++;
			this.__mvChatOutput.txtInfos.htmlText += '<font size="-5">\n</font>' + '<b>' + userName + '</b>' + ": "  + msg;
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
		
	/**************************************************************************************************************/	
	public function updateTyping(msg:String):Void{
		//text
		this.__mvChatOutput.txtTyping.htmlText = msg;
		};

	/*************************************************************************************************************************************************/
	//promotion of a moderator
	public function Promoted(Void):Void{
		//minor check to see if moderator
		if(cFunc.checkIfModeratorOfThisRoom(this.__fromRoomNameWithoutPrefix)){
			//check si le pseudo correspond au nom de la room c'est un chat public appatenant a un membre privilege
			//donc pas d'option de promotion
			if(this.__fromRoomNameWithoutPrefix == BC.__user.__pseudo){
				new CieTextMessages('MB_OK', gLang[198] + '<b>' + this.__fromRoomNameWithoutPrefix + '</b> ' + gLang[199], gLang[109]);
			}else{
				cFunc.promoteUser([this.__arrUserInfos['nopub']], [this.__arrUserInfos['pseudo']], this.__fromRoomNameWithoutPrefix, true);
				}
		}else{
			new CieTextMessages('MB_OK', gLang[181] + '<b>' + this.__fromRoomNameWithoutPrefix + '</b>', gLang[109]);
			}
		};
		
	/**************************************************************************************************************/	
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieUserIrc{
		return this;
		};

	public function Destroy(Void):Void{
		//unregistered resize event
		this.__oPanelTop.__class.removeRegisteredObject(this);
		//the button
		this.__promoteButt.removeButton();	
		this.__blockButt.removeButton();
		this.__closeButt.removeButton();
		this.__sendButt.removeButton();
		this.__killButt.removeButton();
		//remove inpouts and outputs
		this.__mvChatInput.removeMovieClip();
		this.__mvChatOutput.removeMovieClip();
		//elete ref and arrays
		delete this.__oPanelTop;
		delete this.__panelTop;
		delete this.__arrInfos;
		//delete the tab by name
		this.__tabManager.removeTabByName(this.__tabName);
		//delete the tabManager ref
		delete this.__tabManager;
		//tell CIeFunc that the room is closed
		cFunc.closePrivateRoom(this.__tabName);
		//delete moi
		delete this;
		};

		
	}	