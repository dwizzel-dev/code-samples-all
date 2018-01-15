#include "uni.h"
#include <windows.h>
#include <stdio.h>

// function prototypes
int APIENTRY WinMain(HINSTANCE,HINSTANCE,LPSTR,int);
void InitApp(HINSTANCE);
LRESULT APIENTRY MainWndProc(HWND,UINT,WPARAM,LPARAM);
void errorHandling(LPTSTR);
void renameUNI();
void initCheckUNI();
void buildUserInterface(HWND);
void traceMsg(LPTSTR);
DWORD WINAPI ThreadCheckUNI(VOID);

// var global but hey! what the helll, it's easier this way...
BOOL bShow = false;
DWORD threadID; 
HANDLE hThread;
HWND hwnd;
HWND hwdTxtMsg;
HINSTANCE hInst_tm;


// main //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
int APIENTRY WinMain(HINSTANCE hInst,HINSTANCE hPrev,LPSTR line,int CmdShow){
	
	MSG msg;
	InitApp(hInst);

	while(GetMessage(&msg,0,0,0)){
		TranslateMessage(&msg);
		DispatchMessage(&msg);
		}

	return msg.wParam;
	}

	
// initlize application/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
void InitApp(HINSTANCE hInst){
	WNDCLASS wc;
	wc.cbClsExtra = 0;
	wc.cbWndExtra = 0;
	wc.hbrBackground = (HBRUSH) GetStockObject(WHITE_BRUSH);
	wc.hInstance = hInst;
	wc.hCursor = LoadCursor(NULL,IDC_ARROW);
	wc.hIcon = LoadIcon(NULL,IDI_APPLICATION);
	wc.lpfnWndProc = (WNDPROC) MainWndProc;
	wc.lpszClassName = "Main";
	wc.lpszMenuName = NULL;
	wc.style = CS_HREDRAW | CS_VREDRAW;

	RegisterClass(&wc);
	hwnd = CreateWindow(
					"Main",
					"UNI Rename",
					WS_POPUPWINDOW|WS_CAPTION,
					0,
					0,
					250,
					500,
					0,
					0,
					hInst,
					0
					);
	if(bShow){
		ShowWindow(hwnd,SW_SHOW);
	}else{
		ShowWindow(hwnd,SW_HIDE);
		}
	UpdateWindow(hwnd);
	}

	
// Main Process CallBack Function //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
LRESULT APIENTRY MainWndProc(HWND hwnd,UINT msg,WPARAM wParam,LPARAM lParam){
	switch(msg){
		case WM_CREATE:
			if(bShow){
				buildUserInterface(hwnd);		
				}
			initCheckUNI();
			break;
		case WM_DESTROY:
			PostQuitMessage(0);
			break;
		case WM_QUIT:
			PostQuitMessage(0);
			break;	
		case WM_CTLCOLOREDIT:
			/*
			if((HWND)lParam == hwdTxtMsg){ //si c'est le EditBox
				SetTextColor((HDC)wParam, RGB(128,128,128));
				return (LONG)GetStockObject(LTGRAY_BRUSH);
				}
			*/
			break;	
		case WM_COMMAND:
			/*
			switch(HIWORD(wParam)){
				default: break;
				}
			*/	
			break;
		default: 
			return DefWindowProc(hwnd,msg,wParam,lParam);
		}

	return 0;
	}

	
// mon error handling Func /////////////////////////////////////////////////////////////////////////////////////////////////////////////
void errorHandling(LPTSTR lpszFunction){ 
    
	TCHAR szBuf[80]; 
    LPVOID lpMsgBuf;
    DWORD dw = GetLastError(); 
    FormatMessage(
				FORMAT_MESSAGE_ALLOCATE_BUFFER | FORMAT_MESSAGE_FROM_SYSTEM, 
				NULL, 
				dw, 
				MAKELANGID(LANG_NEUTRAL, SUBLANG_DEFAULT),
				(LPTSTR) &lpMsgBuf,
				0, 
				NULL
				);
    wsprintf(szBuf,"%s failed avec erreur %d: %s", lpszFunction, dw, lpMsgBuf); 
    traceMsg(szBuf);
    LocalFree(lpMsgBuf);
	}		

// file rename //////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
void renameUNI(){

	const char strName1[] = "UNI3.exe\0";
	const char strName2[] = "suni.exe\0";
	const char tmpName[] = "UNI3.tmp\0";
	FILE * pFile;
	
	if(strName1!=NULL && strName2!=NULL && strName1!="" && strName2!=""){
		pFile = fopen(strName1,"r");
		if(pFile != NULL){
			traceMsg("UNI3.exe exist");
			fclose(pFile);
			pFile = fopen(strName2,"r");
			if(pFile != NULL){
				traceMsg("suni.exe exist");
				fclose(pFile);
				if(rename(strName1,tmpName) == 0){
					traceMsg("renamed UNI3.exe to UNI3.tmp");
					if(rename(strName2,strName1) == 0){
						traceMsg("renamed suni.exe to UNI3.exe");
						if(rename(tmpName,strName2) == 0){
							traceMsg("renamed UNI3.tmp to suni.exe");
						}else{
							traceMsg("ERROR renaming UNI3.tmp to suni.exe");		
							}
					}else{
						traceMsg("ERROR renaming suni.exe to UNI3.exe");		
						}
				}else{
					traceMsg("ERROR renaming UNI3.exe to UNI3.tmp");		
					}
			}else{
				traceMsg("UniStage.exe doesn't exist");
				}
		}else{
			traceMsg("UniStage.exe doesn't exist");
			}
		}		
	}	

	
// starting thraed for UNI fetchng and killing and wait for it to be destroy before renaming and restrating a new one ////////////////////////////////
void initCheckUNI(){
	
	hThread = CreateThread(
				NULL,
				0,                           
				(LPTHREAD_START_ROUTINE) ThreadCheckUNI,
				NULL,
				0,
				&threadID
				);
	if(hThread == NULL){
		traceMsg("CreateThread error"); 
		}
	}		

	
// basic interface if needed  /////////////////////////////////////////////////////////////////////////////////////////////////////////////		
void buildUserInterface(HWND hwnd){
	
	hwdTxtMsg = CreateWindow( 
				"EDIT", 
				"user messages...",
				//WS_CHILD|WS_VISIBLE|WS_VSCROLL|ES_MULTILINE|ES_LEFT|ES_AUTOVSCROLL|ES_WANTRETURN,
				WS_CHILD|WS_VISIBLE|ES_MULTILINE|ES_LEFT|ES_WANTRETURN,
				5,
				0,
				600,
				500,
				hwnd,
				(HMENU)TXTMSG,
				hInst_tm,
				0
				);			
	}
	
	
// trace msg  /////////////////////////////////////////////////////////////////////////////////////////////////////////////		
void traceMsg(LPTSTR msg){
	if(bShow){
		TCHAR szMsgBuf[4096];
		TCHAR rcvMsgBuf[3072];
		SendMessage(hwdTxtMsg,EM_FMTLINES,TRUE,0);
		SendMessage(hwdTxtMsg, WM_GETTEXT, (WPARAM)3072, (LPARAM)rcvMsgBuf);
		wsprintf(szMsgBuf,"%s\r\n%s",msg,rcvMsgBuf);	
		SendMessage(hwdTxtMsg, WM_SETTEXT, 0, (LPARAM)szMsgBuf);
		}
	}	
	
// thread proc /////////////////////////////////////////////////////////////////////////////////////////////////////////////
DWORD WINAPI ThreadCheckUNI(VOID){ 

	renameUNI(); //rename if we have to
	PostMessage(hwnd,WM_QUIT,NULL,NULL);
	return 0; 
	}
	
	
