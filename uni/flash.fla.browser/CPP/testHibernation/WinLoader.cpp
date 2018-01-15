#include <windows.h>
#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include <psapi.h>

// function prototypes
int APIENTRY WinMain(HINSTANCE,HINSTANCE,LPSTR,int);
LRESULT APIENTRY MainWndProc(HWND,UINT,WPARAM,LPARAM);
void errorHandling(LPTSTR);
void traceMsg(LPTSTR);


 //////////////////////////////////////////////////////////////////////////////////////

 //win prop
 int iWinW = 300;
 int iWinH = 200;
 
// var global but hey! what the helll, it's easier this way...
BOOL bShow = true;

HWND hwnd;
HWND hwdTxtMsg;
HINSTANCE hInst_tm;

// main //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
int APIENTRY WinMain(HINSTANCE hInst,HINSTANCE hPrev,LPSTR line,int CmdShow){
	
	MSG msg;
	WNDCLASS wc;
		
	wc.cbClsExtra = 0;
	wc.cbWndExtra = 0;
	wc.hbrBackground = (HBRUSH) GetStockObject(WHITE_BRUSH);
	wc.hInstance = hInst;
	wc.hCursor = LoadCursor(NULL,IDC_ARROW);
	wc.hIcon = LoadIcon(GetModuleHandle(NULL), MAKEINTRESOURCE(IDI_APPLICATION));
	wc.lpfnWndProc = (WNDPROC) MainWndProc;
	wc.lpszClassName = "Main";
	wc.lpszMenuName = NULL;
	wc.style = CS_HREDRAW | CS_VREDRAW;
	RegisterClass(&wc);
	hwnd = CreateWindow("Main", "Hibernation Test", WS_OVERLAPPEDWINDOW | WS_CLIPCHILDREN, 0, 0, iWinW, iWinH, 0, 0, hInst, 0);
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
	
	char szBuf[1024]; 
		
	switch(msg){
		case WM_CREATE:
			if(bShow){
				hwdTxtMsg = CreateWindow ("listbox", NULL, WS_CHILD|WS_VISIBLE|WS_VSCROLL|LBS_USETABSTOPS|LBS_NOINTEGRALHEIGHT|LBS_NODATA ,0, 0, 0, 0, hwnd, NULL, hInst_tm, NULL );
				traceMsg("WAITING FOR SLEEP MODE");
				}
			break;
		case WM_DESTROY:
			PostQuitMessage(0);
			break;
		case WM_CLOSE:
			PostQuitMessage(0);
			break;
		case WM_SIZE :
			
			RECT rcClient; 
			GetClientRect(hwnd, &rcClient); 
			MoveWindow(hwdTxtMsg,  0, 0, rcClient.right, rcClient.bottom, TRUE);
			/*
			wsprintf(szBuf,"SIZE: %d,%d", rcClient.right, rcClient.bottom); 
			traceMsg(szBuf);
			*/
			break;
		case WM_POWERBROADCAST: //power management
			
			wsprintf(szBuf,"(WPARAM): %d", wParam); 
			traceMsg(szBuf);
			
			switch(wParam){
				case PBT_APMSUSPEND:
					traceMsg("System is going in hibernation (PBT_APMSUSPEND)");
					break;	
				case PBT_APMRESUMESUSPEND:
					traceMsg("System is bringed back from hibernation (PBT_APMRESUMESUSPEND)");
					break;
				case PBT_APMRESUMEAUTOMATIC:
					traceMsg("System is bringed back from hibernation (PBT_APMRESUMEAUTOMATIC)");
					break;	
				case PBT_APMRESUMECRITICAL:
					traceMsg("System is bringed back from hibernation (PBT_APMRESUMECRITICAL)");
					break;		
				default:
					break;
					//return DefWindowProc(hwnd,msg,wParam,lParam);
				}
			break;
			
		default: 
			return DefWindowProc(hwnd,msg,wParam,lParam);
		}
	return 0;
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

	
	