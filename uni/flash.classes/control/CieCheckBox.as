//import mx.controls.CheckBox;
import control.CieTextLine;
//import utils.CieConversion;

dynamic class control.CieCheckBox{

	static private var __className = 'CieCheckBox';
	private var __mv:MovieClip;
	private var __mvCheckBox:MovieClip;
	private var __arr:Array;
	//private var __label:String;
	//private var __checkbox:String;
	//private var __hBox:Number;
	private var __chkBoxWidth:Number;
	private var __cbFunction:Function;
	private var __cbClass:Object;	
	private var __state:String;	
		
	public function CieCheckBox(mv:MovieClip, arr:Array){
		this.__chkBoxWidth = 15;
		//this.__hBox = 17;
		//this.__checkbox = 'my_ch';
		this.__mv = mv;
		this.__arr = arr;
		this.createCheckBox();
		};
		
	public function setCallBackFunction(cbFunc:Function, cbClass:Object):Void{
		// Create handler for checkBox event.
		this.__cbFunction = cbFunc;
		this.__cbClass = cbClass;
		};
		
	public function setSelect(strState:String):Void{
		this.__mvCheckBox.attachMovie('CheckBox_' + strState, 'CheckBox_' + strState, 1);
		this.__state = strState;
		};
	
	private function createCheckBox(Void):Void{
		//create the check box
		this.__mvCheckBox = this.__mv.createEmptyMovieClip('mvCheckBox_' + cConversion.makeRandomNum(0, 10000), this.__mv.getNextHighestDepth());
		if (this.__arr[1]){
			this.__state = '1';
			this.setSelect('1');
		}else{
			this.__state = '0';
			this.setSelect('0');
			}
		//setaction
		this.__mvCheckBox.__super = this;
		this.__mvCheckBox.onRelease = function(Void):Void{
			if (this.__super.__state == '1'){
				this.__super.__state = '0';
				this.__super.setSelect('0');
			}else if (this.__super.__state == '0'){
				this.__super.__state = '1';
				this.__super.setSelect('1');
			}else{
				this.__super.__state = '2';
				this.__super.setSelect('0');
				}
			if(this.__super.__cbClass != undefined){
				this.__super.__cbFunction(this.__super.__cbClass, obj);
				}
			}		
		
		//create the text next the checkBox because f*** flash do shit with that
		new CieTextLine(this.__mvCheckBox, this.__chkBoxWidth, -3, 0, 0, 'textfield', this.__arr[0], 'dynamic',[], false, false, false, false);
		};
	
	public function redraw(x:Number, y:Number):Void{
		this.__mvCheckBox._x = x;
		this.__mvCheckBox._y = y;	
		};
	
	public function getCheckBoxMovie(Void):MovieClip{
		return this.__mvCheckBox;
		};
		
	public function getSelectionValue(Void):String{
		return this.__state;
		};	
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieCheckBox{
		return this;
		};
	};
	