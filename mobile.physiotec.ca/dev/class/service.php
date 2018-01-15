<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	Service API
@to check the include in mail class is done when just needed at the client process line 184, 197
*/


class Service {

	private $reg;
	private $pid;
	private $timestamp;
	private $sessionId;
	private $section;
	private $service;
	private $lang;
	private $data;
	private $errNum;
	private $iJsonErrorOnDataParse = 0;	
	private $className = 'Service';

	//-------------------------------------------------------------------------------------------------------------
	public function __construct(&$reg) {
		//vars
		$this->reg = $reg;
		$this->errNum = '';
		$this->pid = intVal($this->reg->get('req')->get('pid'));
		$this->timestamp = intVal($this->reg->get('req')->get('time'));
		$this->sessionId = $this->reg->get('req')->get('PHPSESSID');
		$this->section = $this->reg->get('req')->get('section');
		$this->service = $this->reg->get('req')->get('service');
		$this->lang = $this->reg->get('req')->get('lang');
		$this->data = false;

		//si data then json decode the string
		if($this->reg->get('req')->get('data').'' != ''){
			$this->data = json_decode($this->reg->get('req')->get('data'), true);
			//check si erreur json
			$this->iJsonErrorOnDataParse = json_last_error();
			if($this->iJsonErrorOnDataParse != JSON_ERROR_NONE){
				$this->data = false;
				}
		}else{
			//on existe pas alors on le rajoute au request object
			$this->reg->get('req')->set('data', '');
			}
		
		//si pas de langue on en set une par defaut dans le request object et le service var
		if(!isTrue($this->lang)){
			$this->lang = DEFAULT_LOCALE_LANG;
			$this->reg->get('req')->set('lang', $this->lang);
			}


		}

	//-------------------------------------------------------------------------------------------------------------	
	public function getClassName(){
		return $this->className;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function getClassObject(){
		return $this;
		}
	
	//-------------------------------------------------------------------------------------------------------------
	public function check() {
		
		//check le parse de this.data de json fait dans le construct
		if($this->iJsonErrorOnDataParse != 0){
			$this->setError($this->reg->get('err')->getJsonError($this->iJsonErrorOnDataParse));
			return false;
			}

		//we need them all except $data, but return different errors on missing parts of the call
		if(!isTrue($this->pid)){
			$this->setError(101);
			return false;
			}
		
		if(!isTrue($this->timestamp)){
			$this->setError(102);
			return false;
			}
		if(!isTrue($this->section)){
			$this->setError(103);
			return false;
			}
		if(!isTrue($this->service)){
			$this->setError(104);
			return false;
			}
		
		//check if we need a session id, only the login dont need some
		if(!($this->section == 'user' && $this->service == 'do-login') && !($this->section == 'user' && $this->service == 'auto-login')){
			if(!$this->reg->get('sess')->validate($this->sessionId)){
				$this->setError(107);
				return false;
				}
			}
		
		//custom php errors
		//trigger_error($this->className.'.check:: "'.$this->section.'", "'.$this->service.'"', E_USER_NOTICE);
		
		//
		return true;	
		}

	//-------------------------------------------------------------------------------------------------------------
	private function setError($num) {
		$this->errNum = intVal($num);
		}

	//-------------------------------------------------------------------------------------------------------------
	public function getErrorNum() {
		return $this->errNum;
		}

	//-------------------------------------------------------------------------------------------------------------
	public function getError() {
		return $this->reg->get('err')->get($this->errNum);
		}
	
	//-------------------------------------------------------------------------------------------------------------
	public function process() {
		//check the section first
		switch($this->section){
			case 'client':
				return $this->processClient();
				break;

			case 'search':	
				return $this->processSearch();
				break;

			case 'programs':	
				return $this->processPrograms();
				break;
	
			case 'user':
				return $this->processUser();
				break;

			case 'template':
				return $this->processTemplate();
				break;
	
			case 'options':	
				return $this->processOptions();
				break;

			case 'test':
				return $this->processTest();
				break;

			default:
				$this->setError(106);
				return false;
				break;
			}

		}

	//-------------------------------------------------------------------------------------------------------------
	private function processClient() {
		//instanciate object
		//require_once(DIR_CLASS.checkForDebugFile('client', $this->reg->get('req')->get('file')).'.php');
		require_once(DIR_CLASS.'client.php');
		$oClient = new Client($this->reg);
		//case services
		switch($this->service){
			case 'get-single-client-infos-by-id':
				return $oClient->getSingleClientInfosById($this->data);
				break;
			
			case 'get-client-listing-by-word': 
				return $oClient->getClientListingByWord($this->data);
				break;

			case 'add-new-client':
				//require_once(DIR_CLASS.'mail.php');
				return $oClient->addNewClient($this->data);
				break;

			case 'modify-client-infos':
				return $oClient->modifyClientInfos($this->data);
				break;

			case 'fetch-client-search-autocomplete': 
				return $oClient->fetchClientSearchAutoCompleteData($this->data);
				break;

			case 'send-program-email': 
				require_once(DIR_CLASS.'mail.php');
				return $oClient->sendProgramEmail($this->data);
				break;
	
			default:
				$this->setError(105);
				return false;
				break;
			}
		}

	//-------------------------------------------------------------------------------------------------------------
	private function processSearch(){
		//instanciate object
		//require_once(DIR_CLASS.checkForDebugFile('search', $this->reg->get('req')->get('file')).'.php');
		require_once(DIR_CLASS.'search.php');
		$oSearch = new Search($this->reg);
		//case services
		switch($this->service){
			case 'get-exercice-listing-by-word':
				return $oSearch->getExerciceListingByWord($this->data);
				break;

			case 'get-template-exercices-by-id':
				return $oSearch->getSearchTemplateExercicesById($this->data);
				break;

			case 'set-has-my-instruction':
				return $oSearch->setHasMyInstruction($this->data);
				break;

			case 'save-exercice-modifications':
				return $oSearch->saveExerciceModifications($this->data);
				break;

			case 'get-search-templates':
				return $oSearch->getSearchTemplates($this->data);
				break;

			case 'get-search-modules':
				return $oSearch->getSearchModules($this->data);
				break;
			
			case 'get-search-filters-name':
				return $oSearch->getSearchFiltersName($this->data);
				break;

			case 'fetch-exercice-search-autocomplete':
				return $oSearch->fetchExerciceSearchAutoCompleteData($this->data);
				break;
			
			default:
				$this->setError(105);
				return false;
				break;
			}	
		
		}
	
	//-------------------------------------------------------------------------------------------------------------
	private function processPrograms() {
		//instanciate object
		//require_once(DIR_CLASS.checkForDebugFile('programs', $this->reg->get('req')->get('file')).'.php');
		require_once(DIR_CLASS.'programs.php');
		$oPrograms = new Programs($this->reg);
		//case services
		switch($this->service){
			case 'save-program-modification':
				return $oPrograms->saveProgramModification($this->data);
				break;

			case 'create-new-program':
				return $oPrograms->createNewProgram($this->data);
				break;

			case 'modify-program-name':
				return $oPrograms->modifyProgramName($this->data);
				break;

			case 'is-new-program-name-exist':
				return $oPrograms->isNewProgramNameExist($this->data);
				break;

			case 'is-modified-program-name-exist':
				return $oPrograms->isModifiedProgramNameExist($this->data);
				break;

			case 'is-client-program-name-exist':
				return $oPrograms->isClientProgramNameExist($this->data);
				break;

			default:
				$this->setError(105);
				return false;
				break;
			}	
		
		}

	//-------------------------------------------------------------------------------------------------------------
	private function processUser() {	
		//instanciate object
		//require_once(DIR_CLASS.checkForDebugFile('user', $this->reg->get('req')->get('file')).'.php');
		require_once(DIR_CLASS.'user.php');
		$oUser = new User($this->reg);
		//case services
		switch($this->service){
			case 'get-modules-for-select-options':
				return $oUser->getModulesForSelectOptions($this->data);
				break;

			case 'get-print-parameters':
				return $oUser->getPrintParameters($this->data);
				break;

			case 'save-print-parameters':
				return $oUser->savePrintParameters($this->data);
				break;

			case 'do-login':
				return $oUser->doLogin($this->data);
				break;

			case 'auto-login':
				return $oUser->autoLogin($this->data);
				break;
			
			case 'do-logout':
				return $oUser->doLogout($this->data);
				break;

			case 'get-basics-infos':
				return $oUser->getBasicsInfos($this->data);
				break;

			case 'ping-service':
				return $oUser->pingService($this->data);
				break;

			default:
				$this->setError(105);
				return false;
				break;
			}
		return false;
		}

	//-------------------------------------------------------------------------------------------------------------
	private function processTemplate() {
		//instanciate object
		//require_once(DIR_CLASS.checkForDebugFile('template', $this->reg->get('req')->get('file')).'.php');
		require_once(DIR_CLASS.'template.php');
		$oTemplate = new Template($this->reg);
		//case services
		switch($this->service){
			case 'is-template-name-exist':
				return $oTemplate->isTemplateNameExist($this->data);
				break;

			case 'create-new-template':
				return $oTemplate->createNewTemplate($this->data);
				break;

			case 'save-template-modification':
				return $oTemplate->saveTemplateModification($this->data);
				break;

			default:
				$this->setError(105);
				return false;
				break;
			}	
		
		}

	//-------------------------------------------------------------------------------------------------------------
	private function processOptions() {	
		//instanciate object
		//require_once(DIR_CLASS.checkForDebugFile('options', $this->reg->get('req')->get('file')).'.php');
		require_once(DIR_CLASS.'options.php');
		$oOptions = new Options($this->reg);
		//case services
		switch($this->service){
			case 'get-preferences':
				return $oOptions->getPreferences($this->data);
				break;

			case 'save-preferences':
				return $oOptions->savePreferences($this->data);
				break;

			case 'get-account-options':
				return $oOptions->getAccountOptions($this->data);
				break;

			case 'save-account-options':
				return $oOptions->saveAccountOptions($this->data);
				break;

			default:
				$this->setError(105);
				return false;
				break;
			}
		
		}

	//-------------------------------------------------------------------------------------------------------------
	private function processTest() {	
		//instanciate object
		//require_once(DIR_CLASS.checkForDebugFile('test', $this->reg->get('req')->get('file')).'.php');
		require_once(DIR_CLASS.'test.php');
		$oTest = new Test($this->reg);
		//case services
		switch($this->service){
			case 'sess':
				return $oTest->sess($this->data);
				break;
	
			case 'read':
				return $oTest->read($this->data);
				break;

			case 'write':
				return $oTest->write($this->data);
				break;

			case 'select':
				return $oTest->select($this->data);
				break;

			case 'insert':
				return $oTest->insert($this->data);
				break;

			case 'get-clinic-option-title':
				return $oTest->testGetClinicOptionTitle($this->data);
				break;

			case 'get-licence-option-title':
				return $oTest->testGetLicenceOptionTitle($this->data);
				break;

			case 'get-brand-option-title':
				return $oTest->testGetBrandOptionTitle($this->data);
				break;

			default:
				$this->setError(105);
				return false;
				break;
			}
		return false;
		}

	}


//END