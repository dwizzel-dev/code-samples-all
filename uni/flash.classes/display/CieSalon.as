/*

section Salon, critere

*/

import display.CieListing;
//import control.CiePanel;
import manager.CieToolsManager;
import messages.CieOptionMessages;
import control.CieBubble;
import messages.CieTextMessages;

import flash.filters.GlowFilter;

dynamic class display.CieSalon{

	static private var __className = 'CieSalon';
	
	static private var __instance:CieSalon;
	
	private var __rowPerPage:Number;
	
	private var __pageIN:Array;
	private var __sortOrder:Array;
	private var __panelClassTop:Array;
	private var __panelClassBottom:Array;
	private var __cListing:Array;
	private var __cToolManager:Array;
	
	private var __intervalCritere:Number;
	
	private var __arrSortName:Array;
	
	private var __cMessageBox:Object;
	private var __cWaitMessageBox:Object;
	
	//for corner butt
	private var __panelClassContainer:Object;
	private var __cornerButt:MovieClip;
	
	private function CieSalon(Void){
		this.__rowPerPage = 8;
		
		//sort
		this.__arrSortName = new Array();
		this.__arrSortName.push(['members.pseudo ASC',gLang[236]]);
		this.__arrSortName.push(['members.pseudo DESC',gLang[237]]);
		this.__arrSortName.push(['members.age, members.pseudo ASC',gLang[238]]);
		this.__arrSortName.push(['members.age DESC, members.pseudo ASC',gLang[239]]);
		this.__arrSortName.push(['members.photo DESC, members.pseudo ASC',gLang[240]]);
		this.__arrSortName.push(['members.album DESC, members.pseudo ASC',gLang[241]]);
		
		//the page we are in
		this.__pageIN = new Array();
		this.__pageIN['salon'] = 0;
		//sort ordre		
		this.__sortOrder = new Array();
		this.__sortOrder['salon'] = 'members.pseudo ASC';
		//ref to the pannel class
		this.__panelClassTop = new Array();		
		this.__panelClassBottom = new Array();		
		//ref to the listing Class
		this.__cListing = new Array();
		//ref to the toolmanager class
		this.__cToolManager = new Array();
		};
		
	static public function getInstance(Void):CieSalon{
		if(__instance == undefined) {
			__instance = new CieSalon();
			}
		return __instance;
		};	
	
	/**************************************************************************************************************/		
	
	public function reset(Void):Void{
		clearInterval(this.__intervalCritere);
		//NEW 14-10-2008
		this.__cWaitMessageBox.closeWindow();
		//END NEW 14-10-2008
		//the page we are in
		//the page we are in
		this.__pageIN = new Array();
		this.__pageIN['salon'] = 0;
		//sort ordre		
		this.__sortOrder = new Array();
		this.__sortOrder['salon'] = 'members.pseudo ASC';
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
		var pObj:Object = cContent.getPanelObject(['salon','_tr']);
		pObj.__class.removeRegisteredObject(pObj.__tabManager);
		
		//clean tool manager
		this.__cToolManager['salon'] = undefined;
		this.__cToolManager['critere'] = undefined;
		//clear panel ref
		this.__panelClassTop['salon'] = undefined;
		this.__panelClassTop['critere'] = undefined;
		this.__panelClassBottom['salon'] = undefined;
		this.__panelClassBottom['critere'] = undefined;
		//listing
		this.__cListing['salon'] = undefined;
						
		};
		
	/*************************************************************************************************************************************************/	
		
	public function changeNode(Void):Void{
		//change the nodeXML value
		var strXml:String = '<P n="_tr" content="mvContent" bgcolor="0xEDEDED" scroll="false"></P>';
		cContent.changeNodeValue(new XML(strXml), ['salon','_tr']);
		//reloads the node
		cContent.openTab(['salon']);
		};
		
	/*************************************************************************************************************************************************/
	
	public function openSection(ctype:String):Void{
		//carnet OR blocked
		cContent.openTab(['salon', ctype]);
		//top right corner butt for resize
		this.drawMinimizeCorner();
		};
		
	/*************************************************************************************************************************************************/

	public function drawMinimizeCorner(Void):Void{
		//put the maximize arrow 
		//get the panel containing the tabs
		this.__panelClassContainer = cContent.getPanelClass(['salon','_tl']);
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
	
	//for the critere
	public function buildRequest(strQueryString:String, ctype:String):Void{
		//keep the args for paging navigation
		this.__reqArgs[ctype] = strQueryString;
		//put the loader movie on the top panel
		this.__panelClassTop[ctype].setContent('mvLoaderAnimated');
		this.__panelClassTop[ctype].setBgColor(CieStyle.__profil.__bgPanelListingLoad);
		//remove bottom navigation
		if(this.__cToolManager[ctype] != undefined){
			//Debug("DELETING TOOL SALON");
			this.__cToolManager[ctype].removeAllTools();
			delete this.__cToolManager[ctype];
			}
		//build request
		var arrD = new Array();
		arrD['methode'] = 'salon';
		arrD['action'] = ctype;
		//build the arguments
		arrD['arguments'] = strQueryString;
		//add the request
		//Debug("REQUEST[" + ctype + "]: " + arrD['arguments']);
		cReqManager.addRequest(arrD, this.cbCritere, {__class:this, __ctype:ctype});	
		
		//NEW 14-10-2008
		//on avertit que l'on va loader le reste
		this.__cWaitMessageBox = new CieTextMessages('MB_NONE', '', 'Updating salon from criteria');
		this.__cWaitMessageBox.setProgress();
		this.setLoadingProgressBox(0, 1); //by default
		//END NEW 14-10-2008
		
		};
	
	/*************************************************************************************************************************************************/	
	
	//callBack function for the critere form
	public function cbCritere(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		// modify the critere locally
		obj.__super.__class.parseXmlCritere(obj.__req.getXml());
		
		//NEW 14-10-2008
		//since criteres have change we can now ask the server for missing users
		cSockManager.socketSend('PMISSING:' + BC.__user.__pseudo);
		//END NEW 14-10-2008
		
		/*
		va maintenant etre caller par le SocketManager
		obj.__super.__class.changeCritere();
		*/
		
		//remove request
		cReqManager.removeRequest(obj.__req.getID());
		};	
		
	/*************************************************************************************************************************************************/	
		
	public function parseXmlCritere(xmlNode:XMLNode):Void{
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.firstChild.nodeType == 1){
				this.parseXmlCritere(currNode);
			}else{
				BC.__user.__critere[currNode.attributes.n] = unescape(String(currNode.firstChild.nodeValue));
				}
			}
		};	
		

	/*************************************************************************************************************************************************/	
	//reload both sections, have to put an interval for the critere because replication of DB is too slow
	public function changeCritere(Void):Void{
		//timer for replication of the Write and Read database mySql
		this.__intervalCritere = setInterval(this, 'refreshCritere', 1000, 'critere');
		//refresh the listing	
		this.refreshSection('salon');
		//focus on the onlne tab
		this.openSection('salon');
		//
		};
	
	/*************************************************************************************************************************************************/	
		
	public function refreshCritere(ctype:String):Void{
		clearInterval(this.__intervalCritere);
		
		//get the panel class
		this.__panelClassTop[ctype] = cContent.getPanelClass(['salon','_tl',ctype,'_tl']);
		this.__panelClassBottom[ctype] = cContent.getPanelClass(['salon','_tl',ctype,'_bl']);
		
		//put the loader
		this.__panelClassTop[ctype].setContent('mvLoaderAnimated');
		this.__panelClassTop[ctype].setBgColor(CieStyle.__profil.__bgPanelListingLoad);
		
		if(this.__cToolManager[ctype] == undefined){
			this.__panelClassBottom[ctype].setContent(CieStyle.__basic.__bgMvBottomNavigation);
			}
		
		//show the form
		cFormManager.afficheTemplate('salon', ctype, this.__panelClassTop[ctype]);
		
		//bottom navigation
		if(this.__cToolManager[ctype] == undefined){
			this.showCriteresBottomNavigation(ctype);
			}
		
		//NEW 14-10-2008
		this.__cWaitMessageBox.closeWindow();
		delete this.__cWaitMessageBox;
		//END NEW 14-10-2008
		
		};
		
	/*************************************************************************************************************************************************/	
		
	public function refreshSection(ctype:String):Void{
		//set page to first one
		this.__pageIN[ctype] = 0;
		
		//get the panel class
		this.__panelClassTop[ctype] = cContent.getPanelClass(['salon','_tl',ctype,'_tl']);
		this.__panelClassBottom[ctype] = cContent.getPanelClass(['salon','_tl',ctype,'_bl']);
		
		//put the loader
		this.__panelClassTop[ctype].setContent('mvContent');
		if(this.__cToolManager[ctype] == undefined){
			this.__panelClassBottom[ctype].setContent(CieStyle.__basic.__bgMvBottomNavigation);
			}
		
		//build the tmp table 
		var strWhere:String = ' WHERE members.msg_' + ctype + ' = "1" ';
		
		//build the query for the salon based on critere
		var strTmp:String = '';
		var bFoundItem:Boolean = false;
		
		//RELATION
		var arrRelation:Array =  BC.__user.__critere['relation'].split(',');
		strTmp = 'AND (';
		for(var i in arrRelation){
			if(arrRelation[i] == '1'){
				strTmp += 'MID(members.relation,' + (Number(i) + 1) + ',1) = "' + arrRelation[i] + '" OR ';
				bFoundItem = true;
				}
			}
		if(bFoundItem){	
			bFoundItem = false;
			strWhere += strTmp.substr(0, (strTmp.length - 3)) + ') ';
			}
		
		//SEXE
		var arrSexe:Array = BC.__user.__critere['sexe'].split(','); //separation
		strTmp = 'AND (';
		for(var i = 1; i <= arrSexe.length; i++){
			if(arrSexe[Number(i)-1] == '1'){
				strTmp += 'members.sexe = "' + i + '" OR ';
				bFoundItem = true;
				}
			}
		if(bFoundItem){	
			bFoundItem = false;	
			strWhere += strTmp.substr(0, (strTmp.length - 3)) + ') ';
			}	
			
		//ORIENTATION
		var arrOrientation:Array = BC.__user.__critere['orientation'].split(','); //separation
		strTmp = 'AND (';
		for(var i = 1; i <= arrOrientation.length; i++){
			if(arrOrientation[Number(i)-1] == '1'){
				strTmp += 'members.orientation = "' + i + '" OR ';	
				bFoundItem = true;
				}
			}
		if(bFoundItem){	
			bFoundItem = false;	
			strWhere += strTmp.substr(0, (strTmp.length - 3)) + ') ';
			}	
			
		//COUNTRY
		var arrCountry:Array = BC.__user.__critere['code_pays'].split(','); //separation
		strTmp = 'AND (';
		for(var i in arrCountry){
			if(arrCountry[i] != '00'){
				strTmp += 'members.code_pays = "' + arrCountry[i] + '" OR ';	
				bFoundItem = true;
				}
			}
		if(bFoundItem){	
			bFoundItem = false;	
			strWhere += strTmp.substr(0, (strTmp.length - 3)) + ') ';;
			}	
			
		//REGION
		if(BC.__user.__critere['region_id'] != '0' && BC.__user.__critere['region_id'] != 0){	
			var arrRegion:Array = BC.__user.__critere['region_id'].split(','); //separation
			strTmp = 'AND (';
			for(var i in arrRegion){
				strTmp += 'members.region_id = ' + arrRegion[i] + ' OR ';	
				bFoundItem = true;
				}
			if(bFoundItem){	
				bFoundItem = false;
				strWhere += strTmp.substr(0, (strTmp.length - 3)) + ') ';;
				}
			}
						
		//VILLE
		if(BC.__user.__critere['ville_id'] != '0' && BC.__user.__critere['ville_id'] != 0){	
			var arrVille:Array = BC.__user.__critere['ville_id'].split(','); //separation
			strTmp = 'AND (';
			for(var i in arrVille){
				strTmp += 'members.ville_id = ' + arrVille[i] + ' OR ';	
				bFoundItem = true;
				}
			if(bFoundItem){	
				bFoundItem = false;
				strWhere += strTmp.substr(0, (strTmp.length - 3)) + ') ';
				}
			}
		
		//AGE
		var arrAge:Array = BC.__user.__critere['age'].split(','); //separation
		strWhere += 'AND (members.age > ' + arrAge[0] + ' AND members.age < ' + arrAge[1] + ') ';
		
		//change the tmp table
		cDbManager.changeTmpTable(ctype, strWhere, this.__sortOrder[ctype]);
				
		//build the page listing
		this.showPageListing(ctype);
		};	
		
	/*************************************************************************************************************************************************/	
		
	private function showPageListing(ctype:String):Void{
		//get the row jack
		var arrRows:Array = cDbManager.getTmpPageRow(this.__pageIN[ctype], this.__rowPerPage, ctype);
		
		//build the xml loop throught the Array of Rows
		var limit:Number = arrRows.length;
		if(limit > this.__rowPerPage){
			limit -= 1; 
			var bShowNextButt:Boolean = true;
			}
		var strXml:String = '<UNILISTING>';
		for(var o = 0; o < limit; o++){
			//Debug("ROW_SALON: " + arrRows[o][1]);
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
			this.__cListing[ctype] = new CieListing(this.__panelClassTop[ctype].getPanelContent(), 'salon');
			}
		//create from the xml coded up	
		this.__cListing[ctype].createFromXmlNode(newXml.firstChild);
		//put the scroollbar at the top
		this.__panelClassTop[ctype].placeScrollBar(0);
		//bottom navigation
		if(this.__cToolManager[ctype] != undefined){
			this.__cToolManager[ctype].removeAllTools();
			delete this.__cToolManager[ctype];
			}
		this.showBottomNavigation(ctype, bShowNextButt);	
		};
		
	/*************************************************************************************************************************************************/		
		
	public function showBottomNavigation(ctype:String, bNav:Boolean):Void{
		//tootlbar
		var strXML:String = '';
		strXML += '<UNITOOLBAR>';
		strXML += '<TOOLGROUP n="actions" align="left">';
		strXML += '<TOOL n="sortorder" type="32X32" icon="mvIconImage_13"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[310] + '"></BUBBLE></TOOL>';
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
		var xmlTool:XML = new XML(strXML);	
		
		this.__cToolManager[ctype] = new CieToolsManager(this.__panelClassBottom[ctype].getPanelContent(), this.__panelClassBottom[ctype].getPanelSize().__width, 65, 0, 0);
		this.__cToolManager[ctype].createFromXml(xmlTool.firstChild);
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
		this.__panelClassBottom[ctype].registerObject(this.__cToolManager[ctype]);
		};
		
	/*************************************************************************************************************************************************/	
		
	public function showCriteresBottomNavigation(ctype:String):Void{
		//tootlbar
		var strXML:String = '' +
				'<UNITOOLBAR>' +
				'<TOOLGROUP n="actions" align="left">' +
				'<TOOL n="refresh" type="32X32" icon="mvIconImage_8"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[313] + '"></BUBBLE></TOOL>' + 
				'</TOOLGROUP>' +
				'<TOOLGROUP n="form" align="right">' +
				'<BUTTON n="send" type="100X32" text="' + gLang[242] + '" style="' + CieStyle.__basic.__toolColor + ',' + CieStyle.__basic.__toolBorderWidth + ',' + CieStyle.__basic.__borderToolColor + ',' + CieStyle.__basic.__toolEffectColor + ',' + CieStyle.__basic.__toolEffectColorOff + ',' + CieStyle.__basic.__buttonToolFontColor + '"><ACTION n="onrollover" func=""></ACTION></BUTTON>' +
				'</TOOLGROUP>' +
				'</UNITOOLBAR>';
		//XML
		var xmlTool:XML = new XML(strXML);	
		
		this.__cToolManager[ctype] = new CieToolsManager(this.__panelClassBottom[ctype].getPanelContent(), this.__panelClassBottom[ctype].getPanelSize().__width, 65, 0, 0);
		this.__cToolManager[ctype].createFromXml(xmlTool.firstChild);
		//send action
		this.__cToolManager[ctype].getIcon('form', 'send').__ctype = ctype;
		this.__cToolManager[ctype].getIcon('form', 'send').onRelease = function(Void):Void{
			cFormManager.sendForm(this.__ctype);
			};
			
		//refresh action
		this.__cToolManager[ctype].getIcon('actions', 'refresh').__class = this;
		this.__cToolManager[ctype].getIcon('actions', 'refresh').__ctype = ctype;
		this.__cToolManager[ctype].getIcon('actions', 'refresh').onRelease = function(Void):Void{
			//for bubble text usage
			if(this.__bubbletext != undefined){
				this.__bubble.destroy();
				}
			this.__class.refreshCritere(this.__ctype);
			};	
				
		this.__panelClassBottom[ctype].registerObject(this.__cToolManager[ctype]);
		};	
		
	/*************************************************************************************************************************************************/
	
	//change the sort order
	public function openSortOrderBox(ctype:String):Void{
		//Debug("CieBotin.openSortOrderBox(" + ctype + ")");
		var selectedID:Number = 0;
		this.__cMessageBox = new CieOptionMessages(gLang[243], this.__arrSortName, gLang[244]);
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
	//called by CieSocketManager
	public function setLoadingProgressBox(iLoaded:Number, iTotal:Number):Void{	
		if(this.__cWaitMessageBox != undefined){
			this.__cWaitMessageBox.setLoadingProgress('Please wait! updating database...', iLoaded, iTotal);
			}
		};
	
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieSalon{
		return this;
		};
	*/	
	}	