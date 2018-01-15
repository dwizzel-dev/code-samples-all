/*

manage le conrnu de l'application au complet tout les panel autrement dit

*/

import manager.CieTabManager;
import control.CiePanel;

dynamic class manager.CieContentManager{
	
	static private var __className:String = "CieContentManager";
	static private var __instance:CieContentManager;
	private var __contentTree:Object;
	private var __contentParsed:Boolean;
	
	private  function CieContentManager(){
		//nothing it's a singleton instanciate with getInstance instead
		this.__contentParsed = false;
		this.init();
		};
		
	static public function getInstance(Void):CieContentManager{
		if(__instance == undefined) {
			__instance = new CieContentManager();
			}
		return __instance;
		};	
		
		
	public function registerTabManager(tabManager:Object):Void{
		this.__contentTree.__tabManager = tabManager;
		};
		
	public function init(Void):Void{
		this.__contentTree = new Object();
		};
		
	public function isLoaded(Void):Boolean{
		return this.__contentParsed;
		};	
		
	public function createFromXmlFile(fileName:String):Void{
		var xmlFile:XML = new XML();
		xmlFile.watch('loaded', objLoaded, {__super:this});
		xmlFile.ignoreWhite = true;
		xmlFile.load(fileName);
		function objLoaded(prop, oldVal:Number, newVal:Number, obj:Object){
			if(newVal){
				obj.__super.createFromXml(obj.__super.__contentTree, this.firstChild, '');
				this.unwatch('loaded');
				delete this;
				}
			return newVal;
			};
		};
		
	private function createFromXml(xmlObject:Object, xmlNode:XMLNode, spacer:String):Void{	
		this.__contentTree.__node = xmlNode;
		this.__contentParsed = true;
		};
		
	private function parsePanel(contentTree:Object, tree:Array, xmlNode:XMLNode):Void{
		var key = '';
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.nodeName == 'P'){
				var pname = currNode.attributes.n;
				if(contentTree[pname] == undefined){
					contentTree[pname] = new Object();
					contentTree[pname].__node = currNode;
					
					contentTree[pname].__class = contentTree.__tabManager.getPanelClass(contentTree.__name, pname);
					contentTree[pname].__class.watch('__destroyed', this.watchRemovedContentNode, {__super:this, __contentT: contentTree, __keyName: pname});
					
					//scrool bar or not
					if(currNode.attributes.scroll == 'false'){
						contentTree[pname].__class.createScrollPane(false);
						//contentTree[pname].__class.disableScroll();
					}else{
						contentTree[pname].__class.createScrollPane(true);
						}
					//for the detailedProfil ref is bby id of noPublique
					if(currNode.attributes.id != undefined){
						contentTree[pname].__id =  currNode.attributes.id;
						}
					//content movie clip
					if(currNode.attributes.content != undefined  && currNode.attributes.content != ''){
						contentTree[pname].__class.setContent(currNode.attributes.content);
						}
					//effect glow or not
					if(currNode.attributes.effect == 'false'){
						contentTree[pname].__class.setGlow(false);
						}	
					//bg color	
					if(currNode.attributes.bgcolor != undefined  && currNode.attributes.bgcolor != ''){	
						contentTree[pname].__class.setBgColor(currNode.attributes.bgcolor);
						}
					}
				//set the tab focus to redraw/resize of none set use last one
				if(contentTree.__tabFocus != undefined && contentTree.__tabFocus != ''){
					contentTree.__tabManager.setTabFocus(contentTree.__tabFocus);		
				}else{
					contentTree.__tabManager.setTabFocus(contentTree.__name);		
					}
				this.parsePanel(contentTree[pname], tree, contentTree[pname].__node);	
				}
			
			if(currNode.nodeName == 'PT'){
				if(key == '' && tree.length != 0){
					key = tree.shift();
					}
				var pname = currNode.attributes.n;
				if(contentTree[pname] == undefined){
					contentTree[pname] = new Object();
					contentTree[pname].__node = currNode;
					contentTree[pname].__name = currNode.attributes.n;
					if(contentTree.__tabManager == undefined){
						//widtyh and height base on the parent panel size
						//since a panel tab always come after a panel in the treeCOntentObject /TAB/PANEL/PANELTAB/PANEL/PANELTAB/PANEL...etc...
						contentTree.__tabManager = new CieTabManager(contentTree.__class.getPanelContent(),contentTree.__class.getPanelSize().__width, contentTree.__class.getPanelSize().__height, CieStyle.__tabPanel.__tabBorderOffSet, Number(currNode.attributes.ystart));
						contentTree.__class.registerObject(contentTree.__tabManager);
						}
					contentTree[pname].__tabManager = contentTree.__tabManager;
					//for removing from the tree on removeTab trigger
					contentTree[pname].__tabManager.watch('__lastRemovedTab', this.watchRemovedContentNode, {__super:this, __contentT: contentTree});
					contentTree[pname].__tabManager.createPanelTab(pname, currNode.attributes.title, currNode.attributes.closebutt);
					contentTree[pname].__tabManager.createPanels(pname, currNode.attributes.model);
					}
				contentTree[pname].__tabFocus = key;	
				this.parsePanel(contentTree[pname], tree, contentTree[pname].__node);
				}
				
			if(currNode.nodeName == 'S'){
				if(key == '' && tree.length != 0){
					key = tree.shift();
					}
				var pname = currNode.attributes.n;	
				if(pname == key){
					if(contentTree[key] == undefined){
						contentTree[pname] = new Object();
						contentTree[pname].__node = currNode;
						contentTree[pname].__name = currNode.attributes.n;
						contentTree[pname].__tabManager = contentTree.__tabManager;
						//for removing from the tree on removeTab trigger
						contentTree[pname].__tabManager.watch('__lastRemovedTab', this.watchRemovedContentNode, {__super:this, __contentT: contentTree});
						contentTree[pname].__tabManager.createSection(pname);
						contentTree[pname].__tabManager.createPanels(pname, currNode.attributes.model);
						}
					//if tis the good key then go throught the tree
					this.parsePanel(contentTree[pname], tree, contentTree[pname].__node);	
					}
				}
			}
		};
		
	public function watchRemovedContentNode(prop, oldVal:Boolean, newVal:Boolean, obj:Object):Boolean{
		if(prop == '__lastRemovedTab'){
			obj.__super.removeContentNode(newVal, obj.__contentT);
		}else if(prop == '__destroyed'){
			if(newVal){
				obj.__super.removeContentNode(obj.__keyName, obj.__contentT);
				}
			}
		return newVal;
		};
	
	private function removeContentNode(kName:String, contentObj:Object):Void{
		//trace("~RM NODE:" + kName);
		contentObj[kName] = null;
		delete contentObj[kName];
		//this.displayContentTree();
		};
	
	private function displayContentTree(Void):Void{
		Debug("------------ PANEL CONTENT TREE ------------");
		var arrDisplay:Array = new Array();
		this.traceObject(this.__contentTree, arrDisplay, '');
		for(var k in arrDisplay){
			Debug(arrDisplay[k]);
			}
		arrDisplay = null;
		delete arrDisplay;
		Debug("------------------------\n\n");
		};
		
	public  function changeNodeValue(xmlNode:XMLNode, arrPanel:Array){
		this.goIntoTheTree(xmlNode, arrPanel, this.__contentTree);
		//this.displayContentTree();
		};
		
	private function goIntoTheTree(xmlNode:XMLNode, arrPanel:Array, cTree:Object):Boolean{
		var key = arrPanel.shift();
		if(arrPanel.length > 0){
			if(this.goIntoTheTree(xmlNode, arrPanel, cTree[key])){
				this.parsePanel(cTree[key], [], xmlNode);
				}
		}else{
			cTree[key] = undefined;
			return true;	
			}
		return false;
		};
	
	private function traceObject(obj:Object, arrDisplay:Array, spacer:String):Void{
		for(var o in obj){
			if(typeof(obj[o]) != 'object'){
				if(o == '__name'){
					arrDisplay.push(spacer + obj[o]);
					}
			}else{
				if(o != '__node' && o != '__registeredObject'){ //because loop for ever on the __registredObject since it's recursive
					this.traceObject(obj[o], arrDisplay, spacer + "\t");
					}
				}
			}
		};	
	
	public function openTab(arrParam:Array):Void{
		this.parsePanel(this.__contentTree, arrParam, this.__contentTree.__node);
		//this.displayContentTree();
		};
	
	public function getPanelClass(arrPanel:Array):CiePanel{
		var obj:Object = this.__contentTree;
		for(var i=0; i<arrPanel.length; i++){
			obj = obj[arrPanel[i]];
			}
		if(obj.__class.getClassName() == 'CiePanel'){
			return obj.__class;	
			}
		return null;	
		};	
		
	public function getPanelObject(arrPanel:Array):Object{
		var obj:Object = this.__contentTree;
		for(var i=0; i<arrPanel.length; i++){
			obj = obj[arrPanel[i]];
			}
		if(typeof(obj) == 'object'){
			return obj;	
			}
		return null;	
		};	
		
	public function getPanelTabManager(arrPanel:Array):CieTabManager{
		var obj:Object = this.__contentTree;
		for(var i=0; i<arrPanel.length; i++){
			obj = obj[arrPanel[i]];
			}
		if(typeof(obj) == 'object'){
			return obj.__tabManager;	
			}
		return null;	
		};		
		
	public function getPanelID(arrPanel:Array):Number{
		var obj:Object = this.__contentTree;
		for(var i=0; i<arrPanel.length; i++){
			obj = obj[arrPanel[i]];
			}
		if(obj.__class.getClassName() == 'CiePanel'){
			return obj.__id;	
			}
			
		return null;	
		};	

	public function getClassName(Void):String{
		return __className;
		};
		
	public function getClass(Void):CieContentManager{
		return this;
		};
	}