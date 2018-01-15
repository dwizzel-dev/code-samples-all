/*

Author: DwiZZel
Date: 14-09-2015
Version: V.3.2.0 BUILD X.X
Notes:	JSetting is instantiating this class for now
		
*/

//----------------------------------------------------------------------------------------------------------------------
    
function JTouchSpin(id, args){

	this.className = 'JTouchSpin';
	
	//the bar id: #setting-bar-* 
	this.parentId = id;

	//increment/decrement step
	this.currentStep = 1;
	this.stepMultiplier = 1;
	this.stepCounter = 0;

	//current value
	this.currentVal = args.val;

	//min, max, val
	this.defaultVal = {
		min: args.min,
		max: args.max,
		val: args.val,
		};

	//hammer to control pan press pressup etc...
	this.hammer = {
		down: null,
		up: null,
		};

	//event for the hammer on press et pressup
	this.hammerEvent = {
		press: 0,
		pressup: 0,
		};	

	//timer to increment with thre press on the hammer
	this.intervalPress;	
	
	//	
	this.draw = function(){
		var str = '';
		
		str += '<div class="spinner">';
			str += '<div class="down">';
				str += '<div class="text">-</div>';
			str += '</div>';
			str += '<div class="val">';
				str += '<div class="text">' + this.currentVal + '</div>';
			str += '</div>';
			str += '<div class="up">';
				str += '<div class="text">+</div>';
			str += '</div>';
			str += '<div class="hitdown"></div>';
			str += '<div class="hitup"></div>';
		str += '</div>';
		
		return str;
		}
	
	//
	this.initTouchSpin = function(){
		//action on click
		this.setDownAction();
		this.setUpAction();
		//hammer on press pressup
		this.addEventManager();
		}
	
	//
	this.setDownAction = function(){
		//adding this.class reference
		$('#' + this.parentId + ' > .component > .spinner > .hitdown').data('parentclass', this);
		//action on click
		$('#' + this.parentId + ' > .component > .spinner > .hitdown').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				oTmp.decrementNumber();
				}
			});
		}

	//
	this.setUpAction = function(){
		//adding this.class reference
		$('#' + this.parentId + ' > .component > .spinner > .hitup').data('parentclass', this);
		//action on click
		$('#' + this.parentId + ' > .component > .spinner > .hitup').click(function(e){
			e.preventDefault();
			//get parent class
			var oTmp = $(this).data('parentclass');
			if(typeof(oTmp) == 'object'){
				oTmp.incrementNumber();
				}
			});
		}	

	//
	this.changeTextVal = function(){
		$('#' + this.parentId + ' > .component > .spinner > .val > .text').text(this.currentVal);
		}

	//
	this.decrementNumber = function(){
		if((this.currentVal - (this.currentStep * this.stepMultiplier)) >= this.defaultVal.min){
			this.currentVal -= (this.currentStep * this.stepMultiplier);
		}else{
			this.currentVal = this.defaultVal.min;
			this.setPressTimer(false);
			}
		//change the val
		this.changeTextVal();	
		}

	//
	this.incrementNumber = function(){
		if((this.currentVal + (this.currentStep * this.stepMultiplier)) <= this.defaultVal.max){
			this.currentVal += (this.currentStep * this.stepMultiplier);
		}else{
			this.currentVal = this.defaultVal.max;
			this.setPressTimer(false);
			}
		//change the val
		this.changeTextVal();
		}

	//
	this.getCurrentVal = function(){
		return this.currentVal;
		}

	//process trigered event by the hammer class
	this.processHammerEventUp = function(ev){
		switch(ev.type){
			case 'press': 
				this.setPressTimer(true);
				this.onPressTimerUp();
				break;
			case 'pressup': 
				this.setPressTimer(false);
				break;
			default: 
				//
				break; 
			}
		}

	//process trigered event by the hammer class
	this.processHammerEventDown = function(ev){
		switch(ev.type){
			case 'press': 
				this.setPressTimer(true);
				this.onPressTimerDown();
				break;
			case 'pressup': 
				this.setPressTimer(false);
				break;
			default:
				//
				break; 
			}
		}


	//timer to increment depending on the duration the user has pressed on it
	this.onPressTimerUp = function(){
		//increment le step counter for acceleration
		this.stepCounter++;	
		//check to increment the multiplier
		if(this.stepCounter > 3){
			this.stepMultiplier++;
			this.stepCounter = 0;
			}
		//increase the main number
		this.incrementNumber();
		//recall the function
		if(this.intervalPress){
			setTimeout(this.onPressTimerUp.bind(this), 100);
			}
		}

		//timer to increment depending on the duration the user has pressed on it
	this.onPressTimerDown = function(){
		//increment le step counter for acceleration
		this.stepCounter++;	
		//check to increment the multiplier
		if(this.stepCounter > 3){
			this.stepMultiplier++;
			this.stepCounter = 0;
			}
		//increase the main number
		this.decrementNumber();
		//recall the function
		if(this.intervalPress){
			setTimeout(this.onPressTimerDown.bind(this), 100);
			}	
		}

	this.setPressTimer = function(bActive){
		//set it for the setTimeout
		this.intervalPress = bActive;
		//if non active we reset the step multiplier
		if(!bActive){
			this.stepMultiplier = 1;
			this.stepCounter = 0;
			}
		}


	//this one use the hammer press and pressup instead of mouseup and mousedown
	this.addEventManager = function(){
		//first the up button press/pressup
		var el = $('#' + this.parentId + ' > .component > .spinner > .hitup');
		if(typeof(el) == 'object'){
			el = el.get(0);
			if(typeof(el) == 'object'){
				//hammer instance
				this.hammer.up = new Hammer.Manager(el);
				//listener
				this.hammer.up.add(new Hammer.Press({event:'press', time:250, threshold:200}));
				//actions //Hammer utilities to pass this.class 
				this.hammer.up.on('press pressup', this.processHammerEventUp.bind(this));
				}
			}
		//first the down button press/pressup
		var el = $('#' + this.parentId + ' > .component > .spinner > .hitdown');
		if(typeof(el) == 'object'){
			el = el.get(0);
			if(typeof(el) == 'object'){
				//hammer instance
				this.hammer.down = new Hammer.Manager(el);
				//listener
				this.hammer.down.add(new Hammer.Press({event:'press', time:250, threshold:200}));
				//actions //Hammer utilities to pass this.class 
				this.hammer.down.on('press pressup', this.processHammerEventDown.bind(this));
				}
			}
		}
		
	//debug
	this.debug = function(str){
		console.log(this.className + '--------------------------------------------------------');	
		console.log(str);
		}
	
	}


//CLASS END
