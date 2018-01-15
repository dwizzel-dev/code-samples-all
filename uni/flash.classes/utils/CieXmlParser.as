/*

parse xml file into an flash pbject passsed by raf when construct
kind of callBack Object when the file is parsed
have to implement a watch method in the caller class

*/

dynamic class utils.CieXmlParser{

	static private var __className:String = 'CieXmlParser';
	private var __xmlFile:String;
	private var __cbObject:Object;
	private var __cbFunction:Function;
	private var __cbClass:Object;
		
	public function CieXmlParser(xmlFile:String, callBackObject:Object, callBackFunction:Function, callBackClass:Object){
		this.__cbFunction = callBackFunction;
		this.__cbClass = callBackClass;
		this.__cbObject = callBackObject;
		this.__xmlFile = xmlFile;
		this.createFromXmlFile();
		};
		
	public function createFromXmlFile(Void):Void{
		var xmlFile:XML = new XML();
		xmlFile.watch('loaded', this.xmlLoaded, {__super:this});
		xmlFile.ignoreWhite = true;
		xmlFile.load(this.__xmlFile);
		};
		
	public function xmlLoaded(prop, oldVal:Number, newVal:Number, obj:Object){
		if(newVal){
			obj.__super.createObjectFromXml(this.firstChild);
			}
		return newVal;
		};
		
	private function createObjectFromXml(xmlNode:XMLNode):Void{	
		this.createObject(this.__cbObject, xmlNode, '');
		this.__cbFunction(this.__cbClass);
		};

	private function createObject(xmlObj:Object, xmlNode:XMLNode, spacer:String):Void{	
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			var objName:String = currNode.attributes.n;
			var objType:String = currNode.attributes.type;
			if(currNode.firstChild.nodeType != 3){
				xmlObj[objName] = new Object();
				this.createObject(xmlObj[objName], currNode, spacer + '\t');
			}else{
				if(objType == 'Number'){
					xmlObj[objName] = Number(currNode.firstChild.nodeValue);
				}else if(objType == 'String'){
					xmlObj[objName] = String(currNode.firstChild.nodeValue);	
				}else if(objType == 'Boolean'){
					if(currNode.firstChild.nodeValue == 'true'){
						xmlObj[objName] = true;
					}else{
						xmlObj[objName] = false;
						}
				}else if(objType == 'Array'){
					xmlObj[objName] = new Array();
				}else if(objType == 'Object'){
					xmlObj[objName] = new Object();
				}else{
					xmlObj[objName] = currNode.firstChild.nodeValue;
					}
				}
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
	
	public function getClass(Void):CieXmlParser{
		return this;
		};
	*/	
	}