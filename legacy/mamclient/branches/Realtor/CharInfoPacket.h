/*
 *  CharInfoPacket.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/4/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _CHARINFOPACKET_H
#define _CHARINFOPACKET_H

#include "IPacket.h"
#include "define.h"
#include <string.h>

typedef struct _CHARINFO_PACKET_HEADER {
    int id;
    short look;
    short level;
    short current_hp;
    short total_hp;
    short current_mana;
    short total_mana;
    BYTE rank;
    BYTE reborn;
    short unk0;
    int money;
    int reputation;
    short unk1;
    short unk2;
    int cultivation;
    int current_exp;
    int wuxing;
    int kungfu;
    int petraising;
    int thievery;
    short available_stats;
    short life;
    short defense;
    short attack;
    short dexterity;
    short mana;
    short x;
    short y;
    int map_id;
    int unk3;
    int unk4;
    short guild_rank;
}CHARINFO_PACKET_HEADER;

class CharInfoPacket : virtual IPacket
{
public:
    
    CharInfoPacket( CPacket packet );
    
    virtual CPacket pack( void ) { CPacket packet; return packet; }
    
public:
    
    char    m_name[32];
    char    m_nickname[32];
    char    m_spouse[32];
    char    m_guildName[32];
    char    m_guildPosition[32];
    
    BYTE    m_rank;
    BYTE    m_reborn;
    
    short   m_look;
    short   m_level;
    short   m_currentHP;
    short   m_totalHP;
    short   m_currentMana;
    short   m_totalMana;
    short   m_availableStats;
    short   m_life;
    short   m_defense;
    short   m_attack;
    short   m_dexterity;
    short   m_mana;
    short   m_x;
    short   m_y;
    short   m_guildRank;
    
    int     m_characterID;
    int     m_money;
    int     m_reputation;
    int     m_cultivation;
    int     m_currentEXP;
    int     m_wuxingPoints;
    int     m_kungfuPoints;
    int     m_petRaisingPoints;
    int     m_thieveryPoints;
    int     m_mapID;
    
};

#endif