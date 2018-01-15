/*



*/
import control.CieWindows;
import control.CieTextLine;
import control.CieButton;
import messages.CieTextMessages;
//import manager.CieRequestManager;;


dynamic class messages.CieLostPsw{

	static private var __className = 'CieLostPsw';
	private var __marge:Number;
	private var __hBox:Number;
	private var __hButt:Number;
	private var __selectBoxWidth:Number;
	
	private var __mvContent:MovieClip;
	private var __mvLoader:MovieClip;
	private var __mvTexte:MovieClip; 
	private var __mvTexteInput:MovieClip; 
	private var __textInput:CieTextLine;
	private var __textWidth:Number;
	
	private var __window:CieWindows;
	private var __titre:String;
	private var __emailAddress:String;
	
	public function CieLostPsw(cTitre:String){
		this.__marge = 10;
		this.__hBox = 17;
		this.__hButt = 30;
		this.__selectBoxWidth = 255;
		this.__textWidth = 275;
		this.__titre = cTitre;
		//this.__titre = "This is a test with a super long title of hell!!!";
		this.openLostPsw();
		};
	
	private function openLostPsw(Void):Void{
		//créer la fenetre pop-up
		this.__window = new CieWindows(this.__titre, this.cbClose, this);
		this.__mvContent = this.__window.getContent();
		new CieGlow(this.__mvContent, true);

		//créer les movies pour le contenu
		this.__mvTexteInput = this.__mvContent.createEmptyMovieClip('mvTexteInput', this.__mvContent.getNextHighestDepth());
		// créer le contenu dans les movie
		this.__mvTexte = this.__mvContent.attachMovie('mvTextMessages', 'mvTextMessages', this.__mvContent.getNextHighestDepth());	
		this.__mvTexte.txtInfos.text = gLang[250];
		this.__mvTexte.txtInfos.autoSize = 'left';
		this.__mvTexte.txtInfos._width = this.__textWidth;
		
		//case texte input	
		this.__textInput = new CieTextLine(this.__mvTexteInput, 0, 0, this.__selectBoxWidth, this.__hBox, 'texteInput', '', 'input', [], false, true, false, true);		
		
		//movies
		var mvButtOK = this.__mvContent.createEmptyMovieClip('mvButtOK', this.__mvContent.getNextHighestDepth());
		var mvButtCANCEL = this.__mvContent.createEmptyMovieClip('mvButtCANCEL', this.__mvContent.getNextHighestDepth());
		
		//contenu
		var btnOK = new CieButton(mvButtOK, gLang[251], this.__selectBoxWidth/2, this.__hButt, 0, 0);
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
			this.__sup.__emailAddress = this.__sup.__textInput.getSelectionText();
			this.__sup.getLostPsw();
			};		
		//action bouton cancel
		btnCANCEL.getMovie().__sup = this;
		btnCANCEL.getMovie().onRelease = function(Void):Void{
			this.__sup.__emailAddress = '';
			this.__sup.closeWindow();
			};
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
	
	public function closeWindow(Void):Void{
		this.__window.destroy();
		this.__window = null;
		};	
	
	/*************************************************************************************************************************************************/	
	
	public function getLostPsw():Void{
		this.setLoading();
		//build request
		var arrD = new Array();
			arrD['methode'] = 'lostpsw';
			arrD['action'] = '';
			arrD['arguments'] = this.__emailAddress;
		//add the request
		cReqManager.addRequest(arrD, this.cbLostPsw, this);
		};
		
	/*************************************************************************************************************************************************/	
		
	public function cbLostPsw(prop, oldVal:Number, newVal:Number, obj:Object){
		obj.__super.parseLostPsw(obj.__req.getXml().firstChild);
		cReqManager.removeRequest(obj.__req.getID());
		};	
		
	/*************************************************************************************************************************************************/	
		
	public function parseLostPsw(xmlNode:XMLNode):Void{
		//parse s'il y a une erreur
		var strError:String = '';
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.attributes.n == 'error'){
				if(currNode.firstChild.nodeValue == "ERROR_EMAIL_DONT_EXIST"){
					strError = gLang[500] + this.__emailAddress + gLang[501];
				}else{
					strError = currNode.firstChild.nodeValue;
					}
				}
			}
		//si erreur
		if(strError != ''){	
			this.closeWindow();
			new CieTextMessages('MB_OK', strError, this.__titre);
		}else{
			this.closeWindow();
			new CieTextMessages('MB_OK', gLang[252] + ' ' + this.__emailAddress, this.__titre);
			}
		};		
	
	/*************************************************************************************************************************************************/
	
	/*
	public function cbClose(cbClass:Object):Void{
		cbClass.__cbClass['__ok'] = false;
		cbClass.__cbFunction(cbClass.__cbClass);
		};
	*/
		
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieLostPsw{
		return this;
		};
	*/	
	};