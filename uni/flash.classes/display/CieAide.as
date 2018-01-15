/*

section aide

*/


//import control.CiePanel;
//import control.CieTextLine;
//import manager.CieToolsManager;

import control.CieButton;

dynamic class display.CieAide{

	static private var __className = 'CieAide';
	static private var __instance:CieAide;
	private var __hvSpacer:Number;
	
	private var __panelClassTop:Array;
	private var __registeredForResizeEvent:Object;
	
	private var __btnW:Number;
	private var __btnH:Number;

	private function CieAide(Void){
		this.__hvSpacer = 10;
		this.__btnW = 200;
		this.__btnH = 30;
		//ref to the pannel class
		this.__panelClassTop = new Array();		
		this.__registeredForResizeEvent = new Object();
		};
		
	/*************************************************************************************************************************************************/	
	
	static public function getInstance(Void):CieAide{
		if(__instance == undefined) {
			__instance = new CieAide();
			}
		return __instance;
		};	
			
	/*************************************************************************************************************************************************/
	
	public function openSection(ctype:String):Void{
		cContent.openTab(['aide', ctype]);
		};
	
	/*************************************************************************************************************************************************/	
		
	public function changeNode(Void):Void{
		//reloads the node
		cContent.openTab(['aide']);
		};
	
	/*************************************************************************************************************************************************/
	public function refreshSection(ctype:String):Void{

		this.__panelClassTop[ctype] = cContent.getPanelClass(['aide','_tl', ctype,'_tl']);
		
		this.__panelClassTop[ctype].setContent('mvContent');
		var mvPanel = this.__panelClassTop[ctype].getPanelContent();
		
		//register for the resize event
		this.__registeredForResizeEvent[ctype] = new Object();
		this.__registeredForResizeEvent[ctype].__super = this;
		this.__registeredForResizeEvent[ctype].__panel = mvPanel;
		this.__registeredForResizeEvent[ctype].__textboxes = new Array();
		this.__registeredForResizeEvent[ctype].resize = function(w:Number, h:Number):Void{
			this.__super.redrawMultipleTextBox(this.__panel, this.__textboxes, w);
			};
		this.__panelClassTop[ctype].registerObject(this.__registeredForResizeEvent[ctype]);
	
		//construire texte
		var mvTexte = mvPanel.attachMovie('mvAide', 'TEXTE_AIDE_' + ctype, mvPanel.getNextHighestDepth());
		
		//positionning
		mvTexte.txtInfos.autoSize = 'left';	
		mvTexte._x = this.__hvSpacer;
		mvTexte._y = this.__hvSpacer;
		//title
		if(ctype == 'apropos'){
			mvTexte.txtInfos.htmlText = gLang[114] + '\n\n<b>Updates: ' + cRegistry.getKey('version') + '</b>';
		}else if(ctype == 'faq'){
			mvTexte.txtInfos.htmlText = gLang[115];
			var btn = new CieButton(mvPanel, gLang[329], this.__btnW, this.__btnH, 0, 0);
			//actions
			btn.getMovie().onRelease = function(Void):Void{
				var arrUser = new Array();
				arrUser['pseudo'] = BC.__user.__pseudo;
				arrUser['no_publique'] = BC.__user.__nopub;
				cFunc.openSiteRedirectionBox('faq', arrUser);
				};
			this.__registeredForResizeEvent[ctype].__textboxes.push(btn);	
		}else if(ctype == 'termes_conditions'){
			mvTexte.txtInfos.htmlText = gLang[116];
			var btn = new CieButton(mvPanel, gLang[330], this.__btnW, this.__btnH, 0, 0);
			//actions
			btn.getMovie().onRelease = function(Void):Void{
				var arrUser = new Array();
				arrUser['pseudo'] = BC.__user.__pseudo;
				arrUser['no_publique'] = BC.__user.__nopub;
				cFunc.openSiteRedirectionBox('terms', arrUser);
				};
			this.__registeredForResizeEvent[ctype].__textboxes.push(btn);	
			}
		
		//register text box in reverse order of apearance
		this.__registeredForResizeEvent[ctype].__textboxes.push(mvTexte);
		
		};	

		
	/*************************************************************************************************************************************************/
	
		//resize the text box the scrool and the background
	private function redrawMultipleTextBox(mvPanel:MovieClip, arrTexte:Array, w:Number):Void{
		//clear the drawing
		mvPanel.clear();
		var iPanelScrollWidth = 16;
		//Ypos
		var tmpHeight:Number = this.__hvSpacer;
		//loop trough all textBoxes
		for(var o in arrTexte){
			if(arrTexte[o].getClassName() == 'CieButton'){
				//its a button then draw is different
				arrTexte[o].redraw(this.__hvSpacer,  tmpHeight);
				tmpHeight += Number(arrTexte[o].getMovie()._height + (this.__hvSpacer * 2)); 
			}else{
				arrTexte[o]._y = tmpHeight;
				//place the width to follow the Panel
				arrTexte[o].txtInfos._width = w - this.__hvSpacer - iPanelScrollWidth;
				//new Height
				tmpHeight += arrTexte[o].txtInfos._height + arrTexte[o].txtInfos._y  + (this.__hvSpacer * 2);
				}
			}
		};
		
	/*************************************************************************************************************************************************/
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieAide{
		return this;
		};
	*/	
	}	