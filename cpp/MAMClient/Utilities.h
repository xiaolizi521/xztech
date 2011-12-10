/*
 *  Utilities.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 10/9/08.
 *  Copyright 2008 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _UTILITIES_H
#define _UTILITIES_H

#include <sys/time.h>
#include <string.h>
#include <stdio.h>
#include <ctype.h>
#include <vector>
#include <string>
#include <sstream>
#include <fstream>
#include <stdlib.h>
#include <confuse.h>

#define TICKSPERSEC 10000000
#define SECSPERDAY 86400
#define SECS_1601_TO_1970 ( ( 369 * 365 + 89 ) * ( unsigned long long )SECSPERDAY )
#define TICKS_1601_to_1970 ( SECS_1601_TO_1970 * TICKSPERSEC )

typedef uint64_t _DWORD;

typedef struct _ACCOUNTINFO {
    char serverip[32];
    char servername[32];
    char username[32];
    char password[32];
    char pcode[32];
}AccountInfo;

unsigned long timeGetTime( void );
void initTime( void );

//uint64_t _rotr( uint64_t a1, uint64_t a2 );
uint64_t _rotr( uint64_t a1, uint64_t a2 );

int _encrypt_password( unsigned char * a1, unsigned char * a2, signed int a3 );
void encrypt_password( char *outbuffer, char *password );

void hexdump( void *ptr, int length );

AccountInfo account_info( const char *name );

template < class T > std::string toString( const T &t );

#endif