/*

section login

*/

import control.CiePanel;
import control.CieButton;
import control.CieOptionBox;
import control.CieTextLine;
import manager.CieToolsManager;
import messages.CieOptionMessages;

dynamic class chat.CieFms{

	static private var __className = "CieFms";
	static private var __instance:CieFms;
	private var __type:String;
	private var __intervalTmp:Number;
	private var __hvSpacer:Number;
	private var __hBox:Number;
	private var __h:Number;
	private var __selectBoxWidth:Number;
	private var __selectBoxHeight:Number;
	private var __mvPanel:MovieClip;
	private var __w:Number;
	
	
	//NEW
	private var __cToolManager:Array;
	private var __panelClassTop:Array;		
	private var __panelClassBottom:Array;
	private var __panelClassCam:Object;
	private var __panelClass:Object;
	private var __mvChatAll:MovieClip;
	private var __mvChatInput:MovieClip;
	private var __sendButton:CieButton;
	private var __camOptionBox:CieOptionBox;
	private var __connectCamButton:CieButton;
	private var __cActionMessages:CieOptionMessages;
	private var __bOtherHasConnectedHisCamera:Boolean;
	
	private var __bPanelCamIsOpened:Boolean;
	
	private var __bOptionBoxIsOpened:Boolean;
	
	private var __keyListener:Object;
	
	private function CieFms(Void){
		this.__bOptionBoxIsOpened = false;
		this.__bOtherHasConnectedHisCamera = false;
		this.__bPanelCamIsOpened = false;
		this.__hBox = 17;
		this.__hvSpacer = CieStyle.__profil.__hvSpacer;
		//ref to the toolmanager class
		this.__cToolManager = new Array();
		//ref to the pannel class
		this.__panelClassTop = new Array();		
		this.__panelClassBottom = new Array();
		};
		
	static public function getInstance(Void):CieFms{
		if(__instance == undefined) {
			__instance = new CieFms();
			}
		return __instance;
		};	
		
	public function reset(Void):Void{
		//
		};	
		
	/*************************************************************************************************************************************************/	
	
	public function openChat(ctype:String):Void{
	
		//ctype is FMS or UNI
		
		//load the node	
		cContent.openTab(['fms', 'chat']);
		
		//get the panel class
		this.__panelClassTop[ctype] = cContent.getPanelClass(['fms','_tl','chat','_tl']);
		this.__panelClassBottom[ctype] = cContent.getPanelClass(['fms','_tl','chat','_bl']);
		
		//put the profil info
		this.__panelClass = cContent.getPanelClass(['fms','_tl']);
		var mvPanelProfil:MovieClip = this.__panelClass.getPanelContent();
		mvPanelProfil.txtPseudo.htmlText = gLang[0] + BC.__user.__pseudo + gLang[1] + BC.__user.__age + gLang[2]; 
		mvPanelProfil.txtSexe.htmlText = unescape(BC.__user.__sexe) + ", " + unescape(BC.__user.__etatcivil) + ", " + unescape(BC.__user.__orientation) ; 
		//mvPanelProfil.txtVille.htmlText = unescape(BC.__user.__ville) + ", " + unescape(BC.__user.__region) + ", " + unescape(BC.__user.__pays) ; 	
		mvPanelProfil.txtVille.htmlText = unescape(BC.__user.__location); 	
		mvPanelProfil.txtSlogan.htmlText = "\"" + unescape(BC.__user.__titre) + "\"";
		
		//the photo
		if(BC.__user.__photo != undefined){
			mvPanelProfil.mvPhoto.mvPicture.loadMovie(unescape(BC.__user.__photo));
		}else{
			mvPanelProfil.mvPhoto.mvSexeLoader.gotoAndStop('_' + BC.__user.__photosexe);
			}

		//option of array of camera and button asking if he wants to activate his webcam
		if(ctype == 'FMS'){
			//get the panel class
			this.__panelClassCam = cContent.getPanelClass(['fms','_tl','chat','_tr']);
			//clear the loader
			this.__panelClassCam.setContent('mvContent');
			this.__bPanelCamIsOpened = false;
			//change the backgound color for text to white
			this.__panelClassCam.setBgColor(0xffffff);
			//the container ref
			var mvPanel = this.__panelClassCam.getPanelContent();
			//arr of cameras
			var arrCam:Array = Camera.names;
			//depends on how many camera we got
			if(arrCam.length > 0){
				//he hava a cam lets show him the array if more then one and the button of connecting
				//empty clip for options
				var mc:MovieClip = mvPanel.createEmptyMovieClip('CAMS', mvPanel.getNextHighestDepth());
				//the question
				var txtLine:CieTextLine = new CieTextLine(mc, 0, 0, 0, 17, 'textfield', gLang[3], 'dynamic',[true,false,false], false, false, false, false);	
				//pos
				mc._x = this.__hvSpacer;	
				mc._y = this.__hvSpacer;
				//rebuild array with index for option box
				var arrCamTmp = new Array();
				var cpt:Number = 1;
				for(var o in arrCam){
					arrCamTmp[cpt++] = new Array(o, arrCam[o]);
					}
				//the options cam
				this.__camOptionBox = new CieOptionBox(mc, arrCamTmp, 'group');
				//default value
				this.__camOptionBox.setSelectionValue(1);
				//instance of the the button for connection
				this.__connectCamButton = new CieButton(mvPanel, gLang[4], 150, 30, this.__hvSpacer, (this.__hvSpacer * 2) + mc._height);
				this.__connectCamButton.getMovie().__supclass = this;
				this.__connectCamButton.getMovie().onRelease = function(Void):Void{
					this.__supclass.connectCamera(true);
		 			};
			}else{
				//he has no camera tell he need one if he wants to video chat
				//the user message holder
				var mvNoCam = mvPanel.attachMovie('mvNoCameraTexte', 'NOCAM_TEXTE', mvPanel.getNextHighestDepth());
				//pos
				mvNoCam._x = this.__hvSpacer;
				mvNoCam._y = this.__hvSpacer;
				//the size of the panel
				var objSize:Object = this.__panelClassCam.getPanelSize();
				mvNoCam.txtInfos._height = objSize.__height - (2 * this.__hvSpacer);
				mvNoCam.txtInfos._width = objSize.__width;
				mvNoCam.txtInfos.htmlText = gLang[5];
				//resize event for the panel 
				mvNoCam.__super = this;
				mvNoCam.resize = function(w:Number, h:Number):Void{
					this.txtInfos._height = h - (2 * this.__hvSpacer);
					this.txtInfos._width = w;
					};
				this.__panelClassCam.registerObject(mvNoCam);
				}
			};
		
		//bottom navigation
		if(this.__cToolManager[ctype] == undefined){
			this.showBottomNavigation(ctype);
			}
		};
		
		
	/*************************************************************************************************************************************************/
	
	//user disconnected his caemra
	public function disconnectCamera(ctype:String):Void{
		if(ctype == 'FMS'){
			//change flag
			this.__bOtherHasConnectedHisCamera = false;
			//clear the loader
			this.__panelClassCam.setContent('mvContent');
			this.__bPanelCamIsOpened = false;
			//change the backgound color for text to white
			this.__panelClassCam.setBgColor(0xffffff);
			//the container ref
			var mvPanel = this.__panelClassCam.getPanelContent();
			//arr of cameras
			var arrCam:Array = Camera.names;
			//depends on how many camera we got
			if(arrCam.length > 0){
				//he hava a cam lets show him the array if more then one and the button of connecting
				//empty clip for options
				var mc:MovieClip = mvPanel.createEmptyMovieClip('CAMS', mvPanel.getNextHighestDepth());
				//the question
				var txtLine:CieTextLine = new CieTextLine(mc, 0, 0, 0, 17, 'textfield', gLang[3], 'dynamic',[true,false,false], false, false, false, false);	
				//pos
				mc._x = this.__hvSpacer;	
				mc._y = this.__hvSpacer;
				//rebuild array with index for option box
				var arrCamTmp = new Array();
				var cpt:Number = 1;
				for(var o in arrCam){
					arrCamTmp[cpt++] = new Array(o, arrCam[o]);
					}
				//the options cam
				this.__camOptionBox = new CieOptionBox(mc, arrCamTmp, 'group');
				//default value
				this.__camOptionBox.setSelectionValue(1);
				//instance of the the button for connection
				this.__connectCamButton = new CieButton(mvPanel, gLang[4], 150, 30, this.__hvSpacer, (this.__hvSpacer * 2) + mc._height);
				this.__connectCamButton.getMovie().__supclass = this;
				this.__connectCamButton.getMovie().onRelease = function(Void):Void{
					this.__supclass.connectCamera(true);
		 			};
			}else{
				//he has no camera tell he need one if he wants to video chat
				//the user message holder
				var mvNoCam = mvPanel.attachMovie('mvNoCameraTexte', 'NOCAM_TEXTE', mvPanel.getNextHighestDepth());
				//pos
				mvNoCam._x = this.__hvSpacer;
				mvNoCam._y = this.__hvSpacer;
				//the size of the panel
				var objSize:Object = this.__panelClassCam.getPanelSize();
				mvNoCam.txtInfos._height = objSize.__height - (2 * this.__hvSpacer);
				mvNoCam.txtInfos._width = objSize.__width;
				mvNoCam.txtInfos.htmlText = gLang[5];
				//resize event for the panel 
				mvNoCam.__super = this;
				mvNoCam.resize = function(w:Number, h:Number):Void{
					this.txtInfos._height = h - (2 * this.__hvSpacer);
					this.txtInfos._width = w;
					};
				this.__panelClassCam.registerObject(mvNoCam);
				}
			}	
		};
		
	/*************************************************************************************************************************************************/

	public function connectCamera(bBroadcast:Boolean):Void{
		//tell the other that we have connect a camera
		if(bBroadcast){
			//set the flasg for UniConnectSymbol
			//to true because I was the one who made the choice by clicking on the this.__connectCamButton Button
			BC.__user.__bReceive = true;
			BC.__user.__bPublish = true;
			//alert the other user
			if(!this.__bOtherHasConnectedHisCamera){
				cFunc.cameraIsOn();
				}
			}
		//get the selection values
		var camChoice = this.__camOptionBox.getSelectionValue();
		//contrer la patch de display car 1 a ete additionner plus haut a l'instanciation de CieOptionBox
		//set la var global de camera pour le UniConnectSymbol
		BC.__user.__cameraindex = Number(camChoice) - 1; 
		//path to the panel
		BC.__user.__campath = 'fms,_tl,chat,_tr';
		BC.__user.__pictprofile = 'fms,_tl';
		//change the backgound color to blac for cam
		//this.__panelClassCam.setBgColor(BC.__panel.__bgColorCamera); 
		this.__panelClassCam.setBgColor(CieStyle.__panel.__bgColorCamera); 
		//load the movie into the panel
		this.__panelClassCam.setContent('UniConnectSymbol');
		this.__bPanelCamIsOpened = true;
		};
	

	/*************************************************************************************************************************************************/
	public function otherCameraIsOff(Void):Void{
		//clear the holder var
		this.__bOtherHasConnectedHisCamera = false;
		this.__bOptionBoxIsOpened = false;
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = null;
		}
	
	/*************************************************************************************************************************************************/

	public function otherCameraIsOn(Void):Void{
		//popup des options s'offrant a l'usager
		//flag for the connectCamera when the other has ask not to ask in return on connection of camera
		this.__bOtherHasConnectedHisCamera = true;
		//arr of cameras
		var arrCam:Array = Camera.names;
		var bCam:Boolean = false;
		//depends on how many camera we got
		if(arrCam.length > 0){
			//at leat one camera
			bCam = true;
			var arrStatus = [
								[0, gLang[6]],
								[1, gLang[7]],
								[2, gLang[8]]
							];
		}else{
			//no camera
			var arrStatus = [
								[0, gLang[9]],
								[1, gLang[10]]
							];
			}
		if(!this.__bPanelCamIsOpened && !this.__bOptionBoxIsOpened){
			this.__cActionMessages = new CieOptionMessages(BC.__user.__pseudo + gLang[11], arrStatus, gLang[12]);
			this.__cActionMessages.setSelectionValue(0);
			this.__cActionMessages.setCallBackFunction(this.cbCameraIsOnChoice, {__class: this, __arrState: arrStatus, __bCam:bCam});
			this.__bOptionBoxIsOpened = true;
			}
		};
	
	/*************************************************************************************************************************************************/
	
	public function cbCameraIsOnChoice(cbObject:Object):Void{
		if(cbObject.__ok == true){
			state = cbObject.__arrState[cbObject.__class.__cActionMessages.getSelectedChoice()][0];
			if(cbObject.__bCam){ //has a camera 
				if(state == '2'){ //doesn't want to see
					BC.__user.__bReceive = false;
					BC.__user.__bPublish = false;
				}else if(state == '1'){ //want to receive but not publish
					//set the flasg for UniConnectSymbol
					BC.__user.__bReceive = true;
					BC.__user.__bPublish = false;
				}else{// wants to receive and publish
					//set the flasg for UniConnectSymbol
					BC.__user.__bReceive = true;
					BC.__user.__bPublish = true;
					}
			}else{ //has no camera
				if(state == '1'){ //doesn't want to see
					BC.__user.__bReceive = false;
					BC.__user.__bPublish = false;
				}else{ //want to receive but not publish
					//set the flasg for UniConnectSymbol
					BC.__user.__bReceive = true;
					BC.__user.__bPublish = false;		
					}
				}
			//check the flags to instanciate or not	
			if(BC.__user.__bReceive || BC.__user.__bPublish){
				//one or the other we need to instanciate the connection 
				cbObject.__class.connectCamera(false);
				}
			}
		//clear the holder var
		cbObject.__class.__bOptionBoxIsOpened = false;
		cbObject.__class.__cActionMessages = null;
		};	
		
	/*************************************************************************************************************************************************/

	public function closeChat(ctype:String):Void{
		//fermer les fenetre d'opption si il y en a
		this.__cActionMessages.closeWindow();
		//enleve la fonctionnalite du send button
		this.__sendButton.getMovie().onRelease = function(Void):Void{};
		//listener key
		this.loginKeyListener(false);
		//clear la case texte d'envoi
		this.__mvChatInput.txtInfos.text = '';
		//close de la connection de la camera si il y a
		if(ctype == 'FMS'){
			//clen les webcam
			this.__panelClassCam.getPanelContent().disconnect();
			this.__panelClassCam.setContent('mvContent');
			this.__panelClassCam.setBgColor(0xffffff);
			}
		};
		
	/*************************************************************************************************************************************************/

	public function resizeTopPanelContent(w:Number, h:Number):Void{
		this.__mvChatAll.txtInfos._height = h - (2 * this.__hvSpacer) - this.__hBox;
		this.__mvChatAll.txtInfos._width = w - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		//the scroll
		this.__mvChatAll.mvDescriptionScroll._x = this.__mvChatAll.txtInfos._width;
		this.__mvChatAll.mvDescriptionScroll.setSize(CieStyle.__profil.__scrollWidth, h - (2 * this.__hvSpacer) - this.__hBox);
		//pos y the typing box
		this.__mvChatAll.txtTyping._y = this.__mvChatAll.txtInfos._height + this.__mvChatAll.txtInfos._y + (this.__hvSpacer/2); 
		this.__mvChatAll.txtTyping._width = w;
		};
		
	/*************************************************************************************************************************************************/

	public function resizeBottomPanelContent(w:Number, h:Number):Void{
		//resize
		var buttW:Number = 80;
		this.__mvChatInput.txtInfos._height = h - (2 * this.__hvSpacer);
		this.__mvChatInput.txtInfos._width = w - buttW - (2 * this.__hvSpacer);
		this.__sendButton.redraw((w - this.__hvSpacer - buttW), this.__hvSpacer);
		};
		
	/*************************************************************************************************************************************************/
	
	public function showBottomNavigation(ctype:String):Void{
		
		//TOP
		//the panel content holding the chat of all
		var mvPanel:MovieClip = this.__panelClassTop[ctype].getPanelContent();
		//the size of the panel
		var objSize:Object = this.__panelClassTop[ctype].getPanelSize();
		//the text box and what goes with it		
		this.__mvChatAll = mvPanel.attachMovie('mvChatTexte', 'CHAT_TEXTE_ALL', mvPanel.getNextHighestDepth());
		//pos
		this.__mvChatAll._x = this.__hvSpacer;
		this.__mvChatAll._y = this.__hvSpacer;
		//size
		//this.__mvChatAll.txtInfos._height = objSize.__height - (2 * this.__hvSpacer);
		this.__mvChatAll.txtInfos._height = objSize.__height - (2 * this.__hvSpacer) - this.__hBox;
		this.__mvChatAll.txtInfos._width = objSize.__width - CieStyle.__profil.__scrollWidth - (this.__hvSpacer * 2);
		this.__mvChatAll.txtInfos.text = '';
		//pos y the typing box
		this.__mvChatAll.txtTyping._y = this.__mvChatAll.txtInfos._height + this.__mvChatAll.txtInfos._y + (this.__hvSpacer/2); 
		this.__mvChatAll.txtTyping._width = objSize.__width;
		this.__mvChatAll.txtTyping.htmlText = gLang[13] + BC.__user.__pseudo + gLang[14];
		//the scroll
		this.__mvChatAll.mvDescriptionScroll._x = this.__mvChatAll.txtInfos._width;

		//resize event for the top panel 
		this.__mvChatAll.__super = this;
		this.__mvChatAll.resize = function(w:Number, h:Number):Void{
			this.__super.resizeTopPanelContent(w, h);
			};
		this.__panelClassTop[ctype].registerObject(this.__mvChatAll);
		
		
		//BOTTOM
		//the panel hosldin the inptu text
		var mvPanel:MovieClip = this.__panelClassBottom[ctype].getPanelContent();
		//the size of the panel
		var objSize:Object = this.__panelClassBottom[ctype].getPanelSize();
		//put the input text
		this.__mvChatInput = mvPanel.attachMovie('mvChatTexteInput', 'CHAT_TEXTE_INPUT', mvPanel.getNextHighestDepth());
		//pos
		this.__mvChatInput._x = this.__hvSpacer;
		this.__mvChatInput._y = this.__hvSpacer;
		//size
		var buttW:Number = 80;
		this.__mvChatInput.txtInfos._height = objSize.__height - (2 * this.__hvSpacer);
		this.__mvChatInput.txtInfos._width = objSize.__width - buttW - (2 * this.__hvSpacer);
		this.__mvChatInput.txtInfos.text = '';
		
		//instance of the the button
		this.__sendButton = new CieButton(mvPanel, gLang[15], buttW, objSize.__height - (2 * this.__hvSpacer), (objSize.__width - this.__hvSpacer - buttW), this.__hvSpacer);
		this.__sendButton.getMovie().onRelease = function(Void):Void{
			cFunc.sendMessage();
 			};
			
		//key pressed enter	
		this.loginKeyListener(true);	
		
		//resize if ctype=UNI if FMS left side his fixed
		if(ctype == 'UNI'){
			//register for resize event
			this.__mvChatInput.__super = this;
			this.__mvChatInput.resize = function(w:Number, h:Number):Void{
				this.__super.resizeBottomPanelContent(w, h);
				};
			this.__panelClassBottom[ctype].registerObject(this.__mvChatInput);
		}else{
			//register for resize event
			this.__mvChatInput.__super = this;
			this.__mvChatInput.resize = function(w:Number, h:Number):Void{
				this.__super.resizeBottomPanelContent(w, h);
				};
			this.__panelClassBottom[ctype].registerObject(this.__mvChatInput);
			}
				
		//register the chat object to CieFunction so it will know where to put the texte and where to retrieve it
		cFunc.registerChatObject(this.__mvChatInput, this.__mvChatAll);
		
		};	

	/*************************************************************************************************************************************************/
	
	public function loginKeyListener(bEnable:Boolean):Void{
		if(bEnable){
			this.__keyListener = new Object();
			this.__keyListener.onKeyUp = function(){
					if(Key.getCode() == Key.ENTER){
						cFunc.sendMessage();
						}
					};
			Key.addListener(this.__keyListener);
		}else{
			Key.removeListener(this.__keyListener);
			delete this.__keyListener;
			}
		};	
	
	/*************************************************************************************************************************************************/	
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieFms{
		return this;
		};
	}	