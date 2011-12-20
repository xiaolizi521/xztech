//
//  irc.h
//  ircbot
//
//  Created by Adam Hubscher on 12/15/11.
//  Copyright 2011 Enova Financial. All rights reserved.
//

#ifndef ircbot_irc_h
#define ircbot_irc_h

#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <netdb.h>
#include <unistd.h>
#include <arpa/inet.h>
#include <sys/time.h>
#include <errno.h>
#include <fcntl.h>
#include <iostream>
#include <stdint.h>
#include <sys/time.h>
#include <string.h>
#include <stdio.h>
#include <ctype.h>
#include <string>
#include <sstream>
#include <fstream>
#include <map>
#include <vector>
#include <errno.h>
#include <time.h>
#include <unistd.h>
#include <stdlib.h>
#include "socket.h"
#include "define.h"

class IRCBot {

public:
    
    IRCBot(irc_host host);
    ~IRCBot(void);
    
    IRCBot(const IRCBot&);
    IRCBot& operator=(const IRCBot &x);
    
    bool sConnect();
    bool sDisconnect();
    
    int32_t Recv( int32_t timeout );
    int32_t mSend(char *, ...);
    
    bool mData( void );
    bool mRecv( void );
    void mParse( void );
    
    bool pingPong();
    
    bool connInit( void );
    
private:
    Socket socket;
    irc_host m_irc;
    
};

#endif
