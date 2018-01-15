/*

Author: DwiZZel
Date: 00-00-0000
Version: V.1.0 BUILD 001

*/

//----------------------------------------------------------------------------------------------------------------------

function JAppz(args){

	this.className = 'JAppz';
	this.version = 'V.1.001';
	this.title;	
	
	//on set une reference qui sera utilise partout pour le scope des classes
	$(document).data('jappzclass', this);

	this.jutils = new JUtils(); //no class dependencies;
	this.jcomm = null; //dependencies but will be local only for now
	this.jsearch = new JSearch({
		'mainappz':this, 
		'jcomm':this.jcomm
		}); //dependencies
	this.jautocomplete = new JAutoComplete({
		'mainappz':this, 
		'basediv':args.basediv,
		}); //dependencies	
	//container size
	this.containerSize = {
		h:0, 
		w:0
		};	
	//conteneur principal
	this.mainContainer = args.maincontainer;

	//----------------------------------------------------------------------------------------------------------------------
	this.init = function(){
		this.debug('init()');	
		//container size
		this.containerSize = {
			w: $(this.mainContainer).innerWidth(),
			h: $(this.mainContainer).innerHeight(),
			}		
		//resize event
		$(window).resize(this.resizeAllElements.bind(this));	
		//creer le input box pour la recherche
		this.createAutoCompleteBox();
		//call le resize pour ajuster au screen
		this.resizeAllElements();	
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

	//----------------------------------------------------------------------------------------------------------------------*
	//init the autocomplete serach fields
	this.createAutoCompleteBox = function(){
		this.debug('createAutoCompleteBox()');
		//
		this.jautocomplete.init([
			{
				input:'input-search',
				layer: 'main-input',
				position:'under',
				},
			]);
		}	

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