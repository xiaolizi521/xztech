/*
 *  MessagePacket.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/3/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _MESSAGEPACKET_H
#define _MESSAGEPACKET_H

#include "IPacket.h"
#include "define.h"
#include <string.h>

typedef struct _MESSAGE_PACKET_HEADER {
    int color;
    short channel;
    short effect;
    int time;
    char emotion[16];
}MESSAGE_PACKET_HEADER;

class MessagePacket : virtual IPacket
{
public:
    
    MessagePacket( CPacket packet );
    MessagePacket( const char *sender, const char *target, const char *message, 
                  short channel, int color=WHITE, short effect=TXT_NORMAL, 
                  const char *emotion=EMOTION );

    virtual CPacket pack( void );
    
public:
    
    char m_sender[32];
    char m_target[32];
    char m_emotion[32];
    char m_message[255];
    char m_channelName[32];
    
    int     m_color;
    short   m_channel;
    short   m_effect;
    int     m_time;
    
};

#endif