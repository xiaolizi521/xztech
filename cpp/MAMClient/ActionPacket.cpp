/*
 *  ActionPacket.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/7/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "ActionPacket.h"

ActionPacket::ActionPacket( CPacket packet ) :
  m_x(0)
, m_y(0)
, m_actionID(0)
, m_playerID(0) {
    
    ACTION_PACKET action;
    
    memcpy( ( void * )&action, ( void * )packet.data, ( packet.header.size - sizeof( CPacketHeader ) ) );
    
    this->m_playerID    = action.player_id;
    this->m_actionID    = action.action_id;
    this->m_x           = action.x;
    this->m_y           = action.y;
}
