import graphic.CieSquare;

dynamic class control.CieSlider{

	static private var __className = "CieSlider";
	private var __mv:MovieClip;
	private var __mvSlider:MovieClip;
	private var __mvButt:MovieClip;
	private var __long:Number;
	private var __iResize:Number;
	private var __panelSize:Number;
	private var __pos:Number;
	private var __offset:Number;
	private var __width:Number;
	

		
	public function CieSlider(mv:MovieClip, iLong:Number, type:String, panelSize:Number){
	
		this.__mv = mv;
		this.__long = iLong;
		this.__type = type;
		this.__panelSize = panelSize;
		this.__iResize = this.__panelSize / this.__long;
		this.__width = 10;
		
		this.__quality = 0;
		this.__pos = 0;

		//build slider
		this.buildSlider();
		};
	
	public function buildSlider(Void):Void{
		
		this.__mvSlider = this.__mv.createEmptyMovieClip('mvSlider', this.__mv.getNextHighestDepth());
		this.__mvContenSlider = this.__mv.createEmptyMovieClip('mvContenSlider', this.__mv.getNextHighestDepth());
		this.__mvButt = this.__mv.createEmptyMovieClip('mvButt', this.__mv.getNextHighestDepth());
		
		
		this.__pos = (this.__long * this.__quality)/100;
		
		//si horizontal
		if (this.__type == 'H'){
			var slider = new CieSquare(this.__mvSlider, 0, 0, this.__long, 1, 3, [], []);					
			var butt = new CieSquare(this.__mvButt, 0, 0, this.__width, this.__width, 3, [], []);
			var contentslider = new CieSquare(this.__mvContenSlider, 0, 0, (this.__long+this.__mvButt._width), this.__width, 3, [], []);	
			this.__mvButt._x = this.__pos;
		//sinon vertical
		}else{
			var slider = new CieSquare(this.__mvSlider, 0, 0, 1, this.__long, 3, [], []);			
			var butt = new CieSquare(this.__mvButt, 0, 0, this.__width, this.__width, 3, [], []);	
			var contentslider = new CieSquare(this.__mvContenSlider, 0, 0, this.__width, (this.__long+this.__mvButt._height), 3, [], []);
			this.__mvButt._y = this.__pos;
			}
			
		this.__mvSlider._visible = false;
		
		this.__mvButt.__long = this.__long;
		this.__mvButt.__offset = this.__offset;
		this.__mvButt.__type = this.__type;
		this.__mvButt.__quality = this.__quality;
		this.__mvButt.__mvSlider = this.__mvSlider;
		this.__mvButt.__pos = this.__pos;
		this.__mvButt.__super = this;
		this.__mvButt.onReleaseOutside = 
		this.__mvButt.onRollOut = 	
		this.__mvButt.onRelease = function(Void):Void{
			this.stopDrag();
			
			if (this.__type == 'H'){
				this.__pos = this._x;	
			}else{
				this.__pos = this._y;
				}
				
			this.__quality = (this.__pos * 100)/this.__long;		
			this.__super.setSelectionValue(this.__quality);
			}
		
		this.__mvButt.onPress = function(Void):Void{
		
			if (this.__type == 'H'){
				this.startDrag(false, this.__mvSlider._x, this.__mvSlider._y, this.__mvSlider._width, this.__mvSlider._y);	
			}else{
				this.startDrag(false, this.__mvSlider._x, this.__mvSlider._y, this.__mvSlider._x, this.__mvSlider._height);	
				}			
			}	
		};
	

	
	public function resize(w:Number, h:Number):Void{

		if (this.__type == 'H'){
			this.__long = w / this.__iResize;
		}else{
			this.__long = h / this.__iResize;
			}

		this.__mvContenSlider.clear();	
		this.__mvSlider.clear();
		this.__mvButt.clear();
				
		this.__mvContenSlider.removeMovieClip();
		this.__mvSlider.removeMovieClip();
		this.__mvButt.removeMovieClip();
		
		delete this.__mvContenSlider;
		delete this.__mvSlider;
		delete this.__mvButt;
	
		this.buildSlider();
		
		};
	
	public function setSelectionValue(iQuality:Number):Void{
		this.__quality = iQuality;
		//Debug("QUALITY: " + Math.round(this.__quality));
		};
	
	public function getSelectionValue(Void):Number{
		return Math.round(this.__quality);
		};
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieSlider{
		return this;
		};
	*/	
	};