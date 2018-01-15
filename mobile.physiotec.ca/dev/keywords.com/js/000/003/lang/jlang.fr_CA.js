/*

Author: DwiZZel
Date: 15-07-2016
Version: 1.0.0 BUILD X.X
Notes:	lang file utility for fr_CA
	
*/

//----------------------------------------------------------------------------------------------------------------------

function JLang(){

	//text container key->translation
	this.tx = [];

	//key => value
	with(this){
		tx['search'] = 'taper une recherche';
		tx['resultat'] = 'resultat';
		tx['count'] = 'nbr';
		tx['total'] = 'Exercices Au Total: ';
		tx['keywords'] = 'Mots Clés';	
		tx['code exercise'] = 'Code Exercice';	
		tx['short title'] = 'Titre Court';	
		tx['kw'] = 'mc';	
		tx['st'] = 'tc';	
		tx['ce'] = 'ce';	
		tx['no result'] = 'Désolé! aucun resultat pour votre recherche';
		}		

	//get the text by key or return the key with tilde
	this.t = function(key){
		if(typeof(this.tx[key]) == 'string'){
			return this.tx[key];
			}
		return '~' + key + '~';
		}

	//convert the text of the index.html file since it will not 
	//be dynamic for intelXDK mobile application
	this.setStaticText = function(){
		//will use the jquery for that
		}

	//build the index.php strings
	this.setStaticText();



	}


//CLASS END