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
void renameSetupFile(VOID);
void traceMsg(LPTSTR);
void exitApplication(VOID);
void getAppzPath(VOID);
DWORD WINAPI ThreadCheckUNI(VOID);

 //////////////////////////////////////////////////////////////////////////////////////

 //win prop
 int iWinW = 600;
 int iWinH = 500;
 
// var global but hey! what the helll, it's easier this way...
BOOL bShow = TRUE;

DWORD threadIDCheck; 
HANDLE hThreadCheck;
HWND hwnd;
HWND hwdTxtMsg;
HINSTANCE hInst_tm;
	
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
	hwnd = CreateWindow("Main", "UNI3 SWITCH VERSION", WS_OVERLAPPEDWINDOW | WS_CLIPCHILDREN, 0, 0, iWinW, iWinH, 0, 0, hInst, 0);
	if(bShow){
		ShowWindow(hwnd,SW_SHOW);
	}else{
		ShowWindow(hwnd,SW_HIDE);
		}
	UpdateWindow(hwnd);
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
			PostQuitMessage(0);
			break;
		case WM_SIZE :
			cxClient = LOWORD(lParam);
			cyClient = HIWORD(lParam);
			MoveWindow(hwdTxtMsg,  0, 0, cxClient, cyClient, FALSE);
			break;
		default: 
			return DefWindowProc(hwnd,msg,wParam,lParam);
		}
	return 0;
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
		appzPath[strlen(appzPath) - 17] = '\0';
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
void renameSetupFile(){
	char szMsg[1024];
	char szBufTmp[1024]; 
	char szBufReal[1024]; 
	FILE * pFile;
	
	//rename executable files for replacement
	wsprintf(szBufTmp, "%s%s\0", appzPath, "setup.tmp");
	pFile = fopen(szBufTmp,"r");
	if(pFile != NULL){
		wsprintf(szMsg,"%s exist", szBufTmp);
		traceMsg(szMsg);
		//close the handle
		fclose(pFile);
		//open the real one
		wsprintf(szBufReal, "%s%s\0", appzPath, "setup.exe");
		if(rename(szBufTmp,szBufReal) == 0){
			wsprintf(szMsg,"%s has been renamed to %s", szBufTmp, szBufReal);
			traceMsg(szMsg);
		}else{
			wsprintf(szMsg,"*** can't rename %s to %s", szBufTmp, szBufReal);
			traceMsg(szMsg);
			}
	}else{
		wsprintf(szMsg,"*** %s doesn't exist", szBufTmp);
		traceMsg(szMsg);
		}
		
	}


// exit this application /////////////////////////////////////////////////////////////////////////////////////////////////////////////		
void exitApplication(VOID){
	PostMessage(hwnd,WM_CLOSE,NULL,NULL);
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
	STARTUPINFO si;
    ZeroMemory(&si, sizeof(si));
    si.cb = sizeof(si);
    PROCESS_INFORMATION pi;
	ZeroMemory(&pi, sizeof(pi));
	char szBufExe[2048];
	if(getOsV()){ //check version
		//appz path
		getAppzPath();
		//rename setup.tmp pour setup.exe
		renameSetupFile();
		
		//start the main setup.exe
		wsprintf(szBufExe, "%s%s", appzPath, "setup.exe\0");	
		traceMsg(szBufExe);
		if(!CreateProcess(szBufExe, NULL, NULL, NULL, FALSE, 0, NULL, NULL, &si, &pi)){
			errorHandling(szBufExe);
			traceMsg("This window will close in 10 sec...");
			Sleep(10000); //wait a bit
			exitApplication();
			}
		
		//wait for single object
		WaitForSingleObject(pi.hProcess, INFINITE);
		CloseHandle(pi.hProcess);
		CloseHandle(pi.hThread);
		
		//start the uninstaller
		wsprintf(szBufExe, "%s%s", appzPath, "uninst.exe\0");
		traceMsg(szBufExe);
		if(!CreateProcess(szBufExe, NULL, NULL, NULL, FALSE, 0, NULL, NULL, &si, &pi)){
			errorHandling(szBufExe);
			traceMsg("This window will close in 10 sec...");
			Sleep(10000); //wait a bit
			exitApplication();
			}
		
		
	}else{
		MessageBox(NULL,"OS VERSION","ERROR WIN VERSION TOO OLD", MB_OK);
		}
	//exit this program
	traceMsg("This window will close in 10 sec...");
	Sleep(10000); //wait a bit
	exitApplication();	
	return 0; 
	}
	
