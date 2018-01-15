#include <windows.h>
#include <stdio.h>

#define BRESTART 101
#define TXTMSG 105

#define ARRLENGHT 10
#define MAX_THREADS 4
#define MAX_BUFFER 8192

#define TIMER_MIN 1000
#define MSG_PER_USER 0.00094


// function prototypes
int APIENTRY WinMain(HINSTANCE,HINSTANCE,LPSTR,int);
void InitApp(HINSTANCE);
LRESULT APIENTRY MainWndProc(HWND,UINT,WPARAM,LPARAM);
void errorHandling(LPTSTR);
void initServer();
void buildUserInterface(HWND);
void traceMsg(LPTSTR);
DWORD WINAPI ThreadServer(VOID);
DWORD WINAPI ThreadListen(VOID);
DWORD WINAPI ThreadEnvoiSalon(VOID);
DWORD WINAPI ThreadHell(VOID);
DWORD WINAPI ThreadFakeActions(VOID);
char * strip(char * const);


// var global but hey! what the helll, it's easier this way...
DWORD dwThreadId[MAX_THREADS]; //thread ID
HANDLE hThread[MAX_THREADS];  //handle tread

BOOL bContinueActions;  //stop sending
HWND hwnd; //window 
HWND hwdTxtMsg; //edit text box
HINSTANCE hInst_tm; //edit text box
SOCKET m_socket; //SOCKET
int iNumberOfMember;
int iMsgPerSeconds;


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
					"UNIFakeServer",
					WS_OVERLAPPEDWINDOW|WS_CAPTION,
					200,
					200,
					640,
					480,
					0,
					0,
					hInst,
					0
					);
	ShowWindow(hwnd,SW_SHOW);
	UpdateWindow(hwnd);
	}

	
// Main Process CallBack Function //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
LRESULT APIENTRY MainWndProc(HWND hwnd,UINT msg,WPARAM wParam,LPARAM lParam){
	switch(msg){
		case WM_CREATE:
			buildUserInterface(hwnd);		
			initServer();
			break;
		case WM_DESTROY:
			PostQuitMessage(0);
			break;
		case WM_QUIT:
			PostQuitMessage(0);
			break;	
		case WM_COMMAND:
			/*
			switch(HIWORD(wParam)){
				case BN_CLICKED:
					switch(LOWORD(wParam)){
						case BRESTART:
							bContinueActions = false;
							break;
						}
					break;
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
    
	TCHAR szBuf[MAX_BUFFER]; 
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

	

// starting thraed for UNI fetchng and killing and wait for it to be destroy before renaming and restrating a new one ////////////////////////////////
void initServer(){
	bContinueActions = true;
	hThread[0] = CreateThread(
				NULL,
				0,                           
				(LPTHREAD_START_ROUTINE) ThreadServer,
				NULL,
				0,
				&dwThreadId[0]
				);
	if(hThread[0] == NULL){
		traceMsg("CreateThread error"); 
		}
		
	}		

// basic interface if needed  /////////////////////////////////////////////////////////////////////////////////////////////////////////////		
void buildUserInterface(HWND hwnd){
	/*
	hwdButtRestart = CreateWindow(
				"Button",
				"Restart Server",
				WS_CHILD|WS_VISIBLE|BS_PUSHBUTTON,
				10,
				10,
				610,
				30,
				hwnd,
				(HMENU)BRESTART,
				hInst_br,
				0
				);
	*/
	hwdTxtMsg = CreateWindow( 
				"EDIT", 
				"",
				//WS_CHILD|WS_VISIBLE|WS_VSCROLL|ES_MULTILINE|ES_LEFT|ES_AUTOVSCROLL|ES_WANTRETURN,
				WS_CHILD|WS_VISIBLE|ES_MULTILINE|ES_LEFT|ES_WANTRETURN,
				5,
				5,
				640,
				480,
				hwnd,
				(HMENU)TXTMSG,
				hInst_tm,
				0
				);	
				
	}
	
	
// trace msg  /////////////////////////////////////////////////////////////////////////////////////////////////////////////		
void traceMsg(LPTSTR msg){
	TCHAR szMsgBuf[MAX_BUFFER];
	TCHAR rcvMsgBuf[MAX_BUFFER];
	SendMessage(hwdTxtMsg,EM_FMTLINES,TRUE,0);
	SendMessage(hwdTxtMsg, WM_GETTEXT, (WPARAM)MAX_BUFFER, (LPARAM)rcvMsgBuf);
	wsprintf(szMsgBuf,"%s\r\n%s",msg,rcvMsgBuf);	
	SendMessage(hwdTxtMsg, WM_SETTEXT, 0, (LPARAM)szMsgBuf);
	}	

// thread proc /////////////////////////////////////////////////////////////////////////////////////////////////////////////
DWORD WINAPI ThreadEnvoiSalon(VOID){
	
	traceMsg ("Starting ThreadEnvoiSalon");
	TCHAR filename[] = "salon.txt";
	TCHAR szBuf[MAX_BUFFER]; 
	TCHAR fLine[MAX_BUFFER];
	FILE * pFile;
	char * pch;	

	iMsgPerSeconds = TIMER_MIN;	
	iNumberOfMember = 0;
	wsprintf(fLine,"IM:1,99778,0331120870c422a6,www.compagnie.com,1");
	send(m_socket, fLine, strlen(fLine)+1, 0 ); //+1 pour le flash catr a beaoin d'un null terminated string 
	pFile = fopen(filename,"r");
	if(pFile != NULL){
		while(!feof(pFile) && WSAGetLastError() != WSAECONNRESET && bContinueActions){
			iNumberOfMember++;
			fgets(fLine, MAX_BUFFER, pFile);
			pch = strip(fLine);
			wsprintf(szBuf,"SENDING: %s", pch); 
			traceMsg(szBuf); 
			Sleep(3);
			send(m_socket, pch, strlen(pch)+1, 0 ); //+1 pour le flash catr a beaoin d'un null terminated string 
			}
		fclose(pFile);
		}
	Sleep(10);	
	wsprintf(fLine,"PLIST_END:");
	send(m_socket, fLine, strlen(fLine)+1, 0 ); //+1 pour le flash catr a beaoin d'un null terminated string 
	
	//pour changer le timer du FakeActions selon le nombre de membre
	//iMsgPerSeconds = (int)(1000/(iNumberOfMember*MSG_PER_USER));
	wsprintf(szBuf,"SEND INTERVAL: %d", iMsgPerSeconds); 
	traceMsg(szBuf); 	
	
	traceMsg ("Quitting ThreadEnvoiSalon");
	return 0;
	}	
	
	
// thread proc /////////////////////////////////////////////////////////////////////////////////////////////////////////////
DWORD WINAPI ThreadHell(VOID){
	
	TCHAR fLine[MAX_BUFFER];
	
	wsprintf(fLine,"HELL OH!");
	send(m_socket, fLine, strlen(fLine)+1, 0 ); //+1 pour le flash catr a beaoin d'un null terminated string 
	traceMsg ("Quitting ThreadHell");
	TerminateThread(hThread[1], NULL);
	WSACleanup();
	initServer();
	return 0;
	}		
	
// thread proc /////////////////////////////////////////////////////////////////////////////////////////////////////////////
DWORD WINAPI ThreadListen(VOID){
	
	traceMsg ("Starting ThreadListen");
	int bytesSent;
    int bytesRecv = SOCKET_ERROR;
    TCHAR sendbuf[MAX_BUFFER];
    TCHAR recvbuf[MAX_BUFFER];
	TCHAR szBuf[MAX_BUFFER]; 
	DWORD dwExitCode = 0;
	BOOL bExit = true;	
	
	while(bytesRecv != 0 && WSAGetLastError() != WSAECONNRESET){
		bytesRecv = recv( m_socket, recvbuf, MAX_BUFFER, 0 );
		wsprintf(szBuf,"RECEIVING: %s", recvbuf); 
		traceMsg(szBuf);
		if(!bContinueActions){
			traceMsg ("bContinueActions:FALSE");	
			}
		}
	traceMsg ("Quitting ThreadListen");		
	bContinueActions = false;
	
	if(hThread[2]){
		while(1){
			bExit = GetExitCodeThread( hThread[2], &dwExitCode);
			if( bExit && ( dwExitCode != STILL_ACTIVE ) ){
				break;
				}
			}
		}	
	
	if(hThread[3]){
		while(1){
			bExit = GetExitCodeThread( hThread[3], &dwExitCode);
			if( bExit && ( dwExitCode != STILL_ACTIVE ) ){
				break;
				}
			}
		}	
		
	WSACleanup();
	initServer();
	return 0;
	}


// thread proc /////////////////////////////////////////////////////////////////////////////////////////////////////////////
DWORD WINAPI ThreadFakeActions(VOID){
	
	traceMsg ("Starting ThreadFakeActions");
	TCHAR filename[] = "actions.txt";
	TCHAR fLine[MAX_BUFFER];
	TCHAR fLine2[MAX_BUFFER];
	FILE * pFile;
	int iTimer;
	TCHAR szBuf[MAX_BUFFER]; 
	pFile = fopen(filename,"r");
	MSG msg;
	char * pch;
	
	
	if(pFile != NULL){
		while(bContinueActions){
			while(!feof(pFile) && WSAGetLastError() != WSAECONNRESET && bContinueActions){
				fgets(fLine, MAX_BUFFER, pFile);
				pch = strip(fLine);
				wsprintf(szBuf,"SENDING: %s", pch); 	
				traceMsg(szBuf); 
				send(m_socket, pch, strlen(pch)+1, 0 ); //+1 pour le flash catr a beaoin d'un null terminated string 
				//iTimer = TIMER_MIN + (int)(rand()%TIMER_MAX);
				Sleep(iMsgPerSeconds);
				}
			rewind (pFile);	
			}
		fclose(pFile);
		}
	traceMsg ("Quitting ThreadFakeActions");		
	return 0;
	}
	
// thread proc /////////////////////////////////////////////////////////////////////////////////////////////////////////////
DWORD WINAPI ThreadServer(VOID){ 
	
	traceMsg("Starting ThreadServer");
	TCHAR szBuf[MAX_BUFFER]; 
	int PORT = 30000;
	TCHAR IP[16] = "127.0.0.1";
	
	// Initialize Winsock.
    WSADATA wsaData;
    int iResult = WSAStartup( MAKEWORD(2,2), &wsaData );
    if ( iResult != NO_ERROR ){
        traceMsg("Error at WSAStartup()");
		}

    // Create a socket.
    m_socket = socket( AF_INET, SOCK_STREAM, IPPROTO_TCP );

    if ( m_socket == INVALID_SOCKET ){
        wsprintf(szBuf,"Error at socket(): %ld\n", WSAGetLastError()); 
		traceMsg(szBuf);
        WSACleanup();
        return 0;
		}

    // Bind the socket.
    sockaddr_in service;

    service.sin_family = AF_INET;
    service.sin_addr.s_addr = inet_addr( IP );
    service.sin_port = htons( PORT );

    if(bind( m_socket, (SOCKADDR*) &service, sizeof(service) ) == SOCKET_ERROR ) {
        traceMsg("bind() failed.");
		closesocket(m_socket);
        return 0;
		}
    
    // Listen on the socket.
    if ( listen( m_socket, 1 ) == SOCKET_ERROR ){
		traceMsg("Error listening on socket.");
		}
	// Accept connections.
    SOCKET AcceptSocket;
	wsprintf(szBuf,"Waiting for a client to connect on %s:%d", IP, PORT);
    traceMsg(szBuf);
	while (1) {
        AcceptSocket = SOCKET_ERROR;
        while ( AcceptSocket == SOCKET_ERROR ) {
            AcceptSocket = accept( m_socket, NULL, NULL );
			}
        traceMsg("Client Connected.");
        m_socket = AcceptSocket; 
        hThread[1] = CreateThread(NULL,0,(LPTHREAD_START_ROUTINE) ThreadListen,NULL,0,&dwThreadId[1]);
		if(hThread[1] == NULL){
			traceMsg("Create ThreadListen Error"); 
			}
		hThread[2] = CreateThread(NULL,0,(LPTHREAD_START_ROUTINE) ThreadHell,NULL,0,&dwThreadId[2]);
		if(hThread[2] == NULL){
			traceMsg("Create ThreadHell Error"); 
			}	
			
		/*
		hThread[2] = CreateThread(NULL,0,(LPTHREAD_START_ROUTINE) ThreadEnvoiSalon,NULL,0,&dwThreadId[2]);
		if(hThread[2] == NULL){
			traceMsg("Create ThreadEnvoiSalon Error"); 
			}
		
		hThread[3] = CreateThread(NULL,0,(LPTHREAD_START_ROUTINE) ThreadFakeActions,NULL,0,&dwThreadId[3]);
		if(hThread[3] == NULL){
			traceMsg("Create ThreadFakeActions Error"); 
			}	
		*/
		break;
		}
    
	traceMsg("Quitting ThreadServer");
	return 0;

	}
	
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
char * strip(char * const s){
	size_t i;
	for (i = strlen(s);	i != 0 && (s[i - 1] == '\n' || 	s[i - 1] == '\r'); i--);
	s[i] = 0;
	return s + strspn(s, "\r\n");
	}
	
	
	
	
