/*

abtarct function layer between user/event call and function class
action are called by string and convert to object here
security abtract layer between user command and CieFunctions

*/

import core.CieFunctions;

dynamic class utils.CieFunctionTable{

	static private var __className = 'CieFunctionTable';
	static private var __instance:CieFunctionTable;
		
	public function CieFunctionTable(Void){
		//method
		};
		
	public function callFunction(funcName:String, fParams:Array):Void{
		switch(funcName){
			case 'tracert'://
					break;
			
			case 'openTab'://
					var arrParam = fParams[0].split('/');
					cFunc.openTab(arrParam);
					break;
					
			case 'openWelcome'://
					cFunc.openWelcome();
					break;			
					
			case 'openBottin'://
					cFunc.openBottin();
					break;	

			case 'openMessage'://
					cFunc.openMessage();
					break;	

			case 'openRecherche'://
					cFunc.openRecherche();
					break;	
					
			case 'openAide'://
					cFunc.openAide();
					break;
					
			case 'openOptions'://
					cFunc.openOptions();
					break;

			case 'openLogin'://
					cFunc.openLogin();
					break;	

			case 'openLogout'://
					cFunc.openLogout();
					break;	

			case 'openSalon'://
					cFunc.openSalon();
					break;
					
			case 'openRecord'://
					//cFunc.openRecord();
					cFunc.askForVideoDescriptionMethod();
					break;		

			case 'openStatus'://
					cFunc.openStatus();
					break;	
					
			case 'openParametreFlash'://		
					System.showSettings(fParams[0]);		
					break;
					
			case 'openSite'://
					var arrD = Array();
					arrD['pseudo'] = 'pseudo';
					arrD['no_publique'] = '0';
					cFunc.openSiteRedirectionBox('help', arrD);
					break;			
					
			case 'openRechercheDetaillee'://
					cFunc.loadRechercheDetaillee();
					break;
					
			case 'openDescriptionVocal'://
					cFunc.openDescriptionVocal(fParams);
					break;	

			case 'openDescriptionVideo'://
					cFunc.openDescriptionVideo(fParams);
					break;			

			case 'resetStyle'://
					cFunc.resetStyle();
					break;		
					
			case 'resizeForm'://
					cFunc.resizeForm();
					break;			
			
			default://
					break;
								
			}
		};
	/*	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieFunctionTable{
		return this;
		};
	*/	
	}	