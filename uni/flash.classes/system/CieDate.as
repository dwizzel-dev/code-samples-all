/*
	format de date a recevoir:

	date: 2000-01-08 00:00:00
	date collée: 20050806115740
	timestamp: 1020097103 (nombre de secondes depuis 1970)

*/

dynamic class system.CieDate{
	
	static private var __className:String = 'CieDate';
	
	private var __rcvDate:String;
	private var __rtnDate:String;
	
	private var __year:String;
	private var __month:String;
	private var __dayOfWeek:String;
	private var __dayOfMonth:String;
	private var __str:String;
			
	private var __arrJour:Array;
	private var __arrMois:Array;
	

/************************************************************************************************************/		
	public function CieDate(rcvDate:String, rtnDate:String){
		
		this.__arrJour = new Array(gLang[256], gLang[257], gLang[258], gLang[259], gLang[260], gLang[261], gLang[262]);
		this.__arrMois = new Array(gLang[263], gLang[264], gLang[265], gLang[266], gLang[267], gLang[268], gLang[269], gLang[270], gLang[271], gLang[272], gLang[273], gLang[274]);
				
		this.__rcvDate = rcvDate;
		this.__rtnDate = rtnDate;
		this.__str = '';
		this.modifyDate();
		this.parseFormat();
		};		

	/********************************************************************************************************/
	private function modifyDate(Void):Void{
		if (this.__rcvDate.substring(4,5) == '-'){
			// 2000-01-08 00:00:00
			this.__year = this.__rcvDate.substring(0,4);
			this.__month = this.__rcvDate.substring(5,7);
			this.__day = this.__rcvDate.substring(8,10);
			this.__hours = this.__rcvDate.substring(11,13);
			this.__min = this.__rcvDate.substring(14,16);
			this.__sec = this.__rcvDate.substring(17,19);
			
			var d = new Date(Number(this.__year),Number(this.__month),Number(this.__day), Number(this.__hours), Number(this.__min), Number(this.__sec));
			this.__dayOfWeek = d.getDay().toString();
			
		}else if (this.__rcvDate.length == 14){
			// 20050806115740
			this.__year = this.__rcvDate.substring(0,4);
			this.__month = this.__rcvDate.substring(4,6);
			this.__day = this.__rcvDate.substring(6,8);
			this.__hours = this.__rcvDate.substring(8,10);
			this.__min = this.__rcvDate.substring(10,12);
			this.__sec = this.__rcvDate.substring(12,14);
			
			var d = new Date(Number(this.__year),Number(this.__month),Number(this.__day), Number(this.__hours), Number(this.__min), Number(this.__sec));
			this.__dayOfWeek = d.getDay().toString();
			
			
		}else if (this.__rcvDate.length == 10){
			// 1020097103
			
			var ts = this.__rcvDate * 1000 //en millisecond
			var d = new Date(ts); //la date selon le timestamp
			
			this.__year = d.getFullYear().toString();
			this.__month = (d.getMonth()+1).toString();
			this.__day = d.getDate();
			this.__dayOfWeek = d.getDay().toString();
			
			this.__hours = d.getHours().toString();
			this.__min = d.getMinutes().toString();
			this.__sec = d.getSeconds().toString();
			}
		};
	
	/************************************************************************************************************************/
	private function parseFormat(Void):Void{
		/*
		var arrStr:Array = new Array();
		for (var i = 0; i < this.__rtnDate.length; i++){
			arrStr.push(this.__rtnDate.charAt(i));
			}
		*/	
		for(var i=0; i<this.__rtnDate.length; i++){
			switch(this.__rtnDate.charAt(i)){
				case 'd':	
					this.__str += this.__day;
					break;
				case 'D':
					this.__str += this.__arrJour[this.__dayOfWeek];
					break;
				case 'F':
					this.__str += this.__arrMois[(this.__month)-1];
					break;
				case 'm':
					this.__str += this.__month;
					break;
				case 'Y':
					this.__str += this.__year;
					break;
				case 'H':
					this.__str += this.formatForTwoSpaces(this.__hours);
					break;
				case 'i':
					this.__str += this.formatForTwoSpaces(this.__min);
					break;
				case 's':
					this.__str += this.formatForTwoSpaces(this.__sec);
					break;
				default:
					this.__str += this.__rtnDate.charAt(i);
					break;	
				}
			}
		};
		
	/******************************************************************************************************************************************************/

	private function formatForTwoSpaces(strToFormat:String):String{
		if(strToFormat.length < 2){
			strToFormat = '0' + strToFormat;
			}
		return strToFormat;
		}
		
	/******************************************************************************************************************************************************/

	public function destroy(Void):Void{
		this = null;
		delete this;
		};

	public function printDate(Void):String{
		return this.__str;
		};
	/*	
	public function getClass(Void):CieDate{
		return this;
		};
		
	public function getClassName(Void):String{
		return __className;
		};	
	*/
	};