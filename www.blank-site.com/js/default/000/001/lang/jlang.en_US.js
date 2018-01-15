/*

Author: DwiZZel
Date: 00-00-0000
Version: V.1.0 BUILD 001
	
*/

//----------------------------------------------------------------------------------------------------------------------

function JLang(){

	//text container key->translation
	this.tx = [];

	//key => value
	with(this){
		tx['search'] = 'type your search';
		tx['result'] = 'result';
		tx['count'] = 'count';
		tx['total'] = 'Total Exercices: ';
		tx['keywords'] = 'Keywords';	
		tx['code exercise'] = 'Code Exercise';	
		tx['short title'] = 'Short Title';	
		tx['kw'] = 'kw';	
		tx['st'] = 'st';	
		tx['ce'] = 'ce';	
		tx['no result'] = 'Sorry! no result for your search';
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