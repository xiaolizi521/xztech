/*
 *  JumpPacket.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/7/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "JumpPacket.h"

JumpPacket::JumpPacket( CPacket packet ) :
  m_mode(0)
, m_direction(0)
, m_timestamp(0)
, m_x(0)
, m_y(0)
, m_playerID(0)
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

JumpPacket::JumpPacket( int player_id, short x, short y, int direction, int mode, int timestamp ) :
  m_mode(mode)
, m_direction(direction)
, m_timestamp(timestamp)
, m_x(x)
, m_y(y)
, m_playerID(player_id) {}

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