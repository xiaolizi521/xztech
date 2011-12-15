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

typedef int32_t _DWORD;

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
int32_t _rotr( int32_t a1, int32_t a2 );

intptr_t _encrypt_password( intptr_t a1, intptr_t a2, int32_t a3 );
void encrypt_password( char *outbuffer, char *password );

void hexdump( void *ptr, int32_t length );

AccountInfo account_info( const char *name );

template < class T > std::string toString( const T &t );

#endif
