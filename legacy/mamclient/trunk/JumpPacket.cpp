/*
 *  JumpPacket.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/7/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "JumpPacket.h"

JumpPacket::JumpPacket( CPacket packet )
{
    JUMP_PACKET jump;
    
    memcpy( ( void * )&jump, ( void * )packet.data, ( packet.header.size - sizeof( CPacketHeader ) ) );
    
    this->m_playerID    = jump.player_id;
    this->m_direction   = jump.direction;
    this->m_mode        = jump.mode;
    this->m_timestamp   = jump.timestamp;
    this->m_x           = jump.x;
    this->m_y           = jump.y;
}

JumpPacket::JumpPacket( int player_id, short x, short y, int direction, int mode, int timestamp )
{
    this->m_playerID    = player_id;
    this->m_direction   = direction;
    this->m_mode        = mode;
    this->m_timestamp   = timestamp;
    this->m_x           = x;
    this->m_y           = y;
}

CPacket JumpPacket::pack( void )
{
    JUMP_PACKET jump;
    CPacket     packet = {
        { 24, 1007 },
        { NULL }
    };
    
    jump.player_id  = this->m_playerID;
    jump.direction  = this->m_direction;
    jump.mode       = this->m_mode;
    jump.timestamp  = this->m_timestamp;
    jump.x          = this->m_x;
    jump.y          = this->m_y;
    
    memcpy( ( void * )&packet.data, ( void * )&jump, sizeof( JUMP_PACKET ) );
    
    return packet;
}