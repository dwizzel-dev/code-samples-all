/*

section Message->
			instant
			express
			courrier
			vocal
			
maintenant:
section Message->
			communications
			quiaconsulte
*/

import display.CieListing;
//import control.CiePanel;
import manager.CieToolsManager;
import control.CieBubble;

import flash.filters.GlowFilter;

dynamic class display.CieMessage{

	static private var __className = 'CieMessage';
	
	static private var __instance:CieMessage;
	
	private var __rowPerPage:Number;
	
	private var __pageIN:Array;
	private var __sortOrder:Array;
	private var __panelClassTop:Array;
	private var __panelClassBottom:Array;
	private var __cListing:Array;
	private var __chkBoxState:Array;
	private var __cToolManager:Array;
	private var __bRefreshIsBlinking:Boolean;
	
	//for corner butt
	private var __panelClassContainer:Object;
	private var __cornerButt:MovieClip;

	private function CieMessage(Void){
		//the checkBoxes
		this.__chkBoxState = new Array();
		this.__chkBoxState['communications'] = '0';
		//row per page
		this.__rowPerPage = 8;
		//the page we are in
		this.__pageIN = new Array();
		this.__pageIN['communications'] = 0;
		this.__pageIN['quiaconsulte'] = 0;
		//sort ordre		
		this.__sortOrder = new Array();
		this.__sortOrder['communications'] = 'cdate';
		this.__sortOrder['quiaconsulte'] = 'members.msg_quiaconsulte_date DESC';
		//ref to the pannel class
		this.__panelClassTop = new Array();		
		this.__panelClassBottom = new Array();		
		//ref to the listing Class
		this.__cListing = new Array();
		//ref to the toolmanager class
		this.__cToolManager = new Array();
		//refrsh butt blink flag
		this.__bRefreshIsBlinking = false;
		};
		
	static public function getInstance(Void):CieMessage{
		if(__instance == undefined) {
			__instance = new CieMessage();
			}
		return __instance;
		};	
	
	/**************************************************************************************************************/		
	
	public function reset(Void):Void{
		//the checkBoxes
		this.__chkBoxState = new Array();
		this.__chkBoxState['communications'] = '0';
		//the page we are in
		this.__pageIN = new Array();
		this.__pageIN['communications'] = 0;
		this.__pageIN['quiaconsulte'] = 0;
		//sort ordre		
		this.__sortOrder = new Array();
		this.__sortOrder['communications'] = 'cdate';
		this.__sortOrder['quiaconsulte'] = 'members.msg_quiaconsulte_date DESC';
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
		var pObj:Object = cContent.getPanelObject(['message','_tr']);
		pObj.__class.removeRegisteredObject(pObj.__tabManager);
		
		//clean tool manager
		this.__cToolManager['communications'] = undefined;
		this.__cToolManager['quiaconsulte'] = undefined;
		//clear panel ref
		this.__panelClassTop['communications'] = undefined;
		this.__panelClassTop['quiaconsulte'] = undefined;
		this.__panelClassBottom['communications'] = undefined;
		this.__panelClassBottom['quiaconsulte'] = undefined;
		//listing
		this.__cListing['communications'] = undefined;
		this.__cListing['quiaconsulte'] = undefined;
		};
		
	/*************************************************************************************************************************************************/	
		
	public function changeNode(Void):Void{
		//change the nodeXML value
		var strXml:String = '<P n="_tr" content="mvContent" bgcolor="0xEDEDED" scroll="false"></P>';
		cContent.changeNodeValue(new XML(strXml), ['message','_tr']);
		//reloads the node
		cContent.openTab(['message']);
		};
		
	/*************************************************************************************************************************************************/
	
	public function openSection(ctype:String):Void{
		cContent.openTab(['message', ctype]);
		//top right corner butt for resize
		this.drawMinimizeCorner();
		};
	
	/*************************************************************************************************************************************************/

	public function drawMinimizeCorner(Void):Void{
		//put the maximize arrow 
		//get the panel containing the tabs
		this.__panelClassContainer = cContent.getPanelClass(['message','_tl']);
		this.__cornerButt = this.__panelClassContainer.getPanelContent().createEmptyMovieClip('mvCornerButt', this.__panelClassContainer.getPanelContent().getNextHighestDepth());
		this.__cornerButt = this.__cornerButt.attachMovie('mvIconMinimize', 'mvIconMinimizeCorner', this.__cornerButt.getNextHighestDepth());
		this.__cornerButt._xscale = this.__cornerButt._yscale = 80;
		this.__cornerButt._y = CieStyle.__tabPanel.__tabBorderOffSet;
		this.__cornerButt._x = (this.__panelClassContainer.getPanelSize().__width - this.__cornerButt._width) - CieStyle.__tabPanel.__tabBorderOffSet;
		
		this.__cornerButt.__bubble = null;
		this.__cornerButt.onRollOver = function(Void):Void{
			this.__bubble = new CieBubble(__stage, 100, 150, 5, '', gLang[323]);
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
			this.showPageListing(ctype);
			}
		};
	
	/*************************************************************************************************************************************************/
	
	public function setSortOrder(strSort:String, ctype:String):Void{
		this.__sortOrder[ctype] = strSort;
		};
		
	/*************************************************************************************************************************************************/	
		
	public function refreshSection(ctype:String):Void{
		//checkBox state 0,1,2
		this.__chkBoxState[ctype] = '0';		
		
		//set page to first one
		this.__pageIN[ctype] = 0;
		
		//get the panel class
		this.__panelClassTop[ctype] = cContent.getPanelClass(['message','_tl',ctype,'_tl']);
		this.__panelClassBottom[ctype] = cContent.getPanelClass(['message','_tl',ctype,'_bl']);
		
		//put the loader
		this.__panelClassTop[ctype].setContent('mvContent');
		if(this.__cToolManager[ctype] == undefined){
			this.__panelClassBottom[ctype].setContent(CieStyle.__basic.__bgMvBottomNavigation);
			}
		
		//build the tmp table for the section
		if(ctype == 'communications'){
			cDbManager.changeTmpTableCommunications(this.__sortOrder[ctype]);
			//remetre l'icone a son etat precedent
			cSysTray.changeSysTrayIcon('cie_' + BC.__user.__status + '.ico');
			//arreter le refrash de blink
			if(this.__bRefreshIsBlinking){
				this.makeRefreshButtonBlink(false);
				}
			//enlever le blink sur l'icone
			cToolManager.getTool('messages', 'message').blinkEffect(false);	
		}else if(ctype == 'quiaconsulte'){
			var strWhere:String = " WHERE msg_quiaconsulte = '1' ";
			cDbManager.changeTmpTable(ctype, strWhere, this.__sortOrder[ctype]);
			}
		
		//build the page listing
		this.showPageListing(ctype);
		};	
		

	/*************************************************************************************************************************************************/	
		
	private function showPageListing(ctype:String):Void{
		
		//remove bottom navigation
		if(this.__cToolManager[ctype] != undefined){
			this.__cToolManager[ctype].removeAllTools();
			delete this.__cToolManager[ctype];
			}
		
		//checkBox state 0,1,2
		this.__chkBoxState[ctype] = '0';
		
		//get the row jack
		if(ctype == 'communications'){
			var arrRows:Array = cDbManager.getTmpPageRowCommunications(this.__pageIN[ctype], this.__rowPerPage);
		}else{
			var arrRows:Array = cDbManager.getTmpPageRow(this.__pageIN[ctype], this.__rowPerPage, ctype);
			}
			
		//build the xml loop throught the Array of Rows
		var limit:Number = arrRows.length;
		if(limit > this.__rowPerPage){
			limit -= 1;
			var bShowNextButt:Boolean = true;
			}
		
		//build the xml loop throught the Array of Rows
		var strXml:String = '<UNILISTING>';
		for(var o = 0; o < limit; o++){
			strXml += '<R>' +
			'<C n="no_publique">' + arrRows[o][0] + '</C>' +
			'<C n="pseudo">' + arrRows[o][1] + '</C>' +
			'<C n="age">' + arrRows[o][2] + '</C>' +
			'<C n="ville_id">' + arrRows[o][3] + '</C>' +
			'<C n="region_id">' + arrRows[o][4] + '</C>' +
			'<C n="code_pays">' + arrRows[o][5] + '</C>' +
			'<C n="album">' + arrRows[o][6] + '</C>' +
			'<C n="photo">' + arrRows[o][7] + '</C>' +
			'<C n="vocal">' + arrRows[o][8] + '</C>' +
			'<C n="membership">' + arrRows[o][9] + '</C>' +
			'<C n="orientation">' + arrRows[o][10] + '</C>' +
			'<C n="sexe">' + arrRows[o][11] + '</C>' +
			'<C n="titre">' + arrRows[o][12] + '</C>' +
			'<C n="relation">' + arrRows[o][13] + '</C>' +
			'<C n="etat_civil">' + arrRows[o][14] + '</C>' + 
			'<C n="direction">' + arrRows[o][17] + '</C>' +
			'<C n="lu">' + arrRows[o][18] + '</C>';
			
			//additionnal rows for instant messages
			if(ctype == 'communications'){
				strXml += '<C n="msgtype">' + arrRows[o][15] + '</C>'; //dernier type de messages instant, express, etc... de cet usager
				strXml += '<C n="cdate">' + arrRows[o][16] + '</C>'; //date du dernier messages venat de cet usager
				}
			strXml += '</R>';
			}
		strXml += '</UNILISTING>';
		//new XMl
		var newXml:XML = new XML(strXml);
		
		//Debug("XML_LISTING: " + strXml);
		
		
		//si deja instancie
		if(this.__cListing[ctype] == undefined){
			if(ctype == 'communications'){
				this.__cListing[ctype] = new CieListing(this.__panelClassTop[ctype].getPanelContent(), 'communications');
			}else{
				this.__cListing[ctype] = new CieListing(this.__panelClassTop[ctype].getPanelContent(), 'message');
				}
			}
		//create from the xml coded up	
		this.__cListing[ctype].createFromXmlNode(newXml.firstChild);
		//put the scroollbar at the top
		this.__panelClassTop[ctype].placeScrollBar(0);	
		//bottom navigation
		if(this.__cToolManager[ctype] == undefined){
			this.showBottomNavigation(ctype, bShowNextButt);
			}
		};
	
	/*************************************************************************************************************************************************/	
	
	//select all check box of the current listing
	public function selectAllCheckBox(ctype:String):Void{
		if(this.__chkBoxState[ctype] == '0'){
			this.__chkBoxState[ctype] = '1';
		}else{
			this.__chkBoxState[ctype] = '0';
			}
		this.__cListing[ctype].selectAllCheckBox(this.__chkBoxState[ctype]);	
		};
	

	/*************************************************************************************************************************************************/
	
	public function makeRefreshButtonBlink(bState:Boolean):Void{
		this.__bRefreshIsBlinking = bState;
		this.__cToolManager['communications'].getTool('actions', 'refresh').blinkEffect(this.__bRefreshIsBlinking);
		};
	
	/*************************************************************************************************************************************************/		
	
	//get all checked items (chkBox = 1 or 2)
	public function getCheckedItems(ctype:String):Void{
		var arrChecked:Array = this.__cListing[ctype].getCheckedItems();
		//Debug("CKECHED_(" + ctype + "): " + arrChecked.toString());
		cFunc.removeAllUserMessages(arrChecked);
		};	
		
	//pour le droit de regardf des albums
	public function giveAlbumRights(ctype:String):Void{
		var arrChecked:Array = this.__cListing[ctype].getCheckedItems();
		cFunc.giveAlbumRights(arrChecked, 'message');
		};	
		
	/*************************************************************************************************************************************************/		
		
	public function showBottomNavigation(ctype:String, bNav:Boolean):Void{
		//tootlbar
		var strXML:String = '';
		strXML += '<UNITOOLBAR>';
		strXML += '<TOOLGROUP n="actions" align="left">';
		/*
		if(ctype == 'communications'){
			strXML += '<TOOL n="rights" type="32X32" icon="mvIconImage_19"><ACTION n="onrollover" func=""></ACTION></TOOL>';
			}
		*/	
		strXML += '<TOOL n="delete" type="32X32" icon="mvIconImage_23"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[311] + '"></BUBBLE></TOOL>';
		if(ctype == 'communications'){
			strXML += '<TOOL n="select" type="32X32" icon="mvIconImage_11"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[312] + '"></BUBBLE></TOOL>';
			}
		strXML += '<TOOL n="refresh" type="32X32" icon="mvIconImage_8"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[313] + '"></BUBBLE></TOOL>';
		strXML += '</TOOLGROUP>';
		//prev/next
		if(bNav || this.__pageIN[ctype]){
			//Debug('+++SHOW NAV');
			strXML += '<TOOLGROUP n="navigation" align="right">';
			if(bNav){
				//Debug('+++SHOW NEXT');
				strXML += '<TOOL n="next" type="40X40" icon="mvIconImage_3"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[314] + '"></BUBBLE></TOOL>';
				}
			if(this.__pageIN[ctype]){
				//Debug('+++SHOW PREV');
				strXML += '<TOOL n="previous" type="40X40" icon="mvIconImage_2"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[315] + '"></BUBBLE></TOOL>';
				}
			strXML += '</TOOLGROUP>';
			}
		strXML += '</UNITOOLBAR>';
		//XML
		var xmlTool:XML = new XML(strXML);	
		
		this.__cToolManager[ctype] = new CieToolsManager(this.__panelClassBottom[ctype].getPanelContent(), this.__panelClassBottom[ctype].getPanelSize().__width, 65, 0, 0);
		this.__cToolManager[ctype].createFromXml(xmlTool.firstChild);
		//delete  checked action
		if(ctype == 'communications'){
			this.__cToolManager[ctype].getIcon('actions', 'delete').__class = this;
			this.__cToolManager[ctype].getIcon('actions', 'delete').__ctype = ctype;
			this.__cToolManager[ctype].getIcon('actions', 'delete').onRelease = function(Void):Void{
				//for bubble text usage
				if(this.__bubbletext != undefined){
					this.__bubble.destroy();
					}
				this.__class.getCheckedItems(this.__ctype);
				};
		}else{ //quiaconsulte
			this.__cToolManager[ctype].getIcon('actions', 'delete').__class = this;
			this.__cToolManager[ctype].getIcon('actions', 'delete').__ctype = ctype;
			this.__cToolManager[ctype].getIcon('actions', 'delete').onRelease = function(Void):Void{
				//for bubble text usage
				if(this.__bubbletext != undefined){
					this.__bubble.destroy();
					}
				cFunc.removeAllUserFromQuiAConsulte();
				};
			}
		if(ctype == 'communications'){
			//select all action
			this.__cToolManager[ctype].getIcon('actions', 'select').__class = this;
			this.__cToolManager[ctype].getIcon('actions', 'select').__ctype = ctype;
			this.__cToolManager[ctype].getIcon('actions', 'select').onRelease = function(Void):Void{
				//for bubble text usage
				if(this.__bubbletext != undefined){
					this.__bubble.destroy();
					}
				this.__class.selectAllCheckBox(this.__ctype);
				};	
			//for your aeyes only
			/*
			this.__cToolManager[ctype].getIcon('actions', 'rights').__class = this;
			this.__cToolManager[ctype].getIcon('actions', 'rights').__ctype = ctype;
			this.__cToolManager[ctype].getIcon('actions', 'rights').onRelease = function(Void):Void{
				this.__class.giveAlbumRights(this.__ctype);
				};
			*/	
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
		
		//navigation
		if(bNav || this.__pageIN[ctype]){
			//nav action
			if(bNav){
				this.__cToolManager[ctype].getIcon('navigation', 'next').__class = this;
				this.__cToolManager[ctype].getIcon('navigation', 'next').__ctype = ctype;
				this.__cToolManager[ctype].getIcon('navigation', 'next').onRelease = function(Void):Void{
					//for bubble text usage
					if(this.__bubbletext != undefined){
						this.__bubble.destroy();
						}
					this.__class.changePage(1, this.__ctype);
					};
				}	
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
			}		
		
		//the blink effect if its on for section communications only
		if(ctype == 'communications' && this.__bRefreshIsBlinking){
			this.makeRefreshButtonBlink(this.__bRefreshIsBlinking);
			}
		
		this.__panelClassBottom[ctype].registerObject(this.__cToolManager[ctype]);
		};
		
	
	/*************************************************************************************************************************************************/
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieMessage{
		return this;
		};
	*/	
	}	