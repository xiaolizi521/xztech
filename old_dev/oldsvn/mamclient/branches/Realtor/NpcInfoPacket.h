/*
 *  NpcInfoPacket.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/8/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _NPCINFOPACKET_H
#define _NPCINFOPACKET_H

#include "IPacket.h"
#include "define.h"
#include <string.h>

typedef struct _NPC_INFO_PACKET {
    int id;
    short unk0;
    short type;
    short look;
    short x;
    short y;
    char name[16];
    char color_shit[18];
}NPC_INFO_PACKET;

class NpcInfoPacket : virtual IPacket
{
public:
    
    NpcInfoPacket( CPacket packet );
    
    virtual CPacket pack( void ) { CPacket packet; return packet; };
    
public:
    
    char    m_name[16];
    
    int     m_id;
    
    short   m_type;
    short   m_look;
    short   m_x;
    short   m_y;
    
};

#endif