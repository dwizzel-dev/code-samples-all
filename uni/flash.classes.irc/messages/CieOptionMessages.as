
import control.CieWindows;
//import control.CieTextLine;
import control.CieButton;
import control.CieOptionBox;
//import manager.CieRequestManager;


dynamic class messages.CieOptionMessages{

	static private var __className:String = 'CieOptionMessages';
	private var __marge:Number;
	private var __hBox:Number;
	private var __hButt:Number;
	private var __selectBoxWidth:Number;
	private var __textWidth:Number;
	private var __mvContent:MovieClip;
	private var __window:CieWindows;
	private var __text:String;
	private var __arrOption:Array;
	private var __selectedChoice:String;
	private var __options:CieOptionBox;
	
	private var __cbFunction:Function;
	private var __cbClass:Object;
	
	/*************************************************************************************************************************************************/
	
	public function CieOptionMessages(cText:String, arrOption:Array, cTitre:String){
		this.__marge = 10;
		this.__hBox = 17;
		this.__hButt = 30;
		this.__selectBoxWidth = 200;
		this.__textWidth = 235;
		this.__text = cText;
		this.__titre = cTitre;
		this.__arrOption = arrOption;
		
		this.openOptionMessagesPopUp();
		};
		
	/*************************************************************************************************************************************************/	
	
	private function openOptionMessagesPopUp(Void):Void{
		//créer la fenetre pop-up
		this.__window = new CieWindows(this.__titre, this.cbClose, this);
		this.__mvContent = this.__window.getContent();
		new CieGlow(this.__mvContent, true);
		
		//options
		var mvOptions = this.__mvContent.createEmptyMovieClip('mvOptions', this.__mvContent.getNextHighestDepth());
		
		var mvTexte = this.__mvContent.attachMovie('mvTextMessages', 'mvTextMessages', this.__mvContent.getNextHighestDepth());	
		mvTexte.txtInfos.htmlText = '<b>' + this.__text + '</b>';
		mvTexte.txtInfos.autoSize = 'left';
		mvTexte.txtInfos._width = this.__textWidth;
		
		//var mvTexte = this.__mvContent.createEmptyMovieClip('mvTexte', this.__mvContent.getNextHighestDepth());
		//var texte = new CieTextLine(mvTexte, 0, 0, 0, this.__hBox, 'textfield', this.__text, 'dynamic',[true,false,false], false, false, false, false);
		
		this.__options = new CieOptionBox(mvOptions, this.__arrOption, 'OptionMessages');
		//set the default values
		this.__options.setSelectionValue(0);	
		
		//pos Y
		mvOptions._y = mvTexte._y + mvTexte._height;
				
		// boutons
		var mvButtOK = this.__mvContent.createEmptyMovieClip('mvButtOK', this.__mvContent.getNextHighestDepth());
		var mvButtCANCEL = this.__mvContent.createEmptyMovieClip('mvButtCANCEL', this.__mvContent.getNextHighestDepth());
		
		//contenu
		var btnOK = new CieButton(mvButtOK, gLang[248], this.__selectBoxWidth/2, this.__hButt, 0, 0);
		var btnCANCEL = new CieButton(mvButtCANCEL, gLang[249], this.__selectBoxWidth/2, this.__hButt, 0, 0);
		
		//Positionnement des boutons
		mvButtOK._y = mvButtCANCEL._y = mvOptions._y + mvOptions._height + (this.__marge * 3);
		
		//création du window popup
		this.__window.setWindow();
		
		//pos X
		mvOptions._x = (this.__marge * 2);
		mvTexte._x = (this.__marge * 2);
		
		//reposition des boutons
		var xStartPos = (this.__window.getWindowWidth() - (this.__selectBoxWidth + this.__marge))/2;
		mvButtOK._x = xStartPos;
		mvButtCANCEL._x = xStartPos + (this.__selectBoxWidth/2) + this.__marge;
		
		//action bouton OK
		btnOK.getMovie().__sup = this;
		btnOK.getMovie().onRelease = function(Void):Void{
			this.__sup.__selectedChoice = this.__sup.__options.getSelectionValue();		
			//rajout d'un flag
			this.__sup.__cbClass['__ok'] = true;
			if(this.__sup.__cbFunction != undefined){
				this.__sup.__cbFunction(this.__sup.__cbClass);
				}
			this.__sup.closeWindow();			
			};
		//action bouton CANCEL
		btnCANCEL.getMovie().__sup = this;
		btnCANCEL.getMovie().onRelease = function(Void):Void{
			this.__sup.__cbClass['__ok'] = false;
			if(this.__sup.__cbFunction != undefined){
				this.__sup.__cbFunction(this.__sup.__cbClass);
				}
			this.__sup.closeWindow();	
			};
		};
	
	/*************************************************************************************************************************************************/
	public function getSelectedChoice(Void):String{
		return this.__selectedChoice; 
		}
	
	/*************************************************************************************************************************************************/
	
	public function setSelectionValue(id):Void{
		this.__options.setSelectionValue(id);
		};
		
	/*************************************************************************************************************************************************/
	
	public function setCallBackFunction(cbFunc:Function, cbClass:Object):Void{
		// Create handler when button his clicked
		this.__cbFunction = cbFunc;
		this.__cbClass = cbClass;
		};
		
	/*************************************************************************************************************************************************/	
		
	public function closeWindow(Void):Void{
		this.__window.destroy();
		this.__window = null;
		};	
	
	/*************************************************************************************************************************************************/
	
	public function cbClose(cbClass:Object):Void{
		cbClass.__cbClass['__ok'] = false;
		cbClass.__cbFunction(cbClass.__cbClass);
		};
		
	/*************************************************************************************************************************************************/	
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieOptionMessages{
		return this;
		};
	*/	
	};
