/*

Author: DwiZZel
Date: 09-12-2015
Version: 3.1.0 BUILD X.X
Notes:	regtest ()[]^${}?|.=+/-*

\(|\)|\[|\]|\^|\$|\{|\}|\?|\||\.|\=|\+|\/|\-|\*


crash server line: \(|\)|\[|\]|\^|\$|\{|\}|\?|\||\.|\=|\+|\/|\-|\*


		
*/

//----------------------------------------------------------------------------------------------------------------------
    
function JAutoComplete(args){
	
	//clas name
	this.className = 'JAutoComplete';

	//base div 
	this.baseDivId = 'main-autocomplete-result';
	
	//array of input box name
	this.arrInputBox = [];		
	
	//main appz soit la classe principale qui soccupe de tout
	this.mainAppz = args.mainappz;
	this.jcomm = args.jcomm;

	//min-max
	this.minStrLen = 1;	

	//le lastPid des requete Client et Exercices
	this.lastAutoCompletePid = {
		client: 0,
		exercice: 0
		};

	
	//----------------------------------------------------------------------------------------------------------------------*	
	this.fetchClientSearchAutoCompleteData = function(evnt, str, params){
		this.debug('fetchClientSearchAutoCompleteData(' + evnt + ', ' + str + ', ' + params + ')');
		//console.log(evnt);	
		//on trim les avant et apres spaces
		str = str.trim();
		//		
		if(evnt.which != '13'){
			if(str != '' && str.length >= this.minStrLen){
				str = this.mainAppz.jutils.toLower(str);
				this.lastAutoCompletePid.client = this.mainAppz.jclientmanager.fetchClientSearchAutoCompleteData(str, params);
			}else{
				this.resetSingleAutoComplete(params);	
				}
		}else{
			//pas etre vide
			if(str != '' && str.length >= this.minStrLen){
				//enleve le focus du input
				$('#' + params.input).blur();
				//
				this.mainAppz.jclientmanager.getClientListingByWord(str, params);
				//selon si c,est le layer ou le popup
				if(params.type == 'client'){
					//clear the window
					this.mainAppz.resetClientSearchWindowForResult();
				}else if(params.type == 'client-popup'){
					//clearl le window
					this.mainAppz.resetPopupClientSearchWindowForResult();
					}
				//le box
				this.resetSingleSearchInputBox(params);
				//on affiche uniquement le word
				this.fillInputWithString(str, params);

				}
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*	
	this.fetchExerciceSearchAutoCompleteData = function(evnt, str, params, idModule){
		this.debug('fetchExerciceSearchAutoCompleteData(' + evnt + ', ' + str + ', ' + params + ', ' + idModule + ')');
		//on trim les avant et apres spaces
		str = str.trim();
		//			
		if(evnt.which != '13'){
			if(str != '' && str.length >= this.minStrLen){
				str = this.mainAppz.jutils.toLower(str);
				this.lastAutoCompletePid.exercice = this.mainAppz.jsearch.fetchExerciceSearchAutoCompleteData(str, idModule, params);
			}else{
				this.resetSingleAutoComplete(params);	
				}
			
		}else{
			//pas etre vide
			if(str != '' && str.length >= this.minStrLen){
				//enleve le focus du input
				$('#' + params.input).blur();
				//set timeout to let the keyboard go down
				this.mainAppz.jsearch.getExerciceListingByWord(str, idModule);
				//hide the form
				this.mainAppz.resetSearchWindowForResult();
				//le box
				this.resetSingleSearchInputBox(params);
				//on affiche uniquement le word
				this.fillInputWithString(str, params);
	
				}
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*	
	this.fetchClientSearchAutoCompleteDataReturnFromServer = function(obj, params, word, pid){
		this.debug('fetchClientSearchAutoCompleteDataReturnFromServer(' + obj + ', ' + params + ', ' + word + ', ' + pid + ')');
		
		/*
		PARAMS:
			.input:'input-main-client-search-autocomplete',
			.layer:'layer-client',
			.type:'client', 
			.position:'under',
		*/

		//check si le pid est le dernier, car certain resultat arrive plus tard que la derniere demande
		if(this.lastAutoCompletePid.client != pid){
			//rien a faire
			return;
			}

		//
		var bContinue = true;
		//check pour les erreurs si il y a, mais on ne faitr rien avec pour l'instant
		if(typeof(obj) == 'object'){
			if(typeof(obj.error) != 'undefined'){
				bContinue = false;
				}
			}
		//continue or not	
		if(bContinue && $('#' + params.input).is(':focus')){
			if(Object.keys(obj).length > 0){
				//les chars permis
				word = word.replace(/[^a-zA-Z0-9\sÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ+-]/gi, ' ');
				//les spacer
				word = word.replace(/[\s]+/gi, ' ');	
				//on split les mots
				var arrWords = word.split(' ');
				//get la position du serach box
				var data = '';
				//data
				for(var o in obj){
					if(typeof(obj[o].firstname) == 'string' && typeof(obj[o].lastname) == 'string' && typeof(obj[o].id) == 'number'){
						//first letter in uppercase
						obj[o].firstname = this.mainAppz.jutils.ucfirst(obj[o].firstname);
						obj[o].lastname = this.mainAppz.jutils.ucfirst(obj[o].lastname);	
						//mettre en bold les caractere retrouve
						for(var q in arrWords){
							//arrWords[p] = this.mainAppz.jutils.pregQuote(arrWords[p], '');
							obj[o].firstname = obj[o].firstname.replace(eval('/^' + this.mainAppz.jutils.ucfirst(arrWords[q]) + '/g'), '<b>' + this.mainAppz.jutils.ucfirst(arrWords[q]) + '</b>');
							obj[o].lastname = obj[o].lastname.replace(eval('/^' + this.mainAppz.jutils.ucfirst(arrWords[q]) + '/g'), '<b>' + this.mainAppz.jutils.ucfirst(arrWords[q]) + '</b>');
							}
						//datas to show
						data += '<div class="single-result" client-id="' + obj[o].id + '">' + obj[o].firstname + ' ' + obj[o].lastname + '</div>';
						/*
						//pour les regex on enleve les chars speciaux
						word = this.mainAppz.jutils.pregQuote(word, '');
						//first letter in uppercase
						obj[o].firstname = this.mainAppz.jutils.ucfirst(obj[o].firstname);
						obj[o].lastname = this.mainAppz.jutils.ucfirst(obj[o].lastname);
						//bold the eword we search
						obj[o].firstname = obj[o].firstname.replace(eval('/^' + word + '/gi'), '<b>' + this.mainAppz.jutils.ucfirst(word) + '</b>');
						obj[o].lastname = obj[o].lastname.replace(eval('/^' + word + '/gi'), '<b>' + this.mainAppz.jutils.ucfirst(word) + '</b>');
						data += '<div class="single-result" client-id="' + obj[o].id + '">' + obj[o].firstname + ' ' + obj[o].lastname + '</div>';
						*/
						}
					}
				//calc les positions
				if(params.position == 'under'){
					//en dessous de la input box
					var wAdjust = 20;
					var pos = $('#' + params.input).position();
					var h = $('#' + params.input).outerHeight(true);	
					var w = $('#' + params.input).outerWidth(true);
					//adjust avec le scroll barre
					pos.top +=  $('#' + params.layer).scrollTop();
					
				}else{
					//au dessus de la input box
					var wAdjust = 20;
					var pos = $('#' + params.input).position();
					var h = $('#' + params.input).outerHeight(true);	
					var w = $('#' + params.input).outerWidth(true);
					//adjust avec le scroll barre
					pos.top = 60 + h + 7; //60px qui equivaut au bottom de la barre outil
					}

				//check si on en a une deja ouverte
				if(!$('#' + params.layer + ' > #' + this.baseDivId).length){
					//rajoute
					if(params.position == 'under'){
						$('#' + params.layer).append('<div id="' + this.baseDivId + '" class="' + params.position + '" style="width:' + (w - wAdjust) + 'px;top:' + (pos.top + h) + 'px;left:' + pos.left + 'px;position:absolute;"></div>'); //20px = padding left et right
					}else{
						$('#' + params.layer).append('<div id="' + this.baseDivId + '" class="' + params.position + '" style="width:' + (w - wAdjust) + 'px;bottom:' + (pos.top) + 'px;left:' + pos.left + 'px;position:fixed;"></div>'); //20px = padding left et right
						}
				}else{
					//on ajuste selon
					if(params.position == 'under'){
						$('#' + params.layer + ' > #' + this.baseDivId).css({
							'width' : (w - wAdjust) + 'px',
							'top'	: (pos.top + h) + 'px',
							'left'	: pos.left + 'px',
							});
					}else{
						$('#' + params.layer + ' > #' + this.baseDivId).css({
							'width' : (w - wAdjust) + 'px',
							'bottom': (pos.top) + 'px',
							'left'	: pos.left + 'px',
							});
						}
					}
				//on ajoute le data
				$('#' + params.layer + ' > #' + this.baseDivId).html(data);
				//on met un listener pour le click sur les resultats
				$('#' + params.layer + ' > #' + this.baseDivId + ' .single-result').data('params', params);
				$('#' + params.layer + ' > #' + this.baseDivId + ' .single-result').click(function(e){
					e.preventDefault();
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						var clientId = $(this).attr('client-id');
						var params = $(this).data('params');
						var str = $(this).text();
						//
						//enleve le focus du input
						$('#' + params.input).blur();	
						//
						oTmp.jclientmanager.getSingleClientInfosById(clientId, params);
						//
						if(params.type == 'client'){
							//reset the window
							oTmp.resetClientSearchWindowForResult();
						}else if(params.type == 'client-popup'){
							//reset the window
							oTmp.resetPopupClientSearchWindowForResult();
							}
						//le box
						oTmp.jautocomplete.resetSingleSearchInputBox(params);
						//on affiche uniquement le word
						oTmp.jautocomplete.fillInputWithString(str, params);
						}
					});
				//on quitte
				return;
				}
			}
		//si on est la alors pas besoin on remove
		this.resetSingleAutoComplete(params);

		}

		//----------------------------------------------------------------------------------------------------------------------*	
	this.fetchExerciceSearchAutoCompleteDataReturnFromServer = function(obj, params, word, pid){
		this.debug('fetchExerciceSearchAutoCompleteDataReturnFromServer(' + obj + ', ' + params + ', ' + word + ', ' + pid + ')');
		
		/*
		PARAMS:
			.input:'input-main-client-search-autocomplete',
			.layer:'layer-client',
			.type:'client', 
			.position:'under',
			.module: 'is_a_number'
		*/

		//check si le pid est le dernier, car certain resultat arrive plus tard que la derniere demande
		if(this.lastAutoCompletePid.exercice != pid){
			//rien a faire
			return;
			}

		//
		var bContinue = true;
		//check pour les erreurs si il y a, mais on ne faitr rien avec pour l'instant
		if(typeof(obj) == 'object'){
			if(typeof(obj.error) != 'undefined'){
				bContinue = false;
				}
			}
		//continue or not
		if(bContinue && $('#' + params.input).is(':focus')){
			if(Object.keys(obj).length > 0){
				//les chars permis
				word = word.replace(/[^a-zA-Z0-9\sÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ+-]/gi, ' ');
				//les spacer
				word = word.replace(/[\s]+/gi, ' ');	
				//on split les mots
				var arrWords = word.split(' ');
				//get la position du serach box
				var data = '';
				//data
				for(var o in obj){
					if(typeof(obj[o].name) == 'string' && typeof(obj[o].id) == 'number'){
						var arrRtnWords = obj[o].name.split(' ');	
						//loop dans les mots
						for(var p in arrRtnWords){
							for(var q in arrWords){
								//arrWords[p] = this.mainAppz.jutils.pregQuote(arrWords[p], '');
								arrRtnWords[p] = arrRtnWords[p].replace(eval('/^' + arrWords[q] + '/gi'), '<b>' + arrWords[q] + '</b>');
								}
							}
						//loop pour reconstruire la phrase
						var strLiWords = '';
						for(var p in arrRtnWords){
							strLiWords += arrRtnWords[p] + ' ';
							}
						//
						data += '<div class="single-result" exercice-id="' + obj[o].id + '">' + strLiWords + '</div>';
						/*
						word = this.mainAppz.jutils.pregQuote(word, '');
						obj[o].name = obj[o].name.replace(eval('/^' + word + '/gi'), '<b>' + word + '</b>');
						data += '<div class="single-result" exercice-id="' + obj[o].id + '">' + obj[o].name + '</div>';
						*/
						}
					}
				//calc les positions
				if(params.position == 'under'){
					//en dessous de la input box
					var wAdjust = 20;
					var pos = $('#' + params.input).position();
					var h = $('#' + params.input).outerHeight(true);	
					var w = $('#' + params.input).outerWidth(true);
					//adjust avec le scroll barre
					pos.top +=  $('#' + params.layer).scrollTop();
					
				}else{
					//au dessus de la input box
					var wAdjust = 20;
					var pos = $('#' + params.input).position();
					var h = $('#' + params.input).outerHeight(true);	
					var w = $('#' + params.input).outerWidth(true);
					//adjust avec le scroll barre
					pos.top = 60 + h + 7; //60px qui equivaut au bottom de la barre outil
					}

				//check si on en a une deja ouverte
				if(!$('#' + params.layer + ' > #' + this.baseDivId).length){
					//rajoute
					if(params.position == 'under'){
						$('#' + params.layer).append('<div id="' + this.baseDivId + '" class="' + params.position + '" style="width:' + (w - wAdjust) + 'px;top:' + (pos.top + h) + 'px;left:' + pos.left + 'px;position:absolute;"></div>'); //20px = padding left et right
					}else{
						$('#' + params.layer).append('<div id="' + this.baseDivId + '" class="' + params.position + '" style="width:' + (w - wAdjust) + 'px;bottom:' + (pos.top) + 'px;left:' + pos.left + 'px;position:fixed;"></div>'); //20px = padding left et right
						}
				}else{
					//on ajuste selon
					if(params.position == 'under'){
						$('#' + params.layer + ' > #' + this.baseDivId).css({
							'width' : (w - wAdjust) + 'px',
							'top'	: (pos.top + h) + 'px',
							'left'	: pos.left + 'px',
							});
					}else{
						$('#' + params.layer + ' > #' + this.baseDivId).css({
							'width' : (w - wAdjust) + 'px',
							'bottom': (pos.top) + 'px',
							'left'	: pos.left + 'px',
							});
						}
					}
				//on ajoute le data
				$('#' + params.layer + ' > #' + this.baseDivId).html(data);
				//on met un listener pour le click sur les resultats
				$('#' + params.layer + ' > #' + this.baseDivId + ' .single-result').data('params', params);
				$('#' + params.layer + ' > #' + this.baseDivId + ' .single-result').click(function(e){
					e.preventDefault();
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						var exerciceId = $(this).attr('exercice-id');
						var params = $(this).data('params');
						var str = $(this).text();
						var idModule = $('#' + params.module).val();
						//enleve le focus du input
						$('#' + params.input).blur();
						//set timeout to let the keyboard go down
						oTmp.jsearch.getExerciceListingByWord(str, idModule);
						//hide the form
						oTmp.resetSearchWindowForResult();
						//le box
						oTmp.jautocomplete.resetSingleSearchInputBox(params);
						//on affiche uniquement le word
						oTmp.jautocomplete.fillInputWithString(str, params);
						}
					});
				//on quitte
				return;
				}
			}
		//si on est la alors pas besoin on remove
		this.resetSingleAutoComplete(params);

		}

	//----------------------------------------------------------------------------------------------------------------------*	
	this.init = function(arrInputs){
		this.debug('init(' + arrInputs + ')');	
		
		//on rajoute les autocomplete
		for(var o in arrInputs){
			this.addInputBox(arrInputs[o]);
			}
		
		
		}


	//----------------------------------------------------------------------------------------------------------------------*	
	this.triggerInputEvent = function(strInputName){
		this.debug('triggerInputEvent(' + strInputName + ')');
		var ev = $.Event('keyup');
		ev.which = 13; // <ENTER>
		//$('#' + strInputName).focus();
		$('#' + strInputName).trigger(ev);
		}


	//----------------------------------------------------------------------------------------------------------------------*	
	this.addInputBox = function(inputs){
		this.debug('addInputBox(' + inputs + ')');	
		/*
		DATA:
			.input:'input-main-client-search-autocomplete',
			.layer:'layer-client',
			.type:'client', 
			.position:'under',
			.module: //le select box des modules pour exercice seuelement
		*/
		//ajoute au array
		this.arrInputBox[inputs.input] = inputs;

		//all inputs keyup
		$('#' + this.arrInputBox[inputs.input].input).data('params', this.arrInputBox[inputs.input]);
		//selon le type client, serach, client-popup
		if(this.arrInputBox[inputs.input].type == 'client' || this.arrInputBox[inputs.input].type == 'client-popup'){
			$('#' + this.arrInputBox[inputs.input].input).keyup(function(e){
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					var oParams = $(this).data('params');
					oTmp.jautocomplete.fetchClientSearchAutoCompleteData(e, $(this).val(), oParams);
					}
				});


		}else if(this.arrInputBox[inputs.input].type == 'exercice'){	
			$('#' + this.arrInputBox[inputs.input].input).keyup(function(e){
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					var oParams = $(this).data('params');
					var idModule = $('#' + oParams.module).val();
					oTmp.jautocomplete.fetchExerciceSearchAutoCompleteData(e, $(this).val(), oParams, idModule);
					}
				});	

			}
	
		
		//le X dans la case de input pour reseter
		$('#' + this.arrInputBox[inputs.input].layer + ' .butt-clear-search[attached-input-id=' + this.arrInputBox[inputs.input].input + ']').data('params', this.arrInputBox[inputs.input]);	
		$('#' + this.arrInputBox[inputs.input].layer + ' .butt-clear-search[attached-input-id=' + this.arrInputBox[inputs.input].input + ']').click(function(e){
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var params = $(this).data('params');
				//efface le contenu de la boite
				oTmp.jautocomplete.resetSingleSearchInputBox(params);
				//on donne le focus a la boite
				$('#' + params.input).focus();
				}
			});


		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.rmInputBox = function(strInputName){
		this.debug('rmInputBox(' + strInputName + ')');	
		//event du butt
		$('#' + this.arrInputBox[strInputName].input).unbind();		
		//event du X reset
		$('#' + this.arrInputBox[strInputName].layer + ' .butt-clear-search[attached-input-id=' + this.arrInputBox[strInputName].input + ']').unbind();
		//
		delete(this.arrInputBox[strInputName]);

		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.resetMainAutoComplete = function(){
		this.debug('resetMainAutoComplete()');	
		//le auto complete
		for(var o in this.arrInputBox){
			$('#' + this.arrInputBox[o].layer + ' > #' + this.baseDivId).remove();
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*	
	this.resetSingleAutoComplete = function(params){
		this.debug('resetSingleAutoComplete(' + params + ')');	
		//le auto complete
		$('#' + params.layer + ' > #' + this.baseDivId).remove();
		}

	//----------------------------------------------------------------------------------------------------------------------*	
	this.resetSearchInputBox = function(){
		this.debug('resetSearchInputBox()');	
		
		for(var o in this.arrInputBox){
			//all inputs
			$('#' + this.arrInputBox[o].input).val('');
			}
		//le auto complete
		this.resetMainAutoComplete();
		}

	//----------------------------------------------------------------------------------------------------------------------*	
	this.resetSingleSearchInputBox = function(params){
		this.debug('resetSingleSearchInputBox(' + params + ')');	
		
		$('#' + params.input).val('');
		//le auto complete
		this.resetSingleAutoComplete(params);
		}

	//----------------------------------------------------------------------------------------------------------------------*	
	this.fillInputWithString = function(str, params){
		this.debug('fillInputWithString(' + str + ', ' + params + ')');	
		$('#' + params.input).val(str);

		
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.debug = function(str){
		jDebug.show(this.className + '::' + str);
		}



		
	}