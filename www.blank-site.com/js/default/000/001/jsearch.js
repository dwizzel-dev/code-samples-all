/*

Author: DwiZZel
Date: 00-00-0000
Version: V.1.0 BUILD 001

*/

//----------------------------------------------------------------------------------------------------------------------
    
function JSearch(args){
	//classname
	this.className = 'JSearch';
	//main appz reference soit la classe principale qui soccupe de tout
	this.mainAppz = args.mainappz;
	//le comm avec le serveur	
	this.jcomm = args.jcomm;
	//garde les derniers resultat de recherche
	this.arrLastResult = [];
	this.countLastResult = 0;
	//le pid de la derniere recherche car si clique vite 
	//les resultats n'arrivent pas dans le bon ordre
	this.lastSearchPid = 0;
	this.lastPid = -1;
		
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.fetchAutoCompleteData = function(str, params, strKwType){
		this.debug('fetchAutoCompleteData()', str, params, strKwType);
		//
		var objServer = {
			word: str,
			kwtype: strKwType,
			};
		//
		var objLocal = {
			word: str,
			params : params,
			kwtype: strKwType,	
			};

		//avec la db
		if(this.jcomm != null){	
			this.lastPid = this.jcomm.process(this, 'search', 'fetch-autocomplete', objServer, objLocal);		
		}else{
			//pour la version javascript
			//en javascript DB, pas de delai alors on utilise tout le temps le 0
			//on stop le timer de serach 
			clearTimeout(this.lastPid);
			//get le serach local
			this.lastPid = setTimeout(this.searchLocalDB.bind(this, objServer, objLocal), 1);
			}
		//
		return this.lastPid;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	this.searchLocalDB = function(obj, extraObj){
		this.debug('searchLocalDB()', obj, extraObj);
		//
		var result = { //les keywords
			'1':[
				{id:"1", name: "abduction"}, 
				{id:"2", name: "ankle"}, 
				{id:"3", name: "activation"}, 
				{id:"4", name: "hip abduction"}, 
				{id:"5", name: "arm"}, 
				{id:"6", name: "adduction"}, 
				{id:"7", name: "abdominal"}, 
				],
			'2':[ //les short title
				{id:"10", name: "abduction title"}, 
				{id:"20", name: "ankle title"}, 
				],
			};
		//data
		var data = {
			cword: obj.word,
			reg: '',
			result: result
			};
		//fake comm call
		this.buildFakeCommObject('search', 'fetch-autocomplete', data, extraObj);	
		
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.buildFakeCommObject = function(section, service, data, extraObj){
		this.debug('buildFakeCommObject()', section, service, data, extraObj);
		//on fake lobject jcomm
		var objComm = {
			ip: '0.0.0.0',
			msgerrors: '',
			pid: this.lastPid,
			sessid: '0',
			sql: 0,
			timestamp: 0,
			usage: '0 Mo',
			section: section,
			service: service,	
			data: data
			};
		//on fake le date pour l'instant
		this.commCallBackFunc(this.lastPid, objComm, extraObj);
		}

		
	//----------------------------------------------------------------------------------------------------------------------*
	this.getExerciceListingByWords = function(str){
		this.debug('getExerciceListingByWords()', str);
		
		//rien a chercher on s'en va
		if(str == ''){
			return false;
			}
		//
		var objServer = {
			word: str,
			};
		//
		var objLocal = {
			word: str,
			};
		//
		this.lastPid = this.jcomm.process(this, 'search', 'get-exercice-listing-by-words', objServer, objLocal);		
		//
		this.lastSearchPid = this.lastPid;
		//
		return true;
		};	
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.getExerciceListingByKeywordIds = function(strIds, str){
		this.debug('getExerciceListingByKeywordIds()', strIds, str);
		
		//rien a chercher on s'en va
		if(strIds == ''){
			return false;
			}
		//
		var objServer = {
			ids: strIds,
			word: str,
			};
		//
		var objLocal = {
			ids: strIds,
			word: str,
			};
		//
		this.lastPid = this.jcomm.process(this, 'search', 'get-exercice-listing-by-keyword-ids', objServer, objLocal);		
		//
		this.lastSearchPid = this.lastPid;
		//
		return true;
		};


	//----------------------------------------------------------------------------------------------------------------------*
	this.getExerciceListingByKeywordIdsForPreview = function(params, strIds, strLiId){
		this.debug('getExerciceListingByKeywordIdsForPreview()', params, strIds, strLiId);
		
		//rien a chercher on s'en va
		if(strIds == ''){
			return false;
			}
		//
		var objServer = {
			ids: strIds,
			};
		//
		var objLocal = {
			ids: strIds,
			params: params,
			strliid: strLiId,
			};
		//
		this.lastPid = this.jcomm.process(this, 'search', 'get-exercice-listing-by-keyword-ids-for-preview', objServer, objLocal);		
		//
		return this.lastPid;
		};
		
	//----------------------------------------------------------------------------------------------------------------------*	
	this.commCallBackFunc = function(pid, obj, extraObj){
		this.debug('commCallBackFunc()', pid, obj, extraObj);
		//	
		if(typeof(obj.msgerrors) == 'string' && obj.msgerrors != ''){
			this.debug(obj.msgerrors);
		}else{
			if(obj.section == 'search'){
				if(obj.service == 'fetch-autocomplete'){
					//retour du auto complete
					this.mainAppz.jautocomplete.fetchAutoCompleteDataRFS(obj.data.result, extraObj.params, extraObj.word, extraObj.kwtype, obj.data.cword, pid);
				}else if(obj.service == 'get-exercice-listing-by-keyword-ids'){
					//check si le pid de la recherche est le meme que le dernier pid de la recherche lance, 
					//car une vielle recherche pourrait ecraser une plus recente
					if(pid == this.lastSearchPid){
						//copy et autres
						this.routineOnListingResult(obj.data);
						//show result
						this.mainAppz.fillSearch(obj.data, extraObj.word);
						}
				}else if(obj.service == 'get-exercice-listing-by-words'){
					//check si le pid de la recherche est le meme que le dernier pid de la recherche lance, 
					//car une vielle recherche pourrait ecraser une plus recente
					if(pid == this.lastSearchPid){
						//copy et autres
						this.routineOnListingResult(obj.data);
						//show result
						this.mainAppz.fillSearch(obj.data, extraObj.word);
						}		
				}else if(obj.service == 'get-exercice-listing-by-keyword-ids-for-preview'){
					//retour des previews
					this.mainAppz.jautocomplete.fetchExercisePreviewRFS(obj.data, extraObj.params, extraObj.strliid, pid);
				}else{
					//
					}
				}
			}
		};	

	//----------------------------------------------------------------------------------------------------------------------*
	this.routineOnListingResult = function(data){
		this.debug('routineOnListingResult()');
		//
		this.clear();
		//real data
		this.arrLastResult = data;
		//last count
		this.countLastResult = this.arrLastResult.length;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	this.clear = function(){
		this.debug('clear()');
		//clear array	
		this.arrLastResult = [];
		//clear counter
		this.countLastResult = 0;
		};
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.contains = function(id){
		this.debug('contains()', id);	
		//
		if(typeof(this.arrExercices[o]) == 'object'){
			return true;
			}
		//
		return false;	
		};
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.debug = function(){
		if(arguments.length == 1){	
			jDebug.show(this.className + '::' + arguments[0]);
		}else{
			jDebug.showObject(this.className + '::' + arguments[0], arguments);
			}
		};

	}


//CLASS END


