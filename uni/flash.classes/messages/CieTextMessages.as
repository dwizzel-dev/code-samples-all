/*

Simple text message when butt OK enable pass "MB_OK" if none then "MB_NONE"

*/
import control.CieWindows;
import control.CieButton;


dynamic class messages.CieTextMessages{

	static private var __className = 'CieTextMessages';
	private var __window:CieWindows;
			
	//callback
	private var __cbFunction:Function;
	private var __cbClass:Object;
	
	//vars
	private var __mvProgress:MovieClip; 
	private var __titre:String; 
	
		
	public function CieTextMessages(cType:String, cText:String, cTitre:String){
		var __marge = 10;
		var __hButt = 30;
		var __selectBoxWidth = 200;
		var __textWidth = 275;
		this.__titre = cTitre;
		
		//créer la fenetre pop-up
		this.__window = new CieWindows(this.__titre, this.cbClose, this);
		var __mvContent = this.__window.getContent();
		new CieGlow(__mvContent, true);
		
		//texte
		var mvTexte = __mvContent.attachMovie('mvTextMessages', 'mvTextMessages', __mvContent.getNextHighestDepth());	
		mvTexte.txtInfos.htmlText = cText;
		mvTexte.txtInfos.autoSize = 'left';
		mvTexte.txtInfos._width = __textWidth;
		
		if(cType != 'MB_NONE'){
			// boutons
			var mvButtOK = __mvContent.createEmptyMovieClip('mvButtOK', __mvContent.getNextHighestDepth());
			
			//contenu
			var btnOK = new CieButton(mvButtOK, gLang[248], (__selectBoxWidth/2), __hButt, 0, 0);
			
			//Positionnement des boutons
			mvButtOK._y = mvTexte._y + mvTexte._height + (__marge * 2);
			}
				
		//draw the window
		this.__window.setWindow();
		
		//pos X
		mvTexte._x = (__marge * 2);
		
		//position des boutons
		var xStartPos = (this.__window.getWindowWidth() - (__selectBoxWidth/2))/2;
		
		if(cType != 'MB_NONE'){
			mvButtOK._x = xStartPos;
			//action bouton OK
			btnOK.getMovie().__sup = this;
			btnOK.getMovie().onRelease = function(Void):Void{
				if(this.__sup.__cbFunction != undefined){
					this.__sup.__cbFunction(this.__sup.__cbClass);
					}
				this.__sup.closeWindow();
				};
		}else{
			//disable close
			this.__window.getButtMovie()._visible = false;
			}
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
		
	/*************************************************************************************************************************************************/		
	
	public function setProgress(Void):Void{
		this.closeWindow();
		this.__window = new CieWindows(this.__titre, this.cbClose, this);
		var __mvContent = this.__window.getContent();
		new CieGlow(__mvContent, true);
		
		// créer le contenu dans les movie	
		this.__mvProgress = __mvContent.attachMovie('progressBar', 'mvProgressBar', __mvContent.getNextHighestDepth());
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
	
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieTextMessages{
		return this;
		};
	*/
	};