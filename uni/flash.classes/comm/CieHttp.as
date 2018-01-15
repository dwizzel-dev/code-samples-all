/*

class des retour de fichier en demande en http au  lieu de Ftp
works with Mdm only, need compile and Excutable from Mdm


*/


dynamic class comm.CieHttp{

	static private var __className:String = 'CieHttp';
	private var __http:Object;
	private var __cbFunction:Function;
	private var __cbClass:Object;
	
	public function CieHttp(callBackFunction:Function, callBackClass:Object){
		this.__cbFunction = callBackFunction;
		this.__cbClass = callBackClass;
		this.init();
		};
		
	private function init(Void):Void{
		this.__http = new mdm.HTTP();
		this.__http.__super = this;
		
		this.__http.onProgress = function(obj){
			this.__super.__cbFunction(this.__super.__cbClass, 'progress', (obj.bytesTransferred/obj.bytesTotal) * 100);
			};	
		this.__http.onError = function(){
			this.__super.__cbFunction(this.__super.__cbClass, 'error', 0);
			};
		this.__http.onBinaryTransferComplete = function(obj){
			this.__super.__cbFunction(this.__super.__cbClass, 'finish', 100);
			};
		this.__http.onTransferComplete = function(obj){
			//
			};
	
		};
		
	public function getFile(filePath:String, savePath:String):Void{
		this.__http.getFile(filePath, '', '', savePath);
		};
	
	public function destroy(Void):Void{
		this.__http.close();
		this.__http = null;
		delete this.__http;
		this = null;
		delete this;
		};
	/*	
	public function getClassName(Void):String{
		return __className;
		};
	*/
	/*
	public function getClass(Void):CieHttp{
		return this;
		};
	*/	
	}	

