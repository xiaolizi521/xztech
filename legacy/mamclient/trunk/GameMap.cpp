/*
 *  Map.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/6/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "GameMap.h"

GameMap::GameMap( Socket *socket )
{
    std::string path = GAMEFILESPATH;
    path += "ini/GameMap.ini";
    
    this->m_socket = socket;
    this->m_mapList = new ConfigFile( path );
}

void GameMap::process( MapInfoPacket *packet )
{
    CPacket map_packet = {
        { 20, 2057 },
        {
            0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x0A, 0x00, 0x00, 0x00
        }
    };
    
    this->m_playerList.erase( this->m_playerList.begin(), this->m_playerList.end() );
    this->m_npcList.erase( this->m_npcList.begin(), this->m_npcList.end() );
    
    this->m_type    = packet->m_type;
    this->m_id      = packet->m_id;
    this->m_mapdoc  = packet->m_mapdoc;
    
    strcpy( this->m_name, packet->m_name );
    
    std::ifstream   file;
    std::string     line;
    std::string     filename    = GAMEFILESPATH;
    std::string     section     = "Map";
    
    section     += toString( this->m_mapdoc );
    filename    += this->m_mapList->Value( section, "File" ).c_str();
    
    file.open( filename.c_str() );
    
    if ( file.is_open() )
    {
        while ( !file.eof() )
        {
            int id, x, y;
            Portal portal;
            
            getline( file, line );
            
            sscanf( line.c_str(), "%d,%d,%d", &id, &x, &y );
            
            portal.id   = id;
            portal.x    = x;
            portal.y    = y;
            
            this->m_portalList.push_back( portal );
        }
        
        file.close();
    }
    
    this->m_socket->send_packet( map_packet );
}

void GameMap::add_player( Player *player )
{
    this->m_playerList[player->m_id] = *( Player * )player;
}

void GameMap::add_npc( Npc *npc )
{
    this->m_npcList[npc->m_id] = *( Npc * )npc;
}

void GameMap::del_player( Player *player )
{
    this->m_playerList.erase( player->m_id );
}

void GameMap::del_player( char *name )
{
    PlayerList::iterator it;
    
    for ( it = this->m_playerList.begin(); it != this->m_playerList.end(); it++ )
    {
        if ( !strcmp( it->second.m_name, name ) )
        {
            this->m_playerList.erase( it->first );
            break;
        }
    }
}

void GameMap::del_player( int id )
{
    this->m_playerList.erase( id );
}

void GameMap::del_npc( Npc *npc )
{
    this->m_npcList.erase( npc->m_id );
}

void GameMap::del_npc( char *name )
{
    NpcList::iterator it;
    
    for ( it = this->m_npcList.begin(); it != this->m_npcList.end(); it++ )
    {
        if ( !strcmp( it->second.m_name, name ) )
        {
            this->m_npcList.erase( it->first );
            break;
        }
    }
}

void GameMap::del_npc( int id )
{
    this->m_npcList.erase( id );
}

Player *GameMap::find_player( char *name )
{
    Player *player = new Player();
    PlayerList::iterator it;
    
    for ( it = this->m_playerList.begin(); it != this->m_playerList.end(); it++ )
    {
        if ( !strcmp( it->second.m_name, name ) )
        {
            delete player;
            return &it->second;
        }
    }
    
    player->m_id = -1;
    
    return player;
}

Player *GameMap::find_player( int id )
{
    Player *player = new Player();
    
    if ( this->m_playerList.find( id ) != this->m_playerList.end() )
    {
        delete player;
        return &this->m_playerList[id];
    }
    
    player->m_id = -1;
    
    return player;
}

Npc *GameMap::find_npc( char *name )
{
    Npc *npc = new Npc();
    NpcList::iterator it;
    
    for ( it = this->m_npcList.begin(); it != this->m_npcList.end(); it++ )
    {
        if ( !strcmp( it->second.m_name, name ) )
        {
            delete npc;
            return &it->second;
        }
    }
    
    npc->m_id = -1;
    
    return npc;
}

Npc *GameMap::find_npc( int id )
{
    Npc *npc = new Npc();
    
    if ( this->m_npcList.find( id ) != this->m_npcList.end() )
    {
        delete npc;
        return &this->m_npcList[id];
    }
    
    npc->m_id = -1;
    
    return npc;
}

int GameMap::is_portal( short x, short y )
{
    PortalList::iterator it;
    
    for ( it = this->m_portalList.begin(); it != this->m_portalList.end(); it++ )
    {
        if ( it->x == x && it->y == y )
            return it->id;
    }
    
    return -1;
}

void GameMap::list_players( void )
{
    PlayerList::iterator it;
    
    for ( it = this->m_playerList.begin(); it != this->m_playerList.end(); it++ )
        printf( "Player Name: %s\n", it->second.m_name );
}

NpcList GameMap::npc_list( void )
{
    return this->m_npcList;
}

template < class T > inline std::string toString( const T &t )
{
	std::stringstream sStream;
	sStream << t;
	
	return sStream.str();
}