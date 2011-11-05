/*
 *  MapInfoPacket.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/6/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _MAPINFOPACKET_H
#define _MAPINFOPACKET_H

#include "IPacket.h"
#include "define.h"
#include <string.h>

typedef struct _MAP_INFO_PACKET {
    int type;
    int self_id;
    int map_id;
    int map_type;
    short self_x;
    short self_y;
    short unk1;
    int direction;
    short unk2;
    short unk3;
    char map_name[16];
}MAP_INFO_PACKET;

class MapInfoPacket : virtual IPacket
{
public:
    
    MapInfoPacket( CPacket packet );
    
    virtual CPacket pack( void ) { CPacket packet; return packet; }

public:
    
    char    m_name[16];
    
    int     m_type;
    int     m_id;
    int     m_mapdoc;
    int     m_direction;
    
    short   m_x;
    short   m_y;
    
};

#endif