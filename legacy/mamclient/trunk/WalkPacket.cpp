/*
 *  WalkPacket.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/7/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "WalkPacket.h"

WalkPacket::WalkPacket( CPacket packet )
{
    WALK_PACKET walk;
    
    memcpy( ( void * )&walk, ( void * )packet.data, ( packet.header.size - sizeof( CPacketHeader ) ) );
    
    this->m_playerID    = walk.player_id;
    this->m_startX      = walk.sx;
    this->m_startY      = walk.sy;
    this->m_destX       = walk.dx;
    this->m_destY       = walk.dy;
}