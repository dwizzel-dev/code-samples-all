/*

Author: DwiZZel
Date: 00-00-0000
Version: V.1.0 BUILD 001
		
*/

//----------------------------------------------------------------------------------------------------------------------
    
function JServer(args){

	//class name
	this.className = 'JServer';
	this.db = false;
	this.urlDB = args.path + 'db-kw.' + args.lang + '.data';

	//---------------------------------------------------------------------
	this.process = function(obj){
		this.debug('process()', obj);
		//on check si on a une DB
		if(this.db === false){
			//call the caller
			obj.callerclass.commCallBackFunc(obj.pid, {msgerrors:'Local DB "' + this.urlDB + '" not available'}, obj.extraobj);
			//get out
			return false;
			}		
		switch(obj.section){
			case 'search':
				this.processSearch(obj);
				break;
			default: 
				//default error
				obj.callerclass.commCallBackFunc(obj.pid, {msgerrors:'Section not available'}, obj.extraobj);	
				break;
			}
		//
		};

	//---------------------------------------------------------------------
	this.processSearch = function(obj){
		this.debug('processSearch()', obj);
		//
		switch(obj.service){
			case 'fetch-autocomplete':
				this.fetchAutocomplete(obj);
				break;
			default: 
				//default error
				obj.callerclass.commCallBackFunc(obj.pid, {msgerrors:'Service not available'}, obj.extraobj);	
				break;
			}
		};

	//---------------------------------------------------------------------
	this.fetchAutocomplete = function(obj){
		this.debug('fetchAutocomplete()', obj);
		var arrResult = [];
		var word = '';
		if(typeof(obj.data.word) == 'string'){
			if(obj.data.word != ''){
				//on strip tout les caractere qui ppeuvent crasher le regex
				word = this.trimKeyword(obj.data.word);
				}
			}
		if(word != ''){
			this.debug('WORD: ' + word);
			//on utilise les mot avec un espace et on fait la combinaison de tous
			var strMatch = '\\|([a-z0-9\\s]{0,}[\\s]{1}' + word + '[a-z0-9\\s]{0,})\\||\\|(' + word + '[a-z0-9\\s]{0,})\\|';
			var arr = this.db.match(new RegExp(strMatch, 'gi'));
			if(typeof(arr) == 'object' && arr != null){
				for(var i=0;i<arr.length;i++){
					if(i>10){
						break;
						}
					var str = arr[i].substring(1,(arr[i].length - 1));
					arrResult.push({
						id:i,
						name:str
						});
					}
				}
			}
		//sinon on conitnue
		var oRtn = {
			section: obj.section,
			service: obj.service,
			data:{	
				cword: obj.data.word,
				result: { //les keywords
					'1': arrResult
					}
				}
			};
		//call the caller
		obj.callerclass.commCallBackFunc(obj.pid, oRtn, obj.extraobj);
		};

	//---------------------------------------------------------------------
	this.trimKeyword = function(str){
		this.debug('trimKeyword()', str);
		//
		str = str.toLowerCase();
		str = str.replace(/[^a-zA-Z0-9\sÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ+-]/gi, ' ');
		str = str.replace(/[\s]+/gi, ' ');	
		str = str.trim();	
		
		return str;
		}

	//---------------------------------------------------------------------
	this.prepareKeyword = function(str){
		this.debug('prepareKeyword()', str);
		//	
		return str;
		}
	
	//---------------------------------------------------------------------
	this.setDB = function(obj){
		this.debug('setDB()', obj);
		//
		this.db = obj;	
		};

	//---------------------------------------------------------------------
	//load the db lang file
	this.getDB = function(){
		this.debug('getDB()');
		//on send
		$.ajax({
			timestamp: Date.now(),
			parentclass: this,
			type: 'POST',
			headers:{'cache-control':'no-cache'},
			cache: false,
			async: true,
			dataType: 'text',
			url: this.urlDB,
			success: function(dataRtn){
				//parse data
				this.parentclass.debug('process().success()', {
					'dataRtn': dataRtn,
					'time': ((Date.now() - this.timestamp)/1000) + 'seconds', 
					'weight': ((dataRtn.length/1024)/1000) + ' Mo'
					});
				//
				var obj = false;
				try{
					eval('obj = "' + dataRtn + '";');
				}catch(e){
					obj = false;	
					}
				//
				this.parentclass.debug(this.url + ' loaded');
				this.parentclass.setDB(obj);
				},
			error: function(dataRtn, ajaxOptions, thrownError){
				//
				this.parentclass.debug(this.url + ' NOT loaded');
				}	
			});	

		};

	//---------------------------------------------------------------------
	this.debug = function(){
		if(arguments.length == 1){	
			jDebug.show(this.className + '::' + arguments[0]);
		}else{
			jDebug.showObject(this.className + '::' + arguments[0], arguments);
			}
		};

	//---------------------------------------------------------------------
	//CLASS DEBUG
	this.debug('JServer()', args);
	//on va chercher la database	
	this.getDB();

	}