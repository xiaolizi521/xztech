/*
 *  MapInfoPacket.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/6/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "MapInfoPacket.h"
#include <stdio.h>
MapInfoPacket::MapInfoPacket( CPacket packet ) :
  m_type(0)
, m_id(0)
, m_mapdoc(0)
, m_direction(0)
, m_x(0)
, m_y(0) {
    
    MAP_INFO_PACKET Map;
    
    memcpy( ( void * )&Map, ( void * )packet.data, ( packet.header.size - sizeof( CPacketHeader ) ) );
    
    this->m_type        = Map.type;
    this->m_id          = Map.map_id;
    this->m_mapdoc      = Map.map_type;
    this->m_direction   = Map.direction;
    this->m_x           = Map.self_x;
    this->m_y           = Map.self_y;
    
    strcpy( this->m_name, Map.map_name );
}
