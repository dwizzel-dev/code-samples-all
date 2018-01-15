/*

Author: DwiZZel
Date: 15-07-2016
Version: V.1.0 BUILD 001
Notes: Application Main
*/

//----------------------------------------------------------------------------------------------------------------------

function JAppz(args){

	this.className = 'JAppz';
	this.version = 'V.1.400';
	this.title;	
	
	//on set une reference qui sera utilise partout pour le scope des classes
	$(document).data('jappzclass', this);

	//args with other classes
	this.jutils = new JUtils(); //no class dependencies;
	this.jcomm = new JComm({
		mainappz:this
		}); //dependencies
	this.jsearch = new JSearch({
		mainappz:this, 
		jcomm:this.jcomm
		}); //dependencies
	this.jautocomplete = new JAutoComplete({
		mainappz:this, 
		basediv:args.basediv,
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
				type:'exercice',
				position:'under',
				},
			]);
		}	

	//----------------------------------------------------------------------------------------------------------------------*	
	this.fillSearch = function(oData, strWord){
		this.debug('fillSearch()', oData, strWord);
		if(typeof(oData.data) == 'object'){
			//on continue
			var data = oData.data;
			//le nom des filters	
			var oFilters = oData.filters;	
			//max result en tout
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
						str += '<img src="' + gExerciceImagePath + data[o].thumb + '">';
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
				//les filters si il y a
				var strFilter = '';
				if(typeof(data[o].filter) == 'object'){
					for(var p in data[o].filter){
						strFilter += oFilters[data[o].filter[p]] + ', ';
						}
					}
				//le title long	
				str += '<SPAN>';
				if(data[o].title != ''){
					str += '<BR />' + data[o].title;
					}
				//les filteers si il y a
				if(strFilter != ''){
					str += '<BR /><i>(' + strFilter.substr(0, (strFilter.length - 2)) + ')</i>';
					}
				str += '</SPAN>';
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
	this.resetSearchWindowResult = function(str, bById){
		this.debug('resetSearchWindowResult()', str);
		//
		if(str == ''){
			str = '<h3>searching</h3>';
		}else{
			if(bById){
				str = '<h3>searching [k-id] for: ' + str + '</h3>';
			}else{
				str = '<h3>searching [word] for: ' + str + '</h3>';
				}
			}
		//show
		$('#main-result').html(str);
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