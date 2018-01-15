/*

le profil detaille builde avec du xml avec changement the node dans COntentManager

*/

import manager.CieToolsManager;
import control.CieTools;
//import manager.CieRequestManager;
import control.CieCheckBox;
import control.CieButton;
import control.CieFileBrowse;
import graphic.CieCornerSquare;
//import graphic.CieSquare;
import media.CieSoundPlayer;
import system.CieDate;
import utils.CieThread;
import control.CieBubble;

import messages.CieTextMessages;

import flash.filters.GlowFilter;
import flash.filters.BlurFilter;

import control.CieTextLine;


dynamic class display.CieDetailedProfil{

	static private var __className = 'CieDetailedProfil';
	
	private var __mv:MovieClip;
	//private var __xmlNode:XMLNode;
	private var __strTab:String;
	private var __strPanel:String;
	private var __strBaseTab:String;
	private var __arrMiniProfil:Array;
	private var __keyName:String;
	private var __registeredForResizeEvent:Array;	
	private var __hvSpacer:Number;
	private var __albumHolder:Array;
	private var __albumMv:MovieClip;
	private var __albumRights:Array;
	private var __cToolsManagers:Array;
	private var __cToolsManagersReply:Array;
	//private var __chatToolsManager:CieToolsManager;
	//private var __blockedToolsManager:CieToolsManager;
	//private var __carnetToolsManager:CieToolsManager;
	private var __albumButtonChoice:CieButton;
	private var __iPanelScrollWidth:Number;
	
	//for detailed menu
	private var __cThreadAnimWindow:CieThread;
	private var __cThreadAnimWindowTimerOff:CieThread;
	private var __cThreadAnimArrow:CieThread;
	private var __cThreadAnimSubMenu:CieThread;
	private var __detailedMenuIsOn:Boolean;
	private var __detailedMenuMouseIsOver:Boolean;
	private var __chatMenuText:Array;
	
	//MODIFIED BY AARIZO
	
	private var __pays:String = new String();
	private var __region:String = new String();
	private var __ville:String = new String();
	private var __mvTools:MovieClip;
	
	
	//END MODIFIED BY AARIZO
		
	public function CieDetailedProfil(arrPanel:Array, arrTab:Array, xmlNode:XMLNode, arrBaseTab:Array){
		this.__detailedMenuIsOn = false;
		this.__detailedMenuMouseIsOver = false;
		this.__cThreadAnimWindow = null;
		this.__cThreadAnimWindowTimerOff = null;
		this.__cThreadAnimArrow = null;
		this.__cThreadAnimSubMenu = null;
		
		this.__cToolsManagers = new Array();
		this.__cToolsManagersReply = new Array();
		this.__albumHolder = new Array();
		this.__registeredForResizeEvent = new Array();
		this.__strPanel = arrPanel.toString();
		this.__strTab = arrTab.toString();
		this.__strBaseTab = arrBaseTab.toString();
		this.__arrMiniProfil = new Array();
		this.__hvSpacer = CieStyle.__profil.__hvSpacer;
		this.__iPanelScrollWidth =  CieStyle.__profil.__scrollWidth;
		
		this.__chatMenuText = new Array();
	
		//this.__xmlNode = xmlNode;
		//this.makeFromXmlNode();
		for(var i = 0; i < xmlNode.childNodes.length; i++){
			this.__arrMiniProfil[xmlNode.childNodes[i].attributes.n] = xmlNode.childNodes[i].firstChild.nodeValue;
			}
		delete xmlNode;	
				
		this.loadProfileTemplate();
		};
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieDetailedProfil{
		return this;
		};
	*/	
	/******************************************************************************************************************************************/	
	
	private function showVocalMsg(Void):Void{
		//Debug("DETAILED_P ----------------> showVocalMsg()");
		var arrPanelPath:Array = this.__strPanel.split(',');
		arrPanelPath.push('messages','_tl','vocal','_tl');
		//the panel object
		var oPanel:Object = cFunc.getPanelObject(arrPanelPath);
		oPanel.__class.setContent('mvContent');	
		var mvPanel:MovieClip = oPanel.__class.getPanelContent();
	
		//register for the resize event
		if(typeof(this.__registeredForResizeEvent['vocal']) == 'object'){
			oPanel.__class.removeRegisteredObject(this.__registeredForResizeEvent['vocal']);
			this.__registeredForResizeEvent['vocal'] = undefined;
			delete this.__registeredForResizeEvent['vocal'];
			}
	
		//register for the resize event
		this.__registeredForResizeEvent['vocal'] = new Object();
		this.__registeredForResizeEvent['vocal'].__super = this;
		this.__registeredForResizeEvent['vocal'].__panel = mvPanel;
		this.__registeredForResizeEvent['vocal'].__textboxes = new Array();
		//resize event
		this.__registeredForResizeEvent['vocal'].resize = function(w:Number, h:Number):Void{
			this.__super.redrawMultipleTextBoxAndButton(this.__panel, this.__textboxes, w);
			};
		oPanel.__class.registerObject(this.__registeredForResizeEvent['vocal']);
			
		var mvTexte:MovieClip = mvPanel.attachMovie('mvDescriptionTexte', 'ALERT_EXPRESS_2', mvPanel.getNextHighestDepth());
		//positionning
		mvTexte.txtInfos.autoSize = 'left';
		mvTexte._x = this.__hvSpacer;
		mvTexte.txtInfos.htmlText = gLang[126] + this.__arrMiniProfil['pseudo'] + gLang[127];
		//instance of the the button
		var cButt = new CieButton(mvPanel, gLang[128], 70, 28, 0, 0);
		cButt.getMovie().__user = this.__arrMiniProfil;
		cButt.getMovie().__class = this;
		cButt.getMovie().__mvpanel = mvPanel;
		cButt.getMovie().__width = w;
		cButt.getMovie().onRelease = function(Void):Void{
			if(this.__user['pseudo'].indexOf('~') == -1){
				cFunc.openSiteRedirectionBox('vocalenvoi', this.__user);
			}else{
				new CieTextMessages('MB_OK', gLang[333], gLang[16]);
				}
 			};
		//register the butt
		this.__registeredForResizeEvent['vocal'].__textboxes.push(cButt);	
		//register the text
		this.__registeredForResizeEvent['vocal'].__textboxes.push(mvTexte);		
			
		//check if this user as sent an express message to me
		var strSql:String = 'SELECT members.msg_vocal FROM members WHERE members.no_publique = ' + this.__arrMiniProfil['no_publique'] + ' AND members.msg_vocal = "1";';
		var arrRows:Array = cDbManager.selectDB(strSql);
		if(arrRows.length != 0){
			//show that this user has sent n express
			var mvTexte1:MovieClip = mvPanel.attachMovie('mvDescriptionTexte', 'ALERT_EXPRESS_1', mvPanel.getNextHighestDepth());
			//positionning
			mvTexte1.txtInfos.autoSize = 'left';
			mvTexte1._x = this.__hvSpacer;
			mvTexte1.txtInfos.htmlText = gLang[129] + this.__arrMiniProfil['pseudo'] + gLang[130];
			//instance of the the button
			var cButt1 = new CieButton(mvPanel, gLang[131], 70, 28, 0, 0);
			cButt1.getMovie().__user = this.__arrMiniProfil;
			cButt1.getMovie().__class = this;
			cButt1.getMovie().__mvpanel = mvPanel;
			cButt1.getMovie().__width = w;
			cButt1.getMovie().onRelease = function(Void):Void{
				cFunc.openSiteRedirectionBox('vocalecoute', this.__user);
				};
			//register the butt
			this.__registeredForResizeEvent['vocal'].__textboxes.push(cButt1);
			//register the text
			this.__registeredForResizeEvent['vocal'].__textboxes.push(mvTexte1);
			}
		//redraw	
		this.redrawMultipleTextBoxAndButton(mvPanel, this.__registeredForResizeEvent['vocal'].__textboxes, oPanel.__class.getPanelSize().__width);		
		};
		
	/******************************************************************************************************************************************/
	
	private function loadExpressMsg(Void):Void{
		//Debug("DETAILED_P ----------------> loadExpressMsg()");
		//build request
		var arrD = new Array();
		arrD['methode'] = 'express';
		arrD['action'] = 'check';
		arrD['arguments'] = this.__arrMiniProfil['no_publique'];
		//add the request
		cReqManager.addRequest(arrD, this.cbExpressMsg, {__caller:this});	
		//set the flag in the local BD
		cDbManager.queryDB("UPDATE members SET members.msg_express = '2' WHERE members.no_publique = " + this.__arrMiniProfil['no_publique'] + " AND members.msg_express = '1';");
		};
		
	public function cbExpressMsg(prop, oldVal:Number, newVal:Number, obj:Object){
		//Debug('GOT IT!!! express IN: ' + obj.__req.getSec()/1000);
		obj.__super.__caller.parseExpressMsg(obj.__req.getXml().firstChild);
		cReqManager.removeRequest(obj.__req.getID());
		};
	
	public function parseExpressMsg(xmlNode:XMLNode):Void{
		var bShow:Boolean = true;
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.attributes.n == 'error'){
				bShow = false;
				}
			}
		//si ou/pas erreur	
		this.showExpressMsg(bShow);
		};	
	
	private function showExpressMsg(bShow:Boolean):Void{
		var arrPanelPath:Array = this.__strPanel.split(',');
		arrPanelPath.push('messages','_tl','express','_tl');
		//the panel object
		var oPanel:Object = cFunc.getPanelObject(arrPanelPath);
		oPanel.__class.setContent('mvContent');	
		var mvPanel:MovieClip = oPanel.__class.getPanelContent();
	
		//register for the resize event
		if(typeof(this.__registeredForResizeEvent['express']) == 'object'){
			oPanel.__class.removeRegisteredObject(this.__registeredForResizeEvent['express']);
			this.__registeredForResizeEvent['express'] = undefined;
			delete this.__registeredForResizeEvent['express'];
			}
		
		//register for the resize event
		this.__registeredForResizeEvent['express'] = new Object();
		this.__registeredForResizeEvent['express'].__super = this;
		this.__registeredForResizeEvent['express'].__panel = mvPanel;
		this.__registeredForResizeEvent['express'].__textboxes = new Array();
		//resize event
		this.__registeredForResizeEvent['express'].resize = function(w:Number, h:Number):Void{
			this.__super.redrawMultipleTextBoxAndButton(this.__panel, this.__textboxes, w);
			};
		oPanel.__class.registerObject(this.__registeredForResizeEvent['express']);	
		
		var str1 = '';
		var str2 = '';
		
		//depending if has already sent a message
		if(bShow){
			//instance of the the button
			var cButt = new CieButton(mvPanel, gLang[128], 70, 28, 0, 0);
			cButt.getMovie().__class = this;
			cButt.getMovie().__mvpanel = mvPanel;
			cButt.getMovie().__user = this.__arrMiniProfil;
			cButt.getMovie().onRelease = function(Void):Void{
				if(this.__user['pseudo'].indexOf('~') == -1){
					cFunc.sendExpressMessage(this.__user);
				}else{
					new CieTextMessages('MB_OK', gLang[333], gLang[16]);
					}	
				};
			//register the butt
			this.__registeredForResizeEvent['express'].__textboxes.push(cButt);	
			str1 = gLang[132] + this.__arrMiniProfil['pseudo'] + gLang[133];
		}else{
			str1 = gLang[134] + this.__arrMiniProfil['pseudo'] + gLang[135];
			}
				
		//check if this user as sent an express message to me
		var strSql:String = 'SELECT members.msg_express, members.msg_express_date FROM members WHERE members.no_publique = ' + this.__arrMiniProfil['no_publique'] + ' AND members.msg_express <> "0";';
		var arrRows:Array = cDbManager.selectDB(strSql);
		if(arrRows.length != 0){
			//show that this user has sent n express
			var date = new CieDate(String(arrRows[0][1]), 'd F Y');
			str2 = date.printDate() + ', ' + gLang[136] + this.__arrMiniProfil['pseudo'] + gLang[137];
			}
			
		//build a form
		var mvTexte:MovieClip = mvPanel.attachMovie('mvDescriptionTexte', 'ALERT_EXPRESS_2', mvPanel.getNextHighestDepth());
		//positionning
		mvTexte.txtInfos.autoSize = 'left';
		mvTexte.txtInfos.htmlText = str2 + str1;
		mvTexte._x = this.__hvSpacer;	
		this.__registeredForResizeEvent['express'].__textboxes.push(mvTexte);	
					
		//redraw	
		this.redrawMultipleTextBoxAndButton(mvPanel, this.__registeredForResizeEvent['express'].__textboxes, oPanel.__class.getPanelSize().__width);		
		};
	
	/******************************************************************************************************************************************/	
		
	public function showCourrielMsg(Void):Void{
		var arrPanelPath:Array = this.__strPanel.split(',');
		arrPanelPath.push('messages','_tl','courriel','_tl');
		//the panel object
		var oPanel:Object = cFunc.getPanelObject(arrPanelPath);
		oPanel.__class.setContent('mvContent');	
		var mvPanel:MovieClip = oPanel.__class.getPanelContent();
		
		//register for the resize event
		if(typeof(this.__registeredForResizeEvent['courriel']) == 'object'){
			oPanel.__class.removeRegisteredObject(this.__registeredForResizeEvent['courriel']);
			this.__registeredForResizeEvent['courriel'] = undefined;
			delete this.__registeredForResizeEvent['courriel'];
			}
				
		this.__registeredForResizeEvent['courriel'] = new Object();
		this.__registeredForResizeEvent['courriel'].__super = this;
		this.__registeredForResizeEvent['courriel'].__panel = mvPanel;
		this.__registeredForResizeEvent['courriel'].__textboxes = new Array();
		//resize event
		this.__registeredForResizeEvent['courriel'].resize = function(w:Number, h:Number):Void{
			this.__super.redrawMultipleTextBoxMsgCourriel(this.__panel, this.__textboxes, w);
			};
		oPanel.__class.registerObject(this.__registeredForResizeEvent['courriel']);
		
		//do the DB query to show courrier messages
		var tmpHeight:Number = this.__hvSpacer;
		var buttY:Number = 0;
		var keyCourriel:String = '';
		var strSql:String = "SELECT courrier.no_courrier, courrier.type, courrier.cdate, courrier.msg, courrier.titre, courrier.lu FROM courrier WHERE courrier.no_publique = " + this.__arrMiniProfil['no_publique'] + " ORDER BY courrier.cdate DESC";
		var arrRows:Array = cDbManager.selectDB(strSql);

		if(arrRows.length != 0){
			
			//put a new boxes	
			var mvTexte = mvPanel.attachMovie('mvCourrielTexte', 'COURRIEL_NEW_C', mvPanel.getNextHighestDepth());
			mvTexte.__keyname = 'NEW_C';
			mvTexte.__isMsgOpen = 3;
			this.__registeredForResizeEvent['courriel'].__textboxes.push(mvTexte);
			this.showCourrierReply(mvTexte, oPanel, true, arrRows);
			//end put a new boxes
						
			for(var o in arrRows){
				keyCourriel = String(arrRows[o][0]);
				var mvTexte = mvPanel.attachMovie('mvCourrielTexte', 'COURRIEL_' + keyCourriel, mvPanel.getNextHighestDepth());
				//keyname ref for the tollsManager ref array
				mvTexte.__keyname = keyCourriel;
				mvTexte.__isMsgOpen = 0;
				mvTexte.__markAsRead = arrRows[o][5];
				
				//positionning
				mvTexte._x = this.__hvSpacer;
				mvTexte._y = tmpHeight;
				//DATE	
				//var date = new CieDate(this.__mv, String(arrRows[o][2]), 'd F Y, H:i:s', 'fr');
				var date = new CieDate(String(arrRows[o][2]), 'd F Y, H:i:s');
				
				mvTexte.__txtTitre = this.__arrMiniProfil['pseudo'];
				mvTexte.__type = arrRows[o][1];
				//TEXT
				if(arrRows[o][1] == '1'){//TO: 
					mvTexte.__txtInfos = '<font color="#666666">' +  unescape(arrRows[o][3]) + '</font>';
					mvTexte.__txtDate = '<font color="#999999">' + date.printDate() + '</font>';
					mvTexte.__txtSujet = '<font color="#999999">' + unescape(arrRows[o][4]) + '</font>';
				}else{//FROM:	
					mvTexte.__txtInfos = unescape(arrRows[o][3]);
					mvTexte.__txtDate = date.printDate();
					mvTexte.__txtSujet = unescape(arrRows[o][4]);
					}
				mvTexte.txtDate.htmlText = mvTexte.__txtDate;
				mvTexte.txtSujet.htmlText = mvTexte.__txtSujet;	
		
				//register
				this.__registeredForResizeEvent['courriel'].__textboxes.push(mvTexte);
				
				//BOUTON PLUS
				mvTexte.mvPlus.__sup = this;					
				mvTexte.mvPlus.__mvTexte = mvTexte;																				
				mvTexte.mvPlus.__panel = mvPanel;					
				mvTexte.mvPlus.__arrRows = arrRows;					
				mvTexte.mvPlus.onRelease = function(Void):Void{
					this.__sup.showCourrielDetail(this.__mvTexte, oPanel, '_open', 1, this.__arrRows);						
					};
					
				mvTexte.mvPlus.onRollOver = function(Void):Void{
					this.mvPlusBg.gotoAndStop('_on');
					};
				mvTexte.mvPlus.onRollOut = 
				mvTexte.mvPlus.onDragOut = 
				mvTexte.mvPlus.onReleaseOutside = function(Void):Void{
					this.mvPlusBg.gotoAndStop('_off');
					};	
					
					
				//lu and non-lu
				//Debug('DET_LU: ' + mvTexte.__markAsRead);
				mvTexte.mvMsgLuInstant.gotoAndStop('_' + mvTexte.__markAsRead);		
					
				}
			//draw all boxes at once				
			this.redrawMultipleTextBoxMsgCourriel(mvPanel, this.__registeredForResizeEvent['courriel'].__textboxes, oPanel.__class.getPanelSize().__width);	
			oPanel.__class.redrawForScrollBar();
			//cStage.redraw();
		}else{
			//no message show the reply then
			var mvTexte = mvPanel.attachMovie('mvCourrielTexte', 'COURRIEL_NEW_C', mvPanel.getNextHighestDepth());
			mvTexte.__keyname = 'NEW_C';
			this.__registeredForResizeEvent['courriel'].__textboxes.push(mvTexte);
			this.showCourrierReply(mvTexte, oPanel, true, arrRows);
			}
		}
		
	public function showCourrielDetail(mvTexte:MovieClip, oPanel:Object, section:String, noSection:Number, arrRows:Array):Void{		
		
		//section c'est l'état du message (ouvert, fermé, reply)
		//numero de section contient des valeur 0, 1, 2 .. utile pour la fonction draw	

		this.closeCourrierDetail(false);

		//pour voir le msg en detail
		mvTexte.__isMsgOpen = noSection;
		if(mvTexte.__keyname != 'NEW_C'){
			//frame
			mvTexte.gotoAndStop(section);
			//texte
			mvTexte.receive.txtDate.htmlText = mvTexte.__txtDate;
			mvTexte.receive.txtSujet.htmlText = '<b>' + mvTexte.__txtSujet + '</b>';
			mvTexte.receive.txtInfos.htmlText = mvTexte.__txtInfos;	
			//positionning
			mvTexte.receive.txtInfos.autoSize = 'left';				
			var keyCourriel = mvTexte.__keyname;
			var nextY:Number = (mvTexte.receive.txtInfos._y + mvTexte.receive.txtInfos._height);
			//query
			strSql = "SELECT attachement.cname, attachement.csize FROM attachement WHERE attachement.no_courrier = " + Number(keyCourriel) + " ORDER BY attachement.cname DESC";
			var arrRowsAttach:Array = cDbManager.selectDB(strSql);		
			if(arrRowsAttach.length != 0){
				var mvAttach:MovieClip = mvTexte.attachMovie('mvContent', 'ATTACH', mvTexte.getNextHighestDepth());
				mvAttach._y = nextY;
				var tmpY = this.__hvSpacer;
				for(var o in arrRowsAttach){
					//attache the textBox
					var mvAttachement:MovieClip = mvAttach.attachMovie('mvAttachReply', 'ATTACHEMENTS_' + arrRowsAttach[o][0], mvAttach.getNextHighestDepth());
					//put the filename
					mvAttachement.txtFile.htmlText = unescape(String(arrRowsAttach[o][0]));
					
					//the view button
					//var toolView = new CieTools(mvAttachement, 'view', '16X16', 'mvIconImage_6');
					var toolView = new CieTools(mvAttachement, 'view', '16X16', 'mvIconImage_4');
					toolView.changeColor(CieStyle.__box.__bgColor, 0, CieStyle.__basic.__buttonBorderColor, CieStyle.__box.__bgColor, CieStyle.__box.__bgColor);
					
					toolView.redraw(0, 0);
					//toolView.getIcon().__class = this;
					toolView.getIcon().__filename = arrRowsAttach[o][0];
					toolView.getIcon().__key = keyCourriel
					toolView.getIcon().onRelease = function(Void):Void{
						//download the attach files
						cFunc.getAttach(this.__filename, this.__key);
						};
					//positionning
					mvAttachement._y = tmpY;
					mvAttachement.txtFile._x = (this.__hvSpacer * 2);
					mvAttachement._x = this.__hvSpacer;
					//next position	
					tmpY += mvAttachement._height + 4;
					}
				nextY += mvAttach._height + (this.__hvSpacer * 3);
			}else{
				nextY += (this.__hvSpacer * 3);
				}
				
			//TOOLBAR
			var strXML:String = '';
			strXML += '<UNITOOLBAR>';
			strXML += '<TOOLGROUP n="action" align="left">';
			if(mvTexte.__type != '1'){
				strXML += '<BUTTON n="reply" type="80X28" text="' + gLang[138] + '" style="' + CieStyle.__basic.__buttonColor + ',' + CieStyle.__basic.__buttonBorderWidth + ',' + CieStyle.__basic.__buttonBorderColor + ',' + CieStyle.__basic.__buttonEffectColor + ',' + CieStyle.__basic.__buttonEffectColorOff + ',' + CieStyle.__basic.__buttonFontColor + '"><ACTION n="onrollover" func=""></ACTION></BUTTON>';
				}
			strXML += '<BUTTON n="delete" type="80X28" text="' + gLang[139] + '" style="' + CieStyle.__basic.__buttonColor + ',' + CieStyle.__basic.__buttonBorderWidth + ',' + CieStyle.__basic.__buttonBorderColor + ',' + CieStyle.__basic.__buttonEffectColor + ',' + CieStyle.__basic.__buttonEffectColorOff + ',' + CieStyle.__basic.__buttonFontColor + '"><ACTION n="onrollover" func=""></ACTION></BUTTON>';
			strXML += '</TOOLGROUP>';
			strXML += '</UNITOOLBAR>';
			//toolmanager
			if (this.__cToolsManagers[keyCourrier] != undefined){
				this.__cToolsManagers[keyCourrier].removeAllTools();		
				delete this.__cToolsManagers[keyCourrier];
				}
			this.__cToolsManagers[keyCourriel] = new CieToolsManager(mvTexte, oPanel.__class.getPanelSize().__width, 0, 0, nextY);
			this.__cToolsManagers[keyCourriel].createFromXml(new XML(strXML).firstChild);
			//set the tool actions
				if(mvTexte.__type != '1'){
				this.__cToolsManagers[keyCourriel].getIcon('action', 'reply').__mv = mvTexte;
				this.__cToolsManagers[keyCourriel].getIcon('action', 'reply').__mvPanel = mvPanel;
				this.__cToolsManagers[keyCourriel].getIcon('action', 'reply').__pobj = oPanel;
				this.__cToolsManagers[keyCourriel].getIcon('action', 'reply').__class = this;
				this.__cToolsManagers[keyCourriel].getIcon('action', 'reply').__key = keyCourriel;
				this.__cToolsManagers[keyCourriel].getIcon('action', 'reply').__arrRows = arrRows;			
				this.__cToolsManagers[keyCourriel].getIcon('action', 'reply').onRelease = function(Void):Void{
					if (this.__mv.__isMsgOpen == 1){
						this.__class.showCourrierReply(this.__mv, this.__pobj, false, this.__arrRows);
						}
					};
				}	
			//set the tool actions
			this.__cToolsManagers[keyCourriel].getIcon('action', 'delete').__class = this;
			this.__cToolsManagers[keyCourriel].getIcon('action', 'delete').__key = keyCourriel;
			this.__cToolsManagers[keyCourriel].getIcon('action', 'delete').__user = this.__arrMiniProfil;
			this.__cToolsManagers[keyCourriel].getIcon('action', 'delete').onRelease = function(Void):Void{
				cFunc.removeCourriel(this.__class, this.__user, this.__key);	
				}	

			this.redrawMultipleTextBoxMsgCourriel(oPanel.__class.getPanelContent(), this.__registeredForResizeEvent['courriel'].__textboxes, oPanel.__class.getPanelSize().__width);
			oPanel.__class.redrawForScrollBar();
			//cStage.redraw();
			
			//mark those msg read //build request
			if(mvTexte.__markAsRead != '1'){
				var arrD = new Array();
					arrD['methode'] = 'courrier';
					arrD['action'] = 'markread';
					arrD['arguments'] = keyCourriel;
				//add the request
				cReqManager.addRequest(arrD, this.cbSetToReadState, null);
				mvTexte.__markAsRead = '1';
				//set the flag in the local BD
				cDbManager.queryDB("UPDATE courrier SET lu = '1' WHERE no_courrier = " + keyCourriel + ";");
				}
		}else{
			mvTexte._x = this.__hvSpacer;
			if(mvTexte.__isMsgOpen != 3){
				mvTexte.gotoAndStop('_new');
			}else{
				mvTexte.gotoAndStop('_alone');
				}
			mvTexte.txtMsg.htmlText = gLang[140] + this.__arrMiniProfil['pseudo'] + gLang[141];
			//Selection.setFocus(mvTexte.txtReplyTitre);
			}
		// pour le champs de texte reply
		mvTexte.mvPlus._visible = false;
		
		//close this message
		mvTexte.receive.mvCloseMessage.__class = this;
		mvTexte.receive.mvCloseMessage.onRelease = function(Void):Void{
			this.__class.closeCourrierDetail(true);
			};
		
		};	
		
	public function showCourrierReply(mvTexte:MovieClip, oPanel:Object, bNew:Boolean, arrRows:Array):Void{
		//message courriel en detail
		if(mvTexte.__isMsgOpen == 3){
			this.showCourrielDetail(mvTexte, oPanel, '_reply', 3, arrRows);
		}else{
			this.showCourrielDetail(mvTexte, oPanel, '_reply', 2, arrRows);
			}
		//REPLY
		var keyCourrier:String = mvTexte.__keyname; 
		
		//the copy checkBox
		mvTexte.__chkbox = new CieCheckBox(mvTexte, ['',0]);
		mvTexte.__chkbox.getCheckBoxMovie()._x = 0;
		mvTexte.__chkbox.getCheckBoxMovie()._y = mvTexte.txtCopy._y + 3;
		mvTexte.txtReply.htmlText = '';
		mvTexte.txtReplyTitre.htmlText = '';
		//set the cursor focus
		//Selection.setFocus(mvTexte.txtReplyTitre);
		
		if(mvTexte.__isMsgOpen == 3 || bNew){
			mvTexte.txtMsg.htmlText = gLang[142] + this.__arrMiniProfil['pseudo'] + gLang[143];
		}else{
			mvTexte.txtMsg.htmlText = gLang[297] + this.__arrMiniProfil['pseudo'] + gLang[298];
			}
		
		mvTexte.txtCopy.htmlText = gLang[144];
		
		//pos
		buttY = mvTexte.txtCopy._y + mvTexte.txtCopy._height + (this.__hvSpacer * 2);

		//the attachement
		var cFileBrowse:CieFileBrowse = new CieFileBrowse(mvTexte);
		mvTexte.ATTACH_REPLY._y = buttY;
		mvTexte.ATTACH_REPLY._x += 0;	
		buttY += mvTexte.ATTACH_REPLY._y + (this.__hvSpacer * 2);
		
		//TOOLBAR
		var strXML:String = '';
		strXML += '<UNITOOLBAR>';
		strXML += '<TOOLGROUP n="action" align="left">';
		strXML += '<BUTTON n="send" type="80X28" text="' + gLang[145] + '" style="' + CieStyle.__basic.__buttonColor + ',' + CieStyle.__basic.__buttonBorderWidth + ',' + CieStyle.__basic.__buttonBorderColor + ',' + CieStyle.__basic.__buttonEffectColor + ',' + CieStyle.__basic.__buttonEffectColorOff + ',' + CieStyle.__basic.__buttonFontColor + '"><ACTION n="onrollover" func=""></ACTION></BUTTON>';
		if(!bNew){
			strXML += '<BUTTON n="cancel" type="80X28" text="' + gLang[146] + '" style="' + CieStyle.__basic.__buttonColor + ',' + CieStyle.__basic.__buttonBorderWidth + ',' + CieStyle.__basic.__buttonBorderColor + ',' + CieStyle.__basic.__buttonEffectColor + ',' + CieStyle.__basic.__buttonEffectColorOff + ',' + CieStyle.__basic.__buttonFontColor + '"><ACTION n="onrollover" func=""></ACTION></BUTTON>';
			}
		strXML += '</TOOLGROUP>';
		strXML += '</UNITOOLBAR>';
		//toolmanager
		if (this.__cToolsManagersReply[keyCourrier] != undefined){
			this.__cToolsManagersReply[keyCourrier].removeAllTools();		
			delete this.__cToolsManagersReply[keyCourrier];
			}
		this.__cToolsManagersReply[keyCourrier] = new CieToolsManager(mvTexte, 0, 0, 0, buttY);
		this.__cToolsManagersReply[keyCourrier].createFromXml(new XML(strXML).firstChild);
		//set the tool actions
		this.__cToolsManagersReply[keyCourrier].getIcon('action', 'send').__class = this;
		this.__cToolsManagersReply[keyCourrier].getIcon('action', 'send').__key = keyCourrier;
		this.__cToolsManagersReply[keyCourrier].getIcon('action', 'send').__user =  this.__arrMiniProfil;
		this.__cToolsManagersReply[keyCourrier].getIcon('action', 'send').__sujet = mvTexte.txtReplyTitre;
		this.__cToolsManagersReply[keyCourrier].getIcon('action', 'send').__text = mvTexte.txtReply;
		this.__cToolsManagersReply[keyCourrier].getIcon('action', 'send').__checkbox = mvTexte.__chkbox;
		this.__cToolsManagersReply[keyCourrier].getIcon('action', 'send').__cFileBrowse = cFileBrowse;
		this.__cToolsManagersReply[keyCourrier].getIcon('action', 'send').onRelease = function(Void):Void{
			if(this.__user['pseudo'].indexOf('~') == -1){
				var arrFileBrowse = this.__cFileBrowse.getFileBrowseArray();
				var cmptAttach = this.__cFileBrowse.getComptAttach();
				// check l'état du checkbox
				if (this.__checkbox.getSelectionValue() == 0){
					var bCopy = 0;
				}else{
					var bCopy = 1;
					}
				// check si c'est une réponse à un email
				if (this.__key != 'NEW_C'){
					var bReply = 1;
				}else{
					var bReply = 0;
					}
				//check s'il y a un attachement
				if (cmptAttach > 0){
					var bAttach = 1;
				}else{
					var bAttach = 0;
					}
				cFunc.sendCourriel(this.__class, arrFileBrowse, this.__user, this.__sujet.text, this.__text.text, bCopy, bReply, bAttach);
			}else{
				new CieTextMessages('MB_OK', gLang[333], gLang[16]);
				}
			};
		//set the tool actions
		if(!bNew){
			this.__cToolsManagersReply[keyCourrier].getIcon('action', 'cancel').__mvreply = mvTexte;
			this.__cToolsManagersReply[keyCourrier].getIcon('action', 'cancel').__pobj = oPanel;
			this.__cToolsManagersReply[keyCourrier].getIcon('action', 'cancel').__class = this;
			this.__cToolsManagersReply[keyCourrier].getIcon('action', 'cancel').__arrRows = arrRows;			
			this.__cToolsManagersReply[keyCourrier].getIcon('action', 'cancel').__key = keyCourrier;
			this.__cToolsManagersReply[keyCourrier].getIcon('action', 'cancel').onRelease = function(Void):Void{
				this.__class.showCourrielDetail(this.__mvreply, this.__pobj, '_open', 1, this.__arrRows);
				}
			}	
		//set the callback to redraw when atrtaching new files
		cFileBrowse.setRedrawFunction(this, oPanel);
		//redraw all boxes at once		
		this.redrawMultipleTextBoxMsgCourriel(oPanel.__class.getPanelContent(), this.__registeredForResizeEvent['courriel'].__textboxes, oPanel.__class.getPanelSize().__width);
		
		//redraw all boxes at once
		if(mvTexte.__isMsgOpen != 3){
			cStage.redraw();
			}
		};
	
	public function closeCourrierDetail(bRedraw:Boolean):Void{
		for(var o in this.__registeredForResizeEvent['courriel'].__textboxes){
			if(this.__registeredForResizeEvent['courriel'].__textboxes[o].__isMsgOpen != 3){
				this.__registeredForResizeEvent['courriel'].__textboxes[o].__isMsgOpen = 0;
				this.__registeredForResizeEvent['courriel'].__textboxes[o].gotoAndStop('_close');
				this.__registeredForResizeEvent['courriel'].__textboxes[o].mvPlus._visible = true;
				}
			
			//lu and non-lu
			this.__registeredForResizeEvent['courriel'].__textboxes[o].mvMsgLuInstant.gotoAndStop('_' + this.__registeredForResizeEvent['courriel'].__textboxes[o].__markAsRead);	
						
			//removes attache clip
			if(this.__registeredForResizeEvent['courriel'].__textboxes[o].ATTACH != undefined && this.__registeredForResizeEvent['courriel'].__textboxes[o].__isMsgOpen != 3){
				this.__registeredForResizeEvent['courriel'].__textboxes[o].ATTACH.removeMovieClip();
				delete this.__registeredForResizeEvent['courriel'].__textboxes[o].ATTACH;
				}
			//effacer le bouton attach file	
			if(this.__registeredForResizeEvent['courriel'].__textboxes[o].ATTACH_REPLY != undefined && this.__registeredForResizeEvent['courriel'].__textboxes[o].__isMsgOpen != 3){
				this.__registeredForResizeEvent['courriel'].__textboxes[o].ATTACH_REPLY.removeMovieClip();
				delete this.__registeredForResizeEvent['courriel'].__textboxes[o].ATTACH_REPLY;
				}
			//effacer les tools
			if (this.__cToolsManagersReply[this.__registeredForResizeEvent['courriel'].__textboxes[o].__keyname] != undefined  && this.__registeredForResizeEvent['courriel'].__textboxes[o].__isMsgOpen != 3 ){
				this.__cToolsManagersReply[this.__registeredForResizeEvent['courriel'].__textboxes[o].__keyname].removeAllTools();		
				delete this.__cToolsManagersReply[this.__registeredForResizeEvent['courriel'].__textboxes[o].__keyname];
				}
				
			if (this.__cToolsManagers[this.__registeredForResizeEvent['courriel'].__textboxes[o].__keyname] != undefined){
				this.__cToolsManagers[this.__registeredForResizeEvent['courriel'].__textboxes[o].__keyname].removeAllTools();		
				delete this.__cToolsManagers[this.__registeredForResizeEvent['courriel'].__textboxes[o].__keyname];
				}
				
			//effacer le checkbox
			if(this.__registeredForResizeEvent['courriel'].__textboxes[o].__isMsgOpen != 3){
				this.__registeredForResizeEvent['courriel'].__textboxes[o].__chkbox.getCheckBoxMovie().removeMovieClip();
				delete this.__registeredForResizeEvent['courriel'].__textboxes[o].__chkbox;
				}
			//text
			this.__registeredForResizeEvent['courriel'].__textboxes[o].txtDate.htmlText = this.__registeredForResizeEvent['courriel'].__textboxes[o].__txtDate;
			this.__registeredForResizeEvent['courriel'].__textboxes[o].txtSujet.htmlText = this.__registeredForResizeEvent['courriel'].__textboxes[o].__txtSujet;
			}
		
		if(bRedraw){
			cStage.redraw();
			}
		};	
		
	/******************************************************************************************************************************************/
	
	public function showInstantMsg(Void):Void{
		
		var arrPanelPath:Array = this.__strPanel.split(',');
		arrPanelPath.push('messages','_tl','instant','_tl');
		//the panel object
		var oPanel:Object = cFunc.getPanelObject(arrPanelPath);
		oPanel.__class.setContent('mvContent');	
		var mvPanel:MovieClip = oPanel.__class.getPanelContent();
		
		//unregistered
		if(typeof(this.__registeredForResizeEvent['instant']) == 'object'){
			oPanel.__class.removeRegisteredObject(this.__registeredForResizeEvent['instant']);
			this.__registeredForResizeEvent['instant'] = undefined;
			delete this.__registeredForResizeEvent['instant'];
			}
				
		//register for the resize event
		this.__registeredForResizeEvent['instant'] = new Object();
		this.__registeredForResizeEvent['instant'].__super = this;
		this.__registeredForResizeEvent['instant'].__panel = mvPanel;	
		this.__registeredForResizeEvent['instant'].__textboxes = new Array();
		//resize event
		this.__registeredForResizeEvent['instant'].resize = function(w:Number, h:Number):Void{
			this.__super.redrawMultipleTextBoxMsg(this.__panel, this.__textboxes, w);
			};
		oPanel.__class.registerObject(this.__registeredForResizeEvent['instant']);
		
		//do the DB query to show instanbt messages
		var buttY:Number = 0;
		var keyInstant:String = '';
		
		var strSql:String = "SELECT instant.no_instant, instant.type, instant.cdate, instant.msg, instant.lu FROM instant WHERE no_publique = " + this.__arrMiniProfil['no_publique'] + " ORDER BY instant.cdate DESC";
		var arrRows:Array = cDbManager.selectDB(strSql);
		if(arrRows.length != 0){
			
			//put a new boxes	
			var mvTexte = mvPanel.attachMovie('mvInstantTexteClose', 'INSTANT_NEW_I', mvPanel.getNextHighestDepth());
			mvTexte.__keyname = 'NEW_I';
			mvTexte.__isMsgOpen = 3;
			this.__registeredForResizeEvent['instant'].__textboxes.push(mvTexte);
			this.showInstantReply(mvTexte, oPanel, true, arrRows);
			//end put a new boxes
						
			for(var o in arrRows){	
				keyInstant = String(arrRows[o][0]);
				//attach
				var mvTexte = mvPanel.attachMovie('mvInstantTexteClose', 'INSTANT_' + keyInstant, mvPanel.getNextHighestDepth());
				mvTexte.__keyname = keyInstant;
				mvTexte.__isMsgOpen = 0;
				mvTexte.__markAsRead = arrRows[o][4];
				
				//positionning
				mvTexte._x = this.__hvSpacer;				
				
				//DATE	
				//var date = new CieDate(this.__mv, String(arrRows[o][2]), 'd:m:Y', 'fr');
				//var date = new CieDate(this.__mv, String(arrRows[o][2]), 'd F Y, H:i:s', 'fr');
				var date = new CieDate(String(arrRows[o][2]), 'd F Y, H:i:s');
				// add text
				mvTexte.__type = arrRows[o][1];
				//TO: 
				if(arrRows[o][1] == '1'){
					mvTexte.__txtDate = '<font color="#999999">' + date.printDate() + '</font>';
					mvTexte.__txtInfos = '<font color="#999999">' + unescape(arrRows[o][3]) + '</font>';
				//FROM:	
				}else{
					mvTexte.__txtDate = date.printDate();
					mvTexte.__txtInfos = unescape(arrRows[o][3]);		
					}
					
				//Debug('INST: ' + arrRows[o][0] + ' | ' + arrRows[o][1] + ' | ' + arrRows[o][2] + ' | ' + arrRows[o][3]);	
					
				mvTexte.txtDate.htmlText = mvTexte.__txtDate;
				mvTexte.txtInfos.htmlText = mvTexte.__txtInfos;
								
				//register
				this.__registeredForResizeEvent['instant'].__textboxes.push(mvTexte);
				
				mvTexte.mvPlus.__sup = this;					
				mvTexte.mvPlus.__mvTexte = mvTexte;																				
				mvTexte.mvPlus.__panel = mvPanel;					
				mvTexte.mvPlus.__arrRows = arrRows;							
				mvTexte.mvPlus.onRelease = function(Void):Void{
					this.__sup.showInstantDetail(this.__mvTexte, oPanel, '_open', 1, this.__arrRows);							
					};
					
				mvTexte.mvPlus.onRollOver = function(Void):Void{
					this.mvPlusBg.gotoAndStop('_on');
					};
				mvTexte.mvPlus.onRollOut = 
				mvTexte.mvPlus.onDragOut = 
				mvTexte.mvPlus.onReleaseOutside = function(Void):Void{
					this.mvPlusBg.gotoAndStop('_off');
					};		
					
				//lu and non-lu
				//Debug('DET_LU: ' + mvTexte.__markAsRead);
				mvTexte.mvMsgLuInstant.gotoAndStop('_' + mvTexte.__markAsRead);		
					
				}
				
			//draw all boxes at once				
			this.redrawMultipleTextBoxMsg(mvPanel, this.__registeredForResizeEvent['instant'].__textboxes, oPanel.__class.getPanelSize().__width);	
			oPanel.__class.redrawForScrollBar();
			//cStage.redraw();
			
		}else{
			//no message whoe the reply then
			var mvTexte = mvPanel.attachMovie('mvInstantTexteClose', 'INSTANT_NEW_I', mvPanel.getNextHighestDepth());
			mvTexte.__keyname = 'NEW_I';
			this.__registeredForResizeEvent['instant'].__textboxes.push(mvTexte);
			this.showInstantReply(mvTexte, oPanel, true, arrRows);
			}
		}
		
	public function showInstantDetail(mvTexte:MovieClip, oPanel:Object, section:String, noSection:Number, arrRows:Array):Void{
		this.closeInstantDetail(false);
		//pour voir le msg en detail
		mvTexte.__isMsgOpen = noSection;	
		if(mvTexte.__keyname != 'NEW_I'){			
			mvTexte.gotoAndStop(section);
			for(var o in arrRows){
				//TO: 
				mvTexte.txtDate.htmlText = mvTexte.__txtDate;
				mvTexte.txtInfos.htmlText = mvTexte.__txtInfos;
				}
			mvTexte.txtInfos.autoSize = 'left';	
			buttY = (mvTexte.txtInfos._y + mvTexte.txtInfos._height + (this.__hvSpacer * 3));
			
			//TOOLBAR
			var strXML:String = '';
			strXML += '<UNITOOLBAR>';
			strXML += '<TOOLGROUP n="action" align="left">';
			
			if(mvTexte.__type != '1'){
				strXML += '<BUTTON n="reply" type="80X28" text="' + gLang[147] + '" style="' + CieStyle.__basic.__buttonColor + ',' + CieStyle.__basic.__buttonBorderWidth + ',' + CieStyle.__basic.__buttonBorderColor + ',' + CieStyle.__basic.__buttonEffectColor + ',' + CieStyle.__basic.__buttonEffectColorOff + ',' + CieStyle.__basic.__buttonFontColor + '"><ACTION n="onrollover" func=""></ACTION></BUTTON>';
				}
			
			strXML += '<BUTTON n="delete" type="80X28" text="' + gLang[148] + '" style="' + CieStyle.__basic.__buttonColor + ',' + CieStyle.__basic.__buttonBorderWidth + ',' + CieStyle.__basic.__buttonBorderColor + ',' + CieStyle.__basic.__buttonEffectColor + ',' + CieStyle.__basic.__buttonEffectColorOff + ',' + CieStyle.__basic.__buttonFontColor + '"><ACTION n="onrollover" func=""></ACTION></BUTTON>';
			strXML += '</TOOLGROUP>';
			strXML += '</UNITOOLBAR>';
			//toolmanager
			this.__cToolsManagers[mvTexte.__keyname] = new CieToolsManager(mvTexte, oPanel.__class.getPanelSize().__width, 0, 0, buttY);							
			this.__cToolsManagers[mvTexte.__keyname].createFromXml(new XML(strXML).firstChild);
			
			//set the tool actions
			if(mvTexte.__type != '1'){
				this.__cToolsManagers[mvTexte.__keyname].getIcon('action', 'reply').__mv = mvTexte;
				this.__cToolsManagers[mvTexte.__keyname].getIcon('action', 'reply').__pobj = oPanel;
				this.__cToolsManagers[mvTexte.__keyname].getIcon('action', 'reply').__class = this;
				this.__cToolsManagers[mvTexte.__keyname].getIcon('action', 'reply').__key = mvTexte.__keyname;
				this.__cToolsManagers[mvTexte.__keyname].getIcon('action', 'reply').__arrRows = arrRows;			
				this.__cToolsManagers[mvTexte.__keyname].getIcon('action', 'reply').onRelease = function(Void):Void{
					if (this.__mv.__isMsgOpen == 1){
						this.__class.showInstantReply(this.__mv, this.__pobj, false, arrRows);
						}
					}
				}	
			
			//set the tool actions
			this.__cToolsManagers[mvTexte.__keyname].getIcon('action', 'delete').__class = this;
			this.__cToolsManagers[mvTexte.__keyname].getIcon('action', 'delete').__key = mvTexte.__keyname;
			this.__cToolsManagers[mvTexte.__keyname].getIcon('action', 'delete').__user = this.__arrMiniProfil;
			this.__cToolsManagers[mvTexte.__keyname].getIcon('action', 'delete').onRelease = function(Void):Void{
				cFunc.removeInstantMsg(this.__class, this.__user, this.__key);				
				}
			//redraw
			this.redrawMultipleTextBoxMsg(oPanel.__class.getPanelContent(), this.__registeredForResizeEvent['instant'].__textboxes, oPanel.__class.getPanelSize().__width);		
			oPanel.__class.redrawForScrollBar();
			//cStage.redraw();
			
			//mark those msg read //build request
			if(mvTexte.__markAsRead != '1'){
				var arrD = new Array();
					arrD['methode'] = 'instant';
					arrD['action'] = 'read';
					arrD['arguments'] = mvTexte.__keyname;
				//add the request
				cReqManager.addRequest(arrD, this.cbSetToReadState, null);
				mvTexte.__markAsRead = '1';
				//set the flag in the local BD
				cDbManager.queryDB("UPDATE instant SET lu = '1' WHERE no_instant = " + mvTexte.__keyname + ";");
				}
		}else{
			mvTexte._x = this.__hvSpacer;
			if(mvTexte.__isMsgOpen != 3){
				mvTexte.gotoAndStop('_new');
			}else{
				mvTexte.gotoAndStop('_alone');
				}
			//Selection.setFocus(mvTexte.txtReply);	
			}
		
		mvTexte.mvPlus._visible = false;		
		//close this message
		mvTexte.mvCloseMessage.__class = this;
		mvTexte.mvCloseMessage.onRelease = function(Void):Void{
			this.__class.closeInstantDetail(true);
			};
		};
	
	public function closeInstantDetail(bRedraw:Boolean):Void{
		for(var o in this.__registeredForResizeEvent['instant'].__textboxes){
			if(this.__registeredForResizeEvent['instant'].__textboxes[o].__isMsgOpen != 3){
				this.__registeredForResizeEvent['instant'].__textboxes[o].__isMsgOpen = 0;
				this.__registeredForResizeEvent['instant'].__textboxes[o].gotoAndStop('_close');
				this.__registeredForResizeEvent['instant'].__textboxes[o].mvPlus._visible = true;
				}
			//lu and non-lu
			this.__registeredForResizeEvent['instant'].__textboxes[o].mvMsgLuInstant.gotoAndStop('_' + this.__registeredForResizeEvent['instant'].__textboxes[o].__markAsRead);	
									
			var keyInstant = this.__registeredForResizeEvent['instant'].__textboxes[o].__keyname;
			
			if(this.__cToolsManagersReply[keyInstant] != undefined && this.__registeredForResizeEvent['instant'].__textboxes[o].__isMsgOpen != 3){
				this.__cToolsManagersReply[keyInstant].removeAllTools();		
				delete this.__cToolsManagersReply[keyInstant];
				}
			if(this.__cToolsManagers[keyInstant] != undefined){
				this.__cToolsManagers[keyInstant].removeAllTools();
				delete this.__cToolsManagers[keyInstant];
				}
			this.__registeredForResizeEvent['instant'].__textboxes[o].txtDate.htmlText = this.__registeredForResizeEvent['instant'].__textboxes[o].__txtDate;
			this.__registeredForResizeEvent['instant'].__textboxes[o].txtInfos.htmlText = this.__registeredForResizeEvent['instant'].__textboxes[o].__txtInfos;
			}
		if(bRedraw){
			cStage.redraw();
			}
		};
	
	public function cbSetToReadState(prop, oldVal:Number, newVal:Number, obj:Object){
		//Debug('GOT IT!!! IN: ' + obj.__req.getSec()/1000);
		cReqManager.removeRequest(obj.__req.getID());
		};	
	
	public function showInstantReply(mvTexte:MovieClip, oPanel:Object, bNew:Boolean, arrRows:Array):Void{		
		
		//message instant en detail
		if(mvTexte.__isMsgOpen == 3){
			this.showInstantDetail(mvTexte, oPanel, '_reply', 3, arrRows);
		}else{
			this.showInstantDetail(mvTexte, oPanel, '_reply', 2, arrRows);
			}
			
		//REPLY
		var keyInstant:String = mvTexte.__keyname; 

		//TITLE
		if(mvTexte.__isMsgOpen == 3 || bNew){
			mvTexte.txtTitre.htmlText = gLang[142] + this.__arrMiniProfil['pseudo'] + gLang[143];
		}else{
			mvTexte.txtTitre.htmlText = gLang[297] + this.__arrMiniProfil['pseudo'] + gLang[298];
			}
		
		//positionning
		mvTexte.txtReply.htmlText = '';
		mvTexte.txtReply._height = 100;	
		//set the cursor focus
		//Selection.setFocus(mvTexte.txtReply);
		buttY = mvTexte.txtReply._y + mvTexte.txtReply._height + (this.__hvSpacer * 3);
		
		//draw a box around the reply text box msg
		new CieCornerSquare(mvTexte, mvTexte.txtReply._x, mvTexte.txtReply._y, (mvTexte.txtReply._width + this.__iPanelScrollWidth), mvTexte.txtReply._height, [CieStyle.__box.__borderRadius, CieStyle.__box.__borderRadius, CieStyle.__box.__borderRadius, CieStyle.__box.__borderRadius], [0xffffff, 100], ['ext', 0.5, 0x006699, 100]);
				
		var strXML:String = '';
		strXML += '<UNITOOLBAR>';
		strXML += '<TOOLGROUP n="action" align="left">';
		strXML += '<BUTTON n="send" type="80X28" text="' + gLang[151] + '" style="' + CieStyle.__basic.__buttonColor + ',' + CieStyle.__basic.__buttonBorderWidth + ',' + CieStyle.__basic.__buttonBorderColor + ',' + CieStyle.__basic.__buttonEffectColor + ',' + CieStyle.__basic.__buttonEffectColorOff + ',' + CieStyle.__basic.__buttonFontColor + '"><ACTION n="onrollover" func=""></ACTION></BUTTON>';
		if(!bNew){
			strXML += '<BUTTON n="cancel" type="80X28" text="' + gLang[152] + '" style="' + CieStyle.__basic.__buttonColor + ',' + CieStyle.__basic.__buttonBorderWidth + ',' + CieStyle.__basic.__buttonBorderColor + ',' + CieStyle.__basic.__buttonEffectColor + ',' + CieStyle.__basic.__buttonEffectColorOff + ',' + CieStyle.__basic.__buttonFontColor + '"><ACTION n="onrollover" func=""></ACTION></BUTTON>';
			}
		strXML += '</TOOLGROUP>';
		strXML += '</UNITOOLBAR>';
		//toolmanager
		this.__cToolsManagersReply[keyInstant] = new CieToolsManager(mvTexte, oPanel.__class.getPanelSize().__width, 0, 0, buttY);
		this.__cToolsManagersReply[keyInstant].createFromXml(new XML(strXML).firstChild);
		//set the tool actions
		this.__cToolsManagersReply[keyInstant].getIcon('action', 'send').__class = this;
		this.__cToolsManagersReply[keyInstant].getIcon('action', 'send').__key = keyInstant;
		this.__cToolsManagersReply[keyInstant].getIcon('action', 'send').__user = this.__arrMiniProfil;
		this.__cToolsManagersReply[keyInstant].getIcon('action', 'send').__text = mvTexte.txtReply;
		this.__cToolsManagersReply[keyInstant].getIcon('action', 'send').onRelease = function(Void):Void{
			if(this.__user['pseudo'].indexOf('~') == -1){
				cFunc.sendInstantMsg(this.__class, this.__user, this.__text.text);
			}else{
				new CieTextMessages('MB_OK', gLang[333], gLang[16]);
				}
			}
		//set the tool actions
		if(!bNew){
			this.__cToolsManagersReply[keyInstant].getIcon('action', 'cancel').__mvreply = mvTexte;
			this.__cToolsManagersReply[keyInstant].getIcon('action', 'cancel').__pobj = oPanel;
			this.__cToolsManagersReply[keyInstant].getIcon('action', 'cancel').__class = this;
			this.__cToolsManagersReply[keyInstant].getIcon('action', 'cancel').__key = keyInstant;
			this.__cToolsManagersReply[keyInstant].getIcon('action', 'cancel').__arrRows = arrRows;			
			this.__cToolsManagersReply[keyInstant].getIcon('action', 'cancel').__tool = this.__cToolsManagersReply[keyInstant];
			this.__cToolsManagersReply[keyInstant].getIcon('action', 'cancel').onRelease = function(Void):Void{
				this.__class.showInstantDetail(this.__mvreply, this.__pobj, '_open', 1, this.__arrRows);			
				}
			}

		//redraw all boxes at once
		if(mvTexte.__isMsgOpen != 3){
			cStage.redraw();
			}
		};
	
	/******************************************************************************************************************************************/

	private function loadProfileTemplate(Void):Void{
	
		//flag for album and profil if member is desactivated
		var bMemberIsDesactivated:Boolean = false;
		//parse the pseudo to find a '~' in it
		if(this.__arrMiniProfil['pseudo'].indexOf('~') != -1){
			bMemberIsDesactivated = true;
			}
	
		this.__chatMenuText['_0'] = gLang[153];
		this.__chatMenuText['_1'] = gLang[154];
		this.__chatMenuText['_2'] = gLang[155];
		this.__chatMenuText['_3'] = gLang[156];
		
		//if to small to show it all
		if(mdm.Forms[BC.__system.__formName].width < BC.__system.__resMax){
			mdm.Forms[BC.__system.__formName].width = BC.__system.__resMax;
			cUni.checkWindowsX();
			}
	
		this.__keyName = this.__arrMiniProfil['no_publique'];
	
		//if(!cFunc.checkIfPanelIDExist(this.__strPanel.split(','), this.__keyName)){
			
			//set the detailed profil opened in cFunction for synchonisation 
			//cFunc.setDetailedProfilOpened(this.__strPanel, this.__arrMiniProfil['no_publique'], this);
			
			
			//unregistered the tabManager previously registered when opening a DelatikedProfil
			var oPanel:Object = cFunc.getPanelObject(this.__strPanel.split(','));
			oPanel.__class.removeRegisteredObject(oPanel.__tabManager);
			
			//slogan or not
			var bTitre:Boolean = true;
			if(unescape(this.__arrMiniProfil['titre'].toLowerCase()) == '...'){
				bTitre = false;
				}
			
			//change offset if no slogan
			var yTopOffSet:Number = CieStyle.__profil.__yTopOffSet;
			if(!bTitre){
				yTopOffSet -= 20;
				}
				
			//build the XML new data	
			var strXml:String = '';
			strXml +='<P id="' + this.__keyName + '" n="_tr" content="mvProfilDetails" bgcolor="' + CieStyle.__profil.__bgTopColor + '" scroll="false">';
			strXml +=	'<PT n="messages" model="un" ystart="' + yTopOffSet + '" title="' + gLang[157] + '" closebutt="false">';
			strXml +=		'<P n="_tl" content="' + CieStyle.__profil.__mvBgMessage + '" bgcolor="' + CieStyle.__profil.__bgPanelColorMessages + '" scroll="false">';
			strXml +=			'<PT n="vocal" model="un" title="' + gLang[158] + '" closebutt="false" ystart="' + CieStyle.__profil.__yOffSet + '">';
			strXml +=				'<P n="_tl" content="mvLoader" bgcolor="' + CieStyle.__profil.__bgPanel + '" scroll="false" effect="false"></P>';
			strXml +=			'</PT>';
			strXml +=			'<PT n="courriel" model="un" title="' + gLang[159] + '" closebutt="false">';
			strXml +=				'<P n="_tl" content="mvLoader" bgcolor="' + CieStyle.__profil.__bgPanel + '" scroll="true" effect="false"></P>';
			strXml +=			'</PT>';
			strXml +=			'<PT n="express" model="un" title="' + gLang[160] + '" closebutt="false">';
			strXml +=				'<P n="_tl" content="mvLoader" bgcolor="' + CieStyle.__profil.__bgPanel + '" scroll="false" effect="false"></P>';
			strXml +=			'</PT>';
			strXml +=			'<PT n="instant" model="un" title="' + gLang[161] + '" closebutt="false">';
			strXml +=				'<P n="_tl" content="mvLoader" bgcolor="' + CieStyle.__profil.__bgPanel + '" scroll="true" effect="false"></P>';
			strXml +=			'</PT>';
			strXml +=		'</P>';
			strXml +=	'</PT>'; 
			
			if(this.__arrMiniProfil['album'] == 'Y' && !bMemberIsDesactivated){
				strXml +=	'<PT n="album" model="un" title="' + gLang[162] + '" closebutt="false">';
				strXml +=		'<P n="_tl" content="mvLoader" bgcolor="' + CieStyle.__profil.__bgAlbum + '" scroll="false" effect="false"></P>';
				strXml +=	'</PT>';
				}
			if(this.__arrMiniProfil['photo'] == '2'){
				//will be in loadPhoto
				strXml +=	'<PT n="photo" model="un" title="' + gLang[163] + '" closebutt="false">';
				strXml +=		'<P n="_tl" content="mvLoader" bgcolor="' + CieStyle.__profil.__bgPhoto + '" scroll="false" effect="false"></P>';
				strXml +=	'</PT>';
				}
			
			strXml +=	'<PT n="profil" model="un" title="' + gLang[164] + '" closebutt="false">';
			strXml +=		'<P n="_tl" content="' + CieStyle.__profil.__mvBgProfil + '" bgcolor="' + CieStyle.__profil.__bgPanelColorProfil + '" scroll="false">';
			strXml +=			'<PT n="description" model="un" ystart="' + CieStyle.__profil.__yOffSet + '" title="' + gLang[165] + '" closebutt="false">';
			strXml +=				'<P n="_tl" content="mvLoader" bgcolor="' + CieStyle.__profil.__bgPanel + '" scroll="true" effect="false"></P>';
			strXml +=			'</PT>';
			strXml +=			'<PT n="mon ideal" model="un" title="' + gLang[166] + '" closebutt="false">';
			strXml +=				'<P n="_tl" content="mvLoader" bgcolor="' + CieStyle.__profil.__bgPanel + '" scroll="true" effect="false"></P>';
			strXml +=			'</PT>';
			strXml +=			'<PT n="mon profil" model="un" title="' + gLang[167] + '" closebutt="false">';
			strXml +=				'<P n="_tl" content="mvLoader" bgcolor="' + CieStyle.__profil.__bgPanel + '" scroll="true" effect="false"></P>';
			strXml +=			'</PT>';
			strXml +=		'</P>';
			strXml +=	'</PT>'; 

			//change the node
			cFunc.changeNodeValue(new XML(strXml), this.__strPanel.split(','));
			
			//main panel BG
			this.__mv = cFunc.getPanelContent(this.__strPanel.split(','));
			//this.__mv.txtPseudo.htmlText = gLang[168] + this.__arrMiniProfil['pseudo'] + gLang[169] + this.__arrMiniProfil['age'] + gLang[170]; 
			this.__mv.txtPseudo.htmlText = '<b><font color="' + (CieStyle.__basic['__membership_' + this.__arrMiniProfil['membership']]) + '">' + this.__arrMiniProfil['pseudo'] + '</font></b>, ' + this.__arrMiniProfil['age'] + gLang[170]; 
			//infos
			this.__mv.txtInfos.htmlText = cFormManager.__obj['sexe'][this.__arrMiniProfil['sexe']][1] + "\n"; 
			this.__mv.txtInfos.htmlText += cFormManager.__obj['etatcivil'][this.__arrMiniProfil['etat_civil']][1] + "\n";
			this.__mv.txtInfos.htmlText += cFormManager.__obj['orientation'][this.__arrMiniProfil['orientation']][1] + "\n" ; 
			//geo
			if(gGeo[this.__arrMiniProfil['code_pays'] + '_' + this.__arrMiniProfil['region_id'] + '_' + this.__arrMiniProfil['ville_id']] != undefined){
				this.__mv.txtInfos.htmlText += gGeo[this.__arrMiniProfil['code_pays'] + '_' + this.__arrMiniProfil['region_id'] + '_' + this.__arrMiniProfil['ville_id']];
				}			
			
			if(bTitre){
				this.__mv.txtSlogan.htmlText = "<b>\"" + unescape(this.__arrMiniProfil['titre'].toLowerCase()) + "\"</b>";
			}else{
				this.__mv.txtSlogan.htmlText = '';
				}
			
			//put the thumbnail of the photo fi there is one
			if(this.__arrMiniProfil['photo'] == '2'){
				this.__mv.mvPhoto.mvPicture.mvPhotoBlur.loadMovie(BC.__server.__thumbs + this.__arrMiniProfil['no_publique'].substr(0,2) + '/' + this.__arrMiniProfil['pseudo'] + '.jpg');
				//action onRelease open the photoTab
				this.__mv.mvPhoto.__basetab = this.__strBaseTab + ',photo';
				this.__mv.mvPhoto.__super = this;
				this.__mv.mvPhoto.onRelease = function(Void):Void{
					this.__super.loadPhoto();
					cFunc.openTab(this.__basetab.split(','));
					};
			}else{
				this.__mv.mvPhoto.mvSexeLoader.gotoAndStop('_' + this.__arrMiniProfil['sexe']);
				}
				
			//START set the DetailsMenu
			
			//hide the detailsBox
			this.__mv.mvMenuDetails._alpha = 0;
			//arrow to first frame
			this.__mv.mvMenuDetails.mvArrow.gotoAndStop('_off');
			//action of the bubble
			this.__mv.mvMenuDetails.mvBubble.useHandCursor = false;
			this.__mv.mvMenuDetails.mvBubble.__super = this;
			this.__mv.mvMenuDetails.mvBubble.__menuBox = this.__mv.mvMenuDetails;
			this.__mv.mvMenuDetails.mvBubble.onRollOver = function(Void):Void{
				this.__super.__detailedMenuMouseIsOver = true;
				};
			this.__mv.mvMenuDetails.mvBubble.onDragOut =
			this.__mv.mvMenuDetails.mvBubble.onReleaseOutside = 			
			this.__mv.mvMenuDetails.mvBubble.onRollOut = function(Void):Void{
				this.__super.__detailedMenuMouseIsOver = false;
				this.__super.__cThreadAnimWindowTimerOff = cThreadManager.newThread(2000, this.__super, 'startRollOutTimer', {__supclass:this.__super, __box:this.__menuBox});
				};	
			
			//insert menu text
			//0
			this.__mv.mvMenuDetails.mvSubMenu.sub_0_0.txtInfos.htmlText = this.__chatMenuText['_' + cSockManager.getOnlineStatus(this.__arrMiniProfil['no_publique'])];
			//1	
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_0.txtInfos.htmlText = gLang[171];
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_1.txtInfos.htmlText = gLang[172];
			//2
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_0.txtInfos.htmlText = gLang[173];
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_1.txtInfos.htmlText = gLang[174];
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_2.txtInfos.htmlText = gLang[175];
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_3.txtInfos.htmlText = gLang[296];
			
			//arrow of menu text
			//0
			this.__mv.mvMenuDetails.mvSubMenu.sub_0_0.mvArrow.gotoAndStop('_off');
			//1
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_0.mvArrow.gotoAndStop('_off');
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_1.mvArrow.gotoAndStop('_off');
			//2
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_0.mvArrow.gotoAndStop('_off');
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_1.mvArrow.gotoAndStop('_off');
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_2.mvArrow.gotoAndStop('_off');
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_3.mvArrow.gotoAndStop('_off');
			
			//action on menu text
			//0
			this.__mv.mvMenuDetails.mvSubMenu.sub_0_0.mvButtOver.__super = this;
			this.__mv.mvMenuDetails.mvSubMenu.sub_0_0.mvButtOver.__menuBox = this.__mv.mvMenuDetails;
			this.__mv.mvMenuDetails.mvSubMenu.sub_0_0.mvButtOver.__user = {__pseudo: this.__arrMiniProfil['pseudo'], __nopub:this.__arrMiniProfil['no_publique']};
			this.__mv.mvMenuDetails.mvSubMenu.sub_0_0.mvButtOver.onRollOver = function(Void):Void{
				this.__super.__detailedMenuMouseIsOver = true;
				this._parent.mvArrow.gotoAndStop('_on');
				};
			this.__mv.mvMenuDetails.mvSubMenu.sub_0_0.mvButtOver.onDragOut = 	
			this.__mv.mvMenuDetails.mvSubMenu.sub_0_0.mvButtOver.onReleaseOutside = 	
			this.__mv.mvMenuDetails.mvSubMenu.sub_0_0.mvButtOver.onRollOut = function(Void):Void{
				this._parent.mvArrow.gotoAndStop('_off');
				};	
			this.__mv.mvMenuDetails.mvSubMenu.sub_0_0.mvButtOver.onRelease = function(Void):Void{
				cFunc.askForVideoChat(this.__user);
				this.__super.__detailedMenuMouseIsOver = false;
				this.__super.__cThreadAnimWindowTimerOff = cThreadManager.newThread(2000, this.__super, 'startRollOutTimer', {__supclass:this.__super, __box:this.__menuBox});
				};	
			//1-0
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_0.mvButtOver.__super = this;
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_0.mvButtOver.__menuBox = this.__mv.mvMenuDetails;
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_0.mvButtOver.__user = this.__arrMiniProfil;
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_0.mvButtOver.onRollOver = function(Void):Void{
				this.__super.__detailedMenuMouseIsOver = true;
				this._parent.mvArrow.gotoAndStop('_on');
				};
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_0.mvButtOver.onDragOut = 	
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_0.mvButtOver.onReleaseOutside = 	
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_0.mvButtOver.onRollOut = function(Void):Void{
				this._parent.mvArrow.gotoAndStop('_off');
				};	
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_0.mvButtOver.onRelease = function(Void):Void{
				if(this.__user['pseudo'].indexOf('~') == -1){
					cFunc.addToCarnet(this.__user);
				}else{
					new CieTextMessages('MB_OK', gLang[333], gLang[16]);
					}
				this.__super.__detailedMenuMouseIsOver = false;
				this.__super.__cThreadAnimWindowTimerOff = cThreadManager.newThread(2000, this.__super, 'startRollOutTimer', {__supclass:this.__super, __box:this.__menuBox});
				};	
			//1-1
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_1.mvButtOver.__super = this;
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_1.mvButtOver.__menuBox = this.__mv.mvMenuDetails;
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_1.mvButtOver.__user = this.__arrMiniProfil;
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_1.mvButtOver.onRollOver = function(Void):Void{
				this.__super.__detailedMenuMouseIsOver = true;
				this._parent.mvArrow.gotoAndStop('_on');
				};
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_1.mvButtOver.onDragOut = 	
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_1.mvButtOver.onReleaseOutside = 	
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_1.mvButtOver.onRollOut = function(Void):Void{
				this._parent.mvArrow.gotoAndStop('_off');
				};	
			this.__mv.mvMenuDetails.mvSubMenu.sub_1_1.mvButtOver.onRelease = function(Void):Void{
				if(this.__user['pseudo'].indexOf('~') == -1){
					cFunc.addToListeNoire(this.__user);
				}else{
					new CieTextMessages('MB_OK', gLang[333], gLang[16]);
					}
				this.__super.__detailedMenuMouseIsOver = false;
				this.__super.__cThreadAnimWindowTimerOff = cThreadManager.newThread(2000, this.__super, 'startRollOutTimer', {__supclass:this.__super, __box:this.__menuBox});
				};			
			//2-0
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_0.mvButtOver.__super = this;
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_0.mvButtOver.__baseTab = this.__strBaseTab;
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_0.mvButtOver.__menuBox = this.__mv.mvMenuDetails;
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_0.mvButtOver.onRollOver = function(Void):Void{
				this.__super.__detailedMenuMouseIsOver = true;
				this._parent.mvArrow.gotoAndStop('_on');
				};
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_0.mvButtOver.onDragOut = 	
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_0.mvButtOver.onReleaseOutside = 	
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_0.mvButtOver.onRollOut = function(Void):Void{
				this._parent.mvArrow.gotoAndStop('_off');
				};	
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_0.mvButtOver.onRelease = function(Void):Void{
				var strTabPath = this.__baseTab + ',messages,express';
				cFunc.openTab(strTabPath.split(','));
				this.__super.__detailedMenuMouseIsOver = false;
				this.__super.__cThreadAnimWindowTimerOff = cThreadManager.newThread(2000, this.__super, 'startRollOutTimer', {__supclass:this.__super, __box:this.__menuBox});
				};	
			//2-1
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_1.mvButtOver.__super = this;
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_1.mvButtOver.__baseTab = this.__strBaseTab;
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_1.mvButtOver.__menuBox = this.__mv.mvMenuDetails;
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_1.mvButtOver.onRollOver = function(Void):Void{
				this.__super.__detailedMenuMouseIsOver = true;
				this._parent.mvArrow.gotoAndStop('_on');
				};
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_1.mvButtOver.onDragOut = 	
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_1.mvButtOver.onReleaseOutside = 	
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_1.mvButtOver.onRollOut = function(Void):Void{
				this._parent.mvArrow.gotoAndStop('_off');
				};	
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_1.mvButtOver.onRelease = function(Void):Void{
				var strTabPath = this.__baseTab + ',messages,instant';
				cFunc.openTab(strTabPath.split(','));
				this.__super.__detailedMenuMouseIsOver = false;
				this.__super.__cThreadAnimWindowTimerOff = cThreadManager.newThread(2000, this.__super, 'startRollOutTimer', {__supclass:this.__super, __box:this.__menuBox});
				};
			//2-2
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_2.mvButtOver.__super = this;
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_2.mvButtOver.__baseTab = this.__strBaseTab;
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_2.mvButtOver.__menuBox = this.__mv.mvMenuDetails;
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_2.mvButtOver.onRollOver = function(Void):Void{
				this.__super.__detailedMenuMouseIsOver = true;
				this._parent.mvArrow.gotoAndStop('_on');
				};
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_2.mvButtOver.onDragOut = 	
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_2.mvButtOver.onReleaseOutside = 	
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_2.mvButtOver.onRollOut = function(Void):Void{
				this._parent.mvArrow.gotoAndStop('_off');
				};	
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_2.mvButtOver.onRelease = function(Void):Void{
				var strTabPath = this.__baseTab + ',messages,courriel';
				cFunc.openTab(strTabPath.split(','));
				this.__super.__detailedMenuMouseIsOver = false;
				this.__super.__cThreadAnimWindowTimerOff = cThreadManager.newThread(2000, this.__super, 'startRollOutTimer', {__supclass:this.__super, __box:this.__menuBox});
				};					
			//2-3
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_3.mvButtOver.__super = this;
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_3.mvButtOver.__baseTab = this.__strBaseTab;
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_3.mvButtOver.__menuBox = this.__mv.mvMenuDetails;
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_3.mvButtOver.onRollOver = function(Void):Void{
				this.__super.__detailedMenuMouseIsOver = true;
				this._parent.mvArrow.gotoAndStop('_on');
				};
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_3.mvButtOver.onDragOut = 	
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_3.mvButtOver.onReleaseOutside = 	
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_3.mvButtOver.onRollOut = function(Void):Void{
				this._parent.mvArrow.gotoAndStop('_off');
				};	
			this.__mv.mvMenuDetails.mvSubMenu.sub_2_3.mvButtOver.onRelease = function(Void):Void{
				var strTabPath = this.__baseTab + ',messages,vocal';
				cFunc.openTab(strTabPath.split(','));
				this.__super.__detailedMenuMouseIsOver = false;
				this.__super.__cThreadAnimWindowTimerOff = cThreadManager.newThread(2000, this.__super, 'startRollOutTimer', {__supclass:this.__super, __box:this.__menuBox});
				};				
			
			//chat button
			this.__mv.mvIconMenuChat.__super = this;
			this.__mv.mvIconMenuChat.__menuBox = this.__mv.mvMenuDetails;
			this.__mv.mvIconMenuChat.onRollOver = function(Void):Void{
				this.__super.showDetailedMenu(true, this.__menuBox, 'chat');
				};
			this.__mv.mvIconMenuChat.onRollOut = function(Void):Void{
				this.__super.__detailedMenuMouseIsOver = false;
				this.__super.__cThreadAnimWindowTimerOff = cThreadManager.newThread(2000, this.__super, 'startRollOutTimer', {__supclass:this.__super, __box:this.__menuBox});
				};	
				
			//event action on online status chages of the chat icon
			this.__mv.mvIconChat.__textMenuRef = this.__mv.mvMenuDetails.mvSubMenu.sub_0_0.txtInfos;
			this.__mv.mvIconChat.__arrChatText = this.__chatMenuText;
			this.__mv.mvIconChat.gotoAndStop('_' + cSockManager.getOnlineStatus(this.__arrMiniProfil['no_publique']));
			this.__mv.mvIconChat.__nopub = this.__arrMiniProfil['no_publique'];
			this.__mv.mvIconChat.updateObject = function(nopub, state):Boolean{
				if(nopub == this.__nopub){
					this.__textMenuRef.htmlText = this.__arrChatText['_' + state];
					this.gotoAndStop('_' + state);
					}
				return true;	
				};
			//register for online event	
			cSockManager.registerObjectForOnlineNotification(this.__mv.mvIconChat);
			
			//message button
			this.__mv.mvIconMenuMessage.__super = this;
			this.__mv.mvIconMenuMessage.__menuBox = this.__mv.mvMenuDetails;
			this.__mv.mvIconMenuMessage.onRollOver = function(Void):Void{
				this.__super.showDetailedMenu(true, this.__menuBox, 'message');
				};
			this.__mv.mvIconMenuMessage.onRollOut = function(Void):Void{
				this.__super.__detailedMenuMouseIsOver = false;
				this.__super.__cThreadAnimWindowTimerOff = cThreadManager.newThread(2000, this.__super, 'startRollOutTimer', {__supclass:this.__super, __box:this.__menuBox});
				};		
			
			//bottin button
			this.__mv.mvIconMenuBottin.__super = this;
			this.__mv.mvIconMenuBottin.__menuBox = this.__mv.mvMenuDetails;
			this.__mv.mvIconMenuBottin.onRollOver = function(Void):Void{
				this.__super.showDetailedMenu(true, this.__menuBox, 'bottin');
				};		
			this.__mv.mvIconMenuBottin.onRollOut = function(Void):Void{
				this.__super.__detailedMenuMouseIsOver = false;
				this.__super.__cThreadAnimWindowTimerOff = cThreadManager.newThread(2000, this.__super, 'startRollOutTimer', {__supclass:this.__super, __box:this.__menuBox});
				};				
			//END set the DetailsMenu
			

			//NEW DWIZZEL 13-07-2007	
			this.__cornerButt = this.__mv.createEmptyMovieClip('mvCornerButt', this.__mv.getNextHighestDepth());
			this.__cornerButt = this.__cornerButt.attachMovie('mvIconMinimize', 'mvIconMinimizeCorner', this.__cornerButt.getNextHighestDepth());
			this.__cornerButt._xscale = this.__cornerButt._yscale = 80;
			this.__cornerButt._y = CieStyle.__tabPanel.__tabBorderOffSet;
			this.__cornerButt._x = (oPanel.__class.getPanelSize().__width - this.__cornerButt._width) - CieStyle.__tabPanel.__tabBorderOffSet;
			this.__cornerButt.mvArrowResize._rotation = 180;
			this.__cornerButt.resize = function(w:Number, h:Number):Void{
				this._x = (w - this._width) - CieStyle.__tabPanel.__tabBorderOffSet;
				};
			
			this.__cornerButt.__bubble = null;
			this.__cornerButt.onRollOver = function(Void):Void{
				if(BC.__user.__showbubble){
					this.__bubble = new CieBubble(__stage, 100, 150, 5, '', gLang[323]);
					}
				};
			this.__cornerButt.onRollOut = this.__cornerButt.onDragOut = this.__cornerButt.onReleaseOutside = function(Void):Void{
				this.__bubble.destroy();
				};
			this.__cornerButt.onRelease = function(Void):Void{
				this.__bubble.destroy();
				cUni.resetSize(true);
				};
			
			
			this.__cornerButt.filters = new Array(new GlowFilter(CieStyle.__panel.__effectGlowColor, 0.3,5,5,2,2,false,false));
			oPanel.__class.registerObject(this.__cornerButt);
			//END NEW DWIZZEL 13-07-2007
			
				
			//load sequence	
			this.loadExpressMsg();
			this.loadPhoto();
			if(!bMemberIsDesactivated){
				this.loadDescription();	
				this.loadIdeal();
				this.loadProfil();
				//this.loadVideo();
				}
			this.showInstantMsg();
			this.showCourrielMsg();
			this.showVocalMsg();
			if(this.__arrMiniProfil['album'] == 'Y' && !bMemberIsDesactivated){
				this.loadAlbum();
				}
			//}
		cFunc.openTab(this.__strTab.split(','));
		};
		
	/******************************************************************************************************************************************/	
	
	public function showDetailedMenu(bState:Boolean, mvMenuBox:MovieClip, section:String):Void{
		
		var arrSectionPos:Array = new Array();
		//0 = arrow, 1 = subMenu
		arrSectionPos['chat'] = [10, 0];
		arrSectionPos['message'] = [29, -100];
		arrSectionPos['bottin'] = [49, -200];
				
		if(bState){
			this.__detailedMenuMouseIsOver = true;
			this.__cThreadAnimWindowTimerOff.destroy();
			if(!this.__detailedMenuIsOn){
				this.__cThreadAnimWindow.destroy();
				//show the box
				this.__cThreadAnimWindow = cThreadManager.newThread(20, this, 'animDetailedMenuAlphaOn', {__supclass:this, __box:mvMenuBox});
				}
			//move the arrow
			this.__cThreadAnimArrow.destroy();
			this.__cThreadAnimArrow = cThreadManager.newThread(20, this, 'animDetailedMenuArrow', {__arrow:mvMenuBox.mvArrow, __pos:arrSectionPos[section][0]});
			//move the subMenu
			this.__cThreadAnimSubMenu.destroy();
			this.__cThreadAnimSubMenu = cThreadManager.newThread(20, this, 'animDetailedMenuSubMenu', {__submenu:mvMenuBox.mvSubMenu, __pos:arrSectionPos[section][1]});
			
		}else{
			this.__cThreadAnimWindowTimerOff.destroy();
			if(this.__detailedMenuIsOn && !this.__detailedMenuMouseIsOver){
				this.__cThreadAnimWindow.destroy();
				this.__cThreadAnimWindow = cThreadManager.newThread(20, this, 'animDetailedMenuAlphaOff', {__supclass:this, __box:mvMenuBox});
				//move the subMenu
				this.__cThreadAnimSubMenu.destroy();
				mvMenuBox.mvSubMenu._y = -300;
				}
			}
		};

	public function animDetailedMenuAlphaOn(obj:Object):Boolean{
		if(obj.__box._alpha < 100){
			obj.__box._alpha += 20;
			obj.__supclass.__detailedMenuIsOn = true;
			return true;
			}
		return false;
		};
		
	public function animDetailedMenuArrow(obj:Object):Boolean{
		var newY = (obj.__pos - obj.__arrow._y)/CieStyle.__profil.__animDetailedDividor;
		if(newY){
			obj.__arrow._y += newY;
			}
		if(!newY){
			return false;
			}
		return true;	
		};
		
	public function animDetailedMenuSubMenu(obj:Object):Boolean{
		var newY = (obj.__pos - obj.__submenu._y)/CieStyle.__profil.__animDetailedDividor;
		if(newY){
			obj.__submenu._y += newY;
			}
		if(!newY){
			return false;
			}
		return true;	
		};	
		
	public function animDetailedMenuAlphaOff(obj:Object):Boolean{
		if(obj.__box._alpha > 0){
			obj.__box._alpha -= 20;
			obj.__supclass.__detailedMenuIsOn = false;
			return true;
			}
		return false;
		};	

	public function startRollOutTimer(obj:Object):Boolean{
		obj.__supclass.showDetailedMenu(false, obj.__box);
		return false;
		};	
	
	/******************************************************************************************************************************************/
	
	private function loadAlbum(Void):Void{
		//build request
		var arrD = new Array();
		arrD['methode'] = 'details';
		arrD['action'] = 'album';
		arrD['arguments'] = this.__arrMiniProfil['no_publique'];
		//add the request
		cReqManager.addRequest(arrD, this.cbAlbum, {__caller:this});	
		};	
		
	public function cbAlbum(prop, oldVal:Number, newVal:Number, obj:Object){
		//trace('GOT IT!!! IN: ' + obj.__req.getSec()/1000);
		obj.__super.__caller.buildAlbum(obj.__req.getXml().firstChild);
		cReqManager.removeRequest(obj.__req.getID());
		};	
	
	/*	
	public function moveAlbumEffect(obj:Object):Boolean{
		var newX = (obj.__pos[0] - obj.__photo._x)/3;
		var newY = (obj.__pos[1] - obj.__photo._y)/3;
		if(newX){
			obj.__photo._x += newX;
			}
		if(newY){
			obj.__photo._y += newY;
			}
		if(!newX && !newY){
			return false;
			}
		return true;	
		};
	*/	
	
	public function buildAlbum(xmlNode:XML):Void{
		
		//panel path
		this.__albumRights = new Array();
		var arrPanelAlbum:Array = this.__strPanel.split(',');
		arrPanelAlbum.push('album','_tl');
		
		//the panel object
		var oPanel:Object = cFunc.getPanelObject(arrPanelAlbum);
		oPanel.__class.setContent('mvContent');	
		var mvPanel:MovieClip = oPanel.__class.getPanelContent();				
		
		//register for the resize event
		if(typeof(this.__registeredForResizeEvent['album']) == 'object'){
			oPanel.__class.removeRegisteredObject(this.__registeredForResizeEvent['album']);
			this.__registeredForResizeEvent['album'] = undefined;
			delete this.__registeredForResizeEvent['album'];
			}
		
		//register for the resize event
		this.__registeredForResizeEvent['album'] = new Object();
		//this.__registeredForResizeEvent['album'].__cthread = new Array(); //for effects
		this.__registeredForResizeEvent['album'].__super = this;
		this.__registeredForResizeEvent['album'].__photos = new Array();
		this.__registeredForResizeEvent['album'].resize = function(w:Number, h:Number):Void{
			var arrPos:Array = this.__super.calculateAlbumPositionning(w, this.__nombre);
			//move the photos individualy
			for(var i=0; i < this.__nombre; i++){
				if(this.__photos[i]._x != arrPos[i][0] || this.__photos[i]._y != arrPos[i][1]){
					/*
					this.__cthread[i].destroy();
					this.__cthread[i] = null;
					this.__cthread[i] = cThreadManager.newThread(25, this.__super, 'moveAlbumEffect', {__photo:this.__photos[i], __pos:arrPos[i]});
					*/
					this.__photos[i]._x = arrPos[i][0];
					this.__photos[i]._y = arrPos[i][1];
					}
				}
			};
		oPanel.__class.registerObject(this.__registeredForResizeEvent['album']);
		
		//parse XML
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var rowNode = xmlNode.childNodes[i];
			if(rowNode.attributes.n == 'droit'){
				for(var j=0; j<rowNode.childNodes.length; j++){
					var currNode = rowNode.childNodes[j];
					this.__albumRights[currNode.attributes.n] = Number(currNode.firstChild.nodeValue);
					}
				this.__registeredForResizeEvent['album'].__nombre = this.__albumRights['nombre'];	
			}else if(rowNode.attributes.n == 'photos'){
				for(var j=0; j<rowNode.childNodes.length; j++){
					var rowNode2 = rowNode.childNodes[j];
					//build the album object because of the rights
					var oAlbum:Array = new Array();
					for(var k=0; k<rowNode2.childNodes.length; k++){
						var currNode = rowNode2.childNodes[k];
						oAlbum[currNode.attributes.n] = String(currNode.firstChild.nodeValue);
						}
					this.__albumHolder.push(oAlbum);
					}
				}
			}
		//if he have the rights to see the album show it,
		//if nor show the appropriate messge, w'll buld it after his answer
		if(this.__albumRights['barre'] == '3' && this.__albumRights['autorise'] != '1'){
			this.showAlbumAlert('barre', mvPanel, oPanel.__class.getPanelSize().__width, oPanel);
		}else if(this.__albumRights['xxx']){
			this.showAlbumAlert('xxx', mvPanel, oPanel.__class.getPanelSize().__width, oPanel);
		}else{
			this.showAlbum(mvPanel, oPanel.__class.getPanelSize().__width);
			}	
		}
		
	private function showAlbumAlert(ctype:String, mvPanel:MovieClip, w:Number, oPanel:Object):Void{
		
		//register for the resize event
		if(typeof(this.__registeredForResizeEvent['albumalert']) == 'object'){
			oPanel.__class.removeRegisteredObject(this.__registeredForResizeEvent['albumalert']);
			this.__registeredForResizeEvent['albumalert'] = undefined;
			delete this.__registeredForResizeEvent['albumalert'];
			}
		
		
		//register for the resize event
		this.__registeredForResizeEvent['albumalert'] = new Object();
		this.__registeredForResizeEvent['albumalert'].__super = this;
		this.__registeredForResizeEvent['albumalert'].__panel = mvPanel;
		this.__registeredForResizeEvent['albumalert'].__textboxes = new Array();
		//resize event
		this.__registeredForResizeEvent['albumalert'].resize = function(w:Number, h:Number):Void{
			this.__super.redrawMultipleTextBoxAndButton(this.__panel, this.__textboxes, w);
			};
		oPanel.__class.registerObject(this.__registeredForResizeEvent['albumalert']);	
		//set the background to default for the message
		oPanel.__class.setBgColor(CieStyle.__profil.__bgPanel);
		//build a form
		var mvTexte = mvPanel.attachMovie('mvDescriptionTexte', 'ALERT_ALBUM', mvPanel.getNextHighestDepth());
		//positionning
		mvTexte.txtInfos.autoSize = 'left';
		mvTexte._x = this.__hvSpacer;
		//title and text
		if(ctype == 'xxx'){
			mvTexte.txtInfos.htmlText = gLang[176];
		}else if(ctype == 'barre'){
			mvTexte.txtInfos.htmlText = gLang[177];
			}
		//instance of the the button
		this.__albumButtonChoice = new CieButton(mvPanel, gLang[178],150, 30, 0, 0);
		this.__albumButtonChoice.getMovie().__class = this;
		this.__albumButtonChoice.getMovie().__mvpanel = mvPanel;
		this.__albumButtonChoice.getMovie().__opanel = oPanel;
		this.__albumButtonChoice.getMovie().__width = w;
		if(ctype == 'xxx'){
			this.__albumButtonChoice.getMovie().onRelease = function(Void):Void{
				//change the background color
				this.__opanel.__class.setBgColor(CieStyle.__profil.__bgAlbum);
				this.__class.showAlbum(this.__mvpanel, this.__width);
				this.__class.removeAlert(this.__mvpanel, this.__opanel);
				};
		}else if(ctype == 'barre'){
			this.__albumButtonChoice.getMovie().onRelease = function(Void):Void{
				var strTabPath = this.__class.__strBaseTab + ',messages,instant';
				cFunc.openTab(strTabPath.split(','));
				};
			}
		//register the butt
		this.__registeredForResizeEvent['albumalert'].__textboxes.push(this.__albumButtonChoice);		
		//register the text
		this.__registeredForResizeEvent['albumalert'].__textboxes.push(mvTexte);	
		//redraw all
		this.redrawMultipleTextBoxAndButton(mvPanel, this.__registeredForResizeEvent['albumalert'].__textboxes, oPanel.__class.getPanelSize().__width);			
		};
	
	public function removeAlert(mvPanel:MovieClip, oPanel:Object):Void{
		//remove the graphic suare around text Bxoxes
		mvPanel.clear();
		//remove the registerde object for resize events
		oPanel.__class.removeRegisteredObject(this.__registeredForResizeEvent['albumalert']);
		//remove the text
		mvPanel.ALERT_ALBUM.removeMovieClip();
		//remove the button
		this.__albumButtonChoice.removeButton();
		};
	
	public function showAlbum(mvPanel:MovieClip, w:Number):Void{
		var arrPos:Array = this.calculateAlbumPositionning(w, this.__albumRights['nombre']);
		this.__albumMv = mvPanel.createEmptyMovieClip('ALBUM', mvPanel.getNextHighestDepth());
		
		for(o in this.__albumHolder){
			
			var mvPhoto = this.__albumMv.attachMovie('mvPhotoCadre', 'PHOTO_' + o, this.__albumMv.getNextHighestDepth());
			
			this.__registeredForResizeEvent['album'].__photos.push(mvPhoto);
			mvPhoto._x = arrPos[o][0];
			mvPhoto._y = arrPos[o][1];
				
			//load the picture
			mvPhoto.mvPicture.mvPhotoBlur.loadMovie(BC.__server.__thumbsAlbum + this.__arrMiniProfil['pseudo'] + "|" + this.__albumHolder[o]['photo'] + '|1' + '&PHPSESSID=' + BC.__user.__sessionID);	
				
			if(this.__albumRights['subscription'] == '1'){
				//action onRelease open the subscription
				var filter:BlurFilter = new BlurFilter(14, 14, 3);
				var filterArray:Array = new Array();
				filterArray.push(filter);
				mvPhoto.mvPicture.filters = filterArray;
				mvPhoto.onRelease = function(Void):Void{		
					cFunc.chatAbonnement();
					};
			}else{
				//action onRelease open the photoTab
				mvPhoto.__super = this;
				mvPhoto.__photo = this.__albumHolder[o]['photo'];
				mvPhoto.__pos = o;
				mvPhoto.onRelease = function(Void):Void{				
					this.__super.hideAlbumMovie(true);
					this.__super.showAlbumSinglePhoto(this.__photo, this.__pos);
					};
				}
			}
		};	
	
	public function hideAlbumMovie(b:Boolean):Void{
		this.__albumMv._visible = !b;
		};
		
	public function showAlbumSinglePhoto(strPhoto:String, iPos:Number):Void{

		//path to  the panel containing the photos album
		var arrPanelPath:Array = this.__strPanel.split(',');
		arrPanelPath.push('album','_tl');
		
		//the panel object
		var oPanel:Object = cFunc.getPanelObject(arrPanelPath);
		oPanel.__class.setBgColor(CieStyle.__profil.__bgAlbum);
		var mvPanel:MovieClip = oPanel.__class.getPanelContent();
		var oSize:Object = oPanel.__class.getPanelSize();
		//var mvPhoto:MovieClip = mvPanel.attachMovie('mvLoader', 'LOADER', mvPanel.getNextHighestDepth());
		var mvPhoto:MovieClip = mvPanel.createEmptyMovieClip('LOADER_PHOTO', mvPanel.getNextHighestDepth());
		var mvButt:MovieClip = mvPanel.createEmptyMovieClip('BUTT', mvPanel.getNextHighestDepth());
	
		//photo loader
		var objPhotoLoader:MovieClipLoader = new MovieClipLoader();
		var objPhotoLoaderListener:Object = new Object();
		objPhotoLoaderListener.__super = this;
		objPhotoLoaderListener.__width = oSize.__width;
		objPhotoLoaderListener.__height = oSize.__height;
		objPhotoLoaderListener.__mvphoto = mvPhoto;
		objPhotoLoaderListener.__mvbutt = mvButt;
		objPhotoLoaderListener.__panelclass = oPanel.__class;
		objPhotoLoaderListener.onLoadInit = function(mvLoad){
		
			if(mvLoad._width > this.__width || mvLoad._height > this.__height){
				var iW:Number = mvLoad._width;
				var iH:Number = mvLoad._height;	
	
				//MODIFIED BY AARIZO
				if(iW > this.__width){//si trop large
					var iResize:Number = (iW/this.__width);
					var tmpH:Number = (iH/iResize);
					var tmpW:Number = (iW/iResize);
					if(tmpH > this.__height){//si trop haut
						iResize = (tmpH/this.__height);
						tmpH = (tmpH/iResize);
						tmpW = (tmpW/iResize);
						}
					
				}else if(iH > this.__height){
					var iResize:Number = (iH/this.__height);
					var tmpH:Number = (iH/iResize);
					var tmpW:Number = (iW/iResize);
					if(tmpW > this.__width){//si trop haut
						iResize = (tmpH/this.__width);
						tmpH = (tmpH/iResize);
						tmpW = (tmpW/iResize);
						}
					}
					mvLoad._height = tmpH;
					mvLoad._width = tmpW;
				
			}else{
				var iW:Number = mvLoad._width;
				var iH:Number = mvLoad._height;	

				if(iW < this.__width){
					var iResize:Number = (this.__width/iW); //3
					var tmpH:Number = (iH*iResize);
					var tmpW:Number = (iW*iResize);
					if(tmpH > this.__height){
						iResize = (tmpH/this.__height);
						tmpH = (tmpH/iResize);
						tmpW = (tmpW/iResize);
						}
					mvLoad._height = tmpH;
					mvLoad._width = tmpW;
					}
					
				}
				//MODIFIED BY AARIZO
			//center th picture in the panel
			mvLoad._x = (this.__width - mvLoad._width)/2;
			mvLoad._y = (this.__height - mvLoad._height)/2;
			};
		
		var photoPath:String = BC.__server.__thumbsAlbum + this.__arrMiniProfil['pseudo'] + '|' + strPhoto + '|0' + '&PHPSESSID=' + BC.__user.__sessionID;
		objPhotoLoader.addListener(objPhotoLoaderListener);
		objPhotoLoader.loadClip(photoPath, mvPhoto);
		
		//TOOLBAR
		this.__mvTools = mvPanel.createEmptyMovieClip('mvTools', mvPanel.getNextHighestDepth());
		var strXML:String = '';
		strXML += '<UNITOOLBAR>';
		strXML += '<TOOLGROUP n="action" align="left">';
		strXML += '<TOOL n="next" type="32X32" icon="mvIconImage_3"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[314] + '"></BUBBLE></TOOL>';
		strXML += '<TOOL n="close" type="32X32" icon="mvIconImage_12"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[316] + '"></BUBBLE></TOOL>';
		strXML += '<TOOL n="previous" type="32X32" icon="mvIconImage_2"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[315] + '"></BUBBLE></TOOL>';
		strXML += '</TOOLGROUP>';
		strXML += '</UNITOOLBAR>';
		//toolmanager	
		var xmlTool:XML = new XML(strXML);	
		var cToolsManagers = new CieToolsManager(this.__mvTools, 0, 0, 0, 0);
		cToolsManagers.createFromXml(xmlTool.firstChild);
		
		//set the tool actions		
		cToolsManagers.getIcon('action', 'previous')._alpha = CieStyle.__basic.__alphaNavAlbum;
		cToolsManagers.getIcon('action', 'previous').__class = this;
		cToolsManagers.getIcon('action', 'previous').__cmpt = iPos;
		cToolsManagers.getIcon('action', 'previous').__mvphoto = mvPhoto;
		cToolsManagers.getIcon('action', 'previous').__mvTools = this.__mvTools;
		cToolsManagers.getIcon('action', 'previous').__mvbutt = mvButt;
		cToolsManagers.getIcon('action', 'previous').__panelclass = oPanel.__class;
		cToolsManagers.getIcon('action', 'previous').__albumHolder = this.__albumHolder;
		cToolsManagers.getIcon('action', 'previous').onRelease = function(Void):Void{
			//for bubble text usage
			if(this.__bubbletext != undefined){
				this.__bubble.destroy();
				}
			this.__panelclass.setBgColor(CieStyle.__profil.__bgAlbum);
			this.__class.removeAlert(this.__mvbutt);
			this.__mvphoto.removeMovieClip();			
			
			this.__cmpt++;
			if (this.__cmpt < this.__albumHolder.length){
				this.__class.showAlbumSinglePhoto(this.__albumHolder[this.__cmpt]['photo'], this.__cmpt);
			}else{
	
				this.__cmpt = 0;
				this.__class.showAlbumSinglePhoto(this.__albumHolder[this.__cmpt]['photo'], this.__cmpt);
				}
			this.__mvTools.removeMovieClip();			
			};
		
		cToolsManagers.getIcon('action', 'close')._alpha = CieStyle.__basic.__alphaNavAlbum;
		cToolsManagers.getIcon('action', 'close').__mvphoto = mvPhoto;
		cToolsManagers.getIcon('action', 'close').__mvTools = this.__mvTools;
		cToolsManagers.getIcon('action', 'close').__mvbutt = mvButt;
		cToolsManagers.getIcon('action', 'close').__class = this;
		cToolsManagers.getIcon('action', 'close').__panelclass = oPanel.__class;	
		cToolsManagers.getIcon('action', 'close').onRelease = function(Void):Void{
			//for bubble text usage
			if(this.__bubbletext != undefined){
				this.__bubble.destroy();
				}
			this.__panelclass.setBgColor(CieStyle.__profil.__bgAlbum);
			this.__class.hideAlbumMovie(false);
			this.__class.removeAlert(this.__mvbutt);
			this.__mvphoto.removeMovieClip();
			this.__mvTools.removeMovieClip();
			};	
		
		cToolsManagers.getIcon('action', 'next')._alpha = CieStyle.__basic.__alphaNavAlbum;	
		cToolsManagers.getIcon('action', 'next').__class = this;
		cToolsManagers.getIcon('action', 'next').__cmpt = iPos;
		cToolsManagers.getIcon('action', 'next').__mvphoto = mvPhoto;
		cToolsManagers.getIcon('action', 'next').__mvTools = this.__mvTools;
		cToolsManagers.getIcon('action', 'next').__mvbutt = mvButt;
		cToolsManagers.getIcon('action', 'next').__panelclass = oPanel.__class;
		cToolsManagers.getIcon('action', 'next').__albumHolder = this.__albumHolder;	
		cToolsManagers.getIcon('action', 'next').onRelease = function(Void):Void{
			//for bubble text usage
			if(this.__bubbletext != undefined){
				this.__bubble.destroy();
				}
			this.__panelclass.setBgColor(CieStyle.__profil.__bgAlbum);
			this.__class.removeAlert(this.__mvbutt);
			this.__mvphoto.removeMovieClip();
			this.__cmpt--;
			if (this.__cmpt >= 0){
				this.__class.showAlbumSinglePhoto(this.__albumHolder[this.__cmpt]['photo'], this.__cmpt);
			}else{
				this.__cmpt = this.__albumHolder.length - 1;
				this.__class.showAlbumSinglePhoto(this.__albumHolder[this.__cmpt]['photo'], this.__cmpt);
				}
			this.__mvTools.removeMovieClip();
			};	
		
		this.redrawPhoto(objPhotoLoaderListener, mvPhoto, oSize.__width, oSize.__height);
		
		//MODIFIED BY AARIZO
		
		//register for the resize event
		if(typeof(this.__registeredForResizeEvent['photoalbum']) == 'object'){
			oPanel.__class.removeRegisteredObject(this.__registeredForResizeEvent['photoalbum']);
			this.__registeredForResizeEvent['photoalbum'] = undefined;
			delete this.__registeredForResizeEvent['photoalbum'];
			}
				
		this.__registeredForResizeEvent['photoalbum'] = new Object();
		this.__registeredForResizeEvent['photoalbum'].__objlistener = objPhotoLoaderListener;
		this.__registeredForResizeEvent['photoalbum'].__mvphoto = mvPhoto;
		this.__registeredForResizeEvent['photoalbum'].__class = this;
		this.__registeredForResizeEvent['photoalbum'].resize = function(w:Number, h:Number):Void{
			this.__class.redrawPhoto(this.__objlistener, this.__mvphoto, w, h);
			};
		oPanel.__class.registerObject(this.__registeredForResizeEvent['photoalbum']);
		
		};
			
	private function calculateAlbumPositionning(w:Number, numPhotos:Number):Array{
		//IMPORTANT: 
		//have to hardcode the value of the photo size, symbol in library is 'mvPhotoCadre'
		//for now its 100X75 pixels, dont think that would change but we never know
		//so if we change the symbol, have to put it here too... hehe!
		
		var photoWidth:Number = 100 + 11;
		var photoHeight:Number = 75 + 5;
		//var totalWidthSpace:Number = Math.floor(w / photoWidth);	
		var totalWidthSpace:Number = Math.floor(w / photoWidth);	
		var xPos:Number = this.__hvSpacer - photoWidth;
		var yPos:Number = this.__hvSpacer;
		var iCols:Number = 0;
		
		var arrPos:Array = new Array();
		for(var i=0; i < numPhotos; i++){
			if(iCols < totalWidthSpace){
				xPos += photoWidth;
			}else{
				iCols = 0;
				xPos = this.__hvSpacer;
				yPos += photoHeight;
				}
			arrPos[i] = [xPos, yPos];	
			iCols++;
			}
		return arrPos;
		};
	
	/******************************************************************************************************************************************/

	private function loadProfil(Void):Void{
		//build request
		var arrD = new Array();
		arrD['methode'] = 'details';
		arrD['action'] = 'profil';
		arrD['arguments'] = this.__arrMiniProfil['no_publique'];
		//add the request
		cReqManager.addRequest(arrD, this.cbProfil, {__caller:this});	
		};
	
	public function cbProfil(prop, oldVal:Number, newVal:Number, obj:Object){
		//Debug('GOT IT!!! IN: ' + obj.__req.getSec()/1000);
		obj.__super.__caller.showProfil(obj.__req.getXml().firstChild);
		cReqManager.removeRequest(obj.__req.getID());
		};
	
	public function showProfil(xmlNode:XML):Void{
		var strTmp = '';
		var strTmp2 = '';
		var cmp = 0;
		//parse XML
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var rowNode = xmlNode.childNodes[i];
			if(rowNode.attributes.n == 'essentiel'){
				if(rowNode.childNodes.length > 0){
					strTmp += gLang[179];
					for(var j=0; j<rowNode.childNodes.length; j++){
						var currNode = rowNode.childNodes[j];
						var nName = currNode.attributes.n;
						var nValue = currNode.firstChild.nodeValue;
						if (nName == 'relation' || nName == 'personnalite' || nName == 'importance'){
							strTitleRow = gLang[nName] + ': ';
							strTmp2 = '';
							cmp = 0;
							for (var o in cFormManager.__obj[nName]){
								if (cFormManager.__obj[nName][nValue][0] != 0){
									cmp++;
									}
								var cData = nValue.substring(cmp-1, cmp);
								if (cData == 1){
									strTmp2 += cFormManager.__obj[nName][cmp][1] + ', ';
									}
								}
							if(strTmp2 != ''){	
								strTmp += strTitleRow + '<i>' + strTmp2.substring(0, strTmp2.length - 2) + '</i>\n';
								}
						}else{
							strTmp += gLang[nName] + ': <i>' + cFormManager.__obj[nName][nValue][1] + '</i>\n';
							}
						}
					}
			}else if(rowNode.attributes.n == 'description'){
				if(rowNode.childNodes.length > 0){
					strTmp += gLang[180];
					for(var j=0; j<rowNode.childNodes.length; j++){
						var currNode = rowNode.childNodes[j];
						var nName = currNode.attributes.n;
						var nValue = currNode.firstChild.nodeValue;
						if (nName == 'taille'){
							strTmp += gLang[182] + cConversion.conversionTailleTexte(nValue) + gLang[183];
						}else if (nName == 'poids'){
							strTmp += gLang[184] + cConversion.conversionPoidsTexte(nValue) + gLang[185];
						}else{
							strTmp += gLang[nName] + ': <i>' + cFormManager.__obj[nName][nValue][1] + '</i>\n';
							}
						}
					}	
			}else if(rowNode.attributes.n == 'style'){
				if(rowNode.childNodes.length > 0){
					strTmp += gLang[181];
					for(var j=0; j<rowNode.childNodes.length; j++){
						var currNode = rowNode.childNodes[j];
						var nName = currNode.attributes.n;
						var nValue = currNode.firstChild.nodeValue;
						if (nName == 'languages'){
							strTitleRow = gLang[nName] + ': ';
							strTmp2 = '';
							cmp = 0;
							var arrTemp = ['FR','AN','ES','AL'];
							for (var o in cFormManager.__obj[nName]){
								if (cFormManager.__obj[nName][nValue][0] != 0){
									cmp++;
									}
								var cData = nValue.substring(cmp-1, cmp);
								if (cData == 1){
									strTmp2 += cFormManager.__obj[nName][arrTemp[cmp-1]][1] + ', ';
									}
								}
							if(strTmp2 != ''){	
								strTmp += strTitleRow + '<i>' + strTmp2.substring(0, strTmp2.length - 2) + '</i>\n';
								}	
						}else{
							strTmp += gLang[nName] + ': <i>' + cFormManager.__obj[nName][nValue][1] + '</i>\n';
							}
						}
					}
				}
			}
		//movie
		var arrPanelPath:Array = this.__strPanel.split(',');
		arrPanelPath.push('profil','_tl','mon profil','_tl');
		//the panel object
		var oPanel:Object = cFunc.getPanelObject(arrPanelPath);
		oPanel.__class.setContent('mvContent');	
		var mvPanel:MovieClip = oPanel.__class.getPanelContent();
		
		if(typeof(this.__registeredForResizeEvent['profil']) == 'object'){
			oPanel.__class.removeRegisteredObject(this.__registeredForResizeEvent['profil']);
			this.__registeredForResizeEvent['profil'] = undefined;
			delete this.__registeredForResizeEvent['profil'];
			}
		
		//register for the resize event
		this.__registeredForResizeEvent['profil'] = new Object();
		this.__registeredForResizeEvent['profil'].__super = this;
		this.__registeredForResizeEvent['profil'].__panel = mvPanel;
		this.__registeredForResizeEvent['profil'].resize = function(w:Number, h:Number):Void{
			this.__super.redrawTextBox(this.__panel, this.__textbox, w);
			};
		oPanel.__class.registerObject(this.__registeredForResizeEvent['profil']);
		
		//text box
		var mvTexte:MovieClip = mvPanel.attachMovie('mvDescriptionTexte', 'TEXTE_PROFIL', mvPanel.getNextHighestDepth());
		this.__registeredForResizeEvent['profil'].__textbox = mvTexte;
		mvTexte._x = this.__hvSpacer;
		mvTexte._y = this.__hvSpacer;
		mvTexte.txtInfos.htmlText = strTmp + '\n\n';
		mvTexte.txtInfos.autoSize = 'left';
		this.redrawTextBox(mvPanel, mvTexte, oPanel.__class.getPanelSize().__width);
		};		
	
	/******************************************************************************************************************************************/
	
	private function loadIdeal(Void):Void{
		//build request
		var arrD = new Array();
		arrD['methode'] = 'details';
		arrD['action'] = 'ideal';
		arrD['arguments'] = this.__arrMiniProfil['no_publique'];
		//add the request
		cReqManager.addRequest(arrD, this.cbIdeal, {__caller:this});	
		};
	
	public function cbIdeal(prop, oldVal:Number, newVal:Number, obj:Object){
		obj.__super.__caller.showIdeal(obj.__req.getXml().firstChild);
		cReqManager.removeRequest(obj.__req.getID());
		};
	
	public function showIdeal(xmlNode:XML):Void{
		var strTmp = '';
		var strTmp2 = '';
		var cmp = 0;
		//parse XML
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var rowNode = xmlNode.childNodes[i];
			if(rowNode.attributes.n == 'recherche'){
				if(rowNode.childNodes.length > 0){
					strTmp += gLang[186];
					for(var j=0; j<rowNode.childNodes.length; j++){
						var currNode = rowNode.childNodes[j];
						var nName = currNode.attributes.n;
						var nValue = currNode.firstChild.nodeValue;
						if (nName == 'relation'){
							strTitleRow = gLang[nName] + ': ';
							strTmp2 = '';
							cmp = 0;
							for (var o in cFormManager.__obj[nName]){
								if (cFormManager.__obj[nName][nValue][0] != 0){
									cmp++;
									}
								var cData = nValue.substring(cmp-1, cmp);
								if (cData == 1){
									strTmp2 += cFormManager.__obj[nName][cmp][1] + ', ';
									}
								}
							if(strTmp2 != ''){	
								strTmp += strTitleRow + '<i>' + strTmp2.substring(0, strTmp2.length - 2) + '</i>\n';
								}
						}else{
							strTmp += gLang[nName] + ': <i>' + cFormManager.__obj[nName][nValue][1] + '</i>\n';
							}
						}
					}
			}else if(rowNode.attributes.n == 'description'){
				if(rowNode.childNodes.length > 0){
					strTmp += gLang[187];
					for(var j=0; j<rowNode.childNodes.length; j++){
						var currNode = rowNode.childNodes[j];
						var nName = currNode.attributes.n;
						var nValue = currNode.firstChild.nodeValue;
						if (nName == 'age1'){
							strTmp += gLang[189] + nValue;
						}else if (nName == 'age2'){
							strTmp += ' - ' + nValue + '</i>\n';
						}else if (nName == 'taille1'){
							if (nValue != 0){
								strTmp += gLang[190] + cConversion.conversionTailleTexte(nValue);
								}
						}else if (nName == 'taille2'){
							strTmp += ' - ' + cConversion.conversionTailleTexte(nValue) + '</i>\n';
						}else if (nName == 'poids1'){
							strTmp += gLang[191] + cConversion.conversionPoidsTexte(nValue);
						}else if (nName == 'poids2'){
							strTmp += ' - ' + cConversion.conversionPoidsTexte(nValue) + '</i>\n';
						}else if (nName == 'country_code'){
							this.__pays = nValue;
						}else if (nName == 'region_id'){
							this.__region = nValue;
						}else if (nName == 'ville_id'){
							this.__ville = nValue;
							if(gGeo[this.__pays + '_' + this.__region + '_' + this.__ville] != undefined){
								strTmp += gLang[192] + gGeo[this.__pays + '_' + this.__region + '_' + this.__ville] + '</i>\n';
								}	
						}else{
							strTmp += gLang[nName] + ': <i>' + cFormManager.__obj[nName][nValue][1] + '</i>\n';
							}
						}
					}
			}else if(rowNode.attributes.n == 'details'){
				if(rowNode.childNodes.length > 0){
					strTmp += gLang[188];
					for(var j=0; j<rowNode.childNodes.length; j++){
						var currNode = rowNode.childNodes[j];
						var nName = currNode.attributes.n;
						var nValue = currNode.firstChild.nodeValue;
						if(nName == 'languages'){
							strTitleRow = gLang[nName] + ': ';
							strTmp2 = '';
							var cmp = 0;
							var arrTemp = ['FR','AN','ES','AL'];
							for (var o in cFormManager.__obj[nName]){
								if (cFormManager.__obj[nName][nValue][0] != 0){
									cmp++;
									}
								var cData = nValue.substring(cmp-1, cmp);
								if (cData == 1){
									strTmp2 += cFormManager.__obj[nName][arrTemp[cmp-1]][1] + ', ';
									}
								}
							if(strTmp2 != ''){	
								strTmp += strTitleRow + '<i>' + strTmp2.substring(0, strTmp2.length - 2) + '</i>\n';
								}
						}else{
							strTmp += gLang[nName] + ': <i>' + cFormManager.__obj[nName][nValue][1] + '</i>\n';
							}
						}
					}
				}
			}
		
		//movie
		var arrPanelPath:Array = this.__strPanel.split(',');
		arrPanelPath.push('profil','_tl','mon ideal','_tl');
		//the panel object
		var oPanel:Object = cFunc.getPanelObject(arrPanelPath);
		oPanel.__class.setContent('mvContent');	
		var mvPanel:MovieClip = oPanel.__class.getPanelContent();
		
		if(typeof(this.__registeredForResizeEvent['ideal']) == 'object'){
			oPanel.__class.removeRegisteredObject(this.__registeredForResizeEvent['ideal']);
			this.__registeredForResizeEvent['ideal'] = undefined;
			delete this.__registeredForResizeEvent['ideal'];
			}
		
		//register for the resize event
		this.__registeredForResizeEvent['ideal'] = new Object();
		this.__registeredForResizeEvent['ideal'].__super = this;
		this.__registeredForResizeEvent['ideal'].__panel = mvPanel;
		this.__registeredForResizeEvent['ideal'].resize = function(w:Number, h:Number):Void{
			this.__super.redrawTextBox(this.__panel, this.__textbox, w);
			};
		oPanel.__class.registerObject(this.__registeredForResizeEvent['ideal']);
		
		//text box
		var mvTexte:MovieClip = mvPanel.attachMovie('mvDescriptionTexte', 'TEXTE_IDEAL', mvPanel.getNextHighestDepth());
		this.__registeredForResizeEvent['ideal'].__textbox = mvTexte;
		mvTexte._x = this.__hvSpacer;
		mvTexte._y = this.__hvSpacer;
		mvTexte.txtInfos.htmlText = strTmp + '\n\n';
		mvTexte.txtInfos.autoSize = 'left';
		this.redrawTextBox(mvPanel, mvTexte, oPanel.__class.getPanelSize().__width);
		};	

	/******************************************************************************************************************************************/
	/*
	private function loadVideo(Void):Void{
		//build request
		var arrD = new Array();
		arrD['methode'] = 'details';
		arrD['action'] = 'video';
		arrD['arguments'] = this.__arrMiniProfil['no_publique'] + ',' + this.__arrMiniProfil['pseudo'];
		//add the request
		cReqManager.addRequest(arrD, this.cbVideo, {__caller:this});	
		};
		
	public function cbVideo(prop, oldVal:Number, newVal:Number, obj:Object){
		//Debug('GOT IT!!! IN: ' + obj.__req.getSec()/1000);
		obj.__super.__caller.showVideo(obj.__req.getXml().firstChild);
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	public function showVideo(xmlNode:XML):Void{
		var arrPanelPath:Array = this.__strPanel.split(',');
		arrPanelPath.push('profil','_tl','mon video','_tl');
		//the panel object
		var oPanel:Object = cFunc.getPanelObject(arrPanelPath);
		oPanel.__class.setContent('mvContent');	
		var mvPanel:MovieClip = oPanel.__class.getPanelContent();
		var oSize:Object = oPanel.__class.getPanelSize();	
		
		//unregister
		if(typeof(this.__registeredForResizeEvent['video']) == 'object'){
			oPanel.__class.removeRegisteredObject(this.__registeredForResizeEvent['video']);
			this.__registeredForResizeEvent['video'] = undefined;
			delete this.__registeredForResizeEvent['video'];
			}
				
		//parse XML
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.attributes.n == 'video'){
				var mvPlayer = new CieVideoPlayer(mvPanel, unescape(currNode.firstChild.nodeValue));
				mvPlayer.resizePlayer(oSize.__width, oSize.__height);
			
				//register for the resize event
				this.__registeredForResizeEvent['video'] = new Object();
				this.__registeredForResizeEvent['video'].__player = mvPlayer;
				this.__registeredForResizeEvent['video'].resize = function(w:Number, h:Number):Void{
					this.__player.resizePlayer(w, h);
					};
				oPanel.__class.registerObject(this.__registeredForResizeEvent['video']);	
				}
			}
		};
	*/	
	
	/******************************************************************************************************************************************/
	
	private function loadDescription(Void):Void{
		//build request
		var arrD = new Array();
		arrD['methode'] = 'details';
		arrD['action'] = 'description';
		arrD['arguments'] = this.__arrMiniProfil['no_publique'];
		//add the request
		cReqManager.addRequest(arrD, this.cbDescription, {__caller:this});	
		};
		
	public function cbDescription(prop, oldVal:Number, newVal:Number, obj:Object){
		//Debug('GOT IT!!! IN: ' + obj.__req.getSec()/1000);
		obj.__super.__caller.showDescription(obj.__req.getXml().firstChild);
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	public function showDescription(xmlNode:XML):Void{
		var arrPanelPath:Array = this.__strPanel.split(',');
		arrPanelPath.push('profil','_tl','description','_tl');
		//the panel object
		var oPanel:Object = cFunc.getPanelObject(arrPanelPath);
		oPanel.__class.setContent('mvContent');	
		var mvPanel:MovieClip = oPanel.__class.getPanelContent();
		
		if(typeof(this.__registeredForResizeEvent['description']) == 'object'){
			oPanel.__class.removeRegisteredObject(this.__registeredForResizeEvent['description']);
			this.__registeredForResizeEvent['description'] = undefined;
			delete this.__registeredForResizeEvent['description'];
			}
		
		//register for the resize event
		this.__registeredForResizeEvent['description'] = new Object();
		this.__registeredForResizeEvent['description'].__super = this;
		this.__registeredForResizeEvent['description'].__panel = mvPanel;
		this.__registeredForResizeEvent['description'].resize = function(w:Number, h:Number):Void{
			this.__super.redrawTextBox(this.__panel, this.__textbox, w);
			};
		oPanel.__class.registerObject(this.__registeredForResizeEvent['description']);
		
		//parse XML
		var tmpHeight = this.__hvSpacer;
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.attributes.n == 'vocal'){
				if(String(currNode.firstChild.nodeValue) != 'N'){
					//will be a button
					var toolSound = new CieTools(mvPanel, 'sound', '32X32', 'mvIconImage_29');
					toolSound.setAction('onclick', 'openDescriptionVocal', [this.__arrMiniProfil['no_publique'], this.__arrMiniProfil['pseudo'], unescape(currNode.firstChild.nodeValue)]);
					//toolSound.setBubble('cliquer ici pour écouter la description vocale de <b>' + this.__arrMiniProfil['pseudo'] + '</b>');
					toolSound.changeColor(CieStyle.__basic.__buttonColor, CieStyle.__basic.__buttonBorderWidth, CieStyle.__basic.__buttonBorderColor, CieStyle.__basic.__buttonEffectColor, CieStyle.__basic.__buttonEffectColorOff);
					//toolSound.redraw(this.__hvSpacer * 1.5, this.__hvSpacer);
					toolSound.redraw(this.__hvSpacer * 1.5, tmpHeight);
					new CieTextLine(mvPanel, ((this.__hvSpacer * 2) + toolSound.getIconWidth()), (tmpHeight + 5), 0, 300, 'tf', gLang[440], 'dynamic',[true,false,false], false, false, false, false);
		
					tmpHeight =  mvPanel._height + (this.__hvSpacer);
									
					/*
					var mvPlayer = new CieSoundPlayer(mvPanel, unescape(currNode.firstChild.nodeValue));
					mvPlayer.getMovie()._x = this.__hvSpacer;
					mvPlayer.getMovie()._y = tmpHeight;
					tmpHeight =  mvPanel._height + (this.__hvSpacer * 2);
					*/
					}
			}else if(currNode.attributes.n == 'texte'){
				
				//si il y a une description audio ou video mettre un separateur
				if(toolVideo != undefined || toolSound != undefined){
					var mvSep:MovieClip = mvPanel.attachMovie('mvHorSeparateur', 'HS', mvPanel.getNextHighestDepth());
					mvSep._y = tmpHeight;
					mvSep._x = (this.__hvSpacer * 1.5);
					tmpHeight = mvPanel._height + (this.__hvSpacer);
					}
				
				var mvTexte = mvPanel.attachMovie('mvDescriptionTexte', 'TEXTE_DESCRIPTION', mvPanel.getNextHighestDepth());
				//positionning
				mvTexte.txtInfos.autoSize = 'left';	
				mvTexte._x = this.__hvSpacer;
				mvTexte._y = tmpHeight;
				//register
				this.__registeredForResizeEvent['description'].__textbox = mvTexte;
				//texte
				if(String(currNode.firstChild.nodeValue) != '' && currNode.firstChild.nodeValue != undefined ){
					mvTexte.txtInfos.htmlText = unescape(currNode.firstChild.nodeValue) + '\n\n';
				}else{
					mvTexte.txtInfos.htmlText = '...';
					}
				//draw box				
				this.redrawTextBox(mvPanel, mvTexte, oPanel.__class.getPanelSize().__width);
				
			}else if(currNode.attributes.n == 'video'){
				if(String(currNode.firstChild.nodeValue) != 'N'){
					//will be a button
					var toolVideo = new CieTools(mvPanel, 'video', '32X32', 'mvIconImage_30');
					toolVideo.setAction('onclick', 'openDescriptionVideo', [this.__arrMiniProfil['no_publique'], this.__arrMiniProfil['pseudo'], unescape(currNode.firstChild.nodeValue), this.__strBaseTab + ',messages,instant']);
					//toolVideo.setBubble('cliquer ici pour visionner la description vidéo de <b>' + this.__arrMiniProfil['pseudo'] + '</b>');
					toolVideo.changeColor(CieStyle.__basic.__buttonColor, CieStyle.__basic.__buttonBorderWidth, CieStyle.__basic.__buttonBorderColor, CieStyle.__basic.__buttonEffectColor, CieStyle.__basic.__buttonEffectColorOff);
					/*
					if(toolSound != undefined){
						toolVideo.redraw((toolSound.getIconWidth() + (this.__hvSpacer * 2)), this.__hvSpacer);
					}else{
						toolVideo.redraw((this.__hvSpacer * 1.5), this.__hvSpacer);
						tmpHeight =  mvPanel._height + (this.__hvSpacer * 1.5);
						}
					*/	
					
					toolVideo.redraw((this.__hvSpacer * 1.5), tmpHeight);
					new CieTextLine(mvPanel, ((this.__hvSpacer * 2) + toolVideo.getIconWidth()), (tmpHeight + 5), 0, 300, 'tf', gLang[441], 'dynamic',[true,false,false], false, false, false, false);
					tmpHeight =  mvPanel._height + (this.__hvSpacer);
					
					/*
					var mvVideoPlayer = new CieVideoPlayer(mvPanel, unescape(currNode.firstChild.nodeValue));
					mvVideoPlayer.getMovie()._x = this.__hvSpacer;
					mvVideoPlayer.getMovie()._y = tmpHeight;
					tmpHeight =  mvPanel._height + (this.__hvSpacer * 2);
					*/
					}
				}
			}
		};	
	
	/******************************************************************************************************************************************/
	//resize the single photo of photo or album //resize the single photo of photo or album
 	private function redrawPhoto(objListener:Object, mv:MovieClip, w:Number, h:Number):Void{
		objListener.__width = w;
		objListener.__height = h;
		objListener.onLoadInit(mv);
		this.__mvTools._x = ((w/2) - (this.__mvTools._width/2))-20;
		this.__mvTools._y = (h - this.__mvTools._height);
		};
	
	//specially for voal and express msg
	private function redrawMultipleTextBoxAndButton(mvPanel:MovieClip, arrTexte:Array, w:Number):Void{
		//clear the drawing
		mvPanel.clear();
		//Ypos
		var tmpHeight:Number = this.__hvSpacer;
		//loop trough all textBoxes/CieButton
		w -= this.__iPanelScrollWidth + this.__hvSpacer;
		for(var o in arrTexte){
			//check if its a button
			if(arrTexte[o].getClassName() == 'CieButton'){
				//its a button then draw is different
				arrTexte[o].redraw((this.__hvSpacer * 3),  tmpHeight);
				tmpHeight += arrTexte[o].getMovie()._height + (this.__hvSpacer * 2); 
			}else{
				arrTexte[o]._y = tmpHeight;
				//place the width to follow the Panel
				arrTexte[o].txtInfos._width = w;
				//new Height
				tmpHeight += (arrTexte[o].txtInfos._height + arrTexte[o].txtInfos._y ) + (this.__hvSpacer * 2);
				}
			}
		};
	
	//specially for instant msg
	private function redrawMultipleTextBoxMsg(mvPanel:MovieClip, arrTexte:Array, w:Number):Void{
		
		//Debug('redrawMultipleTextBoxMsg(' + arrTexte.length + ')(' + w + ')');
		
		//Ypos
		var tmpHeight = this.__hvSpacer;
		var bReplyWasFound = false;
		//loop trough all textBoxes
		for(var o in arrTexte){
			// effacer les dessins graphiques contenu dans les movies des messages
			arrTexte[o].clear();
			if (arrTexte[o].__isMsgOpen == 0){ //close
				//efface les boutons
				this.__cToolsManagers[arrTexte[o].__keyname].removeAllTools();
				//the y of the instant msg
				arrTexte[o]._y = tmpHeight;
				//place the width to follow the Panel	
				arrTexte[o].txtInfos._width = w - arrTexte[o].txtInfos._x - this.__iPanelScrollWidth - this.__hvSpacer;				
				//draw a line
				if(o == 1){ //end of line not the same color
					arrTexte[o].lineStyle(0.5, 0x666666, 100);
				}else{
					arrTexte[o].lineStyle(0.5, 0xcccccc, 100);
					}	
				arrTexte[o].moveTo(0, arrTexte[o]._height);
				arrTexte[o].lineTo((w - this.__iPanelScrollWidth - (this.__hvSpacer)), arrTexte[o]._height);
				//next height
				tmpHeight += (this.__hvSpacer * 2);
				
			}else if (arrTexte[o].__isMsgOpen == 1){ //details
				//the y of the instant msg
				arrTexte[o]._y = tmpHeight;
				//place the width to follow the Panel
				arrTexte[o].txtDate._width = w - arrTexte[o].txtDate._x - this.__iPanelScrollWidth - this.__hvSpacer;
				arrTexte[o].txtInfos._width = w - this.__iPanelScrollWidth - this.__hvSpacer;
				//TOOLS			
				this.__cToolsManagers[arrTexte[o].__keyname].moveY(arrTexte[o].txtInfos._y + arrTexte[o].txtInfos._height + (this.__hvSpacer * 3));
				this.__cToolsManagers[arrTexte[o].__keyname].resize(0, 0);
				//draw a box around the textBox
				new CieCornerSquare(arrTexte[o], 0, -4, (w - this.__iPanelScrollWidth - (this.__hvSpacer)), (arrTexte[o]._height + (this.__hvSpacer * 2)), [0, CieStyle.__box.__borderRadius, 0, CieStyle.__box.__borderRadius], [0xeDeDeD, 100], ['int', 0.5, 0xCCCCCC, 100]);
				//next Y			
				tmpHeight += arrTexte[o]._height;
			
			}else if(arrTexte[o].__isMsgOpen == 2){ //reply
				
				bReplyWasFound = true;
				
				//the y of the instant msg
				arrTexte[o]._y = tmpHeight;
				//place the width to follow the Panel	
				arrTexte[o].txtTitre._width = w - arrTexte[o].txtTitre._x - this.__iPanelScrollWidth - this.__hvSpacer;
				arrTexte[o].txtReply._width = w - (this.__iPanelScrollWidth * 2) - (this.__hvSpacer * 2);
				Selection.setFocus(arrTexte[o].txtReply);
				arrTexte[o].txtDate._width = w - arrTexte[o].txtDate._x - this.__iPanelScrollWidth - this.__hvSpacer;
				arrTexte[o].txtInfos._width = w - this.__iPanelScrollWidth  - this.__hvSpacer;
				
				arrTexte[o].mvDescriptionScroll._x = arrTexte[o].txtReply._x + arrTexte[o].txtReply._width;
				//reply tools
				this.__cToolsManagersReply[arrTexte[o].__keyname].moveY(arrTexte[o].txtReply._y + arrTexte[o].txtReply._height + (this.__hvSpacer * 3));
				this.__cToolsManagersReply[arrTexte[o].__keyname].resize(0, 0);
				//TOOLS
				this.__cToolsManagers[arrTexte[o].__keyname].moveY(arrTexte[o].txtInfos._y + arrTexte[o].txtInfos._height + (this.__hvSpacer * 3));
				this.__cToolsManagers[arrTexte[o].__keyname].resize(0, 0);
				//draw a box around the reply text box msg
				if(arrTexte[o].__keyname == 'NEW_I'){
					new CieCornerSquare(arrTexte[o], arrTexte[o].txtReply._x, arrTexte[o].txtReply._y, (arrTexte[o].txtReply._width + this.__iPanelScrollWidth), arrTexte[o].txtReply._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
				}else{
					new CieCornerSquare(arrTexte[o], arrTexte[o].txtReply._x, arrTexte[o].txtReply._y, (arrTexte[o].txtReply._width + this.__iPanelScrollWidth), arrTexte[o].txtReply._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x006699, 100]);
					}
				//draw a aquare around the message box				
				new CieCornerSquare(arrTexte[o], 0, (arrTexte[o].txtDate._y - 4), (w - this.__iPanelScrollWidth - (this.__hvSpacer)), (arrTexte[o]._height + (this.__hvSpacer * 2) - arrTexte[o].txtDate._y), [0, CieStyle.__box.__borderRadius, 0, CieStyle.__box.__borderRadius], [0xeDeDeD, 100], ['int', 0.5, 0xCCCCCC, 100]);
				//next Y
				tmpHeight += arrTexte[o]._height;
			
			}else if(arrTexte[o].__isMsgOpen == 3){ //alone
			
				if(bReplyWasFound){
					arrTexte[o]._visible = false;
				}else{
					arrTexte[o]._visible = true;
					//the new message instant boxx on the top
					//the y of the instant msg
					tmpHeight += this.__hvSpacer;
					arrTexte[o]._y = tmpHeight;
					//place the width to follow the Panel
					arrTexte[o].txtTitre._width = w - arrTexte[o].txtTitre._x - this.__iPanelScrollWidth - this.__hvSpacer;
					arrTexte[o].txtReply._width = w - (this.__iPanelScrollWidth * 2) - (this.__hvSpacer * 2);
					Selection.setFocus(arrTexte[o].txtReply);
					arrTexte[o].mvDescriptionScroll._x = arrTexte[o].txtReply._x + arrTexte[o].txtReply._width;
					//reply tools
					this.__cToolsManagersReply[arrTexte[o].__keyname].moveY(arrTexte[o].txtReply._y + arrTexte[o].txtReply._height + (this.__hvSpacer * 3));
					this.__cToolsManagersReply[arrTexte[o].__keyname].resize(0, 0);
					//draw a box around the reply text box msg
					new CieCornerSquare(arrTexte[o], arrTexte[o].txtReply._x, arrTexte[o].txtReply._y, (arrTexte[o].txtReply._width + this.__iPanelScrollWidth), arrTexte[o].txtReply._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
					//next Y
					tmpHeight += arrTexte[o]._height + this.__hvSpacer;
					}
				}							
			}
		//draw a shit suqare for scrolling 
		mvPanel.clear();
		mvPanel.lineStyle(0.5, 0xff0000, 0);
		mvPanel.moveTo(0, tmpHeight + 30);
		mvPanel.lineTo(w, tmpHeight + 30);
		};	
	
	//specially for courriel
	private function redrawMultipleTextBoxMsgCourriel(mvPanel:MovieClip, arrTexte:Array, w:Number):Void{
		
		var nextY:Number;
		//si un rely est deja ouvert pour ne pas mettre un new aussi
		var bReplyWasFound = false;
		//attach
		var mvAttach:MovieClip;
		//Ypos
		var tmpHeight = this.__hvSpacer;
		//loop trough all textBoxes
		for(var o in arrTexte){
			// effacer les dessins graphiques contenu dans les movies des messages
			arrTexte[o].clear();
			if (arrTexte[o].__isMsgOpen == 0){ //CLOSE STATE
				//the y of the instant msg
				arrTexte[o]._y = tmpHeight;
				//place the width to follow the Panel		
				arrTexte[o].txtSujet._width = w - arrTexte[o].txtSujet._x - this.__iPanelScrollWidth - this.__hvSpacer;			
				//draw a line
				if(o == 1){ //end of line not the same color
					arrTexte[o].lineStyle(0.5, 0x666666, 100);
				}else{
					arrTexte[o].lineStyle(0.5, 0xcccccc, 100);
					}	
				arrTexte[o].moveTo(0, arrTexte[o]._height);
				arrTexte[o].lineTo((w - this.__iPanelScrollWidth - (this.__hvSpacer)), arrTexte[o]._height);
				//next height
				tmpHeight += (this.__hvSpacer * 2);

			}else if (arrTexte[o].__isMsgOpen == 1){//OPEN STATE
				//the y of the courrier msg
				arrTexte[o]._y = tmpHeight;
				//place the width to follow the Panel
				arrTexte[o].receive.txtDate._width = w - arrTexte[o].receive.txtDate._x - this.__iPanelScrollWidth- this.__hvSpacer;
				arrTexte[o].receive.txtSujet._width = w - this.__iPanelScrollWidth  - this.__hvSpacer;
				arrTexte[o].receive.txtInfos._width = w - this.__iPanelScrollWidth  - this.__hvSpacer;
				//attchement
				nextY = (arrTexte[o].receive.txtInfos._y + arrTexte[o].receive.txtInfos._height) + this.__hvSpacer;	
				if(arrTexte[o].ATTACH != undefined){
					var mvAttach = arrTexte[o].ATTACH;
					mvAttach._y = nextY;
					nextY += mvAttach._height + this.__hvSpacer;					
					}
				nextY += (this.__hvSpacer * 2);					
				//tools					
				this.__cToolsManagers[arrTexte[o].__keyname].moveY(nextY);
				this.__cToolsManagers[arrTexte[o].__keyname].resize((w - this.__iPanelScrollWidth), 0);
				//draw a box around the textBox
				new CieCornerSquare(arrTexte[o], 0, -4, (w - this.__iPanelScrollWidth - (this.__hvSpacer)), (arrTexte[o]._height + (this.__hvSpacer * 2)), [0, CieStyle.__box.__borderRadius, 0, CieStyle.__box.__borderRadius], [0xeDeDeD, 100], ['int', 0.5, 0xCCCCCC, 100]);
				//next Y
				tmpHeight += arrTexte[o]._height;

			}else if(arrTexte[o].__isMsgOpen == 2){ //reply
				bReplyWasFound = true;
				//they of the courriel msg
				arrTexte[o]._y = tmpHeight;
				//place the width to follow the Panel
				arrTexte[o].txtMsg._width = w - arrTexte[o].txtMsg._x - this.__iPanelScrollWidth  - this.__hvSpacer;
				arrTexte[o].txtReplyTitre._width = w - this.__iPanelScrollWidth - (this.__hvSpacer * 2);
				Selection.setFocus(arrTexte[o].txtReplyTitre);
				arrTexte[o].txtReply._width = w - (this.__iPanelScrollWidth * 2) - (this.__hvSpacer * 2);
				arrTexte[o].txtCopy._width = w - arrTexte[o].txtCopy._x - this.__iPanelScrollWidth - this.__hvSpacer;
				arrTexte[o].receive.txtDate._width =  w - arrTexte[o].receive.txtDate._x - this.__iPanelScrollWidth  - this.__hvSpacer;
				arrTexte[o].receive.txtSujet._width = w - this.__iPanelScrollWidth - this.__hvSpacer;
				arrTexte[o].receive.txtInfos._width = w - this.__iPanelScrollWidth - this.__hvSpacer;
				
				arrTexte[o].mvDescriptionScroll._x = arrTexte[o].txtReply._x + arrTexte[o].txtReply._width;
				
				//attach
				mvAttachReply = arrTexte[o].ATTACH_REPLY
				mvAttachReply._y = arrTexte[o].txtCopy._y + arrTexte[o].txtCopy._height + this.__hvSpacer;
				
				nextY = mvAttachReply._y + mvAttachReply._height + (this.__hvSpacer * 3);	
				
				this.__cToolsManagersReply[arrTexte[o].__keyname].moveY(Number(nextY));
				this.__cToolsManagersReply[arrTexte[o].__keyname].resize(0, 0);
				nextY += this.__cToolsManagersReply[arrTexte[o].__keyname].getTool('action', 'send').getIconHeight() + this.__hvSpacer;
				
				if(arrTexte[o].receive != undefined){
					arrTexte[o].receive._y = nextY;					
					nextY += arrTexte[o].receive._height + this.__hvSpacer;	
					}
				
				if(arrTexte[o].ATTACH != undefined){
					var mvAttach = arrTexte[o].ATTACH;
					mvAttach._y = nextY;
					nextY += mvAttach._height + this.__hvSpacer;			
					}
				nextY += (this.__hvSpacer * 2);	
				
				this.__cToolsManagers[arrTexte[o].__keyname].moveY(nextY);
				this.__cToolsManagers[arrTexte[o].__keyname].resize(0, 0);
			
				
				//draw a box around the reply text box msg
				if(arrTexte[o].__keyname == 'NEW_C'){
					new CieCornerSquare(arrTexte[o], arrTexte[o].txtReplyTitre._x, arrTexte[o].txtReplyTitre._y, arrTexte[o].txtReplyTitre._width, arrTexte[o].txtReplyTitre._height + 3, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
					new CieCornerSquare(arrTexte[o], arrTexte[o].txtReply._x, arrTexte[o].txtReply._y, (arrTexte[o].txtReply._width + this.__iPanelScrollWidth), arrTexte[o].txtReply._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
				}else{
					new CieCornerSquare(arrTexte[o], arrTexte[o].txtReplyTitre._x, arrTexte[o].txtReplyTitre._y, arrTexte[o].txtReplyTitre._width, arrTexte[o].txtReplyTitre._height + 3, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x006699, 100]);
					new CieCornerSquare(arrTexte[o], arrTexte[o].txtReply._x, arrTexte[o].txtReply._y, (arrTexte[o].txtReply._width + this.__iPanelScrollWidth), arrTexte[o].txtReply._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x006699, 100]);
					}
								
				//draw a aquare around the message box				
				new CieCornerSquare(arrTexte[o], 0, (arrTexte[o].receive._y - 4), (w - this.__iPanelScrollWidth - (this.__hvSpacer)), (arrTexte[o]._height + (this.__hvSpacer * 2) - arrTexte[o].receive._y), [0, CieStyle.__box.__borderRadius, 0, CieStyle.__box.__borderRadius], [0xeDeDeD, 100], ['int', 0.5, 0xCCCCCC, 100]);
								
				tmpHeight += arrTexte[o]._height + this.__hvSpacer;
			
			}else if(arrTexte[o].__isMsgOpen == 3){ //a alone new box
				if(bReplyWasFound){
					arrTexte[o]._visible = false;
				}else{
					arrTexte[o]._visible = true;
					//they of the courriel msg
					tmpHeight += this.__hvSpacer;
					arrTexte[o]._y = tmpHeight;
									
					//place the width to follow the Panel
					arrTexte[o].txtMsg._width = w - arrTexte[o].txtMsg._x - this.__iPanelScrollWidth  - this.__hvSpacer;
					arrTexte[o].txtReplyTitre._width = w - this.__iPanelScrollWidth - (this.__hvSpacer * 2);
					Selection.setFocus(arrTexte[o].txtReplyTitre);
					arrTexte[o].txtReply._width = w - (this.__iPanelScrollWidth * 2) - (this.__hvSpacer * 2);
					arrTexte[o].txtCopy._width = w - arrTexte[o].txtCopy._x - this.__iPanelScrollWidth - this.__hvSpacer;
					
					arrTexte[o].mvDescriptionScroll._x = arrTexte[o].txtReply._x + arrTexte[o].txtReply._width;
					
					//attach
					mvAttachReply = arrTexte[o].ATTACH_REPLY
					mvAttachReply._y = arrTexte[o].txtCopy._y + arrTexte[o].txtCopy._height + this.__hvSpacer;
					
					nextY = mvAttachReply._y + mvAttachReply._height + (this.__hvSpacer * 3);	
					
					this.__cToolsManagersReply[arrTexte[o].__keyname].moveY(Number(nextY));
					this.__cToolsManagersReply[arrTexte[o].__keyname].resize(0, 0);
					nextY += this.__cToolsManagersReply[arrTexte[o].__keyname].getTool('action', 'send').getIconHeight() + this.__hvSpacer;
					
					if(arrTexte[o].ATTACH != undefined){
						var mvAttach = arrTexte[o].ATTACH;
						mvAttach._y = nextY;
						nextY += mvAttach._height + this.__hvSpacer;			
						}
					nextY += (this.__hvSpacer * 2);	
					
					this.__cToolsManagers[arrTexte[o].__keyname].moveY(nextY);
					this.__cToolsManagers[arrTexte[o].__keyname].resize(0, 0);
					
					//draw a box around the reply text box msg
					new CieCornerSquare(arrTexte[o], arrTexte[o].txtReplyTitre._x, arrTexte[o].txtReplyTitre._y, arrTexte[o].txtReplyTitre._width, arrTexte[o].txtReplyTitre._height + 3, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
					new CieCornerSquare(arrTexte[o], arrTexte[o].txtReply._x, arrTexte[o].txtReply._y, (arrTexte[o].txtReply._width + this.__iPanelScrollWidth), arrTexte[o].txtReply._height, [CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius, CieStyle.__basic.__toolRadius], [0xffffff, 100], ['ext', 0.5, 0x999999, 100]);
					
					tmpHeight += arrTexte[o]._height + this.__hvSpacer;
					}
				}
			}
		mvPanel.clear();
		mvPanel.lineStyle(0.5, 0xff0000, 0);
		mvPanel.moveTo(0, tmpHeight + 30);
		mvPanel.lineTo(w, tmpHeight + 30);	
		};
	
	//specially for test only like description/profil/etc...	
	private function redrawTextBox(mvPanel:MovieClip, mvTexte:MovieClip, w:Number):Void{
		//clear the drawing
		mvPanel.clear();
		w -= this.__iPanelScrollWidth + this.__hvSpacer;
		//place the width to follow the Panel
		mvTexte.txtInfos._width = w;
		};
	
	/******************************************************************************************************************************************/
	
	private function loadPhoto(Void):Void{
		if(this.__arrMiniProfil['photo'] == '2'){
			var photoPath:String = BC.__server.__photos + this.__arrMiniProfil['no_publique'].substr(0,2) + "/" + this.__arrMiniProfil['pseudo'] + ".jpg";

			//path to  the panel containing the photos
			var arrPanelPhoto:Array = this.__strPanel.split(',');
			arrPanelPhoto.push('photo','_tl');
	
			//the panel object
			var oPanel:Object = cFunc.getPanelObject(arrPanelPhoto);
			oPanel.__class.setContent('mvContent');			
			var mvPanel:MovieClip = oPanel.__class.getPanelContent();
			var oSize:Object = oPanel.__class.getPanelSize();
			var mvPhoto:MovieClip = mvPanel.createEmptyMovieClip('LOADER_PHOTO', mvPanel.getNextHighestDepth());
			//var mvPhoto:MovieClip = mvPanel.attachMovie('mvLoader', 'LOADER_PHOTO', mvPanel.getNextHighestDepth());
			
			//photo loader
			var objPhotoLoader:MovieClipLoader = new MovieClipLoader();
			var objPhotoLoaderListener:Object = new Object();
			objPhotoLoaderListener.__mvPanel = mvPanel;
			objPhotoLoaderListener.__mvClipLoader = objPhotoLoader;
			objPhotoLoaderListener.__width = oSize.__width;
			objPhotoLoaderListener.__height = oSize.__height;
			
			//error
			objPhotoLoaderListener.onLoadError = function(mvLoad:MovieClip, errorCode:String, httpStatus:Number):Void{
			    Debug("**ERR_LOADING_PICT(" + errorCode + "):  httpStatus: " + httpStatus);
				this.__mvPanel.attachMovie('mvIconImage_28', 'DEFAULT_PHOTO', this.__mvPanel.getNextHighestDepth());	
				this.__mvClipLoader.removeListener(this);
				delete this;
				};

			//when finish loaded
			objPhotoLoaderListener.onLoadInit = function(mvLoad):Void{
				if(mvLoad._width > this.__width || mvLoad._height > this.__height){
					var iW:Number = mvLoad._width;
					var iH:Number = mvLoad._height;	
					if(iW > this.__width){//si trop large
						var iResize:Number = (iW/this.__width);
						var tmpH:Number = (iH/iResize);
						var tmpW:Number = (iW/iResize);
						if(tmpH > this.__height){//si trop haut
							iResize = (tmpH/this.__height);
							tmpH = (tmpH/iResize);
							tmpW = (tmpW/iResize);
							}
						//mvLoad._height = tmpH;
						//mvLoad._width = tmpW;
							
					//MODIFIED BY AARIZO
					}else if(iH > this.__height){
						var iResize:Number = (iH/this.__height);
						var tmpH:Number = (iH/iResize);
						var tmpW:Number = (iW/iResize);
						if(tmpW > this.__width){//si trop haut
							iResize = (tmpH/this.__width);
							tmpH = (tmpH/iResize);
							tmpW = (tmpW/iResize);
							}
						}
						mvLoad._height = tmpH;
						mvLoad._width = tmpW;
				
				}else{
					var iW:Number = mvLoad._width;
					var iH:Number = mvLoad._height;	

					if(iW < this.__width){
						var iResize:Number = (this.__width/iW); //3
						var tmpH:Number = (iH*iResize);
						var tmpW:Number = (iW*iResize);
						if(tmpH > this.__height){
							iResize = (tmpH/this.__height);
							tmpH = (tmpH/iResize);
							tmpW = (tmpW/iResize);
							}
						mvLoad._height = tmpH;
						mvLoad._width = tmpW;
						}
						
					}
				//center th picture in the panel
				mvLoad._x = (this.__width - mvLoad._width)/2;
				mvLoad._y = (this.__height - mvLoad._height)/2;
				};
			
			//load the clip
			objPhotoLoader.addListener(objPhotoLoaderListener);
			objPhotoLoader.loadClip(photoPath, mvPhoto);	
			}
			
			if(typeof(this.__registeredForResizeEvent['photo']) == 'object'){
				oPanel.__class.removeRegisteredObject(this.__registeredForResizeEvent['photo']);
				this.__registeredForResizeEvent['photo'] = undefined;
				delete this.__registeredForResizeEvent['photo'];
				}
			
			//register for the resize event
			this.__registeredForResizeEvent['photo'] = new Object();
			this.__registeredForResizeEvent['photo'].__objlistener = objPhotoLoaderListener;
			this.__registeredForResizeEvent['photo'].__mvphoto = mvPhoto;
			this.__registeredForResizeEvent['photo'].__super = this;
			this.__registeredForResizeEvent['photo'].resize = function(w:Number, h:Number):Void{
				this.__super.redrawPhoto(this.__objlistener, this.__mvphoto, w, h);
				};
			oPanel.__class.registerObject(this.__registeredForResizeEvent['photo']);		
		};
	
	/******************************************************************************************************************************************/	
	/*	
	public function makeFromXmlNode(Void):Void{
		for(var i=0; i<this.__xmlNode.childNodes.length; i++){
			this.__arrMiniProfil[this.__xmlNode.childNodes[i].attributes.n] =this.__xmlNode.childNodes[i].firstChild.nodeValue;
			}
		};	
	*/	
	}	