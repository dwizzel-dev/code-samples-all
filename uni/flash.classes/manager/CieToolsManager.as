/*

tool bar manager

can be on the Stage or in a Panel 
dont forget to register the object for the resize event

*/

import control.CieToolsGroup;
import control.CieTools;

dynamic class manager.CieToolsManager{
	
	static private var __className:String = 'CieToolsManager';
	static private var __toolBarSeparator:Object = {__l:0.5, __r:0.5};
	
	private var __mv:MovieClip;
	private var __offSetX:Number;
	private var __offSetY:Number;
	private var __lastW:Number;
	private var __lastH:Number;
	private var __nextX:Number;
	
	private var __toolBar:MovieClip;
	private var __bgToolBar:MovieClip;
	private var __barPosY:Number;
	private var __toolsGroup:Array;
	private var __margeWidth:Number;
	
	public function CieToolsManager(mv:MovieClip, w:Number, h:Number, x:Number, y:Number){
		this.__offSetX = x;
		this.__offSetY = y;
		this.__barPosY = y;
		this.__mv = mv;
		this.__lastW = w;
		this.__lastH = h;
		this.__xmlToObject = new Object();
		this.__toolsGroup = new Array();
		this.__margeWidth = 10;
		this.__nextX = 0;
		this.createLayer();
		};
		
	public function removeAllTools(Void):Void{
		for(var o in this.__toolsGroup){
			this.__toolsGroup[o]['__class'].removeToolGroup();
			delete this.__toolsGroup[o]['__class'];
			delete this.__toolsGroup[o];
			}
		};
	
	public function createLayer(Void):Void{
		if(this.__toolBar == undefined){
			var toolBarKey = Math.round(Math.random() * (1000000-0)) + 1;
			this.__toolBar = this.__mv.createEmptyMovieClip('TOOLBAR_' + toolBarKey, this.__mv.getNextHighestDepth());
			}
		};
		
	public function moveY(y:Number):Void{
		this.__offSetY = y;
		};
		
	public function createFromXmlFile(fileName:String):Void{
		//Debug("LOADED:" + fileName);
		
		var xmlFile:XML = new XML();
		xmlFile.watch('loaded', objLoadedTools, {__super:this});
		xmlFile.ignoreWhite = true;
		xmlFile.load(fileName);
		function objLoadedTools(prop, oldVal:Number, newVal:Number, obj:Object){
			if(prop == 'loaded' && newVal){
				obj.__super.createFromXml(this.firstChild);
				this.unwatch('loaded');
				delete this;
				}
			return newVal;
			};
		};
		
	public function createFromXml(xmlNode:XMLNode):Void{	
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			var objName:String = currNode.attributes.n;
			if(currNode.firstChild.nodeType != 3){
				if(currNode.nodeName == 'TOOLGROUP'){
					this.createToolsGroup(currNode.attributes.n, currNode.attributes.align);
					this.createFromXml(currNode);
				}else if(currNode.nodeName == 'TOOL'){
					var toolGroup = currNode.parentNode.attributes.n;
					this.insertTool(toolGroup, currNode.attributes.n, currNode.attributes.type, currNode.attributes.icon, currNode.attributes.style);
					this.createFromXml(currNode);
				}else if(currNode.nodeName == 'BUTTON'){
					var toolGroup = currNode.parentNode.attributes.n;
					this.insertButton(toolGroup, currNode.attributes.n, currNode.attributes.type, currNode.attributes.text, currNode.attributes.style);
					this.createFromXml(currNode);	
				}else if(currNode.nodeName == 'ACTION'){
					var toolGroup = currNode.parentNode.parentNode.attributes.n;
					var tool = currNode.parentNode.attributes.n;
					var arrParams = new Array();
					for(var j=0; j<currNode.childNodes.length; j++){
						arrParams[j] = currNode.childNodes[j].firstChild.nodeValue;
						}
					this.setAction(toolGroup, tool, [currNode.attributes.n, currNode.attributes.func, arrParams]);
				}else if(currNode.nodeName == 'BUBBLE'){
					//Debug('FOUND BUBBLE: ' + currNode.attributes.t);
					var toolGroup = currNode.parentNode.parentNode.attributes.n;
					var tool = currNode.parentNode.attributes.n;
					this.setBubble(toolGroup, tool, currNode.attributes.t);
					}
				}
			}
		};	
		
	public function insertTool(toolGroupName:String, toolName:String, toolType:String, toolIcon:String):Void{
		if(this.__toolsGroup[toolGroupName] != undefined){
			this.__toolsGroup[toolGroupName]['__class'].insertTool(toolName, toolType, toolIcon);
			}
		this.resize(this.__lastW, this.__lastH);	
		};
		
	public function insertButton(toolGroupName:String, toolName:String, toolType:String, toolText:String, toolStyle:String):Void{
		if(this.__toolsGroup[toolGroupName] != undefined){
			this.__toolsGroup[toolGroupName]['__class'].insertButton(toolName, toolType, toolText, toolStyle);
			}
		this.resize(this.__lastW, this.__lastH);	
		};	
		
	public function	setAction(toolGroupName:String, toolName:String, arrActions:Array):Void{
		if(this.__toolsGroup[toolGroupName] != undefined){
			this.__toolsGroup[toolGroupName]['__class'].setAction(toolName, arrActions);
			}
		};	
		
	public function	setBubble(toolGroupName:String, toolName:String, strBubbleText:String):Void{
		if(this.__toolsGroup[toolGroupName] != undefined){
			this.__toolsGroup[toolGroupName]['__class'].setBubble(toolName, strBubbleText);
			}
		};	
		
	public function	getIcon(toolGroupName:String, toolName:String):MovieClip{
		if(this.__toolsGroup[toolGroupName] != undefined){
			return this.__toolsGroup[toolGroupName]['__class'].getIcon(toolName);
			}
		};		
		
	public function	getTool(toolGroupName:String, toolName:String):CieTools{
		if(this.__toolsGroup[toolGroupName] != undefined){
			return this.__toolsGroup[toolGroupName]['__class'].getTool(toolName);
			}
		};		
		
	public function createToolsGroup(toolGroupName:String, posLR:String):Boolean{
		if(this.__toolsGroup[toolGroupName] == undefined){
			this.__toolsGroup[toolGroupName] = new Array();
			if(posLR == 'left'){
				this.__toolsGroup[toolGroupName]['__align'] = 'left';
			}else{
				this.__toolsGroup[toolGroupName]['__align'] = 'right';
				}
			this.__toolsGroup[toolGroupName]['__class'] = new CieToolsGroup(this.__toolBar, this.__lastH);
			this.__toolsGroup[toolGroupName]['__super'] = this;	
			return true;
			}
		return false;	
		};
		
	public function drawBgToolBar(Void):Void{
		if(this.__bgToolBar == undefined){
			this.__bgToolBar = this.__toolBar.createEmptyMovieClip('BG', this.__toolBar.getNextHighestDepth());
			if(typeof(CieStyle.__toolbar.__mvToolBar) == 'string'){
				this.__bgToolBar.attachMovie(CieStyle.__toolbar.__mvToolBar, 'BG2', this.__toolBar.getNextHighestDepth(), {_alpha: CieStyle.__toolbar.__mvToolBarAlpha});
				}
			this.__bgToolBar._x = 0;
			this.__bgToolBar._y = 0;
			}
		this.__bgToolBar.clear();
		
		//draw fill directly
		this.__bgToolBar.beginFill(CieStyle.__toolbar.__bgColor, 100);
		this.__bgToolBar.moveTo(0, 0);
		this.__bgToolBar.lineTo(this.__lastW, 0);
		this.__bgToolBar.lineTo(this.__lastW, this.__lastH);
		this.__bgToolBar.lineTo(0, this.__lastH);
		this.__bgToolBar.lineTo(0, 0);
		this.__bgToolBar.endFill();
		
		//draw line directly
		if(CieStyle.__toolbar.__borderWidth > 0){
			this.__bgToolBar.lineStyle(CieStyle.__toolbar.__borderWidth, CieStyle.__toolbar.__borderColor, 100);
			this.__bgToolBar.moveTo(0, this.__lastH - (CieStyle.__toolbar.__borderWidth / 2));
			this.__bgToolBar.lineTo(this.__lastW, this.__lastH - (CieStyle.__toolbar.__borderWidth / 2));
			}
		
		//offset
		this.__bgToolBar._x = this.__offSetX;
		};
		
	public function resize(w:Number, h:Number):Void{
		this.__nextX = this.__offSetX;
		this.__prevX = w;
		for(var o in this.__toolsGroup){
			if(this.__toolsGroup[o] != undefined){
				if(this.__toolsGroup[o]['__align'] == 'left'){
					this.__toolsGroup[o]['__class'].moveGroupTo(this.__nextX + this.__margeWidth, this.__offSetY); 
					this.__nextX += this.__toolsGroup[o]['__class'].getWidth() + this.__margeWidth ;
				}else{
					this.__prevX -= (this.__toolsGroup[o]['__class'].getWidth() + this.__margeWidth);
					this.__toolsGroup[o]['__class'].moveGroupTo(this.__prevX, this.__offSetY); 
					}
				}
			}
		this.__lastW = w;
		if(this.__bgToolBar != undefined){
			this.drawBgToolBar();
			}
		};	
		
	public function redraw(toolGroupName:String):Void{
		this.__toolsGroup[toolGroupName]['__class'].redraw();
		if(this.__toolsGroup[toolGroupName]['__align'] == 'right'){ //because we have to move the group to the left insteaad so recalculate base on last W
			this.resize(this.__lastW, 0);
			}
		};
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieToolsManager{
		return this;
		};
	}	