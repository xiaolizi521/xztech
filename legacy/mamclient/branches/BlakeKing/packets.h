/*
 *  packets.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 4/30/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _PACKETS_H
#define _PACKETS_H

#include "MessagePacket.h"
#include "CharInfoPacket.h"
#include "MapInfoPacket.h"
#include "WalkPacket.h"
#include "ActionPacket.h"
#include "JumpPacket.h"
#include "NpcInfoPacket.h"
#include "NpcUpdatePacket.h"
#include "NpcDialoguePacket.h"

typedef struct _PET_PACKET {
    int Padding0;
    int id;
    char name[16];
    int pet_class;
    short look;
    short attack;
    short defense;
    short dexterity;
    short level;
    short unk0;
    short current_xp;
    short unk1;
    short hp_current;
    short hp_total;
    char generation;
    char Padding1[27];
    struct {
        unsigned char attack;
        unsigned char defense;
        unsigned char dexterity;
    }medals;
    unsigned char loyalty;
    int owner_id;
    char Padding2[16]; // color shit ( i think )
}PET_PACKET;

typedef struct _GIVE_MONEY_PACKET {
    int self_id;
    int target_id;
    int Padding0;
    int ammount;
}GIVE_MONEY_PACKET;

#endif