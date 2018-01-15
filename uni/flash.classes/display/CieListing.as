/*

All the type of listing of the application

*/

import display.CieMiniProfil;
import effect.CieDropShadow;
//import effect.CieBlur;

dynamic class display.CieListing{

	static private var __className = 'CieListing';
	
	private var __mv:MovieClip;
	private var __type:String;
	private var __arrListing:Array;	
	private var __hvSpacer:Number;
	private var __cthread:Array;
	private var __arrEffectOnSection:Array;
	
	/*************************************************************************************************************************************************/		
	
	public function CieListing(mv:MovieClip, ctype:String){
		this.__mv = mv;
		this.__hvSpacer = 6;
		this.__type = ctype;
		this.__arrListing = new Array();
		this.__cthread = new Array();
		//efffect on section
		this.__arrEffectOnSection = new Array();
		var arrTmp:Array = CieStyle.__miniProfil.__effectOnSection.split(',');
		for(var o in arrTmp){
			this.__arrEffectOnSection[arrTmp[o]] = true;
			}
		};
	
	/*************************************************************************************************************************************************/		
	
	public function createFromXmlNode(xmlNode:XMLNode):Void{
		this.clearListing();
		var mvMiniProfil:MovieClip;
		for(var i=0; i<xmlNode.childNodes.length; i++){
			this.__arrListing[i] = new Array();
			this.__arrListing[i]['__mProfil'] = new CieMiniProfil(this.__mv);
			this.__arrListing[i]['__mProfil'].makeFromXmlNode(xmlNode.childNodes[i]);
			if(this.__type == 'bottin'){
				this.__arrListing[i]['__mProfil'].affichageBottin();
			}else if(this.__type == 'communications'){
				this.__arrListing[i]['__mProfil'].affichageCommunications();
			}else{
				this.__arrListing[i]['__mProfil'].affichageStandard(this.__type);
				}
						
			mvMiniProfil = this.__arrListing[i]['__mProfil'].getProfilMovieClip();
			if(CieStyle.__miniProfil.__effectOn && (this.__arrEffectOnSection[this.__type] != undefined)){  
				mvMiniProfil._y =  (i * CieStyle.__miniProfil.__effectMvHeight) + this.__hvSpacer; 
				if(i != 0){
					new CieDropShadow(mvMiniProfil.mvBgBox, CieStyle.__miniProfil.__effectDistance, CieStyle.__miniProfil.__effectAlpha, CieStyle.__miniProfil.__effectStrength, CieStyle.__miniProfil.__effectQuality);
					}
				mvMiniProfil.mvBgBox.__index = i;
				mvMiniProfil.mvBgBox.__super = this;
				mvMiniProfil.mvBgBox.onRollOver = function(Void):Void{
					this.__super.showProfil(this.__index);
					};
			}else{
				mvMiniProfil._y =  (i * (this.__arrListing[i]['__mProfil'].getProfilHeight() + this.__hvSpacer)) + this.__hvSpacer;
				if(CieStyle.__miniProfil.__dropShadow){
					new CieDropShadow(mvMiniProfil.mvBgBox, CieStyle.__miniProfil.__effectDistance, CieStyle.__miniProfil.__effectAlpha, CieStyle.__miniProfil.__effectStrength, CieStyle.__miniProfil.__effectQuality);
					}
				}
			mvMiniProfil._x = this.__hvSpacer; 
			}
		};
		
	/*************************************************************************************************************************************************/			

	public function showProfil(iIndex:Number):Void{
		var mvMiniProfil:MovieClip;
		var bFound:Boolean = false;
		var iOffSetY = 0;
		for(var i=0; i<this.__arrListing.length; i++){
			mvMiniProfil = this.__arrListing[i]['__mProfil'].getProfilMovieClip();
			if(bFound){
				iOffSetY = this.__arrListing[i]['__mProfil'].getProfilHeight() - CieStyle.__miniProfil.__effectMvHeight + (this.__hvSpacer * 1.5);
				bFound = false;	
				}
			this.__cthread[i].destroy();
			this.__cthread[i] = cThreadManager.newThread(40, this, 'moveProfilEffect', {__mvProfil:mvMiniProfil, __newY:((i * CieStyle.__miniProfil.__effectMvHeight) + iOffSetY + this.__hvSpacer)});
			if(i == iIndex){
				bFound = true;
				}
			}
		};
		
	
	/*************************************************************************************************************************************************/			
	
	public function moveProfilEffect(obj:Object):Boolean{
		var newY = Math.floor((obj.__newY - obj.__mvProfil._y)/CieStyle.__miniProfil.__effectDivider);
		if(newY){
			obj.__mvProfil._y += newY;
			}
		if(!newY){
			return false;
			}
		return true;	
		};
	
	
	/*************************************************************************************************************************************************/			
	
	public function clearListing(Void):Void{
		for(var o in this.__arrListing){
			this.__arrListing[o]['__mProfil'].Destroy();
			delete this.__arrListing[o];
			}
		for(var o in this.__cthread){
			this.__cthread[o].destroy();
			delete this.__cthread[o];
			}	
		};

	/*************************************************************************************************************************************************/			
		
	public function selectAllCheckBox(strState:String):Void{ //0,1,2
		for(var o in this.__arrListing){
			this.__arrListing[o]['__mProfil'].selectCheckBox(strState);
			};
		};
		
	/*************************************************************************************************************************************************/				
		
	public function getCheckedItems(Void):Array{
		var arrChecked:Array = new Array();
		for(var o in this.__arrListing){
			if(this.__arrListing[o]['__mProfil'].getCheckedBoxState() == '1' || this.__arrListing[o]['__mProfil'].getCheckedBoxState() == '2'){
				arrChecked.push(this.__arrListing[o]['__mProfil'].getItemID());
				}
			};
		return arrChecked;	
		};		
	
	/*************************************************************************************************************************************************/			
	/*
	public function getClassName(Void):String{
		return __className;
		};
	*/
	/*************************************************************************************************************************************************/		
	/*	
	public function getClass(Void):CieListing{
		return this;
		};
	*/	
	}	