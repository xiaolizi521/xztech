/*
 *  NpcUpdatePacket.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/8/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "NpcUpdatePacket.h"

NpcUpdatePacket::NpcUpdatePacket( CPacket packet )
{
    NPC_UPDATE_PACKET update;
    
    memcpy( ( void * )&update, ( void * )packet.data, ( packet.header.size - sizeof( CPacketHeader ) ) );
    
    this->m_npcID   = update.npc_id;
    this->m_action  = update.action;
}