/*
 *  WalkPacket.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/7/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _WALKPACKET_H
#define _WALKPACKET_H

#include "IPacket.h"
#include "define.h"
#include <string.h>

typedef struct _WALK_PACKET {
    int player_id;
    short sx;
    short sy;
    short dx;
    short dy;
}WALK_PACKET;

class WalkPacket : virtual IPacket
{
public:
    
    WalkPacket( CPacket packet );
    
    virtual CPacket pack( void ) { CPacket packet; return packet; }
    
public:
    
    int     m_playerID;
    
    short   m_startX;
    short   m_startY;
    short   m_destX;
    short   m_destY;
    
};

#endif