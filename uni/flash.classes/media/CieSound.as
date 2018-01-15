/*

class des sound extended pour controller via le SoundPlayer

*/

import media.CieSoundPlayer;

dynamic class media.CieSound extends Sound{

	static private var __className:String = 'CieSound';
	private var __cSoundPlayer:CieSoundPlayer;
			
	public function CieSound(cSoundPlayer:CieSoundPlayer){
		super();
		this.__cSoundPlayer = cSoundPlayer;
		};
		
	public function onLoad(bOK:Boolean):Void{
		if(bOK){
			this.__cSoundPlayer.startControl();
		}else{
			this.__cSoundPlayer.resetControl(true);
			}
		};	
		
	public function onSoundComplete(Void):Void{
		this.__cSoundPlayer.resetControl(false);
		};		
		
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieSound{
		return this;
		};
	}	
