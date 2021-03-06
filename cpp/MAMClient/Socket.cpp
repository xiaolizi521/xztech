/*
 *  Socket.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 10/7/08.
 *  Copyright 2008 __MyCompanyName__. All rights reserved.
 *
 */

#include "Socket.h"

Socket::Socket( CryptoStuff *crypt ) : 
  m_sock( -1 )
, m_addr()
, readfds()
, crypto(crypt)
{
    memset( &this->m_addr, 0, sizeof( this->m_addr ) );
    this->crypto = crypt;
}

Socket::Socket(const Socket &other) :
  m_sock(other.m_sock)
, m_addr(other.m_addr)
, readfds(other.readfds)
, crypto(other.crypto)
{}

Socket::~Socket( void )
{
}

bool Socket::create( void )
{
    int on = 1;
	
    this->m_sock = socket( AF_INET, SOCK_STREAM, IPPROTO_IP );
	
    if ( !this->is_valid() )
        return false;
	
    if ( setsockopt( this->m_sock, SOL_SOCKET, SO_REUSEADDR, ( const char * )&on, sizeof( on ) ) == -1 )
        return false;
	
    return true;
}

bool Socket::connect( const char *host, const int port )
{
    int status;
	
    if ( !this->create() )
        return false;
	
    if ( !this->is_valid() )
        return false;
	
    this->m_addr.sin_family = AF_INET;
    this->m_addr.sin_port   = htons( port );
	
    status = inet_pton( AF_INET, host, &this->m_addr.sin_addr );
	
    if ( errno == EAFNOSUPPORT )
        return false;
	
    status = ::connect( this->m_sock, ( sockaddr * )&this->m_addr, sizeof( this->m_addr ) );
	
    if ( status == 0 )
        return true;
	
    return false;
}

void Socket::select( void )
{
    struct timeval tv;
	
    tv.tv_sec   = 30;
    tv.tv_usec  = 0;
	
    FD_ZERO( &this->readfds );
    FD_SET( this->m_sock, &this->readfds );
	
    ::select( this->m_sock + 1, &this->readfds, NULL, NULL, &tv );
    
}

bool Socket::is_readable( void )
{
    if ( FD_ISSET( this->m_sock, &this->readfds ) )
        return true;
	
    return false;
}

bool Socket::send( const char *data, size_t size ) const
{
    intptr_t status = 0;
	size_t nSent = 0;
    
    while ( nSent < size )
    {
        status = ::send( this->m_sock, data + nSent, size - nSent, 0 );
        
        if ( status == -1 )
            break;
        
        nSent += status;
    }

    if ( status == -1 )
        return false;
	
    return true;
}

ssize_t Socket::read( const char *buffer, size_t size ) const
{
    ssize_t     status = 0;
    char    rBuffer[0x1000];
	size_t     nRecieved = 0;
    
    memset( rBuffer, 0, 0x1000 );
	
    while ( nRecieved < size )
    {
        status = ::recv( this->m_sock, rBuffer + nRecieved, size  - nRecieved, 0 );
        
        if ( status == -1 )
            break;
        
        nRecieved += status;
    }
        
    if ( status == -1 || status == 0 )
        return status;

    memcpy( ( void * )buffer, ( void * )rBuffer, size );
	
    return status;
}

CPacket Socket::read_packet( void )
{
    CPacket packet;
    char    pHeader[4];
    ssize_t     status;
	
    status = this->read( pHeader, sizeof( pHeader ) );
	
    if ( status == 0 )
    {
        std::cout << "Error in read_packet() ... Empty packet..." << std::endl;
        packet.header.size  = 0;
        packet.header.id    = 0;
        
        return packet;
    }
	
    if ( status == -1 )
        std::cout << "Error in read_packet() ... " << errno << std::endl;
	
    this->crypto->incoming( pHeader, sizeof( pHeader ) );
	
    packet.header = *( CPacketHeader * )pHeader;
	
    if ( packet.header.size < 0 || packet.header.size > MAXPACKETSIZE )
    {
        packet.header.size  = 0;
        packet.header.id    = 0;
        
        return packet;
    }
	
    status = this->read( packet.data, ( packet.header.size - sizeof( CPacketHeader ) ) );
	
    this->crypto->incoming( packet.data, (size_t)( packet.header.size - sizeof( CPacketHeader ) ) );
    
    return packet;
}

bool Socket::send_packet( CPacket packet )
{
    char buffer[MAXPACKETSIZE];
    bool sent;

    memcpy( ( void * )buffer, ( void * )&packet, packet.header.size );

    this->crypto->outgoing( buffer, packet.header.size );
	
    sent = this->send( buffer, packet.header.size );
	
    if ( !sent )
        std::cout << "Error in send_packet() ... " << errno << std::endl;
	
    return sent;
}

Socket& Socket::operator=(const Socket &other) {
    
    if (this == &other) return *this;
    
    m_sock = other.m_sock;
    m_addr = other.m_addr;
    readfds = other.readfds;
    crypto = other.crypto;
    
    return *this;
}
