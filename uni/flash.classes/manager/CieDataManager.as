/*

Insert les données venant du web vers DB local

*/

dynamic class manager.CieDataManager{
	
	static private var __className:String = 'CieDataManager';
	static private var __instance:CieDataManager;
	
	private var __finished:Boolean;
	private var __bCritLoaded:Boolean;
	private var __arrJobs:Array;
	private var __iJobCount:Number;
	private var __arrOnlineMembers:Array;
	
	private var __iLoadState:Number;

	//interval
	private var __intervalParsing:Number;
	private var __iIntervalIndex:Number;
		
	private function CieDataManager(Void){
		this.__iLoadState = 30;
		this.__iJobCount = 0;
		this.__arrJobs = new Array();
		this.__iIntervalIndex = 0;
		this.__finished = false;
		this.__bCritLoaded = false;
		};
		
	static public function getInstance(Void):CieDataManager{
		if(__instance == undefined) {
			__instance = new CieDataManager();
			}
		return __instance;
		};	
	/*	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieDataManager{
		return this;
		};	
	*/	
	/******************************************************************************************************************************************/
	
	public function getWelcome(Void):Void{
		var arrD = new Array();
			arrD['methode'] = 'welcome';
			arrD['action'] = '';
			arrD['arguments'] = '';
		cReqManager.addRequest(arrD, this.cbWelcome, null);
		};
	
		
		
	/******************************************************************************************************************************************/	
	
	public function getCritere(Void):Void{
		var arrD = new Array();
			arrD['methode'] = 'salon';
			arrD['action'] = 'getcritere';
			arrD['arguments'] = '';
		cReqManager.addRequest(arrD, this.cbCritere, null);
		};
		
	public function cbCritere(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		// parsing XML
		cDataManager.parseXmlCritere(obj.__req.getXml().firstChild);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};
		
	public function parseXmlCritere(xmlNode:XMLNode):Void{
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.firstChild.nodeType == 1){
				this.parseXmlCritere(currNode);
			}else{
				BC.__user.__critere[currNode.attributes.n] = unescape(String(currNode.firstChild.nodeValue));
				}
			}
		this.__bCritLoaded = true;	
		};

	public function isCritLoaded(Void):Boolean{
		return this.__bCritLoaded;
		};	
			
	/******************************************************************************************************************************************/	
	
	public function startJobs(Void):Void{
		//init type and order with the config files
		this.__arrJobs = BC.__user.__fetchData.split(',');
		//will be called by CieLogin when we have a PHPSESSION that is valid
		if(this.__arrJobs[this.__iJobCount] != undefined){
			Debug('START JOB: ' + this.__arrJobs[this.__iJobCount]);
			this.fetchData(this.__arrJobs[this.__iJobCount]);
		}else{
			this.__finished = true;
			}
		};
		
	public function isJobsFinished(Void):Boolean{
		return this.__finished;
		};
		
	public function reset(Void):Void{
		this.__iLoadState = 30;
		this.__iJobCount = 0;
		this.__iIntervalIndex = 0;
		//clear interval
		clearInterval(this.__intervalParsing);
		this.__intervalParsing = null;
		
		this.__finished = false;
		this.__bCritLoaded = false;
				
		//reset the folders
		//BC.__user.__folders = new Array();
		};
		
	/******************************************************************************************************************************************/	
	
	public function fetchData(section:String):Void{
		var arrD = new Array();
		arrD['methode'] = section;
		arrD['action'] = 'listeconcat'; //liste avec l'infos des members concatener separe par des pipes "|"
		arrD['arguments'] = '';
		cReqManager.addRequest(arrD, this.cbFetchData, {__section: section});
		};
		
	public function cbFetchData(prop, oldVal:Number, newVal:Number, obj:Object):Void{
		// parsing XML recupere le scope de la classe en meme temps
		cDataManager.startParsingXml(obj.__req.getXml(), obj.__super.__section);
		// remove request
		cReqManager.removeRequest(obj.__req.getID());
		};	
			
	public function startParsingXml(xmlNode:XMLNode, section:String):Void{
		if(section == 'carnet'){
			Debug('CARNET_XML_START');
			this.__intervalParsing = setInterval(this, 'parseXmlCarnet', 1, xmlNode.firstChild, 0);	
		}else if(section == 'listenoire'){
			Debug('LISTENOIRE_XML_START');
			this.__intervalParsing = setInterval(this, 'parseXmlListeNoire', 1, xmlNode.firstChild, 0);		
		}else if(section == 'express'){
			Debug('EXPRESS_XML_START');
			this.__intervalParsing = setInterval(this, 'parseXmlExpress', 1, xmlNode.firstChild, 0);
		}else if(section == 'instant'){
			Debug('INSTANT_XML_START');
			this.__intervalParsing = setInterval(this, 'parseXmlInstant', 1, xmlNode.firstChild, 0);
		}else if(section == 'vocal'){
			Debug('VOCAL_XML_START');
			this.__intervalParsing = setInterval(this, 'parseXmlVocal', 1, xmlNode.firstChild, 0);
		}else if(section == 'courrier'){
			Debug('COURRIER_XML_START');
			this.__intervalParsing = setInterval(this, 'parseXmlCourrier', 1, xmlNode.firstChild, 0);			
		}else if(section == 'quiaconsulte'){
			Debug('QUIACONSULTE_XML_START');
			this.__intervalParsing = setInterval(this, 'parseXmlQuiAConsulte', 1, xmlNode.firstChild, 0);			
			}
		};
		
		
	/*************************************************************************************************************************************************/
	
	public function parseXmlCarnet(xmlNode:XMLNode, iIndex:Number):Void{
		clearInterval(this.__intervalParsing);
		
		//check if undefined
		if(xmlNode != undefined){
			if(iIndex < xmlNode.childNodes.length){
				//check if we have an error must ne the first node with tag n='error'
				if(xmlNode.childNodes[iIndex].attributes.n == 'error'){
					Debug('***ERR (carnet): ' + xmlNode.childNodes[iIndex].firstChild.nodeValue);
					delete xmlNode; 
					cToolManager.getTool('messages', 'bottin').setLoaderProgress(0.5);
					Debug("CARNET_XML_FINISH");
					//next job
					this.__iJobCount++;
					this.startJobs();
				}else{
					cToolManager.getTool('messages', 'bottin').setLoaderProgress((iIndex/xmlNode.childNodes.length)/2);
					var arrInfos:Array = xmlNode.childNodes[iIndex].childNodes[0].firstChild.nodeValue.split('|');
					//rajout a la variable global contenant les no_pub des membres du carnet
					gDbCarnet[arrInfos[0]] = 1;
					//DB
					if(gDbKey[arrInfos[0]] != undefined){
					//if(cDbManager.dbKeyExist('no_publique', arrInfos[0])){
						cDbManager.queryDB("UPDATE members SET msg_carnet = '1' WHERE no_publique = " + arrInfos[0] + ";");
					}else{
						cDbManager.queryDB("INSERT INTO members (no_publique, pseudo, age, ville_id, region_id, code_pays, album, photo, vocal, membership, orientation, sexe, titre, relation, etat_civil, msg_carnet) VALUES ('" + arrInfos[0] + "','" + arrInfos[1] + "','" + arrInfos[2] + "','" + arrInfos[3] + "','" + arrInfos[4] + "','" + arrInfos[5] + "','" + arrInfos[6] + "','" + arrInfos[7] + "','" + arrInfos[8] + "','" + arrInfos[9] + "','" + arrInfos[10] + "','" + arrInfos[11] + "','" + arrInfos[12] + "','" + arrInfos[13] + "','" + arrInfos[14] + "', '1');");
						gDbKey[arrInfos[0]] = 1;
						}
					iIndex++;	
					this.__intervalParsing = setInterval(this, 'parseXmlCarnet', 1, xmlNode, iIndex);	
					}
			}else{
				delete xmlNode; 
				cToolManager.getTool('messages', 'bottin').setLoaderProgress(0.5);
				Debug("CARNET_XML_FINISH");
				//next job
				this.__iJobCount++;
				this.startJobs();
				}
		}else{
			Debug('***ERR (carnet): xmlNode is undefined');
			delete xmlNode; 
			cToolManager.getTool('messages', 'bottin').setLoaderProgress(0.5);
			Debug("CARNET_XML_FINISH_WITH_ERROR");
			//next job
			this.__iJobCount++;
			this.startJobs();
			}
		};
		
	/*************************************************************************************************************************************************/
	
	public function parseXmlListeNoire(xmlNode:XMLNode, iIndex:Number):Void{
		clearInterval(this.__intervalParsing);
		
		//check if undefined
		if(xmlNode != undefined){
			if(iIndex < xmlNode.childNodes.length){
				//check if we have an error must ne the first node with tag n='error'
				if(xmlNode.childNodes[iIndex].attributes.n == 'error'){
					Debug('***ERR (listenoire): ' + xmlNode.childNodes[iIndex].firstChild.nodeValue);
					delete xmlNode; 
					cToolManager.getTool('messages', 'bottin').setLoaderIcon(false);
					Debug("LISTENOIRE_XML_FINISH");
					//next job
					this.__iJobCount++;
					this.startJobs();
				}else{
					//setLoadingBarData((iIndex/xmlNode.childNodes.length) * 100);
					cToolManager.getTool('messages', 'bottin').setLoaderProgress(((iIndex/xmlNode.childNodes.length)/2) + 0.5);
					var arrInfos:Array = xmlNode.childNodes[iIndex].childNodes[0].firstChild.nodeValue.split('|');
					if(gDbKey[arrInfos[0]] != undefined){
					//if(cDbManager.dbKeyExist('no_publique', arrInfos[0])){
						cDbManager.queryDB("UPDATE members SET msg_listenoire = '1' WHERE no_publique = " + arrInfos[0] + ";");
					}else{
						cDbManager.queryDB("INSERT INTO members (no_publique, pseudo, age, ville_id, region_id, code_pays, album, photo, vocal, membership, orientation, sexe, titre, relation, etat_civil, msg_listenoire) VALUES ('" + arrInfos[0] + "','" + arrInfos[1] + "','" + arrInfos[2] + "','" + arrInfos[3] + "','" + arrInfos[4] + "','" + arrInfos[5] + "','" + arrInfos[6] + "','" + arrInfos[7] + "','" + arrInfos[8] + "','" + arrInfos[9] + "','" + arrInfos[10] + "','" + arrInfos[11] + "','" + arrInfos[12] + "','" + arrInfos[13] + "','" + arrInfos[14] + "', '1');");
						gDbKey[arrInfos[0]] = 1;
						}
					iIndex++;	
					this.__intervalParsing = setInterval(this, 'parseXmlListeNoire', 1, xmlNode, iIndex);	
					}
			}else{
				delete xmlNode; 
				cToolManager.getTool('messages', 'bottin').setLoaderIcon(false);
				Debug("LISTENOIRE_XML_FINISH");
				//next job
				this.__iJobCount++;
				this.startJobs();
				}
		}else{
			Debug('***ERR (listenoire): xmlNode is undefined');
			delete xmlNode; 
			cToolManager.getTool('messages', 'bottin').setLoaderIcon(false);
			Debug("LISTENOIRE_XML_FINISH_WITH_ERROR");
			//next job
			this.__iJobCount++;
			this.startJobs();
			}
		};	
		
	/*************************************************************************************************************************************************/
	
	public function parseXmlExpress(xmlNode:XMLNode, iIndex:Number):Void{
		clearInterval(this.__intervalParsing);
		
		//check if undefined
		if(xmlNode != undefined){
			if(iIndex < xmlNode.childNodes.length){
				//check if we have an error must ne the first node with tag n='error'
				if(xmlNode.childNodes[iIndex].attributes.n == 'error'){
					Debug('***ERR (express): ' + xmlNode.childNodes[iIndex].firstChild.nodeValue);
					delete xmlNode; 
					cToolManager.getTool('messages', 'message').setLoaderProgress(0.6);
					Debug("EXPRESS_XML_FINISH");
					//next job
					this.__iJobCount++;
					this.startJobs();	
				}else{
					//setLoadingBarData((iIndex/xmlNode.childNodes.length) * 100);
					cToolManager.getTool('messages', 'message').setLoaderProgress(((iIndex/xmlNode.childNodes.length)/5) + 0.4);
					var arrInfos:Array = xmlNode.childNodes[iIndex].childNodes[0].firstChild.nodeValue.split('|');
					//check if msg is lu or non-lu
					var msgState:Number = 1;
					if(arrInfos[16] == '1'){
						msgState = 2; 
						//pour la BD
						//2=lu 
						//0=pasdeexpress 
						//1=msgexpressmaisnonlu
						}
					if(gDbKey[arrInfos[0]] != undefined){
					//if(cDbManager.dbKeyExist('no_publique', arrInfos[0])){
						cDbManager.queryDB("UPDATE members SET msg_express = '" + msgState + "', msg_express_date = '" + arrInfos[15] + "' WHERE no_publique = " + arrInfos[0] + ";");
					}else{
						cDbManager.queryDB("INSERT INTO members (no_publique, pseudo, age, ville_id, region_id, code_pays, album, photo, vocal, membership, orientation, sexe, titre, relation, etat_civil, msg_express_date, msg_express) VALUES ('" + arrInfos[0] + "','" + arrInfos[1] + "','" + arrInfos[2] + "','" + arrInfos[3] + "','" + arrInfos[4] + "','" + arrInfos[5] + "','" + arrInfos[6] + "','" + arrInfos[7] + "','" + arrInfos[8] + "','" + arrInfos[9] + "','" + arrInfos[10] + "','" + arrInfos[11] + "','" + arrInfos[12] + "','" + arrInfos[13] + "','" + arrInfos[14] + "','" + arrInfos[15] + "', '" + msgState + "');");
						gDbKey[arrInfos[0]] = 1;
						}
					iIndex++;	
					this.__intervalParsing = setInterval(this, 'parseXmlExpress', 1, xmlNode, iIndex);	
					}
			}else{
				delete xmlNode; 
				cToolManager.getTool('messages', 'message').setLoaderProgress(0.6);
				Debug("EXPRESS_XML_FINISH");
				//next job
				this.__iJobCount++;
				this.startJobs();
				}
		}else{
			Debug('***ERR (express): xmlNode is undefined');
			delete xmlNode; 
			cToolManager.getTool('messages', 'message').setLoaderProgress(0.6);
			Debug("EXPRESS_XML_FINISH_WITH_ERROR");
			//next job
			this.__iJobCount++;
			this.startJobs();
			}
		};		
	
	/*************************************************************************************************************************************************/
	
	public function parseXmlQuiAConsulte(xmlNode:XMLNode, iIndex:Number):Void{
		clearInterval(this.__intervalParsing);
		
		//check if undefined
		if(xmlNode != undefined){
			if(iIndex < xmlNode.childNodes.length){
				//check if we have an error must ne the first node with tag n='error'
				if(xmlNode.childNodes[iIndex].attributes.n == 'error'){
					Debug('***ERR (quiaconsulte): ' + xmlNode.childNodes[iIndex].firstChild.nodeValue);
					//xmlNode = null;
					delete xmlNode; 
					cToolManager.getTool('messages', 'message').setLoaderIcon(false);
					Debug("QUIACONSULTE_XML_FINISH");
					//next job
					this.__iJobCount++;
					this.startJobs();	
				}else{
					//setLoadingBarData((iIndex/xmlNode.childNodes.length) * 100);
					cToolManager.getTool('messages', 'message').setLoaderProgress(((iIndex/xmlNode.childNodes.length)/5) + 0.8);
					var arrInfos:Array = xmlNode.childNodes[iIndex].childNodes[0].firstChild.nodeValue.split('|');
					if(gDbKey[arrInfos[0]] != undefined){
					//if(cDbManager.dbKeyExist('no_publique', arrInfos[0])){
						cDbManager.queryDB("UPDATE members SET msg_quiaconsulte = '1', msg_quiaconsulte_date = '" + arrInfos[15] + "' WHERE no_publique = " + arrInfos[0] + ";");
					}else{
						cDbManager.queryDB("INSERT INTO members (no_publique, pseudo, age, ville_id, region_id, code_pays, album, photo, vocal, membership, orientation, sexe, titre, relation, etat_civil, msg_quiaconsulte_date, msg_quiaconsulte) VALUES ('" + arrInfos[0] + "','" + arrInfos[1] + "','" + arrInfos[2] + "','" + arrInfos[3] + "','" + arrInfos[4] + "','" + arrInfos[5] + "','" + arrInfos[6] + "','" + arrInfos[7] + "','" + arrInfos[8] + "','" + arrInfos[9] + "','" + arrInfos[10] + "','" + arrInfos[11] + "','" + arrInfos[12] + "','" + arrInfos[13] + "','" + arrInfos[14] + "','" + arrInfos[15] + "', '1');");
						gDbKey[arrInfos[0]] = 1;
						}
					iIndex++;	
					this.__intervalParsing = setInterval(this, 'parseXmlQuiAConsulte', 1, xmlNode, iIndex);	
					}
			}else{
				delete xmlNode; 
				cToolManager.getTool('messages', 'message').setLoaderIcon(false);
				Debug("QUIACONSULTE_XML_FINISH");
				//next job
				this.__iJobCount++;
				this.startJobs();
				}
		}else{
			Debug('***ERR (quiaconsulte): xmlNode is undefined');
			delete xmlNode; 
			cToolManager.getTool('messages', 'message').setLoaderIcon(false);
			//get if have new messages
			//cFunc.getNewMessagesFlag(); //was in CieLOgin
			Debug("QUIACONSULTE_XML_FINISH_WITH_ERROR");
			//next job
			this.__iJobCount++;
			this.startJobs();
			}
		};		

	
	/*************************************************************************************************************************************************/
	
	public function parseXmlInstant(xmlNode:XMLNode, iIndex:Number):Void{
		clearInterval(this.__intervalParsing);
		
		//check if undefined
		if(xmlNode != undefined  && xmlNode != ''){
			if(iIndex < xmlNode.childNodes.length){
				//check if we have an error must ne the first node with tag n='error'
				if(xmlNode.childNodes[iIndex].attributes.n == 'error'){
					Debug('***ERR (instant): ' + xmlNode.childNodes[iIndex].firstChild.nodeValue);
					delete xmlNode; 
					cToolManager.getTool('messages', 'message').setLoaderProgress(0.2);
					Debug("INSTANT_XML_FINISH");
					//next job
					this.__iJobCount++;
					this.startJobs();
				}else{
					cToolManager.getTool('messages', 'message').setLoaderProgress((iIndex/xmlNode.childNodes.length)/5);
					//user infos at childNode[0]
					var arrInfos:Array = xmlNode.childNodes[iIndex].childNodes[0].firstChild.nodeValue.split('|');
					if(gDbKey[arrInfos[0]] != undefined){
					//if(cDbManager.dbKeyExist('no_publique', arrInfos[0])){
						cDbManager.queryDB("UPDATE members SET msg_instant = '1' WHERE no_publique = " + arrInfos[0] + ";");
					}else{
						cDbManager.queryDB("INSERT INTO members (no_publique, pseudo, age, ville_id, region_id, code_pays, album, photo, vocal, membership, orientation, sexe, titre, relation, etat_civil, msg_instant) VALUES ('" + arrInfos[0] + "','" + arrInfos[1] + "','" + arrInfos[2] + "','" + arrInfos[3] + "','" + arrInfos[4] + "','" + arrInfos[5] + "','" + arrInfos[6] + "','" + arrInfos[7] + "','" + arrInfos[8] + "','" + arrInfos[9] + "','" + arrInfos[10] + "','" + arrInfos[11] + "','" + arrInfos[12] + "','" + arrInfos[13] + "','" + arrInfos[14] + "', '1');");
						gDbKey[arrInfos[0]] = 1;
						}
					//instant messages content at childNode[1]
					this.parseXmlInstantMessageRows(xmlNode.childNodes[iIndex].childNodes[1], arrInfos[0]);
					iIndex++;	
					this.__intervalParsing = setInterval(this, 'parseXmlInstant', 1, xmlNode, iIndex);
					}	
			}else{
				delete xmlNode; 
				cToolManager.getTool('messages', 'message').setLoaderProgress(0.2);
				Debug("INSTANT_XML_FINISH");
				//next job
				this.__iJobCount++;
				this.startJobs();
				}
		}else{
			delete xmlNode; 
			cToolManager.getTool('messages', 'message').setLoaderProgress(0.2);
			Debug("INSTANT_XML_FINISH_WITH_ERROR");
			//next job
			this.__iJobCount++;
			this.startJobs();
			}
		};	

	/*************************************************************************************************************************************************/
		
	//parse the instant messages	content messages
	private function parseXmlInstantMessageRows(xmlNode:XMLNode, iNoPub:Number):Void{	
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var arrInfos:Array = xmlNode.childNodes[i].childNodes[0].firstChild.nodeValue.split('|');
			cDbManager.queryDB("INSERT INTO instant (no_instant, type, cdate, msg, no_publique, lu) VALUES ('" + arrInfos[0] + "','" + arrInfos[1] + "','" + arrInfos[2] + "','" + arrInfos[3] + "','" + iNoPub + "','" + arrInfos[4] + "');");
			}
		};		
		
	/****************************************************************************************************************************************************************/
	
	//parse des vocaux
	public function parseXmlVocal(xmlNode:XMLNode, iIndex:Number):Void{
		clearInterval(this.__intervalParsing);
		
		//check if undefined
		if(xmlNode != undefined){
			if(iIndex < xmlNode.childNodes.length){
				//check if we have an error must ne the first node with tag n='error'
				if(xmlNode.childNodes[iIndex].attributes.n == 'error'){
					Debug('***ERR (vocal): ' + xmlNode.childNodes[iIndex].firstChild.nodeValue);
					delete xmlNode; 
					cToolManager.getTool('messages', 'message').setLoaderProgress(0.4);
					Debug("VOCAL_XML_FINISH");
					//next job
					this.__iJobCount++;
					this.startJobs();
				}else{
					cToolManager.getTool('messages', 'message').setLoaderProgress(((iIndex/xmlNode.childNodes.length)/5) + 0.2);
					//user infos at childNode[0]
					var arrInfos:Array = xmlNode.childNodes[iIndex].childNodes[0].firstChild.nodeValue.split('|');
					if(gDbKey[arrInfos[0]] != undefined){
					//if(cDbManager.dbKeyExist('no_publique', arrInfos[0])){
						cDbManager.queryDB("UPDATE members SET msg_vocal = '1' WHERE no_publique = " + arrInfos[0] + ";");
					}else{
						// 111891|frank34|41|72|9|CA|N|0|N|2|1|2|...|0010000|5
						cDbManager.queryDB("INSERT INTO members (no_publique, pseudo, age, ville_id, region_id, code_pays, album, photo, vocal, membership, orientation, sexe, titre, relation, etat_civil, msg_vocal) VALUES ('" + arrInfos[0] + "','" + arrInfos[1] + "','" + arrInfos[2] + "','" + arrInfos[3] + "','" + arrInfos[4] + "','" + arrInfos[5] + "','" + arrInfos[6] + "','" + arrInfos[7] + "','" + arrInfos[8] + "','" + arrInfos[9] + "','" + arrInfos[10] + "','" + arrInfos[11] + "','" + arrInfos[12] + "','" + arrInfos[13] + "','" + arrInfos[14] + "', '1');");
						gDbKey[arrInfos[0]] = 1;
						}
					//instant messages content at childNode[1]
					this.parseXmlVocalMessageRows(xmlNode.childNodes[iIndex].childNodes[1], arrInfos[0]);
					iIndex++;	
					this.__intervalParsing = setInterval(this, 'parseXmlVocal', 1, xmlNode, iIndex);
					}	
			}else{
				delete xmlNode; 
				cToolManager.getTool('messages', 'message').setLoaderProgress(0.4);
				Debug("VOCAL_XML_FINISH");
				//next job
				this.__iJobCount++;
				this.startJobs();
				}
		}else{
			delete xmlNode; 
			cToolManager.getTool('messages', 'message').setLoaderProgress(0.4);
			Debug("VOCAL_XML_FINISH_WITH_ERROR");
			//next job
			this.__iJobCount++;
			this.startJobs();
			}
		};	
	
	/*************************************************************************************************************************************************/
		
	//parse the vocal messages content messages
	private function parseXmlVocalMessageRows(xmlNode:XMLNode, iNoPub:Number):Void{	
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var arrInfos:Array = xmlNode.childNodes[i].childNodes[0].firstChild.nodeValue.split('|');
			cDbManager.queryDB("INSERT INTO vocal (no_vocal, cdate, lu, repondu, message_id, no_publique) VALUES ('" + arrInfos[0] + "','" + arrInfos[1] + "','" + arrInfos[2] + "','" + arrInfos[3] + "','" + arrInfos[4] + "','" + iNoPub + "');");
			}
		};	
	
	
	/****************************************************************************************************************************************************************/
	
	//parse des courrier
	public function parseXmlCourrier(xmlNode:XMLNode, iIndex:Number):Void{
		clearInterval(this.__intervalParsing);
		
		//check if undefined
		if(xmlNode != undefined){
			if(iIndex < xmlNode.childNodes.length){
				//check if we have an error must ne the first node with tag n='error'
				if(xmlNode.childNodes[iIndex].attributes.n == 'error'){
					Debug('***ERR (courrier): ' + xmlNode.childNodes[iIndex].firstChild.nodeValue);
					delete xmlNode; 
					cToolManager.getTool('messages', 'message').setLoaderProgress(0.8);
					Debug("COURRIER_XML_FINISH");
					//next job
					this.__iJobCount++;
					this.startJobs();
				}else{
					cToolManager.getTool('messages', 'message').setLoaderProgress(((iIndex/xmlNode.childNodes.length)/5) + 0.6);
					//user infos at childNode[0]
					var arrInfos:Array = xmlNode.childNodes[iIndex].childNodes[0].firstChild.nodeValue.split('|');
					if(gDbKey[arrInfos[0]] != undefined){
					//if(cDbManager.dbKeyExist('no_publique', arrInfos[0])){
						cDbManager.queryDB("UPDATE members SET msg_courrier = '1' WHERE no_publique = " + arrInfos[0] + ";");
					}else{
						cDbManager.queryDB("INSERT INTO members (no_publique, pseudo, age, ville_id, region_id, code_pays, album, photo, vocal, membership, orientation, sexe, titre, relation, etat_civil, msg_courrier) VALUES ('" + arrInfos[0] + "','" + arrInfos[1] + "','" + arrInfos[2] + "','" + arrInfos[3] + "','" + arrInfos[4] + "','" + arrInfos[5] + "','" + arrInfos[6] + "','" + arrInfos[7] + "','" + arrInfos[8] + "','" + arrInfos[9] + "','" + arrInfos[10] + "','" + arrInfos[11] + "','" + arrInfos[12] + "','" + arrInfos[13] + "','" + arrInfos[14] + "', '1');");
						gDbKey[arrInfos[0]] = 1;
						}
					//instant messages content at childNode[1]
					this.parseXmlCourrierMessages(xmlNode.childNodes[iIndex].childNodes[1], arrInfos[0]);
					iIndex++;	
					this.__intervalParsing = setInterval(this, 'parseXmlCourrier', 1, xmlNode, iIndex);
					}	
			}else{
				delete xmlNode; 
				cToolManager.getTool('messages', 'message').setLoaderProgress(0.8);
				Debug("COURRIER_XML_FINISH");
				//next job
				this.__iJobCount++;
				this.startJobs();
				}
		}else{
			delete xmlNode; 
			cToolManager.getTool('messages', 'message').setLoaderProgress(0.8);
			Debug("COURRIER_XML_FINISH_WITH_ERROR");
			//next job
			this.__iJobCount++;
			this.startJobs();
			}
		};	
	
	/****************************************************************************************************************************************************************/
	
	//parse the courrier	messages
	private function parseXmlCourrierMessages(xmlNode:XMLNode, iNoPub:Number):Void{	
		var strChamps:String = 'no_publique,';
		var strValues:String = iNoPub + ',';
		var iNoCourrier:Number = 0;
		var bQuery:Boolean = true;
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.firstChild.nodeType == 1){
				//found a ROW
				if(currNode.attributes.n == 'attachement'){
					this.parseXmlCourrierAttachement(currNode, iNoCourrier);
				}else{
					bQuery = false;
					this.parseXmlCourrierMessages(currNode, iNoPub);
					}
			}else{
				//found an ITEM
				if(currNode.attributes.n != 'attachement'){
					strChamps += currNode.attributes.n + ',';
					}
				if(currNode.attributes.n == 'no_courrier'){
					strValues += Number(currNode.firstChild.nodeValue) + ",";
					iNoCourrier = Number(currNode.firstChild.nodeValue);
				}else if(currNode.attributes.n == 'attachement'){	
					//do nothing it'a an error of attachement flag without any attachement
				}else{
					strValues += "'" + String(currNode.firstChild.nodeValue) + "',";
					}
				}
			}
		//finish the query
		if(bQuery){
			cDbManager.queryDB("INSERT INTO courrier (" + strChamps.substring(0, strChamps.length - 1) + ") VALUES (" + strValues.substring(0, strValues.length - 1) + ");");
			}		
		};		
		
	/****************************************************************************************************************************************************************/
	
	//parse the attachement des courriers
	private function parseXmlCourrierAttachement(xmlNode:XMLNode, iNoCourrier:Number):Void{
		var strChamps:String = 'no_courrier,';
		var strValues:String = iNoCourrier + ',';
		var bQuery:Boolean = true;
		for(var i=0; i<xmlNode.childNodes.length; i++){
			var currNode = xmlNode.childNodes[i];
			if(currNode.firstChild.nodeType == 1){
				//found a ROW
				bQuery = false;
				this.parseXmlCourrierAttachement(currNode, iNoCourrier);
			}else{
				//found an ITEM
				strChamps += currNode.attributes.n + ',';
				strValues += "'" + String(currNode.firstChild.nodeValue) + "',";
				}
			}
		//finish the query
		if(bQuery){
			cDbManager.queryDB("INSERT INTO attachement (" + strChamps.substring(0, strChamps.length - 1) + ") VALUES (" + strValues.substring(0, strValues.length - 1) + ");");
			}		
		};			
	
	}