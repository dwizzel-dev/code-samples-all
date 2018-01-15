/*

Author: DwiZZel
Date: 09-09-2015
Version: 3.1.0 BUILD X.X
Notes:	lang file utility
	
*/

//----------------------------------------------------------------------------------------------------------------------

function JLang(){

	//text container key->translation
	this.tx = [];

	//key => value
	with(this){

		tx['no client selected'] = 'Aucun client sélectionné';
		tx['no program saved'] = 'Aucun programme sauvegardé';
		tx['settings:'] = 'Paramètres:';
		tx['no name...'] = 'Aucun nom...';
		tx['new program'] = 'Nouveau programme';
		tx['print'] = 'Impression';
		tx['send'] = 'Envoyer';
		tx['client search'] = 'Recherche Client';
		tx['client name:'] = 'Nom du client:';
		tx['search exercise'] = 'Recherche Exercice';
		tx['by name:'] = 'Par Nom:';
		tx['by template:'] = 'Par Protocole:';
		tx['by filter:'] = 'Par Filtre:';
		tx['search'] = 'rechercher';
		tx['apply to:'] = 'appliquer à:';
		tx['all'] = 'tous';
		tx['selected'] = 'sélectionné';
		tx['unselected'] = 'non-sélectionné';
		tx['close'] = 'fermer';
		tx['start to type a name'] = 'recherche par nom';
		tx['client name hint'] = 'nom de client';
		tx['type name hint'] = 'recherche par nom';
		tx['exercice name hint'] = 'nom d\'exercice';
		tx['sets'] = 'sér.';
		tx['reps'] = 'rép.';
		tx['hold'] = 'tenir';
		tx['weight'] = 'poids';
		tx['tempo'] = 'tempo';
		tx['rest'] = 'repos';
		tx['freq'] = 'fréq.';
		tx['dur'] = 'durée';
		tx['slow'] = 'lent';
		tx['medium'] = 'moyen';
		tx['fast'] = 'vite';
		tx['very fast'] = 'très vite';
		tx['kg'] = 'kg.';
		tx['lbs'] = 'lbs.';
		tx['week'] = 'sem.';
		tx['day'] = 'jr.';
		tx['hr'] = 'hr.';
		tx['min'] = 'min.';
		tx['sec'] = 'sec.';
		tx['languages:'] = 'langues:';
		tx['save'] = 'sauvegarder';
		tx['save settings'] = 'terminer';
		tx['cancel'] = 'annuler';
		tx['save program'] = 'sauvegarder le programme'; 
		tx['program name:'] = 'nom du programme:';
		tx['template name:'] = 'nom du protocole:';
		tx['new program'] = 'nouveau prog.';
		tx['add new client'] = 'ajouter un client';
		tx['firstname:'] = 'prénom:';
		tx['lastname:'] = 'nom de famille:';
		tx['age:'] = 'age:';
		tx['email:'] = 'courriel:';
		tx['mobile:'] = 'mobile:';
		tx['name:'] = 'nom:';
		tx['add new program'] = 'ajouter un nouveau programme';
		tx['program details'] = 'Détails du programme';
		tx['template details'] = 'Détails du protocole';
		tx['new program name'] = 'Nouveau nom du programme';
		tx['new template name'] = 'Nouveau nom de protocole';
		tx['notes:'] = 'notes:';
		tx['client details'] = 'Détails du client';
		tx['programs:'] = 'programmes:';
		tx['no program saved'] = 'programme non sauvegardé';
		tx['delete'] = 'suprimmer';
		tx['exercise name:'] = 'nom de l\'exercice:';
		tx['instructions:'] = 'instructions:';
		tx['my instructions'] = 'mes instructions';
		tx['physiotec instructions']= 'instructions originales';
		tx['programs instructions'] = 'instructions du programme';
		tx['with bullet'] = 'avec point de forme';
		tx['no bullet'] = 'sans point de forme';
		tx['set has my instruction']= 'définir comme mes instructions';
		tx['sets:'] = 'série:';
		tx['repetition:'] = 'répétition:';
		tx['weight:'] = 'poids:';
		tx['frequency:'] = 'fréquence:';
		tx['hold:'] = 'tenir:';
		tx['tempo:'] = 'tempo:';
		tx['rest:'] = 'repos:';
		tx['duration:'] = 'durée:';
		tx['add to program'] = 'rajouter';
		tx['remove from program'] = 'supprimer';
		tx['remove'] = 'supprimer'; 
		tx['next'] = 'suivant';
		tx['previous'] = 'précédent';
		tx['clear'] = 'effacer';
		tx['description:'] = 'description :';
		tx['change settings'] = 'modifier les paramètres';
		tx['title and description:']= 'Titre et description:';
		tx['error!'] = 'Erreur!';
		tx['warning!'] = 'Avertissement!';
		tx['yes'] = 'oui';
		tx['no'] = 'non';
		tx['exit program'] = 'Sortie du programme';
		tx['replace'] = 'remplacer';
		tx['sorry! video is not available'] = 'Désolé! Ce vidéo n\'est pas disponible';
		tx['some data will be lost, are you sure you want to exit?'] = 'Etes-vous certain de vouloir quitter le présent programme?';
		tx['the new program name for <b>%name%</b> have to be different from the ones already existing.'] = 'Le nouveau nom de programme pour <b>%name%</b> doit différer de ceux déjà existants.';
		tx['the program <b>%programname%</b> for <b>%name%</b> already exist, do you want to replace it by this one?'] = 'Le programme <b>%programname%</b> pour <b>%name%</b> existe déjà, voulez-vous le remplacer par celui-ci?';
		tx['duplicate name'] = 'Programme existant';
		tx['the program name "<b>%programname%</b>" already exist, press the button "yes" to overwrite the existing program with this one.'] = 'Le nom de programme "<b>%programname%</b>" existe déjà, presser sur le bouton "' + tx['replace'] + '" pour ecraser le programme existant pas celui-ci, ou modifier le nom et presser sur le bouton "sauvegarder" de nouveau.';
		tx['duplicate template name'] = 'Protocole existant';
		tx['the template <b>%templatename%</b> already exist, do you want to replace it by this one?'] = 'Le protocole <b>%templatename%</b> existe déjà, voulez-vous le remplacer par celui-ci?';
		tx['no change'] = '...';
		tx['saved'] = 'sauvegardé';
		tx['saving'] = 'processing!';
		tx['showing '] = 'affiche ';
		tx[' of '] = ' sur ';
		tx['no result'] = 'aucun retour';
		tx['template name:'] = 'nom du protocole:';
		tx['modules:'] = 'module:';
		tx['loading'] = 'en chargement...'; 
		tx['select a module'] = 'selectionner un module'; 
		tx['select an option'] = 'selectionner une option'; 
		tx['template edition'] = 'PROTOCOLE';
		tx['save template'] = 'sauv. du protocole';
		tx['save a template copy'] = 'sauvegarder une copie du protocole';
		tx['an error occured during the template saving, please retry!'] = 'une erreur est survenue durant la sauvegarde svp veuillez recommencer, merci!';
		tx['select a template'] = 'selectionner un protocole'; 
		tx['select a template - mine'] = 'Les Miens'; 
		tx['select a template - all'] = 'Tous'; 
		tx['select a template - license'] = 'Entreprise'; 
		tx['select a template - brand'] = 'PHYSIOTEC'; 
		tx['sorry template doesnt exist anymore.'] = 'Désolé le gabarit n\'existe pas!'; 
		tx['save options'] = 'Options de sauvegarde';
		tx['save as other program'] = 'sauvegarder une copie du programme';
		tx['save as template'] = 'Sauvegarder comme protocole';
		tx['select a client for this program'] = 'select a client for this program';
		tx['change the client of this program'] = 'Sauvergarder pour un autre client';
		tx['add a new client to this program'] = 'add a new client to this program';
		tx['select a different client'] = 'select a different client';
		tx['save program as'] = 'Sauvegarder une copie';
		tx['client'] = 'Client';
		tx['current client:'] = 'current client:';
		tx['change client'] = 'change client';
		tx['add new client'] = 'Nouveau client';
		tx['select a client'] = 'choisir un client';
		tx['modify'] = 'modify';
		tx['save as'] = 'save as';
		tx['warning'] = 'Avertissement';
		tx['program name cant be empty.'] = 'le nom du programme ne peut être vide.';
		tx['template name cant be empty.'] = 'le nom du protocole ne peut être vide.';
		tx['you need to select a module.'] = 'vous devez sélectionner un module.';
		tx['processing'] = 'en cours!';
		tx['you must select a client first.'] = 'Vous devez sélectionner un client avant.';
		tx['you must create a program name first.'] = 'Vous devez donner un nom de programme avant.';
		tx['change client email at the same time'] = 'Changer aussi le courriel du client';
		tx['sets='] = 'sér.';
		tx['repetition='] = 'rép.';
		tx['weight='] = 'pds.';
		tx['frequency='] = 'frq.';
		tx['hold='] = 'tnr.';
		tx['tempo='] = 'tmp.';
		tx['rest='] = 'rps.'; 
		tx['duration='] = 'dur.'; 
		tx['an error occured during the saving, please retry!'] = 'Une erreur est survenu durant la sauvegarde, svp veuillez recommencer!'; 
		tx['server error on service call:'] = 'Erreur Serveur:';
		tx['service error:'] = 'Erreur Service:'; 
		tx['sub preferences'] = 'Mes Préférences'; 
		tx['sub account'] = 'Mon Compte'; 
		tx['class debugger'] = 'Rapport d\'erreur';
		tx['sub about'] = 'A propos de nous'; 
		tx['about us'] = 'A propos de nous';
		tx['account modification'] = 'Modification du compte'; 
		tx['username:'] = 'Nom d\'usager'; 
		tx['primary email:'] = 'Courriel primaire'; 
		tx['secondary email:'] = 'Courriel secondaire'; 
		tx['old password:'] = 'Mot de passe actuel:'; 
		tx['new password:'] = 'Nouveau mot de passe:'; 
		tx['confirm password:'] = 'Confirmez votre nouveau mot de passe:'; 
		tx['your password must:'] = 'Votre mot de passe doit:';
		tx['be over 8 characters long']= 'Contenir plus de 8 caractères'; 
		tx['use a combination of'] = 'Utiliser une combinaison de lettres majuscules et minuscules';
		tx['include at least'] = 'Inclure au moins un caractère numérique et/ou un des symbole (s) suivant: !, @, #, $, %, * .';
		tx['example of password:'] = 'Exemple de mot de passe:';
		tx['pswexample'] = 'Tremblay01@'; 
		tx['preferences modification'] = 'Modification des préférences';
		tx['clinic:'] = 'Clinique:'; 
		tx['langs:'] = 'Langues:';
		tx['print summary:'] = 'Imprimer un sommaire:'; 
		tx['email client:'] = 'Type de courriel:'; 
		tx['default module:'] = 'Module par défaut:'; 
		tx['search by module only:'] = 'Recherche par module seulement:'; 
		tx['the language has changed'] = 'La langue de préférence a été modifié, vous devez redémmarer l\'application pour que celle-ci soit prise en compte.';
		tx['reload now'] = 'Redémmarer l\'application maintenant?';
		tx['reload'] = 'Redémmarage';
		tx['username'] = 'nom d\'usager';
		tx['password'] = 'mot de passe';
		tx['login'] = 'connection';
		tx['sub logout'] = 'Déconnexion';
		tx['id:'] = 'id:';
		tx['about appz branding'] = '<center><p style="background-color: #3D7DCA;"><img src="[{SOURCE}]"></p><p><b>[{VERSION}]</b></p><p>Copyright © 1996-2016 Tous Droits Réservés</p><br /><p><a href="mailto:support@">support@</a></p><br /</center>';
		tx['search all modules'] = 'Recherche dans tous les modules';
		tx['category filter'] = 'Catégories';
		tx['tag filter'] = 'Filtres';
		tx['no filters'] = 'Aucun Filtre';
		tx['show only my exercises']= 'Mes Exercices';
		tx['show only my favorites']= 'Mes Favoris';
		tx['show only my modified exercises']= 'Mes Exercices Modifiés';
		tx['search by module'] = 'Recherche par module'; 

		/*
		//index.php static text
		tx['no client selected']	=	'Aucun client sélectionné';
		tx['no program saved']		=	'Aucun programme sauvegardé';
		tx['settings:']				=	'Paramètres:';
		tx['no name...']			=	'Aucun nom...';
		tx['new program']			=	'Nouveau programme';
		tx['print']					=	'Impression';
		tx['send']					=	'Envoyer';
		tx['client search']			=	'Recherche Client';
		tx['client name:']			=	'Nom du client:';
		tx['search exercise']		=	'Recherche Exercice';
		tx['by name:']				=	'Par Nom:';
		tx['by template:']			=	'Par Protocole:';
		tx['by filter:']			=	'Par Filtre:';
		tx['search']				=	'rechercher';
		tx['apply to:']				=	'appliquer à:';
		tx['all']					=	'tous';
		tx['selected']				=	'sélectionné';
		tx['unselected']			=	'non-sélectionné';
		tx['close']					=	'fermer';
		tx['start to type a name']	=	'recherche par nom';
		tx['client name hint']		=	'nom de client';
		tx['type name hint']		=	'recherche par nom';
		tx['exercice name hint']	=	'nom d\'exercice';

		//jsetting.js	
		tx['sets'] 		= 	'sér.';
		tx['reps'] 		= 	'rép.';
		tx['hold'] 		= 	'tenir';
		tx['weight'] 	= 	'poids';
		tx['tempo'] 	= 	'tempo';
		tx['rest'] 		= 	'repos';
		tx['freq'] 		= 	'fréq.';
		tx['dur'] 		= 	'durée';
		tx['slow'] 		= 	'lent';
		tx['medium'] 	= 	'moyen';
		tx['fast'] 		= 	'vite';
		tx['very fast'] = 	'très vite';
		tx['kg'] 		= 	'kg.';
		tx['lbs'] 		= 	'lbs.';
		tx['week'] 		= 	'sem.';
		tx['day'] 		= 	'jr.';
		tx['hr'] 		= 	'hr.';
		tx['min'] 		= 	'min.';
		tx['sec'] 		= 	'sec.';

		//jappz.js
		tx['languages:']			= 	'languages:';
		tx['save'] 					= 	'sauvegarder';
		tx['save settings']			= 	'terminer';
		tx['cancel'] 				= 	'annuler';
		tx['save program'] 			= 	'sauvegarder le programme';	
		tx['program name:'] 		= 	'nom du programme:';
		tx['template name:'] 		= 	'nom du protocole:';
		tx['new program'] 			= 	'nouveau prog.';
		tx['add new client'] 		= 	'ajouter un client';
		tx['firstname:'] 			= 	'prénom:';
		tx['lastname:'] 			= 	'nom de famille:';
		tx['age:'] 					= 	'age:';
		tx['email:'] 				= 	'courriel:';
		tx['mobile:'] 				= 	'mobile:';
		tx['name:'] 				= 	'nom:';
		tx['add new program'] 		= 	'ajouter un nouveau programme';
		tx['program details'] 		= 	'Détails du programme';
		tx['template details'] 		= 	'Détails du protocole';
		tx['new program name'] 		= 	'Nouveau nom du programme';
		tx['new template name'] 	= 	'Nouveau nom de protocole';
		tx['notes:'] 				= 	'notes:';
		tx['client details'] 		= 	'Détails du client';
		tx['programs:'] 			= 	'programmes:';
		tx['no program saved'] 		= 	'programme non sauvegardé';
		tx['delete'] 				= 	'suprimmer';
		tx['exercise name:'] 		= 	'nom de l\'exercice:';
		tx['instructions:'] 		= 	'instructions:';
		tx['my instructions'] 		= 	'mes instructions';
		tx['physiotec instructions']= 	'instructions originales';
		tx['programs instructions'] = 	'instructions du programme';
		tx['with bullet'] 			= 	'avec point de forme';
		tx['no bullet'] 			= 	'sans point de forme';
		tx['set has my instruction']= 	'définir comme mes instructions';
		tx['sets:'] 				= 	'série:';
		tx['repetition:'] 			= 	'répétition:';
		tx['weight:'] 				= 	'poids:';
		tx['frequency:'] 			= 	'fréquence:';
		tx['hold:'] 				= 	'tenir:';
		tx['tempo:'] 				= 	'tempo:';
		tx['rest:'] 				= 	'repos:';
		tx['duration:'] 			= 	'durée:';
		tx['add to program'] 		= 	'rajouter';
		tx['remove from program'] 	= 	'supprimer';
		tx['remove'] 				= 	'supprimer';	
		tx['next'] 					= 	'suivant';
		tx['previous'] 				= 	'précédent';
		tx['clear'] 				= 	'effacer';
		tx['description:'] 			= 	'description :';
		tx['change settings'] 		= 	'modifier les paramètres';
		tx['title and description:']= 	'Titre et description:';
		tx['error!']				= 	'Erreur!';
		tx['warning!']				= 	'Avertissement!';
		tx['yes']					= 	'oui';
		tx['no']					= 	'non';
		tx['exit program']			= 	'Sortie du programme';
		tx['replace']					= 	'remplacer';
		tx['sorry! video is not available']	= 'Désolé! Ce vidéo n\'est pas disponible';
		
		tx['some data will be lost, are you sure you want to exit?']	= 	'Etes-vous certain de vouloir quitter le présent programme?';
		tx['the new program name for <b>%name%</b> have to be different from the ones already existing.']	= 	'Le nouveau nom de programme pour <b>%name%</b> doit différer de ceux déjà existants.';
		tx['the program <b>%programname%</b> for <b>%name%</b> already exist, do you want to replace it by this one?']	=	'Le programme <b>%programname%</b> pour <b>%name%</b> existe déjà, voulez-vous le remplacer par celui-ci?';
		tx['duplicate name']		= 'Programme existant';
		tx['the program name "<b>%programname%</b>" already exist, press the button "yes" to overwrite the existing program with this one.']	=	'Le nom de programme "<b>%programname%</b>" existe déjà, presser sur le bouton "' + tx['replace'] + '" pour ecraser le programme existant pas celui-ci, ou modifier le nom et presser sur le bouton "sauvegarder" de nouveau.';
		tx['duplicate template name']		= 'Protocole existant';
		tx['the template <b>%templatename%</b> already exist, do you want to replace it by this one?'] = 'Le protocole <b>%templatename%</b> existe déjà, voulez-vous le remplacer par celui-ci?';
		
		tx['no change']				= 	'...';
		tx['saved']					= 	'sauvegardé';
		tx['saving']				= 	'processing!';
		tx['showing ']				= 	'affiche ';
		tx[' of ']					= 	' sur ';
		tx['no result']				= 	'aucun retour';

		//template
		tx['template name:']		= 	'nom du protocole:';
		tx['modules:']				= 	'module:';
		tx['loading']				= 	'en chargement...';	
		tx['select a module']		= 	'selectionner un module';	
		tx['select an option']		= 	'selectionner une option';	
		tx['template edition']		= 	'PROTOCOLE';
		tx['save template']			= 	'sauv. du protocole';
		tx['save a template copy']	= 	'sauvegarder une copie du protocole';
		tx['an error occured during the template saving, please retry!']	= 	'une erreur est survenue durant la sauvegarde svp veuillez recommencer, merci!';
		tx['select a template']	= 	'selectionner un protocole';		
		tx['select a template - mine']		= 	'selectionner un protocole - mine';		
		tx['select a template - all']		= 	'selectionner un protocole - all';		
		tx['select a template - license']	= 	'selectionner un protocole - license';		
		tx['select a template - brand']		= 	'selectionner un protocole - brand';	
		tx['sorry template doesnt exist anymore.']	= 'Désolé le gabarit n\'existe pas!';						
				
		//popup save
		tx['save options']			= 'Options de sauvegarde';
		tx['save as other program']	= 'sauvegarder une copie du programme';
		tx['save as template']		= 'Sauvegarder comme protocole';
		tx['select a client for this program']	= 'select a client for this program';
		tx['change the client of this program']	= 'change the client of this program';
		tx['add a new client to this program']	= 'add a new client to this program';
		tx['select a different client']			= 'select a different client';
		tx['save program as']		= 'Sauvegarder une copie';
		tx['client']				= 'Client';
		tx['current client:']		= 'current client:';
		tx['change client']			= 'change client';
		tx['add new client']		= 'Nouveau client';
		tx['select a client']		= 'choisir un client';
		tx['modify']				= 'modify';
		tx['save as']				= 'save as';
		tx['warning']				= 'Avertissement';
		tx['program name cant be empty.']		= 'le nom du programme ne peut être vide.';
		tx['template name cant be empty.']		= 'le nom du protocole ne peut être vide.';
		tx['you need to select a module.']		= 'vous devez sélectionner un module.';
		tx['processing']			= 'en cours!';

		//print and send email
		tx['you must select a client first.']	= 'Vous devez sélectionner un client avant.';
		tx['you must create a program name first.']	= 'Vous devez donner un nom de programme avant.';
		tx['change client email at the same time']	= 'Changer aussi le courriel du client';
		
		//pour le format content-view
		tx['sets='] 				= 	'sér.';
		tx['repetition='] 			= 	'rép.';
		tx['weight='] 				= 	'pds.';
		tx['frequency='] 			= 	'frq.';
		tx['hold='] 				= 	'tnr.';
		tx['tempo='] 				= 	'tmp.';
		tx['rest='] 				= 	'rps.';	
		tx['duration='] 			= 	'dur.';	

		//errors
		tx['an error occured during the saving, please retry!'] = 	'Une erreur est survenu durant la sauvegarde, svp veuillez recommencer!';	
		tx['server error on service call:'] = 'Erreur Serveur:';
		tx['service error:'] = 'Erreur Service:';		

		//sub menu
		tx['sub preferences']		=	'Mes Préférences';	
		tx['sub account']			=	'Mon Compte';	
		tx['class debugger']		=	'Rapport d\'erreur';
		tx['sub about']				=	'A propos de nous';	
		tx['about us']				=	'A propos de nous';

		//options account
		tx['account modification']	=	'Modification du compte';	
		tx['username:']				=	'Nom d\'usager';	
		tx['primary email:']		=	'Courriel primaire';	
		tx['secondary email:']		=	'Courriel secondaire';		
		tx['old password:']			=	'Mot de passe actuel:';		
		tx['new password:']			=	'Nouveau mot de passe:';		
		tx['confirm password:']		=	'Confirmez votre nouveau mot de passe:';	
		//
		tx['your password must:']		=	'Votre mot de passe doit:';
		tx['be over 8 characters long']=	'Contenir plus de 8 caractères';		
		tx['use a combination of']		=	'Utiliser une combinaison de lettres majuscules et minuscules';
		tx['include at least']			=	'Inclure au moins un caractère numérique et/ou un des symbole (s) suivant:  !, @, #, $, %, * .';
		tx['example of password:']		=	'Exemple de mot de passe:';
		tx['pswexample']				=	'Tremblay01@';	
		
		//options prefs
		tx['preferences modification']	=	'Modification des préférences';
		tx['clinic:']					=	'Clinique:';	
		tx['langs:']					=	'Languages:';
		tx['print summary:']			=	'Imprimer un sommaire:';		
		tx['email client:']				=	'Type de courriel:';		
		tx['default module:']			=	'Module par défaut:';		
		tx['search by module only:']	=	'Recherche par module seulement:';	
		tx['the language has changed']	= 	'La langue de préférence a été modifié, vous devez redémmarer l\'application pour que celle-ci soit prise en compte.';
		tx['reload now']				= 	'Redémmarer l\'application maintenant?';
		tx['reload']					= 	'Redémmarage';

		//login
		tx['username']					= 	'nom d\'usager';
		tx['password']					= 	'mot de passe';
		tx['login']						= 	'connection';
		tx['sub logout']				= 	'Déconnexion';
		tx['id:']						= 	'id:';

		//for branding purpose
		tx['about appz branding']		= 	'<center><p style="background-color: #3D7DCA;"><img src="[{SOURCE}]"></p><p><b>[{VERSION}]</b></p><p>Copyright © 1996-2016 Tous Droits Réservés</p><br /><p><a href="mailto:support@">support@</a></p><br /</center>';

		//new search options
		tx['search all modules']	= 	'Recherche dans tous les modules';
		tx['category filter']		= 	'Catégories';
		tx['tag filter']			= 	'Filtres';
		tx['no filters']			=	'Aucun Filtre';
		tx['show only my exercises']= 	'Mes Exercices';
		tx['show only my favorites']= 	'Mes Favoris';
		tx['show only my modified exercises']= 	'Mes Exercices Modifiés';
		tx['search by module']		= 	'Recherche par module';
		*/

		}		

	//get the text by key or return the key with tilde
	this.t = function(key){
		if(typeof(this.tx[key]) == 'string'){
			return this.tx[key];
			}
		return '~' + key + '~';
		}

	//convert the text of the index.html file since it will not be dynamic for intelXDK mobile application
	this.setStaticText = function(){
		//will use the jquery for that
		$('#top-client-name').text(this.t('no client selected'));
		$('#top-program-name').text(this.t('no program saved'));
		$('#main-settings-top > .settings-title-text > span:first-child').text(this.t('settings:'));
		$('#main-settings-program-name').text(this.t('no name...'));
		$('#main-program-name-text').text(this.t('new program'));
		$('#input-main-client-search-autocomplete').attr('placeholder', this.t('client search'));
		$('#input-main-exercice-search-autocomplete').attr('placeholder', this.t('search exercise'));
		$('#search-exercise-form > p:nth-child(1)').text(this.t('by template:'));
		//$('#main-settings-bottom > div > div > div:first-child').text(this.t('apply to:'));
		$('#butt-settings-all').text(this.t('all'));
		$('#butt-settings-close').text(this.t('close'));
		}

	//build the index.php strings
	this.setStaticText();



	}


//CLASS END