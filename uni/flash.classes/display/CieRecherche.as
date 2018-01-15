/*

section Bottin->carnet et Bottin->blocked

*/

import display.CieListing;
//import control.CiePanel;
import manager.CieToolsManager;
import messages.CieOptionMessages;
import messages.CieTextMessages;
import messages.CiePromptMessages;
import control.CieBubble;

import flash.filters.GlowFilter;


dynamic class display.CieRecherche{

	static private var __className = 'CieRecherche';
	
	static private var __instance:CieRecherche;
	
	public var __rowPerPage:Number;
	
	private var __pageIN:Array;
	private var __reqArgs:Array;
	private var __sortOrder:Array;
	private var __panelClassTop:Array;
	private var __panelClassBottom:Array;
	private var __cListing:Array;
	private var __cToolManager:Array;
	
	private var __arrSearchName:Array;
	private var __cMessageBox:Object;
	
	//for corner butt
	private var __panelClassContainer:Object;
	private var __cornerButt:MovieClip;

	
	private function CieRecherche(Void){
		this.__rowPerPage = 10;
		//the req args for page navigation
		this.__reqArgs = new Array();
		this.__reqArgs['rapide'] = '';
		this.__reqArgs['detaillees'] = '';
		//this.__reqArgs['photo'] = '';
		this.__reqArgs['pseudo'] = '';
		//the page we are in
		this.__pageIN = new Array();
		this.__pageIN['rapide'] = 0;
		this.__pageIN['detaillees'] = 0;
		//this.__pageIN['photo'] = 0;
		this.__pageIN['pseudo'] = 0;
		//sort ordre		
		this.__sortOrder = new Array();
		this.__sortOrder['rapide'] = 'pseudo';
		this.__sortOrder['detaillees'] = 'pseudo';
		//this.__sortOrder['photo'] = 'pseudo';
		this.__sortOrder['pseudo'] = 'pseudo';
		//ref to the pannel class
		this.__panelClassTop = new Array();		
		this.__panelClassBottom = new Array();		
		//ref to the listing Class
		this.__cListing = new Array();
		//ref to the toolmanager class
		this.__cToolManager = new Array();
		};
		
	static public function getInstance(Void):CieRecherche{
		if(__instance == undefined) {
			__instance = new CieRecherche();
			}
		return __instance;
		};	
		
	/**************************************************************************************************************/		
	
	public function reset(Void):Void{
		//the req args for page navigation
		this.__reqArgs = new Array();
		this.__reqArgs['rapide'] = '';
		this.__reqArgs['detaillees'] = '';
		//this.__reqArgs['photo'] = '';
		this.__reqArgs['pseudo'] = '';
		//the page we are in
		this.__pageIN = new Array();
		this.__pageIN['rapide'] = 0;
		this.__pageIN['detaillees'] = 0;
		//this.__pageIN['photo'] = 0;
		this.__pageIN['pseudo'] = 0;
		//sort ordre		
		this.__sortOrder = new Array();
		this.__sortOrder['rapide'] = 'pseudo';
		this.__sortOrder['detaillees'] = 'pseudo';
		//this.__sortOrder['photo'] = 'pseudo';
		this.__sortOrder['pseudo'] = 'pseudo';
		//ref to the pannel class
		this.__panelClassTop = new Array();		
		this.__panelClassBottom = new Array();		
		//ref to the listing Class
		this.__cListing = new Array();
		//ref to the toolmanager class
		this.__cToolManager = new Array();
		//delte
		delete this.__panelClassContainer;
		};
		
	/*************************************************************************************************************************************************/	
		
	public function removeRegisteredObject(Void):Void{
		//unregistered the tabManager previously registered when opening a DelatikedProfil
		var pObj:Object = cContent.getPanelObject(['recherche','_tr']);
		pObj.__class.removeRegisteredObject(pObj.__tabManager);
		
		//clean tool manager
		this.__cToolManager['rapide'] = undefined;
		this.__cToolManager['detaillees'] = undefined;
		//this.__cToolManager['photo'] = undefined;
		this.__cToolManager['pseudo'] = undefined;
		//clear panel ref top
		this.__panelClassTop['rapide'] = undefined;
		this.__panelClassTop['detaillees'] = undefined;
		//this.__panelClassTop['photo'] = undefined;
		this.__panelClassTop['pseudo'] = undefined;
		//clear panel ref bottom
		this.__panelClassBottom['rapide'] = undefined;
		this.__panelClassBottom['detaillees'] = undefined;
		//this.__panelClassBottom['photo'] = undefined;
		this.__panelClassBottom['pseudo'] = undefined;
		//listing
		this.__cListing['rapide'] = undefined;
		this.__cListing['detaillees'] = undefined;
		//this.__cListing['photo'] = undefined;
		this.__cListing['pseudo'] = undefined;
				
		};
		
	/*************************************************************************************************************************************************/	
		
	public function changeNode(Void):Void{
		//change the nodeXML value
		var strXml:String = '<P n="_tr" content="mvContent" bgcolor="0xEDEDED" scroll="false"></P>';
		cContent.changeNodeValue(new XML(strXml), ['recherche','_tr']);
		//reloads the node
		cContent.openTab(['recherche']);
		};
		
	/*************************************************************************************************************************************************/
	
	public function openSection(ctype:String):Void{
		cContent.openTab(['recherche', ctype]);
		//top right corner butt for resize
		this.drawMinimizeCorner();
		};
		
	/*************************************************************************************************************************************************/
	
	public function drawMinimizeCorner(Void):Void{
		//put the maximize arrow 
		//get the panel containing the tabs
		this.__panelClassContainer = cContent.getPanelClass(['recherche','_tl']);
		this.__cornerButt = this.__panelClassContainer.getPanelContent().createEmptyMovieClip('mvCornerButt', this.__panelClassContainer.getPanelContent().getNextHighestDepth());
		this.__cornerButt = this.__cornerButt.attachMovie('mvIconMinimize', 'mvIconMinimizeCorner', this.__cornerButt.getNextHighestDepth());
		this.__cornerButt._xscale = this.__cornerButt._yscale = 80;
		this.__cornerButt._y = CieStyle.__tabPanel.__tabBorderOffSet;
		this.__cornerButt._x = (this.__panelClassContainer.getPanelSize().__width - this.__cornerButt._width) - CieStyle.__tabPanel.__tabBorderOffSet;
		
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
			cFunc.resizeForm();
			};
		
		this.__cornerButt.filters = new Array(new GlowFilter(CieStyle.__panel.__effectGlowColor, 0.3,5,5,2,2,false,false));
		};
			
	/*************************************************************************************************************************************************/	
	
	public function changePage(iIncrement:Number, ctype:String):Void{
		this.__pageIN[ctype] += iIncrement;
		//check not to go lower then page = 0
		if(this.__pageIN[ctype] < 0){
			this.__pageIN[ctype] = 0;
		}else{
			this.buildRequest(this.__reqArgs[ctype], ctype);	
			}
		};
	
	/*************************************************************************************************************************************************/
	
	public function setSortOrder(strSort:String, ctype:String):Void{
		this.__sortOrder[ctype] = strSort;
		};
		
	/*************************************************************************************************************************************************/	
	
	public function buildRequest(strQueryString:String, ctype:String):Void{
		//keep the args for paging navigation
		this.__reqArgs[ctype] = strQueryString;
		//put the loader movie on the top panel
		this.__panelClassTop[ctype].setContent('mvLoaderAnimated');
		this.__panelClassTop[ctype].setBgColor(CieStyle.__profil.__bgPanelListingLoad);
		//remove bottom navigation
		if(this.__cToolManager[ctype] != undefined){
			this.__cToolManager[ctype].removeAllTools();
			delete this.__cToolManager[ctype];
			}
		//build request
		var arrD = new Array();
		arrD['methode'] = 'recherche';
		arrD['action'] = ctype;
		//build the arguments
		arrD['arguments'] = strQueryString + '|' + this.__pageIN[ctype] + ',' + this.__rowPerPage;
		//add the request
		//Debug("REQUEST[" + ctype + "]: " + arrD['arguments']);
		cReqManager.addRequest(arrD, this.cbRecherche, {__class:this, __ctype:ctype});	
		};
	
	/*************************************************************************************************************************************************/
	
	//callBack function for the pseudo recherche
	public function cbRecherche(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		// show the result
		obj.__super.__class.showPageListing(obj.__req.getXml(), obj.__super.__ctype);
		//remove request
		cReqManager.removeRequest(obj.__req.getID());
		};	
	
	/*************************************************************************************************************************************************/
	
	//show the display of the result
	public function showPageListing(xml:XML, ctype:String):Void{
		//check if there is data
		var bError:Boolean = false;
		var bAboRedirect:Boolean = false;
		for(var i=0; i<xml.firstChild.childNodes.length; i++){
			var currNode = xml.firstChild.childNodes[i];
			if(currNode.attributes.n == 'error'){
				//check le type d'erreur
				if(currNode.firstChild.nodeValue == 'ERROR_RECHERCHE_RIGHTS'){
					bAboRedirect = true;
				}else{
					bError = true;
					}
				break;	
				}
			}
		if(bError){
			//put back the form
			//tell the user we have no return from  his query
			new CieTextMessages('MB_OK', gLang[225], gLang[226]);
			this.refreshSection(ctype);
		}else if(bAboRedirect){
			//abonement popup
			cFunc.chatAbonnement();
			this.refreshSection(ctype);
		}else{
			//put a content to write to it 
			this.__panelClassTop[ctype].setContent('mvContent');
				this.__panelClassTop[ctype].setBgColor(CieStyle.__profil.__bgPanelListing);
			//si deja instancie
			if(this.__cListing[ctype] == undefined){
				this.__cListing[ctype] = new CieListing(this.__panelClassTop[ctype].getPanelContent(), 'recherche');
				}
			//create from the xml coded up
			this.__cListing[ctype].createFromXmlNode(xml.firstChild);
			//put the scroollbar at the top
			this.__panelClassTop[ctype].placeScrollBar(0);
			//bottom navigation
			if(this.__cToolManager[ctype] == undefined){
				//this.showBottomNavigation(ctype);
				this.showBasicBottomNavigation(ctype, true);
				}
			}
		};
	
	/*************************************************************************************************************************************************/	
	//recherche directement un pseudo
	public function directPseudoSearch(strPseudo:String):Void{
		Debug('directPseudoSearch: ' + strPseudo);
		this.buildRequest(strPseudo + ',1', 'pseudo'); //1 is for exact search
		};
	
	/*************************************************************************************************************************************************/	
	//this is to make a direct serach without filling or dowloading the form	
	public function refreshDirectPseudoSection(Void):Void{	
		var ctype = 'pseudo';
		//reset the tmp args
		this.__reqArgs[ctype] = '';
		//set page to first one
		this.__pageIN[ctype] = 0;
		//get the panel class
		this.__panelClassTop[ctype] = cContent.getPanelClass(['recherche','_tl',ctype,'_tl']);
		this.__panelClassBottom[ctype] = cContent.getPanelClass(['recherche','_tl',ctype,'_bl']);
		};
	
	/*************************************************************************************************************************************************/	
		
	public function refreshSection(ctype:String, strRequestArgs:String):Void{
		//reset the tmp args
		this.__reqArgs[ctype] = '';
		
		//set page to first one
		this.__pageIN[ctype] = 0;
		
		//remove bottom navigation
		if(this.__cToolManager[ctype] != undefined){
			this.__cToolManager[ctype].removeAllTools();
			delete this.__cToolManager[ctype];
			}
		
		//get the panel class
		this.__panelClassTop[ctype] = cContent.getPanelClass(['recherche','_tl',ctype,'_tl']);
		this.__panelClassBottom[ctype] = cContent.getPanelClass(['recherche','_tl',ctype,'_bl']);
		
		//put the loader
		this.__panelClassTop[ctype].setContent('mvLoaderAnimated');
		this.__panelClassTop[ctype].setBgColor(CieStyle.__profil.__bgPanelListingLoad);
		
		if(this.__cToolManager[ctype] == undefined){
			this.__panelClassBottom[ctype].setContent(CieStyle.__basic.__bgMvBottomNavigation);
			}
		
		//show the form
		if(strRequestArgs != undefined && strRequestArgs != ''){
			cFormManager.afficheTemplate('recherche', ctype, this.__panelClassTop[ctype], strRequestArgs);
		}else{
			cFormManager.afficheTemplate('recherche', ctype, this.__panelClassTop[ctype]);
			}
		//show the basic navigation at the bottom, refresh butt
		if(this.__cToolManager[ctype] == undefined){
			this.showBasicBottomNavigation(ctype, false);
			}
		};	
		
	/*************************************************************************************************************************************************/		
		
	public function showBasicBottomNavigation(ctype:String, showNav:Boolean):Void{
		//tootlbar
		var strXML:String = '<UNITOOLBAR>';
		//recherche detaille has extra button to load/save search
		if(ctype == 'detaillees'){
			strXML +='<TOOLGROUP n="actions" align="left">';
				if(!showNav){	
					strXML += '<TOOL n="save" type="32X32" icon="mvIconImage_20"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[318] + '"></BUBBLE></TOOL>';
					}		
			strXML += '<TOOL n="load" type="32X32" icon="mvIconImage_21"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[319] + '"></BUBBLE></TOOL>' +
				'<TOOL n="delete" type="32X32" icon="mvIconImage_23"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[311] + '"></BUBBLE></TOOL>' +
				'<TOOL n="refresh" type="32X32" icon="mvIconImage_27"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[313] + '"></BUBBLE></TOOL>' +
				'</TOOLGROUP>';
		}else{
			strXML += '<TOOLGROUP n="actions" align="left">' +
				'<TOOL n="refresh" type="32X32" icon="mvIconImage_27"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[313] + '"></BUBBLE></TOOL>' +
				'</TOOLGROUP>';
			}
		

		//navigation or send button	
		if(showNav){	
			strXML += '<TOOLGROUP n="navigation" align="right">' + 
				'<TOOL n="next" type="40X40" icon="mvIconImage_3"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[314] + '"></BUBBLE></TOOL>';
			if(this.__pageIN[ctype]){
				strXML += '<TOOL n="previous" type="40X40" icon="mvIconImage_2"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[315] + '"></BUBBLE></TOOL>';
				}
			strXML += '</TOOLGROUP>';	
		}else{	
			strXML += '<TOOLGROUP n="form" align="right">' +
				'<BUTTON n="send" type="80X32" text="' + gLang[227] + '" style="' + CieStyle.__basic.__toolColor + ',' + CieStyle.__basic.__toolBorderWidth + ',' + CieStyle.__basic.__borderToolColor + ',' + CieStyle.__basic.__toolEffectColor + ',' + CieStyle.__basic.__toolEffectColorOff + ',' + CieStyle.__basic.__buttonToolFontColor + '"><ACTION n="onrollover" func=""></ACTION></BUTTON>' +
				'</TOOLGROUP>';
			}			
		strXML += '</UNITOOLBAR>';
			
			
		//XML
		var xmlTool:XML = new XML(strXML);	
		
		this.__cToolManager[ctype] = new CieToolsManager(this.__panelClassBottom[ctype].getPanelContent(), this.__panelClassBottom[ctype].getPanelSize().__width, 65, 0, 0);
		this.__cToolManager[ctype].createFromXml(xmlTool.firstChild);
		
		//action on the extra button
		if(ctype == 'detaillees'){
			//load action
			this.__cToolManager[ctype].getIcon('actions', 'load').__class = this;
			this.__cToolManager[ctype].getIcon('actions', 'load').__ctype = ctype;
			this.__cToolManager[ctype].getIcon('actions', 'load').onRelease = function(Void):Void{
				//for bubble text usage
				if(this.__bubbletext != undefined){
					this.__bubble.destroy();
					}
				this.__class.fetchSearchName(this.__ctype, false);
				};
			if(!showNav){
				//save action
				this.__cToolManager[ctype].getIcon('actions', 'save').__class = this;
				this.__cToolManager[ctype].getIcon('actions', 'save').__ctype = ctype;
				this.__cToolManager[ctype].getIcon('actions', 'save').onRelease = function(Void):Void{
					//for bubble text usage
					if(this.__bubbletext != undefined){
						this.__bubble.destroy();
						}
					this.__class.openSaveSearchNameBox(this.__ctype);
					};
				}	
			//delte action
			this.__cToolManager[ctype].getIcon('actions', 'delete').__class = this;
			this.__cToolManager[ctype].getIcon('actions', 'delete').__ctype = ctype;
			this.__cToolManager[ctype].getIcon('actions', 'delete').onRelease = function(Void):Void{
				//for bubble text usage
				if(this.__bubbletext != undefined){
					this.__bubble.destroy();
					}
				this.__class.fetchSearchName(this.__ctype, true);
				};			
			}
		
		//refresh action
		this.__cToolManager[ctype].getIcon('actions', 'refresh').__class = this;
		this.__cToolManager[ctype].getIcon('actions', 'refresh').__ctype = ctype;
		this.__cToolManager[ctype].getIcon('actions', 'refresh').onRelease = function(Void):Void{
			//for bubble text usage
			if(this.__bubbletext != undefined){
				this.__bubble.destroy();
				}
			this.__class.refreshSection(this.__ctype);
			};
		
		if(showNav){	
			//nav action
			this.__cToolManager[ctype].getIcon('navigation', 'next').__class = this;
			this.__cToolManager[ctype].getIcon('navigation', 'next').__ctype = ctype;
			this.__cToolManager[ctype].getIcon('navigation', 'next').onRelease = function(Void):Void{
				//for bubble text usage
				if(this.__bubbletext != undefined){
					this.__bubble.destroy();
					}
				this.__class.changePage(1, this.__ctype);
				};
			//nav action
			if(this.__pageIN[ctype]){
				this.__cToolManager[ctype].getIcon('navigation', 'previous').__class = this;
				this.__cToolManager[ctype].getIcon('navigation', 'previous').__ctype = ctype;
				this.__cToolManager[ctype].getIcon('navigation', 'previous').onRelease = function(Void):Void{
					//for bubble text usage
					if(this.__bubbletext != undefined){
						this.__bubble.destroy();
						}
					this.__class.changePage(-1, this.__ctype);
					};
				}			
		}else{
			//recherche action
			this.__cToolManager[ctype].getIcon('form', 'send').__ctype = ctype;
			this.__cToolManager[ctype].getIcon('form', 'send').onRelease = function(Void):Void{
				cFormManager.sendForm(this.__ctype);
				};
			}
		this.__panelClassBottom[ctype].registerObject(this.__cToolManager[ctype]);
		};

	
	/*************************************************************************************************************************************************/		
	/*	
	public function showBottomNavigation(ctype:String):Void{
		//tootlbar
		var strXML:String = '';
		strXML += '<UNITOOLBAR>';
		strXML += '<TOOLGROUP n="actions" align="left">';
		strXML += '<TOOL n="refresh" type="32X32" icon="mvIconImage_27"><ACTION n="onrollover" func=""></ACTION></TOOL>';
		strXML += '</TOOLGROUP>';
		strXML += '<TOOLGROUP n="navigation" align="right">';
		strXML += '<TOOL n="next" type="40X40" icon="mvIconImage_3"><ACTION n="onrollover" func=""></ACTION></TOOL>';
		if(this.__pageIN[ctype]){
			strXML += '<TOOL n="previous" type="40X40" icon="mvIconImage_2"><ACTION n="onrollover" func=""></ACTION></TOOL>';
			}
		strXML += '</TOOLGROUP>';
		strXML += '</UNITOOLBAR>';
		//XML
		var xmlTool:XML = new XML(strXML);
		
		this.__cToolManager[ctype] = new CieToolsManager(this.__panelClassBottom[ctype].getPanelContent(), this.__panelClassBottom[ctype].getPanelSize().__width, 65, 0, 0);
		this.__cToolManager[ctype].createFromXml(xmlTool.firstChild);
		//refresh action
		this.__cToolManager[ctype].getIcon('actions', 'refresh').__class = this;
		this.__cToolManager[ctype].getIcon('actions', 'refresh').__ctype = ctype;
		this.__cToolManager[ctype].getIcon('actions', 'refresh').onRelease = function(Void):Void{
			this.__class.refreshSection(this.__ctype);
			};
		//nav action
		this.__cToolManager[ctype].getIcon('navigation', 'next').__class = this;
		this.__cToolManager[ctype].getIcon('navigation', 'next').__ctype = ctype;
		this.__cToolManager[ctype].getIcon('navigation', 'next').onRelease = function(Void):Void{
			this.__class.changePage(1, this.__ctype);
			};
		//nav action
		if(this.__pageIN[ctype]){
			this.__cToolManager[ctype].getIcon('navigation', 'previous').__class = this;
			this.__cToolManager[ctype].getIcon('navigation', 'previous').__ctype = ctype;
			this.__cToolManager[ctype].getIcon('navigation', 'previous').onRelease = function(Void):Void{
				this.__class.changePage(-1, this.__ctype);
				};
			}			
		this.__panelClassBottom[ctype].registerObject(this.__cToolManager[ctype]);
		};
	*/	
	/*************************************************************************************************************************************************/
	
	//open the popup
	private function openSaveSearchNameBox(ctype:String):Void{
		this.__cMessageBox = new CiePromptMessages(gLang[228], gLang[229]);
		this.__cMessageBox.setCallBackFunction(this.cbSaveSearchNameInput, {__class: this, __ctype:ctype});
		};
		
	/*************************************************************************************************************************************************/	
		
	//callback when user make a choice of selection	
	public function cbSaveSearchNameInput(cbObject:Object):Void{
		//recfresh the section and passe the searchName
		if(cbObject.__ok == true){
			cbObject.__class.saveSearchName(cbObject.__ctype,  cbObject.__class.__cMessageBox.getInputText());
			}
		//delete the var holding the CieOptionMessages
		cbObject.__class.__cMessageBox = null;
		};	
		
	/*************************************************************************************************************************************************/
	
	//save the new serach name with request
	private function saveSearchName(ctype:String, strSearchName:String):Void{
		//build request
		if(strSearchName != undefined && strSearchName != ''){
			var strFormData:String = cFormManager.getFormData(ctype);
			if(strFormData != undefined && strFormData != ''){
				var arrD = new Array();
				arrD['methode'] = 'recherche';
				arrD['action'] = 'savesearchname';
				//build the arguments
				arrD['arguments'] = strFormData + '|' + strSearchName;
				//add the request
				//Debug("REQUEST[" + ctype + "]: " + arrD['arguments']);
				cReqManager.addRequest(arrD, this.cbSaveSearchName, {__class:this, __ctype:ctype});	
				}
			}	
		};
	
	/*************************************************************************************************************************************************/
	
	//callback when the seacrh is saved
	public function cbSaveSearchName(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//remove request
		cReqManager.removeRequest(obj.__req.getID());
		};		
		
	/*************************************************************************************************************************************************/
	
	//fecth the serach name
	private function fetchSearchName(ctype:String, bRemove:Boolean):Void{
		//build a request to get the serachName
		var arrD = new Array();
		arrD['methode'] = 'recherche';
		arrD['action'] = 'getsearchname';
		//build the arguments
		arrD['arguments'] = '';
		//add the request
		cReqManager.addRequest(arrD, this.cbSearchName, {__class:this, __ctype:ctype, __bremove:bRemove});	
		};

	/*************************************************************************************************************************************************/		
	
	//callback of the fetchSearchName
	public function cbSearchName(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//reset the array
		obj.__super.__class.__arrSearchName = new Array();
		//parse
		obj.__super.__class.parseXmlSearchName(obj.__req.getXml());
		//show the message
		if(obj.__super.__bremove){
			obj.__super.__class.showRemoveSavedSearch(obj.__super.__ctype);
		}else{
			obj.__super.__class.showSavedSearch(obj.__super.__ctype);
			}
		//remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	/*************************************************************************************************************************************************/	
	
	//parse the serahName return
	private function parseXmlSearchName(xmlNode:XMLNode):Void{
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.firstChild.nodeType == 1){
				this.parseXmlSearchName(currNode);
			}else{
				if(currNode.attributes.n == 'error'){
					this.__arrSearchName = undefined;
					//error so let the user know
					this.__cMessageBox = new CieTextMessages('MB_OK', gLang[230], gLang[231]);
				}else{
					var k:String = unescape(String(currNode.firstChild.nodeValue));
					this.__arrSearchName.push([k,k]);
					}
				}
			}
		};	
	
	/*************************************************************************************************************************************************/
	
	//show popup with serahName
	private function showSavedSearch(ctype:String):Void{
		if(this.__arrSearchName != undefined && this.__arrSearchName.length != 0){
			//no error then show the choices
			this.__cMessageBox = new CieOptionMessages(gLang[232], this.__arrSearchName, gLang[233]);
			this.__cMessageBox.setSelectionValue(0);
			this.__cMessageBox.setCallBackFunction(this.cbSearchNameSelected, {__class: this, __ctype:ctype});
			}
		};
	
	/*************************************************************************************************************************************************/
	
	//show popup with serahName for deletation
	private function showRemoveSavedSearch(ctype:String):Void{
		if(this.__arrSearchName != undefined && this.__arrSearchName.length != 0){
			//no error then show the choices
			this.__cMessageBox = new CieOptionMessages(gLang[234], this.__arrSearchName, gLang[235]);
			this.__cMessageBox.setSelectionValue(0);
			this.__cMessageBox.setCallBackFunction(this.cbRemoveSearchNameSelected, {__class: this, __ctype:ctype});
			}
		};	
	
	/*************************************************************************************************************************************************/
	
	//callback when user make a choice of selection	
	public function cbSearchNameSelected(cbObject:Object):Void{
		//recfresh the section and passe the searchName
		if(cbObject.__ok == true){
			cbObject.__class.refreshSection(cbObject.__ctype,  cbObject.__class.__arrSearchName[cbObject.__class.__cMessageBox.getSelectedChoice()][0]);
			}
		//delete the var holding the CieOptionMessages
		cbObject.__class.__cMessageBox = null;
		};
	
	/*************************************************************************************************************************************************/
	
	//callback when user make a choice of selection for deletation
	public function cbRemoveSearchNameSelected(cbObject:Object):Void{
		//recfresh the section and passe the searchName
		if(cbObject.__ok == true){
			cbObject.__class.removeSearchName(cbObject.__ctype,  cbObject.__class.__arrSearchName[cbObject.__class.__cMessageBox.getSelectedChoice()][0]);
			}
		//delete the var holding the CieOptionMessages
		cbObject.__class.__cMessageBox = null;
		};	

	/*************************************************************************************************************************************************/
		
	//remove serach name
	public function removeSearchName(ctype:String, strSearchName:String):Void{
		//build request
		var arrD = new Array();
		arrD['methode'] = 'recherche';
		arrD['action'] = 'removesearchname';
		//build the arguments
		arrD['arguments'] = strSearchName;
		//add the request
		if(arrD['arguments'] != ''){
			//Debug("REQUEST[" + ctype + "]: " + arrD['arguments']);
			cReqManager.addRequest(arrD, this.cbRemoveSearchName, {__class:this, __ctype:ctype});	
			}
		};
	
	/*************************************************************************************************************************************************/
	
	//callback when the seacrh is deleted	
	public function cbRemoveSearchName(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		//reset the array
		obj.__super.__class.__arrSearchName = new Array();
		//remove request
		cReqManager.removeRequest(obj.__req.getID());
		};	
	
	/*************************************************************************************************************************************************/
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieRecherche{
		return this;
		};
	*/	
	}	