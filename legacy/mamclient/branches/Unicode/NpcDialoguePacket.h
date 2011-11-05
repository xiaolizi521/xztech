/*
 *  NpcDialoguePacket.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/9/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _NPCDIALOGUEPACKET_H
#define _NPCDIALOGUEPACKET_H

#include "IPacket.h"
#include "define.h"
#include <map>
#include <string>
#include <string.h>

typedef std::map< int, std::string > OptionList;

class NpcDialoguePacket : virtual IPacket
{
public:
    
    NpcDialoguePacket( CPacket packet );
    
    virtual CPacket pack( void ) { CPacket packet; return packet; }
    
public:
    
    char        m_text[500];
    
    int         m_nOptions;
    
    OptionList  m_options;

};

#endif