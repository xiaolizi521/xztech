/*
 *  NpcDialoguePacket.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/9/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "NpcDialoguePacket.h"

NpcDialoguePacket::NpcDialoguePacket( CPacket packet )
{
    BYTE    nStrings    = *( BYTE * )( packet.data + 0x08 );
    int     index       = 10;
    bool    is_option   = true;
    
    for ( int i = 0; i < nStrings; i++ )
    {
        char    buffer[255];
        BYTE    length = *( BYTE * )( packet.data + ( index - 1 ) );
        
        memcpy( ( void * )buffer, ( void * )( packet.data + index ), 
               length );
        
        index += ( length + 1 );
        
        ( length == 0 ) ? is_option = false : NULL;

        if ( is_option )
        {
            std::string option = buffer;
            
            this->m_options[i+1] = option;
            
            this->m_nOptions++;
        }
        else
            strcpy( this->m_text, buffer );
    }
}