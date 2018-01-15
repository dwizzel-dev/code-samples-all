
//import flash.filters.GlowFilter;

//import media.CieVideo;

dynamic class media.CieVideoPlayer{	
	
	static private var __className:String = 'CieVideoPlayer';
	
	private var __mv:MovieClip;
	public var __mvPlayer:MovieClip;
	private var __playerWidth = 320;
	private var __playerHeight = 240;
	//public var __interval:Number;
	public var __flvpath:String;
	public var __jpgpath:String;
	private var __hvSpacer:Number;
	
	/********************************************************************************************************************************/
	
	public function CieVideoPlayer(mv:MovieClip, path:String){
		this.__mv = mv;
		this.__bIsPlaying = false;
		this.__bIsLoaded = false;
		//path to flv and preview
		arrPath = path.split('|');
		this.__flvpath = arrPath[0];
		this.__jpgpath = arrPath[1];
		
		this.__hvSpacer = CieStyle.__profil.__hvSpacer;
		
		//init
		this.__mvPlayer = mv.attachMovie('mvMoviePlayer', 'mvMoviePlayer', mv.getNextHighestDepth());
		this.__mvPlayer.gotoAndStop('_off');
		//photo
		Debug("JPG_PATH: " + this.__jpgpath);
		this.__mvPlayer.mvLoadPlayer.mvPhoto.loadMovie(this.__jpgpath);
		//action
		this.__mvPlayer.mvLoadPlayer.__super = this;
		this.__mvPlayer.mvLoadPlayer.mvUserMsg.htmlText = "<b>cliquer ici pour demarrer la vidéo</b>";
		this.__mvPlayer.mvLoadPlayer.onRelease = function(){
			this.mvUserMsg.htmlText = "<b>un instant...</b>";
			this.__super.loadPlayer();
			};
		
		
		//load at start
		/*
		this.__mvPlayer = mv.attachMovie('mvMoviePlayer', 'mvMoviePlayer', mv.getNextHighestDepth());
		this.__mvPlayer.gotoAndStop('_off');
		this.__mvPlayer.mvLoadPlayer.mvUserMsg.htmlText = "<b>un instant</b>";
		this.loadPlayer();
		*/
		};
	
	
	/********************************************************************************************************************************/
	
	//load the player	
	public function resizePlayer(w:Number, h:Number):Void{
		//width
		if(((w - this.__playerWidth - this.__hvSpacer)/2) > this.__hvSpacer){
			this.__mvPlayer._x = (w - this.__playerWidth - this.__hvSpacer)/2;
		}else{
			this.__mvPlayer._x = this.__hvSpacer;
			}
		//height
		if(((h - this.__playerHeight - this.__hvSpacer)/2) > this.__hvSpacer){
			this.__mvPlayer._y = (h - this.__playerHeight - this.__hvSpacer)/2;
		}else{
			this.__mvPlayer._y = this.__hvSpacer;
			}
		};
	

	/********************************************************************************************************************************/
	
	//load the player	
	public function loadPlayer(Void):Void{
		//init
		this.__mvPlayer.mvLoadPlayer.onRelease = function(){};
		this.__mvPlayer.mvLoadPlayer.useHandCursor = false;	
		
		//init
		Debug("VIDEO_PATH: " + this.__flvpath);
		this.__mvPlayer.mvPlayer.autoPlay = true;
		this.__mvPlayer.mvPlayer.skin = CieStyle.__skin.__video + ".swf"; //FOR UNI
		this.__mvPlayer.mvPlayer.skinAutoHide = true;
		
		//listener
		var listenerObject:Object = new Object();
		listenerObject.__remover = this.__mvPlayer.mvPlayer;
		listenerObject.__super = this;
		listenerObject.skinLoaded = function(obj:Object):Void{
			//this.__super.__interval = setInterval(this.__super, 'checkProgress', 500, this.__super.__mvPlayer, this.__super);
			//Debug("+++++++VIDEO_IS_ON");
			this.__super.__mvPlayer.gotoAndStop('_on');
			this.__remover.removeListener(this);
			};
		this.__mvPlayer.mvPlayer.addEventListener("skinLoaded", listenerObject);	
			
		//content
		this.__mvPlayer.mvPlayer.contentPath = this.__flvpath;
		};	
	
	/********************************************************************************************************************************/
	
	//to keep the scope	
	/*
	public function checkProgress(player:MovieClip, cVideoPlayer:CieVideoPlayer):Void{
		if(player.mvPlayer == undefined){ //so not on the stage anymore stop everything
			cVideoPlayer.removePlayer();
			}
		};	
	*/

	/********************************************************************************************************************************/	
		
	public function removePlayer(Void):Void{
		//Debug("~~~~~~~~~~~~~~~~~~~~~~~~~VIDEO_REMOVE");
		//clear interval
		//clearInterval(this.__interval);
		//delete movie
		this.__mvPlayer.removeMovieClip();
		//delete the sound
		delete this.__mvPlayer;
		};
	
	/********************************************************************************************************************************/		
		
	public function getMovie(Void):MovieClip{
		return this.__mvPlayer;
		};	
	
	/********************************************************************************************************************************/
	
	public function getPlayer(Void):MovieClip{
		return this.__mvPlayer.mvPlayer;
		};	
	
	/********************************************************************************************************************************/
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieVideoPlayer{
		return this;
		};	
	};