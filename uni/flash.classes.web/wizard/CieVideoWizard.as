/*

The starter of the video wizrd standalone class couple of dependance on the flash.classes.web

*/

import control.CieTextLine;
import control.CieButton;
import flash.net.FileReference;

dynamic class wizard.CieVideoWizard{

	static private var __className = "CieVideoWizard";
	static private var __instance:CieVideoWizard;
	
	private var __hButt:Number = 30;
	private var __wButt:Number = 255;
	private var __hInput:Number = 17;
	private var __wInput:Number = 300;
	private var __hvSpacer:Number = 10;
	private var __stageW:Number = 500;
	private var __stageH:Number = 80;
	private var __cButtUpload:CieButton;
	private var __mvMessage:MovieClip;
	private var __fileListener:Object;
	private var __mvProgress:MovieClip;
		
	private function CieVideoWizard(Void){
		Debug('CieVideoWizard()');
		this.initBasicStyle();
		this.initContent();
		};
		
	static public function getInstance(Void):CieVideoWizard{
		Debug('getInstance()');
		if(__instance == undefined) {
			__instance = new CieVideoWizard();
			}
		return __instance;
		};
		
	/**************************************************************************************************************/	
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieVideoWizard{
		return this;
		};

	/**************************************************************************************************************/
	public function traceObject(obj:Object, spacer:String):Void{
		if(spacer == undefined){
			spacer = "";
			}
		for(var val in obj){
			if(typeof(obj[val]) == "object" || obj[val] == "array"){
				Debug(spacer + "~" + val);
				this.traceObject(obj[val], spacer + "\t");
			}else{
				Debug(spacer + "~" + val + ":" + obj[val]);
				}
			}
		};
	
	/**************************************************************************************************************/
	private function initContent(Void):Void{
		Debug('initContent()');
		//upload button
		this.__cButt = new CieButton(__stage, gLang[0], this.__wButt, this.__hButt, 1, this.__hvSpacer);
		this.__cButt.getMovie().__sup = this;
		this.__cButt.getMovie().onRelease = function(Void):Void{
			this.__sup.fileBrowse();
			};
		};
	
	/**************************************************************************************************************/
	private function initBasicStyle(Void):Void{
		Debug('initBasicStyle()');
		_global.style.setStyle('color', CieStyle.__basic.__fontColor);
		_global.style.setStyle('themeColor', 'haloBlue');
		_global.style.setStyle('fontSize', 11);
		_global.style.setStyle('embedFonts' , false);
		_global.style.setStyle('fontFamily' , CieStyle.__basic.__fontFamily);
		};
	
	/**************************************************************************************************************/
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
			//cFunc.clearVideoUploadMessagesClass();
			};
		var fileRef = new FileReference();
		fileRef.addListener(this.__fileListener);
		fileRef.browse(allTypes);	
		};	
	
	/*************************************************************************************************************************************************/	
	private function insertFile(file:FileReference):Void{
		var maxSize:Number = 8192000; //8 megs
		if(BC.__user.__maxFileSize != undefined){
			maxSize = BC.__user.__maxFileSize;
			}
		var fileName:String = file.name;
		//max file size
		if(Number(file.size) >  maxSize){
			this.showErrorMsg(gLang[3], 'filesize');
		}else{
			this.showDescriptionInput(file);
			}
		};
		
	/*************************************************************************************************************************************************/	
	private function clearStage(Void):Void{
		//upload button
		if(this.__cButt != undefined){
			this.__cButt.removeButton();
			delete this.__cButt;
			}
		if(this.__mvMessage != undefined){
			this.__mvMessage.clear();
			this.__mvMessage.removeMovieClip();
			delete this.__mvMessage;
			}
		if(this.__mvProgress != undefined){
			this.__mvProgress.clear();
			this.__mvProgress.removeMovieClip();
			delete this.__mvProgress;
			}
		};
		
	/*************************************************************************************************************************************************/	
	private function showMsg(str:String):Void{
		//remove any button or upload bar
		this.clearStage();
		//create layer clip
		this.__mvMessage = __stage.createEmptyMovieClip('mvMsg', __stage.getNextHighestDepth());
		//attch a box text
		var mvMessage = this.__mvMessage.attachMovie('mvTextMessages', 'txtMsg', this.__mvMessage.getNextHighestDepth());
		mvMessage.txtInfos._x = 0;
		mvMessage.txtInfos._y = 0;
		mvMessage.txtInfos.autoSize = 'left';
		mvMessage.txtInfos._width = this.__stageW - (2 * this.__hvSpacer);
		mvMessage.txtInfos._height = this.__stageH - (2 * this.__hvSpacer);
		mvMessage.txtInfos.htmlText = str;
		//pos all
		this.__mvMessage._x = 1;
		this.__mvMessage._y = this.__hvSpacer;
		};

	/*************************************************************************************************************************************************/	
	private function showErrorMsg(str:String, strErrorType:String):Void{
		//error with options
		str = '<font color="#be2f00"><b>' + gLang[6] + '</b></font> ' + str;
		//remove any button or upload bar
		this.clearStage();
		//create layer clip
		this.__mvMessage = __stage.createEmptyMovieClip('mvMsg', __stage.getNextHighestDepth());
		//attch a box text
		var mvMessage = this.__mvMessage.attachMovie('mvTextMessages', 'txtMsg', this.__mvMessage.getNextHighestDepth());
		mvMessage.txtInfos._x = 0;
		mvMessage.txtInfos._y = 0;
		mvMessage.txtInfos.autoSize = 'left';
		mvMessage.txtInfos._width = this.__stageW - (2 * this.__hvSpacer);
		mvMessage.txtInfos._height = this.__stageH - (2 * this.__hvSpacer);
		mvMessage.txtInfos.htmlText = str;
		//			
		if(strErrorType == 'filesize'){
			//ok button
			this.__cButt = new CieButton(this.__mvMessage, gLang[7], 80, this.__hButt, 0, (mvMessage.txtInfos._height + this.__hvSpacer));
			this.__cButt.getMovie().__sup = this;
			this.__cButt.getMovie().onRelease = function(Void):Void{
				this.__sup.fileBrowse();
				};
			}
		//pos all
		this.__mvMessage._x = 1;
		this.__mvMessage._y = this.__hvSpacer;
		};
		
	/*************************************************************************************************************************************************/	
	private function showDescriptionInput(file:FileReference):Void{
		//remove any button or upload bar
		this.clearStage();
		//create layer clip
		this.__mvMessage = __stage.createEmptyMovieClip('mvMsg', __stage.getNextHighestDepth());
		//attch a box text
		var mvMessage = this.__mvMessage.attachMovie('mvTextMessages', 'txtMsg', this.__mvMessage.getNextHighestDepth());
		mvMessage.txtInfos._x = 0;
		mvMessage.txtInfos._y = 0;
		mvMessage.txtInfos.autoSize = 'left';
		mvMessage.txtInfos._width = this.__stageW - (2 * this.__hvSpacer);
		mvMessage.txtInfos._height = this.__stageH - (2 * this.__hvSpacer);
		mvMessage.txtInfos.htmlText = gLang[4];
		//attach an input box for description
		var mvInput = new CieTextLine(this.__mvMessage, 0, (mvMessage.txtInfos._height + this.__hvSpacer), this.__wInput, this.__hInput, 'T_', '', 'input', [], false, true, false, true);	
		//ok button
		this.__cButt = new CieButton(this.__mvMessage, gLang[8], 80, this.__hButt, (mvInput.getSelectionMovie()._width + this.__hvSpacer), (mvMessage.txtInfos._height + this.__hvSpacer - (this.__hInput/2)));
		this.__cButt.getMovie().__sup = this;
		this.__cButt.getMovie().__file = file;
		this.__cButt.getMovie().__input = mvInput;
		this.__cButt.getMovie().onRelease = function(Void):Void{
			this.__sup.uploadFile(this.__file, this.__input.getSelectionValue());
			};
		//pos all
		this.__mvMessage._x = 1;
		this.__mvMessage._y = this.__hvSpacer;
		//focus
		Selection.setFocus(mvInput.getTextField());
		};

	/*************************************************************************************************************************************************/
	public function uploadFile(file:FileReference, strDescription:String):Void{
		//remove any button or upload bar
		this.clearStage();
		//set a progress bar
		this.setProgress();
		//listener for progress and complete
		var listener:Object = new Object();								
		listener.__class = this; 
		//complete
		listener.onComplete = function(file:FileReference):Void{			
			file.removeListener(this);
			Debug("onComplete");
			this.__class.showMsg(gLang[5]);
			};
		//progress
		listener.onProgress = function(file:FileReference, bytesLoaded:Number, bytesTotal:Number):Void{
			this.__class.setLoadingProgress(gLang[10] + '<b>' + file.name + '</b>', bytesLoaded, bytesTotal);
			};
		//errors
		listener.onIOError = function(file:FileReference):Void{
			file.removeListener(this);
			this.__class.showErrorMsg(gLang[9], 'filesize');
			Debug("onIOError");
			};
		listener.onSecurityError = function(file:FileReference, errorString:String):Void{
			file.removeListener(this);
			this.__class.showErrorMsg(gLang[9], 'filesize');
			Debug("onSecurityError: " + errorString);
			};
		listener.onHTTPError = function(file:FileReference):Void{
			file.removeListener(this);
			this.__class.showErrorMsg(gLang[9], 'filesize');
			Debug("onHTTPError");
			};	
		
		//upload link build
		var strServeurUpload:String = BC.__server.__vattachement + "?&n=" + BC.__user.__nopub + "&d=" + escape(strDescription);
		file.addListener(listener);
		file.upload(strServeurUpload);
		};	
		
	/*************************************************************************************************************************************************/		
	public function setProgress(Void):Void{
		//create layer clip
		this.__mvMessage = __stage.createEmptyMovieClip('mvMsg', __stage.getNextHighestDepth());
		// créer le contenu dans les movie	
		this.__mvProgress = this.__mvMessage.attachMovie('progressBar', 'mvProgressBar', this.__mvMessage.getNextHighestDepth());
		this.__mvProgress.mvBarLoad._width = 0;
		this.__mvProgress.txtInfos.htmlText = '';	
		this.__mvProgress._x = 0;
		this.__mvProgress._y = 0;
		//pos all					
		this.__mvMessage._x = 1;
		this.__mvMessage._y = this.__hvSpacer;
		};
		
	/*************************************************************************************************************************************************/		
	public function setLoadingProgress(strText:String, iLoaded:Number, iTotal:Number):Void{	
		this.__mvProgress.mvBarLoad._width = Math.floor((Math.floor((iLoaded / iTotal) * 100) * this.__mvProgress.mvBar._width) / 100);
		this.__mvProgress.txtInfos.htmlText = strText;
		};	
	
	
	}	