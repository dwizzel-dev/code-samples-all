/*

Utilise tout ce qui a rapport au Formulaire, champs, etc...

*/

import control.CiePanel;
//import control.CieButton;
//import graphic.CieSquare;
import control.CieTextLine;
import control.CieOptionBox;
import control.CieSelectBox;
import control.CieCheckBox;
import control.CieCheckBoxGroup;
import control.CieListBox;


dynamic class manager.CieFormManager{	
	
	static private var __className:String = 'CieFormManager';
	static private var __instance:CieFormManager;
	private var __hvSpacer:Number;
	private var __hBox:Number;
	private var __objTmp:Object;
	private var __obj:Object;
	private var __arrForm:Array;
	private var __cmpNode:Number = 0;
	private var __node:Number = 0;
	private var __xmlFile:String;
	private var __arrContent:Array;
	private var __arrPanelClass:Array;
	private var __arrContentBgForScroll:Array;
	private var __arrContentDataLayer:Array;
	private var __arrContentLoaderLayer:Array;
	private var __h:Array;
	//private var __iMarge:Number = 10;
	
	//interval
	private var __arrIntervalParsing:Array;
	
	//for callback
	private var __cbFunction:Function;
	private var __cbClass:Object;
	
	//pos
	private var __selectBoxWidth:Number;
	private var __selectBoxHeight:Number;
	private var __listBoxHeight:Number;
	
	//a ref to region and ville selectBox for onChange event because they all depends on country->region->ville
	private var __arrRefCountryBox:Array;
	private var __arrRefRegionBox:Array;
	private var __arrRefVilleBox:Array;
	
	/*************************************************************************************************************************************************/
	
	private function CieFormManager(cbFunction:Function, cbClass:Object){
		
		this.__arrIntervalParsing = new Array();
		this.__arrRefCountryBox = new Array();
		this.__arrRefRegionBox = new Array();
		this.__arrRefVilleBox = new Array();
		
		this.__arrContent = new Array();
		this.__arrPanelClass = new Array();
		this.__arrContentBgForScroll = new Array();
		this.__arrContentDataLayer = new Array();
		this.__arrContentLoaderLayer = new Array();
		
		this.__selectBoxWidth = 270;
		this.__selectBoxHeight = 22;
		this.__listBoxHeight = 90;
		
		//callback
		this.__cbFunction = cbFunction;
		this.__cbClass = cbClass;
		this.__obj = new Object();
		this.__objTmp = new Object();
		this.__arrForm = new Array();
		this.__hvSpacer = 10;
		this.__hBox = 17;
		this.__h = new Array();
		this.fetchData(); 
		};
		
	static public function getInstance(cbFunction:Function, cbClass:Object):CieFormManager{
		if(__instance == undefined){
			__instance = new CieFormManager(cbFunction, cbClass);
			}
		return __instance;
		};
		
	/*************************************************************************************************************************************************/

	public function reset(Void):Void{
		//interval a cleaner
		for(o in this.__arrIntervalParsing){
			clearInterval(this.__arrIntervalParsing[o]);
			delete this.__arrIntervalParsing[o];
			}
		this.__arrIntervalParsing = new Array();
		this.__arrRefCountryBox = new Array();
		this.__arrRefRegionBox = new Array();
		this.__arrRefVilleBox = new Array();
		this.__arrContent = new Array();
		this.__arrPanelClass = new Array();
		this.__arrContentBgForScroll = new Array();
		this.__arrContentDataLayer = new Array();
		this.__arrContentLoaderLayer = new Array();
		this.__arrForm = new Array();
		this.__h = new Array();
		};
	
	/*************************************************************************************************************************************************/
	
	public function fetchData(Void):Void{
		var arrD = new Array();
		arrD['methode'] = 'form';
		arrD['action'] = 'data';
		arrD['arguments'] = '';
		//the __notifyUserOnHttpRequestError is to true
		//because it's the first attemps to connect to the web
		//so if wa have no connection we have to notify the user
		//that there is a problem with his connecion or with our server
		//or the appx will continue to loop for ever
		cReqManager.addRequest(arrD, this.cbFetchData, null, true);
		};
	
	/*************************************************************************************************************************************************/
	
	public function cbFetchData(prop, oldVal:Number, newVal:Number, obj:Object){
		// parsing XML
		//obj.__super.createObjectFromXml(obj.__req.getXml().firstChild);
		cFormManager.createObjectFromXml(obj.__req.getXml().firstChild);
		//remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	/*************************************************************************************************************************************************/	

	private function createObjectFromXml(xmlNode:XMLNode):Void{	
		this.parseXml(xmlNode);
		this.__cbFunction(this.__cbClass);
		};	
		
	/*************************************************************************************************************************************************/	
		
	private function parseXml(xmlNode:XMLNode):Void{
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];	
			if (currNode.firstChild.nodeType != 3){
				var nName = String(currNode.attributes.n); 
				this.__objTmp[nName] = new Array();
				this.__obj[nName] = new Array();
				this.parseXml(currNode);
			}else{
				var last = String(currNode.parentNode.attributes.n);
				var id = currNode.attributes.n; 
				var value = unescape(currNode.firstChild.nodeValue);
				this.__objTmp[last][id] = [id,value];
				}
			}
		//patch
		for (var o in this.__objTmp[last]){
			this.__obj[last][o] = this.__objTmp[last][o];
			}
		};
		
	/*************************************************************************************************************************************************/	
	
	public function afficheTemplate(section:String, ctype:String, cPanel:CiePanel, strRequestArgs:String):Void{
		//reset the interval of parsing in case if it still doing some parsing
		clearInterval(this.__arrIntervalParsing[ctype]);
		//reset the height value
		this.__h[ctype] = this.__hvSpacer;
		//ref to the panel class
		this.__arrPanelClass[ctype] = cPanel;
		//ref to the form
		this.__arrForm[ctype] = new Array();
		//add a request
		var arrD = new Array();
			arrD['methode'] = 'template';
			arrD['action'] = ctype + 'concat'; //concatenated format
			if(strRequestArgs != undefined && strRequestArgs != ''){
				//generally for the saed recherche detaillees
				arrD['arguments'] = strRequestArgs;
			}else{
				arrD['arguments'] = '';
				}
		cReqManager.addRequest(arrD, this.cbFormLoaded, {__class:this, __ctype:ctype});
		};
		
	/*************************************************************************************************************************************************/	
	
	public function cbFormLoaded(prop, oldVal:Number, newVal:Number, obj:Object){
		//remove the loader movie put mvContent instaed to be alb eto write to it
		obj.__super.__class.showLoader(false, obj.__super.__ctype);
		//parse the xml file just loaded
		//Debug('TEMPLATE_XML_START(' + obj.__super.__ctype + ')');
		if(obj.__super.__ctype == 'detaillees'){
			//this one with interval
			if(BC.__user.__detailsWithInterval){// in config.xml
				obj.__super.__class.__arrIntervalParsing[obj.__super.__ctype] = setInterval(obj.__super.__class, 'parseXmlTemplateWithInterval', 1, obj.__req.getXml().firstChild, obj.__super.__ctype, 0);
			}else{
				obj.__super.__class.parseXmlTemplate(obj.__req.getXml().firstChild, obj.__super.__ctype);
				}	
		}else{
			//tje others are fat enough
			obj.__super.__class.parseXmlTemplate(obj.__req.getXml().firstChild, obj.__super.__ctype);
			}
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		}; 
	
	/*************************************************************************************************************************************************/	
	
	public function showLoader(b:Boolean, ctype:String):Void{
		if(b){
			//put the loader movie content
			this.__arrPanelClass[ctype].setContent('mvLoaderAnimated');
			this.__arrPanelClass[ctype].setBgColor(CieStyle.__profil.__bgPanelListingLoad);
			//change the movieclip reference	
			this.__arrContent[ctype] = this.__arrPanelClass[ctype].getPanelContent();
		}else{
			//put the content movie to write in it
			this.__arrPanelClass[ctype].setContent('mvContent');
			this.__arrPanelClass[ctype].setBgColor(CieStyle.__profil.__bgPanelForm);
			//change the movieclip reference	
			this.__arrContent[ctype] = this.__arrPanelClass[ctype].getPanelContent();
			//put a background to enable scrool mouse over
			this.__arrContentBgForScroll[ctype] = this.__arrContent[ctype].createEmptyMovieClip('BG_LAYER', this.__arrContent[ctype].getNextHighestDepth());
			//the datat layer
			this.__arrContentDataLayer[ctype] = this.__arrContent[ctype].createEmptyMovieClip('DATA_LAYER', this.__arrContent[ctype].getNextHighestDepth());
			//hide the data untill its completely build
			this.__arrContentDataLayer[ctype]._visible = false;
			//the loader layer
			this.__arrContentLoaderLayer[ctype] = this.__arrContent[ctype].createEmptyMovieClip('LOADER_LAYER', this.__arrContent[ctype].getNextHighestDepth());
			//attach the loader icon
			this.__arrContentLoaderLayer[ctype].attachMovie('mvLoaderAnimated', 'ANIMATED_LOADER', this.__arrContentLoaderLayer[ctype].getNextHighestDepth());
			}
		};
		
		
	/*************************************************************************************************************************************************/

	//use to set a background to catch mouse event fr scroll
	public function setBgForScroll(ctype:String):Void{
		
		var w:Number = this.__arrPanelClass[ctype].getPanelSize().__width;
		
		this.__arrContentBgForScroll[ctype].beginFill(CieStyle.__profil.__bgPanelForm, 0);
		this.__arrContentBgForScroll[ctype].moveTo(this.__hvSpacer, this.__hvSpacer);
		this.__arrContentBgForScroll[ctype].lineTo(w - this.__hvSpacer, this.__hvSpacer);
		this.__arrContentBgForScroll[ctype].lineTo(w - this.__hvSpacer, this.__h[ctype] - this.__hvSpacer);
		this.__arrContentBgForScroll[ctype].lineTo(this.__hvSpacer,this.__h[ctype] - this.__hvSpacer);
		this.__arrContentBgForScroll[ctype].lineTo(this.__hvSpacer,this.__hvSpacer);
		this.__arrContentBgForScroll[ctype].endFill();
		
		//set the action for mouseOver a form so the banner wont appear
		//this.__arrContentBgForScroll[ctype].useHandCursor = false;
		/*
		this.__arrContentBgForScroll[ctype].onRollOver = function(Void):Void{
			cFunc.changeMouseOverState(true);
			};
		*/
		/*
		this.__arrContentBgForScroll[ctype].onRollOut = 
		this.__arrContentBgForScroll[ctype].onDragOut = 
		this.__arrContentBgForScroll[ctype].onReleaseOutside = function(Void):Void{
			cFunc.changeMouseOverState(false);
			};	
		*/
		//remove the loader layer
		this.__arrContentLoaderLayer[ctype].removeMovieClip();
		delete this.__arrContentLoaderLayer[ctype];
		
		//show the data
		this.__arrContentDataLayer[ctype]._visible = true;
		};	
			
	/*************************************************************************************************************************************************/	
	
	public function parseXmlTemplateWithInterval(xmlNode:XMLNode, ctype:String, iIndex:Number):Void{
		clearInterval(this.__arrIntervalParsing[ctype]);
		if(iIndex < xmlNode.childNodes.length){
			var arrInfos = xmlNode.childNodes[iIndex].firstChild.nodeValue.split('|');
			dataobj = arrInfos[0]
			fcName = arrInfos[1];
			value = arrInfos[2];
			title = unescape(arrInfos[3]);
			switch(fcName){ 
				case "getJeRecherche" : 
					this.getJeRecherche(title, dataobj, ctype, value, false);
					break; 
				case "getJeRecherche_CheckBox" : 
					this.getJeRecherche(title, dataobj, ctype, value, true);
					break; 	
				case "getOrientation" : 
					this.getOrientation(title, dataobj, ctype, value, false);
					break; 
				case "getOrientation_CheckBox" : 
					this.getOrientation(title, dataobj, ctype, value, true);
					break; 	
				case "getAge" : 
					this.getAge(title, dataobj, ctype, value, false);
					break; 
				case "getAge_CheckBox" : 
					this.getAge(title, dataobj, ctype, value, true);
					break; 	
				case "getCountry" : 
					this.getCountry(title, dataobj, ctype, value, false);
					break; 
				case "getCountry_List" : 
					this.getCountry(title, dataobj, ctype, value, true);
					break; 	
				case "getRegion" : 
					this.getRegion(title, dataobj, ctype, value, false);
					break; 
				case "getRegion_List" : 
					this.getRegion(title, dataobj, ctype, value, true);
					break;	
				case "getVille" : 
					this.getVille(title, dataobj, ctype, value, false);
					break; 
				case "getVille_List" : 
					this.getVille(title, dataobj, ctype, value, true);
					break; 	
				case "getRelation" : 
					this.getRelation(title, dataobj, ctype, value, false);
					break; 
				case "getRelation_CheckBox" : 
					this.getRelation(title, dataobj, ctype, value, true);
					break; 	
				case "getApparence" : 
					this.getApparence(title, dataobj, ctype, value);
					break; 
				case "getEtatCivil" : 
					this.getEtatCivil(title, dataobj, ctype, value);
					break; 
				case "getPhoto" : 
					this.getPhoto(title, dataobj, ctype, value);
					break; 
				case "getCondition" : 
					this.getCondition(title, dataobj, ctype, value);
					break; 
				case "getTaille" : 
					this.getTaille(title, dataobj, ctype, value);
					break; 
				case "getPoids" : 
					this.getPoids(title, dataobj, ctype, value);
					break; 
				case "getCheveux" : 
					this.getCheveux(title, dataobj, ctype, value);
					break; 
				case "getYeux" : 
					this.getYeux(title, dataobj, ctype, value);
					break; 
				case "getStyle" : 
					this.getStyle(title, dataobj, ctype, value);
					break; 
				case "getOrig" : 
					this.getOrig(title, dataobj, ctype, value);
					break; 
				case "getZodiac" : 
					this.getZodiac(title, dataobj, ctype, value);
					break; 
				case "getLanguages" : 
					this.getLanguages(title, dataobj, ctype, value);
					break; 						
				case "getEtudes" : 
					this.getEtudes(title, dataobj, ctype, value);
					break; 
				case "getOccupation" : 
					this.getOccupation(title, dataobj, ctype, value);
					break; 
				case "getSecteur" : 
					this.getSecteur(title, dataobj, ctype, value);
					break; 
				case "getCigarette" : 
					this.getCigarette(title, dataobj, ctype, value);
					break; 
				case "getAlcool" : 
					this.getAlcool(title, dataobj, ctype, value);
					break; 
				case "getRevenu" : 
					this.getRevenu(title, dataobj, ctype, value);
					break; 
				case "getEnfants" : 
					this.getEnfants(title, dataobj, ctype, value);
					break; 
				case "getDesire" : 
					this.getDesire(title, dataobj, ctype, value);
					break; 
				case "getPseudoRecherche" : 
					this.getPseudoRecherche(title, dataobj, ctype, value);
					break; 	
				case "getExact" : 
					this.getExact(title, dataobj, ctype, value);
					break; 
				default :
					break;
				}
				
			//continue the parsing
			iIndex++;	
			this.__arrIntervalParsing[ctype] = setInterval(this, 'parseXmlTemplateWithInterval', 50, xmlNode, ctype, iIndex);
		}else{
			//xmlNode = null;
			delete xmlNode; 
			//Debug('TEMPLATE_WITH_INTERVAL_XML_FINISH(' + ctype + ')');
			
			//draw a bg for scroll mouse over
			this.setBgForScroll(ctype);
			
			//force a redraw for the scroolBar
			cStage.redraw();
			}
		//force a redraw for the scroolBar
		//cStage.redraw();
		};
		
	
	
	/*************************************************************************************************************************************************/
	
	public function parseXmlTemplate(xmlNode:XMLNode, ctype:String):Void{
		for(var i = 0; i < xmlNode.childNodes.length; i++){
			var arrInfos = xmlNode.childNodes[i].firstChild.nodeValue.split('|');
			dataobj = arrInfos[0]
			fcName = arrInfos[1];
			value = arrInfos[2];
			title = unescape(arrInfos[3]);
			switch(fcName){ 
				case "getJeRecherche" : 
					this.getJeRecherche(title, dataobj, ctype, value, false);
					break; 
				case "getJeRecherche_CheckBox" : 
					this.getJeRecherche(title, dataobj, ctype, value, true);
					break; 	
				case "getOrientation" : 
					this.getOrientation(title, dataobj, ctype, value, false);
					break; 
				case "getOrientation_CheckBox" : 
					this.getOrientation(title, dataobj, ctype, value, true);
					break; 	
				case "getAge" : 
					this.getAge(title, dataobj, ctype, value, false);
					break; 
				case "getAge_CheckBox" : 
					this.getAge(title, dataobj, ctype, value, true);
					break; 	
				case "getCountry" : 
					this.getCountry(title, dataobj, ctype, value, false);
					break; 
				case "getCountry_List" : 
					this.getCountry(title, dataobj, ctype, value, true);
					break; 	
				case "getRegion" : 
					this.getRegion(title, dataobj, ctype, value, false);
					break; 
				case "getRegion_List" : 
					this.getRegion(title, dataobj, ctype, value, true);
					break;	
				case "getVille" : 
					this.getVille(title, dataobj, ctype, value, false);
					break; 
				case "getVille_List" : 
					this.getVille(title, dataobj, ctype, value, true);
					break; 	
				case "getRelation" : 
					this.getRelation(title, dataobj, ctype, value, false);
					break; 
				case "getRelation_CheckBox" : 
					this.getRelation(title, dataobj, ctype, value, true);
					break; 	
				case "getApparence" : 
					this.getApparence(title, dataobj, ctype, value);
					break; 
				case "getEtatCivil" : 
					this.getEtatCivil(title, dataobj, ctype, value);
					break; 
				case "getPhoto" : 
					this.getPhoto(title, dataobj, ctype, value);
					break; 
				case "getCondition" : 
					this.getCondition(title, dataobj, ctype, value);
					break; 
				case "getTaille" : 
					this.getTaille(title, dataobj, ctype, value);
					break; 
				case "getPoids" : 
					this.getPoids(title, dataobj, ctype, value);
					break; 
				case "getCheveux" : 
					this.getCheveux(title, dataobj, ctype, value);
					break; 
				case "getYeux" : 
					this.getYeux(title, dataobj, ctype, value);
					break; 
				case "getStyle" : 
					this.getStyle(title, dataobj, ctype, value);
					break; 
				case "getOrig" : 
					this.getOrig(title, dataobj, ctype, value);
					break; 
				case "getZodiac" : 
					this.getZodiac(title, dataobj, ctype, value);
					break; 
				case "getLanguages" : 
					this.getLanguages(title, dataobj, ctype, value);
					break; 						
				case "getEtudes" : 
					this.getEtudes(title, dataobj, ctype, value);
					break; 
				case "getOccupation" : 
					this.getOccupation(title, dataobj, ctype, value);
					break; 
				case "getSecteur" : 
					this.getSecteur(title, dataobj, ctype, value);
					break; 
				case "getCigarette" : 
					this.getCigarette(title, dataobj, ctype, value);
					break; 
				case "getAlcool" : 
					this.getAlcool(title, dataobj, ctype, value);
					break; 
				case "getRevenu" : 
					this.getRevenu(title, dataobj, ctype, value);
					break; 
				case "getEnfants" : 
					this.getEnfants(title, dataobj, ctype, value);
					break; 
				case "getDesire" : 
					this.getDesire(title, dataobj, ctype, value);
					break; 
				case "getPseudoRecherche" : 
					this.getPseudoRecherche(title, dataobj, ctype, value);
					break; 	
				case "getExact" : 
					this.getExact(title, dataobj, ctype, value);
					break; 
				default :
					break;
				}
			}
		//some clean up	
		xmlNode = null;
		delete xmlNode;	
		//Debug('TEMPLATE_XML_FINISH(' + ctype + ')');
		
		//draw a bg for scroll mouse over
		this.setBgForScroll(ctype);
		};
		
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getJeRecherche(strTitle:String, strName:String, ctype:String, value, checkBox:Boolean):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('jeRecherche', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		if(!checkBox){ //if not a check box but an optionsbox
			var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
			mc._y = this.__h[ctype];
			mc._x = this.__hvSpacer;
			var sexe:CieOptionBox = new CieOptionBox(mc, this.__obj[strName], 'group');
			//set the default values
			sexe.setSelectionValue(value);
			this.__h[ctype] += mc._height + this.__hvSpacer;	
			this.__arrForm[ctype].push({__name:sexe, __depth:mc.getDepth(), __mc:mc});
		}else{
			var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
			mc._y = this.__h[ctype];
			mc._x = this.__hvSpacer;
			var sexe:CieCheckBoxGroup = new CieCheckBoxGroup(mc, this.__obj[strName]);			
			//reverse the string because flash loop backward
			var arrTmp = value.split(',');
			value = '';
			for(var o in arrTmp){
				value += arrTmp[o] + ','; 
				}
			value = value.substr(0, value.length - 1);	
			//set the default values
			sexe.setSelectionValue(value);
			this.__h[ctype] += mc._height + this.__hvSpacer;	
			this.__arrForm[ctype].push({__name:sexe, __depth:mc.getDepth(), __mc:mc});
			}
		};
		
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getOrientation(strTitle:String, strName:String, ctype:String, value, checkBox:Boolean):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('orientation', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		if(!checkBox){ //if not a check box but an optionsbox
			var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
			mc._y = this.__h[ctype];
			mc._x = this.__hvSpacer;
			var orientation:CieOptionBox = new CieOptionBox(mc, this.__obj[strName], 'group');
			//set the default values
			orientation.setSelectionValue(value);
			this.__h[ctype] += mc._height + this.__hvSpacer;	
			this.__arrForm[ctype].push({__name:orientation, __depth:mc.getDepth(), __mc:mc});
		}else{
			var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
			mc._y = this.__h[ctype];
			mc._x = this.__hvSpacer;
			var orientation:CieCheckBoxGroup = new CieCheckBoxGroup(mc, this.__obj[strName]);
			//reverse the string because flash loop backward
			var arrTmp = value.split(',');
			value = '';
			for(var o in arrTmp){
				value += arrTmp[o] + ','; 
				}
			value = value.substr(0, value.length - 1);
			//set the default values
			orientation.setSelectionValue(value);
			this.__h[ctype] += mc._height + this.__hvSpacer;	
			this.__arrForm[ctype].push({__name:orientation, __depth:mc.getDepth(), __mc:mc});
			}
		};
		
	/*************************************************************************************************************************************************/		
	
	//CHECKOK
	public function getAge(strTitle:String, strName:String, ctype:String, value, checkBox:Boolean):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('age', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		if(!checkBox){
			var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
			mc._y = this.__h[ctype];
			mc._x = this.__hvSpacer;
			mc.callBackFunc = function(obj:Object):Void{
				obj.__class.changeDepth(obj.__mv, obj.__ctype);
				};
			var age:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
			//set the default values
			age.setSelectionValue(value);
			mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
			this.__h[ctype] += mvHeight + this.__hvSpacer;	
			this.__arrForm[ctype].push({__name:age, __depth:mc.getDepth(), __mc:mc});
		}else{
			var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
			mc._y = this.__h[ctype];
			mc._x = this.__hvSpacer;
			var age:CieCheckBoxGroup = new CieCheckBoxGroup(mc, this.__obj[strName]);
			//reverse the string because flash loop backward
			var arrTmp = value.split(',');
			value = '';
			for(var o in arrTmp){
				value += arrTmp[o] + ','; 
				}
			value = value.substr(0, value.length - 1);
			//set the default values
			age.setSelectionValue(value);
			mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
			this.__h[ctype] += mc._height + this.__hvSpacer;	
			this.__arrForm[ctype].push({__name:age, __depth:mc.getDepth(), __mc:mc});
			}
		};
		
	/*************************************************************************************************************************************************/	
	
	//CHECKOK	
	public function getCountry(strTitle:String, strName:String, ctype:String, value, clist:Boolean):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('country', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		if(!clist){
			var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
			mc._y = this.__h[ctype];
			mc._x = this.__hvSpacer;
			mc.callBackFunc = function(obj:Object):Void{
				obj.__class.changeDepth(obj.__mv, obj.__ctype);
				if(obj.__bchanged){
					obj.__class.changeRegionList(obj.__ctype);
					}
				};
			var country:CieSelectBox = new CieSelectBox(mc, gListPays, 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
			
			//keep a ref for onChange event from country SelectBox
			this.__arrRefCountryBox[ctype] = country;
			//set the default values
			country.setSelectionValue(value);
			mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
			this.__h[ctype] += mvHeight + this.__hvSpacer;	
			this.__arrForm[ctype].push({__name:country, __depth:mc.getDepth(), __mc:mc});
		}else{
			//for the list
			var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
			mc._y = this.__h[ctype];
			mc._x = this.__hvSpacer;
			mc.callBackFunc = function(obj:Object):Void{
				obj.__class.changeRegionList_List(obj.__ctype);
				};
			var country:CieListBox = new CieListBox(mc, gListPays, this.__selectBoxWidth, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
			
			//keep a ref for onChange event from country SelectBox
			this.__arrRefCountryBox[ctype] = country;
			//set the default values
			country.setSelectionValue(value);
			mvHeight = this.__hBox + this.__hvSpacer + this.__listBoxHeight;
			this.__h[ctype] += mvHeight + this.__hvSpacer;	
			this.__arrForm[ctype].push({__name:country, __depth:mc.getDepth(), __mc:mc});
			}
		};
		
	/*************************************************************************************************************************************************/	
	
	//CHECKOK	
	public function getRegion(strTitle:String, strName:String, ctype:String, value, clist:Boolean):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('region', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		if(!clist){
			var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
			mc._y = this.__h[ctype];
			mc._x = this.__hvSpacer;
			mc.callBackFunc = function(obj:Object):Void{
				obj.__class.changeDepth(obj.__mv, obj.__ctype);
				if(obj.__bchanged){
					obj.__class.changeVilleList(obj.__ctype);
					}
				};
			var region:CieSelectBox = new CieSelectBox(mc, [[0,'------']], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
			//keep a ref for onChange event from country SelectBox
			this.__arrRefRegionBox[ctype] = region;
			//if value is different then 0 then have to base the ID on the country
			this.changeRegionList(ctype);
			//then set the default values
			region.setSelectionValue(value);
			mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
			this.__h[ctype] += mvHeight + this.__hvSpacer;	
			this.__arrForm[ctype].push({__name:region, __depth:mc.getDepth(), __mc:mc});
		}else{
			var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
			mc._y = this.__h[ctype];
			mc._x = this.__hvSpacer;
			mc.callBackFunc = function(obj:Object):Void{
				obj.__class.changeVilleList_List(obj.__ctype);
				};
			var region:CieListBox = new CieListBox(mc, arrRows, this.__selectBoxWidth, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
			//keep a ref for onChange event from country SelectBox
			this.__arrRefRegionBox[ctype] = region;
			//if value is different then 0 then have to base the ID on the country
			this.changeRegionList_List(ctype);
			//then set the default values
			region.setSelectionValue(value);
			mvHeight = this.__hBox + this.__hvSpacer + this.__listBoxHeight;
			this.__h[ctype] += mvHeight + this.__hvSpacer;	
			this.__arrForm[ctype].push({__name:region, __depth:mc.getDepth(), __mc:mc});
			}
		};
		
	/*************************************************************************************************************************************************/	
	
	//CHECKOK	
	public function getVille(strTitle:String, strName:String, ctype:String, value, clist:Boolean):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('ville', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		if(!clist){
			var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
			mc._y = this.__h[ctype];
			mc._x = this.__hvSpacer;
			mc.callBackFunc = function(obj:Object):Void{
				obj.__class.changeDepth(obj.__mv, obj.__ctype);
				};
			var ville:CieSelectBox = new CieSelectBox(mc, [[0,'------']], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
			//keep a ref for onChange event from region SelectBox
			this.__arrRefVilleBox[ctype] = ville;
			//if value is different then 0 then have to base the ID on the country
			this.changeVilleList(ctype);
			//then set the default values
			ville.setSelectionValue(value);
			mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
			this.__h[ctype] += mvHeight + this.__hvSpacer;	
			this.__arrForm[ctype].push({__name:ville, __depth:mc.getDepth(), __mc:mc});
		}else{
			var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
			mc._y = this.__h[ctype];
			mc._x = this.__hvSpacer;
			var ville:CieListBox = new CieListBox(mc, arrRows, this.__selectBoxWidth, 0, (this.__hvSpacer * 2.5));
			//keep a ref for onChange event from region SelectBox
			this.__arrRefVilleBox[ctype] = ville;
			//if value is different then 0 then have to base the ID on the country
			this.changeVilleList_List(ctype);
			//then set the default values
			ville.setSelectionValue(value);
			mvHeight = this.__hBox + this.__hvSpacer + this.__listBoxHeight;
			this.__h[ctype] += mvHeight + this.__hvSpacer;	
			this.__arrForm[ctype].push({__name:ville, __depth:mc.getDepth(), __mc:mc});
			}
		};
		
	/*************************************************************************************************************************************************/	
	
	//CHECKOK	
	public function getRelation(strTitle:String, strName:String, ctype:String, value, checkBox:Boolean):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('relation', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		if(!checkBox){
			var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
			mc._y = this.__h[ctype];
			mc._x = this.__hvSpacer;
			mc.callBackFunc = function(obj:Object):Void{
				obj.__class.changeDepth(obj.__mv, obj.__ctype);
				};
			var relation:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
			//set the default values
			relation.setSelectionValue(value);
			mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
			this.__h[ctype] += mvHeight + this.__hvSpacer;	
			this.__arrForm[ctype].push({__name:relation, __depth:mc.getDepth(), __mc:mc});
		}else{
			var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
			mc._y = this.__h[ctype];
			mc._x = this.__hvSpacer;
			//F*** EXCEPTION: we have to skip the first one because it have no value '0'=>'------------' and reverse the array
			var tmpObj:Array = new Array(); 
			for(var o in this.__obj[strName]){
				if(this.__obj[strName][o][0] != '0'){
					tmpObj[o] = [this.__obj[strName][o][0], this.__obj[strName][o][1]];
					}
				}
			var tmpObjReverse:Array = new Array(); 	
			for (var o in tmpObj){
				tmpObjReverse[o] = tmpObj[o];
				}	
			var relation:CieCheckBoxGroup = new CieCheckBoxGroup(mc, tmpObjReverse);
			//reverse the string because flash loop backward
			var arrTmp = value.split(',');
			value = '';
			for(var o in arrTmp){
				value += arrTmp[o] + ','; 
				}
			value = value.substr(0, value.length - 1);
			//set the default values
			relation.setSelectionValue(value);
			mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
			this.__h[ctype] += mc._height + this.__hvSpacer;	
			this.__arrForm[ctype].push({__name:relation, __depth:mc.getDepth(), __mc:mc});
			}
		};
		
	/*************************************************************************************************************************************************/	
	
	//CHECKOK	
	public function getApparence(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('apparence', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var apparence:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		apparence.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:apparence, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getEtatCivil(strTitle:String, strName:String, ctype:String, value):Void{	
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('etatcivil', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var etatcivil:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		etatcivil.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:etatcivil, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getPhoto(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('photo', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var photo:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		photo.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:photo, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getCondition(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('condition', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var condition:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		condition.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:condition, __depth:mc.getDepth(), __mc:mc});
		};
		
	/*************************************************************************************************************************************************/	
	
	//CHECKOK	
	public function getTaille(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('taille', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var taille:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		taille.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:taille, __depth:mc.getDepth(), __mc:mc});
		};
		
	/*************************************************************************************************************************************************/	
	
	//CHECKOK		
	public function getPoids(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('poids', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var poids:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		poids.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:poids, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getCheveux(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('cheveux', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var cheveux:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		cheveux.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:cheveux, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK	
	public function getYeux(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('yeux', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var yeux:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		yeux.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:yeux, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK		
	public function getStyle(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('style', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var style:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		style.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:style, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK	
	public function getOrig(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('orig', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var orig:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		orig.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:orig, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getZodiac(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('zodiac', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var zodiac:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		zodiac.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:zodiac, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getLanguages(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('languages', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var languages:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		languages.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:languages, __depth:mc.getDepth(), __mc:mc});
		};
		
	/*************************************************************************************************************************************************/	
	
	//CHECKOK	
	public function getEtudes(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('etudes', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var etudes:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		etudes.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:etudes, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getOccupation(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('occupation', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var occupation:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		occupation.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:occupation, __depth:mc.getDepth(), __mc:mc});
		};
		
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getSecteur(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('secteur', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var secteur:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		secteur.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:secteur, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getCigarette(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('cigarette', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var cigarette:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		cigarette.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:cigarette, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getAlcool(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('alcool', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var alcool:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		alcool.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:alcool, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getRevenu(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('revenu', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var revenu:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		revenu.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:revenu, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getEnfants(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('enfants', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var enfants:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		enfants.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:enfants, __depth:mc.getDepth(), __mc:mc});
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getDesire(strTitle:String, strName:String, ctype:String, value):Void{
		var mc:MovieClip = this.__arrContentDataLayer[ctype].createEmptyMovieClip('desire', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var textline:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield', strTitle, 'dynamic',[true,false,false], false, false, false, false);	
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		mc.callBackFunc = function(obj:Object):Void{
			obj.__class.changeDepth(obj.__mv, obj.__ctype);
			};
		var desire:CieSelectBox = new CieSelectBox(mc, this.__obj[strName], 5, this.__selectBoxWidth, 100, 0, (this.__hvSpacer * 2.5), mc.callBackFunc, {__class:this, __ctype:ctype, __mv:mc});
		//set the default values
		desire.setSelectionValue(value);
		mvHeight = this.__hBox + this.__hvSpacer + this.__selectBoxHeight;
		this.__h[ctype] += mvHeight + this.__hvSpacer;	
		this.__arrForm[ctype].push({__name:desire, __depth:mc.getDepth(), __mc:mc});
        }; 
		
	/*************************************************************************************************************************************************/
	
	//CHECKOK
	public function getPseudoRecherche(strTitle:String, strName:String, ctype:String, value):Void{
		var mc = this.__arrContentDataLayer[ctype].createEmptyMovieClip('pseudoRecherche', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var pseudo:CieTextLine = new CieTextLine(mc, 0, 0, 0, this.__hBox, 'textfield1', strTitle, 'dynamic',[true,false,false], false, false, false, false);
		var pseudoRecherche = new CieTextLine(mc, 0, (pseudo.getHeight() + this.__hvSpacer), this.__selectBoxWidth, this.__hBox, 'textfield2', '', 'input',[], false, true, false, true);
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		this.__h[ctype] += mc._height + this.__hvSpacer;
		this.__arrForm[ctype].push({__name:pseudoRecherche, __depth:mc.getDepth(), __mc:mc});
		//set le focus
		Selection.setFocus(pseudoRecherche.getTextField());
		};
	
	/*************************************************************************************************************************************************/	
	
	//CHECKOK
	public function getExact(strTitle:String, strName:String, ctype:String, value:String):Void{
		var mc = this.__arrContentDataLayer[ctype].createEmptyMovieClip('exact', this.__arrContentDataLayer[ctype].getNextHighestDepth());
		var exact:CieCheckBox = new CieCheckBox(mc, [strTitle, Boolean(value)]);
		mc._y = this.__h[ctype];
		mc._x = this.__hvSpacer;
		this.__h[ctype] += mc._height + this.__hvSpacer;
		this.__arrForm[ctype].push({__name:exact, __depth:mc.getDepth(), __mc:mc});
        }; 
		

	/*************************************************************************************************************************************************/	
	
	public function sendForm(ctype:String):Void{
		//if the loader layer is undefined, we have completed loading the form so we can send it 
		//because it's deleted once the form is completed
		if(this.__arrContentLoaderLayer[ctype] == undefined){		
			var str:String = '';
			if(ctype != 'critere'){ //pour les recherches
				//if recherche detaille arreter l'interva qui construit le form
				if(ctype == 'detaillees'){
					clearInterval(this.__arrIntervalParsing[ctype]);
					}
				//loop trough the form
				for(var i=0; i < this.__arrForm[ctype].length; i++){
					if(this.__arrForm[ctype][i].__name != undefined && this.__arrForm[ctype][i].__button != true){
						//fill the string with coma seperated
						str += this.__arrForm[ctype][i].__name.getSelectionValue() + ',';
						}
					}
				//strip the last virgule	
				str = str.substr(0,	(str.length - 1));
				//send the queryString to the CiewRecherche that will build the Request and manage the result
				secRecherche.buildRequest(str, ctype);
			}else{	//pour lesa criteres du salon
				//loop trough the form
				for(var i=0; i < this.__arrForm[ctype].length; i++){
					if(this.__arrForm[ctype][i].__name != undefined && this.__arrForm[ctype][i].__button != true){
						//have to reverse the f** array
						var bEmpty:Boolean = true;
						arrValues = this.__arrForm[ctype][i].__name.getSelectionValue(); 
						for(o in arrValues){
							str += arrValues[o] + ',';
							bEmpty = false;
							}
						//strip the last virgule
						if(!bEmpty){	
							str = str.substr(0,	(str.length - 1));	
						}else{
							str += '0';
							}
						//fill the string with pipe seperated
						str += '|';
						}
					}
				//strip the last coma or pipe	
				str = str.substr(0,	(str.length - 1));
				//send the queryString to the CiewRecherche that will build the Request and manage the result
				secSalon.buildRequest(str, ctype);
				}
			}
		};
	
	
	/*************************************************************************************************************************************************/	
	
	//ramene le data du formulaire detailles afficher pour faire une sauvegarde de recherche
	public function getFormData(ctype:String):String{
		this.__arrForm[ctype];
		//loop trough the form
		var str:String = '';
		for(var i=0; i < this.__arrForm[ctype].length; i++){
			if(this.__arrForm[ctype][i].__name != undefined && this.__arrForm[ctype][i].__button != true){
				//fill the string with coma seperated
				str += this.__arrForm[ctype][i].__name.getSelectionValue() + ',';
				}
			}
		//strip the last virgule	
		return str.substr(0, (str.length - 1));
		};	
	
	/*************************************************************************************************************************************************/
	
	public function changeRegionList(ctype:String):Void{
		//check if we have a region selectBox
		if(this.__arrRefRegionBox[ctype] != undefined){
			//empty the list of region
			this.__arrRefRegionBox[ctype].removeAll();
			//get the country selected
			var strCodePays:String = this.__arrRefCountryBox[ctype].getSelectionValue();
			if(gListRegions[strCodePays].length != 0){
				this.__arrRefRegionBox[ctype].fillList(gListRegions[strCodePays], 5);
			}else{
				this.__arrRefRegionBox[ctype].fillList([[0,'-------']], 5);
				}
			//put the default value
			this.__arrRefRegionBox[ctype].setSelectionValue(0);
			//change the ville select Box because we change the region
			this.changeVilleList(ctype);
			}
		};
	
		
	/*************************************************************************************************************************************************/	
	
	public function changeVilleList(ctype:String):Void{
		//check if we have a ville selectBox
		if(this.__arrRefVilleBox[ctype] != undefined){
			//empty the list of ville
			this.__arrRefVilleBox[ctype].removeAll();
			//get the region and country selected
			var strCodePays:String = this.__arrRefCountryBox[ctype].getSelectionValue();
			var strRegionID:String = this.__arrRefRegionBox[ctype].getSelectionValue();
			if(gListVilles[strCodePays][strRegionID].length != 0){
				this.__arrRefVilleBox[ctype].fillList(gListVilles[strCodePays][strRegionID], 5);
			}else{
				this.__arrRefVilleBox[ctype].fillList([[0,'-------']], 5);
				}
			//put the default value
			this.__arrRefVilleBox[ctype].setSelectionValue(0);
			}
		};
	
	/*************************************************************************************************************************************************/		
		
	public function changeRegionList_List(ctype:String):Void{
		//check if we have a region selectBox
		if(this.__arrRefRegionBox[ctype] != undefined){
			//empty the list of region
			this.__arrRefRegionBox[ctype].removeAll();
			//get the country selected
			var arrCodePays:Array = this.__arrRefCountryBox[ctype].getSelectionValue();
			if(arrCodePays.length == 1){
				if(gListRegions[arrCodePays[0]] != '' && gListRegions[arrCodePays[0]] != undefined){
					this.__arrRefRegionBox[ctype].fillList(gListRegions[arrCodePays[0]]);
				}else{
					this.__arrRefRegionBox[ctype].fillList([0,'-------']);
					}
				//put the default value
				this.__arrRefRegionBox[ctype].setSelectionValue('0');
				//change the ville select Box because we change the region
				}
			this.changeVilleList_List(ctype);	
			}	
		};	
	
	/*************************************************************************************************************************************************/	
	
	public function changeVilleList_List(ctype:String):Void{
		//check if we have a ville selectBox
		if(this.__arrRefVilleBox[ctype] != undefined){
			//empty the list of ville
			this.__arrRefVilleBox[ctype].removeAll();
			//get the region and country selected
			var arrCodePays:Array = this.__arrRefCountryBox[ctype].getSelectionValue();
			if(arrCodePays.length == 1){
				var arrRegionID:Array = this.__arrRefRegionBox[ctype].getSelectionValue();
				if(arrRegionID.length == 1){
					if(gListVilles[arrCodePays[0]][arrRegionID[0]] != '' && gListVilles[arrCodePays[0]][arrRegionID[0]] != undefined){
						this.__arrRefVilleBox[ctype].fillList(gListVilles[arrCodePays[0]][arrRegionID[0]]);
					}else{
						this.__arrRefVilleBox[ctype].fillList([0,'-------']);
						}
	
					//put the default value
					this.__arrRefVilleBox[ctype].setSelectionValue('0');
					}
				}	
			}
		};	
		
	/*************************************************************************************************************************************************/	

	private function changeDepth(mv:MovieClip, ctype:String):Void{
		var maxDepth = mv;
		var minDepth = mv.getDepth();
		for(var i = 0; i < this.__arrForm[ctype].length; i++){
			if(this.__arrForm[ctype][i].__depth > maxDepth.getDepth()){
				maxDepth = this.__arrForm[ctype][i].__mc;	
				//mettre les choix du selectBox qui avait le focus a invisible
				this.__arrForm[ctype][i].__name.hideChoices();
				}
			}
		mv.swapDepths(maxDepth);
		for(var i = 0; i < this.__arrForm[ctype].length; i++){
			if (this.__arrForm[ctype][i].__mc == maxDepth){		
				this.__arrForm[ctype][i].__depth = minDepth;
			}else if (this.__arrForm[ctype][i].__mc == mv){
				this.__arrForm[ctype][i].__depth = mv.getDepth();
				}
			}
		};	
	
	/*************************************************************************************************************************************************/
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieFormManager{
		return this;
		};
	*/	
	};
