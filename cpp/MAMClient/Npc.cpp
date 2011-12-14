/*
 *  Npc.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/8/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "Npc.h"

Npc::Npc( void ) : m_y(0), m_id(0), m_look(0), m_type(0), m_x(0)
{
}

Npc::Npc( NpcInfoPacket *info ) : 
  m_y(info->m_y)
, m_id(info->m_id)
, m_look(info->m_look)
, m_type(info->m_type)
, m_x(info->m_x)
{
    
    strcpy( this->m_name, info->m_name );
}