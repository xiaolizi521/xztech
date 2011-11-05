/*
 *  User.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/4/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _USER_H
#define _USER_H

#include <list>
#include <vector>
#include <map>
#include "define.h"
#include "CharInfoPacket.h"
#include "JumpPacket.h"
#include "Socket.h"
#include "Utilities.h"
#include "GameMap.h"
#include "Dialogue.h"
#include "MapFile.h"

class User
{
public:
  
    User( Socket *socket, GameMap *gameMap, Dialogue *dialogue, MapFile *mapFile );
    
    void process( CharInfoPacket *packet );
    
    void jump( short x, short y, int mode = 2, int dir = -1 );
    
    void give_money( int targetID, int ammount );
    
    void open_npc( int id );
    void click_dialogue( int index );
    
    void move_map( int destination );
    
public:
    
    BYTE    m_reborn;
    BYTE    m_rank;
    
    char    m_name[32];
    char    m_spouse[32];
    char    m_nickname[32];
    char    m_guildName[32];
    char    m_guildPosition[32];
    
    short   m_level;
    short   m_look;
    short   m_baseLife;
    short   m_baseMana;
    short   m_baseAttack;
    short   m_baseDefense;
    short   m_baseDexterity;
    short   m_totalLife;
    short   m_totalMana;
    short   m_totalAttack;
    short   m_totalDefense;
    short   m_totalDexterity;
    short   m_guildRank;
    short   m_x;
    short   m_y;
    
    int     m_cultivation;
    int     m_money;
    int     m_reputation;
    int     m_thieveryPoints;
    int     m_kungfuPoints;
    int     m_wuxingPoints;
    int     m_petRaisingPoints;
    int     m_virtuePoints;
    int     m_currentEXP;
    int     m_totalEXP;
    int     m_accountID;
    int     m_characterID;
    
private:
    
    Socket      *m_socket;
    GameMap     *m_gameMap;
    Dialogue    *m_dialogue;
    MapFile     *m_mapFile;
    
};

#endif