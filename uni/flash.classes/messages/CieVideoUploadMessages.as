
import control.CieWindows;
import control.CieTextLine;
import control.CieButton;
import control.CieOptionBox;
import flash.net.FileReference;
import messages.CieTextMessages;

dynamic class messages.CieVideoUploadMessages{

	static private var __className:String = 'CieVideoUploadMessages';
	private var __marge:Number;
	private var __hBox:Number;
	private var __hButt:Number;
	private var __selectBoxWidth:Number;
	private var __textWidth:Number;
	private var __mvContent:MovieClip;
	private var __window:CieWindows;
	private var __text:String;
	private var __fileListener:Object;
		
	private var __cbFunction:Function;
	private var __cbClass:Object;
	
	/*************************************************************************************************************************************************/
	
	public function CieVideoUploadMessages(){
		this.__marge = 10;
		this.__hBox = 17;
		this.__hButt = 30;
		this.__hButtBrowse = 20;
		this.__selectBoxWidth = 255;
		this.__textWidth = 275;
		this.__titre = gLang[444];
				
		this.fileBrowse();
		};
		
	/*************************************************************************************************************************************************/	
	
	private function openVideoUploadMessagesPopUp(file:FileReference):Void{
		//créer la fenetre pop-up
		this.__window = new CieWindows(this.__titre, this.cbClose, this);
		this.__mvContent = this.__window.getContent();
		new CieGlow(this.__mvContent, true);
		
		//texte
		var mvTexte = this.__mvContent.attachMovie('mvTextMessages', 'T_0', this.__mvContent.getNextHighestDepth());	
		mvTexte.txtInfos.autoSize = 'left';
		mvTexte.txtInfos._width = this.__textWidth;
		mvTexte.txtInfos.htmlText = gLang[445] + file.name + gLang[446];
		
		//case texte input pour description	
		var tInput = new CieTextLine(this.__mvContent, 0, 0, this.__selectBoxWidth, this.__hBox, 'T_', '', 'input', [], false, true, false, true);	
				
		// boutons
		var mvButtOK = this.__mvContent.createEmptyMovieClip('mvButtOK', this.__mvContent.getNextHighestDepth());
		var mvButtCANCEL = this.__mvContent.createEmptyMovieClip('mvButtCANCEL', this.__mvContent.getNextHighestDepth());
		
		//contenu
		var btnOK = new CieButton(mvButtOK, gLang[248], this.__selectBoxWidth/2, this.__hButt, 0, 0);
		var btnCANCEL = new CieButton(mvButtCANCEL, gLang[249], this.__selectBoxWidth/2, this.__hButt, 0, 0);
		
		//positionnement ccase texte pour description
		tInput.getSelectionMovie()._y = mvTexte._y + mvTexte._height + this.__marge;
		
		//Positionnement des boutons
		mvButtOK._y = mvButtCANCEL._y = tInput.getSelectionMovie()._y + tInput.getSelectionMovie()._height + (this.__marge * 3);
		
		//création du window popup
		this.__window.setWindow();
				
		//pos X after de setWindow
		mvTexte._x = (this.__marge * 2);
		
		//pos X after de setWindow
		tInput.getSelectionMovie()._x = (this.__marge * 2);
				
		//reposition des boutons
		var xStartPos = (this.__window.getWindowWidth() - (this.__selectBoxWidth + this.__marge))/2;
		mvButtOK._x = xStartPos;
		mvButtCANCEL._x = xStartPos + (this.__selectBoxWidth/2) + this.__marge;
		
		//action bouton OK
		btnOK.getMovie().__file = file;
		btnOK.getMovie().__sup = this;
		btnOK.getMovie().__input = tInput;
		btnOK.getMovie().onRelease = function(Void):Void{
			this.__sup.uploadFile(this.__file, this.__input.getSelectionValue());			
			};
		//action bouton CANCEL
		btnCANCEL.getMovie().__sup = this;
		btnCANCEL.getMovie().onRelease = function(Void):Void{
			this.__sup.closeWindow();	
			};
		};
		
	/*************************************************************************************************************************************************/	
	private function fileBrowse(Void):Void{	
		//set the types
		var videoTypes = new Object();
		videoTypes.description = "Video Files (*.mpg;, *.mpeg, *.mp4, *.avi, *.mov, *.wmv, *.flv, *.3gp, *.3g2, *.m4v, *.m2v)";
		videoTypes.extension = "*.mpg;*.mpeg;*.mp4;*.avi;*.mov;*.wmv;*.flv;*.3gp;*.3g2;*.m4v;*.m2v";
		//array of all types
		var allTypes = new Array();
		allTypes.push(videoTypes);
		//set the file listener
		this.__fileListener = new Object();
		this.__fileListener.__class = this;
		//ON SELECT
		this.__fileListener.onSelect = function(file:FileReference):Void{
			this.__class.insertFile(file);
			};
		//ON CANCEL	
		this.__fileListener.onCancel = function(file:FileReference):Void{
			cFunc.clearVideoUploadMessagesClass();
			};
		var fileRef = new FileReference();
		fileRef.addListener(this.__fileListener);
		fileRef.browse(allTypes);	
		};
		
	/*************************************************************************************************************************************************/	
	private function insertFile(file:FileReference):Void{
		var maxSize:Number = 8192000; //8 meg
		var bContinueInsertFile:Boolean = true;
		var fileName:String = file.name;
		//max file size
		if(Number(file.size) >  maxSize){
			new CieTextMessages('MB_OK', gLang[447], gLang[16]);
		}else{
			this.openVideoUploadMessagesPopUp(file);
			}
		};
		
	/*************************************************************************************************************************************************/
	public function uploadFile(file:FileReference, strDescription:String):Void{
		this.setProgress();
		//listener for progress and complete
		var listener:Object = new Object();								
		listener.__class = this; 
		//complete
		listener.onComplete = function(file:FileReference):Void{			
			file.removeListener(this);
			this.__class.closeWindow();
			new CieTextMessages('MB_OK', gLang[448], this.__class.__titre);
			};
		//progress
		listener.onProgress = function(file:FileReference, bytesLoaded:Number, bytesTotal:Number):Void{
			//Debug("PROGRESS: " + file.name + " " + bytesLoaded + "/" + bytesTotal);
			this.__class.setLoadingProgress(file.name, bytesLoaded, bytesTotal);
			};
		//errors
		listener.onIOError = function(file:FileReference):Void{
			file.removeListener(this);
			this.__class.closeWindow();
			//new CieTextMessages('MB_OK', "onIOError: " + file.name, this.__class.__titre);
			new CieTextMessages('MB_OK', gLang[449], this.__class.__titre);
			};
		listener.onSecurityError = function(file:FileReference, errorString:String):Void{
			file.removeListener(this);
			this.__class.closeWindow();
			//new CieTextMessages('MB_OK', "onSecurityError: " + file.name, this.__class.__titre);
			new CieTextMessages('MB_OK', gLang[449], this.__class.__titre);
			};
		listener.onHTTPError = function(file:FileReference):Void{
			file.removeListener(this);
			this.__class.closeWindow();
			//new CieTextMessages('MB_OK', "onHTTPError: " + file.name, this.__class.__titre);
			new CieTextMessages('MB_OK', gLang[449], this.__class.__titre);
			};	
		
		//upload link build
		strServeurUpload = BC.__server.__vattachement + "?&n=" + BC.__user.__nopub + "&d=" + escape(strDescription);
		file.addListener(listener);
		file.upload(strServeurUpload);
		};
		
	/*************************************************************************************************************************************************/		
	public function setProgress(Void):Void{
		this.closeWindow();
		this.__window = new CieWindows(this.__titre, null, this);
		this.__mvContent = this.__window.getContent();
		new CieGlow(this.__mvContent, true);
		
		// créer le contenu dans les movie	
		this.__mvProgress = this.__mvContent.attachMovie('progressBar', 'mvProgressBar', this.__mvContent.getNextHighestDepth());
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
		this.__mvProgress.mvBarLoad._width = Math.floor((Math.floor((iLoaded / iTotal) * 100) * this.__mvProgress.mvBar._width) / 100);
		this.__mvProgress.txtInfos.htmlText = strText;
		};	
	
	/*************************************************************************************************************************************************/	
	public function closeWindow(Void):Void{
		this.__window.destroy();
		this.__window = null;
		};	
	
	/*************************************************************************************************************************************************/	
	public function getClassName(Void):String{
		return __className;
		};
	
	/*************************************************************************************************************************************************/	
	public function getClass(Void):CieVideoUploadMessages{
		return this;
		};
	
	};