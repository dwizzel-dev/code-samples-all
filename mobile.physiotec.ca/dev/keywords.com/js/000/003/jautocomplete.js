/*

Author: DwiZZel
Date: 15-07-2016
Version: V.1.0 BUILD 001
Notes: Desole pour la pauvre qualite du francais des commentaires
	
	1. 	ceci est un protype beaucoup d'amelioraion sont a faire 
		et a recoder pour de la vraie performance	
	
	2. 	RFS = Return From Server	

	3. mettre les lettres du premier search en bold dans le autocomplete pour le visuel

	4. faire un LI -> UL -> LI avec les premier exercise les plus populaires

	5. mettre le premier li en bold pour dire que c,est lui que l,on va prendre par defaut
	
	6.	garder les resultat du autocomplete en attendant les autres resultat, mais les effacer si on fait un serach ou un NO result
	au lieu de le fermer a chaque fois pour le flicker
	
	7. 	si this.bEnableRemovePreview alors on efface ou on fait seulement
		un display a none et on change le attr to
		0 = never loaded
		1 = loaded
		2 = loaded mais invisible
		comme ca on ne va pas chercher les preview pour rien

	8. 	on va avoir un type de retour aussi c'est a dire
		kwty	pe = 1 // un keyword
		kwty	pe = 2 // un short_title d'exercice
		kwty	pe = 3 // un code_exercice
		donc la presentaion ne sera pas la meme un montre lre permiers ressultat pour le keyword
		l'autre va montrer les exercice directement relie a celui-ci
			on peut les differencier en affichant un "kw" => keyword
			on peut les differencier en affichant un "ex" => short_title de exercice
			qui sera afficher a la droite des resultats
			
	9.  aussi mettre une case de choix qui pourra rechercher uniquement dans les 
		keyword ou les short_title ou les deux a la fois ce pourquoi n a besoin du "ex" et "kw"
		pour diffrenecier le type de resultat

	10.	dans un avenir on pourra mme cliquer directement sur un bouton dans 
		les resultat de preview exercice qui rajoutera l'exercice directement a partir de la recherche
		donc en un seul clique on peut rajouter l'exercice a partir des resultats de recherche
		dans le cas d'un "ex"	

	11.	si on ne trouve pas tout les debut de mot demande on ne lui met pas automatiquement
		le premier mot trouve et on lui laisse faire une recherche qui retourne ra un boite dans laquelle	on lui dira que le mot cle est envoye et qu'elle pourrait elle meme se creer un mot clee qu'elle pourra ratacher a un ou des exercise via une interface prevu pour ca
		
		EX.1: 
			"abdo flex wom sup"
			retournera en premier
			"ABDOminal FLEXion SUPine"
			ce mot contient 3 des 4 mots demande 
			alors on ne fill pas automatiquement la boite lors du <ENTER>
			- mais on lui met plutot une box en dessous qui dira
			le mot "abdo flex wom sup" a ete soumis a notre equipte qui se fera une plaisir
			d'etudier la possibilite de le rattacher a des resulat
			- et si vous voulez vous pouvez associer vous meme ce resultat a un mot cle deja existant ou a un exercise ou a un groupe d'exercice

		EX.2: 
			"abdo flex supin" ou "sup flex abdo" ou "flex sup abd"
			retournera
			"ABDominal FLEXion SUPINe"
			les 2 mots se retrouvant donc dans le premier resultat
			lors du <ENTER> la boite se rempolira automatiquement avec 
			"abdominal flexion supine"
			et on lui affiche les resultats correspondant

		EX.3: 
			"adom flex sup"
			la personne a fait une faute de frappe "adom" au lieu de "abdom" 
			alors on fait la meme chose que dans EX.1
			excepte evidement si elle choisit elle meme un des choix du autocomplete
				

	
*/
//----------------------------------------------------------------------------------------------------------------------
    
function JAutoComplete(args){
	//clas name
	this.className = 'JAutoComplete';
	//base div 
	this.baseDivId = 'main-autocomplete-result';
	//base input name for kwtype 
	this.baseKwTypeNameInput = 'kwtype';	
	//array of input box name and ref to jquery selector object
	this.arrInputBox = [];		
	//main appz soit la classe principale qui soccupe de tout
	this.mainAppz = args.mainappz;
	//le comm avec le serveur
	this.jcomm = args.jcomm;
	//les dernier resultat du autocomplete pour reproposer lors dun press <ENTER>
	this.arrLastHintResult = [];
	//have auto complete returned from server
	this.bHaveAutoCompleteResult = false;
	//si on a au moins un match dans le autocomplete result
	//cest a dire un debut de phrase car pour les permutation
	//on essaye de le corriger alors on a un resultat de autocomplete
	//mais si il ne selectionne rien dedans pas besoin de lancer la recherche
	//qu ne ramenera rien de toute facon
	this.bFoundAutoCompleteMatch = false;
	//le dernier mot avant le focus sur les li qui changeront le contenu du input box
	this.lastTypedWord = '';	
	//last typed string qui a retourne un resultat
	this.lastSearchString = '';	
	//le contenue word du permier LI	
	this.firstLiWord = '';	
	//la derniere string
	this.currentSearchWord = '';
	//le contenue values du LI sur lequel il clique ou press enter
	this.focusedKwIds = '';		
	//min-max
	this.minStrLen = 1;
	//le lastPid des requete Exercices
	this.lastAutoCompletePid = {
		exercice: 0
		};
	//le lastPID de la recherche de preview des exercise
	this.lastExercisePreviewPid	= 0;
	//enable preview exercise fetch
	this.bEnableExercisePreview = true;	
	//enable erase preview when up or down
	this.bEnableRemovePreview = true;	
	//enable mouse over keyword exercise preview
	this.bEnableMouseOver = false;	
	//le temps entre chaque call du fetchAutoCompleteDataWithDelay en millisecond google est a 130ms
	this.timeDelay = 100;
	//delai pour le preview	
	this.timePreviewDelay = 333;	
	//le timer du setTimeout poura ller fetcher le autocomplete data
	this.timerFetchAutoComplete = 0;	
	//timer pour le fetch des exercice preview
	this.timerFetchExercisePreview = 0;			
	//event.which we refused
	//les event poru lesquelles on ne fera rien genre : <SHIFT + LEFT_ARROW> etc...
	//http://docstore.mik.ua/orelly/webprog/DHTML_javascript/0596004672_jvdhtmlckbk-app-b.html		
	this.arrRefusedEvent = [
		//9,16,17,18,19,20,27,33,
		9,16,17,18,19,20,33,
		34,35,36,37,39,44,45,
		112,113,114,115,116,117,
		118,119,120,121,122,123,
		144,145
		];
	//kwtype pour les traduction de ttire LI
	this.arrKwType = {
		'1': 'keywords',
		'2': 'short title',
		//'3': 'code exercise',
		};
	//----------------------------------------------------------------------------------------------------------------------*	
	this.setLastTypedWord = function(str){
		this.debug('setLastTypedWord()', str);
		//on garde en memoire	
		this.lastTypedWord = str;
		//		
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.setCarretRange = function(params, start, end){
		this.debug('setCarretRange()', params, start, end);
		//on place le cursor
		params.refinput.prop('selectionStart', start);
		params.refinput.prop('selectionEnd', end);
		//		
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.getLastTypedWord = function(){
		this.debug('getLastTypedWord()');
		//	
		return this.lastTypedWord;	
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.setFirstLiWord = function(str){
		this.debug('setFirstLiWord()', str);
		//on garde en memoire	
		this.firstLiWord = str;
		//		
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.setLastSearchString = function(str){
		this.debug('setLastSearchString()', str);
		//on garde en memoire	
		this.lastSearchString = str;
		//		
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.getLastSearchString = function(){
		this.debug('getLastSearchString()');
		this.debug('LAST_SEARCH_STRING:' + this.lastSearchString);
		//on garde en memoire	
		return this.lastSearchString;
		//		
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.getFirstLiWord = function(){
		this.debug('getFirstLiWord()');
		//	
		return this.firstLiWord;	
		};

	//----------------------------------------------------------------------------------------------------------------------*
	this.setInputBoxText = function(oParams, str, bUpdateBg){
		this.debug('setInputBoxText()', oParams, str, bUpdateBg);
		//on reset le -bg
		if(bUpdateBg){
			this.setInputBgBoxText(oParams, '');
			}
		//le front
		oParams.refinput.val(str);
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.setInputBgBoxText = function(oParams, str){
		this.debug('setInputBgBoxText()', oParams, str);
		//il va falloir checker si le mot est compose et dans le bon sens aussi
		//sinon il ne faut pas remplir la case avec le hint
		//il va falloir faire les bold dans la liste de choix LI aussi
		//EX TAPE: 'a p', 'p a', 'p', 'a'
		//EX CHOIX: 'abdominal plank', 'plank abdominal' 		
		
		//juste le bg input
		oParams.refinputbg.val(str);
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.getCurrentWord = function(oParams){
		this.debug('getCurrentWord()', oParams);
		//juste le bg input
		//on y va selon la la cle keyword-exercise
		return this.currentSearchWord;
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.getInputBoxText = function(oParams){
		this.debug('getInputBoxText()', oParams);
		//juste le bg input
		//le front	
		return oParams.refinput.val();
		};	

	//----------------------------------------------------------------------------------------------------------------------*
	this.trimStringBeginning = function(str){
		this.debug('trimStringBeginning()', str);
		//
		return str.replace(/^\s+/gm, '');
		}

	this.trimStringEnding = function(str){
		this.debug('trimStringEnding()', str);
		//
		return str.replace(/\s+$/gm, '');
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.setFocusedKwIds = function(strKwIds, strWord){
		this.debug('setFocusedKwIds()', strKwIds, strWord);
		//on va setter le kwIDS equivalent au contenu du LI
		this.focusedKwIds = strKwIds;
		//on va setter la strig aussi de recherche pour affichage du retour
		this.currentSearchWord = strWord;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	this.getFocusedKwIds = function(){
		this.debug('getFocusedKwIds()');	
		//on va setter le kwIDS equivalent au contenu du LI
		return this.focusedKwIds;
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	/*
	1. sur le focus du LI 
		a) changer le style et le focus
		b) garder le mot qui a ete ecrit avant d'overwriter
		c) remplacer le input par le contenu du LI qui a le focus et placer le cursor a la fin
		
	2. quand descend si atteint le bas alors retourne a la case INPUT

	3. quand monte si atteint le haut alors retourne a la case INPUT	
	
	*/
	this.changeLiFocus = function(strDirection, oParams){
		this.debug('changeLiFocus()', strDirection, oParams);
		//
		//on stop le timer de fetch preview exercise
		clearTimeout(this.timerFetchExercisePreview);
		//on reset le pid du preview
		this.lastExercisePreviewPid = 0;
		//base ref du autocomplete
		var strUlListingRef = '#' + this.baseDivId + ' > UL.listing';
		//le id present qui a le focus	
		var iFocusId = parseInt($(strUlListingRef).attr('focus-id'));
		//si on veut faire disparaitre les precedent preview
		if(this.bEnableRemovePreview && iFocusId != 0){
			//remove le UL
			$('#lisr' + iFocusId + ' > UL.exercises').css({'display':'none'});
			//check si avait deja un preview de loader = 1
			//si oui setter le status a 2 pour eviter de 
			//reloader pour rien la prochaine fois
			if($('#lisr' + iFocusId).attr('preview-loaded') == '1'){	
				//set l'attribute a non loaded
				$('#lisr' + iFocusId).attr('preview-loaded', '2');
				}	
			}
		//garde le last id qui a ete modifie pour changer le style et le focus
		var iLastFocusId = iFocusId;
		//le max de id row
		var iMaxFocusId = parseInt($(strUlListingRef).attr('focus-id-max'));
		//on incremente le focus-id
		if(strDirection == 'up'){
			iFocusId--;
			//si jamais est le dernier on le remet au premier
			if(iFocusId <= 0){
				iFocusId = 0;
				}
		}else{ //down
			iFocusId++;
			//si jamais est le dernier on le remet au premier
			if(iFocusId > iMaxFocusId){
				iFocusId = 0;
				}
			}
		//on enleve le focus et surlignage
		$('#lisr' + iLastFocusId).removeClass('focus');
		//si le lastInputId est a zero alors on doit garder ce qui etait ecrit
		if(iLastFocusId === 0){
			this.setLastTypedWord($('#' + oParams.input).val());
		}else{
			if(iFocusId === 0){	
				//on doit remettre ce que l'on a garde en memoire 
				//car revient sur le input box apres etre passe d'un LI a l'autre 
				this.setInputBoxText(oParams, this.getLastTypedWord(), true);
				//on remet le bg au permier choix du LI
				this.setInputBgBoxText(oParams, this.getFirstLiWord());
				}
			}
		//si le focus est 0 
		if(iFocusId === 0){	
			//si le focus est a 0 et le direction est up donc on doit aller au dernier choix des LI
			if(strDirection == 'up'){
				//si est le premier de la liste et reviens au input box
				if(iLastFocusId === 1){
					//on set l'attribut
					$(strUlListingRef).attr('focus-id', 0);
					//on set les KWids focused
					//this.setFocusedKwIds($('#lisr1').attr('keyword-ids'), $('#lisr1').attr('keyword-word'));
					//on met plus rien car peut vouloir faire une recherche 
					//juste avec les input box
					this.setFocusedKwIds('', '');	
				}else{
					//on set sur le dernier LI
					$('#lisr' + iMaxFocusId).addClass('focus');	
					//on change le input box avec le contenu du LI
					//this.setInputBoxText(oParams, $('#lisr' + iMaxFocusId).text(), true);
					this.setInputBoxText(oParams, $('#lisr' + iMaxFocusId).attr('keyword-word'), true);
					//on set les KWids focused
					this.setFocusedKwIds($('#lisr' + iMaxFocusId).attr('keyword-ids'), $('#lisr' + iMaxFocusId).attr('keyword-word'));
					//on set l'attribut
					$(strUlListingRef).attr('focus-id', iMaxFocusId);
					//on va loader un preview timer si reste longtemps dessus
					this.timerFetchExercisePreview = setTimeout(this.fetchExercisePreviewWithDelay.bind(this, oParams, this.getFocusedKwIds(), iMaxFocusId), this.timePreviewDelay);
					}
			}else{ 	//alors on descnd et on est rendu au dernier choix
				//met le focus sur le input box
				//$('#' + oParams.input).focus();
				//on set l'attribut
				$(strUlListingRef).attr('focus-id', 0);
				//on set les KWids focused
				//this.setFocusedKwIds($('#lisr1').attr('keyword-ids'), $('#lisr1').attr('keyword-word'));
				//on met plus rien car peut vouloir faire une recherche 
				//juste avec les input box
				this.setFocusedKwIds('', '');
				}
		}else{
			//on set le focus au LI
			$('#lisr' + iFocusId).addClass('focus');
			//on change le input box avec le contenu du LI
			//this.setInputBoxText(oParams, $('#lisr' + iFocusId).text(), true);
			this.setInputBoxText(oParams, $('#lisr' + iFocusId).attr('keyword-word'), true);
			//on set les KWids focused
			this.setFocusedKwIds($('#lisr' + iFocusId).attr('keyword-ids'), $('#lisr' + iFocusId).attr('keyword-word'));
			//on set l'attribut
			$(strUlListingRef).attr('focus-id', iFocusId);
			//on va loader un preview timer si reste longtemps dessus
			this.timerFetchExercisePreview = setTimeout(this.fetchExercisePreviewWithDelay.bind(this, oParams, this.getFocusedKwIds(), iFocusId), this.timePreviewDelay);
			}
		//
		};


	//----------------------------------------------------------------------------------------------------------------------*	
	this.fetchAutoCompleteDataWithDelay = function(str, params){
		this.debug('fetchAutoCompleteDataWithDelay()', str, params);
		//la derniere string que l,on a chercher 
		//pour eviter de recommencer la recherche
		//on va stripper les multiple space en un seul
		//var le serveur le fait pour les retours
		//si on trouve des multiples espaces les remplacer	
		/*
		if(str.match(/\s+/g)){
			str = str.replace(/[\s]+/g, ' ');	
			//on change la value dans le input box
			this.setInputBoxText(params, str, false);		
			}
		*/
		//on va aller chercher les case de kwtype qu'il a coche qui se limit a trois
		//la string des case coche
		var strKwType = '';
		//lop dans nos 3 choix
		for(var i=0; i<3; i++){
			//ref de jquery object	
			var refCheckBox = $('#' + this.baseKwTypeNameInput + i);
			if(refCheckBox.is(':checked')){
				strKwType +=  refCheckBox.val() + ',';
				}
			}
		//minor check
		if(strKwType != ''){	
			//on strip la last virgule	
			strKwType = strKwType.substr(0, (strKwType.length - 1));
		}else{
			//value by default is keyword only
			strKwType = '1';
			}
		//on flag comme quoi on n'a pas de resultat encore du autocomplete
		this.bHaveAutoCompleteResult = false;
		//on flag comme quoi on na pas de match encore
		this.bFoundAutoCompleteMatch = false;
		//on rest le array des dernier resultat du autocomplete pour les hint
		this.arrLastHintResult = [];
		//pour le meme resultat
		this.setLastSearchString(str);
		//on envoi la requete au serveur
		this.lastAutoCompletePid.exercice = this.mainAppz.jsearch.fetchAutoCompleteData(str, params, strKwType);
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.fetchAutoCompleteData = function(evnt, str, params){
		this.debug('fetchAutoCompleteData()', evnt,  str, params);
		//on arrete le timer si il y en avait un car la requete est nouvelle et l,autre n'est plus valide
		clearTimeout(this.timerFetchAutoComplete);
		//on stop le timer de fetch preview exercise car plus valide
		clearTimeout(this.timerFetchExercisePreview);
		//on reset le pid du preview
		this.lastExercisePreviewPid = 0;
		//
		this.debug('EVENT:' + evnt.which);
		//les event poru lesquelle on ne fera rien genre : <SHIFT + LEFT_ARROW> etc...
		if(this.arrRefusedEvent.indexOf(evnt.which) !== -1){
			//on sort
			return;
			}
		//si c'est le <esc> on ferme le autocomplete
		if(evnt.which == '27'){ 
			this.hideAutoComplete();
			return;
			}	
		//on va stripper les multiple space en un seul
		//var le serveur le fait pour les retours
		//si on trouve des multiples espaces les remplacer	
		//BUG creer un fuck sur le carret
		/*
		if(str.match(/\s+/g)){
			str = str.replace(/[\s]+/g, ' ');	
			//on change la value dans le input box
			this.setInputBoxText(params, str, false);		
			}
		*/
		//si le premier chars est un space alors on recule le input de 1
		if(str.charAt(0) === ' '){
			//on recule et no change le input box
			this.setInputBoxText(params, this.trimStringBeginning(str), false);
			//on place le cursor au debut
			this.setCarretRange(params, 0, 0);
			//on sort pas vraiment besoin car doit faire la recherche avec ce qui reste
			//return;
			}
		//on trim les avant et apres spaces
		//this.debug('STRING:"' + str + '"');	
		str = str.trim();
		//this.debug('STRING.TRIMMED:"' + str + '"');		
		//si c'est un <SPACE> avant une lettre alors ne sera pas bon car le input-bg va etre deplace
		//alors on remet le cursor au debut et on efface le input et input-bg
		if(evnt.which == '32'){ 
			//check si vide alors on retourne au debut de la string
			if(str == ''){
				this.setInputBoxText(params, '', true);
				return;
				}
			}
		
		//les autres chars
		if(evnt.which != '13'){ 
			//dans le cas un c'est un debut avec char mais pas autre char apres ex: "ab " et que l,on a deja chercher pour "ab" alors on annule le search ou si c,est la meme recherche avec un backspace sur des espace <space>
			if(str == this.getLastSearchString() && (evnt.which != '38' && evnt.which != '40')){
				return;
				}	
			//on reset le -bg
			this.setInputBgBoxText(params, '');
			//on check pour les <ARROW> et autres touche
			if(evnt.which == '38'){ 
				//GO UP
				this.changeLiFocus('up', params);
			}else if(evnt.which == '40'){ 
				//GO DOWN
				this.changeLiFocus('down', params);
			}else{
				//enleve le autocomplete en dessous car la string n,est plus la meme
				//on va eviter le flickering de quand il la reconstruit
				//sinon on va la remetre	
				//this.resetSingleAutoComplete(params);	
				//	
				//a chaque chars qu'il tape on va mettre le premier choix dans le input du BG
				//si pas vide faire la recherche
				if(str != '' && str.length >= this.minStrLen){
					//mettre en minuscule
					str = this.mainAppz.jutils.toLower(str);
					//on va fetcher le data
					//mais on va laisser un delai car peu taper tres vite (comme yves haha!) et 
					//ca ne sert a rien d'aller chercher tout si il rajoute d'autre truc apres
					//on start un autre timer avec la nouvelle requete
					this.timerFetchAutoComplete = setTimeout(this.fetchAutoCompleteDataWithDelay.bind(this, str, params), this.timeDelay);
				}else{
					//on enle le result vu que lon a plus rien a chercher
					this.resetSingleAutoComplete(params);		
					//on reset le last search car le autocomplete est disparue et si retappe la meme recherche
					//il n'ira pas la fetcher
					this.setLastSearchString('');
					//on reset le pid car peut arriver qu'il soit long vu
					//EX: "ab" -> abduction
					// tape vite 2 backspace
					// 1. il va chercher le "a" -> retourne abduction
					// 2. la case de input est redu vide avec les 2 backspace
					// 3. le retour du "a" arrive, mais le input est vide alors propose un choix pas rapport
					this.resetPid();
					}
				}
			
		}else{ //press <ENTER>
			//pas etre vide
			if(str != '' && str.length >= this.minStrLen){
				//le bloolean du fetch
				var bFetch = true;
				//check si vide
				if(this.getFocusedKwIds() == ''){
					bFetch = false;
				}else{
					//on change le input box avec le contenu de recherche word
					//car peut-etre que les KwId sont setter mais pas le current word	
					this.setInputBoxText(params, this.getCurrentWord(params), true);
					}
				//est-ce que l'on fait une recherche texte
				//CAS 1.lusager a un autocomplete result
				//		mais veux rechercher les mot qu'il a tape quand meme
				//		sans faire de choix dans le autocomplete
				//CAS 2.lusager na aucun result dans le autocomplete
				//		mais veux quand meme lancer sa recherche avec 
				//		les mots tapes, mais on sait en avance quil naura 
				//		aucun retour, car le autocomplete tiens ses resultats
				//		des titres et keywords, si na rien trouver, il ne trouvera
				//		pas plus, alors on lui dit que ce quil a tape est soumis
				//		a notre equipe qui fera un analyse des mots tapes
				//		a savoir si il le rajouteront ou si cetait
				//		est une erreur de frapper
				//CAS 3.lusager lance la recherche avec le mot propose directement
				//		dans sa case de input (le pluis rapide et facile )
				//fetch le listing d'exercice en rapport avec les keyword ids
				//si il y en avait
				if(bFetch){
					if(this.mainAppz.jsearch.getExerciceListingByKeywordIds(this.getFocusedKwIds(), this.getCurrentWord(params))){
						//hide the form
						this.mainAppz.resetSearchWindowResult(this.getCurrentWord(params));
						//on eneleve le autocomplete car on a quelque chose a chercher
						this.resetSingleAutoComplete(params);
						//
						this.setLastSearchString(this.getCurrentWord(params));	
						}
				}else{
					//il ne veut rien savoir des mots du autocomplete
					//alors on lance une recherche text au serveur 
					//au lieu de lui balancer des kwids
					if(this.bHaveAutoCompleteResult){
						//si on a trouve aucun match dans le autcomplete
						//cest que on lui a proposer des  choix permuter
						//pour corriger ses fautes de frappe
						//si il envoit la recherche elle ne trouvera rien
						//vu que lon cherche deja dans les title et keywords
						//alors on lui lance le msg de submit		
						if(this.bFoundAutoCompleteMatch){
							this.debug('RECHERCHE TEXTE: ' + this.getLastSearchString());
							if(this.mainAppz.jsearch.getExerciceListingByWords(this.getLastSearchString())){
								//hide the form
								this.mainAppz.resetSearchWindowResult(this.getLastSearchString());
								//on eneleve le autocomplete car on a quelque chose a chercher
								this.resetSingleAutoComplete(params);
								}	
						}else{
							//on lui dit que son mot est soumis a notre equipe	
							this.sendingWordToOurTeamForValidation(params);
							}
					}else{
						//si on navait aucun retour dans le autocomplete alors 
						//on lui dit que son mot est soumis a notre equipe	
						this.sendingWordToOurTeamForValidation(params);
						}
				
					}
				//on reset le last search car le autocomplete est disparue et si retappe la meme recherche
				//il n'ira pas la fetcher
				//this.setLastSearchString('');
				//reset le pid car la case est vide alors meme si il y a un retour ce n'est pas le bon
				this.resetPid();
				}
			}
		};

	
	//----------------------------------------------------------------------------------------------------------------------*
	/*
	PARAMS:
		.input:'input-search',
		.layer:'main-input',
		.type:'exercise', 
		.position:'under',
		
	*/
	this.fetchAutoCompleteDataRFS = function(obj, params, word, kwtype, cleanword, pid){
		this.debug('fetchAutoCompleteDataRFS()', obj, params, word, kwtype, cleanword, pid);
		// obj = le array de retour avec les mots
		// params = l'object input
		// word = le last typed word	
		// pid le last process id de la recherche de autocomplete

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
		if(bContinue && params.refinput.is(':focus')){
			//num rows, cest a dire le nombre de kwtype au moins 1 au minimum
			var iNumKwType = Object.keys(obj).length;
			//on va voir si on a plus que un kwtype sinon on affiche normal
			//sans de titre en plus
			var iNumRows = 0;
			if(iNumKwType == 1){
				//alors on deoit trouver le premier kw unique 
				//car pourrait etre envoye 1,2,3
				//mais uniquement 2 pourrait avoir des retours
				for(var o in obj){
					obj = obj[o];	
					break;
					}
				iNumRows = Object.keys(obj).length;	
				//va continuer plus loin dans le code
			}else if(iNumKwType > 1){
				//on va passer a une fonctione pour gerer tout ca pour l'instant le temps que 
				//le prototype soit accepte
				this.fetchAutoCompleteDataRFS_extend(obj, params, word, kwtype, cleanword);
				//la finction va prendre en charge le reste
				return;
				}
			//si a un resultat
			if(iNumRows > 0){
				this.bHaveAutoCompleteResult = true;
				//si on doit reset le bg hint, etc...
				var bResetHint = false;
				//on garde le premier choix que l,on va proposer dans le input-bg en gris
				if(typeof(obj[0].name) == 'string'){
					// si le debut du mot correspond
					if(obj[0].name.indexOf(word) === 0){
						//le premier LI	
						//si on le garde cest ce qui sera lance
						this.setFirstLiWord(obj[0].name);
						//set le input-bg
						this.setInputBgBoxText(params, this.getFirstLiWord());
						//on set les KWids focused
						//si on le garde cest ce qui sera lance
						//this.setFocusedKwIds(obj[0].id, obj[0].name);
					}else{
						bResetHint = true;
						}
				}else{
					bResetHint = true;
					}
				var arrWords = cleanword.split(' ');
				if(typeof(arrWords) != 'object'){
					arrWords = [];
					}
				//get la position du serach box
				var iCmpt = 1;
				//var bHint = false;
				var data = '<UL class="listing" focus-id="0" focus-id-max="' + iNumRows + '">';
				var bFoundMatch = false;
				//loop data
				for(var o in obj){
					if(typeof(obj[o].name) == 'string' && (typeof(obj[o].id) == 'string' || typeof(obj[o].id) == 'number')){
						//on garde des resultat pour des hint dans la 
						//proposition a lusager lors de aucun result
						this.arrLastHintResult.push(obj[o].name);
						//change to blue hint if word substr is found in the text
						var strLiText = obj[o].name;
						//var arrNameWords = obj[o].name.split(' ');
						var strMatch = '';
						for(var p in arrWords){
							strMatch += '^' + arrWords[p] + '|[ ]{1}' + arrWords[p] + '|'; 
							}
						//strip last pipe
						if(strMatch != ''){
							strMatch = strMatch.substr(0, (strMatch.length - 1));
							strLiText = strLiText.replace(new RegExp(strMatch, 'gi'), function(m){
								bFoundMatch = true;
								return '<span class="hint">' + m + '</span>';
								});
							}
						//Le <LI>
						data += '<LI keyword-ids="' + obj[o].id + '" keyword-word="' + obj[o].name + '" keyword-type ="' + kwtype + '" class="single-result" preview-loaded="0" li-pos="' + iCmpt + '" id="lisr' + iCmpt + '"><DIV class="mclick" keyword-ids="' + obj[o].id + '" keyword-word="' + obj[o].name + '" keyword-type ="' + kwtype + '" li-pos="' + iCmpt + '">' + strLiText + '</DIV></LI>'; //0=id, 1=name
						//increment
						iCmpt++;
						}
					}
				data += '</UL>';
				//si ion ne trouve pas de hint
				if(bResetHint){
					//set le input-bg
					this.setInputBgBoxText(params, '');
					//le premier LI	
					this.setFirstLiWord('');
					//on set les KWids focused
					this.setFocusedKwIds('', '');
					}
				//flasg de match pour quand il appui enter quand meme
				//car peut etre un permutation de lettre 
				if(bFoundMatch){
					this.bFoundAutoCompleteMatch = true;	
					}
				//on ajoute le data
				$('#' + this.baseDivId).html(data);
				//on show	
				$('#' + this.baseDivId).css({'display':'block'});	
				//on keep du data
				$('#' + this.baseDivId + ' .single-result > .mclick').data('params', params);
				//si enabled
				if(this.bEnableMouseOver){
					//le mouse over du li pour ouvrir exercice
					$('#' + this.baseDivId + ' .single-result > .mclick').mouseover(function(e){
						//get parent class
						var oTmp = $(document).data('jappzclass');
						if(typeof(oTmp) == 'object'){
							//on stop le timer de fetch preview exercise
							clearTimeout(oTmp.jautocomplete.timerFetchExercisePreview);
							//on reset le pid du preview
							oTmp.jautocomplete.lastExercisePreviewPid = 0;
							//on set les KWids focused
							oTmp.jautocomplete.setFocusedKwIds($(this).attr('keyword-ids'), $(this).attr('keyword-word'));
							//on va loader un preview timer si reste longtemps dessus
							oTmp.jautocomplete.timerFetchExercisePreview = setTimeout(oTmp.jautocomplete.fetchExercisePreviewWithDelay.bind(oTmp.jautocomplete, $(this).data('params'), oTmp.jautocomplete.getFocusedKwIds(), $(this).attr('li-pos')), oTmp.jautocomplete.timePreviewDelay);
							}
						});
					//le mouse out
					$('#' + this.baseDivId + ' .single-result > .mclick').mouseout(function(e){
						//get parent class
						var oTmp = $(document).data('jappzclass');
						if(typeof(oTmp) == 'object'){
							//on stop le timer de fetch preview exercise
							clearTimeout(oTmp.jautocomplete.timerFetchExercisePreview);
							//on reset le pid du preview
							oTmp.jautocomplete.lastExercisePreviewPid = 0;
							//le focus one
							var iFocusId = $(this).attr('li-pos');
							//remove le UL
							$('#lisr' + iFocusId + ' > UL.exercises').css({'display':'none'});
							//check si avait deja un preview de loader = 1
							//si oui setter le status a 2 pour eviter de 
							//reloader pour rien la prochaine fois
							if($('#lisr' + iFocusId).attr('preview-loaded') == '1'){	
								//set l'attribute a non loaded
								$('#lisr' + iFocusId).attr('preview-loaded', '2');
								}	
							}
						});
					}		
				//on met un listener pour le click sur les resultats		
				$('#' + this.baseDivId + ' .single-result > .mclick').click(function(e){
					e.preventDefault();
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						var keywordIds = $(this).attr('keyword-ids');
						var keywordWord = $(this).attr('keyword-word');
						var params = $(this).data('params');
						oTmp.debug(params.input + ' -> Clicked');
						//set le input  et nput-bg
						oTmp.jautocomplete.setInputBoxText(params, keywordWord, true);
						//set le focus sur input
						params.refinput.focus();
						//set les keywords ids focused
						oTmp.jautocomplete.setFocusedKwIds(keywordIds, keywordWord);
						//fetch le listing d'exercice en rapport avec les keyword ids
						if(oTmp.jsearch.getExerciceListingByKeywordIds(oTmp.jautocomplete.getFocusedKwIds(), oTmp.jautocomplete.getCurrentWord(params))){
							//hide the form
							oTmp.resetSearchWindowResult(oTmp.jautocomplete.getCurrentWord(params));
							//
							oTmp.jautocomplete.setLastSearchString(oTmp.jautocomplete.getCurrentWord(params));		
							}
						//on eneleve le autocomplete
						oTmp.jautocomplete.resetSingleAutoComplete(params);
						}
					});
				//on quitte
				return;
			}else{
				//pas de resultat alors on enleve le li et les focused kw ids
				this.setFirstLiWord('');
				this.setFocusedKwIds('', '');
				//on nettoie le input box bg
				this.setInputBgBoxText(params, '');
				//on dit que lon a rien
				var data = '<UL class="listing" focus-id="0" focus-id-max="0"><LI class="single-result-title">' + jLang.t('no result') + '</LI></UL>'; 
				//on ajoute le data
				$('#' + this.baseDivId).html(data);
				//on show	
				$('#' + this.baseDivId).css({'display':'block'});
				//on sen va byebye!
				return;
				}
			}
		//si on est la alors pas besoin on remove
		this.resetSingleAutoComplete(params); 
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	//ceci est juste un extend en attendant que ce soit accepte pour optimisation
	//cette fonction va y aller per titre de kwtype
	//et contient automatiquement plus de 1 kwtype	
	
	this.fetchAutoCompleteDataRFS_extend = function(obj, params, word, kwtype, cleanword){
		this.debug('fetchAutoCompleteDataRFS_extend()', obj, params, word, kwtype, cleanword);
		// obj = le array de retour avec les mots
		// params = l'object input
		// word = le last typed word	
		//on show ou pas le autocomplete sulement si on a au moins un resultat
		var bShowAutocomplete = false;
		//si on doit reset le bg hint, etc...
		var bResetHint = true;
		//le word plitter pour trouver le reste
		var arrWords = cleanword.split(' ');
		if(typeof(arrWords) != 'object'){
			arrWords = [];
			}
		//pour la position du search box
		var iCmpt = 1;
		//le total de roes
		var iNumTotalRows = 0; 
		//le contenu a l'interieur du UL listing
		var strDataContent = ''; 
		//on loop dans les kwtype
		for(var kw in obj){	
			//les numrow dun kwtype
			var iNumRows = Object.keys(obj[kw]).length;
			//si a un resultat
			if(iNumRows > 0){
				//total de rows en tout
				iNumTotalRows += iNumRows;
				//on a au moins un resultat a afficher
				bShowAutocomplete = true;
				//on garde le premier choix que l,on va proposer dans le input-bg en gris
				//si on en a pas deja trouve un
				if(bResetHint && typeof(obj[kw][0].name) == 'string'){
					// si le debut du mot correspond
					if(obj[kw][0].name.indexOf(word) === 0){
						//le premier LI	
						this.setFirstLiWord(obj[kw][0].name);
						//set le input-bg
						this.setInputBgBoxText(params, this.getFirstLiWord());
						//on set les KWids focused
						this.setFocusedKwIds(obj[kw][0].id, obj[kw][0].name);
						//on ne reset plus
						bResetHint = false;
						}
					}
				//on ajoute le titre du type de recherche
				strDataContent += '<LI class="single-result-title">' + jLang.t(this.arrKwType[kw]) + '</LI>'; //0=id, 1=name
				//loop data pour les li
				for(var o in obj[kw]){
					if(typeof(obj[kw][o].name) == 'string' && (typeof(obj[kw][o].id) == 'string' || typeof(obj[kw][o].id) == 'number')){
						//change to blue hint if word substr is found in the text
						var strLiText = obj[kw][o].name;
						var strMatch = '';
						for(var p in arrWords){
							strMatch += '^' + arrWords[p] + '|[ ]{1}' + arrWords[p] + '|'; 
							}
						//strip last pipe
						if(strMatch != ''){
							strMatch = strMatch.substr(0, (strMatch.length - 1));
							strLiText = strLiText.replace(new RegExp(strMatch, 'gi'), function(m){
								return '<span class="hint">' + m + '</span>';
								});
							}
						//Le <LI> avec les mots
						strDataContent += '<LI keyword-ids="' + obj[kw][o].id + '" keyword-word="' + obj[kw][o].name + '" keyword-type ="' + kw + '" class="single-result" preview-loaded="0" id="lisr' + iCmpt + '"><DIV class="mclick" keyword-ids="' + obj[kw][o].id + '" keyword-word="' + obj[kw][o].name + '" keyword-type ="' + kw + '">' + strLiText + '</DIV></LI>'; //0=id, 1=name
						//increment
						iCmpt++;
						}
					}
				}
			}
		//si on doit reset le bg hint, etc...
		if(bResetHint){
			//set le input-bg
			this.setInputBgBoxText(params, '');
			//le premier LI	
			this.setFirstLiWord('');
			//on set les KWids focused
			this.setFocusedKwIds('', '');
			}
		//si on a au moins un resultat d'un kwtype
		if(bShowAutocomplete){
			//result
			this.bHaveAutoCompleteResult = true;
			//get la position du serach box
			var strData = '<UL class="listing" focus-id="0" focus-id-max="' + iNumTotalRows + '">' + strDataContent + '</UL>';
			//on ajoute le data
			$('#' + this.baseDivId).html(strData);
			//on show	
			$('#' + this.baseDivId).css({'display':'block'});	
			//on met un listener pour le click sur les resultats
			$('#' + this.baseDivId + ' .single-result > .mclick').data('params', params);
			$('#' + this.baseDivId + ' .single-result > .mclick').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					var keywordIds = $(this).attr('keyword-ids');
					var keywordWord = $(this).attr('keyword-word');
					var params = $(this).data('params');
					oTmp.debug(params.input + ' -> Clicked');
					//set le input  et nput-bg
					oTmp.jautocomplete.setInputBoxText(params, keywordWord, true);
					//set le focus sur input
					params.refinput.focus();
					//set les keywords ids focused
					oTmp.jautocomplete.setFocusedKwIds(keywordIds, keywordWord);
					//fetch le listing d'exercice en rapport avec les keyword ids
					if(oTmp.jsearch.getExerciceListingByKeywordIds(oTmp.jautocomplete.getFocusedKwIds(), oTmp.jautocomplete.getCurrentWord(params))){
						//hide the form
						oTmp.resetSearchWindowResult(oTmp.jautocomplete.getCurrentWord(params));
						//
						oTmp.jautocomplete.setLastSearchString(oTmp.jautocomplete.getCurrentWord(params));		
						}
					//on eneleve le autocomplete
					oTmp.jautocomplete.resetSingleAutoComplete(params);
					}
				});
			//on quitte
			return;
		}else{
			//pas de resultat alors on enleve le li et les focused kw ids
			this.setFirstLiWord('');
			this.setFocusedKwIds('', '');
			//on dit que lon a rien
			var data = '<UL class="listing" focus-id="0" focus-id-max="0"><LI class="single-result-title">' + jLang.t('no result') + '</LI></UL>'; 
			//on ajoute le data
			$('#' + this.baseDivId).html(data);
			//on show	
			$('#' + this.baseDivId).css({'display':'block'});
			//on sen va byebye!
			return;
			}
		};

	
	//----------------------------------------------------------------------------------------------------------------------*	
	this.init = function(arrInputs){
		this.debug('init()', arrInputs);	
		//on rajoute les autocomplete
		for(var o in arrInputs){
			this.addInputBox(arrInputs[o]);
			}
		};


	//----------------------------------------------------------------------------------------------------------------------*	
	this.triggerInputEvent = function(strInputName){
		this.debug('triggerInputEvent()', strInputName);
		var ev = $.Event('keyup');
		ev.which = 13; // <ENTER>
		$('#' + strInputName).trigger(ev);
		};

	
	//----------------------------------------------------------------------------------------------------------------------*	
	this.fetchExercisePreviewWithDelay = function(params, strKwIds, strLiId){
		this.debug('fetchExercisePreviewWithDelay()', params, strKwIds, strLiId);
		//si enabled
		if(this.bEnableExercisePreview){
			//check si les exercice etait deja loade
			var strLoaded = $('#lisr' + strLiId).attr('preview-loaded');	
			//on envoi la requete au serveur pour aller chercher le data
			if(strLoaded == '0'){
				this.lastExercisePreviewPid = this.mainAppz.jsearch.getExerciceListingByKeywordIdsForPreview(params, strKwIds, strLiId);
			}else if(strLoaded == '2'){
				//cest que lon a deja le data alors 
				//il faut juste le faire apparaitre de nouveau
				$('#lisr' + strLiId + ' > UL.exercises').css({'display':'block'});
				//on remet le statut du preview a 1		
				$('#lisr' + strLiId).attr('preview-loaded', '1');	
				}
			}
		};


	//----------------------------------------------------------------------------------------------------------------------*	
	this.fetchExercisePreviewRFS = function(obj, params, strLiId, pid){
		this.debug('fetchExercisePreviewRFS()', obj, params, strLiId, pid);
		//
		//check si le pid est le dernier, car certain resultat arrive plus tard que la derniere demande
		if(this.lastExercisePreviewPid != pid){
			//rien a faire
			this.debug('REJECT(' + this.lastExercisePreviewPid + ') != ' + pid);
			return;
			}
		//si le preview est deja loadeau cas ou il retourne dessus
		var strLoaded = $('#lisr' + strLiId).attr('preview-loaded');
		//pas loader alors on y va avec les result
		var oData = obj.data;
		//le nom des filters	
		var oFilters = obj.filters;	
		if(strLoaded == '0'){
			//on continue
			var str = '<UL class="exercises">';
			for(var o in oData){
				//le LI
				str += '<LI class="single-exercises">';	
				//le image container	
				str += '<DIV class="img-container">';
				if(typeof(oData[o].thumb) == 'string'){
					if(oData[o].thumb != ''){
						str += '<img src="' + gExerciceImagePath + oData[o].thumb + '">'
						}	
					}
				str += '</DIV>';		
				//le titre
				str += '<DIV class="text-container">';	
				if(oData[o].shortTitle == ''){
					str += oData[o].codeExercise;
				}else{
					str += oData[o].shortTitle;
					}
				//les filters si il y a
				var strFilter = '';
				if(typeof(oData[o].filter) == 'object'){
					for(var p in oData[o].filter){
						strFilter += oFilters[oData[o].filter[p]] + ', ';
						}
					}
				//le long title
				str += '<SPAN>';
				if(oData[o].title != ''){
					str += '<BR />' + oData[o].title;	
					}
				//les filteers si il y a
				if(strFilter != ''){
					str += '<BR /><i>(' + strFilter.substr(0, (strFilter.length - 2)) + ')</i>';
					}
				str += '</SPAN>';			
				str += '</DIV>';			
				str += '</LI>';		
				}
			//affiche combien il en reste
			str += '<LI class="single-exercises maxcount">';
			str += '<DIV class="text-container">';		
			str += jLang.t('total') + obj.maxcount;
			str += '</DIV>';		
			str += '</LI>';			
			//ferme le UL	
			str += '</UL>';
			//show result
			$('#lisr' + strLiId).append(str);
			//on change le status
			$('#lisr' + strLiId).attr('preview-loaded', 1);
			}
		return;
		};


	//----------------------------------------------------------------------------------------------------------------------*
	/*
	DATA:
		.input:'input-search',
		.layer:'main-input',
		.type:'exercise', 
		.position:'under',
		
		// les deux plus bas vont etre declare plus loin et 
		//serviront de jquery selector pour aller plus vite 
		//au lieu de les chercher a chaque fois
		.refinput 	
		.refinputbg
		
		//le container du autocompete result
		.refresult
		
	*/
	this.addInputBox = function(inputs){
		this.debug('addInputBox()', inputs);	
		//ajoute au array
		this.arrInputBox[inputs.input] = inputs;
		//str html
		//le container des inputs
		var str = '<div id="main-input-div">';
		//div container box for positionning
		//le input en bg aura toujours le meme nom avec "-bg" en plus
		str += '<div class="input"><input name="' + inputs.input + '-bg" id="' + inputs.input + '-bg" type="text" disabled autocomplete="off" maxlength="256" spellcheck="false"></div>';
		//le input principal
		str += '<div class="input"><input name="' + inputs.input + '" id="' + inputs.input + '" class="translucide" type="text" autocomplete="off" maxlength="256" spellcheck="false"></div>';
		//le auto complete
		str += '<div id="' + this.baseDivId + '"></div>';	
		//ferme le div container	
		str += '</div>';
		//on va creer le input box dans le laqyer desire
		$('#' + inputs.layer).html(str);
		//on va setter le focus dessus
		$('#' + inputs.input).focus();	
		//le ref du onject jqeury selector pour eviter de reparcourrir a chaque fois
		this.arrInputBox[inputs.input].refresult = $('#main-input-div');	
		this.arrInputBox[inputs.input].refinput = $('#' + inputs.input);
		this.arrInputBox[inputs.input].refinputbg = $('#' + inputs.input + '-bg');
		//all inputs keyup
		this.arrInputBox[inputs.input].refinput.data(
			'params', 
			this.arrInputBox[inputs.input]
			);
		//selon le type serach
		this.arrInputBox[inputs.input].refinput.keyup(function(e){
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var oParams = $(this).data('params');
				oTmp.jautocomplete.fetchAutoCompleteData(e, $(this).val(), oParams);
				}
			});
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.rmInputBox = function(strInputName){
		this.debug('rmInputBox()', strInputName);	
		//event du butt
		this.arrInputBox[strInputName].refinput.unbind();		
		this.arrInputBox[strInputName].refinputbg.unbind();		
		//
		delete(this.arrInputBox[strInputName]);
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.resetMainAutoComplete = function(){
		this.debug('resetMainAutoComplete()');	
		//le auto complete
		for(var o in this.arrInputBox){
			$('#' + this.baseDivId).css({'display':'none'});
			$('#' + this.baseDivId).text('');	
			}
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.resetSingleAutoComplete = function(params){
		this.debug('resetSingleAutoComplete()', params);	
		//le auto complete
		$('#' + this.baseDivId).css({'display':'none'});
		$('#' + this.baseDivId).text('');	
		//
		this.setFocusedKwIds('', '');
		};
		
	//----------------------------------------------------------------------------------------------------------------------*	
	this.hideAutoComplete = function(){
		this.debug('hideAutoComplete()');	
		//le auto complete
		$('#' + this.baseDivId).css({'display':'none'});
		};	

	//----------------------------------------------------------------------------------------------------------------------*	
	this.resetSearchInputBox = function(){
		this.debug('resetSearchInputBox()');	
		
		for(var o in this.arrInputBox){
			//all inputs
			this.setInputBgBoxText(this.arrInputBox[o], '');
			//
			this.setFocusedKwIds('', '');
			//
			this.setInputBoxText(this.arrInputBox[o], '', true);
			}
		//le auto complete
		this.resetMainAutoComplete();
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.resetSingleSearchInputBox = function(params){
		this.debug('resetSingleSearchInputBox()', params);	
		//
		this.setInputBgBoxText(params, '');
		//
		this.setFocusedKwIds('', '');
		//
		this.setInputBoxText(params, '', true);	
		//le auto complete
		this.resetSingleAutoComplete(params);
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.fillInputWithString = function(str, params){
		this.debug('fillInputWithString()', str, params);	
		//
		this.setInputBoxText(params, str, true);
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.resetPid = function(){
		this.debug('resetPid()');	
		//
		this.lastAutoCompletePid.exercice = 0;
		};

		
	//----------------------------------------------------------------------------------------------------------------------*	
	this.sendingWordToOurTeamForValidation = function(params){
		this.debug('sendingWordToOurTeamForValidation()', params);	
		
		var word = this.getLastSearchString();
		var msg = 'Sorry! No result for {[WORD]}.<br /><br />The medical language being in constant evolution, you can submit the {[WORD]} terms to our team. Thanks for helping us improve our search system.<br /><button class="send-keyword">submit {[WORD]} to our analysis team</button>';
		//si on avait des resultat de hint dans le autocomplete
		//on pourrait lui proposer ceux-la
		if(this.bHaveAutoCompleteResult){
			msg += '<br />Or you can try the hints bellow:'
			//les hints propose avant si clique dessus 
			//va le rajouter dans la case texte et 
			//relancer le fetch autocomplete
			for(var o in this.arrLastHintResult){
				msg += '<LI class="single-hint" keyword-word="' + this.arrLastHintResult[o] + '">' + this.arrLastHintResult[o] + '</LI>';
				}
			}
		//on met le conenu du msg dans le LI avaec un tag title
		var data = '<UL class="listing" focus-id="0" focus-id-max="0"><LI class="single-result-msg">' + msg.replace(/\{\[WORD\]\}/g, '<b>"' + word + '"</b>') + '</LI></UL>'; 
		//on ajoute le data
		$('#' + this.baseDivId).html(data);
		//on show	
		$('#' + this.baseDivId).css({'display':'block'});
		//on met l'action sur les single-hints si on en avait evidement
		if(this.bHaveAutoCompleteResult && this.arrLastHintResult.length > 0){
			$('#' + this.baseDivId + ' .single-hint').data('params', params);
			$('#' + this.baseDivId + ' .single-hint').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					var keywordWord = $(this).attr('keyword-word');
					var params = $(this).data('params');	
					oTmp.jautocomplete.setInputFromHint(keywordWord, params);
					}	
				});			
			}
		//	
		};	
		
		
	//----------------------------------------------------------------------------------------------------------------------*	
	this.setInputFromHint = function(word, params){
		this.debug('setInputFromHint()', word, params);
		//on va setter le input box comme si on avait fait 
		//une recherche en tapant du texte
		this.setInputBoxText(params, word, true);
		//set le focus sur input
		params.refinput.focus();	
		//on creer un fake event sur le input box
		var evnt = $.Event('keyup',{
			which: 0, //un rien	
			});
		//on enleve le autoaocmplgte
		this.hideAutoComplete();	
		//et on fait comme si on avait tape
		this.fetchAutoCompleteData(evnt, word, params);
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