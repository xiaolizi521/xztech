/*
 *  JumpPacket.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/7/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _JUMPPACKET_H
#define _JUMPPACKET_H

#include "IPacket.h"
#include "define.h"
#include <string.h>

typedef struct _JUMP_PACKET {
    int player_id;
    short x;
    short y;
    int mode;
    int direction;
    int timestamp;
}JUMP_PACKET;

class JumpPacket : virtual IPacket
{
public:
    
    JumpPacket( CPacket packet );
    JumpPacket( int player_id, short x, short y, int direction, int mode, int timestamp );
    
    virtual CPacket pack( void );
    
public:
    
    int     m_playerID;
    int     m_direction;
    int     m_mode;
    int     m_timestamp;
    
    short   m_x;
    short   m_y;
    
};

#endif