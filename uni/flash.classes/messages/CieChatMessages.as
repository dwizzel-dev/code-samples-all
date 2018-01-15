/*

when somebdy is asking for a chat


*/
import control.CieWindows;
//import control.CieTextLine;
import control.CieButton;

dynamic class messages.CieChatMessages{

	static private var __className = 'CieChatMessages';
	private var __marge:Number;
	private var __hButt:Number;
	private var __selectBoxWidth:Number;
	
	private var __mvContent:MovieClip;
	private var __mvLoader:MovieClip;
	//private var __mvTexte:MovieClip; 
	private var __mvProfil:MovieClip; 
	private var __mvButt:MovieClip; 
	private var __textContent:String;
	
	private var __window:CieWindows;
	private var __titre:String;
	
	private var __cbClass:Object;
	private var __cbFunction:Function;
	private var __arrMiniProfil:Array;
	private var __cThreadAutoDeclined:Object;
	
	public function CieChatMessages(cTitre:String, cTexte:String, strProfil:String){
		this.__arrMiniProfil = strProfil.split('|');
		this.__marge = 10;
		this.__hButt = 30;
		this.__selectBoxWidth = 200;
		this.__titre = cTitre;
		this.__textContent = cTexte;
		this.openChatMessage();
		};
	
	public function openChatMessage(strError:String):Void{
		//créer la fenetre pop-up
		this.__window = new CieWindows(this.__titre, this.cbClose, this);
		this.__mvContent = this.__window.getContent();
		new CieGlow(this.__mvContent, true);
		
		// créer le profil
		this.__mvProfil = this.__mvContent.attachMovie('mvProfilDetailsChat', 'PROFIL_DETAILS_CHAT', this.__mvContent.getNextHighestDepth());	
		this.__mvProfil._y = 0;

		//fill up with the infos
		//INFOS ORDER: 1526743|sanglote|38|72|9|CA|1|1|1|1|kiss+me+you+fool%21|0100111|1
		this.__mvProfil.txtPseudo.htmlText = gLang[245] + this.__arrMiniProfil[1] + gLang[246] + this.__arrMiniProfil[2] + gLang[247]; 
		this.__mvProfil.txtSexe.htmlText = cFormManager.__obj['sexe'][this.__arrMiniProfil[7]][1] + ', ' + cFormManager.__obj['etatcivil'][this.__arrMiniProfil[10]][1] + ', ' + cFormManager.__obj['orientation'][this.__arrMiniProfil[6]][1] ; 
	
		//geo
		this.__mvProfil.txtVille.htmlText = unescape(this.__arrMiniProfil[3]);
				
		//slogan	
		this.__mvProfil.txtSlogan.htmlText = "\"" + unescape(this.__arrMiniProfil[8]) + '"';
			
		//put the thumbnail of the photo fi there is one
		if(this.__arrMiniProfil[4] == '2'){
			this.__mvProfil.mvPhoto.mvPicture.mvPhotoBlur.loadMovie(BC.__server.__thumbs + this.__arrMiniProfil[0].substr(0,2) + '/' + this.__arrMiniProfil[1] + '.jpg');
		}else{
			this.__mvProfil.mvPhoto.mvSexeLoader.gotoAndStop('_' + this.__arrMiniProfil[7]);
			//no image thumbnail
			}
				
		// créer la question
		this.__mvProfil.txtInfos.htmlText = this.__textContent;
		this.__mvProfil.txtInfos.autoSize = 'left';
		this.__mvProfil.txtInfos._width = this.__mvProfil._width;
		/*
		var mc:MovieClip = this.__mvContent.createEmptyMovieClip('QUESTION',  this.__mvContent.getNextHighestDepth());
		this.__mvTexte = mc.attachMovie('mvTextMessages', 'mvTextMessages', mc.getNextHighestDepth());	
		this.__mvTexte.txtInfos.htmlText = this.__textContent;
		this.__mvTexte.txtInfos.autoSize = 'left';
		this.__mvTexte.txtInfos._width = this.__mvProfil._width;
		mc._y = this.__mvProfil._height + this.__mvProfil._y;
		*/
		
		//movies boutons
		var mvButtOK = this.__mvContent.createEmptyMovieClip('mvButtOK', this.__mvContent.getNextHighestDepth());
		var mvButtCANCEL = this.__mvContent.createEmptyMovieClip('mvButtCANCEL', this.__mvContent.getNextHighestDepth());
		
		//contenu
		var btnOK = new CieButton(mvButtOK, gLang[248], this.__selectBoxWidth/2, this.__hButt, 0, 0);
		var btnCANCEL = new CieButton(mvButtCANCEL, gLang[249], this.__selectBoxWidth/2, this.__hButt, 0, 0);
		
		//position des boutons en Y
		mvButtOK._y = mvButtCANCEL._y = this.__mvProfil._y + this.__mvProfil._height + (this.__marge * 2);
		
		//création du window popup
		this.__window.setWindow();
		
		//pos X
		this.__mvProfil._x = (this.__marge * 2);
		//mc._x = (this.__marge * 2);
		
		//position des boutons en X
		var xStartPos = (this.__window.getWindowWidth() - (this.__selectBoxWidth + this.__marge))/2;
		mvButtOK._x = xStartPos;
		mvButtCANCEL._x = xStartPos + (this.__selectBoxWidth/2) + this.__marge;
				
		//action bouton ok
		btnOK.getMovie().__sup = this;
		btnOK.getMovie().onRelease = function(Void):Void{
			this.__sup.clearThread();
			//no need to call it of no function werr set
			if(this.__sup.__cbFunction != undefined){
				this.__sup.__cbFunction(this.__sup.__cbClass, true);
				}
			};
		
		//action bouton cancel
		btnCANCEL.getMovie().__sup = this;
		btnCANCEL.getMovie().onRelease = function(Void):Void{
			this.__sup.clearThread();
			if(this.__sup.__cbFunction != undefined){
				this.__sup.__cbFunction(this.__sup.__cbClass, false);
				}
			this.__sup.closeWindow();
			};
			
		//put a thread for autoresponse after a certain time
		this.__cThreadAutoDeclined = cThreadManager.newThread(45000, this, 'autoDeclined', {__supclass:this});
		};
	
	//thread for autoresponse false
	public function autoDeclined(obj:Object):Boolean{
		obj.__supclass.__cbFunction(obj.__supclass.__cbClass, false, true);
		return false;
		};
	
	public function setCallBackFunction(cbFunction:Function, cbClass:Object):Void{
		this.__cbFunction = cbFunction;
		this.__cbClass = cbClass;
		};
	
	public function closeWindow(Void):Void{
		this.clearThread();
		this.__window.destroy();
		this.__window = null;
		};
		
	public function cbClose(cbObject:Object):Void{
		cbObject.__cbFunction(cbObject.__cbClass, false);
		cbObject.clearThread();
		};	
		
	public function clearThread(Void):Void{
		this.__cThreadAutoDeclined.destroy();
		};
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieChatMessages{
		return this;
		};
	*/	
	};