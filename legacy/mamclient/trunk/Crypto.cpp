/*
 *  Crypto.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 10/4/08.
 *  Copyright 2008 __MyCompanyName__. All rights reserved.
 *
 */

#include "Crypto.h"
#include "keys.h"

Crypto::Crypto( void )
{
    this->m_useNewKeys = false;
}

Crypto::~Crypto( void )
{
}

void Crypto::incoming( char *packet, int size )
{
    for ( int i = 0; i < size; i++ )
    {
        int num = this->m_remoteCounter.first;
		
        packet[i] = ( ( packet[i] ^ Key1[num] ) ^ Key2[this->m_remoteCounter.second] );
		
        this->m_remoteCounter++;
    }
}

void Crypto::outgoing( char *packet, int size )
{
    for ( int i = 0; i < size; i++ )
    {
        int num = this->m_localCounter.first;
		
        if ( this->m_useNewKeys )
            packet[i] = ( ( packet[i] ^ Key3[num] ) ^ Key4[this->m_localCounter.second] );
        else
            packet[i] = ( ( packet[i] ^ Key1[num] ) ^ Key2[this->m_localCounter.second] );
		
        this->m_localCounter++;
    }
}

void Crypto::generate_keys( CPacket packet )
{
    int seed, seed2;
    
    memcpy( ( void * )&seed, ( void * )( packet.data + 0x04 ), 4 );
    
    seed2 = seed * seed;
    
    for ( int i = 0; i < 256; i += 4 )
    {
        int *pKey3 = ( int * )&Key3[i];
        int *pKey4 = ( int * )&Key4[i];

        *pKey3 = *( int * )&Key1[i] ^ *( int * )&seed;
        *pKey4 = *( int * )&Key2[i] ^ *( int * )&seed2;
    }
}