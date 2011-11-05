/*
 *  ActionPacket.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/7/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _ACTIONPACKET_H
#define _ACTIONPACKET_H

#include "IPacket.h"
#include "define.h"
#include <string.h>

typedef struct _ACTION_PACKET {
    int player_id;
    short x;
    short y;
    int action_id;
}ACTION_PACKET;

class ActionPacket : virtual IPacket
{
public:
    
    ActionPacket( CPacket packet );
 
    virtual CPacket pack( void ) { CPacket packet; return packet; }
    
public:
    
    int     m_playerID;
    int     m_actionID;
    
    short   m_x;
    short   m_y;
    
};

#endif