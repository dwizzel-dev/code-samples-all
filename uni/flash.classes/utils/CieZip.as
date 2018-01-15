/*

class de decompression de fichier ZIP uniquement
works with the MDM extension DLL flashvnn_zip.fspx
only work when compiled to an EXE via MDM

*/


dynamic class utils.CieZip{

	static private var __className:String = 'CieZip';
	
	private var __filePath:String;
	private var __extractPath:String;
	private var __zipPsw:String;
	private var __zipContent:Array;
	private var __zipNumberOfFilesToUnzip:Number;
	private var __zipNumberOfFilesUnzipped:Number;
	private var __numOfCheck:Number;
	private var __numMaxOfCheck:Number;
	private var __checkUnzippedInterval:Number;
	private var __cbFunction:Function;
	private var __cbClass:Object;
	
	public function CieZip(filePath:String, extractPath:String, zipPsw:String, zipContent:Array, callBackFunction:Function, callBackClass:Object){
		this.__cbFunction = callBackFunction;
		this.__cbClass = callBackClass;
		this.__filePath = filePath;
		this.__extractPath = extractPath;
		this.__zipPsw = zipPsw;
		this.__zipContent = zipContent;
		this.__zipNumberOfFilesToUnzip = this.__zipContent.length;
		this.__zipNumberOfFilesUnzipped = 0;
		this.__numOfCheck = 0;
		this.__numMaxOfCheck = 200;
		};
	
	public function unzip(Void):Boolean{
		if(!mdm.FileSystem.fileExists(this.__filePath)){
			return false;
			}
		fscommand("flashvnn.ExtractZip","\"" + this.__filePath + "\",\"" + this.__extractPath + "\",\"" + this.__zipPsw + "\"");
		this.checkIfUnzipped();
		return true;
		};
		
	public function checkIfUnzipped(Void):Void{
		clearInterval(this.__checkUnzippedInterval);
		var bAllExtracted:Boolean = true;
		for(o in this.__zipContent){
			if(!mdm.FileSystem.fileExists(this.__extractPath + this.__zipContent[o][0])){
				this.__checkUnzippedInterval = setInterval(this, 'checkIfUnzipped', 500);
				bAllExtracted = false;
				break;
			}else{
				//check the weigth to see of finished exptracting
				if(mdm.FileSystem.getFileSize(this.__extractPath + this.__zipContent[o][0]) != this.__zipContent[o][1]){
					this.__checkUnzippedInterval = setInterval(this, 'checkIfUnzipped', 500);
					bAllExtracted = false;
					break;
				}else{
					this.__zipNumberOfFilesUnzipped++;
					this.__cbFunction(this.__cbClass, 'progress', (this.__zipNumberOfFilesUnzipped/this.__zipNumberOfFilesToUnzip) * 100);
					this.__zipContent[o] == null;
					delete this.__zipContent[o];
					}
				}
			}
		if(bAllExtracted){
			clearInterval(this.__checkUnzippedInterval);
			this.__cbFunction(this.__cbClass, 'finish', 100);
			}
		};
	
	public function destroy(Void):Void{
		this = null;
		delete this;
		};
	/*	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieZip{
		return this;
		};
	*/	
	}	
