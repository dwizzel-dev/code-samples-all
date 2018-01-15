/*

when somebody view the video description or message


*/
import control.CieWindows;
import media.CieVideoPlayer;
import control.CieButton;

dynamic class messages.CieVideoConfirmMessages{

	static private var __className = 'CieVideoConfirmMessages';
	private var __marge:Number;
	private var __hButt:Number;
	private var __selectBoxWidth:Number;
	
	private var __mvContent:MovieClip;
	private var __cPlayer:CieVideoPlayer; 
		
	private var __window:CieWindows;
		
	private var __path:String;
	
	private var __cbClass:Object;
	private var __cbFunction:Function;
	
	private var __textWidth:Number;
	
	/*************************************************************************************************************************************************/	
	public function CieVideoConfirmMessages(strPath:String){
		this.__path = strPath;
		this.__marge = 10;
		this.__hButt = 30;
		this.__selectBoxWidth = 200;
		this.__textWidth = 320;
		this.openVideoMessage();
		};
	
	/*************************************************************************************************************************************************/	
	public function openVideoMessage(Void):Void{
		//créer la fenetre pop-up
		this.__window = new CieWindows('Confirmation d\'ecrasement', this.cbClose, this);
		this.__mvContent = this.__window.getContent();
		new CieGlow(this.__mvContent, true);
		
		// créer le contenu dans les movie	
		this.__mvTexte = this.__mvContent.attachMovie('mvTextMessages', 'mvTextMessages', this.__mvContent.getNextHighestDepth());	
		this.__mvTexte.txtInfos.htmlText = "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.";
		this.__mvTexte.txtInfos.autoSize = 'left';
		this.__mvTexte.txtInfos._width = this.__textWidth;
		
		//creer le player
		this.__cPlayer = new CieVideoPlayer(this.__mvContent, this.__path);
		
		//movies boutons
		var mvButtOK = this.__mvContent.createEmptyMovieClip('mvButtOK', this.__mvContent.getNextHighestDepth());
		var mvButtCANCEL = this.__mvContent.createEmptyMovieClip('mvButtCANCEL', this.__mvContent.getNextHighestDepth());
		
		//contenu butt
		var btnOK = new CieButton(mvButtOK, 'accepter', this.__selectBoxWidth/2, this.__hButt, 0, 0);
		var btnCANCEL = new CieButton(mvButtCANCEL, 'refuser', this.__selectBoxWidth/2, this.__hButt, 0, 0);

		//pos the player
		this.__cPlayer.getMovie()._y = this.__mvTexte._y + this.__mvTexte._height + (this.__marge * 2);
		
		//mvButt OK
		mvButtOK._y = mvButtCANCEL._y = this.__cPlayer.getMovie()._y + this.__cPlayer.getMovie()._height + (this.__marge * 2);
				
		//création du window popup
		this.__window.setWindow();
		
		//pos X
		this.__mvTexte._x = (this.__marge * 2);
		
		//pos X
		this.__cPlayer.getMovie()._x = (this.__marge * 2);
		
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
	public function closeWindow(Void):Void{
		//
		this.__cPlayer.removePlayer();
		this.__window.destroy();
		this.__window = null;
		};
		
	/*************************************************************************************************************************************************/	
	public function cbClose(cbObject:Object):Void{
		cbObject.__cPlayer.removePlayer();
		cbObject.__cbFunction(cbObject.__cbClass, false);
		};
	
	};