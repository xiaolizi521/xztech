/*
 *  ircbot.h
 *  ircbot
 *
 *  Created by Adam Hubscher on 2/24/11.
 *
 */
#include <stdio.h>
#include <stdarg.h>
#include <sys/socket.h>
#include <iostream>
#include <cstdlib>
#include <netdb.h>
#include <cstring>
#include <errno.h>
#include <arpa/inet.h>
#include <sys/utsname.h>

using namespace std;

#ifndef MAX_MESSAGE_SIZE
#define MAX_MESSAGE_SIZE 512
#endif

#ifndef DEBUG
#define DEBUG 1
#endif

#ifndef MAX_BUFFQ
#define MAX_BUFFQ 1024
#endif

#ifndef MAX_NICK_SIZE
#define MAX_NICK_SIZE 12
#endif

class IRC;

struct packet {
	
	string nick;
	string ident;
	string host;
	string target;
	string command;
	string message;
    
};


class IRC {
    
public:
    
	// These are normal methods
    IRC(char *name, char *host, int port) {
        
        // Set up the connection information
        _myName = new char[strlen(name)+1];
        strcpy(_myName,name);
        
        _host = new char[strlen(_host)+1];
        strcpy(_host,host);
        
        _port = port;
    }
	~IRC();
	int Connect();
	void Disconnect();
	int theLoop();
    int _quit(string);
	
	// These are data handling methods
	void _buffer(char*);
	void _handler(char*);
	
private:
	
	// Descriptors for Input and Output Buffers
	FILE* _output;
	FILE* _input;
	
	// Informative Variables for Processing
	int _sock;
	bool _connected;
	char* _myName;
	char* _host;
	int _port;
	
	// Structs
	packet* _recv_pkt;
};