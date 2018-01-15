#include "WinLoader.h"
#include <windows.h>
#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include <psapi.h>

// function prototypes
int APIENTRY WinMain(HINSTANCE,HINSTANCE,LPSTR,int);
LRESULT APIENTRY MainWndProc(HWND,UINT,WPARAM,LPARAM);
BOOL getOsV(VOID);
void errorHandling(LPTSTR);
void renameUniTmpFile(VOID);
void traceMsg(LPTSTR);
void exitApplication(VOID);
BOOL EnumProcess(VOID);
void createConnFile(VOID);
void createUserIdleFile(int);
BOOL checkIfStageExist(VOID);
void getAppzPath(VOID);
//void startRenamer(VOID);
DWORD WINAPI ThreadStartWinManager(VOID);
DWORD WINAPI ThreadStartWinAlert(VOID);
DWORD WINAPI ThreadCheckUNI(VOID);
//detection user idle
DWORD WINAPI ThreadIdleDetection(VOID);

 //////////////////////////////////////////////////////////////////////////////////////

 //win prop
 int iWinW = 600;
 int iWinH = 500;
 
// var global but hey! what the helll, it's easier this way...
BOOL bManagerIsStarted = FALSE;
BOOL bShow = FALSE;
BOOL bWaitBeforeLoading = FALSE;
BOOL bAppzIsRunning = FALSE;
BOOL bSuspendResumed = FALSE; //flag to know if its comming back from suspension
BOOL bIsSuspending = FALSE; //flasg to know if we rare going in suspension
BOOL bAlertTerminated = FALSE; 
BOOL bStageTerminated = FALSE; 

DWORD threadIDCheck; 
DWORD threadIDWinManager;
DWORD threadIDWinAlert;
DWORD threadIDIdleDetection;

HANDLE hThreadCheck;
HANDLE hThreadWinManager;
HANDLE hThreadWinAlert;
HANDLE hThreadIdleDetection;

HANDLE hProcessWinManager;
HANDLE hProcessWinAlert;

HWND hwnd;
HWND hwdTxtMsg;
HINSTANCE hInst_tm;

char arrWinToKill[][9] = {
	"auni.exe\0",
	"suni.exe\0"
	};
	
char appzPath[1024]; 	

// main //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
int APIENTRY WinMain(HINSTANCE hInst,HINSTANCE hPrev,LPSTR line,int CmdShow){
	
	MSG msg;
	WNDCLASS wc;
		
	wc.cbClsExtra = 0;
	wc.cbWndExtra = 0;
	wc.hbrBackground = (HBRUSH) GetStockObject(WHITE_BRUSH);
	wc.hInstance = hInst;
	wc.hCursor = LoadCursor(NULL,IDC_ARROW);
	wc.hIcon = LoadIcon(GetModuleHandle(NULL), MAKEINTRESOURCE(IDI_UNIICON));
	wc.lpfnWndProc = (WNDPROC) MainWndProc;
	wc.lpszClassName = "Main";
	wc.lpszMenuName = NULL;
	wc.style = CS_HREDRAW | CS_VREDRAW;
	RegisterClass(&wc);
	hwnd = CreateWindow("Main", "UNI3", WS_OVERLAPPEDWINDOW | WS_CLIPCHILDREN, 0, 0, iWinW, iWinH, 0, 0, hInst, 0);
	if(bShow){
		ShowWindow(hwnd,SW_SHOW);
	}else{
		ShowWindow(hwnd,SW_HIDE);
		}
	UpdateWindow(hwnd);
	
	if(__argv[1] != 0){
		//check si on revient d'un hibernate ou startup from registry key
		bWaitBeforeLoading = TRUE;
		}
	
	while(GetMessage(&msg,0,0,0)){
		TranslateMessage(&msg);
		DispatchMessage(&msg);
		}
	return 0;
	}

// Main Process CallBack Function //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
LRESULT APIENTRY MainWndProc(HWND hwnd,UINT msg,WPARAM wParam,LPARAM lParam){
	
	static char szTitleTxt[] = {"Debug Window\0"};
	static int	cxClient, cyClient;
	char szBuf[1024]; 
	switch(msg){
		case WM_CREATE:
			if(bShow){
				hwdTxtMsg = CreateWindow ("listbox", NULL, WS_CHILD|WS_VISIBLE|WS_VSCROLL|LBS_USETABSTOPS,0, 0, 0, 0, hwnd, NULL, hInst_tm, NULL );
				SetWindowText(hwdTxtMsg, szTitleTxt);
				}
			hThreadCheck = CreateThread(NULL, 0, (LPTHREAD_START_ROUTINE) ThreadCheckUNI, NULL, 0, &threadIDCheck);
			if(hThreadCheck == NULL){
				traceMsg("***Create Thread Check error"); 
				}
			break;
		case WM_DESTROY:
			PostQuitMessage(0);
			break;
		case WM_CLOSE:
			if(bManagerIsStarted){
				if(TerminateProcess(hProcessWinManager, NULL) == 0){
					errorHandling("***TerminateProcess()");
					}
				if(TerminateProcess(hProcessWinAlert, NULL) == 0){
					errorHandling("***TerminateProcess()");
					}
				}	
			PostQuitMessage(0);
			break;
		case WM_SIZE :
			cxClient = LOWORD(lParam);
			cyClient = HIWORD(lParam);
			MoveWindow(hwdTxtMsg,  0, 0, cxClient, cyClient, FALSE);
			break;
		case WM_POWERBROADCAST: //power management
			switch(wParam){
				case PBT_APMSUSPEND:
					bSuspendResumed = FALSE;
					bIsSuspending = TRUE;
					traceMsg("System is going in hibernation");
					if(bManagerIsStarted){
						//shutdown the process if it was started because 
						//the socket is still opened butt no treatment is made
						//so synchronisation won't work
						if(TerminateProcess(hProcessWinManager, NULL) == 0){
							errorHandling("***TerminateProcess(): suni.exe");
						}else{
							bStageTerminated = TRUE;
							}
						if(TerminateProcess(hProcessWinAlert, NULL) == 0){
							errorHandling("***TerminateProcess(): auni.exe");
						}else{
							bAlertTerminated = TRUE;
							}
						}
					break;	
				case PBT_APMRESUMESUSPEND:
					if(!bSuspendResumed){ //to catch the event only one time because system send it twice
						traceMsg("System is bringed back from hibernation");
						//restart the application
						hThreadCheck = CreateThread(NULL, 0, (LPTHREAD_START_ROUTINE) ThreadCheckUNI, NULL, 0, &threadIDCheck);
						if(hThreadCheck == NULL){
							traceMsg("***Create Thread Check error"); 
							}
						}
					bSuspendResumed = TRUE;
					bIsSuspending = FALSE;
					break;
				default:
					return DefWindowProc(hwnd,msg,wParam,lParam);
				}
			
			/*
			traceMsg("System is going for hibernation");
			wsprintf(szBuf,"(WPARAM): %d", wParam); 
			traceMsg(szBuf);
			*/
		default: 
			return DefWindowProc(hwnd,msg,wParam,lParam);
		}
	return 0;
	}

// function to creare a connection name to a file ////////////////////////////////////////////////////////////////////////////////////////////////////
void createConnFile(VOID){
	traceMsg("Creating local connection file descriptor...");
	char szBuf[2048]; 
	wsprintf(szBuf, "%s%s\0", appzPath,"localconnection.txt");
	FILE *file;
	//file = fopen(szBuf, "wt");
	file = fopen(szBuf, "w");
	if(file != NULL){
		fprintf(file, "&lc=%d", random((long)time(NULL)));
		fclose(file);
		}
	}
	
// function to creare a user idle flag to a file to be read by stage.swf///////////////////////////////////////////////////////////////////////////////////
void createUserIdleFile(int iFlag){ //0=off, 1=on
	char szBuf[2048]; 
	wsprintf(szBuf,"USER IDLE: %d", iFlag);
	traceMsg(szBuf);
	wsprintf(szBuf, "%s%s\0", appzPath, "useridle.txt");
	FILE *file;
	file = fopen(szBuf, "w");
	if(file != NULL){
		fprintf(file, "&uidle=%d", iFlag);
		fclose(file);
		}
	}	

// function to creare a connection name to a file ////////////////////////////////////////////////////////////////////////////////////////////////////
BOOL checkIfStageExist(VOID){
	traceMsg("Checking if stage exist...");
	char szBuf[2048]; 
	wsprintf(szBuf, "%s%s\0", appzPath,"stage.swf");
	FILE *file;
	file = fopen(szBuf, "r");
	if(file != NULL){
		fclose(file);
		return TRUE;
		}
	return FALSE;	
	}
	
	
// function to retrieve application path////////////////////////////////////////////////////////////////////////////////////////////////////
void getAppzPath(VOID){
	if(GetModuleFileName(NULL,appzPath,1024) == 0){
		errorHandling("GetModuleFileName()");
		Sleep(10000); //wait a bit
		exitApplication();
	}else{
		char szBufDir[1024];
		//strip the last 
		appzPath[strlen(appzPath) - 8] = '\0';
		wsprintf(szBufDir,"Appz Path: %s",appzPath);
		traceMsg(szBufDir);
		}
	}	
	
// function for detecting OS version ////////////////////////////////////////////////////////////////////////////////////////////////////
BOOL getOsV(VOID){
	OSVERSIONINFO os_version_info;
	os_version_info.dwOSVersionInfoSize = sizeof (OSVERSIONINFO);
	GetVersionEx (&os_version_info);
	switch (os_version_info.dwPlatformId){
		case VER_PLATFORM_WIN32_NT:
			traceMsg("Retrieved OS is VER_PLATFORM_WIN32_NT");
			return TRUE;
		case VER_PLATFORM_WIN32_WINDOWS:
			traceMsg("Retrieved OS is VER_PLATFORM_WIN32_WINDOWS");
			return FALSE;
		case VER_PLATFORM_WIN32s:
			traceMsg("Retrieved OS is VER_PLATFORM_WIN32s");
			return TRUE;
		default:
			traceMsg("Retrieved OS is CASE DEFAULT");
			return FALSE;
		}
	return FALSE;	
	}
	
	
// mon error handling Func /////////////////////////////////////////////////////////////////////////////////////////////////////////////
void errorHandling(LPTSTR lpszFunction){ 
	char szBuf[1024]; 
    LPVOID lpMsgBuf;
    DWORD dw = GetLastError(); 
    FormatMessage(FORMAT_MESSAGE_ALLOCATE_BUFFER | FORMAT_MESSAGE_FROM_SYSTEM, NULL, dw, MAKELANGID(LANG_NEUTRAL, SUBLANG_DEFAULT), (LPTSTR) &lpMsgBuf, 0, NULL);
    wsprintf(szBuf,"%s (ERROR %d): %s", lpszFunction, dw, lpMsgBuf); 
    traceMsg(szBuf);
    LocalFree(lpMsgBuf);
	}


// file rename //////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
void renameUniTmpFile(){
	const int iLenght = 3;
	const char arrTmpName[iLenght][9] = {
		"suni.tmp\0",
		"auni.tmp\0",
		"cuni.tmp\0"
		};
	const char arrRealName[iLenght][9] = {
		"suni.exe\0",
		"auni.exe\0",
		"cuni.exe\0"
		};	
	
	char szMsg[1024];
	char szBufTmp[1024]; 
	char szBufReal[1024]; 
	FILE * pFile;
	
	//rename executable files for replacement
	for(int i=0; i<iLenght; i++){
		wsprintf(szBufTmp,"%s%s\0",appzPath,arrTmpName[i]);
		pFile = fopen(szBufTmp,"r");
		if(pFile != NULL){
			wsprintf(szMsg,"%s exist", szBufTmp);
			traceMsg(szMsg);
			//close the handle
			fclose(pFile);
			//open the real one
			wsprintf(szBufReal,"%s%s\0",appzPath,arrRealName[i]);
			pFile = fopen(szBufReal,"r");
			if(pFile != NULL){
				wsprintf(szMsg,"%s exist", szBufReal);
				traceMsg(szMsg);
				//close the handle
				fclose(pFile);
				//remove the exe
				if(remove(szBufReal) == 0){
					wsprintf(szMsg,"%s has been removed", szBufReal);
					traceMsg(szMsg);
					if(rename(szBufTmp,szBufReal) == 0){
						wsprintf(szMsg,"%s has been renamed to %s", szBufTmp, szBufReal);
						traceMsg(szMsg);
					}else{
						wsprintf(szMsg,"*** can't rename %s to %s", szBufTmp, szBufReal);
						traceMsg(szMsg);
						}
				}else{
					wsprintf(szMsg,"*** %s can't be removed", szBufReal);
					traceMsg(szMsg);
					}
			}else{
				wsprintf(szMsg,"*** %s doesn't exist", szBufReal);
				traceMsg(szMsg);
				}
		}else{
			wsprintf(szMsg,"*** %s doesn't exist", szBufTmp);
			traceMsg(szMsg);
			}
		}
		
	//the renammer files after the install
	wsprintf(szBufTmp,"%s%s\0",appzPath,"UNI3_p.exe");
	pFile = fopen(szBufTmp,"r");
	if(pFile != NULL){
		traceMsg("deleting UNI3_p.exe");
		//close the handle
		fclose(pFile);
		//remove the exe
		if(remove(szBufTmp) == 0){
			traceMsg("UNI3_p.exe has been deleted");
		}else{
			traceMsg("*** can't delete UNI3_p.exe");
			}
	}else{
		traceMsg("*** UNI3_p.exe his already deleted");
		}
		
	//the old files after the install
	wsprintf(szBufTmp,"%s%s\0",appzPath,"UNI2.exe");
	pFile = fopen(szBufTmp,"r");
	if(pFile != NULL){
		traceMsg("deleting UNI2.exe");
		//close the handle
		fclose(pFile);
		//remove the exe
		if(remove(szBufTmp) == 0){
			traceMsg("UNI2.exe has been deleted");
		}else{
			traceMsg("*** can't delete UNI2.exe");
			}
	}else{
		traceMsg("*** UNI2.exe his already deleted");
		}	
		
		
	//rename the main exec file
	/*
	static char strTmpRenamerName[] = {"runi.tmp\0"};
	static char strRealRenamerName[] = {"runi.exe\0"};
	static char strTmpMainName[] = {"UNI2.tmp\0"};
	bool bHaveBothTmpFile = FALSE;
	
	//check if we have both strTmpRenamerName and strTmpMainName
	pFile = fopen(strTmpRenamerName,"r");	
	if(pFile != NULL){
		traceMsg("FOUND runi.tmp");
		//close the handle
		fclose(pFile);
		pFile = fopen("UNI2.tmp\0","r");	
		if(pFile != NULL){
			traceMsg("FOUND UNI2.tmp");
			//close the handle
			fclose(pFile);
			//ok we have both files so rename the renamer and start it
			if(rename(strTmpRenamerName, strRealRenamerName) == 0){
				wsprintf(szMsg,"%s has been renamed to %s", strTmpRenamerName, strRealRenamerName);
				traceMsg(szMsg);
				//ok the renaming his done so let's start the renamer appz and kill this one
				startRenamer();
			}else{
				wsprintf(szMsg,"*** can't rename %s to %s", strTmpRenamerName, strRealRenamerName);
				traceMsg(szMsg);
				}
			}
		}
	*/	
	}

	
// EnumProcess CALLBACK /////////////////////////////////////////////////////////////////////////////////////////////////////////////	
BOOL EnumProcess(VOID){
	bAppzIsRunning = FALSE;
	DWORD aProcesses[1024];
	char szBuf[1024]; 
	HANDLE hProcess;
	DWORD cbNeeded;
	DWORD cProcesses;
	if(!EnumProcesses(aProcesses, sizeof(aProcesses), &cbNeeded)){
		traceMsg("***EnumProcesses ERROR");
		}
	cProcesses = cbNeeded/sizeof(DWORD);
	for(unsigned int i=0; i<cProcesses; i++){
		if(aProcesses[i] != 0 ){
			hProcess = OpenProcess( PROCESS_QUERY_INFORMATION | PROCESS_VM_READ, FALSE, aProcesses[i]);
			if(hProcess != NULL){
				HMODULE hMod;
				DWORD cbNeeded2;
				char szProcessName[1024]; 
				if(EnumProcessModules(hProcess, &hMod, sizeof(hMod), &cbNeeded2) != 0){
					if(GetModuleBaseName(hProcess, hMod, szProcessName, sizeof(szProcessName)/sizeof(char)) != 0){
						int iLenght = sizeof(arrWinToKill);
						for(int j=0; j<iLenght; j++){
							if(strcmp(szProcessName,arrWinToKill[j]) == 0){
								/*
								if(strcmp(szProcessName,arrWinToKill[0]) == 0){
									//PostMessage(hwnd,WM_CLOSE,NULL,NULL);
									if(TerminateProcess(hProcess, NULL) == 0){
										wsprintf(szBuf,"***TerminateProcess:(%s)", szProcessName);
										errorHandling(szBuf);
										}
									//exitApplication();
									//return FALSE;	
									} 
								*/
									
								bAppzIsRunning = TRUE;
								char szMsg[1024]; 
								wsprintf(szMsg,"PROGRAM IS FOUND --> (%s)", szProcessName);
								traceMsg(szMsg);
								CloseHandle(hProcess);
								return TRUE;
								}
							}
					}else{
						//errorHandling("GetModuleBaseName()");
						}
				}else{
					//errorHandling("EnumProcessModules()");
					}
			}else{
				//errorHandling("OpenProcess()");
				}
			CloseHandle(hProcess);
			}
		}
	return FALSE;
	}
	

// restart The WinManager /////////////////////////////////////////////////////////////////////////////////////////////////////////////		
DWORD WINAPI ThreadStartWinManager(VOID){
	STARTUPINFO si;
    PROCESS_INFORMATION pi;

	ZeroMemory(&si, sizeof(si));
    si.cb = sizeof(si);
    ZeroMemory(&pi, sizeof(pi));
	
    char szBufExe[1024];
	
	traceMsg("Starting suni.exe...");
	wsprintf(szBufExe,"%s%s",appzPath,"suni.exe\0");
	traceMsg(szBufExe);
	if(!CreateProcess(szBufExe, NULL, NULL, NULL, FALSE, 0, NULL, NULL, &si, &pi)){
		errorHandling("suni.exe");
		traceMsg("This window will close in 10 sec...");
		Sleep(10000); //wait a bit
		exitApplication();
	}else{
		bManagerIsStarted = TRUE;
		bStageTerminated = FALSE;
		traceMsg("suni.exe is started...");
		hProcessWinManager = pi.hProcess;
		WaitForSingleObject(pi.hProcess, INFINITE);
		CloseHandle(pi.hProcess);
		CloseHandle(pi.hThread);
		traceMsg("suni.exe is killed");
		exitApplication();
		}
	}


// start the renamer program/////////////////////////////////////////////////////////////////////////////////////////////////////////////		
/*
VOID startRenamer(VOID){
	
	STARTUPINFO si;
    PROCESS_INFORMATION pi;

	ZeroMemory(&si, sizeof(si));
    si.cb = sizeof(si);
    ZeroMemory(&pi, sizeof(pi));
	
	char szBufExe[1024];
	
	//start the window manager
	traceMsg("Starting runi.exe...");
	wsprintf(szBufExe,"%s%s",appzPath,"runi.exe\0");
	traceMsg(szBufExe);
	if(!CreateProcess(szBufExe, NULL, NULL, NULL, FALSE, 0, NULL, NULL, &si, &pi)){
		errorHandling("runi.exe");
	}else{
		traceMsg("runi.exe is started...");
		exitApplication();	
		}
	}
*/
	
// restartThe WinAlert  /////////////////////////////////////////////////////////////////////////////////////////////////////////////		
DWORD WINAPI ThreadStartWinAlert(VOID){
	STARTUPINFO si;
    PROCESS_INFORMATION pi;

	ZeroMemory(&si, sizeof(si));
    si.cb = sizeof(si);
    ZeroMemory(&pi, sizeof(pi));
	
	char szBufExe[1024];
	
	//start the window manager
	if(bManagerIsStarted){
		traceMsg("Starting auni.exe...");
		wsprintf(szBufExe,"%s%s",appzPath,"auni.exe\0");
		traceMsg(szBufExe);
		if(!CreateProcess(szBufExe, NULL, NULL, NULL, FALSE, 0, NULL, NULL, &si, &pi)){
			errorHandling("auni.exe");
		}else{
			traceMsg("auni.exe is started...");
			bAlertTerminated = FALSE;
			hProcessWinAlert = pi.hProcess;
			WaitForSingleObject(pi.hProcess, INFINITE);
			CloseHandle(pi.hProcess);
			CloseHandle(pi.hThread);
			traceMsg("auni.exe is killed");
			exitApplication();	
			}
		}
	}	


// exit this application /////////////////////////////////////////////////////////////////////////////////////////////////////////////		
void exitApplication(VOID){
	if(!bIsSuspending){
		//we dont want to quit completely because we are going to resume after the suspensionb
		PostMessage(hwnd,WM_CLOSE,NULL,NULL);
		}
	}
	
// trace msg  /////////////////////////////////////////////////////////////////////////////////////////////////////////////		
void traceMsg(LPTSTR msg){
	if(bShow){
		char szMsgBuf[2048];
		int nIndexVisible;
		wsprintf(szMsgBuf,"%s", msg);	
		nIndexVisible = SendMessage(hwdTxtMsg, LB_ADDSTRING, 0, (LPARAM)szMsgBuf);
		SendMessage(hwdTxtMsg, LB_SETTOPINDEX, (WPARAM) nIndexVisible, 0);
		}
	}	

	
// thread proc /////////////////////////////////////////////////////////////////////////////////////////////////////////////
DWORD WINAPI ThreadCheckUNI(VOID){ 
	traceMsg("Starting UNI thread");
	int iLoop = 0;
	int iLoopMax = 600;
	
	//wait a bit if we are coming back from suspension, because tmp file didn't had time to clened up
	if(bSuspendResumed || bWaitBeforeLoading){
		traceMsg("Waiting 90 seconds to reconnect application");
		bWaitBeforeLoading = FALSE;
		Sleep(90000); //wait a bit
		}
	
	if(getOsV()){ //check version
		while(iLoop < iLoopMax && EnumProcess()){ //try for 60 seconds
			iLoop++;
			Sleep(100); //wait a bit
			}
		if(bAppzIsRunning){
			traceMsg("Application is already running...");
			traceMsg("This window will close in 3 sec...");
			Sleep(3000); //wait a bit
			exitApplication();
		}else{
			getAppzPath();
			if(checkIfStageExist()){
				renameUniTmpFile();
				createConnFile();
				//user idle detection
				hThreadIdleDetection = CreateThread(NULL, 0, (LPTHREAD_START_ROUTINE) ThreadIdleDetection, NULL, 0, &threadIDIdleDetection);
				if(hThreadIdleDetection == NULL){
					traceMsg("***Create Thread Idle Detection error"); 
					}
				//init win manager
				//Sleep(2000); //wait a bit
				hThreadWinManager = CreateThread(NULL, 0, (LPTHREAD_START_ROUTINE) ThreadStartWinManager, NULL, 0, &threadIDWinManager);
				if(hThreadWinManager == NULL){
					traceMsg("***Create Thread suni.exe error");
				}else{
					//init win alert
					Sleep(120000); //wait a bit
					hThreadWinAlert = CreateThread(NULL, 0, (LPTHREAD_START_ROUTINE) ThreadStartWinAlert, NULL, 0, &threadIDWinAlert);
					if(hThreadWinAlert == NULL){
						traceMsg("***Create Thread auni.exe error");
						}
					}
			}else{
				traceMsg("Stage is not present...");
				traceMsg("This window will close in 3 sec...");
				Sleep(3000); //wait a bit
				exitApplication();
				}
			}
	}else{
		MessageBox(NULL,"OS VERSION","ERROR WIN VERSION TOO OLD", MB_OK);
		}
	return 0; 
	}
	
// thread proc for detection of idle user////////////////////////////////////////////////////////////////////////////////////////////////////////
DWORD WINAPI ThreadIdleDetection(VOID){
	//loop to catch user idle
	traceMsg("START CHECKING USER IDLE...");
	createUserIdleFile(0);
	char szMsgBuf[256];
	BOOL bCatch = TRUE;
	BOOL bUserIdle = FALSE;
	DWORD tTime;
	DWORD iDiff;
	LASTINPUTINFO lii;
	ZeroMemory(&lii, sizeof(lii));
	lii.cbSize = sizeof(lii);
	while(bCatch){
		//traceMsg("CHECKING USER IDLE...");
		if(!GetLastInputInfo(&lii)){
			errorHandling("GetLastInputInfo()");
		}else{
			tTime = GetTickCount(); 
			iDiff = (tTime - lii.dwTime)/1000; 
			//wsprintf(szMsgBuf,"TIME DIFF: %d", iDiff);
			//traceMsg(szMsgBuf);
			if(iDiff > 300){
				if(!bUserIdle){
					createUserIdleFile(1);
					}
				bUserIdle = TRUE;
			}else{
				if(bUserIdle){
					createUserIdleFile(0);
					}
				bUserIdle = FALSE;	
				}
			}
		Sleep(30000); //wait a bit
		}
	}
	
	
	