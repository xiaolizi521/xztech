/*
 *  CharInfoPacket.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/4/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "CharInfoPacket.h"
#include <stdio.h>
CharInfoPacket::CharInfoPacket( CPacket packet )
{
    CHARINFO_PACKET_HEADER header;
    
    BYTE nLength = *( BYTE * )( packet.data + 0x57 );
    BYTE mLength = *( BYTE * )( packet.data + 0x58 + nLength );
    BYTE sLength = *( BYTE * )( packet.data + 0x59 + nLength + mLength );
    BYTE gLength = *( BYTE * )( packet.data + 0x5A + nLength + mLength + sLength );
    BYTE pLength = *( BYTE * )( packet.data + 0x5C + nLength + mLength + sLength + gLength );
    
    memcpy( ( void * )&header, ( void * )packet.data, sizeof( header ) );
    
    this->m_characterID         = header.id;
    this->m_level               = header.level;
    this->m_currentHP           = header.current_hp;
    this->m_totalHP             = header.total_hp;
    this->m_currentMana         = header.current_mana;
    this->m_totalMana           = header.total_mana;
    this->m_rank                = header.rank;
    this->m_reborn              = header.reborn;
    this->m_money               = header.money;
    this->m_reputation          = header.reputation;
    this->m_cultivation         = header.cultivation;
    this->m_currentEXP          = header.current_exp;
    this->m_wuxingPoints        = header.wuxing;
    this->m_kungfuPoints        = header.kungfu;
    this->m_petRaisingPoints    = header.petraising;
    this->m_thieveryPoints      = header.thievery;
    this->m_availableStats      = header.available_stats;
    this->m_life                = header.life;
    this->m_defense             = header.defense;
    this->m_attack              = header.attack;
    this->m_dexterity           = header.dexterity;
    this->m_mana                = header.mana;
    this->m_mapID               = header.map_id;
    this->m_guildRank           = header.guild_rank;
    this->m_x                   = header.x;
    this->m_y                   = header.y;
    
    memcpy( ( void * )this->m_name, ( void * )( packet.data + 0x58 ), nLength );
    memcpy( ( void * )this->m_nickname, ( void * )( packet.data + 0x59 + nLength ), mLength );
    memcpy( ( void * )this->m_spouse, ( void * )( packet.data + 0x5A + nLength + mLength ), sLength );
    memcpy( ( void * )this->m_guildName, ( void * )( packet.data + 0x5B + nLength + mLength + sLength ), gLength );
    memcpy( ( void * )this->m_guildPosition, ( void * )( packet.data + 0x5D + nLength + mLength + sLength + gLength ), pLength );
    
    this->m_name[nLength]           = '\0';
    this->m_nickname[mLength]       = '\0';
    this->m_spouse[sLength]         = '\0';
    this->m_guildName[gLength]      = '\0';
    this->m_guildPosition[pLength]  = '\0';
    
    /*printf( "name: %s; nickname: %s; spouse: %s; guild: %s; position: %s;\n",
           this->m_name, this->m_nickname, this->m_spouse, this->m_guildName,
           this->m_guildPosition );
    
    printf( "characterID: %X; level: %d; HP: %d/%d; Mana: %d/%d; rank: %d; reborn: %d;"
           " money: %d; reputation: %d; cultivation: %d; currentEXP: %d;"
           " wuxing: %d; kungfu: %d; prp: %d; thievery: %d; stats: %d; life %d;"
           " defense: %d; attack: %d; dexterity: %d; mana: %d; map_id: %d; "
           " guild_rank: %d; x: %d; y: %d;\n", this->m_characterID, this->m_level,
           this->m_currentHP, this->m_totalHP, this->m_currentMana,
           this->m_totalMana, this->m_rank, this->m_reborn, this->m_money, this->m_reputation,
           this->m_cultivation, this->m_currentEXP, this->m_wuxingPoints, 
           this->m_kungfuPoints, this->m_petRaisingPoints, this->m_thieveryPoints,
           this->m_availableStats, this->m_life, this->m_defense, this->m_attack,
           this->m_dexterity, this->m_mana, this->m_mapID, this->m_guildRank,
           this->m_x, this->m_y );*/
}