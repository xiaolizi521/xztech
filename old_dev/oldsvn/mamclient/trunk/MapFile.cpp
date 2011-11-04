/*
 *  MapFile.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/11/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "MapFile.h"

MapFile::MapFile( void )
{
}

void MapFile::process( const std::string &map_file )
{
    this->m_mapFile = map_file;
    
    int mapNum = atoi( this->m_mapFile.Value( "main", "MapNum" ).c_str() );
    
    MapList::iterator   it;
    NodeList::iterator  nit;
    
    for ( int i = 0; i < mapNum; i++ )
    {
        int         id;
        int         nodeNum;
        std::string section;
        NodeList    nodeList;
        
        section = "map" + toString( i );
        nodeNum = atoi( this->m_mapFile.Value( section, "NodeNum" ).c_str() );
        id      = atoi( this->m_mapFile.Value( section, "Id" ).c_str() );
        
        for ( int n = 0; n < nodeNum; n++ )
        {
            std::string szNode;
            Node node = { NULL };
            
            szNode = this->m_mapFile.Value( section, "Node" + toString( n ) );
            
            sscanf( szNode.c_str(), "%d,%d,%d", &node.x, &node.y, &node.id );
            
            nodeList.push_back( node );
        }
        
        if ( id )
            this->m_mapList[id] = nodeList;
    }
    
    for ( it = this->m_mapList.begin(); it != this->m_mapList.end(); it++ )
    {
        for ( nit = it->second.begin(); nit != it->second.end(); nit++ )
            this->m_adjacencyMap[it->first].push_back( edge( nit->id ) );
    }
}

std::list< vertex_t > MapFile::get_path( int source, int destination )
{
    std::map< vertex_t, weight_t > min_dist;
    std::map< vertex_t, vertex_t > previous;
    
    DijkstraComputePaths( source, this->m_adjacencyMap, min_dist, previous );
    
    return DijkstraGetShortestPathTo( (vertex_t)destination, previous );
}

Node MapFile::get_node( int map_id, int node_id )
{
    Node nullNode = { NULL };
    
    MapList::iterator   it;
    NodeList::iterator  nit;
    
    for ( it = this->m_mapList.begin(); it != this->m_mapList.end(); it++ )
    {
        if ( it->first == map_id )
        {
            for ( nit = it->second.begin(); nit != it->second.end(); nit++ )
            {
                if ( nit->id == node_id )
                    return *nit;
            }
        }
    }
    
    return nullNode;
}

Transport MapFile::get_transport( int transport_id )
{
    int stepCount = atoi( this->m_mapFile.Value( "transport" + toString( transport_id ), "StepNum" ).c_str() );
    Transport transport;
    
    for ( int i = 0; i < stepCount; i++ )
    {
        std::string the_step = this->m_mapFile.Value( "transport" + toString( transport_id ), "Step" + toString( i ) );
        Step step = { NULL };
        
        sscanf( the_step.c_str(), "%d,%d", &step.type, &step.param );
        
        transport.push_back( step );
    }
    
    return transport;
}