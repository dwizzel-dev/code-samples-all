import control.CieTextLine;

dynamic class control.CieOptionBox{

	static private var __className = 'CieOptionBox';
	private var __mv:MovieClip;
	private var __arr:Array;
	private var __group:String;
	private var __optBoxWidth:Number;
	private var __hBox:Number;
	private var __hvSpacer:Number;
	
	private var __arrRadioBox:Array;
	private var __mvRadioBox:MovieClip;
		
	public function CieOptionBox(mv:MovieClip, arr:Array, group:String){
		this.__hvSpacer = 10;
		this.__optBoxWidth = 15;
		this.__hBox = 17;
		this.__mv = mv;
		this.__arr = arr;
		this.__group = group;
		this.__arrRadioBox = new Array();
		this.createOptionBox();
		};
	
	private function createOptionBox(Void):Void{
		//empty clip
		this.__mvRadioBox = this.__mv.createEmptyMovieClip('mvRadioBox_' + this.__group, this.__mv.getNextHighestDepth());
		//loop trought all choices
		for(var i in this.__arr){
			//create empty layer
			this.__arrRadioBox[i] = this.__mvRadioBox.createEmptyMovieClip('mvRadioBox_' + i, this.__mvRadioBox.getNextHighestDepth());
			//create
			this.__arrRadioBox[i].__mv = this.__arrRadioBox[i].attachMovie('RadioBoxes', 'RadioBoxes', this.__arrRadioBox[i].getNextHighestDepth());
			//
			this.__arrRadioBox[i].__mv.gotoAndStop('_0');
			//data holder
			this.__arrRadioBox[i].__mv.__data = this.__arr[i][0];
			//selected
			this.__arrRadioBox[i].__mv.__selected = false;
			//create the text next the optBox
			new CieTextLine(this.__arrRadioBox[i].__mv, this.__optBoxWidth, -3, 0, 0, 'textfield' + i, this.__arr[i][1], 'dynamic',[], false, false, false, false);
			//positionning
			this.__arrRadioBox[i]._x = 0;
			this.__arrRadioBox[i]._y = this.__hvSpacer + (this.__hBox * i);		
			//actions
			this.__arrRadioBox[i].__mv.__super = this;
			this.__arrRadioBox[i].__mv.__id = i;
			this.__arrRadioBox[i].__mv.onRelease = function(Void):Void{
				this.__super.setSelectionValue(this.__id);
				};
			}
		//move the entire movie to the roght
		this.__mvRadioBox._x = this.__hvSpacer;
		};
	

	public function redraw(x:Number, y:Number):Void{
		this.__mvRadioBox._x = x;
		this.__mvRadioBox._y = y;
		};
	
	public function getMovie(Void):MovieClip{
		return this.__mvRadioBox;
		};
		
	public function setSelectionValue(id):Void{
		for(var o in this.__arrRadioBox){
			if(o == id){
				this.__arrRadioBox[o].__mv.__selected = true;
				this.__arrRadioBox[o].__mv.gotoAndStop('_1');
			}else{
				this.__arrRadioBox[o].__mv.__selected = false;
				this.__arrRadioBox[o].__mv.gotoAndStop('_0');
				}
			}
		};
	
	public function getSelectionValue(Void):String{
		for(var o in this.__arrRadioBox){
			if(this.__arrRadioBox[o].__mv.__selected){
				return o;
				break;
				}
			}
		return '';	
		};	
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieOptionBox{
		return this;
		};
	};