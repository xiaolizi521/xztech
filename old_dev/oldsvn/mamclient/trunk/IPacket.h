/*
 *  IPacket.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/3/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _IPACKET_H
#define _IPACKET_H

#include"define.h"

class IPacket
{
public:
    
    virtual CPacket pack( void );
    
};

#endif