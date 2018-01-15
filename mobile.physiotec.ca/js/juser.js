/*

Author: DwiZZel
Date: 25-11-2015
Version: 3.1.0 BUILD X.X
Notes:	classe rtelative a l'usager qui utilise	'application, c'est a dire le physio
		
*/


//----------------------------------------------------------------------------------------------------------------------
    
function JUser(args){
	
	this.className = 'JUser';

	//main appz soit la classe principale qui soccupe de tout
	this.mainAppz = args.mainappz;
	//le comm class	
	this.jcomm = args.jcomm;
	//preference software
	//this.preferences = [];		
	//session holder
	this.sessionId = gSessionId; // is global from index.php
	this.userName = '';	
	this.password = '';	
	this.id = -1;	
	this.defaultModuleId = -1;	//le module par defaut pour la recherche
	//pid comm callback
	this.lastPid = -1;
	//les langue pour les select box depende de la license
	this.arrLang = [];	
	//ping timeout
	this.pingDelay = 60000 * 30;			

	//----------------------------------------------------------------------------------------------------------------------*
	this.getId = function(){
		this.debug('getId()');

		return this.id;	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getUserName = function(){
		this.debug('getUserName()');	
		
		return this.userName;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getPsw = function(){
		this.debug('getPsw()');

		return this.password;	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	/*
	this.getModules = function(){
		this.debug('getModules()');	

		//call servive
		var objServer = {
			username : this.userName,
			id : this.id,
			};

		var objLocal = {
			};
				
		this.lastPid = this.jcomm.process(this, 'user', 'get-modules', objServer, objLocal);	
		}
	*/
	//----------------------------------------------------------------------------------------------------------------------*
	this.getBasicsInfos = function(){
		this.debug('getBasicsInfos()');	

		//call servive
		var objServer = {
			};

		var objLocal = {
			};
				
		this.lastPid = this.jcomm.process(this, 'user', 'get-basics-infos', objServer, objLocal);	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getModulesForSelectOptions = function(boxId){
		this.debug('getModulesForSelectOptions(' + boxId + ')');	
		
		//call servive
		var objServer = {
			//username : this.userName,
			//id : this.id,
			};

		var objLocal = {
			boxid: boxId,
			};
				
		this.lastPid = this.jcomm.process(this, 'user', 'get-modules-for-select-options', objServer, objLocal);	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	/*
	this.getPreferences = function(){
		this.debug('getPreferences()');	
		}
	*/

	//----------------------------------------------------------------------------------------------------------------------*
	this.getPrintParameters = function(progId, clientId){
		this.debug('getPrintParameters(' + progId + ', ' + clientId + ')');	
		
		//call servive
		var objServer = {
			//id : this.id,
			programid : progId,
			clientid : clientId,
			};

		var objLocal = {
			programid : progId,
			clientid : clientId,
			};

		this.lastPid = this.jcomm.process(this, 'user', 'get-print-parameters', objServer, objLocal);	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.savePrintParameters = function(formatId, typeId, sizeId){
		this.debug('savePrintParameters(' + formatId + ', ' + typeId + ', ' + sizeId + ')');	
		
		//par defaut celle de l'interface
		var strLang = gLocaleLang; 
		//on va chercher la langue du client, (PAS celle de l'interface ou du user)
		var oClient = this.mainAppz.jprogram.getClient();
		if(typeof(oClient) == 'object'){
			strLang = oClient.getLocale();
			}

		//call servive
		var objServer = {
			//id: this.id,
			formatid: formatId,
			typeid: typeId,
			sizeid: sizeId,
			clientlang: strLang,
			clientid: this.mainAppz.jprogram.getClientId(),
			progid: this.mainAppz.jprogram.getProgId(),
			};

		var objLocal = {
			formatid : formatId,
			typeid : typeId,
			sizeid : sizeId,
			clientlang : strLang,	
			progid: this.mainAppz.jprogram.getProgId(),
			};

		this.lastPid = this.jcomm.process(this, 'user', 'save-print-parameters', objServer, objLocal);	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getSessionId = function(){
		this.debug('getSessionId()');	

		return this.sessionId;
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.setSessionId = function(str){
		this.debug('setSessionId(' + str + ')');	

		this.sessionId = str;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.setId = function(str){
		this.debug('setId(' + str + ')');	

		this.id = str;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.setUserName = function(str){
		this.debug('setUserName(' + str + ')');	

		this.userName = str;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.setPsw = function(str){
		this.debug('setPsw(' + str + ')');	

		this.password = str;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.setArrLang = function(obj){
		this.debug('setArrLang(' + obj + ')');	
	
		this.arrLang = obj;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getArrLang = function(){
		this.debug('getArrLang()');	
	
		return this.arrLang;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.setModuleId = function(id){
		this.debug('setModuleId(' + id + ')');	
	
		this.defaultModuleId = id;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getModuleId = function(){
		this.debug('s=getModuleId()');	
	
		return this.defaultModuleId;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	/*
	this.getLangById = function(id){
		this.debug('getLangById(' + id + ')');	
		if(typeof(this.arrLang[id]) == 'string'){
			return this.arrLang[id];
			}
		return gLocaleLangDefault; //default in index.php
		}
	*/
	//----------------------------------------------------------------------------------------------------------------------*
	this.doLogin = function(strUsername, strPassword){
		this.debug('doLogin(' + strUsername + ', ' + strPassword + ')');	
		
		var objServer = {
			username: strUsername,
			password: strPassword,
			};
		var objLocal = {
			username: strUsername,
			password: strPassword,
			};

		this.lastPid = this.jcomm.process(this, 'user', 'do-login', objServer, objLocal);	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.doLogout = function(){
		this.debug('doLogout()');	
		
		var objServer = {
			/*sessionid : this.sessionId,*/
			//id : this.id,
			//username: this.userName,
			};
		var objLocal = {
			};

		this.lastPid = this.jcomm.process(this, 'user', 'do-logout', objServer, objLocal);	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.pingService = function(){
		this.debug('pingService()');	
		
		var objServer = {
			};
		var objLocal = {
			};

		this.lastPid = this.jcomm.process(this, 'user', 'ping-service', objServer, objLocal);	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.pingServiceReturnFromServer = function(obj, extraObj){
		this.debug('pingServiceReturnFromServer(' + obj + ', ' + extraObj + ')');

		var bContinue = true;
		//on check si il y a une erreur de la session via le retour du service
		if(typeof(obj.error) != 'undefined'){
			if(obj.error == '1'){
				//on pop le msg d'erreur
				bContinue = false;
				//
				this.mainAppz.openAlert('alert', jLang.t('error!'), obj.errormessage, false);
				}
		}else if(typeof(obj.msgerrors) != 'undefined'){
			//check les errurs de jcomm si il y a eu un probleme de communication
			bContinue = false;
			//
			this.mainAppz.openAlert('alert', jLang.t('error!'), obj.msgerrors, false);
			}

		//recall le service dans 1 minutes sinon get out
		if(bContinue){
			setTimeout(this.pingService.bind(this), this.pingDelay); 
		}else{
			//reload the application
			setTimeout(this.mainAppz.doLogout.bind(this.mainAppz), 2500);
			}

		};

	//----------------------------------------------------------------------------------------------------------------------*
	/*
	this.getBasicsInfosReturnFromServer = function(obj, extraObj){
		this.debug('getBasicsInfosReturnFromServer(' + obj + ', ' + extraObj + ')');

		var bContinue = true;
		//on check si il y a une erreur de la session via le retour du service
		if(typeof(obj.error) != 'undefined'){
			if(obj.error == '1'){
				//on pop le msg d'erreur
				bContinue = false;
				//
				this.mainAppz.openAlert('alert', jLang.t('error!'), obj.errormessage, false);
				}
		}else if(typeof(obj.msgerrors) != 'undefined'){
			//check les errurs de jcomm si il y a eu un probleme de communication
			bContinue = false;
			//
			this.mainAppz.openAlert('alert', jLang.t('error!'), obj.msgerrors, false);
			}

		//recall le service dans 1 minutes sinon get out
		if(bContinue){
			this.mainAppz.getBasicsInfosReturnFromServer(obj, extraobj);
		}else{
			//reload the application
			setTimeout(this.mainAppz.doLogout.bind(this.mainAppz), 3000);
			}

		};
	*/
	//----------------------------------------------------------------------------------------------------------------------*
	this.commCallBackFunc = function(pid, obj, extraobj){
		this.debug('commCallBackFunc(' + pid + ', ' + obj + ', ' + extraobj + ')');
		//
		//if(this.lastPid == pid){
			if(typeof(obj.msgerrors) != 'undefined' && obj.msgerrors != ''){
				this.debug(obj.msgerrors);
				this.mainAppz.openAlert('error', jLang.t('error!'), obj.msgerrors, false);
				//si jamais vient du login on enleve le loader sur le bouton de login, pas moyen de savoir si est ouvert ou pas
				this.mainAppz.removeLoader('#butt-login', jLang.t('login'));
			}else{
				if(obj.section == 'user'){
					//if(obj.service == 'get-modules'){
						//console.log(obj.data);
					if(obj.service == 'get-modules-for-select-options'){
						this.mainAppz.fillPopupModuleSelectOptionsFromArray(extraobj.boxid, obj.data);
					}else if(obj.service == 'get-print-parameters'){
						this.mainAppz.getPrintParametersReturnFromServer(obj.data, extraobj);
					}else if(obj.service == 'save-print-parameters'){
						this.mainAppz.savePrintParametersReturnFromServer(obj.data, extraobj);
					}else if(obj.service == 'do-login'){
						this.mainAppz.doLoginReturnFromServer(obj.data, extraobj);
					}else if(obj.service == 'get-basics-infos'){
						this.mainAppz.getBasicsInfosReturnFromServer(obj.data, extraobj);
					}else if(obj.service == 'ping-service'){
						this.pingServiceReturnFromServer(obj.data, extraobj);
					}else{
						//
						}
					}
				}
			//}
		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.debug = function(str){
		jDebug.show(this.className + '::' + str);
		}
		
		



		
	}

//CLASS END




