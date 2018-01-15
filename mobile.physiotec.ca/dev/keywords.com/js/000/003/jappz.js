/*

Author: DwiZZel
Date: 15-07-2016
Version: V.1.0 BUILD 001
Notes: Application Main
*/

//----------------------------------------------------------------------------------------------------------------------

function JAppz(){

	this.className = 'JAppz';
	this.version = 'V.1.300';
	this.title;	
	
	//on set une reference qui sera utilise partout pour le scope des classes
	$(document).data('jappzclass', this);

	//args with other classes
	this.jutils = new JUtils(); //no class dependencies;
	this.jcomm = new JComm({'mainappz':this}); //dependencies
	this.jsearch = new JSearch({'mainappz':this, 'jcomm':this.jcomm}); //dependencies
	this.jautocomplete = new JAutoComplete({'mainappz':this, 'jcomm':this.jcomm}); //dependencies	
	
	//container size
	this.containerSize = {
		h:0, 
		w:0
		};	
		
	//conteneur principal
	this.mainContainer = 'BODY';	
	//----------------------------------------------------------------------------------------------------------------------
	this.init = function(obj){
		this.debug('init()', obj);	
		//set le title
		if(typeof(obj) == 'object'){
			//le title
			if(typeof(obj.title) == 'string'){
				this.setTitle(obj.title);
				}
			}
		//container size
		this.containerSize = {
			w: $(this.mainContainer).innerWidth(),
			h: $(this.mainContainer).innerHeight(),
			}		
		//loaded needed images if offline
		this.loadNeededImage();
		//resize event
		$(window).resize(this.resizeAllElements.bind(this));	
		//creer l'interface visuel de base
		this.createBasicVisualInterface();
		//creer le input box pour la recherche
		this.createAutoCompleteBox();
		//call le resize pour ajuster au screen
		this.resizeAllElements();	
		};

	//----------------------------------------------------------------------------------------------------------------------
	this.setTitle = function(str){
		this.debug('setTitle()', str);	
		//change browser title tab
		this.title = str;
		document.title = this.title;
		};

	//----------------------------------------------------------------------------------------------------------------------	
	this.resizeAllElements = function(){ 
		this.debug('resizeAllElements()');

		// moins la scrollbar quand pas en mode mobile	
		this.containerSize = {
			w: $(this.mainContainer).innerWidth(),
			h: $(this.mainContainer).innerHeight(),
			};		
		
		//resize resizable class based	
		$('.resizable').each(function(){
			//get parent class
			var oTmp = $(document).data('jappzclass');
			if(typeof(oTmp) == 'object'){
				//on va chercher le padding a soustraire
				var iPaddingRight = parseInt($(this).css('padding-right'));
				//on applique le w - le double padding	
				$(this).css({'width': (oTmp.containerSize.w - (2 * iPaddingRight)) + 'px'});
				}
			});	
		};
	//----------------------------------------------------------------------------------------------------------------------
	this.createBasicVisualInterface = function(){
		this.debug('createBasicVisualInterface()');	
		//aussi on va rajouter les case a cocher c'est a dire les kwtype: 1=keyword,2=title,3=code
		var strKwType = '';
		//keyword
		//strKwType += '<div class="kwtype"><label class="type"><input id="kwtype0" name="kwtype[]" type="checkbox" checked value="1" ><div>' + jLang.t('keywords') + '</div></label></div>';
		//title
		//strKwType += '<div class="kwtype"><label class="type"><input id="kwtype1" name="kwtype[]" type="checkbox" value="2" ><div>' + jLang.t('short title') + '</div></label></div>';
		//code exercice
		//strKwType += '<div class="kwtype"><label class="type"><input id="kwtype2" name="kwtype[]" type="checkbox" value="3" ><div>' + jLang.t('code exercise') + '</div></label></div>';

		//on va creer le main container et le main content
		var str = '';
		str += '<div id="main-container" class="resizable">';
		str += '	<div id="main-content" class="resizable">';
		//str += '		<H1>' + this.title + '</H1>';
		str += '		<div id="main-input"></div>';
		str += '		<div id="main-kwtype">' + strKwType + '</div>';
		str += '		<div id="main-result"><h3>' + jLang.t('result') + '</h3></div>';
		str += '	</div>';
		str += '</div>';
		
		//on output dans le body
		$(this.mainContainer).html(str);
		};

	//----------------------------------------------------------------------------------------------------------------------*
	//init the autocomplete serach fields
	this.createAutoCompleteBox = function(){
		this.debug('createAutoCompleteBox()');
		//
		this.jautocomplete.init([
			{
				input:'input-search',
				layer: 'main-input',
				type:'exercice',
				position:'under',
				},
			]);
		}	

	//----------------------------------------------------------------------------------------------------------------------*
	//init the setting panel
	this.loadNeededImage = function(){
		this.debug('loadNeededImage()');	
		//all needed images if we disconnect from the web still need them to continue functionning
		var arrImages = [
			'icone-search.png',
			'icone-search-invert.png',
			'logo-black.png',
			'logo-white.png',
			'mobile-loading-3.png',
			'mobile-loading-w-3.png',
			];
		//load the missing images
		var img = [];
		for(var o in arrImages){
			img[o] = new Image();
			img[o].onload = function(){
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					oTmp.debug('loaded OK : ' + this.src);
					}
				};
			img[o].onerror = function(){
				//get parent class
				var oTmp = $(document).data('jappzclass');
				if(typeof(oTmp) == 'object'){
					oTmp.debug('loaded ERR : ' + this.src);
					}
				};
			img[o].src = gServerPathImages + arrImages[o];
			}
		};

	//----------------------------------------------------------------------------------------------------------------------*	
	this.fillSearch = function(oData, strWord){
		this.debug('fillSearch()', oData, strWord);
		if(typeof(oData.data) == 'object'){
			//on continue
			var data = oData.data;	
			var iShowMaxResult = parseInt(oData.maxcount);
			var str = '<H3>' + strWord + ' (' + iShowMaxResult + ')</H3>';
			str += '<UL class="exercises">';
			for(var o in data){
				//le LI
				str += '<LI class="single-exercises">';	
				//le image container	
				str += '<DIV class="img-container">';
				if(typeof(data[o].thumb) == 'string'){
					if(data[o].thumb != ''){
						str += '<img src="' + gExerciceImagePath + data[o].thumb + '">'
						}	
					}
				str += '</DIV>';		
				//le titre
				str += '<DIV class="text-container">';	
				if(data[o].shortTitle == ''){
					str += data[o].codeExercise;
				}else{
					str += data[o].shortTitle;
					}
				str += '</DIV>';			
				str += '</LI>';
				}
			//counter
			if(data.length < iShowMaxResult){
				str += '<LI class="single-exercises show-more">' + (iShowMaxResult - data.length) + ' more to show</LI>';
				}	
			//ferme le UL	
			str += '</UL>';
			}
		//sow result
		$('#main-result').html(str);
		};	

	//----------------------------------------------------------------------------------------------------------------------*
	this.resetSearchWindowResult = function(str){
		this.debug('resetSearchWindowResult()', str);
		//
		if(str == ''){
			$('#main-result').html('<h3>searching</h3>');
		}else{
			$('#main-result').html('<h3>searching for: ' + str + '</h3>');
			}
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



//CLASS END