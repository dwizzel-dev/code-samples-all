/*

homepage

*/

import control.CiePanel;
import control.CieTextLine;

import graphic.CieGradientRoundedSquare;
import flash.filters.GlowFilter;

import messages.CieTextMessages;

dynamic class display.CieWelcome{

	static private var __className = 'CieWelcome';
	static private var __instance:CieWelcome;
	private var __hvSpacer:Number;
	private var __panelClass:MovieClip;
	private var __arrBoxes:Array;
	private var __bNewMessage:Boolean = false;
			
	private function CieWelcome(Void){
		this.__hvSpacer = 10;
		this.__arrBoxes = new Array();
		};
		
	static public function getInstance(Void):CieWelcome{
		if(__instance == undefined) {
			__instance = new CieWelcome();
			}
		return __instance;
		};	
		
	public function reset(Void):Void{
		this.__h = this.__hvSpacer;
		};	
		
	/*************************************************************************************************************************************************/	
	
	public function openWelcome(Void):Void{
		//build the tab
		cContent.openTab(['welcome']);
		//class ref
		this.__panelClass = cContent.getPanelClass(['welcome', '_tl']);
		//loader because we will wait for content from request
		this.refreshWelcome();
		};
	
	/*************************************************************************************************************************************************/	
	
	public function refreshWelcome(Void):Void{
		//loader because we will wait for content from request
		this.__panelClass.setContent('mvLoaderAnimated');
		//build the request
		var arrD = new Array();
		arrD['methode'] = 'welcome';
		arrD['action'] = '';
		arrD['arguments'] = '';
		cReqManager.addRequest(arrD, this.cbWelcome, {__class:this});	
		};
	
	/*************************************************************************************************************************************************/
	
	//callBack function for the pseudo recherche
	public function cbWelcome(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		// show the result
		obj.__super.__class.parsePageContent(obj.__req.getXml().firstChild);
		//remove request
		cReqManager.removeRequest(obj.__req.getID());
		};	
		
		
	/*************************************************************************************************************************************************/
	
	//callBack function for the pseudo recherche
	public function parsePageContent(xmlNode:XMLNode):Void{
		var strError:String = '';
		//var strDebug:String = '\n';
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.attributes.n == 'error'){
				//
			}else if(currNode.attributes.n == 'messages_concat'){
				BC.__user.__msgcount = new Array();
				var arrSplit = currNode.firstChild.nodeValue.toString().split(',');
				
				BC.__user.__msgcount['instant'] = Number(arrSplit[0]);
				BC.__user.__msgcount['express'] = Number(arrSplit[1]);
				BC.__user.__msgcount['courriel'] = Number(arrSplit[2]);
				BC.__user.__msgcount['vocal'] = Number(arrSplit[3]);
				this.__bNewMessage = false;
				for(var o in BC.__user.__msgcount){
					if(BC.__user.__msgcount[o] > 0){
						this.__bNewMessage = true;
						}
					//message count
					//strDebug += 'BC.__user.__msgcount[' + o + ']: ' + BC.__user.__msgcount[o] + '\n';
					}
			}else{
				BC.__user['__' + currNode.attributes.n] = currNode.firstChild.nodeValue;
				//strDebug += 'BC.__user.__' + currNode.attributes.n + ': ' + BC.__user['__' + currNode.attributes.n] + '\n';
				}
			}
		//Debug(strDebug);
			
		//build the page
		this.buildPageContent();	
		};
		
	/*************************************************************************************************************************************************/

	public function buildPageContent(Void):Void{
		//effect 
		var filterArray:Array = new Array(new GlowFilter(CieStyle.__welcome.__effGlowColor, 0.25, 4, 4, 2, 3, false, false));
		//attache le template
		this.__panelClass.setContent('mvWelcome');
		//the size
		var oSize:Object = this.__panelClass.getPanelSize();
		//get the movie
		var mvPanel = this.__panelClass.getPanelContent();
		//photo
		if(BC.__user.__photo == '2'){	
			mvPanel.mvPhoto.mvPicture.mvPhotoBlur.loadMovie(BC.__server.__thumbs + BC.__user.__nopub.substr(0,2) + '/' + BC.__user.__pseudo + '.jpg');
		}else{
			mvPanel.mvPhoto.mvSexeLoader.gotoAndStop('_' + BC.__user.__sexe);
			mvPanel.mvPhoto.onRelease = function(){
				cFunc.openOptions('mon_profil');
				};
			}
		
		//BOXES
		//------box bienvenue
		this.__arrBoxes['bienvenue'] = mvPanel.createEmptyMovieClip('BOX_bienvenue', mvPanel.getNextHighestDepth());
		new CieGradientRoundedSquare(this.__arrBoxes['bienvenue'], (oSize.__width - (this.__hvSpacer) - 116), 77);
		this.__arrBoxes['bienvenue']._x = 116;
		this.__arrBoxes['bienvenue']._y = this.__hvSpacer;
		this.__arrBoxes['bienvenue'].filters = filterArray;
		//title
		new CieTextLine(this.__arrBoxes['bienvenue'], (this.__hvSpacer / 2), (this.__hvSpacer / 2), 0, 200, 'tf', gLang[278] + BC.__user.__pseudo + '!', 'dynamic',[true,false,false], false, false, false, false, [0x000000, 11]);
		//text
		var mvTexte:MovieClip = this.__arrBoxes['bienvenue'].attachMovie('mvAide', 'T_0', this.__arrBoxes['bienvenue'].getNextHighestDepth());
		mvTexte.txtInfos.autoSize = 'left';	
		mvTexte.txtInfos._width = oSize.__width - (this.__hvSpacer ) - 116;	
		mvTexte._x = (this.__hvSpacer / 2);
		mvTexte._y = 26;
		var strOnline:String = gLang[610] + '<a href="asfunction:cFunc.openSalonFromOutside"><u><b>' + BC.__user.__onlineusers + gLang[611] + '</b></u></a> ' + gLang[612];
		var strVisite:String = '<a href="asfunction:cFunc.openMessage,quiaconsulte"><u><b>' + BC.__user.__visitors + gLang[611] + '</b></u></a> ' + gLang[613];
		mvTexte.txtInfos.htmlText = '<font color="' + CieStyle.__welcome.__htmlTxColor + '">' + strOnline + '\n' + strVisite + '</font>';
		
		
		//------box essaye maintenant
		this.__arrBoxes['essayer'] = mvPanel.createEmptyMovieClip('BOX_essayer', mvPanel.getNextHighestDepth());
		new CieGradientRoundedSquare(this.__arrBoxes['essayer'], (oSize.__width - (this.__hvSpacer * 2)), 85);
		this.__arrBoxes['essayer']._x = this.__hvSpacer;
		this.__arrBoxes['essayer']._y = this.__arrBoxes['bienvenue']._height + this.__arrBoxes['bienvenue']._y + (this.__hvSpacer/2);
		this.__arrBoxes['essayer'].filters = filterArray;
		//title
		new CieTextLine(this.__arrBoxes['essayer'], (this.__hvSpacer / 2), (this.__hvSpacer / 2), 0, 200, 'tf', gLang[614], 'dynamic',[true,false,false], false, false, false, false);
		//in box with menus
		this.__arrBoxes['essayer'].__menuBox = this.__arrBoxes['essayer'].createEmptyMovieClip('submenu', this.__arrBoxes['essayer'].getNextHighestDepth());
		this.__arrBoxes['essayer'].__menuBox._x = (this.__hvSpacer / 2); 
		this.__arrBoxes['essayer'].__menuBox._y = 23;
		//les icones
		var mvIconVideo:MovieClip = this.__arrBoxes['essayer'].__menuBox.attachMovie('mvIconImage_30', 'I_0', this.__arrBoxes['essayer'].__menuBox.getNextHighestDepth());
		mvIconVideo._xscale *= 0.8;
		mvIconVideo._yscale *= 0.8;
		mvIconVideo._x = 0;
		mvIconVideo._y = 5;
		mvIconVideo.filters = filterArray;
		var tlVideo = new CieTextLine(this.__arrBoxes['essayer'].__menuBox, 25, 8, 180, 20, 'I_0', gLang[615], 'dynamic',[false, false, false], false, false, false, false, [CieStyle.__welcome.__hexTxColor, 11]);
		tlVideo.getSelectionMovie().onRelease = mvIconVideo.onRelease = function(){
			cFunc.askForVideoDescriptionMethod();
			};
		var mvIconSalon:MovieClip = this.__arrBoxes['essayer'].__menuBox.attachMovie('mvIconImage_18', 'I_1', this.__arrBoxes['essayer'].__menuBox.getNextHighestDepth());
		mvIconSalon._xscale *= 0.8;
		mvIconSalon._yscale *= 0.8;
		mvIconSalon._x = 0;
		mvIconSalon._y = 31;
		mvIconSalon.filters = filterArray;
		var tlSalon = new CieTextLine(this.__arrBoxes['essayer'].__menuBox, 25, 34, 180, 20, 'I_1', gLang[616], 'dynamic',[false, false, false], false, false, false, false, [CieStyle.__welcome.__hexTxColor, 11]);
		tlSalon.getSelectionMovie().onRelease = mvIconSalon.onRelease = function(){
			cFunc.openSalonFromOutside();
			};
		var mvIconRecherche:MovieClip = this.__arrBoxes['essayer'].__menuBox.attachMovie('mvIconImage_4', 'I_2', this.__arrBoxes['essayer'].__menuBox.getNextHighestDepth());
		mvIconRecherche._xscale *= 0.8;
		mvIconRecherche._yscale *= 0.8;
		mvIconRecherche._x = 150;
		mvIconRecherche._y = 5;
		mvIconRecherche.filters = filterArray;
		var tlRecherche = new CieTextLine(this.__arrBoxes['essayer'].__menuBox, 175, 8, 180, 20, 'I_2', gLang[617], 'dynamic',[false, false, false], false, false, false, false, [CieStyle.__welcome.__hexTxColor, 11]);
		tlRecherche.getSelectionMovie().onRelease = mvIconRecherche.onRelease = function(){
			cFunc.openRecherche();
			};
		var mvIconOptions:MovieClip = this.__arrBoxes['essayer'].__menuBox.attachMovie('mvIconImage_17', 'I_3', this.__arrBoxes['essayer'].__menuBox.getNextHighestDepth());
		mvIconOptions._xscale *= 0.8;
		mvIconOptions._yscale *= 0.8;
		mvIconOptions._x = 150;
		mvIconOptions._y = 31;
		mvIconOptions.filters = filterArray;
		var tlOptions = new CieTextLine(this.__arrBoxes['essayer'].__menuBox, 175, 34, 180, 20, 'I_3', gLang[618], 'dynamic',[false, false, false], false, false, false, false, [CieStyle.__welcome.__hexTxColor, 11]);
		tlOptions.getSelectionMovie().onRelease = mvIconOptions.onRelease = function(){
			cFunc.openOptions('preferences');
			};
		
		
		//-----box mes communications
		this.__arrBoxes['communications'] = mvPanel.createEmptyMovieClip('BOX_communications', mvPanel.getNextHighestDepth());
		new CieGradientRoundedSquare(this.__arrBoxes['communications'], (oSize.__width - (this.__hvSpacer * 2)), 144);
		this.__arrBoxes['communications']._x = this.__hvSpacer;
		this.__arrBoxes['communications']._y = this.__arrBoxes['essayer']._height + this.__arrBoxes['essayer']._y + (this.__hvSpacer/2);
		this.__arrBoxes['communications'].filters = filterArray;
		//title
		new CieTextLine(this.__arrBoxes['communications'], (this.__hvSpacer / 2), (this.__hvSpacer / 2), 0, 200, 'tf', gLang[619], 'dynamic',[true,false,false], false, false, false, false);
		//text
		var mvTexte:MovieClip = this.__arrBoxes['communications'].attachMovie('mvAide', 'T_0', this.__arrBoxes['communications'].getNextHighestDepth());
		mvTexte.txtInfos.autoSize = 'left';	
		mvTexte.txtInfos._width = oSize.__width - (this.__hvSpacer * 3);
		mvTexte._x = (this.__hvSpacer / 2);
		mvTexte._y = 23;
		var prctMail:Number = Math.round((BC.__user.__mailboxcurrent/BC.__user.__mailboxmax) * 100);
		var prctInstant:Number = Math.round((BC.__user.__instantcurrent/BC.__user.__instantmax) * 100);
		if(prctMail < 90){	
			var strMail:String = gLang[620] + prctMail + gLang[621];
		}else{
			var strMail:String = '<font color="#990000"><b>' + gLang[622] + '</b></font>' + gLang[623] + prctMail + gLang[624];
			}
		if(prctInstant < 90){	
			var strInstant:String = gLang[625] + prctInstant + gLang[626];
		}else{
			var strInstant:String = '<font color="#990000"><b>' + gLang[622] + '</b></font>' + gLang[627] + prctInstant + gLang[628];
			}
		if(this.__bNewMessage){
			var strMsg:String = gLang[629] + '<a href="asfunction:cFunc.openMessage,communications"><u><b>' + gLang[630] + '</b></u></a>.';
		}else{
			var strMsg:String = gLang[631];
			}
		mvTexte.txtInfos.htmlText = '<font color="' + CieStyle.__welcome.__htmlTxColor + '">' + strMail + '\n' + strInstant + '\n' + strMsg + '</font>';
		//line
		this.__arrBoxes['communications'].lineStyle(1, CieStyle.__welcome.__lineSepColor, 100);
		this.__arrBoxes['communications'].moveTo(0, (mvTexte._y + mvTexte._height + this.__hvSpacer));
		this.__arrBoxes['communications'].lineTo((oSize.__width - (this.__hvSpacer * 2)), (mvTexte._y + mvTexte._height + this.__hvSpacer));
		//in box with menus
		this.__arrBoxes['communications'].__menuBox = this.__arrBoxes['communications'].createEmptyMovieClip('submenu', this.__arrBoxes['communications'].getNextHighestDepth());
		new CieTextLine(this.__arrBoxes['communications'].__menuBox, 0, 0, 0, 200, 'tf', gLang[632], 'dynamic',[true,false,false], false, false, false, false, [CieStyle.__welcome.__hexSubTxColor, 11]);
		this.__arrBoxes['communications'].__menuBox._x = (this.__hvSpacer / 2); 
		this.__arrBoxes['communications'].__menuBox._y = (mvTexte._y + mvTexte._height + (this.__hvSpacer * 1.5));
		// inbox mneu left
		var mvTexte:MovieClip = this.__arrBoxes['communications'].__menuBox.attachMovie('mvAide', 'T_0', this.__arrBoxes['communications'].__menuBox.getNextHighestDepth());
		mvTexte.txtInfos.autoSize = 'left';	
		mvTexte.txtInfos._width = 180;	
		mvTexte._x = this.__hvSpacer;
		mvTexte._y = 17;
		mvTexte.txtInfos.htmlText = '<a href="asfunction:cFunc.openMessage,communications"><font color="' + CieStyle.__welcome.__htmlTxColor + '"><b>+ ' + BC.__user.__msgcount['courriel'] + '</b> ' + gLang[633] + '\n<b>+ ' + BC.__user.__msgcount['instant'] + '</b> ' + gLang[634] + '</font></a>';
		// inbox mneu right
		var mvTexte:MovieClip = this.__arrBoxes['communications'].__menuBox.attachMovie('mvAide', 'T_1', this.__arrBoxes['communications'].__menuBox.getNextHighestDepth());
		mvTexte.txtInfos.autoSize = 'left';	
		mvTexte.txtInfos._width = 180;	
		mvTexte._x = 150;
		mvTexte._y = 17;
		mvTexte.txtInfos.htmlText = '<a href="asfunction:cFunc.openMessage,communications"><font color="' + CieStyle.__welcome.__htmlTxColor + '"><b>+ ' + BC.__user.__msgcount['express'] + '</b> ' + gLang[635] + '\n<b>+ ' + BC.__user.__msgcount['vocal'] + '</b> ' + gLang[636] + '</font></a>';
		
		
		//------box mon profil
		this.__arrBoxes['profil'] = mvPanel.createEmptyMovieClip('BOX_profil', mvPanel.getNextHighestDepth());
		new CieGradientRoundedSquare(this.__arrBoxes['profil'], (oSize.__width - (this.__hvSpacer * 2)), (oSize.__height - this.__arrBoxes['communications']._height - this.__arrBoxes['communications']._y - (this.__hvSpacer * 1.5)));
		this.__arrBoxes['profil']._x = this.__hvSpacer;
		this.__arrBoxes['profil']._y = this.__arrBoxes['communications']._height + this.__arrBoxes['communications']._y + (this.__hvSpacer/2);
		this.__arrBoxes['profil'].filters = filterArray;
		//title
		new CieTextLine(this.__arrBoxes['profil'], (this.__hvSpacer / 2), (this.__hvSpacer / 2), 0, 200, 'tf', gLang[637], 'dynamic',[true,false,false], false, false, false, false);
		//text
		var mvTexte:MovieClip = this.__arrBoxes['profil'].attachMovie('mvAide', 'T_0', this.__arrBoxes['profil'].getNextHighestDepth());
		mvTexte.txtInfos.autoSize = 'left';	
		mvTexte.txtInfos._width = oSize.__width - (this.__hvSpacer * 3);
		mvTexte._x = (this.__hvSpacer / 2);
		mvTexte._y = 23;
		var strPriv:String = '';
		var strGeneric:String = '';
		if(BC.__user.__membership == 0){
			strPriv = gLang[638] + '<a href="asfunction:cFunc.gotoSiteRedirection,membershipstate"><b><u>' + gLang[639] + '</u></b></a>.';
			strGeneric = gLang[640];
		}else if(BC.__user.__membership == 1){	
			strPriv = gLang[638] + '<a href="asfunction:cFunc.gotoSiteRedirection,membershipstate"><b><u>' + gLang[641] + '</u></b></a>.';
			strGeneric = gLang[640];
		}else{
			strPriv = gLang[638] + '<a href="asfunction:cFunc.openOptions,abonnement"><b><u>' + gLang[642] + '</u></b></a>.';
			if(BC.__user.__photo == '2'){
				strGeneric = gLang[643];
			}else{
				strGeneric = gLang[644] + '<a href="asfunction:cFunc.openOptions,mon_profil"><u><b>' + gLang[645] + '</b></u></a>' + gLang[646];
				}
			}
		mvTexte.txtInfos.htmlText = '<font color="' + CieStyle.__welcome.__htmlTxColor + '">' + strPriv + '\n' + strGeneric + '</font>';
		//line
		this.__arrBoxes['profil'].lineStyle(1, CieStyle.__welcome.__lineSepColor, 100);
		this.__arrBoxes['profil'].moveTo(0, (mvTexte._y + mvTexte._height + this.__hvSpacer));
		this.__arrBoxes['profil'].lineTo((oSize.__width - (this.__hvSpacer * 2)), (mvTexte._y + mvTexte._height + this.__hvSpacer));
		//in box with menus
		this.__arrBoxes['profil'].__menuBox = this.__arrBoxes['profil'].createEmptyMovieClip('submenu', this.__arrBoxes['profil'].getNextHighestDepth());
		new CieTextLine(this.__arrBoxes['profil'].__menuBox, 0, 0, 0, 200, 'tf', gLang[647], 'dynamic',[true,false,false], false, false, false, false, [CieStyle.__welcome.__hexSubTxColor, 11]);
		this.__arrBoxes['profil'].__menuBox._x = (this.__hvSpacer / 2); 
		this.__arrBoxes['profil'].__menuBox._y = (mvTexte._y + mvTexte._height + (this.__hvSpacer * 1.5));
		// inbox mneu left
		var mvTexte:MovieClip = this.__arrBoxes['profil'].__menuBox.attachMovie('mvAide', 'T_0', this.__arrBoxes['profil'].__menuBox.getNextHighestDepth());
		mvTexte.txtInfos.autoSize = 'left';	
		mvTexte.txtInfos._width = 180;	
		mvTexte._x = this.__hvSpacer;
		mvTexte._y = 17;
		//mvTexte.txtInfos.htmlText = '<font color="' + CieStyle.__welcome.__htmlTxColor + '">+ <a href="asfunction:cFunc.openRecherche,pseudo,' + BC.__user.__pseudo + '">voir mon profil</a>\n+ <a href="asfunction:cFunc.openOptions,mon_profil">modifier mes infos</a>\n+ <a href="asfunction:cFunc.openOptions,mon_profil">modifier mes photos</a></font>';
		mvTexte.txtInfos.htmlText = '<font color="' + CieStyle.__welcome.__htmlTxColor + '">+ <a href="asfunction:cFunc.openDirectPseudoRecherche,' + BC.__user.__pseudo + '">' + gLang[648] + '</a>\n+ <a href="asfunction:cFunc.openOptions,mon_profil">' + gLang[649] + '</a>\n+ <a href="asfunction:cFunc.openOptions,mon_profil">' + gLang[650] + '</a></font>';
		// inbox mneu right
		var mvTexte:MovieClip = this.__arrBoxes['profil'].__menuBox.attachMovie('mvAide', 'T_1', this.__arrBoxes['profil'].__menuBox.getNextHighestDepth());
		mvTexte.txtInfos.autoSize = 'left';	
		mvTexte.txtInfos._width = 180;
		mvTexte._x = 150;
		mvTexte._y = 17;
		mvTexte.txtInfos.htmlText = '<font color="' + CieStyle.__welcome.__htmlTxColor + '">+ <a href="asfunction:cFunc.askForVideoDescriptionMethod">' + gLang[651] + '</a>\n+ <a href="asfunction:cFunc.gotoSiteRedirection,membershipstate">' + gLang[652] + '</a>\n+ <a  href="asfunction:cFunc.gotoSiteRedirection,desactivate">' + gLang[653] + '</a></font>';
						
		this.__panelClass.registerObject(this);
		};
	
	/*************************************************************************************************************************************************/
	
	public function resize(w:Number, h:Number):Void{
		//clear drawing
		this.__arrBoxes['bienvenue'].clear();
		this.__arrBoxes['essayer'].clear();
		this.__arrBoxes['communications'].clear();
		this.__arrBoxes['profil'].clear();
		//redraw boxes
		new CieGradientRoundedSquare(this.__arrBoxes['bienvenue'], (w - (this.__hvSpacer) - 116), 77);
		new CieGradientRoundedSquare(this.__arrBoxes['essayer'], (w - (this.__hvSpacer * 2)), 85);
		new CieGradientRoundedSquare(this.__arrBoxes['communications'], (w - (this.__hvSpacer * 2)), 144);
		new CieGradientRoundedSquare(this.__arrBoxes['profil'], (w - (this.__hvSpacer * 2)), (h - this.__arrBoxes['communications']._height - this.__arrBoxes['communications']._y - (this.__hvSpacer * 1.5)));
		//resize some of the text
		this.__arrBoxes['bienvenue']['T_0'].txtInfos._width = w - (this.__hvSpacer ) - 116;
		this.__arrBoxes['communications']['T_0'].txtInfos._width = w - (this.__hvSpacer * 3);
		this.__arrBoxes['profil']['T_0'].txtInfos._width = w - (this.__hvSpacer * 3);
		//lines separator
		this.__arrBoxes['communications'].lineStyle(1, CieStyle.__welcome.__lineSepColor, 100);
		this.__arrBoxes['communications'].moveTo(0, (this.__arrBoxes['communications']['T_0']._y + this.__arrBoxes['communications']['T_0']._height + this.__hvSpacer));
		this.__arrBoxes['communications'].lineTo((w - (this.__hvSpacer * 2)), (this.__arrBoxes['communications']['T_0']._y + this.__arrBoxes['communications']['T_0']._height + this.__hvSpacer));
		//lines separator
		this.__arrBoxes['profil'].lineStyle(1, CieStyle.__welcome.__lineSepColor, 100);
		this.__arrBoxes['profil'].moveTo(0, (this.__arrBoxes['profil']['T_0']._y + this.__arrBoxes['profil']['T_0']._height + this.__hvSpacer));
		this.__arrBoxes['profil'].lineTo((w - (this.__hvSpacer * 2)), (this.__arrBoxes['profil']['T_0']._y + this.__arrBoxes['profil']['T_0']._height + this.__hvSpacer));
		//sub menu boxes
		this.__arrBoxes['communications'].__menuBox._y = (this.__arrBoxes['communications']['T_0']._y + this.__arrBoxes['communications']['T_0']._height + (this.__hvSpacer * 1.5));
		this.__arrBoxes['profil'].__menuBox._y = (this.__arrBoxes['profil']['T_0']._y + this.__arrBoxes['profil']['T_0']._height + (this.__hvSpacer * 1.5));
		
		};
		
		

	}	