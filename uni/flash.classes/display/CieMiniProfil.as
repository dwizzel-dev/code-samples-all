/*

profil in line or box

*/

import manager.CieToolsManager;
import display.CieDetailedProfil;
//import system.CieDate;
import control.CieCheckBox;
import control.CieBubble;


dynamic class display.CieMiniProfil{

	static private var __className = 'CieMiniProfil';
	
	private var __containerPath:String;
	private var __arrMiniProfil:Array;
	private var __type:String;
	private var __mv:MovieClip;
	private var __profilBox:MovieClip;
	private var __xmlNode:XMLNode;
	private var __hvSpacer:Number;	
	private var __cCheckBox:CieCheckBox;	
	private var __cToolsManager:CieToolsManager;
	
	public function CieMiniProfil(mv:MovieClip){
		this.__hvSpacer = 10;
		this.__mv = mv;
		this.__arrMiniProfil = new Array();
		};
		
	/*************************************************************************************************************************************************/	
	
	//change the state of the checkBox	
	public function selectCheckBox(strState:String):Void{
		this.__cCheckBox.setSelect(strState);
		};	
		
	/*************************************************************************************************************************************************/	
	
	//returned the check State	
	public function getCheckedBoxState(Void):String{
		return this.__cCheckBox.getSelectionValue();
		};	
	
	/*************************************************************************************************************************************************/
	
	//return the id of the check box 
	public function getItemID(Void):String{
		return this.__arrMiniProfil['no_publique'];
		};		
			
	/*************************************************************************************************************************************************/	
	
	public function affichageCommunications(Void):Void{
		this.__type = 'communications';
		this.__profilBox = this.__mv.attachMovie('mvProfil_3', 'mvProfil_' + this.__arrMiniProfil['no_publique'], this.__mv.getNextHighestDepth());
		
		//draw box
		if(CieStyle.__miniProfil.__bgBorderColor > 0){
			this.__profilBox.mvBgBox.lineStyle(CieStyle.__miniProfil.__bgBorderWidth, CieStyle.__miniProfil.__bgBorderColor, CieStyle.__miniProfil.__bgAlpha);
			}
		if(this.__arrMiniProfil['direction'] == '1'){
			this.__profilBox.mvBgBox.beginFill(CieStyle.__miniProfil.__bgColorUpMsg, CieStyle.__miniProfil.__bgAlphaUp);
		}else{
			this.__profilBox.mvBgBox.beginFill(CieStyle.__miniProfil.__bgColorDownMsg, CieStyle.__miniProfil.__bgAlpha);
			}
		this.__profilBox.mvBgBox.moveTo(0, 0);
		this.__profilBox.mvBgBox.lineTo(CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, 0);
		this.__profilBox.mvBgBox.curveTo(CieStyle.__miniProfil.__bgW, 0, CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgBorderRadius);
		this.__profilBox.mvBgBox.lineTo(CieStyle.__miniProfil.__bgW , CieStyle.__miniProfil.__bgHInstant - CieStyle.__miniProfil.__bgBorderRadius);
		this.__profilBox.mvBgBox.curveTo(CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgHInstant, CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, CieStyle.__miniProfil.__bgHInstant);
		this.__profilBox.mvBgBox.lineTo(0, CieStyle.__miniProfil.__bgHInstant);
		this.__profilBox.mvBgBox.lineTo(0, 0);
		this.__profilBox.mvBgBox.endFill();
		
		//checkBox
		this.__cCheckBox = new CieCheckBox(this.__profilBox, ['',0]);
		this.__cCheckBox.getCheckBoxMovie()._x = 7;
		this.__cCheckBox.getCheckBoxMovie()._y = this.__profilBox.mvPhoto._y;
		
		//direction
		this.__profilBox.mvMsgDirection.gotoAndStop('_' + this.__arrMiniProfil['direction']);
		if(this.__arrMiniProfil['direction'] == '0' || this.__arrMiniProfil['direction'] == 'undefined' || this.__arrMiniProfil['direction'] == undefined){
			this.__profilBox.mvMsgDirection.__msgtype = this.__arrMiniProfil['msgtype'];	
			this.__profilBox.mvMsgDirection.__xmlNode = this.__xmlNode;
			this.__profilBox.mvMsgDirection.__bubble = null;
			this.__profilBox.mvMsgDirection.useHandCursor = true;
			this.__profilBox.mvMsgDirection.onRelease = function(Void):Void{
				this.__bubble.destroy();
				new CieDetailedProfil(['message','_tr'], ['message','SKIP','messages',this.__msgtype], this.__xmlNode, ['message','SKIP']);
				};
			this.__profilBox.mvMsgDirection.onRollOver = function(Void):Void{
				if(BC.__user.__showbubble){
					this.__bubble = new CieBubble(__stage, 100, 150, 5, '', gLang[320]);
					}
				};
			this.__profilBox.mvMsgDirection.onRollOut = this.__profilBox.mvMsgDirection.onDragOut = this.__profilBox.mvMsgDirection.onReleaseOutside = function(Void):Void{	
				this.__bubble.destroy();
				};	
		}else{
			this.__profilBox.mvMsgDirection.__msgtype = this.__arrMiniProfil['msgtype'];	
			this.__profilBox.mvMsgDirection.__xmlNode = this.__xmlNode;
			this.__profilBox.mvMsgDirection.__bubble = null;
			this.__profilBox.mvMsgDirection.useHandCursor = true;
			this.__profilBox.mvMsgDirection.onRelease = function(Void):Void{
				this.__bubble.destroy();
				new CieDetailedProfil(['message','_tr'], ['message','SKIP','messages',this.__msgtype], this.__xmlNode, ['message','SKIP']);
				};
			this.__profilBox.mvMsgDirection.onRollOver = function(Void):Void{
				if(BC.__user.__showbubble){
					this.__bubble = new CieBubble(__stage, 100, 150, 5, '', gLang[321]);
					}
				};
			this.__profilBox.mvMsgDirection.onRollOut = this.__profilBox.mvMsgDirection.onDragOut = this.__profilBox.mvMsgDirection.onReleaseOutside = function(Void):Void{	
				this.__bubble.destroy();
				};	
			}
		
		
		//lu et nonlu
		this.__profilBox.mvMsgLu.gotoAndStop('_' + this.__arrMiniProfil['lu']);
		if(this.__arrMiniProfil['lu'] == '0'){
			this.__profilBox.mvMsgLu.__msgtype = this.__arrMiniProfil['msgtype'];	
			this.__profilBox.mvMsgLu.__xmlNode = this.__xmlNode;
			this.__profilBox.mvMsgLu.__bubble = null;
			this.__profilBox.mvMsgLu.useHandCursor = true;
			this.__profilBox.mvMsgLu.onRelease = function(Void):Void{
				this.__bubble.destroy();
				new CieDetailedProfil(['message','_tr'], ['message','SKIP','messages',this.__msgtype], this.__xmlNode, ['message','SKIP']);
				};
			this.__profilBox.mvMsgLu.onRollOver = function(Void):Void{
				if(BC.__user.__showbubble){
					this.__bubble = new CieBubble(__stage, 100, 150, 5, '', gLang[322]);
					}
				};
			this.__profilBox.mvMsgLu.onRollOut = this.__profilBox.mvMsgLu.onDragOut = this.__profilBox.mvMsgLu.onReleaseOutside = function(Void):Void{	
				this.__bubble.destroy();
				};
			}

		
		//infos
		this.__profilBox.txtPseudo.htmlText = '<b><font color="' + (CieStyle.__basic['__membership_' + this.__arrMiniProfil['membership']]) + '">' + this.__arrMiniProfil['pseudo'] + '</font></b>'; 
		this.__profilBox.txtInfos.htmlText = cFormManager.__obj['etatcivil'][this.__arrMiniProfil['etat_civil']][1] + ', ' +  this.__arrMiniProfil['age'] + gLang[202] + '\n'; 
		if(gGeo[this.__arrMiniProfil['code_pays'] + '_' + this.__arrMiniProfil['region_id'] + '_' + this.__arrMiniProfil['ville_id']] != undefined){
			this.__profilBox.txtInfos.htmlText += gGeo[this.__arrMiniProfil['code_pays'] + '_' + this.__arrMiniProfil['region_id'] + '_' + this.__arrMiniProfil['ville_id']];
			}
		this.__profilBox.txtInfos.htmlText += "\"" + unescape(this.__arrMiniProfil['titre'].toLowerCase()) + "\""; 
				
		//la photo
		if(this.__arrMiniProfil['photo'] == '2'){
			this.__profilBox.mvPhoto.mvPicture.mvPhotoBlur.loadMovie(BC.__server.__thumbs + this.__arrMiniProfil['no_publique'].substr(0,2) + '/' + this.__arrMiniProfil['pseudo'] + '.jpg');
			//action onRelease open the photoTab
			this.__profilBox.mvPhoto.__type = this.__type;
			this.__profilBox.mvPhoto.__xmlNode = this.__xmlNode;
			this.__profilBox.mvPhoto.onRelease = function(Void):Void{
				new CieDetailedProfil(['message','_tr'], ['message','SKIP','photo'], this.__xmlNode, ['message','SKIP']);
				};
		}else{
			this.__profilBox.mvPhoto.mvSexeLoader.gotoAndStop('_' + this.__arrMiniProfil['sexe']);
			}
		
		//toolbar
		var strXML:String = '';
		strXML += '<UNITOOLBAR>';
		strXML += '<TOOLGROUP n="communication" align="right">';
		strXML += '<TOOL n="messages" type="28X28" icon="mvIconImageMP_7_' + cSockManager.getOnlineStatus(this.__arrMiniProfil['no_publique']) + '"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[317] + '"></BUBBLE></TOOL>';
		strXML += '</TOOLGROUP>';
		strXML += '</UNITOOLBAR>';

		this.__cToolsManager = new CieToolsManager(this.__profilBox, 332, 0, 0, 20);
		this.__cToolsManager.createFromXml(new XML(strXML).firstChild);

		//change the color
		this.__cToolsManager.getTool('communication', 'messages').changeColor(CieStyle.__basic.__buttonColor, CieStyle.__basic.__buttonBorderWidth, CieStyle.__basic.__buttonBorderColor, CieStyle.__basic.__buttonEffectColor, CieStyle.__basic.__buttonEffectColorOff);
		
		//event action on online status chages
		this.__cToolsManager.getIcon('communication', 'messages').__toolmanager = this.__cToolsManager.getTool('communication', 'messages');
		this.__cToolsManager.getIcon('communication', 'messages').__nopub = this.__arrMiniProfil['no_publique'];
		this.__cToolsManager.getIcon('communication', 'messages').updateObject = function(nopub, state):Boolean{
			if(nopub == this.__nopub){
				this.__toolmanager.changeIcon("mvIconImageMP_7_" + state);
				}
			return true;	
			};
		//register for online event	
		cSockManager.registerObjectForOnlineNotification(this.__cToolsManager.getIcon('communication', 'messages'));
		
		this.__cToolsManager.getIcon('communication', 'messages').__user = {__pseudo: this.__arrMiniProfil['pseudo'], __nopub:this.__arrMiniProfil['no_publique']};
		this.__cToolsManager.getIcon('communication', 'messages').onRelease = function(Void):Void{
			//for bubble text usage
			if(this.__bubbletext != undefined){
				this.__bubble.destroy();
				}
			cFunc.askForVideoChat(this.__user);
			};

		this.__profilBox.mvBgBox.__direction = this.__arrMiniProfil['direction'];
		this.__profilBox.mvBgBox.__msgtype = this.__arrMiniProfil['msgtype'];	
		this.__profilBox.mvBgBox.__xmlNode = this.__xmlNode;
		this.__profilBox.mvBgBox.onRelease = function(Void):Void{
			new CieDetailedProfil(['message','_tr'], ['message','SKIP','messages',this.__msgtype], this.__xmlNode, ['message','SKIP']);
			};	
			
		this.__profilBox.mvBgBox.onRollOver = function(Void):Void{	
			this.lineStyle(CieStyle.__miniProfil.__bgBorderWidth, CieStyle.__miniProfil.__bgBorderColorOver, CieStyle.__miniProfil.__bgAlpha);
			this.moveTo(0, 0);
			this.lineTo(CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, 0);
			this.curveTo(CieStyle.__miniProfil.__bgW, 0, CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgBorderRadius);
			this.lineTo(CieStyle.__miniProfil.__bgW , CieStyle.__miniProfil.__bgHInstant - CieStyle.__miniProfil.__bgBorderRadius);
			this.curveTo(CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgHInstant, CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, CieStyle.__miniProfil.__bgHInstant);
			this.lineTo(0, CieStyle.__miniProfil.__bgHInstant);
			this.lineTo(0, 0);
			//cFunc.changeMouseOverState(true);
			};	
			
		this.__profilBox.mvBgBox.onRollOut = 
		this.__profilBox.mvBgBox.onDragOut = 
		this.__profilBox.mvBgBox.onReleaseOutside = function(Void):Void{	
			this.lineStyle(CieStyle.__miniProfil.__bgBorderWidth, CieStyle.__miniProfil.__bgBorderColor, CieStyle.__miniProfil.__bgAlpha);
			this.moveTo(0, 0);
			this.lineTo(CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, 0);
			this.curveTo(CieStyle.__miniProfil.__bgW, 0, CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgBorderRadius);
			this.lineTo(CieStyle.__miniProfil.__bgW , CieStyle.__miniProfil.__bgHInstant - CieStyle.__miniProfil.__bgBorderRadius);
			this.curveTo(CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgHInstant, CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, CieStyle.__miniProfil.__bgHInstant);
			this.lineTo(0, CieStyle.__miniProfil.__bgHInstant);
			this.lineTo(0, 0);
			//cFunc.changeMouseOverState(false);
			};		
			
		};
	
	/*************************************************************************************************************************************************/
	
	public function affichageStandard(ctype:String):Void{
		this.__type = ctype;
		
		//infos
		this.__profilBox = this.__mv.attachMovie('mvProfil_1', 'mvProfil_' + this.__arrMiniProfil['no_publique'] + '_' + cConversion.makeRandomNum(10000,0), this.__mv.getNextHighestDepth());
		
		//draw box
		if(CieStyle.__miniProfil.__bgBorderColor > 0){
			this.__profilBox.mvBgBox.lineStyle(CieStyle.__miniProfil.__bgBorderWidth, CieStyle.__miniProfil.__bgBorderColor, CieStyle.__miniProfil.__bgAlpha);
			}
		this.__profilBox.mvBgBox.beginFill(CieStyle.__miniProfil.__bgColor, CieStyle.__miniProfil.__bgAlpha);
		this.__profilBox.mvBgBox.moveTo(0, 0);
		this.__profilBox.mvBgBox.lineTo(CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, 0);
		this.__profilBox.mvBgBox.curveTo(CieStyle.__miniProfil.__bgW, 0, CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgBorderRadius);
		this.__profilBox.mvBgBox.lineTo(CieStyle.__miniProfil.__bgW , CieStyle.__miniProfil.__bgHStandard - CieStyle.__miniProfil.__bgBorderRadius);
		this.__profilBox.mvBgBox.curveTo(CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgHStandard, CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, CieStyle.__miniProfil.__bgHStandard);
		this.__profilBox.mvBgBox.lineTo(0, CieStyle.__miniProfil.__bgHStandard);
		this.__profilBox.mvBgBox.lineTo(0, 0);
		this.__profilBox.mvBgBox.endFill();	
		
		//infos
		this.__profilBox.txtPseudo.htmlText = '<b><font color="' + (CieStyle.__basic['__membership_' + this.__arrMiniProfil['membership']]) + '">' + this.__arrMiniProfil['pseudo'] + '</font></b>'; 
		this.__profilBox.txtInfos.htmlText = cFormManager.__obj['etatcivil'][this.__arrMiniProfil['etat_civil']][1] + ', ' +  this.__arrMiniProfil['age'] + gLang[202] + '\n'; 
		if(gGeo[this.__arrMiniProfil['code_pays'] + '_' + this.__arrMiniProfil['region_id'] + '_' + this.__arrMiniProfil['ville_id']] != undefined){
			this.__profilBox.txtInfos.htmlText += gGeo[this.__arrMiniProfil['code_pays'] + '_' + this.__arrMiniProfil['region_id'] + '_' + this.__arrMiniProfil['ville_id']];
			}
		this.__profilBox.txtInfos.htmlText += "\"" + unescape(this.__arrMiniProfil['titre'].toLowerCase()) + "\""; 
				
		//la photo
		if(this.__arrMiniProfil['photo'] == '2'){
			this.__profilBox.mvPhoto.mvPicture.mvPhotoBlur.loadMovie(BC.__server.__thumbs + this.__arrMiniProfil['no_publique'].substr(0,2) + '/' + this.__arrMiniProfil['pseudo'] + '.jpg');
			//action onRelease open the photoTab
			this.__profilBox.mvPhoto.__type = this.__type;
			this.__profilBox.mvPhoto.__xmlNode = this.__xmlNode;
			this.__profilBox.mvPhoto.onRelease = function(Void):Void{
				new CieDetailedProfil([this.__type,'_tr'], [this.__type,'SKIP','photo'], this.__xmlNode, [this.__type,'SKIP']);
				};
		}else{
			this.__profilBox.mvPhoto.mvSexeLoader.gotoAndStop('_' + this.__arrMiniProfil['sexe']);
			}
			
	
		//toolbar
		var strXML:String = '';
		strXML += '<UNITOOLBAR>';
		strXML += '<TOOLGROUP n="communication" align="right">';
		strXML += '<TOOL n="messages" type="28X28" icon="mvIconImageMP_7_' + cSockManager.getOnlineStatus(this.__arrMiniProfil['no_publique']) + '"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[317] + '"></BUBBLE></TOOL>';
		strXML += '</TOOLGROUP>';
		strXML += '</UNITOOLBAR>';
		
		this.__cToolsManager = new CieToolsManager(this.__profilBox, 332, 0, 0, 20);
		this.__cToolsManager.createFromXml(new XML(strXML).firstChild);
		
		//change the color
		this.__cToolsManager.getTool('communication', 'messages').changeColor(CieStyle.__basic.__buttonColor, CieStyle.__basic.__buttonBorderWidth, CieStyle.__basic.__buttonBorderColor, CieStyle.__basic.__buttonEffectColor, CieStyle.__basic.__buttonEffectColorOff);
		
		//event action on online status chages
		this.__cToolsManager.getIcon('communication', 'messages').__toolmanager = this.__cToolsManager.getTool('communication', 'messages');
		this.__cToolsManager.getIcon('communication', 'messages').__nopub = this.__arrMiniProfil['no_publique'];
		this.__cToolsManager.getIcon('communication', 'messages').updateObject = function(nopub, state):Boolean{
			if(nopub == this.__nopub){
				this.__toolmanager.changeIcon("mvIconImageMP_7_" + state);
				}
			return true;	
			};
		//register for online event	
		cSockManager.registerObjectForOnlineNotification(this.__cToolsManager.getIcon('communication', 'messages'));
		
		//register for online event	
		cSockManager.registerObjectForOnlineNotification(this.__cToolsManager.getIcon('communication', 'messages'));
		this.__cToolsManager.getIcon('communication', 'messages').__user = {__pseudo: this.__arrMiniProfil['pseudo'], __nopub:this.__arrMiniProfil['no_publique']};
		this.__cToolsManager.getIcon('communication', 'messages').onRelease = function(Void):Void{
			//for bubble text usage
			if(this.__bubbletext != undefined){
				this.__bubble.destroy();
				}
			cFunc.askForVideoChat(this.__user);
			};
		//set action on the profil instead
		this.__profilBox.mvBgBox.__type = this.__type;		
		this.__profilBox.mvBgBox.__xmlNode = this.__xmlNode;
		this.__profilBox.mvBgBox.onRelease = function(Void):Void{
			new CieDetailedProfil([this.__type,'_tr'], [this.__type,'SKIP','profil','SKIP','description'], this.__xmlNode, [this.__type,'SKIP']);
			};

		this.__profilBox.mvBgBox.onRollOver = function(Void):Void{	
			this.lineStyle(CieStyle.__miniProfil.__bgBorderWidth, CieStyle.__miniProfil.__bgBorderColorOver, CieStyle.__miniProfil.__bgAlpha);
			this.moveTo(0, 0);
			this.lineTo(CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, 0);
			this.curveTo(CieStyle.__miniProfil.__bgW, 0, CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgBorderRadius);
			this.lineTo(CieStyle.__miniProfil.__bgW , CieStyle.__miniProfil.__bgHInstant - CieStyle.__miniProfil.__bgBorderRadius);
			this.curveTo(CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgHInstant, CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, CieStyle.__miniProfil.__bgHInstant);
			this.lineTo(0, CieStyle.__miniProfil.__bgHInstant);
			this.lineTo(0, 0);
			//cFunc.changeMouseOverState(true);
			};	
			
		this.__profilBox.mvBgBox.onRollOut = 
		this.__profilBox.mvBgBox.onDragOut = 
		this.__profilBox.mvBgBox.onReleaseOutside = function(Void):Void{	
			this.lineStyle(CieStyle.__miniProfil.__bgBorderWidth, CieStyle.__miniProfil.__bgBorderColor, CieStyle.__miniProfil.__bgAlpha);
			this.moveTo(0, 0);
			this.lineTo(CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, 0);
			this.curveTo(CieStyle.__miniProfil.__bgW, 0, CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgBorderRadius);
			this.lineTo(CieStyle.__miniProfil.__bgW , CieStyle.__miniProfil.__bgHInstant - CieStyle.__miniProfil.__bgBorderRadius);
			this.curveTo(CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgHInstant, CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, CieStyle.__miniProfil.__bgHInstant);
			this.lineTo(0, CieStyle.__miniProfil.__bgHInstant);
			this.lineTo(0, 0);
			//cFunc.changeMouseOverState(false);
			};			
		
		};
	
	/*************************************************************************************************************************************************/
	
	public function affichageBottin(Void):Void{
		this.__type = 'bottin';
		
		//infos
		this.__profilBox = this.__mv.attachMovie('mvProfil_2', 'mvProfil_' + this.__arrMiniProfil['no_publique'] + '_' + cConversion.makeRandomNum(10000,0), this.__mv.getNextHighestDepth());
		
		//draw box
		if(CieStyle.__miniProfil.__bgBorderColor > 0){
			this.__profilBox.mvBgBox.lineStyle(CieStyle.__miniProfil.__bgBorderWidth, CieStyle.__miniProfil.__bgBorderColor, CieStyle.__miniProfil.__bgAlpha);
			}
		this.__profilBox.mvBgBox.beginFill(CieStyle.__miniProfil.__bgColor, CieStyle.__miniProfil.__bgAlpha);
		this.__profilBox.mvBgBox.moveTo(0, 0);
		this.__profilBox.mvBgBox.lineTo(CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, 0);
		this.__profilBox.mvBgBox.curveTo(CieStyle.__miniProfil.__bgW, 0, CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgBorderRadius);
		this.__profilBox.mvBgBox.lineTo(CieStyle.__miniProfil.__bgW , CieStyle.__miniProfil.__bgHBottin - CieStyle.__miniProfil.__bgBorderRadius);
		this.__profilBox.mvBgBox.curveTo(CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgHBottin, CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, CieStyle.__miniProfil.__bgHBottin);
		this.__profilBox.mvBgBox.lineTo(0, CieStyle.__miniProfil.__bgHBottin);
		this.__profilBox.mvBgBox.lineTo(0, 0);
		this.__profilBox.mvBgBox.endFill();	
		
		//infos
		this.__profilBox.txtPseudo.htmlText = '<b><font color="' + (CieStyle.__basic['__membership_' + this.__arrMiniProfil['membership']]) + '">' + this.__arrMiniProfil['pseudo'] + '</font></b>'; 
		this.__profilBox.txtInfos.htmlText = cFormManager.__obj['etatcivil'][this.__arrMiniProfil['etat_civil']][1] + ', ' +  this.__arrMiniProfil['age'] + gLang[202] + '\n'; 
		if(gGeo[this.__arrMiniProfil['code_pays'] + '_' + this.__arrMiniProfil['region_id'] + '_' + this.__arrMiniProfil['ville_id']] != undefined){
			this.__profilBox.txtInfos.htmlText += gGeo[this.__arrMiniProfil['code_pays'] + '_' + this.__arrMiniProfil['region_id'] + '_' + this.__arrMiniProfil['ville_id']];
			}
		this.__profilBox.txtInfos.htmlText += "\"" + unescape(this.__arrMiniProfil['titre'].toLowerCase()) + "\""; 
				
		//la photo
		if(this.__arrMiniProfil['photo'] == '2'){
			this.__profilBox.mvPhoto.mvPicture.mvPhotoBlur.loadMovie(BC.__server.__thumbs + this.__arrMiniProfil['no_publique'].substr(0,2) + '/' + this.__arrMiniProfil['pseudo'] + '.jpg');
			//action onRelease open the photoTab
			this.__profilBox.mvPhoto.__xmlNode = this.__xmlNode;
			this.__profilBox.mvPhoto.onRelease = function(Void):Void{
				new CieDetailedProfil(['bottin','_tr'], ['bottin','SKIP','photo'], this.__xmlNode, ['bottin','SKIP']);
				};
		}else{
			this.__profilBox.mvPhoto.mvSexeLoader.gotoAndStop('_' + this.__arrMiniProfil['sexe']);
			}
		
		//MODIFIED BY AARIZO
		this.__cCheckBox = new CieCheckBox(this.__profilBox, ['',0]);
		this.__cCheckBox.getCheckBoxMovie()._x = 7;
		this.__cCheckBox.getCheckBoxMovie()._y = this.__profilBox.mvPhoto._y;
		//MODIFIED BY AARIZO
		
		//toolbar
		var strXML:String = '';
		strXML += '<UNITOOLBAR>';
		strXML += '<TOOLGROUP n="communication" align="right">';
		strXML += '<TOOL n="messages" type="28X28" icon="mvIconImageMP_7_' + cSockManager.getOnlineStatus(this.__arrMiniProfil['no_publique']) + '"><ACTION n="onrollover" func=""></ACTION><BUBBLE t="' + gLang[317] + '"></BUBBLE></TOOL>';
		strXML += '</TOOLGROUP>';
		strXML += '</UNITOOLBAR>';
		
		this.__cToolsManager = new CieToolsManager(this.__profilBox, 332, 0, 0, 20);
		this.__cToolsManager.createFromXml(new XML(strXML).firstChild);
		
		//change the color
		this.__cToolsManager.getTool('communication', 'messages').changeColor(CieStyle.__basic.__buttonColor, CieStyle.__basic.__buttonBorderWidth, CieStyle.__basic.__buttonBorderColor, CieStyle.__basic.__buttonEffectColor, CieStyle.__basic.__buttonEffectColorOff);
		
		//event action on online status chages
		this.__cToolsManager.getIcon('communication', 'messages').__toolmanager = this.__cToolsManager.getTool('communication', 'messages');
		this.__cToolsManager.getIcon('communication', 'messages').__nopub = this.__arrMiniProfil['no_publique'];
		this.__cToolsManager.getIcon('communication', 'messages').updateObject = function(nopub, state):Boolean{
			if(nopub == this.__nopub){
				this.__toolmanager.changeIcon("mvIconImageMP_7_" + state);
				}
			return true;	
			};
		//register for online event	
		cSockManager.registerObjectForOnlineNotification(this.__cToolsManager.getIcon('communication', 'messages'));
		
		
		this.__cToolsManager.getIcon('communication', 'messages').__user = {__pseudo: this.__arrMiniProfil['pseudo'], __nopub:this.__arrMiniProfil['no_publique']};
		this.__cToolsManager.getIcon('communication', 'messages').onRelease = function(Void):Void{
			//for bubble text usage
			if(this.__bubbletext != undefined){
				this.__bubble.destroy();
				}
			cFunc.askForVideoChat(this.__user);
			};
		
		this.__profilBox.mvBgBox.__xmlNode = this.__xmlNode;
		this.__profilBox.mvBgBox.onRelease = function(Void):Void{
			new CieDetailedProfil(['bottin','_tr'], ['bottin','SKIP','profil','SKIP','description'], this.__xmlNode, ['bottin','SKIP']);
			};
			
		this.__profilBox.mvBgBox.onRollOver = function(Void):Void{	
			this.lineStyle(CieStyle.__miniProfil.__bgBorderWidth, CieStyle.__miniProfil.__bgBorderColorOver, CieStyle.__miniProfil.__bgAlpha);
			this.moveTo(0, 0);
			this.lineTo(CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, 0);
			this.curveTo(CieStyle.__miniProfil.__bgW, 0, CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgBorderRadius);
			this.lineTo(CieStyle.__miniProfil.__bgW , CieStyle.__miniProfil.__bgHInstant - CieStyle.__miniProfil.__bgBorderRadius);
			this.curveTo(CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgHInstant, CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, CieStyle.__miniProfil.__bgHInstant);
			this.lineTo(0, CieStyle.__miniProfil.__bgHInstant);
			this.lineTo(0, 0);
			//cFunc.changeMouseOverState(true);
			};	
			
		this.__profilBox.mvBgBox.onRollOut = 
		this.__profilBox.mvBgBox.onDragOut = 
		this.__profilBox.mvBgBox.onReleaseOutside = function(Void):Void{	
			this.lineStyle(CieStyle.__miniProfil.__bgBorderWidth, CieStyle.__miniProfil.__bgBorderColor, CieStyle.__miniProfil.__bgAlpha);
			this.moveTo(0, 0);
			this.lineTo(CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, 0);
			this.curveTo(CieStyle.__miniProfil.__bgW, 0, CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgBorderRadius);
			this.lineTo(CieStyle.__miniProfil.__bgW , CieStyle.__miniProfil.__bgHInstant - CieStyle.__miniProfil.__bgBorderRadius);
			this.curveTo(CieStyle.__miniProfil.__bgW, CieStyle.__miniProfil.__bgHInstant, CieStyle.__miniProfil.__bgW - CieStyle.__miniProfil.__bgBorderRadius, CieStyle.__miniProfil.__bgHInstant);
			this.lineTo(0, CieStyle.__miniProfil.__bgHInstant);
			this.lineTo(0, 0);
			//cFunc.changeMouseOverState(false);
			};			
	
		};
	
	/*************************************************************************************************************************************************/
	
	public function Destroy(Void):Void{
		this.__profilBox.clear();
		this.__profilBox.removeMovieClip();
		this.__profilBox = null;
		delete this.__profilBox;
		};
		
	/*************************************************************************************************************************************************/	
	
	public function makeFromXmlNode(xmlNode:XMLNode):Void{
		//ref for when we calle the DetailedProfil pass him this Node
		this.__xmlNode = xmlNode;
		for(var i=0; i<xmlNode.childNodes.length; i++){
			this.__arrMiniProfil[xmlNode.childNodes[i].attributes.n] = xmlNode.childNodes[i].firstChild.nodeValue;
			}
		};
		
	/*************************************************************************************************************************************************/	
		
	public function getProfilMovieClip(Void):MovieClip{
		return this.__profilBox;
		};
		
	public function getProfilHeight(Void):Number{
		return this.__profilBox._height;
		};	
	/*	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieMiniProfil{
		return this;
		};
	*/	
	}	