/*

Author: DwiZZel
Date: 25-11-2015
Version: 3.1.0 BUILD X.X
Notes:	classe rtelative au protocole (template)
		
*/


//----------------------------------------------------------------------------------------------------------------------
    
function JTemplate(args){
	
	this.className = 'JTemplate';

	//main appz soit la classe principale qui soccupe de tout
	this.mainAppz = args.mainappz;
	//le comm class	
	this.jcomm = args.jcomm;
	//pid comm callback
	this.lastPid = -1;	
	//ids
	this.id = -1;	
	this.name = '';		
	this.notes = '';	
	this.module = '';	


	//----------------------------------------------------------------------------------------------------------------------*
	this.getName = function(){
		this.debug('getName()');

		return this.name;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getNotes = function(){
		this.debug('getNotes()');

		return this.notes;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getModule = function(){
		this.debug('getModule()');

		return this.module;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getId = function(){
		this.debug('getId()');

		return this.id;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.setName = function(strName){
		this.debug('setName(' + strName + ')');

		this.name = this.mainAppz.jutils.toUpper(strName);
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.setNotes = function(strNotes){
		this.debug('setNotes(' + strNotes + ')');

		this.notes = strNotes;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.setModule = function(strModule){
		this.debug('setModule(' + strModule + ')');

		this.module = strModule;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.setId = function(id){
		this.debug('setId(' + id + ')');
		
		this.id = id;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.clear = function(){
		this.debug('clear()');

		this.id = -1;	
		this.name = '';	
		/***********************************************/
		/***********************************************/
		// amjad changes 
		this.notes = '';
		// end of amjad changes
		/************************************************/
		/************************************************/
		this.module = '';	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.isTemplateNameAlreadyExistInServerData = function(strName, strModule){
		this.debug('isTemplateNameAlreadyExistInServerData(' + strName + ', ' + strModule + ')');

		//call servive
		var objServer = {
			templatename: strName,
			};

		var objLocal = {
			templatename: strName,
			templatemodule: strModule,
			};
				
		this.lastPid = this.jcomm.process(this, 'template', 'is-template-name-exist', objServer, objLocal);	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.createNewTemplateToDb = function(strName, strModule, extraObj){
		this.debug('createNewTemplateToDb(' + strName + ', ' + strModule + ', ' + extraObj + ')');
		
		//call servive
		var objServer = {
			templatename: strName,
			templatemodule: strModule,
			};

		var objLocal = {
			templatename: strName,
			templatemodule: strModule,
			};
				
		this.lastPid = this.jcomm.process(this, 'template', 'create-new-template', objServer, objLocal);	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.saveTemplateModifications = function(obj){
		this.debug('saveTemplateModifications::obj', obj);

		//call servive
		var objServer = {
			id: obj.id,
			name: obj.name,
			notes: obj.notes,
			order: obj.order.toString(),
			module: obj.module,
			exercices: obj.exercices,
			overwritename: obj.overwritename,
			keeporiginal: obj.keeporiginal,
			};
			

		//local data	
		var objLocal = {
			id: obj.id,
			name: obj.name,
			notes: obj.notes,
			module: obj.module,
			};
				
		this.lastPid = this.jcomm.process(this, 'template', 'save-template-modification', objServer, objLocal);	
		}

	
	//----------------------------------------------------------------------------------------------------------------------*
	this.commCallBackFunc = function(pid, obj, extraobj){
		this.debug('commCallBackFunc(' + pid + ', ' + obj + ', ' + extraobj + ')');
		if(this.lastPid == pid){
			if(typeof(obj.msgerrors) != 'undefined' && obj.msgerrors != ''){
				this.debug(obj.msgerrors);
				this.mainAppz.openAlert('error', jLang.t('error!'), obj.msgerrors, false);
				//remove le loader si il y a 
				this.mainAppz.removeLoader('#butt-save-popup', jLang.t('save'));
			}else{
				if(obj.section == 'template'){
					if(obj.service == 'is-template-name-exist'){
						this.isTemplateNameAlreadyExistReturnFromServer(obj.data, extraobj);
					}else if(obj.service == 'create-new-template'){
						this.createNewTemplateToDbReturnFromServer(obj.data, extraobj);
					}else if(obj.service == 'save-template-modification'){	
						this.saveTemplateModificationsReturnFromServer(obj.data, extraobj);
					}else{
						//
						}
					}
				}
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.isTemplateNameAlreadyExistReturnFromServer = function(obj, extraObj){
		this.debug('isTemplateNameAlreadyExistReturnFromServer(' + obj + ', ' + extraObj + ')');
		//
		if(obj.exist == '1'){
			//advise user
			this.mainAppz.popupAlertReplaceTemplateName(extraObj.templatename, extraObj.templatemodule, {type:'new'});
		
		}else{
			//if not then save it
			this.createNewTemplateToDb(extraObj.templatename, extraObj.templatemodule, {});
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.createNewTemplateToDbReturnFromServer = function(obj, extraObj){
		this.debug('createNewTemplateToDbReturnFromServer(' + obj + ', ' + extraObj + ')');	
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
			this.mainAppz.createNewTemplateToDbReturnFromServer(obj, extraObj); //obj.data = array comme le listing
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.saveTemplateModificationsReturnFromServer = function(obj, extraObj){
		this.debug('saveTemplateModificationsReturnFromServer(' + obj + ', ' + extraObj + ')');	
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
			this.mainAppz.saveTemplateModificationsReturnFromServer(obj, extraObj); //obj.data = array comme le listing
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//----------------------------------------------------------------------------------------------------------------------*
	this.debug = function(){
		if(arguments.length == 1){	
			jDebug.show(this.className + '::' + arguments[0]);
		}else{
			jDebug.showObject(this.className + '::' + arguments[0], arguments[1]);
			}
		}
		
		
	//end class


		
	}
