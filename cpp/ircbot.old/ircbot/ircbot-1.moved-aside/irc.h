/*
 *  ircbot.h
 *  ircbot
 *
 *  Created by Adam Hubscher on 2/24/11.
 *  Copyright 2011 Server Beach. All rights reserved.
 *
 */
using namespace std;

//#include <mysql.h>

#ifndef MAX_MESSAGE_SIZE
#define MAX_MESSAGE_SIZE 512
#endif

#ifndef MAX_BUFFQ
#define MAX_BUFFQ 1024
#endif

#ifndef MAX_NICK_SIZE
#define MAX_NICK_SIZE 12
#endif

class IRC;

struct ircPktData {
	char* nick;
	char* ident;
	char* host;
	char* target;
};

typedef int (IRC::*_func_ptr)(char*, ircPktData*, void*);

struct cmdHook {
	char* cmd;
	_func_ptr funcptr;
	cmdHook* next;
};

struct packet {
	
	string nick;
	string ident;
	string host;
	string target;
	string command;
	string message;

};

struct ircUser {
	char* nick;
	char* channel;
	char flags;
	ircUser* next;
};

class IRC {

public:

	// These are normal methods
	IRC();
	~IRC();
	int Connect();
	void Disconnect();
	int theLoop();
	void _cmdHook(char*, int);
	void _call_hook(char*, char*, ircPktData*);
	
	// These are hook methods, based on expected IRC commands
	int _privmsg(char*, char*);
	int _privmsg(char*, ...);
	int _notice(char*, char*);
	int _notice(char*, ...);
	int _join(char*, ircPktData*, void*);
	int _part(string);
	int _kick(char*, char*);
	int _kick(char*, char*, char*);
	int _mode(char*);
	int _mode(char*, char*, char*);
	int _nick(char*);
	int _quit(string);
	int _raw(char*);
	
	// These are data handling methods
	void _buffer(char*);
	void _handler(char*);
	
	// Hook Handlers
	//void _ircHook(char*, int(*_func_ptr)(char*, ircPktData*, void*));
	void _ircHook(char*, _func_ptr p);
	
	// Simple public functions
	char* _myNickName();
	
private:
	
	// Descriptors for Input and Output Buffers
	FILE* _output;
	FILE* _input;
	
	// Informative Variables for Processing
	int _sock;
	bool _connected;
	bool _ident;
	bool _uPass;
	bool _uName;
	char _myName[40];
	char _host[40];
	int _port;
	
	// Hooks
	void _addHook(cmdHook*, char*, _func_ptr p);
	//void _addHook(cmdHook*, char*, int(*_func_ptr)(char*, ircPktData*, void*));
	void _delHook(cmdHook*);
	
	// Structs
	cmdHook* _myHooks;
	ircUser* _users;
	packet* _recv_pkt;
};

IRC::IRC() {

	char name[] = "jenni";
	char host[] = "irc.x-zen.cx";
	char join[5] = "join";

	// Set up the connection information
	strcpy(_myName,name);
	strcpy(_host,host);
	_port = 6667;
	
	_ircHook(join, &IRC::_join);
}

int IRC::Connect() {
	
	hostent* host;
	sockaddr_in addr;
	char *nick;
	
	host = gethostbyname(_host);
	
	if (!host || _connected) {
		
		return 1;
	}
	
	_sock = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
	
	if (_sock == -1) {
		return 1;
	}
	
	memcpy(&addr.sin_addr, host->h_addr, 4);
	addr.sin_family=AF_INET;
	addr.sin_port=htons(_port);
	
	if (connect(_sock, (const sockaddr*)&addr, sizeof(addr)) == -1) {
		
		close(_sock);
		return 1;
	}
	
	_output = fdopen(_sock, "w");
	
	if (!_output) {
		close(_sock);
		return 1;
	}
	
	_connected = true;
	
	nick = new char[strlen(_myName)+1];
	strcpy(nick, _myName);
	
	fprintf(_output, "PASS %s\r\n", nick);
	fprintf(_output, "NICK %s\r\n", nick);
	fprintf(_output, "USER %s * 0 :%s\r\n", nick, nick);
	fflush(_output);
	
	return 1;
}
			
void IRC::Disconnect() {
	
	if(_connected) {
		
		printf("Disconnected.\n");
		_connected=false;
		
		_quit("So long, and thanks for all the fish.");
		
		fclose(_output);
		
		close(_sock);
	}
}

int IRC::theLoop() {
	
	char buffer[MAX_BUFFQ];
	size_t _recv;
	
	if (!_connected) {
		
		return 1;
	}
	
	while (1) {
		
		_recv = recv(_sock, buffer, 1023, 0);
		
		if (_recv == -1 || !_recv) return 1;
		
		buffer[_recv] = '\0';
		
		_buffer(buffer);
	}
	
	return 0;
	
}

void IRC::_buffer(char* buffq) {
	
	char* pos;
	
	while (pos = strstr(buffq, "\r\n")) {
		
		*pos = '\0';
		_handler(buffq);
		
		buffq = pos+2;
	}
}

void IRC::_handler(char* buffq) {
	
	char* command;
	char* params;
	
	size_t pos, lastpos, x, y;
	
	string in = buffq;
	string cmd, parm;
	ircPktData chunk;
	packet packet;
	
	printf("[RECV] %s\n", buffq);
	
	pos = 0;
	lastpos = 0;

	if(in[0] == ':') {
		
		// The leading : is useless.
		in.erase(0,1);

		// Nickname of the sender
		pos = in.find("!");
		packet.nick = in.substr(0,pos);

		// IDENT string of sender
		lastpos = pos;
		pos = in.find("@", lastpos);
		packet.ident = in.substr(lastpos+1,(pos - (lastpos+1)));

		// HOSTNAME of sender
		lastpos = pos;
		pos = in.find(" ", lastpos);
		packet.host = in.substr(lastpos+1,(pos - (lastpos+1)));

		// RFC IRC Command of sender (IE: PRIVMSG)
		lastpos = pos;
		pos = in.find(" ", lastpos+1);
		packet.command = in.substr(lastpos+1,(pos - (lastpos+1)));

		// Target of sent message (IE: #blah)
		lastpos = pos;
		pos = in.find(" ", lastpos+1);
		packet.target = in.substr(lastpos+1,(pos - (lastpos+1)));

		// Message of command
		lastpos = pos;
		packet.message = in.substr(lastpos+1);
		if(packet.message[0] == ':') {
			packet.message.erase(0,1);
		}

		printf("Packet Info\n");
		printf("Nick: %s Ident: %s Host: %s\n", packet.nick.c_str(), packet.ident.c_str(), packet.host.c_str());
		printf("Target: %s Command: %s\n", packet.target.c_str(), packet.command.c_str());
		printf("Message: %s\n", packet.message.c_str());		

		if (packet.command == "PRIVMSG") {
		
			if(!(packet.message.find(_myName) == std::string::npos)) {

								
				x = strlen(_myName) - 1;

				y = packet.message.find(":", x);
				
				if(y > 0) x = x + (y - x);
				
				x = packet.message.find(" ");
				
				if((y - x) == 1) { 
					
					x = x + (y - x);
					y = packet.message.find(" ", x+1);
				}
				
				cmd = packet.message.substr(x+1, ((y-1)-x));
				
				parm = packet.message.substr(y+1);
				
				printf("COMMAND: %s PARAMS: %s\n", cmd.c_str(), parm.c_str());
			}
		}
	}

	else {
		
		command = buffq;
		buffq = strchr(command, ' ');
		
		if(!buffq) return;
		
		*buffq = '\0';
		params=buffq+1;
		
		// PING? PONG!
		if(!strcmp(command, "PING")) {
			
			if(!params) return;
			
			fprintf(_output, "PONG %s\r\n", &params[1]);
			fflush(_output);
			
			printf("PONG %s\n", &params[1]);
		}
		
		else {
			
			chunk.host = 0;
			chunk.ident = 0;
			chunk.nick = 0;
			chunk.target = 0;
			_call_hook(command, params, &chunk);
		}
	}
}

int IRC::_privmsg(char* target, char* message) {
	
	if(!_connected) return 1;
	
	fprintf(_output, "PRIVMSG %s :%s\r\n", target, message);
	return fflush(_output);
}


void IRC::_call_hook(char* command, char* params, ircPktData* chunk) {
	
}



void IRC::_addHook(cmdHook* hook, char* command, _func_ptr p) {
	
	if (hook->funcptr) {
		if (!hook->next) {
			
			hook->next = new cmdHook;
			hook->next->funcptr=0;
			hook->next->cmd = 0;
			hook->next->next = 0;
		}
		
		_addHook(hook->next, command, p);
	}
	else {
		hook->funcptr = p;
		hook->cmd = new char[strlen(command)+1];
		strcpy(hook->cmd, command);
	}
}

void IRC::_ircHook(char* command, _func_ptr p) {
	
	printf("Registering Hook for Command %s\n", command);
	if (!_myHooks) {
		
		_myHooks = new cmdHook;
		_myHooks->funcptr = 0;
		_myHooks->cmd = 0;
		_myHooks->next = 0;
		_addHook(_myHooks, command, p);
	}
	else {
		_addHook(_myHooks, command, p);
	}
}

void IRC::_delHook(cmdHook* hook) {
	
	if(hook->next) _delHook(hook->next);
	if(hook->cmd) delete hook->cmd;
	
	delete hook;
	
}

int IRC::_join(char* params, ircPktData* chunk, void* bot) {
	
	if(_connected) {
		fprintf(_output, "JOIN %s\r\n", params);
		fflush(_output);
		return 1;
	}
	
	return 0;
}
	
int IRC::_part(string channel) {
	
	if(_connected) {
		fprintf(_output, "PART %s\r\n", channel.c_str());
		fflush(_output);
		return 1;
	}
	
	return 0;
}

int IRC::_quit(string message) {
	
	if (_connected) {
		
		if (message.length() > 0) fprintf(_output, "QUIT %s\r\n", message.c_str());
		else fprintf(_output, "QUIT\r\n");
		
		fflush(_output);
		return 1;
	}
	
	return 0;
}