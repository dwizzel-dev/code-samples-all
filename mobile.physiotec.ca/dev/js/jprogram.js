/*

Author: DwiZZel
Date: 04-09-2015
Version: 3.1.0 BUILD X.X
Notes:	
		
*/


//----------------------------------------------------------------------------------------------------------------------
    
function JProgram(args){
	
	this.className = 'JProgram';

	//main appz soit la classe principale qui soccupe de tout
	this.mainAppz = args.mainappz;
	this.jcomm = args.jcomm;
	this.bAutoSave = true;	

	this.arrExercices = []; //arr jexrecice
	this.count = 0;
		
	this.bSaved = true;
	this.jclient;
	this.name = '';
	this.progId = -1;
	this.notes = '';
	
	this.arrOrder = [];
	
	this.lastPid = -1;

	//setTimeOUt thread
	this.timer;
	
	this.startAutomaticProgramSave = function(){
		this.debug('startAutomaticProgramSave()');
		//set the new timer
		if(this.bAutoSave){
			this.timer = setTimeout(this.saveProgramsModifications.bind(this), 10000);
			}
		}
		
	this.cancelAutomaticProgramSave = function(){
		this.debug('cancelAutomaticProgramSave()');
		clearTimeout(this.timer);
		}
	
	this.setClient = function(jclient){
		this.debug('setClient(' + jclient + ')');
		
		this.jclient = jclient;

		//quand on set un client il a peut-etre une autre langue que la langue de l'application gLocaleLang
		//alors on doit rafraichir la section layer programs pour qu'il change la langue des textes
		this.mainAppz.displayProgramBoxesHasContentView();
		/*
		if(this.jclient.getLocale() != gLocaleLang){
			this.mainAppz.displayProgramBoxesHasContentView();
			}
		*/
		
		}

	this.getClient = function(){
		this.debug('getClient()');
		
		if(typeof(this.jclient) == 'object'){
			return this.jclient;
			}
		return false;
		}
		
	this.getClientId = function(){
		this.debug('getClientId()');
		
		if(typeof(this.jclient) == 'object'){
			return this.jclient.getId();
			}
		return -1;	
		}	
		
	this.setCount = function(num){
		this.debug('setCount(' + num + ')');
		this.count = num;
		}	
	
	this.setName = function(name){
		this.debug('setName(' + name + ')');
		this.name = name;
		}

	this.getName = function(){
		this.debug('getName()');
		return this.name;
		}

	this.setProgId = function(id){
		this.debug('setProgId(' + id + ')');
		this.progId = parseInt(id);
		}

	this.getProgId = function(){
		this.debug('getProgId()');
		
		return this.progId;
		}			
	
	this.setNotes = function(notes){
		this.debug('setNotes(' + notes + ')');
		this.notes = notes;
		}

	this.getNotes = function(){
		this.debug('getNotes()');
		return this.notes;
		}	
	
	this.setSaved = function(bState){
		this.debug('setSaved(' + bState + ')');
		this.bSaved = bState;
		}	

	this.getSaved = function(){
		this.debug('getSaved()');
		
		return this.bSaved;
		}
		
	this.addExercice = function(exerciceId, oExercice){
		this.debug('addExercice(' + exerciceId + ', ' + oExercice + ')');
		
		//base on exercice ID
		this.arrExercices[exerciceId] = oExercice; //E- because of the way it treats array length
		//we copy the title and description from my instruction OR original instruction
		this.arrExercices[exerciceId].copyInstructionToProgramData();
		//pour le'ordre des exercices	
		this.arrOrder.push(exerciceId);
		//si on a un jclient et un progID alors on rajoute aussi a ses infos
		if(typeof(this.jclient) == 'object' && this.progId != -1){
			this.jclient.addExerciceToProgram(this.progId, this.arrExercices[exerciceId].getId(), this.arrExercices[exerciceId].getObj());
			//le order	
			this.jclient.changeExerciceOrder(this.progId, this.arrOrder);
			}
		}

	this.overwriteProgramData = function(exerciceId, objLocaleData){
		this.debug('overwriteProgramData(' + exerciceId + ', ' + objLocaleData + ')');
		if(typeof(this.arrExercices[exerciceId]) == 'object'){
			this.arrExercices[exerciceId].overwriteProgramData(objLocaleData);
			//si un jclient alors on overwrite son programdata aussi
			if(typeof(this.jclient) == 'object' && this.progId != -1){
				this.jclient.overwriteProgramData(this.progId, this.arrExercices[exerciceId].getId(), objLocaleData);
				}
			}
		};

	this.overwriteUserData = function(exerciceId, objLocaleData){
		this.debug('overwriteUserData(' + exerciceId + ', ' + objLocaleData + ')');
		if(typeof(this.arrExercices[exerciceId]) == 'object'){
			this.arrExercices[exerciceId].overwriteUserData(objLocaleData);
			//si un jclient alors on overwrite son userdata aussi
			if(typeof(this.jclient) == 'object' && this.progId != -1){
				this.jclient.overwriteUserData(this.progId, this.arrExercices[exerciceId].getId(), objLocaleData);
				}
			}
		
		};
		
	this.clear = function(){
		this.debug('clear()');
		
		//destroy all event manager
		for(var o in this.arrExercices){
			this.arrExercices[o].destroyEventManager();
			}
		//clear array	
		this.arrExercices = [];
		this.arrOrder = [];
		}
		
	this.clearClient = function(){
		this.debug('clearClient()');
		
		this.jclient = false;
		}

	this.clearProgram = function(){
		this.debug('clearProgram()');
		
		this.name = '';
		this.notes = '';
		this.progId = -1;
		}	
	
	this.rmExercice = function(id){
		this.debug('rmExercice(' + id + ')');
		
		if(typeof(this.arrExercices[id]) == 'object'){
			//et ici aussi
			this.arrExercices[id].destroyEventManager();
			delete(this.arrExercices[id]);
			//order
			var cmpt = 0;
			for(var o in this.arrOrder){
				if(this.arrOrder[o] == id){
					//delete(this.arrOrder[o]);
					this.arrOrder.splice(cmpt, 1);
					break;
					}
				cmpt++;	
				}
			//on l'enleve du client aussi
			if(typeof(this.jclient) == 'object' && this.progId != -1){
				this.jclient.rmExerciceFromProgram(this.progId, id);
				//change le order	
				this.jclient.changeExerciceOrder(this.progId, this.arrOrder);
				}
			}
		}
		
	this.changeOrder = function(dragIndex, dropIndex){
		this.debug('changeOrder(' + dragIndex + ', ' + dropIndex + ')');
		
		//
		var tmpId = this.arrOrder[dragIndex];
		if(dragIndex < dropIndex){			
			for(var i = dragIndex; i < dropIndex; i++){
				this.arrOrder[i] = this.arrOrder[i + 1]; 
				}
		}else{
			for(var i = dragIndex; i > dropIndex; i--){
				this.arrOrder[i] = this.arrOrder[i - 1]; 
				}
			}
		this.arrOrder[dropIndex] = tmpId;	

		//si un jclient alors on doit aussi changer l'ordre des exercices
		if(typeof(this.jclient) == 'object' && this.progId != -1){
				this.jclient.changeExerciceOrder(this.progId, this.arrOrder);
				}
		
		}
		
	this.contains = function(id){
		//this.debug('contains(' + id + ')');
		
		for(var o in this.arrExercices){
			if(this.arrExercices[o].getId() == id){
				return true;
				}
			}
		return false;	
		}
		
	this.addEventManager = function(id, evtMng){
		this.debug('addEventManager(' + id + ', ' + evtMng + ')');
		
		this.arrExercices[id].addEventManager(evtMng);
		}

	this.getExerciceById = function(id){
		this.debug('getExerciceById(' + id + ')');
		
		if(typeof(this.arrExercices[id]) == 'object'){
			return this.arrExercices[id];
			}
		return false;	
		}

	this.getExercices = function(){
		this.debug('getExercices()');
		
		if(typeof(this.arrExercices) == 'object'){
			return this.arrExercices;
			}
		return false;	
		}
		
	this.getExerciceByOrder = function(){
		this.debug('getExerciceByOrder()');
		
		return this.arrOrder;
		}

	this.getExerciceByArrayForTransport = function(){
		this.debug('getExerciceByArrayForTransport()');
		
		var arr = [];
		for(var o in this.arrExercices){
			var oTmp = {
				id: this.arrExercices[o].getId(),
				//description: this.arrExercices[o].getDescription(),
				//name: this.arrExercices[o].getTitle(true),
				settings: this.arrExercices[o].getSettings(), 
				//settings_lang: this.arrExercices[o].getSettingsLang(), 
				flip: this.arrExercices[o].getFlip(), 
				mirror: this.arrExercices[o].getMirror(), 
				programdata: this.arrExercices[o].getProgramData(), 
				code: this.arrExercices[o].getCode(),
				};
			//push
			arr.push(oTmp);
			}
		
		return arr;
		}

	this.setExerciceSettingsById = function(id, obj){
		this.debug('setExerciceSettingsById(' + id + ', ' + obj + ')');
		
		if(typeof(this.arrExercices[id]) == 'object'){
			this.arrExercices[id].setSettings(obj);
			//si on a un jclient et un progID alors on rajoute aussi a ses infos
			if(typeof(this.jclient) == 'object' && this.progId != -1){
				this.jclient.addSettingsToExercice(this.progId, this.arrExercices[id].getId(), this.arrExercices[id].getSettings());
				}
			}
		}
		
	this.setExerciceNameById = function(id, str){
		this.debug('setExerciceNameById(' + id + ', ' + str + ')');
		
		if(typeof(this.arrExercices[id]) == 'object'){	
			this.arrExercices[id].setTitle(str);
			}
		}

	this.setExerciceFlipById = function(id, bState){
		this.debug('setExerciceFlipById(' + id + ', ' + bState + ')');
		
		if(typeof(this.arrExercices[id]) == 'object'){	
			this.arrExercices[id].setFlip(bState);
			//si on a un jclient et un progID alors on rajoute aussi a ses infos
			if(typeof(this.jclient) == 'object' && this.progId != -1){
				this.jclient.setFlipToExercice(this.progId, this.arrExercices[id].getId(), bState);
				}
			}
		}

	this.setExerciceMirrorById = function(id, bState){
		this.debug('setExerciceMirrorById(' + id + ', ' + bState + ')');
		
		if(typeof(this.arrExercices[id]) == 'object'){	
			this.arrExercices[id].setMirror(bState);
			//si on a un jclient et un progID alors on rajoute aussi a ses infos
			if(typeof(this.jclient) == 'object' && this.progId != -1){
				this.jclient.setMirrorToExercice(this.progId, this.arrExercices[id].getId(), bState);
				}
			}
		}
	
		
	this.saveProgramsModifications = function(){
		//this.debug('saveProgramsModifications()');
		
		//cancel le timeout call it back when data returned
		this.cancelAutomaticProgramSave();
		//juste si il y a des changements
		//if((!this.bSaved) && (this.getClientId() != -1)){ 
		if((!this.bSaved) && (this.getClientId() != -1) && (this.progId != -1)){ 
			//build the object to send
			var objServer = {
				clientid: this.getClientId(),
				order: this.arrOrder.toString(),
				name: this.name,
				notes: this.notes,
				id: this.progId,
				exercices: this.getExerciceByArrayForTransport(),
				};
			var objLocal = {
				};

			//change le save state
			this.mainAppz.changeProgramSaveState(false);	
			//this.mainAppz.showLoader(false, '#butt-save', 12, 15);		
			this.mainAppz.showLoader(false, '#butt-save', 12, 15);		
			//call servive
			this.lastPid = this.jcomm.process(this, 'programs', 'save-program-modification', objServer, objLocal);
			//pour chaque modif on modife aussi le jclientmanager->jclient
			//pour avoir le meme data dans la recherche et l'affichage des details client avec le listing des programmes
			//addJProgramToClient
			
		}else{
			this.startAutomaticProgramSave();
			}
		}
		

		
	this.createNewProgramToDb = function(oClient, strName, extraParams){
		this.debug('createNewProgramToDb(' + oClient + ', ' + strName + ', ' + extraParams + ')');
		
		//call servive
		var objServer = {
			clientid : oClient.getId(),
			programname : strName,
			programid : oClient.getProgramIdByName(strName),
			}
		var objLocal = {
			oclient : oClient,
			programname : strName,
			extraparams : extraParams,
			}
		this.lastPid = this.jcomm.process(this, 'programs', 'create-new-program', objServer, objLocal);	
		}

	this.modifyProgramBasicsToDb = function(strName, strNotes, extraObj){
		this.debug('modifyProgramBasicsToDb(' + strName + ', ' + strNotes + ', ' + extraObj + ')');
		this.debug('modifyProgramBasicsToDb', extraObj);
		//si pas un oversrie alors on prend le programId de jprogram
		// si overwrite alors on prend le programId que l'on trouve dans les programmes du client soit dans jclient
		var progId = this.getProgId();
		var progIdFrom = this.getProgId();

		if(typeof(extraObj.overwrite) != 'undefined'){
			if(extraObj.overwrite){
				//on va chercher le id du programme que l'on veut overwrite par nom
				progId = this.jclient.getProgramIdByName(strName);
				}
			}
		//si c'est un changement de client	on met le prog a zero car n'esxitse pas encore
		if(typeof(extraObj.changeclient) != 'undefined' && typeof(extraObj.overwrite) != 'undefined'){
			if(extraObj.changeclient && extraObj.overwrite){
				//on va chercher le id du programme que l'on veut overwrite par nom
				progIdFrom = -1;
				progId = this.jclient.getProgramIdByName(strName);
				}
			}
			
		//IMPORTANT: 
		//si on a un programid == -1 et programidfrom == -1, 
		//alors on ne paut pas modifier vu que lon a pas de id encore
		if(progIdFrom == -1 && progId == -1){
			//get the client object
			this.createNewProgramToDb(this.jclient, strName, {keepprogramexercise: true});
		}else{
			//call servive
			var obj = {
				clientid : this.getClientId(),
				programidfrom : progIdFrom,
				programid : progId,
				programname : strName,
				programnotes : strNotes,
				};
			//
			this.lastPid = this.jcomm.process(this, 'programs', 'modify-program-name', obj, obj);	
			}
		}

	this.isNewProgramNameAlreadyExistInServerData = function(strName, oClient){
		this.debug('isNewProgramNameAlreadyExistInServerData(' + strName + ', ' + oClient + ')');
		
		var objServer = {
			clientid : oClient.getId(),
			programname : strName,
			}
		var objLocal = {
			oclient : oClient,
			programname : strName,
			}
		this.lastPid = this.jcomm.process(this, 'programs', 'is-new-program-name-exist', objServer, objLocal);	
		}

	this.isModifiedProgramNameExistInServerData = function(oClient, strName, strNotes){
		this.debug('isModifiedProgramNameExistInServerData(' + oClient + ', ' + strName + ', ' + strNotes + ')');
		
		var objServer = {
			clientid : oClient.getId(),
			programid : this.getProgId(),
			programname : strName,
			programnotes : strNotes,
			}
		var objLocal = {
			oclient : oClient,
			programid : this.getProgId(),	
			programname : strName,
			programnotes : strNotes,
			}
		this.lastPid = this.jcomm.process(this, 'programs', 'is-modified-program-name-exist', objServer, objLocal);	
		}

	this.isClientProgramNameExist = function(oClient, strName, strFrom){
		this.debug('isClientProgramNameExist(' + oClient + ', ' + strName + ', ' + strFrom + ')');
		
		var objServer = {
			clientid : oClient.getId(),
			programname : strName,
			}
		var objLocal = {
			oclient : oClient,
			programname : strName,
			isfrom : strFrom,
			}
		this.lastPid = this.jcomm.process(this, 'programs', 'is-client-program-name-exist', objServer, objLocal);	
		}

	this.isNewProgramNameValidationReturnFromServer = function(obj, extraObj){
		this.debug('isNewProgramNameValidationReturnFromServer(' + obj + ', ' + extraObj + ')');
		//
		if(obj.exist == '1'){
			//advise user
			this.mainAppz.popupAlertReplaceProgramName(extraObj.programname, extraObj.oclient, {type:'new'});
		}else{
			//create
			this.createNewProgramToDb(extraObj.oclient, extraObj.programname, false);
			}
		}

	this.isModifiedProgramNameValidationReturnFromServer = function(obj, extraObj){
		this.debug('isModifiedProgramNameValidationReturnFromServer(' + obj + ', ' + extraObj + ')');
		//
		if(obj.exist == '1'){
			//advise user
			//this.debug('JProgram::isModifiedProgramNameValidationReturnFromServer: YES');
			//this.mainAppz.buildPopupWindowUserMessage(extraObj.programname, extraObj.programnotes);
			this.mainAppz.popupAlertReplaceProgramName(extraObj.programname, extraObj.oclient, {type:'modify', notes: extraObj.programnotes});
		}else{
			//create
			//this.debug('JProgram::isModifiedProgramNameValidationReturnFromServer: NO');
			this.modifyProgramBasicsToDb(extraObj.programname, extraObj.programnotes, {overwrite:false});
			}
		}

		
	this.isClientProgramNameExistValidationReturnFromServer = function(obj, extraObj){
		this.debug('isClientProgramNameExistValidationReturnFromServer(' + obj + ', ' + extraObj + ')');
		//console.log(obj);
		//console.log(extraObj);
		//
		if(obj.exist == '1'){
			//advise user
			this.mainAppz.popupAlertReplaceProgramName(extraObj.programname, extraObj.oclient, {type:'client', isfrom:extraObj.isfrom});
		}else{
			/*
			DWIZZEL::IMPORTANT
			//dans ce cas quand on crer le programme on va chercher les exercice deja placer pour les mettre dans le nouveau  programme
			*/
			this.createNewProgramToDb(extraObj.oclient, extraObj.programname, {keepprogramexercise: true});
			}
		}
		
	this.commCallBackFunc = function(pid, obj, extraobj){
		this.debug('commCallBackFunc(' + pid + ', ' + obj + ', ' + extraobj + ')');
		//
		//if(this.lastPid == pid){
			if(typeof(obj.msgerrors) != 'undefined' && obj.msgerrors != ''){
				this.startAutomaticProgramSave();
				this.debug(obj.msgerrors);
				this.mainAppz.openAlert('error', jLang.t('error!'), obj.msgerrors, false);
				//si jamais vient d'un "is-new-program-name-exist" on enleve le loader du bouton
				this.mainAppz.removeLoader('#butt-save-popup', jLang.t('save'));
			}else{
				if(obj.section == 'programs'){
					if(obj.service == 'save-program-modification'){
						this.startAutomaticProgramSave();
						this.mainAppz.changeProgramSaveState(true);
					}else if(obj.service == 'create-new-program'){
						this.mainAppz.addNewProgramToClient(obj.data, extraobj);
					}else if(obj.service == 'modify-program-name'){
						this.mainAppz.modifyProgramBasics(true, obj.data, extraobj);
					}else if(obj.service == 'is-new-program-name-exist'){
						this.isNewProgramNameValidationReturnFromServer(obj.data, extraobj);
					}else if(obj.service == 'is-modified-program-name-exist'){
						this.isModifiedProgramNameValidationReturnFromServer(obj.data, extraobj);
					}else if(obj.service == 'is-client-program-name-exist'){
						this.isClientProgramNameExistValidationReturnFromServer(obj.data, extraobj);
					}else{
						//
						}
					}
				}
			//}
		}
	
	this.debug = function(str){
		jDebug.show(this.className + '::' + str);
		}
		
		
	//end class
	}	
