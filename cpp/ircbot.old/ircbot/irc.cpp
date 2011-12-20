//
//  irc.cpp
//  ircbot
//
//  Created by Adam Hubscher on 12/15/11.
//  Copyright 2011 Enova Financial. All rights reserved.
//

#include "irc.h"

IRCBot::IRCBot( irc_host host ) {
    
    m_irc = host;
}

IRCBot::~IRCBot( ) {
    
    this->sDisconnect();
}

bool IRCBot::sConnect() {
    
    bool status;
    
    status = socket.Connect(m_irc.server, m_irc.port);
    
    if(!status) {
        
        printf("Connection failed. Exiting\n");
     
        exit(1);
    }
        
            
}

bool IRCBot::sDisconnect() {
    
    this->mSend((char *) "quit\n");
    
    return socket.Close();
}

int32_t IRCBot::Recv( int32_t timeout ) {
    
    int32_t status;
    
    status = this->mRecv();

    if ( status ) {
        
        this->mParse();
    }
    
    return status;
}

bool IRCBot::pingPong() {
    
    char *pingkey = NULL;
    
    if (!strncmp(m_irc.data, "PING", 5)) {
        
        pingkey = strchr(m_irc.data, ':');
        this->mSend((char *)"PONG %s\n",  ++pingkey);
        
        return 1;
    }
    
    return 0;
}

int32_t IRCBot::mSend(char *fmt, ...) {
    
    va_list ap;
    char tmp[BUF_SIZE];
    
    socket.set_nonblock();
    
    memset(tmp, '\0', BUF_SIZE);
    
    va_start(ap, fmt);
    
    vsprintf(tmp, fmt, ap);
    
    va_end(ap);
    
    socket.Send(tmp, strlen(tmp));
    
    return 0;
}

bool IRCBot::mRecv() {
    
    socket.pRead(&m_irc);

    printf((char *)"Packet Received: %s\n", m_irc.data);
    
    return true;
}

void IRCBot::mParse( void ) {
    
    char *beg_pos, *end_pos;
    
    if(m_irc.message.prefix) free(m_irc.message.prefix);
    if(m_irc.message.command) free(m_irc.message.command);
    if(m_irc.message.parameters) free(m_irc.message.parameters);
    
    if(m_irc.data) {
        
        if(m_irc.data[0] == ':') {
            
            beg_pos = &(m_irc.data)[0];
            
            if(beg_pos) end_pos=strchr(m_irc.data, ' ');
            
            if(beg_pos && end_pos) {
                
                m_irc.message.prefix = (char *) malloc((end_pos - beg_pos));
                strncpy(m_irc.message.prefix, beg_pos +1, (end_pos - beg_pos) - 1);
                m_irc.message.prefix[(end_pos - beg_pos) - 1] = '\0';
            }
        }
        
        else m_irc.message.prefix = NULL;
        
        if ( m_irc.message.prefix ) {
            
            beg_pos = strchr(m_irc.data, ' ');
            
            beg_pos++;
        }
        else beg_pos = &(m_irc.data)[0];
        
        if ( beg_pos ) end_pos = strchr(beg_pos + 1, ' ');
        
        if ( beg_pos && end_pos) {
            
            m_irc.message.command = (char *) malloc((end_pos - beg_pos) + 1);
            strncpy(m_irc.message.command, beg_pos, (end_pos - beg_pos));
            m_irc.message.command[(end_pos - beg_pos)] = '\0';
            
        }
        else m_irc.message.command = NULL;
        
        if (m_irc.message.prefix ) {
            
            beg_pos = strchr(m_irc.data, ' ');
            
            beg_pos = strchr(beg_pos +1, ' ');
        }
        
        else beg_pos = strchr(m_irc.data, ' ');
        
        if (beg_pos) end_pos = strchr(beg_pos, '\0');
        
        if ( beg_pos && end_pos) {
            
            m_irc.message.parameters = (char *) malloc((end_pos - beg_pos));
            strncpy(m_irc.message.parameters, beg_pos +1, (end_pos - beg_pos) - 1);
            m_irc.message.parameters[(end_pos - beg_pos) - 1] = '\0';
        }
        
        else m_irc.message.parameters = NULL;
    }
}

bool IRCBot::connInit( void ) {
    
    this->mSend((char *)"pass ircbot\n");
    this->mSend((char *)"nick %s %s\n", m_irc.nick, m_irc.pass);
    this->mSend((char *)"user %s 8 * ircbot\n", m_irc.user);
    
    return true;
}
w
