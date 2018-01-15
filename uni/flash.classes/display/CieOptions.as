/*

section Bottin->carnet et Bottin->blocked

*/

import control.CieTextLine;
import control.CieCheckBox;
import control.CieOptionBox;
import control.CieButton;
import messages.CieTextMessages;
import messages.CieActionMessages;

import manager.CieToolsManager;
import control.CieTools;

dynamic class display.CieOptions{

	static private var __className = 'CieOptions';
	static private var __instance:CieOptions;
	private var __hvSpacer:Number;
	
	private var __panelClassTop:Array;
	private var __registeredForResizeEvent:Object;
	private var __btnW:Number;
	private var __btnH:Number;
	
	private var __chboxPreference:Array;
	private var __chboxAlert:Array;
	
	private var __optBoxLangue:CieOptionBox;
	
	private var __cActionMessages:Object;
	
	private var __arrLangChoices:Array;
	
	private var __strLastLangSelected:String;
	
	private function CieOptions(Void){
		//
		this.__hvSpacer = 10;
		this.__btnW = 150;
		this.__btnH = 30;
		//ref to the pannel class
		this.__panelClassTop = new Array();		
		this.__registeredForResizeEvent = new Object();
		this.__chboxPreference = new Array();
		this.__chboxAlert = new Array();
		this.__arrLangChoices = [['fr_CA', gLang[300]], ['en_US', gLang[299]], ['de_DE', gLang[301]]];
		this.__arrColorChoices = [['0.', 'orange'], ['1.', 'blue'], ['_.', 'gray']];
		this.__strLastLangSelected = BC.__user.__lang;
		this.__strLastColorSelected = BC.__user.__styleColor;
		};
		
	/*************************************************************************************************************************************************/	
	
	static public function getInstance(Void):CieOptions{
		if(__instance == undefined) {
			__instance = new CieOptions();
			}
		return __instance;
		};	
		
		
	/*************************************************************************************************************************************************/
	
	public function openSection(ctype:String):Void{
		cContent.openTab(['options', ctype]);
		};
	
	/*************************************************************************************************************************************************/	
		
	public function changeNode(Void):Void{
		//reloads the node
		cContent.openTab(['options']);
		};
	
	/*************************************************************************************************************************************************/
	public function refreshSection(ctype:String):Void{

		this.__panelClassTop[ctype] = cContent.getPanelClass(['options','_tl', ctype,'_tl']);
		
		this.__panelClassTop[ctype].setContent('mvContent');
		var mvPanel = this.__panelClassTop[ctype].getPanelContent();
		
		//register for the resize event
		this.__registeredForResizeEvent[ctype] = new Object();
		this.__registeredForResizeEvent[ctype].__super = this;
		this.__registeredForResizeEvent[ctype].__panel = mvPanel;
		this.__registeredForResizeEvent[ctype].__textboxes = new Array();
		this.__registeredForResizeEvent[ctype].__checkboxes = new Array();
		this.__registeredForResizeEvent[ctype].resize = function(w:Number, h:Number):Void{		
			this.__super.redrawMultipleCheckBoxAndOptionBoxAndTextBoxAndButton(mvPanel, this.__textboxes, w);		
			};
		this.__panelClassTop[ctype].registerObject(this.__registeredForResizeEvent[ctype]);
		
		var txtMsg:String = '';

		if (ctype == 'preferences'){
			txtMsg = gLang[212];
			//chek boxes
			this.__chboxPreference['autologin'] = new CieCheckBox(mvPanel, [gLang[217], BC.__user.__autologin]);
			this.__chboxPreference['memorizedpsw'] = new CieCheckBox(mvPanel, [gLang[218], BC.__user.__memorizedpsw]);
			this.__chboxPreference['startup'] = new CieCheckBox(mvPanel, [gLang[219], BC.__user.__startup]);
			this.__chboxPreference['autoupdate'] = new CieCheckBox(mvPanel, [gLang[220], BC.__user.__autoupdate]);
			this.__chboxPreference['showbubble'] = new CieCheckBox(mvPanel, [gLang[327], BC.__user.__showbubble]);
			
			//title for option boxes
			var mvTitleOptLang = mvPanel.createEmptyMovieClip('mvTitleOptLang', mvPanel.getNextHighestDepth());
			mvTitleOptLang.__isOptionBoxTitle = true;
			var textline:CieTextLine = new CieTextLine(mvTitleOptLang, this.__hvSpacer, 0, 0, 250, 'textfield', gLang[308], 'dynamic',[true,false,false], false, false, false, false);	
			
			//option box for langues
			var mvOpbox = mvPanel.createEmptyMovieClip('mvOpbox', mvPanel.getNextHighestDepth());
			this.__optBoxLangue = new CieOptionBox(mvOpbox, this.__arrLangChoices, 'langues');
			for(var o in this.__arrLangChoices){
				if(this.__arrLangChoices[o][0] == this.__strLastLangSelected){
					this.__optBoxLangue.setSelectionValue(o);
					break;
					}
				}
			
			//title for option boxes colors
			var mvTitleOptColor = mvPanel.createEmptyMovieClip('mvTitleOptColor', mvPanel.getNextHighestDepth());
			mvTitleOptColor.__isOptionBoxTitle = true;
			var textline:CieTextLine = new CieTextLine(mvTitleOptColor, this.__hvSpacer, 0, 0, 250, 'textfield', 'Colors:', 'dynamic',[true,false,false], false, false, false, false);	
						
			//option box for colors
			var mvOpColor = mvPanel.createEmptyMovieClip('mvOpColor', mvPanel.getNextHighestDepth());
			this.__optBoxColor = new CieOptionBox(mvOpColor, this.__arrColorChoices, 'colors');
			for(var o in this.__arrColorChoices){
				if(this.__arrColorChoices[o][0] == this.__strLastColorSelected){
					this.__optBoxColor.setSelectionValue(o);
					break;
					}
				}
	
			
						
			//button
			var btn = new CieButton(mvPanel, gLang[203], this.__btnW, this.__btnH, 0, 0);
			btn.getMovie().__class = this;
			btn.getMovie().onRelease = function(Void):Void{
				//Debug('BUTT_PREFERENCE_PRESSED');
				this.__class.savePreferenceOptions();
				};
			
			//event register button
			this.__registeredForResizeEvent[ctype].__textboxes.push(btn);
			//event register option colors
			this.__registeredForResizeEvent[ctype].__textboxes.push(this.__optBoxColor);
			//event register option angues
			this.__registeredForResizeEvent[ctype].__textboxes.push(mvTitleOptColor);
			//event register option angues
			this.__registeredForResizeEvent[ctype].__textboxes.push(this.__optBoxLangue);
			//event register option angues
			this.__registeredForResizeEvent[ctype].__textboxes.push(mvTitleOptLang);
			//event register chkboxes
			for(var o in this.__chboxPreference){
				this.__registeredForResizeEvent[ctype].__textboxes.push(this.__chboxPreference[o]);
				}
						
		}else if (ctype == 'abonnement'){
			txtMsg = gLang[213];
			var btn = new CieButton(mvPanel, gLang[204], this.__btnW, this.__btnH, 0, 0);
			//actions
			btn.getMovie().onRelease = function(Void):Void{
				var arrUser = new Array();
				arrUser['pseudo'] = BC.__user.__pseudo;
				arrUser['no_publique'] = BC.__user.__nopub;
				cFunc.openSiteRedirectionBox('subscription',arrUser);
				};
			this.__registeredForResizeEvent[ctype].__textboxes.push(btn);
			
		}else if (ctype == 'mes_alertes'){
			txtMsg = gLang[215];
			this.__chboxAlert['newprofil'] = new CieCheckBox(mvPanel, [gLang[205], BC.__alert.__newprofil]);
			this.__chboxAlert['newmsg'] = new CieCheckBox(mvPanel, [gLang[206], BC.__alert.__newmsg]);
			this.__chboxAlert['newconn'] = new CieCheckBox(mvPanel, [gLang[207], BC.__alert.__newconn]);
			this.__chboxAlert['newchat'] = new CieCheckBox(mvPanel, [gLang[208], BC.__alert.__newchat]);
			this.__chboxAlert['newcrit'] = new CieCheckBox(mvPanel, [gLang[209], BC.__alert.__newcrit]);
			var btn = new CieButton(mvPanel, gLang[210], this.__btnW, this.__btnH, 0, 0);
			btn.getMovie().__class = this;
			btn.getMovie().onRelease = function(Void):Void{
				//Debug('BUTT_ALERT_PRESSED');
				this.__class.saveAlertOptions();
				};
			this.__registeredForResizeEvent[ctype].__textboxes.push(btn);
			for(var o in this.__chboxAlert){
				this.__registeredForResizeEvent[ctype].__textboxes.push(this.__chboxAlert[o]);
				}
			
		}else if (ctype == 'mon_profil'){
			/*
			posotionning his inverted because of the revese loop in FOR IN of the redraw on resize event
			*/
			//0 - tool record
			var toolVideo = new CieTools(mvPanel, 'video', '32X32', 'mvIconImage_30');
			toolVideo.setAction('onclick', 'openRecord', []);
			toolVideo.changeColor(CieStyle.__basic.__buttonColor, CieStyle.__basic.__buttonBorderWidth, CieStyle.__basic.__buttonBorderColor, CieStyle.__basic.__buttonEffectColor, CieStyle.__basic.__buttonEffectColorOff);
			//text tool
			mvTexte3 = mvPanel.attachMovie('mvAide', 'TEXTE_OPTION3_' + ctype, mvPanel.getNextHighestDepth());
			mvTexte3.__mvTools = toolVideo;
			mvTexte3.txtInfos.autoSize = 'left';	
			mvTexte3.txtInfos._width = 260;	
			mvTexte3._x = ((this.__hvSpacer * 2) + toolVideo.getIconWidth());
			mvTexte3.txtInfos.htmlText = gLang[443];
					
			//1 - saviez-vous que:
			mvTexte2 = mvPanel.attachMovie('mvAide', 'TEXTE_OPTION2_' + ctype, mvPanel.getNextHighestDepth());
			mvTexte2.txtInfos.autoSize = 'left';	
			mvTexte2._x = this.__hvSpacer;
			mvTexte2.txtInfos.htmlText = gLang[442];
			//little croos icon for eye focus
			var mvIconCross = mvPanel.attachMovie('mvIconImage_31', 'ICON_CROSS_' + ctype, mvPanel.getNextHighestDepth());
			mvIconCross.__mvTextToFollow = mvTexte2;
			mvIconCross._x = this.__hvSpacer;
			
			//2 - separator
			var mvSep:MovieClip = mvPanel.attachMovie('mvHorSeparateur', 'HS', mvPanel.getNextHighestDepth());
			mvSep.__isSeparator = true;
			mvSep._x = this.__hvSpacer;
												
			//3 - button 
			var btnProfil = new CieButton(mvPanel, gLang[211], this.__btnW, this.__btnH, 0, 0);
			btnProfil.getMovie().onRelease = function(Void):Void{
				var arrUser = new Array();
				arrUser['pseudo'] = BC.__user.__pseudo;
				arrUser['no_publique'] = BC.__user.__nopub;
				cFunc.openSiteRedirectionBox('monprofil',arrUser);
				};
						
			//4 - profil (will be registered automatically later)
			txtMsg = gLang[216];
			
			//register elements in reverse ordre of drawing
			this.__registeredForResizeEvent[ctype].__textboxes.push(mvTexte3);
			this.__registeredForResizeEvent[ctype].__textboxes.push(toolVideo);
			this.__registeredForResizeEvent[ctype].__textboxes.push(mvIconCross);
			this.__registeredForResizeEvent[ctype].__textboxes.push(mvTexte2);
			this.__registeredForResizeEvent[ctype].__textboxes.push(mvSep);
			this.__registeredForResizeEvent[ctype].__textboxes.push(btnProfil);
			
			}
			
		//construire texte
		mvTexte = mvPanel.attachMovie('mvAide', 'TEXTE_OPTION_' + ctype, mvPanel.getNextHighestDepth());
		//register
		this.__registeredForResizeEvent[ctype].__textboxes.push(mvTexte);
		
		//positionning
		mvTexte.txtInfos.autoSize = 'left';	
		mvTexte._x = this.__hvSpacer;
		mvTexte._y = this.__hvSpacer;
		//disable scroll
		//mvTexte.mvDescriptionScroll.visible = false;
		//mvTexte.mvDescriptionScroll._visible = false;
		//title
		mvTexte.txtInfos.htmlText = txtMsg;
		};	

	
	/***SAVE TO REG*******************************************************************************************************************************************/
	
	public function saveAlertOptions(Void):Void{
		for(var o in this.__chboxAlert){
			var chkVal = this.__chboxAlert[o].getSelectionValue();
			//le array a les mem noms que les cle de registre
			//save to global var then to registry
			if(chkVal == '1'){
				BC.__alert['__' + o] = true;
			}else{
				BC.__alert['__' + o] = false;
				}
			cRegistry.setKey('alert_' + o, BC.__alert['__' + o]);
			}
		new CieTextMessages('MB_OK', gLang[221], gLang[222]);	
		};
	
	/**********************************************************************************************************************************************/
	public function savePreferenceOptions(Void):Void{
		var bRestartAppz:Boolean = false;
		for(var o in this.__chboxPreference){
			var chkVal = this.__chboxPreference[o].getSelectionValue();
			//specail because not the same key
			if(o == 'startup'){ 
				if(chkVal == '1'){
					cRegistry.runOnStart(mdm.Application.path + 'UNI2.exe');
				}else{
					cRegistry.removeRunOnStart();
					}
				}	
			//save to global var then to registry
			if(chkVal == '1'){
				BC.__user['__' + o] = true;
			}else{
				BC.__user['__' + o] = false;
				}
			cRegistry.setKey(o, BC.__user['__' + o]);
			}
			
		//OK maintenant les option de langue	
		var strOpt:String = this.__optBoxLangue.getSelectionValue();	
		if(this.__arrLangChoices[Number(strOpt)][0] != this.__strLastLangSelected){ //not same language has before
			//save it to the registry
			this.__strLastLangSelected = this.__arrLangChoices[Number(strOpt)][0];
			if(this.__strLastLangSelected != ''){
				cRegistry.setKey('language', this.__strLastLangSelected);
				}
			bRestartAppz = true;
			}
			
		//OK maintenant les option de couleurs
		var strOpt:String = this.__optBoxColor.getSelectionValue();	
		if(this.__arrLangColor[Number(strOpt)][0] != this.__strLastColorSelected){ //not same color
			//save it to the registry
			this.__strLastColorSelected = this.__arrColorChoices[Number(strOpt)][0];
			if(this.__strLastColorSelected != ''){
				cRegistry.setKey('color_template', this.__strLastColorSelected);
				}
			bRestartAppz = true;
			}	
	
		if(bRestartAppz){
			//then restart appz question
			this.__cActionMessages = new CieTextMessages('MB_OK', gLang[307], gLang[306]);
			this.__cActionMessages.setCallBackFunction(cFunc.restartApplication, cFunc);
		}else{
			new CieTextMessages('MB_OK', gLang[223], gLang[224]);	
			}
		};	
	
	/*************************************************************************************************************************************************/
	
	//resize the text box the scrool and the background
	private function redrawMultipleCheckBoxAndOptionBoxAndTextBoxAndButton(mvPanel:MovieClip, arrTexte:Array, w:Number):Void{
		//clear the drawing
		mvPanel.clear();
		var iPanelScrollWidth = 16;
		//Ypos
		var tmpHeight:Number = this.__hvSpacer;
		//loop trough all textBoxes
		for(var o in arrTexte){
			if(arrTexte[o].getClassName() == 'CieCheckBox'){
				arrTexte[o].redraw(this.__hvSpacer,  tmpHeight);
				tmpHeight += Number(arrTexte[o].getCheckBoxMovie()._height + this.__hvSpacer); 
				
			}else if(arrTexte[o].getClassName() == 'CieOptionBox'){
				arrTexte[o].redraw(this.__hvSpacer,  tmpHeight);
				tmpHeight += Number(arrTexte[o].getMovie()._height + (this.__hvSpacer * 2)); 
				
			}else if(arrTexte[o].getClassName() == 'CieButton'){
				//its a button then draw is different
				arrTexte[o].redraw(this.__hvSpacer,  tmpHeight);
				tmpHeight += Number(arrTexte[o].getMovie()._height + (this.__hvSpacer * 3)); 
				
			}else{
				arrTexte[o]._y = tmpHeight;
				//if its the attached movie
				if(arrTexte[o].txtInfos != undefined){
					if(arrTexte[o].__mvTools != undefined){ //text that follow a tools like camera/mic
						arrTexte[o]._y = arrTexte[o].__mvTools.getIcon()._y;
					}else{
						//place the width to follow the Panel
						arrTexte[o].txtInfos._width = Number(Math.floor(w - (this.__hvSpacer  + iPanelScrollWidth)));
						//new Height			
						tmpHeight += Number(arrTexte[o]._height + (this.__hvSpacer * 2));
						}
				
				}else{ //must be a textLine then so no need to width redraw and not the same hvSpacing
					if(arrTexte[o].getClassName() == 'CieTools'){ //butt from mon profil
						arrTexte[o].redraw(this.__hvSpacer, tmpHeight);	
						tmpHeight += Number(arrTexte[o].getMovie()._height + (this.__hvSpacer * 2)); 
					}else{
						if(arrTexte[o].__isOptionBoxTitle){ //sic'est un titre de option box le spacer est trop grand
							tmpHeight += Number(arrTexte[o]._height) ;
						}else if(arrTexte[o].__isSeparator){ //si c'est un separateur pour le profil
							tmpHeight += Number(arrTexte[o]._height) + this.__hvSpacer;
						}else if(arrTexte[o].__mvTextToFollow != undefined){ //for the cross in the "mon profil"
							arrTexte[o]._y = arrTexte[o].__mvTextToFollow._y + 3;
						}else{
							tmpHeight += Number(arrTexte[o]._height + this.__hvSpacer);
							}
						}	
					}
				}
			}
		};
		
	/*************************************************************************************************************************************************/
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieOptions{
		return this;
		};
	*/	
	}	