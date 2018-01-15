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

		tx['no client selected'] = 'No client';
		tx['no program saved'] = 'No program';
		tx['settings:'] = 'Settings:';
		tx['no name...'] = 'No Name ...';
		tx['new program'] = 'New program';
		tx['print'] = 'Print';
		tx['send'] = 'Send';
		tx['client search'] = 'Client Search';
		tx['client name:'] = 'Client Name :';
		tx['search exercise'] = 'Exercise Search';
		tx['by name:'] = 'By Name :';
		tx['by template:'] = 'Templates :';
		tx['by filter:'] = 'By Filter :';
		tx['search'] = 'search';
		tx['apply to:'] = 'Apply to :';
		tx['all'] = 'ALL';
		tx['selected'] = 'selected';
		tx['unselected'] = 'unselected';
		tx['close'] = 'CLOSE';
		tx['start to type a name'] = 'name hint';
		tx['client name hint'] = 'client name hint';
		tx['type name hint'] = 'name hint';
		tx['exercice name hint'] = 'exercice name hint';
		tx['sets'] = 'SETS';
		tx['reps'] = 'REPS';
		tx['hold'] = 'HOLD';
		tx['weight'] = 'WEIGHT';
		tx['tempo'] = 'TEMPO';
		tx['rest'] = 'REST';
		tx['freq'] = 'freq.';
		tx['dur'] = 'DUR';
		tx['slow'] = 'slow';
		tx['medium'] = 'medium';
		tx['fast'] = 'fast';
		tx['very fast'] = 'very fast'; 
		tx['kg'] = 'KG';
		tx['lbs'] = 'LBS';
		tx['week'] = 'WEEK';
		tx['day'] = 'DAY';
		tx['hr'] = 'HR';
		tx['min'] = 'MIN';
		tx['sec'] = 'SEC';
		tx['languages:'] = 'language:';
		tx['save'] = 'SAVE';
		tx['cancel'] = 'CANCEL';
		tx['save settings'] = 'finished';
		tx['save program'] = 'save program'; 
		tx['program name:'] = 'Program Name:';
		tx['template name:'] = 'template name:';
		tx['new program'] = 'new prog.';
		tx['add new client'] = 'New client';
		tx['firstname:'] = 'firstname:';
		tx['lastname:'] = 'lastname';
		tx['age:'] = 'age:';
		tx['email:'] = 'email:';
		tx['mobile:'] = 'mobile:';
		tx['name:'] = 'name:';
		tx['add new program'] = 'New Program';
		tx['program details'] = 'Program details';
		tx['template details'] = 'Template details';
		tx['new program name'] = 'PROGRAM NAME';
		tx['new template name'] = 'New template name';
		tx['notes:'] = 'notes:';
		tx['client details'] = 'Client details';
		tx['programs:'] = 'programs:';
		tx['no program saved'] = 'no program saved';
		tx['delete'] = 'delete';
		tx['exercise name:'] = 'exercise name:';
		tx['instructions:'] = 'instructions:';
		tx['my instructions'] = 'my instructions';
		tx['physiotec instructions']= 'original instructions';
		tx['programs instructions'] = 'program instructions';
		tx['with bullet'] = 'with bullet';
		tx['no bullet'] = 'no bullet';
		tx['set has my instruction']= 'set as my instructions';
		tx['sets:'] = 'sets:';
		tx['repetition:'] = 'repetition:';
		tx['weight:'] = 'weight:';
		tx['frequency:'] = 'frequency:';
		tx['hold:'] = 'hold:';
		tx['tempo:'] = 'tempo:';
		tx['rest:'] = 'rest:';
		tx['duration:'] = 'duration:';
		tx['add to program'] = 'ADD';
		tx['remove from program'] = 'REMOVE';
		tx['remove'] = 'REMOVE';
		tx['next'] = 'next';
		tx['previous'] = 'previous';
		tx['clear'] = 'clear text';
		tx['description:'] = 'description :';
		tx['change settings'] = 'change settings';
		tx['title and description:']= 'Title and description:';
		tx['error!'] = 'Error!';
		tx['warning!'] = 'Warning!';
		tx['yes'] = 'yes';
		tx['no'] = 'no';
		tx['exit program'] = 'Exit program';
		tx['replace'] = 'replace';
		tx['sorry! video is not available'] = 'Sorry! Video is not available';
		tx['some data will be lost, are you sure you want to exit?'] = 'The program is not saved, are you sure you want to exit?';
		tx['the new program name for <b>%name%</b> have to be different from the ones already existing.'] = 'The program name for <b>%name%</b> have to be different from the ones already existing.';
		tx['the program <b>%programname%</b> for <b>%name%</b> already exist, do you want to replace it by this one?'] = '<b>%programname%</b> already exist, do you want to replace it?';
		tx['duplicate name'] = 'Existing program';
		tx['the program name "<b>%programname%</b>" already exist, press the button "yes" to overwrite the existing program with this one.'] = 'The program name "<b>%programname%</b>" already exist, press the "' + tx['replace'] + '" button to overwrite the existing program with this one, or change the program name and press the "save" button again.';
		tx['duplicate template name'] = 'Existing template';
		tx['the template <b>%templatename%</b> already exist, do you want to replace it by this one?'] = '<b>%templatename%</b> already exist, do you want to replace it?';
		tx['no change'] = 'no change';
		tx['saved'] = 'saved';
		tx['saving'] = 'saving';
		tx['showing '] = 'showing ';
		tx[' of '] = ' of ';
		tx['no result'] = 'no result';
		tx['template name:'] = 'template name:';
		tx['modules:'] = 'module:';
		tx['loading'] = 'loading...';
		tx['select a module'] = 'select a module'; 
		tx['select an option'] = 'select an option'; 
		tx['template edition'] = 'TEMPLATE EDITING';
		tx['save template'] = 'save template';
		tx['save a template copy'] = 'save a template copy';
		tx['an error occured during the template saving, please retry!'] = 'an error occured during the saving, please retry!';
		tx['select a template'] = 'select a template';
		tx['select a template - mine'] = 'MINE'; 
		tx['select a template - all'] = 'ALL'; 
		tx['select a template - license'] = 'CORPORATE'; 
		tx['select a template - brand'] = 'PHYSIOTEC';
		tx['sorry template doesnt exist anymore.'] = 'Sorry template doesn\'t exist anymore!'; 
		tx['save options'] = 'Saving options';
		tx['save as other program'] = 'save a copy of the program';
		tx['save as template'] = 'Save as template';
		tx['select a client for this program'] = 'select a client';
		tx['change the client of this program'] = 'change client';
		tx['add a new client to this program'] = 'add a new client to this program';
		tx['select a different client'] = 'change client';
		tx['save program as'] = 'Save program as';
		tx['client'] = 'Client';
		tx['current client:'] = 'current client:';
		tx['change client'] = 'change client';
		tx['add new client'] = 'Add new client';
		tx['select a client'] = 'select a client';
		tx['modify'] = 'modify';
		tx['save as'] = 'save as';
		tx['warning'] = 'Warning';
		tx['program name cant be empty.'] = 'program name cant be empty.';
		tx['template name cant be empty.'] = 'template name cant be empty.'; 
		tx['you need to select a module.'] = 'you need to select a module.';
		tx['processing'] = 'processing!';
		tx['you must select a client first.'] = 'You must select a client first.';
		tx['you must create a program name first.'] = 'You must create a program name first.';
		tx['change client email at the same time'] = 'Save client email at the same time';
		tx['sets='] = 'sets';
		tx['repetition='] = 'reps';
		tx['weight='] = 'weight';
		tx['frequency='] = 'freq';
		tx['hold='] = 'hold';
		tx['tempo='] = 'tempo';
		tx['rest='] = 'rest'; 
		tx['duration='] = 'dur'; 
		tx['an error occured during the saving, please retry!'] = 'An error occured during the saving, please retry!';
		tx['server error on service call:'] = 'Server Error:'; 
		tx['service error:'] = 'Service Error:'; 
		tx['sub preferences'] = 'My Preferences'; 
		tx['sub account'] = 'My Account'; 
		tx['class debugger'] = 'Bug Report'; 
		tx['sub about'] = 'About Us'; 
		tx['about us'] = 'About Us'; 
		tx['account modification'] = 'Account modification'; 
		tx['username:'] = 'Username:'; 
		tx['primary email:'] = 'Primary email:'; 
		tx['secondary email:'] = 'Secondary email:'; 
		tx['old password:'] = 'Current password:'; 
		tx['new password:'] = 'New password:'; 
		tx['confirm password:'] = 'Confirm password:';
		tx['your password must:'] = 'Your password must:';
		tx['be over 8 characters long']= 'Be a least 9 characters long'; 
		tx['use a combination of'] = 'Use a combination of upper and lower case letters.';
		tx['include at least'] = 'Include at least one numeric and special character(s) within this set !, @, #, $, %, * .';
		tx['example of password:'] = 'Example of password:';
		tx['pswexample'] = 'Anderson01@';
		tx['preferences modification'] = 'Preferences modification'; 
		tx['clinic:'] = 'Clinic:';
		tx['langs:'] = 'Language:'; 
		tx['print summary:'] = 'Print summary:'; 
		tx['email client:'] = 'Email client:'; 
		tx['default module:'] = 'Default module:'; 
		tx['search by module only:'] = 'Search by module only:';
		tx['the language has changed'] = 'The language has changed, you need to reload the application to apply the new language setting.';
		tx['reload now'] = 'Reload the application now?';
		tx['reload'] = 'Restart';
		tx['username'] = 'username';
		tx['password'] = 'password';
		tx['login'] = 'LOGIN';
		tx['sub logout'] = 'Logout';
		tx['id:'] = 'id:';
		tx['about appz branding'] = '<center><p style="background-color: #3D7DCA;"><img src="[{SOURCE}]"></p><p><b>[{VERSION}]</b></p><p>Copyright © 1996-2016 All Rights Reserved</p><br /><p><a href="mailto:support@...">support@...</a></p><br /></center>';
		tx['search all modules'] = 'Search all modules';
		tx['category filter'] = 'Categories';
		tx['tag filter'] = 'Filters';
		tx['no filters'] = 'No Filters';
		tx['show only my exercises']= 'My Exercises';
		tx['show only my favorites']= 'My Favorites';
		tx['show only my modified exercises']= 'My Modified Exercises';
		tx['search by module'] = 'Search by module';


		/*
		//index.php static text
		tx['no client selected']	=	'No client';
		tx['no program saved']		=	'No program';
		tx['settings:']				=	'Settings:';
		tx['no name...']			=	'No Name ...';
		tx['new program']			=	'New program';
		tx['print']					=	'Print';
		tx['send']					=	'Send';
		tx['client search']			=	'Client Search';
		tx['client name:']			=	'Client Name :';
		tx['search exercise']		=	'Exercise Search';
		tx['by name:']				=	'By Name :';
		tx['by template:']			=	'By Template :';
		tx['by filter:']			=	'By Filter :';
		tx['search']				=	'search';
		tx['apply to:']				=	'Apply to :';
		tx['all']					=	'ALL';
		tx['selected']				=	'selected';
		tx['unselected']			=	'unselected';
		tx['close']					=	'CLOSE';
		tx['start to type a name']	=	'name hint';
		tx['client name hint']		=	'client name hint';
		tx['type name hint']		=	'name hint';
		tx['exercice name hint']	=	'exercice name hint';

		//jsetting.js	
		tx['sets'] 		= 	'SETS';
		tx['reps'] 		= 	'REPS';
		tx['hold'] 		= 	'HOLD';
		tx['weight'] 	= 	'WEIGHT';
		tx['tempo'] 	= 	'TEMPO';
		tx['rest'] 		= 	'REST';
		tx['freq'] 		= 	'freq.';
		tx['dur'] 		= 	'DUR';
		tx['slow'] 		= 	'slow';
		tx['medium'] 	= 	'medium';
		tx['fast'] 		= 	'fast';
		tx['very fast'] = 	'very fast'; 
		tx['kg'] 		= 	'KG';
		tx['lbs'] 		= 	'LBS';
		tx['week'] 		= 	'WEEK';
		tx['day'] 		= 	'DAY';
		tx['hr'] 		= 	'HR';
		tx['min'] 		= 	'MIN';
		tx['sec'] 		= 	'SEC';

		//jappz.js
		tx['languages:']			= 	'language:';
		tx['save'] 					= 	'SAVE';
		tx['cancel'] 				= 	'CANCEL';
		tx['save settings']			= 	'finished';
		tx['save program'] 			= 	'save program';	
		tx['program name:'] 		= 	'Program Name:';
		tx['template name:'] 		= 	'template name:';
		tx['new program'] 			= 	'new prog.';
		tx['add new client'] 		= 	'New client';
		tx['firstname:'] 			= 	'firstname:';
		tx['lastname:'] 			= 	'lastname';
		tx['age:'] 					= 	'age:';
		tx['email:'] 				= 	'email:';
		tx['mobile:'] 				= 	'mobile:';
		tx['name:'] 				= 	'name:';
		tx['add new program'] 		= 	'New Program';
		tx['program details'] 		= 	'Program details';
		tx['template details'] 		= 	'Template details';
		tx['new program name'] 		= 	'PROGRAM NAME';
		tx['new template name'] 	= 	'New template name';
		tx['notes:'] 				= 	'notes:';
		tx['client details'] 		= 	'Client details';
		tx['programs:'] 			= 	'programs:';
		tx['no program saved'] 		= 	'no program saved';
		tx['delete'] 				= 	'delete';
		tx['exercise name:'] 		= 	'exercise name:';
		tx['instructions:'] 		= 	'instructions:';
		tx['my instructions'] 		= 	'my instructions';
		tx['physiotec instructions']= 	'original instructions';
		tx['programs instructions'] = 	'program instructions';
		tx['with bullet'] 			= 	'with bullet';
		tx['no bullet'] 			= 	'no bullet';
		tx['set has my instruction']= 	'set has my instructions';
		tx['sets:'] 				= 	'sets:';
		tx['repetition:'] 			= 	'repetition:';
		tx['weight:'] 				= 	'weight:';
		tx['frequency:'] 			= 	'frequency:';
		tx['hold:'] 				= 	'hold:';
		tx['tempo:'] 				= 	'tempo:';
		tx['rest:'] 				= 	'rest:';
		tx['duration:'] 			= 	'duration:';
		tx['add to program'] 		= 	'ADD';
		tx['remove from program'] 	= 	'REMOVE';
		tx['remove'] 				= 	'REMOVE';
		tx['next'] 					= 	'next';
		tx['previous'] 				= 	'previous';
		tx['clear'] 				= 	'clear text';
		tx['description:'] 			= 	'description :';
		tx['change settings'] 		= 	'change settings';
		tx['title and description:']= 	'Title and description:';
		tx['error!']				= 	'Error!';
		tx['warning!']				= 	'Warning!';
		tx['yes']					= 	'yes';
		tx['no']					= 	'no';
		tx['exit program']			= 	'Exit program';
		tx['replace']				= 	'replace';
		tx['sorry! video is not available']	= 'Sorry! Video is not available';

		tx['some data will be lost, are you sure you want to exit?'] = 'The program is not saved, are you sure you want to exit?';
		tx['the new program name for <b>%name%</b> have to be different from the ones already existing.'] =	'The program name for <b>%name%</b> have to be different from the ones already existing.';
		tx['the program <b>%programname%</b> for <b>%name%</b> already exist, do you want to replace it by this one?'] = '<b>%programname%</b> already exist, do you want to replace it?';
		tx['duplicate name']		= 'Existing program';
		tx['the program name "<b>%programname%</b>" already exist, press the button "yes" to overwrite the existing program with this one.'] = 'The program name "<b>%programname%</b>" already exist, press the "' + tx['replace'] + '" button to overwrite the existing program with this one, or change the program name and press the "save" button again.';
		tx['duplicate template name']		= 'Existing template';
		tx['the template <b>%templatename%</b> already exist, do you want to replace it by this one?'] = '<b>%templatename%</b> already exist, do you want to replace it?';

		tx['no change']				= 	'no change';
		tx['saved']					= 	'saved';
		tx['saving']				= 	'saving';
		tx['showing ']				= 	'showing ';
		tx[' of ']					= 	' of ';
		tx['no result']				= 	'no result';

		//template
		tx['template name:']		= 	'template name:';
		tx['modules:']				= 	'module:';
		tx['loading']				= 	'loading...';
		tx['select a module']		= 	'select a module';	
		tx['select an option']		= 	'select an option';	
		tx['template edition']		= 	'TEMPLATE EDITING';
		tx['save template']			= 	'save template';
		tx['save a template copy']	= 	'save a template copy';
		tx['an error occured during the template saving, please retry!']	= 	'an error occured during the saving, please retry!';
		tx['select a template']		= 	'select a template';
		tx['select a template - mine']		= 	'my template';		
		tx['select a template - all']		= 	'clinic template';		
		tx['select a template - license']	= 	'corporate template';		
		tx['select a template - brand']		= 	'brand template';
		tx['sorry template doesnt exist anymore.']	= 'Sorry template doesn\'t exist anymore!';				

		//popup save
		tx['save options']			= 'Saving options';
		tx['save as other program']	= 'save a copy of the program';
		tx['save as template']		= 'Save as template';
		tx['select a client for this program']	= 'select a client';
		tx['change the client of this program']	= 'change client';
		tx['add a new client to this program']	= 'add a new client to this program';
		tx['select a different client']			= 'change client';
		tx['save program as']		= 'Save program as';
		tx['client']				= 'Client';
		tx['current client:']		= 'current client:';
		tx['change client']			= 'change client';
		tx['add new client']		= 'Add new client';
		tx['select a client']		= 'select a client';
		tx['modify']				= 'modify';
		tx['save as']				= 'save as';
		tx['warning']				= 'Warning';
		tx['program name cant be empty.']		= 'program name cant be empty.';
		tx['template name cant be empty.']		= 'template name cant be empty.';	
		tx['you need to select a module.']		= 'you need to select a module.';
		tx['processing']			= 'processing!';
		
		//print and send email
		tx['you must select a client first.']	= 'You must select a client first.';
		tx['you must create a program name first.']	= 'You must create a program name first.';
		tx['change client email at the same time']	= 'Save client email at the same time';

		//pour le format content-view la key de l'object plus un =
		tx['sets='] 				= 	'sets';
		tx['repetition='] 			= 	'reps';
		tx['weight='] 				= 	'weight';
		tx['frequency='] 			= 	'freq';
		tx['hold='] 				= 	'hold';
		tx['tempo='] 				= 	'tempo';
		tx['rest='] 				= 	'rest';	
		tx['duration='] 			= 	'dur';	

		//errors
		tx['an error occured during the saving, please retry!'] = 	'An error occured during the saving, please retry!';
		tx['server error on service call:'] = 'Server Error:';	
		tx['service error:'] = 'Service Error:';		
		
		//sub menu
		tx['sub preferences']		=	'My Preferences';	
		tx['sub account']			=	'My Account';	
		tx['class debugger']		=	'Bug Report';	
		tx['sub about']				=	'About Us';	
		tx['about us']				=	'About Us';	

		//options account
		tx['account modification']		=	'Account modification';	
		tx['username:']					=	'Username:';	
		tx['primary email:']			=	'Primary email:';	
		tx['secondary email:']			=	'Secondary email:';		
		tx['old password:']				=	'Current password:';		
		tx['new password:']				=	'New password:';		
		tx['confirm password:']			=	'Confirm password:';
		//
		tx['your password must:']		=	'Your password must:';
		tx['be over 8 characters long']=	'Be a least 9 characters long';		
		tx['use a combination of']		=	'Use a combination of upper and lower case letters.';
		tx['include at least']			=	'Include at least one numeric and special character(s) within this set !, @, #, $, %, * .';
		tx['example of password:']		=	'Example of password:';
		tx['pswexample']				=	'Anderson01@';
		
		//options prefs
		tx['preferences modification']	=	'Preferences modification';	
		tx['clinic:']					=	'Clinic:';
		tx['langs:']					=	'Language:';		
		tx['print summary:']			=	'Print summary:';		
		tx['email client:']				=	'Email client:';		
		tx['default module:']			=	'Default module:';		
		tx['search by module only:']	=	'Search by module only:';
		tx['the language has changed']	= 	'The language has changed, you need to reload the application to apply the new language setting.';
		tx['reload now']				= 	'Reload the application now?';
		tx['reload']					= 	'Restart';

		//login
		tx['username']					= 	'username';
		tx['password']					= 	'password';
		tx['login']						= 	'LOGIN';
		tx['sub logout']				= 	'Logout';
		tx['id:']						= 	'id:';

		//for branding purpose
		tx['about appz branding']		= 	'<center><p style="background-color: #3D7DCA;"><img src="[{SOURCE}]"></p><p><b>[{VERSION}]</b></p><p>Copyright © 1996-2016 All Rights Reserved</p><br /><p><a href="mailto:support@...">support@...</a></p><br /></center>';

		//new search options
		tx['search all modules']	= 	'Search all modules';
		tx['category filter']		= 	'Categories';
		tx['tag filter']			= 	'Filters';
		tx['no filters']			=	'No Filters';
		tx['show only my exercises']= 	'My Exercises';
		tx['show only my favorites']= 	'My Favorites';
		tx['show only my modified exercises']= 	'My Modified Exercises';
		tx['search by module']		= 	'Search by module';
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