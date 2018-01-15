/*



*/
import control.CieWindows;
import control.CieTextLine;
import control.CieButton;

dynamic class messages.CiePromptMessages{

	static private var __className:String = 'CiePromptMessages';
	private var __marge:Number;
	private var __hBox:Number;
	private var __hButt:Number;
	private var __selectBoxWidth:Number;
	
	private var __mvContent:MovieClip;
	private var __mvTexte:MovieClip; 
	private var __mvTexteInput:MovieClip; 
	private var __mvButt:MovieClip; 
	
	private var __window:CieWindows;
	private var __textInput:CieTextLine;
	private var __inputText:String;
	private var __textWidth:Number;
	
	private var __titre:String;
	private var __text:String;
	
	private var __cbFunction:Function;
	private var __cbClass:Object;
	
	public function CiePromptMessages(cTitre:String, cText:String){
		this.__marge = 10;
		this.__hBox = 17;
		this.__hButt = 30;
		this.__selectBoxWidth = 255;
		this.__textWidth = 275;
		this.__text = cText;
		this.__titre = cTitre;
		
		this.openPrompt();
		};
		
	/*************************************************************************************************************************************************/	
	
	private function openPrompt(Void):Void{
		//créer la fenetre pop-up
		this.__window = new CieWindows(this.__titre, this.cbClose, this);
		this.__mvContent = this.__window.getContent();
		new CieGlow(this.__mvContent, true);
		
		//créer les movies pour le contenu
		this.__mvTexteInput = this.__mvContent.createEmptyMovieClip('mvTexteInput', this.__mvContent.getNextHighestDepth());
	
		// créer le contenu dans les movie
		//texte dynamique		
		this.__mvTexte = this.__mvContent.attachMovie('mvTextMessages', 'mvTextMessages', this.__mvContent.getNextHighestDepth());	
		this.__mvTexte.txtInfos.htmlText = this.__text;
		this.__mvTexte.txtInfos.autoSize = 'left';
		this.__mvTexte.txtInfos._width = this.__textWidth;
		
		//case texte input	
		this.__textInput = new CieTextLine(this.__mvTexteInput, 0, 0, this.__selectBoxWidth, this.__hBox, 'texteInput', '', 'input', [], false, true, false, true);		
				
		//movies
		var mvButtOK = this.__mvContent.createEmptyMovieClip('mvButtOK', this.__mvContent.getNextHighestDepth());
		var mvButtCANCEL = this.__mvContent.createEmptyMovieClip('mvButtCANCEL', this.__mvContent.getNextHighestDepth());
		
		//contenu
		var btnOK = new CieButton(mvButtOK, gLang[248], this.__selectBoxWidth/2, this.__hButt, 0, 0);
		var btnCANCEL = new CieButton(mvButtCANCEL, gLang[249], this.__selectBoxWidth/2, this.__hButt, 0, 0);
		
		//Positionnement
		this.__mvTexteInput._y = this.__mvTexte._y + this.__mvTexte._height + this.__marge;
		mvButtOK._y = mvButtCANCEL._y = this.__mvTexteInput._y + this.__mvTexteInput._height + (this.__marge * 2);
				
		//création du window popup
		this.__window.setWindow();
		
		//pos X
		this.__mvTexteInput._x = (this.__marge * 2);
		this.__mvTexte._x = (this.__marge * 2);
		
		//position des boutons
		var xStartPos = (this.__window.getWindowWidth() - (this.__selectBoxWidth + this.__marge))/2;
		mvButtOK._x = xStartPos;
		mvButtCANCEL._x = xStartPos + (this.__selectBoxWidth/2) + this.__marge;
		
		//action bouton ok
		btnOK.getMovie().__sup = this;
		btnOK.getMovie().onRelease = function(Void):Void{
			this.__sup.__inputText = this.__sup.__textInput.getSelectionText();
			//rajout d'un flag
			this.__sup.__cbClass['__ok'] = true;
			if(this.__sup.__cbFunction != undefined){
				this.__sup.__cbFunction(this.__sup.__cbClass);
				}
			//destroy
			this.__sup.closeWindow();
			}
		//action bouton cancel
		btnCANCEL.getMovie().__sup = this;
		btnCANCEL.getMovie().onRelease = function(Void):Void{
			this.__sup.__cbClass['__ok'] = false;
			if(this.__sup.__cbFunction != undefined){
				this.__sup.__cbFunction(this.__sup.__cbClass);
				}
			//destroy
			this.__sup.closeWindow();
			}
		
		};
		
	
	/*************************************************************************************************************************************************/
	
	public function getInputText(Void):String{
		return this.__inputText; 
		}
	
	/*************************************************************************************************************************************************/
	
	public function setCallBackFunction(cbFunc:Function, cbClass:Object):Void{
		// Create handler when button his clicked
		this.__cbFunction = cbFunc;
		this.__cbClass = cbClass;
		};	
		
	/*************************************************************************************************************************************************/
	
	public function cbClose(cbClass:Object):Void{
		cbClass.__cbClass['__ok'] = false;
		cbClass.__cbFunction(cbClass.__cbClass);
		};
	
	/*************************************************************************************************************************************************/
	
	public function closeWindow(Void):Void{
		this.__window.destroy();
		this.__window = null;
		};	
	
	/*************************************************************************************************************************************************/	
	/*
	public function getClassName(Void):String{
		return __className;
		};
		
	public function getClass(Void):CiePromptMessages{
		return this;
		};
	*/
	};