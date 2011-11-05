/*
 *  define.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 10/4/08.
 *  Copyright 2008 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _DEFINE_H
#define _DEFINE_H

#define ACCOUNTSERVER           "64.151.106.220"
#define ACCOUNTPORT             9958
#define GAMESERVERPORT          9527

#define GAMEFILESPATH           "/etc/MAMClient/"

#define OWNER                   "jackyyll"

#define MAXPACKETSIZE           0x1000
#define BYTE                    unsigned char

#define CH_NONE                 0
#define CH_UNKNOWN              2000
#define CH_PRIVATE              2001
#define CH_ACTION               2002
#define CH_TEAM                 2003
#define CH_GUILD                2004
#define CH_SYSTEM               2005
#define CH_SPOUSE               2006
#define CH_NORMAL               2007
#define CH_SHOUT                2008
#define CH_FRIEND               2009
#define CH_BROADCAST            2010
#define CH_GM                   2011
#define CH_VENDOR               2104

/* Text Effect Definitions */
#define TXT_NORMAL              0x00
#define TXT_SCROLL              0x01
#define TXT_GLOW                0x02
#define TXT_SCROLL_GLOW         0x03
#define TXT_BLAST               0x08
#define TXT_SCROLL_BLAST        0x09
#define TXT_GLOW_BLAST          0x0A
#define TXT_SCROLL_GLOW_BLAST   0x0B

/* Color Definition */
#define YELLOW                  0x0FFFF00
#define WHITE                   0x0FFFFFF

/* Emotion Definition */
#define EMOTION                 " HackThePlanet "

typedef struct {
    short size;
    short id;
}CPacketHeader;

typedef struct {
    CPacketHeader header;
    char data[MAXPACKETSIZE];
}CPacket;

#endif