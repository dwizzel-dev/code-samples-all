/*

a request object receive an xmlRequest send it and wait for response to redispatch it to his caller

*/

import utils.CieXmlBuilder;

dynamic class comm.CieRequest{

	static private var __className:String = 'CieRequest';
	
	private var __lv:LoadVars; 
	private var __finish:Boolean; 
	private var __cancelled:Boolean;
	private var __arrData:Array;
	private var __strData:String;
	
	private var __iRetry:Number;
	
	private var __intervalSendRequest:Number;
	
	public var __xmlCallBackData:XML;
	public var __id:Number;
	public var __sec:Number;
	
	public var __strGenericError = '<UNIRESPONSE server="127.0.0.1"><C n="error">ERROR_GENERIC</C></UNIRESPONSE>';
	
	public var __bNotifyUserOnHttpError:Boolean;
	
	public function CieRequest(id:Number, arrData:Array){
		this.__intervalSendRequest = 0;
		this.__iRetry = 3;
		this.__id = id;
		this.__finish = false;
		this.__cancelled = false;
		this.__strData = '';
		this.__arrData = arrData;
		this.__bNotifyUserOnHttpError = false;
		this.buildRequest();
		};
		
	public function cancelRequest(Void):Void{
		clearInterval(this.__intervalSendRequest);
		this.__cancelled = true;
		};
		
	private function buildRequest(Void):Void{
		var cXml = new CieXmlBuilder();
		cXml.openNode();
		cXml.addHeader();
        for(var o in this.__arrData){
			cXml.createNode(o, this.__arrData[o]);
            }
        cXml.closeNode();
		this.__strData = cXml.getXml();
		cXml.Destroy();
		delete cXml;
		};
		
		
	public function sendRequestWithDelay(Void):Void{
		this.__intervalSendRequest = setInterval(this, 'sendRequest', 5000);
		};
		
	public function sendRequest(Void):Void{
		if(this.__intervalSendRequest != 0 && this.__intervalSendRequest != undefined){
			clearInterval(this.__intervalSendRequest);
			this.__intervalSendRequest = 0;
			}
		delete this.__lv;
		this.__lv = undefined;
		this.__lv = new LoadVars();
		
		/*
		this.__lv.addRequestHeader('Content-Type', 'text/plain');
		Debug('Content-Type: ' + this.__lv.contentType);
		*/
		var randNumID:Number = ((Math.round(Math.random() * (900)) + 100));
		this.__lv.__super = this;
		this.__lv.__sec = new Date().getTime();
		this.__lv.__cdata = this.__strData;
		if(BC.__user.__debugrequest){
			Debug("REQ_SENDING (RAND_NUM_ID: " + randNumID + ")(" + this.__iRetry + "::" + this.__id + "): " + this.__lv.__cdata);
			}
		//ok this is a F* patch for session ID shit
		//0 =  is the default value in the config file
		
		if(BC.__user.__sessionID == '0'){
			var serverPath = BC.__server.__service + '?&random=' + randNumID;
		}else{
			var serverPath = BC.__server.__service + '?&random=' + randNumID +  '&PHPSESSID=' + BC.__user.__sessionID;
			}
		this.__lv.__randNumID = randNumID;
        this.__lv.sendAndLoad(serverPath, this.__lv, 'POST');
		

		this.__lv.onData = function(str:String){
			//if not cancelled
			if(!this.__super.__cancelled){
				if(str != undefined){ //check if there is something
					if(str.indexOf('<UNIRESPONSE server=') != -1  && str.indexOf('</UNIRESPONSE>') != -1){//check for a response from server
						if(BC.__user.__debugrequest){
							Debug("REQ_OK IN (RAND_NUM_ID: " + this.__randNumID + ")" + ((new Date().getTime() - this.__sec)/1000) + " SEC (" + this.__super.__iRetry + "::" + this.__super.__id + "): " + str);
							}	
						this.__super.__xmlCallBackData.ignoreWhite = true;
						this.__super.__xmlCallBackData = new XML(str);
						this.__super.__finish = true;
					}else{
						//new method throw a general error so won't freeze the client
						Debug("***ERR_REQUEST " + ((new Date().getTime() - this.__sec)/1000) + " SEC (" + this.__super.__iRetry + "::" + this.__super.__id + "): " + str);
						//local error since server send us bad response
						this.__super.__xmlCallBackData.ignoreWhite = true;
						this.__super.__xmlCallBackData = new XML(this.__super.__strGenericError);
						this.__super.__finish = true;
						/*
						//ols method but kind of freeze if problem of compilation in PHP, because valid tags are there but xml is not valid
						this.__super.__xmlCallBackData = undefined;
						cReqManager.removeRequest(this.__super.__id);
						*/
						}
				}else{
					//retry another time
					if(this.__super.__iRetry-- > 0){
						Debug("***REQ_RETRY (RAND_NUM_ID: " + this.__randNumID + ")(" + this.__super.__iRetry + "::" + this.__super.__id + ")");
						if(this.__super.__intervalSendRequest == 0){
							this.__super.sendRequestWithDelay();
							}	
					}else{
						Debug("***REQ_MAX_ATTEMPT_REACH (RAND_NUM_ID: " + this.__randNumID + ")(" + this.__super.__iRetry + "::" + this.__super.__id + ")");
						//this.__super.__xmlCallBackData = undefined;
						if(this.__super.__bNotifyUserOnHttpError && !this.__super.__cancelled){
							cFunc.notifyUserOnHttpRequestError();
							}
						//use method instead of else bellow
						if(!this.__super.__cancelled && !this.__super.__bNotifyUserOnHttpError){
							this.__super.__xmlCallBackData.ignoreWhite = true;
							this.__super.__xmlCallBackData = new XML(this.__super.__strGenericError);
							this.__super.__finish = true;
						}else{
							cReqManager.removeRequest(this.__super.__id);
							}
						}
					}
				}	
			};
			
		this.__lv.onHTTPStatus = function(iHttpState:Number):Void{
			var bFoundError:Boolean = false;
			if(iHttpState < 100) {
		        bFoundError = true;
				Debug("***REQ_HTTP_FLASH_ERROR (RAND_NUM_ID: " + this.__randNumID + ")(" + this.__super.__iRetry + "::" + this.__super.__id + "):" + iHttpState);
		    }else if(iHttpState > 400 && iHttpState < 500) {
		        bFoundError = true;
				Debug("***REQ_HTTP_CLIENT_ERROR (RAND_NUM_ID: " + this.__randNumID + ")(" + this.__super.__iRetry + "::" + this.__super.__id + "):" + iHttpState);
		    }else if(iHttpState > 500 && iHttpState < 600) {
		        bFoundError = true;
				Debug("***REQ_HTTP_SERVER_ERROR (RAND_NUM_ID: " + this.__randNumID + ")(" + this.__super.__iRetry + "::" + this.__super.__id + "):" + iHttpState);
				}
			if(bFoundError){
				//retry another time
				if(this.__super.__iRetry-- > 0){
					Debug("***REQ_HTTP_RETRY (RAND_NUM_ID: " + this.__randNumID + ")(" + this.__super.__iRetry + "::" + this.__super.__id + "):" + iHttpState);
					if(this.__super.__intervalSendRequest == 0){
						this.__super.sendRequestWithDelay();
						}
				}else{
					Debug("***REQ_HTTP_MAX_ATTEMPT_REACH (RAND_NUM_ID: " + this.__randNumID + ")(" + this.__super.__iRetry + "::" + this.__super.__id + "):" + iHttpState);
					//this.__super.__xmlCallBackData = undefined;
					if(this.__super.__bNotifyUserOnHttpError && !this.__super.__cancelled){
						cFunc.notifyUserOnHttpRequestError();
						}
					//use method instead of else bellow
					if(!this.__super.__cancelled && !this.__super.__bNotifyUserOnHttpError){
						this.__super.__xmlCallBackData.ignoreWhite = true;
						this.__super.__xmlCallBackData = new XML(this.__super.__strGenericError);
						this.__super.__finish = true;
					}else{	
						cReqManager.removeRequest(this.__super.__id);
						}
					}
				}
			};
        };
		
	public function addNotificationOnHttpError(Void):Void{
		this.__bNotifyUserOnHttpError = true;
		};
		
	public function getSec(Void):Number{
		return this.__sec;
		};
		
	public function getXml(Void):XML{
		return this.__xmlCallBackData;
		};
		
	public function getID(Void):Number{
		return this.__id;
		};
			
	/*
	public function getClassName(Void):String{
		return __className;
		};
	*/
	/*
	public function getClass(Void):CieRequest{
		return this;
		};
	*/	
	}	
