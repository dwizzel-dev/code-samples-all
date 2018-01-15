/*

Author: DwiZZel
Date: 06-01-2016
Version: 3.1.0 BUILD X.X
Notes:	
		
*/

//----------------------------------------------------------------------------------------------------------------------
    
function JOptions(args){

	//ref class name
	this.className = 'JOptions';
	//args
	this.mainAppz = args.mainappz;
	//le comm class	
	this.jcomm = args.jcomm;
	//pid comm callback
	this.lastPid = -1;
	//sub menu elements
	this.arrSubMenu = [];
	//menus
	this.strMenuName = '#main-site-menu';
	this.strSubMenuName = '#main-site-sub-menu';
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	//main sub menu text and actions
	this.init = function(){
		this.debug('init()');
		
		this.arrSubMenu['prefs'] = {
			text:jLang.t('sub preferences'), 
			action:'prefs',
			title:jLang.t('preferences modification'),
			};
		this.arrSubMenu['account'] = {
			text:jLang.t('sub account'), 
			action:'account',
			title:jLang.t('account modification'),
			};
		this.arrSubMenu['about'] = {
			text:jLang.t('sub about'), 
			action:'about',
			title:jLang.t('about us'),
			};
		if(gDebug != '0'){
			this.arrSubMenu['debugger'] = {
				text:jLang.t('class debugger'), 
				action:'debug',
				title:jLang.t('class debugger'),
				};
			}
		this.arrSubMenu['logout'] = {
			text:jLang.t('sub logout'), 
			action:'logout',
			title:jLang.t('logout'),
			};	
		

		//on va chercher la hauteur de la barre du haut
		var h = parseInt($('#main-menu-top').outerHeight(true)) - 1;
		//change la position du sub-menu
		$(this.strSubMenuName).css({
			top: h + 'px',
			});
		//on rajoute les el au sub-menu
		var str = '<ul>';
		for(var o in this.arrSubMenu){
			str += '<li open-action="' + this.arrSubMenu[o].action + '">' + this.arrSubMenu[o].text + '</li>';
			}
		str += '</ul>';
		str += '<div id="menu-mask-close" style="position:relative;height:100%;width:100%;"></div>';

		//rajoute le contenu au menu
		$(this.strSubMenuName).html(str);

		//le action sur le main-site-menu
		$('#menu-mask-close').data('parentclass', this);
		$('#menu-mask-close').click(function(e){
			e.preventDefault(); 
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				//on ouvre la fenetre de sub-menu
				oTmp.openSiteMenu();
				}
			});

		//le action sur le main-site-menu
		$(this.strMenuName).data('parentclass', this);
		$(this.strMenuName).click(function(e){
			e.preventDefault(); 
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				//on ouvre la fenetre de sub-menu
				oTmp.openSiteMenu();
				}
			});

		//les actions sur les subs menu
		$(this.strSubMenuName + ' > UL > LI').data('parentclass', this);
		$(this.strSubMenuName + ' > UL > LI').click(function(e){
			e.preventDefault(); 
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				//ouvre la section
				oTmp.openWindowFromSubMenu($(this).attr('open-action'));
				}
			});


		};

	//----------------------------------------------------------------------------------------------------------------------*
	this.resizeEvent = function(){
		//on va chercher la hauteur de la barre du haut
		var h = parseInt($('#main-menu-top').outerHeight(true)) - 1;
		//change la position du sub-menu
		$(this.strSubMenuName).css({
			top: h + 'px',
			});
		};
	


	//----------------------------------------------------------------------------------------------------------------------*
	//ouvre le main-site-sub-menu
	this.openSiteMenu = function(){
		this.debug('openSiteMenu()');

		//check si est ouvert ou ferme
		var bOpen = parseInt($(this.strSubMenuName).attr('showed'));
		//
		if(bOpen){
			//menu icon
			$(this.strMenuName + ' > A > IMG').removeClass('rotate');
			//top left name
			$(this.strSubMenuName).animate(
					{
						opacity: 0.0,
						}, 
					{
						queue:false,
						duration:300, 
						progress:function(){
							$('#main-client-logo').css('opacity',$(this).css('opacity'));
							$('#main-client-name-text').css('opacity',(1 - $(this).css('opacity')));
							},
						start:function(){
							$('#main-client-name-text').css('display','block');
							},
						complete:function(){
							$('#main-client-logo').css('display','none');
							$(this).css('display','none');
							$(this).attr('showed', 0);
							}
						}
				);
		}else{
			//menu icon
			$(this.strMenuName + ' > A > IMG').addClass('rotate');
			//top left name
			$(this.strSubMenuName).animate(
					{
						opacity: 1.0,
						}, 
					{
						queue:false,
						duration:300,
						progress:function(){
							$('#main-client-logo').css('opacity',$(this).css('opacity'));
							$('#main-client-name-text').css('opacity',(1 - $(this).css('opacity')));	
							},
						start:function(){
							$('#main-client-logo').css('display','block');
							$(this).css('display','block');
							},
						complete:function(){
							$(this).attr('showed', 1);	
							$('#main-client-name-text').css('display','none');
							}
						}	
				);
			}

		$('#main-client-logo').unbind();
		$('#main-client-logo').data('parentclass', this);
		$('#main-client-logo').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				//close popup
				oTmp.openSiteMenu();
				}
			});	

		};

	//----------------------------------------------------------------------------------------------------------------------*
	//ouvre le main-site-sub-menu
	this.openWindowFromSubMenu = function(strSection){
		this.debug('openWindowFromSubMenu(' + strSection + ')');		

		switch(strSection){
			case 'account': 
				this.getAccountOptions();
				break;
			case 'prefs': 
				this.getPreferences();
				break;
			case 'logout': 
				this.doLogout();
				break;
			case 'debug': 
				this.showBugReport();
				break;
			case 'about': 
				this.showAboutUs();
				break;
			default:
				break;
			}

		};


	//----------------------------------------------------------------------------------------------------------------------*
	//ferme la session
	this.doLogout = function(){
		this.debug('doLogout()');	

		var objServer = {
			};
		var objLocal = {
			submenu: this.arrSubMenu['logout'],
			};

		//show un loader car on va chercher le data sur le serveur
		this.mainAppz.showLoader(true, this.strSubMenuName + ' > UL > LI[open-action=' + objLocal.submenu.action + ']', 0, 0);
		this.mainAppz.doLogout();
		};


	//----------------------------------------------------------------------------------------------------------------------*
	//ouvre la window avec les preferences
	this.getPreferences = function(){
		this.debug('getPreferences()');	

		var objServer = {
			};
		var objLocal = {
			submenu: this.arrSubMenu['prefs'],
			};

		//on ca chercher les preferences via le service
		this.lastPid = this.jcomm.process(this, 'options', 'get-preferences', objServer, objLocal);
		//show un loader car on va chercher le data sur le serveur
		this.mainAppz.showLoader(true, this.strSubMenuName + ' > UL > LI[open-action=' + objLocal.submenu.action + ']', 0, 0);

		};	

	//----------------------------------------------------------------------------------------------------------------------*
	//ouvre la window avec les preferences
	this.getPreferencesReturnFromServer = function(obj, extraObj){
		this.debug('getPreferencesReturnFromServer(' + obj + ', ' + extraObj + ')');

		//on enleve le loader
		this.mainAppz.removeLoader(this.strSubMenuName + ' > UL > LI[open-action=' + extraObj.submenu.action + ']', extraObj.submenu.text);
		//show la window
		this.showPreferences(obj);
		//close le sub menu
		this.openSiteMenu();

		};

	//----------------------------------------------------------------------------------------------------------------------*
	//ouvre la window avec les preferences
	this.showPreferences = function(obj){
		this.debug('showPreferences(' + obj + ')');

		var str = '';
		//save or cancel action
		str += '<div class="popup-tools">';
		str += '<div>';
		str += '<a href="#" class="butt med" id="butt-save-modification">' + jLang.t('save') + '</a>';
		str += '<a href="#" class="butt med" id="butt-cancel-modification">' + jLang.t('cancel') + '</a>';
		str += '</div>';
		str += '</div>';
		//
		str += '<div id="popup-content">';
		//le nom du titre correspond au nom du setting sur lequel il a clique ou quand il fait un next ou prev d'un setting a lautre
		str += '<h1 class="h1-close-popup">' + this.arrSubMenu['prefs'].title + '</h1>';
		str += '<div class="popup-form">';
		
		//page content
		//clinics
		str += '<p><b>' + jLang.t('clinic:') + '</b></p>';
		str += '<p><select id="input-clinic" class="select-1 search">';
		for(var o in obj.clinic){
			str += '<option value="' + o + '"';
			if(o == obj.clinicselected){
				str += ' selected ';
				}
			str += '>' + obj.clinic[o] + '</option>';
			}
		str += '</select></p>';
		
		
		//laguages
		str += '<p><b>' + jLang.t('langs:') + '</b></p>';
		str += '<p><select id="input-languages" class="select-1 search">';
		for(var o in obj.lang){
			str += '<option value="' + o + '"';
			/*
			if(o == obj.langselected){
				str += ' selected ';
				}
			*/
			if(o == gLocaleLang){
				str += ' selected ';
				}
			str += '>' + obj.lang[o] + '</option>';
			}
		str += '</select></p>';

		//print summary
		str += '<p><b>' + jLang.t('print summary:') + '</b></p>';
		str += '<p>';
		for(var o in obj.print){
			str += '<input type="radio" class="radio-1" id="input-print-summary" name="input-print-summary" value="' + o + '"';
			if(o == obj.printselected){
				str += ' checked ';
				}
			str += '>' + obj.print[o];
			}
		str += '</p>';

		//email
		str += '<p><b>' + jLang.t('email client:') + '</b></p>';
		str += '<p>';
		for(var o in obj.email){
			str += '<input type="radio" class="radio-1" id="input-email-client" name="input-email-client" value="' + o + '"';
			if(o == obj.emailselected){
				str += ' checked ';
				}
			str += '>' + obj.email[o];
			}
		str += '</p>';

		//modules
		str += '<p><b>' + jLang.t('default module:') + '</b></p>';
		str += '<p><select id="input-default-module" class="select-1 search">';
		for(var o in obj.module){
			str += '<option value="' + obj.module[o].id + '"';
			if(obj.module[o].id == obj.moduleselected){
				str += ' selected ';
				}
			str += '>' + obj.module[o].name + '</option>';
			}
		str += '</select></p>';

		//search
		str += '<p><b>' + jLang.t('search by module only:') + '</b></p>';
		str += '<p>';
		for(var o in obj.search){
			str += '<input type="radio" class="radio-1" id="input-search-by-module" name="input-search-by-module" value="' + o + '"';
			if(o == obj.searchselected){
				str += ' checked ';
				}
			str += '>' + obj.search[o];
			}
		str += '</p>';


		str += '</div>'; //close popup-form
		//
		str += '</div>'; //close popup-content
		//popup
		$('#main-popup-window').html(str);
		//
		this.mainAppz.openPopup();
		//
		$('#butt-save-modification, #butt-cancel-modification').data('parentclass', this);
		//save
		$('#butt-save-modification').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				//save setting form popup
				oTmp.savePreferences();
				}
			});
		//cancel
		$('#butt-cancel-modification').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				//close popup
				oTmp.mainAppz.closePopup();
				}
			});	

		
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//save les preferences
	this.savePreferences = function(){
		this.debug('savePreferences()');		

		//le loader
		this.mainAppz.showLoader(true, '#butt-save-modification', 0, 0);

		//get les infos du formulaire
		var objServer = {
			lang: $('#input-languages').val(),
			print: $('#input-print-summary:checked').val(),
			email: $('#input-email-client:checked').val(),
			module: $('#input-default-module').val(),
			search: $('#input-search-by-module:checked').val(),
			clinic: $('#input-clinic').val(),
			};
		var objLocal = {
			lang: $('#input-languages').val(),
			module: $('#input-default-module').val(),	
			};

		//on envoi au serveur	
		this.lastPid = this.jcomm.process(this, 'options', 'save-preferences', objServer, objLocal);


		};

	//----------------------------------------------------------------------------------------------------------------------*
	//save les preferences
	this.savePreferencesReturnFromServer = function(obj, extraObj){
		this.debug('savePreferencesReturnFromServer(' + obj + ', ' + extraObj + ')');
		
		//on enleve le loader
		this.mainAppz.removeLoader('#butt-save-modification', jLang.t('save'));

		//check si fonctionne ou pas
		if(obj == '1'){
			//ok alors on ferme le popup
			this.mainAppz.closePopup();
			//check si a change de langue
			if(gLocaleLang != extraObj.lang){
				//on demande au user si il veut reloader l'application , 
				var str = jLang.t('the language has changed') + '<br><br><b>' + jLang.t('reload now') + '</b>';
				this.mainAppz.reloadApplication(str, extraObj.lang);
				}
			//set le default module
			if(typeof(extraObj.module) != 'undefined'){
				this.mainAppz.juser.setModuleId(parseInt(extraObj.module));
				}
			//on reload la recherche car les template ne seront plus les memes
			this.mainAppz.resetSearchWindow();
			
		}else{
			//on pop un e alert generic
			this.mainAppz.openAlert('alert', jLang.t('error!'), jLang.t('an error occured during the saving, please retry!'), false);
			}

		};			


	//----------------------------------------------------------------------------------------------------------------------*
	//ouvre la window avec les preferences
	this.getAccountOptions = function(){
		this.debug('getAccountOptions()');		

		var objServer = {
			//id: this.mainAppz.juser.getId(),
			//username: this.mainAppz.juser.getUserName(),
			/*sessionid: this.mainAppz.juser.getSessionId(),*/
			};
		var objLocal = {
			submenu: this.arrSubMenu['account'],
			};

		//on ca chercher les preferences via le service
		this.lastPid = this.jcomm.process(this, 'options', 'get-account-options', objServer, objLocal);
		//show un loader car on va chercher le data sur le serveur
		this.mainAppz.showLoader(true, this.strSubMenuName + ' > UL > LI[open-action=' + objLocal.submenu.action + ']', 0, 0);

		};


	//----------------------------------------------------------------------------------------------------------------------*
	//ouvre la window avec les preferences
	this.getAccountOptionsReturnFromServer = function(obj, extraObj){
		this.debug('getAccountOptionsReturnFromServer(' + obj + ', ' + extraObj + ')');

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
		this.mainAppz.removeLoader(this.strSubMenuName + ' > UL > LI[open-action=' + extraObj.submenu.action + ']', extraObj.submenu.text);
		//close le sub menu
		this.openSiteMenu();
		//ok then continue with showing otpions
		if(bContinue){
			//show la window
			this.showAccountOptions(obj);
			}

		};

	//----------------------------------------------------------------------------------------------------------------------*
	//ouvre la window avec les preferences
	this.showAccountOptions = function(obj){
		this.debug('showAccountOptions(' + obj + ')');
		
		var str = '';
		//save or cancel action
		str += '<div class="popup-tools">';
		str += '<div>';
		str += '<a href="#" class="butt med" id="butt-save-modification">' + jLang.t('save') + '</a>';
		str += '<a href="#" class="butt med" id="butt-cancel-modification">' + jLang.t('cancel') + '</a>';
		str += '</div>';
		str += '</div>';
		//
		str += '<div id="popup-content">';
		//le nom du titre correspond au nom du setting sur lequel il a clique ou quand il fait un next ou prev d'un setting a lautre
		str += '<h1 class="h1-close-popup">' + this.arrSubMenu['account'].title + '</h1>';
		str += '<div class="popup-form">';
		
		//page content
		//username
		str += '<p><b>' + jLang.t('username:') + '</b></p>';
		str += '<p><input type="text" id="input-username" value="' + obj.username + '" class="input-1 large"></p>';
		//primary email
		str += '<p><b>' + jLang.t('primary email:') + '</b></p>';
		str += '<p><input type="text" id="input-email-primary" value="' + obj.emailprimary + '" class="input-1 large"></p>';
		//secondary email
		str += '<p><b>' + jLang.t('secondary email:') + '</b></p>';
		str += '<p><input type="text" id="input-email-secondary" value="' + obj.emailsecondary + '" class="input-1 large"></p>';
		
		//message du type de password
		str += '<div class="message-box">';
		str += jLang.t('your password must:');
		str += '<ul>';
		str += '<li>' + jLang.t('be over 8 characters long') + '</li>';
		str += '<li>' + jLang.t('use a combination of') + '</li>';
		str += '<li>' + jLang.t('include at least') + '</li>';
		str += '</ul>';
		str += '<p>' + jLang.t('example of password:') + '&nbsp;' + jLang.t('pswexample') + '</p>';
		str += '</div>';

		//old password
		str += '<p><b>' + jLang.t('old password:') + '</b></p>';
		str += '<p><input type="password" id="input-password-old" value="" class="input-1 large"></p>';
		//new password
		str += '<p><b>' + jLang.t('new password:') + '</b></p>';
		str += '<p><input type="password" id="input-password-new" value="" class="input-1 large"></p>';
		//password confirm new
		str += '<p><b>' + jLang.t('confirm password:') + '</b></p>';
		str += '<p><input type="password" id="input-password-confirm" value="" class="input-1 large"></p>';

		
		str += '</div>'; //close popup-form
		//
		str += '</div>'; //close popup-content
		//popup
		$('#main-popup-window').html(str);
		//
		this.mainAppz.openPopup();
		//
		$('#butt-save-modification, #butt-cancel-modification').data('parentclass', this);
		//save
		$('#butt-save-modification').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				//save options form popup
				oTmp.saveAccountOptions();
				}
			});
		//cancel
		$('#butt-cancel-modification').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				//close popup
				oTmp.mainAppz.closePopup();
				}
			});	

		
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//save les accounts potions
	this.saveAccountOptions = function(){
		this.debug('saveAccountOptions()');		

		//le loader
		this.mainAppz.showLoader(true, '#butt-save-modification', 0, 0);

		//get les infos du formulaire
		var objServer = {
			username: $('#input-username').val(),
			oldpsw: $('#input-password-old').val(),
			newpsw: $('#input-password-new').val(),
			confirmpsw: $('#input-password-confirm').val(),
			emailprimary: $('#input-email-primary').val(),
			emailsecondary: $('#input-email-secondary').val(),	
			};
		var objLocal = {
			};

		//on envoi au serveur	
		this.lastPid = this.jcomm.process(this, 'options', 'save-account-options', objServer, objLocal);

		};


	//----------------------------------------------------------------------------------------------------------------------*
	//save les account options return
	this.saveAccountOptionsReturnFromServer = function(obj, extraObj){
		this.debug('saveAccountOptionsReturnFromServer(' + obj + ', ' + extraObj + ')');
		
		//on enleve le loader
		this.mainAppz.removeLoader('#butt-save-modification', jLang.t('save'));

		//check si fonctionne ou pas
		if(obj.ok == '1'){
			//ok alors on ferme le popup
			this.mainAppz.closePopup();
		}else{
			if(typeof(obj.msg) == 'string' && obj.msg != ''){
				this.mainAppz.openAlert('alert', jLang.t('error!'), obj.msg, false);
			}else{
				//on pop un e alert generic
				this.mainAppz.openAlert('alert', jLang.t('error!'), jLang.t('an error occured during the saving, please retry!'), false);
				}
			}

		};


	//----------------------------------------------------------------------------------------------------------------------*
	//ouvre la window du bug report
	this.showBugReport = function(){
		this.debug('showBugReport()');

		//close le sub menu
		this.openSiteMenu();		
		/*
		var str = '';
		//save or cancel action
		str += '<div class="popup-tools">';
		str += '<div>';
		str += '<a href="#" class="butt med" id="butt-send-modification">' + jLang.t('send') + '</a>';
		str += '<a href="#" class="butt med" id="butt-cancel-modification">' + jLang.t('cancel') + '</a>';
		str += '</div>';
		str += '</div>';
		//
		str += '<div id="popup-content">';
		//le nom du titre correspond au nom du setting sur lequel il a clique ou quand il fait un next ou prev d'un setting a lautre
		str += '<h1 class="h1-close-popup">' + this.arrSubMenu['debugger'].title + '</h1>';
		str += '<div class="popup-form">';
		
		//page content
		str += '</div>'; //close popup-form
		//
		str += '</div>'; //close popup-content
		//popup
		$('#main-popup-window').html(str);
		//
		this.mainAppz.openPopup();
		//
		$('#butt-save-modification, #butt-cancel-modification').data('parentclass', this);
		//save
		$('#butt-save-modification').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				//send the bug report
				}
			});
		//cancel
		$('#butt-cancel-modification').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				//close popup
				oTmp.mainAppz.closePopup();
				}
			});	


		//classes to debug
		*/
		console.log(this.mainAppz);
		console.log(this.mainAppz.jslider);
		console.log(this.mainAppz.jutils);
		console.log(this.mainAppz.jcomm);
		console.log(this.mainAppz.jsettingmanager);
		console.log(this.mainAppz.jclientmanager);
		console.log(this.mainAppz.jprogram);
		console.log(this.mainAppz.jsearch);
		console.log(this.mainAppz.jtemplate);
		console.log(this.mainAppz.juser);
		console.log(this.mainAppz.jautocomplete);
		console.log(this.mainAppz.joptions);

		
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//ouvre la window du bug report
	this.showAboutUs = function(){
		this.debug('showAboutUs()');

		//close le sub menu
		this.openSiteMenu();		
		
		var str = '';
		//save or cancel action
		str += '<div class="popup-tools">';
		str += '<div>';
		str += '<a href="#" class="butt med" id="butt-cancel-modification">' + jLang.t('cancel') + '</a>';
		str += '</div>';
		str += '</div>';
		//
		str += '<div id="popup-content">';
		//le nom du titre correspond au nom du setting sur lequel il a clique ou quand il fait un next ou prev d'un setting a lautre
		//str += '<h1 class="h1-close-popup">' + this.arrSubMenu['about'].title + '</h1>';
		str += '<div class="popup-form">';
		
		//le about us text
		str += jLang.t('about appz branding').replace('[{SOURCE}]', gServerPath + 'images/' + gBrand + '/logo.png').replace('[{VERSION}]', this.mainAppz.getVersion());

		//a reload button
		str += '<div><center><a href="#" class="butt" id="butt-reload-application">' + jLang.t('reload') + '</a></center></div>';
		
		//page content
		str += '</div>'; //close popup-form
		//
		str += '</div>'; //close popup-content
		//popup
		$('#main-popup-window').html(str);
		//
		this.mainAppz.openPopup();
		
		//
		$('#butt-reload-application').data('parentclass', this);
		//reload
		$('#butt-reload-application').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				//close popup
				//on demande au user si il veut reloader l'application , 
				var str = '<b>' + jLang.t('reload now') + '</b>';
				oTmp.mainAppz.reloadApplication(str, gLocaleLang);
				}
			});
		//
		$('#butt-cancel-modification').data('parentclass', this);
		//cancel
		$('#butt-cancel-modification').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				//close popup
				oTmp.mainAppz.closePopup();
				}
			});	

		


	
		};


	//----------------------------------------------------------------------------------------------------------------------*
	//deconnecte le client
	/*
	this.doLogout = function(){
		this.debug('doLogout()');	

		//close le sub menu
		this.openSiteMenu();
		//le loader
		this.mainAppz.showLoader(true, this.strSubMenuName + ' > UL > LI[open-action=' + this.arrSubMenu['logout'].logout + ']', 0, 0);
		//on call le logout du main Appz
		this.mainAppz.doLogout();
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
			}else{
				if(obj.section == 'options'){
					if(obj.service == 'get-preferences'){ 
						this.getPreferencesReturnFromServer(obj.data, extraobj);
					}else if(obj.service == 'get-account-options'){
						this.getAccountOptionsReturnFromServer(obj.data, extraobj);
					}else if(obj.service == 'save-preferences'){
						this.savePreferencesReturnFromServer(obj.data, extraobj);
					}else if(obj.service == 'save-account-options'){
						this.saveAccountOptionsReturnFromServer(obj.data, extraobj);
					}else{
						//
						}
					}
				}
			//}
		};


	//----------------------------------------------------------------------------------------------------------------------*
	this.debug = function(str){
		jDebug.show(this.className + '::' + str);
		};

		
	}





