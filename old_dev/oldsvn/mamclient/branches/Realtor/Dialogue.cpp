/*
 *  Dialogue.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/9/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "Dialogue.h"

Dialogue::Dialogue( void )
{
    this->m_open = false;
}

void Dialogue::process( NpcDialoguePacket *packet )
{
    this->m_optionList  = packet->m_options;
    this->m_nOptions    = this->m_optionList.size();
    
    strcpy( this->m_text, packet->m_text );
    
    this->m_open        = true;
}

void Dialogue::close( void )
{
    this->m_optionList.clear();
    
    memset( ( void * )this->m_text, 0, sizeof( this->m_text ) );
    
    this->m_open = false;
}
