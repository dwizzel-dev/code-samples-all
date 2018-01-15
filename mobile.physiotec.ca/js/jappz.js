/*

Author: DwiZZel
Date: 21-04-2016
Version: V.3.0 BUILD 115
*/
//----------------------------------------------------------------------------------------------------------------------

function JAppz(){

	this.className = 'JAppz';

	//on set une reference qui sera utilise partout pour le scope des classes
	$(document).data('jappzclass', this);

	//args with other classes
	this.jslider = new JSlider(); //no class dependencies;
	this.jutils = new JUtils(); //no class dependencies;
	this.jcomm = new JComm({'mainappz':this}); //dependencies
	this.jclientmanager = new JClientManager({'mainappz':this, 'jcomm':this.jcomm}); //dependencies
	this.jsettingmanager = new JSettingManager({'mainappz':this}); //dependencies
	this.juser = new JUser({'mainappz':this, 'jcomm':this.jcomm}); //dependencies
	this.jsearch = new JSearch({'mainappz':this, 'jcomm':this.jcomm}); //dependencies
	this.jprogram = new JProgram({'mainappz':this, 'jcomm':this.jcomm}); //dependencies
	this.jtemplate = new JTemplate({'mainappz':this, 'jcomm':this.jcomm}); //dependencies	
	this.jautocomplete = new JAutoComplete({'mainappz':this, 'jcomm':this.jcomm}); //dependencies	
	this.joptions = new JOptions({'mainappz':this, 'jcomm':this.jcomm}); //dependencies		

	//version from index.php
	this.version = $('#appz-version').text();
	
	// use to cancel the timer for the hit test 
	// when drag a box from listing-programs to the bottom 
	// or top of the container 
	// to make the scroll bar move
	this.timerHitTestPanScroll;
	
	//the obj containg infos about the last selected hover box in listing-programs
	this.lastSelectedForDropId = -1;
	
	//containing infos about the delatX/Y on panmove to compense animation on panstart
	this.lastDeltaPosition = {};
	
	//zoom factor array of obj
	this.zoomFactor = {};
	
	//zoom factor index
	this.zoomFactorLastIndex = 1;

	//zoom factor index
	this.zoomFactorSearchIndex = 0;

	//pour savoir si affichage en content-view ou pas
	this.contentViewDisplay = false;	

	//setting of the box
	this.boxSettings;

	//basic speed of animnation
	this.basicAnimSpeed = 600;

	//default quand pas images
	this.defaultExerciceImageSrc = gServerPath + 'images/' + gBrand + '/default-exercice.png';
	
	//container size
	this.containerSize = {
		h:0, 
		w:0
		};

	//les print link templatye
	this.printTemplate = false;	

	//css style from stylesheet lecture
	this.cssStyle = -1;
	this.cssExtraStyle = -1;
	
	//par defaut si la case a input-exercice-set-has-instruction est coché pour tous ou pas
	this.bSetHasMyInstruction = false;

	//le thread du Search	JAPPZ.displaySearchBoxesHasContentViewThreadLoop
	this.thDisplayHasContentViewLoop;	

	//containing infos about the delatX/Y on panmove to compense animation on panstart	
	this.lastSelectedSearchModule = -1;
	
	//----------------------------------------------------------------------------------------------------------------------
	this.init = function(){
		this.debug('init()');	
		
		//change browser title tab
		document.title = this.version;
	
		//container size
		this.containerSize = {
			w: $('#main-container').innerWidth(),
			h: $('#main-container').innerHeight(),
			}
		
		//loaded needed images if offline
		this.loadNeededImage();
	
		//action on butt	
		this.initButtonAction();	
		
		//init the setting panel
		this.initSettingPanel();

		//init le autocomplete
		this.initAutoComplete();

		//reset all layers and inner-layer
		this.resizeAllElements();

		//init le main sub menu
		this.joptions.init();
		
		//hide les settings
		this.closeToolsLayerSettings();
				
		//routine
		this.openLayer('client');	 
		
		//drag img bug
		this.fixImage();

		//resize event
		$(window).resize(this.resizeAllElements.bind(this));
	
		//si pas logue on montre le form sinon on affiche la application
		if(gSessionId === '' || gSessionId === '0' || gSessionId === '-1'){
			//hide init layer
			setTimeout(this.showLogin.bind(this), 1000);
		}else{
			//sinon on va chercher l'infos de base du user: id, etc...	
			//le retour dans la classe user va faire le showApplication
			this.juser.getBasicsInfos();
			}

		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.getVersion = function(){
		return this.version;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//scroll detection
	this.scrollEventOnLayerSearchForLoopData = function(){
		this.debug('scrollEventOnLayerSearchForLoopData()');
		//juste si il reste des exercices a afficher
		if(this.jsearch.getLastResultPendingCount() > 0){
			var w = $('#layer-search');
			var iTotalHeight = parseInt(w.prop('scrollHeight'));
			var iTopHeight = w.scrollTop() + w.height();// + 52; //52px pour le padding-bottom a 52px;
			//on load le contenu dans le bas de la page serach result
			if((iTotalHeight - iTopHeight) < 100){
				this.fillSearchLoop(false);
				}
			}
		}


	//----------------------------------------------------------------------------------------------------------------------*
	//fix drag image in firefox problem
	this.fixImage = function(){
		//this.debug('fixImage()');
		
		//drag bug
		$(document).unbind('dragstart');
		$(document).on('dragstart', 'img', function(){
			return false;
			});
		}

	
	//----------------------------------------------------------------------------------------------------------------------*
	//init the setting panel
	this.loadNeededImage = function(){
		this.debug('loadNeededImage()');	
		//all needed images if we disconnect from the web still need them to continue functionning
		var arrImages = [
			'glyphicons_237_zoom_out.png',
			'glyphicons_237_zoom_out_invert.png',
			'glyphicons_236_zoom_in.png',
			'glyphicons_236_zoom_in_invert.png',
			'logo-black.png',
			'logo-white.png',
			'icone-disk-0.png',
			'icone-disk-1.png',
			'icone-program-edit-black.png',
			'glyphicons_chevron-down.png',
			'glyphicons_chevron-down-w.png',
			'glyphicons_chevron-up.png',
			'default-exercice.png',
			'icone-search-invert.png',
			'mobile-loading-w-3.png',
			'mobile-loading-3.png',
			'menu-user.png',
			'menu-template.png',
			'icone-refresh.png',
			'icone-refresh-invert.png',
			'icon-selected.png',
			'icon-unselected.png',
			'icon-video.png',
			'icon-mirror.png',
			'icon-switch.png',
			'60577.png',
			'60577-w.png'
			];
		//load the missing images
		var img = [];
		for(var o in arrImages){
			img[o] = new Image();
			/*
			img[o].onload = function(){
				console.log('loaded OK : ' + this.src);
				};
			*/
			img[o].src = gServerPath + 'images/' + gBrand + '/' + arrImages[o];
			}
		}


	//----------------------------------------------------------------------------------------------------------------------*
	//init the autocomplete serach fields
	this.initAutoComplete = function(){
		this.debug('initAutoComplete()');	
		//
		this.jautocomplete.init([
			{ 
				input:'input-main-client-search-autocomplete',
				layer:'layer-client',
				type:'client', 
				position:'under',
				},
			{
				input:'input-main-exercice-search-autocomplete',
				layer: 'layer-search',
				type:'exercice',
				position:'under',
				module: 'search-select-module',
				},
			]);
		}


	//----------------------------------------------------------------------------------------------------------------------*
	//init the setting panel
	this.initSettingPanel = function(){
		this.debug('initSettingPanel()');	
		//pass the container name not the jquery obj because we will have a bug with hammer cause will use getElementById with the Hammer.Manager
		this.jsettingmanager.init({
			container: 'setting-panel-container',
			});
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	//iniit buttons 
	this.initButtonAction = function(){
		this.debug('initButtonAction()');

		//le counter du serach exercice
		$('#search-exercice-counter > .msg').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.scrollToTop('#layer-search');
				}
			});
		
		//main menu program
		$('#butt-programs > IMG').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.closeAllLayers();
				}
			});
		//main menu client
		$('#butt-client > IMG').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.openLayer('client');
				}
			});
		//main menu serach exercice
		$('#butt-search > IMG').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.openLayer('search');
				}
			});
		//main menu exit
		$('#butt-exit > IMG').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.comfirmResetDataAndDisplay();
				}
			});
		//top bar with the user icon
		$('#main-client-name-text').click(function(e){
			e.preventDefault(); 
			//tmp data
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = $(this).attr('clientid');
				oTmp.openClientSectionFromId(id);
				}
			});		
		//le bouton refresh du input de layer client
		$('#butt-refresh-client').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.resetClientSearchWindow();
				}
			});
		//the add user button of the client layer
		$('#butt-new-client').click(function(e){
			e.preventDefault(); 
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.openAddNewClient('from-onglet-client-search', false);
				}
			});	
			
		//the butt icon modify on the program layer
		$('#butt-modify-program-icon').click(function(e){
			e.preventDefault(); 
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.openEditProgramById(); 
				}
			});
		//le bouton du sous menu settings du program layer
		$('#butt-programs-settings').click(function(e){
			e.preventDefault(); 
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.showToolsLayerSettings();
				}
			});	
		//le bouton du sous menu print du program layer
		$('#butt-programs-print').click(function(e){
			e.preventDefault(); 
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.openPrintProgram();
				}
			});	
		//le bouton du sous menu email du program layer
		$('#butt-programs-send').click(function(e){
			e.preventDefault(); 
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.openSendProgram();
				}
			});	
		//la corbeille des selected du layer program
		$('#butt-delete-check-items').click(function(e){
			e.preventDefault(); 
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var arr = [];
				for(var o in oTmp.jprogram.arrExercices){
					var selected = $('#' + oTmp.jprogram.arrExercices[o].boxName).attr('imgselected');
					if(selected == '1'){
						arr.push(oTmp.jprogram.arrExercices[o].id);
						}
					}
				if(arr.length){
					oTmp.rmItemsArrayFromPrograms(arr);
					}
				}
			});
		//la corbeille des unselected du layer program
		$('#butt-delete-uncheck-items').click(function(e){
			e.preventDefault();
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var arr = [];
				for(var o in oTmp.jprogram.arrExercices){
					var selected = $('#' + oTmp.jprogram.arrExercices[o].boxName).attr('imgselected');
					if(selected == '0'){
						arr.push(oTmp.jprogram.arrExercices[o].id);
						}
					}
				if(arr.length){
					oTmp.rmItemsArrayFromPrograms(arr);
					}
				}
			});
		//les boutons zoom du layer program avec celui quand les setting sont ouvert aussi
		$('#butt-zoom-in, #butt-zoom-in-2').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){	
				oTmp.changeZooming(true);
				}
			});
		//le bouton select invert du program layer	
		$('#butt-check-invert').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//check state
				var iChk =  $('#butt-check-invert').attr('checkstate');
				if(iChk == '1'){
					$('#butt-check-invert').attr('checkstate','0');
					for(var o in oTmp.jprogram.arrExercices){
						oTmp.checkProgramsBox(false, oTmp.jprogram.arrExercices[o].id);
						}
				}else{
					$('#butt-check-invert').attr('checkstate','1');
					for(var o in oTmp.jprogram.arrExercices){
						oTmp.checkProgramsBox(true, oTmp.jprogram.arrExercices[o].id);
						}
					}
				}
			});
		//le bouton save du program layer duskette
		$('#butt-save').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.openSaveOptions();
				}
			});	
		//les bouton de selection auquel appliquer les setting du layer program quand les setting sont ouvert
		$('#butt-settings-all, #butt-settings-selected, #butt-settings-unselected').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.applySettingsToExercice($(this).attr('setting-type'));
				}
			});	
		//le bouton close des seetings quand ceux-ci sont ouvert dans le program layer		
		$('#butt-settings-close').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.closeToolsLayerSettings();
				}
			});	
		//le top title seettings close des seetings quand ceux-ci sont ouvert 
		$('#main-settings-top').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.closePopup();
				//met un timer pourque le clavier est le temps de se fermer si edite une case texte
				setTimeout(oTmp.closeToolsLayerSettings.bind(oTmp), 300);
				}
			});
		//le refresh button du layer serach
		$('#butt-new-search-exercise').click(function(e){
			e.preventDefault(); 
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.resetSearchWindow();
				}
			});	
		//le select box des template du layer search
		$('#search-select-template-mine, #search-select-template-all, #search-select-template-license, #search-select-template-brand').change(function(e){
			e.preventDefault();
			var templateId = $(this).val();		
			if(templateId != '0'){
				//hide the form
				$('#search-exercise-form').removeClass('show');
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					//put the loader
					oTmp.showLoader(true, '#listing-search', 10, 10);
					//real data service	
					oTmp.jsearch.getTemplateExerciceById(templateId);
					}
				}
			});
		}
		
	//----------------------------------------------------------------------------------------------------------------------*	
	this.changeZooming = function(bFromButt){
		this.debug('changeZooming(' + bFromButt + ')');	
		var zoomSize = Object.keys(this.zoomFactor);
		var zoomLength = zoomSize.length - 1;	
		//smallest no content-view; 
		var iZoom = zoomLength - 1; 
		var zoomId = parseInt($('#butt-zoom-in').attr('zoom-id'));
		//
		if(bFromButt){ //from the button loupe
			//check si layer-settings est ouvert
			if(zoomId != -1){
				//on change l'index en memoire
				this.zoomFactorLastIndex = zoomId;
				}
			//
			this.zoomFactorLastIndex -= 1;
			if(this.zoomFactorLastIndex < 0){
				this.zoomFactorLastIndex = 4;
				}
			//vu que onm apris celui en memoire il faut remettre le nouveau pour quand il fermera layer-settings		
			if(zoomId != -1){	
				$('#butt-zoom-in').attr('zoom-id', this.zoomFactorLastIndex);	
				}
			//chcnge the zoom index
			iZoom = this.zoomFactorLastIndex;
			
		}else{ //from layer-settings
			 //on ouvre ou ferme le layer-setting selon
			if(zoomId != -1){
				iZoom = zoomId;
				//on reset
				$('#butt-zoom-in').attr('zoom-id', '-1');
			}else{
				//keep old one
				$('#butt-zoom-in').attr('zoom-id', this.zoomFactorLastIndex);
				}
			}
		//image	
		if(iZoom == zoomLength){
			$('#butt-zoom-in > img').attr('src', gServerPath + 'images/' + gBrand + '/glyphicons_237_zoom_out.png');
			$('#butt-zoom-in-2 > img').attr('src', gServerPath + 'images/' + gBrand + '/glyphicons_237_zoom_out_invert.png');			
		}else{
			$('#butt-zoom-in > img').attr('src', gServerPath + 'images/' + gBrand + '/glyphicons_236_zoom_in.png');
			$('#butt-zoom-in-2 > img').attr('src', gServerPath + 'images/' + gBrand + '/glyphicons_236_zoom_in_invert.png');			
			}
		//special content-view
		this.boxSettings.w = this.zoomFactor[iZoom].minW + 'px';
		this.boxSettings.h = this.zoomFactor[iZoom].minH + 'px';
		this.boxSettings.minH = this.zoomFactor[iZoom].minH;
		this.boxSettings.minW = this.zoomFactor[iZoom].minW;
		this.boxSettings.t = this.zoomFactor[iZoom].t;
		this.boxSettings.mrgbott = this.zoomFactor[iZoom].mrgbott;	

		//on set si content view display ou pas, qui se trouve a etre le dernier soit zoomFactor[4]
		if(iZoom == zoomLength){
			this.contentViewDisplay = true;
		}else{
			this.contentViewDisplay = false;
			}
			
		//juste le programme pas les deux comme dans redrawBoxSizes()
		this.displayProgramBoxesHasContentView();	

		}
		
	//----------------------------------------------------------------------------------------------------------------------*	
	this.applySettingsToExercice = function(strType){
		this.debug('applySettingsToExercice(' + strType + ')');
		//on ca chercher les settoings de la inner-layer
		//settings
		var obj = this.jsettingmanager.getActiveSettingsToApply();
		//
		var arrIds = [];	
		//check the type of action
		if(strType == 'all'){
			//sets all
			for(var o in this.jprogram.arrExercices){
				this.jprogram.setExerciceSettingsById(this.jprogram.arrExercices[o].getId(), obj);
				arrIds.push(this.jprogram.arrExercices[o].getId());
				}
		
		}else if(strType == 'selected'){
			//sets seclected
			for(var o in this.jprogram.arrExercices){
				var selected = $('#' + this.jprogram.arrExercices[o].getBoxName()).attr('imgselected');
				if(selected == '1'){
					this.jprogram.setExerciceSettingsById(this.jprogram.arrExercices[o].getId(), obj);
					arrIds.push(this.jprogram.arrExercices[o].getId());
					}
				}
		
		}else if(strType == 'unselected'){
			//sets unselected
			for(var o in this.jprogram.arrExercices){
				var selected = $('#' + this.jprogram.arrExercices[o].getBoxName()).attr('imgselected');
				if(selected == '0'){
					this.jprogram.setExerciceSettingsById(this.jprogram.arrExercices[o].getId(), obj);
					arrIds.push(this.jprogram.arrExercices[o].getId());
					}
				}	
			}
			
		//change le save state de program
		this.changeProgramSaveState(false);
		//
		this.applySettingAnimation(arrIds);
		//reset all the settings to the base
		this.jsettingmanager.resetSettingPanelContainer();
		//
		}
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.applySettingAnimation = function(arrIds){
		this.debug('applySettingAnimation(' + arrIds + ')');	
		//apply the style
		var str = '';
		for(var o in arrIds){
			str += '#img-' + arrIds[o] + ',';
			}
		if(str != ''){
			str = str.substr(0,(str.length - 1));
			}
		$(str).addClass('settings');
		//wait a bit
		setTimeout(function(){
			$(str).removeClass('settings');
			},500);
				
		}
	
	//----------------------------------------------------------------------------------------------------------------------*	
	this.changeProgramSaveState = function(bState){
		this.debug('changeProgramSaveState(' + bState + ')');	
		//change the icon
		if(bState){ //saved
			$('#butt-save').html('<img draggable="false" class="img-sty-3" src="' + gServerPath + 'images/' + gBrand + '/icone-disk-1.png">');
		}else{
			$('#butt-save').html('<img draggable="false" class="img-sty-3" src="' + gServerPath + 'images/' + gBrand + '/icone-disk-0.png">');
			}
		//change the sate of the program
		this.jprogram.setSaved(bState);
		}

	//----------------------------------------------------------------------------------------------------------------------*	
	this.showLoader = function(bWhite, layer, leftMarge, topMarge){
		this.debug('showLoader(' + bWhite + ', ' + layer + ', ' + leftMarge + ', ' + topMarge + ')');		
		//
		if(bWhite){
			$(layer).html('<img id="loading-icon" src="' + gServerPath + 'images/' + gBrand + '/mobile-loading-w-3.png" class="loading loading-icon" style="margin:' + topMarge + 'px ' + leftMarge + 'px">');
		}else{
			$(layer).html('<img id="loading-icon" src="' + gServerPath + 'images/' + gBrand + '/mobile-loading-3.png" class="loading loading-icon" style="margin:' + topMarge + 'px ' + leftMarge + 'px">');
			}
		}


	//----------------------------------------------------------------------------------------------------------------------*	
	this.removeLoader = function(layer, str){
		this.debug('removeLoader(' + layer + ', ' + str + ')');		
		
		$(layer + ' #loading-icon').remove();
		//put word if some ex: on button
		if(str != ''){
			$(layer).html(str);
			}

		}

	//----------------------------------------------------------------------------------------------------------------------*	
	this.redrawBoxSizes = function(){
		this.debug('redrawBoxSizes()');		
		
		//redraw program boxes
		this.displayProgramBoxesHasContentView();
		//redraw serach boxes
		this.displaySearchBoxesHasContentView();
		}	
		
	//----------------------------------------------------------------------------------------------------------------------*	
	//check the basic setting of the box: border, padding, margin from .box-img css class
	this.modifyBoxSettings = function(){
		this.debug('modifyBoxSettings()');	
		
		//load it just one time
		if(this.cssStyle == -1){
			this.cssStyle = this.getStyle('box-img');
			this.cssExtraStyle = this.getExtraStyle();
			}
		
		//vars
		var iBorder = this.cssStyle.borderWidth; 
		var iMargin = this.cssStyle.marginRight + this.cssStyle.marginLeft;
		var iPadding = this.cssStyle.paddingRight;
		var iWidth = 0;
		var iMaxWidth = 140;
		var iContainerMargin = 10;
		
		//zooom press
		this.zoomFactor = {
			'0': {
				margin: 0,
				maxCols: 2,
				minH: 0,
				minW: 0,
				w:0,
				h:0,
				t:true,
				//mrgbott:40,
				mrgbott:36,
				}, // 2 item per cols avec texte
			'1': {
				margin: 0,
				maxCols: 3,
				minH: 0,
				minW: 0,
				w:0,
				h:0,
				t:true,
				//mrgbott:40,
				mrgbott:36,
				}, // 3 items per cols  avec texte
			'2': {
				margin: 0,
				maxCols: 4,
				minH: 0,
				minW: 0,
				w:0,
				h:0,
				t:false,
				mrgbott:3,
				}, // 4 items per cols
			'3': {
				margin: 0,
				maxCols: 6,
				minH: 0,
				minW: 0,
				w:0,
				h:0,
				t:false,
				mrgbott:3,
				}, // 6 items per cols
			};
		

		var iBaseWidth = 360;
		var iLeftScrollBar = 0;
		if(this.containerSize.w >= 1024){
			//we must be on a desktop so check for the scrollBar size
			iLeftScrollBar = 25;
			}
		var iAspectRatio = 3/4;
		//var iWidthRatio = parseInt(this.containerSize.w / iBaseWidth);
		var iWidthRatio = parseInt((this.containerSize.w - iLeftScrollBar) / iBaseWidth);
		for(var o in this.zoomFactor){
			//360px sera notre largeur de base
			if(this.containerSize.w <= (iBaseWidth * 2)){ //smallest vertical
				this.zoomFactor[o].minW = parseInt((((this.containerSize.w - iLeftScrollBar) - (this.zoomFactor[o].maxCols * iMargin) - ((this.zoomFactor[o].maxCols * 2) * iBorder)) - (iContainerMargin * 2))/this.zoomFactor[o].maxCols);
			}else{
				this.zoomFactor[o].minW = parseInt((((this.containerSize.w - iLeftScrollBar) - ((this.zoomFactor[o].maxCols * iWidthRatio) * iMargin) - (((this.zoomFactor[o].maxCols * iWidthRatio) * 2) * iBorder)) - (iContainerMargin * 2))/(this.zoomFactor[o].maxCols * iWidthRatio));
				}
			//
			this.zoomFactor[o].minH = (this.zoomFactor[o].minW * iAspectRatio);
			this.zoomFactor[o].w = this.zoomFactor[o].minW + 'px';
			this.zoomFactor[o].h = (this.zoomFactor[o].minW * iAspectRatio) + 'px';
			this.zoomFactor[o].margin = parseInt((iMargin/2));
			}
		//pour le content view avec height fixe en pixel 
		this.zoomFactor['4'] = {
			maxCols: 1,
			minH : 100, //en px 
			minW : 100,  //en px
			w : '100px', 
			h : '100px', 
			margin : this.zoomFactor['2'].margin,
			t : false, //car est utilise par search-img
			mrgbott : this.zoomFactor['2'].mrgbott,
			}

		//si un content-view large et si on a un ecran ultra large
		var iPrctW = 97;
		if((this.containerSize.w - iLeftScrollBar) >= (iBaseWidth * 2)){ //2 for large screen display
			iPrctW /= parseInt((this.containerSize.w - iLeftScrollBar) / (iBaseWidth + (((this.containerSize.w - iLeftScrollBar) / iBaseWidth) * (iContainerMargin * 2))));
			}  	
		
		//set the boxes size	
		this.boxSettings = {
			margin: this.zoomFactor[this.zoomFactorLastIndex].margin, 
			minH: this.zoomFactor[this.zoomFactorLastIndex].minH, 
			minW: this.zoomFactor[this.zoomFactorLastIndex].minW, 
			w: this.zoomFactor[this.zoomFactorLastIndex].w, 
			h: this.zoomFactor[this.zoomFactorLastIndex].h,
			t: this.zoomFactor[this.zoomFactorLastIndex].t,
			mrgbott: this.zoomFactor[this.zoomFactorLastIndex].mrgbott,
			prctw: iPrctW + '%', //special content-view
			};		
		
		}	
		
		
	//----------------------------------------------------------------------------------------------------------------------*		
	//get the rules of a specifis satyle i the global.css extern styleSheet
	this.getStyle = function(cssClassName){
		this.debug('getStyle(' + cssClassName + ')');		
		
		//create a fake div to get the style
		var obj = {};
		var $tmpDiv = $('<div class="' + cssClassName + '"></div>').hide().appendTo('body');
		//get the style
		obj.borderWidth = parseInt($tmpDiv.css('border-width')) || 0; 
		if(obj.borderWidth == 0){
			obj.borderWidth = parseInt($tmpDiv.css('borderLeftWidth'));
			}
		obj.marginRight = parseInt($tmpDiv.css('margin-right'));
		obj.marginLeft = parseInt($tmpDiv.css('margin-left'));
		obj.paddingRight = parseInt($tmpDiv.css('padding-right'));
		//remo ve fake div
		$tmpDiv.remove();
		//
		return obj;
		}			
		
	//----------------------------------------------------------------------------------------------------------------------*			
	this.getExtraStyle = function(){
		this.debug('getExtraStyle()');		
		
		//create a fake div to get the style
		var obj = {};
		//chercher les autres styles declares de facon globale dans la classe presente, ils sont deja renderer dans le fichier index.php
		obj.leftMargeListingPrograms = parseInt($('#listing-programs').css('left'));
		obj.bottomProgramsTools = parseInt($('#programs-tools').css('bottom'));
		obj.listingProgramsTitleHeight = parseInt($('#butt-modify-program').outerHeight(true));
		obj.listingProgramsMarginTop = parseInt($('#butt-modify-program').css('margin-top'));
		obj.mainMenuTopHeight = parseInt($('#main-menu-top').css('height'));
		//le max padding height of the layer-settings -> layer-content soit la class layer-slide-inner 
		// + border-top si il y a
		var iBorderTop = parseInt($('#layer-settings').css('border-width')) || 0; 
		if(iBorderTop == 0){
			iBorderTop = parseInt($('#layer-settings').css('borderTopWidth'));
			}
		obj.layerSettingsTotalPaddingHeight = iBorderTop + parseInt($('#layer-settings').css('padding-top')) + parseInt($('#layer-settings').css('padding-bottom'));
		//	

		this.debug('cssExtraStyle', obj);


		return obj;
		}	
		
		
	//----------------------------------------------------------------------------------------------------------------------*	
	this.resizeAllElements = function(){
		this.debug('resizeAllElements()');

		//le menu options
		this.joptions.resizeEvent();
		
		// moins la scrollbar quand pas en mode mobile	
		this.containerSize = {
			w: $('#main-container').innerWidth(),
			h: $('#main-container').innerHeight(),
			};

		this.debug('containerSize', this.containerSize);

		//layer serach and client
		$('.layer-slide').each(function(){
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				if($(this).attr('showed') == '0'){
					$(this).css({top:oTmp.containerSize.h});
					}
				}
			});
		//inner layer settings of the program layer
		if($('#layer-settings').attr('showed') == '1'){
			var maxHeight = parseInt(this.containerSize.h - $('#layer-settings > .layer-content').outerHeight() - this.cssExtraStyle.layerSettingsTotalPaddingHeight);
			if(maxHeight < 0){
				maxHeight = 0;
				}
			$('#layer-settings').css({top:maxHeight + 'px'});
			this.changeProgramListingBottom($('#layer-settings').position().top, false);
		}else{
			$('#layer-settings').css({top:this.containerSize.h});
			}	
		//largeur des icones du main menu du bas
		var iIconWidth = ((this.containerSize.w - (2 * 4))/4);
		$('#main-menu-bottom A').each(function(){
			$(this).css({width:iIconWidth + 'px'});
			});
		//hateur du listing-program car quand en largeur la barre de main-menu-top est plus haute
		$('#listing-programs').css({
			'top': parseInt($('#butt-modify-program').outerHeight(true)) + 'px' ,
			});
	
		//recalculate box size	
		this.modifyBoxSettings();
		//fix the opening keyboeard issue
		//BUGS: on check si le setting panel est ouvert sinon va revenir au zoom in precedent
		var zoomId = parseInt($('#butt-zoom-in').attr('zoom-id'));
		// zoomId <> -1 car quand les settings sont ouvert on garde en memoire dans cet attribut le dernier zoomFactor avant que l'on est ouvert
		if(zoomId == -1){ 
			this.redrawBoxSizes();
			}	
		
		}
		

	//----------------------------------------------------------------------------------------------------------------------*	
	this.openPrintProgram = function(){
		this.debug('openPrintProgram()');
		//
		var bAlertClient = false;
		var bAlertProgram = false;
		//check si on a un client sinon on avertit qu'il doit en choisir un
		var oClient = this.jprogram.getClient();
		if(typeof(oClient) != 'object'){
			bAlertClient = true;
			}
		//check si on a un programme sauve
		if(this.jprogram.getProgId() === -1){
			bAlertProgram = true;
			}
		if(bAlertClient || bAlertProgram){
			var strErrorMessage = '';
			if(bAlertClient){
				strErrorMessage += '<li>' + jLang.t('you must select a client first.') + '</li>';
				}
			if(bAlertProgram){
				strErrorMessage += '<li>' + jLang.t('you must create a program name first.') + '</li>';
				}
			//on pop l alerte
			//error message
			strErrorMessage = '<ul class="msg">' + strErrorMessage + '</ul>';
			this.openAlert('alert', jLang.t('warning'), strErrorMessage, false);	
		
		}else{
			//butts bottom		
			var str = '';
			str += '<div class="popup-tools">';
			str += '<div>';
			str += '<a href="#" class="butt med" id="butt-print-popup">' + jLang.t('print') + '</a>';
			str += '<a href="#" class="butt med" id="butt-cancel-popup">' + jLang.t('cancel') + '</a>';
			str += '</div>';
			str += '</div>';
			//
			str += '<div id="popup-content">';
			str += '<h1 class="h1-close-popup">' + jLang.t('print') + '</h1>';
			str += '<div class="popup-form">';
			//format
			str += '<p><select id="popup-input-1" class="select-1 search"><option value="-1">' + jLang.t('loading') + '</option></select></p>';
			//type photo/dessin
			str += '<p><select id="popup-input-2" class="select-1 search"><option value="-1">' + jLang.t('loading') + '</option></select></p>';
			//per page	
			str += '<p><select id="popup-input-3" class="select-1 search"><option value="-1">' + jLang.t('loading') + '</option></select></p>';	
			//	
			str += '</div>'; //close form
			str += '</div>'; //close popup-content
			//write content to popup
			$('#main-popup-window').html(str);
			//parametre impression
			this.juser.getPrintParameters(this.jprogram.getProgId(), this.jprogram.getClientId());
			//
			this.openPopup();
			//
			$('#butt-cancel-popup').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					oTmp.closePopup();
					}
				});
			$('#butt-print-popup').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					//le loader
					oTmp.showLoader(true, '#butt-print-popup', 0, 0);
					oTmp.buildPrintPdfLink();
					}
				});	
			}
		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.buildPrintPdfLink = function(){
		this.debug('buildPrintPdfLink()');
		
		//on reset les color field error	
		$('.select-1').removeClass('error');
		//
		//on va chercher les opiotns selectionne
		var strFormat = $('#popup-input-1').val();
		var strType = $('#popup-input-2').val();
		var strSize = $('#popup-input-3').val();
		//validation
		var arrField = [];
		if(strFormat == '-1'){
			arrField.push('#popup-input-1');
			}
		if(strType == '-1'){
			arrField.push('#popup-input-2');
			}
		if(strSize == '-1'){
			arrField.push('#popup-input-3');
			}
		//si erreur
		if(arrField.length > 0){
			//color the field
			for(var o in arrField){
				$(arrField[o]).addClass('error');
				}
			//le loader
			this.removeLoader('#butt-print-popup', jLang.t('print'));
		}else{
			//
			//on envoi au serveur les parametres modifie ou pas
			this.juser.savePrintParameters(strFormat, strType, strSize);
			//par defaut celle de l'interface
			var strLang = gLocaleLang; 
			//on va chercher la langue du client, (PAS celle de l'interface ou du user)
			var oClient = this.jprogram.getClient();
			if(typeof(oClient) == 'object'){
				strLang = oClient.getLocale();
				}

			var strLink = gPrintServer + this.printTemplate[strSize] + '?';
			strLink += '&PHPSESSID=' + gSessionId;
			strLink += '&idProgram=' + this.jprogram.getProgId();
			strLink += '&picture_type=' + strType;
			strLink += '&program_locale=' + strLang;
			strLink += '&mobile=1';

			this.debug('OPENING LINK: ' + strLink);
			
			//on ouvre la fenetre
			if(!gIsAppz){ 
				//si pas mobile alors on ouvre un onglet
				window.open(strLink, '_blank');
				this.closePopup();
			}else{ //si appz on ouvre dans le iframe
				//si mobile application on call la main window qui a instancie le iframe
				if(gOsType == 'iOS' || gOsType == 'Android' || gOsType == 'Win32NT' || gOsType == 'WinCE'){
					gCallWindow({
						method: 'print',
						args: strLink
						});
					this.closePopup();	
				}else{
					//on utilise le pdf viewer de mozilla dans le cas d'un windows application
					this.openPrintPdfPopup(strLink);
					}
				}
			}
		}	


	//----------------------------------------------------------------------------------------------------------------------*	
	this.openPrintPdfPopup = function(strLink){
		this.debug('openPrintPdfPopup(' + strLink + ')');
		//
		var str = '';
		str += '<div class="popup-tools">';
		str += '<div>';
		str += '<a href="#" class="butt med" id="butt-cancel-print-popup">' + jLang.t('close') + '</a>';
		str += '</div>';
		str += '</div>';
		//
		str += '<div id="popup-content">';
		//str += '<h1 class="h1-close-popup">' + jLang.t('print') + '</h1>';
		str += '<div class="popup-form print-iframe">';
		//str += '<div class="popup-form" style="position:absolute;top:0px;bottom:0px;left:0px;right:0px;overflow:hidden;">';
		//str += '<div class="popup-form" style="position:relative;float:left;width:100%;height:100%; overflow:hidden;border:none;">';
		//on y ava avec un iframe pour ere supporte par tout les mobiles en format application
		str += '<iframe id="print-frame" src="' + gPdfViewer + encodeURIComponent(strLink) + '"></iframe>';
		//str += '<iframe id="print-frame" class="" src="' + strLink + '" style="overflow: scroll !important;width:100%;height:100%;border:none;"></iframe>';
		str += '</div>'; //close popup-form
		str += '</div>'; //close popup-content
		//write content to popup
		$('#main-popup-window').html(str);
		//on set un loader que l'on va eneleve une fois le iframe loade
		this.showLoader(true, '#butt-cancel-print-popup', 0, 0);
		//
		$('#print-frame').load(function(){
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.removeLoader('#butt-cancel-print-popup', jLang.t('close'));
				}
			});
		//
		$('#butt-cancel-print-popup').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.closePopup();
				}
			});
		}		



	//----------------------------------------------------------------------------------------------------------------------*
	//retour du lien a ouvrir dans une nouvelle fenetre pour le pdf	
	this.savePrintParametersReturnFromServer = function(obj, extraObj){
		this.debug('savePrintParametersReturnFromServer(' + obj + ', ' + extraObj + ')');
		
		var bContinue = true;
		//on check si il y a une erreur de la session via le retour du service
		if(typeof(obj.error) != 'undefined'){
			if(obj.error == '1'){
				//on pop le msg d'erreur
				bContinue = false;
				//
				this.openAlert('alert', jLang.t('error!'), obj.errormessage, false);
				}
			}

		//le loader
		this.removeLoader('#butt-print-popup', jLang.t('print'));
		//continu the print sequence
		if(bContinue){
			//close the print popup
			//a remettre si on ne ferme pas ou overwrite pas le openPopup precedent
			//this.closePopup();
			}


		}



	//----------------------------------------------------------------------------------------------------------------------*
	this.getPrintParametersReturnFromServer = function(obj, extraObj){
		this.debug('getPrintParametersReturnFromServer(' + obj + ', ' + extraObj + ')');
		//on fill les box
		var strSelected = '';
		if(typeof(obj.format) == 'object'){
			strSelected = ''
			if(typeof(obj.selected.format) != 'undefined'){
				strSelected = obj.selected.format;
				}
			this.fillPopupPrintSelectOptionsFromArray('popup-input-1', obj.format, strSelected);
			}
		if(typeof(obj.type) == 'object'){
			strSelected = ''
			if(typeof(obj.selected.type) != 'undefined'){
				strSelected = obj.selected.type;
				}
			this.fillPopupPrintSelectOptionsFromArray('popup-input-2', obj.type, strSelected);
			}
		if(typeof(obj.size) == 'object'){
			strSelected = ''
			if(typeof(obj.selected.size) != 'undefined'){
				strSelected = obj.selected.size;
				}
			this.fillPopupPrintSelectOptionsFromArray('popup-input-3', obj.size, strSelected);
			}

		//les sizes template
		if(typeof(obj.template) == 'object'){
			this.printTemplate = obj.template;
			}
		
		}

	//----------------------------------------------------------------------------------------------------------------------*	
	this.fillPopupPrintSelectOptionsFromArray = function(strBoxId, arrData, strSelected){
		this.debug('fillPopupPrintSelectOptionsFromArray(' + strBoxId + ', ' + arrData + ', ' + strSelected + ')');
	
		//minor check
		var el = $('#' + strBoxId);
		var options = '';
		if(el !== false && typeof(el) == 'object'){
			options += '<option value="-1">' + jLang.t('select an option') + '</option>';
			for(var o in arrData){
				options += '<option value="'+ o + '" ';
				if(strSelected == o){
					options += ' selected ';
					}
				options += '>' + arrData[o] + '</option>';
				}
			el.html(options);	
			}
		
		};


	//----------------------------------------------------------------------------------------------------------------------*	
	this.openSendProgram = function(){
		this.debug('openSendProgram()');
		//
		var bAlertClient = false;
		var bAlertProgram = false;
		//check si on a un client sinon on avertit qu'il doit en choisir un
		var oClient = this.jprogram.getClient();
		if(typeof(oClient) != 'object'){
			bAlertClient = true;
			}
		//check si on a un programme sauve
		if(this.jprogram.getProgId() === -1){
			bAlertProgram = true;
			}
		//onm alerte
		if(bAlertClient || bAlertProgram){
			var strErrorMessage = '';
			if(bAlertClient){
				strErrorMessage += '<li>' + jLang.t('you must select a client first.') + '</li>';
				}
			if(bAlertProgram){
				strErrorMessage += '<li>' + jLang.t('you must create a program name first.') + '</li>';
				}
			//on pop l alerte
			//error message
			strErrorMessage = '<ul class="msg">' + strErrorMessage + '</ul>';
			this.openAlert('alert', jLang.t('warning'), strErrorMessage, false);	
		
		}else{
			var strEmail = oClient.getEmail();
			//butts bottom		
			var str = '';
			str += '<div class="popup-tools">';
			str += '<div>';
			str += '<a href="#" class="butt med" id="butt-send-popup">' + jLang.t('send') + '</a>';
			str += '<a href="#" class="butt med" id="butt-cancel-popup">' + jLang.t('cancel') + '</a>';
			str += '</div>';
			str += '</div>';
			//
			str += '<div id="popup-content">';
			str += '<h1 class="h1-close-popup">' + jLang.t('send') + '</h1>';
			str += '<div class="popup-form">';
			//on va chercher l,adressecourriel du client
			//en meme temps si il l'a change ca va la changer dans les informations du client
			str += '<p><b>' + jLang.t('email:') + '</b></p><p><input type="text" id="popup-input-1" value="' + this.jutils.javascriptFormat(strEmail) + '" class="input-1 large uppercase"></p>';
			//le check box pour sauver le courriel dans la DB
			str += '<p class="font-1"><input type="checkbox" value="1" id="popup-checkbox-save-email" checked><i>' + jLang.t('change client email at the same time') + '</i></p>';
			//
			str += '</div>'; //close form
			str += '</div>'; //close popup-content
			//write content to popup
			$('#main-popup-window').html(str);
			//
			this.openPopup();
			//send butt
			$('#butt-send-popup').data('jclient', oClient);
			$('#butt-send-popup').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					//on met le loader
					oTmp.showLoader(true, '#butt-send-popup', 0, 0);
					//clien nfos
					var oClient = $(this).data('jclient');
					//on check le checkbox si est coche on change le courriel dans la DB aussi
					var bCheck = $('#popup-checkbox-save-email').prop('checked');
					strEmail = $('#popup-input-1').val();
					//on envoie le courriel du programme
					oTmp.jclientmanager.sendProgramEmail(oClient.getId(), oTmp.jprogram.getProgId(), strEmail, bCheck);
					}
				});	
			//cancel
			$('#butt-cancel-popup').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					oTmp.closePopup();
					}
				});
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*	
	this.sendProgramEmailReturnFromServer = function(obj, extraObj){
		this.debug('sendProgramEmailReturnFromServer(' + obj + ', ' + extraObj + ')');
		
		//check si envoi OK
		if(obj.ok){
			if(extraObj.saveemail){
				//on change le email localement aussi
				if(extraObj.sendemailto != ''){
					//get le client
					var oClient = this.jclientmanager.getClient(extraObj.clientid);
					if(typeof(oClient) == 'object'){
						oClient.setEmail(extraObj.sendemailto);
						//refresh les infos dans le listing des clients si il y a
						this.modifyClientSearchInnerLi(oClient);
						}
					}
				}
			this.closePopup();
		}else{
			//remove loader	
			this.removeLoader('#butt-send-popup', jLang.t('send'));		
			//show message
			this.openAlert('alert', jLang.t('error!'), obj.msg, false);	
			}
		}


	//----------------------------------------------------------------------------------------------------------------------*	
	this.openSaveOptions = function(){
		this.debug('openSaveOptions()');
		//
		//butts bottom
		var bIsTemplateOnly = false;
		var str = '';
		str += '<div class="popup-tools">';
		str += '<div>';
		//si pas de client ou pas de numero de programme alors pas moyen de sauvegarder
		//mais on peut mettre une alerte pour avertir le user comme quoi il doit donner les deux avant de sauvegarder
		var strButtSave = ''; 
		if(this.jprogram.getClientId() !== -1 && this.jprogram.getProgId() !== -1 ){
			if(this.jprogram.getSaved()){
				strButtSave = '<a href="#" class="butt med saved-ex-prog" id="butt-save-popup">' + jLang.t('saved') + '</a>';
			}else{
				strButtSave = '<a href="#" class="butt med" id="butt-save-popup">' + jLang.t('save') + '</a>';
				}
		}else if(this.jprogram.getClientId() === -1 && this.jprogram.getProgId() === -1 && this.jtemplate.getId() !== -1){
			//pour le template save
			bIsTemplateOnly = true;
			strButtSave = '<a href="#" class="butt med" id="butt-save-popup">' + jLang.t('save template') + '</a>';
			}
		//ajoute le bouteons save
		str += strButtSave;
		//
		str += '<a href="#" class="butt med" id="butt-cancel-popup">' + jLang.t('cancel') + '</a>';
		//
		str += '</div>';
		str += '</div>';
		//
		str += '<div id="popup-content">';
		str += '<h1 class="h1-close-popup">' + jLang.t('save options') + '</h1>';
		//options
		str += '<div style="margin-top:20px;">';
		//save as
		//juste si un client est enregistre
		if(this.jprogram.getClientId() !== -1){
			str += '<div><a href="#" class="butt med fullwidth" id="butt-options-save-as-other-program">' + jLang.t('save as other program') + '</a></div>';
			}
		//save as template
		if(bIsTemplateOnly){
			str += '<div><a href="#" class="butt med fullwidth" id="butt-options-save-as-template">' + jLang.t('save a template copy') + '</a></div>';
		}else{
			str += '<div><a href="#" class="butt med fullwidth" id="butt-options-save-as-template">' + jLang.t('save as template') + '</a></div>';
			}
		//change client
		str += '<div><a href="#" class="butt med fullwidth" id="butt-options-select-client">';
		if(this.jprogram.getClientId() !== -1){
			str += jLang.t('change the client of this program');
		}else{
			str += jLang.t('select a client for this program');
			}
		str += '</a></div>';
		//
		str += '</div>';
		//write content to popup
		$('#main-popup-window').html(str);
		//
		this.openPopup();
		//the butts
		//save as other
		if(this.jprogram.getClientId() !== -1){
			$('#butt-options-save-as-other-program').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					var strName = oTmp.jprogram.getName();
					var strNotes = oTmp.jprogram.getNotes();
					//close
					oTmp.closePopup();
					oTmp.openSaveAsOtherProgramPopup('from-saving-options', strName, strNotes);
					}
				});
			}
		//save as template
		$('#butt-options-save-as-template').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//close
				oTmp.closePopup();
				oTmp.openSaveAsTemplate();
				}
			});
		//select a client
		$('#butt-options-select-client').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//close
				oTmp.closePopup();
				oTmp.openPopupClientSearch('from-saving-options');
				
				}
			});
		//
		$('#butt-cancel-popup').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.closePopup();
				}
			});
		//program avec un client
		if(this.jprogram.getClientId() !== -1){
			$('#butt-save-popup').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					oTmp.jprogram.saveProgramsModifications();
					oTmp.closePopup();
					}
				});
			}
		//juste le template sans programme id ou client
		if(bIsTemplateOnly){
			$('#butt-save-popup').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					//le loader
					oTmp.showLoader(true, '#butt-save-popup', 0, 0);
					//build le obj to send
					var obj = {
						id: oTmp.jtemplate.getId(),
						name: oTmp.jtemplate.getName(),
						notes: oTmp.jtemplate.getNotes(),
						module: oTmp.jtemplate.getModule(),
						order: oTmp.jprogram.getExerciceByOrder(),
						exercices: oTmp.jprogram.getExerciceByArrayForTransport(),
						overwritename: false,
						keeporiginal: false,
						};
					//send
					oTmp.jtemplate.saveTemplateModifications(obj);
					}
				});
			}


		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.saveTemplateModificationsReturnFromServer = function(obj, extraObj){
		this.debug('saveTemplateModificationsReturnFromServer(' + obj + ', ' + extraObj + ')');
		this.debug('saveTemplateModificationsReturnFromServer::obj', obj);	
		this.debug('saveTemplateModificationsReturnFromServer::extraObj', extraObj);	

		//soit un = '1' ou ca peut-etre le id de celui que l'on a remplace
		if(typeof(obj) == 'number' || typeof(obj) == 'string'){
			//c'est un OK car est a '1'	
			if(obj == '1'){	
				// amjad changes
				//change le save state de program
				this.changeProgramSaveState(true);
				// end amjad changes
				//on close le popup	
				this.closePopup();
			}else{
				//on a un id alors on change car on a modifie le nom et a decide d'ecraser un existant alors le id est celui que l'on ecrase
				//pour le nom et le module ils sont toujours retourne dans le extarObj
				//this is a patch, because this function creates everything we need for a copy from a new or existing template
				this.createNewTemplateToDbReturnFromServer(parseInt(obj), {templatename:extraObj.name, templatemodule:extraObj.module, savetemplate:false});

				this.closePopup();
				}
		}else{
			if(typeof(obj) == 'object'){
				if(obj.exist == '1'){
					this.popupAlertReplaceTemplateName(extraObj.name, extraObj.module, {type:'modify'});
				}else if(obj.error == '1' && typeof(obj.errormessage) == 'string'){
					this.openAlert('alert', jLang.t('error!'), obj.errormessage, false);
					this.removeLoader('#butt-save-popup', jLang.t('save template'));	
					}
			}else{
				//message erreur
				this.openAlert('alert', jLang.t('error!'), jLang.t('an error occured during the template saving, please retry!'), false);
				//on remet le bouton a ON
				this.removeLoader('#butt-save-popup', jLang.t('save template'));
				}
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*	
	this.openSaveAsTemplate = function(){
		this.debug('openSaveAsTemplate()');
		
		//le nom du template selon si il en a vait deja un ou si est vide
		var strName = '';
		if(this.jprogram.getProgId() === -1 && this.jprogram.getClientId() === -1 && this.jprogram.getName() != ''){
			strName = this.jprogram.getName();
		}else{
			if(this.jtemplate.getName() != ''){
				strName = this.jtemplate.getName();
			}else if(this.jprogram.getName() != ''){
				strName = this.jprogram.getName();
				}
			}
		//butts bottom		
		var str = '';
		str += '<div class="popup-tools">';
		str += '<div>';
		str += '<a href="#" class="butt med" id="butt-save-popup">' + jLang.t('save') + '</a>';
		str += '<a href="#" class="butt med" id="butt-cancel-popup">' + jLang.t('cancel') + '</a>';
		str += '</div>';
		str += '</div>';
		//
		str += '<div id="popup-content">';
		str += '<h1 class="h1-close-popup">' + jLang.t('save as template') + '</h1>';
		//forms
		str += '<div class="popup-form">';
		str += '<p><b>' + jLang.t('template name:') + '</b></p><p><input type="text" id="popup-input-1" placeholder="' + jLang.t('new template name') + '" value="' + this.jutils.javascriptFormat(strName) + '" class="input-1 large uppercase"></p>';
		str += '<p><b>' + jLang.t('modules:') + '</b></p><p><select id="popup-input-2" class="select-1 search"><option id="-1">' + jLang.t('loading') + '</option></select></p>';
		str += '</div>'; //close form	
		str += '</div>'; //close popup-content
		//write content to popup
		$('#main-popup-window').html(str);
		//on va chercher les modules disponibles
		this.juser.getModulesForSelectOptions('popup-input-2');
		//ouvre
		this.openPopup();	
		//set le focus sur le premier input
		$('#popup-input-1').focus();
		//actions on butt
		$('#butt-cancel-popup').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.closePopup();
				}
			});
		//actions on butt
		$('#butt-save-popup').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//on reset les erreurs
				//reset errors on all field
				$('.input-1, .select-1').removeClass('error');
				//on va chercher les infos du forms
				var strName = oTmp.jutils.toUpper($('#popup-input-1').val()); 
				var strModule = $('#popup-input-2').val();
				var arrField = [];
				//check
				if(strName == ''){
					arrField.push('#popup-input-1');
					}
				if(strModule == '0' || strModule == '-1'){
					arrField.push('#popup-input-2');	
					}
					
				if(arrField.length > 0){
					//color the field
					for(var o in arrField){
						$(arrField[o]).addClass('error');
						}
					
				}else{
					//le loader du save
					oTmp.showLoader(true, '#butt-save-popup', 0, 0);
					//le cserver call
					oTmp.jtemplate.isTemplateNameAlreadyExistInServerData(strName, strModule);
					}
				}
			});	
		};

	//----------------------------------------------------------------------------------------------------------------------*
	this.createNewTemplateToDbReturnFromServer = function(id, extraObj){
		this.debug('createNewTemplateToDbReturnFromServer(' + id + ', ' + extraObj + ')');
		this.debug('createNewTemplateToDbReturnFromServer::extraObj', extraObj);
		
		//on met le id et le name et le module
		this.jtemplate.setName(extraObj.templatename);
		this.jtemplate.setModule(extraObj.templatemodule);
		this.jtemplate.setId(id);

		//on a un template et un id car nouveau alors on fait le save du template au complet
		//build le obj to send
		var obj = {
			id: this.jtemplate.getId(),
			name: this.jtemplate.getName(),
			notes: this.jtemplate.getNotes(),
			module: this.jtemplate.getModule(),
			order: this.jprogram.getExerciceByOrder(),
			exercices: this.jprogram.getExerciceByArrayForTransport(),
			overwritename: false,
			keeporiginal: false,
			};
		//send
		if(typeof(extraObj.savetemplate) == 'undefined'){
			this.jtemplate.saveTemplateModifications(obj);	
			}	
		//on check si dans le programme il n,y a ni client, ni programme name alors on met celui-ci par defaut
		if(this.jprogram.getProgId() === -1 && this.jprogram.getClientId() === -1 ){
			//set le program name only pour affichage
			this.jprogram.setName(extraObj.templatename);
			//set prog name inner program layer
			$('#main-program-name-text').html(extraObj.templatename);
			//top of the window
			$('#top-program-name').html(extraObj.templatename);
			//change le main settings program title
			$('#main-settings-program-name').html(extraObj.templatename);
			//on set un faux nom client pour dire qu'on edite un template
			$('#top-client-name').html(jLang.t('template edition'));
			//on change icon user
			$('#top-user-image').attr('src', gServerPath + 'images/' + gBrand + '/menu-template.png');
			}
		//on close le popup
		this.closePopup();
		
		}

	
	//----------------------------------------------------------------------------------------------------------------------*	
	this.fillPopupModuleSelectOptionsFromArray = function(strBoxId, arrData){
		this.debug('fillPopupModuleSelectOptionsFromArray(' + strBoxId + ', ' + arrData + ')');
	
		//minor check
		var el = $('#' + strBoxId);
		var options = '';
		if(el !== false && typeof(el) == 'object'){
			options += '<option value="-1">' + jLang.t('select a module') + '</option>';
			for(var o in arrData){
				options += '<option value="'+ o + '" ';
				if(this.jtemplate.getModule() == o){
					options += ' selected ';
					}
				options += '>' + arrData[o] + '</option>';
				}
			el.html(options);	
			}
		
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.openSaveAsOtherProgramPopup = function(strFrom, strName, strNotes){
		this.debug('openSaveAsOtherProgramPopup(' + strFrom + ', ' + strName + ', ' + strNotes + ')');
		
		//butts bottom		
		var str = '';
		str += '<div class="popup-tools">';
		str += '<div>';
		str += '<a href="#" class="butt med" id="butt-save-popup">' + jLang.t('save') + '</a>';
		str += '<a href="#" class="butt med" id="butt-cancel-popup">' + jLang.t('cancel') + '</a>';
		str += '</div>';
		str += '</div>';
		//
		str += '<div id="popup-content">';
		str += '<h1 class="h1-close-popup">' + jLang.t('save program as') + '</h1>';
		str += '<div class="popup-form" style="border-bottom: 1px dotted #ccc;">';
		str += '<p><b>' + jLang.t('program name:') + '</b></p><p><input type="text" id="popup-input-1" placeholder="' + jLang.t('new program name') + '" value="' + this.jutils.javascriptFormat(strName) + '" class="input-1 large uppercase"></p>';
		str += '<p><b>' + jLang.t('notes:') + '</b></p><p><textarea id="popup-input-2" class="textarea-1">' + strNotes + '</textarea></p>';
		str += '</div>'; //close form
		//check si on a deja un client selectionne si oui on affiche ses infos
		str += '<div style="margin-top:20px;">';
		str += '<a href="#" class="butt small" id="butt-select-client">';
		//le texte change si a deja un client ou pas
		if(this.jprogram.getClientId() !== -1){	
			str += jLang.t('select a different client');
		}else{
			str += jLang.t('select a client');
			}
		str += '</a>';
		str += '</div>';
		//
		str += '</div>'; //close popup-content
		//write content to popup
		$('#main-popup-window').html(str);
		//
		this.openPopup();
		//popup client
		$('#butt-select-client').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.openPopupClientSearch('from-save-as');
				}
			});
		//
		$('#butt-save-popup').data('popup-from', strFrom);
		$('#butt-save-popup').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//
				var strProgramName = oTmp.jutils.toUpper($('#popup-input-1').val());
				//minor check
				if(strProgramName == ''){
					$('#popup-input-1').addClass('error');
				}else{
					$('#popup-input-1').removeClass('error');
					//put the loader on the LI
					oTmp.showLoader(true, '#butt-save-popup', 0, 0);
					//get de data du form
					var id = oTmp.jprogram.getClientId();
					var strProgramNotes = $('#popup-input-2').val();
					var strPopupFrom = $(this).data('popup-from');
					//il faut checker avec le nouveau/ancien client et le nouveau nom de programme si existe ou pas
					oTmp.checkIfProgramNameCopyExist(id, strPopupFrom, strProgramName);
					}
				}
			});
		$('#butt-cancel-popup').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.closePopup();
				}
			});	
		}
		

	//----------------------------------------------------------------------------------------------------------------------*	
	this.openPopupClientSearch = function(strFrom){
		this.debug('openPopupClientSearch(' + strFrom + ')');
		//
		//si vient de program details on va chercher le nom du program dans la case texte, sinon on prend le nom de programme enregistre dans jProgram
		var strProgramName = '';
		var strProgramNotes = '';
		if(strFrom == 'from-prog-details'){
			//le popup n'etant pas encore ferme on peut aller chercher le data dans la case texte
			strProgramName = this.jutils.toUpper($('#input-modify-program-name').val());
			strProgramNotes = $('#input-modify-program-notes').val();
		}else if(strFrom == 'from-save-as'){
			//les infos du form save as
			strProgramName = this.jutils.toUpper($('#popup-input-1').val());
			strProgramNotes = $('#popup-input-2').val();
		}else{
			strProgramName = this.jprogram.getName();
			strProgramNotes = this.jprogram.getNotes();	
			}
		
		this.debug('PROGNAME:' + strProgramName);

		//butts bottom		
		var str = '';
		str += '<div class="popup-tools">';
		str += '<div>';
		str += '<a href="#" class="butt med" id="butt-cancel-popup">' + jLang.t('cancel') + '</a>';
		str += '</div>';
		str += '</div>';
		//search box
		str += '<div id="popup-content">';
		//top bar
		str += '<div class="layer-top-title">';
		//add butt
		str += '<a href="#" id="popup-butt-add-new-client"><img class="add-client" src="' + gServerPath + 'images/' + gBrand + '/add-user-invert.png"></a>';
		//
		str += '<div>';
		//input
		str += '<input type="text" id="input-popup-client-search-autocomplete" placeholder="' + jLang.t('client search') + '" value="" class="input-1 search-client" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false">';
		//clear input
		str += '<a href="#" class="butt-clear-search" attached-input-id="input-popup-client-search-autocomplete"><img draggable="false" src="' + gServerPath + 'images/' + gBrand + '/glyphicons_197_remove.png"></a>';
		str += '<a href="#" id="popup-butt-clear-client" attached-input-id="input-popup-client-search-autocomplete"><img draggable="false" class="client-search-icon" src="' + gServerPath + 'images/' + gBrand + '/60577.png"></a>';
		str += '</div>'; //close le div du input+clear+etc...
		str += '</div>'; //close layer-top-title
		//
		str += '<div id="popup-listing-client-search" class="div-sty-6"></div>';
		str += '</div>'; //close popup-content
		//write content to popup
		$('#main-popup-window').html(str);
		//
		this.openPopup();
		//
		//le listener pour le autocomplete
		this.jautocomplete.addInputBox({ 
				input:'input-popup-client-search-autocomplete',
				layer:'popup-content',
				type:'client-popup', 
				position:'under',
				programname: strProgramName,
				popupfrom: strFrom,
				});
	
		//focus sur le inut text
		$('#input-popup-client-search-autocomplete').focus();

		//actions on butt
		$('#popup-butt-add-new-client').data('programname', strProgramName);	
		$('#popup-butt-add-new-client').data('programnotes', strProgramNotes);		
		$('#popup-butt-add-new-client').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//infos
				var obj = {
					programname : $(this).data('programname'),
					programnotes : $(this).data('programnotes'),
					}
				//clean
				oTmp.jautocomplete.rmInputBox('input-popup-client-search-autocomplete');
				//close le popup
				oTmp.closePopup();
				//ouvre le nouveau
				oTmp.openAddNewClient('from-popup-client-search', obj);
				}
			});
		//
		$('#popup-butt-clear-client').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.resetPopupClientSearchWindow();
				}
			});
		
		//
		$('#butt-cancel-popup').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//clean
				oTmp.jautocomplete.rmInputBox('input-popup-client-search-autocomplete');
				//close
				oTmp.closePopup();
				}
			});


		}
	
	//----------------------------------------------------------------------------------------------------------------------*	
	this.checkProgramsBox = function(bCheck, id){
		this.debug('checkProgramsBox(' + bCheck + ', ' + id + ')');		
		//
		if(bCheck){
			$('#img-' + id).attr('imgselected','1');
			$('#img-' + id).addClass('imgselected-red');
		}else{
			$('#img-' + id).attr('imgselected','0');
			$('#img-' + id).removeClass('imgselected-red');
			}
		}		
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.rmItemsArrayFromPrograms = function(arr){
		this.debug('rmItemsArrayFromPrograms(' + arr + ')');		
		//
		for(var o in arr){
			this.rmExercice(arr[o], true);
			}
		}	
			
		
	//----------------------------------------------------------------------------------------------------------------------*	
	this.unlightItemsFromSearch = function(id){
		this.debug('unlightItemsFromSearch(' + id + ')');		
		//
		$('#search-img-' + id).attr('imgselected','0');
		$('#search-img-' + id).removeClass('imgselected inprogramlist');
		}
		
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.changeSearchExerciceCounter = function(bShow){
		this.debug('changeSearchExerciceCounter(' + bShow + ')');
		
		//si on le montre et que le layer est ouvert
		if(bShow && ($('#layer-search').attr('showed') == '1' && $('#layer-search').attr('showing') == '0') && this.jsearch.getLastResultCount() > 0){
			$('#search-exercice-counter > .msg').html('<img style="width:12px;height:auto;" src="' + gServerPath + 'images/' + gBrand + '/glyphicons_chevron-up.png">&nbsp;&nbsp;&nbsp;&nbsp;' + jLang.t('showing ') + this.jsearch.getExercicesCount() + jLang.t(' of ') + this.jsearch.getLastResultCount() + '&nbsp;&nbsp;&nbsp;&nbsp;<img style="width:12px;height:auto;" src="' + gServerPath + 'images/' + gBrand + '/glyphicons_chevron-up.png">');
			$('#search-exercice-counter').addClass('show');	
		}else{
			$('#search-exercice-counter').removeClass('show');
			}
		
		}


	//----------------------------------------------------------------------------------------------------------------------*
	//est calle par jsearch.js
	//si la valeur == -1 alors pas de filtre
	//si la valeur == 0 alors on affiche seulement ceux avec du userdata
	this.openSearchFilters = function(){
		this.debug('openSearchFilters()');	

		var str = '';
		//filtre de module
		var arrFilterNames = this.jsearch.getFilterFilters();
		//si on a des filtres
		if(arrFilterNames.length > 0){
			//les select box filter
			//str += '<optgroup label="' + jLang.t('tag filter') + '">';
			str += '<option value="-1" >' + jLang.t('no filters') + '</option>';		
			str += '<option value="-2" >' + jLang.t('show only my exercises') + '</option>';
			//str += '<option value="-3" >' + jLang.t('show only my favorites') + '</option>'; //va aller a plus tard
			//str += '<option value="-4" >' + jLang.t('show only my modified exercises') + '</option>'; //va aller a plus tard
			//str += '</optgroup>';
			str += '<optgroup label="" style="margin-top:5px;"></optgroup>';
			str += '<optgroup label="' + jLang.t('category filter') + '">';
			for(var o in arrFilterNames){
				str += '<option value="' + arrFilterNames[o].id + '" ';
				if(arrFilterNames[o].id == this.jsearch.selectedFilterId){
					str += ' selected ';
					}
				str += '>' + arrFilterNames[o].name + '</option>';	
				}
			str += '</optgroup>';
			$('#search-select-filter').html(str);
			//reduce size of the module select
			$('#search-select-module').addClass('reduce');
			//check si le val est a -1 pour la couleur
			if(parseInt($('#search-select-module').val()) != -1){
				$('#search-select-module').addClass('filter-on');
				}
			//show filters
			$('#search-select-filter').removeClass('filter-on');;
			$('#search-select-filter').addClass('show');
			//
			$('#search-select-filter').unbind();	
			$('#search-select-filter').change(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					var filterId = parseInt($('#search-select-filter').val());
					//si a -1 ou autres on change la couleur
					if(filterId == -1){
						$('#search-select-filter').removeClass('filter-on');
					}else{
						$('#search-select-filter').addClass('filter-on');
						}
					//
					oTmp.jsearch.applySearchFilter(filterId);
					}
				});	

		}else{
			//pas de filtre alors juste les modules
			//no reduce size of the module select
			$('#search-select-module').removeClass('reduce');
			//check si le val est a -1 pour la couleur
			if(parseInt($('#search-select-module').val()) != -1){
				$('#search-select-module').addClass('filter-on');
				}
			}

		}
	

	//----------------------------------------------------------------------------------------------------------------------*
	this.fillSearchLoop = function(bFromTop){
		this.debug('fillSearchLoop(' + bFromTop + ')');	
		//loop
		var iMaxLoop = 12;
		if(bFromTop){
			//il va falloir le calculer selon la grandeur des icone et de l'ecran opur faire apparaitre la scrollbar sinon le event ne fonctionne pas
			var maxW = Math.floor(this.containerSize.w / this.boxSettings.minW);	
			var maxH = Math.floor((this.containerSize.h) / this.boxSettings.minH); 
			//
			iMaxLoop = Math.ceil(maxW * maxH); 
			}
		//
		var iZoomId = parseInt($('#butt-zoom-in').attr('zoom-id'));
		var iLoop = 0;
		while(iLoop < iMaxLoop){
			//check si reste du data dedans
			var data = this.jsearch.getLastResult();
			if(typeof(data) == 'object'){
				//exercice obj
				var oExercice = new JExercice(data.id, data, 'search-img-' + data.id);
				//the box
				var str = '<li id="' + oExercice.getBoxName() +'" class="box-img';
				//si dans les programmes
				if(this.jprogram.contains(oExercice.getId())){
					str += ' inprogramlist " imgselected="1" ';
				}else{
					str += '" imgselected="0" ';
					}
				str += 'exercice-id="' +  oExercice.getId() + '"><div class="div-sty-23 search"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" src="' + oExercice.getThumb() + '"></div><div class="box-img-text"></div></li>';
				//append
				$('#listing-search').append(str);
				//add exercice box to this.jsearch class
				this.jsearch.addExercice(oExercice.getId(), oExercice);
				//add event to the box for single and press event	
				this.boxSearchEvent(oExercice.getId());	
				//
				/*
				if(this.contentViewDisplay && iZoomId == -1){
					this.displaySingleSearchBoxHasContentView(true, oExercice);
				}else{
					this.displaySingleSearchBoxHasContentView(false, oExercice);
					}
				*/
				//only one kind of display
				this.displaySingleSearchBoxHasContentView(oExercice);

			}else{
				break;
				}
			//increment
			iLoop++;
			}
		this.changeSearchExerciceCounter(true);
			
		}
	

	//----------------------------------------------------------------------------------------------------------------------*
	this.fillSearch = function(){
		this.debug('fillSearch()');		
		//le event sur le scroll
		$('#layer-search').on('scroll', this.scrollEventOnLayerSearchForLoopData.bind(this));
		//clear
		$('#listing-search').html('');
		//toptop
		$('#listing-search').scrollTop(0);
		
		//check si vide sinon affche msg
		if(this.jsearch.getLastResultCount() > 0){
			//fill the first result
			this.fillSearchLoop(true);	
		}else{
			//on affiche no result
			$('#listing-search').html('<div style="margin:5px 10px;">' + jLang.t('no result') + '</div>');
			//on enleve la barre de counter
			this.changeSearchExerciceCounter(false);
			}
		}
		

	//----------------------------------------------------------------------------------------------------------------------*	
	this.rmItemsFromPrograms = function(id, bAnimate){
		this.debug('rmItemsFromPrograms(' + id + ', ' + bAnimate + ')');		
		
		//animate will call the rest at complete
		if(bAnimate){
			this.removeProgramAnimation(id);
		}else{
			this.rmItemsFromProgramAtEndOfAnimation(id);
			}
		}	
		
	
	//----------------------------------------------------------------------------------------------------------------------*	
	this.rmItemsFromProgramAtEndOfAnimation = function(id){
		this.debug('rmItemsFromProgramAtEndOfAnimation(' + id + ')');		
		
		//le node
		$('#img-' + id).remove();
		//the array
		this.jprogram.rmExercice(id);
		//
		this.changeProgramCount();
		//change le save state de program
		this.changeProgramSaveState(false);
		//		
		}	
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.removeProgramAnimation = function(id){
		this.debug('removeProgramAnimation(' + id + ')');


		//on check si c'est un selected ou non
		var bCheck = parseInt($('#img-' + id).attr('imgselected'));	
		var strButtName = '#butt-delete-uncheck-items';
		if(bCheck){
			strButtName = '#butt-delete-check-items';
			}
		
		var wButt = $(strButtName).width();
		var hButt = $(strButtName).height();
		var xButt = $(strButtName).offset().left;
		var yButt = $(strButtName).offset().top;
		
		//anim img final size
		var whImg = parseInt(wButt/4);
		
		//the selected
		$('#img-' + id).addClass('trashed');
		
		//get the position of the clicked element
		var x = $('#img-' + id).offset().left;
		var y = $('#img-' + id).offset().top;
		
		//animate
		//draw the box
		var str = '';
		str += '<div style="z-index:50;top:' + y + 'px;left:' + x + 'px;position:absolute;" id="div-anim-' + id + '"><div class="box-img-anim trashed" style="width:' + this.boxSettings.w + ';height:' + this.boxSettings.h + ';"></div></div>';
		$('body').append(str);
		//anim
		$('#div-anim-' + id ).animate( 
				{
					top: yButt + 'px',
					left: xButt + 'px',
					}, 
				{
					queue:false,
					duration:this.basicAnimSpeed/2, 
					complete:function(){
						//get parent class
						var oTmp = $(document).data('jappzclass');
						if(typeof(oTmp) == 'object'){
							$('#div-anim-' + id).remove();
							oTmp.rmItemsFromProgramAtEndOfAnimation(id);
							}
						}
					}	
			);
			
		//anim
		$('#div-anim-' + id + ' > div').animate(
				{
					width: whImg + 'px',
					height: whImg + 'px',
					}, 
				{
					queue:false,
					duration:this.basicAnimSpeed/2, 
					}	
			);			
		
		}
		
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.addProgramAnimation = function(id){
		this.debug('addProgramAnimation(' + id + ')');	

		//IMPORTANTT: le serach a seulement un format de zoom alors on prend celui par defaut
				
		//le tool
		var wButt = $('#butt-programs > img').width();
		var hButt = $('#butt-programs > img').height();
		var xButt = $('#butt-programs > img').offset().left;
		var yButt = $('#butt-programs > img').offset().top;
		
		//anim img final size
		var whImg = parseInt(wButt/2);
		
		//get the position of the clicked element
		var x = $('#search-img-' + id).offset().left;
		var y = $('#search-img-' + id).offset().top;
		
		//animate
		//draw the box
		var str = '';
		str += '<div style="z-index:150;top:' + y + 'px;left:' + x + 'px;position:absolute;" id="div-anim-' + id + '"><div class="box-img-anim" style="width:' + this.zoomFactor[this.zoomFactorSearchIndex].w + ';height:' + this.zoomFactor[this.zoomFactorSearchIndex].h + ';"></div></div>';
		$('body').append(str);
		//anim
		$('#div-anim-' + id).animate(
				{
					top: yButt + 'px',
					left: xButt + 'px',
					}, 
				{
					queue:false,
					duration:this.basicAnimSpeed/2, 
					complete:function(){
						$('#div-anim-' + id).remove();
						}
					}	
			);
		//anim
		$('#div-anim-' + id + ' > div').animate(
				{
					width: whImg + 'px',
					height: whImg + 'px',
					}, 
				{
					queue:false,
					duration:this.basicAnimSpeed/2, 
					}	
			);	
		
		}
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.changeProgramCount = function(){
		this.debug('changeProgramCount()');		
		
		var count = this.jutils.countArray(this.jprogram.arrExercices);
		if(count === false || count == 0){
			count = '';
			}
		$('#program-count-text').html(count);
		}
	
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.addBox = function(id, obj){
		this.debug('addBox(' + id + ', ' + obj + ')');
		//check le type de display
		var iZoomId = parseInt($('#butt-zoom-in').attr('zoom-id'));
		
		var oExercice = new JExercice(id, obj, 'img-' + id);
		//extra class for img
		var strExtraClass = '';
		if(oExercice.getMirror() === 1){
			strExtraClass += ' mirror ';
			}
		//		
		if(typeof(oExercice) == 'object'){
			//the box
			$('#listing-programs').append('<li class="box-img" id="' + oExercice.getBoxName() +'" imgselected="0" exercice-id="' +  oExercice.getId() + '"><div class="div-sty-23 programs"><div class="mask-img"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" src="' + oExercice.getThumb() + '" class="' + strExtraClass + '"></div></div><div class="box-img-text"></div></li>');
			//register exercice to program
			this.jprogram.addExercice(id, oExercice);
			//display selon content-view ou pas
			if(this.contentViewDisplay && iZoomId == -1){
				this.displaySingleProgramBoxHasContentView(true, oExercice);
			}else{
				this.displaySingleProgramBoxHasContentView(false, oExercice);
				}
			//click and hammer
			this.bindEventToBox(id);
			//le counter
			this.changeProgramCount();
			//change le save state de program
			this.changeProgramSaveState(false);
			}							
		
		}
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.bindEventToBox = function(id){
		this.debug('bindEventToBox(' + id + ')');
		//click on box will be replace by singletap event: tap not sure because not performing enough
		//on apple tablet singletap is faster then click ??? ... mystere
		//event layer box instance
		this.boxEvent(id);
		}
	
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.addItemsToProgramsFromCaroussel = function(id){
		this.debug('addItemsToProgramsFromCaroussel(' + id + ')');
		if(!this.jprogram.contains(id)){
			//change the border of the serach box
			$('#search-img-' + id).attr('imgselected','1');
			$('#search-img-' + id).addClass('imgselected');
			//
			//va modifier le programdata de obj avant de l'envoyer a jprogram
			var obj = this.getModifiedSearchCarousselData(id, this.jsearch.getExerciceObjById(id));

			//on check pour le flip et mirror image
			obj.flip = $('#items-' + id).attr('flip-on');
			obj.mirror = $('#items-' + id).attr('mirror-on');

			//le addBox va rajouter l'exercice au jprogram
			this.addBox(id, obj);
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getModifiedSearchCarousselData = function(id, obj){
		this.debug('getModifiedSearchCarousselData(' + id + ', ' + obj + ')');
		//container des input en langue locale, doit etre un object comme dans les notes explicatives plus haut
		var objLocale = this.buildModifiedCarousselData(id);
		//en meme temps on check si la case 'set has my instruction' est coche si oui on envoie les nouvelles infos au serveur
		if(this.bSetHasMyInstruction){
			this.changeSearchCarousselSaveButtStateOnInputChange(1, id);	
			this.jsearch.setHasMyInstruction(id, objLocale, 'search');
			}
		//ajoute le programdata que l'on retourne pour mettre dana le jprogram et creer la addBox avec
		obj.programdata = JSON.stringify(objLocale);
		//minor check
		return obj;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.buildModifiedCarousselData = function(id){
		this.debug('buildModifiedCarousselData(' + id + ')');
		//on va dans le caroussel chercher les infos que l'usager a choisit, 
		//soit: original ou my instruction et ceux qu'il a pu editer
		//toutes les langues offertes
		var arrLang = $('#items-' + id + ' .title-and-description .div-caroussel-lang').map(function(index){
			return $(this).attr('lang-id');
			}).get(); 
		//le .get() est pour le chaining des retour pour que ca donne un array ["en_US", "es_MX", "fr_CA"]	
		//container des input en langue locale, doit etre un object comme dans les notes explicatives plus haut
		var objLocale = {'locale':{}};
		//pour chaque langue on va chercher les input de title et description
		for(var o in arrLang){
			//title
			var title = $('#items-' + id + ' .title-and-description .div-caroussel-lang[lang-id="' + arrLang[o] + '"] input[type=text]').val();
			//description
			var description = $('#items-' + id + ' .title-and-description .div-caroussel-lang[lang-id="' + arrLang[o] + '"] textarea').val();
			//on ajout a objLocale
			objLocale.locale[arrLang[o]] = {
				'description' : description,
				'short_title' : title,
				//'level' : '',
				}
			}
		//minor check
		return objLocale;
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.addExerciceToProgramFromTemplate = function(obj){
		this.debug('addExerciceToProgramFromTemplate', obj);	

		//seulement si on a un nom de programme/template valide sinon c'est que le template a ete delete et on try de le reloade
		if(typeof(obj) == 'object' && typeof(obj.name) == 'string'){
		
			//on call le serveur pour voir si le nom du template est deja un nom de programme, quand on a un client avec un prgId a -1
			if(this.jprogram.getClientId() !== -1 && this.jprogram.getProgId() === -1 && this.jprogram.getName() == ''){
				var oClient = this.jprogram.getClient();
				this.checkIfModifiedProgramNameExist(oClient ,this.jutils.toUpper(obj.name), '');
				}

			//on rajoute au programme
			if(typeof(obj.exercices) == 'object'){
				for(var o in obj.exercices){
					if(!this.jprogram.contains(obj.exercices[o].id)){
						this.addBox(obj.exercices[o].id, obj.exercices[o]);
						}
					}
				}

			//check si on a un nom de programme si non on prend celui du template par defaut
			if(this.jprogram.getName() == ''){
				this.jprogram.setName(obj.name);
				this.setMainProgramName(-1, -1); //client-id, prog-id
				}

			//si c'est uniquement le template alors on se met en mode template
			if(this.jprogram.getClientId() === -1 && this.jprogram.getProgId() === -1){
				//on met le id et le name et le module
				// si on a pas deja un template d'ouvert on met le nom sinon on garde celui deja ouvert
				if(this.jtemplate.getName() == ''){
					this.jtemplate.setName(obj.name);
					}
				if(this.jtemplate.getModule() == ''){
					this.jtemplate.setModule(obj.module);
					}
				if(this.jtemplate.getId() == -1){
					this.jtemplate.setId(obj.id);
					}
				if(this.jtemplate.getNotes() == ''){
					this.jtemplate.setNotes(obj.notes);
					}
				//set le program name only pour affichage
				this.jprogram.setName(this.jtemplate.getName());
				//set prog name inner program layer
				$('#main-program-name-text').html(this.jtemplate.getName());
				//top of the window
				$('#top-program-name').html(this.jtemplate.getName());
				//change le main settings program title
				$('#main-settings-program-name').html(this.jtemplate.getName());
				// amjad changes
				//set le program note only pour affichage
				this.jprogram.setNotes(this.jtemplate.getNotes());
				// end of amjad changes
				//on set un faux nom client pour dire qu'on edite un template
				$('#top-client-name').html(jLang.t('template edition'));
				//on change icon user
				$('#top-user-image').attr('src', gServerPath + 'images/' + gBrand + '/menu-template.png');
				//si jamais ce n'est pas un rajout a un template deja preloade, 
				//on le fait ici car toute les insertions on cree un event de state change du save
				if(this.jtemplate.getId() === obj.id){
					//alors on set le state du save a true
					this.changeProgramSaveState(true);	
					}
				}
			//close layers
			this.closeAllLayers();
		}else{
			//message the user that we didn't find the template
			this.openAlert('error', jLang.t('error!'), jLang.t('sorry template doesnt exist anymore.'), false);
			}
		//clear graphics
		this.resetSearchWindow();
			
		}
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.addItemsToPrograms = function(id){
		this.debug('addItemsToPrograms(' + id + ')');		
		
		//come from search exercice window
		if(!this.jprogram.contains(id)){
			var obj = this.jsearch.getExerciceObjById(id);
			//
			this.addBox(id, obj);
			//
			this.addProgramAnimation(id);
			}
		}
		
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.openClientSectionFromId = function(){
		this.debug('openClientSectionFromId()');		
		
		var id = this.jprogram.getClientId();
		if(id !== -1){
			//open to client from id serach id from test array for now
			this.openEditClientProfileById(id);
		}else{
			//fade carosuel if some
			this.closePopupCaroussel();
			//fade popup if some
			this.closePopup();
			//
			this.openPopupClientSearch('from-main-client-name');
			}
		}
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.openAddNewClient = function(strFrom, objProgInfos){
		this.debug('openAddNewClient(' + strFrom + ', ' + objProgInfos + ')');
		//on doit changer les boutons si veient de /ClientSerach Ppopup ou le /ClientSerach Onglet
		var str = '';
		str += '<div class="popup-tools">';
		str += '<div>';
		if(strFrom == 'from-popup-client-search'){
			str += '<a href="#" class="butt med" id="butt-save-popup">' + jLang.t('save') + '</a>';
			str += '<a href="#" class="butt med" id="butt-cancel-popup">' + jLang.t('cancel') + '</a>';
		}else if(strFrom == 'from-onglet-client-search'){
			str += '<a href="#" class="butt med med-3" id="butt-save-popup">' + jLang.t('save') + '</a>';
			str += '<a href="#" class="butt med med-3" id="butt-cancel-popup">' + jLang.t('cancel') + '</a>';
			str += '<a href="#" class="butt med med-3" id="add-new-program">' + jLang.t('new program') + '</a>';
			}
		str += '</div>';
		str += '</div>';
		//
		str += '<div id="popup-content">';
		str += '<h1 class="h1-close-popup">' + jLang.t('add new client') + '</h1>';
		//
		str += '<div class="popup-form">';
		str += '<p><b>' + jLang.t('firstname:') + '</b></p><p><input type="text" id="input-client-prenom" value="" class="input-1 large"></p>';
		str += '<p><b>' + jLang.t('lastname:') + '</b></p><p><input type="text" id="input-client-nom" value="" class="input-1 large"></p>';
		str += '<p><b>' + jLang.t('email:') + '</b></p><p><input type="text" id="input-client-courriel" value="" class="input-1 large"></p>';
		//select box des langues depend des angues de la license que l'on a initialise dans juser arrLang via le get basics infos
		var arrLang = this.juser.getArrLang();
		str += '<p><b>' + jLang.t('languages:') + '</b></p><p><select id="input-client-locale" class="select-1 search">';
		for(var o in arrLang){
			str += '<option value="' + o + '" ';
			if(gLocaleLang == o){
				str += ' selected ';
				}
			str += '>' + arrLang[o] + '</option>';	
			}
		str += '</select></p>';
		//
		str += '</div>'; //close popup-form
		str += '</div>'; //close popup-content
		//
		$('#main-popup-window').html(str);
		//open popup
		this.openPopup();
		//give focus
		$('#input-client-prenom').focus();
		//actions
		if(strFrom == 'from-popup-client-search'){ //from the popup 
			//save and cancel action
			$('#butt-save-popup').data('programinfos', objProgInfos);
			$('#butt-save-popup').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					//si la string le nom deprogramme est vide
					var obj = $(this).data('programinfos');
					if(typeof(obj) == 'object'){
						//on change les finoe de base du programme
						//change le nom du prog
						oTmp.jprogram.setName(obj.programname);
						oTmp.jprogram.setNotes(obj.programnotes);
						//routine
						oTmp.setMainProgramName(-1, -1); //client-id, prog-id	
						}
					//on save le client
					oTmp.saveNewClientToDb(false, true);
					}
				});
			//cancel action
			$('#butt-cancel-popup').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					oTmp.closePopup();
					}
				});
		}else if(strFrom == 'from-onglet-client-search'){ //from the client tab
			//save and cancel action
			$('#add-new-program').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					//meme chose que save avec le programe en plus
					oTmp.saveNewClientToDb(true, false);
					}
				});
			//save and cancel action
			$('#butt-save-popup').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					oTmp.saveNewClientToDb(false, false);
					}
				});
			//cancel action
			$('#butt-cancel-popup').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					oTmp.closePopup();
					}
				});	
			}
		}
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.saveClientModification = function(clientId){
		this.debug('saveClientModification(' + clientId + ')');		
		
		//on va chercher la liste des programmes
		var strProgIds = $('#client-details-program-listing').attr('removed-program-ids');
		if(strProgIds == null){
			strProgIds = '';
			}
			
		//need some validation on mandatory fields
		var obj = {
			id: clientId,
			firstname: $('#input-client-prenom').val(),
			lastname: $('#input-client-nom').val(),
			email: $('#input-client-courriel').val(),
			locale: $('#input-client-locale').val(),
			rmprograms: strProgIds,
			};

		//reset errors on all field
		$('.input-1').removeClass('error');	
		//error message
		var arrField = [];	
		//on check si vide sinon message
		if(obj.firstname == ''){
			arrField.push('#input-client-prenom');
			}
		if(obj.lastname == ''){
			arrField.push('#input-client-nom');
			}
		//check si erreur
		if(arrField.length > 0){
			//color the field
			for(var o in arrField){
				$(arrField[o]).addClass('error');
				}
		}else{
			//efface la form et on met le loader
			this.showLoader(true, '#butt-save-popup', 0, 0);		
			//real service call
			this.jclientmanager.modifyClientInfosToDb(obj);
			}

		
		}	
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.saveNewClientToDb = function(bNewProgram, bFromPopup){
		this.debug('saveNewClientToDb(' + bNewProgram + ', ' + bFromPopup + ')');		
		
		//need some validation on mandatory fields
		var obj = {
			firstname: $('#input-client-prenom').val(),
			lastname: $('#input-client-nom').val(),
			//age: $('#input-client-age').val(),
			email: $('#input-client-courriel').val(),
			//phone: $('#input-client-mobile').val(),
			locale: $('#input-client-locale').val(),
			};

		//reset errors on all field
		$('.input-1').removeClass('error');	
		//error message
		var arrField = [];	
		//on check si vide sinon message
		if(obj.firstname == ''){
			arrField.push('#input-client-prenom');
			}
		if(obj.lastname == ''){
			arrField.push('#input-client-nom');
			}
		//check si erreur
		if(arrField.length > 0){
			//color the field
			for(var o in arrField){
				$(arrField[o]).addClass('error');
				}
		}else{
			//efface la form et on met le loader
			this.showLoader(true, '#butt-save-popup', 0, 0);		
			//real service call
			this.jclientmanager.addNewClientToDb(obj, bNewProgram, bFromPopup);
			}
		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.saveNewClient = function(obj, bNewProgram, bFromPopup){ //obj retourne par le serveur avec les meme infos que dans le listing
		this.debug('saveNewClient(' + obj + ', ' + bNewProgram + ', ' + bFromPopup + ')');		
		this.debug('saveNewClient', obj);
		
		//di vient du popup add client a partir du client search popup
		if(bFromPopup){
			var strPopupFrom = 'from-prog-details';
			var strProgramName = this.jprogram.getName();
			//on va chercher
			for(var o in obj){
				//add client to the manager
				this.jclientmanager.addClient(obj[o].id, obj[o]);
				//rajoute le client au programme
				this.addClientToProgram(obj[o].id, strPopupFrom, strProgramName);
				//on ouvre le nouveau client dans le client search
				this.fillClientSearch(obj);
				//on ouvre le LI
				this.openClientProfileById(parseInt(obj[o].id));
				//get out since there is only one object
				break;
				}
			
					
		}else{
			//hide the form
			//on met le client dans le listing comme une recherche
			this.fillClientSearch(obj);
			//pour lr id c,est le premier car est tout seul pas le listing
			var clientId = parseInt(obj[0].id); //
			//on ouvre le LI
			this.openClientProfileById(parseInt(clientId));
			//check si un nouceau program
			if(bNewProgram){
				//on clear celui du programme precedent
				this.jprogram.clearClient();
				//this.jprogram.clearProgram();
				//routine
				this.createNewProgramForClientFromPopup(clientId);
			}else{
				//close popup
				this.closePopup();
				}
			}
		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.saveModifyClient = function(clientId, obj){
		this.debug('saveModifyClient(' + clientId + ', ' + obj + ')');		
		
		//modify client
		this.jclientmanager.modifyClient(clientId, obj);
		//refresh les infos dans le listing des clients si il y a
		var oClient = this.jclientmanager.getClient(clientId);
		//le li inner
		this.modifyClientSearchInnerLi(oClient);
		//modify the program if the user is the same
		if(this.jprogram.getClientId() == clientId){
			//set client infos
			this.jprogram.setClient(oClient);
			//change top client name
			this.setMainClientName();
			}
		//close popup
		this.closePopup();
		//check if programs deleted is opened in the appz
		var arrRmProg = obj.rmprograms.split(',');
		for(var o in arrRmProg){
			//on reload si celui supprime est ouvert dans l'interface
			if(arrRmProg[o] == this.jprogram.getProgId()){ //check si est ouvert
				//clear prog
				this.jprogram.clear();
				this.jprogram.clearProgram();
				//reset la fenetre
				this.resetProgramWindow();
				this.setMainProgramName(clientId, -1); //client-id, prog-id
				break;
				}
			}
		}		
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.closeAlert = function(){
		this.debug('closeAlert()');			
		//out
		$('#main-popup-alert').fadeOut(0, function(){
			//on title
			$('#main-popup-alert > .content-box > .content > H1').html('');
			//content
			$('#main-popup-alert > .content-box > .content > .text').html('');
			//butts
			$('#main-popup-alert > .content-box > .butts > .actions').html('');
			});
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.openAlert = function(type, title, content, bAutoHide){
		this.debug('openAlert(' + type + ', ' + title + ', ' + '[content]'+ ', ' + bAutoHide + ')');			
		
		//le type pour l'icone a afficher
		var strSaveButtName = 'butt-save-alert';
		var strCancelButtName = 'butt-close-alert';
		//title
		$('#main-popup-alert > .content-box > .content > H1').html(title);
		//content
		$('#main-popup-alert > .content-box > .content > .text').html(content);
		//type va avoir de diffrent bouton
		var strButt = '';
		if(type == 'saving'){
			strButt += '<a href="#" class="butt med alert" id="' + strSaveButtName + '">' + jLang.t('save') + '</a>';
			strButt += '<a href="#" class="butt med alert" id="' + strCancelButtName + '">' + jLang.t('cancel') + '</a>';
		}else if(type == 'comfirm'){
			strButt += '<a href="#" class="butt med alert" id="' + strSaveButtName + '">' + jLang.t('yes') + '</a>';
			strButt += '<a href="#" class="butt med alert" id="' + strCancelButtName + '">' + jLang.t('no') + '</a>';
		}else if(type == 'apply'){
			strButt += '<a href="#" class="butt med alert" id="' + strSaveButtName + '">' + jLang.t('apply') + '</a>';
			strButt += '<a href="#" class="butt med alert" id="' + strCancelButtName + '">' + jLang.t('cancel') + '</a>';
		}else{ //messsage, alert, warning
			strButt += '<a href="#" class="butt med alert" id="' + strCancelButtName + '">' + jLang.t('close') + '</a>';
			}
		//on ajoute au content butts
		$('#main-popup-alert > .content-box > .butts > .actions').html(strButt);
		//l action sur le close est inclut peu importe le type
		$('#' +  strCancelButtName).click(function(e){
			e.preventDefault(); 
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.closeAlert();
				}
			});	
		//on lui mais un enter key pour fermer la fenetre si jamais aucun input
		/*
		if(type != 'saving' && type != 'comfirm'){
			$('#' +  strCancelButtName).keyup(function(e){
				if(e.which == '13'){
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						oTmp.closeAlert();
						}
					}
				});
			}
		*/
		//on ouvre la fenetre
		$('#main-popup-alert').fadeIn(0);

		if(bAutoHide){
			setTimeout(function(){
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					oTmp.closeAlert();
					}	
				},1500);
			}


		//DWIZZEL: 
		//important, l'action du save se fera dans la fonction qui va appeller la methode this.openAlert();
		//la fenetre etant ecrite la programmation des action pourra se faire pendant qu'elle s'ouvre
		//on passe le cancel aussi si on veut changer l'action par defaut autre que de fermer la fenetre	
		return [strSaveButtName, strCancelButtName];
		}


	
	//----------------------------------------------------------------------------------------------------------------------*
	this.closePopup = function(){
		this.debug('closePopup()');			
		
		//
		$('#main-popup-window').attr('showing', 0);
		//on efface le contenu
		$('#main-popup-window').html('');
		//on part
		$('#main-popup-window').fadeOut(0);
		
		//	et que l'on a le focus sur un input text avec le mini clavier ouvert sur le cell 
		// le containerSize.h n'est pas bon car il calcule l'espace moins celuid du clavier alors on voit toujours le layer-seacrh en partie 
		
		}
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.closePopupCaroussel = function(){
		this.debug('closePopupCaroussel()');			
		
		$('#main-popup-caroussel').html('');
		//$('#main-popup-caroussel').fadeOut(0);
		$('#main-popup-caroussel').css({'display':'none'});
		//search et program listing
		$('#main-container').css({'display':'block'});
		}
		
		
	//-----------------------------------------------------------------------------------------------------------*
	this.rightClickMobileBugBlocker = function(bEnable){
		this.debug('rightClickMobileBugBlocker(' + bEnable + ')');				
		if(bEnable){
			var str = '<div id="div-click-blocker"></div>'
			$('#main-popup-caroussel').append(str);

		}else{
			setTimeout(function(){
				$('#div-click-blocker').remove();
				},1000);

			}
		}

	//-----------------------------------------------------------------------------------------------------------*
	this.openPopupCaroussel = function(){
		this.debug('openPopupCaroussel()');
		
		//avec animation fadeIn
		//hide
		
		this.rightClickMobileBugBlocker(true);
		$('#main-container').css({'display':'none'});
		$('#main-popup-caroussel').css({'display':'block'});
		this.rightClickMobileBugBlocker(false);
		
		//mais vu que le content peut etre trs gros on utilise uniquement un bouton
		$('#butt-caroussel-close').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.closePopupCaroussel();
				}
			});
		}	
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.openPopup = function(){
		this.debug('openPopup()');
		
		//on ferme le carousel slider si open avec program ou search
		this.closePopupCaroussel();
		//ouvre le popup
		$('#main-popup-window').attr('showing', 1);
		$('#main-popup-window').fadeIn(0);
		}	

		//----------------------------------------------------------------------------------------------------------------------*
	this.resetPopupClientSearchWindow = function(){
		this.debug('resetPopupClientSearchWindow()');
		
		//
		this.jautocomplete.resetSearchInputBox();
		//clear
		$('#popup-listing-client-search').html('');
		//scrool top
		$('#popup-content').scrollTop(0);	
		
		}
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.resetClientSearchWindow = function(){
		this.debug('resetClientSearchWindow()');
		
		//on clear le arr de jclientmanager
		this.jclientmanager.clear();
		//
		this.jautocomplete.resetSearchInputBox();
		//clear
		$('#listing-client-search').html('');
		//scrool top
		$('#layer-client').scrollTop(0);
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.resetClientSearchWindowForResult = function(){
		this.debug('resetClientSearchWindowForResult()');
		
		//on clear le arr de jclientmanager
		this.jclientmanager.clear();
		//clear
		$('#listing-client-search').html('');
		//scrool top
		$('#layer-client').scrollTop(0);
		//
		this.showLoader(true, '#listing-client-search', 10, 10);
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.resetPopupClientSearchWindowForResult = function(){
		this.debug('resetPopupClientSearchWindowForResult()');
		//clear
		$('#popup-listing-client-search').html('');
		//scrool top
		$('#popup-content').scrollTop(0);
		//
		this.showLoader(false, '#popup-listing-client-search', 10, 0);
		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.modifyClientSearchInnerLi = function(oClient){
		this.debug('modifyClientSearchInnerLi(' + oClient + ')');
		
		//change content of the client saerach result LI
		$('#listing-client-search > .client-search-result > li[client-id="' + oClient.getId() + '"]').html(this.buildClientSearchInnerLi(oClient));
		//li inner event
		this.addEventOnClientLiListing();
		//open the li we are editing
		this.openClientProfileById(oClient.getId());
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.buildPopupClientSearchInnerLi = function(oClient){
		this.debug('buildPopupClientSearchInnerLi(' + oClient + ')');
		
		var str = '';
		str += '<div class="select-client-profile" client-id="' + oClient.getId() + '">';
		str += '<span class="name">' + oClient.getCompleteName() + '</span>';
		str += '</div>';
				
		return str;	
		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.buildClientSearchInnerLi = function(oClient){
		this.debug('buildClientSearchInnerLi', oClient);
		
		var str = '';
		str += '<div class="open-client-profile" client-id="' + oClient.getId() + '">';
		str += '<span class="name">' + oClient.getCompleteName() + '</span>';
		str += '<div class="icon"><a href="#" id=""><img  draggable="false"  src="' + gServerPath + 'images/' + gBrand + '/glyphicons_chevron-down.png"></a></div>';
		str += '</div>';
		//
		str += '<div class="client-profile-box" id="client-profile-' + oClient.getId() + '">';
		//infos
		str += '<div class="edit-client-profile" client-id="' + oClient.getId() + '">';
		//pour la photo quand il y en aura une
		//
		str += '<p class="nomarge"><b>' + jLang.t('id:') + '</b> ' + oClient.getId() + '</p>';
		str += '<p class="nomarge"><b>' + jLang.t('name:') + '</b> ' + oClient.getCompleteName() + '</p>';
		str += '<p class="nomarge"><b>' + jLang.t('email:') + '</b> ' + oClient.getEmail() + '</p>';
		str += '<p class="nomarge"><a class="edit-client-profile" href="#" client-id="' + oClient.getId() + '"><img draggable="false"  src="' + gServerPath + 'images/' + gBrand + '/icone-program-edit-black.png"></a></p>';
		str += '</div>';
		
		//programs listing
		str += '<div style="clear:both;padding-top:10px;">';
		str += '<ul>';
		//arr porgs
		var arrPrograms = oClient.getArrProgramsName();
		for(var o in arrPrograms){
			str += '<li class="open-old-program" client-id="' + oClient.getId() + '" program-id="' + o + '"><a href="#">' + arrPrograms[o] + '</a></li>';
			}
		str += '</ul>';
		str += '<p><a class="add-new-program butt small" href="#" client-id="' + oClient.getId() + '">' + jLang.t('add new program') + '</a></p>';
		str += '</div>'; //close client-profile-box
		str += '</div>'; //style=
		
		return str;	
		}
		
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.addEventOnPopupClientLiListing = function(strPopupFrom, strProgramName){
		this.debug('addEventOnPopupClientLiListing(' + strPopupFrom + ', ' + strProgramName + ')');
		
		//remove older actions
		$('.select-client-profile').unbind();
		//action on click open
		$('.select-client-profile').data('programname', strProgramName);
		$('.select-client-profile').data('popup-from', strPopupFrom);
		$('.select-client-profile').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = $(this).attr('client-id');
				var strPopupFrom = $(this).data('popup-from');
				var strProgramName = $(this).data('programname');
				//put the loader on the LI
				oTmp.showLoader(false, '.select-client-profile[client-id=' + id + ']', 0, 0);
				//add
				if(strPopupFrom == 'from-save-as'){
					//il faut checker avec le nouveau client et le nouveau nom de programme si existe ou pas
					oTmp.checkIfProgramNameCopyExist(id, strPopupFrom, strProgramName);
				
				}else{	//from-prog-details OR from-saving-options
					oTmp.addClientToProgram(id, strPopupFrom, strProgramName);
					}
				}
			});
			
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.checkIfProgramNameCopyExist = function(iClientId, strPopupFrom, strProgramName){
		this.debug('checkIfProgramNameCopyExist(' + iClientId + ',' + strPopupFrom + ', ' + strProgramName + ')');

		//get le object client
		var oClient = this.jclientmanager.getClient(iClientId);
		//minor check
		if(typeof(oClient) == 'object'){
			//check local
			if(oClient.isProgramNameAlreadyExistInLocalData(strProgramName)){ 
				//existe deja alors on affiche un message comme quoi il peut overwrite ou choisir un autre nom
				this.popupAlertReplaceProgramName(strProgramName, oClient, {type:'client'});	
			}else{
				//on check sur le serveur si une copie avec le nom de programme du client existe deja
				this.jprogram.isClientProgramNameExist(oClient, strProgramName, strPopupFrom);
				}
			}

		}



	//----------------------------------------------------------------------------------------------------------------------*
	this.addClientToProgram = function(iClientId, strPopupFrom, strProgramName){
		this.debug('addClientToProgram(' + iClientId + ',' + strPopupFrom + ', ' + strProgramName + ')');
		//get the client object
		var oClient = this.jclientmanager.getClient(iClientId);
		//si pas de nom pas de sauvegarde
		if(strProgramName != ''){
			//check si le client a un program deja nomme
			this.checkIfInsertedClientProgramNameExist(oClient, strProgramName, strPopupFrom);
		}else{
			//on rajoute le client simplement sans verifier, la verif se fera quand il modifiera le nom du programme
			this.addClientToNoNameProgram(oClient, strPopupFrom);
			}
		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.addClientToNoNameProgram = function(oClient, strPopupFrom){
		this.debug('addClientToNoNameProgram(' + oClient + ',' + strPopupFrom + ')');
		//set the client of program
		this.jprogram.setClient(oClient);
		//change le save state de program
		this.changeProgramSaveState(false); //disable auto save untill we have some changes on it, name exercice, notes, etc...
		//set text
		this.setMainClientName();
		//close popup if some
		this.closePopup();
		}

	
	//----------------------------------------------------------------------------------------------------------------------*
	this.addEventOnClientLiListing = function(){
		this.debug('addEventOnClientLiListing()');
		
		//remove older actions
		$('.open-client-profile, .edit-client-profile, .add-new-program, .open-old-program').unbind();
		//action on click open
		$('.open-client-profile').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = $(this).attr('client-id');
				oTmp.openClientProfileById(id);
				}
			});
		//action on click edit
		$('.edit-client-profile').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = $(this).attr('client-id');
				oTmp.openEditClientProfileById(id);
				}
			});	
		//action on click open old prog
		$('.open-old-program').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var cliId = $(this).attr('client-id');
				var progId = $(this).attr('program-id');
				oTmp.openOldProgramForClient(cliId, progId);
				}
			});
		//action on click add newp program
		$('.add-new-program').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = $(this).attr('client-id');
				oTmp.createNewProgramForClientFromClientListing(id);
				}
			});	
		
		
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.fillPopupClientSearch = function(obj, strPopupFrom, strProgramName){
		this.debug('fillPopupClientSearch(' + obj + ', ' + strPopupFrom + ', ' + strProgramName + ')');
		
		var bNoResult = true;
		if(typeof(obj) == 'object'){
			if(obj.length > 0){
				bNoResult = false;
				var str = '<ul class="client-search-result popup">';
				for(var o in obj){
					//add client to the manager
					this.jclientmanager.addClient(obj[o].id, obj[o]);
					var oClient = this.jclientmanager.getClient(obj[o].id);
					//loop build str	
					if(typeof(oClient) == 'object'){
						str += '<li client-id="' + oClient.getId() + '">';
						str += this.buildPopupClientSearchInnerLi(oClient);
						str += '</li>';
						}
					}
				str += '</ul>';	
				//write html
				$('#popup-listing-client-search').html(str);
				//li event
				this.addEventOnPopupClientLiListing(strPopupFrom, strProgramName);	
				}
			}
		//
		if(bNoResult){
			$('#popup-listing-client-search').html('<div style="margin:10px;">' + jLang.t('no result') + '</div>');
			}
			
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.fillClientSearch = function(obj){
		this.debug('fillClientSearch', obj);
		
		var bNoResult = true;
		if(typeof(obj) == 'object'){
			if(obj.length > 0){
				bNoResult = false;
				var str = '<ul class="client-search-result">';
				for(var o in obj){
					//add client to the manager
					this.jclientmanager.addClient(obj[o].id, obj[o]);
					var oClient = this.jclientmanager.getClient(obj[o].id);
					//loop build str		
					if(typeof(oClient) == 'object'){
						str += '<li client-id="' + oClient.getId() + '">';
						str += this.buildClientSearchInnerLi(oClient);
						str += '</li>';
						}
					}
				str += '</ul>';	
				//write html
				$('#listing-client-search').html(str);
				//li event
				this.addEventOnClientLiListing();
				}
			}
		//
		if(bNoResult){
			$('#listing-client-search').html('<div style="margin:10px;">' + jLang.t('no result') + '</div>');
			}
				
		}
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.openEditProgramById = function(){
		this.debug('openEditProgramById()');

		//les titres et autres selon Template ou Program
		var strWinTitle, strProgField, strProgHint;
		
		var bIsTemplate = false;
		if(this.jprogram.getProgId() === -1 && this.jprogram.getClientId() === -1 && this.jtemplate.getId() !== -1){
			bIsTemplate = true;
			}

		//
		var strStyle = ' med';
		if(this.jprogram.getClientId() !== -1 || bIsTemplate){
			strStyle += ' med-3';
			}

		var str = '';
		str += '<div class="popup-tools">';
		str += '<div>';
		str += '<a href="#" class="butt ' + strStyle + '" id="butt-modify-program-modification">' + jLang.t('modify') + '</a>';
		if(this.jprogram.getClientId() !== -1){
			str += '<a href="#" class="butt ' + strStyle + '" id="butt-save-as-program-modification">' + jLang.t('save as') + '</a>';
		}else if(bIsTemplate){
			str += '<a href="#" class="butt ' + strStyle + '" id="butt-save-as-template-copy">' + jLang.t('save as') + '</a>';
			}
		str += '<a href="#" class="butt ' + strStyle + '" id="butt-cancel-program-modification">' + jLang.t('cancel') + '</a>';
		str += '</div>';
		str += '</div>';
		//
		str += '<div id="popup-content">';
		//
		if(bIsTemplate){
			strWinTitle = jLang.t('template details');
			strProgField = jLang.t('template name:');
			strProgHint = jLang.t('new template name');
		}else{
			strWinTitle = jLang.t('program details');
			strProgField = jLang.t('program name:');
			strProgHint = jLang.t('new program name');
			}
		//
		str += '<h1 class="h1-close-popup">' + strWinTitle + '</h1>';
		//
		str += '<div class="popup-form" style="border-bottom: 1px dotted #ccc;">';
		str += '<p><b>' + strProgField + '</b></p><p><input type="text" id="input-modify-program-name" placeholder="' + strProgHint + '" value="' + this.jutils.javascriptFormat(this.jprogram.name) + '" class="input-1 large uppercase"></p>';
		str += '<p><b>' + jLang.t('notes:') + '</b></p><p><textarea id="input-modify-program-notes" class="textarea-1">' + this.jprogram.notes + '</textarea></p>';
		str += '</div>'; //close popup-form
		//check si on a deja un client selectionne si oui on affiche ses infos
		str += '<div style="margin-top:20px;">';
		str += '<a href="#" class="butt small" id="butt-select-client">';
		//le texte change si a deja un client ou pas
		if(this.jprogram.getClientId() !== -1){	
			str += jLang.t('select a different client');
		}else{
			str += jLang.t('select a client');
			}
		str += '</a>';
		str += '</div>';
		//
		str += '</div>'; //close popup-content
		//popup
		$('#main-popup-window').html(str);
		this.openPopup();
		//focus
		$('#input-modify-program-name').focus();
		//action
		if(this.jprogram.getClientId() !== -1){
			$('#butt-save-as-program-modification').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					//on va chercher les infos si il les a change
					var strName = oTmp.jutils.toUpper($('#input-modify-program-name').val());
					var strNotes = $('#input-modify-program-notes').val();
					oTmp.closePopup();
					oTmp.openSaveAsOtherProgramPopup('from-prog-details', strName, strNotes);
					}
				});	
		}else if(bIsTemplate){
			$('#butt-save-as-template-copy').click(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					oTmp.closePopup();
					oTmp.openSaveAsTemplate();
					}
				});
			}
		//popup client
		$('#butt-select-client').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//avant on check si il a mis un nom de programme dans le input
				oTmp.openPopupClientSearch('from-prog-details');
				}
			});

		//action
		$('#butt-modify-program-modification').data('istemplate', bIsTemplate);
		$('#butt-modify-program-modification').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var bIsTemplate = $(this).data('istemplate');
				//clear erors
				$('.input-1').removeClass('error');
				//change le save state de program
				oTmp.changeProgramSaveState(false);
				//change le nom du programme
				var strName = oTmp.jutils.toUpper($('#input-modify-program-name').val());
				var strNotes = $('#input-modify-program-notes').val();
				//check si le nom est vide
				if(strName == ''){
					$('#input-modify-program-name').addClass('error');
					//
				}else{
					//on met le loader
					oTmp.showLoader(true, '#butt-modify-program-modification', 0, 0);	
					//si on a un client on verifie si le nom n'est pas deja pris
					//si a -1 donc pas de client, donc pas de save et on peut uniquement changer le nom
					if(oTmp.jprogram.getClientId() === -1){
						//on set le jprogram
						//on change directement car sans client on ne sauvegarde rien encore
						var obj = {
							programname : strName,
							programnotes : strNotes,
							}
						oTmp.modifyProgramBasics(false, {}, obj);
						//di vc,est un template on modifie les infos du template aussi
						if(bIsTemplate){
							oTmp.jtemplate.setName(strName);			
							oTmp.jtemplate.setNotes(strNotes);
							}
					}else{
						//maintenant on verifie si le program name exist in local or on the server
						var oClient = oTmp.jprogram.getClient();
						oTmp.checkIfModifiedProgramNameExist(oClient ,strName, strNotes);
						}
					}
				}
			});
		//
		$('#butt-cancel-program-modification').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){	
				//close popup
				oTmp.closePopup();
				}
			});	
			
			
			
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.openEditClientProfileById = function(id){
		this.debug('openEditClientProfileById(' + id + ')');

		//check dans les clients
		var oClient = this.jclientmanager.getClient(id);

		var str = '';
		
		str += '<div class="popup-tools">';
		str += '<div>';
		str += '<a href="#" class="butt med med-3" id="butt-save-popup" client-id="' + oClient.getId() + '">' + jLang.t('save') + '</a>';
		str += '<a href="#" class="butt med med-3" id="butt-cancel-popup">' + jLang.t('cancel') + '</a>';
		str += '<a href="#" class="butt med med-3" id="client-details-add-new-program" client-id="' + oClient.getId() + '" style="">' + jLang.t('new program') + '</a>';
		str += '</div>';
		str += '</div>';
		//
		str += '<div id="popup-content">';
		str += '<h1 class="h1-close-popup" >' + jLang.t('client details') + '</h1>';
		str += '<div class="popup-form">';
		str += '<div class="client-details-box">';
		//infos
		//quand on aura des photos d'usager
		str += '<p><b>' + jLang.t('id:') + ' ' + oClient.getId() + '</b></p>';
		str += '<p><b>' + jLang.t('firstname:') + '</b></p><p><input type="text" id="input-client-prenom" value="' + this.jutils.javascriptFormat(oClient.getFirstName()) + '" class="input-1 large"></p>';
		str += '<p><b>' + jLang.t('lastname:') + '</b></p><p><input type="text" id="input-client-nom" value="' + this.jutils.javascriptFormat(oClient.getLastName()) + '" class="input-1 large"></p>';
		str += '<p><b>' + jLang.t('email:') + '</b></p><p><input type="text" id="input-client-courriel" value="' + this.jutils.javascriptFormat(oClient.getEmail()) + '" class="input-1 large"></p>';
		//avec un select box car les langues dependent de la license
		var arrLang = this.juser.getArrLang();
		str += '<p><b>' + jLang.t('languages:') + '</b></p><p><select id="input-client-locale" class="select-1 search">';
		for(var o in arrLang){
			str += '<option value="' + o + '"';
			if(o == oClient.getLocale()){
				str += ' selected ';
				}
			str += '>' + arrLang[o] + '</option>';
			}
		str += '</select></p>';

		//arr programs
		var arrPrograms = oClient.getArrProgramsName();
		if(arrPrograms.length != 0){
			str += '<div style="clear:both;margin-bottom:30px;">';
			//listing
			str += '<ul style="padding-top:10px;" id="client-details-program-listing" removed-program-ids="">';
			str += '<b>' + jLang.t('programs:') + '</b>';
			
			for(var o in arrPrograms){
				str += '<li id="client-details-line-' + o + '"><a href="#" class="client-details-open-old-program" client-id="' + oClient.getId() + '" program-id="' + o + '">' + arrPrograms[o] + '</a><a href="#" class="client-details-remove-program" client-id="' + oClient.getId() + '" program-id="' + o + '" class="" style="float:right;"><img draggable="false" class="icon-img" src="' + gServerPath + 'images/' + gBrand + '/glyphicons_197_remove.png"></a></li>';
				}
			str += '</ul>';
			str += '</div>';
			}		
		
		str += '</div>'; //close client-details-box
		str += '</div>'; //close popup-form
		str += '</div>'; //close popup-content
		
		//popup
		$('#main-popup-window').html(str);
		this.openPopup();

		//butt actions
		$('#butt-save-popup').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = $(this).attr('client-id');	
				oTmp.saveClientModification(id);
				}
			//
			});
		$('#butt-cancel-popup').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.closePopup();
				}
			});
			
		$('#client-details-add-new-program').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = $(this).attr('client-id');
				oTmp.createNewProgramForClientFromPopup(id);
				}
			});	
			
		$('.client-details-open-old-program').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var cliId = $(this).attr('client-id');
				var progId = $(this).attr('program-id');
				oTmp.openOldProgramForClient(cliId, progId);
				}
			});
		$('.client-details-remove-program').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var cliId = $(this).attr('client-id');
				var progId = $(this).attr('program-id');
				//remove line
				$('#client-details-line-' + progId).remove();
				//add to remove
				var str = $('#client-details-program-listing').attr('removed-program-ids');
				str += progId + ',';
				$('#client-details-program-listing').attr('removed-program-ids', str);
				}
			});	
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	//open new program from client listing
	this.createNewProgramForClientFromClientListing = function(id){
		this.debug('createNewProgramForClientFromClientListing(' + id + ')');
		//come from listing so we hae the value in client manger
		//change le layer de fond
		this.createNewProgramForClient(id, '');
		}
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	//open new program from popup win client details, so it can be in jprogram or jclientmanager
	this.createNewProgramForClientFromPopup = function(id){
		this.debug('createNewProgramForClientFromPopup(' + id + ')');
		//can be from jclientmanager or jprogram
		var oClient = this.jclientmanager.getClient(id);
		//close la fenetre
		this.closePopup();
		//ouvre le popup change le layer de fond
		this.createNewProgramForClient(id, '');
		}
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	//load le programme qui appartient a un client
	this.openOldProgramForClient = function(clientId, progId){
		this.debug('openOldProgramForClient(' + clientId + ', ' + progId + ')');
		//set the client of program
		this.jprogram.setClient(this.jclientmanager.getClient(clientId));
		//change le save state de program
		this.changeProgramSaveState(true); //disable auto save untill we have some changes on it, name exercice, notes, etc...
		//set program
		var oProg = this.jclientmanager.getClientProgramById(clientId, progId);
		this.jprogram.setProgId(progId);
		this.jprogram.setName(oProg.name);
		this.jprogram.setNotes(oProg.notes);
		//clar graphics
		this.resetProgramWindow();
		this.resetSearchWindow();
		//set text
		this.setMainClientName();
		this.setMainProgramName(clientId, progId); //client-id, prog-id
		//on load les exercices du programme client
		//pour respecter l'ordre de tri
		var arrOrder = oProg.order.split(',');
		if(typeof(arrOrder) == 'object'){
			for(var o in arrOrder){
				if(parseInt(arrOrder[o]) > 0){
					//inserre exercice
					this.addBox(arrOrder[o], oProg.exercices[arrOrder[o]]);
					//mais a chaque fois qu'il y a une insertion il y a un changement de state du statut de sauvegarde alors on le reset
					//disable auto save untill we have some real changes on it
					this.changeProgramSaveState(true); 
					}
				}
			}
		//disable auto save untill we have some real changes on it	
		this.changeProgramSaveState(true); 
		//close layers to show the exercice window
		this.closeAllLayers();
		//close popup if some
		this.closePopup();
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	//title client name
	this.setMainClientName = function(){
		this.debug('setMainClientName()');
			
		//icon top user
		$('#top-user-image').attr('src', gServerPath + 'images/' + gBrand + '/menu-user.png');
		//client
		if(typeof(this.jprogram) == 'object'){
			if(typeof(this.jprogram.jclient) == 'object'){
				$('#top-client-name').html(this.jprogram.jclient.getCompleteName());
				return;
				}
			}
		//else
		$('#top-client-name').html(jLang.t('no client selected'));
		}
		
	//----------------------------------------------------------------------------------------------------------------------*
	//title client name
	this.setMainProgramName = function(clientId, progId){
		this.debug('setMainProgramName(' + clientId + ', ' + progId + ')');
		//program
		var strProgName = jLang.t('new program'); //si c'est nouveau ou effacer car L,usager a delete le program
		if(progId != -1 && clientId != -1){
			var obj = this.jclientmanager.getClientProgramById(clientId, progId);
			if(typeof(obj) == 'object'){
				strProgName = obj.name;
				}
		}else if(progId != -1 && clientId == -1){
			strProgName = this.jprogram.name; 
		}else if(progId == -1 && clientId == -1){ //quand on revient d'un  modifyProgramBasics
			strProgName = this.jprogram.name; 
			}
		//set prog name inner program layer
		$('#main-program-name-text').html(strProgName);
		//top of the window
		$('#top-program-name').html(strProgName);
		//change le main settings program title
		$('#main-settings-program-name').html(strProgName);
		}	
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	//reset the program name and exercise
	this.resetProgramWindow = function(){
		this.debug('resetProgramWindow()');	
		//no name
		$('#main-program-name-text').html(jLang.t('new program'));
		$('#top-program-name').html(jLang.t('no program saved'));
		//change le main settings program title
		$('#main-settings-program-name').html(jLang.t('new program'));	
		//on change icon user
		$('#top-user-image').attr('src', gServerPath + 'images/' + gBrand + '/menu-user.png');
		//erase data
		$('#listing-programs').html('');
		$('#listing-programs').scrollTop(0);
		//the array
		this.jprogram.clear();
		//
		this.changeProgramCount();
		}
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.resetSearchWindow = function(){
		this.debug('resetSearchWindow()');
			
		//call le serveur pour les templates
		this.resetSearchTemplatesSelectOptions();
		//va chercher les template du client
		this.jsearch.getSearchTemplates();
		//call le serveur pour les templates
		this.resetSearchModulesSelectOptions();
		//va chercher les modules selon la clinique
		this.jsearch.getSearchModules();	
		//stop le thread
		if(typeof(this.thDisplayHasContentViewLoop) == 'object'){
			this.thDisplayHasContentViewLoop.kill();
			}
		//on clear la class JSearch
		this.jsearch.clear();
		//autocomplete
		this.jautocomplete.resetSearchInputBox();
		//on retire le event on scroll
		$('#layer-search').unbind('scroll');
		//le title
		this.changeSearchExerciceCounter(false);
		//clear
		$('#listing-search').html('');
		//formulaire
		$('#search-exercise-form').addClass('show');
		//les select box
		this.resetSearchFilterSelectBox();
		//scrool top
		$('#layer-search').scrollTop(0);
		//reset les select box
		$('#search-select-template-mine option:first').attr('selected','selected');
		$('#search-select-template-all option:first').attr('selected','selected');
		$('#search-select-template-license option:first').attr('selected','selected');
		$('#search-select-template-brand option:first').attr('selected','selected');	
		
		};

	//----------------------------------------------------------------------------------------------------------------------*
	this.resetSearchFilterSelectBox = function(){
		//formulaire des filtre
		$('#search-select-filter').removeClass('show');
		//le choix de module plus large
		//$('#search-select-module').removeAttr('disabled');
		$('#search-select-module').removeClass('reduce');
		$('#search-select-module').removeClass('filter-on');
		
		};


	//----------------------------------------------------------------------------------------------------------------------*
	this.resetSearchWindowForResult = function(){
		this.debug('resetSearchWindowForResult()');
		
		//stop le thread
		if(typeof(this.thDisplayHasContentViewLoop) == 'object'){
			this.thDisplayHasContentViewLoop.kill();
			}
		//les select box
		this.resetSearchFilterSelectBox();
		//on clear la class JSearch
		this.jsearch.clear();
		//on retire le event on scroll
		$('#layer-search').unbind('scroll');
		//le title
		this.changeSearchExerciceCounter(false);
		//clear
		$('#listing-search').html('');
		//formulaire
		$('#search-exercise-form').removeClass('show');
		//scrool top
		$('#layer-search').scrollTop(0);
		//put the loader
		this.showLoader(true, '#listing-search', 10, 10);
		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.resetSearchTemplatesSelectOptions = function(){
		this.debug('resetSearchTemplatesSelectOptions()');
		
		//minor check
		var el = $('#search-select-template-mine');
		if(el !== false && typeof(el) == 'object'){
			el.html('<option value="-1">' + jLang.t('loading') + '</option>');	
			}
		el = $('#search-select-template-all');
		if(el !== false && typeof(el) == 'object'){
			el.html('<option value="-1">' + jLang.t('loading') + '</option>');	
			}
		el = $('#search-select-template-license');
		if(el !== false && typeof(el) == 'object'){
			el.html('<option value="-1">' + jLang.t('loading') + '</option>');	
			}
		el = $('#search-select-template-brand');
		if(el !== false && typeof(el) == 'object'){
			el.html('<option value="-1">' + jLang.t('loading') + '</option>');	
			}
		}



	//----------------------------------------------------------------------------------------------------------------------*
	this.getSearchTemplatesReturnFromServer = function(obj, extraObj){
		this.debug('getSearchTemplatesReturnFromServer::obj', obj);
		this.debug('getSearchTemplatesReturnFromServer::extraObj', extraObj);
		
		//on y va par la keys
		if(typeof(obj) == 'object'){
			if(typeof(obj.keys) == 'object' && typeof(obj.select) == 'object' && typeof(obj.titles) == 'object'){	
				for(var o in obj.keys){
					var el = $('#search-select-template-' + o);
					var options = '';
					if(el !== false && typeof(el) == 'object'){
						options += '<option value="-1">' + obj.titles[o] + '</option>';
						for(var p in obj.keys[o]){
							options += '<option value="' + obj.keys[o][p] + '">' + obj.select[o][p] + '</option>';
							}
						el.html(options);
						}
					}
				}
			}	
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.resetSearchModulesSelectOptions = function(){
		this.debug('resetSearchModulesSelectOptions()');
		
		//minor check
		var el = $('#search-select-module');
		if(el !== false && typeof(el) == 'object'){
			el.html('<option value="-1">' + jLang.t('loading') + '</option>');	
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.getSearchModulesRFS = function(obj, extraObj){
		this.debug('getSearchModulesRFS::obj', obj);
		this.debug('getSearchModulesRFS::extraObj', extraObj);
		
		//now sorted
		if(typeof(obj) == 'object'){
			//unbind previous state
			$('#search-select-module').unbind();

			var str = '';
			str += '<option value="-1">' + jLang.t('search all modules') + '</option>';
			str += '<optgroup label="" style="margin-top:5px;"></optgroup>';
			str += '<optgroup label="' + jLang.t('search by module') + '" style="margin-top:5px;">';
			for(var o in obj){
				str += '<option value="' + obj[o].id + '"';
				//if(this.lastSelectedSearchModule == obj[o].id){
				if((this.lastSelectedSearchModule !== -1 && obj[o].id == this.lastSelectedSearchModule) || (obj[o].id == this.juser.getModuleId())){
					str += ' selected ';
					}
				str += '>' + obj[o].name + '</option>';
				}
			str += '</optgroup>';
			$('#search-select-module').html(str);
			//le on change pour garder e module selectionne pour la prochaine recherche
			$('#search-select-module').change(function(e){
				e.preventDefault();
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					//on enleve les search filter
					//put size of the module select
					$('#search-select-module').removeClass('reduce');
					$('#search-select-module').removeClass('filter-on');
					//hide filters
					$('#search-select-filter').removeClass('show');
					//on reset le autoomplete deja commence
					//si on veut recommanecer la recherche et tout clearer
					//oTmp.jautocomplete.resetSearchInputBox();
					oTmp.jautocomplete.resetMainAutoComplete();
					//on garde le last one
					oTmp.lastSelectedSearchModule = $(this).val();
					//on lance la recherche avec le meme mot mais pas dans le meme module
					oTmp.jautocomplete.triggerInputEvent('input-main-exercice-search-autocomplete');
					}
				});
			}
		}



	//----------------------------------------------------------------------------------------------------------------------*
	//modify the program name	
	this.modifyProgramBasics = function(bFromService, data, obj){
		this.debug('modifyProgramBasics(' + bFromService + ', ' + data + ', ' + obj + ')');	
		this.debug('modifyProgramBasics::data', data);	
		this.debug('modifyProgramBasics::obj', obj);	
		//si vient du service c'est a dire que le prog a un id et le client aussi
		//l'autre cas est quand on crer un programme sans avoir de sauvegarde encore ni de client associe
		if(bFromService){	
			//on va rajouter au le prog au client
			var oClient = this.jclientmanager.getClient(obj.clientid);
			//minor check
			if(typeof(oClient) == 'object'){
				//change le nom du prog
				this.jprogram.setProgId(data.programid); //from data because if no id yet we will receive one
				this.jprogram.setName(obj.programname);
				this.jprogram.setNotes(obj.programnotes);
				//rajoute au client
				// on avait un id -1 de prog et l'on en recoit un alors il faut le rajouter
				if(obj.programid == -1 && obj.programidfrom == -1){
					this.debug('NEW OLD (' + obj.programid + ') WITH NEW(' + obj.programidfrom + ')');
					oClient.addNewProgram(data.programid, obj.programname);
					//il faut aussi rajouter les exercices au client
					//DWIZZEL:: IMPORTANT...
					var arrExercices = this.jprogram.getExercices();
					if(typeof(arrExercices) == 'object'){
						for(var o in arrExercices){
							//rajoute exercice
							oClient.addExerciceToProgram(data.programid, arrExercices[o].getId(), arrExercices[o].getObj());
							}
						//le order by
						oClient.changeExerciceOrder(data.programid, this.jprogram.getExerciceByOrder());	
						}
					}
				//on prog avec id a -1 et on va ecraser un qui a un id
				if(obj.programid != -1 && obj.programidfrom == -1){
					this.debug('OVERWRITE OLD (' + obj.programid + ') WITH NEW(' + obj.programidfrom + ')');
					//on enleve les exercices precedents du program du client
					oClient.rmAllExerciceFromProgram(obj.programid);
					//on ajoute les exercice du programme dans le client
					var arrExercices = this.jprogram.getExercices();
					if(typeof(arrExercices) == 'object'){
						for(var o in arrExercices){
							//rajoute exercice
							oClient.addExerciceToProgram(obj.programid, arrExercices[o].getId(), arrExercices[o].getObj());
							}
						//le order by
						oClient.changeExerciceOrder(obj.programid, this.jprogram.getExerciceByOrder());	
						}

				}else if(obj.programid != obj.programidfrom){
					this.debug('REPLACE OLD (' + obj.programid + ') WITH NEW(' + obj.programidfrom + ')');
					//si on replace un ancien program par le nouveau
					oClient.replaceProgramByOtherProgram(obj.programidfrom, obj.programid);
					}
				//juste modif du client
				oClient.modifyClientProgramBasics(data.programid, obj.programname, obj.programnotes);
				//routine
				this.setMainProgramName(obj.clientid, data.programid); //client-id, prog-id
				//close popup
				this.closePopup();
				//change le save state de program
				this.changeProgramSaveState(false); //because name can be applied after the exercice has been added
				//refresh les infos dans le listing des clients si il y a
				this.modifyClientSearchInnerLi(oClient);
				}
		}else{
			//change le nom du prog
			this.jprogram.setName(obj.programname);
			this.jprogram.setNotes(obj.programnotes);
			//routine
			this.setMainProgramName(-1, -1); //client-id, prog-id
			//close popup
			this.closePopup();
			}
		}	

	//----------------------------------------------------------------------------------------------------------------------*
	this.createNewProgramForClient = function(clientId, strProgramName){
		this.debug('createNewProgramForClient(' + clientId + ', ' + strProgramName + ')');
		//this is called by popup user details, add new client or by client listing toggle when clicked on "add new program"
		//1. on pop une fenetre qui demande le nom du nouveau programme	
		//2. on verifie si le nom est deja pris ou pas
		//	a. si prise on demande si overwrite
		//	b. sinon on continue avec le buildNewProgramForClient
		//	c. peu canceller en tout temps

		//get les infos du client
		var oClient = this.jclientmanager.getClient(clientId);
		//
		var str = '';
		//bottom buttons
		str += '<div class="popup-tools">';
		str += '<div>';
		str += '<a href="#" class="butt med" id="butt-save-popup">' + jLang.t('save') + '</a>';
		str += '<a href="#" class="butt med" id="butt-cancel-popup">' + jLang.t('cancel') + '</a>';
		str += '</div>';
		str += '</div>';
		//
		str += '<div id="popup-content">';
		str += '<h1 class="h1-close-popup">' + jLang.t('new program name') + '</h1>';
		//
		var strTmp = jLang.t('the new program name for <b>%name%</b> have to be different from the ones already existing.');
		//
		str += '<div class="div-sty-24">' + strTmp.replace('%name%', oClient.getCompleteName()) + '</div>';
		//le input box
		str += '<div class="div-sty-24"><input type="text" id="input-alert-new-program-name" placeholder="' + jLang.t('new program name') + '" value="' + this.jutils.javascriptFormat(strProgramName) + '" class="input-1 large uppercase" client-id="' + clientId + '"></div>';
		str += '</div>'; //close popup-content
		//write content
		$('#main-popup-window').html(str);
		//open popup
		this.openPopup();
		//set focus on the input
		$('#input-alert-new-program-name').focus();	
		//cancel action
		$('#butt-cancel-popup').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.closePopup();
				}
			});

		$('#butt-save-popup').data('jclient', oClient);
		$('#butt-save-popup').click(function(e){
			e.preventDefault(); 
			$('#input-alert-new-program-name').removeClass('error');
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var oClient = $(this).data('jclient');
				var strName = oTmp.jutils.toUpper($('#input-alert-new-program-name').val());
				if(strName == ''){
					$('#input-alert-new-program-name').addClass('error');
				}else{
					oTmp.showLoader(true, '#butt-save-popup', 0, 0);	
					oTmp.checkIfNewProgramNameExist(oClient, strName);
					}
				}
			});

	
		}	

	//----------------------------------------------------------------------------------------------------------------------*
	this.checkIfNewProgramNameExist = function(oClient, strName){
		this.debug('checkIfNewProgramNameExist(' + oClient + ', ' + strName + ')');	
		//check si existe deja
		if(oClient.isProgramNameAlreadyExistInLocalData(strName)){ 
			//existe deja alors on affiche un message comme quoi il peut overwrite ou choisir un autre nom
			this.popupAlertReplaceProgramName(strName, oClient, {type:'new'});
		}else{
			//DWIZZEL::INPORTANT
			//Nexiste pas en local on va verifier sur le serveur
			this.jprogram.isNewProgramNameAlreadyExistInServerData(strName, oClient);
			//on envoi info au serveur
			//va se faire maintenant sur le call back de isNewProgramNameAlreadyExistInServerData dans jprogram
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.checkIfModifiedProgramNameExist = function(oClient, strName, strNotes){
		this.debug('checkIfModifiedProgramNameExist(' + oClient + ', ' + strName + ', ' + strNotes + ')');
		//check si existe deja
		if(oClient.isProgramNameAlreadyExistInLocalData(strName) && this.jprogram.getName() != strName){ //different que celui qui est ouvert
			this.popupAlertReplaceProgramName(strName, oClient, {type:'modify', notes:strNotes});
		}else{
			this.jprogram.isModifiedProgramNameExistInServerData(oClient, strName, strNotes);
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.checkIfInsertedClientProgramNameExist = function(oClient, strProgramName, strFrom){
		this.debug('checkIfInsertedClientProgramNameExist(' + oClient + ', ' + strProgramName + ', ' + strFrom + ')');
		//check si local avant serveur
		if(oClient.isProgramNameAlreadyExistInLocalData(strProgramName)){ //different que celui qui est ouvert
			this.popupAlertReplaceProgramName(strProgramName, oClient, {type:'client'});
		}else{
			this.jprogram.isClientProgramNameExist(oClient, strProgramName, strFrom);
			}
		
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.popupAlertReplaceProgramName = function(strName, oClient, objExtData){
		this.debug('popupAlertReplaceProgramName(' + strName + ', ' + oClient + ', ' + objExtData + ')');	
		//console.log(objExtData);

		//on arrete la suvegarde automatique
		this.jprogram.setSaved(true);

		//str de base
		var str = jLang.t('the program <b>%programname%</b> for <b>%name%</b> already exist, do you want to replace it by this one?');
		str = str.replace('%programname%', strName);
		str = str.replace('%name%', oClient.getCompleteName());
		//open the comfirm box avec en retour le nom du save-butt
		var arrButts = this.openAlert('comfirm', jLang.t('duplicate name'), str, false);
		//datat passed
		var strButtYes = '#' + arrButts[0];
		var strButtNo = '#' + arrButts[1];
		//set action on butt save once it's writed 0 = yes, 1 = no
		if(objExtData.type == 'new'){
			//si vient de creer un nouveau programme
			//YES
			$(strButtYes).data('jclient', oClient);
			$(strButtYes).data('programname', strName);
			$(strButtYes).click(function(e){
				e.preventDefault(); 
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					var strProgramName = $(this).data('programname');
					var oClient = $(this).data('jclient');
					//routine selon dou il vient
					oTmp.jprogram.createNewProgramToDb(oClient, strProgramName, false);
					oTmp.closeAlert();
					}
				});

	
		}else if(objExtData.type == 'modify'){
			$(strButtYes).data('programname', strName);
			$(strButtYes).data('programnotes', objExtData.notes);	
			//si renomme un programme et que le nom existe deja
			$(strButtYes).click(function(e){
				e.preventDefault(); 
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					var strProgramName = $(this).data('programname');
					var strProgramNotes = $(this).data('programnotes');
					//routine selon dou il vient
					oTmp.jprogram.modifyProgramBasicsToDb(strProgramName, strProgramNotes, {overwrite:true});
					oTmp.closeAlert();
					}
				});
			

		}else if(objExtData.type == 'client'){
			$(strButtYes).data('jclient', oClient);
			$(strButtYes).data('programname', strName);
			//si change le client du programme et que celui-ci en a deja un avec ce nom
			$(strButtYes).click(function(e){
				e.preventDefault(); 
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					var strProgramName = $(this).data('programname');
					var oClient = $(this).data('jclient');
					//on rajoute le client au programme
					oTmp.jprogram.setClient(oClient);
					oTmp.setMainClientName();
					//routine selon dou il vient
					//on passe le parametre de progId qu'il va remplacer, car le progsId est automatiquement à 0
					oTmp.jprogram.modifyProgramBasicsToDb(strProgramName, '', {overwrite:true, changeclient:true});
					oTmp.closeAlert();
					}
				});

			
			}
		
		//butt NO
		//unbind car on overwrite ce qui est define dans openAlert();
		$(strButtNo).unbind();
		//data
		$(strButtNo).data('jclient', oClient);
		$(strButtNo).data('type', objExtData.type);	
		$(strButtNo).click(function(e){
			e.preventDefault(); 
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var type = $(this).data('type');
				var oClient = $(this).data('jclient');	
			
				oTmp.debug(type);
				if(type == 'modify'){
					oTmp.removeLoader('#butt-modify-program-modification', jLang.t('modify'));
				}else if(type == 'client'){
					//peut venir d'un <li> 
					oTmp.removeLoader('.select-client-profile[client-id=' + oClient.getId() + ']', oClient.getCompleteName());
					//ou d'un "add new client", pas moyen de les differencier presentement
					oTmp.removeLoader('#butt-save-popup', jLang.t('save'));
				}else{
					oTmp.removeLoader('#butt-save-popup', jLang.t('save'));
					}
				oTmp.closeAlert();
				}
			});	

		
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.popupAlertReplaceTemplateName = function(strName, strModule, objExtData){
		this.debug('popupAlertReplaceTemplateName(' + strName + ', ' + strModule + ', ' + objExtData + ')');
		this.debug('popupAlertReplaceTemplateName::objExtData', objExtData);
		//str de base
		var str = jLang.t('the template <b>%templatename%</b> already exist, do you want to replace it by this one?');
		str = str.replace('%templatename%', strName);
		//open the comfirm box avec en retour le nom du save-butt
		var arrButts = this.openAlert('comfirm', jLang.t('duplicate template name'), str, false);
		//set action on butt save once it's writed 0 = yes, 1 = no
		var strButtYes = '#' + arrButts[0];
		var strButtNo = '#' + arrButts[1];

		
		if(objExtData.type == 'new'){
			//si vient de creer un nouveau template
			//YES
			$(strButtYes).data('templatename', strName);
			$(strButtYes).data('templatemodule', strModule);
			$(strButtYes).click(function(e){
				e.preventDefault(); 
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					var strName = $(this).data('templatename');
					var strModule = $(this).data('templatemodule');
					//on the server side if id != -1 and keeporiginal=true then copy and keep the original
					//build le obj to send
					var obj = {
						id: oTmp.jtemplate.getId(), //if a new one it will be -1, if an open template we will have an id
						name: strName,
						notes: oTmp.jtemplate.getNotes(),
						module: strModule,
						order: oTmp.jprogram.getExerciceByOrder(),
						exercices: oTmp.jprogram.getExerciceByArrayForTransport(),
						overwritename: true,
						keeporiginal: true,
						};
					//send
					oTmp.jtemplate.saveTemplateModifications(obj);
					//close
					oTmp.closeAlert();
					}
				});

		}else if(objExtData.type == 'modify'){
			//si renomme un programme et que le nom existe deja
			$(strButtYes).click(function(e){
				e.preventDefault(); 
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					//
					//build le obj to send
					var obj = {
						id: oTmp.jtemplate.getId(),
						name: oTmp.jtemplate.getName(),
						notes: oTmp.jtemplate.getNotes(),
						module: oTmp.jtemplate.getModule(),
						order: oTmp.jprogram.getExerciceByOrder(),
						exercices: oTmp.jprogram.getExerciceByArrayForTransport(),
						overwritename: true,
						keeporiginal: false,
						};
					//send
					oTmp.jtemplate.saveTemplateModifications(obj);
					//close
					oTmp.closeAlert();
					}
				});
		
		}else{
			//

			}

		//le butt NO
		$(strButtNo).unbind();
		$(strButtNo).data('type', objExtData.type);	
		$(strButtNo).click(function(e){
			e.preventDefault(); 
			//get parent class
			var oTmp = $(document).data('jappzclass');
			var type = $(this).data('type');
			if(typeof(oTmp) == 'object'){
				if(type == 'new'){
					oTmp.removeLoader('#butt-save-popup', jLang.t('save'));
				}else{
					oTmp.removeLoader('#butt-save-popup', jLang.t('save template'));
					}
				oTmp.closeAlert();
				}
			});

			
		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.addNewProgramToClient = function(progId, obj){
		this.debug('addNewProgramToClient(' + progId + ', ' + obj + ')');
		this.debug('addNewProgramToClient::obj', obj);
		//console.log(obj);

		/*
	
		-----
		Si obj contient keepprogramexercise:true
		c'est que l,on doit creer le nouveau programme et ensuite prendre les exercices 
		qui sont dans celui-ci et les rajouter au programme duc client
		
		-----
		Dans les autre ca c'est que c,est un nouveau programme qui est 
		construit avant d'avoir pu rajouter des exercices dedans

		*/

		var bKeepProgramExercise = false;
		if(typeof(obj.extraparams) == 'object'){
			if(typeof(obj.extraparams.keepprogramexercise) != 'undefined'){	
				if(obj.extraparams.keepprogramexercise === true){
					bKeepProgramExercise = true;
					}
				}
			}
		
		//on va rajouter au le prog au client
		var oClient = obj.oclient;
		//minor check
		if(typeof(oClient) == 'object'){
			//c'est un nouveau programme mais on avait deja choisit des exercices	
			if(bKeepProgramExercise){ 
				//set le client du programme
				this.jprogram.setClient(this.jclientmanager.getClient(oClient.getId()));
				//set le id du programme
				this.jprogram.setName(obj.programname);	
				this.jprogram.setProgId(progId);
				//rajoute au client
				oClient.addNewProgram(progId, obj.programname);
				//on rajoute les rexercice au client
				//on ajoute les exercice du programme dans le client
				var arrExercices = this.jprogram.getExercices();
				if(typeof(arrExercices) == 'object'){
					for(var o in arrExercices){
						//rajoute exercice
						oClient.addExerciceToProgram(progId, arrExercices[o].getId(), arrExercices[o].getObj());
						}
					//le order by
					oClient.changeExerciceOrder(progId, this.jprogram.getExerciceByOrder());	
					}
				//routine
				this.setMainClientName();
				this.setMainProgramName(oClient.getId(), progId); //client-id, prog-id
				//close popup
				this.closePopup();
				//change le save state de program
				this.changeProgramSaveState(false); //because no exercice have been entered so set to true
				//check si est dans le listing client pour rajouter le programme dans la liste
				//refresh les infos dans le listing des clients si il y a
				//le li inner
				this.modifyClientSearchInnerLi(oClient);	


			}else{
				//set le client du programme
				this.jprogram.setClient(this.jclientmanager.getClient(oClient.getId()));
				//clear prog
				this.jprogram.clear();
				this.jprogram.clearProgram();
				//change le nom du prog
				this.jprogram.setName(obj.programname);
				this.jprogram.setProgId(progId);
				//rajoute au client
				oClient.addNewProgram(progId, obj.programname);
				//routine
				this.resetProgramWindow();
				this.setMainClientName();
				this.setMainProgramName(oClient.getId(), progId); //client-id, prog-id
				this.resetSearchWindow();
				this.openLayer('search');
				//close popup
				this.closeAlert();
				//change le save state de program
				this.changeProgramSaveState(true); //because no exercice have been entered so set to true
				//check si est dans le listing client pour rajouter le programme dans la liste
				//refresh les infos dans le listing des clients si il y a
				//le li inner
				this.modifyClientSearchInnerLi(oClient);
				}
			}
		
		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.openClientProfileById = function(id){
		$('#client-profile-' + id).slideToggle(
			{
			duration:this.basicAnimSpeed/2, 
			start:function(){
				//make the arrow icon rotate
				if($('.open-client-profile[client-id="' + id + '"]  > div > a > img').hasClass('rotate')){
					$('.open-client-profile[client-id="' + id + '"]  > div > a > img').removeClass('rotate');
				}else{
					$('.open-client-profile[client-id="' + id + '"]  > div > a > img').addClass('rotate');
					}
				},
			});
		}
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.setBoxSelection = function(id){
		//when we tap on the program bozes for selections
		var selected = $('#' + id).attr('imgselected');
		if(selected == '0'){
			$('#' + id).attr('imgselected','1');
			$('#' + id).addClass('imgselected-red');
		}else{
			$('#' + id).attr('imgselected','0');
			$('#' + id).removeClass('imgselected-red');
			}
		}

		//----------------------------------------------------------------------------------------------------------------------*
	this.setSearchBoxSelection = function(id){
		//when we tap on the serach boxes for selections
		var selected = $('#search-img-' + id).attr('imgselected');
		if(selected == '0'){
			$('#search-img-' + id).attr('imgselected','1');
			$('#search-img-' + id).addClass('imgselected');
			//on le rajoute au programs
			this.addItemsToPrograms(id);
		}else{
			$('#search-img-' + id).attr('imgselected','0');
			$('#search-img-' + id).removeClass('imgselected inprogramlist');
			//on enleve au programs
			this.rmItemsFromPrograms(id, false);
			}

		}
		
			
	//----------------------------------------------------------------------------------------------------------------------*
	this.openProgramCaroussel = function(id){
		this.debug('openProgramCaroussel(' + id + ')');
		//on clean le caroussel
		$('#main-caroussel').remove();
		//fill up the layer with the programs we have stacked in the array get the pos of the id
		var index = this.fillProgramCaroussel(id);
		//le event quand le panel change
		this.jslider.setCallBackObjOnPaneSelected(this, {section:'program'});		
		//show the popup slider
		this.openPopupCaroussel();
		//ini the slides hammer action
		this.jslider.init('main-caroussel', index);
		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.openSearchCaroussel = function(id){
		this.debug('openSearchCaroussel(' + id + ')');
		//on clean le caroussel
		$('#main-caroussel').remove();
		//fill up the layer with the programs we have stacked in the array get the pos of the id
		var index = this.fillSearchCaroussel(id);
		//le event quand le panel change
		this.jslider.setCallBackObjOnPaneSelected(this, {section:'search'});		
		//show the popup slider
		this.openPopupCaroussel();
		//ini the slides hammer action
		this.jslider.init('main-caroussel', index);
		}	
		
	//----------------------------------------------------------------------------------------------------------------------
	this.getExerciceIdByIndexFromCaroussel = function(index){
		this.debug('getExerciceIdByIndexFromCaroussel(' + index + ')');
		var id = $('.caroussel-item[index-id="' + index + '"]').attr('exercice-id');
		return parseInt(id);
		}
		
		
	//----------------------------------------------------------------------------------------------------------------------
	/*
	this.callBackFromSlider = function(index, lastIndex, extraObj){
		this.debug('callBackFromSlider(' + index + ', ' + lastIndex + ', ' + extraObj + ')');
		//
		if(typeof(extraObj) == 'object'){
			//get the id
			var id = this.getExerciceIdByIndexFromCaroussel(index);
			var lastId = this.getExerciceIdByIndexFromCaroussel(lastIndex);

			if(extraObj.section == 'search'){ //si vient de serach
				//come from the search panel carrousel
				//remove le focus du input precednt si il y a pour trigger le event du change sur les input
				this.forceFocusLostOnInputFromSearchCarousselWhenSlidingAway(id, lastId);
				//on va loader le content quand on arrive sur le pane
				//si quand ouvre
				if(lastIndex > index){ //alors swipe vers la droit
					//on load celui quivient avant si il y a 
					var prevId = this.getExerciceIdByIndexFromCaroussel(index - 1);
					if(prevId != false && prevId != 0){
						this.setSearchCarousselContentById(prevId, index, lastIndex);
						}
				}else if(lastIndex < index){ //alors swipe vers la gauche
					//on load celui quivient apres si il y a 
					var nextId = this.getExerciceIdByIndexFromCaroussel(index + 1);
					if(nextId != false && nextId != 0){
						this.setSearchCarousselContentById(nextId, index, lastIndex);
						}
				}else{ //soit le premier ou dernier
					this.setSearchCarousselContentById(id, index, lastIndex);
					}
								
				//check si est dans program ou pas
				if(this.jprogram.contains(id)){
					//remove butt
					this.addEventToButtonFromSearchCaroussel(id, false);
				}else{
					//add butt
					this.addEventToButtonFromSearchCaroussel(id, true);
					}
					
			}else if(extraObj.section == 'program'){ //si vient de program
				//come from the program panel carrousel
				//remove le focus du input precednt si il y a pour trigger le event du change sur les input
				this.forceFocusLostOnInputFromProgramCarousselWhenSlidingAway(id, lastId);
				//
				this.addEventToButtonFromProgramCaroussel(id);
				//DWIZZEL:: quand change le text va changer la couleur du bouton save pour avertir l'usager 
				//le event sur le input text change pour chacune des langues
				this.addEventToInputChangeFromProgramCaroussel(id, false);
				}
			}
		}
	*/
	this.callBackFromSlider = function(index, lastIndex, extraObj){
		this.debug('callBackFromSlider(' + index + ', ' + lastIndex + ', ' + extraObj + ')');
		//
		if(typeof(extraObj) == 'object'){
			//get the id
			var id = this.getExerciceIdByIndexFromCaroussel(index);
			var lastId = this.getExerciceIdByIndexFromCaroussel(lastIndex);

			if(extraObj.section == 'search'){ //si vient de serach
				//come from the search panel carrousel
				//remove le focus du input precednt si il y a pour trigger le event du change sur les input
				this.forceFocusLostOnInputFromSearchCarousselWhenSlidingAway(id, lastId);
				//on va loader le content quand on arrive sur le pane
				//si quand ouvre
				if(lastIndex > index){ //alors swipe vers la droit
					//on load celui quivient avant si il y a 
					var prevId = this.getExerciceIdByIndexFromCaroussel(index - 1);
					if(prevId != false && prevId != 0){
						this.setSearchCarousselContentById(prevId, index, lastIndex);
						}
				}else if(lastIndex < index){ //alors swipe vers la gauche
					//on load celui quivient apres si il y a 
					var nextId = this.getExerciceIdByIndexFromCaroussel(index + 1);
					if(nextId != false && nextId != 0){
						this.setSearchCarousselContentById(nextId, index, lastIndex);
						}
				}else{ //soit le premier ou dernier
					this.setSearchCarousselContentById(id, index, lastIndex);
					}
								
				//check si est dans program ou pas
				if(this.jprogram.contains(id)){
					//remove butt
					this.addEventToButtonFromSearchCaroussel(id, false);
				}else{
					//add butt
					this.addEventToButtonFromSearchCaroussel(id, true);
					}
					
			}else if(extraObj.section == 'program'){ //si vient de program
				//come from the program panel carrousel
				//remove le focus du input precednt si il y a pour trigger le event du change sur les input
				this.forceFocusLostOnInputFromProgramCarousselWhenSlidingAway(id, lastId);
				//
				//on va loader le content quand on arrive sur le pane
				//si quand ouvre
				if(lastIndex > index){ //alors swipe vers la droit
					//on load celui quivient avant si il y a 
					var prevId = this.getExerciceIdByIndexFromCaroussel(index - 1);
					if(prevId != false && prevId != 0){
						this.setProgramCarousselContentById(prevId, index, lastIndex);
						}
				}else if(lastIndex < index){ //alors swipe vers la gauche
					//on load celui quivient apres si il y a 
					var nextId = this.getExerciceIdByIndexFromCaroussel(index + 1);
					if(nextId != false && nextId != 0){
						this.setProgramCarousselContentById(nextId, index, lastIndex);
						}
				}else{ //soit le premier ou dernier
					this.setProgramCarousselContentById(id, index, lastIndex);
					}	
				//
				this.addEventToButtonFromProgramCaroussel(id);
				

				//DWIZZEL:: quand change le text va changer la couleur du bouton save pour avertir l'usager 
				//le event sur le input text change pour chacune des langues
				//this.addEventToInputChangeFromProgramCaroussel(id, false);
				}
			}
		}		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.fillProgramCaroussel = function(id){
		this.debug('fillProgramCaroussel(' + id + ')');
		//
		var cmpt = 0;
		var rCmpt = 0;
		var str = '';
		//	
		str += '<div class="popup-tools">';
		str += '<div>';
		str += '<a href="#" class="butt med med-3" id="butt-save-exercice-modification-from-program" exercice-id="-1">' + jLang.t('no change') + '</a>';
		str += '<a href="#" class="butt med med-3" id="butt-caroussel-close">' + jLang.t('close') + '</a>';
		str += '<a href="#" class="butt med med-3 red" id="butt-delete-exercice-from-program" exercice-id="-1" style="">' + jLang.t('remove') + '</a>';
		str += '</div>';
		str += '</div>';
		//main caroussel container
		str += '<div id="main-caroussel"></div>';
		//on set les boutons et le container 
		$('#main-popup-caroussel').html(str);

		//WILL TRY with a key index numeric of a pointer
		var arrShowedIndex = [];
		var arrExerciceByKeys = this.jprogram.getExerciceByOrder();
		str = '';
		//loop build display
		for(var o in arrExerciceByKeys){
			var oExercice = this.jprogram.getExerciceById(arrExerciceByKeys[o]);
			if(oExercice !== false){
				if(oExercice.getId() == id){
					rCmpt = cmpt; //the pos of the id, the one we will see 
					//si loader ou content
					//ceux a qui on set des events listeners ou onclick
					arrShowedIndex.push(cmpt);
					str += '<div class="caroussel-item" id="items-' + oExercice.getId() + '" exercice-id="' + oExercice.getId() + '" index-id="' + cmpt + '" isloaded="1" haschanged="0" mirror-on="' + oExercice.getMirror() + '" flip-on="' + oExercice.getFlip() + '" >';	
					str += this.setProgramCarousselContent(oExercice);
					str += '</div>';
				}else{
					str += '<div class="caroussel-item" id="items-' + oExercice.getId() + '" exercice-id="' + oExercice.getId() + '" index-id="' + cmpt + '" isloaded="0" haschanged="0" mirror-on="' + oExercice.getMirror() + '" flip-on="' + oExercice.getFlip() + '" ></div>';
					}
				cmpt++;
				}
			}	

		$('#main-popup-caroussel > #main-caroussel').append(str);
		
		//le link pour le changement de langue du title et de la description, car il faut que le contenu soit ecrit avant de mettre des events dessus
		//juste celu sur lequel on tombe ca les autres seront loade dynamiquement par la suite sur le change pane du JSlider
		//on check pour le precedent et suivant
		if(typeof(arrExerciceByKeys[rCmpt-1]) != 'undefined'){
			//alors on a un precedent
			arrShowedIndex.push(rCmpt-1);
			//set content
			this.setProgramCarousselContentById(arrExerciceByKeys[rCmpt-1], rCmpt, (rCmpt-1));
			}
		if(typeof(arrExerciceByKeys[rCmpt+1]) != 'undefined'){
			//alors on a un suivant
			arrShowedIndex.push(rCmpt+1);
			//set content
			this.setProgramCarousselContentById(arrExerciceByKeys[rCmpt+1], rCmpt, (rCmpt+1));
			}

		//les loader a 1 car les event et le contenu es deja la
		for(var o in arrShowedIndex){
			//on met le contenu a loader pour ne pas qu'il le cherche encore ou put 2 fois des events sur les boutons
			$('.caroussel-item[index-id="' + arrShowedIndex[o] + '"]').attr('isloaded', 1);
			}		

			
		//les actions sur les boutons de langue
		$('.caroussel-item[index-id="' + rCmpt + '"] .navbar A').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//change the title and description
				oTmp.changeTitleAndDescByLangForExerciceInCaroussel($(this));
				}
			});

		//le event for open video player
		$('.caroussel-item[index-id="' + rCmpt + '"] DIV.svideo').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = $(this).attr('exercice-id');
				oTmp.openExerciceVideo(id, 'program');
				}
			});
		
		//event for mirroring images if there are 2 images
		$('.caroussel-item[index-id="' + rCmpt + '"] DIV.smirror').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = $(this).attr('exercice-id');
				oTmp.mirrorExerciceImage(id, 'programs');
				}
			});	

		//event for switch order
		$('.caroussel-item[index-id="' + rCmpt + '"] DIV.sflip').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = $(this).attr('exercice-id');
				oTmp.flipExerciceImage(id, 'programs');
				}
			});	
		
		//le bouton modifier les pârametre endessous de la description
		//ils font tous la memechose alors y aller avec la classe
		$('.caroussel-item[index-id="' + rCmpt + '"] .butt-change-settings').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = parseInt($(this).attr('exercice-id'));
				oTmp.openSettingFromProgramCaroussel(id);
				}
			});

		//le vent sur le change des input box de description
		this.addEventToInputChangeFromProgramCaroussel(rCmpt, true);
		
		//fix
		this.fixImage();
		//return the position	
		return rCmpt;	
		}




	//----------------------------------------------------------------------------------------------------------------------*
	//ouvre le panneau des settings a parir du caroussel du program
	this.openSettingFromProgramCaroussel = function(id){
		this.debug('openSettingFromProgramCaroussel(' + id + ')');
		//on va chercher les settings de celui-ci selon le id avec un arr the key->val ex: "tempo": 15 minutes
		var oExercice = this.jprogram.getExerciceById(id);
		if(typeof(oExercice) == 'object'){	
			var arrSettings = [];
			var oSettings = oExercice.getSettings();
			for(var o in oSettings){
				if(oSettings[o] != ''){
					arrSettings[o] = oSettings[o];
					}
				}
			//on appliaque au panel de setting
			this.jsettingmanager.saveModeTextSettings(arrSettings);
			}
		//on deselectionne tout les box-img
		for(var o in this.jprogram.arrExercices){
			this.checkProgramsBox(false, this.jprogram.arrExercices[o].id);
			}
		//seclect juste celui avec le id
		this.setBoxSelection('img-' + id);
		//open the setting panel if not already opened cause can press on box-img when opened or not
		/*
		if($('#layer-settings').attr('showed') == '0'){
			this.showToolsLayerSettings();
			}
		*/
		//ferme le popup
		this.closePopupCaroussel();
		//show les settings
		//open the setting panel if not already opened cause can press on box-img when opened or not
		if($('#layer-settings').attr('showed') == '0'){
			this.showToolsLayerSettings();
			}


		}

		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.setProgramCarousselContent = function(oExercice){
		this.debug('setProgramCarousselContent(' + oExercice + ')');
		//
		var str = '';
		//thumbs 1 or 2 depending on arr returned
		var arrThumbs = oExercice.getPicturesArray();
		//mirror img
		var strExtraClass = '';
		if(oExercice.getMirror() === 1){
			strExtraClass += ' mirror ';
			}

		str += '<div class="caroussel-div-container">';
		//open le content des details de exercice
		str += '<div class="caroussel-content">';

		//icons and title
		str += '<div class="top-title-caroussel">';
		str += '<div class="title">' + oExercice.getCode() + '</div>';	
		str += '<div class="butts">';
		if(oExercice.getVideoType() == 'sprout'){
			str += '<div class="square svideo" exercice-id="' + oExercice.getId() + '"><img src="' + gServerPath + 'images/' + gBrand + '/icon-video.png"></div>';
			}
		str += '<div class="square smirror" exercice-id="' + oExercice.getId() + '"><img src="' + gServerPath + 'images/' + gBrand + '/icon-mirror.png"></div>';
		if(arrThumbs.length == 2){ //si a deux images
			str += '<div class="square sflip" exercice-id="' + oExercice.getId() + '"><img src="' + gServerPath + 'images/' + gBrand + '/icon-switch.png"></div>';
			}
		str += '</div>'; //close butts	
		str += '</div>'; //close top-title-caroussel
		//
		str += '<div class="div-sty-24">';
		if(arrThumbs.length == 2){
			if(oExercice.getFlip() === 1){
				str += '<div class="div-sty-26 img-a left ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[1] + '"></div>';
				str += '<div class="div-sty-26 img-b right ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[0] + '"></div>';
			}else{
				str += '<div class="div-sty-26 img-a right ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[1] + '"></div>';
				str += '<div class="div-sty-26 img-b left ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[0] + '"></div>';
				}
		}else{
			str += '<div class="div-sty-26 img-a alone ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[0] + '"></div>';
			}
		str += '</div>';
			
		//get settings
		//show all settings
		var arrSettings = oExercice.getSettings();
		var iRows = 0;
		var strTable = '<div class="table settings">';
		for(var o in arrSettings){
			if(arrSettings[o] != ''){
				iRows++;
				strTable += '<div class="row">';
				strTable += '<div class="cell title">' + jLang.t(o + ':') + '</div>';
				strTable += '<div class="cell">' + arrSettings[o] + '</div>';
				strTable += '</div>'; //close row
				}
			}
		strTable += '</div>'; //close table
		//div des settings
		if(iRows){
			str += '<div class="div-sty-28 settings">';
			str += strTable;
		}else{
			str += '<div class="div-sty-28 nosettings">';
			}
		//title
		//le bouton pour ouvrir les setting panel
		str += '<div class="div-sty-24 settings">';		
		str += '<a href="#" class="butt small butt-change-settings" exercice-id="' + oExercice.getId() + '" >' + jLang.t('change settings') + '</a>';			
		str += '</div>';
		str += '</div>'; //close div des settings 	
		
		//le conteneur de tout ce qui a rapport au langues description, select box et check box incl
		str += '<div class="div-sty-29">';
		//div des langues
		str += '<div class="div-sty-28 title-and-description">';
		//load title and description depending if we show original, myinstructions or program instruction
		str += this.loadTitleAndDescription(oExercice, 'inst_3');
		//close div des langues
		str += '</div>';
		//instructions
		str += '<div class="div-sty-25">';
		str += '<select class="select-1 caroussel" id="input-exercice-type" exercice-id="' + oExercice.getId() + '">';
		//check si a du userdata = myinstructions
		if(oExercice.haveUserData()){
			str += '<option value="inst_2">' + jLang.t('my instructions') + '</option>';
			}
		//par defaut car on est dans la window de programs alors il est selected
		str += '<option value="inst_3" selected>' + jLang.t('programs instructions') + '</option>'; 
		str += '<option value="inst_1">' + jLang.t('physiotec instructions') + '</option>';
		str += '</select>';
		str += '</div>';
		//close le conteneur de tout ce qui a rapport au langues
		str += '</div>';

		//close caroussel-content
		str += '</div>';
		//close caroussel-div-container
		str += '</div>';
		

		//
		return str;	

		}

	//----------------------------------------------------------------------------------------------------------------------*
	//pour le loadng de contenu dynamiquement une fois sur le pane triggerd by the JSlider callBackObject method
	this.setProgramCarousselContentById = function(id, index, lastIndex){
		this.debug('setProgramCarousselContentById(' + id + ', ' + index + ', ' + lastIndex + ')');
		//check si on a pas deja loade deja une fois car peut swiper de droite a gauche
		var bIsLoaded = parseInt($('#items-' + id).attr('isloaded'));
		this.debug('bIsLoaded: ' + bIsLoaded);
		if(!bIsLoaded){
			//
			var oExercice = this.jprogram.getExerciceById(id);
			//
			if(typeof(oExercice) == 'object'){
				//
				var str = '';
				//thumbs 1 or 2 depending on arr returned
				var arrThumbs = oExercice.getPicturesArray();
				//mirror img
				var strExtraClass = '';
				if(oExercice.getMirror() === 1){
					strExtraClass += ' mirror ';
					}
					
				//open inner div
				str += '<div class="caroussel-div-container">';
				//open le content des details de exercice
				str += '<div class="caroussel-content">';	
				
				//icons and title
				str += '<div class="top-title-caroussel">';
				str += '<div class="title">' + oExercice.getCode() + '</div>';	
				str += '<div class="butts">';
				if(oExercice.getVideoType() == 'sprout'){
					str += '<div class="square svideo" exercice-id="' + oExercice.getId() + '"><img src="' + gServerPath + 'images/' + gBrand + '/icon-video.png"></div>';
					}
				str += '<div class="square smirror" exercice-id="' + oExercice.getId() + '"><img src="' + gServerPath + 'images/' + gBrand + '/icon-mirror.png"></div>';
				if(arrThumbs.length == 2){ //si a deux images
					str += '<div class="square sflip" exercice-id="' + oExercice.getId() + '"><img src="' + gServerPath + 'images/' + gBrand + '/icon-switch.png"></div>';
					}
				str += '</div>'; //close butts	
				str += '</div>'; //close top-title-caroussel

				//
				str += '<div class="div-sty-24">';
				if(arrThumbs.length == 2){
					if(oExercice.getFlip() === 1){
						str += '<div class="div-sty-26 img-a left ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[1] + '"></div>';
						str += '<div class="div-sty-26 img-b right ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[0] + '"></div>';
					}else{
						str += '<div class="div-sty-26 img-a right ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[1] + '"></div>';
						str += '<div class="div-sty-26 img-b left ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[0] + '"></div>';
						}
				}else{
					str += '<div class="div-sty-26 img-a alone ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[0] + '"></div>';
					}
				str += '</div>';

				//get settings
				//show all settings
				var arrSettings = oExercice.getSettings();
				var iRows = 0;
				var strTable = '<div class="table settings">';
				for(var o in arrSettings){
					if(arrSettings[o] != ''){
						iRows++;
						strTable += '<div class="row">';
						strTable += '<div class="cell title">' + jLang.t(o + ':') + '</div>';
						strTable += '<div class="cell">' + arrSettings[o] + '</div>';
						strTable += '</div>'; //close row
						}
					}
				strTable += '</div>'; //close table
				//div des settings
				if(iRows){
					str += '<div class="div-sty-28 settings">';
					str += strTable;
				}else{
					str += '<div class="div-sty-28 nosettings">';
					}
				//title
				//le bouton pour ouvrir les setting panel
				str += '<div class="div-sty-24 settings">';		
				str += '<a href="#" class="butt small butt-change-settings" exercice-id="' + oExercice.getId() + '" >' + jLang.t('change settings') + '</a>';			
				str += '</div>';
				str += '</div>'; //close div des settings 			


				//le conteneur de tout ce qui a rapport au langues description, select box et check box incl
				str += '<div class="div-sty-29">';
				//div des langues
				str += '<div class="div-sty-28 title-and-description">';
				//load title and description depending if we show original, myinstructions or program instruction
				str += this.loadTitleAndDescription(oExercice, 'inst_3');
				//close div des langues
				str += '</div>';
				//instructions
				str += '<div class="div-sty-25">';
				str += '<select class="select-1 caroussel" id="input-exercice-type" exercice-id="' + oExercice.getId() + '">';
				//check si a du userdata = myinstructions
				if(oExercice.haveUserData()){
					str += '<option value="inst_2">' + jLang.t('my instructions') + '</option>';
					}
				str += '<option value="inst_3" selected>' + jLang.t('programs instructions') + '</option>'; //par defaut car on est dans la window de programs
				str += '<option value="inst_1">' + jLang.t('physiotec instructions') + '</option>';
				str += '</select>';
				str += '</div>';
				//close le conteneur de tout ce qui a rapport au langues
				str += '</div>';


				//DONE
				//append le contenu dans le container du caroussel
				$('#items-' + id).append(str);
				//set the attr
				$('#items-' + id).attr('isloaded', 1);

				//le link pour le changement de langue du title et de la description, 
				//car il faut que le contenu soit ecrit avant de mettre des events dessus
				
				//juste celu sur lequel on tombe ca les autres seront loade dynamiquement par la suite sur le change pane du JSlider	
				$('#items-' + id + ' .navbar A').click(function(e){
					e.preventDefault();
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						//change the title and description
						oTmp.changeTitleAndDescByLangForExerciceInCaroussel($(this));
						}
					});

				//le event for open video player
				$('#items-' + id + ' DIV.svideo').click(function(e){
					e.preventDefault();
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						var id = $(this).attr('exercice-id');
						oTmp.openExerciceVideo(id, 'program');
						}
					});
				
				//event for mirroring images if there are 2 images
				$('#items-' + id + ' DIV.smirror').click(function(e){
					e.preventDefault();
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						var id = $(this).attr('exercice-id');
						oTmp.mirrorExerciceImage(id, 'programs');
						}
					});	

				//event for switch order
				$('#items-' + id + ' DIV.sflip').click(function(e){
					e.preventDefault();
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						var id = $(this).attr('exercice-id');
						oTmp.flipExerciceImage(id, 'programs');
						}
					});

				//le bouton modifier les pârametre endessous de la description
				//ils font tous la memechose alors y aller avec la classe
				$('#items-' + id + ' .butt-change-settings').click(function(e){
					e.preventDefault();
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						var id = parseInt($(this).attr('exercice-id'));
						oTmp.openSettingFromProgramCaroussel(id);
						}
					});



				//DWIZZEL:: quand change le text va changer la couleur du bouton save pour avertir l'usager 
				//le event sur le input text change pour chacune des langues
				this.addEventToInputChangeFromProgramCaroussel(id, false);
				//fix
				this.fixImage();
			}else{
				//nope
				}
		}else{
			//alors l'exercice est deja loade
			//donc on va le faire afficher si il etait deja loade
			//et on va hider les autres pour liberer la memoire du f&*%$ iPad
			//on montre celui sur lequel on tombe
			}
		//
		}

	//----------------------------------------------------------------------------------------------------------------------*
	this.addEventToButtonFromProgramCaroussel = function(id){
		this.debug('addEventToButtonFromProgramCaroussel(' + id + ')');
		//id
		$('#butt-delete-exercice-from-program, #butt-save-exercice-modification-from-program').attr('exercice-id', id);
		//
		//rm previous
		$('#butt-delete-exercice-from-program, #butt-save-exercice-modification-from-program').unbind();
		//delete exercice from progrm
		$('#butt-delete-exercice-from-program').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = parseInt($(this).attr('exercice-id'));
				//remove	
				oTmp.rmExercice(id, false);
				//close the caroussel
				oTmp.closePopupCaroussel();
				}
			});
		
		this.changeProgramCarousselSaveButtStateOnInputChange(parseInt($('#items-' + id).attr('haschanged')), id);

		//save modification
		$('#butt-save-exercice-modification-from-program').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = parseInt($(this).attr('exercice-id'));
				oTmp.changeProgramCarousselSaveButtStateOnInputChange(1, id);
				oTmp.saveProgramCarousselInputModifications(id);
				}
			});
		

		//le select box pour le choix de myinstruction, program instruction, original instruction
		$('#items-' + id + ' #input-exercice-type').unbind();
		//
		$('#items-' + id + ' #input-exercice-type').change(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var val = $(this).val();
				var id = parseInt($(this).attr('exercice-id'));
				oTmp.changeSearchOrProgramCarousselInstructionType(id, val, true);
				}
			});

		//le checkbxox
		this.initSetHasMyInstructionCheckboxState(id);
		}
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.modifyProgramExerciceFromCaroussel = function(id){ //id = exercice id in the jprogram
		this.debug('modifyProgramExerciceFromCaroussel(' + id + ')');
		//on va dans le caroussel chercher les infos que l'usager a changé, 
		var objLocale = this.buildModifiedCarousselData(id);
		
		//le flip et mirror des images
		this.jprogram.setExerciceFlipById(id, parseInt($('#items-' + id).attr('flip-on')));
		this.jprogram.setExerciceMirrorById(id, parseInt($('#items-' + id).attr('mirror-on')));

		//en meme temps on check si la case 'set has my instruction' est coche si oui on envoie les nouvelles infos au serveur
		if(this.bSetHasMyInstruction){
			//les my-instructions dans le search
			this.jsearch.setHasMyInstruction(id, objLocale, 'program'); //will callback a function
			//les my-instructions dans le programme
			this.jprogram.overwriteUserData(id, objLocale);
		}else{
			//on attaned pas apres le serveur alors on change le state tout de suite
			this.changeProgramCarousselSaveButtStateOnInputChange(2, id);	
			}
				
		//ajoute le programdata que l'on retourne pour mettre dana le jprogram et creer la addBox avec
		this.jprogram.overwriteProgramData(id, objLocale);
		//change le save state de program
		this.changeProgramSaveState(false);	
		//modifier les titres des boites en les redessinant
		this.displayProgramBoxesHasContentView();
		}	
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.rmExercice = function(id, bAnim){
		this.rmItemsFromPrograms(id, bAnim);
		this.unlightItemsFromSearch(id);
		}
			
	//----------------------------------------------------------------------------------------------------------------------*
	this.fillSearchCaroussel = function(id){
		this.debug('fillSearchCaroussel(' + id + ')');
		var cmpt = 0;
		var rCmpt = 0;
		var str = '';
			
		//close and add button at the bottom of the appz
		str += '<div class="popup-tools">';
		str += '<div>';
		str += '<a href="#" class="butt med med-3" id="butt-save-exercice-modification-from-caroussel" exercice-id="-1">' + jLang.t('no change') + '</a>';
		str += '<a href="#" class="butt med med-3" id="butt-caroussel-close">' + jLang.t('close') + '</a>';
		str += '<a href="#" class="butt med med-3" id="add-remove-exercice-from-caroussel" exercice-id="-1" add-exercice="1" style="">' + jLang.t('add to program') + '</a>';
		str += '</div>';
		str += '</div>';
		//main caroussel container
		str += '<div id="main-caroussel"></div>';
		//on set les boutons et le container 
		$('#main-popup-caroussel').html(str);

		//WILL TRY with a key index numeric of a pointer
		var arrShowedIndex = [];
		var arrExerciceByKeys = this.jsearch.getArrExercicesKeyIndex();
		str = '';
		for(var o in arrExerciceByKeys){
			var oExercice = this.jsearch.getExerciceById(arrExerciceByKeys[o]);
			if(oExercice !== false){
				if(oExercice.getId() == id){
					rCmpt = cmpt; //the pos of the id, the one we will see 
					//si loader ou content
					//ceux a qui on set des events listeners ou onclick
					arrShowedIndex.push(cmpt);
					str += '<div class="caroussel-item" id="items-' + oExercice.getId() + '" exercice-id="' + oExercice.getId() + '" index-id="' + cmpt + '" isloaded="1" haschanged="0" mirror-on="' + oExercice.getMirror() + '" flip-on="' + oExercice.getFlip() + '" >';	
					str += this.setSearchCarousselContent(oExercice);
					str += '</div>';
				}else{
					str += '<div class="caroussel-item" id="items-' + oExercice.getId() + '" exercice-id="' + oExercice.getId() + '" index-id="' + cmpt + '" isloaded="0" haschanged="0" mirror-on="' + oExercice.getMirror() + '" flip-on="' + oExercice.getFlip() + '" ></div>';
					}
				cmpt++;
				}
			}	

		$('#main-popup-caroussel > #main-caroussel').append(str);	
			
		//le link pour le changement de langue du title et de la description, car il faut que le contenu soit ecrit avant de mettre des events dessus
		//juste celu sur lequel on tombe ca les autres seront loade dynamiquement par la suite sur le change pane du JSlider
		//on check pour le precedent et suivant
		if(typeof(arrExerciceByKeys[rCmpt-1]) != 'undefined'){
			//alors on a un precedent
			arrShowedIndex.push(rCmpt-1);
			//set content
			this.setSearchCarousselContentById(arrExerciceByKeys[rCmpt-1], rCmpt, (rCmpt-1));
			}
		if(typeof(arrExerciceByKeys[rCmpt+1]) != 'undefined'){
			//alors on a un suivant
			arrShowedIndex.push(rCmpt+1);
			//set content
			this.setSearchCarousselContentById(arrExerciceByKeys[rCmpt+1], rCmpt, (rCmpt+1));
			}
		
		//les flag a loader pour pas refaire
		for(var o in arrShowedIndex){
			//on met le contenu a loader pour ne pas qu'il le cherche encore ou put 2 fois des events sur les boutons
			$('.caroussel-item[index-id="' + arrShowedIndex[o] + '"]').attr('isloaded', 1);
			}
			
		//les boutons de langue
		$('.caroussel-item[index-id="' + rCmpt + '"] .navbar A').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//change the title and description
				oTmp.changeTitleAndDescByLangForExerciceInCaroussel($(this));
				}
			});

		//le event for open video player
		$('.caroussel-item[index-id="' + rCmpt + '"] DIV.svideo').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = $(this).attr('exercice-id');
				oTmp.openExerciceVideo(id, 'search');
				}
			});
		
		//event for mirroring images if there are 2 images
		$('.caroussel-item[index-id="' + rCmpt + '"] DIV.smirror').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = $(this).attr('exercice-id');
				oTmp.mirrorExerciceImage(id, 'search');
				}
			});	

		//event for switch order
		$('.caroussel-item[index-id="' + rCmpt + '"] DIV.sflip').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = $(this).attr('exercice-id');
				oTmp.flipExerciceImage(id, 'search');
				}
			});	
		
		//le vent sur le change des input box de description
		this.addEventToInputChangeFromSearchCaroussel(rCmpt, true);
		
		//fix
		this.fixImage();
		//return the position	
		return rCmpt;
		}	
	
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	//quand on clique sur le lien de langue dans le caroussel pour changer les descripions
	this.changeTitleAndDescByLangForExerciceInCaroussel = function(obj){
		this.debug('changeTitleAndDescByLangForExerciceInCaroussel(' + obj + ')');
		var lang = obj.attr('lang-id');
		var id = parseInt(obj.attr('exercice-id'));
		//removed all selected one
		$('#items-' + id + ' .navbar A').removeClass('selected');	
		//chanage the seclected A
		obj.addClass('selected');
		//show the description and hide the others
		$('#items-' + id + ' .div-caroussel-lang[lang-id!=' + lang + ']').removeClass('show');	
		//
		$('#items-' + id + ' .div-caroussel-lang[lang-id=' + lang + ']').addClass('show');	
	
		}


	//----------------------------------------------------------------------------------------------------------------------*
	this.addEventToButtonFromSearchCaroussel = function(id, bAdd){
		this.debug('addEventToButtonFromSearchCaroussel(' + id + ', ' + bAdd + ')');
	
		//rm previous
		$('#add-remove-exercice-from-caroussel, #butt-save-exercice-modification-from-caroussel').unbind();
		//id
		$('#add-remove-exercice-from-caroussel, #butt-save-exercice-modification-from-caroussel').attr('exercice-id', id);
		//
		if(bAdd){
			$('#add-remove-exercice-from-caroussel').removeClass('red');
			$('#add-remove-exercice-from-caroussel').text(jLang.t('add to program'));	
			$('#add-remove-exercice-from-caroussel').attr('add-exercice', 1);		
		}else{
			$('#add-remove-exercice-from-caroussel').addClass('red');
			$('#add-remove-exercice-from-caroussel').text(jLang.t('remove from program'));
			$('#add-remove-exercice-from-caroussel').attr('add-exercice', 0);	
			}
		
		//check pour le change state des input de langue sur les modifications
		this.changeSearchCarousselSaveButtStateOnInputChange(parseInt($('#items-' + id).attr('haschanged')), id);
		
		//action sur le save butt si haschanged de l'item est à un, mais ce fait maintenant de facon automatique sur la perte de focus
		$('#butt-save-exercice-modification-from-caroussel').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var id = $(this).attr('exercice-id');
				oTmp.saveOnClickSearchCarousselInputModifications(id);
				}
			});
		//
		$('#add-remove-exercice-from-caroussel').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var iAdd = parseInt($(this).attr('add-exercice'));
				var id = parseInt($(this).attr('exercice-id'));
				if(iAdd === 1){
					//add
					oTmp.addItemsToProgramsFromCaroussel(id);
					oTmp.addEventToButtonFromSearchCaroussel(id, false);
				}else{
					//remove
					oTmp.rmExercice(id, false);
					oTmp.addEventToButtonFromSearchCaroussel(id, true);
					}
				}
			});

		//le select box pour le choix de myinstruction, program instruction, original instruction
		$('#items-' + id + ' #input-exercice-type').unbind();
		//
		$('#items-' + id + ' #input-exercice-type').change(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				var val = $(this).val();
				var id = parseInt($(this).attr('exercice-id'));
				oTmp.changeSearchOrProgramCarousselInstructionType(id, val, false);
				}
			});

		//le checkbox
		this.initSetHasMyInstructionCheckboxState(id);

		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	//switch quand clicque dessus car doit ds'appliquer a tout les checkbox d'un coup
	this.switchSetHasMyInstructionCheckboxState = function(id){
		this.debug('switchSetHasMyInstructionCheckboxState(' + id + ')');
		if(this.bSetHasMyInstruction == true){
			this.bSetHasMyInstruction = false;
		}else{
			this.bSetHasMyInstruction = true;
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//iniitialise le state checked du checkbox has my instruction
	this.initSetHasMyInstructionCheckboxState = function(id){
		this.debug('initSetHasMyInstructionCheckboxState(' + id + ')');
		//le checkbox on change
		$('#items-' + id + ' .input-exercice-set-has-instruction').unbind();
		$('#items-' + id + ' .input-exercice-set-has-instruction').prop('checked', this.bSetHasMyInstruction);
		//
		$('#items-' + id + ' .input-exercice-set-has-instruction').change(function(){	
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//change the title and description
				var id = $(this).attr('exercice-id');
				oTmp.switchSetHasMyInstructionCheckboxState(id);
				}	
			});
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//change le type d'instruction pour un pâne selon le exercice-id
	//myinstruction, program instruction, original instruction		
	this.changeSearchOrProgramCarousselInstructionType = function(id, type, bFromProgram){
		this.debug('changeSearchOrProgramCarousselInstructionType(' + id + ', ' + type + ', ' + bFromProgram  + ')');
		//on va changer le content de la case des langues
		var str = '';
		var oExercice;
		//selon le type on ne va pas dans la meme classe
		if((type == 'inst_1' || type == 'inst_2') && !bFromProgram){ //original instruction ou my instructions
			oExercice = this.jsearch.getExerciceById(id);
		}else if(type == 'inst_3'){ //program instruction alway from the class JProgram
			oExercice = this.jprogram.getExerciceById(id);
		}else{
			oExercice = this.jprogram.getExerciceById(id);	
			}
		//minor check
		if(typeof(oExercice) == 'object'){
			str += this.loadTitleAndDescription(oExercice, type);
			}
		//on va remplacer le contenu des langues
		$('#items-' + id + ' .title-and-description').html(str);

		//on va placer les event sur le bouton des langues mais avant on va unbinder le tout
		$('#items-' + id + ' .navbar A').unbind();
		//le link pour le changement de langue du title et de la description, car il faut que le contenu soit ecrit avant de mettre des events dessus
		//juste celu sur lequel on tombe ca les autres seront loade dynamiquement par la suite sur le change pane du JSlider	
		$('#items-' + id + ' .navbar A').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//change the title and description
				oTmp.changeTitleAndDescByLangForExerciceInCaroussel($(this));
				}
			});
		//le change event des input box de description du search caroussel
		if(!bFromProgram){
			//on change le haschanged a 0, car il vient de changer d'instruction et on les a perdu sur le changement de select
			this.changeSearchCarousselSaveButtStateOnInputChange(0, id);
			//le checkbox
			this.initSetHasMyInstructionCheckboxState(id);
			//les event des input
			this.addEventToInputChangeFromSearchCaroussel(id, false);
		}else{
			//from program caroussel
			//on change le haschanged a 0, car il vient de changer d'instruction et on les a perdu sur le changement de select
			this.changeProgramCarousselSaveButtStateOnInputChange(0, id);
			//le checkbox
			this.initSetHasMyInstructionCheckboxState(id);
			//les event des input
			this.addEventToInputChangeFromProgramCaroussel(id, false);
			}
		}


	//----------------------------------------------------------------------------------------------------------------------*
	//retourne le contenu pour l'affichage direct du caroussel, c'est a dire celui sur lequel on a fait le press
	this.setSearchCarousselContent = function(oExercice){
		this.debug('setSearchCarousselContent()');
		//
		var str = '';
		//num of exercices
		var arrThumbs = oExercice.getPicturesArray();
		//mirror img
		var strExtraClass = '';
		if(oExercice.getMirror() === 1){
			strExtraClass += ' mirror ';
			}
			
		str += '<div class="caroussel-div-container">';
		//open le content des details de exercice
		str += '<div class="caroussel-content">';	
			
		//icons and title
		str += '<div class="top-title-caroussel">';
		str += '<div class="title">' + oExercice.getCode() + '</div>';	
		str += '<div class="butts">';
		if(oExercice.getVideoType() == 'sprout'){
			str += '<div class="square svideo" exercice-id="' + oExercice.getId() + '"><img src="' + gServerPath + 'images/' + gBrand + '/icon-video.png"></div>';
			}
		str += '<div class="square smirror" exercice-id="' + oExercice.getId() + '"><img src="' + gServerPath + 'images/' + gBrand + '/icon-mirror.png"></div>';
		if(arrThumbs.length == 2){ //si a deux images
			str += '<div class="square sflip" exercice-id="' + oExercice.getId() + '"><img src="' + gServerPath + 'images/' + gBrand + '/icon-switch.png"></div>';
			}
		str += '</div>'; //close butts	
		str += '</div>'; //close top-title-carouseel

		//thumbs 1 or 2 depending on arr returned
		str += '<div class="div-sty-24">';	
		if(arrThumbs.length == 2){
			if(oExercice.getFlip() === 1){
				str += '<div class="div-sty-26 img-a left ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[1] + '"></div>';
				str += '<div class="div-sty-26 img-b right ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[0] + '"></div>';
			}else{
				str += '<div class="div-sty-26 img-a right ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[1] + '"></div>';
				str += '<div class="div-sty-26 img-b left ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[0] + '"></div>';
				}
		}else{
			str += '<div class="div-sty-26 img-a alone ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[0] + '"></div>';
			}
		str += '</div>';
		//le conteneur de tout ce qui a rapport au langues description, select box et check box incl
		str += '<div class="div-sty-29">';
		//div des langues
		str += '<div class="div-sty-28 title-and-description">';
		//load title and description depending if we show original, myinstructions or program instruction
		str += this.loadTitleAndDescription(oExercice, false);
		//close div des langues
		str += '</div>';
		//instructions
		str += '<div class="div-sty-25">';
		str += '<select class="select-1 caroussel" id="input-exercice-type" exercice-id="' + oExercice.getId() + '">';
		//check si a du userdata = myinstructions
		if(oExercice.haveUserData()){
			str += '<option value="inst_2" selected>' + jLang.t('my instructions') + '</option>';
			}
		str += '<option value="inst_1">' + jLang.t('physiotec instructions') + '</option>';
		str += '</select>';
		str += '</div>';
		//close le conteneur de tout ce qui a rapport au langues
		str += '</div>';
		
		//close caroussel-content
		str += '</div>';
		//close caroussel-div-container
		str += '</div>';
		
		
		//
		return str;	
		
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//load le title et description depend si userdata est vide, depend d
	//type ::  1=original instructiom (inst_1), 2=my instruction (inst_2), 3=program instruction (inst_3) est utilise avec le change du select box
	this.loadTitleAndDescription = function(oExercice, instType){
		//this.debug('loadTitleAndDescription(' + JSON.stringify(oExercice) + ', ' + instType + ')');
		this.debug('loadTitleAndDescription(' + oExercice + ', ' + instType + ')');
		
		var str = '';
		var arrLang;
		//les langues au complet dependemnt du array
		if(!instType){ //pour le carrouseel search,  pour le carousel program c'est le else
			if(oExercice.haveUserData()){
				//my instruction
				arrLang = oExercice.getUserDataLanguageArray();
			}else{
				//physiotec instruction
				arrLang = oExercice.getLanguageArray();
				}
		}else{
			//on check le type pour savoir ou aller cherche le data
			if(instType == 'inst_1'){ //original instruction
				//physiotec instruction
				arrLang = oExercice.getLanguageArray();
			}else if(instType == 'inst_2'){ //my instruction
				//my instruction
				arrLang = oExercice.getUserDataLanguageArray();
			}else if(instType == 'inst_3'){ //program instruction
				//program instruction
				arrLang = oExercice.getProgramDataLanguageArray();
				}
			}
		//par defaut lang
		var strLang = gLocaleLang;
		//si c'est un program carousel alors on affiche le program dans la langue du client si il y a un client	
		if(instType !== false){ //program instruction
			strLang = this.jclientmanager.getClientLocale(this.jprogram.getClientId()); 
			}
		//open container du title and description because we are going to dshow and hide this layer depending n the selected lang from A link	
		for(var o in arrLang){
			str += '<div class="div-caroussel-lang';
			if(o == strLang){
				str += ' show ';
				}
			str += '" lang-id="' + o + '">';
			//
			var title = '';
			var description = '';
			//si c'est un change du select box
			if(!instType){ //pas un select change mais un call de function direct
				//depend si a des my instructions
				if(oExercice.haveUserData()){
					//title = oExercice.getUserDataTitleByLangForCaroussel(o);
					title = oExercice.getUserDataTitleByLang(o);
					description = oExercice.getUserDataDescriptionByLang(o);	
				}else{
					//title = oExercice.getTitleByLangForCaroussel(o);
					title = oExercice.getTitleByLang(o);
					description = oExercice.getDescriptionByLang(o);	
					}
			}else{
				//on check le type pour savoir ou aller cherche le data
				if(instType == 'inst_1'){ //original instruction
					//title = oExercice.getTitleByLangForCaroussel(o);
					title = oExercice.getTitleByLang(o);
					description = oExercice.getDescriptionByLang(o);
				}else if(instType == 'inst_2'){ //my instruction
					//title = oExercice.getUserDataTitleByLangForCaroussel(o);
					title = oExercice.getUserDataTitleByLang(o);
					description = oExercice.getUserDataDescriptionByLang(o);
				}else if(instType == 'inst_3'){ //program instruction
					//title = oExercice.getProgramDataTitleByLangForCaroussel(o);
					title = oExercice.getProgramDataTitleByLang(o);
					description = oExercice.getProgramDataDescriptionByLang(o);
					}
				}
			//short title depending on the language
			str += '<div class="div-sty-24"><input type="text" value="' + this.jutils.javascriptFormat(title) + '" class="input-caroussel-1" lang-id="' + o + '" exercice-id="' + oExercice.getId() + '"></div>';	
			//description depending on the language
			str += '<div class="div-sty-24"><textarea class="textarea-1 caroussel" lang-id="' + o + '" exercice-id="' + oExercice.getId() + '">' + description + '</textarea></div>';
			//close container du title and description
			str += '</div>';
			}
		//checkbox set has my instruction
		str += '<div class="div-sty-24 font-1"><input type="checkbox" value="1" class="input-exercice-set-has-instruction" exercice-id="' + oExercice.getId() + '">' + jLang.t('set has my instruction') + '</div>';	
		//le div des choix de langue
		str += '<div class="div-sty-24">';
		str += '<ul class="navbar">';
		//on click of the lang we are going to load the other language form the oExercise
		for(var o in arrLang){
			str += '<li><a class="butt lang ';
			if(o == strLang){
				str += ' selected ';
				}
			str += '" href="#" exercice-id="' + oExercice.getId() + '" lang-id="' + o + '">' + arrLang[o].substr(0, 2) + '</a></li>';	
			}
		str += '</ul>';	
		str += '</div>';
		//
		return str;
		
		}
	

	//----------------------------------------------------------------------------------------------------------------------*
	//pour le loadng de contenu dynamiquement une fois sur le pane triggerd by the JSlider callBackObject method
	this.setSearchCarousselContentById = function(id, index, lastIndex){
		this.debug('setSearchCarousselContentById(' + id + ', ' + index + ', ' + lastIndex + ')');
		//check si on a pas deja loade deja une fois car peut swiper de droite a gauche
		var bIsLoaded = parseInt($('#items-' + id).attr('isloaded'));
		if(!bIsLoaded){
			//
			var oExercice = this.jsearch.getExerciceById(id);
			//
			if(typeof(oExercice) == 'object'){
				//
				var str = '';
				//thumbs 1 or 2 depending on arr returned
				var arrThumbs = oExercice.getPicturesArray();
				//mirror img
				var strExtraClass = '';
				if(oExercice.getMirror() === 1){
					strExtraClass += ' mirror ';
					}
					
				//open inner div
				str += '<div class="caroussel-div-container">';
				//open le content des details de exercice
				str += '<div class="caroussel-content">';	
				
				//icons and title
				str += '<div class="top-title-caroussel">';
				str += '<div class="title">' + oExercice.getCode() + '</div>';	
				str += '<div class="butts">';
				if(oExercice.getVideoType() == 'sprout'){
					str += '<div class="square svideo" exercice-id="' + id + '"><img src="' + gServerPath + 'images/' + gBrand + '/icon-video.png"></div>';
					}
				str += '<div class="square smirror" exercice-id="' + id + '"><img src="' + gServerPath + 'images/' + gBrand + '/icon-mirror.png"></div>';
				if(arrThumbs.length == 2){ //si a deux images
					str += '<div class="square sflip" exercice-id="' + id + '"><img src="' + gServerPath + 'images/' + gBrand + '/icon-switch.png"></div>';
					}
				str += '</div>'; //close butts	
				str += '</div>'; //close top-title-carouseel

				str += '<div class="div-sty-24">';	
				if(arrThumbs.length == 2){
					if(oExercice.getFlip() === 1){
						str += '<div class="div-sty-26 img-a left ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[1] + '"></div>';
						str += '<div class="div-sty-26 img-b right ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[0] + '"></div>';
					}else{
						str += '<div class="div-sty-26 img-a right ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[1] + '"></div>';
						str += '<div class="div-sty-26 img-b left ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[0] + '"></div>';
						}
				}else{
					str += '<div class="div-sty-26 img-a alone ' + strExtraClass + '"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" class="img-sty-6" src="' + arrThumbs[0] + '"></div>';
					}
				str += '</div>';

				//le conteneur de tout ce qui a rapport au langues description, select box et check box incl
				str += '<div class="div-sty-29">';
				//div des langues
				str += '<div class="div-sty-28 title-and-description">';
				//load title and description depending if we show original, myinstructions or program instruction
				str += this.loadTitleAndDescription(oExercice, false);
				//close div des langues
				str += '</div>';
				//instructions
				str += '<div class="div-sty-25">';
				str += '<select class="select-1 caroussel" id="input-exercice-type" exercice-id="' + oExercice.getId() + '">';
				//check si a du userdata = myinstructions
				if(oExercice.haveUserData()){
					str += '<option value="inst_2" selected>' + jLang.t('my instructions') + '</option>';
					}
				str += '<option value="inst_1">' + jLang.t('physiotec instructions') + '</option>';
				str += '</select>';
				str += '</div>';
				//close le conteneur de tout ce qui a rapport au langues
				str += '</div>';
				
				//close caroussel-content
				str += '</div>';
				//close caroussel-div-container
				str += '</div>';
				
				
				//append le contenu dans le container du caroussel
				$('#items-' + id).append(str);
				//set the attr
				$('#items-' + id).attr('isloaded', 1);
				//le link pour le changement de langue du title et de la description, car il faut que le contenu soit ecrit avant de mettre des events dessus
				//juste celu sur lequel on tombe ca les autres seront loade dynamiquement par la suite sur le change pane du JSlider	
				$('#items-' + id + ' .navbar A').click(function(e){
					e.preventDefault();
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						//change the title and description
						oTmp.changeTitleAndDescByLangForExerciceInCaroussel($(this));
						}
					});

				//le event for open video player
				$('#items-' + id + ' DIV.svideo').click(function(e){
					e.preventDefault();
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						var id = $(this).attr('exercice-id');
						oTmp.openExerciceVideo(id, 'search');
						}
					});
				
				//event for mirroring images if there are 2 images
				$('#items-' + id + ' DIV.smirror').click(function(e){
					e.preventDefault();
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						var id = $(this).attr('exercice-id');
						oTmp.mirrorExerciceImage(id, 'search');
						}
					});	

				//event for switch order
				$('#items-' + id + ' DIV.sflip').click(function(e){
					e.preventDefault();
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						var id = $(this).attr('exercice-id');
						oTmp.flipExerciceImage(id, 'search');
						}
					});


				//DWIZZEL:: quand change le text va changer la couleur du bouton save pour avertir l'usager 
				//le event sur le input text change pour chacune des langues
				this.addEventToInputChangeFromSearchCaroussel(id, false);
				//fix
				this.fixImage();
			}else{
				//nope
				}
		}else{
			//alors l'exercice est deja loade
			//donc on va le faire afficher si il etait deja loade
			//et on va hider les autres pour liberer la memoire du f&*%$ iPad
			//on montre celui sur lequel on tombe
			}
		//
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//change la couleur du bouton save dans le search caroussel quand il y a eu une modification	
	this.changeSearchCarousselSaveButtStateOnInputChange = function(iChanged, exerciceId){
		this.debug('changeSearchCarousselSaveButtStateOnInputChange(' + iChanged + ', ' + exerciceId + ')');
		//on set le bouton de couleur
		if(iChanged == 0){
			$('#butt-save-exercice-modification-from-caroussel').html(jLang.t('save'));	
		}else if(iChanged == 1){
			this.showLoader(true, '#butt-save-exercice-modification-from-caroussel', 0, 0);
		}else if(iChanged == 2){
			$('#butt-save-exercice-modification-from-caroussel').html(jLang.t('save'));
			}	
		//on flag le item du caroussel comme haschanged
		$('#items-' + exerciceId).attr('haschanged', iChanged);
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//change la couleur du bouton save dans le program caroussel quand il y a eu une modification	
	this.changeProgramCarousselSaveButtStateOnInputChange = function(iChanged, exerciceId){
		this.debug('changeProgramCarousselSaveButtStateOnInputChange(' + iChanged + ', ' + exerciceId + ')');	
		//on set le bouton de couleur
		if(iChanged == 0){
			$('#butt-save-exercice-modification-from-program').html(jLang.t('save'));	
		}else if(iChanged == 1){
			this.showLoader(true, '#butt-save-exercice-modification-from-program', 0, 0);
		}else if(iChanged == 2){
			//$('#butt-save-exercice-modification-from-program').html(jLang.t('save'));
			$('#butt-save-exercice-modification-from-program').html(jLang.t('saved'));
		}else if(iChanged == 3){
			$('#butt-save-exercice-modification-from-program').html(jLang.t('changed'));
			}	


		//on flag le item du caroussel comme haschanged
		$('#items-' + exerciceId).attr('haschanged', iChanged);
		}

		//----------------------------------------------------------------------------------------------------------------------*
	//change la couleur du bouton save dans le search caroussel quand il y a eu une modification
	// si le set has instruction est coche alors on set par defaut pour cette exercice
	//caller seulement quand on clique sur save de search devient automatiquement coche
	this.saveOnClickSearchCarousselInputModifications = function(exerciceId){
		this.debug('saveOnClickSearchCarousselInputModifications(' + exerciceId + ')');
		//check si est toujours en mode changed
		this.bSetHasMyInstruction = true;
		this.initSetHasMyInstructionCheckboxState(exerciceId);
		//call la func de save has my instruction
		this.saveSearchCarousselInputModifications(exerciceId);
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//change la couleur du bouton save dans le search caroussel quand il y a eu une modification
	// si le set has instruction est coche alors on set par defaut pour cette exercice
	this.saveSearchCarousselInputModifications = function(exerciceId){
		this.debug('saveSearchCarousselInputModifications(' + exerciceId + ') :: ' + this.bSetHasMyInstruction);
		//check si est toujours en mode changed
		if(this.bSetHasMyInstruction){
			//change le state des boutos save
			this.changeSearchCarousselSaveButtStateOnInputChange(1, exerciceId);
			//get the text modifications
			var objLocale = this.buildModifiedCarousselData(exerciceId);
			//send text modifications
			this.jsearch.saveExerciceModifications(exerciceId, objLocale);
			}	
		}

		//----------------------------------------------------------------------------------------------------------------------*
	//change la couleur du bouton save dans le search caroussel quand il y a eu une modification	
	this.saveProgramCarousselInputModifications = function(exerciceId){
		this.debug('saveProgramCarousselInputModifications(' + exerciceId + ')');
		//check si est toujours en mode changed
		var iChanged = parseInt($('#items-' + exerciceId).attr('haschanged'));
		if(iChanged){
			//function will do the basic save
			this.modifyProgramExerciceFromCaroussel(exerciceId);
			//on change le bouton save et le state du caroussel search
			//this.changeProgramCarousselSaveButtStateOnInputChange(3, exerciceId);
			//on reload l'exercice dans le caroussel car le data a change et le select doit rajouter 'my instructions'
			this.replaceProgramCarousselInstructionOnDataChange(exerciceId);
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//retour du serveur comme quoi c'est fait, alors on peut changer le state du bouton
	this.saveSearchCarousselInputModificationsCallBackFromServer = function(extraObj){
		this.debug('saveSearchCarousselInputModificationsCallBackFromServer(' + extraObj + ')');
		if(typeof(extraObj) == 'object'){
			//on change le bouton save et le state du caroussel search
			this.changeSearchCarousselSaveButtStateOnInputChange(2, extraObj.exerciceid);
			//on reload l'exercice dans le caroussel car le data a change et le select doit rajouter 'my instructions'
			this.replaceSearchCarousselInstructionOnDataChange(extraObj.exerciceid);
			//ajoute le programdata que l'on retourne pour mettre dana le jprogram si jamais il existe
			this.jprogram.overwriteUserData(extraObj.exerciceid, extraObj.locale);
			//change le save state de program
			this.changeProgramSaveState(false);
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//retour du serveur comme quoi c'est fait, alors on peut changer le state du bouton
	this.saveSearchCarousselSetHasMyInstructionCallBackFromServer = function(extraObj){
		this.debug('saveSearchCarousselSetHasMyInstructionCallBackFromServer(' + extraObj + ')');
		if(typeof(extraObj) == 'object'){
			if(extraObj.strfrom == 'search'){
				//on change le bouton save et le state du caroussel search
				this.changeSearchCarousselSaveButtStateOnInputChange(2, extraObj.exerciceid);
			}else{
				//on change le bouton save et le state du caroussel program
				this.changeProgramCarousselSaveButtStateOnInputChange(2, extraObj.exerciceid);
				}
			//on reload l'exercice dans le caroussel car le data a change et le select doit rajouter 'my instructions'
			this.replaceSearchCarousselInstructionOnDataChange(extraObj.exerciceid);
			//ajoute le programdata que l'on retourne pour mettre dana le jprogram si jamais il existe
			this.jprogram.overwriteUserData(extraObj.exerciceid, extraObj.locale);
			//change le save state de program
			this.changeProgramSaveState(false);
			}
		}


	//----------------------------------------------------------------------------------------------------------------------*
	//retour du serveur comme quoi c'est fait, alors on peut changer le state du bouton
	this.replaceSearchCarousselInstructionOnDataChange = function(exerciceId){
		this.debug('replaceSearchCarousselInstructionOnDataChange(' + exerciceId + ')');
		//on reload l'exercice dans le caroussel car le data a change et le select doit rajouter 'my instructions'
		//on met le contenu
		this.bSetHasMyInstruction = true;
		this.initSetHasMyInstructionCheckboxState(exerciceId);
		}	

	//----------------------------------------------------------------------------------------------------------------------*
	//retour du serveur comme quoi c'est fait, alors on peut changer le state du bouton
	this.replaceProgramCarousselInstructionOnDataChange = function(exerciceId){
		this.debug('replaceProgramCarousselInstructionOnDataChange(' + exerciceId + ')');
		//on reload l'exercice dans le caroussel car le data a change et le select doit rajouter 'my instructions'
		}	


	//----------------------------------------------------------------------------------------------------------------------*
	//remove le focus des input du serach caroussel sur l'index precedent celui ou on va,
	//car sinon quand on slide le event n'est pas trigger tant que l'on ne perd pas le focus des input box
	this.forceFocusLostOnInputFromSearchCarousselWhenSlidingAway = function(id, lastId){
		this.debug('forceFocusLostOnInputFromSearchCarousselWhenSlidingAway(' + id + ', ' + lastId + ')');
		//le precedent input qui est active c'est a dire class=show
		if(id !== lastId){ //seulement si pas le meme 
			$('#items-' + lastId + ' .title-and-description .div-caroussel-lang.show input[type=text]').blur();
			$('#items-' + lastId + ' .title-and-description .div-caroussel-lang.show textarea').blur();	
			}
		}

	//----------------------------------------------------------------------------------------------------------------------*
	//remove le focus des input du serach caroussel sur l'index precedent celui ou on va,
	//car sinon quand on slide le event n'est pas trigger tant que l'on ne perd pas le focus des input box
	this.forceFocusLostOnInputFromProgramCarousselWhenSlidingAway = function(id, lastId){
		this.debug('forceFocusLostOnInputFromProgramCarousselWhenSlidingAway(' + id + ', ' + lastId + ')');
		//le precedent input qui est active c'est a dire class=show
		if(id !== lastId){ //seulement si pas le meme 
			$('#items-' + lastId + ' .title-and-description .div-caroussel-lang.show input[type=text]').blur();
			$('#items-' + lastId + ' .title-and-description .div-caroussel-lang.show textarea').blur();	
			}
		}


	//----------------------------------------------------------------------------------------------------------------------*
	//rajoute evenement au input de description et titre, ca etre caller lors de la creation ou quand on change le select boxx de my-instruction, original inst, etc...	
	this.addEventToInputChangeFromProgramCaroussel = function(id, bByIndex){
		this.debug('addEventToInputChangeFromProgramCaroussel(' + id + ', ' + bByIndex + ')');
		//si c'est par index, c'est a dire cree pour le premier element du program caroussel, pas le meme selector syntax
		//les autres se font loader sur le event du slide et envoi le exercice-id à la place
		var strSelector = '#items-' + id + ' ';
		if(bByIndex){
			strSelector = '.caroussel-item[index-id="' + id + '"] ';
			}
		//les langues
		var arrLang = $(strSelector + '.title-and-description .div-caroussel-lang').map(function(ind){
			return $(this).attr('lang-id');
			}).get();
		//le .get() est pour le chaining des retour pour que ca donne un array ["en_US", "es_MX", "fr_CA"]		
		if(typeof(arrLang) == 'object'){
			if(arrLang.length > 0){
				for(var o in arrLang){
					//title
					$(strSelector + '.title-and-description .div-caroussel-lang[lang-id="' + arrLang[o] + '"] input[type=text]').unbind();
					$(strSelector + '.title-and-description .div-caroussel-lang[lang-id="' + arrLang[o] + '"] input[type=text]').change(function(){
						//get parent class
						var oTmp = $(document).data('jappzclass');
						if(typeof(oTmp) == 'object'){
							//change the title and description
							var id = $(this).attr('exercice-id');
							oTmp.changeProgramCarousselSaveButtStateOnInputChange(1, id);
							oTmp.saveProgramCarousselInputModifications(id);
							}	
						});
					//description
					$(strSelector + '.title-and-description .div-caroussel-lang[lang-id="' + arrLang[o] + '"] textarea').unbind();
					$(strSelector + '.title-and-description .div-caroussel-lang[lang-id="' + arrLang[o] + '"] textarea').change(function(){
						//get parent class
						var oTmp = $(document).data('jappzclass');
						if(typeof(oTmp) == 'object'){
							//change the title and description
							var id = $(this).attr('exercice-id');
							oTmp.changeProgramCarousselSaveButtStateOnInputChange(1, id);
							oTmp.saveProgramCarousselInputModifications(id);
							}	
						});			
					}
				}
			}
		
		}


	//----------------------------------------------------------------------------------------------------------------------*
	//rajoute evenement au input de description et titre, ca etre caller lors de la creation ou quand on change le select boxx de my-instruction, original inst, etc...	
	this.addEventToInputChangeFromSearchCaroussel = function(id, bByIndex){
		this.debug('addEventToInputChangeFromSearchCaroussel(' + id + ',' + bByIndex + ')');
		//si c'est par index, c'est a dire cree pour le premier element du search caroussel, pas le meme selector syntax
		//les autres se font loader sur le event du slide et envoi le exercice-id à la place
		var strSelector = '#items-' + id + ' ';
		if(bByIndex){
			strSelector = '.caroussel-item[index-id="' + id + '"] ';
			}
		//les langues
		var arrLang = $(strSelector + '.title-and-description .div-caroussel-lang').map(function(ind){
			return $(this).attr('lang-id');
			}).get();
		//le .get() est pour le chaining des retour pour que ca donne un array ["en_US", "es_MX", "fr_CA"]		
		if(typeof(arrLang) == 'object'){
			if(arrLang.length > 0){
				for(var o in arrLang){
					//title
					$(strSelector + '.title-and-description .div-caroussel-lang[lang-id="' + arrLang[o] + '"] input[type=text]').unbind();
					$(strSelector + '.title-and-description .div-caroussel-lang[lang-id="' + arrLang[o] + '"] input[type=text]').change(function(){
						//get parent class
						var oTmp = $(document).data('jappzclass');
						if(typeof(oTmp) == 'object'){
							//change the title and description
							var id = $(this).attr('exercice-id');
							oTmp.saveSearchCarousselInputModifications(id);
							}	
						});
					//description
					$(strSelector + '.title-and-description .div-caroussel-lang[lang-id="' + arrLang[o] + '"] textarea').unbind();
					$(strSelector + '.title-and-description .div-caroussel-lang[lang-id="' + arrLang[o] + '"] textarea').change(function(){
						//get parent class
						var oTmp = $(document).data('jappzclass');
						if(typeof(oTmp) == 'object'){
							//change the title and description
							var id = $(this).attr('exercice-id');
							oTmp.saveSearchCarousselInputModifications(id);
							}	
						});			
					}
				}
			}
			
		}


	
	//----------------------------------------------------------------------------------------------------------------------*
	this.getAbsoluteContainerHeight = function(el){
		var max = 0;
		$.each($(el).find('li'), function(idx,desc){
			//max = Math.max(max, $(desc).offset().top + $(desc).height() );
			max = Math.max(max, $(desc).offset().top + $(desc).outerHeight(true));
			});
		//scroll top position + height
		return max + $(el).scrollTop() - $(el).offset().top; 
		}
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.hitTestForScroll = function(id, args){
		//goona get it with a setTimer and cancel the timer on panend and pancancel
		//if over the hit test make the #listing-programs scroll UP or DOWN
		//cancel previous timer
		this.cancelHitTestForScroll();
		//set the new timer
		this.timerHitTestPanScroll = setTimeout(this.checkHitTestForScroll.bind(this, id, args), 33);
		
		}
		
		
		
	//----------------------------------------------------------------------------------------------------------------------*	
	this.logarythmIncremetation = function(pos, max, increment){
		var minp = 0;
		var maxp = max;
		//
		var minv = Math.log(1);
		var maxv = Math.log(increment);
		// adjustment factor
		var scale = (maxv-minv) / (maxp-minp);

		var rtn = Math.exp(minv + scale * (pos - minp));
		if( rtn > increment){
			return increment;
			}
		//
		return parseInt(rtn);
		}
		
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.checkHitTestForScroll = function(id, args){

		var deltaX = this.lastDeltaPosition.x;
		var deltaY = this.lastDeltaPosition.y;
			
		//minor check
		if(!$('#div-drag-' + id).length){
			this.cancelHitTestForScroll();
			return;
			}
		
		//pass it has args so no need to recalculate : demanding cpu
		if(typeof(args) == 'undefined' || args.length <= 0){
			args = []; 
			args['box-height'] = $('#div-drag-' + id).outerHeight(true); //box total height with margin
			args['container-rect'] = parseInt($('#listing-programs').height()); //container height
			args['container-height'] = parseInt(this.getAbsoluteContainerHeight('#listing-programs')); // relative total height of container
			}
		
		//base attr		
		var iBaseX = parseInt($('#div-drag-' + id).attr('basex'));
		var iBaseY = parseInt($('#div-drag-' + id).attr('basey'));	
		
		//abs pos
		var yTopAbs = parseInt($('#div-drag-' + id).position().top); // absolute yTop
		var yBottomAbs = parseInt(yTopAbs + args['box-height']); //absolute yBottom
		
		//scroll pos
		var iScroll = parseInt($('#listing-programs').scrollTop()); //scroll Y
		
		//hit speed bar rect at the top and bottom of container		
		var iSpeedBar = parseInt(this.boxSettings.minH / 1.0); //height of the hit test for scroll top and bottom slower or faster
		
		//new rel position
		var nRelX = iBaseX + deltaX;
		var nRelY = iBaseY + deltaY;
		
		// IMPORATNT ajuste l'increment selon la taille max 
		// de la fenetre si petit peit increment si gros big inccrement
		//scroll incrementation
		var increment = parseInt(args['container-height']/args['container-rect']) * 10;
		
		//check for scrolling
		if(yTopAbs < iSpeedBar){ //scrolling up
			if(iScroll > 0){ //adjust scroll
				//plus pres du top on accelere
				if(iSpeedBar != 0){
					increment = this.logarythmIncremetation((iSpeedBar - yTopAbs), iSpeedBar, increment);
					}
				//positioned the drag box
				if((iScroll - increment) <= 0){
					increment = iScroll;
					}
				$('#div-drag-' + id).css({top: (nRelY - increment) + 'px', left: (nRelX) + 'px'});		
				//change the attributes
				iBaseY -= increment;
				$('#div-drag-' + id).attr('basey', iBaseY);
				//move scrool
				$('#listing-programs').scrollTop(iScroll - increment);
				
			}else{
				//positioned the drag box
				$('#div-drag-' + id).css({top: (nRelY) + 'px', left: (nRelX) + 'px'});	
				}
				
		}else if((yBottomAbs + iSpeedBar) > ((args['container-rect'] + this.boxSettings.margin))){ //scrolling down
			//get the part of the box out of container offset
			var iOffSetBottom = parseInt((yBottomAbs + iSpeedBar) - args['container-rect']);
			if(iOffSetBottom < 0){
				iOffSetBottom = 0;
				}
			if((args['container-height'] - iScroll) >= ((yBottomAbs + iSpeedBar) - iOffSetBottom + increment)){ //adjust scroll
				//plus pres du bottom on accelere
				increment = this.logarythmIncremetation((yBottomAbs - (args['container-rect'] - iSpeedBar )), iSpeedBar, increment);
				//positioned the drag box	
				$('#div-drag-' + id).css({top: (nRelY + increment) + 'px', left: (nRelX) + 'px'});	
				//change the attributes	
				iBaseY += increment;
				$('#div-drag-' + id).attr('basey', iBaseY);
				//move scrool
				$('#listing-programs').scrollTop(iScroll + increment);
				
			}else{
				//positioned the drag box
				$('#div-drag-' + id).css({top: (nRelY) + 'px', left: (nRelX) + 'px'});	
				}
				
		}else{
			//positioned the drag box
			$('#div-drag-' + id).css({top: (nRelY) + 'px', left: (nRelX) + 'px'});	
			}
		
			
		//check hit test for box on box	
		this.hitTestForBox(id, args);			
			
		//recall function
		this.hitTestForScroll(id, args);		
		
		}
	


	//----------------------------------------------------------------------------------------------------------------------*
	this.hitTestForBox = function(id, args){
		
		var x = parseInt($('#div-drag-' + id).offset().left + ($('#div-drag-' + id).outerHeight(true)/2));
		var y = parseInt($('#div-drag-' + id).offset().top + ($('#div-drag-' + id).outerHeight(true)/2));
		
		var selectedId = -1;
		
		//hit test
		$('#listing-programs > li').each(function() {
			var eid = parseInt($(this).attr('exercice-id'));
			var hit = $('#img-' + eid).hitTestPoint({'x':x ,'y':y});
			if(hit){
				if(eid != id){
					selectedId = eid;
					}
				return;
				}
			});
		
		//get las tselected
		if(this.lastSelectedForDropId == -1 || (this.lastSelectedForDropId != selectedId && selectedId != id)){
			this.resetSelectedForDrop(id, selectedId);
			}		
		
		
		}	
	
	
	
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.resetSelectedForDrop = function(id, selectedId){
		//css styling old
		if(this.lastSelectedForDropId != -1){
			$('#img-' + this.lastSelectedForDropId).removeClass('dragover');		
			}
		//put las tselected		
		if(selectedId != -1){
			this.lastSelectedForDropId = selectedId;
			//css styling new
			$('#img-' + selectedId).addClass('dragover');		
		}else{
			//reset le last selected holder
			this.lastSelectedForDropId = -1;	
			}
		}
		
		

	//----------------------------------------------------------------------------------------------------------------------*
	this.cancelHitTestForScroll = function(){
		//cancel other timer
		clearTimeout(this.timerHitTestPanScroll);
		}	
		
	

	//----------------------------------------------------------------------------------------------------------------------*
	this.checkForDropOnBox = function(id){
		
		//cancel hit testing
		this.cancelHitTestForScroll();
		
		//pos vars
		var iBoxMargin = this.boxSettings.margin; //need to get it form css style box-img
		var iFromTop = $('#listing-programs').scrollTop();
		
		//if the droped box is the same has the the draged Box or selected equal  -1
		if(this.lastSelectedForDropId == -1 || this.lastSelectedForDropId == id){
			//get the original position
			var x = $('#img-' + id).position().left + iBoxMargin;
			var y = $('#img-' + id).position().top + iFromTop + iBoxMargin;
			//
			//move to the original position
			$('#div-drag-' + id).animate(
				{
					top: y + 'px', 
					left: x + 'px',
					}, 
				{
					queue:false,
					//easing:"easeOutQuad",
					duration:this.basicAnimSpeed/2, 
					complete:function(){
						//remove the dragued box anim 
						//get parent class
						var oTmp = $(document).data('jappzclass');
						if(typeof(oTmp) == 'object'){
							oTmp.removeDragBox(id);
							}
						//bring the scroll bar so we have the focus on the box we've dragued
						},
					}	
				);
			
			
		}else{
			//index of both
			var dragIndex = $('#img-' + id).index('li');
			var dropIndex = $('#img-' + this.lastSelectedForDropId).index('li');
			//change the order in the Jprogram
			this.jprogram.changeOrder(dragIndex, dropIndex);
			//change le save state de program
			this.changeProgramSaveState(false);
			//			
			var x = $('#img-' + this.lastSelectedForDropId).position().left + iBoxMargin;
			var y = $('#img-' + this.lastSelectedForDropId).position().top + iFromTop + iBoxMargin;
		
			//cancel all hit test
			this.cancelHitTestForScroll();
		
			//anim le drag box to fit it's new place then remove drag on complete
			$('#div-drag-' + id).animate(
				{
					top: y + 'px', 
					left: x + 'px',
					}, 
				{
					queue:false,
					//easing:"easeOutQuad",
					duration:this.basicAnimSpeed/2, 
					complete:function(){
						//get parent class
						var oTmp = $(document).data('jappzclass');
						if(typeof(oTmp) == 'object'){
							//remove the dragued box anim 
							oTmp.removeDragBox(id);
							//clone le node de celui a deplacea
							var strLiNode = $('#img-' + id).clone();
							//remove le node de celui a deplacea
							$('#img-' + id).remove();
							//put it at the drop index left<->right
							if(dragIndex < dropIndex){			
								$('#listing-programs li:eq(' + (dropIndex - 1) + ')').after(strLiNode);
							}else{
								$('#listing-programs li:eq(' + dropIndex + ')').before(strLiNode);
								}
							//put ht event catcher on the moved node
							oTmp.bindEventToBox(id);
							}
						},
					}	
				);
				
			}
		
		}

	
			
	//----------------------------------------------------------------------------------------------------------------------*
	this.removeDragBox = function(id){
		//cancel the timer interval
		this.cancelHitTestForScroll();
		//on delete le box drag
		$('#div-drag-' + id).remove();
		//on remet le box original a sa coucleur original
		$('#img-' + id).removeClass('dragselected');
		//remove the border around the hover
		this.resetSelectedForDrop(id, -1);
		//reset the lastpos delta
		this.lastDeltaPosition = {};
				
		//on remet le scroll bar
		$('#listing-programs').css({'overflow':'auto'});
		
		}
	
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.createDragBox = function(id, ev){
		//exercice class
		var oExercice = this.jprogram.getExerciceById(id);
	
		if(typeof(oExercice) == 'object'){
			//margins of the box-img
			var iBoxMargin = this.boxSettings.margin; //need to get it form css style box-img
			//last delat position
			this.lastDeltaPosition = {x: parseInt(ev.deltaX), y:parseInt(ev.deltaY)};
			//scroll increment
			var iFromTop = $('#listing-programs').scrollTop();
			//on disable le scroll sur le listing-program UL
			$('#listing-programs').css({'overflow':'hidden'});
			//on disable le box proncipale
			$('#img-' + id).addClass('dragselected');
			//get the position of the clicked element par rapport a son contenant 'listing-programs'
			var x = $('#img-' + id).position().left + iBoxMargin;
			var y = $('#img-' + id).position().top + iFromTop + iBoxMargin;
			//draw the new box
			var strBoxTextStyle = 'display:block;';	
			if(!this.boxSettings.t){ //pas de text car trop petit
				strBoxTextStyle = 'display:none;';		
				}
			var str = '';
			//div because we calculate total container height with "> LI" or the container will always grow bigger
			str += '<div class="box-img-drag" id="div-drag-' + id +'" style="width:' + this.boxSettings.w + ';height:' + this.boxSettings.h + ';top:' + y + 'px;left:' + x + 'px;"><div class="div-style-23"><div class="mask-img"><img draggable="false" onerror="this.src=\'' + this.defaultExerciceImageSrc + '\'" src="' + oExercice.getThumb() + '"></div></div></div>';
			//fill html a la fin du listing des prgrams
			$('#listing-programs').append(str);
			//base xy
			$('#div-drag-' + id).attr('basex', x);
			$('#div-drag-' + id).attr('basey', y);
			$('#div-drag-' + id).attr('ready', 0);
			//animate to the last delta move untill end
			$('#div-drag-' + id).animate(
				{
					top:y + this.lastDeltaPosition.y + 'px', 
					left:x + this.lastDeltaPosition.x + 'px'
					}, 
				{
					queue:false,
					duration:this.basicAnimSpeed/2, 
					complete:function(){
						//get parent class
						var oTmp = $(document).data('jappzclass');
						if(typeof(oTmp) == 'object'){
							//put ready for panmove event catch
							$('#div-drag-' + id).attr('ready', 1);
							oTmp.checkHitTestForScroll(id, []);
							}
						},
					step:function(now, fx){
						//get parent class
						var oTmp = $(document).data('jappzclass');
						if(typeof(oTmp) == 'object'){
							//change anim of deltaX or dlataY has changed
							if(fx.prop == 'left'){
								//base on panmove last delta
								fx.end = x + oTmp.lastDeltaPosition.x;
							}else if(fx.prop == 'top'){
								fx.end = y + oTmp.lastDeltaPosition.y;
								}
							}
						},		
					}	
				);
			}
		}
	
	
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.boxEventHandler = function(id, ev){
		// disable browser scrolling
		ev.preventDefault();
		//check the type of event
		switch(ev.type) {
			case 'singletap':
				this.setBoxSelection('img-' + id);
				//
				break;
			case 'press':
				this.openProgramCaroussel(id);
				//
				break;
			case 'panstart':
				this.createDragBox(id, ev);
				//
				break;
				
			case 'panend':
			case 'pancancel':
				// si pas par dessus un autre ou sur le meme on delete simplement
				// sinon on check celui sur lequel hover
				if($('#div-drag-' + id).length){
					this.checkForDropOnBox(id);
					}
				break;
				
			case 'panmove':
				//keep it for the end of animation to keep with the pointer move
				this.lastDeltaPosition = {x: parseInt(ev.deltaX), y:parseInt(ev.deltaY)};
				//
				break;
			
			default: 
				//
				break;
				
			}
		
		}
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.boxSearchEventHandler = function(id, ev){
		// disable browser scrolling
		ev.preventDefault();
		//check the type of event
		switch(ev.type) {
			case 'press':
				this.openSearchCaroussel(id);
				//
				break;
			
			case 'singletap':
				this.setSearchBoxSelection(id);
				//
				break;
			
			default: 
				//
				break;
			}
		}	
		

	
	//----------------------------------------------------------------------------------------------------------------------*
	this.boxEvent = function(id){
		this.debug('boxEvent(' + id + ')');
		//
		var el = document.getElementById('img-' + id);
		//minor check for undefined functio bug with jquery
		if(typeof(el) == 'object'){
			var ham = new Hammer.Manager(el);
			ham.add(new Hammer.Tap({event:'singletap', time:300, threshold:30}));
			ham.add(new Hammer.Press({event:'press', time:500}));
			ham.add(new Hammer.Pan({event:'pan', direction: Hammer.DIRECTION_HORIZONTAL, threshold:80}));
			ham.on('singletap press panstart panmove panend pancancel', this.boxEventHandler.bind(this, id));
			this.jprogram.addEventManager(id, ham);
			}				
		}
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.boxSearchEvent = function(id){
		//this.debug('boxSearchEvent(' + id + ')');
		//
		var el = document.getElementById('search-img-' + id);
		//minor check for undefined functio bug with jquery
		if(typeof(el) == 'object'){
			var ham = new Hammer.Manager(el);
			ham.add(new Hammer.Tap({event:'singletap', time:300, threshold:30}));
			ham.add(new Hammer.Press({event:'press', time:500}));
			ham.on('singletap press', this.boxSearchEventHandler.bind(this, id));
			this.jsearch.addEventManager(id, ham);
			}				
		}	
		
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	this.showToolsLayerSettings = function(){
		this.debug('showToolsLayerSettings()');

		//set the flag
		if($('#layer-settings').attr('showing') == '0'){
			this.showListingProgramsTitle(false);
			//
			$('#layer-settings').attr('showing','1');
			//le max top de la layer
			var maxHeight = this.containerSize.h - $('#layer-settings > .layer-content').outerHeight() - this.cssExtraStyle.layerSettingsTotalPaddingHeight;
			if(maxHeight < this.cssExtraStyle.mainMenuTopHeight){
				maxHeight = this.cssExtraStyle.mainMenuTopHeight; //50px from top just under program name
				}
			
			//animate to top
			$('#layer-settings').animate(
				{top:maxHeight + 'px'}, //IMPORATNT: il manque un 20 pixel de la barre du bas pour monter assez haut
				{queue:false,
				duration:this.basicAnimSpeed, 
				complete:function(){
						//get parent class
						var oTmp = $(document).data('jappzclass');
						if(typeof(oTmp) == 'object'){
							$(this).attr('showing','0');
							$(this).attr('showed','1');
							//minimuze the box-img
							oTmp.changeZooming(false);
							}
						},
				progress:function(){
						//get parent class
						var oTmp = $(document).data('jappzclass');
						if(typeof(oTmp) == 'object'){
							//la barre the settings va monter alors on reajuste le contenu de listing-programs
							oTmp.changeProgramListingBottom($('#layer-settings').position().top, false);
							}
						}		
					}	
				);
		
		}else if($('#layer-settings').attr('showed') == '1' && $('#layer-settings').attr('showing') != '1'){
			//on ferme
			this.closeToolsLayerSettings();
			}
		}	
	
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.showListingProgramsTitle = function(bShow){
		this.debug('showListingProgramsTitle(' + bShow + ')');
		if(bShow){
			$('#butt-modify-program').animate(
				{
					'margin-top':0 + 'px'
					}, 
				{
					queue:false,
					duration:this.basicAnimSpeed, 
					}	
				);
			$('#listing-programs').animate(
				{
					//'top':this.cssExtraStyle.listingProgramsTitleHeight + 'px' ,
					//'top':parseInt($('#butt-modify-program').outerHeight(true)) + 'px' ,
					'bottom':this.cssExtraStyle.bottomProgramsTools + 'px' ,					
					}, 
				{
					queue:false,
					duration:this.basicAnimSpeed,
					progress: function(){
						$(this).css({top: parseInt($('#butt-modify-program').outerHeight(true)) + 'px'});
						},	
					}		
				);
			$('#main-settings-top').animate(
				//{'top':-50 + 'px'}, ou -60 quand on est en largeur 600+ 
				{'top':-(this.cssExtraStyle.mainMenuTopHeight + 20) + 'px'}, 
				{
					queue:false,
					duration:this.basicAnimSpeed, 
					}	
				);
			$('#main-settings-bottom').animate(
				//{'bottom':-60 + 'px'}, 
				{
					'bottom':-(this.cssExtraStyle.bottomProgramsTools + 20) + 'px'
					}, 
				{
					queue:false,
					duration:this.basicAnimSpeed, 
					}	
				);
			
		} else{
			$('#butt-modify-program').animate(
				{
					//'margin-top': -this.cssExtraStyle.listingProgramsTitleHeight + 'px'
					'margin-top': -(parseInt($('#butt-modify-program').outerHeight(true))) + 'px'
					}, 
				{
					queue:false,
					duration:this.basicAnimSpeed, 
					}	
				);
			$('#listing-programs').animate(
				{
					'top':0 + 'px',
					'bottom':0 + 'px' , 
				}, 
				{
					queue:false,
					duration:this.basicAnimSpeed, 
					}	
				);
			$('#main-settings-top').animate(
				{'top':0 + 'px'}, 
				{
					queue:false,
					duration:this.basicAnimSpeed, 
					}	
				);
			$('#main-settings-bottom').animate(
				{'bottom':0 + 'px'}, 
				{
					queue:false,
					duration:this.basicAnimSpeed, 
					}	
				);
				
			}
		}
	
	//----------------------------------------------------------------------------------------------------------------------*
	this.showLayer = function(id){
		this.debug('showLayer(' + id + ')');
		//set the flag
		if($('#layer-' + id).attr('showing') == '0'){
			//
			$('#layer-' + id).attr('showing','1');
			//animate to top
			$('#layer-' + id).animate(
				{top:'0px'}, 
				{queue:false,
				duration:this.basicAnimSpeed, 
				start:function(){
						//on display none vu quil est hide
						$(this).css({'display':'block'});
						},
				complete:function(){
						//state
						$(this).attr('showing','0');
						$(this).attr('showed','1');
						if($(this).attr('id') == 'layer-search'){
							var oTmp = $(document).data('jappzclass');
							if(typeof(oTmp) == 'object'){
								oTmp.changeSearchExerciceCounter(true);
								}
							}
						}
					}	
				);
			}
		}
		
		
	//----------------------------------------------------------------------------------------------------------------------*
	//hide all layers
	this.closeAllLayers = function(){
		this.debug('closeAllLayers()');
		//on hide le counter d,exercice du serach layer si il y a 
		this.changeSearchExerciceCounter(false);
		//close popup if some
		this.closePopup();
		//butt no focus
		$('.butt-slide').removeClass('selected');
		//butt programs focus
		$('#butt-programs').addClass('selected');
		//on close the others
		$('.layer-slide').animate(
			{top:this.containerSize.h}, 
			{queue:false,
			duration:this.basicAnimSpeed, 
			complete: function(){
					//on display none vu quil est hide
					$(this).css({'display':'none'});
					//state
					$(this).attr('showed', '0');
					}
				}
			);
		}
	
	
	//----------------------------------------------------------------------------------------------------------------------
	//show clicked layer and hide the others	
	this.openLayer = function(id){
		this.debug('openLayer(' + id + ')');
		//les popup si il y a
		this.closePopup();
		//si c,est le serach alors on montre le cunter de resultat
		if(id != 'search'){
			this.changeSearchExerciceCounter(false);
			}
		//si closed
		if($('#layer-' + id).attr('showed') == '0' && $('#layer-' + id).attr('showing') != '1'){
			//all buttons
			$('.butt-slide').removeClass('selected');
			//le button
			$('#butt-' + id).addClass('selected');
			//on close the others
			$('.layer-slide').each(function(){
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					if($(this).attr('showed') == '1' || $(this).attr('showing') == '1' && $(this).id != 'layer-' + id){
						$(this).animate(
							{top: oTmp.containerSize.h}, 
							{queue:false,
							duration:this.basicAnimSpeed, 
							complete: function(){
									//on display none vu quil est hide
									$(this).css({'display':'none'});
									//state
									$(this).attr('showed', '0');
									}
								}
							);
						}
					}
				});
			this.showLayer(id);
			}	
		}	
		
		
	//----------------------------------------------------------------------------------------------------------------------
	//close program tool layers
	this.closeToolsLayerSettings = function(){
		this.debug('closeToolsLayerSettings()');
		//
		this.showListingProgramsTitle(true);
		//close only layer settings
		$('#layer-settings').animate(
			{top:this.containerSize.h}, 
			{queue:false,
			//easing:"easeInQuad",
			duration:this.basicAnimSpeed, 
			complete: function(){
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						$(this).attr('showed', '0');
						oTmp.changeZooming(false);
						oTmp.changeProgramListingBottom($('#layer-settings').position().top, true);
						}
					},	
			progress: function(){
					//get parent class
					var oTmp = $(document).data('jappzclass');
					if(typeof(oTmp) == 'object'){
						//resize #listing-programs
						oTmp.changeProgramListingBottom($('#layer-settings').position().top, false);
						}
					},		
				}
			);
		}

	
			
		
	//----------------------------------------------------------------------------------------------------------------------
	this.changeProgramListingBottom = function(iHeight, bClose){
		//#listing-programs : 10px padding-bottom in style
		//#main-menu-bottom : 60px height in style
		//#programs-tools : 47px height in style / 55px outterheight with padding and margin
		
		//invert from top sionon animation reverse height 
		if(bClose){
			$('#listing-programs').css({bottom: (this.containerSize.h - iHeight + $('#programs-tools').outerHeight(true)) + 'px'});
		}else{
			$('#listing-programs').css({bottom: (this.containerSize.h - iHeight) + 'px'});
			}
		}	
		

	//----------------------------------------------------------------------------------------------------------------------*
	this.openSettingsPopup = function(settingId, arrSettings, arrChanged){
		this.debug('openSettingsPopup(' + settingId + ', ' + arrSettings + ', ' + arrChanged + ')');
		var str = '';
		//check si deja quelque chose est ecrit dans le input avant le next ou prev
		var strInputValue = '';
		if(typeof(arrChanged[settingId]) == 'string'){
			strInputValue = arrChanged[settingId];
			}
		//save or cancel action
		str += '<div class="popup-tools">';
		str += '<div>';
		str += '<a href="#" class="butt med" id="butt-save-settings-modification">' + jLang.t('save settings') + '</a>';
		str += '<a href="#" class="butt med" id="butt-cancel-settings-modification">' + jLang.t('cancel') + '</a>';
		str += '</div>';
		str += '</div>';
		//
		str += '<div id="popup-content">';
		//le nom du titre correspond au nom du setting sur lequel il a clique ou quand il fait un next ou prev d'un setting a lautre
		str += '<h1 class="h1-close-popup settings">' + arrSettings[settingId].longtitle + '</h1>';
		str += '<div class="popup-form">';
		//page content
		//input
		str += '<p style="margin-top: 5px;"><input type="text" id="popup-settings-input-1" value="' + this.jutils.javascriptFormat(strInputValue) + '" class="input-1 large"></p>';
		str += '</div>'; //close popup-form
		//prev et next button
		str += '<div class="div-sty-21">';
		str += '<a href="#" class="butt small" id="butt-previous-settings">' + jLang.t('previous') + '</a>';
		str += '&nbsp;<a href="#" class="butt small" id="butt-next-settings">' + jLang.t('next') + ' </a>';
		str += '</div>';
		str += '<div class="div-sty-22">';
		str += '<a href="#" class="butt small" id="butt-cleartext-settings">' + jLang.t('clear') + '</a>';
		str += '</div>';
		str += '</div>'; //close popup-content
		//popup
		$('#main-popup-window').html(str);
		this.openPopup();
		//mettre le focus sur le input text	
		$('#popup-settings-input-1').focus();
		//datat ref settings
		$('#butt-save-settings-modification, #butt-previous-settings, #butt-next-settings').data('arrchanged', arrChanged);		
		$('#butt-save-settings-modification, #butt-previous-settings, #butt-next-settings').data('arrsettings', arrSettings);	
		$('#butt-save-settings-modification, #butt-previous-settings, #butt-next-settings').data('currentsetting', settingId);	
		//save
		$('#butt-save-settings-modification').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//save setting form popup
				oTmp.saveSettingsFromPopup($(this).data('currentsetting'), $(this).data('arrchanged'));
				}
			});
		//cancel
		$('#butt-cancel-settings-modification').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//close popup
				oTmp.closePopup();
				}
			});	
		//next	
		$('#butt-next-settings').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.checkForSettingsPopupTextBoxChanges('next', $(this).data('currentsetting'), $(this).data('arrsettings'), $(this).data('arrchanged'));
				}
			});	
		//previous	
		$('#butt-previous-settings').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				oTmp.checkForSettingsPopupTextBoxChanges('previous', $(this).data('currentsetting'), $(this).data('arrsettings'), $(this).data('arrchanged'));
				}
			});
		//clear current text input	
		$('#butt-cleartext-settings').click(function(e){
			e.preventDefault();
			//clear the text
			$('#popup-settings-input-1').val('');
			});
			
		}

	
	//----------------------------------------------------------------------------------------------------------------------
	this.checkForSettingsPopupTextBoxChanges = function(direction, id, arrSettings, arrChanged){
		this.debug('checkForSettingsPopupTextBoxChanges(' + direction + ', ' + id + ', ' + arrSettings + ', ' + arrChanged + ')');	
		//keep track of the changed setting in the textbox:popup-settings-input-1
		var newValue = $('#popup-settings-input-1').val();
		if(newValue != ''){
			arrChanged[id] = newValue;
		}else{
			//si vide alors on efface
			if(typeof(arrChanged[id]) == 'string'){
				delete(arrChanged[id]);
				}
			}
		//go to next or previous setting
		var newId;
		if(direction == 'next'){
			newId = this.jsettingmanager.getNextSettingId(id); 
		}else{
			newId = this.jsettingmanager.getPreviousSettingId(id); 
			}
		//will rewrite the content
		this.openSettingsPopup(newId, arrSettings, arrChanged);
		}


	//----------------------------------------------------------------------------------------------------------------------
	this.saveSettingsFromPopup = function(id, arrChanged){
		this.debug('saveSettingsFromPopup(' + id + ')', arrChanged );
		//keep track of the changed setting in the textbox:popup-settings-input-1
		var newValue = $('#popup-settings-input-1').val();
		if(newValue != ''){
			arrChanged[id] = newValue;
		}else{
			//si vide alors on efface
			if(typeof(arrChanged[id]) == 'string'){
				delete(arrChanged[id]);
				}
			}
		//on call le setting manager
		this.jsettingmanager.saveModeTextSettings(arrChanged);
		//on ferme le popup
		this.closePopup();
		}
		
		
	//----------------------------------------------------------------------------------------------------------------------
	//display les box image en content-view comme dans explorateur de windows
	this.displayProgramBoxesHasContentView = function(){
		this.debug('displayProgramBoxesHasContentView()');	
		//si c'est le last indexx alors c'est le special cotent-view
		var iZoomId = parseInt($('#butt-zoom-in').attr('zoom-id'));
		if(this.contentViewDisplay && iZoomId == -1){
			for(var o in this.jprogram.arrExercices){
				this.displaySingleProgramBoxHasContentView(true, this.jprogram.arrExercices[o]);
				}
		}else{
			for(var o in this.jprogram.arrExercices){
				this.displaySingleProgramBoxHasContentView(false, this.jprogram.arrExercices[o]);
				}
			}
		}

	//----------------------------------------------------------------------------------------------------------------------
	//display un box image en content-view comme dans explorateur de windows
	this.displaySingleProgramBoxHasContentView = function(bContentView, oExercice){
		//le jclientmanager va nous ramener la langue de l'application gLocaleLang
		//si il n'y a pas de client dans le programme ou si il ne trouve pas la langue
		var strLang = this.jclientmanager.getClientLocale(this.jprogram.getClientId());
		//selon la view
		if(bContentView){	
			//change le box style pour plein largeur
			$('#' + oExercice.getBoxName()).css({'width':this.boxSettings.prctw, 'height':this.boxSettings.h, 'margin-bottom':this.boxSettings.mrgbott + 'px'});
			//mofifier le div qui contient l'image si on est en  zoomFactorLastIndex = 0, le max ne doit pas depasser 90px
			$('#' + oExercice.getBoxName() + ' > .div-sty-23').css({'width':this.boxSettings.w, 'height':this.boxSettings.h});
			//on modifie la section colore
			$('#' + oExercice.getBoxName() + ' > .div-sty-23 > .mask-img').addClass('full');
			//on change le style du text dans le div
			$('#' + oExercice.getBoxName() + ' > .box-img-text').addClass('content-view');
			//on le display c,est certain
			$('#' + oExercice.getBoxName() + ' > .box-img-text').css({'display':'block'});
			//on remplace le texte le contenu
			//title
			var strInfos = '<div style="font-weight:bold;margin-bottom:8px;">' + oExercice.getTitleForProgramListing(strLang) + '</div>';
			//show all active settings
			var arrSettings = oExercice.getSettings();
			var arrSettingsShowed = [];
			for(var p in arrSettings){
				if(arrSettings[p] != ''){
					arrSettingsShowed[p] = arrSettings[p];
					}
				}
			//tables
			var iRows = 0;
			var strTable = '';
			var bTableOpened = false;
			for(var p in arrSettingsShowed){
				if(!bTableOpened && iRows%4 === 0){
					strTable += '<div class="table" style="position:relative;float:left;width:50%;margin:0;">';
					bTableOpened = true;
					}
				strTable += '<div class="row2">';
				strTable += '<div class="cell title">' + jLang.t(p + '=') + ': </div>';
				strTable += '<div class="cell">' + arrSettingsShowed[p] + '</div>';
				strTable += '</div>'; //close row
				iRows++;
				if(iRows%4 === 0 && bTableOpened){
					strTable += '</div>'; //close table
					bTableOpened = false;
					}
				}
			if(iRows){
				strInfos += strTable;
				}
			//on construit le html	
			var str = '<div style="margin:0;padding:5px 10px;overflow:auto;height:' + (this.boxSettings.minH - 10) + 'px">' + strInfos + '</div>'; //10 = 2Xpadding
			//on remplace le html	
			$('#' + oExercice.getBoxName() + ' > .box-img-text').html(str);

		}else{
			//change le box style
			$('#' + oExercice.getBoxName()).css({'width':this.boxSettings.w, 'height':this.boxSettings.h, 'margin-bottom': this.boxSettings.mrgbott + 'px'});
			//mofifier le div qui contient l'image
			$('#' + oExercice.getBoxName() + ' > .div-sty-23').css({'width':'100%', 'height':'100%'});
			//on modifie la section colore
			$('#' + oExercice.getBoxName() + ' > .div-sty-23 > .mask-img').removeClass('full');
			//on remove le style du text dans le div
			$('#' + oExercice.getBoxName() + ' > .box-img-text').removeClass('content-view');
			//display 
			if(this.boxSettings.t){
				//on remplace le html	
				$('#' + oExercice.getBoxName() + ' > .box-img-text').html(oExercice.getTitleForProgramListing(strLang));
				$('#' + oExercice.getBoxName() + ' > .box-img-text').css({'display':'block'});
			}else{
				$('#' + oExercice.getBoxName() + ' > .box-img-text').css({'display':'none'});
				}
			}

		//le flip et mirror des images
		if(oExercice.getMirror() === 1){
			$('#' + oExercice.getBoxName() + ' > .div-sty-23 > .mask-img > IMG').addClass('mirror');
		}else{
			$('#' + oExercice.getBoxName() + ' > .div-sty-23 > .mask-img > IMG').removeClass('mirror');
			}

				
		//event sur les images
		this.fixImage();

		}


	//----------------------------------------------------------------------------------------------------------------------
	//display les box image en content-view comme dans explorateur de windows
	this.displaySearchBoxesHasContentView = function(){
		this.debug('displaySearchBoxesHasContentView()');	

		//on va essayer avec un thread car si la loop depasse 500 ca freeze le browser
		var iZoomId = parseInt($('#butt-zoom-in').attr('zoom-id'));
		//check si deja un thread qui roule
		if(typeof(this.thDisplayHasContentViewLoop) == 'object'){
			this.thDisplayHasContentViewLoop.kill();
			}
		this.thDisplayHasContentViewLoop = new JThread('displaySearchBoxesHasContentViewThreadLoop', this, 1, {parent:this, arrexerciceskeys:this.jsearch.getArrExercicesKeyIndex(), counter:0});
		//
		this.thDisplayHasContentViewLoop.start();

		}


	//----------------------------------------------------------------------------------------------------------------------
	//called by thread	
	this.displaySearchBoxesHasContentViewThreadLoop = function(obj){
		this.debug('displaySearchBoxesHasContentViewThreadLoop(' + obj + ')');
		
		var cmpt = obj.counter;
		var cmptMax = cmpt + 20;
		if(cmptMax > obj.arrexerciceskeys.length){
			cmptMax = obj.arrexerciceskeys.length;
			}
		for(var i=cmpt;i<cmptMax;i++){
			obj.parent.displaySingleSearchBoxHasContentView(obj.parent.jsearch.arrExercices[obj.arrexerciceskeys[i]]);
			}
		//check si a la fin
		if(cmptMax == obj.arrexerciceskeys.length){
			obj.parent.fixImage();
			return false; //on arrete la loop
			}
		//refresh le counter for the next loop call
		obj.counter = cmptMax;
		//on ctinue la loop
		return true;
		}




	//----------------------------------------------------------------------------------------------------------------------
	//display un box image en content-view comme dans explorateur de windows
	this.displaySingleSearchBoxHasContentView = function(oExercice){
		//for the search we are going to keep the same size of boxSetting not fallowing the iZoom from programs layer
		if(typeof(oExercice) == 'object'){
			//selon la view, pour le serach on ne mettra pas le details view juste une sorte par defaut
			$('#' + oExercice.getBoxName()).css({'width':this.zoomFactor[this.zoomFactorSearchIndex].w, 'height':this.zoomFactor[this.zoomFactorSearchIndex].h, 'margin-bottom': this.zoomFactor[this.zoomFactorSearchIndex].mrgbott + 'px'});
			//mofifier le div qui contient l'image
			$('#' + oExercice.getBoxName() + ' > .div-sty-23').css({'width':'100%', 'height':'100%'});
			//on remplace le html	
			$('#' + oExercice.getBoxName() + ' > .box-img-text').html(oExercice.getTitleForSearchListing());
			//le flip et mirror des images
			if(oExercice.getMirror() === 1){
				$('#' + oExercice.getBoxName() + ' > .div-sty-23 > IMG').addClass('mirror');
			}else{
				$('#' + oExercice.getBoxName() + ' > .div-sty-23 > IMG').removeClass('mirror');
				}
			}
	
		}

	//----------------------------------------------------------------------------------------------------------------------
	//reset all data and windows like the excit on v3 web version of the site
	this.resetAllDataAndDisplay = function(){
		this.debug('resetAllDataAndDisplay()');	
		//clear tout dans les programmes
		this.jprogram.clear();
		this.jprogram.clearClient();
		this.jprogram.clearProgram();
		//clear tout du client search
		this.jclientmanager.clearAllClients();
		//reset les settings
		this.jsettingmanager.clear();
		//clear les resultat de serach
		this.jsearch.clear();
		//les template
		this.jtemplate.clear();
		//reset top client
		this.setMainClientName();
		//diskette
		this.changeProgramSaveState(true);
		//les popups
		this.closeAlert();	
		this.closePopup();
		this.closePopupCaroussel();
		//reset affichage de la fenetre client, program et exercice
		this.resetClientSearchWindow();
		this.resetSearchWindow();
		this.resetProgramWindow();
		//on retourne al a fenetre des client
		this.openLayer('client');	
		
		}

	
	//----------------------------------------------------------------------------------------------------------------------
	//confirm on rest data when not saved
	this.comfirmResetDataAndDisplay = function(){
		this.debug('comfirmResetDataAndDisplay()');	
		//besoin d'un poup de confirmation avant de quitter car peut s'accrocher dans le bouton facilement
		if(!this.jprogram.getSaved()){
			//str de base
			var str = jLang.t('some data will be lost, are you sure you want to exit?');
			//open the comfirm box avec en retour le nom du save-butt
			var arrButts = this.openAlert('comfirm', jLang.t('exit program'), str, false);
			//set action on butt save once it's writed
			$('#' + arrButts[0]).click(function(e){
				e.preventDefault(); 
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					oTmp.resetAllDataAndDisplay();
					}
				});
			
		}else{
			this.resetAllDataAndDisplay();
			}
		
		}


	//----------------------------------------------------------------------------------------------------------------------
	//quand clique on the counter put window at the top with a thread
	this.scrollToTop = function(el){
		this.debug('scrollToTop(' + el  + ')');	
		var th = new JThread('scrollToTopThread', this, 33, {parent:this, el:el});
		th.start();
		}


	//----------------------------------------------------------------------------------------------------------------------
	//thread to put window on top
	this.scrollToTopThread = function(obj){
		//this.debug('scrollToTopThread(' + obj  + ')');	
		//check la hauteur du scroll
		var w = $(obj.el);
		var y = parseInt(w.scrollTop() / 2);
		//on load le contenu dans le bas de la page serach result
		w.scrollTop(y);
		if(y > 0){
			return true;
			}
				
		return false;
		}

	//----------------------------------------------------------------------------------------------------------------------
	//switch image between img-a and img-b if there are 2 images
	this.flipExerciceImage = function(id, strType){
		this.debug('flipExerciceImage(' + id + ', ' +  strType + ')');	

		var item = '#items-' + id;

		//check if we have 2 images before img-a and img-b	
		if($(item + ' .img-b').length > 0){
			var a = item + ' .img-a';
			var b = item + ' .img-b';
			
			//on change le float des 2
			if($(a).hasClass('left')){
				//img-a
				$(a).removeClass('left');
				$(a).addClass('right');
				//img-b
				$(b).removeClass('right');
				$(b).addClass('left');
			}else{
				//img-a
				$(a).removeClass('right');
				$(a).addClass('left');
				//img-b
				$(b).removeClass('left');
				$(b).addClass('right');
				}
			
			//change the flag
			if($(item).attr('flip-on') == '0'){
				$(item).attr('flip-on', 1);
			}else{
				$(item).attr('flip-on', 0);
				}
			
			if(strType == 'programs'){
				this.changeProgramCarousselSaveButtStateOnInputChange(1, id);
				this.saveProgramCarousselInputModifications(id);
			}else{ // search
				//set exercice to flip choice if the set has my instrcution is there
				if(this.bSetHasMyInstruction){
					var oExercice = this.jsearch.getExerciceById(id);
					if(typeof(oExercice) == 'object'){
						oExercice.setFlip(parseInt($(item).attr('flip-on')));					
						}
					}
				//save the modifs
				this.saveSearchCarousselInputModifications(id);
				}


	
			}
		}

	//----------------------------------------------------------------------------------------------------------------------
	//mirror image on img-a and img-b if both or single only
	this.mirrorExerciceImage = function(id, strType){
		this.debug('mirrorExerciceImage(' + id + ', ' + strType + ')');	

		//IMPORTANT: TODO change the data in the database and locally

		var item = '#items-' + id;
		var a = item + ' .img-a';
		var b = item + ' .img-b';

		//check if we have images before img-a and img-b	
		if($(a).length > 0){
			if($(a).hasClass('mirror')){
				$(a).removeClass('mirror');
			}else{
				$(a).addClass('mirror');
				}
			}
		
		//check if we have images before img-a and img-b	
		if($(b).length > 0){
			if($(b).hasClass('mirror')){
				$(b).removeClass('mirror');
			}else{
				$(b).addClass('mirror');
				}
			}

		//change the flag
		if($(item).attr('mirror-on') == '0'){
			$(item).attr('mirror-on', 1);
		}else{
			$(item).attr('mirror-on', 0);
			}

			
		if(strType == 'programs'){
			this.changeProgramCarousselSaveButtStateOnInputChange(1, id);
			this.saveProgramCarousselInputModifications(id);
		}else{ // search
			//set exercice to flip choice if the set has my instrcution is there
			if(this.bSetHasMyInstruction){
				var oExercice = this.jsearch.getExerciceById(id);
				if(typeof(oExercice) == 'object'){
					oExercice.setMirror(parseInt($(item).attr('mirror-on')));	
					//on change le listing aussi pour la photo mirror (pas pour le flip visuellement ca ne vaut pas le coup)
							
					}
				}
			//save the modifs
			this.saveSearchCarousselInputModifications(id);
			}


		}	

	//----------------------------------------------------------------------------------------------------------------------
	//open le video player
	this.openExerciceVideo = function(id, strFrom){
		this.debug('openExerciceVideo(' + id + ', ' + strFrom + ')');	
			
		var oExercice;
		//on va chrecher lew nom de lexercice dans le search
		if(strFrom == 'search'){
			oExercice = this.jsearch.getExerciceById(id);
		}else if(strFrom == 'program'){
			oExercice = this.jprogram.getExerciceById(id);	
			}
		if(typeof(oExercice) == 'object'){
			var str = '';
			//on va chercher le code de l,exercice
			/*
			if(oExercice.getVideo() !== false){
				str = '<iframe class="sproutvideo-player" src="//' + oExercice.getVideo() + '?type=hd" width="100%" height="98%" frameborder="0" allowfullscreen></iframe>';
			}else{
				str = '<p>' + jLang.t('sorry! video is not available') + '</p>';
				}
			*/
			if(oExercice.getVideoType() === false){
				str = '<p>' + jLang.t('sorry! video is not available') + '</p>';
			}else{
				if(oExercice.getVideoType() == 'sprout'){
					str = '<iframe class="sproutvideo-player" src="//videos.sproutvideo.com/' + oExercice.getVideo() + '?type=hd" width="100%" height="98%" frameborder="0" allowfullscreen></iframe>';
				}else{
					//methoded fliqz avec des mp4
					//str = '<div id="videodiv" style="width:100%"></div><script type="text/javascript" src="//services.fliqz.com/smart/20100401/applications/e9be3ff0699547dc825bd262261fbf91/assets/' + oExercice.getVideo() + '/containers/videodiv/smarttag.js?width=100%25&amp;height=100%25"></script>';
					}
				
				}
			var strTitle = oExercice.getCode();
			//alert
			this.openAlert('video', strTitle, str, false);
			}
		
		};

	//----------------------------------------------------------------------------------------------------------------------
	//message comme quoi il faut reloader l'application 
	this.reloadApplication = function(strMsg, strLocaleLang){
		this.debug('reloadApplication(' + strMsg + ', ' + strLocaleLang + ')');	
		
		var str = strMsg;
		//open the comfirm box avec en retour le nom du save-butt
		var arrButts = this.openAlert('comfirm', jLang.t('reload'), str, false);
		//datat passed
		var strButtYes = '#' + arrButts[0];
		var strButtNo = '#' + arrButts[1];
		//
		$(strButtYes).data('localelang', strLocaleLang);
		$(strButtYes).click(function(e){
			e.preventDefault(); 
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//on met un loader sur le bouton OK
				oTmp.showLoader(true, '#' + $(this).attr('id'), 0, 0);
				//on reload avec les infos de base
				var strLink = gServerPath + '?';
				//check for the debug
				if(gDebug != '0'){
					strLink += '&dwizzel=' + gDebug;	
					}
				strLink += '&lang=' + $(this).data('localelang');
				strLink += '&brand=' + gBrand;	
				strLink += '&style=' + gStyle;	
				strLink += '&is_appz=' + gIsAppz;	
				strLink += '&os_type=' + gOsType;	
				strLink += '&PHPSESSID=' + gSessionId;	
				window.location.href = strLink;
				}
			});

		};

	//----------------------------------------------------------------------------------------------------------------------*
	//deconnecte le client
	this.doLogout = function(){
		this.debug('doLogout()');

		this.juser.doLogout();
		//on set la sess de juser
		this.juser.setSessionId(0);
		//la globale session
		gSessionId = this.juser.getSessionId();
		//on reload l'application au compleyt avec un delai le temps de clear la session
		setTimeout(function(){
				//init local appz
				var strLink = gServerPath + '?';
				//check for the debug
				if(gDebug != '0'){
					strLink += '&dwizzel=' + gDebug;	
					}
				strLink += '&lang=' + gLocaleLang;
				strLink += '&brand=' + gBrand;
				strLink += '&style=' + gStyle;	
				strLink += '&is_appz=' + gIsAppz;	
				strLink += '&os_type=' + gOsType;		
				strLink += '&PHPSESSID=' + gSessionId;
				window.location.href = strLink;
				},500);	
				
		};


	//----------------------------------------------------------------------------------------------------------------------*
	//show le login form
	this.showLogin = function(){
		this.debug('showLogin()');	

		var strTestUserName = '';
		var strTestUserPsw = '';

		//on va mettre un formulaire sur la page de loader dans un div prevu pour ca
		var str = '';
		str += '<div>';
		str += '<p><input autocomplete="off" autocorrect="off" autocapitalize="none" type="text" spellcheck="false" placeholder="' + jLang.t('username') + '" value="' + strTestUserName + '" class="input-1 large" /></p>';
		str += '<p><input autocomplete="off" autocorrect="off" autocapitalize="none" type="password" spellcheck="false" placeholder="' + jLang.t('password') + '" value="' + strTestUserPsw + '" class="input-1 large" /></p>';
		str += '</div>';
		str += '<div style="padding:20px;"><a href="#" class="butt alert login" id="butt-login">' + jLang.t('login') + '</a></div>';
		
		//on ecrit	
		$('#main-login').html(str);	
		
		//action sur le login
		$('#butt-login').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//loader
				oTmp.showLoader(true, '#butt-login', 0, 0);
				//login
				oTmp.doLogin();
				}
			});

		$('#main-login INPUT[type=text], #main-login INPUT[type=password]').keyup(function(e){
			if(e.which == '13'){
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					//loader
					oTmp.showLoader(true, '#butt-login', 0, 0);
					//login
					oTmp.doLogin();
					}
				}
			});


		
		//on desopaque
		$('#main-login').fadeIn();		
				
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//connecte le client
	this.doLogin = function(){
		this.debug('doLogin()');	

		var bError = false;	
		var elUsername = $('#main-login INPUT[type=text]');
		var elPassword = $('#main-login INPUT[type=password]');

		//on remove les errurs
		elUsername.removeClass('error');
		elPassword.removeClass('error');

		//on va chercher les infos du formulaire
		var strUsername = elUsername.val();	
		var strPassword = elPassword.val();
		
		//check si est vide
		if(strUsername == ''){
			elUsername.addClass('error');
			bError = true;
			}
		if(strPassword == ''){
			elPassword.addClass('error');
			bError = true;
			}

		//si pas erreur alors on call le service et on disable le bouton
		if(!bError){
			//call the servive
			this.juser.doLogin(strUsername, strPassword);
		}else{
			//on enleve le loader
			this.removeLoader('#butt-login', jLang.t('login'));	
			}
		
		};	


	//----------------------------------------------------------------------------------------------------------------------*
	this.doLoginReturnFromServer = function(obj, extraObj){
		this.debug('doLoginReturnFromServer(' + obj + ', ' + extraObj + ')');	

		var bError = false;
		var strErrorMessage = '';

		//check si on a un object
		if(typeof(obj) === 'object'){
			//check si on a des erreurs
			if(obj.error !== 0){
				bError = true;
				strErrorMessage = obj.errormessage;
			}else{
				//on set la sess de juser
				this.juser.setSessionId(obj.sessid);	
				this.juser.setId(obj.id);	
				this.juser.setUserName(extraObj.username);
				//this.juser.setPsw(extraObj.password);
				this.juser.setPsw('');
				this.juser.setArrLang(obj.lang);
				this.juser.setModuleId(obj.moduleid);
				//la sessions global
				gSessionId = this.juser.getSessionId();
				}
			}

		//
		if(bError){
			//msg erreur a usager
			this.openAlert('alert', jLang.t('login'), strErrorMessage, false);
			//on remove le loader	du bouton
			this.removeLoader('#butt-login', jLang.t('login'));
		
		}else{
			//pas erreur alors on load le reste de application 
			this.showApplication(false);
			}

			
		};


	//----------------------------------------------------------------------------------------------------------------------*
	this.getBasicsInfosReturnFromServer= function(obj, extraObj){
		this.debug('getBasicsInfosReturnFromServer(' + obj + ', ' + extraObj + ')');	

		//check si on a un object
		if(typeof(obj) === 'object'){
			//on set la sess de juser
			this.juser.setSessionId(gSessionId);	
			this.juser.setId(obj.userid);	
			this.juser.setUserName(obj.username);
			this.juser.setPsw('');
			this.juser.setArrLang(obj.lang);
			this.juser.setModuleId(obj.moduleid);
			}

		//show the application
		this.showApplication(true);
		
		};


	//----------------------------------------------------------------------------------------------------------------------*
	this.showApplication = function(bTimeout){
		this.debug('showApplication(' + bTimeout + ')');

		//call le serveur pour les templates //timout because too fast
		this.jsearch.getSearchTemplates();	

		//va chercher les modules selon la clinique
		this.jsearch.getSearchModules();
				
		//call the save automatic //timout because too fast
		this.jprogram.startAutomaticProgramSave();	

		//ping service to keep sessId alive or kick and relogin if not good anymore
		//important remettre le pingService
		this.juser.pingService();
						
		//hide init layer
		if(bTimeout){
			//quand vient avec un session id deja dans le url
			//car laisser un delai pour dessiner l'interface
			setTimeout(function(){
				//init local appz
				$('#main-loader').fadeOut(this.basicAnimSpeed);
				},1000);	
		}else{
			//quand vient du doLogin
			//on efface le formulaire une fois celui-ci disparu pour ne pas garder d'infos sensible
			$('#main-loader').fadeOut(this.basicAnimSpeed, function(){
				//clean le form pour pas avoir info qui traine
				$('#main-login').html('');		
				});
			}

		};

	
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