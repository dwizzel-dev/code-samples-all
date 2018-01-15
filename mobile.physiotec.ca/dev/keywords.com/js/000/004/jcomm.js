/*

Author: DwiZZel
Date: 04-09-2015
Version: 3.1.0 BUILD X.X

*/

//----------------------------------------------------------------------------------------------------------------------

function JComm(args){
	//class name
	this.className = 'JComm';
	//parent class
	this.mainAppz = args.mainappz;	
	//satrt pid
	this.pid = 100;
	//----------------------------------------------------------------------------------------------------------------------*
	this.getTicket = function(){
		this.debug('getTicket()');
		this.pid++;
		return this.pid;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	this.buildExtraParams = function(){
		var str = '';
		//on rajoute le branding
		if(typeof(gBrand) == 'string'){
			if(gBrand != ''){
				str += '&brand=' + gBrand;
				}
			}
		//on rajoute le branding
		if(typeof(gVersioning) == 'string'){
			if(gVersioning != ''){
				str += '&versioning=' + gVersioning;
				}
			}
		return str;
		};
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.process = function(callerClass, section, service, data, extraObj){
		this.debug('process()', callerClass, section, service, data, extraObj);
		//pid
		var timestamp = Date.now();
		var pid = this.getTicket();
		//en locale uniquement on va le triater avec un serveur locale a la place
		if(typeof(JServer) != 'undefined'){
			return pid;
			}
		//	
		var strUrl = gServerPath + 'service.php?';
		//pour le file debug du cote php et laoder autre fichier que le standard
		strUrl += this.buildExtraParams();
		//seulement si un sessid valide sinon affiche aucun
		if(gSessionId.length >= 26){	
			strUrl += '&PHPSESSID=' + gSessionId;
			}
		//timestamp for cache
		strUrl += '&time=' + timestamp;
		//lang
		strUrl += '&lang=' + gLocaleLang;
		//on send
		$.ajax({
			parentclass: this,
			timestamp: timestamp,
			pid: pid,
			extraobj: extraObj,
			callerclass: callerClass,
			type: 'POST',
			headers:{'cache-control':'no-cache'},
			cache: false,
			async: true,
			dataType: 'text',
			url: strUrl,
			service: service,
			section: section,
			data: {
				section:section, 
				service:service, 
				data:JSON.stringify(data), 
				pid:pid
				},
			success: function(dataRtn){
				//parse data
				if(gDebug != '0'){
					//debug
					this.parentclass.debug('process().success(' + this.pid + ')', {
						'dataRtn': dataRtn,
						'time': ((Date.now() - this.timestamp)/1000) + 'seconds', 
						'weight': ((dataRtn.length/1024)/1000) + ' Mo'
						});
				}else{
					//debug
					this.parentclass.debug('process().success(' + this.pid + ')', {
						'time': ((Date.now() - this.timestamp)/1000) + 'seconds', 
						'weight': ((dataRtn.length/1024)/1000) + ' Mo'
						});
					}
				
				//try catch on it because of php errors , notice, warnings or scrumbled data
				var error = '';
				var obj;
				try{
					eval('var obj = ' + dataRtn + ';');
				}catch(e){
					error = e;
					}
				//check if the object was made ok format
				if(typeof(obj) != 'object'){
					//set state
					obj = {
						msgerrors: '<b>' + jLang.t('server error on service call:') + '</b><br /><br />' + this.section + '.' + this.service + '<br /><br /><b>' + jLang.t('service error:') + '</b><br /><br />' + error,
						};
					}
				//debug
				this.parentclass.debug('process().return(' + this.pid + '):', obj, this.extraobj);
				//call the caller
				this.callerclass.commCallBackFunc(this.pid, obj, this.extraobj);
				//
				},
			error: function(dataRtn, ajaxOptions, thrownError){
				this.parentclass.debug('process().error(' + this.pid + ')', this.data, dataRtn, ajaxOptions, thrownError);
				//set state
				obj = {
					msgerrors: '<b>' + jLang.t('server error on service call:') + '</b><br /><br />' + this.parentclass.formatErrorMessage(dataRtn, thrownError, this.timestamp),
					};
				//call the caller
				this.callerclass.commCallBackFunc(this.pid, obj, this.extraobj);
				//
				}	
			});
		//retun the ticket number	
		return pid;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	this.formatErrorMessage = function(xhr, exception, timestamp){
		this.debug('formatErrorMessage()', xhr, exception, timestamp);
		//
		var str = '';
		//
		if(xhr.status === 0) {
			str = jLang.t('Not connected.\nPlease verify your network connection.');
		}else if(xhr.status == 404) {
			str = jLang.t('The requested page not found. [404]');
		}else if(xhr.status == 500) {
			str = jLang.t('Internal Server Error [500].');
		}else if(exception === 'parsererror') {
			str = jLang.t('Requested JSON parse failed.');
		}else if(exception === 'timeout') {
			str = jLang.t('Time out error.');
		}else if(exception === 'abort') {
			str = jLang.t('Ajax request aborted.');
		}else{
			str = jLang.t('Uncaught Error' + xhr.responseText);
			}
		return '[' + timestamp + '] ' + str;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	this.debug = function(){
		if(arguments.length == 1){	
			jDebug.show(this.className + '::' + arguments[0]);
		}else{
			jDebug.showObject(this.className + '::' + arguments[0], arguments);
			}
		};

	//CLASS DEBUG
	this.debug('JComm()', args);
	

	}	

//CLASS END