//import control.CieTextLine;
import control.CieCheckBox;

dynamic class control.CieCheckBoxGroup{

	static private var __className = 'CieCheckBoxGroup';
	private var __mv:MovieClip;
	private var __arrChkBox:Array;
	private var __arrValue:Array;
	private var __hBox:Number;
	private var __cbFunction:Function;
	private var __cbClass:Object;	
	private var __hvSpacer:Number;
			
	public function CieCheckBoxGroup(mv:MovieClip, arr:Array){
		this.__arrChkBox = new Array();
		this.__arrValue = arr;
		this.__hBox = 17;
		this.__hvSpacer = 10;
		this.__mv = mv;
		this.createCheckBoxes();
		};
		
	private function createCheckBoxes(Void):Void{
		var h:Number = (this.__hvSpacer + this.__hBox);
		for(var o in this.__arrValue){
			var mc:MovieClip = this.__mv.createEmptyMovieClip('chk_' + this.__arrValue[o][1], this.__mv.getNextHighestDepth());
			var chk:CieCheckBox = new CieCheckBox(mc, [this.__arrValue[o][1], false]);
			mc._y = h; 
			mc._x = this.__hvSpacer; 
			h += this.__hBox;
			//container object
			this.__arrChkBox[this.__arrValue[o][1]] = new Object();
			this.__arrChkBox[this.__arrValue[o][1]].__mv = mc;
			this.__arrChkBox[this.__arrValue[o][1]].__chk = chk;
			this.__arrChkBox[this.__arrValue[o][1]].__value = this.__arrValue[o][0];
			this.__arrChkBox[this.__arrValue[o][1]].__name = this.__arrValue[o][1];
			}
		};
		
	public function setCallBackFunction(cbFunc:Function, cbClass:Object):Void{
		this.__cbFunction = cbFunc;
		this.__cbClass = cbClass;
		};
		
	public function setSelectionValue(strValue:String):Void{
		var arrValues:Array = strValue.split(',');
		var iIndex:Number = 0;
		for(var o in this.__arrChkBox){
			if(arrValues[iIndex] == '1'){
				this.__arrChkBox[o].__chk.setSelect('1');
				}
			iIndex++;	
			}
		};
	
	public function getSelectionValue(Void):Array{
		var arrValues:Array = new Array();
		for(var o in this.__arrChkBox){
			arrValues.push(this.__arrChkBox[o].__chk.getSelectionValue());
			}
		return arrValues;
		};	
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieCheckBoxGroup{
		return this;
		};
	};	