/*

Author: DwiZZel
Date: 14-09-2015
Version: V.3.2.0 BUILD X.X
Notes:	JSettingManager and JSetting
		
*/

//----------------------------------------------------------------------------------------------------------------------
    
function JSetting(args){

	this.className = 'JSetting';
	
	//base name of the bar
	this.barName = 'setting-bar-';
	this.barOpacity = 0.25;
	this.isActive = false; //the setting is active
	this.isTextMode = false; //take the textValue not the component values
	this.textValue = ''; //keep the textbox value when set
	
	//the jsettingmanager class ref
	this.manager = args.manager;
	
	//only used by selectLink to keep an array of choices
	this.arrLinkData = [];
	this.currentLinkIndex = 0;

	//only used by touchspin if we instantiate a JTouchSpin
	this.touchSpin;
	
	//image path
	this.image = {
		text : gServerPath + 'images/' + gBrand + '/glyphicons_150_edit-invert.png',
		spinner : gServerPath + 'images/' + gBrand + '/spinner-icon-2.png',
		};

	//default values for starting	
	this.defaultVal = {
		id : 0,	
		title : 'title',
		};
	
	//overwrrite if needed
	if(typeof(args) == 'object'){
		this.defaultVal.id = args.id;	
		this.defaultVal.title = args.title;
		}


	//----------------------------------------------------------------------------------------------------------------------*
	//draw a touch spin component	
	this.insertComponent = function(str){
		//get the inner of the bar
		$('#' + this.barName + this.defaultVal.id + ' > .component').html(str);
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	//draw the main bar contaiuner
	this.drawBar = function(){
		var str = '';
		//open the bar div
		str += '<div class="bar" id="' + this.barName + this.defaultVal.id + '">';
		//title
		str += '<div class="title">';
		str += '<div class="text">' + this.defaultVal.title + ' : </div>';
		str += '<div class="hitpoint"></div>'; //la surface cliquable sur le titre
		str += '</div>';
		//container of the spinner, selectbox, selectlink component
		str += '<div class="component"></div>';
		//container of the text box for text editing values
		str += '<div class="textbox" style="display:none;">';
		str += '<div class="box">';
		str += '<div class="text">' + this.textValue + '</div>';
		str += '</div>';
		str += '</div>';
		//icon for editing spinner/textmode switch
		str += '<div class="icon"><img draggable="false" src="' + this.image.text + '"></div>';
		//layer over for activating the setting bar
		str += '<div class="mask"><div class="text"></div></div>';
		//close the bar div	
		str += '</div>';
		
		return str;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//set the action when clicking on the txetbox to open the seting popup for editing value
	this.setTextBoxAction = function(){
		//title
		//ref to the this.class
		$('#' + this.barName + this.defaultVal.id + ' > .textbox').data('parentclass', this);
		$('#' + this.barName + this.defaultVal.id + ' > .textbox').click(function(e){
			e.preventDefault();
			//get the parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				oTmp.manager.openMainAppzSettingsPopup(oTmp.defaultVal.id);
				}
			});
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	//set the bar action for activating the bar on click then unbind() it to prevent other event on it
	this.setBarActivateAction = function(bActive){
		//title
		//ref to the this.class to the title and the mask
		$('#' + this.barName + this.defaultVal.id + ' > .title > .hitpoint, ' + '#' + this.barName + this.defaultVal.id + ' > .mask').data('parentclass', this);
		//set global to class
		this.isActive = bActive;
		//set the on click action yto activate the component
		$('#' + this.barName + this.defaultVal.id + ' > .title > .hitpoint, ' + '#' + this.barName + this.defaultVal.id + ' > .mask').click(function(e){
			e.preventDefault();
			//get the parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				oTmp.switchBarActivation(oTmp.isActive);
				}
			});
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//set the icon action for switching betwqeen textmode and component mode
	this.setIconEditingModeAction = function(){
		//parent ref class
		$('#' + this.barName + this.defaultVal.id + ' > .icon').data('parentclass', this);
		//set the on click action yto activate the component
		$('#' + this.barName + this.defaultVal.id + ' > .icon').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				oTmp.switchToTextMode();
				}
			});
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//activate the bar on click (activate) event or the title on click (deactivate)
	this.switchBarActivation = function(bActive){
		if(bActive){
			//remove the mask 
			$('#' + this.barName + this.defaultVal.id + ' > .mask').css({'display':'block'});
			this.isActive = false;
		}else{
			//add the mask 
			$('#' + this.barName + this.defaultVal.id + ' > .mask').css({'display':'none'});
			this.isActive = true;
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//switch between textmode and compoenent mode
	this.switchToTextMode = function(){
		if(this.isTextMode){
			//switch to component editing boxes
			$('#' + this.barName + this.defaultVal.id + ' > .textbox').css({'display':'none'});
			$('#' + this.barName + this.defaultVal.id + ' > .component').css({'display':'block'});
			//on change l'icone
			$('#' + this.barName + this.defaultVal.id + ' > .icon > IMG').attr('src', this.image.text);
			//
			this.isTextMode = false;
		}else{
			//on check si il y a quelque chose dans le textbox, 
			//si vide alors on popup la fenetre de settings
			//si deja quelque chose on fait juste enable le textmode
			//si il clique sur la case du textmode box alors on ouvre le popup settings aussi
			if(this.getTextValue() == ''){
				//on ouvre le popup car le textbox est vide on lui passe le id de la component
				this.manager.openMainAppzSettingsPopup(this.defaultVal.id);
				}
			//switch to textmode editing
			$('#' + this.barName + this.defaultVal.id + ' > .textbox').css({'display':'block'});
			$('#' + this.barName + this.defaultVal.id + ' > .component').css({'display':'none'});
			//on change l'icone
			$('#' + this.barName + this.defaultVal.id + ' > .icon > IMG').attr('src', this.image.spinner);
			//
			this.isTextMode = true;
			}
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	//draw a touch spin component	
	this.drawTouchSpin = function(args){
		//instantiate touchSpin obj
		this.touchSpin = new JTouchSpin((this.barName + this.defaultVal.id), args);
		//return the string
		return this.touchSpin.draw();
		}	
	
	//----------------------------------------------------------------------------------------------------------------------*
	//draw a selectbox component	
	this.drawSelectBox = function(arrData, selected){
		var str = '';
		str += '<div class="selectbox">';
		str += '<div class="box">';
		str += '<select>';
		for(var o in arrData){
			str += '<option value="' + arrData[o] + '"';
			if(selected == arrData[o]){
				str += ' selected ';
				}
			str += '>' + arrData[o] + '</option>';
			}
		str += '</select>';
		str += '</div>';
		str += '</div>';
		
		return str;
		}

	//----------------------------------------------------------------------------------------------------------------------*	
	//draw a selectlink: change on touch	
	this.drawSelectLink = function(arrData){
		//keep the array in the class
		this.arrLinkData = arrData;
		//init the index
		this.currentIndex = 0;

		var str = '';
		//use the first one has default
		str += '<div class="selectlink">';
		str += '<div class="text">' + this.arrLinkData[this.currentIndex] + '</div>';
		str += '</div>';
		
		return str;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//activate the select link if this.arrLinkData contains data if not then its not a link component
	this.activateSelectLink = function(){
		if(this.arrLinkData.length > 0){
			//sets some attributes to ref the parent class jsetting
			$('#' + this.barName + this.defaultVal.id + ' > .component > .selectlink > .text').data('parentclass', this);
			//action on the text
			$('#' + this.barName + this.defaultVal.id + ' > .component > .selectlink > .text').click(function(e){
				e.preventDefault();
				var oTmp = $(this).data('parentclass');
				if(typeof(oTmp) == 'object'){
					oTmp.goToNextLinkInArray();
					}
				});
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//activate the touchspin if this.touchSpin is object
	this.activateTouchSpin = function(){
		if(typeof(this.touchSpin) == 'object'){
			this.touchSpin.initTouchSpin();
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//next value of the array of link for selectlink actrion
	this.goToNextLinkInArray = function(){
		this.currentIndex++;
		if(this.currentIndex >= this.arrLinkData.length){
			//on revient a zero
			this.currentIndex = 0;
			}
		//on change le text
		$('#' + this.barName + this.defaultVal.id + ' > .component > .selectlink > .text').text(this.arrLinkData[this.currentIndex]);
		}
	
	//----------------------------------------------------------------------------------------------------------------------*	
	//draw a text fake input box	
	this.drawTextBox = function(val){
		var str = '';
		str += '<div class="textbox">';
		str += '<div class="box">';
		str += '<div class="text">' + val + '</div>';
		str += '</div>';
		str += '</div>';
		
		return str;
		}	
	
	//----------------------------------------------------------------------------------------------------------------------*
	//change le textvalue a partir d'un changement du popup de settings
	this.setTextValue = function(str){
		this.textValue = str;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//change le value a lìnterieur du textbox static
	this.setTextBoxValue = function(){
		$('#' + this.barName + this.defaultVal.id + ' > .textbox > .box > .text').text(this.textValue);
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//get the textvalue string
	this.getTextValue = function(){
		return this.textValue;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//from the select box
	this.getSelectBoxValue = function(){
		return $('#' + this.barName + this.defaultVal.id + ' > .component > .selectbox > .box > SELECT').val();
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	//from the touchspin
	this.getTouchSpinValue = function(){
		return this.touchSpin.getCurrentVal();
		}	

	//----------------------------------------------------------------------------------------------------------------------*
	//from the link button
	this.getSelectLinkValue = function(){
		return $('#' + this.barName + this.defaultVal.id + ' > .component > .selectlink > .text').text();
		}		

	
	
	}	
	
//----------------------------------------------------------------------------------------------------------------------
    
function JSettingManager(args){

	this.className = 'JSettingManager';	
	
	//the base container where we write all the components for the setting panel
	this.containerName;

	//main appz soit la classe principale qui soccupe de tout
	this.mainAppz = args.mainappz;

	//default component settings
	this.settings = {};

	//----------------------------------------------------------------------------------------------------------------------*
	//init settings data
	this.initSettingData = function(){
		this.settings = {
			'sets' 	: {
						id 			: 'sets', //ID must be the same has the main key (sets = sets.id)
						title		: jLang.t('sets'),
						longtitle	: jLang.t('sets:'),
						jsetting	: false,
						components	: [
							{
								type: 'touchspin',
								min: 0,
								max: 10,
								val: 5,
								},
							],
						},
			'repetition' 	: {
						id 		: 'repetition',
						title	: jLang.t('reps'),
						longtitle	: jLang.t('repetition:'),
						jsetting: false,
						components	: [
							{
								type: 'touchspin',
								min: 0,
								max: 30,
								val: 15,
								},
							],
						},
			'hold' 	: {
						id 		: 'hold',
						title	: jLang.t('hold'),
						longtitle	: jLang.t('hold:'),
						jsetting: false,
						components	: [
							{
								type: 'touchspin',
								min: 0,
								max: 60,
								val: 30,
								},
							{
								type: 'link',
								data: [jLang.t('sec'), jLang.t('min')],
								},
							],
						},
			'weight' 	: {
						id 		: 'weight',
						title	: jLang.t('weight'),
						longtitle	: jLang.t('weight:'),
						jsetting: false,
						components	: [
							{
								type: 'touchspin',
								min: 0,
								max: 100,
								val: 50,
								},
							{
								type: 'link',
								data: [jLang.t('kg'), jLang.t('lbs')], 
								},
							],
						},	
			'tempo' 	: {
						id 		: 'tempo',
						title	: jLang.t('tempo'),
						longtitle	: jLang.t('tempo:'),
						jsetting: false,
						components	: [
							{
								type: 'selectbox',
								data: [jLang.t('slow'), jLang.t('medium'), jLang.t('fast'), jLang.t('very fast')],
								selected: jLang.t('medium'),
								},
							],
						},
			'rest' 	: {
						id 		: 'rest',
						title	: jLang.t('rest'),
						longtitle	: jLang.t('rest:'),
						jsetting: false,
						components	: [
							{
								type: 'touchspin',
								min: 0,
								max: 60,
								val: 30,
								},
							{
								type: 'link',
								data: [jLang.t('sec'), jLang.t('min')], 
								},
							],
						},
			'frequency' 	: {
						id 		: 'frequency',
						title	: jLang.t('freq'),
						longtitle	: jLang.t('frequency:'),
						jsetting: false,
						components	: [
							{
								type: 'touchspin',
								min: 0,
								max: 7,
								val: 3,
								},
							{
								type: 'link',
								data: [jLang.t('hr'), jLang.t('day'), jLang.t('week')], 
								},
							],
						},
			'duration' 	: {
						id 		: 'duration',
						title	: jLang.t('dur'),
						longtitle	: jLang.t('duration:'),
						jsetting: false,
						components	: [
							{
								type: 'touchspin',
								min: 0,
								max: 60,
								val: 30,
								},
							{
								type: 'link',
								data: [jLang.t('sec'), jLang.t('min')], 
								},
							],
						},	
			};
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	//init class
	this.init = function(args){
		if(typeof(args) == 'object'){
			this.containerName = args.container;	
			}
		//set the settings data
		this.initSettingData();
		//draw all the settings at once
		this.drawAllSettingsBar();
		this.drawAllSettingsComponent();
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//draw all setting bar at once
	this.drawAllSettingsBar = function(){
		//draw all base jsetting object
		var str = '';
		for(var o in this.settings){
			//init jsetting obj
			this.settings[o].jsetting = new JSetting({
				id: this.settings[o].id, 
				title: this.settings[o].title,
				manager: this, //ref to the jSettingmanager class
				});
			//append to string
			str += this.settings[o].jsetting.drawBar();
			}
		//append the string
		$('#' + this.containerName).append(str);	
		
		//set the bar action for activation
		for(var o in this.settings){
			//la barre translucide
			this.settings[o].jsetting.setBarActivateAction(0);
			//icon pour passer en mode textbox ou component
			this.settings[o].jsetting.setIconEditingModeAction();
			//action on the click of the txetbox
			this.settings[o].jsetting.setTextBoxAction();
			}
		}	

	//----------------------------------------------------------------------------------------------------------------------*
	//draw all inner setting bar components
	this.drawAllSettingsComponent = function(){
		//write the component depending on which setting
		for(var o in this.settings){
			var str = '';
			//on loop dans les types de components
			for(var p in this.settings[o].components){
				//on check the type of component
				if(this.settings[o].components[p].type == 'touchspin'){
					//touchspin component
					str += this.settings[o].jsetting.drawTouchSpin(this.settings[o].components[p]);
				}else if(this.settings[o].components[p].type == 'link'){
					//link component, pass the arrData has default	
					str += this.settings[o].jsetting.drawSelectLink(this.settings[o].components[p].data);			
				}else if(this.settings[o].components[p].type == 'selectbox'){
					//select box components	
					str += this.settings[o].jsetting.drawSelectBox(this.settings[o].components[p].data, this.settings[o].components[p].selected);			
					}
				}
			//append the string in the inner bar
			this.settings[o].jsetting.insertComponent(str);
			//need to draw before activate the selectink if we have some
			this.settings[o].jsetting.activateSelectLink();
			//need to draw before activate the touchspin if we have some
			this.settings[o].jsetting.activateTouchSpin();
	
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//open the popup setting can be used by jsetting or in the exercise details display
	this.openMainAppzSettingsPopup = function(settingId){
		//on check si certain on des valeurs dans le textBox si oui on les passent en params dans le array
		var arrChanged = [];
		for(var o in this.settings){
			//check si active
			if(this.settings[o].jsetting.isActive){
				//check si est vide
				if(this.settings[o].jsetting.getTextValue() != ''){
					arrChanged[o] = this.settings[o].jsetting.getTextValue();
					}
				}
			}
		//on call le popup du main appz
		this.mainAppz.openSettingsPopup(settingId, this.settings, arrChanged); 
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//ramene le prochain setting pour le popup de jappz
	this.getNextSettingId = function(currentSettingId){
		var tmpID;
		var arrKeys = Object.keys(this.settings);
		for(var i=0; i < arrKeys.length; i++){
			if(arrKeys[i] == currentSettingId){
				//check si trop haut
				if((i + 1) < arrKeys.length){
					tmpID = arrKeys[i + 1];	
				}else{
					tmpID = arrKeys[0];	
					}
				break;
				}
			}
		return tmpID;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//ramene le precedent setting pour le popup de jappz
	this.getPreviousSettingId = function(currentSettingId){
		var tmpID;
		var arrKeys = Object.keys(this.settings);
		for(var i=0; i < arrKeys.length; i++){
			if(arrKeys[i] == currentSettingId){
				//check si trop bas
				if((i - 1) >= 0){
					tmpID = arrKeys[i - 1];	
				}else{
					tmpID = arrKeys[arrKeys.length-1];	
					}
				break;
				}
			}
		return tmpID;
		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.saveModeTextSettings = function(arrChangedSettings){
		//on check ceux qui sont change ou effacer pour les remettre a inactive
		for(var o in this.settings){
			//si se retrouve dans le array de changedalors on a une string pleine
			if(typeof(arrChangedSettings[o]) == 'string'){
				//change la valeur dans la classe
				this.settings[o].jsetting.setTextValue(arrChangedSettings[o]);
				//change la valur du textbox
				this.settings[o].jsetting.setTextBoxValue();
				//on switch en mode text
				this.settings[o].jsetting.isTextMode = false;
				this.settings[o].jsetting.switchToTextMode();
				//on active la case
				this.settings[o].jsetting.switchBarActivation(false);
				
			}else{
				//si etait actif et que c'etait un textbox alors on afface le textbox
				if(this.settings[o].jsetting.isActive && this.settings[o].jsetting.isTextMode){
					//change la valeur dans la classe
					this.settings[o].jsetting.setTextValue('');
					//change la valur du textbox
					this.settings[o].jsetting.setTextBoxValue();
					//on switch en mode text
					this.settings[o].jsetting.isTextMode = true;
					this.settings[o].jsetting.switchToTextMode();
					//on active la case
					this.settings[o].jsetting.switchBarActivation(true);
					}
				}
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//retourne un obj de props:textvalue	
	this.getActiveSettingsToApply = function(){
		var obj = {};
		//on loop check ceux qui sont active et check si la component ou le text box
		for(var o in this.settings){
			//check si active
			if(this.settings[o].jsetting.isActive){
				//check le type text ou component
				if(this.settings[o].jsetting.isTextMode){
					//Object.defineProperty
					obj[o] = this.settings[o].jsetting.getTextValue();
				}else{
					//c'est une component
					obj[o] = this.getStringFromComponent(o);	
					}
			}else{
				//pas actif aors empty string
				obj[o] = '';
				}
			}
		return obj;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//retourne un obj de props:textvalue	
	this.getStringFromComponent = function(key){
		//
		var str = '';
		//check le type de compoenent	
		for(var o in this.settings[key].components){
			//check si on avat quelque chose avant dans la string si plus quune compoenet par jsetting ex: touchspin,selectlink	
			if(str != ''){
				str += ' ';
				}
			//on check the type of component
			if(this.settings[key].components[o].type == 'touchspin'){
				//touchspin component
				str += this.settings[key].jsetting.getTouchSpinValue();
			}else if(this.settings[key].components[o].type == 'link'){
				//link component, pass the arrData has default	
				str += this.settings[key].jsetting.getSelectLinkValue();
			}else if(this.settings[key].components[o].type == 'selectbox'){
				//select box components
				str += this.settings[key].jsetting.getSelectBoxValue();
				}
			}
		//
		return str;
		}


	//----------------------------------------------------------------------------------------------------------------------*
	//reset all jsetting	
	this.resetSettingPanelContainer = function(){
		//set the settings data
		this.initSettingData();
		//clear all 
		$('#' + this.containerName).html('');
		//draw all the settings at once
		this.drawAllSettingsBar();
		this.drawAllSettingsComponent();	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//reset all jsetting	
	this.clear = function(){
		this.resetSettingPanelContainer();
		}
		


	//----------------------------------------------------------------------------------------------------------------------*
	this.debug = function(str){
		console.log(this.className + '--------------------------------------------------------');
		console.log(str);
		}

	
	}


//CLASS END	