/*



*/

//in order of use
import control.CieButton;
import graphic.CieCornerSquare;


dynamic class irc.CieSpecialIrc{

	static private var __className = "CieSpecialIrc";
		
	private var __tabName:String;
	private var __arrUserInfos:Array;
	private var __tabManager:Object;
	private var __oPanelTop:Object;
	private var __panelTop:MovieClip;
	private var __closeButt:Object;
	//private var __deleteButt:Object;
	private var __panelLastSize:Object;
	private var __hvSpacer:Number;
	private var __mvChatOutput:MovieClip;
	
	private var __msgCount:Number = 0;
	
	private var __maxMsgHolder:Number;
	
	public function CieSpecialIrc(tabName:String, tabManager:Object, arrUserInfos:Array, bNoFocus:Boolean){
		//
		this.__tabName = tabName;
		this.__tabManager = tabManager;
		this.__arrUserInfos = arrUserInfos;
		this.__hvSpacer = CieStyle.__profil.__hvSpacer;
		
		//for msg
		this.__maxMsgHolder = 100000; //environ 500 par ligne de message avec le formatage html 100000/500 = 200 messages
		
		//
		this.__yTopOffSet = 66 + this.__hvSpacer;
		
		//xml to create the tab
		if(!bNoFocus){
			strXml = '<PT n="' + this.__tabName + '" model="un" title="' + this.__arrUserInfos['name'] + '" closebutt="false">';
			strXml += '<P n="_tl" content="mvProfilRoom" bgcolor="0xffffff" scroll="false" effect="false"></P>';
			strXml += '</PT>';
		}else{
			strXml = '<PT n="' + this.__tabName + '" model="un" title="' + this.__arrUserInfos['name'] + '" closebutt="false" nofocus="true">';
			strXml += '<P n="_tl" content="mvProfilRoom" bgcolor="0xffffff" scroll="false" effect="false"></P>';
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
		
		//register the class to the top panel so it can recevie a resize event
		this.__oPanelTop.__class.registerObject(this);
				
		};
		
		
	/**************************************************************************************************************/	
	
	//fenetre du bas pour le input text and button
	private function drawForOutput(Void):Void{	
		
		var buttHeight = 35;
		
		//la derniere communication
		var dDate = new Date(Number(this.__arrUserInfos['lastmsgtimestamp']));
		var dMinute = dDate.getMinutes();
		if(dMinute < 10){
			dMinute = '0' + dMinute;
			}
		var dSecond = dDate.getSeconds();
		if(dSecond < 10){
			dSecond = '0' + dSecond;
			}	
		var lastCommunicationDate = dDate.getHours() + ':' + dMinute + ':' + dSecond;
		
		//fill the profil
		this.__panelTop.txtPseudo.htmlText = '<b>' +  this.__arrUserInfos['name'] + '</b>'; 
		this.__panelTop.txtSlogan.htmlText = '';
		
		//photo
		this.__panelTop.mvPhoto.mvPicture.loadMovie(BC.__server.__roomthumbs + '/twatch.jpg');
		
		//draw the close butt in the top right corner of top panel
		this.__closeButt = new CieButton(this.__panelTop, gLang[169], 75, buttHeight, (this.__panelLastSize.__width - this.__hvSpacer - 75), this.__hvSpacer);
		this.__closeButt.getMovie().__class = this;
		this.__closeButt.getMovie().onRelease = function(Void):Void{
			this.__class.Destroy();
			};
			
		//draw the delete butt in the top right corner of top panel
		/*
		this.__deleteButt = new CieButton(this.__panelTop, gLang[170], 75, buttHeight, (this.__panelLastSize.__width - this.__hvSpacer - 75), (this.__closeButt.getMovie()._y + this.__closeButt.getMovie()._height + (this.__hvSpacer/2)));
		this.__deleteButt.getMovie().__class = this;
		this.__deleteButt.getMovie().onRelease = function(Void):Void{
			cFunc.deleteSpecial(this.__class.__tabName);
			};
		*/		
			
		//the box output text
		this.__mvChatOutput = this.__panelTop.attachMovie('mvChatTexte', 'CHAT_TEXTE_ALL', this.__panelTop.getNextHighestDepth());
		//pos
		this.__mvChatOutput._x = this.__hvSpacer;
		this.__mvChatOutput._y = this.__yTopOffSet;
		//size
		this.__mvChatOutput.txtInfos._height = (this.__panelLastSize.__height - this.__yTopOffSet ) - (this.__hvSpacer * 3);
		this.__mvChatOutput.txtInfos._width = this.__panelLastSize.__width - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvChatOutput.txtInfos.text = gLang[171];
		//pos y the typing box
		this.__mvChatOutput.txtTyping._y = this.__mvChatOutput.txtInfos._height + this.__mvChatOutput.txtInfos._y + (this.__hvSpacer / 2); 
		this.__mvChatOutput.txtTyping._width = this.__panelLastSize.__width - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvChatOutput.txtTyping.htmlText = gLang[172] + lastCommunicationDate;
		//the scroll
		this.__mvChatOutput.mvDescriptionScroll._x = this.__mvChatOutput.txtInfos._width;
		//the square around
		new CieCornerSquare(this.__panelTop, this.__hvSpacer, this.__yTopOffSet, (this.__mvChatOutput.txtInfos._width + CieStyle.__profil.__scrollWidth), this.__mvChatOutput.txtInfos._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		};	
		
	/**************************************************************************************************************/	
	
	//called when registered the the container panel (top because the bottom will have a fixed height but same width has toppanel)
	public function resize(w:Number, h:Number):Void{
		//replace the values
		this.__panelLastSize.__width = w;
		this.__panelLastSize.__height = h;
		//reposition the closeButt
		this.__closeButt.redraw((this.__panelLastSize.__width - this.__closeButt.getMovie()._width - this.__hvSpacer), this.__hvSpacer);
		//reposition the deleteButt
		/*
		this.__deleteButt.redraw((this.__panelLastSize.__width - this.__closeButt.getMovie()._width - this.__hvSpacer), (this.__closeButt.getMovie()._y + this.__closeButt.getMovie()._height + (this.__hvSpacer/2)));
		*/
		//chat output text
		this.__mvChatOutput.txtInfos._width = this.__panelLastSize.__width - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvChatOutput.txtInfos._height = (this.__panelLastSize.__height - this.__yTopOffSet) - (this.__hvSpacer * 3);
		//chat typing text
		this.__mvChatOutput.txtTyping._width = this.__panelLastSize.__width - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvChatOutput.txtTyping._y = this.__mvChatOutput.txtInfos._height + this.__mvChatOutput.txtInfos._y + (this.__hvSpacer / 2); 
		//the scroll
		this.__mvChatOutput.mvDescriptionScroll._x = this.__mvChatOutput.txtInfos._width;
		this.__mvChatOutput.mvDescriptionScroll.setSize(CieStyle.__profil.__scrollWidth, this.__mvChatOutput.txtInfos._height);
		//suare
		this.__panelTop.clear();
		new CieCornerSquare(this.__panelTop, this.__hvSpacer, this.__yTopOffSet, (this.__mvChatOutput.txtInfos._width + CieStyle.__profil.__scrollWidth), this.__mvChatOutput.txtInfos._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
		};
	
		
	/**************************************************************************************************************/	
	
	public function updateMessage(msg:String, noPub:String):Void{
		
		//text
		
		var arrUsers = cFunc.getUserDB(); //get users DB
		var userName = arrUsers[noPub]['pseudo'];
		var userTextColor = arrUsers[noPub]['textcolor'];
		this.__msgCount++;
		this.__mvChatOutput.txtInfos.htmlText += '<font size="-5">\n</font>' +  '<font color="' + userTextColor + '"><b>' + userName + '</b>' + ": "  + msg;
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
		this.__mvChatOutput.txtTyping.htmlText = gLang[168] +  lastSentMsgDate;
		
		//if this tab dont have focus give it
		if(!this.__tabManager.getTabFocusByName(this.__tabName)){
			this.__tabManager.giveTabAttention(this.__tabName, true);
			};
				
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
	
	
	/**************************************************************************************************************/	
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieSpecialIrc{
		return this;
		};

	public function Destroy(Void):Void{
		//unregistered resize event
		this.__oPanelTop.__class.removeRegisteredObject(this);
		//the button
		//this.__deleteButt.removeButton();
		this.__closeButt.removeButton();
		//remove inpouts and outputs
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
		cFunc.closeSpecialRoom(this.__tabName);
		//delete the oboject
		delete this;
		};
	
	}	