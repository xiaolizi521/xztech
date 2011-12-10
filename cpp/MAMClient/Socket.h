/*
 *  Socket.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 10/7/08.
 *  Copyright 2008 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _SOCKET_H
#define _SOCKET_H

#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <netdb.h>
#include <unistd.h>
#include <string>
#include <arpa/inet.h>
#include <sys/time.h>
#include <errno.h>
#include <fcntl.h>
#include <iostream>
#include "define.h"
#include <Utilities.h>
#include "CryptoStuff.h"

class Socket
{
public:
	
    Socket( CryptoStuff *crypt );
    virtual ~Socket( void );
	
    bool create( void );
    bool connect( const char *host, const int port );
	
    void select( void );
    bool is_readable( void );
    void set_nonblock( void ) { fcntl( this->m_sock, F_SETFL, O_NONBLOCK ); }
	
    bool send( const char *data, int size ) const;
    int read( const char *buffer, int size ) const;
	
    bool is_valid( void ) const { return m_sock != -1; }
    
    int close( void ) { return ::close( this->m_sock ); }
    
    CPacket read_packet( void );
    bool    send_packet( CPacket packet );
	
private:
	
    int         m_sock;
    sockaddr_in m_addr;
    fd_set      readfds;
    CryptoStuff      *crypto;
	
};

#endif