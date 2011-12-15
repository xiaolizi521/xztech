/*
 *  User.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/4/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "User.h"

User::User( Socket *socket, GameMap *gameMap, Dialogue *dialogue, MapFile *mapFile ) :
m_socket(socket), m_gameMap(gameMap), m_dialogue(dialogue), m_mapFile(mapFile)
, m_y(0), m_totalDexterity(0), m_x(0), m_totalLife(0), m_accountID(0)
, m_baseDexterity(0), m_guildRank(0), m_cultivation(0), m_money(0)
, m_totalEXP(0), m_baseDefense(0), m_baseMana(0), m_totalAttack(0)
, m_currentEXP(0), m_thieveryPoints(0), m_look(0), m_rank(0), m_reputation(0)
, m_totalDefense(0), m_kungfuPoints(0), m_virtuePoints(0), m_baseAttack(0)
, m_totalMana(0), m_petRaisingPoints(0), m_wuxingPoints(0), m_baseLife(0)
, m_level(0), m_characterID(0), m_reborn(0)
{}

void User::process( CharInfoPacket *packet )
{
    this->m_reborn              = packet->m_reborn;
    this->m_rank                = packet->m_rank;
    this->m_level               = packet->m_level;
    this->m_look                = packet->m_look;
    this->m_baseLife            = packet->m_life;
    this->m_baseMana            = packet->m_mana;
    this->m_baseAttack          = packet->m_attack;
    this->m_baseDefense         = packet->m_defense;
    this->m_baseDexterity       = packet->m_dexterity;
    this->m_guildRank           = packet->m_guildRank;
    this->m_cultivation         = packet->m_cultivation;
    this->m_money               = packet->m_money;
    this->m_reputation          = packet->m_reputation;
    this->m_thieveryPoints      = packet->m_thieveryPoints;
    this->m_kungfuPoints        = packet->m_kungfuPoints;
    this->m_petRaisingPoints    = packet->m_petRaisingPoints;
    this->m_currentEXP          = packet->m_currentEXP;
    this->m_characterID         = packet->m_characterID;
    this->m_x                   = packet->m_x;
    this->m_y                   = packet->m_y;
    
    strcpy( this->m_name, packet->m_name );
    strcpy( this->m_spouse, packet->m_spouse );
    strcpy( this->m_nickname, packet->m_nickname );
    strcpy( this->m_guildName, packet->m_guildName );
    strcpy( this->m_guildPosition, packet->m_guildPosition );
}

void User::jump( short x, short y, int mode, int dir )
{
    CPacket lcp = {
        { 16, 1018 },
        {
            0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00
        }
    };
    int32_t timestamp = (int32_t) timeGetTime() ^ (int32_t) ( this->m_accountID + this->m_characterID );
    int direction = ( dir >= 0 ) ? dir : rand() % 7;

    memcpy( ( void * )lcp.data, ( void * )&this->m_characterID, sizeof( this->m_characterID ) );
    memcpy( ( void * )( lcp.data + 0x04 ), ( void * )&this->m_x, sizeof( this->m_x ) );
    memcpy( ( void * )( lcp.data + 0x06 ), ( void * )&this->m_y, sizeof( this->m_y ) );
    memcpy( ( void * )( lcp.data + 0x06 ), ( void * )&direction, sizeof( direction ) );
    
    if ( mode != 8 )
        this->m_socket->send_packet( lcp );
    
    JumpPacket *pJump = new JumpPacket( this->m_characterID, x, y, mode, direction, timestamp );
    
    this->m_socket->send_packet( pJump->pack() );
    
    if ( mode != 8 )
    {
        int portal = this->m_gameMap->is_portal( x, y );
        
        if ( portal != -1 )
            this->jump( x, y, 8, portal );
    }
    
    this->m_x = x;
    this->m_y = y;
    
    delete pJump;
}

void User::give_money( int targetID, int ammount )
{
    CPacket packet = {
        { 20, 1012 },
        {
            0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 
            0x00, 0x00, 0x00, 0x00
        }
    };
    
    memcpy( ( void * )packet.data, ( void * )&this->m_characterID, sizeof( this->m_characterID ) );
    memcpy( ( void * )( packet.data + 0x04 ), ( void * )&targetID, sizeof( targetID ) );
    memcpy( ( void * )( packet.data + 0x0C ), ( void * )&ammount, sizeof( ammount ) );
    
    this->m_socket->send_packet( packet );
}

void User::open_npc( int id )
{
    CPacket packet = {
        { 16, 2031 },
        {
            0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00
        }
    };
    
    memcpy( ( void * )packet.data, ( void * )&id, sizeof( id ) );
    
    this->m_socket->send_packet( packet );
}

void User::click_dialogue( int index )
{
    CPacket packet = {
        { 12, 2032 },
        {
            0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00
        }
    };
    
    memcpy( ( void * )packet.data, ( void * )&index, sizeof( index ) );
    memset( ( void * )( packet.data + 0x04 ), 100, 1 );
    
    this->m_socket->send_packet( packet );
    
    this->m_dialogue->close();
}

void User::move_map( int destination )
{
    std::list< vertex_t >::iterator pit;
    std::list< vertex_t > path = this->m_mapFile->get_path( this->m_gameMap->m_id, destination );
    
    for ( pit = path.begin(); pit != path.end(); pit++ )
    {
        if ( *pit == this->m_gameMap->m_id )
            continue;
        
        Node node = this->m_mapFile->get_node( this->m_gameMap->m_id, *pit );
        // This shit still needs error checking, times like 10
        
        if ( node.x == -1 )
        {
            Transport transport = this->m_mapFile->get_transport( node.y );
            Transport::iterator it;
            
            for ( it = transport.begin(); it != transport.end(); it++ )
            {
                switch ( it->type ) 
                {
                    case 10:
                    {
                        this->open_npc( it->param );
                    }
                        
                        break;
                        
                    case 1:
                    {
                        this->click_dialogue( it->param );
                    }
                        
                        break;
                }
                
                if ( ( it + 1 ) != transport.end() )
                {
                    while ( !this->m_dialogue->m_open )
                        sleep((uint32_t) .1 );
                }
            }
        }
        else
            this->jump( node.x, node.y );
        
        time_t wait = time( NULL );
        
        while ( this->m_gameMap->m_id != *pit )
        {
            sleep((uint32_t) .1 );
            
            if ( difftime( wait, time( NULL ) ) > 2 )
            {
                pit--;
                break;
            }
        }
    }
}
