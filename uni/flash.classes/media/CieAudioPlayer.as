
//import flash.filters.GlowFilter;

import media.CieAudio;

dynamic class media.CieAudioPlayer{	
	
	static private var __className:String = 'CieAudioPlayer';
	
	public var __mvPlayer:MovieClip;
	public var __interval:Number;
	public var __bIsPlaying:Boolean;
	public var __bIsLoaded:Boolean;
	public var __path:String;
	public var __cursorPos:Number;
	public var __sound:CieAudio;
	
	/********************************************************************************************************************************/
	
	public function CieAudioPlayer(mv:MovieClip, path:String){
	
		this.__bIsPlaying = false;
		this.__bIsLoaded = false;
		this.__path = path;
		this.__cursorPos = 0;
		
		//reset
		this.__mvPlayer = mv.attachMovie('mvSoundPlayer', 'mvSoundPlayer', mv.getNextHighestDepth());
		this.__mvPlayer.mvSeekBar.mvBarMusic._x = 0;
		this.__mvPlayer.mvSeekBar.mvBarLoad._width = 0;
		
		//set icon
		this.__mvPlayer.mvControl.mvPlayState.gotoAndStop('_on');
		this.__mvPlayer.mvControl.mvStopState.gotoAndStop('_off');
		this.__mvPlayer.mvControl.mvStopAction._visible = false;
		
		//set the button
		this.setAction();
		this.loadMp3();
		};
	
	/********************************************************************************************************************************/
	
	private function loadMp3(Void):Void{
		//Debug("SOUND_LOADING");
		//sound
		this.__sound = new CieAudio(this);
		this.__bIsLoaded = true;
		//load the sound	
		this.__sound.loadSound(this.__path, false);
		};
	
	/********************************************************************************************************************************/
	
	//to keep the scope	
	public function checkProgress(cSound:CieAudio, player:MovieClip, cAudioPlayer:CieAudioPlayer):Void{
		//seek bar load
		player.mvSeekBar.mvBarLoad._width = Math.floor((Math.floor((cSound.getBytesLoaded() / cSound.getBytesTotal()) * 100) * player.mvSeekBar.mvBar._width) / 100);
		//seek bar played
		player.mvSeekBar.mvBarMusic._x = Math.floor((Math.floor((cSound.position / cSound.duration) * 100) * player.mvSeekBar.mvBar._width) / 100);
		//ok if movie _width is undefined then we ahave to remove it because it was overwrited by another detailsedProfil
		if(player.mvSeekBar.mvBar == undefined){ //so not on the stage anymore stop everything
			cAudioPlayer.removePlayer();
			}
		};	
	

	/********************************************************************************************************************************/	
		
	public function removePlayer(Void):Void{
		//Debug("SOUND_REMOVE");
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
		//Debug("SOUND_STOP");
		//clear interval
		clearInterval(this.__interval);
		//stop the soiund
		this.__sound.stop();
		//control to 0
		this.__mvPlayer.mvControl.mvPlayState.gotoAndStop('_on');
		this.__mvPlayer.mvControl.mvStopState.gotoAndStop('_off');
		this.__mvPlayer.mvControl.mvStopAction._visible = false;
		//flag
		this.__bIsPlaying = false;
		//bar
		this.__mvPlayer.mvSeekBar.mvBarMusic._x = 0;
		//this.__mvPlayer.mvSeekBar.mvBarLoad._width = 0;
		this.__cursorPos = 0;
		//error when loading the sound
		if(bErrorOnLoad){
			//reset the flag loaded
			Debug('***ERR_SOUND_LOADING: ' + this.__path);
			this.__bIsLoaded = false;
			}
		};
		
	/********************************************************************************************************************************/	
		
	public function startControl(Void):Void{
		//Debug("SOUND_START");
		//control to 0
		if(this.__bIsPlaying){
			//clear interval
			clearInterval(this.__interval);
			//butt play/pause
			this.__mvPlayer.mvControl.mvPlayState.gotoAndStop('_on');
			//flgs
			this.__bIsPlaying = false;
			this.__cursorPos = (this.__sound.position/1000); //en millisecond
			this.__sound.stop();
		}else{
			this.setAction();
			this.__mvPlayer.mvControl.mvPlayState.gotoAndStop('_off');
			this.__mvPlayer.mvControl.mvStopState.gotoAndStop('_on');
			this.__mvPlayer.mvControl.mvStopAction._visible = true;
			//flag
			this.__bIsPlaying = true;
			//bar
			if(!this.__cursorPos){
				this.__mvPlayer.mvSeekBar.mvBarMusic._x = 0;
				//this.__mvPlayer.mvSeekBar.mvBarLoad._width = 0;
				}
			//clear interval
			clearInterval(this.__interval);
			//set a new one
			this.__interval = setInterval(this, 'checkProgress', 100, this.__sound, this.__mvPlayer, this);
			//start the soiund
			this.__sound.start(this.__cursorPos); //en second
			}
		};	
		
	/********************************************************************************************************************************/	
			
	public function setAction(Void):Void{
		//ref
		this.__mvPlayer.mvControl.mvPlayAction.__super = this;
		this.__mvPlayer.mvControl.mvStopAction.__super = this;
		//action
		this.__mvPlayer.mvControl.mvPlayAction.onRelease = function(Void):Void{
			if(!this.__super.__bIsPlaying){
				//check if the sound was loaded
				if(!this.__super.__bIsLoaded){
					//load the sound
					this.__super.loadMp3();
				}else{
					//play
					this.__super.startControl();
					}
			}else{
				//play
				this.__super.startControl();
				}
			};
		//action
		this.__mvPlayer.mvControl.mvStopAction.onRelease = function(Void):Void{	
			this.__super.resetControl(false);
			};
		//action
		this.__mvPlayer.mvSeekBar.mvBarMusic.__sup = this;
		this.__mvPlayer.mvSeekBar.mvBarMusic.__basepos = this.__mvPlayer.mvSeekBar.mvBar;
		this.__mvPlayer.mvSeekBar.mvBarMusic.onPress = function(){
			if(this.__sup.__bIsLoaded){
				this.startDrag(false, this.__basepos._x, 11, (this.__basepos._x + this.__basepos._width), 11);
				if(this.__sup.__bIsPlaying){
					this.__sup.startControl();
					}
				this.__bIsDraging = true;
				}
			};
		this.__mvPlayer.mvSeekBar.mvBarMusic.onRelease = this.__mvPlayer.mvSeekBar.mvBarMusic.onReleaseOutside = function(){
			if(this.__bIsDraging){
				this.stopDrag();
				this.__sup.__cursorPos = (((this._x - this.__basepos._x)/this.__basepos._width) * this.__sup.__sound.duration)/1000;
				if(this.__sup.__cursorPos >= (this.__sup.__sound.duration/1000)){
					this.__sup.__cursorPos = 0;
					}
				this.__sup.startControl();
				}
			this.__bIsDraging = false;
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
	
	public function getClass(Void):CieAudioPlayer{
		return this;
		};	
	};