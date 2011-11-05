/*
 *  Npc.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/8/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "Npc.h"

Npc::Npc( void )
{
}

Npc::Npc( NpcInfoPacket *info )
{
    this->m_id      = info->m_id;
    this->m_type    = info->m_type;
    this->m_look    = info->m_look;
    this->m_x       = info->m_x;
    this->m_y       = info->m_y;
    
    strcpy( this->m_name, info->m_name );
}