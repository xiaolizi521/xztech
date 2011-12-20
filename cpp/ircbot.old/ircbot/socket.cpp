//
//  socket.cpp
//  ircbot
//
//  Created by Adam Hubscher on 12/15/11.
//  Copyright 2011 Enova Financial. All rights reserved.
//

#include "socket.h"

// Constructor
Socket::Socket() :
  m_sock( -1 )
, m_addr()
, rDescript() {
    
    memset( &this->m_addr, 0, sizeof( this->m_addr ) );
    
}

// Override

Socket::Socket(const Socket &x) :
  m_sock(x.m_sock)
, m_addr(x.m_addr)
, rDescript(x.rDescript) {}

// Destructor

Socket::~Socket(void) {}

// Create a new Socket, prep for opening a connection

bool Socket::Create( void ) {
 
    int32_t on = 1;
    
    this->m_sock = socket( AF_INET, SOCK_STREAM, IPPROTO_IP );
    
    if ( !this->sValid() ) return false;
    
    if ( setsockopt( this->m_sock
                    , SOL_SOCKET
                    , SO_REUSEADDR
                    , (const char *) &on
                    , sizeof( on ) ) == -1 ) 
        return false;
 
    return true;
}

// Connect Socket m_sock to a host:port via TCP

bool Socket::Connect(const char *host, int32_t port) {
    
    int32_t status;
    
    hostent* server = gethostbyname(host);
        
    if (!this->Create() || !this->sValid()) return false;
    
    this->m_addr.sin_family = AF_INET;
    this->m_addr.sin_port   = htons( 6667 );
    
    this->m_addr.sin_addr = *((struct in_addr *) server->h_addr);
    
    
    if (errno == EAFNOSUPPORT) return false;
    
    status = connect( this->m_sock, ( sockaddr * )&this->m_addr, sizeof( this->m_addr ) );
    
    if (status == 0 ) return true;
    
    return false;
}

bool Socket::Send(const char *data, size_t size) const {
    
    intptr_t status = 0;
    size_t nSent = 0;
    
    while (nSent < size )
    {
        status = ::send( this->m_sock, data + nSent, size - nSent, 0 );
        
        if ( status == -1 ) break;
        
        nSent += status;
    }
    
    if ( status == -1 ) return false;
    
    return true;
}

ssize_t Socket::Read(const char *buffer, size_t size) const {

    ssize_t status = 0;
    char rBuffer[0x1000];
    size_t nReceived = 0;
    
    memset( rBuffer, 0, 0x1000 );
    
    while ( nReceived < size ) {
        
        status = :: recv( this->m_sock, rBuffer + nReceived, size - nReceived, 0 );
        
        if ( status == -1 ) break;
        
        nReceived += status;
    }
    
    if ( status == -1 || status == 0 ) return status;
    
    memcpy( ( void * ) buffer, ( void * ) rBuffer, size );
    
    return status;
}

ssize_t Socket::pRead(irc_host *host) {

    ssize_t status;
    
    char c, c_next;
    
    int32_t i;
    
    char tmp[BUF_SIZE];
    
    memset(tmp, '\0', BUF_SIZE);
    i = 0;

    c = '\0';
    c_next = '\0';
    
    //this->Select();
    
    while ( (c != '\r' || c_next != '\n') && i < BUF_SIZE) {
        
        status = read(this->m_sock, &c, 1);
        
        /*if ( status == 0 ) {
            
            printf("-2\n");
            return -2;
        }
        else if ( status == -1)
        {
            printf("-1\n");
            return -1;
        }*/
        
        tmp[i++] = c;
        
        status = recv(this->m_sock, &c_next, 1, MSG_PEEK);
        
        if (status == -1) return status;
        
    }
    
    if (c_next == '\n') {
        
        status = read(this->m_sock, &c, 1);
        
        if ( status == -1 ) return status;
    }
    
    if(host->data) free(host->data);
    
    if(i > 0) {
        
        host->data = (char *) malloc(i + 1);
        host->data[i-1] = '\0';
        strncpy(host->data, tmp, i-1);
        
    }
    printf("In pRead, packet received: %s\n", host->data);
    
    return i;
}

bool Socket::pSend(CPacket packet) {
    
    char buffer[MAX_MESSAGE_SIZE];
    bool sent;
    
    memcpy( (void *) buffer, (void *) &packet, packet.header.size);
    
    sent = this->Send( buffer, packet.header.size );
    
    if ( !sent ) std::cout << "Error in pSend() ... " << errno << std::endl;
    
    return sent;
}

Socket& Socket::operator=(const Socket &x) {
    
    if (this == &x) return *this;
    
    m_sock = x.m_sock;
    m_addr = x.m_addr;
    rDescript = x.rDescript;
    
    return *this;
}