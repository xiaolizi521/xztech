/*
 *  NpcUpdatePacket.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/8/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _NPCUPDATEPACKET_H
#define _NPCUPDATEPACKET_H

#include "IPacket.h"
#include "define.h"
#include <string.h>

typedef struct _NPC_UPDATE_PACKET {
    int npc_id;
    int action;
    int unk0;
}NPC_UPDATE_PACKET;

class NpcUpdatePacket : virtual IPacket
{
public:
    
    NpcUpdatePacket( CPacket packet );
    
    virtual CPacket pack( void ) { CPacket packet; return packet; }
    
public:
    
    int m_npcID;
    int m_action;
    
};

#endif