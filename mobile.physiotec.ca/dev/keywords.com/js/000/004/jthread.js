/*
Author: 	DwiZZel
Date: 	18-11-2015
Version: 3.1.0 BUILD X.X
Notes: 	Thread for big loop or animations or whatever needed to be threated	
		
		Thread loop parameters when they are too big:
			n = function name
			l = parent of the method, can be a class or window
			p = loop call in millisecond
			pm = object with the data we are working with, since object is by reference we never loose the current data

		EXAMPLE: 
			//thread test class
			function TestThread(){
				//thread ref
				this.th;
				//big array
				this.arrBig = ['aaa','bbb', etc...];
				//function init class
				this.init = function(){
					//object passed to the thread
					var oData = {
						counter: 0, 		//loop counter
						arr: this.arrBig, //arr to loop in	
						parent: this, 	// is needed if we are going to call method of a class in the function called by Thread, because it will loose its scope
						};
					//create the thread
					this.th = new Thread('doBigLoop', this, 10, oData); 
					//start the thread
					this.th.start();	
					//stop the thread, we can restart it later and it will keep the current data from the stopped point
					this.th.stop();
					//change the timer a 50 milliseconds
					this.th.timer(50);
					//restart the thread
					this.th.start();
					//kill the thread will delete everything
					this.th.kill();	
					}
				//function called by the thread
				this.doBigLoop = function(obj){
					//max loop end point
					var iMaxLoop = obj.counter + 10;
					//if bigger then the array
					if(iMaxLoop > obj.arr.length){
						iMaxLoop = obj.arr.length;
						}
					//loop through the array
					for(var i=obj.counter; i<iMaxLoop; i++){
						obj.parent.showSomething(obj.arr[i]);
						}
					//we put back where the counter was
					obj.counter = iMaxLoop;
					//if we are at the end of the loop go away
					if(obj.counter == obj.arr.length){
						return false;
						}
					//if not then continue the loop in XX millisecnd depending on the parameters set when the thread was created
					rreturn true;
					}
				//call by the threated function
				this.showSomething = function(str){
					console.log(str);
					}
				};
			//
			var t = new TestThread();
			t.init();
			

*/

//----------------------------------------------------------------------------------------------------------------------
	
function JThread(n,l,p,pm){
	
	this.i=0;
	this.a=1;
	this.s=0;
	this.f=n;
	this.l=l;
	this.p=p;
	this.pm=pm;
	this.run=function(s){
		clearTimeout(s.i);
		if(s.a){
			var b=s.l[s.f](s.pm);
			if(b){
				s.i=setTimeout(
					function(){
						s.run(s)
						},s.p);
			}else{
				s.s=0;
				s.a=0;
				}
			}
		};
	this.start=function(){
		if(this.a){
			clearTimeout(this.i);
			this.run(this);
			this.s=1;
			}
		};
	this.stop=function(){
		if(this.a){
			clearTimeout(this.i);
			this.s=0;
			}
		};
	this.kill=function(){
		clearTimeout(this.i);
		this.p=0;
		this.f='';
		delete(this.l);
		delete(this.pm);
		delete(this);
		};
	this.timer=function(i){
		this.p=i; 
		};

	};
	