/*

section Bottin->carnet et Bottin->blocked

*/

import display.CieListing;
import control.CiePanel;
import manager.CieToolsManager;
import messages.CieOptionMessages;
import control.CieBubble;

import flash.filters.GlowFilter;

dynamic class display.CieBottin{

	static private var __className = 'CieBottin';
	
	static private var __instance:CieBottin;
	
	private var __rowPerPage:Number;
	
	private var __pageIN:Array;
	private var __sortOrder:Array;
	private var __panelClassTop:Array;
	private var __panelClassBottom:Array;
	private var __cListing:Array;
	private var __chkBoxState:Array;
	private var __cToolManager:Array;
	private var __cMessageBox:Object;
	private var __arrSortName:Array;
	private var __arrRowCount:Array;
	
	private var __bubble:Object;
	
	//for corner butt
	private var __panelClassContainer:Object;
	private var __cornerButt:MovieClip;
	
	private function CieBottin(Void){
		if(CieStyle.__miniProfil.__effectOn){
			this.__rowPerPage = CieStyle.__miniProfil.__effectRowPerPage;
		}else{
			this.__rowPerPage = 8;
			}
		this.__arrSortName = new Array();
		this.__arrSortName.push(['members.pseudo ASC',gLang[119]]);
		this.__arrSortName.push(['members.pseudo DESC',gLang[120]]);
		this.__arrSortName.push(['members.active, members.pseudo ASC',gLang[121]]);
		this.__arrSortName.push(['members.age, members.pseudo ASC',gLang[122]]);
		this.__arrSortName.push(['members.age DESC, members.pseudo ASC',gLang[123]]);
		this.__arrSortName.push(['members.photo DESC, members.pseudo ASC',gLang[124]]);
		this.__arrSortName.push(['members.album DESC, members.pseudo ASC',gLang[125]]);
		//the checkBoxes
		this.__chkBoxState = new Array();
		this.__chkBoxState['carnet'] = '0';
		this.__chkBoxState['listenoire'] = '0';
		//the page we are in
		this.__pageIN = new Array();
		this.__pageIN['carnet'] = 0;
		this.__pageIN['listenoire'] = 0;
		//row count
		this.__arrRowCount = new Array();
		this.__arrRowCount['carnet'] = 0;
		this.__arrRowCount['listenoire'] = 0;
		//sort ordre		
		this.__sortOrder = new Array();
		this.__sortOrder['carnet'] = 'members.active, members.pseudo ASC';
		this.__sortOrder['listenoire'] = 'members.pseudo ASC';
		//ref to the pannel class
		this.__panelClassTop = new Array();		
		this.__panelClassBottom = new Array();		
		//ref to the listing Class
		this.__cListing = new Array();
		//ref to the toolmanager class
		this.__cToolManager = new Array();
		};
		
	static public function getInstance(Void):CieBottin{
		if(__instance == undefined) {
			__instance = new CieBottin();
			}
		return __instance;
		};	
	
	/**************************************************************************************************************/		
	
	public function reset(Void):Void{
		//the checkBoxes
		this.__chkBoxState = new Array();
		this.__chkBoxState['carnet'] = '0';
		this.__chkBoxState['listenoire'] = '0';
		//the page we are in
		this.__pageIN = new Array();
		this.__pageIN['carnet'] = 0;
		this.__pageIN['listenoire'] = 0;
		//row count
		this.__arrRowCount = new Array();
		this.__arrRowCount['carnet'] = 0;
		this.__arrRowCount['listenoire'] = 0;
		//sort ordre		
		this.__sortOrder = new Array();
		this.__sortOrder['carnet'] = 'members.active, members.pseudo ASC';
		this.__sortOrder['listenoire'] = 'members.pseudo ASC';
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
		var pObj:Object = cContent.getPanelObject(['bottin','_tr']);
		pObj.__class.removeRegisteredObject(pObj.__tabManager);
		
		//clean tool manager
		this.__cToolManager['carnet'] = undefined;
		this.__cToolManager['listenoire'] = undefined;
		//clear panel ref
		this.__panelClassTop['carnet'] = undefined;
		this.__panelClassTop['listenoire'] = undefined;
		this.__panelClassBottom['carnet'] = undefined;
		this.__panelClassBottom['listenoire'] = undefined;
		//listing
		this.__cListing['carnet'] = undefined;
		this.__cListing['listenoire'] = undefined;
				
		};
		
	/*************************************************************************************************************************************************/	
		
	public function changeNode(Void):Void{
		//change the nodeXML value
		var strXml:String = '<P n="_tr" content="mvContent" bgcolor="0xEDEDED" scroll="false"></P>';
		cContent.changeNodeValue(new XML(strXml), ['bottin','_tr']);
		//reloads the node
		cContent.openTab(['bottin']);
		};
		
	/*************************************************************************************************************************************************/
	
	public function openSection(ctype:String):Void{
		//carnet OR blocked
		cContent.openTab(['bottin', ctype]);
		//top right corner butt for resize
		this.drawMinimizeCorner();
		};
		
	/*************************************************************************************************************************************************/

	public function drawMinimizeCorner(Void):Void{
		//put the maximize arrow 
		//get the panel containing the tabs
		this.__panelClassContainer = cContent.getPanelClass(['bottin','_tl']);
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
		this.__panelClassTop[ctype] = cContent.getPanelClass(['bottin','_tl',ctype,'_tl']);
		this.__panelClassBottom[ctype] = cContent.getPanelClass(['bottin','_tl',ctype,'_bl']);
		
		//put the loader
		this.__panelClassTop[ctype].setContent('mvContent');
		if(this.__cToolManager[ctype] == undefined){
			this.__panelClassBottom[ctype].setContent(CieStyle.__basic.__bgMvBottomNavigation);
			}
		
		//build the tmp table for carnet
		var strWhere:String = " WHERE msg_" + ctype + " = '1' ";
		this.__arrRowCount[ctype] = cDbManager.changeTmpTable(ctype, strWhere, this.__sortOrder[ctype]);
		
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
		var arrRows:Array = cDbManager.getTmpPageRow(this.__pageIN[ctype], this.__rowPerPage, ctype);
		
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
			'</R>';
			}
		delete arrRows;
		strXml += '</UNILISTING>';
		//new XMl
		var newXml:XML = new XML(strXml);
		//si deja instancie
		if(this.__cListing[ctype] == undefined){
			this.__cListing[ctype] = new CieListing(this.__panelClassTop[ctype].getPanelContent(), 'bottin');
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
	
	//get all checked items (chkBox = 1 or 2)
	public function getCheckedItems(ctype:String):Void{
		var arrChecked:Array = this.__cListing[ctype].getCheckedItems();	
		//Debug("BOTTIN_CHECKED(" + ctype + "): " + arrChecked.toString());
		if(ctype == 'carnet'){
			cFunc.removeFromCarnet(arrChecked);
		}else{
			cFunc.removeFromListeNoire(arrChecked);
			}
		};
		
	//pour le droit de regardf des albums
	public function giveAlbumRights(ctype:String):Void{
		var arrChecked:Array = this.__cListing[ctype].getCheckedItems();
		cFunc.giveAlbumRights(arrChecked, 'bottin');
		};
	
	
	
	/*************************************************************************************************************************************************/
	
	//change the sort order
	public function openSortOrderBox(ctype:String):Void{
		//Debug("CieBotin.openSortOrderBox(" + ctype + ")");
		var selectedID:Number = 0;
		this.__cMessageBox = new CieOptionMessages(gLang[117], this.__arrSortName, gLang[118]);
		for(var o in this.__arrSortName){
			if(this.__sortOrder[ctype] == this.__arrSortName[o][0]){
				selectedID = o;
				break;
				}
			}
		this.__cMessageBox.setSelectionValue(selectedID);
		this.__cMessageBox.setCallBackFunction(this.cbSortOrderSelected, {__class: this, __ctype:ctype});
		};
		
	/*************************************************************************************************************************************************/
	
	//user has made a choice
	public function cbSortOrderSelected(cbObject:Object):Void{
		//user has made a choice
		if(cbObject.__ok == true){
			//change the sort order var
			cbObject.__class.__sortOrder[cbObject.__ctype] = cbObject.__class.__arrSortName[cbObject.__class.__cMessageBox.getSelectedChoice()][0];
			//recfresh the section
			cbObject.__class.refreshSection(cbObject.__ctype);
			}
		//delete the var holding the CieOptionMessages
		cbObject.__class.__cMessageBox = null;
		};
		
		
	/*************************************************************************************************************************************************/		
		
	public function showBottomNavigation(ctype:String, bNav:Boolean):Void{
		//Debug("SHOWNAV_1: " + bNav + '<->' + this.__pageIN[ctype]);
		//tootlbar
		var strXML:String = '';
		strXML += '<UNITOOLBAR>';
		strXML += '<TOOLGROUP n="actions" align="left">';
		strXML += '<TOOL n="rights" type="32X32" icon="mvIconImage_19"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[309] + '"></BUBBLE></TOOL>';
		strXML += '<TOOL n="sortorder" type="32X32" icon="mvIconImage_13"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[310] + '"></BUBBLE></TOOL>';
		strXML += '<TOOL n="delete" type="32X32" icon="mvIconImage_23"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[311] + '"></BUBBLE></TOOL>';
		strXML += '<TOOL n="select" type="32X32" icon="mvIconImage_11"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[312] + '"></BUBBLE></TOOL>';
		strXML += '<TOOL n="refresh" type="32X32" icon="mvIconImage_8"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[313] + '"></BUBBLE></TOOL>';
		strXML += '</TOOLGROUP>';
		//prev/next
		if(bNav || this.__pageIN[ctype]){
			strXML += '<TOOLGROUP n="navigation" align="right">';
			if(bNav){
				strXML += '<TOOL n="next" type="40X40" icon="mvIconImage_3"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[314] + '"></BUBBLE></TOOL>';
				}
			if(this.__pageIN[ctype]){
				strXML += '<TOOL n="previous" type="40X40" icon="mvIconImage_2"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[315] + '"></BUBBLE></TOOL>';
				}
			strXML += '</TOOLGROUP>';
			}
		strXML += '</UNITOOLBAR>';
		//XML
		//Debug("BOTTIN:" + strXML);
		var xmlTool:XML = new XML(strXML);	
		
		this.__cToolManager[ctype] = new CieToolsManager(this.__panelClassBottom[ctype].getPanelContent(), this.__panelClassBottom[ctype].getPanelSize().__width, 65, 0, 0);
		this.__cToolManager[ctype].createFromXml(xmlTool.firstChild);
		//sort  items
		this.__cToolManager[ctype].getIcon('actions', 'rights').__class = this;
		this.__cToolManager[ctype].getIcon('actions', 'rights').__ctype = ctype;
		this.__cToolManager[ctype].getIcon('actions', 'rights').onRelease = function(Void):Void{
			//for bubble text usage
			if(this.__bubbletext != undefined){
				this.__bubble.destroy();
				}
			this.__class.giveAlbumRights(this.__ctype);
			};
		//sort  items
		this.__cToolManager[ctype].getIcon('actions', 'sortorder').__class = this;
		this.__cToolManager[ctype].getIcon('actions', 'sortorder').__ctype = ctype;
		this.__cToolManager[ctype].getIcon('actions', 'sortorder').onRelease = function(Void):Void{
			//for bubble text usage
			if(this.__bubbletext != undefined){
				this.__bubble.destroy();
				}
			this.__class.openSortOrderBox(this.__ctype);
			};
		//delete  checked action
		this.__cToolManager[ctype].getIcon('actions', 'delete').__class = this;
		this.__cToolManager[ctype].getIcon('actions', 'delete').__ctype = ctype;
		this.__cToolManager[ctype].getIcon('actions', 'delete').onRelease = function(Void):Void{
			//for bubble text usage
			if(this.__bubbletext != undefined){
				this.__bubble.destroy();
				}
			this.__class.getCheckedItems(this.__ctype);
			};
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
		this.__panelClassBottom[ctype].registerObject(this.__cToolManager[ctype]);
		};
		
	
	/*************************************************************************************************************************************************/
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieBottin{
		return this;
		};
	*/	
	}	