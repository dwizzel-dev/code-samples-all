#pragma once

#ifdef _COMPILING_AS_DLL
	#define DLLSPEC extern "C" __declspec(dllexport)
#else
	#define DLLSPEC extern "C" __declspec(dllimport)
#endif 

#define WIN32_LEAN_AND_MEAN // Exclude rarely-used stuff from Windows

#include <windows.h>

DLLSPEC int pushStr(char *,int);
DLLSPEC void showMembers();
DLLSPEC void showSalon();
DLLSPEC char * getSalonByPseudo(bool,int,int);
DLLSPEC bool getPseudoRowsHasNext();
DLLSPEC void changeStatus(bool,char *);
DLLSPEC int getOnlineStatus(bool,char *);

/**************
#pragma once

class flashArray
{
public:
	flashArray(void);
public:
	virtual ~flashArray(void);
};

/////////////////////////
#include "flashArray.h"

flashArray::flashArray(void){
	char * stackStr[5000];
	int stackPos = 0;
	}

flashArray::~flashArray(void){
	
	}

	
*******************/

