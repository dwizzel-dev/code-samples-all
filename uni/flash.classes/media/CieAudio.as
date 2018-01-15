/*

class des sound extended pour controller via le SoundPlayer

*/

import media.CieAudioPlayer;

dynamic class media.CieAudio extends Sound{

	static private var __className:String = 'CieAudio';
	private var __cAudioPlayer:CieAudioPlayer;
			
	public function CieAudio(cAudioPlayer:CieAudioPlayer){
		super();
		this.__cAudioPlayer = cAudioPlayer;
		};
		
	public function onLoad(bOK:Boolean):Void{
		if(bOK){
			this.__cAudioPlayer.startControl();
		}else{
			this.__cAudioPlayer.resetControl(true);
			}
		};	
		
	public function onSoundComplete(Void):Void{
		this.__cAudioPlayer.resetControl(false);
		};		
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieAudio{
		return this;
		};
	}	
