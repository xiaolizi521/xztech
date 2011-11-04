/*
 *  MapFile.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/11/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _MAPFILE_H
#define _MAPFILE_H

#include <sstream>
#include <string>
#include <vector>
#include <list>
#include <map>
#include <stdlib.h>
#include "Dijkstra.h"
#include "ConfigFile.h"

template < class T > inline std::string toString( const T &t )
{
    std::stringstream sStream;
    sStream << t;
    
    return sStream.str();
}

typedef struct _NODE {
    signed int x;
    signed int y;
    int id;
}Node;

typedef struct _STEP {
    int type;
    int param;
}Step;

typedef std::vector< Node >         NodeList;
typedef std::vector< Step >         Transport;
typedef std::map< int, NodeList >   MapList;

class MapFile
{
public:
    
    MapFile( void );
    
    
    void                    process( const std::string & );
    
    std::list< vertex_t >   get_path( int, int );
    
    Node                    get_node( int, int );
    Transport               get_transport( int );
    
private:
    
    ConfigFile      m_mapFile;
    MapList         m_mapList;
    adjacency_map_t m_adjacencyMap;
    
};

#endif