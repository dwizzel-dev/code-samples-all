/*



*/

import flash.filters.GlowFilter;
import utils.CieThread;

dynamic class alert.CieAlertWindow{

	static private var __className = "CieAlertWindow";
	
	private var __arrMiniProfil:Array;
	private var __mv:MovieClip;
	private var __win:MovieClip;
	private var __winID:Number;
	private var __dockID:Number;
	private var __winDepth:Number;
	private var __x:Number;
	private var __y:Number;
	private var __newY:Number;
	private var __cbClass:Object;
	private var __cbFunc:Function;
	private var __cThreadRemoveWindow:CieThread;
	private var __cThreadAnimWindow:CieThread;
	private var __focus:Boolean;
	private var __strXml:String;
	
	/*******************************************************************************************************************************/	
		
	public function CieAlertWindow(mv:MovieClip, winID:Number, dockID:Number, winDepth:Number, x:Number, y:Number, strXml:String, cbClass:Object, cbFunc:Function){
		this.__focus = false;
		this.__mv = mv;
		this.__dockID = dockID;
		this.__winID = winID;
		this.__winDepth = winDepth;
		this.__x = x;
		this.__y = y;
		this.__newY = y;
		this.__cbClass = cbClass;
		this.__cbFunc = cbFunc;
		//load from xml
		this.createFromXml(strXml);
		//show the win and fill it
		this.createWindow();
		//start thread to show window
		this.__cThreadAnimWindow = cThreadManager.newThread(25, this, 'animWindowAlphaOn', {__supclass:this});
		//start thread for removing windows automatically
		if(this.__arrMiniProfil['ctype'] == 'newchat'){
			this.__cThreadRemoveWindow = cThreadManager.newThread(BC.__user.__windowsTimerChat, this, 'closeWindow', {__supclass:this});
		}else{
			this.__cThreadRemoveWindow = cThreadManager.newThread(BC.__user.__windowsTimer, this, 'closeWindow', {__supclass:this});
			}
		};
	
	/*******************************************************************************************************************************/	
	
	public function createWindow(Void):Void{
		//attach the movie
		this.__win = this.__mv.attachMovie('mvProfilDetails', 'PROFIL_' + this.__winID, this.__winDepth);
		//hide it for now, animation will bring it over
		this.__win._alpha = 0;
		//draw the background dependoing on the style
		if(CieStyle.__miniProfil.__bgBorderColor > 0){
			this.__win.lineStyle(CieStyle.__miniProfil.__bgBorderWidth, CieStyle.__miniProfil.__bgBorderColor, CieStyle.__miniProfil.__bgAlpha);
			}
		
		this.__win.beginFill(CieStyle.__miniProfil.__bgColor, CieStyle.__miniProfil.__bgAlpha);
		this.__win.moveTo(0, 0);
		this.__win.lineTo(CieStyle.__miniProfil.__bgW, 0);
		this.__win.lineTo(CieStyle.__miniProfil.__bgW , CieStyle.__miniProfil.__bgH - CieStyle.__miniProfil.__bgBorderRadius);
		this.__win.curveTo(CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgH, CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, CieStyle.__miniProfil.__bgH);
		this.__win.lineTo(0, CieStyle.__miniProfil.__bgH);
		this.__win.lineTo(0, 0);
		this.__win.endFill();
		
		/*
		this.__win.beginFill(CieStyle.__miniProfil.__bgColor, CieStyle.__miniProfil.__bgAlpha);
		this.__win.moveTo(0, 0);
		this.__win.lineTo(CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, 0);
		this.__win.curveTo(CieStyle.__miniProfil.__bgW, 0, CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgBorderRadius);
		this.__win.lineTo(CieStyle.__miniProfil.__bgW , CieStyle.__miniProfil.__bgH - CieStyle.__miniProfil.__bgBorderRadius);
		this.__win.curveTo(CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgH, CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, CieStyle.__miniProfil.__bgH);
		this.__win.lineTo(0, CieStyle.__miniProfil.__bgH);
		this.__win.lineTo(0, 0);
		this.__win.endFill();
		*/
		
		//put a glow on the bg
		this.__win.filters = [new GlowFilter(0x000000, BC.__win.__alpha, BC.__win.__glowsize, BC.__win.__glowsize, 2, 2, false, false)];
		
		//put the cross at the top -left corner
		var mvCloseButt:MovieClip = this.__win.attachMovie('mvCloseButt', 'CLOSE_BUTT', this.__win.getNextHighestDepth());
		//position the butt
		mvCloseButt._x = CieStyle.__miniProfil.__bgW - mvCloseButt._width - 7;
		mvCloseButt._y = 7;
		//action of the close butt
		mvCloseButt.__class = this;
		mvCloseButt.onRelease = function(Void):Void{
			this.__class.closeWindowEvent();
			};
		//pos the widow
		this.__win._x = this.__x;
		this.__win._y = this.__y;
		//fill infos
		this.__win.txtPseudo.htmlText = gLang[0] + this.__arrMiniProfil['pseudo'] + gLang[1] + this.__arrMiniProfil['age'] + gLang[2]; 
		this.__win.txtInfos.htmlText = this.__arrMiniProfil['t_sexe'] + ", " + this.__arrMiniProfil['t_etatcivil'] + ", " + this.__arrMiniProfil['t_orientation'] + '\n' + unescape(this.__arrMiniProfil['t_location']);
		
		//photo
		if(this.__arrMiniProfil['t_photo'] != undefined){
			this.__win.mvPhoto.mvPicture.loadMovie(this.__arrMiniProfil['t_photo']);
		}else{
			this.__win.mvPhoto.mvSexeLoader.gotoAndStop('_' + this.__arrMiniProfil['sexe']);
			}
		//put an action so when mouse is over it doesn't close
		this.__win.mvActionButt.__class = this;
		//when it'd over
		this.__win.mvActionButt.onRelease = function(Void):Void{
			this.__class.windowFocus(false);	
			//call the main application
			cLocalConn.callRemoteSocket(BC.__user.__remoteconn, ['WM_FOCUS', this.__class.__arrMiniProfil['ctype'], this.__class.__arrMiniProfil['no_publique'], this.__class.__strXml, this.__class.__arrMiniProfil['msgtype']]);	
			};
		//when it'd over
		this.__win.mvActionButt.onRollOver = function(Void):Void{
			this.__class.windowFocus(true);	
			};
		//when not over
		this.__win.mvActionButt.onReleaseOutside = this.__win.mvActionButt.onRollOut = function(Void):Void{
			this.__class.windowFocus(false);
			};	
		//the window title
		var strWinTitle:String = '';
		if(this.__arrMiniProfil['ctype'] == 'newconn'){
			strWinTitle = gLang[3];
		}else if(this.__arrMiniProfil['ctype'] == 'newchat'){
			strWinTitle = gLang[4];	
		}else if(this.__arrMiniProfil['ctype'] == 'newmsg'){
			strWinTitle = gLang[5];	
		}else if(this.__arrMiniProfil['ctype'] == 'newprofil'){
			strWinTitle = gLang[6];	
		}else if(this.__arrMiniProfil['ctype'] == 'newcrit'){
			strWinTitle = gLang[7];	
			}
		this.__win.txtWindowTitle.htmlText = '<b>' + strWinTitle + '</b>';		
		
		};
		
		
	/*******************************************************************************************************************************/	
	//when the mouse is over or out the window
	public function windowFocus(bFocus:Boolean):Void{
		//change the flag
		this.__focus = bFocus;
		//if have the focus
		if(this.__focus){
			//reset the close timer
			this.__cThreadRemoveWindow.destroy();
			//reset the anim alpha out
			this.__cThreadAnimWindow.destroy();
			//put the alpha back to normal
			this.__cThreadAnimWindow = cThreadManager.newThread(50, this, 'animWindowAlphaOn', {__supclass:this});
		}else{
			//restart it if doestn have the focus anymore but close in less time then the one before
			this.__cThreadRemoveWindow = cThreadManager.newThread((BC.__user.__windowsTimer/4), this, 'closeWindow', {__supclass:this});
			}
		};
		
	/*******************************************************************************************************************************/	
	//called by the thread
	public function closeWindow(obj:Object):Boolean{
		obj.__supclass.__cThreadAnimWindow = cThreadManager.newThread(50, obj.__supclass, 'animWindowAlphaOff', {__supclass:obj.__supclass});
		return false;
		};
		
	/*******************************************************************************************************************************/	
	//called by the user when press the xBox
	public function closeWindowEvent(Void):Void{
		//detroy all thread
		this.__cThreadAnimWindow.destroy();
		this.__cThreadRemoveWindow.destroy();
		//call the callback function to notify we are completely gone	
		this.__cbFunc(this.__cbClass, this.__winID, this.__dockID);
		};	
		
	/*******************************************************************************************************************************/	
	
	public function changeWindowDepth(Void):Void{
		//TODO
		};	
	
	/*******************************************************************************************************************************/	
	
	public function getWindowFocus(Void):Boolean{
		return this.__focus;
		};
	
	
	/*******************************************************************************************************************************/	
	
	public function getWindowID(Void):Number{
		return this.__winID;
		};
	
	
	/*******************************************************************************************************************************/	
	
	public function getDockID(Void):Number{
		return this.__dockID;
		};
		
	/*******************************************************************************************************************************/	
	
	public function changeDockID(dockID:Number):Void{
		this.__dockID = dockID;
		};
	
	/*******************************************************************************************************************************/	
	
	public function getWindowDepth(Void):Number{
		return this.__winDepth;
		};
		
	/*******************************************************************************************************************************/	
	
	public function removeWindow(Void):Void{
		this.__win.removeMovieClip();
		};	
	
	/*******************************************************************************************************************************/	
	
	public function changeWindowPosition(y:Number):Void{
		//the new position
		this.__newY = y;
		//start a thread for animation
		cThreadManager.newThread(50, this, 'animWindowPosition', {__win:this.__win, __y:this.__newY});
		};
	
	/*******************************************************************************************************************************/	
	
	public function animWindowPosition(obj:Object):Boolean{
		if(obj.__win._y < obj.__y){
			obj.__win._y += Math.ceil((obj.__y - obj.__win._y)/BC.__win.__movespeeddivider);
			return true;
			}
		return false;
		};	
		
	/*******************************************************************************************************************************/	
	
	public function animWindowAlphaOff(obj:Object):Boolean{
		if(obj.__supclass.__win._alpha > 0){
			obj.__supclass.__win._alpha -= BC.__win.__alphaspeed;
			return true;
			}
		//call the callback function to notify we are completely gone	
		obj.__supclass.__cbFunc(obj.__supclass.__cbClass, obj.__supclass.__winID, obj.__supclass.__dockID);	
		return false;
		};	
		
	/*******************************************************************************************************************************/	
	
	public function animWindowAlphaOn(obj:Object):Boolean{
		if(obj.__supclass.__win._alpha < 100){
			obj.__supclass.__win._alpha += BC.__win.__alphaspeed;
			return true;
			}
		return false;
		};		
	
	/*******************************************************************************************************************************/	
	
	public function createFromXml(strXml:String):Void{
		var newXml:XML = new XML(strXml);
		this.__arrMiniProfil = new Array();
		for(var i=0; i<newXml.firstChild.childNodes.length; i++){
			this.__arrMiniProfil[newXml.firstChild.childNodes[i].attributes.n] = unescape(newXml.firstChild.childNodes[i].firstChild.nodeValue);
			if(newXml.firstChild.childNodes[i].attributes.n == 'titre'){
				var strTitre:String = newXml.firstChild.childNodes[i].firstChild.nodeValue;
				}
			}
			
		//prepare xmlString for passing to main app to open a profil in right side
		this.__strXml = '';
		this.__strXml += '<R>';
		this.__strXml += '<C n="no_publique">' + this.__arrMiniProfil['no_publique'] + '</C>';
		this.__strXml += '<C n="pseudo">' + this.__arrMiniProfil['pseudo'] + '</C>';
		this.__strXml += '<C n="age">' + this.__arrMiniProfil['age'] + '</C>';
		this.__strXml += '<C n="ville_id">' + this.__arrMiniProfil['ville_id'] + '</C>';
		this.__strXml += '<C n="region_id">' + this.__arrMiniProfil['region_id'] + '</C>';
		this.__strXml += '<C n="code_pays">' + this.__arrMiniProfil['code_pays'] + '</C>';
		this.__strXml += '<C n="album">' + this.__arrMiniProfil['album'] + '</C>';
		this.__strXml += '<C n="photo">' + this.__arrMiniProfil['photo'] + '</C>';
		
		//Debug('ALERT_PHOTO_TAG: ' + this.__arrMiniProfil['photo']);
		
		this.__strXml += '<C n="vocal">' + this.__arrMiniProfil['vocal'] + '</C>';
		this.__strXml += '<C n="membership">' + this.__arrMiniProfil['membership'] + '</C>';
		this.__strXml += '<C n="orientation">' + this.__arrMiniProfil['orientation'] + '</C>';
		this.__strXml += '<C n="sexe">' + this.__arrMiniProfil['sexe'] + '</C>';
		this.__strXml += '<C n="titre">' + strTitre + '</C>';
		this.__strXml += '<C n="relation">' + this.__arrMiniProfil['relation'] + '</C>';
		this.__strXml += '<C n="etat_civil">' + this.__arrMiniProfil['etat_civil'] + '</C>';
		this.__strXml += '</R>';
		};
	
	/*******************************************************************************************************************************/
	
	public function getClassName(Void):String{
		return __className;
		};
	
	/*******************************************************************************************************************************/
	
	public function getClass(Void):CieAlertWindow{
		return this;
		};
		
	/*******************************************************************************************************************************/	
		
	};