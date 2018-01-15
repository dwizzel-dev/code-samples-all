/*



*/
import control.CieWindows;
//import control.CieTextLine;
import control.CieButton;
//import manager.CieRequestManager;


dynamic class messages.CieTextMessages{

	static private var __className = 'CieTextMessages';
	private var __marge:Number;
	private var __hButt:Number;
	private var __selectBoxWidth:Number;
	private var __textWidth:Number;
	private var __mvContent:MovieClip;
	private var __window:CieWindows;
	private var __text:String;
	private var __titre:String;
	
	//callback
	private var __cbFunction:Function;
	private var __cbClass:Object;
		
	public function CieTextMessages(cType:String, cText:String, cTitre:String){
		this.__marge = 10;
		this.__hButt = 30;
		this.__selectBoxWidth = 200;
		this.__textWidth = 235;
		this.__text = cText;
		this.__titre = cTitre;

		//créer la fenetre pop-up
		this.__window = new CieWindows(this.__titre, this.cbClose, this);
		this.__mvContent = this.__window.getContent();
		new CieGlow(this.__mvContent, true);
		
		//texte
		var mvTexte = this.__mvContent.attachMovie('mvTextMessages', 'mvTextMessages', this.__mvContent.getNextHighestDepth());	
		mvTexte.txtInfos.htmlText = this.__text;
		mvTexte.txtInfos.autoSize = 'left';
		mvTexte.txtInfos._width = this.__textWidth;
		
		// boutons
		var mvButtOK = this.__mvContent.createEmptyMovieClip('mvButtOK', this.__mvContent.getNextHighestDepth());
		
		//contenu
		var btnOK = new CieButton(mvButtOK, gLang[248], (this.__selectBoxWidth/2), this.__hButt, 0, 0);
		
		//Positionnement des boutons
		mvButtOK._y = mvTexte._y + mvTexte._height + (this.__marge * 2);
				
		//draw the window
		this.__window.setWindow();
		
		//pos X
		mvTexte._x = (this.__marge * 2);
		
		//position des boutons
		var xStartPos = (this.__window.getWindowWidth() - (this.__selectBoxWidth/2))/2;
		mvButtOK._x = xStartPos;
		
		//action bouton OK
		btnOK.getMovie().__sup = this;
		btnOK.getMovie().onRelease = function(Void):Void{
			if(this.__sup.__cbFunction != undefined){
				this.__sup.__cbFunction(this.__sup.__cbClass);
				}
			this.__sup.closeWindow();
			};
		};
	
	/*************************************************************************************************************************************************/		
	
	public function setCallBackFunction(cbFunction:Function, cbClass:Object):Void{
		this.__cbFunction = cbFunction;
		this.__cbClass = cbClass;
		};
	
	/*************************************************************************************************************************************************/		
	
	public function closeWindow(Void):Void{
		this.__window.destroy();
		this.__window = null;
		};
	
	/*************************************************************************************************************************************************/		
	
	public function cbClose(cbClass:Object):Void{
		//
		};
	
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieTextMessages{
		return this;
		};
	*/
	};