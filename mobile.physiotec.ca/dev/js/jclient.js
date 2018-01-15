/*

Author: DwiZZel
Date: 25-04-2016
Version: 3.1.0 BUILD X.X
Notes:	JClientManager and JClient

	
*/
//----------------------------------------------------------------------------------------------------------------------

function JClient(id, obj){
	//clas name
	this.className = 'JClient';	
	//main id
	this.id = parseInt(id);
	//the data we will use
	this.data = {
		'firstname': '',
		'lastname': '',
		'locale': gLocaleLang,
		'email': ''
		};	
	//validation
	if(typeof(obj) == 'object'){
		if(typeof(obj.firstname) != 'undefined'){
			obj.firstname = obj.firstname.toLowerCase();
			this.data.firstname = obj.firstname.charAt(0).toUpperCase() + obj.firstname.slice(1);
			}
		if(typeof(obj.lastname) != 'undefined'){
			obj.lastname = obj.lastname.toLowerCase();
			this.data.lastname = obj.lastname.charAt(0).toUpperCase() + obj.lastname.slice(1);
			}
		if(typeof(obj.locale) != 'undefined'){
			this.data.locale = obj.locale;
			}
		if(typeof(obj.email) != 'undefined'){
			this.data.email = obj.email;
			}
		}
	
	//holder des programmes
	this.programs = obj.programs;


	//----------------------------------------------------------------------------------------------------------------------*
	this.replaceProgramByOtherProgram = function(fromId, toId){
		this.debug('replaceProgramByOtherProgram(' + fromId + ', ' + toId + ')');	

		if(typeof(this.programs[fromId]) == 'object' && typeof(this.programs[toId]) == 'object'){	
			this.programs[toId] = this.programs[fromId];
			delete(this.programs[fromId]);
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.isProgramNameAlreadyExistInLocalData = function(str){
		this.debug('isProgramNameAlreadyExistInLocalData(' + str + ')');	
		
		for(var o in this.programs){
			if(this.programs[o]['name'] == str){
				return true;
				}
			}
		return false;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getProgramIdByName = function(str){
		this.debug('getProgramIdByName(' + str + ')');	
	
		for(var o in this.programs){
			//if(this.programs[o]['name'] == str){
			if(String(this.programs[o]['name']).toUpperCase() == String(str).toUpperCase()){
				return o;
				}
			}
		return -1;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getId = function(){
		//this.debug('getId()');	

		return this.id;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getLocale = function(){
		this.debug('getLocale()');
		return this.data.locale;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getFirstName = function(){
		//this.debug('getFirstName()');
		
		return this.data.firstname;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getCompleteName = function(){
		//this.debug('getCompleteName()');
		return this.getFirstName() + ' ' + this.getLastName();
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.getLastName = function(){
		//this.debug('getLastName()');
		return this.data.lastname;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getEmail = function(){
		//this.debug('getEmail()');
		return this.data.email;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.setEmail = function(str){
		this.debug('setEmail(' + str + ')');
		this.data.email = str;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getArrProgramsName = function(){
		this.debug('getArrProgramsName()');
		var arr = [];	
		for(var o in this.programs){
			arr[o] = this.programs[o].name;
			}
		return arr;
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.addNewProgram = function(id, name){
		this.debug('addNewProgram(' + id + ', ' + name +')');
		if(typeof(this.programs) != 'object'){
			this.programs = [];
			}
		this.programs[id] = {
			'name' : name,
			'notes' : '',
			'exercices' : {},
			'order' : '',	
			};
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.modifyClientProgramBasics = function(id, name, notes){
		this.debug('modifyClientProgramBasics(' + id + ', ' + name + ', ' + notes + ')');
		

		if(typeof(this.programs[id]) == 'object'){	
			this.programs[id].name = name;
			this.programs[id].notes = notes;
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.addExerciceToProgram = function(progId, exerciceId, obj){
		this.debug('addExerciceToProgram(' + progId + ', ' + exerciceId + ', ' + obj + ')');
		//minor check
		if(typeof(this.programs[progId]) == 'object'){
			if(typeof(this.programs[progId].exercices) == 'object'){
				this.programs[progId].exercices[exerciceId] = obj;
				}
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.overwriteProgramData = function(progId, exerciceId, objLocale){
		this.debug('overwriteProgramData(' + progId + ', ' + exerciceId + ', ' + objLocale + ')');
		if(typeof(this.programs[progId]) == 'object'){
			if(typeof(this.programs[progId]['exercices']) == 'object'){
				if(typeof(this.programs[progId]['exercices'][exerciceId]) == 'object'){
					this.programs[progId]['exercices'][exerciceId].programdata = JSON.stringify(objLocale);
					}
				}
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.overwriteUserData = function(progId, exerciceId, objLocale){
		this.debug('overwriteUserData(' + progId + ', ' + exerciceId + ', ' + objLocale + ')');
		if(typeof(this.programs[progId]) == 'object'){
			if(typeof(this.programs[progId]['exercices']) == 'object'){
				if(typeof(this.programs[progId]['exercices'][exerciceId]) == 'object'){
					this.programs[progId]['exercices'][exerciceId].userdata = JSON.stringify(objLocale);
					}
				}
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.addSettingsToExercice = function(progId, exerciceId, obj){
		this.debug('addSettingsToExercice(' + progId + ', ' + exerciceId + ', ' + obj + ')');
		//minor check
		if(typeof(this.programs[progId]) == 'object'){
			if(typeof(this.programs[progId]['exercices']) == 'object'){
				if(typeof(this.programs[progId]['exercices'][exerciceId]) == 'object'){
					if(typeof(this.programs[progId]['exercices'][exerciceId]['settings']) == 'object'){
						this.programs[progId]['exercices'][exerciceId]['settings'] = obj; 
						}
					}
				}
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.setFlipToExercice = function(progId, exerciceId, bState){
		this.debug('setFlipToExercice(' + progId + ', ' + exerciceId + ', ' + bState + ')');
		//minor check
		if(typeof(this.programs[progId]) == 'object'){
			if(typeof(this.programs[progId]['exercices']) == 'object'){
				if(typeof(this.programs[progId]['exercices'][exerciceId]) == 'object'){
					this.programs[progId]['exercices'][exerciceId]['flip'] = bState; 
					}
				}
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.setMirrorToExercice = function(progId, exerciceId, bState){
		this.debug('setMirrorToExercice(' + progId + ', ' + exerciceId + ', ' + bState + ')');
		//minor check
		if(typeof(this.programs[progId]) == 'object'){
			if(typeof(this.programs[progId]['exercices']) == 'object'){
				if(typeof(this.programs[progId]['exercices'][exerciceId]) == 'object'){
					this.programs[progId]['exercices'][exerciceId]['mirror'] = bState;  
					}
				}
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.rmExerciceFromProgram = function(progId, exerciceId){
		this.debug('rmExerciceFromProgram(' + progId + ', ' + exerciceId + ')');
		
		//minor check
		if(typeof(this.programs[progId]) == 'object'){
			if(typeof(this.programs[progId].exercices[exerciceId]) == 'object'){
				delete(this.programs[progId].exercices[exerciceId]);
				}
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.rmAllExerciceFromProgram = function(progId){
		this.debug('rmAllExerciceFromProgram(' + progId + ')');
		
		//on supprme les exercice
		if(typeof(this.programs[progId]) == 'object'){
			if(typeof(this.programs[progId].exercices) == 'object'){
				for(var o in this.programs[progId].exercices){
					delete(this.programs[progId].exercices[o]);
					}
				}
			}
		//on suppriome le arrOrder
		this.programs[progId].order = '';	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.changeExerciceOrder = function(progId, arrOrder){
		this.debug('changeExerciceOrder(' + progId + ', ' + arrOrder + ')');
		
		if(typeof(this.programs[progId]) == 'object'){
			this.programs[progId].order = arrOrder.toString();
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.debug = function(str){
		jDebug.show(this.className + '::' + str);
		}

	}
	
	
//----------------------------------------------------------------------------------------------------------------------


function JClientManager(args){

	this.className = 'JClientManager';	

	//main appz soit la classe principale qui soccupe de tout
	this.mainAppz = args.mainappz;
	this.jcomm = args.jcomm;
	
	this.arrClients = []; //arr jclient
	this.lastPid = -1;
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.addClient = function(id, obj){
		this.debug('addClient(' + id + ', ' + obj + ')');
		//check si est deja ouvert dans jprogram pour garder l'infos sinon on rajoute
		if(typeof(this.arrClients[id]) != 'object'){
			this.arrClients[id] = new JClient(id, obj); //obj = jclient
			}
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.modifyClient = function(id, obj){
		this.debug('modifyClient(' + id + ', ' + obj + ')');
		
		this.arrClients[id].data.lastname = obj.lastname;
		this.arrClients[id].data.firstname = obj.firstname;
		//this.arrClients[id].data.age = obj.age;
		this.arrClients[id].data.email = obj.email;
		//this.arrClients[id].data.phone = obj.phone;
		this.arrClients[id].data.locale = obj.locale;
		//les program a retirer
		var arrRmProg = obj.rmprograms.split(',');
		for(var o in arrRmProg){
			delete(this.arrClients[id].programs[arrRmProg[o]]);
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*	
	this.getClientLocale = function(id){
		this.debug('getClientLocale(' + id + ')');
		
		if(typeof(this.arrClients[id]) == 'object'){
			return this.arrClients[id].getLocale();
			}
		//par defautr on ramene la langue de l'application
		return gLocaleLang;
		}	
	
	//----------------------------------------------------------------------------------------------------------------------*	
	this.getClient = function(id){
		this.debug('getClient(' + id + ')');
		
		if(typeof(this.arrClients[id]) == 'object'){
			return this.arrClients[id];
			}
		return false;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getClientPrograms = function(id){
		this.debug('getClientPrograms(' + id + ')');
		
		return this.arrClients[id].programs;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getClientProgramById = function(id, progId){
		this.debug('getClientProgramById(' + id + ', ' + progId + ')');
		
		return this.arrClients[id].programs[progId];
		}	
	
	//----------------------------------------------------------------------------------------------------------------------*	
	this.rmClient = function(id){
		this.debug('rmClient(' + id + ')');
		
		var keepId = this.mainAppz.jprogram.getClientId();
		if(id != keepId){
			delete(this.arrClients[id]);
			}
		}
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.clear = function(){
		this.debug('clear()');
		
		for(var o in this.arrClients){
			this.rmClient(o);
			}
		
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.clearAllClients = function(){
		this.debug('clearAllClients()');
		
		for(var o in this.arrClients){
			delete(this.arrClients[o]);
			}
		this.arrClients = [];
		}
	
	//----------------------------------------------------------------------------------------------------------------------*	
	this.getClientListingFromSearch = function(){
		this.debug('getClientListingFromSearch()');
		
		//data to send
		var objServer = {
			};
		var objLocal = {
			};
		//call the service
		this.lastPid = this.jcomm.process(this, 'client', 'get-client-listing', objServer, objLocal);
		//
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getClientListingFromPopupSearch = function(strPopupFrom, strProgramName){
		this.debug('getClientListingFromPopupSearch(' + strPopupFrom + ', ' + strProgramName + ')');
		
		var objServer = {
			};
		var objLocal = {
			popupfrom : strPopupFrom,
			programname : strProgramName,
			};
		//call the service
		this.lastPid = this.jcomm.process(this, 'client', 'get-client-listing-from-popup', objServer, objLocal);
		//
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.addNewClientToDb = function(obj, bNewProgram, bFromPopup){
		this.debug('addNewClientToDb(' + obj + ', ' + bNewProgram + ', ' + bFromPopup + ')');
		
		//
		this.lastPid = this.jcomm.process(this, 'client', 'add-new-client', obj, {addprogram: bNewProgram, frompopup: bFromPopup});
		}	

	//----------------------------------------------------------------------------------------------------------------------*
	this.modifyClientInfosToDb = function(obj){
		this.debug('modifyClientInfosToDb(' + obj + ')');
		
		//
		this.lastPid = this.jcomm.process(this, 'client', 'modify-client-infos', obj, {clientdata: obj});
		}	

	//----------------------------------------------------------------------------------------------------------------------*
	this.sendProgramEmail = function(id, progId, strEmail, bSaveEmail){
		this.debug('sendProgramEmail(' + id + ', ' + progId + ', ' + strEmail + ', ' + bSaveEmail + ')');
		//
		var objServer = {
			clientid: id,
			programid: progId,
			sendemailto: strEmail,
			saveemail: bSaveEmail,
			};
		var objLocal = {
			clientid: id,
			sendemailto: strEmail,
			saveemail: bSaveEmail,
			};
		//
		this.lastPid = this.jcomm.process(this, 'client', 'send-program-email', objServer, objLocal);
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.fetchClientSearchAutoCompleteData = function(str, params){
		this.debug('fetchClientSearchAutoCompleteData(' + str + ', ' + params + ')');
		//
		var objLocal = {
			word: str,
			params : params,
			};
		//
		this.lastPid = this.jcomm.process(this, 'client', 'fetch-client-search-autocomplete', str, objLocal);		
		//
		return this.lastPid;
		}	

	//----------------------------------------------------------------------------------------------------------------------*
	this.getSingleClientInfosById = function(clientId, params){
		this.debug('getSingleClientInfosById(' + clientId + ', ' + params + ')');
		//
		var objLocal = {
			params: params,
			};
		//
		this.lastPid = this.jcomm.process(this, 'client', 'get-single-client-infos-by-id', clientId, objLocal);		
		//
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getClientListingByWord = function(strClient, params){
		this.debug('getClientListingByWord(' + strClient + ', ' + params + ')');
		//
		var objLocal = {
			params: params,
			};
		//
		this.lastPid = this.jcomm.process(this, 'client', 'get-client-listing-by-word', strClient, objLocal);		
		//
		}

	//----------------------------------------------------------------------------------------------------------------------*	
	this.commCallBackFunc = function(pid, obj, extraObj){
		this.debug('commCallBackFunc(' + pid + ', ' + obj + ', ' + extraObj + ')');
		//
		if(typeof(obj.msgerrors) != 'undefined' && obj.msgerrors != ''){
			this.debug(obj.msgerrors);
			this.mainAppz.resetClientSearchWindow();
			this.mainAppz.openAlert('error', jLang.t('error!'), obj.msgerrors, false);
			//remove le loader si il y a 
			this.mainAppz.removeLoader('#butt-save-popup', jLang.t('save'));
		}else{
			if(obj.section == 'client'){
				if(obj.service == 'get-client-listing'){
					this.mainAppz.fillClientSearch(obj.data);
				}else if(obj.service == 'get-client-listing-from-popup'){
					this.mainAppz.fillPopupClientSearch(obj.data, extraObj.popupfrom, extraObj.programname);
				}else if(obj.service == 'get-single-client-infos-by-id'){
					if(extraObj.params.type == 'client'){
						this.mainAppz.fillClientSearch(obj.data);
					}else if(extraObj.params.type == 'client-popup'){
						this.mainAppz.fillPopupClientSearch(obj.data, extraObj.params.popupfrom, extraObj.params.programname);
						}
				}else if(obj.service == 'get-client-listing-by-word'){
					if(extraObj.params.type == 'client'){
						this.mainAppz.fillClientSearch(obj.data);
					}else if(extraObj.params.type == 'client-popup'){
						this.mainAppz.fillPopupClientSearch(obj.data, extraObj.params.popupfrom, extraObj.params.programname);
						}
				}else if(obj.service == 'add-new-client'){
					this.addNewClientReturnFromServer(obj.data, extraObj);
				}else if(obj.service == 'modify-client-infos'){
					this.modifyClientInfosReturnFromServer(obj.data, extraObj);
				}else if(obj.service == 'send-program-email'){
					this.mainAppz.sendProgramEmailReturnFromServer(obj.data, extraObj);
				}else if(obj.service == 'fetch-client-search-autocomplete'){
					this.mainAppz.jautocomplete.fetchClientSearchAutoCompleteDataReturnFromServer(obj.data, extraObj.params, extraObj.word, pid);
				}else{
					//
					}
				}
			}
		
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.addNewClientReturnFromServer = function(obj, extraObj){
		this.debug('addNewClientReturnFromServer(' + obj + ', ' + extraObj + ')');	
		//
		var bContinue = true;
		//on check si il y a une erreur
		if(typeof(obj.error) != 'undefined'){
			if(obj.error == '1'){
				//on pop le msg d'erreur
				bContinue = false;
				//
				this.mainAppz.openAlert('alert', jLang.t('error!'), obj.errormessage, false);
				}
			}
		//on enleve le loader
		this.mainAppz.removeLoader('#butt-save-popup', jLang.t('save'));
		//
		if(bContinue){
			this.mainAppz.saveNewClient(obj, extraObj.addprogram, extraObj.frompopup); //obj.data = array comme le listing
			}
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.modifyClientInfosReturnFromServer = function(obj, extraObj){
		this.debug('modifyClientInfosReturnFromServer(' + obj + ', ' + extraObj + ')');
		//
		var bContinue = true;
		//on check si il y a une erreur
		if(typeof(obj.error) != 'undefined'){
			if(obj.error == '1'){
				//on pop le msg d'erreur
				bContinue = false;
				//
				this.mainAppz.openAlert('alert', jLang.t('error!'), obj.errormessage, false);
				}
			}

		//on enleve le loader
		this.mainAppz.removeLoader('#butt-save-popup', jLang.t('save'));
		//
		if(bContinue){
			this.mainAppz.saveModifyClient(obj, extraObj.clientdata); //obj.data = clientID
			}
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.debug = function(str){
		jDebug.show(this.className + '::' + str);
		}



	
	}


//CLASS END