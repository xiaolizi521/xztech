/*
 *  Dialogue.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/9/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _DIALOGUE_H
#define _DIALOGUE_H

#include "define.h"
#include "NpcDialoguePacket.h"
#include <map>
#include <string>

class Dialogue
{
public:
    
    Dialogue( void );
    
    void process( NpcDialoguePacket *packet );
    void close( void );

public:
    
    char        m_text[255];
    
    int         m_nOptions;
    
    bool        m_open;
    
    OptionList  m_optionList;
    
};

#endif