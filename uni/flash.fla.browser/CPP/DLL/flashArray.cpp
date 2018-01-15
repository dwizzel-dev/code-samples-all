#define _COMPILING_AS_DLL
#include "flashArray.h"
#include <stdlib.h>
#include <stdio.h>

#define STACKSIZE 10000

/*****************************************************************************/

int getMembersIndex(char [16][256]);
void reorderPseudoSalon(char *);	
void reorderAgeSalon(int);	
char checkForSpecialChar(char);

/*****************************************************************************/

// struct
struct sMEMBERS{ 
    unsigned long no_publique; 
    char pseudo[40]; 
	int age; 
	char album[2];
	int photo;
	int vocal;
	int membership;
	int orientation;
	int sexe;
	char relation[8];
	char code_pays[3];
	int region_id;
	int ville_id;
	int etat_civil;
	char titre[256]; 
	int status;
	
	bool m_salon;
	bool m_carnet;

	int iRefIndex;
	};
	
// struct
struct sNOPUB{ 
	unsigned long no_publique;
	int iRefIndexMembers;
    };	
	
// struct
struct sSALON_PSEUDOORDER{ 
	int iRefIndexMembers;
    };

// struct
struct sSALON_AGEORDER{ 
	int iRefIndexMembers;
    };			
	
/*****************************************************************************/	

int iStackCmpt = -1; //cpmt for sMEMBERS
int iStackSalonCmpt = -1; //cmpt for sSALON_PSEUDOORDER,  sSALON_AGEORDER
bool iPseudoRowsHasNext = false;
struct sMEMBERS sMembers[STACKSIZE];
struct sNOPUB sNoPub[STACKSIZE];
struct sSALON_PSEUDOORDER sSalonPseudoOrder[STACKSIZE];
struct sSALON_AGEORDER sSalonAgeOrder[STACKSIZE];

/*****************************************************************************/

BOOL APIENTRY DllMain( HANDLE hModule, DWORD ul_reason_for_call, LPVOID lpReserved){
	switch(ul_reason_for_call){
		case DLL_PROCESS_ATTACH:
			MessageBox(0,"DLL_PROCESS_ATTACH...","",0);
			break;
		case DLL_THREAD_ATTACH:
			break;
		case DLL_THREAD_DETACH:
			break;
		case DLL_PROCESS_DETACH:
			MessageBox(0,"DLL_PROCESS_DETACH","",0);
			break;
		}
	return TRUE;
	};	

/*****************************************************************************/	
	 
int pushStr(char * str, int iTypeOfEntry){
	/*
	order in split values: NOPUB,PSEUDO,AGE
	
	iTypeOfEntry:
	0 = salon
	1 = carnet
	*/
	//vars declarations
	int iIndex;
	int iStrCharCmpt = -1;
	int iRowsValues = -1;
	char strSplitedStrBuff[256]; //to much size have to allocate differently
	char strSplitedValues[16][256]; //to much size have to allocate differently
	//put it in the stack of all infos
	for(unsigned int i=0; i<strlen(str); i++){
		if(str[i] != '\n' && str[i] != '\u0000' && str[i] != NULL && str[i] != '\0'){
			if(str[i] == ',' || i == (strlen(str)-1)){ //found a splitter or the end of the string)
				if(i == (strlen(str)-1)){
					//push thelast character into the string
					iStrCharCmpt++;
					strSplitedStrBuff[iStrCharCmpt] = str[i];
					}
				strSplitedStrBuff[iStrCharCmpt + 1] = '\0';
				//cpy buff string into the arra values
				iRowsValues++;
				strcpy(strSplitedValues[iRowsValues],strSplitedStrBuff);
				//reset char compter
				iStrCharCmpt = -1;		
			}else{
				//build the string that we aill put int eh members struct array
				iStrCharCmpt++;
				strSplitedStrBuff[iStrCharCmpt] = str[i];
				strSplitedStrBuff[iStrCharCmpt + 1] = '\0';
				}
			}
		}
	//get the index
	iIndex = getMembersIndex(strSplitedValues);
	//insert or replace
	sMembers[iIndex].no_publique = atol(strSplitedValues[0]);
    strcpy(sMembers[iIndex].pseudo,strSplitedValues[1]);
	sMembers[iIndex].age = atoi(strSplitedValues[2]);
	strncpy(sMembers[iIndex].album,strSplitedValues[3],2);
	sMembers[iIndex].photo = atoi(strSplitedValues[4]);
	sMembers[iIndex].vocal = atoi(strSplitedValues[5]);
	sMembers[iIndex].membership = atoi(strSplitedValues[6]);
	sMembers[iIndex].orientation = atoi(strSplitedValues[7]);
	sMembers[iIndex].sexe = atoi(strSplitedValues[8]);
	strncpy(sMembers[iIndex].relation,strSplitedValues[9],8);
	strncpy(sMembers[iIndex].code_pays,strSplitedValues[10],3);
	sMembers[iIndex].region_id = atoi(strSplitedValues[11]);
	sMembers[iIndex].ville_id = atoi(strSplitedValues[12]);
	sMembers[iIndex].etat_civil = atoi(strSplitedValues[13]);
	strcpy(sMembers[iIndex].titre,strSplitedValues[14]); 
	sMembers[iIndex].status = atoi(strSplitedValues[15]);
	
	//if its a new entry initialize vars and reorder stack pseudo and age
	if(iIndex == iStackCmpt){ //new one
		if(iTypeOfEntry == 0){ //salon
			sMembers[iIndex].m_salon = true;
			sMembers[iIndex].m_carnet = false;
			//reorder salon
			iStackSalonCmpt++;
			reorderPseudoSalon(sMembers[iIndex].pseudo);
			reorderAgeSalon(sMembers[iIndex].age);
		}else if(iTypeOfEntry == 1){ //carnet
			sMembers[iIndex].m_salon = false;
			sMembers[iIndex].m_carnet = true;
			}
	}else{
		//not a new one only change the salon flag
		if(iTypeOfEntry == 0){ //salon
			//if it was not there before
			if(!sMembers[iIndex].m_salon){
				sMembers[iIndex].m_salon = true;
				//reorder salon
				iStackSalonCmpt++;
				reorderPseudoSalon(sMembers[iIndex].pseudo);
				reorderAgeSalon(sMembers[iIndex].age);
				}
		}else if(iTypeOfEntry == 1){ //carnet
			sMembers[iIndex].m_carnet = true;
			}
		}
	
	//ref to his own array key
	sMembers[iIndex].iRefIndex = iIndex;
	
	return iIndex;
	};

/*****************************************************************************/	

int getMembersIndex(char strValues[16][256]){
	for(int i=0; i<=iStackCmpt; i++){
		if(sNoPub[i].no_publique == atol(strValues[0])){
			//return the index
			return sNoPub[i].iRefIndexMembers;
			}
		}
	//we didnt find any so lets put it in the array
	iStackCmpt++;
	sNoPub[iStackCmpt].no_publique = atol(strValues[0]);
	sNoPub[iStackCmpt].iRefIndexMembers = iStackCmpt;
	return iStackCmpt;
	};
	
/*****************************************************************************/	
	
void reorderAgeSalon(int iAge){	
	bool bReorder = false;
	unsigned int iStackRefPos;
	//loop and compare
	for(int i=0; i<iStackSalonCmpt && !bReorder; i++){
		//compare char by char
		if(iAge < sMembers[sSalonAgeOrder[i].iRefIndexMembers].age){
			bReorder = true;
			iStackRefPos = i;
			}
		}
	int iTmpMemberRef = iStackCmpt;	
	if(bReorder){
		int iTmpMemberRefOld;	
		//lets push evything
		for(int i=iStackRefPos; i<iStackSalonCmpt; i++){
			iTmpMemberRefOld = sSalonAgeOrder[i].iRefIndexMembers;
			sSalonAgeOrder[i].iRefIndexMembers = iTmpMemberRef;
			iTmpMemberRef = iTmpMemberRefOld;
			}
		}
	sSalonAgeOrder[iStackSalonCmpt].iRefIndexMembers = iTmpMemberRef;
	};	

/*****************************************************************************/	
	
void reorderPseudoSalon(char * strPseudo){	
	bool bReorder = false;
	bool bContinue = true;
	char cToCpmr = '\0';
	char cFromCpmr = '\0';
	unsigned int iStackRefPos;
	unsigned int iShortestString;
	//loop and compare
	for(int i=0; i<iStackSalonCmpt && !bReorder; i++){
		//compare char by char
		iShortestString = strlen(sMembers[sSalonPseudoOrder[i].iRefIndexMembers].pseudo);
		if(iShortestString < strlen(strPseudo)){
			iShortestString = strlen(strPseudo);
			}
		bContinue = true;	
		for(unsigned int j=0; j<iShortestString && !bReorder && bContinue; j++){	
			cToCpmr = checkForSpecialChar(sMembers[sSalonPseudoOrder[i].iRefIndexMembers].pseudo[j]);
			cFromCpmr = checkForSpecialChar(strPseudo[j]);
			if(cFromCpmr < cToCpmr){
				bReorder = true;
				iStackRefPos = i;
			}else if(cFromCpmr > cToCpmr){
				bContinue = false;
				}
			}
		}
	int iTmpMemberRef = iStackCmpt;	
	if(bReorder){
		int iTmpMemberRefOld;	
		//lets push evything
		for(int i=iStackRefPos; i<iStackSalonCmpt; i++){
			iTmpMemberRefOld = sSalonPseudoOrder[i].iRefIndexMembers;
			sSalonPseudoOrder[i].iRefIndexMembers = iTmpMemberRef;
			iTmpMemberRef = iTmpMemberRefOld;
			}
		}
	sSalonPseudoOrder[iStackSalonCmpt].iRefIndexMembers = iTmpMemberRef;
	};
	
/*****************************************************************************/	
	
char checkForSpecialChar(char cChar){
	char strSearchToken[2] = {'à','é'};
	char strSearchReplace[2] = {'a','e'};
	for(unsigned int i=0; i<strlen(strSearchToken); i++){
		if(cChar == strSearchToken[i]){
			return strSearchReplace[i];
			}
		}
	return cChar;	
	};		
	
/*****************************************************************************/	

//get from the last qyery getPseudoRows
bool getPseudoRowsHasNext(){	
	return iPseudoRowsHasNext;
	};

/*****************************************************************************/	
	
char * getSalonByPseudo(bool bFromTop, int iFrom, int iHowMany){
	
	char szMsg[2506];
	char szMsgPost[5012];
	szMsg[0] = '\0';
	szMsgPost[0] = '\0';
		
	if(iFrom > iStackCmpt){
		iPseudoRowsHasNext = false;
		return "<UNILISTING></UNILISTING>\0";
		}
	
	if((iFrom + iHowMany)> (iStackCmpt + 1)){
		iHowMany = (iStackCmpt + 1) - iFrom;
		iPseudoRowsHasNext = false;
	}else{
		iPseudoRowsHasNext = true;
		}
	
	strcpy(szMsgPost,"<UNILISTING>\0");	
	for(int i=iFrom; i<(iHowMany + iFrom); i++){
		strcat(szMsgPost,"<R>\0");
		sprintf(szMsg,"<C n=\"no_publique\">%d</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].no_publique);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"pseudo\">%s</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].pseudo);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"age\">%d</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].age);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"ville_id\">%d</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].ville_id);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"region_id\">%d</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].region_id);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"code_pays\">%s</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].code_pays);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"album\">%s</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].album);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"photo\">%d</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].photo);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"vocal\">%d</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].vocal);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"membership\">%d</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].membership);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"orientation\">%d</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].orientation);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"sexe\">%d</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].sexe);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"titre\">%s</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].titre);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"relation\">%s</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].relation);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"etat_civil\">%d</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].etat_civil);
		strcat(szMsgPost,szMsg);
		sprintf(szMsg,"<C n=\"status\">%d</C>\0",sMembers[sSalonPseudoOrder[i].iRefIndexMembers].status);
		strcat(szMsgPost,szMsg);
		strcat(szMsgPost,"</R>\0");
		}
	strcat(szMsgPost,"</UNILISTING>\0");
	
	if(bFromTop){
		MessageBox(0,szMsgPost,"PSEUDO",0);
		}
	return szMsgPost;
	};		
	
/*****************************************************************************/	
	
void changeStatus(bool bByPseudo, char * str){
	//split the nopub and status
	//vars declarations
	int iStrCharCmpt = -1;
	int iRowsValues = -1;
	char strSplitedStrBuff[40]; //to much size have to allocate differently
	char strSplitedValues[2][40]; //to much size have to allocate differently
	//put it in the stack of all infos
	for(unsigned int i=0; i<strlen(str); i++){
		if(str[i] != '\n' && str[i] != '\u0000' && str[i] != NULL && str[i] != '\0'){
			if(str[i] == ',' || i == (strlen(str)-1)){ //found a splitter or the end of the string)
				if(i == (strlen(str)-1)){
					//push thelast character into the string
					iStrCharCmpt++;
					strSplitedStrBuff[iStrCharCmpt] = str[i];
					}
				strSplitedStrBuff[iStrCharCmpt + 1] = '\0';
				//cpy buff string into the arra values
				iRowsValues++;
				strcpy(strSplitedValues[iRowsValues],strSplitedStrBuff);
				//reset char compter
				iStrCharCmpt = -1;		
			}else{
				//build the string that we aill put int eh members struct array
				iStrCharCmpt++;
				strSplitedStrBuff[iStrCharCmpt] = str[i];
				strSplitedStrBuff[iStrCharCmpt + 1] = '\0';
				}
			}
		}
		
	if(bByPseudo == true){	
		for(int i=0; i<=iStackCmpt; i++){
			if(strcmp(sMembers[sNoPub[i].iRefIndexMembers].pseudo, strSplitedValues[0])	== 0){
				//change the status
				sMembers[sNoPub[i].iRefIndexMembers].status = atoi(strSplitedValues[1]);
				return;
				}
			}
	}else{
		char szMsg[40];
		szMsg[0] = '\0';
		int j = 1;
		while(strSplitedValues[0][j] != '\0'){
			szMsg[j-1] = strSplitedValues[0][j];
			szMsg[j] = '\0';	
			j++;
			}
		//MessageBox(0,szMsg,"NOPUB",0);
		unsigned long lNoPub = atol(szMsg);
		sprintf(szMsg,"(%d)\0",lNoPub);
		//MessageBox(0,szMsg,"NOPUB",0);
		
		for(int i=0; i<=iStackCmpt; i++){
			if(sNoPub[i].no_publique == lNoPub){
				//change the status
				sMembers[sNoPub[i].iRefIndexMembers].status = atoi(strSplitedValues[2]);
				return;
				}
			}
		}
	};
	
/*****************************************************************************/		
	
int getOnlineStatus(bool bByPseudo, char * str){
	int iStrCharCmpt = -1;
	char strBuff[40]; //to much size have to allocate differently
	//put it in the stack of all infos
	for(unsigned int i=0; i<strlen(str); i++){
		if(str[i] != '\n' && str[i] != '\u0000' && str[i] != NULL && str[i] != '\0'){
			iStrCharCmpt++;
			strBuff[iStrCharCmpt] = str[i];
			strBuff[iStrCharCmpt + 1] = '\0';
			}
		}
	for(int i=0; i<=iStackCmpt; i++){
		if(strcmp(sMembers[sNoPub[i].iRefIndexMembers].pseudo, strBuff)	== 0){
			//change the status
			return sMembers[sNoPub[i].iRefIndexMembers].status;
			}
		}
	return 3;		
	}
	
/*****************************************************************************/	
	
void showMembers(){
	char szMsg[1024];
	char szMsgPost[5012];
	szMsg[0] = '\0';
	szMsgPost[0] = '\0';
	for(int i=0; i<=iStackCmpt; i++){
		sprintf(szMsg,"(%d) %s\n\0",i,sMembers[i].pseudo);
		strcat(szMsgPost, szMsg);
		}
	MessageBox(0,szMsgPost,"MEMBERS",0);
	};	
	
/*****************************************************************************/	
	
void showSalon(){
	char szMsg[1024];
	char szMsgPost[5012];
	szMsg[0] = '\0';
	szMsgPost[0] = '\0';
	for(int i=0; i<=iStackSalonCmpt; i++){
		sprintf(szMsg,"(%d) %s\n\0",sSalonPseudoOrder[i].iRefIndexMembers,sMembers[sSalonPseudoOrder[i].iRefIndexMembers].pseudo);
		strcat(szMsgPost, szMsg);
		}
	MessageBox(0,szMsgPost,"SALON",0);
	};		
	
/*****************************************************************************/	
