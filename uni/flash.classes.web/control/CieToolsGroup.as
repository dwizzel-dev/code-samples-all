/*

groupement d'outils pour faciliter le deplacement de ceux ci

*/

import control.CieButton;
import control.CieTools;
//import graphic.CieLine;

dynamic class control.CieToolsGroup{
	
	static private var __className:String = 'CieToolsGroup';
	private var __toolGroup:MovieClip;
	private var __tools:Array;
	private var __mv:MovieClip;
	private var __nextX:Number;
	private var __offSetY:Number;
	private var __align:String;	
	private var __totalWidth:Number;
	private var __spacerTool:Number;	

	public function CieToolsGroup(mv:MovieClip, offSetY:Number, align:String){
		this.__mv = mv;
		this.__offSetY = offSetY;
		this.__align = align;
		this.__totalWidth = 0;
		this.__nextX = 0;
		this.__spacerTool = 4;
		
		this.__tools = Array();
		this.__toolGroup = this.__mv.createEmptyMovieClip('TOOLGROUP_' + (Math.round(Math.random() * (10000000-0)) + 1), this.__mv.getNextHighestDepth());
		};
		
	public function removeToolGroup(Void):Void{
		this.__toolGroup.removeMovieClip();
		};
		
	public function insertTool(toolName:String, toolType:String, toolIcon:String, toolStyle:String):Void{
		if(this.__tools[toolName] == undefined){
			this.__tools[toolName] = new CieTools(this.__toolGroup, toolName, toolType, toolIcon);
			if(toolStyle != undefined){
				arrStyle = toolStyle.split(',');
				this.__tools[toolName].changeColor(Number(arrStyle[0]), Number(arrStyle[1]), Number(arrStyle[2]), Number(arrStyle[3]), Number(arrStyle[4]));
				}
			this.redraw();	
			}
		};
		
	public function insertButton(toolName:String, toolType:String, toolText:String, toolStyle:String):Void{
		if(this.__tools[toolName] == undefined){
			arrWH = toolType.split('X');
			arrStyle = toolStyle.split(',');
			this.__tools[toolName] = new CieButton(this.__toolGroup, toolText, Number(arrWH[0]), Number(arrWH[1]), 0, 0);	
			this.__tools[toolName].changeColor(Number(arrStyle[0]), Number(arrStyle[1]), Number(arrStyle[2]), Number(arrStyle[3]), Number(arrStyle[4]));
			this.__tools[toolName].changeTextColor(Number(arrStyle[5]));
			this.redraw();	
			}
		};	
		
	public function	setAction(toolName:String, arrActions:Array):Void{
		if(this.__tools[toolName] != undefined){
			this.__tools[toolName].setAction(arrActions[0], arrActions[1], arrActions[2]);
			}
		};
		
	public function	getIcon(toolName:String):MovieClip{
		if(this.__tools[toolName] != undefined){
			return this.__tools[toolName].getIcon();
			}
		return null;	
		};	
		
	public function	getTool(toolName:String):CieTools{
		if(this.__tools[toolName] != undefined){
			return this.__tools[toolName].getClass();
			}
		return null;	
		};		
		
	public function getWidth(Void):Number{
		return this.__totalWidth;
		};
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieToolsGroup{
		return this;
		};
		
	public function	moveGroupTo(x:Number, y:Number):Void{
		this.__toolGroup._x = x;
		this.__toolGroup._y = y;
		};
		
	public function redraw(Void):Void{
		this.__totalWidth = 0;
		var nextX = 0;
		for(var o in this.__tools){
			this.__tools[o].redraw(nextX, (this.__offSetY - this.__tools[o].getIconHeight())/2);
			nextX += this.__tools[o].getIconWidth() + this.__spacerTool;	
			this.__totalWidth = nextX;
			}
		};
			
	}	