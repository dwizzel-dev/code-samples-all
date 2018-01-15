/*

check des updates package sur le site

FORMATING EXAMPLE:

<UNIUPDATE>
	<C n="version">1.000</C>
	<C n="application">UNI.V2</C>
	<C n="type">critical</C>
	<C n="filename">updates.1.17f.zip</C>
	<R n="content">
		<C n="archives_lang.DE_DE.xml">167</C>
		<C n="archives_lang.FR_CA.xml">170</C>
		<C n="archives_lang.US_EN.xml">171</C>
	</R>
</UNIUPDATE>

*/

import comm.CieHttp;
import utils.CieZip;
import utils.CieXmlParser;
import system.CieRegistry;

dynamic class comm.CieUpdate{

	static private var __className:String = 'CieUpdate';
	
	private var __serverAddr:String;
	private var __serverFile:String;
	private var __savePath:String;
	private var __saveFileName:String;
	private var __currVersion:Number;
	
	public var __objUpdate:Object;
	public var __xmlParser:CieXmlParser;
	public var __http:CieHttp;
	public var __zip:CieZip;
	
	private var __pDone:Object;
	private var __zDone:Object;
	
	private var __cbClass:Object;
	private var __cbFunction:Function;
	
	public function CieUpdate(addr:String, fileName:String, savePath:String, saveFileName:String, currVersion:String, cbFunction:Function, cbClass:Object){
		
		this.__pDone = new Object();
		this.__pDone._20 = false;
		this.__pDone._40 = false;
		this.__pDone._60 = false;
		this.__pDone._80 = false;
		this.__pDone._100 = false;
		
		this.__zDone = new Object();
		this.__zDone._20 = false;
		this.__zDone._40 = false;
		this.__zDone._60 = false;
		this.__zDone._80 = false;
		this.__zDone._100 = false;
		
		this.__cbClass = cbClass;
		this.__cbFunction = cbFunction;
		
		this.__currVersion = Number(currVersion);
		this.__savePath = savePath;
		this.__serverAddr = addr;
		this.__serverFile = fileName;
		this.__saveFileName = saveFileName;
		this.__objUpdate = new Object();
		this.__xmlParser = new CieXmlParser(this.__serverAddr + this.__serverFile + '?&random=' + ((Math.round(Math.random() * (900)) + 100)), this.__objUpdate, this.doneParsing, this);
		};
	
	public function doneParsing(cClass:CieUpdate):Void{
		//Debug("DONE PARSING");
		cClass.__xmlParser.destroy();
		delete cClass.__xmlParser;	
		cClass.checkIfNeedUpdates();
		};
		
	private function checkIfNeedUpdates(Void):Void{
		//insted of 0 need the real version from the registry that will be global
		Debug('NEW  VERSION: ' + this.__objUpdate.version);
		Debug('CURRENT VERSION: ' + this.__currVersion);
		if(Number(this.__objUpdate.version) > this.__currVersion){ 
			this.__cbFunction(this.__cbClass, this.__objUpdate.msg);
		}else{
			Debug("NO UPDATES AVAILABLE");
			}
		};
		
	public function loadUpdates(Void):Void{
		//Debug("FETCHING UPDATES");
		this.__http = new CieHttp(this.doneLoading, this);
		//Debug("BUILDING HTTP REQUEST OBJECT");
		this.__http.getFile(this.__serverAddr + this.__objUpdate.filename, this.__savePath + this.__saveFileName);
		};
		
	public function doneLoading(cClass:CieUpdate, strState:String, percentDone:Number):Void{
		//clear th http obj
		if(strState == 'finish'){
			cClass.__http.destroy();
			delete cClass.__http;
			Debug('FINISH DOWNLOADING UPDATES');
			cClass.unzipUpdates();
		}else if(strState == 'progress'){
			var speek:Boolean = false;
			if(percentDone >= 100 && !cClass.__pDone._100){
				cClass.__pDone._100 = true;
				speek = true;
			}else if(percentDone > 80 && !cClass.__pDone._80){
				cClass.__pDone._80 = true;
				speek = true;
			}else if(percentDone > 60 && !cClass.__pDone._60){
				cClass.__pDone._60 = true;
				speek = true;
			}else if(percentDone > 40 && !cClass.__pDone._40){
				cClass.__pDone._40 = true;
				speek = true;
			}else if(percentDone > 20 && !cClass.__pDone._20){
				cClass.__pDone._20 = true;
				speek = true;
				}
			if(speek){
				Debug(Math.round(percentDone) + '% DOWNLOADED');
				}
		}else if(strState == 'error'){
			Debug('ERROR DOWNLOADING UPDATES');
			}
		};	
		
	public function unzipUpdates(Void):Void{
		//convert content to array for CieZip class
		//Debug("CONVERTING OBJECT TO ARRAY");
		var arrContent = new Array();
		for(var o in this.__objUpdate.content){
			arrContent.push([o, this.__objUpdate.content[o]]);
			}
		//decompress
		//Debug("INSTANCIATE ZIP CLASS");
		this.__zip = new CieZip(this.__savePath + this.__saveFileName, this.__savePath, "", arrContent, this.doneUnzipping, this);	
		Debug("START UNZIPPING");
		this.__zip.unzip();
		};
		
	public function doneUnzipping(cClass:CieUpdate, strState:String, percentDone:Number):Void{
		//clear th http obj
		if(strState == 'finish'){
			Debug('FINISH UNZIPPING');
			Debug('UPDATES HAVE BEEN INSTALLED');
			//set the new value to the registry
			cRegistry.setKey('version', cClass.__objUpdate.version);
			cFunc.updatesAreInstalled();
		}else if(strState == 'progress'){
			var speek:Boolean = false;
			if(percentDone >= 100 && !cClass.__zDone._100){
				cClass.__zDone._100 = true;
				speek = true;
			}else if(percentDone > 80 && !cClass.__zDone._80){
				cClass.__zDone._80 = true;
				speek = true;
			}else if(percentDone > 60 && !cClass.__zDone._60){
				cClass.__zDone._60 = true;
				speek = true;
			}else if(percentDone > 40 && !cClass.__zDone._40){
				cClass.__zDone._40 = true;
				speek = true;
			}else if(percentDone > 20 && !cClass.__zDone._20){
				cClass.__zDone._20 = true;
				speek = true;
				}
			if(speek){
				Debug(Math.round(percentDone) + '% UNZIPPED');
				}
		}else if(strState == 'error'){
			Debug('ERROR UNZIPPING FILES');
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
	*/
	/*
	public function getClass(Void):CieUpdate{
		return this;
		};
	*/	
	}	
