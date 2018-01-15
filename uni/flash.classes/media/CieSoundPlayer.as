
//import flash.filters.GlowFilter;

import media.CieSound;

dynamic class media.CieSoundPlayer{	
	
	static private var __className:String = 'CieSoundPlayer';
	
	public var __mvPlayer:MovieClip;
	public var __interval:Number;
	public var __bIsPlaying:Boolean;
	public var __bIsLoaded:Boolean;
	//public var __sound:CieSound;	
	public var __path:String;
	
	/********************************************************************************************************************************/
	
	public function CieSoundPlayer(mv:MovieClip, path:String){
	
		this.__bIsPlaying = false;
		this.__bIsLoaded = false;
		this.__path = path;
		
		//reset
		this.__mvPlayer = mv.attachMovie('mvSoundPlayer', 'mvSoundPlayer', mv.getNextHighestDepth());
		this.__mvPlayer.mvSeekBar.mvBarMusic._width = 0;
		this.__mvPlayer.mvSeekBar.mvBarLoad._width = 0;
		
		//set icon
		this.__mvPlayer.mvControl.mvPlayState.gotoAndStop('_play');
		
		//set the button
		this.setAction();
		};
	
	/********************************************************************************************************************************/
	
	private function loadMp3(Void):Void{
		Debug("SOUND_LOADING");
		//sound
		this.__sound = new CieSound(this);
		//load the sound	
		this.__sound.loadSound(this.__path, false);
		//function will be called by F** extends of Sound by CieSound
		};
	
	/********************************************************************************************************************************/
	
	//to keep the scope	
	public function checkProgress(cSound:CieSound, player:MovieClip, cSoundPlayer:CieSoundPlayer):Void{
		//seek bar load
		player.mvSeekBar.mvBarLoad._width = Math.floor((Math.floor((cSound.getBytesLoaded() / cSound.getBytesTotal()) * 100) * player.mvSeekBar.mvBar._width) / 100);
		//seek bar played
		player.mvSeekBar.mvBarMusic._width = Math.floor((Math.floor((cSound.position / cSound.duration) * 100) * player.mvSeekBar.mvBar._width) / 100);
		//ok if movie _width is undefined then we ahave to remove it because it was overwrited by another detailsedProfil
		if(player.mvSeekBar.mvBar == undefined){ //so not on the stage anymore stop everything
			cSoundPlayer.removeSoundPlayer();
			}
		};	
	

	/********************************************************************************************************************************/	
		
	public function removeSoundPlayer(Void):Void{
		Debug("SOUND_REMOVE");
		//clear interval
		clearInterval(this.__interval);
		//stop the soiund
		this.__sound.stop();
		//delete movie
		this.__mvPlayer.removeMovieClip();
		//delete the sound
		delete this.__sound;
		};
	
	/********************************************************************************************************************************/	
		
	public function resetControl(bErrorOnLoad:Boolean):Void{
		Debug("SOUND_STOP");
		//clear interval
		clearInterval(this.__interval);
		//stop the soiund
		this.__sound.stop();
		//control to 0
		this.__mvPlayer.mvControl.mvPlayState.gotoAndStop('_play');
		//flag
		this.__bIsPlaying = false;
		//bar
		this.__mvPlayer.mvSeekBar.mvBarMusic._width = 0;
		this.__mvPlayer.mvSeekBar.mvBarLoad._width = 0;
		//error when loading the sound
		if(bErrorOnLoad){
			//reset the flag loaded
			Debug('***ERR_SOUND_LOADING: ' + this.__path);
			this.__bIsLoaded = false;
			}
		};
		
	/********************************************************************************************************************************/	
		
	public function startControl(Void):Void{
		Debug("SOUND_START");
		//control to 0
		this.__mvPlayer.mvControl.mvPlayState.gotoAndStop('_stop');
		//flag
		this.__bIsPlaying = true;
		//bar
		this.__mvPlayer.mvSeekBar.mvBarMusic._width = 0;
		this.__mvPlayer.mvSeekBar.mvBarLoad._width = 0;
		//clear interval
		clearInterval(this.__interval);
		//set a new one
		this.__interval = setInterval(this, 'checkProgress', 100, this.__sound, this.__mvPlayer, this);
		//start the soiund
		this.__sound.start();
		};	
		
	/********************************************************************************************************************************/	
			
	public function setAction(Void):Void{
		//ref
		this.__mvPlayer.mvControl.mvAction.__super = this;
		//action
		this.__mvPlayer.mvControl.mvAction.onRelease = function(Void):Void{
			if(!this.__super.__bIsPlaying){
				//check if the sound was loaded
				if(!this.__super.__bIsLoaded){
					//load the sound
					this.__super.__bIsLoaded = true;
					this.__super.loadMp3();
				}else{
					//change icon
					this.__super.startControl();
					}
			}else{
				this.__super.resetControl(false);
				}
			};
		};
		
	/********************************************************************************************************************************/	
		
	public function getMovie(Void):MovieClip{
		return this.__mvPlayer;
		};	
	
	/********************************************************************************************************************************/
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieSoundPlayer{
		return this;
		};	
	};