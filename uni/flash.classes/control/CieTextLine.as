

dynamic class control.CieTextLine{
	static private var __className = 'CieTextLine';
	private var __x:Number;
	private var __y:Number;
	private var __w:Number;
	private var __h:Number;
	
	private var __name:String;
	private var __type:String;
	private var __text:String;
	
	private var __italic:Boolean;
	private var __bold:Boolean;
	private var __underline:Boolean;
	private var __embedFonts:Boolean;
	private var __border:Boolean;
	private var __psw:Boolean;
	private var __selectable:Boolean;
	private var __wordWrap:Boolean;
	private var __main:MovieClip;
	private var __txtField:TextField;
	private var __txtMv:MovieClip;

	//default style
	private var __styleText:Array; 
	private var __styleColor:Array; 
	
	static private var CELLPADDING:Number = 5;
	
	/************************************************************************************************************
	************************************************************************************************************/		
	public function CieTextLine(mvMain:MovieClip, x:Number, y:Number, w:Number, h:Number, sName:String, sText:String, sType:String, arrTextDecor:Array, bEmbedFonts:Boolean, bBorder:Boolean, bPsw:Boolean, bSelectable:Boolean, arrTextStyle:Array){
		
		this.__main = mvMain;
		
		this.__styleText = new Array(CieStyle.__basic.__fontFamily, 0x000000, 11);
		this.__styleColor = new Array(0xEEEEEE, 0x666666);
		
		this.__x = x;
		this.__y = y;
		this.__w = w;
		this.__h = h;

		this.__name = sName;
		this.__type = sType;
		this.__text = sText;
		this.__embedFonts = bEmbedFonts;
		this.__border = bBorder;
		this.__psw = bPsw;
		this.__selectable = bSelectable;
				
		if (arrTextDecor != undefined && arrTextDecor.length == 3){
			this.__bold = arrTextDecor[0];
			this.__italic = arrTextDecor[1];
			this.__underline = arrTextDecor[2];
		}else{
			this.__italic = 
			this.__bold = 
			this.__underline = false;
			}
			
		if(arrTextStyle != undefined && arrTextStyle.length == 2){
			this.__styleText[1] = arrTextStyle[0]; 
			this.__styleText[2] = arrTextStyle[1];
			}
		
		if (this.__type == "input"){
			this.__selectable = true;
			this.__wordWrap = true;
			this.__autoSize = false;
		}else if (this.__type == "dynamic"){
			this.__wordWrap = false;
			this.__autoSize = "left";
			}
		
		
		this.drawTextBox();

		};	

	/************************************************************************************************************
							drawTextBox
	************************************************************************************************************/	
	private function drawTextBox(){
		var tFmt:TextFormat = new TextFormat();
		tFmt.bold = this.__bold;
		tFmt.italic = this.__italic;
		tFmt.underline = this.__underline;
		tFmt.font = this.__styleText[0];
		tFmt.color = this.__styleText[1];
		tFmt.size = this.__styleText[2];
		
		this.__txtMv = this.__main.createEmptyMovieClip('mvText_' + this.__name, this.__main.getNextHighestDepth() + 100);
		
		this.__txtField = this.__txtMv.createTextField(this.__name, this.__txtMv.getNextHighestDepth(), this.__x, this.__y, this.__w, this.__h);	
		this.__txtField.html = false;
		this.__txtField.background = this.__border;
		//this.__txtField.html = true;
		this.__txtField.border = this.__border;
		this.__txtField.borderColor = 0x999999;//this.__styleColor[1];
		this.__txtField.selectable = this.__selectable;
		this.__txtField.type = this.__type;
		this.__txtField.wordWrap = false;
		this.__txtField.autoSize = this.__autoSize;
		this.__txtField.multiline = false;
		this.__txtField.password = this.__psw;
		this.__txtField.setNewTextFormat(tFmt);
		this.__txtField.embedFonts = this.__embedFonts;
		this.__txtField.text = this.__text;
		};
		
	/****************************************************************************************************************************************************/
	
	public function changeTextStyle(arrStyle:Array):Void{
		var tFmt:TextFormat = new TextFormat();
		tFmt.bold = arrStyle[2];
		tFmt.italic = this.__italic;
		tFmt.underline = this.__underline;
		tFmt.font = this.__styleText[0];
		tFmt.color = arrStyle[0];
		tFmt.size = arrStyle[1];
		this.__txtField.setTextFormat(tFmt);
		};
		
	public function setNewText(newText:String):Void{
		this.__txtField.text = newText;
		};		
	
	public function getSelectionMovie(Void):MovieClip{
		return this.__txtMv;
		};
		
	public function getSelectionValue(Void):String{
		this.__text = this.__txtField.text;
		return this.__text;
		};
		
	public function getSelectionText(Void):String{
		this.__text = this.__txtField.text;
		return this.__text;
		};	
		
	public function getTextField(Void):TextField{
		return this.__txtField;
		};	
	
	public function getHeight(Void):Number{
		return this.__txtField._height;
		};
		
	public function getWidth(Void):Number{
		return this.__txtField._width;
		};	
		

	/*******************************************************************************************************************************************************/
	
	public function destroy(Void):Void{
		delete this;
		};
	
	public function getClass(Void):CieTextLine{
		return this;
		};
		
	public function getClassName(Void):String{
		return __className;
		};
		
	}