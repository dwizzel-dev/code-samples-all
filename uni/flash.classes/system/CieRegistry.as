/*

registry manipulation

*/

dynamic class system.CieRegistry{

	private var __className = 'CieRegistry';
	static private var __instance:CieRegistry;
	
	private var __runKeyBase:Number;
	private var __runKeyPath:String;
	private var __runKeyName:String;	
	
	private var __keyBase:Number;
	private var __keyPath:String; 
	private var __keyName:String;
	
	private function CieRegistry(Void){
		
		this.__runKeyBase = 3;
		this.__runKeyPath = '\\SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\Run';
		this.__runKeyName = 'UniMessenger';
		
		this.__keyBase = 2;
		this.__keyPath = '\\SOFTWARE\\';
		this.__keyName = 'UniMessenger';
		
		this.checkIfKeyPathExist();
		};
		
	static public function getInstance(Void):CieRegistry{
		if(__instance == undefined) {
			__instance = new CieRegistry();
			}
		return __instance;
		};		
		
	public function runOnStart(strAppPath:String):Void{
		mdm.System.Registry.saveString(this.__runKeyBase, this.__runKeyPath, this.__runKeyName, strAppPath);
		};
		
	public function removeRunOnStart(Void){
		//NONE OF THOSE FUCKING WORK
		//mdm.System.Registry.deleteValue(this.__runKeyBase, this.__runKeyPath + '\\' + this.__runKeyName);
		//mdm.System.Registry.deleteKey(this.__runKeyBase, this.__runKeyPath + '\\' + this.__runKeyName);
		mdm.System.Registry.saveString(this.__runKeyBase, this.__runKeyPath, this.__runKeyName, '');
		}	
		
	private function checkIfKeyPathExist(Void):Void{
		if(!mdm.System.Registry.keyExists(this.__keyBase, this.__keyPath + this.__keyName)){
			this.createBaseKey();	
			}
		};
		
	private function checkIfKeyExist(keyName:String):Boolean{
		var regKeys:Array = mdm.System.Registry.getValueNames(this.__keyBase, this.__keyPath + this.__keyName);
		for(var o in regKeys){
			if(regKeys[o] == keyName) {
				return true;
				}
			}
		return false;
		}
		
	public function	setKey(keyName:String, keyValue:String):Void{
		mdm.System.Registry.saveString(this.__keyBase, this.__keyPath + this.__keyName, keyName, keyValue);
		};
		
	public function	getKey(keyName:String):String{
		if(this.checkIfKeyExist(keyName)){
			return mdm.System.Registry.loadString(this.__keyBase, this.__keyPath + this.__keyName, keyName);
		}else{
			this.setKey(keyName, '');
			return '';
			}
		};	
		
	private function createBaseKey(Void):Void{
		mdm.System.Registry.createKey(this.__keyBase, this.__keyPath + this.__keyName, '');
		};
	
	/*
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieRegistry{
		return this;
		};
	*/	
	};