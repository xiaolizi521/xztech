/*
 *  NpcInfoPacket.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/8/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "NpcInfoPacket.h"

NpcInfoPacket::NpcInfoPacket( CPacket packet ) : m_id(0), m_x(0), m_y(0), m_type(0), m_look(0)
{
    NPC_INFO_PACKET info;
    
    memcpy( ( void * )&info, ( void * )packet.data, ( packet.header.size - sizeof( CPacketHeader ) ) );
    
    this->m_id      = info.id;
    this->m_type    = info.type;
    this->m_look    = info.look;
    this->m_x       = info.x;
    this->m_y       = info.y;
    
    strcpy( this->m_name, info.name );
}