/*
 *  Player.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 2/18/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _PLAYER_H
#define _PLAYER_H

#include "define.h"
#include <stdio.h>
#include <iostream>
#include <string.h>

class Player
{	
public:
	
    Player( void );
    Player( CPacket packet );
    ~Player( void );
	
    void Display( void );
    
public:
    int m_id;
    
    char m_name[32];
    char m_nickname[32];
    char m_spouse[32];
    char m_guild[32];
    char m_position[32];
    
    short m_x;
    short m_y;
    short m_level;
	
    BYTE m_reborn;
	
    bool m_pkEnable;
	
};

#endif