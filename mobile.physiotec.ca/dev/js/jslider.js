/*

Author: DwiZZel
Date: 04-09-2015
Version: 3.1.0 BUILD X.X
Notes:	

*/

//----------------------------------------------------------------------------------------------------------------------
    
function JSlider(){

	this.className = 'JSlider';
		
	//
	this.container = null;
	this.panes = null;
	this.width = 0;
	this.count = 0;
	this.hammer = null;
	this.currentPane = 0;
	this.lastPane = 0;
	this.callBackObj = null;
	
	//----------------------------------------------------------------------------------------------------------------------
	this.init = function(container, id){
		this.debug('init()');
		this.container = '#' + container;
		this.panes =  $(' > div', this.container);
		this.width = 0;
		this.count = this.panes.length;
        this.currentPane = 0;
		this.lastPane = this.currentPane;	
		//set max dimension have to put a resize event for this one
		this.setPaneDimensions();
		
		//set hammer action on event
		this.hammer = new Hammer.Manager(document.getElementById(container));
		this.hammer.add(new Hammer.Pan({direction:  Hammer.DIRECTION_HORIZONTAL, threshold: 0 }));	
		this.hammer.on('panstart panmove panend pancancel', this.handleEvent.bind(this));
		
		//where to open the panel
		this.showPane(id, false);	
		
		//resize event
		$(window).bind('load resize orientationchange', this.setPaneDimensions.bind(this));
		
		}
		
	
	//----------------------------------------------------------------------------------------------------------------------
	this.setCallBackObjOnPaneSelected = function(obj, extraObj){
		//this.debug('setCallBackObjOnPaneSelected()');
		//this.debug('setCallBackObjOnPaneSelected::obj', obj);
		//this.debug('setCallBackObjOnPaneSelected::extraObj', extraObj);
		this.callBackObj = {
			obj:obj, 
			extraobj:extraObj,
			};
		}
	
	
	//----------------------------------------------------------------------------------------------------------------------
	this.callBackObjOnPaneChange = function(){
		//this.debug('callBackObjOnPaneChange()');
		if(typeof(this.callBackObj) == 'object'){
			this.callBackObj.obj.callBackFromSlider(this.currentPane, this.lastPane, this.callBackObj.extraobj);
			}
		}	
	
	
	//----------------------------------------------------------------------------------------------------------------------
	this.setPaneDimensions = function(){
		//this.debug('setPaneDimensions()');
		//var w = $(this.container).width();
		var w = $('#main-popup-caroussel').width(); //use this intead of containe because bug on resize event
		
		//this.debug('setPaneDimensions.width = ' + w);
		//this.debug('setPaneDimensions.count = ' + this.count);	
		//this.debug('setPaneDimensions.container = ' + (w * this.count));	
		
		this.panes.each(function(){
			$(this).width(w);
			});
		this.width = w;	
		$(this.container).width(this.width * this.count);
		};	
	
	
	//----------------------------------------------------------------------------------------------------------------------	
	this.showPane = function(index, animate){
		//this.debug('showPane(' + index + ', ' + animate + ')');
		// between the bounds
		var index = Math.max(0, Math.min(index, this.count - 1));
		this.lastPane = this.currentPane;
		this.currentPane = index;
		var offset = -((100/this.count) * this.currentPane);
		this.setContainerOffset(offset, animate);
		//callback si il y a
		this.callBackObjOnPaneChange();
		};

		
	//----------------------------------------------------------------------------------------------------------------------	
	this.setContainerOffset = function(percent, animate){
		//this.debug('setContainerOffset(' + percent + ', ' + animate + ')');
		
		//percent = parseInt(percent);
		
		if($(this.container).hasClass('animate')){
			$(this.container).removeClass('animate');
			}
		if(animate){
			$(this.container).addClass('animate');
			}
			
		$(this.container).css('transform', 'translate(' + percent + '%,0)');	
			
		/*	
		if(typeof(Modernizr) == 'undefined'){
			//this.debug('setContainerOffset::Modernizr = NO');
			$(this.container).css('transform', 'translate(' + percent + '%,0)');
		}else{
			if(Modernizr.csstransforms3d){
				//this.debug('setContainerOffset::Modernizr = translate3d');
				$(this.container).css('transform', 'translate3d(' + percent + '%,0,0) scale3d(1,1,1)');
			}else if(Modernizr.csstransforms){
				//this.debug('setContainerOffset::Modernizr = translate');
				$(this.container).css('transform', 'translate(' + percent + '%,0)');
			}else{
				var px = ((this.width * this.count) / 100) * percent;
				$(this.container).css('left', px + 'px');
				}
			}
		*/
		}	

		
	//----------------------------------------------------------------------------------------------------------------------	
	this.next = function(){ 
		//this.debug('next()');
		return this.showPane(this.currentPane + 1, true); 
		};


	//----------------------------------------------------------------------------------------------------------------------
	this.prev = function(){ 
		//this.debug('prev()');
		return this.showPane(this.currentPane - 1, true); 
		};	
		
	
	//----------------------------------------------------------------------------------------------------------------------
	this.handleEvent = function(ev) {
		//this.debug('handleEvent(' + ev + ')');
		var delta = ev.deltaX;
		var percent = (100 / this.width) * delta;
				
		if(ev.type == 'panend' || ev.type == 'pancancel'){
			//if(Math.abs(delta) > this.width/5) {
			if(Math.abs(delta) > this.width/4) {
				if(delta < 0){
					this.next();
				}else{
					this.prev();
					}
			}else{
				this.showPane(this.currentPane, true);
				}
		}else if(ev.type == 'panmove'){
			var paneOffset = -(100/this.count) * this.currentPane;
			var dragOffset = ((100/this.width) * delta ) / this.count;
			// slow down when drag so it dont fallow exactly the speed of the finger
			if((this.currentPane == 0 && delta >= 0) || (this.currentPane == this.count - 1 && delta < 0)){
				dragOffset *= .25;
				}
			this.setContainerOffset(dragOffset + paneOffset, false);
		}else{
			//nothing
			}
		
		}

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

