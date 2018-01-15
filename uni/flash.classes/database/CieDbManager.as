import database.CieDbAccess;

dynamic class database.CieDbManager{
	
	static private var __className = 'CieDbManager';
	static private var __instance:CieDbManager;
	
	private static var TIMERGC:Number = 60000;
	private var TMPTABLENAME:Array;
		
	private var __dbInst:CieDbAccess;
	private var __isConnected:Boolean;
	private var __intervalGC:Number;
	
	private function CieDbManager(Void){
		this.TMPTABLENAME = new Array();
		this.TMPTABLENAME['salon'] = 'tmp_salon';
		this.TMPTABLENAME['carnet'] = 'tmp_carnet';
		this.TMPTABLENAME['listenoire'] = 'tmp_listenoire';
		this.TMPTABLENAME['communications'] = 'tmp_communications';
		this.TMPTABLENAME['quiaconsulte'] = 'tmp_quiaconsulte';
				
		this.__isConnected = false;
		this.__dbInst = new CieDbAccess();
		};
		
	static public function getInstance(Void):CieDbManager{
		if(__instance == undefined) {
			__instance = new CieDbManager();
			}
		return __instance;
		};
	
	/*	
	public function getClassName(Void):String{
		return __className;
		};
		
	public function getClass(Void):CieDbManager{
		return this;
		};
	*/	

	public function reset(Void):Void{
		clearInterval(this.__intervalGC);
		};

	/**************************************************************************************************************/	
	
	public function connectDB(dbname:String, psw:String, cversion:String):Boolean{
		if(dbname != undefined && psw != undefined && cversion != undefined){
			if(this.__dbInst.connectDB(dbname, psw, cversion)){	
				this.__isConnected = true;
				this.__intervalGC = setInterval(this, 'runGC', TIMERGC);
				return true;	
				}
			}
		return false;	
		};
		
	/**************************************************************************************************************/	

	public function runGC(Void):Void{
		clearInterval(this.__intervalGC);
		if(this.__isConnected){
			this.dbSalonGC();
			this.__intervalGC = setInterval(this, 'runGC', TIMERGC);
			}
		};
		
	/**************************************************************************************************************/	
		
	public function compactDB(dbname:String, psw:String, cversion:String):Boolean{
		if(this.__dbInst != undefined){
			if(this.__dbInst.compactDB(dbname, psw, cversion)){
				return true;
			}else{
				return false;
				}
			}
		return false;	
		};
		
	/**************************************************************************************************************/	
		
	public function queryDB(strSQL:String):Boolean{
		if(strSQL != '' && strSQL != undefined){
			mdm.Database.MSAccess.runQuery(strSQL);
			return true;
			/*
			if(this.__dbInst.queryDB(strSQL)){
				return true;
				}
			*/	
			}
		return false;	
		};
		
	/**************************************************************************************************************/	
		
	public function selectDB(strSQL:String):Array{	
		if(strSQL != '' && strSQL != undefined){
			if(this.__dbInst.selectDB(strSQL)){
				//Debug("COUNT: " + mdm.Database.MSAccess.getRecordCount());
				return this.__dbInst.getData();
				//have to patch the Memo fiel in MDM 2.5.0.33
				}
			}
		return [];
		};
		
	/**************************************************************************************************************/	
		
	public function dbKeyExist(ctype:String, cfind:String):Boolean{
		/*
		if(ctype != undefined && cfind != undefined && ctype != '' && cfind != ''){
			if(ctype == 'no_publique'){
				this.__dbInst.selectDB("SELECT members." + ctype + " FROM members WHERE members." + ctype + " = " + cfind + ";");
				if(this.__dbInst.getRowCount()){
					return true;
					}
			}else if(ctype == 'pseudo'){
				this.__dbInst.selectDB("SELECT members." + ctype + " FROM members WHERE members." + ctype + " = '" + cfind + "';");
				if(this.__dbInst.getRowCount()){
					return true;
					}
				}
			}
		return false;
		*/
		//we will use a global var containing all no_publique insertewd in the DB, because it's to slow 
		if(gDbKey[cfind] == undefined){
			return false;
		}else{
			return true;
			}
		};
		
	/**************************************************************************************************************/	
		
	public function clearDB(stayConnect:Boolean):Void{
		if(stayConnect){
			this.__dbInst.queryDB("DELETE * FROM instant;");
			this.__dbInst.queryDB("DELETE * FROM courrier;");
			this.__dbInst.queryDB("DELETE * FROM attachement;");
			this.__dbInst.queryDB("DELETE * FROM members;");
			this.__dbInst.queryDB("DELETE * FROM vocal;");				
			for(var o in TMPTABLENAME){
				this.__dbInst.queryDB("DELETE * FROM " + TMPTABLENAME[o] + ";");		
				}
		}else{
			this.__dbInst.closeConn();
			}
		};
		
	/**************************************************************************************************************/	
		
	public function dbSalonGC(Void):Void{
		//Debug("dbSalonGC");
		//members.active = '4' :: that it's completely disconnect not only offline state
		
		//get  no_publique to delete from the global var gDbKey
		var arrRows:Array = this.selectDB("SELECT members.no_publique FROM members WHERE members.active = '4' AND members.no_publique NOT IN (SELECT " + TMPTABLENAME['salon'] + ".no_publique FROM " + TMPTABLENAME['salon'] + ") AND members.msg_express = '0' AND members.msg_courrier = '0' AND members.msg_vocal = '0' AND members.msg_instant = '0' AND members.msg_carnet = '0' AND members.msg_listenoire = '0' AND members.msg_quiaconsulte = '0';");
		//delete from DB
		this.__dbInst.queryDB("DELETE * FROM members WHERE members.active = '4' AND members.no_publique NOT IN (SELECT " + TMPTABLENAME['salon'] + ".no_publique FROM " + TMPTABLENAME['salon'] + ") AND members.msg_express = '0' AND members.msg_courrier = '0' AND members.msg_vocal = '0' AND members.msg_instant = '0' AND members.msg_carnet = '0' AND members.msg_listenoire = '0' AND members.msg_quiaconsulte = '0';");
		var cmptDel:Number = 0;
		//delete vars
		for(var o in arrRows){
			cmptDel++;
			delete gDbKey[arrRows[o][0]];
			}
		Debug('GC_COLLECTOR_DB: ' + cmptDel);	
		};
		
	/**************************************************************************************************************/	
	
	public function getTmpPageRow(page:Number, row:Number, tmptable:String):Array{
	
		//ok even if row=10 we have to bring back one more, to know if we have a page next or not
	
		return this.selectDB("SELECT " + TMPTABLENAME[tmptable] + ".no_publique, members.pseudo, members.age, members.ville_id, members.region_id, members.code_pays, members.album, members.photo, members.vocal, members.membership, members.orientation, members.sexe, members.titre, members.relation, members.etat_civil " + 
							" FROM " + TMPTABLENAME[tmptable] + ", members " + 
							"WHERE " + TMPTABLENAME[tmptable] + ".cmpt > " + (page * row) + 
							" AND " + TMPTABLENAME[tmptable] + ".cmpt <= " + (((page + 1)* row) + 1) + 
							" AND " + TMPTABLENAME[tmptable] + ".no_publique = members.no_publique " + 
							" ORDER BY " + TMPTABLENAME[tmptable] + ".cmpt;");
		};
	
	/**************************************************************************************************************/	
		
	public function createTmpTable(tmptable:String):Boolean{
		if(this.__dbInst.queryDB("CREATE TABLE " + TMPTABLENAME[tmptable] + " (cmpt COUNTER, no_publique INTEGER PRIMARY KEY);")){
			return true;
			}
		return false;
		};
		
	/**************************************************************************************************************/	
		
	public function createTmpTableCommunications(Void):Boolean{
		if(this.__dbInst.queryDB("CREATE TABLE tmp_communications (cmpt COUNTER, no_publique INTEGER PRIMARY KEY, cdate TEXT(20), ctype TEXT(20), direction TEXT(20), lu TEXT(20));")){
			return true;
			}
		return false;
		};		
		
	/**************************************************************************************************************/	
		
	public function changeTmpTable(tmptable:String, where:String, csort:String):Boolean{
		this.dropTmpTable(tmptable);
		this.createTmpTable(tmptable);
		if(csort == undefined){
			csort = 'members.pseudo';
			}
		if(this.__dbInst.queryDB("INSERT INTO " + TMPTABLENAME[tmptable] + "(no_publique) SELECT members.no_publique FROM members " + where + " ORDER BY "+ csort + ";")){
			return true;
			}
		return false;
		};	
	
	/**************************************************************************************************************/	
	
	public function getTmpPageRowCommunications(page:Number, row:Number):Array{
		var strSql = "SELECT members.no_publique, members.pseudo, members.age, members.ville_id, members.region_id, members.code_pays, members.album, members.photo, members.vocal, members.membership, members.orientation, members.sexe, members.titre, members.relation, members.etat_civil, tmp_communications.ctype, tmp_communications.cdate, tmp_communications.direction, tmp_communications.lu FROM tmp_communications, members WHERE tmp_communications.cmpt > " + (page * row) + " AND tmp_communications.cmpt <= " + (((page + 1)* row) + 1) + " AND tmp_communications.no_publique = members.no_publique ORDER BY tmp_communications.cmpt;";
		return this.selectDB(strSql);
		};
				
	/**************************************************************************************************************/	
	public function changeTmpTableCommunications(csort:String):Boolean{
		this.dropTmpTable('communications');
		if(this.createTmpTableCommunications()){
			//for express
			var msgLu:String = '0';
			//container of all	
			var arrAll:Array = new Array();
			//container of query result by ctype
			var arrQuery:Array = new Array();
			//queqry for all typ of mesassges
			arrQuery['instant'] = cDbManager.selectDB("SELECT no_publique, cdate, type, lu FROM instant;");
			arrQuery['express'] = cDbManager.selectDB("SELECT no_publique, msg_express_date, msg_express FROM members WHERE msg_express <> '0'");
			arrQuery['courriel'] = cDbManager.selectDB("SELECT no_publique, cdate, type, lu FROM courrier;");
			arrQuery['vocal'] = cDbManager.selectDB("SELECT no_publique, cdate, '0', lu FROM vocal");
			//loop trought all type of messages
			for(var o in arrQuery){
				//lop trought all msg
				if(o == 'express'){
					//beacuse it's in the members table of the uni DB
					//express msg doesn't have the same flags, 2=lu, 1=nonlu, 0=nomsgexpress 
					for(var p in arrQuery[o]){
						//push it into the all array
						if(arrAll[arrQuery[o][p][0]] != undefined){
							//check la date
							if(Number(arrQuery[o][p][1]) > Number(arrAll[arrQuery[o][p][0]][0])){
								//replace with new date enad new ctype
								//keep the non-lu even if other are lu
								if(arrAll[arrQuery[o][p][0]][3] == '0'){
									arrAll[arrQuery[o][p][0]] = new Array(arrQuery[o][p][1], o, 'undefined', '0');
								}else{
									if(arrQuery[o][p][2] == '1'){
										msgLu = '0';
									}else{
										msgLu = '1';
										}
									arrAll[arrQuery[o][p][0]] = new Array(arrQuery[o][p][1], o, 'undefined', msgLu);
									}
							}else if(arrQuery[o][p][2] == '1'){ //check si le message est non-lu pour le flag
								arrAll[arrQuery[o][p][0]][3] = '0';				
								}
						}else{
							if(arrQuery[o][p][2] == '1'){
								msgLu = '0';
							}else{
								msgLu = '1';
								}
							//put it in the all array with no_publique has a Key
							arrAll[arrQuery[o][p][0]] = new Array(arrQuery[o][p][1], o, 'undefined', msgLu);
							}
						}					
				}else{ //other type of messages
					for(var p in arrQuery[o]){
						//push it into the all array
						if(arrAll[arrQuery[o][p][0]] != undefined){
							//check la date
							if(Number(arrQuery[o][p][1]) > Number(arrAll[arrQuery[o][p][0]][0])){
								//replace with new date enad new ctype
								//keep the non-lu even if other are lu
								if(arrAll[arrQuery[o][p][0]][3] == '0'){
									arrAll[arrQuery[o][p][0]] = new Array(arrQuery[o][p][1], o, arrQuery[o][p][2], '0');
								}else{
									arrAll[arrQuery[o][p][0]] = new Array(arrQuery[o][p][1], o, arrQuery[o][p][2], arrQuery[o][p][3]);
									}
							}else if(arrQuery[o][p][3] == '0'){ //check si le message est non-lu pour le flag
								arrAll[arrQuery[o][p][0]][3] = '0';				
								}
						}else{
							//put it in the all array with no_publique has a Key
							arrAll[arrQuery[o][p][0]] = new Array(arrQuery[o][p][1], o, arrQuery[o][p][2], arrQuery[o][p][3]);
							}
						}
					}	
				}	
			//OK we have an array have to sort it now,but dumb macromedia use bubble sort so we have to found another way
			//delete all from the temporary table that will hold the arrAll
			this.__dbInst.queryDB("DELETE * FROM tmp_sort;");
			//loop throught all data
			for(var o in arrAll){
				//insert intot the tmp_sort table 
				this.__dbInst.queryDB("INSERT INTO tmp_sort (no_publique, cdate, ctype, direction, lu) VALUES(" + o + ",'" + arrAll[o][0] + "','" + arrAll[o][1] + "','" + arrAll[o][2] + "','" + arrAll[o][3] + "');");
				}
			//now that we have a table lets do a tmp_table sorted by date with number incrmentation for page navigation
			this.__dbInst.queryDB("INSERT INTO tmp_communications (no_publique, cdate, ctype, direction, lu) SELECT no_publique, cdate, ctype, direction, lu FROM tmp_sort ORDER BY cdate DESC");
			return true;
			}
		return false;
		};	

	/**************************************************************************************************************/	

	public function dropTmpTable(tmptable:String):Boolean{
		if(this.__dbInst.queryDB("DROP TABLE " + TMPTABLENAME[tmptable] + ";")){
			return true;
			}
		return false;	
		};	
		
	/**************************************************************************************************************/	
		
	public function disconnectDB(Void):Void{
		this.reset();
		this.__dbInst.closeConn();
		};
		
	/**************************************************************************************************************/	
	
	public function destroy(Void):Void{
		this.__dbInst = null;
		delete this.__dbInst;
		this = null;
		};

	}