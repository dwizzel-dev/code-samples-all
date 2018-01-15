/*

when somebody view the video description or message


*/
import control.CieWindows;
import media.CieAudioPlayer;
import control.CieTextLine;

dynamic class messages.CieAudioMessages{

	static private var __className = 'CieAudioMessages';
	private var __marge:Number;
	private var __selectBoxWidth:Number;
	
	private var __mvContent:MovieClip;
	private var __cPlayer:CieAudioPlayer; 
		
	private var __window:CieWindows;
	
	private var __arrInfos:Array;
	
	public function CieAudioMessages(arrInfos:Array){
		this.__arrInfos = arrInfos;
		this.__marge = 10;
		this.__selectBoxWidth = 200;
		this.openAudioMessage();
		};
	
	public function openAudioMessage(Void):Void{
		//créer la fenetre pop-up
		this.__window = new CieWindows('Description audio', this.cbClose, this);
		this.__mvContent = this.__window.getContent();
		new CieGlow(this.__mvContent, true);
		
		//icon
		var mvIcon = this.__mvContent.attachMovie('mvIconImage_29', 'mvIcon', this.__mvContent.getNextHighestDepth());
		new CieTextLine(mvIcon, mvIcon._width, 5, 0, 200, 'tf', 'description vocal de ' + this.__arrInfos[1], 'dynamic',[true,false,false], false, false, false, false);
		
		//pos icon
		mvIcon._x = (this.__marge * 2);
		
		//creer le player
		this.__cPlayer = new CieAudioPlayer(this.__mvContent, this.__arrInfos[2]);
		
		//pos the player
		this.__cPlayer.getMovie()._y = mvIcon._y + mvIcon._height + this.__marge;
		this.__cPlayer.getMovie()._x = (this.__marge * 2);
		
		//création du window popup
		this.__window.setWindow();
		};
	
	public function closeWindow(Void):Void{
		this.__cPlayer.removePlayer();
		this.__window.destroy();
		this.__window = null;
		};
		
	public function cbClose(cbClass:Object):Void{
		cbClass.__cPlayer.removePlayer();
		};
	};