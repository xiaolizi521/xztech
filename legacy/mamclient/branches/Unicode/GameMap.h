/*
 *  Map.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/6/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _GAMEMAP_H
#define _GAMEMAP_H

#include "define.h"
#include "MapInfoPacket.h"
#include "Player.h"
#include "Npc.h"
#include "Socket.h"
#include "ConfigFile.h"
#include <string.h>
#include <map>
#include <vector>
#include <fstream>
#include <sstream>

typedef struct _PORTAL {
    int id;
    short x;
    short y;
}Portal;

typedef std::map< int, Player > PlayerList;
typedef std::map< int, Npc >    NpcList;
typedef std::vector< Portal >   PortalList;

template < class T > inline std::string toString( const T &t );

class GameMap
{
public:
    
    GameMap( Socket *socket );
    
    void process( MapInfoPacket *packet );
    
    void add_player( Player *player );
    
    void add_npc( Npc *npc );
    
    void del_player( Player *player );
    void del_player( char *name );
    void del_player( int id );
    
    void del_npc( Npc *npc );
    void del_npc( char *name );
    void del_npc( int id );
    
    Player *find_player( char *name );
    Player *find_player( int id );
    
    Npc *find_npc( char *name );
    Npc *find_npc( int id );
    
    void list_players(void);
    
    NpcList npc_list( void );
    
    int is_portal( short x, short y );
    
public:
    
    char    m_name[16];
    
    int     m_type;
    int     m_id;
    int     m_mapdoc;
    
private:
    
    Socket      *m_socket;
    ConfigFile  *m_mapList;
    
    PlayerList  m_playerList;
    NpcList     m_npcList;
    PortalList  m_portalList;
    
};

#endif