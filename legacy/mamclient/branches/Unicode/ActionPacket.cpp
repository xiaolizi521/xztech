/*
 *  ActionPacket.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/7/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "ActionPacket.h"

ActionPacket::ActionPacket( CPacket packet )
{
    ACTION_PACKET action;
    
    memcpy( ( void * )&action, ( void * )packet.data, ( packet.header.size - sizeof( CPacketHeader ) ) );
    
    this->m_playerID    = action.player_id;
    this->m_actionID    = action.action_id;
    this->m_x           = action.x;
    this->m_y           = action.y;
}