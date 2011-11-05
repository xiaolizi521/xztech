/*
 *  irc.cpp
 *  ircbot
 *
 *  Created by Adam Hubscher on 3/9/11.
 *  Copyright 2011 Server Beach. All rights reserved.
 *
 */

#include "irc.h"
#include "event.h"

int IRC::Connect() {
	
	hostent* host;
	sockaddr_in addr;
	
    // Resolve the host provided to an IP address and a real location
	host = gethostbyname(_host);
	
	if (!host || _connected) {
		
		return 1;
	}
	
    // Create/Open a New Socket
	_sock = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
	
	if (_sock == -1) {
		return 1;
	}
	
    // Set the destination address and its appropriate type
	memcpy(&addr.sin_addr, host->h_addr, 4);
	addr.sin_family=AF_INET;
	addr.sin_port=htons(_port);
	
    // Connect the socket to the destination IRC server
	if (connect(_sock, (const sockaddr*)&addr, sizeof(addr)) == -1) {
		
		close(_sock);
		return 1;
	}
	
    // Open a writable stream pointer to the new socket.
	_output = fdopen(_sock, "w");
	
	if (!_output) {
		close(_sock);
        return 1;
	}
	
    // Connection complete
	_connected = true;
	
    // Negotiation of the connection now takes place
	fprintf(_output, "PASS %s\r\n", _myName);
	fprintf(_output, "NICK %s\r\n", _myName);
	fprintf(_output, "USER %s * 0 :%s\r\n", _myName, _myName);
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
   	char* pos;
	
	if (!_connected) {
		
		return 1;
	}
	
	while (1) {
		
		_recv = recv(_sock, buffer, 1023, 0);
		
		if (_recv == -1 || !_recv) return 1;
		
		buffer[_recv] = '\0';
    
        char* buffq = buffer;
        
        while (pos == strstr(buffq, "\r\n")) {
            
            *pos = '\0';
            _handler(buffq);
            
            buffq = pos+2;
        }
        
	}

	return 0;
	
}

void IRC::_handler(char* buffq) {
	
	char* command;
	char* params;
	
	size_t pos(0), lastpos(0);
	
	string in = buffq;
	string cmd, parm;
	packet packet;
	
    #ifdef DEBUG
	printf("[RECV] %s\n", buffq);
    #endif
    
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
        
        #ifdef DEBUG
		printf("Packet Info\n");
		printf("Nick: %s Ident: %s Host: %s\n", packet.nick.c_str(), packet.ident.c_str(), packet.host.c_str());
		printf("Target: %s Command: %s\n", packet.target.c_str(), packet.command.c_str());
		printf("Message: %s\n", packet.message.c_str());		
        #endif
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

	}
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