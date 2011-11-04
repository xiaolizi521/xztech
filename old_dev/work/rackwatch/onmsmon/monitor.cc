/*
**
** OpenNMS Daemon Monitor
** Author: Adam Hubscher
** Date: Q3 '08
**
** Purpose:

This application is written to check for the existence of a process:

onmsmon.php

And if it is not running, run it.

This will run in the background at all times.

Various checks can exist... however at the moment it is very basic in order to achieve the desired effect.

Full functionality to be added later.

**
*/

#include <iostream>
#include <iomanip>
#include <time.h>
#include <fstream>
#include <stdio.h>
#include <string>
#include <sys/types.h>
#include <sys/wait.h>
#include <unistd.h>
#include <signal.h>

using namespace std;

pid_t childPid;
pid_t waitResult;

#define MAXLEN 100

void terminate (int param) {
	
	kill(childPid, SIGHUP);
	
	exit(0);
}

int checkRunning(char ProcessName[100]) {

	char buffer[100];
	string cmd;
	sprintf(buffer, "ps -efl | grep %s | grep -v grep", ProcessName);
	
	//	char psBuffer[128];
	FILE *pPipe = popen(buffer, "r" );
	char ch = '\0';
	int c = 0;
	
	if( pPipe == NULL )
		exit( 1 );
		
	while( ch!=EOF )
	{
		ch = fgetc(pPipe);
		if(ch == '\n') c++;
		
		//if( fgets( psBuffer, 128, pPipe) != NULL )
		//	printf( psBuffer );
	}
	
	pclose(pPipe);
	
	return c;
	
}

char *getTime() {

	time_t t = time(0);
	struct tm* lt = localtime(&t);

	static char tmpTime[15];

	sprintf(tmpTime, "%04d-%02d/%02d %02d:%02d:%02d", lt->tm_year + 1900, lt->tm_mon + 1, lt->tm_mday, lt->tm_hour, lt->tm_min, lt->tm_sec);
	
	return tmpTime;
}

int main (int argc, char * const argv[]) {
	
	void (*prev_fn)(int);

	prev_fn = signal (SIGTERM,terminate);
	if (prev_fn==SIG_IGN) signal (SIGTERM,SIG_IGN);
	
	// char currTime[15];

	int childStatus;
	char* childArgs[2] = {NULL};
	bool childExited = true;

	cout << "[" << getTime() <<  "] - Starting OpenNMS Monitoring Daemon" << endl;
	
	while(true) {
		
		if (childExited = true) childExited = true;
		
		childPid = fork();
		
		switch (childPid) {
		
			case -1: // I am the parent. Failed to execute child process.
				cout << "[" << getTime() <<  "] - Failed to fork new process." << endl;
				break;
			
			case 0: // I am the child. Turn myself into the desired process.
				
				childArgs[0] = "onmsmon.php";
				childArgs[1] = "start";
				
				// Checking if script is running
				cout << "[" << getTime() <<  "] - Checking if onmsmon.php is running." << endl;
				
				if( checkRunning("onmsmon.php") == 0 ) {
					
					cout << "[" << getTime() <<  "] - Monitoring script appears to have died. Restarting." << endl;
					
					execv("/opt/opennms/monitor/onmsmon.php", childArgs);
					
					cout << "[" << getTime() <<  "] - Execution finished or died. Recycling." << endl;
					
					#if defined(__cplusplus) || defined(_cplusplus)
						terminate();
					#else
						abort();
					#endif
				}
				
				else {
					
					cout << "[" << getTime() << "] - Monitoring Script is running fine. Will check again in 2 minutes." << endl;
					
				}
				
				break;
			
			default: // I am the parent. Do as parents do?
				
				waitResult = waitpid( childPid, &childStatus, 0 );
				
				if (waitResult == childPid && !childExited) {
				
					sleep(120); childExited = true;
				}
				
				break;
		}

		waitResult = waitpid( childPid, &childStatus, 0 );
		
		if (waitResult == childPid && !childExited) {
		
			sleep(120); childExited = true;
		}
	}	
			
	return 0;
}

