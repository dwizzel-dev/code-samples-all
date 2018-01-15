/*

rajout du setLoadWithText pour l'envoi de courriel et de fichier video

*/
import control.CieWindows;
//import control.CieTextLine;
import control.CieButton;

dynamic class messages.CieActionMessages{

	static private var __className = 'CieActionMessages';
	private var __marge:Number;
	private var __hButt:Number;
	private var __selectBoxWidth:Number;
	
	private var __mvContent:MovieClip;
	private var __mvLoader:MovieClip;
	private var __mvTexte:MovieClip; 
	private var __mvProgress:MovieClip; 
	private var __textContent:String;
	
	private var __window:CieWindows;
	private var __titre:String;
	
	private var __cbClass:Object;
	private var __cbFunction:Function;
	
	private var __textWidth:Number;
	
	
	public function CieActionMessages(cTitre:String, cTexte:String){
		this.__marge = 10;
		this.__hButt = 30;
		this.__selectBoxWidth = 200;
		this.__titre = cTitre;
		this.__textWidth = 275;
		this.__textContent = cTexte;
		this.openActionMessage();
		};
	
	/*************************************************************************************************************************************************/		
	
	public function openActionMessage(Void):Void{
		//créer la fenetre pop-up
		this.__window = new CieWindows(this.__titre, this.cbClose, this);
		this.__mvContent = this.__window.getContent();
		new CieGlow(this.__mvContent, true);
		
		// créer le contenu dans les movie	
		this.__mvTexte = this.__mvContent.attachMovie('mvTextMessages', 'mvTextMessages', this.__mvContent.getNextHighestDepth());	
		this.__mvTexte.txtInfos.htmlText = this.__textContent;
		this.__mvTexte.txtInfos.autoSize = 'left';
		this.__mvTexte.txtInfos._width = this.__textWidth;
		
		//movies boutons
		var mvButtOK = this.__mvContent.createEmptyMovieClip('mvButtOK', this.__mvContent.getNextHighestDepth());
		var mvButtCANCEL = this.__mvContent.createEmptyMovieClip('mvButtCANCEL', this.__mvContent.getNextHighestDepth());
		
		//contenu
		var btnOK = new CieButton(mvButtOK, gLang[248], this.__selectBoxWidth/2, this.__hButt, 0, 0);
		var btnCANCEL = new CieButton(mvButtCANCEL, gLang[249], this.__selectBoxWidth/2, this.__hButt, 0, 0);
		
		//mvButt OK
		mvButtOK._y = mvButtCANCEL._y = this.__mvTexte._y + this.__mvTexte._height + (this.__marge * 2);
		
		//création du window popup
		this.__window.setWindow();
		
		//pos X
		this.__mvTexte._x = (this.__marge * 2);
		
		//position des boutons
		var xStartPos = (this.__window.getWindowWidth() - (this.__selectBoxWidth + this.__marge))/2;
		mvButtOK._x = xStartPos;
		mvButtCANCEL._x = xStartPos + (this.__selectBoxWidth/2) + this.__marge;
		
		//action bouton ok
		btnOK.getMovie().__sup = this;
		btnOK.getMovie().onRelease = function(Void):Void{
			//no need to call it of no function werr set
			if(this.__sup.__cbFunction != undefined){
				this.__sup.__cbFunction(this.__sup.__cbClass, true);
				}
			};
		//action bouton cancel
		btnCANCEL.getMovie().__sup = this;
		btnCANCEL.getMovie().onRelease = function(Void):Void{
			if(this.__sup.__cbFunction != undefined){
				this.__sup.__cbFunction(this.__sup.__cbClass, false);
				}
			this.__sup.closeWindow();
			};
		};
	
	/*************************************************************************************************************************************************/	
	
	public function setCallBackFunction(cbFunction:Function, cbClass:Object){
		this.__cbFunction = cbFunction;
		this.__cbClass = cbClass;
		};
		
	/*************************************************************************************************************************************************/		
	
	public function setLoading(Void):Void{
		this.closeWindow();
		this.__window = new CieWindows(this.__titre, this.cbClose, this);
		this.__mvContent = this.__window.getContent();
		new CieGlow(this.__mvContent, true);
		
		this.__mvLoader = this.__mvContent.attachMovie('mvLoaderAnimated', 'mvLoaderAnimated', this.__mvContent.getNextHighestDepth());
		
		this.__window.setWindow();
		
		this.__mvLoader._x = (this.__window.getWindowWidth() - this.__mvLoader._width)/2;
			
		//disable close
		//this.__window.getButtMovie().enabled = false;
		this.__window.getButtMovie()._visible = false;
		};
		
	/*************************************************************************************************************************************************/		
	
	public function setProgress(Void):Void{
		this.closeWindow();
		this.__window = new CieWindows(this.__titre, this.cbClose, this);
		this.__mvContent = this.__window.getContent();
		new CieGlow(this.__mvContent, true);
		
		// créer le contenu dans les movie	
		this.__mvProgress = this.__mvContent.attachMovie('progressBar', 'mvProgressBar', this.__mvContent.getNextHighestDepth());
		this.__mvProgress.mvBarLoad._width = 0;
		this.__mvProgress.txtInfos.htmlText = "";	
		
		//set winows before positionning elements
		this.__window.setWindow();
		
		//pos X
		this.__mvProgress._x = (this.__window.getWindowWidth() - this.__mvProgress._width)/2;
							
		//disable close
		//this.__window.getButtMovie().enabled = false;
		this.__window.getButtMovie()._visible = false;
		};	
		
	/*************************************************************************************************************************************************/		
	
	public function setLoadingProgress(strText:String, iLoaded:Number, iTotal:Number):Void{	
		//this.__mvProgress.txtInfos.htmlText = strText;
		//seek bar load
		this.__mvProgress.mvBarLoad._width = Math.floor((Math.floor((iLoaded / iTotal) * 100) * this.__mvProgress.mvBar._width) / 100);
		this.__mvProgress.txtInfos.htmlText = strText;
		};
		
		
	/*************************************************************************************************************************************************/		
	
	public function closeWindow(Void):Void{
		this.__window.destroy();
		this.__window = null;
		};
		
	/*************************************************************************************************************************************************/		
		
	public function cbClose(cbObject:Object):Void{
		cbObject.__cbFunction(cbObject.__cbClass, false);
		};	
	
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieActionMessages{
		return this;
		};
	*/	
	};