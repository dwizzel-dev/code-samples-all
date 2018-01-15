/*

when somebody view the video description or message


*/
import control.CieGradientWindows;
import control.CieTextLine;
import control.CieButton;
import control.CieOptionBox;
import control.CieTools;
import messages.CieTextMessages;
import messages.CiePromptMessages;
//import messages.CieVideoConfirmMessages;
import messages.CieActionMessages;

import mx.containers.ScrollPane;

dynamic class messages.CieRecordMessages{

	static private var __className = 'CieRecordMessages';
	private var __marge:Number;
	private var __hButt:Number;
	private var __selectBoxWidth:Number;
	private var __sepPosX:Number;
	private var __rightWidth:Number;
	private var __yStartPos:Number;
	
	private var __mvContent:MovieClip;
	private var __mvContentBg:MovieClip;
	private var __mvContentLayer:MovieClip;
	private var __mvContentLeft:MovieClip;
	private var __mvContentRight:MovieClip;
	private var __arrMvStep:Array;
	private var __bRecordedOnce:Boolean;
	
	private var __mvUniRecord:MovieClip;
	
	private var __window:CieGradientWindows;
	
	private var __selectedCam:Array;
	
	private var __cActionMessages:Object; //for old description popup win
		
	public function CieRecordMessages(Void){
		this.__marge = 10;
		this.__hButt = 30;
		this.__sepPosX = 220;
		this.__rightWidth = CieStyle.__recordWindow.__width - this.__sepPosX;
		this.__yStartPos = 60;
		this.__selectBoxWidth = 200;
		this.__arrMvStep = new Array();
		this.__bRecordedOnce = false;
		this.openRecordMessage();
		};
	
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	
	public function openRecordMessage(Void):Void{
		//créer la fenetre pop-up
		this.__window = new CieGradientWindows(gLang[400], this.cbClose, this);
		this.__mvContent = this.__window.getContent();
		new CieGlow(this.__mvContent, true);
		
		//draw a bg to have the default win width
		this.__mvContentBg = this.__mvContent.createEmptyMovieClip('mvBgMinMax', this.__mvContent.getNextHighestDepth());
		this.__mvContentBg.attachMovie('mvContent', 'mvBg', this.__mvContentBg.getNextHighestDepth());
		this.__mvContentBg._x = 0;
		this.__mvContentBg._y = 0;
		this.__mvContentBg._width = CieStyle.__recordWindow.__width;
		this.__mvContentBg._height = CieStyle.__recordWindow.__height;
		
		//création du window popup
		this.__window.setWindow();
		//this.__window.drawGradient(this.__sepPosX); //when we want the gradient Horizontal on the left side
		
		//for layout
		this.__mvContentLayer = this.__mvContent.createEmptyMovieClip('mvLayer', this.__mvContent.getNextHighestDepth());
		this.__mvContentLeft = this.__mvContent.createEmptyMovieClip('mvLayerLeft', this.__mvContent.getNextHighestDepth());
		this.__mvContentRight = this.__mvContent.createEmptyMovieClip('mvLayerRight', this.__mvContent.getNextHighestDepth());
		
		//icon
		var mvIcon = this.__mvContentLayer.attachMovie('mvIconImage_30', 'mvIcon', this.__mvContentLayer.getNextHighestDepth());
		new CieTextLine(mvIcon, mvIcon._width, 5, 400, 0, 'tf',gLang[401], 'dynamic',[true,false,false], false, false, false, false);
		mvIcon._x = (this.__marge * 2);
		
		//separateur
		this.drawBar();
		//step one
		this.drawStep(0);
				
		};
	
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		
	private function drawChoices(iStepFocus:Number):Void{
		var tmpHeight:Number = this.__yStartPos + 5;
		var arrChoices = new Array();
		arrChoices[0] = gLang[402];
		arrChoices[1] = gLang[403];
		arrChoices[2] = gLang[404];
		arrChoices[3] = gLang[405];
		arrChoices[4] = gLang[406];
		for(var i=0; i < arrChoices.length; i++){
			if(i > 0){
				tmpHeight += this.__arrMvStep[i-1]._height + this.__marge;
				}
			if(this.__arrMvStep[i] == undefined){
				this.__arrMvStep[i] = this.__mvContentLeft.attachMovie('mvStep', 'mvStep_' + i, this.__mvContentLeft.getNextHighestDepth());
				this.__arrMvStep[i].txtInfosFront.autoSize = 'left';
				this.__arrMvStep[i].txtInfosFront._width = 170;
				}
			if(i == iStepFocus){
				this.__arrMvStep[i].txtInfosFront.htmlText = '<font color="#000000"><b>' + arrChoices[i] + '<b></font>';
				this.__arrMvStep[i].mvOn._visible = true;	
			}else{
				this.__arrMvStep[i].txtInfosFront.htmlText = '<font color="#666666">' + arrChoices[i] + '</font>';
				this.__arrMvStep[i].mvOn._visible = false;
				}
			this.__arrMvStep[i]._x = (this.__marge * 3);
			this.__arrMvStep[i]._y = tmpHeight;
			}
		};
		
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
		
	private function drawBar(Void):Void{	
		//draw the seperation bar
		var matrix = {matrixType:"box", x:this.__sepPosX, y:(this.__yStartPos - (this.__marge * 2)), w:1, h:(CieStyle.__recordWindow.__height - (this.__yStartPos - (this.__marge * 2))), r:(0.5 * Math.PI)};
		var colors:Array = [0x333333, 0x333333, 0x333333, 0x333333]; 
		var alphas:Array = [0, 100, 100, 0];
		var ratios:Array = [0, 0x44, 0xaa, 0xff];
		this.__mvContentLayer.lineStyle(1,null,null,null,null,"none");
		this.__mvContentLayer.lineGradientStyle("linear", colors, alphas, ratios, matrix, "pad", "RGB");
		this.__mvContentLayer.moveTo(this.__sepPosX, (this.__yStartPos - (this.__marge * 2)));
		this.__mvContentLayer.lineTo(this.__sepPosX, CieStyle.__recordWindow.__height);
		this.__mvContentLayer.endFill();
		};
		
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
		
	private function drawStep(iStep:Number):Void{	
		this.drawChoices(iStep);
		//steps
		if(iStep == 0){
			if(step_0_a()){
				step_0_b();
				}
		}else if(iStep == 1){
			step_1_a();
		}else if(iStep == 2){
			step_2_a();	
		}else if(iStep == 3){
			step_3_a();	
		}else if(iStep == 4){
			step_4_a();	
		}else{
			//
			}
		
		};	
		
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	
	private function step_0_a(Void):Boolean{	
		//
		var bCam:Boolean = this.detectCam();
		var bMic:Boolean = this.detectMic();
		var buttWidth:Number = 150;
		Debug("CAM_MIC_DETECT: " + bCam + ',' + bMic);
		if(!bCam || !bMic){
			var tmpHeight:Number = 0;
			//empty clip for options
			this.__mvContentRight.removeMovieClip();
			this.__mvContentRight = this.__mvContent.createEmptyMovieClip('mvLayerRight', this.__mvContent.getNextHighestDepth());
			var mvButt = this.__mvContentRight.createEmptyMovieClip('mvButt', this.__mvContentRight.getNextHighestDepth());
			var mvTexte = this.__mvContentRight.attachMovie('mvTextMessages', 'mvTextMessages', this.__mvContentRight.getNextHighestDepth());
			mvTexte.txtInfos.htmlText = gLang[407];
			mvTexte.txtInfos.autoSize = 'left';
			mvTexte.txtInfos._width = this.__rightWidth;
			//pos
			mvTexte._x = this.__marge * 2 + this.__sepPosX;
			mvTexte._y = this.__yStartPos;
			//butt
			var btnNext = new CieButton(mvButt, gLang[408], buttWidth, this.__hButt, 0, 0);
			btnNext.getMovie().__supclass = this;
			btnNext.getMovie().onRelease = function(Void):Void{
				this.__supclass.drawStep(0);
				};
			//pos	
			mvButt._x = CieStyle.__recordWindow.__width - buttWidth + (this.__marge * 2);
			mvButt._y = CieStyle.__recordWindow.__height - this.__hButt;	
			//
			return false;
			}
		//
		return true;	
		};
	
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
		
	private function step_0_b(Void):Boolean{
		var buttWidth:Number = 100;
		//empty clip for options
		this.__mvContentRight.removeMovieClip();
		this.__mvContentRight = this.__mvContent.createEmptyMovieClip('mvLayerRight', this.__mvContent.getNextHighestDepth());
		var mvButt = this.__mvContentRight.createEmptyMovieClip('mvButt', this.__mvContentRight.getNextHighestDepth());
		var mvTexte = this.__mvContentRight.attachMovie('mvTextMessages', 'mvTextMessages', this.__mvContentRight.getNextHighestDepth());	
		//arr of cameras
		var arrCam:Array = Camera.names;
		//rebuild array with index for option box
		var arrCamTmp = new Array();
		var cpt:Number = 1;
		for(var o in arrCam){
			arrCamTmp[cpt++] = new Array(o, arrCam[o]);
			}
		//the question
		mvTexte.txtInfos.htmlText = '<b>' + gLang[409] + '</b>';
		mvTexte.txtInfos.autoSize = 'left';
		mvTexte.txtInfos._width = this.__rightWidth;
		//the options cam
		optionBox = new CieOptionBox(mvTexte, arrCamTmp, 'group');
		//default value
		optionBox.setSelectionValue(1);
		//pos
		mvTexte._x = this.__marge * 2 + this.__sepPosX;
		mvTexte._y = this.__yStartPos;
		//butt
		var btnNext = new CieButton(mvButt, gLang[410], buttWidth, this.__hButt, 0, 0);
		btnNext.getMovie().__arrCams = arrCamTmp;
		btnNext.getMovie().__options = optionBox;
		btnNext.getMovie().__supclass = this;
		btnNext.getMovie().onRelease = function(Void):Void{
			this.__supclass.__selectedCam = this.__arrCams[this.__options.getSelectionValue()];
			Debug("SELECTED_CAM: " + this.__supclass.__selectedCam[0] + ' ,' + this.__supclass.__selectedCam[1]);
			this.__supclass.drawStep(1);
			};
		//pos	
		mvButt._x = CieStyle.__recordWindow.__width - buttWidth + (this.__marge * 2);
		mvButt._y = CieStyle.__recordWindow.__height - this.__hButt;	
		//		
		return true;
		};
		
	
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	
	private function step_1_a(Void):Boolean{	
		var buttWidth:Number = 100;
		//
		var tmpHeight:Number = 0;
		//empty clip for options
		this.__mvContentRight.removeMovieClip();
		this.__mvContentRight = this.__mvContent.createEmptyMovieClip('mvLayerRight', this.__mvContent.getNextHighestDepth());
		var mvButt = this.__mvContentRight.createEmptyMovieClip('mvButt', this.__mvContentRight.getNextHighestDepth());
		var mvTexte = this.__mvContentRight.attachMovie('mvTextMessages', 'mvTextMessages', this.__mvContentRight.getNextHighestDepth());
		mvTexte.txtInfos.htmlText = gLang[411];
		mvTexte.txtInfos.autoSize = 'left';
		mvTexte.txtInfos._width = this.__rightWidth;
		//pos
		mvTexte._x = this.__marge * 2 + this.__sepPosX;
		mvTexte._y = this.__yStartPos;
		
		//option one
		//will be a button
		tmpHeight +=  mvTexte._height + (this.__marge * 2);
		var toolVideo = new CieTools(mvTexte, 'video', '32X32', 'mvIconImage_30');
		toolVideo.setAction('onclick', 'openParametreFlash', [3]);
		toolVideo.changeColor(CieStyle.__basic.__buttonColor, CieStyle.__basic.__buttonBorderWidth, CieStyle.__basic.__buttonBorderColor, CieStyle.__basic.__buttonEffectColor, CieStyle.__basic.__buttonEffectColorOff);
		toolVideo.redraw((this.__marge * 1.5), tmpHeight);
		new CieTextLine(mvTexte, ((this.__marge * 2) + toolVideo.getIconWidth()), (tmpHeight + 5), 0, 300, 'tf', gLang[412], 'dynamic',[true,false,false], false, false, false, false);
		tmpHeight =  mvTexte._height + this.__marge;	

		//option two
		//will be a button
		tmpHeight +=  this.__marge;
		var toolAudio = new CieTools(mvTexte, 'audio', '32X32', 'mvIconImage_29');
		toolAudio.setAction('onclick', 'openParametreFlash', [2]);
		toolAudio.changeColor(CieStyle.__basic.__buttonColor, CieStyle.__basic.__buttonBorderWidth, CieStyle.__basic.__buttonBorderColor, CieStyle.__basic.__buttonEffectColor, CieStyle.__basic.__buttonEffectColorOff);
		toolAudio.redraw((this.__marge * 1.5), tmpHeight);
		new CieTextLine(mvTexte, ((this.__marge * 2) + toolVideo.getIconWidth()), (tmpHeight + 5), 0, 300, 'tf', gLang[413], 'dynamic',[true,false,false], false, false, false, false);
				
		//butt
		var btnNext = new CieButton(mvButt, gLang[410], buttWidth, this.__hButt, 0, 0);
		btnNext.getMovie().__supclass = this;
		btnNext.getMovie().onRelease = function(Void):Void{
			this.__supclass.drawStep(2);
			};
		//pos	
		mvButt._x = CieStyle.__recordWindow.__width - buttWidth + (this.__marge * 2);
		mvButt._y = CieStyle.__recordWindow.__height - this.__hButt;	
		//
		return true;	
		};	
		
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	
	private function step_2_a(Void):Boolean{	
		//
		var buttWidth:Number = 100;
		var tmpHeight:Number = 0;
		//empty clip for options
		this.__mvContentRight.removeMovieClip();
		this.__mvContentRight = this.__mvContent.createEmptyMovieClip('mvLayerRight', this.__mvContent.getNextHighestDepth());
		var mvButt = this.__mvContentRight.createEmptyMovieClip('mvButt', this.__mvContentRight.getNextHighestDepth());
		
		//scroll pane car ca va etre long
		var depth = this.__mv.getNextHighestDepth(); //patch for F** macromedia shit
		var mvTexte = this.__mvContentRight.createClassObject(ScrollPane, 'SCROLL_TUTORIAL', this.__mvContentRight.getNextHighestDepth());		
		mvTexte.setStyle('borderStyle', 'none');
		mvTexte.hScrollPolicy = false;
		mvTexte.vLineScrollSize = CieStyle.__panel.__scrollSize;
		mvTexte.tabEnabled = false;
		mvTexte.vScrollPolicy = 'auto';
		//le contenu
		mvTexte.contentPath = "mvContent";
		var mvTmp:MovieClip = mvTexte.content.createEmptyMovieClip('mvTutorial', mvTexte.content.getNextHighestDepth());
		mvTmp._x = 0;
		mvTmp._y = 0;
		var mv:MovieClip = mvTmp.attachMovie('mvTextMessages', 'mvTextMessages', mvTmp.getNextHighestDepth());
		mv._x = 0;
		mv._y = 0;
		mv.txtInfos.htmlText = gLang[414];
		mv.txtInfos.autoSize = 'left';
		mv.txtInfos._width = this.__rightWidth - (this.__marge * 2);
				
		//size and pos
		mvTexte.setSize(this.__rightWidth, (CieStyle.__recordWindow.__height - this.__yStartPos - this.__hButt - (this.__marge * 2)));
		mvTexte.move((this.__marge * 2 + this.__sepPosX), this.__yStartPos);
				
		//butt
		var btnNext = new CieButton(mvButt, gLang[410], buttWidth, this.__hButt, 0, 0);
		btnNext.getMovie().__supclass = this;
		btnNext.getMovie().onRelease = function(Void):Void{
			this.__supclass.drawStep(3);
			};
		//pos butt
		mvButt._x = CieStyle.__recordWindow.__width - buttWidth + (this.__marge * 2);
		mvButt._y = CieStyle.__recordWindow.__height - this.__hButt;	
		//
		return true;	
		};	
	
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	
	//fetchde la room avec le serveruSocket
	private function step_3_a(Void):Boolean{	
		//
		var buttWidth:Number = 100;
		var tmpHeight:Number = 0;
		//empty clip for options
		destroyObject('SCROLL_TUTORIAL');
		this.__mvContentRight.removeMovieClip();
		this.__mvContentRight = this.__mvContent.createEmptyMovieClip('mvLayerRight', this.__mvContent.getNextHighestDepth());
		
		//loader en attente de creation d'une record room
		var mvLoader = this.__mvContentRight.attachMovie('mvLoaderAnimated', 'mvLoader', this.__mvContentRight.getNextHighestDepth());
		new CieTextLine(mvLoader, mvLoader._width, 15, 300, 0, 'tf', gLang[415], 'dynamic',[false,false,false], false, false, false, false);
		mvLoader._x = this.__marge * 2 + this.__sepPosX - 10;
		mvLoader._y = this.__yStartPos - 10;
		cSockManager.registerForRecordRoomCallBack(this.cbRecordRoom, {__class: this});
				
		//
		return true;
		};
		
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	
	//callback from socketmanager
	public function cbRecordRoom(cbObject:Object, strResult:String):Void{
		cbObject.__class.initRecord(strResult);
		};
	
	//parse des infos	
	private function initRecord(strResult:String):Void{
		Debug("RECORD_NUMBER: " + strResult);
		/*
		[username, roomnumber, accescode, maxtimetorecord]
		RECORD_NUMBER: 99778,99778,1206641103449,30
		RECORD_NUMBER: ERR_WAIT
		RECORD_NUMBER: ERR_MAXLIMITREACH
		RECORD_NUMBER: ERR_ERRORCREATION
		*/
		var iError:Number = 0;
		if(strResult == "ERR_WAIT"){
			iError = 2;
		}else if(strResult == "ERR_MAXLIMITREACH"){
			iError = 3;	
		}else if(strResult == "ERR_ERRORCREATION"){
			iError = 4;		
		}else{
			var arrResult:Array = strResult.split(',');
			if(arrResult.length == 4){
				BC.__record.__username = arrResult[0];
				BC.__record.__room = arrResult[1];
				BC.__record.__accesscode = arrResult[2];
				BC.__record.__maxRecordTime = arrResult[3];
				//previously selected
				BC.__record.__cam_index = this.__selectedCam[0];
				//ref to this window
				BC.__record.__super = this;
			}else{
				iError = 1; //GENERIC ERROR
				}
			}
			
		if(iError){
			this.step_3_c(iError); //error no session created
		}else{
			this.step_3_b(); //show UniRecord
			}
			
		};

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	//si a atteint sa limite de temps est calle par UniRecord
	private function sessionTimeLimitReach(Void):Void{
		this.step_3_d();
		};
		
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	//allback when user has record at leat one stream to enable the save butt
	private function recordedOnce(Void):Void{
		this.__bRecordedOnce = true;
		};	
	
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	
	//montre le unirecord
	private function step_3_b(Void):Boolean{	
		//UniRecord here
		//
		var buttWidth:Number = 100;
		var tmpHeight:Number = 0;
		//empty clip for options
		this.__mvContentRight.removeMovieClip();
		this.__mvContentRight = this.__mvContent.createEmptyMovieClip('mvLayerRight', this.__mvContent.getNextHighestDepth());
		
		//layers
		var mvButt = this.__mvContentRight.createEmptyMovieClip('mvButt', this.__mvContentRight.getNextHighestDepth());
		var mvTexte = this.__mvContentRight.attachMovie('mvTextMessages', 'mvTextMessages', this.__mvContentRight.getNextHighestDepth());
		this.__mvUniRecord = this.__mvContentRight.attachMovie('UniRecordSymbol', 'UniRecordSymbol', this.__mvContentRight.getNextHighestDepth());
		
		//the camera pos
		this.__mvUniRecord._x = this.__marge * 2 + this.__sepPosX;
		this.__mvUniRecord._y = this.__yStartPos - (this.__marge * 2);
		tmpHeight =  this.__mvUniRecord._height + this.__mvUniRecord._y + this.__marge;
		
		//the text and pos
		mvTexte.txtInfos.htmlText = gLang[416];
		mvTexte.txtInfos.autoSize = 'left';
		mvTexte.txtInfos._width = this.__rightWidth;
		//pos
		mvTexte._x = this.__marge * 2 + this.__sepPosX;
		mvTexte._y = tmpHeight;
		
		//butt
		var btnNext = new CieButton(mvButt, gLang[417], buttWidth, this.__hButt, 0, 0);
		btnNext.getMovie().__supclass = this;
		btnNext.getMovie().onRelease = function(Void):Void{
			if(!this.__supclass.__bRecordedOnce){
				new CieTextMessages('MB_OK', gLang[418], gLang[419]);
			}else{
				this.__supclass.openTitleBox();
				/*
				this.__supclass.__mvUniRecord.keepFile();
				this.__supclass.drawStep(4);
				*/
				}
			};
		//pos butt
		mvButt._x = CieStyle.__recordWindow.__width - buttWidth + (this.__marge * 2);
		mvButt._y = CieStyle.__recordWindow.__height - this.__hButt;
				
		//
		return true;
		};
		
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	
	//erreur de creation de session
	private function step_3_c(iError:Number):Boolean{	
		//
		var buttWidth:Number = 200;
		var tmpHeight:Number = 0;
		//empty clip for options
		this.__mvContentRight.removeMovieClip();
		this.__mvContentRight = this.__mvContent.createEmptyMovieClip('mvLayerRight', this.__mvContent.getNextHighestDepth());
		
		//message
		var mvButt = this.__mvContentRight.createEmptyMovieClip('mvButt', this.__mvContentRight.getNextHighestDepth());
		var mvTexte = this.__mvContentRight.attachMovie('mvTextMessages', 'mvTextMessages', this.__mvContentRight.getNextHighestDepth());
		var strError:String = "";
		if(iError == 2){ 
			strError = gLang[420];
		}else if(iError == 3){
			strError = gLang[421];
		}else if(iError == 4){
			strError = gLang[422];
		}else{ //generic
			strError =  gLang[423];
			}
		mvTexte.txtInfos.htmlText = strError;	
		mvTexte.txtInfos.autoSize = 'left';
		mvTexte.txtInfos._width = this.__rightWidth;
		//pos
		mvTexte._x = this.__marge * 2 + this.__sepPosX;
		mvTexte._y = this.__yStartPos;
		
		//butt
		var btnNext = new CieButton(mvButt, gLang[424], buttWidth, this.__hButt, 0, 0);
		btnNext.getMovie().__supclass = this;
		btnNext.getMovie().onRelease = function(Void):Void{
			this.__supclass.drawStep(3);
			};
		//pos butt
		mvButt._x = CieStyle.__recordWindow.__width - buttWidth + (this.__marge * 2);
		mvButt._y = CieStyle.__recordWindow.__height - this.__hButt;	
		
		//
		return true;
		};	
		
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	
	//erreur de fin de session
	private function step_3_d(Void):Boolean{	
		
		//if an action where set
		if(this.__cActionMessages != undefined){
			this.__cActionMessages.closeWindow();
			this.__cActionMessages = undefined;
			}
		
		//flag for the record at least one time
		this.__bRecordedOnce = false;
		//
		var buttWidth:Number = 200;
		var tmpHeight:Number = 0;
		//empty clip for options
		this.__mvContentRight.removeMovieClip();
		this.__mvContentRight = this.__mvContent.createEmptyMovieClip('mvLayerRight', this.__mvContent.getNextHighestDepth());
		
		//message
		var mvButt = this.__mvContentRight.createEmptyMovieClip('mvButt', this.__mvContentRight.getNextHighestDepth());
		var mvTexte = this.__mvContentRight.attachMovie('mvTextMessages', 'mvTextMessages', this.__mvContentRight.getNextHighestDepth());
		mvTexte.txtInfos.htmlText = gLang[425];
		mvTexte.txtInfos.autoSize = 'left';
		mvTexte.txtInfos._width = this.__rightWidth;
		//pos
		mvTexte._x = this.__marge * 2 + this.__sepPosX;
		mvTexte._y = this.__yStartPos;
		
		//butt
		var btnNext = new CieButton(mvButt, gLang[426], buttWidth, this.__hButt, 0, 0);
		btnNext.getMovie().__supclass = this;
		btnNext.getMovie().onRelease = function(Void):Void{
			this.__supclass.drawStep(3);
			};
		//pos butt
		mvButt._x = CieStyle.__recordWindow.__width - buttWidth + (this.__marge * 2);
		mvButt._y = CieStyle.__recordWindow.__height - this.__hButt;	
		
		//
		return true;
		};	
	
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	//derniere etape
	private function step_4_a(Void):Boolean{
		var buttWidth:Number = 100;
		//empty clip for options
		this.__mvContentRight.removeMovieClip();
		this.__mvContentRight = this.__mvContent.createEmptyMovieClip('mvLayerRight', this.__mvContent.getNextHighestDepth());
		var mvButt = this.__mvContentRight.createEmptyMovieClip('mvButt', this.__mvContentRight.getNextHighestDepth());
		var mvTexte = this.__mvContentRight.attachMovie('mvTextMessages', 'mvTextMessages', this.__mvContentRight.getNextHighestDepth());
		mvTexte.txtInfos.htmlText = gLang[427];
		mvTexte.txtInfos.autoSize = 'left';
		mvTexte.txtInfos._width = this.__rightWidth;
		//pos
		mvTexte._x = this.__marge * 2 + this.__sepPosX;
		mvTexte._y = this.__yStartPos;
		//butt
		var btnNext = new CieButton(mvButt, gLang[428], buttWidth, this.__hButt, 0, 0);
		btnNext.getMovie().__supclass = this;
		btnNext.getMovie().onRelease = function(Void):Void{
			this.__supclass.closeWindow();
			};
		//pos	
		mvButt._x = CieStyle.__recordWindow.__width - buttWidth + (this.__marge * 2);
		mvButt._y = CieStyle.__recordWindow.__height - this.__hButt;
		
		//
		return true;
		}

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	/*
	private function showOldDescription(Void):Void{
		//build request
		var arrD = new Array();
		arrD['methode'] = 'details';
		arrD['action'] = 'video';
		arrD['arguments'] = '';
		//add the request
		cReqManager.addRequest(arrD, this.cbShowOldDescription, {__caller:this});
		};
	*/
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	/*
	public function cbShowOldDescription(prop, oldVal:Number, newVal:Number, obj:Object){
		//Debug('GOT IT!!! IN: ' + obj.__req.getSec()/1000);
		obj.__super.__caller.openConfirmWindow(obj.__req.getXml().firstChild);
		cReqManager.removeRequest(obj.__req.getID());
		};	
	*/	
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	/*
	public function openConfirmWindow(xmlNode:XML):Void{
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.attributes.n == 'video'){
				var strPath:String = unescape(currNode.firstChild.nodeValue);
				this.__cActionMessages = new CieVideoConfirmMessages(strPath);
				this.__cActionMessages.setCallBackFunction(this.cbPromptOnConfirm, {__class: this});
				break;
			}else{
				new CieTextMessages('MB_OK', 'Desole une erreur est survenu durant la recherche de votre ancienne description video', 'Erreur');
				break;
				}
			}	
		};
	*/	
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	/*
	public function cbPromptOnConfirm(cbObject:Object, bResult:Boolean):Void{
		cbObject.__class.treatUserActionOnPromptOnConfirm(bResult);
		};
	*/
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	//keep the scope so have to pass trought another function to 	
	/*
	public function treatUserActionOnPromptOnConfirm(bResult:Boolean):Void{
		//depending on the user selection
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = undefined;	
		if(bResult){
			//confirm l'ecrasement
			//envoyer au FMS le ok via le UniRecord
			this.__mvUniRecord.keepFile();
			//show terminate step
			this.drawStep(4);
		}else{
			//nothing
			}
		//
		};	
	*/
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	
	// CEHCK FOR THE MIC AND CAMERA	
	public function detectCam(Void):Boolean{
		var arrCam = Camera.names;
		if(Camera.get() == null){
			return false;
			}	
		return true;
		};

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		
	// CEHCK FOR THE MIC AND CAMERA	
	public function detectMic(Void):Boolean{
		var arrMic = Microphone.names;
		if(Microphone.get() == null){
			return false;
			}	
		return true;
		};	
		
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
		
	public function closeWindow(Void):Void{
		BC.__record.__username = '';
		BC.__record.__room = '';
		BC.__record.__accesscode = '';
		BC.__record.__maxRecordTime = '';
		BC.__record.__cam_index = null;
		BC.__record.__super = null;
		//movies
		this.__mvUniRecord.removeMovieClip();
		delete this.__mvUniRecord;
		//if the popup was opened
		this.__cActionMessages.closeWindow();
		this.__cActionMessages = undefined;
		//destroy this window
		this.__window.destroy();
		this.__window = null;
		};
		
	public function cbClose(cbClass:Object):Void{
		//
		};
		
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
	//START FOR THER DESCRIPTION TEXT OF THE VIDEO	
	//open the popup
	private function openTitleBox(Void):Void{
		this.__cActionMessages = new CiePromptMessages(gLang[429], gLang[430]);
		this.__cActionMessages.setCallBackFunction(this.cbTitleBoxInput, {__class: this});
		};
	
	//callback when user make a choice of selection	
	public function cbTitleBoxInput(cbObject:Object):Void{
		//recfresh the section and passe the searchName
		var strTitle:String = "";
		if(cbObject.__ok == true){
			var strTitle:String = cbObject.__class.__cActionMessages.getInputText();
			}
		
		
		//delete the var holding the CieOptionMessages
		cbObject.__class.__cActionMessages = null;
		
		//***GOOD when will have more then one video description
		//cbObject.__class.openAskForMainDescription(strTitle);
		
		//***GOOD when we want only one description
		cbObject.__class.__mvUniRecord.keepFile("1" + strTitle);
		cbObject.__class.drawStep(4);	
		};	
		
	//pour demander si il veut que ce soit s description principale
	public function openAskForMainDescription(strTitle:String){
		this.__cActionMessages = new CieActionMessages(gLang[431], gLang[432]);
		this.__cActionMessages.setCallBackFunction(this.cbOpenAskForMainDescription, {__class: this, __title:strTitle});
		};
		
	public function cbOpenAskForMainDescription(cbObject:Object, bResult:Boolean):Void{
		//depending on the user selection
		cbObject.__class.__cActionMessages.closeWindow();
		cbObject.__class.__cActionMessages = null;	
		var strTitleConcat:String = cbObject.__title;
		if(bResult){
			strTitleConcat = "1" + strTitleConcat;
		}else{
			strTitleConcat = "0" + strTitleConcat;
			}
		cbObject.__class.__mvUniRecord.keepFile(strTitleConcat);
		cbObject.__class.drawStep(4);	
		};
	
		
		
	//END FOR THER DESCRIPTION TEXT OF THE VIDEO		
	};