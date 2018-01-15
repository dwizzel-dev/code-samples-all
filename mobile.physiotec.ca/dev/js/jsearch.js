/*

Author: DwiZZel
Date: 04-09-2015
Version: 3.1.0 BUILD X.X
Notes:	


	1.  besoin d'un cancel process
		EX: un client fait une recherche puis clic sur le refresh avant que le result soit revenu, ce qu fait que ca affiche en dessous du formulaire

*/

//----------------------------------------------------------------------------------------------------------------------
    
function JSearch(args){

	this.className = 'JSearch';

	//main appz soit la classe principale qui soccupe de tout
	this.mainAppz = args.mainappz;
	this.jcomm = args.jcomm;
	
	this.arrExercices = [];
	this.arrExercicesByIndexKey = []; //pour le parcours des loop avec un thread, car il faut garder l'index de la ou il est rendu, impossible a faire avec une key numerique
	this.lastPid = -1;
	this.count = 0;
	this.countLastResult = 0;
	
	//garde les derniers resultat de recherche pour le scrollevent display par batch
	this.arrLastResult = [];

	//pour les filtres keyIds = ExerciceId
	this.arrFilterIds = [];	
	
	//pour les filtres keyIds = Name
	this.arrFilterNames = [];	
	
	//les filtres selectionne
	this.selectedFilterId = -1;		
	
	//le data original que l'on recoit et que l'on a clone
	this.data = [];
	
	//pour aller plus vite quand fait une modif des locales, flip, mirror du data original
	//[idExercice] = [position in array numeric]			
	this.dataKeyId = [];
	
	//le pid de la derniere recherche car si clique vite les resultats n'arrivent pas dans le bon ordre
	this.lastSearchPid = 0;
		


	//----------------------------------------------------------------------------------------------------------------------*
	this.applySearchFilter = function(filterId){
		this.debug('applySearchFilter(' + + filterId + ')');

		this.selectedFilterId = filterId;		
		//this.selectedCategoryId = categoryId;	
		var arrKeepedData = [];
		
		if(this.selectedFilterId === -2){
			//si == -2, only chow mes exercices ceux que j'ai cree
			for(var o in this.data){
				if(typeof(this.data[o].mine) != 'undefined'){
					arrKeepedData.push(this.data[o]);
					}
				}
			this.rebuildListingResultWithFilter(arrKeepedData);
		/*
		//option 3 et 4 ironjt a plus tard	
		}else if(this.selectedFilterId === -3){
			//si == -3, only chow favorites
			for(var o in this.data){
				if(typeof(this.data[o].fav) != 'undefined'){
					arrKeepedData.push(this.data[o]);
					}
				}
			this.rebuildListingResultWithFilter(arrKeepedData);
		
		}else if(this.selectedFilterId === -4){
			//si == -4, juste ceux qui ont du userdata comme "mes exercices" modifies
			for(var o in this.data){
				if(typeof(this.data[o].userdata) == 'string'){
					arrKeepedData.push(this.data[o]);
					}
				}
			this.rebuildListingResultWithFilter(arrKeepedData);
		*/	
		}else if(this.selectedFilterId !== -1){
			//les selected ids si different de -1 on a un filtre
			//on loop et on check avec le data original non filtre
			for(var o in this.data){
				//format filter: 614,3946,4711,4963,5548, // soit "614," ou ",5548,"
				//le filter
				if(this.data[o].filter != ''){
					if(this.data[o].filter.match(eval('/^' + this.selectedFilterId + ',|,' + this.selectedFilterId + ',/g')) != null){
						arrKeepedData.push(this.data[o]);
						}
					}
				}
			this.rebuildListingResultWithFilter(arrKeepedData);

		}else{
			//on envoie tout en copiant le data original
			this.rebuildListingResultWithFilter(this.cloneDataObj(this.data, false));
			}
		}



	//----------------------------------------------------------------------------------------------------------------------*
	this.getArrExercicesKeyIndex = function(){
		return this.arrExercicesByIndexKey;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getExercices = function(){
		if(this.arrExercices.length > 0){
			return this.arrExercices;
			}
		return false;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getExercicesCount = function(){
		return this.count;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getLastResult = function(){
		//on retire les resulats un par un	
		if(this.arrLastResult.length <= 0){
			return false;
			}
		return this.arrLastResult.shift();
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getLastResultPendingCount = function(){
		return this.arrLastResult.length;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getLastResultCount = function(){
		return this.countLastResult;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getLastResults = function(){
		return this.arrLastResult;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.addExercice = function(id, oExercice){
		//this.debug('addExercice(' + id + ', ' + oExercice + ')');
		//base on exercice ID
		if(typeof(this.arrExercices[id]) != 'object'){
			this.arrExercices[id] = oExercice; 
			//pour pouvoir looper et garder l'ordre des exercices
			//sinon il va utiliser le id numeric et le classer en ordre de grandeur
			this.arrExercicesByIndexKey[this.count] = id; 
			this.count++;
			}
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.getTemplateExerciceById = function(tId){
		this.debug('getTemplateExerciceById(' + tId + ')');
		//
		var objLocal = {};
		//
		this.lastPid = this.jcomm.process(this, 'search', 'get-template-exercices-by-id', tId, objLocal);
		//
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.setHasMyInstruction = function(id, obj, strFrom){ // le objLocale.locale.en_US...fr_CA.short_title etc...
		this.debug('setHasMyInstruction(' + id + ', ' + obj + ', ' + strFrom + ')');
	
		var oExercice;
		if(strFrom == 'search'){ //from serach
			oExercice = this.getExerciceById(id);
		}else{ //from program
			oExercice = this.mainAppz.jprogram.getExerciceById(id);
			}

		//on fait le call uniquement si on a l'exercice
		if(typeof(oExercice) == 'object'){	
			//
			var objServer = {
				exerciceid : id,
				flip: oExercice.getFlip(),
				mirror: oExercice.getMirror(),	
				locale: obj,
				};
			//	
			var objLocal = {
				exerciceid : id,
				locale: obj,
				strfrom: strFrom,
				flip: oExercice.getFlip(),
				mirror: oExercice.getMirror(),	
				};
			//
			this.lastPid = this.jcomm.process(this, 'search', 'set-has-my-instruction', objServer, objLocal);
		}else{
			this.debug('setHasMyInstruction(' + id + '): exercice was not found');
			}
		//
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.saveExerciceModifications = function(id, obj){ // le objLocale.locale.en_US...fr_CA.short_title etc...
		this.debug('saveExerciceModifications(' + id + ', ' + obj + ')');
		//go get the flip and mirror 
		var oExercice = this.getExerciceById(id);
		//
		var objServer = {
			exerciceid : id,
			flip: oExercice.getFlip(),
			mirror: oExercice.getMirror(),	
			locale: obj,
			};
		//
		var objLocal = {
			exerciceid : id,
			locale: obj,
			flip: oExercice.getFlip(),
			mirror: oExercice.getMirror(),	
			};
		//
		this.lastPid = this.jcomm.process(this, 'search', 'save-exercice-modifications', objServer, objLocal);
		//
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getSearchTemplates = function(){
		this.debug('getSearchTemplates()');
		//
		var objServer = {
			userid: this.mainAppz.juser.getId(),	
			};
		//
		var objLocal = {};
		//
		this.lastPid = this.jcomm.process(this, 'search', 'get-search-templates', objServer, objLocal);		
		//
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getSearchModules = function(){
		this.debug('getSearchModules()');
		//
		this.lastPid = this.jcomm.process(this, 'search', 'get-search-modules', {}, {});		
		//
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.fetchExerciceSearchAutoCompleteData = function(str, idModule, params){
		this.debug('fetchExerciceSearchAutoCompleteData(' + str + ', ' + idModule + ', ' + params + ')');
		//
		var objServer = {
			word: str,
			module: idModule,
			};

		var objLocal = {
			word: str,
			params : params,
			module: idModule,
			};
		//
		this.lastPid = this.jcomm.process(this, 'search', 'fetch-exercice-search-autocomplete', objServer, objLocal);		
		//
		return this.lastPid;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getExerciceListingByWord = function(str, idModule){
		this.debug('getExerciceListingByWord(' + str + ', ' + idModule + ')');
		//
		var objServer = {
			word: str,
			module: idModule,
			};

		var objLocal = {
			word: str,
			module: idModule,
			};
		//
		this.lastPid = this.jcomm.process(this, 'search', 'get-exercice-listing-by-word', objServer, objLocal);		
		//
		this.lastSearchPid = this.lastPid;
		}
		
	//----------------------------------------------------------------------------------------------------------------------*	
	this.commCallBackFunc = function(pid, obj, extraObj){
		this.debug('commCallBackFunc(' + pid + ', ' + obj + ', ' + extraObj + ')');
		//	
		if(typeof(obj.msgerrors) == 'string' && obj.msgerrors != ''){
			this.debug(obj.msgerrors);
			this.mainAppz.openAlert('error', jLang.t('error!'), obj.msgerrors, false);
		}else{
			if(obj.section == 'search'){
				if(obj.service == 'get-template-exercices-by-id'){
					this.mainAppz.addExerciceToProgramFromTemplate(obj.data);
				}else if(obj.service == 'set-has-my-instruction'){
					this.changeSearchUserDataInfos(extraObj);
					this.mainAppz.saveSearchCarousselSetHasMyInstructionCallBackFromServer(extraObj); 
					this.mainAppz.displaySearchBoxesHasContentView();
				}else if(obj.service == 'save-exercice-modifications'){
					this.changeSearchUserDataInfos(extraObj);
					this.mainAppz.saveSearchCarousselInputModificationsCallBackFromServer(extraObj);
					this.mainAppz.displaySearchBoxesHasContentView();
				}else if(obj.service == 'get-search-templates'){
					this.mainAppz.getSearchTemplatesReturnFromServer(obj.data, extraObj);
				}else if(obj.service == 'get-search-modules'){
					this.mainAppz.getSearchModulesRFS(obj.data, extraObj);
				}else if(obj.service == 'fetch-exercice-search-autocomplete'){
					this.mainAppz.jautocomplete.fetchExerciceSearchAutoCompleteDataReturnFromServer(obj.data, extraObj.params, extraObj.word, pid);
				}else if(obj.service == 'get-exercice-listing-by-word'){
					//check si le pid de la recherche est le meme que le dernier pid de la recherche lance, 
					//car une vielle recherche pourrait ecraser une plus recente
					if(pid == this.lastSearchPid){
						this.routineOnListingResult(obj.data);
						}
				}else if(obj.service == 'get-search-filters-name'){
					this.getSearchFiltersNameRFS(obj.data, extraObj);
				}else{
					//
					}
				}
			}

		}	

	//----------------------------------------------------------------------------------------------------------------------*
	this.routineOnListingResult = function(data){
		this.debug('routineOnListingResult()');
		this.clear();
		//clone the base
		this.data = this.cloneDataObj(data, true);
		//real data
		this.arrLastResult = data;
		//last count
		this.countLastResult = this.arrLastResult.length;
		//get all the filter for dropbox filter
		this.getAllFiltersType();
		//show result
		this.mainAppz.fillSearch();
		}

	
	//----------------------------------------------------------------------------------------------------------------------*
	this.rebuildListingResultWithFilter = function(data){
		this.debug('rebuildListingResultWithFilter()');
		this.partialClear();
		this.arrLastResult = data;
		this.countLastResult = this.arrLastResult.length;
		this.mainAppz.fillSearch();
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.cloneDataObj = function(data, bKeepKeyIds){
		this.debug('cloneDataObj(data, ' + bKeepKeyIds + ')');

		var arr = [];
		if(bKeepKeyIds){
			this.dataKeyId = [];
			for(var o in data){
				arr.push(data[o]);
				this.dataKeyId[data[o].id] = o;
				}
		}else{
			for(var o in data){
				arr.push(data[o]);
				}
			}
		//
		return arr;	
		}
	

	//----------------------------------------------------------------------------------------------------------------------*
	this.getAllFiltersType = function(){
		this.debug('getAllFiltersType()');
		//find all the UNIQUE filterIds, since there qill be a repetition on the ids
		var strFilter = '';	
		for(var o in this.arrLastResult){
			//filters: 689,156,123,etc...
			if(this.arrLastResult[o].filter != ''){
				var arrSplitFilter = this.arrLastResult[o].filter.split(',');
				if(typeof(arrSplitFilter) == 'object'){	
					for(var p in arrSplitFilter){
						if(parseInt(arrSplitFilter[p]) > 0 && typeof(this.arrFilterIds[parseInt(arrSplitFilter[p])]) == 'undefined'){
							this.arrFilterIds[parseInt(arrSplitFilter[p])] = parseInt(arrSplitFilter[p]);
							strFilter += parseInt(arrSplitFilter[p]) + ',';
							}
						}
					}
				}
			}
		//check si du data a chercher
		if(strFilter != ''){
			//va caller le openSearchFilters au retour du getSearchFiltersNameRFS
			this.getSearchFiltersNameFromServer(strFilter.substr(0, (strFilter.length - 1)));
		}else{
			//si pas alors on call init du filtre tout de suite
			this.mainAppz.openSearchFilters();
			}
		
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getFilterFilters = function(){
		this.debug('getFilterFilters()');
		return this.arrFilterNames;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.partialClear = function(){
		this.debug('partialClear()');
	
		//clear array	
		this.arrLastResult = [];
		this.arrExercices = [];
		this.arrExercicesByIndexKey = [];
		//clear counter
		this.countLastResult = 0;
		this.count = 0;
		}

		//----------------------------------------------------------------------------------------------------------------------*
	this.getSearchFiltersNameFromServer = function(strFilter){
		this.debug('getSearchFiltersNameFromServer(' + strFilter + ')');
		//
		var objServer = {
			filter: strFilter,
			};
		//
		this.lastPid = this.jcomm.process(this, 'search', 'get-search-filters-name', objServer, {});		
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.getSearchFiltersNameRFS = function(obj, extraObj){
		this.debug('getSearchFiltersNameRFS()', obj);
		this.debug('getSearchFiltersNameRFS()', extraObj);

		this.arrFilterNames = [];
		if(typeof(obj.filter) == 'object'){
			this.arrFilterNames = obj.filter;
			}
		//
		this.mainAppz.openSearchFilters();
		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.changeSearchUserDataInfos = function(extraObj){
		this.debug('changeSearchUserDataInfos()');
		//console.log(extraObj);
		//devra changer les titres des resultats de la recherche si ils sont affichés	c,est a dire le jexercice.userdata
		if(typeof(extraObj) == 'object'){
			if(typeof(this.arrExercices[extraObj.exerciceid]) == 'object'){
				if(typeof(extraObj.locale) != 'undefined'){
					this.arrExercices[extraObj.exerciceid].overwriteUserData(extraObj.locale);
					}	
				//si jamais on a change le flip et mirror on le change ici
				if(typeof(extraObj.flip) != 'undefined'){
					this.arrExercices[extraObj.exerciceid].setFlip(extraObj.flip);
					}
				if(typeof(extraObj.mirror) != 'undefined'){
					this.arrExercices[extraObj.exerciceid].setMirror(extraObj.mirror);
					}
				//IMPORTANT:
				//quand on a des filtres on travaille sur une copie du data original
				//quand on change de filtre il reprend le data original pour en refaire une autre copie, 
				//donc on perd les donnees mofifie car il reprend le data original et non celui du this.arrExercices
				//donc on doit changer aussi :
				//	1. le userdata original compresse en json, 
				//	2. le flip, 
				//	3. le mirror, 
				//pour aller plus vite un array de key_id_exercice => position dans le array du this.data
				var iDataPos = this.dataKeyId[extraObj.exerciceid];
				//check si existe
				if(typeof(this.data[iDataPos]) == 'object'){
					//check si le bon id exercice au cas ou
					if(this.data[iDataPos].id == extraObj.exerciceid){
						//on set le flip
						if(typeof(extraObj.flip) != 'undefined'){
							this.data[iDataPos].flip = extraObj.flip;
							}
						//on set le mirror
						if(typeof(extraObj.mirror) != 'undefined'){
							this.data[iDataPos].mirror = extraObj.mirror;
							}	
						//on compresse et set le local du userdata
						if(typeof(extraObj.locale) != 'undefined'){
							this.data[iDataPos].userdata = JSON.stringify(extraObj.locale);
							}
						}
					}
				

				
				}
			}
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.clear = function(){
		this.debug('clear()');
		
		//destroy all event manager
		//pas certain que ca soit une bonne idee de ne pas destroy le event manager
		/*
		for(var o in this.arrExercices){
			this.arrExercices[o].destroyEventManager();
			}
		*/
		//clear array	
		this.arrLastResult = [];
		this.arrExercices = [];
		this.arrExercicesByIndexKey = [];
		//clear counter
		this.countLastResult = 0;
		this.count = 0;
		//pour les filtres
		this.arrFilterIds = [];	
		//pour les filters name
		this.arrFilterNames = [];	
		//pour les selected filters
		this.selectedFilterId = -1;		
		//le data de base que l'on recoit du service call
		this.data = [];	
		this.dataKeyId = [];	


		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.contains = function(id){
		this.debug('contains(' + id + ')');	

		if(typeof(this.arrExercices[o]) == 'object'){
			return true;
			}

		return false;	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.addEventManager = function(id, evtMng){
		//this.debug('addEventManager(' + id + ', ' + evtMng + ')');	
		
		this.arrExercices[id].addEventManager(evtMng);
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getExerciceById = function(id){
		this.debug('getExerciceById(' + id + ')');	
		
		if(typeof(this.arrExercices[id]) == 'object'){
			return this.arrExercices[id];
			}
		return false;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getExerciceObjById = function(id){
		this.debug('getExerciceObjById(' + id + ')');	
		
		if(typeof(this.arrExercices[id]) == 'object'){
			return this.arrExercices[id].getObj();
			}
		return false;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.debug = function(str){
		jDebug.show(this.className + '::' + str);	
		}


	}


//CLASS END


