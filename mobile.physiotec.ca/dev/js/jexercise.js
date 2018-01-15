/*

Author: DwiZZel
Date: 25-04-2016
Version: 3.1.0 BUILD X.X
Notes:

*/
//----------------------------------------------------------------------------------------------------------------------
    
function JExercice(id, obj, boxName){

	//ref class name
	this.className = 'JExercice';
	//name of the visual box for jquery manipulation search-img-ID, img-ID
	this.boxName = boxName;
	//basic data we need
	this.id = id;
	//
	this.filterId = 0;
	if(typeof(obj.filter) != 'undefined'){
		this.filterId = obj.filter;
		}
	
	//
	this.video = '';	
	if(typeof(obj.video) != 'undefined'){
		if(obj.video != null && obj.video != 'null' ){
			this.video = obj.video;
			}
		}
	//
	this.codeExercise = '';
	if(typeof(obj.codeExercise) != 'undefined'){
		this.codeExercise = obj.codeExercise;
		}	

	//les images flip et mirroring	
	//	
	this.mirror = 0;
	if(typeof(obj.mirror) != 'undefined'){
		this.mirror = parseInt(obj.mirror);
		}
	//
	this.flip = 0;
	if(typeof(obj.flip) != 'undefined'){
		this.flip = parseInt(obj.flip);
		}

	//json compressed original data
	this.data = false;
	if(typeof(obj.data) == 'string'){
		if(obj.data != ''){
			this.data = JSON.parse(obj.data);	
			}
		}

	//json compressed when use with "my instruction" quand remplace la version originale
	this.userdata = false;
	if(typeof(obj.userdata) == 'string'){
		if(obj.userdata != ''){
			this.userdata = JSON.parse(obj.userdata);	
			}
		}

	//mem format que .data et .userdata excepte que c'est les instruction pour le programme	
	this.programdata = false;
	if(typeof(obj.programdata) == 'string'){
		if(obj.programdata != ''){
			this.programdata = JSON.parse(obj.programdata);	
			}
		}

	//holder of the hammer object
	this.boxEvent;

	if(typeof(obj.settings) == 'object'){
		this.settings = obj.settings;	
	}else{
		this.settings = {
			'sets': '',
			'repetition': '',
			'hold': '',
			'weight': '',
			'tempo': '',
			'rest': '',
			'frequency': '',
			'duration': '',
			};
		}
		
	
	//----------------------------------------------------------------------------------------------------------------------*
	//return complete data object returned and parse has json
	this.getObj = function(){
		//on format les infos du JExercice javascript de la meme facon que ce que l'on retourne en php
		var obj = {
			'codeExercise' : this.codeExercise,
			'data' : JSON.stringify(this.data),
			'userdata' : JSON.stringify(this.userdata),
			'programdata' : JSON.stringify(this.programdata),
			'settings' : this.settings,	
			'video' : this.video,
			'flip' : this.flip,
			'mirror' : this.mirror,
			};
		//rtn
		return obj;	
		};
	
	//----------------------------------------------------------------------------------------------------------------------*
	//add the holder hammer to exercice class
	this.addEventManager = function(evtMng){
		this.boxEvent = evtMng;
		};
	
	//----------------------------------------------------------------------------------------------------------------------*	
	//destroy the hammer manager
	this.destroyEventManager = function(){
		if(typeof(this.boxEvent) == 'object'){
			this.boxEvent.destroy();
			}
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//return the flip img state
	this.getFlip = function(){
		return parseInt(this.flip);
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//set the flip img state
	this.setFlip = function(bState){
		this.debug('setFlip(' + bState + ')');
		this.flip = parseInt(bState);
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//return the mirror img state
	this.getMirror = function(){
		return parseInt(this.mirror);
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//set the mirror img state
	this.setMirror = function(bState){
		this.debug('setMirror(' + bState + ')');	
		this.mirror = parseInt(bState);
		};
	
	//----------------------------------------------------------------------------------------------------------------------*
	//return the settings object
	this.getSettings = function(){
		return this.settings;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//set the settings object
	this.setSettings = function(obj){
		this.debug('setSettings(' + obj + ')');
		this.settings = obj;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//return the video link
	this.getVideo = function(){
		if(this.video != ''){
			return this.video;
			}
		return false;	
		};	

	//----------------------------------------------------------------------------------------------------------------------*
	//return the video type fliqz ou sprout
	this.getVideoType = function(){
		if(this.video != ''){
			var index = this.video.indexOf('embed/'); //le seul mot cle dans la phrase
			if(index == -1){
				return false;
				}
			return 'sprout';	
			}
		return false;	
		};


	//----------------------------------------------------------------------------------------------------------------------*
	//return complete data object returned and parse has json
	this.getData = function(){
		return this.data;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//return complete userdata object returned and parse has json
	this.getUserData = function(){
		return this.userdata;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//return complete programdata object returned and parse has json
	this.getProgramData = function(){
		return this.programdata;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get the name based on the lang
	this.haveUserData = function(){
		if(typeof(this.userdata.locale) == 'object'){
			return true;
			}		
		return false;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get the name based on the main application lang for search listing display
	this.getTitleForSearchListing = function(){ 
		var strTitle = '';
		if(typeof(this.userdata) == 'object'){ 
			if(typeof(this.userdata.locale) == 'object'){
				if(typeof(this.userdata.locale[gLocaleLang]) == 'object'){
					if(typeof(this.userdata.locale[gLocaleLang].short_title) == 'string'){
						strTitle = this.userdata.locale[gLocaleLang].short_title;
						}
					}
				}
		}else if(typeof(this.data) == 'object'){ 
			if(typeof(this.data.locale) == 'object'){
				if(typeof(this.data.locale[gLocaleLang]) == 'object'){
					if(typeof(this.data.locale[gLocaleLang].short_title) == 'string'){
						strTitle = this.data.locale[gLocaleLang].short_title;
						}
					}
				}
			}
		//si vide
		if(strTitle == ''){
			if(this.codeExercise != ''){
				strTitle = this.codeExercise;
			}else{
				strTitle = jLang.t('no title');	
				}
			}
		//
		return strTitle;
		};


	//----------------------------------------------------------------------------------------------------------------------*
	//get the name based on the main application lang
	//or on the client lang of the program if some client is selected
	this.getTitleForProgramListing = function(lang){ 
		var strTitle = '';
		//check si programdata, userdata et data
		if(typeof(this.programdata) == 'object'){
			if(typeof(this.programdata.locale) == 'object'){
				if(typeof(this.programdata.locale[lang]) == 'object'){
					if(typeof(this.programdata.locale[lang].short_title) == 'string'){
						strTitle = this.programdata.locale[lang].short_title;
						}
					}
				}
		}else if(typeof(this.userdata) == 'object'){ 
			if(typeof(this.userdata.locale) == 'object'){
				if(typeof(this.userdata.locale[lang]) == 'object'){
					if(typeof(this.userdata.locale[lang].short_title) == 'string'){
						strTitle = this.userdata.locale[lang].short_title;
						}
					}
				}
		}else if(typeof(this.data) == 'object'){ 
			if(typeof(this.data.locale) == 'object'){
				if(typeof(this.data.locale[lang]) == 'object'){
					if(typeof(this.data.locale[lang].short_title) == 'string'){
						strTitle = this.data.locale[lang].short_title;
						}
					}
				}
			}
		//si vide
		if(strTitle == ''){
			if(this.codeExercise != ''){
				strTitle = this.codeExercise;
			}else{
				strTitle = jLang.t('no title');	
				}
			}
		//
		return strTitle;
		};


	
	//----------------------------------------------------------------------------------------------------------------------*
	//get the name based on the lang
	this.getTitleByLang = function(lang){
		var strTitle = '';	
		if(typeof(this.data.locale) == 'object'){
			if(typeof(this.data.locale[lang]) == 'object'){
				if(typeof(this.data.locale[lang].short_title) == 'string'){
					strTitle = this.data.locale[lang].short_title;
					}
				}
			}
		//
		if(strTitle == ''){
			if(this.codeExercise != ''){
				strTitle = this.codeExercise;
			}else{
				strTitle = jLang.t('no title');	
				}
			}
		//
		return strTitle;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get the name based on the lang
	this.getUserDataTitleByLang = function(lang){
		var strTitle = '';
		if(typeof(this.userdata.locale) == 'object'){	
			if(typeof(this.userdata.locale[lang]) == 'object'){
				if(typeof(this.userdata.locale[lang].short_title) == 'string'){
					strTitle = this.userdata.locale[lang].short_title;
					}
				}
			}
		//
		if(strTitle == ''){
			if(this.codeExercise != ''){
				strTitle = this.codeExercise;
			}else{
				strTitle = jLang.t('no title');	
				}
			}
		//
		return strTitle;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get the name based on the lang
	this.getProgramDataTitleByLang = function(lang){
		var strTitle = '';
		if(typeof(this.programdata.locale) == 'object'){
			if(typeof(this.programdata.locale[lang]) == 'object'){
				if(typeof(this.programdata.locale[lang].short_title) == 'string'){
					strTitle = this.programdata.locale[lang].short_title;
					}
				}
			}
		//
		if(strTitle == ''){
			if(this.codeExercise != ''){
				strTitle = this.codeExercise;
			}else{
				strTitle = jLang.t('no title');	
				}
			}
		//
		return strTitle;
		};	

	
	//----------------------------------------------------------------------------------------------------------------------*
	//set the name based on the lang
	this.setTitle = function(str){
		if(typeof(this.data.locale[gLocaleLang]) == 'object'){
			if(typeof(this.data.locale[gLocaleLang].short_title) == 'string'){
				this.data.locale[gLocaleLang].short_title = str;
				}
			}
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get the description based on the lang
	this.getDescription = function(){
		if(typeof(this.data.locale) == 'object'){
			if(typeof(this.data.locale[gLocaleLang]) == 'object'){
				if(typeof(this.data.locale[gLocaleLang].description) == 'string'){
					return this.data.locale[gLocaleLang].description;
					}
				}
			}
		//
		return jLang.t('no description');
		};
	
	//----------------------------------------------------------------------------------------------------------------------*
	//get the description based on the lang
	this.getDescriptionByLang = function(lang){
		if(typeof(this.data.locale) == 'object'){
			if(typeof(this.data.locale[lang]) == 'object'){
				if(typeof(this.data.locale[lang].description) == 'string'){
					return this.data.locale[lang].description;
					}
				}
			}
		//
		return jLang.t('no original description');
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get the description based on the lang
	this.getUserDataDescriptionByLang = function(lang){
		if(typeof(this.userdata.locale) == 'object'){
			if(typeof(this.userdata.locale[lang]) == 'object'){
				if(typeof(this.userdata.locale[lang].description) == 'string'){
					return this.userdata.locale[lang].description;
					}
				}
			}
		//
		return jLang.t('no user description');
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get the description based on the lang
	this.getProgramDataDescriptionByLang = function(lang){
		if(typeof(this.programdata.locale) == 'object'){
			if(typeof(this.programdata.locale[lang]) == 'object'){
				if(typeof(this.programdata.locale[lang].description) == 'string'){
					return this.programdata.locale[lang].description;
					}
				}
			}
		//
		return jLang.t('no program description');
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get the picture array
	this.getPicturesArray = function(){
		var arr = [];	
		if(typeof(this.data.picture) == 'object'){
			for(var o in this.data.picture){
				if(this.data.picture[o].pic == ''){
					arr[o] = gServerPath + 'images/' + gBrand + '/default-exercice.png';
				}else{
					arr[o] = gExerciceImagePath + this.data.picture[o].pic;
					}
				}
			return arr;
			}
		//
		return false;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get the thumbs array
	this.getThumbsArray = function(){
		var arr = [];	
		if(typeof(this.data.picture) == 'object'){
			for(var o in this.data.picture){
				if(this.data.picture[o].thumb == ''){
					arr[o] = gServerPath + 'images/' + gBrand + '/default-exercice.png';
				}else{
					arr[o] = gExerciceImagePath + this.data.picture[o].thumb;
					}
				}
			return arr;
			}
		//
		return false;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get the picture principale soit [0]
	this.getPicture = function(){
		if(typeof(this.data.picture) == 'object'){
			if(typeof(this.data.picture[0]) == 'object'){
				if(typeof(this.data.picture[0].pic) == 'string'){
					if(this.data.picture[0].pic == ''){
						return gServerPath + 'images/' + gBrand + '/default-exercice.png';
					}else{
						return gExerciceImagePath + this.data.picture[0].pic;
						}	
					}
				}
			}
		//
		return gServerPath + 'images/' + gBrand + '/default-exercice.png';
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get the picture principale soit [0]
	this.getThumb = function(){
		if(typeof(this.data.picture) == 'object'){
			for(var i=1;i>=0;i--){
				if(typeof(this.data.picture[i]) == 'object'){
					if(typeof(this.data.picture[i].thumb) == 'string'){
						if(this.data.picture[i].thumb != ''){
							return gExerciceImagePath + this.data.picture[i].thumb;	
							}
						}
					}
				}
			}
		return gServerPath + 'images/' + gBrand + '/default-exercice.png';

		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get le id de exercice
	this.getId = function(){
		return this.id;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get le code de exercice
	this.getCode = function(){
		return this.codeExercise;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get le box name html
	this.getBoxName = function(){
		return this.boxName;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get le array de langua avec au moins un titre dedans sinon on ne le retourne pas
	this.getLanguageArray = function(){
		var arr = [];
		for(var o in this.data.locale){
			if(typeof(this.data.locale[o].short_title) == 'string'){
				arr[o] = o;
				}
			}
		return arr;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get le array de langua avec au moins un titre dedans sinon on ne le retourne pas
	this.getUserDataLanguageArray = function(){
		var arr = [];
		for(var o in this.userdata.locale){
			if(typeof(this.userdata.locale[o].short_title) == 'string'){
				arr[o] = o;
				}
			}
		return arr;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//get le array de langua avec au moins un titre dedans sinon on ne le retourne pas
	this.getProgramDataLanguageArray = function(){
		var arr = [];
		for(var o in this.programdata.locale){
			if(typeof(this.programdata.locale[o].short_title) == 'string'){
				arr[o] = o;
				}
			}
		return arr;
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//va copier les infos de this.data ou de this.userdata dans this.programdata
	this.copyInstructionToProgramData = function(){
		if(typeof(this.programdata) == 'object'){
			//on ne fait rien vu que l'on garde ca
		}else if(typeof(this.userdata) == 'object'){
			//le my instruction en premier
			this.programdata = this.userdata; //auomatiquement locale seulement
		}else{
			//sinon le data de base
			this.programdata = {};
			this.programdata.locale = this.data.locale; //juste locale car les autres sont pour les videos et les images
			}
		};
	
	//----------------------------------------------------------------------------------------------------------------------*
	//returne le titre, pas celui short_title, mais celui qui n'est pas editable et static
	this.getStaticTitleByLang = function(lang){
		if(typeof(this.data.locale) == 'object'){
			if(typeof(this.data.locale[lang]) == 'object'){
				if(typeof(this.data.locale[lang].title) == 'string'){
					return this.data.locale[lang].title;
				}else{
					return this.codeExercise;
					}
				}
			}
		//
		return jLang.t('no static title');	
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//overwirte les infos dans this.programdata
	this.overwriteProgramData = function(objLocaleData){
		this.programdata = objLocaleData;
		};
	
	//----------------------------------------------------------------------------------------------------------------------*
	//overwirte les infos dans this.programdata
	this.overwriteUserData = function(objLocaleData){
		this.userdata = objLocaleData;
		};

	//debugger	
	//----------------------------------------------------------------------------------------------------------------------*
	this.debug = function(){
		if(arguments.length == 1){	
			jDebug.show(this.className + '::' + arguments[0]);
		}else{
			jDebug.showObject(this.className + '::' + arguments[0], arguments[1]);
			}
		}	

	

	} 


//CLASS END