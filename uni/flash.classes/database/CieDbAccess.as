/*

created by: Dwizzel
MSACCESS 2000 DATABASE LAYER


*/


dynamic class database.CieDbAccess{

	static private var __className:String = 'CieDbAccess';
	private var __dbname:String;
	private var __psw:String;
	private var __version:String;
	
	public function CieDbAccess(Void){
		//nothing to do
		};

	public function connectDB(dbname:String, psw:String, cversion:String):Boolean{
		this.__dbname = dbname;
		this.__psw = psw;
		this.__version = cversion;
		mdm.Database.MSAccess.connectAbs(mdm.Application.path + this.__dbname, this.__psw);
		if(mdm.Database.MSAccess.success()){
			return true;
			}
		return false;	
		};
		
	public function closeConn(Void):Void{
		mdm.Database.MSAccess.close();
		};
		
	public function compactDB(dbname:String, psw:String, cversion:String):Boolean{
		mdm.Database.MSAccess.compact(mdm.Application.path + dbname, cversion, psw);
		if(this.getError()){
			return false;
			}
		return true;
		};
		
	public function queryDB(strSQL:String):Boolean{
		if(BC.__user.__debugquery){
			Debug("\n" + strSQL + "\n");
			}
		mdm.Database.MSAccess.runQuery(strSQL);
		if(this.getError()){
			return false;
			}
		return true;	
		};	
		
	
	public function selectDB(strSQL:String):Boolean{
		mdm.Database.MSAccess.select(strSQL);
		if(this.getError()){
			return false;
			}
		return true;	
		};	
		
	public function getData(Void):Array{
		if(this.getRowCount()){
			return mdm.Database.MSAccess.getData();
			}
		return [];	
		};
		
	public function getRowCount(Void):Boolean{
		if(mdm.Database.MSAccess.getRecordCount()){
			return true;
			}
		return false;	
		};
	
	private function getError(Void):Boolean{
		if(mdm.Database.MSAccess.error() == false){
			var errorDetails = mdm.Database.MSAccess.errorDetails;
			//Debug(errorDetails);
			return true;
			}
		return false;	
		};
		
	public function destroy(Void):Void{
		delete this;
		};	
	/*	
	public function getClass(Void):CieDbAccess{
		return this;
		};	
	
	public function getClassName(Void):String{
		return __className;
		};
	*/
	}