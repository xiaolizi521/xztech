//
//  define.h
//  ircbot
//
//  Created by Adam Hubscher on 12/14/11.
//  Copyright 2011 Enova Financial. All rights reserved.
//

#ifndef ircbot_define_h
#define ircbot_define_h

#ifndef MAX_MESSAGE_SIZE
#define MAX_MESSAGE_SIZE 513
#endif

#define BUF_SIZE 513

#ifndef DEBUG
#define DEBUG 1
#endif

#ifndef MAX_BUFFQ
#define MAX_BUFFQ 1024
#endif

#ifndef MAX_NICK_SIZE
#define MAX_NICK_SIZE 12
#endif

#define IP "66.135.41.236"
#define NAME "jenni"
#define PORT "6667"
#define CHANNEL "#blah"

typedef struct {
    int32_t size;
    int32_t id;
} CPacketHeader;

typedef struct {
    CPacketHeader header;
    char data[MAX_MESSAGE_SIZE];
}CPacket;

typedef struct {
    char *prefix;
    char *command;
    char *parameters;
} _pData;

typedef struct {
    
    char *nick;
    char *user;
    char *addr;
    
    int32_t command;
    
    char *target;
    char *msg;
} _data;

typedef struct {
    
    int32_t sock;
    
    char *server;
    char *server_name;
    
    int32_t port;
    
    char *nick;
    char *pass;
    char *user;
    char *data;
    
    _data packet;
    _pData message;
    
} irc_host;

#endif
