/*
 *  Crypto.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 10/4/08.
 *  Copyright 2008 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _CRYPTO_H
#define _CRYPTO_H

#include "Counter.h"
#include "define.h"
#include <string.h>

class Crypto
{
public:
	
    Crypto( void );
    ~Crypto( void );
	
    void incoming( char *packet, int size );
    void outgoing( char *packet, int size );
	
    void generate_keys( CPacket packet );
    void use_new_keys( void ) {	this->m_useNewKeys = true; }
    void reset_counters( void ) { this->m_localCounter.reset(); this->m_remoteCounter.reset(); }
	
private:
				  
    bool    m_useNewKeys;
	
    Counter m_localCounter;
    Counter m_remoteCounter;
	
};

#endif