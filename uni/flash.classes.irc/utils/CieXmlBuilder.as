/*

take some object and converte them into xml + method for sending like xmlHeader, data, request, etc...

*/

dynamic class utils.CieXmlBuilder{

	static private var __className:String = 'CieXmlBuilder';
	
	private var __xmlData:String;
	private var __arrData:Array;
	
	public function CieXmlBuilder(Void){
		this.__xmlData = '';
		};
		
	public function Destroy(Void):Void{
		delete this;
		};
		
	public function openNode(Void):Void{
		this.__xmlData += '<?xml version="1.0" encoding="UTF-8"?>';
        this.__xmlData += '<UNIREQUEST>';
        };
		
	public function addHeader(Void):Void{
		this.__xmlData += '<C n="pseudonyme">' + BC.__user.__pseudo + '</C>';
        this.__xmlData += '<C n="password">' + BC.__user.__psw + '</C>';
		this.__xmlData += '<C n="site">' + escape(BC.__server.__site) + '</C>';
		this.__xmlData += '<C n="encpassword">' + BC.__user.__encpsw + '</C>';
        this.__xmlData += '<C n="nopublique">' + BC.__user.__nopub + '</C>';
        this.__xmlData += '<C n="membership">' + BC.__user.__membership + '</C>';
        this.__xmlData += '<C n="photo">' + BC.__user.__photo + '</C>';
        this.__xmlData += '<C n="session">' + BC.__user.__sessionID + '</C>';
        this.__xmlData += '<C n="language">' + BC.__user.__lang + '</C>';
        this.__xmlData += '<C n="version">' + BC.__user.__version + '</C>';
        this.__xmlData += '<C n="application">' + BC.__user.__application + '</C>';
		};
		
	public function getXml(Void):String{
		return this.__xmlData;
		};
		
	public function closeNode(Void):Void{
		this.__xmlData += '</UNIREQUEST>';
		};	
		
	public function createNode(cname:String, cdata:String):Void{
        this.__xmlData += '<C n="' + cname +'">' + cdata + '</C>';
        };
	/*	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieXmlBuilder{
		return this;
		};
	*/	
	}	
