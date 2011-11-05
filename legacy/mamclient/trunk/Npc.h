/*
 *  Npc.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/8/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _NPC_H
#define _NPC_H

#include "define.h"
#include "NpcInfoPacket.h"

class Npc
{
public:
    
    Npc( void );
    Npc( NpcInfoPacket *info );
    
public:
    
    char    m_name[16];
    
    int     m_id;
    
    short   m_type;
    short   m_look;
    short   m_x;
    short   m_y;
    
};

#endif