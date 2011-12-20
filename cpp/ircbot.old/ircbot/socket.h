//
//  socket.h
//  ircbot
//
//  Created by Adam Hubscher on 12/14/11.
//  Copyright 2011 Enova Financial. All rights reserved.
//

#ifndef ircbot_socket_h
#define ircbot_socket_h

#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <netdb.h>
#include <unistd.h>
#include <arpa/inet.h>
#include <sys/time.h>
#include <errno.h>
#include <fcntl.h>
#include <iostream>
#include <stdint.h>
#include <sys/time.h>
#include <string.h>
#include <stdio.h>
#include <ctype.h>
#include <string>
#include <sstream>
#include <fstream>
#include <map>
#include <vector>
#include <errno.h>
#include <time.h>
#include <stdlib.h>
#include "define.h"

class Socket
{
    
public:
    
    Socket( void );
    Socket(const Socket&);
    Socket& operator=(const Socket &x);
    virtual ~Socket( void );
    
    bool Init( void );
    bool Create( void );
    bool Connect(const char *host, int32_t port);
    bool Close( void ) { return ::close( this->m_sock ); }
    
    void Select( void ) {
        
        struct timeval tv;
        
        tv.tv_sec   = 30;
        tv.tv_usec  = 0;
        
        FD_ZERO( &rDescript );
        FD_SET(m_sock, &rDescript );
        
        ::select(m_sock + 1, &rDescript, NULL, NULL, &tv );
        
    }
    
    
    bool Send( const char *data, size_t size) const;
    ssize_t Read( const char *buffer, size_t size) const;
    
    bool sValid ( void ) const { return m_sock != -1; }
    bool pValid( void ) { return ( FD_ISSET( this->m_sock, &this->rDescript ) ? true : false ); }
    ssize_t pRead( irc_host *host );
    bool pSend( CPacket packet );
    void set_nonblock( void ) { fcntl( this->m_sock, F_SETFL, O_NONBLOCK ); }
    

private:
    
    int             m_sock;
    sockaddr_in     m_addr;
    fd_set          rDescript;
    
                                        
};

#endif
